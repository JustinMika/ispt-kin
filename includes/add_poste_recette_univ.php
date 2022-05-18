<?php
    session_start();
    require_once './ConnexionBdd.class.php';

    if(isset($_POST['poste_recette_univ_']) && !empty($_POST['poste_recette_univ_'])){
        if(isset($_POST['montant_poste_univ']) && !empty($_POST['montant_poste_univ'])){
            // on selectionne le dernier annee academique dnas la base de donnees
            $an =  ConnexionBdd::Connecter()->query("SELECT * FROM annee_acad GROUP BY annee_acad ORDER BY id_annee DESC LIMIT 1");
            if($an->rowCount() > 0){
                $an_r = $an->fetch();
            }else{
                $an_r['id_annee'] = '';
                // die
                die("Veuillez AJouter l annee academique");
            }

            $verify = ConnexionBdd::Connecter()->prepare("SELECT * FROM poste_recette WHERE poste_rec  = ? AND id_annee = ?");
            $verify->execute(array($_POST['poste_recette_univ_'], $an_r['id_annee']));
            $n = $verify->rowCount();

            if($n <= 0){
                // on selectionne le dernier annee academique dnas la base de donnees
                $an =  ConnexionBdd::Connecter()->query("SELECT * FROM annee_acad GROUP BY annee_acad ORDER BY id_annee DESC LIMIT 1");
                if($an->rowCount() > 0){
                    $an_r = $an->fetch();
                }else{
                    $an_r['id_annee'] = '';
                    // die
                    die("Veuillez AJouter l annee academique");
                }

                $insert = ConnexionBdd::Connecter()->prepare("INSERT INTO poste_recette(poste_rec, montant, id_annee) VALUES(?, ?, ?)");
                $ok = $insert->execute(array(htmlspecialchars($_POST['poste_recette_univ_']), $_POST['montant_poste_univ'], $an_r['id_annee']));
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