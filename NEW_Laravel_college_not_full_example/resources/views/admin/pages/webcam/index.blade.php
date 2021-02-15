@extends("admin.admin_app")

@section("content")

    <div id="main">
        <div class="page-header">
            <h2> Записи экзаменов</h2>
        </div>

        <div class="panel panel-default panel-shadow">
            <div class="panel-body">
                <table id="webcam-table" class="table table-striped table-hover dt-responsive" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th class="fio">Фио студента</th>
                            <th class="discipline">Название дисциплины</th>
                            <th class="date">Дата</th>
                            <th class="type">Тип теста</th>
                            <th class="study_form">Форма обучения</th>
                            <th>Видозапись</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="row">
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{csrf_token()}}'
            }
        });
        const dataTable = $('#webcam-table').DataTable( {
            processing: true,
            serverSide: true,
            dom: '<"top">r<"bottom"l><"clear">rtip',
            columns: [
                {orderable: false, data: 'user'},
                {orderable: false, data: 'discipline'},
                {orderable: false, data: 'created_at'},
                {orderable: false, data: 'type'},
                {orderable: false, data: 'study_form'},
                {orderable: false, data: 'actions'}
            ],
            ajax: {
                url: '{{route('admin.webcam.getList')}}',
                type: 'POST'
            }
        } );
        $('<input type="text" class="form-control" />')
            .appendTo($("#webcam-table thead th.fio"))
            .on('change', function () {
                var val = $(this).val();

                dataTable.column(0)
                    .search(val)
                    .draw();
            });
        $('<input type="text" class="form-control" />')
            .appendTo($("#webcam-table thead th.discipline"))
            .on('change', function () {
                var val = $(this).val();

                dataTable.column(1)
                    .search(val)
                    .draw();
            });
        $('<input type="date" class="form-control" />')
            .appendTo($("#webcam-table thead th.date"))
            .on('change', function () {
                var val = $(this).val();

                dataTable.column(2)
                    .search(val)
                    .draw();
            });
        $(`<select class="form-control">
                <option value="">Все</option>
                @foreach($testTypes as $key => $type)
                    <option value="{{$key}}">{{$type}}</option>
                @endforeach
            </select>`)
            .appendTo($("#webcam-table thead th.type"))
            .on('change', function () {
                var val = $(this).val();

                dataTable.column(3)
                    .search(val)
                    .draw();
            });
        $(`<select class="form-control">
                <option value="">Все</option>
                @foreach($educationsStudyForms as $key => $studyForm)
                    <option value="{{$key}}">{{$studyForm}}</option>
                @endforeach
            </select>`)
            .appendTo($("#webcam-table thead th.study_form"))
            .on('change', function () {
                var val = $(this).val();

                dataTable.column(4)
                    .search(val)
                    .draw();
            });
    </script>
@endsection
