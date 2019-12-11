<script language='JavaScript' src='{$request.Site}/public/js/newtracker.js' type='text/javascript'></script>
{if $request.action eq 'edit'}
<script language='JavaScript' src='{$request.Site}/public/js/edittracker.js' type='text/javascript'></script>
{/if}
<input id = 'Site' type = 'hidden' value = '{$request.Site}'>
<title>TDSTraff - {if $request.action eq 'edit'} Просмотр (редактирование) потока {else} Добавить новый поток {/if}</title>


<div class = 'Conteiner'>

<div class = 'notice'>
<label style = 'font-size:14px;'>Памятка</label>
<hr>
Рекомендуемый размер рекламного изображения для данных форматов:<br><br>
<label>Rich-media</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label>AdSpot</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label>TopLine</label><br>
640x480&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;300x250&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;730х90<br><br>
<label>PopUnder</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label>CatFish </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label>Fullscreen</label><br>
468х75&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;730х90&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1024x600(WEB)<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;360x640(WAP)<br><br>

</div>

	<h1>{if $request.action eq 'edit'} Просмотр (редактирование) потока {else} Добавить новый поток {/if}</h1> <hr>
<form id = 'newTrackerForm' enctype='multipart/form-data' action = '{$request.Site}/trackers/digest/' method = 'POST'>
<input id = 'trackerId' type = 'hidden' value = '{$request.tracker.id}' name = 'tracker[trackerId]'>
	<table>
	<tr>
	<td valign="top" style = "width: 284px;position:relative;" >
	<div style = 'width:275px;'></div> 
	<div id = 'primaryConteiner' class = 'primaryConteiner'> 
	<label id = 'tipName'> Введите наименование потока: </label>
	<div class = 'filed'> <input maxlength = '100' type = 'text' name = "tracker[name]" autocomplete='off' value = "{$request.tracker.name}" {if $request.tracker.id != ''} disabled {/if}/></div>
	<label id = 'tipTheme'> Выберите тематику трафика: </label>
		<div class = 'filed'> 
    	<select name = "tracker[theme]" {if $request.tracker.id != ''} disabled {/if} >
		<option value = ''></option>
		{foreach $request.theme as $item}
		<option value = '{$item.name}' {if $request.tracker.theme eq $item.name} selected {/if} > {$item.name} </option>
		{/foreach}
		</select> 
		</div>
		
		<label id = 'tipThemeURL'> Укажите источники трафика, через запятую: </label>
		<div class = 'filed'> <input maxlength = '200' type = 'text' name = "tracker[themeURL]"  placeholder='http://' value = "{$request.tracker.themeURL}" {if $request.tracker.id != ''} disabled {/if} /></div>
		
		<label id = 'tipDomain' > Выберите домен в коде потока: </label>
		<div class = 'filed'> 
    	<select name = "tracker[domain]" {if $request.tracker.id != ''} disabled {/if}>
		<option value = ''></option>
		{if $request.domains eq 'none'} {else}
		{foreach $request.domains as $item}
		<option value = '{$item.domain}' {if $request.tracker.domain eq $item.domain} selected {/if} > {$item.domain} {if $item.for_all == '1'} (Общий) {/if}</option>
		{/foreach} {/if}
		</select> 
		{if $request.tracker.id != ''} <input type = 'hidden' name = 'tracker[domain]' value = '{$request.tracker.domain}'> {/if}
		</div>
		
		<br>
		<div id = 'tipPreview' class = 'filed'> <input id = 'preview' type = 'button' value="Предосмотр" /></div>
		<div id = 'tipTrackerSend' class = 'filed'> <input id = 'newTrackerSend' type = 'button' value="{if $request.action eq 'edit'} Редактировать поток {else} Создать поток {/if}" /></div>
		
	 </div>
	 	</td>
		<td valign="top">
			<div id = 'advansedConteiner' class = 'advansedConteiner'> 
			
			<div class = 'advansedSwitcher' id = 'advansedSwitcher'>
			<div class = 'addBanner' id = 'addBanner'>+</div>
			<div id = 'tipTraffickBack' class = 'ItemBanner switcherActive traffickBack' banner = '0' onclick = 'switchBanner(0);' >&rarr;Traffic Back<span class = 'traficPercent'></span></div>
			

			{foreach from=$request.tracker.rule key=k item=rule}
			{if $k != '_0'} 
			
			<div class = 'ItemBanner' banner = '{$k|replace:'_':''}' onclick = 'switchBanner("{$k|replace:'_':''}");' >&nbsp;&nbsp;&nbsp;{$k|replace:'_':''}&rarr;&nbsp;&nbsp;&nbsp;
			{if $k != '_1'} <div class = 'removeBanner' onclick = 'removeBanner("{$k|replace:'_':''}");'>x</div> {/if}
			<div class = 'ruleName' id = 'ruleName{$k}'>First rule</div>
			</div>
			{/if} 
			{/foreach}
			
			</div>
			<div class = 'clear'></div>
			<div id = 'ConteinerBannerMain'>

			<div class = 'ConteinerBanner' id = 'ConteinerBanner_0'>
			<div style = 'width:710px;padding:20px;'>
				<div class = 'textCenter'>
				<h2>&darr; Настройка Traffic Back &darr;</h2>
				</div> 
		
			<div class = 'firstColl'>
				
				<br>
				<br>

				<center><span style = 'color:blue;' id = 'tipWEB' >WEB трафик</span></center>
				<br>
				
				<div class = 'filedFilter'>
				<center><label id = 'tipType' > &darr; Тип Traffic Back: &darr; </label>	</center>			
				    	<select name = 'rule[_0][trafficBackType_1]' onchange="trafficRuleType(this,'banner_rule_0_1','redirect_rule_0_1');">
						{foreach $request.formats as $item}
						<option value = '{$item.name}'> {$item.name} </option>
						{/foreach}
						</select> 
				
				</div>
				
				<br>
				
				<label id = 'tipURLTraffic' > URL Traffic Back: </label>
				<div class = 'filedUrl'> <input id = '' maxlength = '200' type = 'text' name = 'rule[_0][trafficBackURL_1]'  placeholder='http://' value = '{$request.tracker.rule._0.trafficBackURL_1}' /></div>
				<div id = 'typeBannerStyle_1' class = 'filedUrl {if $request.tracker.id != ''}hide{/if}'>
				<label id = 'tipBannerStyle'> Стиль баннера: </label>
				<div class = 'filed'>
				<label>Ширина: </label><input style = 'width:50px;' value = '{$request.tracker.rule._0.bannerWidth_1|default}' id = '' maxlength = '4' type = 'text' name = 'rule[_0][bannerWidth_1]' onchange = "redirectNumber(this);" />
				<label>Высота: </label><input style = 'width:50px;' value = '{$request.tracker.rule._0.bannerHeight_1|default}' id = '' maxlength = '4' type = 'text' name = 'rule[_0][bannerHeight_1]' onchange = "redirectNumber(this);" />
				</div>
				</div>
			
				<div id = 'banner_rule_0_1'><div id = 'typeBanners_0_1'>
  		        <label id = 'tipBanner' > Баннер Traffic Back: </label><br>
				
				<div class = 'filed left' style = 'margin-right:10px;position:relative;'>
				<div style = 'width:150px;;height:150px;position:absolute;top:0px;left:0px;cursor:pointer;' onclick = "showGallery('banner_web_img_0_1');" ></div>
				<div style = 'width:20px;height:20px;position:absolute;top:-10px;right:-20px;cursor:pointer; background-image: url("../../../public/img/add.png");' onclick = "showGallery('banner_web_img_0_1');" ></div>
				
				<img width = "150px;"  style = "max-height:300px;" id = 'banner_web_img_0_1' src = "{if $request.tracker.rule._0.trafficBackBanner_1 eq ''} {$request.Site}/public/img/addBanner.jpg {else} {$request.tracker.rule._0.trafficBackBanner_1} {/if}"/>
				<object svg = 'banner_web_img_0_1' style = 'width:150px;;height:150px;display:none;' type='application/x-shockwave-flash' data='{if $request.tracker.rule._0.trafficBackBanner_1 eq ''} {$request.Site}/public/img/addBanner.jpg {else} {$request.tracker.rule._0.trafficBackBanner_1} {/if}'></object>

				<input banner_web = 'banner_web_img_0_1' type = 'hidden' name = 'rule[_0][trafficBackBanner_1]' value = '{$request.tracker.rule._0.trafficBackBanner_1}'/></div>
				
				<script> var img_src = '{$request.tracker.rule._0.trafficBackBanner_1}'; var filesExt = ['swf']; var parts = img_src.split('.'); if(filesExt.join().search(parts[parts.length - 1]) != -1) { $('[svg = banner_web_img_0_1]').show(); $('#banner_web_img_0_1').hide(); } </script>
				</div></div>
				
				<div id = 'typeFullvideo_0_1'>
				<label> ID видео с Youtube: </label><br>
				<input type = 'text' name = 'rule[_0][trafficBackVideo_1]' value = '{$request.tracker.rule._0.trafficBackVideo_1|default}' maxlength = '15'/><br><br>
				</div>
				{if $request.tracker.id == ''} <script> $('#typeFullvideo_0_1').hide(); </script> {/if}
				
				<div class = 'clear'></div><br>
				<label id = 'tipUnTracker'> Отключить поток: </label>
				<input type="radio" name="tracker[unTracker]" value="N" {if $request.tracker.unTracker == 'N'}checked{/if}> Нет
			    <input type="radio" name="tracker[unTracker]" value="Y" {if $request.tracker.unTracker == 'Y'}checked{/if}> Да
				<br><br>
				<label id = 'tipUnPreview'> Отключить предпоказ: </label>
				<input type="radio" name="tracker[unPreview]" value="N" {if $request.tracker.unPreview == 'N'}checked{/if}> Нет
			    <input type="radio" name="tracker[unPreview]" value="Y" {if $request.tracker.unPreview == 'Y'}checked{/if}> Да
			
			</div>
			
			<div class = 'secondColl' style = "width:290px;">
					<br>
				<br>

				<center><span style = 'color:red;' id = 'tipWAP'>WAP трафик</span></center>
				<br>
				
				<div class = 'filedFilter'>
				<center><label id = 'tipType' > &darr; Тип Traffic Back: &darr; </label>	</center>			
				    	<select name = 'rule[_0][trafficBackType_2]' onchange="trafficRuleType(this,'banner_rule_0_2','redirect_rule_0_2');">
						{foreach $request.formats as $item}
						<option value = '{$item.name}'> {$item.name} </option>
						{/foreach}
						</select> 
				</div>
				
				<br>
				
				<label id = 'tipURLTraffic' > URL Traffic Back: </label>
				<div class = 'filedUrl'> <input id = '' maxlength = '200' type = 'text' name = 'rule[_0][trafficBackURL_2]'  placeholder='http://' value = '{$request.tracker.rule._0.trafficBackURL_2}' /></div>
				<div id = 'typeBannerStyle_2' class = 'filedUrl {if $request.tracker.id != ''}hide{/if}'>
				<label id = 'tipBannerStyle'> Стиль баннера: </label>
				<div class = 'filed'>
				<label>Ширина: </label><input style = 'width:50px;' value = '{$request.tracker.rule._0.bannerWidth_2|default}' id = '' maxlength = '4' type = 'text' name = 'rule[_0][bannerWidth_2]' onchange = "redirectNumber(this);" />
				<label>Высота: </label><input style = 'width:50px;' value = '{$request.tracker.rule._0.bannerHeight_2|default}' id = '' maxlength = '4' type = 'text' name = 'rule[_0][bannerHeight_2]' onchange = "redirectNumber(this);" />
				</div>
				</div>
				<div id = 'banner_rule_0_2'><div id = 'typeBanners_0_2'>
  		        <label id = 'tipBanner' > Баннер Traffic Back: </label><br>
				
				<div class = 'filed left' style = 'margin-right:10px;position:relative;'>
				<div style = 'width:150px;height:150px;position:absolute;top:0px;left:0px;cursor:pointer;' onclick = "showGallery('banner_web_img_0_2');" ></div>
				<div style = 'width:20px;height:20px;position:absolute;top:-10px;right:-20px;cursor:pointer; background-image: url("../../../public/img/add.png");' onclick = "showGallery('banner_web_img_0_2');" ></div>
				
				<img width = "150px;"  style = "max-height:300px;" id = 'banner_web_img_0_2' src = "{if $request.tracker.rule._0.trafficBackBanner_2 eq ''} {$request.Site}/public/img/addBanner.jpg {else} {$request.tracker.rule._0.trafficBackBanner_2} {/if}"/>
				<object svg = 'banner_web_img_0_2' style = 'width:150px;;height:150px;display:none;' type='application/x-shockwave-flash' data='{if $request.tracker.rule._0.trafficBackBanner_2 eq ''} {$request.Site}/public/img/addBanner.jpg {else} {$request.tracker.rule._0.trafficBackBanner_2} {/if}'></object>

				<input banner_web = 'banner_web_img_0_2' type = 'hidden' name = 'rule[_0][trafficBackBanner_2]' value = '{$request.tracker.rule._0.trafficBackBanner_2}'/></div>
				
				<script> var img_src = '{$request.tracker.rule._0.trafficBackBanner_2}'; var filesExt = ['swf']; var parts = img_src.split('.'); if(filesExt.join().search(parts[parts.length - 1]) != -1) { $('[svg = banner_web_img_0_2]').show(); $('#banner_web_img_0_2').hide(); } </script>
				</div></div>
				
				<div id = 'typeFullvideo_0_2'>
				<label> ID видео с Youtube: </label><br>
				<input type = 'text' name = 'rule[_0][trafficBackVideo_2]' value = '{$request.tracker.rule._0.trafficBackVideo_2|default}' maxlength = '15'/><br><br>
				</div>
				{if $request.tracker.id == ''} <script> $('#typeFullvideo_0_2').hide(); </script> {/if}
				
				<div class = 'clear'></div>
				
				<div class = 'filedRedirectSec' style = 'float:left;'><label id = 'tipBannersTimer'>Один показ формата каждые: </label><input value = '{$request.tracker.rule._0.bannersTimer}' id = '' maxlength = '6' type = 'text' name = 'rule[_0][bannersTimer]' onchange = "redirectNumber(this);" /><label> секунд (кроме форматов Banner,Redirect,Frame)</label></div>
					
				<div class = 'clear'></div>
					
				<div>
					<br>
					<label> Включить Coinhive: </label> <br>					
					<input type="radio" name="tracker[allowCOIN]" value="N" {if $request.tracker.allowCOIN == 'N'}checked{/if}> Нет
					<input type="radio" name="tracker[allowCOIN]" value="A" {if $request.tracker.allowCOIN == 'A'}checked{/if}> Non AdBlock
					<input type="radio" name="tracker[allowCOIN]" value="Y" {if $request.tracker.allowCOIN == 'Y'}checked{/if}> Default
				</div>

				<div>
					<label>CPU Coinhive: </label><input size = '2' value = '{$request.tracker.coinCPU}' maxlength = '2' type = 'text' name = 'tracker[coinCPU]' " /> %
				</div>
				
				
				<div>
					<label>Token Coinhive: </label><input size = '40' value = '{$request.tracker.coinToken}' maxlength = '32' type = 'text' name = 'tracker[coinToken]' " />
					
				</div>
				
			</div>	
			</div>	
			
	</div>
	{if $request.tracker.id != ''}<script> $('[name = "rule[_0][trafficBackType_2]"]').val('{$request.tracker.rule._0.trafficBackType_2}').change(); </script>{/if}	
	{if $request.tracker.id != ''}<script> $('[name = "rule[_0][trafficBackType_1]"]').val('{$request.tracker.rule._0.trafficBackType_1}').change(); </script>{/if}

