<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="My ISP is the number one kenyan webserver software that helps you manage and monitor your webserver.">
    <meta name="keywords" content="admin template, Client template, dashboard template, gradient admin template, responsive client template, webapp, eCommerce dashboard, analytic dashboard">
    <meta name="author" content="ThemeSelect">
    <title>Hypbits - Transfer funds</title>
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
        html{
            scroll-behavior: smooth;
        }
        .hide{
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
    $readonly = readOnly($priviledges,"Transactions");

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
                    <h3 class="content-header-title">Confirm Bill Transfer</h3>
                </div>
                <div class="content-header-right col-md-8 col-12">
                    <div class="breadcrumbs-top float-md-right">
                        <div class="breadcrumb-wrapper mr-1">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/Dashboard">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item"><a href="/Transactions">My Transaction</a>
                                </li>
                                <li class="breadcrumb-item">Confirm Bill Transfer
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
                                <h4 class="card-title">Confirm Bill Transfer</h4>
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
                                    <a href="/Transactions/View/{{$transaction_id}}" class="btn btn-infor"><i class="fas fa-arrow-left"></i> Back</a>
                                    <p><strong>Note</strong><br>- Confirm the data below is correct before confirming the transfer!</p>
                                </div>
                                <div class="card-body row">
                                    <div class="col-lg-6 row">
                                        <div class="col-md-12">
                                            <input type="hidden" id="transaction_id" value="{{$transaction_details[0]->transaction_id}}">
                                            <h6 class="text-primary"><strong><u>Transaction Detail</u></strong></h6>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Transaction Code: </strong></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p>{{$transaction_details[0]->transaction_mpesa_id}}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Transaction Amount: </strong></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p>Kes {{$transaction_details[0]->transacion_amount}}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Transaction Date: </strong></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p>{{$transaction_date}}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Transaction Account: </strong></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p>{{$transaction_details[0]->transaction_account}}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>MSISDN: </strong></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p>{{$transaction_details[0]->phone_transacting}}</p>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 row">
                                        <div class="col-md-12">
                                            <input type="hidden" id="client_id" value="{{$organization_data[0]->organization_id}}">
                                            <h6 class="text-primary"><strong><u>Clients Detail</u></strong></h6>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Client Name: </strong></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p>{{$organization_data[0]->organization_name}}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Wallet Balance: </strong></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p>Kes {{$organization_data[0]->wallet}}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Client`s Package: </strong></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p>{{$organization_data[0]->package_name}}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Client Account No: </strong></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p>{{$organization_data[0]->account_no}}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Client`s Next Expiration Date: </strong></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p>{{$expiration_date}}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body row">
                                    <div class="col-md-6">
                                        <a href="{{route("confirm_transfer",[$organization_data[0]->organization_id,$transaction_details[0]->transaction_id])}}" class="btn btn-primary {{$readonly}}">Confirm Transfer</a>
                                    </div>
                                    <div class="col-md-6">
                                        <a href="/Transactions/View/{{$transaction_id}}" class="btn btn-danger">Cancel</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- footer --}}
        <x-footer/>
    {{-- footer --}}
    <script src="/theme-assets/vendors/js/vendors.min.js" type="text/javascript"></script>

    {{-- transfer the php value to js --}}
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