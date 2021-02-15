@extends('layouts.app')

@section('title', __('Library'))

@section('content')
    <section class="content" id="main-test-form">
        <div class="container-fluid">
            <div class="p-3 mb-2 bg-info">
                <h2 class="text-white no-margin">{{ $literature->name }}</h2>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <a href="{{ route('library.page') }}" class="btn btn-default btn-xs">
                        <i class="far fa-caret-square-left fa-2x"></i> <span class="align-super">@lang('Back')</span>
                    </a>
                </div>
            </div>

            <div class="card shadow-sm p-3 bg-white rounded margin-t10">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group row">
                            <label class="col-sm-2">@lang('Media'):</label>
                            <div class="col-sm-10">
                                <span class="align-middle">{{ $literature->media }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2">@lang('Literature type'):</label>
                            <div class="col-sm-10">
                                <span class="align-middle">{{ $literature->literature_type }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2">@lang('Publication type'):</label>
                            <div class="col-sm-10">
                                <span class="align-middle">{{ $literature->publication_type }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2">@lang('Publication year'):</label>
                            <div class="col-sm-10">
                                <span class="align-middle">{{ $literature->publication_year }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2">ISBN:</label>
                            <div class="col-sm-10">
                                <span class="align-middle">{{ $literature->isbn }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2">УДК:</label>
                            <div class="col-sm-10">
                                <span class="align-middle">{{ $literature->ydk }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2">ББК:</label>
                            <div class="col-sm-10">
                                <span class="align-middle">{{ $literature->bbk }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2">@lang('Author'):</label>
                            <div class="col-sm-10">
                                <span class="align-middle">{{ $literature->author }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2">@lang('More authors'):</label>
                            <div class="col-sm-10">
                                <span class="align-middle">{{ $literature->more_authors }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2">@lang('Knowledge section'):</label>
                            <div class="col-sm-10">
                                <span class="align-middle">{{ $literature->q }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2">@lang('Language'):</label>
                            <div class="col-sm-10">
                                <span class="align-middle">{{ $literature->language }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2">@lang('Pages count'):</label>
                            <div class="col-sm-10">
                                <span class="align-middle">{{ $literature->number_pages }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2">@lang('Key words'):</label>
                            <div class="col-sm-10">
                                <span class="align-middle">{{ $literature->key_words }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2">@lang('Discipline'):</label>
                            <div class="col-sm-10">
                                <span class="align-middle">{{ $literature->q }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2">@lang('Price'):</label>
                            <div class="col-sm-10">
                                <span class="align-middle">{{ $literature->cost }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2">@lang('Receipt date'):</label>
                            <div class="col-sm-10">
                                <span class="align-middle">{{ $literature->receipt_date }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2">@lang('Source of income'):</label>
                            <div class="col-sm-10">
                                <span class="align-middle">{{ $literature->source_income }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2">@lang('Publisher'):</label>
                            <div class="col-sm-10">
                                <span class="align-middle">{{ $literature->publisher }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2">@lang('Publication place'):</label>
                            <div class="col-sm-10">
                                <span class="align-middle">{{ $literature->publication_place }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button 
                        @click="sendRequest($event)" 
                        class="btn btn-primary" 
                        id="{{ $literature->id }}"
                    >
                        @lang('Order')
                    </button>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script type="text/javascript">
    var main = new Vue({
        el: '#main-test-form',
        methods: {
            sendRequest(event) {
                Swal.fire({
                    title: 'Готово!',
                    text: 'Ваша заявка отправлена, обратитесь в библиотеку по адресу: улица Сапак Датка, 2',
                    icon: 'success',
                    confirmButtonText: 'Закрыть'
                }).then(confirmButtonText => {});

                const data = {};
                data['literature_id'] = event.target.id;
                axios.post('{{ route('literature.order') }}', data)
                    .then(response => {
                        //
                    });
            }

        }
    });

</script>
@endsection