<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ShippingAddress;

class ShippingAddressController extends Controller
{

    // =====================================================
    // DANH SÁCH ĐỊA CHỈ
    // =====================================================
    public function index()
    {

        $addresses =
            ShippingAddress::where(
                'user_id',
                Auth::id()
            )

                ->orderBy(
                    'is_default',
                    'desc'
                )

                ->orderBy(
                    'address_id',
                    'desc'
                )

                ->get();



        return view(
            'auth.address.index',
            compact('addresses')
        );

    }





    // =====================================================
    // FORM CREATE
    // =====================================================
    public function create()
    {

        return view(
            'auth.address.create'
        );

    }





    // =====================================================
    // FORM EDIT
    // =====================================================
    public function edit($id)
    {

        $address =
            ShippingAddress::where(
                'user_id',
                Auth::id()
            )

                ->where(
                    'address_id',
                    $id
                )

                ->firstOrFail();



        return view(
            'auth.address.edit',
            compact('address')
        );

    }





    // =====================================================
    // NORMALIZE ADDRESS
    // =====================================================
    private function normalizeAddress($address)
    {

        $address =
            mb_strtolower(
                $address,
                'UTF-8'
            );



        $address =
            str_replace(

                [
                    'à',
                    'á',
                    'ạ',
                    'ả',
                    'ã',
                    'â',
                    'ầ',
                    'ấ',
                    'ậ',
                    'ẩ',
                    'ẫ',
                    'ă',
                    'ằ',
                    'ắ',
                    'ặ',
                    'ẳ',
                    'ẵ',

                    'è',
                    'é',
                    'ẹ',
                    'ẻ',
                    'ẽ',
                    'ê',
                    'ề',
                    'ế',
                    'ệ',
                    'ể',
                    'ễ',

                    'ì',
                    'í',
                    'ị',
                    'ỉ',
                    'ĩ',

                    'ò',
                    'ó',
                    'ọ',
                    'ỏ',
                    'õ',
                    'ô',
                    'ồ',
                    'ố',
                    'ộ',
                    'ổ',
                    'ỗ',
                    'ơ',
                    'ờ',
                    'ớ',
                    'ợ',
                    'ở',
                    'ỡ',

                    'ù',
                    'ú',
                    'ụ',
                    'ủ',
                    'ũ',
                    'ư',
                    'ừ',
                    'ứ',
                    'ự',
                    'ử',
                    'ữ',

                    'ỳ',
                    'ý',
                    'ỵ',
                    'ỷ',
                    'ỹ',

                    'đ'
                ],

                [
                    'a',
                    'a',
                    'a',
                    'a',
                    'a',
                    'a',
                    'a',
                    'a',
                    'a',
                    'a',
                    'a',
                    'a',
                    'a',
                    'a',
                    'a',
                    'a',
                    'a',

                    'e',
                    'e',
                    'e',
                    'e',
                    'e',
                    'e',
                    'e',
                    'e',
                    'e',
                    'e',
                    'e',

                    'i',
                    'i',
                    'i',
                    'i',
                    'i',

                    'o',
                    'o',
                    'o',
                    'o',
                    'o',
                    'o',
                    'o',
                    'o',
                    'o',
                    'o',
                    'o',
                    'o',
                    'o',
                    'o',
                    'o',
                    'o',
                    'o',

                    'u',
                    'u',
                    'u',
                    'u',
                    'u',
                    'u',
                    'u',
                    'u',
                    'u',
                    'u',
                    'u',

                    'y',
                    'y',
                    'y',
                    'y',
                    'y',

                    'd'
                ],

                $address

            );



        return preg_replace(
            '/[^a-z0-9]/',
            '',
            $address
        );

    }





    // =====================================================
    // VALIDATE + CHECK DUPLICATE
    // =====================================================
    private function validateAddress(
        Request $request,
        $ignoreId = null
    ) {

        // validate
        $request->validate([

            'full_name' =>
                'required|max:255',

            'phone' =>
                'required|regex:/^[0-9]{10,11}$/',

            'province' =>
                'required',

            'district' =>
                'required',

            'ward' =>
                'required',

            'street_address' =>
                'required|max:255',

        ], [

            'full_name.required' =>
                'Vui lòng nhập họ tên.',

            'phone.required' =>
                'Vui lòng nhập số điện thoại.',

            'phone.regex' =>
                'Số điện thoại không hợp lệ.',

            'province.required' =>
                'Vui lòng chọn tỉnh/thành phố.',

            'district.required' =>
                'Vui lòng chọn quận/huyện.',

            'ward.required' =>
                'Vui lòng chọn phường/xã.',

            'street_address.required' =>
                'Vui lòng nhập địa chỉ cụ thể.',

        ]);





        // query địa chỉ
        $addresses =
            ShippingAddress::where(
                'user_id',
                Auth::id()
            );



        // update thì bỏ qua chính nó
        if ($ignoreId) {

            $addresses->where(
                'address_id',
                '!=',
                $ignoreId
            );

        }



        $addresses =
            $addresses->get();





        // normalize địa chỉ mới
        $newAddress =
            $this->normalizeAddress(
                $request->street_address
            );





        // check trùng
        foreach ($addresses as $address) {

            $oldAddress =
                $this->normalizeAddress(
                    $address->street_address
                );



            if (

                $address->province ==
                $request->province &&

                $address->district ==
                $request->district &&

                $address->ward ==
                $request->ward &&

                $oldAddress ==
                $newAddress

            ) {

                return back()

                    ->withInput()

                    ->with(
                        'error',
                        'Địa chỉ đã tồn tại.'
                    );

            }

        }



        return null;

    }





    // =====================================================
    // STORE
    // =====================================================
    public function store(Request $request)
    {

        $check =
            $this->validateAddress(
                $request
            );



        if ($check) {

            return $check;

        }





        // địa chỉ đầu tiên => mặc định
        $isDefault =
            ShippingAddress::where(
                'user_id',
                Auth::id()
            )->doesntExist();





        ShippingAddress::create([

            'user_id' =>
                Auth::id(),

            'full_name' =>
                $request->full_name,

            'phone' =>
                $request->phone,

            'province' =>
                $request->province,

            'district' =>
                $request->district,

            'ward' =>
                $request->ward,

            'street_address' =>
                $request->street_address,

            'is_default' =>
                $isDefault,

        ]);




        return redirect()

            ->route('addresses.index')

            ->with(
                'success',
                'Thêm địa chỉ mới thành công.'
            );

    }





    // =====================================================
    // UPDATE
    // =====================================================
    public function update(Request $request, $id)
    {

        $check =
            $this->validateAddress(
                $request,
                $id
            );



        if ($check) {

            return $check;

        }





        $address =
            ShippingAddress::where(
                'user_id',
                Auth::id()
            )

                ->where(
                    'address_id',
                    $id
                )

                ->firstOrFail();





        $address->update([

            'full_name' =>
                $request->full_name,

            'phone' =>
                $request->phone,

            'province' =>
                $request->province,

            'district' =>
                $request->district,

            'ward' =>
                $request->ward,

            'street_address' =>
                $request->street_address,

        ]);





        return redirect()

            ->route('addresses.index')

            ->with(
                'success',
                'Cập nhật địa chỉ thành công.'
            );

    }

}