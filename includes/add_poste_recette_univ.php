<?php
    session_start();
    require_once './ConnexionBdd.class.php';

    if(isset($_POST['poste_recette_univ_']) && !empty($_POST['poste_recette_univ_'])){
        if(isset($_POST['montant_poste_univ']) && !empty($_POST['montant_poste_univ'])){
            // on selectionne le dernier annee academique dnas la base de donnees
            $an =  ConnexionBdd::Connecter()->query("SELECT * FROM annee_academique GROUP BY annee_acad ORDER BY id DESC LIMIT 1");
            if($an->rowCount() > 0){
                $an_r = $an->fetch();
            }else{
                $an_r['annee_acad'] = '';
                // die
                die("Veuillez AJouter l annee academique");
            }

            $verify = ConnexionBdd::Connecter()->prepare("SELECT * FROM previson_frais_univ WHERE poste  = ? AND annee_acad = ?");
            $verify->execute(array($_POST['poste_recette_univ_'], $an_r['annee_acad']));
            $n = $verify->rowCount();

            if($n <= 0){
                // on selectionne le dernier annee academique dnas la base de donnees
                $an =  ConnexionBdd::Connecter()->query("SELECT * FROM annee_academique GROUP BY annee_acad ORDER BY id DESC LIMIT 1");
                if($an->rowCount() > 0){
                    $an_r = $an->fetch();
                }else{
                    $an_r['annee_acad'] = '';
                    die("Veuillez AJouter l annee academique");
                }

                $insert = ConnexionBdd::Connecter()->prepare("INSERT INTO previson_frais_univ(poste, annee_acad, montant) VALUES(?, ?, ?)");
                $ok = $insert->execute(array(htmlspecialchars($_POST['poste_recette_univ_']), $an_r['annee_acad'], $_POST['montant_poste_univ']));
                if($ok){
                    echo "insertion reussi avec succes";
                }else{
                    echo 'une erreur est survenue...; les donnees ne sont inserer dans la base de donnees.';
                }
            }else{
                die("ERROR:le poste de recette {$_POST['poste_recette_univ_']} existe deja dans la base de donnees.");
            }
        }else{
            die("ERROR:veuillez renseigner le montant le poste de recette universitaire");
        }
    }else{
        die('ERROR:veuillez renseinger un poste de recette');
    }
?>