<?php
session_start();

if ($_SESSION["cs"])
{
	unset($_SESSION["cs"]);
	header("Location: ../login.php");
}

else
{
	header("Location: ../login.php");
}

?>