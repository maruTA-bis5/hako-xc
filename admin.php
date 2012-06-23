<?php

//xoops admin header
include("../../mainfile.php");
include_once(XOOPS_ROOT_PATH."/class/xoopsmodule.php");
include(XOOPS_ROOT_PATH."/include/cp_functions.php");
//xoops admin header
if ( $xoopsUser ) {
	$xoopsModule = XoopsModule::getByDirname("hako");
	if ( !$xoopsUser->isAdmin($xoopsModule->mid()) ) { 
		redirect_header(XOOPS_URL."/",3,_NOPERM);
		exit();
	}
} else {
	redirect_header(XOOPS_URL."/",3,_NOPERM);
	exit();
}
	xoops_cp_header();
	OpenTable();
//

//xoops admin footer
	CloseTable();
	xoops_cp_footer();
//
?>