@extends("admin.admin_app")

@section("content")
    <div id="main">
        <div class="page-header">
            <h2> </h2>

            <a href="{{ URL::to('/trends') }}" class="btn btn-default-light btn-xs"><i class="md md-backspace"></i> Назад</a>

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
                {!! Form::open(array('url' => array( route('adminTrendEdit', ['id' => $trend->id == 0 ? 'add' : $trend->id]) ),'class'=>'form-horizontal padding-15','name'=>'trend_form','id'=>'trend_form','role'=>'form','enctype' => 'multipart/form-data')) !!}

                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Наименование</label>
                    <div class="col-sm-9">
                        <input type="text" required name="name" value="{{ $trend->name ?? '' }}" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Наименование KZ</label>
                    <div class="col-sm-9">
                        <input type="text" required name="name_kz" value="{{ $trend->name_kz ?? '' }}" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Наименование EN</label>
                    <div class="col-sm-9">
                        <input type="text" required name="name_en" value="{{ $trend->name_en ?? '' }}" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Классификация направления</label>
                    <div class="col-sm-9">
                        <input type="text" required name="classif_direction" value="{{ $trend->classif_direction ?? '' }}" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Классификация направления KZ</label>
                    <div class="col-sm-9">
                        <input type="text" required name="classif_direction_kz" value="{{ $trend->classif_direction_kz ?? '' }}" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Классификация направления EN</label>
                    <div class="col-sm-9">
                        <input type="text" required name="classif_direction_en" value="{{ $trend->classif_direction_en ?? '' }}" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Код и классификация области образования</label>
                    <div class="col-sm-2">
                        <input type="text" required name="education_area_code" value="{{ $trend->education_area_code ?? '' }}" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Код и классификация направления подготовки</label>
                    <div class="col-sm-2">
                        <input type="text" required name="training_code" value="{{ $trend->training_code ?? '' }}" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Код ОП</label>
                    <div class="col-sm-2">
                        <input type="text" required name="op_code" value="{{ $trend->op_code ?? '' }}" class="form-control">
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Квалификации</label>

                            <div class="col-sm-3">
                                <button type="button" class="btn btn-default" @click="addQualification">
                                    Добавить квалификацию
                                </button>
                            </div>
                        </div>

                        <qualification
                            v-for="(qualification, key) in qualifications"
                            :id="key"
                            :qualification="qualification"
                        >
                        </qualification>
                    </div>
                </div>

                <hr>

                <div class="form-group">
                    <div class="col-md-offset-3 col-sm-9 ">
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                    </div>
                </div>

                {!! Form::close() !!}
            </div>


        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        Vue.component('qualification', {
            props: ['qualification', 'id'],
            methods: {
                removeQualification: function () {
                    this.$parent.$delete(this.$parent.qualifications, this.id);
                }
            },
            template: `
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="form-group">
                            <label :for="'qualifications[' + id + '][name_ru]'" class="col-sm-3 control-label">Квалификация (ru)</label>

                            <div class="col-sm-8">
                                <input type="text" required="required" v-model="qualification.name_ru" :name="'qualifications[' + id + '][name_ru]'" class="form-control">
                            </div>

                            <div class="col-sm-1">
                                <button type="button" class="btn btn-danger" @click="removeQualification">
                                    <i class="fa fa-minus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label :for="'qualifications[' + id + '][name_kz]'" class="col-sm-3 control-label">Квалификация (kz)</label>

                            <div class="col-sm-8">
                                <input type="text" required="required" v-model="qualification.name_kz" :name="'qualifications[' + id + '][name_kz]'" class="form-control">
                            </div>
                        </div>

                        <div class="form-group">
                            <label :for="'qualifications[' + id + '][name_en]'" class="col-sm-3 control-label">Квалификация (en)</label>

                            <div class="col-sm-8">
                                <input type="text" required="required" v-model="qualification.name_en" :name="'qualifications[' + id + '][name_en]'" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
            `
        });

        var app = new Vue({
            el: "#main",
            data: {
                qualifications: {!! json_encode(old('qualifications', $trend->qualifications)) !!}
            },
            methods: {
                addQualification: function () {
                    this.qualifications.push({
                        'name_ru': '',
                        'name_kz': '',
                        'name_en': '',
                    })
                },
            }
        })
    </script>
@endsection
