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
        $request->validate([
            'name' => 'required|string|max:100',
            'unit' => 'nullable|string|max:50',
            'values' => 'nullable|string',
        ]);

        $attribute = Attribute::create([
            'name' => $request->name,
            'unit' => $request->unit,
        ]);

        if ($request->filled('values')) {
            $values = explode(',', $request->values);

            foreach ($values as $value) {
                $value = trim($value);

                if ($value !== '') {
                    $attribute->values()->create([
                        'value' => $value,
                    ]);
                }
            }
        }

        return redirect()
            ->route('admin.attributes.index')
            ->with('success', 'Thêm thuộc tính thành công!');
    }

    public function edit($id)
    {
        $attribute = Attribute::with('values')->findOrFail($id);

        return view('admin.attributes.edit', compact('attribute'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'unit' => 'nullable|string|max:50',
        ]);

        $attribute = Attribute::findOrFail($id);

        $attribute->update([
            'name' => $request->name,
            'unit' => $request->unit,
        ]);

        return redirect()
            ->route('admin.attributes.index')
            ->with('success', 'Cập nhật thuộc tính thành công!');
    }

    public function destroy($id)
    {
        $attribute = Attribute::findOrFail($id);

        $attribute->values()->delete();
        $attribute->delete();

        return redirect()
            ->route('admin.attributes.index')
            ->with('success', 'Xoá thuộc tính thành công!');
    }
}
