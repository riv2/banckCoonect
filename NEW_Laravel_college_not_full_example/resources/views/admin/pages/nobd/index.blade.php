@extends("admin.admin_app")

@section("content")
    <div>
        <div class="page-header">
            <h2>Экспорт: студенты</h2>
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
                <div class="col-md-6">
                    <form class="form-inline" action="{{ route('downloadStudentsList') }}" method="get">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                        <div class="form-group">

                            <label>Год поступления:</label>
                            <select class="form-control" name="year">
                                <option value="0" disabled selected>Выберите год</option>
                                @for($year = date('Y', time()); $year >= 2015; $year--)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endfor
                            </select>
                            <button type="submit" class="btn btn-primary">Экспорт</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div id="main">
        <div class="page-header">
            <h2>Импорт: студенты</h2>
        </div>

        <div class="panel panel-default panel-shadow">
            <div class="panel-body">
                <div class="col-md-12">
                    <form action="{{ route('uploadStudentsList') }}" method="post" id="studentsUploadForm" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-2">
                                <label>Логин:</label>
                                <input type="text" name="login" class="form-control" placeholder="Логин:">
                            </div>
                            <div class="col-md-2">
                                <label>Пароль:</label>
                                <input type="text" name="password" class="form-control" placeholder="Пароль:">
                            </div>
                            <div class="col-md-2">
                                <label>API URL:</label>
                                <input type="text" name="url" class="form-control" placeholder="https://">
                            </div>
                            <div class="col-md-2">
                                <label>Excel файл:</label>
                                <span class="btn btn-default btn-primary btn-file">
                                    Выберите файл <input type="file" id="studentsUpload" name="students">
                                </span>
                            </div>
                            <div class="col-md-3">
                                <div id="uploadedFile" class="margin-t30"></div>
                            </div>
                            <div class="col-md-1 text-right">
                                <button type="button" class="btn btn-primary margin-t25" id="submit">Отправить</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script type="text/javascript">

        $(document).ready(function() {
            $('#studentsUpload').on('change', function() { 
                let fileName = $(this).val().split('\\').pop(); 
                $('#uploadedFile').html(fileName); 
            });

            $('#submit').click(function(){
                $('#submit').html('<div class="loader">Loading...</div>');
                let formData = new FormData(document.forms.studentsUploadForm);

                $('#studentsUploadForm input').removeClass('is-invalid');
                $('#studentsUploadForm span').removeClass('is-invalid');
                $('#studentsUploadForm .invalid-feedback').remove();

                $.ajax({
                    url: '/nobd/upload/students/list',
                    data: formData,
                    processData: false,
                    contentType: false,
                    type: 'POST',
                    success: function(data){
                        if(data){
                            Swal.fire({
                              title: 'Done!',
                              text: 'Данные импортированы успешно',
                              icon: 'success',
                              confirmButtonText: 'Закрыть'
                            }).then(confirmButtonText => {location.reload()});
                        }else{
                            Swal.fire({
                              title: 'Error!',
                              text: 'Ошибка отправления данных',
                              icon: 'error',
                              confirmButtonText: 'Закрыть'
                            }).then(confirmButtonText => {location.reload()});
                        }
                    },
                    error: function(data){
                        console.log(data)
                        if(data.status == 422){
                            $.each(data.responseJSON.errors, function(field, errors) {
                                $.each(errors, function(index, error){
                                    if(field == 'students'){
                                        $('[name="' + field + '"]').parent().after('<div class="invalid-feedback">' + error + '</div>');
                                    }else{
                                        $('[name="' + field + '"]').after('<div class="invalid-feedback">' + error + '</div>');
                                    }
                                });
                                if(field == 'students'){
                                    $('[name="' + field + '"]').parent().addClass('is-invalid');
                                }else{
                                    $('[name="' + field + '"]').addClass('is-invalid');
                                }
                            });
                        }else{
                            Swal.fire({
                              title: 'Error!',
                              text: 'Ошибка отправления данных',
                              icon: 'error',
                              confirmButtonText: 'Закрыть'
                            }).then(confirmButtonText => {location.reload()});
                        }

                        $('#submit').html('Отправить');
                    },
                    complete: function(){
                        $('#submit').html('Отправить');
                    }
                });
            });
        } );

    </script>
@endsection