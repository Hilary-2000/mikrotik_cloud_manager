<?php

namespace App\Http\Controllers;

use App\Models\admin_table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

date_default_timezone_set('Africa/Nairobi');
class Organization_Admin extends Controller
{
    // view organization details

    function view_organization_admin($organization_id){
        // organization id
        $organization_details = DB::select("SELECT * FROM `organizations` WHERE `organization_id` = ?",[$organization_id]);
        if (count($organization_details) == 0) {
            session()->flash("error", "Invalid organization!");
            return redirect(route("Organizations"));
        }

        // get the organization id
        $admin_data = DB::connection("mysql")->select("SELECT * FROM `admin_tables` WHERE `organization_id` = ?",[$organization_id]);
        
        $username = [];
        $date = [];
        foreach ($admin_data as $key => $value) {
            // get the admins username
            array_push($username, $value->admin_username);

            $date_data = $value->last_time_login;
            if (strlen($date_data) > 0) {
                $year = substr($date_data,0,4);
                $month = substr($date_data,4,2);
                $day = substr($date_data,6,2);
                $hour = substr($date_data,8,2);
                $minute = substr($date_data,10,2);
                $second = substr($date_data,12,2);
                $d = mktime($hour, $minute, $second, $month, $day, $year);
                $dates2 = date("D dS M-Y  h:i:sa", $d);
                array_push($date,$dates2);
            }else {
                $dates2 = "Not logged in before";
                array_push($date,$dates2);
            }
        }
        return view("Orgarnizations.admin.index",["organization_details" => $organization_details[0], "username" => $username, "admin_data" => $admin_data, "dates" => $date]);
    }

    function add_administrators(Request $req, $organization_id){
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

        // get the values
        $admin_name = $req->input('admin_name');
        $client_address = $req->input('client_address');
        $admin_username = $req->input('admin_username');
        $admin_password = $req->input('admin_password');
        $privileges = $req->input('privileges');

        // get the username if its already used
        $admin_data = DB::select("SELECT * FROM `admin_tables` WHERE `admin_username` = '$admin_username' AND `deleted` = '0'");

        if (count($admin_data) > 0) {
            // return an error showing thet the username has been used
            session()->flash("network_presence","Username provided is already used");
            return redirect(route("view_organization_admin",[$organization_id]));
        }else {
            $admin_table = new admin_table();
            $admin_table->admin_fullname = $admin_name;
            $admin_table->admin_username = $admin_username;
            $admin_table->admin_password = $admin_password;
            $admin_table->contacts = $client_address;
            $admin_table->organization_id = $organization_id;
            $admin_table->user_status = "1";
            $admin_table->priviledges = $privileges;
            $admin_table->save();
                
            $new_client = new Clients();
            $txt = ":Admin ($admin_name) has been added by ( ".session('Usernames')." )"."!";
            $new_client->log($txt);
            session()->flash("success","The administrator has successfully been added.");
            return redirect(route("view_organization_admin",[$organization_id]));
        }

    }

    function view_administrators($organization_id, $admin_id){
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

        $admin_data = DB::select("SELECT * FROM `admin_tables` WHERE `admin_id` = '$admin_id' AND `deleted` = '0'");
        if (count($admin_data) > 0) {
            return view("Orgarnizations.admin.view", ["organization_details" => $organization_details[0], "admin_data" => $admin_data]);
        }else{
            session()->flash("network_presence","In-valid User!");
            return redirect(route("view_organization_admin",[$organization_id]));
        }
    }

    function update_administrators(Request $req, $organization_id){
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
        $admin_id = $req->input('admin_id');
        $privileges = $req->input('privileges');
        $status = $req->input('status');
        // create a model to update the data
        $update = DB::table("admin_tables")->where("admin_id",$admin_id)->update([
            "admin_fullname" => $req->input('admin_name'),
            "admin_username" => $req->input('admin_username'),
            "admin_password" => $req->input('admin_password'),
            "contacts" => $req->input('client_address'),
            "user_status" => $req->input('status'),
            "date_changed" => date("YmdHis"),
            "activated" => $status,
            "priviledges" => $privileges
        ]);
        session()->flash('success',"Administrator data updates successfully!");
        return redirect(route("view_administrators", [$organization_id, $admin_id]));
    }

    function delete_administrators($organization_id, $admin_id){
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

        // get the administrator`s name
        $administrator_detail = DB::select("SELECT * FROM `admin_tables` WHERE `admin_id` = '".$admin_id."'");
        $admin_name = count($administrator_detail) > 0 ? $administrator_detail[0]->admin_fullname : "NULL";
        // delete the user admin and record that as a log
        DB::delete("DELETE FROM `admin_tables` WHERE `admin_id` = '".$admin_id."'");
        session()->flash("success","The administrator \"".$admin_name."\" has been deleted successfully!");

        // 
        $new_client = new Clients();
        $txt = ":The administrator \"".$admin_name."\" has been deleted successfully! by ".session('Usernames')."!";
        $new_client->log($txt);
        return redirect(route("view_organization_admin",[$organization_details[0]->organization_id]));
    }

    function deactivate_admin($organization_id, $admin_id){
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

        // return $admin_id;
        DB::update("UPDATE `admin_tables` SET `activated` = '0', `user_status` = '0' WHERE `admin_id` = ?",[$admin_id]);
        session()->flash("success","The administrator has successfully deactivated.");
        return redirect(route("view_organization_admin",[$organization_details[0]->organization_id]));
    }
}
