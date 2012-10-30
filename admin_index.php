<?php
require_once "config.php";
require_once CLASS_PATH."/class.ammamember.php";
require_once CLASS_PATH."/awardCategory.php";
//require_once "getnominee.php";
if(!(isset($_SESSION['userid']))){
	header( 'Location:login.php?sessionout=1' );
}
if(isset($_SESSION['admin_view_userid'])){
	unset($_SESSION["admin_view_userid"]);
}

if(isset($_GET['column'])){
	$column =$_GET['column']; 
	if(($_GET['flag'] == "")) {
		$flag = $_GET['column'];
	}
}else{
	$column ="";
	$flag ="";
}
$order =  'ASC';
if(isset($_GET['flag'])){
	if(($_GET['flag'] != "")) {
		if($_GET['flag'] =  $_GET['column']){
			$order =  'DESC';
			$flag= "";
		}
	}
}
$smarty->assign("flag", $flag);
$AmmamemberObj = new ammaMember();
$CategoryObj = new awardCategory();
$AmmamemberObj->login_recheck();

$retVal = $CategoryObj->getPageContents('',$column,$order);
$smarty->assign("contentDetails", $retVal);
$smarty->assign("pageTitle","Overzicht Cases");
$smarty->display("admin_index.tpl");exit;
?>