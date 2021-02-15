@extends("admin.admin_app")

@section("content")

    <div id="main">
        <div class="page-header">
            <h2> {{ isset($module->name) ? 'Редактировать: '. $module->name : 'Добавить модуль' }}</h2>

            <a href="{{ URL::to('/modules') }}" class="btn btn-default-light btn-xs"><i class="md md-backspace"></i> Назад</a>

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
                <form method="post" action="" enctype="multipart/form-data" class="form-horizontal">
                    {{ csrf_field() }}
                    <div role="tabpanel" class="tab-pane active" id="cz">
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Название</label>
                            <div class="col-sm-9">
                                <input type="text" name="name" required value="{{ isset($module->name) ? $module->name : null }}" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Название (en)</label>
                            <div class="col-sm-9">
                                <input type="text" name="name_en" required value="{{ isset($module->name_en) ? $module->name_en : null }}" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Название (kz)</label>
                            <div class="col-sm-9">
                                <input type="text" name="name_kz" required value="{{ isset($module->name_kz) ? $module->name_kz : null }}" class="form-control">
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Дисциплины</label>
                        <div class="col-sm-9">
                            <discipline-list v-bind:select-discipline-list="relatedDisciplineList"></discipline-list>
                        </div>
                    </div>

                    <hr>
                    <div class="form-group">
                        <div class="col-md-offset-3 col-sm-9 ">
                            <button type="submit" class="btn btn-primary">Сохранить</button>

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">

        Vue.component('discipline-list', {
            props: [ 'selectDisciplineList' ],
            data: function(){
                return {
                    disciplineList: [],
                    relatedDisciplineList: this.selectDisciplineList,
                    selectedDiscipline: null,
                    searchDisciplinePanel: false,
                    searchDisciplineText: ''
                };
            },
            methods: {
                loadDisciplineList: function(text){
                    var self = this;
                    axios.post('{{route('disciplineAjaxList')}}', {
                        text: text
                    })
                        .then(function(response){
                            self.disciplineList = response.data;
                        });
                },
                removeDiscipline: function(key){
                    this.relatedDisciplineList.splice(key, 1);
                },
                searchDiscipline: function()
                {
                    this.loadDisciplineList(this.searchDisciplineText)
                },
                searchDisciplineClear: function(){
                    this.disciplineList = [];
                    this.searchDisciplinePanel = false;
                    this.searchDisciplineText = '';
                    this.selectedDiscipline = null;
                },
                inRelated: function(disciplineId) {
                    for(var i = 0; i < this.relatedDisciplineList.length; i++) {
                        if(this.relatedDisciplineList[i].id == disciplineId) {
                            return true;
                        }
                    }

                    return false;
                }
            },
            watch: {
                selectedDiscipline: function(val){
                    if(this.selectedDiscipline !== null) {
                        this.relatedDisciplineList.push(this.disciplineList[this.selectedDiscipline]);
                    }
                    this.searchDisciplineClear();
                }
            },
            created: function() {
            },
            template: `
                        <div class="col-md-12 form-group">
                            <div class="col-md-12 alert" style="margin-bottom: 10px; background-color: rgb(238, 238, 238);"
                                 v-for="(discipline, key) in relatedDisciplineList">
                                    @{{ discipline.name + ' (' + discipline.ects}}<sub>ECTS</sub>)
                                <input type="hidden" name="disciplines[]" v-bind:value="discipline.id">
                                <span style="cursor: pointer" class="pull-right" v-on:click="removeDiscipline(key)"><i class="glyphicon glyphicon-remove"></i></span>
                            </div>
                            <div class="col-md-12" style="padding-left: 0px; padding-right: 0px;">
                                <div class="col-md-12" v-if="!searchDisciplinePanel" style="padding-left: 0px; padding-right: 0px;">
                                    <a style="cursor: pointer" v-on:click="searchDisciplinePanel=true; ">Добавить</a>
                                </div>
                                <div class="col-md-12" v-if="searchDisciplinePanel" style="padding-left: 0px; padding-right: 0px;">
                                    <input type="text" class="form-control" v-on:keyup="searchDiscipline()" v-model="searchDisciplineText" />
                                    <span style="cursor: pointer;position: relative;top: -28px;left: -10px;" class="pull-right" v-on:click="searchDisciplineClear()"><i class="glyphicon glyphicon-remove"></i></span>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu" v-bind:style="{display: (disciplineList.length > 0 ? 'block' : 'none')}" style="overflow-y: auto; max-height: 150px;position: relative;">
                                        <li v-for="(discipline, key) in disciplineList" v-show="!inRelated(discipline.id)">
                                            <a style="cursor: pointer" v-on:click="selectedDiscipline = key">@{{ discipline.name + ' (' + discipline.ects }}<sub>ECTS</sub>)</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
            `
        });

        var app = new Vue({
            el: '#main',
            data: {
                relatedDisciplineList: []
            },
            created: function(){
                @foreach($module->disciplines as $discipline)
                    this.relatedDisciplineList.push({id: {{$discipline->id}}, name: '{{$discipline->name}}', ects: {{$discipline->ects}} });
                @endforeach
            }
        });

    </script>
@endsection