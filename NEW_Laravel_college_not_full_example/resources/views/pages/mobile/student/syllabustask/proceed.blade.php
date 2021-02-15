
<div class="col-md-12">

    <h2 style="margin-bottom:20px">{{ __('Attention!') }}</h2>
    <ul>
        <li>{{ __('Answer the following questions to get a grade for the Assignment.') }}</li>
        <li>{{ __('Each question can have 1 to 4 correct answers.') }}</li>
        <li>{{ __('You can skip the question by clicking the "Next" button.') }}</li>
        <li>{{ __('You can return to the missed questions by clicking the Missed button.') }}</li>
    </ul>
    <p>{{ __('We wish you a successful exam!') }}</p>

</div>
<div class="col-md-12 text-center" style="margin-top: 20px; margin-bottom: 20px">
    <a class="btn btn-primary text-white" :disabled="!loaded" @click="startTest">{{ __('Go to testing') }}</a>
</div>
