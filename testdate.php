<?php
$d1=new DateTime("2012-07-08 11:00");
$d2=new DateTime("2012-07-09 09:00");
$diff=$d2->diff($d1);
print_r( $diff ) ; 
