@extends("admin.admin_app")

@section("content")
    <div id="main">
        @if(Session::has('requirements_success_add'))
            <div class="margin-top alert alert-success" role="alert">
                {{ Session::get('requirements_success_add') }}
            </div>
        @endif
        <div class="page-header">
            <div class="row">
                <div class="col-md-9">
                    <h2>Требования для должности: {{ $position->name }}</h2>
                </div> 
                <div class="col-md-3 text-right">
                    <a href="{{ route('employees.add.new.requirement.page') }}" class="margin-top btn btn-primary btn-lg">
                        Создать новое требование
                    </a>
                </div>
            </div>
        </div>
        
        {!! Form::open([
            'url' => route('employees.link.position.requirements')
        ]) !!}
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary btn-md margin-t10 margin-b10">
                        Добавить требования к должности
                    </button>
                    <div class="form-check margin-t10 pull-right">
                        <input class="form-check-input" type="checkbox" name="selectAll" @change="check($event)">
                        <label class="form-check-label" for="selectAll">
                            Выделить всё
                        </label>
                    </div>
                </div>
            </div>
            <div class="row row-flex margin-t5">
                <div class="col col-md-4">
                    <div class="card shadow h-100">
                        <input type="hidden" name="position_id" value="{{ $position->id }}">
                        <div class="row">
                            <div class="col-md-12">
                                <h4>Персональные данные</h4>
                            </div>
                        </div>

                        <div v-for="item in requirementsPI" class="form-check">
                            <input 
                                class="form-check-input" 
                                type="checkbox" 
                                name="requirements[]" 
                                :value="item.id" 
                                :id="'personal_info_'+item.id" 
                                :checked="IDs.includes(item.id) ? true : false" 
                            >
                            <label class="form-check-label" :for="'personal_info_'+item.id">
                                @{{ item.name }}
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col col-md-8">
                    <div class="card shadow">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-7">
                                        <h4>НИР</h4>
                                    </div>
                                </div>

                                <div v-for="item in requirementsPE" class="form-check">
                                    <input 
                                        class="form-check-input" 
                                        type="checkbox" 
                                        name="requirements[]" 
                                        :value="item.id" 
                                        :id="'personal_info_'+item.id" 
                                        :checked="IDs.includes(item.id) ? true : false" 
                                    >
                                    <label class="form-check-label" :for="'personal_info_'+item.id">
                                        @{{ item.name }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>Трудовой стаж</h4>
                                    </div>
                                </div>

                                <div v-for="item in requirementsPaP" class="form-check">
                                    <input 
                                        class="form-check-input" 
                                        type="checkbox" 
                                        name="requirements[]" 
                                        :value="item.id" 
                                        :id="'personal_info_'+item.id" 
                                        :checked="IDs.includes(item.id) ? true : false" 
                                    >
                                    <label class="form-check-label" :for="'personal_info_'+item.id">
                                        @{{ item.name }}
                                    </label>
                                </div>
                                <div class="row margin-t20">
                                    <div class="col-md-12">
                                        <h4>Повышение квалификации</h4>
                                    </div>
                                </div>

                                <div v-for="item in requirementsQI" class="form-check">
                                    <input 
                                        class="form-check-input" 
                                        type="checkbox" 
                                        name="requirements[]" 
                                        :value="item.id" 
                                        :id="'personal_info_'+item.id" 
                                        :checked="IDs.includes(item.id) ? true : false" 
                                    >
                                    <label class="form-check-label" :for="'personal_info_'+item.id">
                                        @{{ item.name }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card shadow margin-t15">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>Образование</h4>
                                    </div>
                                </div>

                                <div v-for="item in requirementsE" class="form-check">
                                    <input 
                                        class="form-check-input" 
                                        type="checkbox" 
                                        name="requirements[]" 
                                        :value="item.id" 
                                        :id="'personal_info_'+item.id" 
                                        :checked="IDs.includes(item.id) ? true : false" 
                                    >
                                    <label class="form-check-label" :for="'personal_info_'+item.id">
                                        @{{ item.name }}
                                    </label>
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
<script type="text/javascript">
    var main = new Vue({
        el: '#main',
        data: {
            IDs:             {!! json_encode($positionRequirementsIDs) !!},
            requirementsPI:  {!! json_encode($requirements['personal_info']) !!},
            requirementsE:   {!! json_encode($requirements['education']) !!},
            requirementsQI:  {!! json_encode($requirements['qualification_increase']) !!},
            requirementsPaP: {!! json_encode($requirements['seniority']) !!},
            requirementsPE:  {!! json_encode($requirements['nir']) !!}
        },
        methods: {
            check: function(event){
                let allIDs = [
                    ...this.requirementsPI, 
                    ...this.requirementsE, 
                    ...this.requirementsQI, 
                    ...this.requirementsPaP, 
                    ...this.requirementsPE
                ];
                let list = [];

                if(event.target.checked){
                    $.each(allIDs, function(key, value) {
                        list.push(value.id);
                    });
                }

                this.IDs = list;
            }
        }
    });
    $(document).ready(function() {
        $('#quiz-tab a[role=tab]').click(function (e) {
            e.preventDefault();
            $(this).tab('show');
        });
    });
</script>
@endsection