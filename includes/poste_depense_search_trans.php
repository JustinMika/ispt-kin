<?php
    session_start();
    /**
     * la fonction pour afficher/cacher le btn pour faire les transaction.
     */
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

                $req = ConnexionBdd::Connecter()->prepare("SELECT * FROM poste_depense WHERE (depense)*100/montant >= ? AND annee_acad = ?");
                $req->execute(array($_GET['pourcent'], $_GET['annee_acad']));
                while($data = $req->fetch()){
                    echo '
                        <tr>
                            <td id="id_poste_dep">'.$data['id'].'</td>
                            <td id="post">'.$data['poste'].'</td>
                            <td id="montant">'.$data['montant'].'$'.'</td>
                            <td id="m_depense">'.$data['depense'].'$'.'</td>
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
                all();
            }
        }else if(!empty($_GET['poste'])){
            if($_GET['poste'] == "All" && $_GET['ch_sh'] == "false"){
                // on vide les sessions pour les transactions des postes
                $_SESSION['req_rapport_trans'] = "";
                $_SESSION['params_trans'] = array();
                all($_GET['annee_acad']);
            }else if($_GET['poste'] != "All" && $_GET['ch_sh'] == "false"){
                $req = ConnexionBdd::Connecter()->prepare("SELECT * FROM poste_depense WHERE poste  = ? AND annee_acad = ?");
                $req->execute(array($_GET['poste'], $_GET['annee_acad']));
                // on vide les sessions pour les transactions sur le poste de depenses
                $_SESSION['req_rapport'] = "SELECT * FROM poste_depense";
                $_SESSION['params_trans'] = array();

                $_SESSION['req_rapport'] = "SELECT * FROM poste_depense WHERE poste = ? AND annee_acad = ?";
                $_SESSION['params'] = array($_GET['poste'], $_GET['annee_acad']);
                
                while($data = $req->fetch()){
                    echo '
                        <tr>
                            <td id="id_poste_dep">'.$data['id'].'</td>
                            <td id="post">'.$data['poste'].'</td>
                            <td id="montant">'.$data['montant'].'$'.'</td>
                            <td id="m_depense">'.intval($data['montant'] - $data['depense']).'</td>
                            <td id="m_restant">'.$data['depense'].'$</td>
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

            }
        }
    }else if(isset($_GET['data'])){
        // on selectionne le dernier annee academique
        $an =  ConnexionBdd::Connecter()->query("SELECT * FROM annee_academique GROUP BY annee_acad ORDER BY id DESC LIMIT 1");
        if($an->rowCount() > 0){
            $an_r = $an->fetch();
        }else{
            $an_r['annee_acad'] = '';
            die("Veuillez AJouter l annee academique");
        }
        $req = ConnexionBdd::Connecter()->prepare("SELECT * FROM poste_depense WHERE annee_acad = ?");
        $req->execute(array($an_r['annee_acad']));
        // on vide les sessions pour les transactions des postes
        $_SESSION['req_rapport_trans'] = "";
        $_SESSION['params_trans'] = array();

        $_SESSION['req_rapport'] = "SELECT * FROM poste_depense";
        $_SESSION['params_trans'] = array();
        while($data = $req->fetch()){
            echo '
                <tr>
                    <td id="id_poste_dep">'.$data['id'].'</td>
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

    if(isset($_POST['search_poste']) && !empty($_POST['search_poste'])){
        //annee_acad date_2 date_1 poste
        if(!empty($_POST['annee_acad']) && !empty($_POST['date_2']) && !empty($_POST['date_1']) && !empty($_POST['poste'])){
            if($_POST['poste'] == "All"){
                $str_req = "SELECT id, poste, SUM(montant) AS montant FROM transaction_depense WHERE date_t BETWEEN ? AND ? GROUP BY poste";
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
                            <td id="id_poste_dep">'.$data['id'].'</td>
                            <td id="post">'.$data['poste'].'</td>
                            <td>'.$data['montant'].'$'.'</td>
                        </tr>';
                    }
                }else{
                    echo "Aucun resultat trouver";
                }
                $response = $req_searc->fetch();
                // print_r($response);
            }else{
                $str_req = "SELECT poste, SUM(montant) AS montant FROM transaction_depense WHERE poste = ? AND date_t BETWEEN ? AND ?";
                $params_req = array($_POST['poste'], $_POST['date_1'], $_POST['date_2']);
                $req_searc = ConnexionBdd::Connecter()->prepare($str_req);
                $_SESSION['req_rapport_trans_1'] = $str_req;
                $_SESSION['params_trans_1'] = $params_req;
                $req_searc->execute($params_req);
                $n = $req_searc->rowCount();
                if($n >= 1){
                    while($data = $req_searc->fetch()){
                        echo '
                        <tr>
                            <td id="id_poste_dep">'.$data['id'].'</td>
                            <td id="post">'.$data['poste'].'</td>
                            <td>'.date("d/m/Y", strtotime($data['date_t'])).'</td>
                            <td>'.$data['montant'].'$'.'</td>
                            <td>'.$data['motif'].'</td>
                        </tr>';
                    }
                }else{
                    echo "Aucun resultat trouver";
                }
            }
        }
    }

    function all(){
        // on selectionne le dernier annee acad
        $an =  ConnexionBdd::Connecter()->query("SELECT * FROM annee_academique GROUP BY annee_acad ORDER BY id DESC LIMIT 1");
        if($an->rowCount() > 0){
            $an_r = $an->fetch();
        }else{
            $an_r['annee_acad'] = '';
            die("Veuillez AJouter l annee academique");
        }
        $req = ConnexionBdd::Connecter()->prepare("SELECT * FROM poste_depense WHERE annee_acad = ?");
        $req->execute(array($an_r['annee_acad']));
        
        $_SESSION['req_rapport_trans'] = "";
        $_SESSION['params_trans'] = array();

        $_SESSION['req_rapport'] = "SELECT * FROM poste_depense WHERE annee_acad = ?";
        $_SESSION['params'] = array($an_r['annee_acad']);
        while($data = $req->fetch()){
            echo '
                <tr>
                    <td id="id_poste_dep">'.$data['id'].'</td>
                    <td id="post">'.$data['poste'].'</td>
                    <td id="montant">'.$data['montant'].'$'.'</td>
                    <td id="m_depense">'.$data['depense'].'</td>
                    <td id="m_restant">$</td>
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
    }
?>