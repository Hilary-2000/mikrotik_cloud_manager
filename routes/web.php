<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\login;
use App\Http\Controllers\Clients;
use App\Http\Controllers\Transaction;
use App\Http\Controllers\Router;
use App\Http\Controllers\Sms;
use App\Http\Controllers\admin;
use App\Http\Controllers\Clients_data;
use App\Http\Controllers\export_client;
use App\Http\Controllers\billsms_manager;
use App\Http\Controllers\Dashboard;
use App\Http\Controllers\Expenses;
use App\Http\Controllers\Organization;
use App\Http\Controllers\Organization_Admin;
use App\Http\Controllers\organization_client_transaction;
use App\Http\Controllers\Organization_router;
use App\Http\Controllers\Organization_sms;
use App\Http\Controllers\Packages;
use App\Http\Controllers\Router_Cloud;
use App\Http\Controllers\SharedTables;
use App\Http\Controllers\Transactions;
use Facade\Ignition\Support\Packagist\Package;
use Illuminate\Mail\Transport\Transport;
use Symfony\Component\Mime\Crypto\SMime;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::view("/","mainpage");
Route::get("/", function () {
    return redirect('/Hypbits');
});

// Route::view("/Dashboard","index");
Route::get("/Dashboard",[Dashboard::class,"getDashboard"])->name("Dashboard");
Route::view("/Routers/New","newrouter");
Route::get('/Login', function () {
    if (session('error')) {
        session()->flash("error",session('error'));
    }
    return redirect('/Hypbits');
}) ->name("Login");

// Special for Hypbits
Route::view("/Hypbits","login");
Route::view("/verify","verify");

//login controller router
Route::post("/process_login",[login::class,"processLogin"])->name("process_login");
Route::post("/verifycode",[Login::class,"processVerification"])->name("verify_code");

// Organization Links.
Route::get("/Organizations",[Organization::class,"get_organizations"])->name("Organizations");
Route::get("/Organizations/New",[Organization::class,"new_organizations"])->name("NewOrganizations");
Route::post("/Organization/ProcessNew", [Organization::class,"process_new"])->name("ProcessNewOrganization");
Route::get("/Organization/View/{organization_id}", [Organization::class,"view_organization"])->name("ViewOrganization");
Route::get("/Organization/Deactivate/{organization_id}", [Organization::class,"deactivate_organization"])->name("DeactivateOrganization");
Route::get("/Organization/Activate/{organization_id}", [Organization::class,"activate_organization"])->name("ActivateOrganization");
Route::get("/Organization/Deactivate_Payment_Status/{organization_id}", [Organization::class,"deactivate_payment_status"])->name("Deactivate_Payment_Status");
Route::get("/Organization/Activate_Payment_Status/{organization_id}", [Organization::class,"activate_payment_status"])->name("Activate_Payment_Status");
Route::post("/Organization/UpdateWallet/{organization_id}",[Organization::class,"update_wallet"])->name("UpdateWallet");
Route::post("/Organization/UpdateLenience/{organization_id}",[Organization::class,"update_lenience"])->name("UpdateLenience");
Route::post("/Organization/UpdateDiscount/{organization_id}",[Organization::class,"update_discount"])->name("UpdateDiscount");
Route::post("/Organization/Update/{organization_id}",[Organization::class,"update_organization"])->name("UpdateOrganization");
Route::get("/Organization/ViewClients/{organization_id}",[Organization::class,"view_organization_clients"])->name("viewOrganizationClients");
Route::get("/Organization/ViewClient/{organization_id}/{client_id}",[Organization::class,"view_organization_client"])->name("viewOrganizationClient");
Route::get("/Organization/Activate/{organization_id}/{client_id}" ,[Organization::class,"activate_client"])->name("activate_client");
Route::get("/Organization/Deactivate/{organization_id}/{client_id}" ,[Organization::class,"deactivate_client"])->name("deactivate_client");
Route::get("/Organization/deactivate_payment/{organization_id}/{client_id}",[Organization::class, "deactivate_payment"])->name("deactivate_payment");
Route::get("/Organization/activate_payment/{organization_id}/{client_id}",[Organization::class, "activate_payment"])->name("activate_payment");
Route::get("/Organization/delete/{organization_id}/{client_id}",[Organization::class, "delete_user"])->name("delete_user");
Route::post("/Organization/ChangeExpiry/{organization_id}",[Organization::class,'change_expiry_date'])->name("change_expiry_date");
Route::get("/Organization/deactivate_freeze/{organization_id}/{client_id}",[Organization::class,'deactivate_freeze'])->name("deactivate_freeze");
Route::post("/Organization/SetFreeze/{organization_id}",[Organization::class,'set_freeze_date'])->name("set_freeze_date");
Route::post("/Organization/minimum_pay/{organization_id}/{client_id}",[Organization::class,"set_minimum_pay"])->name("set_minimum_pay");
Route::post("/Organization/change_wallet/{organization_id}/{client_id}",[Organization::class,"change_wallet_balance"])->name("change_wallet_balance");
Route::post("/Organization/UpdateClient/{organization_id}",[Organization::class,"update_client"])->name("update_client");
Route::get("/Organization/get_interfaces/{organization_id}/{router_id}",[Organization::class,'get_router_interface'])->name("get_router_interface");

