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

function GetNbDemande(){
    $rowsCount = executerSelectUneLigneObtenirTable("SELECT COUNT(id) as cid FROM demandes WHERE joueurs_id = :jid",[":jid" => $_SESSION["userid"]]);
    return $rowsCount["cid"];
                /*
    $sql = "select * from  joueurs where id ="  . $_SESSION["userid"];
    $table = executerSelectTable($sql);
    while ($rows = $table->fetch()) {
        return $rows["nbDemandeSolde"];
    }
    return null; */ return 0;
}

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST["submit_option"])) {
      
    if(empty($_COOKIE["nbDemande"])){
        setcookie("nbDemande",0);
    }

    if ($_POST['submit_option'] == "Convertir") {
        
        $soldeArgentAAjouter = GetSolde("argent") / 10;
        $resteSoldeArgent = GetSolde("argent") % 10;
        $soldeBronzeAAjouter = GetSolde("bronze") / 100;
        $resteSoldeBronze = GetSolde("bronze") % 100;
        $ajoutTotal = $soldeArgentAAjouter + $soldeBronzeAAjouter;
        $reussi = false;
        $typeSolde = "or";

        try {
            $reussi = executerDML("UPDATE joueurs SET solde_or = solde_or + :ajout, solde_argent = $resteSoldeArgent, solde_bronze = $resteSoldeBronze 
            WHERE id = :id", [
              ":ajout" => $ajoutTotal,
              ":id" => $id
            ]);
        } catch (Exception $e) {}

        if (!$reussi) {
            header('Location: solde.php?alert=Échec+de+la+convertion');
        }
        header('Location: solde.php?alert=Convertion+réussi&alertc=green');
    }
    if($_POST['submit_option'] == "Envoyer la demande" && isset($_POST["demandeTexte"])){
        $raison = $_POST["demandeTexte"];
        if($raison == ""){
            $raison = "raison vide";
        }
        
        
        //le joueur ne peux faire que 3 demande maximum
        $maxDemandes = 4;
        if(GetNbDemande() < $maxDemandes && $_COOKIE["nbDemande"] < $maxDemandes){
            $reussi = false;
            $joueurId = $id;
            try{  
                
                $tab = [
                    ":jid" => $joueurId,
                    ":msg" => $raison,
                    ":jai" => null
                ];
                $reussi = executerDML("INSERT INTO demandes(solde,joueurs_id,message,joueursAdmin_id) VALUES(0,:jid,:msg,:jai)",$tab);
                
                $reussi = true;
            } catch(Exception $e){$reussi = false;}
            if($reussi == true){
                if(isset($_COOKIE["nbDemande"])){
                    setcookie("nbDemande",$_COOKIE["nbDemande"] +1);
                }else{
                    setcookie("nbDemande",1);
                }
                echo "<script> alert('". json_encode($tab)."') </script>";
                header('Location: solde.php?alert=Demande+envoyé&alertc=green');
                exit();
            } else{
                header('Location: solde.php?alert=Demande+non+envoyé');
                exit();
            }
        }
        else{
            header('Location: solde.php?alert=Vous+ne+pouvez+pas+soumettre+plus+de+trois+demande!');
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

    <form method="POST" class="formeOuInscrip">
        <fieldset style="height: fit-content;">
            <legend>Tout convertir en or</legend>
            <input type="submit" name="submit_option" value="Convertir">
        </fieldset>
        <br><br>
        <fieldset style="height: fit-content;">
            <legend>Demande d'ajout de capitale</legend>
            <span>Faites vos demandes d'ajout facilement en remplissant ce formulaire</span>
            <br><br>
            <input type="text" name="demandeTexte" placeholder="Raison de la demande"><br><br>
            <input type="submit" name="submit_option" value="Envoyer la demande">
        </fieldset>
    </form>

</body>

</html>