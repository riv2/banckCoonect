@extends("admin.admin_app")

@section("content")
    <div id="main">
        @if(Session::has('manual_success_add'))
            <div class="margin-top alert alert-success" role="alert">
                {{ Session::get('manual_success_add') }}
            </div>
        @endif
        <div class="page-header">
            <h2>Добавление записи в каталог литературы</h2>
        </div>
        <div class="row">
            <div class="col-md-10">
                <a href="{{ route('library.page') }}" class="btn btn-default-light btn-xs"><i class="md md-backspace"></i> Назад</a>
            </div>
            <div class="col-md-2">
                <a href="{{ route('knowledge.section.page') }}" class="btn btn-primary btn-block">Раздел знаний</a>
            </div>
        </div>
        
        
        {!! Form::open([
            'url' => isset($literature)? route('edit.literature.to.catalog') : route('add.literature.to.catalog'),
            'enctype' => 'multipart/form-data'
        ]) !!}
            <input type="hidden" name="literature_id" value="{{ $literature->id?? '' }}">
            <div class="row margin-t30">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Наименование</label>
                        <input 
                            type="text" 
                            name="catalog[name]" 
                            class="form-control" 
                            placeholder="наименование" 
                            value="{{ $literature->name?? old('catalog.name') }}"
                        >
                        @if(!empty($errors->first('catalog.name')))
                            <span class="invalid-feedback">
                                {{ $errors->first('catalog.name') }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Носитель</label>
                        <select class="form-control" name="catalog[media]">
                            @foreach(App\LibraryLiteratureCatalog::$media as $media)
                                <option 
                                    value="{{ $media }}" 
                                    @if(isset($literature->media) && $literature->media == $media)
                                        {{ 'selected' }} 
                                    @elseif(null !== old('catalog.media') && old('catalog.media') == $media)
                                        {{ 'selected' }} 
                                    @endif
                                >
                                    {{ $media }}
                                </option>
                            @endforeach
                        </select>
                        @if(!empty($errors->first('catalog.media')))
                            <span class="invalid-feedback">
                                {{ $errors->first('catalog.media') }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Вид литературы</label>
                        <select class="form-control" name="catalog[literature_type]">
                            @foreach(App\LibraryLiteratureCatalog::$literature_type as $literature_type)
                                <option 
                                    value="{{ $literature_type }}" 
                                    @if(isset($literature->literature_type) && $literature->literature_type == $literature_type)
                                        {{ 'selected' }} 
                                    @elseif(null !== old('catalog.literature_type') && old('catalog.literature_type') == $literature_type)
                                        {{ 'selected' }} 
                                    @endif
                                >
                                    {{ $literature_type }}
                                </option>
                            @endforeach
                        </select>
                        @if(!empty($errors->first('catalog.literature_type')))
                            <span class="invalid-feedback">
                                {{ $errors->first('catalog.literature_type') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Вид издания</label>
                        <select class="form-control" name="catalog[publication_type]">
                            @foreach(App\LibraryLiteratureCatalog::$publication_type as $publication_type)
                                <option 
                                    value="{{ $publication_type }}"
                                    @if(isset($literature->publication_type) && $literature->publication_type == $publication_type)
                                        {{ 'selected' }}
                                    @elseif(null !== old('catalog.publication_type') && old('catalog.publication_type') == $publication_type)
                                        {{ 'selected' }}
                                    @endif
                                >
                                    {{ $publication_type }}
                                </option>
                            @endforeach
                        </select>
                        @if(!empty($errors->first('catalog.publication_type')))
                            <span class="invalid-feedback">
                                {{ $errors->first('catalog.publication_type') }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Год издания</label>
                        <input 
                            type="date" 
                            name="catalog[publication_year]" 
                            class="form-control" 
                            value="{{ isset($literature->publication_year)? $literature->publication_year.'-01-01' : old('catalog.publication_year') }}"
                        >
                        @if(!empty($errors->first('catalog.publication_year')))
                            <span class="invalid-feedback">
                                {{ $errors->first('catalog.publication_year') }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>ISBN</label>
                        <input 
                            type="text" 
                            name="catalog[isbn]" 
                            class="form-control" 
                            placeholder="ISBN" 
                            value="{{ $literature->isbn?? old('catalog.isbn') }}"
                        >
                        @if(!empty($errors->first('catalog.isbn')))
                            <span class="invalid-feedback">
                                {{ $errors->first('catalog.isbn') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>УДК</label>
                        <input 
                            type="text" 
                            name="catalog[ydk]" 
                            class="form-control" 
                            placeholder="УДК" 
                            value="{{ $literature->ydk?? old('catalog.ydk') }}"
                        >
                        @if(!empty($errors->first('catalog.ydk')))
                            <span class="invalid-feedback">
                                {{ $errors->first('catalog.ydk') }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>ББК</label>
                        <input 
                            type="text" 
                            name="catalog[bbk]" 
                            class="form-control" 
                            placeholder="ББК" 
                            value="{{ $literature->bbk?? old('catalog.bbk') }}"
                        >
                        @if(!empty($errors->first('catalog.bbk')))
                            <span class="invalid-feedback">
                                {{ $errors->first('catalog.bbk') }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Автор</label>
                        <input 
                            type="text" 
                            name="catalog[author]" 
                            class="form-control" 
                            placeholder="автор" 
                            value="{{ $literature->author?? old('catalog.author') }}"
                        >
                        @if(!empty($errors->first('catalog.author')))
                            <span class="invalid-feedback">
                                {{ $errors->first('catalog.author') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Другие авторы</label>
                        <input 
                            type="text" 
                            name="catalog[more_authors]" 
                            class="form-control" 
                            placeholder="другие авторы" 
                            value="{{ $literature->more_authors?? old('catalog.more_authors') }}"
                        >
                        @if(!empty($errors->first('catalog.more_authors')))
                            <span class="invalid-feedback">
                                {{ $errors->first('catalog.more_authors') }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Раздел знаний</label>
                        <select class="form-control selectpicker" name="knowledge_section[]" multiple>
                            @foreach($knowledge_section as $value)
                                <option 
                                    value="{{ $value->id }}"
                                    @if(!empty($literature->knowledgeSections))
                                        @foreach($literature->knowledgeSections as $knowledge)
                                            @if($knowledge->id == $value->id) {{ 'selected' }} @endif
                                        @endforeach
                                    @else
                                        @if(null !== old('knowledge_section') && old('knowledge_section') == $value->id) {{ 'selected' }} @endif
                                    @endif
                                >
                                    {{ $value->name }}
                                </option>
                            @endforeach
                        </select>
                        @if(!empty($errors->first('knowledge_section')))
                            <span class="invalid-feedback">
                                {{ $errors->first('knowledge_section') }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Язык</label>
                        <select class="form-control" name="catalog[language]">
                            @foreach(App\LibraryLiteratureCatalog::$language as $language)
                                <option 
                                    value="{{ $language }}"
                                    @if(isset($literature->language) && $literature->language == $language) 
                                        {{ 'selected' }} 
                                    @elseif(null !== old('catalog.language') && old('catalog.language') == $language)
                                        {{ 'selected' }} 
                                    @endif
                                >
                                    {{ $language }}
                                </option>
                            @endforeach
                        </select>
                        @if(!empty($errors->first('catalog.language')))
                            <span class="invalid-feedback">
                                {{ $errors->first('catalog.language') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Количество страниц</label>
                        <input 
                            type="text" 
                            name="catalog[number_pages]" 
                            class="form-control" 
                            placeholder="количество страниц" 
                            value="{{ $literature->number_pages?? old('catalog.number_pages') }}"
                        >
                        @if(!empty($errors->first('catalog.number_pages')))
                            <span class="invalid-feedback">
                                {{ $errors->first('catalog.number_pages') }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Ключевые слова</label>
                        <input 
                            type="text" 
                            name="catalog[key_words]" 
                            class="form-control" 
                            placeholder="ключевые слова" 
                            value="{{ $literature->key_words?? old('catalog.key_words') }}"
                        >
                        @if(!empty($errors->first('catalog.key_words')))
                            <span class="invalid-feedback">
                                {{ $errors->first('catalog.key_words') }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Стоимость</label>
                        <input 
                            type="text" 
                            name="catalog[cost]" 
                            class="form-control" 
                            placeholder="стоимость" 
                            value="{{ $literature->cost?? old('catalog.cost') }}"
                        >
                        @if(!empty($errors->first('catalog.cost')))
                            <span class="invalid-feedback">
                                {{ $errors->first('catalog.cost') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Дата  поступления</label>
                        <input 
                            type="date" 
                            name="catalog[receipt_date]" 
                            class="form-control" 
                            value="{{ $literature->receipt_date?? old('catalog.receipt_date') }}"
                        >
                        @if(!empty($errors->first('catalog.receipt_date')))
                            <span class="invalid-feedback">
                                {{ $errors->first('catalog.receipt_date') }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Источник поступления</label>
                        <input 
                            type="text" 
                            name="catalog[source_income]" 
                            class="form-control" 
                            placeholder="Источник поступления" 
                            value="{{ $literature->source_income?? old('catalog.source_income') }}"
                        >
                        @if(!empty($errors->first('catalog.source_income')))
                            <span class="invalid-feedback">
                                {{ $errors->first('catalog.source_income') }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Подгрузка элеткронной книги
                            @if(isset($literature) && isset($literature->e_books_name))
                                <a
                                    href="{{ route('library.download.file', ['name' => $literature->e_books_name]) }}"
                                    class="btn btn-default"
                                    data-toggle="tooltip"
                                    data-placement="top"
                                    title="Скачать файл"
                                >
                                    <i class="fa fa-download"></i>
                                </a>
                            @endif
                        </label>
                        <input type="file" name="catalog[e_books_name]" class="form-control" placeholder="стоимость">
                        @if(!empty($errors->first('catalog.e_books_name')))
                            <span class="invalid-feedback">
                                {{ $errors->first('catalog.e_books_name') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Издатель</label>
                        <input 
                            type="text" 
                            name="catalog[publisher]" 
                            class="form-control" 
                            placeholder="издатель" 
                            value="{{ $literature->publisher?? old('catalog.publisher') }}"
                        >
                        @if(!empty($errors->first('catalog.publisher')))
                            <span class="invalid-feedback">
                                {{ $errors->first('catalog.publisher') }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Место издания</label>
                        <input 
                            type="text" 
                            name="catalog[publication_place]" 
                            class="form-control" 
                            placeholder="место издания" 
                            value="{{ $literature->publication_place?? old('catalog.publication_place') }}"
                        >
                        @if(!empty($errors->first('catalog.publication_place')))
                            <span class="invalid-feedback">
                                {{ $errors->first('catalog.publication_place') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <h5>Дисциплина</h5>
                    <div class="panel panel-default panel-shadow margin-top">
                        <div class="panel-body">
                            <table 
                                id="disciplineDatatable" 
                                class="table table-striped table-hover dt-responsive" 
                                style="table-layout: fixed;" 
                                cellspacing="0" 
                                width="100%"
                            >
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Наименование</th>
                                        <th class="text-center width-150">Действие</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Сохранить</button>
        {!! Form::close() !!}
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $('.selectpicker').selectpicker({
                dropupAuto: false
            });
        });

        let dataTable = $('#disciplineDatatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: { 
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('library.catalog.discipline.datatable') }}",
                type: "post",
                data: {
                    id: {{ $literature->id?? 0 }}
                }
            },
            columns: [
                { data: 'id', width: "25px" },
                { data: 'name', width: "300px" },
                { data: 'action', width: "50px" }
            ],
            "drawCallback": function( settings ) {
                $('[data-toggle="tooltip"]').tooltip();
            },
            initComplete: function () {
                $('[data-toggle="tooltip"]').tooltip();
                this.api().columns().every(function () {
                    var column = this;
                    var input = document.createElement("input");
                    $(input).appendTo($(column.footer()).empty())
                    .on('change', function () {
                        column.search($(this).val(), false, false, true).draw();
                    });
                });
            }
        });
    </script>
@endsection