@extends("admin.admin_app")

@section("content")
    <div id="check_list">
        <div class="page-header">
            <h2> Проверочные листы </h2>
        </div>

        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row padding-20">

                    <form class="col-md-4">
                        <div class="form-group">
                            <label> Выбрать год </label>
                            <select @change="checkListChangeYear($event)" v-model="checkListCurentYear" class="form-control">
                                @if( !empty($yearsList) )
                                    @foreach($yearsList as $year)
                                        <option value="{{ $year }}"> {{ $year }} </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </form>
                    <div class="pull-right">
                        @if(\App\Services\Auth::user()->hasRight('test_pc_pl','create'))
                            <a href="{{ route('adminCheckListEdit',['id'=>0]) }}" target="_blank" class="btn btn-primary"> Добавить </a>
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
                                    <th> Образовательная программа </th>
                                    <th> Базовое образование </th>
                                    <th> Гражданство </th>
                                    <th> Уровень образования </th>
                                    <th> Год </th>
                                    <th> Дата </th>
                                    <td></td>
                                </tr>
                                </thead>
                                <tbody>
                                <template v-for="(item, index) in checkListList.data">
                                    <tr>
                                        <td> @{{ item.id }} </td>
                                        <td> @{{ item.speciality_name }} </td>
                                        <td> @{{ item.basic_education_name }} </td>
                                        <td> @{{ item.citizenship_name }} </td>
                                        <td> @{{ item.education_level_name }} </td>
                                        <td> @{{ item.year }} </td>
                                        <td> @{{ item.created_at }} </td>
                                        <td>
                                            @if(\App\Services\Auth::user()->hasRight('test_pc_pl','edit'))
                                            <a :href="checkListEditUrl + '?id=' + item.id" target="_blank" class="btn btn-success"><i class="fa fa-edit"></i></a>
                                            @endif
                                            @if(\App\Services\Auth::user()->hasRight('test_pc_pl','delete'))
                                            <button @click="checkListRemove(item.id)" class="btn btn-danger"><i class="fa fa-trash"></i></button>
                                            @endif
                                        </td>
                                    </tr>
                                </template>
                                </tbody>
                            </table>
                        </div>
                        <nav aria-label="Page navigation">
                            <paginate
                                    v-model="checkListPageNum"
                                    :page-count="checkListList.last_page"
                                    :page-range="3"
                                    :margin-pages="2"
                                    :click-handler="checkListPaginateClickCallback"
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
            el: '#check_list',
            data: {

                checkListEditUrl: '{{ route('adminCheckListEdit') }}',
                isError: false,
                errorMessage: '',
                checkListDataRequest: false,
                checkListList: {
                    current_page: 0,
                    last_page: 0,
                    data: [],
                    total: 0
                },
                checkListPageNum: 1,
                checkListCurentYear: '{{ date('Y') }}'
            },
            methods: {

                checkListPaginateClickCallback: function(pageNum) {

                    this.checkListPageNum = pageNum;
                    this.checkListGetList();
                },
                checkListChangeYear: function(event){
                    this.checkListCurentYear = event.target.value;
                    this.checkListGetList();
                },
                checkListGetList: function(){

                    var self = this;
                    axios.post('{{ route('adminCheckListGetList') }}',{
                        "_token": "{{ csrf_token() }}",
                        "year": this.checkListCurentYear,
                        "page": this.checkListPageNum
                    })
                    .then(function(response){

                        if( response.data.status ){

                            self.checkListList = response.data.models;

                        }
                    })
                    .catch( error => {

                        console.log(error)
                    });

                },
                checkListRemove: function(id){

                    this.isError = false;
                    this.errorMessage = '';

                    if (!confirm('Вы хотите удалить ПЛ?')) {
                        return;
                    }

                    this.checkListDataRequest = true;
                    var self = this;
                    axios.post('{{ route('adminCheckListRemove') }}',{
                        "_token": "{{ csrf_token() }}",
                        "id": id
                    })
                    .then(function(response){

                        if( response.data.status ){

                            self.checkListGetList();

                        } else {

                            self.isError = true;
                            self.errorMessage = response.data.message;
                        }

                        self.checkListDataRequest = false;

                    })
                    .catch( error => {

                        console.log(error)
                    });

                }

            },
            created: function(){

                this.checkListGetList();
            }
        });

    </script>

@endsection