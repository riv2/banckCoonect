<script language='JavaScript' src='{$request.Site}/public/js/admintraffic.js' type='text/javascript'></script>
<input id = "Site" type = "hidden" value = "{$request.Site}">
<title>TDSTraff - Администрирование - Трафик</title>

<div class = "Conteiner">

<h1> Администрирование - Трафик</h1> <hr>
 
<table class = "table" >

<tr>
<th> № </th>
<th> Тип </th>
<th> Название </th>
<th width = '50px;'> Nный </th>
<th> Тематика </th>
<th> Трекер </th>
<th> Настройка трафика </th>
<th class = 'smallAT'> R </th>
<th class = 'smallAT'> F </th>
<th class = 'smallAT'> B </th>
<th class = 'smallAT'> A </th>
<th class = 'smallAT'> C</th>
<th class = 'smallAT'> P</th>
<th class = 'smallAT'> R</th>
<th class = 'smallAT'> T</th>
<th class = 'smallAT'> VK</th>
<th class = 'smallAT'> CU</th>
<th class = 'smallAT'> F</th>
<th class = 'smallAT'> FV</th>
<th> Действие </th>
</tr>

<form action = '{$request.Site}/adminTraffic/digest/' method = 'POST'>
<tr>
<td width = '20px;'>0</td>
<td width = '50px;'> 
<select name = 'typeTraffic'>
<option value = 'wap'>WAP</option>
<option value = 'web'>WEB</option>
</select>
</td>
<td width = '150px;'> <input maxlength = '30' style = 'width:150px;' type = 'text' name = 'name' /></td>
<td width = '50px;'> <input maxlength = '10' style = 'width:50px;' type = 'text' name = 'clicks' /></td>
<td width = '100px;'>     	
	<select name = "theme" style = 'width: 100px;'>
	<option value = 'Весь' > Весь трафик </option>
	{foreach $request.theme as $item}
	<option value = '{$item.name}' > {$item.name} </option>
	{/foreach}
	</select> 
</td>
<td width = '100px;'> 
	<select name = "trackerId" style = 'width: 100px;'>
	<option value = ''>Все</option>
	{foreach $request.trackers as $item}
	<option value = '{$item.id}' > ({$item.id}){$item.name} </option>
	{/foreach}
	</select>
</td>
<td width = '150px;'> <center>Доступны после создания правила</center> </td> 
<td> </td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
<td width = '150px;'> <center> <input style = 'width:100px;'  type = 'submit' value = 'Создать' name = 'action'>  </center></td>
</tr>
</form>


   <tr>
		<td style = 'background-color:rgba(0,0,0,0.5);' colspan="22"></td>
   </tr>

{if ($request.adminTraffic != 'none')} 
{foreach from=$request.adminTraffic key=k item=traffic}
 
<form id = 'form_{$traffic.id}' action = '{$request.Site}/adminTraffic/digest/' method = 'POST'>
<input type = 'hidden' value = '{$traffic.id}' name = 'id'>

<tr class = '{if $traffic.typeTraffic == 'web'}blueTr{else}redTr{/if}'>
<td> {$traffic.id} </td>
<td width = '50px;'> 
<select name = 'typeTraffic'>
<option value = 'web' {if $traffic.typeTraffic == 'web'}selected{/if}>WEB</option> 
<option value = 'wap' {if $traffic.typeTraffic == 'wap'}selected{/if}>WAP</option> 
</select>
</td>
<td>  <input maxlength = '30' style = 'width:150px;' type = 'text' name = 'name' value = '{$traffic.name}' /></td>
<td > <input maxlength = '10' style = 'width:50px;' type = 'text' name = 'clicks' value = '{$traffic.clicks}' /></td>
<td> 
<select name = "theme"  style = 'width: 100px;'>
<option value = 'Весь' > Весь трафик </option>
{foreach $request.theme as $item}
<option value = '{$item.name}' {if $traffic.theme == $item.name} selected {/if}> {$item.name} </option>
{/foreach}
</select> 
</td>

<td> 
	<select name = "trackerId"  style = 'width: 100px;'>
	<option value = ''>Все</option>
	{foreach $request.trackers as $item}
	<option value = '{$item.id}' {if ($traffic.trackerId == $item.id)} selected {/if}> ({$item.id}){$item.name} </option>
	{/foreach}
	</select> 
