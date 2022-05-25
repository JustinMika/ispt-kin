<?php
    require_once './ConnexionBdd.class.php';
    // print_r($_GET);
    if(isset($_GET['get_code']) && !empty($_GET['get_code'])){
        if(isset($_GET['a']) && !empty($_GET['a'])){
            if(isset($_GET['f']) && !empty($_GET['f'])){
                $sel_fac = ConnexionBdd::Connecter()->prepare("SELECT * FROM etudiants_inscrits WHERE matricule  =? AND id_annee = ?");
                $sel_fac->execute(array($_GET['a'], $_GET['f']));
                if($sel_fac->rowCount() > 0){
                    $d = $sel_fac->fetch();
                    $data = array(
                        "id_section" => $d['id_section'],
                        "id_departement" => $d['id_departement'],
                        "id_option" => $d['id_option'],
                        "noms" => $d['noms'],
                    );
                    echo json_encode($data);
                }else{
                    die("");
                }
            }
        }
    } else if(isset($_GET['list_frais']) && $_GET['list_frais'] == "list_frais"){
            // print_r($_GET);
            $id_section = $_GET['a'];
            $id_departement = $_GET['b'];
            $id_option = $_GET['c'];
            $mat = $_GET['d'];
            $annee = $_GET['e'];

            $sql = "SELECT
                        affectation_frais.id,
                        affectation_frais.id_section,
                        affectation_frais.matricule,
                        affectation_frais.id_departement,
                        affectation_frais.id_option,
                        affectation_frais.id_frais,
                        prevision_frais.id_frais,
                        prevision_frais.type_frais,
                        prevision_frais.montant
                    FROM
                        affectation_frais
                    LEFT JOIN prevision_frais ON affectation_frais.id_frais = prevision_frais.id_frais
                    WHERE
                        affectation_frais.matricule = ? AND affectation_frais.id_annee = ?";
            $p = array($mat, $annee);
            // print_r($p);
            $req = ConnexionBdd::Connecter()->prepare($sql);
            $req->execute($p);
            if($req->rowCount() > 0){
                while($data = $req->fetch()){
                    echo '
                        <option value="'.$data['id_frais'].'">'.utf8_decode($data['type_frais']).'</option>';
                }
            }else{
                die("");
            }
        }else{
            die("");
        }
?>