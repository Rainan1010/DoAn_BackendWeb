<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BackupLog;
use Spatie\DbDumper\Databases\MySql;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;
use Symfony\Component\Process\Process;

class BackupController extends Controller
{
    public function index()
    {
        $backups = BackupLog::with('creator')->orderBy('created_at', 'desc')->get();
        return view('admin.backups.index', compact('backups'));
    }

    private function getTableCounts()
    {
        try {
            return [
                'Sản phẩm' => DB::table('products')->count(),
                'Người dùng' => DB::table('users')->count(),
                'Danh mục' => DB::table('categories')->count(),
                'Đơn hàng' => DB::table('orders')->count(),
            ];
        } catch (\Exception $e) {
            return [];
        }
    }

    private function generateChangeMessage($beforeCounts, $afterCounts)
    {
        $changes = [];
        foreach ($beforeCounts as $label => $before) {
            $after = $afterCounts[$label] ?? 0;
            $diff = $after - $before;
            if ($diff != 0) {
                $sign = $diff > 0 ? '+' : '';
                $color = $diff > 0 ? 'text-green-600' : 'text-red-600';
                $changes[] = "{$label}: <strong class='{$color}'>{$sign}{$diff}</strong>";
            }
        }
        return !empty($changes) ? '<br><span class="text-sm text-gray-600">Biến động: ' . implode(' | ', $changes) . '</span>' : '<br><span class="text-sm text-gray-600">Biến động: Không thay đổi về số lượng</span>';
    }

    private function runDatabaseDump($filePath)
    {
        $dumpPath = config('database.dump_path', '');
        $executable = $dumpPath ? rtrim($dumpPath, '\\/') . DIRECTORY_SEPARATOR . 'mysqldump' : 'mysqldump';

        $connection = config('database.default', 'mysql');
        $dbConfig = config("database.connections.{$connection}");

        $command = sprintf(
            '"%s" --user="%s" %s --host="%s" --port="%s" "%s" > "%s"',
            $executable,
            $dbConfig['username'] ?? 'root',
            !empty($dbConfig['password']) ? '--password="' . $dbConfig['password'] . '"' : '',
            $dbConfig['host'] ?? '127.0.0.1',
            $dbConfig['port'] ?? '3306',
            $dbConfig['database'] ?? '',
            $filePath
        );

        $process = Process::fromShellCommandline($command);
        $process->setTimeout(300);
        
        $env = array_merge($_SERVER, $_ENV);
        if (!isset($env['SystemRoot'])) $env['SystemRoot'] = getenv('SystemRoot') ?: 'C:\\Windows';
        if (!isset($env['SYSTEMDRIVE'])) $env['SYSTEMDRIVE'] = getenv('SYSTEMDRIVE') ?: 'C:';
        $process->setEnv($env);

        $process->run();

        if (!$process->isSuccessful()) {
            throw new Exception("ExitCode: {$process->getExitCode()} - " . $process->getErrorOutput());
        }
    }

    private function createAutoBackup($prefix = 'auto_backup')
    {
        try {
            $fileName = $prefix . '_' . Carbon::now()->format('Ymd_His') . '.sql';
            $directory = storage_path('app/backups');
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
            $filePath = $directory . DIRECTORY_SEPARATOR . $fileName;

            $this->runDatabaseDump($filePath);

            $backup = BackupLog::create([
                'file_name' => $fileName,
                'file_path' => 'backups/' . $fileName,
                'file_size' => filesize($filePath),
                'status' => 'success',
                'created_by' => Auth::id() ?? 1, // Fallback if no user
                'created_at' => Carbon::now()
            ]);
            return $backup->backup_id;
        } catch (Exception $e) {
            return null; // Ignore if auto backup fails
        }
    }

    public function create()
    {
        try {
            $fileName = 'backup_' . Carbon::now()->format('Ymd_His') . '.sql';
            $directory = storage_path('app/backups');
            
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            $filePath = $directory . DIRECTORY_SEPARATOR . $fileName;
            
            $this->runDatabaseDump($filePath);

            BackupLog::create([
                'file_name' => $fileName,
                'file_path' => 'backups/' . $fileName,
                'file_size' => filesize($filePath),
                'status' => 'success',
                'created_by' => Auth::id(),
                'created_at' => Carbon::now()
            ]);

            return redirect()->route('admin.backups.index')->with('success', 'Tạo bản sao lưu thành công!');
        } catch (Exception $e) {
            return redirect()->route('admin.backups.index')->with('error', 'Lỗi khi sao lưu: ' . $e->getMessage());
        }
    }

