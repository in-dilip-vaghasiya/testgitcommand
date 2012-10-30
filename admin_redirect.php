<?php
require_once "config.php";
require_once CLASS_PATH."/class.ammamember.php";
require_once CLASS_PATH."/awardCategory.php";
//require_once "getnominee.php";
if(!(isset($_SESSION['userid']))){
	header( 'Location:login.php?sessionout=1' );
}
if(isset($_SESSION['admin_view_userid'])){
	unset($_SESSION["admin_view_userid"]);
}
if(isset($_SESSION['admin_view_userid'])){
	unset($_SESSION["admin_view_userid"]);
}

if(isset($_SESSION['OLD_URL'])){
	$url = $_SESSION['OLD_URL'];
	unset($_SESSION['OLD_URL']);
	header( 'Location:'.$url );
}else{
	header( 'Location:admin_index.php' );
}
exit;
?>