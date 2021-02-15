<div class="col-md-12 form-group">
    <div class="col-md-2 ">
        <label class="pull-right text-right">ФИО</label>
    </div>
    <div class="col-md-10">
        <input @if(!$hasRightEdit) disabled @endif type="text" class="form-control" name="fio" value="{{ $student->studentProfile->fio }}" autocomplete="false" />
    </div>
</div>

<div class="col-md-12 form-group">
    <div class="col-md-2">
        <label class="pull-right text-right">Дата рождения</label>
    </div>
    <div class="col-md-3">
        <input @if(!$hasRightEdit) disabled @endif type="date" class="form-control" name="bdate" value="{{ date('Y-m-d', strtotime($student->studentProfile->bdate)) }}" autocomplete="false" />
    </div>
</div>

<div class="col-md-12 form-group">
    <div class="col-md-2">
        <label class="pull-right text-right">ИИН</label>
    </div>
    <div class="col-md-3">
        <input @if(!$hasRightEdit) disabled @endif type="text" class="form-control" name="iin" value="{{ $student->studentProfile->iin }}" autocomplete="false" />
    </div>
</div>

<div class="col-md-12 form-group">
    <div class="col-md-2 ">
        <label class="pull-right text-right">Телефон</label>
    </div>
    <div class="col-md-3">
        <input @if(!$hasRightEdit) disabled @endif type="text" class="form-control" name="mobile" value="{{ $student->studentProfile->mobile }}" autocomplete="false" />
    </div>
</div>


<view-block label="Документ">
    <div class="col-md-12 subform">

        <div class="col-md-12 form-group">
            <div class="col-md-2 ">
                <label class="pull-right text-right">Серия</label>
            </div>
            <div class="col-md-4">
                <input @if(!$hasRightEdit) disabled @endif type="text" class="form-control" name="docseries" value="{{ $student->studentProfile->docseries ?? '' }}" autocomplete="false" />
            </div>
        </div>

        <div class="col-md-12 form-group">
            <div class="col-md-2">
                <label class="pull-right text-right">Номер</label>
            </div>
            <div class="col-md-4">
                <input @if(!$hasRightEdit) disabled @endif type="text" class="form-control" name="docnumber" value="{{ $student->studentProfile->docnumber ?? '' }}" autocomplete="false" />
            </div>
        </div>

        <div class="col-md-12 form-group">
            <div class="col-md-2 ">
                <label class="pull-right text-right">Кем выдан</label>
            </div>
            <div class="col-md-10">
                <input @if(!$hasRightEdit) disabled @endif type="text" class="form-control" name="issuingData" value="{{ $student->studentProfile->issuing ?? '' }}" autocomplete="false" readonly onfocus="this.removeAttribute('readonly')" />
            </div>
        </div>

        <div class="col-md-12 form-group">
            <div class="col-md-2 ">
                <label class="pull-right text-right">Дата выдачи</label>
            </div>
            <div class="col-md-4">
                <input @if(!$hasRightEdit) disabled @endif type="date" class="form-control" name="issuedate" value="{{ $student->studentProfile->issuedate ? date('Y-m-d', strtotime($student->studentProfile->issuedate)) : date('Y-m-d') }}" autocomplete="false" />
            </div>
        </div>

        <div class="col-md-12 form-group">
            <div class="col-md-2 ">
                <label class="pull-right text-right">Срок действия</label>
            </div>
            <div class="col-md-4">
                <input @if(!$hasRightEdit) disabled @endif type="date" class="form-control" name="expire_date" value="{{ date('Y-m-d', strtotime($student->studentProfile->expire_date)) }}" autocomplete="false" />
            </div>
        </div>

    </div>
</view-block>

<div class="col-md-12 form-group">
    <div class="col-md-2 "></div>
    <div class="col-md-10">
        <input @if(!$hasRightEdit) disabled @endif v-model="isResident" type="checkbox" name="alien" @if( !empty($student->studentProfile->alien) ) checked @endif /> &nbsp;
        Не является резидентом
    </div>
