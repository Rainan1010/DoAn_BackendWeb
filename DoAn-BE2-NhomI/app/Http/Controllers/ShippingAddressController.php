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

        // địa chỉ mặc định lên đầu
        ->orderBy(
            'is_default',
            'desc'
        )

        // địa chỉ mới nhất
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
    // NORMALIZE ADDRESS
    // =====================================================
    private function normalizeAddress($address)
    {

        // lowercase
        $address =
            mb_strtolower(
                $address,
                'UTF-8'
            );



        // bỏ dấu tiếng việt
        $address =
            str_replace(

                [
                    'à','á','ạ','ả','ã',
                    'â','ầ','ấ','ậ','ẩ','ẫ',
                    'ă','ằ','ắ','ặ','ẳ','ẵ',

                    'è','é','ẹ','ẻ','ẽ',
                    'ê','ề','ế','ệ','ể','ễ',

                    'ì','í','ị','ỉ','ĩ',

                    'ò','ó','ọ','ỏ','õ',
                    'ô','ồ','ố','ộ','ổ','ỗ',
                    'ơ','ờ','ớ','ợ','ở','ỡ',

                    'ù','ú','ụ','ủ','ũ',
                    'ư','ừ','ứ','ự','ử','ữ',

                    'ỳ','ý','ỵ','ỷ','ỹ',

                    'đ'
                ],

                [
                    'a','a','a','a','a',
                    'a','a','a','a','a','a',
                    'a','a','a','a','a','a',

                    'e','e','e','e','e',
                    'e','e','e','e','e','e',

                    'i','i','i','i','i',

                    'o','o','o','o','o',
                    'o','o','o','o','o','o',
                    'o','o','o','o','o','o',

                    'u','u','u','u','u',
                    'u','u','u','u','u','u',

                    'y','y','y','y','y',

                    'd'
                ],

                $address

            );



        // bỏ khoảng trắng + ký tự đặc biệt
        $address =
            preg_replace(
                '/[^a-z0-9]/',
                '',
                $address
            );



        return $address;

    }





    // =====================================================
    // STORE
    // =====================================================
    public function store(Request $request)
    {

        // =================================================
        // VALIDATE
        // =================================================
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





        // =================================================
        // CHECK TRÙNG ĐỊA CHỈ
        // =================================================
        $exists = false;



        $addresses =
            ShippingAddress::where(
                'user_id',
                Auth::id()
            )
            ->get();





        foreach ($addresses as $address) {

            // normalize địa chỉ cũ
            $oldAddress =
                $this->normalizeAddress(
                    $address->street_address
                );



            // normalize địa chỉ mới
            $newAddress =
                $this->normalizeAddress(
                    $request->street_address
                );





            if (

                $address->province == $request->province &&

                $address->district == $request->district &&

                $address->ward == $request->ward &&

                $oldAddress == $newAddress

            ) {

                $exists = true;

                break;

            }

        }





        // =================================================
        // ADDRESS EXISTS
        // =================================================
        if ($exists) {

            return back()

                ->withInput()

                ->with(

                    'error',

                    'Địa chỉ đã tồn tại, vui lòng chọn địa chỉ khác.'

                );

        }





        // =================================================
        // FIRST ADDRESS => DEFAULT
        // =================================================
        $isDefault =
            ShippingAddress::where(
                'user_id',
                Auth::id()
            )
            ->count() == 0;





        // =================================================
        // CREATE ADDRESS
        // =================================================
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





        // =================================================
        // SUCCESS
        // =================================================
        return redirect()

            ->route('addresses.create')

            ->with(

                'success',

                'Thêm địa chỉ mới thành công.'

            );

    }

}