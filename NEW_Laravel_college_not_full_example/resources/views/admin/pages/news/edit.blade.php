@extends("admin.admin_app")

@section("content")

    <div id="main">
        <div class="page-header">
            <h2> {{ isset($newsModel) ? 'Редактировать: '. $newsModel->title : 'Добавить объявление' }}</h2>

            <a href="{{ URL::to('/news') }}" class="btn btn-default-light btn-xs"><i class="md md-backspace"></i> Назад</a>

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
        @if(Session::has('flash_message'))
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                {{ Session::get('flash_message') }}
            </div>
        @endif

        <div class="panel panel-default" id="main-panel">
            <div class="panel-body">
                <form action="" method="post" name="news" enctype="multipart/form-data" class="form-horizontal">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="form-group">
                        <label for="" class="col-md-2 control-label">Заголовок</label>
                        <div class="col-md-10">
                            <input type="text" max="255" name="title" value="{{ isset($newsModel->title) ? $newsModel->title : null }}" class="form-control" required />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-md-2 control-label">Текст</label>
                        <div class="col-md-10">
                            <textarea name="text" id="news-text">{{ $newsModel->text }}</textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-2"></div>
                        <div class="col-md-10">
                            <button type="submit" class="btn btn-primary">Сохранить</button>
                        </div>
                    </div>
                </form>
            </div>
            @endsection

@section('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#news-text').summernote({
                height: 450
            });

            $('#news-text').on('summernote.change', function(e) {
                $('#news-text').val($('#notificationText').summernote().code());
            });

            $('#news-text').on('summernote.blur', function(e) {
                $('#news-text').val($('#notificationText').summernote().code());
            });
        } );
    </script>
@endsection