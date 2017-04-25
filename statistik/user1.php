<!DOCTYPE html>
<?php
$liste=array();
if (isset($_COOKIE['userl'])) {
        $liste = $_COOKIE['userl'];
if(preg_match_all('/(\d+)/',$liste,$match)){
$liste=$match[0];
}
}

if(isset($_POST['action'])){
#echo "<b>".$_POST['action']."</b><br>";
if($_POST['action']=='entf'){
#echo "ENTF".$_POST['ent'];
preg_match('/^(\d+)/',$_POST['ent'],$match);

#echo $match[0];
unset($liste[array_search($match[0], $liste)]);
// Und um den Index wiederherzustellen
$liste = array_values($liste);
}
if($_POST['action']=='hinz'){
#echo "HINZU".$_POST['hin'];
preg_match('/^(\d+)/',$_POST['hin'],$match);
#echo $match[0];
array_push($liste, $match[0]);
}
setcookie ("userl", implode('X',$liste));
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
</head>
<body>
<?php
#var_dump($liste);
echo "<table border=1>";
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


$url = $_SERVER['REQUEST_URI'];
preg_match ( '/^([^?]+)/' ,$url ,$match);
$burl=$match[0];
#echo "<b>$url<br>$burl</b><br>";
preg_match ( '/([^\/]+$)/' ,$burl ,$match);
$burl=$match[0];
#echo "<b>$url<br>$burl</b><br>";

echo '<form action="'.$burl.'" method="POST">
  <p>
    <label>
      User entfernen
      <input type="search" list="user" name="ent">
<datalist id="user">
';
foreach($liste as $id){

$sql ="SELECT us.id, 
us.name, 
ue.address
FROM ost_user AS us
LEFT JOIN ost_user_email AS ue ON us.id = ue.id
WHERE us.id = $id
ORDER BY us.name
";
#echo "<pre>$id\n$sql</pre>"; 
$result = mysql_query ( $sql );
$menge = mysql_num_rows ( $result );
#echo $menge;
$row = mysql_fetch_row ( $result );
#var_dump( $result);
echo "<option value=\"$row[0] $row[1] $row[2]\">\n";
}//foreach($liste as $id)
echo '</datalist> 
    </label>
    <button type="submit" name="action" value="entf">Entfernen</button>
  </p>
</form>';


$sql = "SELECT us.id,
us.name,
ue.address
FROM ost_user AS us
LEFT JOIN ost_user_email AS ue ON us.id = ue.id
ORDER BY us.name
";

$result = mysql_query ( $sql );
$menge = mysql_num_rows ( $result );
echo "SQL ERGEBNISSE: $menge<br>";

echo '<form action="'.$burl.'" method="POST">
  <p>
    <label>
      User hinzuf&uuml;gen
      <input type="search" list="userh" name="hin">
<datalist id="userh">
';

while ( $row = mysql_fetch_row ( $result ) ){
echo "<option value=\"$row[0] $row[1] $row[2]\">\n";
}//while

echo '</datalist>
    </label>
    <button type="submit" name="action" value="hinz">hinzu</button>
  </p>
</form>';

if(isset($_POST['action'])){
#echo "<b>".$_POST['action']."</b><br>";
if($_POST['action']=='entf'){
echo "ENTF ".$_POST['ent'];
}
if($_POST['action']=='hinz'){
echo "HINZU ".$_POST['hin'];
}
}
?>
</bod></html>


