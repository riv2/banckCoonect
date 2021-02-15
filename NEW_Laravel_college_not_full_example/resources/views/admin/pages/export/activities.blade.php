@extends("admin.admin_app")

@section("content")
    <div id="main">
        <div class="page-header">
            <h2>Экспорт: активности</h2>
        </div>

        <div role="tabpanel">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active">
                    <a href="#students" aria-controls="students" role="tab" data-toggle="tab">Студенты</a>
                </li>
                <li role="presentation">
                    <a href="#teachers" aria-controls="teachers" role="tab" data-toggle="tab">Учителя</a>
                </li>
            </ul>

            <div class="tab-content tab-content-default">
                <div role="tabpanel" class="tab-pane active" id="students">
                    <form action="{{ route('admin.activities.export', ['type' => 'student']) }}" class="form-horizontal" method="post">
                        @csrf

                        <div class="form-group">
                            <label for="name" class="col-md-2 control-label">Имя студента</label>

                            <div class="col-md-10">
                                <input type="text" name="name" class="form-control">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="group" class="col-md-2 control-label">Группа</label>

                            <div class="col-md-10">
                                <select class="form-control" name="group">
                                    <option value=""></option>
                                    @foreach($groups as $group)
                                        <option value="{{ $group->id }}">{{ strtoupper($group->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="month" class="col-md-2 control-label">Месяц</label>

                            <div class="col-md-10">
                                <select class="form-control" name="month" v-model="student_month">
                                    <option value="1">Январь</option>
                                    <option value="2">Февраль</option>
                                    <option value="3">Март</option>
                                    <option value="4">Апрель</option>
                                    <option value="5">Май</option>
                                    <option value="6">Июнь</option>
                                    <option value="7">Июль</option>
                                    <option value="8">Август</option>
                                    <option value="9">Сентябрь</option>
                                    <option value="10">Октябрь</option>
                                    <option value="11">Ноябрь</option>
                                    <option value="12">Декабрь</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="year" class="col-md-2 control-label">Год</label>

                            <div class="col-md-10">
                                <select class="form-control year-select" name="year">
                                    @for($i = \Carbon\Carbon::now()->year - 9; $i <= \Carbon\Carbon::now()->year; $i++)
                                        <option value="{{ $i }}" {{ \Carbon\Carbon::now()->year == $i ? 'selected' : '' }}>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="col-md-2 control-label">От .. до ..</label>

                            <div class="col-md-2">
                                <input type="text" class="form-control" name="offset" value="0">
                            </div>

                            <div class="col-md-2">
                                <input type="text" class="form-control" name="count" value="100">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-offset-2 col-md-2">
                                <button class="btn btn-primary">Скачать</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div role="tabpanel" class="tab-pane" id="teachers">
                    <form action="{{ route('admin.activities.export', ['type' => 'teacher']) }}" class="form-horizontal" method="post">
                        @csrf

                        <div class="form-group">
                            <label for="name" class="col-md-2 control-label">Имя преподавателя</label>

                            <div class="col-md-10">
                                <input type="text" name="name" class="form-control">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="month" class="col-md-2 control-label">Месяц</label>

                            <div class="col-md-10">
                                <select class="form-control" name="month" v-model="teacher_month">
                                    <option value="1">Январь</option>
                                    <option value="2">Февраль</option>
                                    <option value="3">Март</option>
                                    <option value="4">Апрель</option>
                                    <option value="5">Май</option>
                                    <option value="6">Июнь</option>
                                    <option value="7">Июль</option>
                                    <option value="8">Август</option>
                                    <option value="9">Сентябрь</option>
                                    <option value="10">Октябрь</option>
                                    <option value="11">Ноябрь</option>
                                    <option value="12">Декабрь</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="year" class="col-md-2 control-label">Год</label>

                            <div class="col-md-10">
                                <select class="form-control year-select" name="year">
                                    @for($i = \Carbon\Carbon::now()->year - 9; $i <= \Carbon\Carbon::now()->year; $i++)
                                        <option value="{{ $i }}" {{ \Carbon\Carbon::now()->year == $i ? 'selected' : '' }}>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="col-md-2 control-label">От .. до ..</label>

                            <div class="col-md-2">
                                <input type="text" class="form-control" name="offset" value="0">
                            </div>

                            <div class="col-md-2">
                                <input type="text" class="form-control" name="count" value="100">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-offset-2 col-md-2">
                                <button class="btn btn-primary">Скачать</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        var app = new Vue({
            el: '#main',
            data: {
                teacher_month: new Date().getMonth(),
                student_month: new Date().getMonth(),
            },
            mounted: function(){
                $('div[role=tabpanel] a[role=tab]').click(function (e) {
                    e.preventDefault();
                    $(this).tab('show');
                });
            },
        });
    </script>
@endsection
