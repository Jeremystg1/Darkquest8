<?php
include 'phps/db.php';
require_once 'phps/constructeur.php';
require_once 'phps/evaluationsManager.php';
desactiverForceLogin();
activerSessionSurPage();
global $requeteTable;
global $onOff;
$onOff = 'style="pointer-events:none';
$sql = "select * from items";
$requeteTable = executerSelectTable($sql);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=MedievalSharp" />
    <title><?php echo obtenirNomApplication(); ?></title>
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
            height: 15em;
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
            height: inherit;
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
    </style>
</head>

<body>
    <?php faireHeader(); ?>


    <?php



    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $selectionné = false;
        if (isset($_POST['choixRecherche']) && isset($_POST["types"])) {
            $onOff = ' ';
            $selectionné = true;
            if (isset($_POST['types'])) {
                $checkedBoxes = $_POST['types'];
                
                if (is_String($checkedBoxes)) {
                    $checkedBoxes = [$checkedBoxes];
                }
               
               
                ///echo $checkedBoxes[0];

                if (count($checkedBoxes) == 3) {
                    $requeteTable = executerSelectTableParTypes3($checkedBoxes[0], $checkedBoxes[1], $checkedBoxes[2]);
                } else if (count($checkedBoxes) == 2) {
                    $requeteTable = executerSelectTableParTypes2($checkedBoxes[0], $checkedBoxes[1]);
                } else if (count($checkedBoxes) == 1) {
                    $requeteTable = executerSelectTableParType($checkedBoxes[0]);
                }
            }
        }

        if (isset($_POST['choixRecherche']) && $_POST['choixRecherche'] == "tout" && $selectionné == false) {

            $sql = "select * from items";
            $requeteTable = executerSelectTable($sql);
        }

        if (isset($_POST['choixRecherche']) && $_POST['choixRecherche']  == "prixASC") {

            $requeteTable = executerSelectTableParPrixASC();
        }

        if (isset($_POST['choixRecherche']) && $_POST['choixRecherche'] == "prixDESC") {
            $requeteTable = executerSelectTableParPrixDESC();
        }

        
    }

    ?>
    <br> <br> <br> <br>
    <form method="POST" id="rechercheFormIndex">
        <select name="choixRecherche" id="choixRecherche">
            <option value="tout" selected>Tout</option>
            <option value="prixASC">Par prix ascendant</option>
            <option value="prixDESC">Par prix décroissant</option>
            <option hidden value="type">Par type</option>
        </select>
        <div>
            <label for="armes"> Armes</label>
            <input <?php $onOff ?> type="checkbox" id="armes" name="types[]" value="ar"><br>
            <label for="armures"> Armures</label>
            <input <?php $onOff ?> type="checkbox" id="armures" name="types[]" value="am"><br>
            <label for="potions"> Potions</label>
            <input <?php $onOff ?> type="checkbox" id="potions" name="types[]" value="po"><br>
            <label for="sorts"> Sorts</label>
            <input <?php $onOff ?> type="checkbox" id="sorts" name="types[]" value="so">
        </div>
        <div style="display: none;"> Recherche par étoiles:
            <select name="etoiles">
                <option value="5">5⭐</option>
                <option value="4">4⭐</option>
                <option value="3">3⭐</option>
                <option value="2">2⭐</option>
                <option value="1">1⭐</option>
            </select>
        </div> <br>
        <input type="submit" id="submit" name="submit" value="Rechercher">
    </form>
    <div id="scroll">
        <div class="itemsTable">
            <!--<tr><th></th><th>Nom</th><th>Quantité</th><th>Type</th><th>Prix</th></tr>-->
            <?php
            //$sql = "select * from items";
            //$table = executerSelectTable($sql);
            $table = $requeteTable;
           
            
            //echo $table;
            while ($rows = $table->fetch()) {
            ///foreach ($rows as $table) {
                echo '<div title="'. $rows['nom'].'" id="items">
                    <div id=align ><img id=itemImage src="' . $rows['imageUrl'] . '" /></div>
                    <div id="align">' . $rows['nom'] . '</div>
                    <div id="align">Quantité : ' . $rows['qtystock'] . '</div>
                    <div id="align">Type : ' . getTypeItemName($rows['type']) . '</div>
                    <div id="align">Prix : ' . $rows['prix'] . '</div>
                    <div id "align" style="text-align: center;">' . getAvgReview($rows['id'])  . '</div>
                    <div class="detailsButton"><a class="detailsText" href="./Details.php?id=' . $rows['id'], '">Détails</a></div>
                  </div>';
            }
            ?>
        </div>
    </div>
</body>

</html>