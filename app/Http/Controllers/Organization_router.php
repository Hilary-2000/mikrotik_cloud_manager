<?php

namespace App\Http\Controllers;

use App\Classes\routeros_api;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// set the timezone
date_default_timezone_set('Africa/Nairobi');
class Organization_router extends Controller
{
    //get the organization routers
    function view_routers($organization_id){
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

        // here we get the router data
        $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `deleted` = '0' ORDER BY `router_id` DESC;");
        for ($index=0; $index < count($router_data); $index++) {
            $users = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `router_name` = ?",[$router_data[$index]->router_id]);
            $router_data[$index]->user_count = count($users);
        }
        return view("Orgarnizations.Routers.index",["organization_details" => $organization_details[0], 'router_data'=>$router_data]);
    }

    function updateRouter(Request $request, $organization_id){
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

        // return $request;
        $router_id = $request->input("router_id");
        $router_name = $request->input("router_name");
        $physical_location = $request->input("physical_location");
        $router_coordinates = $request->input("router_coordinates");
        $winbox_ports = $request->input("winbox_ports");
        $api_ports = $request->input("api_ports");

        $update = DB::connection("mysql2")->update("UPDATE `remote_routers` SET `router_name` = ?, `api_port` = ?, `winbox_port` = ?,`router_location` = ?, `router_coordinates` = ? WHERE `router_id` = ?",[$router_name,$api_ports,$winbox_ports,$physical_location,$router_coordinates,$router_id]);

        // sesssion
        session()->flash("success_router","Router details updated successfully!");
        
        // get the router details
        $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `router_id` = ?",[$router_id]);
        $router_name = count($router_data) > 0 ? $router_data[0]->router_name : "Null";
        return redirect(url()->route("view_router_details",[$organization_id, $router_id]));
    }
    // connect router
    function connect_router($organization_id, $router_id){
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

        // check if the router is active
        // check first if the router configuration is done
        $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `router_id` = ?",[$router_id]);

        if (count($router_data) == 0) {
            session()->flash("error_router","Invalid router");
            redirect(url()->route("view_router_cloud"));
        }

        // get all clients under that router
        $client_details = DB::connection("mysql2")->select("SELECT COUNT(*) AS 'Total' FROM `client_tables` WHERE `router_name` = ?",[$router_id]);

        // get the router details
        $router_detail = [];

        // connect to the router and get its details

        // connect to the router and set the sstp client
        $sstp_value = $this->getSSTPAddress($database_name);
        if ($sstp_value == null) {
            $error = "The SSTP server is not set, Contact your administrator!";
            session()->flash("error_router",$error);
            return redirect(url()->route("view_router_cloud"));
        }

        // connect to the router and set the sstp client
        $ip_address = $sstp_value->ip_address;
        $user = $sstp_value->username;
        $pass = $sstp_value->password;
        $port = $sstp_value->port;

        // check if the router is actively connected
        $client_router_ip = $this->checkActive($ip_address,$user,$pass,$port,$router_data[0]->sstp_username);

        $router_stats = [];
        if ($client_router_ip) {
            // get the router details
            $API = new routeros_api();
            $API->debug = false;
            
            $ip_address = $client_router_ip;
            $user = $router_data[0]->sstp_username;
            $pass = $router_data[0]->sstp_password;
            $port = $router_data[0]->api_port;
            if ($API->connect($ip_address, $user, $pass, $port)){
                $router_stats = $API->comm("/system/resource/print");
            }else{
                session()->flash("error_router","Cannot connect to router, ensure you have configured the router correctly!");
                return redirect(url()->route("view_router_cloud",[$router_id]));
            }
        }else{
            session()->flash("error_router","Cannot connect to router, ensure you have configured the router correctly!");
            return redirect(url()->route("view_router_cloud",[$router_id]));
        }
        
        // change the status from unconnected to connected
        $update = DB::connection("mysql2")->update("UPDATE `remote_routers` SET `activated` = '1' WHERE `router_id` = ?",[$router_id]);

        // return to the main page
        return redirect(url()->route("view_router_cloud",[$router_id]));
    }

