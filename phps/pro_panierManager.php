<?php
require_once 'panierManager.php';
require_once 'constructeur.php';
desactiverForceLogin();
activerSessionSurPage();

if(isset($_GET["add"]) && isset($_GET["qty"]))
{
    ajouterItem($_GET["add"],$_GET["qty"],false);
}

if(isset($_GET["remove"]) && isset($_GET["qty"]))
{
    enleverItem($_GET["remove"],$_GET["qty"],false);
}
if(isset($_GET["set"]) && isset($_GET["qty"]))
{
    setItem($_GET["set"],$_GET["qty"],false);
}
if(isset($_GET["allBuy"]) && isset($_GET["qty"]))
{
    enleverToutItem($_GET["allBuy"],$_GET["qty"],false);
}
if(isset($_GET["ConvertirSolde"]) && isset($_GET["totalPrix"])){
    NeedConversion($_GET["totalPrix"],false);
}
header("Location: " . getAppRoot() ."/panier.php")
?>