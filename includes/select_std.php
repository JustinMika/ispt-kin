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
        }else if($_GET['list_frais']){
            $id_section = $_GET['a'];
            $id_departement = $_GET['b'];
            $id_option = $_GET['c'];
            $mat = $_GET['d'];
            $annee = $_GET['e'];

            $sql = "SELECT
                        affectation_frais.id_frais,
                        prevision_frais.type_frais
                    FROM
                        affectation_frais
                    LEFT JOIN prevision_frais ON affectation_frais.id_frais = prevision_frais.id_frais
                    WHERE
                        affectation_frais.matricule = ? AND affectation_frais.id_section = ? AND affectation_frais.id_departement = ? AND affectation_frais.id_option = ? AND affectation_frais.id_annee = ?";
            $p = array($mat, $id_section, $id_departement, $id_option, $annee);
            $req = ConnexionBdd::Connecter()->prepare($sql);
            $req->execute($p);
            if($req->rowCount() > 0){
                while($data = $req->fetch()){
                    ?>
                        <option value="<?=$data['id_frais']?>"><?=$data['type_frais']?></option>
                    <?php
                }
            }else{
                die("Aucun frais affecter a l'étudiant(e)");
            }
        }
    }
?>