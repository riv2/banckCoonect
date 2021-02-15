@extends("admin.admin_app")

@section("content")
    <div id="main">
        @if(Session::has('manual_success_add'))
            <div class="margin-top alert alert-success" role="alert">
                {{ Session::get('manual_success_add') }}
            </div>
        @endif
        <div class="page-header">
            <h2>Добавление записи в справочник: Гражданство</h2>
        </div>
        <a href="{{ route('manualHome') }}" class="text-decoration-none">Вернуться к справочникам</a>
        
        
        {!! Form::open([
            'url' => route('admin.manual.add.note.citizenship')
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
                    <div class="form-group">
                        <label>ID справочника ЕСУВО:</label>
                        <textarea name="link" class="form-control"></textarea>
                        @if(!empty($errors->first('link')))
                            <span class="invalid-feedback">
                                {{ $errors->first('link') }}
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