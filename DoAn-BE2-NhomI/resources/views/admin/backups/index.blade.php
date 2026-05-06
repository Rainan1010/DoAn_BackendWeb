@extends('admin.layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row gap-4 justify-between items-start sm:items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Quản lý Sao lưu Dữ liệu</h2>
            <p class="text-sm text-gray-500">Thực hiện sao lưu và phục hồi database hệ thống</p>
        </div>
        <div class="flex items-center gap-3">
            <form id="uploadForm" action="{{ route('admin.backups.upload') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2">
                @csrf
                <input type="file" id="sqlFileInput" name="sql_file" accept=".sql" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                <button type="button" onclick="confirmUpload()" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors focus:ring-4 focus:ring-green-200">
                    <i data-lucide="upload-cloud" class="w-4 h-4"></i>
                    Upload & Restore
                </button>
            </form>
            <form action="{{ route('admin.backups.create') }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors focus:ring-4 focus:ring-blue-200">
                    <i data-lucide="database-backup" class="w-4 h-4"></i>
                    Tạo bản sao lưu mới
                </button>
            </form>
        </div>
    </div>

    @if(session('undo_id'))
    <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-sm">
        <div class="flex items-center gap-3 text-yellow-800">
            <i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0"></i>
            <span class="font-medium text-sm">Cảnh báo: Dữ liệu hiện tại vừa bị ghi đè. Bạn có muốn hoàn tác lại như cũ không?</span>
        </div>
        <form action="{{ route('admin.backups.restore', session('undo_id')) }}" method="POST" class="shrink-0">
            @csrf
            <button type="submit" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors flex items-center gap-2" onclick="return confirm('Bạn có chắc muốn hoàn tác lại CSDL như lúc chưa phục hồi?');">
                <i data-lucide="undo-2" class="w-4 h-4"></i> Hoàn tác (Undo)
            </button>
        </form>
    </div>
    @endif

    <!-- Bảng dữ liệu -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50/50 border-b border-gray-100 text-gray-500">
                    <tr>
                        <th class="px-6 py-4 font-medium">Tên file</th>
                        <th class="px-6 py-4 font-medium">Dung lượng</th>
                        <th class="px-6 py-4 font-medium">Trạng thái</th>
                        <th class="px-6 py-4 font-medium">Người tạo</th>
                        <th class="px-6 py-4 font-medium">Thời gian</th>
                        <th class="px-6 py-4 font-medium text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($backups as $backup)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-900">
                            {{ $backup->file_name }}
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            {{ number_format($backup->file_size / 1024 / 1024, 2) }} MB
                        </td>
                        <td class="px-6 py-4">
                            @if($backup->status == 'success')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Thành công
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-red-50 text-red-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Thất bại
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            {{ optional($backup->creator)->full_name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            {{ \Carbon\Carbon::parse($backup->created_at)->format('d/m/Y H:i:s') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.backups.download', $backup->backup_id) }}" title="Tải xuống" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                    <i data-lucide="download" class="w-4 h-4"></i>
                                </a>
                                <button type="button" onclick="confirmRestore({{ $backup->backup_id }}, '{{ $backup->file_name }}', '{{ \Carbon\Carbon::parse($backup->created_at)->format('d/m/Y H:i:s') }}')" title="Phục hồi" class="p-2 text-orange-600 hover:bg-orange-50 rounded-lg transition-colors">
                                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                                </button>
                                <form action="{{ route('admin.backups.destroy', $backup->backup_id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xoá bản sao lưu này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Xoá" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            Chưa có bản sao lưu nào.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Restore Warning -->
<div id="restoreModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeRestoreModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i data-lucide="alert-triangle" class="h-6 w-6 text-red-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Cảnh báo phục hồi dữ liệu!</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 mb-2">Thao tác này sẽ ghi đè toàn bộ CSDL hiện tại bằng dữ liệu từ file backup này. Những dữ liệu sinh ra sau thời điểm backup sẽ bị mất vĩnh viễn.</p>
                            <div class="bg-gray-50 p-3 rounded-md text-sm text-gray-700">
                                <p><strong>File:</strong> <span id="modal-filename"></span></p>
                                <p><strong>Thời gian tạo:</strong> <span id="modal-time"></span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <form id="restoreForm" method="POST" action="">
                    @csrf
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Đồng ý Phục hồi
                    </button>
                </form>
                <button type="button" onclick="closeRestoreModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Huỷ bỏ
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Upload Confirm -->
<div id="uploadConfirmModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeUploadModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i data-lucide="info" class="h-6 w-6 text-blue-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Xác nhận tải lên và Phục hồi</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 mb-2">Bạn đang chuẩn bị ghi đè database bằng file tải lên. Hãy kiểm tra lại thông tin file xem đã chọn đúng chưa:</p>
                            <div class="bg-blue-50 p-3 rounded-md text-sm text-blue-800 space-y-1">
                                <p><strong>Tên file:</strong> <span id="upload-filename"></span></p>
                                <p><strong>Dung lượng:</strong> <span id="upload-size"></span></p>
                                <p><strong>Sửa đổi lần cuối:</strong> <span id="upload-time"></span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="submitUpload()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Xác nhận Phục hồi
                </button>
                <button type="button" onclick="closeUploadModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Huỷ bỏ
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Logic của restore có sẵn
    function confirmRestore(id, filename, time) {
        document.getElementById('modal-filename').innerText = filename;
        document.getElementById('modal-time').innerText = time;
        document.getElementById('restoreForm').action = "/admin/backups/" + id + "/restore";
        document.getElementById('restoreModal').classList.remove('hidden');
    }
    function closeRestoreModal() {
        document.getElementById('restoreModal').classList.add('hidden');
    }

    // Logic xử lý file upload từ máy tính
    function confirmUpload() {
        const fileInput = document.getElementById('sqlFileInput');
        if (fileInput.files.length === 0) {
            alert('Vui lòng bấm nút "Chọn tệp" để tải lên file .sql trước!');
            return;
        }
        
        const file = fileInput.files[0];
        const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
        const fileDate = new Date(file.lastModified).toLocaleString('vi-VN');

        document.getElementById('upload-filename').innerText = file.name;
        document.getElementById('upload-size').innerText = fileSizeMB;
        document.getElementById('upload-time').innerText = fileDate;
        
        document.getElementById('uploadConfirmModal').classList.remove('hidden');
    }

    function submitUpload() {
        document.getElementById('uploadConfirmModal').classList.add('hidden');
        document.getElementById('uploadForm').submit();
    }
    
    function closeUploadModal() {
        document.getElementById('uploadConfirmModal').classList.add('hidden');
    }
</script>
@endsection
