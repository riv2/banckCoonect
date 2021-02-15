@extends("admin.admin_app")

@section("title", 'Заявка на скидку')

@section("content")

    <div id="main">
        <div class="page-header">
            <h2>Заявка на скидку</h2>

            <a href="{{ route('adminDiscountRequestsList') }}" class="btn btn-default-light btn-xs"><i class="md md-backspace"></i> Назад</a>

        </div>
        @if(Session::has('flash_message'))
            <div class="alert @if(Session::has('alert-class')) {{Session::get('alert-class')}} @else alert-success @endif">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                {{ Session::get('flash_message') }}
            </div>
        @endif

        <div class="panel panel-default panel-shadow">
            <div class="panel-body" >
                <p><b>Скидки:</b> ({{ $item->category }}), {{ $item->name }}</p>
                
                <p><b>Номер заявки:</b> {{ $item->id }}</p>
                <p><b>Дата подачи:</b> {{ date('d-m-Y',strtotime($item->created_at)) }}</p>
                
                <p><b>Имя студента:</b> {{ $item->fio }}</p>

                <p><b>Дата рождения:</b> {{ $item->bdate }}</p>
                <p><b>ИИН:</b> {{ $item->iin }}</p>
                <p><b>Номер телефона:</b> {{ $item->mobile }}</p>
                <p><b>Пол:</b> {{ $item->sex?"М":"Ж" }}</p>
                <p><b>Тип обучения:</b> {{ __($item->education_study_form) }}</p>
                @if(isset($item->discount))
                    <p><b>Текущая скидка у студента:</b> {{ $item->discount }} %</p>
                @endif
                
                @if(count($images) > 0)
                <div><h3>Приложенные документы:</h3>
                    @foreach($images as $image)
                        <span><a href="{{ $image->filefullpath }}" target="_blank">
                            @if( strtolower( substr( strrchr($image->filefullpath, '.'), 1) ) == 'pdf')
                                <img src="{{ URL::asset('assets/img/pdf-icon.png') }}" width="40" />
                            @else
                                <img src="{{ $image->filefullpath }}" width="100" />
                            @endif
                        </a></span>
                    @endforeach
                </div>
                @endif

                <p>
                    <b>Текущий статус:</b>
                    @if ($item->status == \App\DiscountStudent::STATUS_APPROVED)
                        <span class="label label-success">{{ __($item->status) }}</span>
                    @elseif ($item->status == \App\DiscountStudent::STATUS_DENIED)
                        <span class="label label-danger">{{ __($item->status) }}</span>
                    @elseif ($item->status == \App\DiscountStudent::STATUS_CANCELED)
                        <span class="label label-default">{{ __($item->status) }}</span>
                    @else
                        {{ __($item->status) }}
                    @endif
                </p>

                @if ($item->status == \App\DiscountStudent::STATUS_NEW)
                    <form class="form-horizontal" method="POST" enctype="multipart/form-data" action="{{ route('adminDiscountRequestsSetStatus') }}">
                        {{ csrf_field() }}

                        <input type="hidden" name="discount_id" value="{{ $item->id }}" />

                        <div v-if="!(discountStatus == '{{ app\DiscountStudent::STATUS_DENIED }}')">
                            <div class="form-group">
                                <label for="" class="col-sm-3 control-label">Срок действия скидки</label>
                                <div class="col-sm-3">
                                    <select name="semesters[]" class="form-control show-tick selectpicker" multiple data-live-search="true">
                                        @foreach($semestersList as $semester)
                                            <option value="{{ $semester }}">{{ $semester }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div v-if="!(isCategory && (discountStatus == '{{ app\DiscountStudent::STATUS_DENIED }}'))">
                            <div class="form-group">
                                <label for="" class="col-sm-3 control-label">Коментарий</label>
                                <div class="col-sm-9">
                                    <input type="text" value="{{isset($item->comment)?$item->comment:""}}" id="comment" name="comment" class="form-control" required />
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Размер скидки</label>
                            <div class="col-sm-9">
                                <input type="text" value="{{isset($item->discount)?$item->discount:""}}" id="discountSize" name="discount_custom_size" class="form-control" />
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-12">
                                <label class="text-success">
                                    <input v-model="discountStatus" type="radio" name="status" value="{{ app\DiscountStudent::STATUS_APPROVED }}" @if($item->status == app\DiscountStudent::STATUS_APPROVED) checked @endif />
                                    Подтвердить
                                </label>
                            </div>
                            <div class="col-sm-12">
                                <label class="text-warning">
                                    <input v-model="discountStatus" type="radio" name="status" value="{{ app\DiscountStudent::STATUS_DENIED }}" @if($item->status == app\DiscountStudent::STATUS_DENIED) checked @endif />
                                    Отклонить
                                </label>
                            </div>
                            <div class="col-sm-12">
                                <label class="text-danger">
                                    <input v-model="discountStatus" type="radio" name="status" value="{{ app\DiscountStudent::STATUS_CANCELED }}" @if($item->status == app\DiscountStudent::STATUS_CANCELED) checked @endif />
                                    Отменить
                                </label>
                            </div>
                        </div>


                        <div v-if="isCategory && (discountStatus == '{{ app\DiscountStudent::STATUS_DENIED }}')">
                            <div class="form-group">
                                <label for="" class="col-sm-3 control-label">Причина отклонения</label>
                                <div class="col-sm-9">
                                    <input type="text" value="" name="reason_refusal" class="form-control" required />
                                </div>
                            </div>
                        </div>

                        <button class="btn btn-primary" type="submit">Применить</button>
                    </form>
                @else
                    <p>
                        <b>Действие скидки (семестры): </b>
                        @foreach($item->semesters as $semester)
                            {{ $semester->semester . ($loop->last ? '' : ',') }}
                        @endforeach
                    </p>
                    <p><b>Коментарий</b> {{ $item->comment }}</p>
                    <p><b>Размер скидки</b> {{ $item->discount }}</p>
                @endif
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        var app = new Vue({
            el: '#main',
            data: {
                discountStatus: false,
                isCategory: false,
            },
            created: function(){
                this.discountStatus = '{{ $item->status }}';
                this.isCategory = '{{ !empty($category) ? true : false }}';
            }
        });
    </script>
@endsection
