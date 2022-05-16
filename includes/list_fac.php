<?php
    require_once './ConnexionBdd.class.php';
    function verif_annee($v, $v2){
        $verif = ConnexionBdd::Connecter()->prepare("SELECT DISTINCT fac FROM etudiants_inscrits WHERE fac = ? AND annee_academique = ?");
        $verif->execute(array($v, $v2));
        if($verif->rowcount() >= 1){
            return 'disabled title="impossible de modifier la faculte"';
        }else{
            return 'title="modifier la faculte"';
        }
    }

    $an =  ConnexionBdd::Connecter()->query("SELECT * FROM annee_academique GROUP BY annee_acad ORDER BY id DESC LIMIT 1");
    if($an->rowCount() > 0){
        $an_r = $an->fetch();
    }else{
        $an_r['annee_acad'] = '';
        die("Veuillez AJouter l annee academique");
    }

    $verif = ConnexionBdd::Connecter()->prepare("SELECT * FROM faculte WHERE annee_acad = ? ORDER BY fac ASC");
    $verif->execute(array($an_r['annee_acad']));
    while($data = $verif->fetch()){
        echo '
        <tr>
            <td id="id_fac_list">'.$data['id'].'</td>
            <td id="fac_list">'.$data['fac'].'</td>
            <td>
                <button href="#" data-toggle="modal" data-target="#Modify_fac" class="btn btn-primary btn-sm" id="modif_fac_l" '.verif_annee($data['fac'], $data['annee_acad']).'>Modifier</button>
            </td>
        </tr>';
    }
?>