<h2 class="mb-3">@if(isset($item)) {{ __('advertiser.offersmaterials.material-banner.edit') }}
    @else {{ __('advertiser.offersmaterials.material-banner.add') }} @endif
    {{ __('advertiser.offersmaterials.material-banner.banners') }}</h2>
<div class="form-group mb-3">
    <label  class="col-form-label text-dark" for="">{{ __('advertiser.offersmaterials.material-banner.name') }}</label>
    <input class="form-control" name="name" type="text" value="@if(isset($item)) {{ $item->name }} @endif"
           placeholder="{{ __('advertiser.offersmaterials.material-banner.name') }}" required>
</div>
<div class="form-group">
    <strong>{{ __('advertiser.offersmaterials.material-banner.files') }}</strong> <br>
    <div class="mt-2" id="filelist"></div>
    <input type="file" name="banner[]" multiple>
</div>
@if(isset($item))
    @foreach($item->material_files as $file)
        <br>
        <div class="card">
            <div class="card-header text-end">
                <button class="btn btn-danger"><span class="fa fa-trash"></span></button>
            </div>
            <div class="card-body"><img src="/{{ $file }}" height="60"></div>
        </div>
    @endforeach
@endif
<br> <br>
<button type="submit" class="btn btn-primary"><span>{{ __('advertiser.offersmaterials.material-banner.submit') }}</span>
</button>

