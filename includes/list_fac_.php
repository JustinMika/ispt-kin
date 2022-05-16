<?php
    require_once './ConnexionBdd.class.php';
    $user = ConnexionBdd::Connecter()->query("SELECT * FROM faculte");
    $n = $user->rowCount();
    if($n > 0){
        while($data = $user->fetch()){
            echo '<option values="'.$data['faculte'].'">'.$data['faculte'].'</option>';
        }
    }else{
        header("Erreur interne du serveur", true, 500);
    }
?>