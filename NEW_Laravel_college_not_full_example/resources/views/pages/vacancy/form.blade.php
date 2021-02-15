@extends('layouts.app')

@section('title', __('Vacancies'))

@section('content')
    <section class="content" id="main-test-form">
        <div class="container-fluid">
            <div class="p-3 mb-2 bg-info">
                <h2 class="text-white no-margin">@lang('Fill out a resume')</h2>
            </div>

            {!! Form::open([
                'url' => route('submit.vacancy.form'),
                'enctype' => 'multipart/form-data'
            ]) !!}
                <input type="hidden" name="vacancy_id" value="{{ $vacancy->id }}">
                <div class="row margin-t5">
                    <div class="col-md-12">
                        <div class="card shadow">
                            @if(count($requirements['personal_info']) > 0)
                                <h2>Персональные данные</h2>
                                <div class="row">
                                    <div v-for="records in requirementsPI" class="col-md-3 col-sm-12">
                                        <div v-for="item in records">
                                            <div class="form-group">
                                                <label :for="'personal_info_'+item.id" class="font-weight-light">
                                                    @{{ item.name }}
                                                </label>
                                                <div v-if="item.field_name == 'citizenship' || item.field_name == 'nationality'">
                                                    <select 
                                                        class="form-control" 
                                                        :name="'requirements[personal_info]['+item.id+']'" 
                                                        :id="'personal_info_'+item.id" 
                                                    >
                                                        <option 
                                                            v-for="manual in item.field_name == 'citizenships'? citizenships : nationalities" 
                                                            :value="manual.name" 
                                                        >
                                                            @{{ manual.name }}
                                                        </option>
                                                    </select>
                                                </div>
                                                <div v-else>
                                                    <div v-if="item.field_type == 'select'">
                                                        <select 
                                                            class="form-control" 
                                                            :name="'requirements[personal_info]['+item.id+']'" 
                                                            :id="'personal_info_'+item.id" 
                                                        >

                                                            <option 
                                                                v-for="option in item.options" 
                                                                :value="option" 
                                                            >
                                                                @{{ option }}
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <div v-else>
                                                        <input 
                                                            class="form-control" 
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
                                </div>
                            @endif
                            @if(isset($requirements['education']) && count($requirements['education']) > 0)
                                <h2>Образование</h2>
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
                                                            <select 
                                                                class="form-control"
                                                                :name="
                                                                    'requirements[education]['
                                                                    +record.id+
                                                                    '][replaced_index_'
                                                                    +index+
                                                                    ']['
                                                                    +item.field_name+
                                                                    ']'
                                                                " 
                                                                :id="'education_'+item.id" 
                                                            >
                                                                <div 
                                                                    v-for="option in item.options" 
                                                                    
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
                                    <div id="requirementsE"></div>
                                </div>
                            @endif
                            @if(isset($requirements['nir']) && count($requirements['nir']) > 0)
                                <h2>НИР</h2>
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
                                                                :id="'education_'+item.id"
                                                            >
                                                                <option 
                                                                    v-for="option in item.options" 
                                                                    :value="option"
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
                                    <div id="requirementsNIR"></div>
                                </div>
                            @endif
                            @if(isset($requirements['seniority']) && count($requirements['seniority']) > 0)
                                <h2>Трудовой стаж</h2>
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
                                                                :id="'education_'+item.id"
                                                            >
                                                                <option 
                                                                    v-for="option in item.options" 
                                                                    :value="option"
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
                                    <div id="requirementsS"></div>
                                </div>
                            @endif
                            @if(isset($requirements['qualification_increase']) && count($requirements['qualification_increase']) > 0)
                                <h2>Повышение квалификации</h2>
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
                                                                :id="'education_'+item.id"
                                                            >
                                                                <option 
                                                                    v-for="option in item.options" 
                                                                    :value="option"
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
                                    <div id="requirementsQI"></div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12 text-right margin-b10">
                        <button type="submit" class="btn btn-lg btn-primary btn-md">
                            @lang('Send')
                        </button>
                    </div> 
                </div>
            {!! Form::close() !!}
        </div>
    </section>
@endsection

@section('scripts')
    <script type="text/javascript">
        var main = new Vue({
            el: '#main-test-form',
            data: {
                requirementsPI:  {!! json_encode($requirements['personal_info']) !!},
                requirementsE:   {!! json_encode($requirements['education']) !!},
                requirementsQI:  {!! json_encode($requirements['qualification_increase']) !!},
                requirementsS:   {!! json_encode($requirements['seniority']) !!},
                requirementsNIR: {!! json_encode($requirements['nir']) !!},
                oldRequirements: {!! json_encode(old('requirements')) !!},
                citizenships: {!! json_encode($citizenships) !!},
                nationalities: {!! json_encode($nationalities) !!}
            },
            methods: {
                //
            }
        });
        $(document).ready(function(){
            $('#main-test-form').on('click', '.addAnotherRequirement', function(){
                var seconds = new Date().getTime() / 1000;
                var currentBlock = $(this).closest('.blockRequirement').html();
                var res = currentBlock.split("replaced_index_").join("replaced_index_" + Math.round(seconds));
                $(this).closest('.blockRequirement').after('<div class="col-md-12 col-sm-12 blockRequirement">'+res+'</div>');
            });
            $('[data-toggle="tooltip"]').tooltip()
            $('.custom-file-input').on('change', function() { 
                let fileName = $(this).val().split('\\').pop(); 
                $(this).next('.custom-file-label').addClass("selected").html(fileName); 
            });
        });
    </script>
@endsection
