<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="My ISP is the number one kenyan webserver software that helps you manage and monitor your webserver.">
    <meta name="keywords" content="admin template, Client template, dashboard template, gradient admin template, responsive client template, webapp, eCommerce dashboard, analytic dashboard">
    <meta name="author" content="ThemeSelect">
    <title>Hypbits - New Organization</title>
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
                    <h3 class="content-header-title">New Organization</h3>
                </div>
                <div class="content-header-right col-md-8 col-12">
                    <div class="breadcrumbs-top float-md-right">
                        <div class="breadcrumb-wrapper mr-1">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/Dashboard">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active">New Organization
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
                                <h4 class="card-title">New Organization</h4>
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
                                    {{-- <p>{{($client_data)}}</p> --}}
                                    <p><b>Note:</b></p>
                                    <ul>
                                        <li class="card-text" >Fill all the fields to add a new organization!</li>
                                    </ul>
                                    <hr>
                                    @if (session('success'))
                                        <p class='text-success'>{{ session('success') }}</p>
                                    @endif
                                    @if (session('error'))
                                        <p class='text-danger'>{{ session('error') }}</p>
                                    @endif
                                    <form action="{{url()->route("ProcessNewOrganization")}}" method="post">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-4 form-group">
                                                <label for="organization_name" class="form-control-label"><b>Organization Name</b></label>
                                                <input required type="text" name="organization_name" id="organization_name"
                                                    class="form-control rounded-lg p-1" placeholder="Organization name"
                                                    required
                                                    @if (session("organization_name"))
                                                        value="{{session("organization_name")}}"
                                                    @endif
                                                    >
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="organization_account" class="form-control-label"><b>Organization Account <span class="text-primary">( {{$last_acc_no}} )</span></b></label>
                                                <input required type="text" min="0" name="organization_account" id="organization_account"
                                                    class="form-control rounded-lg p-1" placeholder="HBS101"
                                                    @if (session("organization_account"))
                                                        value="{{session("organization_account")}}"
                                                    @endif>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="organization_location" class="form-control-label"><b>Organization Location</b></label>
                                                <input required type="text" name="organization_location" id="organization_location"
                                                    class="form-control rounded-lg p-1" placeholder="Organization Location e.x Mombasa"
                                                    required
                                                    @if (session("organization_location"))
                                                        value="{{session("organization_location")}}"
                                                    @endif
                                                    >
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="organization_contacts" class="form-control-label"><b>Organization Contacts </b></label>
                                                <input required type="text" min="0" name="organization_contacts" id="organization_contacts"
                                                    class="form-control rounded-lg p-1" placeholder="Organization Contacts. e.x : 0720000000"
                                                    @if (session("organization_contacts"))
                                                        value="{{session("organization_contacts")}}"
                                                    @endif>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="organization_email" class="form-control-label"><b>Organization Email </b></label>
                                                <input required type="text" min="0" name="organization_email" id="organization_email"
                                                    class="form-control rounded-lg p-1" placeholder="Organization E-Mails. e.x : hilaryme45@gmail.com"
                                                    @if (session("organization_email"))
                                                        value="{{session("organization_email")}}"
                                                    @endif>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="client_package" class="form-control-label"><b>Package </b></label>
                                                <select required name="client_package" id="client_package" class="form-control">
                                                    <option value="" hidden>Select Package</option>
                                                    @foreach ($packages as $package)
                                                        <option {{session("client_package") == $package->package_id ? "selected" : ""}} value="{{$package->package_id}}" >{{$package->package_name." - Kes (".number_format($package->amount_paid).")"}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-12 form-group">
                                                <input type="checkbox" class="form-control-checkbox float-left" id="register_main_user" name="register_main_user">
                                                <label for="register_main_user" class="form-control-label"> <b> Register Main User</b></label>
                                            </div>
                                        </div>
                                        <div class="row d-none" id="register_user_window">
                                            <div class="col-md-12">
                                                <h6 class="text-center"><u>Register Main User</u></h6>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="admin_name" class="form-control-label"><b>Fullname</b></label>
                                                <input type="text" name="admin_name" id="admin_name" class="form-control rounded-lg p-1" placeholder="Admin Fullname .."  
                                                    @if (session("admin_name"))
                                                        value="{{session("admin_name")}}"
                                                    @endif
                                                >
                                            </div>
                                            <div class="col-md-4">
                                                <label for="admin_contacts" class="form-control-label"><b>Contacts</b></label>
                                                <input type="text" name="admin_contacts" id="admin_contacts" class="form-control rounded-lg p-1" placeholder="Administrator contacts"  
                                                @if (session("admin_contacts"))
                                                    value="{{session("admin_contacts")}}"
                                                @endif>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="admin_username" class="form-control-label"><b>Username</b> <span class="text-danger" id="error_acc_no"></span></label>
                                                <input type="text" name="admin_username" id="admin_username" class="form-control rounded-lg p-1" placeholder="Administrator Username"  
                                                @if (session("admin_username"))
                                                    value="{{session("admin_username")}}"
                                                @endif>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="admin_password" class="form-control-label"><b>Password</b></label>
                                                <input type="password" name="admin_password" id="admin_password" class="form-control rounded-lg p-1" placeholder="Administrator password" >
                                            </div>
                                            <div class="col-md-12">
                                                <input type="hidden" name="privileges" id="privileged" value="[{&quot;option&quot;:&quot;My Clients&quot;,&quot;view&quot;:true,&quot;readonly&quot;:false},{&quot;option&quot;:&quot;Transactions&quot;,&quot;view&quot;:true,&quot;readonly&quot;:false},{&quot;option&quot;:&quot;Expenses&quot;,&quot;view&quot;:true,&quot;readonly&quot;:false},{&quot;option&quot;:&quot;My Routers&quot;,&quot;view&quot;:true,&quot;readonly&quot;:false},{&quot;option&quot;:&quot;SMS&quot;,&quot;view&quot;:true,&quot;readonly&quot;:false},{&quot;option&quot;:&quot;Account and Profile&quot;,&quot;view&quot;:true,&quot;readonly&quot;:false}]">
                                                {{-- <input type="hidden" name="privileges" id="privileged"> --}}
                                                <div class="container my-2">
                                                    <h6 class="text-center">Assign Administrator Privileges</h6>
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered mb-0">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Menu</th>
                                                                    <th>View <input type="checkbox" checked id="all_view"></th>
                                                                    <th>Read-only <input type="checkbox" id="all_readonly"></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <th scope="row">1</th>
                                                                    <td><label for="my_clients_option" class="form-label"><b>My Clients</b></label></td>
                                                                    <td><input class="all_view" checked type="checkbox" id="my_clients_option_view"></td>
                                                                    <td><input class="all_readonly" type="checkbox" id="my_clients_option_readonly"></td>
                                                                </tr>
                                                                <tr>
                                                                    <th rowspan="3" scope="row">2</th>
                                                                    <td ><label for="my_clients_option" class="form-label"><b>Accounts</b></label></td>
                                                                    <td><input class="all_view" checked type="checkbox" id="accounts_option_view"></td>
                                                                    <td><input class="all_readonly"  type="checkbox" id="accounts_option_readonly"></td>
                                                                </tr>
                                                                <tr>
                                                                    <td ><label for="my_clients_option" class="form-label"><b><i>Transactions</i></b></label></td>
                                                                    <td><input class="all_view account_options" checked type="checkbox" id="transactions_option_view"></td>
                                                                    <td><input class="all_readonly account_options_2"  type="checkbox" id="transactions_option_readonly"></td>
                                                                </tr>
                                                                <tr>
                                                                    <td ><label for="my_clients_option" class="form-label"><b><i>Expenses</i></b></label></td>
                                                                    <td><input class="all_view account_options" checked type="checkbox" id="expenses_option_view"></td>
                                                                    <td><input class="all_readonly account_options_2"  type="checkbox" id="expenses_option_readonly"></td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row">3</th>
                                                                    <td ><label for="my_clients_option" class="form-label"><b>My Routers</b></label></td>
                                                                    <td><input class="all_view" checked type="checkbox" id="my_routers_option_view"></td>
                                                                    <td><input class="all_readonly" type="checkbox" id="my_routers_option_readonly"></td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row">4</th>
                                                                    <td ><label for="my_clients_option" class="form-label"><b>SMS</b></label></td>
                                                                    <td><input class="all_view" checked type="checkbox" id="sms_option_view"></td>
                                                                    <td><input class="all_readonly" type="checkbox" id="sms_option_readonly"></td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row">5</th>
                                                                    <td ><label for="my_clients_option" class="form-label"><b>Account & Profile</b></label></td>
                                                                    <td><input class="all_view" checked type="checkbox" id="account_profile_option_view"></td>
                                                                    <td><input class="all_readonly" type="checkbox" id="account_profile_option_readonly"></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <button class="btn btn-success" type="submit"><i
                                                        class="ft-plus"></i> Register Organization</button>
                                            </div>
                                            <div class="col-md-6">
                                                <a class="btn btn-secondary btn-outline" href="{{route("Packages")}}"
                                                    type="button"><i class="ft-x"></i> Cancel</a>
                                            </div>
                                        </div>
                                    </form>
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

    <!-- BEGIN CLIENT JS-->
    <script src="/theme-assets/js/core/add_organization.js"></script>
    <script>
        var register_main_user_check = document.getElementById("register_main_user");
        register_main_user_check.onclick = function () {
            if (!register_main_user_check.checked) {
                document.getElementById("register_user_window").classList.add("d-none");
            }else{
                document.getElementById("register_user_window").classList.remove("d-none");
            }
        }
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