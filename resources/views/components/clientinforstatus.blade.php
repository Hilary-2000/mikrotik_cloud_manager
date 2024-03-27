<div>
    <!-- Let all your things have their places; let each part of your business have its time. - Benjamin Franklin -->
    <a href='{{route("viewOrganizationClients", $organizationdetails->organization_id)}}' class="btn btn-infor"><i class="fas fa-arrow-left"></i> Back
        to list</a>
    @if (session('success'))
        <p class="success">{{ session('success') }}</p>
    @endif
    @if (session('error'))
        <p class="danger">{{ session('error') }}</p>
    @endif
    <div class="row">
        <div class="col-md-9">
            <p><strong>Note: </strong><br> - User status when active the user will recieve
                internet connection. <br>
                - Automate transaction when active the system will monitor the clients payment
                process and activate or deactivate the client when necessary <br>
                - When a user is frozen don`t activate any option either the <b>Automate Transaction</b> or the <b>User Status</b>
            </p>
        </div>
        <div class="col-md-3 border-left border-secondary">
            <button id="prompt_delete" class="btn btn-secondary float-right btn-sm " {{$readonly}}><i class="fas fa-trash"></i> Delete</button>
            <div class="container d-none" id="prompt_del_window">
                <p class="text-primary" ><strong>Are you sure you want to permanently delete this client?</strong></p>
                <div class="row">
                    <div class="col-md-6">
                        <a href="{{route("delete_user",[$organizationdetails->organization_id,$clientsdata[0]->client_id])}}" class="btn btn-danger btn-sm {{$readonly}}" >Yes</a>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-secondary btn-sm" id="delet_user_no">No</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 card shadow-lg border-right border-infor">
            {{-- client active status --}}
            @if ($clientsdata[0]->client_status == 1)
                <div class="row my-1 border-bottom border-light p-1">
                    <div class="col-sm-6"><strong>User status:</strong></div>
                    <div class="col-sm-6"><a
                            href="{{ route("deactivate_client",[$organizationdetails->organization_id,$clientsdata[0]->client_id]) }}"
                            class="btn btn-sm btn-danger my-1">De-Activate</a><p class="text-success d-none"><b>Activated</b></p></div>
                </div>
            @else
                <div class="row my-1 border-bottom border-light">
                    <div class="col-sm-6"><strong>User status:</strong></div>
                    <div class="col-sm-6"><a
                            href="{{ route("activate_client",[$organizationdetails->organization_id,$clientsdata[0]->client_id]) }}"
                            class="btn btn-sm btn-success my-1">Activate</a><p class="text-danger d-none"><b>De-activated</b></p></div>
                </div>
            @endif

            {{-- client payment automatiom --}}
            @if ($clientsdata[0]->payments_status == 1)
                <div class="row my-1 border-bottom border-light py-1">
                    <div class="col-sm-6"><strong>Automate Transaction:</strong>
                    </div>
                    <div class="col-sm-6"><a
                            href="{{ route("deactivate_payment",[$organizationdetails->organization_id,$clientsdata[0]->client_id]) }}"
                            class="btn btn-sm btn-danger">De-Activate</a><p class="text-success d-none"><b>Activated</b></p></div>
                </div>
            @else
                <div class="row my-1 border-bottom border-light py-1">
                    <div class="col-sm-6"><strong>Automate Transaction:</strong>
                    </div>
                    <div class="col-sm-6"><a
                            href="{{ route("activate_payment",[$organizationdetails->organization_id,$clientsdata[0]->client_id]) }}"
                            class="btn btn-sm btn-success">Activate</a><p class="text-danger d-none"><b>De-activated</b></p></div>
                </div>
            @endif
            <div class="row border-bottom border-light py-2">
                <div class="col-sm-6"><strong>Expiration Date:</strong><button class="text-secondary btn btn-infor btn-sm mx-1" {{$readonly}} style="width: fit-content;" id="edit_epiration"><i class="fas fa-pen"></i> Edit</button>
                </div>
                <div class="col-sm-6">{{$expiredate? $expiredate: "Null"}}</div>
            </div>
            <div id="change_exp_date_windoe" class="w-100 d-none py-2">
                <hr class="mt-0">
                <form action="{{route("change_expiry_date",[$organizationdetails->organization_id])}}" method="post" class="form-control-group">
                    @csrf
                    <h6 class="text-center" >Change Expiration Date</h6>
                    <input type="hidden" name="clients_id"
                        value="{{ $clientsdata[0]->client_id }}">
                    <label for="expiration_date_edits" class="form-control-label" id="">New Expiration Date</label>
                    <input type="date" required name="expiration_date_edits" id="expiration_date_edits" class="form-control" placeholder="New Expiration Date">
                    <div class="row">
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary my-1"><i class="fas fa-save"></i> Save</button>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-secondary my-1" type="button" id="cancel_exp_update">Cancel</button>
                        </div>
                    </div>
                </form>
                <hr>
            </div>
            <div class="row my-1 border-bottom border-light py-2">
                <div class="col-sm-7"><strong class="text-secondary">Freeze Client:</strong> <span class="badge {{$clientsdata[0]->client_freeze_status == "1" || date("YmdHis") < date("YmdHis",strtotime($clientsdata[0]->freeze_date)) ? "badge-success" : "badge-danger";}}">{{$clientsdata[0]->client_freeze_status == "1" || date("YmdHis") < date("YmdHis",strtotime($clientsdata[0]->freeze_date)) ? "Active" : "In-Active";}}</span> <button class="text-secondary btn btn-infor btn-sm mx-1" {{$readonly}} id="edit_freeze_client"><i class="fas fa-pen"></i> Edit</button></div>
                <div class="col-sm-5">
                    <p>{{date("YmdHis") < date("YmdHis",strtotime($clientsdata[0]->freeze_date)) ? "Client will be frozen on : ".date("D dS M Y",strtotime($clientsdata[0]->freeze_date))." until " : "Frozen Until:"}} {{isset($freeze_date) && strlen($freeze_date) > 0 ? $freeze_date : "Not Set"}}</p>
                </div>
            </div>
            <div id="change_freeze_date_window" class="w-100 d-none">
                <hr class="mt-0">
                <form action="{{route("set_freeze_date", [$organizationdetails->organization_id])}}" method="post" class="form-control-group border border-primary rounded p-1">
                    @csrf
                    <h6 class="text-center" >Freeze Until</h6>
                    @if ($clientsdata[0]->client_freeze_status == "1" || date("YmdHis") < date("YmdHis",strtotime($clientsdata[0]->freeze_date)))
                        <a href="{{route("deactivate_freeze",[$organizationdetails->organization_id,$clientsdata[0]->client_id])}}" class="btn btn-secondary">Deactivate Freeze</a>
                        <hr>
                    @else
                        {{-- <a href="/Client/activate_freeze/{{$clientsdata[0]->client_id}}" class="btn btn-danger">Activate</a> 
                        <hr>--}}
                    @endif
                    <br>
                    <div class="container">
                        <label for="freeze_date">Freeze Date</label>
                        <select name="freeze_date" required id="freeze_date" class="form-control">
                            <option value="" hidden>Select Option</option>
                            <option value="set_freeze">Set Freezing Date</option>
                            <option selected value="freeze_now">Freeze Now</option>
                        </select>
                    </div>
                    <div class="container d-none" id="setFreezeDate">
                        <label for="freezing_date">Select Freeze Date</label>
                        <input required type="date" name="freezing_date" id="freezing_date" value="{{date("Y-m-d",strtotime("1 day"))}}" min="{{date("Y-m-d")}}" class="form-control">
                    </div>
                    <div class="container">
                        <label for="freeze_type">Freeze Type</label>
                        <select name="freeze_type" required id="freeze_type" class="form-control">
                            <option value="" hidden>Select Option</option>
                            <option selected value="definate">Definate Freezing</option>
                            <option value="Indefinite">In-definate Freezing</option>
                        </select>
                    </div>
                    <div class="container" id="freeze_window">
                        <input type="hidden" name="clients_id"
                            value="{{ $clientsdata[0]->client_id }}">
                        <input type="hidden" name="indefinate_freezing" value="00000000000000">
                        <label for="freez_dates_edit" class="form-control-label" id="">Freeze until</label>
                        <input type="date" required name="freez_dates_edit" id="freez_dates_edit" class="form-control" min="<?php echo date("Y-m-d",strtotime("1 day"));?>" value='{{date("Y-m-d",strtotime("1 day"))}}' placeholder="New Expiration Date">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary my-1"><i class="fas fa-save"></i> Save</button>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-secondary my-1" type="button" id="cancel_freeze_dates">Cancel</button>
                        </div>
                    </div>
                </form>
                <hr>
            </div>
            <div class="row my-1 border-bottom border-light py-2">
                <div class="col-sm-7"><strong class="text-secondary">Minimum Payment:</strong> <button class="text-secondary btn btn-infor btn-sm mx-1" {{$readonly}} id="edit_minimum_amount"><i class="fas fa-pen"></i> Edit</button></div>
                <div class="col-sm-5">
                    {{$clientsdata[0]->min_amount != 100 ? "Kes ".number_format(($clientsdata[0]->min_amount / 100) * $clientsdata[0]->monthly_payment)." (".$clientsdata[0]->min_amount."%) of Kes ".number_format($clientsdata[0]->monthly_payment) : "Full Payment (Kes ".number_format($clientsdata[0]->monthly_payment).")"}}
                </div>
            </div>
            <form method="POST" action="{{route("set_minimum_pay", [$organizationdetails->organization_id, $clientsdata[0]->client_id])}}" id="hide_min_pay_window" class="form-control-group border border-primary rounded p-1 d-none">
                @csrf
                <h6 class="text-center">Change Minimum Payment</h6>
                <input type="hidden" value="{{$clientsdata[0]->client_id}}" name="client_id">
                <label for="change_minimum_payment" class="form-control-label">Change Minimum Payment</label>
                <select name="change_minimum_payment" id="change_minimum_payment" class="form-control" required>
                    <option hidden value="">Select Payment Option</option>
                    <option {{$clientsdata[0]->min_amount == "10" ? "selected" : ""}} value="10">10%</option>
                    <option {{$clientsdata[0]->min_amount == "15" ? "selected" : ""}} value="15">15%</option>
                    <option {{$clientsdata[0]->min_amount == "25" ? "selected" : ""}} value="25">25% (¼ Payment)</option>
                    <option {{$clientsdata[0]->min_amount == "50" ? "selected" : ""}} value="50">50% (½ Payment)</option>
                    <option {{$clientsdata[0]->min_amount == "75" ? "selected" : ""}} value="75">75% (¾ Payment)</option>
                    <option {{$clientsdata[0]->min_amount == "80" ? "selected" : ""}} value="80">80%</option>
                    <option {{$clientsdata[0]->min_amount == "90" ? "selected" : ""}} value="90">90%</option>
                    <option {{$clientsdata[0]->min_amount == "100" ? "selected" : ""}} value="100">Full Payment</option>
                </select>
                <button type="submit" class="btn btn-primary btn-sm mt-1 w-100">Save</button>
            </form>
        </div>
        <div class="col-md-6 card shadow-lg">
            <div class="row my-1 border-bottom border-light py-1">
                <div class="col-sm-6"><strong>Registration Date:</strong></div>
                <div class="col-sm-6">{{ $registrationdate }}
                </div>
            </div>
            <div class="row my-1 border-bottom border-light py-1">
                <div class="col-sm-6"><strong>Wallet Amount:</strong><button class="text-secondary btn btn-infor btn-sm mx-1" {{$readonly}} style="width: fit-content;" id="edit_wallet"><i class="fas fa-pen"></i> Edit</button></div>
                <div class="col-sm-6">Kes {{ $clientsdata[0]->wallet_amount }}
                </div>
            </div>
            <div id="change_wallet_window" class="w-100 d-none">
                <hr class="mt-0">
                <form action="{{route("change_wallet_balance", [$organizationdetails->organization_id, $clientsdata[0]->client_id])}}" method="post" class="form-control-group">
                    @csrf
                    <h6 class="text-center" >Change wallet balance</h6>
                    <input type="hidden" name="clients_id"
                        value="{{ $clientsdata[0]->client_id }}">
                    <label for="wallet_amounts" class="form-control-label" id="">New Wallet Amount</label>
                    <input type="number" required name="wallet_amounts" id="wallet_amounts" class="form-control" placeholder="New wallet amounts">
                    <div class="row">
                        <div class="col-md-6">
                            <button {{$readonly}} type="submit" class="btn btn-primary my-1"><i class="fas fa-save"></i> Save</button>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-secondary my-1" type="button" id="cancel_wallet_updates">Cancel</button>
                        </div>
                    </div>
                </form>
                <hr>
            </div>
            <div class="row my-1 border-bottom border-light py-1">
                <div class="col-sm-6"><strong>Account Number:</strong></div>
                <div class="col-sm-6">{{ $clientsdata[0]->client_account }}
                </div>
            </div>
            <div class="row my-1 border-bottom border-light py-1">
                <div class="col-sm-6"><strong>Location:</strong></div>
                <div class="col-sm-6">
                    @php
                        echo $clientsdata[0]->location_coordinates ? "<a class='text-danger' href = 'https://www.google.com/maps/place/".$clientsdata[0]->location_coordinates."' target = '_blank'><u>Locate Client</u> </a>" :"No Co-ordinates provided for the client!" ;
                    @endphp
                </div>
            </div>
            <div class="row my-1 border-bottom border-light py-1">
                <div class="col-sm-6"><strong>Reffered By:<button class="text-secondary btn btn-infor btn-sm mx-1 d-none" {{$readonly}} id="edit_refferal"><i class="fas fa-pen"></i> Edit</button></strong></div>
                <div class="col-sm-6">
                    <p>{{$client_refferal ?? 'Refferal Not set'}} </p>
                </div>
            </div>
            <div id="set_refferal_window" class="d-none w-100 border border-primary shadow p-2 ">
                <h6 class="text-center" >Set refferal</h6>
                <div class="form-control-group">
                    <p><b>What you need to know:</b></p>
                    <p>- Start by searching the refferer<br>
                        - If the refferer is valid set the refferers cut<br>
                        - Then save. <br>
                        - If there was a refferer before it will replace their details with the new refferer
                    </p>
                    <label for="wallet_amounts" class="form-control-label" id="">Search Refferer
                        <span class="invisible" id="search_referer_loader"><i class="fas ft-rotate-cw fa-spin"></i></span></label>
                    <div class="row">
                        <div class="col-md-9">
                            <div class="autocomplete">
                                <input type="text" required name="search_refferer_keyword" id="search_refferer_keyword" class="form-control" placeholder="Type keyword: name, acc no, phone number">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-infor" id="find_user_refferal" type="button"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                    <p id="refferer_data" class="d-none"></p>
                    <span id="show_data_inside"></span>
                    <hr class="border border-primary">
                    <div class="container my-2">
                        <h6 class="text-center"><u>Refferer Details</u></h6>
                    </div>
                    <form action="/set_refferal" method="post">
                        @csrf
                        <div class="row my-2">
                            <input type="hidden" name="clients_id"
                        value="{{ $clientsdata[0]->client_id }}">
                            <input type="hidden" name="refferal_account_no" id="refferer_acc_no2">
                            <div class="col-md-6">
                                <p><b>Refferer Fullname</b></p>
                            </div>
                            <div class="col-md-6">
                                <p class="user_data" id="refferer_name">{{$reffer_details[0] ?? 'Unknown'}}</p>
                            </div>
                            <div class="col-md-6">
                                <p><b>Refferer Acc No</b></p>
                            </div>
                            <div class="col-md-6">
                                <p class="user_data" id="refferer_acc_no">{{$reffer_details[1] ?? 'Unknown'}}</p>
                            </div>
                            <div class="col-md-6">
                                <p><b>Refferer wallet</b></p>
                            </div>
                            <div class="col-md-6">
                                <p class="user_data" id="reffer_wallet">{{$reffer_details[2] ?? 'Unknown'}}</p>
                            </div>
                            <div class="col-md-6">
                                <p><b>Refferer Location</b></p>
                            </div>
                            <div class="col-md-6">
                                <p class="user_data" id="refferer_location">{{$reffer_details[3] ?? 'Unknown'}}</p>
                            </div>
                        </div>
                        <div class="">
                            <label for="refferer_amount" class="form-control-label">Refferer Cut</label>
                            <input type="number" class="form-control" name="refferer_amount" id="refferer_amount" placeholder="Refferers Cut - how much is he given" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary my-1" {{$readonly}} id="save_data_inside"><i class="fas fa-save"></i> Set</button>
                            </div>
                            <div class="col-md-6">
                                <button class="btn btn-secondary my-1" type="button" id="cancel_refferer_updates">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
                <hr>
            </div>
        </div>
    </div>
</div>