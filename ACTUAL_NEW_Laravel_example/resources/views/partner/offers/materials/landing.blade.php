<form method="get" action="{{ route("partner.links.create") }}">
    <input type="hidden" name="offer_id" value="{{ $offer->id }}">
    <input type="hidden" name="offer_material_id" value="@isset($item){{ $item->offer_material_id }}@endisset">
    <em>@isset($item){{ $item->material_params["link"] }}@endisset</em>
    <button class="btn btn-primary btn-sm">{{ __('partners.offers.materials.landing.get_link') }}</button>
</form>
