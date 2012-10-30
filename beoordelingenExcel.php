<?php
	require_once "config.php";
	require_once CLASS_PATH."/PHPExcel.php";
	require_once CLASS_PATH."/juryRateCase.php";
	require_once CLASS_PATH."/awardCategory.php";
	// Create new PHPExcel object
	$objPHPExcel = new PHPExcel();
	$caseObj = new juryRateCase();
	$catObj = new awardCategory();
	
	$catInfo = $catObj->getCategory($_REQUEST['catid']);
	
	$normaljury = $caseObj->getAllowedCategoryTOJuryOptimised($_REQUEST['catid']);
	$headjury = $caseObj->getAllowedCategoryTOJuryOptimised($_REQUEST['catid'],'H');
	/*echo '<pre>';
	print_r($normaljury);
	exit;
	*/
	$res = $caseObj->getJuryCase($_REQUEST['catid']);
	$i = 0;
	$note = '';
	$flag = false;
	$njflag = false;
	$hjflag = false;
	if($res){
		foreach($res as $case){
			$criteria = $caseObj->getCriteriaDetails($_REQUEST['catid']);
			$j = 6;
			$flag = true;
			if($i != 0)$objPHPExcel->createSheet();
			$objPHPExcel->setActiveSheetIndex($i);
			$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Category Name : '.$catInfo[0]['category_form_title']);
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
			foreach($criteria as $key => $cri){
				if($cri['parent_id'] == 0){
					$j++;
					$objPHPExcel->getActiveSheet()->setCellValue('A'.$j, $cri['criteria_title']);
					$objPHPExcel->getActiveSheet()->getStyle('A'.$j)->getFont()->setBold(true);
					//$objPHPExcel->getActiveSheet()->mergeCells('A'.$j.':'.'B'.$j);
				}else{
					$objPHPExcel->getActiveSheet()->setCellValue('A'.$j, $cri['criteria_title']);
				}
				$j++;
			}
			$objPHPExcel->getActiveSheet()->setCellValue('A'.++$j, 'Note');
			$objPHPExcel->getActiveSheet()->getStyle('A'.$j)->getFont()->setBold(true);
				
			$col = 'B';
			$n = 0;
			if(is_array($normaljury)){
				foreach($normaljury as $jury){
					$j = 5;
					$userid = $jury['jury_id'];
					
					$objPHPExcel->getActiveSheet()->setCellValue($col.$j,$jury['login_name']);
					$objPHPExcel->getActiveSheet()->getStyle($col.$j)->getFont()->setBold(true);
					$j++;
					$enterdRatings = $caseObj->getEnteredRatings($case['group_no'],$userid);	
					$note = $caseObj->getNote($case['group_no'],$userid);
					$note = trim(strip_tags(stripslashes($note[0]['note'])));
					foreach($criteria as $key => $cri){
						if($cri['parent_id'] != 0){
							$objPHPExcel->getActiveSheet()->setCellValue($col.$j, $enterdRatings[$key]['jury_ratings']);
						}else{
							$j++;
						}
						$j++;
					}
					$objPHPExcel->getActiveSheet()->getColumnDimension($col.++$j)->setWidth(20);
					$objPHPExcel->getActiveSheet()->setCellValue($col.$j,$note);
					$objPHPExcel->getActiveSheet()->getStyle($col.$j)->getAlignment()->setWrapText(true);
					if($n < count($normaljury)-1)
						$col++; $n++;
				}
				$objPHPExcel->getActiveSheet()->setCellValue('B3','Normal Jury');
			}else{
				$njflag = true;
				$objPHPExcel->getActiveSheet()->setCellValue('B3','No Normal Jury');
			}
			$objPHPExcel->getActiveSheet()->setCellValue('B3','Normal Jury');
			$objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->mergeCells('B3'.':'.$col.'3');
			
			if(is_array($headjury ))
				$objPHPExcel->getActiveSheet()->setCellValue(++$col.'3','Head Jury');
			else
				$objPHPExcel->getActiveSheet()->setCellValue(++$col.'3','No Head Jury');
			$objPHPExcel->getActiveSheet()->getStyle($col.'3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle($col.'3')->getFont()->setBold(true);
			
			$oldcol = $col;
			$n = 0;	
			if(is_array($headjury )){
				foreach($headjury as $jury){
					$j = 5;
					$userid = $jury['jury_id'];
					
					$objPHPExcel->getActiveSheet()->setCellValue($col.$j,$jury['login_name']);
					$objPHPExcel->getActiveSheet()->getStyle($col.$j)->getFont()->setBold(true);
					$j++;
					$enterdRatings = $caseObj->getEnteredRatings($case['group_no'],$userid);	
					$note = $caseObj->getNote($case['group_no'],$userid);
					$note = trim(strip_tags(stripslashes($note[0]['note'])));
					foreach($criteria as $key => $cri){
						if($cri['parent_id'] != 0){
							$objPHPExcel->getActiveSheet()->setCellValue($col.$j, $enterdRatings[$key]['jury_ratings']);
						}else{
							$j++;
						}
						$j++;
					}
					$objPHPExcel->getActiveSheet()->getColumnDimension($col.++$j)->setWidth(20);
					$objPHPExcel->getActiveSheet()->setCellValue($col.$j,$note);
					$objPHPExcel->getActiveSheet()->getStyle($col.$j)->getAlignment()->setWrapText(true);
					if($n < count($headjury)-1)
						$col++; $n++;
				}
			}else{
				$hjflag = true;
			}
			$objPHPExcel->getActiveSheet()->mergeCells($oldcol.'2'.':'.$col.'2');
			$title = (strlen($case['field_value']) > 31) ? substr($case['field_value'], 0, 30) : $case['field_value']; 		
			$objPHPExcel->getActiveSheet()->setTitle($title);
			$objPHPExcel->setActiveSheetIndex($i);
			$i++;
		}
	}
	
	// Redirect output to a client’s web browser (Excel5) if any case rating by logged in user
	if($flag && (!$hjflag || !$njflag)){
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$catInfo[0]['category_form_title'].'_'.$_SESSION['username'].'_'.date("d-m-Y").'.xls"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}else if($hjflag && $njflag){
		header('Location:beoordelingen.php?nocaseCat_id='.$_REQUEST['catid'].'&nojury=nj');
		exit;	
	}else{
		header('Location:beoordelingen.php?nocaseCat_id='.$_REQUEST['catid']);
		exit;	
	}