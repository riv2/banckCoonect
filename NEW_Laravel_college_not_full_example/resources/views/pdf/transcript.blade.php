@include('pdf._head')

	<style>
		/* non breacable table header */
	    table tbody tr td:before,
	    table tbody tr td:after,
	    table tbody tr th:before,
	    table tbody tr th:after
	    {
	        content: "";
	        height: 4px;
	        display: block;
	    }
	    .disciplineName {
	    	line-height: 0.8;
	    	font-size: 10px;
	    }
	    .line08 {
	    	line-height: 0.7;
	    }
	    table td {
	    	padding: 0;
	    }
	</style>

	<div style="font-size: 12px; width: 100%;">
		<p style="width: 100%; text-align: center;"><strong>Транскрипт/ The transcript/ Транскрипт</strong></p>
		
		<p>Аты-жөні/ Name/ Ф.И.О. <strong>@if(isset($profile->fio)) {{$profile->fio}} @endif</strong></p>
		
		<p>Секторы/ Sector/ Сектор 
			@if(isset($sector[0]) and $sector[0] != '' and $sector[0] != 'null' )
				<strong>{{ $sector[0] }}</strong>
			@else 
				____________________________________________________________________________
			@endif
			<!--<strong>Шетел тілі: екі шетел тілі/ Foreign language: two foreign languages/Иностранный язык: два иностранных языка</strong>--></p>
		
		<p>Мамандығы/ Speciality/ Специальность <strong>
			{{ $speciality->getOriginal('code_number') . strtoupper($speciality->getOriginal('code_char')) . $speciality->getOriginal('code') }} - 
			{{ __($speciality->getOriginal('name_kz') )}}/ {{ __($speciality->getOriginal('name_en') )}}/ {{ __($speciality->getOriginal('name') )}}
		</strong></p>
		<p>Түскен жылы/ Year/ Год поступления 
			<strong>
				@if(isset($enterDate))
					{{date('Y', $enterDate)}} 
				@endif
			</strong>
		</p>
		
		<p>Оқу тілі/ Language/ Язык <strong>{{ $educationLang }}</strong></p>

	</div>




	<table border="1" style="border-collapse:collapse;width: 100%; font-size: 10px; text-align: center;">
		<tr class="line08">
			<th rowspan="2">№<br />п/п</th>
			<th rowspan="2">Пәндердің атаулы/<br />Courses/<br />Наименование дисциплины</th>
			<th rowspan="2">Кредит саны/<br />Credit hours/<br />Кол-во кредитов</th>
			<!--<th rowspan="2">Сағат саны<br />/ Аmount of hours/<br /> Кол-во часов</th>-->
			<th colspan="4">Баға/ Grade/ Оценка</th>
		</tr>
		<tr  class="line08">
			<th>Пайызбен/<br />In persent/<br />В процентах</th>
			<th>Әріптік/<br />Alphabetic/<br />Буквенная</th>
			<th>Балмен/<br />In points/<br />В баллах</th>
			<th>Дәстүрлі жүйемен/<br />Traditional/<br />Традиционная</th>
		</tr>
		<tr  class="line08">
			<td>1</td>
			<td>2</td>
			<td>3</td>
			<td>4</td>
			<td>5</td>
			<td>6</td>
			<td>7</td>
		</tr>

		{{ $index = 0 }}
		{{ $totalCredits = 0 }}
		@foreach($disciplines as $key => $line)
			@if( 
				isset($line->final_result) && 
				(!isset($line->is_practice) || $line->is_practice != 1) && 
					(
					$line->migrated != 1 || 
					!($line->payed_credit == null && $line->payed = 1)
					)
			)
				{{ $totalCredits += $line->ects }}
				<tr>
					<td>{{ $index = $index+1 }}</td>
					<td class="disciplineName">{!! $line->name_kz . '/<br />' . $line->name_en . '/<br />' . $line->name !!}</td>
					<td>{{ $line->ects}}</td>
					<!--<td>{{ $line->ects * config('app.hoursInEcts') }}</td>-->
					<td>{{ $line->final_result}}</td>
					<td>{{ $line->final_result_letter}}</td>
					<td>{{ $line->final_result_points }}</td>
					{{--<td>{{$line->final_result_string}}</td>--}}
					<td>{{ $line->test_result_string}}</td>
					
				</tr>
			@endif
		@endforeach
		
	</table>

	

	<h3>Кәсіптік практиканы өтті / Has passed professional practice/
Прошел профессиональные практики</h3>

