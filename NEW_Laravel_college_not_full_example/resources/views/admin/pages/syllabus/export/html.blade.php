<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
    </style>
</head>
<body>

<div style="text-align: center">
    <h1>{{$discipline->name}}</h1>
</div>

@foreach($themeList as $theme)
    <!-- mb_convert_encoding($theme->theme_name, 'HTML-ENTITIES', 'UTF-8') -->
    <h2>Тема: {{ $theme->theme_name }}</h2>
    @foreach($theme->quizeQuestions as $question)
        <div style="margin-bottom: 15px">
            <div style="margin-bottom: 5px">
                <span style="font-weight: bold">Вопрос:</span>&nbsp;
                {{ strip_tags( html_entity_decode($question->question)) }}
            </div>
            <span style="font-weight: bold">Ответы:</span>
            <table border="1">
                <tbody>
                @foreach($question->answers as $answer)
                    <tr>
                        <td>{{ strip_tags( html_entity_decode($answer->answer)) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <hr>
    @endforeach
@endforeach

</body>
</html>