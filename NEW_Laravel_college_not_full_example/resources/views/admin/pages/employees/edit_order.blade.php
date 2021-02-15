@extends("admin.admin_app")

@section("content")

    <div id="main">
        <div class="page-header">
            <h2> {{ isset($id)? 'Редактировать приказ' : 'Добавить приказ' }}</h2>
            <a href="{{ route('employees.orders.page') }}" class="btn btn-default-light btn-xs"><i class="md md-backspace"></i> Назад</a>
        </div>

        <div class="panel panel-default" id="main-panel">
            <div class="panel-body">
                {!! Form::open([
                    'class' => 'form-horizontal',
                    'url' => isset($id)? route('edit.order') : route('employees.create.order'),
                    'enctype' => 'multipart/form-data'
                ]) !!}
                    @isset($id)
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                    @endisset
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Наименование</label>
                        <div class="col-sm-5">
                            <select class="form-control" name="order_name_id" required @if(isset($order)) disabled @endif>
                                <option value="">- Выбрать из списка -</option>
                                @foreach($ordersNames as $value)
                                    <option 
                                        value="{{ $value->id }}" 
                                        @if(isset($order) && $order->orderName->id == $value->id)
                                            {{ 'selected' }} 
                                        @endif
                                    >
                                        {{ $value->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Номер приказа</label>
                        <div class="col-sm-5">
                            <input 
                                class="form-control" 
                                type="text" 
                                name="number" 
                                value="{{ isset($order)? $order->number : '' }}" 
                                required 
                                {{ isset($order)? 'disabled' : '' }}
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Дата приказа</label>
                        <div class="col-sm-5">
                            <input 
                                @change="changeOrderDate($event)"
                                class="form-control" 
                                type="date" 
                                name="date" 
                                value="{{ isset($order)? $order->order_date : '' }}" 
                                required
                                {{ isset($order)? 'disabled' : '' }}
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Прикрепить файл</label>
                        <div class="col-sm-9">
                            @isset(optional($order)->file)
                                <div class="row">
                                    <div class="col-sm-9">
                                        <label>{{ $order->file }}</label>
                                    </div>
                                    <div class="col-sm-3 text-right">
                                        <a href="{{ route('employees.download.order', ['name' => $order->file]) }}">
                                            <button type="button" class="btn btn-primary">Скачать приказ</button>
                                        </a>
                                    </div>
                                </div>
                            @endisset
                            @if(!isset($order) || $order->status == 'new')
                                <div class="row">
                                    <div class="col-sm-12">
                                        <input type="file" id="file" class="custom-file-input" name="file">
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    @if(!isset($order))
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Список голосующих</label>
                            <div class="col-sm-5">
                                <select class="form-control" id="voteUsersList" name="vote_positions_ids[]" multiple required data-live-search="true">
                                    @foreach($votesList as $position)
                                        <option value="{{ $position->id }}">{{ $position->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif
                    <hr>

                    @isset($order)
                        @php
                            $orderUsers = $order->orderName->code == 'recruitment'? $order->candidates : $order->users
                        @endphp
                        <div class="form-group">
                            <label for="" class="col-md-3 control-label">Сотрудники</label>
                            <div class="col-md-9">
                                @if($orderUsers->count() > 0)
                                    <table id="main-table" class="table table-striped table-hover dt-responsive" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>ФИО</th>
                                                <th>ИИН</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($orderUsers as $user)
                                                <tr>
                                                    <td>{{ $user->user->id }}</td>
                                                    <td>{{ $user->user->studentProfile->fio ?? '' }}</td>
                                                    <td>{{ $user->user->studentProfile->iin ?? '' }}</td>
                                                    <td>
                                                        @if($order->status == 'new')
                                                            <input type="checkbox" value="{{ $user->id }}" name='selectUserList'>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @if($order->status == 'new')
                                        <a class="btn btn-primary" v-on:click="detachUsers">Исключить из приказа выбранных</a>
                                    @endif
                                @else
                                    Список пуст
                                @endif
                            </div>
                        </div>
                        <hr>
                    @endisset

                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label"></label>
                        <div class="col-sm-8">
                            <button type="submit" class="btn btn-primary">Сохранить</button>
                            @if($id)
                                <a href="{{ route('employees.order.to.agreement', ['id' => $id]) }}" class="btn btn-primary">
                                    Отправить на согласование
                                </a>
                            @endif
                        </div>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script type="text/javascript">
        let app = new Vue({
            el: '#main',
            methods: {
                @if(!isset($order))
                    changeOrderDate: function(event){
                        const data = {};
                        data['order_date'] = event.target.value;
                        axios.post('{{ route('orders.get.positions.by.date') }}', data)
                            .then(response => {
                                $('#voteUsersList').empty();
                                if(response.data.status == 'success' && response.data.positions.length > 0){
                                    $.each(response.data.positions, function(index, value){
                                        $('#voteUsersList').append('<option value="'+value.id+'">'+value.name+'</option>');
                                    });
                                }
                                $('#voteUsersList').selectpicker('refresh');
                            });
                    }
                @endif

                @if(isset($order) && $orderUsers->count() > 0)
                    detachUsers: function() {
                        const swalWithBootstrapButtons = Swal.mixin({
                            customClass: {
                                confirmButton: 'btn btn-success',
                                cancelButton: 'btn btn-danger mr-15'
                            },
                            buttonsStyling: false
                        })

                        swalWithBootstrapButtons.fire({
                            title: 'Вы уверены?',
                            text: "Исключить выбранных сотрудников из списка!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Да, исключить!',
                            cancelButtonText: 'Нет, отменить!',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.value) {
                                var formData = new FormData();
                                formData.append('order_id', {{ $order->id }} );

                                $.each($("input[name='selectUserList']:checked"), function () {
                                    formData.append('employees[]', $(this).val());
                                });

                                axios.post('{{ route('edit.employees.order') }}', formData)
                                    .then(response => { 
                                        if(response.data.status == 'success'){
                                            location.reload();
                                        }
                                    });
                            } else if (
                                result.dismiss === Swal.DismissReason.cancel
                            ) {
                                swalWithBootstrapButtons.fire(
                                    'Отменено',
                                    'Данные в безопасности!',
                                    'success'
                                )
                            }
                        })
                    }
                @endif
            }
        });

        $(document).ready(function(){
            $('#voteUsersList').selectpicker({
                dropupAuto: false
            });
        });
    </script>
@endsection