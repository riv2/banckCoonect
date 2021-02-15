@extends("admin.admin_app")

@section("content")
    <div id="main">
        @if(Session::has('knowledge_section_success_add'))
            <div class="margin-top alert alert-success" role="alert">
                {{ Session::get('knowledge_section_success_add') }}
            </div>
        @endif
        <div class="page-header">
            <h2>Добавление записи в раздел знаний</h2>
        </div>
        <a href="{{ route('add.literature.to.catalog') }}" class="btn btn-default-light btn-xs"><i class="md md-backspace"></i> Назад</a>
        
        
        {!! Form::open([
            'url' => route('add.record.to.knowledge.page')
        ]) !!}
            <div class="row margin-t30">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Наименование</label>
                        <input type="text" name="name" class="form-control" placeholder="наименование" required>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                    </div>
                </div>
            </div>
        {!! Form::close() !!}
    </div>
@endsection

@section('scripts')

@endsection