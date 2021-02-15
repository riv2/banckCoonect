@extends('admin.admin_app')

@section('style')
    <link rel="stylesheet" href="https://unpkg.com/vue-multiselect@2.1.0/dist/vue-multiselect.min.css">
@endsection
@section('content')
    <div id="app">
        <div class="page-header">
            <h2>Назначить преподавателя</h2>

            <a href="{{ back()->getTargetUrl() }}" class="btn btn-default-light btn-xs"><i class="md md-backspace"></i> Назад</a>
        </div>

        <div class="panel panel-default">
            <div class="panel-body">
                <div class="col-sm-5">
                    <div class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-4">Семестр</label>
                            <div class="col-sm-8">
                                <select class="form-control" v-model="selectedSemester" @change="getDisciplines">
                                    @foreach($semesters as $semester)
                                        <option value="{{$semester}}">{{$semester}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4">Дисциплины</label>
                            <div class="col-sm-8">
                                <vue-multiselect
                                        v-model="selectedDiscipline"
                                        label="name"
                                        :options="disciplines"
                                        :searchable="true"
                                        :close-on-select="true"
                                        :show-labels="false"
                                        @close="getDisciplineStudentsTeam"
                                        placeholder="Выбрать дисциплину">

                                </vue-multiselect>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 form-group" :class="{hide: selectedDiscipline === null}">
                    <table id="main-table" class="table table-striped table-hover dt-responsive" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Сгруппировать</th>
                                <th>Группы</th>
                                <th>Преподаватель</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
                <div class="col-md-12 form-group" v-if="selectedDiscipline !== null">
                    <button @click="groupingGroups" class="btn btn-success">Сгруппировать</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script src="https://unpkg.com/vue-multiselect@2.1.0"></script>
    <script>
        Vue.component('vue-multiselect', window.VueMultiselect.default);

        const app = new Vue({
            el: '#app',
            data: {
                disciplines: [],
                disciplineGroups: [],
                disciplineTeachers: [],
                selectedDiscipline: null,
                selectedSemester: null,
                load: false,
                offset: 10,
                currentPage: 0,
                dataTable: null,
                groupingGroupsIds: []
            },
            methods: {
                getDisciplines(){
                    if (this.selectedSemester !== null){
                        axios.post('{{route('admin.assign.teachers.getDisciplinesBySemester')}}/' + this.selectedSemester)
                            .then(({data}) => {
                                this.disciplines = data
                            })
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Выберете семестр!',
                        })
                    }
                },
                getDisciplineStudentsTeam(){
                    if(this.selectedDiscipline !== null){
                        this.renderDataTable();
                    }
                },
                getDisciplineTeachers(){
                    axios.post('{{route('admin.assign.teachers.getDisciplineTeachers')}}/' + this.selectedDiscipline.id)
                        .then(({data}) => {
                            this.disciplineTeachers = data;
                        })
                },
                changeGroupTeacher(groupID, select) {
                    const data = {
                        teacherId: $(select).val(),
                        groupId: groupID,
                        disciplineId: this.selectedDiscipline.id
                    };
                    axios.post(`{{route('admin.assign.teachers.addEditGroupTeacher')}}`, data)
                        .then(({data}) => {
                            Swal.fire({
                                icon: 'success',
                                title: data.message,
                            })
                        }).catch(({response}) => {
                            console.log(response)
                            Swal.fire({
                                icon: 'error',
                                title: response.data.message,
                            })
                        })
                },
                renderDataTable(){
                     if (this.dataTable !== null){
                          this.dataTable.destroy();
                     }
                     this.dataTable = $('#main-table').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: {
                            url : "{{ route('admin.assign.teachers.getDisciplineGroups') }}/" + app.selectedDiscipline.id,
                            type: "post",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                        },
                        columns: [
                            {
                                data: 'actions', className: 'text-center', width: '15%',
                            },
                            {
                                data: 'group', className: 'text-center',
                            },
                            {
                                data: 'teacher',
                            },
                        ]
                    });
                },
                addToGrouping(groupId){
                    const checkboxValue = event.target.checked;

                    if(checkboxValue){
                        this.groupingGroupsIds.push(groupId)
                    } else {
                        this.groupingGroupsIds = this.groupingGroupsIds.filter(e => e !== groupId);
                    }
                },
                groupingGroups(){
                    if (this.groupingGroupsIds.length > 0 && this.selectedDiscipline !== null){
                        const data = {
                            groupsIds: this.groupingGroupsIds,
                            disciplineId: this.selectedDiscipline.id
                        }
                        axios.post('{{route('admin.assign.teachers.groupingStudyGroups')}}', data)
                            .then(({data}) => {
                                this.renderDataTable();
                                this.groupingGroupsIds = [];

                                Swal.fire({
                                    icon: 'success',
                                    title: data.message,
                                })
                            }).catch(({response}) => {
                                Swal.fire({
                                    icon: 'error',
                                    title: response.data.message,
                                })
                            })
                    }
                }
            },
        })
    </script>
@endsection