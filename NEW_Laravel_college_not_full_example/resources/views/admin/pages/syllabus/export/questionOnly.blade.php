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
    <h2>Тема: {{ $theme->theme_name }}</h2>
    <ol>
    @foreach($theme->quizeQuestions as $question)
        <li>{{  strip_tags( html_entity_decode($question->question)) }}</li>
    @endforeach
    </ol>
@endforeach

</body>
</html>