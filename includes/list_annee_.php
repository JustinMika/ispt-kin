<?php
    require_once './ConnexionBdd.class.php';

    $verif = ConnexionBdd::Connecter()->query("SELECT * FROM annee_acad");
    while($data = $verif->fetch()){
        echo '
        <tr>
            <td id="id_annee_acad">'.$data['id_annee'].'</td>
            <td id="annee_acad">'.$data['annee_acad'].'</td>
            <td>
                <button data-toggle="modal" href="#MyModalModif" id="modifier" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#MyModalModif">Modifier</button>
            </td>
        </tr>
        ';
    }
?>