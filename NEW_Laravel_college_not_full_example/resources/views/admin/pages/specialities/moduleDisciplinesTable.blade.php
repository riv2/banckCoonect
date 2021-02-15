<?php
/**
 * @var $speciality \App\Speciality
 * @var $module \App\Module
 */

$hasEditRight = \App\Services\Auth::user()->hasRight('specialities', 'edit');

?>

<table class="table table-striped table-hover dt-responsive">
    <thead>
    <tr>
        <th class="text-center" v-show="selectedModules.indexOf({{$module->id}}) > -1">КГЭ</th>
        <th>Наименование</th>
        <th>Семестр</th>
        <th>Шифр</th>
        <th>Кредиты</th>
        <th>Наличие курсовой работы</th>
        <th>Цикл</th>
        <th>Тип</th>
        <th v-show="selectedModules.indexOf({{$module->id}}) > -1">Язык</th>
    </tr>
    </thead>

    <tbody>

    @php
        foreach ($module->disciplines as $discipline):
            if (class_basename($discipline) == 'Discipline') {
                $semester = $speciality->getDisciplineSemester($discipline->id);
                $cycle = $speciality->getDisciplineDisciplineCicle($discipline->id);
                $mtTk = $speciality->getDisciplineMtTk($discipline->id);
                $langType = $speciality->getDisciplineLangType($discipline->id);
                $pressmark = $speciality->getDisciplinePressmark($discipline->id, 'OK');
            }
            elseif (class_basename($discipline) == 'Submodule') {
                $semester = $speciality->getSubmoduleSemester($discipline->id);
                $cycle = $speciality->getSubmoduleDisciplineCycle($discipline->id);
                $mtTk = $speciality->getSubmoduleMtTk($discipline->id);
                $langType = $speciality->getSubmoduleLangType($discipline->id);
                $pressmark = $speciality->getSubmodulePressmark($discipline->id, 'OK');
            }
    @endphp

    <tr>
        @if (class_basename($discipline) == 'Discipline')
            <td class="text-center" id="check-exam-{{ $discipline->id }}" v-show="selectedModules.indexOf({{$module->id}}) > -1">
                <input type="checkbox" @if(!$hasEditRight) disabled @endif @if($speciality->getDisciplineExam($discipline->id, 0)) checked @endif name="" id="exam-{{$discipline->id}}" onchange="changeDisciplineListExam(this, {{ $discipline->id }})"/>
            </td>

            <td>
                <a href="{{ route('disciplineEdit',['id' => $discipline->id]) }}" target="_blank"> {{ $discipline->name }} </a>
            </td>

            <td id="pressmark-semester-{{ $discipline->id }}" v-show="selectedModules.indexOf({{$module->id}}) > -1">
                <select name="" @if(!$hasEditRight) disabled @endif class="form-control" onchange="changeDisciplineSemester(this, {{ $discipline->id }})">
                    @for ($i = 1; $i < 11; $i++)
                        <option value="{{$i}}" @if($semester == $i) selected @endif>{{$i}}</option>
                    @endfor
                </select>
            </td>

            <td class="text-center" id="pressmark-pressmark-{{ $discipline->id }}" v-show="selectedModules.indexOf({{$module->id}}) > -1">
                <input type="text" @if(!$hasEditRight) disabled @endif class="form-control" value="{{$pressmark}}" name="" id="pressmark-{{$discipline->id}}" onchange="changeDisciplinePressmark(this, {{ $discipline->id }})"/>
            </td>

            <td>{{ $discipline->ects }}&nbsp;<sub>ECTS</sub></td>

            <td class="text-center" id="check-has-coursework-{{ $discipline->id }}" v-show="selectedModules.indexOf({{$module->id}}) > -1">
                <input type="checkbox" @if(!$hasEditRight) disabled @endif @if($speciality->getDisciplineHasCoursework($discipline->id, 0)) checked @endif name="" id="has-coursework-{{$discipline->id}}" onchange="changeDisciplineHasCoursework(this, {{ $discipline->id }})"/>
            </td>


            <td id="discipline-cicle-{{ $discipline->id }}" v-show="selectedModules.indexOf({{$module->id}}) > -1">
                <select name="" @if(!$hasEditRight) disabled @endif class="form-control" onchange="changeDisciplineDisciplineCicle(this, {{ $discipline->id }})">
                    @foreach($disciplineCycles as $disciplineCycle)
                        <option value="{{$disciplineCycle}}" @if($cycle == $disciplineCycle) selected @endif>{{$disciplineCycle}}</option>
                    @endforeach
                </select>
            </td>

            <td id="mt-tk-{{ $discipline->id }}" v-show="selectedModules.indexOf({{$module->id}}) > -1">
                <select name="" @if(!$hasEditRight) disabled @endif class="form-control" onchange="changeDisciplineMtTk(this, {{ $discipline->id }})">
                    @foreach($mtTks as $mtTkItem)
                        <option value="{{$mtTkItem}}" @if($mtTk == $mtTkItem) selected @endif>{{$mtTkItem}}</option>
                    @endforeach
                </select>
            </td>

            <td id="discipline-lang-{{ $discipline->id }}" v-show="selectedModules.indexOf({{$module->id}}) > -1">
                <select name="" @if(!$hasEditRight) disabled @endif class="form-control" onchange="changeDisciplineLangType(this, {{ $discipline->id }})">
                    @foreach($languageTypes as $languageTypeKey => $languageType)
                        <option value="{{$languageTypeKey}}" @if($langType == $languageTypeKey) selected @endif>{{$languageType}}</option>
                    @endforeach
                </select>
            </td>
        @else
            <td></td>

            <td>{{ $discipline->name }}</td>

            <td id="pressmark-semester-submodule-{{ $discipline->id }}">
                <select name="" @if(!$hasEditRight) disabled @endif class="form-control" onchange="changeSubmoduleSemester(this, {{ $discipline->id }})">
                    @for ($i = 1; $i < 11; $i++)
                        <option value="{{$i}}" @if($semester == $i) selected @endif>{{$i}}</option>
                    @endfor
                </select>
            </td>

            <td class="text-center" id="pressmark-pressmark-submodule-{{ $discipline->id }}">
                <input type="text" @if(!$hasEditRight) disabled @endif class="form-control" value="{{$pressmark}}" name="" id="pressmark-submodule-{{$discipline->id}}" onchange="changeSubmodulePressmark(this, {{ $discipline->id }})"/>
            </td>

            <td>{{ $discipline->ects }}&nbsp;<sub>ECTS</sub></td>

            <td></td>

            <td id="discipline-cicle-submodule-{{ $discipline->id }}">
                <select name="" @if(!$hasEditRight) disabled @endif class="form-control" onchange="changeSubmoduleDisciplineCicle(this, {{ $discipline->id }})">
                    @foreach($disciplineCycles as $disciplineCycle)
                        <option value="{{$disciplineCycle}}" @if($cycle == $disciplineCycle) selected @endif>{{$disciplineCycle}}</option>
                    @endforeach
                </select>
            </td>

            <td id="mt-tk-submodule-{{ $discipline->id }}">
                <select name="" @if(!$hasEditRight) disabled @endif class="form-control" onchange="changeSubmoduleMtTk(this, {{ $discipline->id }})">
                    @foreach($mtTks as $mtTkItem)
                        <option value="{{$mtTkItem}}" @if($mtTk == $mtTkItem) selected @endif>{{$mtTkItem}}</option>
                    @endforeach
                </select>
            </td>

            <td id="discipline-lang-submodule-{{ $discipline->id }}">
                <select name="" @if(!$hasEditRight) disabled @endif class="form-control" onchange="changeSubmoduleLangType(this, {{ $discipline->id }})">
                    @foreach($languageTypes as $languageTypeKey => $languageType)
                        <option value="{{$languageTypeKey}}" @if($langType == $languageTypeKey) selected @endif>{{$languageType}}</option>
                    @endforeach
                </select>
            </td>
        @endif
    </tr>
    @endforeach
    </tbody>
</table>