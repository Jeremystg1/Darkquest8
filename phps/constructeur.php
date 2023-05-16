<script src="https://kit.fontawesome.com/ce99d29144.js" crossorigin="anonymous"></script>
<?php

$GLOBALS["connecté"] = false;
$GLOBALS["forceLogin"] = true;
$solde = "";
function desactiverForceLogin(){
    $GLOBALS["forceLogin"] = false;
}
function headerQ($stringe){
    //header($stringe);
    //exit();
    $location = substr($stringe,0,9);
    if($location == "Location:"){
        echo "<script>window.location.href = '" .substr($stringe,9) ."';</script>";
    }
}
function activerSessionSurPage(){
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if(empty($_SESSION["userid"])){
        if($GLOBALS["forceLogin"] == true){
            headerQ("Location: " . getAppRoot() . "/connexion.php?alert=Veuillez+vous+connecter");
        }
    } else{
        $GLOBALS["connecté"] = true;
        //echo "<script> alert('vous etes connecté') </script>";
    }
}

if(!empty($_GET["alert"])){
    //echo '<script>alert("'.$_GET["alert"].'")</script>';
    $couleur = "rgb(177, 0, 0);";
    if(!empty($_GET["alertc"])){
        $couleur = $_GET["alertc"];
    }
    $GLOBALS["alertmsg"] =
     '<p id="_alertc" style="width: 100%; font-family: Arial, Helvetica, sans-serif; margin: auto; font-weight: bold; 100px;text-align:center; background-color: '.$couleur.'; color: white;">'.$_GET["alert"].'</p>';
}
function obtenirNomsUser(){
    if(isset($_SESSION["userid"])){
        return obtenirUserData($_SESSION["userid"])["alias"];
    }
    return "";
}
function obtenirNomsUserAvecBonjour(){
    if($GLOBALS["connecté"] == true){
        return "Bienvenue <a href='" . getAppRoot() ."/modificationProfil.php'> <b>". obtenirNomsUser() . " </b> </a>";
    } else{ return ""; }
}
function obtenirNomApplication(){
    return "Darquest 8";
}
function getAppRoot(){
    return "http://167.114.152.54/~darquest8";
}
function obtenirHREFDeconnexion(){
    if($GLOBALS["connecté"] == true){
        return '<div style="float:right; background-image: url(\'./Images/logoutIcon.png\')" class="icon"><a class="iconLink" title="Deconnexion" href="' . getAppRoot(). '/phps/pro_deconnexion.php"></a></div>';
    } else { 
        return '<style>.invisMobil{
                display: none;}
                .iconRows{
                    justify-content:center;
                }
                .headerGenere{
                    grid-template-columns: repeat(auto-fit,33.3333%);
                }
                </style><div style="float:right; background-image: url(\'./Images/userIcon.png\')" class="icon"><a class="iconLink" title="Connexion" href="' . getAppRoot(). '/connexion.php"></a></div>'; }
}
function obtenirMenuNomUser()
{
  if(!empty($_SESSION['userid']))
  {
    return '<div id=userNameDisplay ><h3 style="margin:0px;margin-top: 0.75em;color:white;font-weight:lighter">Bonjour, '. obtenirNomsUser() . ' !</h3></div>';
  }  
  else
  {
    return "";
  }
}
function getAdminBtn(){
    if(empty($_SESSION["userid"])){return;}
    if(!isUserAdmin($_SESSION["userid"])){
        return;
    }
    return '
    <div style="background-image: url(\'./Images/shield.png\')" class="adminIcon"><a class="iconLink" title="Administration" href="' . getAppRoot(). '/admin.php"></a></div>
    ';
}
function obtenirGoldUser()
{
    if(empty($_SESSION['userid']))
    {
        return ""; /*"<div id='statBar'><span>" . "</span>
                    <div id='pill'>
                    <span> ". "<a href=connexion.php>Connectez-vous!</a>" ." </span>
                    </div>
                </div>";*/
    }
    else
    {   
        //Chercher les données des soldes
        $soldeBronze = GetSolde("bronze");
        $soldeArgent = GetSolde("argent");
        $soldeOr = GetSolde("or");
    $displayNone = empty($_SESSION["userid"]);
    return "<div style='display: $displayNone' id='statBar'><span>" . "</span>
                <div id='pill'>
                    <div class=\"soldeGrid\">
                        <img class='stats' alt=\"gold\" src=\"./Images/gold.png\"/>
                        <span> ". $soldeOr ." </span>
                    </div>
                    <div class=\"soldeGrid\">
                        <img class='stats' alt=\"silver\" src=\"./Images/silver.png\"/>
                        <span> ". $soldeArgent ." </span>
                    </div>
                    <div class=\"soldeGrid\">
                        <img class='stats' alt=\"bronze\" src=\"./Images/bronze.png\"/>
                        <span> ". $soldeBronze ." </span>
                    </div>
                </div>
            </div>"; 
    }
}
function faireHeader(){
    $hrefDecoReco = obtenirHREFDeconnexion();
    $nomApp = obtenirNomApplication();
    $nomUser = obtenirGoldUser();
    $bleuFoncé = "bleuFoncé";
    if(isset($GLOBALS["alertmsg"])){
        echo $GLOBALS["alertmsg"];
    }
    if($nomUser == ""){$bleuFoncé = "";}
    echo '        <div class="headerGenere" style="line-height: 2em;text-align: center;">
    
    <div class="divEspace"> <a id="btnmenuhomeheader" style="color: white; text-decoration: none;" href="' . getAppRoot(). 
    '"><span style="text-align: center;"id=PhoneTitle>'. $nomApp. '</span></a></div><div class="divEspace invisMobil"></div>

    <div class="divEspace invisMobil '.$bleuFoncé.'">'. $nomUser. '</div>
    <div class="divEspace iconRows">
    <div id="PhoneShow" style="background-image: url(\'./Images/money-bagIcon.png\')" class="icon"><a class="iconLink" title="Solde" href="' . getAppRoot(). '/solde.php"></a></div>
        <div style="background-image: url(\'./Images/loginIcon.png\')" class="icon"><a class="iconLink" title="Modifier profil" href="' . getAppRoot(). '/modificationProfil.php"></a></div>
        <div style="background-image: url(\'./Images/inventoryIcon.png\')" class="icon"><a class="iconLink" title="Inventaire" href="' . getAppRoot(). '/inventaire.php"></a></div>
        <div style="background-image: url(\'./Images/cartIcon.png\')" class="icon"><a class="iconLink" title="Panier" href="' . getAppRoot(). '/panier.php"></a></div>
        <div style="background-image: url(\'./Images/quest.png\')" class="icon"><a class="iconLink" title="Enigma" href="' . getAppRoot(). '/enigma.php"></a></div>
    </div>
    <div>'.$hrefDecoReco.'</div>
</div>
'.obtenirMenuNomUser(). getAdminBtn();
}
function faireBouttonRetourHome(){
    echo '
    <a id="btnmenuhomeheader" style="text-decoration:none; background-color: royalblue; padding: 0 1em; color: white;" href="' . getAppRoot(). '"> Retour </a>
    ';
}
function obtenirApplicationHead(){
    echo '<link rel="stylesheet" href="' . getAppRoot() .'/css/styleIndex.css">
        <script src="' .getAppRoot(). '/phps/alertc.js"></script>
        <link rel="stylesheet" href="' . getAppRoot() .'/css/divItemElementGenere.css">
        <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=MedievalSharp" />
    ';
}

function getTypeItemName($abv){
    $abv = strtolower($abv);
    if($abv == "so"){
      return "Sorts";
    }
    else if($abv == "ar"){
      return "Armes";
    }
    else if($abv == "am"){
      return "Armures";
    }
    else if($abv == "so"){
      return "Sorts";
    }
    else if($abv == "po"){
      return "Potions";
    }
    else{
      return "Autres";
    }
  }


?>