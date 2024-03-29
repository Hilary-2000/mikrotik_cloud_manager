<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description"
        content="My ISP is the number one kenyan webserver software that helps you manage and monitor your webserver.">
    <meta name="keywords"
        content="admin template, Client template, dashboard template, gradient admin template, responsive client template, webapp, eCommerce dashboard, analytic dashboard">
    <meta name="author" content="ThemeSelect">
    <title>Hypbits - {{ucwords(strtolower($organization_details->organization_name))}} Client Details - PPPoE Assignment</title>
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
    <style>
        .hide:{
            display: none;
        }
    </style>
</head>

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
    $readonly = readOnly($priviledges,"My Clients");

    function isJson($string) {
        return ((is_string($string) &&
                (is_object(json_decode($string)) ||
                is_array(json_decode($string))))) ? true : false;
    }
@endphp
<style>
    /*the container must be positioned relative:*/
    .autocomplete {
        position: relative;
        display: inline-block;
        width: 100%
    }

    .autocomplete-items {
        position: absolute;
        border: 1px solid #d4d4d4;
        border-bottom: none;
        border-top: none;
        z-index: 99;
        /*position the autocomplete items to be the same width as the container:*/
        top: 100%;
        left: 0;
        right: 0;
    }

    .autocomplete-items div {
        padding: 10px;
        cursor: pointer;
        background-color: #fff;
        border-bottom: 1px solid #d4d4d4;
    }

    /*when hovering an item:*/
    .autocomplete-items div:hover {
        background-color: #e9e9e9;
    }

    /*when navigating through the items using the arrow keys:*/
    .autocomplete-active {
        background-color: DodgerBlue !important;
        color: #ffffff;
    }

