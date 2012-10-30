<?php
	require_once "config.php";
	if(!(isset($_SESSION['userid']))){
		header( 'Location:login.php?sessionout=1' );
	}
	require_once "getnominee.php";
	if(isset($_POST["category_select"])){
		$catg_id = $_POST["category_select"];
		$smarty->assign("printCasesRated",ONE);
	}elseif(isset($_REQUEST["catg_id"])){
		$catg_id = $_REQUEST["catg_id"];
	}
	if(isset($_REQUEST["order"])){
		$OrderArr = explode('~',$_REQUEST["order"]);
		$OrderBy = $OrderArr[0];
		$Order = $OrderArr[1];
	}else{
		$OrderBy = "jury_name";
		$Order = "sort";
	}
	$smarty->assign("order",$Order);
	
	$categoryTitle = $juryObj->getCategoryTitle($catg_id);
	$smarty->assign("category_title",$categoryTitle);

	$retVal = $CategoryObj->checkCategoryExists($catg_id);

	if($retVal == ZERO){//Invalid category number 
		echo "<script language='javascript'>window.location='".REDIRECT_PAGE_ADMIN."'</script>";
	}else{
		$retVal = $CategoryObj->casedetailsOptimised($catg_id);
		if(is_array($retVal)){
			foreach($retVal as $key=>$value){
				if($value['is_final'] == 'N' && $value['finallink'] == '')
					$status = CREATED_STATUS;
				elseif($value['is_final'] == 'N' && $value['finallink'] != '')
					$status = PENDDING_STATUS;
				elseif($value['is_final'] == 'Y')
					$status = FINAL_STATUS;
				$retVal[$key]['is_final'] = $status;	
			}	
		}
		$retVal = $CategoryObj->subval_sort($retVal, $OrderBy,$Order);
		
		$smarty->assign("case_listing",$retVal);
		$retVal = $juryObj->getValidCategoryJury($catg_id);
		$jury_id = "";
		if($retVal != ZERO){
			foreach($retVal as $key=>$value){
				$jury_id[] = $value['jury_id'];
			}
			$smarty->assign("jury_id",$jury_id);
		}
		$smarty->assign("category_id",$catg_id);
		$smarty->assign("pageTitle"," Overzicht Cases voor deze categorie");
			$smarty->assign("excelQueryString", $_SERVER['QUERY_STRING']);
		$smarty->display("category_contents.tpl");
	}


?>