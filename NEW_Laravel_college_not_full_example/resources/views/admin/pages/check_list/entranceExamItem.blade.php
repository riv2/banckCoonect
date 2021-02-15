<div class="panel panel-default margin-20">
    <div class="panel-body">

        <input type="hidden" name="entrance_exam[{{ $model->id }}][id]" value="{{ $model->id }}" />

        <div class="form-group margin-15">
            <label class="col-md-3 control-label"> Год ВИ </label>
            <div class="col-md-3">
                <div class="form-group">
                    <select class="form-control" name="entrance_exam[{{ $model->id }}][year]">
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
                    <input class="form-control" type="text" name="entrance_exam[{{ $model->id }}][name]" value="{{ $model->name ?? '' }}" />
                </div>
            </div>
        </div>
        <div class="clearfix"></div>


        <div class="form-group margin-15">

            <label class="col-md-3 control-label"> Срок записи </label>
            <div class="col-md-5">
                <div class="col-md-12 no-padding">
                    <div class="col-md-5 no-padding">
                        <div onClick="switchBtn(this,'{{ $model->id }}','date')" class="switch-btn {{ !empty($model->date_active) ? 'switch-on-color' : '' }}"></div>
                        <input id="date_input{{ $model->id }}" type="hidden" value="{{ $model->date_active }}" name="entrance_exam[{{ $model->id }}][date_active]" />
                    </div>
                    <div id="date_files{{ $model->id }}" class="{{ empty($model->date_active) ? 'hide' : '' }}">
                        <div class="col-md-12 subform">
                            <div class="form-group col-md-7">
                                <label class="control-label"> Дата начала </label>
                                <input class="form-control" type="date" name="entrance_exam[{{ $model->id }}][date_start]" value="{{ $model->date_start ?? '' }}" />
                            </div>
                            <div class="clearfix"></div>
                            <div class="form-group col-md-7 margin-b15">
                                <label class="control-label"> Дата окончания </label>
                                <input class="form-control" type="date" name="entrance_exam[{{ $model->id }}][date_end]" value="{{ $model->date_end ?? '' }}" />
                            </div>
                            <div class="clearfix"></div>
                            <div class="form-check">
                                <input onchange="changeChecked('date_user_show'+'{{ $model->id }}')" class="form-check-input" type="checkbox" id="date_user_show{{ $model->id }}" name="entrance_exam[{{ $model->id }}][date_user_show]" value="{{ $model->date_user_show ?? 1 }}" @if( (int)$model->date_user_show != 0 ) checked="checked" @endif />
                                <label class="form-check-label" for="date_user_show{{ $model->id }}">
                                    Видно студенту
                                </label>
                            </div>
                            <div class="form-check">
                                <input onchange="changeChecked('date_employee_show'+'{{ $model->id }}')" class="form-check-input" type="checkbox" id="date_employee_show{{ $model->id }}" name="entrance_exam[{{ $model->id }}][date_employee_show]" value="{{ $model->date_employee_show ?? 1 }}" @if( $model->date_employee_show != 0 ) checked="checked" @endif />
                                <label class="form-check-label" for="date_employee_show{{ $model->id }}">
                                    Видно сотруднику
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="clearfix"></div>


        <div class="form-group margin-15">

            <label class="col-md-3 control-label"> Методичка </label>
            <div class="col-md-5">
                <div class="col-md-12 no-padding">
                    <div class="col-md-5 no-padding">
                        <div onClick="switchBtn(this,'{{ $model->id }}','manual')" class="switch-btn {{ !empty($model->manual_active) ? 'switch-on-color' : '' }}"></div>
                        <input id="manual_input{{ $model->id }}" type="hidden" value="{{ $model->manual_active }}" name="entrance_exam[{{ $model->id }}][manual_active]" />
                    </div>
                    <div id="manual_files{{ $model->id }}" class="{{ empty($model->manual_active) ? 'hide' : '' }}">
                        <div class="col-md-12 subform">
                            <div class="form-check">
                                <input onchange="changeChecked('manual_user_show'+'{{ $model->id }}')" class="form-check-input" type="checkbox" id="manual_user_show{{ $model->id }}" name="entrance_exam[{{ $model->id }}][manual_user_show]" value="{{ $model->manual_user_show ?? 1 }}" @if( $model->manual_user_show != 0 ) checked="checked" @endif />
                                <label class="form-check-label" for="manual_user_show{{ $model->id }}">
                                    Видно студенту
                                </label>
                            </div>
                            <div class="form-check">
                                <input onchange="changeChecked('manual_employee_show'+'{{ $model->id }}')" class="form-check-input" type="checkbox" id="manual_employee_show{{ $model->id }}" name="entrance_exam[{{ $model->id }}][manual_employee_show]" value="{{ $model->manual_employee_show ?? 1 }}" @if( $model->manual_employee_show != 0 ) checked="checked" @endif />
                                <label class="form-check-label" for="manual_employee_show{{ $model->id }}">
                                    Видно сотруднику
                                </label>
                            </div>
                            @if( empty($model->getFiles( \App\EntranceExamFiles::TYPE_FILE_MANUAL )) || count($model->getFiles( \App\EntranceExamFiles::TYPE_FILE_MANUAL )) == 0 )
                                <div class="col-md-8 form-group">
                                    <input type="file" name="entrance_exam[{{ $model->id }}][manualFiles][file][]" value="" />
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
                                <a class="btn btn-default" onclick="addManualFile('manual_files{{ $model->id }}','entrance_exam[{{ $model->id }}][manualFiles][file][]')"> Добавить файл <i class="fa fa-plus"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="clearfix"></div>


        <div class="form-group margin-15">

            <label class="col-md-3 control-label"> Регистрация в базе НЦТ </label>
            <div class="col-md-5">
                <div class="col-md-12 no-padding">
                    <div class="col-md-5 no-padding">
                        <div onClick="switchBtn(this,'{{ $model->id }}','nct_number')" class="switch-btn {{ !empty($model->nct_number_active) ? 'switch-on-color' : '' }}"></div>
                        <input id="nct_number_input{{ $model->id }}" type="hidden" value="{{ $model->nct_number_active }}" name="entrance_exam[{{ $model->id }}][nct_number_active]" />
                    </div>
                    <div id="nct_number_files{{ $model->id }}" class="{{ empty($model->nct_number_active) ? 'hide' : '' }}">
                        <div class="col-md-12 subform">
                            <div class="form-check">
                                <input onchange="changeChecked('nct_number_user_show'+'{{ $model->id }}')" class="form-check-input" type="checkbox" id="nct_number_user_show{{ $model->id }}" name="entrance_exam[{{ $model->id }}][nct_number_user_show]" value="{{ $model->nct_number_user_show ?? 1 }}" @if(!empty($model->nct_number_user_show) && ($model->nct_number_user_show != 0)) checked="checked" @endif />
                                <label class="form-check-label" for="nct_number_user_show{{ $model->id }}">
                                    Видно студенту
                                </label>
                            </div>
                            <div class="form-check">
                                <input onchange="changeChecked('nct_number_employee_show'+'{{ $model->id }}')" class="form-check-input" type="checkbox" id="nct_number_employee_show{{ $model->id }}" name="entrance_exam[{{ $model->id }}][nct_number_employee_show]" value="{{ $model->nct_number_employee_show ?? 1 }}" @if(!empty($model->nct_number_employee_show) && ($model->nct_number_employee_show != 0)) checked="checked" @endif />
                                <label class="form-check-label" for="nct_number_employee_show{{ $model->id }}">
                                    Видно сотруднику
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="clearfix"></div>


        <div class="form-group margin-15">

            <label class="col-md-3 control-label"> Проходной балл </label>
            <div class="col-md-5">
                <div class="col-md-12 no-padding">
                    <div class="col-md-5 no-padding">
                        <div onClick="switchBtn(this,'{{ $model->id }}','passing_point')" class="switch-btn {{ !empty($model->passing_point_active) ? 'switch-on-color' : '' }}"></div>
                        <input id="passing_point_input{{ $model->id }}" type="hidden" value="{{ $model->passing_point_active }}" name="entrance_exam[{{ $model->id }}][passing_point_active]" />
                    </div>
                    <div id="passing_point_files{{ $model->id }}" class="{{ empty($model->passing_point_active) ? 'hide' : '' }}">
                        <div class="col-md-12 subform">

                            <div class="form-group col-md-7">
                                <label class="control-label"> Балл </label>
                                <input class="form-control" type="text" name="entrance_exam[{{ $model->id }}][passing_point]" value="{{ $model->passing_point ?? '' }}" />
                            </div>
                            <div class="clearfix"></div>
                            <div class="form-check">
                                <input onchange="changeChecked('passing_point_user_show'+'{{ $model->id }}')" class="form-check-input" type="checkbox" id="passing_point_user_show{{ $model->id }}" name="entrance_exam[{{ $model->id }}][passing_point_user_show]" value="{{ $model->passing_point_user_show ?? 1 }}" @if(!empty($model->passing_point_user_show) && ($model->passing_point_user_show != 0)) checked="checked" @endif />
                                <label class="form-check-label" for="passing_point_user_show{{ $model->id }}">
                                    Видно студенту
                                </label>
                            </div>
                            <div class="form-check">
                                <input onchange="changeChecked('passing_point_employee_show'+'{{ $model->id }}')" class="form-check-input" type="checkbox" id="passing_point_employee_show{{ $model->id }}" name="entrance_exam[{{ $model->id }}][passing_point_employee_show]" value="{{ $model->passing_point_employee_show ?? 1 }}" @if(!empty($model->passing_point_employee_show) && ($model->passing_point_employee_show != 0)) checked="checked" @endif />
                                <label class="form-check-label" for="passing_point_employee_show{{ $model->id }}">
                                    Видно сотруднику
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="clearfix"></div>


        <div class="form-group margin-15">

            <label class="col-md-3 control-label"> Ведомость </label>
            <div class="col-md-5">
                <div class="col-md-12 no-padding">
                    <div class="col-md-5 no-padding">
                        <div onClick="switchBtn(this,'{{ $model->id }}','statement')" class="switch-btn {{ !empty($model->statement_active) ? 'switch-on-color' : '' }}"></div>
                        <input id="statement_input{{ $model->id }}" type="hidden" value="{{ $model->statement_active }}" name="entrance_exam[{{ $model->id }}][statement_active]" />
                    </div>
                    <div id="statement_files{{ $model->id }}" class="{{ empty($model->statement_active) ? 'hide' : '' }}">
                        <div class="col-md-12 subform">
                            <div class="form-check">
                                <input onchange="changeChecked('statement_user_show'+'{{ $model->id }}')" class="form-check-input" type="checkbox" id="statement_user_show{{ $model->id }}" name="entrance_exam[{{ $model->id }}][statement_user_show]" value="{{ $model->statement_user_show ?? 1 }}" @if(!empty($model->statement_user_show)) checked="checked" @endif />
                                <label class="form-check-label" for="statement_user_show{{ $model->id }}">
                                    Видно студенту
                                </label>
                            </div>
                            <div class="form-check margin-b10">
                                <input onchange="changeChecked('statement_employee_show'+'{{ $model->id }}')" class="form-check-input" type="checkbox" id="statement_employee_show{{ $model->id }}" name="entrance_exam[{{ $model->id }}][statement_employee_show]" value="{{ $model->statement_employee_show ?? 1 }}" @if(!empty($model->statement_employee_show)) checked="checked" @endif />
                                <label class="form-check-label" for="statement_employee_show{{ $model->id }}">
                                    Видно сотруднику
                                </label>
                            </div>
                            @if( empty($model->getFiles( \App\EntranceExamFiles::TYPE_FILE_STATEMENT )) || count($model->getFiles( \App\EntranceExamFiles::TYPE_FILE_STATEMENT )) == 0 )
                                <div class="col-md-8 form-group">
                                    <input type="file" name="entrance_exam[{{ $model->id }}][statementFiles][file][]" value="" />
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
                                <a class="btn btn-default" onclick="addStatementFile('statement_files{{ $model->id }}','entrance_exam[{{ $model->id }}][statementFiles][file][]')"> Добавить файл <i class="fa fa-plus"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="clearfix"></div>


        <div class="form-group margin-15">

            <label class="col-md-3 control-label"> Состав комиссии </label>
            <div class="col-md-5">
                <div class="col-md-12 no-padding">
                    <div class="col-md-5 no-padding">
                        <div onClick="switchBtn(this,'{{ $model->id }}','commission_structure')" class="switch-btn {{ !empty($model->commission_structure_active) ? 'switch-on-color' : '' }}"></div>
                        <input id="commission_structure_input{{ $model->id }}" type="hidden" value="{{ $model->commission_structure_active }}" name="entrance_exam[{{ $model->id }}][commission_structure_active]" />
                    </div>
                    <div id="commission_structure_files{{ $model->id }}" class="{{ empty($model->commission_structure_active) ? 'hide' : '' }}">
                        <div class="col-md-12 subform">
                            <div class="form-check">
                                <input onchange="changeChecked('commission_structure_user_show'+'{{ $model->id }}')" class="form-check-input" type="checkbox" id="commission_structure_user_show{{ $model->id }}" name="entrance_exam[{{ $model->id }}][commission_structure_user_show]" value="{{ $model->commission_structure_user_show ?? 1 }}" @if(!empty($model->commission_structure_user_show)) checked="checked" @endif />
                                <label class="form-check-label" for="commission_structure_user_show{{ $model->id }}">
                                    Видно студенту
                                </label>
                            </div>
                            <div class="form-check margin-b10">
                                <input onchange="changeChecked('commission_structure_employee_show'+'{{ $model->id }}')" class="form-check-input" type="checkbox" id="commission_structure_employee_show{{ $model->id }}" name="entrance_exam[{{ $model->id }}][commission_structure_employee_show]" value="{{ $model->commission_structure_employee_show ?? 1 }}" @if(!empty($model->commission_structure_employee_show)) checked="checked" @endif />
                                <label class="form-check-label" for="commission_structure_employee_show{{ $model->id }}">
                                    Видно сотруднику
                                </label>
                            </div>
                            @if( empty($model->getFiles( \App\EntranceExamFiles::TYPE_FILE_COMMISSION_STRUCTURE )) || count($model->getFiles( \App\EntranceExamFiles::TYPE_FILE_COMMISSION_STRUCTURE )) == 0 )
                                <div class="col-md-8 form-group">
                                    <input type="file" name="entrance_exam[{{ $model->id }}][commissionStructureFiles][file][]" value="" />
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
                                <a class="btn btn-default" onclick="addCommissionStructureFile('commission_structure_files{{ $model->id }}','entrance_exam[{{ $model->id }}][commissionStructureFiles][file][]')"> Добавить файл <i class="fa fa-plus"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
        <div class="clearfix"></div>


        <div class="form-group margin-15">

            <label class="col-md-3 control-label"> Состав аппеляционной комиссии </label>
            <div class="col-md-5">
                <div class="col-md-12 no-padding">
                    <div class="col-md-5 no-padding">
                        <div onClick="switchBtn(this,'{{ $model->id }}','composition_appeal_commission')" class="switch-btn {{ !empty($model->composition_appeal_commission_active) ? 'switch-on-color' : '' }}"></div>
                        <input id="composition_appeal_commission_input{{ $model->id }}" type="hidden" value="{{ $model->composition_appeal_commission_active }}" name="entrance_exam[{{ $model->id }}][composition_appeal_commission_active]" />
                    </div>
                    <div id="composition_appeal_commission_files{{ $model->id }}" class="{{ empty($model->composition_appeal_commission_active) ? 'hide' : '' }}">
                        <div class="col-md-12 subform">
                            <div class="form-check">
                                <input onchange="changeChecked('composition_appeal_commission_user_show'+'{{ $model->id }}')" class="form-check-input" type="checkbox" id="composition_appeal_commission_user_show{{ $model->id }}" name="entrance_exam[{{ $model->id }}][composition_appeal_commission_user_show]" value="{{ $model->composition_appeal_commission_user_show ?? 1 }}" @if(!empty($model->composition_appeal_commission_user_show)) checked="checked" @endif />
                                <label class="form-check-label" for="composition_appeal_commission_user_show{{ $model->id }}">
                                    Видно студенту
                                </label>
                            </div>
                            <div class="form-check margin-b10">
                                <input onchange="changeChecked('composition_appeal_commission_employee_show'+'{{ $model->id }}')" class="form-check-input" type="checkbox" id="composition_appeal_commission_employee_show{{ $model->id }}" name="entrance_exam[{{ $model->id }}][composition_appeal_commission_employee_show]" value="{{ $model->composition_appeal_commission_employee_show ?? 1 }}" @if(!empty($model->composition_appeal_commission_employee_show)) checked="checked" @endif />
                                <label class="form-check-label" for="composition_appeal_commission_employee_show{{ $model->id }}">
                                    Видно сотруднику
                                </label>
                            </div>
                            @if( empty($model->getFiles( \App\EntranceExamFiles::TYPE_FILE_COMMISSION_APPEAL_STRUCTURE )) || count($model->getFiles( \App\EntranceExamFiles::TYPE_FILE_COMMISSION_APPEAL_STRUCTURE )) == 0 )
                                <div class="col-md-8 form-group">
                                    <input type="file" name="entrance_exam[{{ $model->id }}][compositionAppealCommissionFiles][file][]" value="" />
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
                                <a class="btn btn-default" onclick="addCompositionAppealCommissionFile('composition_appeal_commission_files{{ $model->id }}','entrance_exam[{{ $model->id }}][compositionAppealCommissionFiles][file][]')"> Добавить файл <i class="fa fa-plus"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="clearfix"></div>


        <div class="form-group margin-15">

            <label class="col-md-3 control-label"> Расписание </label>
            <div class="col-md-5">
                <div class="col-md-12 no-padding">
                    <div class="col-md-5 no-padding">
                        <div onClick="switchBtn(this,'{{ $model->id }}','schedule')" class="switch-btn {{ !empty($model->schedule_active) ? 'switch-on-color' : '' }}"></div>
                        <input id="schedule_input{{ $model->id }}" type="hidden" value="{{ $model->schedule_active }}" name="entrance_exam[{{ $model->id }}][schedule_active]" />
                    </div>
                    <div id="schedule_files{{ $model->id }}" class="{{ empty($model->schedule_active) ? 'hide' : '' }}">
                        <div class="col-md-12 subform">
                            <div class="form-check">
                                <input onchange="changeChecked('schedule_user_show'+'{{ $model->id }}')" class="form-check-input" type="checkbox" id="schedule_user_show{{ $model->id }}" name="entrance_exam[{{ $model->id }}][schedule_user_show]" value="{{ $model->schedule_user_show ?? 1 }}" @if(!empty($model->schedule_user_show)) checked="checked" @endif />
                                <label class="form-check-label" for="schedule_user_show{{ $model->id }}">
                                    Видно студенту
                                </label>
                            </div>
                            <div class="form-check margin-b10">
                                <input onchange="changeChecked('schedule_employee_show'+'{{ $model->id }}')" class="form-check-input" type="checkbox" id="schedule_employee_show{{ $model->id }}" name="entrance_exam[{{ $model->id }}][schedule_employee_show]" value="{{ $model->schedule_employee_show ?? 1 }}" @if(!empty($model->schedule_employee_show)) checked="checked" @endif />
                                <label class="form-check-label" for="schedule_employee_show{{ $model->id }}">
                                    Видно сотруднику
                                </label>
                            </div>
                            @if( empty($model->getFiles( \App\EntranceExamFiles::TYPE_FILE_SCHEDULE )) || count($model->getFiles( \App\EntranceExamFiles::TYPE_FILE_SCHEDULE )) == 0 )
                                <div class="col-md-8 form-group">
                                    <input type="file" name="entrance_exam[{{ $model->id }}][scheduleFiles][file][]" value="" />
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
                                <a class="btn btn-default" onclick="addScheduleFile('schedule_files{{ $model->id }}','entrance_exam[{{ $model->id }}][scheduleFiles][file][]')"> Добавить файл <i class="fa fa-plus"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
        <div class="clearfix"></div>


        <div class="form-group margin-15">

            <label class="col-md-3 control-label"> Общие протоколы по результатам творческих экзаменов </label>
            <div class="col-md-5">

                <div class="col-md-12 no-padding">
                    <div class="col-md-5 no-padding">
                        <div onClick="switchBtn(this,'{{ $model->id }}','protocols_creative_exams')" class="switch-btn {{ !empty($model->protocols_creative_exams_active) ? 'switch-on-color' : '' }}"></div>
                        <input id="protocols_creative_exams_input{{ $model->id }}" type="hidden" value="{{ $model->protocols_creative_exams_active }}" name="entrance_exam[{{ $model->id }}][protocols_creative_exams_active]" />
                    </div>
                    <div id="protocols_creative_exams_files{{ $model->id }}" class="{{ empty($model->protocols_creative_exams_active) ? 'hide' : '' }}">
                        <div class="col-md-12 subform">
                            <div class="form-check">
                                <input onchange="changeChecked('protocols_creative_exams_user_show'+'{{ $model->id }}')" class="form-check-input" type="checkbox" id="protocols_creative_exams_user_show{{ $model->id }}" name="entrance_exam[{{ $model->id }}][protocols_creative_exams_user_show]" value="{{ $model->protocols_creative_exams_user_show ?? 1 }}" @if(!empty($model->protocols_creative_exams_user_show)) checked="checked" @endif />
                                <label class="form-check-label" for="protocols_creative_exams_user_show{{ $model->id }}">
                                    Видно студенту
                                </label>
                            </div>
                            <div class="form-check margin-b10">
                                <input onchange="changeChecked('protocols_creative_exams_employee_show'+'{{ $model->id }}')" class="form-check-input" type="checkbox" id="protocols_creative_exams_employee_show{{ $model->id }}" name="entrance_exam[{{ $model->id }}][protocols_creative_exams_employee_show]" value="{{ $model->protocols_creative_exams_employee_show ?? 1 }}" @if(!empty($model->protocols_creative_exams_employee_show)) checked="checked" @endif />
                                <label class="form-check-label" for="protocols_creative_exams_employee_show{{ $model->id }}">
                                    Видно сотруднику
                                </label>
                            </div>
                            @if( empty($model->getFiles( \App\EntranceExamFiles::TYPE_FILE_PROTOCOLS_CREATIVE_EXAMS )) || count($model->getFiles( \App\EntranceExamFiles::TYPE_FILE_PROTOCOLS_CREATIVE_EXAMS )) == 0 )
                                <div class="col-md-8 form-group">
                                    <input type="file" name="entrance_exam[{{ $model->id }}][protocolsCreativeExamsFiles][file][]" value="" />
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
                                <a class="btn btn-default" onclick="addProtocolsCreativeExamsFile('protocols_creative_exams_files{{ $model->id }}','entrance_exam[{{ $model->id }}][protocolsCreativeExamsFiles][file][]')"> Добавить файл <i class="fa fa-plus"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
        <div class="clearfix"></div>


        <div class="form-group margin-15">

            <label class="col-md-3 control-label"> Общие протоколы по апелляционной комиссии </label>
            <div class="col-md-5">
                <div class="col-md-12 no-padding">
                    <div class="col-md-5 no-padding">
                        <div onClick="switchBtn(this,'{{ $model->id }}','protocols_appeal_commission')" class="switch-btn {{ !empty($model->protocols_appeal_commission_active) ? 'switch-on-color' : '' }}"></div>
                        <input id="protocols_appeal_commission_input{{ $model->id }}" type="hidden" value="{{ $model->protocols_appeal_commission_active }}" name="entrance_exam[{{ $model->id }}][protocols_appeal_commission_active]" />
                    </div>
                    <div id="protocols_appeal_commission_files{{ $model->id }}" class="{{ empty($model->protocols_appeal_commission_active) ? 'hide' : '' }}">
                        <div class="col-md-12 subform">
                            <div class="form-check">
                                <input onchange="changeChecked('protocols_appeal_commission_user_show'+'{{ $model->id }}')" class="form-check-input" type="checkbox" id="protocols_appeal_commission_user_show{{ $model->id }}" name="entrance_exam[{{ $model->id }}][protocols_appeal_commission_user_show]" value="{{ $model->protocols_appeal_commission_user_show ?? 1 }}" @if(!empty($model->protocols_appeal_commission_user_show)) checked="checked" @endif />
                                <label class="form-check-label" for="protocols_appeal_commission_user_show{{ $model->id }}">
                                    Видно студенту
                                </label>
                            </div>
                            <div class="form-check margin-b10">
                                <input onchange="changeChecked('protocols_appeal_commission_employee_show'+'{{ $model->id }}')" class="form-check-input" type="checkbox" id="protocols_appeal_commission_employee_show{{ $model->id }}" name="entrance_exam[{{ $model->id }}][protocols_appeal_commission_employee_show]" value="{{ $model->protocols_appeal_commission_employee_show ?? 1 }}" @if(!empty($model->protocols_appeal_commission_employee_show)) checked="checked" @endif />
                                <label class="form-check-label" for="protocols_appeal_commission_employee_show{{ $model->id }}">
                                    Видно сотруднику
                                </label>
                            </div>
                            @if( empty($model->getFiles( \App\EntranceExamFiles::TYPE_FILE_PROTOCOLS_APPEAL_COMMISSION )) || count($model->getFiles( \App\EntranceExamFiles::TYPE_FILE_PROTOCOLS_APPEAL_COMMISSION )) == 0 )
                                <div class="col-md-8 form-group">
                                    <input type="file" name="entrance_exam[{{ $model->id }}][protocolsAppealCommissionFiles][file][]" value="" />
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
                                <a class="btn btn-default" onclick="addProtocolsAppealCommissionFile('protocols_appeal_commission_files{{ $model->id }}','entrance_exam[{{ $model->id }}][protocolsAppealCommissionFiles][file][]')"> Добавить файл <i class="fa fa-plus"></i></a>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>
        <div class="clearfix"></div>


        <div class="form-group margin-15">
            <label class="col-md-3 control-label"> Отчеты по творческим и специальным экзаменам направленных в МОН РК </label>
            <div class="col-md-5">

                <div class="col-md-12 no-padding">
                    <div class="col-md-5 no-padding">
                        <div onClick="switchBtn(this,'{{ $model->id }}','report_exams')" class="switch-btn {{ !empty($model->report_exams_active) ? 'switch-on-color' : '' }}"></div>
                        <input id="report_exams_input{{ $model->id }}" type="hidden" value="{{ $model->report_exams_active }}" name="entrance_exam[{{ $model->id }}][report_exams_active]" />
                    </div>
                    <div id="report_exams_files{{ $model->id }}" class="{{ empty($model->report_exams_active) ? 'hide' : '' }}">
                        <div class="col-md-12 subform">
                            <div class="form-check">
                                <input onchange="changeChecked('report_exams_user_show'+'{{ $model->id }}')" class="form-check-input" type="checkbox" id="report_exams_user_show{{ $model->id }}" name="entrance_exam[{{ $model->id }}][report_exams_user_show]" value="{{ $model->report_exams_user_show ?? 1 }}" @if(!empty($model->report_exams_user_show)) checked="checked" @endif />
                                <label class="form-check-label" for="report_exams_user_show{{ $model->id }}">
                                    Видно студенту
                                </label>
                            </div>
                            <div class="form-check margin-b10">
                                <input onchange="changeChecked('report_exams_employee_show'+'{{ $model->id }}')" class="form-check-input" type="checkbox" id="report_exams_employee_show{{ $model->id }}" name="entrance_exam[{{ $model->id }}][report_exams_employee_show]" value="{{ $model->report_exams_employee_show ?? 1 }}" @if(!empty($model->report_exams_employee_show)) checked="checked" @endif />
                                <label class="form-check-label" for="report_exams_employee_show{{ $model->id }}">
                                    Видно сотруднику
                                </label>
                            </div>
                            @if( empty($model->getFiles( \App\EntranceExamFiles::TYPE_FILE_REPORT_EXAMS )) || count($model->getFiles( \App\EntranceExamFiles::TYPE_FILE_REPORT_EXAMS )) == 0 )
                                <div class="col-md-8 form-group">
                                    <input type="file" name="entrance_exam[{{ $model->id }}][reportExamsFiles][file][]" value="" />
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
                                <a class="btn btn-default" onclick="addReportExamsFile('report_exams_files{{ $model->id }}','entrance_exam[{{ $model->id }}][reportExamsFiles][file][]')"> Добавить файл <i class="fa fa-plus"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="clearfix"></div>


        <div class="form-group margin-15">

                <label class="col-md-3 control-label"> Ручная проверка </label>
                <div class="col-md-5">
                    <div class="col-md-12 no-padding">
                        <div class="col-md-5 no-padding">
                            <div onClick="switchBtn(this,'{{ $model->id }}','custom_checked')" class="switch-btn {{ !empty($model->custom_checked_active) ? 'switch-on-color' : '' }}"></div>
                            <input id="custom_checked_input{{ $model->id }}" type="hidden" value="{{ $model->custom_checked_active }}" name="entrance_exam[{{ $model->id }}][custom_checked_active]" />
                        </div>
                        <div id="custom_checked_files{{ $model->id }}" class="{{ empty($model->custom_checked_active) ? 'hide' : '' }}">
                            <div class="col-md-12 subform">
                                <div class="form-check">
                                    <input onchange="changeChecked('custom_checked_user_show'+'{{ $model->id }}')" class="form-check-input" type="checkbox" id="custom_checked_user_show{{ $model->id }}" name="entrance_exam[{{ $model->id }}][custom_checked_user_show]" value="{{ $model->custom_checked_user_show ?? 1 }}" @if(!empty($model->custom_checked_user_show)) checked="checked" @endif />
                                    <label class="form-check-label" for="custom_checked_user_show{{ $model->id }}">
                                        Видно студенту
                                    </label>
                                </div>
                                <div class="form-check margin-b10">
                                    <input onchange="changeChecked('custom_checked_employee_show'+'{{ $model->id }}')" class="form-check-input" type="checkbox" id="custom_checked_employee_show{{ $model->id }}" name="entrance_exam[{{ $model->id }}][custom_checked_employee_show]" value="{{ $model->custom_checked_employee_show ?? 1 }}" @if(!empty($model->custom_checked_employee_show)) checked="checked" @endif />
                                    <label class="form-check-label" for="custom_checked_employee_show{{ $model->id }}">
                                        Видно сотруднику
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

        </div>
        <div class="clearfix"></div>

    </div>
</div>
@if( !empty($loadEE) )
    <div class="col-md-1" onclick="entranceItemRemove(this,'{{ $model->id }}')"><a class="btn btn-danger">Удалить <i class="fa fa-remove"></i></a></div><div class="clearfix"></div>
@endif
