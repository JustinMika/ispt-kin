<?php
    session_start();
    require_once './ConnexionBdd.class.php';
    

    if(isset($_POST['search_all']) && !empty($_POST['search_all'])){
        if(!empty($_POST['fac_']) && !empty($_POST['promotion_']) &&  !empty($_POST['poste_']) &&  !empty($_POST['annee_acad_']) &&  !empty($_POST['montant_search']) &&  !empty($_POST['date_2_']) &&  !empty($_POST['date_1_'])){
            if($_POST['poste_'] == "All"){
                // selection des donnees
                $frais_fac = ConnexionBdd::Connecter()->query("SELECT type_frais, faculte, SUM(montant) AS montant FROM payement WHERE  GROUP BY faculte");

                $frais_prev = ConnexionBdd::Connecter()->query("SELECT type_frais, faculte, SUM(montant) AS montants FROM prevision_frais GROUP BY faculte");

                // entete du tableau
                echo '
                    <thead class="bg-gray-200">
                        <p class="text-center">Resultat de votre recherche</p>
                        <tr>
                            <th>Type frais</th>
                            <th>Faculte</th>
                            <th>Promotion</th>
                            <th>Montant prevu</th>
                            <th>Montant payer</th>
                            <th>Reste</th>
                            <th> % </th>
                        </tr>
                    </thead>
                    <tbody>
                ';

                while($data_ = $frais_prev->fetch()){
                    while($data = $frais_fac->fetch()){
                        echo '
                            <tr>
                                <td>'.$data['type_frais'].'</td>
                                <td>$'.$data_['montants'].'</td>
                                <td>$'.$data['montant'].'</td>
                                <td>'.montant_restant_p($data['montant'], $data_['montants']).'%</td>
                                <td>'.montant_restant_pourcent(montant_restant_p($data['montant'], $data_['montants']), $data['montant']).'%</td>
                            </tr>
                        ';
                    }
                }
                echo '</tbody>';
            }else{
                // selection des donnees
                $sql = "SELECT ";
                $params = array();
                $select = ConnexionBdd::Connecter()->prepare($sql);
            }
        }else{
            // echo 'Certains champs sont vide, veuillez les remplir svp.';
        }
    }
?>