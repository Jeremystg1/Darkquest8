<?php
require_once "phps/db.php";
require_once "phps/constructeur.php";
require_once "phps/enigManager.php";
activerSessionSurPage();

function validerReponse()
{
  if (isset($_GET['Reponse']) && isset($_GET['idEnigme'])) {
    donnerPointsSelonDifficulte($_GET['Reponse'], $_GET['idEnigme']);
  }
}
validerReponse();

?>

<!DOCTYPE html>
<html lang="fr">
<meta name="viewport" content="width=device-width, initial-scale=1">

<head>
  <meta charset="UTF-8">
  <title>Enigma</title>

  <style>
    #align:nth-child(even) {
      background-color: #222222;
    }

    .detailsText:hover {
      color: rgb(0, 150, 0);
    }

    .detailsButton {
      text-align: center;
    }

    .hoverEffect {
      -webkit-transition-duration: 0.2s;
      /* Safari */
      transition-duration: 0.2s;
      cursor: pointer;
    }

    .hoverEffect:hover {
      background-color: gray;
      color: rgb(10, 10, 10);
      outline: rgb(101, 55, 205) 10px solid;
    }

    .detailsText {
      color: rgb(0, 200, 0);
      font-size: 20px;
      font-weight: bold;
    }

    .styleBoutton {
      background-color: rgb(100, 100, 100);
      outline: black 4px solid;
      margin-top: 0.5em;
      color: white;
      font-weight: bold;
    }

    .styleBoutton:hover {
      outline: gray 4px solid;
      outline: rgb(101, 55, 205) 4px solid;
      color: black;
      background-color: white;
    }

    footer {
      position: fixed;
      left: 0;
      bottom: 0;
      width: 100%;
      color: white;
      text-align: center;
    }

    .wrapper {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 10px;
      grid-auto-rows: minmax(50px, auto);
      margin-top: 5px;
    }

    .boxW {

      --v: calc(((18/5) * var(--p) - 90)*1deg);
      display: flex;
      justify-content: center;
      font-size: 1.5em;
      font-weight: bolder;
      align-items: center;
      margin: auto;
      width: 100px;
      text-shadow: -1px -1px 0 #000, 1px -1px 0 #000, -1px 1px 0 #000, 1px 1px 0 #000;
      height: 100px;
      display: inline-block;
      border-radius: 50%;
      padding: 10px;
      background:
        linear-gradient(#605d61, #605d61) content-box,
        linear-gradient(var(--v), #988f9c 50%, transparent 0) 0/min(100%, (50 - var(--p))*100%),
        linear-gradient(var(--v), transparent 50%, green 0) 0/min(100%, (var(--p) - 50)*100%),
        linear-gradient(to right, #988f9c 50%, green 0);
    }

    .boxL {

      --v: calc(((18/5) * var(--p) - 90)*1deg);
      display: flex;
      justify-content: center;
      align-items: center;
      margin: auto;
      width: 100px;
      font-size: 1.5em;
      font-weight: bolder;
      text-shadow: -1px -1px 0 #000, 1px -1px 0 #000, -1px 1px 0 #000, 1px 1px 0 #000;
      height: 100px;
      display: inline-block;
      border-radius: 50%;
      padding: 10px;
      background:
        linear-gradient(#605d61, #605d61) content-box,
        linear-gradient(var(--v), #988f9c 50%, transparent 0) 0/min(100%, (50 - var(--p))*100%),
        linear-gradient(var(--v), transparent 50%, red 0) 0/min(100%, (var(--p) - 50)*100%),
        linear-gradient(to right, #988f9c 50%, red 0);
    }

    .stats {
      text-align: center;
      font-weight: bold;
      color: whitesmoke;
    }
  </style>
  <?php
  faireHeader();
  echo obtenirApplicationHead();
  ?>
</head>

<body>
  <br>
  <br>
  <?php



  function envoyerMenu()
  {
    if (isset($_GET['Difficulte'])) {
      afficherEnigme($_GET['Difficulte']);
      afficherWinRatio(); ///
      afficherLossRatio(); ///
    } else {
      afficherChoix();
      afficherWinRatio(); ///
      afficherLossRatio(); ///

    }
  }


  envoyerMenu();
  function afficherChoix()
  {

    echo "
        <br>
        <div class=\"enigmaBubble firstBubble\"><h3 class=\"enigmaText\">Choisissez la Question</h3></div>
        <br>
        <form method=\"GET\">
            <input type=\"submit\" style=\"color:green\" class=\"enigmaBubble enigmaText hoverEffect\" name=\"Difficulte\" value=\"Facile\" />
            <br>
            <input type=\"submit\" style=\"color:yellow\" class=\"enigmaBubble enigmaText hoverEffect\" name=\"Difficulte\" value=\"Moyen\" />
            <br>
            <input type=\"submit\" style=\"color:red\" class=\"enigmaBubble enigmaText hoverEffect\" name=\"Difficulte\" value=\"Difficile\" />
            <br>
            <br>
            <br>
            <input type=\"submit\" style=\"color:white;\" class=\"enigmaBubble enigmaText hoverEffect\" name=\"Difficulte\" value=\"Aléatoire\" />
        <form>

        
        <br>";
  }

  function afficherWinRatio() ////
  {
    $idJoueur = $_SESSION['userid'];
    if (getNbQuestionReponduJoueur($idJoueur) != 0) {

      $winRatio = getWinJoueur($idJoueur) / getNbQuestionReponduJoueur($idJoueur) * 100;
      //echo "<div>" . $winRatio . "%</div>";
      $winRatio = round($winRatio, 2);

      echo '<div class="wrapper">';
      echo '<div class="boxW" style="--p:' . $winRatio . ';"><div class="stats">' . $winRatio . '%</div></div>';
    }
  }

  function afficherLossRatio() ////
  {
    $idJoueur = $_SESSION['userid'];
    if (getNbQuestionReponduJoueur($idJoueur) != 0) {
      
      $lossRatio = getLossJoueur($idJoueur) / getNbQuestionReponduJoueur($idJoueur) * 100;

      $lossRatio = round($lossRatio, 2);

      //echo "<div>" . $lossRatio . "%</div>";
      echo '<div class="boxL" style="--p:' . $lossRatio . ';"><div class="stats">' . $lossRatio . '%</div></div>';
      echo '</div>';
    }
  }




  function afficherEnigme($difficulte)
  {
    $enigme = obtenirEnigmeParDifficulté($difficulte);
    if (is_null($enigme)) {
      echo "<script> window.location.href = '" . getAppRoot() . "/enigma.php?alert=Aucune+enigme+disponible+pour+cette+difficulté'; </script>";
    }
    $question = $enigme['question'];


    $bonneReponse = $enigme['bonneReponse'];
    $mauvaiseReponse1 = $enigme['mauvaiseReponse1'];
    $mauvaiseReponse2 = $enigme['mauvaiseReponse2'];
    $mauvaiseReponse3 = $enigme['mauvaiseReponse3'];

    $tab = [$bonneReponse, $mauvaiseReponse1, $mauvaiseReponse2, $mauvaiseReponse3];

    shuffle($tab);

    $id = $enigme['Id'];

    echo "
              <br>
              <div class=\"enigmaBubble firstBubble\"><h3 class=\"enigmaText\">" . $question . "</h3></div>
              <br>
              <form method=\"GET\">
                  <input type=\"hidden\" name=\"idEnigme\" value=\"" . $id . "\"/>
                  <input type=\"submit\" class=\"enigmaBubble enigmaText hoverEffect\" name=\"Reponse\" value=\"" . $tab[0] . "\" />
                  <br>
                  <input type=\"submit\" class=\"enigmaBubble enigmaText hoverEffect\" name=\"Reponse\" value=\"" . $tab[1] . "\" />
                  <br>
                  <input type=\"submit\" class=\"enigmaBubble enigmaText hoverEffect\" name=\"Reponse\" value=\"" . $tab[2] . "\" />
                  <br>
                  <input type=\"submit\" class=\"enigmaBubble enigmaText hoverEffect\" name=\"Reponse\" value=\"" . $tab[3] . "\" />
              <form>
              <br>";
  }
  ?>
</body>

</html>