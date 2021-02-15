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
                <div class="row row-flex margin-t5">
                    @if(count($requirements['personal_info']) > 0)
                        <div class="col-md-4 col-sm-12 margin-t15">
                            <div class="card shadow h-100">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h3>Персональные данные</h3>
                                    </div>
                                </div>

                                <div v-for="item in requirementsPI" class="form-group">
                                    <label :for="'personal_info_'+item.id">
                                        @{{ item.name }}
                                    </label>
                                    <input 
                                        class="form-control" 
                                        :type="item.field_type" 
                                        :name="'requirements['+item.field_name+']'" 
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
                    @endif

                    @if(isset($requirements['education']) && count($requirements['education']) > 0)
                        <div class="col-md-4 col-sm-12 margin-t15">
                            <div class="card shadow h-100">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>Образование</h4>
                                    </div>
                                </div>

                                <div v-for="item in requirementsE" class="form-group">
                                    <label :for="'personal_info_'+item.id">
                                        @{{ item.name }}
                                    </label>
                                    <input 
                                        class="form-control" 
                                        :type="item.field_type" 
                                        :name="'requirements['+item.field_name+']'" 
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
                    @endif

                    @if(isset($requirements['print_edition']) && count($requirements['print_edition']) > 0)
                        <div class="col-md-4 col-sm-12 margin-t15">
                            <div class="card shadow h-100">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>Печатные издания</h4>
                                    </div>
                                </div>

                                <div v-for="item in requirementsPE" class="form-group">
                                    <label :for="'personal_info_'+item.id">
                                        @{{ item.name }}
                                    </label>
                                    <input 
                                        class="form-control" 
                                        :type="item.field_type" 
                                        :name="'requirements['+item.field_name+']'" 
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
                    @endif
                </div>

                <div class="row row-flex margin-t15">
                    @if(isset($requirements['proceedings_and_publications']) && count($requirements['proceedings_and_publications']) > 0)
                        <div class="col-md-4 col-sm-12">
                            <div class="card shadow h-100">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>Труды и публикации</h4>
                                    </div>
                                </div>

                                <div v-for="item in requirementsPaP" class="form-group">
                                    <label :for="'personal_info_'+item.id">
                                        @{{ item.name }}
                                    </label>
                                    <input 
                                        class="form-control" 
                                        :type="item.field_type" 
                                        :name="'requirements['+item.field_name+']'" 
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
                    @endif

                    @if(isset($requirements['qualification_increase']) && count($requirements['qualification_increase']) > 0)
                        <div class="col-md-4 col-sm-12 margin-t15">
                            <div class="card shadow h-100">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>Повышение квалификации</h4>
                                    </div>
                                </div>

                                <div v-for="item in requirementsQI" class="form-group">
                                    <label :for="'personal_info_'+item.id">
                                        @{{ item.name }}
                                    </label>
                                    <input 
                                        class="form-control" 
                                        :type="item.field_type" 
                                        :name="'requirements['+item.field_name+']'" 
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
                    @endif
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
                requirementsE:   {!! isset($requirements['education']) ? json_encode($requirements['education']) : '{}' !!},
                requirementsQI:  {!! isset($requirements['qualification_increase']) ? json_encode($requirements['qualification_increase']) : '{}' !!},
                requirementsPaP: {!! isset($requirements['proceedings_and_publications']) ? json_encode($requirements['proceedings_and_publications']) : '{}' !!},
                requirementsPE:  {!! isset($requirements['print_edition']) ? json_encode($requirements['print_edition']) : '{}' !!},
                oldRequirements: {!! json_encode(old('requirements')) !!}
            },
            methods: {
                
            }
        });
        $(document).ready(function(){
            $('.custom-file-input').on('change', function() { 
                let fileName = $(this).val().split('\\').pop(); 
                $(this).next('.custom-file-label').addClass("selected").html(fileName); 
            });
        });
    </script>
@endsection
