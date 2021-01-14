<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('authenticated')->only(['selectCompany']);
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

            if ($user === null) {
                exit(json_encode(['status' => false, 'message' => 'Current user not found.']));
            }

            if ($user->faction_id !== 0) {
                exit(json_encode(['status' => false, 'message' => 'This user already registered to a company.']));
            }

            \DB::table('users')->where([['id', '=', $user_id]])->update([
                'faction_id' => Controller::getCompanyIdByName($sanitized->company)
            ]);
        }, 5);

        return response(['status' => true]);
    }
}
