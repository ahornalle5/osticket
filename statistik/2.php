<?php
$liste="6X7X8";
if (isset($liste)){
if(preg_match_all('/(\d+)/',$liste,$match)){
foreach($match[0] as $ma){
echo $ma;
$sql.="AND us.id = ".$ma[0]."\n";
}
}
}

?>
