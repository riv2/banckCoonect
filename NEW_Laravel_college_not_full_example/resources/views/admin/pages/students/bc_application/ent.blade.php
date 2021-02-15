<?php
$hasRightEdit = \App\Services\Auth::user()->hasRight('students', 'edit')
?>



    <div class="form-group cities">
        <div class="col-md-2 ">
            <label class="pull-right text-right">{{__('IKT number')}}</label>
        </div>
        <div class="col-md-4 ">
            <input @if(!$hasRightEdit) disabled @endif id="ikt" v-model="ikt" type="text" class="form-control autocomplete" name="bcApplication[ikt]" value="{{ $student->bcApplication->ikt }}" autofocus>
        </div>
        <div class="col-md-6">
            <button class="btn btn-primary" v-bind:class="" v-on:click="refreshEnt()" v-bind:disabled="refreshEntProcess"><i class="fa fa-refresh"></i></button>
            @if($hasRightEdit)
            <button class="btn btn-primary" v-on:click="changeEnt" type="button">Изменить</button>
            @endif
        </div>
    </div>


<div v-show="changeEntFlag">
    <input type="hidden" name="isChangeEnt" v-model="isChangeEnt" />
    <div class="col-md-12 form-group">
        <div class="col-md-4">
            <select name="ent_name_1" class="form-control">
                @if( !empty($disciplineList) )
                    @foreach($disciplineList as $disItem)
                        @if( $disItem->id == 14 )
                            <option value="{{ $disItem->name }}">{{ $disItem->name }}</option>
                        @endif
                    @endforeach
                @endif
            </select>
        </div>
        <div class="col-md-2">
            <input @if(!$hasRightEdit) disabled @endif class="form-control" type="text" name="ent_val_1" value="" autocomplete="false" />
        </div>
    </div>
    <div class="col-md-12 form-group">
        <div class="col-md-4">
            <select name="ent_name_2" class="form-control">
                @if( !empty($disciplineList) )
                    @foreach($disciplineList as $disItem)
                        @if( $disItem->id == 15 )
                            <option value="{{ $disItem->name }}">{{ $disItem->name }}</option>
                        @endif
                    @endforeach
                @endif
            </select>
        </div>
        <div class="col-md-2">
            <input @if(!$hasRightEdit) disabled @endif class="form-control" type="text" name="ent_val_2" value="" autocomplete="false" />
        </div>
    </div>
    <div class="col-md-12 form-group">
        <div class="col-md-4">
            <select name="ent_name_3" class="form-control">
                @if( !empty($disciplineList) )
                    @foreach($disciplineList as $disItem)
                        <option value="{{ $disItem->name }}">{{ $disItem->name }}</option>
                    @endforeach
                @endif
            </select>
        </div>
        <div class="col-md-2">
            <input @if(!$hasRightEdit) disabled @endif class="form-control" type="text" name="ent_val_3" value="" autocomplete="false" />
        </div>
    </div>
    <div class="col-md-12 form-group">
        <div class="col-md-4">
            <select name="ent_name_4" class="form-control">
                @if( !empty($disciplineList) )
                    @foreach($disciplineList as $disItem)
                        <option value="{{ $disItem->name }}">{{ $disItem->name }}</option>
                    @endforeach
                @endif
            </select>
        </div>
        <div class="col-md-2">
            <input @if(!$hasRightEdit) disabled @endif class="form-control" type="text" name="ent_val_4" value="" autocomplete="false" />
        </div>
    </div>
    <div class="col-md-12 form-group">
        <div class="col-md-4">
            <select name="ent_name_5" class="form-control">
                @if( !empty($disciplineList) )
                    @foreach($disciplineList as $disItem)
                        <option value="{{ $disItem->name }}">{{ $disItem->name }}</option>
                    @endforeach
                @endif
            </select>
        </div>
        <div class="col-md-2">
            <input @if(!$hasRightEdit) disabled @endif class="form-control" type="text" name="ent_val_5" value="" autocomplete="false" />
        </div>
    </div>
</div>

<view-block label="Результаты">
    <div class="subform">

        <div v-if="refreshEntData" v-for="(entElem, i) in refreshEntData" class="col-md-12">
            @{{ entElem.subjectNameRu }}: @{{ entElem.ball }}
            <input type="hidden" v-bind:name="'bcApplication[ent_name_' + (i + 1) + ']'" v-bind:value="entElem.subjectNameRu" />
            <input type="hidden" v-bind:name="'bcApplication[ent_val_' + (i + 1) + ']'" v-bind:value="entElem.ball" />
        </div>
        <div v-if="refreshEntData" class="col-md-12">
            Общий: @{{ entBallTotal }}
            <input type="hidden" v-bind:name="'bcApplication[ent_total]'" v-bind:value="entBallTotal" />
        </div>
        <input type="hidden" name="bcApplication[refresh_ent]" v-if="refreshEntData" value="1" />

        <div v-if="refreshEntData === null">
        @if($student->bcApplication->ent_name_1)
            <div class="col-md-12">
                {{$student->bcApplication->ent_name_1}}:&nbsp;{{$student->bcApplication->ent_val_1}}
            </div>
        @endif
        @if($student->bcApplication->ent_name_2)
            <div class="col-md-12">
                {{$student->bcApplication->ent_name_2}}:&nbsp;{{$student->bcApplication->ent_val_2}}
            </div>
        @endif
        @if($student->bcApplication->ent_name_3)
            <div class="col-md-12">
                {{$student->bcApplication->ent_name_3}}:&nbsp;{{$student->bcApplication->ent_val_3}}
            </div>
        @endif
        @if($student->bcApplication->ent_name_4)
            <div class="col-md-12">
                {{$student->bcApplication->ent_name_4}}:&nbsp;{{$student->bcApplication->ent_val_4}}
            </div>
        @endif
        @if($student->bcApplication->ent_name_5)
            <div class="col-md-12">
                {{$student->bcApplication->ent_name_5}}:&nbsp;{{$student->bcApplication->ent_val_5}}
            </div>
        @endif
        @if($student->bcApplication->ent_total)
            <div class="col-md-12">
                Общий:&nbsp;{{$student->bcApplication->ent_total}}
            </div>
        @endif
        </div>
    </div>
</view-block>
