<!-- Reference Code Modal -->
<div class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title h4" id="mySmallModalLabel">{{translate('reference')}} {{translate('code')}} {{translate('add')}}</h5>
                <button type="button" class="btn btn-xs btn-icon btn-ghost-secondary" data-dismiss="modal" aria-label="Close">
                    <i class="tio-clear tio-lg"></i>
                </button>
            </div>
            <form action="{{route('admin.orders.add-payment-ref-code',[$order['id']])}}" method="post">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <input type="text" name="transaction_reference" class="form-control" placeholder="{{translate('EX : Code123')}}" required>
                    </div>
                    <button class="btn btn-primary">{{translate('submit')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delivery Information Modal -->
<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="deliveryInfoModal" id="deliveryInfoModal" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title h4" id="mySmallModalLabel">{{translate('Update_Delivery_Information')}}</h5>
                <button type="button" class="btn btn-xs btn-icon btn-ghost-secondary" data-dismiss="modal" aria-label="Close">
                    <i class="tio-clear tio-lg"></i>
                </button>
            </div>
            <form action="{{route('admin.orders.update-shipping')}}" method="post">
                @csrf
                <input type="hidden" name="user_id" value="{{$order->user_id}}">
                <input type="hidden" name="order_id" value="{{$order->id}}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>{{translate('Type')}}</label>
                                <input type="text" name="address_type" class="form-control" placeholder="{{translate('EX : Home')}}" value="{{ $address['address_type'] ?? '' }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="input-label" for="">{{ translate('contact_person_name') }}
                                    <span class="input-label-secondary text-danger">*</span></label>
                                <input type="text" class="form-control" name="contact_person_name" placeholder="{{translate('EX : Jhon Doe')}}" value="{{ $address['contact_person_name'] ?? '' }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="input-label" for="">{{ translate('Contact Number') }}
                                    <span class="input-label-secondary text-danger">*</span></label>
                                <input type="text" class="form-control" name="contact_person_number" placeholder="{{translate('EX : 01888888888')}}" value="{{ $address['contact_person_number']?? '' }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>{{translate('floor')}}</label>
                                <input type="text" class="form-control" name="floor" placeholder="{{translate('EX : 5')}}" value="{{ $address['floor'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>{{translate('house')}}</label>
                                <input type="text" class="form-control" name="house" placeholder="{{translate('EX : 21/B')}}" value="{{ $address['house'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>{{translate('road')}}</label>
                                <input type="text" class="form-control" name="road" placeholder="{{translate('EX : Baker Street')}}" value="{{ $address['road'] ?? '' }}">
                            </div>
                        </div>

                        @php($googleMapStatus = \App\CentralLogics\Helpers::get_business_settings('google_map_status'))
                        @if($googleMapStatus)
                            @if($order?->branch?->delivery_charge_setup?->delivery_charge_type == 'distance')
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="input-label" for="">{{ translate('latitude') }}
                                            <span class="input-label-secondary text-danger">*</span></label>
                                        <input type="text" class="form-control" name="latitude" placeholder="{{translate('EX : 23.796584198263794')}}" value="{{ $address['latitude'] ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="input-label" for="">{{ translate('longitude') }}<span class="input-label-secondary text-danger">*</span></label>
                                        <input type="text" class="form-control" name="longitude" placeholder="{{translate('EX : 23.796584198263794')}}" value="{{ $address['longitude'] ?? '' }}" required>
                                    </div>
                                </div>
                            @endif
                        @endif

                        <div class="col-md-12">
                            <div class="form-group">
                                <label>{{translate('Address')}}<span class="input-label-secondary text-danger">*</span></label>
                                <textarea class="form-control" name="address" cols="30" rows="3" placeholder="{{translate('EX : Dhaka,_Bangladesh')}}" required>{{ $address['address'] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-primary">{{translate('submit')}}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Preparation Time Modal -->
@if($order['order_type'] != 'pos' && $order['order_type'] != 'take_away' && !in_array($order['order_status'], ['delivered', 'returned', 'canceled', 'failed', 'completed']))
    <div class="modal fade" id="counter-change" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title custom-text-size" id="exampleModalLabel">{{ translate('Need time to prepare the food') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{route('admin.orders.increase-preparation-time', ['id' => $order->id])}}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group text-center">
                            <input type="number" min="0" name="extra_minute" id="extra_minute" class="form-control" placeholder="{{translate('EX : 20')}}" required>
                        </div>
                        <div class="form-group flex-between predefined-time-input">
                            <div class="badge text-info shadow li-pointer" data-time="10">{{ translate('10min') }}</div>
                            <div class="badge text-info shadow li-pointer" data-time="20">{{ translate('20min') }}</div>
                            <div class="badge text-info shadow li-pointer" data-time="30">{{ translate('30min') }}</div>
                            <div class="badge text-info shadow li-pointer" data-time="40">{{ translate('40min') }}</div>
                            <div class="badge text-info shadow li-pointer" data-time="50">{{ translate('50min') }}</div>
                            <div class="badge text-info shadow li-pointer" data-time="60">{{ translate('60min') }}</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ translate('Close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ translate('Submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<!-- Delivery Area Modal -->
<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editArea" id="editArea" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title h4" id="mySmallModalLabel">{{translate('Update_Delivery_Area')}}</h5>
                <button type="button" class="btn btn-xs btn-icon btn-ghost-secondary" data-dismiss="modal" aria-label="Close">
                    <i class="tio-clear tio-lg"></i>
                </button>
            </div>
            <form action="{{ route('admin.orders.update-order-delivery-area', ['order_id' => $order->id]) }}" method="post">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <?php
                        $branch = \App\Model\Branch::with(['delivery_charge_setup', 'delivery_charge_by_area'])
                            ->where(['id' => $order['branch_id']])
                            ->first(['id', 'name', 'status']);
                        ?>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{translate('Delivery Area')}}</label>
                                <select name="selected_area_id" class="form-control js-select2-custom-x mx-1" id="areaDropdown">
                                    <option value="">{{ translate('Select Area') }}</option>
                                    @foreach($branch->delivery_charge_by_area as $area)
                                        <option value="{{$area['id']}}" {{ (isset($order->order_area) && $order->order_area->area_id == $area['id']) ? 'selected' : '' }} data-charge="{{$area['delivery_charge']}}">{{ $area['area_name'] }} - ({{ Helpers::set_symbol($area['delivery_charge']) }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="input-label" for="">{{ translate('Delivery Charge') }} ({{ Helpers::currency_symbol() }})</label>
                            <input type="number" class="form-control" name="delivery_charge" id="deliveryChargeInput" value="" readonly>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-primary">{{translate('update')}}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Payment Status Modal -->
<div class="modal fade" id="paymentStatusModal" tabindex="-1" role="dialog" aria-labelledby="paymentStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentStatusModalLabel">{{translate('Payment Status')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="paymentStatusForm">
                    <input type="hidden" id="orderId" name="order_id">
                    <input type="hidden" id="paymentAction" name="payment_action">
                    
                    <div class="form-group">
                        <label for="paymentMethod">{{translate('Payment Method')}}</label>
                        <select class="form-control" id="paymentMethod" name="payment_method" required>
                            <option value="">{{translate('Select Payment Method')}}</option>
                            <option value="cash">{{translate('Cash')}}</option>
                            <option value="card">{{translate('Card')}}</option>
                            <option value="bank_transfer">{{translate('Bank Transfer')}}</option>
                            <option value="digital_wallet">{{translate('Digital Wallet')}}</option>
                            <option value="other">{{translate('Other')}}</option>
                        </select>
                    </div>
                    
                    <div class="form-group" id="referenceCodeGroup" style="display: none;">
                        <label for="referenceCode">{{translate('Reference Code')}}</label>
                        <input type="text" class="form-control" id="referenceCode" name="reference_code" placeholder="{{translate('Enter reference code')}}">
                    </div>
                    
                    <div class="form-group" id="refundReasonGroup" style="display: none;">
                        <label for="refundReason">{{translate('Refund Reason')}}</label>
                        <textarea class="form-control" id="refundReason" name="refund_reason" rows="3" placeholder="{{translate('Enter refund reason')}}"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="notes">{{translate('Notes')}}</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="{{translate('Additional notes (optional)')}}"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{translate('Cancel')}}</button>
                <button type="button" class="btn btn-primary" id="confirmPaymentStatus">{{translate('Confirm')}}</button>
            </div>
        </div>
    </div>
</div>
