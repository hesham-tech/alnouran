<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class AuthController extends Controller
{
    use AuthenticatesUsers;
    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        // $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {

        // تسجيل الدخول
        // $credentials = $request->only('email', 'password');
        // $credentials = $request->only('email', 'password');
        // if (Auth::attempt($credentials)) {

        $credentialsEmail = [
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ];
        $credentialsPhone = [
            'phone' => $request->input('email'),
            'password' => $request->input('password'),
        ];
        if (Auth::attempt($credentialsEmail) || Auth::attempt($credentialsPhone)) {
            // إنشاء رمز مميز
            /** @var User */
            $user = Auth::User();
            $token = $user->createToken('API Token')->plainTextToken;
            // إرسال رد
            return response()->json([
                'user' => $user,
                'token' => $token,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'error' => 'Error in email or password.'
            ], 401);
        }
    }
    // public function logout(Request $request)
    // {
       
    //     $request->user()->currentAccessToken()->delete();
    //     return response()->json('Logged out successfully', 200);
    // }

    
// public function logout(Request $request)
// {
//     // الحصول على المستخدم الحالي
//     $user = $request->user();

//     // حذف جميع التوكنات للمستخدم الحالي
//     $user->tokens()->delete();

//     return response()->json(['message' => 'Successfully logged out'], 200);
// }
public function logout(Request $request)
{
    $user = $request->user();
    
    if ($user) {
        // If the user was authenticated via a Sanctum token
        $token = $user->currentAccessToken();
        if ($token && method_exists($token, 'delete')) {
            $token->delete();
        }
        
        // Also logout from the session-based guard if active
        Auth::guard('web')->logout();
    }

    return response()->json(['message' => 'Successfully logged out'], 200);
}
}