<table border="1" style="border-collapse:collapse;width: 100%; font-size: 10px; text-align: center;">
	<tr class="line08">
		<th rowspan="2">№</th>
		<th rowspan="2">Кәсіптік практикалардың түрлері/<br />Form of professional practice/<br />Виды профессиональных практик</th>
		<th rowspan="2">Практика өту кезені/<br />Period of practice passage/<br />Период прохождения практики</th>
		<th rowspan="2">Кредит саны/<br />Credit hours/<br />Кол-во кредитов</th>
		<th colspan="4">Баға/ Grade/ Оценка</th>
	</tr>
	<tr class="line08">
		<th>Пайызбен/<br />In persent/<br />В процентах</th>
		<th>Әріптік/<br />Alphabetiс/<br />Буквенная</th>
		<th>Балмен/<br />In points/<br />В баллах</th>
		<th>Дәстүрлі жүйемен<br />/ Traditional/<br /> Традиционная</th>
	</tr>
	{{ $index = 0 }}
	@foreach($disciplines as $key => $line)
		@if( isset($line->final_result) && isset($line->is_practice) && $line->is_practice == 1 && 
				(
				$line->migrated != 1 || 
				!($line->payed_credit == null && $line->payed = 1)
				)
			)
			<tr>
				<td>{{ $index = $index+1 }}</td>
				<td class="disciplineName">{!! $line->name_kz . '/<br />' . $line->name_en . '/<br />' . $line->name!!}</td>
				<td>{{ $line->practical_hours }}</td>
				<td>{{ $line->ects}}</td>
				<td>{{ $line->final_result}}</td>
				<td>{{ $line->final_result_letter}}</td>
				<td>{{ $line->final_result_gpa }}</td>
				{{--<td>{{$line->final_result_string}}</td>--}}
				<td>{{ $line->test_result_string}}</td>
				
			</tr>
		@endif
	@endforeach
	@if( $index == 0 )
		<tr class="line08">
			<td class="disciplineName">Өтілмеді/ Not pass/ Не проходил(а)</td>
			<td>—</td>
			<td>—</td>
			<td>—</td>
			<td>—</td>
			<td>—</td>
			<td>—</td>
			<td>—</td>
		</tr>
	@endif
	
</table>


<h3>Мемлекеттік қорытынды аттестация / Final state attestation/ Итоговая государственная аттестация</h3>


<table border="1" style="border-collapse:collapse;width: 100%; font-size: 10px; text-align: center;">
	<tr class="line08">
		<th rowspan="2">№</th>
		<th rowspan="2">Мемлекеттік емтиханды тапсырды/<br />Has passed the state examinations/<br />Сдал государственные экзамены</th>
		<th rowspan="2">МАҚ –тың хаттамасының күні және нөмірі/<br />Date and number of the report of SAC/<br /> Дата и номер протокола ГАК</th>
		<th colspan="4">Баға/ Grade/ Оценка</th>
	</tr>
	<tr class="line08">
		<th>Пайызбен/<br />In persent/<br />В процентах</th>
		<th>Әріптік/<br />Alphabetiс/<br />Буквенная</th>
		<th>Балмен/<br />In points/<br />В баллах</th>
		<th>Дәстүрлі жүйемен<br />/ Traditional/<br /> Традиционная</th>
	</tr>
	
	<tr class="line08">
		<td>Өтілмеді/ Not pass/ Не проходил(а)</td>
		<td>—</td>
		<td>—</td>
		<td>—</td>
		<td>—</td>
		<td>—</td>
		<td>—</td>
	</tr>
	
</table>


<div style="font-size: 12px; width: 100%;">
	<p>Жалпы кредит саны/ Total Hours Passed/ Общее количество кредитов 
		<span><strong>{{$totalCredits}}</strong>
		<br />GPA <strong>{{ $average_gpa }}</strong></span>
	</p>

<table border="0">
	<tr>
		<td>
			Мирас университетінің ректоры/ The rector of the University "Miras»/ Ректор университета «Мирас»</p>
		</td><td>        
		 	_________________ <strong>Мырзалиев Б.А.</strong>
		</td>
	</tr>

	<tr>
		<td>
			Сектор менеджері/ Sector Manager/ Менеджер сектора 
		</td><td>  
			___________________________
		
		@if(isset($sector[1]) and $sector[1] != '' and $sector[1] != 'null' )
			<strong>{{ $sector[1] }}</strong>
		@endif
		
		</td>
	</tr>

	<tr>
		<td>
			Тіркеу бөлімінің директоры/ Director Registrar's Office / Директор Офис Регистратора 
		</td><td>  
			______________________  <strong>Альмаханова Н.А.</strong>
		</td>
	</tr>


	<tr>
		<td>
			Тіркеу бөлімі/ Registrar's Office/ Офис регистратор 
		</td><td> 
				__________________ <strong>{{ $executerName }}</strong>
		</td>
	</tr>
		

</table>

<p>М.О. / М.П.  Тіркеу №/ registration №/ регистрационный № ________________________  «____________________» ж./ y./ г.</p>

</div>


{{--@include('pdf._footQR')--}}