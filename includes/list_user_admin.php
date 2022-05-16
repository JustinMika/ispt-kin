<?php
    require_once './ConnexionBdd.class.php';
    $user = ConnexionBdd::Connecter()->query("SELECT * FROM utilisateurs GROUP BY fonction");
    $n = $user->rowCount();
    if($n > 0){
        while($data = $user->fetch()){
            echo '<option value="'.$data['fonction'].'">'.$data['fonction'].'</option>';
        }
    }else{
        header("Erreur interne du serveur", true, 500);
    }
?>