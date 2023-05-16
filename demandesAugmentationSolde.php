<?php
include 'phps/db.php';
require_once 'phps/constructeur.php';
require_once 'phps/evaluationsManager.php';
desactiverForceLogin();
activerSessionSurPage();
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
        table {
            color: antiquewhite;
            font-weight: bold;
        }
        table, th, td {
            background-color:rgb(101, 35, 205);
            border: 1px solid black;
            border-collapse: collapse;
            margin: 20px;
        }
        th, td {
            color:white;
            padding: 5px 10px;
        }
        th {
            background-color: rgb(70, 24, 138);
        }
        tr{
            color:white;
        }
        table{
            margin-left: auto;
            margin-right:auto;
        }
    </style>
</head>
<body>
    <?php 
    faireHeader();
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        if (!isset($_POST['choix'])) {
            headerQ('Location: demandesAugmentationSolde.php?');
        }
        if ($_POST['choix'] == "accepter") {
            $idJoueur = $_POST['idJoueur'];
            $nbDemandeSolde = obtenirUserData($idJoueur)['nbDemandeSolde'];
            $idDemande = $_POST['idDemande'];
            $reussi = false;
            if ($nbDemandeSolde > 3) {
                header('Location: demandesAugmentationSolde.php?alert=nbDeDemandeMaxAtteinte');
            } else {
                try {
                    echo $idDemande;
                    $reussi = executerDML("UPDATE demandes SET flag_accepte = 1 WHERE Id = :idDemande", [
                        ":idDemande" => $idDemande
                    ]);
                } catch (Exception $e) {
                    header('Location: demandesAugmentationSolde.php?alert=setFlagAccepter+error');
                }
                if ($nbDemandeSolde == 2) //// 10 piece de bronze-----------------------------------------------
                {
                    try {
                        $reussi = executerDML("UPDATE joueurs SET solde_bronze = solde_bronze+10 WHERE id = :idJoueur", [
                            ":idJoueur" => $idJoueur
                        ]);
                    } catch (Exception $e) {header('Location: demandesAugmentationSolde.php?alert=10bronze+error');}
                    if (!$reussi) {
                        header('Location: demandesAugmentationSolde.php?alert=10bronze+error');
                    }
                } else if ($nbDemandeSolde == 1) /// 10 piece dargent--------------------------------------
                {
                    try {
                        $reussi = executerDML("UPDATE joueurs SET solde_argent = solde_argent+ 10 WHERE id = :idJoueur", [
                            ":idJoueur" => $idJoueur
                        ]);
                    } catch (Exception $e) { headerQ('Location: demandesAugmentationSolde.php?alert=10argent+error');}
                    if (!$reussi) {
                        headerQ('Location: demandesAugmentationSolde.php?alert=10argent+error');
                    }
                } else //// 10 piece d'or----------------------------------------------------------------
                {
                    try {
                        $reussi = executerDML("UPDATE joueurs SET solde_or = solde_or + 10 WHERE id = :idJoueur", [
                            ":idJoueur" => $idJoueur
                        ]);
                    } catch (Exception $e) {headerQ('Location: demandesAugmentationSolde.php?alert=10or+error');}
                    if (!$reussi) {
                        headerQ('Location: demandesAugmentationSolde.php?alert=10or+error');
                    }
                }
                try {
                    $reussi = executerDML("UPDATE joueurs SET nbDemandeSolde = nbDemandeSolde + 1 WHERE id = :idJoueur", [
                        ":idJoueur" => $idJoueur
                    ]);
                } catch (Exception $e) {headerQ('Location: demandesAugmentationSolde.php?alert=nbDemandeSolde+error&alertc=purple');}
            }
            if (!$reussi) {
                headerQ('Location: demandesAugmentationSolde.php?alert=accepter+error');
            }
            headerQ('Location: demandesAugmentationSolde.php?alert=accepter+valide&alertc=green');
        }
        if ($_POST['choix'] == "refuser") {
            $idDemande = $_POST['idDemande'];
            $reussi = false;
            try {
                $reussi = executerDML("DELETE FROM demandes WHERE id = :idDemande", [":idDemande" => $idDemande]);
            } catch (Exception $e) {headerQ('Location: demandesAugmentationSolde.php?alert=refus+error');}
            if (!$reussi) {
                headerQ('Location: demandesAugmentationSolde.php?alert=refus+error');
            }
            headerQ('Location: demandesAugmentationSolde.php?alert=refus+valide&alertc=green');
        }
    }
    ?>
    <br> <br> <br> <br>
    <div id="scroll">
        <div class="itemsTable">
            <?php
            $sql = "select * from demandes where flag_accepte is null";
            $table = executerSelectTable($sql);
            echo '<table>';
            echo '<tr>';
            echo '<th>Id du joueur </th>';
            echo '<th>Message</th>';
            echo '<th>Accepter</th>';
            echo '<th>Refuser</th>';
            echo '</tr>';
            while ($rows = $table->fetch()) {
                echo '<form method="POST">';
                echo '<tr>';
                echo '<td>' . obtenirUserData($rows['joueurs_id'])['alias'] . '</td>';
                echo '<td>' . $rows['message'] . '</td>';
                echo '<td><input type="submit" name="choix" value="accepter"></td>';
                echo '<td><input type="submit" name="choix" value="refuser"></td>';
                echo '<input type="hidden" name="idDemande" value="' . $rows['Id'] . '">';
                echo '<input type="hidden" name="idJoueur" value="' . $rows['joueurs_id'] . '">';
                echo '<tr>';
                echo '</form>';
            }
            echo '</table>'
            ?>
        </div>
    </div>
</body>
</html>