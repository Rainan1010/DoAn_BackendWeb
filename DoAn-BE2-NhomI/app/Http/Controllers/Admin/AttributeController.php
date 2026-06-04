<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use Illuminate\Http\Request;

class AttributeController extends Controller
{
    public function index()
    {
        $attributes = Attribute::with('values')
            ->orderBy('attribute_id', 'asc')
            ->get();

        return view('admin.attributes.index', compact('attributes'));
    }

    public function create()
    {
        return view('admin.attributes.create');
    }

    public function store(Request $request)
    {
        $request->merge([
            'name' => $this->normalizeText($request->name),
            'unit' => $this->normalizeText($request->unit),
            'values' => $this->normalizeText($request->values),
        ]);

        $request->validate([
            'name' => 'required|string|max:100',
            'unit' => 'nullable|string|max:50',
            'values' => 'nullable|string|max:2000',
        ], [
            'name.required' => 'Vui lòng nhập tên thuộc tính.',
            'name.max' => 'Tên thuộc tính không được vượt quá 100 ký tự.',
            'unit.max' => 'Đơn vị không được vượt quá 50 ký tự.',
            'values.max' => 'Danh sách giá trị thuộc tính quá dài.',
        ]);

        $attribute = Attribute::create([
            'name' => $request->name,
            'unit' => $request->unit,
        ]);

        $this->syncAttributeValues($attribute, $request->values);

        return redirect()
            ->route('admin.attributes.index')
            ->with('success', 'Thêm thuộc tính thành công!');
    }

    public function show($id)
    {
        $attribute = Attribute::with('values')->find($id);

        if (!$attribute) {
            return redirect()
                ->route('admin.attributes.index')
                ->with('error', 'Thuộc tính không tồn tại hoặc đã bị xóa.');
        }

        return redirect()
            ->route('admin.attributes.edit', $attribute->attribute_id);
    }

    public function edit($id)
    {
        $attribute = Attribute::with('values')->find($id);

        if (!$attribute) {
            return redirect()
                ->route('admin.attributes.index')
                ->with('error', 'Thuộc tính không tồn tại hoặc đã bị xóa.');
        }

        $snapshot = $this->makeAttributeSnapshot($attribute);

        return view('admin.attributes.edit', compact('attribute', 'snapshot'));
    }

    public function update(Request $request, $id)
    {
        $attribute = Attribute::with('values')->find($id);

        if (!$attribute) {
            return redirect()
                ->route('admin.attributes.index')
                ->with('error', 'Không thể cập nhật vì thuộc tính không tồn tại hoặc đã bị xóa.');
        }

        /*
        |--------------------------------------------------------------------------
        | Chống cập nhật trùng 2 tab
        |--------------------------------------------------------------------------
        | Khi mở edit ở 2 tab, mỗi tab giữ một snapshot dữ liệu ban đầu.
        | Nếu tab 1 đã update, snapshot trong database sẽ thay đổi.
        | Tab 2 gửi snapshot cũ lên thì hệ thống chặn lại, tránh ghi đè dữ liệu.
        */
        $currentSnapshot = $this->makeAttributeSnapshot($attribute);

        if ($request->input('_snapshot') !== $currentSnapshot) {
            return redirect()
                ->route('admin.attributes.edit', $attribute->attribute_id)
                ->with('error', 'Dữ liệu đã thay đổi ở tab khác. Vui lòng tải lại trang trước khi cập nhật.');
        }

        $request->merge([
            'name' => $this->normalizeText($request->name),
            'unit' => $this->normalizeText($request->unit),
            'values' => $this->normalizeText($request->values),
        ]);

        $request->validate([
            'name' => 'required|string|max:100',
            'unit' => 'nullable|string|max:50',
            'values' => 'nullable|string|max:2000',
        ], [
            'name.required' => 'Vui lòng nhập tên thuộc tính.',
            'name.max' => 'Tên thuộc tính không được vượt quá 100 ký tự.',
            'unit.max' => 'Đơn vị không được vượt quá 50 ký tự.',
            'values.max' => 'Danh sách giá trị thuộc tính quá dài.',
        ]);

        $attribute->update([
            'name' => $request->name,
            'unit' => $request->unit,
        ]);

        if ($request->has('values')) {
            $attribute->values()->delete();
            $this->syncAttributeValues($attribute, $request->values);
        }

        return redirect()
            ->route('admin.attributes.index')
            ->with('success', 'Cập nhật thuộc tính thành công!');
    }

    public function destroy($id)
    {
        $attribute = Attribute::with('values')->find($id);

        if (!$attribute) {
            return redirect()
                ->route('admin.attributes.index')
                ->with('error', 'Thuộc tính không tồn tại hoặc đã bị xóa trước đó.');
        }

        try {
            $attribute->values()->delete();
            $attribute->delete();

            return redirect()
                ->route('admin.attributes.index')
                ->with('success', 'Xoá thuộc tính thành công!');
        } catch (\Throwable $e) {
            return redirect()
                ->route('admin.attributes.index')
                ->with('error', 'Không thể xóa thuộc tính vì đang có dữ liệu liên quan.');
        }
    }

    private function syncAttributeValues(Attribute $attribute, ?string $values): void
    {
        if (!$values) {
            return;
        }

        $values = explode(',', $values);

        foreach ($values as $value) {
            $value = $this->normalizeText($value);

            if ($value !== '') {
                $attribute->values()->create([
                    'value' => $value,
                ]);
            }
        }
    }

    private function normalizeText(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        // Chuyển khoảng trắng full-width thành khoảng trắng thường
        $value = str_replace('　', ' ', $value);

        // Xóa khoảng trắng đầu/cuối, bao gồm Unicode whitespace
        $value = preg_replace('/^\s+|\s+$/u', '', $value);

        // Gộp nhiều khoảng trắng liên tiếp thành một khoảng trắng
        $value = preg_replace('/\s+/u', ' ', $value);

        return $value;
    }

    private function makeAttributeSnapshot(Attribute $attribute): string
    {
        $values = $attribute->values
            ->pluck('value')
            ->map(function ($value) {
                return $this->normalizeText((string) $value);
            })
            ->sort()
            ->values()
            ->toArray();

        return sha1(json_encode([
            'attribute_id' => $attribute->attribute_id,
            'name' => $this->normalizeText($attribute->name),
            'unit' => $this->normalizeText($attribute->unit),
            'values' => $values,
        ], JSON_UNESCAPED_UNICODE));
    }
}