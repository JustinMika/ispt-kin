<?php
    session_start();
    header('Content-type:text/html; charset=UTF-8');
    require_once './ConnexionBdd.class.php';
    require_once './log_user.class.php';
    require_once './verification.class.php';
    require_once '../admin/PHPExcel/PHPExcel.php';
    require_once '../admin/PHPExcel/PHPExcel/IOFactory.php';

    if($_FILES['file']['name'] != ''){
        if(isset($_FILES['file']) && !empty($_FILES['file'])){
            $files_excel = $_FILES["file"]["tmp_name"];
            $nf = strtolower($_FILES['file']['name']);
            $n = $nf[0].''.$nf[1].''.$nf[2].''.$nf[3];
            $extensions_autorisees = array('xlsx', 'xls');

            // verification de l extension du fichier uploader si c;est un fichier excel.
            $extension_upload = strtolower(substr(strrchr($_FILES['file']['name'], '.'), 1));
			if(in_array($extension_upload, $extensions_autorisees)){
                if($n == "ins_"){
                    //on commence les t3
                    $objPHPExcel = PHPExcel_IOFactory::load($files_excel);
                    foreach ($objPHPExcel->getWorksheetIterator() as $worksheet){
                        $worksheetTitle     = $worksheet->getTitle();
                        $highestRow         = $worksheet->getHighestRow(); // e.g. 10
                        $highestColumn      = $worksheet->getHighestColumn(); // e.g 'F'
                        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
                        $nrColumns = ord($highestColumn) - 64;

                        // on recupere le dernier annee academique
                        $a = ConnexionBdd::Connecter()->query("SELECT id_annee FROM annee_acad ORDER BY id_annee DESC LIMIT 0,1");
                        if($a->rowCount() > 0){
                            $data = $a->fetch();
                        }else{
                            $data['id_annee'] = '';
                            die("Veuillez resneigner l'annee academique svp...");
                        }

                        // traitement
                        for ($i=2; $i <= $highestRow+1 ; $i++){
                            $mat = $worksheet->getCellByColumnAndRow(0, $i)->getValue();
                            $pwd = $worksheet->getCellByColumnAndRow(1, $i)->getValue();
                            $noms = $worksheet->getCellByColumnAndRow(2, $i)->getValue();
                            $code = $worksheet->getCellByColumnAndRow(3, $i)->getValue();
                            $pwd = htmlspecialchars(trim($pwd));

                            if(!empty($mat) && !empty($pwd) && !empty($noms) && !empty($code)){
                                $verif = ConnexionBdd::Connecter()->prepare("SELECT * FROM etudiants_inscrits WHERE matricule = ? AND id_annee = ?");

                                $verif->execute(array($mat, $data['id_annee']));
                                $nbre = $verif->rowcount();
                                // verification si les donnees existe deja dans la base de donnees
                                if($nbre <= 0){
                                    // verification pour le matricule, promotion, fac, annee acad
                                    $v = ConnexionBdd::Connecter()->prepare("SELECT * FROM options WHERE code_ = ? AND id_annee = ? ORDER BY id_option DESC LIMIT 0, 1");
                                    $v->execute(array($code, $data['id_annee']));
                                    // verification 
                                    if($v->rowCount() > 0){
                                        $data = $v->fetch();
                                        // print_r($data);
                                        $id_section = $data['id_section'];
                                        $id_option = $data['id_option'];
                                        $id_departement = $data['id_departement'];
                                        $promotion = $data['promotion'];

                                        $insetion_etudiant = ConnexionBdd::Connecter()->prepare("INSERT INTO etudiants_inscrits(matricule, noms, password, id_section, id_departement, id_option, promotion, id_annee) VALUES(?,?,?,?,?,?,?,?)");
                                        $ok = $insetion_etudiant->execute(array($mat, $noms, $pwd, $id_section, $id_departement, $id_option, $promotion, $data['id_annee']));

                                        if($ok){
                                            echo "";
                                        }else{
                                            echo("Erreur : l'etudiant {$mat} n'est pas enregistrer. ligne {$i}<hr class='m-0'>");
                                        }
                                    }else{
                                        echo("lLe code de l'option {$code} n'est pas enregistrer<hr class='m-0'>");
                                    }
                                }else{
                                    // rien a faire les donnees existe deja dans la table
                                    echo("ligne {$i}: l étudiant <b>{$mat} {$noms}</b> existe déjà <hr class='m-0'>");
                                }
                            }else{
                                // echo("Il y'a des elements manquants sur la ligne {$i} du fichier Execel:<hr class='m-0'>");
                            }
                        }
                        echo "<br/>Traitement reussi avec succès<hr class='m-0'>";
                        LogUser::addlog(VerificationUser::verif($_SESSION['data']['id_user']), 'il a uploader le fichier ['.$_FILES['file']['name'].'] contenant les identités des étudiants');
                    }
                }else{
                    die("Veuillez selectionner un fichier d'insciption des étudiants pas n'importe quoi svp !!!");
                }
            }else{
                echo 'Veuillez charger le fichier Excel svp !!! ...';
            }
        }else{
            die("Le fichier Excel n'est pas reçu");
            header('500 Erreur interne du serveur', true, 500);
        }
    }else{
        echo 'Il parait qu il ya une erreur les données ne sont reçu';
        header('500 Erreur interne du serveur', true, 500);
    }
?>