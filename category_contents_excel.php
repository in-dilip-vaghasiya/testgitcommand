<?php
	require_once "config.php";
	require_once CLASS_PATH."/PHPExcel.php";
	if(!(isset($_SESSION['userid']))){
		header( 'Location:login.php?sessionout=1' );
	}
	require_once "getnominee.php";
	if(isset($_REQUEST["catg_id"])){
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
	$retVal = $CategoryObj->checkCategoryExists($catg_id);

	if($retVal == ZERO){//Invalid category number 
		echo "<script language='javascript'>window.location='".REDIRECT_PAGE_ADMIN."'</script>";
	}else{
	
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0);
		
		$categoryTitle = $juryObj->getCategoryTitle($catg_id);
		
		$objPHPExcel->getActiveSheet()->setCellValue('A1', $categoryTitle);
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		
		$objPHPExcel->getActiveSheet()->setCellValue('A2', 'Gebruikersnaam');
		$objPHPExcel->getActiveSheet()->setCellValue('B2', 'Case Title');
		$objPHPExcel->getActiveSheet()->setCellValue('C2', 'Case Status');
		$objPHPExcel->getActiveSheet()->setCellValue('D2', 'Email Address');
		
		//$retVal = $CategoryObj->getCategoryCasesListingALL($catg_id);
		$retVal = $CategoryObj->casedetailsOptimised($catg_id);
		foreach($retVal as $key=>$value){
			if($value['is_final'] == 'N' && $value['finallink'] == '')
				$status = CREATED_STATUS;
			elseif($value['is_final'] == 'N' && $value['finallink'] != '')
				$status = PENDDING_STATUS;
			elseif($value['is_final'] == 'Y')
				$status = FINAL_STATUS;
			$retVal[$key]['is_final'] = $status;	
		}
		$retVal = $CategoryObj->subval_sort($retVal, $OrderBy,$Order);
		
		$smarty->assign("case_listing",$retVal);
		$i = 3;
		foreach($retVal as $key=>$value){
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $value['jury_name']);
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $value['field_value']);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $value['is_final']);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $value['email']);
			$i++;
		}
		$objPHPExcel->getActiveSheet()->setTitle($categoryTitle);
		$objPHPExcel->setActiveSheetIndex(0);
	}
	// Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="Overview_'.$categoryTitle.'_'.date("d-m-Y").'.xls"');
	header('Cache-Control: max-age=0');

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
	exit;
?>