<?php

require_once 'db.php';

function getAllItems(){
    $f = executerSelectTable("SELECT Id FROM Items");
    $tabresultat = [];
    foreach($f as $key => $value){
        $tabresultat[] = $value["Id"];
    }
    return $tabresultat;
}

?>