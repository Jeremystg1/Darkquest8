<?php
$nomcookie = "enigmeFaites1";
require_once 'db.php';
require_once "constructeur.php";
//desactiverForceLogin();
activerSessionSurPage();
$userid = $_SESSION["userid"];

if(empty($_COOKIE[$nomcookie])){
    écrireAuCookie([]);
    $_URRLLL = $_SERVER["REQUEST_URI"];
    header("Location: $_URRLLL");
}
function écrireAuCookie($d){
    global $nomcookie;
    if(is_string($d)){
        $d = json_decode($d);
    }
    foreach($d as $key => $val){
        if($key == "" || $val <= 0){
            unset($d[$key]);
        }
    }
    if(!is_string($d)){
        $d = json_encode($d);
    }
    unset($_COOKIE[$nomcookie]);
    setcookie($nomcookie,null,-1);
    setcookie($nomcookie,$d,time()+606024*30,"/");
}
function lireCookie(){
    global $nomcookie;
    return json_decode($_COOKIE[$nomcookie],true);
}

function addEnigmeFaite($id){
    $d = lireCookie();
    $d[$id] = 1;
    écrireAuCookie($d);
}
function verifierEnigmeFaite($id){
    $d = lireCookie();
    foreach($d as $key => $val){
        if($key == $id){
            return true;
        }
    }return false;
}

function obtenirEnigmeParDifficulté($difficulté){
    //if(is_numeric($difficulté)){
        if($difficulté == "Facile") {$difficulté = 1;}
        if($difficulté == "Moyen") {$difficulté = 2;}
        if($difficulté == "Difficile") {$difficulté = 3;}
    //}
    $sql = "SELECT Id FROM enigmes WHERE difficulte = '$difficulté' AND disponible = 1";
    if($difficulté == "Aléatoire"){
        $sql = "SELECT Id FROM enigmes WHERE disponible = 1";
    }

    $fetcher = executerSelectTable($sql);
    $tabRandom = [];
    while($rows = $fetcher->fetch()){
        $déjàFaiteParLeJoueur = verifierEnigmeFaite($rows["Id"]);
        if(!$déjàFaiteParLeJoueur){
            array_push($tabRandom,$rows["Id"]);
            //$tabRandom[] = $rows["Id"];
        }
    }
    //echo json_encode($tabRandom) . "<br>";
    //echo count($tabRandom);
    if(count($tabRandom) > 0){
        shuffle($tabRandom);
        $tabResult = $tabRandom[0]; //array_rand($tabRandom);
        //echo "ID: ". $tabResult;
        //echo json_encode($tabResult);
        return obtenirEnigmeData($tabResult);
    }
    
    //echo "AUCUNE DISPO: ".$sql;
    return null;
}
function obtenirEnigmeData($id){
    $sql = "SELECT * FROM enigmes WHERE Id = :id";
    //echo "APAPAO".$sql;
    return executerSelectUneLigneObtenirTable($sql,[":id"=>$id]);
}


/*
Énigme difficile →10 pièces d’or
Énigme moyen →10 pièces d’argent
Énigme facile →10 pièces de bronze
*/
function donnerPointsSelonDifficulte($reponse,$id){
    //echo "3/".$reponse . " " . $id . "3/";
    if(empty($reponse)){return false;}
    if(empty($id)){return false;}
    //echo "3/".$reponse . " " . $id . "3/";
    $reussi = false;

    $dataEnigme = obtenirEnigmeData($id);

    $sArgent = 0;
    $sBronze = 0;
    $sOr     = 0;
    $diff = $dataEnigme["difficulte"];
    $reussiEnigme = 1;
    global $userid;

    addEnigmeFaite($id);
    if($dataEnigme["bonneReponse"] != $reponse){
        mauvaiseReponse($userid,$id);
        $reussiEnigme = 0;
        echo "<script> window.location.href = '" . getAppRoot()."/enigma.php?alert=Vous+avez+échoué+une+énigme'; </script>";
        
        $reussi2 = executerDML("INSERT INTO historiqueEnigme(IdEnigme,IdJoueur,reussi) VALUES(:qid,:jid,:reussi)",[
            ":qid" => $id,
            ":jid" => $userid,
            ":reussi" => $reussiEnigme
        ]);
        return false;}

    if($diff == 3){
        $sOr = 10;
    } else if ($diff == 2){
        $sArgent = 10;
    } else if ($diff == 1){
        $sBronze = 10;
    }

    
    $jid = $userid;
    $reussi2 = false;
    $devienMage = false;
    try {
        //echo "salut";
        $reussi = executerDML("UPDATE joueurs SET solde_or = solde_or + $sOr, solde_argent = solde_argent + $sArgent, solde_bronze = solde_bronze + $sBronze 
        WHERE id = :id", [
          ":id" => $jid
        ]);
        $reussi2 = executerDML("INSERT INTO historiqueEnigme(IdEnigme,IdJoueur,reussi) VALUES(:qid,:jid,:reussi)",[
            ":qid" => $id,
            ":jid" => $jid,
            ":reussi" => $reussiEnigme
        ]);

        $resultCount = executerSelectUneLigneObtenirTable("SELECT COUNT(reussi) as creussi FROM historiqueEnigme
        INNER JOIN enigmes ON enigmes.Id = historiqueEnigme.IdEnigme
        WHERE IdJoueur = :jid AND enigmes.difficulte = 3 AND reussi = 1", [":jid" => $jid]);
        //echo "<script> alert('" . json_encode($resultCount). "') </script>";
        if(isset($resultCount["creussi"])){
            if($resultCount["creussi"] >= 5 && obtenirUserData($jid)["role"] != 'm' && empty($_SESSION["devenuMage"])){
                executerDML("UPDATE joueurs SET joueurs.role = 'm' WHERE joueurs.id = :jid",[":jid" => $jid]);
                $devienMage = true;
                $_SESSION["devenuMage"] = true;
            }
        }
    } catch (Exception $e) {echo "aaaa";}
    bonneReponse($userid,$id);
    $url = "enigma.php?alert=Vous+avez+réussi+une+énigme!&alertc=chocolate&reussiEnigme=$reussi2";
    if($devienMage){
        $url = "enigma.php?alert=Vous+avez+réussi+une+énigme+ET+êtes+devenu+mage!&alertc=cornflowerblue";
    }
    
    if (!$reussi) {
        //$url =
    }

    echo "<script> window.location.href = '" . getAppRoot()."/$url'; </script>";
    return true;
}

function mauvaiseReponse($userid,$qid){
    $reussi = false;
    try {
        //$reussi = executerDML("UPDATE statistiquesEnigme");
    } catch (Exception $e) {}

}
function bonneReponse($userid,$qid){

}
?>