<?php /* Smarty version Smarty-3.1.21-dev, created on 2016-08-16 16:52:10
         compiled from "/var/www/html/core/view/tpl/frontpage.tpl" */ ?>
<?php /*%%SmartyHeaderCode:81037665657b31a8a98ebc8-13934223%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8ca68a4d97c480c9ee3a9a61d5e2d0848de5f16c' => 
    array (
      0 => '/var/www/html/core/view/tpl/frontpage.tpl',
      1 => 1468248305,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '81037665657b31a8a98ebc8-13934223',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'request' => 0,
    'Version' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.21-dev',
  'unifunc' => 'content_57b31a8aa0bae8_27073079',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_57b31a8aa0bae8_27073079')) {function content_57b31a8aa0bae8_27073079($_smarty_tpl) {?><div class = "header"> 
<div class = "logo"><center><img src = "public/img/notauthorized/logo.jpg" /></center></div>
<?php if ($_smarty_tpl->tpl_vars['request']->value['is_modile']=='FALSE') {?>
<div class = "contacts"> 
<span class = 'skype'></span> <span class = 'tskype selectOn'>dstraff.support </span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span class = 'email'></span><span class = 'temail selectOn'>support@dstraff.ru </span>
</div>
<?php }?>
<?php if ($_smarty_tpl->tpl_vars['request']->value['is_modile']=='FALSE') {?>
<div class = "forum"> 
<span class = 'tskype'><a target = '_blank' href = 'http://forum.dstraff.ru/index.php/topic,4.0.html'><?php echo $_smarty_tpl->tpl_vars['request']->value['LANG']['notauthorized']['invite'];?>
</a></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class = 'tskype'><a target = '_blank' href = 'http://forum.dstraff.ru/'><?php echo $_smarty_tpl->tpl_vars['request']->value['LANG']['notauthorized']['forum'];?>
 </a></span><br>
</div>
<?php }?>
<div class = "conteiner">

	<div class = "auth" id = "auth" <?php if ($_smarty_tpl->tpl_vars['request']->value['is_modile']=='TRUE') {?>style = "top:38%;left:5%;" <?php }?>>
	<form id = "loginFormGo" action = "auth/login/" method = "POST">
	<?php if ($_smarty_tpl->tpl_vars['request']->value['is_modile']=='FALSE') {?>		<div class = "aHeader"> <?php echo $_smarty_tpl->tpl_vars['request']->value['LANG']['notauthorized']['enter'];?>
</div> 	<?php } else { ?>
	<span style = "color:black;"> <center><?php echo $_smarty_tpl->tpl_vars['request']->value['LANG']['notauthorized']['mobile'];?>
</center></span>
	<?php }?>
		<div class = "aInput"> <span class = 'ilogin'></span><input type = "text" name = "login" maxlength = "30" required id = "login"  placeholder = "<?php echo $_smarty_tpl->tpl_vars['request']->value['LANG']['notauthorized']['plogin'];?>
" /></div>
		<div class = "aInput"> <span class = 'ipass'></span><input type = "password" name = "password" maxlength = "30"  required id = "password"  placeholder = "<?php echo $_smarty_tpl->tpl_vars['request']->value['LANG']['notauthorized']['ppass'];?>
" /></div>
		<div class = "aButton"> <input type = "submit" id = "submitAuth" value = "<?php echo $_smarty_tpl->tpl_vars['request']->value['LANG']['notauthorized']['penter'];?>
"/></div>
	<?php if ($_smarty_tpl->tpl_vars['request']->value['is_modile']=='FALSE') {?>	<div class = "aHref"><a id = "regButton"> <?php echo $_smarty_tpl->tpl_vars['request']->value['LANG']['notauthorized']['reg'];?>
 </a></div> <?php }?>
	</form>
	</div>
	<?php if ($_smarty_tpl->tpl_vars['request']->value['is_modile']=='FALSE') {?>
	<div class = "reg" id = "reg">
		<form id = "regForm">
                <div class = "rHeader"> <?php echo $_smarty_tpl->tpl_vars['request']->value['LANG']['notauthorized']['reg'];?>
 </div>
                <div class = "rInput"><span class = 'ilogin'></span><input type="text" name="User[login]" maxlength = "30" required placeholder="<?php echo $_smarty_tpl->tpl_vars['request']->value['LANG']['notauthorized']['plogin'];?>
" id = "loginReg"/></div>
                <div class = "rInput"><span class = 'ipass'></span><input type="password" name="User[pass]" maxlength = "30" required placeholder="<?php echo $_smarty_tpl->tpl_vars['request']->value['LANG']['notauthorized']['ppass'];?>
" id = "pass"/></div>
                <div class = "rInput"><span class = 'ipass'></span><input type="password" name="User[passConfirm]" maxlength = "30" required placeholder="<?php echo $_smarty_tpl->tpl_vars['request']->value['LANG']['notauthorized']['ppass2'];?>
" id = "passConfirm"/></div>
                <div class = "rInput"><span class = 'iemail'></span><input type="email" name="User[email]" maxlength = "50" required placeholder="<?php echo $_smarty_tpl->tpl_vars['request']->value['LANG']['notauthorized']['email'];?>
" id = "email"/></div>
                <div class = "rInput"><span class = 'iinvite'></span><input type="text" name="User[invite]" maxlength = "32" required placeholder="<?php echo $_smarty_tpl->tpl_vars['request']->value['LANG']['notauthorized']['invite'];?>
" id = "invite"/></div>
				<div class = "rButton"> <input type = "button" id = "regSubmit" value = "<?php echo $_smarty_tpl->tpl_vars['request']->value['LANG']['notauthorized']['reg2'];?>
"/></div>
                <div class = "rHref"><a id = "loginButton"> <?php echo $_smarty_tpl->tpl_vars['request']->value['LANG']['notauthorized']['enter'];?>
 </a></div>
        </form>	
	</div>
	<?php }?>
</div>


<div class = "footer">
<center>
<div class = "copyrg">&copy; <?php echo $_smarty_tpl->tpl_vars['request']->value['LANG']['notauthorized']['copy'];?>
 <?php echo $_smarty_tpl->tpl_vars['Version']->value;?>
</div>
</center>
<div class = "counter"> 
<!--LiveInternet counter--><?php echo '<script'; ?>
 type="text/javascript"><!--
document.write("<a href='//www.liveinternet.ru/click' "+
"target=_blank><img src='//counter.yadro.ru/hit?t25.6;r"+
escape(document.referrer)+((typeof(screen)=="undefined")?"":
";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?
screen.colorDepth:screen.pixelDepth))+";u"+escape(document.URL)+
";"+Math.random()+
"' alt='' title='LiveInternet: показано число посетителей за"+
" сегодня' "+
"border='0' width='88' height='15'><\/a>")
//--><?php echo '</script'; ?>
><!--/LiveInternet-->

</div>
</div><?php }} ?>
