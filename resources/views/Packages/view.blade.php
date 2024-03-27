<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="My ISP is the number one kenyan webserver software that helps you manage and monitor your webserver.">
    <meta name="keywords" content="admin template, Client template, dashboard template, gradient admin template, responsive client template, webapp, eCommerce dashboard, analytic dashboard">
    <meta name="author" content="ThemeSelect">
    <title>Hypbits - View Package</title>
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
                    <h3 class="content-header-title">View Packages</h3>
                </div>
                <div class="content-header-right col-md-8 col-12">
                    <div class="breadcrumbs-top float-md-right">
                        <div class="breadcrumb-wrapper mr-1">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/Dashboard">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active">View Packages
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
                                <h4 class="card-title">New Package</h4>
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
                                <a href="{{route("Packages")}}" class="btn btn-infor"><i class="fas fa-arrow-left"></i> Back
                                    to Package List</a>
                                <div class="card-body">
                                    {{-- <p>{{($client_data)}}</p> --}}
                                    <p><b>Note:</b></p>
                                    <ul>
                                        <li class="card-text" >Fill all the fields to add a new package!</li>
                                        <li class="card-text">Package period is the period the package takes before it expires.</li>
                                        <li class="card-text">Free trial period is the period a new user use their account for a certain period without payment</li>
                                        <li class="card-text">When a package is in-active it will not appear in the dropdown when registering and updating clients.</li>
                                    </ul>
                                    <hr>
                                    @if (session('success'))
                                        <p class='text-success'>{{ session('success') }}</p>
                                    @endif
                                    @if (session('error'))
                                        <p class='text-danger'>{{ session('error') }}</p>
                                    @endif
                                    <form action="{{url()->route("UpdatePackage",$package_details->package_id)}}" method="post">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-12 my-2">
                                                <label for="" class="form-control-label"><b>Status</b></label> <br>
                                                @if ($package_details->status == 0)
                                                    <a href="{{route("ActivatePackage",$package_details->package_id)}}" class="btn btn-sm btn-success">Activate</a>
                                                @else
                                                    <a href="{{route("DeactivatePackage",$package_details->package_id)}}" class="btn btn-sm btn-danger">De-Activate</a>
                                                @endif
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label for="package_name" class="form-control-label"><b>Package Name</b></label>
                                                <input required type="text" name="package_name" id="package_name"
                                                    class="form-control rounded-lg p-1" placeholder="Router name"
                                                    required
                                                    @if (($package_details->package_name))
                                                        value="{{($package_details->package_name)}}"
                                                    @endif
                                                    >
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label for="package_amount" class="form-control-label"><b>Package Amount </b></label>
                                                <input required type="number" min="0" name="package_amount" id="package_amount"
                                                    class="form-control rounded-lg p-1" placeholder="Package Amount. e.x : Kes 1000"
                                                    @if ($package_details->amount_paid)
                                                        value="{{$package_details->amount_paid}}"
                                                    @endif>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <div class="col-md-12 p-0 my-0">
                                                    <h6 class="text-center"><u>Package Period</u></h6>
                                                </div>
                                                <div class="w-100 row">
                                                    <div class="col-md-6">
                                                        <label for="period_metric" class="form-control-label"><b>Period Metric</b></label>
                                                        <select required name="period_metric" id="period_metric" class="form-control">
                                                            <option value="" hidden>Select an Option</option>
                                                            <option {{$package_details->package_period_metric == "days" ? "selected" : ""}} value="days">Days</option>
                                                            <option {{$package_details->package_period_metric == "months" ? "selected" : ""}} value="months">Months</option>
                                                            <option {{$package_details->package_period_metric == "years" ? "selected" : ""}} value="years">Years</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="period_number" class="form-control-label"><b>Period</b></label>
                                                        <input required type="number" min="0" name="period_number" id="period_number"
                                                            class="form-control rounded-lg p-1" placeholder="ex 10"
                                                            required
                                                            value="{{$package_details->package_period_number}}"
                                                            >
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 form-group row">
                                                <div class="col-md-12 p-0 my-0">
                                                    <h6 class="text-center"><u>Free Trial Period</u></h6>
                                                </div>
                                                <div class="w-100 row">
                                                    <div class="col-md-6">
                                                        <label for="free_trial_period_metric" class="form-control-label"><b>Period Metric</b></label>
                                                        <select required name="free_trial_period_metric" id="free_trial_period_metric" class="form-control">
                                                            <option value="" hidden>Select an Option</option>
                                                            <option {{$package_details->free_trial_metric == "days" ? "selected" : ""}} value="days">Days</option>
                                                            <option {{$package_details->free_trial_metric == "months" ? "selected" : ""}} value="months">Months</option>
                                                            <option {{$package_details->free_trial_metric == "years" ? "selected" : ""}} value="years">Years</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="free_trial_period_number" class="form-control-label"><b>Period</b></label>
                                                        <input required type="number" name="free_trial_period_number" id="free_trial_period_number"
                                                            class="form-control rounded-lg p-1" placeholder="ex 10"
                                                            required
                                                            value="{{$package_details->free_trial_number}}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <button class="btn btn-success" type="submit"><i
                                                        class="ft-save"></i> Update Package</button>
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
    <script src="theme-assets/vendors/js/vendors.min.js" type="text/javascript"></script>
    <script src="//cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script>
        let table = new DataTable('#myTable');
    </script>
    <!-- BEGIN CHAMELEON  JS-->
    <script src="theme-assets/js/core/app-menu-lite.js" type="text/javascript"></script>
    <script src="theme-assets/js/core/app-lite.js" type="text/javascript"></script>
    <!-- END CHAMELEON  JS-->

    <!-- BEGIN CLIENT JS-->
    {{-- <script src="theme-assets/js/core/my_organization.js"></script> --}}
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