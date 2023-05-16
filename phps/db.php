<?php

$host = "127.0.0.1";
        $db = "dbdarquest8";
        $charset = "utf8";

        $user = "equipe8";
        $psswd = "s73bsa46";

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        try{
            $pdo = new PDO($dsn,$user,$psswd);
            $GLOBALS["PDO"] = $pdo;
            //$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        } catch(Exception $ex){
            throw new Exception($ex->getMessage());
        }

function guillemets($string){
    return "'".$string."'";
}
function obtenirUserData($userid){
    $sql = "SELECT * FROM joueurs WHERE Id = :useridgiven";
    $userdata = executerSelectUneLigneObtenirTable($sql,[":useridgiven" => $userid]);
    return $userdata;
}
function obtenirUserDataByAlias($username){
    $sql = "SELECT * FROM joueurs WHERE alias = :useridgiven";
    $userdata = executerSelectUneLigneObtenirTable($sql,[":useridgiven" => $username]);
    return $userdata;
}
function executerSelectTable($sql){
    $fetcher = $GLOBALS["PDO"]->query($sql);
    return $fetcher;
}
function obtenirUserInventoryId($idItem){
    $sql = "SELECT Id FROM inventaire WHERE joueurs_id = :useridgiven AND items_id = :".$idItem."";
    $userdata = executerSelectUneLigneObtenirTable($sql,[":useridgiven" => $_SESSION["userid"]]);
    return $userdata;
}
function executerSelectUneLigneObtenirTable($sql, $prepareTable){
    //echo $sql . " ". json_encode($prepareTable);
    $stmt = $GLOBALS["PDO"]->prepare($sql);
    //echo count($prepareTable);
    $stmt->execute($prepareTable);

    $resultat = $stmt->fetch(PDO::FETCH_ASSOC);
    //echo json_encode($resultat);
    return $resultat;
}
function executerDML($sql, $prepareTable){
    $pdo = $GLOBALS["PDO"];
    $exec = $pdo->prepare($sql);
    $success = $exec->execute($prepareTable);
    return $success;
    //echo '<script>alert("'.$sql . " " .$perso . '")</script>';
}


/////////////////////////////////// code custom vvv

function isUserAdmin($uid){
    if(is_null($uid)){return false;}
    $d = obtenirUserData($uid);
    return $d["etatcompte"] == 2;
}

function getItemData($item){
    $d = executerSelectUneLigneObtenirTable("SELECT * from Items WHERE Id=:id",
    [":id" => $item]);
    return $d;
}

function getEvaluationData($evalId){
    $d = executerSelectUneLigneObtenirTable("SELECT * from Evaluations WHERE Id=:id",
    [":id" => $evalId]);
    return $d;
}
function getItemEvaluations($item){
    $d = executerSelectUneLigneObtenirTable("SELECT Id from Evaluations WHERE Id=:id",
    [":id" => $item]);
    return $d;
}




function executerSelectTableParType($type){
    $sql = "SELECT * FROM items WHERE type = '" . $type. "' ORDER BY prix";
    return executerSelectTable($sql);
    
}

function  executerSelectTableParTypes2($type1, $type2){
    $sql = "SELECT * FROM items WHERE type = '" . $type1 ."' OR type = '". $type2 . "' ORDER BY type, prix";
    return executerSelectTable($sql);
}

function  executerSelectTableParTypes3($type1, $type2, $type3){
    $sql = "SELECT * FROM items WHERE type = '" . $type1 ."' OR type = '" . $type2 ."' OR type = '" . $type3 ."' ORDER BY type, prix";
    return executerSelectTable($sql);
}



function executerSelectTableParPrixASC(){
    $sql = "SELECT * FROM items ORDER BY prix ASC";
    return executerSelectTable($sql);
}

function executerSelectTableParPrixDESC(){
    $sql = "SELECT * FROM items ORDER BY prix DESC";
    return executerSelectTable($sql);
}

function GetSolde($typeSolde){
    if(empty($_SESSION["userid"])){
        return 0;
    }
    $sql = "select * from  joueurs where id =" . $_SESSION["userid"];
      try{
        $table = executerSelectTable($sql);
        while($rows = $table->fetch()){
          return $rows["solde_".$typeSolde];
        }
      }catch(Exception $e){

      }
}


function getListeEmail()
{
    $sql = "SELECT email FROM joueurs";
    return executerSelectTable($sql);
}
function getListeAlias()
{
    $sql = "SELECT alias FROM joueurs";
    $fetcher= executerSelectTable($sql);
    $tt = [];
    while($row = $fetcher->fetch()){
        $tt[] = $row["alias"];
    }
    return $tt;
}

function getWinJoueur($idJoueur)
{
    $w = executerSelectUneLigneObtenirTable("SELECT COUNT(idEnigme) AS cWin FROM historiqueEnigme WHERE idJoueur = :idJoueur AND reussi = 1",
    [":idJoueur" => $idJoueur]);
    return $w['cWin'];
}

function getLossJoueur($idJoueur)
{
    $l = executerSelectUneLigneObtenirTable("SELECT COUNT(idEnigme) AS cLoss FROM historiqueEnigme WHERE idJoueur = :idJoueur AND reussi = 0",
    [":idJoueur" => $idJoueur]);
    return $l['cLoss'];
}


function getNbQuestionReponduJoueur($idJoueur)
{
    $nb = executerSelectUneLigneObtenirTable("SELECT COUNT(idEnigme) AS cNbQuestion FROM historiqueEnigme WHERE idJoueur = :idJoueur",
    [":idJoueur" => $idJoueur]);
    return $nb['cNbQuestion'];
}


?>