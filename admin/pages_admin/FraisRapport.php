<?php
    // header()
    session_start();
    require_once '../../includes/ConnexionBdd.class.php';
    require_once '../../includes/verification.class.php';
    //verification des sessions
    require_once './sessions.php';
    require './fpdf/fpdf.php';
    $p = "Frais Rapport";

    function get_annee($id){
        $d = ConnexionBdd::Connecter()->query("SELECT annee_acad from annee_acad where id_annee  = {$id}");
        $data = $d->fetch();
        return $data['annee_acad'];
    }

    function get_fac($f){
        if($f == "Tous"){
            return $f;
        }else{
            return $f;
        }
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
        $pdf->Image("../../images/ispt_kin.png", 10,15,25, 25);
        $pdf->Ln(2);
        $pdf->cell(197,1 ,"",1,1,'C', true);
        $pdf->Ln(3);

        $pdf->SetFont('Arial','',10);
        $pdf->SetTextColor(0,0,0);
        $pdf->cell(60, 5, decode_fr('Année Academique : '.verify(get_annee($_POST['annee_acad_deb']))), 0, 1, 'L');
        $pdf->cell(60, 5, 'Promotion                 : '.decode_fr(verify($_POST['promotion_etud'])), 0, 1, 'L');
        $pdf->cell(60, 5, decode_fr('SECTION                     : '.verify($_POST['fac_etudiant'])), 0, 1, 'L');
        $pdf->cell(60, 5, 'Type de frais             : '.decode_fr(verify($_POST['type_frais'])), 0, 1, 'L');
        $pdf->cell(60, 5, decode_fr('Poucentage min.       : '.verify($_POST['pourcent_debut']).'%'), 0, 1, 'L');
        $pdf->cell(60, 5, decode_fr('Poucentage max.      : '.verify($_POST['pourcent_fin']).'%'), 0, 1, 'L');
        $pdf->Ln(3);
    }

    function verify($var){
        if(isset($var) && !empty($var)){
            return $var;
        }else{
            return ' - ';
        }
    }

    function date_cou($var1, $var2){
        if(isset($var1) && isset($var2) && !empty($var1) && !empty($var2)){
            return 'Du '.date("d F Y", strtotime(strtolower($var1))).' au '.date("d F Y", strtotime(strtolower($var2)));
        }else{
            return ' - ';
        }
    }

    function mm($v){
        if(empty($v)){
            return '0';
        }else{
            return $v;
        }
    }

    function all_trans($pdf){
        $annee_acad_deb = $_POST['annee_acad_deb'];
        $annee_acad_fin = $_POST['annee_acad_fin'];
        $pd = floatval($_POST['pourcent_debut']);
        $pf = floatval($_POST['pourcent_fin']);
        if($annee_acad_deb == $annee_acad_fin){
            $t_fac = array();
            $t_promotion = array();
            $ff = $_POST['fac_etudiant'];
            $pp = $_POST['promotion_etud'];
            
            // selection des toutes les facultes
            $f = ConnexionBdd::Connecter()->query("SELECT etudiants_inscrits.id_section, sections.section, annee_acad.id_annee FROM etudiants_inscrits LEFT JOIN sections ON etudiants_inscrits.id_section = sections.id_section LEFT JOIN annee_acad ON etudiants_inscrits.id_annee = annee_acad.id_annee WHERE etudiants_inscrits.id_annee = {$annee_acad_fin}");
            while($df = $f->fetch()){
                $t_fac[] = $df['section'];
            }


            if($_POST['fac_etudiant'] == "Tous" && $_POST['promotion_etud'] == "Tous"){
                foreach($t_fac as $ff){
                    $t_promotion = array();
                    $f = ConnexionBdd::Connecter()->query("SELECT DISTINCT etudiants_inscrits.promotion, options.id_option, sections.section FROM etudiants_inscrits LEFT JOIN options ON etudiants_inscrits.id_option = options.id_option LEFT JOIN sections ON etudiants_inscrits.id_section = sections.id_section WHERE sections.section = '{$ff}' GROUP BY etudiants_inscrits.promotion");
                    while($df = $f->fetch()){
                        $t_promotion[] = $df['promotion'];
                    }
                    // die($pp);
                    foreach($t_promotion as $pp){
                        if(empty($_POST['pourcent_debut']) && empty($_POST['pourcent_fin'])){
                            // la tables etudiants
                            $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT id_annee FROM etudiants_inscrits WHERE id_annee = ? GROUP BY id_annee");
                            $sel_etud->execute(array($annee_acad_fin));
            
                            if($sel_etud->rowCount() > 0){
                                // while ($data_student = $sel_etud->fetch()){
                                    $pdf->cell(190, 5, decode_fr('Annee Acad.: '.get_annee($annee_acad_fin)), 0, 1, 'L');
                                    $pdf->cell(190, 5, decode_fr('Faculte : '.$ff), 0, 1, 'L');
                                    $pdf->cell(190, 5, decode_fr('Promotion : '.$pp), 0, 1, 'L');
                                    $pdf->Ln(2);
                                    $pdf->cell(60, 5, 'Type frais', 1, 0, 'L');
                                    $pdf->cell(30, 5, 'Montant prevu', 1, 0, 'L');
                                    $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                                    $pdf->cell(30, 5, 'Solde', 1, 0, 'L');
                                    $pdf->cell(15, 5, ' % ', 1, 0, 'L');
                                    $pdf->Ln(1);
            
                                    // tableau
                                    $a = array();
                                    $b = array();
                                    if($_POST['type_frais'] != "Tous"){
                                            $sql_2 = "SELECT
                                                    payement.id_payement,
                                                    SUM(payement.montant) AS mp,
                                                    prevision_frais.id_frais,
                                                    SUM(prevision_frais.montant) AS mt,
                                                    prevision_frais.type_frais,
                                                    annee_acad.annee_acad,
                                                    sections.id_section,
                                                    options.id_option, options.promotion
                                                FROM
                                                    payement
                                                LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                                LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                                LEFT JOIN sections ON payement.id_section = sections.id_section
                                                LEFT JOIN options ON payement.id_option = options.id_option
                                                WHERE
                                                    prevision_frais.type_frais = ? AND annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?";
                                            $sql_2_a = array($_POST['type_frais'], $annee_acad_deb, $ff, $pp);
                                            $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                            $sql_2->execute($sql_2_a);
            
                                            while($data = $sql_2->fetch()){
                                                $pdf->SetFont('Arial','i',8);
                                                $pdf->Ln(4);
                                                $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                                $pdf->cell(30, 5, '$ '.$data['mt'], 1, 0, 'L');
                                                $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                                $pdf->cell(30, 5, '$ '.mm($data['mt'] - $data['mp']), 1, 0, 'L');
                                                $pdf->cell(15, 5, montant_restant_pourcent($data['mp'], $data['mt']).' % ', 1, 0, 'L');
                                                $pdf->Ln(1);
                                                $a[] = $data['mt'];
                                                $b[] = $data['mp'];
                                            }
                                        // }
            
                                        $pdf->Ln(4);
                                        $pdf->SetFont('Arial','',9);
                                        $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                                        $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b) , array_sum($a)).'%', 1, 0, 'L');
                                        $pdf->Ln(8);
                                        $a = array();
                                        $b = array();
                                        // le type de frais n est pas selectionner
                                    }else{
                                        $sql_2 = "SELECT
                                                    payement.id_payement,
                                                    SUM(payement.montant) AS mp,
                                                    prevision_frais.id_frais,
                                                    SUM(prevision_frais.montant) AS mt,
                                                    prevision_frais.type_frais,
                                                    annee_acad.annee_acad,
                                                    sections.id_section,
                                                    options.id_option, options.promotion
                                                FROM
                                                    payement
                                                LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                                LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                                LEFT JOIN sections ON payement.id_section = sections.id_section
                                                LEFT JOIN options ON payement.id_option = options.id_option
                                                WHERE
                                                    annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?";
                                            $sql_2_a = array($annee_acad_deb, $ff, $pp);
                                            $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                            $sql_2->execute($sql_2_a);
            
                                            while($data = $sql_2->fetch()){
                                                $pdf->SetFont('Arial','i',8);
                                                $pdf->Ln(4);
                                                $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                                $pdf->cell(30, 5, '$ '.$data['mt'], 1, 0, 'L');
                                                $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                                $pdf->cell(30, 5, '$ '.mm($data['mt'] - $data['mp']), 1, 0, 'L');
                                                $pdf->cell(15, 5, montant_restant_pourcent($data['mp'], $data['mt']).' % ', 1, 0, 'L');
                                                $pdf->Ln(1);
                                                $a[] = $data['mt'];
                                                $b[] = $data['mp'];
                                            }
                                        $pdf->Ln(4);
                                        $pdf->SetFont('Arial','',9);
                                        $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                                        $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b) , array_sum($a)).'%', 1, 0, 'L');
                                        $pdf->Ln(9);
                                        $a = array();
                                        $b = array();
                                    }
                                // }
                            }
                        }else if(!empty($_POST['pourcent_debut']) && !empty($_POST['pourcent_fin'])){
                            // la tables etudiants
                            $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT id_annee FROM etudiants_inscrits WHERE id_annee = ? GROUP BY id_annee");
                            $sel_etud->execute(array($annee_acad_fin));
            
                            if($sel_etud->rowCount() > 0){
                                while ($data_student = $sel_etud->fetch()){
                                    $pdf->cell(190, 5, decode_fr('Annee Acad.: '.get_annee($annee_acad_fin)), 0, 1, 'L');
                                    $pdf->cell(190, 5, decode_fr('Faculte : '.$ff), 0, 1, 'L');
                                    $pdf->cell(190, 5, decode_fr('Promotion : '.$pp), 0, 1, 'L');
                                    $pdf->Ln(2);
                                    $pdf->cell(60, 5, 'Type frais', 1, 0, 'L');
                                    $pdf->cell(30, 5, 'Montant prevu', 1, 0, 'L');
                                    $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                                    $pdf->cell(30, 5, 'Solde', 1, 0, 'L');
                                    $pdf->cell(15, 5, ' % ', 1, 0, 'L');
                                    $pdf->Ln(1);
            
                                    // tableau
                                    $a = array();
                                    $b = array();
                                    if($_POST['type_frais'] != "Tous"){
                                            $sql_2 = "SELECT
                                                    payement.id_payement,
                                                    SUM(payement.montant) AS mp,
                                                    prevision_frais.id_frais,
                                                    SUM(prevision_frais.montant) AS mt,
                                                    prevision_frais.type_frais,
                                                    annee_acad.annee_acad,
                                                    sections.id_section,
                                                    options.id_option, options.promotion
                                                FROM
                                                    payement
                                                LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                                LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                                LEFT JOIN sections ON payement.id_section = sections.id_section
                                                LEFT JOIN options ON payement.id_option = options.id_option
                                                WHERE
                                                    prevision_frais.type_frais = ? AND annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?
                                                HAVING ROUND(mp * 100 / ?) >= ? AND ROUND(mp * 100 / ?) <= ?";
                                            $sql_2_a = array($_POST['type_frais'], $annee_acad_deb, $ff, $pp, $pd, $pf);
                                            $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                            $sql_2->execute($sql_2_a);
            
                                            while($data = $sql_2->fetch()){
                                                $pdf->SetFont('Arial','i',8);
                                                $pdf->Ln(4);
                                                $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                                $pdf->cell(30, 5, '$ '.$data['mt'], 1, 0, 'L');
                                                $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                                $pdf->cell(30, 5, '$ '.mm($data['mt'] - $data['mp']), 1, 0, 'L');
                                                $pdf->cell(15, 5, montant_restant_pourcent($data['mp'], $data['mt']).' % ', 1, 0, 'L');
                                                $pdf->Ln(1);
                                                $a[] = $data['mt'];
                                                $b[] = $data['mp'];
                                            }
                                        // }
            
                                        $pdf->Ln(4);
                                        $pdf->SetFont('Arial','',9);
                                        $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                                        $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b) , array_sum($a)).'%', 1, 0, 'L');
                                        $pdf->Ln(8);
                                        $a = array();
                                        $b = array();
                                        // le type de frais n est pas selectionner
                                    }else{
                                        $sql_2 = "SELECT
                                                    payement.id_payement,
                                                    SUM(payement.montant) AS mp,
                                                    prevision_frais.id_frais,
                                                    SUM(prevision_frais.montant) AS mt,
                                                    prevision_frais.type_frais,
                                                    annee_acad.annee_acad,
                                                    sections.id_section,
                                                    options.id_option, options.promotion
                                                FROM
                                                    payement
                                                LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                                LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                                LEFT JOIN sections ON payement.id_section = sections.id_section
                                                LEFT JOIN options ON payement.id_option = options.id_option
                                                WHERE
                                                    annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?
                                                HAVING ROUND(mp * 100 / ?) >= ? AND ROUND(mp * 100 / ?) <= ?";
                                            $sql_2_a = array($annee_acad_deb, $ff, $pp, $pd, $pf);
                                            $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                            $sql_2->execute($sql_2_a);
            
                                            while($data = $sql_2->fetch()){
                                                $pdf->SetFont('Arial','i',8);
                                                $pdf->Ln(4);
                                                $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                                $pdf->cell(30, 5, '$ '.$data['mt'], 1, 0, 'L');
                                                $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                                $pdf->cell(30, 5, '$ '.mm($data['mt'] - $data['mp']), 1, 0, 'L');
                                                $pdf->cell(15, 5, montant_restant_pourcent($data['mp'], $data['mt']).' % ', 1, 0, 'L');
                                                $pdf->Ln(1);
                                                $a[] = $data['mt'];
                                                $b[] = $data['mp'];
                                            }
                                        $pdf->Ln(4);
                                        $pdf->SetFont('Arial','',9);
                                        $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                                        $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b) , array_sum($a)).'%', 1, 0, 'L');
                                        $pdf->Ln(9);
                                        $a = array();
                                        $b = array();
                                    }
                                }
                            }
                        }
                    }
                }
            }else if($_POST['fac_etudiant'] != "Tous" && $_POST['promotion_etud'] == "Tous"){
                $t_promotion = array();
                $f = ConnexionBdd::Connecter()->query("SELECT DISTINCT etudiants_inscrits.promotion, options.id_option, sections.section FROM etudiants_inscrits LEFT JOIN options ON etudiants_inscrits.id_option = options.id_option LEFT JOIN sections ON etudiants_inscrits.id_section = sections.id_section WHERE sections.section = '{$_POST['fac_etudiant']}' GROUP BY etudiants_inscrits.promotion");
                while($df = $f->fetch()){
                    $t_promotion[] = $df['promotion'];
                }
                foreach($t_promotion as $pp){
                    if(empty($_POST['pourcent_debut']) && empty($_POST['pourcent_fin'])){
                        // la tables etudiants
                        $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT id_annee FROM etudiants_inscrits WHERE id_annee = ? GROUP BY id_annee");
                        $sel_etud->execute(array($annee_acad_fin));
        
                        if($sel_etud->rowCount() > 0){
                            // while ($data_student = $sel_etud->fetch()){
                                $pdf->cell(190, 5, decode_fr('Annee Acad.: '.get_annee($annee_acad_fin)), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Faculte : '.$ff), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Promotion : '.$pp), 0, 1, 'L');
                                $pdf->Ln(2);
                                $pdf->cell(60, 5, 'Type frais', 1, 0, 'L');
                                $pdf->cell(30, 5, 'Montant prevu', 1, 0, 'L');
                                $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                                $pdf->cell(30, 5, 'Solde', 1, 0, 'L');
                                $pdf->cell(15, 5, ' % ', 1, 0, 'L');
                                $pdf->Ln(1);
        
                                // tableau
                                $a = array();
                                $b = array();
                                if($_POST['type_frais'] != "Tous"){
                                        $sql_2 = "SELECT
                                                payement.id_payement,
                                                SUM(payement.montant) AS mp,
                                                prevision_frais.id_frais,
                                                SUM(prevision_frais.montant) AS mt,
                                                prevision_frais.type_frais,
                                                annee_acad.annee_acad,
                                                sections.id_section,
                                                options.id_option, options.promotion
                                            FROM
                                                payement
                                            LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                            LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                            LEFT JOIN sections ON payement.id_section = sections.id_section
                                            LEFT JOIN options ON payement.id_option = options.id_option
                                            WHERE
                                                prevision_frais.type_frais = ? AND annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?";
                                        $sql_2_a = array($_POST['type_frais'], $annee_acad_deb, $ff, $pp);
                                        $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                        $sql_2->execute($sql_2_a);
        
                                        while($data = $sql_2->fetch()){
                                            $pdf->SetFont('Arial','i',8);
                                            $pdf->Ln(4);
                                            $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.$data['mt'], 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($data['mt'] - $data['mp']), 1, 0, 'L');
                                            $pdf->cell(15, 5, montant_restant_pourcent($data['mp'], $data['mt']).' % ', 1, 0, 'L');
                                            $pdf->Ln(1);
                                            $a[] = $data['mt'];
                                            $b[] = $data['mp'];
                                        }
                                    // }
        
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                                    $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b) , array_sum($a)).'%', 1, 0, 'L');
                                    $pdf->Ln(8);
                                    $a = array();
                                    $b = array();
                                    // le type de frais n est pas selectionner
                                }else{
                                    $sql_2 = "SELECT
                                                payement.id_payement,
                                                SUM(payement.montant) AS mp,
                                                prevision_frais.id_frais,
                                                SUM(prevision_frais.montant) AS mt,
                                                prevision_frais.type_frais,
                                                annee_acad.annee_acad,
                                                sections.id_section,
                                                options.id_option, options.promotion
                                            FROM
                                                payement
                                            LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                            LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                            LEFT JOIN sections ON payement.id_section = sections.id_section
                                            LEFT JOIN options ON payement.id_option = options.id_option
                                            WHERE
                                                annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?";
                                        $sql_2_a = array($annee_acad_deb, $ff, $pp);
                                        $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                        $sql_2->execute($sql_2_a);
        
                                        while($data = $sql_2->fetch()){
                                            $pdf->SetFont('Arial','i',8);
                                            $pdf->Ln(4);
                                            $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.$data['mt'], 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($data['mt'] - $data['mp']), 1, 0, 'L');
                                            $pdf->cell(15, 5, montant_restant_pourcent($data['mp'], $data['mt']).' % ', 1, 0, 'L');
                                            $pdf->Ln(1);
                                            $a[] = $data['mt'];
                                            $b[] = $data['mp'];
                                        }
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                                    $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b) , array_sum($a)).'%', 1, 0, 'L');
                                    $pdf->Ln(9);
                                    $a = array();
                                    $b = array();
                                }
                            // }
                        }
                    }else if(!empty($_POST['pourcent_debut']) && !empty($_POST['pourcent_fin'])){
                        // la tables etudiants
                        $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT id_annee FROM etudiants_inscrits WHERE id_annee = ? GROUP BY id_annee");
                        $sel_etud->execute(array($annee_acad_fin));
        
                        if($sel_etud->rowCount() > 0){
                            while ($data_student = $sel_etud->fetch()){
                                $pdf->cell(190, 5, decode_fr('Annee Acad.: '.get_annee($annee_acad_fin)), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Faculte : '.$ff), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Promotion : '.$pp), 0, 1, 'L');
                                $pdf->Ln(2);
                                $pdf->cell(60, 5, 'Type frais', 1, 0, 'L');
                                $pdf->cell(30, 5, 'Montant prevu', 1, 0, 'L');
                                $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                                $pdf->cell(30, 5, 'Solde', 1, 0, 'L');
                                $pdf->cell(15, 5, ' % ', 1, 0, 'L');
                                $pdf->Ln(1);
        
                                // tableau
                                $a = array();
                                $b = array();
                                if($_POST['type_frais'] != "Tous"){
                                        $sql_2 = "SELECT
                                                payement.id_payement,
                                                SUM(payement.montant) AS mp,
                                                prevision_frais.id_frais,
                                                SUM(prevision_frais.montant) AS mt,
                                                prevision_frais.type_frais,
                                                annee_acad.annee_acad,
                                                sections.id_section,
                                                options.id_option, options.promotion
                                            FROM
                                                payement
                                            LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                            LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                            LEFT JOIN sections ON payement.id_section = sections.id_section
                                            LEFT JOIN options ON payement.id_option = options.id_option
                                            WHERE
                                                prevision_frais.type_frais = ? AND annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?
                                            HAVING ROUND(mp * 100 / ?) >= ? AND ROUND(mp * 100 / ?) <= ?";
                                        $sql_2_a = array($_POST['type_frais'], $annee_acad_deb, $ff, $pp, $pd, $pf);
                                        $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                        $sql_2->execute($sql_2_a);
        
                                        while($data = $sql_2->fetch()){
                                            $pdf->SetFont('Arial','i',8);
                                            $pdf->Ln(4);
                                            $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.$data['mt'], 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($data['mt'] - $data['mp']), 1, 0, 'L');
                                            $pdf->cell(15, 5, montant_restant_pourcent($data['mp'], $data['mt']).' % ', 1, 0, 'L');
                                            $pdf->Ln(1);
                                            $a[] = $data['mt'];
                                            $b[] = $data['mp'];
                                        }
                                    // }
        
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                                    $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b) , array_sum($a)).'%', 1, 0, 'L');
                                    $pdf->Ln(8);
                                    $a = array();
                                    $b = array();
                                    // le type de frais n est pas selectionner
                                }else{
                                    $sql_2 = "SELECT
                                                payement.id_payement,
                                                SUM(payement.montant) AS mp,
                                                prevision_frais.id_frais,
                                                SUM(prevision_frais.montant) AS mt,
                                                prevision_frais.type_frais,
                                                annee_acad.annee_acad,
                                                sections.id_section,
                                                options.id_option, options.promotion
                                            FROM
                                                payement
                                            LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                            LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                            LEFT JOIN sections ON payement.id_section = sections.id_section
                                            LEFT JOIN options ON payement.id_option = options.id_option
                                            WHERE
                                                annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?
                                            HAVING ROUND(mp * 100 / ?) >= ? AND ROUND(mp * 100 / ?) <= ?";
                                        $sql_2_a = array($annee_acad_deb, $ff, $pp, $pd, $pf);
                                        $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                        $sql_2->execute($sql_2_a);
        
                                        while($data = $sql_2->fetch()){
                                            $pdf->SetFont('Arial','i',8);
                                            $pdf->Ln(4);
                                            $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.$data['mt'], 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($data['mt'] - $data['mp']), 1, 0, 'L');
                                            $pdf->cell(15, 5, montant_restant_pourcent($data['mp'], $data['mt']).' % ', 1, 0, 'L');
                                            $pdf->Ln(1);
                                            $a[] = $data['mt'];
                                            $b[] = $data['mp'];
                                        }
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                                    $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b) , array_sum($a)).'%', 1, 0, 'L');
                                    $pdf->Ln(9);
                                    $a = array();
                                    $b = array();
                                }
                            }
                        }
                    }
                }
            }else if($_POST['fac_etudiant'] != "Tous" && $_POST['promotion_etud'] != "Tous"){
                if(empty($_POST['pourcent_debut']) && empty($_POST['pourcent_fin'])){
                    // la tables etudiants
                    $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT id_annee FROM etudiants_inscrits WHERE id_annee = ? GROUP BY id_annee");
                    $sel_etud->execute(array($annee_acad_fin));
    
                    if($sel_etud->rowCount() > 0){
                        // while ($data_student = $sel_etud->fetch()){
                            $pdf->cell(190, 5, decode_fr('Annee Acad.: '.get_annee($annee_acad_fin)), 0, 1, 'L');
                            $pdf->cell(190, 5, decode_fr('Faculte : '.$ff), 0, 1, 'L');
                            $pdf->cell(190, 5, decode_fr('Promotion : '.$pp), 0, 1, 'L');
                            $pdf->Ln(2);
                            $pdf->cell(60, 5, 'Type frais', 1, 0, 'L');
                            $pdf->cell(30, 5, 'Montant prevu', 1, 0, 'L');
                            $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                            $pdf->cell(30, 5, 'Solde', 1, 0, 'L');
                            $pdf->cell(15, 5, ' % ', 1, 0, 'L');
                            $pdf->Ln(1);
    
                            // tableau
                            $a = array();
                            $b = array();
                            if($_POST['type_frais'] != "Tous"){
                                    $sql_2 = "SELECT
                                            payement.id_payement,
                                            SUM(payement.montant) AS mp,
                                            prevision_frais.id_frais,
                                            SUM(prevision_frais.montant) AS mt,
                                            prevision_frais.type_frais,
                                            annee_acad.annee_acad,
                                            sections.id_section,
                                            options.id_option, options.promotion
                                        FROM
                                            payement
                                        LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                        LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                        LEFT JOIN sections ON payement.id_section = sections.id_section
                                        LEFT JOIN options ON payement.id_option = options.id_option
                                        WHERE
                                            prevision_frais.type_frais = ? AND annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?";
                                    $sql_2_a = array($_POST['type_frais'], $annee_acad_deb, $ff, $pp);
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute($sql_2_a);
    
                                    while($data = $sql_2->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.$data['mt'], 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($data['mt'] - $data['mp']), 1, 0, 'L');
                                        $pdf->cell(15, 5, montant_restant_pourcent($data['mp'], $data['mt']).' % ', 1, 0, 'L');
                                        $pdf->Ln(1);
                                        $a[] = $data['mt'];
                                        $b[] = $data['mp'];
                                    }
                                // }
    
                                $pdf->Ln(4);
                                $pdf->SetFont('Arial','',9);
                                $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                                $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b) , array_sum($a)).'%', 1, 0, 'L');
                                $pdf->Ln(8);
                                $a = array();
                                $b = array();
                                // le type de frais n est pas selectionner
                            }else{
                                $sql_2 = "SELECT
                                            payement.id_payement,
                                            SUM(payement.montant) AS mp,
                                            prevision_frais.id_frais,
                                            SUM(prevision_frais.montant) AS mt,
                                            prevision_frais.type_frais,
                                            annee_acad.annee_acad,
                                            sections.id_section,
                                            options.id_option, options.promotion
                                        FROM
                                            payement
                                        LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                        LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                        LEFT JOIN sections ON payement.id_section = sections.id_section
                                        LEFT JOIN options ON payement.id_option = options.id_option
                                        WHERE
                                            annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?";
                                    $sql_2_a = array($annee_acad_deb, $ff, $pp);
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute($sql_2_a);
    
                                    while($data = $sql_2->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.$data['mt'], 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($data['mt'] - $data['mp']), 1, 0, 'L');
                                        $pdf->cell(15, 5, montant_restant_pourcent($data['mp'], $data['mt']).' % ', 1, 0, 'L');
                                        $pdf->Ln(1);
                                        $a[] = $data['mt'];
                                        $b[] = $data['mp'];
                                    }
                                $pdf->Ln(4);
                                $pdf->SetFont('Arial','',9);
                                $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                                $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b) , array_sum($a)).'%', 1, 0, 'L');
                                $pdf->Ln(9);
                                $a = array();
                                $b = array();
                            }
                        // }
                    }
                }else if(!empty($_POST['pourcent_debut']) && !empty($_POST['pourcent_fin'])){
                    // la tables etudiants
                    $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT id_annee FROM etudiants_inscrits WHERE id_annee = ? GROUP BY id_annee");
                    $sel_etud->execute(array($annee_acad_fin));
    
                    if($sel_etud->rowCount() > 0){
                        while ($data_student = $sel_etud->fetch()){
                            $pdf->cell(190, 5, decode_fr('Annee Acad.: '.get_annee($annee_acad_fin)), 0, 1, 'L');
                            $pdf->cell(190, 5, decode_fr('Faculte : '.$ff), 0, 1, 'L');
                            $pdf->cell(190, 5, decode_fr('Promotion : '.$pp), 0, 1, 'L');
                            $pdf->Ln(2);
                            $pdf->cell(60, 5, 'Type frais', 1, 0, 'L');
                            $pdf->cell(30, 5, 'Montant prevu', 1, 0, 'L');
                            $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                            $pdf->cell(30, 5, 'Solde', 1, 0, 'L');
                            $pdf->cell(15, 5, ' % ', 1, 0, 'L');
                            $pdf->Ln(1);
    
                            // tableau
                            $a = array();
                            $b = array();
                            if($_POST['type_frais'] != "Tous"){
                                    $sql_2 = "SELECT
                                            payement.id_payement,
                                            SUM(payement.montant) AS mp,
                                            prevision_frais.id_frais,
                                            SUM(prevision_frais.montant) AS mt,
                                            prevision_frais.type_frais,
                                            annee_acad.annee_acad,
                                            sections.id_section,
                                            options.id_option, options.promotion
                                        FROM
                                            payement
                                        LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                        LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                        LEFT JOIN sections ON payement.id_section = sections.id_section
                                        LEFT JOIN options ON payement.id_option = options.id_option
                                        WHERE
                                            prevision_frais.type_frais = ? AND annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?
                                        HAVING ROUND(mp * 100 / ?) >= ? AND ROUND(mp * 100 / ?) <= ?";
                                    $sql_2_a = array($_POST['type_frais'], $annee_acad_deb, $ff, $pp, $pd, $pf);
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute($sql_2_a);
    
                                    while($data = $sql_2->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.$data['mt'], 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($data['mt'] - $data['mp']), 1, 0, 'L');
                                        $pdf->cell(15, 5, montant_restant_pourcent($data['mp'], $data['mt']).' % ', 1, 0, 'L');
                                        $pdf->Ln(1);
                                        $a[] = $data['mt'];
                                        $b[] = $data['mp'];
                                    }
                                // }
    
                                $pdf->Ln(4);
                                $pdf->SetFont('Arial','',9);
                                $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                                $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b) , array_sum($a)).'%', 1, 0, 'L');
                                $pdf->Ln(8);
                                $a = array();
                                $b = array();
                                // le type de frais n est pas selectionner
                            }else{
                                $sql_2 = "SELECT
                                            payement.id_payement,
                                            SUM(payement.montant) AS mp,
                                            prevision_frais.id_frais,
                                            SUM(prevision_frais.montant) AS mt,
                                            prevision_frais.type_frais,
                                            annee_acad.annee_acad,
                                            sections.id_section,
                                            options.id_option, options.promotion
                                        FROM
                                            payement
                                        LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                        LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                        LEFT JOIN sections ON payement.id_section = sections.id_section
                                        LEFT JOIN options ON payement.id_option = options.id_option
                                        WHERE
                                            annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?
                                        HAVING ROUND(mp * 100 / ?) >= ? AND ROUND(mp * 100 / ?) <= ?";
                                    $sql_2_a = array($annee_acad_deb, $ff, $pp, $pd, $pf);
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute($sql_2_a);
    
                                    while($data = $sql_2->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.$data['mt'], 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($data['mt'] - $data['mp']), 1, 0, 'L');
                                        $pdf->cell(15, 5, montant_restant_pourcent($data['mp'], $data['mt']).' % ', 1, 0, 'L');
                                        $pdf->Ln(1);
                                        $a[] = $data['mt'];
                                        $b[] = $data['mp'];
                                    }
                                $pdf->Ln(4);
                                $pdf->SetFont('Arial','',9);
                                $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                                $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b) , array_sum($a)).'%', 1, 0, 'L');
                                $pdf->Ln(9);
                                $a = array();
                                $b = array();
                            }
                        }
                    }
                }
            }else if($_POST['fac_etudiant'] = "Tous" && $_POST['promotion_etud'] !="Tous"){
                // selection des toutes les facultes
                $t_fac = array();
                $f = ConnexionBdd::Connecter()->query("SELECT etudiants_inscrits.id_section, sections.section, annee_acad.id_annee FROM etudiants_inscrits LEFT JOIN sections ON etudiants_inscrits.id_section = sections.id_section LEFT JOIN annee_acad ON etudiants_inscrits.id_annee = annee_acad.id_annee WHERE etudiants_inscrits.id_annee = {$annee_acad_fin}");
                while($df = $f->fetch()){
                    $t_fac[] = $df['section'];
                }
                foreach($t_fac as $ff){
                    if(empty($_POST['pourcent_debut']) && empty($_POST['pourcent_fin'])){
                        // la tables etudiants
                        $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT id_annee FROM etudiants_inscrits WHERE id_annee = ? GROUP BY id_annee");
                        $sel_etud->execute(array($annee_acad_fin));
        
                        if($sel_etud->rowCount() > 0){
                            // while ($data_student = $sel_etud->fetch()){
                                $pdf->cell(190, 5, decode_fr('Annee Acad.: '.get_annee($annee_acad_fin)), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Faculte : '.$ff), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Promotion : '.$pp), 0, 1, 'L');
                                $pdf->Ln(2);
                                $pdf->cell(60, 5, 'Type frais', 1, 0, 'L');
                                $pdf->cell(30, 5, 'Montant prevu', 1, 0, 'L');
                                $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                                $pdf->cell(30, 5, 'Solde', 1, 0, 'L');
                                $pdf->cell(15, 5, ' % ', 1, 0, 'L');
                                $pdf->Ln(1);
        
                                // tableau
                                $a = array();
                                $b = array();
                                if($_POST['type_frais'] != "Tous"){
                                        $sql_2 = "SELECT
                                                payement.id_payement,
                                                SUM(payement.montant) AS mp,
                                                prevision_frais.id_frais,
                                                SUM(prevision_frais.montant) AS mt,
                                                prevision_frais.type_frais,
                                                annee_acad.annee_acad,
                                                sections.id_section,
                                                options.id_option, options.promotion
                                            FROM
                                                payement
                                            LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                            LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                            LEFT JOIN sections ON payement.id_section = sections.id_section
                                            LEFT JOIN options ON payement.id_option = options.id_option
                                            WHERE
                                                prevision_frais.type_frais = ? AND annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?";
                                        $sql_2_a = array($_POST['type_frais'], $annee_acad_deb, $ff, $pp);
                                        $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                        $sql_2->execute($sql_2_a);
        
                                        while($data = $sql_2->fetch()){
                                            $pdf->SetFont('Arial','i',8);
                                            $pdf->Ln(4);
                                            $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.$data['mt'], 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($data['mt'] - $data['mp']), 1, 0, 'L');
                                            $pdf->cell(15, 5, montant_restant_pourcent($data['mp'], $data['mt']).' % ', 1, 0, 'L');
                                            $pdf->Ln(1);
                                            $a[] = $data['mt'];
                                            $b[] = $data['mp'];
                                        }
                                    // }
        
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                                    $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b) , array_sum($a)).'%', 1, 0, 'L');
                                    $pdf->Ln(8);
                                    $a = array();
                                    $b = array();
                                    // le type de frais n est pas selectionner
                                }else{
                                    $sql_2 = "SELECT
                                                payement.id_payement,
                                                SUM(payement.montant) AS mp,
                                                prevision_frais.id_frais,
                                                SUM(prevision_frais.montant) AS mt,
                                                prevision_frais.type_frais,
                                                annee_acad.annee_acad,
                                                sections.id_section,
                                                options.id_option, options.promotion
                                            FROM
                                                payement
                                            LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                            LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                            LEFT JOIN sections ON payement.id_section = sections.id_section
                                            LEFT JOIN options ON payement.id_option = options.id_option
                                            WHERE
                                                annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?";
                                        $sql_2_a = array($annee_acad_deb, $ff, $pp);
                                        $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                        $sql_2->execute($sql_2_a);
        
                                        while($data = $sql_2->fetch()){
                                            $pdf->SetFont('Arial','i',8);
                                            $pdf->Ln(4);
                                            $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.$data['mt'], 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($data['mt'] - $data['mp']), 1, 0, 'L');
                                            $pdf->cell(15, 5, montant_restant_pourcent($data['mp'], $data['mt']).' % ', 1, 0, 'L');
                                            $pdf->Ln(1);
                                            $a[] = $data['mt'];
                                            $b[] = $data['mp'];
                                        }
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                                    $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b) , array_sum($a)).'%', 1, 0, 'L');
                                    $pdf->Ln(9);
                                    $a = array();
                                    $b = array();
                                }
                            // }
                        }
                    }else if(!empty($_POST['pourcent_debut']) && !empty($_POST['pourcent_fin'])){
                        // la tables etudiants
                        $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT id_annee FROM etudiants_inscrits WHERE id_annee = ? GROUP BY id_annee");
                        $sel_etud->execute(array($annee_acad_fin));
        
                        if($sel_etud->rowCount() > 0){
                            while ($data_student = $sel_etud->fetch()){
                                $pdf->cell(190, 5, decode_fr('Annee Acad.: '.get_annee($annee_acad_fin)), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Faculte : '.$ff), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Promotion : '.$pp), 0, 1, 'L');
                                $pdf->Ln(2);
                                $pdf->cell(60, 5, 'Type frais', 1, 0, 'L');
                                $pdf->cell(30, 5, 'Montant prevu', 1, 0, 'L');
                                $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                                $pdf->cell(30, 5, 'Solde', 1, 0, 'L');
                                $pdf->cell(15, 5, ' % ', 1, 0, 'L');
                                $pdf->Ln(1);
        
                                // tableau
                                $a = array();
                                $b = array();
                                if($_POST['type_frais'] != "Tous"){
                                        $sql_2 = "SELECT
                                                payement.id_payement,
                                                SUM(payement.montant) AS mp,
                                                prevision_frais.id_frais,
                                                SUM(prevision_frais.montant) AS mt,
                                                prevision_frais.type_frais,
                                                annee_acad.annee_acad,
                                                sections.id_section,
                                                options.id_option, options.promotion
                                            FROM
                                                payement
                                            LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                            LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                            LEFT JOIN sections ON payement.id_section = sections.id_section
                                            LEFT JOIN options ON payement.id_option = options.id_option
                                            WHERE
                                                prevision_frais.type_frais = ? AND annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?
                                            HAVING ROUND(mp * 100 / ?) >= ? AND ROUND(mp * 100 / ?) <= ?";
                                        $sql_2_a = array($_POST['type_frais'], $annee_acad_deb, $ff, $pp, $pd, $pf);
                                        $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                        $sql_2->execute($sql_2_a);
        
                                        while($data = $sql_2->fetch()){
                                            $pdf->SetFont('Arial','i',8);
                                            $pdf->Ln(4);
                                            $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.$data['mt'], 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($data['mt'] - $data['mp']), 1, 0, 'L');
                                            $pdf->cell(15, 5, montant_restant_pourcent($data['mp'], $data['mt']).' % ', 1, 0, 'L');
                                            $pdf->Ln(1);
                                            $a[] = $data['mt'];
                                            $b[] = $data['mp'];
                                        }
                                    // }
        
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                                    $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b) , array_sum($a)).'%', 1, 0, 'L');
                                    $pdf->Ln(8);
                                    $a = array();
                                    $b = array();
                                    // le type de frais n est pas selectionner
                                }else{
                                    $sql_2 = "SELECT
                                                payement.id_payement,
                                                SUM(payement.montant) AS mp,
                                                prevision_frais.id_frais,
                                                SUM(prevision_frais.montant) AS mt,
                                                prevision_frais.type_frais,
                                                annee_acad.annee_acad,
                                                sections.id_section,
                                                options.id_option, options.promotion
                                            FROM
                                                payement
                                            LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                            LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                            LEFT JOIN sections ON payement.id_section = sections.id_section
                                            LEFT JOIN options ON payement.id_option = options.id_option
                                            WHERE
                                                annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?
                                            HAVING ROUND(mp * 100 / ?) >= ? AND ROUND(mp * 100 / ?) <= ?";
                                        $sql_2_a = array($annee_acad_deb, $ff, $pp, $pd, $pf);
                                        $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                        $sql_2->execute($sql_2_a);
        
                                        while($data = $sql_2->fetch()){
                                            $pdf->SetFont('Arial','i',8);
                                            $pdf->Ln(4);
                                            $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.$data['mt'], 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($data['mt'] - $data['mp']), 1, 0, 'L');
                                            $pdf->cell(15, 5, montant_restant_pourcent($data['mp'], $data['mt']).' % ', 1, 0, 'L');
                                            $pdf->Ln(1);
                                            $a[] = $data['mt'];
                                            $b[] = $data['mp'];
                                        }
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                                    $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b) , array_sum($a)).'%', 1, 0, 'L');
                                    $pdf->Ln(9);
                                    $a = array();
                                    $b = array();
                                }
                            }
                        }
                    }
                }
            }
        } 
    }

    // detail de payement pour chaque etudiants selon les faculte, promotion et annee aca.
    if(isset($_POST['btn_detail_etud'])){
        $pdf = new FPDF('P', 'mm', 'A4');
        all($pdf);
        $pdf->SetFont('Arial','BU',10);
        $pdf->cell(190, 10, decode_fr('DETAIL DE PAYEMENT DES FRAIS PAR ETUDIANT '.$_POST['annee_acad_deb']), 0, 1, 'C');
        $pdf->SetFont('Arial','',9);
        $annee_acad_deb = $_POST['annee_acad_deb'];
        $annee_acad_fin = $_POST['annee_acad_fin'];

        // tableau
        $a = array();
        $b = array();
        $c = array();
        $t_fac = array();
        $t_promotion = array();

        if($annee_acad_deb == $annee_acad_fin){
            if($_POST['fac_etudiant'] == "Tous" && $_POST['promotion_etud'] == "Tous"){
                if(empty($_POST['pourcent_debut']) && empty($_POST['pourcent_fin'])){
                    // on commence par selectionner tous les etudiants
                    $sel_all = ConnexionBdd::Connecter()->prepare("SELECT * FROM etudiants_inscrits WHERE annee_academique = ? ORDER BY fac, promotion, noms ASC");
                    $sel_all->execute(array($annee_acad_fin));

                    while($data_student = $sel_all->fetch()){
                        $pdf->SetFont('Arial','',8);
                        $pdf->cell(60, 5, 'Matricule      : '.decode_fr($data_student['matricule']), 0, 1, 'L');
                        $pdf->cell(60, 5, 'Noms           : '.decode_fr($data_student['noms']), 0, 1, 'L');
                        $pdf->cell(60, 5, 'Faculte         : '.decode_fr($data_student['fac']), 0, 1, 'L');
                        $pdf->cell(60, 5, 'Promotion     : '.decode_fr($data_student['promotion']), 0, 1, 'L');
                        $pdf->cell(60, 5, 'Annee Acad. : '.decode_fr($data_student['annee_academique']), 0, 1, 'L');

                        // selection du montant
                        $pdf->SetFont('Arial','B',8);
                        $pdf->cell(60, 5, "Type frais" , 1, 0, 'L');
                        $pdf->cell(25, 5, "Montant prevu", 1, 0, 'L');
                        $pdf->cell(25, 5, "montant payer", 1, 0, 'L');
                        $pdf->cell(22, 5, "Solde", 1, 0, 'L');
                        $pdf->cell(22, 5, "%", 1, 0, 'L');
                        $pdf->Ln(5);
                        if($_POST['type_frais'] == "Tous"){
                            $sql = "SELECT faculte, promotion, type_frais, SUM(montant) as mt FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ? GROUP BY faculte, type_frais";
                            $sql_array = array($data_student['matricule'], $data_student['promotion'], $data_student['fac'], $data_student['annee_academique']);
                                                                        
                            $all = ConnexionBdd::Connecter()->prepare($sql);
                            $all->execute($sql_array);

                            while ($data = $all->fetch()){
                                $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ? AND type_frais = ?  HAVING mp >= 0";
                                $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                $sql_2->execute(array($data_student['matricule'], $data['faculte'], $data['promotion'], $data['type_frais'])); 
                                $pdf->SetFont('Arial','',8);
                                while($d = $sql_2->fetch()){
                                    $pdf->cell(60, 5, ucfirst(decode_fr($data['type_frais'])) , 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                    $pdf->cell(22, 5, '$ '.mm($data['mt']-$d['mp']), 1, 0, 'L');
                                    $pdf->cell(22, 5, montant_restant_pourcent($d['mp'], $data['mt']).' % ', 1, 0, 'L');
                                    // on affete les frais dans le tableau
                                    $a[] = $data['mt'];
                                    $b[] = $d['mp'];
                                    $c[] = $data['mt']-$d['mp'];
                                    $pdf->Ln(5);
                                }
                            }
                            
                            // total de frais
                            if(count($a) >= 0 && count($b) >= 0 && count($c) >= 0){
                                $pdf->cell(60, 5, decode_fr("Total") , 1, 0, 'L');
                                $pdf->cell(25, 5, '$ '.decode_fr(array_sum($a)) , 1, 0, 'L');
                                $pdf->cell(25, 5, '$ '.decode_fr(array_sum($b)) , 1, 0, 'L');
                                $pdf->cell(22, 5, '$ '.decode_fr(array_sum($c)) , 1, 0, 'L');
                                $pdf->cell(22, 5, decode_fr(montant_restant_pourcent(array_sum($b), array_sum($a))).'%' , 1, 0, 'L');
                                $a = array();
                                $b = array();
                                $c = array();
                                $pdf->Ln(5);
                            }else{
                                $pdf->cell(154, 5, decode_fr("Aucun frais affecté à l'étudiant(e)") , 1, 0, 'L');$pdf->Ln(5);
                                $pdf->cell(60, 5, decode_fr("Total") , 1, 0, 'L');
                                $pdf->cell(25, 5, '$ '.decode_fr(array_sum($a)) , 1, 0, 'L');
                                $pdf->cell(25, 5, '$ '.decode_fr(array_sum($b)) , 1, 0, 'L');
                                $pdf->cell(22, 5, '$ '.decode_fr(array_sum($c)) , 1, 0, 'L');
                                $pdf->cell(22, 5, decode_fr(montant_restant_pourcent(array_sum($b), array_sum($a))).'%' , 1, 0, 'L');
                                $pdf->Ln(2);
                            }

                        }else if($_POST['type_frais'] !="Tous"){
                            $sql = "SELECT faculte, promotion, type_frais, SUM(montant) as mt FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ? AND type_frais = ? GROUP BY faculte, type_frais";
                            $sql_array = array($data_student['matricule'], $data_student['promotion'], $data_student['fac'], $data_student['annee_academique'], $_POST['type_frais']);
                                                                        
                            $all = ConnexionBdd::Connecter()->prepare($sql);
                            $all->execute($sql_array);

                            if($all->rowCount() > 0){
                                while ($data = $all->fetch()){
                                    $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ? AND type_frais = ?";
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute(array($data_student['matricule'], $data['faculte'], $data['promotion'], $data['type_frais'])); 
                                    $pdf->SetFont('Arial','',8);
                                    while($d = $sql_2->fetch()){
                                        $pdf->cell(60, 5, ucfirst(decode_fr($data['type_frais'])) , 1, 0, 'L');
                                        $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L');
                                        $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                        $pdf->cell(22, 5, '$ '.mm($data['mt']-$d['mp']), 1, 0, 'L');
                                        $pdf->cell(22, 5, montant_restant_pourcent($d['mp'], $data['mt']).' % ', 1, 0, 'L');
                                        // on affete les frais dans le tableau
                                        $a[] = $data['mt'];
                                        $b[] = $d['mp'];
                                        $c[] = $data['mt']-$d['mp'];
                                        $pdf->Ln(5);
                                    }
                                }
                                
                                // total de frais
                                if(count($a) >= 0 && count($b) >= 0 && count($c) >= 0){
                                    $pdf->cell(60, 5, decode_fr("Total") , 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.decode_fr(array_sum($a)) , 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.decode_fr(array_sum($b)) , 1, 0, 'L');
                                    $pdf->cell(22, 5, '$ '.decode_fr(array_sum($c)) , 1, 0, 'L');
                                    $pdf->cell(22, 5, decode_fr(montant_restant_pourcent(array_sum($b), array_sum($a))).'%' , 1, 0, 'L');
                                    $a = array();
                                    $b = array();
                                    $c = array();
                                    $pdf->Ln(5);
                                }else{
                                    $pdf->cell(154, 5, decode_fr("Aucun frais affecté à l'étudiant(e)") , 1, 0, 'L');$pdf->Ln(5);
                                    $pdf->cell(60, 5, decode_fr("Total") , 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.decode_fr(array_sum($a)) , 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.decode_fr(array_sum($b)) , 1, 0, 'L');
                                    $pdf->cell(22, 5, '$ '.decode_fr(array_sum($c)) , 1, 0, 'L');
                                    $pdf->cell(22, 5, decode_fr(montant_restant_pourcent(array_sum($b), array_sum($a))).'%' , 1, 0, 'L');
                                    $pdf->Ln(2);
                                }
                            }else{
                                $pdf->cell(154, 5, decode_fr("ce type de frais nest pas affecté à l'étudiant(e)") , 1, 0, 'L');$pdf->Ln(5);
                                $pdf->cell(60, 5, decode_fr("Total") , 1, 0, 'L');
                                $pdf->cell(25, 5, '$ '.decode_fr(array_sum($a)) , 1, 0, 'L');
                                $pdf->cell(25, 5, '$ '.decode_fr(array_sum($b)) , 1, 0, 'L');
                                $pdf->cell(22, 5, '$ '.decode_fr(array_sum($c)) , 1, 0, 'L');
                                $pdf->cell(22, 5, decode_fr(montant_restant_pourcent(array_sum($b), array_sum($a))).'%' , 1, 0, 'L');
                                $pdf->Ln(2);
                            }
                        }
                        $pdf->Ln(5);
                    }
                    $pdf->Ln(5);
                }else if(!empty($_POST['pourcent_debut']) && !empty($_POST['pourcent_fin'])){
                    // on commence par selectionner tous les etudiants
                    $sel_all = ConnexionBdd::Connecter()->prepare("SELECT * FROM etudiants_inscrits WHERE annee_academique = ? ORDER BY fac, promotion, noms ASC");
                    $sel_all->execute(array($annee_acad_fin));
                    while($data_student = $sel_all->fetch()){
                        $pdf->SetFont('Arial','',8);
                        $pdf->cell(60, 5, 'Matricule      : '.decode_fr($data_student['matricule']), 0, 1, 'L');
                        $pdf->cell(60, 5, 'Noms           : '.decode_fr($data_student['noms']), 0, 1, 'L');
                        $pdf->cell(60, 5, 'Faculte         : '.decode_fr($data_student['fac']), 0, 1, 'L');
                        $pdf->cell(60, 5, 'Promotion     : '.decode_fr($data_student['promotion']), 0, 1, 'L');
                        $pdf->cell(60, 5, 'Annee Acad. : '.decode_fr($data_student['annee_academique']), 0, 1, 'L');

                        // selection du montant
                        $pdf->SetFont('Arial','B',8);
                        $pdf->cell(60, 5, "Type frais" , 1, 0, 'L');
                        $pdf->cell(25, 5, "Montant prevu", 1, 0, 'L');
                        $pdf->cell(25, 5, "montant payer", 1, 0, 'L');
                        $pdf->cell(22, 5, "Solde", 1, 0, 'L');
                        $pdf->cell(22, 5, "%", 1, 0, 'L');
                        $pdf->Ln(5);
                        if($_POST['type_frais'] == "Tous"){
                            $sql = "SELECT faculte, promotion, type_frais, SUM(montant) as mt FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ? GROUP BY faculte, type_frais";
                            $sql_array = array($data_student['matricule'], $data_student['promotion'], $data_student['fac'], $data_student['annee_academique']);
                                                                        
                            $all = ConnexionBdd::Connecter()->prepare($sql);
                            $all->execute($sql_array);

                            while ($data = $all->fetch()){
                                $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ? AND type_frais = ?  HAVING ROUND(mp * 100 / ?) >= ? AND ROUND(mp * 100 / ?) <= ?";
                                $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                $sql_2->execute(array($data_student['matricule'], $data['faculte'], $data['promotion'], $data['type_frais'], $data['mt'], $_POST['pourcent_debut'], $data['mt'], $_POST['pourcent_fin'])); 
                                $pdf->SetFont('Arial','',8);
                                while($d = $sql_2->fetch()){
                                    $pdf->cell(60, 5, ucfirst(decode_fr($data['type_frais'])) , 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                    $pdf->cell(22, 5, '$ '.mm($data['mt']-$d['mp']), 1, 0, 'L');
                                    $pdf->cell(22, 5, montant_restant_pourcent($d['mp'], $data['mt']).' % ', 1, 0, 'L');
                                    // on affete les frais dans le tableau
                                    $a[] = $data['mt'];
                                    $b[] = $d['mp'];
                                    $c[] = $data['mt']-$d['mp'];
                                    $pdf->Ln(5);
                                }
                            }
                            
                            // total de frais
                            if(count($a) > 0 && count($b) > 0 && count($c) > 0){
                                $pdf->cell(60, 5, decode_fr("Total") , 1, 0, 'L');
                                $pdf->cell(25, 5, '$ '.decode_fr(array_sum($a)) , 1, 0, 'L');
                                $pdf->cell(25, 5, '$ '.decode_fr(array_sum($b)) , 1, 0, 'L');
                                $pdf->cell(22, 5, '$ '.decode_fr(array_sum($c)) , 1, 0, 'L');
                                $pdf->cell(22, 5, decode_fr(montant_restant_pourcent(array_sum($b), array_sum($a))).'%' , 1, 0, 'L');
                                $a = array();
                                $b = array();
                                $c = array();
                                $pdf->Ln(5);
                            }else{
                                $pdf->cell(154, 5, decode_fr("Aucun frais affecté à l'étudiant(e)") , 1, 0, 'L');$pdf->Ln(5);
                                $pdf->cell(60, 5, decode_fr("Total") , 1, 0, 'L');
                                $pdf->cell(25, 5, '$ '.decode_fr(array_sum($a)) , 1, 0, 'L');
                                $pdf->cell(25, 5, '$ '.decode_fr(array_sum($b)) , 1, 0, 'L');
                                $pdf->cell(22, 5, '$ '.decode_fr(array_sum($c)) , 1, 0, 'L');
                                $pdf->cell(22, 5, decode_fr(montant_restant_pourcent(array_sum($b), array_sum($a))).'%' , 1, 0, 'L');
                                $pdf->Ln(2);
                            }

                        }else if($_POST['type_frais'] !="Tous"){
                            $sql = "SELECT faculte, promotion, type_frais, SUM(montant) as mt FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ? AND type_frais = ? GROUP BY faculte, type_frais";
                            $sql_array = array($data_student['matricule'], $data_student['promotion'], $data_student['fac'], $data_student['annee_academique'], $_POST['type_frais']);
                                                                        
                            $all = ConnexionBdd::Connecter()->prepare($sql);
                            $all->execute($sql_array);

                            if($all->rowCount() > 0){
                                while ($data = $all->fetch()){
                                    $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ? AND type_frais = ? HAVING ROUND(mp * 100 / ?) >= ? AND ROUND(mp * 100 / ?) <= ?";
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute(array($data_student['matricule'], $data['faculte'], $data['promotion'], $data['type_frais'], $data['mt'], $_POST['pourcent_debut'], $data['mt'], $_POST['pourcent_fin'])); 
                                    $pdf->SetFont('Arial','',8);
                                    while($d = $sql_2->fetch()){
                                        $pdf->cell(60, 5, ucfirst(decode_fr($data['type_frais'])) , 1, 0, 'L');
                                        $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L');
                                        $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                        $pdf->cell(22, 5, '$ '.mm($data['mt']-$d['mp']), 1, 0, 'L');
                                        $pdf->cell(22, 5, montant_restant_pourcent($d['mp'], $data['mt']).' % ', 1, 0, 'L');
                                        // on affete les frais dans le tableau
                                        $a[] = $data['mt'];
                                        $b[] = $d['mp'];
                                        $c[] = $data['mt']-$d['mp'];
                                        $pdf->Ln(5);
                                    }
                                }
                                
                                // total de frais
                                if(count($a) >= 0 && count($b) >= 0 && count($c) >= 0){
                                    $pdf->cell(60, 5, decode_fr("Total") , 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.decode_fr(array_sum($a)) , 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.decode_fr(array_sum($b)) , 1, 0, 'L');
                                    $pdf->cell(22, 5, '$ '.decode_fr(array_sum($c)) , 1, 0, 'L');
                                    $pdf->cell(22, 5, decode_fr(montant_restant_pourcent(array_sum($b), array_sum($a))).'%' , 1, 0, 'L');
                                    $a = array();
                                    $b = array();
                                    $c = array();
                                    $pdf->Ln(5);
                                }
                            }else{
                                $pdf->cell(154, 5, decode_fr("ce type de frais nest pas affecté à l'étudiant(e)") , 1, 0, 'L');$pdf->Ln(5);
                                $pdf->cell(60, 5, decode_fr("Total") , 1, 0, 'L');
                                $pdf->cell(25, 5, '$ '.decode_fr(array_sum($a)) , 1, 0, 'L');
                                $pdf->cell(25, 5, '$ '.decode_fr(array_sum($b)) , 1, 0, 'L');
                                $pdf->cell(22, 5, '$ '.decode_fr(array_sum($c)) , 1, 0, 'L');
                                $pdf->cell(22, 5, decode_fr(montant_restant_pourcent(array_sum($b), array_sum($a))).'%' , 1, 0, 'L');
                                $pdf->Ln(2);
                            }
                        }
                        $pdf->Ln(5);
                    }
                    $pdf->Ln(5);
                }
            }else if($_POST['fac_etudiant'] != "Tous" && $_POST['promotion_etud'] == "Tous"){
                if(empty($_POST['pourcent_debut']) && empty($_POST['pourcent_fin'])){
                    foreach($t_promotion as $pp){
                        // on commence par selectionner tous les etudiants
                        $sel_all = ConnexionBdd::Connecter()->prepare("SELECT * FROM etudiants_inscrits WHERE annee_academique = ? AND fac = ? AND promotion = ? ORDER BY fac, promotion, noms ASC");
                        $sel_all->execute(array($annee_acad_fin, $_POST['fac_etudiant'], $pp));

                        while($data_student = $sel_all->fetch()){
                            $pdf->SetFont('Arial','',8);
                            $pdf->cell(60, 5, 'Matricule      : '.decode_fr($data_student['matricule']), 0, 1, 'L');
                            $pdf->cell(60, 5, 'Noms           : '.decode_fr($data_student['noms']), 0, 1, 'L');
                            $pdf->cell(60, 5, 'Faculte         : '.decode_fr($data_student['fac']), 0, 1, 'L');
                            $pdf->cell(60, 5, 'Promotion     : '.decode_fr($data_student['promotion']), 0, 1, 'L');
                            $pdf->cell(60, 5, 'Annee Acad. : '.decode_fr($data_student['annee_academique']), 0, 1, 'L');

                            // selection du montant
                            $pdf->SetFont('Arial','B',8);
                            $pdf->cell(60, 5, "Type frais" , 1, 0, 'L');
                            $pdf->cell(25, 5, "Montant prevu", 1, 0, 'L');
                            $pdf->cell(25, 5, "montant payer", 1, 0, 'L');
                            $pdf->cell(22, 5, "Solde", 1, 0, 'L');
                            $pdf->cell(22, 5, "%", 1, 0, 'L');
                            $pdf->Ln(5);
                            if($_POST['type_frais'] == "Tous"){
                                $sql = "SELECT faculte, promotion, type_frais, SUM(montant) as mt FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ? GROUP BY faculte, type_frais";
                                $sql_array = array($data_student['matricule'], $data_student['promotion'], $data_student['fac'], $data_student['annee_academique']);
                                                                            
                                $all = ConnexionBdd::Connecter()->prepare($sql);
                                $all->execute($sql_array);

                                while ($data = $all->fetch()){
                                    $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ? AND type_frais = ?  HAVING mp >= 0";
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute(array($data_student['matricule'], $data['faculte'], $data['promotion'], $data['type_frais'])); 
                                    $pdf->SetFont('Arial','',8);
                                    while($d = $sql_2->fetch()){
                                        $pdf->cell(60, 5, ucfirst(decode_fr($data['type_frais'])) , 1, 0, 'L');
                                        $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L');
                                        $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                        $pdf->cell(22, 5, '$ '.mm($data['mt']-$d['mp']), 1, 0, 'L');
                                        $pdf->cell(22, 5, montant_restant_pourcent($d['mp'], $data['mt']).' % ', 1, 0, 'L');
                                        // on affete les frais dans le tableau
                                        $a[] = $data['mt'];
                                        $b[] = $d['mp'];
                                        $c[] = $data['mt']-$d['mp'];
                                        $pdf->Ln(5);
                                    }
                                }
                                
                                // total de frais
                                if(count($a) > 0 && count($b) > 0 && count($c) > 0){
                                    $pdf->cell(60, 5, decode_fr("Total") , 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.decode_fr(array_sum($a)) , 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.decode_fr(array_sum($b)) , 1, 0, 'L');
                                    $pdf->cell(22, 5, '$ '.decode_fr(array_sum($c)) , 1, 0, 'L');
                                    $pdf->cell(22, 5, decode_fr(montant_restant_pourcent(array_sum($b), array_sum($a))).'%' , 1, 0, 'L');
                                    $a = array();
                                    $b = array();
                                    $c = array();
                                    $pdf->Ln(5);
                                }else{
                                    $pdf->cell(154, 5, decode_fr("Aucun frais affecté à l'étudiant(e)") , 1, 0, 'L');$pdf->Ln(5);
                                    $pdf->cell(60, 5, decode_fr("Total") , 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.decode_fr(array_sum($a)) , 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.decode_fr(array_sum($b)) , 1, 0, 'L');
                                    $pdf->cell(22, 5, '$ '.decode_fr(array_sum($c)) , 1, 0, 'L');
                                    $pdf->cell(22, 5, decode_fr(montant_restant_pourcent(array_sum($b), array_sum($a))).'%' , 1, 0, 'L');
                                    $pdf->Ln(2);
                                }

                            }else if($_POST['type_frais'] !="Tous"){
                                $sql = "SELECT faculte, promotion, type_frais, SUM(montant) as mt FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ? AND type_frais = ? GROUP BY faculte, type_frais";
                                $sql_array = array($data_student['matricule'], $data_student['promotion'], $data_student['fac'], $data_student['annee_academique'], $_POST['type_frais']);
                                                                            
                                $all = ConnexionBdd::Connecter()->prepare($sql);
                                $all->execute($sql_array);

                                if($all->rowCount() > 0){
                                    while ($data = $all->fetch()){
                                        $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ? AND type_frais = ?";
                                        $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                        $sql_2->execute(array($data_student['matricule'], $data['faculte'], $data['promotion'], $data['type_frais'])); 
                                        $pdf->SetFont('Arial','',8);
                                        while($d = $sql_2->fetch()){
                                            $pdf->cell(60, 5, ucfirst(decode_fr($data['type_frais'])) , 1, 0, 'L');
                                            $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L');
                                            $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                            $pdf->cell(22, 5, '$ '.mm($data['mt']-$d['mp']), 1, 0, 'L');
                                            $pdf->cell(22, 5, montant_restant_pourcent($d['mp'], $data['mt']).' % ', 1, 0, 'L');
                                            // on affete les frais dans le tableau
                                            $a[] = $data['mt'];
                                            $b[] = $d['mp'];
                                            $c[] = $data['mt']-$d['mp'];
                                            $pdf->Ln(5);
                                        }
                                    }
                                    
                                    // total de frais
                                    if(count($a) >= 0 && count($b) >= 0 && count($c) >= 0){
                                        $pdf->cell(60, 5, decode_fr("Total") , 1, 0, 'L');
                                        $pdf->cell(25, 5, '$ '.decode_fr(array_sum($a)) , 1, 0, 'L');
                                        $pdf->cell(25, 5, '$ '.decode_fr(array_sum($b)) , 1, 0, 'L');
                                        $pdf->cell(22, 5, '$ '.decode_fr(array_sum($c)) , 1, 0, 'L');
                                        $pdf->cell(22, 5, decode_fr(montant_restant_pourcent(array_sum($b), array_sum($a))).'%' , 1, 0, 'L');
                                        $a = array();
                                        $b = array();
                                        $c = array();
                                        $pdf->Ln(5);
                                    }else{
                                        $pdf->cell(154, 5, decode_fr("Aucun frais affecté à l'étudiant(e)") , 1, 0, 'L');$pdf->Ln(5);
                                        $pdf->cell(60, 5, decode_fr("Total") , 1, 0, 'L');
                                        $pdf->cell(25, 5, '$ '.decode_fr(array_sum($a)) , 1, 0, 'L');
                                        $pdf->cell(25, 5, '$ '.decode_fr(array_sum($b)) , 1, 0, 'L');
                                        $pdf->cell(22, 5, '$ '.decode_fr(array_sum($c)) , 1, 0, 'L');
                                        $pdf->cell(22, 5, decode_fr(montant_restant_pourcent(array_sum($b), array_sum($a))).'%' , 1, 0, 'L');
                                        $pdf->Ln(2);
                                    }
                                }else{
                                    $pdf->cell(154, 5, decode_fr("ce type de frais nest pas affecté à l'étudiant(e)") , 1, 0, 'L');$pdf->Ln(5);
                                    $pdf->cell(60, 5, decode_fr("Total") , 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.decode_fr(array_sum($a)) , 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.decode_fr(array_sum($b)) , 1, 0, 'L');
                                    $pdf->cell(22, 5, '$ '.decode_fr(array_sum($c)) , 1, 0, 'L');
                                    $pdf->cell(22, 5, decode_fr(montant_restant_pourcent(array_sum($b), array_sum($a))).'%' , 1, 0, 'L');
                                    $pdf->Ln(2);
                                }
                            }
                            $pdf->Ln(5);
                        }
                    }
                    $pdf->Ln(5);
                }else if(!empty($_POST['pourcent_debut']) && !empty($_POST['pourcent_fin'])){
                    foreach($t_promotion as $pp){
                        // on commence par selectionner tous les etudiants
                        $sel_all = ConnexionBdd::Connecter()->prepare("SELECT * FROM etudiants_inscrits WHERE annee_academique = ? AND fac = ? AND promotion = ? ORDER BY fac, promotion, noms ASC");
                        $sel_all->execute(array($annee_acad_fin, $_POST['fac_etudiant'], $pp));
                        while($data_student = $sel_all->fetch()){
                            $pdf->SetFont('Arial','',8);
                            $pdf->cell(60, 5, 'Matricule      : '.decode_fr($data_student['matricule']), 0, 1, 'L');
                            $pdf->cell(60, 5, 'Noms           : '.decode_fr($data_student['noms']), 0, 1, 'L');
                            $pdf->cell(60, 5, 'Faculte         : '.decode_fr($data_student['fac']), 0, 1, 'L');
                            $pdf->cell(60, 5, 'Promotion     : '.decode_fr($data_student['promotion']), 0, 1, 'L');
                            $pdf->cell(60, 5, 'Annee Acad. : '.decode_fr($data_student['annee_academique']), 0, 1, 'L');

                            // selection du montant
                            $pdf->SetFont('Arial','B',8);
                            $pdf->cell(60, 5, "Type frais" , 1, 0, 'L');
                            $pdf->cell(25, 5, "Montant prevu", 1, 0, 'L');
                            $pdf->cell(25, 5, "montant payer", 1, 0, 'L');
                            $pdf->cell(22, 5, "Solde", 1, 0, 'L');
                            $pdf->cell(22, 5, "%", 1, 0, 'L');
                            $pdf->Ln(5);
                            if($_POST['type_frais'] == "Tous"){
                                $sql = "SELECT faculte, promotion, type_frais, SUM(montant) as mt FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ? GROUP BY faculte, type_frais";
                                $sql_array = array($data_student['matricule'], $data_student['promotion'], $data_student['fac'], $data_student['annee_academique']);
                                                                            
                                $all = ConnexionBdd::Connecter()->prepare($sql);
                                $all->execute($sql_array);

                                while ($data = $all->fetch()){
                                    $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ? AND type_frais = ?  HAVING ROUND(mp * 100 / ?) >= ? AND ROUND(mp * 100 / ?) <= ?";
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute(array($data_student['matricule'], $data['faculte'], $data['promotion'], $data['type_frais'], $data['mt'], $_POST['pourcent_debut'], $data['mt'], $_POST['pourcent_fin'])); 
                                    $pdf->SetFont('Arial','',8);
                                    while($d = $sql_2->fetch()){
                                        $pdf->cell(60, 5, ucfirst(decode_fr($data['type_frais'])) , 1, 0, 'L');
                                        $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L');
                                        $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                        $pdf->cell(22, 5, '$ '.mm($data['mt']-$d['mp']), 1, 0, 'L');
                                        $pdf->cell(22, 5, montant_restant_pourcent($d['mp'], $data['mt']).' % ', 1, 0, 'L');
                                        // on affete les frais dans le tableau
                                        $a[] = $data['mt'];
                                        $b[] = $d['mp'];
                                        $c[] = $data['mt']-$d['mp'];
                                        $pdf->Ln(5);
                                    }
                                }
                                
                                // total de frais
                                if(count($a) > 0 && count($b) > 0 && count($c) > 0){
                                    $pdf->cell(60, 5, decode_fr("Total") , 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.decode_fr(array_sum($a)) , 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.decode_fr(array_sum($b)) , 1, 0, 'L');
                                    $pdf->cell(22, 5, '$ '.decode_fr(array_sum($c)) , 1, 0, 'L');
                                    $pdf->cell(22, 5, decode_fr(montant_restant_pourcent(array_sum($b), array_sum($a))).'%' , 1, 0, 'L');
                                    $a = array();
                                    $b = array();
                                    $c = array();
                                    $pdf->Ln(5);
                                }else{
                                    $pdf->cell(154, 5, decode_fr("Aucun frais affecté à l'étudiant(e)") , 1, 0, 'L');$pdf->Ln(5);
                                    $pdf->cell(60, 5, decode_fr("Total") , 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.decode_fr(array_sum($a)) , 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.decode_fr(array_sum($b)) , 1, 0, 'L');
                                    $pdf->cell(22, 5, '$ '.decode_fr(array_sum($c)) , 1, 0, 'L');
                                    $pdf->cell(22, 5, decode_fr(montant_restant_pourcent(array_sum($b), array_sum($a))).'%' , 1, 0, 'L');
                                    $pdf->Ln(2);
                                }

                            }else if($_POST['type_frais'] !="Tous"){
                                $sql = "SELECT faculte, promotion, type_frais, SUM(montant) as mt FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ? AND type_frais = ? GROUP BY faculte, type_frais";
                                $sql_array = array($data_student['matricule'], $data_student['promotion'], $data_student['fac'], $data_student['annee_academique'], $_POST['type_frais']);
                                                                            
                                $all = ConnexionBdd::Connecter()->prepare($sql);
                                $all->execute($sql_array);

                                if($all->rowCount() > 0){
                                    while ($data = $all->fetch()){
                                        $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ? AND type_frais = ? HAVING ROUND(mp * 100 / ?) >= ? AND ROUND(mp * 100 / ?) <= ?";
                                        $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                        $sql_2->execute(array($data_student['matricule'], $data['faculte'], $data['promotion'], $data['type_frais'], $data['mt'], $_POST['pourcent_debut'], $data['mt'], $_POST['pourcent_fin'])); 
                                        $pdf->SetFont('Arial','',8);
                                        while($d = $sql_2->fetch()){
                                            $pdf->cell(60, 5, ucfirst(decode_fr($data['type_frais'])) , 1, 0, 'L');
                                            $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L');
                                            $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                            $pdf->cell(22, 5, '$ '.mm($data['mt']-$d['mp']), 1, 0, 'L');
                                            $pdf->cell(22, 5, montant_restant_pourcent($d['mp'], $data['mt']).' % ', 1, 0, 'L');
                                            // on affete les frais dans le tableau
                                            $a[] = $data['mt'];
                                            $b[] = $d['mp'];
                                            $c[] = $data['mt']-$d['mp'];
                                            $pdf->Ln(5);
                                        }
                                    }
                                    
                                    // total de frais
                                    if(count($a) >= 0 && count($b) >= 0 && count($c) >= 0){
                                        $pdf->cell(60, 5, decode_fr("Total") , 1, 0, 'L');
                                        $pdf->cell(25, 5, '$ '.decode_fr(array_sum($a)) , 1, 0, 'L');
                                        $pdf->cell(25, 5, '$ '.decode_fr(array_sum($b)) , 1, 0, 'L');
                                        $pdf->cell(22, 5, '$ '.decode_fr(array_sum($c)) , 1, 0, 'L');
                                        $pdf->cell(22, 5, decode_fr(montant_restant_pourcent(array_sum($b), array_sum($a))).'%' , 1, 0, 'L');
                                        $a = array();
                                        $b = array();
                                        $c = array();
                                        $pdf->Ln(5);
                                    }
                                }else{
                                    $pdf->cell(154, 5, decode_fr("ce type de frais nest pas affecté à l'étudiant(e)") , 1, 0, 'L');$pdf->Ln(5);
                                    $pdf->cell(60, 5, decode_fr("Total") , 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.decode_fr(array_sum($a)) , 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.decode_fr(array_sum($b)) , 1, 0, 'L');
                                    $pdf->cell(22, 5, '$ '.decode_fr(array_sum($c)) , 1, 0, 'L');
                                    $pdf->cell(22, 5, decode_fr(montant_restant_pourcent(array_sum($b), array_sum($a))).'%' , 1, 0, 'L');
                                    $pdf->Ln(2);
                                }
                            }
                            $pdf->Ln(5);
                        }
                    }
                    $pdf->Ln(5);
                }
            }else if($_POST['fac_etudiant'] != "Tous" && $_POST['promotion_etud'] != "Tous"){
                if(empty($_POST['pourcent_debut']) && empty($_POST['pourcent_fin'])){
                    // on commence par selectionner tous les etudiants
                    $sel_all = ConnexionBdd::Connecter()->prepare("SELECT * FROM etudiants_inscrits WHERE annee_academique = ? AND fac = ? AND promotion = ? ORDER BY fac, promotion, noms ASC");
                    $sel_all->execute(array($annee_acad_fin, $_POST['fac_etudiant'], $_POST['promotion_etud']));

                    while($data_student = $sel_all->fetch()){
                        $pdf->SetFont('Arial','',8);
                        $pdf->cell(60, 5, 'Matricule      : '.decode_fr($data_student['matricule']), 0, 1, 'L');
                        $pdf->cell(60, 5, 'Noms           : '.decode_fr($data_student['noms']), 0, 1, 'L');
                        $pdf->cell(60, 5, 'Faculte         : '.decode_fr($data_student['fac']), 0, 1, 'L');
                        $pdf->cell(60, 5, 'Promotion     : '.decode_fr($data_student['promotion']), 0, 1, 'L');
                        $pdf->cell(60, 5, 'Annee Acad. : '.decode_fr($data_student['annee_academique']), 0, 1, 'L');

                        // selection du montant
                        $pdf->SetFont('Arial','B',8);
                        $pdf->cell(60, 5, "Type frais" , 1, 0, 'L');
                        $pdf->cell(25, 5, "Montant prevu", 1, 0, 'L');
                        $pdf->cell(25, 5, "montant payer", 1, 0, 'L');
                        $pdf->cell(22, 5, "Solde", 1, 0, 'L');
                        $pdf->cell(22, 5, "%", 1, 0, 'L');
                        $pdf->Ln(5);
                        if($_POST['type_frais'] == "Tous"){
                            $sql = "SELECT faculte, promotion, type_frais, SUM(montant) as mt FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ? GROUP BY faculte, type_frais";
                            $sql_array = array($data_student['matricule'], $data_student['promotion'], $data_student['fac'], $data_student['annee_academique']);
                                                                        
                            $all = ConnexionBdd::Connecter()->prepare($sql);
                            $all->execute($sql_array);

                            while ($data = $all->fetch()){
                                $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ? AND type_frais = ?  HAVING mp >= 0";
                                $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                $sql_2->execute(array($data_student['matricule'], $data['faculte'], $data['promotion'], $data['type_frais'])); 
                                $pdf->SetFont('Arial','',8);
                                while($d = $sql_2->fetch()){
                                    $pdf->cell(60, 5, ucfirst(decode_fr($data['type_frais'])) , 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                    $pdf->cell(22, 5, '$ '.mm($data['mt']-$d['mp']), 1, 0, 'L');
                                    $pdf->cell(22, 5, montant_restant_pourcent($d['mp'], $data['mt']).' % ', 1, 0, 'L');
                                    // on affete les frais dans le tableau
                                    $a[] = $data['mt'];
                                    $b[] = $d['mp'];
                                    $c[] = $data['mt']-$d['mp'];
                                    $pdf->Ln(5);
                                }
                            }
                            
                            // total de frais
                            if(count($a) > 0 && count($b) > 0 && count($c) > 0){
                                $pdf->cell(60, 5, decode_fr("Total") , 1, 0, 'L');
                                $pdf->cell(25, 5, '$ '.decode_fr(array_sum($a)) , 1, 0, 'L');
                                $pdf->cell(25, 5, '$ '.decode_fr(array_sum($b)) , 1, 0, 'L');
                                $pdf->cell(22, 5, '$ '.decode_fr(array_sum($c)) , 1, 0, 'L');
                                $pdf->cell(22, 5, decode_fr(montant_restant_pourcent(array_sum($b), array_sum($a))).'%' , 1, 0, 'L');
                                $a = array();
                                $b = array();
                                $c = array();
                                $pdf->Ln(5);
                            }else{
                                $pdf->cell(154, 5, decode_fr("Aucun frais affecté à l'étudiant(e)") , 1, 0, 'L');$pdf->Ln(5);
                                $pdf->cell(60, 5, decode_fr("Total") , 1, 0, 'L');
                                $pdf->cell(25, 5, '$ '.decode_fr(array_sum($a)) , 1, 0, 'L');
                                $pdf->cell(25, 5, '$ '.decode_fr(array_sum($b)) , 1, 0, 'L');
                                $pdf->cell(22, 5, '$ '.decode_fr(array_sum($c)) , 1, 0, 'L');
                                $pdf->cell(22, 5, decode_fr(montant_restant_pourcent(array_sum($b), array_sum($a))).'%' , 1, 0, 'L');
                                $pdf->Ln(2);
                            }

                        }else if($_POST['type_frais'] !="Tous"){
                            $sql = "SELECT faculte, promotion, type_frais, SUM(montant) as mt FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ? AND type_frais = ? GROUP BY faculte, type_frais";
                            $sql_array = array($data_student['matricule'], $data_student['promotion'], $data_student['fac'], $data_student['annee_academique'], $_POST['type_frais']);
                                                                        
                            $all = ConnexionBdd::Connecter()->prepare($sql);
                            $all->execute($sql_array);

                            if($all->rowCount() > 0){
                                while ($data = $all->fetch()){
                                    $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ? AND type_frais = ?";
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute(array($data_student['matricule'], $data['faculte'], $data['promotion'], $data['type_frais'])); 
                                    $pdf->SetFont('Arial','',8);
                                    while($d = $sql_2->fetch()){
                                        $pdf->cell(60, 5, ucfirst(decode_fr($data['type_frais'])) , 1, 0, 'L');
                                        $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L');
                                        $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                        $pdf->cell(22, 5, '$ '.mm($data['mt']-$d['mp']), 1, 0, 'L');
                                        $pdf->cell(22, 5, montant_restant_pourcent($d['mp'], $data['mt']).' % ', 1, 0, 'L');
                                        // on affete les frais dans le tableau
                                        $a[] = $data['mt'];
                                        $b[] = $d['mp'];
                                        $c[] = $data['mt']-$d['mp'];
                                        $pdf->Ln(5);
                                    }
                                }
                                
                                // total de frais
                                if(count($a) >= 0 && count($b) >= 0 && count($c) >= 0){
                                    $pdf->cell(60, 5, decode_fr("Total") , 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.decode_fr(array_sum($a)) , 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.decode_fr(array_sum($b)) , 1, 0, 'L');
                                    $pdf->cell(22, 5, '$ '.decode_fr(array_sum($c)) , 1, 0, 'L');
                                    $pdf->cell(22, 5, decode_fr(montant_restant_pourcent(array_sum($b), array_sum($a))).'%' , 1, 0, 'L');
                                    $a = array();
                                    $b = array();
                                    $c = array();
                                    $pdf->Ln(5);
                                }else{
                                    $pdf->cell(154, 5, decode_fr("Aucun frais affecté à l'étudiant(e)") , 1, 0, 'L');$pdf->Ln(5);
                                    $pdf->cell(60, 5, decode_fr("Total") , 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.decode_fr(array_sum($a)) , 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.decode_fr(array_sum($b)) , 1, 0, 'L');
                                    $pdf->cell(22, 5, '$ '.decode_fr(array_sum($c)) , 1, 0, 'L');
                                    $pdf->cell(22, 5, decode_fr(montant_restant_pourcent(array_sum($b), array_sum($a))).'%' , 1, 0, 'L');
                                    $pdf->Ln(2);
                                }
                            }else{
                                $pdf->cell(154, 5, decode_fr("ce type de frais nest pas affecté à l'étudiant(e)") , 1, 0, 'L');$pdf->Ln(5);
                                $pdf->cell(60, 5, decode_fr("Total") , 1, 0, 'L');
                                $pdf->cell(25, 5, '$ '.decode_fr(array_sum($a)) , 1, 0, 'L');
                                $pdf->cell(25, 5, '$ '.decode_fr(array_sum($b)) , 1, 0, 'L');
                                $pdf->cell(22, 5, '$ '.decode_fr(array_sum($c)) , 1, 0, 'L');
                                $pdf->cell(22, 5, decode_fr(montant_restant_pourcent(array_sum($b), array_sum($a))).'%' , 1, 0, 'L');
                                $pdf->Ln(2);
                            }
                        }
                        $pdf->Ln(5);
                    }
                    $pdf->Ln(5);
                }else if(!empty($_POST['pourcent_debut']) && !empty($_POST['pourcent_fin'])){
                    // on commence par selectionner tous les etudiants
                    $sel_all = ConnexionBdd::Connecter()->prepare("SELECT * FROM etudiants_inscrits WHERE annee_academique = ? AND fac = ? AND promotion = ? ORDER BY fac, promotion, noms ASC");
                    $sel_all->execute(array($annee_acad_fin, $_POST['fac_etudiant'], $_POST['promotion_etud']));
                    while($data_student = $sel_all->fetch()){
                        $pdf->SetFont('Arial','',8);
                        $pdf->cell(60, 5, 'Matricule      : '.decode_fr($data_student['matricule']), 0, 1, 'L');
                        $pdf->cell(60, 5, 'Noms           : '.decode_fr($data_student['noms']), 0, 1, 'L');
                        $pdf->cell(60, 5, 'Faculte         : '.decode_fr($data_student['fac']), 0, 1, 'L');
                        $pdf->cell(60, 5, 'Promotion     : '.decode_fr($data_student['promotion']), 0, 1, 'L');
                        $pdf->cell(60, 5, 'Annee Acad. : '.decode_fr($data_student['annee_academique']), 0, 1, 'L');

                        // selection du montant
                        $pdf->SetFont('Arial','B',8);
                        $pdf->cell(60, 5, "Type frais" , 1, 0, 'L');
                        $pdf->cell(25, 5, "Montant prevu", 1, 0, 'L');
                        $pdf->cell(25, 5, "montant payer", 1, 0, 'L');
                        $pdf->cell(22, 5, "Solde", 1, 0, 'L');
                        $pdf->cell(22, 5, "%", 1, 0, 'L');
                        $pdf->Ln(5);
                        if($_POST['type_frais'] == "Tous"){
                            $sql = "SELECT faculte, promotion, type_frais, SUM(montant) as mt FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ? GROUP BY faculte, type_frais";
                            $sql_array = array($data_student['matricule'], $data_student['promotion'], $data_student['fac'], $data_student['annee_academique']);
                                                                        
                            $all = ConnexionBdd::Connecter()->prepare($sql);
                            $all->execute($sql_array);

                            while ($data = $all->fetch()){
                                $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ? AND type_frais = ?  HAVING ROUND(mp * 100 / ?) >= ? AND ROUND(mp * 100 / ?) <= ?";
                                $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                $sql_2->execute(array($data_student['matricule'], $data['faculte'], $data['promotion'], $data['type_frais'], $data['mt'], $_POST['pourcent_debut'], $data['mt'], $_POST['pourcent_fin'])); 
                                $pdf->SetFont('Arial','',8);
                                while($d = $sql_2->fetch()){
                                    $pdf->cell(60, 5, ucfirst(decode_fr($data['type_frais'])) , 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                    $pdf->cell(22, 5, '$ '.mm($data['mt']-$d['mp']), 1, 0, 'L');
                                    $pdf->cell(22, 5, montant_restant_pourcent($d['mp'], $data['mt']).' % ', 1, 0, 'L');
                                    // on affete les frais dans le tableau
                                    $a[] = $data['mt'];
                                    $b[] = $d['mp'];
                                    $c[] = $data['mt']-$d['mp'];
                                    $pdf->Ln(5);
                                }
                            }
                            
                            // total de frais
                            if(count($a) > 0 && count($b) > 0 && count($c) > 0){
                                $pdf->cell(60, 5, decode_fr("Total") , 1, 0, 'L');
                                $pdf->cell(25, 5, '$ '.decode_fr(array_sum($a)) , 1, 0, 'L');
                                $pdf->cell(25, 5, '$ '.decode_fr(array_sum($b)) , 1, 0, 'L');
                                $pdf->cell(22, 5, '$ '.decode_fr(array_sum($c)) , 1, 0, 'L');
                                $pdf->cell(22, 5, decode_fr(montant_restant_pourcent(array_sum($b), array_sum($a))).'%' , 1, 0, 'L');
                                $a = array();
                                $b = array();
                                $c = array();
                                $pdf->Ln(5);
                            }else{
                                $pdf->cell(154, 5, decode_fr("Aucun frais affecté à l'étudiant(e)") , 1, 0, 'L');$pdf->Ln(5);
                                $pdf->cell(60, 5, decode_fr("Total") , 1, 0, 'L');
                                $pdf->cell(25, 5, '$ '.decode_fr(array_sum($a)) , 1, 0, 'L');
                                $pdf->cell(25, 5, '$ '.decode_fr(array_sum($b)) , 1, 0, 'L');
                                $pdf->cell(22, 5, '$ '.decode_fr(array_sum($c)) , 1, 0, 'L');
                                $pdf->cell(22, 5, decode_fr(montant_restant_pourcent(array_sum($b), array_sum($a))).'%' , 1, 0, 'L');
                                $pdf->Ln(2);
                            }

                        }else if($_POST['type_frais'] !="Tous"){
                            $sql = "SELECT faculte, promotion, type_frais, SUM(montant) as mt FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ? AND type_frais = ? GROUP BY faculte, type_frais";
                            $sql_array = array($data_student['matricule'], $data_student['promotion'], $data_student['fac'], $data_student['annee_academique'], $_POST['type_frais']);
                                                                        
                            $all = ConnexionBdd::Connecter()->prepare($sql);
                            $all->execute($sql_array);

                            if($all->rowCount() > 0){
                                while ($data = $all->fetch()){
                                    $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ? AND type_frais = ? HAVING ROUND(mp * 100 / ?) >= ? AND ROUND(mp * 100 / ?) <= ?";
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute(array($data_student['matricule'], $data['faculte'], $data['promotion'], $data['type_frais'], $data['mt'], $_POST['pourcent_debut'], $data['mt'], $_POST['pourcent_fin'])); 
                                    $pdf->SetFont('Arial','',8);
                                    while($d = $sql_2->fetch()){
                                        $pdf->cell(60, 5, ucfirst(decode_fr($data['type_frais'])) , 1, 0, 'L');
                                        $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L');
                                        $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                        $pdf->cell(22, 5, '$ '.mm($data['mt']-$d['mp']), 1, 0, 'L');
                                        $pdf->cell(22, 5, montant_restant_pourcent($d['mp'], $data['mt']).' % ', 1, 0, 'L');
                                        // on affete les frais dans le tableau
                                        $a[] = $data['mt'];
                                        $b[] = $d['mp'];
                                        $c[] = $data['mt']-$d['mp'];
                                        $pdf->Ln(5);
                                    }
                                }
                                
                                // total de frais
                                if(count($a) >= 0 && count($b) >= 0 && count($c) >= 0){
                                    $pdf->cell(60, 5, decode_fr("Total") , 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.decode_fr(array_sum($a)) , 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.decode_fr(array_sum($b)) , 1, 0, 'L');
                                    $pdf->cell(22, 5, '$ '.decode_fr(array_sum($c)) , 1, 0, 'L');
                                    $pdf->cell(22, 5, decode_fr(montant_restant_pourcent(array_sum($b), array_sum($a))).'%' , 1, 0, 'L');
                                    $a = array();
                                    $b = array();
                                    $c = array();
                                    $pdf->Ln(5);
                                }
                            }else{
                                $pdf->cell(154, 5, decode_fr("ce type de frais nest pas affecté à l'étudiant(e)") , 1, 0, 'L');$pdf->Ln(5);
                                $pdf->cell(60, 5, decode_fr("Total") , 1, 0, 'L');
                                $pdf->cell(25, 5, '$ '.decode_fr(array_sum($a)) , 1, 0, 'L');
                                $pdf->cell(25, 5, '$ '.decode_fr(array_sum($b)) , 1, 0, 'L');
                                $pdf->cell(22, 5, '$ '.decode_fr(array_sum($c)) , 1, 0, 'L');
                                $pdf->cell(22, 5, decode_fr(montant_restant_pourcent(array_sum($b), array_sum($a))).'%' , 1, 0, 'L');
                                $pdf->Ln(2);
                            }
                        }
                        $pdf->Ln(5);
                    }
                    $pdf->Ln(5);
                }
            }else if($_POST['fac_etudiant'] = "Tous" && $_POST['promotion_etud'] !="Tous"){
                // selection des toutes les facultes
                $f = ConnexionBdd::Connecter()->query("SELECT DISTINCT fac FROM etudiants_inscrits GROUP BY fac");
                while($df = $f->fetch()){
                    $t_fac[] = $df['fac'];
                }
                // selection des toutes les promotions
                $f = ConnexionBdd::Connecter()->query("SELECT DISTINCT promotion FROM etudiants_inscrits GROUP BY promotion");
                while($df = $f->fetch()){
                    $t_promotion[] = $df['promotion'];
                }

                if(empty($_POST['pourcent_debut']) && empty($_POST['pourcent_fin'])){
                    foreach($t_fac as $ff){
                        // on commence par selectionner tous les etudiants
                        $sel_all = ConnexionBdd::Connecter()->prepare("SELECT * FROM etudiants_inscrits WHERE annee_academique = ? AND fac = ? AND promotion = ? ORDER BY fac, promotion, noms ASC");
                        $sel_all->execute(array($annee_acad_fin, $ff, $_POST['promotion_etud']));

                        while($data_student = $sel_all->fetch()){
                            $pdf->SetFont('Arial','',8);
                            $pdf->cell(60, 5, 'Matricule      : '.decode_fr($data_student['matricule']), 0, 1, 'L');
                            $pdf->cell(60, 5, 'Noms           : '.decode_fr($data_student['noms']), 0, 1, 'L');
                            $pdf->cell(60, 5, 'Faculte         : '.decode_fr($data_student['fac']), 0, 1, 'L');
                            $pdf->cell(60, 5, 'Promotion     : '.decode_fr($data_student['promotion']), 0, 1, 'L');
                            $pdf->cell(60, 5, 'Annee Acad. : '.decode_fr($data_student['annee_academique']), 0, 1, 'L');

                            // selection du montant
                            $pdf->SetFont('Arial','B',8);
                            $pdf->cell(60, 5, "Type frais" , 1, 0, 'L');
                            $pdf->cell(25, 5, "Montant prevu", 1, 0, 'L');
                            $pdf->cell(25, 5, "montant payer", 1, 0, 'L');
                            $pdf->cell(22, 5, "Solde", 1, 0, 'L');
                            $pdf->cell(22, 5, "%", 1, 0, 'L');
                            $pdf->Ln(5);
                            if($_POST['type_frais'] == "Tous"){
                                $sql = "SELECT faculte, promotion, type_frais, SUM(montant) as mt FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ? GROUP BY faculte, type_frais";
                                $sql_array = array($data_student['matricule'], $data_student['promotion'], $data_student['fac'], $data_student['annee_academique']);
                                                                            
                                $all = ConnexionBdd::Connecter()->prepare($sql);
                                $all->execute($sql_array);

                                while ($data = $all->fetch()){
                                    $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ? AND type_frais = ?  HAVING mp >= 0";
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute(array($data_student['matricule'], $data['faculte'], $data['promotion'], $data['type_frais'])); 
                                    $pdf->SetFont('Arial','',8);
                                    while($d = $sql_2->fetch()){
                                        $pdf->cell(60, 5, ucfirst(decode_fr($data['type_frais'])) , 1, 0, 'L');
                                        $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L');
                                        $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                        $pdf->cell(22, 5, '$ '.mm($data['mt']-$d['mp']), 1, 0, 'L');
                                        $pdf->cell(22, 5, montant_restant_pourcent($d['mp'], $data['mt']).' % ', 1, 0, 'L');
                                        // on affete les frais dans le tableau
                                        $a[] = $data['mt'];
                                        $b[] = $d['mp'];
                                        $c[] = $data['mt']-$d['mp'];
                                        $pdf->Ln(5);
                                    }
                                }
                                
                                // total de frais
                                if(count($a) > 0 && count($b) > 0 && count($c) > 0){
                                    $pdf->cell(60, 5, decode_fr("Total") , 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.decode_fr(array_sum($a)) , 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.decode_fr(array_sum($b)) , 1, 0, 'L');
                                    $pdf->cell(22, 5, '$ '.decode_fr(array_sum($c)) , 1, 0, 'L');
                                    $pdf->cell(22, 5, decode_fr(montant_restant_pourcent(array_sum($b), array_sum($a))).'%' , 1, 0, 'L');
                                    $a = array();
                                    $b = array();
                                    $c = array();
                                    $pdf->Ln(5);
                                }else{
                                    $pdf->cell(154, 5, decode_fr("Aucun frais affecté à l'étudiant(e)") , 1, 0, 'L');$pdf->Ln(5);
                                    $pdf->cell(60, 5, decode_fr("Total") , 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.decode_fr(array_sum($a)) , 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.decode_fr(array_sum($b)) , 1, 0, 'L');
                                    $pdf->cell(22, 5, '$ '.decode_fr(array_sum($c)) , 1, 0, 'L');
                                    $pdf->cell(22, 5, decode_fr(montant_restant_pourcent(array_sum($b), array_sum($a))).'%' , 1, 0, 'L');
                                    $pdf->Ln(2);
                                }

                            }else if($_POST['type_frais'] !="Tous"){
                                $sql = "SELECT faculte, promotion, type_frais, SUM(montant) as mt FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ? AND type_frais = ? GROUP BY faculte, type_frais";
                                $sql_array = array($data_student['matricule'], $data_student['promotion'], $data_student['fac'], $data_student['annee_academique'], $_POST['type_frais']);
                                                                            
                                $all = ConnexionBdd::Connecter()->prepare($sql);
                                $all->execute($sql_array);

                                if($all->rowCount() > 0){
                                    while ($data = $all->fetch()){
                                        $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ? AND type_frais = ?";
                                        $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                        $sql_2->execute(array($data_student['matricule'], $data['faculte'], $data['promotion'], $data['type_frais'])); 
                                        $pdf->SetFont('Arial','',8);
                                        while($d = $sql_2->fetch()){
                                            $pdf->cell(60, 5, ucfirst(decode_fr($data['type_frais'])) , 1, 0, 'L');
                                            $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L');
                                            $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                            $pdf->cell(22, 5, '$ '.mm($data['mt']-$d['mp']), 1, 0, 'L');
                                            $pdf->cell(22, 5, montant_restant_pourcent($d['mp'], $data['mt']).' % ', 1, 0, 'L');
                                            // on affete les frais dans le tableau
                                            $a[] = $data['mt'];
                                            $b[] = $d['mp'];
                                            $c[] = $data['mt']-$d['mp'];
                                            $pdf->Ln(5);
                                        }
                                    }
                                    
                                    // total de frais
                                    if(count($a) >= 0 && count($b) >= 0 && count($c) >= 0){
                                        $pdf->cell(60, 5, decode_fr("Total") , 1, 0, 'L');
                                        $pdf->cell(25, 5, '$ '.decode_fr(array_sum($a)) , 1, 0, 'L');
                                        $pdf->cell(25, 5, '$ '.decode_fr(array_sum($b)) , 1, 0, 'L');
                                        $pdf->cell(22, 5, '$ '.decode_fr(array_sum($c)) , 1, 0, 'L');
                                        $pdf->cell(22, 5, decode_fr(montant_restant_pourcent(array_sum($b), array_sum($a))).'%' , 1, 0, 'L');
                                        $a = array();
                                        $b = array();
                                        $c = array();
                                        $pdf->Ln(5);
                                    }else{
                                        $pdf->cell(154, 5, decode_fr("Aucun frais affecté à l'étudiant(e)") , 1, 0, 'L');$pdf->Ln(5);
                                        $pdf->cell(60, 5, decode_fr("Total") , 1, 0, 'L');
                                        $pdf->cell(25, 5, '$ '.decode_fr(array_sum($a)) , 1, 0, 'L');
                                        $pdf->cell(25, 5, '$ '.decode_fr(array_sum($b)) , 1, 0, 'L');
                                        $pdf->cell(22, 5, '$ '.decode_fr(array_sum($c)) , 1, 0, 'L');
                                        $pdf->cell(22, 5, decode_fr(montant_restant_pourcent(array_sum($b), array_sum($a))).'%' , 1, 0, 'L');
                                        $pdf->Ln(2);
                                    }
                                }else{
                                    $pdf->cell(154, 5, decode_fr("ce type de frais nest pas affecté à l'étudiant(e)") , 1, 0, 'L');$pdf->Ln(5);
                                    $pdf->cell(60, 5, decode_fr("Total") , 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.decode_fr(array_sum($a)) , 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.decode_fr(array_sum($b)) , 1, 0, 'L');
                                    $pdf->cell(22, 5, '$ '.decode_fr(array_sum($c)) , 1, 0, 'L');
                                    $pdf->cell(22, 5, decode_fr(montant_restant_pourcent(array_sum($b), array_sum($a))).'%' , 1, 0, 'L');
                                    $pdf->Ln(2);
                                }
                            }
                            $pdf->Ln(5);
                        }
                    }
                    $pdf->Ln(5);
                }else if(!empty($_POST['pourcent_debut']) && !empty($_POST['pourcent_fin'])){
                    foreach($t_fac as $ff){
                        // on commence par selectionner tous les etudiants
                        $sel_all = ConnexionBdd::Connecter()->prepare("SELECT * FROM etudiants_inscrits WHERE annee_academique = ? AND fac = ? AND promotion  = ? ORDER BY fac, promotion, noms ASC");
                        $sel_all->execute(array($annee_acad_fin, $ff, $_POST['promotion_etud']));
                        while($data_student = $sel_all->fetch()){
                            $pdf->SetFont('Arial','',8);
                            $pdf->cell(60, 5, 'Matricule      : '.decode_fr($data_student['matricule']), 0, 1, 'L');
                            $pdf->cell(60, 5, 'Noms           : '.decode_fr($data_student['noms']), 0, 1, 'L');
                            $pdf->cell(60, 5, 'Faculte         : '.decode_fr($data_student['fac']), 0, 1, 'L');
                            $pdf->cell(60, 5, 'Promotion     : '.decode_fr($data_student['promotion']), 0, 1, 'L');
                            $pdf->cell(60, 5, 'Annee Acad. : '.decode_fr($data_student['annee_academique']), 0, 1, 'L');

                            // selection du montant
                            $pdf->SetFont('Arial','B',8);
                            $pdf->cell(60, 5, "Type frais" , 1, 0, 'L');
                            $pdf->cell(25, 5, "Montant prevu", 1, 0, 'L');
                            $pdf->cell(25, 5, "montant payer", 1, 0, 'L');
                            $pdf->cell(22, 5, "Solde", 1, 0, 'L');
                            $pdf->cell(22, 5, "%", 1, 0, 'L');
                            $pdf->Ln(5);
                            if($_POST['type_frais'] == "Tous"){
                                $sql = "SELECT faculte, promotion, type_frais, SUM(montant) as mt FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ? GROUP BY faculte, type_frais";
                                $sql_array = array($data_student['matricule'], $data_student['promotion'], $data_student['fac'], $data_student['annee_academique']);
                                                                            
                                $all = ConnexionBdd::Connecter()->prepare($sql);
                                $all->execute($sql_array);

                                while ($data = $all->fetch()){
                                    $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ? AND type_frais = ?  HAVING ROUND(mp * 100 / ?) >= ? AND ROUND(mp * 100 / ?) <= ?";
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute(array($data_student['matricule'], $data['faculte'], $data['promotion'], $data['type_frais'], $data['mt'], $_POST['pourcent_debut'], $data['mt'], $_POST['pourcent_fin'])); 
                                    $pdf->SetFont('Arial','',8);
                                    while($d = $sql_2->fetch()){
                                        $pdf->cell(60, 5, ucfirst(decode_fr($data['type_frais'])) , 1, 0, 'L');
                                        $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L');
                                        $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                        $pdf->cell(22, 5, '$ '.mm($data['mt']-$d['mp']), 1, 0, 'L');
                                        $pdf->cell(22, 5, montant_restant_pourcent($d['mp'], $data['mt']).' % ', 1, 0, 'L');
                                        // on affete les frais dans le tableau
                                        $a[] = $data['mt'];
                                        $b[] = $d['mp'];
                                        $c[] = $data['mt']-$d['mp'];
                                        $pdf->Ln(5);
                                    }
                                }
                                
                                // total de frais
                                if(count($a) > 0 && count($b) > 0 && count($c) > 0){
                                    $pdf->cell(60, 5, decode_fr("Total") , 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.decode_fr(array_sum($a)) , 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.decode_fr(array_sum($b)) , 1, 0, 'L');
                                    $pdf->cell(22, 5, '$ '.decode_fr(array_sum($c)) , 1, 0, 'L');
                                    $pdf->cell(22, 5, decode_fr(montant_restant_pourcent(array_sum($b), array_sum($a))).'%' , 1, 0, 'L');
                                    $a = array();
                                    $b = array();
                                    $c = array();
                                    $pdf->Ln(5);
                                }else{
                                    $pdf->cell(154, 5, decode_fr("Aucun frais affecté à l'étudiant(e)") , 1, 0, 'L');$pdf->Ln(5);
                                    $pdf->cell(60, 5, decode_fr("Total") , 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.decode_fr(array_sum($a)) , 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.decode_fr(array_sum($b)) , 1, 0, 'L');
                                    $pdf->cell(22, 5, '$ '.decode_fr(array_sum($c)) , 1, 0, 'L');
                                    $pdf->cell(22, 5, decode_fr(montant_restant_pourcent(array_sum($b), array_sum($a))).'%' , 1, 0, 'L');
                                    $pdf->Ln(2);
                                }

                            }else if($_POST['type_frais'] !="Tous"){
                                $sql = "SELECT faculte, promotion, type_frais, SUM(montant) as mt FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ? AND type_frais = ? GROUP BY faculte, type_frais";
                                $sql_array = array($data_student['matricule'], $data_student['promotion'], $data_student['fac'], $data_student['annee_academique'], $_POST['type_frais']);
                                                                            
                                $all = ConnexionBdd::Connecter()->prepare($sql);
                                $all->execute($sql_array);

                                if($all->rowCount() > 0){
                                    while ($data = $all->fetch()){
                                        $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ? AND type_frais = ? HAVING ROUND(mp * 100 / ?) >= ? AND ROUND(mp * 100 / ?) <= ?";
                                        $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                        $sql_2->execute(array($data_student['matricule'], $data['faculte'], $data['promotion'], $data['type_frais'], $data['mt'], $_POST['pourcent_debut'], $data['mt'], $_POST['pourcent_fin'])); 
                                        $pdf->SetFont('Arial','',8);
                                        while($d = $sql_2->fetch()){
                                            $pdf->cell(60, 5, ucfirst(decode_fr($data['type_frais'])) , 1, 0, 'L');
                                            $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L');
                                            $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                            $pdf->cell(22, 5, '$ '.mm($data['mt']-$d['mp']), 1, 0, 'L');
                                            $pdf->cell(22, 5, montant_restant_pourcent($d['mp'], $data['mt']).' % ', 1, 0, 'L');
                                            // on affete les frais dans le tableau
                                            $a[] = $data['mt'];
                                            $b[] = $d['mp'];
                                            $c[] = $data['mt']-$d['mp'];
                                            $pdf->Ln(5);
                                        }
                                    }
                                    
                                    // total de frais
                                    if(count($a) >= 0 && count($b) >= 0 && count($c) >= 0){
                                        $pdf->cell(60, 5, decode_fr("Total") , 1, 0, 'L');
                                        $pdf->cell(25, 5, '$ '.decode_fr(array_sum($a)) , 1, 0, 'L');
                                        $pdf->cell(25, 5, '$ '.decode_fr(array_sum($b)) , 1, 0, 'L');
                                        $pdf->cell(22, 5, '$ '.decode_fr(array_sum($c)) , 1, 0, 'L');
                                        $pdf->cell(22, 5, decode_fr(montant_restant_pourcent(array_sum($b), array_sum($a))).'%' , 1, 0, 'L');
                                        $a = array();
                                        $b = array();
                                        $c = array();
                                        $pdf->Ln(5);
                                    }
                                }else{
                                    $pdf->cell(154, 5, decode_fr("ce type de frais nest pas affecté à l'étudiant(e)") , 1, 0, 'L');$pdf->Ln(5);
                                    $pdf->cell(60, 5, decode_fr("Total") , 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.decode_fr(array_sum($a)) , 1, 0, 'L');
                                    $pdf->cell(25, 5, '$ '.decode_fr(array_sum($b)) , 1, 0, 'L');
                                    $pdf->cell(22, 5, '$ '.decode_fr(array_sum($c)) , 1, 0, 'L');
                                    $pdf->cell(22, 5, decode_fr(montant_restant_pourcent(array_sum($b), array_sum($a))).'%' , 1, 0, 'L');
                                    $pdf->Ln(2);
                                }
                            }
                            $pdf->Ln(5);
                        }
                    }
                    $pdf->Ln(5);
                }
            }
        }
        $pdf->Ln(15);
        $pdf->SetFont('Arial','',10);
	    $pdf->cell(300,20, decode_fr('par : '.$_SESSION['data']['noms'].'; le '.date('d/M/Y')),0,1,'C');
        $pdf->Output();
    }

    // synthese par promotion
    if(isset($_POST['btn_promotion'])){
        $pdf = new FPDF('P', 'mm', 'A4');
        $annee_acad_deb = $_POST['annee_acad_deb'];
        $annee_acad_fin = $_POST['annee_acad_fin'];
        $pd = floatval($_POST['pourcent_debut']);
        $pf = floatval($_POST['pourcent_fin']);

        all($pdf);
        $pdf->SetFont('Arial','BU',10);
        $pdf->cell(190, 10, decode_fr('SYNTHSE DES FRAIS PAR PROMOTION'), 0, 1, 'C');
        $pdf->SetFont('Arial','',10);

        all_trans($pdf);

        $pdf->Output();
    }

    // filtre par faculte
    if(isset($_POST['btn_fac'])){
        $pdf = new FPDF('P', 'mm', 'A4');
        $annee_acad_deb = $_POST['annee_acad_deb'];
        $annee_acad_fin = $_POST['annee_acad_fin'];
        $pd = floatval($_POST['pourcent_debut']);
        $pf = floatval($_POST['pourcent_fin']);

        all($pdf);
        $pdf->SetFont('Arial','BU',10);
        $pdf->cell(190, 10, decode_fr('SYNTHESE DES FRAIS PAR SECTION'), 0, 1, 'C');
        $pdf->SetFont('Arial','',10);

        $annee_acad_deb = $_POST['annee_acad_deb'];
        $annee_acad_fin = $_POST['annee_acad_fin'];
        $pd = floatval($_POST['pourcent_debut']);
        $pf = floatval($_POST['pourcent_fin']);
        if($annee_acad_deb == $annee_acad_fin){
            $t_fac = array();
            $t_promotion = array();
            $ff = $_POST['fac_etudiant'];
            $pp = $_POST['promotion_etud'];

            // selection des toutes les facultes
            $f = ConnexionBdd::Connecter()->query("SELECT etudiants_inscrits.id_section, sections.section, annee_acad.id_annee FROM etudiants_inscrits LEFT JOIN sections ON etudiants_inscrits.id_section = sections.id_section LEFT JOIN annee_acad ON etudiants_inscrits.id_annee = annee_acad.id_annee WHERE etudiants_inscrits.id_annee = {$annee_acad_fin}");
            while($df = $f->fetch()){
                $t_fac[] = $df['section'];
            }

            if($_POST['fac_etudiant'] == "Tous" && $_POST['promotion_etud'] == "Tous"){
                if(empty($_POST['pourcent_debut']) && empty($_POST['pourcent_fin'])){
                    // la tables etudiants
                    $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT id_annee FROM etudiants_inscrits WHERE id_annee = ? GROUP BY id_annee");
                    $sel_etud->execute(array($annee_acad_fin));
    
                    if($sel_etud->rowCount() > 0){
                        while ($data_student = $sel_etud->fetch()){
                            $pdf->cell(190, 5, decode_fr('Annee Acad.: '.get_annee($annee_acad_fin)), 0, 1, 'L');
                            $pdf->cell(190, 5, decode_fr('Faculte : '.$ff), 0, 1, 'L');
                            $pdf->cell(190, 5, decode_fr('Promotion : '.$pp), 0, 1, 'L');
                            $pdf->Ln(2);
                            $pdf->cell(60, 5, 'Type frais', 1, 0, 'L');
                            $pdf->cell(30, 5, 'Montant prevu', 1, 0, 'L');
                            $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                            $pdf->cell(30, 5, 'Solde', 1, 0, 'L');
                            $pdf->cell(15, 5, ' % ', 1, 0, 'L');
                            $pdf->Ln(1);
    
                            // tableau
                            $a = array();
                            $b = array();
                            if($_POST['type_frais'] != "Tous"){
                                    $sql_2 = "SELECT
                                            payement.id_payement,
                                            SUM(payement.montant) as mp,
                                            prevision_frais.id_frais,
                                            SUM(prevision_frais.montant) as mt,
                                            prevision_frais.type_frais,
                                            annee_acad.annee_acad
                                        FROM
                                            payement
                                        LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                        LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                        WHERE
                                            prevision_frais.type_frais = ? AND annee_acad.id_annee = ?";
                                    $sql_2_a = array($_POST['type_frais'], $annee_acad_deb);
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute($sql_2_a);
    
                                    while($data = $sql_2->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.$data['mt'], 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($data['mt'] - $data['mp']), 1, 0, 'L');
                                        $pdf->cell(15, 5, montant_restant_pourcent($data['mp'], $data['mt']).' % ', 1, 0, 'L');
                                        $pdf->Ln(1);
                                        $a[] = $data['mt'];
                                        $b[] = $data['mp'];
                                    }
                                // }
    
                                $pdf->Ln(4);
                                $pdf->SetFont('Arial','',9);
                                $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                                $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b) , array_sum($a)).'%', 1, 0, 'L');
                                $pdf->Ln(8);
                                $a = array();
                                $b = array();
                                // le type de frais n est pas selectionner
                            }else{
                                $sql_2 = "SELECT
                                            payement.id_payement,
                                            SUM(payement.montant) as mp,
                                            prevision_frais.id_frais,
                                            SUM(prevision_frais.montant) as mt,
                                            prevision_frais.type_frais,
                                            annee_acad.annee_acad
                                        FROM
                                            payement
                                        LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                        LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                        WHERE
                                            annee_acad.id_annee = ?";
                                    $sql_2_a = array($annee_acad_deb);
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute($sql_2_a);
    
                                    while($data = $sql_2->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.$data['mt'], 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($data['mt'] - $data['mp']), 1, 0, 'L');
                                        $pdf->cell(15, 5, montant_restant_pourcent($data['mp'], $data['mt']).' % ', 1, 0, 'L');
                                        $pdf->Ln(1);
                                        $a[] = $data['mt'];
                                        $b[] = $data['mp'];
                                    }
                                $pdf->Ln(4);
                                $pdf->SetFont('Arial','',9);
                                $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                                $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b) , array_sum($a)).'%', 1, 0, 'L');
                                $pdf->Ln(9);
                                $a = array();
                                $b = array();
                            }
                        }
                    }
                }else if(!empty($_POST['pourcent_debut']) && !empty($_POST['pourcent_fin'])){
                    $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT id_annee FROM etudiants_inscrits WHERE id_annee = ? GROUP BY id_annee");
                    $sel_etud->execute(array($annee_acad_fin));
    
                    if($sel_etud->rowCount() > 0){
                        while ($data_student = $sel_etud->fetch()){
                            $pdf->cell(190, 5, decode_fr('Annee Acad.: '.get_annee($annee_acad_fin)), 0, 1, 'L');
                            $pdf->cell(190, 5, decode_fr('Faculte : '.$ff), 0, 1, 'L');
                            $pdf->cell(190, 5, decode_fr('Promotion : '.$pp), 0, 1, 'L');
                            $pdf->Ln(2);
                            $pdf->cell(60, 5, 'Type frais', 1, 0, 'L');
                            $pdf->cell(30, 5, 'Montant prevu', 1, 0, 'L');
                            $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                            $pdf->cell(30, 5, 'Solde', 1, 0, 'L');
                            $pdf->cell(15, 5, ' % ', 1, 0, 'L');
                            $pdf->Ln(1);
    
                            // tableau
                            $a = array();
                            $b = array();
                            if($_POST['type_frais'] != "Tous"){
                                    $sql_2 = "SELECT
                                            payement.id_payement,
                                            SUM(payement.montant) as mp,
                                            prevision_frais.id_frais,
                                            SUM(prevision_frais.montant) as mt,
                                            prevision_frais.type_frais,
                                            annee_acad.annee_acad
                                        FROM
                                            payement
                                        LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                        LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                        WHERE
                                            prevision_frais.type_frais = ? AND annee_acad.id_annee = ?
                                        HAVING ROUND(mp * 100 / ?) >= ? AND ROUND(mp * 100 / ?) <= ?";
                                    $sql_2_a = array($_POST['type_frais'], $annee_acad_deb, $pd, $pf);
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute($sql_2_a);
    
                                    while($data = $sql_2->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.$data['mt'], 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($data['mt'] - $data['mp']), 1, 0, 'L');
                                        $pdf->cell(15, 5, montant_restant_pourcent($data['mp'], $data['mt']).' % ', 1, 0, 'L');
                                        $pdf->Ln(1);
                                        $a[] = $data['mt'];
                                        $b[] = $data['mp'];
                                    }
                                // }
    
                                $pdf->Ln(4);
                                $pdf->SetFont('Arial','',9);
                                $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                                $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b) , array_sum($a)).'%', 1, 0, 'L');
                                $pdf->Ln(8);
                                $a = array();
                                $b = array();
                                // le type de frais n est pas selectionner
                            }else{
                                $sql_2 = "SELECT
                                            payement.id_payement,
                                            SUM(payement.montant) as mp,
                                            prevision_frais.id_frais,
                                            SUM(prevision_frais.montant) as mt,
                                            prevision_frais.type_frais,
                                            annee_acad.annee_acad
                                        FROM
                                            payement
                                        LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                        LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                        WHERE
                                            annee_acad.id_annee = ?
                                        HAVING ROUND(mp * 100 / ?) >= ? AND ROUND(mp * 100 / ?) <= ?";
                                    $sql_2_a = array($annee_acad_deb, $pd, $pf);
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute($sql_2_a);
    
                                    while($data = $sql_2->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.$data['mt'], 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($data['mt'] - $data['mp']), 1, 0, 'L');
                                        $pdf->cell(15, 5, montant_restant_pourcent($data['mp'], $data['mt']).' % ', 1, 0, 'L');
                                        $pdf->Ln(1);
                                        $a[] = $data['mt'];
                                        $b[] = $data['mp'];
                                    }
                                $pdf->Ln(4);
                                $pdf->SetFont('Arial','',9);
                                $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                                $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b) , array_sum($a)).'%', 1, 0, 'L');
                                $pdf->Ln(9);
                                $a = array();
                                $b = array();
                            }
                        }
                    }
                }
            }else if($_POST['fac_etudiant'] != "Tous" && $_POST['promotion_etud'] == "Tous"){
                $t_promotion = array();
                $f = ConnexionBdd::Connecter()->query("SELECT DISTINCT etudiants_inscrits.promotion, options.id_option, sections.section FROM etudiants_inscrits LEFT JOIN options ON etudiants_inscrits.id_option = options.id_option LEFT JOIN sections ON etudiants_inscrits.id_section = sections.id_section WHERE sections.section = '{$_POST['fac_etudiant']}' GROUP BY etudiants_inscrits.promotion");
                while($df = $f->fetch()){
                    $t_promotion[] = $df['promotion'];
                }
                foreach($t_promotion as $pp){
                    if(empty($_POST['pourcent_debut']) && empty($_POST['pourcent_fin'])){
                        // la tables etudiants
                        $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT id_annee FROM etudiants_inscrits WHERE id_annee = ? GROUP BY id_annee");
                        $sel_etud->execute(array($annee_acad_fin));
        
                        if($sel_etud->rowCount() > 0){
                            while ($data_student = $sel_etud->fetch()){
                                $pdf->cell(190, 5, decode_fr('Annee Acad.: '.get_annee($annee_acad_fin)), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Faculte : '.$ff), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Promotion : '.$pp), 0, 1, 'L');
                                $pdf->Ln(2);
                                $pdf->cell(60, 5, 'Type frais', 1, 0, 'L');
                                $pdf->cell(30, 5, 'Montant prevu', 1, 0, 'L');
                                $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                                $pdf->cell(30, 5, 'Solde', 1, 0, 'L');
                                $pdf->cell(15, 5, ' % ', 1, 0, 'L');
                                $pdf->Ln(1);
        
                                // tableau
                                $a = array();
                                $b = array();
                                if($_POST['type_frais'] != "Tous"){
                                        $sql_2 = "SELECT
                                                payement.id_payement,
                                                SUM(payement.montant) AS mp,
                                                prevision_frais.id_frais,
                                                SUM(prevision_frais.montant) AS mt,
                                                prevision_frais.type_frais,
                                                annee_acad.annee_acad,
                                                sections.id_section,
                                                options.id_option, options.promotion
                                            FROM
                                                payement
                                            LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                            LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                            LEFT JOIN sections ON payement.id_section = sections.id_section
                                            LEFT JOIN options ON payement.id_option = options.id_option
                                            WHERE
                                                prevision_frais.type_frais = ? AND annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?";
                                        $sql_2_a = array($_POST['type_frais'], $annee_acad_deb, $ff, $pp);
                                        $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                        $sql_2->execute($sql_2_a);
        
                                        while($data = $sql_2->fetch()){
                                            $pdf->SetFont('Arial','i',8);
                                            $pdf->Ln(4);
                                            $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.$data['mt'], 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($data['mt'] - $data['mp']), 1, 0, 'L');
                                            $pdf->cell(15, 5, montant_restant_pourcent($data['mp'], $data['mt']).' % ', 1, 0, 'L');
                                            $pdf->Ln(1);
                                            $a[] = $data['mt'];
                                            $b[] = $data['mp'];
                                        }
                                    // }
        
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                                    $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b) , array_sum($a)).'%', 1, 0, 'L');
                                    $pdf->Ln(8);
                                    $a = array();
                                    $b = array();
                                    // le type de frais n est pas selectionner
                                }else{
                                    $sql_2 = "SELECT
                                                payement.id_payement,
                                                SUM(payement.montant) AS mp,
                                                prevision_frais.id_frais,
                                                SUM(prevision_frais.montant) AS mt,
                                                prevision_frais.type_frais,
                                                annee_acad.annee_acad,
                                                sections.id_section,
                                                options.id_option, options.promotion
                                            FROM
                                                payement
                                            LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                            LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                            LEFT JOIN sections ON payement.id_section = sections.id_section
                                            LEFT JOIN options ON payement.id_option = options.id_option
                                            WHERE
                                                annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?";
                                        $sql_2_a = array($annee_acad_deb, $ff, $pp);
                                        $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                        $sql_2->execute($sql_2_a);
        
                                        while($data = $sql_2->fetch()){
                                            $pdf->SetFont('Arial','i',8);
                                            $pdf->Ln(4);
                                            $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.$data['mt'], 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($data['mt'] - $data['mp']), 1, 0, 'L');
                                            $pdf->cell(15, 5, montant_restant_pourcent($data['mp'], $data['mt']).' % ', 1, 0, 'L');
                                            $pdf->Ln(1);
                                            $a[] = $data['mt'];
                                            $b[] = $data['mp'];
                                        }
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                                    $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b) , array_sum($a)).'%', 1, 0, 'L');
                                    $pdf->Ln(9);
                                    $a = array();
                                    $b = array();
                                }
                            }
                        }
                    }else if(!empty($_POST['pourcent_debut']) && !empty($_POST['pourcent_fin'])){
                        // la tables etudiants
                        $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT id_annee FROM etudiants_inscrits WHERE id_annee = ? GROUP BY id_annee");
                        $sel_etud->execute(array($annee_acad_fin));
        
                        if($sel_etud->rowCount() > 0){
                            while ($data_student = $sel_etud->fetch()){
                                $pdf->cell(190, 5, decode_fr('Annee Acad.: '.get_annee($annee_acad_fin)), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Faculte : '.$ff), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Promotion : '.$pp), 0, 1, 'L');
                                $pdf->Ln(2);
                                $pdf->cell(60, 5, 'Type frais', 1, 0, 'L');
                                $pdf->cell(30, 5, 'Montant prevu', 1, 0, 'L');
                                $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                                $pdf->cell(30, 5, 'Solde', 1, 0, 'L');
                                $pdf->cell(15, 5, ' % ', 1, 0, 'L');
                                $pdf->Ln(1);
        
                                // tableau
                                $a = array();
                                $b = array();
                                if($_POST['type_frais'] != "Tous"){
                                        $sql_2 = "SELECT
                                                payement.id_payement,
                                                SUM(payement.montant) AS mp,
                                                prevision_frais.id_frais,
                                                SUM(prevision_frais.montant) AS mt,
                                                prevision_frais.type_frais,
                                                annee_acad.annee_acad,
                                                sections.id_section,
                                                options.id_option, options.promotion
                                            FROM
                                                payement
                                            LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                            LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                            LEFT JOIN sections ON payement.id_section = sections.id_section
                                            LEFT JOIN options ON payement.id_option = options.id_option
                                            WHERE
                                                prevision_frais.type_frais = ? AND annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?
                                            HAVING ROUND(mp * 100 / ?) >= ? AND ROUND(mp * 100 / ?) <= ?";
                                        $sql_2_a = array($_POST['type_frais'], $annee_acad_deb, $ff, $pp, $pd, $pf);
                                        $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                        $sql_2->execute($sql_2_a);
        
                                        while($data = $sql_2->fetch()){
                                            $pdf->SetFont('Arial','i',8);
                                            $pdf->Ln(4);
                                            $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.$data['mt'], 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($data['mt'] - $data['mp']), 1, 0, 'L');
                                            $pdf->cell(15, 5, montant_restant_pourcent($data['mp'], $data['mt']).' % ', 1, 0, 'L');
                                            $pdf->Ln(1);
                                            $a[] = $data['mt'];
                                            $b[] = $data['mp'];
                                        }
                                    // }
        
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                                    $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b) , array_sum($a)).'%', 1, 0, 'L');
                                    $pdf->Ln(8);
                                    $a = array();
                                    $b = array();
                                    // le type de frais n est pas selectionner
                                }else{
                                    $sql_2 = "SELECT
                                                payement.id_payement,
                                                SUM(payement.montant) AS mp,
                                                prevision_frais.id_frais,
                                                SUM(prevision_frais.montant) AS mt,
                                                prevision_frais.type_frais,
                                                annee_acad.annee_acad,
                                                sections.id_section,
                                                options.id_option, options.promotion
                                            FROM
                                                payement
                                            LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                            LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                            LEFT JOIN sections ON payement.id_section = sections.id_section
                                            LEFT JOIN options ON payement.id_option = options.id_option
                                            WHERE
                                                annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?
                                            HAVING ROUND(mp * 100 / ?) >= ? AND ROUND(mp * 100 / ?) <= ?";
                                        $sql_2_a = array($annee_acad_deb, $ff, $pp, $pd, $pf);
                                        $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                        $sql_2->execute($sql_2_a);
        
                                        while($data = $sql_2->fetch()){
                                            $pdf->SetFont('Arial','i',8);
                                            $pdf->Ln(4);
                                            $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.$data['mt'], 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($data['mt'] - $data['mp']), 1, 0, 'L');
                                            $pdf->cell(15, 5, montant_restant_pourcent($data['mp'], $data['mt']).' % ', 1, 0, 'L');
                                            $pdf->Ln(1);
                                            $a[] = $data['mt'];
                                            $b[] = $data['mp'];
                                        }
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                                    $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b) , array_sum($a)).'%', 1, 0, 'L');
                                    $pdf->Ln(9);
                                    $a = array();
                                    $b = array();
                                }
                            }
                        }
                    }
                }
            }else if($_POST['fac_etudiant'] != "Tous" && $_POST['promotion_etud'] != "Tous"){
                if(empty($_POST['pourcent_debut']) && empty($_POST['pourcent_fin'])){
                    // la tables etudiants
                    $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT id_annee FROM etudiants_inscrits WHERE id_annee = ? GROUP BY id_annee");
                    $sel_etud->execute(array($annee_acad_fin));
    
                    if($sel_etud->rowCount() > 0){
                        while ($data_student = $sel_etud->fetch()){
                            $pdf->cell(190, 5, decode_fr('Annee Acad.: '.get_annee($annee_acad_fin)), 0, 1, 'L');
                            $pdf->cell(190, 5, decode_fr('Faculte : '.$ff), 0, 1, 'L');
                            $pdf->cell(190, 5, decode_fr('Promotion : '.$pp), 0, 1, 'L');
                            $pdf->Ln(2);
                            $pdf->cell(60, 5, 'Type frais', 1, 0, 'L');
                            $pdf->cell(30, 5, 'Montant prevu', 1, 0, 'L');
                            $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                            $pdf->cell(30, 5, 'Solde', 1, 0, 'L');
                            $pdf->cell(15, 5, ' % ', 1, 0, 'L');
                            $pdf->Ln(1);
    
                            // tableau
                            $a = array();
                            $b = array();
                            if($_POST['type_frais'] != "Tous"){
                                    $sql_2 = "SELECT
                                            payement.id_payement,
                                            SUM(payement.montant) AS mp,
                                            prevision_frais.id_frais,
                                            SUM(prevision_frais.montant) AS mt,
                                            prevision_frais.type_frais,
                                            annee_acad.annee_acad,
                                            sections.id_section,
                                            options.id_option, options.promotion
                                        FROM
                                            payement
                                        LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                        LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                        LEFT JOIN sections ON payement.id_section = sections.id_section
                                        LEFT JOIN options ON payement.id_option = options.id_option
                                        WHERE
                                            prevision_frais.type_frais = ? AND annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?";
                                    $sql_2_a = array($_POST['type_frais'], $annee_acad_deb, $ff, $pp);
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute($sql_2_a);
    
                                    while($data = $sql_2->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.$data['mt'], 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($data['mt'] - $data['mp']), 1, 0, 'L');
                                        $pdf->cell(15, 5, montant_restant_pourcent($data['mp'], $data['mt']).' % ', 1, 0, 'L');
                                        $pdf->Ln(1);
                                        $a[] = $data['mt'];
                                        $b[] = $data['mp'];
                                    }
                                // }
    
                                $pdf->Ln(4);
                                $pdf->SetFont('Arial','',9);
                                $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                                $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b) , array_sum($a)).'%', 1, 0, 'L');
                                $pdf->Ln(8);
                                $a = array();
                                $b = array();
                                // le type de frais n est pas selectionner
                            }else{
                                $sql_2 = "SELECT
                                            payement.id_payement,
                                            SUM(payement.montant) AS mp,
                                            prevision_frais.id_frais,
                                            SUM(prevision_frais.montant) AS mt,
                                            prevision_frais.type_frais,
                                            annee_acad.annee_acad,
                                            sections.id_section,
                                            options.id_option, options.promotion
                                        FROM
                                            payement
                                        LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                        LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                        LEFT JOIN sections ON payement.id_section = sections.id_section
                                        LEFT JOIN options ON payement.id_option = options.id_option
                                        WHERE
                                            annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?";
                                    $sql_2_a = array($annee_acad_deb, $ff, $pp);
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute($sql_2_a);
    
                                    while($data = $sql_2->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.$data['mt'], 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($data['mt'] - $data['mp']), 1, 0, 'L');
                                        $pdf->cell(15, 5, montant_restant_pourcent($data['mp'], $data['mt']).' % ', 1, 0, 'L');
                                        $pdf->Ln(1);
                                        $a[] = $data['mt'];
                                        $b[] = $data['mp'];
                                    }
                                $pdf->Ln(4);
                                $pdf->SetFont('Arial','',9);
                                $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                                $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b) , array_sum($a)).'%', 1, 0, 'L');
                                $pdf->Ln(9);
                                $a = array();
                                $b = array();
                            }
                        }
                    }
                }else if(!empty($_POST['pourcent_debut']) && !empty($_POST['pourcent_fin'])){
                    // la tables etudiants
                    $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT id_annee FROM etudiants_inscrits WHERE id_annee = ? GROUP BY id_annee");
                    $sel_etud->execute(array($annee_acad_fin));
    
                    if($sel_etud->rowCount() > 0){
                        while ($data_student = $sel_etud->fetch()){
                            $pdf->cell(190, 5, decode_fr('Annee Acad.: '.get_annee($annee_acad_fin)), 0, 1, 'L');
                            $pdf->cell(190, 5, decode_fr('Faculte : '.$ff), 0, 1, 'L');
                            $pdf->cell(190, 5, decode_fr('Promotion : '.$pp), 0, 1, 'L');
                            $pdf->Ln(2);
                            $pdf->cell(60, 5, 'Type frais', 1, 0, 'L');
                            $pdf->cell(30, 5, 'Montant prevu', 1, 0, 'L');
                            $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                            $pdf->cell(30, 5, 'Solde', 1, 0, 'L');
                            $pdf->cell(15, 5, ' % ', 1, 0, 'L');
                            $pdf->Ln(1);
    
                            // tableau
                            $a = array();
                            $b = array();
                            if($_POST['type_frais'] != "Tous"){
                                    $sql_2 = "SELECT
                                            payement.id_payement,
                                            SUM(payement.montant) AS mp,
                                            prevision_frais.id_frais,
                                            SUM(prevision_frais.montant) AS mt,
                                            prevision_frais.type_frais,
                                            annee_acad.annee_acad,
                                            sections.id_section,
                                            options.id_option, options.promotion
                                        FROM
                                            payement
                                        LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                        LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                        LEFT JOIN sections ON payement.id_section = sections.id_section
                                        LEFT JOIN options ON payement.id_option = options.id_option
                                        WHERE
                                            prevision_frais.type_frais = ? AND annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?
                                        HAVING ROUND(mp * 100 / ?) >= ? AND ROUND(mp * 100 / ?) <= ?";
                                    $sql_2_a = array($_POST['type_frais'], $annee_acad_deb, $ff, $pp, $pd, $pf);
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute($sql_2_a);
    
                                    while($data = $sql_2->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.$data['mt'], 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($data['mt'] - $data['mp']), 1, 0, 'L');
                                        $pdf->cell(15, 5, montant_restant_pourcent($data['mp'], $data['mt']).' % ', 1, 0, 'L');
                                        $pdf->Ln(1);
                                        $a[] = $data['mt'];
                                        $b[] = $data['mp'];
                                    }
                                // }
    
                                $pdf->Ln(4);
                                $pdf->SetFont('Arial','',9);
                                $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                                $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b) , array_sum($a)).'%', 1, 0, 'L');
                                $pdf->Ln(8);
                                $a = array();
                                $b = array();
                                // le type de frais n est pas selectionner
                            }else{
                                $sql_2 = "SELECT
                                            payement.id_payement,
                                            SUM(payement.montant) AS mp,
                                            prevision_frais.id_frais,
                                            SUM(prevision_frais.montant) AS mt,
                                            prevision_frais.type_frais,
                                            annee_acad.annee_acad,
                                            sections.id_section,
                                            options.id_option, options.promotion
                                        FROM
                                            payement
                                        LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                        LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                        LEFT JOIN sections ON payement.id_section = sections.id_section
                                        LEFT JOIN options ON payement.id_option = options.id_option
                                        WHERE
                                            annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?
                                        HAVING ROUND(mp * 100 / ?) >= ? AND ROUND(mp * 100 / ?) <= ?";
                                    $sql_2_a = array($annee_acad_deb, $ff, $pp, $pd, $pf);
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute($sql_2_a);
    
                                    while($data = $sql_2->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.$data['mt'], 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($data['mt'] - $data['mp']), 1, 0, 'L');
                                        $pdf->cell(15, 5, montant_restant_pourcent($data['mp'], $data['mt']).' % ', 1, 0, 'L');
                                        $pdf->Ln(1);
                                        $a[] = $data['mt'];
                                        $b[] = $data['mp'];
                                    }
                                $pdf->Ln(4);
                                $pdf->SetFont('Arial','',9);
                                $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                                $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b) , array_sum($a)).'%', 1, 0, 'L');
                                $pdf->Ln(9);
                                $a = array();
                                $b = array();
                            }
                        }
                    }
                }
            }else if($_POST['fac_etudiant'] = "Tous" && $_POST['promotion_etud'] !="Tous"){
                $t_fac = array();
                $f = ConnexionBdd::Connecter()->query("SELECT etudiants_inscrits.id_section, sections.section, annee_acad.id_annee FROM etudiants_inscrits LEFT JOIN sections ON etudiants_inscrits.id_section = sections.id_section LEFT JOIN annee_acad ON etudiants_inscrits.id_annee = annee_acad.id_annee WHERE etudiants_inscrits.id_annee = {$annee_acad_fin}");
                while($df = $f->fetch()){
                    $t_fac[] = $df['section'];
                }

                foreach($t_fac as $ff){
                    if(empty($_POST['pourcent_debut']) && empty($_POST['pourcent_fin'])){
                        // la tables etudiants
                        $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT id_annee FROM etudiants_inscrits WHERE id_annee = ? GROUP BY id_annee");
                        $sel_etud->execute(array($annee_acad_fin));
        
                        if($sel_etud->rowCount() > 0){
                            while ($data_student = $sel_etud->fetch()){
                                $pdf->cell(190, 5, decode_fr('Annee Acad.: '.get_annee($annee_acad_fin)), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Faculte : '.$ff), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Promotion : '.$pp), 0, 1, 'L');
                                $pdf->Ln(2);
                                $pdf->cell(60, 5, 'Type frais', 1, 0, 'L');
                                $pdf->cell(30, 5, 'Montant prevu', 1, 0, 'L');
                                $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                                $pdf->cell(30, 5, 'Solde', 1, 0, 'L');
                                $pdf->cell(15, 5, ' % ', 1, 0, 'L');
                                $pdf->Ln(1);
        
                                // tableau
                                $a = array();
                                $b = array();
                                if($_POST['type_frais'] != "Tous"){
                                        $sql_2 = "SELECT
                                                payement.id_payement,
                                                SUM(payement.montant) AS mp,
                                                prevision_frais.id_frais,
                                                SUM(prevision_frais.montant) AS mt,
                                                prevision_frais.type_frais,
                                                annee_acad.annee_acad,
                                                sections.id_section,
                                                options.id_option, options.promotion
                                            FROM
                                                payement
                                            LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                            LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                            LEFT JOIN sections ON payement.id_section = sections.id_section
                                            LEFT JOIN options ON payement.id_option = options.id_option
                                            WHERE
                                                prevision_frais.type_frais = ? AND annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?";
                                        $sql_2_a = array($_POST['type_frais'], $annee_acad_deb, $ff, $pp);
                                        $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                        $sql_2->execute($sql_2_a);
        
                                        while($data = $sql_2->fetch()){
                                            $pdf->SetFont('Arial','i',8);
                                            $pdf->Ln(4);
                                            $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.$data['mt'], 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($data['mt'] - $data['mp']), 1, 0, 'L');
                                            $pdf->cell(15, 5, montant_restant_pourcent($data['mp'], $data['mt']).' % ', 1, 0, 'L');
                                            $pdf->Ln(1);
                                            $a[] = $data['mt'];
                                            $b[] = $data['mp'];
                                        }
                                    // }
        
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                                    $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b) , array_sum($a)).'%', 1, 0, 'L');
                                    $pdf->Ln(8);
                                    $a = array();
                                    $b = array();
                                    // le type de frais n est pas selectionner
                                }else{
                                    $sql_2 = "SELECT
                                                payement.id_payement,
                                                SUM(payement.montant) AS mp,
                                                prevision_frais.id_frais,
                                                SUM(prevision_frais.montant) AS mt,
                                                prevision_frais.type_frais,
                                                annee_acad.annee_acad,
                                                sections.id_section,
                                                options.id_option, options.promotion
                                            FROM
                                                payement
                                            LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                            LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                            LEFT JOIN sections ON payement.id_section = sections.id_section
                                            LEFT JOIN options ON payement.id_option = options.id_option
                                            WHERE
                                                annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?";
                                        $sql_2_a = array($annee_acad_deb, $ff, $pp);
                                        $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                        $sql_2->execute($sql_2_a);
        
                                        while($data = $sql_2->fetch()){
                                            $pdf->SetFont('Arial','i',8);
                                            $pdf->Ln(4);
                                            $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.$data['mt'], 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($data['mt'] - $data['mp']), 1, 0, 'L');
                                            $pdf->cell(15, 5, montant_restant_pourcent($data['mp'], $data['mt']).' % ', 1, 0, 'L');
                                            $pdf->Ln(1);
                                            $a[] = $data['mt'];
                                            $b[] = $data['mp'];
                                        }
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                                    $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b) , array_sum($a)).'%', 1, 0, 'L');
                                    $pdf->Ln(9);
                                    $a = array();
                                    $b = array();
                                }
                            }
                        }
                    }else if(!empty($_POST['pourcent_debut']) && !empty($_POST['pourcent_fin'])){
                        // la tables etudiants
                        $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT id_annee FROM etudiants_inscrits WHERE id_annee = ? GROUP BY id_annee");
                        $sel_etud->execute(array($annee_acad_fin));
        
                        if($sel_etud->rowCount() > 0){
                            while ($data_student = $sel_etud->fetch()){
                                $pdf->cell(190, 5, decode_fr('Annee Acad.: '.get_annee($annee_acad_fin)), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Faculte : '.$ff), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Promotion : '.$pp), 0, 1, 'L');
                                $pdf->Ln(2);
                                $pdf->cell(60, 5, 'Type frais', 1, 0, 'L');
                                $pdf->cell(30, 5, 'Montant prevu', 1, 0, 'L');
                                $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                                $pdf->cell(30, 5, 'Solde', 1, 0, 'L');
                                $pdf->cell(15, 5, ' % ', 1, 0, 'L');
                                $pdf->Ln(1);
        
                                // tableau
                                $a = array();
                                $b = array();
                                if($_POST['type_frais'] != "Tous"){
                                        $sql_2 = "SELECT
                                                payement.id_payement,
                                                SUM(payement.montant) AS mp,
                                                prevision_frais.id_frais,
                                                SUM(prevision_frais.montant) AS mt,
                                                prevision_frais.type_frais,
                                                annee_acad.annee_acad,
                                                sections.id_section,
                                                options.id_option, options.promotion
                                            FROM
                                                payement
                                            LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                            LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                            LEFT JOIN sections ON payement.id_section = sections.id_section
                                            LEFT JOIN options ON payement.id_option = options.id_option
                                            WHERE
                                                prevision_frais.type_frais = ? AND annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?
                                            HAVING ROUND(mp * 100 / ?) >= ? AND ROUND(mp * 100 / ?) <= ?";
                                        $sql_2_a = array($_POST['type_frais'], $annee_acad_deb, $ff, $pp, $pd, $pf);
                                        $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                        $sql_2->execute($sql_2_a);
        
                                        while($data = $sql_2->fetch()){
                                            $pdf->SetFont('Arial','i',8);
                                            $pdf->Ln(4);
                                            $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.$data['mt'], 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($data['mt'] - $data['mp']), 1, 0, 'L');
                                            $pdf->cell(15, 5, montant_restant_pourcent($data['mp'], $data['mt']).' % ', 1, 0, 'L');
                                            $pdf->Ln(1);
                                            $a[] = $data['mt'];
                                            $b[] = $data['mp'];
                                        }
                                    // }
        
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                                    $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b) , array_sum($a)).'%', 1, 0, 'L');
                                    $pdf->Ln(8);
                                    $a = array();
                                    $b = array();
                                    // le type de frais n est pas selectionner
                                }else{
                                    $sql_2 = "SELECT
                                                payement.id_payement,
                                                SUM(payement.montant) AS mp,
                                                prevision_frais.id_frais,
                                                SUM(prevision_frais.montant) AS mt,
                                                prevision_frais.type_frais,
                                                annee_acad.annee_acad,
                                                sections.id_section,
                                                options.id_option, options.promotion
                                            FROM
                                                payement
                                            LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                            LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                            LEFT JOIN sections ON payement.id_section = sections.id_section
                                            LEFT JOIN options ON payement.id_option = options.id_option
                                            WHERE
                                                annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?
                                            HAVING ROUND(mp * 100 / ?) >= ? AND ROUND(mp * 100 / ?) <= ?";
                                        $sql_2_a = array($annee_acad_deb, $ff, $pp, $pd, $pf);
                                        $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                        $sql_2->execute($sql_2_a);
        
                                        while($data = $sql_2->fetch()){
                                            $pdf->SetFont('Arial','i',8);
                                            $pdf->Ln(4);
                                            $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.$data['mt'], 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($data['mt'] - $data['mp']), 1, 0, 'L');
                                            $pdf->cell(15, 5, montant_restant_pourcent($data['mp'], $data['mt']).' % ', 1, 0, 'L');
                                            $pdf->Ln(1);
                                            $a[] = $data['mt'];
                                            $b[] = $data['mp'];
                                        }
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                                    $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b) , array_sum($a)).'%', 1, 0, 'L');
                                    $pdf->Ln(9);
                                    $a = array();
                                    $b = array();
                                }
                            }
                        }
                    }
                }
            }
        }

        $pdf->Ln(15);
        $pdf->SetFont('Arial','',10);
	    $pdf->cell(300,20, decode_fr('par : '.$_SESSION['data']['noms'].'; le '.date('d/M/Y')),0,1,'C');
        $pdf->Output();
    }

    // filtre par annee acaemique
    if(isset($_POST['acad_f'])){
        $pdf = new FPDF('P', 'mm', 'A4');
        
        all($pdf);
        $pdf->SetFont('Arial','BU',10);
        $pdf->cell(190, 10, decode_fr('RAPPORT FRAIS PAR ANNEE ACADEMIQUE '.get_annee($_POST['annee_acad_deb'])), 0, 1, 'C');
        $pdf->SetFont('Arial','',10);
        

        $annee_acad_deb = $_POST['annee_acad_deb'];
        $annee_acad_fin = $_POST['annee_acad_fin'];
        $pd = floatval($_POST['pourcent_debut']);
        $pf = floatval($_POST['pourcent_fin']);
        $ff = $_POST['fac_etudiant'];
        $pp = $_POST['promotion_etud'];
        // die($pf);
        if($annee_acad_deb == $annee_acad_fin){
            $t_fac = array();
            $t_promotion = array();
            $ff = $_POST['fac_etudiant'];
            $pp = $_POST['promotion_etud'];

            // selection des toutes les facultes
            $f = ConnexionBdd::Connecter()->query("SELECT etudiants_inscrits.id_section, sections.section, annee_acad.id_annee FROM etudiants_inscrits LEFT JOIN sections ON etudiants_inscrits.id_section = sections.id_section LEFT JOIN annee_acad ON etudiants_inscrits.id_annee = annee_acad.id_annee WHERE etudiants_inscrits.id_annee = {$annee_acad_fin}");
            while($df = $f->fetch()){
                $t_fac[] = $df['section'];
            }

            if($_POST['fac_etudiant'] == "Tous" && $_POST['promotion_etud'] == "Tous"){
                if(empty($_POST['pourcent_debut']) && empty($_POST['pourcent_fin'])){
                    // la tables etudiants
                    $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT id_annee FROM etudiants_inscrits WHERE id_annee = ? GROUP BY id_annee");
                    $sel_etud->execute(array($annee_acad_fin));
    
                    if($sel_etud->rowCount() > 0){
                        while ($data_student = $sel_etud->fetch()){
                            $pdf->cell(190, 5, decode_fr('Annee Acad.: '.get_annee($annee_acad_fin)), 0, 1, 'L');
                            $pdf->cell(190, 5, decode_fr('Faculte : '.$ff), 0, 1, 'L');
                            $pdf->cell(190, 5, decode_fr('Promotion : '.$pp), 0, 1, 'L');
                            $pdf->Ln(2);
                            $pdf->cell(60, 5, 'Type frais', 1, 0, 'L');
                            $pdf->cell(30, 5, 'Montant prevu', 1, 0, 'L');
                            $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                            $pdf->cell(30, 5, 'Solde', 1, 0, 'L');
                            $pdf->cell(15, 5, ' % ', 1, 0, 'L');
                            $pdf->Ln(1);
    
                            // tableau
                            $a = array();
                            $b = array();
                            if($_POST['type_frais'] != "Tous"){
                                    $sql_2 = "SELECT
                                            payement.id_payement,
                                            SUM(payement.montant) as mp,
                                            prevision_frais.id_frais,
                                            SUM(prevision_frais.montant) as mt,
                                            prevision_frais.type_frais,
                                            annee_acad.annee_acad
                                        FROM
                                            payement
                                        LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                        LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                        WHERE
                                            prevision_frais.type_frais = ? AND annee_acad.id_annee = ?";
                                    $sql_2_a = array($_POST['type_frais'], $annee_acad_deb);
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute($sql_2_a);
    
                                    while($data = $sql_2->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.$data['mt'], 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($data['mt'] - $data['mp']), 1, 0, 'L');
                                        $pdf->cell(15, 5, montant_restant_pourcent($data['mp'], $data['mt']).' % ', 1, 0, 'L');
                                        $pdf->Ln(1);
                                        $a[] = $data['mt'];
                                        $b[] = $data['mp'];
                                    }
                                // }
    
                                $pdf->Ln(4);
                                $pdf->SetFont('Arial','',9);
                                $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                                $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b) , array_sum($a)).'%', 1, 0, 'L');
                                $pdf->Ln(8);
                                $a = array();
                                $b = array();
                                // le type de frais n est pas selectionner
                            }else{
                                $sql_2 = "SELECT
                                            payement.id_payement,
                                            SUM(payement.montant) as mp,
                                            prevision_frais.id_frais,
                                            SUM(prevision_frais.montant) as mt,
                                            prevision_frais.type_frais,
                                            annee_acad.annee_acad
                                        FROM
                                            payement
                                        LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                        LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                        WHERE
                                            annee_acad.id_annee = ?";
                                    $sql_2_a = array($annee_acad_deb);
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute($sql_2_a);
    
                                    while($data = $sql_2->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.$data['mt'], 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($data['mt'] - $data['mp']), 1, 0, 'L');
                                        $pdf->cell(15, 5, montant_restant_pourcent($data['mp'], $data['mt']).' % ', 1, 0, 'L');
                                        $pdf->Ln(1);
                                        $a[] = $data['mt'];
                                        $b[] = $data['mp'];
                                    }
                                $pdf->Ln(4);
                                $pdf->SetFont('Arial','',9);
                                $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                                $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b) , array_sum($a)).'%', 1, 0, 'L');
                                $pdf->Ln(9);
                                $a = array();
                                $b = array();
                            }
                        }
                    }
                }else if(!empty($_POST['pourcent_debut']) && !empty($_POST['pourcent_fin'])){
                    $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT id_annee FROM etudiants_inscrits WHERE id_annee = ? GROUP BY id_annee");
                    $sel_etud->execute(array($annee_acad_fin));
    
                    if($sel_etud->rowCount() > 0){
                        while ($data_student = $sel_etud->fetch()){
                            $pdf->cell(190, 5, decode_fr('Annee Acad.: '.get_annee($annee_acad_fin)), 0, 1, 'L');
                            $pdf->cell(190, 5, decode_fr('Faculte : '.$ff), 0, 1, 'L');
                            $pdf->cell(190, 5, decode_fr('Promotion : '.$pp), 0, 1, 'L');
                            $pdf->Ln(2);
                            $pdf->cell(60, 5, 'Type frais', 1, 0, 'L');
                            $pdf->cell(30, 5, 'Montant prevu', 1, 0, 'L');
                            $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                            $pdf->cell(30, 5, 'Solde', 1, 0, 'L');
                            $pdf->cell(15, 5, ' % ', 1, 0, 'L');
                            $pdf->Ln(1);
    
                            // tableau
                            $a = array();
                            $b = array();
                            if($_POST['type_frais'] != "Tous"){
                                    $sql_2 = "SELECT
                                            payement.id_payement,
                                            SUM(payement.montant) as mp,
                                            prevision_frais.id_frais,
                                            SUM(prevision_frais.montant) as mt,
                                            prevision_frais.type_frais,
                                            annee_acad.annee_acad
                                        FROM
                                            payement
                                        LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                        LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                        WHERE
                                            prevision_frais.type_frais = ? AND annee_acad.id_annee = ?
                                        HAVING ROUND(mp * 100 / ?) >= ? AND ROUND(mp * 100 / ?) <= ?";
                                    $sql_2_a = array($_POST['type_frais'], $annee_acad_deb, $pd, $pf);
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute($sql_2_a);
    
                                    while($data = $sql_2->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.$data['mt'], 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($data['mt'] - $data['mp']), 1, 0, 'L');
                                        $pdf->cell(15, 5, montant_restant_pourcent($data['mp'], $data['mt']).' % ', 1, 0, 'L');
                                        $pdf->Ln(1);
                                        $a[] = $data['mt'];
                                        $b[] = $data['mp'];
                                    }
                                // }
    
                                $pdf->Ln(4);
                                $pdf->SetFont('Arial','',9);
                                $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                                $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b) , array_sum($a)).'%', 1, 0, 'L');
                                $pdf->Ln(8);
                                $a = array();
                                $b = array();
                                // le type de frais n est pas selectionner
                            }else{
                                $sql_2 = "SELECT
                                            payement.id_payement,
                                            SUM(payement.montant) as mp,
                                            prevision_frais.id_frais,
                                            SUM(prevision_frais.montant) as mt,
                                            prevision_frais.type_frais,
                                            annee_acad.annee_acad
                                        FROM
                                            payement
                                        LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                        LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                        WHERE
                                            annee_acad.id_annee = ?
                                        HAVING ROUND(mp * 100 / ?) >= ? AND ROUND(mp * 100 / ?) <= ?";
                                    $sql_2_a = array($annee_acad_deb, $pd, $pf);
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute($sql_2_a);
    
                                    while($data = $sql_2->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.$data['mt'], 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($data['mt'] - $data['mp']), 1, 0, 'L');
                                        $pdf->cell(15, 5, montant_restant_pourcent($data['mp'], $data['mt']).' % ', 1, 0, 'L');
                                        $pdf->Ln(1);
                                        $a[] = $data['mt'];
                                        $b[] = $data['mp'];
                                    }
                                $pdf->Ln(4);
                                $pdf->SetFont('Arial','',9);
                                $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                                $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b) , array_sum($a)).'%', 1, 0, 'L');
                                $pdf->Ln(9);
                                $a = array();
                                $b = array();
                            }
                        }
                    }
                }
            }else if($_POST['fac_etudiant'] != "Tous" && $_POST['promotion_etud'] == "Tous"){
                $t_promotion = array();
                $f = ConnexionBdd::Connecter()->query("SELECT DISTINCT etudiants_inscrits.promotion, options.id_option, sections.section FROM etudiants_inscrits LEFT JOIN options ON etudiants_inscrits.id_option = options.id_option LEFT JOIN sections ON etudiants_inscrits.id_section = sections.id_section WHERE sections.section = '{$_POST['fac_etudiant']}' GROUP BY etudiants_inscrits.promotion");
                while($df = $f->fetch()){
                    $t_promotion[] = $df['promotion'];
                }
                foreach($t_promotion as $pp){
                    if(empty($_POST['pourcent_debut']) && empty($_POST['pourcent_fin'])){
                        // la tables etudiants
                        $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT id_annee FROM etudiants_inscrits WHERE id_annee = ? GROUP BY id_annee");
                        $sel_etud->execute(array($annee_acad_fin));
        
                        if($sel_etud->rowCount() > 0){
                            while ($data_student = $sel_etud->fetch()){
                                $pdf->cell(190, 5, decode_fr('Annee Acad.: '.get_annee($annee_acad_fin)), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Faculte : '.$ff), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Promotion : '.$pp), 0, 1, 'L');
                                $pdf->Ln(2);
                                $pdf->cell(60, 5, 'Type frais', 1, 0, 'L');
                                $pdf->cell(30, 5, 'Montant prevu', 1, 0, 'L');
                                $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                                $pdf->cell(30, 5, 'Solde', 1, 0, 'L');
                                $pdf->cell(15, 5, ' % ', 1, 0, 'L');
                                $pdf->Ln(1);
        
                                // tableau
                                $a = array();
                                $b = array();
                                if($_POST['type_frais'] != "Tous"){
                                        $sql_2 = "SELECT
                                                payement.id_payement,
                                                SUM(payement.montant) AS mp,
                                                prevision_frais.id_frais,
                                                SUM(prevision_frais.montant) AS mt,
                                                prevision_frais.type_frais,
                                                annee_acad.annee_acad,
                                                sections.id_section,
                                                options.id_option, options.promotion
                                            FROM
                                                payement
                                            LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                            LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                            LEFT JOIN sections ON payement.id_section = sections.id_section
                                            LEFT JOIN options ON payement.id_option = options.id_option
                                            WHERE
                                                prevision_frais.type_frais = ? AND annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?";
                                        $sql_2_a = array($_POST['type_frais'], $annee_acad_deb, $ff, $pp);
                                        $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                        $sql_2->execute($sql_2_a);
        
                                        while($data = $sql_2->fetch()){
                                            $pdf->SetFont('Arial','i',8);
                                            $pdf->Ln(4);
                                            $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.$data['mt'], 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($data['mt'] - $data['mp']), 1, 0, 'L');
                                            $pdf->cell(15, 5, montant_restant_pourcent($data['mp'], $data['mt']).' % ', 1, 0, 'L');
                                            $pdf->Ln(1);
                                            $a[] = $data['mt'];
                                            $b[] = $data['mp'];
                                        }
                                    // }
        
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                                    $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b) , array_sum($a)).'%', 1, 0, 'L');
                                    $pdf->Ln(8);
                                    $a = array();
                                    $b = array();
                                    // le type de frais n est pas selectionner
                                }else{
                                    $sql_2 = "SELECT
                                                payement.id_payement,
                                                SUM(payement.montant) AS mp,
                                                prevision_frais.id_frais,
                                                SUM(prevision_frais.montant) AS mt,
                                                prevision_frais.type_frais,
                                                annee_acad.annee_acad,
                                                sections.id_section,
                                                options.id_option, options.promotion
                                            FROM
                                                payement
                                            LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                            LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                            LEFT JOIN sections ON payement.id_section = sections.id_section
                                            LEFT JOIN options ON payement.id_option = options.id_option
                                            WHERE
                                                annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?";
                                        $sql_2_a = array($annee_acad_deb, $ff, $pp);
                                        $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                        $sql_2->execute($sql_2_a);
        
                                        while($data = $sql_2->fetch()){
                                            $pdf->SetFont('Arial','i',8);
                                            $pdf->Ln(4);
                                            $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.$data['mt'], 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($data['mt'] - $data['mp']), 1, 0, 'L');
                                            $pdf->cell(15, 5, montant_restant_pourcent($data['mp'], $data['mt']).' % ', 1, 0, 'L');
                                            $pdf->Ln(1);
                                            $a[] = $data['mt'];
                                            $b[] = $data['mp'];
                                        }
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                                    $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b) , array_sum($a)).'%', 1, 0, 'L');
                                    $pdf->Ln(9);
                                    $a = array();
                                    $b = array();
                                }
                            }
                        }
                    }else if(!empty($_POST['pourcent_debut']) && !empty($_POST['pourcent_fin'])){
                        // la tables etudiants
                        $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT id_annee FROM etudiants_inscrits WHERE id_annee = ? GROUP BY id_annee");
                        $sel_etud->execute(array($annee_acad_fin));
        
                        if($sel_etud->rowCount() > 0){
                            while ($data_student = $sel_etud->fetch()){
                                $pdf->cell(190, 5, decode_fr('Annee Acad.: '.get_annee($annee_acad_fin)), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Faculte : '.$ff), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Promotion : '.$pp), 0, 1, 'L');
                                $pdf->Ln(2);
                                $pdf->cell(60, 5, 'Type frais', 1, 0, 'L');
                                $pdf->cell(30, 5, 'Montant prevu', 1, 0, 'L');
                                $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                                $pdf->cell(30, 5, 'Solde', 1, 0, 'L');
                                $pdf->cell(15, 5, ' % ', 1, 0, 'L');
                                $pdf->Ln(1);
        
                                // tableau
                                $a = array();
                                $b = array();
                                if($_POST['type_frais'] != "Tous"){
                                        $sql_2 = "SELECT
                                                payement.id_payement,
                                                SUM(payement.montant) AS mp,
                                                prevision_frais.id_frais,
                                                SUM(prevision_frais.montant) AS mt,
                                                prevision_frais.type_frais,
                                                annee_acad.annee_acad,
                                                sections.id_section,
                                                options.id_option, options.promotion
                                            FROM
                                                payement
                                            LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                            LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                            LEFT JOIN sections ON payement.id_section = sections.id_section
                                            LEFT JOIN options ON payement.id_option = options.id_option
                                            WHERE
                                                prevision_frais.type_frais = ? AND annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?
                                            HAVING ROUND(mp * 100 / ?) >= ? AND ROUND(mp * 100 / ?) <= ?";
                                        $sql_2_a = array($_POST['type_frais'], $annee_acad_deb, $ff, $pp, $pd, $pf);
                                        $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                        $sql_2->execute($sql_2_a);
        
                                        while($data = $sql_2->fetch()){
                                            $pdf->SetFont('Arial','i',8);
                                            $pdf->Ln(4);
                                            $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.$data['mt'], 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($data['mt'] - $data['mp']), 1, 0, 'L');
                                            $pdf->cell(15, 5, montant_restant_pourcent($data['mp'], $data['mt']).' % ', 1, 0, 'L');
                                            $pdf->Ln(1);
                                            $a[] = $data['mt'];
                                            $b[] = $data['mp'];
                                        }
                                    // }
        
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                                    $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b) , array_sum($a)).'%', 1, 0, 'L');
                                    $pdf->Ln(8);
                                    $a = array();
                                    $b = array();
                                    // le type de frais n est pas selectionner
                                }else{
                                    $sql_2 = "SELECT
                                                payement.id_payement,
                                                SUM(payement.montant) AS mp,
                                                prevision_frais.id_frais,
                                                SUM(prevision_frais.montant) AS mt,
                                                prevision_frais.type_frais,
                                                annee_acad.annee_acad,
                                                sections.id_section,
                                                options.id_option, options.promotion
                                            FROM
                                                payement
                                            LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                            LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                            LEFT JOIN sections ON payement.id_section = sections.id_section
                                            LEFT JOIN options ON payement.id_option = options.id_option
                                            WHERE
                                                annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?
                                            HAVING ROUND(mp * 100 / ?) >= ? AND ROUND(mp * 100 / ?) <= ?";
                                        $sql_2_a = array($annee_acad_deb, $ff, $pp, $pd, $pf);
                                        $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                        $sql_2->execute($sql_2_a);
        
                                        while($data = $sql_2->fetch()){
                                            $pdf->SetFont('Arial','i',8);
                                            $pdf->Ln(4);
                                            $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.$data['mt'], 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($data['mt'] - $data['mp']), 1, 0, 'L');
                                            $pdf->cell(15, 5, montant_restant_pourcent($data['mp'], $data['mt']).' % ', 1, 0, 'L');
                                            $pdf->Ln(1);
                                            $a[] = $data['mt'];
                                            $b[] = $data['mp'];
                                        }
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                                    $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b) , array_sum($a)).'%', 1, 0, 'L');
                                    $pdf->Ln(9);
                                    $a = array();
                                    $b = array();
                                }
                            }
                        }
                    }
                }
            }else if($_POST['fac_etudiant'] != "Tous" && $_POST['promotion_etud'] != "Tous"){
                if(empty($_POST['pourcent_debut']) && empty($_POST['pourcent_fin'])){
                    // la tables etudiants
                    $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT id_annee FROM etudiants_inscrits WHERE id_annee = ? GROUP BY id_annee");
                    $sel_etud->execute(array($annee_acad_fin));
    
                    if($sel_etud->rowCount() > 0){
                        while ($data_student = $sel_etud->fetch()){
                            $pdf->cell(190, 5, decode_fr('Annee Acad.: '.get_annee($annee_acad_fin)), 0, 1, 'L');
                            $pdf->cell(190, 5, decode_fr('Faculte : '.$ff), 0, 1, 'L');
                            $pdf->cell(190, 5, decode_fr('Promotion : '.$pp), 0, 1, 'L');
                            $pdf->Ln(2);
                            $pdf->cell(60, 5, 'Type frais', 1, 0, 'L');
                            $pdf->cell(30, 5, 'Montant prevu', 1, 0, 'L');
                            $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                            $pdf->cell(30, 5, 'Solde', 1, 0, 'L');
                            $pdf->cell(15, 5, ' % ', 1, 0, 'L');
                            $pdf->Ln(1);
    
                            // tableau
                            $a = array();
                            $b = array();
                            if($_POST['type_frais'] != "Tous"){
                                    $sql_2 = "SELECT
                                            payement.id_payement,
                                            SUM(payement.montant) AS mp,
                                            prevision_frais.id_frais,
                                            SUM(prevision_frais.montant) AS mt,
                                            prevision_frais.type_frais,
                                            annee_acad.annee_acad,
                                            sections.id_section,
                                            options.id_option, options.promotion
                                        FROM
                                            payement
                                        LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                        LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                        LEFT JOIN sections ON payement.id_section = sections.id_section
                                        LEFT JOIN options ON payement.id_option = options.id_option
                                        WHERE
                                            prevision_frais.type_frais = ? AND annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?";
                                    $sql_2_a = array($_POST['type_frais'], $annee_acad_deb, $ff, $pp);
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute($sql_2_a);
    
                                    while($data = $sql_2->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.$data['mt'], 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($data['mt'] - $data['mp']), 1, 0, 'L');
                                        $pdf->cell(15, 5, montant_restant_pourcent($data['mp'], $data['mt']).' % ', 1, 0, 'L');
                                        $pdf->Ln(1);
                                        $a[] = $data['mt'];
                                        $b[] = $data['mp'];
                                    }
                                // }
    
                                $pdf->Ln(4);
                                $pdf->SetFont('Arial','',9);
                                $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                                $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b) , array_sum($a)).'%', 1, 0, 'L');
                                $pdf->Ln(8);
                                $a = array();
                                $b = array();
                                // le type de frais n est pas selectionner
                            }else{
                                $sql_2 = "SELECT
                                            payement.id_payement,
                                            SUM(payement.montant) AS mp,
                                            prevision_frais.id_frais,
                                            SUM(prevision_frais.montant) AS mt,
                                            prevision_frais.type_frais,
                                            annee_acad.annee_acad,
                                            sections.id_section,
                                            options.id_option, options.promotion
                                        FROM
                                            payement
                                        LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                        LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                        LEFT JOIN sections ON payement.id_section = sections.id_section
                                        LEFT JOIN options ON payement.id_option = options.id_option
                                        WHERE
                                            annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?";
                                    $sql_2_a = array($annee_acad_deb, $ff, $pp);
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute($sql_2_a);
    
                                    while($data = $sql_2->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.$data['mt'], 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($data['mt'] - $data['mp']), 1, 0, 'L');
                                        $pdf->cell(15, 5, montant_restant_pourcent($data['mp'], $data['mt']).' % ', 1, 0, 'L');
                                        $pdf->Ln(1);
                                        $a[] = $data['mt'];
                                        $b[] = $data['mp'];
                                    }
                                $pdf->Ln(4);
                                $pdf->SetFont('Arial','',9);
                                $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                                $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b) , array_sum($a)).'%', 1, 0, 'L');
                                $pdf->Ln(9);
                                $a = array();
                                $b = array();
                            }
                        }
                    }
                }else if(!empty($_POST['pourcent_debut']) && !empty($_POST['pourcent_fin'])){
                    // la tables etudiants
                    $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT id_annee FROM etudiants_inscrits WHERE id_annee = ? GROUP BY id_annee");
                    $sel_etud->execute(array($annee_acad_fin));
    
                    if($sel_etud->rowCount() > 0){
                        while ($data_student = $sel_etud->fetch()){
                            $pdf->cell(190, 5, decode_fr('Annee Acad.: '.get_annee($annee_acad_fin)), 0, 1, 'L');
                            $pdf->cell(190, 5, decode_fr('Faculte : '.$ff), 0, 1, 'L');
                            $pdf->cell(190, 5, decode_fr('Promotion : '.$pp), 0, 1, 'L');
                            $pdf->Ln(2);
                            $pdf->cell(60, 5, 'Type frais', 1, 0, 'L');
                            $pdf->cell(30, 5, 'Montant prevu', 1, 0, 'L');
                            $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                            $pdf->cell(30, 5, 'Solde', 1, 0, 'L');
                            $pdf->cell(15, 5, ' % ', 1, 0, 'L');
                            $pdf->Ln(1);
    
                            // tableau
                            $a = array();
                            $b = array();
                            if($_POST['type_frais'] != "Tous"){
                                    $sql_2 = "SELECT
                                            payement.id_payement,
                                            SUM(payement.montant) AS mp,
                                            prevision_frais.id_frais,
                                            SUM(prevision_frais.montant) AS mt,
                                            prevision_frais.type_frais,
                                            annee_acad.annee_acad,
                                            sections.id_section,
                                            options.id_option, options.promotion
                                        FROM
                                            payement
                                        LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                        LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                        LEFT JOIN sections ON payement.id_section = sections.id_section
                                        LEFT JOIN options ON payement.id_option = options.id_option
                                        WHERE
                                            prevision_frais.type_frais = ? AND annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?
                                        HAVING ROUND(mp * 100 / ?) >= ? AND ROUND(mp * 100 / ?) <= ?";
                                    $sql_2_a = array($_POST['type_frais'], $annee_acad_deb, $ff, $pp, $pd, $pf);
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute($sql_2_a);
    
                                    while($data = $sql_2->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.$data['mt'], 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($data['mt'] - $data['mp']), 1, 0, 'L');
                                        $pdf->cell(15, 5, montant_restant_pourcent($data['mp'], $data['mt']).' % ', 1, 0, 'L');
                                        $pdf->Ln(1);
                                        $a[] = $data['mt'];
                                        $b[] = $data['mp'];
                                    }
                                // }
    
                                $pdf->Ln(4);
                                $pdf->SetFont('Arial','',9);
                                $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                                $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b) , array_sum($a)).'%', 1, 0, 'L');
                                $pdf->Ln(8);
                                $a = array();
                                $b = array();
                                // le type de frais n est pas selectionner
                            }else{
                                $sql_2 = "SELECT
                                            payement.id_payement,
                                            SUM(payement.montant) AS mp,
                                            prevision_frais.id_frais,
                                            SUM(prevision_frais.montant) AS mt,
                                            prevision_frais.type_frais,
                                            annee_acad.annee_acad,
                                            sections.id_section,
                                            options.id_option, options.promotion
                                        FROM
                                            payement
                                        LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                        LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                        LEFT JOIN sections ON payement.id_section = sections.id_section
                                        LEFT JOIN options ON payement.id_option = options.id_option
                                        WHERE
                                            annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?
                                        HAVING ROUND(mp * 100 / ?) >= ? AND ROUND(mp * 100 / ?) <= ?";
                                    $sql_2_a = array($annee_acad_deb, $ff, $pp, $pd, $pf);
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute($sql_2_a);
    
                                    while($data = $sql_2->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.$data['mt'], 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($data['mt'] - $data['mp']), 1, 0, 'L');
                                        $pdf->cell(15, 5, montant_restant_pourcent($data['mp'], $data['mt']).' % ', 1, 0, 'L');
                                        $pdf->Ln(1);
                                        $a[] = $data['mt'];
                                        $b[] = $data['mp'];
                                    }
                                $pdf->Ln(4);
                                $pdf->SetFont('Arial','',9);
                                $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                                $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b) , array_sum($a)).'%', 1, 0, 'L');
                                $pdf->Ln(9);
                                $a = array();
                                $b = array();
                            }
                        }
                    }
                }
            }else if($_POST['fac_etudiant'] = "Tous" && $_POST['promotion_etud'] !="Tous"){
                $t_fac = array();
                $f = ConnexionBdd::Connecter()->query("SELECT etudiants_inscrits.id_section, sections.section, annee_acad.id_annee FROM etudiants_inscrits LEFT JOIN sections ON etudiants_inscrits.id_section = sections.id_section LEFT JOIN annee_acad ON etudiants_inscrits.id_annee = annee_acad.id_annee WHERE etudiants_inscrits.id_annee = {$annee_acad_fin}");
                while($df = $f->fetch()){
                    $t_fac[] = $df['section'];
                }

                foreach($t_fac as $ff){
                    if(empty($_POST['pourcent_debut']) && empty($_POST['pourcent_fin'])){
                        // la tables etudiants
                        $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT id_annee FROM etudiants_inscrits WHERE id_annee = ? GROUP BY id_annee");
                        $sel_etud->execute(array($annee_acad_fin));
        
                        if($sel_etud->rowCount() > 0){
                            while ($data_student = $sel_etud->fetch()){
                                $pdf->cell(190, 5, decode_fr('Annee Acad.: '.get_annee($annee_acad_fin)), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Faculte : '.$ff), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Promotion : '.$pp), 0, 1, 'L');
                                $pdf->Ln(2);
                                $pdf->cell(60, 5, 'Type frais', 1, 0, 'L');
                                $pdf->cell(30, 5, 'Montant prevu', 1, 0, 'L');
                                $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                                $pdf->cell(30, 5, 'Solde', 1, 0, 'L');
                                $pdf->cell(15, 5, ' % ', 1, 0, 'L');
                                $pdf->Ln(1);
        
                                // tableau
                                $a = array();
                                $b = array();
                                if($_POST['type_frais'] != "Tous"){
                                        $sql_2 = "SELECT
                                                payement.id_payement,
                                                SUM(payement.montant) AS mp,
                                                prevision_frais.id_frais,
                                                SUM(prevision_frais.montant) AS mt,
                                                prevision_frais.type_frais,
                                                annee_acad.annee_acad,
                                                sections.id_section,
                                                options.id_option, options.promotion
                                            FROM
                                                payement
                                            LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                            LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                            LEFT JOIN sections ON payement.id_section = sections.id_section
                                            LEFT JOIN options ON payement.id_option = options.id_option
                                            WHERE
                                                prevision_frais.type_frais = ? AND annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?";
                                        $sql_2_a = array($_POST['type_frais'], $annee_acad_deb, $ff, $pp);
                                        $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                        $sql_2->execute($sql_2_a);
        
                                        while($data = $sql_2->fetch()){
                                            $pdf->SetFont('Arial','i',8);
                                            $pdf->Ln(4);
                                            $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.$data['mt'], 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($data['mt'] - $data['mp']), 1, 0, 'L');
                                            $pdf->cell(15, 5, montant_restant_pourcent($data['mp'], $data['mt']).' % ', 1, 0, 'L');
                                            $pdf->Ln(1);
                                            $a[] = $data['mt'];
                                            $b[] = $data['mp'];
                                        }
                                    // }
        
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                                    $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b) , array_sum($a)).'%', 1, 0, 'L');
                                    $pdf->Ln(8);
                                    $a = array();
                                    $b = array();
                                    // le type de frais n est pas selectionner
                                }else{
                                    $sql_2 = "SELECT
                                                payement.id_payement,
                                                SUM(payement.montant) AS mp,
                                                prevision_frais.id_frais,
                                                SUM(prevision_frais.montant) AS mt,
                                                prevision_frais.type_frais,
                                                annee_acad.annee_acad,
                                                sections.id_section,
                                                options.id_option, options.promotion
                                            FROM
                                                payement
                                            LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                            LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                            LEFT JOIN sections ON payement.id_section = sections.id_section
                                            LEFT JOIN options ON payement.id_option = options.id_option
                                            WHERE
                                                annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?";
                                        $sql_2_a = array($annee_acad_deb, $ff, $pp);
                                        $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                        $sql_2->execute($sql_2_a);
        
                                        while($data = $sql_2->fetch()){
                                            $pdf->SetFont('Arial','i',8);
                                            $pdf->Ln(4);
                                            $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.$data['mt'], 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($data['mt'] - $data['mp']), 1, 0, 'L');
                                            $pdf->cell(15, 5, montant_restant_pourcent($data['mp'], $data['mt']).' % ', 1, 0, 'L');
                                            $pdf->Ln(1);
                                            $a[] = $data['mt'];
                                            $b[] = $data['mp'];
                                        }
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                                    $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b) , array_sum($a)).'%', 1, 0, 'L');
                                    $pdf->Ln(9);
                                    $a = array();
                                    $b = array();
                                }
                            }
                        }
                    }else if(!empty($_POST['pourcent_debut']) && !empty($_POST['pourcent_fin'])){
                        // la tables etudiants
                        $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT id_annee FROM etudiants_inscrits WHERE id_annee = ? GROUP BY id_annee");
                        $sel_etud->execute(array($annee_acad_fin));
        
                        if($sel_etud->rowCount() > 0){
                            while ($data_student = $sel_etud->fetch()){
                                $pdf->cell(190, 5, decode_fr('Annee Acad.: '.get_annee($annee_acad_fin)), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Faculte : '.$ff), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Promotion : '.$pp), 0, 1, 'L');
                                $pdf->Ln(2);
                                $pdf->cell(60, 5, 'Type frais', 1, 0, 'L');
                                $pdf->cell(30, 5, 'Montant prevu', 1, 0, 'L');
                                $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                                $pdf->cell(30, 5, 'Solde', 1, 0, 'L');
                                $pdf->cell(15, 5, ' % ', 1, 0, 'L');
                                $pdf->Ln(1);
        
                                // tableau
                                $a = array();
                                $b = array();
                                if($_POST['type_frais'] != "Tous"){
                                        $sql_2 = "SELECT
                                                payement.id_payement,
                                                SUM(payement.montant) AS mp,
                                                prevision_frais.id_frais,
                                                SUM(prevision_frais.montant) AS mt,
                                                prevision_frais.type_frais,
                                                annee_acad.annee_acad,
                                                sections.id_section,
                                                options.id_option, options.promotion
                                            FROM
                                                payement
                                            LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                            LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                            LEFT JOIN sections ON payement.id_section = sections.id_section
                                            LEFT JOIN options ON payement.id_option = options.id_option
                                            WHERE
                                                prevision_frais.type_frais = ? AND annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?
                                            HAVING ROUND(mp * 100 / ?) >= ? AND ROUND(mp * 100 / ?) <= ?";
                                        $sql_2_a = array($_POST['type_frais'], $annee_acad_deb, $ff, $pp, $pd, $pf);
                                        $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                        $sql_2->execute($sql_2_a);
        
                                        while($data = $sql_2->fetch()){
                                            $pdf->SetFont('Arial','i',8);
                                            $pdf->Ln(4);
                                            $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.$data['mt'], 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($data['mt'] - $data['mp']), 1, 0, 'L');
                                            $pdf->cell(15, 5, montant_restant_pourcent($data['mp'], $data['mt']).' % ', 1, 0, 'L');
                                            $pdf->Ln(1);
                                            $a[] = $data['mt'];
                                            $b[] = $data['mp'];
                                        }
                                    // }
        
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                                    $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b) , array_sum($a)).'%', 1, 0, 'L');
                                    $pdf->Ln(8);
                                    $a = array();
                                    $b = array();
                                    // le type de frais n est pas selectionner
                                }else{
                                    $sql_2 = "SELECT
                                                payement.id_payement,
                                                SUM(payement.montant) AS mp,
                                                prevision_frais.id_frais,
                                                SUM(prevision_frais.montant) AS mt,
                                                prevision_frais.type_frais,
                                                annee_acad.annee_acad,
                                                sections.id_section,
                                                options.id_option, options.promotion
                                            FROM
                                                payement
                                            LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                            LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                            LEFT JOIN sections ON payement.id_section = sections.id_section
                                            LEFT JOIN options ON payement.id_option = options.id_option
                                            WHERE
                                                annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?
                                            HAVING ROUND(mp * 100 / ?) >= ? AND ROUND(mp * 100 / ?) <= ?";
                                        $sql_2_a = array($annee_acad_deb, $ff, $pp, $pd, $pf);
                                        $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                        $sql_2->execute($sql_2_a);
        
                                        while($data = $sql_2->fetch()){
                                            $pdf->SetFont('Arial','i',8);
                                            $pdf->Ln(4);
                                            $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.$data['mt'], 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($data['mt'] - $data['mp']), 1, 0, 'L');
                                            $pdf->cell(15, 5, montant_restant_pourcent($data['mp'], $data['mt']).' % ', 1, 0, 'L');
                                            $pdf->Ln(1);
                                            $a[] = $data['mt'];
                                            $b[] = $data['mp'];
                                        }
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                                    $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b) , array_sum($a)).'%', 1, 0, 'L');
                                    $pdf->Ln(9);
                                    $a = array();
                                    $b = array();
                                }
                            }
                        }
                    }
                }
            }
        }

        $pdf->Ln(15);
        $pdf->SetFont('Arial','',10);
	    $pdf->cell(300,20, decode_fr('par : '.$_SESSION['data']['noms'].'; le '.date('d/M/Y')),0,1,'C');
        $pdf->Output();
    }

    // sythese par etudiant {chaque etudiant : tous les etudiants d une faculte dans une meme faculte.}
    if(isset($_POST['btn_etudiant'])){
        $pdf = new FPDF('P', 'mm', 'A4');
        $annee_acad_deb = $_POST['annee_acad_deb'];
        $annee_acad_fin = $_POST['annee_acad_deb'];
        $pd = floatval($_POST['pourcent_debut']);
        $pf = floatval($_POST['pourcent_fin']);

        if($annee_acad_deb == $annee_acad_fin){
            $pdf = new FPDF('P', 'mm', 'A4');
            all($pdf);
            $pdf->SetFont('Arial','BU',10);
            $pdf->cell(190, 10, decode_fr('SYNTHESE  PAR ETUDIANT '.get_annee($_POST['annee_acad_deb'])), 0, 1, 'C');
            $pdf->SetFont('Arial','',9);
            $annee_acad_deb = $_POST['annee_acad_deb'];
            $annee_acad_fin = $_POST['annee_acad_fin'];

            // tableau
            $a = array();
            $b = array();

            $t_fac = array();
            $t_promotion = array();
            $ff = $_POST['fac_etudiant'];
            $pp = $_POST['promotion_etud'];

            // selection des toutes les facultes
            $f = ConnexionBdd::Connecter()->query("SELECT etudiants_inscrits.id_section, sections.section, annee_acad.id_annee FROM etudiants_inscrits LEFT JOIN sections ON etudiants_inscrits.id_section = sections.id_section LEFT JOIN annee_acad ON etudiants_inscrits.id_annee = annee_acad.id_annee WHERE etudiants_inscrits.id_annee = {$annee_acad_fin}");
            while($df = $f->fetch()){
                $t_fac[] = $df['section'];
            }

            if($_POST['fac_etudiant'] == "Tous" && $_POST['promotion_etud'] == "Tous"){
                if(empty($_POST['pourcent_debut']) && empty($_POST['pourcent_fin'])){
                    foreach($t_fac as $ff){
                        $t_promotion = array();
                        $f = ConnexionBdd::Connecter()->query("SELECT DISTINCT etudiants_inscrits.promotion, options.id_option, sections.section FROM etudiants_inscrits LEFT JOIN options ON etudiants_inscrits.id_option = options.id_option LEFT JOIN sections ON etudiants_inscrits.id_section = sections.id_section WHERE sections.section = '{$_POST['fac_etudiant']}' GROUP BY etudiants_inscrits.promotion");
                        while($df = $f->fetch()){
                            $t_promotion[] = $df['promotion'];
                        }
                        foreach($t_promotion as $pp){
                            $pdf->SetFont('Arial','B',10);
                            $pdf->cell(190, 5, decode_fr("Faculte : ".$ff), 0, 1, 'L');
                            $pdf->cell(190, 5, decode_fr("Promotion : ".$pp), 0, 1, 'L');
                            $pdf->cell(190, 5, decode_fr("Année Acad. : ".get_annee($annee_acad_fin)), 0, 1, 'L');
                            $pdf->Ln(2);
                            $pdf->cell(15, 5, 'mat', 1, 0, 'L');
                            $pdf->cell(70, 5, 'Noms', 1, 0, 'L');
                            $pdf->cell(30, 5, 'Montant prevu', 1, 0, 'L');
                            $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                            $pdf->cell(30, 5, 'Solde ', 1, 0, 'L');
                            $pdf->cell(15, 5, ' % ', 1, 0, 'L');
                            $pdf->Ln(2);
                            $pdf->SetFont('Arial','',10);

                            $a = array();
                            $b = array();

                            // selection de l etudiant
                            $sql = "SELECT
                                    etudiants_inscrits.matricule,
                                    etudiants_inscrits.noms,
                                    affectation_frais.id,
                                    prevision_frais.type_frais,
                                    prevision_frais.montant,
                                    payement.montant
                                FROM
                                    etudiants_inscrits,
                                    affectation_frais
                                LEFT JOIN prevision_frais ON affectation_frais.id_frais = prevision_frais.id_frais
                                LEFT JOIN payement ON affectation_frais.id_frais = payement.id_frais";
                                
                            $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT matricule, noms, promotion, fac, annee_academique FROM etudiants_inscrits WHERE promotion = ? AND fac = ? AND annee_academique = ? GROUP BY matricule, fac, promotion");
                            $sel_etud->execute(array($pp, $ff, $annee_acad_fin)); 
                            
                            while($data_etu = $sel_etud->fetch()){
                                if($_POST['type_frais'] == "Tous"){
                                    $mt = ConnexionBdd::Connecter()->prepare("SELECT SUM(montant) AS mt FROM affectation_frais WHERE matricule  = ? AND promotion = ? AND faculte = ? AND annee_acad = ?");
                                    $mt->execute(array($data_etu['matricule'], $data_etu['promotion'], $data_etu['fac'], $data_etu['annee_academique']));

                                    while($data = $mt->fetch()){
                                        $payement = ConnexionBdd::Connecter()->prepare("SELECT SUM(montant) AS mp FROM payement WHERE matricule  = ? AND promotion = ? AND faculte = ? AND annee_acad = ?");
                                        $payement->execute(array($data_etu['matricule'], $data_etu['promotion'], $data_etu['fac'], $data_etu['annee_academique']));
                                        while($pay = $payement->fetch()){
                                            $pdf->SetFont('Arial','I',8);
                                            $pdf->Ln(3);
                                            $pdf->cell(15, 7, $data_etu['matricule'], 1, 0, 'L');
                                            $pdf->cell(70, 7, $data_etu['noms'], 1, 0, 'L');
                                            $pdf->cell(30, 7, '$ '.mm($data['mt']), 1, 0, 'L');
                                            $pdf->cell(30, 7, '$ '.mm($pay['mp']), 1, 0, 'L');
                                            $pdf->cell(30, 7, '$ '.mm($data['mt'] - $pay['mp']), 1, 0, 'L');
                                            $pdf->cell(15, 7, montant_restant_pourcent($pay['mp'], $data['mt']).' % ', 1, 0, 'L');
                                            $pdf->Ln(4);
                                            $b[] = $pay['mp'];
                                            $a[] = $data['mt'];
                                        }
                                    }
                                }else{
                                    $mt = ConnexionBdd::Connecter()->prepare("SELECT type_frais, SUM(montant) AS mt FROM affectation_frais WHERE matricule  = ? AND promotion = ? AND faculte = ? AND type_frais = ? AND annee_acad = ? HAVING mt > 0 ");
                                    $mt->execute(array($data_etu['matricule'], $data_etu['promotion'], $data_etu['fac'], $_POST['type_frais'], $data_etu['annee_academique']));

                                    while($data = $mt->fetch()){
                                        $payement = ConnexionBdd::Connecter()->prepare("SELECT SUM(montant) AS mp FROM payement WHERE matricule  = ? AND promotion = ? AND faculte = ? AND type_frais= ? AND annee_acad = ?");
                                        $payement->execute(array($data_etu['matricule'], $data_etu['promotion'], $data_etu['fac'], $data['type_frais'], $data_etu['annee_academique']));
                                        while($pay = $payement->fetch()){
                                            $pdf->SetFont('Arial','I',8);
                                            $pdf->Ln(3);
                                            $pdf->cell(15, 7, $data_etu['matricule'], 1, 0, 'L');
                                            $pdf->cell(70, 7, $data_etu['noms'], 1, 0, 'L');
                                            $pdf->cell(30, 7, '$ '.mm($data['mt']), 1, 0, 'L');
                                            $pdf->cell(30, 7, '$ '.mm($pay['mp']), 1, 0, 'L');
                                            $pdf->cell(30, 7, '$ '.mm($data['mt'] - $pay['mp']), 1, 0, 'L');
                                            $pdf->cell(15, 7, montant_restant_pourcent($pay['mp'], $data['mt']).' % ', 1, 0, 'L');
                                            $pdf->Ln(4);
                                            $b[] = $pay['mp'];
                                            $a[] = $data['mt'];
                                        }
                                    }
                                }   
                            }
                                
                            $pdf->Ln(3);
                            $pdf->SetFillColor(0,0,0);
                            $pdf->cell(15, 5, decode_fr('Total'), 1, 0, 'L', true);
                            $pdf->cell(70, 5, decode_fr('Total'), 1, 0, 'L');
                            $pdf->cell(30, 5, '$ '.mm(array_sum($a)), 1, 0, 'L');
                            $pdf->cell(30, 5, '$ '.mm(array_sum($b)), 1, 0, 'L');
                            $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                            $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b), array_sum($a)).'%', 1, 0, 'L');
                            $a = array();
                            $b = array();
                            $pdf->Ln(8);
                            $pdf->Ln(9);
                        }
                    }
                }else if(!empty($_POST['pourcent_debut']) && !empty($_POST['pourcent_fin'])){
                    foreach($t_fac as $ff){
                        foreach($t_promotion as $pp){
                            $pdf->SetFont('Arial','B',10);
                            $pdf->cell(190, 5, decode_fr("Faculte : ".$ff), 0, 1, 'L');
                            $pdf->cell(190, 5, decode_fr("Promotion : ".$pp), 0, 1, 'L');
                            $pdf->cell(190, 5, decode_fr("Année Acad. : ".$annee_acad_fin), 0, 1, 'L');
                            $pdf->Ln(2);
                            $pdf->cell(15, 5, 'mat', 1, 0, 'L');
                            $pdf->cell(70, 5, 'Noms', 1, 0, 'L');
                            $pdf->cell(30, 5, 'Montant prevu', 1, 0, 'L');
                            $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                            $pdf->cell(30, 5, 'Solde ', 1, 0, 'L');
                            $pdf->cell(15, 5, ' % ', 1, 0, 'L');
                            $pdf->Ln(2);
                            $pdf->SetFont('Arial','',10);

                            $a = array();
                            $b = array();

                            // selection de l etudiant
                            $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT matricule, noms, promotion, fac, annee_academique FROM etudiants_inscrits WHERE promotion = ? AND fac = ? AND annee_academique = ? GROUP BY matricule");
                            $sel_etud->execute(array($pp, $ff, $annee_acad_fin)); 
                            
                            while($data_etu = $sel_etud->fetch()){
                                if($_POST['type_frais'] == "Tous"){
                                    $mt = ConnexionBdd::Connecter()->prepare("SELECT SUM(montant) AS mt FROM affectation_frais WHERE matricule  = ? AND promotion = ? AND faculte = ? AND annee_acad = ?");
                                    $mt->execute(array($data_etu['matricule'], $data_etu['promotion'], $data_etu['fac'], $data_etu['annee_academique']));

                                    while($data = $mt->fetch()){
                                        $payement = ConnexionBdd::Connecter()->prepare("SELECT SUM(montant) AS mp FROM payement WHERE matricule  = ? AND promotion = ? AND faculte = ? AND annee_acad = ? HAVING ROUND(mp * 100 / ?) >= ? AND ROUND(mp * 100 / ?) <= ?");
                                        $payement->execute(array($data_etu['matricule'], $data_etu['promotion'], $data_etu['fac'], $data_etu['annee_academique'], $data['mt'], $_POST['pourcent_debut'], $data['mt'], $_POST['pourcent_fin']));
                                        while($pay = $payement->fetch()){
                                            $pdf->SetFont('Arial','I',8);
                                            $pdf->Ln(3);
                                            $pdf->cell(15, 7, $data_etu['matricule'], 1, 0, 'L');
                                            $pdf->cell(70, 7, $data_etu['noms'], 1, 0, 'L');
                                            $pdf->cell(30, 7, '$ '.mm($data['mt']), 1, 0, 'L');
                                            $pdf->cell(30, 7, '$ '.mm($pay['mp']), 1, 0, 'L');
                                            $pdf->cell(30, 7, '$ '.mm($data['mt'] - $pay['mp']), 1, 0, 'L');
                                            $pdf->cell(15, 7, montant_restant_pourcent($pay['mp'], $data['mt']).' % ', 1, 0, 'L');
                                            $pdf->Ln(4);
                                            $b[] = $pay['mp'];
                                            $a[] = $data['mt'];
                                        }
                                    }   
                                }else{
                                    $mt = ConnexionBdd::Connecter()->prepare("SELECT type_frais, SUM(montant) AS mt FROM affectation_frais WHERE matricule  = ? AND promotion = ? AND faculte = ? AND type_frais = ? AND annee_acad = ?");
                                    $mt->execute(array($data_etu['matricule'], $data_etu['promotion'], $data_etu['fac'], $_POST['type_frais'], $data_etu['annee_academique']));

                                    while($data = $mt->fetch()){
                                        $payement = ConnexionBdd::Connecter()->prepare("SELECT SUM(montant) AS mp FROM payement WHERE matricule  = ? AND promotion = ? AND faculte = ? AND type_frais  = ? AND annee_acad = ? HAVING ROUND(mp * 100 / ?) >= ? AND ROUND(mp * 100 / ?) <= ?");
                                        $payement->execute(array($data_etu['matricule'], $data_etu['promotion'], $data_etu['fac'], $data['type_frais'], $data_etu['annee_academique'], $data['mt'], $_POST['pourcent_debut'], $data['mt'], $_POST['pourcent_fin']));
                                        while($pay = $payement->fetch()){
                                            $pdf->SetFont('Arial','I',8);
                                            $pdf->Ln(3);
                                            $pdf->cell(15, 7, $data_etu['matricule'], 1, 0, 'L');
                                            $pdf->cell(70, 7, $data_etu['noms'], 1, 0, 'L');
                                            $pdf->cell(30, 7, '$ '.mm($data['mt']), 1, 0, 'L');
                                            $pdf->cell(30, 7, '$ '.mm($pay['mp']), 1, 0, 'L');
                                            $pdf->cell(30, 7, '$ '.mm($data['mt'] - $pay['mp']), 1, 0, 'L');
                                            $pdf->cell(15, 7, montant_restant_pourcent($pay['mp'], $data['mt']).' % ', 1, 0, 'L');
                                            $pdf->Ln(4);
                                            $b[] = $pay['mp'];
                                            $a[] = $data['mt'];
                                        }
                                    }
                                }
                            }
                                
                            $pdf->Ln(3);
                            $pdf->SetFillColor(0,0,0);
                            $pdf->cell(15, 5, decode_fr('Total'), 1, 0, 'L', true);
                            $pdf->cell(70, 5, decode_fr('Total'), 1, 0, 'L');
                            $pdf->cell(30, 5, '$ '.mm(array_sum($a)), 1, 0, 'L');
                            $pdf->cell(30, 5, '$ '.mm(array_sum($b)), 1, 0, 'L');
                            $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                            $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b), array_sum($a)).'%', 1, 0, 'L');
                            $a = array();
                            $b = array();
                            $pdf->Ln(8);
                            $pdf->Ln(9);
                        }
                    }
                }
            }else if($_POST['fac_etudiant'] != "Tous" && $_POST['promotion_etud'] == "Tous"){
                if(empty($_POST['pourcent_debut']) && empty($_POST['pourcent_fin'])){
                    foreach($t_promotion as $pp){
                        $pdf->SetFont('Arial','B',10);
                        $pdf->cell(190, 5, decode_fr("Faculte : ".$ff), 0, 1, 'L');
                        $pdf->cell(190, 5, decode_fr("Promotion : ".$pp), 0, 1, 'L');
                        $pdf->cell(190, 5, decode_fr("Année Acad. : ".$annee_acad_fin), 0, 1, 'L');
                        $pdf->Ln(2);
                        $pdf->cell(15, 5, 'mat', 1, 0, 'L');
                        $pdf->cell(70, 5, 'Noms', 1, 0, 'L');
                        $pdf->cell(30, 5, 'Montant prevu', 1, 0, 'L');
                        $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                        $pdf->cell(30, 5, 'Solde ', 1, 0, 'L');
                        $pdf->cell(15, 5, ' % ', 1, 0, 'L');
                        $pdf->Ln(2);
                        $pdf->SetFont('Arial','',10);

                        $a = array();
                        $b = array();

                        // selection de l etudiant
                        $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT matricule, noms, promotion, fac, annee_academique FROM etudiants_inscrits WHERE fac = ? AND promotion = ? AND annee_academique = ? GROUP BY matricule");
                        $sel_etud->execute(array($ff, $pp, $annee_acad_fin)); 
                        
                        while($data_etu = $sel_etud->fetch()){
                            if($_POST['type_frais'] == "Tous"){
                                $mt = ConnexionBdd::Connecter()->prepare("SELECT SUM(montant) AS mt FROM affectation_frais WHERE matricule  = ? AND promotion = ? AND faculte = ? AND annee_acad = ?");
                                $mt->execute(array($data_etu['matricule'], $data_etu['promotion'], $data_etu['fac'], $data_etu['annee_academique']));

                                while($data = $mt->fetch()){
                                    $payement = ConnexionBdd::Connecter()->prepare("SELECT SUM(montant) AS mp FROM payement WHERE matricule  = ? AND promotion = ? AND faculte = ? AND annee_acad = ?");
                                    $payement->execute(array($data_etu['matricule'], $data_etu['promotion'], $data_etu['fac'], $data_etu['annee_academique']));
                                    while($pay = $payement->fetch()){
                                        $pdf->SetFont('Arial','I',8);
                                        $pdf->Ln(3);
                                        $pdf->cell(15, 7, $data_etu['matricule'], 1, 0, 'L');
                                        $pdf->cell(70, 7, $data_etu['noms'], 1, 0, 'L');
                                        $pdf->cell(30, 7, '$ '.mm($data['mt']), 1, 0, 'L');
                                        $pdf->cell(30, 7, '$ '.mm($pay['mp']), 1, 0, 'L');
                                        $pdf->cell(30, 7, '$ '.mm($data['mt'] - $pay['mp']), 1, 0, 'L');
                                        $pdf->cell(15, 7, montant_restant_pourcent($pay['mp'], $data['mt']).' % ', 1, 0, 'L');
                                        $pdf->Ln(4);
                                        $b[] = $pay['mp'];
                                        $a[] = $data['mt'];
                                    }
                                }
                            }else{
                                $mt = ConnexionBdd::Connecter()->prepare("SELECT type_frais, SUM(montant) AS mt FROM affectation_frais WHERE matricule  = ? AND promotion = ? AND faculte = ? AND type_frais = ? AND annee_acad = ?");
                                $mt->execute(array($data_etu['matricule'], $data_etu['promotion'], $data_etu['fac'], $_POST['type_frais'], $data_etu['annee_academique']));

                                while($data = $mt->fetch()){
                                    $payement = ConnexionBdd::Connecter()->prepare("SELECT SUM(montant) AS mp FROM payement WHERE matricule  = ? AND promotion = ? AND faculte = ? AND type_frais = ? AND annee_acad = ?");
                                    $payement->execute(array($data_etu['matricule'], $data_etu['promotion'], $data_etu['fac'], $data['type_frais'], $data_etu['annee_academique']));
                                    while($pay = $payement->fetch()){
                                        $pdf->SetFont('Arial','I',8);
                                        $pdf->Ln(3);
                                        $pdf->cell(15, 7, $data_etu['matricule'], 1, 0, 'L');
                                        $pdf->cell(70, 7, $data_etu['noms'], 1, 0, 'L');
                                        $pdf->cell(30, 7, '$ '.mm($data['mt']), 1, 0, 'L');
                                        $pdf->cell(30, 7, '$ '.mm($pay['mp']), 1, 0, 'L');
                                        $pdf->cell(30, 7, '$ '.mm($data['mt'] - $pay['mp']), 1, 0, 'L');
                                        $pdf->cell(15, 7, montant_restant_pourcent($pay['mp'], $data['mt']).' % ', 1, 0, 'L');
                                        $pdf->Ln(4);
                                        $b[] = $pay['mp'];
                                        $a[] = $data['mt'];
                                    }
                                }
                            }   
                        }
                            
                        $pdf->Ln(3);
                        $pdf->SetFillColor(0,0,0);
                        $pdf->cell(15, 5, decode_fr('Total'), 1, 0, 'L', true);
                        $pdf->cell(70, 5, decode_fr('Total'), 1, 0, 'L');
                        $pdf->cell(30, 5, '$ '.mm(array_sum($a)), 1, 0, 'L');
                        $pdf->cell(30, 5, '$ '.mm(array_sum($b)), 1, 0, 'L');
                        $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                        $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b), array_sum($a)).'%', 1, 0, 'L');
                        $a = array();
                        $b = array();
                        $pdf->Ln(8);
                        $pdf->Ln(9);
                    }
                }else if(!empty($_POST['pourcent_debut']) && !empty($_POST['pourcent_fin'])){
                    foreach($t_promotion as $pp){
                        $pdf->SetFont('Arial','B',10);
                        $pdf->cell(190, 5, decode_fr("Faculte : ".$ff), 0, 1, 'L');
                        $pdf->cell(190, 5, decode_fr("Promotion : ".$pp), 0, 1, 'L');
                        $pdf->cell(190, 5, decode_fr("Année Acad. : ".$annee_acad_fin), 0, 1, 'L');
                        $pdf->Ln(2);
                        $pdf->cell(15, 5, 'mat', 1, 0, 'L');
                        $pdf->cell(70, 5, 'Noms', 1, 0, 'L');
                        $pdf->cell(30, 5, 'Montant prevu', 1, 0, 'L');
                        $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                        $pdf->cell(30, 5, 'Solde ', 1, 0, 'L');
                        $pdf->cell(15, 5, ' % ', 1, 0, 'L');
                        $pdf->Ln(2);
                        $pdf->SetFont('Arial','',10);

                        $a = array();
                        $b = array();

                        // selection de l etudiant
                        $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT matricule, noms, promotion, fac, annee_academique FROM etudiants_inscrits WHERE fac = ? AND promotion = ? AND annee_academique = ? GROUP BY matricule");
                        $sel_etud->execute(array($ff, $pp, $annee_acad_fin)); 
                        
                        while($data_etu = $sel_etud->fetch()){
                            if($_POST['type_frais'] == "Tous"){
                                $mt = ConnexionBdd::Connecter()->prepare("SELECT SUM(montant) AS mt FROM affectation_frais WHERE matricule  = ? AND promotion = ? AND faculte = ? AND annee_acad = ?");
                                $mt->execute(array($data_etu['matricule'], $data_etu['promotion'], $data_etu['fac'], $data_etu['annee_academique']));

                                while($data = $mt->fetch()){
                                    $payement = ConnexionBdd::Connecter()->prepare("SELECT SUM(montant) AS mp FROM payement WHERE matricule  = ? AND promotion = ? AND faculte = ? AND annee_acad = ? HAVING ROUND(mp * 100 / ?) >= ? AND ROUND(mp * 100 / ?) <= ?");
                                    $payement->execute(array($data_etu['matricule'], $data_etu['promotion'], $data_etu['fac'], $data_etu['annee_academique'], $data['mt'], $_POST['pourcent_debut'], $data['mt'], $_POST['pourcent_fin']));
                                    while($pay = $payement->fetch()){
                                        $pdf->SetFont('Arial','I',8);
                                        $pdf->Ln(3);
                                        $pdf->cell(15, 7, $data_etu['matricule'], 1, 0, 'L');
                                        $pdf->cell(70, 7, $data_etu['noms'], 1, 0, 'L');
                                        $pdf->cell(30, 7, '$ '.mm($data['mt']), 1, 0, 'L');
                                        $pdf->cell(30, 7, '$ '.mm($pay['mp']), 1, 0, 'L');
                                        $pdf->cell(30, 7, '$ '.mm($data['mt'] - $pay['mp']), 1, 0, 'L');
                                        $pdf->cell(15, 7, montant_restant_pourcent($pay['mp'], $data['mt']).' % ', 1, 0, 'L');
                                        $pdf->Ln(4);
                                        $b[] = $pay['mp'];
                                        $a[] = $data['mt'];
                                    }
                                }   
                            }else{
                                $mt = ConnexionBdd::Connecter()->prepare("SELECT type_frais, SUM(montant) AS mt FROM affectation_frais WHERE matricule  = ? AND promotion = ? AND faculte = ? AND type_frais = ? AND annee_acad = ?");
                                $mt->execute(array($data_etu['matricule'], $data_etu['promotion'], $data_etu['fac'], $_POST['type_frais'], $data_etu['annee_academique']));

                                while($data = $mt->fetch()){
                                    $payement = ConnexionBdd::Connecter()->prepare("SELECT SUM(montant) AS mp FROM payement WHERE matricule  = ? AND promotion = ? AND faculte = ? AND type_frais = ? AND annee_acad = ? HAVING ROUND(mp * 100 / ?) >= ? AND ROUND(mp * 100 / ?) <= ?");
                                    $payement->execute(array($data_etu['matricule'], $data_etu['promotion'], $data_etu['fac'], $data['type_frais'], $data_etu['annee_academique'], $data['mt'], $_POST['pourcent_debut'], $data['mt'], $_POST['pourcent_fin']));
                                    while($pay = $payement->fetch()){
                                        $pdf->SetFont('Arial','I',8);
                                        $pdf->Ln(3);
                                        $pdf->cell(15, 7, $data_etu['matricule'], 1, 0, 'L');
                                        $pdf->cell(70, 7, $data_etu['noms'], 1, 0, 'L');
                                        $pdf->cell(30, 7, '$ '.mm($data['mt']), 1, 0, 'L');
                                        $pdf->cell(30, 7, '$ '.mm($pay['mp']), 1, 0, 'L');
                                        $pdf->cell(30, 7, '$ '.mm($data['mt'] - $pay['mp']), 1, 0, 'L');
                                        $pdf->cell(15, 7, montant_restant_pourcent($pay['mp'], $data['mt']).' % ', 1, 0, 'L');
                                        $pdf->Ln(4);
                                        $b[] = $pay['mp'];
                                        $a[] = $data['mt'];
                                    }
                                }
                            }
                        }
                            
                        $pdf->Ln(3);
                        $pdf->SetFillColor(0,0,0);
                        $pdf->cell(15, 5, decode_fr('Total'), 1, 0, 'L', true);
                        $pdf->cell(70, 5, decode_fr('Total'), 1, 0, 'L');
                        $pdf->cell(30, 5, '$ '.mm(array_sum($a)), 1, 0, 'L');
                        $pdf->cell(30, 5, '$ '.mm(array_sum($b)), 1, 0, 'L');
                        $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                        $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b), array_sum($a)).'%', 1, 0, 'L');
                        $a = array();
                        $b = array();
                        $pdf->Ln(8);
                        $pdf->Ln(9);
                    }
                }
            }else if($_POST['fac_etudiant'] != "Tous" && $_POST['promotion_etud'] != "Tous"){
                if(empty($_POST['pourcent_debut']) && empty($_POST['pourcent_fin'])){
                    $pdf->SetFont('Arial','B',10);
                    $pdf->cell(190, 5, decode_fr("Faculte : ".$ff), 0, 1, 'L');
                    $pdf->cell(190, 5, decode_fr("Promotion : ".$pp), 0, 1, 'L');
                    $pdf->cell(190, 5, decode_fr("Année Acad. : ".$annee_acad_fin), 0, 1, 'L');
                    $pdf->Ln(2);
                    $pdf->cell(15, 5, 'mat', 1, 0, 'L');
                    $pdf->cell(70, 5, 'Noms', 1, 0, 'L');
                    $pdf->cell(30, 5, 'Montant prevu', 1, 0, 'L');
                    $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                    $pdf->cell(30, 5, 'Solde ', 1, 0, 'L');
                    $pdf->cell(15, 5, ' % ', 1, 0, 'L');
                    $pdf->Ln(2);
                    $pdf->SetFont('Arial','',10);

                    $a = array();
                    $b = array();

                    // selection de l etudiant
                    $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT matricule, noms, promotion, fac, annee_academique FROM etudiants_inscrits WHERE fac = ? AND promotion = ? AND annee_academique = ? GROUP BY matricule");
                    $sel_etud->execute(array($ff, $pp, $annee_acad_fin)); 
                    
                    while($data_etu = $sel_etud->fetch()){
                        if($_POST['type_frais'] == "Tous"){
                            $mt = ConnexionBdd::Connecter()->prepare("SELECT SUM(montant) AS mt FROM affectation_frais WHERE matricule  = ? AND promotion = ? AND faculte = ? AND annee_acad = ?");
                            $mt->execute(array($data_etu['matricule'], $data_etu['promotion'], $data_etu['fac'], $data_etu['annee_academique']));

                            while($data = $mt->fetch()){
                                $payement = ConnexionBdd::Connecter()->prepare("SELECT SUM(montant) AS mp FROM payement WHERE matricule  = ? AND promotion = ? AND faculte = ? AND annee_acad = ?");
                                $payement->execute(array($data_etu['matricule'], $data_etu['promotion'], $data_etu['fac'], $data_etu['annee_academique']));
                                while($pay = $payement->fetch()){
                                    $pdf->SetFont('Arial','I',8);
                                    $pdf->Ln(3);
                                    $pdf->cell(15, 7, $data_etu['matricule'], 1, 0, 'L');
                                    $pdf->cell(70, 7, $data_etu['noms'], 1, 0, 'L');
                                    $pdf->cell(30, 7, '$ '.mm($data['mt']), 1, 0, 'L');
                                    $pdf->cell(30, 7, '$ '.mm($pay['mp']), 1, 0, 'L');
                                    $pdf->cell(30, 7, '$ '.mm($data['mt'] - $pay['mp']), 1, 0, 'L');
                                    $pdf->cell(15, 7, montant_restant_pourcent($pay['mp'], $data['mt']).' % ', 1, 0, 'L');
                                    $pdf->Ln(4);
                                    $b[] = $pay['mp'];
                                    $a[] = $data['mt'];
                                }
                            }
                        }else{
                            $mt = ConnexionBdd::Connecter()->prepare("SELECT type_frais, SUM(montant) AS mt FROM affectation_frais WHERE matricule  = ? AND promotion = ? AND faculte = ? AND type_frais = ? AND annee_acad = ?");
                            $mt->execute(array($data_etu['matricule'], $data_etu['promotion'], $data_etu['fac'], $_POST['type_frais'], $data_etu['annee_academique']));

                            while($data = $mt->fetch()){
                                $payement = ConnexionBdd::Connecter()->prepare("SELECT SUM(montant) AS mp FROM payement WHERE matricule  = ? AND promotion = ? AND faculte = ? AND type_frais = ? AND annee_acad = ? HAVING mt > 0 ");
                                $payement->execute(array($data_etu['matricule'], $data_etu['promotion'], $data_etu['fac'], $data['type_frais'], $data_etu['annee_academique']));
                                while($pay = $payement->fetch()){
                                    $pdf->SetFont('Arial','I',8);
                                    $pdf->Ln(3);
                                    $pdf->cell(15, 7, $data_etu['matricule'], 1, 0, 'L');
                                    $pdf->cell(70, 7, $data_etu['noms'], 1, 0, 'L');
                                    $pdf->cell(30, 7, '$ '.mm($data['mt']), 1, 0, 'L');
                                    $pdf->cell(30, 7, '$ '.mm($pay['mp']), 1, 0, 'L');
                                    $pdf->cell(30, 7, '$ '.mm($data['mt'] - $pay['mp']), 1, 0, 'L');
                                    $pdf->cell(15, 7, montant_restant_pourcent($pay['mp'], $data['mt']).' % ', 1, 0, 'L');
                                    $pdf->Ln(4);
                                    $b[] = $pay['mp'];
                                    $a[] = $data['mt'];
                                }
                            }
                        }   
                    }
                        
                    $pdf->Ln(3);
                    $pdf->SetFillColor(0,0,0);
                    $pdf->cell(15, 5, decode_fr('Total'), 1, 0, 'L', true);
                    $pdf->cell(70, 5, decode_fr('Total'), 1, 0, 'L');
                    $pdf->cell(30, 5, '$ '.mm(array_sum($a)), 1, 0, 'L');
                    $pdf->cell(30, 5, '$ '.mm(array_sum($b)), 1, 0, 'L');
                    $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                    $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b), array_sum($a)).'%', 1, 0, 'L');
                    $a = array();
                    $b = array();
                    $pdf->Ln(8);
                    $pdf->Ln(9);
                }else if(!empty($_POST['pourcent_debut']) && !empty($_POST['pourcent_fin'])){
                    $pdf->SetFont('Arial','B',10);
                    $pdf->cell(190, 5, decode_fr("Faculte : ".$ff), 0, 1, 'L');
                    $pdf->cell(190, 5, decode_fr("Promotion : ".$pp), 0, 1, 'L');
                    $pdf->cell(190, 5, decode_fr("Année Acad. : ".$annee_acad_fin), 0, 1, 'L');
                    $pdf->Ln(2);
                    $pdf->cell(15, 5, 'mat', 1, 0, 'L');
                    $pdf->cell(70, 5, 'Noms', 1, 0, 'L');
                    $pdf->cell(30, 5, 'Montant prevu', 1, 0, 'L');
                    $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                    $pdf->cell(30, 5, 'Solde ', 1, 0, 'L');
                    $pdf->cell(15, 5, ' % ', 1, 0, 'L');
                    $pdf->Ln(2);
                    $pdf->SetFont('Arial','',10);

                    $a = array();
                    $b = array();

                    // selection de l etudiant
                    $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT matricule, noms, promotion, fac, annee_academique FROM etudiants_inscrits WHERE fac = ? AND promotion = ? AND annee_academique = ? GROUP BY matricule");
                    $sel_etud->execute(array($ff, $pp, $annee_acad_fin)); 
                    
                    while($data_etu = $sel_etud->fetch()){
                        if($_POST['type_frais'] == "Tous"){
                            $mt = ConnexionBdd::Connecter()->prepare("SELECT SUM(montant) AS mt FROM affectation_frais WHERE matricule  = ? AND promotion = ? AND faculte = ? AND annee_acad = ?");
                            $mt->execute(array($data_etu['matricule'], $data_etu['promotion'], $data_etu['fac'], $data_etu['annee_academique']));

                            while($data = $mt->fetch()){
                                $payement = ConnexionBdd::Connecter()->prepare("SELECT SUM(montant) AS mp FROM payement WHERE matricule  = ? AND promotion = ? AND faculte = ? AND annee_acad = ? HAVING ROUND(mp * 100 / ?) >= ? AND ROUND(mp * 100 / ?) <= ?");
                                $payement->execute(array($data_etu['matricule'], $data_etu['promotion'], $data_etu['fac'], $data_etu['annee_academique'], $data['mt'], $_POST['pourcent_debut'], $data['mt'], $_POST['pourcent_fin']));
                                while($pay = $payement->fetch()){
                                    $pdf->SetFont('Arial','I',8);
                                    $pdf->Ln(3);
                                    $pdf->cell(15, 7, $data_etu['matricule'], 1, 0, 'L');
                                    $pdf->cell(70, 7, $data_etu['noms'], 1, 0, 'L');
                                    $pdf->cell(30, 7, '$ '.mm($data['mt']), 1, 0, 'L');
                                    $pdf->cell(30, 7, '$ '.mm($pay['mp']), 1, 0, 'L');
                                    $pdf->cell(30, 7, '$ '.mm($data['mt'] - $pay['mp']), 1, 0, 'L');
                                    $pdf->cell(15, 7, montant_restant_pourcent($pay['mp'], $data['mt']).' % ', 1, 0, 'L');
                                    $pdf->Ln(4);
                                    $b[] = $pay['mp'];
                                    $a[] = $data['mt'];
                                }
                            }
                        }else{
                            $mt = ConnexionBdd::Connecter()->prepare("SELECT type_frais, SUM(montant) AS mt FROM affectation_frais WHERE matricule  = ? AND promotion = ? AND faculte = ? AND type_frais = ? AND annee_acad = ? HAVING mt > 0 ");
                            $mt->execute(array($data_etu['matricule'], $data_etu['promotion'], $data_etu['fac'], $_POST['type_frais'], $data_etu['annee_academique']));

                            while($data = $mt->fetch()){
                                $payement = ConnexionBdd::Connecter()->prepare("SELECT SUM(montant) AS mp FROM payement WHERE matricule  = ? AND promotion = ? AND faculte = ? AND annee_acad = ? AND type_frais = ? HAVING ROUND(mp * 100 / ?) >= ? AND ROUND(mp * 100 / ?) <= ?");
                                $payement->execute(array($data_etu['matricule'], $data_etu['promotion'], $data_etu['fac'], $data_etu['annee_academique'], $data['type_frais'], $data['mt'], $_POST['pourcent_debut'], $data['mt'], $_POST['pourcent_fin']));
                                while($pay = $payement->fetch()){
                                    $pdf->SetFont('Arial','I',8);
                                    $pdf->Ln(3);
                                    $pdf->cell(15, 7, $data_etu['matricule'], 1, 0, 'L');
                                    $pdf->cell(70, 7, $data_etu['noms'], 1, 0, 'L');
                                    $pdf->cell(30, 7, '$ '.mm($data['mt']), 1, 0, 'L');
                                    $pdf->cell(30, 7, '$ '.mm($pay['mp']), 1, 0, 'L');
                                    $pdf->cell(30, 7, '$ '.mm($data['mt'] - $pay['mp']), 1, 0, 'L');
                                    $pdf->cell(15, 7, montant_restant_pourcent($pay['mp'], $data['mt']).' % ', 1, 0, 'L');
                                    $pdf->Ln(4);
                                    $b[] = $pay['mp'];
                                    $a[] = $data['mt'];
                                }
                            }
                        }   
                    }
                        
                    $pdf->Ln(3);
                    $pdf->SetFillColor(0,0,0);
                    $pdf->cell(15, 5, decode_fr('Total'), 1, 0, 'L', true);
                    $pdf->cell(70, 5, decode_fr('Total'), 1, 0, 'L');
                    $pdf->cell(30, 5, '$ '.mm(array_sum($a)), 1, 0, 'L');
                    $pdf->cell(30, 5, '$ '.mm(array_sum($b)), 1, 0, 'L');
                    $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                    $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b), array_sum($a)).'%', 1, 0, 'L');
                    $a = array();
                    $b = array();
                    $pdf->Ln(8);
                    $pdf->Ln(9);
                }
            }else if($_POST['fac_etudiant'] = "Tous" && $_POST['promotion_etud'] !="Tous"){
                if(empty($_POST['pourcent_debut']) && empty($_POST['pourcent_fin'])){
                    foreach($t_fac as $ff){
                        $pdf->SetFont('Arial','B',10);
                        $pdf->cell(190, 5, decode_fr("Faculte : ".$ff), 0, 1, 'L');
                        $pdf->cell(190, 5, decode_fr("Promotion : ".$pp), 0, 1, 'L');
                        $pdf->cell(190, 5, decode_fr("Année Acad. : ".$annee_acad_fin), 0, 1, 'L');
                        $pdf->Ln(2);
                        $pdf->cell(15, 5, 'mat', 1, 0, 'L');
                        $pdf->cell(70, 5, 'Noms', 1, 0, 'L');
                        $pdf->cell(30, 5, 'Montant prevu', 1, 0, 'L');
                        $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                        $pdf->cell(30, 5, 'Solde ', 1, 0, 'L');
                        $pdf->cell(15, 5, ' % ', 1, 0, 'L');
                        $pdf->Ln(2);
                        $pdf->SetFont('Arial','',10);

                        $a = array();
                        $b = array();

                        // selection de l etudiant
                        $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT matricule, noms, promotion, fac, annee_academique FROM etudiants_inscrits WHERE fac = ? AND promotion = ? AND annee_academique = ? GROUP BY matricule");
                        $sel_etud->execute(array($ff, $pp, $annee_acad_fin)); 
                        
                        while($data_etu = $sel_etud->fetch()){
                            if($_POST['type_frais'] == "Tous"){
                                $mt = ConnexionBdd::Connecter()->prepare("SELECT SUM(montant) AS mt FROM affectation_frais WHERE matricule  = ? AND promotion = ? AND faculte = ? AND annee_acad = ?");
                                $mt->execute(array($data_etu['matricule'], $data_etu['promotion'], $data_etu['fac'], $data_etu['annee_academique']));

                                while($data = $mt->fetch()){
                                    $payement = ConnexionBdd::Connecter()->prepare("SELECT SUM(montant) AS mp FROM payement WHERE matricule  = ? AND promotion = ? AND faculte = ? AND annee_acad = ?");
                                    $payement->execute(array($data_etu['matricule'], $data_etu['promotion'], $data_etu['fac'], $data_etu['annee_academique']));
                                    while($pay = $payement->fetch()){
                                        $pdf->SetFont('Arial','I',8);
                                        $pdf->Ln(3);
                                        $pdf->cell(15, 7, $data_etu['matricule'], 1, 0, 'L');
                                        $pdf->cell(70, 7, $data_etu['noms'], 1, 0, 'L');
                                        $pdf->cell(30, 7, '$ '.mm($data['mt']), 1, 0, 'L');
                                        $pdf->cell(30, 7, '$ '.mm($pay['mp']), 1, 0, 'L');
                                        $pdf->cell(30, 7, '$ '.mm($data['mt'] - $pay['mp']), 1, 0, 'L');
                                        $pdf->cell(15, 7, montant_restant_pourcent($pay['mp'], $data['mt']).' % ', 1, 0, 'L');
                                        $pdf->Ln(4);
                                        $b[] = $pay['mp'];
                                        $a[] = $data['mt'];
                                    }
                                }   
                            }else{
                                $mt = ConnexionBdd::Connecter()->prepare("SELECT type_frais, SUM(montant) AS mt FROM affectation_frais WHERE matricule  = ? AND promotion = ? AND faculte = ? AND type_frais = ? AND annee_acad = ? HAVING mt > 0 ");
                                $mt->execute(array($data_etu['matricule'], $data_etu['promotion'], $data_etu['fac'], $_POST['type_frais'], $data_etu['annee_academique']));

                                while($data = $mt->fetch()){
                                    $payement = ConnexionBdd::Connecter()->prepare("SELECT SUM(montant) AS mp FROM payement WHERE matricule  = ? AND promotion = ? AND faculte = ? AND type_frais = ? AND annee_acad = ?");
                                    $payement->execute(array($data_etu['matricule'], $data_etu['promotion'], $data_etu['fac'], $data['type_frais'], $data_etu['annee_academique']));
                                    while($pay = $payement->fetch()){
                                        $pdf->SetFont('Arial','I',8);
                                        $pdf->Ln(3);
                                        $pdf->cell(15, 7, $data_etu['matricule'], 1, 0, 'L');
                                        $pdf->cell(70, 7, $data_etu['noms'], 1, 0, 'L');
                                        $pdf->cell(30, 7, '$ '.mm($data['mt']), 1, 0, 'L');
                                        $pdf->cell(30, 7, '$ '.mm($pay['mp']), 1, 0, 'L');
                                        $pdf->cell(30, 7, '$ '.mm($data['mt'] - $pay['mp']), 1, 0, 'L');
                                        $pdf->cell(15, 7, montant_restant_pourcent($pay['mp'], $data['mt']).' % ', 1, 0, 'L');
                                        $pdf->Ln(4);
                                        $b[] = $pay['mp'];
                                        $a[] = $data['mt'];
                                    }
                                }
                            }
                        }
                            
                        $pdf->Ln(3);
                        $pdf->SetFillColor(0,0,0);
                        $pdf->cell(15, 5, decode_fr('Total'), 1, 0, 'L', true);
                        $pdf->cell(70, 5, decode_fr('Total'), 1, 0, 'L');
                        $pdf->cell(30, 5, '$ '.mm(array_sum($a)), 1, 0, 'L');
                        $pdf->cell(30, 5, '$ '.mm(array_sum($b)), 1, 0, 'L');
                        $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                        $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b), array_sum($a)).'%', 1, 0, 'L');
                        $a = array();
                        $b = array();
                        $pdf->Ln(8);
                        $pdf->Ln(9);
                    }
                }else if(!empty($_POST['pourcent_debut']) && !empty($_POST['pourcent_fin'])){
                    foreach($t_fac as $ff){
                        $pdf->SetFont('Arial','B',10);
                        $pdf->cell(190, 5, decode_fr("Faculte : ".$ff), 0, 1, 'L');
                        $pdf->cell(190, 5, decode_fr("Promotion : ".$pp), 0, 1, 'L');
                        $pdf->cell(190, 5, decode_fr("Année Acad. : ".$annee_acad_fin), 0, 1, 'L');
                        $pdf->Ln(2);
                        $pdf->cell(15, 5, 'mat', 1, 0, 'L');
                        $pdf->cell(70, 5, 'Noms', 1, 0, 'L');
                        $pdf->cell(30, 5, 'Montant prevu', 1, 0, 'L');
                        $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                        $pdf->cell(30, 5, 'Solde ', 1, 0, 'L');
                        $pdf->cell(15, 5, ' % ', 1, 0, 'L');
                        $pdf->Ln(2);
                        $pdf->SetFont('Arial','',10);

                        $a = array();
                        $b = array();

                        // selection de l etudiant
                        $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT matricule, noms, promotion, fac, annee_academique FROM etudiants_inscrits WHERE fac = ? AND promotion = ? AND annee_academique = ? GROUP BY matricule");
                        $sel_etud->execute(array($ff, $pp, $annee_acad_fin)); 
                        
                        while($data_etu = $sel_etud->fetch()){
                            if($_POST['type_frais'] == "Tous"){
                                $mt = ConnexionBdd::Connecter()->prepare("SELECT SUM(montant) AS mt FROM affectation_frais WHERE matricule  = ? AND promotion = ? AND faculte = ? AND annee_acad = ?");
                                $mt->execute(array($data_etu['matricule'], $data_etu['promotion'], $data_etu['fac'], $data_etu['annee_academique']));

                                while($data = $mt->fetch()){
                                    $payement = ConnexionBdd::Connecter()->prepare("SELECT SUM(montant) AS mp FROM payement WHERE matricule  = ? AND promotion = ? AND faculte = ? AND annee_acad = ? HAVING ROUND(mp * 100 / ?) >= ? AND ROUND(mp * 100 / ?) <= ?");
                                    $payement->execute(array($data_etu['matricule'], $data_etu['promotion'], $data_etu['fac'], $data_etu['annee_academique'], $data['mt'], $_POST['pourcent_debut'], $data['mt'], $_POST['pourcent_fin']));
                                    while($pay = $payement->fetch()){
                                        $pdf->SetFont('Arial','I',8);
                                        $pdf->Ln(3);
                                        $pdf->cell(15, 7, $data_etu['matricule'], 1, 0, 'L');
                                        $pdf->cell(70, 7, $data_etu['noms'], 1, 0, 'L');
                                        $pdf->cell(30, 7, '$ '.mm($data['mt']), 1, 0, 'L');
                                        $pdf->cell(30, 7, '$ '.mm($pay['mp']), 1, 0, 'L');
                                        $pdf->cell(30, 7, '$ '.mm($data['mt'] - $pay['mp']), 1, 0, 'L');
                                        $pdf->cell(15, 7, montant_restant_pourcent($pay['mp'], $data['mt']).' % ', 1, 0, 'L');
                                        $pdf->Ln(4);
                                        $b[] = $pay['mp'];
                                        $a[] = $data['mt'];
                                    }
                                }
                            }else{
                                $mt = ConnexionBdd::Connecter()->prepare("SELECT type_frais, SUM(montant) AS mt FROM affectation_frais WHERE matricule  = ? AND promotion = ? AND faculte = ? AND type-Frais = ? AND annee_acad = ? HAVING mt > 0 ");
                                $mt->execute(array($data_etu['matricule'], $data_etu['promotion'], $data_etu['fac'], $_POST['type_frais'], $data_etu['annee_academique']));

                                while($data = $mt->fetch()){
                                    $payement = ConnexionBdd::Connecter()->prepare("SELECT SUM(montant) AS mp FROM payement WHERE matricule  = ? AND promotion = ? AND faculte = ? AND type_frais = ? AND annee_acad = ? HAVING ROUND(mp * 100 / ?) >= ? AND ROUND(mp * 100 / ?) <= ?");
                                    $payement->execute(array($data_etu['matricule'], $data_etu['promotion'], $data_etu['fac'], $data['type_frais'], $data_etu['annee_academique'], $data['mt'], $_POST['pourcent_debut'], $data['mt'], $_POST['pourcent_fin']));
                                    while($pay = $payement->fetch()){
                                        $pdf->SetFont('Arial','I',8);
                                        $pdf->Ln(3);
                                        $pdf->cell(15, 7, $data_etu['matricule'], 1, 0, 'L');
                                        $pdf->cell(70, 7, $data_etu['noms'], 1, 0, 'L');
                                        $pdf->cell(30, 7, '$ '.mm($data['mt']), 1, 0, 'L');
                                        $pdf->cell(30, 7, '$ '.mm($pay['mp']), 1, 0, 'L');
                                        $pdf->cell(30, 7, '$ '.mm($data['mt'] - $pay['mp']), 1, 0, 'L');
                                        $pdf->cell(15, 7, montant_restant_pourcent($pay['mp'], $data['mt']).' % ', 1, 0, 'L');
                                        $pdf->Ln(4);
                                        $b[] = $pay['mp'];
                                        $a[] = $data['mt'];
                                    }
                                }
                            }   
                        }
                            
                        $pdf->Ln(3);
                        $pdf->SetFillColor(0,0,0);
                        $pdf->cell(15, 5, decode_fr('Total'), 1, 0, 'L', true);
                        $pdf->cell(70, 5, decode_fr('Total'), 1, 0, 'L');
                        $pdf->cell(30, 5, '$ '.mm(array_sum($a)), 1, 0, 'L');
                        $pdf->cell(30, 5, '$ '.mm(array_sum($b)), 1, 0, 'L');
                        $pdf->cell(30, 5, '$ '.mm(array_sum($a) - array_sum($b)), 1, 0, 'L');
                        $pdf->cell(15, 5, montant_restant_pourcent(array_sum($b), array_sum($a)).'%', 1, 0, 'L');
                        $a = array();
                        $b = array();
                        $pdf->Ln(8);
                        $pdf->Ln(9);
                    }
                }
            }
        }
        $pdf->Ln(15);
        $pdf->SetFont('Arial','',10);
	    $pdf->cell(300,20, decode_fr('par : '.$_SESSION['data']['noms'].'; le '.date('d/M/Y')),0,1,'C');
        $pdf->Output();
    }
