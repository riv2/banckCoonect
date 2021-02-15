@extends("admin.admin_app")

@section('title', 'Кабинет ОР')

@section("content")
<div id="or_cabinet">

    <div id="main">
        <div class="page-header">
            <h2>Кабинет ОР</h2>
        </div>
        @if(Session::has('flash_message'))
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                {{ Session::get('flash_message') }}
            </div>
        @endif

        <div id="alert-danger" class="alert alert-danger hide">
            <div id="alert-danger-message"></div>
        </div>

        <div id="alert-success" class="alert alert-success hide">
            <div id="alert-success-message"></div>
        </div>

        <div class="panel panel-default panel-shadow">
            <div class="panel-body">
                <ul class="nav nav-tabs nav-justified">
                    <li class="active">
                        <a  href="#list" data-toggle="tab">{{__("Students list")}}</a>
                    </li>
                    <li>
                        <a href="#audit" data-toggle="tab">{{__("Audit finance operations")}}</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="list">
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
                            <button class="btn btn-info" onclick="showOrderModal()">Добавить в приказ</button>
                            <button class="btn btn-danger" onclick="deleteStudents()">Удалить выбранных</button>
                            @if(\App\Services\Auth::user()->hasRight('', 'add_aditional_service_to_user'))
                            <button @click="serviceShowModal" class="btn btn-primary">{{ __('To create additional service') }}</button>
                            @endif
                            <button class="btn btn-warning" onclick="moveToInspection();">В приёмку</button>
                        </div>
                    </div>
                    <div class="tab-pane" id="audit">
                        <div class="col-md-12 no-padding">
                            <table id="data-table-audit" class="table table-striped table-hover dt-responsive" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>ФИО</th>
                                    <th>Провел</th>
                                    <th>Услуга</th>
                                    <th>Код</th>
                                    <th>Стоимость</th>
                                    <th>Статус</th>
                                    <th>Дата</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
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
                    <textarea id="notificationText"></textarea>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" onclick="sendNotification()">Отправить</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" tabindex="-1" role="dialog" aria-labelledby="" id="orderModal">
        <div class="modal-dialog modal-lg " style="min-width:950px;" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" onclick="hideOrderModal()"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Добавление в приказ</h4>
                </div>
                <div class="modal-body col-sm-12" style="overflow-y: auto;max-height: 75vh;">
                    <select id="order_id" class="form-control attach_to_order_elemnent">
                        @foreach($orderList as $order)
                            <option value="{{ $order->id }}">{{ $order->orderName->name }} ({{ $order->number }})</option>
                        @endforeach
                    </select>
                    <div class="alert alert-success order_attach_element_success hide">
                        Студенты добавлены в приказ.
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary attach_to_order_elemnent" onclick="attachToOrder()">Добавить студентов в приказ</button>
                    <div class="order_attach_element_success hide">
                        <a class="btn btn-primary" id="open_order_button" onclick="redirectToOrder()">Открыть приказ</a>
                        <a class="btn btn-link" onclick="hideOrderModal()">Закрыть окно</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div :class="{ show: serviceShowedModal }" id="serviceModal" class="modal" tabindex="-1" role="dialog" aria-labelledby="">
        <div class="modal-dialog modal-lg " style="min-width:950px;" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" @click="serviceToggleModal"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">{{ __('To create additional service') }}</h4>
                </div>
                <div class="modal-body col-md-12">

                    <div v-if="serviceErrorMessage" :class="{ 'alert-danger': serviceIsError, 'alert-success': !serviceIsError }" class="alert">
                        @{{ serviceErrorMessage }}
                    </div>

                    <br>

                    <div class="form-group">

                        <h4> {{ __('Selected students') }} </h4>

                        <div style="max-height:200px;overflow-y:scroll;">
                        <table class="table">
                            <tr>
                                <th>id</th>
                                <th>{{ __('ITN') }}</th>
                                <th>{{ __('Full name') }}</th>
                            </tr>
                            <template v-if="serviceCheckedUsersData && (serviceCheckedUsersData.length > 0)">
                            <tr v-for="(user,index) in serviceCheckedUsersData"
                                :class="{ danger : (serviceIsNotApprove && user.notApprove), 'success': !user.notApprove }"
                            >
                                <td> @{{ user.id }} </td>
                                <td> @{{ user.iin }} </td>
                                <td> @{{ user.fio }} </td>
                            </tr>
                            </template>
                        </table>
                        </div>

                    </div>

                    <br>

                    <div class="form-group">
                        <label for="serviceSelected" class="col-md-2 control-label">{{__('Selected service')}}</label>
                        <div class="col-md-10">
                            <select v-model="serviceSelected" id="serviceSelected" class="form-control">
                                @foreach($oFinanceNomenclature as $itemFN)
                                    <option value="{{ $itemFN->id }}">{{ $itemFN->code }} ({{ $itemFN->cost }} {{ __('tenge') }}) : {{ $itemFN->$locale }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <br>
                    <br>

                    <div class="form-group">
                        <label class="col-md-2 control-label">{{__('Count')}}</label>
                        <div class="col-md-10">
                            <input v-model="serviceCount" class="form-control" type="number" value="" />
                        </div>
                    </div>

                    <br>
                    <br>

                </div>
                <div class="modal-footer">
                    <button @click="serviceAttachService" :disabled="serviceSendRequest" class="btn btn-primary" type="button">{{ __('Attach service') }}</button>
                    <button @click="serviceToggleModal" class="btn btn-link" type="button">{{ __('Cancel') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script type="text/javascript">

        var category = 'all';
        let  isDeleted = 0;
        let dataTable;

        var vueApp = new Vue({
            el: '#or_cabinet',
            data: {
                serviceShowedModal: false,
                serviceCheckedUsers: [],
                serviceCheckedUsersData: [],
                serviceSelected: false,
                serviceIsNotApprove: false,
                serviceIsError: false,
                serviceErrorMessage: false,
                serviceCount: 1,
                serviceSendRequest: false
            },
            methods: {
                serviceToggleModal: function () {
                    this.serviceShowedModal = !this.serviceShowedModal;
                },
                serviceShowModal: function () {
                    this.serviceIsError = false;
                    this.serviceErrorMessage = false;
                    this.serviceIsNotApprove = false;
                    this.serviceCheckedUsers = getSelectedUser();
                    var self = this;

                    // get user data
                    axios.post('{{ route('adminMatriculantAjaxGetUserData') }}', {
                        "_token": "{{ csrf_token() }}",
                        "ids": self.serviceCheckedUsers
                    })
                        .then(function (response) {
                            if (response.data.status) {

                            } else {

                                self.serviceIsError = true;
                            }
                            if (response.data.data) {
                                self.serviceCheckedUsersData = response.data.data;
                            }
                            if (response.data.message) {
                                self.serviceErrorMessage = response.data.message;
                            }
                            if (response.data.data == null){
                                self.serviceCheckedUsersData = []
                            }
                        });

                    this.serviceToggleModal();
                },
                serviceAttachService: function () {

                    this.serviceSendRequest = true;
                    this.serviceIsError = false;
                    this.serviceErrorMessage = false;
                    this.serviceIsNotApprove = false;

                    var self = this;
                    axios.post('{{ route('adminMatriculantAjaxAttachService') }}', {
                        "_token": "{{ csrf_token() }}",
                        "service": self.serviceSelected,
                        "ids": self.serviceCheckedUsers,
                        "count": self.serviceCount
                    })
                        .then(function (response) {
                            if (response.data.status) {

                            } else {
                                self.serviceIsError = true;
                            }

                            if (response.data.isNotApprove) {
                                self.serviceIsNotApprove = response.data.isNotApprove;
                            }

                            if (response.data.data) {
                                self.serviceCheckedUsersData = response.data.data;
                            }

                            if (response.data.message) {
                                self.serviceErrorMessage = response.data.message;
                            }

                            self.serviceSendRequest = false;
                        });
                }
            },
            created: function () {
                this.serviceShowedModal = false;
                this.serviceCheckedUsers = [];
            }
        });

        $(document).ready(function () {
            $('#notificationText').summernote({
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                ],
                height: {
                    type: String,
                    default: '150'
                }
            });
            $('#notificationText').on('summernote.change', function (e) {
                $('#notificationText').val($('#notificationText').summernote().code());
            });

            $('#notificationText').on('summernote.blur', function (e) {
                $('#notificationText').val($('#notificationText').summernote().code());
            });

            dataTable = $('#data-table-ajax').DataTable({
                "processing": true,
                "serverSide": true,
                "columns": [
                    {"orderable": true},
                    {"orderable": false},
                    {"orderable": false},
                    {"orderable": false},
                    {"orderable": false},
                    {"orderable": true},
                    {"orderable": false},
                    {"orderable": false},
                    {"orderable": false},
                    {"orderable": false},
                    {"orderable": false},
                    {"orderable": false},
                    {"orderable": false},
                    {"orderable": false}
                ],
                "ajax": {
                    url: "{{ route('adminMatriculantsListAjax', '') }}/" + category, // json datasource
                    type: "post",  // method  , by default get
                    error: function () {  // error handling
                        $(".employee-grid-error").html("");
                        $("#employee-grid").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                        $("#employee-grid_processing").css("display", "none");
                    },
                    "data": function ( d ) {
                        d.deleted = isDeleted;
                    }
                },
                "dom": "<<'col-sm-4'l><'btn-toggle-container text-center col-sm-4'><'col-sm-4'f>>rtip"
            });

            $("#data-table-ajax thead th").each(function (i) {
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
                    $(`<select class="form-control">
                            <option value="">Все</option>
                            @foreach($study_forms as $key => $study_form)
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
            });


            $('.btn-toggle-container').append(`
                <button id="users_toggle_btn" class="btn btn-default" onclick="usersToggle()">
                    Показать удаленных
                </button>`);

            let dataTableAudit = $('#data-table-audit').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: "{{ route('adminMatriculantAjaxAuditList', '') }}",
                    data: {
                        "_token": "{{ csrf_token() }}",
                    },
                    type: "post",
                    error: function () {
                        $(".employee-grid-error").html("");
                        $("#employee-grid").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                        $("#employee-grid_processing").css("display", "none");

                    }
                }
            });

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
            $('#notificationModal').removeClass('show');
            buttonsOn();
        }

        function showOrderModal() {
            let userList = getSelectedUser();

            if (userList.length == 0) {
                alert('Необходимо выбрать абитуриентов');
                return;
            }

            buttonsOff();
            $('#orderModal').addClass('show');
        }

        function hideOrderModal() {
            $('#orderModal').removeClass('show');
            $('.attach_to_order_elemnent').removeClass('hide');
            $('.order_attach_element_success').addClass('hide');
            buttonsOn();
        }

        function getSelectedUser() {
            let favorite = [];

            $.each($("input[name='selectUserList']:checked"), function () {
                favorite.push($(this).val());
            });

            return favorite;
        }

        function sendNotification() {

            $('alert-success').addClass('hide');
            $('alert-danger').addClass('hide');

            var data = {
                users: getSelectedUser(),
                text: $('#notificationText').val()
            };

            if (data.users.length == 0) {
                alert('Необходимо выбрать абитуриентов');
                return;
            }

            if (data.text == '') {
                alert('Необходимо ввести сообщение');
                return;
            }

            $.ajax({
                url: '{{ route('adminSendNotification') }}',
                type: "POST",
                data: data,
                success: function (data) {
                    alert('Уведомление отправлено.');
                    hideNotificationModal();

                    if (data.status) {

                        $('#alert-success-message').html(data.message);
                        $('#alert-success').removeClass('hide');
                    } else {

                        $('#alert-danger-message').html(data.message);
                        $('#alert-danger').removeClass('hide');
                    }

                },
                dataType: 'json'
            });
        }

        function attachToOrder() {
            let data = {
                users: getSelectedUser(),
                order_id: $('#order_id').val()
            };

            if (data.users.length == 0) {
                alert('Необходимо выбрать абитуриентов');
                return;
            }

            if (!data.order_id) {
                alert('Необходимо выбрать приказ');
                return;
            }

            $.ajax({
                url: '{{ route('adminOrderAttachUsers') }}',
                type: "POST",
                data: data,
                success: function (data) {
                    $('#open_order_button').attr('href', '/orders/edit/' + $('#order_id').val());
                    $('.attach_to_order_elemnent').addClass('hide');
                    $('.order_attach_element_success').removeClass('hide');
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

            $('#data-table-ajax').DataTable().ajax.url("{{ route('adminMatriculantsListAjax', '') }}/" + category);
            $('#data-table-ajax').DataTable().ajax.reload();
        }

        function setEducationStatusFilter(statusName) {
            status = statusName;
            $('.category_filter').removeClass('btn-primary');
            $('.category_filter').addClass('btn-default');
            $('#category_filter_' + statusName).addClass('btn-primary');
            $('#category_filter_' + statusName).removeClass('btn-default');

            $('#data-table-ajax').DataTable().ajax.url("{{ route('adminMatriculantsListAjax', '') }}/" + status);
            $('#data-table-ajax').DataTable().ajax.reload();
        }

        function select_all_toggle(obj) {
            $("input[name='selectUserList']").prop('checked', $(obj).prop('checked'))
        }

        function buttonsOff() {
            $('div#buttons button').prop('disabled', true);
        }

        function buttonsOn() {
            $('div#buttons button').prop('disabled', false);
        }

        function moveToInspection() {
            let users = getSelectedUser();

            if (!users.length) {
                alert('Необходимо выбрать студентов');
                return;
            }

            if (confirm('Перевести ' + users.length + ' студентов в приёмку?')) {
                buttonsOff();

                axios.post('{{ route('adminMatriculantMoveToInspection') }}', {
                    "users": users
                })
                    .then(function (response) {
                        if (response.data.status) {
                            $('#data-table-ajax').DataTable().ajax.reload();
                        } else {
                            alert(response.data.error);
                        }

                        buttonsOn();
                    });
            }
        }

        function setBuying(obj) {
            let value = $(obj).prop('checked');
            let userId = $(obj).val();

            $(obj).prop('disabled', true);

            axios.post('{{route('adminMatriculantSetBuying')}}', {
                _token : '{{csrf_token()}}',
                user_id : userId,
                buying : value
            })
                .then(function (response) {
                    if (response.data.success) {

                    } else {
                        alert(response.data.error);
                    }

                    $(obj).prop('disabled', false);
                });
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