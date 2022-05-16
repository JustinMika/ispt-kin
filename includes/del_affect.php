<?php
    session_start();
    require_once './ConnexionBdd.class.php';
    require_once './log_user.class.php';
    require_once './verification.class.php';
    // affichage des types des frais deja affecter a l etudiats
    if(isset($_GET['payement_del_aff']) && !empty($_GET['payement_del_aff'])){
        if(!empty($_GET['fac']) && !empty($_GET['promotion']) && !empty($_GET['annee_acad']) && !empty($_GET['mat_student'])){
            // verification si on a deja affetcre le frais a l'etudiant 
            $ss = "SELECT * FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ?";
            $s_array = array($_GET['mat_student'], $_GET['promotion'], $_GET['fac'], $_GET['annee_acad']);
            $ch = ConnexionBdd::Connecter()->prepare($ss);
            $ch->execute($s_array);

            while($data_f = $ch->fetch()){
                echo '
                    <div class="form-group m-0 p-0">
                        <div id="ch_elmts" class="form-check" style="">
                            <label class="form-check-label">
                                <input type="checkbox" class="form-check-input" name="ch_sh_d" id="ch_sh_d" value="'.$data_f['montant'].'" placeholder="'.$data_f['type_frais'].'">'.$data_f['type_frais'].'($'.$data_f['montant'].')
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
        if(!empty($_POST['mat']) && !empty($_POST['promotion']) && !empty($_POST['frais']) && !empty($_POST['fac']) && !empty($_POST['annee_acad']) && !empty($_POST['montant_f'])){

            $a = $_POST['frais'];
            $b = $_POST['montant_f'];
            // print_r($_POST);
            $erreur = array();

            if(count($_POST['frais']) == count($_POST['montant_f'])){
                for ($i=0; $i < count($a) ; $i++) { 
                    // suppresion des frais selectionner
                    $insert = ConnexionBdd::Connecter()->prepare("DELETE FROM affectation_frais where matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ? AND type_frais = ? AND montant = ?");
                    $ok  = $insert->execute(array($_POST['mat'], $_POST['promotion'], $_POST['fac'], $_POST['annee_acad'], $a[$i], $b[$i])); 
                    if(!$ok){
                        $erreur[] = "une affectation n'a pas reussie.";
                    }
                }
                if(count($erreur) < 1){
                    echo 'ok';
                    LogUser::addlog(VerificationUser::verif($_SESSION['data']['noms']), 'il/elle a supprimé(e) certains type de frais affecter à '.$_POST['mat'].' : '.$_POST['promotion'].' : '.$_POST['fac']);
                }else{
                    echo "l'affectation n'a pas recu.";
                }
            }
        }else{
            echo "Veuillez selectionner un type de frais";
        }
    }else{
        die("une erreur est survenue #2");
    }
?>