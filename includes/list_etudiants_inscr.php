<?php
    require_once './ConnexionBdd.class.php';
    // on recupere le dernier annee academique
    $a = ConnexionBdd::Connecter()->query("SELECT id_annee FROM annee_acad ORDER BY id_annee DESC LIMIT 0,1");
    if($a->rowCount() > 0){
        $data = $a->fetch();
    }else{
        $data['id_annee'] = '';
    }

    $verif = ConnexionBdd::Connecter()->query("SELECT etudiants_inscrits.id, etudiants_inscrits.matricule, etudiants_inscrits.noms, sections.section as fac, departement.departement, options.option_ as option, options.promotion, annee_acad.annee_acad as annee_academique  FROM etudiants_inscrits 
    LEFT JOIN sections ON etudiants_inscrits.id_section = sections.id_section 
    LEFT JOIN departement ON etudiants_inscrits.id_departement = departement.id_departement 
    LEFT JOIN options ON etudiants_inscrits.id_option = options.id_option 
    LEFT JOIN annee_acad ON etudiants_inscrits.id_annee = annee_acad.id_annee WHERE etudiants_inscrits.id_annee = '{$data['id_annee']}'");
    while($data = $verif->fetch()){
        echo '
            <tr>
                <td id="insc_id" style="display:none">'.$data['id'].'</td>
                <td id="insc_mat">'.$data['matricule'].'</td>
                <td id="insc_noms">'. utf8_decode($data['noms']) .'</td>
                <td id="insc_fac">'.$data['fac'].'</td>
                <td id="insc_promotion">'.$data['option'].'</td>
                <td id="insc_promotion">'.$data['promotion'].'</td>
                <td id="insc_annee_acad">'.$data['annee_academique'].'</td>
                <td>
                    <div class="btn-group">
                        <button class="btn btn-primary btn-sm" title="editer" id="btn_update_stud"><i class="fa fa-edit" aria-hidden="true" data-toggle="modal" data-target="#update_student"></i></button>

                        <button class="btn btn-primary btn-sm" title="modifier le mot de passe" id="btn_update_pwd_stud">
                        <i class="fa fa-key" aria-hidden="true" data-toggle="modal" data-target="#update_student_pwd"></i></button>

                        <button class="btn btn-danger btn-sm" title="supprimer" id="btn_del_student" data-toggle="modal" data-target="#del_student"><i class="fa fa-cut" aria-hidden="true"></i></button>
                    </div>
                </td>
            </tr>';
    }
?>