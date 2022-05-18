<?php
    require_once './ConnexionBdd.class.php';
    // print_r($_GET);
    if(isset($_GET['a']) && !empty($_GET['a'])){
        if(isset($_GET['f']) && !empty($_GET['f'])){
            $sel_fac = ConnexionBdd::Connecter()->prepare("SELECT DISTINCT type_frais FROM affect_fac_frais WHERE faculte = ? AND annee_acad = ?");
            $sel_fac->execute(array($_GET['f'], $_GET['a']));
            while($d = $sel_fac->fetch()){
                echo '
                    <option value="'.$d['type_frais'].'">'.$d['type_frais'].'</option>';
            } 
        }
    }
    
?>