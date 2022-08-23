<?php

namespace App\Http\Controllers\Auth;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AutenticationsController extends Controller
{
    public function login(){

        return Inertia::render('Auth/Login');
    }

    public function authenticate(Request $request)
    {
        //dd($request->all());
        $credentials=$request->only('email', 'password');
        $autenticado=Auth::attempt($credentials);
        //dd($autenticado);
        if (Auth::attempt($credentials)) {
            // Authentication passed...
            return redirect()->route('welcome');
        }

        $errors = ["sysMessage"=>"Los datos no coinciden con la información guardada"];
        
        return redirect()->back()
            ->withErrors($errors);
    }

    

    public function logout(){
        session()->forget('menu');
        Auth::logout();
        return redirect()->route('login');
    }
}
