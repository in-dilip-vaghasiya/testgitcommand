<?php

	require_once "config.php";
    require_once CLASS_PATH."/class.ammamember.php";
	$act="";

if (isset($_GET['activate']) && $_GET['activate'] != "")
	{
		$activecode = $_GET['activate'];

	    $AmmamemberObj = new ammaMember();
	    $retVal = ZERO;
	    $retVal = $AmmamemberObj->activateAccount($activecode);

		switch($retVal)
		{
		  case 0:
			$act = ACTIVATION_FAILURE;	
		  break ;
			  
		  case 1:
			$act = ACTIVATION_SUCCESS;
		  break;
		  
		  case -1:
			$act = ALREAADY_ACTIVATED;
		  break;	
		}

	}

	else // if proper activation link is not accessed
	{
	  $act = INVALID_ACTIVATION;
	}
		$smarty->assign("act",$act);
$smarty->assign("pageTitle","Activeren account");
$smarty->display("activation.tpl");
?>