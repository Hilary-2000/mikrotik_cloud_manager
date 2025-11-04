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
    <title>Hypbits - My profile</title>
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
                    <h3 class="content-header-title">Account & Settings</h3>
                </div>
                <div class="content-header-right col-md-8 col-12">
                    <div class="breadcrumbs-top float-md-right">
                        <div class="breadcrumb-wrapper mr-1">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/Dashboard">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item inactive"><a href="#">Account & Settings</a>
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
                                    {{-- <a href="{{route("AddAdmin")}}" class="btn btn-primary "><i class="ft-plus"></i> Manage Admin</a> --}}
                                    @php
                                        $btnText = "<i class=\"ft-plus\"></i> Manage Admin";
                                        $otherClasses = "";
                                        $btnLink = route("AddAdmin");
                                        $otherAttributes = "";
                                        $readonly = "";
                                    @endphp
                                    <x-button-link :otherAttributes="$otherAttributes"  :btnText="$btnText" :btnLink="$btnLink" btnType="primary" btnSize="sm" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                    {{-- <a href="/SharedTables" class="btn btn-info "><i class="ft-wind"></i> Shared Tables</a> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Basic Tables end -->
            </div>
            <section class="section profile">
              <div class="row">
                <div class="col-xl-4">
        
                  <div class="card">
                    <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                      <img style="width: 200px;height: 200px;" src="{{session('dp_locale') ? session('dp_locale') :'/theme-assets/images/pngegg.png'}}" alt="Profile" class="rounded-circle">
                      <h4 class="my-1">{{ session('Usernames') }}</h4>
                      <div class="p-1 my-1">
                        @if (session('error'))
                            <p class="text-danger text-bolder">{{ session('error') }}</p>
                        @endif
                        @if (session('success'))
                            <p class="text-success text-bolder">{{ session('success') }}</p>
                        @endif
                      </div>
                    </div>
                  </div>
        
                </div>
        
                <div class="col-xl-8">
        
                  <div class="card">
                    <div class="card-body pt-3">
                      <!-- Bordered Tabs -->
                      <ul class="nav nav-tabs nav-tabs-bordered">
        
                        <li class="nav-item">
                          <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview">Overview</button>
                        </li>
                        <li class="nav-item">
                          <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit">Edit Profile</button>
                        </li>
        
                        <li class="nav-item">
                          <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password">Change Password</button>
                        </li>
        
                        <li class="nav-item">
                          <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-settings">General Settings</button>
                        </li>
        
                      </ul>
                      <div class="tab-content pt-2">
        
                        <div class="tab-pane fade show active profile-overview text-lg" id="profile-overview">
                          <h5 class="card-title">Profile Details</h5>
        
                          <div class="row my-2">
                            <div class="col-lg-3 col-md-4 label ">Full Name</div>
                            <div class="col-lg-9 col-md-8">{{$admin_data[0]->admin_fullname ?$admin_data[0]->admin_fullname:"Null"}}</div>
                          </div>
        
                          <div class="row my-2">
                            <div class="col-lg-3 col-md-4 label">Role</div>
                            <div class="col-lg-9 col-md-8">Administrator</div>
                          </div>
        
                          <div class="row my-2">
                            <div class="col-lg-3 col-md-4 label">Last time Login</div>
                            <div class="col-lg-9 col-md-8">{{$date_time ?$date_time:"Null"}}</div>
                          </div>
        
                          <div class="row my-2">
                            <div class="col-lg-3 col-md-4 label">Phone</div>
                            <div class="col-lg-9 col-md-8">{{$admin_data[0]->contacts ?$admin_data[0]->contacts:"Null"}}</div>
                          </div>
        
                          <div class="row my-2">
                            <div class="col-lg-3 col-md-4 label">Email</div>
                            <div class="col-lg-9 col-md-8">{{$admin_data[0]->email ?$admin_data[0]->email:"Null"}}</div>
                          </div>
        
                        </div>
        
                        <div class="tab-pane fade profile-edit pt-3" id="profile-edit">
        
                          <div class="container">
                            <div class="row mb-3">
                              <label for="profileImage" class="col-md-4 col-lg-3 col-form-label">Profile Image</label>
                              <div class="col-md-8 col-lg-9">
                                <img style="width: 150px" src="{{session('dp_locale') ? session('dp_locale') :'/theme-assets/images/pngegg.png'}}" alt="Profile">
                                <div class="pt-2">
                                  @php
                                      $btnText = "<i class=\"ft-upload\"></i>";
                                      $otherClasses = "";
                                      $btn_id = "update_dp_btn";
                                      $btnSize="sm";
                                      $type = "button";
                                      $readonly = "";
                                      $otherAttributes = "";
                                  @endphp
                                  <x-button toolTip="" btnType="primary" :otherAttributes="$otherAttributes" :btnText="$btnText" :type="$type" :btnSize="$btnSize" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                  {{-- <button type="button" id="update_dp_btn" {{$readonly}} class="btn btn-primary btn-sm" title="Upload new profile image"><i class="ft-upload"></i></button> --}}
                                  @php
                                      $btnText = "<i class=\"ft-trash\"></i>";
                                      $otherClasses = "";
                                      $btnLink = route("delete_pp", [$admin_data[0]->admin_id]);
                                      $otherAttributes = "";
                                      $readonly = "";
                                  @endphp
                                  <x-button-link :otherAttributes="$otherAttributes"  :btnText="$btnText" :btnLink="$btnLink" btnType="danger" btnSize="sm" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                  {{-- <a href="{{route("delete_pp", [$admin_data[0]->admin_id])}}" class="btn btn-danger btn-sm {{$readonly}}" title="Remove my profile image"><i class="ft-trash"></i></a> --}}
                                </div>
                              </div>
                            </div>

                            <div class="container d-none" id="change_dp_window">
                                <form action="{{route("update_dp")}}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <hr>
                                    <h5 class="my-2">Change Profile Picture</h5>
                                    <input type="hidden" name="client_id" value="{{$admin_data[0]->admin_id}}">
                                    <p id="mine_dp_errors"></p>
                                    <label for="mine_dp" class="form-control-label">Select an Image</label>
                                    <input type="file" name="mine_dp" id="mine_dp" class="form-control" required>
                                    <div class="row">
                                        <div class="col-md-6">
                                          @php
                                              $btnText = "<i class=\"ft-upload\"></i> Save Image";
                                              $otherClasses = "my-1";
                                              $btn_id = "upload_image";
                                              $btnSize="sm";
                                              $type = "submit";
                                              $readonly = "";
                                              $otherAttributes = "";
                                          @endphp
                                          <x-button toolTip="" btnType="primary" :otherAttributes="$otherAttributes" :btnText="$btnText" :type="$type" :btnSize="$btnSize" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                          {{-- <button type="submit" id="upload_image" {{$readonly}} class="btn btn-primary my-1 text-lg">Save Image</button> --}}
                                        </div>
                                        <div class="col-md-6">
                                          @php
                                              $btnText = "<i class=\"ft-x\"></i> Cancel";
                                              $otherClasses = "my-1";
                                              $btn_id = "close_window_btn";
                                              $btnSize="sm";
                                              $type = "submit";
                                              $readonly = "";
                                              $otherAttributes = "";
                                          @endphp
                                          <x-button toolTip="" btnType="secondary" :otherAttributes="$otherAttributes" :btnText="$btnText" :type="$type" :btnSize="$btnSize" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                            {{-- <button type="button" id="close_window_btn" class="btn btn-secondary my-1 text-lg">Cancel</button> --}}
                                        </div>
                                    </div>
                                    <hr>
                                </form>
                            </div>
                          </div>
                          <!-- Profile Edit Form -->
                          <form action="{{route("update_admin")}}"  enctype="multipart/form-data" method="POST">
                            @csrf
                            <input type="hidden" name="client_id" value="{{$admin_data[0]->admin_id}}">
                            <div class="row mb-3">
                              <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Full Name</label>
                              <div class="col-md-8 col-lg-9">
                                <input name="fullName" type="text" class="form-control" id="fullName" value="{{$admin_data[0]->admin_fullname ?$admin_data[0]->admin_fullname:""}}" placeholder="Fullname" required>
                              </div>
                            </div>
                            <div class="row mb-3">
                              <label for="Phone" class="col-md-4 col-lg-3 col-form-label">Phone</label>
                              <div class="col-md-8 col-lg-9">
                                <input name="phone" type="text" class="form-control" id="Phone" value="{{$admin_data[0]->contacts ?$admin_data[0]->contacts:""}}" placeholder="Phone Number" required>
                              </div>
                            </div>
        
                            <div class="row mb-3">
                              <label for="Email" class="col-md-4 col-lg-3 col-form-label">Email</label>
                              <div class="col-md-8 col-lg-9">
                                <input name="email" type="email" class="form-control" id="Email" value="{{$admin_data[0]->email ?$admin_data[0]->email:""}}" placeholder="Email" >
                              </div>
                            </div>
        
                            <div class="text-center">
                              @php
                                  $btnText = "<i class=\"ft-save\"></i> Save Changes";
                                  $otherClasses = "";
                                  $btn_id = "";
                                  $btnSize="sm";
                                  $type = "submit";
                                  $readonly = "";
                                  $otherAttributes = "";
                              @endphp
                              <x-button toolTip="" btnType="primary" :otherAttributes="$otherAttributes" :btnText="$btnText" :type="$type" :btnSize="$btnSize" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                              {{-- <button type="submit" {{$readonly}} class="btn btn-primary">Save Changes</button> --}}
                            </div>
                          </form><!-- End Profile Edit Form -->
        
                        </div>
                        <div class="tab-pane fade profile-edit pt-3" id="company-profile-edit">
        
                          <div class="container">
                            <div class="row mb-3">
                              <label for="profileImage" class="col-md-4 col-lg-3 col-form-label">Company Profile Image</label>
                              <div class="col-md-8 col-lg-9">
                                <img class="border border-success rounded" style="width: 150px" src="{{'/theme-assets/images/logo-placeholder-image.png'}}" alt="Profile">
                                <div class="pt-2">
                                  @php
                                      $btnText = "<i class=\"ft-upload\"></i>";
                                      $otherClasses = "";
                                      $btn_id = "update_company_profile_btn";
                                      $btnSize="sm";
                                      $type = "submit";
                                      $readonly = "";
                                      $otherAttributes = "";
                                  @endphp
                                  <x-button toolTip="" btnType="primary" :otherAttributes="$otherAttributes" :btnText="$btnText" :type="$type" :btnSize="$btnSize" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                  {{-- <button type="button" id="update_company_profile_btn" {{$readonly}} class="btn btn-primary btn-sm" title="Upload new profile image"><i class="ft-upload"></i></button> --}}
                                  @php
                                      $btnText = "<i class=\"ft-trash\"></i>";
                                      $otherClasses = "";
                                      $btnLink = "/delete_pp_organization";
                                      $otherAttributes = "";
                                      $readonly = "";
                                  @endphp
                                  <x-button-link :otherAttributes="$otherAttributes"  :btnText="$btnText" :btnLink="$btnLink" btnType="danger" btnSize="sm" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                  {{-- <a href="/delete_pp_organization" class="btn btn-danger btn-sm {{$readonly}}" title="Remove my profile image"><i class="ft-trash"></i></a> --}}
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
        
                        <div class="tab-pane fade" id="profile-settings">
                          <!-- Settings Form -->
                          <form method="POST" action="{{route("update_delete_option")}}">
                            @csrf
                            <p class="text-danger">Note: <br> <small>Changes done here will affect the whole system</small></p>
                            <input type="hidden" name="client_id" value="{{$admin_data[0]->admin_id}}">
                            <h5>Select period of deleting your data</h5>
                            <div class="row mb-3 pt-2">
                              <div class="col-md-6">
                                <label for="delete_message_records">Frequency of deleting SMS records</label>
                              </div>
                              <div class="col-md-6">
                                <select class="form-control" name="delete_message_records" id="delete_message_records">
                                  <option value="" hidden>Select an option</option>
                                  <option {{$delete_sms == "daily" ? "selected":""}} value="daily">Daily</option>
                                  <option {{$delete_sms == "weekly" ? "selected":""}} value="weekly">Weekly</option>
                                  <option {{$delete_sms == "monthly" ? "selected":""}} value="monthly">Monthly</option>
                                  <option {{$delete_sms == "yearly" ? "selected":""}} value="yearly">Yearly</option>
                                  <option {{$delete_sms == "2years" ? "selected":""}} value="2years">2 Years</option>
                                  <option {{$delete_sms == "5years" ? "selected":""}} value="5years">5 Years</option>
                                  <option {{$delete_sms == "never" ? "selected":""}} value="never">Never</option>
                                </select>
                              </div>
                            </div>
                            <div class="row mb-3">
                              <div class="col-md-6">
                                <label for="delete_transactions">Frequency of deleting Transaction records</label>
                              </div>
                              <div class="col-md-6">
                                <select class="form-control" name="delete_transactions" id="delete_transactions">
                                  <option value="" hidden>Select an option</option>
                                  <option {{$delete_trans == "daily" ? "selected":""}} value="daily">Daily</option>
                                  <option {{$delete_trans == "weekly" ? "selected":""}} value="weekly">Weekly</option>
                                  <option {{$delete_trans == "monthly" ? "selected":""}} value="monthly">Monthly</option>
                                  <option {{$delete_trans == "yearly" ? "selected":""}} value="yearly">Yearly</option>
                                  <option {{$delete_trans == "2years" ? "selected":""}} value="2years">2 Years</option>
                                  <option {{$delete_trans == "5years" ? "selected":""}} value="5years">5 Years</option>
                                  <option {{$delete_trans == "never" ? "selected":""}} value="never">Never</option>
                                </select>
                              </div>
                            </div>
                            <div class="text-center">
                              @php
                                  $btnText = "<i class=\"ft-save\"></i> Save Changes";
                                  $otherClasses = "";
                                  $btn_id = "";
                                  $btnSize="sm";
                                  $type = "submit";
                                  $readonly = "";
                                  $otherAttributes = "";
                              @endphp
                              <x-button toolTip="" btnType="primary" :otherAttributes="$otherAttributes" :btnText="$btnText" :type="$type" :btnSize="$btnSize" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                              {{-- <button type="submit" {{$readonly}} class="btn btn-primary">Save Changes</button> --}}
                            </div>
                          </form><!-- End settings Form -->
                          <div class="container d-none">
                            <p><strong>Export user data</strong></p>
                            {{-- <a href="/Clients/Export" class="btn btn-secondary text-bolder {{$readonly}} disabled"><i class="ft-command"> </i>Export</a> --}}
                            @php
                                $btnText = "<i class=\"ft-command\"></i> Export";
                                $otherClasses = "";
                                $btnLink = "/Clients/Export";
                                $otherAttributes = "";
                                $readonly = "";
                            @endphp
                            <x-button-link :otherAttributes="$otherAttributes"  :btnText="$btnText" :btnLink="$btnLink" btnType="secondary" btnSize="sm" :otherClasses="$otherClasses" :readOnly="$readonly" />
                            <hr>
                            <p><strong>Manage Billing SMSes</strong></p>
                            {{-- <a href="/BillingSms/Manage" class="btn btn-secondary text-bolder {{$readonly}} disabled"><i class="ft-settings"> </i>Manage</a> --}}
                            @php
                                $btnText = "<i class=\"ft-command\"></i> Manage";
                                $otherClasses = "";
                                $btnLink = "/BillingSms/Manage";
                                $otherAttributes = "";
                                $readonly = "";
                            @endphp
                            <x-button-link :otherAttributes="$otherAttributes"  :btnText="$btnText" :btnLink="$btnLink" btnType="secondary" btnSize="sm" :otherClasses="$otherClasses" :readOnly="$readonly" />
                          </div>
                        </div>
        
                        <div class="tab-pane fade pt-3" id="profile-change-password">
                          <!-- Change Password Form -->
                            <form action="{{route("updatePassword")}}" method="post">
                                <h6><strong>Edit Password</strong></h6>
                                <div class="row">
                                    @csrf
                                    <div class="col-md-6">
                                        <label for="username" class="form-control-label">Username</label>
                                        <input type="text" class="form-control" name="username"
                                            placeholder="Username" required>
                                        <input type="hidden" class="form-control" name="admin_id"
                                            value="{{ $admin_data[0]->admin_id }}" placeholder="Username">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="old_password" class="form-control-label">Old
                                            Password</label>
                                        <input type="password" class="form-control" name="old_password"
                                            placeholder="Old password" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="password" class="form-control-label">New Password</label>
                                        <input type="password" class="form-control" name="password"
                                            placeholder="New password" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="confirm_password" class="form-control-label">Confirm
                                            Password</label>
                                        <input type="password" class="form-control" name="confirm_password"
                                            placeholder="Confirm password" required>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                      @php
                                          $btnText = "<i class=\"ft-save\"></i> Save Changes";
                                          $otherClasses = "";
                                          $btn_id = "";
                                          $btnSize="sm";
                                          $type = "submit";
                                          $readonly = "";
                                          $otherAttributes = "";
                                      @endphp
                                      <x-button toolTip="" btnType="primary" :otherAttributes="$otherAttributes" :btnText="$btnText" :type="$type" :btnSize="$btnSize" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                        {{-- <button class="btn btn-primary" {{$readonly}} type="submit">Save Changes</button> --}}
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div><!-- End Bordered Tabs -->
                </div>
            </div>
        </div>
    </div>
</section>
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
    <script src="/theme-assets/js/core/bootstrap.bundle.min.js"></script>
    <script>
      document.getElementById('mine_dp').onchange = function(){
          var filesize = document.getElementById('mine_dp').files[0].size;
          console.log(filesize);
          var size_in_mb = filesize/1000000;
          size_in_mb = size_in_mb.toFixed();
          if (size_in_mb > 7) {
            document.getElementById("mine_dp_errors").innerHTML = "<p class='text-danger'>Your image size should not be greater than 7MBS</p>";
            document.getElementById("mine_dp").classList.add("border");
            document.getElementById("mine_dp").classList.add("border-danger");
            document.getElementById("upload_image").disabled = true;
          }else{
            document.getElementById("mine_dp_errors").innerHTML = "";
            document.getElementById("mine_dp").classList.remove("border");
            document.getElementById("mine_dp").classList.remove("border-danger");
            document.getElementById("upload_image").disabled = false;
          }
      }

      document.getElementById("update_dp_btn").onclick = function () {
        document.getElementById("change_dp_window").classList.remove("d-none");
      }
      document.getElementById("close_window_btn").onclick = function () {
        document.getElementById("change_dp_window").classList.add("d-none");
      }
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
</body>

</html>