</div>

<div class="col-md-12 form-group" style="margin-bottom: 20px;">
    <div class="col-md-2"></div>
    <div class="col-lg-10">
        <input @if(!$hasRightEdit) disabled @endif type="checkbox" v-model="ignoreDebt" /> &nbsp;
        Игнорировать задолжность(фин. и академ.)
    </div>
</div>

<div class="col-md-12 form-group">
    <div class="col-md-2 "><label class="pull-right text-right">Категория</label></div>
    <div class="col-md-3">
        <select @if(!$hasRightEdit) disabled @endif class="form-control" name="category" v-model="currentCategory">
            <option value="{{ \App\Profiles::CATEGORY_MATRICULANT }}" >Абитуриент</option>
            <option value="{{ \App\Profiles::CATEGORY_STANDART }}" >Стандарт</option>
            <option value="{{ \App\Profiles::CATEGORY_STANDART_RECOUNT }}" >Стандарт (перезачеты)</option>
            <option value="{{ \App\Profiles::CATEGORY_TRAJECTORY_CHANGE }}" >Смена траектории</option>
            <option value="{{ \App\Profiles::CATEGORY_RETAKE_ENT }}" >Пересдача ЕНТ</option>
            <option value="{{ \App\Profiles::CATEGORY_TRANSIT }}" >Транзит</option>
            <option value="{{ \App\Profiles::CATEGORY_TRANSFER }}" >Переводник</option>
        </select>
    </div>
    {{--
    <div class="col-md-3">
        @if($student->studentProfile->check_level == \App\Profiles::CHECK_LEVEL_OR_CABINET)
            {{ __($student->studentProfile->category) }}
        @endif
    </div>
    --}}
</div>