// statistics
Route::get("/Organization/Client-Statistics/{organization_id}",[Organization::class,'get_clients_statistics'])->name("get_clients_statistics");
Route::post("/Organization/Client-due-demographics/{organization_id}",[Organization::class,'clients_demographics'])->name("clients_demographics");
Route::get("/Organization/Clients/generate_reports/{organization_id}",[Organization::class,"generate_reports"])->name("generate_reports");

// organization view transactions 
Route::get("/Organization/ViewTransaction/{organization_id}",[organization_client_transaction::class,"get_transactions"])->name("get_transactions");
Route::get("/Organization/ViewTransactionDetail/{organization_id}/{transaction_id}",[organization_client_transaction::class,"transaction_details"])->name("transaction_details");
Route::get("/Organization/TransactionStats/{organization_id}",[organization_client_transaction::class,"transaction_statistics"])->name("transaction_statistics");
Route::get("/Organization/TransactionStats/Reports/{organization_id}",[organization_client_transaction::class,"generate_reports"])->name("generate_reports");


// organization routers
Route::get("/Organization/Routers/{organization_id}",[Organization_router::class,"view_routers"])->name("view_routers");
Route::get("/Organization/Router/{organization_id}/{router_id}",[Organization_router::class,"view_router_details"])->name("view_router_details");
Route::post("/Organization/Router/Update/{organization_id}",[Organization_router::class,"updateRouter"])->name("update_organization_router");
Route::get("/Organization/Router/Connect/{organization_id}/{router_id}",[Organization_router::class,"connect_organization_router"])->name("connect_organization_router");
Route::get("/Organization/Routers/Delete/{organization_id}/{routerid}",[Organization_router::class,"delete_router"])->name("delete_router");

// Organization SMS
Route::get("/Organizations/SMS/{organization_id}",[Organization_sms::class,"view_organization_sms"])->name("view_organization_sms");
Route::get("/Organizations/SMS/View/{organization_id}/{sms_id}",[Organization_sms::class,"view_sms"])->name("view_sms");
Route::get("/Organizations/SMS/Deleted/{organization_id}/{sms_id}",[Organization_sms::class,"delete_sms"])->name("delete_sms");
Route::get("/Organizations/SMS/CustomSMS/{organization_id}",[Organization_sms::class,"customize_sms"])->name("customize_sms");
Route::post("/Organizations/SMS/save_custom/{organization_id}",[Organization_sms::class,"save_sms_content"])->name("save_sms_content");
Route::get("/Organizations/SMS/generate_reports_sms/{organization_id}",[Organization_sms::class,"generate_reports_sms"])->name("generate_reports_sms");

// organization administrators
Route::get("/Organizations/Admins/{organization_id}",[Organization_Admin::class, "view_organization_admin"])->name("view_organization_admin");
Route::post("/Organization/Admins/add_administrator/{organization_id}",[Organization_Admin::class,"add_administrators"])->name("add_administrators");
Route::get("/Organization/Admins/view_administrator/{organization_id}/{administrator_id}",[Organization_Admin::class,"view_administrators"])->name("view_administrators");
Route::post("/Organization/Admins/update_administrator/{organization_id}",[Organization_Admin::class,"update_administrators"])->name("update_administrators");
Route::get("/Organization/Admins/delete_administrator/{organization_id}/{administrator_id}",[Organization_Admin::class,"delete_administrators"])->name("delete_administrators");
Route::get("/Organization/Admins/deactivate_administrator/{organization_id}/{administrator_id}",[Organization_Admin::class,"deactivate_admin"])->name("deactivate_admin");



