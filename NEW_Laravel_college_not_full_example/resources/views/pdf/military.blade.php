@include('pdf._headInvisible')


<div style="font-size: 55%;">
	<table style="font-size: 0.8em; width: 100%;">
		<tr>
			<td style="text-align: left; width: 50%;">
			</td>
			<td style="text-align: right; width: 50%;">
				Азаматтарды әскери қызметке<br />
				шақыруды ұйымдастыру және<br />
				өткізу ережесіне<br />
				3 — қосымша
				
			</td>
		</tr>
	</table>


	<h2 style="width: 100%; text-align: center;">АНЫҚТАМА</h2>



<p>{{date('d-m-Y', strtotime($profile->bdate))}} жылы туған әскерге шақырылушы {{$profile->fio}}</p>
 

<p>
@if(isset($enterDate))
	{{date('Y', $enterDate)}} жылы 
@endif

	{{$enderDocId}} бұйрығымен «Мирас» университетіне түсті және
қазіргі кезде «Мирас» университетінде
(оқу орнының толық атауы)</p>

 

<p>Күндізгі бөлімінің {{$profile->course}} курсында оқитындығы туралы берілді.</p>

@if(isset($enterDate))
	<p>Оқу орнын бітіру мерзімі {{ date('Y') + $studyPeriodLeft }} ж. маусым</p>
@endif

 
<p>Анықтама<br />
______________________________________________________<br /> 
<span style="font-size: 0.8em;">(қорғаныс істері жөніндегі басқарма (бөлім) атауы)</span></p>

<p>ұсынуға берілді.</p>
  
</div>




@include('pdf._footSign')