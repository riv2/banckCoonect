@extends("admin.admin_app")

@section("content")
    <div id="check-list-edit">
        <div class="page-header">
            <h2> {{  !empty($isEdit) ? 'Редактировать' : 'Добавить' }} ПЛ</h2>
            <a href="{{ route('adminCheckListList') }}" class="btn btn-default-light btn-xs"><i class="md md-backspace"></i> Назад</a>
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


                    <div v-if="errorMessage" :class="{ 'alert-danger': isError, 'alert-success': !isError }" class="alert margin-t20 margin-b20">
                        <div v-html="errorMessage"> </div>
                    </div>


                    <form action="{{ route('adminCheckListEditPost') }}" method="post" enctype="multipart/form-data">
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
                            <label class="col-md-3 control-label"> Образовательная программа </label>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <select class="form-control" name="speciality_id">
                                        @if( !empty($specialityList) )
                                            @foreach($specialityList as $specId => $specialityName)
                                                <option value="{{ $specId }}" @if( !empty($model->speciality_id) && ($model->speciality_id == $specId) ) selected @endif> {{ $specialityName }} </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>


                        <div class="form-group margin-15">
                            <label class="col-md-3 control-label"> Базовое образование </label>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <select class="form-control" name="basic_education">
                                        @if( !empty($basicEducation) )
                                            @foreach($basicEducation as $basicItem)
                                                <option value="{{ $basicItem }}" @if( !empty($model->basic_education) && ($model->basic_education == $basicItem) ) selected @endif> {{ __($basicItem) }} </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>


                        <div class="form-group margin-15">
                            <label class="col-md-3 control-label"> Гражданство </label>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <select class="form-control" name="citizenship">
                                        @if( !empty($citizenshipList) )
                                            @foreach($citizenshipList as $citizenshipItem)
                                                <option value="{{ $citizenshipItem }}" @if( !empty($model->citizenship) && ($model->citizenship == $citizenshipItem) ) selected @endif> {{ __($citizenshipItem) }} </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>


                        <div class="form-group margin-15">
                            <label class="col-md-3 control-label"> Уровень образования </label>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <select class="form-control" name="education_level">
                                        @if( !empty($educationLevel) )
                                            @foreach($educationLevel as $educationLevelItem)
                                                <option value="{{ $educationLevelItem }}" @if( !empty($model->education_level) && ($model->education_level == $educationLevelItem) ) selected @endif> {{ __($educationLevelItem) }} </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>


                        <div class="form-group margin-15">
                            <label class="col-md-3 control-label"> ЕНТ </label>
                            <div class="col-md-5">
                                <div class="col-md-12 subform">
                                    <div class="form-check">
                                        <input onchange="changeChecked('ent_checked')" class="form-check-input" type="checkbox" id="ent_checked" name="ent_checked" value="{{ $model->ent_checked ?? 1 }}" @if(!empty($model->ent_checked)) checked="checked" @endif />
                                        <label class="form-check-label" for="ent_checked">
                                            Статус соответствия
                                        </label>
                                    </div>
                                    <div class="form-check margin-b10">
                                        <input onchange="changeChecked('ent_is_sum')" class="form-check-input" type="checkbox" id="ent_is_sum" name="ent_is_sum" value="{{ $model->ent_is_sum ?? 1 }}" @if(!empty($model->ent_is_sum)) checked="checked" @endif />
                                        <label class="form-check-label" for="ent_is_sum">
                                            Суммирование баллов
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>


                        <div class="form-group margin-15">
                            <label class="col-md-3 control-label"> Документы </label>
                            <div class="col-md-5">
                                <div class="col-md-12 subform">
                                    <div class="form-check">
                                        <input onchange="changeChecked('documents_checked')" class="form-check-input" type="checkbox" id="documents_checked" name="documents_checked" value="{{ $model->documents_checked ?? 1 }}" @if(!empty($model->documents_checked)) checked="checked" @endif />
                                        <label class="form-check-label" for="documents_checked">
                                            Статус соответствия
                                        </label>
                                    </div>
                                    <div class="form-check margin-b10">
                                        <input onchange="changeChecked('documents_is_sum')" class="form-check-input" type="checkbox" id="documents_is_sum" name="documents_is_sum" value="{{ $model->documents_is_sum ?? 1 }}" @if(!empty($model->documents_is_sum)) checked="checked" @endif />
                                        <label class="form-check-label" for="documents_is_sum">
                                            Суммирование баллов
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>


                        <div class="form-group margin-15">
                            <label class="col-md-3 control-label"> Пререквизиты </label>
                            <div class="col-md-5">
                                <div class="col-md-12 subform">
                                    <div class="form-check">
                                        <input onchange="changeChecked('prerequisites_checked')" class="form-check-input" type="checkbox" id="prerequisites_checked" name="prerequisites_checked" value="{{ $model->prerequisites_checked ?? 1 }}" @if(!empty($model->prerequisites_checked)) checked="checked" @endif />
                                        <label class="form-check-label" for="prerequisites_checked">
                                            Статус соответствия
                                        </label>
                                    </div>
                                    <div class="form-check margin-b10">
                                        <input onchange="changeChecked('prerequisites_is_sum')" class="form-check-input" type="checkbox" id="prerequisites_is_sum" name="prerequisites_is_sum" value="{{ $model->prerequisites_is_sum ?? 1 }}" @if(!empty($model->prerequisites_is_sum)) checked="checked" @endif />
                                        <label class="form-check-label" for="prerequisites_is_sum">
                                            Суммирование баллов
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>


                        <div class="form-group margin-15">
                            <label class="col-md-3 control-label"> Собеседование </label>
                            <div class="col-md-5">
                                <div class="col-md-12 subform">
                                    <div class="form-check">
                                        <input onchange="changeChecked('interview_checked')" class="form-check-input" type="checkbox" id="interview_checked" name="interview_checked" value="{{ $model->interview_checked ?? 1 }}" @if(!empty($model->interview_checked)) checked="checked" @endif />
                                        <label class="form-check-label" for="interview_checked">
                                            Статус соответствия
                                        </label>
                                    </div>
                                    <div class="form-check margin-b10">
                                        <input onchange="changeChecked('interview_is_sum')" class="form-check-input" type="checkbox" id="interview_is_sum" name="interview_is_sum" value="{{ $model->interview_is_sum ?? 1 }}" @if(!empty($model->interview_is_sum)) checked="checked" @endif />
                                        <label class="form-check-label" for="interview_is_sum">
                                            Суммирование баллов
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>


                        <div class="form-group margin-15">
                            <label class="col-md-3 control-label"> Общая сумма баллов </label>
                            <div class="col-md-5">
                                <div class="col-md-12 subform">
                                    <div class="form-check">
                                        <input onchange="changeChecked('total_point_checked')" class="form-check-input" type="checkbox" id="total_point_checked" name="total_point_checked" value="{{ $model->total_point_checked ?? 1 }}" @if(!empty($model->total_point_checked)) checked="checked" @endif />
                                        <label class="form-check-label" for="total_point_checked">
                                            Суммировать баллы
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>


                        <div class="form-group margin-15">
                            <div class="col-md-10">
                                <button @click="checkListShowSpecialityModal" class="btn btn-primary" type="button"> Добавить ОП </button>
                            </div>
                        </div>
                        <div class="clearfix"></div>


                        <div id="entrance_exam_block">

                            @if( !empty($entranceExamList) && (count($entranceExamList) > 0) )
                                @foreach( $entranceExamList as $itemEEL )

                                    @include('admin.pages.check_list.entranceExamItem', [
                                        'model'      => $itemEEL,
                                        'yearsList'  => $yearsList,
                                        'loadEE'     => $loadEE
                                    ])

                                @endforeach
                            @endif

                        </div>

                        <br>
                        <br>
                        <br>

                        <div class="form-group margin-tb15">
                            <div class="col-md-4">
                                @if(\App\Services\Auth::user()->hasRight('test_pc_pl','edit'))
                                <button class="btn btn-info" type="submit"> Сохранить </button>
                                @endif
                            </div>
                        </div>

                    </form>


                    <!-- modal -->
                    <div id="specialityModal" class="modal" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title pull-left"> Добавить ОП </h5>
                                    <button @click="checkListHideSpecialityModal" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="row padding-20">

                                        <div class="form-group">
                                            <label class="col-12 control-label"> Выбрать год </label>
                                            <div class="col-12">
                                                <select @change="checkListGetEntranceExamList" v-model="checkListCurrentYear" class="form-control" name="year">
                                                    @if( !empty($yearsList) )
                                                        @foreach($yearsList as $year)
                                                            <option value="{{ $year }}"> {{ $year }} </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-12 control-label"> Выбрать ВИ </label>
                                            <div class="col-12">
                                                <select v-model="checkListSelectedAttachEntranceExam" class="form-control">
                                                    <option v-for="(itemSL, index) in checkListEntranceExamList" :value="itemSL.id" :key="itemSL.id"> @{{ itemSL.name }} </option>
                                                </select>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="modal-footer">
                                    @if(\App\Services\Auth::user()->hasRight('test_pc_pl','edit'))
                                    <button @click="checkListAttachEntranceExam" class="btn btn-primary"> Добавить </button>
                                    @endif
                                    <button @click="checkListHideSpecialityModal" type="button" class="btn btn-info" data-dismiss="modal"> {{ __('Close') }} </button>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script type="text/javascript">

        var app = new Vue({
            el: '#check-list-edit',
            data: {

                isError: false,
                errorMessage: '',
                checkListEntranceExamList: [],
                checkListSelectedEntranceExam: '',
                checkListCurrentYear: '{{ date('Y') }}',
                checkListSelectedAttachEntranceExam: ''

            },
            methods: {

                checkListShowSpecialityModal: function(){
                    $('#specialityModal').addClass('show');
                },
                checkListHideSpecialityModal: function(){
                    $('#specialityModal').removeClass('show');
                },
                checkListGetEntranceExamList: function(){

                    var self = this;
                    axios.post('{{ route('adminCheckListGetEntranceExamList') }}',{
                        "_token": "{{ csrf_token() }}",
                        "year": self.checkListCurrentYear
                    }).then(function(response){

                        if( response.data.status ){

                            self.checkListEntranceExamList = response.data.models;
                        }

                    }).catch( error => {

                        console.log(error)
                    });

                },
                checkListRenderEntranceExam: function(id){

                    var self = this;
                    axios.post('{{ route('adminCheckListRenderEntranceExamItem') }}',{
                        "_token": "{{ csrf_token() }}",
                        "id": id
                    }).then(function(response){

                        if( response.data ){

                            $('#entrance_exam_block').append( response.data +
                                '<div class="col-md-1" onclick="entranceItemRemove(this,'+id+')"><a class="btn btn-danger">Удалить <i class="fa fa-remove"></i></a></div><div class="clearfix"></div>');
                        }

                    }).catch( error => {

                        console.log(error)
                    });

                },
                checkListAttachEntranceExam: function(){

                    if( this.checkListSelectedAttachEntranceExam ){

                        this.checkListRenderEntranceExam( this.checkListSelectedAttachEntranceExam );
                    }
                    this.checkListHideSpecialityModal();
                }


            },
            created: function(){

                this.checkListGetEntranceExamList();
            }
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

        function addManualFile(id,name)
        {
            $('#'+id).append('<div class="col-md-8 form-group padding-5">\n' +
                '<input type="file" name="'+name+'" value="" />' +
                '</div><div class="col-md-1" onclick="deleteFile(this,null)"><a class="btn btn-default"><i class="fa fa-remove"></i></a></div>');
        }

        function addStatementFile(id,name)
        {
            $('#'+id).append('<div class="col-md-8 form-group padding-5">\n' +
                '<input type="file" name="'+name+'" value="" />' +
                '</div><div class="col-md-1" onclick="deleteFile(this,null)"><a class="btn btn-default"><i class="fa fa-remove"></i></a></div>');
        }

        function addCommissionStructureFile(id,name)
        {
            $('#'+id).append('<div class="col-md-8 form-group padding-5">\n' +
                '<input type="file" name="'+name+'" value="" />' +
                '</div><div class="col-md-1" onclick="deleteFile(this,null)"><a class="btn btn-default"><i class="fa fa-remove"></i></a></div>');
        }

        function addCompositionAppealCommissionFile(id,name)
        {
            $('#'+id).append('<div class="col-md-8 form-group padding-5">\n' +
                '<input type="file" name="'+name+'" value="" />' +
                '</div><div class="col-md-1" onclick="deleteFile(this,null)"><a class="btn btn-default"><i class="fa fa-remove"></i></a></div>');
        }

        function addScheduleFile(id,name)
        {
            $('#'+id).append('<div class="col-md-8 form-group padding-5">\n' +
                '<input type="file" name="'+name+'" value="" />' +
                '</div><div class="col-md-1" onclick="deleteFile(this,null)"><a class="btn btn-default"><i class="fa fa-remove"></i></a></div>');
        }

        function addProtocolsCreativeExamsFile(id,name)
        {
            $('#'+id).append('<div class="col-md-8 form-group padding-5">\n' +
                '<input type="file" name="'+name+'" value="" />' +
                '</div><div class="col-md-1" onclick="deleteFile(this,null)"><a class="btn btn-default"><i class="fa fa-remove"></i></a></div>');
        }

        function addProtocolsAppealCommissionFile(id,name)
        {
            $('#'+id).append('<div class="col-md-8 form-group padding-5">\n' +
                '<input type="file" name="'+name+'" value="" />' +
                '</div><div class="col-md-1" onclick="deleteFile(this,null)"><a class="btn btn-default"><i class="fa fa-remove"></i></a></div>');
        }

        function addReportExamsFile(id,name)
        {
            $('#'+id).append('<div class="col-md-8 form-group padding-5">\n' +
                '<input type="file" name="'+name+'" value="" />' +
                '</div><div class="col-md-1" onclick="deleteFile(this,null)"><a class="btn btn-default"><i class="fa fa-remove"></i></a></div>');
        }

        function deleteFile(elem,id)
        {
            $(elem).prev().remove();
            $(elem).after('<input type="hidden" value="'+id+'" name="removeFiles[]">');
            $(elem).remove();
        }

        function entranceItemRemove(elem,id){
            $(elem).prev().remove();
            $(elem).after('<input type="hidden" value="'+id+'" name="removeEntranceExam[]">');
            $(elem).remove();
        }

        function switchBtn(elem,id,name)
        {
            var value = $('#'+name+'_input'+id).val();

            if( parseInt(value) == 1 ){
                $(elem).removeClass('switch-on-color');
                $('#'+name+'_input'+id).prop('value',0);
                $('#'+name+'_files'+id).addClass('hide');

            } else {
                $(elem).addClass('switch-on-color');
                $('#'+name+'_input'+id).prop('value',1);
                $('#'+name+'_files'+id).removeClass('hide');
            }
        }

    </script>
@endsection
