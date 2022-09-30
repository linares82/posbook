<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
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
        
        //dd($autenticado);
        if (Auth::attempt($credentials, $request->input('remember'))) {
            // Authentication passed...
            //return redirect()->route('welcome');
            $user=Auth::user();
            $this->authenticated($request, $user);
            return redirect()->intended('/');
        }

        $errors = ["sysMessage"=>"Los datos no coinciden con la información guardada"];
        
        return redirect()->back()
            ->withErrors($errors);
    }

    protected function authenticated(Request $request, $user)
    {
        //dd($user);
        $user->update([
            'last_login' => Carbon::now(),
            'last_login_ip' => $request->getClientIp()
        ]);
    }

    

    public function logout(){
        session()->forget('menu');
        Auth::logout();
        return redirect()->route('login');
    }

    
}
