<?php

namespace App\Http\Controllers;

use App\Classes\reports\PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

// set the timezone
date_default_timezone_set('Africa/Nairobi');
class Organization_sms extends Controller
{
    //Organization sms
    function view_organization_sms($organization_id){
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

        $sms_data = DB::connection("mysql2")->select("SELECT * FROM `sms_tables` WHERE `deleted`= '0' ORDER BY `sms_id` DESC LIMIT 1000");
        // get the clients names
        $client_names = [];
        $dates = [];
        foreach ($sms_data as $value) {
            // get the clients data
            $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `client_id` = '$value->account_id'");
            $client_name = $value->recipient_phone;

            if (count($client_data) > 0) {
                $client_name = $client_data[0]->client_name;
            }else{
                // $client_name = (count($client_data)>0) ? $client_data[0]->client_name: $value->recipient_phone;
                $phone_db = (strlen($value->recipient_phone) == 12) ? substr($value->recipient_phone,3,9) : substr($value->recipient_phone,1,9);
                $user_data_return = $this->getOwnerPhone($value->account_id,$phone_db);
                $client_name = $value->recipient_phone;
            }
            array_push($client_names,$client_name);
            // get the payment dates
            // return $client_name;
            
            $date_data = $value->date_sent;
            $year = substr($date_data,0,4);
            $month = substr($date_data,4,2);
            $day = substr($date_data,6,2);
            $hour = substr($date_data,8,2);
            $minute = substr($date_data,10,2);
            $second = substr($date_data,12,2);
            $d = mktime($hour, $minute, $second, $month, $day, $year);
            $dates2 = date("D dS M Y  h:i:sa", $d);
            array_push($dates,$dates2);
        }
        // return count($client_names);
        // GET ALL THE SMS SENT TODAY
        $today = date("Ymd");
        $sms_today = DB::connection("mysql2")->select("SELECT COUNT(*) AS 'Total' FROM `sms_tables` WHERE `deleted`= '0' AND `date_sent` LIKE '$today%'");
        $sms_count = $sms_today[0]->Total;
        // GET FOR THE LAST ONE WEEK
        $last_week = date("YmdHis",strtotime("-7 days"));
        $lastweek_sms = DB::connection("mysql2")->select("SELECT COUNT(*) AS 'Total' FROM `sms_tables` WHERE `deleted`= '0' AND `date_sent` > $last_week;");
        $sms_week = $lastweek_sms[0]->Total;
        // GET ALL SMS SENT BY THE SYSTEM
        $total_sms = DB::connection("mysql2")->select("SELECT COUNT(*) AS 'Total' FROM `sms_tables` WHERE `deleted`= '0'");
        $totalsms = $total_sms[0]->Total;

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
        return view("Orgarnizations.SMS.index",["organization_details" => $organization_details[0], "sms_data" =>$sms_data,"client_names" => $client_names, "dates" => $dates, "sms_count" => $sms_count, "last_week" => $sms_week,"total_sms" => $totalsms,"clients_name" => $clients_name,"clients_acc" => $clients_acc,"clients_phone" => $clients_phone]);
    }
    
    function getOwnerPhone($account_id,$phone_number){
        // change db
        $change_db = new login();
        $change_db->change_db();

        $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `client_id` = '$account_id'");
        if (count($client_data) > 0) {
            return [$account_id,ucwords(strtolower($client_data[0]->client_name))];
        }
        $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `clients_contacts` LIKE '%".$phone_number."%'");
        $client_name = (count($client_data)>0) ? $client_data[0]->client_name: $phone_number;
        return [(count($client_data) > 0 ? $client_data[0]->client_id:0),ucwords(strtolower($client_name))];
    }

