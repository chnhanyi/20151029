<?php
function date_to_lxs($date){
	$d = preg_split("/\-/",$date);
	return $d[2]."/".$d[1]."/".$d[0];
}
function date_to_utc($date){
	$d = preg_split("/\//",$date);
	return $d[2]."-".$d[1]."-".$d[0];
}
?>