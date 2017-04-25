 <!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Ticketsystem Statistik</title>
        <!-- link calendar resources -->
        <link rel="stylesheet" type="text/css" href="tcal.css" />
<script type="text/javascript" src="language.js"></script> 
        <script type="text/javascript" src="tcal.js"></script>
</head>
<body>
<?php

#if(isset($_GET["ausbl"])){
#echo "DEBUG ausbl<br>";
#if(preg_match ( '/^(\d+)/' ,$_GET["ausbl"] ,$match)){
#$au=$match[0];
#$au.="\n";
#echo "DEBUG ausbl $au<br>";
#$fp = fopen("excl.txt", 'a');
#fwrite($fp, $au);
#fclose($fp);
#}
#}
#$exclu=file( "excl.txt");
#foreach($exclu as $ex){
#echo "$ex <br>";
#}
$auswahl=0;
$url = $_SERVER['REQUEST_URI'];
preg_match ( '/^([^?]+)/' ,$url ,$match);
$burl=$match[0];
#echo "<b>$url<br>$burl</b><br>";
if(isset($_GET["t"])&&isset($_GET["h"])){
if(preg_match ( '/^(\d+)/' ,$_GET["t"] ,$match)){
$t1=$match[0];
if(preg_match ( '/^(\d+)/' ,$_GET["h"] ,$match)){
$h1=$match[0];
$auswahl=1;

echo "DEBUG AUSWAHL $t1 $h1 <br>";
}
}
}
if(isset($_get["einblenden"])){
echo "DEBUG EINBLENDEN<br>";
}
echo '<form action="index.php" method="get">
<!-- add class="tcal" to your input field -->
von <input type="text" name="date" class="tcal" value="2017-01-01" /><br>
bis <input type="text" name="date2" class="tcal" value="2017-02-28" /><br>';
$wt = array('So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa');
#for($t=0;$t<7;$t++){
#echo '<input type="checkbox" name="'.$t.'" value="1" checked="checked"/>'.$wt[$t].'<br>';
#}
echo'<input type="submit" name="absenden" value="Abfrage absenden">
</form>';
//Mit isset() wird überprüft ob einer Variablen bereits
//ein Wert zugewiesen wurde
if(isset($_GET['absenden'])){
preg_match('/^(\d\d\d\d-\d\d-\d\d)/',$_GET['date'],$match);
$von=$match[0];
echo 'von:'.$von.'<br>';
preg_match('/^(\d\d\d\d\-\d\d-\d\d)/',$_GET['date2'],$match);
$bis=$match[0];
echo 'bis:'.$bis.'<br>';
/* Datenbankserver - In der Regel die IP */
$db_server = 'localhost';
/* Datenbankname */
$db_name = 'osticket';
/* Datenbankuser */
$db_user = 'stat';
/* Datenbankpasswort */
$db_passwort = 'hjuvRcUswSX9n8aT';
/* Erstellt Connect zu Datenbank her */
$db = @ mysql_connect ( $db_server, $db_user, $db_passwort );
$db_select = @ mysql_select_db( $db_name );
$sql = "SELECT  tk.ticket_id,
tk.created,
cd.subject,
us.name
FROM ost_ticket AS tk
LEFT JOIN ost_ticket__cdata AS cd ON tk.ticket_id=cd.ticket_id
LEFT JOIN ost_user AS us ON tk.user_id=us.id
WHERE DATE(tk.created) BETWEEN CAST('$von' AS DATE) AND CAST('$bis' AS DATE)
AND us.name NOT REGEXP '^PRTG'
AND us.name NOT REGEXP '^postmaster'
AND us.name NOT REGEXP '^nagios-vaw'
AND us.name NOT REGEXP '^NetVault'
AND us.name NOT REGEXP '^ky-scan'
AND cd.subject NOT REGEXP '^AHORNBKUP: Job finished'
AND cd.subject NOT REGEXP '^WSUS: Warnung zu neuen Updates von AHORNWSUS01'
AND cd.subject NOT REGEXP '^System Notification from ahorndata-backup'
ORDER BY tk.created";
#echo '<br><pre>'.$sql.'</pre><br>';
$result = mysql_query ( $sql );
$menge = mysql_num_rows ( $result );
echo '<td><b>SQL Ergebnisse:' . $menge.'</b><br>';
$warray=array();
$res2=0;
while ( $row = mysql_fetch_row ( $result ) ){
preg_match('/^(\d+)/',$row[0],$match);
$ticket_id=$match[0];
$created=$row[1];
$subject=$row[2];
$username=$row[3];
$gef=0;
#foreach($exclu as $ex){ 
#echo "$ex $ticket_id<br>";
#if($ex == $ticket_id){
#echo "exclude: $ticket_id $created \"$subject\" $username <br>";
#$gef=1;
#}
#}
#if($gef==0){
$res2++;
$w= date("w",strtotime($created));
$warray[$w][]=$row;
#echo "$wt[$w] $w : ";
#echo "include: $<br>";
#}
}#while
#echo '<td><b>nach Filter Ergebnisse:' . $menge.'</b><br>';
$co=array();
$summe_tag=array();


$time1=strtotime($von);
#echo "$time1<br>";
$time2=strtotime($bis);
#echo "$time2<br>";
$delta=$time2-$time1;
$tage_delta=$delta/24/60/60;
echo "$tage_delta Tage gesamt<br>";
$tc_array=array();
for($i=0;$i<7;$i++){
$tc_array[$i]=0;
}
$count=0;
for($i=$time1; $i<$time2; $i+=24*60*60){
$w=date("w", $i);
$tc_array[$w]++;
if(($w>0)&&($w<6)){
$count++;
}
#echo "$w $i <br>";
}
echo "$count Wochentage<br>";
for($t=0;$t<7;$t++){
echo "$wt[$t] $tc_array[$t]<br>";
}
for($x=0;$x<7;$x++){
for($t=0;$t<24;$t++){
$co[$x][$t]=0;
}
foreach ($warray[$x] as $row){
preg_match('/^(\d+)/',$row[0],$match);
$ticket_id=$match[0];
$created=$row[1];
$subject=$row[2];
$username=$row[3];
$w= date("w",strtotime($created));
$h= date("G",strtotime($created));
$co[$x][$h]++;
#echo "$x - $wt[$w] $w $t1: $h $h1: ". $co[$x][$h]."<br>" ;
if($auswahl==1){
if(($h==$h1)&&($w==$t1)){
echo "<b>Detail: $ticket_id : $created : $username : \"$subject\"</b><br>";
#echo "Detail: <a href='".$burl."?ausbl=".$ticket_id."'>$ticket_id</a> $created \"$subject\" $username <br>";
}
}
}
}
echo "Summen für die einzelnen Tage und Stunden:<br><table border=1><tr><th>X</th>";
for ($h=0;$h<24;$h++){
echo "<th>".$h.":00 - ".($h+1).":00</th>";
}
echo "</tr>";
for ($t=0;$t<7;$t++){
echo "<tr><td>$wt[$t]</td>";
for ($h=0;$h<24;$h++){
echo "<td><a href='".$burl."?absenden=get&date=".$von."&date2=".$bis."&t=".$t."&h=".$h."'>";
echo $co[$t][$h];
echo "</a></td>";
}
echo "</tr>";
}
echo "</table>";
}?>
</body>
</html>

