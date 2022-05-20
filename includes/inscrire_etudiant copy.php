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

                        // traitement
                        for ($i=2; $i <= $highestRow+1 ; $i++){
                            $mat = $worksheet->getCellByColumnAndRow(0, $i)->getValue();
                            $pwd = $worksheet->getCellByColumnAndRow(1, $i)->getValue();
                            $noms = $worksheet->getCellByColumnAndRow(2, $i)->getValue();
                            $faculte = $worksheet->getCellByColumnAndRow(3, $i)->getValue();
                            $promotion = $worksheet->getCellByColumnAndRow(4, $i)->getValue();
                            $annee_academique = $worksheet->getCellByColumnAndRow(5, $i)->getValue();
                            $pwd = htmlspecialchars(trim($pwd));

                            if(!empty($mat) && !empty($pwd) && !empty($noms) && !empty($faculte) && !empty($promotion) && !empty($annee_academique)){
                                $verif = ConnexionBdd::Connecter()->prepare("SELECT * FROM etudiants_inscrits WHERE matricule = ? AND password = ? AND noms = ? AND fac = ? AND promotion = ? AND annee_academique = ?");

                                $verif->execute(array($mat, sha1($pwd), $noms, $faculte, $promotion, $annee_academique));
                                $nbre = $verif->rowcount();
                                // verification si les donnees existe deja dans la base de donnees
                                if($nbre <= 0){
                                    // verification pour le matricule, promotion, fac, annee acad
                                    $v = ConnexionBdd::Connecter()->prepare("SELECT * FROM etudiants_inscrits WHERE matricule = ? AND fac = ? AND promotion = ? AND annee_academique = ?");
                                    $v->execute(array($mat, $faculte, $promotion, $annee_academique));
                                    // verification
                                    if($v->rowCount() <= 0){
                                        // verification sur un matricule et l'annee
                                        $v = ConnexionBdd::Connecter()->prepare("SELECT * FROM etudiants_inscrits WHERE matricule = ? AND annee_academique = ?");
                                        $v->execute(array($mat, $annee_academique));

                                        if($v->rowCount() <= 0){
                                            // insertion de donnees dans la base de donnees 
                                            $pwd = htmlspecialchars(trim($pwd));
                                            $insert_etud = ConnexionBdd::Connecter()->prepare("INSERT INTO etudiants_inscrits(matricule, password, noms, fac, promotion, annee_academique) VALUES(?, ?, ?, ?, ?, ?)");
                                            $ok = $insert_etud->execute(array($mat, sha1($pwd), $noms, $faculte, $promotion, $annee_academique));
                                            if($ok){
                                                // tables des etudiants
                                                $pwd = htmlspecialchars(trim($pwd));
                                                $r = ConnexionBdd::Connecter()->prepare("SELECT * FROM etudiants WHERE matricule = ? AND noms = ? AND photo = '../images/etudiants.jpg' AND password = ?");
                                                $r->execute(array($mat, $noms, $pwd));
                                                if($r->rowCount() <= 0){
                                                    $insert_etud = ConnexionBdd::Connecter()->prepare("INSERT INTO etudiants(matricule, noms, photo, password) VALUES(?,?,'../images/etudiants.jpg', ?)");
                                                    $pwd = sha1($pwd);
                                                    $insert_etud->execute(array($mat, $noms, $pwd));
                                                }
                                            }else{
                                                // les donnees ne sont inserer dans la base de donnees.
                                                echo "Données non inserée à la ligne {$i} dans le fichier {$nf}<hr class='m-0'>";
                                            }
                                        }else{
                                            echo ("le matricule {$mat} est deja prise pour l'annee academique {$annee_academique}");
                                        }
                                    }else{
                                        echo("ligne {$i}: l étudiant <b>{$mat} {$noms}-{$faculte}-{$promotion} {$annee_academique}</b> existe déjà <hr class='m-0'>");
                                    }
                                }else{
                                    // rien a faire les donnees existe deja dans la table
                                    echo("l etudiant existe deja<hr class='m-0'>");
                                }
                            }
                        }
                        echo "<br/>Traitement reussi avec succès<hr class='m-0'>";
                        LogUser::addlog(VerificationUser::verif($_SESSION['data']['noms']), 'il a uploader le fichier ['.$_FILES['file']['name'].'] contenant les identités des étudiants');
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