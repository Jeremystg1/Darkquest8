
<?php
require_once "phps/db.php";
require_once "phps/hashEtEncrypt.php";
require_once "phps/constructeur.php";
activerSessionSurPage();

function CheckAdmin(){
    $sql = "select * from  joueurs where id =" . $_SESSION["userid"];
      $table = executerSelectTable($sql);
      while ($rows = $table->fetch()) {
        return $rows["isAdmin"];
      }
      return null;
}

if(CheckAdmin() != 1){
    header('Location: index.php?alert=Vous+devez+etre+administateur+pour+acceder+a+cette+page!');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=MedievalSharp" />
    <title><?php echo obtenirNomApplication(); ?></title>
    <?php obtenirApplicationHead(); ?>
    <style>
        .modificationProfil {
            margin-left: auto;
            margin-right: auto;
            margin-top: 75px;
            max-width: 100%;
        }

        @media(orientation: portrait) {
            .modificationProfil {
                display: grid;
                grid-template-columns: auto;
                margin-top: 30px;
                max-width: 100%;

            }
        }
    </style>
</head>

<?php
    if(isset($_POST["Ajouter"])){
        //ajout enigme
    }
?>
<body>
<?php faireHeader(); ?>
<div class="modificationProfil">
    <form method="POST" class="formeOuInscrip">
            <fieldset>
                <legend>Ajouter un énigme</legend>
                

                <label for="Question">Question</label>
                <input type="text" id="Question" name="Question" required>
                <br>
                <label for="Reponse1">Reponse 1</label>
                <input type="text" id="Reponse1" name="Reponse1" required>
                <br>
                <label for="Reponse2">Reponse 2</label>
                <input type="text" id="Reponse2" name="Reponse2" required>
                <br>
                <label for="Reponse3">Reponse 3</label>
                <input type="text" id="Reponse3" name="Reponse3" required>
                <label for="Reponse4">Reponse 4</label>
                <input type="text" id="Reponse4" name="Reponse4" required>

                <br><br>
                <label for="Difficulté">Difficulté :</label>
                <select name="Difficulté" id="Difficulté">
                    <option value="facile" selected>facile</option>
                    <option value="moyen">moyen</option>
                    <option value="difficile">difficile</option>
                </select><br>
                <label for="BonneReponse">Bonne Reponse :</label>
                <select name="BonneReponse" id="Reponse">
                    <option value="1" selected>Reponse 1</option>
                    <option value="2">Reponse 2</option>
                    <option value="3">Reponse 3</option>
                    <option value="4">Reponse 4</option>
                </select><br><br>
                

                <input type="submit" name="Ajouter" value="Ajouter">
            </fieldset>
        </form>
</div>
</body>

</html>