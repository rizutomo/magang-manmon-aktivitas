<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Admin;
use App\Models\Supervisor;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Notifications\ResetPasswordNotification;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'email|required',
            'password' => 'required',
            'role' => 'required'
        ]);

        $emailExistsInAdmins = Admin::where('email', $request->email)->exists();
        $emailExistsInSupervisors = Supervisor::where('email', $request->email)->exists();
        $emailExistsInUsers = User::where('email', $request->email)->exists();

        if ($emailExistsInAdmins || $emailExistsInSupervisors || $emailExistsInUsers) {
            return response([
                'message' => 'Email telah digunakan'
            ], 422);
        }

        if ($request->role == 'admin') {
            $newUser = new Admin();
            $newUser->name = $request->name;
            $newUser->email = $request->email;
            $newUser->password = Hash::make($request->password);
            $newUser->save();
        } else if ($request->role == 'supervisor') {
            $newUser = new Supervisor();
            $newUser->name = $request->name;
            $newUser->email = $request->email;
            $newUser->password = Hash::make($request->password);
            $newUser->save();
        } else if ($request->role == 'user') {
            $newUser = new User();
            $newUser->name = $request->name;
            $newUser->email = $request->email;
            $newUser->password = Hash::make($request->password);
            $newUser->save();
        }

        return response([
            'message' => 'Berhasil menambahkan akun',
            'user' => $newUser,
        ], 200);
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);
        $user = User::whereEmail($request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'message' => 'Invalid credentials'
            ], 422);
        }

        $token = $user->createToken('APIToken')->plainTextToken;

        return response([
            'user' => $user,
            'token' => $token
        ], 200);
    }

    public function logout(Request $request)
    {
        $user = auth()->user();
        $user->currentAccessToken()->delete();

        return response([
            "message" => "Logout berhasil"
        ], 200);

    }

    public function getAllUser(Request $request)
    {
        $users = User::with('occupation.sector')->get(); 
        return response([
            "message" => "Berhasil ambil data user",
            "users" => $users
        ], 200);
        
    }
    public function getUserCount()
    {
        $count = User::count(); 
        return response()->json(['count' => $count]);
    }

    public function sendResetCode(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Cari user, admin, atau supervisor berdasarkan email
        $admin = Admin::where('email', $request->email)->first();
        $supervisor = Supervisor::where('email', $request->email)->first();
        $user = User::where('email', $request->email)->first();

        // Jika tidak ditemukan, kembalikan respons error
        if (!$user && !$admin && !$supervisor) {
            return response()->json(['message' => 'Email not found'], 404);
        }

        // Generate kode reset password 8 karakter
        $resetCode = Str::random(8);

        // Simpan kode reset ke dalam tabel password_reset_tokens
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => bcrypt($resetCode),
                'created_at' => now()
            ]
        );
        $notifiable = $user ?? $admin ?? $supervisor;
        // sendPasswordResetNotification($resetCode);

        return response()->json(['message' => 'Reset code sent']);
    }


    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'reset_code' => 'required|string',
            'password' => 'required|string|confirmed|min:8',
        ]);

        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetRecord) {
            return response()->json(['message' => 'Invalid email or reset code'], 404);
        }

        // Check if the provided reset code matches the stored hashed token
        if (!\Hash::check($request->reset_code, $resetRecord->token)) {
            return response()->json(['message' => 'Invalid reset code'], 400);
        }

        // Update the user's password
        $user = User::where('email', $request->email)->first();
        $user->update(['password' => bcrypt($request->password)]);

        // Delete the reset record
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Password reset successfully']);
    }
    public function validateToken(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Token is valid.',
            'user' => $request->user()
        ], 200);
    }
}
