<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="My ISP is the number one kenyan webserver software that helps you manage and monitor your webserver.">
    <meta name="keywords" content="admin template, Client template, dashboard template, gradient admin template, responsive client template, webapp, eCommerce dashboard, analytic dashboard">
    <meta name="author" content="ThemeSelect">
    <title>HBS - View Organization</title>
    <link rel="apple-touch-icon" href="/theme-assets/images/logo2.jpeg">
    <link rel="shortcut icon" href="/theme-assets/images/logo2.jpeg">
    <link href="https://fonts.googleapis.com/css?family=Muli:300,300i,400,400i,600,600i,700,700i%7CComfortaa:300,400,700" rel="stylesheet">
    <link href="https://maxcdn.icons8.com/fonts/line-awesome/1.1/css/line-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- BEGIN VENDOR CSS-->
    <link rel="stylesheet" type="text/css" href="/theme-assets/css/vendors.css">
    <link rel="stylesheet" type="text/css" href="/theme-assets/vendors/css/charts/chartist.css">
    <!-- END VENDOR CSS-->
    <!-- BEGIN CHAMELEON  CSS-->
    <link rel="stylesheet" type="text/css" href="/theme-assets/css/app-lite.css">
    <!-- END CHAMELEON  CSS-->
    <!-- BEGIN Page Level CSS-->
    <link rel="stylesheet" type="text/css" href="/theme-assets/css/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" type="text/css" href="/theme-assets/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="/theme-assets/css/pages/dashboard-ecommerce.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <!-- END Page Level CSS-->
    <!-- BEGIN Custom CSS-->
    <!-- END Custom CSS-->
</head>

<style>
    .hide{
        display: none;
    }
</style>

