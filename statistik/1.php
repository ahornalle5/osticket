<!DOCTYPE html>
<?php
setcookie ("cookie", "6X7X8");
if (isset($_COOKIE['cookie'])) {
        $liste = $_COOKIE['cookie'];
if(preg_match_all('/(\d+)/',$liste,$match)){
$liste=$match[0];
}
}
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


?>
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
<table border=1>
<?php

foreach($liste as $id){
$sql = "SELECT us.id, 
us.name
FROM ost_user AS us
WHERE us.id = $id
ORDER BY us.name
";

$result = mysql_query ( $sql );
$menge = mysql_num_rows ( $result );
$row = mysql_fetch_row ( $result );
#var_dump( $result);
echo "<tr><td>$row[0]</td><td>$row[1]</td></tr>";

}//foreach($liste as $id)
echo "</table>";
$t1_array=array(
0 => 0,
1 => 0,
2 => 0,
3 => 0,
4 => 0,
5 => 0,
6 => 6,
7 => 7,
8 => 7,
9 => 7,
10 => 7,
11 => 7,
12 => 7,
13 => 7,
14 => 7,
15 => 7,
16 => 16,
17 => 17,
18 => 18,
19 => 18,
20 => 18,
21 => 18,
22 => 18,
23 => 18,
);


$auswahl=0;
$url = $_SERVER['REQUEST_URI'];
preg_match ( '/^([^?]+)/' ,$url ,$match);
$burl=$match[0];
#echo "<b>$url<br>$burl</b><br>";
preg_match ( '/([^\/]+$)/' ,$burl ,$match);
$burl=$match[0];
#echo "<b>$url<br>$burl</b><br>";

if(isset($_GET["t"])&&isset($_GET["h"])){
if(preg_match ( '/^(\d+)/' ,$_GET["t"] ,$match)){
$t1=$match[0];
if(preg_match ( '/^(\d+)/' ,$_GET["h"] ,$match)){
$h1=$match[0];
if(preg_match ( '/^(\d+)/' ,$_GET["tab"] ,$match)){
$auswahl=1;
$tab=$match[0];
//echo "<pre>DEBUG: $tab</pre>";
}
}
}
}
if(isset($_GET['absenden'])){
preg_match('/^(\d\d\d\d-\d\d-\d\d)/',$_GET['date'],$match);
$von=$match[0];
preg_match('/^(\d\d\d\d\-\d\d-\d\d)/',$_GET['date2'],$match);
$bis=$match[0];
}else{
$von="2017-01-01";
$bis=date("Y-m-d");
}

echo '<form action="'.$burl.'" method="get">
<!-- add class="tcal" to your input field -->
von <input type="text" name="date" class="tcal" value="'.$von.'" /><br>
bis <input type="text" name="date2" class="tcal" value="'.$bis.'" /><br>';
$wt = array('So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa');


if(isset($_GET['ausw'])){
echo '<input type="checkbox" name="ausw" value="1" checked="checked"/>Benutzerauswahl anwenden<br>';
}else{
echo '<input type="checkbox" name="ausw" value="1" />Benutzerauswahl anwenden<br>';
}//if(isset($_GET['ausschl']))


if(isset($_GET['not'])){
echo '<input type="checkbox" name="not" value="1" checked="checked"/>Benutzerauswahl umkehren<br>';
}else{
echo '<input type="checkbox" name="not" value="1" />Benutzerauswahl umkehren<br>';
}//if(isset($_GET['not']))


if(isset($_GET['absenden'])){
if(isset($_GET['ausb'])){
echo '<input type="checkbox" name="ausb" value="1" checked="checked"/>automatische ausblenden<br>';
}else{
echo '<input type="checkbox" name="ausb" value="1" />automatische ausblenden<br>';
}//if(isset($_GET['ausb'])){
}else{
echo '<input type="checkbox" name="ausb" value="1" checked="checked"/>automatische ausblenden<br>';
}//if(isset($_GET['absenden'])){

echo '<input type="submit" name="absenden" value="Abfrage absenden">
</form>';


