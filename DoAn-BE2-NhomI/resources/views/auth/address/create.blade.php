@extends('layouts.app')

@section('content')

<div class="max-w-7xl mx-auto flex flex-col lg:flex-row min-h-screen">

    {{-- ===================================================== --}}
    {{-- SIDEBAR --}}
    {{-- ===================================================== --}}
    <aside class="bg-slate-50 w-full lg:w-80 rounded-r-2xl py-8">

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

                <form action="{{ route('logout') }}" method="POST">

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

    </aside>





    {{-- ===================================================== --}}
    {{-- CONTENT --}}
    {{-- ===================================================== --}}
    <section class="flex-1 bg-gray-50 px-4 md:px-12 py-12">

        <div class="max-w-4xl mx-auto">

            {{-- TITLE --}}
            <div class="mb-12">

                <h1 class="text-3xl font-bold text-blue-900">

                    Thêm địa chỉ mới

                </h1>

                <p class="text-gray-500 mt-2">

                    Thêm địa chỉ giao hàng cho tài khoản của bạn

                </p>

            </div>





            {{-- ERROR --}}
            @if(session('error'))

                <div
                    class="bg-red-100 border border-red-300 text-red-600 px-5 py-4 rounded-2xl mb-8">

                    {{ session('error') }}

                </div>

            @endif





            {{-- CARD --}}
            <div class="bg-white rounded-2xl shadow-sm p-8">

                <form
                    id="addressForm"
                    action="{{ route('addresses.store') }}"
                    method="POST"
                    class="space-y-6">

                    @csrf





                    {{-- FULL NAME --}}
                    <div>

                        <label
                            class="block text-xs font-bold uppercase tracking-widest text-blue-900 mb-2">

                            Họ và tên

                        </label>

                        <input
                            type="text"
                            name="full_name"
                            value="{{ old('full_name') }}"
                            class="w-full border-0 border-b-2 rounded-md transition border-gray-200
                            focus:border-blue-900 focus:ring-0 py-3 px-0">



                        @error('full_name')

                            <p class="text-red-500 text-sm mt-2">

                                {{ $message }}

                            </p>

                        @enderror

                    </div>





                    {{-- PHONE --}}
                    <div>

                        <label
                            class="block text-xs font-bold uppercase tracking-widest text-blue-900 mb-2">

                            Số điện thoại

                        </label>

                        <input
                            type="text"
                            name="phone"
                            value="{{ old('phone') }}"
                            class="w-full border-0 border-b-2 rounded-md transition border-gray-200
                            focus:border-blue-900 focus:ring-0 py-3 px-0">



                        @error('phone')

                            <p class="text-red-500 text-sm mt-2">

                                {{ $message }}

                            </p>

                        @enderror

                    </div>





                    {{-- ADDRESS --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                        {{-- province --}}
                        <div>

                            <label
                                class="block text-xs font-bold uppercase tracking-widest text-blue-900 mb-2">

                                Tỉnh / Thành phố

                            </label>

                            <select
                                id="province"
                                name="province"
                                data-old="{{ old('province') }}"
                                class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:border-blue-900 focus:ring-0">

                                <option value="">

                                    Chọn tỉnh/thành

                                </option>

                            </select>



                            @error('province')

                                <p class="text-red-500 text-sm mt-2">

                                    {{ $message }}

                                </p>

                            @enderror

                        </div>





                        {{-- district --}}
                        <div>

                            <label
                                class="block text-xs font-bold uppercase tracking-widest text-blue-900 mb-2">

                                Quận / Huyện

                            </label>

                            <select
                                id="district"
                                name="district"
                                data-old="{{ old('district') }}"
                                class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:border-blue-900 focus:ring-0">

                                <option value="">

                                    Chọn quận/huyện

                                </option>

                            </select>



                            @error('district')

                                <p class="text-red-500 text-sm mt-2">

                                    {{ $message }}

                                </p>

                            @enderror

                        </div>





                        {{-- ward --}}
                        <div>

                            <label
                                class="block text-xs font-bold uppercase tracking-widest text-blue-900 mb-2">

                                Phường / Xã

                            </label>

                            <select
                                id="ward"
                                name="ward"
                                data-old="{{ old('ward') }}"
                                class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:border-blue-900 focus:ring-0">

                                <option value="">

                                    Chọn phường/xã

                                </option>

                            </select>



                            @error('ward')

                                <p class="text-red-500 text-sm mt-2">

                                    {{ $message }}

                                </p>

                            @enderror

                        </div>

                    </div>





                    {{-- STREET --}}
                    <div>

                        <label
                            class="block text-xs font-bold uppercase tracking-widest text-blue-900 mb-2">

                            Địa chỉ cụ thể

                        </label>

                        <textarea
                            name="street_address"
                            rows="4"
                            class="w-full border border-gray-200 rounded-2xl focus:border-blue-900 focus:ring-0 p-4 resize-none">{{ old('street_address') }}</textarea>



                        @error('street_address')

                            <p class="text-red-500 text-sm mt-2">

                                {{ $message }}

                            </p>

                        @enderror

                    </div>





                    {{-- BUTTON --}}
                    <div class="pt-8">

                        <button
                            type="button"
                            id="saveButton"
                            onclick="openSaveModal()"
                            class="bg-gradient-to-r from-blue-900 to-blue-700 text-white py-3 px-12 rounded-xl font-bold uppercase tracking-widest hover:brightness-110 transition">

                            <span id="saveButtonText">

                                Lưu địa chỉ

                            </span>

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </section>

</div>





{{-- ===================================================== --}}
{{-- MODAL SAVE --}}
{{-- ===================================================== --}}
<div
    id="saveModal"
    class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">

    <div
        class="bg-white rounded-2xl p-6 w-[90%] max-w-md">

        <h3
            class="text-xl font-bold text-blue-900 mb-3">

            Xác nhận thêm địa chỉ

        </h3>

        <p
            class="text-gray-500 mb-6">

            Bạn có chắc muốn thêm địa chỉ mới không?

        </p>

        <div class="flex justify-end gap-3">

            <button
                onclick="closeSaveModal()"
                class="px-5 py-2 rounded-xl border">

                Không

            </button>

            <button
                onclick="submitAddressForm()"
                class="bg-blue-900 text-white px-5 py-2 rounded-xl">

                Có

            </button>

        </div>

    </div>

</div>

<script>

    // =====================================================
    // SELECT
    // =====================================================
    const province =
        document.getElementById('province');

    const district =
        document.getElementById('district');

    const ward =
        document.getElementById('ward');



    // =====================================================
    // OLD VALUE
    // =====================================================
    const oldProvince =
        province.dataset.old;

    const oldDistrict =
        district.dataset.old;

    const oldWard =
        ward.dataset.old;





    // =====================================================
    // LOAD PROVINCES
    // =====================================================
    async function loadProvinces() {

        const response =
            await fetch(
                'https://provinces.open-api.vn/api/p/'
            );



        const data =
            await response.json();



        data.forEach(function(item) {

            const option =
                new Option(
                    item.name,
                    item.name
                );



            if (item.name == oldProvince) {

                option.selected = true;

            }



            province.options[
                province.options.length
            ] = option;

        });



        if (oldProvince) {

            await loadDistricts(oldProvince);

        }

    }





    // =====================================================
    // LOAD DISTRICTS
    // =====================================================
    async function loadDistricts(provinceName) {

        district.length = 1;

        ward.length = 1;



        const response =
            await fetch(
                'https://provinces.open-api.vn/api/p/'
            );



        const provinces =
            await response.json();



        const provinceData =
            provinces.find(
                p => p.name == provinceName
            );



        if (!provinceData) return;



        const districtResponse =
            await fetch(
                `https://provinces.open-api.vn/api/p/${provinceData.code}?depth=2`
            );



        const data =
            await districtResponse.json();



        data.districts.forEach(function(item) {

            const option =
                new Option(
                    item.name,
                    item.name
                );



            if (item.name == oldDistrict) {

                option.selected = true;

            }



            district.options[
                district.options.length
            ] = option;

        });



        if (oldDistrict) {

            await loadWards(oldDistrict);

        }

    }





    // =====================================================
    // LOAD WARDS
    // =====================================================
    async function loadWards(districtName) {

        ward.length = 1;



        const response =
            await fetch(
                'https://provinces.open-api.vn/api/d/'
            );



        const districts =
            await response.json();



        const districtData =
            districts.find(
                d => d.name == districtName
            );



        if (!districtData) return;



        const wardResponse =
            await fetch(
                `https://provinces.open-api.vn/api/d/${districtData.code}?depth=2`
            );



        const data =
            await wardResponse.json();



        data.wards.forEach(function(item) {

            const option =
                new Option(
                    item.name,
                    item.name
                );



            if (item.name == oldWard) {

                option.selected = true;

            }



            ward.options[
                ward.options.length
            ] = option;

        });

    }





    // =====================================================
    // CHANGE
    // =====================================================
    province.onchange = function() {

        loadDistricts(this.value);

    };



    district.onchange = function() {

        loadWards(this.value);

    };



    loadProvinces();





    // =====================================================
    // MODAL
    // =====================================================
    function openSaveModal() {

        document
            .getElementById(
                'saveModal'
            )
            .classList.remove(
                'hidden'
            );



        document
            .getElementById(
                'saveModal'
            )
            .classList.add(
                'flex'
            );

    }





    function closeSaveModal() {

        document
            .getElementById(
                'saveModal'
            )
            .classList.add(
                'hidden'
            );



        document
            .getElementById(
                'saveModal'
            )
            .classList.remove(
                'flex'
            );

    }





    // =====================================================
    // SUBMIT
    // =====================================================
    function submitAddressForm() {

        const button =
            document.getElementById(
                'saveButton'
            );



        const text =
            document.getElementById(
                'saveButtonText'
            );



        text.innerHTML =
            'ĐANG LƯU... ⏳';



        button.disabled = true;



        setTimeout(() => {

            document
                .getElementById(
                    'addressForm'
                )
                .submit();

        }, 1000);

    }





    // =====================================================
    // SUCCESS
    // =====================================================
    function closeSuccessModal() {

        window.location.href =
            "{{ route('addresses.index') }}";

    }

</script>

@endsection