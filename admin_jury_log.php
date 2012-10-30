<?php
require_once "config.php";
if((!(isset($_SESSION['userid']))) ) {
		header( 'Location:login.php?sessionout=1' );
}elseif((($_SESSION['usertype'] != 'A') && ($_SESSION['usertype'] != 'P'))){
	header( 'Location:redirect.php' );
}
require_once "getnominee.php";
if(isset($_REQUEST["selected_jury"])){
	$jury_id = $_REQUEST["selected_jury"];
}
/* For  Sorting    */
if(isset($_GET['column']) && ($_GET['column'] != '')){
	$smarty->assign("column", $_GET['column']);
	if((isset($_GET['flag_order']))&& ($_GET['flag_order'] ==$_GET['column'] )){
		$_REQUEST['PageNumber'] = 1;
	}
	$column =$_GET['column']; 

	if(!(isset($_GET['flag_order'])) || ($_GET['flag_order'] == "")) {
		$flag_order = $_GET['column'];
	}
}else{
	$column =" pid ";
	$flag_order ="";
}
$PageNumber = $_REQUEST['PageNumber'];

if(isset($_GET['flag_order'])){
	$order =  'ASC';
	if(($_GET['flag_order'] != "")) {
		if($_GET['flag_order'] =  $_GET['column']){
			$order =  'DESC';
			$flag_order= "";
		}
	}
	
	$retVal = $juryObj->getLogs($jury_id,$PageNumber-1,$column,$order);
}else{
	$retVal = $juryObj->getLogs($jury_id,$PageNumber-1,$column);
}
$smarty->assign("flag_order", $flag_order);

/* End Sorting    */

	$smarty->assign("recordsPerPage", LOG_RECORDS_PER_PAGE);
	$smarty->assign("PageNumber", $PageNumber);
	$smarty->assign("selected_jury", $_REQUEST["selected_jury"]);

	$fromRecord = ((($PageNumber)*LOG_RECORDS_PER_PAGE)+1)-LOG_RECORDS_PER_PAGE;
	$smarty->assign("FromRecord",$fromRecord);
	$smarty->assign("ToRecord",$PageNumber*LOG_RECORDS_PER_PAGE);

	$totalLogs = $juryObj->getTotalLogs($jury_id);

	$smarty->assign("TotalLogs",$totalLogs);

	$LastPage= (ceil($totalLogs/LOG_RECORDS_PER_PAGE));
	$smarty->assign("LastPage",$LastPage);
	if($totalLogs == "0"){
		$smarty->assign("act",NO_LOGS_FOR_THIS_USER);
	}
	$smarty->assign("prevPage", $PageNumber-1);

	if($totalLogs > ((LOG_RECORDS_PER_PAGE*$PageNumber))){
		$smarty->assign("nextPage", $PageNumber+1);
	}else{
		$smarty->assign("ToRecord", ($fromRecord+count($retVal)-1));
		$smarty->assign("nextPage", "0");
	}

	$smarty->assign("TotalLogs",$totalLogs);

	$smarty->assign("retVal",$retVal);
	$getMemArr= $juryObj->getmemberType($jury_id);

	if((is_array($getMemArr) )&&( $getMemArr[0]['member_type'] == "U")){
		$smarty->assign("pageTitle","Gebruiker Logboek");
	}else{
		$smarty->assign("pageTitle","Jury Logboek");
	}
	
	$smarty->display("admin_jury_log.tpl");
?>