<?php
    session_start();
    /**
     * la fonction pour afficher/cacher le btn pour faire les transaction.
     */
    function trans($v1, $v2){
        if(intval($v1) - intval($v2) == 0){
            return 'display:none';
        }else{
            return '';
        }
    }
    require_once './ConnexionBdd.class.php';
    if(isset($_GET['p']) && !empty($_GET['p']) && $_GET['p'] == "search"){
        if(!empty($_GET['poste']) && !empty($_GET['annee_acad']) && !empty($_GET['pourcent'])){
            if($_GET['poste'] == "All" && $_GET['ch_sh'] == "true" && !empty($_GET['pourcent'])){
                // on vide les sessions pour les transactions des postes
                $_SESSION['req_rapport_trans'] = "";
                $_SESSION['params_trans'] = array();
                
                $pr = intval($_GET['pourcent']);
                $_SESSION['req_rapport'] = "SELECT * FROM poste_depense WHERE (montant-depense)*100/montant >= ? AND annee_acad = ?";
                $_SESSION['params'] = array($_GET['pourcent'], $_GET['annee_acad']);

                $req = ConnexionBdd::Connecter()->prepare("SELECT * FROM poste_depense WHERE (montant-depense)*100/montant >= ? AND annee_acad = ?");
                $req->execute(array($_GET['pourcent'], $_GET['annee_acad']));
                while($data = $req->fetch()){
                    // if(intval($_GET['pourcent']) < intval(montant_restant_pourcent(montant_restant($data['montant'], $data['depense']), $data['montant']))){
                    echo '
                        <tr>
                            <td>'.$data['id'].'</td>
                            <td id="post">'.$data['poste'].'</td>
                            <td id="montant">'.$data['montant'].'$'.'</td>
                            <td id="m_depense">'.$data['depense'].'$'.'</td>
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
                                <button class="btn btn-primary btn-sm" id="btn_transaction" data-toggle="modal" data-target="#transactions" style="'.trans($data['montant'], $data['depense']).'">Transaction</button>
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
                $req = ConnexionBdd::Connecter()->prepare("SELECT * FROM poste_depense WHERE poste  = ? AND annee_acad = ?");
                $req->execute(array($_GET['poste'], $_GET['annee_acad']));
                // on vide les sessions pour les transactions sur le poste de depenses
                $_SESSION['req_rapport'] = "SELECT * FROM poste_depense";
                $_SESSION['params_trans'] = array();

                $_SESSION['req_rapport'] = "SELECT * FROM poste_depense WHERE poste  = ? AND annee_acad = ?";
                $_SESSION['params'] = array($_GET['poste'], $_GET['annee_acad']);
                
                while($data = $req->fetch()){
                    echo '
                        <tr>
                            <td>'.$data['id'].'</td>
                            <td id="post">'.$data['poste'].'</td>
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
                                <button class="btn btn-primary btn-sm" id="btn_transaction" data-toggle="modal" data-target="#transactions" style="'.trans($data['montant'], $data['depense']).'">Transaction</button>
                            </td>
                        </tr>
                    ';
                }
            }else{

            }
        }
    }else if(isset($_GET['data'])){
        $req = ConnexionBdd::Connecter()->query("SELECT * FROM poste_depense");
        // on vide les sessions pour les transactions des postes
        $_SESSION['req_rapport_trans'] = "";
        $_SESSION['params_trans'] = array();

        $_SESSION['req_rapport'] = "SELECT * FROM poste_depense";
        $_SESSION['params_trans'] = array();
        while($data = $req->fetch()){
            echo '
                <tr>
                    <td>'.$data['id'].'</td>
                    <td id="post">'.$data['poste'].'</td>
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
                        <button class="btn btn-primary btn-sm" id="btn_transaction" data-toggle="modal" data-target="#transactions" style="'.trans($data['montant'], $data['depense']).'">Transaction</button>
                    </td>
                </tr>
            ';
        }
    }else{
        echo '';
    }

    if(isset($_POST['search_poste']) && !empty($_POST['search_poste'])){
        //annee_acad date_2 date_1 poste
        if(!empty($_POST['annee_acad']) && !empty($_POST['date_2']) && !empty($_POST['date_1']) && !empty($_POST['poste'])){
            if($_POST['poste'] == "All"){
                $str_req = "SELECT * FROM transaction_depense WHERE date_t BETWEEN ? AND ?";
                $params_req = array($_POST['date_1'], $_POST['date_2']);
                $req_searc = ConnexionBdd::Connecter()->prepare($str_req);
                $_SESSION['req_rapport_trans'] = $str_req;
                $_SESSION['params_trans'] = $params_req;
                // on vide les sessions pour les postes
                $_SESSION['req_rapport'] = "";
                $_SESSION['params'] = array();

                $req_searc->execute($params_req);
                $n = $req_searc->rowCount();
                if($n >= 1){
                    while($data = $req_searc->fetch()){
                        echo '
                        <tr>
                            <td>'.$data['id'].'</td>
                            <td>'.$data['poste'].'</td>
                            <td>'.date("d/m/Y", strtotime($data['date_t'])).'</td>
                            <td>'.$data['montant'].'$'.'</td>
                            <td>'.$data['motif'].'</td>
                        </tr>';
                    }
                }else{
                    echo "Aucun resultat trouver";
                }
                $response = $req_searc->fetch();
                print_r($response);
            }else{
                $str_req = "SELECT * FROM transaction_depense WHERE poste = ? AND date_t BETWEEN ? AND ?";
                $params_req = array($_POST['poste'], $_POST['date_1'], $_POST['date_2']);
                $req_searc = ConnexionBdd::Connecter()->prepare($str_req);
                $_SESSION['req_rapport_trans'] = $str_req;
                $_SESSION['params_trans'] = $params_req;
                $req_searc->execute($params_req);
                $n = $req_searc->rowCount();
                if($n >= 1){
                    while($data = $req_searc->fetch()){
                        echo '
                        <tr>
                            <td>'.$data['id'].'</td>
                            <td>'.$data['poste'].'</td>
                            <td>'.date("d/m/Y", strtotime($data['date_t'])).'</td>
                            <td>'.$data['montant'].'$'.'</td>
                            <td>'.$data['motif'].'</td>
                        </tr>';
                    }
                }else{
                    echo "Aucun resultat trouver";
                }
            }
        }//else{
        //     echo '';
        // }
    }

    function all(){
        $req = ConnexionBdd::Connecter()->query("SELECT * FROM poste_depense");
        $_SESSION['req_rapport_trans'] = "";
        $_SESSION['params_trans'] = array();

        $_SESSION['req_rapport'] = "SELECT * FROM poste_depense";
        $_SESSION['params'] = array();
        while($data = $req->fetch()){
            echo '
                <tr>
                    <td>'.$data['id'].'</td>
                    <td id="post">'.$data['poste'].'</td>
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
                        <button class="btn btn-primary btn-sm" id="btn_transaction" data-toggle="modal" data-target="#transactions" style="'.trans($data['montant'], $data['depense']).'">Transaction</button>
                    </td>
                </tr>
            ';
        }
    }
?>