// Packages Link
Route::get("/Packages",[Packages::class,"get_packages"])->name("Packages");
Route::get("/Packages/New",[Packages::class,"new_packages"])->name("NewPackages");
Route::post("/Packages/Save",[Packages::class,"process_new_package"])->name("ProcessNewPackage");
Route::post("/Packages/Update/{package_id}",[Packages::class,"update_package"])->name("UpdatePackage");
Route::get("/Packages/View/{package_id}",[Packages::class,"view_package"])->name("ViewPackage");
Route::get("/Packages/Deactivate/{package_id}",[Packages::class,"deactivate_package"])->name("DeactivatePackage");
Route::get("/Packages/Activate/{package_id}",[Packages::class,"activate_package"])->name("ActivatePackage");


// Transactions
Route::get("/Transactions",[Transactions::class, "display_transaction"])->name("Transactions");
Route::get("Transactions/View/{organization_id}",[Transactions::class,"view_transactions"])->name("view_transactions");
Route::get("/Transactions/Statistics",[Transactions::class,'transaction_statistics']);
Route::get("/Transactions/Assign/{organization_id}/{transaction_id}",[Transactions::class, "assign_transaction"])->name("assign_transaction");
Route::get("/Transactions/ConfirmTransfer/{organization_id}/{transaction_id}",[Transactions::class,"confirm_transfer"])->name("confirm_transfer");
Route::post("/HBS/Transact",[Transactions::class,"mpesa_transactions"])->name("mpesa_transactions");
Route::get("/Transport/Report",[Transactions::class,"generate_reports"])->name("generate_reports");

Route::get("/SMS",[Sms::class,"get_sms"])->name("SMS");
Route::get("/SMS/View/{sms_id}",[Sms::class,"get_sms_data"])->name("view_sms");
Route::get("/SMS/Delete/{sms_id}",[Sms::class,"delete_sms_data"])->name("delete_sms");
Route::get("/SMS/Resend/{sms_id}",[Sms::class,"resend_sms"])->name("resend_sms");
Route::post("/SMS/Send",[Sms::class,"send_sms"])->name("send_sms");







































// save client route
Route::post("addClient",[Clients::class,'processNewClient'])->name("clients.addstatic");
// save client pppoe
Route::post("addClientPppoe",[Clients::class,'processClientPPPoE'])->name("clients.addppoe");
// the clients controller route
Route::get("/Clients",[Clients::class,'getClientData'])->name("myclients");
// get the router information for the new client
Route::get("/Clients/NewStatic",[Clients::class,"getRouterDataClients"]);
Route::get("/Clients/NewPPPoE",[Clients::class,"getRouterDatappoe"])->name("newclient.pppoe");
// get the router interface
Route::get("/router/{routerid}",[Clients::class,"getRouterInterfaces"]);
// get the router profile
Route::get("/routerProfile/{routerid}",[Clients::class,"getRouterProfile"]);
// get the clients information interface
Route::get("/Clients/View/{clientid}",[Clients::class,"getClientInformation"])->name("client.viewinformation");
// incase the user enters an invalid username
Route::get('/Clients/View', function () {
    return redirect('Clients');
});
// get the refferer details
Route::get("/get_refferal/{client_account}",[Clients::class,"getRefferal"]);
// save the refferer data
Route::post("/set_refferal",[Clients::class,"setRefferal"]);
// update clients
Route::post("/updateClients",[Clients::class,'updateClients']);
// update clients expiration
Route::post("/changeExpDate",[Clients::class,'updateExpDate']);
// freeze dates set
Route::post("/set_freeze",[Clients::class,'set_freeze_date']);
// deactivate freeze
Route::get("/Client/deactivate_freeze/{client_id}",[Clients::class,"deactivatefreeze"]);
// activate freeze
Route::get("/Client/activate_freeze/{client_id}",[Clients::class,"activatefreeze"]);
// client syncs
Route::get("/ClientSync",[Clients::class,"syncclient"]);
// sync transactions
Route::get("/TransactionSync",[Clients::class,"synctrans"]);
// change wallet balance
Route::post("/changeWallet",[Clients::class,"changeWalletBal"]);
//export my clients
Route::get("/Clients/Export",[export_client::class,"exportClients"]);
// get detailed router information in order to export
Route::get("/Clients/Export/View/{router_id}",[export_client::class,"router_client_information"]);
// sync client information
Route::get("/Client/epxsync/{client_id}",[export_client::class,"sync_client_router"]);
// export all from the router
Route::get("/Clients/ExportAll/{router_id}",[export_client::class,"exportall"]);
// delete user
Route::get("/delete_user/{user_id}",[Clients::class,'delete_user']);

