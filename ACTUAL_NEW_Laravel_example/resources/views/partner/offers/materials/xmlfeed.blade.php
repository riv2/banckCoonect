<form method="get" action="{{ route("partner.links.create") }}">
    @csrf

    <input type="hidden" name="id" value="{{ $offer->id }}">
    <input type="hidden" name="offer_material_id" value="{{ $item->offer_material_id }}">
    <div class="card">
        <div class="card-header">{{ $item->name }}</div>
        <div class="card-body">
            <button class="btn btn-primary">{{ __('partners.offers.materials.xmlfeed.get_link') }}</button>
        </div>
    </div>
    <br>
    <button class="btn btn-primary">Получить ссылку</button>

</form>
