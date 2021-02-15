@extends("admin.admin_app")

@section('style')
    <link rel="stylesheet" href="https://unpkg.com/vue-multiselect@2.1.0/dist/vue-multiselect.min.css">
@endsection
@section("content")
    <div id="main">
        {!! Form::open([
            'url' => route('user.edit.employees'),
            'enctype' => 'multipart/form-data'
        ]) !!}
        <div class="row margin-t25">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Выберите должность</label>
                    <select v-model="vacancy" class="form-control" name="vacancy_id" @change="onChangeSelectPosition($event)" required>
                        <option></option>
                        @foreach($vacancies as $vacancy)
                            @if($vacancy->position && array_key_exists($vacancy->position->id, $positions))
                                @php unset($positions[$vacancy->position->id]); @endphp
                                <option value="{{ $vacancy->id }}" data-type="resume">{{ $vacancy->position->name }}</option>
                            @endif
                        @endforeach
                        @foreach($positions as $position_id => $position_name)
                            <option value="{{ $position_id }}" data-type="non_resume">{{ $position_name }}</option>
                        @endforeach
                    </select>
                </div>
                <input type="hidden" name="user_id" value="{{ $id }}">
            </div>
            <div class="col-md-3 col-md-offset-6 text-right">
                <button type="submit" class="btn btn-primary btn-lg margin-t15">Сохранить</button>
            </div>
        </div>

        <div class="userPositionsBlockShow" style="display: none;">
            <h3>Требования</h3>
            <div id="quiz-tab" class="margin-top">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li role="presentation" class="nav-item active">
                        <a class="nav-link" id="personal_data-tab" data-toggle="tab" href="#personal_data" role="tab" aria-controls="personal_data" aria-selected="true">Персональные Данные</a>
                    </li>
                    <li role="presentation" class="nav-item">
                        <a class="nav-link" id="education-tab" data-toggle="tab" href="#education" role="tab" aria-controls="education" aria-selected="false">Образование</a>
                    </li>
                    <li role="presentation" class="nav-item">
                        <a class="nav-link" id="teacher-tab" data-toggle="tab" href="#teacher" role="tab" aria-controls="teacher" aria-selected="false">Повышение квалификации</a>
                    </li>
                    <li role="presentation" class="nav-item">
                        <a class="nav-link" id="print_edition-tab" data-toggle="tab" href="#print_edition" role="tab" aria-controls="print_edition" aria-selected="false">Печатные издания</a>
                    </li>
                    <li role="presentation" class="nav-item">
                        <a class="nav-link" id="publications-tab" data-toggle="tab" href="#publications" role="tab" aria-controls="publications" aria-selected="false">Труды и публикации</a>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane active" id="personal_data" role="tabpanel" aria-labelledby="personal_data-tab">
                        <div v-if="requirementsPI">
                            <div class="row">
                                <div v-for="item in requirementsPI" class="col-md-3 col-sm-12">
                                    <div class="form-group">
                                        <label :for="'personal_info_'+item.requirement_id" class="font-weight-light">
                                            @{{ item.requirement.name }}
                                        </label>
                                        <input
                                                class="form-control"
                                                :type="item.requirement.field_type"
                                                :name="'requirements[personal_info]['+item.requirement_id+']'"
                                                :id="'personal_info_'+item.requirement_id"
                                                :value="item.requirement.field_type != 'file' ? item.content : ''"
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else>
                            <div class="row">
                                <div class="col-md-12">
                                    <p>Требования категории "персональная информация" для этой должности отсутствуют.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="education" role="tabpanel" aria-labelledby="education-tab">
                        <div v-if="requirementsE">
                            <div class="row">
                                <div v-for="records in requirementsE" class="col-md-12 col-sm-12">
                                    <div v-for="(record, index) in JSON.parse(records.json_content)">
                                        <hr>
                                        <h3 class="text-uppercase text-monospace">
                                            @{{ records.requirement.name }}
                                        </h3>
                                        <div class="row">
                                            <div v-for="(content, field_name) in record" class="col-md-3 col-sm-12">
                                                <div class="form-group">
                                                    <div v-for="requirement_field in records.requirement.fields">
                                                        <div v-if="requirement_field.field_name == field_name">
                                                            <label
                                                                    :for="'education_'+records.requirement.id"
                                                                    class="font-weight-light"
                                                            >
                                                                @{{ requirement_field.name }}
                                                            </label>
                                                            <div v-if="requirement_field.field_type == 'select'">
                                                                <select
                                                                        class="form-control"
                                                                        :name="
                                                                            'requirements[education]['
                                                                            +records.requirement.id+
                                                                            '][replaced_index_'
                                                                            +index+
                                                                            ']['
                                                                            +requirement_field.field_name+
                                                                            ']'"
                                                                        :id="'education_'+records.requirement.id+'_'+requirement_field.field_name"
                                                                >
                                                                    <option
                                                                            v-for="option in records.requirement.options"
                                                                            :value="option"
                                                                            :selected="option == content"
                                                                    >
                                                                        @{{ option }}
                                                                    </option>
                                                                </select>
                                                            </div>
                                                            <div v-else>
                                                                <input
                                                                        class="form-control"
                                                                        :type="requirement_field.field_type"
                                                                        :name="
                                                                            'requirements[education]['
                                                                            +records.requirement.id+
                                                                            '][replaced_index_'
                                                                            +index+
                                                                            ']['
                                                                            +requirement_field.field_name+
                                                                            ']'"
                                                                        :id="'education_'+records.requirement.id+'_'+requirement_field.field_name"
                                                                        :value="requirement_field.field_type != 'file' ? content : ''"
                                                                >
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else>
                            <div class="row margin-t15">
                                <div class="col-md-12">
                                    <p>Требования категории "образование" для этой должности отсутствуют.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="teacher" role="tabpanel" aria-labelledby="teacher-tab">
                        <div v-if="requirementsQI">
                            <div class="row">
                                <div v-for="records in requirementsQI" class="col-md-12 col-sm-12">
                                    <div v-for="(record, index) in JSON.parse(records.json_content)">
                                        <hr>
                                        <h3 class="text-uppercase text-monospace">
                                            @{{ records.requirement.name }}
                                        </h3>
                                        <div class="row">
                                            <div v-for="(content, field_name) in record" class="col-md-3 col-sm-12">
                                                <div class="form-group">
                                                    <div v-for="requirement_field in records.requirement.fields">
                                                        <div v-if="requirement_field.field_name == field_name">
                                                            <label
                                                                    :for="'education_'+records.requirement.id"
                                                                    class="font-weight-light"
                                                            >
                                                                @{{ requirement_field.name }}
                                                            </label>
                                                            <div v-if="requirement_field.field_type == 'select'">
                                                                <select
                                                                        class="form-control"
                                                                        :name="
                                                                            'requirements[qualification_increase]['
                                                                            +records.requirement.id+
                                                                            '][replaced_index_'
                                                                            +index+
                                                                            ']['
                                                                            +requirement_field.field_name+
                                                                            ']'"
                                                                        :id="'qualification_'+records.requirement.id+'_'+requirement_field.field_name"
                                                                >
                                                                    <option
                                                                            v-for="option in records.requirement.options"
                                                                            :value="option"
                                                                            :selected="option == content"
                                                                    >
                                                                        @{{ option }}
                                                                    </option>
                                                                </select>
                                                            </div>
                                                            <div v-else>
                                                                <input
                                                                        class="form-control"
                                                                        :type="requirement_field.field_type"
                                                                        :name="
                                                                            'requirements[qualification_increase]['
                                                                            +records.requirement.id+
                                                                            '][replaced_index_'
                                                                            +index+
                                                                            ']['
                                                                            +requirement_field.field_name+
                                                                            ']'"
                                                                        :id="'qualification_'+records.requirement.id+'_'+requirement_field.field_name"
                                                                        :value="requirement_field.field_type != 'file' ? content : ''"
                                                                >
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else>
                            <div class="row margin-t15">
                                <div class="col-md-12">
                                    <p>Требования категории "повышение квалификации" для этой должности отсутствуют.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="print_edition" role="tabpanel" aria-labelledby="print_edition-tab">
                        <div v-if="requirementsS">
                            <div class="row">
                                <div v-for="records in requirementsS" class="col-md-12 col-sm-12">
                                    <div v-for="(record, index) in JSON.parse(records.json_content)">
                                        <hr>
                                        <h3 class="text-uppercase text-monospace">
                                            @{{ records.requirement.name }}
                                        </h3>
                                        <div class="row">
                                            <div v-for="(content, field_name) in record" class="col-md-3 col-sm-12">
                                                <div class="form-group">
                                                    <div v-for="requirement_field in records.requirement.fields">
                                                        <div v-if="requirement_field.field_name == field_name">
                                                            <label
                                                                    :for="'education_'+records.requirement.id"
                                                                    class="font-weight-light"
                                                            >
                                                                @{{ requirement_field.name }}
                                                            </label>
                                                            <div v-if="requirement_field.field_type == 'select'">
                                                                <select
                                                                        class="form-control"
                                                                        :name="
                                                                            'requirements[seniority]['
                                                                            +records.requirement.id+
                                                                            '][replaced_index_'
                                                                            +index+
                                                                            ']['
                                                                            +requirement_field.field_name+
                                                                            ']'"
                                                                        :id="'seniority_'+records.requirement.id+'_'+requirement_field.field_name"
                                                                >
                                                                    <option
                                                                            v-for="option in records.requirement.options"
                                                                            :value="option"
                                                                            :selected="option == content"
                                                                    >
                                                                        @{{ option }}
                                                                    </option>
                                                                </select>
                                                            </div>
                                                            <div v-else>
                                                                <input
                                                                        class="form-control"
                                                                        :type="requirement_field.field_type"
                                                                        :name="
                                                                            'requirements[seniority]['
                                                                            +records.requirement.id+
                                                                            '][replaced_index_'
                                                                            +index+
                                                                            ']['
                                                                            +requirement_field.field_name+
                                                                            ']'"
                                                                        :id="'seniority_'+records.requirement.id+'_'+requirement_field.field_name"
                                                                        :value="requirement_field.field_type != 'file' ? content : ''"
                                                                >
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else>
                            <div class="row margin-t15">
                                <div class="col-md-12">
                                    <p>Требования категории "печатные издания" для этой должности отсутствуют.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="publications" role="tabpanel" aria-labelledby="publications-tab">
                        <div v-if="requirementsNIR">
                            <div class="row">
                                <div v-for="records in requirementsNIR" class="col-md-12 col-sm-12">
                                    <div v-for="(record, index) in JSON.parse(records.json_content)">
                                        <hr>
                                        <h3 class="text-uppercase text-monospace">
                                            @{{ records.requirement.name }}
                                        </h3>
                                        <div class="row">
                                            <div v-for="(content, field_name) in record" class="col-md-3 col-sm-12">
                                                <div class="form-group">
                                                    <div v-for="requirement_field in records.requirement.fields">
                                                        <div v-if="requirement_field.field_name == field_name">
                                                            <label
                                                                    :for="'education_'+records.requirement.id"
                                                                    class="font-weight-light"
                                                            >
                                                                @{{ requirement_field.name }}
                                                            </label>
                                                            <div v-if="requirement_field.field_type == 'select'">
                                                                <select
                                                                        class="form-control"
                                                                        :name="
                                                                            'requirements[nir]['
                                                                            +records.requirement.id+
                                                                            '][replaced_index_'
                                                                            +index+
                                                                            ']['
                                                                            +requirement_field.field_name+
                                                                            ']'"
                                                                        :id="'nir_'+records.requirement.id+'_'+requirement_field.field_name"
                                                                >
                                                                    <option
                                                                            v-for="option in requirement_field.options"
                                                                            :value="option"
                                                                            :selected="option == content"
                                                                    >
                                                                        @{{ option }}
                                                                    </option>
                                                                </select>
                                                            </div>
                                                            <div v-else>
                                                                <input
                                                                        class="form-control"
                                                                        :type="requirement_field.field_type"
                                                                        :name="
                                                                            'requirements[nir]['
                                                                            +records.requirement.id+
                                                                            '][replaced_index_'
                                                                            +index+
                                                                            ']['
                                                                            +requirement_field.field_name+
                                                                            ']'"
                                                                        :id="'nir_'+records.requirement.id+'_'+requirement_field.field_name"
                                                                        :value="requirement_field.field_type != 'file' ? content : ''"
                                                                >
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else>
                            <div class="row margin-t15">
                                <div class="col-md-12">
                                    <p>Требования категории "труды и публикации" для этой должности отсутствуют.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}

        <div class="row userPositionsBlockShow" id="userPositionInfo" style="display: none;">
            <div class="col-md-12">
                <h3>
                    Информация о должности
                    <a
                            id="linkToUserPositionEditFields"
                            href=""
                            class="btn btn-success"
                            data-toggle="tooltip"
                            data-placement="top"
                            title="Редактировать поля"
                    >
                        <i class="md md-edit"></i>
                    </a>
                </h3>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Организация</label>
                            <input type="text" class="form-control" name="organization" disabled="true" value="">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Тип расчёта зарплаты</label>
                            <input type="text" class="form-control" name="payroll_type" disabled="true" value="">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Испытательный срок с</label>
                            <input type="date" class="form-control btn-block" name="probation_from" disabled="true" value="">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Испытательный срок по</label>
                            <input type="date" class="form-control btn-block" name="probation_to" disabled="true" value="">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Выберите График</label>
                            <select class="form-control" name="schedule" disabled="true">
                                @foreach($work_shedule as $shedule)
                                    <option value="{{ $shedule->id }}">{{ $shedule->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Выберите Тип Занятости</label>
                            <select class="form-control" name="employment" disabled="true">
                                <option value="{{ App\EmployeesUsersPosition::EMPLOYMENT_MAIN }}">
                                    {{ App\EmployeesUsersPosition::EMPLOYMENT_MAIN }}
                                </option>
                                <option value="{{ App\EmployeesUsersPosition::EMPLOYMENT_PART_TIME }}">
                                    {{ App\EmployeesUsersPosition::EMPLOYMENT_PART_TIME }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Выберите Форму Занятости</label>
                            <select class="form-control" name="employment_form" disabled="true">
                                <option value="{{ App\EmployeesUsersPosition::EMPLOYMENT_FORM_MAIN }}">
                                    {{ App\EmployeesUsersPosition::EMPLOYMENT_FORM_MAIN }}
                                </option>
                                <option value="{{ App\EmployeesUsersPosition::EMPLOYMENT_FORM_PART_TIME }}">
                                    {{ App\EmployeesUsersPosition::EMPLOYMENT_FORM_PART_TIME }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Ставка</label>
                            <input type="text" class="form-control" name="price" disabled="true" value="">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Оклад</label>
                            <input type="text" class="form-control" name="salary" disabled="true" value="">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Премия</label>
                            <input type="text" class="form-control" name="premium" disabled="true" value="">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Надбавки</label>
                            <select class="filter-select selectpicker" name="perks[]" disabled="true" multiple>
                                @foreach($perks as $perk)
                                    <option value="{{ $perk->id }}">{{ $perk->name.' - '.$perk->value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <h3>
                    Соц. пакет сотрудника
                    <a
                            href="{{ route('employees.user.social.package', ['id' => $id]) }}"
                            class="btn btn-success"
                            data-toggle="tooltip"
                            data-placement="top"
                            title="Редактировать поля"
                    >
                        <i class="md md-edit"></i>
                    </a>
                </h3>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Бензин</label>
                            <input
                                    type="text"
                                    class="form-control"
                                    disabled="true"
                                    value="{{ optional($socialPackage)->gas != '' ? $socialPackage->gas : 'Не установлено' }}"
                            >
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Серт. Корзина</label>
                            <input
                                    type="text"
                                    class="form-control"
                                    disabled="true"
                                    value="{{ optional($socialPackage)->basket != '' ? $socialPackage->basket : 'Не установлено' }}"
                            >
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Серт. Медикаменты</label>
                            <input
                                    type="text"
                                    class="form-control"
                                    disabled="true"
                                    value="{{ optional($socialPackage)->medicines != '' ? $socialPackage->medicines : 'Не установлено' }}"
                            >
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Сотовая связь</label>
                            <input
                                    type="text"
                                    class="form-control"
                                    disabled="true"
                                    value="{{ optional($socialPackage)->cellular != '' ? $socialPackage->cellular : 'Не установлено' }}"
                            >
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Такси</label>
                            <input
                                    type="text"
                                    class="form-control"
                                    disabled="true"
                                    value="{{ optional($socialPackage)->taxi != '' ? $socialPackage->taxi : 'Не установлено' }}"
                            >
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-1">
                        <div class="form-check">
                            <input
                                    class="form-check-input"
                                    type="checkbox"
                                    disabled="true"
                                    name="food"
                                    {{ optional($socialPackage)->food ? 'checked' : '' }}
                            >
                            <label class="form-check-label" for="file">
                                Питание
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @isset($isTeacher)
            <div :class="vacancy ? '' : 'hide'" class="row">
                <div class="col-md-12">
                    <h3>
                        Дисциплины преподавателя
                    </h3>
                    <div class="row">
                        <div class="form-group form-horizontal ">
                            <div class="col-sm-6">
                                <multiselect :multiple="true"
                                             v-model="selectedDisciplines"
                                             label="name"
                                             class="padding-b10"
                                             track-by="name"
                                             :options="positionSectorDisciplines"
                                             :close-on-select="false"
                                             :clear-on-select="true"
                                             :preserve-search="true"
                                             :limit="1"
                                             :preselect-first="true">
                                </multiselect>
                            </div>
                            <div class="btn-group col-sm-5">
                                <button v-on:click="addDisciplines" class="btn btn-info">Добавить дисциплины</button>
                                <button v-on:click="saveDisciplinesChange" class="btn btn-success margin-l15">Сохранить </button>
                            </div>
                        </div>
                    </div>
                    <form name="disciplines" class="row padding-15">
                        <table id="data-table-disciplines" class="table table-striped table-hover dt-responsive" cellspacing="0" width="100%">
                            <thead>
                            <tr>
                                <th class="id">ID</th>
                                <th class="name">Наименование</th>
                                <th class="credits">Кредитность</th>
                                <th class="lang">Язык</th>
                                <th>Действие</th>
                            </tr>
                            </thead>

                            <tbody>
                            {{-- TODO --}}
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        @endisset
        @endsection

        @section('scripts')
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
            <script src="https://unpkg.com/vue-multiselect@2.1.0"></script>
            <script type="text/javascript">
                Vue.component('multiselect', window.VueMultiselect.default);

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                var main = new Vue({
                    el: '#main',
                    data:{
                        clear: false,
                        requirementsPI:  {!! json_encode($requirements['personal_info'] ?? null) !!},
                        requirementsE:   {!! json_encode($requirements['education'] ?? null) !!},
                        requirementsQI:  {!! json_encode($requirements['qualification_increase'] ?? null) !!},
                        requirementsS: {!! json_encode($requirements['seniority'] ?? null) !!},
                        requirementsNIR:  {!! json_encode($requirements['nir'] ?? null) !!},
                        vacancy: null,
                        selectedDisciplines: [],
                        showPositionDisciplines: false,
                        disciplinesDataTable: null,
                        positionDepartmentId: null,
                        positionSectorDisciplines: [],
                    },
                    methods: {
                        deleteDiscipline(disciplineId){
                            axios.post('{{route('employees.user.delete.discipline', ['id' => $id])}}', {disciplineId})
                                .then(({data}) => {
                                    this.getSectorDisciplines()
                                    this.disciplinesDataTable.ajax.reload()
                                })
                        },
                        saveDisciplinesChange(){
                            const form = new FormData(document.forms.disciplines)

                            axios.post('{{route('employees.user.save.disciplines', ['id' => $id])}}', form)
                                .then(() => {
                                    Swal.fire(
                                        'Good job!',
                                        'Дисциплины были сохранены!',
                                        'success'
                                    )
                                    this.disciplinesDataTable.ajax.reload()
                                })
                                .catch(({data}) => {
                                    Swal.fire('Fail!', data, 'error')
                                })
                        },
                        addDisciplines(){
                            axios.post('{{route('employees.user.add.disciplines', ['id' => $id])}}', {
                                disciplines: this.selectedDisciplines,
                                vacancy: this.vacancy
                            })
                                .then( ({data}) => {
                                    this.getSectorDisciplines()
                                    this.disciplinesDataTable.ajax.reload()

                                    Swal.fire(
                                        'Good job!',
                                        'Дисциплины были добавлены!',
                                        'success'
                                    )
                                })
                                .catch(({data}) => {
                                    Swal.fire('Fail!', data, 'error')
                                })
                        },
                        getSectorDisciplines(){
                            axios.post('{{route('employees.user.disciplinesList', ['id' => $id , '']) }}/'+ this.positionDepartmentId)
                                .then(({data}) => {
                                    this.positionSectorDisciplines = data
                                })
                        },
                        initDisciplinesTable(){
                            this.disciplinesDataTable = $('#data-table-disciplines').DataTable({
                                "processing": true,
                                "serverSide": true,
                                drawCallback: function(){
                                    $('.selectpiker').selectpicker();
                                },
                                "ajax": {
                                    url: "{{ route('employees.user.disciplinesDataTable', ['id' => $id ]) }}",
                                    type: "post",
                                },
                                columns: [
                                    {
                                        data: 'discipline_id',
                                    },
                                    {
                                        data: 'name',
                                        orderable: false
                                    },
                                    {
                                        data: 'credits',
                                        orderable: false
                                    },
                                    {
                                        data: 'lang',
                                        orderable: false
                                    },
                                    {
                                        data: 'actions',
                                        orderable: false
                                    }
                                ]
                            });
                            $('<input type="text" style="width: 100px" class="form-control"/>')
                                .appendTo($("#data-table-disciplines thead th.id"))
                                .on('change', function () {
                                    var val = $(this).val();

                                    main.disciplinesDataTable.column(0)
                                        .search(val)
                                        .draw();
                                });
                            $('<input type="text" style="width: 200px" class="form-control"/>')
                                .appendTo($("#data-table-disciplines thead th.name"))
                                .on('change', function () {
                                    var val = $(this).val();

                                    main.disciplinesDataTable.column(1)
                                        .search(val)
                                        .draw();
                                });
                            $('<input type="number" style="width: 100px" class="form-control"/>')
                                .appendTo($("#data-table-disciplines thead th.credits"))
                                .on('change', function () {
                                    var val = $(this).val();

                                    main.disciplinesDataTable.column(2)
                                        .search(val)
                                        .draw();
                                });
                            $(`<select class="form-control">
                                <option value="">По умолчанию</option>
                                <option value="kz">kz</option>
                                <option value="ru">ru</option>
                                <option value="en">en</option>
                            </select>`)
                                .appendTo($("#data-table-disciplines thead th.lang"))
                                .on('change', function () {
                                    var val = $(this).val();

                                    main.disciplinesDataTable.column(3)
                                        .search(val)
                                        .draw();
                                });
                        },
                        onChangeSelectPosition(event) {
                            if(this.vacancy !== '' && this.vacancy !== null){
                                axios.post('{{route('employees.checkPositionDisciplines', '')}}/' + this.vacancy)
                                    .then( ({data}) => {
                                        this.showPositionDisciplines = data.position_is_discipline
                                        this.positionDepartmentId = data.department_id

                                        this.getSectorDisciplines()
                                    })
                            } else {
                                this.showPositionDisciplines = false
                            }
                            if(event.target.value != ''){
                                const data = {};
                                let pos_id = '';
                                let url = '{{ route("employees.user.edit.position", ["user_id" => $id]) }}';
                                data['vacancy_id'] = event.target.value;
                                data['type'] = event.target.options[event.target.options.selectedIndex].dataset.type;
                                data['user_id'] = {{ $id?? '' }}
                                axios.post('{{ route('get.user.position.requirements') }}', data)
                                    .then(response => {
                                        console.log(response.data.requirements);
                                        if(response.data.requirements != 'empty' && response.data.requirements.personal_info.length > 0){
                                            this.requirementsPI = response.data.requirements.personal_info;
                                        } else {
                                            this.requirementsPI = false;
                                        }
                                        if(response.data.requirements != 'empty' && Object.keys(response.data.requirements.education).length > 0){
                                            this.requirementsE = response.data.requirements.education;
                                        } else {
                                            this.requirementsE = false;
                                        }
                                        if(response.data.requirements != 'empty' && Object.keys(response.data.requirements.qualification_increase).length > 0){
                                            this.requirementsQI = response.data.requirements.qualification_increase;
                                        } else {
                                            this.requirementsQI = false;
                                        }
                                        if(response.data.requirements != 'empty' && Object.keys(response.data.requirements.seniority).length > 0){
                                            this.requirementsS = response.data.requirements.seniority;
                                        } else {
                                            this.requirementsS = false;
                                        }
                                        if(response.data.requirements != 'empty' && Object.keys(response.data.requirements.nir).length > 0){
                                            this.requirementsNIR = response.data.requirements.nir;
                                        } else {
                                            this.requirementsNIR = false;
                                        }

                                        $.each(response.data.userPosition, function(index, value){
                                            $('#userPositionInfo [name="'+index+'"]').val(value);
                                        });

                                        $('.selectpicker').selectpicker('val', response.data.perks);

                                        $('.userPositionsBlockShow').show();
                                        pos_id = '/'+response.data.position_id;
                                        $('#linkToUserPositionEditFields').attr('href', url+pos_id);
                                    });
                            } else {
                                $('.userPositionsBlockShow').hide();
                            }
                        },
                    },
                    mounted(){
                        @isset($isTeacher)
                            this.initDisciplinesTable()
                        @endisset
                    }
                });

                $(document).ready(function() {
                    $(window).keydown(function(event){
                        if(event.keyCode == 13) {
                            event.preventDefault();
                            return false;
                        }
                    });

                    $('#quiz-tab a[role=tab]').click(function (e) {
                        e.preventDefault();
                        $(this).tab('show');
                    });

                    $('.selectpicker').selectpicker();
                    $('[data-toggle="tooltip"]').tooltip();
                });

            </script>
@endsection@extends("admin.admin_app")

        @section('style')
            <link rel="stylesheet" href="https://unpkg.com/vue-multiselect@2.1.0/dist/vue-multiselect.min.css">
        @endsection
        @section("content")
            <div id="main">
                {!! Form::open([
                    'url' => route('user.edit.employees'),
                    'enctype' => 'multipart/form-data'
                ]) !!}
                <div class="row margin-t25">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Выберите должность</label>
                            <select v-model="vacancy" class="form-control" name="vacancy_id" @change="onChangeSelectPosition($event)" required>
                                <option></option>
                                @foreach($vacancies as $vacancy)
                                    @if($vacancy->position && array_key_exists($vacancy->position->id, $positions))
                                        @php unset($positions[$vacancy->position->id]); @endphp
                                        <option value="{{ $vacancy->id }}" data-type="resume">{{ $vacancy->position->name }}</option>
                                    @endif
                                @endforeach
                                @foreach($positions as $position_id => $position_name)
                                    <option value="{{ $position_id }}" data-type="non_resume">{{ $position_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" name="user_id" value="{{ $id }}">
                    </div>
                    <div class="col-md-3 col-md-offset-6 text-right">
                        <button type="submit" class="btn btn-primary btn-lg margin-t15">Сохранить</button>
                    </div>
                </div>

                <div class="userPositionsBlockShow" style="display: none;">
                    <h3>Требования</h3>
                    <div id="quiz-tab" class="margin-top">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li role="presentation" class="nav-item active">
                                <a class="nav-link" id="personal_data-tab" data-toggle="tab" href="#personal_data" role="tab" aria-controls="personal_data" aria-selected="true">Персональные Данные</a>
                            </li>
                            <li role="presentation" class="nav-item">
                                <a class="nav-link" id="education-tab" data-toggle="tab" href="#education" role="tab" aria-controls="education" aria-selected="false">Образование</a>
                            </li>
                            <li role="presentation" class="nav-item">
                                <a class="nav-link" id="teacher-tab" data-toggle="tab" href="#teacher" role="tab" aria-controls="teacher" aria-selected="false">Повышение квалификации</a>
                            </li>
                            <li role="presentation" class="nav-item">
                                <a class="nav-link" id="print_edition-tab" data-toggle="tab" href="#print_edition" role="tab" aria-controls="print_edition" aria-selected="false">Печатные издания</a>
                            </li>
                            <li role="presentation" class="nav-item">
                                <a class="nav-link" id="publications-tab" data-toggle="tab" href="#publications" role="tab" aria-controls="publications" aria-selected="false">Труды и публикации</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane active" id="personal_data" role="tabpanel" aria-labelledby="personal_data-tab">
                                <div v-if="requirementsPI">
                                    <div class="row">
                                        <div v-for="item in requirementsPI" class="col-md-3 col-sm-12">
                                            <div class="form-group">
                                                <label :for="'personal_info_'+item.requirement_id" class="font-weight-light">
                                                    @{{ item.requirement.name }}
                                                </label>
                                                <input
                                                        class="form-control"
                                                        :type="item.requirement.field_type"
                                                        :name="'requirements[personal_info]['+item.requirement_id+']'"
                                                        :id="'personal_info_'+item.requirement_id"
                                                        :value="item.requirement.field_type != 'file' ? item.content : ''"
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <p>Требования категории "персональная информация" для этой должности отсутствуют.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="education" role="tabpanel" aria-labelledby="education-tab">
                                <div v-if="requirementsE">
                                    <div class="row">
                                        <div v-for="records in requirementsE" class="col-md-12 col-sm-12">
                                            <div v-for="(record, index) in JSON.parse(records.json_content)">
                                                <hr>
                                                <h3 class="text-uppercase text-monospace">
                                                    @{{ records.requirement.name }}
                                                </h3>
                                                <div class="row">
                                                    <div v-for="(content, field_name) in record" class="col-md-3 col-sm-12">
                                                        <div class="form-group">
                                                            <div v-for="requirement_field in records.requirement.fields">
                                                                <div v-if="requirement_field.field_name == field_name">
                                                                    <label
                                                                            :for="'education_'+records.requirement.id"
                                                                            class="font-weight-light"
                                                                    >
                                                                        @{{ requirement_field.name }}
                                                                    </label>
                                                                    <div v-if="requirement_field.field_type == 'select'">
                                                                        <select
                                                                                class="form-control"
                                                                                :name="
                                                                            'requirements[education]['
                                                                            +records.requirement.id+
                                                                            '][replaced_index_'
                                                                            +index+
                                                                            ']['
                                                                            +requirement_field.field_name+
                                                                            ']'"
                                                                                :id="'education_'+records.requirement.id+'_'+requirement_field.field_name"
                                                                        >
                                                                            <option
                                                                                    v-for="option in records.requirement.options"
                                                                                    :value="option"
                                                                                    :selected="option == content"
                                                                            >
                                                                                @{{ option }}
                                                                            </option>
                                                                        </select>
                                                                    </div>
                                                                    <div v-else>
                                                                        <input
                                                                                class="form-control"
                                                                                :type="requirement_field.field_type"
                                                                                :name="
                                                                            'requirements[education]['
                                                                            +records.requirement.id+
                                                                            '][replaced_index_'
                                                                            +index+
                                                                            ']['
                                                                            +requirement_field.field_name+
                                                                            ']'"
                                                                                :id="'education_'+records.requirement.id+'_'+requirement_field.field_name"
                                                                                :value="requirement_field.field_type != 'file' ? content : ''"
                                                                        >
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else>
                                    <div class="row margin-t15">
                                        <div class="col-md-12">
                                            <p>Требования категории "образование" для этой должности отсутствуют.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="teacher" role="tabpanel" aria-labelledby="teacher-tab">
                                <div v-if="requirementsQI">
                                    <div class="row">
                                        <div v-for="records in requirementsQI" class="col-md-12 col-sm-12">
                                            <div v-for="(record, index) in JSON.parse(records.json_content)">
                                                <hr>
                                                <h3 class="text-uppercase text-monospace">
                                                    @{{ records.requirement.name }}
                                                </h3>
                                                <div class="row">
                                                    <div v-for="(content, field_name) in record" class="col-md-3 col-sm-12">
                                                        <div class="form-group">
                                                            <div v-for="requirement_field in records.requirement.fields">
                                                                <div v-if="requirement_field.field_name == field_name">
                                                                    <label
                                                                            :for="'education_'+records.requirement.id"
                                                                            class="font-weight-light"
                                                                    >
                                                                        @{{ requirement_field.name }}
                                                                    </label>
                                                                    <div v-if="requirement_field.field_type == 'select'">
                                                                        <select
                                                                                class="form-control"
                                                                                :name="
                                                                            'requirements[qualification_increase]['
                                                                            +records.requirement.id+
                                                                            '][replaced_index_'
                                                                            +index+
                                                                            ']['
                                                                            +requirement_field.field_name+
                                                                            ']'"
                                                                                :id="'qualification_'+records.requirement.id+'_'+requirement_field.field_name"
                                                                        >
                                                                            <option
                                                                                    v-for="option in records.requirement.options"
                                                                                    :value="option"
                                                                                    :selected="option == content"
                                                                            >
                                                                                @{{ option }}
                                                                            </option>
                                                                        </select>
                                                                    </div>
                                                                    <div v-else>
                                                                        <input
                                                                                class="form-control"
                                                                                :type="requirement_field.field_type"
                                                                                :name="
                                                                            'requirements[qualification_increase]['
                                                                            +records.requirement.id+
                                                                            '][replaced_index_'
                                                                            +index+
                                                                            ']['
                                                                            +requirement_field.field_name+
                                                                            ']'"
                                                                                :id="'qualification_'+records.requirement.id+'_'+requirement_field.field_name"
                                                                                :value="requirement_field.field_type != 'file' ? content : ''"
                                                                        >
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else>
                                    <div class="row margin-t15">
                                        <div class="col-md-12">
                                            <p>Требования категории "повышение квалификации" для этой должности отсутствуют.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="print_edition" role="tabpanel" aria-labelledby="print_edition-tab">
                                <div v-if="requirementsS">
                                    <div class="row">
                                        <div v-for="records in requirementsS" class="col-md-12 col-sm-12">
                                            <div v-for="(record, index) in JSON.parse(records.json_content)">
                                                <hr>
                                                <h3 class="text-uppercase text-monospace">
                                                    @{{ records.requirement.name }}
                                                </h3>
                                                <div class="row">
                                                    <div v-for="(content, field_name) in record" class="col-md-3 col-sm-12">
                                                        <div class="form-group">
                                                            <div v-for="requirement_field in records.requirement.fields">
                                                                <div v-if="requirement_field.field_name == field_name">
                                                                    <label
                                                                            :for="'education_'+records.requirement.id"
                                                                            class="font-weight-light"
                                                                    >
                                                                        @{{ requirement_field.name }}
                                                                    </label>
                                                                    <div v-if="requirement_field.field_type == 'select'">
                                                                        <select
                                                                                class="form-control"
                                                                                :name="
                                                                            'requirements[seniority]['
                                                                            +records.requirement.id+
                                                                            '][replaced_index_'
                                                                            +index+
                                                                            ']['
                                                                            +requirement_field.field_name+
                                                                            ']'"
                                                                                :id="'seniority_'+records.requirement.id+'_'+requirement_field.field_name"
                                                                        >
                                                                            <option
                                                                                    v-for="option in records.requirement.options"
                                                                                    :value="option"
                                                                                    :selected="option == content"
                                                                            >
                                                                                @{{ option }}
                                                                            </option>
                                                                        </select>
                                                                    </div>
                                                                    <div v-else>
                                                                        <input
                                                                                class="form-control"
                                                                                :type="requirement_field.field_type"
                                                                                :name="
                                                                            'requirements[seniority]['
                                                                            +records.requirement.id+
                                                                            '][replaced_index_'
                                                                            +index+
                                                                            ']['
                                                                            +requirement_field.field_name+
                                                                            ']'"
                                                                                :id="'seniority_'+records.requirement.id+'_'+requirement_field.field_name"
                                                                                :value="requirement_field.field_type != 'file' ? content : ''"
                                                                        >
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else>
                                    <div class="row margin-t15">
                                        <div class="col-md-12">
                                            <p>Требования категории "печатные издания" для этой должности отсутствуют.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="publications" role="tabpanel" aria-labelledby="publications-tab">
                                <div v-if="requirementsNIR">
                                    <div class="row">
                                        <div v-for="records in requirementsNIR" class="col-md-12 col-sm-12">
                                            <div v-for="(record, index) in JSON.parse(records.json_content)">
                                                <hr>
                                                <h3 class="text-uppercase text-monospace">
                                                    @{{ records.requirement.name }}
                                                </h3>
                                                <div class="row">
                                                    <div v-for="(content, field_name) in record" class="col-md-3 col-sm-12">
                                                        <div class="form-group">
                                                            <div v-for="requirement_field in records.requirement.fields">
                                                                <div v-if="requirement_field.field_name == field_name">
                                                                    <label
                                                                            :for="'education_'+records.requirement.id"
                                                                            class="font-weight-light"
                                                                    >
                                                                        @{{ requirement_field.name }}
                                                                    </label>
                                                                    <div v-if="requirement_field.field_type == 'select'">
                                                                        <select
                                                                                class="form-control"
                                                                                :name="
                                                                            'requirements[nir]['
                                                                            +records.requirement.id+
                                                                            '][replaced_index_'
                                                                            +index+
                                                                            ']['
                                                                            +requirement_field.field_name+
                                                                            ']'"
                                                                                :id="'nir_'+records.requirement.id+'_'+requirement_field.field_name"
                                                                        >
                                                                            <option
                                                                                    v-for="option in requirement_field.options"
                                                                                    :value="option"
                                                                                    :selected="option == content"
                                                                            >
                                                                                @{{ option }}
                                                                            </option>
                                                                        </select>
                                                                    </div>
                                                                    <div v-else>
                                                                        <input
                                                                                class="form-control"
                                                                                :type="requirement_field.field_type"
                                                                                :name="
                                                                            'requirements[nir]['
                                                                            +records.requirement.id+
                                                                            '][replaced_index_'
                                                                            +index+
                                                                            ']['
                                                                            +requirement_field.field_name+
                                                                            ']'"
                                                                                :id="'nir_'+records.requirement.id+'_'+requirement_field.field_name"
                                                                                :value="requirement_field.field_type != 'file' ? content : ''"
                                                                        >
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else>
                                    <div class="row margin-t15">
                                        <div class="col-md-12">
                                            <p>Требования категории "труды и публикации" для этой должности отсутствуют.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}

                <div class="row userPositionsBlockShow" id="userPositionInfo" style="display: none;">
                    <div class="col-md-12">
                        <h3>
                            Информация о должности
                            <a
                                    id="linkToUserPositionEditFields"
                                    href=""
                                    class="btn btn-success"
                                    data-toggle="tooltip"
                                    data-placement="top"
                                    title="Редактировать поля"
                            >
                                <i class="md md-edit"></i>
                            </a>
                        </h3>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Организация</label>
                                    <input type="text" class="form-control" name="organization" disabled="true" value="">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Тип расчёта зарплаты</label>
                                    <input type="text" class="form-control" name="payroll_type" disabled="true" value="">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Испытательный срок с</label>
                                    <input type="date" class="form-control btn-block" name="probation_from" disabled="true" value="">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Испытательный срок по</label>
                                    <input type="date" class="form-control btn-block" name="probation_to" disabled="true" value="">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Выберите График</label>
                                    <select class="form-control" name="schedule" disabled="true">
                                        @foreach($work_shedule as $shedule)
                                            <option value="{{ $shedule->id }}">{{ $shedule->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Выберите Тип Занятости</label>
                                    <select class="form-control" name="employment" disabled="true">
                                        <option value="{{ App\EmployeesUsersPosition::EMPLOYMENT_MAIN }}">
                                            {{ App\EmployeesUsersPosition::EMPLOYMENT_MAIN }}
                                        </option>
                                        <option value="{{ App\EmployeesUsersPosition::EMPLOYMENT_PART_TIME }}">
                                            {{ App\EmployeesUsersPosition::EMPLOYMENT_PART_TIME }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Выберите Форму Занятости</label>
                                    <select class="form-control" name="employment_form" disabled="true">
                                        <option value="{{ App\EmployeesUsersPosition::EMPLOYMENT_FORM_MAIN }}">
                                            {{ App\EmployeesUsersPosition::EMPLOYMENT_FORM_MAIN }}
                                        </option>
                                        <option value="{{ App\EmployeesUsersPosition::EMPLOYMENT_FORM_PART_TIME }}">
                                            {{ App\EmployeesUsersPosition::EMPLOYMENT_FORM_PART_TIME }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Ставка</label>
                                    <input type="text" class="form-control" name="price" disabled="true" value="">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Оклад</label>
                                    <input type="text" class="form-control" name="salary" disabled="true" value="">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Премия</label>
                                    <input type="text" class="form-control" name="premium" disabled="true" value="">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Надбавки</label>
                                    <select class="filter-select selectpicker" name="perks[]" disabled="true" multiple>
                                        @foreach($perks as $perk)
                                            <option value="{{ $perk->id }}">{{ $perk->name.' - '.$perk->value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <h3>
                            Соц. пакет сотрудника
                            <a
                                    href="{{ route('employees.user.social.package', ['id' => $id]) }}"
                                    class="btn btn-success"
                                    data-toggle="tooltip"
                                    data-placement="top"
                                    title="Редактировать поля"
                            >
                                <i class="md md-edit"></i>
                            </a>
                        </h3>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Бензин</label>
                                    <input
                                            type="text"
                                            class="form-control"
                                            disabled="true"
                                            value="{{ optional($socialPackage)->gas != '' ? $socialPackage->gas : 'Не установлено' }}"
                                    >
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Серт. Корзина</label>
                                    <input
                                            type="text"
                                            class="form-control"
                                            disabled="true"
                                            value="{{ optional($socialPackage)->basket != '' ? $socialPackage->basket : 'Не установлено' }}"
                                    >
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Серт. Медикаменты</label>
                                    <input
                                            type="text"
                                            class="form-control"
                                            disabled="true"
                                            value="{{ optional($socialPackage)->medicines != '' ? $socialPackage->medicines : 'Не установлено' }}"
                                    >
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Сотовая связь</label>
                                    <input
                                            type="text"
                                            class="form-control"
                                            disabled="true"
                                            value="{{ optional($socialPackage)->cellular != '' ? $socialPackage->cellular : 'Не установлено' }}"
                                    >
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Такси</label>
                                    <input
                                            type="text"
                                            class="form-control"
                                            disabled="true"
                                            value="{{ optional($socialPackage)->taxi != '' ? $socialPackage->taxi : 'Не установлено' }}"
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-1">
                                <div class="form-check">
                                    <input
                                            class="form-check-input"
                                            type="checkbox"
                                            disabled="true"
                                            name="food"
                                            {{ optional($socialPackage)->food ? 'checked' : '' }}
                                    >
                                    <label class="form-check-label" for="file">
                                        Питание
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @isset($isTeacher)
                    <div :class="vacancy ? '' : 'hide'" class="row">
                        <div class="col-md-12">
                            <h3>
                                Дисциплины преподавателя
                            </h3>
                            <div class="row">
                                <div class="form-group form-horizontal ">
                                    <div class="col-sm-6">
                                        <multiselect :multiple="true"
                                                     v-model="selectedDisciplines"
                                                     label="name"
                                                     class="padding-b10"
                                                     track-by="name"
                                                     :options="positionSectorDisciplines"
                                                     :close-on-select="false"
                                                     :clear-on-select="true"
                                                     :preserve-search="true"
                                                     :limit="1"
                                                     :preselect-first="true">
                                        </multiselect>
                                    </div>
                                    <div class="btn-group col-sm-5">
                                        <button v-on:click="addDisciplines" class="btn btn-info">Добавить дисциплины</button>
                                        <button v-on:click="saveDisciplinesChange" class="btn btn-success margin-l15">Сохранить </button>
                                    </div>
                                </div>
                            </div>
                            <form name="disciplines" class="row padding-15">
                                <table id="data-table-disciplines" class="table table-striped table-hover dt-responsive" cellspacing="0" width="100%">
                                    <thead>
                                    <tr>
                                        <th class="id">ID</th>
                                        <th class="name">Наименование</th>
                                        <th class="credits">Кредитность</th>
                                        <th class="lang">Язык</th>
                                        <th>Действие</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    {{-- TODO --}}
                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>
                @endisset
                @endsection

                @section('scripts')
                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
                    <script src="https://unpkg.com/vue-multiselect@2.1.0"></script>
                    <script type="text/javascript">
                        Vue.component('multiselect', window.VueMultiselect.default);

                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        var main = new Vue({
                            el: '#main',
                            data:{
                                clear: false,
                                requirementsPI:  {!! json_encode($requirements['personal_info'] ?? null) !!},
                                requirementsE:   {!! json_encode($requirements['education'] ?? null) !!},
                                requirementsQI:  {!! json_encode($requirements['qualification_increase'] ?? null) !!},
                                requirementsS: {!! json_encode($requirements['seniority'] ?? null) !!},
                                requirementsNIR:  {!! json_encode($requirements['nir'] ?? null) !!},
                                vacancy: null,
                                selectedDisciplines: [],
                                showPositionDisciplines: false,
                                disciplinesDataTable: null,
                                positionDepartmentId: null,
                                positionSectorDisciplines: [],
                            },
                            methods: {
                                deleteDiscipline(disciplineId){
                                    axios.post('{{route('employees.user.delete.discipline', ['id' => $id])}}', {disciplineId})
                                        .then(({data}) => {
                                            this.getSectorDisciplines()
                                            this.disciplinesDataTable.ajax.reload()
                                        })
                                },
                                saveDisciplinesChange(){
                                    const form = new FormData(document.forms.disciplines)

                                    axios.post('{{route('employees.user.save.disciplines', ['id' => $id])}}', form)
                                        .then(() => {
                                            Swal.fire(
                                                'Good job!',
                                                'Дисциплины были сохранены!',
                                                'success'
                                            )
                                            this.disciplinesDataTable.ajax.reload()
                                        })
                                        .catch(({data}) => {
                                            Swal.fire('Fail!', data, 'error')
                                        })
                                },
                                addDisciplines(){
                                    axios.post('{{route('employees.user.add.disciplines', ['id' => $id])}}', {
                                        disciplines: this.selectedDisciplines,
                                        vacancy: this.vacancy
                                    })
                                        .then( ({data}) => {
                                            this.getSectorDisciplines()
                                            this.disciplinesDataTable.ajax.reload()

                                            Swal.fire(
                                                'Good job!',
                                                'Дисциплины были добавлены!',
                                                'success'
                                            )
                                        })
                                        .catch(({data}) => {
                                            Swal.fire('Fail!', data, 'error')
                                        })
                                },
                                getSectorDisciplines(){
                                    axios.post('{{route('employees.user.disciplinesList', ['id' => $id , '']) }}/'+ this.positionDepartmentId)
                                        .then(({data}) => {
                                            this.positionSectorDisciplines = data
                                        })
                                },
                                initDisciplinesTable(){
                                    this.disciplinesDataTable = $('#data-table-disciplines').DataTable({
                                        "processing": true,
                                        "serverSide": true,
                                        drawCallback: function(){
                                            $('.selectpiker').selectpicker();
                                        },
                                        "ajax": {
                                            url: "{{ route('employees.user.disciplinesDataTable', ['id' => $id ]) }}",
                                            type: "post",
                                        },
                                        columns: [
                                            {
                                                data: 'discipline_id',
                                            },
                                            {
                                                data: 'name',
                                                orderable: false
                                            },
                                            {
                                                data: 'credits',
                                                orderable: false
                                            },
                                            {
                                                data: 'lang',
                                                orderable: false
                                            },
                                            {
                                                data: 'actions',
                                                orderable: false
                                            }
                                        ]
                                    });
                                    $('<input type="text" style="width: 100px" class="form-control"/>')
                                        .appendTo($("#data-table-disciplines thead th.id"))
                                        .on('change', function () {
                                            var val = $(this).val();

                                            main.disciplinesDataTable.column(0)
                                                .search(val)
                                                .draw();
                                        });
                                    $('<input type="text" style="width: 200px" class="form-control"/>')
                                        .appendTo($("#data-table-disciplines thead th.name"))
                                        .on('change', function () {
                                            var val = $(this).val();

                                            main.disciplinesDataTable.column(1)
                                                .search(val)
                                                .draw();
                                        });
                                    $('<input type="number" style="width: 100px" class="form-control"/>')
                                        .appendTo($("#data-table-disciplines thead th.credits"))
                                        .on('change', function () {
                                            var val = $(this).val();

                                            main.disciplinesDataTable.column(2)
                                                .search(val)
                                                .draw();
                                        });
                                    $(`<select class="form-control">
                                <option value="">По умолчанию</option>
                                <option value="kz">kz</option>
                                <option value="ru">ru</option>
                                <option value="en">en</option>
                            </select>`)
                                        .appendTo($("#data-table-disciplines thead th.lang"))
                                        .on('change', function () {
                                            var val = $(this).val();

                                            main.disciplinesDataTable.column(3)
                                                .search(val)
                                                .draw();
                                        });
                                },
                                onChangeSelectPosition(event) {
                                    if(this.vacancy !== '' && this.vacancy !== null){
                                        axios.post('{{route('employees.checkPositionDisciplines', '')}}/' + this.vacancy)
                                            .then( ({data}) => {
                                                this.showPositionDisciplines = data.position_is_discipline
                                                this.positionDepartmentId = data.department_id

                                                this.getSectorDisciplines()
                                            })
                                    } else {
                                        this.showPositionDisciplines = false
                                    }
                                    if(event.target.value != ''){
                                        const data = {};
                                        let pos_id = '';
                                        let url = '{{ route("employees.user.edit.position", ["user_id" => $id]) }}';
                                        data['vacancy_id'] = event.target.value;
                                        data['type'] = event.target.options[event.target.options.selectedIndex].dataset.type;
                                        data['user_id'] = {{ $id?? '' }}
                                        axios.post('{{ route('get.user.position.requirements') }}', data)
                                            .then(response => {
                                                console.log(response.data.requirements);
                                                if(response.data.requirements != 'empty' && response.data.requirements.personal_info.length > 0){
                                                    this.requirementsPI = response.data.requirements.personal_info;
                                                } else {
                                                    this.requirementsPI = false;
                                                }
                                                if(response.data.requirements != 'empty' && Object.keys(response.data.requirements.education).length > 0){
                                                    this.requirementsE = response.data.requirements.education;
                                                } else {
                                                    this.requirementsE = false;
                                                }
                                                if(response.data.requirements != 'empty' && Object.keys(response.data.requirements.qualification_increase).length > 0){
                                                    this.requirementsQI = response.data.requirements.qualification_increase;
                                                } else {
                                                    this.requirementsQI = false;
                                                }
                                                if(response.data.requirements != 'empty' && Object.keys(response.data.requirements.seniority).length > 0){
                                                    this.requirementsS = response.data.requirements.seniority;
                                                } else {
                                                    this.requirementsS = false;
                                                }
                                                if(response.data.requirements != 'empty' && Object.keys(response.data.requirements.nir).length > 0){
                                                    this.requirementsNIR = response.data.requirements.nir;
                                                } else {
                                                    this.requirementsNIR = false;
                                                }

                                                $.each(response.data.userPosition, function(index, value){
                                                    $('#userPositionInfo [name="'+index+'"]').val(value);
                                                });

                                                $('.selectpicker').selectpicker('val', response.data.perks);

                                                $('.userPositionsBlockShow').show();
                                                pos_id = '/'+response.data.position_id;
                                                $('#linkToUserPositionEditFields').attr('href', url+pos_id);
                                            });
                                    } else {
                                        $('.userPositionsBlockShow').hide();
                                    }
                                },
                            },
                            mounted(){
                                @isset($isTeacher)
                                    this.initDisciplinesTable()
                                @endisset
                            }
                        });

                        $(document).ready(function() {
                            $(window).keydown(function(event){
                                if(event.keyCode == 13) {
                                    event.preventDefault();
                                    return false;
                                }
                            });

                            $('#quiz-tab a[role=tab]').click(function (e) {
                                e.preventDefault();
                                $(this).tab('show');
                            });

                            $('.selectpicker').selectpicker();
                            $('[data-toggle="tooltip"]').tooltip();
                        });

                    </script>
@endsection