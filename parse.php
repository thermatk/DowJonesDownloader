<?php
date_default_timezone_set ("UTC");
include('simple_html_dom.php');
function postconnect($link,$cookie,$postdata,$opt_header,$opt_follow){
  $сonnection = curl_init();
  curl_setopt($сonnection, CURLOPT_URL,$link);
  curl_setopt($сonnection, CURLOPT_COOKIE,$cookie);
  curl_setopt($сonnection, CURLOPT_HEADER,$opt_header);
  curl_setopt($сonnection, CURLOPT_RETURNTRANSFER,1);
  curl_setopt($сonnection, CURLOPT_POST,1);
  curl_setopt($сonnection, CURLOPT_FOLLOWLOCATION,$opt_follow);  
  curl_setopt($сonnection, CURLOPT_POSTFIELDS, $postdata);
  curl_setopt($сonnection, CURLOPT_сonnectTIMEOUT,30);
  $all = curl_exec($сonnection);
  curl_close($сonnection);
return $all;
}
function getconnect($link,$cookie,$opt_header,$opt_follow){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$link);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,$opt_follow);
        curl_setopt($ch, CURLOPT_HEADER,$opt_header);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2; Trident/6.0)");
        curl_setopt($ch, CURLOPT_COOKIE,$cookie);
        $otvet = curl_exec($ch);
        curl_close($ch);
return $otvet;
}
function grab($inf,$begin,$end){
        if (substr_count($inf, $begin) and substr_count($inf, $end)){
                $begin=strpos($inf,$begin)+strlen($begin);
                $end=strpos($inf,$end,$begin);
                $grab=substr($inf,$begin, $end-$begin);
return $grab;
        } else {
return FALSE;
        }
}

echo "Starting...\n";

$jointstring = '<table border="0" cellpadding="2" cellspacing="1" width="100%"><tr><th scope="col" class="yfnc_tablehead1" align="right" width="16%">Date</th><th scope="col" class="yfnc_tablehead1" align="right" width="12%">Open</th><th scope="col" class="yfnc_tablehead1" align="right" width="12%">High</th><th scope="col" class="yfnc_tablehead1" align="right" width="12%">Low</th><th scope="col" class="yfnc_tablehead1" align="right" width="12%">Close</th><th scope="col" class="yfnc_tablehead1" align="right" width="16%">Avg Vol</th><th scope="col" class="yfnc_tablehead1" align="right" width="15%">Adj Close*                             </th></tr>';
for($counter=0;$counter<=1518;$counter=$counter + 66) {
	$djindex = getconnect("http://finance.yahoo.com/q/hp?s=^DJI&a=00&b=29&c=1985&d=09&e=13&f=2014&g=w&z=66&y=".$counter,null,null,null);
	$html = str_get_html($djindex);
	echo "\nDownloaded #".$counter."\n";
	$table = $html->find("table.yfnc_datamodoutline1")[0];
	$newpart = $table->innertext;
	$newpart = str_replace ('<tr><td class="yfnc_tabledata1" colspan="7" align="center">                             * <small>Close price adjusted for dividends and splits.</small></td></tr></table></td></tr>', "", $newpart );
	$newpart = str_replace ('<tr valign="top"><td><table border="0" cellpadding="2" cellspacing="1" width="100%"><tr><th scope="col" class="yfnc_tablehead1" align="right" width="16%">Date</th><th scope="col" class="yfnc_tablehead1" align="right" width="12%">Open</th><th scope="col" class="yfnc_tablehead1" align="right" width="12%">High</th><th scope="col" class="yfnc_tablehead1" align="right" width="12%">Low</th><th scope="col" class="yfnc_tablehead1" align="right" width="12%">Close</th><th scope="col" class="yfnc_tablehead1" align="right" width="16%">Avg Vol</th><th scope="col" class="yfnc_tablehead1" align="right" width="15%">Adj Close*                             </th></tr>', "", $newpart );
	
	while(substr_count($newpart,'<td class="yfnc_tabledata1" nowrap align="right">')) {
		$date=grab($newpart,'<td class="yfnc_tabledata1" nowrap align="right">','</td>');
		$unixdate=strtotime($date);
		$newformat = date('d.m.Y',$unixdate);
		$newpart = str_replace ('<td class="yfnc_tabledata1" nowrap align="right">'.$date.'</td>', '<td class="yfnc_tabledata1" align="right">'.$newformat.'</td>', $newpart );
	}

	echo "\nStrlen #".strlen($newpart)."\n";	
	$jointstring.=$newpart;
}
$jointstring .= "</table>";
file_put_contents("finaltable.html", $jointstring);
?>
