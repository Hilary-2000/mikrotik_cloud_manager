<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    function formatKenyanPhone($number) {
        // Remove spaces, dashes, and plus sign
        $number = preg_replace('/[\s\-\+]/', '', $number);

        // If it starts with "07", replace with "2547"
        if (preg_match('/^07\d{8}$/', $number)) {
            return '254' . substr($number, 1);
        }

        // If it starts with "+2547" (after plus removal)
        if (preg_match('/^2547\d{8}$/', $number)) {
            return $number;
        }

        // If it starts with "7" only, add "254"
        if (preg_match('/^7\d{8}$/', $number)) {
            return '254' . $number;
        }

        // Invalid number
        return null;
    }
}
