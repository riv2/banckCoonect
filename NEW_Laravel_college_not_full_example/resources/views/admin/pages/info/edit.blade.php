@extends("admin.admin_app")

@section("content")
    <div id="main">
        <div class="page-header">
            <h2>{{ isset($info) ? 'Редактировать' : 'Добавить объявление' }}</h2>

            <a href="{{ route('admin.info.get') }}" class="btn btn-default-light btn-xs"><i class="md md-backspace"></i> Назад</a>
        </div>

        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <ul class="nav nav-tabs">
            <li role="presentation" class="tab-ru" v-bind:class="{'active': language=='ru'}" v-on:click="language='ru'"><a href="#">Русский</a></li>
            <li role="presentation" class="tab-kz" v-bind:class="{'active': language=='kz'}" v-on:click="language='kz'"><a href="#">Казахский</a></li>
            <li role="presentation" class="tab-en" v-bind:class="{'active': language=='en'}" v-on:click="language='en'"><a href="#">Английский</a></li>
        </ul>

        <div class="panel panel-default panel-shadow" style="border-radius: 0px 5px 5px 5px;">
            <div class="panel-body">
                <form action="{{ route('admin.info.store', ['info_id' => $info->id ?? 0]) }}" method="post" name="info" enctype="multipart/form-data" class="form-horizontal">
                    @csrf

                    @foreach(['ru', 'kz', 'en'] as $lang)
                        <div v-show="language == '{{ $lang }}'" style="display:none;">
                            <div class="form-group">
                                <label class="col-md-2 control-label">Заголовок</label>

                                <div class="col-md-10">
                                    <input type="text" max="255" name="title_{{ $lang }}" value="{{ $info->{'title_' . $lang} ?? '' }}" class="form-control" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label">Предпросмотр</label>

                                <div class="col-md-10">
                                    <textarea name="text_preview_{{ $lang }}" class="text-preview">{{ $info->{'text_preview_' . $lang} ?? '' }}</textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label">Текст</label>

                                <div class="col-md-10">
                                    <textarea name="text_{{ $lang }}" class="news-text">{{ $info->{'text_' . $lang} ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="form-group">
                        <label for="is_important" class="col-md-2 control-label">Важное</label>

                        <div class="col-md-10">
                            <input type="checkbox" name="is_important" id="is_important" {{ (isset($info) && $info->is_important) ? 'checked' : '' }}>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-offset-2 col-md-10">
                            <button type="submit" class="btn btn-primary">Сохранить</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        var app = new Vue({
            el: '#main',
            data: {
                language: 'ru',
            },
        });

        $(document).ready(function() {
            $('.news-text').summernote({
                height: 450
            });
            $('.text-preview').summernote({
                height: 100
            })
        } );
    </script>
@endsection
