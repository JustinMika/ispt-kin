<?php
    require_once './ConnexionBdd.class.php';
    require_once './log_user.class.php';
    require_once './verification.class.php';
    require_once '../admin/PHPExcel/PHPExcel.php';
    require_once '../admin/PHPExcel/PHPExcel/IOFactory.php';

    if(isset($_FILES['files']['name']) && !empty($_FILES['files']['name'])){
        if(isset($_FILES['files']) && !empty($_FILES['files'])){
            $files_excel = $_FILES["files"]["tmp_name"];
            $file_name = strtolower($_FILES['files']['name']);
            $extensions_autorisees = array('xlsx', 'xls');
            // verification de l extension du fichier uploader si c;est un fichier excel.
            $extension_upload = strtolower(substr(strrchr($_FILES['files']['name'], '.'), 1));
			if(in_array($extension_upload, $extensions_autorisees)){
                // code du fichier : prev_
                $fn = $file_name[0].''.$file_name[1].''.$file_name[2].''.$file_name[3].''.$file_name[4];
                if($fn == "prev_"){
                    try {
                        //on commence les t3
                        $objPHPExcel = PHPExcel_IOFactory::load($files_excel);

                        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                            $worksheetTitle     = $worksheet->getTitle();
                            $highestRow         = $worksheet->getHighestRow(); // e.g. 10
                            $highestColumn      = $worksheet->getHighestColumn(); // e.g 'F'
                            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
                            $nrColumns = ord($highestColumn) - 64;

                            for ($i=2; $i <= $highestRow ; $i++){
                                $type = $worksheet->getCellByColumnAndRow(1, $i)->getValue();
                                $code = $worksheet->getCellByColumnAndRow(0, $i)->getValue();
                                $code = strtoupper($code);
                                $montant = $worksheet->getCellByColumnAndRow(2, $i)->getValue();

                                // verification s il y a pas des champs qui sont laiasser vide par erreur par l utilisateur
                                if(!empty($type) || !empty($code) || !empty($montant) ){
                                    // on recupere le dernier annee academique
                                    $an =  ConnexionBdd::Connecter()->query("SELECT * FROM annee_acad GROUP BY annee_acad ORDER BY id_annee DESC LIMIT 1");
                                    if($an->rowCount() > 0){
                                        $an_r = $an->fetch();
                                    }else{
                                        $an_r['id_annee'] = '';
                                        die("Veuillez AJouter l annee academique");
                                    }
                                    // verification pour le matricule, promotion, fac, annee acad
                                    $v = ConnexionBdd::Connecter()->prepare("SELECT * FROM options WHERE code_ = ? AND id_annee = ? ORDER BY id_option DESC LIMIT 0, 1");
                                    $v->execute(array($code, $an_r['id_annee']));

                                    if($v->rowCount() > 0){
                                        $data = $v->fetch();
                                        $id_section = $data['id_section'];
                                        $id_option = $data['id_option'];
                                        $id_departement = $data['id_departement'];
                                        $promotion = $data['promotion'];

                                        // on verifie si le frais n existe pas dans la base de donnees

                                        $verif = ConnexionBdd::Connecter()->prepare("SELECT * FROM prevision_frais WHERE type_frais = ? AND id_annee  = ? AND promotion = ? AND id_section = ? AND id_departement = ? AND id_option = ?");
                                        $verif->execute(array($type, $an_r['id_annee'], $promotion, $id_section, $id_departement, $id_option));

                                        $n = $verif->rowCount();

                                        if($n >= 0){
                                            // insertion de donnees dans la base de donnees 
                                            $insert_etud = ConnexionBdd::Connecter()->prepare("INSERT INTO prevision_frais(type_frais, montant, promotion, id_section, id_departement, id_option, id_annee) VALUES(?, ?, ?, ?, ?, ?, ?)");
                                            $ok = $insert_etud->execute(array($type, $montant, $promotion, $id_section, $id_departement, $id_option, $an_r['id_annee']));
                                            if(!$ok){
                                                echo "Ligne {$i} :: une erreur est survenues : les donnees ne sont pas inserer";
                                            }
                                        }else{
                                            echo "le type de frais : '".$type."' existe déjà pour l'année ".$an_r['annee_acad'];
                                        }
                                    }else{
                                        echo("Ligne {$i} : Le code de l'option {$code} n'est pas enregistrer<hr class='m-0'>");
                                    }
                                }else{
                                    // on fait quedal on saute cette ligne
                                }
                            }
                            // LogUser::addlog(VerificationUser::verif($_SESSION['data']['id_user']), "a ajouté des prevision de frais pour les etudiants.");
                            echo "Traitement reussi avec succes"; 
                        }               
                    } catch (\Throwable $th) {
                        echo "Erreur : ".$th->getMessage();
                    }
                }else{
                    echo("Veuillez charger le fichier de prevision des frais pas n importe quoi svp !!!.");
                }
            }else{
                echo "Veuillez charger le fichier Excel svp ...";
            }
        }else{
            echo("Le fichier Excel n'est pas recu");
            header('500 Erreur interne du serveur', true, 500);
        }
    }elseif ($_FILES['files_post_d']['name'] != '') {
        if(isset($_FILES['files_post_d']) && !empty($_FILES['files_post_d'])){
            $files_excel = $_FILES["files_post_d"]["tmp_name"];
            // code du fichier : rec_univ_
            $f = strtolower($_FILES['files_post_d']['name']);
            $fn = $f[0].''.$f[1].''.$f[2].''.$f[3].''.$f[4].''.$f[5].''.$f[6].''.$f[7].''.$f[8];
            $extensions_autorisees = array('xlsx', 'xls');
            // verification de l extension du fichier uploader si c;est un fichier excel.
            $extension_upload = strtolower(substr(strrchr($_FILES['files_post_d']['name'], '.'), 1));
			if(in_array($extension_upload, $extensions_autorisees)){
                if($fn == "rec_univ_"){
                    try {
                        //on commence les t3
                        $objPHPExcel = PHPExcel_IOFactory::load($files_excel);

                        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                            $worksheetTitle     = $worksheet->getTitle();
                            $highestRow         = $worksheet->getHighestRow(); // e.g. 10
                            $highestColumn      = $worksheet->getHighestColumn(); // e.g 'F'
                            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
                            $nrColumns = ord($highestColumn) - 64;

                            for ($i=2; $i <= $highestRow ; $i++){
                                $poste = $worksheet->getCellByColumnAndRow(0, $i)->getValue();
                                $annee_acad = $worksheet->getCellByColumnAndRow(1, $i)->getValue();
                                $montant = $worksheet->getCellByColumnAndRow(2, $i)->getValue();

                                // verification s il y a pas des champs qui sont laiasser vide par erreur par l utilisateur
                                if(!empty($poste) && !empty($annee_acad) && !empty($montant)){
                                    $verif = ConnexionBdd::Connecter()->prepare("SELECT * FROM previson_frais_univ WHERE poste = ? AND annee_acad  = ? AND montant = ?");
                                    $verif->execute(array($poste, $annee_acad, $montant));

                                    $nbre = $verif->rowcount();
                                    if($nbre <= 0){
                                        // insertion de donnees dans la base de donnees 
                                        $insert_etud = ConnexionBdd::Connecter()->prepare("INSERT INTO previson_frais_univ(poste, annee_acad, montant) VALUES(?,?,?)");
                                        $ok = $insert_etud->execute(array($poste, $annee_acad, $montant));
                                        if($ok){
                                            // echo ("Donnees non insere");
                                        }else{
                                            echo "Erreur sur la ligne {$i} : une erreur est survenues : les donnees ne sont pas inserer";
                                        }
                                    }else{
                                        // on branle quedal 
                                        echo "{$poste} existe deja dans la base de donnees pour cette annee academique";
                                    }
                                }else{
                                    // on fait quedal on saute cette ligne
                                }
                            }
                            echo "Traitement reussi avec succes"; 
                        }               
                    } catch (\Throwable $th) {
                        echo "Erreur : "+$th;
                    }
                }else{
                    echo 'Veuillez charger le fichier de recette universitaire pas n importe quoi svp !...';
                }
            }else{
                echo 'Veuillez charger le fichier Excel svp !!!';
            }
        }else{
            echo("Le fichier Excel n'est pas recu");
            // header('500 Erreur interne du serveur', true, 500);
        }
    } else{
        echo 'Il parait qu il ya une erreur les donnees ne sont recu';
        // header('500 Erreur interne du serveur', true, 500);
    }
?>