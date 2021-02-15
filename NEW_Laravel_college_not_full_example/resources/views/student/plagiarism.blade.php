@extends('layouts.app')

@section("content")
    <div id="main">
        <div class="page-header text-center">
            <h2>@lang('Plagiarism Checker')</h2>
        </div>

        @if(Session::has('success_message'))
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

                {{ Session::get('success_message') }}
            </div>
        @endif

        <div class="text-center">
            @lang('Notice: You can check only 1 text per day')
        </div>

        @if ($answersToday == 0)
            <div class="panel panel-default panel-shadow">
                <div class="panel-body pb-4">
                    <div class="col-md-6 col-sm-12 offset-md-3">
                        {!! Form::open([
                            'route' => 'student.plagiarism.check',
                            'method' => 'post',
                            'class' => 'form-horizontal',
                            'files' => true]
                        ) !!}
                        <div class="form-group">
                            <label for="name">@lang('Work theme')</label>

                            <input type="text" name="name" id="name" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="compare_method">@lang('Compare Method')</label>

                            <select name="compare_method" id="compare_method" class="form-control">
                                <option value="">@lang('By default')</option>
                                <option value="Shingle" selected>@lang('Copy')</option>
                                <option value="Frequency">@lang('Rewriting')</option>
                                <option value="ShingleFrequency">@lang('Copy and Rewriting')</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="num_samples_per_document">@lang('Samples')</label>

                            <select name="num_samples_per_document" id="num_samples_per_document" class="form-control">
                                <option value="">@lang('By default')</option>
                                <option value="0">@lang('For document')</option>
                                <option value="1" selected>@lang('For each 1000 words')</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="num_samples">@lang('Number of samples')</label>

                            <input type="number" class="form-control" name="num_samples" id="num_samples" value="9">
                        </div>

                        <div class="form-group">
                            <label for="num_ref_per_sample">@lang('Number of links per sample')</label>

                            <input type="number" class="form-control" name="num_ref_per_sample" id="num_ref_per_sample" value="3">
                        </div>

                        <div class="form-group">
                            <label for="num_words_i_shingle">@lang('Number words in shingle')</label>

                            <input type="number" class="form-control" name="num_words_i_shingle" id="num_words_i_shingle" value="3">
                        </div>

                        <div class="form-group">
                            <label for="uniqueness_threshold">@lang('Uniqueness threshold')</label>

                            <input type="number" class="form-control" name="uniqueness_threshold" id="uniqueness_threshold">
                        </div>

                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" name="self_uniq" id="self_uniq">

                            <label for="self_uniq">@lang('Search duplicates')</label>
                        </div>

                        <div class="form-group form-check">
                            <input type="checkbox" checked class="form-check-input" name="ignore_citation" id="ignore_citation">

                            <label for="ignore_citation">@lang('Ignore citation')</label>
                        </div>

                        <div class="form-group">
                            <label for="text">@lang('Text')</label>

                            <textarea type="text" cols="200" rows="10" name="text" id="text" class="form-control"></textarea>
                        </div>

                        <div class="form-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="document" name="document" @change="selectNewDoc">
                                <label class="custom-file-label" for="document">@lang('Document')</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary col-md-auto">@lang('Checks')</button>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        @endif

        <div id="texts_container">
            <texts-block
                    title="@lang('Check text')"
                    :pagination="on_check_texts.pagination"
                    :texts="on_check_texts.texts"
                    :offset="{{ env('ETXT_PAGINATE_OFFSET', 10) }}"
                    @paginate="getOnCheckTexts()">
            </texts-block>

            <texts-block
                    title="@lang('Checked text')"
                    :pagination="success_texts.pagination"
                    :texts="success_texts.texts"
                    :offset="{{ env('ETXT_PAGINATE_OFFSET', 10) }}"
                    @paginate="getSuccessTexts()">
            </texts-block>
        </div>

        <b-modal size="xl" id="generate-pdf" ref="generate-pdf" title=""@lang('Generate statement') hide-footer>
            <form action="{{ route('student.plagiarism.report.pdf') }}" class="form-horizontal" method="post" ref="generatePdfForm">
                @csrf

                <input type="hidden" name="text_id">

                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="text_type" class="col-md-2 control-label">@lang('Work Type')</label>

                        <div class="col-md-10">
                            <select name="text_type" id="text_type" class="form-control" required>
                                @foreach($workTypes as $type => $name)
                                    <option value="{{ $type }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <b-button class="mt-2" variant="primary" block @click="generatePdf">@lang('Download')</b-button>
                <b-button class="mt-3" block @click="hideModal('generate-pdf')">@lang('Close')</b-button>
            </form>
        </b-modal>
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
                        'Shingle': '@lang("Copy")',
                        'Frequency': '@lang("Rewriting")',
                        'ShingleFrequency': '@lang("Copy and Rewriting")'
                    },
                    deleteRoute: "{{ route('student.plagiarism.delete', ['']) }}",
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
                }
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
            template:   `<div class="panel panel-default panel-shadow">
                            <div class="page-header text-center">
                                <h3 class="margin-15" v-html="title"></h3>
                            </div>

                            <div class="card" v-for="text in texts">
                                <div class="card-body col-md-8 col-sm-12 offset-md-2">
                                    <div class="form-group">
                                        <label>@lang('Title')</label>

                                        <strong class="form-control" v-html="text.name"></strong>
                                    </div>

                                    <div class="form-group" v-if="text.uniq != null">
                                        <label>@lang('Uniqueness')</label>

                                        <strong type="text" class="form-control" v-html="text.uniq + '%'"></strong>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-2 control-label">@lang('Text')</label>

                                        <p v-html="text.text" class="list-group-item table-responsive" style="max-height: 400px;"></p>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-6" v-if="text.compare_method != null">
                                            <span class="list-group-item">
                                                @lang('Compare Method') :
                                                <strong v-html="CompareMethod[text.compare_method]"></strong>
                                            </span>
                                        </div>

                                        <div class="col-6" v-if="text.num_samples_per_document != null">
                                            <span class="list-group-item">
                                                @lang('Samples'):
                                                <strong v-html="text.num_samples_per_document ? '@lang('For each 1000 words')' : '@lang('For document')'"></strong>
                                            </span>
                                        </div>

                                        <div class="col-6" v-if="text.num_samples != null">
                                            <span class="list-group-item">
                                                @lang('Number of samples'):
                                                <strong v-html="text.num_samples"></strong>
                                            </span>
                                        </div>

                                        <div class="col-6" v-if="text.num_ref_per_sample != null">
                                            <span class="list-group-item">
                                                @lang('Number of links per sample'):
                                                <strong v-html="text.num_ref_per_sample"></strong>
                                            </span>
                                        </div>

                                        <div class="col-6" v-if="text.num_words_i_shingle != null">
                                            <span class="list-group-item">
                                                @lang('Number words in shingle'):
                                                <strong v-html="text.num_words_i_shingle"></strong>
                                            </span>
                                        </div>

                                        <div class="col-6" v-if="text.uniqueness_threshold != null">
                                            <span class="list-group-item">
                                                @lang('Uniqueness threshold'):
                                                <strong v-html="text.uniqueness_threshold"></strong>
                                            </span>
                                        </div>

                                        <div class="col-6" v-if="text.self_uniq != null">
                                            <span class="list-group-item">
                                                @lang('Search duplicates'):
                                                <strong v-html="text.self_uniq ? '@lang('Yes')' : '@lang('No')'"></strong>
                                            </span>
                                        </div>

                                        <div class="col-6" v-if="text.ignore_citation != null">
                                            <span class="list-group-item">
                                                @lang('Ignore citation'):
                                                <strong v-html="text.ignore_citation ? '@lang('Yes')' : '@lang('No')'"></strong>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="form-group" v-if="text.uniq != null">
                                        <b-button block variant="primary" @click="showGeneratePDFModal(text.id)" v-b-modal.generate-pdf>@lang('Certificate of the results of checking the written work for plagiarism')</b-button>
                                    </div>

                                    <div class="form-group" v-else>
                                        <b-link :href="deleteRoute + '/' + text.id" class="btn btn-danger btn-block">@lang('Delete')</b-link>
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
        });

        const app = new Vue({
            el: '#main',
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
                    text_id: 0,
                }
            },
            components: {
                'texts-block': textsBlock,
            },
            methods: {
                getOnCheckTexts() {
                    axios.get(`{{route('student.plagiarism.texts.oncheck')}}?page=${this.on_check_texts.pagination.current_page}`)
                        .then(res => {
                            this.on_check_texts.texts = res.data.data.data;
                            this.on_check_texts.pagination = res.data.pagination
                        })
                        .catch(error => {
                            console.error(error.response.data);
                        });
                },
                getSuccessTexts() {
                    axios.get(`{{route('student.plagiarism.texts.success')}}?page=${this.success_texts.pagination.current_page}`)
                        .then(res => {
                            this.success_texts.texts = res.data.data.data;
                            this.success_texts.pagination = res.data.pagination
                        })
                        .catch(error => {
                            console.error(error.response.data);
                        });
                },
                selectNewDoc(event) {
                    document.getElementsByClassName('custom-file-label').item(0).innerText = event.target.files[0].name;
                },
                generatePdf() {
                    this.$refs.generatePdfForm.text_id.value = this.text_id;
                    this.$refs.generatePdfForm.submit();
                },
                hideModal(modalID) {
                    this.$refs[modalID].hide()
                }
            },
            mounted() {
                this.getOnCheckTexts();
                this.getSuccessTexts();
            }
        })
    </script>
@endsection
