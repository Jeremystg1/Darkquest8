<?php
include 'phps/db.php';
require_once 'phps/constructeur.php';
require_once 'phps/evaluationsManager.php';
desactiverForceLogin();
activerSessionSurPage();

function CheckAdmin()
{
    if (isset($_SESSION["userid"])) {
        $sql = "select * from  joueurs where id =" . $_SESSION["userid"];
        $table = executerSelectTable($sql);
        while ($rows = $table->fetch()) {
            return $rows["isAdmin"];
        }
        return null;
    } else {
        return null;
    }
}

if (CheckAdmin() != 1) {
    header('Location: index.php?alert=Vous+devez+etre+administateur+pour+acceder+a+cette+page!');
}
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
        .buttonSub[type="submit"]:hover{
            width: 70%;
            background-color: green;
            color: white;
        }
        .buttonSub[type="submit"]{
            width: 60%;
            background-color: greenyellow;
            color: black;
            font-weight: bolder;
            transition: .2s;
        }
        .formBase > fieldset{
            text-align: center;
            width: 20em;
            min-width: 30%;
            background-color: blueviolet;
            border-radius: 1em;
            color: white;
            margin: auto;
        }
    </style>

</head>

<body>
    <?php
    faireHeader(); 

    if(isset($_GET["demandes"])){
        if($_GET["demandes"]  == "Gérer les demandes"){
            header('Location: demandesAugmentationSolde.php');
        }
    }
    
    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        $nom = "enigme";
        $question = $_POST['enigme'];
        $disponible = 1;
        $difficulte = $_POST['niveau'];
        $bonneReponse = $_POST['bonneReponse'];
        $mauvaiseReponse1 = $_POST['mauvaiseReponse1'];
        $mauvaiseReponse2 = $_POST['mauvaiseReponse2'];
        $mauvaiseReponse3 = $_POST['mauvaiseReponse3'];

        $reussi = false;
        try {
            $reussi = executerDML("INSERT INTO enigmes (nom, question,disponible,difficulte,bonneReponse,mauvaiseReponse1,mauvaiseReponse2,mauvaiseReponse3) VALUES (:nom, :question,:disponible,:difficulte,:bonneReponse,:mauvaiseReponse1,:mauvaiseReponse2,:mauvaiseReponse3) ", [
                ":nom" => $nom,
                ":question" => $question,
                ":disponible" => $disponible,
                ":difficulte" => $difficulte,
                ":bonneReponse" => $bonneReponse,
                ":mauvaiseReponse1" => $mauvaiseReponse1,
                ":mauvaiseReponse2" => $mauvaiseReponse2,
                ":mauvaiseReponse3" => $mauvaiseReponse3

            ]);
        } catch (Exception $e) {
            header('Location: admin.php?alert=creation+enigme+invalide');
        }

        if (!$reussi) {
            header('Location: admin.php?alert=creation+enigme+invalide');
        }

        header('Location: admin.php?alert=creation+enigme+valide&alertc=green');
    }

    ?>
    <br> <br> <br> <br>

    <div>
        <form method="POST" class="formeOuInscrip">
            <fieldset>
                <legend>Créer nouvelle énigme:</legend>
                <label for="enigme">Énigme</label>
                <input type="text" id="enigme" name="enigme" required>
                <br>
                <label for="bonneReponse">Bonne réponse:</label>
                <input type="text" id="bonneReponse" name="bonneReponse" required>
                <br>
                <label for="mauvaiseReponse1">Mauvaise réponse 1:</label>
                <input type="text" id="mauvaiseReponse1" name="mauvaiseReponse1" required>
                <br>
                <label for="mauvaiseReponse2">Mauvaise réponse 2:</label>
                <input type="text" id="mauvaiseReponse2" name="mauvaiseReponse2" required>
                <br>
                <label for="mauvaiseReponse3">Mauvaise réponse 3:</label>
                <input type="text" id="mauvaiseReponse3" name="mauvaiseReponse3" required>
                <br>
                <label for="hard">Difficile</label>
                <input type="radio" id="hard" name="niveau" value="3" required><br>
                <label for="medium">Medium</label>
                <input type="radio" id="medium" name="niveau" value="2"><br>
                <label for="easy">Facile</label>
                <input type="radio" id="easy" name="niveau" value="1"><br>
                <input type="submit" name="enregistrer" value="Enregistrer">
            </fieldset>
        </form>

        <form class="formBase" method="GET">
            <fieldset>
                <input class="buttonSub" type="submit" name="demandes" value="Gérer les demandes">
            </fieldset>
        </form>
    </div>
</body>

</html>