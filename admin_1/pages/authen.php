<?php 
    require_once '../../service/connect.php' ; 
    if( !isset($_SESSION['MB_ID'] ) ){
        header('Location: ../../../login.php');  
    }
?>
