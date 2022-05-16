<?php
    if((isset($_SESSION['matricule']) && !empty($_SESSION['matricule'])) && (isset($_SESSION['identifiant']) && !empty($_SESSION['identifiant']))){
        if(md5($_SESSION['matricule']) == $_SESSION['identifiant']){
            $req = ConnexionBdd::Connecter()->prepare("SELECT * FROM etudiants_inscrits WHERE matricule  = ? AND noms = ? ");
            $req->execute(array($_SESSION['matricule'], $_SESSION['noms']));

            $n = $req->rowCount();

            if($n > 0){
                $data = $req->fetch();
                $_SESSION['fac_etud'] = $data['fac'];
                $_SESSION['promotion'] = $data['promotion'];
                $_SESSION['annee_academique'] = $data['annee_academique'];
            }else{
                session_destroy();
                header('location:../index.php');
            }
        }else{
            session_destroy();
            header('location:../index.php');
            exit();
        }
    }else{
        $_SESSION = [];
        session_destroy();
        header('location:../index.php');
        exit();
    }
?>