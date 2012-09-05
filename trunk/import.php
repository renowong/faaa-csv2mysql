<?php
include_once('config.php');

$file = "extract.csv"; 
 $handle = fopen($file, 'r'); 
 while (!feof($handle)) 
 { 
 $data = fgets($handle, 1024);
 $data = removewhitespace($data);
 
 add($data);

  print "<p>";
 } 
 fclose($handle);
 
 function removewhitespace($d){
    $d = str_replace("  "," ",$d);
    $d = str_replace("  "," ",$d);
    $d = str_replace("  "," ",$d);
    $d = str_replace("  "," ",$d);
    $d = str_replace("  "," ",$d);
    $d = str_replace("  "," ",$d);
    $d = str_replace("  "," ",$d);
    $d = str_replace("  "," ",$d);
    return $d;
 }
 
 
 function add($d){
    $ardata = explode(";",$d);
	$arbp = explode(" ",$ardata[6]);
	$arprenom = explode(" ",$ardata[1]);

    $nom = $ardata[0];
	if(strpos($nom,"pse")>0){
		$arnom = explode(" ",$ardata[0]);
		$nom = $arnom[0];
		$nommarriage = $arnom[2];
	}


    $prenom = utf8_encode($arprenom[0]);
    $prenom2 = utf8_encode($arprenom[1]);
	$gender = $ardata[2];
    
    $dn = reversedate($ardata[3]);
	$lieun = $ardata[4];
    $obs = $ardata[5];
    $bp = $arbp[1];
    $cp = $ardata[7];
    $codeprenom = str_replace('-','',$prenom);
    $codenom = str_replace('-','',$nom);
    $clientcode = code($dn,$codenom,$codeprenom);
    
    if(!exist($clientcode)){
    
    $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
    $mysqli->set_charset("utf8");
    $query = "INSERT INTO `".DB."`.`clients` (`clientcode`, `clientstatus`,".
				 " `clientcivilite`, `clientnom`, `clientnommarital`, `clientprenom`, `clientprenom2`, `clientdatenaissance`, `clientlieunaissance`,".
                                 " `clientbp`, `clientcp`,`obs`)".
				 " VALUES ('".$clientcode."', '1', '$gender', '".$nom."', '".$nommarriage."', '".$prenom."', '".$prenom2."', '".$dn."', '".$lieun."',".
                                 "'".$bp."','".$cp."','".$obs."')";
	$mysqli->query($query);
        $mysqli->close();

	print $query;

	}
 }


	function exist($code){
		$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
		$stmt = $mysqli->prepare("SELECT COUNT(*) FROM `clients` WHERE `clientcode`=? LIMIT 1");
    		$stmt->bind_param("s", $code);
    		$stmt->execute();
    		$stmt->bind_result($count);
		$stmt->fetch();
    		$stmt->close();

print "-----searching for ".$code."----count=".$count."------";
		return ($count > 0 ? true : false);
	}

    function reversedate($d){
        $ar = explode("/",$d);
        return $ar[2]."-".$ar[1]."-".$ar[0];
    }
    
    function code($date,$nom,$prenom){

	$nom = strtr($nom,utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'),'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
        $nom = strtoupper($nom);	
        $nom = str_replace(' ','',$nom);
        $prenom = utf8_decode($prenom);
	$prenom = strtr($prenom,utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'),'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
	$prenom = strtoupper($prenom);
        $prenom = str_replace(' ','',$prenom);
	$nom .= "000";
	$nom = substr($nom, 0, 3);
	$prenom .= "0000000";
	$prenom = substr($prenom, 0, 7);
	$date = explode("-", $date);
        //if ($date[1]<10) $date[1] = "0".$date[1];
	$generatedcode = $date[0].$date[1].$date[2].$nom.$prenom;
        
        print $generatedcode;
        return $generatedcode;
    }
?>
