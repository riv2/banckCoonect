<?php
$hasRightEdit = \App\Services\Auth::user()->hasRight('students', 'edit');
$hasRightAddComment = \App\Services\Auth::user()->hasRight('students', 'create_student_comment');
?>

@extends("admin.admin_app")

@section('title', 'Студент ' . $student->studentProfile->fio)

@section("content")

    <div id="main">
        <div class="page-header">
            <h2>{{$student->studentProfile->fio}} (id {{$student->id}})</h2>

            <a href="{{ url()->previous() }}" class="btn btn-default-light btn-xs"><i class="md md-backspace"></i> Назад</a>
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

        {!! Form::open(['url' => route('adminStudentEdit', ['id' => $student->id]),'class'=>'form-horizontal padding-15','name'=>'student_form','id'=>'student_form','role'=>'form','enctype' => 'multipart/form-data', 'autocomplete'=>'off']) !!}
        <div id="main-vue" class="accordion">

        <div class="panel panel-default">
            <div class="panel-heading collapsed" data-toggle="collapse" data-target="#profileData">Профиль</div>
            <div class="panel-body collapse" id="profileData">
                @include('admin.pages.students.edit-profile')             
            </div>
        </div>

        @if($student->bcApplication)
            <div class="panel panel-default">
                <div class="panel-heading collapsed" data-toggle="collapse" data-target="#bachelorData">Анкета бакалавра</div>
                <div class="panel-body collapse" id="bachelorData">
                    @include('admin.pages.students.bc_application.main')
                </div>
            </div>
        @endif

        @if($student->mgApplication)
        <div class="panel panel-default">
            <div class="panel-heading collapsed" data-toggle="collapse" data-target="#magisterData">Анкета магистра</div>
            <div class="panel-body collapse" id="magisterData">
                @include('admin.pages.students.mg_application.main')
            </div>
        </div>
        @endif

        <div class="panel panel-default">
            <div class="panel-heading collapsed" data-toggle="collapse" data-target="#docsData">Документы</div>
            <div class="panel-body collapse" id="docsData">
                @include('admin.pages.students.edit-docs')
            </div>
        </div>

        

        <!-- Transaction History -->
        <div class="panel panel-default">
            <div class="panel-heading collapsed" data-toggle="collapse" data-target="#transactionHistoryData">История транзакций</div>
            <div class="panel-body collapse" id="transactionHistoryData">
                <div class="alert alert-info alert-dismissible" role="alert">
                    {{ __('The request for the transaction to take up to 5 min') }}
                </div>

                <div v-if="auditMessage" :class="{ 'alert-danger': auditIsError, 'alert-success': !auditIsError }" class="alert">
                    @{{ auditMessage }}
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"> {{ __('Transaction search') }} </h3>
                    </div>
                    <div class="panel-body">

                        <div class="form-group">
                            <label for="date_from" class="col-md-3 control-label text-right">{{__('Date from')}}</label>
                            <div class="col-md-3">
                                <input v-model="auditDateFrom" id="date_from" type="date" class="form-control" value="" autofocus maxlength="9">
                            </div>
                            <div class="col-md-6"> &nbsp; </div>
                            <div class="clearfix"></div>
                        </div>

                        <div class="form-group">
                            <label for="date_to" class="col-md-3 control-label text-right">{{__('Date to')}}</label>
                            <div class="col-md-3">
                                <input v-model="auditDateTo" id="date_to" type="date" class="form-control" value="" autofocus maxlength="9">
                            </div>
                            <div class="col-md-6"> &nbsp; </div>
                            <div class="clearfix"></div>
                        </div>

                        <br>

                        <div class="form-group">
                            <div class="col-md-3 col-md-offset-3">
                                <button @click="auditAddtHistory" :disabled="auditSendRequest" class="btn btn-primary btn-sm" type="button"> {{ __('Search') }} </button>
                            </div>
                            <div class="col-md-6"> &nbsp; </div>
                            <div class="clearfix"></div>
                        </div>


                    </div>
                </div>

                <div class="no-padding">
                    <table id="data-table-history" class="table table-striped table-hover dt-responsive" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th> {{ __('Type') }} </th>
                            <th> {{ __('Code') }} </th>
                            <th> {{ __('Name') }} </th>
                            <th> {{ __('Cost') }} </th>
                            <th> {{ __('Date') }} </th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <!-- Transaction History -->

        @if($student->studentProfile->speciality)
            <div class="panel panel-default">
                <div class="panel-heading collapsed" data-toggle="collapse" data-target="#specialityData">Специальность</div>
                <div class="panel-body collapse" id="specialityData">
                    {{ $student->studentProfile->speciality->name }}
                </div>
            </div>
        @endif

        @if($student->submodules)
            <div class="panel panel-default">
                <div class="panel-heading collapsed" data-toggle="collapse" data-target="#submoduleData">Сабмодули</div>
                <div class="panel-body collapse" id="submoduleData">
                    @include('admin.pages.students.disciplines.submodules_list')
                </div>
            </div>
        @endif

        @if($student->disciplines)
            <div class="panel panel-default">
                <div class="panel-heading collapsed" data-toggle="collapse" data-target="#disciplinesData">Дисциплины</div>
                <div class="panel-body collapse" id="disciplinesData">
                    @include('admin.pages.students.disciplines.list')
                </div>
            </div>
        @endif

        
        <div class="panel panel-default">
            <div class="panel-heading collapsed" data-toggle="collapse" data-target="#govExamData">Комплексный государственный экзамен</div>
            <div class="panel-body collapse" id="govExamData">
                @if($student->quize_result_kge)
                    {{$student->quize_result_kge->value}} ({{$student->quize_result_kge->letter}})
                    <a href="{{ route('adminStudentShowResult', ['id'=> $student->id, 'disciplineId'=>'kge']) }}">Ответы</a>
                    <a onclick="deleteResultKge()">Удалить</a>
                @else
                    Нет результатов.
                @endif
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading collapsed" data-toggle="collapse" data-target="#commentsData">Комментарии</div>
            <div class="panel-body collapse" id="commentsData" style="max-height: 500px; overflow-y: auto;">
                <div class="alert alert-warning" v-for="comment in comments">
                    @{{ comment.text }}
                    <p>
                        <sub v-if="comment.check_level == 'inspection'">Приемка</sub>
                        <sub v-if="comment.check_level == 'or_cabinet'">Кабинет ОР</sub>
                        <sub> - @{{ comment.author }}</sub>
                        <sub v-if="comment.date != ''"> - @{{ comment.date }}</sub>
                    </p>
                </div>
                <hr>
                @if( $hasRightAddComment )
                    <textarea name="comment" v-model="comment" class="form-control"></textarea>
                    <br>
                    <a class="btn btn-primary" v-on:click="addComment()">Добавить комментарий</a>                
                @endif

            </div>
            
        </div>

        <div class="panel panel-default">
            <div class="panel-heading collapsed" data-toggle="collapse" data-target="#notificationsData">Уведомления</div>
            <div class="panel-body collapse" id="notificationsData" style="max-height: 500px; overflow-y: auto;">
                @foreach($student->notifications as $notification)
                <div class="alert alert-warning">
                    <a onclick="deleteNotification({{ $notification->id }})" class="close" >x</a>
                    {!! $notification->text !!}
                </div>
                @endforeach
                <hr>
                @if( $hasRightEdit )
                    <a class="btn btn-primary" onclick="showNotificationModal()">Добавить уведомление</a>
                @endif
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading collapsed" data-toggle="collapse" data-target="#ordersListData">История приказов</div>
            <div class="panel-body collapse" id="ordersListData">
                @include('admin.pages.students.orders-list')
            </div>
        </div>


        <div class="panel panel-default">
            <div class="panel-heading collapsed" data-toggle="collapse" data-target="#nobdData">Данные НОБД</div>
            <div class="panel-body collapse padding-15" id="nobdData">
                <div class="padding-20">


                    <div class="col-md-12 form-group">
                        <div class="col-md-3">
                            <label class="pull-right text-right"> Студент обучается по обмену </label>
                        </div>
                        <div class="col-md-3">
                            <select v-model="nobdData.study_exchange" class="form-control" name="nobdUser[study_exchange]">
                                <option> ... </option>
                                @if( !empty($studyExchange) )
                                    @foreach( $studyExchange as $itemSE )
                                        <option value="{{ $itemSE->id }}"> {{ $itemSE->name }} </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <div v-if="(nobdData.study_exchange == 2) || (nobdData.study_exchange == 3) || (nobdData.study_exchange == 4)" class="col-md-12 form-group">
                        <div class="col-md-3">
                            <label class="pull-right text-right"> Принимающая страна / Зарубежная страна отправитель </label>
                        </div>
                        <div class="col-md-3">
                            <select v-model="nobdData.host_country" class="form-control" name="nobdUser[host_country]">
                                <option> ... </option>
                                @if( !empty($countryList) )
                                    @foreach( $countryList as $itemCL )
                                        <option value="{{ $itemCL->id }}"> {{ $itemCL->name }} </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <div v-if="nobdData.study_exchange != 1" class="col-md-12 form-group">
                        <div class="col-md-3">
                            <label class="pull-right text-right"> Наименование принимающего зарубежного вуза-партнера </label>
                        </div>
                        <div class="col-md-3">
                            <input v-model="nobdData.host_university_name" value="{{ $student->nobdUser->host_university_name ?? '' }}" class="form-control" name="nobdUser[host_university_name]" type="text" />
                        </div>
                    </div>

                    <div v-if="nobdData.study_exchange != 1" class="col-md-12 form-group">
                        <div class="col-md-3">
                            <label class="pull-right text-right"> Язык обучения в принимающем вузе </label>
                        </div>
                        <div class="col-md-3">
                            <select v-model="nobdData.host_university_language" class="form-control" name="nobdUser[host_university_language]">
                                <option> ... </option>
                                @if( !empty($language) )
                                    @foreach( $language as $itemL )
                                        <option value="{{ $itemL->id }}"> {{ $itemL->name }} </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <div v-if="(nobdData.study_exchange == 2) || (nobdData.study_exchange == 4) || (nobdData.study_exchange == 5)" class="col-md-12 form-group">
                        <div class="col-md-3">
                            <label class="pull-right text-right"> Специальность по обмену </label>
                        </div>
                        <div class="col-md-3">
                            <select v-model="nobdData.exchange_specialty" class="form-control" name="nobdUser[exchange_specialty]">
                                <option> ... </option>
                                @if( !empty($exchangeSpecialty) )
                                    @foreach( $exchangeSpecialty as $itemES )
                                        <option value="{{ $itemES->id }}"> {{ $itemES->name }} </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <div v-if="nobdData.study_exchange == 3" class="col-md-12 form-group">
                        <div class="col-md-3">
                            <label class="pull-right text-right"> Специальность по обмену </label>
                        </div>
                        <div class="col-md-3">
                            <input v-model="nobdData.exchange_specialty_st" value="{{ $student->nobdUser->exchange_specialty_st ?? '' }}" class="form-control" name="nobdUser[exchange_specialty_st]" type="text" />
                        </div>
                    </div>

                    <div v-if="nobdData.study_exchange != 1" class="col-md-12 form-group">
                        <div class="col-md-3">
                            <label class="pull-right text-right"> Начало срока пребывания по обмену </label>
                        </div>
                        <div class="col-md-3">
                            <input v-model="nobdData.exchange_date_start" value="{{ $student->nobdUser->exchange_date_start ?? null }}" class="form-control" name="nobdUser[exchange_date_start]" type="date" />
                        </div>
                    </div>

                    <div v-if="nobdData.study_exchange != 1" class="col-md-12 form-group">
                        <div class="col-md-3">
                            <label class="pull-right text-right"> Окончание срока пребывания </label>
                        </div>
                        <div class="col-md-3">
                            <input v-model="nobdData.exchange_date_end" value="{{ $student->nobdUser->exchange_date_end ?? null }}" class="form-control" name="nobdUser[exchange_date_end]" type="date" />
                        </div>
                    </div>

                    <div v-if="nobdData.study_exchange != 1" class="col-md-12 form-group">
                        <div class="col-md-3">
                            <label class="pull-right text-right"> Академическая мобильность </label>
                        </div>
                        <div class="col-md-3">
                            <select v-model="nobdData.academic_mobility" class="form-control" name="nobdUser[academic_mobility]">
                                <option> ... </option>
                                @if( !empty($academicMobility) )
                                    @foreach( $academicMobility as $itemAM )
                                        <option value="{{ $itemAM->id }}"> {{ $itemAM->name }} </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>


                    <div class="clearfix padding-30">&nbsp;</div>


                    <div class="col-md-12 form-group">
                        <div class="col-md-3">
                            <label class="pull-right text-right"> Находится в академическом отпуске </label>
                        </div>
                        <div class="col-md-3">
                            <select v-model="nobdData.academic_leave" class="form-control" name="nobdUser[academic_leave]">
                                <option> ... </option>
                                @if( !empty($academicLeave) )
                                    @foreach( $academicLeave as $itemAL )
                                        <option value="{{ $itemAL->id }}"> {{ $itemAL->name }} </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <div v-if="nobdData.academic_leave != 4" class="col-md-12 form-group">
                        <div class="col-md-3">
                            <label class="pull-right text-right"> № приказа о предоставлении обучающемуся академического отпуска </label>
                        </div>
                        <div class="col-md-3">
                            <input v-model="nobdData.academic_leave_order_number" value="{{ $student->nobdUser->academic_leave_order_number ?? '' }}" class="form-control" name="nobdUser[academic_leave_order_number]" type="text" />
                        </div>
                    </div>

                    <div v-if="nobdData.academic_leave != 4" class="col-md-12 form-group">
                        <div class="col-md-3">
                            <label class="pull-right text-right"> Дата приказа о предоставлении обучающемуся академического отпуска </label>
                        </div>
                        <div class="col-md-3">
                            <input v-model="nobdData.academic_leave_order_date" value="{{ $student->nobdUser->academic_leave_order_date ?? null }}" class="form-control" name="nobdUser[academic_leave_order_date]" type="date" />
                        </div>
                    </div>

                    <div v-if="nobdData.academic_leave != 4" class="col-md-12 form-group">
                        <div class="col-md-3">
                            <label class="pull-right text-right"> № приказа о выходе обучающегося из академического отпуска </label>
                        </div>
                        <div class="col-md-3">
                            <input v-model="nobdData.academic_leave_out_order_number" value="{{ $student->nobdUser->academic_leave_out_order_number ?? '' }}" class="form-control" name="nobdUser[academic_leave_out_order_number]" type="text" />
                        </div>
                    </div>

                    <div v-if="nobdData.academic_leave != 4" class="col-md-12 form-group">
                        <div class="col-md-3">
                            <label class="pull-right text-right"> Дата приказа о выходе обучающегося из академического отпуска </label>
                        </div>
                        <div class="col-md-3">
                            <input v-model="nobdData.academic_leave_out_order_date" value="{{ $student->nobdUser->academic_leave_out_order_date ?? null }}" class="form-control" name="nobdUser[academic_leave_out_order_date]" type="date" />
                        </div>
                    </div>


                    <div class="clearfix padding-10">&nbsp;</div>


                    <div class="col-md-12 form-check">
                        <div class="col-md-3"></div>
                        <div class="col-md-4">
                            <input onchange="changeChecked('passing_point_employee_show')" class="form-check-input" type="checkbox" id="is_national_student_league" name="nobdUser[is_national_student_league]" value="{{ $student->nobdUser->is_national_student_league ?? 1 }}" @if(!empty($student->nobdUser->is_national_student_league) && ($student->nobdUser->is_national_student_league != 0)) checked="checked" @endif />
                            <label class="form-check-label" for="is_national_student_league">
                                Участвует в Национальной студенческой лиге
                            </label>
                        </div>
                    </div>

                    <div class="col-md-12 form-check">
                        <div class="col-md-3"></div>
                        <div class="col-md-4">
                            <input onchange="changeChecked('is_world_winter_universiade')" class="form-check-input" type="checkbox" id="is_world_winter_universiade" name="nobdUser[is_world_winter_universiade]" value="{{ $student->nobdUser->is_world_winter_universiade ?? 1 }}" @if(!empty($student->nobdUser->is_world_winter_universiade) && ($student->nobdUser->is_world_winter_universiade != 0)) checked="checked" @endif />
                            <label class="form-check-label" for="is_world_winter_universiade">
                                Участвует во всемирной зимней Универсиаде
                            </label>
                        </div>
                    </div>

                    <div class="col-md-12 form-check">
                        <div class="col-md-3"></div>
                        <div class="col-md-4">
                            <input onchange="changeChecked('is_world_summer_universiade')" class="form-check-input" type="checkbox" id="is_world_summer_universiade" name="nobdUser[is_world_summer_universiade]" value="{{ $student->nobdUser->is_world_summer_universiade ?? 1 }}" @if(!empty($student->nobdUser->is_world_summer_universiade) && ($student->nobdUser->is_world_summer_universiade != 0)) checked="checked" @endif />
                            <label class="form-check-label" for="is_world_summer_universiade">
                                Участвует во всемирной летней Универсиаде
                            </label>
                        </div>
                    </div>

                    <div class="col-md-12 form-check">
                        <div class="col-md-3"></div>
                        <div class="col-md-4">
                            <input onchange="changeChecked('is_winter_universiade_republic_kz')" class="form-check-input" type="checkbox" id="is_winter_universiade_republic_kz" name="nobdUser[is_winter_universiade_republic_kz]" value="{{ $student->nobdUser->is_winter_universiade_republic_kz ?? 1 }}" @if(!empty($student->nobdUser->is_winter_universiade_republic_kz) && ($student->nobdUser->is_winter_universiade_republic_kz != 0)) checked="checked" @endif />
                            <label class="form-check-label" for="is_winter_universiade_republic_kz">
                                Участвует в зимней Универсиаде Республики Казахстан
                            </label>
                        </div>
                    </div>

                    <div class="col-md-12 form-check">
                        <div class="col-md-3"></div>
                        <div class="col-md-4">
                            <input onchange="changeChecked('is_summer_universiade_republic_kz')" class="form-check-input" type="checkbox" id="is_summer_universiade_republic_kz" name="nobdUser[is_summer_universiade_republic_kz]" value="{{ $student->nobdUser->is_summer_universiade_republic_kz ?? 1 }}" @if(!empty($student->nobdUser->is_summer_universiade_republic_kz) && ($student->nobdUser->is_summer_universiade_republic_kz != 0)) checked="checked" @endif />
                            <label class="form-check-label" for="is_summer_universiade_republic_kz">
                                Участвует в летней Универсиаде Республики Казахстан
                            </label>
                        </div>
                    </div>

                    <div class="col-md-12 form-check">
                        <div class="col-md-3"></div>
                        <div class="col-md-4">
                            <input onchange="changeChecked('is_nonresident_student')" class="form-check-input" type="checkbox" id="is_nonresident_student" name="nobdUser[is_nonresident_student]" value="{{ $student->nobdUser->is_nonresident_student ?? 1 }}" @if(!empty($student->nobdUser->is_nonresident_student) && ($student->nobdUser->is_nonresident_student != 0)) checked="checked" @endif />
                            <label class="form-check-label" for="is_nonresident_student">
                                Иногородний студент
                            </label>
                        </div>
                    </div>

                    <div class="col-md-12 form-check">
                        <div class="col-md-3"></div>
                        <div class="col-md-4">
                            <input onchange="changeChecked('is_needs_hostel')" class="form-check-input" type="checkbox" id="is_needs_hostel" name="nobdUser[is_needs_hostel]" value="{{ $student->nobdUser->is_needs_hostel ?? 1 }}" @if(!empty($student->nobdUser->is_needs_hostel) && ($student->nobdUser->is_needs_hostel != 0)) checked="checked" @endif />
                            <label class="form-check-label" for="is_needs_hostel">
                                Нуждается в общежитии
                            </label>
                        </div>
                    </div>

                    <div class="col-md-12 form-check">
                        <div class="col-md-3"></div>
                        <div class="col-md-4">
                            <input onchange="changeChecked('is_lives_hostel')" class="form-check-input" type="checkbox" id="is_lives_hostel" name="nobdUser[is_lives_hostel]" value="{{ $student->nobdUser->is_lives_hostel ?? 1 }}" @if(!empty($student->nobdUser->is_lives_hostel) && ($student->nobdUser->is_lives_hostel != 0)) checked="checked" @endif />
                            <label class="form-check-label" for="is_lives_hostel">
                                Проживает в общежитии
                            </label>
                        </div>
                    </div>


                    <div class="clearfix padding-10">&nbsp;</div>


                    <div class="col-md-12 form-group">
                        <div class="col-md-3">
                            <label class="pull-right text-right"> Обучение за счет средств </label>
                        </div>
                        <div class="col-md-3">
                            <select v-model="nobdData.payment_type" class="form-control" name="nobdUser[payment_type]">
                                <option> ... </option>
                                @if( !empty($paymentType) )
                                    @foreach( $paymentType as $itemPT )
                                        <option value="{{ $itemPT->id }}"> {{ $itemPT->name }} </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12 form-group">
                        <div class="col-md-3">
                            <label class="pull-right text-right"> Стоимость обучения (за год), тысяч тенге </label>
                        </div>
                        <div class="col-md-3">
                            <input v-model="nobdData.cost_education" value="{{ $student->nobdUser->cost_education ?? '' }}" class="form-control" name="nobdUser[cost_education]" type="number" />
                        </div>
                    </div>

                    <div class="col-md-12 form-group">
                        <div class="col-md-3">
                            <label class="pull-right text-right"> № свидетельства об присуждении  гранта </label>
                        </div>
                        <div class="col-md-3">
                            <input v-model="nobdData.number_grant_certificate" value="{{ $student->nobdUser->number_grant_certificate ?? '' }}" class="form-control" name="nobdUser[number_grant_certificate]" type="text" />
                        </div>
                    </div>

                    <div class="col-md-12 form-group">
                        <div class="col-md-3">
                            <label class="pull-right text-right"> Обучается по квоте </label>
                        </div>
                        <div class="col-md-3">
                            <select v-model="nobdData.trained_quota" class="form-control" name="nobdUser[trained_quota]">
                                <option> ... </option>
                                @if( !empty($trainedQuota) )
                                    @foreach( $trainedQuota as $itemTQ )
                                        <option value="{{ $itemTQ->id }}"> {{ $itemTQ->name }} </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12 form-group">
                        <div class="col-md-3">
                            <label class="pull-right text-right"> Оставлен на повторный курс </label>
                        </div>
                        <div class="col-md-3">
                            <select v-model="nobdData.cause_stay_year" class="form-control" name="nobdUser[cause_stay_year]">
                                <option> ... </option>
                                @if( !empty($causeStayYear) )
                                    @foreach( $causeStayYear as $itemCSY )
                                        <option value="{{ $itemCSY->id }}"> {{ $itemCSY->name }} </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>


                    <div class="clearfix padding-20">&nbsp;</div>


                    <div class="col-md-12 form-group">
                        <div class="col-md-3">
                            <label class="pull-right text-right">  </label>
                        </div>
                        <div class="col-md-3"></div>
                    </div>



                    <div class="form-group margin-15">
                        <label class="col-md-3 control-label"> Участие в соревнованиях, конкурсах и олимпиадах </label>
                        <div class="col-md-5">
                            <div id="nobd_user_pc">
                                <div class="col-md-12 subform">

                                    @if( !empty($student->nobdUser->pc) )
                                        @foreach( $student->nobdUser->pc as $keyPc => $ItemPc)
                                            <div class="panel panel-default">
                                                <div class="panel-body">
                                                    <div class="col-md-12 form-group">
                                                        <div class="col-md-6">
                                                            <label class="pull-right text-right"> Вид мероприятия </label>
                                                        </div>
                                                        <input type="hidden" value="{{$ItemPc->id}}" name="nobdUser[nobdUserPc][{{ $ItemPc->id }}][id]" />
                                                        <div class="col-md-6">
                                                            <select v-model="nobdData.pc['{{ $keyPc }}'].type_event" class="form-control" name="nobdUser[nobdUserPc][{{ $ItemPc->id }}][type_event]">
                                                                <option> ... </option>
                                                                @if( !empty($typeEvent) )
                                                                    @foreach( $typeEvent as $itemTE )
                                                                        <option value="{{ $itemTE->id }}" @if($ItemPc->type_event == $itemTE->id) selected @endif> {{ $itemTE->name }} </option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div v-if="nobdData.pc['{{ $keyPc }}'].type_event != 7" class="col-md-12 form-group">
                                                        <div class="col-md-6">
                                                            <label class="pull-right text-right"> Вид направления </label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <select class="form-control" name="nobdUser[nobdUserPc][{{ $ItemPc->id }}][type_direction]">
                                                                <option> ... </option>
                                                                @if( !empty($typeDirection) )
                                                                    @foreach( $typeDirection as $itemTD )
                                                                        <option value="{{ $itemTD->id }}" @if($ItemPc->type_direction == $itemTD->id) selected @endif> {{ $itemTD->name }} </option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div v-if="nobdData.pc['{{ $keyPc }}'].type_event != 7" class="col-md-12 form-group">
                                                        <div class="col-md-6">
                                                            <label class="pull-right text-right"> Уровень мероприятия </label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <select class="form-control" name="nobdUser[nobdUserPc][{{ $ItemPc->id }}][events]">
                                                                <option> ... </option>
                                                                @if( !empty($events) )
                                                                    @foreach( $events as $itemE )
                                                                        <option value="{{ $itemE->id }}" @if($ItemPc->events == $itemE->id) selected @endif> {{ $itemE->name }} </option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div v-if="nobdData.pc['{{ $keyPc }}'].type_event != 7" class="col-md-12 form-group">
                                                        <div class="col-md-6">
                                                            <label class="pull-right text-right"> Дата участия </label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input value="{{ $ItemPc->date_participation ?? null }}" class="form-control" name="nobdUser[nobdUserPc][{{ $ItemPc->id }}][date_participation]" type="date" />
                                                        </div>
                                                    </div>
                                                    <div v-if="nobdData.pc['{{ $keyPc }}'].type_event != 7" class="col-md-12 form-group">
                                                        <div class="col-md-6">
                                                            <label class="pull-right text-right"> Награда </label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <select class="form-control" name="nobdUser[nobdUserPc][{{ $ItemPc->id }}][reward]">
                                                                <option> ... </option>
                                                                @if( !empty($reward) )
                                                                    @foreach( $reward as $itemR )
                                                                        <option value="{{ $itemR->id }}" @if($ItemPc->reward == $itemR->id) selected @endif> {{ $itemR->name }} </option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="pull-right" onclick="deleteNobdUserPc(this,'{{$ItemPc->id}}')"><a class="btn btn-default"><i class="fa fa-remove"></i></a></div><br><br>
                                        @endforeach
                                    @endif

                                    <div class="col-md-12 margin-b10">
                                        <a @click="renderUserPca" class="btn btn-default"> Добавить <i class="fa fa-plus"></i></a>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>



                    <div class="clearfix padding-20">&nbsp;</div>


                    <div class="col-md-12 form-check">
                        <div class="col-md-3"></div>
                        <div class="col-md-4">
                            <input onchange="changeChecked('is_orphan')" class="form-check-input" type="checkbox" id="is_orphan" name="nobdUser[is_orphan]" value="{{ $student->nobdUser->is_orphan ?? 1 }}" @if(!empty($student->nobdUser->is_orphan) && ($student->nobdUser->is_orphan != 0)) checked="checked" @endif />
                            <label class="form-check-label" for="is_orphan">
                                Сирота
                            </label>
                        </div>
                    </div>

                    <div class="col-md-12 form-check">
                        <div class="col-md-3"></div>
                        <div class="col-md-4">
                            <input onchange="changeChecked('is_child_without_parents')" class="form-check-input" type="checkbox" id="is_child_without_parents" name="nobdUser[is_child_without_parents]" value="{{ $student->nobdUser->is_child_without_parents ?? 1 }}" @if(!empty($student->nobdUser->is_child_without_parents) && ($student->nobdUser->is_child_without_parents != 0)) checked="checked" @endif />
                            <label class="form-check-label" for="is_child_without_parents">
                                Ребенок, оставшийся без попечения родителей
                            </label>
                        </div>
                    </div>

                    <div class="col-md-12 form-check">
                        <div class="col-md-3"></div>
                        <div class="col-md-4">
                            <input onchange="changeChecked('is_invalid')" class="form-check-input" type="checkbox" id="is_invalid" name="nobdUser[is_invalid]" value="{{ $student->nobdUser->is_invalid ?? 1 }}" @if(!empty($student->nobdUser->is_invalid) && ($student->nobdUser->is_invalid != 0)) checked="checked" @endif />
                            <label class="form-check-label" for="is_invalid">
                                Инвалид
                            </label>
                        </div>
                    </div>


                    <div class="clearfix padding-10">&nbsp;</div>


                    <div class="col-md-12 form-group">
                        <div class="col-md-3">
                            <label class="pull-right text-right"> Группа инвалидности </label>
                        </div>
                        <div class="col-md-3">
                            <select v-model="nobdData.disability_group" class="form-control" name="nobdUser[disability_group]">
                                <option> ... </option>
                                @if( !empty($disabilityGroup) )
                                    @foreach( $disabilityGroup as $itemDG )
                                        <option value="{{ $itemDG->id }}"> {{ $itemDG->name }} </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12 form-group">
                        <div class="col-md-3">
                            <label class="pull-right text-right"> Виды нарушений </label>
                        </div>
                        <div class="col-md-3">
                            <select v-model="nobdData.type_violation" class="form-control" name="nobdUser[type_violation]">
                                <option> ... </option>
                                @if( !empty($typeViolation) )
                                    @foreach( $typeViolation as $itemTV )
                                        <option value="{{ $itemTV->id }}"> {{ $itemTV->name }} </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12 form-group">
                        <div class="col-md-3">
                            <label class="pull-right text-right"> Заключение ПМПК (до 18 лет)/ВКК (старше 18 лет) </label>
                        </div>
                        <div class="col-md-3">
                            <input v-model="nobdData.conclusion_pmpc" value="{{ $student->nobdUser->conclusion_pmpc ?? '' }}" class="form-control" name="nobdUser[conclusion_pmpc]" type="text" />
                        </div>
                    </div>

                    <div class="col-md-12 form-group">
                        <div class="col-md-3">
                            <label class="pull-right text-right"> Дата заключения </label>
                        </div>
                        <div class="col-md-3">
                            <input v-model="nobdData.conclusion_date" value="{{ $student->nobdUser->conclusion_date ?? null }}" class="form-control" name="nobdUser[conclusion_date]" type="date" />
                        </div>
                    </div>

                    <div class="clearfix padding-10">&nbsp;</div>

                    <div class="col-md-12 form-check">
                        <div class="col-md-3"></div>
                        <div class="col-md-4">
                            <input onchange="changeChecked('is_thesis_defense')" class="form-check-input" type="checkbox" id="is_thesis_defense" name="nobdUser[is_thesis_defense]" value="{{ $student->nobdUser->is_thesis_defense ?? 1 }}" @if(!empty($student->nobdUser->is_thesis_defense) && ($student->nobdUser->is_thesis_defense != 0)) checked="checked" @endif />
                            <label class="form-check-label" for="is_thesis_defense">
                                С защитой диссертации
                            </label>
                        </div>
                    </div>

                    <div class="clearfix padding-10">&nbsp;</div>

                    <div class="col-md-12 form-group">
                        <div class="col-md-3">
                            <label class="pull-right text-right"> Вид диплома </label>
                        </div>
                        <div class="col-md-3">
                            <select v-model="nobdData.form_diplom" class="form-control" name="nobdUser[form_diplom]">
                                <option> ... </option>
                                @if( !empty($formDiplom) )
                                    @foreach( $formDiplom as $itemFD )
                                        <option value="{{ $itemFD->id }}"> {{ $itemFD->name }} </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12 form-group">
                        <div class="col-md-3">
                            <label class="pull-right text-right"> Серия диплома </label>
                        </div>
                        <div class="col-md-3">
                            <input v-model="nobdData.diplom_series" value="{{ $student->nobdUser->diplom_series ?? '' }}" class="form-control" name="nobdUser[diplom_series]" type="text" />
                        </div>
                    </div>

                    <div class="col-md-12 form-group">
                        <div class="col-md-3">
                            <label class="pull-right text-right"> № диплома </label>
                        </div>
                        <div class="col-md-3">
                            <input v-model="nobdData.diplom_number" value="{{ $student->nobdUser->diplom_number ?? '' }}" class="form-control" name="nobdUser[diplom_number]" type="text" />
                        </div>
                    </div>

                    <div class="col-md-12 form-group">
                        <div class="col-md-3">
                            <label class="pull-right text-right"> Дата выбытия </label>
                        </div>
                        <div class="col-md-3">
                            <input v-model="nobdData.date_disposal" value="{{ $student->nobdUser->date_disposal ?? null }}" class="form-control" name="nobdUser[date_disposal]" type="date" />
                        </div>
                    </div>

                    <div class="col-md-12 form-group">
                        <div class="col-md-3">
                            <label class="pull-right text-right"> Номер приказа выбытия </label>
                        </div>
                        <div class="col-md-3">
                            <input v-model="nobdData.number_disposal_order" value="{{ $student->nobdUser->number_disposal_order ?? '' }}" class="form-control" name="nobdUser[number_disposal_order]" type="text" />
                        </div>
                    </div>

                    <div class="col-md-12 form-group">
                        <div class="col-md-3">
                            <label class="pull-right text-right"> Причина выбытия </label>
                        </div>
                        <div class="col-md-3">
                            <select v-model="nobdData.reason_disposal" class="form-control" name="nobdUser[reason_disposal]">
                                <option> ... </option>
                                @if( !empty($reasonDisposal) )
                                    @foreach( $reasonDisposal as $itemRD )
                                        <option value="{{ $itemRD->id }}"> {{ $itemRD->name }} </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12 form-group">
                        <div class="col-md-3">
                            <label class="pull-right text-right"> Трудоустройство </label>
                        </div>
                        <div class="col-md-3">
                            <select v-model="nobdData.employment_opportunity" class="form-control" name="nobdUser[employment_opportunity]">
                                <option> ... </option>
                                @if( !empty($employmentOpportunity) )
                                    @foreach( $employmentOpportunity as $itemEO )
                                        <option value="{{ $itemEO->id }}"> {{ $itemEO->name }} </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>


                </div>
            </div>
        </div>


        <input type="hidden" name="check_level" value="{{ $student->studentProfile->check_level }}" />
        <input type="hidden" name="send_to" value="false" />

        @if($hasRightEdit)
            @if(($student->bcApplication || $student->mgApplication))
                <div class="col-md-12" id="bottomButtons">
                    <button type="submit" class="btn btn-primary">Сохранить изменения</button>

                    @if($student->studentProfile->check_level == \App\Profiles::CHECK_LEVEL_INSPECTION)
                    <a class="btn btn-primary" v-on:click="changeCheckLevel()" >Отправить в ОР</a>
                    @endif

                    @if($student->studentProfile->check_level == \App\Profiles::CHECK_LEVEL_OR_CABINET)
                    <a class="btn btn-primary" v-on:click="changeCheckLevel()">Отправить в приемку</a>
                    <a class="btn btn-primary" onclick="showOrderModal()">Добавить в приказ</a>
                    @endif

                </div><br><br>
            @endif
        @endif


        {!! Form::close() !!}
    </div>

    <div class="modal" tabindex="-1" role="dialog" aria-labelledby="" id="orderModal">
        <div class="modal-dialog modal-lg " style="min-width:950px;" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" onclick="hideOrderModal()"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Добавление в приказ</h4>
                </div>
                <div class="modal-body col-sm-12" style="overflow-y: auto;max-height: 75vh;">
                    <select @if(!$hasRightEdit) disabled @endif id="order_id" class="form-control attach_to_order_elemnent">
                        @foreach($orderList as $order)
                            <option value="{{ $order->id }}">{{ $order->orderName->name}} ({{ $order->number }})</option>
                        @endforeach
                    </select>
                    <div class="alert alert-success order_attach_element_success hide">
                        Студенты добавлены в приказ.
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary attach_to_order_elemnent" onclick="attachToOrder()">Добавить студентов в приказ</button>
                    <div class="order_attach_element_success hide">
                        <a class="btn btn-primary" id="open_order_button" onclick="redirectToOrder()">Открыть приказ</a>
                        <a class="btn btn-primary" onclick="hideOrderModal()">Закрыть окно</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" tabindex="-1" role="dialog" aria-labelledby="" id="notificationModal">
        <div class="modal-dialog modal-lg " style="min-width:950px;" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" onclick="hideNotificationModal()"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Отправка уведомления</h4>
                </div>
                <div class="modal-body col-sm-12" style="overflow-y: auto;max-height: 75vh;">
                    <div class="col-md-12">
                        <select @if(!$hasRightEdit) disabled @endif id="notification_template" class="form-control" onchange="changeTemplate()">
                            <option value="0">Выберите шаблон</option>
                            @foreach($notificationTemplates as $template)
                                <option value="{{ $template->id }}">{{ $template->name }}</option>
                            @endforeach
                        </select>
                    </div> <br><br>
                    <div class="col-md-12">
                        <textarea id="notificationText"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" onclick="sendNotification()">Отправить</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script type="text/javascript">

        function getExtension(filename) {
            var parts = filename.split('.');
            return parts[parts.length - 1];
        }

        function isImage(filename) {
            var ext = this.getExtension(filename);
            switch (ext.toLowerCase()) {
                case 'jpg':
                case 'jpeg':
                case 'png':
                case 'gif':
                case 'webp':
                    return true;
            }
            return false;
        }

        Vue.component('view-text', {
            props: {
                label: ''
            },
            template: `
                        <div class="col-md-12 form-group">
                            <div class="col-md-2 "><label class="pull-right text-right">@{{label}}</label></div>
                            <div class="col-md-10"><slot></slot></div>
                        </div>
            `
        });

        Vue.component('view-block', {
            props: {
                label: ''
            },
            template: `
                        <div class="col-md-12 form-group">
                            <div class="col-md-2 "><label class="pull-right text-right">@{{label}}</label></div>
                            <div class="col-md-10"><slot></slot></div>
                        </div>
            `
        });

        Vue.component('view-date', {
            props: {
                label: ''
            },
            computed: {
                formatDate: function() {
                    var date = new Date(this.$slots.default[0].text);
                    return date.toLocaleDateString();
                }
            },
            template: `
                        <div class="col-md-12 form-group">
                            <div class="col-md-2 "><label class="pull-right text-right">@{{label}}</label></div>
                            <div class="col-md-10">@{{formatDate}}</div>
                        </div>
            `
        });

        Vue.component('view-doc-item', {
            props: [
                'label',
                'status',
                'statustext',
                'delivered'
            ],
            data: function () {
                return {
                    statusInner: this.status,
                    deliveredInner: this.delivered
                };
            },
            computed: {
                alertClass: function() {
                    if(this.statusInner == 'moderation')
                        return 'alert-warning';
                    if(this.statusInner == 'allow')
                        return 'alert-success';
                    if(this.statusInner == 'disallow')
                        return 'alert-danger';

                    return '';
                }
            },
            template: `
                        <div class="col-md-12 no-padding">
                            <div class="col-md-6"><label class="pull-right text-right">@{{label}}</label></div>
                            <div class="col-md-6">
                                <div class="col-md-8 no-padding " v-bind:class="alertClass">
                                    <span v-if="!status">документ не предоставлен</span>
                                    <div class="col-md-12" v-if="status">
                                        @{{statustext}} и @{{deliveredInner}}
                                    </div>
                                </div>
                            </div>
                        </div>
            `
        });
{{--
        Vue.component('view-doc', {
            props: [
                'label',
                'name',
                'status',
                'fileName',
                'delivered',
                'deliveredname'
            ],
            data: function () {
                return {
                    statusInner: this.status,
                    deliveredInner: this.delivered
                };
            },
            computed: {
                alertClass: function() {
                    if(this.statusInner == 'moderation')
                        return 'alert-warning';
                    if(this.statusInner == 'allow')
                        return 'alert-success';
                    if(this.statusInner == 'disallow')
                        return 'alert-danger';

                    return '';
                }
            },
            template: `
                        <div class="col-md-12 form-group">
                            <div class="col-md-2 "><label class="pull-right text-right">@{{label}}</label></div>
                            <div class="col-md-10">
                                <div class="col-md-4 no-padding alert " v-bind:class="alertClass">
                                    <span v-if="!fileName">документ не предоставлен</span>
                                    <div class="col-md-12" v-if="fileName">
                                        <div class="col-md-4 no-padding">
                                            <a target="_blank" v-bind:href="fileName + '-b.jpg'">
                                            <img v-bind:src="fileName + '-b.jpg'" class="doc-preview" />
                                            </a>
                                        </div>
                                        <div class="col-md-8 no-padding">
                                            <select @if(!$hasRightEdit) disabled @endif v-model="statusInner" class="form-control" v-bind:name="name">
                                                <option value="moderation">Не проверено</option>
                                                <option value="allow">Принято</option>
                                                <option value="disallow">Отклонено</option>
                                            </select>
                                            <div class="col-md-12 no-padding" style="margin-top: 10px;">
                                                <input type="checkbox" @if(!$hasRightEdit) disabled @endif v-model="deliveredInner" value="1" v-bind:name="deliveredname" />

                                                <span>Доставлен</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
            `
        });
--}}
        var app = new Vue({
            el: '#main-vue',
            mounted: function(){
                this.loader = true;
                this.getUserDocsList();
            },
            methods: {
                docUpdateStatus: function(index){
                    axios.post('{{ route('adminDocsSetStatus', ['id' => $student->id]) }}', {
                        status: app.docsStatus[index],
                        delivery: app.docsDelivered[index],
                        docId: app.docsId[index]
                        })
                        .then(function(response){
                            //console.log(index);
                            //if (response.data.status === 'success') {
                                //app.docsBtnSuccess[index] = true;
                                //setInterval(function(){
                                //    app.docsBtnSuccess[index] = false;        
                                //}, 2000);
                                app.getUserDocsList();
                            //}

                            //setInterval(function(){
                            //     this.changeColor = !this.changeColor;         
                            //}.bind(this), 2000);

                        }).catch(function(error){
                    });

                },
                docTypeSelected: function(elemId){
                    //var file = $('#' + elemId);
                    //file[0].file = null;
                    app.$refs.docFiles.value = '';
                },
                checkImageValid: function(event, elemId){
                    app.docsMessage = null;
                    var file = $('#' + elemId);

                    if(!file.val()) {
                        return false;
                    }

                    if(!isImage(file.val())) {
                        app.docsHasError = true;
                        app.docsMessage = "{{__("Invalid image format. File type require jpg, png, gif or webp")}}";
                        app.$refs.docFiles.value = '';
                        return false;
                    }

                    if (event.target.files.length > 10) {
                        app.docsHasError = true;
                        app.docsMessage = "{{__("You can upload up tp 10 files")}}";
                        app.$refs.docFiles.value = '';
                    }
                    
                    app.docsFormData = new FormData();

                    Array.from(Array(event.target.files.length).keys())
                      .map(x => {
                        //app.formData.append(event.target.files[x].name, event.target.files[x]);
                        app.docsFormData.append('files[]', event.target.files[x]);
                    });

                    return true;
                },
                uploadDocFiles: function(){
                    app.docsLoader = true;
                    app.docsFormData.append('doc_type', app.docFilesType);
                    app.docsFormData.append('type', '{{(isset($student->bcApplication))?'bc':'mg'}}');

                    axios.post('{{ route('adminDocsUploadPost', ['id' => $student->id])}}', app.docsFormData, {
                        headers: {
                          'Content-Type': 'multipart/form-data'
                        }
                    })
                        .then(function(response){
                            if(response.data.status === 'success') {
                                app.docsHasError = false;
                                app.docsMessage = "{{__("Your files has beed uploaded")}}";
                                app.getUserDocsList();
                            }
                            app.$refs.docFiles.value = '';
                            app.docsLoader = false;
                        }).catch(function(error){
                        app.docsHasError = true;
                        app.docsMessage = "{{__("File upload error")}}";
                        app.$refs.docFiles.value = '';
                        app.loader = false;
                    });

                },
                getUserDocsList: function() {
                    axios.get('{{ route('adminGetUserDocsList', ['id' => $student->id])}}')
                        .then(function(response){
                            app.uploadedDocsList = response.data;
                            app.docLoader = false;
                            app.docsStatus = [];
                            app.docsDelivered = [];
                            app.docsStatusOriginal = [];
                            app.docsDeliveredOriginal = [];
                            app.docsId = [];
                            response.data.map(function(value, key) {
                              app.docsStatus.push(value.statusval);
                              app.docsDelivered.push(value.delivered);
                              app.docsStatusOriginal.push(value.statusval);
                              app.docsDeliveredOriginal.push(value.delivered);
                              app.docsId.push(value.id);
                            });

                        }).catch(function(error){
                    });

                },

                changeIgnoreDebt: function()
                {
                    axios.post('{{route('adminStudentChangeIgnoreDebt', ['id' => $student->id])}}', {
                        ignore_debt: this.ignoreDebt
                    })
                        .then(function(response){
                            alert('Изменения сохранены.')
                        });
                },

                changeCheckLevel: function() {
                    @if($student->studentProfile->check_level == \App\Profiles::CHECK_LEVEL_OR_CABINET)
                        if(!confirm('Отправить в приемку?')){
                            return;
                        }

                        $('[name=check_level]').val('{{ \App\Profiles::CHECK_LEVEL_INSPECTION }}');
                    @endif

                    @if($student->studentProfile->check_level == \App\Profiles::CHECK_LEVEL_INSPECTION)
                        if(!confirm('Отправить в кабинет ОР?')){
                            return;
                        }

                        $('[name=check_level]').val('{{ \App\Profiles::CHECK_LEVEL_OR_CABINET }}');
                    @endif

                    $('[name=send_to]').val('true');
                    $('#student_form').submit();
                },

                addComment: function() {

                    if(this.comment == '') {
                        return;
                    }

                    var self = this;

                    axios.post('{{route('adminStudentAddComment')}}', {
                        user_id: {{ $student->id }},
                        text: this.comment
                    })
                        .then(function(response){
                            self.comments.push({
                                author: '{{ \App\Services\Auth::user()->name }}',
                                date: '',
                                text: self.comment,
                                check_level: '{{ $student->studentProfile->check_level }}'
                            });

                            self.comment = '';
                        });
                },
                refreshEnt: function() {
                    var self = this;
                    this.refreshEntProcess = true;
                    axios.post('{{route('adminStudentRefreshEnt', ['id' => $student->id])}}', {
                        ikt: this.ikt
                    })
                        .then(function(response){
                            self.refreshEntData = response.data.userBallList;
                            self.refreshEntProcess = false;
                        });
                },
                changeEnt: function() {
                    var self = this;
                    if( this.changeEntFlag ){

                        this.changeEntFlag = false;
                        this.isChangeEnt = 0;
                    } else {

                        this.changeEntFlag = true;
                        this.isChangeEnt = 1;
                    }
                },
                changeKt: function() {
                    var self = this;
                    if( this.changeKtFlag ){

                        this.changeKtFlag = false;
                        this.isChangeKt = 0;
                    } else {

                        this.changeKtFlag = true;
                        this.isChangeKt = 1;
                    }
                },
                refreshBalance: function(){

                    this.userBalance = false;
                    this.isRefreshBalance = true;

                    var self = this;
                    axios.post('{{route('StudentAjaxGetUserBalance')}}',{
                        "_token": "{{ csrf_token() }}",
                        "id": "{{ $student->id }}"
                    })
                    .then(function( response ){

                        if( response.data.status ){

                            self.userBalance = response.data.data.balance;
                        }
                        self.isRefreshBalance = false;
                    });

                },
                auditAddtHistory: function(){

                    this.auditSendRequest = true;
                    this.auditIsError = false;
                    this.auditMessage = false;

                    if( !this.auditDateFrom || ( this.auditDateFrom == '' ) ){

                        this.auditSendRequest = false;
                        this.auditIsError = true;
                        this.auditMessage = '{{ __('Error, date from required') }}';
                        return;
                    }
                    if( !this.auditDateTo || ( this.auditDateTo == '' ) ) {

                        this.auditSendRequest = false;
                        this.auditIsError = true;
                        this.auditMessage = '{{ __('Error, date to required') }}';
                        return;
                    }

                    var self = this;
                    axios.post('{{ route('profileAjaxAddTransactionHistory') }}',{
                        "_token": "{{ csrf_token() }}",
                        "iin": '{{ $student->studentProfile->iin }}',
                        "date_from": self.auditDateFrom,
                        "date_to": self.auditDateTo
                    })
                        .then(function(response){

                            if( response.data.status ){

                                self.auditMessage = response.data.message;

                            } else {

                                self.auditIsError = true;
                                self.auditMessage = '{{ __('Request error') }}';
                            }

                            self.auditSendRequest = false;

                        });

                },
                getNobdUserData: function(){

                    var self = this;
                    axios.post('{{ route('adminNobddataGetDataByUserId') }}',{
                        "_token": "{{ csrf_token() }}",
                        "user": '{{ $student->studentProfile->user_id }}'
                    })
                    .then(function(response){

                        if( response.data.status ){

                            self.nobdData = response.data.model;
                        }

                    });
                },
                renderUserPca: function(){

                    this.count++;

                    var self = this;
                    axios.post('{{ route('adminNobddataRenderNobdUserPc') }}',{
                        "_token": "{{ csrf_token() }}",
                        "count": this.count
                    })
                    .then(function(response){

                        $('#nobd_user_pc').append(response.data);
                    });
                }


                /*deleteResult: function(id)
                {
                    if(!confirm('Удалить результат?'))
                    {
                        return;
                    }

                    axios.post('{{route('adminStudentDeleteResult', ['id' => $student->id])}}', {
                        discipline_id: id
                    })
                    .then(function(response){
                        $('#test-result-' + id).html('');
                    });
                }*/
            },
            data: {
                ignoreDebt: {{ $student->studentProfile->ignore_debt ? 'true' : 'false' }},
                docsTypeStatus: '{{ $student->studentProfile->docs_status ?? 'new' }}',
                currentCategory: '{{$student->studentProfile->category}}',
                comment: '',
                comments: [],
                checkLevel: '{{ $student->studentProfile->check_level }}',
                sendTo: false,
                ikt: '{{ $student->bcApplication->ikt ?? '' }}',
                refreshEntData: null,
                refreshEntProcess:false,
                changeEntFlag: false,
                isChangeEnt: null,
                changeKtFlag: false,
                isChangeKt: null,
                isResident: null,
                userBalance: false,
                isRefreshBalance: false,

                auditDateFrom: '',
                auditDateTo: '',
                auditIsError: false,
                auditMessage: false,
                auditSendRequest: false,

                docFilesType: null,
                docsMessage: null,
                docsHasError: false,
                docsFormData: null,
                docsloader: false,
                uploadedDocsList: null,
                docsStatus: [],
                docsDelivered: [],
                docsStatusOriginal: [],
                docsDeliveredOriginal: [],
                docsId: [],
                docsBtnSuccess: [],
                sector: null,
                sectorManager: null,


                nobdData: {},
                count: 0


            },
            watch: {
                ignoreDebt: function(){
                    this.changeIgnoreDebt();
                }
            },
            created: function() {
                //$('#data-table-disciplines').dataTable();

                this.count = 0;

                @foreach($student->adminComments as $comment)
                this.comments.push({
                    author: '{{ $comment->author->name ?? 'автор удален'}}',
                    date: '{{ date('d.m.Y', strtotime($comment->created_at)) }}',
                    text: '{{ str_replace("\n", ' ', $comment->text) }}',
                    check_level: '{{ $comment->user->studentProfile->check_level }}'
                });
                @endforeach

                this.isResident = '{{ !empty($student->studentProfile->alien) ? 1 : 0 }}' * 1;
                this.refreshBalance();

                this.getNobdUserData();


            },
            computed: {
                entBallTotal: function(){
                    var total = 0;

                    for(var i=0; i<this.refreshEntData.length; i++) {
                        total = total + this.refreshEntData[i].ball;
                    }

                    return total;
                }
            }
        });

        function changeAdmissionYear() {
            @if(!isset($student->studentProfile->speciality))
            var year = $("[name=admission_year]").val();
            $("[name=education_speciality_id]").html('');

            axios.post('{{route('adminSpecialityListByYear')}}', {
                year: year
            })
                .then(function(response){

                    var data = response.data;

                    for(var i=0; i<data.length; i++)
                    {
                        $("[name=education_speciality_id]").append("<option value='" + data[i].id + "'>" + data[i].name + "</option>");
                    }
                });
            @endif
        }

        function deleteResult(id){
            if(!confirm('Удалить результат?'))
            {
                return;
            }

            axios.post('{{route('adminStudentDeleteResult', ['id' => $student->id])}}', {
                discipline_id: id
            })
            .then(function(response){
                $('#test-result-' + id).html('');
            });
        }

        function deleteResultKge(){
            if(!confirm('Удалить результат экзамена?'))
            {
                return;
            }
            axios.post('{{route('adminStudentDeleteResultKge', ['id' => $student->id])}}', {})
                .then(function(response){
                    $('#govExamData').html('Нет результатов.');
                });
        }

        @if($student->studentProfile->check_level == \App\Profiles::CHECK_LEVEL_OR_CABINET)

        function hideOrderModal()
        {
            $('#orderModal').removeClass('show');
            $('.attach_to_order_elemnent').removeClass('hide');
            $('.order_attach_element_success').addClass('hide');
        }

        function attachToOrder()
        {
            var data = {
                users: [{{ $student->id }}],
                order_id: $('#order_id').val()
            }

            if(data.users.length == 0)
            {
                alert('Необходимо выбрать абитуриентов');
                return;
            }

            if(!data.order_id)
            {
                alert('Необходимо выбрать приказ');
                return;
            }

            $.ajax({
                url: '{{ route('adminOrderAttachUsers') }}',
                type: "POST",
                data: data,
                success: function(data){
                    $('#open_order_button').attr('href', '/orders/edit/' + $('#order_id').val())
                    $('.attach_to_order_elemnent').addClass('hide');
                    $('.order_attach_element_success').removeClass('hide');
                },
                dataType: 'json'
            });
        }

        function showOrderModal()
        {
            var userList = [{{ $student->id }}];

            if(userList.length == 0)
            {
                alert('Необходимо выбрать абитуриентов');
                return;
            }
            $('#orderModal').addClass('show');
        }

        @endif

        const STUDENT_ID = {{ $student->id }};

        $(document).ready(function() {
            $('.td-extendable-button').on('click', function(){
                $(this).parent().find('.after-extend').toggle('fast');
            });

            @if(in_array(\App\Services\Auth::user()->id, [8579, 96, 18365, 15546, 4876]) || env('APP_DEBUG'))
                $('.after-extend .add-discipline, .after-extend .add-pay-req-discipline').on('click', function() {
                    var disciplineId = $(this).attr('disciplineId');
                    var points = $(this).parent().find('input').val();
                    var ects = $(this).attr('ects')*1;
                    var creditsAllowedGot = $('.credits-allowed-got').html() * 1;
                    var creditsAllowed = $('.credits-allowed').html();
                    var creditsAllowedPayReqGot = $('.pay-req-credits-allowed-got').html() * 1;
                    var creditsAllowedPayReq = $('.pay-req-credits-allowed').html() * 1;
                    var testResults = $('#test-result-' + disciplineId).html();
                    var payReq = $(this).hasClass('add-pay-req-discipline');

                    if (creditsAllowed == "∞") {
                        creditsAllowed = 999999;
                    } else {
                        creditsAllowed = creditsAllowed * 1;
                    }

                    if (points == '') {
                        alert('Введите результат');
                        return;
                    }

                    if (!confirm('Засчитать дисциплину?')) {
                        return;
                    }

                    if (!payReq && creditsAllowedGot + ects > creditsAllowed) {
                        alert('Превышение максимального допустимого значения.');
                        return;
                    }

                    if (testResults != '') {
                        alert('Предмет имеет результаты тестов. Пожалуйста, сначала удалите результаты.');
                        return;
                    }

                    if (points > 100) {
                        alert('Максимальное значение 100.');
                        return;
                    }

                    axios.post('{{route('adminStudentDisciplineMigration')}}', {
                        disciplineId: disciplineId,
                        studentId: STUDENT_ID,
                        points: points,
                        ects: ects,
                        payReq: payReq
                    })
                    .then(function(response) {
                        if (response.data.success) {
                            $('#test-result-' + disciplineId).html(response.data.value + ' (' + response.data.letter + ')');
                            if (payReq) {
                                $('.pay-req-credits-allowed-got').html(creditsAllowedPayReqGot   + ects);
                            } else {
                                $('.credits-allowed-got').html(creditsAllowedGot + ects);
                            }

                        }
                    });
                });

                $('.after-extend .add-submodule-discipline, .after-extend .add-pay-req-submodule-discipline').on('click', function() {
                    var submoduleId = $(this).attr('submoduleId');
                    var languageLevel = $(this).parent().find('select').val();
                    var points = $(this).parent().find('input').val();
                    var ects = $(this).attr('ects') * 1;
                    var payReq = $(this).hasClass('add-pay-req-submodule-discipline');

                    if (points == '') {
                        alert('Введите результат');
                        return;
                    }

                    if (points > 100) {
                        alert('Максимальное значения 100');
                        return;
                    }

                    if (!confirm('Засчитать дисциплину?')) {
                        return;
                    }

                    $('.after-extend .add-submodule-discipline, .after-extend .add-pay-req-submodule-discipline').prop('disabled', 'disabled');

                    axios.post('{{route('adminStudentSubmoduleDisciplineMigration')}}', {
                        submoduleId: submoduleId,
                        userId: STUDENT_ID,
                        languageLevel: languageLevel,
                        points: points,
                        ects: ects,
                        payReq: payReq
                    })
                        .then(function(response){
                            if(response.data.success) {
                                window.location.reload();
                            } else {
                                alert(response.data.error);
                            }
                        });
                });
            @endif

            $('.change-free-credits').on('click', function() {
                let $btn = $(this);
                let disciplineId = $btn.data('discipline-id');
                let maxCredits = $btn.data('max-credits');
                let $freeCredits = $('#free-credits-' + disciplineId);
                let freeCredits = $freeCredits.val();
                let $spanFreeCredits = $('#span-free-credits-' + disciplineId);

                $btn.prop('disabled', true);
                $freeCredits.prop('disabled', true);

                if (freeCredits < 0) {
                    freeCredits = 0;
                    $freeCredits.val(0);
                }
                else if (freeCredits > maxCredits) {
                    freeCredits = maxCredits;
                    $freeCredits.val(maxCredits);
                }

                axios.post('{{route('adminStudentDisciplineSetFreeCredits')}}', {
                    disciplineId: disciplineId,
                    studentId: STUDENT_ID,
                    freeCredits: freeCredits
                })
                .then(function(response) {
                    if(response.data.success) {
                        $btn.prop('disabled', false);
                        $freeCredits.prop('disabled', false);
                        $spanFreeCredits.text(freeCredits);
                    }
                });
            });

            var dataTableHistory = $('#data-table-history').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax":{
                    url :"{{ route('profileAjaxGetTransactionHistory') }}",
                    data:{
                        "_token": "{{ csrf_token() }}",
                        "iin": '{{ $student->studentProfile->iin }}'
                    },
                    type: "post",
                    error: function(){
                        $(".employee-grid-error").html("");
                        $("#employee-grid").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                        $("#employee-grid_processing").css("display","none");

                    }
                }
            });

        });

        

        $(document).ready(function() {
            $('#data-table-dispciplines').DataTable();
            $('#data-table-submodules').DataTable();
            $('#notificationText').summernote({
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                ],
                height: {
                    type: String,
                    default: '150'
                }
            });
            $('#notificationText').on('summernote.change', function(e) {
                $('#notificationText').val($('#notificationText').summernote().code());
            });

            $('#notificationText').on('summernote.blur', function(e) {
                $('#notificationText').val($('#notificationText').summernote().code());
            });
        } );

        var notificationTemplates = [];

        @foreach($notificationTemplates as $template)
            notificationTemplates.push({
                id: {{ $template->id }},
                text: `{!! $template->text !!}`
            });

        @endforeach

        function changeTemplate()
        {
            templateId = $('#notification_template').val();
            templateActive = null;

            for (var i=0; i<notificationTemplates.length; i++)
            {
                if(notificationTemplates[i].id == templateId) {
                    templateActive = notificationTemplates[i];
                }
            }

            $('#notificationText').val(templateActive.text);
            $('#notificationText').code(templateActive.text);
        }

        function showNotificationModal()
        {
            $('#notificationModal').addClass('show');
        }

        function hideNotificationModal()
        {
            $('#notificationModal').removeClass('show');
        }

        function sendNotification()
        {
            var data = {
                users: [{{ $student->id }}],
                text: $('#notificationText').val()
            };

            if(data.text == '')
            {
                alert('Необходимо ввести сообщение');
                return;
            }

            $.ajax({
                url: '{{ route('adminSectionSendNotification') }}',
                type: "POST",
                data: data,
                success: function(data){
                    alert('Уведомление отправлено.');
                    hideNotificationModal();
                    location.reload();
                },
                dataType: 'json'
            });
        }

        function deleteNotification(id)
        {
            if(!confirm('Удалить уведомление?'))
            {
                return false;
            }

            $.ajax({
                url: '{{ route('adminSectionNotificationDelete') }}',
                type: "POST",
                data: {id: id},
                success: function(data){
                    location.reload();
                },
                dataType: 'json'
            });
        }

        function changeChecked(id) {
            var val = $('#'+id).val();
            if( val == 1 ){
                $('#'+id).prop('checked',false);
                $('#'+id).prop('value',0);
            } else {
                $('#'+id).prop('checked',true);
                $('#'+id).prop('value',1);
            }
        }

        function deleteNobdUserPc(elem,id){
            $(elem).prev().remove();
            $(elem).after('<input type="hidden" value="'+id+'" name="removeNobdUserPC[]">');
            $(elem).remove();
        }


    </script>
@endsection