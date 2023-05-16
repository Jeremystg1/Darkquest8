<?php
require_once 'db.php';
require_once 'constructeur.php';

if(isset($_GET['SupComm'])){
    RemoveCommentaire();
}
function getAvgReview($id)
{
       $sql= "SELECT AVG(review) as avgreview FROM evaluations 
       INNER JOIN inventaire ON evaluations.inventaire_id = inventaire.Id 
       INNER JOIN items ON inventaire.items_id = :id;";
       $r =  executerSelectUneLigneObtenirTable($sql,[":id" => $id])["avgreview"];
       if(empty($r)){
            return "";//"- ⭐";
       } else{
        $r = floor($r);
        return "$r ⭐";
       }
}
function getInventaireIdFromUserWithIdItem($idItem){
    $sql = "SELECT Id FROM inventaire WHERE joueurs_id = :jid AND items_id=:iid";
    $jid = $_SESSION["userid"];
    $rows = executerSelectUneLigneObtenirTable($sql,[":jid" => $jid, ":iid" => $idItem]);
    return $rows["Id"];
}
function addReview($commentaire, $review, $idItem)
{   
    $inventaire_Id = getInventaireIdFromUserWithIdItem($idItem);
    $aDéjàRated = is_null($inventaire_Id);

    

    if($aDéjàRated){return false;}
       try {
        executerDML("INSERT INTO evaluations(commentaire,review,inventaire_Id) VALUES(:commentaire, :review, :idItem)", [
            ":commentaire" => null,
            ":review" => $review,
            ":idItem" => $inventaire_Id
        ]);
    } catch (Exception $e) {
    }

    if($commentaire != null)
    {
        setCommentaire($commentaire,$inventaire_Id);
    }


}

function setCommentaire($commentaire,$inventaire_Id)
{
    try {
    executerDML("UPDATE evaluations SET commentaire = :commentaire WHERE inventaire_Id = :idItem",[
        ":commentaire" => $commentaire,
        ":idItem" => $inventaire_Id
    ]);
    } catch (Exception $e) {
    }
  
}

function RemoveCommentaire(){
   
    try {
        //echo "DELETE FROM evaluations WHERE Id = ". $_GET['SupComm'];
        executerDML("DELETE FROM evaluations WHERE Id = :idComment",[
            ":idComment" => $_GET['SupComm']
        ]);
        } catch (Exception $e) {
        }

        headerQ('Location: ' . getAppRoot() .'/Details.php?id=' . $_GET["IdItem"].  '&alert=Le+commentaire+a+été+retiré!&alertc=green');
}
