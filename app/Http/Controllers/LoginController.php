<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Controllers\Controller;
use Auth;
use Validator;
use App\User;
use App\Invite_code;
use Illuminate\Support\Collection;

class LoginController extends Controller
{
	public function __construct()
	{
		$this->middleware('guest', ['except' => 'getLogout']);
	}

	public function getLogin()
	{
		return view('user.login');
	}

	public function postLogin(LoginRequest $request)
	{
		$input = $request->all();

		if (Auth::attempt(['user_name' => $input['username'], 'password' => $input['password']], isset($input['rememberme']))) {
			// Authentication passed...
			return redirect()->intended('dashboard');
		}
		else {
			return redirect('login')
				->withInput()
				->with('error', 'Email/password wrong, or account not activated.');
		}
	}

	public function getLogout()
	{
		Auth::logout();
		return redirect('/');
	}

	public function getRegister()
	{
	  return view('user.register');
	}

	public function postRegister(RegisterRequest $request)
	{
		$input = $request->all();

		// is this a valid invite code?
		$invite_code = Invite_code::isvalid($input['invcode'])->first();
		if ($invite_code->isEmpty())
		{
			return redirect('register')
				->withInput()
				->with('error', 'Invalid invite code');
		}

		User::create([
			'user_name' => $input['user_name'],
			'user_email' => $input['user_email'],
			'user_password' => bcrypt($input['password']),
		]);

		// Account created remove use from invite code
		Invite_code::where('code_id', $invite_code->code_id)->decrement('code_uses');

		if (Auth::attempt(['user_name' => $input['user_name'], 'password' => $input['password']])) {
			// Authentication passed...
			return redirect()->intended('dashboard');
		}
	}
}
