<?php

namespace App\Http\Controllers;

use App\Models\ShippingAddress;
use Illuminate\Support\Facades\Auth;

class ShippingAddressController extends Controller
{

    // =====================================================
    // DANH SÁCH ĐỊA CHỈ
    // =====================================================
    public function index()
    {

        // =================================================
        // LẤY ĐỊA CHỈ USER HIỆN TẠI
        // =================================================
        $addresses =
            ShippingAddress::where(
                'user_id',
                Auth::id()
            )
            ->orderByDesc('is_default')
            ->get();



        // =================================================
        // TRẢ VIEW
        // =================================================
       return view(
    'auth.address.index',
    compact('addresses')
);

    }

}