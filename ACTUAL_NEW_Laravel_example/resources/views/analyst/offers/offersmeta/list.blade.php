@php
$selected = $offer->getMeta($k, "list");
@endphp
@foreach($options as $item_k => $item_v)
<div class="form-check">
    <input type="{{ ($multiple) ? 'checkbox' : 'radio' }}" name="{{ $k }}[]" value="{{ $item_k }}" class="form-check-input" id="check_{{ $k }}_{{ $loop->index }}" @if(in_array($item_k, $selected)) checked @endif>
    <label class="form-check-label" for="check_{{ $k }}_{{ $loop->index }}">
        {{ $item_v }}
    </label>
</div>
@endforeach