// add router
Route::post("addRouter",[Clients::class,'addRouter']);


// de-activate and activate user the user
Route::get("/deactivate/{userid}",[Clients::class,"deactivate"]);
Route::get("/deactivate/{userid}/{db_name}",[Clients::class,"deactivate"]);
Route::get("/activate/{userid}",[Clients::class,"activate"]);
Route::get("/activate/{userid}/{db_name}",[Clients::class,"activate"]);

// deactivate and activate the user api
// Route::get("/deactivate_user/{userid}",[Clients::class,"deactivate2"]);
// Route::get("/activate_user/{userid}",[Clients::class,"activate2"]);

// deactivate the user payment status
Route::get("/deactivatePayment/{userid}", [Clients::class,"dePay"]);
Route::get("/activatePayment/{userid}", [Clients::class,"actPay"]);




//TRANSACTIONS SECTION
// Route::get("/Transactions",[Transaction::class,"getTransactions"]);
// Route::get("/Transactions/View/{trans_id}",[Transaction::class,"transDetails"]);
// Route::get("/Assign/Transaction/{trans_id}/Client/{client_id}",[Transaction::class,"assignTransaction"]);
// Route::get("/confirmTransfer/{user_id}/{transaction_id}",[Transaction::class,"confirmTransfer"]);
// Route::post("/Transact",[Transaction::class,"mpesaTransactions"]);
// Route::post("/Validate",[Transaction::class,"verify_client_transaction"]);

// Router section
// Route::get("/Router/View/{routerid}",[Router::class,"getRouterInfor"]);
// Route::get("/Routers/Delete/{routerid}",[Router::class,"deleteRouter"]);
Route::get("/Router/Reboot/{routerid}",[Router_Cloud::class,"reboot"]);

// cloud router
Route::post("/new_cloud_router",[Router_Cloud::class,"save_cloud_router"])->name("newCloudRouter");
Route::get("/Router/View/{router_id}",[Router_Cloud::class,"view_router_details"])->name("view_router_cloud");
Route::get("/Router/Connect/{router_id}",[Router_Cloud::class,"connect_router"])->name("connect_router");
Route::post("/updateRouter",[Router_Cloud::class,"updateRouter"])->name("update_router");
Route::get("/Routers/Delete/{routerid}",[Router_Cloud::class,"deleteRouter"]);

// get the routers information
Route::get("/Routers",[Router_Cloud::class,'getRouterData'])->name("my_routers");

// Sms section
// Route::get("/sms",[Sms::class,"getSms"]);
// Route::get("/sms/View/{smsid}", [Sms::class,"getSMSData"]);
// Route::get("/sms/delete/{smsid}", [Sms::class,"delete"]);
// Route::get("/sms/compose",[Sms::class,"compose"]);
// Route::post("/sendsms",[Sms::class,"sendsms"]);
// Route::get("/sms/system_sms",[Sms::class,"customsms"]);
// Route::post("/save_sms_content",[Sms::class,"save_sms_content"]);
// Route::get("/sms_balance",[Sms::class,"sms_balance"]);
// Route::get("/sms/resend/{sms_id}",[Sms::class,"resend_sms"]);
// Route::post("/sendsms_routers",[Sms::class,"sendsms_routers"]);

// accounts and profile
Route::get("/Accounts",[admin::class,"getAdmin"]);
Route::post("/changePasswordAdmin", [admin::class,"updatePassword"]);
Route::get("/Accounts/add",[admin::class,"addAdmin"]);
Route::get("/Accounts/delete/{admin_id}",[admin::class,"delete_admin"])->name("delete_admin");
Route::post("/addAdministrator", [admin::class,"addAdministrator"]);
Route::get("/Admin/View/{admin_id}", [admin::class,"viewAdmin"]);
Route::post("/updateAdministrator", [admin::class,"updateAdmin"]);
Route::post("/update_dp", [admin::class,"upload_dp"]);
Route::post("/update_company_dp", [admin::class,"update_company_dp"]);
Route::post("/update_admin", [admin::class,"update_admin"]);
Route::post("/update_delete_option", [admin::class,"update_delete_option"]);
Route::get("/delete_pp/{admin_id}",[admin::class,"delete_pp"]);
Route::get("/delete_pp_organization",[admin::class,"delete_pp_organization"]);
Route::post("/update_organization_profile",[admin::class,"update_organization_profile"]);


