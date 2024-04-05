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
        $sms_sent = DB::select("SELECT * FROM `sms_tables` WHERE `deleted`= '0' ORDER BY `sms_id` DESC LIMIT 5");
        $transaction_data = DB::select("SELECT * FROM `transaction_tables` ORDER BY `transaction_id` DESC LIMIT 5");
        $client_data = DB::select("SELECT * FROM `organizations` ORDER BY `organization_id` DESC LIMIT 5;");

        // transaction data
        foreach ($transaction_data as $key => $value) {
            $user = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = '".$value->transaction_acc_id."'");
            $transaction_data[$key]->organization_name = count($user) > 0 ? $user[0]->organization_name : "N/A";
            $transaction_data[$key]->transaction_date = date("D dS M Y @ h:i:sA",strtotime($transaction_data[$key]->transaction_date));
        }

        // client data
        foreach ($client_data as $key => $value) {
            $package_details = DB::select("SELECT * FROM `packages` WHERE `package_id` = ?",[$value->package_name]);
            $client_data[$key]->package_name = count($package_details) > 0 ? $package_details[0]->package_name." @ Kes ".number_format($package_details[0]->amount_paid) : "Null";
        }
        
        $user_fullname = [];
        $dates = [];
        $fullnames = [];
        $dates_trans = [];
        return view("Dashboard.index", ["sms_sent" => $sms_sent, "fullnames" => $user_fullname, "dates" => $dates, "transaction_data" => $transaction_data, "trans_fullname" => $fullnames, "trans_dates" => $dates_trans, "client_data" => $client_data]);
    }
}
