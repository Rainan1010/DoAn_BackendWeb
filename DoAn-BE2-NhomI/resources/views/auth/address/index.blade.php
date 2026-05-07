@extends('layouts.app')

@section('content')

<div class="min-h-screen bg-gray-50 py-10 px-4">

    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8">

        {{-- ===================================================== --}}
        {{-- SIDEBAR --}}
        {{-- ===================================================== --}}
        <aside class="lg:col-span-3">

            <div class="bg-slate-50 rounded-2xl py-8 overflow-hidden">

                {{-- title --}}
                <div class="px-8 mb-6">

                    <h2 class="text-blue-900 text-sm font-bold uppercase tracking-widest">

                        Account Settings

                    </h2>

                    <p class="text-gray-500 text-xs mt-1">

                        Quản lý thông tin tài khoản

                    </p>

                </div>





                {{-- MENU --}}
                <div class="flex flex-col gap-1">

                    {{-- profile --}}
                    <a
                        href="{{ url('/profile') }}"
                        class="flex items-center gap-4 py-4 text-gray-500 pl-8 hover:bg-gray-100 transition">

                        <span class="material-symbols-outlined">

                            person

                        </span>

                        <span class="text-sm uppercase tracking-widest">

                            Thông tin tài khoản

                        </span>

                    </a>





                    {{-- password --}}
                    <a
                        href="{{ url('/password/change') }}"
                        class="flex items-center gap-4 py-4 text-gray-500 pl-8 hover:bg-gray-100 transition">

                        <span class="material-symbols-outlined">

                            shield

                        </span>

                        <span class="text-sm uppercase tracking-widest">

                            Đổi mật khẩu

                        </span>

                    </a>





                    {{-- address --}}
                    <a
                        href="{{ route('addresses.index') }}"
                        class="flex items-center gap-4 py-4 text-blue-900 font-bold border-l-4 border-blue-900 pl-8 bg-white">

                        <span class="material-symbols-outlined">

                            location_on

                        </span>

                        <span class="text-sm uppercase tracking-widest">

                            Địa chỉ giao hàng

                        </span>

                    </a>





                    {{-- logout --}}
                    <div class="mt-8 border-t pt-4">

                        <form
                            action="{{ route('logout') }}"
                            method="POST">

                            @csrf

                            <button
                                class="flex items-center gap-4 py-4 text-red-500 pl-8 hover:bg-red-50 transition w-full">

                                <span class="material-symbols-outlined">

                                    logout

                                </span>

                                <span class="text-sm uppercase tracking-widest">

                                    Đăng xuất

                                </span>

                            </button>

                        </form>

                    </div>

                </div>

            </div>

        </aside>





        {{-- ===================================================== --}}
        {{-- CONTENT --}}
        {{-- ===================================================== --}}
        <section class="lg:col-span-9">

            <div class="bg-white rounded-3xl shadow-sm p-8 md:p-12">

                {{-- HEADER --}}
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">

                    <div>

                        <h1 class="text-4xl font-black tracking-tight text-blue-900 mb-2">

                            Địa chỉ của tôi

                        </h1>

                        <p class="text-gray-500 max-w-lg">

                            Quản lý các địa chỉ nhận hàng của bạn để quá trình
                            thanh toán diễn ra nhanh chóng và chính xác nhất.

                        </p>

                    </div>





                    {{-- add address --}}
                    <a
                        href="{{ route('addresses.create') }}"
                        class="bg-blue-900 text-white px-6 py-3 rounded-xl font-semibold flex items-center gap-2 hover:bg-blue-800 transition shadow-lg shadow-blue-900/20 active:scale-95">

                        <span class="material-symbols-outlined">

                            add

                        </span>

                        Thêm địa chỉ mới

                    </a>

                </div>





                {{-- LIST ADDRESS --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                    @forelse($addresses as $address)

                        <div class="
                            {{ $address->is_default
                                ? 'bg-slate-50 border-l-4 border-blue-900'
                                : 'bg-gray-50'
                            }}

                            p-8 rounded-2xl shadow-sm relative overflow-hidden group
                        ">





                            {{-- icon --}}
                            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">

                                <span class="material-symbols-outlined text-8xl">

                                    {{ $address->is_default
                                        ? 'home'
                                        : 'apartment'
                                    }}

                                </span>

                            </div>





                            {{-- content --}}
                            <div class="relative z-10">

                                {{-- top --}}
                                <div class="flex items-center gap-3 mb-6">

                                    @if($address->is_default)

                                        <span
                                            class="bg-blue-100 text-blue-900 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider">

                                            Mặc định

                                        </span>

                                    @endif





                                    <span class="text-blue-900 font-bold text-sm">

                                        {{ $address->is_default
                                            ? 'Địa chỉ chính'
                                            : 'Địa chỉ phụ'
                                        }}

                                    </span>

                                </div>





                                {{-- full name --}}
                                <h3 class="text-xl font-bold text-blue-900 mb-1">

                                    {{ $address->full_name }}

                                </h3>





                                {{-- phone --}}
                                <p class="text-gray-500 font-medium mb-4">

                                    {{ $address->phone }}

                                </p>





                                {{-- address --}}
                                <div class="space-y-1 text-gray-600 text-sm mb-8 leading-relaxed">

                                    <p>

                                        {{ $address->street_address }}

                                    </p>

                                    <p>

                                        {{ $address->ward }}

                                    </p>

                                    <p>

                                        {{ $address->district }}

                                    </p>

                                    <p>

                                        {{ $address->province }}

                                    </p>

                                </div>





                                {{-- actions --}}
                                <div class="flex gap-4 items-center">

                                    {{-- edit --}}
                                    <a
                                        href="{{ route('addresses.edit', $address->address_id) }}"
                                        class="text-blue-900 font-bold text-xs uppercase tracking-widest hover:underline flex items-center gap-1">

                                        <span class="material-symbols-outlined text-sm">

                                            edit

                                        </span>

                                        Sửa

                                    </a>





                                    {{-- delete --}}
                                    <form
                                        id="deleteForm{{ $address->address_id }}"
                                        action="{{ route('addresses.destroy', $address->address_id) }}"
                                        method="POST">

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="button"
                                            onclick="openDeleteModal({{ $address->address_id }})"
                                            class="text-red-500 font-bold text-xs uppercase tracking-widest hover:underline flex items-center gap-1">

                                            <span
                                                class="material-symbols-outlined text-sm">

                                                delete

                                            </span>

                                            Xoá

                                        </button>

                                    </form>





                                    {{-- set default --}}
                                    @if(!$address->is_default)

                                        <button
                                            class="ml-auto text-gray-500 font-bold text-[10px] uppercase tracking-widest hover:text-blue-900 transition-colors">

                                            Thiết lập mặc định

                                        </button>

                                    @endif

                                </div>

                            </div>

                        </div>





                    {{-- EMPTY --}}
                    @empty

                        <div
                            class="col-span-full border-2 border-dashed border-gray-300 rounded-2xl p-16 text-center">

                            <div
                                class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-5">

                                <span
                                    class="material-symbols-outlined text-5xl text-gray-400">

                                    location_on

                                </span>

                            </div>





                            <h3
                                class="text-2xl font-bold text-gray-700 mb-3">

                                Chưa có địa chỉ

                            </h3>





                            <p class="text-gray-500">

                                Bạn chưa thêm địa chỉ giao hàng nào.

                            </p>

                        </div>

                    @endforelse

                </div>

            </div>

        </section>

    </div>

</div>





{{-- ===================================================== --}}
{{-- DELETE MODAL --}}
{{-- ===================================================== --}}
<div
    id="deleteModal"
    class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">

    <div
        class="bg-white rounded-3xl p-8 w-[90%] max-w-md text-center">

        <div
            class="w-20 h-20 rounded-full bg-red-100 mx-auto flex items-center justify-center mb-5">

            <span
                class="material-symbols-outlined text-red-600 text-5xl">

                delete

            </span>

        </div>





        <h3
            class="text-2xl font-bold text-red-600 mb-3">

            Xác nhận xoá

        </h3>





        <p
            class="text-gray-500 leading-relaxed">

            Bạn có chắc muốn xoá địa chỉ này không?

        </p>





        <div class="flex justify-center gap-3 mt-8">

            <button
                onclick="toggleModal('deleteModal')"
                class="px-6 py-3 rounded-2xl border border-gray-300 hover:bg-gray-100 transition">

                Không

            </button>





            <button
                onclick="submitDeleteForm()"
                class="bg-red-600 hover:bg-red-700 transition text-white px-6 py-3 rounded-2xl font-bold">

                Có

            </button>

        </div>

    </div>

</div>





{{-- ===================================================== --}}
{{-- MESSAGE MODAL --}}
{{-- ===================================================== --}}
@if(session('success') || session('error'))

    @php

        $success =
            session('success');



        $message =
            session('success')
            ?? session('error');

    @endphp

    <div
        id="messageModal"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">

        <div
            class="bg-white rounded-3xl p-8 w-[90%] max-w-md text-center">

            <div
                class="w-20 h-20 rounded-full mx-auto flex items-center justify-center mb-5

                {{ $success
                    ? 'bg-green-100'
                    : 'bg-red-100'
                }}">

                <span
                    class="material-symbols-outlined text-5xl

                    {{ $success
                        ? 'text-green-600'
                        : 'text-red-600'
                    }}">

                    {{ $success
                        ? 'check_circle'
                        : 'error'
                    }}

                </span>

            </div>





            <h3
                class="text-2xl font-bold mb-3

                {{ $success
                    ? 'text-green-600'
                    : 'text-red-600'
                }}">

                {{ $success
                    ? 'Thành công'
                    : 'Thông báo'
                }}

            </h3>





            <p
                class="text-gray-500 leading-relaxed">

                {{ $message }}

            </p>





            <button
                onclick="closeModal('messageModal')"
                class="mt-8 text-white px-8 py-3 rounded-2xl font-bold transition

                {{ $success
                    ? 'bg-green-600 hover:bg-green-700'
                    : 'bg-red-600 hover:bg-red-700'
                }}">

                OK

            </button>

        </div>

    </div>

@endif





<script>

    let currentDeleteId = null;





    // =====================================================
    // TOGGLE MODAL
    // =====================================================
    function toggleModal(id) {

        const modal =
            document.getElementById(id);



        modal.classList.toggle('hidden');

        modal.classList.toggle('flex');

    }





    // =====================================================
    // CLOSE MODAL
    // =====================================================
    function closeModal(id) {

        document
            .getElementById(id)
            ?.remove();

    }





    // =====================================================
    // OPEN DELETE MODAL
    // =====================================================
    function openDeleteModal(id) {

        currentDeleteId = id;

        toggleModal('deleteModal');

    }





    // =====================================================
    // SUBMIT DELETE
    // =====================================================
    function submitDeleteForm() {

        document
            .getElementById(
                `deleteForm${currentDeleteId}`
            )
            .submit();

    }

</script>

@endsection