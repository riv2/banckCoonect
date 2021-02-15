<?php
$hasRightEdit = \App\Services\Auth::user()->hasRight('students', 'edit')
?>

<view-block label="Адрес">
    <div class="form-group subform" >

        @if( true )

            <div v-if="!isResident" class="form-group">
                <div class="col-md-2 ">
                    <label class="pull-right text-right">Область</label>
                </div>
                <div class="col-md-8">
                    <select @if(!$hasRightEdit) disabled @endif class="selectpicker" name="mgApplication[region_id]" data-live-search="true" data-size="5" title="{{ __('Please select') }}" required>
                        @foreach($regions as $item)
                            <option @if($item->id == $student->mgApplication->region_id) selected @endif value="{{$item->id}}">{{$item->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div v-if="isResident" class="form-group">
                <div class="col-md-2 ">
                    <label class="pull-right text-right">Страна</label>
                </div>
                <div class="col-md-8">
                    <select @if(!$hasRightEdit) disabled @endif class="form-control" name="mgApplication[country_id]">
                        @foreach($country as $itemС)
                            <option value="{{$itemС->id}}" @if( !empty($student->mgApplication) && ($student->mgApplication->country_id == $itemС->id) ) selected @endif >
                                {{ __($itemС->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group cities">
                <div class="col-md-2 ">
                    <label class="pull-right text-right">Населенный пункт</label>
                </div>
                <div class="col-md-8 ">
                    <input @if(!$hasRightEdit) disabled @endif id="city" type="text" class="form-control autocomplete" name="mgApplication[city]" value="{{ $student->mgApplication->city->name ?? '' }}" required autofocus autocomplete="false" />
                </div>
            </div>

            <div class="col-md-12 form-group">
                <div class="col-md-2 ">
                    <label class="pull-right text-right">Улица</label>
                </div>
                <div class="col-md-10">
                    <input @if(!$hasRightEdit) disabled @endif type="text" class="form-control" name="mgApplication[street]" value="{{ $student->mgApplication->street ?? '' }}" autocomplete="false" />
                </div>
            </div>

            <div class="col-md-12 form-group">
                <div class="col-md-2 ">
                    <label class="pull-right text-right">{{__('Buliding number')}}</label>
                </div>
                <div class="col-md-2">
                    <input @if(!$hasRightEdit) disabled @endif type="text" class="form-control" name="mgApplication[building_number]" value="{{ $student->mgApplication->building_number ?? '' }}" autocomplete="false" />
                </div>
            </div>

            <div class="col-md-12 form-group">
                <div class="col-md-2 ">
                    <label class="pull-right text-right">{{__('Apartment number')}}</label>
                </div>
                <div class="col-md-2">
                    <input @if(!$hasRightEdit) disabled @endif type="text" class="form-control" name="mgApplication[apartment_number]" value="{{ $student->mgApplication->apartment_number ?? '' }}" autocomplete="false" />
                </div>
            </div>

        @else

            <view-text label="Область">{{$student->mgApplication->region->name ?? ''}}</view-text>
            <view-text label="Населенный пункт">{{$student->mgApplication->city->name ?? ''}}</view-text>
            <view-text label="Улица">{{$student->mgApplication->street}}</view-text>
            <view-text label="{{__('Buliding number')}}">{{$student->mgApplication->building_number ?? ''}}</view-text>
            <view-text label="{{__('Apartment number')}}">{{$student->mgApplication->apartment_number ?? ''}}</view-text>

        @endif
    </div>
</view-block>

<!--
@if($student->mgApplication->eng_certificate_number)
<view-block label="{{__('Сертификат подтверждающий англ-язык')}}">
    <div class="form-group subform" >
        <view-text label="{{__('Number')}}">{{ __($student->mgApplication->eng_certificate_number)}}</view-text>
        <view-text label="{{__('Series')}}">{{ __($student->mgApplication->eng_certificate_series)}}</view-text>
        <view-date label="{{__('Issue date')}}">{{ __($student->mgApplication->eng_certificate_date)}}</view-date>
        {{--
        <view-doc label="{{__('Photo certificate')}}"
                  name="mgApplication[eng_certificate_status]"
                  status="{{$student->mgApplication->eng_certificate_status}}"
                  @if($student->mgApplication->eng_certificate_photo)
                    file-name="/images/uploads/eng_certificate/{{$student->mgApplication->eng_certificate_photo}}"
                  @endif
        >
        </view-doc>
        --}}
    </div>
</view-block>
@else
    <div class="col-md-12 form-group">
        <div class="col-md-2 "><label class="pull-right">{{__('Сертификат подтверждающий англ-язык')}}</label></div>
        <div class="col-md-10">
            <span class="alert alert-warning">данные не предоставлены</span>
        </div>
    </div>
@endif
-->

<div class="col-md-12 form-group">
  <div class="col-md-2">
      <label class="pull-right text-right">Список необходимых документов</label>
  </div>
  <div class="col-md-10">

    <view-doc-item label="{{__('diploma_photo')}}"
      @if($student->studentProfile->diploma_photo)
        status="{{$student->studentProfile->diploma_photo->status}}"
        statustext="{{__($student->studentProfile->diploma_photo->status)}}"
        delivered="{{ $student->studentProfile->diploma_photo->delivered ? '':'не' }}доставлен"
      @endif
    ></view-doc-item>

    <view-doc-item label="{{__('doc_atteducation')}}"
      @if($student->studentProfile->doc_atteducation)
        status="{{$student->studentProfile->doc_atteducation->status}}"
        statustext="{{__($student->studentProfile->doc_atteducation->status)}}"
        delivered="{{ $student->studentProfile->doc_atteducation->delivered ? '':'не' }}доставлен"
      @endif
    ></view-doc-item>

    <view-doc-item label="{{__('doc_atteducation_back')}}"
      @if($student->studentProfile->doc_atteducation_back)
        status="{{$student->studentProfile->doc_atteducation_back->status}}"
        statustext="{{__($student->studentProfile->doc_atteducation_back->status)}}"
        delivered="{{ $student->studentProfile->doc_atteducation_back->delivered ? '':'не' }}доставлен"
      @endif
    ></view-doc-item>

    <view-doc-item label="{{__('front_id_photo')}}"
      @if($student->studentProfile->front_id_photo)
        status="{{$student->studentProfile->front_id_photo->status}}"
        statustext="{{__($student->studentProfile->front_id_photo->status)}}"
        delivered="{{ $student->studentProfile->front_id_photo->delivered ? '':'не' }}доставлен"
      @endif
    ></view-doc-item>

    <view-doc-item label="{{__('back_id_photo')}}"
      @if($student->studentProfile->back_id_photo)
        status="{{$student->studentProfile->back_id_photo->status}}"
        statustext="{{__($student->studentProfile->back_id_photo->status)}}"
        delivered="{{ $student->studentProfile->back_id_photo->delivered ? '':'не' }}доставлен"
      @endif
    ></view-doc-item>

    <view-doc-item label="{{__('military_status')}}"
      @if($student->studentProfile->military_status)
        status="{{$student->studentProfile->military_status->status}}"
        statustext="{{__($student->studentProfile->military_status->status)}}"
        delivered="{{ $student->studentProfile->military_status->delivered ? '':'не' }}доставлен"
      @endif
    ></view-doc-item>

    <view-doc-item label="{{__('kt_certificate')}}"
      @if($student->studentProfile->kt_certificate)
        status="{{$student->studentProfile->kt_certificate->status}}"
        statustext="{{__($student->studentProfile->kt_certificate->status)}}"
        delivered="{{ $student->studentProfile->kt_certificate->delivered ? '':'не' }}доставлен"
      @endif
    ></view-doc-item>

    <view-doc-item label="{{__('doc_r086')}}"
      @if($student->studentProfile->doc_r086)
        status="{{$student->studentProfile->doc_r086->status}}"
        statustext="{{__($student->studentProfile->doc_r086->status)}}"
        delivered="{{ $student->studentProfile->doc_r086->delivered ? '':'не' }}доставлен"
      @endif
    ></view-doc-item>

    <view-doc-item label="{{__('doc_r086_back')}}"
      @if($student->studentProfile->doc_r086_back)
        status="{{$student->studentProfile->doc_r086_back->status}}"
        statustext="{{__($student->studentProfile->doc_r086_back->status)}}"
        delivered="{{ $student->studentProfile->doc_r086_back->delivered ? '':'не' }}доставлен"
      @endif
    ></view-doc-item>

    <view-doc-item label="{{__('doc_r063')}}"
      @if($student->studentProfile->doc_r063)
        status="{{$student->studentProfile->doc_r063->status}}"
        statustext="{{__($student->studentProfile->doc_r063->status)}}"
        delivered="{{ $student->studentProfile->doc_r063->delivered ? '':'не' }}доставлен"
      @endif
    ></view-doc-item>

    <view-doc-item label="{{__('doc_work_book')}}"
      @if($student->studentProfile->doc_work_book)
        status="{{$student->studentProfile->doc_work_book->status}}"
        statustext="{{__($student->studentProfile->doc_work_book->status)}}"
        delivered="{{ $student->studentProfile->doc_work_book->delivered ? '':'не' }}доставлен"
      @endif
    ></view-doc-item>

    <view-doc-item label="{{__('education_contracts')}} (страниц {{count($student->studentProfile->education_contracts)}})"
      @if( !empty($student->studentProfile->education_contracts[0]) )
        status="{{$student->studentProfile->education_contracts[0]->status}}"
        statustext="{{__($student->studentProfile->education_contracts[0]->status)}}"
        delivered="{{ $student->studentProfile->education_contracts[0]->delivered ? '':'не' }}доставлен"
      @endif
    ></view-doc-item>


    <view-doc-item label="{{__('education_statement')}}"
      @if($student->studentProfile->education_statement)
        status="{{$student->studentProfile->education_statement->status}}"
        statustext="{{__($student->studentProfile->education_statement->status)}}"
        delivered="{{ $student->studentProfile->education_statement->delivered ? '':'не' }}доставлен"
      @endif
    ></view-doc-item>

    @if($student->mgApplication->kzornot == 'false')

      <view-doc-item label="{{__('doc_nostrificationattach')}}"
        @if($student->studentProfile->doc_nostrificationattach)
          status="{{$student->studentProfile->doc_nostrificationattach->status}}"
          statustext="{{__($student->studentProfile->doc_nostrificationattach->status)}}"
          delivered="{{ $student->studentProfile->doc_nostrificationattach->delivered ? '':'не' }}доставлен"
        @endif
      ></view-doc-item>

    @endif

  </div>
</div>


{{--
<view-doc label="{{__('Diploma')}}"
          name="mgApplication[diploma_photo]"
          @if($student->studentProfile->diploma_photo)
          status="{{$student->studentProfile->diploma_photo->status}}"
          file-name="{{$student->studentProfile->diploma_photo->getPublicFileName()}}"
          delivered="{{ (bool)$student->studentProfile->diploma_photo->delivered }}"
          deliveredname="mgApplication[delivered][diploma_photo]"
        @endif
></view-doc>
<view-doc label="{{__('Diploma supplement')}}"
          name="mgApplication[atteducation_status]"
          @if($student->studentProfile->doc_atteducation)
          status="{{$student->studentProfile->doc_atteducation->status}}"
          file-name="{{$student->studentProfile->doc_atteducation->getPublicFileName()}}"
          delivered="{{ (bool)$student->studentProfile->doc_atteducation->delivered }}"
          deliveredname="mgApplication[delivered][atteducation_status]"
          @endif
></view-doc>
<view-doc label="{{__('Diploma supplement back')}}"
          name="mgApplication[atteducation_status_back]"
          @if($student->studentProfile->doc_atteducation_back)
          status="{{$student->studentProfile->doc_atteducation_back->status}}"
          file-name="{{$student->studentProfile->doc_atteducation_back->getPublicFileName()}}"
          delivered="{{ (bool)$student->studentProfile->doc_atteducation_back->delivered }}"
          deliveredname="mgApplication[delivered][atteducation_status_back]"
        @endif
></view-doc>
--}}
{{--
<view-doc label="{{__('Identification ID (page 1)')}}"
          name="mgApplication[front_id_photo]"
          @if($student->studentProfile->front_id_photo)
          status="{{$student->studentProfile->front_id_photo->status}}"
          file-name="{{ $student->studentProfile->front_id_photo->getPublicFileName() }}"
          delivered="{{ (bool)$student->studentProfile->front_id_photo->delivered }}"
          deliveredname="mgApplication[delivered][front_id_photo]"
        @endif
>
</view-doc>
<view-doc label="{{__('Identification ID (page 2)')}}"
          name="mgApplication[back_id_photo]"
          @if($student->studentProfile->back_id_photo)
          status="{{$student->studentProfile->back_id_photo->status}}"
          file-name="{{ $student->studentProfile->back_id_photo->getPublicFileName() }}"
          delivered="{{ (bool)$student->studentProfile->back_id_photo->delivered }}"
          deliveredname="mgApplication[delivered][back_id_photo]"
        @endif
>
</view-doc>
<view-doc label="{{__('Military enlistment office')}}"
          name="mgApplication[military_status]"
          @if($student->studentProfile->doc_military)
          status="{{$student->studentProfile->doc_military->status}}"
          file-name="{{ $student->studentProfile->doc_military->getPublicFileName() }}"
          delivered="{{ (bool)$student->studentProfile->doc_military->delivered }}"
          deliveredname="mgApplication[delivered][military_status]"
          @endif
>
</view-doc>
--}}
{{--
<view-doc label="{{__('KT certificate')}}"
          name="mgApplication[kt_certificate]"
          @if($student->studentProfile->kt_certificate)
          status="{{ $student->studentProfile->kt_certificate ? $student->studentProfile->kt_certificate->status : '' }}"
          file-name="{{ $student->studentProfile->kt_certificate ? $student->studentProfile->kt_certificate->getPublicFileName() : '' }}"
          delivered="{{ $student->studentProfile->kt_certificate ? (bool)$student->studentProfile->kt_certificate->delivered : '' }}"
          deliveredname="mgApplication[delivered][kt_certificate]"
          @endif
>
</view-doc>
--}}



    <view-block label="{{__('Education')}}">
        <div class="form-group subform" >
            @include('admin.pages.students.mg_application.education')
        </div>
    </view-block>

@if($student->mgApplication->publications)
    <view-block label="{{__('Publications')}}">
        @include('admin.pages.students.mg_application.publication')
    </view-block>
@else
    <div class="col-md-12 form-group">
        <div class="col-md-2 "><label class="pull-right">{{__('Publications')}}</label></div>
        <div class="col-md-10">
            <span class="alert alert-warning">данные не предоставлены</span>
        </div>
    </div>
@endif

<div class="form-group">
    <div class="col-md-2"></div>
    <div class="col-md-10">
        <button class="btn btn-primary btn-sm" v-on:click="changeKt" type="button">Изменить</button>
    </div>
</div>

<div v-if="changeKtFlag">
    <input type="hidden" name="isChangeKt" v-model="isChangeKt" />
    <div class="col-md-2"></div>
    <div class="col-md-10 form-group">
        <div class="col-md-4">
            <select @if(!$hasRightEdit) disabled @endif name="kt_name_1" class="form-control" required>
                @foreach($masterDisciplineKtList as $itemMDKTL)
                    <option value="{{ $itemMDKTL }}">{{ $itemMDKTL }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <input @if(!$hasRightEdit) disabled @endif class="form-control" type="text" name="kt_val_1" value="" required autocomplete="false" />
        </div>
    </div>
    <div class="col-md-2"></div>
    <div class="col-md-10 form-group">
        <div class="col-md-4">
            <select name="kt_name_2" class="form-control" required>
                @foreach($masterDisciplineKtList as $itemMDKTL)
                    <option value="{{ $itemMDKTL }}">{{ $itemMDKTL }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <input @if(!$hasRightEdit) disabled @endif class="form-control" type="text" name="kt_val_2" value="" required autocomplete="false" />
        </div>
    </div>
    <div class="col-md-2"></div>
    <div class="col-md-10 form-group">
        <div class="col-md-4">
            <select @if(!$hasRightEdit) disabled @endif name="kt_name_3" class="form-control" required>
                @foreach($masterDisciplineKtList as $itemMDKTL)
                    <option value="{{ $itemMDKTL }}">{{ $itemMDKTL }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <input @if(!$hasRightEdit) disabled @endif class="form-control" type="text" name="kt_val_3" value="" required autocomplete="false" />
        </div>
    </div>
    <div class="col-md-2"></div>
    <div class="col-md-10 form-group">
        <div class="col-md-4">
            <select @if(!$hasRightEdit) disabled @endif name="kt_name_4" class="form-control" required>
                @foreach($masterDisciplineKtList as $itemMDKTL)
                    <option value="{{ $itemMDKTL }}">{{ $itemMDKTL }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <input @if(!$hasRightEdit) disabled @endif class="form-control" type="text" name="kt_val_4" value="" required autocomplete="false" />
        </div>
    </div>
</div>

@if( !empty($student->mgApplication->kt_name_1) && !empty($student->mgApplication->kt_val_1) )
<view-block label="Результаты">
    <div class="subform">
        <div>
            @if($student->mgApplication->kt_name_1)
                <div class="col-md-12">
                    {{$student->mgApplication->kt_name_1}}:&nbsp;{{$student->mgApplication->kt_val_1}}
                </div>
            @endif
            @if($student->mgApplication->kt_name_2)
                <div class="col-md-12">
                    {{$student->mgApplication->kt_name_2}}:&nbsp;{{$student->mgApplication->kt_val_2}}
                </div>
            @endif
            @if($student->mgApplication->kt_name_3)
                <div class="col-md-12">
                    {{$student->mgApplication->kt_name_3}}:&nbsp;{{$student->mgApplication->kt_val_3}}
                </div>
            @endif
            @if($student->mgApplication->kt_name_4)
                <div class="col-md-12">
                    {{$student->mgApplication->kt_name_4}}:&nbsp;{{$student->mgApplication->kt_val_4}}
                </div>
            @endif
            @if($student->mgApplication->kt_total)
                <div class="col-md-12">
                    Общий:&nbsp;{{$student->mgApplication->kt_total}}
                </div>
            @endif
        </div>
    </div>
</view-block>
@endif
{{--
<view-doc label="{{__('Reference 086')}}"
          name="mgApplication[r086_status]"
          @if($student->studentProfile->doc_r086)
          status="{{$student->studentProfile->doc_r086->status}}"
          file-name="{{ $student->studentProfile->doc_r086->getPublicFileName() }}"
          delivered="{{ (bool)$student->studentProfile->doc_r086->delivered }}"
          deliveredname="mgApplication[delivered][r086_status]"
        @endif
>
</view-doc>
<view-doc label="{{__('Reference 086 back')}}"
          name="mgApplication[r086_status_back]"
          @if($student->studentProfile->doc_r086_back)
          status="{{$student->studentProfile->doc_r086_back->status}}"
          file-name="{{ $student->studentProfile->doc_r086_back->getPublicFileName() }}"
          delivered="{{ (bool)$student->studentProfile->doc_r086_back->delivered }}"
          deliveredname="mgApplication[delivered][r086_status_back]"
        @endif
>
</view-doc>
<view-doc label="{{__('Reference 063')}}"
          name="mgApplication[r063_status]"
          @if($student->studentProfile->doc_r063)
          status="{{ $student->studentProfile->doc_r063->status }}"
          file-name="{{ $student->studentProfile->doc_r063->getPublicFileName() }}"
          delivered="{{ (bool)$student->studentProfile->doc_r063->delivered }}"
          deliveredname="mgApplication[delivered][r063_status]"
        @endif
>
</view-doc>

<view-doc label="{{__('Employment history')}}"
          name="mgApplication[work_book_status]"
          @if($student->studentProfile->doc_work_book)
          status="{{$student->studentProfile->doc_work_book->status}}"
          file-name="{{ $student->studentProfile->doc_work_book->getPublicFileName() }}"
          delivered="{{ (bool)$student->studentProfile->doc_work_book->delivered }}"
          deliveredname="mgApplication[delivered][work_book_status]"
          @endif
>
</view-doc>

<view-block label="{{__('Education contract')}}">
    <div class="form-group subform" >

        <view-doc label="{{__('Side 1')}}"
                  name="mgApplication[education_contracts][0]"
                  @if( !empty($student->studentProfile->education_contracts[0]) )
                  status="{{$student->studentProfile->education_contracts[0]->status}}"
                  file-name="{{ $student->studentProfile->education_contracts[0]->getPublicFileName() }}"
                  delivered="{{ (bool)$student->studentProfile->education_contracts[0]->delivered }}"
                  deliveredname="mgApplication[delivered][education_contracts][0]"
                @endif
        >
        </view-doc>

        <view-doc label="{{__('Side 2')}}"
                  name="mgApplication[education_contracts][1]"
                  @if( !empty($student->studentProfile->education_contracts[1]) )
                  status="{{$student->studentProfile->education_contracts[1]->status}}"
                  file-name="{{ $student->studentProfile->education_contracts[1]->getPublicFileName() }}"
                  delivered="{{ (bool)$student->studentProfile->education_contracts[1]->delivered }}"
                  deliveredname="mgApplication[delivered][education_contracts][1]"
                @endif
        >
        </view-doc>

        <view-doc label="{{__('Side 3')}}"
                  name="mgApplication[education_contracts][2]"
                  @if( !empty($student->studentProfile->education_contracts[2]) )
                  status="{{$student->studentProfile->education_contracts[2]->status}}"
                  file-name="{{ $student->studentProfile->education_contracts[2]->getPublicFileName() }}"
                  delivered="{{ (bool)$student->studentProfile->education_contracts[2]->delivered }}"
                  deliveredname="mgApplication[delivered][education_contracts][2]"
                @endif
        >
        </view-doc>

        <view-doc label="{{__('Side 4')}}"
                  name="mgApplication[education_contracts][3]"
                  @if( !empty($student->studentProfile->education_contracts[3]) )
                  status="{{$student->studentProfile->education_contracts[3]->status}}"
                  file-name="{{ $student->studentProfile->education_contracts[3]->getPublicFileName() }}"
                  delivered="{{ (bool)$student->studentProfile->education_contracts[3]->delivered }}"
                  deliveredname="mgApplication[delivered][education_contracts][3]"
                @endif
        >
        </view-doc>

        <view-doc label="{{__('Side 5')}}"
                  name="mgApplication[education_contracts][4]"
                  @if( !empty($student->studentProfile->education_contracts[4]) )
                  status="{{$student->studentProfile->education_contracts[4]->status}}"
                  file-name="{{ $student->studentProfile->education_contracts[4]->getPublicFileName() }}"
                  delivered="{{ (bool)$student->studentProfile->education_contracts[4]->delivered }}"
                  deliveredname="mgApplication[delivered][education_contracts][4]"
                @endif
        >
        </view-doc>

    </div>
</view-block>

<view-doc label="{{__('Education statement')}}"
          name="mgApplication[education_statement]"
          @if($student->studentProfile->education_statement)
          status="{{$student->studentProfile->education_statement->status}}"
          file-name="{{ $student->studentProfile->education_statement->getPublicFileName() }}"
          delivered="{{ (bool)$student->studentProfile->education_statement->delivered }}"
          deliveredname="mgApplication[delivered][education_statement]"
        @endif
>
</view-doc>
--}}
<div class="col-md-12 form-group">
    <div class="col-md-2 "><label class="pull-right"></label></div>
    <div class="col-md-10" >

        <div class="alert alert-success col-md-3" v-if="docsTypeStatus == 'accept'"><i class="fa fa-check"></i> Все документы проверены</div>

        <a class="btn btn-success" v-on:click="docsTypeStatus = 'accept'" v-if="docsTypeStatus != 'accept'"> Все документы проверены </a>
        <input type="hidden" value="true" name="docs_type_success" v-if="docsTypeStatus == 'accept'">

        <div class="clearfix"></div>
        <a href="{{ route('StudentgenerateNoteEducationDocument',['id'=>$id]) }}">{{ __('Download note list documents receipt') }}</a>
        <br>
        <a href="{{ route('StudentgenerateOpisEducationDocument',['id'=>$id]) }}">{{ __('Download note list inventory documents') }}</a>
        <br>
        <a href="{{ route('StudentgenerateTitleList',['id'=>$id]) }}">{{ __('Download title list') }}</a>
        <br>
        <a href="{{ route('userGenerateEducationContract',['id'=>$id]) }}">Скачать договор</a>
        <br>
        <a href="{{ route('userPrintEducationStatement',['id'=>$id]) }}">Скачать заявление</a>

    </div>
</div>