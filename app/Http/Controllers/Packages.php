<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

date_default_timezone_set('Africa/Nairobi');

class Packages extends Controller
{
    //
    function get_packages(){
        $all_packages = DB::select("SELECT * FROM `packages`");
        // return $all_packages;
        foreach ($all_packages as $key => $value) {
            $all_packages[$key]->amount_paid = "Kes ".number_format($value->amount_paid);
        }
        return view("Packages.index", ["packages" => $all_packages]);
    }

    // new package
    function new_packages(){
        return view("Packages.new");
    }

    // process package
    function process_new_package(Request $request){
        // return $request;
        $package_name = $request->input("package_name");
        $package_amount = $request->input("package_amount");
        $period_metric = $request->input("period_metric");
        $period_number = $request->input("period_number");
        $free_trial_period_metric = $request->input("free_trial_period_metric");
        $free_trial_period_number = $request->input("free_trial_period_number");
        $today = date("YmdHis");
        
        // insert
        $insert = DB::insert("INSERT INTO `packages` (`package_name`, `amount_paid`,`free_trial_period`,`package_period`,`date_created`,`date_updated`) VALUES (?,?,?,?,?,?)",
                    [$package_name, $package_amount,$free_trial_period_number." ".$free_trial_period_metric, $period_number." ".$period_metric, $today, $today]);

        session()->flash("success","\"".$package_name."\" has been successfully registered!");
        return redirect(route("NewPackages"));
    }

    // update package
    function update_package($package_id, Request $request){
        $date_updated = date("YmdHis");
        // return $request;
        $update = DB::update("UPDATE `packages` SET `package_name` = ?, `amount_paid` = ?, `free_trial_period` = ?, `package_period` = ?, `date_updated` = ? WHERE `package_id` = ?",
                            [$request->package_name,$request->package_amount,$request->free_trial_period_number." ".$request->free_trial_period_metric,$request->period_number." ".$request->period_metric,$date_updated,$package_id]);
        session()->flash("success","Package \"".ucwords(strtolower($request->package_name))."\" has been updated successfully!");
        return redirect(route("ViewPackage",$package_id));
    }

    // view packages
    function view_package($package_id){
        $packages = DB::select("SELECT * FROM `packages` WHERE `package_id` = ?",[$package_id]);
        if (count($packages) == 0) {
            session()->flash("error","Invalid package!");
            return redirect(route("Packages"));
        }

        // proceed and get the package details and process
        $package_details = $packages[0];

        // split the free trial period and the package period
        $free_trial_period = $package_details->free_trial_period;
        $package_period = $package_details->package_period;
        
        // get the free trial period and month
        $free_trial_metric = explode(" ", $free_trial_period)[1] != null ? explode(" ", $free_trial_period)[1] : 0;;
        $free_trial_number = explode(" ", $free_trial_period)[0] != null ? explode(" ", $free_trial_period)[0] : 0;

        // package period
        $package_period_metric = explode(" ", $package_period)[1] != null ? explode(" ", $package_period)[1] : "";
        $package_period_number = explode(" ", $package_period)[0] != null ? explode(" ", $package_period)[0] : "";

        // set the new package details
        $package_details->free_trial_metric = $free_trial_metric;
        $package_details->free_trial_number = $free_trial_number;
        $package_details->package_period_metric = $package_period_metric;
        $package_details->package_period_number = $package_period_number;

        // return the package details
        return view("Packages.view",["package_details" => $package_details]);
    }

    // deactivate
    function deactivate_package($deactivate_package){
        $update = DB::update("UPDATE `packages` SET `status` = '0' WHERE `package_id` = ?",[$deactivate_package]);
        session()->flash("success","Package status has been changed successfully!");
        return redirect(route("ViewPackage",[$deactivate_package]));
    }

    // deactivate
    function activate_package($activate_package){
        $update = DB::update("UPDATE `packages` SET `status` = '1' WHERE `package_id` = ?",[$activate_package]);
        session()->flash("success","Package status has been changed successfully!");
        return redirect(route("ViewPackage",[$activate_package]));
    }
}
