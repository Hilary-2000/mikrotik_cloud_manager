<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        {{-- <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests"> --}}
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
        <meta name="description" content="My ISP is the number one kenyan webserver software that helps you manage and monitor your webserver.">
        <meta name="keywords" content="admin template, Client template, dashboard template, gradient admin template, responsive client template, webapp, eCommerce dashboard, analytic dashboard">
        <meta name="author" content="ThemeSelect">
        <title>Hypbits - "{{ucwords(strtolower($organization_details->organization_name))}}" Administrators</title>
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
        <!-- END Page Level CSS-->
        <!-- BEGIN Custom CSS-->
        <!-- END Custom CSS-->
    </head>
    <style>
        .hide{
            display: none;
        }
    </style>
    @php
        date_default_timezone_set('Africa/Nairobi');
        $privilleged = session("priviledges");
        $priviledges = ($privilleged);
        function showOption($priviledges,$name){
            if (isJson($priviledges)) {
                $priviledges = json_decode($priviledges);
                for ($index=0; $index < count($priviledges); $index++) { 
                    if ($priviledges[$index]->option == $name) {
                        if ($priviledges[$index]->view) {
                            return "";
                        }
                    }
                }
            }
            return "hide";
        }
        function readOnly($priviledges,$name){
            if (isJson($priviledges)){
                $priviledges = json_decode($priviledges);
                for ($index=0; $index < count($priviledges); $index++) { 
                    if ($priviledges[$index]->option == $name) {
                        if ($priviledges[$index]->readonly) {
                            return "disabled";
                        }
                    }
                }
            }
            return "";
        }

        // get the readonly value
        $readonly = readOnly($priviledges,"Account and Profile");
        function isJson($string) {
            return ((is_string($string) &&
                    (is_object(json_decode($string)) ||
                    is_array(json_decode($string))))) ? true : false;
        }
    @endphp
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
                        <h3 class="content-header-title">"{{ucwords(strtolower($organization_details->organization_name))}}" Administrators</h3>
                    </div>
                    <div class="content-header-right col-md-8 col-12">
                        <div class="breadcrumbs-top float-md-right">
                            <div class="breadcrumb-wrapper mr-1">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/Dashboard">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="/Clients">Account And Profile</a>
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
                                    <h4 class="card-title">"{{ucwords(strtolower($organization_details->organization_name))}}" Administrators</h4>
                                    <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                                    <input type="hidden" id="readonly_flag" value="{{$readonly}}">
                                    <div class="heading-elements">
                                        {{-- <button data-action="collapse" class="btn btn-primary"><i class="ft-plus"></i> Add Administrator</button> --}}
                                        <ul class="list-inline mb-0">
                                            <li><a class="btn btn-primary text-white" data-action="collapse"><i class="ft-plus"></i> Add Admin</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="card-content collapse">
                                    <div class="card-body">
                                        @if ($errors->any())
                                            <h6 style="color: orangered">Errors</h6>
                                            <ul class="text-danger" style="color: orangered">
                                                @foreach ($errors->all() as $item)
                                                    <li>{{$item}}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                        @if(session('network_presence'))
                                            <p class="text-danger">{{session('network_presence')}}</p>
                                        @endif
                                        @if(session('success'))
                                            <p class="text-success">{{session('success')}}</p>
                                        @endif
                                        <form class="border border-secondary rounded border-3 p-1" action="{{route("add_administrators",[$organization_details->organization_id])}}" method="post">
                                            <h6><strong>Add administrator</strong></h6>
                                            <p class="card-text">Fill all fields to add the Administrator.</p>
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-4 form-group">
                                                    <label for="admin_name" class="form-control-label">Fullname</label>
                                                    <input type="text" name="admin_name" id="admin_name" class="form-control rounded-lg p-1" placeholder="Admin Fullname .." required  >
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="client_address" class="form-control-label">Contacts</label>
                                                    <input type="text" name="client_address" id="client_address" class="form-control rounded-lg p-1" placeholder="Administrator contacts" required  >
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="admin_email" class="form-control-label">Administrator E-mail</label>
                                                    <input type="text" class="form-control" placeholder="e.g, mail@hypbits.com" id="admin_email" name="admin_email">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4 form-group">
                                                    <label for="admin_username" class="form-control-label">Username <span class="text-danger" id="error_acc_no"></span></label>
                                                    <input type="text" name="admin_username" id="admin_username" class="form-control rounded-lg p-1" placeholder="Administrator Username" required  >
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="admin_password" class="form-control-label">Password</label>
                                                    <input type="password" name="admin_password" id="admin_password" class="form-control rounded-lg p-1" placeholder="Administrator password" required >
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="user_status" class="form-control-label">Administrator Status</label>
                                                    <select name="user_status" id="user_status" class="form-control">
                                                        <option value="" hidden>Select status</option>
                                                        <option value="1">Active</option>
                                                        <option value="0">In-active</option>
                                                    </select>
                                                </div>
                                            </div>
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
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <button class="btn btn-primary" {{$readonly}} type="submit"><i class="ft-plus"></i> Add Administrator</button>
                                                </div>
                                            </div>
                                            <hr>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Basic Tables end -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <a href="{{route("ViewOrganization",[$organization_details->organization_id])}}" class="btn btn-infor"><i class="fas fa-arrow-left"></i> Back
                                        to Organization List</a>
                                    <h4 class="card-title">Administrator List <small data-toggle="tooltip" title="" class="badge bg-danger text-sm" data-original-title="Mikrorik Cloud Manager Admin View">MCMAV</small></h4>
                                    <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                                    <div class="heading-elements">
                                        <ul class="list-inline mb-0">
                                            <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                                            {{-- <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li> --}}
                                            <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                            <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
                                        </ul>
                                    </div>
                                </div>
                                <div class="card-content collapse show">
                                    <div class="card-body">
                                        @if(session('network_presence'))
                                            <p class="text-danger">{{session('network_presence')}}</p>
                                        @endif
                                        @if(session('success'))
                                            <p class="text-success">{{session('success')}}</p>
                                        @endif
                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <input type="text" name="search" id="searchkey" class="form-control rounded-lg p-1" placeholder="Search here ..">
                                            </div>
                                        </div>
                                        <div class="table-responsive" id="transDataReciever">
                                            <div class="container text-center my-2">
                                                <img class=" mx-auto fa-beat-fade"  width="100" alt="Your Logo Appear Here"
                                                    src="/theme-assets/images/logo.jpeg"/>
                                            </div>
                                        </div>
                                        <nav aria-label="Page navigation example" id="tablefooter">
                                            <ul class="pagination" id="datatable_paginate">
                                                <li class="page-item"  id="tofirstNav">
                                                    <a class="page-link" href="#" aria-label="Fisrt">
                                                        <span aria-hidden="true">&laquo; &laquo;</span>
                                                        <span class="sr-only">First</span>
                                                    </a>
                                                </li>
                                                <li class="page-item" id="toprevNac">
                                                    <a class="page-link" href="#" aria-label="Previous">
                                                        <span aria-hidden="true">&laquo;</span>
                                                        <span class="sr-only">Previous</span>
                                                    </a>
                                                </li>
                                                <li class="page-item"><button disabled class="page-link" id="pagenumNav">Page: 1</button></li>
                                                <li class="page-item">
                                                    <a class="page-link" href="#" aria-label="Next" id="tonextNav">
                                                        <span aria-hidden="true">&raquo;</span>
                                                        <span class="sr-only">Next</span>
                                                    </a>
                                                </li>
                                                <li class="page-item">
                                                    <a class="page-link" href="#" aria-label="Last Page"  id="tolastNav">
                                                        <span aria-hidden="true">&raquo;&raquo;</span>
                                                        <span class="sr-only">Next</span>
                                                    </a>
                                                </li>
                                            </ul>
                                            <p class="card-text text-xxs">Showing from <span class="text-primary" id="startNo">1</span> to <span class="text-secondary"  id="finishNo">10</span> records of <span  id="tot_records">56</span></p>
                                        </nav>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- end view administrators --}}
                </div>
            </div>
        </div>
        <!-- ////////////////////////////////////////////////////////////////////////////-->
        <!-- The footer -->
            <x-footer/>
        <!-- ////////////////////////// -->

        <!-- BEGIN VENDOR JS-->
        <script src="/theme-assets/vendors/js/vendors.min.js" type="text/javascript"></script>
        
        <script>
            var username = @json($username ?? '');
            var admin_data = @json($admin_data ?? '');
            var dates = @json($dates ?? '');
            var organization_details = @json($organization_details ?? []);
        </script>

        <script src="/theme-assets/js/core/addadmin.js"></script>
        <script src="/theme-assets/js/core/app-menu-lite.js" type="text/javascript"></script>
        <script src="/theme-assets/js/core/app-lite.js" type="text/javascript"></script>
        <script>
        var milli_seconds = 1200;
        setInterval(() => {
            if (milli_seconds == 0) {
                window.location.href = "/";
            }
            milli_seconds--;
        }, 1000);
        </script>
    </body>

</html>