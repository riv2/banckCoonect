@extends("admin.admin_app")

@section("content")

<div id="main">
	<div class="page-header">
		<h2> {{ isset($item->id) ? 'Редактировать: ' : 'Добавить' }} тип</h2>
		
		<a href="{{ route('adminApplicationTypeList') }}" class="btn btn-default-light btn-xs"><i class="md md-backspace"></i> Назад</a>
	  
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
                <span aria-hidden="true">&times;</span></button>
            {{ Session::get('flash_message') }}
        </div>
    @endif
   
   	<div class="panel panel-default">
            <div class="panel-body">
                {!! Form::open(array('url' => array( route('adminApplicationTypeAddEdit') ), 'class'=>'form-horizontal padding-15', 'role'=>'form','enctype' => 'multipart/form-data')) !!}
                <input type="hidden" name="id" value="{{ isset($item->id) ? $item->id : null }}">
                
                <div class="form-group">
                    <label for="" class="col-sm-4 control-label">Ключ (на английском и уникальный)</label>
                      <div class="col-sm-6">
                        <input type="text" name="key" value="{{ isset($item->key) ? $item->key : null }}" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label for="" class="col-sm-4 control-label">Название на русском</label>
                      <div class="col-sm-6">
                        <input type="text" name="name_ru" value="{{ isset($item->name_ru) ? $item->name_ru : null }}" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label for="" class="col-sm-4 control-label">Название на казахском</label>
                      <div class="col-sm-6">
                        <input type="text" name="name_kz" value="{{ isset($item->name_kz) ? $item->name_kz : null }}" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label for="" class="col-sm-4 control-label">Название на английский</label>
                      <div class="col-sm-6">
                        <input type="text" name="name_en" value="{{ isset($item->name_en) ? $item->name_en : null }}" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label for="" class="col-sm-4 control-label">Шаблон для студента</label>
                      <div class="col-sm-6">
                        <input type="file" name="template_doc" value="{{ isset($item->template_doc) ? $item->template_doc : null }}" class="form-control">
                        @if(isset($item->template_doc))
                            <a href="{{ isset($item->template_doc) ? '/images/uploads/requests_templates/'.$item->template_doc : null }}">{{ isset($item->template_doc) ? $item->template_doc : null }}</a>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <label for="" class="col-sm-4 control-label">Список должностей подписантов</label>
                      <div class="col-sm-6">
                        <select id="position" name="positions[]" class="selectpicker show-tick form-control" multiple data-live-search="true" value="{{ isset($item->name_en) ? $item->name_en : null }}">
                            @foreach($positions as $position)
                                <option 
                                    @if( isset($signers) && in_array($position->id, $signers) )
                                        selected 
                                    @endif 
                                    value="{{$position->id}}">{{$position->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                 
                <hr>
                <div class="form-group">
                    <div class="col-md-offset-3 col-sm-6 ">
                    	<button type="submit" class="btn btn-primary">{{ isset($item->id) ? 'Редактировать ' : 'Добавить' }} тип</button>
                         
                    </div>
                </div>
                
                {!! Form::close() !!} 
            </div>
        </div>
   
    
</div>

@endsection