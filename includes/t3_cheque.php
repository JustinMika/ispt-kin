<?php
    session_start();
    require_once './ConnexionBdd.class.php';
    require_once './verification.class.php';
    require_once './log_user.class.php';

    if(!empty($_POST['date_cheque']) && !empty($_POST['libelle_cheque']) && !empty($_POST['n_cheque']) && !empty($_POST['montant_cheque'])){
        if(date("Y-m-d", strtotime($_POST['date_cheque'])) <= date("Y-m-d")){
            $t3 = ConnexionBdd::Connecter()->prepare("INSERT INTO hist_cheque(date_c, libelle, num_cheque, montant) VALUES(?,?,?,?)");
            $ok = $t3->execute(array(
                $_POST['date_cheque'],
                htmlspecialchars(trim($_POST['libelle_cheque'])), 
                htmlspecialchars(trim($_POST['n_cheque'])), 
                $_POST['montant_cheque']
            ));
            if($ok){
                echo 'success';
                LogUser::addlog(VerificationUser::verif($_SESSION['data']['noms']), 'a fait un chèque pour un montant de $'.$_POST['montant_cheque'].' le '.date("d/m/Y", strtotime($_POST['date_cheque'])));
            }else{
                echo 'Erreur : une erreur est survenue veuillez réesayer ...';
            }
        }else{
            echo 'la date doit être inferieur à la date d aujourdhui.';
        }
    }else{
        echo 'Veuillez completer tous les champs svp ...';
    }
?>