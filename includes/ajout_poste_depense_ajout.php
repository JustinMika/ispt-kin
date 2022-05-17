<?php
    session_start();
    require_once './ConnexionBdd.class.php';
    require_once './verification.class.php';
    require_once './log_user.class.php';

    if(!empty($_POST['p_depense']) && !empty($_POST['annee_acad']) && !empty($_POST['a_montant'])){
        // verification si les donnees n existe pas encore dans la base de donnees
        $v = ConnexionBdd::Connecter()->prepare("SELECT * FROM poste_depense WHERE poste = ? AND annee_acad = ?");
        $v->execute(array(formater($_POST['p_depense']), formater($_POST['annee_acad'])));

        if($v->rowCount() <= 0){
            $insert = ConnexionBdd::Connecter()->prepare("INSERT INTO poste_depense(poste,montant,annee_acad) VALUES(?,?,?)");
            $ok = $insert->execute(array($_POST['p_depense'], formater($_POST['a_montant']), formater($_POST['annee_acad'])));
            if($ok){
                echo "Donnees inserer avec succes";
            }else{
                die("une erreur est durvenue : les donnees ne sont pas inserer dans la base de données.");
                header('500 Erreur interne du serveur', true, 500);
            }
        }else{
            echo $_POST['p_depense'].' existe déjà dans la base de données';
        }
    }else{
        die("une erreur est survenue");
        header('500 Erreur interne du serveur', true, 500);
    }

    function formater($var){
        return htmlentities(htmlspecialchars($var));
    }
?>