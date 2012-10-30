<?php
	require_once "config.php";
	require_once CLASS_PATH."/juryRateCase.php";
	require_once "getnominee.php";
	$flag = 'Selecteer een jury';
	$pageTitle="Jury Logboek";
	$selectTitle = "Jury";
	$juryObj = new juryRateCase();
	if(isset($_REQUEST['type'])&& ($_REQUEST['type'] == 'U')){
		$retVal = $juryObj->getJury('U');
		$flag = 'Selecteer een gebruiker';
		$pageTitle="Gebruiker Logboek";
		$selectTitle = "Gebruiker";
	}else{
		$retVal = $juryObj->getJury();
	}
	$smarty->assign("flag",$flag);
	$smarty->assign("selectTitle",$selectTitle);
	$smarty->assign("retVal",$retVal);
	$smarty->assign("pageTitle",$pageTitle);
	$smarty->display("admin_select_jury.tpl");
?>