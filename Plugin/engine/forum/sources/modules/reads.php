<?php
/*=====================================================
 DLE Forum 2.7.1
-----------------------------------------------------
 Author: Dj_AlexN
-----------------------------------------------------
 http://novak-studio.pl/
-----------------------------------------------------
=====================================================
 Copyright (c) 2015,2021 NOVAK Studio
=====================================================
*/

$param = (isset($_GET['param']))?htmlspecialchars($_GET['param']):'';

switch($param){
	
case "mark_all_read": 

break;

case "read_topics":

break;

default;
echo '<meta http-equiv="Refresh" content="1;URL=index.php">';
break;
}
?>