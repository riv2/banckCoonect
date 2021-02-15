@extends("admin.admin_app")

@section("content")
    <div id="main">
        @if(Session::has('manual_success_add'))
            <div class="margin-top alert alert-success" role="alert">
                {{ Session::get('manual_success_add') }}
            </div>
        @endif
        <div class="page-header">
            <h2>Добавление записи в справочник: Надбавки</h2>
        </div>
        <a href="{{ route('manualHome') }}" class="text-decoration-none">Вернуться к справочникам</a>
        
        
        {!! Form::open([
            'url' => route('admin.manual.add.note.perks')
        ]) !!}
            <div class="row margin-top">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Имя:</label>
                        <input type="text" name="name" class="form-control" placeholder="Введите имя">
                        @if(!empty($errors->first('name')))
                            <span class="invalid-feedback">
                                {{ $errors->first('name') }}
                            </span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label>Размер надбавки:</label>
                        <input type="text" name="value" class="form-control" placeholder="Введите размер надбавки">
                        @if(!empty($errors->first('value')))
                            <span class="invalid-feedback">
                                {{ $errors->first('value') }}
                            </span>
                        @endif
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