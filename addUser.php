<!-- testing 1 -->
<?php
require_once "config.php";
if(!(isset($_SESSION['userid']))){
	header( 'Location:login.php?sessionout=1' );
}
$act='';
	require_once CLASS_PATH."/class.ammamember.php";
	require_once CLASS_PATH."/juryRateCase.php";
	require_once CLASS_PATH."/awardCategory.php";
	//require_once "getnominee.php";
if (isset($_POST['Subscribe']) && $_POST['Subscribe'] =="Subscribe"){
   $AmmamemberObj = new ammaMember();	  
   $retVal = ZERO;
    $retVal = $AmmamemberObj->addMemberFromAdmin();

	if($retVal == ONE){
		$act= USER_ADDED;
	}elseif($retVal == ZERO){
		$act= USEREXIST;
	}elseif($retVal == MINUSONE){
		$act= EMAIL_DUPLICATE;
	}else{
		$act = SUBSCRIBE_FAILURE;
	}
}


$categoryObj = new awardCategory();
$categoryArr = $categoryObj->getCategory();
$retArr = $categoryObj->getPageContents('new_subscribe');
$smarty->assign("new_subscribe", $retArr[0]);
$smarty->assign('act', $act);
$smarty->assign("catId", $categoryArr);//contents category names
//$smarty->assign("pageTitle","Add user");
$smarty->assign("pageTitle","Gebruiker toevoegen");

$smarty->display("addUser.tpl"); 
?>
