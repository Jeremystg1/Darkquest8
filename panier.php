<?php 
    include 'phps/db.php';
    require_once 'phps/constructeur.php';
    require_once 'phps/panierManager.php';
    activerSessionSurPage();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
         #itemImage{
        width: 100px;
        height: 100px;
        }
        #items:hover{
            outline: rgb(101, 55, 205) 4px solid;
            background-color:#444444;
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
        #items { border-top-left-radius: 20px; }
        #items { border-top-right-radius: 20px; }

        #items { border-bottom-left-radius: 50%; }
        #items { border-bottom-right-radius: 50%; }
        #align:nth-child(even) {
            background-color: #222222;
        }

        table{
            margin-left: auto;
            margin-right:auto;
            width: max-content;
        }
        .itemsTable {
            display: grid;
            grid-template-columns: repeat(auto-fill, 19em );
            width: initial;
            justify-content: center;
        }
        button{
        width: 75px;
        height: 40px;
        }
        #scroll {
            overflow: visible;
            margin-left: auto;
            margin-right: auto;
            width: 80%;
            top: 2em;
            position: relative;
        }
        #align{
            text-align: center;
        }
        .centrer{
            text-align: center;
            margin-left: auto;
            margin-right: auto;
            color: white;
        }
        .detailsText:hover{
            color: rgb(0,150,0);
        }
        .detailsButton{
            text-align: center;
        }
        .detailsText{
            color: rgb(0,200,0);
            font-size: 20px;
            font-weight: bold;
        }
        .styleBoutton{
            background-color: rgb(100, 100, 100);
            outline: black 4px solid;
            margin-top: 1em;
            color: white;
            font-weight: bold;
        }
        .styleBoutton:hover{
            outline: gray 4px solid;
            outline: rgb(101, 55, 205) 4px solid;
            color:black;
            background-color: white;
        }
        .editpanierform{
            text-align: center;
        }
        .editpanierform>input[type="number"]{
            width: 3em;
        }
        .editpanierform>input[type="submit"]{
            background-color: greenyellow;
            width: 7em;
            border: none;
            outline: 2px solid black;
            transition: .2s;
            color: black;
            font-weight: bolder;
        }
        .editpanierform>input[type="submit"]:hover{
            background-color: green;
            color: white;
            width: 8em;
        }
        .acheterToutForm>input[type="submit"]{
            background-color: greenyellow;
            width: 9em;
            height: 5em;
            border: none;
            outline: 2px solid black;
            transition: .2s;
            color: black;
            font-weight: bolder;
        }
        .acheterToutForm>input[type="submit"]:hover{
            background-color: green;
            color: white;
            width: 10em;
        }
        footer {
          position: fixed;
          left: 0;
          bottom: 0;
          width: 100%;
          color: white;
          text-align: center;
        }
    </style>
    <title><?php  echo obtenirNomApplication(); ?></title>
    <?php  echo obtenirApplicationHead(); ?>