    // view_router_details
    function view_router_details($organization_id, $router_id){
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

        // change db
        $change_db = new login();
        $change_db->change_db();

        // check first if the router configuration is done
        $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `router_id` = ?",[$router_id]);

        if (count($router_data) == 0) {
            session()->flash("error_router","Invalid router");
            redirect(url()->route("view_router_cloud"));
        }

        // get all clients under that router
        $client_details = DB::connection("mysql2")->select("SELECT COUNT(*) AS 'Total' FROM `client_tables` WHERE `router_name` = ?",[$router_id]);

        // get the router details
        $router_detail = [];

        // connect to the router and get its details

        // connect to the router and set the sstp client
        $sstp_value = $this->getSSTPAddress($database_name);
        if ($sstp_value == null) {
            $error = "The SSTP server is not set, Contact your administrator!";
            session()->flash("error_router",$error);
            return redirect(url()->route("view_router_cloud"));
        }

        // connect to the router and set the sstp client
        $ip_address = $sstp_value->ip_address;
        $user = $sstp_value->username;
        $pass = $sstp_value->password;
        $port = $sstp_value->port;

        // check if the router is actively connected
        $client_router_ip = $this->checkActive($ip_address,$user,$pass,$port,$router_data[0]->sstp_username);

        $router_stats = [];
        if ($client_router_ip) {
            // get the router details
            $API = new routeros_api();
            $API->debug = false;
            
            $ip_address = $client_router_ip;
            $user = $router_data[0]->sstp_username;
            $pass = $router_data[0]->sstp_password;
            $port = $router_data[0]->api_port;
            if ($API->connect($ip_address, $user, $pass, $port)){
                $router_stats = $API->comm("/system/resource/print");
            }
        }
        // return $router_stats;

        return view("Orgarnizations.Routers.view",["organization_details" => $organization_details[0], "router_data" => $router_data, "router_stats" => $router_stats, "user_count" => $client_details,"router_detail" => $router_detail, "ip_address" => $ip_address]);
        // return view("Orgarnizations.Routers.view",["organization_details" => $organization_details[0], "router_data" => $router_data, "router_stats" => $router_stats, "user_count" => $client_details,"router_detail" => $router_detail, "ip_address" => $ip_address]);
    }

    // connect router
    function connect_organization_router($organization_id, $router_id){
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

        // check if the router is active
        // check first if the router configuration is done
        $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `router_id` = ?",[$router_id]);

        if (count($router_data) == 0) {
            session()->flash("error_router","Invalid router");
            redirect(url()->route("view_routers",[$organization_id]));
        }

        // connect to the router and get its details

        // connect to the router and set the sstp client
        $sstp_value = $this->getSSTPAddress($database_name);
        if ($sstp_value == null) {
            $error = "The SSTP server is not set, Contact your administrator!";
            session()->flash("error_router",$error);
            return redirect(url()->route("view_routers",[$organization_id]));
        }

        // connect to the router and set the sstp client
        $ip_address = $sstp_value->ip_address;
        $user = $sstp_value->username;
        $pass = $sstp_value->password;
        $port = $sstp_value->port;

        // check if the router is actively connected
        $client_router_ip = $this->checkActive($ip_address,$user,$pass,$port,$router_data[0]->sstp_username);

        $router_stats = [];
        if ($client_router_ip) {
            // get the router details
            $API = new routeros_api();
            $API->debug = false;
            
            $ip_address = $client_router_ip;
            $user = $router_data[0]->sstp_username;
            $pass = $router_data[0]->sstp_password;
            $port = $router_data[0]->api_port;
            if ($API->connect($ip_address, $user, $pass, $port)){
                $router_stats = $API->comm("/system/resource/print");
            }else{
                session()->flash("error_router","Cannot connect to router, ensure you have configured the router correctly!");
                return redirect(url()->route("view_router_details",[$organization_id, $router_id]));
            }
        }else{
            session()->flash("error_router","Cannot connect to router, ensure you have configured the router correctly!");
            return redirect(url()->route("view_router_details",[$organization_id, $router_id]));
        }
        
        // change the status from unconnected to connected
        $update = DB::connection("mysql2")->update("UPDATE `remote_routers` SET `activated` = '1' WHERE `router_id` = ?",[$router_id]);

        // return to the main page
        return redirect(url()->route("view_router_details",[$organization_id, $router_id]));
    }
    
