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
    if(isset($_GET['p']) && !empty($_GET['p']) && $_GET['p'] == "search"){
        if(!empty($_GET['poste']) && !empty($_GET['annee_acad']) && !empty($_GET['pourcent'])){
            if($_GET['poste'] == "All" && $_GET['ch_sh'] == "true" && !empty($_GET['pourcent'])){
                // on vide les sessions pour les transactions des postes
                $_SESSION['req_rapport_trans'] = "";
                $_SESSION['params_trans'] = array();
                
                $pr = intval($_GET['pourcent']);
                $_SESSION['req_rapport'] = "SELECT * FROM depense_facultaire WHERE (montant-depense)*100/montant >= ? AND annee_acad = ?";
                $_SESSION['params'] = array($_GET['pourcent'], $_GET['annee_acad']);

                $req = ConnexionBdd::Connecter()->prepare("SELECT * FROM depense_facultaire WHERE (montant-depense)*100/montant >= ? AND annee_acad = ?");
                $req->execute(array($_GET['pourcent'], $_GET['annee_acad']));
                while($data = $req->fetch()){
                    // if(intval($_GET['pourcent']) < intval(montant_restant_pourcent(montant_restant($data['montant'], $data['depense']), $data['montant']))){
                    echo '
                        <tr>
                            <td>'.$data['id'].'</td>
                            <td id="post">'.$data['poste'].'</td>
                            <td id="montant">'.$data['montant'].'$'.'</td>
                            <td id="m_depense">'.$data['depense'].'$'.'</td>
                            <td id="m_depense">'.$data['faculte'].'</td>
                            <td id="m_depense">'.$data['promotion'].'</td>
                            <td id="m_restant">'.montant_restant($data['montant'], $data['depense']).'$</td>
                            <td>'.montant_restant_pourcent(montant_restant($data['montant'], $data['depense']), $data['montant']).'%
                            </td>
                            <td>
                                <div class="progress">
                                    <div class="progress-bar text-white" role="progressbar" style="width:'.montant_restant_pourcent(montant_restant($data['montant'], $data['depense']), $data['montant']).'%;"
                                        aria-valuenow="'.montant_restant($data['montant'], $data['depense']).'" aria-valuemin="0" aria-valuemax="'.$data['montant'].'">'.montant_restant_pourcent(montant_restant($data['montant'], $data['depense']), $data['montant']).'%
                                    </div>
                                </div>
                            </td>
                            <td>
                                <button class="btn btn-primary btn-sm" id="btn_transaction" data-toggle="modal" data-target="#transactions" style="">Transaction</button>
                            </td>
                        </tr>
                        ';
                }
            }else{
                all();
            }
        }else if(!empty($_GET['poste'])){
            if($_GET['poste'] == "All" && $_GET['ch_sh'] == "false"){
                // on vide les sessions pour les transactions des postes
                $_SESSION['req_rapport_trans'] = "";
                $_SESSION['params_trans'] = array();
                all();
            }else if($_GET['poste'] != "All" && $_GET['ch_sh'] == "false"){
                $req = ConnexionBdd::Connecter()->prepare("SELECT * FROM depense_facultaire WHERE poste  = ? AND annee_acad = ?");
                $req->execute(array($_GET['poste'], $_GET['annee_acad']));
                // on vide les sessions pour les transactions sur le poste de depenses
                $_SESSION['req_rapport'] = "SELECT * FROM depense_facultaire";
                $_SESSION['params_trans'] = array();

                $_SESSION['req_rapport'] = "SELECT * FROM depense_facultaire WHERE poste  = ? AND annee_acad = ?";
                $_SESSION['params'] = array($_GET['poste'], $_GET['annee_acad']);
                
                while($data = $req->fetch()){
                    echo '
                        <tr>
                            <td>'.$data['id'].'</td>
                            <td id="post">'.$data['poste'].'</td>
                            <td id="montant">'.$data['montant'].'$'.'</td>
                            <td id="m_depense">'.$data['depense'].'</td>
                            <td id="m_restant">'.montant_restant($data['montant'], $data['depense']).'$</td>
                            <td>'.montant_restant_pourcent($data['depense'], $data['montant']).'%
                            </td>
                            <td>
                                <div class="progress">
                                    <div class="progress-bar text-white" role="progressbar" style="width:'.montant_restant_pourcent($data['depense'], $data['montant']).'%;"
                                        aria-valuenow="'.montant_restant($data['montant'], $data['depense']).'" aria-valuemin="0" aria-valuemax="'.$data['montant'].'">'.montant_restant_pourcent($data['depense'], $data['montant']).'%
                                    </div>
                                </div>
                            </td>
                            <td>
                                <button class="btn btn-primary btn-sm" id="btn_transaction" data-toggle="modal" data-target="#transactions" style="">Transaction</button>
                            </td>
                        </tr>
                    ';
                }
            }else{

            }
        }
    }else if(isset($_GET['data'])){
        $req = ConnexionBdd::Connecter()->query("SELECT * FROM depense_facultaire WHERE faculte = '".$_SESSION['data']['access']."'");
        // on vide les sessions pour les transactions des postes
        $_SESSION['req_rapport_trans'] = "";
        $_SESSION['params_trans'] = array();

        $_SESSION['req_rapport'] = "SELECT * FROM depense_facultaire WHERE faculte = '".$_SESSION['data']['access']."'";
        $_SESSION['params_trans'] = array();
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
    }

    function all(){
        $req = ConnexionBdd::Connecter()->query("SELECT * FROM depense_facultaire");
        $_SESSION['req_rapport_trans'] = "";
        $_SESSION['params_trans'] = array();

        $_SESSION['req_rapport'] = "SELECT * FROM depense_facultaire";
        $_SESSION['params'] = array();
        while($data = $req->fetch()){
            echo '
                <tr>
                    <td id="id_">'.$data['id'].'</td>
                    <td id="post">'.$data['poste'].'</td>
                    <td id="m_fac">'.$data['faculte'].'</td>
                    
                    <td id="montant">'.$data['montant'].'$'.'</td>
                    <td id="m_depense">'.$data['depense'].'</td>
                    <td id="m_restant">'.montant_restant($data['montant'], $data['depense']).'$</td>
                    <td>'.montant_restant_pourcent(montant_restant($data['montant'], $data['depense']), $data['montant']).'%
                    </td>
                    <td>
                        <div class="progress">
                            <div class="progress-bar text-white" role="progressbar" style="width:'.montant_restant_pourcent(montant_restant($data['montant'], $data['depense']), $data['montant']).'%;"
                                aria-valuenow="'.montant_restant($data['montant'], $data['depense']).'" aria-valuemin="0" aria-valuemax="'.$data['montant'].'">'.montant_restant_pourcent(montant_restant($data['montant'], $data['depense']), $data['montant']).'%
                            </div>
                        </div>
                    </td>
                    <td>
                        <button class="btn btn-primary btn-sm" id="btn_transaction" data-toggle="modal" data-target="#transactions" style="">Transaction</button>
                    </td>
                </tr>
            ';
        }
    }
?>