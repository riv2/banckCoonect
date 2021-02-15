<?php
/**
 * @var \App\Speciality $speciality
 */

$hasEditRight = \App\Services\Auth::user()->hasRight('specialities', 'edit');

?>

@extends("admin.admin_app")

@section('style')
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/tablesorter.css') }}" />
@endsection

@section("content")

<meta name="csrf-token" content="{{ csrf_token() }}">
	<div id="main">
		<div class="page-header">
			<h2> {{ isset($speciality->name) ? 'Редактировать: '. $speciality->name : 'Добавить специальность' }}</h2>

			<a href="{{ URL::to('/specialities') }}" class="btn btn-default-light btn-xs"><i class="md md-backspace"></i> Назад</a>
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
					<span aria-hidden="true">&times;</span>
				</button>

				{{ Session::get('flash_message') }}
			</div>
		@endif

		<div class="panel panel-default" id="main-panel">
			<div class="panel-body">
				{!! Form::open(['url' => [ route('POSTSpecialityAdd') ],'class'=>'form-horizontal padding-15','name'=>'service_form','id'=>'service_form','role'=>'form','enctype' => 'multipart/form-data']) !!}

                                <div id="selectedDisciplineList">
                                        <!-- Not in modules -->
                                        @foreach($speciality->disciplines as $item)
                                                @if(!$item->InSpecialityModules($speciality))

                                                @php
                                                  $rowId = $item->pivot->id;                                                                                           
                                                @endphp                                                                                        

                                                        <input type="hidden" name="discipline[{{ $rowId }}][visible]" value="1" />
                                                        <input type="hidden" name="discipline[{{ $rowId }}][exam]" value="{{ ($item->pivot->exam == true ? '1' : '0') }}" />
                                                        <input type="hidden" name="discipline[{{ $rowId }}][has_coursework]" value="{{ ($item->pivot->has_coursework == true ? '1' : '0') }}" />
                                                        <input type="hidden" name="discipline[{{ $rowId }}][discipline_cicle]" value="{{$item->pivot->discipline_cicle}}" />
                                                        <input type="hidden" name="discipline[{{ $rowId }}][mt_tk]" value="{{$item->pivot->mt_tk}}" />
                                                        <input type="hidden" name="discipline[{{ $rowId }}][pressmark]" value="{{$item->pivot->pressmark}}" />
                                                        <input type="hidden" name="discipline[{{ $rowId }}][language_type]" value="{{$item->pivot->language_type}}" />
                                                @endif
                                        @endforeach

                                <!-- In modules -->
                                        @foreach($moduleList as $module)
                                        <!-- disciplines -->
                                                @foreach($module->disciplines as $discipline)
                                                        <input type="hidden" v-if="selectedModules.indexOf({{$module->id}}) > -1" name="discipline[{{ $discipline->id }}][visible]" value="1" />
                                                        <input type="hidden" v-if="selectedModules.indexOf({{$module->id}}) > -1" name="discipline[{{ $discipline->id }}][exam]" value="{{ $speciality->getDisciplineExam2($discipline->id, 0) }}"  />
                                                        <input type="hidden" v-if="selectedModules.indexOf({{$module->id}}) > -1" name="discipline[{{ $discipline->id }}][has_coursework]" value="{{ $speciality->getDisciplineHasCoursework2($discipline->id, 0) }}"  />
                                                        <input type="hidden" v-if="selectedModules.indexOf({{$module->id}}) > -1" name="discipline[{{ $discipline->id }}][discipline_cicle]" value="{{ $speciality->getDisciplineDisciplineCicle2($discipline->id, 'ООД') }}" />
                                                        <input type="hidden" v-if="selectedModules.indexOf({{$module->id}}) > -1" name="discipline[{{ $discipline->id }}][mt_tk]" value="{{ $speciality->getDisciplineMtTk2($discipline->id, 'ОК') }}" />
                                                        <input type="hidden" v-if="selectedModules.indexOf({{$module->id}}) > -1" name="discipline[{{ $discipline->id }}][pressmark]" value="{{ $speciality->getDisciplinePressmark2($discipline->id, 'ОК') }}" />
                                                        <input type="hidden" v-if="selectedModules.indexOf({{$module->id}}) > -1" name="discipline[{{ $discipline->id }}][language_type]" value="{{ $speciality->getDisciplineLangType($discipline->id, 'native') }}" />
                                                @endforeach

                                        <!-- submodules -->
                                                @foreach($module->submodules as $submodule)
                                                        <input type="hidden" v-if="selectedModules.indexOf({{$module->id}}) > -1" name="submodule[{{ $submodule->id }}][visible]" value="1" />
                                                        <input type="hidden" v-if="selectedModules.indexOf({{$module->id}}) > -1" name="submodule[{{ $submodule->id }}][discipline_cicle]" value="{{ $speciality->getSubmoduleDisciplineCycle($submodule->id) }}" />
                                                        <input type="hidden" v-if="selectedModules.indexOf({{$module->id}}) > -1" name="submodule[{{ $submodule->id }}][mt_tk]" value="{{ $speciality->getSubmoduleMtTk($submodule->id) }}" />
                                                        <input type="hidden" v-if="selectedModules.indexOf({{$module->id}}) > -1" name="submodule[{{ $submodule->id }}][pressmark]" value="{{ $speciality->getSubmodulePressmark($submodule->id, 'ОК') }}" />
                                                        <input type="hidden" v-if="selectedModules.indexOf({{$module->id}}) > -1" name="submodule[{{ $submodule->id }}][language_type]" value="{{ $speciality->getSubmoduleLangType($submodule->id) }}" />
                                                @endforeach
                                        @endforeach
                                </div>
                                
                                
                                
                                <input type="hidden" name="id" value="{{ isset($speciality->id) ? $speciality->id : null }}">

				<div role="tabpanel">

					<div class="tab-content tab-content-default">

						<div role="tabpanel" class="tab-pane active" id="cz">

							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Код</label>
								<div class="col-sm-4">
									<div class="col-sm-2 code-char" style="padding-left: 0px;">
										<select @if(!$hasEditRight) disabled @endif class="selectpicker form-control" name="code_char" value="{{ $speciality->year ?? '' }}" title="{{ __('Please select') }}" required autofocus>
											<option value="b:5" @if($speciality->code_char . ':' . $speciality->code_number == 'b:5') selected @endif>5B</option>
											<option value="b:6" @if($speciality->code_char . ':' . $speciality->code_number == 'b:6') selected @endif>6B</option>
											<option value="m:6" @if($speciality->code_char . ':' . $speciality->code_number == 'm:6') selected @endif>6M</option>
											<option value="m:7" @if($speciality->code_char . ':' . $speciality->code_number == 'm:7') selected @endif>7M</option>
										</select>
									</div>
									<div class="col-sm-4" style="padding-left: 0px;">
										<input @if(!$hasEditRight) disabled @endif type="number" name="code" required value="{{ isset($speciality->code) ? $speciality->code : null }}" class="form-control">
									</div>
								</div>
							</div>

							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Год</label>
								<div class="col-sm-2">
									<select class="selectpicker" @if(!$hasEditRight) disabled @endif name="year" value="{{ $speciality->year ?? '' }}" data-live-search="true" data-size="5"
											title="{{ __('Please select') }}" required autofocus>
										@for($i=date('Y'); $i>=1997; $i--)
											<option value="{{$i}}" @if(isset($speciality->year) && $i == $speciality->year) selected @endif>
												{{$i}}
											</option>
										@endfor
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Название</label>
								<div class="col-sm-9">
									<input type="text" @if(!$hasEditRight) disabled @endif name="name" required value="{{ isset($speciality->name) ? $speciality->name : null }}" class="form-control">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Название (en)</label>
								<div class="col-sm-9">
									<input type="text" @if(!$hasEditRight) disabled @endif name="name_en" required value="{{ isset($speciality->name_en) ? $speciality->name_en : null }}" class="form-control">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Название (kz)</label>
								<div class="col-sm-9">
									<input type="text" @if(!$hasEditRight) disabled @endif name="name_kz" required value="{{ isset($speciality->name_kz) ? $speciality->name_kz : null }}" class="form-control">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">На анлг. для URL </label>
								<div class="col-sm-9">
									<input type="text" @if(!$hasEditRight) disabled @endif name="url" value="{{ isset($speciality->url) ? $speciality->url : null }}" class="form-control">
								</div>
							</div>

							<div class="form-group{{ $errors->has('iin') ? ' has-error' : '' }}">
								<label for="trend_id" class="col-md-3 control-label">Направление</label>

								<div class="col-md-6">

									<select class="form-control" @if(!$hasEditRight) disabled @endif name="trend_id" value="{{ $speciality->trend_id ?? '' }}" data-live-search="true" data-size="5"
											title="{{ __('Please select') }}" autofocus>
										@foreach($trendList as $item)
											<option value="{{$item->id}}" @if(isset($speciality->trend_id) && $item->id == $speciality->trend_id) selected @endif>
												{{$item->name}}&nbsp;({{$item->training_code}})
											</option>
										@endforeach
									</select>

									@if ($errors->has('trend_id'))
										<span class="help-block">
									<strong>{{ $errors->first('trend_id') }}</strong>
								</span>
									@endif
								</div>
							</div>

							<div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
								<label for="description" class="col-md-3 control-label">Описание (ru)</label>

								<div class="col-md-9">

									<textarea name="description" @if(!$hasEditRight) disabled @endif id="description" class="form-control">{{ isset($speciality->description) ? $speciality->description : '' }}</textarea>

									@if ($errors->has('description'))
										<span class="help-block">
									  <strong>{{ $errors->first('description') }}</strong>
								  </span>
									@endif
								</div>
							</div>

							<div class="form-group{{ $errors->has('description_kz') ? ' has-error' : '' }}">
								<label for="description_kz" class="col-md-3 control-label">Описание (kz)</label>

								<div class="col-md-9">

									<textarea name="description_kz" @if(!$hasEditRight) disabled @endif id="description_kz" class="form-control">{{ isset($speciality->description_kz) ? $speciality->description_kz : '' }}</textarea>

									@if ($errors->has('description_kz'))
										<span class="help-block">
                                  <strong>{{ $errors->first('description_kz') }}</strong>
                              </span>
									@endif
								</div>
							</div>

							<div class="form-group{{ $errors->has('description_en') ? ' has-error' : '' }}">
								<label for="description_en" class="col-md-3 control-label">Описание (en)</label>

								<div class="col-md-9">

									<textarea name="description_en" @if(!$hasEditRight) disabled @endif id="description_kz" class="form-control">{{ isset($speciality->description_en) ? $speciality->description_en : '' }}</textarea>

									@if ($errors->has('description_en'))
										<span class="help-block">
											<strong>{{ $errors->first('description_en') }}</strong>
										</span>
									@endif
								</div>
							</div>

							<div class="form-group{{ $errors->has('goals') ? ' has-error' : '' }}">
								<label for="goals" class="col-md-3 control-label">Цели (ru)</label>

								<div class="col-md-9">

									<textarea name="goals" @if(!$hasEditRight) disabled @endif id="goals" class="form-control">{{ isset($speciality->goals) ? $speciality->goals : '' }}</textarea>

									@if ($errors->has('goals'))
										<span class="help-block">
									<strong>{{ $errors->first('goals') }}</strong>
								  </span>
									@endif
								</div>
							</div>

							<div class="form-group{{ $errors->has('goals_kz') ? ' has-error' : '' }}">
								<label for="goals_kz" class="col-md-3 control-label">Цели (kz)</label>

								<div class="col-md-9">

									<textarea name="goals_kz" @if(!$hasEditRight) disabled @endif id="goals_kz" class="form-control">{{ isset($speciality->goals_kz) ? $speciality->goals_kz : '' }}</textarea>

									@if ($errors->has('goals_kz'))
										<span class="help-block">
                                <strong>{{ $errors->first('goals_kz') }}</strong>
                              </span>
									@endif
								</div>
							</div>

							<div class="form-group{{ $errors->has('goals_en') ? ' has-error' : '' }}">
								<label for="goals_en" class="col-md-3 control-label">Цели (en)</label>

								<div class="col-md-9">

									<textarea name="goals_en" @if(!$hasEditRight) disabled @endif id="goals_en" class="form-control">{{ isset($speciality->goals_en) ? $speciality->goals_en : '' }}</textarea>

									@if ($errors->has('goals_en'))
										<span class="help-block">
                            <strong>{{ $errors->first('goals_en') }}</strong>
                          </span>
									@endif
								</div>
							</div>

							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Квалификация</label>

								<div class="col-sm-9">
									<select name="qualification_id" class="form-control" @if(!$hasEditRight) disabled @endif>
										@if(!empty($speciality->trend->qualifications))
											@foreach($speciality->trend->qualifications as $qualification)
												<option
														value="{{ $qualification->id }}"
														{{ (old('qualification_id', $speciality->qualification_id) == $qualification->id) ? 'selected' : '' }}
												>
													{{ $qualification->name_ru }}
												</option>
											@endforeach
										@endif
									</select>
								</div>
							</div>

							<div class="form-group">
								<label for="trend_id" class="col-md-3 control-label">Модули</label>

								<div class="col-md-12">
									<hr>
									<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
										<table class="table table-striped table-hover dt-responsive" id="module-discipline-table">
											<thead>
											<tr>
												<th></th>
												<th>
													Модуль
													<input class="form-control module-search-input max-w-100" data-column-id="1" type="text">
												</th>
												<th>
													КГЭ
												</th>
												<th>
													Дисциплина
													<input class="form-control module-search-input max-w-100" data-column-id="3" type="text">
												</th>
												<th>
													Семестр
													<select class="form-control module-search-select max-w-100" data-column-id="4">
														<option value="">Все</option>
														@for($i = 1; $i < 11; $i++)
															<option value="{{ $i }}">{{ $i }}</option>
														@endfor
													</select>
												</th>
												<th>
													@{{ !moduleTableStatus ? 'Шифр': 'Пререквизит' }}
												</th>
												<th>
													Кредиты
													<input class="form-control module-search-input max-w-100" data-column-id="6" type="text">
												</th>
												<th>
													Наличие курсовой работы
													<select class="form-control module-search-select max-w-100" data-column-id="7">
														<option value="">Все</option>
														<option value="1">Есть</option>
														<option value="0">Нет</option>
													</select>
												</th>
												<th>
													Цикл
													<select class="form-control module-search-select max-w-100" data-column-id="8">
														<option value="">Все</option>
														@foreach($disciplineCycles as $disciplineCycle)
															<option value="{{ $disciplineCycle }}">{{ $disciplineCycle }}</option>
														@endforeach
													</select>
												</th>
												<th>
													Тип
													<select class="form-control module-search-select max-w-100" data-column-id="9">
														<option value="">Все</option>
														@foreach($mtTks as $mtTk)
															<option value="{{ $mtTk }}">{{ $mtTk }}</option>
														@endforeach
													</select>
												</th>
												<th>
													Язык
													<select class="form-control module-search-select max-w-100" data-column-id="10">
														<option value="">Все</option>
														@foreach($languageTypes as $languageTypeKey => $languageType)
															<option value="{{ $languageTypeKey }}">{{ $languageType }}</option>
														@endforeach
													</select>
												</th>
											</tr>
											</thead>

											<tbody>{{-- Insert by DataTables plugin --}}</tbody>
										</table>
									</div>
								</div>
							</div>
                                                        {!! Form::close() !!}
							<div class="form-group">
								<label for="trend_id" class="col-md-3 control-label">Дисциплины</label>
                                                                
								<div class="col-md-12">                                                                    
                                                                        <div id = "progressbar-disciplines-main" class="progress" style = "margin-top: 20px;">
                                                                            <div id = "progressbar-disciplines" class="progress-bar" role="progressbar" style="width:0%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                                                        </div>                                                                    
									<hr>
                                                                        
									<table class="hide table table-striped table-hover dt-responsive" id="main-discipline-table">
										<thead>                                                                                    
										<tr>
                                                                                        <th  class="text-center hide"></th>
                                                                                        <th  data-sorter="false" class="text-center"></th>
											<th  class="text-center"></th>
											<th  class="text-center">КГЭ<span class="sorter-arrow"></span></th>
											<th>Наименование<span class="sorter-arrow"></span></th>                                                                                        
                                                                                        <th>Кредиты<span class="sorter-arrow"></span></th>                                                                                        
											<th>Семестр<span class="sorter-arrow"></span></th> 
                                                                                        <th>Лекционные часы<span class="sorter-arrow"></span></th>                                                                                        
                                                                                        <th>
                                                                                            Практические<span class="sorter-arrow"></span>                                   
											</th>                                                                                        
                                                                                        <th>
                                                                                            Лабараторные<span class="sorter-arrow"></span>                                                                                           
											</th>                                                                                        
                                                                                        <th>
                                                                                            СРО<span class="sorter-arrow"></span>                                                                                           
											</th>                                                                                        
                                                                                        <th>
                                                                                            СРОП<span class="sorter-arrow"></span>
                                                                                        </th>
                                                                                        <th>
											    Форма контроля <span class="sorter-arrow"></span>                                                                                      
                                                                                        </th>
											<th>
												Наличие курсовой работы	<span class="sorter-arrow"></span>									
											</th>
											<th>
												Цикл<span class="sorter-arrow"></span>												                                               
											</th>											
											<th>
												Язык<span class="sorter-arrow"></span>												
											</th>
										</tr>
										</thead>

										<tbody>                                                                                 
                                                                                   
										@foreach($speciality->disciplines as $item)
											@if(!$item->InSpecialityModules($speciality))
                                                                                        
                                                                                        @php
                                                                                            $rowId = $item->pivot->id;                                                                                           
                                                                                        @endphp
                                                                                                                                                                                
												<tr class="row-{{ $rowId }}">
                                                                                                     <td class="text-center hide">                                                                                                            
                                                                                                         <span class = "disciplineElId" >{{ $item->pivot->discipline_id }} </span>
                                                                                                        </td>                                                                                                      
                                                                                                    
                                                                                                        <td class="text-center">                                                                                                            
                                                                                                            <button onclick="cloneDiscipline({{ $rowId }})" type="button" class="@if($hasEditRight && $item->pivot->cloned > 0) hide @endif btn btn-success btn-sm disciplineEl">+</button>
                                                                                                            <button onclick="deleteCloneDiscipline('{{ $rowId }}')" type="button" class="@if($hasEditRight && $item->pivot->cloned == 0) hide @endif btn btn-danger btn-sm disciplineEl">-</button>
                                                                                                        </td>                                                                                                    
													<td class="text-center">
														<input type="checkbox" @if(!$hasEditRight) disabled @endif name="" checked value="1" onchange="changeDisciplineList(this, {{ $rowId }})" />
													</td>
													<td class="text-center" id="check-exam-{{ $rowId }}">
														<input class ="disciplineEl" type="checkbox" @if(!$hasEditRight) disabled @endif name="" id="exam-{{$rowId}}" onchange="changeDisciplineListExam(this, {{ $rowId }})" @if($item->pivot->exam) checked @endif />
													</td>
													<td>
														<a href="{{ route('disciplineEdit',['id' => $rowId]) }}" target="_blank"> {{ $item->name }} </a>
													</td>
                                                                                                        <td> {{ $item->ects }}&nbsp;<sub>ECTS</sub> </td>
													<td id="discipline-semester-{{ $rowId }}" data-search="{{ $item->pivot->semester }}">
                                                                                                            <select class ="disciplineEl" @if(!$hasEditRight) disabled @endif name="discipline[{{ $rowId}}][semester]" onchange="changeDisciplineSemester(this, {{ $rowId }}" class="selectpicker">
                                                                                                                <option value="" @if(!$item->pivot->semester) selected @endif >нет</option>
                                                                                                                <option value="1" @if(strpos($item->pivot->semester,'1') !== false) selected @endif >1</option>
                                                                                                                <option value="2" @if(strpos($item->pivot->semester,'2') !== false) selected @endif >2</option>
                                                                                                                <option value="3" @if(strpos($item->pivot->semester,'3') !== false) selected @endif >3</option>
                                                                                                                <option value="4" @if(strpos($item->pivot->semester,'4') !== false) selected @endif >4</option>
                                                                                                                <option value="5" @if(strpos($item->pivot->semester,'5') !== false) selected @endif >5</option>
                                                                                                                <option value="6" @if(strpos($item->pivot->semester,'6') !== false) selected @endif >6</option>
                                                                                                                <option value="7" @if(strpos($item->pivot->semester,'7') !== false) selected @endif >7</option>
                                                                                                                <option value="8" @if(strpos($item->pivot->semester,'8') !== false) selected @endif >8</option>
                                                                                                            </select>
													</td>
                                                                                                        
                                                                                                        <td id="discipline-verbal-sro-{{ $rowId }}"       @if($item->pivot->verbal_sro) data-search="{{ $item->pivot->verbal_sro }}" @endif >           <input @if($item->pivot->verbal_sro) value = "{{ $item->pivot->verbal_sro }}"@endif       onchange="changeDisciplineVerbalSro(this, {{ $rowId }})" name="discipline[{{ $rowId}}][verbal_sro]" class="disciplineEl form-control discipline-search max-w-100" type="text"> </td>
                                                                                                        <td id="discipline-sro-hours-{{ $rowId }}"        @if($item->pivot->sro_hours) data-search="{{ $item->pivot->sro_hours }}"@endif>               <input @if($item->pivot->sro_hours) value = "{{ $item->pivot->sro_hours }}"@endif        onchange="changeDisciplineSroHours(this, {{ $rowId }})" name="discipline[{{ $rowId}}][sro_hours]" class="disciplineEl form-control discipline-search max-w-100" type="text"> </td>
                                                                                                        <td id="discipline-laboratory-hours-{{ $rowId }}" @if($item->pivot->laboratory_hours) data-search="{{ $item->pivot->laboratory_hours }}"@endif> <input @if($item->pivot->laboratory_hours) value = "{{ $item->pivot->laboratory_hours }}"@endif onchange="changeDisciplineLaboratoryHours(this, {{ $rowId }})" name="discipline[{{ $rowId}}][laboratory_hours]" class="disciplineEl form-control discipline-search max-w-100" type="text"> </td>
                                                                                                        <td id="discipline-practical-hours-{{ $rowId }}"  @if($item->pivot->practical_hours) data-search="{{ $item->pivot->practical_hours }}"@endif>   <input @if($item->pivot->practical_hours) value = "{{ $item->pivot->practical_hours }}"@endif  onchange="changeDisciplinePracticalHours(this, {{ $rowId }})" name="discipline[{{ $rowId}}][practical_hours]" class="disciplineEl form-control discipline-search max-w-100" type="text"> </td>
                                                                                                        <td id="discipline-lecture-hours-{{ $rowId }}"    @if($item->pivot->lecture_hours) data-search="{{ $item->pivot->lecture_hours }}"@endif>       <input @if($item->pivot->lecture_hours) value = "{{ $item->pivot->lecture_hours }}"@endif    onchange="changeDisciplineLectureHours(this, {{ $rowId }})" name="discipline[{{ $rowId}}][lecture_hours]" class="disciplineEl form-control discipline-search max-w-100" type="text"> </td>
                                                                                                        
                                                                                                        <td id="control-form-{{ $rowId }}" data-search="{{ $item->pivot->control_form }}">
                                                                                                            <select @if(!$hasEditRight) disabled @endif name="discipline[{{$rowId}}][control_form]" class="form-control disciplineEl" onchange="changeDisciplineComtrolForm(this, {{ $rowId }})">
                                                                                                                <option value="test"        @if( $item->pivot->control_form == 'test' ) selected @endif>Тест</option>
                                                                                                                <option value="traditional" @if( $item->pivot->control_form == 'traditional' ) selected @endif>Традиционная</option>
                                                                                                                <option value="report"      @if( $item->pivot->control_form == 'report' ) selected @endif>Отчет</option>
                                                                                                                <option value="credit"      @if( $item->pivot->control_form == 'credit' ) selected @endif>Диф. зачет</option>
                                                                                                                <option value="protection"  @if( $item->pivot->control_form == 'protection' ) selected @endif>Защита</option>
                                                                                                            </select>
													</td>     
           												
													<td class="text-center" id="check-has-coursework-{{ $rowId }}" data-search="{{ $item->pivot->has_coursework }}">
														<input class = "disciplineEl" type="checkbox" @if(!$hasEditRight) disabled @endif name="discipline[{{ $rowId}}][has_coursework]" id="has-coursework-{{$rowId}}" onchange="changeDisciplineHasCoursework(this, {{ $rowId }})" @if($item->pivot->has_coursework) checked @endif />
													</td>
													<td id="discipline-cicle-{{ $rowId }}" data-search="{{ $item->pivot->discipline_cicle }}">
														<select @if(!$hasEditRight) disabled @endif name="discipline[{{ $rowId}}][discipline_cicle]" class="form-control disciplineEl" onchange="changeDisciplineDisciplineCicle(this, {{ $rowId }})">
                                                                                                                    <option value="ООД" @if( $item->pivot->discipline_cicle == 'ООД' ) selected @endif>Общеобразовательные дисциплины</option>
                                                                                                                    <option value="ОГД" @if( $item->pivot->discipline_cicle == 'ОГД' ) selected @endif>Общие гуманитарные дисциплины</option>
                                                                                                                    <option value="СЭД" @if( $item->pivot->discipline_cicle == 'СЭД' ) selected @endif>Социально-экономические дисциплины</option>
                                                                                                                    <option value="ОПД" @if( $item->pivot->discipline_cicle == 'ОПД' ) selected @endif>Общепрофессиональные дисциплины</option>
                                                                                                                    <option value="СД" @if( $item->pivot->discipline_cicle == 'СД' ) selected @endif>Специальные дисциплины</option>
                                                                                                                    <option value="ДД" @if( $item->pivot->discipline_cicle == 'ДД' ) selected @endif>ДД - Дисциплины, определяемые организацией образования</option>
                                                                                                                    <option value="ДО" @if( $item->pivot->discipline_cicle == 'ДО' ) selected @endif>ДО - Дисциплины, определяемые организацией образования</option>
                                                                                                                    <option value="ДООО" @if( $item->pivot->discipline_cicle == 'ДООО' ) selected @endif>Дисциплины, определяемые организацией образования</option>
                                                                                                                    <option value="ПО" @if( $item->pivot->discipline_cicle == 'ПО' ) selected @endif>Производственное обучение</option>
                                                                                                                    <option value="ПП" @if( $item->pivot->discipline_cicle == 'ПП' ) selected @endif>Профессиональная практика</option>
                                                                                                                    <option value="ДП" @if( $item->pivot->discipline_cicle == 'ДП' ) selected @endif>Дипломное проектирование</option>
                                                                                                                    <option value="ИА" @if( $item->pivot->discipline_cicle == 'ИА' ) selected @endif>Итоговая аттестация</option>
                                                                                                                    <option value="Ф" @if( $item->pivot->discipline_cicle == 'Ф' ) selected @endif>Факультативные занятия</option>
                                                                                                                    <option value="БМ" @if( $item->pivot->discipline_cicle == 'БМ' ) selected @endif>Базовые модули</option>
                                                                                                                    <option value="ПМ" @if( $item->pivot->discipline_cicle == 'ПМ' ) selected @endif>Профессиональные модули</option>
                                                                                                                    <option value="МОО" @if( $item->pivot->discipline_cicle == 'МОО' ) selected @endif>Модули, определяемые организацией образования</option>
                                                                                                                </select>
													</td>
													
													<td id="discipline-lang-{{ $rowId }}" data-search="{{ $item->pivot->language_type }}">
														<select @if(!$hasEditRight) disabled @endif name="" class="form-control disciplineEl" onchange="changeDisciplineLangType(this, {{ $rowId }})">
															<option value="native" @if($item->pivot->language_type == 'native') selected @endif>Родной</option>
															<option value="second" @if($item->pivot->language_type == 'second') selected @endif>Второй</option>
															<option value="other" @if($item->pivot->language_type == 'other') selected @endif>Другой</option>
														</select>
													</td>
                                                                                                        <input type="hidden" name="discipline[{{ $rowId }}][cloned]" value="{{ $item->pivot->cloned }}" />
                                                                                                        <input type="hidden" name="discipline[{{ $rowId }}][speciality_discipline_row_id]" value="{{ $item->pivot->id }}" />
                                                                                                        <input type="hidden" name="discipline[{{ $rowId }}][discipline_id]" value="{{ $item->id }}" />
                                                                                                </tr>
											@endif
										@endforeach
										</tbody>
									</table> 
                                                                        
                                                                        <div style = "color:#bdbdbd;" id="main-discipline-table-pager" class="main-discipline-table-pager hide" style="height:50px"> 
                                                                            
                                                                            <span style = "margin-right:50px;" class="main-discipline-table-pager-pagedisplay"></span>
                                                                            
                                                                            Перейти на <input style = "color:#000;" size="2" type="text" class="main-discipline-table-pager-gotopage"/> страницу.
                                                                            
                                                                            Записей на странице:
                                                                                <select style = "color:#000;" class="main-discipline-table-pager-pagesize">                                                                                    
                                                                                    <option value="10" selected>10</option>
                                                                                    <option value="25">25</option>
                                                                                    <option value="100">100</option>
                                                                                    <option value="9999">Все</option>
                                                                                </select> 
                                                                            
                                                                            <div style ="float:right;font-weight:600;cursor:pointer;word-spacing: 10px;">
                                                                                <span class="main-discipline-table-pager-first" >First</span>
                                                                                <span class="main-discipline-table-pager-prev" >Previous</span>                                                                                                                                                      
                                                                                <span class="main-discipline-table-pager-next" >Next</span>
                                                                                <span class="main-discipline-table-pager-last" >Last</span>
                                                                            </div>
                                                                            
                                                                        </div>                                                                        

								</div>
							</div>

							<hr>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Проверка ЕНТ</label>
								<div class="col-sm-1">
									<select class="selectpicker" @if(!$hasEditRight) disabled @endif onchange="changeCheckEnt()" name="check_ent" value="{{ $speciality->check_ent ?? 0 }}" data-size="5" required autofocus>
										<option value="1" @if($speciality->check_ent) selected @endif>Да</option>
										<option value="0" @if(!$speciality->check_ent) selected @endif>Нет</option>
									</select>
								</div>
							</div>

							<div class="col-md-12" id="ent-block" @if(!$speciality->check_ent) style="display: none;" @endif>
								<div class="form-group">
									<label for="" class="col-sm-3 control-label">Общий проходной балл ЕНТ</label>
									<div class="col-sm-1" style="padding-left: 7px;">
										<input type="number" @if(!$hasEditRight) disabled @endif step="0.01" name="passing_ent_total" value="{{ isset($speciality->passing_ent_total) ? $speciality->passing_ent_total : null }}" class="form-control">
									</div>
								</div>

								<div class="form-group">
									<label for="trend_id" class="col-md-3 control-label">Предметы</label>
									<div class="col-md-12">
										<table class="table table-striped table-hover dt-responsive">
											<thead>
											<tr>
												<th class="text-center"></th>
												<th>Наименование</th>
												<th style="width: 100px;">Проходной балл ЕНТ</th>
											</tr>
											</thead>
											<tbody>
											@foreach($subjectList as $item)
												<tr>
													<td class="text-center">
														<input type="checkbox" @if(!$hasEditRight) disabled @endif id="subject-checkbox-{{ $item->id }}" onchange="changeSubjectVisible({{ $item->id }})" name="subject[{{ $item->id }}][visible]" @if($speciality->idInSubjects($item->id)) checked @endif value="1" />
													</td>
													<td>
														{{ $item->name }}
													</td>
													<td>
														<input type="number" @if(!$hasEditRight) disabled @endif step="0.01" class="form-control" id="subject-ent-{{ $item->id }}" @if(!$speciality->idInSubjects($item->id)) style="display:none;" @endif name="subject[{{ $item->id }}][ent]" value="{{$speciality->getSubjectEntById($item->id)}}" />
													</td>
												</tr>
											@endforeach
											</tbody>
										</table>
									</div>
								</div>
							</div>

							<hr>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Вступительный экзамен</label>
								<div class="col-sm-1">
									<select @if(!$hasEditRight) disabled @endif class="selectpicker" onchange="changeEntranceTest()" name="entrance_test" value="{{ $speciality->check_entrance_test ?? 0 }}" data-size="5"
											required autofocus>
										<option value="1" @if($speciality->check_entrance_test) selected @endif>Да</option>
										<option value="0" @if(!$speciality->check_entrance_test) selected @endif>Нет</option>
									</select>
								</div>
							</div>

							<div class="col-md-12" id="entrance-test-block" @if(!$speciality->entrance_test) style="display: none;" @endif>
								<div class="form-group no-padding">
									<label for="trend_id" class="col-md-3 control-label">Вступительный тест</label>
									<div class="col-md-6" style="padding-left: 7px;">
										<select @if(!$hasEditRight) disabled @endif class="selectpicker" name="entrance_test_id" value="{{ $speciality->entrance_test->id ?? 0 }}" data-size="5"
												autofocus>
											@foreach($entranceTestList as $item)
												<option value="{{ $item->id }}" @if(isset($speciality->entrance_test) && $speciality->entrance_test->id == $item->id) selected @endif>{{ $item->name }}</option>
											@endforeach
										</select>
									</div>
								</div>
							</div>
						</div>

						<hr>

						<div class="form-group">
							<div class="col-md-offset-3 col-sm-9 ">
								@if($hasEditRight)
									<button id="submit_form" type="button" class="btn btn-primary">{{ isset($speciality->name) ? 'Сохранить' : 'Добавить' }}</button>
								@endif

								<button class="btn btn-default dropdown-toggle" type="button" id="dropdownl-export-langs" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
									Выгрузить КГЭ в PDF
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu" aria-labelledby="dropdownl-export-langs">
									<li>
										<a href="{{ route('adminExportKgePdf', ['specialityId' => $speciality->id]) }}?lang=kz">Казахский</a>
									</li>
									<li>
										<a href="{{ route('adminExportKgePdf', ['specialityId' => $speciality->id]) }}?lang=ru">Русский</a>
									</li>
									<li>
										<a href="{{ route('adminExportKgePdf', ['specialityId' => $speciality->id]) }}?lang=en">Английский</a>
									</li>
								</ul>
							</div>
						</div>
						<div class="modal fade" id="disciplinesSemesters" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
							<div class="modal-dialog modal-lg">
								<div class="modal-content">
									<div class="modal-header">
										Семестры
									</div>
									<div class="modal-body">
										<div class="row">
											<div class="col-sm-12 padding-5" v-for="semester in disciplineSemesters">
												<div class="panel panel-default padding-0" :class="semester.checked? 'panel-success': 'panel-default'">
													<div class="panel-heading">
														Семестр @{{ semester.name }}
														<input class="pull-right" type="checkbox" v-model="semester.checked" :name="`semester[${semester.name}]`" @change="checkDisciplineSemester(semester.name)">
													</div>
													<div class="panel-body">
														<table class="table table-responsive">
															<thead>
															<tr>
																<th></th>
																<th>Очная</th>
																<th>Заочная</th>
															</tr>
															</thead>
															<tbody>
															<tr v-for="(name, type) in hoursTypes">
																<td>@{{ name }}</td>
																<td>
																	<input :name="`semesters[${semester.name}][fulltime][${type}]`"
																			class="form-control"
																			type="number"
																			disabled
																			v-model="semester.hours.fulltime[type]">
																</td>
																<td>
																	<input :name="`semesters[${semester.name}][extramural][${type}]`"
																			class="form-control"
																			type="number"
																			disabled
																			v-model="semester.hours.extramural[type]">
																</td>
															</tr>
															<tr>
																<td>Итого часов</td>
																<td>
                                                                <span disabled class="form-control ">
                                                                    @{{ getSumHoursForSemester(semester.name, 'fulltime') }}
                                                                </span>
																</td>
																<td>
                                                                <span disabled class="form-control">
                                                                    @{{ getSumHoursForSemester(semester.name, 'extramural') }}
                                                                </span>
																</td>
															</tr>
															<tr>
																<td>Форма контроля</td>
																<td>
																	<input class="form-control"
																		   type="text"
																		   disabled
																		   :value="getSemesterControlForm(semester.controlForm.fulltime)">
																</td>
																<td>
																	<input class="form-control"
																		   type="text"
																		   disabled
																		   :value="getSemesterControlForm(semester.controlForm.extramural)">
																</td>
															</tr>
															</tbody>
														</table>
													</div>
												</div>
											</div>
										</div>
									</div>
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

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/jquery.tablesorter.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/extras/jquery.tablesorter.pager.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/jquery.tablesorter.widgets.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.quicksearch/2.4.0/jquery.quicksearch.min.js"></script>


