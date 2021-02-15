@extends("admin.admin_app")

@section("content")
    <div id="main" xmlns:v-on="http://www.w3.org/1999/xhtml">
        <div class="page-header">
            <h2>Результаты проверки уникальности</h2>
        </div>

        <div class="alert alert-danger" role="alert" v-if="err_message != null">
            <p v-html="err_message"></p>
        </div>
        <div class="alert alert-success" role="alert" v-if="success_message != null">
            <p v-html="success_message"></p>
        </div>

        <div class="panel panel-default panel-shadow">
            <div class="panel-body">
                <div class="col-md-6">
                   <div class="form-horizontal">
                       <div class="form-group padding-t10">
                           <label class="col-md-2 control-label">ID проверки:</label>
                           <div class="col-md-10">
                               <input type="text" class="form-control" v-on:change="getPlagiarismResult" v-model="id" style="width: 100px"/>
                           </div>
                       </div>
                   </div>
                </div>
            </div>
            <div >
                <div class="col-md-12 margin-b30">
                    <div class="col-md-10 form-horizontal">
                        <div class="form-group">
                            <div class="col-md-10 padding-0">
                                <div class="col-md-6">
                                    <span class="list-group-item">
                                        Автор работы:
                                        <strong v-html="author"></strong>
                                    </span>
                                </div>
                                <div class="col-md-6">
                                    <span class="list-group-item">
                                        Образовательная программа:
                                        <strong v-html="education_program"></strong>
                                    </span>
                                </div>
                                <div class="col-md-6" >
                                    <span class="list-group-item">
                                        Название работы:
                                        <strong v-html="work_name"></strong>
                                    </span>
                                </div>
                                <div class="col-md-6" >
                                    <span class="list-group-item">
                                        Дата и время проверки:
                                        <strong v-html="check_time"></strong>
                                    </span>
                                </div>
                                <div class="col-md-6" >
                                    <span class="list-group-item">
                                        Учетная запись:
                                        <strong v-html="account"></strong>
                                    </span>
                                </div>
                                <div class="col-md-6">
                                    <span class="list-group-item">
                                        Процент уникальности работы:
                                        <strong v-html="uniqueness_percentage"></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-10 padding-t10">
                                <label for="">Текст</label>
                                <p class="col-md-12 list-group-item table-responsive" style="max-height: 700px" v-html="text">
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const app = new Vue({
            el: '#main',
            data: function(){
                return {
                    id: 1,
                    author: '',
                    education_program: '',
                    work_name: '',
                    check_time: '',
                    account: '',
                    uniqueness_percentage: '',
                    err_message: null,
                    success_message: null,
                    text: ''
                }
            },
            methods: {
                getPlagiarismResult: function(){
                    this.author = '';
                    this.education_program = '';
                    this.work_name = '';
                    this.check_time = '';
                    this.account = '';
                    this.uniqueness_percentage = '';
                    this.err_message = null
                    this.success_message = null

                    if(this.id !== ""){
                        axios.post('{{route('admin.plagiarism.getResult', ['id' => ''])}}/' + this.id)
                            .then( ({data}) => {
                                this.author = data.author;
                                this.education_program = data.education_program;
                                this.work_name = data.work_name;
                                this.check_time = data.check_time;
                                this.account = data.account;
                                this.uniqueness_percentage = data.uniqueness_percentage;
                                this.success_message = data.message;
                                this.text = data.text;

                            }).catch(err => {
                                this.err_message = err.response.data.message
                        })
                    }
                }
            }
        })
    </script>
@endsection
