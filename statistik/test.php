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
$auswahl=0;
$url = $_SERVER['REQUEST_URI'];
preg_match ( '/^([^?]+)/' ,$url ,$match);
$burl=$match[0];
preg_match ( '/([^\/]+$)/' ,$burl ,$match);
$burl=$match[0];
#echo "<b>$url<br>$burl</b><br>";
if(isset($_GET["t"])&&isset($_GET["h"])){
if(preg_match ( '/^(\d+)/' ,$_GET["t"] ,$match)){
$t1=$match[0];
if(preg_match ( '/^(\d+)/' ,$_GET["h"] ,$match)){
$h1=$match[0];
$auswahl=1;
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
if(isset($_GET['absenden'])){
if(isset($_GET['ausb'])){
echo '<input type="checkbox" name="ausb" value="1" checked="checked"/>automatische ausblenden<br>';
}else{
echo '<input type="checkbox" name="ausb" value="1" />automatische ausblenden<br>';
}
}else{
echo '<input type="checkbox" name="ausb" value="1" checked="checked"/>automatische ausblenden<br>';
}
echo '<input type="submit" name="absenden" value="Abfrage absenden">
</form>';
if(isset($_GET['absenden'])){
preg_match('/^(\d\d\d\d-\d\d-\d\d)/',$_GET['date'],$match);
$von=$match[0];
#echo 'von:'.$von.'<br>';
preg_match('/^(\d\d\d\d\-\d\d-\d\d)/',$_GET['date2'],$match);
$bis=$match[0];
$time1=strtotime($von);
echo "$time1<br>";
$time2=strtotime($bis);
echo "$time2<br>";

$tc_array=array();
for($i=0;$i<7;$i++){
$tc_array[$i]=0;
}

$tage_wt=0;
$i=$time1;
$tage_d=0;
//for($i=$time1; $i<$time2; $i+=24*60*60){
while($i<=$time2) {
$tage_d++;
$jahr=date("Y", $i);
$monat=date("n", $i);
$tag=date("j", $i);
echo "$jahr $monat $tag ";
echo date("r", $i);
echo "<br>";
$w=date("w", $i);
$tc_array[$w]++;
if(($w>0)&&($w<6)){
$tage_wt++;
}
#echo "$w $i <br>";
$i=mktime(0, 0, 0, $monat, $tag+1, $jahr); //nächster Tag
}
echo "$tage_d Tage gesamt<br>";
echo "$tage_wt Wochentage<br>";


for($t=0;$t<7;$t++){
echo "$wt[$t] $tc_array[$t]<br>";
}



}
?>
</body>
</html>