<script type="text/javascript">
		const app = new Vue({
			el: '#main-panel',
			data: {
				moduleTableStatus: 0,
				showDependence: false,
				selectedModules: [],
				moduleDisciplinesTable: null,
				disciplineTable: null,
				disciplineId: null,
				disciplineSemesters: [],
				successMessage: null,
				hoursTypes: {
					lecture: 'Лекционные',
					practice: 'Практические',
					lab: 'Лабораторные',
					sro: 'СРО',
					srop: 'СРОП',
				},
				controlForms: {
					test: 'Тест',
					traditional: 'Традиционный',
					report: 'Отчет',
					score: 'Диф. зачет',
					protect: 'Защита',
				},
				dependenceModal: {
					disciplineId: null,
					dataTable: null,
					dependenceDisciplinesIds: null,
					year: null,
					message: null,
					errors: {},
				}
			},
			created: function() {
				@if($speciality->modules)
					@foreach($speciality->modules as $module)
						this.selectedModules.push({{ $module->id }});
					@endforeach
				@endif
				this.loadAllDisciplines();
			},
			methods: {
				showDependenceModal: function(disciplineId){
					this.dependenceModal.disciplineId = disciplineId;
					$('#modulesDependenceModal').modal('show')

					this.reloadDependenceTable()
				},
				reloadDependenceTable: function(){
					if (this.dependenceModal.dataTable === null){
						this.initDependenceTable()
					} else {
						this.dependenceModal.dataTable.draw();
					}
				},
				deleteSpecialityDisciplineDependence: function(specialityDisciplineDependenceID) {
					axios.post('{{route('adminDeleteSpecialityDisciplineDependence')}}/' + specialityDisciplineDependenceID)
						.then(({data}) => {
							this.dependenceModal.message = data.message;
							this.reloadDependenceTable()
						})
				},
				initDependenceTable: function(){
					this.dependenceModal.dataTable = $('#dependence-discipline-table').DataTable({
						processing: true,
						serverSide: true,
						ajax: {
							data: function (request) {
								request._token = "{{ csrf_token() }}"
							},
							url: `{{ route('adminAjaxGetListDependenceForDiscipline', ['specialityId' => $speciality->id]) }}/${this.dependenceModal.disciplineId}`,
							type: "POST",
						},
						drawCallback: function(){
							$('.selectpicker_dependence').selectpicker()
						},
						columns: [
							{
								data: 'num',
								orderable: false,
							},
							{
								data: 'disciplines',
								orderable: false,
							},
							{
								data: 'year',
								orderable: false,
							},
							{
								data: 'actions',
								orderable: false,
							},
						],
					});
				},
				addDependenceToSpecialityDiscipline: function() {
					const data = {
						speciality_id: '{{$speciality->id}}',
						discipline_id: this.dependenceModal.disciplineId,
						year: this.dependenceModal.year,
						dependence_disciplines_ids:  $('#dependenceDisciplines').val()
					};
					axios.post('{{route('adminAddSpecialityDisciplineDependence')}}', data)
						.then(({data}) => {
							this.dependenceModal.message = data.message;
							this.dependenceModal.errors = {}

							this.reloadDependenceTable()
						}).catch(({response}) => {
							this.dependenceModal.errors = response.data.errors;
						})
				},
				saveSpecialityDisciplineDependence: function(specialityDisciplineDependenceID) {
					const data = {
						dependence_disciplines_ids: $(`[name="dependence[${specialityDisciplineDependenceID}]"]`).val()
					};
					axios.post('{{route('adminSaveSpecialityDisciplineDependence')}}/' + specialityDisciplineDependenceID, data)
						.then(({data}) => {
							this.dependenceModal.message = data.message;
							this.dependenceModal.errors = {}

							this.reloadDependenceTable()
						})
				},
				loadAllDisciplines: function() {
					axios.post('{{route('getAllDisciplinesTable')}}', {
						speciality_id: {{$speciality->id ?? '0'}}
					}).then(function(response){                                                                                       
                                            $('#main-discipline-table tbody').append(response.data);
                                            initMainDisciplineTablePagerPage();
                                            setTimeout(function() {
                                                progressMainDisciplineTable("off");
                                            }, 500);
     					});
				},
				getSemesterControlForm: function(controlForm){
					if (controlForm !== null){
						return this.controlForms[controlForm];
					}
				},
				showEditDisciplinesSemestersModal: function(disciplineId){
					this.getSpecialityDisciplineSemester(disciplineId);
					$('#disciplinesSemesters').modal('show');
				},
				getSpecialityDisciplineSemester(disciplineId) {
					this.disciplineId = disciplineId;
					axios.post('{{route('adminAjaxGetSpecialityDisciplineSemester')}}/' + `{{$speciality->id}}/${disciplineId}`)
					7		.then(({data}) => {
								this.disciplineSemesters = data;
							});
				},
				getSumHoursForSemester: function(name, type){
					let sum = 0;
					const semester = this.disciplineSemesters.find(semester => {
						return semester.name === name;
					});
					for(let time in semester.hours[type]){
						sum += +semester.hours[type][time];
					}
					return sum;
				},
				checkDisciplineSemester: function(semester){
					const checkbox = document.getElementsByName(`semester[${semester}]`);
					const data = {
						semester,
						disciplineId: this.disciplineId,
						specialityId: @if($speciality->id) {{$speciality->id}} @else 1 @endif,
						checked: checkbox[0].checked
					};
					axios.post('{{route('adminAjaxAddSpecialityDisciplineSemester')}}', data)
							.then(({data}) => {
								this.getSpecialityDisciplineSemester(this.disciplineId);
							});
				}
			},
			mounted: function () {
					this.moduleDisciplinesTable = $('#module-discipline-table').DataTable({
						processing: true,
						serverSide: true,
						order: [[ 3, "asc" ]],
						dom: '<"col-md-4"l><"col-md-4 buttons text-center"><"col-md-4"f><t><ip>',
						ajax: {
							data: function (request) {
								request._token = "{{ csrf_token() }}"
								request.speciality_modules = $('.only-speciality').data('status')
							},
							url: "{{ route('adminAjaxGetListForSpecialityEdit', [
								'speciality_id' => isset($speciality->id) ? $speciality->id : null
							]) }}",
							type: "POST",
						},
						drawCallback: function() {
							app.selectedModules.map(function (value) {
								$('.module_id[value="' + value + '"]').prop('checked', true);
								$('.module_id[value="' + value + '"]').parents('tr').css('background-color', '#d6e9c6');
							});
						},
						columns: [
							{
								data: 'module_id',
								orderable: false,
							},
							{
								data: 'module_name',
							},
							{
								data: 'discipline_kge',
								orderable: false,
							},
							{
								data: 'discipline_name',
							},
							{
								data: 'discipline_semester.html',
								name: 'discipline_semester.value',
								orderable: false,
							},
							{
								data:'dependence',
								className: 'text-center',
								orderable: false,
							},
							{
								data: 'discipline_ects',
							},
							{
								data: 'discipline_has_course_work.html',
								name: 'discipline_has_course_work.value',
								orderable: false,
							},
							{
								data: 'discipline_cicle.html',
								name: 'discipline_cicle.value',
								orderable: false,
							},
							{
								data: 'discipline_mt_tk.html',
								name: 'discipline_mt_tk.value',
								orderable: false,
							},
							{
								data: 'discipline_language_type.html',
								name: 'discipline_language_type.value',
								orderable: false,
							},
						],
					});

					$('div.buttons').append(
							'<button type="button" class="btn btn-default only-speciality" data-status="0" data-text="Все модули">' +
							'Только для специальности' +
							'</button>'
					);
					$('.selectpicker').selectpicker({});
			}
		});
		$('body').on('click', '.only-speciality', function () {
			var newButtonText = $(this).data('text');
			var oldButtonText = $(this).html();
			var status = $(this).data('status');

			if (status == 0) {
				app.moduleTableStatus = 1;
				$(this).data('status', 1);
			} else {
				app.moduleTableStatus = 0;
				$(this).data('status', 0);
			}

			$(this).html(newButtonText);
			$(this).data('text', oldButtonText);
			$(this).prop('disabled', true);

			app.moduleDisciplinesTable.draw();

			$(this).prop('disabled', false);
		});

		$('body').on('change', '.module-search-select', function () {
			var val = $(this).val();

			app.moduleDisciplinesTable.column($(this).attr('data-column-id'))
					.search(val)
					.draw();
		});

		$('body').on('keypress', '.module-search-input', function (event) {
			if (event.key == 'Enter') {
				var val = $(this).val();

				app.moduleDisciplinesTable.column($(this).attr('data-column-id'))
						.search(val)
						.draw();
			}
		});

		$('#module-discipline-table').on('change', '.module_id', function () {
			var module_id = parseInt($(this).val());

			if ($(this).prop('checked')) {
				if (app.selectedModules.indexOf(module_id) == -1) {
					app.selectedModules.push(module_id);
					$('.module_id[value="' + module_id + '"]').parents('tr').css('background-color', '#d6e9c6');
				}
			} else {
				if (app.selectedModules.indexOf(module_id) != -1) {
					app.selectedModules.splice(app.selectedModules.indexOf(module_id), 1);
					$('.module_id[value="' + module_id + '"]').parents('tr').css('background-color', '');
				}
			}

			$('.module_id[value="' + module_id + '"]').prop('checked', $(this).prop('checked'));
		});
                
                $('#submit_form').click(function () {                     
                    
                    $('#main-discipline-table').find('input').each(function() {
                         this.disabled = 'disabled';
                    });
                    $('#selectedDisciplineList').find('input').each(function() {
                         this.disabled = 'disabled';
                    });

                    var service_form = $('#service_form');                   
                   
                    var send_serialize   = service_form.serialize();
                    var inputs = '';
                    
                    app.selectedModules.map(function (value) {
                            inputs += '<input type="hidden" name="modules[]" value="' + value + '">';
                    });

                    $(this).append(inputs);         

                    $.ajaxSetup({
                        headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                      });

                    $.ajax({
                        url: '{{ route('POSTSpecialityAdd') }}',
                        type: "POST",
                        data: send_serialize,
                        success: function (data) {
                            window.location.reload(false); 
                        },
                        error: function (msg) {
                            console.log(msg);
                            alert('Ошибка - см. консоль.');
                        }
                    });
                });
                
                function getRandomInRange(min, max) {
                    return Math.floor(Math.random() * (max - min + 1)) + min;
                }
                
                function deleteCloneDiscipline(disciplineId) {                 
                    $(".row-"+disciplineId).remove();
                }
                
                function cloneDiscipline(disciplineId) {
                    row      = $(".row-"+disciplineId);
                    rowHtml  = row.html();
                    clonedId = getRandomInt(99999, 999999);
           
                    rowHtml = rowHtml + ' <input type="hidden" name="discipline[' + disciplineId + '][visible]" value="1" />' +
                    '<input type="hidden" name="discipline[' + disciplineId + '][exam]" value="0" />' +
                    '<input type="hidden" name="discipline[' + disciplineId + '][has_coursework]" value="0" />' +
                    '<input type="hidden" name="discipline[' + disciplineId + '][discipline_cicle]" value="ООД" />' +
                    '<input type="hidden" name="discipline[' + disciplineId + '][mt_tk]" value="ОК" />' +
                    '<input type="hidden" name="discipline[' + disciplineId + '][pressmark]" value="" />' +
                    '<input type="hidden" name="discipline[' + disciplineId + '][language_type]" value="native" />' +
                    '<input type="hidden" name="discipline[' + disciplineId + '][speciality_discipline_row_id]" value="0" />' +
                    '<input type="hidden" name="discipline[' + disciplineId + '][new_cloned]" value="1" />' +                  
                    '<input type="hidden" name="discipline[' + disciplineId + '][cloned]" value="' + clonedId  + '" />'; 
            
                    rowHtml = rowHtml.replace(new RegExp(disciplineId,'g'),clonedId);
                    rowHtml = rowHtml.replace(new RegExp('btn-success','g'), 'btn-success hide');    
                    rowHtml = rowHtml.replace(new RegExp('bootstrap-select','g'), 'bootstrap-select hide');                    
                    rowHtml = rowHtml.replace(new RegExp('class=" hide','g'), 'class="');  
                    rowHtml = rowHtml.replace(new RegExp('"'+clonedId+'"','g'), '"'+disciplineId+'"');                    
                    rowHtml = rowHtml.replace(new RegExp('>'+clonedId+'</option>','g'), '>'+disciplineId+'</option>');

                    $(row).after('<tr class = "row-'+clonedId+'">' + rowHtml + ' </tr>'); 
    
                    $('select[name="discipline['+clonedId+'][semester]"]').selectpicker({});
                 
                }
                
                function getRandomInt(min, max) {
                    min = Math.ceil(min);
                    max = Math.floor(max);
                    return Math.floor(Math.random() * (max - min)) + min; //Максимум не включается, минимум включается
                }
                
		function changeDisciplineList(elem, disciplineId) {
			if(elem.checked) {
				$('#selectedDisciplineList').append(
                                    '<input type="hidden" name="discipline[' + disciplineId + '][visible]" value="1" />' +
                                    '<input type="hidden" name="discipline[' + disciplineId + '][exam]" value="0" />' +
                                    '<input type="hidden" name="discipline[' + disciplineId + '][has_coursework]" value="0" />' +
                                    '<input type="hidden" name="discipline[' + disciplineId + '][discipline_cicle]" value="ООД" />' +
                                    '<input type="hidden" name="discipline[' + disciplineId + '][mt_tk]" value="ОК" />' +
                                    '<input type="hidden" name="discipline[' + disciplineId + '][pressmark]" value="" />' +
                                    '<input type="hidden" name="discipline[' + disciplineId + '][language_type]" value="native" />' +
                                    '<input type="hidden" name="discipline[' + disciplineId + '][language_type]" value="native" />' +
                                    '<input type="hidden" name="discipline[' + disciplineId + '][new_cloned]" value="1" />' +
                                    '<input type="hidden" name="discipline[' + disciplineId + '][discipline_id]" value="'+ disciplineId +'" />'
				);

				$('.check-exam-' + disciplineId).append('<input class = "disciplineEl" type="checkbox" @if(!$hasEditRight) disabled @endif name="" onchange="changeDisciplineListExam(this, ' + disciplineId + ')" />');

				$('.check-has-coursework-' + disciplineId).append('<input class = "disciplineEl" type="checkbox" @if(!$hasEditRight) disabled @endif name="" onchange="changeDisciplineHasCoursework(this, ' + disciplineId + ')" />');

                                $('.discipline-verbal-sro-' + disciplineId).html(`
                                    <input onchange="changeDisciplineVerbalSro(this, ` + disciplineId + `)" name="discipline[` + disciplineId + `][verbal_sro]" class="form-control discipline-search max-w-100 disciplineEl" type="text">
				`);
                                $('.discipline-sro-hours-' + disciplineId).html(`
                                    <input onchange="changeDisciplineSroHours(this, ` + disciplineId + `)" name="discipline[` + disciplineId + `][sro_hours]" class="form-control discipline-search max-w-100 disciplineEl" type="text">
				`);
                                 $('.discipline-laboratory-hours-' + disciplineId).html(`
                                    <input onchange="changeDisciplineLaboratoryHours(this, ` + disciplineId + `)" name="discipline[` + disciplineId + `][laboratory_hours]" class="form-control discipline-search max-w-100 disciplineEl" type="text">
				`);
                                 $('.discipline-practical-hours-' + disciplineId).html(`
                                    <input onchange="changeDisciplinePracticalHours(this, ` + disciplineId + `)" name="discipline[` + disciplineId + `][practical_hours]" class="form-control discipline-search max-w-100 disciplineEl" type="text">
				`);
                                 $('.discipline-lecture-hours-' + disciplineId).html(`
                                    <input onchange="changeDisciplineLectureHours(this, ` + disciplineId + `)" name="discipline[` + disciplineId + `][lecture_hours]" class="form-control discipline-search max-w-100 disciplineEl" type="text">
				`);        

                                 $('.discipline-cicle-' + disciplineId).html(`
					<select name="" onchange="changeDisciplineDisciplineCicle(this, ` + disciplineId + `)" class="form-control disciplineEl">
                                            <option value="ООД">Общеобразовательные дисциплины</option>
                                            <option value="ОГД">Общие гуманитарные дисциплины</option>
                                            <option value="СЭД">Социально-экономические дисциплины</option>
                                            <option value="ОПД">Общепрофессиональные дисциплины</option>
                                            <option value="СД">Cпециальные дисциплины</option>
                                            <option value="ДД">ДД - Дисциплины, определяемые организацией образования</option>
                                            <option value="ДО">ДО - Дисциплины, определяемые организацией образования</option>
                                            <option value="ДООО">Дисциплины, определяемые организацией образования</option>
                                            <option value="ПО">Производственное обучение</option>
                                            <option value="ПП">Профессиональная практика</option>
                                            <option value="ДП">Дипломное проектирование</option>
                                            <option value="ИА">Итоговая аттестация</option>
                                            <option value="Ф">Факультативные занятия</option>
                                            <option value="БМ">Базовые модули</option>
                                            <option value="ПМ">Профессиональные модули</option>
                                            <option value="МОО">Модули, определяемые организацией образования</option>
					</select>
				`);

				$('.discipline-semester-' + disciplineId).html(`                                
                                        <select	@if(!$hasEditRight) disabled @endif name="discipline[` + disciplineId + `][semester]" onchange="changeDisciplineSemester(this, ` + disciplineId + `" class="selectpicker disciplineEl">
                                            <option value="">нет</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5">5</option>
                                            <option value="6">6</option>
                                            <option value="7">7</option>
                                            <option value="8">8</option>
                                        </select> `);
                                            
				$('.selectpicker').selectpicker('render');

				$('.discipline-pressmark-' + disciplineId).html(`
				  	<input type="text" @if(!$hasEditRight) disabled @endif name="" onchange="changeDisciplinePressmark(this, ` + disciplineId + `)" class="disciplineEl form-control" />
				`);

                                $('.control-form-' + disciplineId).html(`
                                    <select name="" onchange="changeDisciplineControlForm(this, ` + disciplineId + `)" class="disciplineEl form-control">
                                        <option value="test">Тест</option>
                                        <option value="traditional">Традиционная</option>
                                        <option value="report">Отчет</option>
                                        <option value="credit">Диф. зачет</option>
                                        <option value="protection">Защита</option>
                                    </select>
                                `);

				$('.discipline-lang-' + disciplineId).html(`
					<select name="" onchange="changeDisciplineLangType(this, ` + disciplineId + `)" class="disciplineEl form-control">
					  <option value="native">Родной</option>
					  <option value="second">Второй</option>
					  <option value="other">Другой</option>
					</select>
				`);
			} else {
				$('[name="discipline[' + disciplineId + '][visible]"]').remove();
				$('[name="discipline[' + disciplineId + '][exam]"]').remove();
				$('[name="discipline[' + disciplineId + '][has_coursework]"]').remove();
				$('[name="discipline[' + disciplineId + '][discipline_cicle]"]').remove();
				$('[name="discipline[' + disciplineId + '][mt_tk]"]').remove();
				$('[name="discipline[' + disciplineId + '][language_type]"]').remove();
				$('[name="discipline[' + disciplineId + '][pressmark]"]').remove();
				$('[name="discipline[' + disciplineId + '][semestr]"]').remove();
                                $('[name="discipline[' + disciplineId + '][verbal_sro]"]').remove();
				$('[name="discipline[' + disciplineId + '][sro_hours]"]').remove();
				$('[name="discipline[' + disciplineId + '][laboratory_hours]"]').remove();
				$('[name="discipline[' + disciplineId + '][practical_hours]"]').remove();
				$('[name="discipline[' + disciplineId + '][lecture_hours]"]').remove();
				$('.check-exam-' + disciplineId).html('');
				$('.check-has-coursework-' + disciplineId).html('');
				$('.discipline-cicle-' + disciplineId).html('');
				$('.mt-tk-' + disciplineId).html('');
				$('.discipline-lang-' + disciplineId).html('');
				$('.discipline-pressmark-' + disciplineId).html('');
				$('.discipline-semester-' + disciplineId).html('');
                                $('.discipline-verbal-sro-' + disciplineId).html('');
				$('.discipline-sro-hours' + disciplineId).html('');
				$('.discipline-laboratory-hours-' + disciplineId).html('');
				$('.discipline-practical-hours-' + disciplineId).html('');
				$('.discipline-lecture-hours-' + disciplineId).html('');  
			}
                        
                       initDisciplineAjaxWorker();
                        
		}

		function changeDisciplineListExam(elem, disciplineId) {
                    //return;
			if(elem.checked) {
				$('[name="discipline[' + disciplineId + '][exam]"]').val(1);
			} else
			{
				$('[name="discipline[' + disciplineId + '][exam]"]').val(0);
			}
		}

		function changeDisciplineHasCoursework(elem, disciplineId) {
                     //return;
			if(elem.checked) {
				$('[name="discipline[' + disciplineId + '][has_coursework]"]').val(1);
			} else
			{
				$('[name="discipline[' + disciplineId + '][has_coursework]"]').val(0);
			}
		}

		function changeDisciplinePressmark(elem, disciplineId) {
                     //return;
			$('[name="discipline[' + disciplineId + '][pressmark]"]').val($(elem).val());
		}
                
                function changeDisciplineVerbalSro(elem, disciplineId) {
                     //return;
			$('[name="discipline[' + disciplineId + '][verbal_sro]"]').val($(elem).val());
		}  
                
                function changeDisciplineSroHours(elem, disciplineId) {
                     //return;
			$('[name="discipline[' + disciplineId + '][sro_hours]"]').val($(elem).val());
		}
                
                function changeDisciplineLaboratoryHours(elem, disciplineId) {
                     //return;
			$('[name="discipline[' + disciplineId + '][laboratory_hours]"]').val($(elem).val());
		}
                
                function changeDisciplinePracticalHours(elem, disciplineId) {
                     //return;
			$('[name="discipline[' + disciplineId + '][practical_hours]"]').val($(elem).val());
		}
                
                function changeDisciplineLectureHours(elem, disciplineId) {
                     //return;
			$('[name="discipline[' + disciplineId + '][lecture_hours]"]').val($(elem).val());
		}
   
		function changeDisciplineSemester(elem, disciplineId) {                   
                     //return;
			$('[name="discipline[' + disciplineId + '][semester]"]').val($(elem).val());
		}

		function changeDisciplineLangType(elem, disciplineId) {
                     //return;
			$('[name="discipline[' + disciplineId + '][language_type]"]').val($(elem).val());
		}

		function changeDisciplineDisciplineCicle(elem, disciplineId) {
                     //return;
			$('[name="discipline[' + disciplineId + '][discipline_cicle]"]').val($(elem).val());
		}

		function changeDisciplineMtTk(elem, disciplineId) {
                     //return;
			$('[name="discipline[' + disciplineId + '][mt_tk]"]').val($(elem).val());
		}

		$("#serviceType").change(function(){
			var placesList = $('#placesList');

			if( $(this).find('select').val() == 'Master' ) placesList.show(150);
			else placesList.hide(150);
		});

		function changeSubjectVisible(subjectId) {
			if($('#subject-checkbox-' + subjectId).prop('checked') == true) {
				$('#subject-ent-' + subjectId).show();
			} else {
				$('#subject-ent-' + subjectId).hide();
			}
		}

		function changeCheckEnt() {
			if($('[name=check_ent]').val() == '1' ) {
				$('#ent-block').show();
			} else {
				$('#ent-block').hide();
			}
		}

		function changeEntranceTest() {
			if($('[name=entrance_test]').val() == '1' ) {
				$('#entrance-test-block').show();
			} else {
				$('#entrance-test-block').hide();
			}
		}

		function changeSubmoduleSemester(elem, submoduleId) {
			$('[name="submodule[' + submoduleId + '][semester]"]').val($(elem).val());
		}

		function changeSubmodulePressmark(elem, submoduleId) {
			$('[name="submodule[' + submoduleId + '][pressmark]"]').val($(elem).val());
		}

		function changeSubmoduleDisciplineCicle(elem, submoduleId) {
			$('[name="submodule[' + submoduleId + '][discipline_cicle]"]').val($(elem).val());
		}

		function changeSubmoduleMtTk(elem, submoduleId) {
			$('[name="submodule[' + submoduleId + '][mt_tk]"]').val($(elem).val());
		}

		function changeSubmoduleLangType(elem, submoduleId) {
			$('[name="submodule[' + submoduleId + '][language_type]"]').val($(elem).val());
		}
		$('.selectpicker').selectpicker();
                
                var progressBarOnIntervalMainDisciplineTable;
                
                function progressMainDisciplineTable(state) {                    
                    width = 0;
                    if (state == "on") {
                       progressBarOnIntervalMainDisciplineTable = setInterval(progressBarOnMainDisciplineTable, 100);
                       $('.main-discipline-table-pager').addClass("hide");
                       $('#progressbar-disciplines').removeClass("hide");
                       $('#progressbar-disciplines-main').removeClass("hide");
                       $('#main-discipline-table').addClass("hide");
                       $('#main-discipline-table-search').addClass("hide");                      
                    } else {
                       $('.main-discipline-table-pager').removeClass("hide");
                       $('#main-discipline-table').removeClass("hide");
                       $('#progressbar-disciplines').addClass("hide");
                       $('#progressbar-disciplines-main').addClass("hide");
                       $('#main-discipline-table-search').removeClass("hide");
                       clearInterval(progressBarOnIntervalMainDisciplineTable);
                    }                   
                   
                    function progressBarOnMainDisciplineTable() {
                        if (width > 99) {
                            width = 0;
                        }
                        width += 3;
                        $('#progressbar-disciplines').css("width", width + "%");
                    }
		}                 
               
                function initMainDisciplineTablePagerPage() {
                    setTimeout(function() {
                        $("#main-discipline-table").tablesorter({
                            widgets: ['zebra', 'filter'],
                            sortList: [[0,0]],
                            widgetOptions : {
                                
                            } 
                            }).tablesorterPager({
                            container: $(".main-discipline-table-pager"),
                            page: 0,
                            size: 10,
                            cssPageDisplay: '.main-discipline-table-pager-pagedisplay',
                            cssFirst: '.main-discipline-table-pager-first', 
                            cssPrev: '.main-discipline-table-pager-prev', 
                            cssNext: '.main-discipline-table-pager-next',
                            cssLast: '.main-discipline-table-pager-last', 
                            cssPageSize: '.main-discipline-table-pager-pagesize', 
                            cssGoto: '.main-discipline-table-pager-gotopage',
                        });
                        
                        $('input[data-column="0"]').hide();
                        $('input[data-column="1"]').hide();  
                        $('input[data-column="2"]').hide();
                        $('input[data-column="3"]').hide();
                        $('input[data-column="15"]').hide();  
                        $('td[data-column="0"]').hide();
                         
                    }, 100);        
                     
                }
                progressMainDisciplineTable('on');
                initDisciplineAjaxWorker();
                
                function initDisciplineAjaxWorker() {
                    $('.disciplineEl').change(function() {
                         var atrName = $(this).attr('name');
                         var rowId = parseInt(atrName.replace(/\D+/g,""));
                         var $data = {};                    
                         $data['visible'] = $("input[name='discipline["+rowId+"][visible]']").val();
                         $data['exam'] = $("input[name='discipline["+rowId+"][exam]']").val();
                         $data['has_coursework'] = $("input[name='discipline["+rowId+"][has_coursework]']").val();
                         $data['discipline_cicle'] = $("input[name='discipline["+rowId+"][discipline_cicle]']").val();
                         $data['mt_tk'] = $("input[name='discipline["+rowId+"][mt_tk]']").val();
                         $data['pressmark'] = $("input[name='discipline["+rowId+"][pressmark]']").val();
                         $data['language_type'] = $("input[name='discipline["+rowId+"][language_type]']").val();
                         $data['new_cloned'] = $("input[name='discipline["+rowId+"][new_cloned]']").val();
                         $data['discipline_id'] = $("input[name='discipline["+rowId+"][discipline_id]']").val();
                         $data['semester'] = $("input[name='discipline["+rowId+"][semester]']").val();
                         $data['verbal_sro'] = $("input[name='discipline["+rowId+"][verbal_sro]']").val();
                         $data['sro_hours'] = $("input[name='discipline["+rowId+"][sro_hours]']").val();
                         $data['laboratory_hours'] = $("input[name='discipline["+rowId+"][laboratory_hours]']").val();
                         $data['practical_hours'] = $("input[name='discipline["+rowId+"][practical_hours]']").val();
                         $data['lecture_hours'] = $("input[name='discipline["+rowId+"][lecture_hours]']").val();
                         $data['disciplineId'] = rowId;   

                       $.ajaxSetup({
                        headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                       });


                       var editRoute = '{{ route('POSTDisciplineEdit_v2') }}';
                       var deleteRoute = '{{ route('POSTDisciplineDelete_v2') }}';
                       var addRoute = '{{ route('POSTDisciplineAdd_v2') }}';
                       var route = '';

console.log($data);


                    $.ajax({
                        url: route,
                        type: "POST",
                        data: $data,
                        success: function (data) {
                            console.log(data);
                        },
                        error: function (msg) {
                            console.log(msg);
                            alert('Ошибка - см. консоль.');
                        }
                    });




                    });
                }
                
	</script>
@endsection
