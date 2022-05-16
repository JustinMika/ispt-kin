<?php
    session_start();
    require_once './ConnexionBdd.class.php';
    require_once './log_user.class.php';
    require_once './verification.class.php';

    if(!empty($_POST['p_depense']) && !empty($_POST['annee_acad']) && !empty($_POST['a_montant']) && !empty($_POST['faculte']) && !empty($_POST['promotion'])){

        // verification si le poste n existe pas
        $v = ConnexionBdd::Connecter()->prepare("SELECT * FROM depense_facultaire WHERE poste = ? AND faculte = ? AND annee_acad = ?");
        $v->execute(array(strtolower($_POST['p_depense']), $_POST['faculte'], formater($_POST['annee_acad'])));
        $n = $v->rowCount();

        if($n <= 0){
            $insert = ConnexionBdd::Connecter()->prepare("INSERT INTO depense_facultaire(poste, montant, faculte, annee_acad) VALUES(?,?,?,?)");
            $ok = $insert->execute(array(strtolower($_POST['p_depense']), intval(formater($_POST['a_montant'])), $_POST['faculte'], formater($_POST['annee_acad'])));

            if($ok){
                echo "Donnees inserer avec succes";
                LogUser::addlog(VerificationUser::verif($_SESSION['data']['noms']), 'a ajouté(e) un nouveau poste de dépense facultaire.');
            } else {
                die("une erreur est durvenue : les donnees ne sont pas inserer dans la base e donnees.");
                header('500 Erreur interne du serveur', true, 500);
            }
        }else{
            echo "le poste {$_POST['p_depense']} existe déjà ...";
        }
    } else {
        die("une erreur est survenue");
        header('500 Erreur interne du serveur', true, 500);
    }

    function formater($var){
        return htmlentities(htmlspecialchars($var));
    }
?>