// routes for the clients information
Route::get("/ClientDashboard",[Clients_data::class,"getClientInfor"]);
Route::get("/Payment",[Clients_data::class,"getTransaction"]);
Route::get("/Payment/View/{paymentid}",[Clients_data::class,'viewPayment']);
Route::view("/Payment/Confirm","confirmPay");
Route::get("/Payment/mpesa/{mpesaid}",[Clients_data::class,"confirm_mpesa"]);
Route::view("/Credentials","credential");
Route::post("/changePassword",[Clients_data::class,"change_password"]);
Route::get("/Payment/stkpush",[Transaction::class,"stkpush"]);

// get ip addresses
Route::get("/ipAddress/{routerid}",[Clients::class,"getIpaddresses"]);

// go and manage the billing sms
Route::get("/BillingSms/Manage",[billsms_manager::class,"getBilledClients"]);
// new sms client
Route::get("/BillingSms/New",[billsms_manager::class,"newClient"]);
// Register new client
Route::post("/register_new",[billsms_manager::class,"registerClient"]);
// VIEW AND UPDATE SMS CLIENT
Route::get("/BillingSms/ViewClient/{clientid}",[billsms_manager::class,"displayClient"]);
// update client sms
Route::post("/update_client_sms",[billsms_manager::class,"updateClient"]);
// delete client sms
Route::get("/delete_user_sms/{client_id}",[billsms_manager::class,"deleteClient"]);
// deactivate a client
Route::get("/deactivate_sms_client/{client_id}",[billsms_manager::class,"deactivateClient"]);
// activate a client
Route::get("/activate_sms_client/{client_id}",[billsms_manager::class,"activateClient"]);
// change sms balance
Route::post("/changeSmsBal",[billsms_manager::class,"changeSmsBal"]);
// view transactions for the transaction manager
Route::get("/BillingSms/Transactions",[billsms_manager::class,"viewTransaction"]);
// view transactions details
Route::get("/BillingSms/Transactions/View/{transaction_id}",[billsms_manager::class,"viewTransactionDetails"]);
// ASSIGNE TRANSACTION
Route::get("/BillingSms/Assign/Transaction/{transaction_id}/Client/{client_id}",[billsms_manager::class,"assignTransaction"]);
// confirm transfer of funnds
Route::get("/BillingSms/confirmTransfer/{client_id}/{transactionid}",[billsms_manager::class,"transferFunds"]);
Route::post("/renew_licence",[billsms_manager::class,"renew_Licence"]);
// manage the packages so that user can know how to pay
Route::get("/BillingSms/Packages",[billsms_manager::class,"myPackages"]);
// set ne package
Route::get("/BillingSms/NewPackage",[billsms_manager::class,"newPackage"]);
// register package
Route::post("/register_package",[billsms_manager::class,"registerPackage"]);
// register package
Route::get("/BillingSms/ViewPackage/{id}",[billsms_manager::class,"viewPackages"]);
// update_package
Route::post("/update_package",[billsms_manager::class,"updatePackage"]);
// delete packages
Route::get("/delete_package/{package_id}",[billsms_manager::class,"deletePackage"]);
// GET PACKAGE LSISTS
Route::get("/getpackages",[billsms_manager::class,"showPackages"]);

