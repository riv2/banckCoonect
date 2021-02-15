@extends("admin.admin_app")

@section("content")
    <div id="agitator-transactions">
        <div class="page-header">
            <h2>Агитаторы: Транзакции</h2>
        </div>
        @if(Session::has('flash_message'))
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                {{ Session::get('flash_message') }}
            </div>
        @endif

        <div class="panel panel-default panel-shadow">
            <div class="panel-body">
                <div class="col-md-12">


                    {{-- message block --}}
                    <div v-if="errorMessage" :class="{ 'alert-danger': isError, 'alert-success': !isError }" class="alert margin-t20 margin-b20">
                        <div v-html="errorMessage"> </div>
                    </div>

                    <div class="table-responsive no-padding">
                        <div class="col-md-3 pull-right">
                            <input v-model="agitatorTransactionSearch" @change="agitatorTransactionChangeSearch" class="form-control" type="text" placeholder="id или ФИО" />
                        </div>
                        <table class="table table-striped" style="width:100%;">
                            <thead>
                            <tr>
                                <th> Юзер </th>
                                <th> Банк </th>
                                <th> Сумма </th>
                                <th> Сумма % </th>
                                <th> Статус </th>
                                <th> Дата </th>
                            </tr>
                            </thead>
                            <tbody>
                            <template v-for="(item, index) in agitatorTransactions.data">
                                <tr>
                                    <td> @{{ item.user.student_profile.fio }} (@{{ item.user.id }}) <i
                                                :title="item.user_info"
                                                data-toggle="tooltip" data-placement="bottom"
                                                class="fa fa-info-circle"></i> </td>
                                    <td> @{{ item.bank.name }} <i
                                                :title="item.bank_info"
                                                data-toggle="tooltip" data-placement="bottom"
                                                class="fa fa-info-circle"></i> </td>
                                    <td> @{{ item.cost }} </td>
                                    <td> @{{ item.percent }} </td>
                                    <td>
                                        <select v-model="item.status" @change="changeTransactionStatus(item)" :disabled="isRequest" class="form-control">
                                            <option value="{{ \App\AgitatorRefunds::STATUS_PROCESS }}"> {{ __(\App\AgitatorRefunds::STATUS_PROCESS) }} </option>
                                            <option value="{{ \App\AgitatorRefunds::STATUS_SUCCESS }}"> {{ __(\App\AgitatorRefunds::STATUS_SUCCESS) }} </option>
                                            <option value="{{ \App\AgitatorRefunds::STATUS_ERROR }}"> {{ __(\App\AgitatorRefunds::STATUS_ERROR) }} </option>
                                            <option value="{{ \App\AgitatorRefunds::STATUS_CANCELLED }}"> {{ __(\App\AgitatorRefunds::STATUS_CANCELLED) }} </option>
                                        </select>
                                    </td>
                                    <td> @{{ item.date }} </td>
                                </tr>
                            </template>
                            </tbody>
                        </table>
                    </div>

                    <nav aria-label="Page navigation">
                        <paginate
                                v-model="agitatorTransactionsPageNum"
                                :page-count="agitatorTransactions.last_page"
                                :page-range="3"
                                :margin-pages="2"
                                :click-handler="agitatorTransactionPaginateClickCallback"
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
            <div class="clearfix"></div>
        </div>

    </div>

@endsection

@section('scripts')

    <script src="https://unpkg.com/vuejs-paginate@0.9.0"></script>

    <script type="text/javascript">

        Vue.component('paginate', VuejsPaginate);

        var app = new Vue({
            el: '#agitator-transactions',
            data: {
                isError: false,
                errorMessage: '',
                isRequest: false,

                agitatorTransactions: {
                    current_page: 0,
                    last_page: 0,
                    data: [],
                    total: 0
                },
                agitatorTransactionsPageNum: 1,
                agitatorTransactionSearch: ''

            },
            methods: {

                getTransactions: function(){

                    this.isRequest = true;
                    var self = this;
                    axios.post('{{ route('adminAgitatorAjaxGetTransactions') }}',{
                        "_token": "{{ csrf_token() }}",
                        "page": this.agitatorTransactionsPageNum,
                        "search": this.agitatorTransactionSearch
                    })
                    .then(function(response){

                        if( response.data.status ){

                            self.agitatorTransactions = response.data.models;

                        }
                        self.isRequest = false;
                    })
                    .catch( error => {

                        console.log(error)
                    });

                },
                agitatorTransactionPaginateClickCallback: function(pageNum) {

                    this.agitatorTransactionsPageNum = pageNum;
                    this.getTransactions();
                },
                changeTransactionStatus: function(transaction){

                    this.isError      = false;
                    this.errorMessage = '';

                    this.isRequest = true;
                    var self = this;
                    axios.post('{{ route('adminAgitatorAjaxChangeTransactionStatus') }}',{
                        "_token": "{{ csrf_token() }}",
                        "transaction": transaction.id,
                        "status": transaction.status
                    })
                    .then(function(response){

                        if( response.data.status ){

                            self.errorMessage = response.data.message;
                            self.getTransactions();
                        }

                        self.isRequest = false;
                    })
                    .catch( error => {

                        console.log(error)
                    });

                },
                agitatorTransactionChangeSearch: function(){

                    this.getTransactions();
                }
            },
            created: function(){

                this.getTransactions();

            }
        });

    </script>

    <script type="text/javascript">

        window.onload = function(){

            $('[data-toggle="tooltip"]').tooltip();

        };

    </script>

@endsection