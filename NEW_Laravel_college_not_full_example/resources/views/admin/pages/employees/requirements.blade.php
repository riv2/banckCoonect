@extends("admin.admin_app")

@section("content")
    <div id="main">
        @if(Session::has('requirement_success_add'))
            <div class="margin-top alert alert-success" role="alert">
                {{ Session::get('requirement_success_add') }}
            </div>
        @endif
        <div class="page-header">
            <div class="row">
                <div class="col-md-9">
                    <h2>Создание нового требования</h2>
                </div> 
            </div>
            <a href="{{ route('employeesPosition') }}" class="btn btn-default-light btn-xs">
                <i class="md md-backspace"></i> Назад к должностям
            </a>
        </div>
        
        {!! Form::open([
            'id' => 'createRecordsForm'
        ]) !!}
            <div class="row">
                <div class="col-md-6">
                    <div class="accordion" id="accordionExample">
                        <div class="card">
                            <div 
                                class="card-header" 
                                id="headingOne" 
                                data-toggle="collapse" 
                                data-target="#collapseOne" 
                                aria-expanded="true" 
                                aria-controls="collapseOne"
                            >
                                <h4 class="mb-0">
                                    Персональная информация
                                </h4>
                            </div>

                            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
                                <div class="card-body">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="category" id="exampleRadios1" value="personal_info">
                                        <label class="form-check-label" for="exampleRadios1">
                                            Добавить поле в блок "Персональная информация"
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div 
                                class="card-header" 
                                id="headingTwo" 
                                data-toggle="collapse" 
                                data-target="#collapseTwo" 
                                aria-expanded="false" 
                                aria-controls="collapseTwo"
                            >
                                <h4 class="mb-0">
                                    Образование
                                    <div 
                                        class="plus alt addAnotherRequirement" 
                                        data-toggle="tooltip" 
                                        data-placement="top" 
                                        title="Добавить раздел" 
                                        data-category-name="education"
                                    ></div>
                                </h4>
                            </div>
                            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
                                <div class="card-body">
                                    @foreach($requirements['education'] as $requirement)
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="category" id="exampleRadios{{ $requirement['id'] }}" value="{{ $requirement['id'] }}">
                                            <label class="form-check-label" for="exampleRadios{{ $requirement['id'] }}">
                                                Добавить поле в раздел "{{ $requirement['name'] }}"
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div 
                                class="card-header" 
                                id="headingThree" 
                                data-toggle="collapse" 
                                data-target="#collapseThree" 
                                aria-expanded="false" 
                                aria-controls="collapseThree"
                            >
                                <h4 class="mb-0">
                                    НИР
                                    <div 
                                        class="plus alt addAnotherRequirement" 
                                        data-toggle="tooltip" 
                                        data-placement="top" 
                                        title="Добавить раздел" 
                                        data-category-name="nir"
                                    ></div>
                                </h4>
                            </div>
                            <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
                                <div class="card-body">
                                    @foreach($requirements['nir'] as $requirement)
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="category" id="exampleRadios{{ $requirement['id'] }}" value="{{ $requirement['id'] }}">
                                            <label class="form-check-label" for="exampleRadios{{ $requirement['id'] }}">
                                                Добавить поле в раздел "{{ $requirement['name'] }}"
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div 
                                class="card-header" 
                                id="headingFour" 
                                data-toggle="collapse" 
                                data-target="#collapseFour" 
                                aria-expanded="false" 
                                aria-controls="collapseFour"
                            >
                                <h4 class="mb-0">
                                    Трудовой стаж
                                    <div 
                                        class="plus alt addAnotherRequirement" 
                                        data-toggle="tooltip" 
                                        data-placement="top" 
                                        title="Добавить раздел" 
                                        data-category-name="seniority"
                                    ></div>
                                </h4>
                            </div>
                            <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#accordionExample">
                                <div class="card-body">
                                    @foreach($requirements['seniority'] as $requirement)
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="category" id="exampleRadios{{ $requirement['id'] }}" value="{{ $requirement['id'] }}">
                                            <label class="form-check-label" for="exampleRadios{{ $requirement['id'] }}">
                                                Добавить поле в раздел "{{ $requirement['name'] }}"
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div 
                                class="card-header" 
                                id="headingFive" 
                                data-toggle="collapse" 
                                data-target="#collapseFive" 
                                aria-expanded="false" 
                                aria-controls="collapseFive"
                            >
                                <h4 class="mb-0">
                                    Повышение квалификации
                                    <div 
                                        class="plus alt addAnotherRequirement" 
                                        data-toggle="tooltip" 
                                        data-placement="top" 
                                        title="Добавить раздел" 
                                        data-category-name="qualification_increase"
                                    ></div>
                                </h4>
                            </div>
                            <div id="collapseFive" class="collapse" aria-labelledby="headingFive" data-parent="#accordionExample">
                                <div class="card-body">
                                    @foreach($requirements['qualification_increase'] as $requirement)
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="category" id="exampleRadios{{ $requirement['id'] }}" value="{{ $requirement['id'] }}">
                                            <label class="form-check-label" for="exampleRadios{{ $requirement['id'] }}">
                                                Добавить поле в раздел "{{ $requirement['name'] }}"
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card" id="formColumn" style="display: none;">
                        <div class="form-group">
                            <label>Имя поля</label>
                            <input type="text" name="name" class="form-control" placeholder="имя поля">
                            @if(!empty($errors->first('name')))
                                <span class="invalid-feedback">
                                    {{ $errors->first('name') }}
                                </span>
                            @endif
                        </div>
                        <div class="form-group">
                            <label>Тип поля</label>
                            <select class="form-control" name="field_type" id="fieldTypeSelect">
                                @foreach(App\EmployeesRequirement::$fieldTypes as $type)
                                    <option value="{{ $type['code'] }}">{{ $type['name'] }}</option>
                                @endforeach
                            </select>
                            @if(!empty($errors->first('field_type')))
                                <span class="invalid-feedback">
                                    {{ $errors->first('field_type') }}
                                </span>
                            @endif
                        </div>
                        <div class="form-group">
                            <label>Имя поля (англ)</label>
                            <input type="text" name="field_name" class="form-control" placeholder="Имя на английском, маленькими буквами, без пробелов. Пример: field_name">
                            @if(!empty($errors->first('field_name')))
                                <span class="invalid-feedback">
                                    {{ $errors->first('field_name') }}
                                </span>
                            @endif
                        </div>
                        <div class="form-group" id="selectOptions" style="display: none;">
                            <label>Значение для списка</label>
                            <div class="plus alt margin-l15" data-toggle="tooltip" data-placement="top" title="Добавить значение"></div>
                            <input type="text" class="form-control optionInput" name="options[]">
                        </div>
                        <div class="form-group">
                            <label>Применить ко всем должностям</label>
                            <input type="checkbox" name="apply_to_all" class="form-check-input">
                        </div>
                        <button class="btn btn-primary">
                            Сохранить требование
                        </button>
                    </div>
                    <div class="card" id="formRequirement" style="display: none;">
                        <div class="form-group">
                            <label>Имя раздела</label>
                            <input type="text" name="name_requirement" class="form-control" placeholder="имя раздела">
                            @if(!empty($errors->first('name')))
                                <span class="invalid-feedback">
                                    {{ $errors->first('name') }}
                                </span>
                            @endif
                            <input type="hidden" name="category_name" id="addRequirementCategoryName">
                        </div>
                        <button class="btn btn-primary">
                            Сохранить требование
                        </button>
                    </div>
                </div>
            </div>
        {!! Form::close() !!}
               
    </div>
@endsection

@section('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        var input = '<input type="text" class="form-control optionInput margin-t10" name="options[]">';
        $('.plus').click(function(){
            $('#selectOptions').append(input);
        });
        $('#fieldTypeSelect').change(function(){
            if($(this).val() == 'select'){
                $('#selectOptions').show();
            } else {
                $('#selectOptions').hide();
            }
        });
        $('#main').on('click', '.form-check-input', function(){
            $('#formColumn').show();
            $('#formRequirement').hide();
            $('#createRecordsForm').attr( 'action', '{{ route('employees.add.new.requirement.field') }}' );
        });
        $('[data-toggle="tooltip"]').tooltip();
        $('#main').on('click', '.addAnotherRequirement', function(){
            $('#formColumn').hide();
            $('#formRequirement').show();
            $('#createRecordsForm').attr( 'action', '{{ route('employees.add.new.requirement') }}' );
            $('#addRequirementCategoryName').val($(this).attr('data-category-name'));
        });
    });
</script>
@endsection