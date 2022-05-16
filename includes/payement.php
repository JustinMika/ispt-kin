<?php
    session_start();
    require_once './ConnexionBdd.class.php';
    require_once './log_user.class.php';
    require_once './verification.class.php';
    require_once '../admin/PHPExcel/PHPExcel.php';
    require_once '../admin/PHPExcel/PHPExcel/IOFactory.php';

    // tableau d'erreur
    $erreur = array();

    if($_FILES['fichier_payement']['name'] != ''){
        if(isset($_FILES['fichier_payement']) && !empty($_FILES['fichier_payement'])){
            $files_excel = $_FILES["fichier_payement"]["tmp_name"];

            $extensions_autorisees = array('xlsx', 'xls');
            // verification de l extension du fichier uploader si c;est un fichier excel.
            $extension_upload = strtolower(substr(strrchr($_FILES['fichier_payement']['name'], '.'), 1));
			if(in_array($extension_upload, $extensions_autorisees)){
                // code du fichier : pay_
                $file_name = strtolower($_FILES['fichier_payement']['name']);
                $fn = $file_name[0].''.$file_name[1].''.$file_name[2].''.$file_name[3];
                if($fn == "pay_"){
                    try {
                        $objPHPExcel = PHPExcel_IOFactory::load($files_excel);
                        // die("");
                        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet){
                            $worksheetTitle     = $worksheet->getTitle();
                            $highestRow         = $worksheet->getHighestRow(); // e.g. 10
                            $highestColumn      = $worksheet->getHighestColumn(); // e.g 'F'
                            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
                            $nrColumns = ord($highestColumn) - 64;

                            // selection de l'annee academique
                            $list = ConnexionBdd::Connecter()->query("SELECT * FROM `annee_academique` ORDER BY id DESC LIMIT 1"); 
                            $data = $list->fetch();
                            if(isset($data['annee_acad']) && !empty($data['annee_acad'])){
                                $annee_acad = $data['annee_acad'];
                            }else{
                                $annee_acad = '';
                            }
                            
                            // traitement des donees
                            for ($i=2; $i <= $highestRow ; $i++){
                                $mat = $worksheet->getCellByColumnAndRow(0, $i)->getValue();
                                $fac = $worksheet->getCellByColumnAndRow(1, $i)->getValue();
                                $promotion = $worksheet->getCellByColumnAndRow(2, $i)->getValue();
                                $type_frais = $worksheet->getCellByColumnAndRow(3, $i)->getValue();
                                $montant = floatval($worksheet->getCellByColumnAndRow(4, $i)->getValue());
                                $num_recu = $worksheet->getCellByColumnAndRow(5, $i)->getValue();
                                $date_p = $worksheet->getCellByColumnAndRow(6, $i)->getValue();
                                $date_p = date("Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($date_p, false, null));
                                // $date_p = date("Y-m-d");

                                // selection des frais que l'etudiants doit payer
                                $sql_req = "SELECT * FROM etudiants_inscrits WHERE matricule = ? AND fac = ? AND promotion  = ? AND annee_academique = ?";
                                $sel_etudiant = ConnexionBdd::Connecter()->prepare($sql_req);
                                $sel_etudiant->execute(array($mat, $fac, $promotion,  $annee_acad));

                                $n = $sel_etudiant->rowCount();

                                if($n > 0){
                                    // on verifie si les frais qu on veut payer a l' etudiant lui est affecte
                                    $sql_t_frais = "SELECT * FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ? AND type_frais = ?";
                                    $verif_t_frais = ConnexionBdd::Connecter()->prepare($sql_t_frais);
                                    $verif_t_frais->execute(array($mat, $promotion, $fac,  $annee_acad, $type_frais));

                                    $n_result = $verif_t_frais->rowCount();

                                    if($n_result > 0){
                                        // on verifie si le payement n pas dans la base de donnees. meme si le numero de bordereau est unique pour chaque payement : on est sait jamais : il faut jamais faire confiance aux p*tains des utilisateurs
                                        $verif = ConnexionBdd::Connecter()->prepare("SELECT * FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ? AND annee_acad = ? AND date_payement = ? AND type_frais = ? AND num_borderon = ? AND montant = ?");
                                        $verif->execute(array($mat, $fac, $promotion, $annee_acad, $date_p, $type_frais, $num_recu, $montant));
                                                    
                                        $nbre = $verif->rowcount();
                                        if($nbre <= 0){
                                            // on verifie s il ne va pas depasser le montant au dela de c qu il devrait payer

                                            $tot_aff = ConnexionBdd::Connecter()->prepare("SELECT SUM(montant) as tot_af FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ? AND type_frais = ?");
                                            $tot_aff->execute(array($mat, $promotion, $fac, $annee_acad, $type_frais));
                                            $v_tot_aff = $tot_aff->fetch();
                                            $montant_t_p = $v_tot_aff['tot_af'];

                                            // // le montant
                                            $verif = ConnexionBdd::Connecter()->prepare("SELECT SUM(montant) AS montant FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ? AND annee_acad = ? AND type_frais = ?");
                                            $verif->execute(array($mat, $fac, $promotion, $annee_acad, $type_frais));
                                            $v_verif = $verif->fetch();
                                            $montant_t_a_payer = $v_verif['montant'];

                                            if(intval($montant_t_a_payer + $montant) <= intval($montant_t_p)){
                                                // on procede au payement
                                                // insertion de donnees dans la base de donnees 
                                                $insert_etud = ConnexionBdd::Connecter()->prepare("INSERT INTO payement(matricule, faculte, promotion, annee_acad, date_payement, type_frais, num_borderon, montant) VALUES(?,?,?,?,?,?,?,?)");
                                                $ok = $insert_etud->execute(array($mat, $fac, $promotion, $annee_acad, $date_p, $type_frais, $num_recu, $montant));
                                                if(!$ok){
                                                    echo("ligne : {$i}: une erreur est survenue. reesayer plus tard ... pour la ligne {$i} du fichier excel".PHP_EOL);
                                                }
                                            }else{
                                                echo 'ligne : {'.$i.'}: le montant '.$montant.' pour le '.$type_frais.' est superieur a celui affecter a l\'etudiant : <b>['.$mat.'-'.$promotion.'-'.$fac.']</b> <br><hr>';
                                            }
                                        }else{
                                            $erreur[] = "le payement existe deja dans la base de donnees. Veuillez verifier le bordereau avant ...".PHP_EOL;
                                            // echo "le payement existe deja dans la base de donnees. Veuillez verifier le bordereau avant ...";
                                        }
                                    }else{
                                        $erreur[] = 'le <i>'.$type_frais.'</i> n\'est pas affecter a l\'etudiant <b>['.$mat.'-'.$promotion.'-'.$fac.']</b><hr class="m-0 p-0" />'.PHP_EOL;
                                        echo 'le <i>'.$type_frais.'</i> n\'est pas affecter a l\'etudiant <b>['.$mat.'-'.$promotion.'-'.$fac.']</b><hr class="m-0 p-0" />'.PHP_EOL;
                                    }
                                }else{
                                    $erreur[] = 'l\'etudiant <b>['.$mat.'-'.$promotion.'-'.$fac.']</b> n\'est pas inscrit<hr class="m-0 p-0" />'.PHP_EOL;
                                    echo('l\'etudiant <b>['.$mat.'-'.$promotion.'-'.$fac.']</b> n\'est pas inscrit<hr class="m-0 p-0" />'.PHP_EOL);
                                }
                            }
                            $path = "archive_excel";
                            $full_name = "$path/".date("Y-m-d H_m_s").'_'.strtolower($_FILES["fichier_payement"]["name"]);
                            // move_uploaded_file($this->getNameFile(), $this->$full_name);
                            // $result = move_uploaded_file($_FILES["fichier_payement"]["tmp_name"], $full_name);
                            
                            LogUser::addlog(VerificationUser::verif($_SESSION['data']['noms']), 'a uploader le fichier Excel contant les payements des etudiants');

                            if(count($erreur) < 1){
                                echo "Donnees inserer avec succes.";
                            }else{
                                $_SESSION['erreur'] = $erreur;
                            }
                        }
                    } catch (Exception $e) {
                        die("Error : ".$e);
                    }
                }else{
                    die("Veuillez charger le fichier Excel de payement pas n importe quoi svp !!! <br/>");
                }
			}
			else{
				die("Extenction non valide. Veuillez selectionner un fichier Excel svp...".PHP_EOL);
			}
        }else{
            die("Le fichier Excel n'est pas recu : veuillez selectionner un fichier contenant les payements des etudiants.".PHP_EOL);
        }
    }else{
        echo 'Il parait qu il ya une erreur les donnees ne sont recu'.PHP_EOL;
    }
?>