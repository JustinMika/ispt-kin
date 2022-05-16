<?php
    session_start();
    require_once './ConnexionBdd.class.php';
    require_once './log_user.class.php';
    require_once './verification.class.php';
    // ajouter l'annee academique dans la nase de donnees
    // $faculte = $_POST['fac'];
    if(isset($_POST['id_payement']) && !empty($_POST['id_payement'])){
        $id_payement =  htmlentities(htmlspecialchars($_POST['id_payement']));
        $del  = ConnexionBdd::Connecter()->prepare("DELETE FROM payement WHERE id = ?");
        $ok = $del->execute(array($id_payement));
        if($ok){
            echo "ok";
            LogUser::addlog(VerificationUser::verif($_SESSION['data']['noms']), "a supprimé(e) un payement");
        }else{
            echo("Erreur interne du serveur : les donnees ne sont pas enregister");
            // header("Erreur interne du serveur", true, 500);
        }
    }
?>