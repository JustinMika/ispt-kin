<?php 
    session_start();
    require_once '../../../includes/ConnexionBdd.class.php';

    // print_r($_GET);

    // FPDF
	require '../fpdf/fpdf.php';

    if(array_sum($_SESSION) == 0 && empty($_SESSION['data'])){
        header('location:../dec.php');
        exit();
    }

    function mm($v){
        if(empty($v)){
            return '0';
        }else{
            return $v;
        }
    }

    function entete($pdf){
        $pdf->SetFont('Arial','B',8);
        $pdf->cell(20, 5, "Matricule" , 1, 0, 'L');
        $pdf->cell(90, 5, "Noms", 1, 0, 'L');
        $pdf->cell(25, 5, decode_fr("montant à payer"), 1, 0, 'L');
        $pdf->cell(25, 5, "montant payer", 1, 0, 'L');
        $pdf->cell(22, 5, "Solde", 1, 0, 'L');
        $pdf->cell(10, 5, "%", 1, 0, 'L');
        $pdf->Ln(5);
    }

    function all($pdf){
        $pdf->AddPage();
        $pdf->SetFont('Arial','B',12);

        $pdf->cell(150,10,'',0,1,'C');
        $pdf->cell(197,6, decode_fr(strtoupper("institut superieur pedagogique et technique de kinshasa")),0,1,'C');
        $pdf->SetFont('Arial','',11); //Mail : info@isptkin.ac.cd
        $pdf->cell(197,6, decode_fr("ISPT-KIN"),0,1,'C');
        $pdf->cell(197,6, decode_fr("E-mail : info@isptkin.ac.cd"),0,1,'C');
        $pdf->cell(197,6, decode_fr("site web : www.isptkin.ac.cd"),0,1,'C', false, 'www.isptkin.ac.cd');
        $pdf->Ln(5);
        // logo de la faculte
        $pdf->Image("../../../images/ispt_kin.png", 10,15,25, 25);
        $pdf->Ln(2);
        $pdf->cell(197,1 ,"",1,1,'C', true);
        $pdf->Ln(2);
    }

	$pdf = new FPDF('P', 'mm', 'A4');
    all($pdf);
	

    // on d'abord la l'annee acad, promotion et faculte
    if(!empty($_GET['a']) && !empty($_GET['pr']) && !empty($_GET['fac']) && $_GET['al_fc'] =="on" && $_GET['ch_date'] == "on" && $_GET['ch_max'] == "on" && $_GET['ch_min'] == "on"){
        $pdf->SetFont('Arial','',8);
        $pdf->SetTextColor(0,0,0);
        $pdf->cell(60, 5, 'Annee Academique  : '.decode_fr($_GET['a']), 0, 1, 'L');
        $pdf->cell(60, 5, 'Promotion                 : '.decode_fr($_GET['pr']), 0, 1, 'L');
        $pdf->cell(60, 5, 'Faculte                      : '.decode_fr($_GET['fac']), 0, 1, 'L');
        $pdf->cell(60, 5, 'Type de frais             : Tous', 0, 1, 'L');
        $pdf->Ln(3);

        if($_GET['pr'] == "Toute les promotions" && $_GET['fac'] == "Toute les facultés"){
            $sel_all = ConnexionBdd::Connecter()->prepare("SELECT * FROM etudiants_inscrits WHERE annee_academique = ?");
            $sel_all->execute(array($_GET['a']));

            $pdf->SetFont('Arial','B',8);

            // tableau pour les facultes et les promotions
            $a_promotion = array();
            $a_faculte  = array();

            while($data_student = $sel_all->fetch()){
                if(!in_array($data_student['fac'], $a_faculte)){
                    $a_faculte[] = $data_student['fac'];
                }

                if(!in_array($data_student['promotion'], $a_promotion)){
                    $a_promotion[] = $data_student['promotion'];
                }
            }
        
            foreach($a_faculte as $f){
                foreach($a_promotion as $p){
                    $pdf->Ln(5);
                    $pdf->cell(60, 5, 'Faculte         : '.decode_fr($f), 0, 1, 'L');
                    $pdf->cell(60, 5, 'Promotion     : '.decode_fr($p), 0, 1, 'L');
                    $pdf->cell(60, 5, 'Annee Acad. : '.decode_fr($_GET['a']), 0, 1, 'L');
                    $pdf->Ln(1);
                    $sel_all = ConnexionBdd::Connecter()->prepare("SELECT * FROM etudiants_inscrits WHERE fac = ? AND promotion  = ? AND annee_academique = ?");
                    $sel_all->execute(array($f, $p, $_GET['a']));

                    $nn = $sel_all->rowCount();
                    if($nn > 0){
                        $pdf->SetFont('Arial','B',8);
                        $pdf->cell(20, 5, "Matricule" , 1, 0, 'L');
                        $pdf->cell(60, 5, "Noms" , 1, 0, 'L');
                        $pdf->cell(25, 5, "Montant prevu", 1, 0, 'L');
                        $pdf->cell(25, 5, "montant payer", 1, 0, 'L');
                        $pdf->cell(22, 5, "Solde", 1, 0, 'L');
                        $pdf->cell(22, 5, "%", 1, 0, 'L');
                        $pdf->Ln(5);
                        while($data_student = $sel_all->fetch()){
                            // le titre
                            $sql = "SELECT SUM(montant) as mt FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ? GROUP BY faculte";
                            $sql_array = array($data_student['matricule'], $data_student['promotion'], $data_student['fac'],$data_student['annee_academique']);
                                                                        
                            $all = ConnexionBdd::Connecter()->prepare($sql);
                            $all->execute($sql_array);

                            $n = $all->rowCount();

                            // $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                            // $pdf->cell(60, 5, ucfirst(decode_fr($data_student['noms'])) , 1, 0, 'L');
                            
                            if($n > 0){
                                while ($data = $all->fetch()){
                                    $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ? AND annee_acad = ?";
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute(array($data_student['matricule'], $data_student['fac'], $data_student['promotion'], $data_student['annee_academique'])); 
                                    $pdf->SetFont('Arial','',8);
                                    while($d = $sql_2->fetch()){
                                        $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                                        $pdf->cell(60, 5, ucfirst(decode_fr($data_student['noms'])) , 1, 0, 'L');
                                        $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L');
                                        $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                        $pdf->cell(22, 5, '$ '.mm($data['mt']-$d['mp']), 1, 0, 'L');
                                        $pdf->cell(22, 5, montant_restant_pourcent($d['mp'], $data['mt']).'%', 1, 0, 'L');
                                        // $pdf->Ln(0);
                                    }
                                }
                                $pdf->Ln(5);
                            }else{
                                $pdf->SetFont('Arial','I',8);
                                $pdf->SetTextColor(166, 10, 10);
                                $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                                $pdf->cell(60, 5, ucfirst(decode_fr($data_student['noms'])) , 1, 0, 'L');
                                $pdf->cell(25, 5, mm(""), 1, 0, 'L');
                                $pdf->cell(25, 5, mm(""), 1, 0, 'L');
                                $pdf->cell(22, 5, mm(""), 1, 0, 'L');
                                $pdf->cell(22, 5, " # ", 1, 0, 'L');
                                $pdf->Ln(5);
                                $pdf->SetTextColor(0, 0, 0);
                            }
                        }
                    }else{
                        $pdf->SetTextColor(200, 12, 12);
                        $pdf->SetFont('Arial','B',10);
                        $pdf->cell(60, 5, decode_fr('Auncun etudiant n\'est inscrit en : '.$p.' '.$f.' dans l\'annee acad.: '.$_GET['a']), 0, 1, 'L');
                        $pdf->SetTextColor(0,0,0);
                        $pdf->SetFont('Arial','',8);
                        $pdf->Ln(3);
                    }
                }
                $pdf->Ln(5);
            }
        }else if($_GET['pr'] != "Toute les promotions" && $_GET['fac'] == "Toute les facultés"){
            // 
            // on selectionne les info dans la table des etudiants
            // on selectionne les info dans la table des etudiants
            $sel_all = ConnexionBdd::Connecter()->prepare("SELECT fac, promotion, noms, matricule,annee_academique FROM etudiants_inscrits WHERE promotion = ? AND annee_academique = ? GROUP BY matricule");
            $sel_all->execute(array($_GET['pr'], $_GET['a']));

            $lst_fac = array();

            while($data_student = $sel_all->fetch()){
                // append dans la tableau
                if(!in_array($data_student['fac'], $lst_fac)){
                    $lst_fac[] = $data_student['fac'];
                }
            }

            foreach($lst_fac as $fac){
                $pdf->Ln(5);
                $pdf->cell(20, 5, ucfirst(decode_fr('faculté : '.$fac)) , 0, 0, 'L');
                $pdf->Ln(5);
                $pdf->cell(20, 5, ucfirst(decode_fr('promotion : '.$_GET['pr'])) , 0, 0, 'L');
                $pdf->Ln(5);
                $pdf->cell(20, 5, ucfirst(decode_fr('Annee Academique : '.$_GET['a'])) , 0, 0, 'L');
                $pdf->Ln(5);

                $sel_all = ConnexionBdd::Connecter()->prepare("SELECT fac, promotion, noms, matricule,annee_academique FROM etudiants_inscrits WHERE promotion = ? AND fac = ? AND annee_academique = ?");
                $sel_all->execute(array($_GET['pr'], $fac, $_GET['a']));

                while($data_student = $sel_all->fetch()){
                    $sql = "SELECT faculte, promotion, type_frais, SUM(montant) as mt FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ?";
                    $sql_array = array($data_student['matricule'], $data_student['promotion'], $fac, $data_student['annee_academique']);
                    $all = ConnexionBdd::Connecter()->prepare($sql);
                    $all->execute($sql_array);
                    
                    while ($data = $all->fetch()){
                        $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ? AND annee_acad = ?";

                        $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                        $sql_2->execute(array($data_student['matricule'], $fac, $data['promotion'], $_GET['a'])); 

                        $pdf->SetFont('Arial','',8);

                        while($d = $sql_2->fetch()){
                            $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                            $pdf->cell(90, 5, mm($data_student['noms']), 1, 0, 'L');
                            $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L');
                            $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                            $pdf->cell(22, 5, '$ '.mm($data['mt']-$d['mp']), 1, 0, 'L');
                            $pdf->cell(10, 5, montant_restant_pourcent($d['mp'], $data['mt']).'%', 1, 0, 'L');
                            $pdf->Ln(5);
                        }
                    }
                }
            }

        }else if($_GET['pr'] == "Toute les promotions" && $_GET['fac'] != "Toute les facultés"){
            // on selectionne les info dans la table des etudiants
            $sel_all = ConnexionBdd::Connecter()->prepare("SELECT fac, promotion, noms, matricule,annee_academique FROM etudiants_inscrits WHERE fac = ? AND annee_academique = ?");
            $sel_all->execute(array($_GET['fac'], $_GET['a']));

            while($data_student = $sel_all->fetch()){
                // $pdf->cell(60, 5, 'Promotion : '.$data_student['promotion'], 0, 1, 'L');
                $sql = "SELECT faculte, promotion, type_frais, SUM(montant) as mt FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ?";
                $sql_array = array($data_student['matricule'], $data_student['promotion'], $data_student['fac'], $data_student['annee_academique']);
                
                // $pdf->Ln(3);
                $all = ConnexionBdd::Connecter()->prepare($sql);
                $all->execute($sql_array);
                
                while ($data = $all->fetch()){
                    $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ? AND type_frais = ? AND annee_acad = ?";

                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                    $sql_2->execute(array($data_student['matricule'], $data['faculte'], $data['promotion'], $data['type_frais'], $_GET['a'])); 

                    $pdf->SetFont('Arial','',8);

                    while($d = $sql_2->fetch()){
                        $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                        $pdf->cell(90, 5, mm($data_student['noms']), 1, 0, 'L');
                        $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L');
                        $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                        $pdf->cell(22, 5, '$ '.mm($data['mt']-$d['mp']), 1, 0, 'L');
                        $pdf->cell(10, 5, montant_restant_pourcent($d['mp'], $data['mt']).'%', 1, 0, 'L');
                        $pdf->Ln(5);
                    }
                }
            }
            // ok
        }else if($_GET['pr'] != "Toute les promotions" && $_GET['fac'] != "Toute les facultés"){
            // on selectionne les info dans la table des etudiants
            $sel_all = ConnexionBdd::Connecter()->prepare("SELECT fac, promotion, noms, matricule,annee_academique FROM etudiants_inscrits WHERE fac = ? AND promotion = ? AND annee_academique = ?");
            $sel_all->execute(array($_GET['fac'], $_GET['pr'], $_GET['a']));
            entete($pdf);

            while($data_student = $sel_all->fetch()){
                $sql = "SELECT SUM(montant) as mt FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ?";
                $sql_array = array($data_student['matricule'], $data_student['promotion'], $data_student['fac'], $data_student['annee_academique']);
    
                $all = ConnexionBdd::Connecter()->prepare($sql);
                $all->execute($sql_array);
                $data = $all->fetch();

                // montant deja payer par l'etudiant
                $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ? AND annee_acad = ?";
    
                $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                $sql_2->execute(array($data_student['matricule'], $data_student['fac'], $data_student['promotion'], $_GET['a'])); 

                $pdf->SetFont('Arial','',8);

                $d = $sql_2->fetch();

                $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                $pdf->cell(90, 5, mm($data_student['noms']), 1, 0, 'L');
                $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L');
                $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                $pdf->cell(22, 5, '$ '.mm($data['mt'] - $d['mp']), 1, 0, 'L');
                $pdf->cell(10, 5, montant_restant_pourcent($d['mp'], $data['mt']).'%', 1, 0, 'L');
                $pdf->Ln(5);
            }
        }else{/* eh mrd.*/}
    }// annee acad - promotion - fac - type de frais -> ok
    else if(!empty($_GET['a']) && !empty($_GET['pr']) && !empty($_GET['fac']) && $_GET['al_fc'] !="on" && $_GET['ch_date'] == ""){
        $pdf->SetFont('Arial','',8);
        $pdf->SetTextColor(0,0,0);
        $pdf->cell(60, 5, 'Annee Academique  : '.decode_fr($_GET['a']), 0, 1, 'L');
        $pdf->cell(60, 5, 'Promotion                 : '.decode_fr($_GET['pr']), 0, 1, 'L');
        $pdf->cell(60, 5, 'Faculte                      : '.decode_fr($_GET['fac']), 0, 1, 'L');
        $pdf->cell(20, 5, 'Type de frais : ', 0, 1, 'L');
        $a = explode(",", $_GET['typ_f']);
        $t = array();
        for ($i=0; $i < count($a); $i++){
            // echo trim(str_replace(",", " ", $a[$i]));
            $t[] = trim(str_replace(",", " ", $a[$i]));
        }

        foreach ($t as $k) {
            $pdf->SetFont('Arial','IU',8);
            $pdf->cell(100, 5, $k.PHP_EOL, 0, 1, 'L');
        }

        // les putains d'options commmmmmmmmmmmmmmmmmmmmmmmmmmmmence /!\
        if($_GET['pr'] == "Toute les promotions" && $_GET['fac'] == "Toute les facultés"){
            // on selectionne les info dans la table des etudiants
            $sel_all = ConnexionBdd::Connecter()->prepare("SELECT fac, promotion, noms, matricule,annee_academique FROM etudiants_inscrits WHERE annee_academique = ?");
            $sel_all->execute(array($_GET['a']));

            // tableau pour les facultes et les promotions
            $a_promotion = array();
            $a_faculte  = array();

            while($data_student = $sel_all->fetch()){
                if(!in_array($data_student['fac'], $a_faculte)){
                    $a_faculte[] = $data_student['fac'];
                }

                if(!in_array($data_student['promotion'], $a_promotion)){
                    $a_promotion[] = $data_student['promotion'];
                }
            }

            // affichage des resultats des requettes
            foreach($a_faculte as $f){
                foreach($a_promotion as $p){
                    $pdf->Ln(5);
                    $pdf->SetFont('Arial','',8);
                    $pdf->cell(60, 5, 'Faculte         : '.decode_fr($f), 0, 1, 'L');
                    $pdf->cell(60, 5, 'Promotion     : '.decode_fr($p), 0, 1, 'L');
                    $pdf->cell(60, 5, 'Annee Acad. : '.decode_fr($_GET['a']), 0, 1, 'L');
                    $pdf->Ln(1);

                    $sel_all = ConnexionBdd::Connecter()->prepare("SELECT * FROM etudiants_inscrits WHERE fac = ? AND promotion  = ? AND annee_academique = ? GROUP BY matricule");
                    $sel_all->execute(array($f, $p, $_GET['a']));

                    // tableau pour les montant
                    $m_t = array();
                    $m_p = array();

                    $nn = $sel_all->rowCount();
                    if($nn > 0){
                        // on selectionne etudiant par etudiant 
                        entete($pdf);
                        while($data_student = $sel_all->fetch()){
                            foreach($t as $type_f){
                                // on selectionne le type de frais a payer par l etudiant(e)s
                                $sql = "SELECT DISTINCT type_frais, SUM(montant) as mt FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ? AND type_frais = ? GROUP BY type_frais";
                                $sql_array = array($data_student['matricule'], $data_student['promotion'], $data_student['fac'],$data_student['annee_academique'], $type_f);
                                                                            
                                $all = ConnexionBdd::Connecter()->prepare($sql);
                                $all->execute($sql_array);

                                while ($data = $all->fetch()){
                                    $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ? AND annee_acad = ? AND type_frais = ?";
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);

                                    $sql_2->execute(array($data_student['matricule'], $data_student['fac'], $data_student['promotion'], $data_student['annee_academique'], $data['type_frais'])); 
                                    //$data_student['matricule'], $data_student['fac'], $data_student['promotion'], $data_student['annee_academique']), $data['type_frais']
                                    $pdf->SetFont('Arial','',8);

                                    while($d = $sql_2->fetch()){
                                        // $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                                        // $pdf->cell(60, 5, ucfirst(decode_fr($data_student['noms'])) , 1, 0, 'L');
                                        // $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L');
                                        // $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                        // $pdf->cell(22, 5, '$ '.mm($data['mt']-$d['mp']), 1, 0, 'L');
                                        // $pdf->cell(22, 5, montant_restant_pourcent($d['mp'], $data['mt']).'%', 1, 0, 'L');
                                        // $pdf->Ln(5);
                                        $m_t[] = $data['mt'];
                                        $m_p[] = $d['mp'];
                                    }
                                }
                            }
                            $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                            $pdf->cell(90, 5, ucfirst(decode_fr($data_student['noms'])) , 1, 0, 'L');
                            $pdf->cell(25, 5, '$ '.mm(array_sum($m_t)), 1, 0, 'L');
                            $pdf->cell(25, 5, '$ '.mm(array_sum($m_p)), 1, 0, 'L');
                            $pdf->cell(22, 5, '$ '.mm(array_sum($m_t) - array_sum($m_p)), 1, 0, 'L');
                            $pdf->cell(10, 5, montant_restant_pourcent(array_sum($m_p), array_sum($m_t)).'%', 1, 0, 'L');
                            $pdf->Ln(5);
                            $m_t = array();
                            $m_p = array();
                        }
                    }else{
                        $pdf->SetTextColor(200, 12, 12);
                        $pdf->SetFont('Arial','B',10);
                        $pdf->cell(60, 5, decode_fr('Auncun etudiant n\'est inscrit en : '.$p.' '.$f.' dans l\'annee acad.: '.$_GET['a']), 0, 1, 'L');
                        $pdf->SetTextColor(0,0,0);
                        $pdf->SetFont('Arial','',8);
                        $pdf->Ln(3);
                    }
                }
                $pdf->Ln(5);
            }
        }else if($_GET['pr'] != "Toute les promotions" && $_GET['fac'] == "Toute les facultés"){
            // on selectionne les info dans la table des etudiants
            $sel_all = ConnexionBdd::Connecter()->prepare("SELECT fac, promotion, noms, matricule,annee_academique FROM etudiants_inscrits WHERE promotion = ? AND annee_academique = ? GROUP BY matricule");
            $sel_all->execute(array($_GET['pr'], $_GET['a']));

            $lst_fac = array();

            while($data_student = $sel_all->fetch()){
                // append dans la tableau
                if(!in_array($data_student['fac'], $lst_fac)){
                    $lst_fac[] = $data_student['fac'];
                }
            }

            foreach($lst_fac as $fac){
                $pdf->SetFont('Arial','I',8);
                $pdf->Ln(5);
                $pdf->cell(20, 5, ucfirst(decode_fr('faculté : '.$fac)) , 0, 0, 'L');
                $pdf->Ln(5);
                $pdf->cell(20, 5, ucfirst(decode_fr('promotion : '.$_GET['pr'])) , 0, 0, 'L');
                $pdf->Ln(5);
                $pdf->cell(20, 5, ucfirst(decode_fr('Annee Academique : '.$_GET['a'])) , 0, 0, 'L');
                $pdf->Ln(5);

                $sel_all = ConnexionBdd::Connecter()->prepare("SELECT fac, promotion, noms, matricule,annee_academique FROM etudiants_inscrits WHERE promotion = ? AND fac = ? AND annee_academique = ?");
                $sel_all->execute(array($_GET['pr'], $fac, $_GET['a']));

                $n_e = $sel_all->fetch();
                if($n_e > 0){
                    entete($pdf);
                    while ($data_student = $sel_all->fetch()){
                        $a_1 = array();
                        $b_1 = array();

                        $t = array();
                        for ($i=0; $i < count($a); $i++){
                            $t[] = trim(str_replace(",", " ", $a[$i]));
                        }

                        foreach ($t as $k) {
                            $sql = "SELECT faculte, promotion, type_frais, SUM(montant) as mt FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ? AND type_frais = ?";
                            $sql_array = array($data_student['matricule'], $data_student['promotion'], $fac, $data_student['annee_academique'], trim($k));
                            $all = ConnexionBdd::Connecter()->prepare($sql);
                            $all->execute($sql_array);

                            if($all->rowCount() > 0){
                                while($data = $all->fetch()){
                                    $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ? AND annee_acad = ? AND type_frais = ?";

                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute(array($data_student['matricule'], $fac, $data['promotion'], $_GET['a'], $data['type_frais'])); 

                                    $pdf->SetFont('Arial','',8);

                                    if($sql_2->rowCount()){
                                        while($d = $sql_2->fetch()){
                                            $a_1[] = $data['mt'];
                                            $b_1[] = $d['mp'];
                                        }
                                    }
                                }
                            }
                        }
                        if(count($a_1) > 0 &&  count($b_1) > 0){
                            $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                            $pdf->cell(90, 5, mm($data_student['noms']), 1, 0, 'L');
                            $pdf->cell(25, 5, '$ '.mm(array_sum($a_1)), 1, 0, 'L');
                            $pdf->cell(25, 5, '$ '.mm(array_sum($b_1)), 1, 0, 'L');
                            $pdf->cell(22, 5, '$ '.mm(array_sum($a_1) - array_sum($b_1)), 1, 0, 'L');
                            $pdf->cell(10, 5, montant_restant_pourcent(array_sum($b_1), array_sum($a_1)).'%', 1, 0, 'L');
                            $pdf->Ln(5);
                            $a_1 = array();
                            $b_1 = array();
                        }else{
                            $pdf->cell(90, 5, "", 1, 0, 'L', true);
                        }
                    }
                }else{
                    $pdf->SetTextColor(150, 1, 1);
                    $pdf->cell(20, 5, ucfirst(decode_fr('Aucun etudiant n\'est inscrit dans cete faculté : '.$fac)) , 0, 0, 'L');
                    $pdf->SetTextColor(1, 1, 1);
                }
            }
        }else if($_GET['pr'] == "Toute les promotions" && $_GET['fac'] != "Toute les facultés"){
            // on selectionne les info dans la table des etudiants
            $pdf->SetFont('Arial','B',8);
            $pdf->cell(20, 5, "Matricule" , 1, 0, 'L');
            $pdf->cell(90, 5, "Noms", 1, 0, 'L');
            $pdf->cell(25, 5, decode_fr("montant à payer"), 1, 0, 'L');
            $pdf->cell(25, 5, "montant payer", 1, 0, 'L');
            $pdf->cell(22, 5, "Solde", 1, 0, 'L');
            $pdf->cell(10, 5, "%", 1, 0, 'L');
            $pdf->Ln(5);
            $sel_all = ConnexionBdd::Connecter()->prepare("SELECT fac, promotion, noms, matricule,annee_academique FROM etudiants_inscrits WHERE fac = ? AND annee_academique = ?");
            $sel_all->execute(array($_GET['fac'], $_GET['a']));

            // tableau
            $m_p = array();
            $m_t = array();

            while($data_student = $sel_all->fetch()){
                foreach($t as $type_f){
                    // $pdf->cell(60, 5, 'Promotion : '.$type_f, 0, 1, 'L');
                    $sql = "SELECT faculte, promotion, type_frais, SUM(montant) as mt FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ? AND type_frais = ?";
                    $sql_array = array($data_student['matricule'], $data_student['promotion'], $data_student['fac'], $data_student['annee_academique'], $type_f);
                    
                    // $pdf->Ln(3);
                    $all = ConnexionBdd::Connecter()->prepare($sql);
                    $all->execute($sql_array);
                    
                    while ($data = $all->fetch()){
                        $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ? AND type_frais = ? AND annee_acad = ?";

                        $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                        $sql_2->execute(array($data_student['matricule'], $data['faculte'], $data['promotion'], $type_f, $_GET['a'])); 

                        $pdf->SetFont('Arial','',8);

                        while($d = $sql_2->fetch()){
                            $m_t[] = $data['mt'];
                            $m_p[] = $d['mp'];
                        }
                    }
                }
                $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                $pdf->cell(90, 5, mm($data_student['noms']), 1, 0, 'L');
                $pdf->cell(25, 5, '$ '.mm(array_sum($m_t)), 1, 0, 'L');
                $pdf->cell(25, 5, '$ '.mm(array_sum($m_p)), 1, 0, 'L');
                $pdf->cell(22, 5, '$ '.mm(array_sum($m_t) - array_sum($m_p)), 1, 0, 'L');
                $pdf->cell(10, 5, montant_restant_pourcent(array_sum($m_p), array_sum($m_t)).'%', 1, 0, 'L');
                $pdf->Ln(5);
                // on vide les deux tableaux
                $m_p = array();
                $m_t = array();
            }
        }else if($_GET['pr'] != "Toute les promotions" && $_GET['fac'] != "Toute les facultés"){
            // on selectionne les info dans la table des etudiants
            $sel_all = ConnexionBdd::Connecter()->prepare("SELECT fac, promotion, noms, matricule,annee_academique FROM etudiants_inscrits WHERE fac = ? AND promotion = ? AND annee_academique = ?");
            $sel_all->execute(array($_GET['fac'], $_GET['pr'], $_GET['a']));
            entete($pdf);

            $nn = $sel_all->rowCount();

            if($nn > 0){
                $t1 = array();
                $t2 = array();
                while($data_student = $sel_all->fetch()){
                    foreach ($t as $type_frais){
                        // echo $type_frais;
                        $sql = "SELECT type_frais, SUM(montant) as mt FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ? AND type_frais = ?";
                        $sql_array = array($data_student['matricule'], $data_student['promotion'], $data_student['fac'], $data_student['annee_academique'], trim($type_frais));
            
                        $all = ConnexionBdd::Connecter()->prepare($sql);
                        $all->execute($sql_array);

                        $n1 = $all->rowCount();

                        if($n1 > 0){
                            $data = $all->fetch();
                            $t1[] = $data['mt'];

                            $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ? AND annee_acad = ? AND type_frais = ?";
                    
                            $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                            $sql_2->execute(array($data_student['matricule'], $data_student['fac'], $data_student['promotion'], $_GET['a'], trim($type_frais)));
                            
                            $pdf->SetFont('Arial','',8);

                            $n2 = $sql_2->rowCount();

                            if($n2 > 0){
                                $d = $sql_2->fetch();
                                $t2[] = $d['mp'];
                            }
                        }                     
                    }
                    // $d = $sql_2->fetch();
                    $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                    $pdf->cell(90, 5, mm($data_student['noms']), 1, 0, 'L');
                    $pdf->cell(25, 5, '$ '.mm(array_sum($t1)), 1, 0, 'L');
                    $pdf->cell(25, 5, '$ '.mm(array_sum($t2)), 1, 0, 'L');
                    $pdf->cell(22, 5, '$ '.mm(array_sum($t1) - array_sum($t2)), 1, 0, 'L');
                    $pdf->cell(10, 5, montant_restant_pourcent(array_sum($t2), array_sum($t1)).'%', 1, 0, 'L');
                    $pdf->Ln(5);
                }
            }else{
                $pdf->SetTextColor(222, 1, 1);
                $pdf->Cell(200, 10, "Auncun etudiant n'est inscrit en {$_GET['pr']} {$_GET['fac']}");
            }
        }


    }
    // annee acad - promotion - fac- date de coupure -> ok
    elseif (!empty($_GET['a']) && !empty($_GET['pr']) && !empty($_GET['fac']) && $_GET['al_fc'] =="on" && $_GET['ch_date'] != "on" && $_GET['ch_max'] == "on" && $_GET['ch_min'] == "on") {
        $pdf->SetFont('Arial','',8);
        $pdf->SetTextColor(0,0,0);
        $pdf->cell(60, 5, 'Annee Academique  : '.decode_fr($_GET['a']), 0, 1, 'L');
        $pdf->cell(60, 5, 'Promotion                 : '.decode_fr($_GET['pr']), 0, 1, 'L');
        $pdf->cell(60, 5, 'Faculte                      : '.decode_fr($_GET['fac']), 0, 1, 'L');
        $pdf->cell(60, 5, 'Type de frais : Tous', 0, 1, 'L');
        // $pdf->cell(60, 5, decode_fr('Date de coupure : '.date('d/m/Y', strftime($_GET['d1'])).' - '.date('d/m/Y', strftime($_GET['d2']))), 0, 1, 'L');
        $pdf->cell(60, 5, decode_fr('Date de coupure : du '.date("d F Y", strtotime(strtolower($_GET['d1']))).' au '.date("d F Y", strtotime(strtolower($_GET['d2'])))), 0, 1, 'L');
        $pdf->Ln(3);

        if($_GET['pr'] == "Toute les promotions" && $_GET['fac'] == "Toute les facultés"){
            $sel_all = ConnexionBdd::Connecter()->prepare("SELECT * FROM etudiants_inscrits WHERE annee_academique = ?");
            $sel_all->execute(array($_GET['a']));

            $pdf->SetFont('Arial','B',8);

            // tableau pour les facultes et les promotions
            $a_promotion = array();
            $a_faculte  = array();

            while($data_student = $sel_all->fetch()){
                if(!in_array($data_student['fac'], $a_faculte)){
                    $a_faculte[] = $data_student['fac'];
                }

                if(!in_array($data_student['promotion'], $a_promotion)){
                    $a_promotion[] = $data_student['promotion'];
                }
            }
        
            foreach($a_faculte as $f){
                foreach($a_promotion as $p){
                    $pdf->Ln(5);
                    $pdf->cell(60, 5, 'Faculte         : '.decode_fr($f), 0, 1, 'L');
                    $pdf->cell(60, 5, 'Promotion     : '.decode_fr($p), 0, 1, 'L');
                    $pdf->cell(60, 5, 'Annee Acad. : '.decode_fr($_GET['a']), 0, 1, 'L');
                    $pdf->Ln(1);
                    $sel_all = ConnexionBdd::Connecter()->prepare("SELECT * FROM etudiants_inscrits WHERE fac = ? AND promotion  = ? AND annee_academique = ?");
                    $sel_all->execute(array($f, $p, $_GET['a']));

                    $nn = $sel_all->rowCount();
                    if($nn > 0){
                        $pdf->SetFont('Arial','B',8);
                        $pdf->cell(20, 5, "Matricule" , 1, 0, 'L');
                        $pdf->cell(90, 5, "Noms" , 1, 0, 'L');
                        $pdf->cell(25, 5, "montant a payer", 1, 0, 'L');
                        $pdf->cell(25, 5, "montant payer", 1, 0, 'L');
                        $pdf->Ln(5);

                        while($data_student = $sel_all->fetch()){
                            $sql = "SELECT SUM(montant) as mt FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ?";
                            $sql_array = array($data_student['matricule'], $data_student['promotion'], $data_student['fac'], $data_student['annee_academique']);
                            $all = ConnexionBdd::Connecter()->prepare($sql);
                            $all->execute($sql_array);

                            while($data = $all->fetch()){
                                $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE date_payement BETWEEN ? AND ? AND matricule = ? AND faculte = ? AND promotion = ? AND annee_acad = ? GROUP BY matricule";

                                $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                $sql_2->execute(array($_GET['d1'], $_GET['d2'], $data_student['matricule'], $data_student['fac'], $data_student['promotion'], $_GET['a'])); 
                                
                                $pdf->SetFont('Arial','',8);

                                $n = $sql_2->rowCount();
                                // die('nmbre : '.$n);

                                if($n >= 1){
                                    while($d = $sql_2->fetch()){
                                        $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                                        $pdf->cell(90, 5, $data_student['noms'], 1, 0, 'L');
                                        $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L');
                                        $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                        $pdf->Ln(5);
                                    }
                                }else{
                                    $pdf->SetFillColor(0,0,0);
                                    $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                                    $pdf->cell(90, 5, $data_student['noms'], 1, 0, 'L');
                                    $pdf->cell(25, 5, '', 1, 0, 'L', true);
                                    $pdf->cell(25, 5, '', 1, 0, 'L', true);
                                    $pdf->Ln(5);
                                }
                            }
                        }
                    }else{
                        $pdf->SetTextColor(200, 12, 12);
                        $pdf->SetFont('Arial','B',10);
                        $pdf->cell(60, 5, decode_fr('Auncun etudiant n\'est inscrit en : '.$p.' '.$f.' dans l\'annee acad.: '.$_GET['a']), 0, 1, 'L');
                        $pdf->SetTextColor(0,0,0);
                        $pdf->SetFont('Arial','',8);
                        $pdf->Ln(3);
                    }
                }
                $pdf->Ln(5);
            }
        }else if($_GET['pr'] != "Toute les promotions" && $_GET['fac'] == "Toute les facultés"){
            // 
            // on selectionne les info dans la table des etudiants
            $sel_all = ConnexionBdd::Connecter()->prepare("SELECT fac, promotion, noms, matricule,annee_academique FROM etudiants_inscrits WHERE promotion = ? AND annee_academique = ? GROUP BY matricule");
            $sel_all->execute(array($_GET['pr'], $_GET['a']));

            $lst_fac = array();

            while($data_student = $sel_all->fetch()){
                // append dans la tableau
                if(!in_array($data_student['fac'], $lst_fac)){
                    $lst_fac[] = $data_student['fac'];
                }
            }

            foreach($lst_fac as $fac){
                $pdf->Ln(5);
                $pdf->cell(20, 5, ucfirst(decode_fr('faculté : '.$fac)) , 0, 0, 'L');
                $pdf->Ln(5);
                $pdf->cell(20, 5, ucfirst(decode_fr('promotion : '.$_GET['pr'])) , 0, 0, 'L');
                $pdf->Ln(5);
                $pdf->cell(20, 5, ucfirst(decode_fr('Annee Academique : '.$_GET['a'])) , 0, 0, 'L');
                $pdf->Ln(5);

                $sel_all = ConnexionBdd::Connecter()->prepare("SELECT fac, promotion, noms, matricule,annee_academique FROM etudiants_inscrits WHERE promotion = ? AND fac = ? AND annee_academique = ?");
                $sel_all->execute(array($_GET['pr'], $fac, $_GET['a']));

                $pdf->SetFont('Arial','B',8);
                $pdf->cell(20, 5, "Matricule" , 1, 0, 'L');
                $pdf->cell(90, 5, "Noms" , 1, 0, 'L');
                $pdf->cell(25, 5, "montant a payer", 1, 0, 'L');
                $pdf->cell(25, 5, "montant payer", 1, 0, 'L');
                $pdf->Ln(5);
                while($data_student = $sel_all->fetch()){
                    // entete
                    $sql = "SELECT faculte, promotion, type_frais, SUM(montant) as mt FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ?";
                    $sql_array = array($data_student['matricule'], $data_student['promotion'], $fac, $data_student['annee_academique']);
                    $all = ConnexionBdd::Connecter()->prepare($sql);
                    $all->execute($sql_array);
                    
                    while ($data = $all->fetch()){
                        $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE date_payement BETWEEN ? AND ? AND matricule = ? AND faculte = ? AND promotion = ? AND annee_acad = ? GROUP BY matricule";

                        $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                        $sql_2->execute(array($_GET['d1'], $_GET['d2'], $data_student['matricule'], $data_student['fac'], $data_student['promotion'], $_GET['a'])); 
                        
                        $pdf->SetFont('Arial','',8);

                        $n = $sql_2->rowCount();
                        // die('nmbre : '.$n);

                        if($n >= 1){
                            while($d = $sql_2->fetch()){
                                $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                                $pdf->cell(90, 5, $data_student['noms'], 1, 0, 'L');
                                $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L');
                                $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                $pdf->Ln(5);
                            }
                        }else{
                            $pdf->SetFillColor(0,0,0);
                            $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                            $pdf->cell(90, 5, $data_student['noms'], 1, 0, 'L');
                            $pdf->cell(25, 5, '', 1, 0, 'L', true);
                            $pdf->cell(25, 5, '', 1, 0, 'L', true);
                            $pdf->Ln(5);
                        }
                    }
                }
            }

        }else if($_GET['pr'] == "Toute les promotions" && $_GET['fac'] != "Toute les facultés"){
            // on selectionne les info dans la table des etudiants
            $sel_all = ConnexionBdd::Connecter()->prepare("SELECT fac, promotion, noms, matricule,annee_academique FROM etudiants_inscrits WHERE fac = ? AND annee_academique = ?");
            $sel_all->execute(array($_GET['fac'], $_GET['a']));

            $pdf->SetFont('Arial','B',8);
            $pdf->cell(20, 5, "Matricule" , 1, 0, 'L');
            $pdf->cell(90, 5, "Noms" , 1, 0, 'L');
            $pdf->cell(25, 5, "montant a payer", 1, 0, 'L');
            $pdf->cell(25, 5, "montant payer", 1, 0, 'L');
            $pdf->cell(15, 5, "Solde", 1, 0, 'L');
            $pdf->cell(15, 5, "%", 1, 0, 'L');
            $pdf->Ln(5);

            while($data_student = $sel_all->fetch()){
                // $pdf->cell(60, 5, 'Promotion : '.$data_student['promotion'], 0, 1, 'L');
                $sql = "SELECT faculte, promotion, type_frais, SUM(montant) as mt FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ?";
                $sql_array = array($data_student['matricule'], $data_student['promotion'], $data_student['fac'], $data_student['annee_academique']);
                
                // $pdf->Ln(3);
                $all = ConnexionBdd::Connecter()->prepare($sql);
                $all->execute($sql_array);

                $nn = $all->rowCount();

                if($nn >= 1){
                    while ($data = $all->fetch()){
                        $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE date_payement BETWEEN ? AND ? AND matricule = ? AND faculte = ? AND promotion = ? AND type_frais = ? AND annee_acad = ? GROUP BY matricule";

                        $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                        $sql_2->execute(array($_GET['d1'], $_GET['d2'], $data_student['matricule'], $data['faculte'], $data['promotion'], $data['type_frais'], $_GET['a'])); 

                        $pdf->SetFont('Arial','',8);
                        $n1 = $sql_2->rowCount();
                        if($n1 > 0){
                            while($d = $sql_2->fetch()){
                                $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                                $pdf->cell(90, 5, mm($data_student['noms']), 1, 0, 'L');
                                $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L');
                                $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                $pdf->cell(15, 5, '$ '.mm($data['mt']-$d['mp']), 1, 0, 'L');
                                $pdf->cell(15, 5, montant_restant_pourcent($d['mp'], $data['mt']).'%', 1, 0, 'L');
                                $pdf->Ln(5);
                            }
                        }else{
                            $pdf->SetFillColor(0,0,0);
                            $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                            $pdf->cell(90, 5, mm($data_student['noms']), 1, 0, 'L');
                            $pdf->cell(25, 5, '', 1, 0, 'L', true);
                            $pdf->cell(25, 5, '', 1, 0, 'L', true);
                            $pdf->cell(15, 5, '', 1, 0, 'L', true);
                            $pdf->cell(15, 5, '', 1, 0, 'L', true);
                            $pdf->Ln(5);
                        }
                    }
                }else{
                    $pdf->SetTextColor(130, 1, 1);
                    $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                    $pdf->cell(90, 5, mm($data_student['noms']), 1, 0, 'L');
                    $pdf->cell(25, 5, '', 1, 0, 'L',);
                    $pdf->cell(25, 5, '', 1, 0, 'L',);
                    $pdf->cell(22, 5, '', 1, 0, 'L',);
                    $pdf->cell(10, 5, '', 1, 0, 'L',);
                    $pdf->Ln(5);
                    $pdf->SetTextColor(1, 1, 1);
                }
            }
            // ok
        }else if($_GET['pr'] != "Toute les promotions" && $_GET['fac'] != "Toute les facultés"){
            // on selectionne les info dans la table des etudiants
            $sel_all = ConnexionBdd::Connecter()->prepare("SELECT fac, promotion, noms, matricule,annee_academique FROM etudiants_inscrits WHERE fac = ? AND promotion = ? AND annee_academique = ?");
            $sel_all->execute(array($_GET['fac'], $_GET['pr'], $_GET['a']));
            entete($pdf);

            while($data_student = $sel_all->fetch()){
                $sql = "SELECT SUM(montant) as mt FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ?";
                $sql_array = array($data_student['matricule'], $data_student['promotion'], $data_student['fac'], $data_student['annee_academique']);
    
                $all = ConnexionBdd::Connecter()->prepare($sql);
                $all->execute($sql_array);

                $n1 = $all->rowCount();

                if($n1 > 0){
                    $data = $all->fetch();

                    // montant deja payer par l'etudiant
                    $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE date_payement BETWEEN ? AND ? AND matricule = ? AND faculte = ? AND promotion = ? AND annee_acad = ? GROUP BY matricule";
        
                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                    $sql_2->execute(array($_GET['d1'], $_GET['d2'], $data_student['matricule'], $data_student['fac'], $data_student['promotion'], $_GET['a'])); 

                    $pdf->SetFont('Arial','',8);

                    $n2 = $sql_2->rowCount();

                    if($n2 > 0){
                        $d = $sql_2->fetch();

                        $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                        $pdf->cell(90, 5, mm($data_student['noms']), 1, 0, 'L');
                        $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L');
                        $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                        $pdf->cell(22, 5, '$ '.mm($data['mt'] - $d['mp']), 1, 0, 'L');
                        $pdf->cell(10, 5, montant_restant_pourcent($d['mp'], $data['mt']).'%', 1, 0, 'L');
                        $pdf->Ln(5);
                    }else{
                        $pdf->SetFillColor(0,0,0);
                        $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                        $pdf->cell(90, 5, mm($data_student['noms']), 1, 0, 'L');
                        $pdf->cell(25, 5, '', 1, 0, 'L', true);
                        $pdf->cell(25, 5, '', 1, 0, 'L', true);
                        $pdf->cell(22, 5, '', 1, 0, 'L', true);
                        $pdf->cell(10, 5, '', 1, 0, 'L', true);
                        $pdf->Ln(5);
                    }
                }
            }
        }else{/* eh mrd.*/}
    }
    // annee acad - promotion - fac - pourcent min. 
    else if(!empty($_GET['a']) && !empty($_GET['pr']) && !empty($_GET['fac']) && !empty($_GET['ch_min']) && $_GET['ch_min'] == "_min_" /*&& $_GET['al_fc'] =="on"*/ && $_GET['ch_date'] == ""){
        $pdf->SetFont('Arial','',8);
        $pdf->SetTextColor(0,0,0);
        $pdf->cell(60, 5, 'Annee Academique  : '.decode_fr($_GET['a']), 0, 1, 'L');
        $pdf->cell(60, 5, 'Promotion                 : '.decode_fr($_GET['pr']), 0, 1, 'L');
        $pdf->cell(60, 5, 'Faculte                      : '.decode_fr($_GET['fac']), 0, 1, 'L');
        $pdf->cell(60, 5, 'Type de frais             : Tous', 0, 1, 'L');
        $pdf->cell(60, 5, decode_fr('Date de coupure       : '), 0, 1, 'L');
        $pdf->cell(60, 5, decode_fr('Poucentage min.       : '.$_GET['m_min'].'%'), 0, 1, 'L');
        $pdf->cell(60, 5, decode_fr('Poucentage max.      : '), 0, 1, 'L');
        $pdf->Ln(3);
        // les putains d'options commmmmmmmmmmmmmmmmmmmmmmmmmmmmence /!\
        if($_GET['pr'] == "Toute les promotions" && $_GET['fac'] == "Toute les facultés"){
            $sel_all = ConnexionBdd::Connecter()->prepare("SELECT * FROM etudiants_inscrits WHERE annee_academique = ?");
            $sel_all->execute(array($_GET['a']));

            $pdf->SetFont('Arial','B',8);

            // tableau pour les facultes et les promotions
            $a_promotion = array();
            $a_faculte  = array();

            while($data_student = $sel_all->fetch()){
                if(!in_array($data_student['fac'], $a_faculte)){
                    $a_faculte[] = $data_student['fac'];
                }

                if(!in_array($data_student['promotion'], $a_promotion)){
                    $a_promotion[] = $data_student['promotion'];
                }
            }

            foreach($a_faculte as $f){
                foreach($a_promotion as $p){
                    $pdf->Ln(5);
                    $pdf->cell(60, 5, 'Faculte         : '.decode_fr($f), 0, 1, 'L');
                    $pdf->cell(60, 5, 'Promotion     : '.decode_fr($p), 0, 1, 'L');
                    $pdf->cell(60, 5, 'Annee Acad. : '.decode_fr($_GET['a']), 0, 1, 'L');
                    $pdf->Ln(1);
                    $sel_all = ConnexionBdd::Connecter()->prepare("SELECT * FROM etudiants_inscrits WHERE fac = ? AND promotion  = ? AND annee_academique = ?");
                    $sel_all->execute(array($f, $p, $_GET['a']));

                    $nn = $sel_all->rowCount();
                    if($nn > 0){
                        $pdf->SetFont('Arial','B',8);
                        $pdf->cell(20, 5, "Matricule" , 1, 0, 'L');
                        $pdf->cell(60, 5, "Noms" , 1, 0, 'L');
                        $pdf->cell(25, 5, "Montant prevu", 1, 0, 'L');
                        $pdf->cell(25, 5, "montant payer", 1, 0, 'L');
                        $pdf->cell(22, 5, "Solde", 1, 0, 'L');
                        $pdf->cell(22, 5, "%", 1, 0, 'L');
                        $pdf->Ln(5);
                        while($data_student = $sel_all->fetch()){
                            // le titre
                            $sql = "SELECT SUM(montant) as mt FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ? GROUP BY faculte";
                            $sql_array = array($data_student['matricule'], $data_student['promotion'], $data_student['fac'],$data_student['annee_academique']);
                                                                        
                            $all = ConnexionBdd::Connecter()->prepare($sql);
                            $all->execute($sql_array);

                            $n = $all->rowCount();
                            if($n > 0){
                                while ($data = $all->fetch()){
                                    $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ? AND annee_acad = ?";
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute(array($data_student['matricule'], $data_student['fac'], $data_student['promotion'], $data_student['annee_academique'])); 
                                    $pdf->SetFont('Arial','',8);
                                    while($d = $sql_2->fetch()){
                                        if(intval(($d['mp'] * 100)/$data['mt']) >= intval($_GET['m_min'])){
                                            $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                                            $pdf->cell(60, 5, ucfirst(decode_fr($data_student['noms'])) , 1, 0, 'L');
                                            $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L');
                                            $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                            $pdf->cell(22, 5, '$ '.mm($data['mt']-$d['mp']), 1, 0, 'L');
                                            $pdf->cell(22, 5, montant_restant_pourcent($d['mp'], $data['mt']).'%', 1, 0, 'L');
                                            // $pdf->Ln(0);
                                        }else{
                                            $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                                            $pdf->cell(60, 5, ucfirst(decode_fr($data_student['noms'])) , 1, 0, 'L');
                                            $pdf->SetTextColor(255,255,255);
                                            $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L', true);
                                            $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L', true);
                                            $pdf->cell(22, 5, '$ '.mm($data['mt']-$d['mp']), 1, 0, 'L', true);
                                            $pdf->cell(22, 5, montant_restant_pourcent($d['mp'], $data['mt']).'%', 1, 0, 'L', true);
                                            $pdf->SetTextColor(11,11,11);
                                        }
                                    }
                                }
                                $pdf->Ln(5);
                            }else{
                                $pdf->SetFont('Arial','I',8);
                                $pdf->SetTextColor(166, 10, 10);
                                $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                                $pdf->cell(60, 5, ucfirst(decode_fr($data_student['noms'])) , 1, 0, 'L');
                                $pdf->cell(25, 5, mm(""), 1, 0, 'L');
                                $pdf->cell(25, 5, mm(""), 1, 0, 'L');
                                $pdf->cell(22, 5, mm(""), 1, 0, 'L');
                                $pdf->cell(22, 5, " # ", 1, 0, 'L');
                                $pdf->Ln(5);
                                $pdf->SetTextColor(0, 0, 0);
                            }
                        }
                    }else{
                        $pdf->SetTextColor(200, 12, 12);
                        $pdf->SetFont('Arial','B',10);
                        $pdf->cell(60, 5, decode_fr('Auncun etudiant n\'est inscrit en : '.$p.' '.$f.' dans l\'annee acad.: '.$_GET['a']), 0, 1, 'L');
                        $pdf->SetTextColor(0,0,0);
                        $pdf->SetFont('Arial','',8);
                        $pdf->Ln(3);
                    }
                }
                $pdf->Ln(5);
            }
        }else if($_GET['pr'] != "Toute les promotions" && $_GET['fac'] == "Toute les facultés"){
            // on selectionne les info dans la table des etudiants
            $sel_all = ConnexionBdd::Connecter()->prepare("SELECT fac, promotion, noms, matricule,annee_academique FROM etudiants_inscrits WHERE promotion = ? AND annee_academique = ? GROUP BY matricule");
            $sel_all->execute(array($_GET['pr'], $_GET['a']));

            $lst_fac = array();

            while($data_student = $sel_all->fetch()){
                // append dans la tableau
                if(!in_array($data_student['fac'], $lst_fac)){
                    $lst_fac[] = $data_student['fac'];
                }
            }

            foreach($lst_fac as $fac){
                $pdf->Ln(5);
                $pdf->cell(20, 5, ucfirst(decode_fr('faculté : '.$fac)) , 0, 0, 'L');
                $pdf->Ln(5);
                $pdf->cell(20, 5, ucfirst(decode_fr('promotion : '.$_GET['pr'])) , 0, 0, 'L');
                $pdf->Ln(5);
                $pdf->cell(20, 5, ucfirst(decode_fr('Annee Academique : '.$_GET['a'])) , 0, 0, 'L');
                $pdf->Ln(5);

                entete($pdf);
                $sel_all = ConnexionBdd::Connecter()->prepare("SELECT fac, promotion, noms, matricule,annee_academique FROM etudiants_inscrits WHERE promotion = ? AND fac = ? AND annee_academique = ?");
                $sel_all->execute(array($_GET['pr'], $fac, $_GET['a']));

                while($data_student = $sel_all->fetch()){
                    $sql = "SELECT faculte, promotion, type_frais, SUM(montant) as mt FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ?";
                    $sql_array = array($data_student['matricule'], $data_student['promotion'], $fac, $data_student['annee_academique']);
                    $all = ConnexionBdd::Connecter()->prepare($sql);
                    $all->execute($sql_array);
                    
                    while ($data = $all->fetch()){
                        $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ? AND annee_acad = ?";

                        $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                        $sql_2->execute(array($data_student['matricule'], $fac, $data['promotion'], $_GET['a'])); 

                        $pdf->SetFont('Arial','',8);

                        while($d = $sql_2->fetch()){
                            if($data['mt'] > 0){
                                if(intval($d['mp'] * 100 / $data['mt']) >= intval($_GET['m_min'])){
                                    $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                                    $pdf->cell(90, 5, mm($data_student['noms']), 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                    $pdf->cell(22, 5, '$ '.mm($data['mt']-$d['mp']), 1, 0, 'L');
                                    $pdf->cell(10, 5, montant_restant_pourcent($d['mp'], $data['mt']).'%', 1, 0, 'L');
                                    $pdf->Ln(5);
                                }else{
                                    $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                                    $pdf->SetFillColor(20,20,20);
                                    $pdf->cell(90, 5, mm($data_student['noms']), 1, 0, 'L');
                                    $pdf->SetTextColor(255,255,255);
                                    $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L', true);
                                    $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L', true);
                                    $pdf->cell(22, 5, '$ '.mm($data['mt']-$d['mp']), 1, 0, 'L', true);
                                    $pdf->cell(10, 5, montant_restant_pourcent($d['mp'], $data['mt']).'%', 1, 0, 'L', true);
                                    $pdf->SetTextColor(1,1,1);
                                    $pdf->Ln(5);
                                }
                            }
                        }
                    }
                }
            }
        }else if($_GET['pr'] == "Toute les promotions" && $_GET['fac'] != "Toute les facultés"){
            // --------------------------
            // on selectionne les info dans la table des etudiants
            $sel_all = ConnexionBdd::Connecter()->prepare("SELECT fac, promotion, noms, matricule,annee_academique FROM etudiants_inscrits WHERE fac = ? AND annee_academique = ?");
            $sel_all->execute(array($_GET['fac'], $_GET['a']));

            while($data_student = $sel_all->fetch()){
                // $pdf->cell(60, 5, 'Promotion : '.$data_student['promotion'], 0, 1, 'L');
                $sql = "SELECT faculte, promotion, type_frais, SUM(montant) as mt FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ?";
                $sql_array = array($data_student['matricule'], $data_student['promotion'], $data_student['fac'], $data_student['annee_academique']);
                
                // $pdf->Ln(3);
                $all = ConnexionBdd::Connecter()->prepare($sql);
                $all->execute($sql_array);
                
                while ($data = $all->fetch()){
                    $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ? AND type_frais = ? AND annee_acad = ?";

                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                    $sql_2->execute(array($data_student['matricule'], $data['faculte'], $data['promotion'], $data['type_frais'], $_GET['a'])); 

                    $pdf->SetFont('Arial','',8);

                    while($d = $sql_2->fetch()){
                        if(intval($data['mt']) > 0){
                            if(intval($d['mp'] * 100 / $data['mt']) >= intval($_GET['m_min'])){
                                $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                                $pdf->cell(90, 5, mm($data_student['noms']), 1, 0, 'L');
                                $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L');
                                $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                $pdf->cell(22, 5, '$ '.mm($data['mt']-$d['mp']), 1, 0, 'L');
                                $pdf->cell(10, 5, montant_restant_pourcent($d['mp'], $data['mt']).'%', 1, 0, 'L');
                                $pdf->Ln(5);
                            }else{
                                $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                                $pdf->cell(90, 5, mm($data_student['noms']), 1, 0, 'L');
                                $pdf->SetTextColor(255,255,255);
                                $pdf->SetFillColor(22,22,22);
                                $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L', true);
                                $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L', true);
                                $pdf->cell(22, 5, '$ '.mm($data['mt']-$d['mp']), 1, 0, 'L', true);
                                $pdf->cell(10, 5, montant_restant_pourcent($d['mp'], $data['mt']).'%', 1, 0, 'L', true);
                                $pdf->Ln(5);
                                $pdf->SetTextColor(1,1,1);
                            }
                        }
                    }
                }
            }
        }else if($_GET['pr'] != "Toute les promotions" && $_GET['fac'] != "Toute les facultés"){
            // on selectionne les info dans la table des etudiants
            $sel_all = ConnexionBdd::Connecter()->prepare("SELECT fac, promotion, noms, matricule,annee_academique FROM etudiants_inscrits WHERE fac = ? AND promotion = ? AND annee_academique = ?");
            $sel_all->execute(array($_GET['fac'], $_GET['pr'], $_GET['a']));
            entete($pdf);

            while($data_student = $sel_all->fetch()){
                $sql = "SELECT SUM(montant) as mt FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ?";
                $sql_array = array($data_student['matricule'], $data_student['promotion'], $data_student['fac'], $data_student['annee_academique']);

                $all = ConnexionBdd::Connecter()->prepare($sql);
                $all->execute($sql_array);
                $data = $all->fetch();

                // montant deja payer par l'etudiant
                $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ? AND annee_acad = ?";

                $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                $sql_2->execute(array($data_student['matricule'], $data_student['fac'], $data_student['promotion'], $_GET['a'])); 

                $pdf->SetFont('Arial','',8);

                $d = $sql_2->fetch();

                if(intval($data['mt'])){
                    if(intval($d['mp'] * 100 / $data['mt']) >= intval($_GET['m_min'])){
                        $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                        $pdf->cell(90, 5, mm($data_student['noms']), 1, 0, 'L');
                        $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L');
                        $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                        $pdf->cell(22, 5, '$ '.mm($data['mt'] - $d['mp']), 1, 0, 'L');
                        $pdf->cell(10, 5, montant_restant_pourcent($d['mp'], $data['mt']).'%', 1, 0, 'L');
                        $pdf->Ln(5);
                    }else{
                        $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                        $pdf->cell(90, 5, mm($data_student['noms']), 1, 0, 'L');
                        $pdf->SetTextColor(255,255,255);
                        $pdf->SetFillColor(22,22,22);
                        $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L', true);
                        $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L', true);
                        $pdf->cell(22, 5, '$ '.mm($data['mt'] - $d['mp']), 1, 0, 'L', true);
                        $pdf->cell(10, 5, montant_restant_pourcent($d['mp'], $data['mt']).'%', 1, 0, 'L', true);
                        $pdf->Ln(5);
                        // $pdf->SetTextColor(1,1,1);
                    }
                }
            }
        }
    }
    // annee acad - promotion - fac - pourcent max. 
    else if(!empty($_GET['a']) && !empty($_GET['pr']) && !empty($_GET['fac']) && $_GET['ch_min'] == "on" && $_GET['al_fc'] =="on" && $_GET['ch_date'] == "on" && $_GET['ch_max'] == "_max_" && !empty($_GET['m_max'])){
        $pdf->SetFont('Arial','',8);
        $pdf->SetTextColor(0,0,0);
        $pdf->cell(60, 5, 'Annee Academique  : '.decode_fr($_GET['a']), 0, 1, 'L');
        $pdf->cell(60, 5, 'Promotion                 : '.decode_fr($_GET['pr']), 0, 1, 'L');
        $pdf->cell(60, 5, 'Faculte                      : '.decode_fr($_GET['fac']), 0, 1, 'L');
        $pdf->cell(60, 5, 'Type de frais             : Tous', 0, 1, 'L');
        $pdf->cell(60, 5, decode_fr('Date de coupure       : '), 0, 1, 'L');
        $pdf->cell(60, 5, decode_fr('Poucentage min.       : '), 0, 1, 'L');
        $pdf->cell(60, 5, decode_fr('Poucentage max.      : '.$_GET['m_max'].'%'), 0, 1, 'L');
        $pdf->Ln(3);
        // les putains d'options commmmmmmmmmmmmmmmmmmmmmmmmmmmmence /!\
        if($_GET['pr'] == "Toute les promotions" && $_GET['fac'] == "Toute les facultés"){
            $sel_all = ConnexionBdd::Connecter()->prepare("SELECT * FROM etudiants_inscrits WHERE annee_academique = ?");
            $sel_all->execute(array($_GET['a']));

            $pdf->SetFont('Arial','B',8);

            // tableau pour les facultes et les promotions
            $a_promotion = array();
            $a_faculte  = array();

            while($data_student = $sel_all->fetch()){
                if(!in_array($data_student['fac'], $a_faculte)){
                    $a_faculte[] = $data_student['fac'];
                }

                if(!in_array($data_student['promotion'], $a_promotion)){
                    $a_promotion[] = $data_student['promotion'];
                }
            }

            foreach($a_faculte as $f){
                foreach($a_promotion as $p){
                    $pdf->Ln(5);
                    $pdf->cell(60, 5, 'Faculte         : '.decode_fr($f), 0, 1, 'L');
                    $pdf->cell(60, 5, 'Promotion     : '.decode_fr($p), 0, 1, 'L');
                    $pdf->cell(60, 5, 'Annee Acad. : '.decode_fr($_GET['a']), 0, 1, 'L');
                    $pdf->Ln(1);
                    $sel_all = ConnexionBdd::Connecter()->prepare("SELECT * FROM etudiants_inscrits WHERE fac = ? AND promotion  = ? AND annee_academique = ?");
                    $sel_all->execute(array($f, $p, $_GET['a']));

                    $nn = $sel_all->rowCount();
                    if($nn > 0){
                        $pdf->SetFont('Arial','B',8);
                        $pdf->cell(20, 5, "Matricule" , 1, 0, 'L');
                        $pdf->cell(60, 5, "Noms" , 1, 0, 'L');
                        $pdf->cell(25, 5, "Montant prevu", 1, 0, 'L');
                        $pdf->cell(25, 5, "montant payer", 1, 0, 'L');
                        $pdf->cell(22, 5, "Solde", 1, 0, 'L');
                        $pdf->cell(22, 5, "%", 1, 0, 'L');
                        $pdf->Ln(5);
                        while($data_student = $sel_all->fetch()){
                            // le titre
                            $sql = "SELECT SUM(montant) as mt FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ? GROUP BY faculte";
                            $sql_array = array($data_student['matricule'], $data_student['promotion'], $data_student['fac'],$data_student['annee_academique']);
                                                                        
                            $all = ConnexionBdd::Connecter()->prepare($sql);
                            $all->execute($sql_array);

                            $n = $all->rowCount();
                            if($n > 0){
                                while ($data = $all->fetch()){
                                    $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ? AND annee_acad = ?";
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute(array($data_student['matricule'], $data_student['fac'], $data_student['promotion'], $data_student['annee_academique'])); 
                                    $pdf->SetFont('Arial','',8);
                                    while($d = $sql_2->fetch()){
                                        if(intval(($d['mp'] * 100)/$data['mt']) >= intval($_GET['m_max'])){
                                            $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                                            $pdf->cell(60, 5, ucfirst(decode_fr($data_student['noms'])) , 1, 0, 'L');
                                            $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L');
                                            $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                            $pdf->cell(22, 5, '$ '.mm($data['mt']-$d['mp']), 1, 0, 'L');
                                            $pdf->cell(22, 5, montant_restant_pourcent($d['mp'], $data['mt']).'%', 1, 0, 'L');
                                            // $pdf->Ln(0);
                                        }else{
                                            $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                                            $pdf->cell(60, 5, ucfirst(decode_fr($data_student['noms'])) , 1, 0, 'L');
                                            $pdf->SetTextColor(255,255,255);
                                            $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L', true);
                                            $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L', true);
                                            $pdf->cell(22, 5, '$ '.mm($data['mt']-$d['mp']), 1, 0, 'L', true);
                                            $pdf->cell(22, 5, montant_restant_pourcent($d['mp'], $data['mt']).'%', 1, 0, 'L', true);
                                            $pdf->SetTextColor(11,11,11);
                                        }
                                    }
                                }
                                $pdf->Ln(5);
                            }else{
                                $pdf->SetFont('Arial','I',8);
                                $pdf->SetTextColor(166, 10, 10);
                                $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                                $pdf->cell(60, 5, ucfirst(decode_fr($data_student['noms'])) , 1, 0, 'L');
                                $pdf->cell(25, 5, mm(""), 1, 0, 'L');
                                $pdf->cell(25, 5, mm(""), 1, 0, 'L');
                                $pdf->cell(22, 5, mm(""), 1, 0, 'L');
                                $pdf->cell(22, 5, " # ", 1, 0, 'L');
                                $pdf->Ln(5);
                                $pdf->SetTextColor(0, 0, 0);
                            }
                        }
                    }else{
                        $pdf->SetTextColor(200, 12, 12);
                        $pdf->SetFont('Arial','B',10);
                        $pdf->cell(60, 5, decode_fr('Auncun etudiant n\'est inscrit en : '.$p.' '.$f.' dans l\'annee acad.: '.$_GET['a']), 0, 1, 'L');
                        $pdf->SetTextColor(0,0,0);
                        $pdf->SetFont('Arial','',8);
                        $pdf->Ln(3);
                    }
                }
                $pdf->Ln(5);
            }
        }else if($_GET['pr'] != "Toute les promotions" && $_GET['fac'] == "Toute les facultés"){
            // on selectionne les info dans la table des etudiants
            $sel_all = ConnexionBdd::Connecter()->prepare("SELECT fac, promotion, noms, matricule,annee_academique FROM etudiants_inscrits WHERE promotion = ? AND annee_academique = ? GROUP BY matricule");
            $sel_all->execute(array($_GET['pr'], $_GET['a']));

            $lst_fac = array();

            while($data_student = $sel_all->fetch()){
                // append dans la tableau
                if(!in_array($data_student['fac'], $lst_fac)){
                    $lst_fac[] = $data_student['fac'];
                }
            }

            foreach($lst_fac as $fac){
                $pdf->Ln(5);
                $pdf->cell(20, 5, ucfirst(decode_fr('faculté : '.$fac)) , 0, 0, 'L');
                $pdf->Ln(5);
                $pdf->cell(20, 5, ucfirst(decode_fr('promotion : '.$_GET['pr'])) , 0, 0, 'L');
                $pdf->Ln(5);
                $pdf->cell(20, 5, ucfirst(decode_fr('Annee Academique : '.$_GET['a'])) , 0, 0, 'L');
                $pdf->Ln(5);

                entete($pdf);
                $sel_all = ConnexionBdd::Connecter()->prepare("SELECT fac, promotion, noms, matricule,annee_academique FROM etudiants_inscrits WHERE promotion = ? AND fac = ? AND annee_academique = ?");
                $sel_all->execute(array($_GET['pr'], $fac, $_GET['a']));

                while($data_student = $sel_all->fetch()){
                    $sql = "SELECT faculte, promotion, type_frais, SUM(montant) as mt FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ?";
                    $sql_array = array($data_student['matricule'], $data_student['promotion'], $fac, $data_student['annee_academique']);
                    $all = ConnexionBdd::Connecter()->prepare($sql);
                    $all->execute($sql_array);
                    
                    while ($data = $all->fetch()){
                        $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ? AND annee_acad = ?";

                        $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                        $sql_2->execute(array($data_student['matricule'], $fac, $data['promotion'], $_GET['a'])); 

                        $pdf->SetFont('Arial','',8);

                        while($d = $sql_2->fetch()){
                            if($data['mt'] > 0){
                                if(intval($d['mp'] * 100 / $data['mt']) >= intval($_GET['m_max'])){
                                    $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                                    $pdf->cell(90, 5, mm($data_student['noms']), 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                    $pdf->cell(22, 5, '$ '.mm($data['mt']-$d['mp']), 1, 0, 'L');
                                    $pdf->cell(10, 5, montant_restant_pourcent($d['mp'], $data['mt']).'%', 1, 0, 'L');
                                    $pdf->Ln(5);
                                }else{
                                    $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                                    $pdf->SetFillColor(20,20,20);
                                    $pdf->cell(90, 5, mm($data_student['noms']), 1, 0, 'L');
                                    $pdf->SetTextColor(255,255,255);
                                    $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L', true);
                                    $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L', true);
                                    $pdf->cell(22, 5, '$ '.mm($data['mt']-$d['mp']), 1, 0, 'L', true);
                                    $pdf->cell(10, 5, montant_restant_pourcent($d['mp'], $data['mt']).'%', 1, 0, 'L', true);
                                    $pdf->SetTextColor(1,1,1);
                                    $pdf->Ln(5);
                                }
                            }
                        }
                    }
                }
            }
        }else if($_GET['pr'] == "Toute les promotions" && $_GET['fac'] != "Toute les facultés"){
            // --------------------------
            // on selectionne les info dans la table des etudiants
            $sel_all = ConnexionBdd::Connecter()->prepare("SELECT fac, promotion, noms, matricule,annee_academique FROM etudiants_inscrits WHERE fac = ? AND annee_academique = ?");
            $sel_all->execute(array($_GET['fac'], $_GET['a']));

            while($data_student = $sel_all->fetch()){
                // $pdf->cell(60, 5, 'Promotion : '.$data_student['promotion'], 0, 1, 'L');
                $sql = "SELECT faculte, promotion, type_frais, SUM(montant) as mt FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ?";
                $sql_array = array($data_student['matricule'], $data_student['promotion'], $data_student['fac'], $data_student['annee_academique']);
                
                // $pdf->Ln(3);
                $all = ConnexionBdd::Connecter()->prepare($sql);
                $all->execute($sql_array);
                
                while ($data = $all->fetch()){
                    $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ? AND type_frais = ? AND annee_acad = ?";

                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                    $sql_2->execute(array($data_student['matricule'], $data['faculte'], $data['promotion'], $data['type_frais'], $_GET['a'])); 

                    $pdf->SetFont('Arial','',8);

                    while($d = $sql_2->fetch()){
                        if(intval($data['mt']) > 0){
                            if(intval($d['mp'] * 100 / $data['mt']) >= intval($_GET['m_max'])){
                                $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                                $pdf->cell(90, 5, mm($data_student['noms']), 1, 0, 'L');
                                $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L');
                                $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                $pdf->cell(22, 5, '$ '.mm($data['mt']-$d['mp']), 1, 0, 'L');
                                $pdf->cell(10, 5, montant_restant_pourcent($d['mp'], $data['mt']).'%', 1, 0, 'L');
                                $pdf->Ln(5);
                            }else{
                                $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                                $pdf->cell(90, 5, mm($data_student['noms']), 1, 0, 'L');
                                $pdf->SetTextColor(255,255,255);
                                $pdf->SetFillColor(22,22,22);
                                $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L', true);
                                $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L', true);
                                $pdf->cell(22, 5, '$ '.mm($data['mt']-$d['mp']), 1, 0, 'L', true);
                                $pdf->cell(10, 5, montant_restant_pourcent($d['mp'], $data['mt']).'%', 1, 0, 'L', true);
                                $pdf->Ln(5);
                                $pdf->SetTextColor(1,1,1);
                            }
                        }
                    }
                }
            }
        }else if($_GET['pr'] != "Toute les promotions" && $_GET['fac'] != "Toute les facultés"){
            // on selectionne les info dans la table des etudiants
            $sel_all = ConnexionBdd::Connecter()->prepare("SELECT fac, promotion, noms, matricule,annee_academique FROM etudiants_inscrits WHERE fac = ? AND promotion = ? AND annee_academique = ?");
            $sel_all->execute(array($_GET['fac'], $_GET['pr'], $_GET['a']));
            entete($pdf);

            while($data_student = $sel_all->fetch()){
                $sql = "SELECT SUM(montant) as mt FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ?";
                $sql_array = array($data_student['matricule'], $data_student['promotion'], $data_student['fac'], $data_student['annee_academique']);

                $all = ConnexionBdd::Connecter()->prepare($sql);
                $all->execute($sql_array);
                $data = $all->fetch();

                // montant deja payer par l'etudiant
                $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ? AND annee_acad = ?";

                $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                $sql_2->execute(array($data_student['matricule'], $data_student['fac'], $data_student['promotion'], $_GET['a'])); 

                $pdf->SetFont('Arial','',8);

                $d = $sql_2->fetch();

                if(intval($data['mt'])){
                    if(intval($d['mp'] * 100 / $data['mt']) >= intval($_GET['m_max'])){
                        $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                        $pdf->cell(90, 5, mm($data_student['noms']), 1, 0, 'L');
                        $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L');
                        $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                        $pdf->cell(22, 5, '$ '.mm($data['mt'] - $d['mp']), 1, 0, 'L');
                        $pdf->cell(10, 5, montant_restant_pourcent($d['mp'], $data['mt']).'%', 1, 0, 'L');
                        $pdf->Ln(5);
                    }else{
                        $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                        $pdf->cell(90, 5, mm($data_student['noms']), 1, 0, 'L');
                        $pdf->SetTextColor(255,255,255);
                        $pdf->SetFillColor(22,22,22);
                        $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L', true);
                        $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L', true);
                        $pdf->cell(22, 5, '$ '.mm($data['mt'] - $d['mp']), 1, 0, 'L', true);
                        $pdf->cell(10, 5, montant_restant_pourcent($d['mp'], $data['mt']).'%', 1, 0, 'L', true);
                        $pdf->Ln(5);
                        // $pdf->SetTextColor(1,1,1);
                    }
                }
            }
        }
    }
    // annee acad - promotion - fac - pourcent min - pourcentage max. 
    else if(!empty($_GET['a']) && !empty($_GET['pr']) && !empty($_GET['fac']) && $_GET['al_fc'] =="on" && $_GET['ch_date'] == "on" && $_GET['ch_max'] == "_max_" && $_GET['ch_min'] == "_min_"){
        $pdf->SetFont('Arial','',8);
        $pdf->SetTextColor(0,0,0);
        $pdf->cell(60, 5, 'Annee Academique  : '.decode_fr($_GET['a']), 0, 1, 'L');
        $pdf->cell(60, 5, 'Promotion                 : '.decode_fr($_GET['pr']), 0, 1, 'L');
        $pdf->cell(60, 5, 'Faculte                      : '.decode_fr($_GET['fac']), 0, 1, 'L');
        $pdf->cell(60, 5, 'Type de frais             : Tous', 0, 1, 'L');
        $pdf->cell(60, 5, decode_fr('Date de coupure       : '), 0, 1, 'L');
        $pdf->cell(60, 5, decode_fr('Poucentage min.       : '.$_GET['m_min'].'%'), 0, 1, 'L');
        $pdf->cell(60, 5, decode_fr('Poucentage max.      : '.$_GET['m_max'].'%'), 0, 1, 'L');
        $pdf->Ln(3);
        // les putains d'options commmmmmmmmmmmmmmmmmmmmmmmmmmmmence /!\
        if($_GET['pr'] == "Toute les promotions" && $_GET['fac'] == "Toute les facultés"){
            $sel_all = ConnexionBdd::Connecter()->prepare("SELECT * FROM etudiants_inscrits WHERE annee_academique = ?");
            $sel_all->execute(array($_GET['a']));

            $pdf->SetFont('Arial','B',8);

            // tableau pour les facultes et les promotions
            $a_promotion = array();
            $a_faculte  = array();

            while($data_student = $sel_all->fetch()){
                if(!in_array($data_student['fac'], $a_faculte)){
                    $a_faculte[] = $data_student['fac'];
                }

                if(!in_array($data_student['promotion'], $a_promotion)){
                    $a_promotion[] = $data_student['promotion'];
                }
            }

            foreach($a_faculte as $f){
                foreach($a_promotion as $p){
                    $pdf->Ln(5);
                    $pdf->cell(60, 5, 'Faculte         : '.decode_fr($f), 0, 1, 'L');
                    $pdf->cell(60, 5, 'Promotion     : '.decode_fr($p), 0, 1, 'L');
                    $pdf->cell(60, 5, 'Annee Acad. : '.decode_fr($_GET['a']), 0, 1, 'L');
                    $pdf->Ln(1);
                    $sel_all = ConnexionBdd::Connecter()->prepare("SELECT * FROM etudiants_inscrits WHERE fac = ? AND promotion  = ? AND annee_academique = ?");
                    $sel_all->execute(array($f, $p, $_GET['a']));

                    $nn = $sel_all->rowCount();
                    if($nn > 0){
                        $pdf->SetFont('Arial','B',8);
                        $pdf->cell(20, 5, "Matricule" , 1, 0, 'L');
                        $pdf->cell(60, 5, "Noms" , 1, 0, 'L');
                        $pdf->cell(25, 5, "Montant prevu", 1, 0, 'L');
                        $pdf->cell(25, 5, "montant payer", 1, 0, 'L');
                        $pdf->cell(22, 5, "Solde", 1, 0, 'L');
                        $pdf->cell(22, 5, "%", 1, 0, 'L');
                        $pdf->Ln(5);
                        while($data_student = $sel_all->fetch()){
                            // le titre
                            $sql = "SELECT SUM(montant) as mt FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ? GROUP BY faculte";
                            $sql_array = array($data_student['matricule'], $data_student['promotion'], $data_student['fac'],$data_student['annee_academique']);
                                                                        
                            $all = ConnexionBdd::Connecter()->prepare($sql);
                            $all->execute($sql_array);

                            $n = $all->rowCount();
                            if($n > 0){
                                while ($data = $all->fetch()){
                                    $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ? AND annee_acad = ?";
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute(array($data_student['matricule'], $data_student['fac'], $data_student['promotion'], $data_student['annee_academique'])); 
                                    $pdf->SetFont('Arial','',8);
                                    //intval(($d['mp'] * 100)/$data['mt']) >= intval($_GET['m_min']) && intval(($d['mp'] * 100)/$data['mt']) <= intval($_GET['m_max'])
                                    while($d = $sql_2->fetch()){
                                        if(intval(($d['mp'] * 100)/$data['mt']) >= intval($_GET['m_min']) && intval(($d['mp'] * 100)/$data['mt']) <= intval($_GET['m_max'])){
                                            $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                                            $pdf->cell(60, 5, ucfirst(decode_fr($data_student['noms'])) , 1, 0, 'L');
                                            $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L');
                                            $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                            $pdf->cell(22, 5, '$ '.mm($data['mt']-$d['mp']), 1, 0, 'L');
                                            $pdf->cell(22, 5, montant_restant_pourcent($d['mp'], $data['mt']).'%', 1, 0, 'L');
                                            // $pdf->Ln(0);
                                        }else{
                                            $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                                            $pdf->cell(60, 5, ucfirst(decode_fr($data_student['noms'])) , 1, 0, 'L');
                                            $pdf->SetTextColor(255,255,255);
                                            $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L', true);
                                            $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L', true);
                                            $pdf->cell(22, 5, '$ '.mm($data['mt']-$d['mp']), 1, 0, 'L', true);
                                            $pdf->cell(22, 5, montant_restant_pourcent($d['mp'], $data['mt']).'%', 1, 0, 'L', true);
                                            $pdf->SetTextColor(11,11,11);
                                        }
                                    }
                                }
                                $pdf->Ln(5);
                            }else{
                                $pdf->SetFont('Arial','I',8);
                                $pdf->SetTextColor(166, 10, 10);
                                $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                                $pdf->cell(60, 5, ucfirst(decode_fr($data_student['noms'])) , 1, 0, 'L');
                                $pdf->cell(25, 5, mm(""), 1, 0, 'L');
                                $pdf->cell(25, 5, mm(""), 1, 0, 'L');
                                $pdf->cell(22, 5, mm(""), 1, 0, 'L');
                                $pdf->cell(22, 5, " # ", 1, 0, 'L');
                                $pdf->Ln(5);
                                $pdf->SetTextColor(0, 0, 0);
                            }
                        }
                    }else{
                        $pdf->SetTextColor(200, 12, 12);
                        $pdf->SetFont('Arial','B',10);
                        $pdf->cell(60, 5, decode_fr('Auncun etudiant n\'est inscrit en : '.$p.' '.$f.' dans l\'annee acad.: '.$_GET['a']), 0, 1, 'L');
                        $pdf->SetTextColor(0,0,0);
                        $pdf->SetFont('Arial','',8);
                        $pdf->Ln(3);
                    }
                }
                $pdf->Ln(5);
            }
        }else if($_GET['pr'] != "Toute les promotions" && $_GET['fac'] == "Toute les facultés"){
            // on selectionne les info dans la table des etudiants
            $sel_all = ConnexionBdd::Connecter()->prepare("SELECT fac, promotion, noms, matricule,annee_academique FROM etudiants_inscrits WHERE promotion = ? AND annee_academique = ? GROUP BY matricule");
            $sel_all->execute(array($_GET['pr'], $_GET['a']));

            $lst_fac = array();

            while($data_student = $sel_all->fetch()){
                // append dans la tableau
                if(!in_array($data_student['fac'], $lst_fac)){
                    $lst_fac[] = $data_student['fac'];
                }
            }

            foreach($lst_fac as $fac){
                $pdf->Ln(5);
                $pdf->cell(20, 5, ucfirst(decode_fr('faculté : '.$fac)) , 0, 0, 'L');
                $pdf->Ln(5);
                $pdf->cell(20, 5, ucfirst(decode_fr('promotion : '.$_GET['pr'])) , 0, 0, 'L');
                $pdf->Ln(5);
                $pdf->cell(20, 5, ucfirst(decode_fr('Annee Academique : '.$_GET['a'])) , 0, 0, 'L');
                $pdf->Ln(5);

                entete($pdf);
                $sel_all = ConnexionBdd::Connecter()->prepare("SELECT fac, promotion, noms, matricule,annee_academique FROM etudiants_inscrits WHERE promotion = ? AND fac = ? AND annee_academique = ?");
                $sel_all->execute(array($_GET['pr'], $fac, $_GET['a']));

                while($data_student = $sel_all->fetch()){
                    $sql = "SELECT faculte, promotion, type_frais, SUM(montant) as mt FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ?";
                    $sql_array = array($data_student['matricule'], $data_student['promotion'], $fac, $data_student['annee_academique']);
                    $all = ConnexionBdd::Connecter()->prepare($sql);
                    $all->execute($sql_array);
                    
                    while ($data = $all->fetch()){
                        $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ? AND annee_acad = ?";

                        $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                        $sql_2->execute(array($data_student['matricule'], $fac, $data['promotion'], $_GET['a'])); 

                        $pdf->SetFont('Arial','',8);

                        while($d = $sql_2->fetch()){
                            if($data['mt'] > 0){
                                if(intval($d['mp'] * 100 / $data['mt']) >= intval($_GET['m_max'])){
                                    $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                                    $pdf->cell(90, 5, mm($data_student['noms']), 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                    $pdf->cell(22, 5, '$ '.mm($data['mt']-$d['mp']), 1, 0, 'L');
                                    $pdf->cell(10, 5, montant_restant_pourcent($d['mp'], $data['mt']).'%', 1, 0, 'L');
                                    $pdf->Ln(5);
                                }else{
                                    $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                                    $pdf->SetFillColor(20,20,20);
                                    $pdf->cell(90, 5, mm($data_student['noms']), 1, 0, 'L');
                                    $pdf->SetTextColor(255,255,255);
                                    $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L', true);
                                    $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L', true);
                                    $pdf->cell(22, 5, '$ '.mm($data['mt']-$d['mp']), 1, 0, 'L', true);
                                    $pdf->cell(10, 5, montant_restant_pourcent($d['mp'], $data['mt']).'%', 1, 0, 'L', true);
                                    $pdf->SetTextColor(1,1,1);
                                    $pdf->Ln(5);
                                }
                            }
                        }
                    }
                }
            }
        }else if($_GET['pr'] == "Toute les promotions" && $_GET['fac'] != "Toute les facultés"){
            // --------------------------
            // on selectionne les info dans la table des etudiants
            $sel_all = ConnexionBdd::Connecter()->prepare("SELECT fac, promotion, noms, matricule,annee_academique FROM etudiants_inscrits WHERE fac = ? AND annee_academique = ?");
            $sel_all->execute(array($_GET['fac'], $_GET['a']));

            while($data_student = $sel_all->fetch()){
                // $pdf->cell(60, 5, 'Promotion : '.$data_student['promotion'], 0, 1, 'L');
                $sql = "SELECT faculte, promotion, type_frais, SUM(montant) as mt FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ?";
                $sql_array = array($data_student['matricule'], $data_student['promotion'], $data_student['fac'], $data_student['annee_academique']);
                
                // $pdf->Ln(3);
                $all = ConnexionBdd::Connecter()->prepare($sql);
                $all->execute($sql_array);
                
                while ($data = $all->fetch()){
                    $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ? AND type_frais = ? AND annee_acad = ?";

                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                    $sql_2->execute(array($data_student['matricule'], $data['faculte'], $data['promotion'], $data['type_frais'], $_GET['a'])); 

                    $pdf->SetFont('Arial','',8);

                    while($d = $sql_2->fetch()){
                        if(intval($data['mt']) > 0){
                            if(intval($d['mp'] * 100 / $data['mt']) >= intval($_GET['m_max'])){
                                $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                                $pdf->cell(90, 5, mm($data_student['noms']), 1, 0, 'L');
                                $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L');
                                $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                $pdf->cell(22, 5, '$ '.mm($data['mt']-$d['mp']), 1, 0, 'L');
                                $pdf->cell(10, 5, montant_restant_pourcent($d['mp'], $data['mt']).'%', 1, 0, 'L');
                                $pdf->Ln(5);
                            }else{
                                $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                                $pdf->cell(90, 5, mm($data_student['noms']), 1, 0, 'L');
                                $pdf->SetTextColor(255,255,255);
                                $pdf->SetFillColor(22,22,22);
                                $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L', true);
                                $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L', true);
                                $pdf->cell(22, 5, '$ '.mm($data['mt']-$d['mp']), 1, 0, 'L', true);
                                $pdf->cell(10, 5, montant_restant_pourcent($d['mp'], $data['mt']).'%', 1, 0, 'L', true);
                                $pdf->Ln(5);
                                $pdf->SetTextColor(1,1,1);
                            }
                        }
                    }
                }
            }
        }else if($_GET['pr'] != "Toute les promotions" && $_GET['fac'] != "Toute les facultés"){
            // on selectionne les info dans la table des etudiants
            $sel_all = ConnexionBdd::Connecter()->prepare("SELECT fac, promotion, noms, matricule,annee_academique FROM etudiants_inscrits WHERE fac = ? AND promotion = ? AND annee_academique = ?");
            $sel_all->execute(array($_GET['fac'], $_GET['pr'], $_GET['a']));
            entete($pdf);

            while($data_student = $sel_all->fetch()){
                $sql = "SELECT SUM(montant) as mt FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ?";
                $sql_array = array($data_student['matricule'], $data_student['promotion'], $data_student['fac'], $data_student['annee_academique']);

                $all = ConnexionBdd::Connecter()->prepare($sql);
                $all->execute($sql_array);
                $data = $all->fetch();

                // montant deja payer par l'etudiant
                $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ? AND annee_acad = ?";

                $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                $sql_2->execute(array($data_student['matricule'], $data_student['fac'], $data_student['promotion'], $_GET['a'])); 

                $pdf->SetFont('Arial','',8);

                $d = $sql_2->fetch();

                if(intval($data['mt'])){
                    if(intval($d['mp'] * 100 / $data['mt']) >= intval($_GET['m_max'])){
                        $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                        $pdf->cell(90, 5, mm($data_student['noms']), 1, 0, 'L');
                        $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L');
                        $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                        $pdf->cell(22, 5, '$ '.mm($data['mt'] - $d['mp']), 1, 0, 'L');
                        $pdf->cell(10, 5, montant_restant_pourcent($d['mp'], $data['mt']).'%', 1, 0, 'L');
                        $pdf->Ln(5);
                    }else{
                        $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                        $pdf->cell(90, 5, mm($data_student['noms']), 1, 0, 'L');
                        $pdf->SetTextColor(255,255,255);
                        $pdf->SetFillColor(22,22,22);
                        $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L', true);
                        $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L', true);
                        $pdf->cell(22, 5, '$ '.mm($data['mt'] - $d['mp']), 1, 0, 'L', true);
                        $pdf->cell(10, 5, montant_restant_pourcent($d['mp'], $data['mt']).'%', 1, 0, 'L', true);
                        $pdf->Ln(5);
                        $pdf->SetTextColor(1,1,1);
                    }
                }
            }
        }
    }
    else if(!empty($_GET['a']) && !empty($_GET['pr']) && !empty($_GET['fac']) && $_GET['al_fc'] =="on" && $_GET['ch_max'] == "_max_" && $_GET['ch_min'] == "_min_" && $_GET['ch_date'] == "date_ok"){
        $pdf->SetFont('Arial','',8);
        $pdf->SetTextColor(0,0,0);
        $pdf->cell(60, 5, 'Annee Academique  : '.decode_fr($_GET['a']), 0, 1, 'L');
        $pdf->cell(60, 5, 'Promotion                 : '.decode_fr($_GET['pr']), 0, 1, 'L');
        $pdf->cell(60, 5, 'Faculte                      : '.decode_fr($_GET['fac']), 0, 1, 'L');
        $pdf->cell(60, 5, 'Type de frais             : Tous', 0, 1, 'L');
        $pdf->cell(60, 5, decode_fr('Date de coupure   : Du '.date("d F Y", strtotime(strtolower($_GET['d1']))).' au '.date("d F Y", strtotime(strtolower($_GET['d2'])))), 0, 1, 'L');
        $pdf->cell(60, 5, decode_fr('Poucentage min.       : '.$_GET['m_min'].'%'), 0, 1, 'L');
        $pdf->cell(60, 5, decode_fr('Poucentage max.      : '.$_GET['m_max'].'%'), 0, 1, 'L');
        $pdf->Ln(3);
        // les putains d'options commmmmmmmmmmmmmmmmmmmmmmmmmmmmence /!\
        if($_GET['pr'] == "Toute les promotions" && $_GET['fac'] == "Toute les facultés"){
            $sel_all = ConnexionBdd::Connecter()->prepare("SELECT * FROM etudiants_inscrits WHERE annee_academique = ?");
            $sel_all->execute(array($_GET['a']));

            $pdf->SetFont('Arial','B',8);

            // tableau pour les facultes et les promotions
            $a_promotion = array();
            $a_faculte  = array();

            while($data_student = $sel_all->fetch()){
                if(!in_array($data_student['fac'], $a_faculte)){
                    $a_faculte[] = $data_student['fac'];
                }

                if(!in_array($data_student['promotion'], $a_promotion)){
                    $a_promotion[] = $data_student['promotion'];
                }
            }

            foreach($a_faculte as $f){
                foreach($a_promotion as $p){
                    $pdf->Ln(5);
                    $pdf->cell(60, 5, 'Faculte         : '.decode_fr($f), 0, 1, 'L');
                    $pdf->cell(60, 5, 'Promotion     : '.decode_fr($p), 0, 1, 'L');
                    $pdf->cell(60, 5, 'Annee Acad. : '.decode_fr($_GET['a']), 0, 1, 'L');
                    $pdf->Ln(1);
                    $sel_all = ConnexionBdd::Connecter()->prepare("SELECT * FROM etudiants_inscrits WHERE fac = ? AND promotion  = ? AND annee_academique = ?");
                    $sel_all->execute(array($f, $p, $_GET['a']));

                    $nn = $sel_all->rowCount();
                    if($nn > 0){
                        $pdf->SetFont('Arial','B',8);
                        $pdf->cell(20, 5, "Matricule" , 1, 0, 'L');
                        $pdf->cell(60, 5, "Noms" , 1, 0, 'L');
                        $pdf->cell(25, 5, "Montant prevu", 1, 0, 'L');
                        $pdf->cell(25, 5, "montant payer", 1, 0, 'L');
                        $pdf->cell(22, 5, "Solde", 1, 0, 'L');
                        $pdf->cell(22, 5, "%", 1, 0, 'L');
                        $pdf->Ln(5);
                        while($data_student = $sel_all->fetch()){
                            // le titre
                            $sql = "SELECT SUM(montant) as mt FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ? GROUP BY faculte";
                            $sql_array = array($data_student['matricule'], $data_student['promotion'], $data_student['fac'],$data_student['annee_academique']);
                                                                        
                            $all = ConnexionBdd::Connecter()->prepare($sql);
                            $all->execute($sql_array);

                            $n = $all->rowCount();
                            if($n > 0){
                                while ($data = $all->fetch()){
                                    $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ? AND annee_acad = ?";
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute(array($data_student['matricule'], $data_student['fac'], $data_student['promotion'], $data_student['annee_academique'])); 
                                    $pdf->SetFont('Arial','',8);
                                    //intval(($d['mp'] * 100)/$data['mt']) >= intval($_GET['m_min']) && intval(($d['mp'] * 100)/$data['mt']) <= intval($_GET['m_max'])
                                    while($d = $sql_2->fetch()){
                                        if(intval(($d['mp'] * 100)/$data['mt']) >= intval($_GET['m_min']) && intval(($d['mp'] * 100)/$data['mt']) <= intval($_GET['m_max'])){
                                            $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                                            $pdf->cell(60, 5, ucfirst(decode_fr($data_student['noms'])) , 1, 0, 'L');
                                            $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L');
                                            $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                            $pdf->cell(22, 5, '$ '.mm($data['mt']-$d['mp']), 1, 0, 'L');
                                            $pdf->cell(22, 5, montant_restant_pourcent($d['mp'], $data['mt']).'%', 1, 0, 'L');
                                            // $pdf->Ln(0);
                                        }else{
                                            $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                                            $pdf->cell(60, 5, ucfirst(decode_fr($data_student['noms'])) , 1, 0, 'L');
                                            $pdf->SetTextColor(255,255,255);
                                            $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L', true);
                                            $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L', true);
                                            $pdf->cell(22, 5, '$ '.mm($data['mt']-$d['mp']), 1, 0, 'L', true);
                                            $pdf->cell(22, 5, montant_restant_pourcent($d['mp'], $data['mt']).'%', 1, 0, 'L', true);
                                            $pdf->SetTextColor(11,11,11);
                                        }
                                    }
                                }
                                $pdf->Ln(5);
                            }else{
                                $pdf->SetFont('Arial','I',8);
                                $pdf->SetTextColor(166, 10, 10);
                                $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                                $pdf->cell(60, 5, ucfirst(decode_fr($data_student['noms'])) , 1, 0, 'L');
                                $pdf->cell(25, 5, mm(""), 1, 0, 'L');
                                $pdf->cell(25, 5, mm(""), 1, 0, 'L');
                                $pdf->cell(22, 5, mm(""), 1, 0, 'L');
                                $pdf->cell(22, 5, " # ", 1, 0, 'L');
                                $pdf->Ln(5);
                                $pdf->SetTextColor(0, 0, 0);
                            }
                        }
                    }else{
                        $pdf->SetTextColor(200, 12, 12);
                        $pdf->SetFont('Arial','B',10);
                        $pdf->cell(60, 5, decode_fr('Auncun etudiant n\'est inscrit en : '.$p.' '.$f.' dans l\'annee acad.: '.$_GET['a']), 0, 1, 'L');
                        $pdf->SetTextColor(0,0,0);
                        $pdf->SetFont('Arial','',8);
                        $pdf->Ln(3);
                    }
                }
                $pdf->Ln(5);
            }
        }else if($_GET['pr'] != "Toute les promotions" && $_GET['fac'] == "Toute les facultés"){
            // on selectionne les info dans la table des etudiants
            $sel_all = ConnexionBdd::Connecter()->prepare("SELECT fac, promotion, noms, matricule,annee_academique FROM etudiants_inscrits WHERE promotion = ? AND annee_academique = ? GROUP BY matricule");
            $sel_all->execute(array($_GET['pr'], $_GET['a']));

            $lst_fac = array();

            while($data_student = $sel_all->fetch()){
                // append dans la tableau
                if(!in_array($data_student['fac'], $lst_fac)){
                    $lst_fac[] = $data_student['fac'];
                }
            }

            foreach($lst_fac as $fac){
                $pdf->Ln(5);
                $pdf->cell(20, 5, ucfirst(decode_fr('faculté : '.$fac)) , 0, 0, 'L');
                $pdf->Ln(5);
                $pdf->cell(20, 5, ucfirst(decode_fr('promotion : '.$_GET['pr'])) , 0, 0, 'L');
                $pdf->Ln(5);
                $pdf->cell(20, 5, ucfirst(decode_fr('Annee Academique : '.$_GET['a'])) , 0, 0, 'L');
                $pdf->Ln(5);

                entete($pdf);
                $sel_all = ConnexionBdd::Connecter()->prepare("SELECT fac, promotion, noms, matricule,annee_academique FROM etudiants_inscrits WHERE promotion = ? AND fac = ? AND annee_academique = ?");
                $sel_all->execute(array($_GET['pr'], $fac, $_GET['a']));

                while($data_student = $sel_all->fetch()){
                    $sql = "SELECT faculte, promotion, type_frais, SUM(montant) as mt FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ?";
                    $sql_array = array($data_student['matricule'], $data_student['promotion'], $fac, $data_student['annee_academique']);
                    $all = ConnexionBdd::Connecter()->prepare($sql);
                    $all->execute($sql_array);
                    
                    while ($data = $all->fetch()){
                        $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ? AND annee_acad = ?";

                        $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                        $sql_2->execute(array($data_student['matricule'], $fac, $data['promotion'], $_GET['a'])); 

                        $pdf->SetFont('Arial','',8);

                        while($d = $sql_2->fetch()){
                            if($data['mt'] > 0){
                                if(intval($d['mp'] * 100 / $data['mt']) >= intval($_GET['m_max'])){
                                    $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                                    $pdf->cell(90, 5, mm($data_student['noms']), 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                    $pdf->cell(22, 5, '$ '.mm($data['mt']-$d['mp']), 1, 0, 'L');
                                    $pdf->cell(10, 5, montant_restant_pourcent($d['mp'], $data['mt']).'%', 1, 0, 'L');
                                    $pdf->Ln(5);
                                }else{
                                    $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                                    $pdf->SetFillColor(20,20,20);
                                    $pdf->cell(90, 5, mm($data_student['noms']), 1, 0, 'L');
                                    $pdf->SetTextColor(255,255,255);
                                    $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L', true);
                                    $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L', true);
                                    $pdf->cell(22, 5, '$ '.mm($data['mt']-$d['mp']), 1, 0, 'L', true);
                                    $pdf->cell(10, 5, montant_restant_pourcent($d['mp'], $data['mt']).'%', 1, 0, 'L', true);
                                    $pdf->SetTextColor(1,1,1);
                                    $pdf->Ln(5);
                                }
                            }
                        }
                    }
                }
            }
        }else if($_GET['pr'] == "Toute les promotions" && $_GET['fac'] != "Toute les facultés"){
            // --------------------------
            // on selectionne les info dans la table des etudiants
            $sel_all = ConnexionBdd::Connecter()->prepare("SELECT fac, promotion, noms, matricule,annee_academique FROM etudiants_inscrits WHERE fac = ? AND annee_academique = ?");
            $sel_all->execute(array($_GET['fac'], $_GET['a']));

            while($data_student = $sel_all->fetch()){
                // $pdf->cell(60, 5, 'Promotion : '.$data_student['promotion'], 0, 1, 'L');
                $sql = "SELECT faculte, promotion, type_frais, SUM(montant) as mt FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ?";
                $sql_array = array($data_student['matricule'], $data_student['promotion'], $data_student['fac'], $data_student['annee_academique']);
                
                // $pdf->Ln(3);
                $all = ConnexionBdd::Connecter()->prepare($sql);
                $all->execute($sql_array);
                
                while ($data = $all->fetch()){
                    $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ? AND type_frais = ? AND annee_acad = ?";

                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                    $sql_2->execute(array($data_student['matricule'], $data['faculte'], $data['promotion'], $data['type_frais'], $_GET['a'])); 

                    $pdf->SetFont('Arial','',8);

                    while($d = $sql_2->fetch()){
                        if(intval($data['mt']) > 0){
                            if(intval($d['mp'] * 100 / $data['mt']) >= intval($_GET['m_max'])){
                                $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                                $pdf->cell(90, 5, mm($data_student['noms']), 1, 0, 'L');
                                $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L');
                                $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                $pdf->cell(22, 5, '$ '.mm($data['mt']-$d['mp']), 1, 0, 'L');
                                $pdf->cell(10, 5, montant_restant_pourcent($d['mp'], $data['mt']).'%', 1, 0, 'L');
                                $pdf->Ln(5);
                            }else{
                                $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                                $pdf->cell(90, 5, mm($data_student['noms']), 1, 0, 'L');
                                $pdf->SetTextColor(255,255,255);
                                $pdf->SetFillColor(22,22,22);
                                $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L', true);
                                $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L', true);
                                $pdf->cell(22, 5, '$ '.mm($data['mt']-$d['mp']), 1, 0, 'L', true);
                                $pdf->cell(10, 5, montant_restant_pourcent($d['mp'], $data['mt']).'%', 1, 0, 'L', true);
                                $pdf->Ln(5);
                                $pdf->SetTextColor(1,1,1);
                            }
                        }
                    }
                }
            }
        }else if($_GET['pr'] != "Toute les promotions" && $_GET['fac'] != "Toute les facultés"){
            // on selectionne les info dans la table des etudiants
            $sel_all = ConnexionBdd::Connecter()->prepare("SELECT fac, promotion, noms, matricule,annee_academique FROM etudiants_inscrits WHERE fac = ? AND promotion = ? AND annee_academique = ?");
            $sel_all->execute(array($_GET['fac'], $_GET['pr'], $_GET['a']));
            entete($pdf);

            while($data_student = $sel_all->fetch()){
                $sql = "SELECT SUM(montant) as mt FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ?";
                $sql_array = array($data_student['matricule'], $data_student['promotion'], $data_student['fac'], $data_student['annee_academique']);

                $all = ConnexionBdd::Connecter()->prepare($sql);
                $all->execute($sql_array);
                $data = $all->fetch();

                // montant deja payer par l'etudiant
                $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ? AND annee_acad = ?";

                $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                $sql_2->execute(array($data_student['matricule'], $data_student['fac'], $data_student['promotion'], $_GET['a'])); 

                $pdf->SetFont('Arial','',8);

                $d = $sql_2->fetch();

                if(intval($data['mt'])){
                    if(intval($d['mp'] * 100 / $data['mt']) >= intval($_GET['m_max'])){
                        $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                        $pdf->cell(90, 5, mm($data_student['noms']), 1, 0, 'L');
                        $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L');
                        $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                        $pdf->cell(22, 5, '$ '.mm($data['mt'] - $d['mp']), 1, 0, 'L');
                        $pdf->cell(10, 5, montant_restant_pourcent($d['mp'], $data['mt']).'%', 1, 0, 'L');
                        $pdf->Ln(5);
                    }else{
                        $pdf->cell(20, 5, ucfirst(decode_fr($data_student['matricule'])) , 1, 0, 'L');
                        $pdf->cell(90, 5, mm($data_student['noms']), 1, 0, 'L');
                        $pdf->SetTextColor(255,255,255);
                        $pdf->SetFillColor(22,22,22);
                        $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L', true);
                        $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L', true);
                        $pdf->cell(22, 5, '$ '.mm($data['mt'] - $d['mp']), 1, 0, 'L', true);
                        $pdf->cell(10, 5, montant_restant_pourcent($d['mp'], $data['mt']).'%', 1, 0, 'L', true);
                        $pdf->Ln(5);
                        $pdf->SetTextColor(1,1,1);
                    }
                }
            }
        }
    }
    // else{
    //     echo 'all fail';
    // }
    $pdf->Output();
?>