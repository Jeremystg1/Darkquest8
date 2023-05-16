<?php 
    include 'phps/db.php';
    require_once 'phps/constructeur.php';
    desactiverForceLogin();
    activerSessionSurPage();
    if(!isset($_GET["id"])){
        header("Location: " . getAppRoot());
    }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=MedievalSharp" />
    <title><?php  echo obtenirNomApplication(); ?></title>
    <?php 
        echo obtenirApplicationHead();
        function GetPourcent($note){
            $total = 0;
            $nb = 0;
            $sql = "select * from evaluations e 
                    inner join inventaire i on i.id = e.inventaire_Id 
                    inner join joueurs j on j.id = i.joueurs_id 
                    where i.items_id =".$_GET["id"];
            $table = executerSelectTable($sql);
            while($rows = $table->fetch()){
                $total +=1;
                if($rows['review'] == $note){
                    $nb += 1;
                }
            }
            if($total === 0)
                return 0;
            return round((($nb * 100) / $total),2);
        }

        function IPostAComment(){
            $sql = "select commentaire,alias,j.id from evaluations e 
                    inner join inventaire i on i.id = e.inventaire_Id 
                    inner join joueurs j on j.id = i.joueurs_id 
                    where i.items_id =".$_GET["id"];
            $table = executerSelectTable($sql);
            while($rows = $table->fetch()){
                if($rows["id"] === $_SESSION["userid"])
                    return true;
            }
            return false;

        }
    ?>
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
            grid-template-columns: 10% 20% 5% 55% 10%;
            grid-template-rows: auto;
        }
        table.Commentaire{
            grid-column: 4/5;
            color:#C8C8C8;
            background-color: #444444;
            border: solid black 2px;
            width: 100%;
            height: fit-content;
            border-collapse: collapse;
        }
        table.Commentaire th{
            background-color:#222222;
            border: solid black 2px;
            width: 100%;
            height: 50px;
        }
        table.Commentaire td{
            font-size: 16px;
            border: solid black 2px;
            width: 100%;
            text-align: center;
            height: 40px;
        }

        .Note{
            grid-column: 2/3;
            color:#C8C8C8;
            background-color: #444444;
            border: solid black 2px;
            width: 100%;
            border-collapse: collapse;
        }
        .Note th{
            background-color:#222222;
            border: solid black 2px;
            height: 10%;
        }
        .Note td.titre{
            font-size: 20px;
            border: solid black 2px;
            width: 20%;
            text-align: center;
        }
        .Note td{
            font-size: 16px;
            border: solid black 2px;
            width: 80%;
            text-align: center;
            height: 15%;
        }
        .pcom{
            
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
          $typeitem = "";
          $afficheType = "inconnu";
          while($rows = $table->fetch()){
            echo '<h1 id="alignCenter" style="color:rgb(121, 65, 225);">' . $rows['nom'] . '</h1>
                    <div id="alignCenter"><img class=imgClass src="'.$rows['imageUrl'].'" /></div>';

                    $typeitem = $rows['type'];
                    if($typeitem == 'ar'){
                        $afficheType = "Arme";
                    }
                    elseif($typeitem == 'am'){
                        $afficheType = "Armure";
                    }
                    elseif($typeitem == 'po'){
                        $afficheType = "Potion";
                    }
                    elseif($typeitem == 'so'){
                        $afficheType = "Sort";
                    }
                    //NB ETOILES MOYENNE
                echo  '<div id="align"><h2 class="itemTitle">Prix : </h2><span class="desc spanClass">' . $rows['prix'] . ' pièces d\'or</span></div>
                       <div id="align"><h2 class="itemTitle">Quantité : </h2><span class="desc spanClass">' . $rows['qtystock'] . '</span></div>
                       <div id="align"><h2 class="itemTitle">Type : </h2><span class="desc spanClass">' . $afficheType . '</psan></div>';
          }
          //Tables : armes armures potions sorts 
          if($typeitem == 'ar') //Arme
          {
                $sql = "select * from armes where items_id =".$_GET["id"];
                $table = executerSelectTable($sql);
                while($rows = $table->fetch()){
                    echo '<div id="align"><h2 class="itemTitle">Efficacité : </h2><span class="desc spanClass">' . $rows['efficacite'] . '</span></div>
                          <div id="align"><h2 class="itemTitle">Genre : </h2><span class="desc spanClass">' . $rows['genre'] . '</span></div>
                          <div id="align"><h2 class="itemTitle">Description : </h2><span class="desc spanClass">' . $rows['description'] . '</span></div>';
                }
          }
          elseif($typeitem == 'am') //Armure
          {
            $sql = "select * from armures where items_id =".$_GET["id"];
            $table = executerSelectTable($sql);
            while($rows = $table->fetch()){
                echo '<div id="align"><h2 class="itemTitle">Matière : </h2><span class="desc spanClass">' . $rows['matiere'] . '</span></div>
                      <div id="align"><h2 class="itemTitle">Emplacement : </h2><span class="desc spanClass">' . $rows['emplacement'] . '</span></div>';
            }
          }
          elseif($typeitem == 'po') //Potion
          {
            $sql = "select * from potions where items_id =".$_GET["id"];
            $table = executerSelectTable($sql);
            while($rows = $table->fetch()){
                echo '<div id="align"><h2 class="itemTitle">Effet : </h2><span class="desc spanClass">' . $rows['effet'] . '</span></div>
                      <div id="align"><h2 class="itemTitle">Durée : </h2><span class="desc spanClass">' . $rows['duree'] . '</span></div>';
            }
          }
          elseif($typeitem == 'so') //Sort
          {
            $sql = "select * from sorts where items_id =".$_GET["id"];
            $table = executerSelectTable($sql);
            while($rows = $table->fetch()){
                echo '<div id="align"><h2 class="itemTitle">Délai : </h2><span class="desc spanClass">' . $rows['delai'] . 'sec</span></div>
                      <div id="align"><h2 class="itemTitle">Dommages : </h2><span class="desc spanClass">' . $rows['dommages'] . 'pts</span></div>';
            }
          }
          //Affiche les boutons
          echo "<div id=\"alignCenter\">
                    <form>
                        <a href='".getAppRoot()."/panier.php?add=" . $_GET['id'] . "'><input class=\"styleBoutton\" type=\"button\" value=\"AJOUTER AU PANIER\"/></a>
                        <input class=\"styleBoutton\" type=\"button\" value=\"AJOUTER (admin)\"/>
                    </form>
                </div>";
        ?>
    </div>
    
    <div class="commentaireNoteDiv">
        <!-- liste commentaire plus affichage des notes -->
        <!-- Source Graphique note : https://canvasjs.com/php-charts/bar-chart/ -->
       <?php 
          $data = array(array("y" => GetPourcent(1),"label" => "1" ),
          array("y" => GetPourcent(2),"label" => "2" ),
          array("y" => GetPourcent(3),"label" => "3" ),
          array("y" => GetPourcent(4),"label" => "4" ),
          array("y" => GetPourcent(5),"label" => "5" ));
                        ?>
        <script>
            window.onload = function() {
                CanvasJS.addColorSet("customColorSet1",
                [//colorSet Array
                "#3F2345",
                "#843B7A",
                "#8A5D91",
                "#B394B6",
                "#ECC6F8"
                ]);
            var chart = new CanvasJS.Chart("Note", {
                animationEnabled: true,
                colorSet:  "customColorSet1",
                theme: "dark2",
                color:"white",
                title:{
                    text: "Note"
                },
                axisX: {
                    gridColor:"#444444",
                    lineColor:"#444444",
                    suffix:"⭐",
                },
                axisY: {
                    gridColor:"#444444",
                    lineColor:"#444444",
                    title: "Pourcentage",
                    includeZero: true,
                    maximum:100,
                    interval:20,
                    suffix:  "%",
                },
                data: [{
                    type: "bar",
                    indexLabel: "{y}%",
                    indexLabelPlacement: "inside",
                    indexLabelFontWeight: "bolder",
                    indexLabelFontColor: "white",
                    dataPoints: <?php echo json_encode($data, JSON_NUMERIC_CHECK); ?>
                }]
            });
            chart.render();
            }
        </script>
        <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
        <div id="Note" class="Note" style="height: 370px; width: 100%;"></div>
          <?php 
            $admin = false;
            //Affichage Commentaire
            //regarde si le joueur est connecter
            if(isset($_SESSION["userid"])){
                //s'il est admin il doit avoir les boutons admin dafficher en plus
                if(obtenirUserData($_SESSION["userid"])['etatcompte'] == 2){
                    $admin = true;
                    echo '<table class="Commentaire">
                    <tr><th>Commentaire</th><th>Publicateur</th><th>Note</th><th></th></tr>';
                }else if(IPostAComment()){
                    $admin = false;
                    echo '<table class="Commentaire">
                    <tr><th>Commentaire</th><th>Publicateur</th><th>Note</th><th></th></tr>';
                }
                else{
                    $admin = false;
                    echo '<table class="Commentaire">
                    <tr><th>Commentaire</th><th>Publicateur</th><th>Note</th></tr>';
                }
            }else{
                $admin = false;
                echo '<table class="Commentaire">
                <tr><th>Commentaire</th><th>Publicateur</th><th>Note</th></tr>';
            }


            $sql = "select e.Id,commentaire,alias,review,j.id from evaluations e 
                    inner join inventaire i on i.id = e.inventaire_Id 
                    inner join joueurs j on j.id = i.joueurs_id 
                    where i.items_id =".$_GET["id"];
            $table = executerSelectTable($sql);
            while($rows = $table->fetch()){
                if(isset($_SESSION["userid"])){
                    //verifie sur le user est un admin ou si c<Est lui qui a publier le commentaire
                    if($admin || $_SESSION["userid"] == $rows["id"]){
                        echo "<tr>
                                    <td><p class='pcom'>".$rows['commentaire']."</p></td>
                                    <td>".$rows['alias']."</td>
                                    <td>".$rows['review']."⭐</td>
                                    <td><form>
                                    <a href='".getAppRoot()."/phps/evaluationsManager.php?SupComm=" . $rows['Id'] . "&IdItem=".$_GET["id"] . "'><input class=\"styleBoutton\" type=\"button\" value=\"SUPPRIMER\"/></a>
                                    </form></td>
                                <tr>";
                    }else{
                        echo '<tr><td><p class="pcom">'.$rows["commentaire"].'</p></td><td>'.$rows["alias"].'</td><td>'.$rows['review'].'⭐</td><td></td><tr>';
                    }
                }else{
                    echo '<tr><td><p class="pcom">'.$rows["commentaire"].'</p></td><td>'.$rows["alias"].'</td><td> '.$rows['review'].'⭐</td><tr>';
                }
            }
            echo '</table>';
          ?>
    </div>
</body>
</html>