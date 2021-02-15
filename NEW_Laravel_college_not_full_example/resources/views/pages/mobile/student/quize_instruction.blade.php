<div class="col-md-12">
    <h2 style="margin-bottom: 20px">{{ __('Attention!') }}</h2>
    <ul>
        <li>{{ __('Take the phone offline, make sure that the phone has enough power and there is an Internet connection. If disconnected, the test is considered failed.') }}</li>
        <li>{{ __('Each question can have from 1 to 4 correct answers.') }}</li>
        <li>{{ __('You can skip the question by clicking the "Next" button.') }}</li>
        <li>{{ __('You can return to the missed questions by clicking the “Missed” button.') }}</li>
        <li>{{ __('The total score is summed from the number of correct answers.') }}</li>
        <li>{{ __('Please be advised that the process of passing the test is being recorded, in case of unfair passing the exam, please follow the rules.') }}</li>
        @if($hasAudio)
            <li>
                {{ __('Check sound please:') }}
                <audio style="width: 100%; margin-top: 10px;"
                       height="32"
                       controls="controls">
                    <source src="/audio/audio_test.mp3" type="audio/mpeg">
                    Your browser does not support the audio element.
                </audio>
            </li>
        @endif
    </ul>
    <p>{{ __('We wish you a successful exam!') }}</p>
</div>
<div class="col-md-12 text-center" style="margin-top: 20px; margin-bottom: 20px">
    <a class="btn btn-info btn-lg" v-bind:disabled="!loaded" v-on:click="instructionAccept = true">{{ __('Go to testing') }}</a>
</div>