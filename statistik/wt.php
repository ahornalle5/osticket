<?php
$dat1="2017-01-01";
$dat2="2017-02-28";
$t1=strtotime($dat1);
echo "$t1<br>";
$t2=strtotime($dat2);
echo "$t2<br>";
$delta=$t2-$t1;
$t=$delta/24/60/60;
echo "$delta <br> $t <br>";
$count=0;
for($i=$t1; $i<$t2; $i+=24*60*60){
$wt=date("w", $i);
if(($wt>0)&&($wt<6)){
$count++;
}
echo "$wt $i <br>";
}
echo "$count Wochentage<br>";
?>