<div class="col-md-12 form-group" v-show="currentCategory == '{{ \App\Profiles::CATEGORY_TRANSFER }}' || currentCategory == '{{ \App\Profiles::CATEGORY_TRANSIT }}'">
    <div class="col-md-2 "><label class="pull-right text-right">Университет, из которого перевелся студент</label></div>
    <div class="col-md-3">
        <select @if(!$hasRightEdit) disabled @endif @if(!$hasRightEdit) disabled @endif class="selectpicker" data-live-search="true" name="transfer_university_id">
            @if(!$student->studentProfile->transfer_university_id)
            <option value="">Не выбрано</option>
            @endif
            @foreach($univerList as $univer)
            <option @if($univer->id == $student->studentProfile->transfer_university_id) selected @endif value="{{ $univer->id }}" >{{ $univer->name }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="col-md-12 form-group">
    <div class="col-md-2 "><label class="pull-right text-right">{{__('Select education language')}}</label></div>
    <div class="col-md-3">
        <select @if(!$hasRightEdit) disabled @endif class="form-control" name="education_lang">
            <option value="{{\App\Profiles::EDUCATION_LANG_RU}}" @if($student->studentProfile->education_lang == \App\Profiles::EDUCATION_LANG_RU) selected @endif > {{ __('russian') }} </option>
            <option value="{{\App\Profiles::EDUCATION_LANG_KZ}}" @if($student->studentProfile->education_lang == \App\Profiles::EDUCATION_LANG_KZ) selected @endif  > {{ __('kazakh') }} </option>
        </select>
    </div>
</div>

<div class="col-md-12 form-group">
    <div class="col-md-2 "><label class="pull-right text-right">{{__('Education study form')}}</label></div>
    <div class="col-md-3">
        <select @if(!$hasRightEdit) disabled @endif class="form-control" name="education_study_form">
            <option value="{{\App\Profiles::EDUCATION_STUDY_FORM_FULLTIME}}" @if($student->studentProfile->education_study_form == \App\Profiles::EDUCATION_STUDY_FORM_FULLTIME) selected @endif >{{ __(\App\Profiles::EDUCATION_STUDY_FORM_FULLTIME) }}</option>
            <option value="{{\App\Profiles::EDUCATION_STUDY_FORM_EVENING}}" @if($student->studentProfile->education_study_form == \App\Profiles::EDUCATION_STUDY_FORM_EVENING) selected @endif>{{ __(\App\Profiles::EDUCATION_STUDY_FORM_EVENING) }}</option>
            <option value="{{\App\Profiles::EDUCATION_STUDY_FORM_ONLINE}}" @if($student->studentProfile->education_study_form == \App\Profiles::EDUCATION_STUDY_FORM_ONLINE) selected @endif>{{ __(\App\Profiles::EDUCATION_STUDY_FORM_ONLINE) }}</option>
            <option value="{{\App\Profiles::EDUCATION_STUDY_FORM_EXTRAMURAL}}" @if($student->studentProfile->education_study_form == \App\Profiles::EDUCATION_STUDY_FORM_EXTRAMURAL) selected @endif>{{ __(\App\Profiles::EDUCATION_STUDY_FORM_EXTRAMURAL) }}</option>
        </select>
    </div>
</div>

<div class="col-md-12 form-group">
    <div class="col-md-2 "><label class="pull-right text-right">Год поступления</label></div>
    <div class="col-md-3">
        <select @if(!$hasRightEdit) disabled @endif class="form-control" name="admission_year" onchange="changeAdmissionYear()">
            @for($year=date('Y', time()); $year >= 2015; $year--)
                <option @if($year == $student->admission_year) selected @endif>{{$year}}</option>
            @endfor
        </select>
    </div>
</div>

<div class="col-md-12 form-group">
    <div class="col-md-2 "><label class="pull-right text-right">{{__('Specialty')}}</label></div>
    <div class="col-md-7">
        <select @if(!$hasRightEdit) disabled @endif class="form-control" name="education_speciality_id">
            @foreach($oSpeciality as $oItem)
                <option @if($oItem->id == $student->studentProfile->education_speciality_id) selected @endif value="{{$oItem->id}}">{{$oItem->name}}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="col-md-12 form-group">
    <div class="col-md-2 "><label class="pull-right text-right">{{__('Sex')}}</label></div>
    <div class="col-md-3">
        <select @if(!$hasRightEdit) disabled @endif class="form-control" name="sex">
            <option value="{{\App\Profiles::GENDER_MALE}}" @if($student->studentProfile->sex == \App\Profiles::GENDER_MALE) selected @endif >{{ __('Male') }}</option>
            <option value="{{\App\Profiles::GENDER_FEMALE}}" @if($student->studentProfile->sex == \App\Profiles::GENDER_FEMALE) selected @endif>{{ __('Female') }}</option>
        </select>
    </div>
</div>

<div class="col-md-12 form-group">
    <div class="col-md-2 "><label class="pull-right text-right">{{__('Nationality')}}</label></div>
    <div class="col-md-3">
        <select @if(!$hasRightEdit) disabled @endif class="form-control" name="nationality_id">
            @foreach($nationalityList as $item)
                <option value="{{$item->id}}" @if( $student->studentProfile->nationality_id == $item->id ) selected @endif >
                    {{ $item->$locale }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="col-md-12 form-group">
    <div class="col-md-2 "><label class="pull-right text-right">{{__('Citizenship')}}</label></div>
    <div class="col-md-3">
        @if( !empty($student->bcApplication) || !empty($student->mgApplication) )
            @if( !empty($student->bcApplication) )
                <select @if(!$hasRightEdit) disabled @endif class="form-control" name="bcApplication[citizenship_id]">
            @elseif( !empty($student->mgApplication) )
                <select @if(!$hasRightEdit) disabled @endif class="form-control" name="mgApplication[citizenship_id]">
            @endif
                @foreach($country as $itemС)
                    <option value="{{$itemС->id}}" @if(
                     ( !empty($student->bcApplication) && ($student->bcApplication->citizenship_id == $itemС->id) ) ||
                     ( !empty($student->mgApplication) && ($student->mgApplication->citizenship_id == $itemС->id) )
                      ) selected @endif >
                        {{ __($itemС->name) }}
                    </option>
                @endforeach
            </select>
        @endif
    </div>
</div>

<div class="col-md-12 form-group">
    <div class="col-md-2 "><label class="pull-right text-right">Дата регистрации</label></div>
    <div class="col-md-3">
        <p>{{ date('d.m.Y',strtotime( $student->created_at )) }}</p>
    </div>
</div>

@if( !empty($student->register_fio) )
    <div class="col-md-12 form-group">
        <div class="col-md-2 "><label class="pull-right text-right">Регистратор</label></div>
        <div class="col-md-3">
            <p>{{ $student->register_fio }}</p>
        </div>
    </div>
@endif

<div class="col-md-12 form-group">
    <div class="col-md-2 "><label class="pull-right text-right">{{__('Course')}}</label></div>
    <div class="col-md-3">
        <select @if(!$hasRightEdit) disabled @endif class="form-control" name="course">
            @if(!$student->studentProfile->course)
            <option value="" selected >
                Не выбрано
            </option>
            @endif
            <option value="{{ \App\Profiles::EDUCATION_COURSE_1 }}" @if( $student->studentProfile->course == \App\Profiles::EDUCATION_COURSE_1 ) selected @endif >
                {{ \App\Profiles::EDUCATION_COURSE_1 }}
            </option>
            <option value="{{ \App\Profiles::EDUCATION_COURSE_2 }}" @if( $student->studentProfile->course == \App\Profiles::EDUCATION_COURSE_2 ) selected @endif >
                {{ \App\Profiles::EDUCATION_COURSE_2 }}
            </option>
            <option value="{{ \App\Profiles::EDUCATION_COURSE_3 }}" @if( $student->studentProfile->course == \App\Profiles::EDUCATION_COURSE_3 ) selected @endif >
                {{ \App\Profiles::EDUCATION_COURSE_3 }}
            </option>
            <option value="{{ \App\Profiles::EDUCATION_COURSE_4 }}" @if( $student->studentProfile->course == \App\Profiles::EDUCATION_COURSE_4 ) selected @endif >
                {{ \App\Profiles::EDUCATION_COURSE_4 }}
            </option>
            <option value="{{ \App\Profiles::EDUCATION_COURSE_5 }}" @if( $student->studentProfile->course == \App\Profiles::EDUCATION_COURSE_5 ) selected @endif >
                {{ \App\Profiles::EDUCATION_COURSE_5 }}
            </option>
            <option value="{{ \App\Profiles::EDUCATION_COURSE_6 }}" @if( $student->studentProfile->course == \App\Profiles::EDUCATION_COURSE_6 ) selected @endif >
                {{ \App\Profiles::EDUCATION_COURSE_6 }}
            </option>
        </select>
    </div>
</div>

<div class="col-md-12 form-group">
    <div class="col-md-2 ">
        <label class="pull-right text-right">{{__('Team')}}</label>
    </div>
    <div class="col-md-3">
        <select @if(!$hasRightEdit) disabled @endif class="selectpicker" name="study_group_id" data-live-search="true">
            <option value="">Не выбрано</option>
            @foreach($studyGroupList as $studyGroup)
                <option @if($studyGroup->id == $student->studentProfile->study_group_id) selected @endif value="{{$studyGroup->id}}">{{$studyGroup->name}}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="col-md-12 form-group">
    <div class="col-md-2 ">
        <label class="pull-right text-right">{{__('Workplace')}}</label>
    </div>
    <div class="col-md-3">
        <textarea @if(!$hasRightEdit) disabled @endif class="form-control" name="workplace">{{ $student->studentProfile->workplace ?? '' }}</textarea>
    </div>
</div>

@if($hasRightEdit)
<div class="col-md-12 form-group">
    <div class="col-md-2 ">
        <label class="pull-right text-right">{{__('Сменить пароль')}}</label>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="" class="col-sm-5 control-label">Новый пароль</label>
            <div class="col-sm-7">
                <input type="password" name="passwordData" value="" class="form-control" value="" autocomplete="false" readonly onfocus="this.removeAttribute('readonly')" />
            </div>
        </div>
        <div class="form-group">
            <label for="" class="col-sm-5 control-label">Подтвердить пароль</label>
            <div class="col-sm-7">
                <input type="password" name="password_confirmation" value="" class="form-control" value="" autocomplete="false" readonly onfocus="this.removeAttribute('readonly')" />
            </div>
        </div>
    </div>
</div>
@endif

<div class="col-md-12 form-group">
    <div class="col-md-2 ">
        <label class="pull-right text-right">{{__('Balance')}}</label>
    </div>
    <div class="col-md-3">
        <button @click="refreshBalance" v-bind:disabled="isRefreshBalance" class="btn btn-sm btn-primary" type="button"><i class="fa fa-refresh"></i></button>
        &nbsp;&nbsp;
        <span v-if="userBalance !== false">@{{ userBalance }}</span>
    </div>
</div>

<div class="col-md-12 form-group" v-if="currentCategory == 'trajectory_change'">
    <div class="col-md-2 "></div>
    <div class="col-md-8">
        <select @if(!$hasRightEdit) disabled @endif name="change_speciality_id" class="form-control">
            @if($equalSpecialities)
                @foreach($equalSpecialities as $eqSpeciality)
                    <option value="{{ $eqSpeciality->id }}" @if($eqSpeciality->id == $student->studentProfile->speciality->id) selected @endif>{{ $eqSpeciality->name}} ({{ $eqSpeciality->year }}) - {{ $eqSpeciality->eqDisciplineCount }} совпадений</option>
                @endforeach
            @endif
        </select>
        @if($originalSpeciality)
        <div>
            Исходная специальность - {{ $originalSpeciality->name}} ({{ $originalSpeciality->year }})
        </div>
        @endif
    </div>
</div>

<div class="col-md-12 form-group">
    <div class="col-md-2 ">
        <label class="pull-right text-right">{{__('Semester credits limit')}}</label>
    </div>
    <div class="col-md-1">
        <input @if(!$hasRightEdit) disabled @endif type="number" class="form-control" name="semester_credits_limit" value="{{ $student->studentProfile->semester_credits_limit ?? '' }}" autocomplete="false" />
    </div>
</div>

<div class="col-md-12 form-group">
    <div class="col-md-2 ">
        <label class="pull-right text-right">Активная скидка</label>
    </div>
    <div class="col-md-8">
        <div class="panel panel-default">
            <div class="panel-body">
                @forelse($approvedDiscounts as $discount)
                    <h5>
                        <a href="{{ route('adminDiscountRequestsEdit', [$discount->id]) }}">
                            {{ $discount->name }}
                        </a>
                    </h5>
                    <small>
                        <label>Статус:</label> {{ __($discount->status) }}
                    </small>
                    <p>
                        <label>Размер скидки:</label> {{ $discount->discount }} %
                    </p>
                    <p>
                        <label>Действие скидки (семестры):</label>
                        @foreach($discount->semesters as $semester)
                            {{ $semester->semester . ($loop->last ? '' : ',') }}
                        @endforeach
                    </p>
                @empty
                    <h5>Нет активных скидок</h5>
                @endforelse
            </div>
        </div>

    </div>
</div>

<div class="col-md-12 form-group">
    <div class="col-md-2 ">
        <label class="pull-right text-right">Транскрипт</label>
    </div>
    <div class="col-md-8">

        <div class="form-group">
            <div class="col-sm-5">
                <input type="text" name="sector" id="sector" v-model="sector" class="form-control" placeholder="Укажите сектор">
                <input type="text" name="sectorManager" id="sectorManager" v-model="sectorManager" class="form-control" placeholder="Укажите Менеджера сектора">
            </div>
            <div class="col-sm-4">
                <a class="btn btn-info" v-bind:href="'{{ route('adminStudentGenTranscript', [$id]) }}?sector=' + sector + '||' + sectorManager" target="_blank">Сгенерировать транскрипт</a>
            </div>
        </div>
        
        
    </div>
</div>

