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
    <title>Hypbits - View Admin Details</title>
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

<body class="vertical-layout vertical-menu 2-columns  menu-expanded fixed-navbar" data-open="click"
    data-menu="vertical-menu" data-color="bg-chartbg" data-col="2-columns">

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
                    <h3 class="content-header-title">Add Administrator</h3>
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
                                <h4 class="card-title">Account and Profile</h4>
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
                                <div class="card-body">
                                    @if ($errors->any())
                                        <h6 style="color: orangered">Errors</h6>
                                        <ul class="text-danger" style="color: orangered">
                                            @foreach ($errors->all() as $item)
                                                <li>{{ $item }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    @if (session('network_presence'))
                                        <p class="text-danger">{{ session('network_presence') }}</p>
                                    @endif
                                    @if (session('success'))
                                        <p class="text-success">{{ session('success') }}</p>
                                    @endif
                                    <div class="row">
                                        <div class="col-md-6">
                                            <a href="{{route("AddAdmin")}}" class="btn btn-infor"><i class="ft-arrow-left"></i>
                                                Back to list</a>
                                        </div>
                                        <div class="col-md-6">
                                            @php
                                                $btnText = "<i class=\"ft-trash-2\"></i> Delete";
                                                $otherClasses = "float-right";
                                                $btn_id = "delete_user";
                                                $btnSize="sm";
                                                $type = "submit";
                                                $readonly = "";
                                                $otherAttributes = "";
                                            @endphp
                                            <x-button toolTip="" btnType="danger" :otherAttributes="$otherAttributes" :btnText="$btnText" :type="$type" :btnSize="$btnSize" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                            {{-- <button id="delete_user" class="btn btn-danger text-lg float-right"><i class="ft-trash-2"> Delete</i></button> --}}
                                            <div class="container d-none mt-4" id="prompt_del_window">
                                                <p class="text-secondary"><strong>Are you sure you want to permanently delete this user?</strong></p>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        @php
                                                            $btnText = "<i class=\"fas fa-trash\"></i> Delete";
                                                            $otherClasses = "my-1 ";
                                                            $btnLink = route("deleteAdmin",[$admin_data[0]->admin_id]);
                                                            $otherAttributes = '';
                                                            $readonly = "";
                                                        @endphp
                                                        <x-button-link :otherAttributes="$otherAttributes"  :btnText="$btnText" :btnLink="$btnLink" btnType="danger" btnSize="sm" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                                        {{-- <a href="{{route("deleteAdmin",[$admin_data[0]->admin_id])}}" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</a> --}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <h6><strong>Update Administrator</strong></h6>
                                    <p class="card-text">Fill all fields to add the Administrator.</p>
                                    <form action="{{route("updateAdmin")}}" method="post">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label for="admin_name" class="form-control-label">Fullname</label>
                                                <input type="text" name="admin_name" id="admin_name"
                                                    class="form-control rounded-lg p-1"
                                                    value="{{ $admin_data[0]->admin_fullname }}"
                                                    placeholder="Admin Fullname .." required>
                                                <input type="hidden" name="admin_id"
                                                    value="{{ $admin_data[0]->admin_id }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="client_address" class="form-control-label">Contacts</label>
                                                <input type="text" name="client_address" id="client_address"
                                                    value="{{ $admin_data[0]->contacts }}"
                                                    class="form-control rounded-lg p-1"
                                                    placeholder="Administrator contacts" required>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 form-group">
                                                <label for="admin_username" class="form-control-label">Username <span
                                                        class="text-danger" id="error_acc_no"></span></label>
                                                <input type="text" name="admin_username" id="admin_username"
                                                    value="{{ $admin_data[0]->admin_username }}"
                                                    class="form-control rounded-lg p-1"
                                                    placeholder="Administrator Username" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="admin_password" class="form-control-label">Password</label>
                                                <input type="password" name="admin_password" id="admin_password"
                                                    value="{{ $admin_data[0]->admin_password }}"
                                                    class="form-control rounded-lg p-1"
                                                    placeholder="Administrator password" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="admin_password" class="form-control-label">Status</label>
                                                <select class="form-control" name="status" id="status" required>
                                                    <option value="" hidden>Select an option</option>
                                                    <option {{ $admin_data[0]->activated == "0" ? "selected" : ""}} value="0">In-Active</option>
                                                    <option {{$admin_data[0]->activated == "1" ? "selected" : ""}} value="1">Active</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                @php
                                                    $btnText = "<i class=\"ft-upload\"> </i> Update Administrator";
                                                    $otherClasses = "";
                                                    $btn_id = "";
                                                    $btnSize="sm";
                                                    $type = "submit";
                                                    $readonly = "";
                                                    $otherAttributes = "";
                                                @endphp
                                                <x-button toolTip="" btnType="primary" :otherAttributes="$otherAttributes" :btnText="$btnText" :type="$type" :btnSize="$btnSize" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                {{-- <button class="btn btn-primary" {{$readonly}} type="submit"><i
                                                        class="ft-upload"></i> Update Administrator</button> --}}
                                            </div>
                                        </div>
                                        <hr>
                                    </form>
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

    <script src="/theme-assets/js/core/app-menu-lite.js" type="text/javascript"></script>
    <script src="/theme-assets/js/core/app-lite.js" type="text/javascript"></script>
    <script src="/theme-assets/js/core/Accounts/view_admin.js"></script>
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
