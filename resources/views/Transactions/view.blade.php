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
        <title>Hypbits -View Transactions Details</title>
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
            html {
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

    <body class="vertical-layout vertical-menu 2-columns  menu-expanded fixed-navbar" data-open="click"
        data-menu="vertical-menu" data-color="bg-chartbg" data-col="2-columns">

        <!-- fixed-top-->
            <x-navbar/>
        <!-- ////////////////////////////////////////////////////////////////////////////-->
        
        
        <div class="main-menu menu-fixed menu-light menu-accordion menu-shadow " data-scroll-to-active="true" data-img="theme-assets/images/backgrounds/02.jpg">
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
                        <h3 class="content-header-title">View Transaction</h3>
                    </div>
                    <div class="content-header-right col-md-8 col-12">
                        <div class="breadcrumbs-top float-md-right">
                            <div class="breadcrumb-wrapper mr-1">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/Dashboard">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="/Transactions">My Transaction</a>
                                    </li>
                                    <li class="breadcrumb-item">View Transaction
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
                                    <h4 class="card-title">View Transactions</h4>
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
                                    <input type="hidden" value="{{$readonly}}" id="transaction_assign_flag">
                                    <div class="card-body">
                                        @if (session('success'))
                                            <p class="text-success">{{ session('success') }}</p>
                                        @endif
                                        <a href="{{route("Transactions")}}" class="btn btn-infor"><i class="fas fa-arrow-left"></i>
                                            Back to list</a>
                                    </div>
                                    <div class="card-body row">
                                        <input type="hidden" id="transaction_id"
                                            value="{{ $transaction_data[0]->transaction_id }}">
                                        <div class="col-md-6">
                                            @if ($transaction_data[0]->transaction_status == 1)
                                                <div class="row my-1">
                                                    <div class="col-sm-6"><strong>Transaction status:</strong></div>
                                                    <div class="col-sm-6">
                                                        <a href="#" {{$readonly}} class="btn btn-sm btn-success">Assigned</a>
                                                        {{-- <p class="text-success"><b>Assigned!</b></p> --}}
                                                    </div>
                                                </div>
                                            @else
                                                <div class="row my-1">
                                                    <div class="col-sm-6"><strong>Transaction status:</strong></div>
                                                    <div class="col-sm-6"><a href="#assign_transaction"
                                                            class="btn btn-sm btn-danger {{$readonly}}">Assign?</a>
                                                            {{-- <p class="text-danger"><b>Not-Assigned!</b></p> --}}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text">Assign the payment to a client if not assigned.</p>
                                        <p class="card-text">View the transaction details</p>
                                    </div>
                                    <div class="row card-body">
                                        <div class="col-md-6">
                                            <label for="" class="form-control-label">Payer Fullnames</label>
                                            <input type="text" name="" id="" class="form-control"
                                                placeholder="Payer Fullname" disabled
                                                value="{{ $transaction_data[0]->fullnames }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="" class="form-control-label">Account Owner Fullnames</label>
                                            <input type="text" name="" id="" class="form-control"
                                                placeholder="Payer Fullname" disabled value="{{ $user_fullname }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row card-body">
                                    <div class="col-md-6">
                                        <label for="transaction_id_mpesa" class="form-control-label">M-Pesa Id</label>
                                        <input type="text" name="transaction_id_mpesa" id="transaction_id_mpesa"
                                            value="{{ $transaction_data[0]->transaction_mpesa_id }}"
                                            class="form-control" placeholder="M-Pesa Id" disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="transaction_id_mpesa" class="form-control-label">Amount
                                            Transcated</label>
                                        <input type="text" name="transaction_id_mpesa" id="transaction_id_mpesa"
                                            class="form-control" value="{{ $transaction_data[0]->transacion_amount }}"
                                            placeholder="Amount Transacted" disabled>
                                    </div>
                                </div>
                                <div class="row card-body">
                                    <div class="col-md-6">
                                        <label for="transaction_id_mpesa" class="form-control-label">Transaction Date:
                                        </label>

                                        <input type="text" name="transaction_id_mpesa" id="transaction_id_mpesa"
                                            class="form-control" value="{{ $dates }}"
                                            placeholder="Transaction Date" disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="transaction_id_mpesa" class="form-control-label">Phone Number
                                            Paying:</label>
                                        <input type="text" name="transaction_id_mpesa" id="transaction_id_mpesa"
                                            class="form-control" value="{{ $transaction_data[0]->phone_transacting }}"
                                            placeholder="Phone number paying" disabled>
                                    </div>
                                </div>
                                <div class="row card-body">
                                    <div class="col-md-6">
                                        <label for="transaction_id_mpesa" class="form-control-label">Transaction Account
                                            Number</label>
                                        <input type="text" name="transaction_id_mpesa" id="transaction_id_mpesa"
                                            class="form-control" value="{{ $transaction_data[0]->transaction_account }}"
                                            placeholder="Transaction Account Number" disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="transaction_id_mpesa" class="form-control-label">Transaction Short
                                            Code</label>
                                        <input type="text" name="transaction_id_mpesa" id="transaction_id_mpesa"
                                            class="form-control"
                                            value="{{ $transaction_data[0]->transaction_short_code }}"
                                            placeholder="Paybill number used" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Basic Tables end -->
                {{-- Assign transaction --}}
                @if ($transaction_data[0]->transaction_status == 0)
                {{-- @if (false) --}}
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header" id="assign_transaction">
                                    <h4 class="card-title">Assign Transactions</h4>
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
                                        <div class="">
                                            <p><strong>Note:</strong><br>- Start by finding the client to assign the
                                                payment. <br>- By clicking the assign button you will be redirected to a
                                                page where you will confirm the payments transfer.</p>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        {{-- get the table --}}
                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <input type="text" name="search" id="searchkey"
                                                    class="form-control rounded-lg p-1" placeholder="Search here ..">
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
                                                <li class="page-item" id="tofirstNav">
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
                                                <li class="page-item"><button disabled class="page-link"
                                                        id="pagenumNav">Page: 1</button></li>
                                                <li class="page-item">
                                                    <a class="page-link" href="#" aria-label="Next" id="tonextNav">
                                                        <span aria-hidden="true">&raquo;</span>
                                                        <span class="sr-only">Next</span>
                                                    </a>
                                                </li>
                                                <li class="page-item">
                                                    <a class="page-link" href="#" aria-label="Last Page"
                                                        id="tolastNav">
                                                        <span aria-hidden="true">&raquo;&raquo;</span>
                                                        <span class="sr-only">Next</span>
                                                    </a>
                                                </li>
                                            </ul>
                                            <p class="card-text text-xxs">Showing from <span class="text-primary"
                                                    id="startNo">1</span> to <span class="text-secondary"
                                                    id="finishNo">10</span> records of <span id="tot_records">56</span></p>
                                        </nav>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        </div>

        {{-- footer --}}
            <x-footer/>
        {{-- footers --}}
        <script src="/theme-assets/vendors/js/vendors.min.js" type="text/javascript"></script>

        {{-- transfer the php value to js --}}
        <script>
            var data = @json($organization_data ?? []);
            // console.log(data);
        </script>
        @if ($transaction_data[0]->transaction_status == 0)
            <script src="/theme-assets/js/core/clientsAssign.js" type="text/javascript"></script>
        @endif
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
