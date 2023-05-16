<?php 
    include 'phps/db.php';
    require_once 'phps/constructeur.php';
    require_once 'phps/evaluationsManager.php';
    activerSessionSurPage();
    if(isset($_POST["ENVOYER"]))
    {
        if(!empty($_POST["commentaire"]))
        {
            try
            {
                //Ajouter le commentaire dans la BD
                addReview($_POST ['commentaire'],$_POST['rating'],$_POST['id']);
                //$sql = "INSERT INTO evaluations (commentaire,review,inventaire_Id) VALUES (\"".$_POST['commentaire']."\",".$_POST['rating'].",".obtenirUserInventoryId($_POST['id']).");";
                
                //$table = executerDML($sql,);
                
                //Mettre un alert que ça a fonctionné
                headerQ("Location: " . getAppRoot()."/Details.php?id=". $_POST['id']);
                //echo "<script> window.location.href = '" . getAppRoot()."/Details.php?id=".$_POST['id'],"&alert=Votre+évaluation+a+été+ajouté!' </script>";
                //echo "<script> window.location.href = '" . getAppRoot()."/Details.php?id=".$_POST['id']."</script>";

            }
            catch(Exception $e)
            {
                echo "<script> window.location.href = '" . getAppRoot()."/commentaireForm.php?id=".$_POST['id'],"&alert=Une+erreur+dans+la+base+de+données+est+survenu!' </script>";
            }
        }
        else
        {                                                     //./commentaireForm.php?id=' . $rows['id'], '
             echo "<script> window.location.href = '" . getAppRoot()."/commentaireForm.php?id=".$_POST['id'],"&alert=".$e->getMessage()." </script>";
        }
    }
    if(!isset($_GET["id"])){
        headerQ("Location: " . getAppRoot());
    }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/starability-slot.css">
    <title><?php  echo obtenirNomApplication(); ?></title>
    <?php echo obtenirApplicationHead(); ?>
    <style>
        .imgClass{
            width: 150px;
            height: 150px;
        }
        #items{
            background-color:#333333;
            outline:black 4px solid;
            color:white;
            margin:auto;
            width: 80%;
            box-shadow: rgba(0, 0, 0, 0.55) 0px 10px 50px, 
                        rgba(0, 0, 0, 0.52) 0px -12px 30px,
                        rgba(0, 0, 0, 0.52) 0px 4px 6px, 
                        rgba(0, 0, 0, 0.57) 0px 12px 13px, 
                        rgba(0, 0, 0, 0.59) 0px -3px 5px;
        }
        #align:nth-child(even) {
            background-color: #222222;
        }
        #align:nth-child(odd) {
            background-color: #444444;
        }
        table{
            margin-left: auto;
            margin-right:auto;
            width: max-content;
        }
        .itemsTable{
            display: grid;
            grid-template-columns: repeat(auto-fill, 300px
            );
        }
        button{
        width: 75px;
        height: 40px;
        }
        #scroll{
        overflow: auto;
        max-height: 60em;
        margin-left:auto;
        margin-right: auto;
        }
        #align{
            text-align: left;
            display: flex;
            height: fit-content;
        }
        #alignCenter{
            text-align: center;
        }
        .detailsButton{
            text-align: center;
        }
        .detailsText{
            color: rgb(0,200,0);
            font-size: 20px;
            font-weight: bold;
        }
        .styleBoutton{
            background-color: rgb(100, 100, 100);
            outline: black 4px solid;
            margin: 1em;
            color: white;
            font-weight: bold;
            height: 3em;
            width: 10%;
            min-width: fit-content;
        }
        .styleBoutton:hover{
            outline: gray 4px solid;
            outline: rgb(101, 55, 205) 4px solid;
            color:black;
            background-color: white;
        }
        .spanClass{
            color:#C8C8C8;
            font-weight: bold;
            font-size: 25px;
        }
        .desc{
            text-align: left;
            margin: auto;
            width: 80%;
        }
        .itemTitle{
            width:40%;
        }

        div.commentaireNoteDiv{
            display: grid;
            grid-template-columns: 10% 20% 60% 10%;
            grid-template-rows: auto;
        }
        table.Commentaire{
            grid-column: 3/4;
            background-color: #444444;
            border: solid black 2px;
            width: 100%;
            border-collapse: collapse;
        }
        table.Commentaire th{
            background-color:#333333;
            border: solid black 2px;
            width: 100%;
        }
        table.Commentaire td{
            color:#C8C8C8;
            font-size: 20px;
            border: solid black 2px;
            width: 100%;
        }
        
  </style>
</head>
<body>
    <?php faireHeader(); ?>
    <br>
    <br>
    <br>
    <br>
    <div id="items">
        <?php 

          $sql = "select * from items where id =".$_GET["id"];

          $table = executerSelectTable($sql);

          while($rows = $table->fetch()){
            
            //Lien pour étoiles => https://www.cssscript.com/accessible-star-rating-system-pure-css/
            //Section des commentaires
            echo "<h1 id=\"alignCenter\" style=\"color:rgb(121, 65, 225);\">" . $rows['nom'] . "</h1>
                    <div id=\"alignCenter\"><img class=imgClass src=" . $rows['imageUrl'] . " /></div>
                    <form action=". getAppRoot()."/commentaireForm.php?id=".$_GET['id']." method=\"POST\" id=\"alignCenter\">
                        <fieldset class=\"starability-slot\"> 
                            <legend>Évaluez</legend>
                            <input type=\"radio\" id=\"rate5\" name=\"rating\" value=\"1\" />
                            <label for=\"rate5\" title=\"Pas bon\"></label>

                            <input type=\"radio\" id=\"rate4\" name=\"rating\" value=\"2\" />
                            <label for=\"rate4\" title=\"Ça va\"></label>

                            <input type=\"radio\" id=\"rate3\" name=\"rating\" value=\"3\" />
                            <label for=\"rate3\" title=\"Bon\"></label>

                            <input type=\"radio\" id=\"rate2\" name=\"rating\" value=\"4\" />
                            <label for=\"rate2\" title=\"Très bon\"></label>

                            <input type=\"radio\" id=\"rate1\" name=\"rating\" checked value=\"5\" />
                            <label for=\"rate1\" title=\"Parfait\"></label>
                        </fieldset>

                        <label>Commentez ici : </label>
                        <br>
                        <TextArea name=\"commentaire\" maxlength=\"80\" style=\"font-size:2em ;width:80%; height: 7em;outline: 3px black solid; border-radius:1em;\"></TextArea>
                        <br>
                        <input type=\"hidden\" name=\"id\" value=". $rows['id'] ." />
                        <input class=\"styleBoutton\" type=\"submit\" name=\"ENVOYER\" value=\"ENVOYER\"/>
                    </form>";

          }
        ?>
    </div>
</body>
</html>