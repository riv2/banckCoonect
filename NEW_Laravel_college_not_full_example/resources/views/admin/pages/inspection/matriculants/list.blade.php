@extends("admin.admin_app")

@section("content")
    <div id="main">
        <div class="page-header">
            <h2>Приёмка</h2>
        </div>
        @if(Session::has('flash_message'))
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                {{ Session::get('flash_message') }}
            </div>
        @endif

        <a class="btn btn-primary pull-right" href="{{ str_replace('admin.','',route('studentLogin',['receipt'=>1,'register_fio'=>$sAuthUser])) }}" target="_blank">Зарегистрировать нового абитуриенты</a>
        <div class="clearfix"></div>
        <br>

        <div class="panel panel-default panel-shadow">
            <div class="panel-body">
                <div class="col-md-12 no-padding">
                    <table id="data-table-ajax" class="table table-striped table-hover dt-responsive" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Оплата</th>
                            <th>ФИО</th>
                            <th>Номер</th>
                            <th>Специальность</th>
                            <th>Год поступления</th>
                            <th>Статус</th>
                            <th>Баз. обр.</th>
                            <th>Форма обучения</th>
                            <th>Степень</th>
                            <th>Язык обучения</th>
                            <th>Категория</th>
                            <th>Загруженные документы</th>
                            <th class="text-center width-100" style="padding-right:8px;"><input type="checkbox" onchange="select_all_toggle(this);"></th>
                        </tr>
                        </thead>
                    </table>
                </div>
                <div class="col-md-12" id="buttons">
                    <button class="btn btn-primary" onclick="showNotificationModal()">Отправить уведомление</button>
                    <button class="btn btn-danger" onclick="deleteStudents()">Удалить выбранных</button>
                    <button class="btn btn-success" onclick="moveToOR(this);">В кабинет ОР</button>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>

    <div class="modal" tabindex="-1" role="dialog" aria-labelledby="" id="notificationModal">
        <div class="modal-dialog modal-lg " style="min-width:950px;" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" onclick="hideNotificationModal()"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Отправка уведомления</h4>
                </div>
                <div class="modal-body col-sm-12" style="overflow-y: auto;max-height: 75vh;">
                    <div class="col-md-12">
                        <textarea id="notificationText"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" onclick="sendNotification()">Отправить</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script type="text/javascript">
        let category = 'all';
        let dataTable;
        let isDeleted = 0;

        $(document).ready(function() {
            $('#notificationText').summernote({
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                ],
                height: {
                    type: String,
                    default: '150'
                }
            });
            $('#notificationText').on('summernote.change', function(e) {
                $('#notificationText').val($('#notificationText').summernote().code());
            });

            $('#notificationText').on('summernote.blur', function(e) {
                $('#notificationText').val($('#notificationText').summernote().code());
            });

            dataTable = $('#data-table-ajax').DataTable( {
                "processing": true,
                "serverSide": true,
                "columns": [
                    {"orderable": true},
                    {
                        "orderable": false,
                        "sClass": "display-none"
                    },
                    {"orderable": true},
                    {"orderable": false},
                    {"orderable": true},
                    {"orderable": false},
                    {"orderable": false},
                    {"orderable": false},
                    {"orderable": false},
                    {"orderable": false},
                    {"orderable": false},
                    {"orderable": false},
                    {"orderable": false},
                    {"orderable": false}
                ],
                "ajax":{
                    url :"{{ route('adminInspectionMatriculantsListAjax', '') }}/" + category , // json datasource
                    type: "post",  // method  , by default get
                    error: function(){  // error handling
                        $(".employee-grid-error").html("");
                        $("#employee-grid").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                        $("#employee-grid_processing").css("display","none");
                    },
                    "data": function ( d ) {
                        d.deleted = isDeleted;
                    }
                },
                "dom": "<<'col-sm-4'l><'btn-toggle-container text-center col-sm-4'><'col-sm-4'f>>rtip"
            } );

            $("#data-table-ajax thead th").each( function ( i ) {
                //id
                if (i == 0) {
                    $('<input type="text" class="form-control" />')
                        .appendTo($(this))
                        .on('change', function () {
                            var val = $(this).val();

                            dataTable.column(i)
                                .search(val)
                                .draw();
                        });
                }
                //fio
                if (i == 2 ) {
                    $('<input type="text" class="form-control" />')
                        .appendTo($(this))
                        .on('change', function () {
                            var val = $(this).val();

                            dataTable.column(i)
                                .search(val)
                                .draw();
                        });
                }
                //email
                if (i == 3 ) {
                    $('<input type="text" class="form-control" />')
                        .appendTo($(this))
                        .on('change', function () {
                            var val = $(this).val();

                            dataTable.column(i)
                                .search(val)
                                .draw();
                        });
                }
                //speciality
                if (i == 4 ) {
                    $('<input type="text" class="form-control" />')
                        .appendTo($(this))
                        .on('change', function () {
                            var val = $(this).val();

                            dataTable.column(i)
                                .search(val)
                                .draw();
                        });
                }
                //year
                if (i == 5 ) {
                    $(`<select  class="form-control" >
                            <option value="">По умолчанию</option>
                            @foreach($years as $year)
                                <option value={{$year['year']}}>{{$year['year']}}</option>
                            @endforeach
                    </select>`)
                        .appendTo($(this))
                        .on('change', function () {
                            var val = $(this).val();
                            dataTable.column(i)
                                .search(val)
                                .draw();
                        });
                }
                //status
                if (i == 6 ) {
                    $(`<select  class="form-control" >
                            <option value="">По умолчанию</option>
                            @foreach($status as $stat)
                                <option value={{$stat}}>@lang($stat)</option>
                            @endforeach
                    </select>`)
                        .appendTo($(this))
                        .on('change', function () {
                            var val = $(this).val();
                            dataTable.column(i)
                                .search(val)
                                .draw();
                        });
                }
                //base_education
                if (i == 7 ) {
                    $(`<select  class="form-control" >
                            <option value="">По умолчанию</option>
                            @foreach($base_education as $base)
                                <option value={{$base['key']}}>{{$base['value']}}</option>
                            @endforeach
                    </select>`)
                        .appendTo($(this))
                        .on('change', function () {
                            var val = $(this).val();
                            dataTable.column(i)
                                .search(val)
                                .draw();
                        });
                }
                //form study
                if (i == 8) {
                    $(`<select class="form-control" >
                            <option value="">По умолчанию</option>
                            @foreach($study_forms as $study_form)
                                <option value={{$study_form}}>{{$study_form}}</option>
                            @endforeach
                    </select>`)
                        .appendTo($(this))
                        .on('change', function () {
                            var val = $(this).val();
                            dataTable.column(i)
                                .search(val)
                                .draw();
                        });
                }
                //degree
                if (i == 9) {
                    $(`<select class="form-control" >
                            <option value="">По умолчанию</option>
                           @foreach($degree as $deg)
                                <option value="@lang($deg)">@lang($deg)</option>
                            @endforeach
                    </select>`)
                        .appendTo($(this))
                        .on('change', function () {
                            var val = $(this).val();
                            dataTable.column(i)
                                .search(val)
                                .draw();
                        });
                }
                //lang
                if (i == 10) {
                    $(`<select class="form-control" >
                            <option value="">По умолчанию</option>
                            @foreach($langList as $key => $lang)
                                <option value={{$key}}>{{$lang}}</option>
                            @endforeach
                    </select>`)
                        .appendTo($(this))
                        .on('change', function () {
                            var val = $(this).val();
                            dataTable.column(i)
                                .search(val)
                                .draw();
                        });
                }
                //categories
                if (i == 11) {
                    $(`<select class="form-control" >
                            <option value="">По умолчанию</option>
                            @foreach($categories as $key => $category)
                                <option value={{$key}}>{{$category}}</option>
                            @endforeach
                    </select>`)
                        .appendTo($(this))
                        .on('change', function () {
                            var val = $(this).val();
                            dataTable.column(i)
                                .search(val)
                                .draw();
                        });
                }
            } );

            $('.btn-toggle-container').append(`
                <button id="users_toggle_btn" class="btn btn-default" onclick="usersToggle()">
                    Показать удаленных
                </button>`);
        });

        function showNotificationModal() {
            let userList = getSelectedUser();

            if (userList.length == 0) {
                alert('Необходимо выбрать абитуриентов');
                return;
            }
            buttonsOff();
            $('#notificationModal').addClass('show');
        }

        function hideNotificationModal() {
            buttonsOn();
            $('#notificationModal').removeClass('show');
        }


        function getSelectedUser() {
            let favorite = [];

            $.each($("input[name='selectUserList']:checked"), function () {
                favorite.push($(this).val());
            });

            return favorite;
        }

        function sendNotification() {
            let data = {
                users: getSelectedUser(),
                text: $('#notificationText').val()
            };

            if (data.users.length == 0) {
                alert('Необходимо выбрать студентов');
                return;
            }

            if (data.text == '') {
                alert('Необходимо ввести сообщение');
                return;
            }

            $.ajax({
                url: '{{ route('adminSectionSendNotification') }}',
                type: "POST",
                data: data,
                success: function (data) {
                    alert('Уведомление отправлено.');
                    hideNotificationModal();
                },
                dataType: 'json'
            });
        }

        function deleteStudents() {
            let data = {
                user_list: getSelectedUser(),
            };

            if (data.user_list.length == 0) {
                alert('Необходимо выбрать студентов');
                return;
            }

            if (!confirm('Удалить выбранных студентов?')) {
                return false;
            }

            buttonsOff();

            $.ajax({
                url: '{{ route('adminStudentDeleteAjax') }}',
                type: "POST",
                data: data,
                success: function (data) {
                    $('#data-table-ajax').DataTable().ajax.reload();
                    buttonsOn();
                },
                dataType: 'json'
            });
        }

        function setCategoryFilter(categoryName) {
            category = categoryName;
            $('.category_filter').removeClass('btn-primary');
            $('.category_filter').addClass('btn-default');
            $('#category_filter_' + categoryName).addClass('btn-primary');
            $('#category_filter_' + categoryName).removeClass('btn-default');

            $('#data-table-ajax').DataTable().ajax.url("{{ route('adminInspectionMatriculantsListAjax', '') }}/" + category);
            $('#data-table-ajax').DataTable().ajax.reload();
        }

        function select_all_toggle(obj) {
            $("input[name='selectUserList']").prop('checked', $(obj).prop('checked'))
        }

        function moveToOR() {
            let users = getSelectedUser();

            if (!users.length) {
                alert('Необходимо выбрать студентов');
                return;
            }

            if (confirm('Перевести ' + users.length + ' студентов в кабинет ОР?')) {
                buttonsOff();

                axios.post('{{ route('adminInspectionMoveToOR') }}', {
                    "users": users
                })
                .then(function(response) {
                    if (response.data.status) {
                        $('#data-table-ajax').DataTable().ajax.reload();
                    } else {
                        alert(response.data.error);
                    }

                    buttonsOn();
                });
            }
        }

        function buttonsOff() {
            $('div#buttons button').prop('disabled', true);
        }

        function buttonsOn() {
            $('div#buttons button').prop('disabled', false);
        }

        function usersToggle() {

            let btn = $('#users_toggle_btn')
            if(!isDeleted){
                btn.html('Скрыть удаленных')
                isDeleted = 1
                dataTable.ajax.reload();
            } else {
                btn.html('Показать удаленных')
                isDeleted = 0
                dataTable.ajax.reload();
            }
        }

    </script>
@endsection