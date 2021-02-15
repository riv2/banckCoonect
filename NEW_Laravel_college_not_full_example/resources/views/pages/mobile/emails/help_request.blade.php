<html>
<head>
    <style>
        p {
            font-size: 16px;
            color: #000;
        }
    </style>
</head>
<body>
<p>
    Пользователь просит обратной связи по телефону. <a href="{{ route('adminHelpInfo', ['id' => $helpRequest->id]) }}">Подробнее</a>
</p>
</body>
</html>