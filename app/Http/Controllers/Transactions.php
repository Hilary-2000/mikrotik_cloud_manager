<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

date_default_timezone_set('Africa/Nairobi');
class Transactions extends Controller
{
    //get all the transaction
    function display_transaction(){
        // change db
        $change_db = new login();
        $change_db->change_db();

        $transaction_data = DB::connection("mysql")->select("SELECT * FROM `transaction_tables`  ORDER by `transaction_id` DESC LIMIT 500");
        $date = date("Ymd");
        $account_names = [];
        $dates_infor = [];
        for ($index=0; $index < count($transaction_data); $index++) { 
            // return $transaction_data[$index]->transaction_account;
            $organizations = DB::connection("mysql")->select("SELECT * FROM `organizations` WHERE `account_no` = '".$transaction_data[$index]->transaction_account."'");
            $client_name = "Invalid Org!";
            if (count($organizations) > 0) {
                $client_name = $organizations[0]->organization_name;
                $transaction_data[$index]->transaction_acc_id = $organizations[0]->client_id;
            }else {
                // get the client name from the account linked to that transaction
                $organizations = DB::connection("mysql")->select("SELECT * FROM `organizations` WHERE `organization_id` = '".$transaction_data[$index]->transaction_acc_id."'");
                $client_name = count($organizations) > 0 ? $organizations[0]->organization_name : "Invalid Org!";
                $transaction_data[$index]->transaction_acc_id = count($organizations) > 0 ? $organizations[0]->client_id : $transaction_data[$index]->transaction_acc_id;
            }
            array_push($account_names,$client_name);

            $date_data = $transaction_data[$index]->transaction_date;
            // return $date_data;
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

        // return $dates_infor;
        $todayDate = date("YmdHis");
        $weekAgo = date("YmdHis",strtotime("-7 days"));
        $twoWeeksAgo = date("YmdHis",strtotime("-14 days"));
        $amonthAgo = date("YmdHis",strtotime("-1 Month"));
        $sums = DB::connection("mysql")->select("SELECT sum(`transacion_amount`) AS 'Total' FROM `transaction_tables` WHERE `transaction_date` LIKE '$date%';");
        $week = DB::connection("mysql")->select("SELECT sum(`transacion_amount`) AS 'Total' FROM `transaction_tables` WHERE `transaction_date` BETWEEN '$weekAgo' AND '$todayDate';");
        $twoWeek = DB::connection("mysql")->select("SELECT sum(`transacion_amount`) AS 'Total' FROM `transaction_tables` WHERE `transaction_date` BETWEEN '$twoWeeksAgo' AND '$todayDate';");
        $months = DB::connection("mysql")->select("SELECT sum(`transacion_amount`) AS 'Total' FROM `transaction_tables` WHERE `transaction_date` BETWEEN '$amonthAgo' AND '$todayDate';");

        // get the clients name username and phonenumber
        $clients_data = DB::connection("mysql")->select("SELECT * FROM `organizations` ORDER BY `organization_id` DESC");
        $clients_name = [];
        $clients_acc = [];
        $clients_phone = [];
        for ($index=0; $index < count($clients_data); $index++) { 
            array_push($clients_name,ucwords(strtolower($clients_data[$index]->organization_name)));
            array_push($clients_acc,$clients_data[$index]->account_no);
            array_push($clients_phone,$clients_data[$index]->organization_main_contact);
        }
        // return $transaction_data;
        return view("Transactions.index",["transaction_data" => $transaction_data, "today" => $sums,"week" => $week,"month" => $months,"twoweeks" => $twoWeek ,"account_name" => $account_names,"trans_dates" => $dates_infor,"clients_name" => $clients_name,"clients_acc" => $clients_acc,"clients_phone" => $clients_phone]);
    }

    // view transactions
    function view_transactions($transaction_id){
        // transaction data
        $transaction_data = DB::select("SELECT * FROM `transaction_tables` WHERE `transaction_id` = ?",[$transaction_id]);

        // get the transaction details and pass them to the view
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
        $user_data = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = '$transaction_acc_id'");
        if (count($user_data) > 0) {
            $user_fullname = $user_data[0]->organization_name;
        }else {
            $user_data = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = '$transaction_account'");
            $user_fullname = (count($user_data) > 0) ? $user_data[0]->organization_name : "Null";
        }

        // get the clients data
        $organization_data = DB::select("SELECT * FROM `organizations`");
        // return $transaction_data;
        return view("Transactions.view",["transaction_data" => $transaction_data, "dates" => $dates, "user_fullname"=>$user_fullname, "organization_data"=>$organization_data]);
    }

    // transaction stats
    function transaction_statistics(){
        // change db
        $change_db = new login();
        $change_db->change_db();

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
        $first_payment = DB::select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0' ORDER BY `transaction_date` ASC LIMIT 1");
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
                $get_amount_per_day = DB::select("SELECT SUM(`transacion_amount`) AS 'Total' FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_date` LIKE '".date("Ymd",strtotime($day_1))."%'");
                $daily_trans_records = DB::select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_date` LIKE '".date("Ymd",strtotime($day_1))."%' ORDER BY `transaction_date` DESC");
                $trans_amount = $get_amount_per_day[0]->Total == null ? 0 : $get_amount_per_day[0]->Total;


                for ($indexex=0; $indexex < count($daily_trans_records); $indexex++) { 
                    $client_data = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = '".$daily_trans_records[$indexex]->transaction_account."'");
                    $client_name = isset($client_data[0]->organization_name) ? $client_data[0]->organization_name : $daily_trans_records[$indexex]->transaction_account;
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
         $first_payment = DB::select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0'  ORDER BY `transaction_date` ASC LIMIT 1");
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
                 $get_amount_per_day = DB::select("SELECT SUM(`transacion_amount`) AS 'Total' FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_date` LIKE '".date("Ym",strtotime($day_1))."%'");
                 $daily_trans_records = DB::select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_date` LIKE '".date("Ym",strtotime($day_1))."%' ORDER BY `transaction_date` DESC");
                 $trans_amount = $get_amount_per_day[0]->Total == null ? 0 : $get_amount_per_day[0]->Total;

                for ($indexex=0; $indexex < count($daily_trans_records); $indexex++) { 
                    $client_data = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = '".$daily_trans_records[$indexex]->transaction_account."'");
                    $client_name = isset($client_data[0]->organization_name) ? $client_data[0]->organization_name : $daily_trans_records[$indexex]->transaction_account;
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
        $first_payment = DB::select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0' ORDER BY `transaction_date` ASC LIMIT 1");
        $first_payment_year = date("YmdHis",strtotime(count($first_payment) > 0 ? $first_payment[0]->transaction_date : date("YmdHis")));

        $transaction_yearly_stats = [];
        $transaction_yearly_records = [];

        for ($index=(date("Y",strtotime($first_payment_year))*1); $index <= (date("Y")*1); $index++) {
            $get_amount_per_day = DB::select("SELECT SUM(`transacion_amount`) AS 'Total' FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_date` LIKE '".$index."%'");
            $daily_trans_records = DB::select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_date` LIKE '".$index."%' ORDER BY `transaction_date` DESC");
            $trans_amount = $get_amount_per_day[0]->Total == null ? 0 : $get_amount_per_day[0]->Total;

            for ($indexex=0; $indexex < count($daily_trans_records); $indexex++) { 
                $client_data = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = '".$daily_trans_records[$indexex]->transaction_account."'");
                $client_name = isset($client_data[0]->organization_name) ? $client_data[0]->organization_name : $daily_trans_records[$indexex]->transaction_account;
                // array_push($account_names,$client_name);
                $daily_trans_records[$indexex]->account_names = $client_name;
            }

            $transaction_data = array("date" => $index,"trans_amount" => $trans_amount);
            array_push($transaction_yearly_stats,$transaction_data);
            array_push($transaction_yearly_records,$daily_trans_records);
        }

        // return $transaction_yearly_records;

        
        // proceed to the next year
        return view("Transactions.trans-stats",["transaction_stats_weekly" => $transaction_stats_weekly,"transaction_records_weekly" => $transaction_records_weekly,"transaction_stats_monthly" => $transaction_stats_monthly,"transaction_records_monthly" => $transaction_records_monthly,"transaction_yearly_stats" => $transaction_yearly_stats,"transaction_yearly_records" => $transaction_yearly_records]);
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
