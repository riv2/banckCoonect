@extends("admin.admin_app")

@section("content")
    <div id="entrance_exam">
        <div class="page-header">
            <h2> Вступительные испытания </h2>
        </div>

        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row padding-20">

                    <form class="col-md-4">
                        <div class="form-group">
                            <label> Выбрать год </label>
                            <select @change="entranceExamChangeYear($event)" v-model="entranceExamCurentYear" class="form-control">
                                @if( !empty($yearsList) )
                                    @foreach($yearsList as $year)
                                        <option value="{{ $year }}"> {{ $year }} </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </form>
                    <div class="pull-right">
                        @if(\App\Services\Auth::user()->hasRight('test_pc_vi','create'))
                            <a href="{{ route('adminEntranceExamEdit',['id'=>0]) }}" target="_blank" class="btn btn-primary"> Добавить </a>
                        @endif
                    </div>
                    <div class="clearfix"></div>

                    <div class="col-12 padding-15">


                        <div v-if="errorMessage" :class="{ 'alert-danger': isError, 'alert-success': !isError }" class="alert margin-t20 margin-b20">
                            <div v-html="errorMessage"> </div>
                        </div>


                        <div class="table-responsive no-padding">
                            <table class="table table-striped" style="width:100%;">
                                <thead>
                                <tr>
                                    <th> ID </th>
                                    <th> Название </th>
                                    <th> Год </th>
                                    <th> Дата </th>
                                    <td></td>
                                </tr>
                                </thead>
                                <tbody>
                                <template v-for="(item, index) in entranceExamList.data">
                                    <tr>
                                        <td> @{{ item.id }} </td>
                                        <td> @{{ item.name }} </td>
                                        <td> @{{ item.year }} </td>
                                        <td> @{{ item.created_at }} </td>
                                        <td>
                                            @if(\App\Services\Auth::user()->hasRight('test_pc_vi','edit'))
                                            <a :href="entranceExamEditUrl + '?id=' + item.id" target="_blank" class="btn btn-success"><i class="fa fa-edit"></i></a>
                                            @endif
                                            @if(\App\Services\Auth::user()->hasRight('test_pc_vi','delete'))
                                            <button @click="entranceExamRemove(item.id)" class="btn btn-danger"><i class="fa fa-trash"></i></button>
                                            @endif
                                        </td>
                                    </tr>
                                </template>
                                </tbody>
                            </table>
                        </div>
                        <nav aria-label="Page navigation">
                            <paginate
                                    v-model="entranceExamPageNum"
                                    :page-count="entranceExamList.last_page"
                                    :page-range="3"
                                    :margin-pages="2"
                                    :click-handler="entranceExamPaginateClickCallback"
                                    :prev-text="'{{ __('Previous') }}'"
                                    :next-text="'{{ __('Next') }}'"
                                    :container-class="'pagination'"
                                    :page-class="'page-item'"
                                    :page-link-class="'page-link'"
                                    :prev-class="'page-link'"
                                    :next-class="'page-link'">
                            </paginate>
                        </nav>


                    </div>


                </div>
            </div>
        </div>

    </div>

@endsection

@section('scripts')

    <script src="https://unpkg.com/vuejs-paginate@0.9.0"></script>

    <script type="text/javascript">

        Vue.component('paginate', VuejsPaginate);

        var app = new Vue({
            el: '#entrance_exam',
            data: {

                entranceExamEditUrl: '{{ route('adminEntranceExamEdit') }}',
                isError: false,
                errorMessage: '',
                entranceExamDataRequest: false,
                entranceExamList: {
                    current_page: 0,
                    last_page: 0,
                    data: [],
                    total: 0
                },
                entranceExamPageNum: 1,
                entranceExamCurentYear: '{{ date('Y') }}'
            },
            methods: {

                entranceExamPaginateClickCallback: function(pageNum) {

                    this.entranceExamPageNum = pageNum;
                    this.entranceExamGetList();
                },
                entranceExamChangeYear: function(event){
                    this.entranceExamCurentYear = event.target.value;
                    this.entranceExamGetList();
                },
                entranceExamGetList: function()
                {
                    var self = this;
                    axios.post('{{ route('adminEntranceExamGetList') }}',{
                        "_token": "{{ csrf_token() }}",
                        "year": this.entranceExamCurentYear,
                        "page": this.entranceExamPageNum
                    })
                    .then(function(response){

                        if( response.data.status ){

                            self.entranceExamList = response.data.models;

                        }
                    })
                    .catch( error => {

                        console.log(error)
                    });

                },
                entranceExamRemove: function(id){

                    this.isError = false;
                    this.errorMessage = '';

                    if (!confirm('Вы хотите удалить ВИ?')) {
                        return;
                    }

                    this.entranceExamDataRequest = true;
                    var self = this;
                    axios.post('{{ route('adminEntranceExamRemove') }}',{
                        "_token": "{{ csrf_token() }}",
                        "id": id
                    })
                    .then(function(response){

                        if( response.data.status ){

                            self.entranceExamGetList();

                        } else {

                            self.isError = true;
                            self.errorMessage = response.data.message;
                        }

                        self.entranceExamDataRequest = false;

                    })
                    .catch( error => {

                        console.log(error)
                    });


                }


            },
            created: function(){

                this.entranceExamGetList();
            }
        });

    </script>

@endsection