</head>
<body>
    <?php faireHeader(); ?>
    <?php
        function tryGet($case){return $case;}
        if(isset($_GET["add"])){
            ajouterItem($_GET["add"],1,true);
        }
        if(isset($_GET["submit_option"])){
            if($_GET["submit_option"] == "Supprimer"){
                //echo tryGet($_GET["id"]);
                enleverItem(tryGet($_GET["id"]),tryGet($_GET["qtyedit"]),true);
            } elseif ($_GET["submit_option"] == "Acheter"){
                if(CheckSoldeValide()){
                    if(BaisserQtyStock($_GET["id"],$_GET["qtyedit"])){
                        if(BaisserLeSolde(null)){
                            addToInventory($_GET["id"],$_GET["qtyedit"]);
                            enleverItem(tryGet($_GET["id"]),tryGet($_GET["qtyedit"]),true);
                        }
                    }
                }
            }elseif($_GET["submit_option"] === "Acheter Tout"){ //jeremy
                ToutAcheter();
            } elseif($_GET["submit_option"] === "Modifier"){
                setItem(tryGet($_GET["id"]),tryGet($_GET["qtyedit"]),true);
            }
        }

        function NeedConversion2($totalPrix){
            if( GetSolde("or") < $totalPrix){
                //convertir les piece dargent pour voir si on peut acheter mtn
                conversion("argent");
                if(GetSolde("or") < $totalPrix){
                    //convertir les piece de bronze en or
                    conversion("bronze");
                    if(GetSolde("or") < $totalPrix){
                        //si tjs pas assez retourne null
                        headerQ('Location: panier.php?alert=Vous+n\'avez+pas+assez+d\'argent+pour+procéder+à+l\'achat!');
                    }
                }
            }
        }

        function conversion($typeArgent){
            $soldeAAjouter = 0;
            $nouveauSoldeArgent = GetSolde("argent");
            $nouveauSoldeBronze = GetSolde("bronze");
            if($typeArgent === "argent"){
                if($nouveauSoldeArgent != 0){
                    $soldeAAjouter = GetSolde("argent") / 10;
                    $nouveauSoldeArgent = GetSolde("argent") %10;
                }
            }elseif($typeArgent === "bronze"){
                if($nouveauSoldeBronze != 0){
                    $soldeAAjouter = GetSolde("bronze") / 100;
                    $nouveauSoldeBronze = GetSolde("bronze") %100;
                }
            }
            $reussi = false;
            try {
                $reussi = executerDML("UPDATE joueurs SET solde_or = solde_or + :ajout, solde_argent = $nouveauSoldeArgent , solde_bronze = $nouveauSoldeBronze
                WHERE id = :id", [
                ":ajout" => $soldeAAjouter,
                ":id" => $_SESSION["userid"]
                ]);
            } catch (Exception $e) {}
            return $reussi;
        }

        function addToInventory($id,$qty){
            $quantité = GetDataItemInventory($id);
            $ADeja = false;
            if($quantité > 0){
                $ADeja = true;
            }
            if(!$ADeja){
                $reussi = false;
                try {
                $reussi = executerDML("insert into inventaire(qty,joueurs_id,items_id,dateAchat) values (".$qty.",".$_SESSION["userid"].",".$id.",".date("Y-m-d").")", []);
                } catch (Exception $e) {}
                return $reussi;
            }else{
                try {
                    $reussi = executerDML("update inventaire set qty = " . ($quantité + $qty) . " where joueurs_id = :idJoueur and items_id = :items_id", [
                    ":items_id" => $id,
                    ":idJoueur" => $_SESSION["userid"]
                    ]);
                } catch (Exception $e) {}
            }
        }

        function GetDataItemInventory($item_id){
        $sql = "select * from  inventaire where items_id =" . $item_id . " and joueurs_id =" . $_SESSION["userid"];
        $table = executerSelectTable($sql);
        while ($rows = $table->fetch()) {
            return $rows["qty"];
        }
        return null;
        }

        function BaisserLeSolde($totalPrix){
            $solde = GetSolde("or");
            if($totalPrix === null){
                $rows = executerSelectUneLigneObtenirTable("select * from items where id=:id",[":id" => $_GET["id"]]);
                $coutOr = $rows["prix"];
                try {
                    $reussi = executerDML("update joueurs set solde_or = " . ($solde - ($coutOr * $_GET["qtyedit"])) . " where id = ".$_SESSION["userid"], []);
                } catch (Exception $e) {}
            }else{
                $coutOr = $totalPrix; 
                try {
                    $reussi = executerDML("update joueurs set solde_or = " . ($solde - $coutOr) . " where id = ".$_SESSION["userid"], []);
                } catch (Exception $e) {}
            }
            return $reussi;
        }

        function CheckSoldeValide(){
            if(isset($_SESSION["userid"])){
                $solde = GetSolde("or");
                $rows = executerSelectUneLigneObtenirTable("select * from items where id=:id",[":id" => $_GET["id"]]);
                $coutOr = $rows["prix"];
                NeedConversion2($coutOr);
                if($coutOr > $solde){
                return false;
                } else{
                return true;
                }
            }else{
                //header('Location: connexion.php?alert=Vous+devez+vous+connecter+pour+procéder+à+l\'achat!');
                echo "<script> window.location.href = '" . getAppRoot()."/connexion.php?alert=Connectez+vous+pour+procéder+aux+achats!' </script>";
            }
        }
        function BaisserQtyStock($id,$qty){
            $reussi = false;
            $rows = executerSelectUneLigneObtenirTable("select * from items where id=:id",[":id" => $id]);
            if(isset($rows["type"])){
                if($rows["type"] == "so"){
                    if(obtenirUserData($_SESSION["userid"])["role"] != "m"){
                        echo "<script> window.location.href = '" . getAppRoot()."/panier.php?alert=Vous+devez+être+mage+pour+effectuer+cet+achat!' </script>";
                        exit();
                    }
                }
            }
            $qtyRestante = $rows["qtystock"];
            if(($qtyRestante - $qty) < 0 || $qtyRestante == 0){
                header('Location: panier.php?alert=Cet+Item+est+en+rupture+de+stock!');
            }else{
                try {
                    $reussi = executerDML("update items set qtystock = " . ($qtyRestante - $qty) . " where id =  :items_id", [
                    ":items_id" => $id
                    ]);
                } catch (Exception $e) {
                }
            }
            return $reussi;
        }
        function afficherComment($str){
            if(is_array($str)){$str = json_encode($str);}
            echo "<script>/*$str*/</script>";
        }
        function ToutAcheter(){
            //CheckSoldeValide();
            if(isset($_SESSION["userid"])){
                $totalPrix = calculerTotalPanier();
                $solde = GetSolde("or");
                NeedConversion2($totalPrix);
                if($totalPrix > $solde){
                    headerQ('Location: index.php?alert=Vous+navez+pas+assez+de+ressources+pour+tout+acheter!');
                }else{
                    $table = lireCookie();
                    foreach($table as $id => $qty){
                        BaisserQtyStock($id,$qty);
                        addToInventory($id,$qty);
                    }
                    //delete le cookie
                    enleverToutItem($id,$qty,true);
                    BaisserLeSolde($totalPrix);
                }
            }else{
                headerQ('Location: connexion.php?alert=Vous+devez+vous+connecter+pour+procéder+à+l\'achat!');
            }
        }
        function calculerTotalPanier(){
            $table = lireCookie();
            $coutOr = 0;
            foreach($table as $id => $qty){
                $rows = executerSelectUneLigneObtenirTable("select * from items where id=:id",[":id" => $id]);
                $coutOr += $rows["prix"] * $qty;
            }
            return $coutOr;
        }
    ?>
    <div class="itemsTable">
        <?php 
            $table = lireCookie();
            if($table != null){
            foreach($table as $id => $qty){
                $rows = executerSelectUneLigneObtenirTable("select * from items where id=:id",[":id" => $id]);
                echo '<div id="items" ide=" '.$id.'" qtye=' .$qty.'">
                        <div id=align ><img src="'.$rows['imageUrl'].'" id="itemImage" /></div>
                        <div id="align">' . $rows['nom'] . '</div>
                        <div id="align">Quantité en stock: ' . $rows['qtystock'] . '</div>
                        <div id="align">Type : ' . getTypeItemName($rows['type']) . '</div>
                        <div id="align">Prix : ' . $rows['prix'] . '</div>
                        <form method="GET" class="editpanierform"> <input type="hidden" name="id" value="'.$id.'">
                                Quantité:<input type="number" min="0" max="'.$rows['qtystock']. '" name="qtyedit" value="'.$qty.'">
                                <input type="submit" name="submit_option" value="Modifier"></input>
                                <br><br>
                                <input type="submit" name="submit_option" value="Acheter"><br>
                                <input type="submit" name="submit_option" value="Supprimer">
                        </form>
                        </div>';
                }
            }
        ?>
    </div>
</body>
<footer>
    <?php
    if(calculerTotalPanier() != 0){
echo '<form method="GET" class="acheterToutForm">
                <input type="submit" name="submit_option" value="Acheter Tout">
              </form>';
    } else{
        echo "<span style='font-size: 1.5em; padding: 0 2em; background-color: rgb(121, 65, 225); color:white; border: 4px solid rgba(0, 0, 0, 0.553);'>Votre panier est vide!</span>";
    }
    ?>
</footer>
</html>