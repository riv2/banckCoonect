<h2 class="mb-3">@if(isset($item)) {{ __('advertiser.offersmaterials.material-landing.edit') }} @else {{ __('advertiser.offersmaterials.material-landing.add') }} @endif
    {{ __('advertiser.offersmaterials.material-landing.landing') }}</h2>
<div class="form-group mb-2">
    <label class="col-form-label text-dark" for="">{{ __('advertiser.offersmaterials.material-landing.name') }}</label>
    <input class="form-control" name="name" type="text" value="@if(isset($item)) {{ $item->name }} @endif"
           placeholder="{{ __('advertiser.offersmaterials.material-landing.name') }}" required autofocus>
</div>
<div class="form-group">
    <label class="col-form-label text-dark" for="">{{ __('advertiser.offersmaterials.material-landing.link') }}</label>
    <input class="form-control" name="link" type="url"
           value="@if(isset($item)) {{ $item->material_params["link"] }} @endif"
           placeholder="{{ __('advertiser.offersmaterials.material-landing.url') }}" required>
</div>
<br> <br>
<button type="submit" class="btn btn-primary">
    <span>{{ __('advertiser.offersmaterials.material-landing.submit') }}</span></button>
