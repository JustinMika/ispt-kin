<?php
    require_once './ConnexionBdd.class.php';
    $an =  ConnexionBdd::Connecter()->query("SELECT * FROM annee_acad GROUP BY annee_acad ORDER BY id_annee DESC LIMIT 1");
    if($an->rowCount() > 0){
        $an_r = $an->fetch();
    }else{
        $an_r['annee_acad'] = '';
        die("Veuillez AJouter l annee académique");
    }

    $verif = ConnexionBdd::Connecter()->prepare("SELECT * FROM sections WHERE id_annee = ? ORDER BY section ASC");
    $verif->execute(array($an_r['id_annee']));
    while($data = $verif->fetch()){
        echo '
        <tr>
            <td id="id_fac_list">'.$data['id_section'].'</td>
            <td id="fac_list">'.$data['section'].'</td>
            <td>
                <button href="#" data-toggle="modal" data-target="#Modify_fac" class="btn btn-primary btn-sm" id="modif_fac_l">Modifier</button>
            </td>
        </tr>';
    }
?>