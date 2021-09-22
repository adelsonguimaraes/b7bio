<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{

    public function __construct () {
        $this->middleware('auth', ['except'=>['login', 'loginAction', 'register', 'registerAction']]);
    }

    public function index () {
        echo "admin";
    }

    public function login(Request $request) {
        return view('admin/login', [
            'error'=> $request->session()->get('error')
        ]);
    }

    public function loginAction (Request $request) {
        $creds = $request->only('email', 'password');
        if (Auth::attempt($creds)) {
            return redirect('/admin');
        }else{
            $request->session()->flash('error', 'E-mail e/ou senha não conferem.');
            return redirect('/admin/login');
        }
    }

    public function register() {
        echo "register..";
    }
}
