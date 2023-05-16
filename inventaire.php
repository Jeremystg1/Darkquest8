<?php
require_once "phps/db.php";
require_once "phps/constructeur.php";
activerSessionSurPage();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <title>Inventaire</title>
  <link href="style.css" rel="stylesheet">
  <?php echo obtenirApplicationHead(); ?>
  <style>
    #itemImage {
      width: 100px;
      height: 100px;
    }

    #items:hover {
      outline: rgb(101, 55, 205) 4px solid;
      background-color: #444444;
    }

    #items {
      background-color: #333333;
      outline: black 4px solid;
      min-width: 15em;
      max-width: 15em;
      color: white;
      margin: auto;
      height: 17em;
      margin-top: 3em;
      box-shadow: rgba(0, 0, 0, 0.55) 0px 10px 50px, rgba(0, 0, 0, 0.52) 0px -12px 30px, rgba(0, 0, 0, 0.52) 0px 4px 6px, rgba(0, 0, 0, 0.57) 0px 12px 13px, rgba(0, 0, 0, 0.59) 0px -3px 5px;
    }

    #items {
      border-top-left-radius: 20px;
    }

    #items {
      border-top-right-radius: 20px;
    }

    #items {
      border-bottom-left-radius: 50%;
    }

    #items {
      border-bottom-right-radius: 50%;
    }

    #align:nth-child(even) {
      background-color: #222222;
    }

    table {
      margin-left: auto;
      margin-right: auto;
      width: max-content;
    }

    .itemsTable {
      
            display: grid;
            justify-content: center;
            grid-template-columns: repeat(auto-fill,19em);
            width: initial;
        }

    button {
      width: 75px;
      height: 40px;
    }
    /*
    #scroll {
      overflow: visible;
      margin-left: auto;
      margin-right: auto;
      width: 80%;
      top: 2em;
      position: relative;
    }*/
    @media (orientation: portrait) {
                #scroll {
                    height: 50%;
                    
                    overflow-y: scroll;
                }
    }   
        #scroll {
          
            margin-left: auto;
            margin-right: auto;
            width: 80%;
            top: 2em;
            position: relative;
            /*height: inherit;*/
        }

    #align {
      text-align: center;
    }

    .detailsText:hover {
      color: rgb(0, 150, 0);
    }

    .detailsButton {
      text-align: center;
    }

    .detailsText {
      color: rgb(0, 200, 0);
      font-size: 20px;
      font-weight: bold;
    }

    .styleBoutton {
      background-color: rgb(100, 100, 100);
      outline: black 4px solid;
      margin-top: 0.5em;
      color: white;
      font-weight: bold;
    }

    .styleBoutton:hover {
      outline: gray 4px solid;
      outline: rgb(101, 55, 205) 4px solid;
      color: black;
      background-color: white;
    }
    /*
    footer {
      position: fixed;
      left: 0;
      bottom: 0;
      width: 100%;
      color: white;
      text-align: center;
    }*/
  </style>
  </head>

