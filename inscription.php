<?php
require_once "phps/hashEtEncrypt.php";
require_once "phps/db.php";
require_once 'phps/constructeur.php';
//require_once 'phps/emails.php';
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $GLOBALS["reussiInscrip1"] = false;
    if (isset($_POST['nom']) == true) {

        $listeEmail = getListeEmail();
        $listeEmailGood = [];
        
        while ($rows = $listeEmail->fetch()) { 
        
            //echo $rows;
            $listeEmailGood[] = $rows["email"];
        } 
       // echo "<script> alert(" .json_encode($listeEmailGood) .")</script>";
        //exit();
        /**/
        if (in_array(trim($_POST['email']), $listeEmailGood)) {
            $emailInvalide = true;
            header('Location: inscription.php?alert=email+déjà+utilisé');
            exit();
        }
        if (in_array(trim($_POST['alias']), getListeAlias())) {
            $emailInvalide = true;
            header('Location: inscription.php?alert=alias+déjà+utilisé');
            exit();
        }
        else 
        {
            $emailInvalide = false;
            $alias = trim($_POST['alias']);
            $prenom = trim($_POST['prenom']);
            $email = trim($_POST["email"]);
            $nom = trim($_POST['nom']);
            $motDePasse = trim($_POST['password']);
            $role = trim($_POST['role']);
            $motDePasseHash = obtenirNewPassHash($motDePasse);
            //get hashed pwd

            $GLOBALS["reussiInscrip1"] = false;
            try {
                executerDML("INSERT INTO joueurs (alias, prenom,nom, role,email,pwdhash,etatcompte,dateCreation) VALUES (:alias,:prenom,:nom,:role,:email,:motDePasse,1,:dateCreation)", [
                    ":alias" => $alias,
                    ":prenom" => $prenom,
                    ":nom" => $nom,
                    ":role" => $role,
                    ":email" => $email,
                    ":dateCreation" => date('Y-m-d H:i:s'),
                    ":motDePasse" => $motDePasseHash
                    
                ]);//":etatcompte" => 0
                $GLOBALS["reussiInscrip1"] = true;
                //echo "<script> alert(".$GLOBALS["reussiInscrip"].")</script>";
            } 
            catch (Exception $e) 
            {
                $GLOBALS["reussiInscrip1"] = false;
                executerDML("INSERT INTO erreurs (erreur, dateC) VALUES(:eu, :da)",[
                    ":eu" => $e->getMessage(),
                    ":da" => date('l d m Y h:i:s'),
                ]);
                header('Location: inscription.php?alert=DODODODODO+échoué&reussiInscrip='.$GLOBALS["reussiInscrip1"]);
            }
        




        if(!empty($GLOBALS["reussiInscrip1"]))
        {
            if($GLOBALS["reussiInscrip1"] == true){
                header('Location: index.php?alert=inscription+réussi&alertc=green&reussiInscrip='.$GLOBALS["reussiInscrip1"]);
                exit();
            }
        }
        
        }
    }

}






?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo obtenirNomApplication(); ?>
    </title>
    <?PHP obtenirApplicationHead(); ?>
    <style>
        .soldier {
            position: fixed;
            left: 0px;
            bottom: 0px;
            z-index: 5000;
            width: 300px;
            height: 500px;
            -webkit-transform: scaleX(-1);
            transform: scaleX(-1);
            background-repeat: no-repeat;
            background-size: contain;
        }

        .bubble {
            background-color: white;
            width: 150px;
            height: 50px;
            border-radius: 25px;
            text-align: center;
            padding: 20px;
            left: 100px;
            font-weight: bold;
        }

        .frame {
            height: fit-content;
            text-align: center;
        }
    </style>
</head>

<body>
    <?php 
    if(isset($GLOBALS["alertmsg"])){
        echo $GLOBALS["alertmsg"];
    }
    ?>
    <br>
    <form method="POST" class="formeOuInscrip">
        <fieldset>
            <legend>Veuillez vous inscrire</legend>
            <label for="alias">Alias</label>
            <input type="text" id="alias" name="alias" required>
            <br>
            <label for="prenom">Prenom</label>
            <input type="text" id="prenom" name="prenom" required>
            <br>
            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom" required>
            <br>
            <label for="password">Mot de passe</label><br>
            <input type="password" id="password" name="password" required>
            <br>
            <label for="email">Email</label><br>
            <input type="email" id="email" name="email" required>
            <br>
            <div>Role:
                <br>
                <label for="c">Chevalier</label>
                <input type="radio" id="c" name="role" value="c" checked>
                <br>
                <label for="m">Mage</label>
                <input type="radio" id="m" name="role" value="m">
                <br>
                <label for="a">Archer</label>
                <input type="radio" id="a" name="role" value="a">
                <br>
            </div>
            <br>
            <input type="submit" value="S'inscrire">
            <br>
            <?php faireBouttonRetourHome(); ?>
            <br>
        </fieldset>
    </form>
    <div class=frame>
        <div class=bubble>Joins toi à nous si tu veux être chevalier!</div>
        <img class=soldier style="background-image: url(https://i.pinimg.com/originals/d2/5c/a5/d25ca5c08791d5ce4d94dfffff296583.png);"></img>
    </div>
</body>

</html>