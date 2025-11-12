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
use Illuminate\Routing\RouteRegistrar;
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
Route::post("/Organization/UpdateExpiration/{organization_id}",[Organization::class,"update_expiry"])->name("UpdateExpiration");
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
Route::get("/Organization/ActivateSMS/{organization_id}",[Organization::class,"ActivateSMS"])->name("ActivateSMS");
Route::get("/Organization/DeactivateSMS/{organization_id}",[Organization::class,"DeactivateSMS"])->name("DeactivateSMS");
Route::get("/Organization/Delete/{delete_id}",[Organization::class,"DeleteOrganization"])->name("DeleteOrganization");
Route::get("/Organization/Clients/datatable/{organization_id}", [Organization::class, 'getClientsDatatable'])->name("getClientsDatatable");
Route::get("/Organization/Clients/Active/pdf/{organization_id}", [Organization::class, 'getActiveClients'])->name("getActiveClients");

// save minimum payment
Route::post("/Organization/Client/Update/MinimumPay/{organization_id}", [Organization::class, "updateMinPay"])->name("client.update.minimum_payment.static");
Route::post("/Organization/Client/Update/Phone/{organization_id}", [Organization::class, "change_phone_number"])->name("change_client_phone");
Route::post("/Organization/Client/Update/MonthlyPayment/{organization_id}", [Organization::class, "change_client_monthly_payment"])->name("change_client_monthly_payment");
Route::post("/Organization/Client/Update/Wallet/{organization_id}", [Organization::class, "changeWalletBal"])->name("changeWallet");
Route::post("/Organization/Client/Update/ExpiryDate/{organization_id}", [Organization::class, "updateExpDate"])->name("updateExpDate");
Route::post("/Organization/Client/Update/Comment/{organization_id}", [Organization::class, "update_client_comment"])->name("update_client_comment");
Route::post("/Organization/Client/InitiateSTK/{organization_id}", [Organization::class, "initiate_stk"])->name("initiate_stk");
Route::post("/Organization/Client/UpdateRefferer/{organization_id}", [Organization::class, "setRefferal"])->name("setRefferal");
Route::post("/Organization/Client/ConvertClient/{organization_id}", [Organization::class, "convertClient"])->name("convertClient");
Route::get("/Organization/Client/reffererInfor/{organization_id}/{client_account}", [Organization::class, "getRefferal"])->name("getRefferal");
Route::get("/Organization/Client/getRouterProfiles/{organization_id}/{routerid}", [Organization::class, "getRouterProfile"])->name("getRouterProfile");
Route::get("/Organization/Client/getRouterInterfaces/{organization_id}/{routerid}", [Organization::class, "getRouterInterfaces"])->name("getRouterInterfaces");
Route::get("/Organization/Client/DeactivateFreeze/{organization_id}/{client_id}", [Organization::class, "deactivatefreeze"])->name("deactivatefreeze");

// statistics
Route::get("/Organization/Client-Statistics/{organization_id}",[Organization::class,'get_clients_statistics'])->name("get_clients_statistics");
Route::post("/Organization/Client-due-demographics/{organization_id}",[Organization::class,'clients_demographics'])->name("clients_demographics");
Route::get("/Organization/Clients/generate_reports/{organization_id}",[Organization::class,"generate_reports"])->name("generate_reports");

// organization view transactions 
Route::get("/Organization/ViewTransaction/{organization_id}",[organization_client_transaction::class,"get_transactions"])->name("get_transactions");
Route::get("/Organization/ViewTransactionDetail/{organization_id}/{transaction_id}",[organization_client_transaction::class,"transaction_details"])->name("transaction_details");
Route::get("/Organization/TransactionStats/{organization_id}",[organization_client_transaction::class,"transaction_statistics"])->name("transaction_statistics");
Route::get("/Organization/Admins/deactivate_administrator/{organization_id}/{administrator_id}",[Organization_Admin::class,"deactivate_admin"])->name("deactivate_admin");
Route::get("/Organization/TransactionStats/Assign/{organization_id}/{transaction_id}/{client_id}",[organization_client_transaction::class,"assign_transaction"])->name("assign_transaction");
Route::get("/Organization/TransactionStats/confirmTransfer/{organization_id}/{transaction_id}/{client_id}",[organization_client_transaction::class,"confirmTransfer"])->name("confirmTransfer");


