<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('unauthenticated')->only(['register']);

        $this->middleware('authenticated')->only(['logout']);
    }

    public function check()
    {
        return response(['status' => \Auth::check()]);
    }

    public function register(Request $request)
    {
        if (!$request->exists('username')) {
            return response(['status' => false, 'message' => '"username" parameter does not exists.']);
        }

        if (!is_string($request->username) || !preg_match('/^[A-Za-z0-9]{4,20}$/', $request->username)) {
            return response(['status' => 'error', 'code' => 231, 'message' => '"username" parameter is invalid.']);
        }

        if (!$request->exists('email')) {
            return response(['status' => false, 'message' => '"email" parameter does not exists.']);
        }

        if (!is_string($request->email) || !filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            return response(['status' => false, 'message' => '"email" parameter is invalid.']);
        }

        if (!$request->exists('password')) {
            return response(['status' => false, 'message' => '"password" parameter does not exists.']);
        }

        $sanitized = (object)\Sanitizer::make($request->all(), 
        [
            'username' => ['trim', 'strip_tags', 'escape'],
            'email'  => ['trim', 'strip_tags', 'escape', 'lowercase']
        ])->sanitize();

        \DB::transaction(function() use($sanitized, $request) {
            $user = \DB::table('users')->where([['email', '=', $sanitized->email]])->orWhere([['username', '=', $sanitized->username]])->first();

            if ($user !== null) {
                exit(json_encode(['status' => false, 'message' => 'An user is already exists by this username or email address.']));
            }

            $insert = [
                'username' => $sanitized->username,
                'email' => $sanitized->email,
                'password' => \Hash::make($request->password),
                'created_at' => Controller::getTimestamp()
            ];

            $user_id = \DB::table('users')->insertGetId($insert);

            if (!\Auth::loginUsingId($user_id)) {
                exit(json_encode(['status' => false, 'message' => 'An error occurred while logging in.']));
            }
            
            session()->regenerate();
        }, 5);

        return response(['status' => true]); 
    }

    public function logout(Request $request)
    {
        \Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response(['status' => true]);
    }

    public function getUser()
    {
        return response(['status' => true, 'data' => ['name' => 'test']]);
    }
}
