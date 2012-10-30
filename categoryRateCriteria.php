<?php include "config.php" ;
	if(!(isset($_SESSION['userid']))){
		echo "<script>parent.location.reload();</script>";
	}
	require_once CLASS_PATH."/juryRateCase.php";
	$juryRateObj = new JuryRateCase();
	$cat_title = "";
	$group_no = "";
	$categoryFinal = ZERO;
		
	if(isset($_SESSION['admin_view_userid'])){
		$userid=$_SESSION['admin_view_userid'];
		
	}else{
		$userid=$_SESSION['userid'];
		
	}
	if(isset($_POST['param'])){ //when case is rated.....
		if($_POST['param'] == 'CaseRate') {
			$cat_title = $_REQUEST['cat_title'];
			$group_no = $_REQUEST['group_id'];
			$retVal = $juryRateObj->saveRatings($userid);
			if($retVal == ONE){
				$act = RATINGS_UPDATE_SUCCESS;
			}else{
				$act = RATINGS_UPDATE_FAILURE;
			}
			$smarty->assign("act",$act);
			$selectFlag = $juryRateObj->isCategoryFinal($cat_title);
			$smarty->assign("selectFlag",$selectFlag);
		}
	}else{
		if(isset($_REQUEST['group_id'])){ //at loading time
			$strArr = explode('/',$_REQUEST['dir']);
			$cat_title = $strArr[1];
			$group_no = $_REQUEST['group_id'];
			$categoryFinal = $juryRateObj->isCategoryFinal($cat_title);
			$smarty->assign("categoryFinal",$categoryFinal);
		}
	}
	$smarty->assign("group_id",$group_no);
	$smarty->assign("dir",$_REQUEST['dir']);
	$note = $juryRateObj->getNote($group_no,$userid);
	$enterdRatings = $juryRateObj->getEnteredRatings($group_no,$userid);
	$smarty->assign("enterdRatings",$enterdRatings);
	$smarty->assign("category_title",$cat_title);
	$smarty->assign("note",stripslashes($note[0]['note']));
	$smarty->assign("juryinvolved",$note[0]['jury_involved']);
	$caseTitle=  $juryRateObj->getCaseTitle($group_no);
	$wrapFileName = $juryRateObj->getCategoryTitle($cat_title);
	$Casejurynote = $juryRateObj->getCasejurynote($group_no,$_SESSION['userid']);
	$smarty->assign("Caseheading",$wrapFileName." - ".$caseTitle);
	if($Casejurynote == ''){
		$Casejurynote = array();
		$smarty->assign("Casejurynote",$Casejurynote);
	}
	else
		$smarty->assign("Casejurynote",$Casejurynote);
	$retVal = $juryRateObj->getCriteriaDetails($cat_title);
	$smarty->assign("rateCriteria",$retVal);
	$smarty->display("categoryRateCriteria.tpl");
?> 