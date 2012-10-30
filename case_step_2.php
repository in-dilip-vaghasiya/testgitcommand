<?php
	require_once "config.php";
	require_once CLASS_PATH."/awardCategory.php";
	require_once CLASS_PATH."/class.ammamember.php";
	if((!(isset($_SESSION['userid']))) ||  ( ($_SESSION['usertype'] != 'U') && ($_SESSION['usertype'] != 'A'))) {
			header( 'Location:login.php?sessionout=1' );
	}
	if(isset($_POST["category_select"])){
		$cat = $_POST["category_select"];
	}else{
		if(isset($_REQUEST["category_select"])){
			$cat = $_GET["category_select"];
			if(isset($_SESSION['categoryFieldValue'])){
				$smarty->assign("categoryFieldValue", $_SESSION['categoryFieldValue']);
			}
		}else{
			header( 'Location:'.REDIRECT_PAGE_USER.'?sessionout=1' );
		}
	}
	foreach(unserialize(ALLOWED_FILE_TYPE) as $key=>$value){
		if($key != 0){																																				$allowed_file_type_array .= ";*.".$value;
		}else{
			$allowed_file_type_array = "*.".$value;
		}
	}
	$smarty->assign("allowed_file_type_array",$allowed_file_type_array);
	if(isset($_SESSION['admin_view_userid'])){
		$userid=$_SESSION['admin_view_userid'];
		$loginname= $_SESSION['admin_view_username'];
	}else{
		$userid=$_SESSION['userid'];
		$loginname="";
	}



	$categoryNameTitle = new awardCategory();
	$ammaObj = new ammaMember();

	/* added by prithi for display of the data in formtitle like hdc-prof,hdc-std,mideabereau*/
	$AmmamemberObj = new ammaMember();
	$categoryObj = new awardCategory();
	$retArr = $AmmamemberObj->RetrieveMSSqlMember($loginname);
	$retVal = $retArr[0];
	$smarty->assign("memberArray", $retVal);
	/* added by prithi for display of the data in formtitle like hdc-prof,hdc-std,mideabereau*/
	
	
	$retVal = $categoryNameTitle->getFormNameTitle($cat);
	$wordCountLimit = $retVal[0]['category_count_limits'];
	$retArr = $categoryNameTitle->getPageContents('add_remove_attachments-'.$retVal[0]['category_code']);
	$smarty->assign("add_remove_attachments", $retArr[0]);
	$smarty->assign("maxFormWords","$wordCountLimit");
	$smarty->assign("charecterFilled","$wordCountLimit");
	$smarty->assign("FormTitle",$retVal[0]['category_form_title']);
	$smarty->assign("awardID",$retVal[0]['award_detail_id']);
	$smarty->assign("cat",$cat);
	$smarty->assign("category_code",$retVal[0]['category_code']);

	if($cat == 1)
		$smarty->assign("pageTitle",'Deelnameformulier 2A');
	else if	($cat == 2 || $cat == 3)
		$smarty->assign("pageTitle",'Deel 2B');
			
	if(isset($_REQUEST['group_no'])){
		$attachmentArr = $categoryNameTitle->getAttachments($_REQUEST['group_no']);
		$smarty->assign("attachment", $attachmentArr);
	}
	if(isset($_REQUEST['attchmentName'])){
		$smarty->assign("attchmentName",$_REQUEST['attchmentName']);
		$smarty->assign("FilePath",$retVal[0]['category_code']."/".$_REQUEST['group_no']."/");
		$smarty->assign("group_id", $_REQUEST['group_no']);
		if($_REQUEST['attchmentName']==""){
			$smarty->assign("FormName", $retVal[0]['category_form_file_name'].".tpl");
		}
	}else{
		$group_id = $ammaObj->createNewCase($cat,$retVal[0]['award_detail_id'],$userid);
		$folderFlag = $ammaObj->createNewFolder($retVal[0]['category_code'],$group_id);
		$smarty->assign("group_id",$group_id);
		$smarty->assign("FormName", $retVal[0]['category_form_file_name'].".tpl");
	}
		$sizeLimitVar = ALLOWED_ONE_FILE_SIZE;
		if($_REQUEST['group_no'] !='')
		{
			$attachmentArr     = $categoryNameTitle->getAttachments($_REQUEST['group_no']);
			$fileSizeUtilized  =  $categoryNameTitle->getTotalFileSize($_REQUEST['group_no']);
			
			if(is_numeric($fileSizeUtilized)  && $fileSizeUtilized > 0){
				$sizeLimitVar = ALLOWED_TOTAL_UPLOAD_FILE_SIZE - $fileSizeUtilized;
			}
			if($sizeLimitVar > ALLOWED_ONE_FILE_SIZE){
				$sizeLimitVar = ALLOWED_ONE_FILE_SIZE;
			}
		}
	$smarty->assign("sizeLimitVar",$sizeLimitVar);
	$smarty->assign("action_flag","Add");
	$smarty->display("case_step_2.tpl");
?>