{foreach from=$request.tracker.rule key=k item=rule}
{if $k != '_0'} 
		
<div class = 'ConteinerBanner hide' id = 'ConteinerBanner{$k}'>
			
			<div style = 'width:710px;padding:20px;'>
				<div>
				<h2> &darr; Настройка правила №{$k} &darr; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
				<span id = 'tipNameRule'>Название:</span><input style = 'color:black;' size = '9' maxlength = '10' type = 'text' name = 'rule[{$k}][name]' value = '{$rule.name}' onkeypress = "ruleName(this.value,'{$k}');" onchange = "ruleName(this.value,'{$k}');"/></h3>
				<div id = 'tipFlush' class = 'flush' onclick = 'flush ("{$k}");'>&bull; Очистить &bull;</div>
				</div> 
			<script>$('[name = "rule[{$k}][name]"]').val('{$rule.name}').change(); </script>
			<div class = 'firstColl'>
				
				<br>
				<div class = 'filedFilter'>
				<center><label id = 'tipTypeTrafic'> &darr; Выберите тип трафика &darr; </label>	</center>			
				    	<select name = "rule[{$k}][trafficType]" onchange="trafficType(this,'traffictype{$k}');" >
						<option value = 'web' style = 'color:blue;'>WEB трафик</option>
						<option value = 'wap' style = 'color:red;'>WAP трафик</option>
						</select> 
				</div>	
				
				
				<br>
				<div class = 'filedFilter'>
				<center><label id = 'tipType'> &darr; Выберите формат передачи трафика &darr; </label>	</center>			
				    	<select name = 'rule[{$k}][trafficTypeView]' onchange="trafficRuleType(this,'banner_rule{$k}','redirect_rule{$k}','{$k}');">
						{foreach $request.formats as $item}
						<option value = '{$item.name}'> {$item.name} </option>
						{/foreach}
						</select> 
				</div>
	
				<br>
				
				<label id = 'tipURLTraffic'> URL отдачи трафика: </label>
				<div class = 'filedUrl'> <input id = '' maxlength = '200' type = 'text' name = 'rule[{$k}][trafficURL]'  placeholder='http://' value = '{$rule.trafficURL}'/></div>
			
			     			   <span id = "blank{$k}" style = 'float:right;'><label id = 'tipBlank'>Новая вкладка </label><input type = 'checkbox' {if $rule.blank eq 'on'}checked{/if} name = 'rule[{$k}][blank]'/></span>
				<div id = 'banner_rule{$k}'>
				
				<div class = 'filedRedirectSec'><label id = 'tipLimitView'>Укажите лимит показов: </label><input value = '{$rule.limitView}' id = 'limitView{$k}' maxlength = '4' type = 'text' name = 'rule[{$k}][limitView]' onchange = "redirectNumber(this);limit(this,'limitClick{$k}');" /></div>
				<div class = 'filedRedirectSec'><label id = 'tipLimitClicks'>Укажите лимит кликов: </label><input value = '{$rule.limitClick}' id = 'limitClick{$k}' maxlength = '4' type = 'text' name = 'rule[{$k}][limitClick]' onchange = "redirectNumber(this);limit(this,'limitView{$k}');" /></div>
					<div id = 'typeVK{$k}' class = 'hide filedUrl'>
					<label id = 'tipVkTitle'> Заголовок сообщения VK: </label>
					<div class = 'filed'> <input maxlength = '15' type = 'text' name = "rule[{$k}][vkTitle]" value = '{$rule.vkTitle}'/></div>
					<label id = 'tipVkMsg'> Текст сообщения VK: </label>
					<div class = 'filed'> <input maxlength = '30' type = 'text' name = "rule[{$k}][vkMsg]" value = '{$rule.vkMsg}'/></div>
					</div>
					
					<div id = 'typeBanner{$k}' class = 'hide filedUrl'>
					<label id = 'tipBannerStyle'> Стиль баннера: </label>
						<div class = 'filed'>
						<br>
						<label>Ширина: </label><input style = 'width:50px;' value = '{$rule.bannerWidth|default}' id = '' maxlength = '4' type = 'text' name = 'rule[{$k}][bannerWidth]' onchange = "redirectNumber(this);" />
						<label>Высота: </label><input style = 'width:50px;' value = '{$rule.bannerHeight|default}' id = '' maxlength = '4' type = 'text' name = 'rule[{$k}][bannerHeight]' onchange = "redirectNumber(this);" />
						</div>
					</div>
				<div id = 'typeBanners{$k}'>
  		        <label id = 'tipBanner'> Баннер: </label><br>
				<div class = 'filed left' style = 'margin-right:10px;position:relative;'>
				<div style = 'width:150px;height:150px;position:absolute;top:0px;left:0px;cursor:pointer;' onclick = "showGallery('banner_web_img{$k}');" ></div>
				<div style = 'width:20px;height:20px;position:absolute;top:-10px;right:-20px;cursor:pointer; background-image: url("../../../public/img/add.png");' onclick = "showGallery('banner_web_img{$k}');" ></div>
				
				<img width = "150px;"  style = "max-height:300px;" height = "150px;" id = 'banner_web_img{$k}' src = "{if $rule.banner eq ''} {$request.Site}/public/img/addBanner.jpg {else} {$rule.banner} {/if}"/>
				<object svg = 'banner_web_img{$k}' style = 'width:150px;;height:150px;display:none;' type='application/x-shockwave-flash' data='{if $rule.banner eq ''} {$request.Site}/public/img/addBanner.jpg {else} {$rule.banner} {/if}'></object>
				<input banner_web = 'banner_web_img{$k}' type = 'hidden' name = 'rule[{$k}][banner]' value = '{$rule.banner}' /></div>
				
				<script> var img_src = '{$rule.banner}'; var filesExt = ['swf']; var parts = img_src.split('.'); if(filesExt.join().search(parts[parts.length - 1]) != -1) { $('[svg = banner_web_img{$k}]').show(); $('#banner_web_img{$k}').hide(); } </script>
				</div>
				<div class = 'filedFilterSelect'></div>
				</div>
				
				<div id = 'typeFullvideo{$k}'>
				<label> ID видео с Youtube: </label><br>
				<input type = 'text' name = 'rule[{$k}][video]' value = '{$rule.video|default}' maxlength = '15'/>
				</div>
				<div id = 'typeFullparams{$k}'>
				<div class = 'clear'></div><br>
				<label>Закрыть через: </label><input style = 'width:50px;' value = '{$rule.videoTimer|default}' id = '' maxlength = '6' type = 'text' name = 'rule[{$k}][videoTimer]' onchange = "redirectNumber(this);" /> <label>сек.</label><br><br>
				<label>Принудительный переход </label><input type = 'checkbox' {if $rule.videoGo|default eq 'on'}checked{/if} name = 'rule[{$k}][videoGo]'/>
				</div>
				
				<div id = 'redirect_rule{$k}' class = 'redirect_rule'>
				<div class = 'filedRedirectSec'><label id = 'tipRedirectTimer'>Сработать через: </label><input value = '{$rule.redirectTimer}' id = '' maxlength = '6' type = 'text' name = 'rule[{$k}][redirectTimer]' onchange = "redirectNumber(this);" /><label> Милисекунд</label></div>
				<div class = 'filedRedirectSec'><label id = 'tipRedirectLimitView'>Укажите лимит показов: </label><input value = '{$rule.redirectLimitView}' id = 'redirectLimitView{$k}' maxlength = '6' type = 'text' name = 'rule[{$k}][redirectLimitView]' onchange = "redirectNumber(this);" /></div>

				</div>
			</div>
			
			<div class = 'secondColl'>
			<br> 
			<table> <tr> <td>
			<div class = 'filedFilterSelect'>
				<label id = 'tipСountry'> Страна: </label><br>				
				    	<select onfocus="fliterBig(this);" onblur="fliterSmall(this);" name = 'rule[{$k}][filter][country][]' onchange = "referenceCity(this,'referenceCity{$k}');" multiple=true >
								{foreach $request.country as $item}

									{if $item.name eq ''}{else}
									<option value = '{$item.name}'> {$item.name} </option>
									{/if}
									
								{/foreach}
						</select> 
						
				{if $rule.filter.country|@count < 2}
					<script>$('[name = "rule[{$k}][filter][country][]"]').val('{$rule.filter.country.0}').change(); </script>
				{else}	
				{foreach $rule.filter.country as $item}
					<script>$('[name = "rule[{$k}][filter][country][]"] [value="{$item}"]').attr("selected", "selected"); </script>
				{/foreach}
				   <script>$('[name = "rule[{$k}][filter][country][]"]').change(); </script>
				{/if}
				
				</div>
			  </td><td>
				<div class = 'filedFilterSelect'>		
				<label id = 'tipCity'> Город: </label><br>					
				    	<select onfocus="fliterBig(this);" onblur="fliterSmall(this);" name = 'rule[{$k}][filter][city][]' id = 'referenceCity{$k}' multiple=true >
						</select> 
				{foreach $rule.filter.city as $item}
					<script>setTimeout(function() { $('[name = "rule[{$k}][filter][city][]"] [value="{$item}"]').attr("selected", "selected");},900); </script>
				{/foreach}			
				</div>
				 </td></tr>
				 <tr><td>
				
				<div class = 'filedFilterSelect'>	
				<label id = 'tipOS'> ОС: </label><br>					
				    	<select onfocus="fliterBig(this);" onblur="fliterSmall(this);" name = 'rule[{$k}][filter][OS][]' selecter = 'traffictype{$k}' multiple=true >
						{foreach $request.OS as $item}
								<option style = 'display:none;' value = '{$item.data}' traffictype{$k} = '{$item.type}'> {$item.name} </option>
						{/foreach}
						</select>
				{foreach $rule.filter.OS as $item}
					<script>setTimeout(function() { $('[name = "rule[{$k}][filter][OS][]"] [value="{$item}"]').attr("selected", "selected");},300); </script>
				{/foreach}							
				</div>
				</td><td>
				<div class = 'filedFilterSelect'>
				<label id = 'tipBrowser'> Браузер: </label><br>					
				    	<select onfocus="fliterBig(this);" onblur="fliterSmall(this);" name = 'rule[{$k}][filter][browser][]' selecter = 'traffictype{$k}' multiple=true >
						{foreach $request.browser as $item}
								<option style = 'display:none;' value = '{$item.data}' traffictype{$k} = '{$item.type}'> {$item.name} </option>
						{/foreach}
						</select> 
				{foreach $rule.filter.browser as $item}
					<script>setTimeout(function() { $('[name = "rule[{$k}][filter][browser][]"] [value="{$item}"]').attr("selected", "selected");},300); </script>
				{/foreach}
				</div>	
				</td></tr>
				<tr><td>
				<div class = 'filedFilterSelect'>
				<label id = 'tipLanguage'> Язык: </label><br>					
				    	<select onfocus="fliterBig(this);" onblur="fliterSmall(this);" name = 'rule[{$k}][filter][language][]' multiple=true >
						{foreach $request.language as $item}
								<option value = '{$item.data}'> {$item.name} </option>
						{/foreach}
						</select>
				{foreach $rule.filter.language as $item}
					<script>$('[name = "rule[{$k}][filter][language][]"] [value="{$item}"]').attr("selected", "selected"); </script>
				{/foreach}		
				</div>	
				 </td><td>
				
				<div class = 'filedFilterSelect'>
				<label id = 'tipReferals'> URL`s переходов: </label><br>					
				    	<textarea name = 'rule[{$k}][referals]'>{$rule.referals}</textarea>
				</div>	
				</td></tr>

			  <tr><td>
			  
				<div class = 'filedFilterSelect'>
				<label id = 'tipIpfiterWhite'> Белый IP список: </label><br>					
				    	<textarea name = 'rule[{$k}][ipfiterWhite]'>{$rule.ipfiterWhite}</textarea>
				</div>	
				</td><td>
				<div class = 'filedFilterSelect'>
				<label id = 'tipIpfiterBlack'> Черный IP список: </label><br>					
				    	<textarea name = 'rule[{$k}][ipfiterBlack]'>{$rule.ipfiterBlack}</textarea>
				</div>
				</td></tr>
			    <tr><td>
				<div class = 'filedFilterSelect'>
				<label id = 'tipTime'> Время работы (МСК): </label><br>
				<textarea name = 'rule[{$k}][time]'>{$rule.time}</textarea>
			  </div>
				</td>
				
			<td>
				
		
			  <div class = 'filedFilterSelect'>
				<label id = 'tipDay'> Дни работы: </label><br>
					<label>Пн </label><input type = 'checkbox' {if $rule.day1 eq 'on'}checked="checked"{/if} name = 'rule[{$k}][day1]'/>	
					<label>Вт </label><input type = 'checkbox' {if $rule.day2 eq 'on'}checked="checked"{/if} name = 'rule[{$k}][day2]'/>	
					<label>Ср </label><input type = 'checkbox' {if $rule.day3 eq 'on'}checked="checked"{/if} name = 'rule[{$k}][day3]'/>	
					<label>Чт </label><input type = 'checkbox' {if $rule.day4 eq 'on'}checked="checked"{/if} name = 'rule[{$k}][day4]'/>	
					<label>Пт </label><input type = 'checkbox' {if $rule.day5 eq 'on'}checked="checked"{/if} name = 'rule[{$k}][day5]'/><br>	
					<label>Сб </label><input type = 'checkbox' {if $rule.day6 eq 'on'}checked="checked"{/if} name = 'rule[{$k}][day6]'/>	
					<label>Вс </label><input type = 'checkbox' {if $rule.day0 eq 'on'}checked="checked"{/if} name = 'rule[{$k}][day0]'/>
				
				<br>	
				    
					<!-- <input type = 'checkbox' {if $rule.unreferal eq 'on'}checked{/if} name = 'rule[{$k}][unreferal]'/>	<label>Убрать referer</label> <br> -->
					<input type = 'checkbox' {if $rule.unrule eq 'on'}checked{/if} name = 'rule[{$k}][unrule]'/> <label id = 'tipUnrule'>Отключить Правило</label>
					

   			  </div>
			  
			  
			  
				</td></tr>
				<tr><td>
				<!--
				<div class = 'clear'></div>
			  	<div>
					<br>
					<label> Coinhive: </label> <br> 
					<input type="radio" name="rule[{$k}][allowCOIN]" value="N" {if $rule.allowCOIN == 'N'}checked{/if}> Нет
					<input type="radio" name="rule[{$k}][allowCOIN]" value="A" {if $rule.allowCOIN == 'A'}checked{/if}> Non AdBlock
					<input type="radio" name="rule[{$k}][allowCOIN]" value="Y" {if $rule.allowCOIN == 'Y'}checked{/if}> Default
				</div>

				<div>
					<label>CPU Coinhive: </label><input size = '2' value = '{$rule.coinCPU}' maxlength = '2' type = 'text' name = 'rule[{$k}][coinCPU]' /> %
				</div>
				
				
				<div>
					<label>Token Coinhive: </label><input size = '40' value = '{$rule.coinToken}' maxlength = '32' type = 'text' name = 'rule[{$k}][coinToken]' />
					
				</div>
				-->
				</td> <td>
				</td>
				</tr>
				
				</table>
			</div>
			</div>	
			
			
</div>		

	
				<script>
				$('[name = "rule[{$k}][trafficType]"]').val('{$rule.trafficType}').change();
				$('[name = "rule[{$k}][trafficTypeView]"]').val('{$rule.trafficTypeView}').change();
				</script>
				
	
{/if}
{/foreach}	


			

</div>
		</td>
		</tr>
		</table>
</form>
	 
	 
	 	 
	 
	 
<div id = 'galleryDiv' class = 'galleryDiv'>
	     <div class = 'galleryDivClose' id = 'galleryDivClose'></div> 
		<div id = "galleryDivContent" class = "galleryDivContent">
	 <iframe id = "iframeGallery" src="" width="100%" height="90%" align="center" frameborder="no"></iframe>
		</div>	
</div>	 
	 
	 

	
<div class = 'ButtonA'><a href = '{$request.Site}/trackers/'>Список потоков</a></div>	 
		 
</div>
	 





