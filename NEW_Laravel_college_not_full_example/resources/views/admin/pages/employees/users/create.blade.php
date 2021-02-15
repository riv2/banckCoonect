@extends("admin.admin_app")

@section("content")
    <div id="main">
        {!! Form::open([
            'url' => route('user.create.employees') 
        ]) !!}
            <div class="row margin-t25">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Выберите вакансию</label>
                        <select class="form-control" name="vacancy_id" @change="onChangeSelect($event)" required>
                            <option></option>
                            @foreach($vacancies as $vacancy)
                                @if($vacancy->position)
                                    <option value="{{ $vacancy->id }}">{{ $vacancy->position->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3 col-md-offset-6 text-right">
                    <button type="submit" class="btn btn-primary btn-lg margin-t15">Сохранить</button>
                </div>
            </div>
            <div class="row margin-top searchUsersBlock">
                <div class="col-md-3">
                    <label class="demo-label">Выберите пользователя:</label><br/>
                    <input type="text" name="txtUser" id="txtUser" class="form-control" autocomplete="off" required>
                    <div class="pos-relative full-width">
                        <div id="searchData" class="shadow" style="display: none;"></div>
                    </div>
                    <input type="hidden" name="user_id" value="">
                </div>
            </div>

            <div id="quiz-tab" class="margin-top">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li role="presentation" class="nav-item active">
                        <a class="nav-link" id="personal_data-tab" data-toggle="tab" href="#personal_data" role="tab" aria-controls="personal_data" aria-selected="true">Персональные Данные</a>
                    </li>
                    <li role="presentation" class="nav-item">
                        <a class="nav-link" id="education-tab" data-toggle="tab" href="#education" role="tab" aria-controls="education" aria-selected="false">Образование</a>
                    </li>
                    <li role="presentation" class="nav-item">
                        <a class="nav-link" id="teacher-tab" data-toggle="tab" href="#teacher" role="tab" aria-controls="teacher" aria-selected="false">НИР</a>
                    </li>
                    <li role="presentation" class="nav-item">
                        <a class="nav-link" id="print_edition-tab" data-toggle="tab" href="#print_edition" role="tab" aria-controls="print_edition" aria-selected="false">Трудовой стаж</a>
                    </li>
                    <li role="presentation" class="nav-item">
                        <a class="nav-link" id="publications-tab" data-toggle="tab" href="#publications" role="tab" aria-controls="publications" aria-selected="false">Повышение квалификации</a>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane active" id="personal_data" role="tabpanel" aria-labelledby="personal_data-tab">
                        <div v-if="requirementsPI">
                            <div class="row">
                                <div v-for="records in requirementsPI" class="col-md-3 col-sm-12">
                                    <div v-for="item in records">
                                        <div class="form-group">
                                            <label :for="'personal_info_'+item.id" class="font-weight-light">
                                                @{{ item.name }}
                                            </label>
                                            <input 
                                                class="form-control"
                                                :class="
                                                    item.field_name == 'fio_ru' || item.field_name == 'fio_kz' || item.field_name == 'fio_en' ? 'fio' : 
                                                    item.field_name == 'bdate' ? 'bdate' : 
                                                    item.field_name == 'family_status' ? 'family_status' : 
                                                    item.field_name == 'iin' ? 'iin' : 
                                                    item.field_name == 'nationality_id' ? 'nationality_id' : 
                                                    item.field_name == 'mobile_phone' ? 'mobile_phone' : 
                                                    item.field_name == 'sex' ? 'sex' : 
                                                    item.field_name == 'email' ? 'email' : '' 
                                                " 
                                                :type="item.field_type" 
                                                :name="'requirements[personal_info]['+item.id+']'" 
                                                :id="'personal_info_'+item.id" 
                                                :value="
                                                    oldRequirements !== null && oldRequirements[item.field_name] !== null ? 
                                                    item.file_type == 'file' ? '' : oldRequirements[item.field_name] : 
                                                    '' 
                                                "
                                            >
                                        </div>
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
                                <div v-for="records in requirementsE" class="col-md-12 col-sm-12 blockRequirement">
                                    <div v-for="(record, index) in records">
                                        <hr>
                                        <h3 class="text-uppercase text-monospace">
                                            @{{ record.name }} 
                                            <div 
                                                class="plus alt addAnotherRequirement" 
                                                data-toggle="tooltip" 
                                                data-placement="top" 
                                                title="Дублировать раздел"
                                                :data-requirement-id="record.id"
                                                data-requirement-category="requirementsE"
                                                
                                            ></div>
                                        </h3>
                                        <div class="row">
                                            <div v-for="item in record.fields" class="col-md-3 col-sm-12">
                                                <div class="form-group">
                                                    <label :for="'personal_info_'+item.id" class="font-weight-light">
                                                        @{{ item.name }}
                                                    </label>
                                                    <div v-if="item.field_type == 'select'">
                                                        <select class="form-control">
                                                            <div 
                                                                v-for="option in item.options" 
                                                                :name="'requirements[education]['+record.id+'][replaced_index_'+index+']['+item.field_name+']'" 
                                                                :id="'education_'+item.id" 
                                                            >
                                                                <option :value="option">@{{ option }}</option>
                                                            </div>
                                                        </select>
                                                    </div>
                                                    <div v-else>
                                                        <input 
                                                            class="form-control" 
                                                            :type="item.field_type" 
                                                            :name="'requirements[education]['+record.id+'][replaced_index_'+index+']['+item.field_name+']'" 
                                                            :id="'education_'+item.id" 
                                                            :value="
                                                                oldRequirements !== null && oldRequirements[item.field_name] !== null ? 
                                                                item.file_type == 'file' ? 'file' : oldRequirements[item.field_name] : 
                                                                '' 
                                                            "
                                                        >
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
                        <div v-if="requirementsNIR">
                            <div class="row">
                                <div v-for="records in requirementsNIR" class="col-md-12 col-sm-12 blockRequirement">
                                    <div v-for="(record, index) in records">
                                        <hr>
                                        <h3 class="text-uppercase text-monospace">
                                            @{{ record.name }} 
                                            <div 
                                                class="plus alt addAnotherRequirement" 
                                                data-toggle="tooltip" 
                                                data-placement="top" 
                                                title="Дублировать раздел"
                                                :data-requirement-id="record.id"
                                                data-requirement-category="requirementsNIR"
                                            ></div>
                                        </h3>
                                        <div class="row">
                                            <div v-for="item in record.fields" class="col-md-3 col-sm-12">
                                                <div class="form-group">
                                                    <label :for="'personal_info_'+item.id" class="font-weight-light">
                                                        @{{ item.name }}
                                                    </label>
                                                    <div v-if="item.field_type == 'select'">
                                                        <select 
                                                            class="form-control" 
                                                            :name="'requirements[nir]['+record.id+'][replaced_index_'+index+']['+item.field_name+']'"
                                                        >
                                                            <option 
                                                                v-for="option in item.options" 
                                                                :value="option"
                                                                :id="'education_'+item.id"
                                                            >
                                                                @{{ option }}
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <div v-else>
                                                        <input 
                                                            class="form-control" 
                                                            :type="item.field_type" 
                                                            :name="'requirements[nir]['+record.id+'][replaced_index_'+index+']['+item.field_name+']'" 
                                                            :id="'nir_'+item.id" 
                                                            :value="
                                                                oldRequirements !== null && oldRequirements[item.field_name] !== null ? 
                                                                item.file_type == 'file' ? '' : oldRequirements[item.field_name] : 
                                                                '' 
                                                            "
                                                        >
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
                                    <p>Требования категории "нир" для этой должности отсутствуют.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="print_edition" role="tabpanel" aria-labelledby="print_edition-tab">
                        <div v-if="requirementsS">
                            <div class="row">
                                <div v-for="records in requirementsS" class="col-md-12 col-sm-12 blockRequirement">
                                    <div v-for="(record, index) in records">
                                        <hr>
                                        <h3 class="text-uppercase text-monospace">
                                            @{{ record.name }} 
                                            <div 
                                                class="plus alt addAnotherRequirement" 
                                                data-toggle="tooltip" 
                                                data-placement="top" 
                                                title="Дублировать раздел"
                                                :data-requirement-id="record.id"
                                                data-requirement-category="requirementsS"
                                            ></div>
                                        </h3>
                                        <div class="row">
                                            <div v-for="item in record.fields" class="col-md-3 col-sm-12">
                                                <div class="form-group">
                                                    <label :for="'personal_info_'+item.id" class="font-weight-light">
                                                        @{{ item.name }}
                                                    </label>
                                                    <div v-if="item.field_type == 'select'">
                                                        <select 
                                                            class="form-control" 
                                                            :name="'requirements[seniority]['+record.id+'][replaced_index_'+index+']['+item.field_name+']'"
                                                        >
                                                            <option 
                                                                v-for="option in item.options" 
                                                                :value="option"
                                                                :id="'education_'+item.id"
                                                            >
                                                                @{{ option }}
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <div v-else>
                                                        <input 
                                                            class="form-control" 
                                                            :type="item.field_type" 
                                                            :name="'requirements[seniority]['+record.id+'][replaced_index_'+index+']['+item.field_name+']'" 
                                                            :id="'seniority_'+item.id" 
                                                            :value="
                                                                oldRequirements !== null && oldRequirements[item.field_name] !== null ? 
                                                                item.file_type == 'file' ? '' : oldRequirements[item.field_name] : 
                                                                '' 
                                                            "
                                                        >
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
                                    <p>Требования категории "трудовой стаж" для этой должности отсутствуют.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="publications" role="tabpanel" aria-labelledby="publications-tab">
                        <div v-if="requirementsQI">
                            <div class="row">
                                <div v-for="records in requirementsQI" class="col-md-12 col-sm-12 blockRequirement">
                                    <div v-for="(record, index) in records">
                                        <hr>
                                        <h3 class="text-uppercase text-monospace">
                                            @{{ record.name }} 
                                            <div 
                                                class="plus alt addAnotherRequirement" 
                                                data-toggle="tooltip" 
                                                data-placement="top" 
                                                title="Дублировать раздел"
                                                :data-requirement-id="record.id"
                                                data-requirement-category="requirementsQI"
                                            ></div>
                                        </h3>
                                        <div class="row">
                                            <div v-for="item in record.fields" class="col-md-3 col-sm-12">
                                                <div class="form-group">
                                                    <label :for="'personal_info_'+item.id" class="font-weight-light">
                                                        @{{ item.name }}
                                                    </label>
                                                    <div v-if="item.field_type == 'select'">
                                                        <select 
                                                            class="form-control" 
                                                            :name="'requirements[qualification_increase]['+record.id+'][replaced_index_'+index+']['+item.field_name+']'"
                                                        >
                                                            <option 
                                                                v-for="option in item.options" 
                                                                :value="option"
                                                                :id="'education_'+item.id"
                                                            >
                                                                @{{ option }}
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <div v-else>
                                                        <input 
                                                            class="form-control" 
                                                            :type="item.field_type" 
                                                            :name="'requirements[qualification_increase]['+record.id+'][replaced_index_'+index+']['+item.field_name+']'" 
                                                            :id="'qualification_increase_'+item.id" 
                                                            :value="
                                                                oldRequirements !== null && oldRequirements[item.field_name] !== null ? 
                                                                item.file_type == 'file' ? '' : oldRequirements[item.field_name] : 
                                                                '' 
                                                            "
                                                        >
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
                </div>
            </div>
        {!! Form::close() !!}
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script type="text/javascript">
    var main = new Vue({
        el: '#main',
        data:{
            requirementsPI:  {!! json_encode($requirements['personal_info'] ?? null) !!},
            requirementsE:   {!! json_encode($requirements['education'] ?? null) !!},
            requirementsQI:  {!! json_encode($requirements['qualification_increase'] ?? null) !!},
            requirementsS: {!! json_encode($requirements['seniority'] ?? null) !!},
            requirementsNIR:  {!! json_encode($requirements['nir'] ?? null) !!},
            oldRequirements: {!! json_encode(old('requirements')) !!}
        },
        methods: {
            onChangeSelect(event) {
                if(event.target.value != ''){
                    const data = {};
                    data['vacancy_id'] = event.target.value;
                    axios.post('{{ route('get.position.requirements') }}', data)
                        .then(response => {
                            if(response.data.requirements.personal_info != undefined){
                                this.requirementsPI = response.data.requirements.personal_info;
                            } else {
                                this.requirementsPI = null;
                            }
                            if(response.data.requirements.education != undefined){
                                this.requirementsE = response.data.requirements.education;
                            } else {
                                this.requirementsE = null;
                            }
                            if(response.data.requirements.qualification_increase != undefined){
                                this.requirementsQI = response.data.requirements.qualification_increase;
                            } else {
                                this.requirementsQI = null;
                            }
                            if(response.data.requirements.seniority != undefined){
                                this.requirementsS = response.data.requirements.seniority;
                            } else {
                                this.requirementsS = null;
                            }
                            if(response.data.requirements.nir != undefined){
                                this.requirementsNIR = response.data.requirements.nir;
                            } else {
                                this.requirementsNIR = null;
                            }
                        });
                }
            }
        }
    });

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#main').on('click', '.addAnotherRequirement', function(){
            var seconds = new Date().getTime() / 1000;
            var currentBlock = $(this).closest('.blockRequirement').html();
            var res = currentBlock.split("replaced_index_").join("replaced_index_" + Math.round(seconds));
            $(this).closest('.blockRequirement').after('<div class="col-md-12 col-sm-12 blockRequirement">'+res+'</div>');
        });

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


        $('#txtUser').change(function(){
            $('#searchData').html('<div class="loader">Loading...</div>');
            $('#searchData').show();
            var query = $(this).val();
            if(query.length >= 3){
               $.ajax({
                    url: "{{ route('usersSearchUser') }}",
                    data: 'query=' + query, 
                    type: "POST", 
                    success: data => {
                        $('#searchData').html(data);
                    },
                    complete: data =>{
                        $('#searchData .loader').remove();
                        if( $('#searchData').is(':empty') ){
                            $('#searchData').hide();
                        }
                    }
                });
            }else{
                $('#searchData .loader').remove();
                $('#searchData').hide();
            }
        });

        $('.searchUsersBlock').on('click', '.searchEmail', function(){
            const data = {};
            data['email'] = $(this).html();

            axios.post('{{ route('usersGetData') }}', data)
                .then(response => { 
                    $('#searchData').html('');
                    $('#searchData').hide();
                    $('#quiz-tab input').val('');

                    if(response.data.status != 'empty'){
                        if(response.data.profile != null){
                            var date = new Date(response.data.profile.bdate);
                            $('input.bdate')
                                .val(date.getFullYear()+'-'+("0" + (date.getMonth() + 1)).slice(-2)+'-'+date.getDate());
                            $('input.fio').val(response.data.user.name);
                            $('input.family_status').val(response.data.profile.family_status);
                            $('input.iin').val(response.data.profile.iin);
                            $('input.nationality_id').val(response.data.profile.nationality_id);
                            $('input.sex').val(response.data.profile.sex == 0 ? 'Ж' : 'М');
                            $('input.mobile_phone').val(response.data.profile.mobile);
                        }
                        $('#txtUser').val(data['email']);
                        $('input.email').val(data['email']);
                        $('input[name="user_id"]').val(response.data.user.id);
                    }
                });
        });
    });

</script>
@endsection