<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class AuthController extends Controller
{
    /**
     * Login user dengan email dan password.
     */
    public function login(Request $request)
    {
        // Validasi input
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        // Cek jika email atau password kosong (sebenarnya sudah ditangani oleh validator, tapi disiapkan untuk validasi manual juga)
        if (empty($data['email']) || empty($data['password'])) {
            return response()->json([
                'status_code' => 400,
                'message' => 'Email dan password harus diisi',
            ], 400);
        }

        try {
            // Attempt login menggunakan guard 'api'
            if (!$token = Auth::guard('api')->attempt($data)) {
                return response()->json([
                    'status_code' => 401,
                    'message' => 'Email atau password salah',
                ], 401);
            }

            $user = Auth::guard('api')->user();

            return response()->json([
                'status_code' => 200,
                'message' => 'Login berhasil',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'is_admin' => $user->is_admin,
                        'token' => $token,
                    ]
                ]
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Terjadi kesalahan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Logout user yang sedang login.
     */
    public function logout()
    {
        Auth::guard('api')->logout();

        return response()->json([
            'message' => 'Logout berhasil',
        ], 200);
    }
}