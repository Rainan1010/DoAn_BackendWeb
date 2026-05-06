<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Mail\VerifyOTPMail;

class OTPController extends Controller
{
    public function showVerifyForm()
    {
        if (!session()->has('otp_user_id')) {
            return redirect()->route('register')->with('error', 'Vui lòng đăng ký trước.');
        }
        return view('auth.verify_otp');
    }

    public function verifyOTP(Request $request) 
    {
        $request->validate([
            'otp' => 'required|array|size:6',
            'otp.*' => 'required|numeric|digits:1'
        ]); 
        
        $code = implode('', $request->otp);
        $userId = session('otp_user_id');
        
        $otpEntry = DB::table('otp_verifications')
            ->where('user_id', $userId)
            ->where('otp_code', $code)
            ->where('purpose', 'register')
            ->where('used', 0)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otpEntry) {
            return back()->with('error', 'Mã OTP không đúng hoặc đã hết hạn.');
        }

        // Xác thực thành công
        DB::beginTransaction();
        try {
            // Cập nhật User
            User::where('user_id', $userId)->update([
                'is_verified' => 1,
                'email_verified_at' => now()
            ]);

            // Đánh dấu OTP đã dùng
            DB::table('otp_verifications')->where('otp_id', $otpEntry->otp_id)->update(['used' => 1]);

            DB::commit();
            
            // Đăng nhập luôn
            $user = User::where('user_id', $userId)->first();
            Auth::login($user);
            
            session()->forget('otp_user_id');
            return redirect()->route('home')->with('success', 'Xác thực tài khoản thành công!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra, vui lòng thử lại.');
        }
    }

    public function resendOTP(Request $request)
    {
        $userId = session('otp_user_id');
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Phiên đăng ký đã hết hạn.']);
        }

        $user = User::where('user_id', $userId)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy người dùng.']);
        }

        // Xóa OTP cũ chưa dùng
        DB::table('otp_verifications')
            ->where('user_id', $userId)
            ->where('purpose', 'register')
            ->where('used', 0)
            ->delete();

        // Tạo mã mới
        $otpCode = rand(100000, 999999);
        
        DB::table('otp_verifications')->insert([
            'user_id' => $userId,
            'otp_code' => $otpCode,
            'purpose' => 'register',
            'expires_at' => now()->addMinutes(10),
        ]);

        // Gửi Mail
        try {
            Mail::to($user->email)->send(new VerifyOTPMail($otpCode));
            return response()->json(['success' => true, 'message' => 'Đã gửi lại mã xác nhận.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Không thể gửi email.']);
        }
    }
}