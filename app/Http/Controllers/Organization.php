<?php

namespace App\Http\Controllers;

use App\Classes\reports\PDF;
use App\Classes\routeros_api;
use App\Models\admin_table;
use App\Models\admin_table_mikrotik_cloud;
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

    function new_organizations(){
        $packages = DB::select("SELECT * FROM `packages` WHERE `status` = '1';");
        $organizations = DB::select("SELECT * FROM `organizations` ORDER BY `organization_id` DESC LIMIT 1;");
        $last_acc_no = count($organizations) > 0 ? $organizations[0]->account_no : "N/A";
        return view("Orgarnizations.new",["packages" => $packages, "last_acc_no" => $last_acc_no]);
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
        $today = date("YmdHis");
        $status = "1";
        $insert_org = DB::insert("INSERT INTO `organizations` (`organization_name`,`organization_address`,`organization_main_contact`,`organization_email`,`organization_database`,`account_no`,`last_payment_date`, `account_renewal_date`, `package_name`,`organization_status`)
                                VALUES 
                                (?,?,?,?,?,?,?,?,?,?)",[$organization_name, $organization_location, $organization_contacts, $organization_email, $organization_account, $organization_account, $today, $today, $client_package, $status]);
                                

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
            $admin_table->user_status = "1";
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
            return ["success" => false,"date" => null, "account_renewal_date" => null, "reason" => "Invalid Organization!"];
        }

        // get the organization package
        $package_used = DB::select("SELECT * FROM `packages` WHERE `package_id` = ?",[$organization_details[0]->package_name]);
        if (count($package_used) == 0) {
            return ["success" => false,"date" => null,"account_renewal_date" => null,"reason" => "Invalid Package!"];
        }

        // get the date of expiry
        $last_renewal_date = $organization_details[0]->account_renewal_date;
        $package_period = $package_used[0]->package_period;
        $add_date = $this->addPeriodToDate($last_renewal_date,$package_period);
        
        return ["success" => true, "date" => $add_date, "account_renewal_date" => $last_renewal_date, "reason" => ""];
    }

    function view_organization($organization_id){
        // organization id
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization_details) == 0) {
            session()->flash("error", "Invalid organization!");
            return redirect(route("Organizations"));
        }

        // get the rest of the account details
        $account_users = DB::select("SELECT * FROM `admin_tables` WHERE `organization_id` = ?",[$organization_id]);
        $package_used = DB::select("SELECT * FROM `packages` WHERE `package_id` = ?",[$organization_details[0]->package_name]);
        $packages = DB::select("SELECT * FROM `packages` WHERE `status` = '1';");

        // get expiry
        $exp_date = $this->get_expiry($organization_id);
        $expiry_date = $exp_date['date'];
        
        // get the organization view file
        $today = date("YmdHis");
        $lenience = $exp_date['success'] ? $this->dateDiffInDays($expiry_date,$today) : $exp_date['reason'];
        $organization_details[0]->lenience = $exp_date['success'] ? ($lenience < 0 ? 0 : $lenience) : $exp_date['reason'];
        $expiry_date = $exp_date['success'] ? date("D dS M Y @ h:i:sA",strtotime($exp_date['date'])) : $exp_date['reason'];

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

        return view("Orgarnizations.view",["administrator_count" => $administrator_count, "transaction_count" => $transaction_count, "routers_count" => $routers_count, "sms_count" => $sms_count, "client_count" => $client_count, "expiry_date" => $expiry_date, "packages" => $packages, "organization_details" => $organization_details[0], "account_users" => $account_users, "package_used" => $package_used]);
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

    function update_organization (Request $request,$organization_id){
        // organization id
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization_details) == 0) {
            session()->flash("error", "Invalid organization!");
            return redirect(route("Organizations"));
        }

        // organization 
        $organization_name = $request->input("organization_name");
        $organization_location = $request->input("organization_location");
        $organization_contacts = $request->input("organization_contacts");
        $organization_email = $request->input("organization_email");
        $business_short_code = $request->input("business_short_code");
        $client_package = $request->input("client_package");

        // update the organization
        $update = DB::update("UPDATE `organizations` SET `organization_name` = ?, `BusinessShortCode` = ?, `organization_address` = ?, `organization_main_contact` = ?, `organization_email` = ?, `package_name` = ? WHERE `organization_id` = ?",
        [$organization_name,$business_short_code,$organization_location,$organization_contacts,$organization_email,$client_package,$organization_id]);
        
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
        $last_renewal_date = ($organization[0]->account_renewal_date == null) ? date("YmdHis") : $organization[0]->account_renewal_date;
        
        // add the number of days
        $today = date("YmdHis")*1;

        // add the days
        $period_to_add = $request->input("linience_days")." days";
        $last_renewal_date = $this->addPeriodToDate($last_renewal_date,$period_to_add);
        // return [$last_renewal_date, $a,$organization];

        // check if the data is yesterday`s
        // $last_renewal_date = ($last_renewal_date*1) < $today ? $today : $last_renewal_date;
        
        // update the database
        $update = DB::update("UPDATE `organizations` SET `account_renewal_date` = ?, `lenience` = ? WHERE `organization_id` = ?",[$last_renewal_date,$request->input("linience_days"),$organization_id]);
        
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
        $database_name = $organization_details[0]->organization_database;

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
            // log message
            if ($freeze_type == "definate"){
                $txt = $client_data[0]->client_name." has been frozen for $days untill ".date("dS M Y ",strtotime($freeze_date))." by ".session('Usernames')."!";
            }else{
                $txt = $client_data[0]->client_name." has been frozen for Indefinately by ".session('Usernames')."!";
            }
            // log txt
            // $this->log($txt);
            // end of log file
            return redirect(route("viewOrganizationClient",[$organization_id, $client_id]));
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
                return redirect("Clients/View/".$client_id);
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

            if ($freeze_type == "definate"){
                $txt = $client_data[0]->client_name." will be frozen on ".date("D dS M Y",strtotime($freezing_date))." for $days untill ".date("dS M Y ",strtotime($freeze_date)).". Action done by ".session('Usernames')."!";
            }else{
                $txt = $client_data[0]->client_name." will be frozen on ".date("D dS M Y",strtotime($freezing_date))." Indefinately. Action done by ".session('Usernames')."!";
            }
            return redirect(route("viewOrganizationClient",[$organization_id, $client_id]));
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
