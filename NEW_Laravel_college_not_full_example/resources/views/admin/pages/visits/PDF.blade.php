<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }

        table {
            border-collapse: collapse;
        }

        table tbody td {
            border: 1px solid;
            padding: 5px;
        }

        thead:before, thead:after { display: none; }
        tbody:before, tbody:after { display: none; }
    </style>
</head>
<body>

<div style="text-align: center">
    <h1>Отчет о посещаемости</h1>
</div>

<div>
    @if($filters_info["month"] && $filters_info["year"])
    <span style="margin-right: 15px"> <span style="text-transform: capitalize"> {{ \App\Services\DateTime::getMonthNameByNum($filters_info["month"]) }} </span> {{ $filters_info["year"] }}</span>
    @endif

    @if($filters_info["group_name"])
    <span> Группа: {{ $filters_info["group_name"] }} </span>
    @endif
</div>

@foreach($profiles as $profile)

<table style="margin: 30px 0; width: 100%">
    <tr>
        <td colspan="3" class="text-center" style="font-weight: bold">{{ $profile['user_full_name'] }}</td>
    </tr>

    <tbody>
    @if($profile['lecture_list'])
        @foreach($profile['lecture_list'] as $lecture)
            <tr>
                <td class="text-center" style="width: 25%">{{ $lecture['visits_time'] }}  </td>
                <td class="text-center" style="width: 25%">{{ $lecture['discipline_name'] }}</td>
                <td class="text-center" style="width: 50%">{{ $lecture['teacher_fio'] }}</td>
            </tr>
        @endforeach
    @endif

    @if($profile['other_discipline_list'])
        @foreach($profile['other_discipline_list'] as $discipline)
            <tr>
                <td class="text-center" style="width: 25%">{{ $discipline['visits_time'] }}</td>
                <td class="text-center" style="width: 25%">{{ $discipline['discipline_name'] }}</td>
                <td class="text-center" style="width: 50%">{{ $discipline['subject_name'] }}</td>
            </tr>
        @endforeach
    @endif

    @if(!count($profile['lecture_list']) & !count($profile['other_discipline_list']))
        <tr>
            <td colspan="3" class="text-center" >Нет посещений</td>
        </tr>
    @endif
    </tbody>
</table>

@endforeach

</body>
</html>