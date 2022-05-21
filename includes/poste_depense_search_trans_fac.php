<?php
    session_start();
    /**
     * la fonction pour afficher/cacher le btn pour faire les transaction.
     */
    function trans($v1, $v2){
        if(intval($v1) - intval($v2) == 0){
            return '';
        }else{
            return '';
        }
    }
    require_once './ConnexionBdd.class.php';
    require_once './verification.class.php';
    if(isset($_GET['data'])){
        $sql = "SELECT
                    depense_facultaire.id_pdf,
                    depense_facultaire.poste,
                    depense_facultaire.montant,
                    depense_facultaire.depense,
                    sections.id_section,
                    sections.section,
                    annee_acad.id_annee,
                    annee_acad.annee_acad
                FROM
                    depense_facultaire
                LEFT JOIN sections ON depense_facultaire.id_section = sections.id_section
                LEFT JOIN annee_acad ON depense_facultaire.id_annee = annee_acad.id_annee
                WHERE
                    sections.section = '".$_SESSION['data']['access']."' AND annee_acad.id_annee = 1";
        $req = ConnexionBdd::Connecter()->query($sql);
        if($req->rowCount() > 0){
            while($data = $req->fetch()){
                echo '
                    <tr>
                        <td id="id_poste_dep">'.$data['id'].'</td>
                        <td id="post">'.$data['poste'].'</td>
                        <td id="m_fac">'.$data['faculte'].'</td>
                        
                        <td id="montant">'.$data['montant'].'$'.'</td>
                        <td id="m_depense">'.$data['depense'].'</td>
                        <td>'.montant_restant_pourcent($data['depense'], $data['montant']).'%
                        </td>
                        <td>
                            <div class="progress">
                                <div class="progress-bar text-white" role="progressbar" style="width:'.montant_restant_pourcent($data['depense'],$data['montant']).'%;"
                                    aria-valuenow="'.montant_restant($data['montant'], $data['depense']).'" aria-valuemin="0" aria-valuemax="'.$data['montant'].'">'.montant_restant_pourcent($data['depense'], $data['montant']).'%
                                </div>
                            </div>
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
            die("Aucun resultat trouvé, ...");
        }
    }   
?>