<div class = "headerMain">
<div >
<span class = ' fade '><a href = "{$Site}"><img src = "{$Site}/public/img/authorized/logo.jpg" /></a> </span>

{if $ADMIN eq 'true'}
<div class = "admin_menu">
	<span class = ' fade '><a {if $request.category eq 'adminNews'} class = "admin_item_active" {/if} href="{$Site}/adminNews/">{$request.LANG.header.menuNewsAdm}</a> </span>
	<span class = ' fade '><a {if $request.category eq 'adminFaq'} class = "admin_item_active" {/if} href="{$Site}/adminFaq/">{$request.LANG.header.menuFAQAdm}</a> </span>
	<span class = ' fade '><a {if $request.category eq 'adminUsers'} class = "admin_item_active" {/if} href="{$Site}/adminUsers/">{$request.LANG.header.menuUsersAdm}</a> </span>
	<span class = ' fade '><a {if $request.category eq 'adminTraffic'} class = "admin_item_active" {/if} href="{$Site}/adminTraffic/">{$request.LANG.header.menuOurTraffAdm}</a> </span><br> 
	<span class = ' fade '><a {if $request.category eq 'adminMail'} class = "admin_item_active" {/if} href="{$Site}/adminMail/">{$request.LANG.header.menuMailAdm}</a></span>
	<span class = ' fade '><a {if $request.category eq 'adminDomains'} class = "admin_item_active" {/if} href="{$Site}/adminDomains/">{$request.LANG.header.menuDomainsAdm}<span {if $cDomains > 0} class = "red"{/if}>({$cDomains})</span></a> </span>
</div>
{/if}	

	<div class = "menu_auth">
	<span class = "ilogin"> </span><span class = "itext">{$request.LANG.header.hello} <b>{$request.login}</b> </span>
	<span class = "iexit"> </span><span class = "iahref  fade "><a href="{$Site}/auth/logout">{$request.LANG.header.exit}</a></span>
	</div>
</div>

<div class = "poloska"></div>

<div>
<span class = "menu_item fade {if $request.category eq 'listtrackers' or $request.category eq 'newtracker'}menu_active{/if}"> <a href="{$Site}/trackers/">{$request.LANG.header.potoks}{if $ADMIN eq 'true'}<span {if $cTrackers > 0} class = "red"{/if}>({$cTrackers})</span>{/if}</a> </span>
<span class = "menu_item load {if $request.category eq 'statistic'}menu_active{/if} "> <a href="{$Site}/statistic/">{$request.LANG.header.analyz}</a></span>
<span class = "menu_item fade {if $request.category eq 'domains'}menu_active{/if}"> <a href="{$Site}/domains/">{$request.LANG.header.domains}</a></span>
<span class = "menu_item fade {if $request.category eq 'top'}menu_active{/if}"> <a href="{$Site}/top/">{$request.LANG.header.top}</a></span>
<span class = "menu_item fade {if $request.category eq 'news'}menu_active{/if}"> <a href="{$Site}/news/">{$request.LANG.header.news}</a></span>
<span class = "menu_item fade {if $request.category eq 'faq'}menu_active{/if}"> <a href="{$Site}/faq/">{$request.LANG.header.faq}</a></span>
<span class = "menu_item fade {if $request.category eq 'support'}menu_active{/if}"> <a href="{$Site}/support/">{$request.LANG.header.tech}{if $ADMIN eq 'true'}<span {if $cSupport > 0} class = "red"{/if}>({$cSupport})</span>{/if}</a></span>
<span class = "menu_item fade {if $request.category eq 'profile'}menu_active{/if}"> <a href="{$Site}/profile/">{$request.LANG.header.profile}</a></span>

<span class = "menu_item"> <a id = "rules">{$request.LANG.header.rules}</a></span>
             
</div>
<div class = "poloska_menu"></div>

</div>

			