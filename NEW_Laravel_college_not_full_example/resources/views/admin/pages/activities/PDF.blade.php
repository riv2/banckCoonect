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
            <h1>Отчет об активности</h1>
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
                    @foreach($profile['activities'] as $lecture)
                        <tr>
                            <td class="text-center" style="width: 25%">{{ $lecture['page'] }}  </td>
                            <td class="text-center" style="width: 25%">{{ $lecture['time'] }}</td>
                            <td class="text-center" style="width: 50%">{{ $lecture['url'] }}</td>
                        </tr>
                    @endforeach

                    @if(count($profile['activities']) == 0)
                        <tr>
                            <td colspan="3" class="text-center" >Нет посещений</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        @endforeach

    </body>
</html>
