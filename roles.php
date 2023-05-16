<?php
 
?>
<!DOCTYPE html>
<html lang="fr">
<style>
  body {
    background-color: purple;
  }
  @media (orientation: landscape) {
  .table {
      border-spacing: 0px;
      margin-left: auto;
      margin-right: auto;
      /* float: left; */
      margin-right: auto;
      /* height: 330px; */
      white-space: break-spaces;
      display: flex;
      grid-gap: 20px;
      justify-content: center;
      align-content: center;
  }
}
    #background {
      max-width: 100%;
      min-width: 100%;
      background-repeat: no-repeat;
      position: absolute;
      top: 0px;
      left: 0px;
      height: 100%;
    }
    #background_roles:hover{
      border: 10px gold outset;
      box-shadow:
      inset 0 0 50px red,      /* inner white */
      inset 20px 0 80px red,   /* inner left magenta short */
      inset -20px 0 80px black,  /* inner right cyan short */
      inset 20px 0 300px gray,  /* inner left magenta broad */
      inset -20px 0 300px red, /* inner right cyan broad */
      0 0 50px red,            /* outer white */
      -10px 0 80px red,        /* outer left magenta */
      10px 0 80px purple;         /* outer right cyan */
    }
        #background_roles {
        border: 10px gray inset;
        margin: 5%;
        max-width: 500px;
        max-height: 500px;
        z-index: 10;
        float: left;
    }
    @media (orientation: portrait){
    .table {
        border-spacing: 0px;
        margin-left: auto;
        margin-right: auto;
        white-space: revert;
        width: min-content;
        display: grid;
        grid-gap: 20px;
    }
}
    h3 {
      text-shadow: 5px 5px 10px blue;
      color: gold;
      text-align: center;
      width: 100%;
      font-size: 50px;
      text-overflow: ellipsis;
    }
    h4{
      text-align: center;
      width: 100%;
      font-size: 20px;
      text-overflow: ellipsis;
    }
    a {
      text-shadow: 5px 5px 10px blue;
      color: gold;
    }
    .Archer {
      background-image:url("../Images/Archer.jpg");
    }
    .Mage {
      background-image:url("../Images/Mage.jpg");
    }
    .Chevalier {
      background-image:url("../Images/Knight.jpg");
    }
    input {
      font-size: 30px;
      color:white;
      border-radius: 40%;
    }
  </style>
<head>
  <meta charset="UTF-8">
  <link rel="shortcut icon" href="#">
</head>
<body>
<div>
  <div id="background">
  </div>
  <div style="position:absolute; width: 100%;">
    <h3>Choisissez votre rôle pour créer votre compte</h3>
    <form action="../inscription.php" method="GET" class="table">
          <input type="button" id="background_roles" class="Archer" name="roleChoisi" value="A">
          <input type="button" id="background_roles" class="Mage" name="roleChoisi" value="M">
          <input type="button" id="background_roles" class="Chevalier" name="roleChoisi" value="C">
    </form>
      <h4><a href="../connexion.php">Vous avez déjà un compte?</a></h4>
  </div>
  </div>
</body>
</html>