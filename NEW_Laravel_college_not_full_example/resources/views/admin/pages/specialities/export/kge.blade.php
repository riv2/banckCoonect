<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
    </style>
</head>
<body>

<div style="text-align: center">
    <h1>{{$speciality->name}}</h1>
</div>

@foreach($quizeQuestions as $question)
    <div style="margin-bottom: 15px">
        <div style="margin-bottom: 5px">
            <span style="font-weight: bold">Вопрос:</span>&nbsp;
            {{ strip_tags(mb_convert_encoding($question->question, 'HTML-ENTITIES', 'UTF-8')) }}
        </div>
        <span style="font-weight: bold">Ответы:</span>
        <table border="1">
            <tbody>
            @foreach($question->answers as $answer)
                <tr>
                    <td>@if($answer->correct) + @endif</td>
                    <td>{{ strip_tags(mb_convert_encoding($answer->answer, 'HTML-ENTITIES', 'UTF-8')) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <hr>
@endforeach

</body>
</html>