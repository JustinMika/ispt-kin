<?php
    session_start();
    require_once './ConnexionBdd.class.php';
    require_once './verification.class.php';
    require_once './log_user.class.php';

    if(isset($_POST['mat_etu']) && !empty($_POST['mat_etu']) && isset($_POST['annee_acad']) && !empty($_POST['annee_acad'])){
        // selection de l etudiant dans la base de donnees
        $sel = ConnexionBdd::Connecter()->prepare("SELECT noms  FROM etudiants_inscrits WHERE matricule = ? AND annee_academique = ?");
        $sel->execute(array($_POST['mat_etu'], $_POST['annee_acad']));

        if($sel->rowCount() > 0){
            $dd = $sel->fetch();
            $det = ConnexionBdd::Connecter()->prepare("SELECT * FROM payement WHERE matricule = ? AND annee_acad = ? ORDER BY date_payement DESC");
            $det->execute(array($_POST['mat_etu'], $_POST['annee_acad']));

            if($det->rowCount() > 0){
                echo '
                <table class="table table-bordered table-hover">
                    <p class="text-primary"> historique de payement de "'.$dd['noms'].'"</p>
                    <thead class="bg-gra-200">
                        <tr>
                            <th>Type frais</th>
                            <th>montant</th>
                            <th>N<sup>0</sup> Bordereau</th>
                            <th>date</th>
                        </tr>
                    </thead>
                    <tbody style="font-size:90%">';
                        while($r = $det->fetch()){
                            echo 
                            '<tr>
                                <td>'.$r['type_frais'].'</td>
                                <td>$'.$r['montant'].'</td>
                                <td>'.$r['num_borderon'].'</td>
                                <td>'.$r['date_payement'].'</td>
                            </tr>';
                        }
                    echo '
                    </tbody>
                </table>';
            }else{
                echo 'Aucun payement de l etudiant(e)';
            }
        }else{
            echo 'l etudiant(e) "'.$_POST['mat_etu'].'"n est pas inscrit dans l ann??e academique "'.$_POST['annee_acad'].'" ...';
        }
    }else{
        echo 'Veuillez completer tous les champs puis reesayer';
    }
?>