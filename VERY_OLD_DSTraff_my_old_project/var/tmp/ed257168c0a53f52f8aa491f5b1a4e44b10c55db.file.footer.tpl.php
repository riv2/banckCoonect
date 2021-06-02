<?php /* Smarty version Smarty-3.1.21-dev, created on 2016-08-16 16:20:56
         compiled from "/var/www/html/core/view/tpl/footer.tpl" */ ?>
<?php /*%%SmartyHeaderCode:132764317757b31338c93947-79340511%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ed257168c0a53f52f8aa491f5b1a4e44b10c55db' => 
    array (
      0 => '/var/www/html/core/view/tpl/footer.tpl',
      1 => 1450363346,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '132764317757b31338c93947-79340511',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'request' => 0,
    'Version' => 0,
    'ADMIN' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.21-dev',
  'unifunc' => 'content_57b31338c97160_12234039',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_57b31338c97160_12234039')) {function content_57b31338c97160_12234039($_smarty_tpl) {?>        <hr/>        <div class = "footer">            <p class="small"><?php echo $_smarty_tpl->tpl_vars['request']->value['LANG']['footer']['copy'];?>
 <?php echo $_smarty_tpl->tpl_vars['Version']->value;?>
</p>        </div><div class = "counter"><!--LiveInternet counter--><?php echo '<script'; ?>
 type="text/javascript"><!--document.write("<a href='//www.liveinternet.ru/click' "+<?php if ($_smarty_tpl->tpl_vars['ADMIN']->value=='true') {?>"target=_blank><img src='//counter.yadro.ru/hit?t29.6;r"+<?php } else { ?>"target=_blank><img src='//counter.yadro.ru/hit?t25.6;r"+<?php }?>escape(document.referrer)+((typeof(screen)=="undefined")?"":";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?screen.colorDepth:screen.pixelDepth))+";u"+escape(document.URL)+";"+Math.random()+"' alt='' title='LiveInternet: показано количество просмотров и"+" посетителей' "+<?php if ($_smarty_tpl->tpl_vars['ADMIN']->value=='true') {?>"border='0' width='88' height='120'><\/a>")<?php } else { ?>"border='0' width='88' height='15'><\/a>")<?php }?>//--><?php echo '</script'; ?>
><!--/LiveInternet--></div>
<?php }} ?>