    function view_sms($organization_id, $sms_id){
        // organization id
        // return $organization_id;
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization_details) == 0) {
            session()->flash("error","Invalid organization!");
            return redirect(route("Organizations"));
        }

        // change_db
        $change_db = new login();
        $change_db->change_db($organization_details[0]->organization_database);
        $database_name = $organization_details[0]->organization_database;

        $sms_data = DB::connection("mysql2")->select("SELECT * FROM `sms_tables` WHERE `deleted`= '0' AND `sms_id` = '$sms_id'");
        
        $date_data = $sms_data[0]->date_sent;;
        $year = substr($date_data,0,4);
        $month = substr($date_data,4,2);
        $day = substr($date_data,6,2);
        $hour = substr($date_data,8,2);
        $minute = substr($date_data,10,2);
        $second = substr($date_data,12,2);
        $d = mktime($hour, $minute, $second, $month, $day, $year);
        $dates2 = date("D dS M Y  h:i:sa", $d);

        $account_id = $sms_data[0]->account_id;
        $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `client_id` = '$account_id'");
        $client_name = (count($client_data)>0)? $client_data[0]->client_name: "Unknown";
        $sms_type = $sms_data[0]->sms_type;
        // get the sms type
        $sms_type = ($sms_type == "1") ? "Transaction" : "Notification";
        return view("Orgarnizations.SMS.view",["organization_details" => $organization_details[0], "sms_data" => $sms_data,"date" => $dates2, "client_name" => $client_name, "sms_type" => $sms_type]);
    }

    function delete_sms($organization_id, $sms_id){
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

        $data = DB::connection("mysql2")->delete("DELETE FROM `sms_tables` WHERE `deleted`= '0' AND `sms_id` = '$sms_id'");
        session()->flash("success_sms","Message successfully deleted");
        return redirect(route("view_organization_sms", [$organization_id]));
    }

    function customize_sms($organization_id){
        // organization_id
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization_details) == 0) {
            session()->flash("error","Invalid organization!");
            return redirect(route("Organizations"));
        }

        // change_db
        $change_db = new login();
        $change_db->change_db($organization_details[0]->organization_database);
        $database_name = $organization_details[0]->organization_database;

        $sms_data = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'Messages'");
        $message_content =  json_decode($sms_data[0]->value);
        // return $message_content;
        return view("Orgarnizations.SMS.custom_sms",["organization_details" => $organization_details[0], "sms_data"=>$message_content]);
    }


    function save_sms_customize ($organization_id, Request $req){
        // organization_id
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization_details) == 0) {
            session()->flash("error","Invalid organization!");
            return redirect(route("Organizations"));
        }

        // change_db
        $change_db = new login();
        $change_db->change_db($organization_details[0]->organization_database);
        $database_name = $organization_details[0]->organization_database;

        // return $req->input();
        // save it in settings
        $sms_content = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'Messages'");
        // if we have some data we update the data
        if (count($sms_content) > 0) {
            $message_content =  json_decode($sms_content[0]->value);
            // return $message_content;
            if ($req->input('date_before')) {
                $message_content[0]->messages[0]->message = $req->input("message_contents");
                // return $message_content;
                DB::connection("mysql2")->table('settings')
                ->where('keyword', 'Messages')
                ->update([
                    'value' => $message_content,
                    'date_changed' => date("YmdHis")
                ]);
                return redirect(route("customize_sms", [$organization_id]));
            }elseif($req->input('deday')){
                $message_content[0]->messages[1]->message = $req->input("message_contents");
                // return $message_content;
                DB::connection("mysql2")->table('settings')
                ->where('keyword', 'Messages')
                ->update([
                    'value' => $message_content,
                    'date_changed' => date("YmdHis")
                ]);
                return redirect(route("customize_sms", [$organization_id]));
            }elseif ($req->input('after_due_date')) {
                $message_content[0]->messages[2]->message = $req->input("message_contents");
                // return $req->input();
                DB::connection("mysql2")->table('settings')
                ->where('keyword', 'Messages')
                ->update([
                    'value' => $message_content,
                    'date_changed' => date("YmdHis")
                ]);
                return redirect(route("customize_sms", [$organization_id]));
            }elseif ($req->input('correct_acc_no')) {
                $message_content[1]->messages[0]->message = $req->input("message_contents");// return $req->input();
                DB::connection("mysql2")->table('settings')
                ->where('keyword', 'Messages')
                ->update([
                    'value' => $message_content,
                    'date_changed' => date("YmdHis")
                ]);
                return redirect(route("customize_sms", [$organization_id]));
            }elseif ($req->input("incorrect_acc_no")) {
                $message_content[1]->messages[1]->message = $req->input("message_contents");
                DB::connection("mysql2")->table('settings')
                ->where('keyword', 'Messages')
                ->update([
                    'value' => $message_content,
                    'date_changed' => date("YmdHis")
                ]);
                return redirect(route("customize_sms", [$organization_id]));
            }elseif ($req->input('account_renewed')) {
                $message_content[2]->messages[0]->message = $req->input("message_contents");
                DB::connection("mysql2")->table('settings')
                ->where('keyword', 'Messages')
                ->update([
                    'value' => $message_content,
                    'date_changed' => date("YmdHis")
                ]);
                return redirect(route("customize_sms", [$organization_id]));
            }elseif ($req->input('account_extended')) {
                // return $message_content[2]->messages[1]->message;
                $message_content[2]->messages[1]->message = $req->input("message_contents");
                DB::connection("mysql2")->table('settings')
                ->where('keyword', 'Messages')
                ->update([
                    'value' => $message_content,
                    'date_changed' => date("YmdHis")
                ]);
                return redirect(route("customize_sms", [$organization_id]));
            }elseif ($req->input('welcome_sms')) {
                // return $message_content[3]->messages[0]->message;
                $message_content[3]->messages[0]->message = $req->input("message_contents");
                DB::connection("mysql2")->table('settings')
                ->where('keyword', 'Messages')
                ->update([
                    'value' => $message_content,
                    'date_changed' => date("YmdHis")
                ]);
                return redirect(route("customize_sms", [$organization_id]));
            }elseif ($req->input('account_deactivated')) {
                $message_content[2]->messages[2]->message = $req->input("message_contents");
                DB::connection("mysql2")->table('settings')
                ->where('keyword', 'Messages')
                ->update([
                    'value' => $message_content,
                    'date_changed' => date("YmdHis")
                ]);
                return redirect(route("customize_sms", [$organization_id]));
            }elseif ($req->input('refferer_msg')) {
                // return $message_content;
                if(isset($message_content[1]->messages[2]->message)){
                    $message_content[1]->messages[2]->message = $req->input('message_contents');
                    DB::connection("mysql2")->table('settings')
                    ->where('keyword', 'Messages')
                    ->update([
                        'value' => $message_content,
                        'date_changed' => date("YmdHis")
                    ]);
                    return redirect(route("customize_sms", [$organization_id]));
                }else{
                    $msgs = array("Name" => "refferer_msg","message" => $req->input('message_contents'));
                    // return $msgs;
                    array_push($message_content[1]->messages,$msgs);
                    DB::connection("mysql2")->table('settings')
                    ->where('keyword', 'Messages')
                    ->update([
                        'value' => $message_content,
                        'date_changed' => date("YmdHis")
                    ]);
                    return redirect(route("customize_sms", [$organization_id]));
                }
            }elseif ($req->input('below_min_amnt')) {
                // return $message_content;
                if(isset($message_content[1]->messages[3]->message)){
                    $message_content[1]->messages[3]->message = $req->input('message_contents');
                    DB::connection("mysql2")->table('settings')
                    ->where('keyword', 'Messages')
                    ->update([
                        'value' => $message_content,
                        'date_changed' => date("YmdHis")
                    ]);
                    return redirect(route("customize_sms", [$organization_id]));
                }else{
                    $msgs = array("Name" => "refferer_msg","message" => $req->input('message_contents'));
                    // return $msgs;
                    array_push($message_content[1]->messages,$msgs);
                    DB::connection("mysql2")->table('settings')
                    ->where('keyword', 'Messages')
                    ->update([
                        'value' => $message_content,
                        'date_changed' => date("YmdHis")
                    ]);
                    return redirect(route("customize_sms", [$organization_id]));
                }
            }elseif ($req->input('welcome_client_sms')) {
                // return $message_content[4];
                if(isset($message_content[4])){
                    // set the welcome client sms
                    $messages = $message_content[4]->messages;
                    $present = 0;
                    for ($index=0; $index < count($messages); $index++) { 
                        if ($messages[$index]->Name == $req->input('welcome_client_sms')) {
                            $messages[$index]->message = $req->input('message_contents');
                            $present = 1;
                        }
                    }
                    // return $message_content;
                    if ($present == 1){
                        DB::connection("mysql2")->table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect(route("customize_sms", [$organization_id]));
                    }
                    if ($present == 0) {
                        // add the message to the messages list
                        $data = array("Name" =>"welcome_client_sms", "message" => $req->input('message_contents'));
                        array_push($message_content[4]->messages,$data);
                        // return $message_content;
                        DB::connection("mysql2")->table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect(route("customize_sms", [$organization_id]));
                    }
                }else{
                    // create the array
                    $arrayed = ["Name" => "sms_bill_manager","messages" => []];
                    array_push($message_content,$arrayed);
                    $message_content = json_decode(json_encode($message_content));
                    // proceed and add the new message
                    $message = array("Name" => $req->input('welcome_client_sms'), "message" => $req->input('message_contents'));
                    array_push($message_content[4]->messages,$message);
                    // return $message_content;
                    DB::connection("mysql2")->table('settings')
                    ->where('keyword', 'Messages')
                    ->update([
                        'value' => $message_content,
                        'date_changed' => date("YmdHis")
                    ]);
                    return redirect(route("customize_sms", [$organization_id]));
                }
            }elseif ($req->input('rcv_coracc_billsms')) {
                if(isset($message_content[4])){
                    // set the welcome client sms
                    $messages = $message_content[4]->messages;
                    $present = 0;
                    for ($index=0; $index < count($messages); $index++) { 
                        if ($messages[$index]->Name == $req->input('rcv_coracc_billsms')) {
                            $messages[$index]->message = $req->input('message_contents');
                            $present = 1;
                        }
                    }
                    // return $message_content;
                    if ($present == 1) {
                        DB::connection("mysql2")->table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect(route("customize_sms", [$organization_id]));
                    }
                    if ($present == 0) {
                        // add the message to the messages list
                        $data = array("Name" =>"rcv_coracc_billsms", "message" => $req->input('message_contents'));
                        array_push($message_content[4]->messages,$data);
                        // return $message_content;
                        DB::connection("mysql2")->table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect(route("customize_sms", [$organization_id]));
                    }
                }else{
                    // create the array
                    $arrayed = ["Name" => "sms_bill_manager","messages" => []];
                    array_push($message_content,$arrayed);
                    $message_content = json_decode(json_encode($message_content));
                    // proceed and add the new message
                    $message = array("Name" => $req->input('rcv_coracc_billsms'), "message" => $req->input('message_contents'));
                    array_push($message_content[4]->messages,$message);
                    // return $message_content;
                    DB::connection("mysql2")->table('settings')
                    ->where('keyword', 'Messages')
                    ->update([
                        'value' => $message_content,
                        'date_changed' => date("YmdHis")
                    ]);
                    return redirect(route("customize_sms", [$organization_id]));
                }
            }elseif ($req->input('rcv_incoracc_billsms')) {
                // return $req->input();
                if(isset($message_content[4])){
                    // set the welcome client sms
                    $messages = $message_content[4]->messages;
                    $present = 0;
                    for ($index=0; $index < count($messages); $index++) { 
                        if ($messages[$index]->Name == $req->input('rcv_incoracc_billsms')) {
                            $messages[$index]->message = $req->input('message_contents');
                            $present = 1;
                        }
                    }
                    // return $message_content;
                    if ($present == 1) {
                        DB::connection("mysql2")->table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect(route("customize_sms", [$organization_id]));
                    }
                    if ($present == 0) {
                        // add the message to the messages list
                        $data = array("Name" =>"rcv_incoracc_billsms", "message" => $req->input('message_contents'));
                        array_push($message_content[4]->messages,$data);
                        // return $message_content;
                        DB::connection("mysql2")->table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect(route("customize_sms", [$organization_id]));
                    }
                }else{
                    // create the array
                    $arrayed = ["Name" => "sms_bill_manager","messages" => []];
                    array_push($message_content,$arrayed);
                    $message_content = json_decode(json_encode($message_content));
                    // proceed and add the new message
                    $message = array("Name" => $req->input('rcv_incoracc_billsms'), "message" => $req->input('message_contents'));
                    array_push($message_content[4]->messages,$message);
                    // return $message_content;
                    DB::connection("mysql2")->table('settings')
                    ->where('keyword', 'Messages')
                    ->update([
                        'value' => $message_content,
                        'date_changed' => date("YmdHis")
                    ]);
                    return redirect(route("customize_sms", [$organization_id]));
                }
            }elseif ($req->input('rcv_belowmin_billsms')) {
                // return $req->input();
                if(isset($message_content[4])){
                    // set the welcome client sms
                    $messages = $message_content[4]->messages;
                    $present = 0;
                    for ($index=0; $index < count($messages); $index++) { 
                        if ($messages[$index]->Name == $req->input('rcv_belowmin_billsms')) {
                            $messages[$index]->message = $req->input('message_contents');
                            $present = 1;
                        }
                    }
                    // return $message_content;
                    if ($present == 1) {
                        DB::connection("mysql2")->table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect(route("customize_sms", [$organization_id]));
                    }
                    if ($present == 0) {
                        // add the message to the messages list
                        $data = array("Name" =>"rcv_belowmin_billsms", "message" => $req->input('message_contents'));
                        array_push($message_content[4]->messages,$data);
                        // return $message_content;
                        DB::connection("mysql2")->table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect(route("customize_sms", [$organization_id]));
                    }
                }else{
                    // create the array
                    $arrayed = ["Name" => "sms_bill_manager","messages" => []];
                    array_push($message_content,$arrayed);
                    $message_content = json_decode(json_encode($message_content));
                    // proceed and add the new message
                    $message = array("Name" => $req->input('rcv_belowmin_billsms'), "message" => $req->input('message_contents'));
                    array_push($message_content[4]->messages,$message);
                    // return $message_content;
                    DB::connection("mysql2")->table('settings')
                    ->where('keyword', 'Messages')
                    ->update([
                        'value' => $message_content,
                        'date_changed' => date("YmdHis")
                    ]);
                    return redirect(route("customize_sms", [$organization_id]));
                }
            }elseif ($req->input('msg_reminder_bal')) {
                // return $req->input();
                if(isset($message_content[4])){
                    // set the welcome client sms
                    $messages = $message_content[4]->messages;
                    $present = 0;
                    for ($index=0; $index < count($messages); $index++) { 
                        if ($messages[$index]->Name == $req->input('msg_reminder_bal')) {
                            $messages[$index]->message = $req->input('message_contents');
                            $present = 1;
                        }
                    }
                    // return $message_content;
                    if ($present == 1) {
                        DB::connection("mysql2")->table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect(route("customize_sms", [$organization_id]));
                    }
                    if ($present == 0) {
                        // add the message to the messages list
                        $data = array("Name" =>"msg_reminder_bal", "message" => $req->input('message_contents'));
                        array_push($message_content[4]->messages,$data);
                        // return $message_content;
                        DB::connection("mysql2")->table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect(route("customize_sms", [$organization_id]));
                    }
                }else{
                    // create the array
                    $arrayed = ["Name" => "sms_bill_manager","messages" => []];
                    array_push($message_content,$arrayed);
                    $message_content = json_decode(json_encode($message_content));
                    // proceed and add the new message
                    $message = array("Name" => $req->input('msg_reminder_bal'), "message" => $req->input('message_contents'));
                    array_push($message_content[4]->messages,$message);
                    // return $message_content;
                    DB::connection("mysql2")->table('settings')
                    ->where('keyword', 'Messages')
                    ->update([
                        'value' => $message_content,
                        'date_changed' => date("YmdHis")
                    ]);
                    return redirect(route("customize_sms", [$organization_id]));
                }
            }elseif ($req->input('account_frozen')) {
                // return $req->input();
                if (isset($message_content[5])) {
                    // set the welcome client sms
                    $messages = $message_content[5]->messages;
                    $present = 0;
                    for ($index=0; $index < count($messages); $index++) { 
                        if ($messages[$index]->Name == $req->input('account_frozen')) {
                            $messages[$index]->message = $req->input('message_contents');
                            $present = 1;
                        }
                    }
                    // return $message_content;
                    if ($present == 1) {
                        DB::connection("mysql2")->table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect(route("customize_sms", [$organization_id]));
                    }
                    if ($present == 0) {
                        // add the message to the messages list
                        $data = array("Name" => $req->input('account_frozen'), "message" => $req->input('message_contents'));
                        array_push($message_content[5]->messages,$data);
                        // return $message_content;
                        DB::connection("mysql2")->table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect(route("customize_sms", [$organization_id]));
                    }
                }else{
                    // create the std class
                    $message = new stdClass();
                    $message->Name = "account_freezing";
                    $message->messages = [array("Name" => $req->input('account_frozen'), "message" => $req->input('message_contents')),array("Name" => 'account_unfrozen', "message" => ''),array("Name" => 'future_account_freeze', "message" => '')];

                    // array_push index 5
                    array_push($message_content,$message);
                    // return $message_content;

                    DB::connection("mysql2")->table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect(route("customize_sms", [$organization_id]));
                }
            }elseif ($req->input('account_unfrozen')) {
                // return $req->input();
                if (isset($message_content[5])) {
                    // set the welcome client sms
                    $messages = $message_content[5]->messages;
                    $present = 0;
                    for ($index=0; $index < count($messages); $index++) { 
                        if ($messages[$index]->Name == $req->input('account_unfrozen')) {
                            $messages[$index]->message = $req->input('message_contents');
                            $present = 1;
                        }
                    }
                    if ($present == 1) {
                        DB::connection("mysql2")->table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect(route("customize_sms", [$organization_id]));
                    }
                    if ($present == 0) {
                        // add the message to the messages list
                        $data = array("Name" => $req->input('account_unfrozen'), "message" => $req->input('message_contents'));
                        array_push($message_content[5]->messages,$data);
                        // return $message_content;
                        DB::connection("mysql2")->table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect(route("customize_sms", [$organization_id]));
                    }
                }else{
                    // create the std class
                    $message = new stdClass();
                    $message->Name = "account_freezing";
                    $message->messages = [array("Name" => 'account_frozen', "message" => ''),array("Name" => $req->input('account_unfrozen'), "message" => $req->input('message_contents')),array("Name" => "future_account_freeze", "message" => "")];

                    // array_push index 5
                    array_push($message_content,$message);
                    // return $message_content;

                    DB::connection("mysql2")->table('settings')
                    ->where('keyword', 'Messages')
                    ->update([
                        'value' => $message_content,
                        'date_changed' => date("YmdHis")
                    ]);
                    return redirect(route("customize_sms", [$organization_id]));
                }
            }elseif ($req->input('future_account_freeze')) {
                // return $req->input();
                if (isset($message_content[5])) {
                    // set the welcome client sms
                    $messages = $message_content[5]->messages;
                    $present = 0;
                    for ($index=0; $index < count($messages); $index++) {
                        if ($messages[$index]->Name == $req->input('future_account_freeze')) {
                            $messages[$index]->message = $req->input('message_contents');
                            $present = 1;
                        }
                    }
                    if ($present == 1) {
                        DB::connection("mysql2")->table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect(route("customize_sms", [$organization_id]));
                    }
                    if ($present == 0) {
                        // add the message to the messages list
                        $data = array("Name" => $req->input('future_account_freeze'), "message" => $req->input('message_contents'));
                        array_push($message_content[5]->messages,$data);
                        // return $message_content;
                        DB::connection("mysql2")->table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect(route("customize_sms", [$organization_id]));
                    }
                }else{
                    // create the std class
                    // array("Name" => 'account_freezing', "message" => '');
                    $message = new stdClass();
                    $message->Name = "account_freezing";
                    $message->messages = [array("Name" => 'account_frozen', "message" => ''),array("Name" => 'account_unfrozen', "message" => ''),array("Name" => $req->input('future_account_freeze'), "message" => $req->input('message_contents'))];

                    // then add the rest
                    array_push($message_content,$message);
                    // return $message_content;

                    DB::connection("mysql2")->table('settings')
                    ->where('keyword', 'Messages')
                    ->update([
                        'value' => $message_content,
                        'date_changed' => date("YmdHis")
                    ]);
                    return redirect(route("customize_sms", [$organization_id]));
                }
            }
            // DO NOT ADD MORE FROM THESE AREA.
            // YOU BETTER SELECT A CATEGORY TO ADD TO OR CHANGE HOW THE MESSAGES ARE GOING TO BE RETRIEVED
            // OTHERWISE ITS GOING TO BE MESSY
        }else{
            // is we dont have any data we insert the data
            return redirect(route("customize_sms", [$organization_id]));
        }
    }

    // save sms content
    function save_sms_content (Request $req, $organization_id){
        // organization_id
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization_details) == 0) {
            session()->flash("error","Invalid organization!");
            return redirect(route("Organizations"));
        }

        // change_db
        $change_db = new login();
        $change_db->change_db($organization_details[0]->organization_database);
        $database_name = $organization_details[0]->organization_database;
        // return $organization_details;

        // return $req->input();
        // save it in settings
        $sms_content = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `deleted`= '0' AND `keyword` = 'Messages'");
        // if we have some data we update the data
        if (count($sms_content) > 0) {
            $message_content =  json_decode($sms_content[0]->value);
            // return $message_content;
            if ($req->input('date_before')) {
                $message_content[0]->messages[0]->message = $req->input("message_contents");
                // return $message_content;
                DB::connection("mysql2")->table('settings')
                ->where('keyword', 'Messages')
                ->update([
                    'value' => $message_content,
                    'date_changed' => date("YmdHis")
                ]);
                return redirect(route("customize_sms",[$organization_id]));
            }elseif($req->input('deday')){
                $message_content[0]->messages[1]->message = $req->input("message_contents");
                // return $message_content;
                DB::connection("mysql2")->table('settings')
                ->where('keyword', 'Messages')
                ->update([
                    'value' => $message_content,
                    'date_changed' => date("YmdHis")
                ]);
                return redirect(route("customize_sms",[$organization_id]));
            }elseif ($req->input('after_due_date')) {
                $message_content[0]->messages[2]->message = $req->input("message_contents");
                // return $req->input();
                DB::connection("mysql2")->table('settings')
                ->where('keyword', 'Messages')
                ->update([
                    'value' => $message_content,
                    'date_changed' => date("YmdHis")
                ]);
                return redirect(route("customize_sms",[$organization_id]));
            }elseif ($req->input('correct_acc_no')) {
                $message_content[1]->messages[0]->message = $req->input("message_contents");// return $req->input();
                DB::connection("mysql2")->table('settings')
                ->where('keyword', 'Messages')
                ->update([
                    'value' => $message_content,
                    'date_changed' => date("YmdHis")
                ]);
                return redirect(route("customize_sms",[$organization_id]));
            }elseif ($req->input("incorrect_acc_no")) {
                $message_content[1]->messages[1]->message = $req->input("message_contents");
                DB::connection("mysql2")->table('settings')
                ->where('keyword', 'Messages')
                ->update([
                    'value' => $message_content,
                    'date_changed' => date("YmdHis")
                ]);
                return redirect(route("customize_sms",[$organization_id]));
            }elseif ($req->input('account_renewed')) {
                $message_content[2]->messages[0]->message = $req->input("message_contents");
                DB::connection("mysql2")->table('settings')
                ->where('keyword', 'Messages')
                ->update([
                    'value' => $message_content,
                    'date_changed' => date("YmdHis")
                ]);
                return redirect(route("customize_sms",[$organization_id]));
            }elseif ($req->input('account_extended')) {
                // return $message_content[2]->messages[1]->message;
                $message_content[2]->messages[1]->message = $req->input("message_contents");
                DB::connection("mysql2")->table('settings')
                ->where('keyword', 'Messages')
                ->update([
                    'value' => $message_content,
                    'date_changed' => date("YmdHis")
                ]);
                return redirect(route("customize_sms",[$organization_id]));
            }elseif ($req->input('welcome_sms')) {
                // return $message_content[3]->messages[0]->message;
                $message_content[3]->messages[0]->message = $req->input("message_contents");
                DB::connection("mysql2")->table('settings')
                ->where('keyword', 'Messages')
                ->update([
                    'value' => $message_content,
                    'date_changed' => date("YmdHis")
                ]);
                return redirect(route("customize_sms",[$organization_id]));
            }elseif ($req->input('account_deactivated')) {
                $message_content[2]->messages[2]->message = $req->input("message_contents");
                DB::connection("mysql2")->table('settings')
                ->where('keyword', 'Messages')
                ->update([
                    'value' => $message_content,
                    'date_changed' => date("YmdHis")
                ]);
                return redirect(route("customize_sms",[$organization_id]));
            }elseif ($req->input('refferer_msg')) {
                // return $message_content;
                if(isset($message_content[1]->messages[2]->message)){
                    $message_content[1]->messages[2]->message = $req->input('message_contents');
                    DB::connection("mysql2")->table('settings')
                    ->where('keyword', 'Messages')
                    ->update([
                        'value' => $message_content,
                        'date_changed' => date("YmdHis")
                    ]);
                    return redirect(route("customize_sms",[$organization_id]));
                }else{
                    $msgs = array("Name" => "refferer_msg","message" => $req->input('message_contents'));
                    // return $msgs;
                    array_push($message_content[1]->messages,$msgs);
                    DB::connection("mysql2")->table('settings')
                    ->where('keyword', 'Messages')
                    ->update([
                        'value' => $message_content,
                        'date_changed' => date("YmdHis")
                    ]);
                    return redirect(route("customize_sms",[$organization_id]));
                }
            }elseif ($req->input('below_min_amnt')) {
                // return $message_content;
                if(isset($message_content[1]->messages[3]->message)){
                    $message_content[1]->messages[3]->message = $req->input('message_contents');
                    DB::connection("mysql2")->table('settings')
                    ->where('keyword', 'Messages')
                    ->update([
                        'value' => $message_content,
                        'date_changed' => date("YmdHis")
                    ]);
                    return redirect(route("customize_sms",[$organization_id]));
                }else{
                    $msgs = array("Name" => "refferer_msg","message" => $req->input('message_contents'));
                    // return $msgs;
                    array_push($message_content[1]->messages,$msgs);
                    DB::connection("mysql2")->table('settings')
                    ->where('keyword', 'Messages')
                    ->update([
                        'value' => $message_content,
                        'date_changed' => date("YmdHis")
                    ]);
                    return redirect(route("customize_sms",[$organization_id]));
                }
            }elseif ($req->input('welcome_client_sms')) {
                // return $message_content[4];
                if(isset($message_content[4])){
                    // set the welcome client sms
                    $messages = $message_content[4]->messages;
                    $present = 0;
                    for ($index=0; $index < count($messages); $index++) { 
                        if ($messages[$index]->Name == $req->input('welcome_client_sms')) {
                            $messages[$index]->message = $req->input('message_contents');
                            $present = 1;
                        }
                    }
                    // return $message_content;
                    if ($present == 1){
                        DB::connection("mysql2")->table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect(route("customize_sms",[$organization_id]));
                    }
                    if ($present == 0) {
                        // add the message to the messages list
                        $data = array("Name" =>"welcome_client_sms", "message" => $req->input('message_contents'));
                        array_push($message_content[4]->messages,$data);
                        // return $message_content;
                        DB::connection("mysql2")->table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect(route("customize_sms",[$organization_id]));
                    }
                }else{
                    // create the array
                    $arrayed = ["Name" => "sms_bill_manager","messages" => []];
                    array_push($message_content,$arrayed);
                    $message_content = json_decode(json_encode($message_content));
                    // proceed and add the new message
                    $message = array("Name" => $req->input('welcome_client_sms'), "message" => $req->input('message_contents'));
                    array_push($message_content[4]->messages,$message);
                    // return $message_content;
                    DB::connection("mysql2")->table('settings')
                    ->where('keyword', 'Messages')
                    ->update([
                        'value' => $message_content,
                        'date_changed' => date("YmdHis")
                    ]);
                    return redirect(route("customize_sms",[$organization_id]));
                }
            }elseif ($req->input('rcv_coracc_billsms')) {
                if(isset($message_content[4])){
                    // set the welcome client sms
                    $messages = $message_content[4]->messages;
                    $present = 0;
                    for ($index=0; $index < count($messages); $index++) { 
                        if ($messages[$index]->Name == $req->input('rcv_coracc_billsms')) {
                            $messages[$index]->message = $req->input('message_contents');
                            $present = 1;
                        }
                    }
                    // return $message_content;
                    if ($present == 1) {
                        DB::connection("mysql2")->table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect(route("customize_sms",[$organization_id]));
                    }
                    if ($present == 0) {
                        // add the message to the messages list
                        $data = array("Name" =>"rcv_coracc_billsms", "message" => $req->input('message_contents'));
                        array_push($message_content[4]->messages,$data);
                        // return $message_content;
                        DB::connection("mysql2")->table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect(route("customize_sms",[$organization_id]));
                    }
                }else{
                    // create the array
                    $arrayed = ["Name" => "sms_bill_manager","messages" => []];
                    array_push($message_content,$arrayed);
                    $message_content = json_decode(json_encode($message_content));
                    // proceed and add the new message
                    $message = array("Name" => $req->input('rcv_coracc_billsms'), "message" => $req->input('message_contents'));
                    array_push($message_content[4]->messages,$message);
                    // return $message_content;
                    DB::connection("mysql2")->table('settings')
                    ->where('keyword', 'Messages')
                    ->update([
                        'value' => $message_content,
                        'date_changed' => date("YmdHis")
                    ]);
                    return redirect(route("customize_sms",[$organization_id]));
                }
            }elseif ($req->input('rcv_incoracc_billsms')) {
                // return $req->input();
                if(isset($message_content[4])){
                    // set the welcome client sms
                    $messages = $message_content[4]->messages;
                    $present = 0;
                    for ($index=0; $index < count($messages); $index++) { 
                        if ($messages[$index]->Name == $req->input('rcv_incoracc_billsms')) {
                            $messages[$index]->message = $req->input('message_contents');
                            $present = 1;
                        }
                    }
                    // return $message_content;
                    if ($present == 1) {
                        DB::connection("mysql2")->table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect(route("customize_sms",[$organization_id]));
                    }
                    if ($present == 0) {
                        // add the message to the messages list
                        $data = array("Name" =>"rcv_incoracc_billsms", "message" => $req->input('message_contents'));
                        array_push($message_content[4]->messages,$data);
                        // return $message_content;
                        DB::connection("mysql2")->table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect(route("customize_sms",[$organization_id]));
                    }
                }else{
                    // create the array
                    $arrayed = ["Name" => "sms_bill_manager","messages" => []];
                    array_push($message_content,$arrayed);
                    $message_content = json_decode(json_encode($message_content));
                    // proceed and add the new message
                    $message = array("Name" => $req->input('rcv_incoracc_billsms'), "message" => $req->input('message_contents'));
                    array_push($message_content[4]->messages,$message);
                    // return $message_content;
                    DB::connection("mysql2")->table('settings')
                    ->where('keyword', 'Messages')
                    ->update([
                        'value' => $message_content,
                        'date_changed' => date("YmdHis")
                    ]);
                    return redirect(route("customize_sms",[$organization_id]));
                }
            }elseif ($req->input('rcv_belowmin_billsms')) {
                // return $req->input();
                if(isset($message_content[4])){
                    // set the welcome client sms
                    $messages = $message_content[4]->messages;
                    $present = 0;
                    for ($index=0; $index < count($messages); $index++) { 
                        if ($messages[$index]->Name == $req->input('rcv_belowmin_billsms')) {
                            $messages[$index]->message = $req->input('message_contents');
                            $present = 1;
                        }
                    }
                    // return $message_content;
                    if ($present == 1) {
                        DB::connection("mysql2")->table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect(route("customize_sms",[$organization_id]));
                    }
                    if ($present == 0) {
                        // add the message to the messages list
                        $data = array("Name" =>"rcv_belowmin_billsms", "message" => $req->input('message_contents'));
                        array_push($message_content[4]->messages,$data);
                        // return $message_content;
                        DB::connection("mysql2")->table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect(route("customize_sms",[$organization_id]));
                    }
                }else{
                    // create the array
                    $arrayed = ["Name" => "sms_bill_manager","messages" => []];
                    array_push($message_content,$arrayed);
                    $message_content = json_decode(json_encode($message_content));
                    // proceed and add the new message
                    $message = array("Name" => $req->input('rcv_belowmin_billsms'), "message" => $req->input('message_contents'));
                    array_push($message_content[4]->messages,$message);
                    // return $message_content;
                    DB::connection("mysql2")->table('settings')
                    ->where('keyword', 'Messages')
                    ->update([
                        'value' => $message_content,
                        'date_changed' => date("YmdHis")
                    ]);
                    return redirect(route("customize_sms",[$organization_id]));
                }
            }elseif ($req->input('msg_reminder_bal')) {
                // return $req->input();
                if(isset($message_content[4])){
                    // set the welcome client sms
                    $messages = $message_content[4]->messages;
                    $present = 0;
                    for ($index=0; $index < count($messages); $index++) { 
                        if ($messages[$index]->Name == $req->input('msg_reminder_bal')) {
                            $messages[$index]->message = $req->input('message_contents');
                            $present = 1;
                        }
                    }
                    // return $message_content;
                    if ($present == 1) {
                        DB::connection("mysql2")->table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect(route("customize_sms",[$organization_id]));
                    }
                    if ($present == 0) {
                        // add the message to the messages list
                        $data = array("Name" =>"msg_reminder_bal", "message" => $req->input('message_contents'));
                        array_push($message_content[4]->messages,$data);
                        // return $message_content;
                        DB::connection("mysql2")->table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect(route("customize_sms",[$organization_id]));
                    }
                }else{
                    // create the array
                    $arrayed = ["Name" => "sms_bill_manager","messages" => []];
                    array_push($message_content,$arrayed);
                    $message_content = json_decode(json_encode($message_content));
                    // proceed and add the new message
                    $message = array("Name" => $req->input('msg_reminder_bal'), "message" => $req->input('message_contents'));
                    array_push($message_content[4]->messages,$message);
                    // return $message_content;
                    DB::connection("mysql2")->table('settings')
                    ->where('keyword', 'Messages')
                    ->update([
                        'value' => $message_content,
                        'date_changed' => date("YmdHis")
                    ]);
                    return redirect(route("customize_sms",[$organization_id]));
                }
            }elseif ($req->input('account_frozen')) {
                // return $req->input();
                if (isset($message_content[5])) {
                    // set the welcome client sms
                    $messages = $message_content[5]->messages;
                    $present = 0;
                    for ($index=0; $index < count($messages); $index++) { 
                        if ($messages[$index]->Name == $req->input('account_frozen')) {
                            $messages[$index]->message = $req->input('message_contents');
                            $present = 1;
                        }
                    }
                    // return $message_content;
                    if ($present == 1) {
                        DB::connection("mysql2")->table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect(route("customize_sms",[$organization_id]));
                    }
                    if ($present == 0) {
                        // add the message to the messages list
                        $data = array("Name" => $req->input('account_frozen'), "message" => $req->input('message_contents'));
                        array_push($message_content[5]->messages,$data);
                        // return $message_content;
                        DB::connection("mysql2")->table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect(route("customize_sms",[$organization_id]));
                    }
                }else{
                    // create the std class
                    $message = new stdClass();
                    $message->Name = "account_freezing";
                    $message->messages = [array("Name" => $req->input('account_frozen'), "message" => $req->input('message_contents')),array("Name" => 'account_unfrozen', "message" => ''),array("Name" => 'future_account_freeze', "message" => '')];

                    // array_push index 5
                    array_push($message_content,$message);
                    // return $message_content;

                    DB::connection("mysql2")->table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect(route("customize_sms",[$organization_id]));
                }
            }elseif ($req->input('account_unfrozen')) {
                // return $req->input();
                if (isset($message_content[5])) {
                    // set the welcome client sms
                    $messages = $message_content[5]->messages;
                    $present = 0;
                    for ($index=0; $index < count($messages); $index++) { 
                        if ($messages[$index]->Name == $req->input('account_unfrozen')) {
                            $messages[$index]->message = $req->input('message_contents');
                            $present = 1;
                        }
                    }
                    if ($present == 1) {
                        DB::connection("mysql2")->table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect(route("customize_sms",[$organization_id]));
                    }
                    if ($present == 0) {
                        // add the message to the messages list
                        $data = array("Name" => $req->input('account_unfrozen'), "message" => $req->input('message_contents'));
                        array_push($message_content[5]->messages,$data);
                        // return $message_content;
                        DB::connection("mysql2")->table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect(route("customize_sms",[$organization_id]));
                    }
                }else{
                    // create the std class
                    $message = new stdClass();
                    $message->Name = "account_freezing";
                    $message->messages = [array("Name" => 'account_frozen', "message" => ''),array("Name" => $req->input('account_unfrozen'), "message" => $req->input('message_contents')),array("Name" => "future_account_freeze", "message" => "")];

                    // array_push index 5
                    array_push($message_content,$message);
                    // return $message_content;

                    DB::connection("mysql2")->table('settings')
                    ->where('keyword', 'Messages')
                    ->update([
                        'value' => $message_content,
                        'date_changed' => date("YmdHis")
                    ]);
                    return redirect(route("customize_sms",[$organization_id]));
                }
            }elseif ($req->input('future_account_freeze')) {
                // return $req->input();
                if (isset($message_content[5])) {
                    // set the welcome client sms
                    $messages = $message_content[5]->messages;
                    $present = 0;
                    for ($index=0; $index < count($messages); $index++) {
                        if ($messages[$index]->Name == $req->input('future_account_freeze')) {
                            $messages[$index]->message = $req->input('message_contents');
                            $present = 1;
                        }
                    }
                    if ($present == 1) {
                        DB::connection("mysql2")->table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect(route("customize_sms",[$organization_id]));
                    }
                    if ($present == 0) {
                        // add the message to the messages list
                        $data = array("Name" => $req->input('future_account_freeze'), "message" => $req->input('message_contents'));
                        array_push($message_content[5]->messages,$data);
                        // return $message_content;
                        DB::connection("mysql2")->table('settings')
                        ->where('keyword', 'Messages')
                        ->update([
                            'value' => $message_content,
                            'date_changed' => date("YmdHis")
                        ]);
                        return redirect(route("customize_sms",[$organization_id]));
                    }
                }else{
                    // create the std class
                    // array("Name" => 'account_freezing', "message" => '');
                    $message = new stdClass();
                    $message->Name = "account_freezing";
                    $message->messages = [array("Name" => 'account_frozen', "message" => ''),array("Name" => 'account_unfrozen', "message" => ''),array("Name" => $req->input('future_account_freeze'), "message" => $req->input('message_contents'))];

                    // then add the rest
                    array_push($message_content,$message);
                    // return $message_content;

                    DB::connection("mysql2")->table('settings')
                    ->where('keyword', 'Messages')
                    ->update([
                        'value' => $message_content,
                        'date_changed' => date("YmdHis")
                    ]);
                    return redirect(route("customize_sms",[$organization_id]));
                }
            }
            // DO NOT ADD MORE FROM THESE AREA.
            // YOU BETTER SELECT A CATEGORY TO ADD TO OR CHANGE HOW THE MESSAGES ARE GOING TO BE RETRIEVED
            // OTHERWISE ITS GOING TO BE MESSY
        }else{
            // is we dont have any data we insert the data
            return redirect(route("customize_sms",[$organization_id]));
        }
    }

    function generate_reports_sms(Request $req, $organization_id){
        // organization_id
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
        $sms_date_option = $req->input("sms_date_option");
        $from_select_date = $req->input("from_select_date");
        $to_select_date = $req->input("to_select_date");
        $select_registration_date = $req->input("select_registration_date");
        $select_user_option = $req->input("select_user_option");
        $client_account = $req->input("client_account");
        $client_phone = $req->input("client_phone");
        $contain_text_option = $req->input("contain_text_option");
        $text_keyword = $req->input("text_keyword");

        // get the sms reports
        $sms_data = [];
        $title = "No data to display";
        if ($contain_text_option == "All") {
            if ($select_user_option == "All") {
                if ($sms_date_option == "select date") {
                    $title = "SMS sent on ".date("D dS M Y",strtotime($select_registration_date));
                    $select_registration_date = date("Ymd",strtotime($select_registration_date));
                    $sms_data = DB::connection("mysql2")->select("SELECT * FROM `sms_tables` WHERE `deleted`= '0' AND `date_sent` LIKE '".$select_registration_date."%' ORDER BY `sms_id` DESC");
                }elseif ($sms_date_option == "between dates") {
                    $title = "SMS sent between ".date("D dS M Y",strtotime($from_select_date))." and ".date("D dS M Y",strtotime($to_select_date));
                    $from = date("YmdHis",strtotime($from_select_date));
                    $to = date("Ymd",strtotime($to_select_date))."235959";
                    $sms_data = DB::connection("mysql2")->select("SELECT * FROM `sms_tables` WHERE `deleted`= '0' AND `date_sent` BETWEEN ? AND ?  ORDER BY `sms_id` DESC",[$from,$to]);
                }else{
                    $title = "All SMS sent";
                    $sms_data = DB::connection("mysql2")->select("SELECT * FROM `sms_tables` ORDER BY `sms_id` DESC");
                }
            }elseif($select_user_option == "specific_user"){
                // get the user data
                $user_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `client_account` = ?",[$client_account]);
                if (count($user_data) < 1) {
                    return "<p style='color:red;'>This account number is invalid</p>";
                }
                $client_name = ucwords(strtolower($user_data[0]->client_name));
                $client_id = $user_data[0]->client_id;
                if ($sms_date_option == "select date") {
                    $title = "SMS sent to ".$client_name." on ".date("D dS M Y",strtotime($select_registration_date));
                    $select_registration_date = date("Ymd",strtotime($select_registration_date));
                    $sms_data = DB::connection("mysql2")->select("SELECT * FROM `sms_tables` WHERE `deleted`= '0' AND `date_sent` LIKE '".$select_registration_date."%' AND `account_id` = ? ORDER BY `sms_id` DESC",[$client_id]);
                }elseif ($sms_date_option == "between dates") {
                    $title = "SMS sent to ".$client_name." between ".date("D dS M Y",strtotime($from_select_date))." to ". date("D dS M Y",strtotime($to_select_date));
                    $from = date("YmdHis",strtotime($from_select_date));
                    $to = date("Ymd",strtotime($to_select_date))."235959";
                    $sms_data = DB::connection("mysql2")->select("SELECT * FROM `sms_tables` WHERE `deleted`= '0' AND `date_sent` BETWEEN ? AND ? AND `account_id` = ? ORDER BY `sms_id` DESC",[$from,$to,$client_id]);
                }else{
                    $title = "All SMS sent to ".$client_name.".";
                    $sms_data = DB::connection("mysql2")->select("SELECT * FROM `sms_tables` WHERE `deleted`= '0' AND `account_id` = ? ORDER BY `sms_id` DESC",[$client_id]);
                }
            }elseif($select_user_option == "specific_user_phone"){
                // get the user data
                if ($sms_date_option == "select date") {
                    $title = "SMS sent to ".$client_phone." on ".date("D dS M Y",strtotime($select_registration_date));
                    $client_phone = $client_phone*=1;
                    $select_registration_date = date("Ymd",strtotime($select_registration_date));
                    $sms_data = DB::connection("mysql2")->select("SELECT * FROM `sms_tables` WHERE `deleted`= '0' AND `date_sent` LIKE '".$select_registration_date."%' AND `recipient_phone` LIKE '%".$client_phone."%' ORDER BY `sms_id` DESC");
                }elseif ($sms_date_option == "between dates") {
                    $title = "SMS sent to ".$client_phone." between ".date("D dS M Y",strtotime($from_select_date))." to ". date("D dS M Y",strtotime($to_select_date));
                    $from = date("YmdHis",strtotime($from_select_date));
                    $to = date("Ymd",strtotime($to_select_date))."235959";
                    $client_phone = $client_phone*=1;
                    $sms_data = DB::connection("mysql2")->select("SELECT * FROM `sms_tables` WHERE `deleted`= '0' AND `date_sent` BETWEEN ? AND ? AND `recipient_phone` LIKE '%".$client_phone."%' ORDER BY `sms_id` DESC",[$from,$to]);
                }else{
                    $title = "All SMS sent to ".$client_phone.".";
                    $client_phone = $client_phone*=1;
                    $sms_data = DB::connection("mysql2")->select("SELECT * FROM `sms_tables` WHERE `deleted`= '0' AND `recipient_phone` LIKE '%".$client_phone."%' ORDER BY `sms_id` DESC");
                }
            }
        }elseif ($contain_text_option == "text_containing") {
            if ($select_user_option == "All") {
                if ($sms_date_option == "select date") {
                    $title = "SMS sent on ".date("D dS M Y",strtotime($select_registration_date));
                    $select_registration_date = date("Ymd",strtotime($select_registration_date));
                    $sms_data = DB::connection("mysql2")->select("SELECT * FROM `sms_tables` WHERE `deleted`= '0' AND `date_sent` LIKE '".$select_registration_date."%' AND `sms_content` LIKE '%".$text_keyword."%' ORDER BY `sms_id` DESC");
                }elseif ($sms_date_option == "between dates") {
                    $title = "SMS sent between ".date("D dS M Y",strtotime($from_select_date))." and ".date("D dS M Y",strtotime($to_select_date));
                    $from = date("YmdHis",strtotime($from_select_date));
                    $to = date("Ymd",strtotime($to_select_date))."235959";
                    $sms_data = DB::connection("mysql2")->select("SELECT * FROM `sms_tables` WHERE `deleted`= '0' AND `date_sent` BETWEEN ? AND ? AND `sms_content` LIKE '%".$text_keyword."%'  ORDER BY `sms_id` DESC",[$from,$to]);
                }else{
                    $title = "All SMS sent";
                    $sms_data = DB::connection("mysql2")->select("SELECT * FROM `sms_tables` WHERE `deleted`= '0' AND `sms_content` LIKE '%".$text_keyword."%' ORDER BY `sms_id` DESC");
                }
            }elseif($select_user_option == "specific_user"){
                // get the user data
                $user_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted`= '0' AND `client_account` = ? ",[$client_account]);
                if (count($user_data) < 1) {
                    return "<p style='color:red;'>This account number is invalid</p>";
                }
                $client_name = ucwords(strtolower($user_data[0]->client_name));
                $client_id = $user_data[0]->client_id;
                if ($sms_date_option == "select date") {
                    $title = "SMS sent to ".$client_name." on ".date("D dS M Y",strtotime($select_registration_date));
                    $select_registration_date = date("Ymd",strtotime($select_registration_date));
                    $sms_data = DB::connection("mysql2")->select("SELECT * FROM `sms_tables` WHERE `deleted`= '0' AND `date_sent` LIKE '".$select_registration_date."%' AND `account_id` = ? AND `sms_content` LIKE '%".$text_keyword."%' ORDER BY `sms_id` DESC",[$client_id]);
                }elseif ($sms_date_option == "between dates") {
                    $title = "SMS sent to ".$client_name." between ".date("D dS M Y",strtotime($from_select_date))." to ". date("D dS M Y",strtotime($to_select_date));
                    $from = date("YmdHis",strtotime($from_select_date));
                    $to = date("Ymd",strtotime($to_select_date))."235959";
                    $sms_data = DB::connection("mysql2")->select("SELECT * FROM `sms_tables` WHERE `deleted`= '0' AND `date_sent` BETWEEN ? AND ? AND `account_id` = ? AND `sms_content` LIKE '%".$text_keyword."%' ORDER BY `sms_id` DESC",[$from,$to,$client_id]);
                }else{
                    $title = "All SMS sent to ".$client_name.".";
                    $sms_data = DB::connection("mysql2")->select("SELECT * FROM `sms_tables` WHERE `deleted`= '0' AND `account_id` = ? AND `sms_content` LIKE '%".$text_keyword."%' ORDER BY `sms_id` DESC",[$client_id]);
                }
            }elseif($select_user_option == "specific_user_phone"){
                // get the user data
                if ($sms_date_option == "select date") {
                    $title = "SMS sent to ".$client_phone." on ".date("D dS M Y",strtotime($select_registration_date));
                    $client_phone = $client_phone*=1;
                    $select_registration_date = date("Ymd",strtotime($select_registration_date));
                    $sms_data = DB::connection("mysql2")->select("SELECT * FROM `sms_tables` WHERE `deleted`= '0' AND `date_sent` LIKE '".$select_registration_date."%' AND `recipient_phone` LIKE '%".$client_phone."%' AND `sms_content` LIKE '%".$text_keyword."%' ORDER BY `sms_id` DESC");
                }elseif ($sms_date_option == "between dates") {
                    $title = "SMS sent to ".$client_phone." between ".date("D dS M Y",strtotime($from_select_date))." to ". date("D dS M Y",strtotime($to_select_date));
                    $from = date("YmdHis",strtotime($from_select_date));
                    $to = date("Ymd",strtotime($to_select_date))."235959";
                    $client_phone = $client_phone*=1;
                    $sms_data = DB::connection("mysql2")->select("SELECT * FROM `sms_tables` WHERE `deleted`= '0' AND `date_sent` BETWEEN ? AND ? AND `recipient_phone` LIKE '%".$client_phone."%' AND `sms_content` LIKE '%".$text_keyword."%' ORDER BY `sms_id` DESC",[$from,$to]);
                }else{
                    $title = "All SMS sent to ".$client_phone.".";
                    $client_phone = $client_phone*=1;
                    $sms_data = DB::connection("mysql2")->select("SELECT * FROM `sms_tables` WHERE `deleted`= '0' AND `recipient_phone` LIKE '%".$client_phone."%' AND `sms_content` LIKE '%".$text_keyword."%' ORDER BY `sms_id` DESC");
                }
            }
            $title.=" containing \"".$text_keyword."\"";
        }
        
        // return $sms_data;
        $new_sms_data = [];
        for ($index=0; $index < count($sms_data); $index++) {
            $data = array(
                ($index+1),
                $sms_data[$index]->sms_content,
                $sms_data[$index]->date_sent,
                $sms_data[$index]->recipient_phone,
            );
            array_push($new_sms_data,$data);
        }

        // print as pdf
        $pdf = new PDF('L','mm',"A4");
        $pdf->set_company_name($organization_details[0]->organization_name);
        $pdf->set_school_contact($organization_details[0]->organization_main_contact);
        $pdf->setHeaderPos(300);
        $pdf->set_document_title($title);
        $pdf->AddPage();
        $pdf->SetFont('Times', 'B', 10);
        $pdf->SetMargins(3,3);
        $pdf->Cell(40, 10, "Statistics", 0, 0, 'L', false);
        $pdf->Ln();
        $pdf->SetFont('Times', 'I', 9);
        $pdf->Cell(40, 5, "SMS Count :", 'B', 0, 'L', false);
        $pdf->Cell(30, 5, count($new_sms_data) . " SMS(es)", 'B', 0, 'L', false);
        $pdf->SetFont('Helvetica', 'BU', 9);
        $pdf->Ln();
        $pdf->Cell(300,8,"SMS Table",0,1,"C",false);
        $pdf->SetFont('Helvetica', 'B', 7);
        $width = array(15,225,30,20);
        $header = array('No','SMS Content','Date Sent','Phone');
        $pdf->smsTable($header,$new_sms_data,$width);
        $pdf->Output("I",$title,false);
    }
}
