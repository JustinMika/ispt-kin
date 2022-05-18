<?php
    session_start();
    require_once './ConnexionBdd.class.php';
    require_once './log_user.class.php';
    require_once './verification.class.php';

    // ajout departement
    $an =  ConnexionBdd::Connecter()->query("SELECT * FROM annee_acad GROUP BY annee_acad ORDER BY id_annee DESC LIMIT 1");
    if($an->rowCount() > 0){
        $an_r = $an->fetch();
    }else{
        $an_r['id_annee'] = '';
        die("Veuillez AJouter l annee academique");
    }
    
    $section_id = $_POST['section_id'];
    $id_departement_ = $_POST['id_departement_'];
    $Option_Option = $_POST['Option_Option'];
    $Option_promotion = $_POST['Option_promotion'];
    $Option_code = $_POST['Option_code'];
    // print_r($_POST);
    if(isset($section_id) && isset($id_departement_) && isset($Option_Option) && isset($Option_promotion) && isset($Option_code)){
        $verif = ConnexionBdd::Connecter()->prepare("SELECT * FROM options WHERE option_ = ? AND promotion = ? AND code_ = ? AND id_departement = ? AND id_section = ? AND id_annee = ?");
        $verif->execute(array($Option_Option, $Option_promotion, $Option_code, $id_departement_, $section_id, $an_r['id_annee']));
        $n = $verif->rowCount();

        if($n < 1){
            try{
                $del  = ConnexionBdd::Connecter()->prepare("INSERT INTO options(option_, promotion, code_,id_departement, id_section, id_annee) VALUES(?, ?, ?, ?, ?, ?)");
                $ok = $del->execute(array($Option_Option, $Option_promotion, $Option_code, $id_departement_, $section_id, $an_r['id_annee']));
                if($ok){
                    echo "ok";
                    LogUser::addlog(VerificationUser::verif($_SESSION['data']['id_user']), "ajout d'une option.");
                }else{
                    echo("le departement n'est pas enregistré.");
                }
            } catch (PDOException $e) {
                die($e->getMessage());
            }
        }else{
            echo "L'option que vous voulez ajouter existe deja.";
        }
    }else{
        echo("Veuillez renseigner le departement a inserer dans la base de donnees.");
    }
?>