<?php
require_once "config.php";
require_once CLASS_PATH."/class.ammamember.php";
require_once CLASS_PATH."/awardCategory.php";
$act='';
if (isset($_POST['Subscribe']) && $_POST['Subscribe'] =="Subscribe"){
   $AmmamemberObj = new ammaMember();	  
   $retVal = ZERO;
   $retVal = $AmmamemberObj->addMember();

	if($retVal == ONE){
		
		$act= SUBSCRIBE_SUCCESS;
	}elseif($retVal == ZERO){
		
		$act= USEREXIST;
	}elseif($retVal == MINUSONE){
		
		$act= EMAIL_DUPLICATE;
	}else{
		
		$act = SUBSCRIBE_FAILURE;
	}
	
}
$categoryObj = new awardCategory();
$retArr = $categoryObj->getPageContents('new_subscribe');
$smarty->assign("new_subscribe", $retArr[0]);
$smarty->assign('act', $act);
$smarty->assign("pageTitle","Inschrijfformulier");
$smarty->display('subscribe.tpl');
?>