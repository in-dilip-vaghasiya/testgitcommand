<?php
require_once "config.php";
require_once CLASS_PATH."/class.ammamember.php";
require_once CLASS_PATH."/awardCategory.php";

if((!(isset($_SESSION['userid']))) ||  ( ($_SESSION['usertype'] != 'U') && ($_SESSION['usertype'] != 'A'))) {
	header( 'Location:login.php?sessionout=1' );
}
if(isset($_GET['status'])){
	$smarty->assign("act", $_GET['status']);
}
if(isset($_SESSION['categoryFieldValue'])){
	$fileTemp = SUBMITTED_FORMS_PATH."/".$_SESSION['categoryFieldValue']['category_code']."/".$_SESSION['categoryFieldValue']['group_id']."/".$_SESSION['categoryFieldValue']['group_id']."_temp.txt" ;
	
	if(file_exists($fileTemp)){
		unlink($fileTemp);
	}
	unset($_SESSION["categoryFieldValue"]);
}
$AmmamemberObj = new ammaMember();
$categoryObj = new awardCategory();
if(isset($_GET['loginname'])){
	$_SESSION['admin_view_username'] = $_GET['loginname'];
	$_SESSION['admin_view_userid'] = $_GET['userid'];
}
if(isset($_SESSION['admin_view_userid'])){
	$userid=$_SESSION['admin_view_userid'];
	$loginname= $_SESSION['admin_view_username'];
}else{
	$userid=$_SESSION['userid'];
	$loginname="";
}
$retArr = $AmmamemberObj->RetrieveMSSqlMember($loginname);



$retVal = $retArr[0];

$smarty->assign("memberArray", $retVal);
$retVal = $categoryObj->submmitedUserCases($userid);

$smarty->assign("submitedCategoryForm", $retVal);

$retVal = $categoryObj->getFinalCases($userid);

$smarty->assign("finalCases", $retVal);

$retArr = $categoryObj->getPageContents('overview_list_post');
$smarty->assign("overview_list_post", $retArr[0]);

$retArr = $categoryObj->getPageContents('overview_case_final');
$smarty->assign("overview_case_final", $retArr[0]);

$smarty->assign("pageTitle","Uw Account");
$smarty->display("account_overview.tpl");exit;
?>