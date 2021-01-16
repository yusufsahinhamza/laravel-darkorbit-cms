<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('authenticated')->only(['selectCompany', 'changePilotName', 'changePassword']);
    }

    public function changePilotName(Request $request)
    {
        if (!$request->exists('pilotName')) {
            return response(['status' => false, 'message' => '"pilotName" parameter does not exists.']);
        }

        $sanitized = (object)\Sanitizer::make($request->all(),
        [
            'pilotName' => ['trim']
        ])->sanitize();

        \DB::transaction(function() use($sanitized) {
            $user = \DB::table('users')->where([['pilot_name', '=', $sanitized->pilotName]])->first();

            if ($user !== null) {
                exit(json_encode(['status' => false, 'message' => 'A user is already exists with this pilot name.']));
            }

            \DB::table('users')->where([['id', '=', \Auth::id()]])->update([
                'pilot_name' => $sanitized->pilotName
            ]);
        }, 5);

        return response(['status' => true]);
    }

    public function changePassword(Request $request)
    {
        if (!$request->exists('oldPassword')) {
            return response(['status' => false, 'message' => '"oldPassword" parameter does not exists.']);
        }

        if (!$request->exists('newPassword')) {
            return response(['status' => false, 'message' => '"newPassword" parameter does not exists.']);
        }

        $sanitized = (object)\Sanitizer::make($request->all(),
        [
            'oldPassword' => ['trim'],
            'newPassword' => ['trim']
        ])->sanitize();

        \DB::transaction(function() use($sanitized) {
            $user = \DB::table('users')->where([['id', '=', \Auth::id()]])->first();

            if (!\Hash::check($sanitized->oldPassword, $user->password)) {
                exit(json_encode(['status' => false, 'message' => 'Old password doesnt match.']));
            }

            \DB::table('users')->where([['id', '=', \Auth::id()]])->update([
                'password' => \Hash::make($sanitized->newPassword)
            ]);
        }, 5);

        return response(['status' => true]);
    }

    public function selectCompany(Request $request)
    {
        if (!$request->exists('company')) {
            return response(['status' => false, 'message' => '"company" parameter does not exists.']);
        }

        if (!is_string($request->company) || !in_array($request->company, ['MMO', 'EIC', 'VRU'], true)) {
            return response(['status' => false, 'message' => '"company" parameter is invalid.']);
        }

        $sanitized = (object)\Sanitizer::make($request->all(),
        [
            'company' => ['trim', 'strip_tags', 'escape']
        ])->sanitize();

        \DB::transaction(function() use($sanitized) {
            $user_id = \Auth::id();

            $user = \DB::table('users')->where([['id', '=', $user_id]])->lockForUpdate()->first();

            if ($user->faction_id !== 0) {
                exit(json_encode(['status' => false, 'message' => 'You are already registered to a company.']));
            }

            \DB::table('users')->where([['id', '=', $user_id]])->update([
                'faction_id' => Controller::getCompanyIdByName($sanitized->company)
            ]);
        }, 5);

        return response(['status' => true]);
    }
}
