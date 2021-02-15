@extends("admin.admin_app")

@section("content")

    <div id="main">
        <div class="page-header">
            <h2> </h2>

            <a href="{{ route('adminRoleList') }}" class="btn btn-default-light btn-xs"><i class="md md-backspace"></i> Назад</a>

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

        <div class="panel panel-default">
            <div class="panel-body">
                {!! Form::open(array('url' => array( route('adminRoleEdit', ['id' => $role->id == 0 ? 'add' : $role->id]) ),'class'=>'form-horizontal padding-15','name'=>'role_form','id'=>'role_form','role'=>'form','enctype' => 'multipart/form-data')) !!}

                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Наименование (ru)</label>
                    <div class="col-sm-3">
                        <input type="text" required name="title_ru" value="{{ $role->title_ru }}" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Системное имя (только латинские буквы, цифры и знак подчеркивания)</label>
                    <div class="col-sm-3">
                        <input type="text" required name="name" value="{{ $role->name }}" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Описание (необязательно)</label>
                    <div class="col-sm-9">
                        <textarea name="description" class="form-control">{{ $role->description }}</textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label">Права (админка)</label>
                    <div class="col-sm-9">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Раздел</th>
                                    <th class="text-center" style="width: 150px;">Просмотр</th>
                                    <th class="text-center" style="width: 150px;">Создание</th>
                                    <th class="text-center" style="width: 150px;">Редактирование</th>
                                    <th class="text-center" style="width: 150px;">Удаление</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sectionAdminList as $item)
                                <tr >
                                    <td>{{$item->name_ru}}</td>
                                    <td class="text-center">
                                        <input type="checkbox"
                                               name="sectionRights[{{ $item->id }}][read]"
                                               value="1"
                                               @if($role->hasRight($item->id, 'read')) checked @endif>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox"
                                               name="sectionRights[{{ $item->id }}][create]"
                                               value="1"
                                               @if($role->hasRight($item->id, 'create')) checked @endif>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox"
                                               name="sectionRights[{{ $item->id }}][edit]"
                                               value="1"
                                               @if($role->hasRight($item->id, 'edit')) checked @endif>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox"
                                               name="sectionRights[{{ $item->id }}][delete]"
                                               value="1"
                                               @if($role->hasRight($item->id, 'delete')) checked @endif>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Особые права</label>
                    <div class="col-sm-9">
                        <div class="col-md-12">
                            <input type="checkbox"
                                   name="can_set_pay_in_orcabinet"
                                   value="1"
                                   @if($role->can_set_pay_in_orcabinet == true) checked @endif>
                            <span>Разрешить включать/отключать покупки в кабинете ОР</span>
                        </div>
                        <div class="col-md-12">
                            <input type="checkbox"
                                   name="can_upload_student_docs"
                                   value="1"
                                   @if($role->can_upload_student_docs == true) checked @endif>
                            <span>Разрешить загружать документы для студентов</span>
                        </div>
                        <div class="col-md-12">
                            <input type="checkbox"
                                   name="can_create_student_comment"
                                   value="1"
                                   @if($role->can_create_student_comment == true) checked @endif>
                            <span>Разрешить добавлять комментариии для студентов</span>
                        </div>
                        <div class="col-md-12">
                            <input type="checkbox"
                                   name="can_add_aditional_service_to_user"
                                   value="1"
                                   @if($role->can_add_aditional_service_to_user == true) checked @endif>
                            <span>Разрешить формирование доп. услуг для студентов в ОР</span>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="form-group">
                    <div class="col-md-offset-3 col-sm-9 ">
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                    </div>
                </div>

                {!! Form::close() !!}
            </div>


        </div>
    </div>

@endsection