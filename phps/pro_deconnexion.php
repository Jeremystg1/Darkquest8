<?php

require_once 'constructeur.php';
activerSessionSurPage();

$userid = $_SESSION["userid"];
unset($_SESSION["userid"]);
session_destroy();                // supprime le fichier de session
session_unset();                  // supprime le tableau des variables
setcookie("PHPSESSID", null, -1); // supprime le cookie

header("Location: " . getAppRoot(). "/index.php?alert=Deconnexion+réussie&alertc=red");


?>