<?php
    session_start();
    require_once './ConnexionBdd.class.php';
    require_once './log_user.class.php';
    require_once './verification.class.php';
    // affichage des types des frais deja affecter a l etudiats
    // print_r($_GET);
    if(isset($_GET['payement_del_aff']) && !empty($_GET['payement_del_aff'])){
        // print_r($_GET);
        if(!empty($_GET['mat_student']) && !empty($_GET['section']) && !empty($_GET['departement']) && !empty($_GET['option']) && !empty($_GET['promotion']) && !empty($_GET['annee_acad'])){
            // verification si on a deja affetcre le frais a l'etudiant 
            $ss = "SELECT affectation_frais.id, affectation_frais.matricule, prevision_frais.type_frais, prevision_frais.montant 
            FROM affectation_frais 
            LEFT JOIN prevision_frais ON affectation_frais.id_frais=prevision_frais.id_frais 
            WHERE affectation_frais.matricule = ?
            AND affectation_frais.id_section = ?
            AND affectation_frais.id_departement = ?
            AND affectation_frais.id_option = ?
            AND affectation_frais.id_annee = ?";

            $s_array = array($_GET['mat_student'], $_GET['section'], $_GET['departement'], $_GET['option'], $_GET['annee_acad']);
            $ch = ConnexionBdd::Connecter()->prepare($ss);
            $ch->execute($s_array);

            while($data_f = $ch->fetch()){
                echo '
                    <div class="form-group m-0 p-0">
                        <div id="ch_elmts" class="form-check" style="">
                            <label class="form-check-label">
                                <input type="checkbox" class="form-check-input" name="ch_sh_d" id="ch_sh_d" value="'.$data_f['id'].'" placeholder="'.$data_f['type_frais'].'">'.$data_f['type_frais'].'($'.$data_f['montant'].')
                            </label>
                        </div>
                        <hr class="mt-0 p-0"/>
                    </div>';
            }

        }else{
            echo '';
        }
    }

    // suppression s des frais sur l etudiants
    else if(isset($_POST['d_affect']) && !empty($_POST['d_affect']) && $_POST['d_affect'] = "d_affect"){  
        // print_r($_POST);
        if(!empty($_POST['frais'])){

            $a = $_POST['frais'];
            $erreur = array();

            if(count($_POST['frais']) >= 1 ){
                for ($i=0; $i < count($a) ; $i++) { 
                    // suppresion des frais selectionner
                    $insert = ConnexionBdd::Connecter()->prepare("DELETE FROM affectation_frais where id = ?");
                    $ok  = $insert->execute(array($a[$i])); 
                    if(!$ok){
                        $erreur[] = "une affectation n'a pas reussie.";
                    }
                }
                if(count($erreur) < 1){
                    echo 'ok';
                    LogUser::addlog(VerificationUser::verif($_SESSION['data']['id_user']), 'il/elle a supprimé(e) certains type de frais affecter à l étudiant(e)');
                }else{
                    echo "l'affectation n'a pas recu.";
                }
            }
        }else{
            echo "Veuillez selectionner un type de frais";
        }
    }else{
        die("une erreur est survenue.");
    }
?>