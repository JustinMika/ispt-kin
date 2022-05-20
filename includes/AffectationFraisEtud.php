<?php
    session_start();
    require_once './ConnexionBdd.class.php';
    if(isset($_GET['affich']) && !empty($_GET['affich'])){
        // on recupere le dernier annee academique
        $a = ConnexionBdd::Connecter()->query("SELECT id_annee FROM annee_acad ORDER BY id_annee DESC LIMIT 0,1");
        if($a->rowCount() > 0){
            $data = $a->fetch();
        }else{
            $data['id_annee'] = '';
        }

        $verif = ConnexionBdd::Connecter()->query("SELECT etudiants_inscrits.id, etudiants_inscrits.matricule, etudiants_inscrits.noms, sections.id_section, sections.section as fac, departement.id_departement, departement.departement, options.id_option, options.option_ as option, options.promotion, annee_acad.id_annee, annee_acad.annee_acad as annee_academique  
        FROM etudiants_inscrits 
        LEFT JOIN sections ON etudiants_inscrits.id_section = sections.id_section 
        LEFT JOIN departement ON etudiants_inscrits.id_departement = departement.id_departement 
        LEFT JOIN options ON etudiants_inscrits.id_option = options.id_option 
        LEFT JOIN annee_acad ON etudiants_inscrits.id_annee = annee_acad.id_annee WHERE etudiants_inscrits.id_annee = '{$data['id_annee']}'");
        while($data = $verif->fetch()){
            echo '
                <tr>
                    <td id="mat">'.$data['matricule'].'</td>
                    <td id="noms">'.utf8_decode($data['noms']).'</td>

                    <td >'.$data['fac'].'</td>
                    <td id="id_section" style="display:none">'.$data['id_section'].'</td>

                    <td>'.$data['departement'].'</td>
                    <td id="id_departement"  style="display:none">'.$data['id_departement'].'</td>

                    <td>'.$data['option'].'</td>
                    <td id="id_option" style="display:none">'.$data['id_option'].'</td>

                    <td id="promotion">'.$data['promotion'].'</td>

                    <td>'.$data['annee_academique'].'</td>
                    <td id="id_annee" style="display:none">'.$data['id_annee'].'</td>
                    <td>
                        <a class="btn btn-primary" href="#" id="btn_affecter" data-toggle="modal" data-target="#mod_affectation"><i class="fa fa-plus-circle" aria-hidden="true"></i> Affecter</a>
                        <a class="btn btn-danger" href="#" id="btn_del_affecter" data-toggle="modal" data-target="#del_affectation"><i class="fa fa-recycle" aria-hidden="true"></i></a>
                    </td>
                </tr>';
        }
    }
    // search
    if(isset($_POST['id_search']) && !empty($_POST['id_search'])){
        if(!empty($_POST['mat']) && !empty($_POST['fac']) && !empty($_POST['promotion']) && !empty($_POST['annee_acad'])){
            
            //selection de l etudiants
            $verif = ConnexionBdd::Connecter()->prepare("SELECT * FROM etudiants_inscrits WHERE matricule = ? AND fac = ? AND promotion = ? AND annee_academique = ?");
            $verif->execute(array($_POST['mat'], $_POST['fac'], $_POST['promotion'], $_POST['annee_acad']));
            $n = $verif->rowCount();

            if($n >= 1){
                while($data = $verif->fetch()){
                    echo '
                        <tr>
                            <td id="mat">'.$data['matricule'].'</td>
                            <td id="noms">'.utf8_decode($data['noms']).'</td>
                            <td id="fac">'.$data['fac'].'</td>
                            <td id="promotion">'.$data['promotion'].'</td>
                            <td id="annee_academique">'.$data['annee_academique'].'</td>
                            <td>
                                <a class="btn btn-primary" href="#" id="btn_affecter" data-toggle="modal" data-target="#mod_affectation"><i class="fa fa-plus-circle" aria-hidden="true"></i> Affecter</a>
                                <a class="btn btn-danger" href="#" id="btn_del_affecter" data-toggle="modal" data-target="#del_affectation"><i class="fa fa-recycle" aria-hidden="true"></i></a>
                            </td>
                        </tr>';
                }
            }else{
                die('<p class="h4 text-danger">Aucun resultat pour votre recherche</p>');
            }
        }else{
            die('<p class="h4 text-danger">Veuillez repmlir tous les champs svp !</p>');
        }
    }

    function vf($v, $t = array()){
        if(in_array($v, $t)){
            return 'checked';
        }else{
            return '';
        }
    }

    $erreur = array();

    function m($a = array()){
        foreach($a as $el){
            echo $el.', ';
        }
    }

    if(isset($_GET['payement']) && !empty($_GET['payement'])){
        if(!empty($_GET['section']) && !empty($_GET['promotion']) && !empty($_GET['annee_acad']) && !empty($_GET['mat_student']) && !empty($_GET['departement']) && !empty($_GET['option'])){
            // verification si on a deja affetcre le frais a l'etudiant 
            $ss = "SELECT affectation_frais.id, affectation_frais.matricule, prevision_frais.type_frais, prevision_frais.montant 
            FROM affectation_frais 
            LEFT JOIN prevision_frais ON affectation_frais.id_frais=prevision_frais.id_frais 
            WHERE affectation_frais.matricule = ?
            AND affectation_frais.id_section = ?
            AND affectation_frais.id_departement = ?
            AND affectation_frais.id_option = ?
            AND affectation_frais.id_annee = ?;";
            $s_array = array($_GET['mat_student'], $_GET['section'], $_GET['departement'], $_GET['option'], $_GET['annee_acad']);
            $ch = ConnexionBdd::Connecter()->prepare($ss);
            $ch->execute($s_array);

            $list_frais = array();
            $n = $ch->rowCount();

            // print_r($_GET);

            // selection du montant
            $verif_montant = ConnexionBdd::Connecter()->prepare("SELECT prevision_frais.id_frais, prevision_frais.type_frais, prevision_frais.montant, sections.section, departement.departement, options.option_, options.promotion FROM prevision_frais 
            LEFT JOIN sections on prevision_frais.id_section = sections.id_section 
            LEFT JOIN departement ON prevision_frais.id_departement = departement.id_departement 
            LEFT JOIN options ON prevision_frais.id_option = options.id_option 
            WHERE prevision_frais.id_section = ? AND
            prevision_frais.id_departement = ? AND
            prevision_frais.id_option = ? AND
            prevision_frais.id_annee = ?");
            $verif_montant->execute(array($_GET['section'], $_GET['departement'], $_GET['option'], $_GET['annee_acad']));
            $f = "";
            while($data_f = $ch->fetch()){
                $list_frais[] = $data_f['type_frais'];
                $f = $f.''. $data_f['type_frais'].', ';
            }
            $f = trim($f, ",");
            if(!empty($f)){
                echo('<b class="text-primary h6">'.$f.'</b>');
                echo '<hr class="mt-0 p-0"/>';
            }else{
                echo '<span class="text-danger">Aucun frais affecté</span><hr class="mt-0 p-0"/>';
            }
            
            while($data = $verif_montant->fetch()){
                echo '
                    <div class="form-group m-0 p-0">
                        <div id="ch_elmts" class="form-check" style="">
                            <label class="form-check-label">
                                <input type="checkbox" class="form-check-input" name="ch_sh" id="ch_sh" value="'.$data['id_frais'].'" placeholder="'.$data['type_frais'].'" '.vf($data['type_frais'], $list_frais).'>'.$data['type_frais'].'($'.$data['montant'].')
                            </label>
                        </div>
                        <hr class="mt-0 p-0"/>
                    </div>';
            }
        }else{
            echo 'Aucun type frais disponible ';
        }
    }

    // affectationdes frais aux etudiants
    if(isset($_POST['affect']) && !empty($_POST['affect']) && $_POST['affect'] = "affecter"){  
        // print_r($_POST);
        if(!empty($_POST['mat']) && !empty($_POST['promotion']) && !empty($_POST['frais']) && !empty($_POST['section']) && !empty($_POST['departement']) && !empty($_POST['option']) && !empty($_POST['annee_acad'])){
            $a = $_POST['frais'];
            // print_r($_POST);

            if(count($_POST['frais']) >= 1){
                for ($i=0; $i < count($a) ; $i++) { 
                    // verificatio si le montant n existe pas dans la base de donnees.
                    //matricule	id_frais	promotion	id_section	id_departement	id_option	id_annee
                    $verification = ConnexionBdd::Connecter()->prepare("SELECT * FROM affectation_frais WHERE matricule = ? AND id_frais =? AND promotion = ? AND id_section = ? AND id_departement=? AND id_option = ? AND id_annee = ?");
                    $verification->execute(array($_POST['mat'], $a[$i], $_POST['promotion'], $_POST['section'], $_POST['departement'], $_POST['option'], $_POST['annee_acad']));

                    $n = $verification->rowCount();
                    if($n <= 0){
                        $insert = ConnexionBdd::Connecter()->prepare("INSERT INTO affectation_frais(matricule, id_frais, promotion, id_section, id_departement, id_option, id_annee) VALUES(?, ?, ?, ?, ?, ?, ?)");
                        $ok  = $insert->execute(array($_POST['mat'], $a[$i], $_POST['promotion'], $_POST['section'], $_POST['departement'], $_POST['option'], $_POST['annee_acad'])); 
                        if(!$ok){
                            $erreur[] = "une affectation n'a pas reussie.";
                        }
                    }
                }
                if(count($erreur) < 1){
                    echo 'ok';
                }else{
                    echo "l'affectation n'a pas recu.";
                }
            }
        }else{
            echo "Veuillez selectionner un type de frais";
        }
    }else{
    }

    // recherche par faculte, promotion et par annee academique
    if(isset($_POST['id_fpa']) && !empty($_POST['id_fpa'])){
        if(!empty($_POST['fac_fpa']) && !empty($_POST['annee_acad_fpa']) && !empty($_POST['promotion_fpa'])){
            $sql_str = "SELECT * FROM etudiants_inscrits WHERE fac = ? AND promotion  = ? AND  annee_academique = ?";
            $sql_array = array($_POST['fac_fpa'], $_POST['promotion_fpa'], $_POST['annee_acad_fpa']);
            $insert_fpa = ConnexionBdd::Connecter()->prepare($sql_str);
            $insert_fpa->execute($sql_array);

            $n = $insert_fpa->rowCount();

            if($n >= 1){
                while($data = $insert_fpa->fetch()){
                    echo '
                        <tr>
                            <td id="mat">'.$data['matricule'].'</td>
                            <td id="noms">'.utf8_decode($data['noms']).'</td>
                            <td id="fac">'.$data['fac'].'</td>
                            <td id="promotion">'.$data['promotion'].'</td>
                            <td id="annee_academique">'.$data['annee_academique'].'</td>
                            <td>
                                <a class="btn btn-primary" href="#" id="btn_affecter" data-toggle="modal" data-target="#mod_affectation"><i class="fa fa-plus-circle" aria-hidden="true"></i> Affecter</a>
                                <a class="btn btn-danger" href="#" id="btn_del_affecter" data-toggle="modal" data-target="#del_affectation"><i class="fa fa-recycle" aria-hidden="true"></i></a>
                            </td>
                        </tr>';
                }
            }else{
                die('<p class="h3 text-danger">Aucun resultat trouver pour votre recherche</p>');
            }
        }else{
            die('<p class="h3 text-danger">Veuillez completer tous les champs svp</p>');
        }
    }else{
    }
?>