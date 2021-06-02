<h2>@if(isset($item)) {{ __('advertiser.offersmaterials.material-xmlfeed.edit') }} @else {{ __('advertiser.offersmaterials.material-xmlfeed.add') }} @endif
    {{ __('advertiser.offersmaterials.material-xmlfeed.xml-feeds') }}</h2>
<div class="form-group">
    <input class="form-control" name="name" type="text" value="@if(isset($item)) {{ $item->name }} @endif"
           placeholder="{{ __('advertiser.offersmaterials.material-xmlfeed.name') }}" required>
    <label for="">{{ __('advertiser.offersmaterials.material-xmlfeed.name') }}</label>
</div>
<div class="form-group">
    <input class="form-control" name="link" type="url"
           value="@if(isset($item)) {{ $item->material_params["link"] }} @endif" placeholder="{{ __('advertiser.offersmaterials.material-xmlfeed.link-placeholder') }}" required>
    <label for="">{{ __('advertiser.offersmaterials.material-xmlfeed.link') }}</label>
</div>
<br> <br>
<button type="submit" class="btn btn-primary"><span>{{ __('advertiser.offersmaterials.material-xmlfeed.submit') }}</span></button>

