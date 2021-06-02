@props(['format' => 'd F Y H:i', 'value'])
<time {{ $attributes->merge(['class' => 'text-nowrap']) }} title="{{ $value->format($format) }}" datetime="{{ $value->toDateTimeString() }}">{{ $value->format($format) }}</time>
