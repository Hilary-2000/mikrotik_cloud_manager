<?php

namespace App\Http\Controllers;

use App\Classes\reports\FPDF;
use App\Classes\reports\PDF;
use App\Classes\routeros_api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// use PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Php;
use App\Models\router_table;
use App\Models\client_table;
use App\Models\sms_table;
use Illuminate\Routing\Route;
use mysqli;

date_default_timezone_set('Africa/Nairobi');
class Clients extends Controller
{
    // check json structure
    function isJson_report($string) {
        return ((is_string($string) &&
                (is_object(json_decode($string)) ||
                is_array(json_decode($string))))) ? true : false;
    }
    //here we get the clients information from the database
    function getClientData(){
<<<<<<< HEAD
        // change db
        $change_db = new login();
        $change_db->change_db();

        $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' ORDER BY `client_id` DESC;");
        $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `deleted` = '0'");
        // return $client_data;
        // get all the clients that have been frozen
        $frozen_clients = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `client_freeze_status` = '1'");
=======
        $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' ORDER BY `client_id` DESC;");
        $router_data = DB::select("SELECT * FROM `router_tables` WHERE `deleted` = '0'");
        // return $client_data;
        // get all the clients that have been frozen
        $frozen_clients = DB::select("SELECT * FROM `client_tables` WHERE `client_freeze_status` = '1'");
>>>>>>> origin/main
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
        return view('myclients',["frozen_clients" => $frozen_clients,'client_data'=>$client_data,"router_infor" => $router_data]);
    }
    
