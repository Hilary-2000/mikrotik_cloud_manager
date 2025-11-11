<?php

namespace App\Http\Controllers;

use App\Classes\MpesaService;
use App\Classes\reports\PDF;
use App\Classes\routeros_api;
use App\Models\admin_table;
use App\Models\admin_table_mikrotik_cloud;
use App\Models\sms_table;
use DateInterval;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use mysqli;

date_default_timezone_set('Africa/Nairobi');
class Organization extends Controller
{
    //
    function get_organizations(){
        // select from organization.
        $organizations = DB::select("SELECT * FROM `organizations` ORDER BY `organization_id` DESC");
        return view("Orgarnizations.index",["organizations" => $organizations]);
    }

    // update expiration date
    function updateExpDate($organization_id, Request $req)
    {
        // organization id
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization_details) == 0) {
            session()->flash("error", "Invalid organization!");
            return redirect(route("Organizations"));
        }

        // change db
        $change_db = new login();
        $change_db->change_db($organization_details[0]->organization_database);


        $affect_wallet_balance = $req->input("affect_wallet_balance") ?? "off";
        $client_id = $req->input('clients_id');

        $client_tables = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `client_id` = '$client_id' AND `deleted` = '0'");
        $previous_expiry = $client_tables[0]->next_expiration_date*1 > date("YmdHis")*1 ? $client_tables[0]->next_expiration_date : date("YmdHis");
        $wallet_amount = $client_tables[0]->wallet_amount;
        $new_expiration = date("Ymd", strtotime($req->input('expiration_date_edits'))) . str_replace(":", "", $req->input("expiration_time_edits")) . date("s", strtotime($previous_expiry));

        if ($new_expiration*1 > date("YmdHis")) {
            // valid
            if($affect_wallet_balance == "on"){
                // difference ine days
                $per_day_cost = round($client_tables[0]->monthly_payment / 30);
                if ($new_expiration > $previous_expiry) {
                    $date1 = date_create($previous_expiry);
                    $date2 = date_create($new_expiration);
                    $diff = date_diff($date1,$date2);
                    $days =  $diff->format("%a");
                    $wallet_amount -= ($days*$per_day_cost);
                }else{
                    $date1 = date_create($new_expiration);
                    $date2 = date_create($previous_expiry);
                    $diff = date_diff($date1,$date2);
                    $days =  $diff->format("%a");
                    $wallet_amount += ($days*$per_day_cost);
                }
            }
        } else {
            session()->flash("error", "The new expiration date must be greater than today!");
        }

        DB::connection("mysql2")->table('client_tables')
        ->where('client_id', $client_id)
        ->update([
            'next_expiration_date' => $new_expiration,
            'date_changed' => date("YmdHis"),
            'wallet_amount' => $wallet_amount
        ]);

        $client = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `client_id` = '$client_id' AND `deleted` = '0'");
        $client_name = $client[0]->client_name;

        $txt = ":Client ( " . $client_name . " - " . $client[0]->client_account . " ) expiration date changed to " . date("D dS M Y", strtotime($new_expiration)) . "" . "! by " . session('Usernames');
        $this->log($txt);
        // redirect to the client table
        session()->flash("success", "Updates have been done successfully!");
        return redirect(url()->previous());
    }

    // change wallet
    function changeWalletBal($organization_id, Request $req)
    {
        // organization id
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization_details) == 0) {
            session()->flash("error", "Invalid organization!");
            return redirect(route("Organizations"));
        }

        // change db
        $change_db = new login();
        $change_db->change_db($organization_details[0]->organization_database);

        // return $req;
        $client_id = $req->input('clients_id');
        $wallet_amount = $req->input('wallet_amounts');
        DB::connection("mysql2")->table('client_tables')
            ->where('client_id', $client_id)
            ->update([
                'wallet_amount' => $wallet_amount,
                'last_changed' => date("YmdHis"),
                'date_changed' => date("YmdHis")
            ]);

        $client = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `client_id` = '$client_id' AND `deleted` = '0'");
        $client_name = $client[0]->client_name;

        $txt = ":Client ( $client_name ) wallet balance has been changed to Kes $wallet_amount by " . session('Usernames') . "" . "!";
        $this->log($txt);
        // end of log file
        session()->flash("success", "Wallet balance has been successfully changed!");
        return redirect(url()->previous());
    }

    // change_client_monthly_payment
    function change_client_monthly_payment($organization_id, Request $req)
    {
        // organization id
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization_details) == 0) {
            session()->flash("error", "Invalid organization!");
            return redirect(route("Organizations"));
        }

        // change db
        $change_db = new login();
        $change_db->change_db($organization_details[0]->organization_database);


        // GET THE DATA
        $client_id = $req->input('clients_id');
        $client_monthly_payment = $req->input('client_monthly_payment');


        // check if its a valid phone number
        if ($client_monthly_payment <= 0) {
            session()->flash("error", "Monthly Payments cant be less or equals to zero");
            return redirect(url()->previous());
        }

        $client = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `client_id` = '$client_id' AND `deleted` = '0'");

        DB::connection("mysql2")->table('client_tables')
            ->where('client_id', $client_id)
            ->update([
                'monthly_payment' => $client_monthly_payment,
                'last_changed' => date("YmdHis"),
                'date_changed' => date("YmdHis")
            ]);

        $client_name = $client[0]->client_name;
        $monthly_payment = $client[0]->monthly_payment;

        $txt = ":Client ( $client_name ) monthly payment has been changed from (Kes " . number_format($monthly_payment) . ") to (Kes " . number_format($client_monthly_payment) . ") by " . session('Usernames') . "" . "!";
        // $this->log($txt);
        // end of log file
        session()->flash("success", "Client monthly payment has been successfully changed!");
        return redirect(url()->previous());
    }

    function change_phone_number($organization_id, Request $req)
    {
        // organization id
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization_details) == 0) {
            session()->flash("error", "Invalid organization!");
            return redirect(route("Organizations"));
        }

        // change db
        $change_db = new login();
        $change_db->change_db($organization_details[0]->organization_database);


        // GET THE DATA
        $client_id = $req->input('clients_id');
        $client_new_phone = $req->input('client_new_phone');


        // check if its a valid phone number
        if (!ctype_digit($client_new_phone) || (strlen(trim($client_new_phone)) != 10 && strlen(trim($client_new_phone)) != 12)) {
            session()->flash("error", "The phone number given is invalid : Format 0712345678 or 254712345678");
            return redirect(url()->previous());
        }

        $client = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `client_id` = '$client_id' AND `deleted` = '0'");

        DB::connection("mysql2")->table('client_tables')
            ->where('client_id', $client_id)
            ->update([
                'clients_contacts' => $client_new_phone,
                'last_changed' => date("YmdHis"),
                'date_changed' => date("YmdHis")
            ]);

        $client_name = $client[0]->client_name;
        $old_phone = $client[0]->clients_contacts;

        $txt = ":Client ( $client_name ) contact has been changed from (" . $old_phone . ") to (" . $client_new_phone . ") by " . session('Usernames') . "" . "!";
        $this->log($txt);
        // end of log file
        session()->flash("success", "Client contact has been successfully changed!");
        return redirect(url()->previous());
    }

    function log($log_message, $log_subdirectory = null)
    {

        // Log subdirectory
        $log_subdirectory = $log_subdirectory != null ? $log_subdirectory : "";
        $log_subdirectory = strlen($log_subdirectory) > 0 ? (substr($log_subdirectory, -1) == "/" ? $log_subdirectory : $log_subdirectory . "/") : "";

        // Log directory path
        $log_directory = public_path("/logs/" . $log_subdirectory);

        // Create directory if it doesn't exist
        if (!is_dir($log_directory)) {
            mkdir($log_directory, 0755, true); // 0755 is the default permission
        }

        // Log file path
        $log_file_path = $log_directory . session("database_name") . ".txt";

        // Open or create the log file
        $myfile = fopen($log_file_path, "a+") or die("Unable to open file!");

        // Get existing content
        $file_sizes = filesize($log_file_path) > 0 ? filesize($log_file_path) : 8190;
        $existing_txt = fread($myfile, $file_sizes);

        // Write to the log file
        $myfile = fopen($log_file_path, "w") or die("Unable to open file!");
        $date = date("dS M Y (H:i:sa)");

        // this is an extension message to make the investigator know which system was perfoming this action
        $extension_message = $log_subdirectory != null ? " {regular checks}" : "";

        $txt = $date . $log_message . $extension_message . "\n" . $existing_txt;
        fwrite($myfile, $txt);
        fclose($myfile);
    }

    // update minimum payment
    function updateMinPay($organization_id, Request $request)
    {
        // organization id
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization_details) == 0) {
            session()->flash("error", "Invalid organization!");
            return redirect(route("Organizations"));
        }

        // change db
        $change_db = new login();
        $change_db->change_db($organization_details[0]->organization_database);

        // return $request;
        $client_id = $request->input("client_id");
        $change_minimum_payment = $request->input("change_minimum_payment");

        // update the clients minimum pay
        $update = DB::connection("mysql2")->update("UPDATE `client_tables` SET `min_amount` = ? WHERE `client_id` = ?", [$change_minimum_payment, $client_id]);

        // set a success
        session()->flash("success", "Update has been done successfully!");
        return redirect(url()->previous());
    }

    function new_organizations(){
        $organizations = DB::select("SELECT * FROM `organizations` ORDER BY `organization_id` DESC LIMIT 1;");
        $last_acc_no = count($organizations) > 0 ? $organizations[0]->account_no : "N/A";
        return view("Orgarnizations.new",["last_acc_no" => $last_acc_no]);
    }
    
    function DeleteOrganization($organization_id){
        // drop database
        // organization id
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        $organization_name = "N/A";
        if (count($organization_details) == 0) {
            session()->flash("error", "Invalid organization!");
            return redirect(route("Organizations"));
        }

        // if the organization is present delete its database
        DB::statement("DROP DATABASE ".$organization_details[0]->organization_database.";");
        $organization_name = ucwords(strtolower($organization_details[0]->organization_name));
        
        // delete the organization from the table.
        DB::delete("DELETE FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);

        // delete all users associated with the organization
        DB::delete("DELETE FROM `admin_tables` WHERE `organization_id` = ?",[$organization_id]);

        // return to the main page
        session()->flash("success", "Organizations \"".$organization_name."\" have been deleted successfully!");
        return redirect(route("Organizations"));

    }

    function process_new(Request $request){
        // return $request;
        $organization_name = $request->input("organization_name");
        $organization_account = $request->input("organization_account");
        $organization_location = $request->input("organization_location");
        $organization_contacts = $request->input("organization_contacts");
        $organization_email = $request->input("organization_email");
        $admin_name = $request->input("admin_name");
        $client_address = $request->input("client_address");
        $admin_username = $request->input("admin_username");
        $admin_password = $request->input("admin_password");
        $admin_contacts = $request->input("admin_contacts");
        $client_package = $request->input("client_package");
        $privileges = $request->input("privileges");
        $register_main_user = $request->input("register_main_user");
        $administrator_status = $request->input("administrator_status");
        $administrator_email = $request->input("administrator_email");
        $free_trial_period = $request->input("free_trial_period");
        $monthly_payment = $request->input("monthly_payment");
        $registration_date = $request->input("registration_date");

        if ($register_main_user == "on") {
            // check if the administrator data has been set
            if (strlen($admin_username) == 0 || strlen($admin_password) == 0 || strlen($admin_contacts) == 0 || strlen($admin_name) == 0) {
                session()->flash("organization_name",$organization_name);
                session()->flash("organization_account",$organization_account);
                session()->flash("organization_location",$organization_location);
                session()->flash("organization_contacts",$organization_contacts);
                session()->flash("organization_email",$organization_email);
                session()->flash("admin_name",$admin_name);
                session()->flash("client_address",$client_address);
                session()->flash("admin_username",$admin_username);
                session()->flash("admin_password",$admin_password);
                session()->flash("client_package",$client_package);
                session()->flash("admin_contacts",$admin_contacts);
                session()->flash("privileges",$privileges);
    
                // error message
                session()->flash("error","Ensure you have captured the administrator`s Fullname, Username, password and contacts.");
                return redirect(route("NewOrganizations"));
            }
        }
        
        // get the organization details
        $organizations = DB::select("SELECT * FROM `organizations` WHERE `account_no` = ?",[$organization_account]);
        if (count($organizations) > 0) {
            session()->flash("organization_name",$organization_name);
            session()->flash("organization_account",$organization_account);
            session()->flash("organization_location",$organization_location);
            session()->flash("organization_contacts",$organization_contacts);
            session()->flash("organization_email",$organization_email);
            session()->flash("admin_name",$admin_name);
            session()->flash("client_address",$client_address);
            session()->flash("admin_username",$admin_username);
            session()->flash("admin_password",$admin_password);
            session()->flash("client_package",$client_package);
            session()->flash("admin_contacts",$admin_contacts);
            session()->flash("privileges",$privileges);

            // error message
            session()->flash("error","Duplicate account number!");
            return redirect(route("NewOrganizations"));
        }

        // import the database for the new user first
        $filePath = public_path("db_imports/imports.sql");
        $dbname = $organization_account;
        $done = $this->import_database($filePath,$dbname);

        // if not done return error
        if(!$done){
            session()->flash("organization_name",$organization_name);
            session()->flash("organization_account",$organization_account);
            session()->flash("organization_location",$organization_location);
            session()->flash("organization_contacts",$organization_contacts);
            session()->flash("organization_email",$organization_email);
            session()->flash("admin_name",$admin_name);
            session()->flash("client_address",$client_address);
            session()->flash("admin_username",$admin_username);
            session()->flash("admin_password",$admin_password);
            session()->flash("client_package",$client_package);
            session()->flash("admin_contacts",$admin_contacts);
            session()->flash("privileges",$privileges);

            // error message
            session()->flash("error","Can`t create user`s account! Contact administrator");
            return redirect(route("NewOrganizations"));
        }

        if ($register_main_user == "on") {
            // check the credentials of the administrator is used
            $admin_data = DB::select("SELECT * FROM `admin_tables` WHERE `admin_username` = '$admin_username' AND `deleted` = '0'");
            if (count($admin_data) > 0) {
                session()->flash("organization_name",$organization_name);
                session()->flash("organization_account",$organization_account);
                session()->flash("organization_location",$organization_location);
                session()->flash("organization_contacts",$organization_contacts);
                session()->flash("organization_email",$organization_email);
                session()->flash("admin_name",$admin_name);
                session()->flash("client_address",$client_address);
                session()->flash("admin_username",$admin_username);
                session()->flash("admin_password",$admin_password);
                session()->flash("client_package",$client_package);
                session()->flash("admin_contacts",$admin_contacts);
                session()->flash("privileges",$privileges);
    
                // error message
                session()->flash("error","Use another username for the user - (Phone number is recommended)!");
                return redirect(route("NewOrganizations"));
            }
        }

        // proceed and insert the organization details
        $status = "1";
        $free_trial_period = $request->input("free_trial_period");
        $monthly_payment = $request->input("monthly_payment");
        $registration_date = date("Ymd", strtotime($request->input("registration_date"))).date("His");

        // add free trial period.
        $expiry_date = $this->addMonths($registration_date, explode(" ",$free_trial_period)[0]);
        $insert_org = DB::insert("INSERT INTO `organizations` (`organization_name`,`organization_address`,`organization_main_contact`,`organization_email`,`organization_database`,`account_no`,`free_trial_period`, `monthly_payment`, `date_joined`, `organization_status`, `expiry_date`)
                                VALUES (?,?,?,?,?,?,?,?,?,?,?)",[$organization_name, $organization_location, $organization_contacts, $organization_email, $organization_account, $organization_account, $free_trial_period, $monthly_payment, $registration_date, $status, $expiry_date]);
                                

        if ($register_main_user == "on") {
            // organization details
            $organization_data = DB::select("SELECT * FROM `organizations` WHERE `account_no` = ? ",[$organization_account]);
    
            // organization id
            $org_id = count($organization_data) > 0 ? $organization_data[0]->organization_id : null;
    
            if ($org_id == null) {
                session()->flash("error","Error has occured!, Can`t add a new user!");
                return redirect(route("NewOrganizations"));
            }
    
            // proceed and add the new user
            $admin_table = new admin_table();
            $admin_table->admin_fullname = $admin_name;
            $admin_table->admin_username = $admin_username;
            $admin_table->admin_password = $admin_password;
            $admin_table->contacts = $admin_contacts;
            $admin_table->organization_id = $org_id;
            $admin_table->user_status = $administrator_status;
            $admin_table->activated = $administrator_status;
            $admin_table->email = $administrator_email;
            $admin_table->priviledges = $privileges;
            $admin_table->save();
        }

        // return the success message and redirect to the main page
        session()->flash("success","New organization \"".ucwords(strtolower($organization_name))."\" has been successfully registered!");
        return redirect(route("Organizations"));
    }

    // get expiry date of the organization
    function get_expiry($organization_id){
        // organization id
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization_details) == 0) {
            return ["success" => false,"date" => null, "expiry_date" => null, "reason" => "Invalid Organization!"];
        }

        // get the organization package
        $package_used = DB::select("SELECT * FROM `packages` WHERE `package_id` = ?",[$organization_details[0]->package_name]);
        if (count($package_used) == 0) {
            return ["success" => false,"date" => null,"expiry_date" => null,"reason" => "Invalid Package!"];
        }

        // get the date of expiry
        $last_renewal_date = $organization_details[0]->expiry_date;
        $package_period = $package_used[0]->package_period;
        $add_date = $this->addPeriodToDate($last_renewal_date,$package_period);
        
        return ["success" => true, "date" => $add_date, "expiry_date" => $last_renewal_date, "reason" => ""];
    }

    function view_organization($organization_id){
        // organization id
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization_details) == 0) {
            session()->flash("error", "Invalid organization!");
            return redirect(route("Organizations"));
        }

        // change db
        $change_db = new login();
        $change_db->change_db($organization_details[0]->organization_database);

        // GET THE SMS API LINK
        $select = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `keyword` = 'sms_sender'");
        $sms_sender = count($select) > 0 ? $select[0]->value : "";
        $organization_details[0]->sms_sender = $sms_sender;

        // GET THE SMS API LINK
        $select = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `keyword` = 'sms_api_key'");
        $sms_api_key = count($select) > 0 ? $select[0]->value : "";
        $organization_details[0]->sms_api_key = $sms_api_key;

        // GET THE SMS API LINK
        $select = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `keyword` = 'sms_partner_id'");
        $sms_partner_id = count($select) > 0 ? $select[0]->value : "";
        $organization_details[0]->sms_partner_id = $sms_partner_id;

        // GET THE SMS API LINK
        $select = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `keyword` = 'sms_shortcode'");
        $sms_shortcode = count($select) > 0 ? $select[0]->value : "";
        $organization_details[0]->sms_shortcode = $sms_shortcode;

        // GET THE SMS API LINK
        $select = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `keyword` = 'consumer_key'");
        $consumer_key = count($select) > 0 ? $select[0]->value : "";
        $organization_details[0]->consumer_key = $consumer_key;

        // GET THE SMS API LINK
        $select = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `keyword` = 'consumer_secret'");
        $consumer_secret = count($select) > 0 ? $select[0]->value : "";
        $organization_details[0]->consumer_secret = $consumer_secret;

        // GET THE SMS API LINK
        $select = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `keyword` = 'passkey'");
        $passkey = count($select) > 0 ? $select[0]->value : "";
        $organization_details[0]->passkey = $passkey;
        // return $organization_details[0];

        // get the rest of the account details
        $account_users = DB::select("SELECT * FROM `admin_tables` WHERE `organization_id` = ?",[$organization_id]);

        // get the company stats
        $change_db = new login();
        $change_db->change_db($organization_details[0]->organization_database);

        // get the client counts
        $clients = DB::connection("mysql2")->select("SELECT COUNT(*) AS 'total' FROM `client_tables`;");
        $client_count = count($clients) > 0 ? $clients[0]->total : 0;

        // get the transaction done
        $transaction = DB::connection("mysql2")->select("SELECT COUNT(*) AS 'total' FROM `transaction_tables`;");
        $transaction_count = count($transaction) > 0 ? $transaction[0]->total : 0;

        // get the routers
        $routers = DB::connection("mysql2")->select("SELECT COUNT(*) AS 'total' FROM `remote_routers`;");
        $routers_count = count($routers) > 0 ? $routers[0]->total : 0;

        // get the smses sent
        $sms = DB::connection("mysql2")->select("SELECT COUNT(*) AS 'total' FROM `sms_tables`;");
        $sms_count = count($sms) > 0 ? $sms[0]->total : 0;

        // get administrator
        $administrators = DB::select("SELECT * FROM `admin_tables` WHERE `organization_id` = ?",[$organization_id]);
        $administrator_count = count($administrators);
        
        // get this months payment
        $clients_monthly = DB::connection("mysql2")->select("SELECT COUNT(*) AS 'total' FROM `client_tables` WHERE next_expiration_date > ?;",[date("Ym", strtotime("-3 months"))."010000"]);
        $monthly_payment = count($clients_monthly) > 50 ? $clients_monthly[0]->total*20 : 1000;

        return view("Orgarnizations.view",["clients_monthly" => $clients_monthly, "monthly_payment" => $monthly_payment, "administrator_count" => $administrator_count, "transaction_count" => $transaction_count, "routers_count" => $routers_count, "sms_count" => $sms_count, "client_count" => $client_count, "organization_details" => $organization_details[0], "account_users" => $account_users]);
    }

    function getClientsDatatable(Request $request, $organization_id){
        // organization id
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization_details) == 0) {
            session()->flash("error", "Invalid organization!");
            return redirect(route("Organizations"));
        }

        // change db
        $change_db = new login();
        $change_db->change_db($organization_details[0]->organization_database);

        // return $request->input();
        $order_by = !empty($request->input('order.0.column')) ? $request->input('order.0.column') : 0;
        $order_dir = !empty($request->input('order.0.dir')) ? $request->input('order.0.dir') : 'desc';
        $order_string = "ORDER BY ".($request->input('columns.'.$order_by.'.data') == "rownum" ? "client_tables.client_id" : "client_tables.".$request->input('columns.'.$order_by.'.data')).' '.$order_dir;
        // return $order_string;
        $start  = $request->input('start');
        $length = $request->input('length');
        $accepted_columns = ["client_id","validated","client_name","client_network","client_status","clients_contacts","client_address","monthly_payment","next_expiration_date","router_name","wallet_amount","client_account","reffered_by","comment","location_coordinates","assignment","client_default_gw"];
        $str = "";
        $str_router_filter = "";
        foreach ($request->all() as $key => $value) {
            if (in_array($key, $accepted_columns) && (!empty($value) || $value == "0")) {
                if($key == "client_name" || $key == "client_account" || $key == "clients_contacts" || $key == "client_address" || $key == "reffered_by" || $key == "comment" || $key == "location_coordinates"){
                    $str.= " AND client_tables.$key LIKE '%$value%' ";
                }else{
                    $str.= " AND client_tables.$key = '$value' ";
                }
            }

            if($key == "router_name" && !empty($value)){
                $str_router_filter .= "WHERE client_tables.$key = '$value'";
            }
        }
        // return $str;

        if(!empty($request->input("search.value"))){
            $search = $request->input("search.value");
            $str.= " AND (client_tables.client_name LIKE '%$search%' OR client_tables.client_account LIKE '%$search%' OR client_tables.clients_contacts LIKE '%$search%' OR client_tables.client_address LIKE '%$search%' OR client_tables.comment LIKE '%$search%' OR remote_routers.router_name LIKE '%$search%' OR client_tables.client_network LIKE '%$search%' OR client_tables.client_default_gw LIKE '%$search%') ";
        }

        // get the total clients count
        $client_count = DB::connection("mysql2")->select("SELECT COUNT(*) AS total_clients FROM `client_tables` LEFT JOIN `remote_routers` ON remote_routers.router_id = client_tables.router_name WHERE client_tables.deleted = '0' $str;");
        $total_clients = count($client_count) > 0 ? $client_count[0]->total_clients : 0;
        $all_clients = DB::connection("mysql2")->select("SELECT COUNT(*) AS total_clients FROM `client_tables` $str_router_filter");
        $all_clients = count($all_clients) > 0 ? $all_clients[0]->total_clients : 0;
        
        // here we get the clients information from the database
        $client_data = DB::connection("mysql2")->select("SELECT client_tables.last_seen, client_tables.client_id,client_tables.validated,client_tables.client_name,client_tables.client_network,client_tables.client_status,client_tables.clients_contacts,client_tables.client_address,client_tables.monthly_payment,client_tables.next_expiration_date,client_tables.payments_status,client_tables.router_name,client_tables.wallet_amount,client_tables.client_account,client_tables.reffered_by,client_tables.comment,client_tables.location_coordinates,client_tables.assignment,client_tables.client_default_gw,
        (SELECT report_title FROM `client_reports` WHERE client_id = client_tables.client_id ORDER BY report_date DESC LIMIT 1) AS 'latest_issue', 
        (SELECT report_description FROM `client_reports` WHERE client_id = client_tables.client_id ORDER BY report_date DESC LIMIT 1) AS 'report_description',
        (SELECT problem FROM `client_reports` WHERE client_id = client_tables.client_id ORDER BY report_date DESC LIMIT 1) AS 'problem', 
        (SELECT solution FROM `client_reports` WHERE client_id = client_tables.client_id ORDER BY report_date DESC LIMIT 1) AS 'solution', 
        (SELECT diagnosis FROM `client_reports` WHERE client_id = client_tables.client_id ORDER BY report_date DESC LIMIT 1) AS 'diagnosis',
        (SELECT report_date FROM `client_reports` WHERE client_id = client_tables.client_id ORDER BY report_date DESC LIMIT 1) AS 'date_reported',
        (SELECT report_code FROM `client_reports` WHERE client_id = client_tables.client_id ORDER BY report_date DESC LIMIT 1) AS 'ticket_number',
        (SELECT `status` FROM `client_reports` WHERE client_id = client_tables.client_id ORDER BY report_date DESC LIMIT 1) AS 'report_status',
        (SELECT `report_id` FROM `client_reports` WHERE client_id = client_tables.client_id ORDER BY report_date DESC LIMIT 1) AS 'report_id',
        (SELECT (SELECT admin_tables.admin_fullname FROM ".session("database_name").".client_reports LEFT JOIN mikrotik_cloud_manager.admin_tables ON admin_tables.admin_id = client_reports.admin_reporter WHERE client_reports.report_id = CR.report_id LIMIT 1) AS admin_fullname FROM `client_reports` AS CR WHERE client_id = client_tables.client_id ORDER BY report_date DESC LIMIT 1) AS 'opened_by',
        (SELECT (SELECT admin_tables.admin_fullname FROM ".session("database_name").".client_reports LEFT JOIN mikrotik_cloud_manager.admin_tables ON admin_tables.admin_id = client_reports.closed_by WHERE client_reports.report_id = CR.report_id LIMIT 1) AS admin_fullname FROM `client_reports` AS CR WHERE client_id = client_tables.client_id ORDER BY report_date DESC LIMIT 1) AS 'closed_by',
        (SELECT `admin_attender` FROM `client_reports` WHERE client_id = client_tables.client_id ORDER BY report_date DESC LIMIT 1) AS 'admin_attender',
        remote_routers.router_name
         FROM `client_tables`
         LEFT JOIN `remote_routers` ON remote_routers.router_id = client_tables.router_name
         WHERE client_tables.deleted = '0' $str $order_string LIMIT $start, $length;");
        // return $client_data; 

        $data = [];
        foreach ($client_data as $i => $client) {
            $online = date("YmdHis", strtotime("-2 minutes")) < $client->last_seen;
            $data[] = [
                "rownum"      => '<input type="checkbox" class="actions_id d-none" id="actions_id_'.$client->client_account.'"><input type="hidden" id="actions_value_'.$client->client_account.'" value="'.$client->client_account.'"> '.($start + $i + 1),
                "client_name"   => ($client->assignment == "static" ? '<span class="badge text-light" style="background: rgb(141, 110, 99);" data-toggle="tooltip" title="" data-original-title="Static Assigned">S</span>':'<span class="badge text-light" style="background: rgb(119, 105, 183);" data-toggle="tooltip" title="" data-original-title="PPPoE Assigned">P</span>').' <a href="/Organization/ViewClient/'.$organization_id.'/'.$client->client_id.'" class="text-secondary" data-toggle="tooltip" title="View this client!">'.(ucwords(strtolower($client->client_name))).'</a> <span class="badge badge-'.($client->client_status == "1" ? "success" : "danger").'"> </span>'.("<br><small>".$client->comment."</small>"),
                "client_account"     => ($client->client_account).($online ? " <span class='badge bg-success fa-beat-fade' style='font-size:7px;'>Online</span>" : " <small class='badge bg-danger' style='font-size:7px;'>Offline</small>")."<br><small>".($client->clients_contacts ?? '{No contact}')."</small>",
                "client_address"    => ucwords(strtolower($client->client_address)).($client->location_coordinates ? '<small class="d-none d-md-block"><a class="text-danger" href="https://www.google.com/maps/place/'.$client->location_coordinates.'" target="_blank"><u>Locate Client</u> </a></small>' : ''),
                "next_expiration_date"    => (date("D d M Y @ H:i:s", strtotime($client->next_expiration_date))),
                "client_default_gw"  => "<small>".($client->assignment == "static" ? ("<span class='badge bg-success text-dark'><b>GW : </b>".$client->client_default_gw."</span> <br><span class='badge bg-success text-dark'><b>NW : </b>". $client->client_network."</span><br>") : "")." <span class='badge bg-primary'><b>Router: </b>".($client->router_name ?? '{No queues router}')."</span>"."</small>",
                "actions"     => //'<a href="/Clients/View/'.$client->client_id.'" class="btn btn-primary btn-sm" data-toggle="tooltip" title="View this client!"><i class="ft-eye"></i></a>'
                                '<a href="/Organization/ViewClient/'.$organization_id.'/'.$client->client_id.'" class="btn btn-sm btn-primary text-bolder " data-toggle="tooltip" title="" style="padding: 3px; background-color: rgb(105, 103, 206); transition: background-color 0.3s;" id="" data-original-title="View this client"><span class="d-inline-block border border-white w-100 text-center" style="border-radius: 2px; padding: 5px; background-color: rgba(0, 0, 0, 0); color: rgb(255, 255, 255); border-color: rgb(255, 255, 255); transition: color 0.3s, background-color 0.3s, border-color 0.3s;"><i class="ft-eye"></i></span></a>'
            ];
        }

         $json_data = [
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => $all_clients,
            "recordsFiltered" => $total_clients,
            "data"            => $data
        ];

        return response()->json($json_data);
    }

    function view_organization_clients($organization_id){
        // organization id
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization_details) == 0) {
            session()->flash("error", "Invalid organization!");
            return redirect(route("Organizations"));
        }

        // change db
        $change_db = new login();
        $change_db->change_db($organization_details[0]->organization_database);

        $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' ORDER BY `client_id` DESC;");
        $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers`");
        // return $client_data;
        // get all the clients that have been frozen
        $frozen_clients = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `client_freeze_status` = '1'");
        for ($index=0; $index < count($frozen_clients); $index++) {
            // get difference in todays date and the day selected
            $date_today = date_create(date("Ymd"));
            // return $date_today;
            $days = "Indefinite";
            if (strlen($frozen_clients[$index]->client_freeze_untill) > 0 && $frozen_clients[$index]->client_freeze_untill !== "00000000000000") {
                // return $frozen_clients[$index]->client_freeze_untill;
                $selected_date = date_create($frozen_clients[$index]->client_freeze_untill);
                $diff=date_diff($date_today,$selected_date);
                $days = $diff->format("%a Days");
            }

            $frozen_clients[$index]->freeze_days_left = $days;
        }
        // return $frozen_clients;
        for ($index=0; $index < count($client_data); $index++) { 
            $client_data[$index]->reffered_by = str_replace("'","\"",$client_data[$index]->reffered_by);
        }
        
        return view('Orgarnizations.organization_clients',["organization_details" => $organization_details[0],"frozen_clients" => $frozen_clients,'client_data'=>$client_data,"router_infor" => $router_data]);
    }

    function deactivate_payment($organization_id, $userid){
        // organization id
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization_details) == 0) {
            session()->flash("error", "Invalid organization!");
            return redirect(route("Organizations"));
        }

        // change db
        $change_db = new login();
        $change_db->change_db($organization_details[0]->organization_database);

        // update the payment information
        DB::connection("mysql2")->table('client_tables')
        ->where('client_id', $userid)
        ->update([
            'payments_status' => "0",
            'date_changed' => date("YmdHis")
        ]);
        $client = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `client_id` = '$userid' AND `deleted` = '0'");
        $client_name = $client[0]->client_name;

        // log message
        $txt = ":Client ( $client_name ) pay status has been changed to In-active by ".session('Usernames');
        // // $this->log($txt);
        // end of log file
        session()->flash("success","User payment automation has been successfully de-activated");
        return redirect(route("viewOrganizationClient",[$organization_id,$userid]));
    }
    function activate_payment($organization_id,$userid){
        // organization id
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization_details) == 0) {
            session()->flash("error", "Invalid organization!");
            return redirect(route("Organizations"));
        }

        // change db
        $change_db = new login();
        $change_db->change_db($organization_details[0]->organization_database);

        // update the payment information
        DB::connection("mysql2")->table('client_tables')
        ->where('client_id', $userid)
        ->update([
            'payments_status' => "1",
            'date_changed' => date("YmdHis")
        ]);
        $client = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `client_id` = '$userid' AND `deleted` = '0'");
        $client_name = $client[0]->client_name;

        // log message
        $txt = ":Client ( $client_name ) pay status has been changed to active by ".session('Usernames');
        // $this->log($txt);
        // end of log file
        session()->flash("success","User payment automation has been successfully Activated");
        return redirect(route("viewOrganizationClient",[$organization_id,$userid]));
    }

    function deactivate_client($organization_id,$clientid){
        // organization id
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization_details) == 0) {
            session()->flash("error", "Invalid organization!");
            return redirect(route("Organizations"));
        }
        $userid = $clientid;

        // change db
        $change_db = new login();
        $change_db->change_db($organization_details[0]->organization_database);
        $database_name = $organization_details[0]->organization_database;

        // get the user router and update the setting
        $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `client_id` = '$userid' AND `deleted` = '0'");
        if (count($client_data) > 0) {
            if ($client_data[0]->assignment == "static") {
                $router_id = $client_data[0]->router_name;
                // connect to the router and deactivate the client address
                $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `router_id` = '$router_id' AND `deleted` = '0'");

                // get the sstp credentails they are also the api usernames
                $sstp_username = $router_data[0]->sstp_username;
                $sstp_password = $router_data[0]->sstp_password;
                $api_port = $router_data[0]->api_port;
                

                // connect to the router and set the sstp client
                $sstp_value = $this->getSSTPAddress($database_name);
                if ($sstp_value == null) {
                    if (session('Usernames')) {
                        $error = "The SSTP server is not set, Contact your administrator!";
                        session()->flash("error",$error);
                        return redirect(url()->previous());
                    }else{
                        return "";
                    }
                }

                // connect to the router and set the sstp client
                $server_ip_address = $sstp_value->ip_address;
                $user = $sstp_value->username;
                $pass = $sstp_value->password;
                $port = $sstp_value->port;

                // check if the router is actively connected
                $client_router_ip = $this->checkActive($server_ip_address,$user,$pass,$port,$sstp_username);

                if ($client_router_ip == null) {
                    if (session('Usernames')) {
                        $error = "Your router is not active, Restart it and try again!";
                        session()->flash("error",$error);
                        return redirect(url()->previous());
                    }else{
                        // update the user data to de-activated
                        DB::connection("mysql2")->table('client_tables')
                        ->where('client_id', $userid)
                        ->update([
                            'client_status' => "0",
                            'date_changed' => date("YmdHis")
                        ]);
                        return "";
                    }
                }
        
                // create the router os api
                $API = new routeros_api();
                $API->debug = false;

                // create connection
                if ($API->connect($client_router_ip,$sstp_username,$sstp_password,$api_port)) {
                    // get the IP ADDRES
                    $curl_handle = curl_init();
                    $url = "https://crontab.hypbits.com/getIpaddress.php?db_name=".$database_name."&r_id=".$router_id."&r_ip=true";
                    curl_setopt($curl_handle, CURLOPT_URL, $url);
                    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
                    $curl_data = curl_exec($curl_handle);
                    curl_close($curl_handle);
                    $router_ip_addresses = json_decode($curl_data, true);
                    // save the router ip address
                    $ip_addresses = $router_ip_addresses;
                    
                    // loop through the ip addresses and get the clents ip address id
                    $client_network = $client_data[0]->client_network;
                    $present = 0;
                    $ip_id = "";
                    foreach ($ip_addresses as $key => $value) {
                        foreach ($value as $key1 => $value1) {
                            if ($key1 == ".id") {
                                $ip_id = $value1;
                            }
                            if ($value1 == $client_network) {
                                $present = 1;
                                break;
                            }
                        }
                        if ($present == 1) {
                            break;
                        }
                    }
                    

                    // deactivate the id
                    if (strlen($ip_id) > 0) {
                        // deactivate
                        $deactivate = $API->comm("/ip/address/set", array(
                            "disabled" => "true",
                            ".id" => $ip_id
                        ));
                        // update the user data to de-activated
                        DB::connection("mysql2")->table('client_tables')
                        ->where('client_id', $userid)
                        ->update([
                            'client_status' => "0",
                            'date_changed' => date("YmdHis")
                        ]);

                        // log message
                        $txt = ":Client (".$client_data[0]->client_name.") deactivated by ".(session('Usernames') ? session('Usernames'):"System");
                        // $this->log($txt);

                        // end of log file
                        if (session('Usernames')) {
                            session()->flash("success","User has been successfully deactivated");
                            return redirect(route("viewOrganizationClient",[$organization_details[0]->organization_id,$userid]));
                        }else{
                            return "";
                        }
                    }else {
                        if (session('Usernames')) {
                            session()->flash("error","The user ip address not found in the router address list");
                            return redirect(route("viewOrganizationClient",[$organization_details[0]->organization_id,$userid]));
                        }else{
                            return "";
                        }
                    }
                }else {
                    // update the user data to de-activated
                    DB::connection("mysql2")->table('client_tables')
                    ->where('client_id', $userid)
                    ->update([
                        'client_status' => "0",
                        'date_changed' => date("YmdHis")
                    ]);

                    // redirect
                    if (session('Usernames')) {
                        session()->flash("error","Cannot connect to the router!");
                        return redirect(route("viewOrganizationClient",[$organization_details[0]->organization_id,$userid]));
                    }else{
                        return "";
                    }
                }
            }elseif ($client_data[0]->assignment == "pppoe") {
                // disable the client secret and remove the client from active connections
                $router_id = $client_data[0]->router_name;
                // connect to the router and deactivate the client address
                $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `router_id` = '$router_id' AND `deleted` = '0'");
                
                if (count($router_data) == 0) {
                    if (session('Usernames')) {
                        $error = "Router that the client is connected to is not present!";
                        session()->flash("error",$error);
                        return redirect(url()->previous());
                    }else{
                        // update the user data to de-activated
                        DB::connection("mysql2")->table('client_tables')
                        ->where('client_id', $userid)
                        ->update([
                            'client_status' => "0",
                            'date_changed' => date("YmdHis")
                        ]);
                        return "";
                    }
                }
        
                // get the sstp credentails they are also the api usernames
                $sstp_username = $router_data[0]->sstp_username;
                $sstp_password = $router_data[0]->sstp_password;
                $api_port = $router_data[0]->api_port;
                

                // connect to the router and set the sstp client
                $sstp_value = $this->getSSTPAddress($database_name);
                if ($sstp_value == null) {
                    if (session('Usernames')) {
                        $error = "The SSTP server is not set, Contact your administrator!";
                        session()->flash("error",$error);
                        return redirect(url()->previous());
                    }else{
                        return "";
                    }
                }

                // connect to the router and set the sstp client
                $server_ip_address = $sstp_value->ip_address;
                $user = $sstp_value->username;
                $pass = $sstp_value->password;
                $port = $sstp_value->port;

                // check if the router is actively connected
                $client_router_ip = $this->checkActive($server_ip_address,$user,$pass,$port,$sstp_username);

                if ($client_router_ip == null) {
                    if (session('Usernames')) {
                        $error = "Your router is not active, Restart it and try again!";
                        session()->flash("error",$error);
                        return redirect(url()->previous());
                    }else{
                        return "";
                    }
                }
        
                // client secret name 
                $secret_name = $client_data[0]->client_secret;
                // create the router os api
                $API = new routeros_api();
                $API->debug = false;
                if ($API->connect($client_router_ip,$sstp_username,$sstp_password,$api_port)){

                    // get the IP ADDRES
                    $curl_handle = curl_init();
                    $url = "https://crontab.hypbits.com/getIpaddress.php?db_name=".$database_name."&r_id=".$router_id."&r_active_secrets=true";
                    curl_setopt($curl_handle, CURLOPT_URL, $url);
                    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
                    $curl_data = curl_exec($curl_handle);
                    curl_close($curl_handle);
                    $active_connections = json_decode($curl_data, true);

                    // get the IP ADDRES
                    $curl_handle = curl_init();
                    $url = "https://crontab.hypbits.com/getIpaddress.php?db_name=".$database_name."&r_id=".$router_id."&r_ppoe_secrets=true";
                    curl_setopt($curl_handle, CURLOPT_URL, $url);
                    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
                    $curl_data = curl_exec($curl_handle);
                    curl_close($curl_handle);
                    $router_secrets = json_decode($curl_data, true);

                    // loop through the secrets get the id and use it to disable the secret
                    $secret_id = "0";
                    for ($indexes=0; $indexes < count($router_secrets); $indexes++) { 
                        $secrets = $router_secrets[$indexes];
                        if ($secrets['name'] == $secret_name) {
                            // loop through and pull the id we will use to disable the secret
                            foreach ($secrets as $key => $value) {
                                if ($key == ".id") {
                                    $secret_id = $value;
                                    break;
                                }
                            }
                        }
                    }
                    $API->comm("/ppp/secret/set", array(
                        "disabled" => "true",
                        ".id" => $secret_id
                    ));
                    $active_id = "0";
                    // loop through the active connections and drop the users active connection
                    for ($index=0; $index < count($active_connections); $index++) { 
                        $actives = $active_connections[$index];
                        if ($actives['name'] == $secret_name) {
                            foreach ($actives as $key => $value) {
                                if ($key == ".id") {
                                    $active_id = $value;
                                }
                            }
                        }
                    }

                    // remove the active connection if there is, it will do nothing if the id is not present
                    $API->comm("/ppp/active/remove", array(
                        ".id" => $active_id
                    ));

                    // uodate the database
                    // update the user data to de-activated
                    DB::connection("mysql2")->table('client_tables')
                    ->where('client_id', $userid)
                    ->update([
                        'client_status' => "0",
                        'date_changed' => date("YmdHis")
                    ]);

                    // log message
                    $txt = ":Client (".$client_data[0]->client_name.") deactivated by ".(session('Usernames') ? session('Usernames'):"System");
                    // $this->log($txt);
                    // end of log file
                    if (session('Usernames')) {
                        session()->flash("success","User has been successfully deactivated");
                        return redirect(route("viewOrganizationClient",[$organization_details[0]->organization_id,$userid]));
                    }else{
                        return "";
                    }
                }else {
                    // update the user data to de-activated
                    DB::connection("mysql2")->table('client_tables')
                    ->where('client_id', $userid)
                    ->update([
                        'client_status' => "0",
                        'date_changed' => date("YmdHis")
                    ]);

                    // update the user data to deactivate
                    if (session('Usernames')) {
                        session()->flash("error","Cannot connect to the router!");
                        return redirect(route("viewOrganizationClient",[$organization_details[0]->organization_id,$userid]));
                    }else{
                        return "";
                    }
                }
            }
        }else {
            if (session('Usernames')) {
                session()->flash("error","Client not found!");
                return redirect(route("viewOrganizationClients",[$organization_id]));
            }else{
                return "";
            }
        }
    }

    // activate the user
    function activate_client($organization_id, $clientid){
        // organization id
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization_details) == 0) {
            session()->flash("error", "Invalid organization!");
            return redirect(route("Organizations"));
        }
        $userid = $clientid;

        // change db
        $change_db = new login();
        $change_db->change_db($organization_details[0]->organization_database);
        $database_name = $organization_details[0]->organization_database;

        /*****starts here */
        // get the user router and update the setting
        $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `client_id` = '$userid' AND `deleted` = '0'");
        if (count($client_data) > 0) {
            if ($client_data[0]->assignment == "static") {
                $router_id = $client_data[0]->router_name;
                // connect to the router and deactivate the client address
                $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `router_id` = '$router_id' AND `deleted` = '0'");
                if (count($router_data) == 0) {
                    if (session('Usernames')) {
                        $error = "The router the client is connected to is not present!";
                        session()->flash("error",$error);
                        return redirect(url()->previous());
                    }else{
                        return ["success" => false, "message" => $client_data[0]->client_name." : The router the client is connected to is not present!"];
                    }
                }
        
                // get the sstp credentails they are also the api usernames
                $sstp_username = $router_data[0]->sstp_username;
                $sstp_password = $router_data[0]->sstp_password;
                $api_port = $router_data[0]->api_port;
                

                // connect to the router and set the sstp client
                $sstp_value = $this->getSSTPAddress($database_name);
                if ($sstp_value == null) {
                    if (session('Usernames')) {
                        $error = "The SSTP server is not set, Contact your administrator!";
                        session()->flash("error",$error);
                        return redirect(url()->previous());
                    }else{
                        return ["success" => false, "message" => $client_data[0]->client_name." : The SSTP server is not set, Contact your administrator!"];
                    }
                }

                // connect to the router and set the sstp client
                $server_ip_address = $sstp_value->ip_address;
                $user = $sstp_value->username;
                $pass = $sstp_value->password;
                $port = $sstp_value->port;

                // check if the router is actively connected
                $client_router_ip = $this->checkActive($server_ip_address,$user,$pass,$port,$sstp_username);

                if ($client_router_ip == null) {
                    if (session('Usernames')) {
                        $error = "Your router is not active, Restart it and try again!";
                        session()->flash("error",$error);
                        return redirect(url()->previous());
                    }else{
                        return ["success" => false, "message" => $client_data[0]->client_name." : Your router is not active, Restart it and try again!"];
                    }
                }
        
                // create the router os api
                $API = new routeros_api();
                $API->debug = false;
                // create connection
        
                if ($API->connect($client_router_ip,$sstp_username,$sstp_password,$api_port)) {
                    // get the IP ADDRES
                    $curl_handle = curl_init();
                    $url = "https://crontab.hypbits.com/getIpaddress.php?db_name=".session("database_name")."&r_id=".$router_id."&r_ip=true";
                    curl_setopt($curl_handle, CURLOPT_URL, $url);
                    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
                    $curl_data = curl_exec($curl_handle);
                    curl_close($curl_handle);
                    $router_ip_addresses = json_decode($curl_data, true);
                    // save the router ip address
                    $ip_addresses = $router_ip_addresses;

                    // loop through the ip addresses and get the clents ip address id
                    $client_network = $client_data[0]->client_network;
                    $present = 0;
                    $ip_id = "";
                    foreach ($ip_addresses as $key => $value) {
                        foreach ($value as $key1 => $value1) {
                            if ($key1 == ".id") {
                                $ip_id = $value1;
                            }
                            if ($value1 == $client_network) {
                                $present = 1;
                                break;
                            }
                        }
                        if ($present == 1) {
                            break;
                        }
                    }
                    // return $ip_addresses;
                    // deactivate the id
                    if (strlen($ip_id) > 0) {
                        // deactivate
                        $deactivate = $API->comm("/ip/address/set", array(
                            "disabled" => "false",
                            ".id" => $ip_id
                        ));

                        // update the user data to de-activated
                        DB::connection("mysql2")->table('client_tables')
                        ->where('client_id', $userid)
                        ->update([
                            'client_status' => "1",
                            'date_changed' => date("YmdHis")
                        ]);

                        // log message
                        $txt = ":Client (".$client_data[0]->client_name.") activated by ".(session('Usernames') ? session('Usernames'):"System");
                        // $this->log($txt);
                        // end of log file
                        if (session('Usernames')) {
                            session()->flash("success","User has been successfully activated");
                            return redirect(route("viewOrganizationClient",[$organization_details[0]->organization_id,$userid]));
                        }else{
                            return ["success" => true, "message" => $client_data[0]->client_name." updated successfully!"];
                        }
                    }else {
                        if (session('Usernames')) {
                            session()->flash("error","The user ip address not found in the router address list");
                            return redirect(route("viewOrganizationClient",[$organization_details[0]->organization_id,$userid]));
                        }else{
                            return ["success" => false, "message" => $client_data[0]->client_name." : The user ip address not found in the router address list!"];
                        }
                    }
                }else {
                    if (session('Usernames')) {
                        session()->flash("error","Cannot connect to the router!");
                        return redirect(route("viewOrganizationClient",[$organization_details[0]->organization_id,$userid]));
                    }else{
                        return ["success" => false, "message" => $client_data[0]->client_name." : Cannot connect to the router!"];
                    }
                }
            }elseif ($client_data[0]->assignment == "pppoe") {
                // disable the client secret and remove the client from active connections
                $router_id = $client_data[0]->router_name;
                // connect to the router and deactivate the client address
                $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `router_id` = '$router_id' AND `deleted` = '0'");
                
                // router value
                if (count($router_data) == 0) {
                    if (session('Usernames')) {
                        $error = "Router connected to client not found!";
                        session()->flash("error",$error);
                        return redirect(url()->previous());
                    }else{
                        return ["success" => false, "message" => $client_data[0]->client_name." : Router connected to client not found!"];
                    }
                }
        
                // get the sstp credentails they are also the api usernames
                $sstp_username = $router_data[0]->sstp_username;
                $sstp_password = $router_data[0]->sstp_password;
                $api_port = $router_data[0]->api_port;
                

                // connect to the router and set the sstp client
                $sstp_value = $this->getSSTPAddress($database_name);
                if ($sstp_value == null) {
                    if (session('Usernames')) {
                        $error = "The SSTP server is not set, Contact your administrator!";
                        session()->flash("error",$error);
                        return redirect(url()->previous());
                    }else{
                        return ["success" => false, "message" => $client_data[0]->client_name." : The SSTP server is not set, Contact your administrator!"];
                    }
                }

                // connect to the router and set the sstp client
                $server_ip_address = $sstp_value->ip_address;
                $user = $sstp_value->username;
                $pass = $sstp_value->password;
                $port = $sstp_value->port;

                // check if the router is actively connected
                $client_router_ip = $this->checkActive($server_ip_address,$user,$pass,$port,$sstp_username);

                if ($client_router_ip == null) {
                    if (session('Usernames')) {
                        $error = "Your router is not active, Restart it and try again!";
                        session()->flash("error",$error);
                        return redirect(url()->previous());
                    }else{
                        return ["success" => false, "message" => $client_data[0]->client_name." : Your router is not active, Restart it and try again!"];
                    }
                }
        
                // client secret name 
                $secret_name = $client_data[0]->client_secret;
                // create the router os api
                $API = new routeros_api();
                $API->debug = false;
                if ($API->connect($client_router_ip,$sstp_username,$sstp_password,$api_port)){
                    // get the IP ADDRES
                    $curl_handle = curl_init();
                    $url = "https://crontab.hypbits.com/getIpaddress.php?db_name=".session("database_name")."&r_id=".$router_id."&r_ppoe_secrets=true";
                    curl_setopt($curl_handle, CURLOPT_URL, $url);
                    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
                    $curl_data = curl_exec($curl_handle);
                    curl_close($curl_handle);
                    $router_secrets = json_decode($curl_data, true);

                    // loop through the secrets get the id and use it to disable the secret
                    $secret_id = "0";
                    for ($indexes=0; $indexes < count($router_secrets); $indexes++) {
                        $secrets = $router_secrets[$indexes];
                        if ($secrets['name'] == $secret_name) {
                            // loop through and pull the id we will use to disable the secret
                            foreach ($secrets as $key => $value) {
                                if ($key == ".id") {
                                    $secret_id = $value;
                                    break;
                                }
                            }
                        }
                    }

                    $API->comm("/ppp/secret/set", array(
                        "disabled" => "false",
                        ".id" => $secret_id
                    ));

                    // uodate the database
                    // update the user data to de-activated
                    DB::connection("mysql2")->table('client_tables')
                    ->where('client_id', $userid)
                    ->update([
                        'client_status' => "1",
                        'date_changed' => date("YmdHis")
                    ]);

                    // log message
                    $txt = ":Client (".$client_data[0]->client_name.") activated by ".(session('Usernames') ? session('Usernames'):"System");
                    // $this->log($txt);
                    // end of log file
                    if (session('Usernames')) {
                        session()->flash("success","User has been successfully activated");
                        return redirect(route("viewOrganizationClient",[$organization_details[0]->organization_id,$userid]));
                    }else{
                        return ["success" => true, "message" => $client_data[0]->client_name." : User has been successfully activated!"];
                    }
                }else {
                    if (session('Usernames')) {
                        session()->flash("error","Cannot connect to the router!");
                        return redirect(route("viewOrganizationClient",[$organization_details[0]->organization_id,$userid]));
                    }else{
                        return ["success" => false, "message" => $client_data[0]->client_name." : Cannot connect to the router!"];
                    }
                }
            }
        }else {
            if (session('Usernames')) {
                session()->flash("error","Client not found!");
                return redirect(route("viewOrganizationClients",[$organization_id]));
            }else{
                return "";
            }
        }
        /*****ends here */
    }

    function view_organization_client($organization_id,$clientid){
        // return $clientid;
        // organization id
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization_details) == 0) {
            session()->flash("error", "Invalid organization!");
            return redirect(route("Organizations"));
        }

        // change db
        $change_db = new login();
        $change_db->change_db($organization_details[0]->organization_database);

        // get the clients information from the database
        $clients_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_id` = '$clientid'");
        if (count($clients_data) > 0) {
            // here we get the router data
            // check if the client is static or pppoe
            $assignment = $clients_data[0]->assignment;
            if ($assignment == "static") {
                $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `deleted` = '0'");
                // get the clients expiration date
                $expire = $clients_data[0]->next_expiration_date;
                $registration = $clients_data[0]->clients_reg_date;
                $freeze_date = strlen($clients_data[0]->client_freeze_untill) > 0 ?( ($clients_data[0]->client_freeze_untill*=1) == 0 ? "Indefinite Date" : $clients_data[0]->client_freeze_untill) : "";
                // return the client data and the router data
                $date_data = $expire;
                $year = substr($date_data,0,4);
                $month = substr($date_data,4,2);
                $day = substr($date_data,6,2);
                $hour = substr($date_data,8,2);
                $minute = substr($date_data,10,2);
                $second = substr($date_data,12,2);
                $d = mktime($hour, $minute, $second, $month, $day, $year);
                $expire_date = date("D dS M-Y", $d)." at ".date("h:i:sa", $d);
        
        
                $date_data = $registration;
                $year = substr($date_data,0,4);
                $month = substr($date_data,4,2);
                $day = substr($date_data,6,2);
                $hour = substr($date_data,8,2);
                $minute = substr($date_data,10,2);
                $second = substr($date_data,12,2);
                $d = mktime($hour, $minute, $second, $month, $day, $year);
                $reg_date = date("D dS M-Y", $d)." at ".date("h:i:sa", $d);
                
                if ($freeze_date != "Indefinite Date") {
                    if(strlen($freeze_date) > 0){
                        $freeze_date = date("D dS M Y",strtotime($freeze_date));
                    }
                }
                // get the client name, phone number, account number
                $clients_infor = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0'");
                $clients_name = [];
                $clients_phone = [];
                $clients_acc_no = [];
                for ($index=0; $index < count($clients_infor); $index++) { 
                    if ($clientid != $clients_infor[$index]->client_id) {
                        array_push($clients_name,$clients_infor[$index]->client_name);
                        array_push($clients_phone,$clients_infor[$index]->clients_contacts);
                        array_push($clients_acc_no,$clients_infor[$index]->client_account);
                    }
                }
                // get refferal
                $clients_data[0]->reffered_by = str_replace("'","\"",$clients_data[0]->reffered_by);
                $client_data = strlen($clients_data[0]->reffered_by) > 0 ? json_decode($clients_data[0]->reffered_by) : json_decode("{}");
                $client_refferal = "No refferee";
                $reffer_details = [];
                $payment_histoty = [];
                if (isset($client_data->client_acc)) {
                    $month_pay = $client_data->monthly_payment; 
                    $client_name = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `client_account` = '".$client_data->client_acc."' AND `deleted` = '0'");
                    if (count($client_name) > 0) {
                        $client_refferal = ucwords(strtolower($client_name[0]->client_name." @ Kes ".number_format($month_pay)));
                        $reffer_details = [$client_name[0]->client_name,$client_name[0]->client_account,$client_name[0]->wallet_amount,$client_name[0]->client_address];
                        $pay = $client_data->payment_history;
                        // return $pay;
                        for ($i=0; $i < count($pay); $i++) { 
                            $payments = [$pay[$i]->amount, date("D dS M Y @ H:i:s A", strtotime($pay[$i]->date))];
                            array_push($payment_histoty,$payments);
                        }
                    }
                }
                // client account use it to get the clients that are reffered by him
                $client_reffer = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0'");
                // return $client_reffer;
                $refferer_acc = $clients_data[0]->client_account;
                $reffered_list = [];
                for ($count=0; $count < count($client_reffer); $count++) { 
                    if (isset($client_reffer[$count]->reffered_by)) {
                        if ($client_reffer[$count]->reffered_by != null && trim($client_reffer[$count]->reffered_by) != "") {
                            $string = $client_reffer[$count]->reffered_by;
                            if (substr($string,0,1) == "\"") {
                                $string = substr(trim($string),1,strlen(trim($string))-2);
                            }
                            $string = str_replace("\\","",$string);
                            $string = str_replace("'","\"",$string);
                            $reffer_infor = json_decode($string);
                            // return $reffer_infor;
                            if($reffer_infor->client_acc == $refferer_acc){
                                $reffer_infor->reffered = $client_reffer[$count];
                                array_push($reffered_list,$reffer_infor);
                                // return $reffer_infor;
                            }
                        }
                    }
                }
                return view("Orgarnizations.client_info",['organization_details' => $organization_details[0], 'clients_data'=>$clients_data,'router_data'=>$router_data,"expire_date" => $expire_date,"registration_date" => $reg_date, "freeze_date" => $freeze_date,"clients_names"=>$clients_name,"clients_account"=>$clients_acc_no,"clients_contacts"=>$clients_phone,"client_refferal" => $client_refferal,"reffer_details" => $reffer_details,"refferal_payment" => $payment_histoty,"reffered_list" => $reffered_list]);
            }elseif ($assignment == "pppoe") {
                $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `deleted` = '0'");
                // get the clients expiration date
                $expire = $clients_data[0]->next_expiration_date;
                $registration = $clients_data[0]->clients_reg_date;
                $freeze_date = strlen($clients_data[0]->client_freeze_untill) > 0 ?( ($clients_data[0]->client_freeze_untill*=1) == 0 ? "Indefinite Date" : $clients_data[0]->client_freeze_untill) : "";
                // return the client data and the router data
                $date_data = $expire;
                $year = substr($date_data,0,4);
                $month = substr($date_data,4,2);
                $day = substr($date_data,6,2);
                $hour = substr($date_data,8,2);
                $minute = substr($date_data,10,2);
                $second = substr($date_data,12,2);
                $d = mktime($hour, $minute, $second, $month, $day, $year);
                $expire_date = date("D dS M-Y", $d)." at ".date("h:i:sa", $d);
        
        
                $date_data = $registration;
                $year = substr($date_data,0,4);
                $month = substr($date_data,4,2);
                $day = substr($date_data,6,2);
                $hour = substr($date_data,8,2);
                $minute = substr($date_data,10,2);
                $second = substr($date_data,12,2);
                $d = mktime($hour, $minute, $second, $month, $day, $year);
                $reg_date = date("D dS M-Y", $d)." at ".date("h:i:sa", $d);
        
                if ($freeze_date != "Indefinite Date") {
                    if (strlen($freeze_date) > 0) {
                        $freeze_date = date("D dS M Y",strtotime($freeze_date));
                    }
                }
                // get the client name, phone number, account number
                $clients_infor = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0'");
                $clients_name = [];
                $clients_phone = [];
                $clients_acc_no = [];
                for ($index=0; $index < count($clients_infor); $index++) { 
                    if ($clientid != $clients_infor[$index]->client_id) {
                        array_push($clients_name,$clients_infor[$index]->client_name);
                        array_push($clients_phone,$clients_infor[$index]->clients_contacts);
                        array_push($clients_acc_no,$clients_infor[$index]->client_account);
                    }
                }
                // get refferal
                $client_data = strlen($clients_data[0]->reffered_by) > 0 ? json_decode($clients_data[0]->reffered_by) : json_decode("{}");
                $client_refferal = "No refferee";
                $reffer_details = [];
                $payment_histoty = [];
                if (isset($client_data->client_acc)) {
                    $month_pay = $client_data->monthly_payment; 
                    $client_name = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_account` = '".$client_data->client_acc."'");
                    if (count($client_name) > 0) {
                        $client_refferal = ucwords(strtolower($client_name[0]->client_name." @ Kes ".number_format($month_pay)));
                        $reffer_details = [$client_name[0]->client_name,$client_name[0]->client_account,$client_name[0]->wallet_amount,$client_name[0]->client_address];
                        $pay = $client_data->payment_history;
                        // return $pay;
                        for ($i=0; $i < count($pay); $i++) { 
                            $payments = [$pay[$i]->amount, date("D dS M Y @ H:i:s A", strtotime($pay[$i]->date))];
                            array_push($payment_histoty,$payments);
                        }
                    }
                }
                // client account use it to get the clients that are reffered by him
                $client_reffer = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0'");
                // return $client_reffer;
                $refferer_acc = $clients_data[0]->client_account;
                $reffered_list = [];
                for ($count=0; $count < count($client_reffer); $count++) { 
                    if (isset($client_reffer[$count]->reffered_by)) {
                        if ($client_reffer[$count]->reffered_by != null) {
                            $string = $client_reffer[$count]->reffered_by;
                            if (substr($string,0,1) == "\"") {
                                $string = substr(trim($string),1,strlen(trim($string))-2);
                            }
                            $string = str_replace("\\","",$string);
                            $string = str_replace("'","\"",$string);
                            $reffer_infor = json_decode($string);
                            if($reffer_infor->client_acc == $refferer_acc){
                                $reffer_infor->reffered = $client_reffer[$count];
                                array_push($reffered_list,$reffer_infor);
                                // return $reffer_infor;
                            }
                        }
                    }
                }
                return view("Orgarnizations.client_info_pppoe",['organization_details' => $organization_details[0], 'clients_data'=>$clients_data,'router_data'=>$router_data,"expire_date" => $expire_date,"registration_date" => $reg_date, "freeze_date" => $freeze_date,"clients_names"=>$clients_name,"clients_account"=>$clients_acc_no,"clients_contacts"=>$clients_phone,"client_refferal" => $client_refferal,"reffer_details" => $reffer_details,"refferal_payment" => $payment_histoty,"reffered_list" => $reffered_list]);
            }else {
                session()->flash("error","Invalid Assignment!!");
                return redirect(route("viewOrganizationClients",[$organization_id]));
            }
        }else {
            session()->flash("error","Invalid User!!");
            return redirect(route("viewOrganizationClients",[$organization_id]));
        }
    
    }

    // convert client
    function convertClient($organization_id, Request $request){
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization_details) == 0) {
            session()->flash("error", "Invalid organization!");
            return redirect(route("Organizations"));
        }

        // change db
        $change_db = new login();
        $change_db->change_db($organization_details[0]->organization_database);

        // client id
        $client_id = $request->input("client_id");
        // select the client details
        $client_data = DB::connection("mysql2")->select("SELECT * FROM client_tables WHERE client_id = ?",[$client_id]);
        if (count($client_data) > 0) {
            if ($client_data[0]->assignment == "static"){
                $client_secret_username = $request->input("client_secret_username");
                $client_secret_password = $request->input("client_secret_password");
                $router_list = $request->input("router_list");
                $pppoe_profile = $request->input("pppoe_profile");
                // delete the user ip address

                // check if the routers are the same
                $router_id =  $router_list;
                $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `router_id` = ? AND `deleted` = '0'", [$router_id]);
                if (count($router_data) > 0) {
                    // disable the interface in that router

                    // get the sstp credentails they are also the api usernames
                    $sstp_username = $router_data[0]->sstp_username;
                    $sstp_password = $router_data[0]->sstp_password;
                    $api_port = $router_data[0]->api_port;


                    // connect to the router and set the sstp client
                    $sstp_value = $this->getSSTPAddress($organization_details[0]->organization_database);
                    if ($sstp_value == null) {
                        $error = "The SSTP server is not set, Contact your administrator!";
                        session()->flash("error", $error);
                        return redirect(url()->previous());
                    }

                    // connect to the router and set the sstp client
                    $server_ip_address = $sstp_value->ip_address;
                    $user = $sstp_value->username;
                    $pass = $sstp_value->password;
                    $port = $sstp_value->port;

                    // check if the router is actively connected
                    $client_router_ip = $this->checkActive($server_ip_address, $user, $pass, $port, $sstp_username);
                    if($client_router_ip != null){
                        $API = new routeros_api();
                        $API->debug = false;
                        if ($API->connect($client_router_ip, $sstp_username, $sstp_password, $api_port)) {
                            // GET IP ADDRESS
                            $ip_addresses = $this->getRouterIPAddress($router_id);
                            // GET QUEUES
                            $simple_queues = $this->getRouterQueues($router_id);
                            $subnet = explode("/", $client_data[0]->client_default_gw);

                            // DELETE IP ADDRESS
                            $client_network = $client_data[0]->client_network;
                            foreach ($ip_addresses as $key => $ip_address) {
                                if ($client_network == $ip_address['network']) {
                                    $API->comm("/ip/address/remove", array(
                                        ".id" => $ip_address['.id']
                                    ));
                                    break;
                                }
                            }

                            // DELETE THE QUEUES
                            $target_key = array_key_exists('address', $simple_queues[0]) ? 'address' : 'target';
                            $queue_ip = $client_network . "/" . $subnet[1];
                            foreach ($simple_queues as $key => $queue) {
                                if ($queue[$target_key] == $queue_ip) {
                                    $API->comm("/queue/simple/remove", array(
                                        ".id" => $queue['.id']
                                    ));
                                    break;
                                }
                            }

                            // ADD THE SECRET TO THE ROUTER
                            $ppp_secrets = $this->getPPPSecrets($router_id);

                            // FIND THE SECRET
                            $present_ppp_profile = 0;
                            $secret_id = 0;
                            for ($index = 0; $index < count($ppp_secrets); $index++) {
                                if ($ppp_secrets[$index]['name'] == $client_secret_username) {
                                    $present_ppp_profile = 1;
                                    $secret_id = $ppp_secrets[$index]['.id'];
                                    break;
                                }
                            }
                            
                            if ($present_ppp_profile == 1) {
                                // update the password and the service
                                $API->comm(
                                    "/ppp/secret/set",
                                    array(
                                        "name"     => $client_secret_username,
                                        "service" => "pppoe",
                                        "password" => $client_secret_password,
                                        "profile"  => $pppoe_profile,
                                        "comment"  => $client_data[0]->client_name . " (" . $client_data[0]->client_address . " - " . $client_data[0]->location_coordinates . ") - " . $client_data[0]->client_account,
                                        "disabled" => $client_data[0]->client_status == "1" ? "false" : "true",
                                        ".id" => $secret_id
                                    )
                                );
                                // log message
                                $txt = ":Client (" . $client_data[0]->client_name . ") converted from static assignment to pppoe by  " . session('Usernames') . "!";
                                $this->log($txt);
                            } else {
                                $API->comm(
                                    "/ppp/secret/add",
                                    array(
                                        "name"     => $client_secret_username,
                                        "service" => "pppoe",
                                        "password" => $client_secret_password,
                                        "profile"  => $pppoe_profile,
                                        "comment"  => $client_data[0]->client_name . " (" . $client_data[0]->client_address . " - " . $client_data[0]->location_coordinates . ") - " . $client_data[0]->client_account,
                                        "disabled" => $client_data[0]->client_status == "1" ? "false" : "true"
                                    )
                                );

                                // log message
                                $txt = ":New Client (" . $client_data[0]->client_name . ") successfully converted from static assignment to pppoe by  " . session('Usernames') . "!";
                                $this->log($txt);
                            }

                            // update the client details
                            DB::connection("mysql2")->table('client_tables')
                            ->where('client_id', $client_data[0]->client_id)
                            ->update([
                                // 'client_network' => "",
                                // 'client_default_gw' => "",
                                // 'max_upload_download' => "",
                                'router_name' => $router_list,
                                'assignment' => "pppoe",
                                'client_secret' => $client_secret_username,
                                'client_secret_password' => $client_secret_password,
                                'client_profile' => $pppoe_profile,
                                'date_changed' => date("YmdHis")
                            ]);

                            session()->flash("success", $client_data[0]->client_name." assignment to PPPoE has been successfully done!");
                            return redirect(url()->previous());
                        }else {
                            session()->flash("error", "Can`t connect to router please try again later!");
                            return redirect(url()->previous());
                        }
                    }else {
                        session()->flash("error", "Can`t connect to router please try again later!");
                        return redirect(url()->previous());
                    }
                }else {
                    session()->flash("error", "Can`t connect to router please try again later!");
                    return redirect(url()->previous());
                }
            }elseif ($client_data[0]->assignment == "pppoe") {
                // CONVERT FROM PPPOE TO STATIC
                
                // DELETE SECRET AND ACTIVE CONNECTION
                // get the router data
                $router_id = $client_data[0]->router_name;
                $client_name = $client_data[0]->client_name;
                $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `router_id` = ? AND `deleted` = '0'", [$client_data[0]->router_name]);
                if (count($router_data) > 0) {
                    // get the sstp credentails they are also the api usernames
                    $sstp_username = $router_data[0]->sstp_username;
                    $sstp_password = $router_data[0]->sstp_password;
                    $api_port = $router_data[0]->api_port;

                    // connect to the router and set the sstp client
                    $sstp_value = $this->getSSTPAddress($organization_details[0]->organization_database);
                    if ($sstp_value == null) {
                        $error = "The SSTP server is not set, Contact your administrator!";
                        session()->flash("error", $error);
                        return redirect(url()->previous());
                    }

                    // connect to the router and set the sstp client
                    $server_ip_address = $sstp_value->ip_address;
                    $user = $sstp_value->username;
                    $pass = $sstp_value->password;
                    $port = $sstp_value->port;

                    // check if the router is actively connected
                    $client_router_ip = $this->checkActive($server_ip_address, $user, $pass, $port, $sstp_username);
                    if($client_router_ip !=null){
                        $API = new routeros_api();
                        $API->debug = false;

                        $router_secrets = [];
                        $active_connections = [];
                        if ($API->connect($client_router_ip, $sstp_username, $sstp_password, $api_port)) {
                            // get the secret details
                            $secret_name = $client_data[0]->client_secret;
                            // ACTIVE SECRET CONNECTIONS
                            $active_connections = $this->getRouterActiveSecrets($client_data[0]->router_name);
                            // ROUTER SECRETS
                            $router_secrets = $this->getRouterSecrets($client_data[0]->router_name);

                            // router secrets
                            foreach ($router_secrets as $key => $router_secret) {
                                if ($router_secret['name'] == $secret_name) {
                                    $API->comm("/ppp/secret/remove", array(
                                        ".id" => $router_secret['.id']
                                    ));
                                    break;
                                }
                            }
                            foreach ($active_connections as $key => $connection) {
                                if ($connection['name'] == $secret_name) {
                                    // remove the active connection if there is, it will do nothing if the id is not present
                                    $API->comm("/ppp/active/remove", array(
                                        ".id" => $connection['.id']
                                    ));
                                }
                            }

                        }
                    }
                }else{
                    session()->flash("error","An error has occured!");
                    return redirect(url()->previous());
                }

                // ROUTER LIST
                $router_name = $request->input("router_list");

                // check if the selected router is connected
                // get the router data
                $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `router_id` = ? AND `deleted` = '0'", [$router_name]);
                if (count($router_data) == 0) {
                    $error = "Router selected does not exist!";
                    session()->flash("error", $error);
                    return redirect(url()->previous());
                }

                // get the sstp credentails they are also the api usernames
                $sstp_username = $router_data[0]->sstp_username;
                $sstp_password = $router_data[0]->sstp_password;
                $api_port = $router_data[0]->api_port;

                // connect to the router and set the sstp client
                $sstp_value = $this->getSSTPAddress($organization_details[0]->organization_database);
                if ($sstp_value == null) {
                    $error = "The SSTP server is not set, Contact your administrator!";
                    session()->flash("error", $error);
                    return redirect(url()->previous());
                }

                // connect to the router and set the sstp client
                $server_ip_address = $sstp_value->ip_address;
                $user = $sstp_value->username;
                $pass = $sstp_value->password;
                $port = $sstp_value->port;

                // check if the router is actively connected
                $client_router_ip = $this->checkActive($server_ip_address, $user, $pass, $port, $sstp_username);

                if($client_router_ip != null){
                    // get ip address and queues
                    // start with IP address
                    // connect to the router and add the ip address and queues to the interface
                    $API = new routeros_api();
                    $API->debug = false;

                    $router_ip_addresses = [];
                    $router_simple_queues = [];
                    if ($API->connect($client_router_ip, $sstp_username, $sstp_password, $api_port)) {
                        $router_ip_addresses = $this->getRouterIPAddress($router_name);
                        $simple_queues = $this->getRouterQueues($router_name);

                        // proceed and add the client to the router
                        // check if the ip address is present
                        $present = false;
                        foreach ($router_ip_addresses as $key => $value_address) {
                            if ($value_address['network'] == $request->input('client_network')) {
                                $present = true;
                                // update the ip address
                                $result = $API->comm(
                                    "/ip/address/set",
                                    array(
                                        "address"     => $request->input('client_gw'),
                                        "interface" => $request->input('interface_name'),
                                        "comment"  => $client_data[0]->client_name . " (" . $client_data[0]->client_address . " - " . $client_data[0]->location_coordinates . ") - " . $client_data[0]->client_account,
                                        "disabled" => $client_data[0]->client_status == "1" ? "false" : "true",
                                        ".id" => $value_address['.id']
                                    )
                                );
                                if (count($result) > 0) {
                                    // this means there is an error
                                    $API->comm(
                                        "/ip/address/set",
                                        array(
                                            "interface" => $request->input('interface_name'),
                                            "comment"  => $client_data[0]->client_name . " (" . $client_data[0]->client_address . " - " . $client_data[0]->location_coordinates . ") - " . $client_data[0]->client_account,
                                            "disabled" => $client_data[0]->client_status == "1" ? "false" : "true",
                                            ".id" => $value_address['.id']
                                        )
                                    );
                                }
                                break;
                            }
                        }

                        // return $present_ip;
                        if (!$present) {
                            // add the ip address
                            $result = $API->comm(
                                "/ip/address/add",
                                array(
                                    "address"     => $request->input('client_gw'),
                                    "interface" => $request->input('interface_name'),
                                    "network" => $request->input('client_network'),
                                    "disabled" => $client_data[0]->client_status == "1" ? "false" : "true",
                                    "comment"  => $client_data[0]->client_name . " (" . $client_data[0]->client_address . " - " . $client_data[0]->location_coordinates . ") - " . $client_data[0]->client_account
                                )
                            );
                        }

                        // proceed and add the queues 
                        // first check the queues
                        $upload = $request->input("upload_speed") . $request->input("unit1");
                        $download = $request->input("download_speed") . $request->input("unit2");
                        $target_key = array_key_exists('address', $simple_queues) ? 'address' : 'target';
                        $queue_present = false;
                        foreach ($router_simple_queues as $key => $value_simple_queues) {
                            if ($value_simple_queues[$target_key] == $request->input("client_network") . "/" . explode("/", $request->input("client_gw"))[1]) {
                                $queue_present = true;
                                $API->comm(
                                    "/queue/simple/set",
                                    array(
                                        "name" => $client_data[0]->client_name . " (" . $client_data[0]->client_address . " - " . $client_data[0]->location_coordinates . ") - " . $client_data[0]->client_account,
                                        "$target_key" => $request->input("client_network") . "/" . explode("/", $request->input("client_gw"))[1],
                                        "max-limit" => $upload . "/" . $download,
                                        ".id" => $value_simple_queues['.id']
                                    )
                                );
                                break;
                            }
                        }

                        // queue not present
                        if (!$queue_present) {
                            // add the queue to the list
                            $API->comm(
                                "/queue/simple/add",
                                array(
                                    "name" => $client_data[0]->client_name . " (" . $client_data[0]->client_address . " - " . $client_data[0]->location_coordinates . ") - " . $client_data[0]->client_account,
                                    "$target_key" => $request->input("client_network") . "/" . explode("/", $request->input("client_gw"))[1],
                                    "max-limit" => $upload . "/" . $download
                                )
                            );
                        }
                        // disconnect the api
                        $API->disconnect();
                        
                        // update the client information
                        // log message
                        $txt = ":Client (" . $client_name . ") has been successfully converted to Static Assignment by  " . session('Usernames');
                        $this->log($txt);
                        // end of log file

                        // update the client details
                        DB::connection("mysql2")->table('client_tables')
                        ->where('client_id', $client_data[0]->client_id)
                        ->update([
                            'client_network' => $request->input("client_network"),
                            'client_default_gw' => $request->input('client_gw'),
                            'max_upload_download' => $upload . "/" . $download,
                            'router_name' => $router_name,
                            'assignment' => "static",
                            // 'client_secret' => "",
                            // 'client_secret_password' => "",
                            // 'client_profile' => "",
                            'date_changed' => date("YmdHis")
                        ]);

                        // return to the main page
                        session()->flash("success", $client_data[0]->client_name." assignment to static has been successfully done!");
                        return redirect(url()->previous());
                    }
                }
            }
            session()->flash("error", "Invalid Conversion try again!");
            return redirect(url()->previous());
        }else{
            session()->flash("error", "Invalid user!");
            return redirect(url()->previous());
        }
    }
    function getRouterProfile($organization_id,$routerid)
    {
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization_details) == 0) {
            session()->flash("error", "Invalid organization!");
            return redirect(route("Organizations"));
        }

        // change db
        $change_db = new login();
        $change_db->change_db($organization_details[0]->organization_database);

        // get the router data
        $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `router_id` = ? AND `deleted` = '0'", [$routerid]);
        if (count($router_data) == 0) {
            echo "Router does not exist!";
            return "";
        }

        // get the IP ADDRES
        $curl_handle = curl_init();
        $url = env('CRONJOB_URL', 'https://crontab.hypbits.com')."/getIpaddress.php?db_name=" . session("database_name") . "&r_id=" . $routerid . "&r_ppoe_profiles=true";
        curl_setopt($curl_handle, CURLOPT_URL, $url);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
        $curl_data = curl_exec($curl_handle);
        curl_close($curl_handle);
        $pppoe_profiles = strlen($curl_data) > 0 ? json_decode($curl_data, true) : [];

        if (isset($pppoe_profiles) && count($pppoe_profiles) > 0){
            // create the select selector
            $data_to_display = "<select name='pppoe_profile' class='form-control' id='pppoe_profile'  ><option value='' hidden>Select a Profile</option>";
            for ($index = 0; $index < count($pppoe_profiles); $index++) {
                $data_to_display .= "<option value='" . $pppoe_profiles[$index]['name'] . "'>" . $pppoe_profiles[$index]['name'] . "</option>";
            }
            $data_to_display .= "</select>";
            echo $data_to_display;
        } else {
            echo "No data to display : \"Your router might be In-active!\"";
        }
        return "";
    }

    function getRouterInterfaces($organization_id,$routerid)
    {
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization_details) == 0) {
            session()->flash("error", "Invalid organization!");
            return redirect(route("Organizations"));
        }

        // change db
        $change_db = new login();
        $change_db->change_db($organization_details[0]->organization_database);

        // get the router data
        $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `router_id` = ? AND `deleted` = '0'", [$routerid]);
        if (count($router_data) == 0) {
            echo "Router does not exist!";
            return "";
        }

        // get the IP ADDRES
        $curl_handle = curl_init();
        $url = env('CRONJOB_URL', 'https://crontab.hypbits.com')."/getIpaddress.php?db_name=" . session("database_name") . "&r_id=" . $routerid . "&r_interfaces=true";
        curl_setopt($curl_handle, CURLOPT_URL, $url);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
        $curl_data = curl_exec($curl_handle);
        curl_close($curl_handle);
        $interfaces = strlen($curl_data) > 0 ? json_decode($curl_data, true) : [];
        
        if (!empty($interfaces)) {
            $data_to_display = "<select name='interface_name' class='form-control' id='interface_name' required ><option value='' hidden>Select an Interface</option>";
            for ($index = 0; $index < count($interfaces); $index++) {
                if($interfaces[$index]['type'] == "ether" || $interfaces[$index]['type'] == "bridge"){
                    $data_to_display .= "<option value='" . $interfaces[$index]['name'] . "'>" . $interfaces[$index]['name'] . "</option>";
                }
            }
            $data_to_display .= "</select>";
            echo $data_to_display;
        } else {
            echo "No data to display : \"Your router might be In-active!\"";
        }
        return "";
    }

    function update_organization (Request $request,$organization_id){
        // organization id
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization_details) == 0) {
            session()->flash("error", "Invalid organization!");
            return redirect(route("Organizations"));
        }
        // change db
        $change_db = new login();
        $change_db->change_db($organization_details[0]->organization_database);

        // insert or update the API SENDER
        $api_link = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `keyword` = 'sms_sender'");
        if (count($api_link) > 0) {
            $api_link_id = $api_link[0]->id;
            DB::connection("mysql2")->update("UPDATE `settings` SET `value` = '".$request->input("sms_sender")."' WHERE `id` = '".$api_link_id."'");
        }else{
            $insert = DB::connection("mysql2")->insert("INSERT INTO `settings` (`keyword`, `value`,`status`) VALUES ('sms_sender','".$request->input("sms_sender")."','1')");
        }

        // insert or update the PATNER ID
        $api_link = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `keyword` = 'sms_partner_id'");
        if (count($api_link) > 0) {
            $api_link_id = $api_link[0]->id;
            DB::connection("mysql2")->update("UPDATE `settings` SET `value` = '".$request->input("sms_partner_id")."' WHERE `id` = '".$api_link_id."'");
        }else{
            $insert = DB::connection("mysql2")->insert("INSERT INTO `settings` (`keyword`, `value`,`status`) VALUES ('sms_partner_id','".$request->input("sms_partner_id")."','1')");
        }

        // insert or update the SHORT CODE
        $api_link = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `keyword` = 'sms_shortcode'");
        if (count($api_link) > 0) {
            $api_link_id = $api_link[0]->id;
            DB::connection("mysql2")->update("UPDATE `settings` SET `value` = '".$request->input("sms_shortcode")."' WHERE `id` = '".$api_link_id."'");
        }else{
            $insert = DB::connection("mysql2")->insert("INSERT INTO `settings` (`keyword`, `value`,`status`) VALUES ('sms_shortcode','".$request->input("sms_shortcode")."','1')");
        }

        // insert or update the API KEY
        $api_link = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `keyword` = 'sms_api_key'");
        if (count($api_link) > 0) {
            $api_link_id = $api_link[0]->id;
            DB::connection("mysql2")->update("UPDATE `settings` SET `value` = '".$request->input("sms_api_key")."' WHERE `id` = '".$api_link_id."'");
        }else{
            $insert = DB::connection("mysql2")->insert("INSERT INTO `settings` (`keyword`, `value`,`status`) VALUES ('sms_api_key','".$request->input("sms_api_key")."','1')");
        }

        // insert or update the pass key
        $api_link = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `keyword` = 'passkey'");
        if (count($api_link) > 0) {
            $api_link_id = $api_link[0]->id;
            DB::connection("mysql2")->update("UPDATE `settings` SET `value` = '".$request->input("mpesa_pass_key")."' WHERE `id` = '".$api_link_id."'");
        }else{
            $insert = DB::connection("mysql2")->insert("INSERT INTO `settings` (`keyword`, `value`,`status`) VALUES ('passkey','".$request->input("mpesa_pass_key")."','1')");
        }

        // insert or update the CONSUMER KEY
        $api_link = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `keyword` = 'consumer_key'");
        if (count($api_link) > 0) {
            $api_link_id = $api_link[0]->id;
            DB::connection("mysql2")->update("UPDATE `settings` SET `value` = '".$request->input("mpesa_consumer_key")."' WHERE `id` = '".$api_link_id."'");
        }else{
            $insert = DB::connection("mysql2")->insert("INSERT INTO `settings` (`keyword`, `value`,`status`) VALUES ('consumer_key','".$request->input("mpesa_consumer_key")."','1')");
        }

        // insert or update the API KEY
        $api_link = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `keyword` = 'consumer_secret'");
        if (count($api_link) > 0) {
            $api_link_id = $api_link[0]->id;
            DB::connection("mysql2")->update("UPDATE `settings` SET `value` = '".$request->input("mpesa_consumer_secret")."' WHERE `id` = '".$api_link_id."'");
        }else{
            $insert = DB::connection("mysql2")->insert("INSERT INTO `settings` (`keyword`, `value`,`status`) VALUES ('consumer_secret','".$request->input("mpesa_consumer_secret")."','1')");
        }

        // insert or update the API KEY
        $api_link = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `keyword` = 'paybill'");
        if (count($api_link) > 0) {
            $api_link_id = $api_link[0]->id;
            DB::connection("mysql2")->update("UPDATE `settings` SET `value` = '".$request->input("business_short_code")."' WHERE `id` = '".$api_link_id."'");
        }else{
            $insert = DB::connection("mysql2")->insert("INSERT INTO `settings` (`keyword`, `value`,`status`) VALUES ('paybill','".$request->input("business_short_code")."','1')");
        }

        // organization 
        $organization_name = $request->input("organization_name");
        $organization_location = $request->input("organization_location");
        $organization_contacts = $request->input("organization_contacts");
        $organization_email = $request->input("organization_email");
        $business_short_code = $request->input("business_short_code");
        $free_trial_period = $request->input("free_trial_period");
        $monthly_payment = $request->input("monthly_payment");
        $registration_date = date("Ymd", strtotime($request->input("registration_date"))).date("His");

        // update the organization
        $update = DB::update("UPDATE `organizations` SET `organization_name` = ?, `BusinessShortCode` = ?, `organization_address` = ?, `organization_main_contact` = ?, `organization_email` = ?, `free_trial_period` = ?, `monthly_payment` = ?, `date_joined` = ?  WHERE `organization_id` = ?",
        [$organization_name,$business_short_code,$organization_location,$organization_contacts,$organization_email,$free_trial_period, $monthly_payment, $registration_date, $organization_id]);
        
        session()->flash("success","Organization information has been updated successfully!");
        return redirect(route("ViewOrganization",$organization_id));
    }

    function dateDiffInDays($date1, $date2) {
        // Convert date strings to DateTime objects
        $dateTime1 = DateTime::createFromFormat('YmdHis', $date1);
        $dateTime2 = DateTime::createFromFormat('YmdHis', $date2);
    
        // Calculate the difference between the dates
        $interval = $dateTime2->diff($dateTime1);
    
        // Get the difference in days
        $daysDiff = $interval->format('%R%a');
    
        // Return the difference in days as an integer
        return (int)$daysDiff;
    }

    // import database
    function import_database($filePath,$dbname) {
        // Connect to MySQL server
        $servername = $_ENV['DB_HOST'];
        $username = $_ENV['DB_USERNAME'];
        $password = $_ENV['DB_PASSWORD'];
        $conn = new mysqli($servername, $username, $password);
    
        // Check connection
        if ($conn->connect_error) {
            // die("Connection failed: " . $conn->connect_error);
            return false;
        }

        // Drop database if it exists
        $dropDbSql = "DROP DATABASE IF EXISTS $dbname";
        if ($conn->query($dropDbSql) === TRUE) {
            // echo "Database dropped successfully<br>";
        } else {
            return false;
        }
    
        // Create database if not exists
        $sql = "CREATE DATABASE IF NOT EXISTS $dbname";
        if ($conn->query($sql) === TRUE) {
            // echo "Database created successfully<br>";
        } else {
            // echo "Error creating database: " . $conn->error;
            return false;
        }
    
        // Select the database
        $conn->select_db($dbname);
    
        // Read SQL file
        $sqlCommands = file_get_contents($filePath);
    
        // Split file contents into individual SQL commands
        $commands = explode(";", $sqlCommands);
    
        // Execute each command
        foreach ($commands as $command) {
            // Trim whitespace
            $command = trim($command);
    
            // Skip empty commands
            if ($command == "") continue;
    
            // Execute command
            if ($conn->query($command) === TRUE) {
                // echo "Command executed successfully<br>";
            } else {
                // echo "Error executing command: " . $conn->error . "<br>";
            }
        }
    
        // Close connection
        $conn->close();
        return true;
    }

    // deactivate 
    function deactivate_organization($organization_id){
        // get if the organization is valid
        $select = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($select) == 0) {
            session()->flash("error","Organization is invalid!");
            return redirect(route("Organizations"));
        }

        // update the organization status
        $update = DB::update("UPDATE `organizations` SET `organization_status` = '0' WHERE `organization_id` = ?",[$organization_id]);

        session()->flash("success","Status has been changed successfully!");
        return redirect(route("ViewOrganization",$organization_id));
    }

    // deactivate 
    function activate_organization($organization_id){
        // get if the organization is valid
        $select = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($select) == 0) {
            session()->flash("error","Organization is invalid!");
            return redirect(route("Organizations"));
        }

        // update the organization status
        $update = DB::update("UPDATE `organizations` SET `organization_status` = '1' WHERE `organization_id` = ?",[$organization_id]);

        session()->flash("success","Status has been changed successfully!");
        return redirect(route("ViewOrganization",$organization_id));
    }

    // update wallet
    function update_wallet(Request $request,$organization_id){
        // get if the organization is valid
        $select = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($select) == 0) {
            session()->flash("error","Organization is invalid!");
            return redirect(route("Organizations"));
        }

        // update the wallet balance
        $wallet_balance = $request->input("wallet_balance");
        $update = DB::update("UPDATE `organizations` SET `wallet` = ? WHERE `organization_id` = ?",[$wallet_balance,$organization_id]);

        // return organization details
        session()->flash("success","Wallet has been updated successfully!");
        return redirect(route("ViewOrganization",[$organization_id]));
    }

    // update wallet
    function update_expiry(Request $request,$organization_id){
        // get if the organization is valid
        $select = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($select) == 0) {
            session()->flash("error","Organization is invalid!");
            return redirect(route("Organizations"));
        }

        // update the wallet balance
        $expiration_date = date("YmdHis", strtotime($request->input("expiration_date")));
        $update = DB::update("UPDATE `organizations` SET `expiry_date` = ? WHERE `organization_id` = ?",[$expiration_date,$organization_id]);

        // return organization details
        session()->flash("success","Expiry date has been updated successfully!");
        return redirect(route("ViewOrganization",[$organization_id]));
    }

    // get date differences
    function addPeriodToDate($dateString, $periodString) {
        // Convert date string to DateTime object
        $date = new DateTime($dateString);
    
        // Parse period string
        preg_match('/([-+]?\d+)\s*(day|month|year)s?/', $periodString, $matches);
        $value = intval($matches[1]);
        $unit = $matches[2];
    
        // Add or subtract period to/from date
        switch ($unit) {
            case 'day':
                $date->modify("$value day");
                break;
            case 'month':
                $date->modify("$value month");
                break;
            case 'year':
                $date->modify("$value year");
                break;
            default:
                // Invalid unit
                throw new InvalidArgumentException("Invalid period unit: $unit");
        }
    
        // Return the resulting date as a string
        return $date->format('YmdHis');
    }

    // update lenience
    function update_lenience(Request $request, $organization_id){
        // get if the organization is valid
        $organization = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization) == 0) {
            session()->flash("error","Organization is invalid!");
            return redirect(route("Organizations"));
        }

        // update the organization last payment date
        $last_renewal_date = ($organization[0]->expiry_date == null) ? date("YmdHis") : $organization[0]->expiry_date;
        
        // add the number of days
        $today = date("YmdHis")*1;

        // add the days
        $period_to_add = $request->input("linience_days")." days";
        $last_renewal_date = $this->addPeriodToDate($last_renewal_date,$period_to_add);
        // return [$last_renewal_date, $a,$organization];

        // check if the data is yesterday`s
        // $last_renewal_date = ($last_renewal_date*1) < $today ? $today : $last_renewal_date;
        
        // update the database
        $update = DB::update("UPDATE `organizations` SET `expiry_date` = ?, `lenience` = ? WHERE `organization_id` = ?",[$last_renewal_date,$request->input("linience_days"),$organization_id]);
        
        // return organization details
        session()->flash("success","Lenience days have been successfully updated!");
        return redirect(route("ViewOrganization",[$organization_id]));
    }

    // set the discount
    function update_discount(Request $request, $organization_id){
        // get if the organization is valid
        $organization = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization) == 0) {
            session()->flash("error","Organization is invalid!");
            return redirect(route("Organizations"));
        }
        $discount_type = $request->input("discount_type");
        $discount_amount = $request->input("discount_amount");
        
        if ($discount_amount == 0) {
            $discount_type = null;
            $discount_amount = null;
            // update the discount to the figure and the type given only when its above zero otherwise set it to null
            $update = DB::update("UPDATE `organizations` SET `discount_type` = ?, `discount_amount` = ? WHERE `organization_id` = ?",[$discount_type,$discount_amount,$organization_id]);
        }else{
            // update the discount to the figure and the type given only when its above zero otherwise set it to null
            $update = DB::update("UPDATE `organizations` SET `discount_type` = ?, `discount_amount` = ? WHERE `organization_id` = ?",[$discount_type,$discount_amount,$organization_id]);
        }
        
        session()->flash("success","Discounts have been successfully set!");
        return redirect(route("ViewOrganization",[$organization_id]));
    }

    // activate payment status
    function deactivate_payment_status($organization_id){
        // get if the organization is valid
        $organization = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization) == 0) {
            session()->flash("error","Organization is invalid!");
            return redirect(route("Organizations"));
        }

        // update the organization payment status to 0;
        $update = DB::update("UPDATE `organizations` SET `payment_status` = '0' WHERE `organization_id` = ?",[$organization_id]);

        // redirect to the organization details
        session()->flash("success","Payment status has been successfully deactivated!");
        return redirect(route("ViewOrganization",[$organization_id]));
    }

    // activate payment status
    function activate_payment_status($organization_id){
        // get if the organization is valid
        $organization = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization) == 0) {
            session()->flash("error","Organization is invalid!");
            return redirect(route("Organizations"));
        }

        // update the organization payment status to 0;
        $update = DB::update("UPDATE `organizations` SET `payment_status` = '1' WHERE `organization_id` = ?",[$organization_id]);

        // redirect to the organization details
        session()->flash("success","Payment status has been successfully activated!");
        return redirect(route("ViewOrganization",[$organization_id]));
    }

    function getSSTPAddress($database_name){
        // change db
        $change_db = new login();
        $change_db->change_db($database_name);

        // get the server details
        $sstp_settings = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `keyword` = 'sstp_server'");
        if (count($sstp_settings) == 0) {
            return null;
        }

        // connect to the server
        $sstp_value = $this->isJson($sstp_settings[0]->value) ? json_decode($sstp_settings[0]->value) : null;

        if ($sstp_value == null) {
            return null;
        }
        return $sstp_value;
    }


    function isJson($string) {
        return ((is_string($string) &&
                (is_object(json_decode($string)) ||
                is_array(json_decode($string))))) ? true : false;
    }

    function checkActive($ip_address,$user,$pass,$port,$sstp_username){
        $API = new routeros_api();
        $API->debug = false;

        if ($API->connect($ip_address, $user, $pass, $port)){
            // connect and get the 
            $active = $API->comm("/ppp/active/print");
            // return $active;

            // loop through the active routers to get if the router is active or not so that we connect
            $found = 0;
            $ip_address_remote_client = null;
            for ($index=0; $index < count($active); $index++) { 
                if ($active[$index]['name'] == $sstp_username && $active[$index]['service'] == "sstp") {
                    $found = 1;
                    $ip_address_remote_client = $active[$index]['address'];
                    break;
                }
            }

            // if found the router is actively connected
            if ($found == 1) {
                $API->disconnect();
                return $ip_address_remote_client;
            }
            $API->disconnect();
        }
        return false;
    }
    // update expiration date
    function change_expiry_date(Request $req,$organization_id){
        // return $clientid;
        // organization id
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization_details) == 0) {
            session()->flash("error", "Invalid organization!");
            return redirect(route("Organizations"));
        }

        // change db
        $change_db = new login();
        $change_db->change_db($organization_details[0]->organization_database);

        $new_expiration = date("Ymd",strtotime($req->input('expiration_date_edits')))."235959";
        $client_id = $req->input('clients_id');
        DB::connection("mysql2")->table('client_tables')
        ->where('client_id', $client_id)
        ->update([
            'next_expiration_date' => $new_expiration,
            'date_changed' => date("YmdHis")
        ]);

        $client = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `client_id` = '$client_id' AND `deleted` = '0'");
        $client_name = $client[0]->client_name;
        
        $txt = ":Client ( $client_name ) expiration date changed to ".date("D dS M Y",strtotime($new_expiration)).""."! by ".session('Usernames');
        // $this->log($txt);
        // redirect to the client table
        session()->flash("success","Updates have been done successfully!");
        return redirect(route("viewOrganizationClient",[$organization_id, $client_id]));
    }

    // deactivate user from freeze
    function deactivate_freeze($organization_id, $client_id){
        // organization id
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization_details) == 0) {
            session()->flash("error", "Invalid organization!");
            return redirect(route("Organizations"));
        }

        // change db
        $change_db = new login();
        $change_db->change_db($organization_details[0]->organization_database);
        $database_name = $organization_details[0]->organization_database;

        $client = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `client_id` = '$client_id' AND `deleted` = '0'");
        $client_name = $client[0]->client_name;
        $next_expiration_date = $client[0]->next_expiration_date;
        $freeze_date = date("Ymd",strtotime($client[0]->freeze_date)) > date("Ymd") ? date("Ymd",strtotime($client[0]->freeze_date)) : date("Ymd");
        $client_freeze_untill = $client[0]->client_freeze_untill;
        // return $next_expiration_date;
        // take the difference of todays date and the client freeze date
        // $$next_expiration_date = 0;//days

        $full_days = "";
        if ($freeze_date < $client_freeze_untill) {
            $date1=date_create($freeze_date);
            $date2=date_create($client_freeze_untill);
            $diff=date_diff($date1,$date2);
            $days =  $diff->format("-%a days");
            $full_days = $days;
            $date=date_create($next_expiration_date);
            date_add($date,date_interval_create_from_date_string($days));
            $next_expiration_date = date_format($date,"YmdHis");
        }

        // update the client freeze status deactivated status to 
        DB::connection("mysql2")->table('client_tables')
        ->where('client_id', $client_id)
        ->update([
            'client_freeze_status' => "0",
            'next_expiration_date' => $next_expiration_date,
            'client_freeze_untill' => "",
            'date_changed' => date("YmdHis"),
            'payments_status' => '1',
            'freeze_date' => date("YmdHis",strtotime("-1 day"))
        ]);

        // send the client message on unfreeze
        $message_contents = $this->get_sms();
        if (count($message_contents) > 4) {
            $messages = $message_contents[5]->messages;

            // get the messages for freezing clients
            $message = "";
            for ($index=0; $index < count($messages); $index++) {
                if ($messages[$index]->Name == "account_unfrozen") {
                    $message = $messages[$index]->message;
                }
            }

            if (strlen($message) > 0 && $message != null) {
                // send the message
                // change the tags first
                $new_message = $this->message_content($message,$client_id,null);

                // get the sms keys
                $sms_keys = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'sms_api_key'");
                $sms_api_key = $sms_keys[0]->value;
                $sms_keys = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'sms_partner_id'");
                $sms_partner_id = $sms_keys[0]->value;
                $sms_keys = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'sms_shortcode'");
                $sms_shortcode = $sms_keys[0]->value;
                $partnerID = $sms_partner_id;
                $apikey = $sms_api_key;
                $shortcode = $sms_shortcode;
                
                
                $client_id = $client_id;
                $mobile = $client[0]->clients_contacts;
                $sms_type = 2;
                $message = $new_message;
                
                $trans_amount = 0;
                $finalURL = "https://isms.celcomafrica.com/api/services/sendsms/?apikey=" . urlencode($apikey) . "&partnerID=" . urlencode($partnerID) . "&message=" . urlencode($message) . "&shortcode=$shortcode&mobile=$mobile";
                $ch = \curl_init();
                \curl_setopt($ch, CURLOPT_URL, $finalURL);
                \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                \curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $response = \curl_exec($ch);
                \curl_close($ch);
                $res = json_decode($response,true);
                // return $res;
                $message_status = 0;
                $values = !isset($res['responses']) ? $res['response-code'] : $res['response'][0];
                // return $values;
                if (is_array($values)) {
                    foreach ($values as  $key => $value) {
                        // echo $key;
                        if ($key == "response-code") {
                            if ($value == "200") {
                                // if its 200 the message is sent delete the
                                $message_status = 1;
                            }
                        }
                    }
                }

                // if the message status is one the message is already sent to the user
                $now = date("YmdHis");
                $insert = DB::insert("INSERT INTO `sms_tables` (`sms_content`, `date_sent`, `recipient_phone`, `sms_status`, `account_id`, `sms_type`) VALUES (?,?,?,?,?,?)",
                                        [$message,$now,$mobile,$message_status,$client_id,$sms_type]);
            }
        }

        $txt = ":Client ( $client_name ) freeze status changed to in-active by ".session('Usernames').""."!";
        // $this->log($txt);
        // end of log file
        session()->flash("success","Client Unfrozen successfully".($full_days != "" ? " and ".$full_days." has been deducted to the expiration date":"")."!");
        return redirect(route("viewOrganizationClient",[$organization_id, $client_id]));
    }

    // set refferal information
    function setRefferal($organization_id, Request $req)
    {
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization_details) == 0) {
            session()->flash("error", "Invalid organization!");
            return redirect(route("Organizations"));
        }

        // change db
        $change_db = new login();
        $change_db->change_db($organization_details[0]->organization_database);

        // return $req->input();
        // get the user refferal information if there is any
        $user_id = $req->input('clients_id');
        $refferal_account_no = $req->input('refferal_account_no');
        $refferer_amount = $req->input("refferer_amount");
        $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `client_id` = '" . $user_id . "' AND `deleted` = '0'");
        $refferer_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `client_account` = '" . $refferal_account_no . "' AND `deleted` = '0'");
        if (count($client_data) > 0 && count($refferer_data) > 0) {
            $user_refferal = $client_data[0]->reffered_by;
            // check if there is anyone who reffered them by getting the str len
            if (strlen(trim($user_refferal)) > 0) {
                // if there is a refferal set
                $user_refferal = str_contains($user_refferal, "\\") === true ? trim(str_replace("\\", "", $user_refferal)) : trim($user_refferal);
                $user_refferal = substr($user_refferal, 0, 1) == "\"" ? substr($user_refferal, 1, (strlen($user_refferal) - 2)) : $user_refferal;
                $user_refferal = str_replace("'", "\"", $user_refferal);
                $reffered_by = json_decode($user_refferal);
                $reffered_by->client_acc = $refferal_account_no;
                $reffered_by->monthly_payment = $refferer_amount;
                // update the table and set the refferer information
                DB::connection("mysql2")->table('client_tables')
                    ->where('client_id', $user_id)
                    ->update([
                        'reffered_by' => json_encode($reffered_by),
                        "date_changed" => date("YmdHis")
                    ]);
                // return $json_data;
                session()->flash("success", "" . $client_data[0]->client_name . " refferer is set to " . $refferer_data[0]->client_name . " and will recieve Kes " . number_format($refferer_amount) . "!");

                // log message
                $txt = $client_data[0]->client_name . " - " . $client_data[0]->client_account . " refferer is updated to " . $refferer_data[0]->client_name . " and will recieve Kes " . number_format($refferer_amount) . " by " . session('Usernames') . "!";
                $this->log($txt);
                // end of log file
                return redirect(url()->previous());
            } else {
                // create a new refferal
                $string = "{\"client_acc\":\"unknown\",\"monthly_payment\":0,\"payment_history\":[]}";
                $json_data = json_decode($string);
                $json_data->client_acc = $refferal_account_no;
                $json_data->monthly_payment = $refferer_amount;
                // update the table and set the refferer information
                DB::connection("mysql2")->table('client_tables')
                    ->where('client_id', $user_id)
                    ->update([
                        'reffered_by' => json_encode($json_data),
                        'date_changed' => date("YmdHis")
                    ]);
                // return $json_data;
                session()->flash("success", "" . $client_data[0]->client_name . " refferer is set  to " . $refferer_data[0]->client_name . " and will recieve Kes " . number_format($refferer_amount) . "!");

                // log message
                $txt = $client_data[0]->client_name . " - " . $client_data[0]->client_account . " refferer is set to " . $refferer_data[0]->client_name . " and will recieve Kes " . number_format($refferer_amount) . " by " . session('Usernames') . "!";
                $this->log($txt);

                // end of log file
                return redirect(url()->previous());
            }
        }
    }

    // get refferal
    function getRefferal($organization_id, $client_account)
    {
        // organization id
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization_details) == 0) {
            session()->flash("error", "Invalid organization!");
            return redirect(route("Organizations"));
        }

        // change db
        $change_db = new login();
        $change_db->change_db($organization_details[0]->organization_database);

        $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `client_account` = '$client_account' AND `deleted` = '0'");
        if (count($client_data) > 0) {
            return $client_data[0]->client_name . ":" . $client_data[0]->client_account . ":" . $client_data[0]->wallet_amount . ":" . $client_data[0]->client_address;
        } else {
            return "Invalid User!";
        }
    }

    function initiate_stk($organization_id, Request $request){
        $phone = $request->input("phone_number");
        $amount = $request->input("amount");
        $acc_no = $request->input("account_number");

        
        $phone = $this->formatKenyanPhone($phone);

        if($phone == null){
            return "<p class='text-danger'>Please enter a valid phone number</p>";
        }

        if ($amount < 1) {
            return "<p class='text-danger'>Please enter a valid amount</p>";
        }

        // SEND STK PUSH VERSION 1
        $mpesa = new MpesaService($organization_id);
        $response = $mpesa->stkPush($phone, $amount, $acc_no, "Payment for $acc_no", "v1");
        $response = $this->handleStkPushResponse($response);
        if ($response['status'] == "success") {
            return "<p class='text-success'>".$response['message']."</p>";
        }
        if ($response['status'] == "error" || $response['status'] == "unknown") {
            // SEND WITH VERSION 2
            $response = $mpesa->stkPush($phone, $amount, $acc_no, "Payment for $acc_no", "v2");
            $response = $this->handleStkPushResponse($response);
            if ($response['status'] == "success") {
                return "<p class='text-success'>".$response['message']."</p>";
            }
            return "<p class='text-danger'>".$response['message']."</p>";
        }
        
        return $response;
    }
    function handleStkPushResponse($response)
    {
        // Convert JSON string to array if needed
        if (is_string($response)) {
            $response = json_decode($response, true);
        }

        // Handle invalid JSON
        if ($response === null) {
            return [
                'status'  => 'error',
                'message' => 'Invalid JSON response',
                'data'    => null,
            ];
        }

        // Check for error response
        if (isset($response['errorCode'])) {
            return [
                'status'  => 'error',
                'code'    => $response['errorCode'],
                'message' => $response['errorMessage'] ?? 'Unknown error occurred',
                'data'    => $response,
            ];
        }

        // Check for success response
        if (isset($response['ResponseCode']) && $response['ResponseCode'] == "0") {
            return [
                'status'   => 'success',
                'code'     => $response['ResponseCode'],
                'message'  => $response['CustomerMessage'] ?? 'Request accepted',
                'requestId'=> $response['MerchantRequestID'] ?? null,
                'checkoutId'=> $response['CheckoutRequestID'] ?? null,
                'data'     => $response,
            ];
        }

        // If neither errorCode nor success is present, return unknown
        return [
            'status'  => 'unknown',
            'message' => 'Unexpected response format',
            'data'    => $response,
        ];
    }
    
    function update_client_comment($organization_id, Request $request){
        // organization id
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization_details) == 0) {
            session()->flash("error", "Invalid organization!");
            return redirect(route("Organizations"));
        }

        // change db
        $change_db = new login();
        $change_db->change_db($organization_details[0]->organization_database);

        $clients_id = $request->input("clients_id");
        $comments = $request->input("comments");

        // update the comment
        $update = DB::connection("mysql2")->update("UPDATE client_tables SET comment = ? WHERE client_id = ?", [$comments, $clients_id]);
        session()->flash("success", "Comment has been updated successfully!");
        return redirect(url()->previous());
    }

    // update freeze date
    function set_freeze_date(Request $req, $organization_id){
        // organization id
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization_details) == 0) {
            session()->flash("error", "Invalid organization!");
            return redirect(route("Organizations"));
        }

        // change db
        $change_db = new login();
        $change_db->change_db($organization_details[0]->organization_database);
        $database_name = $organization_details[0]->organization_database;

        // return $req;
        if ($req->input("freeze_date") == "freeze_now") {
            $freeze_type = $req->input("freeze_type");
            $indefinate_freezing = $req->input("indefinate_freezing");
    
            // message contents
            $message_contents = $this->get_sms();
            // return $message_contents;
            // get difference in todays date and the day selected
            $date_today = date_create(date("Y-m-d"));
            // return $date_today;
            $selected_date = date_create($req->input('freez_dates_edit'));
            $diff=date_diff($date_today,$selected_date);
            $days = $diff->format("%R %a days");
            $day_frozen = $diff->format("%a");
            $client_id = $req->input('clients_id');
    
            // get the clients expiration date and add the days
            $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `client_id` = '".$client_id."'");
    
            // add the days you got to the expiration dates
            $next_expiration_date = $client_data[0]->next_expiration_date;
            $date1 = date("YmdHis", strtotime($next_expiration_date.''.$days));
    
            // freeze date
            $freeze_date = $freeze_type == "definate" ? date("YmdHis",strtotime($req->input('freez_dates_edit'))) : $indefinate_freezing;
            // return $freeze_date;
    
            // update the freeze data and the freeze status and the expiration date
            DB::connection("mysql2")->table('client_tables')
            ->where('client_id', $client_id)
            ->update([
                'client_freeze_status' => "1",
                'client_freeze_untill' => $freeze_date,
                'next_expiration_date' => $date1,
                'date_changed' => date("YmdHis"),
                'payments_status' => '0',
                'freeze_date' => date("YmdHis")
            ]);
            if ($freeze_type == "definate") {
                session()->flash("success","".$client_data[0]->client_name." will be frozen for $days untill ".date("dS M Y ",strtotime($freeze_date))."!");
            }else{
                session()->flash("success","".$client_data[0]->client_name." will be frozen Indefinately! You will activate them when they return back");
            }
    
            // send message to the client
            // [client_f_name]
            $message_contents = $this->get_sms();
            if (count($message_contents) > 4) {
                $messages = $message_contents[5]->messages;
    
                // get the messages for freezing clients
                $message = "";
                for ($index=0; $index < count($messages); $index++) {
                    if ($messages[$index]->Name == "account_frozen") {
                        $message = $messages[$index]->message;
                    }
                }
    
                if (strlen($message) > 0 && $message != null) {
                    // send the message
                    // change the tags first
                    $day_frozen = $freeze_type == "definate" ? $day_frozen : "Indefinite";
                    $freeze_date = $freeze_date != "00000000000000" ? $freeze_date : "Indefinite";
                    $new_message = $this->message_content($message,$client_id,null,$day_frozen,$freeze_date);
    
                    // get the sms keys
                    $sms_keys = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'sms_api_key'");
                    $sms_api_key = $sms_keys[0]->value;
                    $sms_keys = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'sms_partner_id'");
                    $sms_partner_id = $sms_keys[0]->value;
                    $sms_keys = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'sms_shortcode'");
                    $sms_shortcode = $sms_keys[0]->value;
                    $partnerID = $sms_partner_id;
                    $apikey = $sms_api_key;
                    $shortcode = $sms_shortcode;
                    
                    
                    $client_id = $client_id;
                    $mobile = $client_data[0]->clients_contacts;
                    $sms_type = 2;
                    $message = $new_message;
                    
                    $trans_amount = 0;
                    $finalURL = "https://isms.celcomafrica.com/api/services/sendsms/?apikey=" . urlencode($apikey) . "&partnerID=" . urlencode($partnerID) . "&message=" . urlencode($message) . "&shortcode=$shortcode&mobile=$mobile";
                    $ch = \curl_init();
                    \curl_setopt($ch, CURLOPT_URL, $finalURL);
                    \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    \curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    $response = \curl_exec($ch);
                    \curl_close($ch);
                    $res = json_decode($response,true);

                    // return $res;
                    $message_status = 1;
                    $values = !isset($res['responses']) ? $res['response-code'] : null;
                    // return $values;
                    // if (is_array($values)) {
                    //     foreach ($values as  $key => $value) {
                    //         // echo $key;
                    //         if ($key == "response-code") {
                    //             if ($value == "200") {
                    //                 // if its 200 the message is sent delete the
                    //                 $message_status = 1;
                    //             }
                    //         }
                    //     }
                    // }
    
                    // if the message status is one the message is already sent to the user
                    $now = date("YmdHis");
                    $insert = DB::insert("INSERT INTO `sms_tables` (`sms_content`, `date_sent`, `recipient_phone`, `sms_status`, `account_id`, `sms_type`) VALUES (?,?,?,?,?,?)",
                                        [$message,$now,$mobile,$message_status,$client_id,$sms_type]);
                }
            }
            // log message
            if ($freeze_type == "definate"){
                $txt = $client_data[0]->client_name." has been frozen for $days untill ".date("dS M Y ",strtotime($freeze_date))." by ".session('Usernames')."!";
            }else{
                $txt = $client_data[0]->client_name." has been frozen for Indefinately by ".session('Usernames')."!";
            }
            // log txt
            // $this->log($txt);
            // end of log file
            return redirect(url()->previous());
        }else{
            // return $req;
            $freeze_type = $req->input("freeze_type");
            $indefinate_freezing = $req->input("indefinate_freezing");
            $freezing_date = date("YmdHis",strtotime($req->input("freezing_date")));
            $freez_dates_edit = date("YmdHis",strtotime($req->input("freez_dates_edit")));
            $client_id = $req->input('clients_id');
            
            // check if its definate and has the unfreeze date more than the start date
            if ($freeze_type == "definate" && $freezing_date > $freez_dates_edit) {
                session()->flash("error","The date the client should be frozen should not be greater than the day the freezing ends!");
                return redirect(url()->previous());
            }

            // get difference in todays date and the day selected
            $date_today = date_create(date("Y-m-d"));
            $frozen_dates = date_create($freezing_date);

            // return $freezing_date;
            $selected_date = date_create($req->input('freez_dates_edit'));
            $diff=date_diff($frozen_dates,$selected_date);
            $days = $diff->format("%R %a days");
            $day_frozen = $diff->format("%a");
            // return $days;
    
            // get the clients expiration date and add the days
            $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `client_id` = '".$client_id."'");
    
            // add the days you got to the expiration dates
            $next_expiration_date = $client_data[0]->next_expiration_date;
            $date1 = date("YmdHis", strtotime($next_expiration_date.''.$days));
    
            // freeze date
            $freeze_date = $freeze_type == "definate" ? date("YmdHis",strtotime($req->input('freez_dates_edit'))) : $indefinate_freezing;
            // return $freeze_date;
    
            // update the freeze data and the freeze status and the expiration date
            DB::connection("mysql2")->table('client_tables')
            ->where('client_id', $client_id)
            ->update([
                'client_freeze_status' => "0",
                'client_freeze_untill' => $freeze_date,
                'next_expiration_date' => $date1,
                'date_changed' => date("YmdHis"),
                'payments_status' => '1',
                'freeze_date' => $freezing_date
            ]);
            if ($freeze_type == "definate") {
                session()->flash("success","".$client_data[0]->client_name." will be frozen on ".date("D dS M Y",strtotime($freezing_date))." for $days untill ".date("dS M Y ",strtotime($freeze_date))."!");
            }else{
                session()->flash("success","".$client_data[0]->client_name." will be frozen on ".date("D dS M Y",strtotime($freezing_date))." Indefinately! You will activate them when they return back");
            }
    
            // send message to the client
            // [client_f_name]
            $message_contents = $this->get_sms();
            if (count($message_contents) > 4) {
                $messages = $message_contents[5]->messages;
    
                // get the messages for freezing clients
                $message = "";
                for ($index=0; $index < count($messages); $index++) {
                    if ($messages[$index]->Name == "future_account_freeze") {
                        $message = $messages[$index]->message;
                    }
                }
    
                if (strlen($message) > 0 && $message != null) {
                    // change the tags first
                    $day_frozen = $freeze_type == "definate" ? $day_frozen : "Indefinite";
                    $freeze_date = $freeze_date != "00000000000000" ? $freeze_date : "Indefinite";
                    $new_message = $this->message_content($message,$client_id,null,$day_frozen,$freeze_date,$freezing_date);
    
                    // get the sms keys
                    $sms_keys = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'sms_api_key'");
                    $sms_api_key = $sms_keys[0]->value;
                    $sms_keys = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'sms_partner_id'");
                    $sms_partner_id = $sms_keys[0]->value;
                    $sms_keys = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'sms_shortcode'");
                    $sms_shortcode = $sms_keys[0]->value;
                    $partnerID = $sms_partner_id;
                    $apikey = $sms_api_key;
                    $shortcode = $sms_shortcode;
                    
                    
                    $client_id = $client_id;
                    $mobile = $client_data[0]->clients_contacts;
                    $sms_type = 2;
                    $message = $new_message;
                    
                    $trans_amount = 0;
                    $finalURL = "https://isms.celcomafrica.com/api/services/sendsms/?apikey=" . urlencode($apikey) . "&partnerID=" . urlencode($partnerID) . "&message=" . urlencode($message) . "&shortcode=$shortcode&mobile=$mobile";
                    $ch = \curl_init();
                    \curl_setopt($ch, CURLOPT_URL, $finalURL);
                    \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    \curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    $response = \curl_exec($ch);
                    \curl_close($ch);
                    $res = json_decode($response,true);

                    // return $res;
                    $message_status = 1;
                    $values = !isset($res['responses']) ? $res['response-code'] : null;
                    // return $values;
                    // if (is_array($values)) {
                    //     foreach ($values as  $key => $value) {
                    //         // echo $key;
                    //         if ($key == "response-code") {
                    //             if ($value == "200") {
                    //                 // if its 200 the message is sent delete the
                    //                 $message_status = 1;
                    //             }
                    //         }
                    //     }
                    // }
                    
                    // if the message status is one the message is already sent to the user
                    $now = date("YmdHis");
                    $insert = DB::insert("INSERT INTO `sms_tables` (`sms_content`, `date_sent`, `recipient_phone`, `sms_status`, `account_id`, `sms_type`) VALUES (?,?,?,?,?,?)",
                                        [$message,$now,$mobile,$message_status,$client_id,$sms_type]);
                }
            }

            if ($freeze_type == "definate"){
                $txt = $client_data[0]->client_name." will be frozen on ".date("D dS M Y",strtotime($freezing_date))." for $days untill ".date("dS M Y ",strtotime($freeze_date)).". Action done by ".session('Usernames')."!";
            }else{
                $txt = $client_data[0]->client_name." will be frozen on ".date("D dS M Y",strtotime($freezing_date))." Indefinately. Action done by ".session('Usernames')."!";
            }
            return redirect(url()->previous());
        }
    }
	function get_sms(){
        // change db
        $change_db = new login();
        $change_db->change_db();

        $data = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `keyword` = 'Messages' AND `deleted` = '0'");
        return json_decode($data[0]->value);
	}
	function message_content($data,$user_id,$trans_amount,$freeze_days = null,$freeze_date = null,$future_freeze_date = null) {
        $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `client_id` = '$user_id' AND `deleted` = '0'");
		$exp_date = $client_data[0]->next_expiration_date;
		$reg_date = $client_data[0]->clients_reg_date;
		$monthly_payment = $client_data[0]->monthly_payment;
		$full_name = $client_data[0]->client_name;
        $f_name = ucfirst(strtolower((explode(" ",$full_name)[0])));
		$address = $client_data[0]->client_address;
		$internet_speeds = $client_data[0]->max_upload_download;
		$contacts = $client_data[0]->clients_contacts;
		$account_no = $client_data[0]->client_account;
		$wallet = $client_data[0]->wallet_amount;
		$username = $client_data[0]->client_username;
		$password = $client_data[0]->client_password;
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
		$data = str_replace("[int_speeds]", $internet_speeds, $data);
		$data = str_replace("[monthly_fees]", "Ksh ".$monthly_payment, $data);
		$data = str_replace("[client_phone]", $contacts, $data);
		$data = str_replace("[acc_no]", $account_no, $data);
		$data = str_replace("[client_wallet]", "Ksh ".$wallet, $data);
		$data = str_replace("[username]", $username, $data);
		$data = str_replace("[password]", $password, $data);
		$data = str_replace("[trans_amnt]", "Ksh ".$trans_amount, $data);
		$data = str_replace("[today]", $today, $data);
		$data = str_replace("[now]", $now,$data);
		$data = str_replace("[days_frozen]", $freeze_days." Day(s)",$data);
		$data = str_replace("[frozen_date]", date("D dS M Y",strtotime($future_freeze_date)),$data);
		$data = str_replace("[unfreeze_date]", ($freeze_date == "Indefinite" ? "Indefinite Date" : date("dS M Y \a\\t h:iA",strtotime($freeze_date))),$data);
		return $data;
	}

    // deactivate user from freeze
    function deactivatefreeze($organization_id, $client_id)
    {
        // organization id
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization_details) == 0) {
            session()->flash("error", "Invalid organization!");
            return redirect(route("Organizations"));
        }

        // change db
        $change_db = new login();
        $change_db->change_db($organization_details[0]->organization_database);

        $client = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `client_id` = '$client_id' AND `deleted` = '0'");
        $client_name = $client[0]->client_name;
        $next_expiration_date = $client[0]->next_expiration_date;
        $freeze_date = $client[0]->freeze_date != null ? date("YmdHis", strtotime($client[0]->freeze_date)) : date("YmdHis");
        $client_freeze_untill = $client[0]->client_freeze_untill;

        $full_days = "";
        if ($freeze_date < $client_freeze_untill) {
            $date1 = date_create($freeze_date);
            $date2 = date_create($client_freeze_untill);
            $diff = date_diff($date1, $date2);
            $days =  $diff->format("-%a days");
            $full_days = $days;
            $date = date_create($next_expiration_date);
            date_add($date, date_interval_create_from_date_string($days));
            $next_expiration_date = date_format($date, "YmdHis");
        } else {
            // take the freeze date and the date today 
            // and get the difference and 
            // the number of days got should be added to the expiry date
            $today = date("YmdHis");
            $date1 = date_create($freeze_date);
            $date2 = date_create($today);
            $diff = date_diff($date1, $date2);
            $days =  $diff->format("%a");

            // add the date
            if ($days > 0) {
                // add the days to the expiry date
                $next_expiration_date = $this->addDaysToDate($next_expiration_date, $days);
            }
        }

        // update the client freeze status deactivated status to 
        DB::connection("mysql2")->table('client_tables')
            ->where('client_id', $client_id)
            ->update([
                'client_freeze_status' => "0",
                'next_expiration_date' => $next_expiration_date,
                'client_freeze_untill' => "",
                'date_changed' => date("YmdHis"),
                'payments_status' => '1',
                'freeze_date' => date("YmdHis", strtotime("-1 day"))
            ]);

        // send the client message on unfreeze
        $message_contents = $this->get_sms();
        if (count($message_contents) > 4) {
            $messages = $message_contents[5]->messages;

            // get the messages for freezing clients
            $message = "";
            for ($index = 0; $index < count($messages); $index++) {
                if ($messages[$index]->Name == "account_unfrozen") {
                    $message = $messages[$index]->message;
                }
            }

            if (strlen($message) > 0 && $message != null) {
                // send the message
                // change the tags first
                $new_message = $this->message_content($message, $client_id, null);

                // get the sms keys
                $sms_keys = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'sms_api_key'");
                $sms_api_key = $sms_keys[0]->value;
                $sms_keys = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'sms_partner_id'");
                $sms_partner_id = $sms_keys[0]->value;
                $sms_keys = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'sms_shortcode'");
                $sms_shortcode = $sms_keys[0]->value;
                $select = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `keyword` = 'sms_sender'");
                $sms_sender = count($select) > 0 ? $select[0]->value : "";
                $partnerID = $sms_partner_id;
                $apikey = $sms_api_key;
                $shortcode = $sms_shortcode;


                // $client_id = $client_id;
                $mobile = $client[0]->clients_contacts;
                $sms_type = 2;
                $message = $new_message;
                $message_status = 1;

                // if the message status is one the message is already sent to the user
                $sms_table = new sms_table();
                $sms_table->sms_content = $message;
                $sms_table->date_sent = date("YmdHis");
                $sms_table->recipient_phone = $mobile;
                $sms_table->sms_status = $message_status;
                $sms_table->account_id = $client_id;
                $sms_table->sms_type = $sms_type;
                $sms_table->save();
            }
        }

        $txt = ":Client ( " . $client_name . " - " . $client[0]->client_account . " ) freeze status changed to in-active by " . session('Usernames') . "" . "!";
        $this->log($txt);
        // end of log file
        session()->flash("success", "Client Unfrozen successfully" . ($full_days != "" ? " and " . $full_days . " has been deducted to the expiration date" : "") . "!");
        return redirect(url()->previous());
    }
    
    function addDaysToDate($date, $days)
    {
        // Create a DateTime object from the given date
        $dateTime = new DateTime($date);

        // Create a DateInterval object for the specified number of days
        $interval = new DateInterval('P' . $days . 'D');

        // Add the interval to the date
        $dateTime->add($interval);

        // Return the modified date as a string
        return $dateTime->format('YmdHis');
    }

    function delete_user($organization_id, $user_id){
        // return $clientid;
        // organization id
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization_details) == 0) {
            session()->flash("error", "Invalid organization!");
            return redirect(route("Organizations"));
        }

        // change db
        $change_db = new login();
        $change_db->change_db($organization_details[0]->organization_database);
        $database_name = $organization_details[0]->organization_database;

        // get the user information
        $user_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_id` = '$user_id'");
        if (count($user_data) > 0) {
            if ($user_data[0]->assignment == "static") {

                // check if the routers are the same
                // if not proceed and disable the router profile
                // get the router data
                $router_id =  $user_data[0]->router_name;
                $client_name = $user_data[0]->client_name;
                $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `router_id` = ? AND `deleted` = '0'",[$router_id]);
                if (count($router_data) > 0) {
                    // disable the interface in that router
            
                    // get the sstp credentails they are also the api usernames
                    $sstp_username = $router_data[0]->sstp_username;
                    $sstp_password = $router_data[0]->sstp_password;
                    $api_port = $router_data[0]->api_port;
                    

                    // connect to the router and set the sstp client
                    $sstp_value = $this->getSSTPAddress($database_name);
                    if ($sstp_value == null) {
                        $error = "The SSTP server is not set, Contact your administrator!";
                        session()->flash("error",$error);
                        return redirect(url()->previous());
                    }

                    // connect to the router and set the sstp client
                    $server_ip_address = $sstp_value->ip_address;
                    $user = $sstp_value->username;
                    $pass = $sstp_value->password;
                    $port = $sstp_value->port;

                    // check if the router is actively connected
                    $client_router_ip = $this->checkActive($server_ip_address,$user,$pass,$port,$sstp_username);
                    $API = new routeros_api();
                    $API->debug = false;
    
                    $router_secrets = [];
                    if ($API->connect($client_router_ip, $sstp_username, $sstp_password, $api_port)){
                        // get the IP ADDRES
                        $curl_handle = curl_init();
                        $url = "https://crontab.hypbits.com/getIpaddress.php?db_name=".session("database_name")."&r_id=".$router_id."&r_ip=true";
                        curl_setopt($curl_handle, CURLOPT_URL, $url);
                        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
                        $curl_data = curl_exec($curl_handle);
                        curl_close($curl_handle);
                        $router_ip_addresses = json_decode($curl_data, true);
                        // save the router ip address
                        $ip_addresses = $router_ip_addresses;

                    
                        // get the SIMPLE QUEUES
                        $curl_handle = curl_init();
                        $url = "https://crontab.hypbits.com/getIpaddress.php?db_name=".session("database_name")."&r_id=".$router_id."&r_queues=true";
                        curl_setopt($curl_handle, CURLOPT_URL, $url);
                        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
                        $curl_data = curl_exec($curl_handle);
                        curl_close($curl_handle);
                        $router_simple_queues = json_decode($curl_data, true);

                        $simple_queues = $router_simple_queues;
                        $subnet = explode("/",$user_data[0]->client_default_gw);
                        
                        // loop through the ip addresses and get the clents ip address id
                        $client_network = $user_data[0]->client_network;
                        $ip_id = false;
                        foreach ($ip_addresses as $key => $ip_address) {
                            if($client_network == $ip_address['network']){
                                $ip_id = $ip_address['.id'];
                                break;
                            }
                        }

                        // remove the id
                        if ($ip_id) {
                            // remove
                            $API->comm("/ip/address/remove", array(
                                ".id" => $ip_id
                            ));
                        }

                        // loopt through the simple queues and get the queue to remove
                        $queue_ip = $client_network."/".$subnet[1];
                        $queue_id = false;
                        foreach ($simple_queues as $key => $queue) {
                            if($queue['target'] == $queue_ip){
                                $queue_id = $queue['.id'];
                                break;
                            }
                        }

                        // remove the queue
                        if($queue_id){
                            $API->comm("/queue/simple/remove", array(
                                ".id" => $queue_id
                            ));
                        }
                    }
                }
                DB::connection("mysql2")->update("DELETE FROM `client_tables` WHERE `client_id` = ?",[$user_id]);
                session()->flash("success",".".$client_name." has been deleted successfully!");

                // log message
                $txt = ":Client (".$client_name.") has been deleted by ".session('Usernames')."!";
                // $this->log($txt);
                // end of log file
                return redirect(route("viewOrganizationClients",[$organization_id]));
            }elseif ($user_data[0]->assignment == "pppoe"){
                // get the router data
                $router_id = $user_data[0]->router_name;
                $client_name = $user_data[0]->client_name;
                $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `router_id` = ? AND `deleted` = '0'",[$user_data[0]->router_name]);
                if (count($router_data) > 0) {
                    // get the sstp credentails they are also the api usernames
                    $sstp_username = $router_data[0]->sstp_username;
                    $sstp_password = $router_data[0]->sstp_password;
                    $api_port = $router_data[0]->api_port;

                    // connect to the router and set the sstp client
                    $sstp_value = $this->getSSTPAddress($database_name);
                    if ($sstp_value == null) {
                        $error = "The SSTP server is not set, Contact your administrator!";
                        session()->flash("error",$error);
                        return redirect(url()->previous());
                    }

                    // connect to the router and set the sstp client
                    $server_ip_address = $sstp_value->ip_address;
                    $user = $sstp_value->username;
                    $pass = $sstp_value->password;
                    $port = $sstp_value->port;

                    // check if the router is actively connected
                    $client_router_ip = $this->checkActive($server_ip_address,$user,$pass,$port,$sstp_username);
                    // return $client_router_ip;
                    $API = new routeros_api();
                    $API->debug = false;
    
                    $router_secrets = [];
                    $active_connections = [];
                    if ($API->connect($client_router_ip, $sstp_username, $sstp_password, $api_port)){
                        // get the secret details
                        $secret_name = $user_data[0]->client_secret;
                        // get the IP ADDRES
                        $curl_handle = curl_init();
                        $url = "https://crontab.hypbits.com/getIpaddress.php?db_name=".session("database_name")."&r_id=".$user_data[0]->router_name."&r_active_secrets=true";
                        curl_setopt($curl_handle, CURLOPT_URL, $url);
                        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
                        $curl_data = curl_exec($curl_handle);
                        curl_close($curl_handle);
                        $active_connections = json_decode($curl_data, true);
                        
                        // get the IP ADDRES
                        $curl_handle = curl_init();
                        $url = "https://crontab.hypbits.com/getIpaddress.php?db_name=".session("database_name")."&r_id=".$user_data[0]->router_name."&r_ppoe_secrets=true";
                        curl_setopt($curl_handle, CURLOPT_URL, $url);
                        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
                        $curl_data = curl_exec($curl_handle);
                        curl_close($curl_handle);
                        $router_secrets = json_decode($curl_data, true);

                        // router secrets
                        $secret_id = false;
                        foreach ($router_secrets as $key => $router_secret) {
                            if ($router_secret['name'] == $secret_name) {
                                $secret_id = $router_secret['.id'];
                                break;
                            }
                        }

                        // disable the secret
                        if ($secret_id) {
                            $API->comm("/ppp/secret/remove", array(
                                ".id" => $secret_id
                            ));
                        }

                        $active_id = false;
                        foreach ($active_connections as $key => $connection) {
                            if($connection['name'] == $secret_name){
                                $active_id = $connection['.id'];
                            }
                        }
    
                        if ($active_id) {
                            // remove the active connection if there is, it will do nothing if the id is not present
                            $API->comm("/ppp/active/remove", array(
                                ".id" => $active_id
                            ));
                        }
                    }
                }

                // DB::connection("mysql2")->delete("DELETE FROM `client_tables` WHERE `deleted` = '0' AND `client_id` = ".$user_id."");
                DB::connection("mysql2")->update("DELETE FROM `client_tables` WHERE `client_id` = ?",[$user_id]);
                session()->flash("success",".".$client_name." has been deleted successfully!");

                // log message
                $txt = ":Client (".$client_name.") has been deleted by ".session('Usernames')."!";
                // $this->log($txt);
                return redirect(route("viewOrganizationClients",[$organization_id]));
            }
        }else{
            session()->flash("error","User not found!");
            return redirect(route("viewOrganizationClients",[$organization_id]));
        }
    }

    // set minimum pay
    function set_minimum_pay(Request $request,$organization_id,$client_id){
        // organization id
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization_details) == 0) {
            session()->flash("error", "Invalid organization!");
            return redirect(route("Organizations"));
        }

        // change_db
        $change_db = new login();
        $change_db->change_db($organization_details[0]->organization_database);
        $database_name = $organization_details[0]->organization_database;

        // return $request;
        $client_id = $request->input("client_id");
        $change_minimum_payment = $request->input("change_minimum_payment");

        // update the clients minimum pay
        $update = DB::connection("mysql2")->update("UPDATE `client_tables` SET `min_amount` = ? WHERE `client_id` = ?",[$change_minimum_payment,$client_id]);

        // set a success
        session()->flash("success","Update has been done successfully!");
        return redirect(route("viewOrganizationClient",[$organization_id, $client_id]));
    }

    function change_wallet_balance(Request $req, $organization_id,$client_id){
        // organization id
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization_details) == 0) {
            session()->flash("error", "Invalid organization!");
            return redirect(route("Organizations"));
        }

        // change_db
        $change_db = new login();
        $change_db->change_db($organization_details[0]->organization_database);
        $database_name = $organization_details[0]->organization_database;

        // return $req;
        $client_id = $req->input('clients_id');
        $wallet_amount = $req->input('wallet_amounts');
        DB::connection("mysql2")->table('client_tables')
        ->where('client_id', $client_id)
        ->update([
            'wallet_amount' => $wallet_amount,
            'last_changed' => date("YmdHis"),
            'date_changed' => date("YmdHis")
        ]);

        $client = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `client_id` = '$client_id' AND `deleted` = '0'");
        $client_name = $client[0]->client_name;
        
        $txt = ":Client ( $client_name ) wallet balance has been changed to Kes $wallet_amount by ".session('Usernames').""."!";
        // $this->log($txt);
        // end of log file
        session()->flash("success","Wallet balance has been successfully changed!");
        return redirect(route("viewOrganizationClient",[$organization_id, $client_id]));
    }
    
    // update client
    function update_client(Request $req, $organization_id){
        // organization id
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization_details) == 0) {
            session()->flash("error", "Invalid organization!");
            return redirect(route("Organizations"));
        }

        // change_db
        $change_db = new login();
        $change_db->change_db($organization_details[0]->organization_database);
        $database_name = $organization_details[0]->organization_database;

        $clients_id = $req->input('clients_id');
        // check user assignment 
        $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `client_id` = '".$clients_id."' AND `deleted` = '0'");
        // return $client_data;
        if (count($client_data) > 0) {
            if($client_data[0]->assignment == "static"){
                if(!$req->input("interface_name")){
                    session()->flash("error","Kindly select the interface the client is to be assigned!");
                    return redirect(url()->previous());
                }

                // get the clients details to see if the router is different
                $original_client_dets = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `client_id` = ?;",[$req->input("clients_id")]);
                // return $req;

                if (count($original_client_dets) == 0) {
                    session()->flash("error","Update cannot be done to an invalid user!");
                    return redirect(url()->previous());
                }

                // check if the routers are the same
                if ($original_client_dets[0]->router_name != $req->input("router_name")) {
                    // if not proceed and disable the router profile
                    // get the router data
                    $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `router_id` = ? AND `deleted` = '0'",[$original_client_dets[0]->router_name]);
                    if (count($router_data) > 0) {
                        // disable the interface in that router
                
                        // get the sstp credentails they are also the api usernames
                        $sstp_username = $router_data[0]->sstp_username;
                        $sstp_password = $router_data[0]->sstp_password;
                        $api_port = $router_data[0]->api_port;
                        

                        // connect to the router and set the sstp client
                        $sstp_value = $this->getSSTPAddress($database_name);
                        if ($sstp_value == null) {
                            $error = "The SSTP server is not set, Contact your administrator!";
                            session()->flash("network_presence",$error);
                            return redirect(url()->previous());
                        }

                        // connect to the router and set the sstp client
                        $server_ip_address = $sstp_value->ip_address;
                        $user = $sstp_value->username;
                        $pass = $sstp_value->password;
                        $port = $sstp_value->port;

                        // check if the router is actively connected
                        $client_router_ip = $this->checkActive($server_ip_address,$user,$pass,$port,$sstp_username);
                        $API = new routeros_api();
                        $API->debug = false;
        
                        $router_secrets = [];
                        if ($API->connect($client_router_ip, $sstp_username, $sstp_password, $api_port)){
                            // connection created deactivate the user
                            // get the simple queues
                            $API_2 = new routeros_api();
                            $API_2->debug = false;
                            $ip_addresses = [];
                            if($API_2->connect($client_router_ip, $sstp_username, $sstp_password, $api_port)){
                                // get the IP ADDRES
                                $curl_handle = curl_init();
                                $url = "https://crontab.hypbits.com/getIpaddress.php?db_name=".session("database_name")."&r_id=".$original_client_dets[0]->router_name."&r_ip=true";
                                curl_setopt($curl_handle, CURLOPT_URL, $url);
                                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
                                $curl_data = curl_exec($curl_handle);
                                curl_close($curl_handle);
                                $router_ip_addresses = json_decode($curl_data, true);

                                // save the router ip address
                                $ip_addresses = $router_ip_addresses;
                                
                                // delete the IP ADDRESS FROM WITHIN
                                $client_network = $original_client_dets[0]->client_network;
                                $ip_id = false;
                                foreach ($ip_addresses as $key => $ip_address) {
                                    if($client_network == $ip_address['network']){
                                        $ip_id = $ip_address['.id'];
                                        // remove
                                        $API_2->comm("/ip/address/remove", array(
                                            ".id" => $ip_id
                                        ));
                                        break;
                                    }
                                }
                                $API_2->disconnect();
                            }
                            
                            $router_simple_queues = [];
                            $API_2 = new routeros_api();
                            $API_2->debug = false;
                            if($API_2->connect($client_router_ip, $sstp_username, $sstp_password, $api_port)){
                                // get the IP ADDRES
                                $curl_handle = curl_init();
                                $url = "https://crontab.hypbits.com/getIpaddress.php?db_name=".session("database_name")."&r_id=".$original_client_dets[0]->router_name."&r_queues=true";
                                curl_setopt($curl_handle, CURLOPT_URL, $url);
                                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
                                $curl_data = curl_exec($curl_handle);
                                curl_close($curl_handle);
                                $router_simple_queues = json_decode($curl_data, true);

                                // set the target key for simple queues because this changes in different routers.
                                $target_key = 'target';
                                $first_simple_queues = count($router_simple_queues) > 0 ? $router_simple_queues[0] : null;
                                if ($first_simple_queues != null) {
                                    foreach($first_simple_queues as $key => $simple_queue) {
                                        if ($key == 'address') {
                                            $target_key = $key;
                                        }
                                    }
                                }


                                $subnet = explode("/",$original_client_dets[0]->client_default_gw);
                                // REMOVE THE QUEUE
                                $queue_ip = $client_network."/".$subnet[1];
                                foreach ($router_simple_queues as $key => $queue) {
                                    if($queue[$target_key] == $queue_ip){
                                        $queue_id = $queue['.id'];
                                        $API_2->comm("/queue/simple/remove", array(
                                            ".id" => $queue_id
                                        ));
                                        break;
                                    }
                                }
                                $API_2->disconnect();
                            }

                            // disconnect the api connection
                            $API->disconnect();
                        }
                    }
                }


                // get the client information
                $client_name = $req->input('client_name');
                $client_address = $req->input('client_address');
                $client_phone = $req->input('client_phone');
                $client_monthly_pay = $req->input('client_monthly_pay');
                $client_network = $req->input('client_network');
                $client_gw_name = $req->input('client_gw');
                $upload_speed = $req->input('upload_speed');
                $download_speed = $req->input('download_speed');
                $unit1 = $req->input('unit1');
                $unit2 = $req->input('unit2');
                $router_name = $req->input('router_name');
                $comments = $req->input('comments');
                $client_username = $req->input('client_username');
                $client_password = $req->input('client_password');
                $interface_name = $req->input('interface_name');
                $clients_id = $req->input('clients_id');
                $location_coordinates = $req->input('location_coordinates');
                $client_account_number = $req->input('client_account_number');
                
                // get the ip address and queue list above
                // check if the selected router is connected
                // get the router data
                $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `router_id` = ? AND `deleted` = '0'",[$router_name]);
                if (count($router_data) == 0) {
                    $error = "Router selected does not exist!";
                    session()->flash("error",$error);
                    return redirect(url()->previous());
                }
                
                // get the sstp credentails they are also the api usernames
                $sstp_username = $router_data[0]->sstp_username;
                $sstp_password = $router_data[0]->sstp_password;
                $api_port = $router_data[0]->api_port;

                

                // connect to the router and set the sstp client
                $sstp_value = $this->getSSTPAddress($database_name);
                if ($sstp_value == null) {
                    $error = "The SSTP server is not set, Contact your administrator!";
                    session()->flash("network_presence",$error);
                    return redirect(url()->previous());
                }

                // connect to the router and set the sstp client
                $server_ip_address = $sstp_value->ip_address;
                $user = $sstp_value->username;
                $pass = $sstp_value->password;
                $port = $sstp_value->port;

                // check if the router is actively connected
                $client_router_ip = $this->checkActive($server_ip_address,$user,$pass,$port,$sstp_username);
                // return $client_router_ip;

                // get ip address and queues
                // start with IP address
                // connect to the router and add the ip address and queues to the interface
                $API = new routeros_api();
                $API->debug = false;

                $router_ip_addresses = [];
                $router_simple_queues = [];
                $client_status = $client_data[0]->client_status;
                if ($API->connect($client_router_ip, $sstp_username, $sstp_password, $api_port)){
                    if($req->input('allow_router_changes') == "on"){
                        // get the IP ADDRESS
                        $curl_handle = curl_init();
                        $url = "https://crontab.hypbits.com/getIpaddress.php?db_name=".session("database_name")."&r_id=".$router_name."&r_ip=true";
                        curl_setopt($curl_handle, CURLOPT_URL, $url);
                        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
                        $curl_data = curl_exec($curl_handle);
                        curl_close($curl_handle);
                        $router_ip_addresses = json_decode($curl_data, true);

                        // get the SIMPLE QUEUES
                        $curl_handle = curl_init();
                        $url = "https://crontab.hypbits.com/getIpaddress.php?db_name=".session("database_name")."&r_id=".$router_name."&r_queues=true";
                        curl_setopt($curl_handle, CURLOPT_URL, $url);
                        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
                        $curl_data = curl_exec($curl_handle);
                        curl_close($curl_handle);
                        $router_simple_queues = json_decode($curl_data, true);

                        // set the target key for simple queues because this changes in different routers.
                        $target_key = 'target';
                        $first_simple_queues = count($router_simple_queues) > 0 ? $router_simple_queues[0] : null;
                        if ($first_simple_queues != null) {
                            foreach($first_simple_queues as $key => $simple_queue) {
                                if ($key == 'address') {
                                    $target_key = $key;
                                }
                            }
                        }
    
                        // check if the queues and ip address for the existing clients configuration
                        // if the configurations are present update them accordingly
                        
                        // check if the network is present
                        $old_network = $client_data[0]->client_network;
                        $old_client_gw = $client_data[0]->client_default_gw;
                        $present = 0;
                        $ip_id = 0;
                        foreach ($router_ip_addresses as $key => $value_ip_address) {
                            if ($value_ip_address['network'] == $old_network) {
                                $present = 1;
                                $ip_id = $value_ip_address['.id'];
                                break;
                            }
                        }
    
                        // if present update the network details
                        if ($present == 1) {

                            // connect and get the router ip address and queues
                            $API_2 = new routeros_api();
                            $API_2->debug = false;
                            // set the ip address using its id
                            if($API_2->connect($client_router_ip, $sstp_username, $sstp_password, $api_port)){
                                $result = $API_2->comm("/ip/address/set",
                                array(
                                    "address"     => $req->input('client_gw'),
                                    "disabled" => ($client_status == 0 ? "true" : "false"),
                                    "interface" => $req->input('interface_name'),
                                    "comment"  => $req->input('client_name')." (".$req->input('client_address')." - ".$location_coordinates.") - ".$client_account_number,
                                    ".id" => $ip_id
                                ));
    
                                if(count($result) > 0){
                                    // this means there is an error
                                    $API_2->comm("/ip/address/set",
                                    array(
                                        "interface" => $req->input('interface_name'),
                                        "disabled" => ($client_status == 0 ? "true" : "false"),
                                        "comment"  => $req->input('client_name')." (".$req->input('client_address')." - ".$location_coordinates.") - ".$client_account_number,
                                        ".id" => $ip_id
                                    ));
                                }
                                $API_2->disconnect();
                            }
                        }else{
                            // if the ip address is not present add it!

                            // add a new ip address
                            $API_2 = new routeros_api();
                            $API_2->debug = false;

                            // set the ip address using its id
                            if($API_2->connect($client_router_ip, $sstp_username, $sstp_password, $api_port)){
                                $API_2->comm("/ip/address/add", 
                                array(
                                    "address"     => $req->input('client_gw'),
                                    "interface" => $req->input('interface_name'),
                                    "network" => $req->input('client_network'),
                                    "disabled" => ($client_status == 0 ? "true" : "false"),
                                    "comment"  => $req->input('client_name')." (".$req->input('client_address')." - ".$location_coordinates.") - ".$client_account_number
                                ));
                                $API_2->disconnect();
                            }
                        }
    
                        // simple queues
                        // loop through the queues to see if the current queue is present!
                        $queue_id = 0;
                        $present = 0;
                        foreach ($router_simple_queues as $key => $value_simple_queues) {
                            if ($value_simple_queues["$target_key"] == $client_network."/".explode("/",$client_gw_name)[1]) {
                                $present = 1;
                                $queue_id = $value_simple_queues['.id'];
                                break;
                            }
                        }
            
                        $upload = $upload_speed.$unit1;
                        $download = $download_speed.$unit2;
    
                        // return $old_network."/".explode("/",$old_client_gw)[1];
                        if ($present == 1) {

                            // add a new ip address
                            $API_2 = new routeros_api();
                            $API_2->debug = false;
                            
                            // set the ip address using its id
                            if($API_2->connect($client_router_ip, $sstp_username, $sstp_password, $api_port)){
                                // set the queue using the ip address
                                $API_2->comm("/queue/simple/set",
                                    array(
                                        "name" => $req->input('client_name')." (".$req->input('client_address')." - ".$location_coordinates.") - ".$client_account_number,
                                        "$target_key" => $client_network."/".explode("/",$client_gw_name)[1],
                                        "max-limit" => $upload."/".$download,
                                        ".id" => $queue_id
                                    )
                                );
                                $API_2->disconnect();
                            }
                        }else {

                            // add a new ip address
                            $API_2 = new routeros_api();
                            $API_2->debug = false;
                            
                            // set the ip address using its id
                            if($API_2->connect($client_router_ip, $sstp_username, $sstp_password, $api_port)){
                                // add the queue to the list
                                $API_2->comm("/queue/simple/add",
                                    array(
                                        "name" => $req->input('client_name')." (".$req->input('client_address')." - ".$location_coordinates.") - ".$client_account_number,
                                        "$target_key" => $client_network."/".explode("/",$client_gw_name)[1],
                                        "max-limit" => $upload."/".$download
                                    )
                                );
                                $API_2->disconnect();
                            }
                        }
        
                        $txt = ":Client (".$client_name.") information updated by ".session('Usernames')." to both the database and the router";
                        $this->log($txt);
                        // end of log file
                    }else{
        
                        $txt = ":Client (".$client_name.") information updated by ".session('Usernames')." to on the database.";
                        // $this->log($txt);
                        // end of log file
                    }

                    // update the clients
                    $upload = $upload_speed.$unit1;
                    $download = $download_speed.$unit2;

                    // update the table
                    DB::connection("mysql2")->table('client_tables')
                    ->where('client_id', $clients_id)
                    ->update([
                        'client_name' => $client_name,
                        'client_network' => $client_network,
                        'client_default_gw' => $client_gw_name,
                        'max_upload_download' => $upload."/".$download,
                        'monthly_payment' => $client_monthly_pay,
                        'router_name' => $router_name,
                        'client_interface' => $interface_name,
                        'comment' => $req->input('comments'),
                        'clients_contacts' => $client_phone,
                        'client_username' => $req->input('client_username'),
                        'client_password' => $client_password,
                        'location_coordinates' => $location_coordinates,
                        'client_address' => $req->input('client_address'),
                        'date_changed' => date("YmdHis")
                    ]);
                            
                    // redirect to the client table
                    $API->disconnect();
                    session()->flash("success","Updates have been done successfully!");
                    return redirect(route("viewOrganizationClient",[$organization_id, $clients_id]));
                }else{
                    session()->flash("error","An error occured! Check your router credentials and try again!");
                    return redirect(url()->previous());
                }
            }elseif ($client_data[0]->assignment == "pppoe") {
                if(!$req->input("pppoe_profile")){
                    session()->flash("error","Kindly select the PPPOE profile the client is to be assigned!");
                    return redirect(url()->previous());
                }

                // get the clients details to see if the router is different
                $original_client_dets = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `client_id` = ?;",[$req->input("clients_id")]);
                // return $req;

                if (count($original_client_dets) == 0) {
                    session()->flash("error","Update cannot be done to an invalid user!");
                    return redirect(url()->previous());
                }
                $client_status = $client_data[0]->client_status;

                // check if the routers are the same
                if ($original_client_dets[0]->router_name != $req->input("router_name")) {
                    // if not proceed and disable the router profile
                    // get the router data
                    $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `router_id` = ? AND `deleted` = '0'",[$original_client_dets[0]->router_name]);
                    if (count($router_data) > 0) {
                        // get the sstp credentails they are also the api usernames
                        $sstp_username = $router_data[0]->sstp_username;
                        $sstp_password = $router_data[0]->sstp_password;
                        $api_port = $router_data[0]->api_port;

                        // connect to the router and set the sstp client
                        $sstp_value = $this->getSSTPAddress($database_name);
                        if ($sstp_value == null) {
                            $error = "The SSTP server is not set, Contact your administrator!";
                            session()->flash("network_presence",$error);
                            return redirect(url()->previous());
                        }

                        // connect to the router and set the sstp client
                        $server_ip_address = $sstp_value->ip_address;
                        $user = $sstp_value->username;
                        $pass = $sstp_value->password;
                        $port = $sstp_value->port;

                        // check if the router is actively connected
                        $client_router_ip = $this->checkActive($server_ip_address,$user,$pass,$port,$sstp_username);
                        // return $client_router_ip;
                        $API = new routeros_api();
                        $API->debug = false;
        
                        $router_secrets = [];
                        if ($API->connect($client_router_ip, $sstp_username, $sstp_password, $api_port)){
                            // get the secret details
                            $secret_name = $original_client_dets[0]->client_secret;

                            // get the ACTIVE PPPOE CONNECTION
                            $curl_handle = curl_init();
                            $url = "https://crontab.hypbits.com/getIpaddress.php?db_name=".session("database_name")."&r_id=".$original_client_dets[0]->router_name."&r_active_secrets=true";
                            curl_setopt($curl_handle, CURLOPT_URL, $url);
                            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
                            $curl_data = curl_exec($curl_handle);
                            curl_close($curl_handle);
                            $active_connections = json_decode($curl_data, true);
                            

                            // get the ACTIVE PPPOE CONNECTION
                            $curl_handle = curl_init();
                            $url = "https://crontab.hypbits.com/getIpaddress.php?db_name=".session("database_name")."&r_id=".$original_client_dets[0]->router_name."&r_ppoe_secrets=true";
                            curl_setopt($curl_handle, CURLOPT_URL, $url);
                            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
                            $curl_data = curl_exec($curl_handle);
                            curl_close($curl_handle);
                            $router_secrets = json_decode($curl_data, true);

                            // router secrets
                            $secret_id = false;
                            foreach ($router_secrets as $key => $router_secret) {
                                if ($router_secret['name'] == $secret_name) {
                                    $secret_id = $router_secret['.id'];
                                    break;
                                }
                            }

                            // disable the secret
                            if ($secret_id) {
                                $API->comm("/ppp/secret/remove", array(
                                    ".id" => $secret_id
                                ));
                            }

                            $active_id = false;
                            foreach ($active_connections as $key => $connection) {
                                if($connection['name'] == $secret_name){
                                    $active_id = $connection['.id'];
                                }
                            }
        
                            if ($active_id) {
                                // remove the active connection if there is, it will do nothing if the id is not present
                                $API->comm("/ppp/active/remove", array(
                                    ".id" => $active_id
                                ));
                            }
                        }
                    }
                }

                // get the data for the ppoe clients
                $clients_id = $req->input("clients_id");
                $allow_router_changes = $req->input("allow_router_changes");
                $client_name = $req->input("client_name");
                $client_address = $req->input("client_address");
                $location_coordinates = $req->input("location_coordinates");
                $client_phone = $req->input("client_phone");
                $client_account_number = $req->input("client_account_number");
                $client_monthly_pay = $req->input("client_monthly_pay");
                $client_secret_username = $req->input("client_secret_username");
                $client_secret_password = $req->input("client_secret_password");
                $router_name = $req->input("router_name");
                $pppoe_profile = $req->input("pppoe_profile");
                $comments = $req->input("comments");
                $client_username = $req->input("client_username");
                $client_password = $req->input("client_password");
                // check if the secret and the username is present in the router
                
                // if the secret is present in the router overwrite it
                // get the router data
                $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `router_id` = ? AND `deleted` = '0'",[$router_name]);
                if (count($router_data) == 0) {
                    $error = "Router selected does not exist!";
                    session()->flash("error",$error);
                    return redirect(url()->previous());
                }
                
                // get the sstp credentails they are also the api usernames
                $sstp_username = $router_data[0]->sstp_username;
                $sstp_password = $router_data[0]->sstp_password;
                $api_port = $router_data[0]->api_port;
                

                // connect to the router and set the sstp client
                $sstp_value = $this->getSSTPAddress($database_name);
                if ($sstp_value == null) {
                    $error = "The SSTP server is not set, Contact your administrator!";
                    session()->flash("network_presence",$error);
                    return redirect(url()->previous());
                }

                // connect to the router and set the sstp client
                $server_ip_address = $sstp_value->ip_address;
                $user = $sstp_value->username;
                $pass = $sstp_value->password;
                $port = $sstp_value->port;

                // check if the router is actively connected
                $client_router_ip = $this->checkActive($server_ip_address,$user,$pass,$port,$sstp_username);
                // return $client_router_ip;

                // get ip address and queues
                // start with IP address
                // connect to the router and add the ip address and queues to the interface
                $API = new routeros_api();
                $API->debug = false;

                $router_secrets = [];
                if ($API->connect($client_router_ip, $sstp_username, $sstp_password, $api_port)){
                    // get the ACTIVE PPPOE CONNECTION
                    $curl_handle = curl_init();
                    $url = "https://crontab.hypbits.com/getIpaddress.php?db_name=".session("database_name")."&r_id=".$original_client_dets[0]->router_name."&r_ppoe_secrets=true";
                    curl_setopt($curl_handle, CURLOPT_URL, $url);
                    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
                    $curl_data = curl_exec($curl_handle);
                    curl_close($curl_handle);
                    $router_secrets = json_decode($curl_data, true);
                    // return $router_secrets;

                    // loop through the secrets and find the name
                    $present = 0;
                    $secret_id = 0;
                    for ($index=0; $index < count($router_secrets); $index++) { 
                        if ($router_secrets[$index]['name'] == $client_data[0]->client_secret) {
                            $present = 1;
                            $secret_id = $router_secrets[$index]['.id'];
                            break;
                        }
                    }

                    // 
                    if ($allow_router_changes == "on"){
                        // if present update the client secrets
                        if ($present == 1) {
                            $API->comm("/ppp/secret/set", 
                            array(
                                "name"     => $client_secret_username,
                                "service" => "pppoe",
                                "password" => $client_secret_password,
                                "profile"  => $pppoe_profile,
                                "comment"  => $client_name." (".$client_address." - ".$location_coordinates.") - ".$client_account_number,
                                "disabled" => ($client_status == 0 ? "true" : "false"),
                                ".id" => $secret_id
                            ));
                        }else{
                            // if the secret is not found add the secrets
                            $API->comm("/ppp/secret/add", 
                            array(
                                "name"     => $client_secret_username,
                                "service" => "pppoe",
                                "password" => $client_secret_password,
                                "profile"  => $pppoe_profile,
                                "comment"  => $client_name." (".$client_address." - ".$location_coordinates.") - ".$client_account_number,
                                "disabled" => ($client_status == 0 ? "true" : "false")
                            ));
                            // return $client_data;
                        }
                    
                        // disconnect
                        $API->disconnect();

                        // update the user data // update the table
                        DB::connection("mysql2")->table('client_tables')
                        ->where('client_id', $clients_id)
                        ->update([
                            'client_name' => $client_name,
                            'client_secret' => $client_secret_username,
                            'client_secret_password' => $client_secret_password,
                            'monthly_payment' => $client_monthly_pay,
                            'router_name' => $router_name,
                            'client_profile' => $pppoe_profile,
                            'comment' => $req->input('comments'),
                            'clients_contacts' => $client_phone,
                            'client_username' => $req->input('client_username'),
                            'client_password' => $client_password,
                            'location_coordinates' => $location_coordinates,
                            'client_address' => $client_address,
                            'date_changed' => date("YmdHis")
                        ]);
        
                        $txt = ":Client (".$client_name.") information updated by ".session('Usernames')." both on the database and the router!";
                        // $this->log($txt);
                        // end of log file
                    }else{
                        // update the user data // update the table
                        DB::connection("mysql2")->table('client_tables')
                        ->where('client_id', $clients_id)
                        ->update([
                            'client_name' => $client_name,
                            'client_secret' => $client_secret_username,
                            'client_secret_password' => $client_secret_password,
                            'monthly_payment' => $client_monthly_pay,
                            'router_name' => $router_name,
                            'client_profile' => $pppoe_profile,
                            'comment' => $req->input('comments'),
                            'clients_contacts' => $client_phone,
                            'client_username' => $req->input('client_username'),
                            'client_password' => $client_password,
                            'location_coordinates' => $location_coordinates,
                            'client_address' => $client_address,
                            'date_changed' => date("YmdHis")
                        ]);

                        // log message
                        $txt = ":Client (".$client_name.") information updated by ".session('Usernames')." on the database! \n";
                        // $this->log($txt);
                    }
                }
                                
                // redirect to the client table
                session()->flash("success","Updates have been done successfully!");
                return redirect(url()->previous());
            }
        }else {
            // redirect to the client table
            session()->flash("error","Invalid client!");
            return redirect("Clients");
        }
    }

    function get_router_interface($organization_id, $routerid){
        // organization id
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization_details) == 0) {
            echo "Invalid organization!";
            return "";
        }

        // change_db
        $change_db = new login();
        $change_db->change_db($organization_details[0]->organization_database);
        $database_name = $organization_details[0]->organization_database;

        // get the router data
        $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `router_id` = ? AND `deleted` = '0'",[$routerid]);
        if (count($router_data) == 0) {
            echo "Router does not exist!";
            return "";
        }
        
        // get the IP ADDRES
        $curl_handle = curl_init();
        $url = "https://crontab.hypbits.com/getIpaddress.php?db_name=".$database_name."&r_id=".$routerid."&r_interfaces=true";
        curl_setopt($curl_handle, CURLOPT_URL, $url);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
        $curl_data = curl_exec($curl_handle);
        curl_close($curl_handle);
        $interfaces = json_decode($curl_data, true);
        // return $interfaces;


        if(count($interfaces) > 0 ){
            $data_to_display = "<select name='interface_name' class='form-control' id='interface_name' required ><option value='' hidden>Select an Interface</option>";
                for ($index=0; $index < count($interfaces); $index++) { 
                    $data_to_display.="<option value='".$interfaces[$index]['name']."'>".$interfaces[$index]['name']."</option>";
                }
                $data_to_display.="</select>";
            echo $data_to_display;
        }else{
            echo "No data to display : \"Your router might be In-active!\"";
        }
        return "";
    }

    // activate and deactivate sms
    function ActivateSMS($organization_id){
        // organization id
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization_details) == 0) {
            session()->flash("error","Invalid organization!");
            return redirect(route("Organizations"));
        }

        // update the status to activate
        DB::update("UPDATE `organizations` SET `send_sms` = '1' WHERE `organization_id` = ?",[$organization_id]);
        session()->flash("success","Organization successfully Activated!");
        return redirect(route("ViewOrganization", [$organization_id]));
    }

    // activate and deactivate sms
    function DeactivateSMS($organization_id){
        // organization id
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization_details) == 0) {
            session()->flash("error","Invalid organization!");
            return redirect(route("Organizations"));
        }

        // update the status to activate
        DB::update("UPDATE `organizations` SET `send_sms` = '0' WHERE `organization_id` = ?",[$organization_id]);
        session()->flash("success","Organization successfully Deactivated!");
        return redirect(route("ViewOrganization", [$organization_id]));
    }

    // get the clients statistics
    function get_clients_statistics($organization_id){
        // organization id
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization_details) == 0) {
            echo "Invalid organization!";
            return "";
        }

        // change_db
        $change_db = new login();
        $change_db->change_db($organization_details[0]->organization_database);
        $database_name = $organization_details[0]->organization_database;

        // get weekly data
        $dates = date("D");
        $days = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
        $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        $days_index = 0;
        for ($i=0; $i < count($days); $i++) { 
            if ($dates == $days[$i]) {
                break;
            }
            $days_index++;
        }

        $week_starts = date("YmdHis",strtotime("-".$days_index." days"));
        $week_ends = $this->addDays($week_starts,6);
        // return $week_ends;
        
        $clients_statistics = [];
        $clients_data = [];

        $clientd_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' ORDER BY `clients_reg_date` ASC");
        $client_reg_date = date("D",strtotime($clientd_data[0]->clients_reg_date));
        $client_reg_date_mon = date("M",strtotime($clientd_data[0]->clients_reg_date));

        // get the first day of the week the client was registered
        $days_index = 0;
        for ($i=0; $i < count($days); $i++) { 
            if ($client_reg_date == $days[$i]) {
                break;
            }
            $days_index++;
        }

        // get the date the week started when the first client was registered
        $duration_start = $this->addDays($clientd_data[0]->clients_reg_date,-$days_index);
        // return $duration_start." -$days_index ".$clientd_data[0]->clients_reg_date;

        // start from this first date to today looping through seven days of the week
        $day_1 = date("Ymd",strtotime($duration_start));
        // echo $day_1;
        $COUNTER = 0;
        $break = false;
        while(true){
            // store the arrays in the data
            $client_metrics = [];
            $clients_weekly = [];
            for ($index=0; $index < 7; $index++) {
                $day_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `clients_reg_date` LIKE '".date("Ymd",strtotime($day_1))."%'");
                $cl_data = array("date" => date("D dS M",strtotime($day_1)),"number" => count($day_data));
                array_push($client_metrics,$cl_data);
                array_push($clients_weekly,$day_data);

                // echo date("Ymd",strtotime($day_1))." ".date("Ymd",strtotime($week_ends))." (".(date("Ymd",strtotime($day_1)) == date("Ymd",strtotime($week_ends))).")<br>";
                if (date("Ymd",strtotime($day_1)) == date("Ymd",strtotime($week_ends))) {
                    $break = true;
                }
                $day_1 = $this->addDays($day_1,1);
            }
            // echo "<hr>";
            array_push($clients_statistics,$client_metrics);
            array_push($clients_data,$clients_weekly);
            
            $COUNTER++;
            if ($break) {
                break;
            }
        }
        // return $clients_data;

        // get the monthly data for the clients
        $months_index = 0;
        $this_month = date("M");
        for ($index=0; $index < count($months); $index++) {
            if ($this_month == $months[$index]) {
                break;
            }
            $months_index++;
        }

        $start_month = date("YmdHis",strtotime("-$months_index months"));
        $end_months = date("YmdHis",strtotime($this->addMonths($start_month,11)));
        // return $end_months;
        
        $clients_statistics_monthly = [];
        $clients_data_monthly = [];

        $clientd_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' ORDER BY `clients_reg_date` ASC");
        $client_reg_date_mon = date("M",strtotime($clientd_data[0]->clients_reg_date));

        // get the first day of the week the client was registered
        $months_index = 0;
        for ($i=0; $i < count($months); $i++) { 
            if ($client_reg_date_mon == $months[$i]) {
                break;
            }
            $months_index++;
        }

        // get the date the week started when the first client was registered
        $duration_start = $this->addMonths($clientd_data[0]->clients_reg_date,-$months_index);
        // return $duration_start." -$months_index ".$clientd_data[0]->clients_reg_date;

        // start from this first date to today looping through seven days of the week
        $month_1 = date("YmdHis",strtotime($duration_start));
        // echo $month_1;
        $COUNTER = 0;
        $break = false;
        while(true){
            // store the arrays in the data
            $client_metrics = [];
            $clients_monthly = [];
            for ($index=0; $index < 12; $index++) {
                $months_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `clients_reg_date` LIKE '".date("Ym",strtotime($month_1))."%'");
                $cl_data = array("date" => date("M Y",strtotime($month_1)),"number" => count($months_data));
                array_push($client_metrics,$cl_data);
                array_push($clients_monthly,$months_data);

                // echo date("Ymd",strtotime($month_1))." ".date("Ymd",strtotime($week_ends))." (".(date("Ymd",strtotime($month_1)) == date("Ymd",strtotime($week_ends))).")<br>";
                if (date("Ym",strtotime($month_1)) == date("Ym",strtotime($end_months))) {
                    $break = true;
                }
                $month_1 = $this->addMonths($month_1,1);
            }
            // echo "<hr>";
            array_push($clients_statistics_monthly,$client_metrics);
            array_push($clients_data_monthly,$clients_monthly);
            
            $COUNTER++;
            if ($break) {
                break;
            }
        }
        // return [$clients_data_monthly,$clients_statistics_monthly];

        // clients statistics yearly
        $clients_statistics_yearly = [];
        $clients_data_yearly = [];

        $clientd_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' ORDER BY `clients_reg_date` ASC");

        // get the date the week started when the first client was registered
        $duration_start = $clientd_data[0]->clients_reg_date;
        $end_year = date("Y");
        // return $duration_start." ".$clientd_data[0]->clients_reg_date;

        // start from this first date to today looping through seven days of the week
        $year_1 = date("YmdHis",strtotime($duration_start));
        // return (date("Y",strtotime($year_1))*1)." ".$end_year;
        // store the arrays in the data
        for ($index = (date("Y",strtotime($year_1))*1); $index <= ($end_year*1); $index++) {
            $yearly_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `clients_reg_date` LIKE '".$index."%'");
            $cl_data = array("date" => $index,"number" => count($yearly_data));
            
            array_push($clients_statistics_yearly,$cl_data);
            array_push($clients_data_yearly,$yearly_data);
        }
        // return $clients_data_yearly[0][0];
        // return [$clients_statistics_yearly,$clients_data_yearly];
        return view('Orgarnizations.client_stats',["organization_details" => $organization_details, "clients_weekly" => $clients_data,"client_metrics_weekly" => $clients_statistics,"clients_statistics_monthly" => $clients_statistics_monthly,"clients_monthly" => $clients_data_monthly,"clients_statistics_yearly" => $clients_statistics_yearly,"clients_data_yearly" => $clients_data_yearly]);
    }
    function clients_demographics(Request $req, $organization_id){
        // organization id
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization_details) == 0) {
            echo "Invalid organization!";
            return "";
        }

        // change_db
        $change_db = new login();
        $change_db->change_db($organization_details[0]->organization_database);
        $database_name = $organization_details[0]->organization_database;

        $selected_dates = $req->input('selected_dates');
        $from_today = $req->input('from_today');

        $today = date("Ymd")."235959";
        $future = date("Ymd",strtotime($selected_dates))."235959";
        // return $future;
        $clients_data = [];
        // select all clients that are to be due from today to the future
        if ($from_today == "true") {
            $clients_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `next_expiration_date` <= '".$future."' AND `next_expiration_date` >= '".$today."'");
        }else{
            $clients_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `next_expiration_date` <= '".$future."'");
        }

        return $clients_data;
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
        $client_report_option = $req->input("client_report_option");
        $client_registration_date_option = $req->input("client_registration_date_option");
        $select_registration_date = $req->input("select_registration_date");
        $select_router_option = $req->input("select_router_option");
        $client_statuses = $req->input("client_statuses");
        $from_select_date = $req->input("from_select_date");
        $to_select_date = $req->input("to_select_date");

        if ($client_report_option == "client registration") {
            // get the clients data
                // return $select_router_option . " " . $client_statuses;
            $clients_data = [];
            $title = "No data to display!";
            if ($select_router_option == "All" && $client_statuses == "2") {
                if ($client_registration_date_option == "all dates") {
                    $title = "All Clients Registered";
                    $clients_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' ORDER BY `clients_reg_date` DESC");
                }elseif ($client_registration_date_option == "select date") {
                    $title = "Clients Registered on ".date("D dS M Y",strtotime($select_registration_date));
                    $clients_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `clients_reg_date` LIKE '".date("Ymd",strtotime($select_registration_date))."%' ORDER BY `clients_reg_date` DESC");
                }elseif ($client_registration_date_option == "between dates") {
                    $title = "Clients Registered between ".date("D dS M Y",strtotime($from_select_date))." AND ".date("D dS M Y",strtotime($to_select_date));
                    $clients_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `clients_reg_date` BETWEEN '".date("YmdHis",strtotime($from_select_date))."' AND '".date("Ymd",strtotime($to_select_date))."235959"."' ORDER BY `clients_reg_date` DESC");
                }else{
                    $title = "Clients Registered";
                    $clients_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' ORDER BY `clients_reg_date` DESC");
                }
            }elseif (($client_statuses == "1" || $client_statuses == "0") && $select_router_option != "All") {
                $status = $client_statuses == "0" ? "In-Active" : "Active";
                $router_data = DB::connection("mysql2")->select("SELECT * FROM `router_tables` WHERE `deleted` = '0' AND `router_id` = ?",[$select_router_option]);
                $router_name = count($router_data) > 0 ? $router_data[0]->router_name : "Null";

                if ($client_registration_date_option == "all dates") {
                    $title = "All ".$status." Clients Registered in Router: ".ucwords(strtolower($router_name));
                    $clients_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_status` = ? AND `router_name` = ? ORDER BY `clients_reg_date` DESC",[$client_statuses,$select_router_option]);
                }elseif ($client_registration_date_option == "select date") {
                    $title = $status." Clients Registered on ".date("D dS M Y",strtotime($select_registration_date))." in Router: ".ucwords(strtolower($router_name));
                    $clients_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_status` = ? AND `router_name` = ? AND `clients_reg_date` LIKE '".date("Ymd",strtotime($select_registration_date))."%' ORDER BY `clients_reg_date` DESC",[$client_statuses,$select_router_option]);
                }elseif ($client_registration_date_option == "between dates") {
                    $title = $status." Clients Registered between ".date("D dS M Y",strtotime($from_select_date))." AND ".date("D dS M Y",strtotime($to_select_date))." in Router: ".ucwords(strtolower($router_name));
                    $clients_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_status` = ? AND `router_name` = ? AND `clients_reg_date` BETWEEN '".date("YmdHis",strtotime($from_select_date))."' AND '".date("Ymd",strtotime($to_select_date))."235959"."' ORDER BY `clients_reg_date` DESC",[$client_statuses,$select_router_option]);
                }else{
                    $title = "All ".$status." Clients Registered"." in Router: ".ucwords(strtolower($router_name));
                    $clients_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_status` = ? AND `router_name` = ? AND ORDER BY `clients_reg_date` DESC",[$client_statuses,$select_router_option]);
                }
            }elseif ($client_statuses == "3" && $select_router_option != "All") {
                $router_data = DB::connection("mysql2")->select("SELECT * FROM `router_tables` WHERE `deleted` = '0' AND `router_id` = ?",[$select_router_option]);
                $router_name = count($router_data) > 0 ? $router_data[0]->router_name : "Null";

                if ($client_registration_date_option == "all dates") {
                    $title = "All reffered Clients Registered in Router: ".ucwords(strtolower($router_name));
                    $clients_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `reffered_by` IS NOT NULL AND `reffered_by` != '' AND `router_name` = ? ORDER BY `clients_reg_date` DESC",[$select_router_option]);
                }elseif ($client_registration_date_option == "select date") {
                    $title = "Reffered Clients Registered on ".date("D dS M Y",strtotime($select_registration_date))." in Router: ".ucwords(strtolower($router_name));
                    $clients_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `reffered_by` IS NOT NULL AND `reffered_by` != '' AND `router_name` = ? AND `clients_reg_date` LIKE '".date("Ymd",strtotime($select_registration_date))."%' ORDER BY `clients_reg_date` DESC",[$select_router_option]);
                }elseif ($client_registration_date_option == "between dates") {
                    $title = "Reffered Clients Registered between ".date("D dS M Y",strtotime($from_select_date))." AND ".date("D dS M Y",strtotime($to_select_date))." in Router: ".ucwords(strtolower($router_name));
                    $clients_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `reffered_by` IS NOT NULL AND `reffered_by` != '' AND `router_name` = ? AND `clients_reg_date` BETWEEN '".date("YmdHis",strtotime($from_select_date))."' AND '".date("Ymd",strtotime($to_select_date))."235959"."' ORDER BY `clients_reg_date` DESC",[$select_router_option]);
                }else{
                    $title = "All reffered Clients Registered"." in Router: ".ucwords(strtolower($router_name));
                    $clients_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `reffered_by` IS NOT NULL AND `reffered_by` != '' AND `router_name` = ? AND ORDER BY `clients_reg_date` DESC",[$select_router_option]);
                }
            }elseif (($client_statuses == "4" || $client_statuses == "5") && $select_router_option != "All") {
                $assignment = $client_statuses == "4" ? "static":"pppoe";
                $router_data = DB::connection("mysql2")->select("SELECT * FROM `router_tables` WHERE `deleted` = '0' AND `router_id` = ?",[$select_router_option]);
                $router_name = count($router_data) > 0 ? $router_data[0]->router_name : "Null";

                if ($client_registration_date_option == "all dates") {
                    $title = "All ".$assignment." assigned Clients Registered in Router: ".ucwords(strtolower($router_name));
                    $clients_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `assignment` = ? AND `router_name` = ? ORDER BY `clients_reg_date` DESC",[$assignment,$select_router_option]);
                }elseif ($client_registration_date_option == "select date") {
                    $title = "".$assignment." assigned Clients Registered on ".date("D dS M Y",strtotime($select_registration_date))." in Router: ".ucwords(strtolower($router_name));
                    $clients_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `assignment` = ? AND `router_name` = ? AND `clients_reg_date` LIKE '".date("Ymd",strtotime($select_registration_date))."%' ORDER BY `clients_reg_date` DESC",[$assignment,$select_router_option]);
                }elseif ($client_registration_date_option == "between dates") {
                    $title = "".$assignment." assigned Clients Registered between ".date("D dS M Y",strtotime($from_select_date))." AND ".date("D dS M Y",strtotime($to_select_date))." in Router: ".ucwords(strtolower($router_name));
                    $clients_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `assignment` = ? AND `router_name` = ? AND `clients_reg_date` BETWEEN '".date("YmdHis",strtotime($from_select_date))."' AND '".date("Ymd",strtotime($to_select_date))."235959"."' ORDER BY `clients_reg_date` DESC",[$assignment,$select_router_option]);
                }else{
                    $title = "All ".$assignment." assigned Clients Registered in Router: ".ucwords(strtolower($router_name));
                    $clients_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `assignment` = ? AND `router_name` = ? AND ORDER BY `clients_reg_date` DESC",[$assignment,$select_router_option]);
                }
            }elseif ($select_router_option != "All" && $client_statuses == "2") {
                $status = $client_statuses == "0" ? "In-Active" : "Active";
                $router_data = DB::connection("mysql2")->select("SELECT * FROM `router_tables` WHERE `deleted` = '0' AND `router_id` = ?",[$select_router_option]);
                $router_name = count($router_data) > 0 ? $router_data[0]->router_name : "Null";

                if ($client_registration_date_option == "all dates") {
                    $title = "All Clients Registered in Router: ".ucwords(strtolower($router_name));
                    $clients_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `router_name` = ? ORDER BY `clients_reg_date` DESC",[$select_router_option]);
                }elseif ($client_registration_date_option == "select date") {
                    $title = "Clients Registered on ".date("D dS M Y",strtotime($select_registration_date))." in Router: ".ucwords(strtolower($router_name));
                    $clients_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `router_name` = ? AND `clients_reg_date` LIKE '".date("Ymd",strtotime($select_registration_date))."%' ORDER BY `clients_reg_date` DESC",[$select_router_option]);
                }elseif ($client_registration_date_option == "between dates") {
                    $title = "Clients Registered between ".date("D dS M Y",strtotime($from_select_date))." AND ".date("D dS M Y",strtotime($to_select_date))." in Router: ".ucwords(strtolower($router_name));
                    $clients_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `router_name` = ? AND `clients_reg_date` BETWEEN '".date("YmdHis",strtotime($from_select_date))."' AND '".date("Ymd",strtotime($to_select_date))."235959"."' ORDER BY `clients_reg_date` DESC",[$select_router_option]);
                }else{
                    $title = "All Clients Registered"." in Router: ".ucwords(strtolower($router_name));
                    $clients_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `router_name` = ? AND ORDER BY `clients_reg_date` DESC",[$select_router_option]);
                }
            }elseif ($select_router_option == "All" && ($client_statuses == "1" || $client_statuses == "0")) {
                $status = $client_statuses == "0" ? "In-Active" : "Active";

                if ($client_registration_date_option == "all dates") {
                    $title = "All ".$status." Clients Registered";
                    $clients_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_status` = ? ORDER BY `clients_reg_date` DESC",[$client_statuses]);
                }elseif ($client_registration_date_option == "select date") {
                    $title = $status." Clients Registered on ".date("D dS M Y",strtotime($select_registration_date))."";
                    $clients_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_status` = ? AND `clients_reg_date` LIKE '".date("Ymd",strtotime($select_registration_date))."%' ORDER BY `clients_reg_date` DESC",[$client_statuses]);
                }elseif ($client_registration_date_option == "between dates") {
                    $title = $status." Clients Registered between ".date("D dS M Y",strtotime($from_select_date))." AND ".date("D dS M Y",strtotime($to_select_date))."";
                    $clients_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_status` = ? AND `clients_reg_date` BETWEEN '".date("YmdHis",strtotime($from_select_date))."' AND '".date("Ymd",strtotime($to_select_date))."235959"."' ORDER BY `clients_reg_date` DESC",[$client_statuses]);
                }else{
                    $title = "All ".$status." Clients Registered"."";
                    $clients_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_status` = ? AND ORDER BY `clients_reg_date` DESC",[$client_statuses]);
                }
            }elseif ($select_router_option == "All" && $client_statuses == "3"){
                
                if ($client_registration_date_option == "all dates") {
                    $title = "All reffered Clients Registered";
                    $clients_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `reffered_by` IS NOT NULL AND `reffered_by` != '' ORDER BY `clients_reg_date` DESC");
                }elseif ($client_registration_date_option == "select date") {
                    $title = "Reffered Clients Registered on ".date("D dS M Y",strtotime($select_registration_date))."";
                    $clients_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND  `reffered_by` IS NOT NULL AND `reffered_by` != '' AND `clients_reg_date` LIKE '".date("Ymd",strtotime($select_registration_date))."%' ORDER BY `clients_reg_date` DESC");
                }elseif ($client_registration_date_option == "between dates") {
                    $title = "Reffered Clients Registered between ".date("D dS M Y",strtotime($from_select_date))." AND ".date("D dS M Y",strtotime($to_select_date))."";
                    $clients_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `reffered_by` IS NOT NULL AND `reffered_by` != '' AND `clients_reg_date` BETWEEN '".date("YmdHis",strtotime($from_select_date))."' AND '".date("Ymd",strtotime($to_select_date))."235959"."' ORDER BY `clients_reg_date` DESC");
                }else{
                    $title = "All reffered Clients Registered"."";
                    $clients_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `reffered_by` IS NOT NULL AND `reffered_by` != '' AND ORDER BY `clients_reg_date` DESC");
                }
            }elseif (($client_statuses == "4" || $client_statuses == "5") && $select_router_option == "All"){
                $assignment = $client_statuses == "4" ? "static":"pppoe";

                if ($client_registration_date_option == "all dates") {
                    $title = "All ".$assignment." assigned Clients Registered ";
                    $clients_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `assignment` = ? ORDER BY `clients_reg_date` DESC",[$assignment]);
                }elseif ($client_registration_date_option == "select date") {
                    $title = "".$assignment." assigned Clients Registered on ".date("D dS M Y",strtotime($select_registration_date))." ";
                    $clients_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `assignment` = ? AND `clients_reg_date` LIKE '".date("Ymd",strtotime($select_registration_date))."%' ORDER BY `clients_reg_date` DESC",[$assignment]);
                }elseif ($client_registration_date_option == "between dates") {
                    $title = "".$assignment." assigned Clients Registered between ".date("D dS M Y",strtotime($from_select_date))." AND ".date("D dS M Y",strtotime($to_select_date))." ";
                    $clients_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `assignment` = ? AND `clients_reg_date` BETWEEN '".date("YmdHis",strtotime($from_select_date))."' AND '".date("Ymd",strtotime($to_select_date))."235959"."' ORDER BY `clients_reg_date` DESC",[$assignment]);
                }else{
                    $title = "All ".$assignment." assigned Clients Registered ";
                    $clients_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `assignment` = ? AND ORDER BY `clients_reg_date` DESC",[$assignment]);
                }
            }
            // return $clients_data;
            $new_client_data = [];
            $static = 0;
            $ppoe = 0;
            $active = 0;
            $inactive = 0;
            for ($index=0; $index < count($clients_data); $index++) {
                $data = array(
                        $clients_data[$index]->client_name,
                        $clients_data[$index]->client_account,
                        $clients_data[$index]->next_expiration_date,
                        $clients_data[$index]->clients_reg_date,
                        $clients_data[$index]->wallet_amount,
                        $clients_data[$index]->clients_contacts,
                        $clients_data[$index]->assignment,
                        $clients_data[$index]->max_upload_download == null ? "secret: ".$clients_data[$index]->client_secret : $clients_data[$index]->max_upload_download,
                        $clients_data[$index]->monthly_payment,
                        $clients_data[$index]->client_address
                    );

                    // return $client_statuses;
                if($client_statuses == "3"){
                    $refferal = str_replace("'","\"",$clients_data[$index]->reffered_by);
                    if (strlen($refferal) > 0) {
                        $refferal = json_decode($refferal);
                        // return $refferal;
                        if ($refferal->monthly_payment > 0) {
                            array_push($new_client_data,$data); 
                            if($clients_data[$index]->assignment == "static"){
                                $static++;
                            }else{
                                $ppoe++;
                            }
                            if($clients_data[$index]->client_status == "1"){
                                $active++;
                            }else{
                                $inactive++;
                            }
                        }
                    }
                }else{
                    array_push($new_client_data,$data); 
                    if($clients_data[$index]->assignment == "static"){
                        $static++;
                    }else{
                        $ppoe++;
                    }
                    if($clients_data[$index]->client_status == "1"){
                        $active++;
                    }else{
                        $inactive++;
                    }
                }
            }
            // return $new_client_data;
            $pdf = new PDF("P","mm","A4");
            
            // organization logo.
            // return [$organization_details[0], public_path()];

            // $organization_details[0]->organization_logo != null ? $pdf->setCompayLogo($organization_details[0]->organization_logo) : $pdf->setCompayLogo("../../../../../..".public_path(session("organization_logo")));
            $pdf->set_company_name($organization_details[0]->organization_name);
            $pdf->set_school_contact($organization_details[0]->organization_main_contact);

            $pdf->set_document_title($title);
            $pdf->AddPage();
            $pdf->SetFont('Times', 'B', 10);
            $pdf->SetMargins(5,5);
            $pdf->Cell(40, 10, "Statistics", 0, 0, 'L', false);
            $pdf->Ln();
            $pdf->SetFont('Times', 'I', 9);
            $pdf->Cell(40, 5, "PPPOE Assigned :", 0, 0, 'L', false);
            $pdf->Cell(20, 5, $ppoe . " Client(s)", 'R', 0, 'L', false);
            $pdf->Cell(40, 5, "Active Clients :", 0, 0, 'L', false);
            $pdf->Cell(20, 5, $active . " Client(s)", 0, 0, 'L', false);
            $pdf->Ln();
            $pdf->Cell(40, 5, "Static Assigned :", 0, 0, 'L', false);
            $pdf->Cell(20, 5, $static . " Client(s)", 'R', 0, 'L', false);
            $pdf->Cell(40, 5, "In-Active Clients :", 'B', 0, 'L', false);
            $pdf->Cell(20, 5, $inactive . " Client(s)", 'B', 0, 'L', false);
            $pdf->Ln();
            $pdf->Cell(40, 5, "Total :", 'T', 0, 'L', false);
            $pdf->Cell(20, 5, ($static+$ppoe) . " Client(s)", 'T', 0, 'L', false);
            $pdf->Ln();
            $pdf->SetFont('Helvetica', 'BU', 9);
            $pdf->Cell(200,8,"Client(s) Table",0,1,"C",false);
            $pdf->SetFont('Helvetica', 'B', 7);
            $width = array(6,35,12,20,20,15,20,13,20,40);
            $header = array('No', 'Client Name', 'Acc No', 'Due Date', 'Reg Date','Price','Contacts', 'Assign','Speed/PPPOE', 'Location');
            $pdf->FancyTable($header,$new_client_data,$width);
            $pdf->Output("I","clients_data.pdf",false);
        }elseif ($client_report_option == "client information") {
            $client_data = [];
            $title = "No data to display!";
            if ($select_router_option == "All") {
                if ($client_statuses == "2") {
                    $title = "All Clients Registered";
                    $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' ORDER BY `clients_reg_date` DESC");
                }elseif ($client_statuses == "0" || $client_statuses == "1") {
                    $status = $client_statuses == "1" ? "Active" : "In-Active";
                    $title = "All ".$status." Clients Registered";
                    $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_status` = ? ORDER BY `clients_reg_date` DESC",[$client_statuses]);
                }elseif ($client_statuses == "3") {
                    $title = "All reffered Clients Registered";
                    $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `reffered_by` IS NOT NULL AND `reffered_by` != '' ORDER BY `clients_reg_date` DESC");
                }elseif ($client_statuses == "4" || $client_statuses == "5") {
                    $assignment = $client_statuses == "4" ? "static":"pppoe";
                    $title = "All ".$assignment." assigned Clients Registered";
                    $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `assignment` = ? ORDER BY `clients_reg_date` DESC",[$assignment]);
                }else{
                    $title = "All Clients Registered";
                    $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' ORDER BY `clients_reg_date` DESC");
                }
            }elseif ($select_router_option != "All") {
                $router_data = DB::connection("mysql2")->select("SELECT * FROM `router_tables` WHERE `deleted` = '0' AND `router_id` = ?",[$select_router_option]);
                $router_name = count($router_data) > 0 ? $router_data[0]->router_name : "Null";
                if ($client_statuses == "2") {
                    $title = "All Clients Registered in Router: ".$router_name."";
                    $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `router_name` = ? ORDER BY `clients_reg_date` DESC",[$select_router_option]);
                }elseif ($client_statuses == "0" || $client_statuses == "1") {
                    $status = $client_statuses == "1" ? "Active" : "In-Active";
                    $title = "All ".$status." Clients Registered in Router: ".$router_name."";
                    $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_status` = ? AND `router_name` = ? ORDER BY `clients_reg_date` DESC",[$client_statuses,$select_router_option]);
                }elseif ($client_statuses == "3") {
                    $title = "All reffered Clients Registered in Router: ".$router_name."";
                    $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `reffered_by` IS NOT NULL AND `reffered_by` != '' AND `router_name` = ? ORDER BY `clients_reg_date` DESC",[$select_router_option]);
                }elseif ($client_statuses == "4" || $client_statuses == "5") {
                    $assignment = $client_statuses == "4" ? "static":"pppoe";
                    $title = "All ".$assignment." assigned Clients Registered in Router: ".$router_name."";
                    $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `assignment` = ? AND `router_name` = ? ORDER BY `clients_reg_date` DESC",[$assignment,$select_router_option]);
                }else{
                    $title = "All Clients Registered in Router: ".$router_name."";
                    $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `router_name` = ? ORDER BY `clients_reg_date` DESC",[$select_router_option]);
                }
            }

            // get the client data and store the information in array form
            $new_client_data = [];
            $ppoe = 0;
            $static = 0;
            $active = 0;
            $inactive = 0;
            for ($index=0; $index < count($client_data); $index++) {
                $data = array(
                    $client_data[$index]->client_name,
                    $client_data[$index]->client_account,
                    $client_data[$index]->clients_contacts,
                    $client_data[$index]->monthly_payment,
                    $client_data[$index]->wallet_amount,
                    $client_data[$index]->max_upload_download != null && trim($client_data[$index]->max_upload_download) != "" ? $client_data[$index]->max_upload_download : "secret:".$client_data[$index]->client_secret,
                    $client_data[$index]->next_expiration_date,
                    $client_data[$index]->clients_reg_date,
                    $client_data[$index]->client_address,
                    $client_data[$index]->location_coordinates,
                    $client_data[$index]->client_status,
                    $client_data[$index]->client_freeze_status == "0" ? "In-Active" : date("D dS M Y",strtotime($client_data[$index]->client_freeze_untill)),
                    $client_data[$index]->reffered_by,
                    $client_data[$index]->assignment
                );
                if($client_statuses == "3"){
                    $refferal = str_replace("'","\"",$client_data[$index]->reffered_by);
                    if (strlen($refferal) > 0) {
                        $refferal = json_decode($refferal);
                        // return $refferal;
                        if ($refferal->monthly_payment > 0) {
                            array_push($new_client_data,$data); 
                            if($client_data[$index]->assignment == "static"){
                                $static++;
                            }else{
                                $ppoe++;
                            }
                            if($client_data[$index]->client_status == "1"){
                                $active++;
                            }else{
                                $inactive++;
                            }
                        }
                    }
                }else{
                    array_push($new_client_data,$data); 
                    if($client_data[$index]->assignment == "static"){
                        $static++;
                    }else{
                        $ppoe++;
                    }
                    if($client_data[$index]->client_status == "1"){
                        $active++;
                    }else{
                        $inactive++;
                    }
                }
            }

            // create the pdf include titlergb(201, 186, 181)
            $pdf = new PDF("L","mm","A4");
            $pdf->setHeaderPos(280);
            $pdf->set_document_title($title);
            $pdf->AddPage();
            $pdf->SetFont('Times', 'B', 10);
            $pdf->SetMargins(5,5);
            $pdf->Cell(40, 10, "Statistics", 0, 0, 'L', false);
            $pdf->Ln();
            $pdf->SetFont('Times', 'I', 9);
            $pdf->Cell(40, 5, "PPPOE Assigned :", 0, 0, 'L', false);
            $pdf->Cell(20, 5, $ppoe . " Client(s)", 'R', 0, 'L', false);
            $pdf->Cell(40, 5, "Active Clients :", 0, 0, 'L', false);
            $pdf->Cell(20, 5, $active . " Client(s)", 0, 0, 'L', false);
            $pdf->Ln();
            $pdf->Cell(40, 5, "Static Assigned :", 0, 0, 'L', false);
            $pdf->Cell(20, 5, $static . " Client(s)", 'R', 0, 'L', false);
            $pdf->Cell(40, 5, "In-Active Clients :", 'B', 0, 'L', false);
            $pdf->Cell(20, 5, $inactive . " Client(s)", 'B', 0, 'L', false);
            $pdf->Ln();
            $pdf->Cell(40, 5, "Total :", 'T', 0, 'L', false);
            $pdf->Cell(20, 5, ($static+$ppoe) . " Client(s)", 'T', 0, 'L', false);
            $pdf->Ln();
            $pdf->SetFont('Helvetica', 'BU', 9);
            $pdf->Cell(280,8,"Client(s) Information Table",0,1,"C",false);
            $pdf->SetFont('Helvetica', 'B', 7);
            $width = array(6,33,12,25,25,17,20,20,20,40,45,25);
            $header = array('No', 'Client Name', 'Acc No', 'Due Date', 'Registration Date','Monthly Fee','Contacts', 'Assignment','Speed/PPPOE', 'Location','Location Co-ordinates','Freeze Status');
            $pdf->clientInformation($header,$new_client_data,$width);
            $pdf->Output("I","clients_data.pdf",false);
        }elseif ($client_report_option == "client router information") {
            $client_data = [];
            $title = "No data to display!";
            if ($select_router_option == "All") {
                if ($client_statuses == "2") {
                    $title = "All Clients Registered";
                    $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' ORDER BY `clients_reg_date` DESC");
                }elseif ($client_statuses == "0" || $client_statuses == "1") {
                    $status = $client_statuses == "1" ? "Active" : "In-Active";
                    $title = "All ".$status." Clients Registered";
                    $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_status` = ? ORDER BY `clients_reg_date` DESC",[$client_statuses]);
                }elseif ($client_statuses == "3") {
                    $title = "All reffered Clients Registered";
                    $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `reffered_by` IS NOT NULL AND `reffered_by` != '' ORDER BY `clients_reg_date` DESC");
                }elseif ($client_statuses == "4" || $client_statuses == "5") {
                    $assignment = $client_statuses == "4" ? "static":"pppoe";
                    $title = "All ".$assignment." assigned Clients Registered";
                    $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `assignment` = ? ORDER BY `clients_reg_date` DESC",[$assignment]);
                }else{
                    $title = "All Clients Registered";
                    $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' ORDER BY `clients_reg_date` DESC");
                }
            }elseif ($select_router_option != "All") {
                $router_data = DB::connection("mysql2")->select("SELECT * FROM `router_tables` WHERE `deleted` = '0' AND `router_id` = ?",[$select_router_option]);
                $router_name = count($router_data) > 0 ? $router_data[0]->router_name : "Null";
                if ($client_statuses == "2") {
                    $title = "All Clients Registered in Router: ".$router_name."";
                    $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `router_name` = ? ORDER BY `clients_reg_date` DESC",[$select_router_option]);
                }elseif ($client_statuses == "0" || $client_statuses == "1") {
                    $status = $client_statuses == "1" ? "Active" : "In-Active";
                    $title = "All ".$status." Clients Registered in Router: ".$router_name."";
                    $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_status` = ? AND `router_name` = ? ORDER BY `clients_reg_date` DESC",[$client_statuses,$select_router_option]);
                }elseif ($client_statuses == "3") {
                    $title = "All reffered Clients Registered in Router: ".$router_name."";
                    $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `reffered_by` IS NOT NULL AND `reffered_by` != '' AND `router_name` = ? ORDER BY `clients_reg_date` DESC",[$select_router_option]);
                }elseif ($client_statuses == "4" || $client_statuses == "5") {
                    $assignment = $client_statuses == "4" ? "static":"pppoe";
                    $title = "All ".$assignment." assigned Clients Registered in Router: ".$router_name."";
                    $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `assignment` = ? AND `router_name` = ? ORDER BY `clients_reg_date` DESC",[$assignment,$select_router_option]);
                }else{
                    $title = "All Clients Registered in Router: ".$router_name."";
                    $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `router_name` = ? ORDER BY `clients_reg_date` DESC",[$select_router_option]);
                }
            }

            // get the client data and store the information in array form
            $new_client_data = [];
            $ppoe = 0;
            $static = 0;
            $active = 0;
            $inactive = 0;
            for ($index=0; $index < count($client_data); $index++) {
                $data = array(
                    $client_data[$index]->client_name,
                    $client_data[$index]->client_account,
                    ($client_data[$index]->client_interface),
                    $this->getRouterName($client_data[$index]->router_name),
                    $client_data[$index]->wallet_amount,
                    $client_data[$index]->max_upload_download != null && trim($client_data[$index]->max_upload_download) != "" ? $client_data[$index]->max_upload_download : "secret:".$client_data[$index]->client_secret,
                    $client_data[$index]->next_expiration_date,
                    $client_data[$index]->clients_reg_date,
                    $client_data[$index]->client_secret_password,
                    $client_data[$index]->client_network,
                    $client_data[$index]->client_status,
                    $client_data[$index]->client_default_gw,
                    $client_data[$index]->reffered_by,
                    $client_data[$index]->assignment
                );
                if($client_statuses == "3"){
                    $refferal = str_replace("'","\"",$client_data[$index]->reffered_by);
                    if (strlen($refferal) > 0) {
                        $refferal = json_decode($refferal);
                        // return $refferal;
                        if ($refferal->monthly_payment > 0) {
                            array_push($new_client_data,$data); 
                            if($client_data[$index]->assignment == "static"){
                                $static++;
                            }else{
                                $ppoe++;
                            }
                            if($client_data[$index]->client_status == "1"){
                                $active++;
                            }else{
                                $inactive++;
                            }
                        }
                    }
                }else{
                    array_push($new_client_data,$data); 
                    if($client_data[$index]->assignment == "static"){
                        $static++;
                    }else{
                        $ppoe++;
                    }
                    if($client_data[$index]->client_status == "1"){
                        $active++;
                    }else{
                        $inactive++;
                    }
                }
            }

            // create the pdf include titlergb(201, 186, 181)
            $pdf = new PDF("L","mm","A4");
            $pdf->setHeaderPos(280);
            $pdf->set_document_title($title);
            $pdf->AddPage();
            $pdf->SetFont('Times', 'B', 10);
            $pdf->SetMargins(5,5);
            $pdf->Cell(40, 10, "Statistics", 0, 0, 'L', false);
            $pdf->Ln();
            $pdf->SetFont('Times', 'I', 9);
            $pdf->Cell(40, 5, "PPPOE Assigned :", 0, 0, 'L', false);
            $pdf->Cell(20, 5, $ppoe . " Client(s)", 'R', 0, 'L', false);
            $pdf->Cell(40, 5, "Active Clients :", 0, 0, 'L', false);
            $pdf->Cell(20, 5, $active . " Client(s)", 0, 0, 'L', false);
            $pdf->Ln();
            $pdf->Cell(40, 5, "Static Assigned :", 0, 0, 'L', false);
            $pdf->Cell(20, 5, $static . " Client(s)", 'R', 0, 'L', false);
            $pdf->Cell(40, 5, "In-Active Clients :", 'B', 0, 'L', false);
            $pdf->Cell(20, 5, $inactive . " Client(s)", 'B', 0, 'L', false);
            $pdf->Ln();
            $pdf->Cell(40, 5, "Total :", 'T', 0, 'L', false);
            $pdf->Cell(20, 5, ($static+$ppoe) . " Client(s)", 'T', 0, 'L', false);
            $pdf->Ln();
            $pdf->SetFont('Helvetica', 'BU', 9);
            $pdf->Cell(280,8,"Client(s) Router Information Table",0,1,"C",false);
            $pdf->SetFont('Helvetica', 'B', 7);
            $width = array(6,35,15,25,25,20,20,20,20,30,30,30);
            $header = array('No', 'Client Name', 'Acc No', 'Due Date', 'Registration Date','Router Name','Interface', 'Assignment','Speed/PPPOE', 'Secret Password','Network Address','Default GW');
            $pdf->clientRouterInformation($header,$new_client_data,$width);
            $pdf->Output("I","clients_data.pdf",false);
        }
    }
}
