@extends("admin.admin_app")

@section("content")

    <div id="main">
        <div class="page-header">
            <h2> </h2>

            <a href="{{ URL::to('/rooms') }}" class="btn btn-default-light btn-xs"><i class="md md-backspace"></i> Назад</a>

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
                {!! Form::open(array('url' => array( route('adminRoomEdit', ['id' => isset($room->id) ? $room->id : 'add']) ),'class'=>'form-horizontal padding-15','name'=>'service_form','id'=>'service_form','role'=>'form','enctype' => 'multipart/form-data')) !!}

                <div class="form-group{{ $errors->has('iin') ? ' has-error' : '' }}">
                    <label for="building_id" class="col-md-3 control-label">Здание</label>

                    <div class="col-md-3">

                        <select required class="selectpicker" name="building_id" value="{{ $room->building->id ?? '' }}" data-live-search="true" data-size="5"
                                title="{{ __('Please select') }}" required autofocus>
                            @foreach($buildingList as $building)
                                <option value="{{$building->id}}" @if(isset($room->building->id) && $room->building->id == $building->id) selected @endif>{{$building->name}}</option>
                            @endforeach
                        </select>

                        @if ($errors->has('title'))
                            <span class="help-block">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('iin') ? ' has-error' : '' }}">
                    <label for="type" class="col-md-3 control-label">Тип</label>

                    <div class="col-md-3">

                        <select required class="selectpicker" name="type" value="{{ $room->type ?? '' }}" data-live-search="true" data-size="5"
                                title="{{ __('Please select') }}" required autofocus>
                                <option value="lecture" @if(isset($room->type) && $room->type == 'lecture') selected @endif>Лекционная</option>
                                <option value="computing" @if(isset($room->type) && $room->type == 'computing') selected @endif>Компьютерный класс</option>
                                <option value="laboratory_chemical" @if(isset($room->type) && $room->type == 'laboratory_chemical') selected @endif>Химическая лаборатория</option>
                                <option value="laboratory_bio" @if(isset($room->type) && $room->type == 'laboratory_bio') selected @endif>Биолаборатория</option>
                                <option value="laboratory_physical" @if(isset($room->type) && $room->type == 'laboratory_physical') selected @endif>Физическая лаборатория</option>
                                <option value="sport" @if(isset($room->type) && $room->type == 'sport') selected @endif>Спортзал</option>
                                <option value="multimedia" @if(isset($room->type) && $room->type == 'multimedia') selected @endif>Мультимедия</option>
                        </select>

                        @if ($errors->has('title'))
                            <span class="help-block">
                                        <strong>{{ $errors->first('type') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <label for="number" class="col-sm-3 control-label">Номер</label>
                    <div class="col-sm-3">
                        <input required type="text" name="number" value="{{ $room->number ?? '' }}" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label for="floor" class="col-sm-3 control-label">Этаж</label>
                    <div class="col-sm-3">
                        <input required type="number" name="floor" value="{{ $room->floor ?? '' }}" class="form-control">
                    </div>
                </div>

                <div class="form-group{{ $errors->has('iin') ? ' has-error' : '' }}">
                    <label for="seats_count" class="col-md-3 control-label">Количество мест</label>

                    <div class="col-md-3">

                        <select required class="selectpicker" name="seats_count" value="{{ $room->seats_count ?? '' }}" data-live-search="true" data-size="5"
                                title="{{ __('Please select') }}" required autofocus>
                            <option value="10" @if(isset($room->seats_count) && $room->seats_count == '10') selected @endif>10</option>
                            <option value="20" @if(isset($room->seats_count) && $room->seats_count == '20') selected @endif>20</option>
                            <option value="30" @if(isset($room->seats_count) && $room->seats_count == '30') selected @endif>30</option>
                            <option value="40" @if(isset($room->seats_count) && $room->seats_count == '40') selected @endif>40</option>
                            <option value="45" @if(isset($room->seats_count) && $room->seats_count == '45') selected @endif>45</option>
                        </select>

                        @if ($errors->has('seats_count'))
                            <span class="help-block">
                                <strong>{{ $errors->first('seats_count') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <label for="conditioner" class="col-md-3 control-label">Кондиционер</label>
                    <div class="col-md-3">
                        <input type="radio" name="conditioner" {{ isset($room->conditioner) && $room->conditioner == true ? 'checked' : '' }} value="1">
                        Есть<br />
                        <input type="radio" name="conditioner"{{ isset($room->conditioner) && $room->conditioner == false ? 'checked' : '' }} value="0">
                        Нету
                    </div>
                </div>

                @foreach($stuffList as $i=>$stuff)
                    <div class="field_all form-group">
                        <label for="room[stuff][]" class="col-md-3 control-label">{{__('Room stuff ' . $stuff->name)}}</label>
                        <div class="col-md-3">
                                <input type="radio" name="stuff[{{ $stuff->id }}]" @if(isset($room->stuff) && in_array($stuff->id, $room->stuffIds)) checked @endif value="1">
                                Есть<br/>
                                <input type="radio" name="stuff[{{ $stuff->id }}]" @if((isset($room->stuff) && !in_array($stuff->id, $room->stuffIds)) || !isset($room->stuff) ) checked @endif value="0">
                                Нету
                        </div>
                    </div>
                @endforeach

                <div class="form-group">
                    <label for="description" class="col-sm-3 control-label">Описание</label>
                    <div class="col-sm-5">
                        <textarea name="description" value="{{ $room->description ?? '' }}" class="form-control">{{ $room->description ?? '' }}</textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label for="cost" class="col-sm-3 control-label">Стоимость</label>
                    <div class="col-sm-3">
                        <input required type="number" min="10" name="cost" value="{{ $room->cost ?? '' }}" class="form-control">
                    </div>
                </div>

                <div class="form-group{{ $errors->has('iin') ? ' has-error' : '' }}">
                    <label for="status" class="col-md-3 control-label">Статус</label>

                    <div class="col-md-3">

                        <select required class="selectpicker" name="status" value="{{ $room->status ?? '' }}" data-live-search="true" data-size="5"
                                title="{{ __('Please select') }}" required autofocus>
                            <option value="active" @if(isset($room->status) && $room->status == 'active') selected @endif>Работает</option>
                            <option value="block" @if(isset($room->status) && $room->status == 'block') selected @endif>Не работает</option>
                        </select>

                        @if ($errors->has('seats_count'))
                            <span class="help-block">
                                <strong>{{ $errors->first('status') }}</strong>
                            </span>
                        @endif
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

        <script type="text/javascript">
            $("#serviceType").change(function(){
                var placesList = $('#placesList');

                if( $(this).find('select').val() == 'Master' ) placesList.show(150);
                else placesList.hide(150);
            });

        </script>

@endsection