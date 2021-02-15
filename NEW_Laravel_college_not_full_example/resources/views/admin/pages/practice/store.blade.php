@extends("admin.admin_app")

@section('content')
    <div id="main">
        <div class="page-header">
            <a href="{{ route('admin.practice.show') }}" class="btn btn-default-light btn-xs">
                <i class="md md-backspace"></i> Назад
            </a>
        </div>

        <div class="panel panel-default">
            {!! Form::open(array(
                            'url' => route('admin.practice.store', [
                                'practice_id' => $practice->id ?? 0
                            ]) ,
                            'class'=>'form-horizontal padding-15',
                            'name'=>'practice_form',
                            'id'=>'practice_form',
                            'role'=>'form',
                            'enctype' => 'multipart/form-data')
            ) !!}
            <div class="panel-body">

                <div class="form-group
                    @if(!empty($errors->first('organization_name')))
                        has-error
                    @endif
                ">
                    <label for="organization_name" class="col-sm-3 control-label">Наименование организации</label>

                    <div class="col-sm-9">
                        <input type="text" required name="organization_name" id="organization_name" value="{{ old('organization_name') ?? $practice->organization_name ?? '' }}" class="form-control">

                        @if(!empty($errors->first('organization_name')))
                            <span class="help-block">
                                {{ $errors->first('organization_name') }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group
                    @if(!empty($errors->first('organization_activity_type')))
                        has-error
                    @endif
                ">
                    <label for="organization_activity_type" class="col-sm-3 control-label">Вид деятельности организации</label>

                    <div class="col-sm-9">
                        <input type="text" required name="organization_activity_type" id="organization_activity_type" value="{{ old('organization_activity_type') ?? $practice->organization_activity_type ?? '' }}" class="form-control">

                        @if(!empty($errors->first('organization_activity_type')))
                            <span class="help-block">
                                {{ $errors->first('organization_activity_type') }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group
                    @if(!empty($errors->first('specialities')))
                        has-error
                    @endif
                ">
                    <label for="title_ru" class="col-sm-3 control-label">Образовательные программы</label>

                    <div class="col-sm-9">
                        <select name="specialities[]" id="specialities" class="form-control selectpicker" data-live-search="true" multiple>
                            @foreach($specialitiesGroup as $year => $specialities)
                                <optgroup label="{{ $year }}">
                                    @foreach($specialities as $speciality)
                                        <option value="{{ $speciality['id'] }}" {{ $speciality['selected'] ? 'selected' : '' }}>{{ $speciality['name'] }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>

                        @if(!empty($errors->first('specialities')))
                            <span class="help-block">
                                {{ $errors->first('specialities') }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group
                    @if(!empty($errors->first('contract_number')))
                        has-error
                    @endif
                ">
                    <label for="contract_number" class="col-sm-3 control-label">№ Договора</label>

                    <div class="col-sm-9">
                        <input type="text" required name="contract_number" id="contract_number" value="{{ old('contract_number') ?? $practice->contract_number ?? '' }}" class="form-control">

                        @if(!empty($errors->first('contract_number')))
                            <span class="help-block">
                                {{ $errors->first('contract_number') }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group
                    @if(!empty($errors->first('contract_start_date')))
                        has-error
                    @endif
                ">
                    <label for="contract_start_date" class="col-sm-3 control-label">Начало действия договора</label>

                    <div class="col-sm-9">
                        <input type="date" required name="contract_start_date" id="contract_start_date" value="{{ old('contract_start_date') ?? $practice->contract_start_date ?? '' }}" class="form-control">

                        @if(!empty($errors->first('contract_start_date')))
                            <span class="help-block">
                                {{ $errors->first('contract_start_date') }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group
                    @if(!empty($errors->first('contract_end_date')))
                        has-error
                    @endif
                ">
                    <label for="contract_end_date" class="col-sm-3 control-label">Окончание действия договора</label>

                    <div class="col-sm-9">
                        <input type="date" required name="contract_end_date" id="contract_end_date" value="{{ old('contract_end_date') ?? $practice->contract_end_date ?? '' }}" class="form-control">

                        @if(!empty($errors->first('contract_end_date')))
                            <span class="help-block">
                                {{ $errors->first('contract_end_date') }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group
                    @if(!empty($errors->first('capacity')))
                        has-error
                    @endif
                ">
                    <label for="capacity" class="col-sm-3 control-label">Вместимость базы практики</label>

                    <div class="col-sm-9">
                        <input type="text" required name="capacity" id="capacity" value="{{ old('capacity') ?? $practice->capacity ?? '' }}" class="form-control">

                        @if(!empty($errors->first('capacity')))
                            <span class="help-block">
                                {{ $errors->first('capacity') }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group
                    @if(!empty($errors->first('scan.*')))
                        has-error
                    @endif
                ">
                    <label for="scan" class="col-sm-3 control-label">Скан договора</label>

                    <div class="col-sm-9">
                        @if(!empty($scans))
                            @foreach($scans as $scan)
                                <div class="row form-group">
                                    <a class="col-md-11"
                                            href="{{ route('admin.download.scan', ['file' => $scan -> file_name]) }}">
                                        {{ $scan -> original_name }}
                                    </a>
                                    <span class="col-md-1">
                                        <a href="{{ route('admin.remove.scan', ['file' => $scan -> file_name]) }}" class="btn btn-default">
                                            <i class="md md-remove"></i>
                                        </a>
                                    </span>
                                </div>
                            @endforeach
                        @endif


                        <input type="file" multiple name="scan[]" id="scan" value="" class="form-control">

                        @if(!empty($errors->first('scan.*')))
                            <span class="help-block">
                                {{ $errors->first('scan.*')}}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-2">
                        <button class="btn btn-primary">Сохранить</button>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@endsection

@section('scripts')

@endsection
