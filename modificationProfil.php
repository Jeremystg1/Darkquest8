
<?php
require_once "phps/db.php";
require_once "phps/hashEtEncrypt.php";
require_once "phps/constructeur.php";
activerSessionSurPage();
$id = $_SESSION["userid"];
$userdata = obtenirUserData($_SESSION['userid']);
$pwdHash = $userdata["pwdhash"];
$role = $userdata["role"];

function obtenirChecked($r)
{
    global $role;
    if ($r == $role) {
        echo "checked";
    }
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {


    $pwdCourant = trim($_POST['passwordCourant1']);

    if ($pwdCourant == null) {
        $pwdCourant = trim($_POST['passwordCourant2']);
    }


    if ($pwdCourant == null) {
        $pwdCourant = trim($_POST['passwordCourant3']);
    }

    if (!verifierPassword($pwdCourant, $pwdHash)) {
        ////PASSWORD INVALIDE

        header('Location: modificationProfil.php?alert=Mot+de+passe+Invalide&alertc=red');
    } else {


        if (isset($_POST['supprimerProfil'])) {

            $reussi = false;
            try {
                $reussi = executerDML("DELETE FROM joueurs WHERE id = :id", [
                    ":id" => $id
                ]);
            } catch (Exception $e) {
            }

            if (!$reussi) {
                header('Location: modificationProfil.php?alert=Erreur+de+delete&alertc=red');
            }

            header('Location: index.php?alert=Compte+Supprimer&alertc=red');
        }

        if (isset($_POST['aliasBouton'])) {
            ///CHANGER ALIAS
            $alias = trim($_POST['alias']);


            $reussi = false;
            try {
                $reussi = executerDML("UPDATE joueurs SET alias = :nouveauAlias WHERE id = :id", [
                    ":nouveauAlias" => $alias,
                    ":id" => $id
                ]);
            } catch (Exception $e) {
            }

            if (!$reussi) {
                header('Location: modificationProfil.php?alert=Échec+modification+alias&alertc=red');
            }
            header('Location: index.php?alert=Modification+alias+réussi&alertc=green');
        }

        if (isset($_POST['passwordModifier'])) {
            $pwd1 = trim($_POST['passwordNouveau1']);
            $pwd2 = trim($_POST['passwordNouveau2']);


            if (strcmp($pwd1, $pwd2) != 0) {
                ///PASSWORD DIFFERENT

                header('Location: modificationProfil.php?alert=Password+non+similaire&alertc=red');
            } else {

                ///CHANGER PASSWORD

                $nouveauPwdHash = obtenirNewPassHash($pwd1);
                $reussi = false;
                try {
                    $reussi = executerDML("UPDATE joueurs SET pwdhash = :nouveauPwdHash WHERE id = :id", [
                        ":nouveauPwdHash" => $nouveauPwdHash,
                        ":id" => $id
                    ]);
                } catch (Exception $e) {
                }

                if (!$reussi) {
                    header('Location: modificationProfil.php?alert=Échec&alertc=red');
                }
                header('Location: index.php?alert=Changement+de+mot+de+passe+réussi&alertc=green');
            }
        }
    }

    if (isset($_POST['roleBouton'])) {
        $nouveauRole = trim($_POST['role']);


        $reussi = false;
        try {
            $reussi = executerDML("UPDATE joueurs SET role = :nouveauRole WHERE id = :id", [
                ":nouveauRole" => $nouveauRole,
                ":id" => $id
            ]);
        } catch (Exception $e) {
        }

        if (!$reussi) {
            header('Location: modificationProfil.php?alert=Changement+role+pas+réussi&alertc=red');
        }
        header('Location: index.php?alert=Changement+role+réussi&alertc=green');
    }
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
            display: grid;
            grid-template-columns: auto auto auto;
            margin-top: 30px;
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


<body>
<?php faireHeader(); ?>
<br>
<br>
    <div class="modificationProfil">
        <form method="POST" class="formeOuInscrip">
            <fieldset>
                <legend>Modifier votre Alias!</legend>
                <label for="alias">Nouveau Alias</label>
                <input type="text" id="alias" name="alias" required>
                <br>
                <label for="passwordCourant1">Mot de passe</label>
                <input type="text" id="passwordCourant1" name="passwordCourant1" required>
                <br>
                <input type="submit" name="aliasBouton" value="Modifier">
            </fieldset>
        </form>

        <form method="POST" class="formeOuInscrip">
            <fieldset>
                <legend>Modifier votre mot de passe!</legend>
                <label for="passwordCourant2">Mot de passe courant</label>
                <input type="text" id="passwordCourant2" name="passwordCourant2" required>
                <br>
                <label for="passwordNouveau1">Nouveau mot de passe</label>
                <input type="text" id="passwordNouveau1" name="passwordNouveau1" required>
                <br>
                <label for="passwordNouveau2">Nouveau mot de passe</label>
                <input type="text" id="passwordNouveau2" name="passwordNouveau2" required>
                <br>
                <input type="submit" name="passwordModifier" value="Modifier">
            </fieldset>
        </form>


        <form method="POST" class="formeOuInscrip">
            <fieldset>
                <label for="c">Chevalier</label>
                <input type="radio" id="c" name="role" value="c" <?php obtenirChecked("c") ?>>
                <br>
                <label for="m">Mage</label>
                <input type="radio" id="m" name="role" value="m" <?php obtenirChecked("m") ?>>
                <br>
                <label for="a">Archer</label>
                <input type="radio" id="a" name="role" value="a" <?php obtenirChecked("a") ?>>
                <br>
                <label for="passwordCourant3">Mot de passe</label>
                <input type="text" id="passwordCourant3" name="passwordCourant3" required>
                <br>
                <input type="submit" name="roleBouton" value="Modifier">
                <br><br>
                <input style="background-color:red" type="submit" name="supprimerProfil" value="Supprimer votre compte">
            </fieldset>
        </form>
    </div>

    <?php /* echo "<a href='" . getAppRoot() . "/inventaire.php'>Inventaire</a>"; */?>
</body>

</html>