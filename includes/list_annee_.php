<?php
    require_once './ConnexionBdd.class.php';

    function verif_annee($v){
        $verif = ConnexionBdd::Connecter()->prepare("SELECT DISTINCT annee_academique FROM etudiants_inscrits WHERE annee_academique = ?");
        $verif->execute(array($v));
        if($verif->rowcount() >= 1){
            return 'disabled';
        }else{
            return $verif->rowcount();
        }
    }
    $verif = ConnexionBdd::Connecter()->query("SELECT * FROM annee_academique");
    while($data = $verif->fetch()){
        echo '
        <tr>
            <td id="id_annee_acad">'.$data['id'].'</td>
            <td id="annee_acad">'.$data['annee_acad'].'</td>
            <td>
                <button data-toggle="modal" href="#MyModalModif" id="modifier" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#MyModalModif" '.verif_annee($data['annee_acad']).'>Modifier</button>
            </td>
        </tr>';
    }
?>