<body class="vertical-layout vertical-menu 2-columns  menu-expanded fixed-navbar" data-open="click" data-menu="vertical-menu" data-color="bg-chartbg" data-col="2-columns">

    <!-- fixed-top-->
        <x-navbar/>
    <!-- ////////////////////////////////////////////////////////////////////////////-->


    <div class="main-menu menu-fixed menu-light menu-accordion menu-shadow " data-scroll-to-active="true" data-img="/theme-assets/images/backgrounds/02.jpg">
        <x-menu/>
        <!-- <a class="btn btn-danger btn-block btn-glow btn-upgrade-pro mx-1" href="https://themeselection.com/products/chameleon-admin-modern-bootstrap-webapp-dashboard-html-template-ui-kit/" target="_blank">Download PRO!</a> -->
        <div class="navigation-background">
        </div>
    </div>
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-wrapper-before"></div>
            <div class="content-header row">
                <div class="content-header-left col-md-4 col-12 mb-2">
                    <h3 class="content-header-title">View Organization</h3>
                </div>
                <div class="content-header-right col-md-8 col-12">
                    <div class="breadcrumbs-top float-md-right">
                        <div class="breadcrumb-wrapper mr-1">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/Dashboard">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active">View Organization
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- Basic Tables start -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">View Organization - "{{ucwords(strtolower($organization_details->organization_name))}}"</h4>
                                <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                                <div class="heading-elements">
                                    <ul class="list-inline mb-0">
                                        <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                                        <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
                                        <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                        <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
                                    </ul>
                                </div>
                            </div>
                            <div class="card-content collapse show">
                                <a href="{{route("Organizations")}}" class="btn btn-infor"><i class="fas fa-arrow-left"></i> Back
                                    to Organization List</a>
                                <div class="card-body">
                                    <h6 class="text-center"><b><u>View Organization "{{ucwords(strtolower($organization_details->organization_name))}}"</u></b></h6>
                                    <hr>
                                    @if (session('success'))
                                        <p class='text-success'>{{ session('success') }}</p>
                                    @endif
                                    @if (session('error'))
                                        <p class='text-danger'>{{ session('error') }}</p>
                                    @endif
                                    <div class="container row ">
                                        <div class="col-md-6 form-group border border-primary border-1 rounded row w-75 mx-auto py-2">
                                            <div class="col-md-6">
                                                <p><b>Last Payment Date : </b><span class="badge bg-secondary">Readonly</span></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="text-primary"><b>{{$organization_details->last_payment_date != null ? date("D dS M Y @ h:i:sA",strtotime($organization_details->last_payment_date)) : "Not-Set"}}</b></p>
                                            </div>
                                        </div>
                                        <div class="col-md-6 form-group border border-primary border-1 rounded row w-75 mx-auto py-2">
                                            <div class="col-md-6">
                                                <p><b>Account Renewal Date : </b><span class="badge bg-secondary">Readonly</span></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="text-primary"><b>{{$organization_details->account_renewal_date != null ? date("D dS M Y @ h:i:sA",strtotime($organization_details->account_renewal_date)) : "Not-Set"}}</b></p>
                                            </div>
                                        </div>
                                        <div class="col-md-6 form-group border border-primary border-1 rounded row w-75 mx-auto py-2">
                                            <div class="col-md-6">
                                                <p><b>Date of Expiration : </b><span class="badge bg-secondary">Readonly</span></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="text-primary"><b>{{$expiry_date}}</b></p>
                                            </div>
                                        </div>
                                        <div class="col-md-6 form-group border border-primary border-1 rounded row w-75 mx-auto py-2">
                                            <div class="col-md-6">
                                                <p><b>Organization Status : </b></p>
                                            </div>
                                            <div class="col-md-6">
                                                <a href="{{route("DeactivateOrganization",$organization_details->organization_id)}}" class="btn btn-sm btn-danger {{$organization_details->organization_status == "1" ? "" : "d-none"}}">De-Activate</a>
                                                <a href="{{route("ActivateOrganization",$organization_details->organization_id)}}" class="btn btn-sm btn-success {{$organization_details->organization_status == "1" ? "d-none" : ""}}">Activate</a>
                                            </div>
                                        </div>
                                        <div class="col-md-6 form-group border border-primary border-1 rounded row w-75 mx-auto py-2">
                                            <div class="form-group w-100" id="lenience_days_viewer">
                                                <p class="px-1"><b>Active Day Remaining : {{$organization_details->wallet != null ? number_format($organization_details->lenience) : "0"}} Day(s)</b> <button class="btn btn-sm btn-primary float-right" id="lenience_days_btn"><i class="ft-refresh-cw"></i> Change</button></p>
                                            </div>
                                            <form method="POST" action="{{route("UpdateLenience",$organization_details->organization_id)}}" class="form-group d-none w-100" id="lenience_days_window">
                                                <hr>
                                                <h6 class="text-center">Set Leniece Days</h6>
                                                @csrf
                                                <label for="linience_days" class="form-control-label"><b>Lenience Days</b></label>
                                                <input type="number" placeholder="Leniece Days" class="form-control" name="linience_days" id="linience_days">
                                                <input type="submit" class="btn btn-success btn-sm my-2" value="Update">
                                            </form>
                                        </div>
                                        <div class="col-md-6 form-group border border-primary border-1 rounded row w-75 mx-auto py-2">
                                            <div class="col-md-6">
                                                <p><b>Payment Status : </b></p>
                                            </div>
                                            <div class="col-md-6">
                                                <a href="{{route("Deactivate_Payment_Status",$organization_details->organization_id)}}" class="btn btn-sm btn-danger {{$organization_details->payment_status == "1" ? "" : "d-none"}}">De-Activate</a>
                                                <a href="{{route("Activate_Payment_Status",$organization_details->organization_id)}}" class="btn btn-sm btn-success {{$organization_details->payment_status == "1" ? "d-none" : ""}}">Activate</a>
                                            </div>
                                        </div>
                                        <div class="col-md-6 form-group border border-primary border-1 rounded row w-75 mx-auto py-2">
                                            <div class="col-md-8">
                                                <p><b>Send SMS Status : </b></p>
                                            </div>
                                            <div class="col-md-4">
                                                <a href="{{route("DeactivateSMS",$organization_details->organization_id)}}" class="btn btn-sm btn-danger {{$organization_details->send_sms == "1" ? "" : "d-none"}}">De-Activate</a>
                                                <a href="{{route("ActivateSMS",$organization_details->organization_id)}}" class="btn btn-sm btn-success {{$organization_details->send_sms == "1" ? "d-none" : ""}}">Activate</a>
                                            </div>
                                        </div>
                                        <div class="col-md-6 form-group border border-primary border-1 rounded row w-75 mx-auto py-2">
                                            <div class="form-group w-100" id="discount_viewer">
                                                <p for="discount" class="px-1"><b>Discount : {{$organization_details->discount_type != null ? ($organization_details->discount_type == "number" ? "Kes ".number_format($organization_details->discount_amount) : $organization_details->discount_amount."%") : "Not-Set"}}</b> <button class="btn btn-sm btn-primary float-right" id="discount_change_btn"><i class="ft-refresh-cw"></i> Change</button></p>
                                            </div>
                                            <form action="{{route("UpdateDiscount",$organization_details->organization_id)}}" method="POST" class="form-group d-none w-100" id="discount_window">
                                                <hr>
                                                <h6 class="text-center">Set Discount</h6>
                                                @csrf
                                                <label for="discount_type" class="form-control-label"><b>Discount</b></label>
                                                <select name="discount_type" id="discount_type" class="form-control">
                                                    <option selected value="number">Discount Price</option>
                                                    <option value="percentage">Discount Percentage</option>
                                                </select>
                                                <label for="discount_amount" class="form-control-label"><b>Discount</b></label>
                                                <input type="number" name="discount_amount" id="discount_amount" placeholder="Discount Balance" class="form-control" value="{{$organization_details->discount_type != null ? ($organization_details->discount_type == "number" ? $organization_details->discount_amount : $organization_details->discount_amount) : "0"}}" >
                                                <input type="submit" class="btn btn-success btn-sm my-2" value="Set">
                                            </form>
                                        </div>
                                        <div class="col-md-6 form-group border border-primary border-1 rounded row w-75 mx-auto py-2">
                                            <div class="form-group w-100" id="wallet_balance_viewer">
                                                <p for="wallet_balance" class="px-1"><b>Wallet : Kes {{$organization_details->wallet != null ? number_format($organization_details->wallet) : "0"}}</b> <button class="btn btn-sm btn-primary float-right" id="wallet_change_btn"><i class="ft-refresh-cw"></i> Change</button></p>
                                            </div>
                                            <form action="{{route("UpdateWallet",$organization_details->organization_id)}}" method="POST" class="form-group d-none w-100" id="wallet_balance_window">
                                                <hr>
                                                <h6 class="text-center">Set Wallet Balance</h6>
                                                @csrf
                                                <label for="wallet_balance" class="form-control-label"><b>Wallet</b></label>
                                                <input type="number" placeholder="Wallet Balance" class="form-control" value="{{$organization_details->wallet != null ? $organization_details->wallet : "0"}}" name="wallet_balance" id="wallet_balance">
                                                <input type="submit" class="btn btn-success btn-sm my-2" value="Update">
                                            </form>
                                        </div>
                                        <div class="col-md-6 form-group row w-75 mx-auto py-2">
                                            
                                        </div>
                                    </div>
                                    <hr>
                                    <p class="text-primary text-center"><u>Organization Details</u></p>
                                    <hr>
                                    @if (session('success'))
                                        <p class='text-success'>{{ session('success') }}</p>
                                    @endif
                                    @if (session('error'))
                                        <p class='text-danger'>{{ session('error') }}</p>
                                    @endif
                                    <form action="{{url()->route("UpdateOrganization",$organization_details->organization_id)}}" method="post">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-4 form-group">
                                                <label for="organization_name" class="form-control-label"><b>Organization Name</b></label>
                                                <input required type="text" name="organization_name" id="organization_name"
                                                    class="form-control rounded-lg p-1" placeholder="Organization name"
                                                    required value="{{$organization_details->organization_name}}">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="organization_account" class="form-control-label"><b>Organization Account</b></label>
                                                <input required type="text" readonly min="0" name="organization_account" id="organization_account"
                                                    class="form-control rounded-lg p-1" placeholder="HBS101" value="{{$organization_details->account_no}}">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="organization_location" class="form-control-label"><b>Organization Location</b></label>
                                                <input required type="text" name="organization_location" id="organization_location"
                                                    class="form-control rounded-lg p-1" placeholder="Organization Location e.x Mombasa"
                                                    required value="{{$organization_details->organization_address}}"
                                                    >
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="organization_contacts" class="form-control-label"><b>Organization Contacts </b></label>
                                                <input required type="text" min="0" name="organization_contacts" id="organization_contacts"
                                                    class="form-control rounded-lg p-1" placeholder="Organization Contacts. e.x : 0720000000" value="{{$organization_details->organization_main_contact}}">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="organization_email" class="form-control-label"><b>Organization Email </b></label>
                                                <input required type="text" min="0" name="organization_email" id="organization_email"
                                                    class="form-control rounded-lg p-1" placeholder="Organization E-Mails. e.x : hilaryme45@gmail.com" value="{{$organization_details->organization_email}}">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="client_package" class="form-control-label"><b>Package </b></label>
                                                <select required name="client_package" id="client_package" class="form-control">
                                                    <option value="" hidden>Select Package</option>
                                                    @foreach ($packages as $package)
                                                        <option {{$organization_details->package_name == $package->package_id ? "selected" : ""}} value="{{$package->package_id}}" >{{$package->package_name." - Kes (".number_format($package->amount_paid).")"}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="business_short_code" class="form-control-label"><b>Organization Paybill Number</b> <span class="text-danger">{Double check your paybill! When captured incorrectly the customer`s transactions won`t be captured automatically!}</span></label>
                                                <input required type="text" min="0" name="business_short_code" id="business_short_code"
                                                    class="form-control rounded-lg p-1" placeholder="Ex : 112233" value="{{$organization_details->BusinessShortCode}}">
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <button class="btn btn-success" type="submit"><i
                                                        class="ft-save"></i> Update</button>
                                            </div>
                                            <div class="col-md-6">
                                                <a class="btn btn-secondary btn-outline" href="{{url()->route("Organizations")}}"
                                                    type="button"><i class="ft-x"></i> Cancel</a>
                                            </div>
                                        </div>
                                    </form>
                                    <hr class="mt-3">
                                    <h6 class="text-center"><u>Client Data</u></h6>
                                    <div class="row border border-primary rounded p-2">
                                        <div class="col-md-2 text-center">
                                            <p class="text-center"><b>Client : {{$client_count}} Client(s)</b></p>
                                            <hr>
                                            <a href="{{route("viewOrganizationClients",$organization_details->organization_id)}}" class="btn btn-sm btn-secondary">View Client(s)</a>
                                        </div>
                                        <div class="col-md-3 text-center">
                                            <p class="text-center"><b>Transactions : {{$transaction_count}} Transaction(s)</b></p>
                                            <hr>
                                            <a href="{{route("get_transactions",[$organization_details->organization_id])}}" class="btn btn-sm btn-secondary">View Transaction(s)</a>
                                        </div>
                                        <div class="col-md-2 text-center">
                                            <p class="text-center"><b>Routers : {{$routers_count}} Router(s)</b></p>
                                            <hr>
                                            <a href="{{route("view_routers",[$organization_details->organization_id])}}" class="btn btn-sm btn-secondary">View Router(s)</a>
                                        </div>
                                        <div class="col-md-2 text-center">
                                            <p class="text-center"><b>SMS : {{$sms_count}} SMS(es)</b></p>
                                            <hr>
                                            <a href="{{route("view_organization_sms",[$organization_details->organization_id])}}" class="btn btn-sm btn-secondary">View SMS(es)</a>
                                        </div>
                                        <div class="col-md-3 text-center">
                                            <p class="text-center"><b>Admin : {{$administrator_count}} Administrators</b></p>
                                            <hr>
                                            <a href="{{route("view_organization_admin",[$organization_details->organization_id])}}" class="btn btn-sm btn-secondary">View Admins</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Basic Tables end -->

            </div>
        </div>
    </div>
    <!-- ////////////////////////////////////////////////////////////////////////////-->
    <!-- The footer -->
        <x-footer/>
    <!-- ////////////////////////// -->

    <!-- BEGIN VENDOR JS-->
    <script src="/theme-assets/vendors/js/vendors.min.js" type="text/javascript"></script>
    
    <!-- BEGIN CHAMELEON  JS-->
    <script src="/theme-assets/js/core/app-menu-lite.js" type="text/javascript"></script>
    <script src="/theme-assets/js/core/app-lite.js" type="text/javascript"></script>
    <!-- END CHAMELEON  JS-->
    <script>
        var lenience_days_btn = document.getElementById("lenience_days_btn");
        var wallet_change_btn = document.getElementById("wallet_change_btn");
        var discount_change_btn = document.getElementById("discount_change_btn");

        wallet_change_btn.onclick = function () {
            var wallet_balance_window = document.getElementById("wallet_balance_window");
            wallet_balance_window.classList.toggle("d-none");
        }
        lenience_days_btn.onclick = function () {
            var lenience_days_window = document.getElementById("lenience_days_window");
            lenience_days_window.classList.toggle("d-none");
        }
        discount_change_btn.onclick = function () {
            var discount_window = document.getElementById("discount_window");
            discount_window.classList.toggle("d-none");
        }
    </script>

    <!-- BEGIN CLIENT JS-->
    {{-- <script src="/theme-assets/js/core/add_organization.js"></script> --}}
    <script>
        var milli_seconds = 1200;
        setInterval(() => {
            if (milli_seconds == 0) {
                window.location.href = "/";
            }
            milli_seconds--;
        }, 1000);
    </script>
    <!-- END CLIENT JS-->
</body>

</html>