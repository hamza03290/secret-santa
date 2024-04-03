<?php
session_start();

if(isset($_SESSION['id']))
{
  unset($_SESSION['id']); // Corrected typo here
}

header("Location: index.php");
die;
?>
  