<?php
/**
 * @var App\StudentSubmodule $submodule
 * @var array $allowedGroupsToBuy
 */
?>

@if($submodule->chooseAvailable)
    <div class="card panel-{{$submodule->color}} discipline padding-0">

        {{-- Header --}}
        <div class="card-header panel-heading padding-0" id="heading{{$key}}">
            <h2 class="mb-0">
                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse{{$key}}" aria-expanded="true" aria-controls="collapse{{$key}}">
                    {{$submodule->submodule->name}}
                </button>
            </h2>
        </div>

        {{-- Buy panel --}}
        <div id="collapse{{$key}}" class="collapse" aria-labelledby="heading{{$key}}" data-parent="#accordionExample">
            <div class="card-body">
                <form action="{{route('submodulePay', ['id' => $submodule->submodule_id])}}" method="post">
                    {{csrf_field()}}

                    <input name="submodule_id" value="{{$submodule->submodule->id}}" type="hidden">

                    <div class="form-group">
                        <label for="exampleInputName2">{{__('Level')}}</label>

                        @if ($buyEnabled && $submodule->buyAvailable && !count($submodule->submodule->depWithoutResult))
                            <select name="language_level" class="form-control">
                                @foreach($submodule->submodule->languageLevels as $llId => $languageLevel)
                                    <option value="{{$llId}}">{{$languageLevel}}</option>
                                @endforeach
                            </select>
                        @else
                            <select name="language_level" class="form-control" disabled="disabled"></select>
                        @endif
                    </div>

                    {{-- To pay button --}}
                    @if ($buyEnabled && $submodule->buyAvailable && !count($submodule->submodule->depWithoutResult))
                        <div class="btn-group margin-b10" role="group" aria-label="Button group with nested dropdown">
                            <div class="btn-group" role="group">
                                <button id="btnGroupDrop1" type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    @lang('To pay')
                                </button>
                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                    <button class="dropdown-item" name="buy" type="submit"> @lang('Pay in full') </button>
                                    <button class="dropdown-item" name="buyPartial" type="submit"> @lang('Pay in part') </button>
                                </div>
                            </div>
                        </div>
                    @else
                        <button type="submit" class="btn btn-success" disabled="disabled">@lang('To pay')</button>
                    @endif

                    <div class="clearfix"></div>
                    @if(!\App\Services\Auth::user()->keycloak)
                        <span class="pull-right">{{$submodule->submodule->ects}} {{__("credits")}}</span>
                    @endif

                    @if(!$buyEnabled)
                        <div class="col-12 margin-t20">
                            <p class="bg-warning padding-5 text-white">{{ __('The ability to purchase disciplines is temporarily unavailable') }}.</p>
                        </div>
                    @endif

                    @if(!$discipline->buyAvailable)
                        <div class="col-12 margin-t10">
                            <span>{{ __('Monthly credits limit reached') }}</span>
                        </div>
                    @endif

                    @if(count($submodule->submodule->depWithoutResult) > 0)
                        <div class="col-12 margin-t10">
                            <span>{{ __('For this discipline, you need to complete other disciplines') }}:</span>
                            <ul>
                                @foreach($submodule->submodule->depWithoutResult as $depList)
                                    <li>
                                        @foreach($depList as $i => $dep)
                                            "{{ $dep->name }}" @if($i < count($depList)-1) {{ __('or') }} @endif
                                        @endforeach
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </form>

                {{-- SRO
                @if( $isSROTime && $discipline->SROButtonShow )
                    <a class="btn btn-success margin-t5" href="{{ route('sroGetList',['discipline_id'=>$submodule->id]) }}">@lang('SRO')</a>
                @elseif( $submodule->hasCoursework() )
                    <a class="btn btn-success margin-t5" href="{{route('sroGetList', ['discipline_id' => $submodule->id, 'course' => 1])}}">@lang('SRO')</a>
                @endif
                --}}

            </div>
        </div>
    </div>
@else
    <div class="card discipline padding-0" style="background-color: #e8e8e8;">
        {{-- Header --}}
        <div class="card-header panel-heading padding-0" id="heading{{$key}}">
            <h2 class="mb-0">
                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse{{$key}}" aria-expanded="true" aria-controls="collapse{{$key}}">
                    {{$submodule->submodule->name}}
                </button>
            </h2>
        </div>
    </div>
@endif