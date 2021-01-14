<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('unauthenticated')->only(['login', 'register']);

        $this->middleware('authenticated')->only(['logout', 'getUser']);
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
            return response(['status' => false, 'message' => '"username" parameter is invalid.']);
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
            'email'  => ['trim', 'strip_tags', 'escape', 'lowercase'],
            'password' => ['trim']
        ])->sanitize();

        \DB::transaction(function() use($sanitized) {
            $user = \DB::table('users')->where([['email', '=', $sanitized->email]])->orWhere([['username', '=', $sanitized->username]])->first();

            if ($user !== null) {
                exit(json_encode(['status' => false, 'message' => 'A user is already exists by this username or email address.']));
            }

            $insert = [
                'username' => $sanitized->username,
                'email' => $sanitized->email,
                'password' => \Hash::make($sanitized->password),
                'created_at' => Controller::getTimestamp()
            ];

            \DB::table('users')->insert($insert);

            if (!\Auth::attempt(['username' => $sanitized->username, 'password' => $sanitized->password])) {
                exit(json_encode(['status' => false, 'message' => 'An error occurred while logging in.']));
            }

            session()->regenerate();
        }, 5);

        return response(['status' => true]);
    }

    public function login(Request $request)
    {
        if (!$request->exists('username')) {
            return response(['status' => false, 'message' => '"username" parameter does not exists.']);
        }

        if (!is_string($request->username) || !preg_match('/^[A-Za-z0-9]{4,20}$/', $request->username)) {
            return response(['status' => false, 'message' => '"username" parameter is invalid.']);
        }

        if (!$request->exists('password')) {
            return response(['status' => false, 'message' => '"password" parameter does not exists.']);
        }

        $sanitized = (object)\Sanitizer::make($request->all(),
        [
            'username' => ['trim', 'strip_tags', 'escape'],
            'password' => ['trim']
        ])->sanitize();

        \DB::transaction(function() use($sanitized) {
            $user = \DB::table('users')->where([['username', '=', $sanitized->username]])->first();

            if ($user === null) {
                exit(json_encode(['status' => false, 'message' => 'A user with this username does not exists.']));
            }

            if (!\Hash::check($sanitized->password, $user->password)) {
                exit(json_encode(['status' => false, 'message' => 'This password doesnt match.']));
            }

            if (!\Auth::attempt(['username' => $user->username, 'password' => $sanitized->password])) {
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
        $user = null;

        $get_user = \DB::table('users')->where([['id', '=', \Auth::id()]])->first();

        if ($get_user === null) {
            return response(['status' => false, 'message' => 'Current user not found.']);
        }

        $user['u'] = $get_user->username;
        $user['f'] = Controller::getCompanyNameById($get_user->faction_id);

        return response(['status' => true, 'data' => $user]);
    }
}
