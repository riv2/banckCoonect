<?php /* Smarty version Smarty-3.1.21-dev, created on 2018-01-28 17:18:30
         compiled from "/var/www/tds/core/view/tpl/header.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2321887365a423eae49b5d0-66487201%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '320f450bd8a182d808d01a29c0b2ec7ba62a317f' => 
    array (
      0 => '/var/www/tds/core/view/tpl/header.tpl',
      1 => 1517149099,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2321887365a423eae49b5d0-66487201',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.21-dev',
  'unifunc' => 'content_5a423eae4e6378_71264586',
  'variables' => 
  array (
    'Site' => 0,
    'ADMIN' => 0,
    'request' => 0,
    'cDomains' => 0,
    'cTrackers' => 0,
    'cSupport' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5a423eae4e6378_71264586')) {function content_5a423eae4e6378_71264586($_smarty_tpl) {?><div class = "headerMain">
<div >
<span class = ' fade '><a href = "<?php echo $_smarty_tpl->tpl_vars['Site']->value;?>
"><img src = "<?php echo $_smarty_tpl->tpl_vars['Site']->value;?>
/public/img/authorized/logo.jpg" /></a> </span>

<?php if ($_smarty_tpl->tpl_vars['ADMIN']->value=='true') {?>
<div class = "admin_menu">
	<span class = ' fade '><a <?php if ($_smarty_tpl->tpl_vars['request']->value['category']=='adminNews') {?> class = "admin_item_active" <?php }?> href="<?php echo $_smarty_tpl->tpl_vars['Site']->value;?>
/adminNews/"><?php echo $_smarty_tpl->tpl_vars['request']->value['LANG']['header']['menuNewsAdm'];?>
</a> </span>
	<span class = ' fade '><a <?php if ($_smarty_tpl->tpl_vars['request']->value['category']=='adminFaq') {?> class = "admin_item_active" <?php }?> href="<?php echo $_smarty_tpl->tpl_vars['Site']->value;?>
/adminFaq/"><?php echo $_smarty_tpl->tpl_vars['request']->value['LANG']['header']['menuFAQAdm'];?>
</a> </span>
	<span class = ' fade '><a <?php if ($_smarty_tpl->tpl_vars['request']->value['category']=='adminUsers') {?> class = "admin_item_active" <?php }?> href="<?php echo $_smarty_tpl->tpl_vars['Site']->value;?>
/adminUsers/"><?php echo $_smarty_tpl->tpl_vars['request']->value['LANG']['header']['menuUsersAdm'];?>
</a> </span>
	<span class = ' fade '><a <?php if ($_smarty_tpl->tpl_vars['request']->value['category']=='adminTraffic') {?> class = "admin_item_active" <?php }?> href="<?php echo $_smarty_tpl->tpl_vars['Site']->value;?>
/adminTraffic/"><?php echo $_smarty_tpl->tpl_vars['request']->value['LANG']['header']['menuOurTraffAdm'];?>
</a> </span><br> 
	<span class = ' fade '><a <?php if ($_smarty_tpl->tpl_vars['request']->value['category']=='adminMail') {?> class = "admin_item_active" <?php }?> href="<?php echo $_smarty_tpl->tpl_vars['Site']->value;?>
/adminMail/"><?php echo $_smarty_tpl->tpl_vars['request']->value['LANG']['header']['menuMailAdm'];?>
</a></span>
	<span class = ' fade '><a <?php if ($_smarty_tpl->tpl_vars['request']->value['category']=='adminDomains') {?> class = "admin_item_active" <?php }?> href="<?php echo $_smarty_tpl->tpl_vars['Site']->value;?>
/adminDomains/"><?php echo $_smarty_tpl->tpl_vars['request']->value['LANG']['header']['menuDomainsAdm'];?>
<span <?php if ($_smarty_tpl->tpl_vars['cDomains']->value>0) {?> class = "red"<?php }?>>(<?php echo $_smarty_tpl->tpl_vars['cDomains']->value;?>
)</span></a> </span>
</div>
<?php }?>	

	<div class = "menu_auth">
	<span class = "ilogin"> </span><span class = "itext"><?php echo $_smarty_tpl->tpl_vars['request']->value['LANG']['header']['hello'];?>
 <b><?php echo $_smarty_tpl->tpl_vars['request']->value['login'];?>
</b> </span>
	<span class = "iexit"> </span><span class = "iahref  fade "><a href="<?php echo $_smarty_tpl->tpl_vars['Site']->value;?>
/auth/logout"><?php echo $_smarty_tpl->tpl_vars['request']->value['LANG']['header']['exit'];?>
</a></span>
	</div>
</div>

<div class = "poloska"></div>

<div>
<span class = "menu_item fade <?php if ($_smarty_tpl->tpl_vars['request']->value['category']=='listtrackers'||$_smarty_tpl->tpl_vars['request']->value['category']=='newtracker') {?>menu_active<?php }?>"> <a href="<?php echo $_smarty_tpl->tpl_vars['Site']->value;?>
/trackers/"><?php echo $_smarty_tpl->tpl_vars['request']->value['LANG']['header']['potoks'];
if ($_smarty_tpl->tpl_vars['ADMIN']->value=='true') {?><span <?php if ($_smarty_tpl->tpl_vars['cTrackers']->value>0) {?> class = "red"<?php }?>>(<?php echo $_smarty_tpl->tpl_vars['cTrackers']->value;?>
)</span><?php }?></a> </span>
<span class = "menu_item load <?php if ($_smarty_tpl->tpl_vars['request']->value['category']=='statistic') {?>menu_active<?php }?> "> <a href="<?php echo $_smarty_tpl->tpl_vars['Site']->value;?>
/statistic/"><?php echo $_smarty_tpl->tpl_vars['request']->value['LANG']['header']['analyz'];?>
</a></span>
<span class = "menu_item fade <?php if ($_smarty_tpl->tpl_vars['request']->value['category']=='domains') {?>menu_active<?php }?>"> <a href="<?php echo $_smarty_tpl->tpl_vars['Site']->value;?>
/domains/"><?php echo $_smarty_tpl->tpl_vars['request']->value['LANG']['header']['domains'];?>
</a></span>
<span class = "menu_item fade <?php if ($_smarty_tpl->tpl_vars['request']->value['category']=='top') {?>menu_active<?php }?>"> <a href="<?php echo $_smarty_tpl->tpl_vars['Site']->value;?>
/top/"><?php echo $_smarty_tpl->tpl_vars['request']->value['LANG']['header']['top'];?>
</a></span>
<span class = "menu_item fade <?php if ($_smarty_tpl->tpl_vars['request']->value['category']=='news') {?>menu_active<?php }?>"> <a href="<?php echo $_smarty_tpl->tpl_vars['Site']->value;?>
/news/"><?php echo $_smarty_tpl->tpl_vars['request']->value['LANG']['header']['news'];?>
</a></span>
<span class = "menu_item fade <?php if ($_smarty_tpl->tpl_vars['request']->value['category']=='faq') {?>menu_active<?php }?>"> <a href="<?php echo $_smarty_tpl->tpl_vars['Site']->value;?>
/faq/"><?php echo $_smarty_tpl->tpl_vars['request']->value['LANG']['header']['faq'];?>
</a></span>
<span class = "menu_item fade <?php if ($_smarty_tpl->tpl_vars['request']->value['category']=='support') {?>menu_active<?php }?>"> <a href="<?php echo $_smarty_tpl->tpl_vars['Site']->value;?>
/support/"><?php echo $_smarty_tpl->tpl_vars['request']->value['LANG']['header']['tech'];
if ($_smarty_tpl->tpl_vars['ADMIN']->value=='true') {?><span <?php if ($_smarty_tpl->tpl_vars['cSupport']->value>0) {?> class = "red"<?php }?>>(<?php echo $_smarty_tpl->tpl_vars['cSupport']->value;?>
)</span><?php }?></a></span>
<span class = "menu_item fade <?php if ($_smarty_tpl->tpl_vars['request']->value['category']=='profile') {?>menu_active<?php }?>"> <a href="<?php echo $_smarty_tpl->tpl_vars['Site']->value;?>
/profile/"><?php echo $_smarty_tpl->tpl_vars['request']->value['LANG']['header']['profile'];?>
</a></span>

<span class = "menu_item"> <a id = "rules"><?php echo $_smarty_tpl->tpl_vars['request']->value['LANG']['header']['rules'];?>
</a></span>
             
</div>
<div class = "poloska_menu"></div>

</div>

			<?php }} ?>
