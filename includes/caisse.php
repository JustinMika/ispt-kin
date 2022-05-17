<?php
    session_start();
    require_once './ConnexionBdd.class.php';

    // function m_format($v){
    //     if(empty($v)){
    //         return '$ 0';
    //     }else{
    //         return '$ '.$v;
    //     }
    // }

    function v($v){
        if(empty($v)){
            return '0';
        }else{
            return $v;
        }
    }

    // liste tous les payements
    if(isset($_POST['all']) && !empty($_POST['all'])){
        try {
            // on recupere le dernier annee academique
            $a = ConnexionBdd::Connecter()->query("SELECT DISTINCT annee_acad FROM annee_academique ORDER BY id DESC LIMIT 1");
            $data = $a->fetch();

            $req = ConnexionBdd::Connecter()->prepare("SELECT * FROM etudiants_inscrits WHERE annee_academique = ?");
            if(!empty($data['annee_acad'])){
                $req->execute(array($data['annee_acad']));
            }
            // selection dans la base de donnees
            // etudiants_inscrits  payement  affectation_frais  , noms, fac promotion

            $sql_sel = "SELECT matricule, noms, fac, promotion FROM etudiants_inscrits";
            $list_etud = ConnexionBdd::Connecter()->query($sql_sel);
            while($data_1 = $list_etud->fetch()){
                // selection du montant total affecter a l'etudiant
                $sql_tot_m = "SELECT SUM(montant) AS mt FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ?";
                $tot_m = connexionBdd::Connecter()->prepare($sql_tot_m);
                $tot_m->execute(array($data_1['matricule'], $data_1['promotion'], $data_1['fac']));

                while($data_2 = $tot_m->fetch()){
                    // selection du montant deja payer par l'etudiant en question
                    $sql_montant_p = "SELECT SUM(montant) as montant_t FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ?";
                    $mont_p = ConnexionBdd::Connecter()->prepare($sql_montant_p);
                    $mont_p->execute(array($data_1['matricule'], $data_1['fac'], $data_1['promotion']));
                    while($data_3 = $mont_p->fetch()){
                        echo '
                            <tr>
                                <td>'.$data_1['matricule'].'</td>
                                <td>'.$data_1['noms'].'</td>
                                <td>'.$data_1['fac'].'</td>
                                <td>'.$data_1['promotion'].'</td>
                                <td>'.m_format($data_2['mt']).'</td>
                                <td>'.m_format($data_3['montant_t']).'</td>
                                <td>$'.floatval(v($data_2['mt']) - v($data_3['montant_t'])).'</td>
                            </tr>';
                    }
                }
            }
            
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    // recherche de l etudiants par promotion , faculte, promotion
    if(isset($_POST['insert']) && !empty($_POST['insert'])){
        if(!empty($_POST['mat']) && !empty($_POST['fac']) && !empty($_POST['promotion']) && !empty($_POST['poste']) && !empty($_POST['annee_acad'])){
            if($_POST['poste'] == "All"){
                // $sql = "SELECT * FROM `payement` WHERE `matricule`=? AND `faculte`=? AND `promotion`=? AND `annee_acad`=?";
                $sql = "SELECT * FROM `payement` WHERE `matricule`= ? AND `faculte`= ?";
                // $params = array($_POST['mat'], $_POST['fac'], $_POST['promotion'], $_POST['annee_acad']);
                $params = array($_POST['mat'], $_POST['fac']);
                // 
                $_SESSION['p_etud'] = $sql;
                $_SESSION['p_etud_t'] = $params;
                // 
                $req = ConnexionBdd::Connecter()->prepare($sql);
                $req->execute($params);
                $n = $req->rowCount();

                if($n >= 1){
                    echo '
                        <thead class="bg-gray-200">
                            <p class="text-center">Resultat de votre recherche</p>
                            <tr>
                                <th>#ID</th>
                                <th>Matricule</th>
                                <th>Faculte</th>
                                <th>Promotion</th>
                                <th>Type frais</th>
                                <th>Montant</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                    ';
                    while($data = $req->fetch()){
                        echo '
                            <tr>
                                <td>'.$data['id'].'</td>
                                <td>'.$data['matricule'].'</td>
                                <td>'.$data['faculte'].'</td>
                                <td>'.$data['promotion'].'</td>
                                <td>'.$data['type_frais'].'</td>
                                <td>$'.v($data['montant']).'</td>
                                <td>'.$data['date_payement'].'</td>
                            </tr>';
                    } 
                    echo '</tbody>';
                }else{
                    echo '<p class="h3 text-danger">Aucun resultat trouver pour votre recherche.</p>';
                }
            }else{
                // $sql = "SELECT * FROM `payement` WHERE `matricule`= ? AND `faculte` = ? AND `promotion` = ? AND `annee_acad` = ? AND `type_frais` = ?";
                $sql = "SELECT * FROM `payement` WHERE matricule  = ? AND `faculte` = ? AND `type_frais` = ?";
                // $params = array($_POST['mat'], $_POST['fac'], $_POST['promotion'], $_POST['annee_acad'], $_POST['poste']);
                $params = array($_POST['mat'], $_POST['fac'], $_POST['poste']);
                // 
                $_SESSION['p_etud'] = $sql;
                $_SESSION['p_etud_t'] = $params;
                // 
                $req = ConnexionBdd::Connecter()->prepare($sql);
                $req->execute($params);

                $n = $req->rowCount();

                if($n >= 1){
                    echo '
                        <thead class="bg-gray-200">
                            <tr>
                                <th>#ID</th>
                                <th>Matricule</th>
                                <th>Faculte</th>
                                <th>Promotion</th>
                                <th>Type frais</th>
                                <th>Montant</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                    ';
                    while($data = $req->fetch()){
                        echo '
                            <tr>
                                <td>'.$data['id'].'</td>
                                <td>'.$data['matricule'].'</td>
                                <td>'.$data['faculte'].'</td>
                                <td>'.$data['promotion'].'</td>
                                <td>'.$data['type_frais'].'</td>
                                <td>$'.v($data['montant']).'</td>
                                <td>'.$data['date_payement'].'</td>
                            </tr>';
                    } 
                    echo '</tbody>';
                }else{
                    echo '<p class="h3 text-danger">Aucun resultat trouver pour votre recherche.</p>';
                }
            }
        }
    }

    // recher par faculte , promotion et par poste
    if(isset($_POST['data']) && !empty($_POST['data'])){
        if(!empty($_POST['fac']) && !empty($_POST['promotion']) && !empty($_POST['poste_']) && !empty($_POST['annee_acad'])){
            if($_POST['poste_'] == "All"){
                // "SELECT *, SUM(montant) as m FROM payement WHERE faculte='Droit' AND promotion='G3' AND annee_acad='2021-2022' GROUP BY type_frais"
                $sql = "SELECT *, SUM(montant) as m FROM payement WHERE faculte  = ? AND annee_acad = ?";
                $params = array($_POST['fac'], $_POST['annee_acad']);
                $_SESSION['p_etud_fac'] = $sql;
                $_SESSION['p_etud_t_fac'] = $params;

                // requettes
                $req_search = ConnexionBdd::Connecter()->prepare($sql);
                $req_search->execute($params);

                $n = $req_search->rowCount();
                // die("Erreur : ". $n);

                if($n >= 1){
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
                    while($data = $req_search->fetch()){
                        $s = "SELECT *, SUM(montant) AS mm FROM prevision_frais WHERE type_frais = ? AND annee_acad  = ? AND faculte  = ? AND promotion = ?";
                        $s_params = array($data['type_frais'], $data['annee_acad'], $data['faculte'], $data['promotion']);
                        $r = ConnexionBdd::Connecter()->prepare($s);
                        $r->execute($s_params);
                        while($data2 = $r->fetch()){
                            echo '
                                <tr>
                                    <td>'.$data['type_frais'].'</td>
                                    <td>'.$data['faculte'].'</td>
                                    <td>'.$data['promotion'].'</td>
                                    <td>'.$data['type_frais'].'</td>
                                    <td>$'.v($data['m']).'</td>
                                    <td>'.$data['date_payement'].'</td>
                                </tr>';
                        }
                    } 
                    echo '</tbody>';
                }else{
                    echo '<caption class="text-left h3 text-danger">pas des donnees pour l\'instant ...</caption>';
                }
            }else{
                $sql = "SELECT * FROM `payement` WHERE `faculte`= ? AND promotion = ? AND type_frais = ?";
                $params = array($_POST['fac'], $_POST['poste_']);

                $_SESSION['p_etud'] = $sql;
                $_SESSION['p_etud_t'] = $params;
            }
        }else{
            echo 'certains champs sont vide';
        }
    }
?>