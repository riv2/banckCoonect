@php
$list = $v["model"]::all()->pluck($v["value"],$v["key"])->toArray();
@endphp
<div class="card-text"><small
        class="text-muted">{{ $v["title"] }}:
    @foreach($offer->getMeta($k, $v["type"]) as $item_k => $item_v)
        @if(isset($list[$item_v]))
        <span class="badge badge-light" style="background:#060">{{ $list[$item_v] }}</span>
        @endif
    @endforeach
    </small></div>

