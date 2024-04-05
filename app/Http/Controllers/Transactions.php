<?php

namespace App\Http\Controllers;

use App\Classes\reports\PDF;
use App\Models\sms_table;
use App\Models\transaction_table;
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
                $transaction_data[$index]->transaction_acc_id = $organizations[0]->organization_id;
            }else {
                // get the client name from the account linked to that transaction
                $organizations = DB::connection("mysql")->select("SELECT * FROM `organizations` WHERE `organization_id` = '".$transaction_data[$index]->transaction_acc_id."'");
                $client_name = count($organizations) > 0 ? $organizations[0]->organization_name : "Invalid Org!";
                $transaction_data[$index]->transaction_acc_id = count($organizations) > 0 ? $organizations[0]->organization_id : $transaction_data[$index]->transaction_acc_id;
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
        $amonthAgo = date("YmdHis",strtotime("-1 Month"));
        $three_months = date("YmdHis",strtotime("-3 Month"));
        $six_months = date("YmdHis",strtotime("-4 Month"));
        $twelve_months = date("YmdHis",strtotime("-12 Month"));

        $today = DB::connection("mysql")->select("SELECT sum(`transacion_amount`) AS 'Total' FROM `transaction_tables` WHERE `transaction_date` LIKE '$date%';");
        $last_month = DB::connection("mysql")->select("SELECT sum(`transacion_amount`) AS 'Total' FROM `transaction_tables` WHERE `transaction_date` BETWEEN '$amonthAgo' AND '$todayDate';");
        $last_three_months = DB::connection("mysql")->select("SELECT sum(`transacion_amount`) AS 'Total' FROM `transaction_tables` WHERE `transaction_date` BETWEEN '$three_months' AND '$todayDate';");
        $last_six_months = DB::connection("mysql")->select("SELECT sum(`transacion_amount`) AS 'Total' FROM `transaction_tables` WHERE `transaction_date` BETWEEN '$six_months' AND '$todayDate';");
        $last_year = DB::connection("mysql")->select("SELECT sum(`transacion_amount`) AS 'Total' FROM `transaction_tables` WHERE `transaction_date` BETWEEN '$twelve_months' AND '$todayDate';");

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
        return view("Transactions.index",["transaction_data" => $transaction_data, "today" => $today,"last_month" => $last_month,"last_three_months" => $last_three_months,"last_six_months" => $last_six_months, "last_year" => $last_year ,"account_name" => $account_names,"trans_dates" => $dates_infor,"clients_name" => $clients_name,"clients_acc" => $clients_acc,"clients_phone" => $clients_phone]);
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

    // assign transaction
    function assign_transaction($organization_id, $transaction_id){
        $organizations = new Organization();
        $exp_date = $organizations->get_expiry($organization_id);
        $expiry_date = $exp_date['date'];

        $transaction_detail = DB::select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_id` = '$transaction_id'");
        $organization_data = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = '$organization_id'");

        // organization expiry date
        $dates = date("D dS M Y  h:i:sa", strtotime($expiry_date));

        // get package name
        $package_data = DB::select("SELECT * FROM `packages` WHERE `package_id` = '".$organization_data[0]->package_name."'");
        $organization_data[0]->package_name = count($package_data) > 0 ? $package_data[0]->package_name." - (Kes ". number_format($package_data[0]->amount_paid).")" : "Null";

        // Transaction date
        $dates2 = date("D dS M Y  h:i:sa", strtotime($transaction_detail[0]->transaction_date));
        return view("Transactions.accept-transfer",["organization_data" => $organization_data, "transaction_details" => $transaction_detail,"transaction_id" => $transaction_id, "expiration_date" => $dates, "transaction_date" => $dates2]);
    }

    function confirm_transfer($organization_id, $transaction_id){

        $organization_data = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = '$organization_id'");
        $transaction_data = DB::select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_id` = '$transaction_id'");
        // update the transaction status to 1 and the transaction account id and account number to 1
        $amount = ($transaction_data[0]->transacion_amount) + ($organization_data[0]->wallet);

        // update the users wallet and the transaction account id account number and the transaction status and return the confirmation message
        DB::table('organizations')
        ->where('organization_id', $organization_id)
        ->update([
            'wallet' => $amount
        ]);

        // update the transaction details
        // transaction status, transaction acc number acc id
        DB::table('transaction_tables')
        ->where('transaction_id', $transaction_id)
        ->update([
            'transaction_acc_id' => $organization_id,
            'transaction_status' => "1",
            'date_changed' => date("YmdHis")
        ]);
                
        $new_client = new Clients();
        $txt = ":Fund successfully transfered by  ".session('Usernames')." to ".$organization_data[0]->organization_name."!";
        // $new_client->log($txt);
        // end of log file
        session()->flash("success","You have successfully transfered the funds to ".$organization_data[0]->organization_name."");
        return redirect(route("view_transactions", [$transaction_id]));
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
    function mpesa_transactions(Request $response){
        // get the database for the business shortcode
		$mpesaResponse = $response;
        
        // echo $mpesaResponse;
         $jsonMpesaResponse = $mpesaResponse;
         if(isset($jsonMpesaResponse)){
            //  check the account number to know the user
            $acc_no = trim($jsonMpesaResponse['BillRefNumber']);
            $ipo = 0;

            // ipo is used to check if its hypbits clients
            $organization_data = DB::select("SELECT * FROM `organizations` WHERE `account_no` = '$acc_no';");
            $phone_number = $jsonMpesaResponse['MSISDN'];

            $organization_id = 0;
            $organization_transaction_id = 0;
            $transaction_status = "0";
            if (count($organization_data) > 0) {
                $transaction_status = "1";
                $wallet = $organization_data[0]->wallet + ($jsonMpesaResponse['TransAmount'] * 1);
                $organization_id = $organization_data[0]->organization_id;
                $organization_transaction_id = $organization_id;

                // update the wallet amount and send the sms to the user
                DB::table("organizations")->where('organization_id', $organization_data[0]->organization_id)->update(["wallet" => $wallet]);
                
                // check if the user phone number is same to the one stored in the database
                $phone_db = (strlen($organization_data[0]->organization_main_contact) == 12) ? substr($organization_data[0]->organization_main_contact,3,9) : substr($organization_data[0]->organization_main_contact,1,9);
                
                // get the sms keys
                $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'sms_api_key'");
                $sms_api_key = $sms_keys[0]->value;
                $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'sms_partner_id'");
                $sms_partner_id = $sms_keys[0]->value;
                $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'sms_shortcode'");
                $sms_shortcode = $sms_keys[0]->value;


                $partnerID = $sms_partner_id;
                $apikey = $sms_api_key;
                $shortcode = $sms_shortcode;
                $mobile = "";
                $message = "";
                $sms_type = 1;

                $mobile = "254$phone_db"; // Bulk messages can be comma separated
                // send sms
                $message_contents = $this->get_sms();
                $message = $message_contents[1]->messages[0]->message;

                if ($message) {
                    // replace false with message above
                    $trans_amount = $jsonMpesaResponse['TransAmount'];
                    $message = $this->message_content($message,$organization_id,$trans_amount);

                    // send_sms($conn,$row['clients_contacts'],$message,$row['client_id']);
                    $finalURL = "https://isms.celcomafrica.com/api/services/sendsms/?apikey=" . urlencode($apikey) . "&partnerID=" . urlencode($partnerID) . "&message=" . urlencode($message) . "&shortcode=$shortcode&mobile=$mobile";
                    $ch = \curl_init();
                    \curl_setopt($ch, CURLOPT_URL, $finalURL);
                    \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    \curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    $response = \curl_exec($ch);
                    \curl_close($ch);
                    $res = json_decode($response);

                    if (isset($res->responses[0])) {
                        $values = $res->responses[0];
                        // return $values;
                        // return $res;
                        $message_status = 0;
                        foreach ($values as  $key => $value) {
                            // echo $key;
                            if ($key == "response-code") {
                                if ($value == "200") {
                                    // if its 200 the message is sent delete the
                                    $message_status = 1;
                                }
                            }
                        }
                        
                        // if the message status is one the message is already sent to the user
                        $sms_table = new sms_table();
                        $sms_table->sms_content = $message;
                        $sms_table->date_sent = date("YmdHis");
                        $sms_table->recipient_phone = $mobile;
                        $sms_table->sms_status = $message_status;
                        $sms_table->account_id = $organization_transaction_id;
                        $sms_table->sms_type = $sms_type;
                        $sms_table->save();
                    }
                }
            }else {
                // if the user is not known
                // send the sms showing that the transaction is pending

                // get the sms keys
                $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'sms_api_key'");
                $sms_api_key = $sms_keys[0]->value;
                $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'sms_partner_id'");
                $sms_partner_id = $sms_keys[0]->value;
                $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'sms_shortcode'");
                $sms_shortcode = $sms_keys[0]->value;


                $partnerID = $sms_partner_id;
                $apikey = $sms_api_key;
                $shortcode = $sms_shortcode;
                $mobile = $jsonMpesaResponse['MSISDN'];
                $message_contents = $this->get_sms();
                $message = $message_contents[1]->messages[1]->message;
                if ($message) {// replace false with message
                    $trans_amount = $jsonMpesaResponse['TransAmount'];
                    $message = $this->message_content($message,$organization_id,$trans_amount);
                    // send the sms
                    $finalURL = "https://isms.celcomafrica.com/api/services/sendsms/?apikey=" . urlencode($apikey) . "&partnerID=" . urlencode($partnerID) . "&message=" . urlencode($message) . "&shortcode=$shortcode&mobile=$mobile";
                    $ch = \curl_init();
                    \curl_setopt($ch, CURLOPT_URL, $finalURL);
                    \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    \curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    $response = \curl_exec($ch);
                    \curl_close($ch);
                    $res = json_decode($response);
                    
                    // return $res;
                    $values = (isset($res->responses[0])) ? $res->responses[0] : $res->success ;
                    
                    if ((isset($res->responses[0]))) {
                        if ($values != "false") {
                            $message_status = 0;
                            foreach ($values as  $key => $value) {
                                // echo $key;
                                if ($key == "response-code") {
                                    if ($value == "200") {
                                        // if its 200 the message is sent delete the
                                        $message_status = 1;
                                    }
                                }
                            }
                
                            // save to the database the transaction made
                            $sms_table = new sms_table();
                            $sms_table->sms_content = $message;
                            $sms_table->date_sent = date("YmdHis");
                            $sms_table->recipient_phone = $mobile;
                            $sms_table->sms_status = $message_status;
                            $sms_table->account_id = "0";
                            $sms_table->sms_type = "1";
                            $sms_table->save();
                        }
                    }
                }
            }

            // save the data in the transaction table
            $clientelle = str_replace("'","_",$jsonMpesaResponse['FirstName']);
            $transTable = new transaction_table();
            $transTable->transaction_mpesa_id = $jsonMpesaResponse['TransID'];
            $transTable->transaction_date = $jsonMpesaResponse['TransTime'];
            $transTable->transacion_amount = $jsonMpesaResponse['TransAmount'];
            $transTable->phone_transacting = $jsonMpesaResponse['MSISDN'];
            $transTable->transaction_account = $jsonMpesaResponse['BillRefNumber'];
            $transTable->transaction_acc_id = $organization_transaction_id;
            $transTable->transaction_status = $transaction_status;
            $transTable->transaction_short_code = $jsonMpesaResponse['BusinessShortCode'];
            $transTable->fullnames = $clientelle;
            $transTable->save();
        
                
            $new_client = new Clients();
            $txt = ": Funds successfully received from ".$jsonMpesaResponse['FirstName']." paid for INVALID account number ".$jsonMpesaResponse['BillRefNumber']."!";
            if (count($organization_data) > 0) {
                $txt = ": Funds successfully received from ".$jsonMpesaResponse['FirstName']." paid for ".ucwords(strtolower($organization_data[0]->organization_name))." account number ".$jsonMpesaResponse['BillRefNumber']."!";
            }
            // $new_client->log_db($txt,$organization[0]->organization_database);
            // end of log file
            return [$txt];
        }
    }
    function generate_reports(Request $req){
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
                $transaction_data = DB::select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0' ORDER BY `transaction_id` DESC");
            }elseif ($transaction_date_option == "select date") {
                $title = "All Transactions on ".date("D dS M Y", strtotime($select_registration_date))."!";
                $date = date("Ymd",strtotime($select_registration_date));
                $transaction_data = DB::select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_date` LIKE '".$date."%' ORDER BY `transaction_id` DESC");
            }elseif ($transaction_date_option == "between dates") {
                $from = date("YmdHis",strtotime($from_select_date));
                $to = date("Ymd",strtotime($to_select_date))."235959";
                $title = "All Transactions done between (".date("D dS M Y", strtotime($from_select_date)).") and (".date("D dS M Y",strtotime($to_select_date)).")!";
                $transaction_data = DB::select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_date` BETWEEN ? AND ? ORDER BY `transaction_id` DESC",[$from,$to]);
            }else{
                $title = "All Transactions done!";
                $transaction_data = DB::select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0' ORDER BY `transaction_id` DESC");
            }
        }elseif ($select_user_option == "specific_user") {
            $client_names = $this->getClientName($client_account);
            if ($transaction_date_option == "all dates") {
                $title = "All ".$client_names." Transactions done!";
                $transaction_data = DB::select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_account` = ? ORDER BY `transaction_id` DESC",[$client_account]);
            }elseif ($transaction_date_option == "select date") {
                $title = "All ".$client_names."`s Transactions done on ".date("D dS M Y",strtotime($select_registration_date))."!";
                $date = date("Ymd",strtotime($select_registration_date));
                $transaction_data = DB::select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_account` = ? AND `transaction_date` LIKE '".$date."%' ORDER BY `transaction_id` DESC",[$client_account]);
            }elseif ($transaction_date_option == "between dates") {
                $from = date("YmdHis",strtotime($from_select_date));
                $to = date("Ymd",strtotime($to_select_date))."235959";
                $title = "All ".$client_names."`s Transactions done between (".date("D dS M Y",strtotime($from_select_date)).") AND (".date("D dS M Y",strtotime($to_select_date)).")!";
                $transaction_data = DB::select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_account` = ? AND `transaction_date` BETWEEN ? AND ? ORDER BY `transaction_id` DESC",[$client_account,$from,$to]);
            }else{
                $title = "All ".$client_names." Transactions done!";
                $transaction_data = DB::select("SELECT * FROM `transaction_tables` WHERE `deleted`= '0' AND `transaction_account` = ? ORDER BY `transaction_id` DESC",[$client_account]);
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
                $this->getClientNames($transaction_data[$index]->transaction_account,$transaction_data[$index]->transaction_acc_id)." {".$transaction_data[$index]->transaction_account."}",
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
        if (session("organization_logo")) {
            $pdf->setCompayLogo("../../../../../../../../..".public_path(session("organization_logo")));
            $pdf->set_company_name(session("organization")->organization_name);
            $pdf->set_school_contact(session("organization")->organization_main_contact);
        }
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
    function getClientNames($account_no,$organization_id){
        // change db
        $change_db = new login();
        $change_db->change_db();

        // return by organization data
        $organizations_data = DB::select("SELECT * FROM `organizations` WHERE `account_no` = ?",[$account_no]);
        if (count($organizations_data) > 0) {
            return ucwords(strtolower($organizations_data[0]->organization_name));
        }

        // return by organization
        $organizations_data = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        $organization_name = count($organizations_data) > 0 ? $organizations_data[0]->organization_name : "Null";
        return ucwords(strtolower($organization_name));
    }

    // get sms
	function get_sms(){
        $data = DB::select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'Messages'");
        return json_decode($data[0]->value);
	}
	function message_content($data,$organization_id,$trans_amount) {

        $organization_data = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = '$organization_id'");
        if (count($organization_data) > 0) {
            $organizations = new Organization();
            $exp_date = $organizations->get_expiry($organization_id);
            $expiry_date = $exp_date['date'];

            // expiry date
            $exp_date = ($expiry_date);

            // package details
            $package_details = DB::select("SELECT * FROM `packages` WHERE `package_id` = ?",[$organization_data[0]->organization_id]);
            $package_name = count($package_details) > 0 ? "{".$package_details[0]->package_name. " @ Kes ". number_format($package_details[0]->amount_paid)." after every ".$package_details[0]->package_period."}" : "Null";

            $reg_date = $organization_data[0]->date_joined;
            $monthly_payment = $package_name;
            $full_name = $organization_data[0]->organization_name;
            $f_name = ucfirst(strtolower((explode(" ",$full_name)[0])));
            $address = $organization_data[0]->organization_address;
            $contacts = $organization_data[0]->organization_main_contact;
            $account_no = $organization_data[0]->account_no;
            $wallet = $organization_data[0]->wallet;
            $trans_amount = isset($trans_amount)?$trans_amount:"Null";
            
            // edited
            $today = date("dS-M-Y");
            $now = date("H:i:s");
            $time = $exp_date;
            $exp_date = date("dS-M-Y",strtotime($exp_date));
            $exp_time = date("H:i:s",strtotime($time));
            $reg_date = date("dS-M-Y",strtotime($reg_date));
            $data = str_replace("[client_name]", $full_name, $data);
            $data = str_replace("[client_f_name]", $f_name, $data);
            $data = str_replace("[client_addr]", $address, $data);
            $data = str_replace("[exp_date]", $exp_date." at ".$exp_time, $data);
            $data = str_replace("[reg_date]", $reg_date, $data);
            $data = str_replace("[package_name]", "Ksh ".$monthly_payment, $data);
            $data = str_replace("[client_phone]", $contacts, $data);
            $data = str_replace("[acc_no]", $account_no, $data);
            $data = str_replace("[client_wallet]", "Ksh ".$wallet, $data);
            $data = str_replace("[trans_amnt]", "Ksh ".$trans_amount, $data);
            $data = str_replace("[today]", $today, $data);
            $data = str_replace("[now]", $now,$data);
            return $data;
        }else {
            // null data
            $exp_date = "Null";
            $reg_date = "Null";
            $monthly_payment = "Null";
            $full_name = "Null";
            $f_name = ucfirst(strtolower((explode(" ",$full_name)[0])));
            $address = "Null";
            $contacts = "Null";
            $account_no = "Null";
            $wallet = "Null";
            $trans_amount = isset($trans_amount)?$trans_amount:"Null";

            // edited
            $today = date("dS-M-Y");
            $now = date("H:i:s");
            $time = $exp_date;
            $exp_date = date("dS-M-Y",strtotime($exp_date));
            $exp_time = date("H:i:s",strtotime($time));
            $reg_date = date("dS-M-Y",strtotime($reg_date));

            // replace the string data
            $data = str_replace("[client_name]", $full_name, $data);
            $data = str_replace("[client_f_name]", $f_name, $data);
            $data = str_replace("[client_addr]", $address, $data);
            $data = str_replace("[exp_date]", $exp_date." at ".$exp_time, $data);
            $data = str_replace("[reg_date]", $reg_date, $data);
            $data = str_replace("[package_name]", "Ksh ".$monthly_payment, $data);
            $data = str_replace("[client_phone]", $contacts, $data);
            $data = str_replace("[acc_no]", $account_no, $data);
            $data = str_replace("[client_wallet]", "Ksh ".$wallet, $data);
            $data = str_replace("[trans_amnt]", "Ksh ".$trans_amount, $data);
            $data = str_replace("[today]", $today, $data);
            $data = str_replace("[now]", $now,$data);
            return $data;
        }
	}
}
