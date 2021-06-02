<script language='JavaScript' src='{$request.Site}/public/js/news.js' type='text/javascript'></script>
<input id = 'Site' type = 'hidden' value = '{$request.Site}'>
<title>TDSTraff - Новости</title>

<div class = 'Conteiner'>

	<h1> Новости</h1> <hr>
	
<table class = "newsTable">


<tr>
<td width="20%">
<div class = "newsList">
 {if $request.news eq 'none'} 
    <center><b>Список новостей пуст</b></center> 
   {else}
   {foreach $request.news as $item}
	  
	  <div class = "newsTime">{$item.timestamp}</div>
	  <div id = "link{$item.id}" class = "newsLink" onclick = "newsshow('{$item.id}');">{$item.subject}</div>

   {/foreach}
   {/if}




</div>
</td>

<td width="70%">
<div class = "newsDitail">
 {if $request.news eq 'none'} 
    <center><b>Список новостей пуст</b></center> 
   {else}
   {foreach $request.news as $item}
	  
	<div class = "hidden" id = "message{$item.id}"> {$item.message} </div>

   {/foreach}
   {/if}

</div>
</td>
</tr>

</table>


</div>