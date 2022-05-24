<?php
    session_start();
    require_once './ConnexionBdd.class.php';
    require_once './verification.class.php';

    // print_r($_POST);

    if(isset($_POST['mat_etu']) && !empty($_POST['mat_etu']) && isset($_POST['annee_acad']) && !empty($_POST['annee_acad'])){
        // selection de l etudiant dans la base de donnees
        $sql = "SELECT
                    etudiants_inscrits.matricule,
                    etudiants_inscrits.noms,
                    payement.id_payement,
                    payement.date_payement,
                    payement.montant,
                    payement.num_bordereau,
                    prevision_frais.type_frais,
                    sections.section,
                    departement.departement,
                    options.option_ as fac,
                    options.promotion,
                    annee_acad.annee_acad
                FROM
                    payement
                RIGHT JOIN etudiants_inscrits ON payement.matricule = etudiants_inscrits.matricule
                LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                LEFT JOIN sections ON payement.id_section = sections.id_section
                LEFT JOIN departement ON payement.id_departement = departement.id_departement
                LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                LEFT JOIN options ON payement.id_option = options.id_option
                WHERE
                    payement.matricule = ? AND payement.id_annee = ?";
        $sel = ConnexionBdd::Connecter()->prepare($sql);
        $sel->execute(array($_POST['mat_etu'], $_POST['annee_acad']));

        if($sel->rowCount() > 0){
            echo '
                <table class="table table-bordered table-hover">
                    <thead class="bg-gra-200">
                        <tr>
                            <th>Type frais</th>
                            <th>montant</th>
                            <th>N<sup>0</sup> Bordereau</th>
                            <th>date</th>
                        </tr>
                    </thead>
                    <tbody style="font-size:90%">';
                        while($dd = $sel->fetch()){
                            $noms = $dd['noms'];
                            echo 
                            '<tr>
                                <td>'.utf8_decode($dd['type_frais']).'</td>
                                <td>$'.$dd['montant'].'</td>
                                <td>'.$dd['num_bordereau'].'</td>
                                <td>'.date("d/m/Y", strtotime($dd['date_payement'])).'</td>
                            </tr>';
                        }
                    echo '
                    </tbody>
                    <p class="text-primary"> historique de payement de '.$_POST['mat_etu'].'::'.$noms.'</p>
                </table>';
        }else{
            echo 'l etudiant(e) "'.$_POST['mat_etu'].'"n\'est pas inscrit(e)';
        }
    }else{
        echo 'Veuillez completer tous les chams puis réesayer';
    }
?>