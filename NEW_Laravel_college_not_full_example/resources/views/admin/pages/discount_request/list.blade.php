@extends("admin.admin_app")

@section("content")
    <div id="main">
        <div class="page-header">
            <h2>Список заявок на скидку</h2>
        </div>
        @if(Session::has('flash_message'))
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                {{ Session::get('flash_message') }}
            </div>
        @endif

        {{--  Tabs      --}}
        <ul class="nav nav-tabs nav-justified">
            @foreach($categories as $index => $category)
                <li>
                    <a class="{{$index == 0 ? "active show" : ""}}" href="#genTab{{ $category->id }}" data-toggle="tab">{{ $category->name }}</a>
                </li>
            @endforeach

            <li>
                <a href="#entTab" data-toggle="tab">ENT</a>
            </li>
            <li>
                <a href="#gpaTab" data-toggle="tab">GPA</a>
            </li>
            <li>
                <a href="#customTab" data-toggle="tab">Ручная</a>
            </li>
        </ul>

        <div class="panel panel-default panel-shadow">
            <div class="panel-body" >
                <div class="tab-content">
                    @foreach($categories as $index => $category)
                        <div class="tab-pane {{$index == 0 ? 'active' : ''}}" id="genTab{{ $category->id }}">
                            <table id="data-table-ajax{{ $category->id }}" class="table table-striped table-hover dt-responsive" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>ФИО</th>
                                        <th>Наименование</th>
                                        <th>Статус</th>
                                        <th>Дата подачи</th>
                                        <th class="text-center width-100">Действие</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    @endforeach

                    <div class="tab-pane" id="entTab">
                        <div class="row">
                            <blockquote class="col-sm-9">Список выданных скидок можете найти в закладке Учебные</blockquote>
                            <div class="col-sm-3">
                                <select class="form-control" id="entYear">
                                    <option>Выберите год</option>
                                    @for($i = now()->year - 5; $i <= now()->year + 1; $i++)
                                        <option
                                        @if( now()->year == $i )
                                            selected
                                        @endif
                                        value="{{$i}}">{{$i}}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12" style="padding: 15px;">
                            <table id="data-table-ajax-ent" class="table table-striped table-hover dt-responsive" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>ФИО</th>
                                    <th>ENT</th>
                                    <th>Направление</th>
                                    <th>Статус</th>
                                    <th class="text-center width-100">Действие</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane" id="gpaTab">
                        <blockquote>Список выданных скидок можете найти в закладке Учебные</blockquote>

                        <table id="data-table" class="table table-striped table-hover dt-responsive" cellspacing="0" width="100%">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>ФИО</th>
                                <th>GPA</th>
                                <th>Размер скидки</th>
                                {{--<th>Направление</th>
                                <th>Статус</th>--}}
                                <th class="text-center width-100">Действие</th>
                            </tr>
                            </thead>
                            <tbody>

                           @foreach($gpaNewList as $index => $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->fio }}</td>
                                    <td>{{ $item->value }}</td>
                                    <td>{{$item->discountSize}}</td>
                                    {{--<td>{{ $item->trendName }}</td>
                                    <td>{{ $item->status }}</td>--}}
                                    <td class="text-center">
                                        @if(\App\Services\Auth::user()->hasRight('discountrequests','edit'))
                                            <div class="btn-group">
                                                <a class="btn btn-default" href="{{ route('adminDiscountRequestsAdd', [
                                                'user_id' => $item->user_id,
                                                'discount_type_id' => 3
                                                ]) }}"><i class="md md-add"></i></a>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>

                    <div class="tab-pane" id="customTab">
                        <table id="data-table-ajax-custom" class="table table-striped table-hover dt-responsive" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>ФИО</th>
                            <th>Наименование</th>
                            <th>Статус</th>
                            <th class="text-center width-100">Действие</th>
                        </tr>
                        </thead>
                        </table>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        const HAS_EDIT_RIGHT = {{ var_export(\App\Services\Auth::user()->hasRight('discountrequests', 'edit'), true) }};

        $(document).ready(function () {
            @foreach($categories as $index => $category)
                let dataTable{{$category->id}} = $('#data-table-ajax{{$category->id}}').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "columns": [
                        {"orderable": true},
                        {"orderable": true},
                        {"orderable": true},
                        {"orderable": true},
                        {"orderable": true},
                        {"orderable": false}
                    ],
                    "ajax": {
                        url: "{{ route('adminDiscountRequestsByCategoryAjax', ['category_id' => $category->id]) }}",
                        type: "post",
                        error: function () {  // error handling
                            $(".employee-grid-error").html("");
                            $("#employee-grid").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                            $("#employee-grid_processing").css("display", "none");
                        },
                        "dataSrc": function (json) {
                            for (let i = 0, ien = json.data.length ; i < ien ; i++) {
                                json.data[i][5] = '<div class="btn-group">';
                                if (HAS_EDIT_RIGHT) {
                                    json.data[i][5] += '<a class="btn btn-default" href="/discountrequests/' + json.data[i][0] + '/category" title="Редактировать"><i class="md md-edit"></i></a>';
                                }

                                json.data[i][5] += '</div>';
                                json.data[i][1] = '<a href="/students/' + json.data[i][6] + '">' + json.data[i][1] + '</a>';
                            }

                            return json.data;
                        }
                    }
                });
            @endforeach

            let dataTableEnt = $('#data-table-ajax-ent').DataTable({
                "processing": true,
                "serverSide": true,
                "columns": [
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": false},
                    {"orderable": false}
                ],
                "ajax": {
                    url: "{{ route('adminDiscountRequestsEntAjax') }}",
                    type: "post",
                    error: function () {  // error handling
                        $(".employee-grid-error").html("");
                        $("#employee-grid").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                        $("#employee-grid_processing").css("display", "none");
                    },
                    "dataSrc": function (json) {
                        for (let i = 0, ien = json.data.length ; i < ien ; i++) {
                            json.data[i][5] = '<div class="btn-group">';
                            if (HAS_EDIT_RIGHT) {
                                json.data[i][5] += '<a class="btn btn-default" href="/discountrequests/add/' + json.data[i][0] + '/2" title="Редактировать"><i class="md md-edit"></i></a>';
                            }

                            json.data[i][5] += '</div>';
                            
                        }

                        return json.data;
                    }
                }
            });

            $('#entYear').on("change", function() {
                dataTableEnt.column(0)
                    .search($(this).val(), false, false)
                    .draw();
            });

            let dataTableCustom = $('#data-table-ajax-custom').DataTable({
                "processing": true,
                "serverSide": true,
                "columns": [
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": true},
                    {"orderable": false}
                ],
                "ajax": {
                    url: "{{ route('adminDiscountRequestsCustomAjax') }}",
                    type: "post",
                    error: function () {  // error handling
                        $(".employee-grid-error").html("");
                        $("#employee-grid").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                        $("#employee-grid_processing").css("display", "none");
                    },
                    "dataSrc": function (json) {
                        for (let i = 0, ien = json.data.length ; i < ien ; i++) {
                            json.data[i][4] = '<div class="btn-group">';
                            if (HAS_EDIT_RIGHT) {
                                json.data[i][4] += '<a class="btn btn-default" href="/discountrequests/' + json.data[i][0] + '" title="Редактировать"><i class="md md-edit"></i></a>';
                            }

                            json.data[i][4] += '</div>';
                        }

                        return json.data;
                    }
                }
            });
        });
    </script>
@endsection
