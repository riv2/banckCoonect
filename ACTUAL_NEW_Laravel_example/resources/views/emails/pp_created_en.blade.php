@component('mail::message')
# Hi {{ $notifiable->name }}!

You’ve just made your own affiliate program, cool!
Take this data to log into your account as an advertiser:

Your login: <code>{{ $notifiable->email }}</code>

Your password: <code>{{ $generated_password }}</code>

@component('mail::button', ['url' => 'https://' . $notifiable->pp->tech_domain . '/login/onetime/' . $notifiable->auth_token ])
Log into your account
@endcomponent

And this is your affiliate program link: <a href="https://{{ $notifiable->pp->tech_domain }}">{{ $notifiable->pp->tech_domain }}</a>.

Send it to your partners so that they can sign up and start working with you right away.

To start, add your offer in the “Promo” section and complete the simple integration to set up data import and analytics. You can find the setup guide in the “Integration” section.

You can always ask us for help or advice:

E-mail: <a href="mailto:help@gocpa.cloud">help@gocpa.cloud</a>

Wishing you great success,
GoCPA Cloud Team

@endcomponent
