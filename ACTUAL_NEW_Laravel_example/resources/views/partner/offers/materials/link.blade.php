<form method="post" action="{{ route("partner.links.create") }}">
    @csrf
    <input type="hidden" name="id" value="{{ $offer->id }}">
    <input type="hidden" name="offer_material_id" value="@isset($item){{ $item->offer_material_id }}@endisset">
    <div class="card">
        <div class="card-header">@isset($item){{ $item->name }}@endisset</div>
        <div class="card-body">
            <em>{{ __('partners.offers.materials.link.allowed_domain') }}: @isset($item){{ $item->material_params["link"] }}@endisset</em>
            <input type="url" name="link" class="form-control" value="" required placeholder="{{ __('partners.offers.materials.link.placeholder') }}">
            <button class="btn btn-primary">{{ __('partners.offers.materials.link.create_link') }}</button>
        </div>
    </div>
    <br>
</form>