if(isset($_GET['absenden'])){
preg_match('/^(\d\d\d\d-\d\d-\d\d)/',$_GET['date'],$match);
$von=$match[0];
#echo 'von:'.$von.'<br>';
preg_match('/^(\d\d\d\d\-\d\d-\d\d)/',$_GET['date2'],$match);
$bis=$match[0];
#echo 'bis:'.$bis.'<br>';


$sql = "SELECT tk.ticket_id,
tk.created,
cd.subject,
us.name
FROM ost_ticket AS tk
LEFT JOIN ost_ticket__cdata AS cd ON tk.ticket_id=cd.ticket_id
LEFT JOIN ost_user AS us ON tk.user_id=us.id
WHERE DATE(tk.created) BETWEEN CAST('$von' AS DATE) AND CAST('$bis' AS DATE)
";
if(isset($_GET['ausb'])){
$sql.="AND us.name NOT REGEXP '^PRTG'
AND us.name NOT REGEXP '^postmaster'
AND us.name NOT REGEXP '^nagios-vaw'
AND us.name NOT REGEXP '^NetVault'
AND us.name NOT REGEXP '^ky-scan'
AND cd.subject NOT REGEXP '^AHORNBKUP: Job finished'
AND cd.subject NOT REGEXP '^WSUS: Warnung zu neuen Updates von AHORNWSUS01'
AND cd.subject NOT REGEXP '^System Notification from ahorndata-backup'
";
}//if(isset($_GET['ausb']))


if(isset($_GET['ausw'])){
if (isset($_GET['not'])){
$sql.="AND NOT us.id IN (";
}else{
$sql.="AND us.id IN (";
}//if (isset($_GET['not']))
$sql.=join(",",$liste);
$sql.=")\n";
}//if (isset($ausw))

$sql.="ORDER BY tk.created";

echo '<br><pre>'.$sql.'</pre><br>';


$result = mysql_query ( $sql );
$menge = mysql_num_rows ( $result );
echo '<td><b>SQL Ergebnisse:' . $menge.'</b><br>';
$warray=array();
$res2=0;

//sql ergebisse in array schreiben
while ( $row = mysql_fetch_row ( $result ) ){
preg_match('/^(\d+)/',$row[0],$match);
$ticket_id=$match[0];
$created=$row[1];
$subject=$row[2];
$username=$row[3];
$gef=0;
$res2++;
$w= date("w",strtotime($created));
$warray[$w][]=$row;
#echo "$ticket_id $created $subject $username <br>";
}//while ( $row = mysql_fetch_row ( $result ) )

$co=array();
$count2=array();
$summe_tag=array();

//anfangs und enddatum in zeitstempel umrechenen
$time1=strtotime($von);
#echo "$time1<br>";
$time2=strtotime($bis);
#echo "$time2<br>";

$tc_array=array();
for($i=0;$i<7;$i++){
$tc_array[$i]=0;
}

//wochentage und gesamttage ermitteln
$tage_wt=0;
$i=$time1;
$tage_d=0;

while($i<=$time2) {
$tage_d++;
$jahr=date("Y", $i);
$monat=date("n", $i);
$tag=date("j", $i);
#echo "$jahr $monat $tag ";
#echo date("r", $i);
#echo "<br>";
$w=date("w", $i);
$tc_array[$w]++;

if(($w>0)&&($w<6)){
$tage_wt++;
}

#echo "$w $i <br>";
$i=mktime(0, 0, 0, $monat, $tag+1, $jahr); //nächster Tag
}//while($i<=$time2)

echo "$tage_d Tage gesamt<br>";
echo "$tage_wt Wochentage<br>";


for($t=0;$t<7;$t++){
echo "$wt[$t] $tc_array[$t]<br>";
}


for($x=0;$x<7;$x++){

for($t=0;$t<24;$t++){
$co[$x][$t]=0;
$count2[$x][$t]=0;
}


//Verarbeitung sql ergebniszeilen
foreach ($warray[$x] as $row){
preg_match('/^(\d+)/',$row[0],$match);
$ticket_id=$match[0];
$created=$row[1];
$subject=$row[2];
$username=$row[3];
$w= date("w",strtotime($created));
$h= date("G",strtotime($created));
$co[$x][$t1_array[$h]]++;

$count2[$x][$h]++;

#echo "$x - $wt[$w] $w $t1: $h $h1: ". $co[$x][$h]."<br>" ;
if(($auswahl==1)&&($tab==1)){
if(($t1_array[$h]==$h1)&&($w==$t1)){
echo "<b>Detail: $ticket_id : $created : ".$username." : &quot;".$subject."&quot;</b><br>";
}
}
if(($auswahl==1)&&($tab==2)){
if(($h==$h1)&&($w==$t1)){
echo "<b>Detail: $ticket_id : $created : ".$username." : &quot;".$subject."&quot;</b><br>";
}
}
}//foreach
}//for x





echo "Summen für die einzelnen Tage und Stunden:<br><table border=1><tr><th>X</th>";
$anf=0;
for ($h=0;$h<24;$h++){
if($t1_array[$h] >$anf){
echo "<th>".$anf.":00 - ".($h).":00</th>";
$anf=$h;
}//if
}//for h
echo "<th>".$anf.":00 - 24:00</th>";

for ($t=0;$t<7;$t++){
echo "<tr><td>$wt[$t]</td>";
$anf=0;
for ($h=0;$h<24;$h++){
if(($t1_array[$h] >$anf)||($h>=23)){

echo "<td><a href='".$burl."?absenden=get&date=".$von."&date2=".$bis."&t=".$t."&h=".$anf."&tab=1";
if(isset($_GET['ausb'])){
echo "&ausb=1";
}//if(isset($_GET['ausb'])){
if(isset($_GET['ausw'])){
echo "&ausw=1";
}
if(isset($_GET['not'])){
echo "&not=1";
}

echo ".'>".$co[$t][$anf];
echo "</a></td>";
$anf=$t1_array[$h];
}//if($t1_array[$h] >$anf){

}//for h
echo "</tr>";
}//for t
echo "</table>";


echo "Summen für die einzelnen Tage und Stunden:<br><table border=1><tr><th>X</th>";
for ($h=0;$h<24;$h++){
echo "<th>".$h.":00 - ".($h+1).":00</th>";
}
echo "</tr><tr>";

for ($t=0;$t<7;$t++){
echo "<td>$wt[$t]</td>";

for ($h=0;$h<24;$h++){


echo "<td><a href='".$burl."?absenden=get&date=".$von."&date2=".$bis."&t=".$t."&h=".$anf."&tab=2";
if(isset($_GET['ausb'])){
echo "&ausb=1";
}//if(isset($_GET['ausb'])){
if(isset($_GET['ausw'])){
echo "&ausw=1";
}
if(isset($_GET['not'])){
echo "&not=1";
}

echo "'>";
/*
if(isset($_GET['ausb'])){
echo "<td><a href='".$burl."?absenden=get&date=".$von."&date2=".$bis."&ausb=1&t=".$t."&h=".$h."&tab=2'>";
}else{
echo "<td><a href='".$burl."?absenden=get&date=".$von."&date2=".$bis."&t=".$t."&h=".$h."&tab=2'>";
}//if*/

echo $count2[$t][$h];
echo "</a></td>";
}//for h
echo "</tr>";
}//for t
echo "</table>";


}//if(isset($_GET['absenden'])){



?>
</body>
</html>
