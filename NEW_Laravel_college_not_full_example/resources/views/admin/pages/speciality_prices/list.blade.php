@extends("admin.admin_app")

@section("title", __('Specialities prices'))

@section("content")
    <div id="main">
        <div class="page-header">
            <h2>Цены для специальностей</h2>
        </div>

        @if(Session::has('flash_message'))
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                {{ Session::get('flash_message') }}
            </div>
        @endif

        <div class="panel panel-default panel-shadow">
            <div class="panel-body">
                <table id="main-table-ajax" class="table table-striped table-hover dt-responsive" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th class="text-center width-150">ID</th>
                        <th class="text-center width-150">Код</th>
                        <th>Название</th>
                        <th>Name with code</th>
                        <th class="text-center width-150">
                            Год
                            <select class="form-control" id="year_select">
                                <option value=""></option>
                                @foreach($years as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                        </th>
                        <th class="text-center width-150">Действие</th>

                        <th class="text-center width-100" style="padding-right:8px;"><input type="checkbox" onchange="select_all_toggle(this);"></th>
                    </tr>
                    </thead>
                </table>

                @if (\App\Services\Auth::user()->hasRight('specialities', 'edit'))
                    <div class="col-md-12" id="buttons">
                        <hr>
                        <form class="form-inline" id="priceForm">
                            <div class="form-group">
                                <label for="">Форма обучения</label>
                                <select class="form-control" id="selForm">
                                    @foreach($educationForms as $eKey => $educationForm)
                                        <option value="{{$eKey}}">{{$educationForm}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">Образование</label>
                                <select class="form-control" id="selStudy">
                                    @foreach($baseEducations as $bKey => $baseEducation)
                                        <option value="{{$bKey}}">{{$baseEducation}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">Тип цены</label>
                                <select class="form-control" id="selType">
                                    @foreach($priceTypes as $pKey => $priceType)
                                        <option value="{{$pKey}}">{{$priceType}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">Значение</label>
                                <input id="price" type="text" value="" class="form-control text-right" style="width: 90px;">
                            </div>
                            <a class="btn btn-primary" onclick="savePrice(); return false;" id="btnSet">Назначить</a>
                        </form>
                    </div>
                @endif
            </div>

            <div class="clearfix"></div>
        </div>
    </div>

    <div class="modal" tabindex="-1" role="dialog" aria-labelledby="" id="infoModal">
        <div class="modal-dialog modal-lg " style="min-width:950px;" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" onclick="hideInfoModal()"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Цены специальности "<span id="modalSpecialityName"></span>"</h4>
                </div>
                <div class="modal-body col-sm-12" style="overflow-y: auto;max-height: 75vh;" id="modalInfoBody">

                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" onclick="hideInfoModal()">Закрыть</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
		const HAS_EDIT_RIGHT = {{ var_export(\App\Services\Auth::user()->hasRight('specialities', 'edit'), true) }};

        function select_all_toggle(obj) {
            $("input[name='selectSpecialityList']").prop('checked', $(obj).prop('checked'));
        }

        function formOff() {
            $('#priceForm select, #priceForm input').prop('disabled', true);
            $('#btnSet').addClass('disabled');
        }

        function formOn() {
            $('#priceForm select, #priceForm input').prop('disabled', false);
            $('#btnSet').removeClass('disabled');
        }

        function savePrice() {
            let data = {
                specialities : getSelected(),
                education_form : $('#selForm').val(),
                base_education : $('#selStudy').val(),
                price_type : $('#selType').val(),
                price : $('#price').val()
            };

            formOff();

            axios.post('{{ route('adminAjaxSaveSpecialityPrice') }}', data)
                .then(function(response) {
                    if (response.data.status) {

                    } else {
                        alert(response.data.error);
                    }

                    formOn();
                });
        }

        function getSelected() {
            let favorite = [];

            $.each($("input[name='selectSpecialityList']:checked"), function() {
                favorite.push($(this).val());
            });

            return favorite;
        }

        function showInfoModal(specialityId, specialityName) {
            $('#modalSpecialityName').text(specialityName);
            $('#modalInfoBody').text('Загрузка...');
            $('#infoModal').addClass('show');

            axios.get('/speciality_prices/info/' + specialityId)
                .then(function(response) {
                    if (response.data) {
                        $('#modalInfoBody').html(response.data);

                        let tableModal = $('#modalInfoBody table#modal_info').DataTable({
                            "columns": [
                                {"orderable": false},
                                {"orderable": false},
                                {"orderable": false},
                                {"orderable": true}
                            ]
                        });

                        $('#edu_form_select').on('change', function () {
                            tableModal.column(0)
                                .search($(this).val(), false, false, false)
                                .draw();
                        });

                        $('#base_edu_select').on('change', function () {
                            tableModal.column(1)
                                .search($(this).val(), false, false, false)
                                .draw();
                        });

                        $('#price_type_select').on('change', function () {
                            tableModal.column(2)
                                .search($(this).val(), false, false, false)
                                .draw();
                        });
                    } else {
                        alert(response.data.error);
                    }
                });
        }

        function hideInfoModal() {
            $('#infoModal').removeClass('show');
        }

        $(document).ready(function () {
            let table = $('#main-table-ajax').DataTable({
                "processing": true,
                "serverSide": true,
                "columns": [
                    {"orderable": false},
                    {"orderable": false},
                    {"orderable": true},
                    {"orderable": false, className: 'hide'},
                    {"orderable": false},
                    {"orderable": false},
                    {"orderable": false}
                ],
                "ajax": {
                    url: "{{ route('adminSpecialityListAjax') }}",
                    type: "post",
                    error: function () {  // error handling
                        $(".employee-grid-error").html("");
                        $("#employee-grid").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                        $("#employee-grid_processing").css("display", "none");
                    },
                    "dataSrc": function (json) {
                        for (let i = 0, ien = json.data.length ; i < ien ; i++) {
                            json.data[i][5] = '<div class="btn-group">';
                            json.data[i][5] += '<a class="btn btn-default" onclick="showInfoModal(' + json.data[i][0] + ', \'' + json.data[i][2] + '\'); return false;" title="Просмотреть"><i class="md md-attach-money"></i></a>';

                            if (HAS_EDIT_RIGHT) {
                                json.data[i][5] += '<a class="btn btn-default" href="/specialities/edit/' + json.data[i][0] + '" title="Редактировать"><i class="md md-edit"></i></a>';
                            }

                            json.data[i][5] += '</div>';
                        }

                        return json.data;
                    }
                }
            });

            $('#year_select').on('change', function () {
                table.column(4)
                    .search($(this).val(), false, false)
                    .draw();
            });
        });
    </script>
@endsection
