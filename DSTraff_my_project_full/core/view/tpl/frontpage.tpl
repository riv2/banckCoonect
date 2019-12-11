<div class = "header"> 
<div class = "logo"><center><img src = "public/img/notauthorized/logo.jpg" /></center></div>
{if $request.is_modile eq 'FALSE'}
<div class = "contacts"> 
<span class = 'skype'></span> <span class = 'tskype selectOn'>kornienko_rivigor </span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span class = 'email'></span><span class = 'temail selectOn'>riv_1988@mail.ru </span>
</div>
{/if}
{if $request.is_modile eq 'FALSE'}

{/if}
<div class = "conteiner">

	<div class = "auth" id = "auth" {if $request.is_modile eq 'TRUE'}style = "top:38%;left:5%;" {/if}>
	<form id = "loginFormGo" action = "auth/login/" method = "POST">
	{if $request.is_modile eq 'FALSE'}		<div class = "aHeader"> {$request.LANG.notauthorized.enter}</div> 	{else}
	<span style = "color:black;"> <center>{$request.LANG.notauthorized.mobile}</center></span>
	{/if}
		<div class = "aInput"> <span class = 'ilogin'></span><input type = "text" name = "login" maxlength = "30" required id = "login"  placeholder = "{$request.LANG.notauthorized.plogin}" /></div>
		<div class = "aInput"> <span class = 'ipass'></span><input type = "password" name = "password" maxlength = "30"  required id = "password"  placeholder = "{$request.LANG.notauthorized.ppass}" /></div>
		<div class = "aButton"> <input type = "submit" id = "submitAuth" value = "{$request.LANG.notauthorized.penter}"/></div>
	{if $request.is_modile eq 'FALSE'}	<div class = "aHref"><a id = "regButton"> {$request.LANG.notauthorized.reg} </a></div> {/if}
	</form>
	</div>
	{if $request.is_modile eq 'FALSE'}
	<div class = "reg" id = "reg">
		<form id = "regForm">
                <div class = "rHeader"> {$request.LANG.notauthorized.reg} </div>
                <div class = "rInput"><span class = 'ilogin'></span><input type="text" name="User[login]" maxlength = "30" required placeholder="{$request.LANG.notauthorized.plogin}" id = "loginReg"/></div>
                <div class = "rInput"><span class = 'ipass'></span><input type="password" name="User[pass]" maxlength = "30" required placeholder="{$request.LANG.notauthorized.ppass}" id = "pass"/></div>
                <div class = "rInput"><span class = 'ipass'></span><input type="password" name="User[passConfirm]" maxlength = "30" required placeholder="{$request.LANG.notauthorized.ppass2}" id = "passConfirm"/></div>
                <div class = "rInput"><span class = 'iemail'></span><input type="email" name="User[email]" maxlength = "50" required placeholder="{$request.LANG.notauthorized.email}" id = "email"/></div>
                <div class = "rInput"><span class = 'iinvite'></span><input type="text" name="User[invite]" maxlength = "32" required placeholder="{$request.LANG.notauthorized.invite}" id = "invite"/></div>
				<div class = "rButton"> <input type = "button" id = "regSubmit" value = "{$request.LANG.notauthorized.reg2}"/></div>
                <div class = "rHref"><a id = "loginButton"> {$request.LANG.notauthorized.enter} </a></div>
        </form>	
	</div>
	{/if}
</div>


<div class = "footer">
<center>
<div class = "copyrg">&copy; {$request.LANG.notauthorized.copy} {$Version}</div>
</center>
<div class = "counter"> 
<!--LiveInternet counter--><script type="text/javascript"><!--
document.write("<a href='//www.liveinternet.ru/click' "+
"target=_blank><img src='//counter.yadro.ru/hit?t25.6;r"+
escape(document.referrer)+((typeof(screen)=="undefined")?"":
";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?
screen.colorDepth:screen.pixelDepth))+";u"+escape(document.URL)+
";"+Math.random()+
"' alt='' title='LiveInternet: показано число посетителей за"+
" сегодня' "+
"border='0' width='88' height='15'><\/a>")
//--></script><!--/LiveInternet-->

</div>
</div>