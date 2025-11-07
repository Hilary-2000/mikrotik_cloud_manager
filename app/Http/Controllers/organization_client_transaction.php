<?php

namespace App\Http\Controllers;

use App\Classes\reports\PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// set the timezone
date_default_timezone_set('Africa/Nairobi');
class organization_client_transaction extends Controller
{
    // get transactions
    function get_transactions($organization_id){
        // organization id
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization_details) == 0) {
            session()->flash("error","Invalid organization!");
            return redirect(route("Organizations"));
        }

        // change_db
        $change_db = new login();
        $change_db->change_db($organization_details[0]->organization_database);
        $database_name = $organization_details[0]->organization_database;

        $transaction_data = DB::connection("mysql2")->select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0'  ORDER by `transaction_id` DESC LIMIT 500");
        $date = date("Ymd");
        $account_names = [];
        $dates_infor = [];
        for ($index=0; $index < count($transaction_data); $index++) {
            $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `client_account` = '".$transaction_data[$index]->transaction_account."'");
            $client_name = "Null";
            if (count($client_data) > 0) {
                $client_name = $client_data[0]->client_name;
                $transaction_data[$index]->transaction_acc_id = $client_data[0]->client_id;
            }else {
                // get the client name from the account linked to that transaction
                $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `client_id` = '".$transaction_data[$index]->transaction_acc_id."'");
                $client_name = count($client_data) > 0 ? $client_data[0]->client_name : $transaction_data[$index]->transaction_acc_id;
                $transaction_data[$index]->transaction_acc_id = count($client_data) > 0 ? $client_data[0]->client_id : $transaction_data[$index]->transaction_acc_id;
            }
            array_push($account_names,$client_name);

            $date_data = $transaction_data[$index]->transaction_date;
            $year = substr($date_data,0,4);
            $month = substr($date_data,4,2);
            $day = substr($date_data,6,2);
            $hour = substr($date_data,8,2);
            $minute = substr($date_data,10,2);
            $second = substr($date_data,12,2);
            $d = mktime($hour, $minute, $second, $month, $day, $year);
            $dates = date("dS-M-Y  h:i:sa", $d);
            array_push($dates_infor,$dates);
        }

        $todayDate = date("YmdHis");
        $weekAgo = date("YmdHis",strtotime("-7 days"));
        $twoWeeksAgo = date("YmdHis",strtotime("-14 days"));
        $amonthAgo = date("YmdHis",strtotime("-1 Month"));
        $sums = DB::connection("mysql2")->select("SELECT sum(`transacion_amount`) AS 'Total' FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_date` LIKE '$date%';");
        $week = DB::connection("mysql2")->select("SELECT sum(`transacion_amount`) AS 'Total' FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_date` BETWEEN '$weekAgo' AND '$todayDate';");
        $twoWeek = DB::connection("mysql2")->select("SELECT sum(`transacion_amount`) AS 'Total' FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_date` BETWEEN '$twoWeeksAgo' AND '$todayDate';");
        $months = DB::connection("mysql2")->select("SELECT sum(`transacion_amount`) AS 'Total' FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_date` BETWEEN '$amonthAgo' AND '$todayDate';");

        // get the clients name username and phonenumber
        $clients_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted`= '0' ORDER BY `client_id` DESC");
        $clients_name = [];
        $clients_acc = [];
        $clients_phone = [];
        for ($index=0; $index < count($clients_data); $index++) { 
            array_push($clients_name,ucwords(strtolower($clients_data[$index]->client_name)));
            array_push($clients_acc,$clients_data[$index]->client_account);
            array_push($clients_phone,$clients_data[$index]->clients_contacts);
        }
        return view("Orgarnizations.client_transactions",["organization_details" => $organization_details[0], "transaction_data" => $transaction_data, "today" => $sums,"week" => $week,"month" => $months,"twoweeks" => $twoWeek ,"account_name" => $account_names,"trans_dates" => $dates_infor,"clients_name" => $clients_name,"clients_acc" => $clients_acc,"clients_phone" => $clients_phone]);
    }

    function confirmTransfer($organization_id, $trans_id, $client_id){
        // organization_id
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization_details) == 0) {
            session()->flash("error","Invalid organization!");
            return redirect(route("Organizations"));
        }

        // change_db
        $change_db = new login();
        $change_db->change_db($organization_details[0]->organization_database);

        $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `client_id` = $client_id");
        $trans_data = DB::connection("mysql2")->select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_id` = '$trans_id'");
        // update the transaction status to 1 and the transaction account id and account number to 1
        $amount = ($trans_data[0]->transacion_amount) + ($client_data[0]->wallet_amount);

        // update the users wallet and the transaction account id account number and the transaction status and return the confirmation message
        DB::connection("mysql2")->table('client_tables')
        ->where('client_id', $client_id)
        ->update([
            'wallet_amount' => $amount,
            'last_changed' => date("YmdHis"),
            'date_changed' => date("YmdHis")
        ]);

        // update the transaction details
        // transaction status, transaction acc number acc id
        DB::connection("mysql2")->table('transaction_tables')
        ->where('transaction_id', $trans_id)
        ->update([
            'transaction_acc_id' => $client_id,
            'transaction_status' => "1",
            'date_changed' => date("YmdHis")
        ]);
        // check if its the user or the admin
        if (session()->has('client_id')) {
            session()->flash("success","You have successfully transfered the funds to your account");
            return redirect("/Payment");
        }
        // end of log file
        session()->flash("success","You have successfully transfered the funds to ".$client_data[0]->client_name."");
        return redirect("/Organization/ViewTransaction/$organization_id");
    }

    function assign_transaction($organization_id, $transaction_id, $client_id){
        // organization_id
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization_details) == 0) {
            session()->flash("error","Invalid organization!");
            return redirect(route("Organizations"));
        }

        // change_db
        $change_db = new login();
        $change_db->change_db($organization_details[0]->organization_database);

        $transaction_detail = DB::connection("mysql2")->select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_id` = '$transaction_id'");
        $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `client_id` = '$client_id'");
        $date_data = $client_data[0]->next_expiration_date;
        $year = substr($date_data,0,4);
        $month = substr($date_data,4,2);
        $day = substr($date_data,6,2);
        $hour = substr($date_data,8,2);
        $minute = substr($date_data,10,2);
        $second = substr($date_data,12,2);
        $d = mktime($hour, $minute, $second, $month, $day, $year);
        $dates = date("D dS M Y  h:i:sa", $d);

        // Transaction date
        $date_data = $transaction_detail[0]->transaction_date;
        $year = substr($date_data,0,4);
        $month = substr($date_data,4,2);
        $day = substr($date_data,6,2);
        $hour = substr($date_data,8,2);
        $minute = substr($date_data,10,2);
        $second = substr($date_data,12,2);
        $d = mktime($hour, $minute, $second, $month, $day, $year);
        $dates2 = date("D dS M Y  h:i:sa", $d);
        return view("acceptTransfer",["organization_details" => $organization_details[0] ,"client_data" => $client_data, "transaction_details" => $transaction_detail,"transaction_id" => $transaction_id, "expiration_date" => $dates, "transaction_date" => $dates2]);
    }

    // transaction detail
    function transaction_details($organization_id, $transaction_id){
        // organization id
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization_details) == 0) {
            session()->flash("error","Invalid organization!");
            return redirect(route("Organizations"));
        }

        // change_db
        $change_db = new login();
        $change_db->change_db($organization_details[0]->organization_database);
        $database_name = $organization_details[0]->organization_database;

        // get the transaction details and pass them to the ciew
        $transaction_data = DB::connection("mysql2")->select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_id` = $transaction_id");
        $date_data = $transaction_data[0]->transaction_date;
        $year = substr($date_data,0,4);
        $month = substr($date_data,4,2);
        $day = substr($date_data,6,2);
        $hour = substr($date_data,8,2);
        $minute = substr($date_data,10,2);
        $second = substr($date_data,12,2);
        $d = mktime($hour, $minute, $second, $month, $day, $year);
        $dates = date("dS-M-Y  h:i:sa", $d);

        // get the client the money was paid to
        $transaction_acc_id	 = $transaction_data[0]->transaction_acc_id;
        $transaction_account	 = $transaction_data[0]->transaction_account;
        // return $transaction_acc_id;
        $user_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `client_id` = '$transaction_acc_id'");
        if (count($user_data) > 0) {
            $user_fullname = $user_data[0]->client_name;
        }else {
            $user_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `client_account` = '$transaction_account'");
            $user_fullname = (count($user_data) > 0) ? $user_data[0]->client_name : "Null";
        }

        // get the clients data
        $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted`= '0'");
        return view("Orgarnizations.view_client_transaction",["organization_details" => $organization_details[0], "transaction_data" => $transaction_data, "dates" => $dates, "user_fullname"=>$user_fullname, "client_data"=>$client_data]);
    }

    function transaction_statistics($organization_id){
        // organization id
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization_details) == 0) {
            session()->flash("error","Invalid organization!");
            return redirect(route("Organizations"));
        }

        // change_db
        $change_db = new login();
        $change_db->change_db($organization_details[0]->organization_database);
        $database_name = $organization_details[0]->organization_database;

        // get the data for weeks months and years
        $days = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
        $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        
        // date today
        $date_today = date("D");
        
        // get how many days we are after the week starts
        $date_index = 0;
        for ($index=0; $index < count($days); $index++) { 

            if ($date_today == $days[$index]) {
                break;
            }
            $date_index++;
        }

        // substract today with the date index value to get when the week starts
        $last_week_start = date("YmdHis",strtotime(-$date_index." days"));
        $last_end_week = $this->addDays($last_week_start,6);

        // get when the first client made their payment
        $first_payment = DB::connection("mysql2")->select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0' ORDER BY `transaction_date` ASC LIMIT 1");
        $first_payment_date = count($first_payment) > 0 ? $first_payment[0]->transaction_date : date("YmdHis");
        // return $first_payment_date;

        // get when the week started when the first payment was made
        $first_pay_day = date("D",strtotime($first_payment_date));
        // return $first_pay_day;

        $date_index = 0;
        for ($i=0; $i < count($days); $i++) { 
            if ($first_pay_day == $days[$i]) {
                break;
            }
            $date_index++;
        }
        // return $date_index;

        // get when the week start date
        $first_pay_week_start = $this->addDays($first_payment_date,-$date_index);
        $day_1 = $first_pay_week_start;
        // return date("D dS M Y",strtotime($day_1));

        $transaction_stats_weekly = [];
        $transaction_records_weekly = [];
        $break = false;
        $counter = 0;
        while (true) {
            $trans_stats = [];
            $trans_records = [];
            for ($index=0; $index < 7; $index++) {
                $get_amount_per_day = DB::connection("mysql2")->select("SELECT SUM(`transacion_amount`) AS 'Total' FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_date` LIKE '".date("Ymd",strtotime($day_1))."%'");
                $daily_trans_records = DB::connection("mysql2")->select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_date` LIKE '".date("Ymd",strtotime($day_1))."%' ORDER BY `transaction_date` DESC");
                $trans_amount = $get_amount_per_day[0]->Total == null ? 0 : $get_amount_per_day[0]->Total;


                for ($indexex=0; $indexex < count($daily_trans_records); $indexex++) { 
                    $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `client_account` = '".$daily_trans_records[$indexex]->transaction_account."'");
                    $client_name = isset($client_data[0]->client_name) ? $client_data[0]->client_name : $daily_trans_records[$indexex]->transaction_account;
                    // array_push($account_names,$client_name);
                    $daily_trans_records[$indexex]->account_names = $client_name;
                }

                $transaction_data = array("date" => date("D dS M",strtotime($day_1)),"trans_amount" => $trans_amount);
                // echo date("D dS M Y",strtotime($day_1))." Amounts".$trans_amount."<br>";
                array_push($trans_stats,$transaction_data);
                array_push($trans_records,$daily_trans_records);
                
                if (date("Ymd",strtotime($last_end_week)) == date("Ymd",strtotime($day_1))) {
                    $break = true;
                }
                $day_1 = $this->addDays($day_1,1);
            }
            $counter++;
            // echo $counter." Weeks <hr>";
            array_push($transaction_stats_weekly,$trans_stats);
            array_push($transaction_records_weekly,$trans_records);
            if ($break) {
                break;
            }
        }
        // return $transaction_records_weekly;

        // get the transaction data for monthly
         // date today
         $month_today = date("M");
        
         // get how many days we are after the week starts
         $months_index = 0;
         for ($index=0; $index < count($months); $index++) { 
 
             if ($month_today == $months[$index]) {
                 break;
             }
             $months_index++;
         }
        //  return $months_index;
         // substract today with the date index value to get when the week starts
         $last_month_start = date("YmdHis",strtotime(-$months_index." months"));
         $last_end_month = $this->addMonths($last_month_start,11);
        //  return $months_index;
 
         // get when the first client made their payment
         $first_payment = DB::connection("mysql2")->select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0'  ORDER BY `transaction_date` ASC LIMIT 1");
         $first_payment_date = count($first_payment) > 0 ? $first_payment[0]->transaction_date : date("YmdHis");
         // return $first_payment_date;
 
         // get when the week started when the first payment was made
         $first_pay_month = date("M",strtotime($first_payment_date));

         $months_index = 0;
         for ($i=0; $i < count($months); $i++) { 
             if ($first_pay_month == $months[$i]) {
                 break;
             }
             $months_index++;
         }
        //  return $months_index;
 
         // get when the week start date
         $first_pay_month_start = $this->addMonths($first_payment_date,-$months_index);
         $day_1 = $first_pay_month_start;
        //  return date("D dS M Y",strtotime($day_1));
 
         $transaction_stats_monthly = [];
         $transaction_records_monthly = [];
         $break = false;
         $counter = 0;
         while (true) {
             $trans_stats = [];
             $trans_records = [];
             for ($index=0; $index < 12; $index++) {
                 $get_amount_per_day = DB::connection("mysql2")->select("SELECT SUM(`transacion_amount`) AS 'Total' FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_date` LIKE '".date("Ym",strtotime($day_1))."%'");
                 $daily_trans_records = DB::connection("mysql2")->select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_date` LIKE '".date("Ym",strtotime($day_1))."%' ORDER BY `transaction_date` DESC");
                 $trans_amount = $get_amount_per_day[0]->Total == null ? 0 : $get_amount_per_day[0]->Total;

                for ($indexex=0; $indexex < count($daily_trans_records); $indexex++) { 
                    $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `client_account` = '".$daily_trans_records[$indexex]->transaction_account."'");
                    $client_name = isset($client_data[0]->client_name) ? $client_data[0]->client_name : $daily_trans_records[$indexex]->transaction_account;
                    // array_push($account_names,$client_name);
                    $daily_trans_records[$indexex]->account_names = $client_name;
                }
 
                 $transaction_data = array("date" => date("M Y",strtotime($day_1)),"trans_amount" => $trans_amount);
                 // echo date("D dS M Y",strtotime($day_1))." Amounts".$trans_amount."<br>";
                 array_push($trans_stats,$transaction_data);
                 array_push($trans_records,$daily_trans_records);
                 
                 if (date("Ym",strtotime($last_end_month)) == date("Ym",strtotime($day_1))) {
                     $break = true;
                 }
                 $day_1 = $this->addMonths($day_1,1);
             }
             $counter++;
             // echo $counter." Weeks <hr>";
             array_push($transaction_stats_monthly,$trans_stats);
             array_push($transaction_records_monthly,$trans_records);
             if ($break) {
                 break;
             }
         }
        // return $transaction_stats_monthly;

        // get the yearly data
        $first_payment = DB::connection("mysql2")->select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0' ORDER BY `transaction_date` ASC LIMIT 1");
        $first_payment_year = date("YmdHis",strtotime(count($first_payment) > 0 ? $first_payment[0]->transaction_date : date("YmdHis")));

        $transaction_yearly_stats = [];
        $transaction_yearly_records = [];

        for ($index=(date("Y",strtotime($first_payment_year))*1); $index <= (date("Y")*1); $index++) {
            $get_amount_per_day = DB::connection("mysql2")->select("SELECT SUM(`transacion_amount`) AS 'Total' FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_date` LIKE '".$index."%'");
            $daily_trans_records = DB::connection("mysql2")->select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_date` LIKE '".$index."%' ORDER BY `transaction_date` DESC");
            $trans_amount = $get_amount_per_day[0]->Total == null ? 0 : $get_amount_per_day[0]->Total;

            for ($indexex=0; $indexex < count($daily_trans_records); $indexex++) { 
                $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `client_account` = '".$daily_trans_records[$indexex]->transaction_account."'");
                $client_name = isset($client_data[0]->client_name) ? $client_data[0]->client_name : $daily_trans_records[$indexex]->transaction_account;
                // array_push($account_names,$client_name);
                $daily_trans_records[$indexex]->account_names = $client_name;
            }

            $transaction_data = array("date" => $index,"trans_amount" => $trans_amount);
            array_push($transaction_yearly_stats,$transaction_data);
            array_push($transaction_yearly_records,$daily_trans_records);
        }
        // proceed to the next year
        return view("Orgarnizations.organization_client_stats",["organization_details" => $organization_details[0], "transaction_stats_weekly" => $transaction_stats_weekly,"transaction_records_weekly" => $transaction_records_weekly,"transaction_stats_monthly" => $transaction_stats_monthly,"transaction_records_monthly" => $transaction_records_monthly,"transaction_yearly_stats" => $transaction_yearly_stats,"transaction_yearly_records" => $transaction_yearly_records]);
    }
    // generate reports 
    function generate_reports(Request $req, $organization_id){
        // organization id
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization_details) == 0) {
            session()->flash("error","Invalid organization!");
            return redirect(route("Organizations"));
        }

        // change_db
        $change_db = new login();
        $change_db->change_db($organization_details[0]->organization_database);
        $database_name = $organization_details[0]->organization_database;

        // return $req;
        $transaction_date_option = $req->input('transaction_date_option');
        $from_select_date = $req->input('from_select_date');
        $to_select_date = $req->input('to_select_date');
        $select_registration_date = $req->input('select_registration_date');
        $select_user_option = $req->input('select_user_option');
        $client_account = $req->input('client_account');

        // sort in two options of the client specific or a group
        $title = "";
        $transaction_data = [];
        if ($select_user_option == "All") {
            if ($transaction_date_option == "all dates") {
                $title = "All Transactions done!";
                $transaction_data = DB::connection("mysql2")->select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0' ORDER BY `transaction_id` DESC");
            }elseif ($transaction_date_option == "select date") {
                $title = "All Transactions on ".date("D dS M Y", strtotime($select_registration_date))."!";
                $date = date("Ymd",strtotime($select_registration_date));
                $transaction_data = DB::connection("mysql2")->select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_date` LIKE '".$date."%' ORDER BY `transaction_id` DESC");
            }elseif ($transaction_date_option == "between dates") {
                $from = date("YmdHis",strtotime($from_select_date));
                $to = date("Ymd",strtotime($to_select_date))."235959";
                $title = "All Transactions done between (".date("D dS M Y", strtotime($from_select_date)).") and (".date("D dS M Y",strtotime($to_select_date)).")!";
                $transaction_data = DB::connection("mysql2")->select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_date` BETWEEN ? AND ? ORDER BY `transaction_id` DESC",[$from,$to]);
            }else{
                $title = "All Transactions done!";
                $transaction_data = DB::connection("mysql2")->select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0' ORDER BY `transaction_id` DESC");
            }
        }elseif ($select_user_option == "specific_user") {
            $client_names = $this->getClientName($client_account);
            if ($transaction_date_option == "all dates") {
                $title = "All ".$client_names." Transactions done!";
                $transaction_data = DB::connection("mysql2")->select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_account` = ? ORDER BY `transaction_id` DESC",[$client_account]);
            }elseif ($transaction_date_option == "select date") {
                $title = "All ".$client_names."`s Transactions done on ".date("D dS M Y",strtotime($select_registration_date))."!";
                $date = date("Ymd",strtotime($select_registration_date));
                $transaction_data = DB::connection("mysql2")->select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_account` = ? AND `transaction_date` LIKE '".$date."%' ORDER BY `transaction_id` DESC",[$client_account]);
            }elseif ($transaction_date_option == "between dates") {
                $from = date("YmdHis",strtotime($from_select_date));
                $to = date("Ymd",strtotime($to_select_date))."235959";
                $title = "All ".$client_names."`s Transactions done between (".date("D dS M Y",strtotime($from_select_date)).") AND (".date("D dS M Y",strtotime($to_select_date)).")!";
                $transaction_data = DB::connection("mysql2")->select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_account` = ? AND `transaction_date` BETWEEN ? AND ? ORDER BY `transaction_id` DESC",[$client_account,$from,$to]);
            }else{
                $title = "All ".$client_names." Transactions done!";
                $transaction_data = DB::connection("mysql2")->select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_account` = ? ORDER BY `transaction_id` DESC",[$client_account]);
            }
        }

        // GET THE TRANSACTION INFORMATION
        $new_transaction_data = [];
        $assigned = 0;
        $un_assigned = 0;
        $assigned_amount = 0;
        $un_assigned_amount = 0;
        for ($index=0; $index < count($transaction_data); $index++) { 
            if ($transaction_data[$index]->transaction_status) {
                $assigned++;
                $assigned_amount += $transaction_data[$index]->transacion_amount;
            }else {
                $un_assigned++;
                $un_assigned_amount += $transaction_data[$index]->transacion_amount;
            }
            $data = array(
                $transaction_data[$index]->transaction_mpesa_id,
                $this->getClientNames($transaction_data[$index]->transaction_account,$transaction_data[$index]->transaction_acc_id, $organization_id)." {".$transaction_data[$index]->transaction_account."}",
                $transaction_data[$index]->phone_transacting,
                $transaction_data[$index]->transacion_amount,
                $transaction_data[$index]->transaction_date,
                $transaction_data[$index]->fullnames,
                ($transaction_data[$index]->transaction_status == "1" ? "Assigned" : "Un-Assigned")
            );
            array_push($new_transaction_data,$data);
        }

        // create pdf
        $pdf = new PDF("P","mm","A4");
        $pdf->set_company_name($organization_details[0]->organization_name);
        $pdf->set_school_contact($organization_details[0]->organization_main_contact);
        $pdf->set_document_title($title);
        $pdf->AddPage();
        $pdf->SetFont('Times', 'B', 10);
        $pdf->SetMargins(5,5);
        $pdf->Cell(40, 10, "Statistics", 0, 0, 'L', false);
        $pdf->Ln();
        $pdf->Cell(40, 5, "", 0, 0, 'L', false);
        $pdf->Cell(30, 5, "Records", 0, 0, 'L', false);
        $pdf->Cell(30, 5, "Amount", 0, 1, 'L', false);
        $pdf->SetFont('Times', 'I', 9);
        $pdf->Cell(40, 5, "Un-Assigned Payments :", 0, 0, 'L', false);
        $pdf->Cell(30, 5, $un_assigned . " Payment(s)", 0, 0, 'L', false);
        $pdf->Cell(30, 5, "Kes ".number_format($un_assigned_amount), 0, 1, 'L', false);
        $pdf->Cell(40, 5, "Assigned Payments :", 0, 0, 'L', false);
        $pdf->Cell(30, 5, $assigned . " Payment(s)", 0,0, 'L', false);
        $pdf->Cell(30, 5, "Kes ".number_format($assigned_amount), 0, 1, 'L', false);
        $pdf->Cell(40, 5, "Total :", 'T', 0, 'L', false);
        $pdf->Cell(30, 5, ($un_assigned+$assigned) . " Payment(s)", 'T', 0, 'L', false);
        $pdf->Cell(30, 5, "Kes ".number_format($un_assigned_amount + $assigned_amount), 'T', 0, 'L', false);
        $pdf->Ln();
        $pdf->SetFont('Helvetica', 'BU', 9);
        $pdf->Cell(200,8,"Payment(s) Table",0,1,"C",false);
        $pdf->SetFont('Helvetica', 'B', 7);
        $width = array(6,20,40,20,20,40,40,15);
        $header = array('No', 'M-Pesa Code', 'Linked To {Acc Paid To}', 'Phone Number', 'Amount','Date','M-Pesa Fullname', 'Status');
        $pdf->transactionReports($header,$new_transaction_data,$width);
        $pdf->Output("I","transaction_data.pdf",false);

    }
    function getClientNames($client_account, $client_acc_id, $organization_id){
        // organization id
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization_details) == 0) {
            // session()->flash("error","Invalid organization!");
            return "NULL";
        }

        // change_db
        $change_db = new login();
        $change_db->change_db($organization_details[0]->organization_database);
        $database_name = $organization_details[0]->organization_database;

        $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `client_account` = ?",[$client_account]);
        if (count($client_data) > 0) {
            return ucwords(strtolower($client_data[0]->client_name));
        }
        $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `client_id` = ?",[$client_acc_id]);
        $client_name = count($client_data) > 0 ? $client_data[0]->client_name : "Null";
        return ucwords(strtolower($client_name));
    }

    function addDays($date,$days){
        $date = date_create($date);
        date_add($date,date_interval_create_from_date_string($days." day"));
        return date_format($date,"YmdHis");
    }

    function addMonths($date,$months){
        $date = date_create($date);
        date_add($date,date_interval_create_from_date_string($months." Month"));
        return date_format($date,"YmdHis");
    }
    function addYear($date,$years){
        $date = date_create($date);
        date_add($date,date_interval_create_from_date_string($years." Year"));
        return date_format($date,"YmdHis");
    }
}
