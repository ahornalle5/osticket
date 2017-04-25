<!DOCTYPE html>
<?php
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

if (isset($_COOKIE['cookie'])) {
        $liste = $_COOKIE['cookie'];
}else{
$meldung="kein cookie gesetzt - cookie setzen";
//setcookie ("cookie", "TEST");
}
if(isset($_POST['absenden'])){
if(isset($_POST['name_hinzu')){

}
if(preg_match('/^([0-9X]+)/',$_POST['liste'],$match)){
$neu=$match[0];
setcookie ("cookie", "$neu");
$value=$neu;
}
}

$auswahl=0;
$url = $_SERVER['REQUEST_URI'];
preg_match ( '/^([^?]+)/' ,$url ,$match);
$burl=$match[0];
#echo "<b>$url<br>$burl</b><br>";
preg_match ( '/([^\/]+$)/' ,$burl ,$match);
$burl=$match[0];
echo "<b>$url<br>$burl</b><br>";


echo '<html>
  <head>
    <meta charset="UTF-8">
    <title>Ticketsystem Statistik - User</title>
</head>
<body>
<pre>$meldung</pre>
';
echo "<pre>$value</pre><br>";
echo '<form action="'.$burl.'" method="post">

<input type="hidden" name="X" value="'.$x.'">
<label for="name_hinzu">hinzuf&uuml;gen (Name)</label> <input type="text" name="name_hinzu" id="name_hinzu" maxlength="30" value="'.$value.'">
<label for="name_entf">entfernen (Name)</label> <input type="text" name="name_entf" id="name_entf" maxlength="30" value="'.$value.'">
<input type="hidden" name="liste" id="liste">

<input type="submit" name="absenden" value="Abfrage absenden">
</form>
';

/*
if(isset($_GET['absenden'])){

preg_match('/^(\d\d\d\d\-\d\d-\d\d)/',$_GET['date2'],$match);
$bis=$match[0];


//SQL
/*

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
$sql.="ORDER BY tk.created";

#echo '<br><pre>'.$sql.'</pre><br>';
$result = mysql_query ( $sql );
$menge = mysql_num_rows ( $result );
echo '<td><b>SQL Ergebnisse:' . $menge.'</b><br>';


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

}//if(isset($_GET['absenden'])){
*/
?>
</body>
</html>
