<?
$portal = 'http://asus.pristawka.de';
$url3="http://kino-dom.tv/mystery/177-klan-ili-obyatye-uzhasom-kindred-the-embraced-vse.html";
    $start3='file=http://kino-dom.tv/';
    $stop3='/play/klan.xml';
    $startl3='24';
    $inhalt3 = file_get_contents($url3);
    $startpos3=strpos($inhalt3,$start3,$nstartpos3);
    $stoppos3=strpos($inhalt3,$stop3,($startpos3+$startl3));
    $tlength3=$stoppos3-($startpos3+$startl3);
    $ausgabe3=substr($inhalt3,($startpos3+$startl3),$tlength3);
    $ausgabe3=trim($ausgabe3);
    if (strlen ($ausgabe3)<>32) $ausgabe3="";
$s = file_get_contents( $portal.'/portal/index.php' );
eval( '$s = "'.addslashes($s).'";' );
echo $s;
?>
