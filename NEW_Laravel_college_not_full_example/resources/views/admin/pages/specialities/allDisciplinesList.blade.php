<?php
/**
 * @var $speciality \App\Speciality
 * @var $module \App\Module
 */

$hasEditRight = \App\Services\Auth::user()->hasRight('specialities', 'edit');
?>

@foreach($disciplineList as $item)
    @if(!$item->InSpecialityModules($speciality) && !$speciality->idInDisciplines($item->id))
        <tr>
            <td class="text-center hide">       
            </td> 
            
             <td class="text-center">       
            </td> 
            
            
            <td class="text-center">
                <input type="checkbox" @if(!$hasEditRight) disabled @endif name="" value="1" onchange="changeDisciplineList(this, {{ $item->id }})" />
            </td>
            <td class="text-center check-exam-{{ $item->id }}" id="check-exam-{{ $item->id }}"></td>
            <td>
                <a href="{{ route('disciplineEdit',['id' => $item->id]) }}" target="_blank"> {{ $item->name }} </a>
            </td>
            <td> {{ $item->ects }}&nbsp;<sub>ECTS</sub> </td>            
            <td class="discipline-semester-{{ $item->id }}" id="discipline-semester-{{ $item->id }}">{{ $item->semester }}</td>
            <td class="discipline-verbal-sro-{{ $item->id }}" id="discipline-verbal-sro-{{ $item->id }}" >   </td>
            <td class="discipline-sro-hours-{{ $item->id }}" id="discipline-sro-hours-{{ $item->id }}"  >   </td>
            <td class="discipline-laboratory-hours-{{ $item->id }}" id="discipline-laboratory-hours-{{ $item->id }}">   </td>
            <td class="discipline-practical-hours-{{ $item->id }}" id="discipline-practical-hours-{{ $item->id }}" >   </td>
            <td class="discipline-lecture-hours-{{ $item->id }}" id="discipline-lecture-hours-{{ $item->id }}" > </td>
            <td id="control-form-{{ $item->id }}" class="control-form-{{ $item->id }}"></td>
            <td class="text-center check-has-coursework-{{ $item->id }}" id="check-has-coursework-{{ $item->id }}" data-search="0"></td>
            <td id="discipline-cicle-{{ $item->id }}" class="discipline-cicle-{{ $item->id }}"></td>
            <td id="discipline-lang-{{ $item->id }}" class="discipline-lang-{{ $item->id }}"></td>
            <td></td>
        </tr>
    @endif
@endforeach
