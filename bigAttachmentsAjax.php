<?php
include "config.php" ;
require_once CLASS_PATH."/awardCategory.php";
$attachmentObj = new awardCategory();
$attachmentArr = $attachmentObj->getAttachments($_REQUEST['id']);
$smarty->assign("attachment", $attachmentArr);

$fileSizeUtilized = $attachmentObj->getTotalFileSize($_REQUEST['id']);
$sizeLimitVar = ALLOWED_ONE_FILE_SIZE;
if(isset($_REQUEST['action']) && ($_REQUEST['action'])== 'fileSize'){
	if(is_numeric($fileSizeUtilized)  && $fileSizeUtilized > 0){
		$sizeLimitVar = ALLOWED_TOTAL_UPLOAD_FILE_SIZE - $fileSizeUtilized;
	}
	/*if($sizeLimitVar > ALLOWED_ONE_FILE_SIZE){
		$sizeLimitVar = ALLOWED_ONE_FILE_SIZE;
	}*/
	$smarty->assign("sizeLimitVar",$sizeLimitVar);
	exit;
}
echo $sizelimitvar;
$smarty->assign("group_id",$_REQUEST['id']);
$smarty->assign("category_code",$_REQUEST['category_code']);
$smarty->display("bigAttachmentsAjax.tpl");

?>