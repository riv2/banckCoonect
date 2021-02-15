@extends('layouts.app')

@section('title', __('Library'))

@section('content')
    <section class="content" id="main-test-form">
        <div class="container-fluid">
            <div class="p-3 mb-2 bg-info">
                <h2 class="text-white no-margin">{{ $literature->name }}</h2>
            </div>

            <div class="card shadow-sm p-3 bg-white rounded">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group row">
                            <label class="col-sm-2">Носитель:</label>
                            <div class="col-sm-10">
                                <span class="align-middle">{{ $literature->media }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2">Вид литературы:</label>
                            <div class="col-sm-10">
                                <span class="align-middle">{{ $literature->literature_type }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2">Вид издания:</label>
                            <div class="col-sm-10">
                                <span class="align-middle">{{ $literature->publication_type }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2">Год издания:</label>
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
                            <label class="col-sm-2">Автор:</label>
                            <div class="col-sm-10">
                                <span class="align-middle">{{ $literature->author }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2">Другие авторы:</label>
                            <div class="col-sm-10">
                                <span class="align-middle">{{ $literature->more_authors }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2">Раздел знаний:</label>
                            <div class="col-sm-10">
                                <span class="align-middle">{{ $literature->q }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2">Язык:</label>
                            <div class="col-sm-10">
                                <span class="align-middle">{{ $literature->language }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2">Количество страниц:</label>
                            <div class="col-sm-10">
                                <span class="align-middle">{{ $literature->number_pages }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2">Ключевые слова:</label>
                            <div class="col-sm-10">
                                <span class="align-middle">{{ $literature->key_words }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2">Дисциплина:</label>
                            <div class="col-sm-10">
                                <span class="align-middle">{{ $literature->q }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2">Стоимость:</label>
                            <div class="col-sm-10">
                                <span class="align-middle">{{ $literature->cost }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2">Дата  поступления:</label>
                            <div class="col-sm-10">
                                <span class="align-middle">{{ $literature->receipt_date }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2">Источник поступления:</label>
                            <div class="col-sm-10">
                                <span class="align-middle">{{ $literature->source_income }}</span>
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
                        Заказать
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