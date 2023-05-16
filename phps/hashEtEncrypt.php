<?php

function obtenirNewPassHash($pass){
    return password_hash($pass,PASSWORD_BCRYPT,["cost" => 12]);
}
function verifierPassword($passInput,$pwdhash){
    return password_verify($passInput,$pwdhash);
}

?>