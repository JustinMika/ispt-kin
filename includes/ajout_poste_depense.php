<?php
    session_start();
    require_once './ConnexionBdd.class.php';
    require_once '../admin/PHPExcel/PHPExcel.php';
    require_once '../admin/PHPExcel/PHPExcel/IOFactory.php';

    if($_FILES['file']['name'] != ''){
        if(isset($_FILES['file']) && !empty($_FILES['file'])){
            $files_excel = $_FILES["file"]["tmp_name"];
            $file_name = strtolower($_FILES['file']['name']);
            $fn = $file_name[0].''.$file_name[1].''.$file_name[2];
            // code du fichier : pd_nom_fichier
            $extensions_autorisees = array('xlsx', 'xls');
            // verification de l extension du fichier uploader si c;est un fichier excel.
            $extension_upload = strtolower(substr(strrchr($_FILES['file']['name'], '.'), 1));
			if(in_array($extension_upload, $extensions_autorisees)){
                if($fn == "pd_"){
                    $extensions_autorisees = array('xlsx', 'xls');
                    // verification de l extension du fichier uploader si c;est un fichier excel.
                    $extension_upload = strtolower(substr(strrchr($_FILES['file']['name'], '.'), 1));
                    if(in_array($extension_upload, $extensions_autorisees)){
                        //on commence les t3
                        $objPHPExcel = PHPExcel_IOFactory::load($files_excel);
                        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet){
                            try {
                                $worksheetTitle     = $worksheet->getTitle();
                                $highestRow         = $worksheet->getHighestRow(); // e.g. 10
                                $highestColumn      = $worksheet->getHighestColumn(); // e.g 'F'
                                $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
                                $nrColumns = ord($highestColumn) - 64;
                            }catch (\Throwable $th) {
                                die($th);
                            }

                            // traitement
                            for ($i=2; $i <= $highestRow ; $i++){
                                try {
                                    $poste = $worksheet->getCellByColumnAndRow(0, $i)->getValue();
                                    $montant = $worksheet->getCellByColumnAndRow(1, $i)->getValue();
                                } catch (\Throwable $th) {
                                die($th);
                                }

                                // on recupere l'annee academique encours

                                $annee_acad = ConnexionBdd::Connecter()->query("SELECT * FROM annee_academique ORDER BY id DESC LIMIT 1");
                                $n_annee_acad = $annee_acad->rowCount();

                                if($n_annee_acad >= 1){
                                    $data_annee = $annee_acad->fetch();
                                    $annee_acad = $data_annee['annee_acad'];
                                    // verification 1 
                                    if(!empty($poste) && !empty($montant) && !empty($annee_acad)){
                                        $verif = ConnexionBdd::Connecter()->prepare("SELECT * FROM poste_depense WHERE  poste  = ? AND montant = ? AND annee_acad = ?");
                                        $verif->execute(array(strtolower($poste), $montant,  $annee_acad));

                                        $nbre = $verif->rowcount();
                                        if($nbre <= 0){
                                            // insertion de donnees dans la base de donnees 
                                            $insert_etud = ConnexionBdd::Connecter()->prepare("INSERT INTO poste_depense(poste, montant, annee_acad) VALUES(?,?,?)");
                                            $ok = $insert_etud->execute(array(strtolower($poste), $montant,  $annee_acad));
                                            if(!$ok){
                                                echo ("Donnees non insere <br>");
                                            }
                                        }else{
                                            echo "les donnees existe deja dans la base de donnees";
                                        }
                                    }else{
                                        echo 'certain champs sont vide.';
                                    }
                                }else{
                                    echo "Veuillez inserer les annee academique dans la base de donnees";
                                    header("500 Erreur interne du serveur", true, 500);
                                }
                            }
                            echo "Traitement reussi avec succes";
                        }
                    }else{
                        echo 'Veuillez charger le fichier Excel svp !!!';
                    }
                }else{
                    echo ("Veuillez charger le fichier de poste de depense; pas n importe quoi svp !!!.");
                }
            }else{
                echo "Veuillez charger le fichier Excel svp !!!...";
            }
        }else{
            die("Le fichier Excel n'est pas recu");
            header('500 Erreur interne du serveur', true, 500);
        }
    }else{
        echo 'Il parait qu il ya une erreur les donnees ne sont recu';
        header('500 Erreur interne du serveur', true, 500);
    }
?>