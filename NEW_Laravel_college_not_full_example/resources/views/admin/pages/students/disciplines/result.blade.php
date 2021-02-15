@extends("admin.admin_app")

@section("content")
    <div id="main">
        <div class="page-header">
            <h2>Ответы студента</h2>
        </div>

        <a href="{{ URL::to('/students/' . $id) }}" class="btn btn-default-light btn-xs"><i class="md md-backspace"></i> Назад</a>
        <br><br>
        @foreach($questionList as $question)
        <div class="panel panel-default panel-shadow">
            <div class="panel-body">
                <div class="col-md-12">
                    <label>Вопрос:</label> <br>
                    <div class="col-md-12">{!! trim(strip_tags($question['question']->question, '<img><table><tbody><thead><tfoot><tr><th><td>')) !!}</div>
                </div>
                <div class="col-md-12" style="margin-top: 20px">
                    <label>Ответы:</label>
                </div>
                <div class="col-md-12">
                @foreach($question['question']->answers as $answer)
                    <div class="alert col-md-12
                        @if(in_array($answer->id, $question['answer_id']) && $answer->correct)
                            alert-success
                        @endif
                        @if(in_array($answer->id, $question['answer_id']) && !$answer->correct)
                            alert-danger
                        @endif
                        @if(!in_array($answer->id, $question['answer_id']) && $answer->correct)
                            alert-warning
                        @endif
                    ">
                    {!! trim(strip_tags($answer->answer, '<img><table><tbody><thead><tfoot><tr><th><td>')) !!}
                    </div>
                @endforeach
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endsection