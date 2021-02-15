@extends("admin.admin_app")

@section("content")
    <div id="main">
        @if(Session::has('manual_success_add'))
            <div class="margin-top alert alert-success" role="alert">
                {{ Session::get('manual_success_add') }}
            </div>
        @endif
        <div class="page-header">
            <h2>Добавление записи в справочник: Образовательные степени (для сотрудника)</h2>
        </div>
        <a href="{{ route('manualHome') }}" class="text-decoration-none">Вернуться к справочникам</a>
        
        
        {!! Form::open([
            'url' => route('admin.manual.add.note.education')
        ]) !!}
            <div class="row margin-top">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Имя:</label>
                        <input type="text" name="name" class="form-control">
                        @if(!empty($errors->first('name')))
                            <span class="invalid-feedback">
                                {{ $errors->first('name') }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Имя (Англ.):</label>
                        <input type="text" name="name_en" class="form-control">
                        @if(!empty($errors->first('name_en')))
                            <span class="invalid-feedback">
                                {{ $errors->first('name_en') }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Имя (Каз.):</label>
                        <input type="text" name="name_kz" class="form-control">
                        @if(!empty($errors->first('name_kz')))
                            <span class="invalid-feedback">
                                {{ $errors->first('name_kz') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Краткое имя:</label>
                        <input type="text" name="short_name" class="form-control">
                        @if(!empty($errors->first('short_name')))
                            <span class="invalid-feedback">
                                {{ $errors->first('short_name') }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Краткое имя (Англ.):</label>
                        <input type="text" name="short_name_en" class="form-control">
                        @if(!empty($errors->first('short_name_en')))
                            <span class="invalid-feedback">
                                {{ $errors->first('short_name_en') }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Краткое имя (Каз.):</label>
                        <input type="text" name="short_name_kz" class="form-control">
                        @if(!empty($errors->first('short_name_kz')))
                            <span class="invalid-feedback">
                                {{ $errors->first('short_name_kz') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Тип:</label>
                        <select class="form-control" name="type">
                            <option value="{{ App\ManualEducation::TYPE_DOCTOR }}">{{ App\ManualEducation::TYPE_DOCTOR }}</option>
                            <option value="{{ App\ManualEducation::TYPE_CANDIDATE }}">{{ App\ManualEducation::TYPE_CANDIDATE }}</option>
                            <option value="{{ App\ManualEducation::TYPE_MASTER }}">{{ App\ManualEducation::TYPE_MASTER }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Добавить запись</button>
                    </div>
                </div>
            </div> 
        {!! Form::close() !!}
    </div>
@endsection

@section('scripts')

@endsection