    // delete router
    function delete_router($organization_id, $router_id){
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

        // get the router details
        $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `router_id` = ?",[$router_id]);
        if (count($router_data) == 0) {
            session()->flash("error_router","The router you are deleting is invalid!");
            return redirect(url()->route("view_routers",[$organization_id]));
        }
        
        // create a SSTP secret on the SSTP server
        // get the server details
        $sstp_settings = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `keyword` = 'sstp_server'");
        if (count($sstp_settings) == 0) {
            session()->flash("error_router","The SSTP server is not set, Contact your administrator!");
            return redirect(url()->previous());
        }

        // connect to the server
        $sstp_value = $this->isJson($sstp_settings[0]->value) ? json_decode($sstp_settings[0]->value) : null;

        if ($sstp_value == null) {
            session()->flash("error_router","The SSTP server is not set, Contact your administrator!");
            return redirect(url()->previous());
        }

        // connect to the router and set the sstp client
        $ip_address = $sstp_value->ip_address;
        $user = $sstp_value->username;
        $pass = $sstp_value->password;
        $port = $sstp_value->port;


        // connect to the router
        $API = new routeros_api();
        $API->debug = false;
        if ($API->connect($ip_address,$user,$pass,$port)){
            // get the router username and password and delete it from the list of profiles in the system
            // return $API->comm("/ppp/secret/print");
            // create a ppp profile
            $ppp_secrets = $API->comm("/ppp/secret/print");
            // return $ppp_secrets;
            $id = false;
            for ($index=0; $index < count($ppp_secrets); $index++) {
                if ($ppp_secrets[$index]['name'] == $router_data[0]->sstp_username && $ppp_secrets[$index]['password'] == $router_data[0]->sstp_password) {
                    $id = $ppp_secrets[$index]['.id'];
                    break;
                }
            }

            // delete the profile if the id is found
            if ($id) {
                $API->comm("/ppp/secret/remove",array(
                    ".id" => $id
                ));
            }
            $API->disconnect();
        }

        // delete users associated to the router
        // $delete = DB::connection("mysql2")->delete("DELETE FROM `client_tables` WHERE `router_name` = '".$router_id."'");
        $UPDATE = DB::connection("mysql2")->update("UPDATE `client_tables` SET `date_changed` = ?, `deleted` = ? WHERE `router_name` = ?",[date("YmdHis"),"1",$router_id]);
        
        // delete the router
        DB::connection("mysql2")->update("UPDATE `remote_routers` SET `date_changed` = ?, `deleted` = '1' WHERE `router_id` = ?",[date("YmdHis"),$router_id]);
        
        // get the router details
        $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `router_id` = ?",[$router_id]);
        $router_name = count($router_data) > 0 ? $router_data[0]->router_name : "Null";

        // delete router
        DB::connection("mysql2")->delete("DELETE FROM `remote_routers` WHERE `router_id` = ?",[$router_id]);
        
        // DB::connection("mysql2")->delete("DELETE FROM `router_tables` WHERE `router_id` = '".$router_id."'");
        session()->flash("success_router","Router deleted Successfully!");

        // log routers
        $new_client = new Clients();
        $txt = ":Router (".$router_name.") deleted successfully!";
        // $new_client->log($txt);

        // redirect url
        return redirect(url()->route("view_routers",[$organization_id]));
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
}
