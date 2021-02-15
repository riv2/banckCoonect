@extends("admin.admin_app")

@section("content")
    <div id="main">
        <div class="page-header">
            <h2>Проверка уникальности</h2>
        </div>

        @if(Session::has('flash_message'))
            <div class="alert alert-danger">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                {{ Session::get('flash_message') }}
            </div>
        @endif

        @if(Session::has('success_message'))
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

                {{ Session::get('success_message') }}
            </div>
        @endif

        <div class="panel panel-default panel-shadow">
            <div class="panel-body">
                <div class="col-md-6">
                    {!! Form::open(['route' => 'etxtAntiPlagiatSend', 'method' => 'post', 'class' => 'form-horizontal', 'files' => true]) !!}
                        <div class="form-group">
                            <label for="" class="col-md-2 control-label">Тема работы</label>
                            <div class="col-md-10">
                                <input type="text" max="255" name="name" class="form-control" />
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-5 col-md-offset-2">
                                <label for="" class="control-label">Метод обнаружения</label>
                                <div>
                                    <select name="compare_method" class="form-control">
                                        <option value="">По умолчанию</option>
                                        <option value="Shingle" selected>Копии</option>
                                        <option value="Frequency">Рерайт</option>
                                        <option value="ShingleFrequency">Копии и Рерайт</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <label for="" class="control-label">На весь документ или на каждую 1000 слов</label>
                                <div>
                                    <select name="num_samples_per_document" class="form-control">
                                        <option value="">По умолчанию</option>
                                        <option value="0">Весь документ</option>
                                        <option value="1" selected>Каждые 1000 слов</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-5 col-md-offset-2">
                                <label for="">Число выборок</label>
                                <input type="number" class="form-control" name="num_samples" value="9"/>
                            </div>
                            <div class="col-md-5">
                                <label for="">Число ссылок на выборку</label>
                                <input type="number" class="form-control" name="num_ref_per_sample" value="3"/>
                            </div>
                            <div class="col-md-5 col-md-offset-2">
                                <label for="">Число слов в шингле</label>
                                <input type="number" class="form-control" name="num_words_i_shingle" value="3"/>
                            </div>
                            <div class="col-md-5">
                                <label for="">Порог уникальности</label>
                                <input type="number" class="form-control" name="uniqueness_threshold"/>
                            </div>
                            <div class="col-md-5 col-md-offset-2 ">
                                <label for="" class="col-md-7 control-label margin-t5">Искать дубли</label>
                                <div class="col-md-1">
                                    <input type="checkbox" class="ckeckbox margin-t15" name="self_uniq"/>
                                </div>
                            </div>
                            <div class="col-md-5 ">
                                <label for="" class="col-md-7 control-label margin-t5">Игнорировать цитаты</label>
                                <div class="col-md-1">
                                    <input type="checkbox" checked class="ckeckbox margin-t15" name="ignore_citation"/>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-md-2 control-label">Текст</label>
                            <div class="col-md-10">
                                <textarea type="text" max="255" cols="200" rows="10" name="text" class="form-control" > </textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-md-2 control-label">Документ</label>
                            <div class="col-md-10">
                                <input type="file" name="document" class="filestyle">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-10 col-md-offset-2">
                                <button type="submit" class="btn btn-primary ">Проверка</button>
                            </div>
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
            <div class="clearfix"></div>
        </div>

        <div id="texts_container">
            <texts-block
                    title="Текст на проверке"
                    :pagination="on_check_texts.pagination"
                    :texts="on_check_texts.texts"
                    :offset="{{ env('ETXT_PAGINATE_OFFSET', 10) }}"
                    @paginate="getOnCheckTexts()">
            </texts-block>

            <texts-block
                    title="Проверенный текст"
                    :pagination="success_texts.pagination"
                    :texts="success_texts.texts"
                    :offset="{{ env('ETXT_PAGINATE_OFFSET', 10) }}"
                    @paginate="getSuccessTexts()">
            </texts-block>

            <div class="modal" tabindex="-1" role="dialog" aria-labelledby="" id="generate_pdf" :class="{show:showGeneratePDFModalProcess}">
                <div class="modal-dialog modal-lg" style="min-width:950px;" role="document">
                    <form action="{{ route('etxt.report.pdf') }}" class="form-horizontal" method="post" ref="generatePdfForm">
                        @csrf

                        <input type="hidden" name="text_id">

                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" @click="showGeneratePDFModalProcess = false">
                                    <span aria-hidden="true">&times;</span>
                                </button>

                                <h4 class="modal-title">Сгенерировать ведомость</h4>
                            </div>

                            <div class="modal-body col-sm-12">
                                <div class="form-group">
                                    <label for="author" class="col-md-2 control-label">Автор работы</label>

                                    <div class="col-md-10">
                                        <input type="text" name="author" id="author" class="form-control" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="text_type" class="col-md-2 control-label">Вид работы</label>

                                    <div class="col-md-10">
                                        <select name="text_type" id="text_type" class="form-control" required>
                                            @foreach($workTypes as $type => $name)
                                                <option value="{{ $type }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button class="btn btn-primary btn-lg" @click="generatePdf" type="submit">Скачать</button>
                                <button class="btn btn-link" @click="showGeneratePDFModalProcess = false" type="button">Отмена</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const textsBlock = Vue.component('texts_block', {
            props: [
                'pagination',
                'offset',
                'texts',
                'title',
                'paginate'
            ],
            data: function(){
                return {
                    'CompareMethod': {
                        'Shingle': 'Копии',
                        'Frequency': 'Рерайт',
                        'ShingleFrequency': 'Копии и Рерайт'
                    },
                    deleteRoute: "{{ route('etxt.remove', ['']) }}",
                }
            },
            methods: {
                isCurrentPage(page) {
                    if(this.pagination.current_page === page){
                        return true
                    } else {
                        return false
                    }
                },
                changePage(page) {
                    if (page > this.pagination.last_page) {
                        page = this.pagination.last_page;
                    }
                    this.pagination.current_page = page;
                    this.$emit('paginate');
                },
                showGeneratePDFModal(text_id) {
                    app.text_id = text_id;
                    app.showGeneratePDFModalProcess = true;
                },
            },
            computed: {
                pages() {
                    let pages = [];
                    let from = this.pagination.current_page - Math.floor(this.offset / 2);
                    if (from < 1) {
                        from = 1;
                    }
                    let to = from + this.offset - 1;
                    if (to > this.pagination.last_page) {
                        to = this.pagination.last_page;
                    }
                    while (from <= to) {
                        pages.push(from);
                        from++;
                    }
                    return pages;
                }
            },
            template: `<div class="panel panel-default panel-shadow">
                             <div class="page-header">
                                <h3 class="margin-15" v-html="title"></h3>
                             </div>
                                <div class="col-md-12 margin-b30" v-for="text in texts">
                                    <div class="col-md-10 form-horizontal">
                                        <div class="form-group">
                                            <label class="col-md-2 control-label">Название</label>
                                            <div class="col-md-10">
                                                <strong class="form-control" v-html="text.name"></strong>
                                            </div>
                                        </div>

                                        <div class="form-group" v-if="text.uniq != null">
                                            <label class="col-md-2 control-label">Уникальность</label>
                                            <div class="col-md-10">
                                                <strong type="text" class="form-control" v-html="text.uniq + '%'"></strong>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-2 control-label">Текст</label>
                                            <div class="col-md-10">
                                                <p v-html="text.text" class="list-group-item table-responsive" style="max-height: 400px;">>
                                                </p>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="col-md-10 padding-0 col-md-offset-2">
                                                <div class="col-md-6" v-if="text.compare_method != null">
                                                    <span class="list-group-item">
                                                        Метод обнаружения :
                                                        <strong v-html="CompareMethod[text.compare_method]"></strong>
                                                    </span>
                                                </div>
                                                <div class="col-md-6" v-if="text.num_samples_per_document != null">
                                                    <span class="list-group-item">
                                                        На весь документ или на каждую 1000 слов:
                                                        <strong v-html="text.num_samples_per_document ? 'Каждые 1000 слов' : 'Весь документ'"></strong>
                                                    </span>
                                                </div>
                                                <div class="col-md-6" v-if="text.num_samples != null">
                                                    <span class="list-group-item">
                                                        Число выборок:
                                                        <strong v-html="text.num_samples"></strong>
                                                    </span>
                                                </div>
                                                <div class="col-md-6" v-if="text.num_ref_per_sample != null">
                                                    <span class="list-group-item">
                                                        Число ссылок на выборку:
                                                        <strong v-html="text.num_ref_per_sample"></strong>
                                                    </span>
                                                </div>
                                                <div class="col-md-6" v-if="text.num_words_i_shingle != null">
                                                    <span class="list-group-item">
                                                        Число слов в шингле:
                                                        <strong v-html="text.num_words_i_shingle"></strong>
                                                    </span>
                                                </div>
                                                <div class="col-md-6" v-if="text.uniqueness_threshold != null">
                                                    <span class="list-group-item">
                                                        Порог уникальности:
                                                        <strong v-html="text.uniqueness_threshold"></strong>
                                                    </span>
                                                </div>
                                                <div class="col-md-6" v-if="text.self_uniq != null">
                                                    <span class="list-group-item">
                                                        Искать дубли:
                                                        <strong v-html="text.self_uniq ? 'Да' : 'Нет'"></strong>
                                                    </span>
                                                </div>
                                                <div class="col-md-6" v-if="text.ignore_citation != null">
                                                    <span class="list-group-item">
                                                        Игнорировать цитаты:
                                                        <strong v-html="text.ignore_citation ? 'Да' : 'Нет'"></strong>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group" v-if="text.uniq != null">
                                            <div class="col-md-offset-2 col-md-10">
                                                <button class="btn btn-primary" @click="showGeneratePDFModal(text.id)">Ведомость</button>
                                            </div>
                                        </div>

                                        <div class="form-group" v-else>
                                            <div class="col-md-offset-2 col-md-10">
                                                <a :href="deleteRoute + '/' + text.id" class="btn btn-danger">Удалить</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <nav class="d-block text-center padding-10">
                                   <div class="btn-group">
                                        <button class="btn btn-default"
                                                @click="changePage(pagination.current_page - 1)"
                                                :disabled="pagination.current_page <= 1">
                                                &laquo;
                                        </button>
                                        <button v-for="page in pages"
                                                class="btn"
                                                :class="isCurrentPage(page) ? 'btn-primary' : 'btn-default'"
                                                @click="changePage(page)"
                                                v-html="page">
                                        </button>
                                        <button class="btn btn-default"
                                                @click="changePage(pagination.current_page + 1)"
                                                :disabled="pagination.current_page >= pagination.last_page">
                                                &raquo;
                                        </button>
                                    </div>
                                </nav>
                       </div>`
        })

        const app = new Vue({
            el: '#texts_container',
            data: function(){
                return{
                    on_check_texts: {
                        texts: '',
                        pagination: {
                            current_page : 1
                        }
                    },
                    success_texts: {
                        texts: '',
                        pagination: {
                            current_page : 1
                        }
                    },
                    showGeneratePDFModalProcess: false,
                    text_id: 0,
                }
            },
            components: {
                'texts-block': textsBlock,
            },
            methods: {
                getOnCheckTexts() {
                    axios.get(`{{route('etxtGetTextsOnCheck')}}?page=${this.on_check_texts.pagination.current_page}`)
                        .then(res => {
                            this.on_check_texts.texts = res.data.data.data;
                            this.on_check_texts.pagination = res.data.pagination
                        })
                        .catch(error => {
                            console.error(error.response.data);
                        });
                },
                getSuccessTexts() {
                    axios.get(`{{route('etxtGetTextSuccess')}}?page=${this.success_texts.pagination.current_page}`)
                        .then(res => {
                            this.success_texts.texts = res.data.data.data;
                            this.success_texts.pagination = res.data.pagination
                        })
                        .catch(error => {
                            console.error(error.response.data);
                        });
                },
                generatePdf() {
                    this.$refs.generatePdfForm.text_id.value = this.text_id;
                    this.$refs.generatePdfForm.submit();
                }
           },
            mounted() {
                this.getOnCheckTexts();
                this.getSuccessTexts();
            }
        })
    </script>
@endsection
