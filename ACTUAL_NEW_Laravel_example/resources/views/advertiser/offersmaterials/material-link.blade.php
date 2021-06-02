<h2>@if(isset($item)) {{ __('advertiser.offersmaterials.material-link.edit') }} @else {{ __('advertiser.offersmaterials.material-link.add') }} @endif
    {{ __('advertiser.offersmaterials.material-link.links') }}</h2>
<div class="form-group">
    <input class="form-control" name="link" type="url"
           value="@if(isset($item)) {{ $item->material_params["link"] }} @endif"
           placeholder="{{ __('advertiser.offersmaterials.material-link.input-label') }}" required>
    <label for="">{{ __('advertiser.offersmaterials.material-link.input-label') }}</label>
</div>
<br> <br>
<button type="submit" class="btn btn-primary"><span>{{ __('advertiser.offersmaterials.material-link.submit') }}</span></button>

