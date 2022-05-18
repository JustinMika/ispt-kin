<?php
    require_once './ConnexionBdd.class.php';
    $an =  ConnexionBdd::Connecter()->query("SELECT * FROM annee_acad GROUP BY annee_acad ORDER BY id_annee DESC LIMIT 1");
    if($an->rowCount() > 0){
        $an_r = $an->fetch();
    }else{
        $an_r['annee_acad'] = '';
        die("Veuillez AJouter l annee académique");
    }

    $verif = ConnexionBdd::Connecter()->prepare("SELECT * FROM departement WHERE id_annee = ? ORDER BY departement ASC");
    $verif->execute(array($an_r['id_annee']));
    while($data = $verif->fetch()){
        echo '
        <tr>
            <td id="id_departement">'.$data['id_departement'].'</td>
            <td id="id_section" style="display:none">'.$data['id_section'].'</td>
            <td id="departement">'.$data['departement'].'</td>
            <td>
                <button href="#" data-toggle="modal" data-target="#update_depart_" class="btn btn-primary btn-sm" id="modif_depart">
                    <i class="fa fa-edit" aria-hidden="true"></i>
                </button>

                <button href="#" data-toggle="modal" data-target="#add_option" class="btn btn-primary btn-sm" id="add_option_option" title="Ajouter une option">
                    <i class="fa fa-plus-circle" aria-hidden="true"></i>
                </button>

                <button href="#" class="btn btn-info btn-sm" id="list_option_option" title="Ajouter une option">
                    <i class="fa fa-list" aria-hidden="true"></i>
                </button>
            </td>
        </tr>';
    }
?>