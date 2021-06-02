<div class="col-md-6">
        <div class="form-group">
            <label for="neighborhood"> <i
                        class="fa fa-map-marker"></i> @lang('ecommerce::master.neighborhood')
                :</label>
            <div class="input-group">

                <select class="form-control" name="delivery_id"
                        id="delivery_id" onchange="updateTotalCost()">
                    <option id="address_default" price="0"
                            value="default">@lang('ecommerce::master.no_one')</option>
                    @foreach($addresses as $address)
                        <option id="address_{{ $address->id }}"
                                price="{{$address->price ?? ''}}"
                                value="{{ $address->id }}" {{ !empty($current_address) ?( $current_address->delivery_id == $address->id ? 'selected': '') :'' }} >{{ $address->name }}</option>
                    @endforeach
                </select>
                @if ($errors->has('delivery_id'))
                    <span class="help-block">
                      <strong>{{ $errors->first('delivery_id') }}</strong>
                  </span>
                @endif
            </div>
        </div>
</div>
