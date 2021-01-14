<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public static function getTimestamp()
    {
        return intval(microtime(true) * 1000);
    }

    public static function getCompanyNameById($faction_id)
    {
        switch ($faction_id) {
            case 1:
                return 'MMO';
            case 2:
                return 'EIC';
            case 3:
                return 'VRU';       
            default:
                return 'NONE';
        }
    }

    public static function getCompanyIdByName($company)
    {
        switch ($company) {
            case 'MMO':
                return 1;
            case 'EIC':
                return 2;
            case 'VRU':
                return 3;       
            default:
                return 0;
        }
    }
}
