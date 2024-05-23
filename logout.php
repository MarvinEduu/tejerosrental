<?php

@include 'components/connection.php';

session_start();
session_unset();
session_destroy();

header('location:loading-page-in.php');
exit();

?>