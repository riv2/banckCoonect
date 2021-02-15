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
                    <select @if(!$hasRightEdit) disabled @endif class="form-control" name="bcApplication[region_id]" data-live-search="true" data-size="5" title="{{ __('Please select') }}">
                        @foreach($regions as $item)
                            <option @if($item->id == $student->bcApplication->region_id) selected @endif value="{{$item->id}}">{{ $item->name ?? '' }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div v-if="isResident" class="form-group">
                <input @if(!$hasRightEdit) disabled @endif name="bcApplication[region_id]" type="hidden" value="" />
                <div class="col-md-2 ">
                    <label class="pull-right text-right">Страна</label>
                </div>
                <div class="col-md-8">
                    <select @if(!$hasRightEdit) disabled @endif class="form-control" name="bcApplication[country_id]">
                        @foreach($country as $itemС)
                            <option value="{{$itemС->id}}" @if( !empty($student->bcApplication) && ($student->bcApplication->country_id == $itemС->id) ) selected @endif >
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
                    <input @if(!$hasRightEdit) disabled @endif id="city" type="text" class="form-control" name="bcApplication[city]" value="{{ $student->bcApplication->city->name ?? '' }}" autocomplete="false" />
                </div>
            </div>

            <div class="col-md-12 form-group">
                <div class="col-md-2 ">
                    <label class="pull-right text-right">Улица</label>
                </div>
                <div class="col-md-10">
                    <input @if(!$hasRightEdit) disabled @endif type="text" class="form-control" name="bcApplication[street]" value="{{ $student->bcApplication->street ?? '' }}" autocomplete="false" />
                </div>
            </div>

            <div class="col-md-12 form-group">
                <div class="col-md-2 ">
                    <label class="pull-right text-right">{{__('Buliding number')}}</label>
                </div>
                <div class="col-md-2">
                    <input @if(!$hasRightEdit) disabled @endif type="text" class="form-control" name="bcApplication[building_number]" value="{{ $student->bcApplication->building_number ?? '' }}" autocomplete="false" />
                </div>
            </div>

            <div class="col-md-12 form-group">
                <div class="col-md-2 ">
                    <label class="pull-right text-right">{{__('Apartment number')}}</label>
                </div>
                <div class="col-md-2">
                    <input @if(!$hasRightEdit) disabled @endif type="text" class="form-control" name="bcApplication[apartment_number]" value="{{ $student->bcApplication->apartment_number ?? '' }}" autocomplete="false" />
                </div>
            </div>

        @else

            <view-text label="Область">{{ $student->bcApplication->region->name ?? '' }}</view-text>
            <view-text label="Населенный пункт">{{ $student->bcApplication->city->name ?? '' }}</view-text>
            <view-text label="Улица">{{ $student->bcApplication->street ?? '' }}</view-text>
            <view-text label="{{__('Buliding number')}}">{{ $student->bcApplication->building_number ?? '' }}</view-text>
            <view-text label="{{__('Apartment number')}}">{{ $student->bcApplication->apartment_number ?? '' }}</view-text>

        @endif
    </div>
</view-block>


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

          <view-doc-item label="{{__('doc_ent')}}"
            @if($student->studentProfile->doc_ent)
              status="{{$student->studentProfile->doc_ent->status}}"
              statustext="{{__($student->studentProfile->doc_ent->status)}}"
              delivered="{{ $student->studentProfile->doc_ent->delivered ? '':'не' }}доставлен"
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

          <view-doc-item label="{{__('doc_military')}}"
            @if($student->studentProfile->doc_military)
              status="{{$student->studentProfile->doc_military->status}}"
              statustext="{{__($student->studentProfile->doc_military->status)}}"
              delivered="{{ $student->studentProfile->doc_military->delivered ? '':'не' }}доставлен"
            @endif
          ></view-doc-item>

          <view-doc-item label="{{__('doc_r086')}}"
            @if($student->studentProfile->doc_r086)
              status="{{$student->studentProfile->doc_r086->status}}"
              statustext="{{__($student->studentProfile->doc_r086->status)}}"
              delivered="{{ $student->studentProfile->doc_r086->delivered ? '':'не' }}доставлен"
            @endif
          ></view-doc-item>

          <view-doc-item label="{{__('r086_status_back')}}"
            @if($student->studentProfile->r086_status_back)
              status="{{$student->studentProfile->r086_status_back->status}}"
              statustext="{{__($student->studentProfile->r086_status_back->status)}}"
              delivered="{{ $student->studentProfile->r086_status_back->delivered ? '':'не' }}доставлен"
            @endif
          ></view-doc-item>

          <view-doc-item label="{{__('r063_status')}}"
            @if($student->studentProfile->r063_status)
              status="{{$student->studentProfile->r063_status->status}}"
              statustext="{{__($student->studentProfile->r063_status->status)}}"
              delivered="{{ $student->studentProfile->r063_status->delivered ? '':'не' }}доставлен"
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

          @if($student->bcApplication->kzornot == 'false')

            <view-doc-item label="{{__('doc_nostrification')}}"
              @if($student->studentProfile->doc_nostrification)
                status="{{$student->studentProfile->doc_nostrification->status}}"
                statustext="{{__($student->studentProfile->doc_nostrification->status)}}"
                delivered="{{ $student->studentProfile->doc_nostrification->delivered ? '':'не' }}доставлен"
              @endif
            ></view-doc-item>

            <view-doc-item label="{{__('doc_con_confirm')}}"
              @if($student->studentProfile->doc_con_confirm)
                status="{{$student->studentProfile->doc_con_confirm->status}}"
                statustext="{{__($student->studentProfile->doc_con_confirm->status)}}"
                delivered="{{ $student->studentProfile->doc_con_confirm->delivered ? '':'не' }}доставлен"
              @endif
            ></view-doc-item>

          @endif

          
{{--
          <view-doc label="{{__('Diploma supplement')}}"
                    name="bcApplication[atteducation_status]"
                    @if($student->studentProfile->doc_atteducation)
                    status="{{$student->studentProfile->doc_atteducation->status}}"
                    file-name="{{$student->studentProfile->doc_atteducation->getPublicFileName()}}"
                    delivered="{{ (bool)$student->studentProfile->doc_atteducation->delivered }}"
                    deliveredname="bcApplication[delivered][atteducation_status]"
                    @endif
          ></view-doc>
          <view-doc label="{{__('Diploma supplement back')}}"
                    name="bcApplication[atteducation_status_back]"
                    @if($student->studentProfile->doc_atteducation_back)
                    status="{{$student->studentProfile->doc_atteducation_back->status}}"
                    file-name="{{$student->studentProfile->doc_atteducation_back->getPublicFileName()}}"
                    delivered="{{ (bool)$student->studentProfile->doc_atteducation_back->delivered }}"
                    deliveredname="bcApplication[delivered][atteducation_status_back]"
                    @endif
          ></view-doc>

          <view-doc label=""
                    name="bcApplication[ent_certificate]"
                    @if($student->studentProfile->doc_ent)
                    status="{{$student->studentProfile->doc_ent->status}}"
                    file-name="{{$student->studentProfile->doc_ent->getPublicFileName()}}"
                    delivered="{{ (bool)$student->studentProfile->doc_ent->delivered }}"
                    deliveredname="bcApplication[delivered][ent_certificate]"
                  @endif
          >
          </view-doc>

          <view-doc label="{{__('Identification ID (page 1)')}}"
                    name="bcApplication[front_id_photo]"
                    @if($student->studentProfile->front_id_photo)
                      status="{{$student->studentProfile->front_id_photo->status}}"
                      file-name="{{ $student->studentProfile->front_id_photo->getPublicFileName() }}"
                      delivered="{{ (bool)$student->studentProfile->front_id_photo->delivered }}"
                      deliveredname="bcApplication[delivered][front_id_photo]"
                    @endif
          >
          </view-doc>
          <view-doc label="{{__('Identification ID (page 2)')}}"
                    name="bcApplication[back_id_photo]"
                    @if($student->studentProfile->back_id_photo)
                    status="{{$student->studentProfile->back_id_photo->status}}"
                    file-name="{{ $student->studentProfile->back_id_photo->getPublicFileName() }}"
                    delivered="{{ (bool)$student->studentProfile->back_id_photo->delivered }}"
                    deliveredname="bcApplication[delivered][back_id_photo]"
                  @endif
          >
          </view-doc>
          <view-doc label="{{__('Military enlistment office')}}"
                    name="bcApplication[military_status]"
                    @if($student->studentProfile->doc_military)
                    status="{{$student->studentProfile->doc_military->status}}"
                    file-name="{{$student->studentProfile->doc_military->getPublicFileName()}}"
                    delivered="{{ (bool)$student->studentProfile->doc_military->delivered }}"
                    deliveredname="bcApplication[delivered][military_status]"
                    @endif
          >
          </view-doc>
          <view-doc label="{{__('Reference 086')}}"
                    name="bcApplication[r086_status]"
                    @if($student->studentProfile->doc_r086)
                    status="{{$student->studentProfile->doc_r086->status}}"
                    file-name="{{$student->studentProfile->doc_r086->getPublicFileName()}}"
                    delivered="{{ (bool)$student->studentProfile->doc_r086->delivered }}"
                    deliveredname="bcApplication[delivered][r086_status]"
                    @endif
          >
          </view-doc>
          <view-doc label="{{__('Reference 086 back')}}"
                    name="bcApplication[r086_status_back]"
                    @if($student->studentProfile->doc_r086_back)
                    status="{{$student->studentProfile->doc_r086_back->status}}"
                    file-name="{{$student->studentProfile->doc_r086_back->getPublicFileName()}}"
                    delivered="{{ (bool)$student->studentProfile->doc_r086_back->delivered }}"
                    deliveredname="bcApplication[delivered][r086_status_back]"
                  @endif
          >
          </view-doc>
          <view-doc label="{{__('Reference 063')}}"
                    name="bcApplication[r063_status]"
                    @if($student->studentProfile->doc_r063)
                    status="{{$student->studentProfile->doc_r063->status}}"
                    file-name="{{$student->studentProfile->doc_r063->getPublicFileName()}}"
                    delivered="{{ (bool)$student->studentProfile->doc_r063->delivered }}"
                    deliveredname="bcApplication[delivered][r063_status]"
                    @endif
          >
          </view-doc>
          --}}

          {{--
          <view-block label="{{__('Education contract')}}">
              <div class="form-group subform" >

                  <view-doc label="{{__('Side 1')}}"
                            name="bcApplication[education_contracts][0]"
                            @if( !empty($student->studentProfile->education_contracts[0]) )
                            status="{{$student->studentProfile->education_contracts[0]->status}}"
                            file-name="{{ $student->studentProfile->education_contracts[0]->getPublicFileName() }}"
                            delivered="{{ (bool)$student->studentProfile->education_contracts[0]->delivered }}"
                            deliveredname="bcApplication[delivered][education_contracts][0]"
                          @endif
                  >
                  </view-doc>

                  <view-doc label="{{__('Side 2')}}"
                            name="bcApplication[education_contracts][1]"
                            @if( !empty($student->studentProfile->education_contracts[1]) )
                            status="{{$student->studentProfile->education_contracts[1]->status}}"
                            file-name="{{ $student->studentProfile->education_contracts[1]->getPublicFileName() }}"
                            delivered="{{ (bool)$student->studentProfile->education_contracts[1]->delivered }}"
                            deliveredname="bcApplication[delivered][education_contracts][1]"
                          @endif
                  >
                  </view-doc>

                  <view-doc label="{{__('Side 3')}}"
                            name="bcApplication[education_contracts][2]"
                            @if( !empty($student->studentProfile->education_contracts[2]) )
                            status="{{$student->studentProfile->education_contracts[2]->status}}"
                            file-name="{{ $student->studentProfile->education_contracts[2]->getPublicFileName() }}"
                            delivered="{{ (bool)$student->studentProfile->education_contracts[2]->delivered }}"
                            deliveredname="bcApplication[delivered][education_contracts][2]"
                          @endif
                  >
                  </view-doc>

                  <view-doc label="{{__('Side 4')}}"
                            name="bcApplication[education_contracts][3]"
                            @if( !empty($student->studentProfile->education_contracts[3]))
                            status="{{$student->studentProfile->education_contracts[3]->status}}"
                            file-name="{{ $student->studentProfile->education_contracts[3]->getPublicFileName() }}"
                            delivered="{{ (bool)$student->studentProfile->education_contracts[3]->delivered }}"
                            deliveredname="bcApplication[delivered][education_contracts][3]"
                          @endif
                  >
                  </view-doc>

                  <view-doc label="{{__('Side 5')}}"
                            name="bcApplication[education_contracts][4]"
                            @if( !empty($student->studentProfile->education_contracts[4]) )
                            status="{{$student->studentProfile->education_contracts[4]->status}}"
                            file-name="{{ $student->studentProfile->education_contracts[4]->getPublicFileName() }}"
                            delivered="{{ (bool)$student->studentProfile->education_contracts[4]->delivered }}"
                            deliveredname="bcApplication[delivered][education_contracts][4]"
                          @endif
                  >
                  </view-doc>

              </div>
          </view-block>

          <view-doc label="{{__('Education statement')}}"
                    name="bcApplication[education_statement]"
                    @if($student->studentProfile->education_statement)
                    status="{{$student->studentProfile->education_statement->status}}"
                    file-name="{{ $student->studentProfile->education_statement->getPublicFileName() }}"
                    delivered="{{ (bool)$student->studentProfile->education_statement->delivered }}"
                    deliveredname="bcApplication[delivered][education_statement]"
                  @endif
          >
          </view-doc>

          <view-doc label="{{__('Документ')}}"
                      name="bcApplication[nostrification_status]"
                      @if($student->studentProfile->doc_nostrification)
                      status="{{$student->studentProfile->doc_nostrification->status}}"
                      file-name="{{$student->studentProfile->doc_nostrification->getPublicFileName()}}"
                      delivered="{{ (bool)$student->studentProfile->doc_nostrification->delivered === 1 }}"
                      deliveredname="bcApplication[delivered][nostrification_status]"
                      @endif
            ></view-doc>
            <view-doc label="{{__('Документ')}}"
                      name="bcApplication[con_confirm]"
                      @if($student->studentProfile->doc_con_confirm)
                      status="{{$student->studentProfile->doc_con_confirm->status}}"
                      file-name="{{$student->studentProfile->doc_con_confirm->getPublicFileName()}}"
                      delivered="{{ (bool)$student->studentProfile->doc_con_confirm->delivered === 1 }}"
                      deliveredname="bcApplication[delivered][con_confirm]"
                    @endif
            ></view-doc>

          --}}

        </div>
    </div>


@if($student->bcApplication->ikt || true)
    <view-block label="{{__('ENT certificate data')}}">
        <div class="form-group subform" >
            @include('admin.pages.students.bc_application.ent')
        </div>
    </view-block>
@else
    <div class="col-md-12 form-group">
        <div class="col-md-2 "><label class="pull-right">{{__('ENT certificate data')}}</label></div>
        <div class="col-md-10">
            <span class="alert alert-warning">данные не предоставлены</span>
        </div>
    </div>
@endif

<view-block label="{{__('Education')}}">
    <div class="form-group subform" >
        @include('admin.pages.students.bc_application.education')
    </div>
</view-block>

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