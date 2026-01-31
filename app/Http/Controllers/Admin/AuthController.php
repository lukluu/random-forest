<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * 1. Tampilkan Halaman Login
     */
    public function index()
    {
        // Jika sudah login, lempar ke dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }
    /**
     * 2. Proses Login (POST)
     */
    public function authenticate(Request $request)
    {
        // A. Validasi Input
        // PERBAIKAN: Ganti rule 'name' (tidak valid) menjadi 'string'
        $credentials = $request->validate([
            'name' => ['required', 'string'],
            'password' => ['required'],
        ]);

        // B. Coba Login 
        // Karena array $credentials isinya ['name' => '...', 'password' => '...']
        // Maka Laravel otomatis akan mencocokkan kolom 'name' di database.
        if (Auth::attempt($credentials, $request->filled('remember'))) {

            // C. Jika Sukses: Regenerasi Session
            $request->session()->regenerate();

            // D. Redirect ke Dashboard
            return redirect()->intended('admin/dashboard');
        }

        // E. Jika Gagal
        return back()->withErrors([
            'name' => 'Nama atau password salah.', // Pesan error disesuaikan
        ])->onlyInput('name');
    }

    /**
     * 3. Proses Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
