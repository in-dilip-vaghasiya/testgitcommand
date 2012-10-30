<?php	
	require_once "config.php";
if((!(isset($_SESSION['userid']))) ||  ( ($_SESSION['usertype'] != 'U') && ($_SESSION['usertype'] != 'A'))) {
		header( 'Location:login.php?sessionout=1' );
}
if(isset($_SESSION['admin_view_userid'])){
	$userid=$_SESSION['admin_view_userid'];
	$loginname= $_SESSION['admin_view_username'];
}else{
	$userid=$_SESSION['userid'];
	$loginname="";
}

if(isset($_SESSION['categoryFieldValue'])) {
	foreach($_SESSION['categoryFieldValue'] as $key=> $value){
		$_SESSION['categoryFieldValue'][$key] = str_replace('"', "'", $_SESSION['categoryFieldValue'][$key]);
		$_SESSION['categoryFieldValue'][$key] = str_replace("\\\'", "'", $_SESSION['categoryFieldValue'][$key]);
		$_SESSION['categoryFieldValue'][$key] = str_replace("\\'", "'", $_SESSION['categoryFieldValue'][$key]);
		$_SESSION['categoryFieldValue'][$key] = str_replace("\'", "'", $_SESSION['categoryFieldValue'][$key]);
	}
}
	$txt ="";
	require_once CLASS_PATH."/awardCategory.php";
	$group_id =  $_GET["gno"];
	$categoryObj = new awardCategory();
	
	$sizeLimitVar =$categoryObj->getTotalFileSize($group_id);
	$sizeLimitVar = ALLOWED_TOTAL_UPLOAD_FILE_SIZE - $sizeLimitVar;
	if($sizeLimitVar > ALLOWED_ONE_FILE_SIZE){
		$sizeLimitVar = ALLOWED_ONE_FILE_SIZE;
	}	
	$smarty->assign("sizeLimitVar",$sizeLimitVar);


	$retVal = $categoryObj->checkCaseExists($group_id,0,$userid);
	if($retVal == ZERO){//Invalid group number and category for the current logged-in member
		echo "<script language='javascript'>window.location='".REDIRECT_PAGE_USER."'</script>";
	}else{
		if(isset($_GET['new'])){
			$smarty->assign("newCase",ONE);//print  case flag
		}
		$retVal = $categoryObj->getcategoryCode($group_id);
		if(isset($_SESSION['categoryFieldValue'])){
			//print_r($_SESSION['categoryFieldValue']);
			$fileName = "submitted_forms/".$retVal[0]['category_code']."/".$group_id."/".$group_id."_temp.txt";
		}else{
			$fileName = "submitted_forms/".$retVal[0]['category_code']."/".$group_id."/".$group_id.".txt";
		}


		//View Text file.
		if(file_exists($fileName)){
			$txt = file_get_contents($fileName);
			$content_array = file($fileName);
			$txt = implode("", $content_array);
			//$txt = nl2br($content);
		}
		$smarty->assign("caseTextFile",$txt);//print  case flag
		//View Text File

		$cat= $retVal[0]['award_category_id'];
		$retVal = $categoryObj->getFormNameTitle($cat);
		$attachmentArr = $categoryObj->getAttachments($group_id);
		$isCaseFinalArr = $categoryObj->isCasefinal($group_id);

		if($isCaseFinalArr[0]['is_final'] == '0' && $_SESSION['usertype'] == 'U')
			$isCaseFinalArr = $categoryObj->isCasefinalforuser($group_id);
		
		$smarty->assign("isFinalFlag", $isCaseFinalArr[0]['is_final']);
		$smarty->assign("View", "1");
		$smarty->assign("FilePath", $retVal[0]['category_code']."/".$group_id);
		if($isCaseFinalArr[0]['is_final'] != ONE){ // case not fine then 
			$fileName = $group_id."_temp.txt";
			if(!(file_exists(SUBMITTED_FORMS_PATH.$retVal[0]['category_code']."/".$group_id."/".$fileName ))){
				$fileName = $group_id.".txt";
			}			
		}else{ 
			$fileName = $group_id.".txt";
		}
		// For 
		foreach(unserialize(ALLOWED_FILE_TYPE) as $key=>$value){
			if($key != 0){																																				$allowed_file_type_array .= ";*.".$value;
			}else{
				$allowed_file_type_array = "*.".$value;
			}
		}
		$smarty->assign("allowed_file_type_array",$allowed_file_type_array);

		$smarty->assign("defaultFile", $fileName);
		$smarty->assign("group_id", $group_id);
		$smarty->assign("cat",$cat);
		$smarty->assign("category_code",$retVal[0]['category_code']);
		$smarty->assign("attachment", $attachmentArr);
		$smarty->assign("FormTitle",$retVal[0]['category_form_title']." ".$isCaseFinalArr[0]['modified']);
	}
	$smarty->assign("pageTitle","Overzicht van uw inzending");
	$smarty->display("case_step_3.tpl");
?>