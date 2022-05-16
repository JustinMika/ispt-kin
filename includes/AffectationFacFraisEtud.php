<?php
    session_start();
    require_once './ConnexionBdd.class.php';
    if(isset($_GET['affich']) && !empty($_GET['affich'])){
        // on recupere le dernier annee academique
        $a = ConnexionBdd::Connecter()->query("SELECT annee_acad FROM annee_academique ORDER BY id DESC LIMIT 0,1");
        if($a->rowCount() > 0){
            $data = $a->fetch();
        }else{
            $data['annee_acad'] = '';
        }
        $verif = ConnexionBdd::Connecter()->prepare("SELECT * FROM etudiants_inscrits WHERE annee_academique = ? AND fac = ?");
        $verif->execute(array($data['annee_acad'], $_GET['fac']));
        if($verif->rowCount() >= 1){
            while($data = $verif->fetch()){
                echo '
                    <tr>
                        <td id="mat">'.$data['matricule'].'</td>
                        <td id="noms">'.$data['noms'].'</td>
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
            echo 'Aucun(e) etudiant(e)s';
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
                            <td id="noms">'.$data['noms'].'</td>
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
        if(!empty($_GET['fac']) && !empty($_GET['promotion']) && !empty($_GET['annee_acad']) && !empty($_GET['mat_student'])){
            // verification si on a deja affetcre le frais a l'etudiant 
            $ss = "SELECT * FROM affect_fac_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ?";
            $s_array = array($_GET['mat_student'], $_GET['promotion'], $_GET['fac'], $_GET['annee_acad']);
            $ch = ConnexionBdd::Connecter()->prepare($ss);
            $ch->execute($s_array);

            $list_frais = array();
            $n = $ch->rowCount();

            // selection du montant
            $verif_montant = ConnexionBdd::Connecter()->prepare("SELECT * FROM  prev_fac_frais WHERE annee_acad = ? AND faculte = ? AND promotion = ?");
            $verif_montant->execute(array($_GET['annee_acad'], $_GET['fac'], $_GET['promotion']));
            $f = "";
            while($data_f = $ch->fetch()){
                $list_frais[] = $data_f['type_frais'];
                $f = $f.''. $data_f['type_frais'].', ';
            }
            $f = trim($f, ",");
            echo('<b class="text-primary h6">'.$f.'</b>');
            echo '<hr class="mt-0 p-0"/>';
            while($data = $verif_montant->fetch()){
                echo '
                    <div class="form-group m-0 p-0">
                        <div id="ch_elmts" class="form-check" style="">
                            <label class="form-check-label">
                                <input type="checkbox" class="form-check-input" name="ch_sh" id="ch_sh" value="'.$data['montant'].'" placeholder="'.$data['type_frais'].'" '.vf($data['type_frais'], $list_frais).'>'.$data['type_frais'].'($'.$data['montant'].')
                            </label>
                        </div>
                        <hr class="mt-0 p-0"/>
                    </div>';
            }
        }else{
            echo '';
        }
    }

    if(isset($_POST['affect']) && !empty($_POST['affect']) && $_POST['affect'] = "affecter"){  
        // print_r($_POST);
        if(!empty($_POST['matricule']) && !empty($_POST['promotion_aff']) && !empty($_POST['frais']) && !empty($_POST['fac_aff']) && !empty($_POST['annee_acad_aff']) && !empty($_POST['montant_f'])){

            $a = $_POST['frais'];
            $b = $_POST['montant_f'];
            // print_r($_POST);

            if(count($_POST['frais']) == count($_POST['montant_f'])){
                for ($i=0; $i < count($a) ; $i++) { 
                    // verificatio si le montant n existe pas dans la base de donnees.
                    $verification = ConnexionBdd::Connecter()->prepare("SELECT * FROM affect_fac_frais WHERE matricule = ? AND promotion=? AND faculte = ? AND annee_acad=? AND type_frais=? AND montant = ?");
                    $verification->execute(array($_POST['matricule'], $_POST['promotion_aff'], $_POST['fac_aff'], $_POST['annee_acad_aff'], $a[$i], $b[$i]));

                    $n = $verification->rowCount();
                    if($n <= 0){
                        $insert = ConnexionBdd::Connecter()->prepare("INSERT INTO affect_fac_frais(matricule, promotion, faculte, annee_acad, type_frais, montant) VALUES(?, ?, ?, ?, ?, ?)");
                        $ok  = $insert->execute(array($_POST['matricule'], $_POST['promotion_aff'], $_POST['fac_aff'], $_POST['annee_acad_aff'], $a[$i], $b[$i])); 
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
        // die("une erreur est survenue -2");
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
                            <td id="noms">'.$data['noms'].'</td>
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
        // die('<p class="h3 text-danger">Veuillez completer tous les champs svp</p>');
    }
?>