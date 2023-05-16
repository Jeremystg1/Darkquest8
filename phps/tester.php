<?php
require_once "db.php";


$sql = "
             select enigmes.id 
             from enigmes 
             INNER JOIN historiqueEnigme 
             ON enigmes.id = historiqueEnigme.Id
             WHERE disponible = 1";

            $table = executerSelectTable($sql);

            while($rows = $table->fetch()){
              echo$rows["id"];
            }
            ?>
            <!DOCTYPE html>
<html lang="fr">
<meta name="viewport" content="width=device-width, initial-scale=1">
<head>
  <meta charset="UTF-8">
  <title>Enigma</title>
