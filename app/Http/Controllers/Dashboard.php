<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Dashboard extends Controller
{
    //

    // HANDLE THE DASHBOARD
    function getDashboard(){
        // get sms sent
        $sms_sent = DB::connection("mysql")->select("SELECT * FROM `sms_tables` WHERE `deleted`= '0' ORDER BY `sms_id` DESC LIMIT 5");
        
        $user_fullname = [];
        $dates = [];
        $transaction_data = [];
        $fullnames = [];
        $dates_trans = [];
        $client_data = [];
        return view("Dashboard.index", ["sms_sent" => $sms_sent, "fullnames" => $user_fullname, "dates" => $dates, "transaction_data" => $transaction_data, "trans_fullname" => $fullnames, "trans_dates" => $dates_trans, "client_data" => $client_data]);
    }
}
