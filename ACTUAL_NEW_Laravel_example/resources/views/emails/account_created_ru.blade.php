@component('mail::layout')
{{-- Header --}}
{{--
@slot('header')
@if ($notifiable->pp_id)
    @component('mail::header', ['url' => 'https://' . $notifiable->pp->pp_domain])
        @if ($notifiable->pp->logo)
            <img src="https://{{ $notifiable->pp->pp_domain }}/{{ $notifiable->pp->logo }}" height="100">
        @else
            {{ $notifiable->pp->short_name }}
        @endif
    @endcomponent
@else
    @component('mail::header', ['url' => 'https://gocpa.cloud'])
        GoCPA cloud
    @endcomponent
@endif
@endslot
--}}

# Здравствуйте, {{ $notifiable->name }}!

Вы зарегистрировались в партнёрской программе {{ $notifiable->pp->pp_domain }}

Ваш логин: {{ $notifiable->email }}

Ваш пароль: {{ $generated_password }}

{{--
@component('mail::button', ['url' => 'https://' . $notifiable->pp->pp_domain . '/login/onetime/' . $notifiable->auth_token ])
Перейти в личный кабинет
@endcomponent
--}}
Желаем вам успешной работы!

{{--
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
 --}}
@endcomponent
