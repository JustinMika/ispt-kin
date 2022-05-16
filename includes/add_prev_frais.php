<?php
    require_once './ConnexionBdd.class.php';
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
                                $type = $worksheet->getCellByColumnAndRow(0, $i)->getValue();
                                $annee_acad = $worksheet->getCellByColumnAndRow(1, $i)->getValue();
                                $montant = $worksheet->getCellByColumnAndRow(2, $i)->getValue();
                                $faculte = $worksheet->getCellByColumnAndRow(3, $i)->getValue();
                                $promotion = $worksheet->getCellByColumnAndRow(4, $i)->getValue();

                                // verification s il y a pas des champs qui sont laiasser vide par erreur par l utilisateur
                                if(!empty($type) && !empty($annee_acad) && !empty($montant) && !empty($faculte) && !empty($promotion)){
                                    $verif = ConnexionBdd::Connecter()->prepare("SELECT * FROM prevision_frais WHERE type_frais = ? AND annee_acad  = ? AND montant = ? AND faculte = ? AND promotion = ?");
                                    $verif->execute(array($type, $annee_acad, $montant,  $faculte, $promotion));

                                    $nbre = $verif->rowcount();
                                    if($nbre <= 0){
                                        // insertion de donnees dans la base de donnees 
                                        $insert_etud = ConnexionBdd::Connecter()->prepare("INSERT INTO prevision_frais(type_frais, annee_acad, montant, faculte, promotion) VALUES(?,?,?,?,?)");
                                        $ok = $insert_etud->execute(array($type, $annee_acad, $montant,  $faculte, $promotion));
                                        if(!$ok){
                                            echo "une erreur est survenues : les donnees ne sont pas inserer";
                                        }
                                    }else{
                                        // on branle quedal 
                                        // echo "les donnees existe deja dans la base de donnees";
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