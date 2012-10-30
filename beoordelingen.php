<!-- testing 1 -->
<?php
	require_once "config.php";
	if(!(isset($_SESSION['userid']))){
		header( 'Location:login.php?sessionout=1' );
	}

	require_once CLASS_PATH."/awardCategory.php";
	require_once CLASS_PATH."/juryRateCase.php";
	$caseObj = new juryRateCase();
	$CategoryObj = new awardCategory();
	$retVal = ($_SESSION['user_type'] != 'A') ? $CategoryObj->getJuryCategory($_SESSION['userid']) : $CategoryObj->getCategory();
	if(isset($_REQUEST['nocaseCat_id'])){	
		foreach($retVal as $val){
			if($val['award_category_id'] == $_REQUEST['nocaseCat_id']){
				$cattitle = $val['category_code'];
				$cat_title = $val['category_title'];
				$catid = $val['award_category_id'];
				if(isset($_REQUEST['nojury']) && $_REQUEST['nojury'] == 'nj')
					$nocase = NOCASE_JURY_TEXT;
				else
					$nocase = NOCASE_MSG_TEXT;
				break;
			}
		}
	}else{
		$cattitle = $retVal[0]['category_code'];
		$cat_title = $retVal[0]['category_title'];
		$catid = $retVal[0]['award_category_id'];
	}
	$case = $caseObj->getJuryCase($catid);
	$normaljury = $caseObj->getAllowedCategoryTOJuryOptimised($catid);
	$headjury = $caseObj->getAllowedCategoryTOJuryOptimised($catid,'H');
	$smarty->assign("Normaljury",$normaljury);
	$smarty->assign("Headjury",$headjury);
	$smarty->assign("Cattitle",$cattitle);
	$smarty->assign("Cat_title",$cat_title);
	$smarty->assign("Catid",$catid);
	$smarty->assign("CategoryArr",$retVal);
	$smarty->assign("CaseArr",$case);
	$smarty->assign("nocase",$nocase);
	$smarty->assign("pageTitle","Beoordelingen");
	$smarty->display("beoordelingen.tpl");
?>