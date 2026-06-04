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

                {{-- active --}}
                <a href="{{ url('/profile') }}"
                    class="flex items-center gap-4 py-4 text-blue-900 font-bold border-l-4 border-blue-900 pl-8 bg-white">

                    <span class="material-symbols-outlined">
                        person
                    </span>

                    <span class="text-sm uppercase tracking-widest">
                        Thông tin tài khoản
                    </span>

                </a>



                {{-- đổi mật khẩu --}}
                <a href="{{ url('/password/change') }}"
                    class="flex items-center gap-4 py-4 text-gray-500 pl-8 hover:bg-gray-100 transition">

                    <span class="material-symbols-outlined">
                        shield
                    </span>

                    <span class="text-sm uppercase tracking-widest">
                        Đổi mật khẩu
                    </span>

                </a>


                {{-- địa chỉ --}}
                <a href="{{ route('addresses.index') }}"
                    class="flex items-center gap-4 py-4 text-gray-500 pl-8 hover:bg-gray-100 transition">

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

                        <button class="flex items-center gap-4 py-4 text-red-500 pl-8 hover:bg-red-50 transition w-full">

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
                        Thông tin tài khoản
                    </h1>

                    <p class="text-gray-500 mt-2">
                        Cập nhật thông tin và ảnh đại diện của bạn
                    </p>

                </div>


                {{-- CARD --}}
                <div class="bg-white rounded-2xl shadow-sm p-8">

                    <div class="flex flex-col lg:flex-row gap-12 items-start">



                        {{-- ===================================================== --}}
                        {{-- AVATAR --}}
                        {{-- ===================================================== --}}
                        <div class="flex flex-col items-center gap-4 w-full lg:w-1/3">

                            <div class="relative group">

                                {{-- có avatar --}}
                                @if(Auth::user()->avatar_url)

                                    <img id="preview-avatar" src="{{ asset(Auth::user()->avatar_url) }}"
                                        class="w-40 h-40 rounded-full object-cover border-4 border-gray-200">

                                    <span id="preview-avatar-icon"
                                        class="hidden material-symbols-outlined text-[160px] text-gray-400">
                                        account_circle
                                    </span>

                                @else

                                    {{-- icon --}}
                                    <span id="preview-avatar-icon" class="material-symbols-outlined text-[160px] text-gray-400">
                                        account_circle
                                    </span>

                                    {{-- image --}}
                                    <img id="preview-avatar"
                                        class="hidden w-40 h-40 rounded-full object-cover border-4 border-gray-200">

                                @endif



                                {{-- edit --}}
                                <label for="avatar"
                                    class="absolute bottom-2 right-2 bg-blue-900 text-white p-2 rounded-full shadow-lg cursor-pointer hover:scale-105 transition">

                                    <span class="material-symbols-outlined text-sm">
                                        edit
                                    </span>

                                </label>

                            </div>



                            {{-- tên --}}
                            <div class="text-center">

                                <p class="text-blue-900 font-bold text-lg">

                                    {{ Auth::user()->full_name }}

                                </p>

                                <p class="text-gray-500 text-sm">

                                    Thành viên B-Tris

                                </p>

                            </div>
                            
                            {{-- NOTE --}}
                            <div class="text-sm text-gray-500">

                                Chỉ hỗ trợ JPG, JPEG, PNG, WEBP. Tối đa 2MB.

                            </div>
                        </div>






                        {{-- ===================================================== --}}
                        {{-- FORM --}}
                        {{-- ===================================================== --}}
                        <div class="flex-1 w-full">

                            <form id="profileForm" action="{{ route('profile.update') }}" method="POST"
                                enctype="multipart/form-data" class="space-y-6">

                                @csrf



                                {{-- FILE --}}
                                <input type="file" name="avatar" id="avatar" accept=".jpg,.jpeg,.png,.webp" class="hidden"
                                    onchange="previewImage(event)">



                                {{-- FULL NAME --}}
                                <div>

                                    <label class="block text-xs font-bold uppercase tracking-widest text-blue-900 mb-2">
                                        Họ và tên
                                    </label>

                                    <input type="text" name="full_name" id="full_name" maxlength="50"
                                        value="{{ old('full_name', Auth::user()->full_name) }}" class="w-full border-0 border-b-2 rounded-md transition border-gray-200
                                            focus:border-blue-900 focus:ring-0 py-3 px-0">

                                    {{-- ERROR --}}
                                    <p id="full_name_error" class="text-red-500 text-sm mt-2 hidden">
                                    </p>

                                </div>




                                {{-- PHONE --}}
                                <div>

                                    <label class="block text-xs font-bold uppercase tracking-widest text-blue-900 mb-2">
                                        Số điện thoại
                                    </label>

                                    <input type="text" name="phone" id="phone" maxlength="10"
                                        value="{{ old('phone', Auth::user()->phone) }}" class="w-full border-0 border-b-2 rounded-md transition border-gray-200
                                            focus:border-blue-900 focus:ring-0 py-3 px-0">

                                    {{-- ERROR --}}
                                    <p id="phone_error" class="text-red-500 text-sm mt-2 hidden">
                                    </p>

                                </div>





                                {{-- EMAIL --}}
                                <div>

                                    <label class="block text-xs font-bold uppercase tracking-widest text-blue-900 mb-2">

                                        Email

                                    </label>

                                    <div class="flex items-center gap-3">

                                        <input type="email" value="{{ Auth::user()->email }}" readonly disabled
                                            class="w-full border-0 border-b-2 border-gray-200 bg-gray-100 text-gray-500 cursor-not-allowed py-3 px-0">

                                        @if(Auth::user()->is_verified)

                                            <span
                                                class="bg-blue-100 text-blue-900 text-[10px] px-2 py-1 rounded-full uppercase font-bold whitespace-nowrap">

                                                Đã xác thực

                                            </span>

                                        @endif

                                    </div>

                                </div>





                                {{-- ROLE --}}
                                <div>

                                    <label class="block text-xs font-bold uppercase tracking-widest text-blue-900 mb-2">

                                        Vai trò

                                    </label>

                                    <input type="text" value="{{ ucfirst(Auth::user()->role) }}" disabled
                                        class="w-full border-0 border-b-2 border-gray-200 bg-gray-50 py-3 px-0">

                                </div>











                                {{-- BUTTON --}}
                                <div class="pt-8">

                                    <button type="button" id="saveButton" onclick="openSaveModal()"
                                        class="bg-gradient-to-r from-blue-900 to-blue-700 text-white py-3 px-12 rounded-xl font-bold uppercase tracking-widest hover:brightness-110 transition">

                                        <span id="saveButtonText">
                                            Lưu thay đổi
                                        </span>

                                    </button>

                                </div>

                            </form>

                        </div>

                    </div>

                </div>






                {{-- SECURITY --}}
                <div class="bg-blue-50 border border-blue-100 rounded-2xl p-6 flex items-center gap-6 mt-8">

                    <div class="bg-white p-3 rounded-xl shadow-sm">

                        <span class="material-symbols-outlined text-blue-900 text-3xl">
                            verified_user
                        </span>

                    </div>

                    <div>

                        <h4 class="text-blue-900 font-bold text-sm uppercase tracking-wider">
                            Mức độ bảo mật: Cao
                        </h4>

                        <p class="text-gray-500 text-xs mt-1">
                            Tài khoản của bạn đang được bảo vệ an toàn.
                        </p>

                    </div>

                </div>

            </div>

        </section>

    </div>





    {{-- ===================================================== --}}
    {{-- MODAL SAVE --}}
    {{-- ===================================================== --}}
    <div id="saveModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">

        <div class="bg-white rounded-2xl p-6 w-[90%] max-w-md">

            <h3 class="text-xl font-bold text-blue-900 mb-3">
                Xác nhận thay đổi
            </h3>

            <p class="text-gray-500 mb-6">
                Bạn có chắc muốn thay đổi thông tin tài khoản không?
            </p>

            <div class="flex justify-end gap-3">

                <button onclick="closeSaveModal()" class="px-5 py-2 rounded-xl border">

                    Không

                </button>

                <button onclick="submitProfileForm()" class="bg-blue-900 text-white px-5 py-2 rounded-xl">

                    Có

                </button>

            </div>

        </div>

    </div>
    {{-- ===================================================== --}}
    {{-- MODAL NO CHANGE --}}
    {{-- ===================================================== --}}
    <div id="noChangeModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">

        <div class="bg-white rounded-2xl p-6 w-[90%] max-w-md">

            {{-- TITLE --}}
            <h3 class="text-xl font-bold text-yellow-500 mb-3">

                Thông báo

            </h3>

            {{-- CONTENT --}}
            <p class="text-gray-500 mb-6">

                Bạn chưa thay đổi dữ liệu nào.

            </p>

            {{-- BUTTON --}}
            <div class="flex justify-end">

                <button onclick="closeNoChangeModal()" class="bg-yellow-500 text-white px-5 py-2 rounded-xl">

                    OK

                </button>

            </div>

        </div>

    </div>

    {{-- ===================================================== --}}
    {{-- MODAL SUCCESS --}}
    {{-- ===================================================== --}}
    @if(session('success'))

        <div id="successModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">

            <div class="bg-white rounded-3xl p-8 w-[90%] max-w-md text-center animate-[fadeIn_.25s_ease]">

                {{-- ICON --}}
                <div class="w-20 h-20 rounded-full bg-green-100 mx-auto flex items-center justify-center mb-5">

                    <span class="material-symbols-outlined text-green-600 text-5xl">

                        check_circle

                    </span>

                </div>

                {{-- TITLE --}}
                <h3 class="text-2xl font-bold text-green-600 mb-3">

                    Thành công

                </h3>

                {{-- MESSAGE --}}
                <p class="text-gray-500 leading-relaxed">

                    {{ session('success') }}

                </p>

                {{-- BUTTON --}}
                <button onclick="closeSuccessModal()"
                    class="mt-8 bg-green-600 hover:bg-green-700 transition text-white px-8 py-3 rounded-2xl font-bold">

                    OK

                </button>

            </div>

        </div>

    @endif


    <script>

        // =====================================================
        // BIẾN TOÀN CỤC
        // =====================================================

        // kiểm tra form có thay đổi hay chưa
        let isChanged = false;



        // =====================================================
        // INPUT
        // =====================================================

        // input họ tên
        const fullNameInput =
            document.getElementById('full_name');



        // input số điện thoại
        const phoneInput =
            document.getElementById('phone');



        // input avatar
        const avatarInput =
            document.getElementById('avatar');



        // =====================================================
        // DỮ LIỆU GỐC
        // =====================================================

        // họ tên ban đầu
        const originalFullName =
            fullNameInput.value.trim();



        // số điện thoại ban đầu
        const originalPhone =
            phoneInput.value.trim();



        // avatar ban đầu
        const originalAvatar =
            avatarInput.value;





        // =====================================================
        // DOM LOADED
        // =====================================================
        document.addEventListener('DOMContentLoaded', () => {

            // form
            const form =
                document.getElementById('profileForm');



            // =================================================
            // THEO DÕI INPUT
            // =================================================
            form.querySelectorAll('input').forEach(input => {

                // nhập text
                input.addEventListener('input', () => {

                    checkFormChanged();

                });



                // change
                input.addEventListener('change', () => {

                    checkFormChanged();

                });

            });



            // =================================================
            // VALIDATE REALTIME FULL NAME
            // =================================================
            fullNameInput.addEventListener(
                'blur',
                validateFullName
            );



            fullNameInput.addEventListener('input', () => {

                checkFormChanged();



                if (
                    fullNameInput.value.trim() !== ''
                ) {

                    validateFullName();

                }

            });



            // =================================================
            // VALIDATE REALTIME PHONE
            // =================================================
            phoneInput.addEventListener(
                'blur',
                validatePhone
            );



            phoneInput.addEventListener('input', () => {

                checkFormChanged();

                validatePhone();

            });

        });





        // =====================================================
        // KIỂM TRA FORM CÓ THAY ĐỔI KHÔNG
        // =====================================================
        function checkFormChanged() {

            // dữ liệu hiện tại
            const currentFullName =
                fullNameInput.value.trim();



            const currentPhone =
                phoneInput.value.trim();



            const currentAvatar =
                avatarInput.value;



            // =================================================
            // SO SÁNH
            // =================================================
            isChanged =

                currentFullName !== originalFullName ||

                currentPhone !== originalPhone ||

                currentAvatar !== originalAvatar;

        }





        // =====================================================
        // HIỂN THỊ LỖI INPUT
        // =====================================================
        function showInputError(input, message) {

            input.classList.remove(
                'border-gray-200'
            );



            input.classList.add(
                'border-red-500',
                'ring-2',
                'ring-red-200'
            );



            const error =
                document.getElementById(
                    input.id + '_error'
                );



            error.innerText = message;

            error.classList.remove('hidden');

        }





        // =====================================================
        // XOÁ LỖI INPUT
        // =====================================================
        function removeInputError(input) {

            input.classList.remove(
                'border-red-500',
                'ring-2',
                'ring-red-200'
            );



            input.classList.add(
                'border-gray-200'
            );



            const error =
                document.getElementById(
                    input.id + '_error'
                );



            error.innerText = '';

            error.classList.add('hidden');

        }





        // =====================================================
        // VALIDATE FULL NAME
        // =====================================================
        function validateFullName() {

            const value =
                fullNameInput.value.trim();



            // rỗng
            if (value === '') {

                showInputError(
                    fullNameInput,
                    'Vui lòng nhập họ và tên.'
                );

                return false;

            }



            // tối thiểu 2 ký tự
            if (value.length < 2) {

                showInputError(
                    fullNameInput,
                    'Họ tên phải từ 2 ký tự.'
                );

                return false;

            }



            removeInputError(
                fullNameInput
            );



            return true;

        }





        // =====================================================
        // VALIDATE PHONE
        // =====================================================
        function validatePhone() {

            let value =
                phoneInput.value;



            // chỉ cho nhập số
            value =
                value.replace(/[^0-9]/g, '');



            phoneInput.value = value;



            // rỗng
            if (value.trim() === '') {

                showInputError(
                    phoneInput,
                    'Vui lòng nhập số điện thoại.'
                );

                return false;

            }



            // đủ 10 số
            if (value.length !== 10) {

                showInputError(
                    phoneInput,
                    'Số điện thoại phải đủ 10 số.'
                );

                return false;

            }



            // bắt đầu bằng 0
            if (!value.startsWith('0')) {

                showInputError(
                    phoneInput,
                    'Số điện thoại phải bắt đầu bằng số 0.'
                );

                return false;

            }



            removeInputError(
                phoneInput
            );



            return true;

        }





        // =====================================================
        // RESET AVATAR
        // =====================================================
        function resetAvatar() {

            const image =
                document.getElementById(
                    'preview-avatar'
                );



            const icon =
                document.getElementById(
                    'preview-avatar-icon'
                );



            // có avatar
            if (
                "{{ Auth::user()->avatar_url }}"
            ) {

                image.src =
                    "{{ Auth::user()->avatar_url
        ? asset(Auth::user()->avatar_url)
        : '' }}";



                image.classList.remove(
                    'hidden'
                );



                if (icon) {

                    icon.style.display = 'none';

                }

            }

            // không có avatar
            else {

                image.src = '';

                image.classList.add(
                    'hidden'
                );



                if (icon) {

                    icon.style.display = 'block';

                }

            }

        }





        // =====================================================
        // XOÁ LỖI AVATAR
        // =====================================================
        function removeAvatarError() {

            const old =
                document.getElementById(
                    'avatar-error'
                );



            if (old) {

                old.remove();

            }



            const image =
                document.getElementById(
                    'preview-avatar'
                );



            image.classList.remove(
                'border-red-500',
                'ring-4',
                'ring-red-200'
            );



            image.classList.add(
                'border-gray-200'
            );

        }





        // =====================================================
        // HIỂN THỊ LỖI AVATAR
        // =====================================================
        function showAvatarError(message) {

            removeAvatarError();



            const image =
                document.getElementById(
                    'preview-avatar'
                );



            image.classList.remove(
                'border-gray-200'
            );



            image.classList.add(
                'border-red-500',
                'ring-4',
                'ring-red-200'
            );



            const avatarContainer =
                image.closest('.flex-col');



            const error =
                document.createElement('p');



            error.id = 'avatar-error';



            error.className =
                'text-red-500 text-sm text-center font-medium mt-2';



            error.innerText = message;



            avatarContainer.appendChild(error);

        }





        // =====================================================
        // PREVIEW IMAGE
        // =====================================================
        function previewImage(event) {

            checkFormChanged();



            const file =
                event.target.files[0];



            const image =
                document.getElementById(
                    'preview-avatar'
                );



            const icon =
                document.getElementById(
                    'preview-avatar-icon'
                );



            removeAvatarError();



            // không chọn file
            if (!file) {

                resetAvatar();

                return;

            }



            // file 0KB
            if (file.size <= 0) {

                showAvatarError(
                    'Dung lượng ảnh phải lớn hơn 0KB.'
                );

                resetAvatar();

                return;

            }



            // mime type
            const allowed = [
                'image/jpeg',
                'image/jpg',
                'image/png',
                'image/webp'
            ];



            if (!allowed.includes(file.type)) {

                showAvatarError(
                    'Chỉ hỗ trợ file JPG, JPEG, PNG, WEBP.'
                );

                resetAvatar();

                return;

            }



            // max size
            if (file.size > 2048 * 1024) {

                showAvatarError(
                    'Dung lượng ảnh vượt quá 2MB.'
                );

                resetAvatar();

                return;

            }



            // check ảnh thật
            const testImage =
                new Image();



            // hợp lệ
            testImage.onload = function () {

                image.src =
                    URL.createObjectURL(file);

                image.classList.remove(
                    'hidden'
                );



                if (icon) {

                    icon.style.display = 'none';

                }

            };



            // lỗi
            testImage.onerror = function () {

                showAvatarError(
                    'File tải lên không phải hình ảnh hợp lệ.'
                );

                resetAvatar();

            };



            testImage.src =
                URL.createObjectURL(file);

        }





        // =====================================================
        // MỞ MODAL SAVE
        // =====================================================
        function openSaveModal() {

            // check thay đổi
            checkFormChanged();



            // không thay đổi
            if (!isChanged) {

                openNoChangeModal();

                return;

            }



            // validate
            const fullNameValid =
                validateFullName();



            const phoneValid =
                validatePhone();



            // lỗi full name
            if (!fullNameValid) {

                fullNameInput.focus();

                return;

            }



            // lỗi phone
            if (!phoneValid) {

                phoneInput.focus();

                return;

            }



            // lỗi avatar
            if (
                document.getElementById(
                    'avatar-error'
                )
            ) {

                document
                    .getElementById(
                        'preview-avatar'
                    )
                    .scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });

                return;

            }



            // mở modal
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





        // =====================================================
        // ĐÓNG MODAL SAVE
        // =====================================================
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
        // MỞ MODAL NO CHANGE
        // =====================================================
        function openNoChangeModal() {

            document
                .getElementById(
                    'noChangeModal'
                )
                .classList.remove(
                    'hidden'
                );



            document
                .getElementById(
                    'noChangeModal'
                )
                .classList.add(
                    'flex'
                );

        }





        // =====================================================
        // ĐÓNG MODAL NO CHANGE
        // =====================================================
        function closeNoChangeModal() {

            document
                .getElementById(
                    'noChangeModal'
                )
                .classList.add(
                    'hidden'
                );



            document
                .getElementById(
                    'noChangeModal'
                )
                .classList.remove(
                    'flex'
                );

        }





        // =====================================================
        // SUBMIT FORM
        // =====================================================
        function submitProfileForm() {

            const avatarError =
                document.getElementById(
                    'avatar-error'
                );



            // có lỗi avatar
            if (avatarError) {

                closeSaveModal();

                document
                    .getElementById(
                        'preview-avatar'
                    )
                    .scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });

                return;

            }



            // loading
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



            // submit
            setTimeout(() => {

                isChanged = false;

                document
                    .getElementById(
                        'profileForm'
                    )
                    .submit();

            }, 1000);

        }





        // =====================================================
        // ĐÓNG MODAL SUCCESS
        // =====================================================
        function closeSuccessModal() {

            const modal =
                document.getElementById(
                    'successModal'
                );



            if (modal) {

                modal.remove();

            }

        }

    </script>
@endsection