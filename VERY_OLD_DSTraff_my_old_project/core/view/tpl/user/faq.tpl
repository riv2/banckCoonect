<script language='JavaScript' src='{$request.Site}/public/js/faq.js' type='text/javascript'></script>


<input id = 'Site' type = 'hidden' value = '{$request.Site}'>
<title>TDSTraff - FAQ</title>

<div class = 'Conteiner'>

	<h1> Часто Задаваемые Вопросы и Ответы</h1> <hr>
	
<table class = "faqTable">


<tr>
<td width="20%">
<div class = "faqList">
 {if $request.faq eq 'none'} 
    <center><b>Список faq пуст</b></center> 
   {else}
   {foreach $request.faq as $item}
	  
	  <div id = "link{$item.id}" class = "faqLink" onclick = "faqshow('{$item.id}');">{$item.subject}</div>

   {/foreach}
   {/if}




</div>
</td>

<td width="70%">
<div class = "faqDitail">

 {if $request.faq eq 'none'} 
    <center><b>Список faq пуст</b></center> 
   {else}

   {foreach $request.faq as $item}
	  
	<div class = "hidden" id = "message{$item.id}"> {$item.message} </div>

   {/foreach}
   {/if}

</div>
</td>
</tr>

</table>






</div>