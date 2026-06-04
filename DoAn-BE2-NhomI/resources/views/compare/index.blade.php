@extends('layouts.app')

@section('content')
<main class="max-w-7xl mx-auto px-6 py-10">
    <h1 class="text-2xl font-black mb-6">So sánh sản phẩm</h1>

    @if(empty($products) || count($products) == 0)
        <div class="p-6 bg-white border rounded">Danh sách so sánh đang trống.</div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-{{ max(1, min(3, count($products))) }} gap-6">
            @foreach($products as $product)
                <div class="border p-4 rounded bg-white">
                    <div class="flex items-center gap-4">
                        <img src="{{ asset(str_replace(['public/', '/storage/products/'], ['', '/products/'], $product->image_url ?? 'images/products/default.png')) }}" class="w-24 h-24 object-contain" />
                        <div>
                            <div class="font-bold">{{ $product->name }}</div>
                            <div class="text-sm text-gray-500">{{ $product->brand_name ?? '' }} - {{ $product->category_name ?? '' }}</div>
                            <div class="text-red-600 font-black mt-2">{{ number_format($product->base_price,0,',','.') }}₫</div>
                        </div>
                    </div>

                    <div class="mt-4 text-sm">
                        @php $specs = json_decode($product->specs ?? null, true) ?: [] @endphp
                        <div><strong>Chipset:</strong> {{ $specs['chipset'] ?? '-' }}</div>
                        <div><strong>Camera:</strong> {{ $specs['camera'] ?? '-' }}</div>
                        <div><strong>Pin:</strong> {{ $specs['battery'] ?? '-' }}</div>
                    </div>

                    <form action="{{ route('compare.remove') }}" method="POST" class="mt-4">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->product_id }}">
                        <button class="text-sm text-red-600 font-bold">Xóa</button>
                    </form>
                </div>
            @endforeach
        </div>

        <form action="{{ route('compare.clear') }}" method="POST" class="mt-6">
            @csrf
            <button class="px-4 py-2 bg-red-600 text-white rounded">Xóa toàn bộ so sánh</button>
        </form>
    @endif
</main>
@endsection
