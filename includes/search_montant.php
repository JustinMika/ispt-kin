<?php
    session_start();
    require_once './ConnexionBdd.class.php';

    if(isset($_POST['annee_acad_search_s']) && !empty($_POST['annee_acad_search_s'])){
        if(isset($_POST['poste_s']) && !empty($_POST['poste_s'])){
            if($_POST['poste_s'] == "All"){
                // print_r($_POST);
                if(isset($_POST['ch_ch_date']) && !empty($_POST['ch_ch_date'])){
                    if(isset($_POST['ch_m_min']) && !empty($_POST['ch_m_min']) && isset($_POST['montant_minimum']) && !empty($_POST['montant_minimum'])){
                        // tout est definie
                        $sql_all = "SELECT type_frais, SUM(montant) as mt FROM affectation_frais WHERE annee_acad = '{}' GROUP BY type_frais";
                        $sql_all_array = array();

                        $sql = "SELECT SUM(montant) AS montant, type_frais FROM payement WHERE type_frais = ? AND date_payement BETWEEN ? AND ? GROUP BY type_frais HAVING  montant >= ? AND montant <= ?";
                        $sql_array = array($_POST['poste_s'], $_POST['date_debut'], $_POST['date_fin'], $_POST['montant_minimum '], $_POST['montant_maximum']);

                        // $_SESSION['all_']
                        $req1 = ConnexionBdd::Connecter()->prepare($sql_all);
                        $req1->execute($sql_all_array);

                        while($d = $req1->fetch()){
                            $req2 = ConnexionBdd::Connecter()->prepare($sql);
                            // $req2->execute();
                        }

                        echo 'okey_req';
                        // -------------------------------------
                    }else if(isset($_POST['ch_m_min']) && !empty($_POST['ch_m_min']) && isset($_POST['montant_minimum']) && empty($_POST['montant_minimum'])){
                        // le max n pas definie
                        $sql = "SELECT SUM(montant) AS montant, type_frais FROM payement WHERE date_payement BETWEEN ? AND ? GROUP BY type_frais HAVING  montant >= ?";
                        $sql_array = array($_POST['date_debut'], $_POST['date_fin'], $_POST['montant_minimum ']);
                        // ----------------------------------------
                    }else if(isset($_POST['ch_m_min']) && empty($_POST['ch_m_min']) && isset($_POST['montant_minimum']) && !empty($_POST['montant_minimum'])){
                        // le min n pas definie
                        $sql = "SELECT SUM(montant) AS montant, type_frais FROM payement WHERE date_payement BETWEEN ? AND ? GROUP BY type_frais HAVING montant == ?";
                        $sql_array = array($_POST['date_debut'], $_POST['date_fin'], $_POST['montant_maximum']);
                    }
                }else{
                    if(isset($_POST['ch_m_min']) && !empty($_POST['ch_m_min']) && isset($_POST['montant_minimum']) && !empty($_POST['montant_minimum'])){
                        // tout est definie
                        $sql = "SELECT SUM(montant) AS montant, type_frais FROM payement GROUP BY type_frais HAVING  montant >= 1 AND montant <= 20";
                        $sql_array = array($_POST['montant_minimum '], $_POST['montant_maximum']);
                        // -------------------------------------
                    }else if(isset($_POST['ch_m_min']) && !empty($_POST['ch_m_min']) && isset($_POST['montant_minimum']) && empty($_POST['montant_minimum'])){
                        // le max n pas definie
                        $sql = "SELECT SUM(montant) AS montant, type_frais FROM payement GROUP BY type_frais HAVING  montant >= ?";
                        $sql_array = array($_POST['montant_minimum ']);
                        // ----------------------------------------
                    }else if(isset($_POST['ch_m_min']) && empty($_POST['ch_m_min']) && isset($_POST['montant_minimum']) && !empty($_POST['montant_minimum'])){
                        // le min n pas definie
                        $sql = "SELECT SUM(montant) AS montant, type_frais FROM payement GROUP BY type_frais HAVING montant == ?";
                        $sql_array = array($_POST['montant_maximum']);
                    }
                }
            }else{
                if(isset($_POST['ch_ch_date']) && !empty($_POST['ch_ch_date'])){
                    if(isset($_POST['ch_m_min']) && !empty($_POST['ch_m_min']) && isset($_POST['montant_minimum']) && !empty($_POST['montant_minimum'])){
                        // tout est definie
                        $sql = "SELECT SUM(montant) AS montant, type_frais FROM payement WHERE type_frais = ? AND date_payement BETWEEN ? AND ? GROUP BY type_frais HAVING  montant >= ? AND montant <= ?";
                        $sql_array = array($_POST['date_debut'], $_POST['date_fin'], $_POST['montant_minimum '], $_POST['montant_maximum']);
                        // -------------------------------------
                    }else if(isset($_POST['ch_m_min']) && !empty($_POST['ch_m_min']) && isset($_POST['montant_minimum']) && empty($_POST['montant_minimum'])){
                        // le max n pas definie
                        $sql = "SELECT SUM(montant) AS montant, type_frais FROM payement WHERE type_frais = ? AND date_payement BETWEEN ? AND ? GROUP BY type_frais HAVING  montant >= ?";
                        $sql_array = array($_POST['date_debut'], $_POST['date_fin'], $_POST['montant_minimum ']);
                        // ----------------------------------------
                    }else if(isset($_POST['ch_m_min']) && empty($_POST['ch_m_min']) && isset($_POST['montant_minimum']) && !empty($_POST['montant_minimum'])){
                        // le min n pas definie
                        $sql = "SELECT SUM(montant) AS montant, type_frais FROM payement WHERE type_frais = ? AND date_payement BETWEEN ? AND ? GROUP BY type_frais HAVING montant == ?";
                        $sql_array = array($_POST['date_debut'], $_POST['date_fin'], $_POST['montant_maximum']);
                    }
                }else{
                    if(isset($_POST['ch_m_min']) && !empty($_POST['ch_m_min']) && isset($_POST['montant_minimum']) && !empty($_POST['montant_minimum'])){
                        // tout est definie
                        $sql = "SELECT SUM(montant) AS montant, type_frais FROM payement WHERE type_frais = ? GROUP BY type_frais HAVING  montant >= ? AND montant <= ?";
                        $sql_array = array($_POST['poste_s'], $_POST['montant_minimum '], $_POST['montant_maximum']);
                        // -------------------------------------
                    }else if(isset($_POST['ch_m_min']) && !empty($_POST['ch_m_min']) && isset($_POST['montant_minimum']) && empty($_POST['montant_minimum'])){
                        // le max n pas definie
                        $sql = "SELECT SUM(montant) AS montant, type_frais FROM payement WHERE type_frais = ? GROUP BY type_frais HAVING  montant >= ?";
                        $sql_array = array($_POST['poste_s'], $_POST['montant_minimum']);
                        // ----------------------------------------
                    }else if(isset($_POST['ch_m_min']) && empty($_POST['ch_m_min']) && isset($_POST['montant_minimum']) && !empty($_POST['montant_minimum'])){
                        // le min n pas definie
                        $sql = "SELECT SUM(montant) AS montant, type_frais FROM payement WHERE type_frais = ? GROUP BY type_frais HAVING montant == ?";
                        $sql_array = array($_POST['poste_s'], $_POST['montant_maximum']);
                    }
                }
            }
        }else{
            echo "Error : selectionner un type de frais";
        }
    }else{
        echo "Error : l'annee academique n'est pas selectionner";
    }
?>