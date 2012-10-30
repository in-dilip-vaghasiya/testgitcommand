<?php
require_once "config.php";

if((!(isset($_SESSION['userid']))) ||  ( ($_SESSION['usertype'] != 'U') && ($_SESSION['usertype'] != 'A'))) {
		header( 'Location:login.php?sessionout=1' );
}
require_once CLASS_PATH."/awardCategory.php";
$AwardCategory = new awardCategory();
if(isset($_GET['addcase'])){// new case create
	$retVal = $AwardCategory->getCategory();
	$introtextArr = $AwardCategory->getPageContents('category_intro_text');
	$smarty->assign("introtextArr",$introtextArr[0]);
	$smarty->assign("retVal",$retVal);
	$smarty->assign("pageTitle","Selecteer een categorie 1/3");
	$smarty->display("case_step_1.tpl");
}elseif (isset($_POST['FinalizeCases'])){// to finalize the cases
	if(isset($_POST['submitCasesCheckBox'])){
		
		$retVal = $AwardCategory->makeCaseFinal();
		if($retVal[0] == ZERO){
			$cat = $AwardCategory->getSubmmisionCategoryId($retVal[1]);
			echo "<script> alert ('". CaseIncomplte."');</script>";
			echo "<script language='javascript'>location.href='edit_cases.php?group_no=".$retVal[1]."&cat=".$cat."&action_type=record_edit'</script>";
		}elseif($retVal[0] == MINUSONE){
			$cat = $AwardCategory->getSubmmisionCategoryId($retVal[1]);
			$msg = maxWordLimitExceeds.$retVal[2].'\\n        Pas uw case aan.';
			echo "<script> alert ('". $msg ." ');</script>";
			echo "<script language='javascript'>location.href='edit_cases.php?group_no=".$retVal[1]."&cat=".$cat."&action_type=record_edit'</script>";
		}
	}	
	echo "<script language='javascript'>window.location='".REDIRECT_PAGE_USER."'</script>";
}elseif (isset($_POST['UnFinalizeCases'])){// to un finalize the cases
	if(isset($_POST['finalCasesCheckBox'])){
		$retVal = $AwardCategory->makeCaseUnFinal();
	}	
	echo "<script language='javascript'>window.location='".REDIRECT_PAGE_USER."'</script>";
}
?>