?>
<!doctype html>
<html lang="fr">
    <head>
        <title>Frais Rapports</title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="Justin Micah" content="">
        <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
        <link rel="shortcut icon" href="../../images/ispt_kin.png" type="image/x-icon">
        <link href="css/sb-admin-2.min.css" rel="stylesheet">
        <style>
            .form-control{
                padding:0px !important;
                height: 100% !important;
                
                /* width: 100%; */
            }
            label, .link>button{
                font-size: 90%;
                border:none;
            }
            input[type="number"]{
                width:80px;
            }

            input[type="date"]{
                width:140px;
            }
        </style>
    </head>
    <body id="page-top">
        <div id="wrapper">
            <?php require_once './menu.php'; ?>
            <!-- End Sidebar -->
            <div id="content-wrapper" class="d-flex flex-column">
                <div id="content mt-4">
                    <!-- menu user -->
                    <?php require_once 'menu_user.php'; ?>
                    <!-- main Content -->
                    <div class="container-fluid" style="margin-top: -15px;">
                        <div class="card shadow ml-3 mt-2 m-0 p-0" style="width:42rem;">
                            <div class="card-body  ml-3 mr-3 mt-1 mb-2 p-0">
                                <h4 style="text-transform: capitalize;" class="text-center h6"><?=$p?></h4>
                                <form action="" method="POST" class="form-login" style="width:40rem;">
                                    <div class="card m-0 p-0">
                                        <div class="card-body p-2 m-1">
                                            <div class="">
                                                <div class="row mt-1" >
                                                    <div class="col-sm-12 col-md-4 col-lg-4">
                                                        <label for="">Faculté</label>
                                                    </div>
                                                    <div class="col-sm-12 col-md-9 col-lg-7">
                                                        <select class="form-control" name="fac_etudiant" id="fac_etudiant">
                                                            <option value="Tous" selected>Tous</option>
                                                            <?php
                                                                $sql = "SELECT DISTINCT section FROM sections";
                                                                $state = ConnexionBdd::Connecter()->query($sql);
                                                                while($d = $state->fetch()){
                                                                    echo' 
                                                                        <option value="'.$d['section'].'">'.$d['section'].'</option>';
                                                                }
                                                            ?>
                                                        </select>
                                                    </div>                                  
                                                </div>

                                                <div class="row mt-1" >
                                                    <div class="col-sm-12 col-md-4 col-lg-4">
                                                        <label for="">Promotion</label>
                                                    </div>
                                                    <div class="col-sm-12 col-md-9 col-lg-7">
                                                        <select class="form-control" name="promotion_etud" id="promotion_etud">
                                                            <option value="Tous">Tous</option>
                                                            <?php
                                                                $sql = "SELECT DISTINCT promotion FROM etudiants_inscrits";
                                                                $state = ConnexionBdd::Connecter()->query($sql);
                                                                while($d = $state->fetch()){
                                                                    echo' 
                                                                        <option value="'.$d['promotion'].'">'.$d['promotion'].'</option>';
                                                                }
                                                            ?>
                                                        </select>
                                                    </div>                                  
                                                </div>

                                                <div class="row mt-1" >
                                                    <div class="col-sm-12 col-md-5 col-lg-4">
                                                        <label for="">Année Académique(début)</label>
                                                    </div>
                                                    <div class="col-sm-12 col-md-9 col-lg-7">
                                                        <select class="form-control" name="annee_acad_deb" id="annee_acad_deb">
                                                            <?php
                                                                $sql = "SELECT * FROM annee_acad ORDER BY annee_acad DESC";
                                                                $state = ConnexionBdd::Connecter()->query($sql);
                                                                while($d = $state->fetch()){
                                                                    echo' 
                                                                        <option value="'.$d['id_annee'].'">'.$d['annee_acad'].'</option>';
                                                                }
                                                            ?>
                                                        </select>
                                                    </div>                                  
                                                </div>

                                                <div class="row mt-1" >
                                                    <div class="col-sm-12 col-md-5 col-lg-4">
                                                        <label for="">Année Académique(fin)</label>
                                                    </div>
                                                    <div class="col-sm-12 col-md-9 col-lg-7">
                                                        <select class="form-control" name="annee_acad_fin" id="annee_acad_fin">
                                                            <?php
                                                                $sql = "SELECT * FROM annee_acad ORDER BY annee_acad DESC";
                                                                $state = ConnexionBdd::Connecter()->query($sql);
                                                                while($d = $state->fetch()){
                                                                    echo' 
                                                                        <option value="'.$d['id_annee'].'">'.$d['annee_acad'].'</option>';
                                                                }
                                                            ?>
                                                        </select>
                                                    </div>                                  
                                                </div>

                                                <div class="row mt-1" >
                                                    <div class="col-sm-12 col-md-4 col-lg-4">
                                                        <label for="">Type de frais</label>
                                                    </div>
                                                    <div class="col-sm-12 col-md-9 col-lg-7">
                                                        <select class="form-control" name="type_frais" id="type_frais">
                                                            <option value="Tous">Tous</option>
                                                            <?php
                                                                //le type de frais
                                                                $sql = "SELECT DISTINCT prevision_frais.type_frais FROM affectation_frais LEFT JOIN prevision_frais ON affectation_frais.id_frais = prevision_frais.id_frais";
                                                                $state = ConnexionBdd::Connecter()->query($sql);
                                                                while($d = $state->fetch()){
                                                                    echo' 
                                                                        <option value="'.$d['type_frais'].'">'.decode_fr($d['type_frais']).'</option>';
                                                                }
                                                            ?>
                                                        </select>
                                                    </div>                                  
                                                </div>
                                                <div class="row mt-1" >
                                                    <div class="col-sm-12 col-md-4 col-lg-4">
                                                        <label for="">Intervale de payement</label>
                                                    </div>
                                                    <div class="col-sm-12 col-md-9 col-lg-7">
                                                        <div class="">
                                                            De <input type="number" name="pourcent_debut" id="pourcent_debut" placeholder="min"  min="1" value=""> 
                                                            % jusqu'à <input type="number" name="pourcent_fin" id="pourcent_fin" placeholder="max"  min="0"> 
                                                        </div>
                                                    </div>                                  
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex flex flex-column link ml-2">
                                            <label class=""><b>Fichiers des paiements</b></label>
                                            <!-- Synthèse par année académique -->
                                            <button type="submit" name="acad_f" class="btn-link text-left">Synthèse par année académique</button>
                                            <!--  -->
                                            <!-- systhese par faculte -->
                                            <button type="submit" id="btn_fac" class="btn-link text-left" name="btn_fac" value="btn_fac">Synthèse par faculté</button>
                                            <!--  -->
                                            <button type="submit" id="btn_promotion" class="btn-link text-left" name="btn_promotion" value="btn_promotion">Synthèse par promotion</button>
                                            <!--  -->
                                            <button type="submit" id="btn_etudiant" class="btn-link text-left" name="btn_etudiant" value="btn_etudiant">Synthèse par étudiant</button>
                                            <!--  -->
                                            <button type="submit" id="btn_detail_etud" class="btn-link text-left" name="btn_detail_etud" value="btn_detail_etud">Détail de payement par étudiant</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- footer -->
            <?php include './footer.php';?>
            </div>
        </div>
        <script src="../../js/jquery-3.6.0.min.js"></script>
        <!-- fenetre modal pour la deconnexion-->
        <?php include_once './modal_decon.php';?>

        <script type="text/javascript">
            $("#pourcent_debut").change(function (e) { 
                e.preventDefault();
                if($("#pourcent_debut").val().length > 0){
                    $("#pourcent_fin").attr('required', true);
                }else{
                    $("#pourcent_fin").removeAttr('required');
                }
            });

            $("#pourcent_debut").keyup(function (e) { 
                if($("#pourcent_debut").val().length > 0){
                    $("#pourcent_fin").attr('required', true);
                }else{
                    $("#pourcent_fin").removeAttr('required');
                }
            });
            // pour le % max
            $("#pourcent_fin").change(function (e) { 
                e.preventDefault();
                if($("#pourcent_fin").val().length > 0){
                    $("#pourcent_debut").attr('required', true);
                }else{
                    $("#pourcent_debut").removeAttr('required');
                }
            });

            $("#pourcent_fin").keyup(function (e) { 
                if($("#pourcent_fin").val().length > 0){
                    $("#pourcent_debut").attr('required', true);
                }else{
                    $("#pourcent_debut").removeAttr('required');
                }
            });

            // pour les dates debit
            $("#date_debit").change(function (e) { 
                e.preventDefault();
                if($("#date_debit").val().length > 0){
                    $("#date_fin").attr('required', true);
                }else{
                    $("#date_fin").removeAttr('required');
                }
            });

            $("#date_debit").keyup(function (e) { 
                if($("#date_debit").val().length > 0){
                    $("#date_fin").attr('required', true);
                }else{
                    $("#date_fin").removeAttr('required');
                }
            });

            // pour les dates
            $("#date_fin").change(function (e) { 
                e.preventDefault();
                if($("#date_fin").val().length > 0){
                    $("#date_debit").attr('required', true);
                }else{
                    $("#date_debit").removeAttr('required');
                }
            });

            $("#date_fin").keyup(function (e) { 
                if($("#date_fin").val().length > 1){
                    $("#date_debit").attr('required', true);
                }else{
                    $("#date_debit").removeAttr('required');
                }
            });
        </script>

        <script>
            $("#pourcent_debut").keyup(function (e) { 
                if($("#pourcent_debut").val() <= 0){
                    $("#pourcent_debut").val('1')
                }
            });

            $("#pourcent_debut").change(function (e) { 
                e.preventDefault();
                if($("#pourcent_debut").val() <= 0){
                    $("#pourcent_debut").val('1')
                }
            });
        </script>

        <script type="text/javascript">
            // une fonction
            getMatricule();

            $("#annee_acad_deb").change(function (e) { 
                e.preventDefault();
                getMatricule();
            });

            $("#fac_etudiant").change(function (e) { 
                e.preventDefault();
                getMatricule();
            });

            function getMatricule(){
                const data = {
                    a:$("#annee_acad_deb option:selected").text(),
                    f:$("#fac_etudiant option:selected").text()
                };
                $.ajax({
                    type: "GET",
                    url: "../../includes/promotion_.php",
                    data: data,
                    success: function (data) {
                        if(data !=""){
                            $("#promotion_etud").empty();
                            $("#promotion_etud").html('<option value="Tous">Tous</option>');
                            $("#promotion_etud").append(data);
                            // $("#error").html("");
                        }else{
                            // $("#error").html("");
                            // $("#error").html("Désolé, Aucun résultat trouvé...");
                        }
                    },
                    error: function(e){
                        alert("Erreur de connexion...");
                        $("#error").html("");
                        $("#error").html("Erreur de connexion...");
                    }
                });
            }
        </script>
    </body>
</html>