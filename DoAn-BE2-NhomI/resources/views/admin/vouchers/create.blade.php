@extends('admin.layouts.app')

@section('header_search')
<div class="relative">
    <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>
    <input type="text" placeholder="Tìm kiếm voucher..." class="w-full bg-[#F4F5F7] border-none rounded-xl py-3 pl-12 pr-4 focus:ring-2 focus:ring-[#0A2540]/10 text-sm">
</div>
@endsection

@section('content')
<div x-data="{ 
    code: 'TECHSPRING20',
    type: 'percent',
    value: 15,
    min_order: 500000,
    max_discount: 100000,
    start_at: '',
    end_at: '2024-12-31',
    is_active: true
}" class="pb-10">
    
    <form action="{{ route('admin.vouchers.store') }}" method="POST">
        @csrf
        
        <!-- Header Actions -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <nav class="flex text-xs font-medium text-gray-400 mb-2 gap-2">
                    <span>Khuyến mãi</span>
                    <span>&rsaquo;</span>
                    <span class="text-gray-600">Thêm Voucher Mới</span>
                </nav>
                <h1 class="text-4xl font-black text-[#0A2540] tracking-tight">Thêm Voucher Mới</h1>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.vouchers.index') }}" class="px-8 py-3 bg-white border border-gray-200 rounded-xl text-sm font-bold text-gray-700 hover:bg-gray-50 transition-colors">
                    Hủy bỏ
                </a>
                <button type="submit" class="px-8 py-3 bg-[#0A2540] text-white rounded-xl text-sm font-bold hover:bg-[#113255] transition-colors shadow-lg shadow-[#0A2540]/20">
                    Lưu Voucher
                </button>
            </div>
        </div>

        @if($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-red-50 text-red-600 border border-red-100">
                <ul class="list-disc list-inside text-sm font-bold">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Form -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Basic Info -->
                <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-[#0A2540]">
                            <i data-lucide="info" class="w-5 h-5"></i>
                        </div>
                        <h2 class="text-xl font-black text-[#0A2540]">Thông tin cơ bản</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">Voucher ID (Tự động)</label>
                            <input type="text" value="VCH-2024-001" disabled class="w-full bg-gray-50 border-none rounded-xl py-3 px-4 text-sm font-bold text-gray-400 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">Mã Voucher (Code)</label>
                            <input type="text" name="code" x-model="code" placeholder="Ví dụ: TECHSPRING20" class="w-full bg-[#F4F5F7] border-none rounded-xl py-3 px-4 text-sm font-bold text-[#0A2540] focus:ring-2 focus:ring-[#0A2540]/10 uppercase tracking-wider">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">Loại Voucher</label>
                            <select name="type" x-model="type" class="w-full bg-[#F4F5F7] border-none rounded-xl py-3 px-4 text-sm font-bold text-[#0A2540] focus:ring-2 focus:ring-[#0A2540]/10">
                                <option value="percent">Phần trăm (%)</option>
                                <option value="fixed">Số tiền cố định (VNĐ)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">Giá trị giảm</label>
                            <div class="relative">
                                <input type="number" name="value" x-model="value" class="w-full bg-[#F4F5F7] border-none rounded-xl py-3 px-4 text-sm font-bold text-[#0A2540] focus:ring-2 focus:ring-[#0A2540]/10">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold" x-text="type === 'percent' ? '%' : 'đ'"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Conditions -->
                <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-[#0A2540]">
                            <i data-lucide="filter" class="w-5 h-5"></i>
                        </div>
                        <h2 class="text-xl font-black text-[#0A2540]">Điều kiện áp dụng</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">Giá trị đơn hàng tối thiểu</label>
                            <div class="relative">
                                <input type="number" name="min_order_value" x-model="min_order" class="w-full bg-[#F4F5F7] border-none rounded-xl py-3 px-4 text-sm font-bold text-[#0A2540] focus:ring-2 focus:ring-[#0A2540]/10">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">đ</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">Mức giảm tối đa</label>
                            <div class="relative">
                                <input type="number" name="max_discount" x-model="max_discount" class="w-full bg-[#F4F5F7] border-none rounded-xl py-3 px-4 text-sm font-bold text-[#0A2540] focus:ring-2 focus:ring-[#0A2540]/10">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">đ</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">Giới hạn sử dụng</label>
                            <input type="number" name="usage_limit" placeholder="1000" class="w-full bg-[#F4F5F7] border-none rounded-xl py-3 px-4 text-sm font-bold text-[#0A2540] focus:ring-2 focus:ring-[#0A2540]/10">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">Đã sử dụng</label>
                            <input type="text" value="0" disabled class="w-full bg-gray-50 border-none rounded-xl py-3 px-4 text-sm font-bold text-gray-400 cursor-not-allowed">
                        </div>
                    </div>
                </div>

                <!-- Time & Status -->
                <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-[#0A2540]">
                            <i data-lucide="clock" class="w-5 h-5"></i>
                        </div>
                        <h2 class="text-xl font-black text-[#0A2540]">Thời gian & Trạng thái</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">Thời gian bắt đầu</label>
                            <input type="datetime-local" name="start_at" x-model="start_at" class="w-full bg-[#F4F5F7] border-none rounded-xl py-3 px-4 text-sm font-bold text-[#0A2540] focus:ring-2 focus:ring-[#0A2540]/10">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">Thời gian kết thúc</label>
                            <input type="datetime-local" name="end_at" x-model="end_at" class="w-full bg-[#F4F5F7] border-none rounded-xl py-3 px-4 text-sm font-bold text-[#0A2540] focus:ring-2 focus:ring-[#0A2540]/10">
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-2xl p-4 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-[#0A2540] border border-gray-200">
                                <i data-lucide="power" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-[#0A2540]">Kích hoạt ngay</p>
                                <p class="text-xs text-gray-500">Voucher sẽ có thể sử dụng ngay khi được lưu.</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" x-model="is_active" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#0A2540]"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Right Column: Preview & Tips -->
            <div class="lg:col-span-1">
                <div class="space-y-8 sticky top-8 self-start">
                    <!-- Live Preview -->
                    <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm">
                    <div class="flex items-center gap-3 mb-6">
                        <i data-lucide="eye" class="w-4 h-4 text-gray-400"></i>
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Live Preview</span>
                    </div>

                    <div class="bg-[#F8FAFC] rounded-2xl p-6 relative">
                        <!-- Voucher Card UI -->
                        <div class="bg-[#0A2540] rounded-2xl p-8 text-white text-center relative overflow-hidden">
                            <!-- Cutouts -->
                            <div class="absolute left-[-10px] top-1/2 -translate-y-1/2 w-5 h-5 bg-[#F8FAFC] rounded-full"></div>
                            <div class="absolute right-[-10px] top-1/2 -translate-y-1/2 w-5 h-5 bg-[#F8FAFC] rounded-full"></div>
                            
                            <div class="relative z-10 flex flex-col items-center">
                                <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center mb-4">
                                    <i data-lucide="ticket" class="w-5 h-5 text-white"></i>
                                </div>
                                <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-blue-300 mb-2">Giảm ngay</p>
                                <h4 class="text-4xl font-black mb-2" x-text="type === 'percent' ? value + '%' : new Intl.NumberFormat('vi-VN').format(value) + 'đ'"></h4>
                                <p class="text-[10px] text-blue-100/60 font-medium leading-relaxed max-w-[150px]">
                                    Tối đa <span x-text="new Intl.NumberFormat('vi-VN').format(max_discount)"></span>đ cho đơn từ <span x-text="new Intl.NumberFormat('vi-VN').format(min_order)"></span>đ
                                </p>

                                <div class="mt-8 w-full">
                                    <div class="bg-white/10 border border-white/20 rounded-xl p-3 flex items-center justify-between">
                                        <span class="text-xs font-black tracking-widest uppercase" x-text="code"></span>
                                        <i data-lucide="copy" class="w-3 h-3 text-white/40"></i>
                                    </div>
                                    <p class="text-[8px] text-white/30 uppercase font-bold tracking-widest mt-4">Hết hạn: <span x-text="end_at || '31/12/2024'"></span></p>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Specs -->
                        <div class="mt-8 space-y-4">
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-gray-400 font-bold uppercase tracking-wider">Phạm vi:</span>
                                <span class="text-[#0A2540] font-black">Toàn sàn</span>
                            </div>
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-gray-400 font-bold uppercase tracking-wider">Đối tượng:</span>
                                <span class="text-[#0A2540] font-black">Khách hàng mới</span>
                            </div>
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-gray-400 font-bold uppercase tracking-wider">Hình thức:</span>
                                <span class="text-[#0A2540] font-black">Mã công khai</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Editorial Tips -->
                <div class="bg-gradient-to-br from-[#0A2540] to-[#113255] rounded-3xl p-8 text-white relative overflow-hidden shadow-xl">
                    <div class="absolute right-[-20px] top-[-20px] opacity-10">
                        <i data-lucide="lightbulb" class="w-32 h-32"></i>
                    </div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-4">
                            <i data-lucide="lightbulb" class="w-5 h-5 text-yellow-400"></i>
                            <h3 class="text-lg font-black tracking-tight">Mẹo Editorial</h3>
                        </div>
                        <p class="text-xs leading-relaxed text-blue-100/80 font-medium">
                            Sử dụng mã ngắn gọn, dễ nhớ như "SPRING24" thay vì mã tự động. Điều này giúp tăng tỷ lệ chuyển đổi đơn hàng lên tới 22% cho các chiến dịch theo mùa.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