    public function download($id)
    {
        $backup = BackupLog::findOrFail($id);
        $path = storage_path('app/' . $backup->file_path);
        
        if (!file_exists($path)) {
            return redirect()->route('admin.backups.index')->with('error', 'File không còn tồn tại trên server.');
        }

        return response()->download($path);
    }

    public function restore($id)
    {
        try {
            $backup = BackupLog::findOrFail($id);
            $path = storage_path('app/' . $backup->file_path);

            if (!file_exists($path)) {
                return redirect()->route('admin.backups.index')->with('error', 'Không tìm thấy file backup trên server.');
            }

            // Tự động sao lưu trước khi ghi đè
            $undoId = $this->createAutoBackup('undo_restore');
            $undoLogData = BackupLog::find($undoId);
            $undoArray = $undoLogData ? $undoLogData->toArray() : null;

            $beforeCounts = $this->getTableCounts();

            $sql = file_get_contents($path);
            DB::unprepared($sql);

            $afterCounts = $this->getTableCounts();
            $changeMsg = $this->generateChangeMessage($beforeCounts, $afterCounts);

            // Vì DB::unprepared đã ghi đè lại toàn bộ CSDL (bao gồm bảng backup_logs)
            // Nên record undo_restore vừa tạo đã bị xoá mất. Ta cần insert lại nó.
            if ($undoArray) {
                unset($undoArray['backup_id']); // Xoá ID cũ để tạo ID mới tự động
                $newUndoLog = BackupLog::create($undoArray);
                $undoId = $newUndoLog->backup_id;
            }

            return redirect()->route('admin.backups.index')->with('success', 'Phục hồi dữ liệu thành công!' . $changeMsg)->with('undo_id', $undoId);
        } catch (Exception $e) {
            return redirect()->route('admin.backups.index')->with('error', 'Lỗi phục hồi: ' . $e->getMessage());
        }
    }

    public function uploadRestore(Request $request)
    {
        $request->validate([
            'sql_file' => 'required|file'
        ]);

        try {
            $file = $request->file('sql_file');
            if ($file->getClientOriginalExtension() !== 'sql') {
                return redirect()->back()->with('error', 'Chỉ chấp nhận file định dạng .sql');
            }
            
            $sql = file_get_contents($file->getRealPath());

            $isValidDb = str_contains($sql, '`users`') && 
                         str_contains($sql, '`products`') && 
                         str_contains($sql, '`categories`') &&
                         str_contains($sql, '`backup_logs`');

            if (!$isValidDb) {
                return redirect()->back()->with('error', 'Cảnh báo: File không hợp lệ! Đây không phải là file CSDL được trích xuất từ hệ thống này.');
            }

            // Tự động sao lưu trước khi ghi đè
            $undoId = $this->createAutoBackup('undo_restore');
            $undoLogData = BackupLog::find($undoId);
            $undoArray = $undoLogData ? $undoLogData->toArray() : null;

            $beforeCounts = $this->getTableCounts();

            DB::unprepared($sql);

            $afterCounts = $this->getTableCounts();
            $changeMsg = $this->generateChangeMessage($beforeCounts, $afterCounts);

            // Khôi phục lại record undo_restore bị ghi đè
            if ($undoArray) {
                unset($undoArray['backup_id']);
                $newUndoLog = BackupLog::create($undoArray);
                $undoId = $newUndoLog->backup_id;
            }

            $fileName = 'upload_' . Carbon::now()->format('Ymd_His') . '_' . $file->getClientOriginalName();
            $fileSize = $file->getSize();
            $file->storeAs('backups', $fileName);
            
            BackupLog::create([
                'file_name' => $fileName,
                'file_path' => 'backups/' . $fileName,
                'file_size' => $fileSize,
                'status' => 'success',
                'created_by' => Auth::id(),
                'created_at' => Carbon::now()
            ]);

            return redirect()->route('admin.backups.index')->with('success', 'Đã tải lên và phục hồi CSDL thành công!' . $changeMsg)->with('undo_id', $undoId);
        } catch (Exception $e) {
            return redirect()->route('admin.backups.index')->with('error', 'Lỗi khi phục hồi từ file tải lên: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $backup = BackupLog::findOrFail($id);
        $path = storage_path('app/' . $backup->file_path);
        
        if (file_exists($path)) {
            unlink($path);
        }
        
        $backup->delete();

        return redirect()->route('admin.backups.index')->with('success', 'Đã xoá bản sao lưu.');
    }
}
