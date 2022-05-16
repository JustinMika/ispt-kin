<?php
    require_once './ConnexionBdd.class.php';
    // print_r($_GET);
    if(isset($_GET['a']) && !empty($_GET['a'])){
        if(isset($_GET['f']) && !empty($_GET['f'])){
            $sel_fac = ConnexionBdd::Connecter()->prepare("SELECT DISTINCT promotion FROM etudiants_inscrits WHERE fac = ? AND annee_academique = ?");
            $sel_fac->execute(array($_GET['f'], $_GET['a']));
            while($d = $sel_fac->fetch()){
                echo '
                    <option value="'.$d['promotion'].'">'.$d['promotion'].'</option>';
            } 
        }
    }
    
?>