    function generateReports(Request $req){
<<<<<<< HEAD
        // change db
        $change_db = new login();
        $change_db->change_db();

=======
>>>>>>> origin/main
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
<<<<<<< HEAD
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
=======
                    $clients_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' ORDER BY `clients_reg_date` DESC");
                }elseif ($client_registration_date_option == "select date") {
                    $title = "Clients Registered on ".date("D dS M Y",strtotime($select_registration_date));
                    $clients_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `clients_reg_date` LIKE '".date("Ymd",strtotime($select_registration_date))."%' ORDER BY `clients_reg_date` DESC");
                }elseif ($client_registration_date_option == "between dates") {
                    $title = "Clients Registered between ".date("D dS M Y",strtotime($from_select_date))." AND ".date("D dS M Y",strtotime($to_select_date));
                    $clients_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `clients_reg_date` BETWEEN '".date("YmdHis",strtotime($from_select_date))."' AND '".date("Ymd",strtotime($to_select_date))."235959"."' ORDER BY `clients_reg_date` DESC");
                }else{
                    $title = "Clients Registered";
                    $clients_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' ORDER BY `clients_reg_date` DESC");
                }
            }elseif (($client_statuses == "1" || $client_statuses == "0") && $select_router_option != "All") {
                $status = $client_statuses == "0" ? "In-Active" : "Active";
                $router_data = DB::select("SELECT * FROM `router_tables` WHERE `deleted` = '0' AND `router_id` = ?",[$select_router_option]);
>>>>>>> origin/main
                $router_name = count($router_data) > 0 ? $router_data[0]->router_name : "Null";

                if ($client_registration_date_option == "all dates") {
                    $title = "All ".$status." Clients Registered in Router: ".ucwords(strtolower($router_name));
<<<<<<< HEAD
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
=======
                    $clients_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_status` = ? AND `router_name` = ? ORDER BY `clients_reg_date` DESC",[$client_statuses,$select_router_option]);
                }elseif ($client_registration_date_option == "select date") {
                    $title = $status." Clients Registered on ".date("D dS M Y",strtotime($select_registration_date))." in Router: ".ucwords(strtolower($router_name));
                    $clients_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_status` = ? AND `router_name` = ? AND `clients_reg_date` LIKE '".date("Ymd",strtotime($select_registration_date))."%' ORDER BY `clients_reg_date` DESC",[$client_statuses,$select_router_option]);
                }elseif ($client_registration_date_option == "between dates") {
                    $title = $status." Clients Registered between ".date("D dS M Y",strtotime($from_select_date))." AND ".date("D dS M Y",strtotime($to_select_date))." in Router: ".ucwords(strtolower($router_name));
                    $clients_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_status` = ? AND `router_name` = ? AND `clients_reg_date` BETWEEN '".date("YmdHis",strtotime($from_select_date))."' AND '".date("Ymd",strtotime($to_select_date))."235959"."' ORDER BY `clients_reg_date` DESC",[$client_statuses,$select_router_option]);
                }else{
                    $title = "All ".$status." Clients Registered"." in Router: ".ucwords(strtolower($router_name));
                    $clients_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_status` = ? AND `router_name` = ? AND ORDER BY `clients_reg_date` DESC",[$client_statuses,$select_router_option]);
                }
            }elseif ($client_statuses == "3" && $select_router_option != "All") {
                $router_data = DB::select("SELECT * FROM `router_tables` WHERE `deleted` = '0' AND `router_id` = ?",[$select_router_option]);
>>>>>>> origin/main
                $router_name = count($router_data) > 0 ? $router_data[0]->router_name : "Null";

                if ($client_registration_date_option == "all dates") {
                    $title = "All reffered Clients Registered in Router: ".ucwords(strtolower($router_name));
<<<<<<< HEAD
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
=======
                    $clients_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `reffered_by` IS NOT NULL AND `reffered_by` != '' AND `router_name` = ? ORDER BY `clients_reg_date` DESC",[$select_router_option]);
                }elseif ($client_registration_date_option == "select date") {
                    $title = "Reffered Clients Registered on ".date("D dS M Y",strtotime($select_registration_date))." in Router: ".ucwords(strtolower($router_name));
                    $clients_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `reffered_by` IS NOT NULL AND `reffered_by` != '' AND `router_name` = ? AND `clients_reg_date` LIKE '".date("Ymd",strtotime($select_registration_date))."%' ORDER BY `clients_reg_date` DESC",[$select_router_option]);
                }elseif ($client_registration_date_option == "between dates") {
                    $title = "Reffered Clients Registered between ".date("D dS M Y",strtotime($from_select_date))." AND ".date("D dS M Y",strtotime($to_select_date))." in Router: ".ucwords(strtolower($router_name));
                    $clients_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `reffered_by` IS NOT NULL AND `reffered_by` != '' AND `router_name` = ? AND `clients_reg_date` BETWEEN '".date("YmdHis",strtotime($from_select_date))."' AND '".date("Ymd",strtotime($to_select_date))."235959"."' ORDER BY `clients_reg_date` DESC",[$select_router_option]);
                }else{
                    $title = "All reffered Clients Registered"." in Router: ".ucwords(strtolower($router_name));
                    $clients_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `reffered_by` IS NOT NULL AND `reffered_by` != '' AND `router_name` = ? AND ORDER BY `clients_reg_date` DESC",[$select_router_option]);
                }
            }elseif (($client_statuses == "4" || $client_statuses == "5") && $select_router_option != "All") {
                $assignment = $client_statuses == "4" ? "static":"pppoe";
                $router_data = DB::select("SELECT * FROM `router_tables` WHERE `deleted` = '0' AND `router_id` = ?",[$select_router_option]);
>>>>>>> origin/main
                $router_name = count($router_data) > 0 ? $router_data[0]->router_name : "Null";

                if ($client_registration_date_option == "all dates") {
                    $title = "All ".$assignment." assigned Clients Registered in Router: ".ucwords(strtolower($router_name));
<<<<<<< HEAD
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
=======
                    $clients_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `assignment` = ? AND `router_name` = ? ORDER BY `clients_reg_date` DESC",[$assignment,$select_router_option]);
                }elseif ($client_registration_date_option == "select date") {
                    $title = "".$assignment." assigned Clients Registered on ".date("D dS M Y",strtotime($select_registration_date))." in Router: ".ucwords(strtolower($router_name));
                    $clients_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `assignment` = ? AND `router_name` = ? AND `clients_reg_date` LIKE '".date("Ymd",strtotime($select_registration_date))."%' ORDER BY `clients_reg_date` DESC",[$assignment,$select_router_option]);
                }elseif ($client_registration_date_option == "between dates") {
                    $title = "".$assignment." assigned Clients Registered between ".date("D dS M Y",strtotime($from_select_date))." AND ".date("D dS M Y",strtotime($to_select_date))." in Router: ".ucwords(strtolower($router_name));
                    $clients_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `assignment` = ? AND `router_name` = ? AND `clients_reg_date` BETWEEN '".date("YmdHis",strtotime($from_select_date))."' AND '".date("Ymd",strtotime($to_select_date))."235959"."' ORDER BY `clients_reg_date` DESC",[$assignment,$select_router_option]);
                }else{
                    $title = "All ".$assignment." assigned Clients Registered in Router: ".ucwords(strtolower($router_name));
                    $clients_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `assignment` = ? AND `router_name` = ? AND ORDER BY `clients_reg_date` DESC",[$assignment,$select_router_option]);
                }
            }elseif ($select_router_option != "All" && $client_statuses == "2") {
                $status = $client_statuses == "0" ? "In-Active" : "Active";
                $router_data = DB::select("SELECT * FROM `router_tables` WHERE `deleted` = '0' AND `router_id` = ?",[$select_router_option]);
>>>>>>> origin/main
                $router_name = count($router_data) > 0 ? $router_data[0]->router_name : "Null";

                if ($client_registration_date_option == "all dates") {
                    $title = "All Clients Registered in Router: ".ucwords(strtolower($router_name));
<<<<<<< HEAD
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
=======
                    $clients_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `router_name` = ? ORDER BY `clients_reg_date` DESC",[$select_router_option]);
                }elseif ($client_registration_date_option == "select date") {
                    $title = "Clients Registered on ".date("D dS M Y",strtotime($select_registration_date))." in Router: ".ucwords(strtolower($router_name));
                    $clients_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `router_name` = ? AND `clients_reg_date` LIKE '".date("Ymd",strtotime($select_registration_date))."%' ORDER BY `clients_reg_date` DESC",[$select_router_option]);
                }elseif ($client_registration_date_option == "between dates") {
                    $title = "Clients Registered between ".date("D dS M Y",strtotime($from_select_date))." AND ".date("D dS M Y",strtotime($to_select_date))." in Router: ".ucwords(strtolower($router_name));
                    $clients_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `router_name` = ? AND `clients_reg_date` BETWEEN '".date("YmdHis",strtotime($from_select_date))."' AND '".date("Ymd",strtotime($to_select_date))."235959"."' ORDER BY `clients_reg_date` DESC",[$select_router_option]);
                }else{
                    $title = "All Clients Registered"." in Router: ".ucwords(strtolower($router_name));
                    $clients_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `router_name` = ? AND ORDER BY `clients_reg_date` DESC",[$select_router_option]);
>>>>>>> origin/main
                }
            }elseif ($select_router_option == "All" && ($client_statuses == "1" || $client_statuses == "0")) {
                $status = $client_statuses == "0" ? "In-Active" : "Active";

                if ($client_registration_date_option == "all dates") {
                    $title = "All ".$status." Clients Registered";
<<<<<<< HEAD
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
=======
                    $clients_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_status` = ? ORDER BY `clients_reg_date` DESC",[$client_statuses]);
                }elseif ($client_registration_date_option == "select date") {
                    $title = $status." Clients Registered on ".date("D dS M Y",strtotime($select_registration_date))."";
                    $clients_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_status` = ? AND `clients_reg_date` LIKE '".date("Ymd",strtotime($select_registration_date))."%' ORDER BY `clients_reg_date` DESC",[$client_statuses]);
                }elseif ($client_registration_date_option == "between dates") {
                    $title = $status." Clients Registered between ".date("D dS M Y",strtotime($from_select_date))." AND ".date("D dS M Y",strtotime($to_select_date))."";
                    $clients_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_status` = ? AND `clients_reg_date` BETWEEN '".date("YmdHis",strtotime($from_select_date))."' AND '".date("Ymd",strtotime($to_select_date))."235959"."' ORDER BY `clients_reg_date` DESC",[$client_statuses]);
                }else{
                    $title = "All ".$status." Clients Registered"."";
                    $clients_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_status` = ? AND ORDER BY `clients_reg_date` DESC",[$client_statuses]);
>>>>>>> origin/main
                }
            }elseif ($select_router_option == "All" && $client_statuses == "3"){
                
                if ($client_registration_date_option == "all dates") {
                    $title = "All reffered Clients Registered";
<<<<<<< HEAD
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
=======
                    $clients_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `reffered_by` IS NOT NULL AND `reffered_by` != '' ORDER BY `clients_reg_date` DESC");
                }elseif ($client_registration_date_option == "select date") {
                    $title = "Reffered Clients Registered on ".date("D dS M Y",strtotime($select_registration_date))."";
                    $clients_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND  `reffered_by` IS NOT NULL AND `reffered_by` != '' AND `clients_reg_date` LIKE '".date("Ymd",strtotime($select_registration_date))."%' ORDER BY `clients_reg_date` DESC");
                }elseif ($client_registration_date_option == "between dates") {
                    $title = "Reffered Clients Registered between ".date("D dS M Y",strtotime($from_select_date))." AND ".date("D dS M Y",strtotime($to_select_date))."";
                    $clients_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `reffered_by` IS NOT NULL AND `reffered_by` != '' AND `clients_reg_date` BETWEEN '".date("YmdHis",strtotime($from_select_date))."' AND '".date("Ymd",strtotime($to_select_date))."235959"."' ORDER BY `clients_reg_date` DESC");
                }else{
                    $title = "All reffered Clients Registered"."";
                    $clients_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `reffered_by` IS NOT NULL AND `reffered_by` != '' AND ORDER BY `clients_reg_date` DESC");
>>>>>>> origin/main
                }
            }elseif (($client_statuses == "4" || $client_statuses == "5") && $select_router_option == "All"){
                $assignment = $client_statuses == "4" ? "static":"pppoe";

                if ($client_registration_date_option == "all dates") {
                    $title = "All ".$assignment." assigned Clients Registered ";
<<<<<<< HEAD
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
=======
                    $clients_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `assignment` = ? ORDER BY `clients_reg_date` DESC",[$assignment]);
                }elseif ($client_registration_date_option == "select date") {
                    $title = "".$assignment." assigned Clients Registered on ".date("D dS M Y",strtotime($select_registration_date))." ";
                    $clients_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `assignment` = ? AND `clients_reg_date` LIKE '".date("Ymd",strtotime($select_registration_date))."%' ORDER BY `clients_reg_date` DESC",[$assignment]);
                }elseif ($client_registration_date_option == "between dates") {
                    $title = "".$assignment." assigned Clients Registered between ".date("D dS M Y",strtotime($from_select_date))." AND ".date("D dS M Y",strtotime($to_select_date))." ";
                    $clients_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `assignment` = ? AND `clients_reg_date` BETWEEN '".date("YmdHis",strtotime($from_select_date))."' AND '".date("Ymd",strtotime($to_select_date))."235959"."' ORDER BY `clients_reg_date` DESC",[$assignment]);
                }else{
                    $title = "All ".$assignment." assigned Clients Registered ";
                    $clients_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `assignment` = ? AND ORDER BY `clients_reg_date` DESC",[$assignment]);
>>>>>>> origin/main
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
<<<<<<< HEAD
            // organization logo.
            if (session("organization_logo")) {
                $pdf->setCompayLogo("../../../../../../../../..".public_path(session("organization_logo")));
                $pdf->set_company_name(session("organization")->organization_name);
                $pdf->set_school_contact(session("organization")->organization_main_contact);
            }
=======
>>>>>>> origin/main
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
<<<<<<< HEAD
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
=======
                    $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' ORDER BY `clients_reg_date` DESC");
                }elseif ($client_statuses == "0" || $client_statuses == "1") {
                    $status = $client_statuses == "1" ? "Active" : "In-Active";
                    $title = "All ".$status." Clients Registered";
                    $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_status` = ? ORDER BY `clients_reg_date` DESC",[$client_statuses]);
                }elseif ($client_statuses == "3") {
                    $title = "All reffered Clients Registered";
                    $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `reffered_by` IS NOT NULL AND `reffered_by` != '' ORDER BY `clients_reg_date` DESC");
                }elseif ($client_statuses == "4" || $client_statuses == "5") {
                    $assignment = $client_statuses == "4" ? "static":"pppoe";
                    $title = "All ".$assignment." assigned Clients Registered";
                    $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `assignment` = ? ORDER BY `clients_reg_date` DESC",[$assignment]);
                }else{
                    $title = "All Clients Registered";
                    $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' ORDER BY `clients_reg_date` DESC");
                }
            }elseif ($select_router_option != "All") {
                $router_data = DB::select("SELECT * FROM `router_tables` WHERE `deleted` = '0' AND `router_id` = ?",[$select_router_option]);
                $router_name = count($router_data) > 0 ? $router_data[0]->router_name : "Null";
                if ($client_statuses == "2") {
                    $title = "All Clients Registered in Router: ".$router_name."";
                    $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `router_name` = ? ORDER BY `clients_reg_date` DESC",[$select_router_option]);
                }elseif ($client_statuses == "0" || $client_statuses == "1") {
                    $status = $client_statuses == "1" ? "Active" : "In-Active";
                    $title = "All ".$status." Clients Registered in Router: ".$router_name."";
                    $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_status` = ? AND `router_name` = ? ORDER BY `clients_reg_date` DESC",[$client_statuses,$select_router_option]);
                }elseif ($client_statuses == "3") {
                    $title = "All reffered Clients Registered in Router: ".$router_name."";
                    $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `reffered_by` IS NOT NULL AND `reffered_by` != '' AND `router_name` = ? ORDER BY `clients_reg_date` DESC",[$select_router_option]);
                }elseif ($client_statuses == "4" || $client_statuses == "5") {
                    $assignment = $client_statuses == "4" ? "static":"pppoe";
                    $title = "All ".$assignment." assigned Clients Registered in Router: ".$router_name."";
                    $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `assignment` = ? AND `router_name` = ? ORDER BY `clients_reg_date` DESC",[$assignment,$select_router_option]);
                }else{
                    $title = "All Clients Registered in Router: ".$router_name."";
                    $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `router_name` = ? ORDER BY `clients_reg_date` DESC",[$select_router_option]);
>>>>>>> origin/main
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
<<<<<<< HEAD
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
=======
                    $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' ORDER BY `clients_reg_date` DESC");
                }elseif ($client_statuses == "0" || $client_statuses == "1") {
                    $status = $client_statuses == "1" ? "Active" : "In-Active";
                    $title = "All ".$status." Clients Registered";
                    $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_status` = ? ORDER BY `clients_reg_date` DESC",[$client_statuses]);
                }elseif ($client_statuses == "3") {
                    $title = "All reffered Clients Registered";
                    $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `reffered_by` IS NOT NULL AND `reffered_by` != '' ORDER BY `clients_reg_date` DESC");
                }elseif ($client_statuses == "4" || $client_statuses == "5") {
                    $assignment = $client_statuses == "4" ? "static":"pppoe";
                    $title = "All ".$assignment." assigned Clients Registered";
                    $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `assignment` = ? ORDER BY `clients_reg_date` DESC",[$assignment]);
                }else{
                    $title = "All Clients Registered";
                    $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' ORDER BY `clients_reg_date` DESC");
                }
            }elseif ($select_router_option != "All") {
                $router_data = DB::select("SELECT * FROM `router_tables` WHERE `deleted` = '0' AND `router_id` = ?",[$select_router_option]);
                $router_name = count($router_data) > 0 ? $router_data[0]->router_name : "Null";
                if ($client_statuses == "2") {
                    $title = "All Clients Registered in Router: ".$router_name."";
                    $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `router_name` = ? ORDER BY `clients_reg_date` DESC",[$select_router_option]);
                }elseif ($client_statuses == "0" || $client_statuses == "1") {
                    $status = $client_statuses == "1" ? "Active" : "In-Active";
                    $title = "All ".$status." Clients Registered in Router: ".$router_name."";
                    $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_status` = ? AND `router_name` = ? ORDER BY `clients_reg_date` DESC",[$client_statuses,$select_router_option]);
                }elseif ($client_statuses == "3") {
                    $title = "All reffered Clients Registered in Router: ".$router_name."";
                    $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `reffered_by` IS NOT NULL AND `reffered_by` != '' AND `router_name` = ? ORDER BY `clients_reg_date` DESC",[$select_router_option]);
                }elseif ($client_statuses == "4" || $client_statuses == "5") {
                    $assignment = $client_statuses == "4" ? "static":"pppoe";
                    $title = "All ".$assignment." assigned Clients Registered in Router: ".$router_name."";
                    $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `assignment` = ? AND `router_name` = ? ORDER BY `clients_reg_date` DESC",[$assignment,$select_router_option]);
                }else{
                    $title = "All Clients Registered in Router: ".$router_name."";
                    $client_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `router_name` = ? ORDER BY `clients_reg_date` DESC",[$select_router_option]);
>>>>>>> origin/main
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

    function getRouterName($router_id){
<<<<<<< HEAD
        // change db
        $change_db = new login();
        $change_db->change_db();

        $router_data = DB::connection("mysql2")->select("SELECT * FROM `router_tables` WHERE `deleted` = '0' AND `router_id` = ?",[$router_id]);
=======
        $router_data = DB::select("SELECT * FROM `router_tables` WHERE `deleted` = '0' AND `router_id` = ?",[$router_id]);
>>>>>>> origin/main
        $router_name = count($router_data) > 0 ? $router_data[0]->router_name : "Null";
        return $router_name;
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
    // get the clients statistics
    function getClients_Statistics(){
<<<<<<< HEAD
        // change db
        $change_db = new login();
        $change_db->change_db();

=======
>>>>>>> origin/main
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

<<<<<<< HEAD
        $clientd_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' ORDER BY `clients_reg_date` ASC");
=======
        $clientd_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' ORDER BY `clients_reg_date` ASC");
>>>>>>> origin/main
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
<<<<<<< HEAD
                $day_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `clients_reg_date` LIKE '".date("Ymd",strtotime($day_1))."%'");
=======
                $day_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `clients_reg_date` LIKE '".date("Ymd",strtotime($day_1))."%'");
>>>>>>> origin/main
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

<<<<<<< HEAD
        $clientd_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' ORDER BY `clients_reg_date` ASC");
=======
        $clientd_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' ORDER BY `clients_reg_date` ASC");
>>>>>>> origin/main
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
<<<<<<< HEAD
                $months_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `clients_reg_date` LIKE '".date("Ym",strtotime($month_1))."%'");
=======
                $months_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `clients_reg_date` LIKE '".date("Ym",strtotime($month_1))."%'");
>>>>>>> origin/main
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

<<<<<<< HEAD
        $clientd_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' ORDER BY `clients_reg_date` ASC");
=======
        $clientd_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' ORDER BY `clients_reg_date` ASC");
>>>>>>> origin/main

        // get the date the week started when the first client was registered
        $duration_start = $clientd_data[0]->clients_reg_date;
        $end_year = date("Y");
        // return $duration_start." ".$clientd_data[0]->clients_reg_date;

        // start from this first date to today looping through seven days of the week
        $year_1 = date("YmdHis",strtotime($duration_start));
        // return (date("Y",strtotime($year_1))*1)." ".$end_year;
        // store the arrays in the data
        for ($index = (date("Y",strtotime($year_1))*1); $index <= ($end_year*1); $index++) {
<<<<<<< HEAD
            $yearly_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `clients_reg_date` LIKE '".$index."%'");
=======
            $yearly_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `clients_reg_date` LIKE '".$index."%'");
>>>>>>> origin/main
            $cl_data = array("date" => $index,"number" => count($yearly_data));
            
            array_push($clients_statistics_yearly,$cl_data);
            array_push($clients_data_yearly,$yearly_data);
        }
        // return $clients_data_yearly[0][0];
        // return [$clients_statistics_yearly,$clients_data_yearly];
        return view('client-stats',["clients_weekly" => $clients_data,"client_metrics_weekly" => $clients_statistics,"clients_statistics_monthly" => $clients_statistics_monthly,"clients_monthly" => $clients_data_monthly,"clients_statistics_yearly" => $clients_statistics_yearly,"clients_data_yearly" => $clients_data_yearly]);
    }
    function clientsDemographics(Request $req){
<<<<<<< HEAD
        // change db
        $change_db = new login();
        $change_db->change_db();

=======
>>>>>>> origin/main
        $selected_dates = $req->input('selected_dates');
        $from_today = $req->input('from_today');

        $today = date("Ymd")."235959";
        $future = date("Ymd",strtotime($selected_dates))."235959";
        // return $future;
        $clients_data = [];
        // select all clients that are to be due from today to the future
        if ($from_today == "true") {
<<<<<<< HEAD
            $clients_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `next_expiration_date` <= '".$future."' AND `next_expiration_date` >= '".$today."'");
        }else{
            $clients_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `next_expiration_date` <= '".$future."'");
=======
            $clients_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `next_expiration_date` <= '".$future."' AND `next_expiration_date` >= '".$today."'");
        }else{
            $clients_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `next_expiration_date` <= '".$future."'");
>>>>>>> origin/main
        }

        return $clients_data;
    }
    function deleteClients(Request $req){
<<<<<<< HEAD
        // change db
        $change_db = new login();
        $change_db->change_db();

=======
>>>>>>> origin/main
        // return $req;
        $hold_user_id_data = $req->input("hold_user_id_data");
        $delete_from_router = $req->input("delete_from_router");
        
        if ($this->isJson_report($hold_user_id_data)) {
            $hold_user_id_data = json_decode($hold_user_id_data);
            for ($inde=0; $inde < count($hold_user_id_data); $inde++) { 
                // return $hold_user_id_data[$inde];
                $data = $this->delete_user_use_acc($hold_user_id_data[$inde],$delete_from_router);
                // return $data;
            }
            session()->flash("success_reg","Clients deleted successfully!");
        }else{
            session()->flash("error_clients","An error occured!");
        }
        return redirect("/Clients");
    }

    // this functions add a router to the database
    function addRouter(Request $req){
<<<<<<< HEAD
        // change db
        $change_db = new login();
        $change_db->change_db();

=======
>>>>>>> origin/main
        // get the user data
        $router_name = $req->input('router_name');
        $ip_address = $req->input('ip_address');
        $api_username = $req->input('api_username');
        $router_api_password = $req->input('router_api_password');
        $router_api_port = $req->input('router_api_port');
        $mac_address = '';

        // check the route connection
        @include("test-api/api_mt_include2.php");
        $API = new routeros_api();
        $API->debug = false;

        // check if the connection is valid
        if ($API->connect($ip_address, $api_username, $router_api_password, $router_api_port)) {
            $API->disconnect();
            // check if the router is in the database default gateway ip address
<<<<<<< HEAD
            $router_present = DB::connection("mysql2")->select("SELECT * FROM `router_tables` WHERE `deleted` = '0' AND `router_ipaddr` = '".$ip_address."'");
=======
            $router_present = DB::select("SELECT * FROM `router_tables` WHERE `deleted` = '0' AND `router_ipaddr` = '".$ip_address."'");
>>>>>>> origin/main
            // save the routers data to the database
            if (count($router_present) < 1) {
                $routerTable = new router_table();
                // $routerTable->router_id = 'NULL';
                $routerTable->router_name = $router_name;
                $routerTable->router_ipaddr = $ip_address;
                $routerTable->router_api_username = $api_username;
                $routerTable->router_api_password = $router_api_password;
                $routerTable->router_api_port = $router_api_port;
                $routerTable->router_status = '1';
                $routerTable->save();
                session()->flash("success_router","Router ( $router_name ) successfully added to the system");

<<<<<<< HEAD
                // log message
                $txt = ":New Router successfully added by  ".session('Usernames')."!";
                $this->log($txt);
                // end of log file
                
=======
                // log file capture error
                // read the data 
                $myfile = fopen(public_path("/logs/log.txt"), "r") or die("Unable to open file!");
                $file_sizes = filesize(public_path("/logs/log.txt")) > 0?filesize(public_path("/logs/log.txt")):8190;
                $existing_txt = fread($myfile,$file_sizes);
                // return $existing_txt;
                $myfile = fopen(public_path("/logs/log.txt"), "w") or die("Unable to open file!");
                $date = date("dS M Y (H:i:sa)");
                $txt = $date.":New Router successfully added by  ".session('Usernames')."!\n".$existing_txt;
                // return $txt;
                fwrite($myfile, $txt);
                fclose($myfile);
                // end of log file
>>>>>>> origin/main
                return redirect("/Routers");
            }else {
                session()->flash("error_router","The router with the ip address of '".$ip_address."' is already present!");
                return redirect("/Routers/New");
            }
        }else {
            session()->flash("error_router","Check if the Router is active and the api activated as well!");
            return redirect("/Routers/New");
        }
    }

<<<<<<< HEAD

    function getRouterDataClients(){
        // change db
        $change_db = new login();
        $change_db->change_db();

        // here we get the router data
        $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `deleted` = '0'");
        // GET ALL THE ACCOUNT NUMBERS PRESENT AND STORE THEM AS ARRAYS
        $clients_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' ORDER BY `client_id` DESC;");
=======
    function getRouterData(){
        // here we get the router data
        $router_data = DB::select("SELECT * FROM `router_tables` WHERE `deleted` = '0'");
        return view("myRouter",['router_data'=>$router_data]);
    }

    function getRouterDataClients(){
        // here we get the router data
        $router_data = DB::select("SELECT * FROM `router_tables` WHERE `deleted` = '0'");
        // GET ALL THE ACCOUNT NUMBERS PRESENT AND STORE THEM AS ARRAYS
        $clients_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' ORDER BY `client_id` DESC;");
>>>>>>> origin/main

        // get the clients account numbers and usernames
        $client_accounts = [];
        $client_username = [];
        foreach ($clients_data as $key => $value) {
            // store the clients account number
            array_push($client_accounts,$value->client_account);
            array_push($client_username,$value->client_username);
        }
        // return $client_accounts;
        return view("newClient",['router_data'=>$router_data, "client_accounts" => $client_accounts, "client_username" => $client_username]);
    }
<<<<<<< HEAD


    function getRouterDatappoe(){
        // change db
        $change_db = new login();
        $change_db->change_db();

        // here we get the router data
        $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `deleted` = '0' ");
        // GET ALL THE ACCOUNT NUMBERS PRESENT AND STORE THEM AS ARRAYS
        $clients_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' ORDER BY `client_id` DESC;");
=======
    function getRouterDatappoe(){
        // here we get the router data
        $router_data = DB::select("SELECT * FROM `router_tables` WHERE `deleted` = '0' ");
        // GET ALL THE ACCOUNT NUMBERS PRESENT AND STORE THEM AS ARRAYS
        $clients_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' ORDER BY `client_id` DESC;");
>>>>>>> origin/main

        // get the clients account numbers and usernames
        $client_accounts = [];
        $client_username = [];
        foreach ($clients_data as $key => $value) {
            // store the clients account number
            array_push($client_accounts,$value->client_account);
            array_push($client_username,$value->client_username);
        }
        // return $client_accounts;
        return view("newPPOEclient",['router_data'=>$router_data, "client_accounts" => $client_accounts, "client_username" => $client_username]);
    }

<<<<<<< HEAD
    function getSSTPAddress(){
        // change db
        $change_db = new login();
        $change_db->change_db();

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

    // save client in PPPoE
    function processClientPPPoE(Request $req){
        // change db
        $change_db = new login();
        $change_db->change_db();

=======
    // save client in PPPoE
    function processClientPPPoE(Request $req){
>>>>>>> origin/main
        // ADD IP ADDRESS ADD QUEUE AND ADD FILTER WHEN NEEDED
        // FIRST GET THE USER DATA
        $client_name = $req->input('client_name');
        $client_address = $req->input('client_address');
        $client_phone = $req->input('client_phone');
        $client_monthly_pay = $req->input('client_monthly_pay');
        $pppoe_profile = $req->input('pppoe_profile');
        $router_name = $req->input('router_name');
        $comments = $req->input('comments');
        $allow_router_changes = $req->input('allow_router_changes');
        $client_username = $req->input('client_username');
        $client_password = $req->input('client_password');
        $client_acc_number = $req->input("client_acc_number");
        $location_coordinates = $req->input('location_coordinates');
        $expiration_date = $req->input('expiration_date');
        $client_secret_username = $req->input('client_secret_username');
        $client_secret_password = $req->input('client_secret_password');
        $repeat_secret_password = $req->input('repeat_secret_password');
        $expiration_dates = date("Ymd",strtotime($expiration_date));
        $expiration_dates = date("YmdHis",strtotime($expiration_dates."235959"));
        $minimum_payment = $req->input("minimum_payment");
        // return $req->input();
        // return $location_coordinates;
        // return $expiration_dates;
        session()->flash('client_secret_username',$client_secret_username);
        session()->flash('client_name',$client_name);
        session()->flash('client_address',$client_address);
        session()->flash('client_phone',$client_phone);
        session()->flash('client_monthly_pay',$client_monthly_pay);
        session()->flash('comments',$comments);
        session()->flash('client_username',$client_username);
        session()->flash('client_password',$client_password);
        session()->flash('client_acc_number',$client_acc_number);
        session()->flash('location_coordinates',$location_coordinates);
        session()->flash('expiration_date',$expiration_date);
        session()->flash('minimum_payment',$minimum_payment);
        // validate the user
        $req->validate([
            'client_phone'=>'max:12|min:10',
            'pppoe_profile'=>'required',
            'router_name' => 'required'
        ]);

<<<<<<< HEAD
        $client_account = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_account` = '$client_acc_number'");
=======
        $client_account = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_account` = '$client_acc_number'");
>>>>>>> origin/main
        if (count($client_account) > 0 ) {
            // display an error that the account number is already used
            session()->flash("network_presence","The account number provided is already present");
            session()->flash("account_number_present","The account number provided is already present!");
            return redirect(route("newclient.pppoe"));
        }

        // check if the passwords match
        if ($client_secret_password == $repeat_secret_password) {
<<<<<<< HEAD
            // first check if the users router is connected
            // get the router data
            $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `router_id` = ? AND `deleted` = '0'",[$router_name]);
            if (count($router_data) == 0) {
                $error = "Router selected does not exist!";
                session()->flash("network_presence",$error);
                return redirect(url()->previous());
            }
            
            // get the sstp credentails they are also the api usernames
            $sstp_username = $router_data[0]->sstp_username;
            $sstp_password = $router_data[0]->sstp_password;
            $api_port = $router_data[0]->api_port;

            // connect to the router and set the sstp client
            $sstp_value = $this->getSSTPAddress();
            if ($sstp_value == null) {
                $error = "The SSTP server is not set, Contact your administrator!";
                session()->flash("network_presence",$error);
                return redirect(url()->previous());
            }

            // server settings
            $server_ip_address = $sstp_value->ip_address;
            $user = $sstp_value->username;
            $pass = $sstp_value->password;
            $port = $sstp_value->port;

            // check if the router is actively connected
            $client_router_ip = $this->checkActive($server_ip_address,$user,$pass,$port,$sstp_username);
            // return $client_router_ip;
            
            // connect to the router
            $API = new routeros_api();
            $API->debug = false;
            if ($API->connect($client_router_ip, $sstp_username, $sstp_password, $api_port)){
                if ($allow_router_changes == "on") {
                    // get the IP ADDRES
                    $curl_handle = curl_init();
                    $url = "https://crontab.hypbits.com/getIpaddress.php?db_name=".session("database_name")."&r_id=".$router_name."&r_ppoe_secrets=true";
                    curl_setopt($curl_handle, CURLOPT_URL, $url);
                    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
                    $curl_data = curl_exec($curl_handle);
                    curl_close($curl_handle);
                    $ppp_secrets = json_decode($curl_data, true);
                    
                    // loop through the secrets to see if its present
                    $present_ppp_profile = 0;
                    $secret_id = 0;
                    for ($index=0; $index < count($ppp_secrets); $index++) { 
                        if ($ppp_secrets[$index]['name'] == $client_secret_username) {
                            $present_ppp_profile = 1;
                            $secret_id = $ppp_secrets[$index]['.id'];
                            break;
                        }
                    }
    
                    // create a ppp profile
                    if ($present_ppp_profile == 1) {
                        // update the password and the service
=======
            // continue and register the user
            // check if the name is present in the router

            // get the pppoe profiles and remove and check if the name provided exists
                $curl_handle = curl_init();
                $baseUrl = explode(":",url('/'));
                $local_url = $baseUrl[0].":".$baseUrl[1];
                $url = "$local_url:81/crontab/getIpaddress.php?r_ppoe_secrets=true&r_id=".$router_name;
                // Set the curl URL option
                curl_setopt($curl_handle, CURLOPT_URL, $url);
                // This option will return data as a string instead of direct output
                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
                // Execute curl & store data in a variable
                $curl_data = curl_exec($curl_handle);
                curl_close($curl_handle);
                // Decode JSON into PHP array
                $router_secrets = json_decode($curl_data);
                // loop through the secrets and find the name
                $present = 0;
                $secret_id = 0;
                for ($index=0; $index < count($router_secrets); $index++) { 
                    $secret = $router_secrets[$index];
                    if ($secret->name == $client_secret_username) {
                        $present = 1;
                        foreach ($secret as $key => $value) {
                            if ($key == ".id") {
                                $secret_id = $value;
                                break;
                            }
                        }
                        break;
                    }
                }
                // return $present;

                // if present add a message saying the secret is present and no usre will be added jus add the data to the database
                if ($present == 0) {
                    // this instance the secret is not present
                    // proceed and register the user
                    $router_data = DB::select("SELECT * FROM `router_tables` WHERE `deleted` = '0' AND `router_id` = '$router_name' LIMIT 1");
                    $ip_address = $router_data[0]->router_ipaddr;
                    $router_api_username = $router_data[0]->router_api_username;
                    $router_api_password = $router_data[0]->router_api_password;
                    $router_api_port = $router_data[0]->router_api_port;
            
            
                    // connect to the router and add the ip address and queues to the interface
                    $API = new routeros_api();
                    $API->debug = false;
                    // check if the connection is valid
                    if ($API->connect($ip_address, $router_api_username, $router_api_password, $router_api_port)){
                        // add a new ip address
                        $API->comm("/ppp/secret/add", 
                        array(
                            "name"     => $client_secret_username,
                            "service" => "pppoe",
                            "password" => $client_secret_password,
                            "profile"  => $pppoe_profile,
                            "comment"  => $client_name." (".$client_address." - ".$location_coordinates.") - ".$client_acc_number,
                            "disabled" => "false"
                        ));
                        // proceed and add the user data in the database
                        // add the clients information in the database
                        $clients_table = new client_table();
                        $clients_table->client_name = $client_name;
                        $clients_table->client_address = $client_address;
                        $clients_table->client_secret = $client_secret_username;
                        $clients_table->client_secret_password = $client_secret_password;
                        $clients_table->next_expiration_date = $expiration_dates;
                        $clients_table->monthly_payment = $client_monthly_pay;
                        $clients_table->router_name = $router_name;
                        $clients_table->comment = $req->input('comments');
                        $clients_table->clients_contacts = $client_phone;
                        $clients_table->client_status = "1";
                        $clients_table->payments_status = "1";
                        $clients_table->clients_reg_date = date("YmdHis");
                        $clients_table->client_profile = $pppoe_profile;
                        $clients_table->client_username = $client_username;
                        $clients_table->client_password = $client_password;
                        $clients_table->client_account = $client_acc_number;
                        $clients_table->location_coordinates = $req->input('location_coordinates');
                        $clients_table->assignment = "pppoe";
                        $clients_table->min_amount = $minimum_payment;
                        // return $clients_table;
                        $clients_table->save();
                        session()->flash("success_reg","The user data has been successfully registered!");
    
                        // log file capture error
                        // read the data 
                        $myfile = fopen(public_path("/logs/log.txt"), "r") or die("Unable to open file!");
                        $file_sizes = filesize(public_path("/logs/log.txt")) > 0?filesize(public_path("/logs/log.txt")):8190;
                        $existing_txt = fread($myfile,$file_sizes);
                        // return $existing_txt;
                        $myfile = fopen(public_path("/logs/log.txt"), "w") or die("Unable to open file!");
                        $date = date("dS M Y (H:i:sa)");
                        $txt = $date.":New Client (".$client_name.") successfully registered by  ".session('Usernames')." added to DB only! PPPoE Assignment!\n".$existing_txt;
                        // return $txt;
                        fwrite($myfile, $txt);
                        fclose($myfile);
                        // end of log file
                        

                        // get the sms keys
                        $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'sms_api_key'");
                        $sms_api_key = $sms_keys[0]->value;
                        $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'sms_partner_id'");
                        $sms_partner_id = $sms_keys[0]->value;
                        $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'sms_shortcode'");
                        $sms_shortcode = $sms_keys[0]->value;
                        $partnerID = $sms_partner_id;
                        $apikey = $sms_api_key;
                        $shortcode = $sms_shortcode;

                        $message_contents = $this->get_sms();
                        $message = $message_contents[3]->messages[0]->message;
                        $user_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' ORDER BY `client_id` DESC LIMIT 1;");
                        if ($user_data && $req->input('send_sms') == "on") {
                            $client_id = $user_data[0]->client_id;
                            $mobile = $user_data[0]->clients_contacts;
                            $sms_type = 2;
                            if ($message) {
                                $trans_amount = 0;
                                $message = $this->message_content($message,$client_id,$trans_amount);
                                $finalURL = "https://mysms.celcomafrica.com/api/services/sendsms/?apikey=" . urlencode($apikey) . "&partnerID=" . urlencode($partnerID) . "&message=" . urlencode($message) . "&shortcode=$shortcode&mobile=$mobile";
                                $ch = \curl_init();
                                \curl_setopt($ch, CURLOPT_URL, $finalURL);
                                \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                \curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                                $response = \curl_exec($ch);
                                \curl_close($ch);
                                $res = json_decode($response);
                                // return $res;
                                $values = $res->responses[0];
                                // return $values;
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
                                $sms_table->account_id = $client_id;
                                $sms_table->sms_type = $sms_type;
                                $sms_table->save();
                            }
                        }
                        return redirect("Clients");
                    }

                }else {
                    // this instance the secret is not present
                    // proceed and register the user
                    $router_data = DB::select("SELECT * FROM `router_tables` WHERE `deleted` = '0' AND `router_id` = '$router_name' LIMIT 1");
                    $ip_address = $router_data[0]->router_ipaddr;
                    $router_api_username = $router_data[0]->router_api_username;
                    $router_api_password = $router_data[0]->router_api_password;
                    $router_api_port = $router_data[0]->router_api_port;
            
            
                    // connect to the router and add the ip address and queues to the interface
                    $API = new routeros_api();
                    $API->debug = false;
                    // check if the connection is valid
                    if ($API->connect($ip_address, $router_api_username, $router_api_password, $router_api_port)){
>>>>>>> origin/main
                        // add a new ip address
                        $API->comm("/ppp/secret/set", 
                        array(
                            "name"     => $client_secret_username,
                            "service" => "pppoe",
                            "password" => $client_secret_password,
                            "profile"  => $pppoe_profile,
                            "comment"  => $client_name." (".$client_address." - ".$location_coordinates.") - ".$client_acc_number,
                            "disabled" => "false",
                            ".id" => $secret_id
                        ));
<<<<<<< HEAD

                        // log message
                        $txt = ":New Client (".$client_name.") successfully registered by  ".session('Usernames')." added! PPPoE Assignment! but settings overwritten";
                        $this->log($txt);
                        // end of log file
                    }else{
                        // add the ppp profile
                        // add a new ip address
                        $API->comm("/ppp/secret/add", 
                        array(
                            "name"     => $client_secret_username,
                            "service" => "pppoe",
                            "password" => $client_secret_password,
                            "profile"  => $pppoe_profile,
                            "comment"  => $client_name." (".$client_address." - ".$location_coordinates.") - ".$client_acc_number,
                            "disabled" => "false"
                        ));

                        // log message
                        $txt = ":New Client (".$client_name.") successfully registered by  ".session('Usernames')." added! PPPoE Assignment!";
                        $this->log($txt);
                        // end of log file
                    }
                }

                if ($allow_router_changes == "off") {
                    // log message
                    $txt = ":New Client (".$client_name.") successfully registered by  ".session('Usernames')." added to the database only!";
                    $this->log($txt);
                    // end of log file
                }

                // proceed and add the user data in the database
                // add the clients information in the database
                $clients_table = new client_table();
                $clients_table->client_name = $client_name;
                $clients_table->client_address = $client_address;
                $clients_table->client_secret = $client_secret_username;
                $clients_table->client_secret_password = $client_secret_password;
                $clients_table->next_expiration_date = $expiration_dates;
                $clients_table->monthly_payment = $client_monthly_pay;
                $clients_table->router_name = $router_name;
                $clients_table->comment = $req->input('comments');
                $clients_table->clients_contacts = $client_phone;
                $clients_table->client_status = "1";
                $clients_table->payments_status = "1";
                $clients_table->clients_reg_date = date("YmdHis");
                $clients_table->client_profile = $pppoe_profile;
                $clients_table->client_username = $client_username;
                $clients_table->client_password = $client_password;
                $clients_table->client_account = $client_acc_number;
                $clients_table->location_coordinates = $req->input('location_coordinates');
                $clients_table->assignment = "pppoe";
                $clients_table->min_amount = $minimum_payment;
                // return $clients_table;
                $clients_table->save();
                session()->flash("success_reg","The user data has been successfully registered!");

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

                $message_contents = $this->get_sms();
                $message = $message_contents[3]->messages[0]->message;
                $user_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' ORDER BY `client_id` DESC LIMIT 1;");
                if ($user_data && $req->input('send_sms') == "on") {
                    $client_id = $user_data[0]->client_id;
                    $mobile = $user_data[0]->clients_contacts;
                    $sms_type = 2;
                    if ($message) {
                        $trans_amount = 0;
                        $message = $this->message_content($message,$client_id,$trans_amount);
                        $finalURL = "https://isms.celcomafrica.com/api/services/sendsms/?apikey=" . urlencode($apikey) . "&partnerID=" . urlencode($partnerID) . "&message=" . urlencode($message) . "&shortcode=$shortcode&mobile=$mobile";
                        $ch = \curl_init();
                        \curl_setopt($ch, CURLOPT_URL, $finalURL);
                        \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        \curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        $response = \curl_exec($ch);
                        \curl_close($ch);
                        $res = json_decode($response);
                        // return $res;
                        $values = $res->responses[0];
                        // return $values;
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
                        $sms_table->account_id = $client_id;
                        $sms_table->sms_type = $sms_type;
                        $sms_table->save();
                    }
                }
                return redirect("/Clients");
            }else{
                $error = "New client is not added, check if the Hypbits credentials are still present and not altered!";
                session()->flash("network_presence",$error);
                return redirect(url()->previous());
            }
=======
                        // proceed and add the user data in the database
                        // add the clients information in the database
                        $clients_table = new client_table();
                        $clients_table->client_name = $client_name;
                        $clients_table->client_address = $client_address;
                        $clients_table->client_secret = $client_secret_username;
                        $clients_table->client_secret_password = $client_secret_password;
                        $clients_table->next_expiration_date = $expiration_dates;
                        $clients_table->monthly_payment = $client_monthly_pay;
                        $clients_table->router_name = $router_name;
                        $clients_table->comment = $req->input('comments');
                        $clients_table->clients_contacts = $client_phone;
                        $clients_table->client_status = "1";
                        $clients_table->payments_status = "1";
                        $clients_table->clients_reg_date = date("YmdHis");
                        $clients_table->client_profile = $pppoe_profile;
                        $clients_table->client_username = $client_username;
                        $clients_table->client_password = $client_password;
                        $clients_table->client_account = $client_acc_number;
                        $clients_table->location_coordinates = $req->input('location_coordinates');
                        $clients_table->assignment = "pppoe";
                        $clients_table->min_amount = $minimum_payment;
                        // return $clients_table;
                        $clients_table->save();
                        session()->flash("success_reg","The user data has been successfully registered but the name has been found but setting have been overwritten to the new user`s settings!");
    
                        // log file capture error
                        // read the data 
                        $myfile = fopen(public_path("/logs/log.txt"), "r") or die("Unable to open file!");
                        $file_sizes = filesize(public_path("/logs/log.txt")) > 0?filesize(public_path("/logs/log.txt")):8190;
                        $existing_txt = fread($myfile,$file_sizes);
                        // return $existing_txt;
                        $myfile = fopen(public_path("/logs/log.txt"), "w") or die("Unable to open file!");
                        $date = date("dS M Y (H:i:sa)");
                        $txt = $date.":New Client (".$client_name.") successfully registered by  ".session('Usernames')." added! PPPoE Assignment! but settings overwritten\n".$existing_txt;
                        // return $txt;
                        fwrite($myfile, $txt);
                        fclose($myfile);
                        // end of log file
                        

                        // get the sms keys
                        $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'sms_api_key'");
                        $sms_api_key = $sms_keys[0]->value;
                        $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'sms_partner_id'");
                        $sms_partner_id = $sms_keys[0]->value;
                        $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'sms_shortcode'");
                        $sms_shortcode = $sms_keys[0]->value;
                        $partnerID = $sms_partner_id;
                        $apikey = $sms_api_key;
                        $shortcode = $sms_shortcode;

                        $message_contents = $this->get_sms();
                        $message = $message_contents[3]->messages[0]->message;
                        $user_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' ORDER BY `client_id` DESC LIMIT 1;");
                        if ($user_data && $req->input('send_sms') == "on") {
                            $client_id = $user_data[0]->client_id;
                            $mobile = $user_data[0]->clients_contacts;
                            $sms_type = 2;
                            if ($message) {
                                $trans_amount = 0;
                                $message = $this->message_content($message,$client_id,$trans_amount);
                                $finalURL = "https://mysms.celcomafrica.com/api/services/sendsms/?apikey=" . urlencode($apikey) . "&partnerID=" . urlencode($partnerID) . "&message=" . urlencode($message) . "&shortcode=$shortcode&mobile=$mobile";
                                $ch = \curl_init();
                                \curl_setopt($ch, CURLOPT_URL, $finalURL);
                                \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                \curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                                $response = \curl_exec($ch);
                                \curl_close($ch);
                                $res = json_decode($response);
                                // return $res;
                                $values = $res->responses[0];
                                // return $values;
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
                                $sms_table->account_id = $client_id;
                                $sms_table->sms_type = $sms_type;
                                $sms_table->save();
                            }
                        }
                        return redirect("Clients");
                    }
                }

>>>>>>> origin/main
        }else {
            // return the user to the new client
            // display an error that the account number is already used
            session()->flash("network_presence","The passwords provided does not match!");
            return redirect("/Clients/NewPPPoE");
        }
    }
    // save a new client in the database
    function processNewClient(Request $req){
<<<<<<< HEAD
        // change db
        $change_db = new login();
        $change_db->change_db();

=======
>>>>>>> origin/main
        // ADD IP ADDRESS ADD QUEUE AND ADD FILTER WHEN NEEDED
        // FIRST GET THE USER DATA
        $client_name = $req->input('client_name');
        $client_address = $req->input('client_address');
        $client_phone = $req->input('client_phone');
        $client_monthly_pay = $req->input('client_monthly_pay');
        $client_network = $req->input('client_network');
        $client_gw = $req->input('client_gw');
        $upload_speed = $req->input('upload_speed');
        $unit1 = $req->input('unit1');
        $download_speed = $req->input('download_speed');
        $unit2 = $req->input('unit2');
        $router_name = $req->input('router_name');
        $interface_name = $req->input('interface_name');
        $comments = $req->input('comments');
        $client_username = $req->input('client_username');
        $client_password = $req->input('client_password');
        $client_acc_number = $req->input("client_acc_number");
        $location_coordinates = $req->input('location_coordinates');
        $expiration_date = $req->input('expiration_date');
        $expiration_dates = date("Ymd",strtotime($expiration_date));
        $expiration_dates = date("YmdHis",strtotime($expiration_dates."235959"));
        $minimum_payment = $req->input("minimum_payment");
        // return $req->input();
        // return $location_coordinates;
        // return $expiration_dates;
        session()->flash('client_name',$client_name);
        session()->flash('client_address',$client_address);
        session()->flash('client_phone',$client_phone);
        session()->flash('client_monthly_pay',$client_monthly_pay);
        session()->flash('client_network',$client_network);
        session()->flash('client_gw',$client_gw);
        session()->flash('upload_speed',$upload_speed);
        session()->flash('unit1',$unit1);
        session()->flash('download_speed',$download_speed);
        session()->flash('unit2',$unit2);
        session()->flash('router_name',$router_name);
        session()->flash('interface_name',$interface_name);
        session()->flash('comments',$comments);
        session()->flash('client_username',$client_username);
        session()->flash('client_password',$client_password);
        session()->flash('client_acc_number',$client_acc_number);
        session()->flash('location_coordinates',$location_coordinates);
        session()->flash('expiration_date',$expiration_date);
        session()->flash('minimum_payment',$minimum_payment);
        
        // validate the user
        // return $client_gw;
        $req->validate([
            'client_phone'=>'max:12|min:10',
            'interface_name'=>'required',
            'router_name' => 'required'
        ]);


        // if the clients account number is present dont accept any inputs
<<<<<<< HEAD
        $client_usernamed = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_username` = '$client_username'");
=======
        $client_usernamed = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_username` = '$client_username'");
>>>>>>> origin/main
        if (count($client_usernamed) > 0 ) {
            // display an error that the account number is already used
            session()->flash("network_presence","The username provided is already present!");
            session()->flash("client_username_present","The username provided is already present!");
            return redirect("/Clients/NewStatic");
        }

<<<<<<< HEAD
        $client_account = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_account` = '$client_acc_number'");
=======
        $client_account = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_account` = '$client_acc_number'");
>>>>>>> origin/main
        if (count($client_account) > 0 ) {
            // display an error that the account number is already used
            session()->flash("network_presence","The account number provided is already present");
            session()->flash("account_number_present","The account number provided is already present!");
            return redirect("/Clients/NewStatic");
        }else {
            // check if the client with that username OR client default gateway is present in the system
<<<<<<< HEAD
            $user_information = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_default_gw` = '$client_gw' AND  `router_name` = '".$router_name."'");
    
=======
            $user_information = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_default_gw` = '$client_gw' AND  `router_name` = '".$router_name."'");
    
            $err_pres = 0;
>>>>>>> origin/main
            if (count($user_information) > 0) {
                // the phone number or the client gw is shared
                $error = "The clients address (".$client_gw.") is present in the database and used by ".$user_information[0]->client_name."(".$user_information[0]->client_address.") use another value to proceed or change the user information to suit your new user.";
                session()->flash("network_presence",$error);
                return redirect("Clients/NewStatic");
            }else {
<<<<<<< HEAD
                // check if the selected router is connected
                // get the router data
                $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `router_id` = ? AND `deleted` = '0'",[$router_name]);
                if (count($router_data) == 0) {
                    $error = "Router selected does not exist!";
                    session()->flash("network_presence",$error);
                    return redirect(url()->previous());
                }
                
                // get the sstp credentails they are also the api usernames
                $sstp_username = $router_data[0]->sstp_username;
                $sstp_password = $router_data[0]->sstp_password;
                $api_port = $router_data[0]->api_port;

                // connect to the router and set the sstp client
                $sstp_value = $this->getSSTPAddress();
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
                if ($API->connect($client_router_ip, $sstp_username, $sstp_password, $api_port)){
                    // get the IP ADDRES
                    $curl_handle = curl_init();
                    $url = "https://crontab.hypbits.com/getIpaddress.php?db_name=".session("database_name")."&r_id=".$router_name."&r_ip=true";
                    curl_setopt($curl_handle, CURLOPT_URL, $url);
                    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
                    $curl_data = curl_exec($curl_handle);
                    curl_close($curl_handle);
                    $router_ip_addresses = json_decode($curl_data, true);
                    // save the router ip address
                    $ip_addr = $router_ip_addresses;
                    
                    // get the SIMPLE QUEUES
                    $curl_handle = curl_init();
                    $url = "https://crontab.hypbits.com/getIpaddress.php?db_name=".session("database_name")."&r_id=".$router_name."&r_queues=true";
                    curl_setopt($curl_handle, CURLOPT_URL, $url);
                    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
                    $curl_data = curl_exec($curl_handle);
                    curl_close($curl_handle);
                    $router_simple_queues = json_decode($curl_data, true);
                    $simple_queues = $router_simple_queues;

                    // set the target key for simple queues because this changes in different routers.
                    $target_key = 'target';
                    $first_simple_queues = count($simple_queues) > 0 ? $simple_queues[0] : null;
                    if ($first_simple_queues != null) {
                        foreach($first_simple_queues as $key => $simple_queue) {
                            if ($key == 'address') {
                                $target_key = $key;
                            }
                        }
                    }

                    // proceed and add the client to the router
                    if ($req->input('allow_router_changes') == "on"){
                        // check if the ip address is present
                        $present_ip = 0;
                        $ip_id = 0;
                        
                        // loop through the ip address
                        foreach ($router_ip_addresses as $key => $value_address) {
                            if ($value_address['network'] == $req->input('client_network')) {
                                $present_ip = 1;
                                $ip_id = $value_address['.id'];
                                break;
                            }
                        }
                        // return $present_ip;
                        
                        if ($present_ip == 1) {
                            // update the ip address
=======
                // get the ip address and queue list above
                // get ip
                // Initiate curl session in a variable (resource)
                $curl_handle = curl_init();
                $baseUrl = explode(":",url('/'));
                $local_url = $baseUrl[0].":".$baseUrl[1];

                $url = "$local_url:81/crontab/getIpaddress.php?r_ip=true&r_id=".$router_name;

                // Set the curl URL option
                curl_setopt($curl_handle, CURLOPT_URL, $url);

                // This option will return data as a string instead of direct output
                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);

                // Execute curl & store data in a variable
                $curl_data = curl_exec($curl_handle);

                curl_close($curl_handle);

                // Decode JSON into PHP array
                $router_ip_addresses = json_decode($curl_data);

                // get the queue
                // Initiate curl session in a variable (resource)
                $curl_handle = curl_init();
                $baseUrl = explode(":",url('/'));
                $local_url = $baseUrl[0].":".$baseUrl[1];

                $url = "$local_url:81/crontab/getIpaddress.php?r_queues=true&r_id=".$router_name;

                // Set the curl URL option
                curl_setopt($curl_handle, CURLOPT_URL, $url);

                // This option will return data as a string instead of direct output
                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);

                // Execute curl & store data in a variable
                $curl_data = curl_exec($curl_handle);

                curl_close($curl_handle);

                // Decode JSON into PHP array
                $router_simple_queues = json_decode($curl_data);
                
                if ($req->input('allow_router_changes') == "on") {
                    // get the router information
                    // get the router data
                    $router_data = DB::select("SELECT * FROM `router_tables` WHERE `deleted` = '0' AND `router_id` = '$router_name' LIMIT 1");
                    $ip_address = $router_data[0]->router_ipaddr;
                    $router_api_username = $router_data[0]->router_api_username;
                    $router_api_password = $router_data[0]->router_api_password;
                    $router_api_port = $router_data[0]->router_api_port;
            
            
                    // connect to the router and add the ip address and queues to the interface
                    $API = new routeros_api();
                    $API->debug = false;
                    // check if the connection is valid
                    if ($API->connect($ip_address, $router_api_username, $router_api_password, $router_api_port)) {
                        // echo $client_gw;
                        // get the ip address list to see if the ip address is already present
                        // check if the ip is present if its not present add it if not change the data
                        $ip_address = $router_ip_addresses;
                        // return $ip_address;
                        $present = 0;
                        $myids = "";
                        foreach ($ip_address as $key => $value) {
                            foreach ($value as $key1 => $value1) {
                                if ($key1 == ".id") {
                                    $myids = $value1;
                                }
                                if ($value1 == $req->input('client_network')) {
                                    $present = 1;
                                    break;
                                }
                            }
                            if ($present == 1) {
                                break;
                            }
                        }
                        // return $client_address;
                        // if the ip address is present change its details
                        if ($present == 1) {
>>>>>>> origin/main
                            // set the ip address using its id
                            $result = $API->comm("/ip/address/set",
                            array(
                                "address"     => $req->input('client_gw'),
                                "interface" => $req->input('interface_name'),
                                "comment"  => $req->input('client_name')." (".$req->input('client_address')." - ".$location_coordinates.") - ".$client_acc_number,
<<<<<<< HEAD
                                ".id" => $ip_id
=======
                                ".id" => $myids
>>>>>>> origin/main
                            ));
                            if(count($result) > 0){
                                // this means there is an error
                                $API->comm("/ip/address/set",
                                array(
                                    "interface" => $req->input('interface_name'),
                                    "comment"  => $req->input('client_name')." (".$req->input('client_address')." - ".$location_coordinates.") - ".$client_acc_number,
<<<<<<< HEAD
                                    ".id" => $ip_id
                                ));
                            }
                        }else{
                            // add the ip address
                            // add a new ip address
                            $result = $API->comm("/ip/address/add", 
=======
                                    ".id" => $myids
                                ));
                            }
                        }else {
                            // add a new ip address
                            $API->comm("/ip/address/add", 
>>>>>>> origin/main
                            array(
                                "address"     => $req->input('client_gw'),
                                "interface" => $req->input('interface_name'),
                                "network" => $req->input('client_network'),
                                "comment"  => $req->input('client_name')." (".$req->input('client_address')." - ".$location_coordinates.") - ".$client_acc_number
                            ));
                        }

<<<<<<< HEAD
                        // proceed and add the queues 
                        // first check the queues
                        $queue_present = 0;
                        $queue_id = 0;
                        foreach ($router_simple_queues as $key => $value_simple_queues) {
                            if ($value_simple_queues['target'] == $client_network."/".explode("/",$client_gw)[1]) {
                                $queue_id = $value_simple_queues['.id'];
                                $queue_present = 1;
                                break;
=======
                        // check if there is a queue if its not present add if its present set it
                        $queueList = $router_simple_queues;
                        // return $queueList;
                        $present = 0;
                        $queue_id = "";
                        if (($queueList)) {
                            foreach ($queueList as $key => $value) {
                                foreach ($value as $key1 => $value1) {
                                    if($key1 == ".id"){
                                        $queue_id = $value1;
                                    }
                                    if($value1 == $client_network."/".explode("/",$client_gw)[1]){
                                        $present = 1;
                                        break;
                                    }
                                }
                                if ($present == 1) {
                                    break;
                                }
>>>>>>> origin/main
                            }
                        }

                        $upload = $upload_speed.$unit1;
                        $download = $download_speed.$unit2;
<<<<<<< HEAD
                        // queue present
                        if ($queue_present == 1) {
                            // set the queue
=======

                        if ($present == 1) {
>>>>>>> origin/main
                            // set the queue using the ip address
                            $API->comm("/queue/simple/set",
                                array(
                                    "name" => $req->input('client_name')." (".$req->input('client_address')." - ".$location_coordinates.") - ".$client_acc_number,
<<<<<<< HEAD
                                    "$target_key" => $client_network."/".explode("/",$client_gw)[1],
=======
                                    "target" => $client_network."/".explode("/",$client_gw)[1],
>>>>>>> origin/main
                                    "max-limit" => $upload."/".$download,
                                    ".id" => $queue_id
                                )
                            );
<<<<<<< HEAD
                        }else{
=======
                        }else {
>>>>>>> origin/main
                            // add the queue to the list
                            $API->comm("/queue/simple/add",
                                array(
                                    "name" => $req->input('client_name')." (".$req->input('client_address')." - ".$location_coordinates.") - ".$client_acc_number,
<<<<<<< HEAD
                                    "$target_key" => $client_network."/".explode("/",$client_gw)[1],
=======
                                    "target" => $client_network."/".explode("/",$client_gw)[1],
>>>>>>> origin/main
                                    "max-limit" => $upload."/".$download
                                )
                            );
                        }
<<<<<<< HEAD
                    }
                    // disconnect the api
                    $API->disconnect();

                    // save to the databases
            
=======
                        // return $present;
            
                        // add the clients information in the database
                        $clients_table = new client_table();
                        $clients_table->client_name = $client_name;
                        $clients_table->client_address = $client_address;
                        $clients_table->client_network = $client_network;
                        $clients_table->client_default_gw = $client_gw;
                        $clients_table->next_expiration_date = $expiration_dates;
                        $clients_table->max_upload_download = $upload_speed.$unit1."/".$download_speed.$unit2;
                        $clients_table->monthly_payment = $client_monthly_pay;
                        $clients_table->router_name = $router_name;
                        $clients_table->comment = $req->input('comments');
                        $clients_table->clients_contacts = $client_phone;
                        $clients_table->client_status = "1";
                        $clients_table->payments_status = "1";
                        $clients_table->clients_reg_date = date("YmdHis");
                        $clients_table->client_interface = $interface_name;
                        $clients_table->client_username = $client_username;
                        $clients_table->client_password = $client_password;
                        $clients_table->client_account = $client_acc_number;
                        $clients_table->location_coordinates = $req->input('location_coordinates');
                        $clients_table->assignment = "static";
                        $clients_table->min_amount = $minimum_payment;
                        // return $clients_table;
                        $clients_table->save();
                        $API->disconnect();
                        // get the sms keys
                        $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'sms_api_key'");
                        $sms_api_key = $sms_keys[0]->value;
                        $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'sms_partner_id'");
                        $sms_partner_id = $sms_keys[0]->value;
                        $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'sms_shortcode'");
                        $sms_shortcode = $sms_keys[0]->value;
                        $partnerID = $sms_partner_id;
                        $apikey = $sms_api_key;
                        $shortcode = $sms_shortcode;

                        $message_contents = $this->get_sms();
                        $message = $message_contents[3]->messages[0]->message;
                        $user_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' ORDER BY `client_id` DESC LIMIT 1;");
                        if ($user_data && $req->input('send_sms') == "on") {
                            $client_id = $user_data[0]->client_id;
                            $mobile = $user_data[0]->clients_contacts;
                            $sms_type = 2;
                            if ($message) {
                                $trans_amount = 0;
                                $message = $this->message_content($message,$client_id,$trans_amount);
                                $finalURL = "https://mysms.celcomafrica.com/api/services/sendsms/?apikey=" . urlencode($apikey) . "&partnerID=" . urlencode($partnerID) . "&message=" . urlencode($message) . "&shortcode=$shortcode&mobile=$mobile";
                                $ch = \curl_init();
                                \curl_setopt($ch, CURLOPT_URL, $finalURL);
                                \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                \curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                                $response = \curl_exec($ch);
                                \curl_close($ch);
                                $res = json_decode($response);
                                // return $res;
                                $values = $res->responses[0];
                                // return $values;
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
                                $sms_table->account_id = $client_id;
                                $sms_table->sms_type = $sms_type;
                                $sms_table->save();
                            }
                        }


                        // log file capture error
                        // read the data 
                        $myfile = fopen(public_path("/logs/log.txt"), "r") or die("Unable to open file!");
                        $file_sizes = filesize(public_path("/logs/log.txt")) > 0?filesize(public_path("/logs/log.txt")):8190;
                        $existing_txt = fread($myfile,$file_sizes);
                        // return $existing_txt;
                        $myfile = fopen(public_path("/logs/log.txt"), "w") or die("Unable to open file!");
                        $date = date("dS M Y (H:i:sa)");
                        $txt = $date.":New Client (".$client_name.") successfully registered by  ".session('Usernames')." to database and router! Static Assignment!\n".$existing_txt;
                        // return $txt;
                        fwrite($myfile, $txt);
                        fclose($myfile);
                        // end of log file
                        
                        if ($err_pres == 0) {
                            session()->flash("success_reg","The user has been successfully registered!");
                            return redirect("Clients");
                        }
                    }
                }else {
>>>>>>> origin/main
                    // add the clients information in the database
                    $clients_table = new client_table();
                    $clients_table->client_name = $client_name;
                    $clients_table->client_address = $client_address;
                    $clients_table->client_network = $client_network;
                    $clients_table->client_default_gw = $client_gw;
<<<<<<< HEAD
                    $clients_table->next_expiration_date = $expiration_dates;
=======
                    $clients_table->next_expiration_date = date("YmdHis",strtotime("12 hour"));
>>>>>>> origin/main
                    $clients_table->max_upload_download = $upload_speed.$unit1."/".$download_speed.$unit2;
                    $clients_table->monthly_payment = $client_monthly_pay;
                    $clients_table->router_name = $router_name;
                    $clients_table->comment = $req->input('comments');
                    $clients_table->clients_contacts = $client_phone;
                    $clients_table->client_status = "1";
                    $clients_table->payments_status = "1";
                    $clients_table->clients_reg_date = date("YmdHis");
                    $clients_table->client_interface = $interface_name;
                    $clients_table->client_username = $client_username;
                    $clients_table->client_password = $client_password;
                    $clients_table->client_account = $client_acc_number;
                    $clients_table->location_coordinates = $req->input('location_coordinates');
                    $clients_table->assignment = "static";
                    $clients_table->min_amount = $minimum_payment;
<<<<<<< HEAD
                    // return $clients_table;
                    $clients_table->save();
                    // return $clients_table;


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

                    $message_contents = $this->get_sms();
                    $message = $message_contents[3]->messages[0]->message;
                    $user_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' ORDER BY `client_id` DESC LIMIT 1;");
                    if ($user_data && $req->input('send_sms') == "on") {
                        $client_id = $user_data[0]->client_id;
                        $mobile = $user_data[0]->clients_contacts;
                        $sms_type = 2;
                        if ($message) {
                            $trans_amount = 0;
                            $message = $this->message_content($message,$client_id,$trans_amount);
                            $finalURL = "https://isms.celcomafrica.com/api/services/sendsms/?apikey=" . urlencode($apikey) . "&partnerID=" . urlencode($partnerID) . "&message=" . urlencode($message) . "&shortcode=$shortcode&mobile=$mobile";
                            $ch = \curl_init();
                            \curl_setopt($ch, CURLOPT_URL, $finalURL);
                            \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            \curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                            $response = \curl_exec($ch);
                            \curl_close($ch);
                            $res = json_decode($response);
                            // return $res;
                            $values = $res->responses[0];
                            // return $values;
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
                            $sms_table->account_id = $client_id;
                            $sms_table->sms_type = $sms_type;
                            $sms_table->save();
                        }
                    }

                    // log message
                    $txt = ":New Client (".$client_name.") successfully registered by  ".session('Usernames')." to database and router! Static Assignment!";
                    $this->log($txt);
                    // end of log file

                    // return to the main page
                    session()->flash("success_reg","The user has been successfully registered!");
                    return redirect(url()->route("myclients"));
                }else{
                    session()->flash("network_presence","Cannot connect to the router, Check is the credentials are all setup well!");
                    return redirect(url()->previous());
                }
            }
        }
        session()->flash("network_presence","Cannot add user, contact your administrator for further guidance!");
        return redirect(url()->previous());
    }

    function delete_user_use_acc($user_acc,$affect_router){
        // change db
        $change_db = new login();
        $change_db->change_db();

        // get the user information
        $affect_router = $affect_router == "on" ? true : false;
        // return $user_acc;
        $user_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_account` = '$user_acc'");
=======
                    $clients_table->save();
                    // save the user data in the database
                    session()->flash("success_reg","The user data has been successfully registered but no information in your router has been added!");

                    // log file capture error
                    // read the data 
                    $myfile = fopen(public_path("/logs/log.txt"), "r") or die("Unable to open file!");
                    $file_sizes = filesize(public_path("/logs/log.txt")) > 0?filesize(public_path("/logs/log.txt")):8190;
                    $existing_txt = fread($myfile,$file_sizes);
                    // return $existing_txt;
                    $myfile = fopen(public_path("/logs/log.txt"), "w") or die("Unable to open file!");
                    $date = date("dS M Y (H:i:sa)");
                    $txt = $date.":New Client (".$client_name.") successfully registered by  ".session('Usernames')." added to DB only! Static Assignment\n".$existing_txt;
                    // return $txt;
                    fwrite($myfile, $txt);
                    fclose($myfile);
                    // end of log file

                    return redirect("Clients");
                }
            }
        }
    }

    function delete_user_use_acc($user_acc,$affect_router){
        // get the user information
        $affect_router = $affect_router == "on" ? true : false;
        // return $user_acc;
        $user_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_account` = '$user_acc'");
>>>>>>> origin/main
        if (count($user_data) > 0) {
            if ($user_data[0]->assignment == "static") {
                $router_id =  $user_data[0]->router_name;
                $client_name = $user_data[0]->client_name;
                $user_id = $user_data[0]->client_id;

                // only if allowed to change the router
                if ($affect_router) {
                    // return $user_id;
                    $curl_handle = curl_init();
                    // get router data
<<<<<<< HEAD
                    $router_data = DB::connection("mysql2")->select("SELECT * FROM `router_tables` WHERE `deleted` = '0' AND `router_id` = '$router_id' LIMIT 1");
=======
                    $router_data = DB::select("SELECT * FROM `router_tables` WHERE `deleted` = '0' AND `router_id` = '$router_id' LIMIT 1");
>>>>>>> origin/main
                    $ip_address = $router_data[0]->router_ipaddr;
                    $router_api_username = $router_data[0]->router_api_username;
                    $router_api_password = $router_data[0]->router_api_password;
                    $router_api_port = $router_data[0]->router_api_port;
            
                    // get queues and ip addresses
                    $baseUrl = explode(":",url('/'));
                    $local_url = $baseUrl[0].":".$baseUrl[1];
                    $url = "$local_url:81/crontab/getIpaddress.php?r_queues=true&r_id=".$router_id;
            
                    // Set the curl URL option
                    curl_setopt($curl_handle, CURLOPT_URL, $url);
            
                    // This option will return data as a string instead of direct output
                    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
            
                    // Execute curl & store data in a variable
                    $curl_data = curl_exec($curl_handle);
            
                    curl_close($curl_handle);
            
                    // Decode JSON into PHP array
                    $router_simple_queues = json_decode($curl_data);
<<<<<<< HEAD
=======
                    // return $router_simple_queues;
>>>>>>> origin/main
            
                    // route ip addresses
                    $curl_handle = curl_init();
            
                    $baseUrl = explode(":",url('/'));
                    $local_url = $baseUrl[0].":".$baseUrl[1];
                    $url = "$local_url:81/crontab/getIpaddress.php?r_ip=true&r_id=".$router_id;
            
                    // Set the curl URL option
                    curl_setopt($curl_handle, CURLOPT_URL, $url);
            
                    // This option will return data as a string instead of direct output
                    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
            
                    // Execute curl & store data in a variable
                    $curl_data = curl_exec($curl_handle);
            
                    curl_close($curl_handle);
            
                    // Decode JSON into PHP array
                    $router_ip_addresses = json_decode($curl_data);
<<<<<<< HEAD

=======
                    // return $router_simple_queues;
>>>>>>> origin/main
                    // loop through the ip addresses and delete the one with the clients details
                    $clients_network = $user_data[0]->client_network;
                    $client_subnet = explode("/",$user_data[0]->client_default_gw)[1];
                    $client_default_gw = explode("/",$user_data[0]->client_default_gw)[1];
                    // ip address to delete
                    $id = "";
                    foreach ($router_ip_addresses as $key => $value) {
                        if ($value->address == $user_data[0]->client_default_gw && $value->network == $clients_network) {
                            foreach ($value as $key2 => $value2) {
                                if ($key2 == ".id") {
                                    $id = $value2;
                                    break;
                                }
                            }
                            break;
                        }
                    }
                    // return $id;
                    // delete that id
            
                    // end of delete ip address
                    // loop through the simple queues and delete the user queue
                    $id2 = "";
                    foreach ($router_simple_queues as $key => $value) {
                        if ($value->target == $clients_network."/".$client_subnet) {
                            foreach ($value as $key2 => $value2) {
                                if ($key2 == ".id") {
                                    $id2 = $value2;
                                    break;
                                }
                            }
                        }
                    }
                    // return $id2;
            
                    // connect to the router and add the ip address and queues to the interface
                    $API = new routeros_api();
                    $API->debug = false;
                    // check if the connection is valid
                    if ($API->connect($ip_address, $router_api_username, $router_api_password, $router_api_port)){
                        $API->comm("/ip/address/remove",
                        array(
                            ".id" => $id
                        ));
                        $API->comm("/queue/simple/remove",
                            array(
                                ".id" => $id2
                            )
                        );
                    }
                }
<<<<<<< HEAD
                // DB::connection("mysql2")->delete("DELETE FROM `client_tables` WHERE `deleted` = '0' AND `client_id` = ".$user_id."");
                $update = DB::connection("mysql2")->update("UPDATE `client_tables` SET `date_changed` = ?, `deleted` = '1' WHERE `client_id` = ?",[date("YmdHis"),$user_id]);

                // log message
                $txt = ":Client (".$client_name.") has been deleted by ".session('Usernames')."!";
                $this->log($txt);
                // end of log file
=======
                // DB::delete("DELETE FROM `client_tables` WHERE `deleted` = '0' AND `client_id` = ".$user_id."");
                $update = DB::update("UPDATE `client_tables` SET `date_changed` = ?, `deleted` = '1' WHERE `client_id` = ?",[date("YmdHis"),$user_id]);
        
                // log file capture error
                // read the data 
                $myfile = fopen(public_path("/logs/log.txt"), "r") or die("Unable to open file!");
                $file_sizes = filesize(public_path("/logs/log.txt")) > 0?filesize(public_path("/logs/log.txt")):8190;
                $existing_txt = fread($myfile,$file_sizes);
                // return $existing_txt;
                $myfile = fopen(public_path("/logs/log.txt"), "w") or die("Unable to open file!");
                $date = date("dS M Y (H:i:sa)");
                $txt = $date.":Client (".$client_name.") has been deleted by ".session('Usernames')."!\n".$existing_txt;
                // return $txt;
                fwrite($myfile, $txt);
                fclose($myfile);
                // end of log file
                // return redirect("/Clients");
>>>>>>> origin/main
            }elseif ($user_data[0]->assignment == "pppoe"){
                // remove the client secret and all active connections associated to it
                // get secrets
                // Initiate curl session in a variable (resource)
                $curl_handle = curl_init();
                $router_id = $user_data[0]->router_name;
                $client_name = $user_data[0]->client_name;
                $user_id = $user_data[0]->client_id;


                // allow changes to the router when needed
                if ($affect_router){
                    $baseUrl = explode(":",url('/'));
                    $local_url = $baseUrl[0].":".$baseUrl[1];
                    $url = "$local_url:81/crontab/getIpaddress.php?r_ppoe_secrets=true&r_id=".$router_id;
            
                    // Set the curl URL option
                    curl_setopt($curl_handle, CURLOPT_URL, $url);
            
                    // This option will return data as a string instead of direct output
                    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
            
                    // Execute curl & store data in a variable
                    $curl_data = curl_exec($curl_handle);
            
                    curl_close($curl_handle);
            
                    // Decode JSON into PHP array
                    $router_secrets = json_decode($curl_data);
                    // return $router_secrets;
    
                    // get the active connection
                    // Initiate curl session in a variable (resource)
                    $curl_handle = curl_init();
            
                    $baseUrl = explode(":",url('/'));
                    $local_url = $baseUrl[0].":".$baseUrl[1];
                    $url = "$local_url:81/crontab/getIpaddress.php?r_active_secrets=true&r_id=".$router_id;
            
                    // Set the curl URL option
                    curl_setopt($curl_handle, CURLOPT_URL, $url);
            
                    // This option will return data as a string instead of direct output
                    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
            
                    // Execute curl & store data in a variable
                    $curl_data = curl_exec($curl_handle);
            
                    curl_close($curl_handle);
            
                    // Decode JSON into PHP array
                    $active_connections = json_decode($curl_data);
                    // return $active_connections;
                    // get router data
<<<<<<< HEAD
                    $router_data = DB::connection("mysql2")->select("SELECT * FROM `router_tables` WHERE `deleted` = '0' AND `router_id` = '$router_id' LIMIT 1");
=======
                    $router_data = DB::select("SELECT * FROM `router_tables` WHERE `deleted` = '0' AND `router_id` = '$router_id' LIMIT 1");
>>>>>>> origin/main
                    $ip_address = $router_data[0]->router_ipaddr;
                    $router_api_username = $router_data[0]->router_api_username;
                    $router_api_password = $router_data[0]->router_api_password;
                    $router_api_port = $router_data[0]->router_api_port;
                    // connect to the router and add the ip address and queues to the interface
                    $API = new routeros_api();
                    $API->debug = false;
    
                    $secret_name = $user_data[0]->client_secret;
                    // go through secrets and delete the username thats active
                    if ($API->connect($ip_address, $router_api_username, $router_api_password, $router_api_port)){
                        for ($index1=0; $index1 < count($router_secrets); $index1++) {
                            $secret = $router_secrets[$index1];
                            if ($secret->name == $secret_name) {
                                foreach ($secret as $key => $value) {
                                    if ($key == ".id") {
                                        $API->comm("/ppp/secret/remove",array(
                                            ".id" => $value
                                        ));
                                        break;
                                    }
                                }
                            }
                        }
                        for ($index2=0; $index2 < count($active_connections); $index2++) { 
                            $active = $active_connections[$index2];
                            if ($active->name == $secret_name) {
                                foreach ($active as $key => $value) {
                                    if ($key == ".id") {
                                        $API->comm("/ppp/active/remove",array(
                                            ".id" => $value
                                        ));
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
<<<<<<< HEAD
                // DB::connection("mysql2")->delete("DELETE FROM `client_tables` WHERE `deleted` = '0' AND `client_id` = ".$user_id."");
                DB::connection("mysql2")->update("UPDATE `client_tables` SET `date_changed` = ? , `deleted` = '1' WHERE `client_id` = ?",[date("YmdHis"),$user_id]);

                // log message
                $txt = ":Client (".$client_name.") has been deleted by ".session('Usernames')."!";
                $this->log($txt);
=======
                // DB::delete("DELETE FROM `client_tables` WHERE `deleted` = '0' AND `client_id` = ".$user_id."");
                DB::update("UPDATE `client_tables` SET `date_changed` = ? , `deleted` = '1' WHERE `client_id` = ?",[date("YmdHis"),$user_id]);
            
                // log file capture error
                // read the data 
                $myfile = fopen(public_path("/logs/log.txt"), "r") or die("Unable to open file!");
                $file_sizes = filesize(public_path("/logs/log.txt")) > 0?filesize(public_path("/logs/log.txt")):8190;
                $existing_txt = fread($myfile,$file_sizes);
                // return $existing_txt;
                $myfile = fopen(public_path("/logs/log.txt"), "w") or die("Unable to open file!");
                $date = date("dS M Y (H:i:sa)");
                $txt = $date.":Client (".$client_name.") has been deleted by ".session('Usernames')."!\n".$existing_txt;
                // return $txt;
                fwrite($myfile, $txt);
                fclose($myfile);
>>>>>>> origin/main
                // end of log file
            }
        }else{
            // session()->flash("error_clients","User not found!");
            // return redirect("/Clients");
        }
    }

    function sendSmsClients(Request $req){
<<<<<<< HEAD
        // change db
        $change_db = new login();
        $change_db->change_db();

=======
>>>>>>> origin/main
        // return $req;
        $hold_user_id_data = $req->input("hold_user_id_data");
        if ($this->isJson_report($hold_user_id_data)) {
            $hold_user_id_data = json_decode($hold_user_id_data);
            // return $hold_user_id_data;

            // get all clients and get their phone numbers
<<<<<<< HEAD
            $user_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted`= '0';");
=======
            $user_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted`= '0';");
>>>>>>> origin/main

            $phone_numbers = "";
            for ($index=0; $index < count($hold_user_id_data); $index++) { 
                for ($ind=0; $ind < count($user_data); $ind++) { 
                    if ($hold_user_id_data[$index] == $user_data[$ind]->client_account) {
                        $phone_numbers.=$user_data[$ind]->clients_contacts.",";
                    }
                }
            }
            $phone_number = substr($phone_numbers,0,(strlen($phone_numbers)-1));
            // return $phone_number;

            // get the sms data it contains the client data
            $messages = "";
            // get the data to display for the client list
<<<<<<< HEAD
            $user_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted`= '0';");
=======
            $user_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted`= '0';");
>>>>>>> origin/main
            $client_names = [];
            foreach ($user_data as $key => $value) {
                array_push($client_names,$value->client_name);
            }
            $client_contacts = [];
            foreach ($user_data as $key => $value) {
                array_push($client_contacts,$value->clients_contacts);
            }
            $client_account = [];
            foreach ($user_data as $key => $value) {
                array_push($client_account,$value->client_account);
            }        
            return view("compose",["client_names" => $client_names,"client_contacts" => $client_contacts,"client_account" => $client_account,"messages" => $messages,"phone_number" => $phone_number]);
        }else {
            session()->flash("error_clients","An error occured!");
            return redirect("/Clients");
        }
    }

    function delete_user($user_id){
<<<<<<< HEAD
        // change db
        $change_db = new login();
        $change_db->change_db();

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
                    $sstp_value = $this->getSSTPAddress();
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
                DB::connection("mysql2")->update("UPDATE `client_tables` SET `date_changed` = ? , `deleted` = '1' WHERE `client_id` = ?",[date("YmdHis"),$user_id]);
                DB::connection("mysql2")->delete("DELETE FROM `client_tables` WHERE `deleted` = '0' AND `client_id` = ".$user_id."");
                session()->flash("success",".".$client_name." has been deleted successfully!");

                // log message
                $txt = ":Client (".$client_name.") has been deleted by ".session('Usernames')."!";
                $this->log($txt);
                // end of log file
                return redirect("/Clients");
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
                    $sstp_value = $this->getSSTPAddress();
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
                DB::connection("mysql2")->update("UPDATE `client_tables` SET `date_changed` = ? , `deleted` = '1' WHERE `client_id` = ?",[date("YmdHis"),$user_id]);
                session()->flash("success",".".$client_name." has been deleted successfully!");

                // log message
                $txt = ":Client (".$client_name.") has been deleted by ".session('Usernames')."!";
                $this->log($txt);
                return redirect("/Clients");
=======
        // get the user information
        $user_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_id` = '$user_id'");
        if (count($user_data) > 0) {
            if ($user_data[0]->assignment == "static") {
                $router_id =  $user_data[0]->router_name;
                $client_name = $user_data[0]->client_name;
                $curl_handle = curl_init();
                // get router data
                $router_data = DB::select("SELECT * FROM `router_tables` WHERE `deleted` = '0' AND `router_id` = '$router_id' LIMIT 1");
                $ip_address = $router_data[0]->router_ipaddr;
                $router_api_username = $router_data[0]->router_api_username;
                $router_api_password = $router_data[0]->router_api_password;
                $router_api_port = $router_data[0]->router_api_port;
        
                // get queues and ip addresses
                $baseUrl = explode(":",url('/'));
                $local_url = $baseUrl[0].":".$baseUrl[1];
                $url = "$local_url:81/crontab/getIpaddress.php?r_queues=true&r_id=".$router_id;
        
                // Set the curl URL option
                curl_setopt($curl_handle, CURLOPT_URL, $url);
        
                // This option will return data as a string instead of direct output
                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
        
                // Execute curl & store data in a variable
                $curl_data = curl_exec($curl_handle);
        
                curl_close($curl_handle);
        
                // Decode JSON into PHP array
                $router_simple_queues = json_decode($curl_data);
                // return $router_simple_queues;
        
                // route ip addresses
                $curl_handle = curl_init();
        
                $baseUrl = explode(":",url('/'));
                $local_url = $baseUrl[0].":".$baseUrl[1];
                $url = "$local_url:81/crontab/getIpaddress.php?r_ip=true&r_id=".$router_id;
        
                // Set the curl URL option
                curl_setopt($curl_handle, CURLOPT_URL, $url);
        
                // This option will return data as a string instead of direct output
                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
        
                // Execute curl & store data in a variable
                $curl_data = curl_exec($curl_handle);
        
                curl_close($curl_handle);
        
                // Decode JSON into PHP array
                $router_ip_addresses = json_decode($curl_data);
                // return $router_simple_queues;
                // loop through the ip addresses and delete the one with the clients details
                $clients_network = $user_data[0]->client_network;
                $client_subnet = explode("/",$user_data[0]->client_default_gw)[1];
                $client_default_gw = explode("/",$user_data[0]->client_default_gw)[1];
                // ip address to delete
                $id = "";
                foreach ($router_ip_addresses as $key => $value) {
                    if ($value->address == $user_data[0]->client_default_gw && $value->network == $clients_network) {
                        foreach ($value as $key2 => $value2) {
                            if ($key2 == ".id") {
                                $id = $value2;
                                break;
                            }
                        }
                        break;
                    }
                }
                // return $id;
                // delete that id
        
                // end of delete ip address
                // loop through the simple queues and delete the user queue
                $id2 = "";
                foreach ($router_simple_queues as $key => $value) {
                    if ($value->target == $clients_network."/".$client_subnet) {
                        foreach ($value as $key2 => $value2) {
                            if ($key2 == ".id") {
                                $id2 = $value2;
                                break;
                            }
                        }
                    }
                }
                // return $id2;
        
                // connect to the router and add the ip address and queues to the interface
                $API = new routeros_api();
                $API->debug = false;
                // check if the connection is valid
                if ($API->connect($ip_address, $router_api_username, $router_api_password, $router_api_port)){
                    $API->comm("/ip/address/remove",
                    array(
                        ".id" => $id
                    ));
                    $API->comm("/queue/simple/remove",
                        array(
                            ".id" => $id2
                        )
                    );
                }
                // DB::delete("DELETE FROM `client_tables` WHERE `deleted` = '0' AND `client_id` = ".$user_id."");
                DB::update("UPDATE `client_tables` SET `date_changed` = ? , `deleted` = '1' WHERE `client_id` = ?",[date("YmdHis"),$user_id]);
                session()->flash("success",".".$client_name." has been deleted successfully!");
        
                // log file capture error
                // read the data 
                $myfile = fopen(public_path("/logs/log.txt"), "r") or die("Unable to open file!");
                $file_sizes = filesize(public_path("/logs/log.txt")) > 0?filesize(public_path("/logs/log.txt")):8190;
                $existing_txt = fread($myfile,$file_sizes);
                // return $existing_txt;
                $myfile = fopen(public_path("/logs/log.txt"), "w") or die("Unable to open file!");
                $date = date("dS M Y (H:i:sa)");
                $txt = $date.":Client (".$client_name.") has been deleted by ".session('Usernames')."!\n".$existing_txt;
                // return $txt;
                fwrite($myfile, $txt);
                fclose($myfile);
                // end of log file
                return redirect("/Clients");
            }elseif ($user_data[0]->assignment == "pppoe"){
                // remove the client secret and all active connections associated to it
                // get secrets
                // Initiate curl session in a variable (resource)
                $curl_handle = curl_init();
                $router_id = $user_data[0]->router_name;
                $client_name = $user_data[0]->client_name;


                $baseUrl = explode(":",url('/'));
                $local_url = $baseUrl[0].":".$baseUrl[1];
                $url = "$local_url:81/crontab/getIpaddress.php?r_ppoe_secrets=true&r_id=".$router_id;
        
                // Set the curl URL option
                curl_setopt($curl_handle, CURLOPT_URL, $url);
        
                // This option will return data as a string instead of direct output
                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
        
                // Execute curl & store data in a variable
                $curl_data = curl_exec($curl_handle);
        
                curl_close($curl_handle);
        
                // Decode JSON into PHP array
                $router_secrets = json_decode($curl_data);
                // return $router_secrets;

                // get the active connection
                // Initiate curl session in a variable (resource)
                $curl_handle = curl_init();
        
                $baseUrl = explode(":",url('/'));
                $local_url = $baseUrl[0].":".$baseUrl[1];
                $url = "$local_url:81/crontab/getIpaddress.php?r_active_secrets=true&r_id=".$router_id;
        
                // Set the curl URL option
                curl_setopt($curl_handle, CURLOPT_URL, $url);
        
                // This option will return data as a string instead of direct output
                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
        
                // Execute curl & store data in a variable
                $curl_data = curl_exec($curl_handle);
        
                curl_close($curl_handle);
        
                // Decode JSON into PHP array
                $active_connections = json_decode($curl_data);
                // return $active_connections;
                // get router data
                $router_data = DB::select("SELECT * FROM `router_tables` WHERE `deleted` = '0' AND `router_id` = '$router_id' LIMIT 1");
                $ip_address = $router_data[0]->router_ipaddr;
                $router_api_username = $router_data[0]->router_api_username;
                $router_api_password = $router_data[0]->router_api_password;
                $router_api_port = $router_data[0]->router_api_port;
                // connect to the router and add the ip address and queues to the interface
                $API = new routeros_api();
                $API->debug = false;

                $secret_name = $user_data[0]->client_secret;
                // go through secrets and delete the username thats active
                if ($API->connect($ip_address, $router_api_username, $router_api_password, $router_api_port)){
                    for ($index1=0; $index1 < count($router_secrets); $index1++) { 
                        $secret = $router_secrets[$index1];
                        if ($secret->name == $secret_name) {
                            foreach ($secret as $key => $value) {
                                if ($key == ".id") {
                                    $API->comm("/ppp/secret/remove",array(
                                        ".id" => $value
                                    ));
                                    break;
                                }
                            }
                        }
                    }
                    for ($index2=0; $index2 < count($active_connections); $index2++) { 
                        $active = $active_connections[$index2];
                        if ($active->name == $secret_name) {
                            foreach ($active as $key => $value) {
                                if ($key == ".id") {
                                    $API->comm("/ppp/active/remove",array(
                                        ".id" => $value
                                    ));
                                    break;
                                }
                            }
                        }
                    }
                    // DB::delete("DELETE FROM `client_tables` WHERE `deleted` = '0' AND `client_id` = ".$user_id."");
                    DB::update("UPDATE `client_tables` SET `date_changed` = ? , `deleted` = '1' WHERE `client_id` = ?",[date("YmdHis"),$user_id]);
                    session()->flash("success",".".$client_name." has been deleted successfully!");
            
                    // log file capture error
                    // read the data 
                    $myfile = fopen(public_path("/logs/log.txt"), "r") or die("Unable to open file!");
                    $file_sizes = filesize(public_path("/logs/log.txt")) > 0?filesize(public_path("/logs/log.txt")):8190;
                    $existing_txt = fread($myfile,$file_sizes);
                    // return $existing_txt;
                    $myfile = fopen(public_path("/logs/log.txt"), "w") or die("Unable to open file!");
                    $date = date("dS M Y (H:i:sa)");
                    $txt = $date.":Client (".$client_name.") has been deleted by ".session('Usernames')."!\n".$existing_txt;
                    // return $txt;
                    fwrite($myfile, $txt);
                    fclose($myfile);
                    // end of log file
                    return redirect("/Clients");
                }
>>>>>>> origin/main
            }
        }else{
            session()->flash("error_clients","User not found!");
            return redirect("/Clients");
        }
    }

<<<<<<< HEAD
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

    function getRouterInterfaces($routerid){
        // change db
        $change_db = new login();
        $change_db->change_db();

        // get the router data
        $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `router_id` = ? AND `deleted` = '0'",[$routerid]);
        if (count($router_data) == 0) {
            echo "Router does not exist!";
            return "";
        }
        
        // get the IP ADDRES
        $curl_handle = curl_init();
        $url = "https://crontab.hypbits.com/getIpaddress.php?db_name=".session("database_name")."&r_id=".$routerid."&r_interfaces=true";
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

    function getRouterProfile($routerid){
        // change db
        $change_db = new login();
        $change_db->change_db();

        // get the router data
        $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `router_id` = ? AND `deleted` = '0'",[$routerid]);
        if (count($router_data) == 0) {
            echo "Router does not exist!";
            return "";
        }

        // get the sstp credentails they are also the api usernames
        $sstp_username = $router_data[0]->sstp_username;
        $sstp_password = $router_data[0]->sstp_password;
        $api_port = $router_data[0]->api_port;

        

        // connect to the router and set the sstp client
        $sstp_value = $this->getSSTPAddress();
        if ($sstp_value == null) {
            $error = "The SSTP server is not set, Contact your administrator!";
            session()->flash("network_presence",$error);
            return redirect(url()->previous());
        }

        // connect to the router and set the sstp client
        $ip_address = $sstp_value->ip_address;
        $user = $sstp_value->username;
        $pass = $sstp_value->password;
        $port = $sstp_value->port;

        // check if the router is actively connected
        $client_router_ip = $this->checkActive($ip_address,$user,$pass,$port,$sstp_username);
        
        if ($client_router_ip == false) {
            echo "Your router is currently in-active, kindly ensure its online then proceed to request for the PPPOE Profiles!";
            return "";
        }

        // proceed to connect and get the routers interfaces using the ip address youve gotten
        $API = new routeros_api();
        $API->debug = false;

        // connect to the router
        if ($API->connect($client_router_ip, $sstp_username, $sstp_password, $api_port)){
            // get the router profiles
            $pppoe_profiles = $API->comm("/ppp/profile/print");
            // return $interfaces;
            if (isset($interfaces['!trap'])) {
                $API->disconnect();
                echo "An error occured! Kindly contact your administrator!";
                return 0;
            }
            
            // create the select selector
            $data_to_display = "<select name='pppoe_profile' class='form-control' id='pppoe_profile' required ><option value='' hidden>Select a Profile</option>";
            for ($index=0; $index < count($pppoe_profiles); $index++) {
                $data_to_display.="<option value='".$pppoe_profiles[$index]['name']."'>".$pppoe_profiles[$index]['name']."</option>";
            }
            $data_to_display.="</select>";

            echo $data_to_display;
            $API->disconnect();
            return "";
        }

        echo "No data to display : \"An error occured!\"";
        return "";
=======
    function getRouterInterfaces($routerid){
        // get the router data
        $router_data = DB::select("SELECT * FROM `router_tables` WHERE `deleted` = '0' AND `router_id` = '$routerid' LIMIT 1");
        $ip_address = $router_data[0]->router_ipaddr;
        $router_api_username = $router_data[0]->router_api_username;
        $router_api_password = $router_data[0]->router_api_password;
        $router_api_port = $router_data[0]->router_api_port;
        // connect to the router and get all its interfaces

        $API = new routeros_api();
        $API->debug = false;
        $baseUrl = explode(":",url('/'));
        $local_url = $baseUrl[0].":".$baseUrl[1];
        // return $local_url;

        // check if the connection is valid
        if ($API->connect($ip_address, $router_api_username, $router_api_password, $router_api_port)) {
            // get the data

            // Initiate curl session in a variable (resource)
            $curl_handle = curl_init();

            $url = $local_url.":81/crontab/getIpaddress.php?r_interfaces=true&r_id=".$routerid;

            // Set the curl URL option
            curl_setopt($curl_handle, CURLOPT_URL, $url);

            // This option will return data as a string instead of direct output
            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);

            // Execute curl & store data in a variable
            $curl_data = curl_exec($curl_handle);

            curl_close($curl_handle);

            // Decode JSON into PHP array
            $router_interfaces = json_decode($curl_data);

            // get the queue
            // Initiate curl session in a variable (resource)
            $curl_handle = curl_init();
            // print interfaces
            $interfaces = $router_interfaces;
            // return $curl_data;
            $data_to_display = "<select name='interface_name' class='form-control' id='interface_name' required ><option value='' hidden>Select an Interface</option>";
            foreach ($interfaces as $key => $value) {
                if ($value->type == "ether" || $value->type == "wlan" || $value->type == "bridge") {
                    $data_to_display.="<option value='".$value->name."'>".$value->name."</option>";
                }
            }
            $data_to_display.="</select>";
            echo $data_to_display;
            $API->disconnect();
        }else {
            echo "No data to display";
        }
    }

    function getRouterProfile($routerid){
        // get the router data
        $router_data = DB::select("SELECT * FROM `router_tables` WHERE `deleted` = '0' AND `router_id` = '$routerid' LIMIT 1");
        $ip_address = $router_data[0]->router_ipaddr;
        $router_api_username = $router_data[0]->router_api_username;
        $router_api_password = $router_data[0]->router_api_password;
        $router_api_port = $router_data[0]->router_api_port;
        // connect to the router and get all its interfaces

        $API = new routeros_api();
        $API->debug = false;

        // check if the connection is valid
        if ($API->connect($ip_address, $router_api_username, $router_api_password, $router_api_port)) {
            // get the data

            // Initiate curl session in a variable (resource)
            $curl_handle = curl_init();

            $baseUrl = explode(":",url('/'));
            $local_url = $baseUrl[0].":".$baseUrl[1];
            $url = "$local_url:81/crontab/getIpaddress.php?r_ppoe_profiles=true&r_id=".$routerid;

            // Set the curl URL option
            curl_setopt($curl_handle, CURLOPT_URL, $url);

            // This option will return data as a string instead of direct output
            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);

            // Execute curl & store data in a variable
            $curl_data = curl_exec($curl_handle);

            curl_close($curl_handle);

            // Decode JSON into PHP array
            $router_interfaces = json_decode($curl_data);

            // get the queue
            // Initiate curl session in a variable (resource)
            $curl_handle = curl_init();
            // print interfaces
            $profiles = $router_interfaces;
            // return $profiles;
            $data_to_display = "<select name='pppoe_profile' class='form-control' id='pppoe_profile' required ><option value='' hidden>Select a Profile</option>";
            foreach ($profiles as $key => $value) {
                    $data_to_display.="<option value='".$value->name."'>".$value->name."</option>";
            }
            $data_to_display.="</select>";
            echo $data_to_display;
            $API->disconnect();
        }else {
            echo "No data to display";
        }
>>>>>>> origin/main
    }


    // update minimum payment
    function updateMinPay(Request $request){
<<<<<<< HEAD
        // change db
        $change_db = new login();
        $change_db->change_db();

=======
>>>>>>> origin/main
        // return $request;
        $client_id = $request->input("client_id");
        $change_minimum_payment = $request->input("change_minimum_payment");

        // update the clients minimum pay
<<<<<<< HEAD
        $update = DB::connection("mysql2")->update("UPDATE `client_tables` SET `min_amount` = ? WHERE `client_id` = ?",[$change_minimum_payment,$client_id]);
=======
        $update = DB::update("UPDATE `client_tables` SET `min_amount` = ? WHERE `client_id` = ?",[$change_minimum_payment,$client_id]);
>>>>>>> origin/main

        // set a success
        session()->flash("success","Update has been done successfully!");
        return redirect(route("client.viewinformation",['clientid' => $client_id]));
    }


    // get the client information
    function getClientInformation($clientid){
<<<<<<< HEAD
        // change db
        $change_db = new login();
        $change_db->change_db();

        // get the clients information from the database
        $clients_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_id` = '$clientid'");
=======
        // get the clients information from the database
        $clients_data = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_id` = '$clientid'");
>>>>>>> origin/main
        if (count($clients_data) > 0) {
            // here we get the router data
            // check if the client is static or pppoe
            $assignment = $clients_data[0]->assignment;
            if ($assignment == "static") {
<<<<<<< HEAD
                $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `deleted` = '0'");
=======
                $router_data = DB::select("SELECT * FROM `router_tables` WHERE `deleted` = '0'");
>>>>>>> origin/main
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
<<<<<<< HEAD
                $clients_infor = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0'");
=======
                $clients_infor = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0'");
>>>>>>> origin/main
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
<<<<<<< HEAD
                    $client_name = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `client_account` = '".$client_data->client_acc."' AND `deleted` = '0'");
=======
                    $client_name = DB::select("SELECT * FROM `client_tables` WHERE `client_account` = '".$client_data->client_acc."' AND `deleted` = '0'");
>>>>>>> origin/main
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
<<<<<<< HEAD
                $client_reffer = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0'");
=======
                $client_reffer = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0'");
>>>>>>> origin/main
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
                return view("clientInfor",['clients_data'=>$clients_data,'router_data'=>$router_data,"expire_date" => $expire_date,"registration_date" => $reg_date, "freeze_date" => $freeze_date,"clients_names"=>$clients_name,"clients_account"=>$clients_acc_no,"clients_contacts"=>$clients_phone,"client_refferal" => $client_refferal,"reffer_details" => $reffer_details,"refferal_payment" => $payment_histoty,"reffered_list" => $reffered_list]);
            }elseif ($assignment == "pppoe") {
<<<<<<< HEAD
                $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `deleted` = '0'");
=======
                $router_data = DB::select("SELECT * FROM `router_tables` WHERE `deleted` = '0'");
>>>>>>> origin/main
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
<<<<<<< HEAD
                $clients_infor = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0'");
=======
                $clients_infor = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0'");
>>>>>>> origin/main
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
<<<<<<< HEAD
                    $client_name = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_account` = '".$client_data->client_acc."'");
=======
                    $client_name = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0' AND `client_account` = '".$client_data->client_acc."'");
>>>>>>> origin/main
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
<<<<<<< HEAD
                $client_reffer = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `deleted` = '0'");
=======
                $client_reffer = DB::select("SELECT * FROM `client_tables` WHERE `deleted` = '0'");
>>>>>>> origin/main
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
                return view("clientInforPppoe",['clients_data'=>$clients_data,'router_data'=>$router_data,"expire_date" => $expire_date,"registration_date" => $reg_date, "freeze_date" => $freeze_date,"clients_names"=>$clients_name,"clients_account"=>$clients_acc_no,"clients_contacts"=>$clients_phone,"client_refferal" => $client_refferal,"reffer_details" => $reffer_details,"refferal_payment" => $payment_histoty,"reffered_list" => $reffered_list]);
            }else {
                session()->flash("error_clients","Invalid Assignment!!");
                return redirect("/Clients");
            }
        }else {
            session()->flash("error_clients","Invalid User!!");
            return redirect("/Clients");
        }
    }
    // get refferal
    function getRefferal($client_account){
<<<<<<< HEAD
        // change db
        $change_db = new login();
        $change_db->change_db();

        $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `client_account` = '$client_account' AND `deleted` = '0'");
=======
        $client_data = DB::select("SELECT * FROM `client_tables` WHERE `client_account` = '$client_account' AND `deleted` = '0'");
>>>>>>> origin/main
        if (count($client_data) > 0) {
            return $client_data[0]->client_name.":".$client_data[0]->client_account.":".$client_data[0]->wallet_amount.":".$client_data[0]->client_address;
        }else {
            return "Invalid User!";
        }
    }
    // set refferal information
    function setRefferal(Request $req){
<<<<<<< HEAD
        // change db
        $change_db = new login();
        $change_db->change_db();

=======
>>>>>>> origin/main
        // return $req->input();
        // get the user refferal information if there is any
        $user_id = $req->input('clients_id');
        $refferal_account_no = $req->input('refferal_account_no');
        $refferer_amount = $req->input("refferer_amount");
<<<<<<< HEAD
        $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `client_id` = '".$user_id."' AND `deleted` = '0'");
        $refferer_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `client_account` = '".$refferal_account_no."' AND `deleted` = '0'");
=======
        $client_data = DB::select("SELECT * FROM `client_tables` WHERE `client_id` = '".$user_id."' AND `deleted` = '0'");
        $refferer_data = DB::select("SELECT * FROM `client_tables` WHERE `client_account` = '".$refferal_account_no."' AND `deleted` = '0'");
>>>>>>> origin/main
        if (count($client_data) > 0 && count($refferer_data) > 0) {
            $user_refferal = $client_data[0]->reffered_by;
            // check if there is anyone who reffered them by getting the str len
            if (strlen(trim($user_refferal)) > 0) {
                // if there is a refferal set
                $user_refferal = str_contains($user_refferal,"\\") === true ? trim(str_replace("\\","",$user_refferal)):trim($user_refferal);
                $user_refferal = substr($user_refferal,0,1) == "\"" ? substr($user_refferal,1,(strlen($user_refferal)-2)) : $user_refferal;

                $user_refferal = str_replace("'","\"",$user_refferal);
                $reffered_by = json_decode($user_refferal);
                $reffered_by->client_acc = $refferal_account_no;
                $reffered_by->monthly_payment = $refferer_amount;
                // update the table and set the refferer information
<<<<<<< HEAD
                DB::connection("mysql2")->table('client_tables')
=======
                DB::table('client_tables')
>>>>>>> origin/main
                ->where('client_id', $user_id)
                ->update([
                    'reffered_by' => json_encode($reffered_by),
                    "date_changed" => date("YmdHis")
                ]);
                // return $json_data;
                session()->flash("success","".$client_data[0]->client_name." refferer is set to ".$refferer_data[0]->client_name." and will recieve Kes ".number_format($refferer_amount)."!");
<<<<<<< HEAD

                // log message
                $txt = $client_data[0]->client_name." refferer is updated to ".$refferer_data[0]->client_name." and will recieve Kes ".number_format($refferer_amount)." by ".session('Usernames')."!";
                $this->log($txt);
=======
        
                // log file capture error
                // read the data 
                $myfile = fopen(public_path("/logs/log.txt"), "r") or die("Unable to open file!");
                $file_sizes = filesize(public_path("/logs/log.txt")) > 0?filesize(public_path("/logs/log.txt")):8190;
                $existing_txt = fread($myfile,$file_sizes);
                // return $existing_txt;
                $myfile = fopen(public_path("/logs/log.txt"), "w") or die("Unable to open file!");
                $date = date("dS M Y (H:i:sa)");
                $txt = $date."".$client_data[0]->client_name." refferer is updated to ".$refferer_data[0]->client_name." and will recieve Kes ".number_format($refferer_amount)." by ".session('Usernames')."!\n".$existing_txt;
                // return $txt;
                fwrite($myfile, $txt);
                fclose($myfile);
>>>>>>> origin/main
                // end of log file
                return redirect("Clients/View/".$user_id);
            }else {
                // create a new refferal
                $string = "{\"client_acc\":\"unknown\",\"monthly_payment\":0,\"payment_history\":[]}";
                $json_data = json_decode($string);
                $json_data->client_acc = $refferal_account_no;
                $json_data->monthly_payment = $refferer_amount;
                // update the table and set the refferer information
<<<<<<< HEAD
                DB::connection("mysql2")->table('client_tables')
=======
                DB::table('client_tables')
>>>>>>> origin/main
                ->where('client_id', $user_id)
                ->update([
                    'reffered_by' => json_encode($json_data),
                    'date_changed' => date("YmdHis")
                ]);
                // return $json_data;
                session()->flash("success","".$client_data[0]->client_name." refferer is set  to ".$refferer_data[0]->client_name." and will recieve Kes ".number_format($refferer_amount)."!");
<<<<<<< HEAD

                // log message
                $txt = $client_data[0]->client_name." refferer is set to ".$refferer_data[0]->client_name." and will recieve Kes ".number_format($refferer_amount)." by ".session('Usernames')."!";
                $this->log($txt);
=======
        
                // log file capture error
                // read the data 
                $myfile = fopen(public_path("/logs/log.txt"), "r") or die("Unable to open file!");
                $file_sizes = filesize(public_path("/logs/log.txt")) > 0?filesize(public_path("/logs/log.txt")):8190;
                $existing_txt = fread($myfile,$file_sizes);
                // return $existing_txt;
                $myfile = fopen(public_path("/logs/log.txt"), "w") or die("Unable to open file!");
                $date = date("dS M Y (H:i:sa)");
                $txt = $date."".$client_data[0]->client_name." refferer is set to ".$refferer_data[0]->client_name." and will recieve Kes ".number_format($refferer_amount)." by ".session('Usernames')."!\n".$existing_txt;
                // return $txt;
                fwrite($myfile, $txt);
                fclose($myfile);
>>>>>>> origin/main
                // end of log file
                return redirect("Clients/View/".$user_id);
            }
        }
    }
    // update freeze date
    function set_freeze_date(Request $req){
<<<<<<< HEAD
        // change db
        $change_db = new login();
        $change_db->change_db();

=======
>>>>>>> origin/main
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
<<<<<<< HEAD
            $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `client_id` = '".$client_id."'");
=======
            $client_data = DB::select("SELECT * FROM `client_tables` WHERE `client_id` = '".$client_id."'");
>>>>>>> origin/main
    
            // add the days you got to the expiration dates
            $next_expiration_date = $client_data[0]->next_expiration_date;
            $date1 = date("YmdHis", strtotime($next_expiration_date.''.$days));
    
            // freeze date
            $freeze_date = $freeze_type == "definate" ? date("YmdHis",strtotime($req->input('freez_dates_edit'))) : $indefinate_freezing;
            // return $freeze_date;
    
            // update the freeze data and the freeze status and the expiration date
<<<<<<< HEAD
            DB::connection("mysql2")->table('client_tables')
=======
            DB::table('client_tables')
>>>>>>> origin/main
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
<<<<<<< HEAD
                    $sms_keys = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'sms_api_key'");
                    $sms_api_key = $sms_keys[0]->value;
                    $sms_keys = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'sms_partner_id'");
                    $sms_partner_id = $sms_keys[0]->value;
                    $sms_keys = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'sms_shortcode'");
=======
                    $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'sms_api_key'");
                    $sms_api_key = $sms_keys[0]->value;
                    $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'sms_partner_id'");
                    $sms_partner_id = $sms_keys[0]->value;
                    $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'sms_shortcode'");
>>>>>>> origin/main
                    $sms_shortcode = $sms_keys[0]->value;
                    $partnerID = $sms_partner_id;
                    $apikey = $sms_api_key;
                    $shortcode = $sms_shortcode;
                    
                    
                    $client_id = $client_id;
                    $mobile = $client_data[0]->clients_contacts;
                    $sms_type = 2;
                    $message = $new_message;
                    
                    $trans_amount = 0;
<<<<<<< HEAD
                    $finalURL = "https://isms.celcomafrica.com/api/services/sendsms/?apikey=" . urlencode($apikey) . "&partnerID=" . urlencode($partnerID) . "&message=" . urlencode($message) . "&shortcode=$shortcode&mobile=$mobile";
=======
                    $finalURL = "https://mysms.celcomafrica.com/api/services/sendsms/?apikey=" . urlencode($apikey) . "&partnerID=" . urlencode($partnerID) . "&message=" . urlencode($message) . "&shortcode=$shortcode&mobile=$mobile";
>>>>>>> origin/main
                    $ch = \curl_init();
                    \curl_setopt($ch, CURLOPT_URL, $finalURL);
                    \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    \curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    $response = \curl_exec($ch);
                    \curl_close($ch);
                    $res = json_decode($response);
                    // return $res;
                    $values = $res->responses[0];
                    // return $values;
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
                    $sms_table->account_id = $client_id;
                    $sms_table->sms_type = $sms_type;
                    $sms_table->save();
                }
            }
<<<<<<< HEAD
            
            // log message
            if ($freeze_type == "definate"){
                $txt = $client_data[0]->client_name." has been frozen for $days untill ".date("dS M Y ",strtotime($freeze_date))." by ".session('Usernames')."!";
            }else{
                $txt = $client_data[0]->client_name." has been frozen for Indefinately by ".session('Usernames')."!";
            }
            $this->log($txt);
=======
    
    
            // log file capture error
            // read the data 
            $myfile = fopen(public_path("/logs/log.txt"), "r") or die("Unable to open file!");
            $file_sizes = filesize(public_path("/logs/log.txt")) > 0?filesize(public_path("/logs/log.txt")):8190;
            $existing_txt = fread($myfile,$file_sizes);
            // return $existing_txt;
            $myfile = fopen(public_path("/logs/log.txt"), "w") or die("Unable to open file!");
            $date = date("dS M Y (H:i:sa)");
            if ($freeze_type == "definate"){
                $txt = $date.": ".$client_data[0]->client_name." has been frozen for $days untill ".date("dS M Y ",strtotime($freeze_date))." by ".session('Usernames')."!\n".$existing_txt;
            }else{
                $txt = $date.": ".$client_data[0]->client_name." has been frozen for Indefinately by ".session('Usernames')."!\n".$existing_txt;
            }
            // return $txt;
            fwrite($myfile, $txt);
            fclose($myfile);
>>>>>>> origin/main
            // end of log file
            return redirect("Clients/View/".$client_id);
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
<<<<<<< HEAD
            $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `client_id` = '".$client_id."'");
=======
            $client_data = DB::select("SELECT * FROM `client_tables` WHERE `client_id` = '".$client_id."'");
>>>>>>> origin/main
    
            // add the days you got to the expiration dates
            $next_expiration_date = $client_data[0]->next_expiration_date;
            $date1 = date("YmdHis", strtotime($next_expiration_date.''.$days));
    
            // freeze date
            $freeze_date = $freeze_type == "definate" ? date("YmdHis",strtotime($req->input('freez_dates_edit'))) : $indefinate_freezing;
            // return $freeze_date;
    
            // update the freeze data and the freeze status and the expiration date
<<<<<<< HEAD
            DB::connection("mysql2")->table('client_tables')
=======
            DB::table('client_tables')
>>>>>>> origin/main
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
<<<<<<< HEAD
                    $sms_keys = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'sms_api_key'");
                    $sms_api_key = $sms_keys[0]->value;
                    $sms_keys = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'sms_partner_id'");
                    $sms_partner_id = $sms_keys[0]->value;
                    $sms_keys = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'sms_shortcode'");
=======
                    $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'sms_api_key'");
                    $sms_api_key = $sms_keys[0]->value;
                    $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'sms_partner_id'");
                    $sms_partner_id = $sms_keys[0]->value;
                    $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'sms_shortcode'");
>>>>>>> origin/main
                    $sms_shortcode = $sms_keys[0]->value;
                    $partnerID = $sms_partner_id;
                    $apikey = $sms_api_key;
                    $shortcode = $sms_shortcode;
                    
                    
                    $client_id = $client_id;
                    $mobile = $client_data[0]->clients_contacts;
                    $sms_type = 2;
                    $message = $new_message;
                    
                    $trans_amount = 0;
<<<<<<< HEAD
                    $finalURL = "https://isms.celcomafrica.com/api/services/sendsms/?apikey=" . urlencode($apikey) . "&partnerID=" . urlencode($partnerID) . "&message=" . urlencode($message) . "&shortcode=$shortcode&mobile=$mobile";
=======
                    $finalURL = "https://mysms.celcomafrica.com/api/services/sendsms/?apikey=" . urlencode($apikey) . "&partnerID=" . urlencode($partnerID) . "&message=" . urlencode($message) . "&shortcode=$shortcode&mobile=$mobile";
>>>>>>> origin/main
                    $ch = \curl_init();
                    \curl_setopt($ch, CURLOPT_URL, $finalURL);
                    \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    \curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    $response = \curl_exec($ch);
                    \curl_close($ch);
                    $res = json_decode($response);
                    // return $res;
                    $values = $res->responses[0];
                    // return $values;
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
                    $sms_table->account_id = $client_id;
                    $sms_table->sms_type = $sms_type;
                    $sms_table->save();
                }
            }
<<<<<<< HEAD

            if ($freeze_type == "definate"){
                $txt = $client_data[0]->client_name." will be frozen on ".date("D dS M Y",strtotime($freezing_date))." for $days untill ".date("dS M Y ",strtotime($freeze_date)).". Action done by ".session('Usernames')."!";
            }else{
                $txt = $client_data[0]->client_name." will be frozen on ".date("D dS M Y",strtotime($freezing_date))." Indefinately. Action done by ".session('Usernames')."!";
            }
            $this->log($txt);
=======
    
    
            // log file capture error
            // read the data 
            $myfile = fopen(public_path("/logs/log.txt"), "r") or die("Unable to open file!");
            $file_sizes = filesize(public_path("/logs/log.txt")) > 0?filesize(public_path("/logs/log.txt")):8190;
            $existing_txt = fread($myfile,$file_sizes);
            // return $existing_txt;
            $myfile = fopen(public_path("/logs/log.txt"), "w") or die("Unable to open file!");
            $date = date("dS M Y (H:i:sa)");
            if ($freeze_type == "definate"){
                $txt = $date.": ".$client_data[0]->client_name." will be frozen on ".date("D dS M Y",strtotime($freezing_date))." for $days untill ".date("dS M Y ",strtotime($freeze_date)).". Action done by ".session('Usernames')."!\n".$existing_txt;
            }else{
                $txt = $date.": ".$client_data[0]->client_name." will be frozen on ".date("D dS M Y",strtotime($freezing_date))." Indefinately. Action done by ".session('Usernames')."!\n".$existing_txt;
            }
            // return $txt;
            fwrite($myfile, $txt);
            fclose($myfile);
            // end of log file
>>>>>>> origin/main
            return redirect("Clients/View/".$client_id);
        }
    }
    // update expiration date
    function updateExpDate(Request $req){
<<<<<<< HEAD
        // change db
        $change_db = new login();
        $change_db->change_db();

        $new_expiration = date("Ymd",strtotime($req->input('expiration_date_edits')))."235959";
        $client_id = $req->input('clients_id');
        DB::connection("mysql2")->table('client_tables')
=======
        $new_expiration = date("Ymd",strtotime($req->input('expiration_date_edits')))."235959";
        $client_id = $req->input('clients_id');
        DB::table('client_tables')
>>>>>>> origin/main
        ->where('client_id', $client_id)
        ->update([
            'next_expiration_date' => $new_expiration,
            'date_changed' => date("YmdHis")
        ]);

<<<<<<< HEAD
        $client = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `client_id` = '$client_id' AND `deleted` = '0'");
        $client_name = $client[0]->client_name;
        
        $txt = ":Client ( $client_name ) expiration date changed to ".date("D dS M Y",strtotime($new_expiration)).""."! by ".session('Usernames');
        $this->log($txt);
=======
        $client = DB::select("SELECT * FROM `client_tables` WHERE `client_id` = '$client_id' AND `deleted` = '0'");
        $client_name = $client[0]->client_name;

        // log file capture error
        // read the data 
        $myfile = fopen(public_path("/logs/log.txt"), "r") or die("Unable to open file!");
        $file_sizes = filesize(public_path("/logs/log.txt")) > 0?filesize(public_path("/logs/log.txt")):8190;
        $existing_txt = fread($myfile,$file_sizes);
        // return $existing_txt;
        $myfile = fopen(public_path("/logs/log.txt"), "w") or die("Unable to open file!");
        $date = date("dS M Y (H:i:sa)");
        $txt = $date.":Client ( $client_name ) expiration date changed to ".date("D dS M Y",strtotime($new_expiration)).""."! by ".session('Usernames')."\n".$existing_txt;
        // return $txt;
        fwrite($myfile, $txt);
        fclose($myfile);
        // end of log file
>>>>>>> origin/main
        // redirect to the client table
        session()->flash("success","Updates have been done successfully!");
        return redirect("Clients/View/".$client_id);
    }

    // deactivate user from freeze
    function deactivatefreeze($client_id){
<<<<<<< HEAD
        // change db
        $change_db = new login();
        $change_db->change_db();

        $client = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `client_id` = '$client_id' AND `deleted` = '0'");
=======
        $client = DB::select("SELECT * FROM `client_tables` WHERE `client_id` = '$client_id' AND `deleted` = '0'");
>>>>>>> origin/main
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
<<<<<<< HEAD
        DB::connection("mysql2")->table('client_tables')
=======
        DB::table('client_tables')
>>>>>>> origin/main
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
<<<<<<< HEAD
                $sms_keys = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'sms_api_key'");
                $sms_api_key = $sms_keys[0]->value;
                $sms_keys = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'sms_partner_id'");
                $sms_partner_id = $sms_keys[0]->value;
                $sms_keys = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'sms_shortcode'");
=======
                $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'sms_api_key'");
                $sms_api_key = $sms_keys[0]->value;
                $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'sms_partner_id'");
                $sms_partner_id = $sms_keys[0]->value;
                $sms_keys = DB::select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'sms_shortcode'");
>>>>>>> origin/main
                $sms_shortcode = $sms_keys[0]->value;
                $partnerID = $sms_partner_id;
                $apikey = $sms_api_key;
                $shortcode = $sms_shortcode;
                
                
                $client_id = $client_id;
                $mobile = $client[0]->clients_contacts;
                $sms_type = 2;
                $message = $new_message;
                
                $trans_amount = 0;
<<<<<<< HEAD
                $finalURL = "https://isms.celcomafrica.com/api/services/sendsms/?apikey=" . urlencode($apikey) . "&partnerID=" . urlencode($partnerID) . "&message=" . urlencode($message) . "&shortcode=$shortcode&mobile=$mobile";
=======
                $finalURL = "https://mysms.celcomafrica.com/api/services/sendsms/?apikey=" . urlencode($apikey) . "&partnerID=" . urlencode($partnerID) . "&message=" . urlencode($message) . "&shortcode=$shortcode&mobile=$mobile";
>>>>>>> origin/main
                $ch = \curl_init();
                \curl_setopt($ch, CURLOPT_URL, $finalURL);
                \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                \curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $response = \curl_exec($ch);
                \curl_close($ch);
                $res = json_decode($response);
                // return $res;
                $values = $res->responses[0];
                // return $values;
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
                $sms_table->account_id = $client_id;
                $sms_table->sms_type = $sms_type;
                $sms_table->save();
            }
        }

<<<<<<< HEAD
        $txt = ":Client ( $client_name ) freeze status changed to in-active by ".session('Usernames').""."!";
        $this->log($txt);
=======
        
        // log file capture error
        // read the data 
        $myfile = fopen(public_path("/logs/log.txt"), "r") or die("Unable to open file!");
        $file_sizes = filesize(public_path("/logs/log.txt")) > 0?filesize(public_path("/logs/log.txt")):8190;
        $existing_txt = fread($myfile,$file_sizes);
        // return $existing_txt;
        $myfile = fopen(public_path("/logs/log.txt"), "w") or die("Unable to open file!");
        $date = date("dS M Y (H:i:sa)");
        $txt = $date.":Client ( $client_name ) freeze status changed to in-active by ".session('Usernames').""."!\n".$existing_txt;
        // return $txt;
        fwrite($myfile, $txt);
        fclose($myfile);
>>>>>>> origin/main
        // end of log file
        session()->flash("success","Client Unfrozen successfully".($full_days != "" ? " and ".$full_days." has been deducted to the expiration date":"")."!");
        return redirect("Clients/View/".$client_id);
    }
    // deactivate user from freeze
    function activatefreeze($client_id){
<<<<<<< HEAD
        // change db
        $change_db = new login();
        $change_db->change_db();

        // return $client_id;
        // update the client freeze status deactivated status to 
        DB::connection("mysql2")->table('client_tables')
=======
        // return $client_id;
        // update the client freeze status deactivated status to 
        DB::table('client_tables')
>>>>>>> origin/main
        ->where('client_id', $client_id)
        ->update([
            'client_freeze_status' => "1",
            'date_changed' => date("YmdHis")
        ]);

<<<<<<< HEAD
        $client = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `client_id` = '$client_id' AND `deleted` = '0'");
        $client_name = $client[0]->client_name;

        $txt = ":Client ( $client_name ) has been frozen by ".session('Usernames')."!";
        $this->log($txt);
=======
        $client = DB::select("SELECT * FROM `client_tables` WHERE `client_id` = '$client_id' AND `deleted` = '0'");
        $client_name = $client[0]->client_name;
        
        // log file capture error
        // read the data 
        $myfile = fopen(public_path("/logs/log.txt"), "r") or die("Unable to open file!");
        $file_sizes = filesize(public_path("/logs/log.txt")) > 0?filesize(public_path("/logs/log.txt")):8190;
        $existing_txt = fread($myfile,$file_sizes);
        // return $existing_txt;
        $myfile = fopen(public_path("/logs/log.txt"), "w") or die("Unable to open file!");
        $date = date("dS M Y (H:i:sa)");
        $txt = $date.":Client ( $client_name ) has been frozen by ".session('Usernames').""."!\n".$existing_txt;
        // return $txt;
        fwrite($myfile, $txt);
        fclose($myfile);
        // end of log file
>>>>>>> origin/main
        session()->flash("success","Client Unfrozen successfully!");
        return redirect("Clients/View/".$client_id);
    }
    function changeWalletBal(Request $req){
<<<<<<< HEAD
        // change db
        $change_db = new login();
        $change_db->change_db();

        // return $req;
        $client_id = $req->input('clients_id');
        $wallet_amount = $req->input('wallet_amounts');
        DB::connection("mysql2")->table('client_tables')
=======
        // return $req;
        $client_id = $req->input('clients_id');
        $wallet_amount = $req->input('wallet_amounts');
        DB::table('client_tables')
>>>>>>> origin/main
        ->where('client_id', $client_id)
        ->update([
            'wallet_amount' => $wallet_amount,
            'last_changed' => date("YmdHis"),
            'date_changed' => date("YmdHis")
        ]);

<<<<<<< HEAD
        $client = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `client_id` = '$client_id' AND `deleted` = '0'");
        $client_name = $client[0]->client_name;
        
        $txt = ":Client ( $client_name ) wallet balance has been changed to Kes $wallet_amount by ".session('Usernames').""."!";
        $this->log($txt);
=======
        $client = DB::select("SELECT * FROM `client_tables` WHERE `client_id` = '$client_id' AND `deleted` = '0'");
        $client_name = $client[0]->client_name;
        
        // log file capture error
        // read the data 
        $myfile = fopen(public_path("/logs/log.txt"), "r") or die("Unable to open file!");
        $file_sizes = filesize(public_path("/logs/log.txt")) > 0?filesize(public_path("/logs/log.txt")):8190;
        $existing_txt = fread($myfile,$file_sizes);
        // return $existing_txt;
        $myfile = fopen(public_path("/logs/log.txt"), "w") or die("Unable to open file!");
        $date = date("dS M Y (H:i:sa)");
        $txt = $date.":Client ( $client_name ) wallet balance has been changed to Kes $wallet_amount by ".session('Usernames').""."!\n".$existing_txt;
        // return $txt;
        fwrite($myfile, $txt);
        fclose($myfile);
>>>>>>> origin/main
        // end of log file
        session()->flash("success","Wallet balance has been successfully changed!");
        return redirect("Clients/View/".$client_id);
    }
<<<<<<< HEAD

    // update user
    function updateClients(Request $req){
        // change db
        $change_db = new login();
        $change_db->change_db();

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
                        $sstp_value = $this->getSSTPAddress();
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


=======
    // update user
    function updateClients(Request $req){
        $clients_id = $req->input('clients_id');
        // check user assignment 
        $client_data = DB::select("SELECT * FROM `client_tables` WHERE `client_id` = '".$clients_id."' AND `deleted` = '0'");
        if (count($client_data) > 0) {
            if($client_data[0]->assignment == "static"){
>>>>>>> origin/main
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
<<<<<<< HEAD
                
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
                $sstp_value = $this->getSSTPAddress();
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
=======
                // get the ip address and queue list above
                // get ip
                // Initiate curl session in a variable (resource)
                $curl_handle = curl_init();
        
                $baseUrl = explode(":",url('/'));
                $local_url = $baseUrl[0].":".$baseUrl[1];
                $url = "$local_url:81/crontab/getIpaddress.php?r_ip=true&r_id=".$router_name;
        
                // Set the curl URL option
                curl_setopt($curl_handle, CURLOPT_URL, $url);
        
                // This option will return data as a string instead of direct output
                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
        
                // Execute curl & store data in a variable
                $curl_data = curl_exec($curl_handle);
        
                curl_close($curl_handle);
        
                // Decode JSON into PHP array
                $router_ip_addresses = json_decode($curl_data);
        
                // get the queue
                // Initiate curl session in a variable (resource)
                $curl_handle = curl_init();
        
                $baseUrl = explode(":",url('/'));
                $local_url = $baseUrl[0].":".$baseUrl[1];
                $url = "$local_url:81/crontab/getIpaddress.php?r_queues=true&r_id=".$router_name;
        
                // Set the curl URL option
                curl_setopt($curl_handle, CURLOPT_URL, $url);
        
                // This option will return data as a string instead of direct output
                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
        
                // Execute curl & store data in a variable
                $curl_data = curl_exec($curl_handle);
        
                curl_close($curl_handle);
        
                // Decode JSON into PHP array
                $router_simple_queues = json_decode($curl_data);
                // return $router_simple_queues;
        
        
                if ($req->input('allow_router_changes') == "on") {
                    // lets get the router connection information
                    $router_data = DB::select("SELECT * FROM `router_tables` WHERE `router_id` = '".$req->input('router_name')."' AND `deleted` = '0'");
                    // connect to the router
                    $API = new routeros_api();
                    $API->debug = false;
                    // get the clients data and update in the database
                    // check if the ip addresses of the client is updated and the interface
                    $clientsData = DB::select("SELECT * FROM `client_tables` WHERE `client_id` = '".$req->input('clients_id')."' AND `deleted` = '0'");
                    // print the ip addresses on the address list
                    // edit the network of the clients
                    // if the address of the user is used as a parent of other clients don`t delete the address in the ip address list
                    // add the new address to the address table
                    // if the target is a child of a network in the queue list delete the queue and add a new address from the new network
                    if ($req->input('client_network') != $clientsData[0]->client_network) {
                        // if ($req->input('client_gw') != $clientsData[0]->client_default_gw) {
                            // print the ip address from the address list
                            if ($API->connect($router_data[0]->router_ipaddr,$router_data[0]->router_api_username,$router_data[0]->router_api_password,$router_data[0]->router_api_port)) {
                                $client_data = DB::select("SELECT * FROM `client_tables` WHERE `client_id` = '$clients_id' AND `deleted` = '0'");
                                // return $client_data;
                                $old_network = $client_data[0]->client_network;
                                $old_client_gw = $client_data[0]->client_default_gw;
                                // check if the ip is present if its not present add it if not change the data
                                // return $old_client_gw;
                                $ip_address = $router_ip_addresses;
                                // return $ip_address;
                                $present = 0;
                                $myids = "";
                                foreach ($ip_address as $key => $value) {
                                    foreach ($value as $key1 => $value1) {
                                        if ($key1 == ".id") {
                                            $myids = $value1;
                                        }
                                        if ($key1 == "network") {
                                            if ($value1 == $old_network) {
                                                $present = 1;
                                                break;
                                            }
                                        }
                                    }
                                    if ($present == 1) {
                                        break;
                                    }
                                }
                                // return $old_network;
                                // if the ip address is present change its details
                                if ($present == 1) {
                                    // set the ip address using its id
                                    $result = $API->comm("/ip/address/set",
                                    array(
                                        "address"     => $req->input('client_gw'),
                                        "interface" => $req->input('interface_name'),
                                        "comment"  => $req->input('client_name')." (".$req->input('client_address')." - ".$location_coordinates.") - ".$client_account_number,
                                        ".id" => $myids
                                    ));
                                    if(count($result) > 0){
                                        // this means there is an error
                                        $API->comm("/ip/address/set",
                                        array(
                                            "interface" => $req->input('interface_name'),
                                            "comment"  => $req->input('client_name')." (".$req->input('client_address')." - ".$location_coordinates.") - ".$client_account_number,
                                            ".id" => $myids
                                        ));
                                    }
                                }else {
                                    // add a new ip address
                                    $API->comm("/ip/address/add", 
                                    array(
                                        "address"     => $req->input('client_gw'),
                                        "interface" => $req->input('interface_name'),
                                        "network" => $req->input('client_network'),
                                        "comment"  => $req->input('client_name')." (".$req->input('client_address')." - ".$location_coordinates.") - ".$client_account_number
                                    ));
                                }
                                // return "New ip = ".$req->input('client_network');
                                // check if there is a queue if its not present add if its present set it
                                $queueList = $router_simple_queues;
                                $present = 0;
                                $queue_id = "";
                                foreach ($queueList as $key => $value) {
                                    foreach ($value as $key1 => $value1) {
                                        if($key1 == ".id"){
                                            $queue_id = $value1;
                                        }
                                        if($value1 == $old_network."/".explode("/",$old_client_gw)[1]){
                                            $present = 1;
                                            break;
                                        }
                                    }
                                    if ($present == 1) {
                                        break;
                                    }
                                }
        
                                $upload = $upload_speed.$unit1;
                                $download = $download_speed.$unit2;
        
                                // return $old_network."/".explode("/",$old_client_gw)[1];
                                if ($present == 1) {
                                    // set the queue using the ip address
                                    $API->comm("/queue/simple/set",
                                        array(
                                            "name" => $req->input('client_name')." (".$req->input('client_address')." - ".$location_coordinates.") - ".$client_account_number,
                                            "target" => $client_network."/".explode("/",$client_gw_name)[1],
                                            "max-limit" => $upload."/".$download,
                                            ".id" => $queue_id
        
                                        )
                                    );
                                }else {
                                    // add the queue to the list
                                    $API->comm("/queue/simple/add",
                                        array(
                                            "name" => $req->input('client_name')." (".$req->input('client_address')." - ".$location_coordinates.") - ".$client_account_number,
                                            "target" => $client_network."/".explode("/",$client_gw_name)[1],
                                            "max-limit" => $upload."/".$download
                                        )
                                    );
                                }
        
                                $upload = $upload_speed.$unit1;
                                $download = $download_speed.$unit2;
            
                                // update the table
                                DB::table('client_tables')
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
        
                                // log file capture error
                                // read the data 
                                $myfile = fopen(public_path("/logs/log.txt"), "r") or die("Unable to open file!");
                                $file_sizes = filesize(public_path("/logs/log.txt")) > 0?filesize(public_path("/logs/log.txt")):8190;
                                $existing_txt = fread($myfile,$file_sizes);
                                // return $existing_txt;
                                $myfile = fopen(public_path("/logs/log.txt"), "w") or die("Unable to open file!");
                                $date = date("dS M Y (H:i:sa)");
                                $txt = $date.":Client (".$client_name.") information modified by ".session('Usernames')." on the router\n".$existing_txt;
                                // return $txt;
                                fwrite($myfile, $txt);
                                fclose($myfile);
                                // end of log file
                                        
                                // redirect to the client table
                                session()->flash("success","Updates have been done successfully!");
                                return redirect("Clients/View/".$clients_id);
                                $API->disconnect();
                            }else {
                                session()->flash("error","Cannot connect to the router check if its active or its ip address has been changed");
                                return redirect("Clients/View/".$clients_id);
                            }
                        // }else {
                        //     session()->flash("error","Updates cant be done because the ip address is different to the network!");
                        //     return redirect("Clients/View/".$clients_id);
                        // }
                    }else {
                        session()->flash("success","Updates have been done successfully!");
                        // update if the ip addresses have not changed
                        // go throught the ips and make changes on the new ip changes
                        $API = new routeros_api();
                        $API->debug = false;
                        if ($API->connect($router_data[0]->router_ipaddr,$router_data[0]->router_api_username,$router_data[0]->router_api_password,$router_data[0]->router_api_port)) {
                            // get the clients old network and ip address
                            $client_data = DB::select("SELECT * FROM `client_tables` WHERE `client_id` = '$clients_id' AND `deleted` = '0'");
                            // return $client_data;
                            $old_network = $client_data[0]->client_network;
                            $old_client_gw = $client_data[0]->client_default_gw;
                            // check if the ip is present if its not present add it if not change the data
                            $ip_address = $router_ip_addresses;
                            // return $ip_address;
                            $present = 0;
                            $myids = "";
                            foreach ($ip_address as $key => $value) {
                                foreach ($value as $key1 => $value1) {
                                    if ($key1 == ".id") {
                                        $myids = $value1;
                                    }
                                    if ($value1 == $old_network) {
                                        $present = 1;
                                        break;
                                    }
                                }
                                if ($present == 1) {
                                    break;
                                }
                            }
                            // return $old_network;
                            // if the ip address is present change its details
                            if ($present == 1) {
                                // set the ip address using its id
                                $result = $API->comm("/ip/address/set",
                                array(
                                    "address"     => $req->input('client_gw'),
                                    "interface" => $req->input('interface_name'),
                                    "comment"  => $req->input('client_name')." (".$req->input('client_address')." - ".$location_coordinates.") - ".$client_account_number,
                                    ".id" => $myids
                                ));
                                if(count($result) > 0){
                                    // this means there is an error
                                    $API->comm("/ip/address/set",
                                    array(
                                        "interface" => $req->input('interface_name'),
                                        "comment"  => $req->input('client_name')." (".$req->input('client_address')." - ".$location_coordinates.") - ".$client_account_number,
                                        ".id" => $myids
                                    ));
                                }
                            }else {
                                // add a new ip address
                                $API->comm("/ip/address/add", 
>>>>>>> origin/main
                                array(
                                    "address"     => $req->input('client_gw'),
                                    "interface" => $req->input('interface_name'),
                                    "network" => $req->input('client_network'),
<<<<<<< HEAD
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
                        $this->log($txt);
                        // end of log file
                    }

                    // update the clients
                    $upload = $upload_speed.$unit1;
                    $download = $download_speed.$unit2;

                    // update the table
                    DB::connection("mysql2")->table('client_tables')
=======
                                    "comment"  => $req->input('client_name')." (".$req->input('client_address')." - ".$location_coordinates.") - ".$client_account_number
                                ));
                            }
        
                            // check if there is a queue if its not present add if its present set it
                            $queueList = $router_simple_queues;
                            $present = 0;
                            $queue_id = "";
                            foreach ($queueList as $key => $value) {
                                foreach ($value as $key1 => $value1) {
                                    if($key1 == ".id"){
                                        $queue_id = $value1;
                                    }
                                    if($value1 == $old_network."/".explode("/",$old_client_gw)[1]){
                                        $present = 1;
                                        break;
                                    }
                                }
                                if ($present == 1) {
                                    break;
                                }
                            }
        
                            $upload = $upload_speed.$unit1;
                            $download = $download_speed.$unit2;
        
                            // return $download;
                            if ($present == 1) {
                                // set the queue using the ip address
                                $API->comm("/queue/simple/set",
                                    array(
                                        "name" => $req->input('client_name')." (".$req->input('client_address')." - ".$location_coordinates.") - ".$client_account_number,
                                        "target" => $client_network."/".explode("/",$client_gw_name)[1],
                                        "max-limit" => $upload."/".$download,
                                        ".id" => $queue_id
        
                                    )
                                );
                            }else {
                                // add the queue to the list
                                $API->comm("/queue/simple/add",
                                    array(
                                        "name" => $req->input('client_name')." (".$req->input('client_address')." - ".$location_coordinates.") - ".$client_account_number,
                                        "target" => $client_network."/".explode("/",$client_gw_name)[1],
                                        "max-limit" => $upload."/".$download
                                    )
                                );
                            }
            
            
                            // update the user data in the database
                                // update the table
                            DB::table('client_tables')
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
                            session()->flash("success","Updates have been done successfully!");
        
                            // log file capture error
                            // read the data 
                            $myfile = fopen(public_path("/logs/log.txt"), "r") or die("Unable to open file!");
                            $file_sizes = filesize(public_path("/logs/log.txt")) > 0?filesize(public_path("/logs/log.txt")):8190;
                            $existing_txt = fread($myfile,$file_sizes);
                            // return $existing_txt;
                            $myfile = fopen(public_path("/logs/log.txt"), "w") or die("Unable to open file!");
                            $date = date("dS M Y (H:i:sa)");
                            $txt = $date.":Client (".$client_name.") information modified by ".session('Usernames')." on the router\n".$existing_txt;
                            // return $txt;
                            fwrite($myfile, $txt);
                            fclose($myfile);
                            // end of log file
                        }else{
                            session()->flash("error","Cannot connect to the router check if its active or its ip address has been changed");
                        }
                        // go through the ips and get the id for the ip to set with the new changes
                        return redirect("Clients/View/".$clients_id);
                    }
                }else {
                    // update the table
        
                    $upload = $upload_speed.$unit1;
                    $download = $download_speed.$unit2;
                    $comments = $client_name." (".$client_address.")";
        
                    DB::table('client_tables')
>>>>>>> origin/main
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
<<<<<<< HEAD
                            
                    // redirect to the client table
                    $API->disconnect();
                    session()->flash("success","Updates have been done successfully!");
                    return redirect("Clients/View/".$clients_id);
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
                        $sstp_value = $this->getSSTPAddress();
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

=======
        
                    // log file capture error
                    // read the data 
                    $myfile = fopen(public_path("/logs/log.txt"), "r") or die("Unable to open file!");
                    $file_sizes = filesize(public_path("/logs/log.txt")) > 0?filesize(public_path("/logs/log.txt")):8190;
                    $existing_txt = fread($myfile,$file_sizes);
                    // return $existing_txt;
                    $myfile = fopen(public_path("/logs/log.txt"), "w") or die("Unable to open file!");
                    $date = date("dS M Y (H:i:sa)");
                    $txt = "".$date.":Client (".$client_name.") information modified by ".session('Usernames')." on the DB only\n".$existing_txt;
                    // return $txt;
                    fwrite($myfile, $txt);
                    fclose($myfile);
                    // end of log file
                    session()->flash("success","Updates have been done successfully to the database, your router has not been changed!");
                }
                // return $clientsData;
    
            }elseif ($client_data[0]->assignment == "pppoe") {
>>>>>>> origin/main
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
<<<<<<< HEAD
                
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
                $sstp_value = $this->getSSTPAddress();
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
=======
                // if the secret is present in the router overwrite it

                $curl_handle = curl_init();
                $baseUrl = explode(":",url('/'));
                $local_url = $baseUrl[0].":".$baseUrl[1];
                $url = "$local_url:81/crontab/getIpaddress.php?r_ppoe_secrets=true&r_id=".$router_name;
                // Set the curl URL option
                curl_setopt($curl_handle, CURLOPT_URL, $url);
                // This option will return data as a string instead of direct output
                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
                // Execute curl & store data in a variable
                $curl_data = curl_exec($curl_handle);
                curl_close($curl_handle);
                // Decode JSON into PHP array
                $router_secrets = json_decode($curl_data);
                // loop through the secrets and find the name
                $present = 0;
                $secret_id = 0;
                for ($index=0; $index < count($router_secrets); $index++) { 
                    $secret = $router_secrets[$index];
                    if ($secret->name == $client_secret_username) {
                        $present = 1;
                        foreach ($secret as $key => $value) {
                            if ($key == ".id") {
                                $secret_id = $value;
                                break;
                            }
                        }
                        break;
                    }
                }
                // return $present;
                if ($present == 1) {
                    // set the client secret// proceed and set the user credentials
                    $router_data = DB::select("SELECT * FROM `router_tables` WHERE `router_id` = '$router_name' AND `deleted` = '0' LIMIT 1");
                    $ip_address = $router_data[0]->router_ipaddr;
                    $router_api_username = $router_data[0]->router_api_username;
                    $router_api_password = $router_data[0]->router_api_password;
                    $router_api_port = $router_data[0]->router_api_port;
            
            
                    // connect to the router and add the ip address and queues to the interface
                    $API = new routeros_api();
                    $API->debug = false;
                    // check if the connection is valid
                    if ($API->connect($ip_address, $router_api_username, $router_api_password, $router_api_port)){
                        // add a new ip address
                        $change_router = 0;
                        if ($allow_router_changes == "on") {
>>>>>>> origin/main
                            $API->comm("/ppp/secret/set", 
                            array(
                                "name"     => $client_secret_username,
                                "service" => "pppoe",
                                "password" => $client_secret_password,
                                "profile"  => $pppoe_profile,
                                "comment"  => $client_name." (".$client_address." - ".$location_coordinates.") - ".$client_account_number,
<<<<<<< HEAD
                                "disabled" => ($client_status == 0 ? "true" : "false"),
                                ".id" => $secret_id
                            ));
                        }else{
                            // if the secret is not found add the secrets
=======
                                "disabled" => "false",
                                ".id" => $secret_id
                            ));
                            $change_router = 1;
                        }
                        // update the user data // update the table
                        DB::table('client_tables')
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

                        // log file capture error
                        // read the data 
                        $myfile = fopen(public_path("/logs/log.txt"), "r") or die("Unable to open file!");
                        $file_sizes = filesize(public_path("/logs/log.txt")) > 0?filesize(public_path("/logs/log.txt")):8190;
                        $existing_txt = fread($myfile,$file_sizes);
                        // return $existing_txt;
                        $myfile = fopen(public_path("/logs/log.txt"), "w") or die("Unable to open file!");
                        $date = date("dS M Y (H:i:sa)");
                        $text = $change_router == 0 ? "" : "on router!";
                        $txt = $date.":Client (".$client_name.") information modified by ".session('Usernames')." ".$text."\n".$existing_txt;
                        // return $txt;
                        fwrite($myfile, $txt);
                        fclose($myfile);
                        // end of log file
                                
                        // redirect to the client table
                        session()->flash("success","Updates have been done successfully!");
                        $API->disconnect();
                        return redirect("Clients/View/".$clients_id);
                    }else{
                        session()->flash("error","Cannot connect to the router check if its active or its ip address has been changed");
                        return redirect("Clients/View/".$clients_id);
                    }
                }else {
                    // set the client secret
                    // proceed and register the user
                    $router_data = DB::select("SELECT * FROM `router_tables` WHERE `router_id` = '$router_name' AND `deleted` = '0' LIMIT 1");
                    $ip_address = $router_data[0]->router_ipaddr;
                    $router_api_username = $router_data[0]->router_api_username;
                    $router_api_password = $router_data[0]->router_api_password;
                    $router_api_port = $router_data[0]->router_api_port;
            
            
                    // connect to the router and add the ip address and queues to the interface
                    $API = new routeros_api();
                    $API->debug = false;
                    // check if the connection is valid
                    if ($API->connect($ip_address, $router_api_username, $router_api_password, $router_api_port)){
                        // add a new ip address
                        $change_router = 0;
                        if ($allow_router_changes == "on") {
>>>>>>> origin/main
                            $API->comm("/ppp/secret/add", 
                            array(
                                "name"     => $client_secret_username,
                                "service" => "pppoe",
                                "password" => $client_secret_password,
                                "profile"  => $pppoe_profile,
                                "comment"  => $client_name." (".$client_address." - ".$location_coordinates.") - ".$client_account_number,
<<<<<<< HEAD
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
                        $this->log($txt);
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
                        $this->log($txt);
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
=======
                                "disabled" => "false"
                            ));
                            $change_router = 1;
                        }
                        // update the user data // update the table
                            DB::table('client_tables')
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

                        // log file capture error
                        // read the data 
                        $myfile = fopen(public_path("/logs/log.txt"), "r") or die("Unable to open file!");
                        $file_sizes = filesize(public_path("/logs/log.txt")) > 0?filesize(public_path("/logs/log.txt")):8190;
                        $existing_txt = fread($myfile,$file_sizes);
                        // return $existing_txt;
                        $myfile = fopen(public_path("/logs/log.txt"), "w") or die("Unable to open file!");
                        $date = date("dS M Y (H:i:sa)");
                        $text = $change_router == 0 ? "" : "on router!";
                        $txt = $date.":Client (".$client_name.") information modified by ".session('Usernames')." ".$text."\n".$existing_txt;
                        // return $txt;
                        fwrite($myfile, $txt);
                        fclose($myfile);
                        // end of log file
                                
                        // redirect to the client table
                        session()->flash("success","Updates have been done successfully!");
                        $API->disconnect();
                        return redirect("Clients/View/".$clients_id);
                    }else{
                        session()->flash("error","Cannot connect to the router check if its active or its ip address has been changed");
                        return redirect("Clients/View/".$clients_id);
                    }
                }
            }
        }else {
            // return error of client not present
>>>>>>> origin/main
        }

    }

<<<<<<< HEAD
    function log($log_message){
        // read the data
        $myfile = fopen(public_path("/logs/".session("database_name").".txt"), "a+") or die("Unable to open file!");
        $file_sizes = filesize(public_path("/logs/".session("database_name").".txt")) > 0?filesize(public_path("/logs/".session("database_name").".txt")):8190;
        $existing_txt = fread($myfile,$file_sizes);
        // return $existing_txt;
        $myfile = fopen(public_path("/logs/".session("database_name").".txt"), "w") or die("Unable to open file!");
        $date = date("dS M Y (H:i:sa)");
        $txt = $date.$log_message."\n".$existing_txt;
        // return $txt;
        fwrite($myfile, $txt);
        fclose($myfile);
    }

    function log_db($log_message, $database_name = null){
        if ($database_name == null) {
            return 0;
        }
        // read the data
        $myfile = fopen(public_path("/logs/".$database_name.".txt"), "a+") or die("Unable to open file!");
        $file_sizes = filesize(public_path("/logs/".$database_name.".txt")) > 0?filesize(public_path("/logs/".$database_name.".txt")):8190;
        $existing_txt = fread($myfile,$file_sizes);
        // return $existing_txt;
        $myfile = fopen(public_path("/logs/".$database_name.".txt"), "w") or die("Unable to open file!");
        $date = date("dS M Y (H:i:sa)");
        $txt = $date.$log_message."\n".$existing_txt;
        // return $txt;
        fwrite($myfile, $txt);
        fclose($myfile);
    }

    // deactivate the user
    function deactivate($userid,$database_name = null){
        // change db
        $change_db = new login();
        if ($database_name != null) {
            $change_db->change_db($database_name);
            session()->put("database_name", $database_name);
        }else{
            $change_db->change_db();
        }

        // get the user router and update the setting
        $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `client_id` = '$userid' AND `deleted` = '0'");
=======
    // deactivate the user
    function deactivate($userid){
        // get the user router and update the setting
        $client_data = DB::select("SELECT * FROM `client_tables` WHERE `client_id` = '$userid' AND `deleted` = '0'");
>>>>>>> origin/main
        if (count($client_data) > 0) {
            if ($client_data[0]->assignment == "static") {
                $router_id = $client_data[0]->router_name;
                // connect to the router and deactivate the client address
<<<<<<< HEAD
                $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `router_id` = '$router_id' AND `deleted` = '0'");

                // get the sstp credentails they are also the api usernames
                $sstp_username = $router_data[0]->sstp_username;
                $sstp_password = $router_data[0]->sstp_password;
                $api_port = $router_data[0]->api_port;
                

                // connect to the router and set the sstp client
                $sstp_value = $this->getSSTPAddress();
                if ($sstp_value == null) {
                    if (session('Usernames')) {
                        $error = "The SSTP server is not set, Contact your administrator!";
                        session()->flash("network_presence",$error);
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
                        session()->flash("network_presence",$error);
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
=======
                $router_data = DB::select("SELECT * FROM `router_tables` WHERE `router_id` = '$router_id' AND `deleted` = '0'");
        
                // get the ip address and queue list above
                // get ip
                // Initiate curl session in a variable (resource)
                $curl_handle = curl_init();
        
                $baseUrl = explode(":",url('/'));
                $local_url = $baseUrl[0].":".$baseUrl[1];
                $url = "$local_url:81/crontab/getIpaddress.php?r_ip=true&r_id=".$router_id;
        
                // Set the curl URL option
                curl_setopt($curl_handle, CURLOPT_URL, $url);
        
                // This option will return data as a string instead of direct output
                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
        
                // Execute curl & store data in a variable
                $curl_data = curl_exec($curl_handle);
        
                curl_close($curl_handle);
        
                // Decode JSON into PHP array
                $router_ip_addresses = json_decode($curl_data);
        
>>>>>>> origin/main
        
                // create the router os api
                $API = new routeros_api();
                $API->debug = false;
<<<<<<< HEAD

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
                    
=======
                // create connection
        
                if ($API->connect($router_data[0]->router_ipaddr,$router_data[0]->router_api_username,$router_data[0]->router_api_password,$router_data[0]->router_api_port)) {
                    // connection created deactivate the user
                    $ip_addresses = $router_ip_addresses;
                    // return $ip_addresses;
>>>>>>> origin/main
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
<<<<<<< HEAD
                    

=======
                    // return $ip_id;
>>>>>>> origin/main
                    // deactivate the id
                    if (strlen($ip_id) > 0) {
                        // deactivate
                        $deactivate = $API->comm("/ip/address/set", array(
<<<<<<< HEAD
                            "disabled" => "true",
                            ".id" => $ip_id
                        ));
                        // update the user data to de-activated
                        DB::connection("mysql2")->table('client_tables')
=======
                            "disabled" => "yes",
                            ".id" => $ip_id
                        ));
                        // update the user data to de-activated
                        DB::table('client_tables')
>>>>>>> origin/main
                        ->where('client_id', $userid)
                        ->update([
                            'client_status' => "0",
                            'date_changed' => date("YmdHis")
                        ]);
<<<<<<< HEAD

                        // log message
                        $txt = ":Client (".$client_data[0]->client_name.") deactivated by ".(session('Usernames') ? session('Usernames'):"System");
                        $this->log($txt);

                        // end of log file
                        if (session('Usernames')) {
                            session()->flash("success","User has been successfully deactivated");
                            return redirect("/Clients/View/$userid");
                        }else{
                            return "";
                        }
                    }else {
                        if (session('Usernames')) {
                            session()->flash("error","The user ip address not found in the router address list");
                            return redirect("/Clients/View/$userid");
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
                        return redirect("/Clients/View/$userid");
                    }else{
                        return "";
                    }
=======
    
                        // log file capture error
                        // read the data 
                        $myfile = fopen(public_path("/logs/log.txt"), "r") or die("Unable to open file!");
                        $file_sizes = filesize(public_path("/logs/log.txt")) > 0?filesize(public_path("/logs/log.txt")):8190;
                        $existing_txt = fread($myfile,$file_sizes);
                        // return $existing_txt;
                        $myfile = fopen(public_path("/logs/log.txt"), "w") or die("Unable to open file!");
                        $date = date("dS M Y (H:i:sa)");
                        $txt = $date.":Client (".$client_data[0]->client_name.") deactivated by ".(session('Usernames') ? session('Usernames'):"System")."\n".$existing_txt;
                        // return $txt;
                        fwrite($myfile, $txt);
                        fclose($myfile);
                        // end of log file
                        session()->flash("success","User has been successfully deactivated");
                        return redirect("/Clients/View/$userid");
                    }else {
                        session()->flash("error","The user ip address not found in the router address list");
                        return redirect("/Clients/View/$userid");
                    }
                }else {
                    session()->flash("error","Cannot connect to the router!");
                    return redirect("/Clients/View/$userid");
>>>>>>> origin/main
                }
            }elseif ($client_data[0]->assignment == "pppoe") {
                // disable the client secret and remove the client from active connections
                $router_id = $client_data[0]->router_name;
                // connect to the router and deactivate the client address
<<<<<<< HEAD
                $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `router_id` = '$router_id' AND `deleted` = '0'");
                
                if (count($router_data) == 0) {
                    if (session('Usernames')) {
                        $error = "Router that the client is connected to is not present!";
                        session()->flash("network_presence",$error);
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
                $sstp_value = $this->getSSTPAddress();
                if ($sstp_value == null) {
                    if (session('Usernames')) {
                        $error = "The SSTP server is not set, Contact your administrator!";
                        session()->flash("network_presence",$error);
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
                        session()->flash("network_presence",$error);
                        return redirect(url()->previous());
                    }else{
                        return "";
                    }
                }
=======
                $router_data = DB::select("SELECT * FROM `router_tables` WHERE `router_id` = '$router_id' AND `deleted` = '0'");
        
                // get the ip address and queue list above
                // get secrets
                // Initiate curl session in a variable (resource)
                $curl_handle = curl_init();
        
                $baseUrl = explode(":",url('/'));
                $local_url = $baseUrl[0].":".$baseUrl[1];
                $url = "$local_url:81/crontab/getIpaddress.php?r_ppoe_secrets=true&r_id=".$router_id;
        
                // Set the curl URL option
                curl_setopt($curl_handle, CURLOPT_URL, $url);
        
                // This option will return data as a string instead of direct output
                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
        
                // Execute curl & store data in a variable
                $curl_data = curl_exec($curl_handle);
        
                curl_close($curl_handle);
        
                // Decode JSON into PHP array
                $router_secrets = json_decode($curl_data);
                // return $router_secrets;

                // get the active connection
                // Initiate curl session in a variable (resource)
                $curl_handle = curl_init();
        
                $baseUrl = explode(":",url('/'));
                $local_url = $baseUrl[0].":".$baseUrl[1];
                $url = "$local_url:81/crontab/getIpaddress.php?r_active_secrets=true&r_id=".$router_id;
        
                // Set the curl URL option
                curl_setopt($curl_handle, CURLOPT_URL, $url);
        
                // This option will return data as a string instead of direct output
                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
        
                // Execute curl & store data in a variable
                $curl_data = curl_exec($curl_handle);
        
                curl_close($curl_handle);
        
                // Decode JSON into PHP array
                $active_connections = json_decode($curl_data);
                // return $active_connections;
>>>>>>> origin/main
        
                // client secret name 
                $secret_name = $client_data[0]->client_secret;
                // create the router os api
                $API = new routeros_api();
                $API->debug = false;
<<<<<<< HEAD
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

=======
                if ($API->connect($router_data[0]->router_ipaddr,$router_data[0]->router_api_username,$router_data[0]->router_api_password,$router_data[0]->router_api_port)){
>>>>>>> origin/main
                    // loop through the secrets get the id and use it to disable the secret
                    $secret_id = "0";
                    for ($indexes=0; $indexes < count($router_secrets); $indexes++) { 
                        $secrets = $router_secrets[$indexes];
<<<<<<< HEAD
                        if ($secrets['name'] == $secret_name) {
=======
                        if ($secrets->name == $secret_name) {
>>>>>>> origin/main
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
<<<<<<< HEAD
                        if ($actives['name'] == $secret_name) {
=======
                        if ($actives->name == $secret_name) {
>>>>>>> origin/main
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
<<<<<<< HEAD
                    DB::connection("mysql2")->table('client_tables')
=======
                    DB::table('client_tables')
>>>>>>> origin/main
                    ->where('client_id', $userid)
                    ->update([
                        'client_status' => "0",
                        'date_changed' => date("YmdHis")
                    ]);

<<<<<<< HEAD
                    // log message
                    $txt = ":Client (".$client_data[0]->client_name.") deactivated by ".(session('Usernames') ? session('Usernames'):"System");
                    $this->log($txt);
                    // end of log file
                    if (session('Usernames')) {
                        session()->flash("success","User has been successfully deactivated");
                        return redirect("/Clients/View/$userid");
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
                        return redirect("/Clients/View/$userid");
                    }else{
                        return "";
                    }
                }
            }
        }else {
            if (session('Usernames')) {
                session()->flash("error_clients","Client not found!");
                return redirect("/Clients");
            }else{
                return "";
            }
        }
    }
    // activate the user
    function activate($userid,$database_name = null){
        // change db
        $change_db = new login();
        if ($database_name != null) {
            $change_db->change_db($database_name);
            session()->put("database_name", $database_name);
        }else{
            $change_db->change_db();
        }

        /*****starts here */
        // get the user router and update the setting
        $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `client_id` = '$userid' AND `deleted` = '0'");
=======
                    // log file capture error
                    // read the data 
                    $myfile = fopen(public_path("/logs/log.txt"), "r") or die("Unable to open file!");
                    $file_sizes = filesize(public_path("/logs/log.txt")) > 0?filesize(public_path("/logs/log.txt")):8190;
                    $existing_txt = fread($myfile,$file_sizes);
                    // return $existing_txt;
                    $myfile = fopen(public_path("/logs/log.txt"), "w") or die("Unable to open file!");
                    $date = date("dS M Y (H:i:sa)");
                    $txt = $date.":Client (".$client_data[0]->client_name.") deactivated by ".(session('Usernames') ? session('Usernames'):"System")."\n".$existing_txt;
                    // return $txt;
                    fwrite($myfile, $txt);
                    fclose($myfile);
                    // end of log file
                    session()->flash("success","User has been successfully deactivated");
                    return redirect("/Clients/View/$userid");
                }else {
                    session()->flash("error","Cannot connect to the router!");
                    return redirect("/Clients/View/$userid");
                }
            }
        }else {
            session()->flash("error_clients","Client not found!");
            return redirect("/Clients");
        }
    }
    // deactivate the user outer API
    function deactivate2($userid){
        // get the user router and update the setting
        // get if the client id is separated with a comma
        if (str_contains($userid,",")) {
            $client_ids = explode(",",$userid);
            for ($i=0; $i < count($client_ids); $i++) {
                $userid = $client_ids[$i];
                /**Starts Here */
                $client_data = DB::select("SELECT * FROM `client_tables` WHERE `client_id` = '$userid' AND `deleted` = '0'");
                if (count($client_data) > 0) {
                    if ($client_data[0]->assignment == "static") {
                        $router_id = $client_data[0]->router_name;
                        // connect to the router and deactivate the client address
                        $router_data = DB::select("SELECT * FROM `router_tables` WHERE `router_id` = '$router_id' AND `deleted` = '0'");
                
                        // get the ip address and queue list above
                        // get ip
                        // Initiate curl session in a variable (resource)
                        $curl_handle = curl_init();
                
                        $baseUrl = explode(":",url('/'));
                        $local_url = $baseUrl[0].":".$baseUrl[1];
                        $url = "$local_url:81/crontab/getIpaddress.php?r_ip=true&r_id=".$router_id;
                
                        // Set the curl URL option
                        curl_setopt($curl_handle, CURLOPT_URL, $url);
                
                        // This option will return data as a string instead of direct output
                        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
                
                        // Execute curl & store data in a variable
                        $curl_data = curl_exec($curl_handle);
                
                        curl_close($curl_handle);
                
                        // Decode JSON into PHP array
                        $router_ip_addresses = json_decode($curl_data);
                
                
                        // create the router os api
                        $API = new routeros_api();
                        $API->debug = false;
                        // create connection
                
                        if ($API->connect($router_data[0]->router_ipaddr,$router_data[0]->router_api_username,$router_data[0]->router_api_password,$router_data[0]->router_api_port)) {
                            // connection created deactivate the user
                            $ip_addresses = $router_ip_addresses;
                            // return $ip_addresses;
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
                            // return $ip_id;
                            // deactivate the id
                            if (strlen($ip_id) > 0) {
                                // deactivate
                                $deactivate = $API->comm("/ip/address/set", array(
                                    "disabled" => "yes",
                                    ".id" => $ip_id
                                ));
                                // update the user data to de-activated
                                DB::table('client_tables')
                                ->where('client_id', $userid)
                                ->update([
                                    'client_status' => "0",
                                    'date_changed' => date("YmdHis")
                                ]);
            
                                // log file capture error
                                // read the data 
                                $myfile = fopen(public_path("/logs/log.txt"), "r") or die("Unable to open file!");
                                $file_sizes = filesize(public_path("/logs/log.txt")) > 0?filesize(public_path("/logs/log.txt")):8190;
                                $existing_txt = fread($myfile,$file_sizes);
                                // return $existing_txt;
                                $myfile = fopen(public_path("/logs/log.txt"), "w") or die("Unable to open file!");
                                $date = date("dS M Y (H:i:sa)");
                                $txt = $date.":Client (".$client_data[0]->client_name.") deactivated by ".(session('Usernames') ? session('Usernames'):"System")."\n".$existing_txt;
                                // return $txt;
                                fwrite($myfile, $txt);
                                fclose($myfile);
                                // end of log file
                                // session()->flash("success","User has been successfully deactivated");
                                // return redirect("/Clients/View/$userid");
                            }else {
                                // session()->flash("error","The user ip address not found in the router address list");
                                // return redirect("/Clients/View/$userid");
                            }
                        }else {
                            // session()->flash("error","Cannot connect to the router!");
                            // return redirect("/Clients/View/$userid");
                        }
                    }elseif ($client_data[0]->assignment == "pppoe") {
                        // disable the client secret and remove the client from active connections
                        $router_id = $client_data[0]->router_name;
                        // connect to the router and deactivate the client address
                        $router_data = DB::select("SELECT * FROM `router_tables` WHERE `router_id` = '$router_id' AND `deleted` = '0'");
                
                        // get the ip address and queue list above
                        // get secrets
                        // Initiate curl session in a variable (resource)
                        $curl_handle = curl_init();
                
                        $baseUrl = explode(":",url('/'));
                        $local_url = $baseUrl[0].":".$baseUrl[1];
                        $url = "$local_url:81/crontab/getIpaddress.php?r_ppoe_secrets=true&r_id=".$router_id;
                
                        // Set the curl URL option
                        curl_setopt($curl_handle, CURLOPT_URL, $url);
                
                        // This option will return data as a string instead of direct output
                        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
                
                        // Execute curl & store data in a variable
                        $curl_data = curl_exec($curl_handle);
                
                        curl_close($curl_handle);
                
                        // Decode JSON into PHP array
                        $router_secrets = json_decode($curl_data);
                        // return $router_secrets;

                        // get the active connection
                        // Initiate curl session in a variable (resource)
                        $curl_handle = curl_init();
                
                        $baseUrl = explode(":",url('/'));
                        $local_url = $baseUrl[0].":".$baseUrl[1];
                        $url = "$local_url:81/crontab/getIpaddress.php?r_active_secrets=true&r_id=".$router_id;
                
                        // Set the curl URL option
                        curl_setopt($curl_handle, CURLOPT_URL, $url);
                
                        // This option will return data as a string instead of direct output
                        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
                
                        // Execute curl & store data in a variable
                        $curl_data = curl_exec($curl_handle);
                
                        curl_close($curl_handle);
                
                        // Decode JSON into PHP array
                        $active_connections = json_decode($curl_data);
                        // return $curl_data;
                
                        // client secret name 
                        $secret_name = $client_data[0]->client_secret;
                        // create the router os api
                        $API = new routeros_api();
                        $API->debug = false;
                        if ($API->connect($router_data[0]->router_ipaddr,$router_data[0]->router_api_username,$router_data[0]->router_api_password,$router_data[0]->router_api_port)){
                            // loop through the secrets get the id and use it to disable the secret
                            $secret_id = "0";
                            for ($indexes=0; $indexes < count($router_secrets); $indexes++) { 
                                $secrets = $router_secrets[$indexes];
                                if ($secrets->name == $secret_name) {
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
                                if ($actives->name == $secret_name) {
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
                            DB::table('client_tables')
                            ->where('client_id', $userid)
                            ->update([
                                'client_status' => "0",
                                'date_changed' => date("YmdHis")
                            ]);

                            // log file capture error
                            // read the data 
                            $myfile = fopen(public_path("/logs/log.txt"), "r") or die("Unable to open file!");
                            $file_sizes = filesize(public_path("/logs/log.txt")) > 0?filesize(public_path("/logs/log.txt")):8190;
                            $existing_txt = fread($myfile,$file_sizes);
                            // return $existing_txt;
                            $myfile = fopen(public_path("/logs/log.txt"), "w") or die("Unable to open file!");
                            $date = date("dS M Y (H:i:sa)");
                            $txt = $date.":Client (".$client_data[0]->client_name.") deactivated by ".(session('Usernames') ? session('Usernames'):"System")."\n".$existing_txt;
                            // return $txt;
                            fwrite($myfile, $txt);
                            fclose($myfile);
                            // end of log file
                            // session()->flash("success","User has been successfully deactivated");
                            // return redirect("/Clients/View/$userid");
                        }else {
                            // session()->flash("error","Cannot connect to the router!");
                            // return redirect("/Clients/View/$userid");
                        }
                    }
                }else {
                    // session()->flash("error_clients","Client not found!");
                    // return redirect("/Clients");
                }
                /**End Here */
            }
            return "Users has been successfully deactivated";
        }else{
            /**Starts here */
            $client_data = DB::select("SELECT * FROM `client_tables` WHERE `client_id` = '$userid' AND `deleted` = '0'");
            if (count($client_data) > 0) {
                if ($client_data[0]->assignment == "static") {
                    $router_id = $client_data[0]->router_name;
                    // connect to the router and deactivate the client address
                    $router_data = DB::select("SELECT * FROM `router_tables` WHERE `router_id` = '$router_id' AND `deleted` = '0'");
            
                    // get the ip address and queue list above
                    // get ip
                    // Initiate curl session in a variable (resource)
                    $curl_handle = curl_init();
            
                    $baseUrl = explode(":",url('/'));
                    $local_url = $baseUrl[0].":".$baseUrl[1];
                    $url = "$local_url:81/crontab/getIpaddress.php?r_ip=true&r_id=".$router_id;
            
                    // Set the curl URL option
                    curl_setopt($curl_handle, CURLOPT_URL, $url);
            
                    // This option will return data as a string instead of direct output
                    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
            
                    // Execute curl & store data in a variable
                    $curl_data = curl_exec($curl_handle);
            
                    curl_close($curl_handle);
            
                    // Decode JSON into PHP array
                    $router_ip_addresses = json_decode($curl_data);
            
            
                    // create the router os api
                    $API = new routeros_api();
                    $API->debug = false;
                    // create connection
            
                    if ($API->connect($router_data[0]->router_ipaddr,$router_data[0]->router_api_username,$router_data[0]->router_api_password,$router_data[0]->router_api_port)) {
                        // connection created deactivate the user
                        $ip_addresses = $router_ip_addresses;
                        // return $ip_addresses;
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
                        // return $ip_id;
                        // deactivate the id
                        if (strlen($ip_id) > 0) {
                            // deactivate
                            $deactivate = $API->comm("/ip/address/set", array(
                                "disabled" => "yes",
                                ".id" => $ip_id
                            ));
                            // update the user data to de-activated
                            DB::table('client_tables')
                            ->where('client_id', $userid)
                            ->update([
                                'client_status' => "0",
                                'date_changed' => date("YmdHis")
                            ]);
        
                            // log file capture error
                            // read the data 
                            $myfile = fopen(public_path("/logs/log.txt"), "r") or die("Unable to open file!");
                            $file_sizes = filesize(public_path("/logs/log.txt")) > 0?filesize(public_path("/logs/log.txt")):8190;
                            $existing_txt = fread($myfile,$file_sizes);
                            // return $existing_txt;
                            $myfile = fopen(public_path("/logs/log.txt"), "w") or die("Unable to open file!");
                            $date = date("dS M Y (H:i:sa)");
                            $txt = $date.":Client (".$client_data[0]->client_name.") deactivated by ".(session('Usernames') ? session('Usernames'):"System")."\n".$existing_txt;
                            // return $txt;
                            fwrite($myfile, $txt);
                            fclose($myfile);
                            // end of log file
                            // session()->flash("success","User has been successfully deactivated");
                            // return redirect("/Clients/View/$userid");
                        }else {
                            // session()->flash("error","The user ip address not found in the router address list");
                            // return redirect("/Clients/View/$userid");
                        }
                    }else {
                        // session()->flash("error","Cannot connect to the router!");
                        // return redirect("/Clients/View/$userid");
                    }
                }elseif ($client_data[0]->assignment == "pppoe") {
                    // disable the client secret and remove the client from active connections
                    $router_id = $client_data[0]->router_name;
                    // connect to the router and deactivate the client address
                    $router_data = DB::select("SELECT * FROM `router_tables` WHERE `router_id` = '$router_id' AND `deleted` = '0'");
            
                    // get the ip address and queue list above
                    // get secrets
                    // Initiate curl session in a variable (resource)
                    $curl_handle = curl_init();
            
                    $baseUrl = explode(":",url('/'));
                    $local_url = $baseUrl[0].":".$baseUrl[1];
                    $url = "$local_url:81/crontab/getIpaddress.php?r_ppoe_secrets=true&r_id=".$router_id;
            
                    // Set the curl URL option
                    curl_setopt($curl_handle, CURLOPT_URL, $url);
            
                    // This option will return data as a string instead of direct output
                    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
            
                    // Execute curl & store data in a variable
                    $curl_data = curl_exec($curl_handle);
            
                    curl_close($curl_handle);
            
                    // Decode JSON into PHP array
                    $router_secrets = json_decode($curl_data);
                    // return $router_secrets;

                    // get the active connection
                    // Initiate curl session in a variable (resource)
                    $curl_handle = curl_init();
            
                    $baseUrl = explode(":",url('/'));
                    $local_url = $baseUrl[0].":".$baseUrl[1];
                    $url = "$local_url:81/crontab/getIpaddress.php?r_active_secrets=true&r_id=".$router_id;
            
                    // Set the curl URL option
                    curl_setopt($curl_handle, CURLOPT_URL, $url);
            
                    // This option will return data as a string instead of direct output
                    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
            
                    // Execute curl & store data in a variable
                    $curl_data = curl_exec($curl_handle);
            
                    curl_close($curl_handle);
            
                    // Decode JSON into PHP array
                    $active_connections = json_decode($curl_data);
                    // return $active_connections;
            
                    // client secret name 
                    $secret_name = $client_data[0]->client_secret;
                    // create the router os api
                    $API = new routeros_api();
                    $API->debug = false;
                    if ($API->connect($router_data[0]->router_ipaddr,$router_data[0]->router_api_username,$router_data[0]->router_api_password,$router_data[0]->router_api_port)){
                        // loop through the secrets get the id and use it to disable the secret
                        $secret_id = "0";
                        for ($indexes=0; $indexes < count($router_secrets); $indexes++) { 
                            $secrets = $router_secrets[$indexes];
                            if ($secrets->name == $secret_name) {
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
                            if ($actives->name == $secret_name) {
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
                        DB::table('client_tables')
                        ->where('client_id', $userid)
                        ->update([
                            'client_status' => "0",
                            'date_changed' => date("YmdHis")
                        ]);

                        // log file capture error
                        // read the data 
                        $myfile = fopen(public_path("/logs/log.txt"), "r") or die("Unable to open file!");
                        $file_sizes = filesize(public_path("/logs/log.txt")) > 0?filesize(public_path("/logs/log.txt")):8190;
                        $existing_txt = fread($myfile,$file_sizes);
                        // return $existing_txt;
                        $myfile = fopen(public_path("/logs/log.txt"), "w") or die("Unable to open file!");
                        $date = date("dS M Y (H:i:sa)");
                        $txt = $date.":Client (".$client_data[0]->client_name.") deactivated by ".(session('Usernames') ? session('Usernames'):"System")."\n".$existing_txt;
                        // return $txt;
                        fwrite($myfile, $txt);
                        fclose($myfile);
                        // end of log file
                        // session()->flash("success","User has been successfully deactivated");
                        // return redirect("/Clients/View/$userid");
                    }else {
                        // session()->flash("error","Cannot connect to the router!");
                        // return redirect("/Clients/View/$userid");
                    }
                }
            }else {
                // session()->flash("error_clients","Client not found!");
                // return redirect("/Clients");
            }
            /**Ends Here */
        }
    }
    // activate the user
    function activate($userid){
        /*****starts here */
        // get the user router and update the setting
        $client_data = DB::select("SELECT * FROM `client_tables` WHERE `client_id` = '$userid' AND `deleted` = '0'");
>>>>>>> origin/main
        if (count($client_data) > 0) {
            if ($client_data[0]->assignment == "static") {
                $router_id = $client_data[0]->router_name;
                // connect to the router and deactivate the client address
<<<<<<< HEAD
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
                $sstp_value = $this->getSSTPAddress();
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
=======
                $router_data = DB::select("SELECT * FROM `router_tables` WHERE `router_id` = '$router_id' AND `deleted` = '0'");
        
                // get the ip address and queue list above
                // get ip
                // Initiate curl session in a variable (resource)
                $curl_handle = curl_init();
        
                $baseUrl = explode(":",url('/'));
                $local_url = $baseUrl[0].":".$baseUrl[1];
                $url = "$local_url:81/crontab/getIpaddress.php?r_ip=true&r_id=".$router_id;
        
                // Set the curl URL option
                curl_setopt($curl_handle, CURLOPT_URL, $url);
        
                // This option will return data as a string instead of direct output
                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
        
                // Execute curl & store data in a variable
                $curl_data = curl_exec($curl_handle);
        
                curl_close($curl_handle);
        
                // Decode JSON into PHP array
                $router_ip_addresses = json_decode($curl_data);
        
>>>>>>> origin/main
        
                // create the router os api
                $API = new routeros_api();
                $API->debug = false;
                // create connection
        
<<<<<<< HEAD
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

=======
                if ($API->connect($router_data[0]->router_ipaddr,$router_data[0]->router_api_username,$router_data[0]->router_api_password,$router_data[0]->router_api_port)) {
                    // connection created deactivate the user
                    $ip_addresses = $router_ip_addresses;
                    // return $ip_addresses;
>>>>>>> origin/main
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
<<<<<<< HEAD
                    // return $ip_addresses;
=======
                    // return $ip_id;
>>>>>>> origin/main
                    // deactivate the id
                    if (strlen($ip_id) > 0) {
                        // deactivate
                        $deactivate = $API->comm("/ip/address/set", array(
<<<<<<< HEAD
                            "disabled" => "false",
                            ".id" => $ip_id
                        ));

                        // update the user data to de-activated
                        DB::connection("mysql2")->table('client_tables')
=======
                            "disabled" => "no",
                            ".id" => $ip_id
                        ));
                        // update the user data to de-activated
                        DB::table('client_tables')
>>>>>>> origin/main
                        ->where('client_id', $userid)
                        ->update([
                            'client_status' => "1",
                            'date_changed' => date("YmdHis")
                        ]);
<<<<<<< HEAD

                        // log message
                        $txt = ":Client (".$client_data[0]->client_name.") activated by ".(session('Usernames') ? session('Usernames'):"System");
                        $this->log($txt);
                        // end of log file
                        if (session('Usernames')) {
                            session()->flash("success","User has been successfully activated");
                            return redirect("/Clients/View/$userid");
                        }else{
                            return ["success" => true, "message" => $client_data[0]->client_name." updated successfully!"];
                        }
                    }else {
                        if (session('Usernames')) {
                            session()->flash("error","The user ip address not found in the router address list");
                            return redirect("/Clients/View/$userid");
                        }else{
                            return ["success" => false, "message" => $client_data[0]->client_name." : The user ip address not found in the router address list!"];
                        }
                    }
                }else {
                    if (session('Usernames')) {
                        session()->flash("error","Cannot connect to the router!");
                        return redirect("/Clients/View/$userid");
                    }else{
                        return ["success" => false, "message" => $client_data[0]->client_name." : Cannot connect to the router!"];
                    }
=======
    
                        // log file capture error
                        // read the data 
                        $myfile = fopen(public_path("/logs/log.txt"), "r") or die("Unable to open file!");
                        $file_sizes = filesize(public_path("/logs/log.txt")) > 0?filesize(public_path("/logs/log.txt")):8190;
                        $existing_txt = fread($myfile,$file_sizes);
                        // return $existing_txt;
                        $myfile = fopen(public_path("/logs/log.txt"), "w") or die("Unable to open file!");
                        $date = date("dS M Y (H:i:sa)");
                        $txt = $date.":Client (".$client_data[0]->client_name.") activated by ".(session('Usernames') ? session('Usernames'):"System")."\n".$existing_txt;
                        // return $txt;
                        fwrite($myfile, $txt);
                        fclose($myfile);
                        // end of log file
                        session()->flash("success","User has been successfully activated");
                        return redirect("/Clients/View/$userid");
                    }else {
                        session()->flash("error","The user ip address not found in the router address list");
                        return redirect("/Clients/View/$userid");
                    }
                }else {
                    session()->flash("error","Cannot connect to the router!");
                    return redirect("/Clients/View/$userid");
>>>>>>> origin/main
                }
            }elseif ($client_data[0]->assignment == "pppoe") {
                // disable the client secret and remove the client from active connections
                $router_id = $client_data[0]->router_name;
                // connect to the router and deactivate the client address
<<<<<<< HEAD
                $router_data = DB::connection("mysql2")->select("SELECT * FROM `remote_routers` WHERE `router_id` = '$router_id' AND `deleted` = '0'");
                
                // router value
                if (count($router_data) == 0) {
                    if (session('Usernames')) {
                        $error = "Router connected to client not found!";
                        session()->flash("network_presence",$error);
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
                $sstp_value = $this->getSSTPAddress();
                if ($sstp_value == null) {
                    if (session('Usernames')) {
                        $error = "The SSTP server is not set, Contact your administrator!";
                        session()->flash("network_presence",$error);
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
                        session()->flash("network_presence",$error);
                        return redirect(url()->previous());
                    }else{
                        return ["success" => false, "message" => $client_data[0]->client_name." : Your router is not active, Restart it and try again!"];
                    }
                }
=======
                $router_data = DB::select("SELECT * FROM `router_tables` WHERE `router_id` = '$router_id' AND `deleted` = '0'");
        
                // get the ip address and queue list above
                // get secrets
                // Initiate curl session in a variable (resource)
                $curl_handle = curl_init();
        
                $baseUrl = explode(":",url('/'));
                $local_url = $baseUrl[0].":".$baseUrl[1];
                $url = "$local_url:81/crontab/getIpaddress.php?r_ppoe_secrets=true&r_id=".$router_id;
        
                // Set the curl URL option
                curl_setopt($curl_handle, CURLOPT_URL, $url);
        
                // This option will return data as a string instead of direct output
                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
        
                // Execute curl & store data in a variable
                $curl_data = curl_exec($curl_handle);
        
                curl_close($curl_handle);
        
                // Decode JSON into PHP array
                $router_secrets = json_decode($curl_data);
                // return $router_secrets;
>>>>>>> origin/main
        
                // client secret name 
                $secret_name = $client_data[0]->client_secret;
                // create the router os api
                $API = new routeros_api();
                $API->debug = false;
<<<<<<< HEAD
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
=======
                if ($API->connect($router_data[0]->router_ipaddr,$router_data[0]->router_api_username,$router_data[0]->router_api_password,$router_data[0]->router_api_port)){
                    // loop through the secrets get the id and use it to disable the secret
                    $secret_id = "0";
                    for ($indexes=0; $indexes < count($router_secrets); $indexes++) { 
                        $secrets = $router_secrets[$indexes];
                        if ($secrets->name == $secret_name) {
>>>>>>> origin/main
                            // loop through and pull the id we will use to disable the secret
                            foreach ($secrets as $key => $value) {
                                if ($key == ".id") {
                                    $secret_id = $value;
                                    break;
                                }
                            }
                        }
                    }
<<<<<<< HEAD

=======
>>>>>>> origin/main
                    $API->comm("/ppp/secret/set", array(
                        "disabled" => "false",
                        ".id" => $secret_id
                    ));

                    // uodate the database
                    // update the user data to de-activated
<<<<<<< HEAD
                    DB::connection("mysql2")->table('client_tables')
=======
                    DB::table('client_tables')
>>>>>>> origin/main
                    ->where('client_id', $userid)
                    ->update([
                        'client_status' => "1",
                        'date_changed' => date("YmdHis")
                    ]);

<<<<<<< HEAD
                    // log message
                    $txt = ":Client (".$client_data[0]->client_name.") activated by ".(session('Usernames') ? session('Usernames'):"System");
                    $this->log($txt);
                    // end of log file
                    if (session('Usernames')) {
                        session()->flash("success","User has been successfully activated");
                        return redirect("/Clients/View/$userid");
                    }else{
                        return ["success" => true, "message" => $client_data[0]->client_name." : User has been successfully activated!"];
                    }
                }else {
                    if (session('Usernames')) {
                        session()->flash("error","Cannot connect to the router!");
                        return redirect("/Clients/View/$userid");
                    }else{
                        return ["success" => false, "message" => $client_data[0]->client_name." : Cannot connect to the router!"];
                    }
                }
            }
        }else {
            if (session('Usernames')) {
                session()->flash("error_clients","Client not found!");
                return redirect("/Clients");
            }else{
                return "";
            }
        }
        /*****ends here */
    }

    function dePay($userid){
        // change db
        $change_db = new login();
        $change_db->change_db();

        // update the payment information
        DB::connection("mysql2")->table('client_tables')
=======
                    // log file capture error
                    // read the data 
                    $myfile = fopen(public_path("/logs/log.txt"), "r") or die("Unable to open file!");
                    $file_sizes = filesize(public_path("/logs/log.txt")) > 0?filesize(public_path("/logs/log.txt")):8190;
                    $existing_txt = fread($myfile,$file_sizes);
                    // return $existing_txt;
                    $myfile = fopen(public_path("/logs/log.txt"), "w") or die("Unable to open file!");
                    $date = date("dS M Y (H:i:sa)");
                    $txt = $date.":Client (".$client_data[0]->client_name.") activated by ".(session('Usernames') ? session('Usernames'):"System")."\n".$existing_txt;
                    // return $txt;
                    fwrite($myfile, $txt);
                    fclose($myfile);
                    // end of log file
                    session()->flash("success","User has been successfully activated");
                    return redirect("/Clients/View/$userid");
                }else {
                    session()->flash("error","Cannot connect to the router!");
                    return redirect("/Clients/View/$userid");
                }
            }
        }else {
            session()->flash("error_clients","Client not found!");
            return redirect("/Clients");
        }
        /*****ends here */
    }
    // activate the user
    function activate2($userid){
        /**Start here */
        // get the user router and update the setting
        $client_data = DB::select("SELECT * FROM `client_tables` WHERE `client_id` = '$userid' AND `deleted` = '0'");
        if (count($client_data) > 0) {
            if ($client_data[0]->assignment == "static") {
                $router_id = $client_data[0]->router_name;
                // connect to the router and deactivate the client address
                $router_data = DB::select("SELECT * FROM `router_tables` WHERE `router_id` = '$router_id' AND `deleted` = '0'");
        
                // get the ip address and queue list above
                // get ip
                // Initiate curl session in a variable (resource)
                $curl_handle = curl_init();
        
                $baseUrl = explode(":",url('/'));
                $local_url = $baseUrl[0].":".$baseUrl[1];
                $url = "$local_url:81/crontab/getIpaddress.php?r_ip=true&r_id=".$router_id;
        
                // Set the curl URL option
                curl_setopt($curl_handle, CURLOPT_URL, $url);
        
                // This option will return data as a string instead of direct output
                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
        
                // Execute curl & store data in a variable
                $curl_data = curl_exec($curl_handle);
        
                curl_close($curl_handle);
        
                // Decode JSON into PHP array
                $router_ip_addresses = json_decode($curl_data);
        
        
                // create the router os api
                $API = new routeros_api();
                $API->debug = false;
                // create connection
        
                if ($API->connect($router_data[0]->router_ipaddr,$router_data[0]->router_api_username,$router_data[0]->router_api_password,$router_data[0]->router_api_port)) {
                    // connection created deactivate the user
                    $ip_addresses = $router_ip_addresses;
                    // return $ip_addresses;
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
                    // return $ip_id;
                    // deactivate the id
                    if (strlen($ip_id) > 0) {
                        // deactivate
                        $deactivate = $API->comm("/ip/address/set", array(
                            "disabled" => "no",
                            ".id" => $ip_id
                        ));
                        // update the user data to de-activated
                        DB::table('client_tables')
                        ->where('client_id', $userid)
                        ->update([
                            'client_status' => "1",
                            'date_changed' => date("YmdHis")
                        ]);
    
                        // log file capture error
                        // read the data 
                        $myfile = fopen(public_path("/logs/log.txt"), "r") or die("Unable to open file!");
                        $file_sizes = filesize(public_path("/logs/log.txt")) > 0?filesize(public_path("/logs/log.txt")):8190;
                        $existing_txt = fread($myfile,$file_sizes);
                        // return $existing_txt;
                        $myfile = fopen(public_path("/logs/log.txt"), "w") or die("Unable to open file!");
                        $date = date("dS M Y (H:i:sa)");
                        $txt = $date.":Client (".$client_data[0]->client_name.") activated by ".(session('Usernames') ? session('Usernames'):"System")."\n".$existing_txt;
                        // return $txt;
                        fwrite($myfile, $txt);
                        fclose($myfile);
                        // end of log file
                        // session()->flash("success","User has been successfully activated");
                        // return redirect("/Clients/View/$userid");
                    }else {
                        // session()->flash("error","The user ip address not found in the router address list");
                        // return redirect("/Clients/View/$userid");
                    }
                }else {
                    // session()->flash("error","Cannot connect to the router!");
                    // return redirect("/Clients/View/$userid");
                }
            }elseif ($client_data[0]->assignment == "pppoe") {
                // disable the client secret and remove the client from active connections
                $router_id = $client_data[0]->router_name;
                // connect to the router and deactivate the client address
                $router_data = DB::select("SELECT * FROM `router_tables` WHERE `router_id` = '$router_id' AND `deleted` = '0'");
        
                // get the ip address and queue list above
                // get secrets
                // Initiate curl session in a variable (resource)
                $curl_handle = curl_init();
        
                $baseUrl = explode(":",url('/'));
                $local_url = $baseUrl[0].":".$baseUrl[1];
                $url = "$local_url:81/crontab/getIpaddress.php?r_ppoe_secrets=true&r_id=".$router_id;
        
                // Set the curl URL option
                curl_setopt($curl_handle, CURLOPT_URL, $url);
        
                // This option will return data as a string instead of direct output
                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
        
                // Execute curl & store data in a variable
                $curl_data = curl_exec($curl_handle);
        
                curl_close($curl_handle);
        
                // Decode JSON into PHP array
                $router_secrets = json_decode($curl_data);
                // return $router_secrets;
        
                // client secret name 
                $secret_name = $client_data[0]->client_secret;
                // create the router os api
                $API = new routeros_api();
                $API->debug = false;
                if ($API->connect($router_data[0]->router_ipaddr,$router_data[0]->router_api_username,$router_data[0]->router_api_password,$router_data[0]->router_api_port)){
                    // loop through the secrets get the id and use it to disable the secret
                    $secret_id = "0";
                    for ($indexes=0; $indexes < count($router_secrets); $indexes++) { 
                        $secrets = $router_secrets[$indexes];
                        if ($secrets->name == $secret_name) {
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
                    DB::table('client_tables')
                    ->where('client_id', $userid)
                    ->update([
                        'client_status' => "1",
                        'date_changed' => date("YmdHis")
                    ]);

                    // log file capture error
                    // read the data 
                    $myfile = fopen(public_path("/logs/log.txt"), "r") or die("Unable to open file!");
                    $file_sizes = filesize(public_path("/logs/log.txt")) > 0?filesize(public_path("/logs/log.txt")):8190;
                    $existing_txt = fread($myfile,$file_sizes);
                    // return $existing_txt;
                    $myfile = fopen(public_path("/logs/log.txt"), "w") or die("Unable to open file!");
                    $date = date("dS M Y (H:i:sa)");
                    $txt = $date.":Client (".$client_data[0]->client_name.") activated by ".(session('Usernames') ? session('Usernames'):"System")."\n".$existing_txt;
                    // return $txt;
                    fwrite($myfile, $txt);
                    fclose($myfile);
                    // end of log file
                    // session()->flash("success","User has been successfully activated");
                    // return redirect("/Clients/View/$userid");
                }else {
                    // session()->flash("error","Cannot connect to the router!");
                    // return redirect("/Clients/View/$userid");
                }
            }
        }else {
            // session()->flash("error_clients","Client not found!");
            // return redirect("/Clients");
        }
        /**End here */
    }

    function dePay($userid){
        // update the payment information
        DB::table('client_tables')
>>>>>>> origin/main
        ->where('client_id', $userid)
        ->update([
            'payments_status' => "0",
            'date_changed' => date("YmdHis")
        ]);
<<<<<<< HEAD
        $client = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `client_id` = '$userid' AND `deleted` = '0'");
        $client_name = $client[0]->client_name;

        // log message
        $txt = ":Client ( $client_name ) pay status has been changed to In-active by ".session('Usernames');
        $this->log($txt);
=======
        $client = DB::select("SELECT * FROM `client_tables` WHERE `client_id` = '$userid' AND `deleted` = '0'");
        $client_name = $client[0]->client_name;
        
        // log file capture error
        // read the data 
        $myfile = fopen(public_path("/logs/log.txt"), "r") or die("Unable to open file!");
        $file_sizes = filesize(public_path("/logs/log.txt")) > 0?filesize(public_path("/logs/log.txt")):8190;
        $existing_txt = fread($myfile,$file_sizes);
        // return $existing_txt;
        $myfile = fopen(public_path("/logs/log.txt"), "w") or die("Unable to open file!");
        $date = date("dS M Y (H:i:sa)");
        $txt = $date.":Client ( $client_name ) pay status has been changed to In-active by ".session('Usernames').""."!\n".$existing_txt;
        // return $txt;
        fwrite($myfile, $txt);
        fclose($myfile);
>>>>>>> origin/main
        // end of log file
        session()->flash("success","User payment automation has been successfully de-activated");
        return redirect("/Clients/View/$userid");
    }
    function actPay($userid){
<<<<<<< HEAD
        // change db
        $change_db = new login();
        $change_db->change_db();

        // update the payment information
        DB::connection("mysql2")->table('client_tables')
=======
        // update the payment information
        DB::table('client_tables')
>>>>>>> origin/main
        ->where('client_id', $userid)
        ->update([
            'payments_status' => "1",
            'date_changed' => date("YmdHis")
        ]);
<<<<<<< HEAD
        $client = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `client_id` = '$userid' AND `deleted` = '0'");
        $client_name = $client[0]->client_name;

        // log message
        $txt = ":Client ( $client_name ) pay status has been changed to active by ".session('Usernames');
        $this->log($txt);
=======
        $client = DB::select("SELECT * FROM `client_tables` WHERE `client_id` = '$userid' AND `deleted` = '0'");
        $client_name = $client[0]->client_name;
        
        // log file capture error
        // read the data 
        $myfile = fopen(public_path("/logs/log.txt"), "r") or die("Unable to open file!");
        $file_sizes = filesize(public_path("/logs/log.txt")) > 0?filesize(public_path("/logs/log.txt")):8190;
        $existing_txt = fread($myfile,$file_sizes);
        // return $existing_txt;
        $myfile = fopen(public_path("/logs/log.txt"), "w") or die("Unable to open file!");
        $date = date("dS M Y (H:i:sa)");
        $txt = $date.":Client ( $client_name ) pay status has been changed to active by ".session('Usernames').""."!\n".$existing_txt;
        // return $txt;
        fwrite($myfile, $txt);
        fclose($myfile);
>>>>>>> origin/main
        // end of log file
        session()->flash("success","User payment automation has been successfully Activated");
        return redirect("/Clients/View/$userid");
    }
    // get the router ip addresses
    function getIpaddresses($router_id){
<<<<<<< HEAD
        // change db
        $change_db = new login();
        $change_db->change_db();

        // get router information
        $router_data = DB::connection("mysql2")->select("SELECT * FROM `router_tables` WHERE `router_id` = '$router_id' AND `deleted` = '0'");
=======
        // get router information
        $router_data = DB::select("SELECT * FROM `router_tables` WHERE `router_id` = '$router_id' AND `deleted` = '0'");
>>>>>>> origin/main
        $router_ipaddr = $router_data[0]->router_ipaddr;
        $router_password = $router_data[0]->router_api_password;
        $router_api_port = $router_data[0]->router_api_port;
        $router_username = $router_data[0]->router_api_username;

        // connect to the router and get the router details
        $API = new routeros_api();
        $API->debug = false;
        if ($API->connect($router_ipaddr,$router_username,$router_password,$router_api_port)) {
            // $API->write("/ip/address/print");
            // $data = $API->read(true);
            // $router_ip = $data;//$API->parse_response($data);
            // return $router_ip;

            // Initiate curl session in a variable (resource)
            $curl_handle = curl_init();

            $baseUrl = explode(":",url('/'));
            $local_url = $baseUrl[0].":".$baseUrl[1];
            $url = "$local_url:81/crontab/getIpaddress.php?r_ip=true&r_id=".$router_id;

            // Set the curl URL option
            curl_setopt($curl_handle, CURLOPT_URL, $url);

            // This option will return data as a string instead of direct output
            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);

            // Execute curl & store data in a variable
            $curl_data = curl_exec($curl_handle);

            // Decode JSON into PHP array
            $response_data = json_decode($curl_data);
            curl_close($curl_handle);
            // return $curl_data;
            return $response_data;

        }else {
            return "No data to display!";
        }
    }
	function get_sms(){
<<<<<<< HEAD
        // change db
        $change_db = new login();
        $change_db->change_db();

        $data = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `keyword` = 'Messages' AND `deleted` = '0'");
=======
        $data = DB::select("SELECT * FROM `settings` WHERE `keyword` = 'Messages' AND `deleted` = '0'");
>>>>>>> origin/main
        return json_decode($data[0]->value);
	}
    function syncclient(){
        // Initiate curl session in a variable (resource)
        $curl_handle = curl_init();

        $baseUrl = explode(":",url('/'));
        $local_url = $baseUrl[0].":".$baseUrl[1];
        $url = "$local_url:81/crontab/syncclients.php";

        // Set the curl URL option
        curl_setopt($curl_handle, CURLOPT_URL, $url);

        // This option will return data as a string instead of direct output
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);

        // Execute curl & store data in a variable
        $curl_data = curl_exec($curl_handle);

        // Decode JSON into PHP array
        $response_data = json_decode($curl_data);
        curl_close($curl_handle);
        // return $curl_data;
<<<<<<< HEAD

        // log message
        $txt = ":Clients data has been synced by ".session('Usernames');
        $this->log($txt);
=======
        
        // log file capture error
        // read the data 
        $myfile = fopen(public_path("/logs/log.txt"), "r") or die("Unable to open file!");
        $file_sizes = filesize(public_path("/logs/log.txt")) > 0?filesize(public_path("/logs/log.txt")):8190;
        $existing_txt = fread($myfile,$file_sizes);
        // return $existing_txt;
        $myfile = fopen(public_path("/logs/log.txt"), "w") or die("Unable to open file!");
        $date = date("dS M Y (H:i:sa)");
        $txt = $date.":Clients data has been synced by ".session('Usernames').""."!\n".$existing_txt;
        // return $txt;
        fwrite($myfile, $txt);
        fclose($myfile);
>>>>>>> origin/main
        // end of log file
        session()->flash("success","Syncing done successfully!");
        return redirect("/Clients");
    }
    function synctrans(){
        // Initiate curl session in a variable (resource)
        $curl_handle = curl_init();

        $baseUrl = explode(":",url('/'));
        $local_url = $baseUrl[0].":".$baseUrl[1];
        $url = "$local_url:81/crontab/syncTransactions.php";

        // Set the curl URL option
        curl_setopt($curl_handle, CURLOPT_URL, $url);

        // This option will return data as a string instead of direct output
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);

        // Execute curl & store data in a variable
        $curl_data = curl_exec($curl_handle);

        // Decode JSON into PHP array
        $response_data = json_decode($curl_data);
        curl_close($curl_handle);
<<<<<<< HEAD

        // log message
        $txt = ":Transaction data has been synced by ".session('Usernames')."!";
        $this->log($txt);
=======
        // return $curl_data;
        
        // log file capture error
        // read the data 
        $myfile = fopen(public_path("/logs/log.txt"), "r") or die("Unable to open file!");
        $file_sizes = filesize(public_path("/logs/log.txt")) > 0?filesize(public_path("/logs/log.txt")):8190;
        $existing_txt = fread($myfile,$file_sizes);
        // return $existing_txt;
        $myfile = fopen(public_path("/logs/log.txt"), "w") or die("Unable to open file!");
        $date = date("dS M Y (H:i:sa)");
        $txt = $date.":Transaction data has been synced by ".session('Usernames').""."!\n".$existing_txt;
        // return $txt;
        fwrite($myfile, $txt);
        fclose($myfile);
>>>>>>> origin/main
        // end of log file
        session()->flash("success","Syncing done successfully!");
        return redirect("/Transactions");
    }
	function message_content($data,$user_id,$trans_amount,$freeze_days = null,$freeze_date = null,$future_freeze_date = null) {
<<<<<<< HEAD
        $client_data = DB::connection("mysql2")->select("SELECT * FROM `client_tables` WHERE `client_id` = '$user_id' AND `deleted` = '0'");
=======
        $client_data = DB::select("SELECT * FROM `client_tables` WHERE `client_id` = '$user_id' AND `deleted` = '0'");
>>>>>>> origin/main
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
}
