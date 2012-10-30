<?php
	require_once "config.php";
	require_once CLASS_PATH."/awardCategory.php";
	if((!(isset($_SESSION['userid']))) ||  (($_SESSION['usertype'] != 'U') && ($_SESSION['usertype'] != 'A'))) {
		header( 'Location:login.php?sessionout=1' );
	}
	$awardCatObj = new awardCategory();
	$sentEmail = array();
	if(isset($_POST['txtEmail'])){
		$awardCatObj->makeFinalcase($_POST['hidGroupno'],$_POST['txtEmail']);
		$sentEmail[] = $_POST['txtEmail'];
		if($_POST['txtComm1email'] && !in_array(trim($_POST['txtComm1email']),$sentEmail,true)){
			$awardCatObj->makeFinalcase($_POST['hidGroupno'],$_POST['txtComm1email']);
			$sentEmail[] = trim($_POST['txtComm1email']);
		}
		if($_POST['txtComm2email'] && !in_array(trim($_POST['txtComm2email']),$sentEmail,true)){
			$awardCatObj->makeFinalcase($_POST['hidGroupno'],$_POST['txtComm2email']);
			$sentEmail[] = trim($_POST['txtComm2email']);
		}
		if($_POST['txtComm3email'] && !in_array(trim($_POST['txtComm3email']),$sentEmail,true)){
			$awardCatObj->makeFinalcase($_POST['hidGroupno'],$_POST['txtComm3email']);
			$sentEmail[] = trim($_POST['txtComm3email']);
		}
		header( 'Location:account_overview.php?status=success');
		exit;		
	}
	
	if(isset($_REQUEST['radCases'])){
		$arr = explode('#',$_REQUEST['radCases']);
		$email = $awardCatObj->getCaseemail($arr[0]);
		foreach($email as $mail){
			$smarty->assign($mail['field_name'],$mail['field_value']);
		}
		$smarty->assign('group_no',$arr[0]);
		$smarty->assign('cat_id',$arr[1]);
		$smarty->display("casefinal.tpl");
	}else{
		header( 'Location:account_overview.php');
	}