<body>
  <?php
    faireHeader();
    

    if (isset($_POST["vendreTout"]) && isset($_POST["idItem"])) {
      GetDataItem($_POST["idItem"]);
      //MoneyDispatcher((($_COOKIE["prix"] * GetDataItemInventory($_POST["idItem"]))+ GetSolde()));
      VendreToutItem();
    }

    if (isset($_POST["vendre"]) && isset($_POST["idItem"])) {
      GetDataItem($_POST["idItem"]);
      //MoneyDispatcher((GetSolde() +$_COOKIE["prix"]));
      VendreItem();
    }

    //Vendre 1 seule fois l'item
    function VendreItem()
    {
      $quantité = GetDataItemInventory($_POST["idItem"]);
      $reussi = false;
      if ($quantité > 1) {
        try {
          $reussi = executerDML("update inventaire set qty = " . ($quantité - 1) . " where joueurs_id = :idJoueur and items_id = :items_id", [
            ":items_id" => $_POST["idItem"],
            ":idJoueur" => $_SESSION["userid"]
          ]);
        } catch (Exception $e) {
        }
        if (!$reussi) {
          echo "<script> window.location.href = 'inventaire.php?alert=Erreur&alertc=red' </script>";
          //header('Location: inventaire.php?alert=Erreur&alertc=red');
        }
        echo "<script> window.location.href = 'inventaire.php?alert=Item&Vendu&alertc=green' </script>";
        //header('Location: inventaire.php?alert=Item&Vendu&alertc=green');
      }

      //si il ne reste qu'une seule fois l'item, le retirer completement de la bd
      else {
        $reussi = VendreToutItem();
        if (!$reussi) {
          header('Location: inventaire.php?alert=Erreur&alertc=red');
        }
        header('Location: inventaire.php?alert=Item Vendu&alertc=green');
      }
    }

    //Vendre tout les items de ce type
    function VendreToutItem()
    {
      //Retirer litem de l'inventaire
      $reussi = false;
      try {
        $reussi = executerDML("DELETE FROM inventaire WHERE joueurs_id = :idJoueur and items_id = :items_id", [
          ":items_id" => $_POST["idItem"],
          ":idJoueur" => $_SESSION["userid"]
        ]);
      } catch (Exception $e) {
      }
      return $reussi;
    }

    /*
    function MoneyDispatcher($soldeFinal)
    {
      $reussi = false;
      try {
        $solde = GetSolde();
        $reussi = executerDML("update joueurs set solde_or = " . $soldeFinal . " where id = :idJoueur", [
          ":idJoueur" => $_SESSION["userid"]
        ]);
        echo $soldeFinal;
      } catch (Exception $e) {
      }
      return $reussi;
    }
    */

    function GetDataItem($item_id)
    {
      $sql = "select * from  items where id =" . $item_id;
      $table = executerSelectTable($sql);
      while ($rows = $table->fetch()) {
        //setcookie("prix", $rows["prix"]);
      }
    }

    function GetDataItemInventory($item_id)
    {
      $sql = "select * from  inventaire where items_id =" . $item_id . " and joueurs_id =" . $_SESSION["userid"];
      $table = executerSelectTable($sql);
      while ($rows = $table->fetch()) {
        return $rows["qty"];
      }
      return null;
    }
  ?>
  
  <div id="scroll">
    <div class="itemsTable">
      <?php 
      function getInventaireIdFromUserWithIdItem($idItem){
        $sql = "SELECT Id FROM inventaire WHERE joueurs_id = :jid AND items_id=:iid";
        $jid = $_SESSION["userid"];
        $rows = executerSelectUneLigneObtenirTable($sql,[":jid" => $jid, ":iid" => $idItem]);
        if(empty($rows["Id"])){
          return -1;
        }
        return $rows["Id"];
    }
    function déjaCommenté($idItem){
      $sql = "SELECT Id FROM evaluations WHERE inventaire_Id = :iid";
      $jid = $_SESSION["userid"];
      $rows = executerSelectUneLigneObtenirTable($sql,[":iid" => $idItem]);
      if(empty($rows["Id"])){return false;}
      return true;
    }
      function obtenirSiPeutCommenter($idItem){
        $idInv = getInventaireIdFromUserWithIdItem($idItem);
        if(!déjaCommenté($idInv)){return "style='display:'";}
        return "style='display: none;'";
      }

      $sql = "select * from inventaire i 
          inner join items it on it.id = i.items_id 
          where joueurs_id = " . $_SESSION["userid"];
      $table = executerSelectTable($sql);
      while ($rows = $table->fetch()) {
        echo '<div id="items">
                    <div id=align ><img src="' . $rows['imageUrl'] . '" id="itemImage" /></div>
                    <div id="align">' . $rows['nom'] . '</div>
                    <div id="align">Quantité : ' . $rows['qty'] . '</div>
                    <div id="align">Type : ' . getTypeItemName($rows['type']) . '</div>
                    <div id="align">Prix : ' . $rows['prix'] . '</div>'.
                    //'<td><a onClick=\"javascript: return confirm("Please confirm deletion");\" href="delete.php?id=".$query2["id"]."">x</a></td><tr>'.


                    //'<div class="detailsButton"><form method="GET"><input type="hidden" value="' . $rows["items_id"] . '" name="idItem" ><input type="hidden" SUBMIT value="Vendre" name="vendre" class="styleBoutton"></form></div>'.
                    //'<div class="detailsButton"><form method="GET"><input type="hidden" value="' . $rows["items_id"] . '" name="idItem" ><input type="hidden"SUBMIT value="Vendre tout" name="vendreTout" class="styleBoutton"></form></div>
                    '<div class="detailsButton" ' . obtenirSiPeutCommenter($rows["id"]). '>
                        <div> <a class="detailsText" href="./commentaireForm.php?id=' . $rows["id"]. '">Commentez</a>
                        </div>
                    </div>
                </div>';
      }
      ?>
    </div>
  </div>
  <!--<footer>
  <div> /*faireBouttonRetourHome();*/ ?></div>
</footer>-->
</body>
</html>