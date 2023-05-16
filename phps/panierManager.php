<?php
$nomcookie = "panierID1";
require_once 'db.php';
require_once "constructeur.php";
desactiverForceLogin();
//activerSessionSurPage();
if(empty($_COOKIE[$nomcookie])){
    écrireAuCookie([]);
    $_URRLLL = $_SERVER["REQUEST_URI"];
    header("Location: $_URRLLL");
}
function écrireAuCookie($d){
    global $nomcookie;
    if(is_string($d)){
        $d = json_decode($d);
    }
    foreach($d as $key => $val){
        if($key == "" || $val <= 0){
            unset($d[$key]);
        }
    }
    if(!is_string($d)){
        $d = json_encode($d);
    }
    unset($_COOKIE[$nomcookie]);
    setcookie($nomcookie,null,-1);
    setcookie($nomcookie,$d,time()+606024*30,"/");
}
function lireCookie(){
    global $nomcookie;
    return json_decode($_COOKIE[$nomcookie],true);
}
function ajouterItem($id,$qty,$fileHopperEnabled){
    if($fileHopperEnabled){
        echo "<script>window.location.href='" . getAppRoot() . "/phps/pro_panierManager.php?add=" . $id ."&qty=$qty'</script>";
        return;
    }
    $p = lireCookie();
    if(isset($p[$id])){
        $p[$id] = $p[$id]+$qty;
    } else{
        $p[$id] = $qty;
    }
    écrireAuCookie($p);
    return true;
}
function enleverItem($id,$qty,$fileHopperEnabled){
    if($fileHopperEnabled){
        echo "<script>window.location.href='" . getAppRoot() . "/phps/pro_panierManager.php?remove=" . $id ."&qty=$qty'</script>";
        return;
    }
    $p = lireCookie();
    if(isset($p[$id])){
        $p[$id] = 0;//$p[$id]+= -$qty; //reset à 0
    } else{
        $p[$id] = 0;
    }
    écrireAuCookie($p);
    return true;
}

function enleverToutItem($id,$qty,$fileHopperEnabled){
    if($fileHopperEnabled){
        echo "<script>window.location.href='" . getAppRoot() . "/phps/pro_panierManager.php?allBuy=" . $id ."&qty=$qty'</script>";
        return;
    }
    $d = [];
    $d = json_encode($d);
    écrireAuCookie($d);
    return true;
}

function setItem($id,$qty,$fileHopperEnabled){
    if($fileHopperEnabled){
        echo "<script>window.location.href='" . getAppRoot() . "/phps/pro_panierManager.php?set=" . $id ."&qty=$qty'</script>";
        return;
    }
    $p = lireCookie();
    $p[$id] = $qty;
    écrireAuCookie($p);
    return true;
}

function créerDivListItems($itemsList){
    foreach($itemsList as $k => $id){
        echo obtenirDivItemElement($id) . '<br>';
    }
}
function obtenirDivItemElement($id){
    $data = getItemData($id);
    $nom = $data["nom"];
    $imageUrl = $data["imageUrl"];
    $qtyStock = $data["qtystock"];
    $prix = $data["prix"];

    $s = "<div class='divItemElementGenere'><div>
        <img src='".$imageUrl."'/>
        
    </div></div>";
    return $s;
}
function NeedConversion($totalPrix,$fileHopperEnabled){
    /*
    $role = obtenirUserData($_SESSION['userid'])['role'];
    if($role != 'm' && )
    */
    if($fileHopperEnabled){
        echo "<script>window.location.href='" . getAppRoot() . "/phps/pro_panierManager.php?ConvertirSolde=1&totalPrix='$totalPrix'</script>";
        return;
    }
    if( GetSolde("or") < $totalPrix){
        //convertir les piece dargent pour voir si on peut acheter mtn
        conversion("argent");
        if(GetSolde("or") < $totalPrix){
            //convertir les piece de bronze en or
            conversion("bronze");
            if(GetSolde("or") < $totalPrix){
                //si tjs pas assez retourne null
                header('Location: panier.php?alert=Vous+n\'avez+pas+assez+d\'argent+pour+procéder+à+l\'achat!');
            }
        }
    }
    return true;
}
?>