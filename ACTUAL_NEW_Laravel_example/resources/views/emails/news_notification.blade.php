@component('mail::layout')
{{-- Header --}}
@slot('header')
@if ($news->pp_id)
    @component('mail::header', ['url' => 'https://' . $news->pp->prod_domain ?? $news->pp->tech_domain])
        @if ($news->pp->logo)
            <img src="https://{{ $news->pp->prod_domain ?? $news->pp->tech_domain }}{{ $news->pp->logo }}" height="100">
        @else
            {{ $news->pp->short_name }}
        @endif
    @endcomponent
@else
    @component('mail::header', ['url' => 'https://gocpa.cloud'])
        GoCPA cloud
    @endcomponent
@endif
@endslot

{{-- Body --}}
{!! $news->news_text_parsed !!}

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
@isset($notifiable)
© {{ date('Y') }} <a href="https://{{ $notifiable->pp_domain ?? 'gocpa.ru' }}">{{ $notifiable->pp_name ?? 'GoCPA.ru' }}</a>

@if ($notifiable->unsubscribe_link)
[отписаться]({{ $notifiable->unsubscribe_link }})
@endif

@endisset
@endcomponent
@endslot
@endcomponent
