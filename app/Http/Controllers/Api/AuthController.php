<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\UserProfile;

class AuthController extends Controller {
    public function checkLogin(Request $request) {
        $login = $request->query('login');
        if (!$login) return response()->json(['available' => false, 'message' => 'Логин не указан'], 422);
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $login)) {
            return response()->json(['available' => false, 'message' => 'Только символы латиницы'], 200);
        }
        $exists = User::where('login', $login)->exists();
        return response()->json(['available' => !$exists, 'message' => $exists ? 'Логин уже занят' : 'Логин свободен']);
    }

    public function register(Request $request) {
        $request->validate([
            'first_name' => ['required', 'string', 'regex:/^[a-zA-Zа-яА-ЯёЁ\s-]+$/u'],
            'last_name'  => ['required', 'string', 'regex:/^[a-zA-Zа-яА-ЯёЁ\s-]+$/u'],
            'login'      => ['required', 'string', 'regex:/^[a-zA-Z0-9_-]+$/', 'unique:users,login'],
            'email'      => ['required', 'email', 'unique:users,email'],
            'password'   => ['required', 'string', 'min:6', 'confirmed'],
            'terms'      => ['accepted'],
            'categories' => ['nullable', 'array'],
            'avatar'     => ['nullable', 'image', 'max:2048']
        ]);

        return DB::transaction(function () use ($request) {
            $user = User::create([
                'login' => $request->login,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'user',
            ]);

            $avatarUrl = null;
            if ($request->hasFile('avatar')) {
                $path = $request->file('avatar')->store('avatars', 'public');
                $avatarUrl = '/storage/' . $path;
            }

            UserProfile::create([
                'user_id' => $user->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'avatar_url' => $avatarUrl,
            ]);

            if ($request->filled('categories')) {
                $user->categories()->sync($request->categories);
            }

            Auth::login($user);
            return response()->json(['success' => true, 'user' => $user->load('profile', 'categories')], 201);
        });
    }

    public function login(Request $request) {
        $cred = $request->validate(['login' => 'required', 'password' => 'required']);
        if (Auth::attempt($cred)) {
            $request->session()->regenerate();
            $user = Auth::user()->load('profile');
            return response()->json(['success' => true, 'user' => $user, 'redirect' => $user->role === 'admin' ? '/admin' : '/feed']);
        }
        return response()->json(['success' => false, 'message' => 'Неверная пара логин или пароль'], 401);
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(['success' => true]);
    }
}