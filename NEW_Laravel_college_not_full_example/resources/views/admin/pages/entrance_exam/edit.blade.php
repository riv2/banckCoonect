@extends("admin.admin_app")

@section("content")
    <div id="entrance_exam_edit">
        <div class="page-header">
            <h2> {{  !empty($isEdit) ? 'Редактировать ВИ' : 'Создать' }} </h2>
            <a href="{{ route('adminEntranceExamList') }}" class="btn btn-default-light btn-xs"><i class="md md-backspace"></i> Назад</a>
        </div>

        @if(Session::has('flash_message'))
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                {{ Session::get('flash_message') }}
            </div>
        @endif

        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row padding-20">


                    <form action="{{ route('adminEntranceExamEditPost') }}" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                        <input type="hidden" name="isEdit" value="{{ $isEdit }}" />


                        <div class="form-group margin-15">
                            <label class="col-md-3 control-label"> Год ВИ </label>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <select class="form-control" name="year">
                                        @if( !empty($yearsList) )
                                            @foreach($yearsList as $year)
                                                <option value="{{ $year }}" @if( !empty($model->year) && ($model->year == $year) ) selected @elseif( empty($model->year) && (date('Y') == $year) ) selected @endif> {{ $year }} </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>


                        <div class="form-group margin-15">
                            <label class="col-md-3 control-label"> Название ВИ </label>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input class="form-control" type="text" name="name" value="{{ $model->name ?? '' }}" />
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>


                        <div class="form-group margin-15">
                            <label class="col-md-3 control-label"> Срок записи </label>
                            <div class="col-md-5">
                                <entrance-exam-option name="date_active" :active="{{ $model->date_active ?? 'false' }}">
                                    <div class="col-md-12 subform">
                                        <div class="form-group col-md-7">
                                            <label class="control-label"> Дата начала </label>
                                            <input class="form-control" type="date" name="date_start" value="{{ $model->date_start ?? '' }}" />
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="form-group col-md-7 margin-b15">
                                            <label class="control-label"> Дата окончания </label>
                                            <input class="form-control" type="date" name="date_end" value="{{ $model->date_end ?? '' }}" />
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="form-check">
                                            <input onchange="changeChecked('date_user_show')" class="form-check-input" type="checkbox" id="date_user_show" name="date_user_show" value="{{ $model->date_user_show ?? 1 }}" @if( (int)$model->date_user_show != 0 ) checked="checked" @endif />
                                            <label class="form-check-label" for="date_user_show">
                                                Видно студенту
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input onchange="changeChecked('date_employee_show')" class="form-check-input" type="checkbox" id="date_employee_show" name="date_employee_show" value="{{ $model->date_employee_show ?? 1 }}" @if( $model->date_employee_show != 0 ) checked="checked" @endif />
                                            <label class="form-check-label" for="date_employee_show">
                                                Видно сотруднику
                                            </label>
                                        </div>
                                    </div>
                                </entrance-exam-option>
                            </div>
                        </div>
                        <div class="clearfix"></div>


                        <div class="form-group margin-15">
                            <label class="col-md-3 control-label"> Методичка </label>
                            <div class="col-md-5">
                                <entrance-exam-option id="manual-files-block" name="manual_active" :active="{{ $model->manual_active ?? 'false' }}">
                                    <div class="col-md-12 subform">
                                        <div class="form-check">
                                            <input onchange="changeChecked('manual_user_show')" class="form-check-input" type="checkbox" id="manual_user_show" name="manual_user_show" value="{{ $model->manual_user_show ?? 1 }}" @if( $model->manual_user_show != 0 ) checked="checked" @endif />
                                            <label class="form-check-label" for="manual_user_show">
                                                Видно студенту
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input onchange="changeChecked('manual_employee_show')" class="form-check-input" type="checkbox" id="manual_employee_show" name="manual_employee_show" value="{{ $model->manual_employee_show ?? 1 }}" @if( $model->manual_employee_show != 0 ) checked="checked" @endif />
                                            <label class="form-check-label" for="manual_employee_show">
                                                Видно сотруднику
                                            </label>
                                        </div>
                                        @if( empty($model->getFiles( \App\EntranceExamFiles::TYPE_FILE_MANUAL )) || count($model->getFiles( \App\EntranceExamFiles::TYPE_FILE_MANUAL )) == 0 )
                                            <div class="col-md-8 form-group">
                                                <input type="file" name="manualFiles[file][]" value="" />
                                            </div>
                                        @else
                                            @foreach($model->getFiles( \App\EntranceExamFiles::TYPE_FILE_MANUAL ) as $file)
                                                <div class="col-md-8 form-group">
                                                    <a href="{{ $file->getPublicUrl() }}" target="_blank">{{ $file->name ?? '<без названия>' }}</a>
                                                </div>
                                                <div class="col-md-1" onclick="deleteFile(this,'{{$file->id}}')"><a class="btn btn-default"><i class="fa fa-remove"></i></a></div>
                                            @endforeach
                                        @endif
                                        <div class="col-md-12 margin-b10">
                                            <a class="btn btn-default" onclick="addManualFile()"> Добавить файл <i class="fa fa-plus"></i></a>
                                        </div>
                                    </div>
                                </entrance-exam-option>
                            </div>
                        </div>
                        <div class="clearfix"></div>


                        <div class="form-group margin-15">
                            <label class="col-md-3 control-label"> Регистрация в базе НЦТ </label>
                            <div class="col-md-5">
                                <entrance-exam-option name="nct_number_active" :active="{{ $model->nct_number_active ?? 'false' }}">
                                    <div class="col-md-12 subform">
                                        <div class="form-check">
                                            <input onchange="changeChecked('nct_number_user_show')" class="form-check-input" type="checkbox" id="nct_number_user_show" name="nct_number_user_show" value="{{ $model->nct_number_user_show ?? 1 }}" @if(!empty($model->nct_number_user_show) && ($model->nct_number_user_show != 0)) checked="checked" @endif />
                                            <label class="form-check-label" for="nct_number_user_show">
                                                Видно студенту
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input onchange="changeChecked('nct_number_employee_show')" class="form-check-input" type="checkbox" id="nct_number_employee_show" name="nct_number_employee_show" value="{{ $model->nct_number_employee_show ?? 1 }}" @if(!empty($model->nct_number_employee_show) && ($model->nct_number_employee_show != 0)) checked="checked" @endif />
                                            <label class="form-check-label" for="nct_number_employee_show">
                                                Видно сотруднику
                                            </label>
                                        </div>
                                    </div>
                                </entrance-exam-option>
                            </div>
                        </div>
                        <div class="clearfix"></div>


                        <div class="form-group margin-15">
                            <label class="col-md-3 control-label"> Проходной балл </label>
                            <div class="col-md-5">
                                <entrance-exam-option name="passing_point_active" :active="{{ $model->passing_point_active ?? 'false' }}">
                                    <div class="col-md-12 subform">

                                        <div class="form-group col-md-7">
                                            <label class="control-label"> Балл </label>
                                            <input class="form-control" type="text" name="passing_point" value="{{ $model->passing_point ?? '' }}" />
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="form-check">
                                            <input onchange="changeChecked('passing_point_user_show')" class="form-check-input" type="checkbox" id="passing_point_user_show" name="passing_point_user_show" value="{{ $model->passing_point_user_show ?? 1 }}" @if(!empty($model->passing_point_user_show) && ($model->passing_point_user_show != 0)) checked="checked" @endif />
                                            <label class="form-check-label" for="passing_point_user_show">
                                                Видно студенту
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input onchange="changeChecked('passing_point_employee_show')" class="form-check-input" type="checkbox" id="passing_point_employee_show" name="passing_point_employee_show" value="{{ $model->passing_point_employee_show ?? 1 }}" @if(!empty($model->passing_point_employee_show) && ($model->passing_point_employee_show != 0)) checked="checked" @endif />
                                            <label class="form-check-label" for="passing_point_employee_show">
                                                Видно сотруднику
                                            </label>
                                        </div>
                                    </div>
                                </entrance-exam-option>
                            </div>
                        </div>
                        <div class="clearfix"></div>


                        <div class="form-group margin-15">
                            <label class="col-md-3 control-label"> Ведомость </label>
                            <div class="col-md-5">
                                <entrance-exam-option id="statement-files-block" name="statement_active" :active="{{ $model->statement_active ?? 'false' }}">
                                    <div class="col-md-12 subform">
                                        <div class="form-check">
                                            <input onchange="changeChecked('statement_user_show')" class="form-check-input" type="checkbox" id="statement_user_show" name="statement_user_show" value="{{ $model->statement_user_show ?? 1 }}" @if(!empty($model->statement_user_show)) checked="checked" @endif />
                                            <label class="form-check-label" for="statement_user_show">
                                                Видно студенту
                                            </label>
                                        </div>
                                        <div class="form-check margin-b10">
                                            <input onchange="changeChecked('statement_employee_show')" class="form-check-input" type="checkbox" id="statement_employee_show" name="statement_employee_show" value="{{ $model->statement_employee_show ?? 1 }}" @if(!empty($model->statement_employee_show)) checked="checked" @endif />
                                            <label class="form-check-label" for="statement_employee_show">
                                                Видно сотруднику
                                            </label>
                                        </div>
                                        @if( empty($model->getFiles( \App\EntranceExamFiles::TYPE_FILE_STATEMENT )) || count($model->getFiles( \App\EntranceExamFiles::TYPE_FILE_STATEMENT )) == 0 )
                                            <div class="col-md-8 form-group">
                                                <input type="file" name="statementFiles[file][]" value="" />
                                            </div>
                                        @else
                                            @foreach($model->getFiles( \App\EntranceExamFiles::TYPE_FILE_STATEMENT ) as $file)
                                                <div class="col-md-8 form-group">
                                                    <a href="{{ $file->getPublicUrl() }}" target="_blank">{{ $file->name ?? '<без названия>' }}</a>
                                                </div>
                                                <div class="col-md-1" onclick="deleteFile(this,'{{$file->id}}')"><a class="btn btn-default"><i class="fa fa-remove"></i></a></div>
                                            @endforeach
                                        @endif
                                        <div class="col-md-12 margin-b10">
                                            <a class="btn btn-default" onclick="addStatementFile()"> Добавить файл <i class="fa fa-plus"></i></a>
                                        </div>
                                    </div>
                                </entrance-exam-option>
                            </div>
                        </div>
                        <div class="clearfix"></div>


                        <div class="form-group margin-15">
                            <label class="col-md-3 control-label"> Состав комиссии </label>
                            <div class="col-md-5">
                                <entrance-exam-option id="commission-structure-files-block" name="commission_structure_active" :active="{{ $model->commission_structure_active ?? 'false' }}">
                                    <div class="col-md-12 subform">
                                        <div class="form-check">
                                            <input onchange="changeChecked('commission_structure_user_show')" class="form-check-input" type="checkbox" id="commission_structure_user_show" name="commission_structure_user_show" value="{{ $model->commission_structure_user_show ?? 1 }}" @if(!empty($model->commission_structure_user_show)) checked="checked" @endif />
                                            <label class="form-check-label" for="commission_structure_user_show">
                                                Видно студенту
                                            </label>
                                        </div>
                                        <div class="form-check margin-b10">
                                            <input onchange="changeChecked('commission_structure_employee_show')" class="form-check-input" type="checkbox" id="commission_structure_employee_show" name="commission_structure_employee_show" value="{{ $model->commission_structure_employee_show ?? 1 }}" @if(!empty($model->commission_structure_employee_show)) checked="checked" @endif />
                                            <label class="form-check-label" for="commission_structure_employee_show">
                                                Видно сотруднику
                                            </label>
                                        </div>
                                        @if( empty($model->getFiles( \App\EntranceExamFiles::TYPE_FILE_COMMISSION_STRUCTURE )) || count($model->getFiles( \App\EntranceExamFiles::TYPE_FILE_COMMISSION_STRUCTURE )) == 0 )
                                            <div class="col-md-8 form-group">
                                                <input type="file" name="commissionStructureFiles[file][]" value="" />
                                            </div>
                                        @else
                                            @foreach($model->getFiles( \App\EntranceExamFiles::TYPE_FILE_COMMISSION_STRUCTURE ) as $file)
                                                <div class="col-md-8 form-group">
                                                    <a href="{{ $file->getPublicUrl() }}" target="_blank">{{ $file->name ?? '<без названия>' }}</a>
                                                </div>
                                                <div class="col-md-1" onclick="deleteFile(this,'{{$file->id}}')"><a class="btn btn-default"><i class="fa fa-remove"></i></a></div>
                                            @endforeach
                                        @endif
                                        <div class="col-md-12 margin-b10">
                                            <a class="btn btn-default" onclick="addCommissionStructureFile()"> Добавить файл <i class="fa fa-plus"></i></a>
                                        </div>
                                    </div>
                                </entrance-exam-option>
                            </div>
                        </div>
                        <div class="clearfix"></div>


                        <div class="form-group margin-15">
                            <label class="col-md-3 control-label"> Состав аппеляционной комиссии </label>
                            <div class="col-md-5">
                                <entrance-exam-option id="composition-appeal-commission-files-block" name="composition_appeal_commission_active" :active="{{ $model->composition_appeal_commission_active ?? 'false' }}">
                                    <div class="col-md-12 subform">
                                        <div class="form-check">
                                            <input onchange="changeChecked('composition_appeal_commission_user_show')" class="form-check-input" type="checkbox" id="composition_appeal_commission_user_show" name="composition_appeal_commission_user_show" value="{{ $model->composition_appeal_commission_user_show ?? 1 }}" @if(!empty($model->composition_appeal_commission_user_show)) checked="checked" @endif />
                                            <label class="form-check-label" for="composition_appeal_commission_user_show">
                                                Видно студенту
                                            </label>
                                        </div>
                                        <div class="form-check margin-b10">
                                            <input onchange="changeChecked('composition_appeal_commission_employee_show')" class="form-check-input" type="checkbox" id="composition_appeal_commission_employee_show" name="composition_appeal_commission_employee_show" value="{{ $model->composition_appeal_commission_employee_show ?? 1 }}" @if(!empty($model->composition_appeal_commission_employee_show)) checked="checked" @endif />
                                            <label class="form-check-label" for="composition_appeal_commission_employee_show">
                                                Видно сотруднику
                                            </label>
                                        </div>
                                        @if( empty($model->getFiles( \App\EntranceExamFiles::TYPE_FILE_COMMISSION_APPEAL_STRUCTURE )) || count($model->getFiles( \App\EntranceExamFiles::TYPE_FILE_COMMISSION_APPEAL_STRUCTURE )) == 0 )
                                            <div class="col-md-8 form-group">
                                                <input type="file" name="compositionAppealCommissionFiles[file][]" value="" />
                                            </div>
                                        @else
                                            @foreach($model->getFiles( \App\EntranceExamFiles::TYPE_FILE_COMMISSION_APPEAL_STRUCTURE ) as $file)
                                                <div class="col-md-8 form-group">
                                                    <a href="{{ $file->getPublicUrl() }}" target="_blank">{{ $file->name ?? '<без названия>' }}</a>
                                                </div>
                                                <div class="col-md-1" onclick="deleteFile(this,'{{$file->id}}')"><a class="btn btn-default"><i class="fa fa-remove"></i></a></div>
                                            @endforeach
                                        @endif
                                        <div class="col-md-12 margin-b10">
                                            <a class="btn btn-default" onclick="addCompositionAppealCommissionFile()"> Добавить файл <i class="fa fa-plus"></i></a>
                                        </div>
                                    </div>
                                </entrance-exam-option>
                            </div>
                        </div>
                        <div class="clearfix"></div>


                        <div class="form-group margin-15">
                            <label class="col-md-3 control-label"> Расписание </label>
                            <div class="col-md-5">
                                <entrance-exam-option id="schedule-files-block" name="schedule_active" :active="{{ $model->schedule_active ?? 'false' }}">
                                    <div class="col-md-12 subform">
                                        <div class="form-check">
                                            <input onchange="changeChecked('schedule_user_show')" class="form-check-input" type="checkbox" id="schedule_user_show" name="schedule_user_show" value="{{ $model->schedule_user_show ?? 1 }}" @if(!empty($model->schedule_user_show)) checked="checked" @endif />
                                            <label class="form-check-label" for="schedule_user_show">
                                                Видно студенту
                                            </label>
                                        </div>
                                        <div class="form-check margin-b10">
                                            <input onchange="changeChecked('schedule_employee_show')" class="form-check-input" type="checkbox" id="schedule_employee_show" name="schedule_employee_show" value="{{ $model->schedule_employee_show ?? 1 }}" @if(!empty($model->schedule_employee_show)) checked="checked" @endif />
                                            <label class="form-check-label" for="schedule_employee_show">
                                                Видно сотруднику
                                            </label>
                                        </div>
                                        @if( empty($model->getFiles( \App\EntranceExamFiles::TYPE_FILE_SCHEDULE )) || count($model->getFiles( \App\EntranceExamFiles::TYPE_FILE_SCHEDULE )) == 0 )
                                            <div class="col-md-8 form-group">
                                                <input type="file" name="scheduleFiles[file][]" value="" />
                                            </div>
                                        @else
                                            @foreach($model->getFiles( \App\EntranceExamFiles::TYPE_FILE_SCHEDULE ) as $file)
                                                <div class="col-md-8 form-group">
                                                    <a href="{{ $file->getPublicUrl() }}" target="_blank">{{ $file->name ?? '<без названия>' }}</a>
                                                </div>
                                                <div class="col-md-1" onclick="deleteFile(this,'{{$file->id}}')"><a class="btn btn-default"><i class="fa fa-remove"></i></a></div>
                                            @endforeach
                                        @endif
                                        <div class="col-md-12 margin-b10">
                                            <a class="btn btn-default" onclick="addScheduleFile()"> Добавить файл <i class="fa fa-plus"></i></a>
                                        </div>
                                    </div>
                                </entrance-exam-option>
                            </div>
                        </div>
                        <div class="clearfix"></div>


                        <div class="form-group margin-15">
                            <label class="col-md-3 control-label"> Общие протоколы по результатам творческих экзаменов </label>
                            <div class="col-md-5">
                                <entrance-exam-option id="protocols-creative-exams-files-block" name="protocols_creative_exams_active" :active="{{ $model->protocols_creative_exams_active ?? 'false' }}">
                                    <div class="col-md-12 subform">
                                        <div class="form-check">
                                            <input onchange="changeChecked('protocols_creative_exams_user_show')" class="form-check-input" type="checkbox" id="protocols_creative_exams_user_show" name="protocols_creative_exams_user_show" value="{{ $model->protocols_creative_exams_user_show ?? 1 }}" @if(!empty($model->protocols_creative_exams_user_show)) checked="checked" @endif />
                                            <label class="form-check-label" for="protocols_creative_exams_user_show">
                                                Видно студенту
                                            </label>
                                        </div>
                                        <div class="form-check margin-b10">
                                            <input onchange="changeChecked('protocols_creative_exams_employee_show')" class="form-check-input" type="checkbox" id="protocols_creative_exams_employee_show" name="protocols_creative_exams_employee_show" value="{{ $model->protocols_creative_exams_employee_show ?? 1 }}" @if(!empty($model->protocols_creative_exams_employee_show)) checked="checked" @endif />
                                            <label class="form-check-label" for="protocols_creative_exams_employee_show">
                                                Видно сотруднику
                                            </label>
                                        </div>
                                        @if( empty($model->getFiles( \App\EntranceExamFiles::TYPE_FILE_PROTOCOLS_CREATIVE_EXAMS )) || count($model->getFiles( \App\EntranceExamFiles::TYPE_FILE_PROTOCOLS_CREATIVE_EXAMS )) == 0 )
                                            <div class="col-md-8 form-group">
                                                <input type="file" name="protocolsCreativeExamsFiles[file][]" value="" />
                                            </div>
                                        @else
                                            @foreach($model->getFiles( \App\EntranceExamFiles::TYPE_FILE_PROTOCOLS_CREATIVE_EXAMS ) as $file)
                                                <div class="col-md-8 form-group">
                                                    <a href="{{ $file->getPublicUrl() }}" target="_blank">{{ $file->name ?? '<без названия>' }}</a>
                                                </div>
                                                <div class="col-md-1" onclick="deleteFile(this,'{{$file->id}}')"><a class="btn btn-default"><i class="fa fa-remove"></i></a></div>
                                            @endforeach
                                        @endif
                                        <div class="col-md-12 margin-b10">
                                            <a class="btn btn-default" onclick="addProtocolsCreativeExamsFile()"> Добавить файл <i class="fa fa-plus"></i></a>
                                        </div>
                                    </div>
                                </entrance-exam-option>
                            </div>
                        </div>
                        <div class="clearfix"></div>


                        <div class="form-group margin-15">
                            <label class="col-md-3 control-label"> Общие протоколы по апелляционной комиссии </label>
                            <div class="col-md-5">
                                <entrance-exam-option id="protocols-appeal-commission-files-block" name="protocols_appeal_commission_active" :active="{{ $model->protocols_appeal_commission_active ?? 'false' }}">
                                    <div class="col-md-12 subform">
                                        <div class="form-check">
                                            <input onchange="changeChecked('protocols_appeal_commission_user_show')" class="form-check-input" type="checkbox" id="protocols_appeal_commission_user_show" name="protocols_appeal_commission_user_show" value="{{ $model->protocols_appeal_commission_user_show ?? 1 }}" @if(!empty($model->protocols_appeal_commission_user_show)) checked="checked" @endif />
                                            <label class="form-check-label" for="protocols_appeal_commission_user_show">
                                                Видно студенту
                                            </label>
                                        </div>
                                        <div class="form-check margin-b10">
                                            <input onchange="changeChecked('protocols_appeal_commission_employee_show')" class="form-check-input" type="checkbox" id="protocols_appeal_commission_employee_show" name="protocols_appeal_commission_employee_show" value="{{ $model->protocols_appeal_commission_employee_show ?? 1 }}" @if(!empty($model->protocols_appeal_commission_employee_show)) checked="checked" @endif />
                                            <label class="form-check-label" for="protocols_appeal_commission_employee_show">
                                                Видно сотруднику
                                            </label>
                                        </div>
                                        @if( empty($model->getFiles( \App\EntranceExamFiles::TYPE_FILE_PROTOCOLS_APPEAL_COMMISSION )) || count($model->getFiles( \App\EntranceExamFiles::TYPE_FILE_PROTOCOLS_APPEAL_COMMISSION )) == 0 )
                                            <div class="col-md-8 form-group">
                                                <input type="file" name="protocolsAppealCommissionFiles[file][]" value="" />
                                            </div>
                                        @else
                                            @foreach($model->getFiles( \App\EntranceExamFiles::TYPE_FILE_PROTOCOLS_APPEAL_COMMISSION ) as $file)
                                                <div class="col-md-8 form-group">
                                                    <a href="{{ $file->getPublicUrl() }}" target="_blank">{{ $file->name ?? '<без названия>' }}</a>
                                                </div>
                                                <div class="col-md-1" onclick="deleteFile(this,'{{$file->id}}')"><a class="btn btn-default"><i class="fa fa-remove"></i></a></div>
                                            @endforeach
                                        @endif
                                        <div class="col-md-12 margin-b10">
                                            <a class="btn btn-default" onclick="addProtocolsAppealCommissionFile()"> Добавить файл <i class="fa fa-plus"></i></a>
                                        </div>
                                    </div>
                                </entrance-exam-option>
                            </div>
                        </div>
                        <div class="clearfix"></div>


                        <div class="form-group margin-15">
                            <label class="col-md-3 control-label"> Отчеты по творческим и специальным экзаменам направленных в МОН РК </label>
                            <div class="col-md-5">
                                <entrance-exam-option id="report-exams-files-block" name="report_exams_active" :active="{{ $model->report_exams_active ?? 'false' }}">
                                    <div class="col-md-12 subform">
                                        <div class="form-check">
                                            <input onchange="changeChecked('report_exams_user_show')" class="form-check-input" type="checkbox" id="report_exams_user_show" name="report_exams_user_show" value="{{ $model->report_exams_user_show ?? 1 }}" @if(!empty($model->report_exams_user_show)) checked="checked" @endif />
                                            <label class="form-check-label" for="report_exams_user_show">
                                                Видно студенту
                                            </label>
                                        </div>
                                        <div class="form-check margin-b10">
                                            <input onchange="changeChecked('report_exams_employee_show')" class="form-check-input" type="checkbox" id="report_exams_employee_show" name="report_exams_employee_show" value="{{ $model->report_exams_employee_show ?? 1 }}" @if(!empty($model->report_exams_employee_show)) checked="checked" @endif />
                                            <label class="form-check-label" for="report_exams_employee_show">
                                                Видно сотруднику
                                            </label>
                                        </div>
                                        @if( empty($model->getFiles( \App\EntranceExamFiles::TYPE_FILE_REPORT_EXAMS )) || count($model->getFiles( \App\EntranceExamFiles::TYPE_FILE_REPORT_EXAMS )) == 0 )
                                            <div class="col-md-8 form-group">
                                                <input type="file" name="reportExamsFiles[file][]" value="" />
                                            </div>
                                        @else
                                            @foreach($model->getFiles( \App\EntranceExamFiles::TYPE_FILE_REPORT_EXAMS ) as $file)
                                                <div class="col-md-8 form-group">
                                                    <a href="{{ $file->getPublicUrl() }}" target="_blank">{{ $file->name ?? '<без названия>' }}</a>
                                                </div>
                                                <div class="col-md-1" onclick="deleteFile(this,'{{$file->id}}')"><a class="btn btn-default"><i class="fa fa-remove"></i></a></div>
                                            @endforeach
                                        @endif
                                        <div class="col-md-12 margin-b10">
                                            <a class="btn btn-default" onclick="addReportExamsFile()"> Добавить файл <i class="fa fa-plus"></i></a>
                                        </div>
                                    </div>
                                </entrance-exam-option>
                            </div>
                        </div>
                        <div class="clearfix"></div>


                        <div class="form-group margin-15">
                            <label class="col-md-3 control-label"> Ручная проверка </label>
                            <div class="col-md-5">
                                <entrance-exam-option id="custom-checked-files-block" name="custom_checked_active" :active="{{ $model->custom_checked_active ?? 'false' }}">
                                    <div class="col-md-12 subform">
                                        <div class="form-check">
                                            <input onchange="changeChecked('custom_checked_user_show')" class="form-check-input" type="checkbox" id="custom_checked_user_show" name="custom_checked_user_show" value="{{ $model->custom_checked_user_show ?? 1 }}" @if(!empty($model->custom_checked_user_show)) checked="checked" @endif />
                                            <label class="form-check-label" for="custom_checked_user_show">
                                                Видно студенту
                                            </label>
                                        </div>
                                        <div class="form-check margin-b10">
                                            <input onchange="changeChecked('custom_checked_employee_show')" class="form-check-input" type="checkbox" id="custom_checked_employee_show" name="custom_checked_employee_show" value="{{ $model->custom_checked_employee_show ?? 1 }}" @if(!empty($model->custom_checked_employee_show)) checked="checked" @endif />
                                            <label class="form-check-label" for="custom_checked_employee_show">
                                                Видно сотруднику
                                            </label>
                                        </div>
                                    </div>
                                </entrance-exam-option>
                            </div>
                        </div>
                        <div class="clearfix"></div>


                        <div class="form-group margin-tb15">
                            <div class="col-md-4">
                                @if(\App\Services\Auth::user()->hasRight('test_pc_vi','edit'))
                                <button class="btn btn-info" type="submit"> Сохранить </button>
                                @endif
                            </div>
                        </div>

                    </form>


                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script type="text/javascript">

        Vue.component('entrance-exam-option', {
            props: {
                name: '',
                slotClass: '',
                active: false,
                image: false
            },
            data: function () {
                return { show: this.active || false };
            },
            methods: {},
            template: `
                <div class="col-md-12 no-padding">
                    <div class="col-md-5 no-padding">
                        <div class="switch-btn" :class="{'switch-on-color': show}" @click="show = !show"></div>
                    </div>
                    <slot v-if="show"></slot>
                    <input type="hidden" :value="show" :name="name" />
                </div>
            `
        });

        var app = new Vue({
            el: '#entrance_exam_edit',
            data: {},
            methods: {}
        });

        function changeChecked(id)
        {
            var val = $('#'+id).val();
            if( val == 1 ){
                $('#'+id).prop('checked',false);
                $('#'+id).prop('value',0);
            } else {
                $('#'+id).prop('checked',true);
                $('#'+id).prop('value',1);
            }
        }

        function addManualFile()
        {
            $('#manual-files-block').append('<div class="col-md-8 form-group padding-5">\n' +
                '<input type="file" name="manualFiles[file][]" value="" />' +
                '</div><div class="col-md-1" onclick="deleteFile(this,null)"><a class="btn btn-default"><i class="fa fa-remove"></i></a></div>');
        }

        function addStatementFile()
        {
            $('#statement-files-block').append('<div class="col-md-8 form-group padding-5">\n' +
                '<input type="file" name="statementFiles[file][]" value="" />' +
                '</div><div class="col-md-1" onclick="deleteFile(this,null)"><a class="btn btn-default"><i class="fa fa-remove"></i></a></div>');
        }

        function addCommissionStructureFile()
        {
            $('#commission-structure-files-block').append('<div class="col-md-8 form-group padding-5">\n' +
                '<input type="file" name="commissionStructureFiles[file][]" value="" />' +
                '</div><div class="col-md-1" onclick="deleteFile(this,null)"><a class="btn btn-default"><i class="fa fa-remove"></i></a></div>');
        }

        function addCompositionAppealCommissionFile()
        {
            $('#composition-appeal-commission-files-block').append('<div class="col-md-8 form-group padding-5">\n' +
                '<input type="file" name="compositionAppealCommissionFiles[file][]" value="" />' +
                '</div><div class="col-md-1" onclick="deleteFile(this,null)"><a class="btn btn-default"><i class="fa fa-remove"></i></a></div>');
        }

        function addScheduleFile()
        {
            $('#schedule-files-block').append('<div class="col-md-8 form-group padding-5">\n' +
                '<input type="file" name="scheduleFiles[file][]" value="" />' +
                '</div><div class="col-md-1" onclick="deleteFile(this,null)"><a class="btn btn-default"><i class="fa fa-remove"></i></a></div>');
        }

        function addProtocolsCreativeExamsFile()
        {
            $('#protocols-creative-exams-files-block').append('<div class="col-md-8 form-group padding-5">\n' +
                '<input type="file" name="scheduleFiles[file][]" value="" />' +
                '</div><div class="col-md-1" onclick="deleteFile(this,null)"><a class="btn btn-default"><i class="fa fa-remove"></i></a></div>');
        }

        function addProtocolsAppealCommissionFile()
        {
            $('#protocols-appeal-commission-files-block').append('<div class="col-md-8 form-group padding-5">\n' +
                '<input type="file" name="protocolsAppealCommissionFiles[file][]" value="" />' +
                '</div><div class="col-md-1" onclick="deleteFile(this,null)"><a class="btn btn-default"><i class="fa fa-remove"></i></a></div>');
        }

        function addReportExamsFile()
        {
            $('#report-exams-files-block').append('<div class="col-md-8 form-group padding-5">\n' +
                '<input type="file" name="reportExamsFiles[file][]" value="" />' +
                '</div><div class="col-md-1" onclick="deleteFile(this,null)"><a class="btn btn-default"><i class="fa fa-remove"></i></a></div>');
        }

        function deleteFile(elem,id)
        {
            $(elem).prev().remove();
            $(elem).after('<input type="hidden" value="'+id+'" name="removeFiles[]">');
            $(elem).remove();
        }

    </script>
@endsection
