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
echo '<form action="index.php" method="post">
<!-- add class="tcal" to your input field -->
von <input type="text" name="date" class="tcal" value="2017-01-01" /><br>
bis <input type="text" name="date2" class="tcal" value="2017-02-28" /><br>';

$wt = array('So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa');
for($t=0;$t<7;$t++){
echo '<input type="checkbox" name="'.$t.'" value="1" checked="checked"/>'.$wt[$t].'<br>';
}
echo'<input type="submit" name="absenden" value="Liste absenden">
</form>';

//Mit isset() wird überprüft ob einer Variablen bereits
//ein Wert zugewiesen wurde

if(isset($_POST['absenden'])){
preg_match('/(\d\d\d\d-\d\d-\d\d)/',$_POST['date'],$match);
$von=$match[0];
echo 'anfang:'.$von.'<br>';
preg_match('/(\d\d\d\d\-\d\d-\d\d)/',$_POST['date2'],$match);
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

echo '<table border=1><tr><th>X</th>';
for ( $z = 5; $z < 18; $z++ ){
$z1=$z+1;
echo "<th>$z - $z1</th>";
}

echo "</tr>";
for ( $x = 1; $x < 7 ; $x++ ){
echo "<tr><td>Tag ".$wt[$x-1]."</td>";
for ( $z = 5; $z < 18; $z++ ){
$z1=$z+1;

$sql = "SELECT tk.created,
cd.subject,
us.name
FROM ost_ticket AS tk
LEFT JOIN ost_ticket__cdata AS cd ON tk.ticket_id=cd.ticket_id
LEFT JOIN ost_user AS us ON tk.user_id=us.id
WHERE TIME(tk.created) BETWEEN CAST('$z:00' AS TIME) AND CAST('$z1:00' AS TIME)
AND DATE(tk.created) BETWEEN CAST('$von' AS DATE) AND CAST('$bis' AS DATE)
AND DAYOFWEEK(tk.created) = $x
AND us.name NOT REGEXP '^PRTG'
AND us.name NOT REGEXP '^postmaster'
AND us.name NOT REGEXP '^nagios-vaw'
ORDER BY tk.created";

#echo '<br><pre>'.$sql.'</pre><br>';

$result = mysql_query ( $sql );

$menge = mysql_num_rows ( $result );

echo '<td><b>' . $menge.'</b><br>';


while ( $row = mysql_fetch_row ( $result ) )
{
  echo $row[0] . ' - ' . $row[1] . ' - ' . $row[2] . '<br><br>';
}
echo "</rd>";

}
echo "</tr>";
}

echo '</table>';
}?>


  </body>
</html>


