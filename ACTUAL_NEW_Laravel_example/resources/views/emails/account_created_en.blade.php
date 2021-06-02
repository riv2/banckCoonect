@component('mail::message')
# Hi {{ $notifiable->name }}!

You have successfully signed up to affiliate program {{ $notifiable->pp->prod_domain ?? $notifiable->pp->tech_domain }}

Your login: <code>{{ $notifiable->email }}</code>

Your password: <code>{{ $generated_password }}</code>

@component('mail::button', ['url' => 'https://' . $notifiable->pp->prod_domain ?? $notifiable->pp->tech_domain . '/login/onetime/' . $notifiable->auth_token ])
Log into your account
@endcomponent

Good luck with your work!
@endcomponent
