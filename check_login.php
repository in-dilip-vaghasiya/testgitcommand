<?php
require_once "config.php";

if(isset($_SESSION['userid'])) {
	echo ONE ;
}else{
	echo ZERO;
}

?>