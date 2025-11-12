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
                                                <label for="organization_name" class="form-control-label">Organization Name </label>
                                                <input required type="text" name="organization_name" id="organization_name"
                                                    class="form-control rounded-lg p-1" placeholder="Organization name"
                                                    required
                                                    @if (session("organization_name"))
                                                        value="{{session("organization_name")}}"
                                                    @endif
                                                    >
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="organization_account" class="form-control-label">Organization Account <span class="text-primary">( {{$last_acc_no}} )</span></label>
                                                <input required type="text" min="0" name="organization_account" id="organization_account"
                                                    class="form-control rounded-lg p-1" placeholder="HBS101"
                                                    @if (session("organization_account"))
                                                        value="{{session("organization_account")}}"
                                                    @endif>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="organization_location" class="form-control-label">Organization Location </label>
                                                <input required type="text" name="organization_location" id="organization_location"
                                                    class="form-control rounded-lg p-1" placeholder="Organization Location e.x Mombasa"
                                                    required
                                                    @if (session("organization_location"))
                                                        value="{{session("organization_location")}}"
                                                    @endif
                                                    >
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="organization_contacts" class="form-control-label">Organization Contacts </label>
                                                <input required type="text" min="0" name="organization_contacts" id="organization_contacts"
                                                    class="form-control rounded-lg p-1" placeholder="Organization Contacts. e.x : 0720000000"
                                                    @if (session("organization_contacts"))
                                                        value="{{session("organization_contacts")}}"
                                                    @endif>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="organization_email" class="form-control-label">Organization Email</label>
                                                <input required type="text" min="0" name="organization_email" id="organization_email"
                                                    class="form-control rounded-lg p-1" placeholder="Organization E-Mails. e.x : hilaryme45@gmail.com"
                                                    @if (session("organization_email"))
                                                        value="{{session("organization_email")}}"
                                                    @endif>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="free_trial_period" class="form-control-label">Free Trial Period </label>
                                                <select required name="free_trial_period" id="free_trial_period" class="form-control">
                                                    <option value="" hidden>Select Period</option>
                                                    <option value="1 Month">1 Month</option>
                                                    <option value="2 Month">2 Month</option>
                                                    <option value="3 Month">3 Month</option>
                                                    <option value="4 Month">4 Month</option>
                                                    <option value="5 Month">5 Month</option>
                                                    <option value="6 Month">6 Month</option>
                                                    <option value="7 Month">7 Month</option>
                                                    <option value="8 Month">8 Month</option>
                                                    <option value="9 Month">9 Month</option>
                                                    <option value="10 Month">10 Month</option>
                                                    <option value="11 Month">11 Month</option>
                                                    <option value="12 Month">12 Month</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="monthly_payment" class="form-control-label">Monthly Payment <i>(per 50 clients)</i></label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend"><span class="input-group-text">Kes </span></div>
                                                    <input type="number" class="form-control" id="monthly_payment" name="monthly_payment" placeholder="E.g, 1000" value="1000">
                                                </div>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="registration_date" class="form-control-label">Registration Date</label>
                                                <input type="date" class="form-control" id="registration_date" name="registration_date" value="<?=date("Y-m-d")?>">
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
                                                <label for="admin_name" class="form-control-label">Fullname</label>
                                                <input type="text" name="admin_name" id="admin_name" class="form-control rounded-lg p-1" placeholder="Admin Fullname .."  
                                                    @if (session("admin_name"))
                                                        value="{{session("admin_name")}}"
                                                    @endif
                                                >
                                            </div>
                                            <div class="col-md-4">
                                                <label for="admin_contacts" class="form-control-label">Contacts</label>
                                                <input type="text" name="admin_contacts" id="admin_contacts" class="form-control rounded-lg p-1" placeholder="Administrator contacts"  
                                                @if (session("admin_contacts"))
                                                    value="{{session("admin_contacts")}}"
                                                @endif>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="admin_username" class="form-control-label">Username <span class="text-danger" id="error_acc_no"></span></label>
                                                <input type="text" name="admin_username" id="admin_username" class="form-control rounded-lg p-1" placeholder="Administrator Username"  
                                                @if (session("admin_username"))
                                                    value="{{session("admin_username")}}"
                                                @endif>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="admin_password" class="form-control-label">Password </label>
                                                <input type="password" name="admin_password" id="admin_password" class="form-control rounded-lg p-1" placeholder="Administrator password" >
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="administrator_status" class="form-control-label">Administrator Contacts </label>
                                                <select name="administrator_status" id="administrator_status" class="form-control" required>
                                                    <option value="" hidden>Select Admin Status</option>
                                                    <option selected value="1">Active</option>
                                                    <option value="0">In-Active</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="administrator_email" class="form-control-label">Administrator Email </label>
                                                <input required type="text" min="0" name="administrator_email" id="administrator_email"
                                                    class="form-control rounded-lg p-1" placeholder="e.g : mail@ladybirdsmis.com">
                                            </div>
                                            <div class="col-md-12">
                                                <input type="hidden" name="privileges" id="privileged" value="[{&quot;option&quot;:&quot;My Clients&quot;,&quot;view&quot;:true,&quot;readonly&quot;:false,&quot;expiry&quot;:&quot;indefinate_expiry&quot;,&quot;expiry_date&quot;:&quot;&quot;},{&quot;option&quot;:&quot;Transactions&quot;,&quot;view&quot;:true,&quot;readonly&quot;:false,&quot;expiry&quot;:&quot;indefinate_expiry&quot;,&quot;expiry_date&quot;:&quot;&quot;},{&quot;option&quot;:&quot;Expenses&quot;,&quot;view&quot;:true,&quot;readonly&quot;:false,&quot;expiry&quot;:&quot;indefinate_expiry&quot;,&quot;expiry_date&quot;:&quot;&quot;},{&quot;option&quot;:&quot;My Routers&quot;,&quot;view&quot;:true,&quot;readonly&quot;:false,&quot;expiry&quot;:&quot;indefinate_expiry&quot;,&quot;expiry_date&quot;:&quot;&quot;},{&quot;option&quot;:&quot;SMS&quot;,&quot;view&quot;:true,&quot;readonly&quot;:false,&quot;expiry&quot;:&quot;indefinate_expiry&quot;,&quot;expiry_date&quot;:&quot;&quot;},{&quot;option&quot;:&quot;Account and Profile&quot;,&quot;view&quot;:true,&quot;readonly&quot;:false,&quot;expiry&quot;:&quot;indefinate_expiry&quot;,&quot;expiry_date&quot;:&quot;&quot;},{&quot;option&quot;:&quot;Quick Register&quot;,&quot;view&quot;:true,&quot;readonly&quot;:false,&quot;expiry&quot;:&quot;indefinate_expiry&quot;,&quot;expiry_date&quot;:&quot;&quot;},{&quot;option&quot;:&quot;Clients Issues&quot;,&quot;view&quot;:true,&quot;readonly&quot;:false,&quot;expiry&quot;:&quot;indefinate_expiry&quot;,&quot;expiry_date&quot;:&quot;&quot;}]">
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
                                                                    <th>Role Expiry</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <th rowspan="4" scope="row">1</th>
                                                                    <td><label for="my_clients_option" class="form-label"><b>Clients</b></label></td>
                                                                    <td><input class="" type="checkbox" checked id="clients_option_view"></td>
                                                                    <td><input class="" type="checkbox"  id="clients_option_readonly"></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><label for="my_clients_option" class="form-label"><b>My Clients</b></label></td>
                                                                    <td><input class="all_view client_options" checked type="checkbox" id="my_clients_option_view"></td>
                                                                    <td><input class="all_readonly client_options_2"  type="checkbox" id="my_clients_option_readonly"></td>
                                                                    <td>
                                                                        <div class="container" id="dropdown_roles_1">
                                                                            <select id="select_expiry_1" class="form-control dropdown_roles">
                                                                                <option value="" hidden>Select Role Expiry</option>
                                                                                <option selected value="indefinate_expiry">Indefinate Expiry</option>
                                                                                <option value="definate_expiry">Definate Expiry</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="container hide" id="dropdown_date_1">
                                                                            <input type="hidden" id="menu_label_value_1" value="My Clients">
                                                                            <input class="form-control selected_date_time_roles" type="datetime-local" id="select_date_time_1" placeholder="Select date and time">
                                                                            <p id="back_to_dropdown_1" style="width: fit-content; cursor: pointer;" class="back_to_dropdown text-primary mt-1"><i class="fa fa-arrow-left"></i> back</p>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><label for="my_clients_option" class="form-label"><b>Quick Register</b></label></td>
                                                                    <td><input class="all_view client_options" checked type="checkbox" id="quick_register_view"></td>
                                                                    <td><input class="all_readonly client_options_2" type="checkbox" id="quick_register_readonly"></td>
                                                                    <td>
                                                                        <div class="container" id="dropdown_roles_2">
                                                                            <select id="select_expiry_2" class="form-control dropdown_roles">
                                                                                <option value="" hidden>Select Role Expiry</option>
                                                                                <option selected value="indefinate_expiry">Indefinate Expiry</option>
                                                                                <option value="definate_expiry">Definate Expiry</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="container hide" id="dropdown_date_2">
                                                                            <input type="hidden" id="menu_label_value_2" value="Quick Register">
                                                                            <input class="form-control selected_date_time_roles" type="datetime-local" id="select_date_time_2" placeholder="Select date and time">
                                                                            <p id="back_to_dropdown_2" style="width: fit-content; cursor: pointer;" class="back_to_dropdown text-primary mt-1"><i class="fa fa-arrow-left"></i> back</p>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><label for="my_clients_option" class="form-label"><b>Clients Issues</b></label></td>
                                                                    <td><input class="all_view client_options" checked type="checkbox" id="clients_issues_view"></td>
                                                                    <td><input class="all_readonly client_options_2" type="checkbox" id="clients_issues_readonly"></td>
                                                                    <td>
                                                                        <div class="container" id="dropdown_roles_3">
                                                                            <select id="select_expiry_3" class="form-control dropdown_roles">
                                                                                <option value="" hidden>Select Role Expiry</option>
                                                                                <option selected value="indefinate_expiry">Indefinate Expiry</option>
                                                                                <option value="definate_expiry">Definate Expiry</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="container hide" id="dropdown_date_3">
                                                                            <input type="hidden" id="menu_label_value_3" value="Clients Issues">
                                                                            <input class="form-control selected_date_time_roles" type="datetime-local" id="select_date_time_3" placeholder="Select date and time">
                                                                            <p id="back_to_dropdown_3" style="width: fit-content; cursor: pointer;" class="back_to_dropdown text-primary mt-1"><i class="fa fa-arrow-left"></i> back</p>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <th rowspan="3" scope="row">2</th>
                                                                    <td ><label for="my_clients_option" class="form-label"><b>Accounts</b></label></td>
                                                                    <td><input class="" checked type="checkbox" id="accounts_option_view"></td>
                                                                    <td><input class=""  type="checkbox" id="accounts_option_readonly"></td>
                                                                </tr>
                                                                <tr>
                                                                    <td ><label for="my_clients_option" class="form-label"><b><i>Transactions</i></b></label></td>
                                                                    <td><input class="all_view account_options" checked type="checkbox" id="transactions_option_view"></td>
                                                                    <td><input class="all_readonly account_options_2"  type="checkbox" id="transactions_option_readonly"></td>
                                                                    <td>
                                                                        <div class="container" id="dropdown_roles_4">
                                                                            <select id="select_expiry_4" class="form-control dropdown_roles">
                                                                                <option value="" hidden>Select Role Expiry</option>
                                                                                <option selected value="indefinate_expiry">Indefinate Expiry</option>
                                                                                <option value="definate_expiry">Definate Expiry</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="container hide" id="dropdown_date_4">
                                                                            <input type="hidden" id="menu_label_value_4" value="Transactions">
                                                                            <input class="form-control selected_date_time_roles" type="datetime-local" id="select_date_time_4" placeholder="Select date and time">
                                                                            <p id="back_to_dropdown_4" style="width: fit-content; cursor: pointer;" class="back_to_dropdown text-primary mt-1"><i class="fa fa-arrow-left"></i> back</p>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td ><label for="my_clients_option" class="form-label"><b><i>Expenses</i></b></label></td>
                                                                    <td><input class="all_view account_options" checked type="checkbox" id="expenses_option_view"></td>
                                                                    <td><input class="all_readonly account_options_2"  type="checkbox" id="expenses_option_readonly"></td>
                                                                    <td>
                                                                        <div class="container" id="dropdown_roles_5">
                                                                            <select id="select_expiry_5" class="form-control dropdown_roles">
                                                                                <option value="" hidden>Select Role Expiry</option>
                                                                                <option selected value="indefinate_expiry">Indefinate Expiry</option>
                                                                                <option value="definate_expiry">Definate Expiry</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="container hide" id="dropdown_date_5">
                                                                            <input type="hidden" id="menu_label_value_5" value="Expenses">
                                                                            <input class="form-control selected_date_time_roles" type="datetime-local" id="select_date_time_5" placeholder="Select date and time">
                                                                            <p id="back_to_dropdown_5" style="width: fit-content; cursor: pointer;" class="back_to_dropdown text-primary mt-1"><i class="fa fa-arrow-left"></i> back</p>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row">3</th>
                                                                    <td ><label for="my_clients_option" class="form-label"><b>My Routers</b></label></td>
                                                                    <td><input class="all_view" checked type="checkbox" id="my_routers_option_view"></td>
                                                                    <td><input class="all_readonly" type="checkbox" id="my_routers_option_readonly"></td>
                                                                    <td>
                                                                        <div class="container" id="dropdown_roles_6">
                                                                            <select id="select_expiry_6" class="form-control dropdown_roles">
                                                                                <option value="" hidden>Select Role Expiry</option>
                                                                                <option selected value="indefinate_expiry">Indefinate Expiry</option>
                                                                                <option value="definate_expiry">Definate Expiry</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="container hide" id="dropdown_date_6">
                                                                            <input type="hidden" id="menu_label_value_6" value="My Routers">
                                                                            <input class="form-control selected_date_time_roles" type="datetime-local" id="select_date_time_6" placeholder="Select date and time">
                                                                            <p id="back_to_dropdown_6" style="width: fit-content; cursor: pointer;" class="back_to_dropdown text-primary mt-1"><i class="fa fa-arrow-left"></i> back</p>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row">4</th>
                                                                    <td ><label for="my_clients_option" class="form-label"><b>SMS</b></label></td>
                                                                    <td><input class="all_view" checked type="checkbox" id="sms_option_view"></td>
                                                                    <td><input class="all_readonly" type="checkbox" id="sms_option_readonly"></td>
                                                                    <td>
                                                                        <div class="container" id="dropdown_roles_7">
                                                                            <select id="select_expiry_7" class="form-control dropdown_roles">
                                                                                <option value="" hidden>Select Role Expiry</option>
                                                                                <option selected value="indefinate_expiry">Indefinate Expiry</option>
                                                                                <option value="definate_expiry">Definate Expiry</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="container hide" id="dropdown_date_7">
                                                                            <input type="hidden" id="menu_label_value_7" value="SMS">
                                                                            <input class="form-control selected_date_time_roles" type="datetime-local" id="select_date_time_7" placeholder="Select date and time">
                                                                            <p id="back_to_dropdown_7" style="width: fit-content; cursor: pointer;" class="back_to_dropdown text-primary mt-1"><i class="fa fa-arrow-left"></i> back</p>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row">5</th>
                                                                    <td ><label for="my_clients_option" class="form-label"><b>Account & Profile</b></label></td>
                                                                    <td><input class="all_view" checked type="checkbox" id="account_profile_option_view"></td>
                                                                    <td><input class="all_readonly" type="checkbox" id="account_profile_option_readonly"></td>
                                                                    <td>
                                                                        <div class="container" id="dropdown_roles_8">
                                                                            <select id="select_expiry_8" class="form-control dropdown_roles">
                                                                                <option value="" hidden>Select Role Expiry</option>
                                                                                <option selected value="indefinate_expiry">Indefinate Expiry</option>
                                                                                <option value="definate_expiry">Definate Expiry</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="container hide" id="dropdown_date_8">
                                                                            <input type="hidden" id="menu_label_value_8" value="Account and Profile">
                                                                            <input class="form-control selected_date_time_roles" type="datetime-local" id="select_date_time_8" placeholder="Select date and time">
                                                                            <p id="back_to_dropdown_8" style="width: fit-content; cursor: pointer;" class="back_to_dropdown text-primary mt-1"><i class="fa fa-arrow-left"></i> back</p>
                                                                        </div>
                                                                    </td>
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
                                                {{-- <button class="btn btn-success" type="submit"><i class="ft-plus"></i> Register Organization</button> --}}
                                                @php
                                                    $btnText = "<i class=\"ft-plus\"></i> Register Organization";
                                                    $otherClasses = "";
                                                    $btn_id = "";
                                                    $btnSize="sm";
                                                    $type = "submit";
                                                    $readonly = "";
                                                    $otherAttributes = "";
                                                @endphp
                                                <x-button toolTip="" btnType="success" :otherAttributes="$otherAttributes" :btnText="$btnText" :type="$type" :btnSize="$btnSize" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                            </div>
                                            <div class="col-md-6">
                                                @php
                                                    $btnText = "<i class=\"ft-x\"></i> Cancel";
                                                    $otherClasses = "my-1 ";
                                                    $btnLink = route("Packages");
                                                    $otherAttributes = "";
                                                    $readonly = "";
                                                @endphp
                                                <x-button-link :otherAttributes="$otherAttributes"  :btnText="$btnText" :btnLink="$btnLink" btnType="secondary" btnSize="sm" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                                {{-- <a class="btn btn-secondary btn-outline" href="{{route("Packages")}}"
                                                    type="button"><i class="ft-x"></i> Cancel</a> --}}
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