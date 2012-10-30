<?php
	require_once "config.php";
	error_reporting(E_ALL); // Crank up error reporting temporarily 
	if(!(isset($_SESSION['userid']))){
		header( 'Location:login.php?sessionout=1' );
	}
	require_once "getnominee.php";
	if(isset($_POST["category_select"])){
		$catg_id = $_POST["category_select"];
		$smarty->assign("printCasesRated",ONE);

		require_once CLASS_PATH."/juryRateCase.php";
		$categoryNameTitle =  new juryRateCase();
		$categoryTitle = $categoryNameTitle->getCategoryTitle($catg_id);
		$smarty->assign("category_title",$categoryTitle);
		
	}elseif(isset($_REQUEST["catg_id"])){
		$catg_id = $_REQUEST["catg_id"];
	}
	require_once CLASS_PATH."/awardCategory.php";

	$categoryNameTitle = new awardCategory();
	$retVal = $categoryNameTitle->checkCategoryExists($catg_id);
	if($retVal == ZERO){//Invalid category number 
		echo "<script language='javascript'>window.location='".REDIRECT_PAGE_ADMIN."'</script>";
	}else{
		$retVal = $categoryNameTitle->getCategoryCasesListing($catg_id);
		$smarty->assign("case_listing",$retVal);
		$jury_rated[]= array();	
		if($retVal){
			foreach($retVal as $key=>$value){
				$retVal = $categoryNameTitle->getCaseRatedJury($value['group_no']);
				$jury_name = '';
				$amma_jury_id ='';
				
				if($retVal == ZERO){
					$jury_name = 'NO jury has rated.';
					$jury_rated[] = $jury_name;
				}else{
					foreach($retVal as $key=>$value){
						$jury_name = $jury_name . ", ". $value['jury_name'] ;
						$amma_jury_id = $amma_jury_id . "~". $value['amma_jury_id'] ;
					}
					$jury_name = substr($jury_name,1);
					$jury_rated[] = $jury_name;
				}
			}
		}
		$retVal = $juryObj->getValidCategoryJury($catg_id);
		$smarty->assign("allowed_jury",$retVal);
		$jury_names = '';
		$amma_jury_id ='';
		if($retVal == ZERO){
			$jury_name = 'NO jury associated.';
		}else{
			
			foreach($retVal as $key=>$value){
				$jury_names = $jury_names . ", ". $value['jury_name'] ;
				$amma_jury_id = $amma_jury_id . "~". $value['amma_jury_id'] ;
			}
			$jury_names = substr($jury_names,1);
			$amma_jury_id = substr($amma_jury_id,1);
		}
		$smarty->assign("jury_names",$jury_names);
		$smarty->assign("category_id",$catg_id);
		$smarty->assign("amma_jury_id",$amma_jury_id);
		
		$smarty->assign("pageTitle","Overzicht Cases voor deze categorie");
		$smarty->display("category_contents.tpl");
	}
?>