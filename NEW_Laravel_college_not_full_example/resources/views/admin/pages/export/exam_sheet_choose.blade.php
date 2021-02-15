@extends("admin.admin_app")

@section('title', 'Экспорт экзаменационной ведомости')

@section("content")
    <div id="app">
        <div class="page-header">
            <h2>Экспорт экзаменационной ведомости</h2>
        </div>

        <div class="panel panel-default panel-shadow">
            <div class="panel-body">
                <div class="col-md-6">
                    <div class="form-group">
                        {!!Form::open(['url' => route('adminExportExamSheets'), 'method' => 'POST'])!!}

                        <label>Дисциплина:</label>
                        <input type="hidden" name="discipline_id" id="discipline_id">
                        <v-select :options="disciplines" v-model="discipline" v-on:input="discipline_changed"></v-select>

                        <div v-if="discipline_selected" v-cloak class="margin-t20">
                            <label>Группа:</label>
                            <input type="hidden" name="group_id" id="group_id">
                            <v-select :options="groups" v-model="group" v-on:input="group_changed" label="name"></v-select>
                        </div>

                        <div v-if="discipline_selected" v-cloak class="form-group margin-t20">
                            <label>Семестр:</label>
                            <select name="semester" class="form-control" >
                                @foreach($semesters as $semester)
                                    <option value="{{$semester}}">{{$semester}}</option>
                                @endforeach
                            </select>
                        </div>


                        <hr>
                        <button type="submit" class="btn btn-primary" v-if="group_selected" v-cloak>Экспорт</button>
                        
                        {!!Form::close()!!}
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://unpkg.com/vue-select@latest"></script>
    <link rel="stylesheet" href="https://unpkg.com/vue-select@latest/dist/vue-select.css">

    <script type="text/javascript">
        Vue.component('v-select', VueSelect.VueSelect);

        var app = new Vue({
            el: '#app',
            data: {
                disciplines: [
                    @foreach($disciplines as $id => $name)
                        {id: {{$id}}, label: '{{$name}}'},
                    @endforeach
                    ''
                ],
                discipline : null,
                discipline_selected: false,
                groups: [],
                group: null,
                group_selected:false,
                date_from:null,
                date_to:null
            },
            methods: {
                discipline_changed: function() {
                    this.group = null;
                    this.groups = [];
                    this.group_selected = false;

                    this.discipline_selected = false;

                    $('#discipline_id').val(this.discipline.id);

                    var self = this;

                    axios.post('{{route('adminGetDisciplineGroupsJSON')}}', {
                        discipline_id: self.discipline.id
                    })
                        .then(function(response) {
                            self.groups = response.data;

                            self.discipline_selected = true;
                        });
                },
                group_changed: function() {
                    this.group_selected = true;

                    $('#group_id').val(this.group.id);
                }
            }
        });
    </script>
@endsection