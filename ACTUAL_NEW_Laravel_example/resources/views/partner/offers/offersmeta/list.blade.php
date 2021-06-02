<div class="card-text"><small
        class="text-muted">{{ $v["title"] }}:
    @foreach($offer->getMeta($k, $v["type"]) as $item_k => $item_v)
        <span class="badge badge-light" style="background:#060">{{ $item_v }}</span>
    @endforeach
    </small></div>
