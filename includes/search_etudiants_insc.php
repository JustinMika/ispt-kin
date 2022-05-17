<?php
    session_start();
    require_once './ConnexionBdd.class.php';
    require_once './verification.class.php';
    require_once './log_user.class.php';

    if(isset($_POST['mat_etu']) && !empty($_POST['mat_etu']) && isset($_POST['annee_acad']) && !empty($_POST['annee_acad'])){

        // selection de l etudiant dans la base de donnees
        $sel = ConnexionBdd::Connecter()->prepare("SELECT *  FROM etudiants_inscrits WHERE matricule = ? AND annee_academique = ?");
        $sel->execute(array($_POST['mat_etu'], $_POST['annee_acad']));

        if($sel->rowCount() > 0){
            echo '
            <table class="table table-bordered table-hover">
                <p class="text-primary">Informations de l étudiant(e)</p>
                <tbody style="font-size:90%">';
                    while($r = $sel->fetch()){
                        echo 
                        '<tr>
                            <td>matricule</td>
                            <td>'.$r['matricule'].'</td>
                        </tr>
                        <tr>
                            <td>Noms</td>
                            <td>'.$r['noms'].'</td>
                        </tr>
                        <tr>
                            <td>faculte</td>
                            <td>'.$r['fac'].'</td>
                        </tr>
                        <tr>
                            <td>Promotion</td>
                            <td>'.$r['promotion'].'</td>
                        </tr>
                        <tr>
                            <td>Annee Acad. </td>
                            <td>'.$r['annee_academique'].'</td>
                        </tr>
                        ';
                    }
                echo '
                </tbody>
            </table>';
        }else{
            echo 'l etudiant(e) "'.$_POST['mat_etu'].'"n est pas inscrit dans l annee academique "'.$_POST['annee_acad'].'"';
        }
    }else{
        echo 'Veuillez completer tous les chams puis reesayer';
    }
?>