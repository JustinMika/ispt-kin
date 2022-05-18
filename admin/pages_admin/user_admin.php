<?php
    session_start();
    require_once '../../includes/ConnexionBdd.class.php';

    $con = self::Connecter();

    // on recure la liste de tous les utilisateur disponibles dans la base de donnees
    $data_user = $con->query("SELECT * FROM utilisateurs");
    $nmbre = $data_user->rowcount();

    if($nmbre > 0){
        while($data = $data_user->fetch()){
            echo $data;
        }
    }else{
        header('Erreur interne du serveur', true, 500);
    }
?>