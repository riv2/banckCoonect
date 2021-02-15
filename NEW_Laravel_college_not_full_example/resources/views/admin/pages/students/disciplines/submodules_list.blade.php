<div class="panel panel-default panel-shadow">
    <div class="panel-body">
        <table id="data-table-submodules" class="table table-striped table-hover dt-responsive" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th>Наименование</th>
                <th>Кредиты</th>
            </tr>
            </thead>

            <tbody>
            @foreach($submodules as $i => $submodule)
                <tr>
                    <td class="td-extendable">
                        @if(\App\Services\Auth::user()->hasRight('or_cabinet','edit') && ($maxCreditsAllowed == -1 || $maxCreditsAllowed != 0))
                            <div class="td-extendable-button"><i class="plusIcon">+</i> {{ $submodule->name }}</div>

                            @if(in_array(\App\Services\Auth::user()->id, [96, 18365, 15546, 4876]))
                                <div class="after-extend" style="display: none;">
                                    <div class="col-md-2">
                                        <select name="language_level" class="form-control">
                                            <?php $submodule->submodule->setAvailableLanguageLevels() ?>
                                            @foreach($submodule->submodule->languageLevels as $llId => $languageLevel)
                                                <option value="{{$llId}}">{{$languageLevel}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-2">
                                        <input class="form-control" type="text" name="points-{{$submodule->id}}">
                                    </div>

                                    <span id="add-submodule-discipline-{{$submodule->id}}" class="btn btn-success add-submodule-discipline" ects={{$submodule->ects}} submoduleId={{$submodule->id}}>Засчитать дисциплину</span>
                                    <span id="add-pay-req-submodule-discipline-{{$submodule->id}}" class="btn btn-info add-pay-req-submodule-discipline" ects={{$submodule->ects}} submoduleId={{$submodule->id}}>Засчитать как платную</span>
                                </div>
                            @endif
                        @else
                            <div class="td-extendable-button">{{$submodule->name}}</div>
                        @endif
                    </td>
                    <td>{{$submodule->ects}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="clearfix"></div>
</div>
