@extends('layouts.app')

@section('content')

    <section class="content">
        <div class="container-fluid">

            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin"> {{__('GID')}} </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">

                            @if( (\auth()->user()) && \auth()->user()->hasRole('guest') )

                                <h3> {{ __('Welcome to MIRAS!') }} </h3>

                                <p> {{ __('You are using the virtual guide MIRAS.APP.') }} </p>
                                <p> {{ __('To access the territory, use the QR code.') }} </p>
                                <p> {{ __('Purchase goods and services in all Miras.education buildings.') }} </p>
                                <p> {{ __('At your service packet Wi-Fi, services of a gym, pool and cafe.') }} </p>
                                <p> {{ __('You can pay for services by card of any bank or in cash at the cash desk.') }} </p>
                                <p class="text-bold"> {{ __('Answers to the most asked questions HERE:') }} </p>
                                <ul>
                                    <li> <p> {{ __('Should I use the MIRAS.APP app for all purchases?') }} <br>
                                            <small class="text-muted"> {{ __('Yes. Goods and services can only be purchased through MIRAS.APP') }} </small> </p>
                                    </li>
                                    <li> <p> {{ __('If I have not used the entire Internet, will it remain in my account the next day?') }} <br>
                                            <small class="text-muted"> {{ __('Yes, if you do not change the phone number associated with the application.') }} </small> </p>
                                    </li>
                                    <li> <p> {{ __('How is my data stored in MIRAS.APP?') }} <br>
                                            <small class="text-muted"> {{ __('Your account is tied to a phone number.') }} </small> </p>
                                    </li>
                                    <li> <p> {{ __('I am not a Miras.education student, can I purchase goods and services?') }} <br>
                                            <small class="text-muted"> {{ __('Yes. We are glad to all residents and guests of Shymkent.') }} </small> </p>
                                    </li>
                                </ul>
                                <br>
                                <p> {{ __('If you have questions, please contact us.') }} </p>

                            @endif

                            @if( (\auth()->user()) && \auth()->user()->hasRole('client') )

                                <p> {{ __('You are in the Training section of the Miras.App website.') }} </p>
                                <p> {{ __('We remind you that according to the User Agreement, all actions performed under your username are legally binding. Do not share your username and password with anyone.') }} </p>
                                <p> {{ __('Please note the attendance of classes in the system using a QR code.') }} </p>
                                <p> {{ __('The Training section includes subsections:') }} </p>
                                <ol>
                                    <li> {{ __('Profile. It contains personal data and copies of documents.') }} </li>
                                    <li> {{ __('Cabinet. The information on the balance, data on the discounts provided, payment methods are presented.') }} </li>
                                    <li> {{ __('Training. The educational history, the studied educational program is displayed. Granted access to syllabuses and exams. In this section, recording on disciplines is carried out and knowledge is evaluated.') }} </li>
                                    <li> {{ __('Communication Track personalized notifications, university announcements, and ask questions.') }} </li>
                                </ol>

                                <p> {{ __('More detailed instructions:') }} </p>
                                <ul>
                                    <li> {{ __('Record') }} </li>
                                    <li> {{ __('Number change') }} </li>
                                    <li> {{ __('Testing') }} </li>
                                    <li> {{ __('Reference') }} </li>
                                </ul>

                                @if(app()->getLocale() == 'ru')
                                    <video class="col-6 margin-t20 margin-b20" loop autoplay controls muted="muted" preload="true">
                                        <source src="https://assets.object.pscloud.io/video/miras_guide.mov" type="video/mov"/>
                                        <source src="https://assets.object.pscloud.io/video/miras_guide.webm" type="video/webm" />
                                        <source src="https://assets.object.pscloud.io/video/miras_guide.mp4" type="video/mp4" />
                                        Your browser does not support the video tag.
                                    </video>
                                @elseif(app()->getLocale() == 'kz')
                                    <video class="col-6 margin-t20 margin-b20" loop autoplay controls muted="muted" preload="true">
                                        <source src="https://assets.object.pscloud.io/video/miras_guide_kz.mov" type="video/mov"/>
                                        <source src="https://assets.object.pscloud.io/video/miras_guide_kz.webm" type="video/webm" />
                                        <source src="https://assets.object.pscloud.io/video/miras_guide_kz.mp4" type="video/mp4" />
                                        Your browser does not support the video tag.
                                    </video>
                                @endif

                            @endif

                        </div>
                        <div class="col-md-2"></div>
                    </div>

                </div>
            </div>

        </div>
    </section>

@endsection
