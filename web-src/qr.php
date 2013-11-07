<?php

    include('libs/phpqrcode.php'); 
     
    QRcode::png($_GET['encode']);
?>
