@extends("admin.admin_app")

@section("content")

    <div id="main">
        <div class="page-header">
            <h2>Участие в акции</h2>

            <a href="{{ route('adminPromotionList') }}" class="btn btn-default-light btn-xs"><i class="md md-backspace"></i> Назад</a>

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
                {!! Form::open(array('url' => array( route('adminPromotionInfo', ['id' => $promotion->id]) ),'class'=>'form-horizontal padding-15','name'=>'service_form','id'=>'service_form','role'=>'form','enctype' => 'multipart/form-data')) !!}

                <div class="form-group">
                    <label class="col-md-3 control-label">ФИО студента</label>
                    <div class="col-md-9">
                        {{ $promotion->fio }}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label">Наименование акции</label>
                    <div class="col-md-9">
                        {{ __($promotion->name) }}
                    </div>
                </div>

                @if($promotion->name == 'working_student')
                <div class="form-group">
                    <label for="theme_number" class="col-md-3 control-label">Справка с места работы</label>
                    <div class="col-md-2">
                        <a href="/images/uploads/works/{{ $promotion->work->work_certificate_file }}" target="_blank">{{ __('See document') }}</a>
                    </div>
                </div>
                <div class="form-group">
                    <label for="theme_number" class="col-md-3 control-label">Справка о пенсионных отчислениях</label>
                    <div class="col-md-2">
                        <a href="/images/uploads/works/{{ $promotion->work->pension_report_file }}" target="_blank">{{ __('See document') }}</a>
                    </div>
                </div>
                @endif

                <div class="form-group">
                    <label for="status" class="col-md-3 control-label">Статус</label>
                    <div class="col-md-3">
                        <select class="selectpicker" name="status" data-live-search="true" data-size="5"
                                title="{{ __('Please select') }}">
                            <option value="moderation" @if($promotion->status == 'moderation') selected @endif>Не проверено</option>
                            <option value="active" @if($promotion->status == 'active') selected @endif>Проверено</option>
                            <option value="reject" @if($promotion->status == 'reject') selected @endif>Отказ</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <div  class="col-md-3 control-label"></div>
                    <div class="col-md-9">
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                    </div>
                </div>

                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
