@extends("admin.admin_app")

@section("content")
    <div id="main">
        <div class="page-header">
            <div class="row">
                <div class="col-md-10">
                    <h2>Должности Сотрудника - {{ $user->name == '' ? $user->fio : $user->name }}</h2>
                </div> 
            </div>
        </div>

        {!! Form::open([
            'url' => route('employees.user.edit.link.position')
        ]) !!}
            <input type="hidden" name="user_id" id="user_id" value="{{ $user->id }}">
            <input type="hidden" name="user_position_id" id="user_position_id" value="{{ isset($user_position)? $user_position->id : '' }}">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Должность</label>
                        <input type="text" class="form-control" value="{{ $user_position->position->name }}" disabled>
                        @if(!empty($errors->first('position_id')))
                            <span class="invalid-feedback">
                                {{ $errors->first('position_id') }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Организация</label>
                        <select class="form-control" name="organization">
                            @foreach($organizations as $organization)
                                <option value="{{ $organization->id }}">{{ $organization->name }}</option>
                            @endforeach
                        </select>
                        @if(!empty($errors->first('organization')))
                            <span class="invalid-feedback">
                                {{ $errors->first('organization') }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Тип расчёта зарплаты</label>
                        <input type="text" name="payroll_type" class="form-control" placeholder="Тип:" value="{{ $user_position->payroll_type }}">
                        @if(!empty($errors->first('payroll_type')))
                            <span class="invalid-feedback">
                                {{ $errors->first('payroll_type') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Выберите График</label>
                        <select class="form-control" name="schedule">
                            @foreach($work_shedule as $shedule)
                                <option value="{{ $shedule->id }}" @if($user_position->schedule == $shedule->id) {{ 'selected' }} @endif>
                                    {{ $shedule->name }}
                                </option>
                            @endforeach
                        </select>
                        @if(!empty($errors->first('schedule')))
                            <span class="invalid-feedback">
                                {{ $errors->first('schedule') }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Выберите Тип Занятости</label>
                        <select class="form-control" name="employment">
                            <option 
                                value="{{ App\EmployeesUsersPosition::EMPLOYMENT_MAIN }}" 
                                @if(App\EmployeesUsersPosition::EMPLOYMENT_MAIN == $user_position->employment) {{ 'selected' }} @endif
                            >
                                {{ App\EmployeesUsersPosition::EMPLOYMENT_MAIN }}
                            </option>
                            <option 
                                value="{{ App\EmployeesUsersPosition::EMPLOYMENT_PART_TIME }}" 
                                @if(App\EmployeesUsersPosition::EMPLOYMENT_PART_TIME == $user_position->employment) {{ 'selected' }} @endif
                            >
                                {{ App\EmployeesUsersPosition::EMPLOYMENT_PART_TIME }}
                            </option>
                        </select>
                        @if(!empty($errors->first('employment')))
                            <span class="invalid-feedback">
                                {{ $errors->first('employment') }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Выберите Форму Занятости</label>
                        <select class="form-control" name="employment_form">
                            <option 
                                value="{{ App\EmployeesUsersPosition::EMPLOYMENT_FORM_MAIN }}" 
                                @if(App\EmployeesUsersPosition::EMPLOYMENT_FORM_MAIN == $user_position->employment_form) {{ 'selected' }} @endif
                            >
                                {{ App\EmployeesUsersPosition::EMPLOYMENT_FORM_MAIN }}
                            </option>
                            <option 
                                value="{{ App\EmployeesUsersPosition::EMPLOYMENT_FORM_PART_TIME }}" 
                                @if(App\EmployeesUsersPosition::EMPLOYMENT_FORM_PART_TIME == $user_position->employment_form) {{ 'selected' }} @endif
                            >
                                {{ App\EmployeesUsersPosition::EMPLOYMENT_FORM_PART_TIME }}
                            </option>
                        </select>
                        @if(!empty($errors->first('employment_form')))
                            <span class="invalid-feedback">
                                {{ $errors->first('employment_form') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Испытательный срок с</label>
                        <input type="date" class="form-control btn-block" name="probation_from" value="{{ $user_position->probation_from }}">
                        @if(!empty($errors->first('probation_from')))
                            <span class="invalid-feedback">
                                {{ $errors->first('probation_from') }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Испытательный срок по</label>
                        <input type="date" class="form-control btn-block" name="probation_to" value="{{  $user_position->probation_to }}">
                        @if(!empty($errors->first('probation_to')))
                            <span class="invalid-feedback">
                                {{ $errors->first('probation_to') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Ставка</label>
                        <input type="text" name="price" class="form-control" placeholder="Ставка:" value="{{  $user_position->price }}">
                        @if(!empty($errors->first('price')))
                            <span class="invalid-feedback">
                                {{ $errors->first('price') }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Оклад</label>
                        <input type="text" name="salary" class="form-control" placeholder="Оклад:" value="{{ $user_position->salary }}">
                        @if(!empty($errors->first('salary')))
                            <span class="invalid-feedback">
                                {{ $errors->first('salary') }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Премия</label>
                        <input type="text" name="premium" class="form-control" placeholder="Премия:" value="{{  $user_position->premium }}">
                        @if(!empty($errors->first('premium')))
                            <span class="invalid-feedback">
                                {{ $errors->first('premium') }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Надбавки</label>
                        <select class="filter-select selectpicker" name="perks[]" multiple>
                            @foreach($perks as $perk)
                                <option 
                                    value="{{ $perk->id }}" 
                                    @if(in_array($perk->id, $user_position_perks->toArray())) {{ 'selected' }} @endif
                                >
                                    {{ $perk->name.' - '.$perk->value }}
                                </option>
                            @endforeach
                        </select>
                        @if(!empty($errors->first('perks')))
                            <span class="invalid-feedback">
                                {{ $errors->first('perks') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row margin-t30">
                <div class="col-md-12 text-right">
                    <button type="submit" class="btn btn-lg btn-primary">Сохранить</button>
                </div>
            </div>
        {!! Form::close() !!}
    </div>
@endsection

@section('scripts')
<script type="text/javascript">
    $(document).ready(function(){
        $('.filter-select').selectpicker();
    });
</script>
@endsection