@extends("admin.admin_app")

@section("content")
    <div id="main">
        <div class="page-header">
            <div class="row">
                <div class="col-md-10">
                    <h2>Редактирование Приказа</h2>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('download.decree', ['name' => $decree->name]) }}" class="btn btn-primary btn-block margin-t20">Скачать приказ</a>
                    @if($decree->is_signed == false)
                        <button v-on:click="openModal" class="btn btn-primary btn-block margin-t20">Загрузить приказ</button>
                    @endif
                </div>
            </div>
        </div>

        <div class="form-group row">
            <label for="staticEmail" class="col-sm-1 col-form-label">Приказ:</label>
            <div class="col-sm-9">
                <p>{{ $decree->name }}</p>
            </div>
        </div>
        <div class="form-group row">
            <label for="inputPassword" class="col-sm-1 col-form-label">Содержание:</label>
            <div class="col-sm-9">
                <p>{{ $text }}</p>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="uploadDecreeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    {!! Form::open([
                        'url' => route('upload.decree'),
                        'enctype' => 'multipart/form-data'
                    ]) !!}
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title" id="myModalLabel">Загрузка подписанного приказа</h4>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="decree_id" value="{{ $decree->id }}">
                            <label>Прикрепите файл:</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="decree">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal" id="modalClose">Закрыть</button>
                            <button type="submit" class="btn btn-primary" id="linkPositions" data-new="true">Импортировать</button>
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<!-- <script src="https://cdn.ckeditor.com/ckeditor5/12.2.0/classic/ckeditor.js"></script> -->
<script type="text/javascript">
    var main = new Vue({
            el: '#main',
            methods: {
                openModal: function (event) {
                    $('#uploadDecreeModal').modal('show');
                }
            }
        });
    $(document).ready(function(){
        // ClassicEditor.create( document.querySelector( '#overview' ), {
        //     // toolbar: [ 'bold', 'bulletedList', 'numberedList' ],
        //     // removePlugins: [ 'Heading', 'Link', 'Autoformat', 'BlockQuote', 'Image', 'ImageCaption', 'ImageStyle', 'ImageToolbar', 'EasyImage', 'ImageUpload', 'UploadAdapter' ]
        // }).then( editor => {
        //     overview = editor;
        // }).catch( error => {});
    });
</script>
@endsection