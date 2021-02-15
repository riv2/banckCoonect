@extends("admin.admin_app")

@section('title', isset($module->name) ? $module->name : 'Добавить модуль для дисциплины')

@section("content")
    <div id="main">
        <div class="page-header">
            <h2> {{ isset($module->name) ? 'Редактировать: '. $module->name : 'Добавить модуль' }}</h2>

            <a href="{{ route('adminSyllabusList', ['disciplineId' => $discipline->id]) }}" class="btn btn-default-light btn-xs">
                <i class="md md-backspace"></i> Назад
            </a>
        </div>

        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(Session::has('flash_message'))
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                {{ Session::get('flash_message') }}
            </div>
        @endif

        <div class="panel panel-default">
            <div class="panel-body">
                <div role="tabpanel">
                    <div class="tab-content tab-content-default">
                        {!! Form::open(
                            [
                                'url' => [
                                    route('admin.syllabus.module.store', [
                                        'disciplineId' => $discipline->id,
                                        'module_id' => $module->id ?? null,
                                        'language' => $language
                                    ])
                                ],
                                'class' => 'form-horizontal padding-15',
                                'role' => 'form',
                                'enctype' => 'multipart/form-data'
                            ]
                        ) !!}

                        <div role="tabpanel" class="tab-pane active">
                            <div class="form-group">
                                <label for="name" class="col-sm-3 control-label">Название</label>

                                <div class="col-sm-9">
                                    <input type="text" name="name" value="{{ old('name') ?? $module->name ?? '' }}" class="form-control">
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group">
                            <div class="col-md-offset-3 col-sm-9 ">
                                <button type="submit" class="btn btn-primary btn-lg">{{ isset($module->name) ? 'Сохранить' : 'Добавить' }}</button>
                            </div>
                        </div>

                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
@endsection
