@extends("admin.admin_app")

@section('title', 'Преподаватель '. $userTeacher->fio)

@section("content")
    <div id="main">
        <div class="page-header">
            <h2> </h2>

            <a href="{{ URL::to('/teachers') }}" class="btn btn-default-light btn-xs"><i class="md md-backspace"></i> Назад</a>

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
                {!! Form::open(array('url' => route('adminTeacherEdit', ['id' => $userTeacher->id]),'class'=>'form-horizontal padding-15','name'=>'service_form','id'=>'service_form','role'=>'form','enctype' => 'multipart/form-data')) !!}

                @include('admin.pages.teachers.edit.main')

                <hr>

                <div class="form-group">
                    <div class="col-md-offset-3 col-sm-9 ">
                        <button type="submit" class="btn btn-primary btn-lg">Сохранить</button>
                    </div>
                </div>

                {!! Form::close() !!}
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ URL::asset('admin_assets/js/education-form.js') }}"></script>

    <script type="text/javascript">
        const bus = new Vue();
        Vue.component('group-list', {
            props: ['selectGroupList', 'fullGroupList', 'disciplineModel'],
            data: function () {
                return {
                    groupList: this.fullGroupList,
                    relatedGroupList: this.selectGroupList,
                    selectedGroup: null,
                    searchGroupPanel: false,
                    searchGroupText: '',
                    discipline: this.disciplineModel
                };
            },
            methods: {
                removeGroup: function (key) {
                    this.relatedGroupList.splice(key, 1);
                },
                /*searchDiscipline: function()
                {
                    this.loadDisciplineList(this.searchDisciplineText)
                },*/
                searchGroupClear: function () {
                    this.selectedGroup = null;
                    this.searchGroupPanel = false;
                },
                inRelated: function (groupId) {
                    for (var i = 0; i < this.relatedGroupList.length; i++) {
                        if (this.relatedGroupList[i].id == groupId) {
                            return true;
                        }
                    }

                    return false;
                }
            },
            watch: {
                selectedGroup: function (val) {
                    if (this.selectedGroup !== null) {
                        this.relatedGroupList.push(this.groupList[this.selectedGroup]);
                    }
                    this.searchGroupClear();
                }
            },
            created: function () {

                var self = this;

                /*bus.$on('changeMainGroupList', function(mainGroupList) {
                    self.groupList = mainGroupList;
                });*/

            },
            template: `
                        <div class="col-md-12 form-group">
                            <div class="col-md-12 alert" style="margin-bottom: 10px; background-color: rgb(214,214,214);"
                                 v-for="(group, key) in relatedGroupList">
                                    <span>@{{ group.name }}</span>
                                <div class="form-inline">
                                    <span>Дата с: </span><input type="date" v-bind:name="'study_group_ids[' + discipline.id + '][date_from][]'" v-model="group.date_from" class="form-control" />
                                    <span>Дата по: </span><input type="date" v-bind:name="'study_group_ids[' + discipline.id + '][date_to][]'" v-model="group.date_to" class="form-control" />
                                </div>
                                <input type="hidden" v-bind:name="'study_group_ids[' + discipline.id + '][id][]'" v-bind:value="group.id">
                                <span style="cursor: pointer" class="pull-right" v-on:click="removeGroup(key)"><i class="glyphicon glyphicon-remove"></i></span>
                            </div>
                            <hr>
                            <div class="col-md-12" style="padding-left: 0px; padding-right: 0px;">
                                <div class="col-md-12" v-if="!searchGroupPanel" style="padding-left: 0px; padding-right: 0px;">
                                    <a style="cursor: pointer" v-on:click="searchGroupPanel=true; ">Добавить</a>
                                </div>
                                <div class="col-md-4" v-if="searchGroupPanel" style="padding-left: 0px; padding-right: 0px;">
                                    <select v-model="selectedGroup" class="form-control" data-live-search="true">
                                        <option v-for="(group, key) in groupList" v-bind:value="key">@{{ group.name }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    `
        });

        Vue.component('discipline-list', {
            props: ['selectDisciplineList', 'mainGroupList'],
            data: function () {
                return {
                    disciplineList: [],
                    relatedDisciplineList: this.selectDisciplineList,
                    selectedGroup: this.mainGroupList,
                    selectedDiscipline: null,
                    searchDisciplinePanel: false,
                    searchDisciplineText: '',
                    relatedGroupList: []
                };
            },
            methods: {
                loadDisciplineList: function (text) {
                    var self = this;
                    axios.post('{{route('disciplineAjaxList')}}', {
                        text: text
                    })
                        .then(function (response) {
                            self.disciplineList = response.data;
                        });
                },
                removeDiscipline: function (key) {
                    this.relatedDisciplineList.splice(key, 1);
                    //this.updateMainGroupList();
                },
                searchDiscipline: function () {
                    this.loadDisciplineList(this.searchDisciplineText)
                },
                searchDisciplineClear: function () {
                    this.disciplineList = [];
                    this.searchDisciplinePanel = false;
                    this.searchDisciplineText = '';
                    this.selectedDiscipline = null;
                },
                inRelated: function (disciplineId) {
                    for (var i = 0; i < this.relatedDisciplineList.length; i++) {
                        if (this.relatedDisciplineList[i].id == disciplineId) {
                            return true;
                        }
                    }

                    return false;
                },
                updateMainGroupList: function () {
                    var self = this;
                    var disciplineIdList = [];

                    for (var i = 0; i < this.relatedDisciplineList.length; i++) {
                        disciplineIdList.push(this.relatedDisciplineList[i].id);
                    }

                    axios.post('{{route('groupListByDisciplinesAjax')}}', {
                        discipline_id_list: disciplineIdList,
                        "_token": "{{ csrf_token() }}"
                    })
                        .then(function (response) {
                            app.mainGroupList = response.data;
                        });
                },
                loadFullGroupListForDiscipline: function (discipline, disciplineIdex) {

                    var self = this;
                    self.activeDiscipline = discipline;
                    axios.post('{{route('groupListByDisciplinesAjax')}}', {
                        discipline_id_list: [discipline.id],
                        "_token": "{{ csrf_token() }}"
                    })
                        .then(function (response) {
                            self.activeDiscipline.fullGroupList = response.data;
                            self.activeDiscipline.relatedGroupList = [];
                            self.relatedDisciplineList.push(self.activeDiscipline);
                        });
                }
            },
            watch: {
                selectedDiscipline: function (val) {
                    if (this.selectedDiscipline !== null) {
                        //this.relatedDisciplineList.push(this.disciplineList[this.selectedDiscipline]);
                        this.loadFullGroupListForDiscipline(this.disciplineList[this.selectedDiscipline]);
                        //this.updateMainGroupList();
                    }
                    this.searchDisciplineClear();
                }
            },
            created: function () {
                //this.updateMainGroupList();
            },
            template: `
                        <div class="col-md-12 form-group">
                            <div class="col-md-12 alert" style="margin-bottom: 10px; background-color: rgb(238, 238, 238);" v-for="(discipline, key) in relatedDisciplineList">
                                @{{ discipline.name + ' (' + discipline.ects }}<sub>ECTS</sub>)
                                <input type="hidden" name="disciplines[]" v-bind:value="discipline.id">
                                <span style="cursor: pointer" class="pull-right" v-on:click="removeDiscipline(key)"><i class="glyphicon glyphicon-remove"></i></span>
                                <div class="form-group">
                                    <label class="col-sm-1 control-label">Группы</label>
                                    <div class="col-sm-11">
                                        <group-list v-bind:select-group-list="discipline.relatedGroupList" v-bind:full-group-list="discipline.fullGroupList" v-bind:discipline-model="discipline"></group-list>
                                    </div>
                                </div>
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
                relatedGroupList: [],
                relatedDisciplineList: [],
                mainGroupList: []
            },
            created: function () {
                @foreach($userTeacher->teacherStudyGroups as $group)
                    this.relatedGroupList.push({
                        id: {{$group->id}},
                        name: '{{$group->name}}'
                    });
                @endforeach

                @foreach($userTeacher->teacherDisciplines as $discipline)
                    var fullGroupList = [];
                    var relatedGroupList = [];

                    <?php $groupList = \App\StudyGroup::groupListByDisciplines([$discipline->id]); ?>

                    @foreach($groupList as $group)
                        fullGroupList.push({
                            id: '{{$group->id}}',
                            name: '{{$group->name}}',
                        });

                        @foreach($userTeacher->teacherStudyGroups as $teacherGroup)
                            @if($teacherGroup->id == $group->id && $teacherGroup->pivot->discipline_id == $discipline->id)
                                relatedGroupList.push({
                                    id: '{{$teacherGroup->id}}',
                                    name: '{{$teacherGroup->name}}',
                                    date_from: '{{$teacherGroup->pivot->date_from ? $teacherGroup->pivot->date_from : ''}}',
                                    date_to: '{{$teacherGroup->pivot->date_to ? $teacherGroup->pivot->date_to : ''}}'
                                });
                            @endif
                        @endforeach
                    @endforeach

                    this.relatedDisciplineList.push({
                        id: {{$discipline->id}},
                        name: '{{$discipline->name}}',
                        ects: {{$discipline->ects}},
                        fullGroupList: fullGroupList,
                        relatedGroupList: relatedGroupList
                    });
                @endforeach
            },
            watch: {
                mainGroupList: function () {
                    //bus.$emit('changeMainGroupList', this.mainGroupList);
                }
            }
        });

        $("#serviceType").change(function () {
            var placesList = $('#placesList');

            if ($(this).find('select').val() == 'Master') placesList.show(150);
            else placesList.hide(150);
        });
    </script>
@endsection