<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=MedievalSharp" />
<?php
///
require_once "phps/hashEtEncrypt.php";
require_once "phps/db.php";
require_once 'phps/constructeur.php';
if ($_SERVER['REQUEST_METHOD'] == "POST") {

    
if(count($_POST) != 2){
    header("Location: login.php");
}


session_start();
$username = $_POST["nom"];
$pwd = $_POST["password"];
$headerLocation = "";
$headerSuffix = "";
$userdata = obtenirUserDataByAlias($username);
//echo '<script>alert("'.json_encode($userdata).'")</script>';
if(is_bool($userdata)){
    if($userdata == false){
         header("Location: connexion.php?alert=Compte+inexistant");
         exit();
    }
}
$pwdhash = $userdata["pwdhash"];
$success = false;
if($userdata["etatcompte"] <= 0){ 
    //echo json_encode($userdata);
    $headerLocation = "Location: connexion.php";
    $headerSuffix = "?alert=Compte+non+actif";
}elseif (verifierPassword($pwd,$pwdhash)){
    $_SESSION["userid"] = $userdata["id"];
    $_SESSION["username"] = $userdata["alias"];
    $success = true;
    $_SESSION["connecté"] = true;
    $GLOBALS["connecté"] = true;
    $headerLocation = "Location: index.php?alert=Connexion+Réussie&alertc=green";
} else {
    $headerLocation = "Location: connexion.php";
    $headerSuffix = "?alert=Mauvais+mot+de+passe";
}
header($headerLocation.$headerSuffix);
}

?>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo obtenirNomApplication(); ?></title>
    <?php obtenirApplicationHead(); ?>
</head>

<body>
<?php 
    if(isset($GLOBALS["alertmsg"])){
        echo $GLOBALS["alertmsg"];
    }
    ?>
    <br>
    <form method="POST" class="formeOuInscrip">
        <fieldset >
            <legend>Veuillez vous identifer</legend>
            <label for="larg">Nom d'utilisateur</label>
            <input type="text" id="nom" name="nom" required>
            <br>
            <label for="haut">Mot de passe</label><br>
            <input type="password" id="password" name="password" required>
            <br>
            <input type="submit" value="Se connecter"><br>
            <br>
            <a id="pasDeCompteLoginForm" href="inscription.php"> Pas de compte?</a>
            <?php faireBouttonRetourHome();?>
        </fieldset>
    </form>

   

    
</body>

</html>

