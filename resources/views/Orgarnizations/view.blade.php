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
    <title>HBS - View Organization</title>
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
  .showBlock{
    display: block;
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
                    <h3 class="content-header-title">View Organization</h3>
                </div>
                <div class="content-header-right col-md-8 col-12">
                    <div class="breadcrumbs-top float-md-right">
                        <div class="breadcrumb-wrapper mr-1">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/Dashboard">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active">View Organization
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
                                <h4 class="card-title">View Organization - "{{ucwords(strtolower($organization_details->organization_name))}}"</h4>
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
                                <a href="{{route("Organizations")}}" class="btn btn-infor"><i class="fas fa-arrow-left"></i> Back to Organization List</a>
                                <div class="card-body">
                                    <h3 class="text-center"><b><u>View Organization "{{ucwords(strtolower($organization_details->organization_name))}}"</u></b></h3>
                                    <hr>
                                    @if (session('success'))
                                        <p class='text-success'>{{ session('success') }}</p>
                                    @endif
                                    @if (session('error'))
                                        <p class='text-danger'>{{ session('error') }}</p>
                                    @endif
                                    <div class="container">
                                      <div class="modal fade text-left hide" id="delete_column_details" tabindex="-1" role="dialog" aria-labelledby="myModalLabel11" style="padding-right: 17px;" aria-modal="true">
                                          <div class="modal-dialog modal-dialog-centered" role="document">
                                              <div class="modal-content">
                                                  <div class="modal-header bg-danger white">
                                                  <h4 class="modal-title white" id="myModalLabel11">Confirm Delete Of "{{ucwords(strtolower($organization_details->organization_name))}}".</h4>
                                                  <input type="hidden" id="delete_columns_ids">
                                                  <button id="hide_delete_column" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                      <span aria-hidden="true">×</span>
                                                  </button>
                                                  </div>
                                                  <div class="modal-body">
                                                      <div class="container">
                                                          <p class="text-dark"><b>Are you sure you want to delete "{{ucwords(strtolower($organization_details->organization_name))}}" ?</b></p>
                                                          <b>Note</b><br>
                                                          <p>- All "{{ucwords(strtolower($organization_details->organization_name))}}" data will be deleted.</p>
                                                          <p>- This action is irreversible.</p>
                                                      </div>
                                                  </div>
                                                  <div class="modal-footer">
                                                    @php
                                                        $btnText = "<i class=\"ft-x\"></i> Close";
                                                        $otherClasses = "";
                                                        $btn_id = "close_this_window_delete";
                                                        $btnSize="sm";
                                                        $type = "button";
                                                        $readonly = "";
                                                        $otherAttributes = "";
                                                    @endphp
                                                    <x-button toolTip="" btnType="secondary" :otherAttributes="$otherAttributes" :btnText="$btnText" :type="$type" :btnSize="$btnSize" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                      {{-- <button type="button" id="close_this_window_delete" class="btn grey btn-secondary" data-dismiss="modal">Close</button> --}}
                                                        @php
                                                            $readonly="";
                                                            $btnText = "<i class=\"ft-trash\"></i> Delete";
                                                            $otherClasses = "my-1 ".$readonly;
                                                            $btnLink = route("DeleteOrganization",[$organization_details->organization_id]);
                                                            $otherAttributes = "";
                                                        @endphp
                                                        <x-button-link :otherAttributes="$otherAttributes"  :btnText="$btnText" :btnLink="$btnLink" btnType="danger" btnSize="sm" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                                      {{-- <a href="{{route("DeleteOrganization",[$organization_details->organization_id])}}" class="btn btn-primary my-1 "><i class="ft-trash"></i> Delete</a> --}}
                                                  </div>
                                              </div>
                                          </div>
                                      </div>

                                      {{-- CHANGE DISCOUNT --}}
                                      <div class="modal fade text-left hide" id="change_discounts" tabindex="-1" role="dialog" aria-labelledby="myModalLabel11" style="padding-right: 17px;" aria-modal="true">
                                          <div class="modal-dialog modal-dialog-centered" role="document">
                                              <div class="modal-content">
                                                  <div class="modal-header bg-primary white">
                                                    <h4 class="text-white">Set Discount</h4>
                                                    <button id="hide_change_discounts" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">×</span>
                                                    </button>
                                                  </div>
                                                  <div class="modal-body">
                                                    <form action="{{route("UpdateDiscount",$organization_details->organization_id)}}" method="POST" class="form-group w-100" id="discount_window">
                                                        <hr>
                                                        <h6 class="text-center">Set Discount</h6>
                                                        @csrf
                                                        <label for="discount_type" class="form-control-label">Discount</label>
                                                        <select name="discount_type" id="discount_type" class="form-control">
                                                            <option {{$organization_details->discount_type == "number" ? "selected" : ""}} value="number">Discount Price</option>
                                                            <option {{$organization_details->discount_type == "percentage" ? "selected" : ""}} value="percentage">Discount Percentage</option>
                                                        </select>
                                                        <label for="discount_amount" class="form-control-label">Discount Amount</label>
                                                        <input type="number" name="discount_amount" id="discount_amount" placeholder="Discount Balance" class="form-control" value="{{$organization_details->discount_type != null ? ($organization_details->discount_type == "number" ? $organization_details->discount_amount : $organization_details->discount_amount) : "0"}}" >
                                                        <input type="submit" class="btn btn-success btn-sm my-2 d-none" value="Set" id="update_discount_window">
                                                    </form>
                                                  </div>
                                                  <div class="modal-footer">
                                                        @php
                                                            $btnText = "<i class=\"ft-x\"></i> Close";
                                                            $otherClasses = "";
                                                            $btn_id = "close_change_discounts";
                                                            $btnSize="sm";
                                                            $type = "button";
                                                            $readonly = "";
                                                            $otherAttributes = "";
                                                        @endphp
                                                        <x-button toolTip="" btnType="secondary" :otherAttributes="$otherAttributes" :btnText="$btnText" :type="$type" :btnSize="$btnSize" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                      {{-- <button type="button" id="close_change_discounts" class="btn grey btn-secondary" data-dismiss="modal">Close</button> --}}
                                                        @php
                                                            $btnText = "<i class=\"ft-save\"></i> Update";
                                                            $otherClasses = "";
                                                            $btn_id = "update_discounts";
                                                            $btnSize="sm";
                                                            $type = "button";
                                                            $readonly = "";
                                                            $otherAttributes = "";
                                                        @endphp
                                                        <x-button toolTip="" btnType="success" onclick="document.getElementById('update_discount_window').click();" :otherAttributes="$otherAttributes" :btnText="$btnText" :type="$type" :btnSize="$btnSize" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                      {{-- <button class="btn btn-success" id="update_discounts" onclick="document.getElementById('update_discount_window').click();"> Update</button> --}}
                                                  </div>
                                              </div>
                                          </div>
                                      </div>

                                      {{-- CHANGE WALLET --}}
                                      <div class="modal fade text-left hide" id="change_wallet_window" tabindex="-1" role="dialog" aria-labelledby="myModalLabel11" style="padding-right: 17px;" aria-modal="true">
                                          <div class="modal-dialog modal-dialog-centered" role="document">
                                              <div class="modal-content">
                                                  <div class="modal-header bg-primary white">
                                                    <h4 class="text-white">Set Wallet</h4>
                                                    <button id="hide_change_wallet" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">×</span>
                                                    </button>
                                                  </div>
                                                  <div class="modal-body">
                                                    <form action="{{route("UpdateWallet",$organization_details->organization_id)}}" method="POST" class="form-group w-100" id="wallet_balance_window">
                                                        <hr>
                                                        <h6 class="text-center">Set Wallet Balance</h6>
                                                        @csrf
                                                        <label for="wallet_balance" class="form-control-label">Wallet</label>
                                                        <input type="number" placeholder="Wallet Balance" class="form-control" value="{{$organization_details->wallet != null ? $organization_details->wallet : "0"}}" name="wallet_balance" id="wallet_balance">
                                                        <input type="submit" class="btn btn-success btn-sm my-2 d-none" id="update_Wallet_amount" value="Update">
                                                    </form>
                                                  </div>
                                                  <div class="modal-footer">
                                                    @php
                                                        $btnText = "<i class=\"ft-x\"></i> Close";
                                                        $otherClasses = "";
                                                        $btn_id = "close_change_wallet";
                                                        $btnSize="sm";
                                                        $type = "button";
                                                        $readonly = "";
                                                        $otherAttributes = "";
                                                    @endphp
                                                    <x-button toolTip="" btnType="secondary" :otherAttributes="$otherAttributes" :btnText="$btnText" :type="$type" :btnSize="$btnSize" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                    {{-- <button type="button" id="close_change_wallet" class="btn grey btn-secondary" data-dismiss="modal">Close</button> --}}
                                                    @php
                                                        $btnText = "<i class=\"ft-save\"></i> Update";
                                                        $otherClasses = "";
                                                        $btn_id = "close_change_wallet";
                                                        $btnSize="sm";
                                                        $type = "button";
                                                        $readonly = "";
                                                        $otherAttributes = "";
                                                    @endphp
                                                    <x-button toolTip="" btnType="success" onclick="document.getElementById('update_Wallet_amount').click();" :otherAttributes="$otherAttributes" :btnText="$btnText" :type="$type" :btnSize="$btnSize" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                    {{-- <button class="btn btn-success" onclick="document.getElementById('update_Wallet_amount').click();"> Update</button> --}}
                                                  </div>
                                              </div>
                                          </div>
                                      </div>

                                      {{-- CHANGE EXPIRATION DATE --}}
                                      <div class="modal fade text-left hide" id="change_expiration_date" tabindex="-1" role="dialog" aria-labelledby="myModalLabel11" style="padding-right: 17px;" aria-modal="true">
                                          <div class="modal-dialog modal-dialog-centered" role="document">
                                              <div class="modal-content">
                                                  <div class="modal-header bg-primary white">
                                                    <h4 class="text-white">Change Expiration Date</h4>
                                                    <button id="hide_change_expiration_date" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">×</span>
                                                    </button>
                                                  </div>
                                                  <div class="modal-body">
                                                    <form action="{{route("UpdateExpiration",$organization_details->organization_id)}}" method="POST" class="form-group w-100">
                                                        <hr>
                                                        <h6 class="text-center">Set Expiration Date</h6>
                                                        @csrf
                                                        <label for="wallet_balance" class="form-control-label">Expiration Date</label>
                                                        <input type="date" placeholder="Expiration Date" class="form-control" value="{{date("Y-m-d", strtotime($organization_details->expiry_date))}}" name="expiration_date">
                                                        <input type="submit" class="btn btn-success btn-sm my-2 d-none" id="update_expiration_date_btn" value="Update">
                                                    </form>
                                                  </div>
                                                  <div class="modal-footer">
                                                    @php
                                                        $btnText = "<i class=\"ft-x\"></i> Close";
                                                        $otherClasses = "";
                                                        $btn_id = "close_change_expiration_date";
                                                        $btnSize="sm";
                                                        $type = "button";
                                                        $readonly = "";
                                                        $otherAttributes = "";
                                                    @endphp
                                                    <x-button toolTip="" btnType="secondary" :otherAttributes="$otherAttributes" :btnText="$btnText" :type="$type" :btnSize="$btnSize" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                    {{-- <button type="button" id="close_change_expiration_date" class="btn grey btn-secondary" data-dismiss="modal">Close</button> --}}
                                                    @php
                                                        $btnText = "<i class=\"ft-save\"></i> Update";
                                                        $otherClasses = "";
                                                        $btn_id = "";
                                                        $btnSize="sm";
                                                        $type = "button";
                                                        $readonly = "";
                                                        $otherAttributes = "";
                                                    @endphp
                                                    <x-button toolTip="" btnType="success" onclick="document.getElementById('update_expiration_date_btn').click();" :otherAttributes="$otherAttributes" :btnText="$btnText" :type="$type" :btnSize="$btnSize" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                    {{-- <button class="btn btn-success" onclick="document.getElementById('update_expiration_date_btn').click();"> Update</button> --}}
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                    </div>
                                    <div class="container row ">
                                        <div class="col-md-12">
                                        {{-- <button type="button" class="btn btn-danger btn-sm my-1" id="DeleteTable"><i class="ft-trash"></i></button> --}}
                                        @php
                                            $btnText = "<i class=\"ft-trash\"></i> Delete Organization";
                                            $otherClasses = "mb-1";
                                            $btn_id = "DeleteTable";
                                            $btnSize="sm";
                                            $type = "button";
                                            $readonly = "";
                                            $otherAttributes = "";
                                        @endphp
                                        <x-button toolTip="" btnType="danger" :otherAttributes="$otherAttributes" :btnText="$btnText" :type="$type" :btnSize="$btnSize" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                        </div>
                                        <table class="table table-bordered mb-0">
                                            <tr>
                                                <td colspan="2">
                                                    <a href="{{route("getActiveClients", [$organization_details->organization_id])}}" target="_blank" style="width:fit-content;" class="badge bg-success text-dark" data-toggle="tooltip" data-html="true" title="{{$clients_monthly." client(s) active in the last 3 months (Click to display)"}}"><b>Estimated Earnings on "{{date("dS M Y", strtotime($organization_details->expiry_date))}}"</b> : Kes {{number_format($monthly_payment)}}</a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="col-md-6">
                                                        <p>Organization Status : 
                                                            @php
                                                                $readonly="";
                                                                $btnText = "De-Activate";
                                                                $otherClasses = "".$readonly." ".($organization_details->organization_status == "1" ? "" : "d-none");
                                                                $btnLink = route("DeactivateOrganization",$organization_details->organization_id);
                                                                $otherAttributes = "";
                                                            @endphp
                                                            <x-button-link :otherAttributes="$otherAttributes"  :btnText="$btnText" :btnLink="$btnLink" btnType="danger" btnSize="sm" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                                            {{-- <a href="{{route("DeactivateOrganization",$organization_details->organization_id)}}" class="btn btn-sm btn-danger {{$organization_details->organization_status == "1" ? "" : "d-none"}}">De-Activate</a> --}}
                                                            @php
                                                                $readonly="";
                                                                $btnText = "Activate";
                                                                $otherClasses = "".$readonly." ".($organization_details->organization_status == "1" ? "d-none" : "");
                                                                $btnLink = route("ActivateOrganization",$organization_details->organization_id);
                                                                $otherAttributes = "";
                                                            @endphp
                                                            <x-button-link :otherAttributes="$otherAttributes"  :btnText="$btnText" :btnLink="$btnLink" btnType="success" btnSize="sm" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                                            {{-- <a href="{{route("ActivateOrganization",$organization_details->organization_id)}}" class="btn btn-sm btn-success {{$organization_details->organization_status == "1" ? "d-none" : ""}}">Activate</a> --}}
                                                        </p>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="col-md-6">
                                                        <p>Payment Status : <br>
                                                            @php
                                                                $readonly="";
                                                                $btnText = "De-Activate";
                                                                $otherClasses = "".$readonly." ".($organization_details->payment_status == "1" ? "" : "d-none");
                                                                $btnLink = route("Deactivate_Payment_Status",$organization_details->organization_id);
                                                                $otherAttributes = "";
                                                            @endphp
                                                            <x-button-link :otherAttributes="$otherAttributes"  :btnText="$btnText" :btnLink="$btnLink" btnType="danger" btnSize="sm" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                                            {{-- <a href="{{route("Deactivate_Payment_Status",$organization_details->organization_id)}}" class="btn btn-sm btn-danger {{$organization_details->payment_status == "1" ? "" : "d-none"}}">De-Activate</a> --}}
                                                            @php
                                                                $readonly="";
                                                                $btnText = "Activate";
                                                                $otherClasses = "".$readonly." ".($organization_details->payment_status == "1" ? "d-none" : "");
                                                                $btnLink = route("Activate_Payment_Status",$organization_details->organization_id);
                                                                $otherAttributes = "";
                                                            @endphp
                                                            <x-button-link :otherAttributes="$otherAttributes"  :btnText="$btnText" :btnLink="$btnLink" btnType="success" btnSize="sm" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                                            {{-- <a href="{{route("Activate_Payment_Status",$organization_details->organization_id)}}" class="btn btn-sm btn-success {{$organization_details->payment_status == "1" ? "d-none" : ""}}">Activate</a> --}}
                                                        </p>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="col-md-8">
                                                        <p>Send SMS Status : <br>
                                                            @php
                                                                $readonly="";
                                                                $btnText = "De-Activate";
                                                                $otherClasses = "".$readonly." ".($organization_details->send_sms == "1" ? "" : "d-none");
                                                                $btnLink = route("DeactivateSMS",$organization_details->organization_id);
                                                                $otherAttributes = "";
                                                            @endphp
                                                            <x-button-link :otherAttributes="$otherAttributes"  :btnText="$btnText" :btnLink="$btnLink" btnType="danger" btnSize="sm" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                                            {{-- <a href="{{route("DeactivateSMS",$organization_details->organization_id)}}" class="btn btn-sm btn-danger {{$organization_details->send_sms == "1" ? "" : "d-none"}}">De-Activate</a> --}}
                                                            @php
                                                                $readonly="";
                                                                $btnText = "Activate";
                                                                $otherClasses = "".$readonly." ".($organization_details->send_sms == "1" ? "d-none" : "");
                                                                $btnLink = route("ActivateSMS",$organization_details->organization_id);
                                                                $otherAttributes = "";
                                                            @endphp
                                                            <x-button-link :otherAttributes="$otherAttributes"  :btnText="$btnText" :btnLink="$btnLink" btnType="success" btnSize="sm" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                                            {{-- <a href="{{route("ActivateSMS",$organization_details->organization_id)}}" class="btn btn-sm btn-success {{$organization_details->send_sms == "1" ? "d-none" : ""}}">Activate</a> --}}
                                                        </p>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group w-100" id="discount_viewer">
                                                        <p for="discount" class="px-1">Discount : {{$organization_details->discount_type != null ? ($organization_details->discount_type == "number" ? "Kes ".number_format($organization_details->discount_amount) : $organization_details->discount_amount."%") : "Not-Set"}} <br>
                                                            {{-- <button class="btn btn-sm btn-primary" id="discount_change_btn"><i class="ft-refresh-cw"></i> Change</button> --}}
                                                            @php
                                                                $btnText = "<i class=\"ft-refresh-cw\"></i> Change";
                                                                $otherClasses = "mb-1";
                                                                $btn_id = "discount_change_btn";
                                                                $btnSize="sm";
                                                                $type = "button";
                                                                $readonly = "";
                                                                $otherAttributes = "";
                                                            @endphp
                                                            <x-button toolTip="" btnType="primary" :otherAttributes="$otherAttributes" :btnText="$btnText" :type="$type" :btnSize="$btnSize" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                        </p>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="form-group w-100">
                                                        <p for="wallet_balance" class="px-1">Wallet : Kes {{$organization_details->wallet != null ? number_format($organization_details->wallet) : "0"}}<br> 
                                                            {{-- <button class="btn btn-sm btn-primary" id="wallet_change_btn"><i class="ft-refresh-cw"></i> Change</button> --}}
                                                            @php
                                                                $btnText = "<i class=\"ft-refresh-cw\"></i> Change";
                                                                $otherClasses = "mb-1";
                                                                $btn_id = "wallet_change_btn";
                                                                $btnSize="sm";
                                                                $type = "button";
                                                                $readonly = "";
                                                                $otherAttributes = "";
                                                            @endphp
                                                            <x-button toolTip="" btnType="primary" :otherAttributes="$otherAttributes" :btnText="$btnText" :type="$type" :btnSize="$btnSize" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                        </p>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group w-100">
                                                        <p for="expiration_date" class="px-1">Expiry Date : {{date("D dS M Y", strtotime($organization_details->expiry_date))}}<br> 
                                                            {{-- <button class="btn btn-sm btn-primary" id="expiration_date"><i class="ft-refresh-cw"></i> Change</button> --}}
                                                            @php
                                                                $btnText = "<i class=\"ft-refresh-cw\"></i> Change";
                                                                $otherClasses = "mb-1";
                                                                $btn_id = "expiration_date";
                                                                $btnSize="sm";
                                                                $type = "button";
                                                                $readonly = "";
                                                                $otherAttributes = "";
                                                            @endphp
                                                            <x-button toolTip="" btnType="primary" :otherAttributes="$otherAttributes" :btnText="$btnText" :type="$type" :btnSize="$btnSize" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                        </p>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <hr>
                                    <h3 class="text-primary text-center"><u>Organization Details</u></h3>
                                    <hr>
                                    @if (session('success'))
                                        <p class='text-success'>{{ session('success') }}</p>
                                    @endif
                                    @if (session('error'))
                                        <p class='text-danger'>{{ session('error') }}</p>
                                    @endif
                                    <form action="{{url()->route("UpdateOrganization",$organization_details->organization_id)}}" method="post">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-4 form-group">
                                                <label for="organization_name" class="form-control-label">Organization Name </label>
                                                <input required type="text" name="organization_name" id="organization_name"
                                                    class="form-control rounded-lg p-1" placeholder="Organization name"
                                                    required value="{{$organization_details->organization_name}}">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="organization_account" class="form-control-label">Organization Account</label>
                                                <input required type="text" readonly min="0" name="organization_account" id="organization_account"
                                                    class="form-control rounded-lg p-1" placeholder="HBS101" value="{{$organization_details->account_no}}">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="organization_location" class="form-control-label">Organization Location</label>
                                                <input required type="text" name="organization_location" id="organization_location"
                                                    class="form-control rounded-lg p-1" placeholder="Organization Location e.x Mombasa"
                                                    required value="{{$organization_details->organization_address}}"
                                                    >
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="organization_contacts" class="form-control-label">Organization Contacts </label>
                                                <input required type="text" min="0" name="organization_contacts" id="organization_contacts"
                                                    class="form-control rounded-lg p-1" placeholder="Organization Contacts. e.x : 0720000000" value="{{$organization_details->organization_main_contact}}">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="organization_email" class="form-control-label">Organization Email</label>
                                                <input required type="text" min="0" name="organization_email" id="organization_email"
                                                    class="form-control rounded-lg p-1" placeholder="Organization E-Mails. e.x : hilaryme45@gmail.com" value="{{$organization_details->organization_email}}">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="free_trial_period" class="form-control-label">Free Trial Period </label>
                                                <select required name="free_trial_period" id="free_trial_period" class="form-control">
                                                    <option value="" hidden>Select Period</option>
                                                    <option {{$organization_details->free_trial_period == "1 Month" ? "selected" : ""}} value="1 Month">1 Month</option>
                                                    <option {{$organization_details->free_trial_period == "2 Month" ? "selected" : ""}} value="2 Month">2 Month</option>
                                                    <option {{$organization_details->free_trial_period == "3 Month" ? "selected" : ""}} value="3 Month">3 Month</option>
                                                    <option {{$organization_details->free_trial_period == "4 Month" ? "selected" : ""}} value="4 Month">4 Month</option>
                                                    <option {{$organization_details->free_trial_period == "5 Month" ? "selected" : ""}} value="5 Month">5 Month</option>
                                                    <option {{$organization_details->free_trial_period == "6 Month" ? "selected" : ""}} value="6 Month">6 Month</option>
                                                    <option {{$organization_details->free_trial_period == "7 Month" ? "selected" : ""}} value="7 Month">7 Month</option>
                                                    <option {{$organization_details->free_trial_period == "8 Month" ? "selected" : ""}} value="8 Month">8 Month</option>
                                                    <option {{$organization_details->free_trial_period == "9 Month" ? "selected" : ""}} value="9 Month">9 Month</option>
                                                    <option {{$organization_details->free_trial_period == "10 Month" ? "selected" : ""}} value="10 Month">10 Month</option>
                                                    <option {{$organization_details->free_trial_period == "11 Month" ? "selected" : ""}} value="11 Month">11 Month</option>
                                                    <option {{$organization_details->free_trial_period == "12 Month" ? "selected" : ""}} value="12 Month">12 Month</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="monthly_payment" class="form-control-label">Monthly Payment <i>(per 50 clients)</i></label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend"><span class="input-group-text">Kes </span></div>
                                                    <input type="number" class="form-control" id="monthly_payment" name="monthly_payment" placeholder="E.g, 1000" value="{{$organization_details->monthly_payment}}">
                                                </div>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="registration_date" class="form-control-label">Registration Date</label>
                                                <input type="date" class="form-control" id="registration_date" name="registration_date" value="<?=date("Y-m-d", strtotime($organization_details->date_joined))?>">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="sms_sender" class="form-control-label"><b>SMS SENDER</b></label>
                                                <select name="sms_sender" id="sms_sender" class="form-control">
                                                    <option value="" hidden>Select Sender</option>
                                                    <option {{$organization_details->sms_sender == "celcom" ? "selected" : ""}} value="celcom">Celcom Kenya</option>
                                                    <option {{$organization_details->sms_sender == "afrokatt" ? "selected" : ""}} value="afrokatt">Afrokatt Kenya</option>
                                                    <option {{$organization_details->sms_sender == "hostpinnacle" ? "selected" : ""}} value="hostpinnacle">Hostpinnacle Kenya</option>
                                                    <option {{$organization_details->sms_sender == "talksasa" ? "selected" : ""}} value="talksasa">Talk Sasa Kenya</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="sms_api_key" class="form-control-label"><b>Organization API KEY / API USERNAME</b></label>
                                                <input type="text" min="0" name="sms_api_key" id="sms_api_key"
                                                    class="form-control rounded-lg p-1" placeholder="Ex : 112233" value="{{$organization_details->sms_api_key}}">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="sms_shortcode" class="form-control-label"><b>Organization SHORTCODE / SENDER ID</b></label>
                                                <input type="text" min="0" name="sms_shortcode" id="sms_shortcode"
                                                    class="form-control rounded-lg p-1" placeholder="Ex : 112233" value="{{$organization_details->sms_shortcode}}">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="sms_partner_id" class="form-control-label"><b>Organization PATNER ID/ API PASSWORD</b></label>
                                                <input type="text" min="0" name="sms_partner_id" id="sms_partner_id"
                                                    class="form-control rounded-lg p-1" placeholder="Ex : 112233" value="{{$organization_details->sms_partner_id}}">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="business_short_code" class="form-control-label"><b>Organization Paybill Number</b> <span class="text-danger">{Double check your paybill! When captured incorrectly the customer`s transactions won`t be captured automatically!}</span></label>
                                                <input type="text" min="0" name="business_short_code" id="business_short_code"
                                                    class="form-control rounded-lg p-1" placeholder="Ex : 112233" value="{{$organization_details->BusinessShortCode}}">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="mpesa_pass_key" class="form-control-label"><b>M-Pesa Passkey</b></label>
                                                <input type="text" min="0" name="mpesa_pass_key" id="mpesa_pass_key"
                                                    class="form-control rounded-lg p-1" placeholder="Ex : XXXXXXX" value="{{$organization_details->passkey}}">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="mpesa_consumer_key" class="form-control-label"><b>M-Pesa Consumer Key</b></label>
                                                <input type="text" min="0" name="mpesa_consumer_key" id="mpesa_consumer_key"
                                                    class="form-control rounded-lg p-1" placeholder="Ex : YYYYYY" value="{{$organization_details->consumer_key}}">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="mpesa_consumer_secret" class="form-control-label"><b>M-Pesa Consumer Secret</b></label>
                                                <input type="text" min="0" name="mpesa_consumer_secret" id="mpesa_consumer_secret"
                                                    class="form-control rounded-lg p-1" placeholder="Ex : ZZZZZZZZ" value="{{$organization_details->consumer_secret}}">
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-6">
                                                @php
                                                    $btnText = "<i class=\"ft-save\"></i> Update";
                                                    $otherClasses = "";
                                                    $btn_id = "";
                                                    $btnSize="sm";
                                                    $type = "submit";
                                                    $readonly = "";
                                                    $otherAttributes = "";
                                                @endphp
                                                <x-button toolTip="" btnType="success" :otherAttributes="$otherAttributes" :btnText="$btnText" :type="$type" :btnSize="$btnSize" :otherClasses="$otherClasses" :btnId="$btn_id" :readOnly="$readonly" />
                                                {{-- <button class="btn btn-success" type="submit"><i
                                                        class="ft-save"></i> Update</button> --}}
                                            </div>
                                            <div class="col-md-6">
                                                @php
                                                    $readonly="";
                                                    $btnText = "<i class=\"ft-x\"></i> Cancel";
                                                    $otherClasses = "".$readonly;
                                                    $btnLink = url()->route("Organizations");
                                                    $otherAttributes = "";
                                                @endphp
                                                <x-button-link :otherAttributes="$otherAttributes"  :btnText="$btnText" :btnLink="$btnLink" btnType="secondary" btnSize="sm" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                                {{-- <a class="btn btn-secondary btn-outline" href="{{url()->route("Organizations")}}"
                                                    type="button"><i class="ft-x"></i> Cancel</a> --}}
                                            </div>
                                        </div>
                                    </form>
                                    <hr class="mt-3">
                                    <h6 class="text-center"><u>Client Data</u></h6>
                                    <div class="row border border-primary rounded p-2">
                                        <div class="col-md-2 text-center">
                                            <p class="text-center"><b>Client : {{$client_count}} Client(s)</b></p>
                                            <hr>
                                            @php
                                                $readonly="";
                                                $btnText = "View Client(s)";
                                                $otherClasses = "".$readonly;
                                                $btnLink = route("viewOrganizationClients",$organization_details->organization_id);
                                                $otherAttributes = "";
                                            @endphp
                                            <x-button-link :otherAttributes="$otherAttributes"  :btnText="$btnText" :btnLink="$btnLink" btnType="secondary" btnSize="sm" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                            {{-- <a href="{{route("viewOrganizationClients",$organization_details->organization_id)}}" class="btn btn-sm btn-secondary">View Client(s)</a> --}}
                                        </div>
                                        <div class="col-md-3 text-center">
                                            <p class="text-center"><b>Transactions : {{$transaction_count}} Transaction(s)</b></p>
                                            <hr>
                                            @php
                                                $readonly="";
                                                $btnText = "View Transaction(s)";
                                                $otherClasses = "".$readonly;
                                                $btnLink = route("get_transactions",[$organization_details->organization_id]);
                                                $otherAttributes = "";
                                            @endphp
                                            <x-button-link :otherAttributes="$otherAttributes"  :btnText="$btnText" :btnLink="$btnLink" btnType="secondary" btnSize="sm" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                            {{-- <a href="{{route("get_transactions",[$organization_details->organization_id])}}" class="btn btn-sm btn-secondary">View Transaction(s)</a> --}}
                                        </div>
                                        <div class="col-md-2 text-center">
                                            <p class="text-center"><b>Routers : {{$routers_count}} Router(s)</b></p>
                                            <hr>
                                            @php
                                                $readonly="";
                                                $btnText = "View Router(s)";
                                                $otherClasses = "".$readonly;
                                                $btnLink = route("view_routers",[$organization_details->organization_id]);
                                                $otherAttributes = "";
                                            @endphp
                                            <x-button-link :otherAttributes="$otherAttributes"  :btnText="$btnText" :btnLink="$btnLink" btnType="secondary" btnSize="sm" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                            {{-- <a href="{{route("view_routers",[$organization_details->organization_id])}}" class="btn btn-sm btn-secondary">View Router(s)</a> --}}
                                        </div>
                                        <div class="col-md-2 text-center">
                                            <p class="text-center"><b>SMS : {{$sms_count}} SMS(es)</b></p>
                                            <hr>
                                            @php
                                                $readonly="";
                                                $btnText = "View SMS(es)";
                                                $otherClasses = "".$readonly;
                                                $btnLink = route("view_organization_sms",[$organization_details->organization_id]);
                                                $otherAttributes = "";
                                            @endphp
                                            <x-button-link :otherAttributes="$otherAttributes"  :btnText="$btnText" :btnLink="$btnLink" btnType="secondary" btnSize="sm" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                            {{-- <a href="{{route("view_organization_sms",[$organization_details->organization_id])}}" class="btn btn-sm btn-secondary">View SMS(es)</a> --}}
                                        </div>
                                        <div class="col-md-3 text-center">
                                            <p class="text-center"><b>Admin : {{$administrator_count}} Administrators</b></p>
                                            <hr>
                                            @php
                                                $readonly="";
                                                $btnText = "View Admins";
                                                $otherClasses = "".$readonly;
                                                $btnLink = route("view_organization_admin",[$organization_details->organization_id]);
                                                $otherAttributes = "";
                                            @endphp
                                            <x-button-link :otherAttributes="$otherAttributes"  :btnText="$btnText" :btnLink="$btnLink" btnType="secondary" btnSize="sm" :otherClasses="$otherClasses" :readOnly="$readonly" />
                                            {{-- <a href="{{route("view_organization_admin",[$organization_details->organization_id])}}" class="btn btn-sm btn-secondary">View Admins</a> --}}
                                        </div>
                                    </div>
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
    <script src="/theme-assets/js/core/bootstrap.bundle.min.js" type="text/javascript"></script>
    <!-- END CHAMELEON  JS-->
    <script>
        document.getElementById("DeleteTable").onclick = function () {
            document.getElementById("delete_column_details").classList.remove("hide");
            document.getElementById("delete_column_details").classList.add("show");
            document.getElementById("delete_column_details").classList.add("showBlock");
        }

        document.getElementById("close_change_wallet").onclick = function () {
            document.getElementById("change_wallet_window").classList.add("hide");
            document.getElementById("change_wallet_window").classList.remove("show");
            document.getElementById("change_wallet_window").classList.remove("showBlock");
        }

        document.getElementById("hide_change_wallet").onclick = function () {
            document.getElementById("change_wallet_window").classList.add("hide");
            document.getElementById("change_wallet_window").classList.remove("show");
            document.getElementById("change_wallet_window").classList.remove("showBlock");
        }

        document.getElementById("wallet_change_btn").onclick = function () {
            document.getElementById("change_wallet_window").classList.remove("hide");
            document.getElementById("change_wallet_window").classList.add("show");
            document.getElementById("change_wallet_window").classList.add("showBlock");
        }

        document.getElementById("expiration_date").onclick = function () {
            document.getElementById("change_expiration_date").classList.remove("hide");
            document.getElementById("change_expiration_date").classList.add("show");
            document.getElementById("change_expiration_date").classList.add("showBlock");
        }

        document.getElementById("close_change_expiration_date").onclick = function () {
            document.getElementById("change_expiration_date").classList.add("hide");
            document.getElementById("change_expiration_date").classList.remove("show");
            document.getElementById("change_expiration_date").classList.remove("showBlock");
        }

        document.getElementById("hide_change_expiration_date").onclick = function () {
            document.getElementById("change_expiration_date").classList.add("hide");
            document.getElementById("change_expiration_date").classList.remove("show");
            document.getElementById("change_expiration_date").classList.remove("showBlock");
        }

        document.getElementById("close_change_discounts").onclick = function () {
            document.getElementById("change_discounts").classList.add("hide");
            document.getElementById("change_discounts").classList.remove("show");
            document.getElementById("change_discounts").classList.remove("showBlock");
        }

        document.getElementById("hide_change_discounts").onclick = function () {
            document.getElementById("change_discounts").classList.add("hide");
            document.getElementById("change_discounts").classList.remove("show");
            document.getElementById("change_discounts").classList.remove("showBlock");
        }

        document.getElementById("discount_change_btn").onclick = function () {
            document.getElementById("change_discounts").classList.remove("hide");
            document.getElementById("change_discounts").classList.add("show");
            document.getElementById("change_discounts").classList.add("showBlock");
        }

        document.getElementById("close_this_window_delete").onclick = function () {
            document.getElementById("delete_column_details").classList.add("hide");
            document.getElementById("delete_column_details").classList.remove("show");
            document.getElementById("delete_column_details").classList.remove("showBlock");
        }

        document.getElementById("hide_delete_column").onclick = function () {
            document.getElementById("close_this_window_delete").click();
        }
    </script>

    <!-- BEGIN CLIENT JS-->
    {{-- <script src="/theme-assets/js/core/add_organization.js"></script> --}}
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