</style>
<body class="vertical-layout vertical-menu 2-columns  menu-expanded fixed-navbar" data-open="click"
    data-menu="vertical-menu" data-color="bg-chartbg" data-col="2-columns">

    <!-- fixed-top-->
        <x-navbar/>
    <!-- ////////////////////////////////////////////////////////////////////////////-->


    <div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true"
        data-img="/theme-assets/images/backgrounds/02.jpg">
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
                    <h3 class="content-header-title">{{ucwords(strtolower($organization_details->organization_name))}} Client Details</h3>
                </div>
                <div class="content-header-right col-md-8 col-12">
                    <div class="breadcrumbs-top float-md-right">
                        <div class="breadcrumb-wrapper mr-1">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/Dashboard">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item"><a href="{{url()->previous();}}">{{ucwords(strtolower($organization_details->organization_name))}} Client Details</a>
                                </li>
                                <li class="breadcrumb-item">View Clients
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
                                <h4 id="view_clients_inform" class="card-title">View <span class="text-secondary">"{{ ucwords(strtolower($clients_data[0]->client_name)) }}" <small data-toggle='tooltip' title="Mikrorik Cloud Manager Client View" class="badge bg-danger text-sm">MCMCV</small></span></h4>
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
                                    @if ($errors->any())
                                        <h6 style="color: orangered">Errors</h6>
                                        <ul class="text-danger" style="color: orangered">
                                            @foreach ($errors->all() as $item)
                                                <li>{{ $item }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    </ul>
                                    
                                    {{-- client statuses --}}
                                        <x-clientinforstatus :organizationdetails="$organization_details" :registrationdate="$registration_date" :expiredate="$expire_date " :clientsdata="$clients_data" :readonly="$readonly"/>
                                    {{-- end --}}
                                    <hr>
                                    <p><strong>Note: </strong><br> - Some fields can`t be left blank the default
                                        configuration is surounded with the {curly braces} you may select that if you
                                        dont want to change anything</small><br>
                                        - The upload and download speed might not work because of the fast track in
                                        firewall filter. <br>
                                        - Fill all the fields to update the client. <br>
                                        - When the "Allow router change" is not checked the changes will only be made in
                                        the database
                                    </p>
                                    <form class="form-group" action="/updateClients" method="POST">
                                        @csrf
                                        <input type="hidden" name="clients_id"
                                            value="{{ $clients_data[0]->client_id }}">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <input type="checkbox" name="allow_router_changes"
                                                    id="allow_router_changes" checked>
                                                <label for="allow_router_changes"
                                                    class="form-control-label text-primary"
                                                    style="font-weight: 800;cursor: pointer;">Apply changes to
                                                    router</label>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 form-group">
                                                <label for="client_name" class="form-control-label">Clients Fullname {
                                                    <span
                                                        class="primary">{{ $clients_data[0]->client_name }}</span>
                                                    }</label>
                                                <input type="text" name="client_name" id="client_name"
                                                    class="form-control rounded-lg p-1"
                                                    placeholder="Clients Fullname .." required
                                                    value="{{ old('client_name') }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="client_address" class="form-control-label">Clients Address
                                                    { <span
                                                        class="primary">{{ $clients_data[0]->client_address }}</span>
                                                    }</label>
                                                <input type="text" name="client_address" id="client_address"
                                                    class="form-control rounded-lg p-1" placeholder="Client location"
                                                    required value="{{ old('client_address') }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="location_coordinates" class="form-control-label">Location
                                                    co-ordinates { <span
                                                        class="primary">{{ $clients_data[0]->location_coordinates ?? '' }}</span>
                                                    }</label>
                                                <input type="text" name="location_coordinates"
                                                    onkeypress="return isNumber(event)" id="location_coordinates"
                                                    class="form-control rounded-lg p-1"
                                                    placeholder="Exclude All special characters"
                                                    value="{{ $clients_data[0]->location_coordinates ?? '' }}"
                                                    onpaste="return pasted(event,'location_coordinates');">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 form-group">
                                                <label for="client_phone" class="form-control-label">Clients Phone
                                                    number { <span
                                                        class="primary">{{ $clients_data[0]->clients_contacts }}</span>
                                                    }</label>
                                                <input type="number" name="client_phone" id="client_phone"
                                                    class="form-control rounded-lg p-1"
                                                    placeholder="Client valid phone number" required
                                                    value="{{ old('client_phone') }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="client_account_number" class="form-control-label">Clients
                                                    Account Number { <span
                                                        class="primary">{{ $clients_data[0]->client_account }}</span>
                                                    }</label>
                                                <input type="text" name="client_account_number"
                                                    id="client_account_number" class="form-control rounded-lg p-1"
                                                    placeholder="Client account number" readonly
                                                    value="{{ $clients_data[0]->client_account }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="client_monthly_pay" class="form-control-label">Clients
                                                    Monthly Payment { <span
                                                        class="primary">{{ $clients_data[0]->monthly_payment }}</span>
                                                    }</label>
                                                <input type="number" name="client_monthly_pay" id="client_monthly_pay"
                                                    class="form-control rounded-lg p-1"
                                                    placeholder="Client Monthly Payment" required
                                                    value="{{ old('client_monthly_pay') }}">
                                            </div>
                                        </div>
                                        <p></p>
                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                @if (session('network_error'))
                                                    <p class="danger">{{ session('network_error') }}</p>
                                                @endif
                                                <label  id="errorMsg" for="client_secret_username" class="form-control-label">Clients Username
                                                    { <span class="primary" id="secret_username"></span> }</label>
                                                <input type="text" name="client_secret_username" id="client_secret_username"
                                                    class="form-control rounded-lg p-1" placeholder="ex 10.10.30.0"
                                                    required value="{{ old('client_secret_username') }}">
                                            </div>
                                            <div class="col-md-6">
                                                <span class="d-none" id="secret_holder"></span>
                                                <label  id="errorMsg1" for="client_secret_password" class="form-control-label">Clients Secret Password {
                                                    <span class="primary" id="addresses"></span> } <button type="button" id="display_secret" class="btn btn-sm btn-infor"><span class="text-secondary"><i class="fas fa-eye"></i></span></button></label>
                                                <input type="password" name="client_secret_password" id="client_secret_password"
                                                    class="form-control rounded-lg p-1" placeholder="ex 10.10.30.1/24"
                                                    required value="{{ old('client_secret_password') }}">
                                            </div>
                                        </div>
                                        <div class="row my-1">
                                            <div class="col-md-6 form-group">
                                                <label for="router_name" class="form-control-label">Router Name: {
                                                    <span class="primary bolder" id="router_named">Hilary Dev</span> }
                                                    <span class="invisible" id="interface_load"><i
                                                            class="fas ft-rotate-cw fa-spin"></i></span></label>
                                                <p id="router_data"><span class="secondary">The router list will
                                                        appear here.. If this message is still present you have no
                                                        routers present in your database.</span></p>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="client_address" class="form-control-label">Router
                                                    Profile: { <span class="primary bolder"
                                                        id="router_profiles"></span> } </label>
                                                <p class="text-secondary" id="interface_holder">The router secret profiles
                                                    will appear here If the router is selected.If this message is still
                                                    present a router is not selected.</p>
                                            </div>
                                        </div>
                                        <div class="row my-1">
                                            <div class="col-md-12">
                                                <label for="client_address"
                                                    class="form-control-label">Comments:</label>
                                                <textarea name="comments" id="comments" cols="30" rows="3" class="form-control"
                                                    placeholder="Comment here"></textarea>
                                            </div>
                                        </div>
                                        <div class="row my-1">
                                            <div class="col-md-6">
                                                <label for="client_username" class="form-control-label">Client
                                                    Username</label>
                                                <input type="text" name="client_username" id="client_username"
                                                    class="form-control" required placeholder="Client`s Username"
                                                    readonly>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="client_password" class="form-control-label">Client`s
                                                    Password</label>
                                                <input type="password" name="client_password" id="client_password"
                                                    class="form-control" required placeholder="Client`s Password">
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <button {{$readonly}} class="btn btn-success text-dark " disabled type="submit"><i
                                                        class="ft-upload"></i> Update User</button>
                                            </div>
                                            <div class="col-md-6">
                                                <a class="btn btn-secondary btn-outline" href="{{url()->previous()}}"><i
                                                        class="ft-x"></i> Cancel</a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Basic Tables end -->
                {{-- Transactions done by the client --}}

            </div>
            <div class="content-body {{count($reffer_details)>0 ? "":"d-none" }}">
                <!-- Basic Tables start -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 id="view_clients_inform" class="card-title"><span class="text-secondary">{{ ucwords(strtolower($clients_data[0]->client_name)) }}</span>`s refferee</h4>
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
                                    @if ($errors->any())
                                        <h6 style="color: orangered">Errors</h6>
                                        <ul class="text-danger" style="color: orangered">
                                            @foreach ($errors->all() as $item)
                                                <li>{{ $item }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    </ul>
                                    <a href="/Clients" class="btn btn-infor"><i class="fas fa-arrow-left"></i> Back
                                        to list</a>
                                    @if (session('success'))
                                        <p class="success">{{ session('success') }}</p>
                                    @endif
                                    @if (session('error'))
                                        <p class="danger">{{ session('error') }}</p>
                                    @endif
                                    <div class="row">
                                        <div class="col-md-9">
                                            <p><strong>Note: </strong><br> 
                                                - View user payment history.
                                            </p>
                                        </div>
                                    </div>
                                    <div class="row my-2 w-50">
                                        <input type="hidden" name="clients_id"
                                    value="{{ $clients_data[0]->client_id }}">
                                        <input type="hidden" name="refferal_account_no" id="refferer_acc_no2">
                                        <div class="col-md-6">
                                            <p><b>Refferer Fullname :</b></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="user_data" id="refferer_name">{{$reffer_details[0] ?? 'Unknown'}}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><b>Refferer Acc No : </b></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="user_data" id="refferer_acc_no">{{$reffer_details[1] ?? 'Unknown'}}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><b>Refferer wallet :</b></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="user_data" id="reffer_wallet">{{$reffer_details[2] ?? 'Unknown'}}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><b>Refferer Location :</b></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="user_data" id="refferer_location">{{$reffer_details[3] ?? 'Unknown'}}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-8 form-group row border-right border-dark">
                                        <div class="col-md-6">
                                            <input type="text" name="search" id="searchkey" class="form-control rounded-lg " placeholder="Your keyword ..">
                                        </div>
                                    </div>
                                    <div class="table-responsive" id="transDataReciever">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Amount</th>
                                                    <th>Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <th scope="row">1</th>
                                                    <td>Kes 10,100</td>
                                                    <td>Mon 10th June 2022 10:48:00 AM</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">2</th>
                                                    <td>Kes 10,100</td>
                                                    <td>Mon 10th June 2022 10:48:00 AM</td>
                                                </tr>
                                            </tbody>
                                        </table>
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
                                        <p class="card-text text-xxs">Showing from <span class="text-primary" id="startNo">1</span> to <span class="text-secondary"  id="finishNo">10</span> records of <span  id="tot_records" class="d-none">56</span></p>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Basic Tables end -->
                {{-- Transactions done by the client --}}

            </div>
            <div class="card p-1 {{count($reffered_list) > 0 ? "" : "d-none"}}">
                <h4 class="text-center text-dark">Refferer List</h4>
                @for ($i = 0; $i < count($reffered_list); $i++)
                    {{-- get the client information --}}
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 id="view_clients_inform" class="card-title">{{$i+1}})  Reffered : {{$reffered_list[$i]->reffered->client_name}}</h4>
                                </div>
                                <div class="card-content collapse show">
                                    <div class="card-body">
                                        @if ($errors->any())
                                            <h6 style="color: orangered">Errors</h6>
                                            <ul class="text-danger" style="color: orangered">
                                                @foreach ($errors->all() as $item)
                                                    <li>{{ $item }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                        </ul>
                                        @if (session('success'))
                                            <p class="success">{{ session('success') }}</p>
                                        @endif
                                        @if (session('error'))
                                            <p class="danger">{{ session('error') }}</p>
                                        @endif
                                        <div class="row">
                                            <div class="col-md-9">
                                                <p><strong>Note: </strong><br> 
                                                    - View user payment history.
                                                </p>
                                            </div>
                                        </div>
                                        <div class="row my-2 w-50">
                                            <input type="hidden" name="clients_id"
                                        value="{{ $clients_data[0]->client_id }}">
                                            <input type="hidden" name="refferal_account_no" id="">
                                            <div class="col-md-6">
                                                <p><b>Refferer Fullname :</b></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="user_data" id="">{{$reffered_list[$i]->reffered->client_name}}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><b>Refferer Acc No : </b></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="user_data" id="">{{$reffered_list[$i]->reffered->client_account}}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><b>Refferer wallet :</b></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="user_data" id="">Kes {{$reffered_list[$i]->reffered->wallet_amount}}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><b>Refferer Location :</b></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="user_data" id="">{{$reffered_list[$i]->reffered->client_address}}</p>
                                            </div>
                                        </div>
                                        <div class="table-responsive" id="">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Amount</th>
                                                        <th>Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @for ($index = 0; $index < count($reffered_list[$i]->payment_history); $index++)
                                                        <tr>
                                                            <th scope="row">{{$index+1}}</th>
                                                            <td>Kes {{number_format($reffered_list[$i]->payment_history[$index]->amount)}}</td>
                                                            <td>{{date("D dS M  H:i:s A",$reffered_list[$i]->payment_history[$index]->date)}}</td>
                                                        </tr>
                                                    @endfor
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                @endfor
                <hr>
            </div>
        </div>
    </div>
    <!-- ////////////////////////////////////////////////////////////////////////////-->
    <!-- The footer -->
        <x-footer/>
    <!-- ////////////////////////// -->

    <!-- BEGIN VENDOR JS-->
    <script src="/theme-assets/vendors/js/vendors.min.js" type="text/javascript"></script>


    {{-- START OF THE ROUTER DATA RETRIEVAL --}}
    <script>
        var clients_data = @json($clients_data ?? '');
        // console.log(clients_data);

        // display the router data
        var router_data = @json($router_data ?? '');
        var data_to_display =
            "<select name='router_name' class='form-control' id='router_name' required ><option value='' hidden>Select an option</option>";
        for (let index = 0; index < router_data.length; index++) {
            const element = router_data[index];
            data_to_display += "<option class='router_id_infor' value='" + element['router_id'] + "'>" + element[
                'router_name'] + "</option>";
        }
        data_to_display += "</select>";
        var router_data = document.getElementById("router_data");
        router_data.innerHTML = data_to_display;
    </script>
    <script>
        // only the special characters allowed
        function isNumber(evt) {
            evt = (evt) ? evt : window.event;
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            console.log(charCode);
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                if (charCode == 45 || charCode == 44 || charCode == 46) {
                    return true;
                }
                return false;
            }
            return true;
        }
        // check if the field is pasted
        function pasted(e,id) {
            var clipboardData, pastedData;
            // console.log(id);
            // Stop data actually being pasted into div
            e.stopPropagation();
            e.preventDefault();

            // Get pasted data via clipboard API
            clipboardData = e.clipboardData || window.clipboardData;
            pastedData = clipboardData.getData('Text');

            // Do whatever with pasteddata
            // go for character by character and take only characters that are of cetain type
            // alert(pastedData);
            var data_accept = "";
            var strlen = pastedData.length;
            for (let index = 0; index < strlen; index++) {
                var crcode = pastedData.charCodeAt(index);
                if (crcode > 31 && (crcode < 48 || crcode > 57)) {
                    if (crcode == 45 || crcode == 44 || crcode == 46) {
                        data_accept+=pastedData.charAt(index);
                    }
                }else{
                    data_accept+=pastedData.charAt(index);
                }
            }
            document.getElementById("location_coordinates").value = data_accept;
        }
    </script>
    <script src="/theme-assets/js/core/viewclientpppoe.js" type="text/javascript"></script>
    <script src="/theme-assets/js/core/app-menu-lite.js" type="text/javascript"></script>
    <script src="/theme-assets/js/core/app-lite.js" type="text/javascript"></script>
    <script>
        var client_names = @json($clients_names ?? '');
        var client_contacts = @json($clients_contacts ?? '');
        var client_account = @json($clients_account ?? '');
        var refferal_payment = @json($refferal_payment ?? '');
        var reffered_list = @json($reffered_list ?? '');
        console.log(reffered_list);
    </script>
    <script src="/theme-assets/js/core/refferer.js"></script>
    <script>
        function autocomplete(inp, arr, arr2, arr3) {
            /*the autocomplete function takes two arguments,
            the text field element and an array of possible autocompleted values:*/
            var currentFocus;
            /*execute a function when someone writes in the text field:*/
            inp.addEventListener("input", function(e) {
                var a, b, i, val = this.value;
                /*close any already open lists of autocompleted values*/
                closeAllLists();
                if (!val) {
                    return false;
                }
                currentFocus = -1;
                /*create a DIV element that will contain the items (values):*/
                a = document.createElement("DIV");
                a.setAttribute("id", this.id + "autocomplete-list");
                a.setAttribute("class", "autocomplete-items");
                /*append the DIV element as a child of the autocomplete container:*/
                this.parentNode.appendChild(a);
                /*for each item in the array...*/
                var counter = 0;
                for (i = 0; i < arr.length; i++) {
                    if (counter > 10) {
                        break;
                    }
                    /*check if the item starts with the same letters as the text field value:*/
                    if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase() ||
                        arr2[i].substr(0, val.length).toUpperCase() == val.toUpperCase() ||
                        arr3[i].substr(0, val.length).toUpperCase() == val.toUpperCase()
                    ) {
                        /*create a DIV element for each matching element:*/
                        b = document.createElement("DIV");
                        /*make the matching letters bold:*/
                        b.innerHTML = /**"<strong>" +*/ arr3[i] + " (" + arr[i] + ") - " + arr2[
                            i] /**.substr(0, val.length)*/ /**+ "</strong>"*/ ;
                        // b.innerHTML += arr[i].substr(val.length);
                        /*insert a input field that will hold the current array item's value:*/
                        b.innerHTML += "<input type='hidden' value='" + arr2[i] + "'>";
                        /*execute a function when someone clicks on the item value (DIV element):*/
                        b.addEventListener("click", function(e) {
                            /*insert the value for the autocomplete text field:*/
                            inp.value = this.getElementsByTagName("input")[0].value;
                            /*close the list of autocompleted values,
                            (or any other open lists of autocompleted values:*/
                            closeAllLists();
                            getUser();
                        });
                        a.appendChild(b);
                        counter++;
                    }
                    console.log(counter);
                }
            });
            /*execute a function presses a key on the keyboard:*/
            inp.addEventListener("keydown", function(e) {
                var x = document.getElementById(this.id + "autocomplete-list");
                if (x) x = x.getElementsByTagName("div");
                if (e.keyCode == 40) {
                    /*If the arrow DOWN key is pressed,
                    increase the currentFocus variable:*/
                    currentFocus++;
                    /*and and make the current item more visible:*/
                    addActive(x);
                } else if (e.keyCode == 38) { //up
                    /*If the arrow UP key is pressed,
                    decrease the currentFocus variable:*/
                    currentFocus--;
                    /*and and make the current item more visible:*/
                    addActive(x);
                } else if (e.keyCode == 13) {
                    /*If the ENTER key is pressed, prevent the form from being submitted,*/
                    e.preventDefault();
                    if (currentFocus > -1) {
                        /*and simulate a click on the "active" item:*/
                        if (x) x[currentFocus].click();
                    }
                }
            });

            function addActive(x) {
                /*a function to classify an item as "active":*/
                if (!x) return false;
                /*start by removing the "active" class on all items:*/
                removeActive(x);
                if (currentFocus >= x.length) currentFocus = 0;
                if (currentFocus < 0) currentFocus = (x.length - 1);
                /*add class "autocomplete-active":*/
                x[currentFocus].classList.add("autocomplete-active");
            }

            function removeActive(x) {
                /*a function to remove the "active" class from all autocomplete items:*/
                for (var i = 0; i < x.length; i++) {
                    x[i].classList.remove("autocomplete-active");
                }
            }

            function closeAllLists(elmnt) {
                /*close all autocomplete lists in the document,
                except the one passed as an argument:*/
                var x = document.getElementsByClassName("autocomplete-items");
                for (var i = 0; i < x.length; i++) {
                    if (elmnt != x[i] && elmnt != inp) {
                        x[i].parentNode.removeChild(x[i]);
                    }
                }
            }
            /*execute a function when someone clicks in the document:*/
            document.addEventListener("click", function(e) {
                closeAllLists(e.target);
            });
        }

        /*An array containing all the country names in the world:*/
        var countries = client_contacts;

        /*initiate the autocomplete function on the "search_refferer_keyword" element, and pass along the countries array as possible autocomplete values:*/
        autocomplete(document.getElementById("search_refferer_keyword"), client_contacts, client_account, client_names);
    </script>
    <script>
      var milli_seconds = 1200;
      setInterval(() => {
          if (milli_seconds == 0) {
              window.location.href = "/";
          }
          milli_seconds--;
      }, 1000);
    </script>
    <script>
        var freeze_type = document.getElementById("freeze_type");
        freeze_type.onchange = function () {
            var freeze_window = document.getElementById("freeze_window");
            if(this.value == "Indefinite"){
                freeze_window.classList.add("d-none");
            }else{
                freeze_window.classList.remove("d-none");
            }
        }
    </script>
</body>

</html>
