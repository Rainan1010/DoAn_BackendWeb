@extends('admin.layouts.app')

@section('header_search')
<div class="relative">
    <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>
    <input type="text" placeholder="Search vouchers, codes, or campaigns..." class="w-full bg-[#F4F5F7] border-none rounded-xl py-3 pl-12 pr-4 focus:ring-2 focus:ring-[#0A2540]/10 text-sm">
</div>
@endsection

@section('content')
<div class="space-y-8 pb-10">
    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Vouchers -->
        <div class="bg-[#0A2540] rounded-3xl p-8 text-white relative overflow-hidden shadow-2xl shadow-[#0A2540]/20">
            <div class="absolute right-[-20px] bottom-[-20px] opacity-10">
                <i data-lucide="ticket" class="w-40 h-40"></i>
            </div>
            <div class="relative z-10">
                <p class="text-[11px] font-bold uppercase tracking-widest text-blue-300 mb-2">Total Vouchers</p>
                <h3 class="text-5xl font-black mb-4">{{ number_format($stats['total'] ?? 0) }}</h3>
                <div class="flex items-center gap-2 text-xs font-bold text-green-400">
                    <i data-lucide="trending-up" class="w-4 h-4"></i>
                    <span>+12% from last month</span>
                </div>
            </div>
        </div>

        <!-- Active Now -->
        <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-2">Active Now</p>
            <h3 class="text-5xl font-black text-[#0A2540] mb-6">{{ number_format($stats['active'] ?? 0) }}</h3>
            
            <div class="space-y-2">
                @php
                    $percentActive = $stats['total'] > 0 ? ($stats['active'] / $stats['total']) * 100 : 0;
                @endphp
                <div class="h-2 w-full bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-[#0A2540] rounded-full" style="width: {{ $percentActive }}%"></div>
                </div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ round($percentActive) }}% of total pool active</p>
            </div>
        </div>

        <!-- Used Rate -->
        <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm relative">
            <div class="absolute right-8 top-8 w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600">
                <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
            </div>
            <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-2">Used Rate</p>
            <h3 class="text-5xl font-black text-[#0A2540] mb-6">{{ $stats['used_rate'] ?? 0 }}%</h3>
            
            <div class="flex items-center gap-3">
                <div class="flex -space-x-2">
                    <img class="w-7 h-7 rounded-full border-2 border-white" src="https://i.pravatar.cc/100?img=1" alt="">
                    <img class="w-7 h-7 rounded-full border-2 border-white" src="https://i.pravatar.cc/100?img=2" alt="">
                    <div class="w-7 h-7 rounded-full border-2 border-white bg-gray-100 flex items-center justify-center text-[8px] font-bold text-gray-500">+12k</div>
                </div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Redeemed by users</p>
            </div>
        </div>
    </div>

    <!-- Main Table Section -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-8 border-b border-gray-50 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-2xl font-black text-[#0A2540] mb-1">Voucher Inventory</h2>
                <p class="text-sm text-gray-500">Manage promotional codes and technical engineering discounts.</p>
            </div>
            <div class="flex items-center gap-3 w-full md:w-auto">
                <button class="flex-1 md:flex-none flex items-center justify-center gap-2 px-5 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-bold text-gray-700 hover:bg-gray-50 transition-colors">
                    <i data-lucide="filter" class="w-4 h-4"></i> Filter
                </button>
                <button class="flex-1 md:flex-none flex items-center justify-center gap-2 px-5 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-bold text-gray-700 hover:bg-gray-50 transition-colors">
                    <i data-lucide="download" class="w-4 h-4"></i> Export CSV
                </button>
                <a href="{{ route('admin.vouchers.create') }}" class="flex-1 md:flex-none flex items-center justify-center gap-2 px-6 py-2.5 bg-[#0A2540] text-white rounded-xl text-sm font-bold hover:bg-[#113255] transition-colors shadow-lg shadow-[#0A2540]/20">
                    <i data-lucide="plus" class="w-4 h-4"></i> Create Voucher
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="py-4 px-8 text-[11px] font-bold text-gray-400 uppercase tracking-widest">ID</th>
                        <th class="py-4 px-8 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Code</th>
                        <th class="py-4 px-8 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Type</th>
                        <th class="py-4 px-8 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Value</th>
                        <th class="py-4 px-8 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Min Order</th>
                        <th class="py-4 px-8 text-[11px] font-bold text-gray-400 uppercase tracking-widest text-center">Limit / Used</th>
                        <th class="py-4 px-8 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Validity</th>
                        <th class="py-4 px-8 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Status</th>
                        <th class="py-4 px-8 text-[11px] font-bold text-gray-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($vouchers as $voucher)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="py-5 px-8">
                            <span class="text-[11px] font-bold text-gray-400">VCH-{{ str_pad($voucher->voucher_id, 3, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td class="py-5 px-8">
                            <span class="text-sm font-black text-[#0A2540] uppercase tracking-wide">{{ $voucher->code }}</span>
                        </td>
                        <td class="py-5 px-8">
                            @if($voucher->type == 'percent')
                            <span class="bg-blue-50 text-blue-600 text-[10px] font-bold px-2.5 py-1 rounded-md uppercase">PERCENT</span>
                            @else
                            <span class="bg-orange-50 text-orange-600 text-[10px] font-bold px-2.5 py-1 rounded-md uppercase">FIXED</span>
                            @endif
                        </td>
                        <td class="py-5 px-8">
                            <span class="text-sm font-black text-[#0A2540]">{{ $voucher->type == 'percent' ? $voucher->value . '%' : number_format($voucher->value) . ' đ' }}</span>
                        </td>
                        <td class="py-5 px-8">
                            <span class="text-sm font-medium text-gray-600">{{ number_format($voucher->min_order_value) }} đ</span>
                        </td>
                        <td class="py-5 px-8">
                            <div class="flex flex-col items-center gap-1">
                                @php
                                    $usagePercent = ($voucher->usage_limit > 0) ? ($voucher->used_count / $voucher->usage_limit) * 100 : 0;
                                @endphp
                                <div class="h-1.5 w-16 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-[#0A2540] rounded-full" style="width: {{ $usagePercent }}%"></div>
                                </div>
                                <span class="text-[10px] font-bold text-gray-400">{{ $voucher->used_count }} / {{ $voucher->usage_limit ?? '∞' }}</span>
                            </div>
                        </td>
                        <td class="py-5 px-8">
                            <div class="text-[10px] leading-relaxed">
                                <p class="text-gray-400 font-bold uppercase"><span class="text-gray-300">S:</span> {{ $voucher->start_at ? \Carbon\Carbon::parse($voucher->start_at)->format('M d, Y') : 'N/A' }}</p>
                                <p class="text-gray-400 font-bold uppercase"><span class="text-gray-300">E:</span> {{ $voucher->end_at ? \Carbon\Carbon::parse($voucher->end_at)->format('M d, Y') : 'N/A' }}</p>
                            </div>
                        </td>
                        <td class="py-5 px-8">
                            @php
                                $isActive = $voucher->is_active && ($voucher->end_at ? \Carbon\Carbon::parse($voucher->end_at)->isFuture() : true);
                            @endphp
                            <div class="flex items-center gap-2">
                                <div class="w-1.5 h-1.5 rounded-full {{ $isActive ? 'bg-green-500' : 'bg-gray-400' }}"></div>
                                <span class="text-[10px] font-black uppercase tracking-wider {{ $isActive ? 'text-green-600' : 'text-gray-400' }}">
                                    {{ $isActive ? 'ACTIVE' : 'EXHAUSTED' }}
                                </span>
                            </div>
                        </td>
                        <td class="py-5 px-8">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.vouchers.show', $voucher->voucher_id) }}" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-400 transition-colors" title="Xem chi tiết">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                                <a href="{{ route('admin.vouchers.edit', $voucher->voucher_id) }}" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-400 transition-colors" title="Chỉnh sửa">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </a>
                                <form action="{{ route('admin.vouchers.destroy', $voucher->voucher_id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Delete this voucher?')" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-red-50 text-gray-400 hover:text-red-500 transition-colors">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-20 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center text-gray-300">
                                    <i data-lucide="ticket" class="w-10 h-10"></i>
                                </div>
                                <div>
                                    <p class="text-lg font-bold text-[#0A2540]">No vouchers found</p>
                                    <p class="text-sm text-gray-400">Get started by creating your first promotional code.</p>
                                </div>
                                <a href="{{ route('admin.vouchers.create') }}" class="mt-2 px-6 py-2.5 bg-[#0A2540] text-white rounded-xl text-sm font-bold">Create Voucher</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-8 border-t border-gray-50 flex justify-between items-center">
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">
                Showing 1 to {{ $vouchers->count() }} of {{ $stats['total'] }} results
            </p>
            <div class="flex items-center gap-2">
                <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-400 hover:bg-gray-50" disabled>
                    <i data-lucide="chevron-left" class="w-4 h-4"></i>
                </button>
                <button class="w-8 h-8 flex items-center justify-center rounded-lg bg-[#0A2540] text-white text-xs font-bold shadow-lg shadow-[#0A2540]/20">1</button>
                <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-400 hover:bg-gray-50">
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
