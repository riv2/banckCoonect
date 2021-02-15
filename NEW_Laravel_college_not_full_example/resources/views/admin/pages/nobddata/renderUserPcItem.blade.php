<div class="panel panel-default">
    <div class="panel-body">
        <div class="col-md-12 form-group">
            <div class="col-md-6">
                <label class="pull-right text-right"> Вид мероприятия </label>
            </div>
            <input type="hidden" value="0" name="nobdUser[nobdUserPc][{{ $count }}][id]" />
            <div class="col-md-6">
                <select class="form-control" name="nobdUser[nobdUserPc][{{ $count }}][type_event]">
                    <option> ... </option>
                    @if( !empty($typeEvent) )
                        @foreach( $typeEvent as $itemTE )
                            <option value="{{ $itemTE->id }}"> {{ $itemTE->name }} </option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
        <div class="col-md-12 form-group">
            <div class="col-md-6">
                <label class="pull-right text-right"> Вид направления </label>
            </div>
            <div class="col-md-6">
                <select class="form-control" name="nobdUser[nobdUserPc][{{ $count }}][type_direction]">
                    <option> ... </option>
                    @if( !empty($typeDirection) )
                        @foreach( $typeDirection as $itemTD )
                            <option value="{{ $itemTD->id }}"> {{ $itemTD->name }} </option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
        <div class="col-md-12 form-group">
            <div class="col-md-6">
                <label class="pull-right text-right"> Уровень мероприятия </label>
            </div>
            <div class="col-md-6">
                <select class="form-control" name="nobdUser[nobdUserPc][{{ $count }}][events]">
                    <option> ... </option>
                    @if( !empty($events) )
                        @foreach( $events as $itemE )
                            <option value="{{ $itemE->id }}"> {{ $itemE->name }} </option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
        <div class="col-md-12 form-group">
            <div class="col-md-6">
                <label class="pull-right text-right"> Дата участия </label>
            </div>
            <div class="col-md-6">
                <input value="" class="form-control" name="nobdUser[nobdUserPc][{{ $count }}][date_participation]" type="date" />
            </div>
        </div>
        <div class="col-md-12 form-group">
            <div class="col-md-6">
                <label class="pull-right text-right"> Награда </label>
            </div>
            <div class="col-md-6">
                <select class="form-control" name="nobdUser[nobdUserPc][{{ $count }}][reward]">
                    <option> ... </option>
                    @if( !empty($reward) )
                        @foreach( $reward as $itemR )
                            <option value="{{ $itemR->id }}"> {{ $itemR->name }} </option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
    </div>
</div>
<div class="pull-right" onclick="deleteNobdUserPc(this,0)"><a class="btn btn-default"><i class="fa fa-remove"></i></a></div><br><br>