// organization routers
Route::get("/Organization/Routers/{organization_id}",[Organization_router::class,"view_routers"])->name("view_routers");
Route::get("/Organization/Router/{organization_id}/{router_id}",[Organization_router::class,"view_router_details"])->name("view_router_details");
Route::post("/Organization/Router/Update/{organization_id}",[Organization_router::class,"updateRouter"])->name("update_organization_router");
Route::get("/Organization/Router/Connect/{organization_id}/{router_id}",[Organization_router::class,"connect_organization_router"])->name("connect_organization_router");
Route::get("/Organization/Routers/Delete/{organization_id}/{routerid}",[Organization_router::class,"delete_router"])->name("delete_router");
Route::get("/Organization/Routers/Connect/{organization_id}/{routerid}",[Organization_router::class,"connect_router"])->name("connect_router");

// Organization SMS
Route::get("/Organizations/SMS/{organization_id}",[Organization_sms::class,"view_organization_sms"])->name("view_organization_sms");
Route::get("/Organizations/SMS/View/{organization_id}/{sms_id}",[Organization_sms::class,"view_sms"])->name("view_sms");
Route::get("/Organizations/SMS/Deleted/{organization_id}/{sms_id}",[Organization_sms::class,"delete_sms"])->name("delete_sms_organization");
Route::get("/Organizations/SMS/CustomSMS/{organization_id}",[Organization_sms::class,"customize_sms"])->name("customize_sms");
Route::post("/Organizations/SMS/save_custom/{organization_id}",[Organization_sms::class,"save_sms_content"])->name("save_sms_content");
Route::post("/Organizations/SMS/save_custom/{organization_id}",[Organization_sms::class,"save_sms_customize"])->name("save_sms_customize");
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
Route::get("/SMS/Compose",[Sms::class,"Compose"])->name("Compose");
Route::post("/SMS/Delete_bulk",[Sms::class,"Delete_bulk_sms"])->name("Delete_bulk_sms");
Route::post("/SMS/Resend_bulk_sms",[Sms::class,"Resend_bulk_sms"])->name("Resend_bulk_sms");
Route::get("/SMS/Customize",[Sms::class,"customize"])->name("customize");
Route::post("/SMS/save_content",[Sms::class,"save_sms_content"])->name("save_sms_content");
Route::get("/SMS/Balance",[Sms::class,"sms_balance"])->name("sms_balance");
Route::get("/SMS/generateReports",[Sms::class,"generateReports"]);


// Accounts and profile
// Route::get("/Accounts",[Account])
Route::get("/Accounts",[admin::class,"getAdmin"])->name("Accounts");
Route::post("/Accounts/update_dp", [admin::class,"upload_dp"])->name("update_dp");
Route::post("/Accounts/update_admin", [admin::class,"update_admin"])->name("update_admin");
Route::get("/Accounts/delete_pp/{admin_id}",[admin::class,"delete_pp"])->name("delete_pp");
Route::post("/Account/UpdatePassword", [admin::class,"updatePassword"])->name("updatePassword");
Route::post("/Account/update_delete_option", [admin::class,"update_delete_option"])->name("update_delete_option");
Route::get("/Accounts/add",[admin::class,"addAdmin"])->name("AddAdmin");
Route::post("/Accounts/AddAdministrator", [admin::class,"addAdministrator"])->name("AddAdministrator");
Route::get("/Accounts/Admin/View/{admin_id}", [admin::class,"view_admin"])->name("ViewAdmin");
Route::post("/Accounts/Admin/Update", [admin::class,"updateAdmin"])->name("updateAdmin");
Route::get("/Accounts/Admin/Delete/{admin_id}", [admin::class,"delete_admin"])->name("deleteAdmin");
Route::get("/Accounts/Admin/Disable/{admin_id}", [admin::class,"deactivateAdmin"])->name("disableAdmin");