// create a new link to set up the router
Route::view("/Clients/NewRouterSetup","RouterSetup");
Route::post("/connect_router",[Router::class,"test_router"]);
Route::post("/remove_interface_bridge",[Router::class,"remove_interface_bridge"]);
Route::get("/getbridge",[Router::class,"process_interfaces"]);
Route::post("/add_bridge",[Router::class,"add_bridge"]);
Route::post("/remove_bridge",[Router::class,"remove_bridge"]);
Route::post("/change_bridge",[Router::class,"change_bridge"]);
Route::post("/get_setting",[Router::class,"get_setting"]);
Route::post("/set_dynamic",[Router::class,"set_dynamic"]);
Route::post(("/set_static_access"),[Router::class,"set_static_access"]);
Route::post("/set_pppoe_assignment",[Router::class,"set_pppoe_assignment"]);
Route::post("/set_pool",[Router::class,"set_pool"]);
Route::get("/get_pools",[Router::class,"get_pools"]);
Route::post("/add_pppoe_profile",[Router::class,"add_pppoe_profile"]);
Route::get("/get_pppoe_server",[Router::class,"get_pppoe_server"]);
Route::post("/save_ppoe_server",[Router::class,"save_ppoe_server"]);
Route::post("/add_security",[Router::class,"add_security"]);
Route::get("/get_security_profile",[Router::class,"get_security_profile"]);
Route::post("/save_ssid",[Router::class,"save_ssid"]);
Route::post("/get_interface_supply",[Router::class,"get_interface_supply"]);
Route::post("/get_wireless",[Router::class,"get_wireless"]);
Route::get("/getconnection",[Router::class,"getconnection"]);
Route::post("/get_interface_config",[Router::class,"get_interface_config"]);
Route::post("/get_internet_access",[Router::class,"get_internet_access"]);
Route::post("/get_supply_method",[Router::class,"get_supply_method"]);
Route::post("/wireless_settings",[Router::class,"wireless_settings"]);

// statistics
Route::get("/Client-Statistics",[Clients::class,'getClients_Statistics']);
Route::post("/Client-due-demographics",[Clients::class,'clientsDemographics']);
// Route::get("/Transactions/Statistics",[Transaction::class,'transactionStatistics']);

// router logs
Route::get("/Router/writeLogs/{router_id}",[Router::class,"writeRouterLogs"]);
Route::get("/Router/Logs/{router_id}",[Router::class,"readLogs"]);

// reports
Route::get("/Clients/generateReports",[Clients::class,"generateReports"]);
Route::get("/Transaction/generateReports",[Transaction::class,"generateReports"]);
Route::get("/SMS/generateReports",[Sms::class,"generateReports"]);

// expenses
Route::get("/Expenses",[Expenses::class,"getExpenses"]);
Route::post("/Expense/Category/Add",[Expenses::class,"addExpenseCategory"]);
Route::get("/Expense/Delete/{expense_index}",[Expenses::class,"deleteExpense"]);
Route::post("/Expense/Add",[Expenses::class,"addExpense"]);
Route::post("/Expense/Update",[Expenses::class,"updateExpense"]);
Route::get("/Expense/View/{expense_id}",[Expenses::class,"viewExpense"]);
Route::get("/Expense/DeleteRecords/{expense_id}",[Expenses::class,"deleteExpenseRecords"]);
Route::get("/Expenses/Generate/Reports",[Expenses::class,"generateReports"]);
Route::get("/Expense/Statistics",[Expenses::class,"expenseStatistics"]);
Route::get("/Expenses/Generate/FinStats",[Expenses::class,"financeStats"]);

// delete users
Route::post("/delete_clients",[Clients::class,"deleteClients"]);
Route::post("/send_sms_clients",[Clients::class,"sendSmsClients"]);
Route::get("/admin/deactivate/{admin_id}",[admin::class,"deactivateAdmin"]);

// bulk sms
Route::post("/Delete_bulk_sms",[Sms::class,"Delete_bulk_sms"]);
Route::post("/Resend_bulk_sms",[Sms::class,"Resend_bulk_sms"]);

Route::get("/SharedTables",[SharedTables::class,"openSharedTables"]);
Route::view("/CreateShareTables","createTable");
Route::post("/SaveTable",[SharedTables::class,"SaveTable"]);
Route::get("SharedTables/View/{table_id}/Name/{table_name}",[SharedTables::class,"getTable"]);
Route::get("SharedTables/Edit/{table_id}/Name/{table_name}",[SharedTables::class,"editTable"]);
Route::post("/UpdateTableCreated",[SharedTables::class,"UpdateTableCreated"]);
Route::get("/SharedTables/addRecord/{table_id}/Name/{table_name}",[SharedTables::class,"addRecords"]);
Route::post("/SharedTables/AddRecords",[SharedTables::class,"saveRecord"]);
Route::get("/SharedTables/Edit/{table_id}/Name/{table_name}/Record/{record_no}",[SharedTables::class,"editRecord"]);
Route::post("/SharedTables/UpdateRecords",[SharedTables::class,"UpdateRecords"]);
Route::get("/SharedTables/Delete/{table_id}/Name/{table_name}",[SharedTables::class,"deleteTable"]);
Route::get("/SharedTables/Delete/{table_id}/Name/{link_table_name}/Record/{rows_id}",[SharedTables::class,"deleteRecord"]);