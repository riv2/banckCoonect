<h2>@if(isset($item)) {{ __('advertiser.offersmaterials.material-pwa.edit') }} @else {{ __('advertiser.offersmaterials.material-pwa.add') }} @endif
    {{ __('advertiser.offersmaterials.material-pwa.pwa') }}</h2>
<div class="form-group">
    <input class="form-control" name="name" type="text" value="@if(isset($item)) {{ $item->name }} @endif"
           placeholder="{{ __('advertiser.offersmaterials.material-pwa.name') }}" required>
    <label for="">{{ __('advertiser.offersmaterials.material-pwa.name') }}</label>
</div>
<div class="form-group">
    <textarea class="form-control" name="script" placeholder="{{ __('advertiser.offersmaterials.material-pwa.script') }}" required>@if(isset($item)) {{ $item->material_params["script"] }} @endif</textarea>
    <label for="">{{ __('advertiser.offersmaterials.material-pwa.script') }}</label>
    <em>{{ __('advertiser.offersmaterials.material-pwa.script-p') }}: %partner_id% , %offer_id%</em>
</div>
<br> <br>
<button type="submit" class="btn btn-primary"><span>{{ __('advertiser.offersmaterials.material-pwa.submit') }}</span></button>

