<?php
require_once "config.php";
if(!(isset($_SESSION['userid']))){
	header( 'Location:login.php?sessionout=1' );
}

	require_once CLASS_PATH."/juryRateCase.php";
	require_once CLASS_PATH."/awardCategory.php";
	require_once "getnominee.php";
	
	
	
	$smarty->assign("catId", $catId);//contents category names
	$smarty->assign("pageTitle","Cases Nomineren");
	$smarty->display("case_nominee.tpl"); 
?>
