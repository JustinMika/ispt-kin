<?php
    session_start();
    /**
     * la fonction pour afficher/cacher le btn pour faire les transaction.
     */
    require_once './ConnexionBdd.class.php';
    if(isset($_GET['data'])){
        // on selectionne le dernier annee academique
        $an =  ConnexionBdd::Connecter()->query("SELECT * FROM annee_acad GROUP BY annee_acad ORDER BY id_annee DESC LIMIT 1");
        if($an->rowCount() > 0){
            $an_r = $an->fetch();
        }else{
            $an_r['id_annee'] = '';
            die("Veuillez AJouter l annee academique");
        }
        $req = ConnexionBdd::Connecter()->prepare("SELECT * FROM poste_depense WHERE id_annee = ?");
        $req->execute(array($an_r['id_annee']));
        // on vide les sessions pour les transactions des postes
        $_SESSION['req_rapport_trans'] = "";
        $_SESSION['params_trans'] = array();

        $_SESSION['req_rapport'] = "SELECT * FROM poste_depense";
        $_SESSION['params_trans'] = array();
        while($data = $req->fetch()){
            echo '
                <tr>
                    <td id="id_poste_dep">'.$data['id_poste'].'</td>
                    <td id="post">'.$data['poste'].'</td>
                    <td id="montant">'.$data['montant'].'$'.'</td>
                    <td id="m_depense">'.$data['depense'].'</td>
                    <td id="m_restant">'.intval($data['montant'] - $data['depense']).'$</td>
                    <td>'.montant_restant_pourcent($data['depense'], $data['montant']).'%
                    </td>
                    <td>
                        <button class="btn btn-primary btn-sm" id="btn_transaction" data-toggle="modal" data-target="#transactions" style="">Transaction</button>

                        <button title="supprimer" class="btn btn-danger btn-sm" id="btn_del_poste" data-toggle="modal" data-target="#del_poste" style=""><i class="fa fa-recycle" aria-hidden="true"></i></button>   

                        <button title="Modifier le montant" class="btn btn-primary btn-sm" id="btn_del_poste" data-toggle="modal" data-target="#update_poste_montant" style=""><i class="fa fa-edit" aria-hidden="true"></i></button>
                    </td>
                </tr>
            ';
        }
    }else{
        echo '';
    }
?>