</td>

<td> <div class = 'traffic center' onclick = "showAll('{$traffic.id}');"> Показать/Скрыть Все настройки</div> </td>

<td class = 'center'><span class = 'smallAT'>{$traffic.wapRedirect|default}{$traffic.webRedirect|default}</span></td>
<td class = 'center'><span class = 'smallAT'>{$traffic.wapFrame|default}{$traffic.webFrame|default}</span></td>
<td class = 'center'><span class = 'smallAT'>{$traffic.wapBanner|default}{$traffic.webBanner|default}</span></td>
<td class = 'center'><span class = 'smallAT'>{$traffic.wapAdspot|default}{$traffic.webAdspot|default}</span></td>
<td class = 'center'><span class = 'smallAT'>{$traffic.wapCatfish|default}{$traffic.webCatfish|default}</span></td>
<td class = 'center'><span class = 'smallAT'>{$traffic.wapPopunder|default}{$traffic.webPopunder|default}</span></td>
<td class = 'center'><span class = 'smallAT'>{$traffic.wapRichmedia|default}{$traffic.webRichmedia|default}</span></td>
<td class = 'center'><span class = 'smallAT'>{$traffic.wapTopline|default}{$traffic.webTopline|default}</span></td>
<td class = 'center'><span class = 'smallAT'>{$traffic.wapVKMessage|default}{$traffic.webVKMessage|default}</span></td>
<td class = 'center'><span class = 'smallAT'>{$traffic.wapClickUnder|default}{$traffic.webClickUnder|default}</span></td>
<td class = 'center'><span class = 'smallAT'>{$traffic.wapFullscreen|default}{$traffic.webFullscreen|default}</span></td>
<td class = 'center'><span class = 'smallAT'>{$traffic.wapFullvideo|default}{$traffic.webFullvideo|default}</span></td>
<td>  <center> <input type = 'submit' style = 'width:100px;' value = 'Ред.' name = 'action'> <input onclick = "
		swal({
		title: 'Удалить правило?',
		text: 'Правило будет удалено',
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#DD6B55',
		confirmButtonText: 'Да, удалить'
		},
		function(){
		$('#del_{$traffic.id}').attr('name','action');
		$('#del_{$traffic.id}').attr('value','Удалить');
		$('#form_{$traffic.id}').submit();
		});  																				" type = 'button' style = 'width:100px;' value = 'Удалить' name = 'action'>
<input type = 'hidden' id = 'del_{$traffic.id}' >
 </center> </td>

</tr>

