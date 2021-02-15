@extends('layouts.app')

@section('title', __('Polls'))

@section('content')
    <div id="main">
        <div class="container-fluid">
            <div class="p-3 mb-2 bg-info">
                <h2 class="text-white no-margin">{{ $poll->{'title_' . app()->getLocale()} ?? $poll->title_ru }}</h2>
            </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">
                    {!! Form::open([
                                    'url' => [ route('student.poll.pass', ['poll_id' => $poll->id]) ],
                                    'class'=>'form-horizontal padding-15',
                                    'name'=>'poll_form',
                                    'id'=>'poll_form',
                                    'role'=>'form',
                                    'enctype' => 'multipart/form-data']
                    ) !!}
                        @foreach($poll->questions as $key => $question)
                            <div class="col-xs-12" v-show="question_number === {{ $key }}">
                                <div class="form-group">
                                    <label>{{ $question->{'text_' . app()->getLocale()} ?? $question->text_ru }}</label>

                                    @if(!$question->answers->isEmpty())
                                        <select
                                                name="answers[{{ $question->id }}][]"
                                                class="form-control"
                                                {{ $question->is_multiple? 'multiple' : '' }}
                                        >
                                            @foreach($question->answers as $answer)
                                                <option value="{{ $answer->{'text_' . app()->getLocale()} ?? $answer->text_ru }}">
                                                    {{ $answer->{'text_' . app()->getLocale()} ?? $answer->text_ru }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @endif

                                    @if ($question->is_custom_answer)
                                        <br>

                                        <div class="custom-answer" data-question-id="{{ $question->id }}">
                                            <div class="input-group mb-3">
                                                <input type="text" name="answers[{{ $question->id }}][]" class="form-control">
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                @if(!$loop->last)
                                    <div class="form-group">
                                        <div class="col-sm-offset-3 col-sm-2">
                                            <button class="btn btn-primary" type="button" @click="nextQuestion">@lang('Next')</button>
                                        </div>
                                    </div>
                                @else
                                    <div class="form-group">
                                        <div class="col-sm-offset-3 col-sm-2">
                                            <button class="btn btn-primary" type="submit">@lang('Submit')</button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        var main = new Vue({
            el: '#main',
            data: {
                answers: {},
                question_number: 0,
            },
            methods: {
                nextQuestion: function () {
                    this.question_number++;
                },
                newAnswer: function (event) {
                    console.log(event.target.value);
                }
            }
        });
    </script>
@endsection
