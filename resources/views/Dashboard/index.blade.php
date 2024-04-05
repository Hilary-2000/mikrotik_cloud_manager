<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="My ISP is the number one kenyan webserver software that helps you manage and monitor your webserver.">
    <meta name="keywords" content="admin template, Client template, dashboard template, gradient admin template, responsive client template, webapp, eCommerce dashboard, analytic dashboard">
    <meta name="author" content="ThemeSelect">
    <title>Hypbits Cloud Manager - Dashboard</title>
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

<body class="vertical-layout vertical-menu 2-columns  menu-expanded fixed-navbar" data-open="click" data-menu="vertical-menu" data-color="bg-chartbg" data-col="2-columns">

    <!-- fixed-top-->
        <x-navbar/>
    <!-- ////////////////////////////////////////////////////////////////////////////-->

    @php
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
        $readonly_1 = readOnly($priviledges,"Transactions");
        $readonly_2 = readOnly($priviledges,"SMS");
        $readonly_3 = readOnly($priviledges,"My Clients");

        function isJson($string) {
            return ((is_string($string) &&
                    (is_object(json_decode($string)) ||
                    is_array(json_decode($string))))) ? true : false;
        }
    @endphp

    <div class="main-menu menu-fixed menu-light menu-accordion menu-shadow " data-scroll-to-active="true" data-img="/theme-assets/images/backgrounds/02.jpg">
        {{-- MENU COMPONENT --}}
        <x-menu/>
        <!-- <a class="btn btn-danger btn-block btn-glow btn-upgrade-pro mx-1" href="https://themeselection.com/products/chameleon-admin-modern-bootstrap-webapp-dashboard-html-template-ui-kit/" target="_blank">Download PRO!</a> -->
        <div class="navigation-background">
        </div>
    </div>
    <div class="app-content content pt-2">
        <div class="content-wrapper">
            <div class="content-wrapper-before"></div>
            <div class="content-header row">
            </div>
            <div class="content-body">
                <!-- Chart -->
                <div class="d-none row match-height">
                    <div class="col-12">
                        <div class="">
                            <div id="gradient-line-chart1" class="height-250 GradientlineShadow1"></div>
                        </div>
                    </div>
                </div>
                <!-- Chart -->
                <!-- eCommerce statistic -->
                <div class="card text-center p-1">
                    <h4 class="text-dark">Dashboard</h4>
                    <p>@if (session('danger'))
                        <p class="text-danger">{{ session('danger') }}</p>
                    @endif</p>
                </div>
                {{-- <div class="row">
                    <div class="col-xl-4 col-lg-6 col-md-12">
                        <div class="card pull-up ecom-card-1 bg-white">
                            <div class="card-content ecom-card2 height-180">
                                <h5 class="text-muted danger position-absolute p-1">SMS Usage</h5>
                                <div>
                                    <i class="ft-cloud danger font-large-1 float-right p-1"></i>
                                </div>
                                <div class="progress-stats-container ct-golden-section height-75 position-relative pt-3  ">
                                    <div id="progress-stats-bar-chart"></div>
                                    <div id="progress-stats-line-chart" class="progress-stats-shadow"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-6 col-md-12">
                        <div class="card pull-up ecom-card-1 bg-white">
                            <div class="card-content ecom-card2 height-180">
                                <h5 class="text-muted info position-absolute p-1">Active Clients</h5>
                                <div>
                                    <i class="ft-user-check info font-large-1 float-right p-1"></i>
                                </div>
                                <div class="progress-stats-container ct-golden-section height-75 position-relative pt-3">
                                    <div id="progress-stats-bar-chart1"></div>
                                    <div id="progress-stats-line-chart1" class="progress-stats-shadow"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-12">
                        <div class="card pull-up ecom-card-1 bg-white">
                            <div class="card-content ecom-card2 height-180">
                                <h5 class="text-muted warning position-absolute p-1">Income Stats</h5>
                                <div>
                                    <i class="ft-refresh-ccw warning font-large-1 float-right p-1"></i>
                                </div>
                                <div class="progress-stats-container ct-golden-section height-75 position-relative pt-3">
                                    <div id="progress-stats-bar-chart2"></div>
                                    <div id="progress-stats-line-chart2" class="progress-stats-shadow"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}
                <!--/ eCommerce statistic -->

                <!-- Statistics -->
                <!-- Statistics -->
                <div class="row match-height">
                <!-- SMS Statistics -->
                    <div class="col-xl-4 col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Recent SMS Sent</h4>
                                <a class="heading-elements-toggle">
                                    <i class="fa fa-ellipsis-v font-medium-3"></i>
                                </a>
                                <div class="heading-elements">
                                    <ul class="list-inline mb-0">
                                        <li>
                                            <a data-action="reload">
                                                <i class="ft-rotate-cw"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-content">
                                <div id="recent-buyers" class="media-list">
                                    @if (count($sms_sent) > 0)
                                        {{-- display the sms data --}}
                                        @for ($i = 0; $i < count($sms_sent); $i++)
                                                @if ($readonly_2 == "disabled")
                                                    <a href="/sms/View/{{$sms_sent[$i]->sms_id}}" class="media border-0">
                                                @else
                                                    <a href="#" class="media border-0">
                                                @endif
                                                <div class="media-left pr-1 text-center text-lg">
                                                    @if ($sms_sent[$i]->sms_status == 1)
                                                        <h3 class="text-success"><i class="ft-message-circle"></i></h3>
                                                    @else
                                                        <h3 class="text-danger"><i class="ft-message-circle"></i></h3>
                                                    @endif
                                                </div>
                                                <div class="media-body w-100">
                                                    <span class="list-group-item-heading">{{$sms_sent[$i]->sms_content}} |
                                                    </span>
                                                    <small class="text-primary">{{date("D dS M Y",strtotime($sms_sent[$i]->date_sent))}}</small>
                                                    <p class="list-group-item-text mb-0">
                                                        <span class="blue-grey lighten-2 font-small-3"> 
                                                            To:
                                                            <span class="text-primary">{{$sms_sent[$i]->recipient_phone}}</span> </span>
                                                    </p>
                                                </div>
                                            </a>
                                        @endfor
                                    @else
                                        <a href="#" class="media border-0">
                                            <div class="media-left pr-1 text-center text-lg">
                                                <h3 class="text-success"><i class="ft-message-circle"></i></h3>
                                            </div>
                                            <div class="media-body w-100">
                                                <span class="list-group-item-heading"><h6 class="text-danger">No messages sent</h6> |
                                                </span>
                                                <small class="text-primary">{{date("D dS M Y (H:i:s)")}}</small>
                                                <p class="list-group-item-text mb-0">
                                                    
                                                </p>
                                            </div>
                                        </a>
                                    @endif
                                    {{-- <a href="#" class="media border-0">
                                        <div class="media-left pr-1 text-center text-lg">
                                            <h3 class="text-success"><i class="ft-message-circle"></i></h3>
                                        </div>
                                        <div class="media-body w-100">
                                            <span class="list-group-item-heading">Confirmed you have recieved Ksh 2000 from Acc: 0743551250 your new wallet balance is 1000 |
                                            </span>
                                            <small class="text-primary">25th Aug 2021</small>
                                            <p class="list-group-item-text mb-0">
                                                <span class="blue-grey lighten-2 font-small-3"> To: <span class="text-primary">James Hector</span> </span>
                                            </p>
                                        </div>
                                    </a> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                <!-- end of sms statistics -->
                    <div class="col-xl-4 col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Recent Transaction Recieved</h4>
                                <a class="heading-elements-toggle">
                                    <i class="fa fa-ellipsis-v font-medium-3"></i>
                                </a>
                                <div class="heading-elements">
                                    <ul class="list-inline mb-0">
                                        <li>
                                            <a data-action="reload">
                                                <i class="ft-rotate-cw"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-content">
                                <div id="recent-buyers" class="media-list">
                                    @if (count($transaction_data) > 0)
                                        {{-- display the transaction messages --}}
                                        @for ($i = 0; $i < count($transaction_data); $i++)
                                            @if ($readonly_1 == "disabled")
                                                <a href="/Transactions/View/{{$transaction_data[$i]->transaction_id}}" class="media border-0">
                                            @else
                                                <a href="#" class="media border-0">
                                            @endif
                                                <div class="media-left pr-1">
                                                    <span class="avatar avatar-md avatar-online">
                                                        <img class="media-object rounded-circle" src="/theme-assets/logos/money-bag-512.jpg" alt="Generic placeholder image">
                                                        <i></i>
                                                    </span>
                                                </div>
                                                <div class="media-body w-100">
                                                    <span class="list-group-item-heading">{{$transaction_data[$i]->transaction_mpesa_id}} |Kes  {{$transaction_data[$i]->transacion_amount}} |
                                                    </span>
                                                    <small>{{$transaction_data[$i]->transaction_date}}</small>
                                                    <p class="list-group-item-text mb-0">
                                                        <span class="blue-grey lighten-2 font-small-3"> Account: <span class="text-primary">{{$transaction_data[$i]->organization_name}}</span> </span>
                                                    </p>
                                                </div>
                                            </a>
                                        @endfor
                                    @else
                                        <a href="#" class="media border-0">
                                            <div class="media-left pr-1">
                                                <span class="avatar avatar-md avatar-online">
                                                    <img class="media-object rounded-circle" src="/theme-assets/logos/money-bag-512.jpg" alt="Generic placeholder image">
                                                    <i></i>
                                                </span>
                                            </div>
                                            <div class="media-body w-100">
                                                <span class="list-group-item-heading text-danger">No transaction to display at the moment |
                                                </span>
                                                <small class="text-primary">{{date("D dS M Y (H:i:s)")}}</small>
                                                <p class="list-group-item-text mb-0">
                                                    <span class="blue-grey lighten-2 font-small-3"> Account: <span class="text-primary">NULL</span> </span>
                                                </p>
                                            </div>
                                        </a>
                                    @endif
                                    {{-- <a href="#" class="media border-0">
                                        <div class="media-left pr-1">
                                            <span class="avatar avatar-md avatar-online">
                                                <img class="media-object rounded-circle" src="/theme-assets/logos/money-bag-512.jpg" alt="Generic placeholder image">
                                                <i></i>
                                            </span>
                                        </div>
                                        <div class="media-body w-100">
                                            <span class="list-group-item-heading">PMKYGHBNJJ |
                                            </span>
                                            <small>25th Aug 2021</small>
                                            <p class="list-group-item-text mb-0">
                                                <span class="blue-grey lighten-2 font-small-3"> Account: <span class="text-primary">James Hector</span> </span>
                                            </p>
                                        </div>
                                    </a>--}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Recent Registered Clients</h4>
                                <a class="heading-elements-toggle">
                                    <i class="fa fa-ellipsis-v font-medium-3"></i>
                                </a>
                                <div class="heading-elements">
                                    <ul class="list-inline mb-0">
                                        <li>
                                            <a data-action="reload">
                                                <i class="ft-rotate-cw"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-content">
                                <div id="recent-buyers" class="media-list">
                                    @if (count($client_data) > 0)
                                        @for ($i = 0; $i < count($client_data); $i++)
                                            @if ($readonly_3 == "disabled")
                                                <a href="/Clients/View/{{$client_data[$i]->client_id}}" class="media border-0">
                                            @else
                                                <a href="#" class="media border-0">
                                            @endif
                                                <div class="media-left pr-1">
                                                    <span class="avatar avatar-md {{($client_data[$i]->organization_status == "1") ? 'avatar-online' : 'avatar-busy'}}">
                                                        <img class="media-object rounded-circle" src="/theme-assets/logos/young-user-icon.jpg" alt="Generic placeholder image">
                                                        <i></i>
                                                    </span>
                                                </div>
                                                <div class="media-body w-100">
                                                    <span class="list-group-item-heading">{{$client_data[$i]->organization_name}}
        
                                                    </span>
                                                    <ul class="list-unstyled users-list m-0 float-right">
                                                        <span class="text-xxs text-infor"><i class="ft-map-pin"></i> <small>{{$client_data[$i]->organization_address}}</small></span>
                                                    </ul>
                                                    <p class="list-group-item-text mb-0">
                                                        <span class="blue-grey lighten-2 font-small-3">{{$client_data[$i]->package_name}}</span>
                                                    </p>
                                                </div>
                                            </a>
                                        @endfor
                                    @else
                                        
                                    @endif
                                    {{-- <a href="#" class="media border-0">
                                        <div class="media-left pr-1">
                                            <span class="avatar avatar-md avatar-online,away,busy">
                                                <img class="media-object rounded-circle" src="/theme-assets/logos/young-user-icon.jpg" alt="Generic placeholder image">
                                                <i></i>
                                            </span>
                                        </div>
                                        <div class="media-body w-100">
                                            <span class="list-group-item-heading">Kristopher Candy

                                            </span>
                                            <ul class="list-unstyled users-list m-0 float-right">
                                                <span class="text-xxs text-infor"><i class="ft-map-pin"></i> <small>Juja Town</small></span>
                                            </ul>
                                            <p class="list-group-item-text mb-0">
                                                <span class="blue-grey lighten-2 font-small-3"> #3mbps / #3mbps</span>
                                            </p>
                                        </div>
                                    </a> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--/ Statistics -->
            </div>
        </div>
    </div>
    <!-- ////////////////////////////////////////////////////////////////////////////-->
    <!-- The footer -->
        <x-footer/>
    <!-- ////////////////////////// -->

    
    <!-- BEGIN VENDOR JS-->
    <script src="/theme-assets/vendors/js/vendors.min.js" type="text/javascript"></script>
    <!-- BEGIN VENDOR JS-->
    <!-- BEGIN PAGE VENDOR JS-->
    
    <!-- END PAGE VENDOR JS-->
    <!-- BEGIN CHAMELEON  JS-->
    <script src="/theme-assets/js/core/app-menu-lite.js" type="text/javascript"></script>
    <script src="/theme-assets/js/core/app-lite.js" type="text/javascript"></script>
    <!-- END CHAMELEON  JS-->
    <!-- BEGIN PAGE LEVEL JS-->
    <script>
        var milli_seconds = 1200;
        setInterval(() => {
            if (milli_seconds == 0) {
                window.location.href = "/";
            }
            milli_seconds--;
        }, 1000);
      </script>
  
    
    <!-- END PAGE LEVEL JS-->
</body>

</html>