<tr id = 'tr_{$traffic.id}' class='hide'>
	<td  colspan="22" style = "background-color:rgba(255,255,255,0.9);">

		<table><tr>
		<td>
		<b> Banner URL: </b><br>
		<div><input style = 'width:100px;' onfocus = 'inputBig(this);' onblur = 'inputSmall(this);'  type = 'text' name = 'url_Banner' value = '{$traffic.url_Banner}' />  </div><br>
		<div id = 'typeBanner{$traffic.id}'>
		<div class = 'filed left' style = 'margin-right:20px;position:relative;'>
		<div style = 'width:100px;height:100px;position:absolute;top:0px;left:0px;cursor:pointer;' onclick = "showGallery('Banner_img{$traffic.id}');" ></div>
		<div style = 'width:20px;height:20px;position:absolute;top:-10px;right:-20px;cursor:pointer; background-image: url("../../../public/img/add.png");' onclick = "showGallery('Banner_img{$traffic.id}');" ></div>
				
		<img width = "100px;"  style = "max-height:300px;" height = "100px;" id = 'Banner_img{$traffic.id}' src = "{if $traffic.img_Banner eq ''} {$request.Site}/public/img/addBanner.jpg {else} {$traffic.img_Banner} {/if}"/>
		<object svg = 'Banner_img{$traffic.id}' style = 'width:100px;height:100px;display:none;' type='application/x-shockwave-flash' data='{if $traffic.img_Banner eq ''} {$request.Site}/public/img/addBanner.jpg {else} {$traffic.img_Banner} {/if}'></object>
		<input banner_web = 'Banner_img{$traffic.id}' type = 'hidden' name = 'img_Banner' value = '{$traffic.img_Banner}' /></div>
		<script> var img_src = '{$traffic.img_Banner}'; var filesExt = ['swf']; var parts = img_src.split('.'); if(filesExt.join().search(parts[parts.length - 1]) != -1) { $('[svg = Banner_img{$traffic.id}]').show(); $('#Banner_img{$traffic.id}').hide(); } </script>
		</div>
		</td>
		
			<td>
		<b> Adspot URL: </b><br>
		<div><input style = 'width:100px;' onfocus = 'inputBig(this);' onblur = 'inputSmall(this);'  type = 'text' name = 'url_Adspot' value = '{$traffic.url_Adspot}' />  </div><br>
		<div id = 'typeAdspot{$traffic.id}'>
		<div class = 'filed left' style = 'margin-right:10px;position:relative;'>
		<div style = 'width:100px;height:100px;position:absolute;top:0px;left:0px;cursor:pointer;' onclick = "showGallery('Adspot_img{$traffic.id}');" ></div>
		<div style = 'width:20px;height:20px;position:absolute;top:-10px;right:-20px;cursor:pointer; background-image: url("../../../public/img/add.png");' onclick = "showGallery('Adspot_img{$traffic.id}');" ></div>
	
		<img width = "100px;"  style = "max-height:300px;" height = "100px;" id = 'Adspot_img{$traffic.id}' src = "{if $traffic.img_Adspot eq ''} {$request.Site}/public/img/addAdspot.jpg {else} {$traffic.img_Adspot} {/if}"/>
		<object svg = 'Adspot_img{$traffic.id}' style = 'width:100px;height:100px;display:none;' type='application/x-shockwave-flash' data='{if $traffic.img_Adspot eq ''} {$request.Site}/public/img/addBanner.jpg {else} {$traffic.img_Adspot} {/if}'></object>
		<input banner_web = 'Adspot_img{$traffic.id}' type = 'hidden' name = 'img_Adspot' value = '{$traffic.img_Adspot}' /></div>
		<script> var img_src = '{$traffic.img_Adspot}'; var filesExt = ['swf']; var parts = img_src.split('.'); if(filesExt.join().search(parts[parts.length - 1]) != -1) { $('[svg = Adspot_img{$traffic.id}]').show(); $('#Adspot_img{$traffic.id}').hide(); } </script>
		</div>
		</td>
		
				<td>
		<b> Catfish URL: </b><br>
		<div><input style = 'width:100px;' onfocus = 'inputBig(this);' onblur = 'inputSmall(this);'  type = 'text' name = 'url_Catfish' value = '{$traffic.url_Catfish}' />  </div><br>
		<div id = 'typeCatfish{$traffic.id}'>
		<div class = 'filed left' style = 'margin-right:10px;position:relative;'>
		<div style = 'width:100px;height:100px;position:absolute;top:0px;left:0px;cursor:pointer;' onclick = "showGallery('Catfish_img{$traffic.id}');" ></div>
		<div style = 'width:20px;height:20px;position:absolute;top:-10px;right:-20px;cursor:pointer; background-image: url("../../../public/img/add.png");' onclick = "showGallery('Catfish_img{$traffic.id}');" ></div>
	
		<img width = "100px;"  style = "max-height:300px;" height = "100px;" id = 'Catfish_img{$traffic.id}' src = "{if $traffic.img_Catfish eq ''} {$request.Site}/public/img/addBanner.jpg {else} {$traffic.img_Catfish} {/if}"/>
		<object svg = 'Catfish_img{$traffic.id}' style = 'width:100px;;height:100px;display:none;' type='application/x-shockwave-flash' data='{if $traffic.img_Catfish eq ''} {$request.Site}/public/img/addBanner.jpg {else} {$traffic.img_Catfish} {/if}'></object>
		<input banner_web = 'Catfish_img{$traffic.id}' type = 'hidden' name = 'img_Catfish' value = '{$traffic.img_Catfish}' /></div>
		<script> var img_src = '{$traffic.img_Catfish}'; var filesExt = ['swf']; var parts = img_src.split('.'); if(filesExt.join().search(parts[parts.length - 1]) != -1) { $('[svg = Catfish_img{$traffic.id}]').show(); $('#Catfish_img{$traffic.id}').hide(); } </script>
		</div>
		</td>
	
				<td>
		<b> Popunder URL: </b><br>
		<div><input style = 'width:100px;' onfocus = 'inputBig(this);' onblur = 'inputSmall(this);'  type = 'text' name = 'url_Popunder' value = '{$traffic.url_Popunder}' />  </div><br>
		<div id = 'typePopunder{$traffic.id}'>
		<div class = 'filed left' style = 'margin-right:10px;position:relative;'>
		<div style = 'width:100px;height:100px;position:absolute;top:0px;left:0px;cursor:pointer;' onclick = "showGallery('Popunder_img{$traffic.id}');" ></div>
		<div style = 'width:20px;height:20px;position:absolute;top:-10px;right:-20px;cursor:pointer; background-image: url("../../../public/img/add.png");' onclick = "showGallery('Popunder_img{$traffic.id}');" ></div>
	
		<img width = "100px;"  style = "max-height:300px;" height = "100px;" id = 'Popunder_img{$traffic.id}' src = "{if $traffic.img_Popunder eq ''} {$request.Site}/public/img/addBanner.jpg {else} {$traffic.img_Popunder} {/if}"/>
		<object svg = 'Popunder_img{$traffic.id}' style = 'width:100px;;height:100px;display:none;' type='application/x-shockwave-flash' data='{if $traffic.img_Popunder eq ''} {$request.Site}/public/img/addBanner.jpg {else} {$traffic.img_Popunder} {/if}'></object>
		<input banner_web = 'Popunder_img{$traffic.id}' type = 'hidden' name = 'img_Popunder' value = '{$traffic.img_Popunder}' /></div>
		<script> var img_src = '{$traffic.img_Popunder}'; var filesExt = ['swf']; var parts = img_src.split('.'); if(filesExt.join().search(parts[parts.length - 1]) != -1) { $('[svg = Popunder_img{$traffic.id}]').show(); $('#Popunder_img{$traffic.id}').hide(); } </script>
		</div>
		</td>
		
				<td>
		<b> Richmedia URL: </b><br>
		<div><input style = 'width:100px;' onfocus = 'inputBig(this);' onblur = 'inputSmall(this);'  type = 'text' name = 'url_Richmedia' value = '{$traffic.url_Richmedia}' />  </div><br>
		<div id = 'typeRichmedia{$traffic.id}'>
		<div class = 'filed left' style = 'margin-right:10px;position:relative;'>
		<div style = 'width:100px;height:100px;position:absolute;top:0px;left:0px;cursor:pointer;' onclick = "showGallery('Richmedia_img{$traffic.id}');" ></div>
		<div style = 'width:20px;height:20px;position:absolute;top:-10px;right:-20px;cursor:pointer; background-image: url("../../../public/img/add.png");' onclick = "showGallery('Richmedia_img{$traffic.id}');" ></div>
	
		<img width = "100px;"  style = "max-height:300px;" height = "100px;" id = 'Richmedia_img{$traffic.id}' src = "{if $traffic.img_Richmedia eq ''} {$request.Site}/public/img/addBanner.jpg {else} {$traffic.img_Richmedia} {/if}"/>
		<object svg = 'Richmedia_img{$traffic.id}' style = 'width:100px;;height:100px;display:none;' type='application/x-shockwave-flash' data='{if $traffic.img_Richmedia eq ''} {$request.Site}/public/img/addBanner.jpg {else} {$traffic.img_Richmedia} {/if}'></object>
		<input banner_web = 'Richmedia_img{$traffic.id}' type = 'hidden' name = 'img_Richmedia' value = '{$traffic.img_Richmedia}' /></div>
		<script> var img_src = '{$traffic.img_Richmedia}'; var filesExt = ['swf']; var parts = img_src.split('.'); if(filesExt.join().search(parts[parts.length - 1]) != -1) { $('[svg = Richmedia_img{$traffic.id}]').show(); $('#Richmedia_img{$traffic.id}').hide(); } </script>
		</div>
		</td>
		
				<td>
		<b> Topline URL: </b><br>
		<div><input style = 'width:100px;' onfocus = 'inputBig(this);' onblur = 'inputSmall(this);'  type = 'text' name = 'url_Topline' value = '{$traffic.url_Topline}' />  </div><br>
		<div id = 'typeTopline{$traffic.id}'>
		<div class = 'filed left' style = 'margin-right:10px;position:relative;'>
		<div style = 'width:100px;height:100px;position:absolute;top:0px;left:0px;cursor:pointer;' onclick = "showGallery('Topline_img{$traffic.id}');" ></div>
		<div style = 'width:20px;height:20px;position:absolute;top:-10px;right:-20px;cursor:pointer; background-image: url("../../../public/img/add.png");' onclick = "showGallery('Topline_img{$traffic.id}');" ></div>
	
		<img width = "100px;"  style = "max-height:300px;" height = "100px;" id = 'Topline_img{$traffic.id}' src = "{if $traffic.img_Topline eq ''} {$request.Site}/public/img/addBanner.jpg {else} {$traffic.img_Topline} {/if}"/>
		<object svg = 'Topline_img{$traffic.id}' style = 'width:100px;;height:100px;display:none;' type='application/x-shockwave-flash' data='{if $traffic.img_Topline eq ''} {$request.Site}/public/img/addBanner.jpg {else} {$traffic.img_Topline} {/if}'></object>
		<input banner_web = 'Topline_img{$traffic.id}' type = 'hidden' name = 'img_Topline' value = '{$traffic.img_Topline}' /></div>
		<script> var img_src = '{$traffic.img_Topline}'; var filesExt = ['swf']; var parts = img_src.split('.'); if(filesExt.join().search(parts[parts.length - 1]) != -1) { $('[svg = Topline_img{$traffic.id}]').show(); $('#Topline_img{$traffic.id}').hide(); } </script>
		</div>
		</td>
		
		<td>
		<b> VKMessage URL: </b><br>
		<div><input style = 'width:100px;' onfocus = 'inputBig(this);' onblur = 'inputSmall(this);'  type = 'text' name = 'url_VKMessage' value = '{$traffic.url_VKMessage}' />  </div><br>
		<div id = 'typeVKMessage{$traffic.id}'>
		<div class = 'filed left' style = 'margin-right:10px;position:relative;'>
		<div style = 'width:100px;height:100px;position:absolute;top:0px;left:0px;cursor:pointer;' onclick = "showGallery('VKMessage_img{$traffic.id}');" ></div>
		<div style = 'width:20px;height:20px;position:absolute;top:-10px;right:-20px;cursor:pointer; background-image: url("../../../public/img/add.png");' onclick = "showGallery('VKMessage_img{$traffic.id}');" ></div>
	
		<img width = "100px;"  style = "max-height:300px;" height = "100px;" id = 'VKMessage_img{$traffic.id}' src = "{if $traffic.img_VKMessage eq ''} {$request.Site}/public/img/addBanner.jpg {else} {$traffic.img_VKMessage} {/if}"/>
		<object svg = 'VKMessage_img{$traffic.id}' style = 'width:100px;;height:100px;display:none;' type='application/x-shockwave-flash' data='{if $traffic.img_VKMessage eq ''} {$request.Site}/public/img/addBanner.jpg {else} {$traffic.img_VKMessage} {/if}'></object>
		<input banner_web = 'VKMessage_img{$traffic.id}' type = 'hidden' name = 'img_VKMessage' value = '{$traffic.img_VKMessage}' /></div>
		<script> var img_src = '{$traffic.img_VKMessage}'; var filesExt = ['swf']; var parts = img_src.split('.'); if(filesExt.join().search(parts[parts.length - 1]) != -1) { $('[svg = VKMessage_img{$traffic.id}]').show(); $('#VKMessage_img{$traffic.id}').hide(); } </script>
		</div>
		</td>
		
		<td>
		<b> Fullscreen URL: </b><br>
		<div><input style = 'width:100px;' onfocus = 'inputBig(this);' onblur = 'inputSmall(this);'  type = 'text' name = 'url_Fullscreen' value = '{$traffic.url_Fullscreen}' />  </div><br>
		<div id = 'typeFullscreen{$traffic.id}'>
		<div class = 'filed left' style = 'margin-right:10px;position:relative;'>
		<div style = 'width:100px;height:100px;position:absolute;top:0px;left:0px;cursor:pointer;' onclick = "showGallery('Fullscreen_img{$traffic.id}');" ></div>
		<div style = 'width:20px;height:20px;position:absolute;top:-10px;right:-20px;cursor:pointer; background-image: url("../../../public/img/add.png");' onclick = "showGallery('Fullscreen_img{$traffic.id}');" ></div>
	
		<img width = "100px;"  style = "max-height:300px;" height = "100px;" id = 'Fullscreen_img{$traffic.id}' src = "{if $traffic.img_Fullscreen eq ''} {$request.Site}/public/img/addBanner.jpg {else} {$traffic.img_Fullscreen} {/if}"/>
		<object svg = 'Fullscreen_img{$traffic.id}' style = 'width:100px;;height:100px;display:none;' type='application/x-shockwave-flash' data='{if $traffic.img_Fullscreen eq ''} {$request.Site}/public/img/addBanner.jpg {else} {$traffic.img_Fullscreen} {/if}'></object>
		<input banner_web = 'Fullscreen_img{$traffic.id}' type = 'hidden' name = 'img_Fullscreen' value = '{$traffic.img_Fullscreen}' /></div>
		<script> var img_src = '{$traffic.img_Fullscreen}'; var filesExt = ['swf']; var parts = img_src.split('.'); if(filesExt.join().search(parts[parts.length - 1]) != -1) { $('[svg = Fullscreen_img{$traffic.id}]').show(); $('#Fullscreen_img{$traffic.id}').hide(); } </script>
		</div>
		</td>
		
						<td>
		<b> Fullvideo URL: </b><br>
		<div><input style = 'width:100px;' onfocus = 'inputBig(this);' onblur = 'inputSmall(this);'  type = 'text' name = 'url_Fullvideo' value = '{$traffic.url_Fullvideo}' />  </div><br>
		<b> ID Youtube: </b><br>
		<input style = 'width:100px;' onfocus = 'inputBig(this);' onblur = 'inputSmall(this);' type = 'text' name = 'img_Fullvideo' value = '{$traffic.img_Fullvideo}' maxlength = '15'/></div>
		</td>
		
				<td>
		<b> ClickUnder URL: </b><br>
		<div><input style = 'width:100px;' onfocus = 'inputBig(this);' onblur = 'inputSmall(this);'  type = 'text' name = 'url_ClickUnder' value = '{$traffic.url_ClickUnder}' />  </div><br>
		</td>
		
				<td>
		<b> Redirect URL: </b><br>
		<div><input style = 'width:100px;' onfocus = 'inputBig(this);' onblur = 'inputSmall(this);'  type = 'text' name = 'url_Redirect' value = '{$traffic.url_Redirect}' />  </div><br>
		</td>
		
				<td>
		<b> Frame URL: </b><br>
		<div><input style = 'width:100px;' onfocus = 'inputBig(this);' onblur = 'inputSmall(this);'  type = 'text' name = 'url_Frame' value = '{$traffic.url_Frame}' />  </div><br>
		</td>
				<td>
		<b> Convert Format: </b><br>
		<div>
		<select name = 'convertFormat' style = 'width:100%;'>
		<option value = 'N' {if $traffic.convertFormat == 'N'}selected{/if}>Нет</option>
		<option value = 'Y' {if $traffic.convertFormat == 'Y'}selected{/if}>Да</option>
		</select>
		</div><br>
		<b> IP Filter White: </b><br>
		<div>
		<textarea name = 'ipFilterWhite' style = 'height:90px;width:200px;font-size:10px;'>{$traffic.ipFilterWhite}</textarea>
		</div>
		</td>
		
		</tr></table>
			
		
	</td>
</tr>

</form>	
 <tr>
		<td  colspan="22" style = "background-color:rgba(0,0,0,0.2);"></td>
 </tr>
{/foreach}
{/if}
</table>


<div id = 'galleryDiv' class = 'galleryDiv'>
	     <div class = 'galleryDivClose' id = 'galleryDivClose'></div> 
		<div id = "galleryDivContent" class = "galleryDivContent">
	 <iframe id = "iframeGallery" src="" width="100%" height="90%" align="center" frameborder="no"></iframe>
		</div>	
</div>	




</div> 