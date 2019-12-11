<?php 
require_once(smartyDIR); 
$smarty = new Smarty();     
$smarty->template_dir = tlpDir;
$smarty->compile_dir  = tlpCache;      
$smarty->assign('notauthorizedCss', notauthorizedCss);
$smarty->assign('authorizedCss', authorizedCss);
$smarty->assign('initJs', initJs);
$smarty->assign('jQuery', jQuery);
$smarty->assign('Site', Site); 
$smarty->assign('Version', Version); 
$smarty->assign('ADMIN', ADMIN); 
$smarty->assign('countInvites', countInvites);
$smarty->assign('request', $request); 