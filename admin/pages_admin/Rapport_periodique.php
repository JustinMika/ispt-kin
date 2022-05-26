<?php
    // header()
    session_start();
    require_once '../../includes/ConnexionBdd.class.php';
    require_once '../../includes/verification.class.php';
    //verification des sessions
    require_once './sessions.php';
    require './fpdf/fpdf.php';
    $p = "Rapport Périodique";

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
        $pdf->cell(60, 5, decode_fr('Année Academique Debut : '.verify(get_annee($_POST['annee_acad_deb']))), 0, 1, 'L');
        $pdf->cell(60, 5, decode_fr('Année Academique  Fin: '.verify(get_annee($_POST['annee_acad_fin']))), 0, 1, 'L');
        $pdf->cell(60, 5, 'Promotion                 : '.decode_fr(verify($_POST['promotion_etud'])), 0, 1, 'L');
        $pdf->cell(60, 5, decode_fr('Faculté                     : '.verify(get_fac($_POST['fac_etudiant']))), 0, 1, 'L');
        $pdf->cell(60, 5, 'Type de frais             : '.decode_fr(verify($_POST['type_frais'])), 0, 1, 'L');
        $pdf->cell(60, 5, decode_fr('Date de coupure       : '.date_cou($_POST['date_debit'], $_POST['date_fin'])), 0, 1, 'L');
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
        $ff = $_POST['fac_etudiant'];
        $pp = $_POST['promotion_etud'];
        $dd = $_POST['date_debit'];
        $df = $_POST['date_fin'];
        // die($df);
        if($annee_acad_deb == $annee_acad_fin){
            $t_fac = array();
            $t_promotion = array();
            $ff = $_POST['fac_etudiant'];
            $pp = $_POST['promotion_etud'];

            // selection des toutes les facultes
            $f = ConnexionBdd::Connecter()->query("SELECT DISTINCT etudiants_inscrits.id_section, sections.section, annee_acad.id_annee FROM etudiants_inscrits LEFT JOIN sections ON etudiants_inscrits.id_section = sections.id_section LEFT JOIN annee_acad ON etudiants_inscrits.id_annee = annee_acad.id_annee WHERE etudiants_inscrits.id_annee = {$annee_acad_fin}");
            while($df = $f->fetch()){
                $t_fac[] = $df['section'];
            }

            if($_POST['fac_etudiant'] == "Tous" && $_POST['promotion_etud'] == "Tous"){
                foreach($t_fac as $ff){
                    $f = ConnexionBdd::Connecter()->query("SELECT DISTINCT etudiants_inscrits.promotion, options.id_option, sections.section FROM etudiants_inscrits LEFT JOIN options ON etudiants_inscrits.id_option = options.id_option LEFT JOIN sections ON etudiants_inscrits.id_section = sections.id_section WHERE sections.section = '{$ff}' GROUP BY etudiants_inscrits.promotion");
                    while($df = $f->fetch()){
                        $t_promotion[] = $df['promotion'];
                    }
                    foreach($t_promotion as $pp){
                        if(!empty($_POST['date_debit']) && !empty($_POST['date_fin'])){
                            // la tables etudiants
                            // selection des l etudiant
                            $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT id_annee FROM etudiants_inscrits WHERE id_annee = ? GROUP BY id_annee");
                            $sel_etud->execute(array($annee_acad_fin));
                
                            if($sel_etud->rowCount() > 0){
                                // while ($data_student = $sel_etud->fetch()){
                                    $pdf->Ln(5);
                                    $pdf->cell(190, 5, decode_fr('Annee Acad. : '.get_annee($annee_acad_fin)), 0, 1, 'L');
                                    $pdf->cell(190, 5, decode_fr('Section : '.$ff), 0, 1, 'L');
                                    $pdf->cell(190, 5, decode_fr('Promotion : '.$pp), 0, 1, 'L');
                                    $pdf->Ln(2);
                                    $pdf->cell(60, 5, 'Type frais', 1, 0, 'L');
                                    $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                                    $pdf->Ln(1);
                
                                    // tableau
                                    $a = array();
                                    $b = array();
                
                                    if($_POST['type_frais'] != "Tous"){
                                        $sql_2 = "SELECT
                                                payement.id_payement,
                                                SUM(payement.montant) AS mp,
                                                payement.id_frais,
                                                prevision_frais.type_frais,
                                                annee_acad.annee_acad,
                                                sections.section,
                                                options.id_option,
                                                options.option_
                                            FROM
                                                payement
                                            LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                            LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                            LEFT JOIN sections ON payement.id_section = sections.id_section
                                            LEFT JOIN options ON payement.id_section = options.id_option
                                            WHERE prevision_frais.type_frais = ? AND annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ? AND payement.date_payement BETWEEN ? AND ?  group by prevision_frais.type_frais";
                                        $sql_2_a = array($_POST['type_frais'], $annee_acad_deb, $_POST['fac_etudiant'], $pp, $dd, $_POST['date_fin']);
                                        $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                        $sql_2->execute($sql_2_a);
                
                                        while($d = $sql_2->fetch()){
                                            $pdf->SetFont('Arial','i',8);
                                            $pdf->Ln(4);
                                            $pdf->cell(60, 5, utf8_decode(decode_fr($d['type_frais'])), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                            $pdf->Ln(1);
                                            $b[] = $d['mp'];
                                        }
                                        $pdf->Ln(4);
                                        $pdf->SetFont('Arial','',9);
                                        $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                        $pdf->Ln(2);
                                        $b = array();
                                    } else {
                                        $sql_2 = "SELECT
                                            payement.id_payement,
                                            SUM(payement.montant) AS mp,
                                            payement.id_frais,
                                            prevision_frais.type_frais,
                                            annee_acad.annee_acad,
                                            sections.section,
                                            options.id_option,
                                            options.option_
                                        FROM
                                            payement
                                        LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                        LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                        LEFT JOIN sections ON payement.id_section = sections.id_section
                                        LEFT JOIN options ON payement.id_section = options.id_option
                                        WHERE annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ? AND payement.date_payement BETWEEN ? AND ? GROUP BY prevision_frais.type_frais";
                                        // die($dd);
                                        $sql_2_a = array($annee_acad_deb, trim($ff), $pp, $dd, $_POST['date_fin']);
                                        $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                        $sql_2->execute($sql_2_a);
                
                                        while($d = $sql_2->fetch()){
                                            $pdf->SetFont('Arial','i',8);
                                            $pdf->Ln(4);
                                            $pdf->cell(60, 5, utf8_decode(decode_fr($d['type_frais'])), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                            $pdf->Ln(1);
                                            $a[] = $d['mp'];
                                        }
                
                                        $pdf->Ln(4);
                                        $pdf->SetFont('Arial','',9);
                                        $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                        // $pdf->cell(15, 5, '#', 1, 0, 'L');
                                        $pdf->Ln(2);
                                        $a = array();
                                        $b = array();
                                    }
                                // }
                            }
                        }else if(empty($_POST['date_debit']) && empty($_POST['date_fin'])){
                            // selection des l etudiant
                            $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT id_annee FROM etudiants_inscrits WHERE id_annee = ? GROUP BY id_annee");
                            $sel_etud->execute(array($annee_acad_fin));
                
                            if($sel_etud->rowCount() > 0){
                                while ($data_student = $sel_etud->fetch()){
                                    $pdf->Ln(5);
                                    $pdf->cell(190, 5, decode_fr('Annee Acad. : '.get_annee($annee_acad_fin)), 0, 1, 'L');
                                    $pdf->cell(190, 5, decode_fr('Section : '.$ff), 0, 1, 'L');
                                    $pdf->cell(190, 5, decode_fr('Promotion : '.$pp), 0, 1, 'L');
                                    $pdf->Ln(2);
                                    $pdf->cell(60, 5, 'Type frais', 1, 0, 'L');
                                    $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                                    $pdf->Ln(1);
                
                                    // tableau
                                    $a = array();
                                    $b = array();
                
                                    if($_POST['type_frais'] != "Tous"){
                                        $sql_2 = "SELECT
                                                payement.id_payement,
                                                SUM(payement.montant) AS mp,
                                                payement.id_frais,
                                                prevision_frais.type_frais,
                                                annee_acad.annee_acad,
                                                sections.section,
                                                options.id_option,
                                                options.option_
                                            FROM
                                                payement
                                            LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                            LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                            LEFT JOIN sections ON payement.id_section = sections.id_section
                                            LEFT JOIN options ON payement.id_section = options.id_option
                                            WHERE prevision_frais.type_frais = ? AND annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ? group by prevision_frais.type_frais";
                                        $sql_2_a = array($_POST['type_frais'], $annee_acad_deb, $_POST['fac_etudiant'], $pp);
                                        $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                        $sql_2->execute($sql_2_a);
                
                                        while($d = $sql_2->fetch()){
                                            $pdf->SetFont('Arial','i',8);
                                            $pdf->Ln(4);
                                            $pdf->cell(60, 5, utf8_decode(decode_fr($d['type_frais'])), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                            $pdf->Ln(1);
                                            $b[] = $d['mp'];
                                        }
                                        $pdf->Ln(4);
                                        $pdf->SetFont('Arial','',9);
                                        $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                        $pdf->Ln(2);
                                        $b = array();
                                    } else {
                                        $sql_2 = "SELECT
                                            payement.id_payement,
                                            SUM(payement.montant) AS mp,
                                            payement.id_frais,
                                            prevision_frais.type_frais,
                                            annee_acad.annee_acad,
                                            sections.section,
                                            options.id_option,
                                            options.option_
                                        FROM
                                            payement
                                        LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                        LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                        LEFT JOIN sections ON payement.id_section = sections.id_section
                                        LEFT JOIN options ON payement.id_section = options.id_option
                                        WHERE annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ? group by prevision_frais.type_frais";
                                        // die($pp);
                                        $sql_2_a = array($annee_acad_deb, trim($ff), $pp);
                                        $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                        $sql_2->execute($sql_2_a);
                
                                        while($d = $sql_2->fetch()){
                                            $pdf->SetFont('Arial','i',8);
                                            $pdf->Ln(4);
                                            $pdf->cell(60, 5, utf8_decode(decode_fr($d['type_frais'])), 1, 0, 'L');
                                            $pdf->cell(30, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                            $pdf->Ln(1);
                                            $a[] = $d['mp'];
                                        }
                
                                        $pdf->Ln(4);
                                        $pdf->SetFont('Arial','',9);
                                        $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                        // $pdf->cell(15, 5, '#', 1, 0, 'L');
                                        $pdf->Ln(2);
                                        $a = array();
                                        $b = array();
                                    }
                                }
                            }
                        }
                    }
                }
            }else if($_POST['fac_etudiant'] != "Tous" && $_POST['promotion_etud'] == "Tous"){
                $f = ConnexionBdd::Connecter()->query("SELECT DISTINCT etudiants_inscrits.promotion, options.id_option, sections.section FROM etudiants_inscrits LEFT JOIN options ON etudiants_inscrits.id_option = options.id_option LEFT JOIN sections ON etudiants_inscrits.id_section = sections.id_section WHERE sections.section = '{$_POST['fac_etudiant']}' GROUP BY etudiants_inscrits.promotion");
                while($df = $f->fetch()){
                    $t_promotion[] = $df['promotion'];
                }
                foreach($t_promotion as $pp){
                    if(!empty($_POST['date_debit']) && !empty($_POST['date_fin'])){
                        // la tables etudiants
                        // selection des l etudiant
                        $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT id_annee FROM etudiants_inscrits WHERE id_annee = ? GROUP BY id_annee");
                        $sel_etud->execute(array($annee_acad_fin));
            
                        if($sel_etud->rowCount() > 0){
                            // while ($data_student = $sel_etud->fetch()){
                                $pdf->Ln(5);
                                $pdf->cell(190, 5, decode_fr('Annee Acad. : '.get_annee($annee_acad_fin)), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Section : '.$ff), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Promotion : '.$pp), 0, 1, 'L');
                                $pdf->Ln(2);
                                $pdf->cell(60, 5, 'Type frais', 1, 0, 'L');
                                $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                                $pdf->Ln(1);
            
                                // tableau
                                $a = array();
                                $b = array();
            
                                if($_POST['type_frais'] != "Tous"){
                                    $sql_2 = "SELECT
                                            payement.id_payement,
                                            SUM(payement.montant) AS mp,
                                            payement.id_frais,
                                            prevision_frais.type_frais,
                                            annee_acad.annee_acad,
                                            sections.section,
                                            options.id_option,
                                            options.option_
                                        FROM
                                            payement
                                        LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                        LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                        LEFT JOIN sections ON payement.id_section = sections.id_section
                                        LEFT JOIN options ON payement.id_section = options.id_option
                                        WHERE prevision_frais.type_frais = ? AND annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ? AND payement.date_payement BETWEEN ? AND ?  group by prevision_frais.type_frais";
                                    $sql_2_a = array($_POST['type_frais'], $annee_acad_deb, $_POST['fac_etudiant'], $pp, $dd, $_POST['date_fin']);
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute($sql_2_a);
            
                                    while($d = $sql_2->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, utf8_decode(decode_fr($d['type_frais'])), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                        $pdf->Ln(1);
                                        $b[] = $d['mp'];
                                    }
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                    $pdf->Ln(2);
                                    $b = array();
                                } else {
                                    $sql_2 = "SELECT
                                        payement.id_payement,
                                        SUM(payement.montant) AS mp,
                                        payement.id_frais,
                                        prevision_frais.type_frais,
                                        annee_acad.annee_acad,
                                        sections.section,
                                        options.id_option,
                                        options.option_
                                    FROM
                                        payement
                                    LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                    LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                    LEFT JOIN sections ON payement.id_section = sections.id_section
                                    LEFT JOIN options ON payement.id_section = options.id_option
                                    WHERE annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ? AND payement.date_payement BETWEEN ? AND ?  group by prevision_frais.type_frais";
                                    // die($dd);
                                    $sql_2_a = array($annee_acad_deb, trim($ff), $pp, $dd, $_POST['date_fin']);
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute($sql_2_a);
            
                                    while($d = $sql_2->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, utf8_decode(decode_fr($d['type_frais'])), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                        $pdf->Ln(1);
                                        $a[] = $d['mp'];
                                    }
            
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                    // $pdf->cell(15, 5, '#', 1, 0, 'L');
                                    $pdf->Ln(2);
                                    $a = array();
                                    $b = array();
                                }
                            // }
                        }
                    }else if(empty($_POST['date_debit']) && empty($_POST['date_fin'])){
                        // selection des l etudiant
                        $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT id_annee FROM etudiants_inscrits WHERE id_annee = ? GROUP BY id_annee");
                        $sel_etud->execute(array($annee_acad_fin));
            
                        if($sel_etud->rowCount() > 0){
                            while ($data_student = $sel_etud->fetch()){
                                $pdf->Ln(5);
                                $pdf->cell(190, 5, decode_fr('Annee Acad. : '.get_annee($annee_acad_fin)), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Section : '.$ff), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Promotion : '.$pp), 0, 1, 'L');
                                $pdf->Ln(2);
                                $pdf->cell(60, 5, 'Type frais', 1, 0, 'L');
                                $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                                $pdf->Ln(1);
            
                                // tableau
                                $a = array();
                                $b = array();
            
                                if($_POST['type_frais'] != "Tous"){
                                    $sql_2 = "SELECT
                                            payement.id_payement,
                                            SUM(payement.montant) AS mp,
                                            payement.id_frais,
                                            prevision_frais.type_frais,
                                            annee_acad.annee_acad,
                                            sections.section,
                                            options.id_option,
                                            options.option_
                                        FROM
                                            payement
                                        LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                        LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                        LEFT JOIN sections ON payement.id_section = sections.id_section
                                        LEFT JOIN options ON payement.id_section = options.id_option
                                        WHERE prevision_frais.type_frais = ? AND annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?  group by prevision_frais.type_frais";
                                    $sql_2_a = array($_POST['type_frais'], $annee_acad_deb, $_POST['fac_etudiant'], $pp);
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute($sql_2_a);
            
                                    while($d = $sql_2->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, utf8_decode(decode_fr($d['type_frais'])), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                        $pdf->Ln(1);
                                        $b[] = $d['mp'];
                                    }
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                    $pdf->Ln(2);
                                    $b = array();
                                } else {
                                    $sql_2 = "SELECT
                                        payement.id_payement,
                                        SUM(payement.montant) AS mp,
                                        payement.id_frais,
                                        prevision_frais.type_frais,
                                        annee_acad.annee_acad,
                                        sections.section,
                                        options.id_option,
                                        options.option_
                                    FROM
                                        payement
                                    LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                    LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                    LEFT JOIN sections ON payement.id_section = sections.id_section
                                    LEFT JOIN options ON payement.id_section = options.id_option
                                    WHERE annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?  group by prevision_frais.type_frais";
                                    // die($pp);
                                    $sql_2_a = array($annee_acad_deb, trim($ff), $pp);
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute($sql_2_a);
            
                                    while($d = $sql_2->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, utf8_decode(decode_fr($d['type_frais'])), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                        $pdf->Ln(1);
                                        $a[] = $d['mp'];
                                    }
            
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                    // $pdf->cell(15, 5, '#', 1, 0, 'L');
                                    $pdf->Ln(2);
                                    $a = array();
                                    $b = array();
                                }
                            }
                        }
                    }
                }
            }else if($_POST['fac_etudiant'] != "Tous" && $_POST['promotion_etud'] != "Tous"){
                if(!empty($_POST['date_debit']) && !empty($_POST['date_fin'])){
                    // la tables etudiants
                    // selection des l etudiant
                    $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT id_annee FROM etudiants_inscrits WHERE id_annee = ? GROUP BY id_annee");
                    $sel_etud->execute(array($annee_acad_fin));
        
                    if($sel_etud->rowCount() > 0){
                        // while ($data_student = $sel_etud->fetch()){
                            $pdf->Ln(5);
                            $pdf->cell(190, 5, decode_fr('Annee Acad. : '.get_annee($annee_acad_fin)), 0, 1, 'L');
                            $pdf->cell(190, 5, decode_fr('Section : '.$ff), 0, 1, 'L');
                            $pdf->cell(190, 5, decode_fr('Promotion : '.$pp), 0, 1, 'L');
                            $pdf->Ln(2);
                            $pdf->cell(60, 5, 'Type frais', 1, 0, 'L');
                            $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                            $pdf->Ln(1);
        
                            // tableau
                            $a = array();
                            $b = array();
        
                            if($_POST['type_frais'] != "Tous"){
                                $sql_2 = "SELECT
                                        payement.id_payement,
                                        SUM(payement.montant) AS mp,
                                        payement.id_frais,
                                        prevision_frais.type_frais,
                                        annee_acad.annee_acad,
                                        sections.section,
                                        options.id_option,
                                        options.option_
                                    FROM
                                        payement
                                    LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                    LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                    LEFT JOIN sections ON payement.id_section = sections.id_section
                                    LEFT JOIN options ON payement.id_section = options.id_option
                                    WHERE prevision_frais.type_frais = ? AND annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ? AND payement.date_payement BETWEEN ? AND ?  group by prevision_frais.type_frais";
                                $sql_2_a = array($_POST['type_frais'], $annee_acad_deb, $_POST['fac_etudiant'], $pp, $dd, $_POST['date_fin']);
                                $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                $sql_2->execute($sql_2_a);
        
                                while($d = $sql_2->fetch()){
                                    $pdf->SetFont('Arial','i',8);
                                    $pdf->Ln(4);
                                    $pdf->cell(60, 5, utf8_decode(decode_fr($d['type_frais'])), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                    $pdf->Ln(1);
                                    $b[] = $d['mp'];
                                }
                                $pdf->Ln(4);
                                $pdf->SetFont('Arial','',9);
                                $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                $pdf->Ln(2);
                                $b = array();
                            } else {
                                $sql_2 = "SELECT
                                    payement.id_payement,
                                    SUM(payement.montant) AS mp,
                                    payement.id_frais,
                                    prevision_frais.type_frais,
                                    annee_acad.annee_acad,
                                    sections.section,
                                    options.id_option,
                                    options.option_
                                FROM
                                    payement
                                LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                LEFT JOIN sections ON payement.id_section = sections.id_section
                                LEFT JOIN options ON payement.id_section = options.id_option
                                WHERE annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ? AND payement.date_payement BETWEEN ? AND ?  group by prevision_frais.type_frais";
                                // die($dd);
                                $sql_2_a = array($annee_acad_deb, trim($ff), $pp, $dd, $_POST['date_fin']);
                                $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                $sql_2->execute($sql_2_a);
        
                                while($d = $sql_2->fetch()){
                                    $pdf->SetFont('Arial','i',8);
                                    $pdf->Ln(4);
                                    $pdf->cell(60, 5, utf8_decode(decode_fr($d['type_frais'])), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                    $pdf->Ln(1);
                                    $a[] = $d['mp'];
                                }
        
                                $pdf->Ln(4);
                                $pdf->SetFont('Arial','',9);
                                $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                // $pdf->cell(15, 5, '#', 1, 0, 'L');
                                $pdf->Ln(2);
                                $a = array();
                                $b = array();
                            }
                        // }
                    }
                }else if(empty($_POST['date_debit']) && empty($_POST['date_fin'])){
                    // selection des l etudiant
                    $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT id_annee FROM etudiants_inscrits WHERE id_annee = ? GROUP BY id_annee");
                    $sel_etud->execute(array($annee_acad_fin));
        
                    if($sel_etud->rowCount() > 0){
                        while ($data_student = $sel_etud->fetch()){
                            $pdf->Ln(5);
                            $pdf->cell(190, 5, decode_fr('Annee Acad. : '.get_annee($annee_acad_fin)), 0, 1, 'L');
                            $pdf->cell(190, 5, decode_fr('Section : '.$ff), 0, 1, 'L');
                            $pdf->cell(190, 5, decode_fr('Promotion : '.$pp), 0, 1, 'L');
                            $pdf->Ln(2);
                            $pdf->cell(60, 5, 'Type frais', 1, 0, 'L');
                            $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                            $pdf->Ln(1);
        
                            // tableau
                            $a = array();
                            $b = array();
        
                            if($_POST['type_frais'] != "Tous"){
                                $sql_2 = "SELECT
                                        payement.id_payement,
                                        SUM(payement.montant) AS mp,
                                        payement.id_frais,
                                        prevision_frais.type_frais,
                                        annee_acad.annee_acad,
                                        sections.section,
                                        options.id_option,
                                        options.option_
                                    FROM
                                        payement
                                    LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                    LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                    LEFT JOIN sections ON payement.id_section = sections.id_section
                                    LEFT JOIN options ON payement.id_section = options.id_option
                                    WHERE prevision_frais.type_frais = ? AND annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?  group by prevision_frais.type_frais";
                                $sql_2_a = array($_POST['type_frais'], $annee_acad_deb, $_POST['fac_etudiant'], $pp);
                                $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                $sql_2->execute($sql_2_a);
        
                                while($d = $sql_2->fetch()){
                                    $pdf->SetFont('Arial','i',8);
                                    $pdf->Ln(4);
                                    $pdf->cell(60, 5, utf8_decode(decode_fr($d['type_frais'])), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                    $pdf->Ln(1);
                                    $b[] = $d['mp'];
                                }
                                $pdf->Ln(4);
                                $pdf->SetFont('Arial','',9);
                                $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                $pdf->Ln(2);
                                $b = array();
                            } else {
                                $sql_2 = "SELECT
                                    payement.id_payement,
                                    SUM(payement.montant) AS mp,
                                    payement.id_frais,
                                    prevision_frais.type_frais,
                                    annee_acad.annee_acad,
                                    sections.section,
                                    options.id_option,
                                    options.option_
                                FROM
                                    payement
                                LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                LEFT JOIN sections ON payement.id_section = sections.id_section
                                LEFT JOIN options ON payement.id_section = options.id_option
                                WHERE annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?  group by prevision_frais.type_frais";
                                // die($pp);
                                $sql_2_a = array($annee_acad_deb, trim($ff), $pp);
                                $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                $sql_2->execute($sql_2_a);
        
                                while($d = $sql_2->fetch()){
                                    $pdf->SetFont('Arial','i',8);
                                    $pdf->Ln(4);
                                    $pdf->cell(60, 5, utf8_decode(decode_fr($d['type_frais'])), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                    $pdf->Ln(1);
                                    $a[] = $d['mp'];
                                }
        
                                $pdf->Ln(4);
                                $pdf->SetFont('Arial','',9);
                                $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                // $pdf->cell(15, 5, '#', 1, 0, 'L');
                                $pdf->Ln(2);
                                $a = array();
                                $b = array();
                            }
                        }
                    }
                }
            }else if($_POST['fac_etudiant'] = "Tous" && $_POST['promotion_etud'] !="Tous"){
                foreach($t_fac as $ff){
                    if(!empty($_POST['date_debit']) && !empty($_POST['date_fin'])){
                        // la tables etudiants
                        // selection des l etudiant
                        $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT id_annee FROM etudiants_inscrits WHERE id_annee = ? GROUP BY id_annee");
                        $sel_etud->execute(array($annee_acad_fin));
            
                        if($sel_etud->rowCount() > 0){
                            // while ($data_student = $sel_etud->fetch()){
                                $pdf->Ln(5);
                                $pdf->cell(190, 5, decode_fr('Annee Acad. : '.get_annee($annee_acad_fin)), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Section : '.$ff), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Promotion : '.$pp), 0, 1, 'L');
                                $pdf->Ln(2);
                                $pdf->cell(60, 5, 'Type frais', 1, 0, 'L');
                                $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                                $pdf->Ln(1);
            
                                // tableau
                                $a = array();
                                $b = array();
            
                                if($_POST['type_frais'] != "Tous"){
                                    $sql_2 = "SELECT
                                            payement.id_payement,
                                            SUM(payement.montant) AS mp,
                                            payement.id_frais,
                                            prevision_frais.type_frais,
                                            annee_acad.annee_acad,
                                            sections.section,
                                            options.id_option,
                                            options.option_
                                        FROM
                                            payement
                                        LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                        LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                        LEFT JOIN sections ON payement.id_section = sections.id_section
                                        LEFT JOIN options ON payement.id_section = options.id_option
                                        WHERE prevision_frais.type_frais = ? AND annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ? AND payement.date_payement BETWEEN ? AND ?  group by prevision_frais.type_frais";
                                    $sql_2_a = array($_POST['type_frais'], $annee_acad_deb, $_POST['fac_etudiant'], $pp, $dd, $_POST['date_fin']);
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute($sql_2_a);
            
                                    while($d = $sql_2->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, utf8_decode(decode_fr($d['type_frais'])), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                        $pdf->Ln(1);
                                        $b[] = $d['mp'];
                                    }
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                    $pdf->Ln(2);
                                    $b = array();
                                } else {
                                    $sql_2 = "SELECT
                                        payement.id_payement,
                                        SUM(payement.montant) AS mp,
                                        payement.id_frais,
                                        prevision_frais.type_frais,
                                        annee_acad.annee_acad,
                                        sections.section,
                                        options.id_option,
                                        options.option_
                                    FROM
                                        payement
                                    LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                    LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                    LEFT JOIN sections ON payement.id_section = sections.id_section
                                    LEFT JOIN options ON payement.id_section = options.id_option
                                    WHERE annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ? AND payement.date_payement BETWEEN ? AND ?  group by prevision_frais.type_frais";
                                    // die($dd);
                                    $sql_2_a = array($annee_acad_deb, trim($ff), $pp, $dd, $_POST['date_fin']);
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute($sql_2_a);
            
                                    while($d = $sql_2->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, utf8_decode(decode_fr($d['type_frais'])), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                        $pdf->Ln(1);
                                        $a[] = $d['mp'];
                                    }
            
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                    // $pdf->cell(15, 5, '#', 1, 0, 'L');
                                    $pdf->Ln(2);
                                    $a = array();
                                    $b = array();
                                }
                            // }
                        }
                    }else if(empty($_POST['date_debit']) && empty($_POST['date_fin'])){
                        // selection des l etudiant
                        $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT id_annee FROM etudiants_inscrits WHERE id_annee = ? GROUP BY id_annee");
                        $sel_etud->execute(array($annee_acad_fin));
            
                        if($sel_etud->rowCount() > 0){
                            while ($data_student = $sel_etud->fetch()){
                                $pdf->Ln(5);
                                $pdf->cell(190, 5, decode_fr('Annee Acad. : '.get_annee($annee_acad_fin)), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Section : '.$ff), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Promotion : '.$pp), 0, 1, 'L');
                                $pdf->Ln(2);
                                $pdf->cell(60, 5, 'Type frais', 1, 0, 'L');
                                $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                                $pdf->Ln(1);
            
                                // tableau
                                $a = array();
                                $b = array();
            
                                if($_POST['type_frais'] != "Tous"){
                                    $sql_2 = "SELECT
                                            payement.id_payement,
                                            SUM(payement.montant) AS mp,
                                            payement.id_frais,
                                            prevision_frais.type_frais,
                                            annee_acad.annee_acad,
                                            sections.section,
                                            options.id_option,
                                            options.option_
                                        FROM
                                            payement
                                        LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                        LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                        LEFT JOIN sections ON payement.id_section = sections.id_section
                                        LEFT JOIN options ON payement.id_section = options.id_option
                                        WHERE prevision_frais.type_frais = ? AND annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?  group by prevision_frais.type_frais";
                                    $sql_2_a = array($_POST['type_frais'], $annee_acad_deb, $_POST['fac_etudiant'], $pp);
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute($sql_2_a);
            
                                    while($d = $sql_2->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, utf8_decode(decode_fr($d['type_frais'])), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                        $pdf->Ln(1);
                                        $b[] = $d['mp'];
                                    }
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                    $pdf->Ln(2);
                                    $b = array();
                                } else {
                                    $sql_2 = "SELECT
                                        payement.id_payement,
                                        SUM(payement.montant) AS mp,
                                        payement.id_frais,
                                        prevision_frais.type_frais,
                                        annee_acad.annee_acad,
                                        sections.section,
                                        options.id_option,
                                        options.option_
                                    FROM
                                        payement
                                    LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                    LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                    LEFT JOIN sections ON payement.id_section = sections.id_section
                                    LEFT JOIN options ON payement.id_section = options.id_option
                                    WHERE annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?  group by prevision_frais.type_frais";
                                    // die($pp);
                                    $sql_2_a = array($annee_acad_deb, trim($ff), $pp);
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute($sql_2_a);
            
                                    while($d = $sql_2->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, utf8_decode(decode_fr($d['type_frais'])), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                        $pdf->Ln(1);
                                        $a[] = $d['mp'];
                                    }
            
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                    // $pdf->cell(15, 5, '#', 1, 0, 'L');
                                    $pdf->Ln(2);
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

    // synthese par promotion
    if(isset($_POST['btn_promotion'])){
        $pdf = new FPDF('P', 'mm', 'A4');
        $annee_acad_deb = $_POST['annee_acad_deb'];
        $annee_acad_fin = $_POST['annee_acad_fin'];
        $_POST['fac_etudiant'] == "Tous";

        all($pdf);
        $pdf->SetFont('Arial','BU',10);
        $pdf->cell(190, 10, decode_fr('SYNTHSE DES FRAIS PAR PROMOTION'), 0, 1, 'C');
        $pdf->SetFont('Arial','',10);

        all_trans($pdf);
        $pdf->Ln(15);
        $pdf->SetFont('Arial','',10);
	    $pdf->cell(300,20, decode_fr('par : '.$_SESSION['data']['noms'].'; le '.date('d/M/Y')),0,1,'C');
        $pdf->Output();
    }

    // filtre par faculte
    if(isset($_POST['btn_fac'])){
        $pdf = new FPDF('P', 'mm', 'A4');
        $annee_acad_deb = $_POST['annee_acad_deb'];
        $annee_acad_fin = $_POST['annee_acad_fin'];

        all($pdf);
        $pdf->SetFont('Arial','BU',10);
        $pdf->cell(190, 10, decode_fr('SYNTHÈSE DES FRAIS PAR SECTION'), 0, 1, 'C');
        $pdf->SetFont('Arial','',10);

        $annee_acad_deb = $_POST['annee_acad_deb'];
        $annee_acad_fin = $_POST['annee_acad_fin'];
        $ff = $_POST['fac_etudiant'];
        $pp = $_POST['promotion_etud'];
        $dd = $_POST['date_debit'];
        $df = $_POST['date_fin'];
        if($annee_acad_deb == $annee_acad_fin){
            $t_fac = array();
            $t_promotion = array();
            $ff = $_POST['fac_etudiant'];
            $pp = $_POST['promotion_etud'];
            // selection des toutes les facultes
            
            if($_POST['fac_etudiant'] == "Tous" && $_POST['promotion_etud'] == "Tous"){
                $f = ConnexionBdd::Connecter()->query("SELECT DISTINCT  etudiants_inscrits.id_section, sections.section, annee_acad.id_annee FROM etudiants_inscrits LEFT JOIN sections ON etudiants_inscrits.id_section = sections.id_section LEFT JOIN annee_acad ON etudiants_inscrits.id_annee = annee_acad.id_annee WHERE etudiants_inscrits.id_annee = {$annee_acad_fin}");
                while($df = $f->fetch()){
                    $t_fac[] = $df['section'];
                }
                foreach($t_fac as $ff){
                    if(!empty($_POST['date_debit']) && !empty($_POST['date_fin'])){
                        // la tables etudiants
                        // selection des l etudiant
                        $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT id_annee FROM etudiants_inscrits WHERE id_annee = ? GROUP BY id_annee");
                        $sel_etud->execute(array($annee_acad_fin));
        
                        if($sel_etud->rowCount() > 0){
                            // while ($data_student = $sel_etud->fetch()){
                                $pdf->Ln(5);
                                $pdf->cell(190, 5, decode_fr('Annee Acad. : '.get_annee($annee_acad_fin)), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Section : '.$ff), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Promotion : '.$pp), 0, 1, 'L');
                                $pdf->Ln(2);
                                $pdf->cell(60, 5, 'Type frais', 1, 0, 'L');
                                $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                                $pdf->Ln(1);
        
                                // tableau
                                $a = array();
                                $b = array();
        
                                if($_POST['type_frais'] != "Tous"){
                                    $sql_2 = "SELECT
                                            payement.id_payement,
                                            SUM(payement.montant) AS mp,
                                            payement.id_frais,
                                            prevision_frais.type_frais,
                                            annee_acad.annee_acad,
                                            sections.section,
                                            options.id_option,
                                            options.option_
                                        FROM
                                            payement
                                        LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                        LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                        LEFT JOIN sections ON payement.id_section = sections.id_section
                                        LEFT JOIN options ON payement.id_section = options.id_option
                                        WHERE prevision_frais.type_frais = ? AND annee_acad.id_annee = ? AND sections.section = ? AND payement.date_payement BETWEEN ? AND ? GROUP BY prevision_frais.type_frais";
                                    $sql_2_a = array($_POST['type_frais'], $annee_acad_deb, $ff, $dd, $_POST['date_fin']);
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute($sql_2_a);
    
                                    while($d = $sql_2->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, utf8_decode(decode_fr($d['type_frais'])), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                        $pdf->Ln(1);
                                        $b[] = $d['mp'];
                                    }
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                    $pdf->Ln(2);
                                    $b = array();
                                } else {
                                    $sql_2 = "SELECT
                                        payement.id_payement,
                                        SUM(payement.montant) AS mp,
                                        payement.id_frais,
                                        prevision_frais.type_frais,
                                        annee_acad.annee_acad,
                                        sections.section,
                                        options.id_option,
                                        options.option_
                                    FROM
                                        payement
                                    LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                    LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                    LEFT JOIN sections ON payement.id_section = sections.id_section
                                    LEFT JOIN options ON payement.id_section = options.id_option
                                    WHERE annee_acad.id_annee = ? AND sections.section = ? AND payement.date_payement BETWEEN ? AND ?  GROUP BY prevision_frais.type_frais";
                                    // die($dd);
                                    $sql_2_a = array($annee_acad_deb, trim($ff), $dd, $_POST['date_fin']);
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute($sql_2_a);
        
                                    while($d = $sql_2->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, utf8_decode(decode_fr($d['type_frais'])), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                        $pdf->Ln(1);
                                        $a[] = $d['mp'];
                                    }
        
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                    // $pdf->cell(15, 5, '#', 1, 0, 'L');
                                    $pdf->Ln(2);
                                    $a = array();
                                    $b = array();
                                }
                            // }
                        }
                    }else if(empty($_POST['date_debit']) && empty($_POST['date_fin'])){
                        // selection des l etudiant
                        $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT id_annee FROM etudiants_inscrits WHERE id_annee = ? GROUP BY id_annee");
                        $sel_etud->execute(array($annee_acad_fin));
        
                        if($sel_etud->rowCount() > 0){
                            while ($data_student = $sel_etud->fetch()){
                                $pdf->Ln(5);
                                $pdf->cell(190, 5, decode_fr('Annee Acad. : '.get_annee($annee_acad_fin)), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Section : '.$ff), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Promotion : '.$pp), 0, 1, 'L');
                                $pdf->Ln(2);
                                $pdf->cell(60, 5, 'Type frais', 1, 0, 'L');
                                $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                                $pdf->Ln(1);
        
                                // tableau
                                $a = array();
                                $b = array();
        
                                if($_POST['type_frais'] != "Tous"){
                                    $sql_2 = "SELECT
                                            payement.id_payement,
                                            SUM(payement.montant) AS mp,
                                            payement.id_frais,
                                            prevision_frais.type_frais,
                                            annee_acad.annee_acad,
                                            sections.section,
                                            options.id_option,
                                            options.option_
                                        FROM
                                            payement
                                        LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                        LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                        LEFT JOIN sections ON payement.id_section = sections.id_section
                                        LEFT JOIN options ON payement.id_section = options.id_option
                                        WHERE prevision_frais.type_frais = ? AND annee_acad.id_annee = ? AND sections.section = ? 
                                        group by prevision_frais.type_frais";
                                    $sql_2_a = array($_POST['type_frais'], $annee_acad_deb, $ff);
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute($sql_2_a);
    
                                    while($d = $sql_2->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, utf8_decode(decode_fr($d['type_frais'])), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                        $pdf->Ln(1);
                                        $b[] = $d['mp'];
                                    }
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                    $pdf->Ln(2);
                                    $b = array();
                                } else {
                                    $sql_2 = "SELECT
                                        payement.id_payement,
                                        SUM(payement.montant) AS mp,
                                        payement.id_frais,
                                        prevision_frais.type_frais,
                                        annee_acad.annee_acad,
                                        sections.section,
                                        options.id_option,
                                        options.option_
                                    FROM
                                        payement
                                    LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                    LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                    LEFT JOIN sections ON payement.id_section = sections.id_section
                                    LEFT JOIN options ON payement.id_section = options.id_option
                                    WHERE annee_acad.id_annee = ? AND sections.section = ? 
                                    group by prevision_frais.type_frais, sections.section";
                                    // die($pp);
                                    $sql_2_a = array($annee_acad_deb, trim($ff));
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute($sql_2_a);
        
                                    while($d = $sql_2->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, utf8_decode(decode_fr($d['type_frais'])), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                        $pdf->Ln(1);
                                        $a[] = $d['mp'];
                                    }
        
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                    // $pdf->cell(15, 5, '#', 1, 0, 'L');
                                    $pdf->Ln(2);
                                    $a = array();
                                    $b = array();
                                }
                            }
                        }
                    }
                }
            }else if($_POST['fac_etudiant'] != "Tous" && $_POST['promotion_etud'] == "Tous"){
                $f = ConnexionBdd::Connecter()->query("SELECT DISTINCT etudiants_inscrits.promotion, options.id_option, sections.section FROM etudiants_inscrits LEFT JOIN options ON etudiants_inscrits.id_option = options.id_option LEFT JOIN sections ON etudiants_inscrits.id_section = sections.id_section WHERE sections.section = '{$_POST['fac_etudiant']}' GROUP BY etudiants_inscrits.promotion");
                while($df = $f->fetch()){
                    $t_promotion[] = $df['promotion'];
                }
                foreach($t_promotion as $pp){
                    if(!empty($_POST['date_debit']) && !empty($_POST['date_fin'])){
                        // la tables etudiants
                        // selection des l etudiant
                        $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT id_annee FROM etudiants_inscrits WHERE id_annee = ? GROUP BY id_annee");
                        $sel_etud->execute(array($annee_acad_fin));
        
                        if($sel_etud->rowCount() > 0){
                            // while ($data_student = $sel_etud->fetch()){
                                $pdf->Ln(5);
                                $pdf->cell(190, 5, decode_fr('Annee Acad. : '.get_annee($annee_acad_fin)), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Section : '.$ff), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Promotion : '.$pp), 0, 1, 'L');
                                $pdf->Ln(2);
                                $pdf->cell(60, 5, 'Type frais', 1, 0, 'L');
                                $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                                $pdf->Ln(1);
        
                                // tableau
                                $a = array();
                                $b = array();
        
                                if($_POST['type_frais'] != "Tous"){
                                    $sql_2 = "SELECT
                                            payement.id_payement,
                                            SUM(payement.montant) AS mp,
                                            payement.id_frais,
                                            prevision_frais.type_frais,
                                            annee_acad.annee_acad,
                                            sections.section,
                                            options.id_option,
                                            options.option_
                                        FROM
                                            payement
                                        LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                        LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                        LEFT JOIN sections ON payement.id_section = sections.id_section
                                        LEFT JOIN options ON payement.id_section = options.id_option
                                        WHERE prevision_frais.type_frais = ? AND annee_acad.id_annee = ? AND sections.section = ? AND payement.date_payement BETWEEN ? AND ? GROUP BY prevision_frais.type_frais";
                                    $sql_2_a = array($_POST['type_frais'], $annee_acad_deb, $ff, $dd, $_POST['date_fin']);
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute($sql_2_a);
    
                                    while($d = $sql_2->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, utf8_decode(decode_fr($d['type_frais'])), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                        $pdf->Ln(1);
                                        $b[] = $d['mp'];
                                    }
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                    $pdf->Ln(2);
                                    $b = array();
                                } else {
                                    $sql_2 = "SELECT
                                        payement.id_payement,
                                        SUM(payement.montant) AS mp,
                                        payement.id_frais,
                                        prevision_frais.type_frais,
                                        annee_acad.annee_acad,
                                        sections.section,
                                        options.id_option,
                                        options.option_
                                    FROM
                                        payement
                                    LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                    LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                    LEFT JOIN sections ON payement.id_section = sections.id_section
                                    LEFT JOIN options ON payement.id_section = options.id_option
                                    WHERE annee_acad.id_annee = ? AND sections.section = ? AND payement.date_payement BETWEEN ? AND ?  GROUP BY prevision_frais.type_frais";
                                    // die($dd);
                                    $sql_2_a = array($annee_acad_deb, trim($ff), $dd, $_POST['date_fin']);
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute($sql_2_a);
        
                                    while($d = $sql_2->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, utf8_decode(decode_fr($d['type_frais'])), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                        $pdf->Ln(1);
                                        $a[] = $d['mp'];
                                    }
        
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                    // $pdf->cell(15, 5, '#', 1, 0, 'L');
                                    $pdf->Ln(2);
                                    $a = array();
                                    $b = array();
                                }
                            // }
                        }
                    }else if(empty($_POST['date_debit']) && empty($_POST['date_fin'])){
                        // selection des l etudiant
                        $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT id_annee FROM etudiants_inscrits WHERE id_annee = ? GROUP BY id_annee");
                        $sel_etud->execute(array($annee_acad_fin));
        
                        if($sel_etud->rowCount() > 0){
                            while ($data_student = $sel_etud->fetch()){
                                $pdf->Ln(5);
                                $pdf->cell(190, 5, decode_fr('Annee Acad. : '.get_annee($annee_acad_fin)), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Section : '.$ff), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Promotion : '.$pp), 0, 1, 'L');
                                $pdf->Ln(2);
                                $pdf->cell(60, 5, 'Type frais', 1, 0, 'L');
                                $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                                $pdf->Ln(1);
        
                                // tableau
                                $a = array();
                                $b = array();
        
                                if($_POST['type_frais'] != "Tous"){
                                    $sql_2 = "SELECT
                                            payement.id_payement,
                                            SUM(payement.montant) AS mp,
                                            payement.id_frais,
                                            prevision_frais.type_frais,
                                            annee_acad.annee_acad,
                                            sections.section,
                                            options.id_option,
                                            options.option_
                                        FROM
                                            payement
                                        LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                        LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                        LEFT JOIN sections ON payement.id_section = sections.id_section
                                        LEFT JOIN options ON payement.id_section = options.id_option
                                        WHERE prevision_frais.type_frais = ? AND annee_acad.id_annee = ? AND sections.section = ? 
                                        group by prevision_frais.type_frais";
                                    $sql_2_a = array($_POST['type_frais'], $annee_acad_deb, $ff);
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute($sql_2_a);
    
                                    while($d = $sql_2->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, utf8_decode(decode_fr($d['type_frais'])), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                        $pdf->Ln(1);
                                        $b[] = $d['mp'];
                                    }
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                    $pdf->Ln(2);
                                    $b = array();
                                } else {
                                    $sql_2 = "SELECT
                                        payement.id_payement,
                                        SUM(payement.montant) AS mp,
                                        payement.id_frais,
                                        prevision_frais.type_frais,
                                        annee_acad.annee_acad,
                                        sections.section,
                                        options.id_option,
                                        options.option_
                                    FROM
                                        payement
                                    LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                    LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                    LEFT JOIN sections ON payement.id_section = sections.id_section
                                    LEFT JOIN options ON payement.id_section = options.id_option
                                    WHERE annee_acad.id_annee = ? AND sections.section = ? 
                                    group by prevision_frais.type_frais, sections.section";
                                    // die($pp);
                                    $sql_2_a = array($annee_acad_deb, trim($ff));
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute($sql_2_a);
        
                                    while($d = $sql_2->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, utf8_decode(decode_fr($d['type_frais'])), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                        $pdf->Ln(1);
                                        $a[] = $d['mp'];
                                    }
        
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                    // $pdf->cell(15, 5, '#', 1, 0, 'L');
                                    $pdf->Ln(2);
                                    $a = array();
                                    $b = array();
                                }
                            }
                        }
                    }
                }
            }else if($_POST['fac_etudiant'] != "Tous" && $_POST['promotion_etud'] != "Tous"){
                if(!empty($_POST['date_debit']) && !empty($_POST['date_fin'])){
                    // la tables etudiants
                    // selection des l etudiant
                    $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT id_annee FROM etudiants_inscrits WHERE id_annee = ? GROUP BY id_annee");
                    $sel_etud->execute(array($annee_acad_fin));
    
                    if($sel_etud->rowCount() > 0){
                        // while ($data_student = $sel_etud->fetch()){
                            $pdf->Ln(5);
                            $pdf->cell(190, 5, decode_fr('Annee Acad. : '.get_annee($annee_acad_fin)), 0, 1, 'L');
                            $pdf->cell(190, 5, decode_fr('Section : '.$ff), 0, 1, 'L');
                            $pdf->cell(190, 5, decode_fr('Promotion : '.$pp), 0, 1, 'L');
                            $pdf->Ln(2);
                            $pdf->cell(60, 5, 'Type frais', 1, 0, 'L');
                            $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                            $pdf->Ln(1);
    
                            // tableau
                            $a = array();
                            $b = array();
    
                            if($_POST['type_frais'] != "Tous"){
                                $sql_2 = "SELECT
                                        payement.id_payement,
                                        SUM(payement.montant) AS mp,
                                        payement.id_frais,
                                        prevision_frais.type_frais,
                                        annee_acad.annee_acad,
                                        sections.section,
                                        options.id_option,
                                        options.option_
                                    FROM
                                        payement
                                    LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                    LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                    LEFT JOIN sections ON payement.id_section = sections.id_section
                                    LEFT JOIN options ON payement.id_section = options.id_option
                                    WHERE prevision_frais.type_frais = ? AND annee_acad.id_annee = ? AND sections.section = ? AND payement.date_payement BETWEEN ? AND ? GROUP BY prevision_frais.type_frais";
                                $sql_2_a = array($_POST['type_frais'], $annee_acad_deb, $ff, $dd, $_POST['date_fin']);
                                $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                $sql_2->execute($sql_2_a);

                                while($d = $sql_2->fetch()){
                                    $pdf->SetFont('Arial','i',8);
                                    $pdf->Ln(4);
                                    $pdf->cell(60, 5, utf8_decode(decode_fr($d['type_frais'])), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                    $pdf->Ln(1);
                                    $b[] = $d['mp'];
                                }
                                $pdf->Ln(4);
                                $pdf->SetFont('Arial','',9);
                                $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                $pdf->Ln(2);
                                $b = array();
                            } else {
                                $sql_2 = "SELECT
                                    payement.id_payement,
                                    SUM(payement.montant) AS mp,
                                    payement.id_frais,
                                    prevision_frais.type_frais,
                                    annee_acad.annee_acad,
                                    sections.section,
                                    options.id_option,
                                    options.option_
                                FROM
                                    payement
                                LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                LEFT JOIN sections ON payement.id_section = sections.id_section
                                LEFT JOIN options ON payement.id_section = options.id_option
                                WHERE annee_acad.id_annee = ? AND sections.section = ? AND payement.date_payement BETWEEN ? AND ?  GROUP BY prevision_frais.type_frais";
                                // die($dd);
                                $sql_2_a = array($annee_acad_deb, trim($ff), $dd, $_POST['date_fin']);
                                $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                $sql_2->execute($sql_2_a);
    
                                while($d = $sql_2->fetch()){
                                    $pdf->SetFont('Arial','i',8);
                                    $pdf->Ln(4);
                                    $pdf->cell(60, 5, utf8_decode(decode_fr($d['type_frais'])), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                    $pdf->Ln(1);
                                    $a[] = $d['mp'];
                                }
    
                                $pdf->Ln(4);
                                $pdf->SetFont('Arial','',9);
                                $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                // $pdf->cell(15, 5, '#', 1, 0, 'L');
                                $pdf->Ln(2);
                                $a = array();
                                $b = array();
                            }
                        // }
                    }
                }else if(empty($_POST['date_debit']) && empty($_POST['date_fin'])){
                    // selection des l etudiant
                    $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT id_annee FROM etudiants_inscrits WHERE id_annee = ? GROUP BY id_annee");
                    $sel_etud->execute(array($annee_acad_fin));
    
                    if($sel_etud->rowCount() > 0){
                        while ($data_student = $sel_etud->fetch()){
                            $pdf->Ln(5);
                            $pdf->cell(190, 5, decode_fr('Annee Acad. : '.get_annee($annee_acad_fin)), 0, 1, 'L');
                            $pdf->cell(190, 5, decode_fr('Section : '.$ff), 0, 1, 'L');
                            $pdf->cell(190, 5, decode_fr('Promotion : '.$pp), 0, 1, 'L');
                            $pdf->Ln(2);
                            $pdf->cell(60, 5, 'Type frais', 1, 0, 'L');
                            $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                            $pdf->Ln(1);
    
                            // tableau
                            $a = array();
                            $b = array();
    
                            if($_POST['type_frais'] != "Tous"){
                                $sql_2 = "SELECT
                                        payement.id_payement,
                                        SUM(payement.montant) AS mp,
                                        payement.id_frais,
                                        prevision_frais.type_frais,
                                        annee_acad.annee_acad,
                                        sections.section,
                                        options.id_option,
                                        options.option_
                                    FROM
                                        payement
                                    LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                    LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                    LEFT JOIN sections ON payement.id_section = sections.id_section
                                    LEFT JOIN options ON payement.id_section = options.id_option
                                    WHERE prevision_frais.type_frais = ? AND annee_acad.id_annee = ? AND sections.section = ? 
                                    group by prevision_frais.type_frais";
                                $sql_2_a = array($_POST['type_frais'], $annee_acad_deb, $ff);
                                $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                $sql_2->execute($sql_2_a);

                                while($d = $sql_2->fetch()){
                                    $pdf->SetFont('Arial','i',8);
                                    $pdf->Ln(4);
                                    $pdf->cell(60, 5, utf8_decode(decode_fr($d['type_frais'])), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                    $pdf->Ln(1);
                                    $b[] = $d['mp'];
                                }
                                $pdf->Ln(4);
                                $pdf->SetFont('Arial','',9);
                                $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                $pdf->Ln(2);
                                $b = array();
                            } else {
                                $sql_2 = "SELECT
                                    payement.id_payement,
                                    SUM(payement.montant) AS mp,
                                    payement.id_frais,
                                    prevision_frais.type_frais,
                                    annee_acad.annee_acad,
                                    sections.section,
                                    options.id_option,
                                    options.option_
                                FROM
                                    payement
                                LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                LEFT JOIN sections ON payement.id_section = sections.id_section
                                LEFT JOIN options ON payement.id_section = options.id_option
                                WHERE annee_acad.id_annee = ? AND sections.section = ? 
                                group by prevision_frais.type_frais, sections.section";
                                // die($pp);
                                $sql_2_a = array($annee_acad_deb, trim($ff));
                                $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                $sql_2->execute($sql_2_a);
    
                                while($d = $sql_2->fetch()){
                                    $pdf->SetFont('Arial','i',8);
                                    $pdf->Ln(4);
                                    $pdf->cell(60, 5, utf8_decode(decode_fr($d['type_frais'])), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                    $pdf->Ln(1);
                                    $a[] = $d['mp'];
                                }
    
                                $pdf->Ln(4);
                                $pdf->SetFont('Arial','',9);
                                $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                // $pdf->cell(15, 5, '#', 1, 0, 'L');
                                $pdf->Ln(2);
                                $a = array();
                                $b = array();
                            }
                        }
                    }
                }
            }else if($_POST['fac_etudiant'] = "Tous" && $_POST['promotion_etud'] !="Tous"){
                foreach($t_fac as $ff){
                    if(!empty($_POST['date_debit']) && !empty($_POST['date_fin'])){
                        // la tables etudiants
                        // selection des l etudiant
                        $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT id_annee FROM etudiants_inscrits WHERE id_annee = ? GROUP BY id_annee");
                        $sel_etud->execute(array($annee_acad_fin));
        
                        if($sel_etud->rowCount() > 0){
                            // while ($data_student = $sel_etud->fetch()){
                                $pdf->Ln(5);
                                $pdf->cell(190, 5, decode_fr('Annee Acad. : '.get_annee($annee_acad_fin)), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Section : '.$ff), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Promotion : '.$pp), 0, 1, 'L');
                                $pdf->Ln(2);
                                $pdf->cell(60, 5, 'Type frais', 1, 0, 'L');
                                $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                                $pdf->Ln(1);
        
                                // tableau
                                $a = array();
                                $b = array();
        
                                if($_POST['type_frais'] != "Tous"){
                                    $sql_2 = "SELECT
                                            payement.id_payement,
                                            SUM(payement.montant) AS mp,
                                            payement.id_frais,
                                            prevision_frais.type_frais,
                                            annee_acad.annee_acad,
                                            sections.section,
                                            options.id_option,
                                            options.option_
                                        FROM
                                            payement
                                        LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                        LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                        LEFT JOIN sections ON payement.id_section = sections.id_section
                                        LEFT JOIN options ON payement.id_section = options.id_option
                                        WHERE prevision_frais.type_frais = ? AND annee_acad.id_annee = ? AND sections.section = ? AND payement.date_payement BETWEEN ? AND ? GROUP BY prevision_frais.type_frais";
                                    $sql_2_a = array($_POST['type_frais'], $annee_acad_deb, $ff, $dd, $_POST['date_fin']);
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute($sql_2_a);
    
                                    while($d = $sql_2->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, utf8_decode(decode_fr($d['type_frais'])), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                        $pdf->Ln(1);
                                        $b[] = $d['mp'];
                                    }
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                    $pdf->Ln(2);
                                    $b = array();
                                } else {
                                    $sql_2 = "SELECT
                                        payement.id_payement,
                                        SUM(payement.montant) AS mp,
                                        payement.id_frais,
                                        prevision_frais.type_frais,
                                        annee_acad.annee_acad,
                                        sections.section,
                                        options.id_option,
                                        options.option_
                                    FROM
                                        payement
                                    LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                    LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                    LEFT JOIN sections ON payement.id_section = sections.id_section
                                    LEFT JOIN options ON payement.id_section = options.id_option
                                    WHERE annee_acad.id_annee = ? AND sections.section = ? AND payement.date_payement BETWEEN ? AND ?  GROUP BY prevision_frais.type_frais";
                                    // die($dd);
                                    $sql_2_a = array($annee_acad_deb, trim($ff), $dd, $_POST['date_fin']);
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute($sql_2_a);
        
                                    while($d = $sql_2->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, utf8_decode(decode_fr($d['type_frais'])), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                        $pdf->Ln(1);
                                        $a[] = $d['mp'];
                                    }
        
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                    // $pdf->cell(15, 5, '#', 1, 0, 'L');
                                    $pdf->Ln(2);
                                    $a = array();
                                    $b = array();
                                }
                            // }
                        }
                    }else if(empty($_POST['date_debit']) && empty($_POST['date_fin'])){
                        // selection des l etudiant
                        $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT id_annee FROM etudiants_inscrits WHERE id_annee = ? GROUP BY id_annee");
                        $sel_etud->execute(array($annee_acad_fin));
        
                        if($sel_etud->rowCount() > 0){
                            while ($data_student = $sel_etud->fetch()){
                                $pdf->Ln(5);
                                $pdf->cell(190, 5, decode_fr('Annee Acad. : '.get_annee($annee_acad_fin)), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Section : '.$ff), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Promotion : '.$pp), 0, 1, 'L');
                                $pdf->Ln(2);
                                $pdf->cell(60, 5, 'Type frais', 1, 0, 'L');
                                $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                                $pdf->Ln(1);
        
                                // tableau
                                $a = array();
                                $b = array();
        
                                if($_POST['type_frais'] != "Tous"){
                                    $sql_2 = "SELECT
                                            payement.id_payement,
                                            SUM(payement.montant) AS mp,
                                            payement.id_frais,
                                            prevision_frais.type_frais,
                                            annee_acad.annee_acad,
                                            sections.section,
                                            options.id_option,
                                            options.option_
                                        FROM
                                            payement
                                        LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                        LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                        LEFT JOIN sections ON payement.id_section = sections.id_section
                                        LEFT JOIN options ON payement.id_section = options.id_option
                                        WHERE prevision_frais.type_frais = ? AND annee_acad.id_annee = ? AND sections.section = ? 
                                        group by prevision_frais.type_frais";
                                    $sql_2_a = array($_POST['type_frais'], $annee_acad_deb, $ff);
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute($sql_2_a);
    
                                    while($d = $sql_2->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, utf8_decode(decode_fr($d['type_frais'])), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                        $pdf->Ln(1);
                                        $b[] = $d['mp'];
                                    }
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                    $pdf->Ln(2);
                                    $b = array();
                                } else {
                                    $sql_2 = "SELECT
                                        payement.id_payement,
                                        SUM(payement.montant) AS mp,
                                        payement.id_frais,
                                        prevision_frais.type_frais,
                                        annee_acad.annee_acad,
                                        sections.section,
                                        options.id_option,
                                        options.option_
                                    FROM
                                        payement
                                    LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                    LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                    LEFT JOIN sections ON payement.id_section = sections.id_section
                                    LEFT JOIN options ON payement.id_section = options.id_option
                                    WHERE annee_acad.id_annee = ? AND sections.section = ? 
                                    group by prevision_frais.type_frais, sections.section";
                                    // die($pp);
                                    $sql_2_a = array($annee_acad_deb, trim($ff));
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute($sql_2_a);
        
                                    while($d = $sql_2->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, utf8_decode(decode_fr($d['type_frais'])), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                        $pdf->Ln(1);
                                        $a[] = $d['mp'];
                                    }
        
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                    // $pdf->cell(15, 5, '#', 1, 0, 'L');
                                    $pdf->Ln(2);
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

    // filtre par annee academique
    if(isset($_POST['acad_f'])){
        $pdf = new FPDF('P', 'mm', 'A4');
        all($pdf);
        $pdf->SetFont('Arial','BU',10);
        $pdf->cell(190, 10, decode_fr('RAPPORT FRAIS PAR ANNEE ACADEMIQUE '.$_POST['annee_acad_deb']), 0, 1, 'C');
        $pdf->SetFont('Arial','',10);
        
        $pdf = new FPDF('P', 'mm', 'A4');
        $annee_acad_deb = $_POST['annee_acad_deb'];
        $annee_acad_fin = $_POST['annee_acad_fin'];

        all($pdf);
        $pdf->SetFont('Arial','BU',10);
        $pdf->cell(190, 10, decode_fr('SYNTHÈSE DES FRAIS PAR ANNEE ACADEMIQUE'), 0, 1, 'C');
        $pdf->SetFont('Arial','',10);

        
        if($annee_acad_deb == $annee_acad_fin){
            $t_fac = array();
            $t_promotion = array();
            $ff = $_POST['fac_etudiant'];
            $pp = $_POST['promotion_etud'];
            $annee_acad_deb = $_POST['annee_acad_deb'];
            $annee_acad_fin = $_POST['annee_acad_fin'];
            $ff = $_POST['fac_etudiant'];
            $pp = $_POST['promotion_etud'];
            $dd = date("Y-m-d", strtotime($_POST['date_debit']));
            $df = date("Y-m-d", strtotime($_POST['date_fin']));
            // selection des toutes les facultes
            $f = ConnexionBdd::Connecter()->query("SELECT DISTINCT id_section FROM sections GROUP BY id_section");
            while($df = $f->fetch()){
                $t_fac[] = $df['id_section'];
            }
            // selection des toutes les promotions
            $f = ConnexionBdd::Connecter()->query("SELECT DISTINCT promotion FROM etudiants_inscrits GROUP BY promotion");
            while($df = $f->fetch()){
                $t_promotion[] = $df['promotion'];
            }
            if($_POST['fac_etudiant'] == "Tous" && $_POST['promotion_etud'] == "Tous"){
                if(!empty($_POST['date_debit']) && !empty($_POST['date_fin'])){
                    // la tables etudiants
                    $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT promotion, id_annee FROM etudiants_inscrits WHERE id_annee = ? GROUP BY id_annee");
                    $sel_etud->execute(array($annee_acad_fin));
    
                    if($sel_etud->rowCount() > 0){
                        // die($_POST['type_frais']);
                        while ($data_student = $sel_etud->fetch()){
                            $pdf->cell(190, 5, decode_fr('Annee Acad. : '.get_annee($annee_acad_fin)), 0, 1, 'L');
                            $pdf->cell(190, 5, decode_fr('Faculte : '.get_fac($ff)), 0, 1, 'L');
                            $pdf->cell(190, 5, decode_fr('Promotion : '.$pp), 0, 1, 'L');
                            $pdf->Ln(2);
                            $pdf->cell(60, 5, 'Type frais', 1, 0, 'L');
                            $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                            $pdf->Ln(1);
    
                            // tableau
                            $a = array();
                            $b = array();
                            if($_POST['type_frais'] != "Tous"){
                                $sql_2 = "SELECT
                                            payement.id_payement,
                                            SUM(payement.montant) AS montant,
                                            payement.id_frais,
                                            prevision_frais.type_frais,
                                            annee_acad.annee_acad
                                        FROM
                                            payement
                                        LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                        LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                        WHERE payement.id_annee  = ? AND prevision_frais.type_frais = ? AND payement.date_payement BETWEEN ? AND ? GROUP BY prevision_frais.type_frais";
                                
                                $sql_2_a = array($annee_acad_deb, $_POST['type_frais'], $_POST['date_debit'], $_POST['date_fin']);
                                $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                $sql_2->execute($sql_2_a);
    
                                while($d = $sql_2->fetch()){
                                    $sql = "SELECT type_frais, SUM(montant) as mt FROM affectation_frais WHERE annee_acad = ? AND type_frais = ?";
                                    $sq_array = array($annee_acad_deb, $d['type_frais']);
                                    $data_ = ConnexionBdd::Connecter()->prepare($sql);
                                    $data_->execute($sq_array);
    
                                    while($data = $data_->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, decode_fr($data['type_frais']), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                        $pdf->Ln(4);
                                        $a[] = $d['mp'];
                                        // $b[] = $d['mp'];
                                    }
                                }
                                if(array_sum($b) <= 0){$pdf->Ln(4);}else{$pdf->Ln(3);}
                                $pdf->SetFont('Arial','',9);
                                $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                $pdf->Ln(2);
                                $a = array();
                                $b = array();
                                // le type de frais n est pas selectionner
                            }else{
                                $sql_2 = "SELECT
                                            payement.id_payement,
                                            SUM(payement.montant) AS mp,
                                            payement.id_frais,
                                            prevision_frais.type_frais,
                                            annee_acad.annee_acad
                                        FROM
                                            payement
                                        LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                        LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                        WHERE payement.id_annee  = ? AND payement.date_payement BETWEEN ? AND ? GROUP BY prevision_frais.type_frais";

                                $sql_2_a = array($annee_acad_fin, $_POST['date_debit'], $_POST['date_fin']);
                                $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                $sql_2->execute($sql_2_a);
    
                                while($data = $sql_2->fetch()){
                                    $pdf->SetFont('Arial','i',8);
                                    $pdf->Ln(4);
                                    $pdf->cell(60, 5, decode_fr(utf8_decode($data['type_frais'])), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.mm($data['mp']), 1, 0, 'L');
                                    $pdf->Ln(1);
                                    $b[] = $data['mp'];
                                }
                                if(array_sum($b) <= 0){$pdf->Ln(4);}else{$pdf->Ln(4);}
                                $pdf->SetFont('Arial','',9);
                                $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                $pdf->Ln(2);
                                $a = array();
                                $b = array();
                            }
                        }
                    }
                }else if(empty($_POST['date_debit']) && empty($_POST['date_fin'])){
                    // selection des l etudiant
                    $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT id_annee FROM etudiants_inscrits WHERE id_annee = ? GROUP BY id_annee");
                    $sel_etud->execute(array($annee_acad_fin));
    
                    if($sel_etud->rowCount() > 0){
                        while ($data_student = $sel_etud->fetch()){
                            $pdf->Ln(5);
                            $pdf->cell(190, 5, decode_fr('Annee Acad. : '.get_annee($annee_acad_fin)), 0, 1, 'L');
                            $pdf->cell(190, 5, decode_fr('Faculte : '.get_fac($ff)), 0, 1, 'L');
                            $pdf->cell(190, 5, decode_fr('Promotion : '.$pp), 0, 1, 'L');
                            $pdf->Ln(2);
                            $pdf->cell(60, 5, 'Type frais', 1, 0, 'L');
                            $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                            $pdf->Ln(1);
    
                            // tableau
                            $a = array();
                            $b = array();
    
                            if($_POST['type_frais'] != "Tous"){
                                $sql_2 = "SELECT
                                            payement.id_payement,
                                            SUM(payement.montant) AS mp,
                                            payement.id_frais,
                                            prevision_frais.type_frais,
                                            annee_acad.annee_acad
                                        FROM
                                            payement
                                        LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                        LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                        WHERE prevision_frais.type_frais  = ? AND payement.id_annee  = ? GROUP BY prevision_frais.type_frais";

                                $sql_2_a = array($_POST['type_frais'], $annee_acad_deb);
                                $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                $sql_2->execute($sql_2_a);

                                while($d = $sql_2->fetch()){
                                    $pdf->SetFont('Arial','i',8);
                                    $pdf->Ln(4);
                                    $pdf->cell(60, 5, utf8_decode(decode_fr($d['type_frais'])), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                    $pdf->Ln(1);
                                    $b[] = $d['mp'];
                                }
                                $pdf->Ln(4);
                                $pdf->SetFont('Arial','',9);
                                $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                $pdf->Ln(2);
                                $b = array();
                            } else {
                                $sql_2 = "SELECT
                                            payement.id_payement,
                                            SUM(payement.montant) AS mp,
                                            payement.id_frais,
                                            prevision_frais.type_frais,
                                            annee_acad.annee_acad
                                        FROM
                                            payement
                                        LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                        LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                        WHERE payement.id_annee  = ? GROUP BY prevision_frais.type_frais";
                                $sql_2_a = array($annee_acad_deb);
                                $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                $sql_2->execute($sql_2_a);
    
                                while($d = $sql_2->fetch()){
                                    $pdf->SetFont('Arial','i',8);
                                    $pdf->Ln(4);
                                    $pdf->cell(60, 5, utf8_decode(decode_fr($d['type_frais'])), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                    $pdf->Ln(1);
                                    $a[] = $d['mp'];
                                }
    
                                $pdf->Ln(4);
                                $pdf->SetFont('Arial','',9);
                                $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                // $pdf->cell(15, 5, '#', 1, 0, 'L');
                                $pdf->Ln(2);
                                $a = array();
                                $b = array();
                            }
                        }
                    }
                }
            }else if($_POST['fac_etudiant'] != "Tous" && $_POST['promotion_etud'] == "Tous"){
                $f = ConnexionBdd::Connecter()->query("SELECT DISTINCT etudiants_inscrits.promotion, options.id_option, sections.section FROM etudiants_inscrits LEFT JOIN options ON etudiants_inscrits.id_option = options.id_option LEFT JOIN sections ON etudiants_inscrits.id_section = sections.id_section WHERE sections.section = '{$_POST['fac_etudiant']}' GROUP BY etudiants_inscrits.promotion");
                while($df = $f->fetch()){
                    $t_promotion[] = $df['promotion'];
                }
                foreach($t_promotion as $pp){
                    if(!empty($_POST['date_debit']) && !empty($_POST['date_fin'])){
                        // la tables etudiants
                        // selection des l etudiant
                        $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT id_annee FROM etudiants_inscrits WHERE id_annee = ? GROUP BY id_annee");
                        $sel_etud->execute(array($annee_acad_fin));
        
                        if($sel_etud->rowCount() > 0){
                            // while ($data_student = $sel_etud->fetch()){
                                $pdf->Ln(5);
                                $pdf->cell(190, 5, decode_fr('Annee Acad. : '.get_annee($annee_acad_fin)), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Section : '.$ff), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Promotion : '.$pp), 0, 1, 'L');
                                $pdf->Ln(2);
                                $pdf->cell(60, 5, 'Type frais', 1, 0, 'L');
                                $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                                $pdf->Ln(1);
        
                                // tableau
                                $a = array();
                                $b = array();
        
                                if($_POST['type_frais'] != "Tous"){
                                    $sql_2 = "SELECT
                                            payement.id_payement,
                                            SUM(payement.montant) AS mp,
                                            payement.id_frais,
                                            prevision_frais.type_frais,
                                            annee_acad.annee_acad,
                                            sections.section,
                                            options.id_option,
                                            options.option_
                                        FROM
                                            payement
                                        LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                        LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                        LEFT JOIN sections ON payement.id_section = sections.id_section
                                        LEFT JOIN options ON payement.id_section = options.id_option
                                        WHERE prevision_frais.type_frais = ? AND annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ? AND payement.date_payement BETWEEN ? AND ?";
                                    $sql_2_a = array($_POST['type_frais'], $annee_acad_deb, $_POST['fac_etudiant'], $pp, $dd, $_POST['date_fin']);
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute($sql_2_a);

                                    while($d = $sql_2->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, utf8_decode(decode_fr($d['type_frais'])), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                        $pdf->Ln(1);
                                        $b[] = $d['mp'];
                                    }
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                    $pdf->Ln(2);
                                    $b = array();
                                } else {
                                    $sql_2 = "SELECT
                                        payement.id_payement,
                                        SUM(payement.montant) AS mp,
                                        payement.id_frais,
                                        prevision_frais.type_frais,
                                        annee_acad.annee_acad,
                                        sections.section,
                                        options.id_option,
                                        options.option_
                                    FROM
                                        payement
                                    LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                    LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                    LEFT JOIN sections ON payement.id_section = sections.id_section
                                    LEFT JOIN options ON payement.id_section = options.id_option
                                    WHERE annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ? AND payement.date_payement BETWEEN ? AND ?";
                                    // die($dd);
                                    $sql_2_a = array($annee_acad_deb, trim($ff), $pp, $dd, $_POST['date_fin']);
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute($sql_2_a);
        
                                    while($d = $sql_2->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, utf8_decode(decode_fr($d['type_frais'])), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                        $pdf->Ln(1);
                                        $a[] = $d['mp'];
                                    }
        
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                    // $pdf->cell(15, 5, '#', 1, 0, 'L');
                                    $pdf->Ln(2);
                                    $a = array();
                                    $b = array();
                                }
                            // }
                        }
                    }else if(empty($_POST['date_debit']) && empty($_POST['date_fin'])){
                        // selection des l etudiant
                        $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT id_annee FROM etudiants_inscrits WHERE id_annee = ? GROUP BY id_annee");
                        $sel_etud->execute(array($annee_acad_fin));
        
                        if($sel_etud->rowCount() > 0){
                            while ($data_student = $sel_etud->fetch()){
                                $pdf->Ln(5);
                                $pdf->cell(190, 5, decode_fr('Annee Acad. : '.get_annee($annee_acad_fin)), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Section : '.$ff), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Promotion : '.$pp), 0, 1, 'L');
                                $pdf->Ln(2);
                                $pdf->cell(60, 5, 'Type frais', 1, 0, 'L');
                                $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                                $pdf->Ln(1);
        
                                // tableau
                                $a = array();
                                $b = array();
        
                                if($_POST['type_frais'] != "Tous"){
                                    $sql_2 = "SELECT
                                            payement.id_payement,
                                            SUM(payement.montant) AS mp,
                                            payement.id_frais,
                                            prevision_frais.type_frais,
                                            annee_acad.annee_acad,
                                            sections.section,
                                            options.id_option,
                                            options.option_
                                        FROM
                                            payement
                                        LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                        LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                        LEFT JOIN sections ON payement.id_section = sections.id_section
                                        LEFT JOIN options ON payement.id_section = options.id_option
                                        WHERE prevision_frais.type_frais = ? AND annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?";
                                    $sql_2_a = array($_POST['type_frais'], $annee_acad_deb, $_POST['fac_etudiant'], $pp);
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute($sql_2_a);

                                    while($d = $sql_2->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, utf8_decode(decode_fr($d['type_frais'])), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                        $pdf->Ln(1);
                                        $b[] = $d['mp'];
                                    }
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                    $pdf->Ln(2);
                                    $b = array();
                                } else {
                                    $sql_2 = "SELECT
                                        payement.id_payement,
                                        SUM(payement.montant) AS mp,
                                        payement.id_frais,
                                        prevision_frais.type_frais,
                                        annee_acad.annee_acad,
                                        sections.section,
                                        options.id_option,
                                        options.option_
                                    FROM
                                        payement
                                    LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                    LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                    LEFT JOIN sections ON payement.id_section = sections.id_section
                                    LEFT JOIN options ON payement.id_section = options.id_option
                                    WHERE annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?";
                                    // die($pp);
                                    $sql_2_a = array($annee_acad_deb, trim($ff), $pp);
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute($sql_2_a);
        
                                    while($d = $sql_2->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, utf8_decode(decode_fr($d['type_frais'])), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                        $pdf->Ln(1);
                                        $a[] = $d['mp'];
                                    }
        
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                    // $pdf->cell(15, 5, '#', 1, 0, 'L');
                                    $pdf->Ln(2);
                                    $a = array();
                                    $b = array();
                                }
                            }
                        }
                    }
                }
            }else if($_POST['fac_etudiant'] != "Tous" && $_POST['promotion_etud'] != "Tous"){
                if(!empty($_POST['date_debit']) && !empty($_POST['date_fin'])){
                    // la tables etudiants
                    // selection des l etudiant
                    $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT id_annee FROM etudiants_inscrits WHERE id_annee = ? GROUP BY id_annee");
                    $sel_etud->execute(array($annee_acad_fin));
    
                    if($sel_etud->rowCount() > 0){
                        // while ($data_student = $sel_etud->fetch()){
                            $pdf->Ln(5);
                            $pdf->cell(190, 5, decode_fr('Annee Acad. : '.get_annee($annee_acad_fin)), 0, 1, 'L');
                            $pdf->cell(190, 5, decode_fr('Section : '.$ff), 0, 1, 'L');
                            $pdf->cell(190, 5, decode_fr('Promotion : '.$pp), 0, 1, 'L');
                            $pdf->Ln(2);
                            $pdf->cell(60, 5, 'Type frais', 1, 0, 'L');
                            $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                            $pdf->Ln(1);
    
                            // tableau
                            $a = array();
                            $b = array();
    
                            if($_POST['type_frais'] != "Tous"){
                                $sql_2 = "SELECT
                                        payement.id_payement,
                                        SUM(payement.montant) AS mp,
                                        payement.id_frais,
                                        prevision_frais.type_frais,
                                        annee_acad.annee_acad,
                                        sections.section,
                                        options.id_option,
                                        options.option_
                                    FROM
                                        payement
                                    LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                    LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                    LEFT JOIN sections ON payement.id_section = sections.id_section
                                    LEFT JOIN options ON payement.id_section = options.id_option
                                    WHERE prevision_frais.type_frais = ? AND annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ? AND payement.date_payement BETWEEN ? AND ?";
                                $sql_2_a = array($_POST['type_frais'], $annee_acad_deb, $_POST['fac_etudiant'], $pp, $dd, $_POST['date_fin']);
                                $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                $sql_2->execute($sql_2_a);

                                while($d = $sql_2->fetch()){
                                    $pdf->SetFont('Arial','i',8);
                                    $pdf->Ln(4);
                                    $pdf->cell(60, 5, utf8_decode(decode_fr($d['type_frais'])), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                    $pdf->Ln(1);
                                    $b[] = $d['mp'];
                                }
                                $pdf->Ln(4);
                                $pdf->SetFont('Arial','',9);
                                $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                $pdf->Ln(2);
                                $b = array();
                            } else {
                                $sql_2 = "SELECT
                                    payement.id_payement,
                                    SUM(payement.montant) AS mp,
                                    payement.id_frais,
                                    prevision_frais.type_frais,
                                    annee_acad.annee_acad,
                                    sections.section,
                                    options.id_option,
                                    options.option_
                                FROM
                                    payement
                                LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                LEFT JOIN sections ON payement.id_section = sections.id_section
                                LEFT JOIN options ON payement.id_section = options.id_option
                                WHERE annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ? AND payement.date_payement BETWEEN ? AND ?";
                                // die($dd);
                                $sql_2_a = array($annee_acad_deb, trim($ff), $pp, $dd, $_POST['date_fin']);
                                $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                $sql_2->execute($sql_2_a);
    
                                while($d = $sql_2->fetch()){
                                    $pdf->SetFont('Arial','i',8);
                                    $pdf->Ln(4);
                                    $pdf->cell(60, 5, utf8_decode(decode_fr($d['type_frais'])), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                    $pdf->Ln(1);
                                    $a[] = $d['mp'];
                                }
    
                                $pdf->Ln(4);
                                $pdf->SetFont('Arial','',9);
                                $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                // $pdf->cell(15, 5, '#', 1, 0, 'L');
                                $pdf->Ln(2);
                                $a = array();
                                $b = array();
                            }
                        // }
                    }
                }else if(empty($_POST['date_debit']) && empty($_POST['date_fin'])){
                    // selection des l etudiant
                    $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT id_annee FROM etudiants_inscrits WHERE id_annee = ? GROUP BY id_annee");
                    $sel_etud->execute(array($annee_acad_fin));
    
                    if($sel_etud->rowCount() > 0){
                        while ($data_student = $sel_etud->fetch()){
                            $pdf->Ln(5);
                            $pdf->cell(190, 5, decode_fr('Annee Acad. : '.get_annee($annee_acad_fin)), 0, 1, 'L');
                            $pdf->cell(190, 5, decode_fr('Section : '.$ff), 0, 1, 'L');
                            $pdf->cell(190, 5, decode_fr('Promotion : '.$pp), 0, 1, 'L');
                            $pdf->Ln(2);
                            $pdf->cell(60, 5, 'Type frais', 1, 0, 'L');
                            $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                            $pdf->Ln(1);
    
                            // tableau
                            $a = array();
                            $b = array();
    
                            if($_POST['type_frais'] != "Tous"){
                                $sql_2 = "SELECT
                                        payement.id_payement,
                                        SUM(payement.montant) AS mp,
                                        payement.id_frais,
                                        prevision_frais.type_frais,
                                        annee_acad.annee_acad,
                                        sections.section,
                                        options.id_option,
                                        options.option_
                                    FROM
                                        payement
                                    LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                    LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                    LEFT JOIN sections ON payement.id_section = sections.id_section
                                    LEFT JOIN options ON payement.id_section = options.id_option
                                    WHERE prevision_frais.type_frais = ? AND annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?";
                                $sql_2_a = array($_POST['type_frais'], $annee_acad_deb, $_POST['fac_etudiant'], $pp);
                                $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                $sql_2->execute($sql_2_a);

                                while($d = $sql_2->fetch()){
                                    $pdf->SetFont('Arial','i',8);
                                    $pdf->Ln(4);
                                    $pdf->cell(60, 5, utf8_decode(decode_fr($d['type_frais'])), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                    $pdf->Ln(1);
                                    $b[] = $d['mp'];
                                }
                                $pdf->Ln(4);
                                $pdf->SetFont('Arial','',9);
                                $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                $pdf->Ln(2);
                                $b = array();
                            } else {
                                $sql_2 = "SELECT
                                    payement.id_payement,
                                    SUM(payement.montant) AS mp,
                                    payement.id_frais,
                                    prevision_frais.type_frais,
                                    annee_acad.annee_acad,
                                    sections.section,
                                    options.id_option,
                                    options.option_
                                FROM
                                    payement
                                LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                LEFT JOIN sections ON payement.id_section = sections.id_section
                                LEFT JOIN options ON payement.id_section = options.id_option
                                WHERE annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?";
                                // die($pp);
                                $sql_2_a = array($annee_acad_deb, trim($ff), $pp);
                                $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                $sql_2->execute($sql_2_a);
    
                                while($d = $sql_2->fetch()){
                                    $pdf->SetFont('Arial','i',8);
                                    $pdf->Ln(4);
                                    $pdf->cell(60, 5, utf8_decode(decode_fr($d['type_frais'])), 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                    $pdf->Ln(1);
                                    $a[] = $d['mp'];
                                }
    
                                $pdf->Ln(4);
                                $pdf->SetFont('Arial','',9);
                                $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                // $pdf->cell(15, 5, '#', 1, 0, 'L');
                                $pdf->Ln(2);
                                $a = array();
                                $b = array();
                            }
                        }
                    }
                }
            }else if($_POST['fac_etudiant'] = "Tous" && $_POST['promotion_etud'] !="Tous"){
                foreach($t_fac as $ff){
                    if(!empty($_POST['date_debit']) && !empty($_POST['date_fin'])){
                        // la tables etudiants
                        // selection des l etudiant
                        $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT id_annee FROM etudiants_inscrits WHERE id_annee = ? GROUP BY id_annee");
                        $sel_etud->execute(array($annee_acad_fin));
        
                        if($sel_etud->rowCount() > 0){
                            // while ($data_student = $sel_etud->fetch()){
                                $pdf->Ln(5);
                                $pdf->cell(190, 5, decode_fr('Annee Acad. : '.get_annee($annee_acad_fin)), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Section : '.$ff), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Promotion : '.$pp), 0, 1, 'L');
                                $pdf->Ln(2);
                                $pdf->cell(60, 5, 'Type frais', 1, 0, 'L');
                                $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                                $pdf->Ln(1);
        
                                // tableau
                                $a = array();
                                $b = array();
        
                                if($_POST['type_frais'] != "Tous"){
                                    $sql_2 = "SELECT
                                            payement.id_payement,
                                            SUM(payement.montant) AS mp,
                                            payement.id_frais,
                                            prevision_frais.type_frais,
                                            annee_acad.annee_acad,
                                            sections.section,
                                            options.id_option,
                                            options.option_
                                        FROM
                                            payement
                                        LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                        LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                        LEFT JOIN sections ON payement.id_section = sections.id_section
                                        LEFT JOIN options ON payement.id_section = options.id_option
                                        WHERE prevision_frais.type_frais = ? AND annee_acad.id_annee = ? AND sections.section = ? AND  payement.date_payement BETWEEN ? AND ?";
                                    $sql_2_a = array($_POST['type_frais'], $annee_acad_deb, $ff, $dd, $_POST['date_fin']);
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute($sql_2_a);
    
                                    while($d = $sql_2->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, utf8_decode(decode_fr($d['type_frais'])), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                        $pdf->Ln(1);
                                        $b[] = $d['mp'];
                                    }
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                    $pdf->Ln(2);
                                    $b = array();
                                } else {
                                    $sql_2 = "SELECT
                                        payement.id_payement,
                                        SUM(payement.montant) AS mp,
                                        payement.id_frais,
                                        prevision_frais.type_frais,
                                        annee_acad.annee_acad,
                                        sections.section,
                                        options.id_option,
                                        options.option_
                                    FROM
                                        payement
                                    LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                    LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                    LEFT JOIN sections ON payement.id_section = sections.id_section
                                    LEFT JOIN options ON payement.id_section = options.id_option
                                    WHERE annee_acad.id_annee = ? AND sections.section = ? AND payement.date_payement BETWEEN ? AND ?";
                                    // die($dd);
                                    $sql_2_a = array($annee_acad_deb, trim($ff), $dd, $_POST['date_fin']);
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute($sql_2_a);
        
                                    while($d = $sql_2->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, utf8_decode(decode_fr($d['type_frais'])), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                        $pdf->Ln(1);
                                        $a[] = $d['mp'];
                                    }
        
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                    // $pdf->cell(15, 5, '#', 1, 0, 'L');
                                    $pdf->Ln(2);
                                    $a = array();
                                    $b = array();
                                }
                            // }
                        }
                    }else if(empty($_POST['date_debit']) && empty($_POST['date_fin'])){
                        // selection des l etudiant
                        $sel_etud = ConnexionBdd::Connecter()->prepare("SELECT id_annee FROM etudiants_inscrits WHERE id_annee = ? GROUP BY id_annee");
                        $sel_etud->execute(array($annee_acad_fin));
        
                        if($sel_etud->rowCount() > 0){
                            while ($data_student = $sel_etud->fetch()){
                                $pdf->Ln(5);
                                $pdf->cell(190, 5, decode_fr('Annee Acad. : '.get_annee($annee_acad_fin)), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Section : '.$ff), 0, 1, 'L');
                                $pdf->cell(190, 5, decode_fr('Promotion : '.$pp), 0, 1, 'L');
                                $pdf->Ln(2);
                                $pdf->cell(60, 5, 'Type frais', 1, 0, 'L');
                                $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                                $pdf->Ln(1);
        
                                // tableau
                                $a = array();
                                $b = array();
        
                                if($_POST['type_frais'] != "Tous"){
                                    $sql_2 = "SELECT
                                            payement.id_payement,
                                            SUM(payement.montant) AS mp,
                                            payement.id_frais,
                                            prevision_frais.type_frais,
                                            annee_acad.annee_acad,
                                            sections.section,
                                            options.id_option,
                                            options.option_
                                        FROM
                                            payement
                                        LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                        LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                        LEFT JOIN sections ON payement.id_section = sections.id_section
                                        LEFT JOIN options ON payement.id_section = options.id_option
                                        WHERE prevision_frais.type_frais = ? AND annee_acad.id_annee = ? AND sections.section = ? AND options.promotion = ?";
                                    $sql_2_a = array($_POST['type_frais'], $annee_acad_deb, $ff);
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute($sql_2_a);
    
                                    while($d = $sql_2->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, utf8_decode(decode_fr($d['type_frais'])), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                        $pdf->Ln(1);
                                        $b[] = $d['mp'];
                                    }
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($b), 1, 0, 'L');
                                    $pdf->Ln(2);
                                    $b = array();
                                } else {
                                    $sql_2 = "SELECT
                                        payement.id_payement,
                                        SUM(payement.montant) AS mp,
                                        payement.id_frais,
                                        prevision_frais.type_frais,
                                        annee_acad.annee_acad,
                                        sections.section,
                                        options.id_option,
                                        options.option_
                                    FROM
                                        payement
                                    LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                                    LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                                    LEFT JOIN sections ON payement.id_section = sections.id_section
                                    LEFT JOIN options ON payement.id_section = options.id_option
                                    WHERE annee_acad.id_annee = ? AND sections.section = ?";
                                    // die($pp);
                                    $sql_2_a = array($annee_acad_deb, trim($ff));
                                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                    $sql_2->execute($sql_2_a);
        
                                    while($d = $sql_2->fetch()){
                                        $pdf->SetFont('Arial','i',8);
                                        $pdf->Ln(4);
                                        $pdf->cell(60, 5, utf8_decode(decode_fr($d['type_frais'])), 1, 0, 'L');
                                        $pdf->cell(30, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                                        $pdf->Ln(1);
                                        $a[] = $d['mp'];
                                    }
        
                                    $pdf->Ln(4);
                                    $pdf->SetFont('Arial','',9);
                                    $pdf->cell(60, 5, 'Total', 1, 0, 'L');
                                    $pdf->cell(30, 5, '$ '.array_sum($a), 1, 0, 'L');
                                    // $pdf->cell(15, 5, '#', 1, 0, 'L');
                                    $pdf->Ln(2);
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

    // rapport mensuel
    if(isset($_POST['btn_rap_mensuel'])){
        $pdf = new FPDF('L', 'mm', 'A4');
        $annee_acad_deb = $_POST['annee_acad_deb'];
        $annee_acad_fin = $_POST['annee_acad_deb'];

        $pdf->AddPage();
        $pdf->SetFont('Arial','B',12);

        $pdf->cell(270,6,'',0,1,'C');
        $pdf->cell(270,6, decode_fr("UNIVERSITE DE GOMA"),0,1,'C');
        $pdf->cell(270,6, decode_fr("«UNIGOM»"),0,1,'C');
        $pdf->SetFont('Arial','',11);
        $pdf->cell(270,6, decode_fr("BP 204 Goma (RDC)"),0,1,'C');
        $pdf->cell(270,6, decode_fr("BP 2277 Gisenyi (RWANDA)"),0,1,'C');
        $pdf->cell(270,6, decode_fr("E-mail : rectorat@unigom.ac.cd"),0,1,'C');
        $pdf->SetFont('Arial','BI',11);
        $pdf->cell(270,4, decode_fr("site web : www.unigom.ac.cd"),0,1,'C', false, 'www.unigom.ac.cd');
        $pdf->SetFont('Arial','',11);

        $pdf->Ln(5);
        $pdf->cell(270,5,'Pax ex scientia splendeat',0,1,'L');
        // logo de la faculte
        $pdf->Image("../../images/UNIGOM_W260px.jpg", 15,16,30, 30);
        $pdf->SetFillColor(12, 12, 200);
        $pdf->SetTextColor(12, 12, 200);
        $pdf->cell(270,1 ,"",1,0,'C', true);
        $pdf->Ln(3);

        $pdf->SetFont('Arial','',10);
        $pdf->SetTextColor(0,0,0);
        $pdf->cell(60, 5, decode_fr('Année Academique : '.verify(get_annee($_POST['annee_acad_deb']))), 0, 1, 'L');
        $pdf->Ln(3);

        $pdf->SetFont('Arial','BU',10);
        $an = get_annee($annee_acad_deb);
        // annee academique moins un
        $year_below = $an[5].''.$an[6].''.$an[7].''.$an[8];
        $pdf->cell(250, 10, decode_fr('RAPPORT MENSUEL DES PAYENMETS '.get_annee($annee_acad_deb).' de '.$year_below), 0, 1, 'C');
        $pdf->SetFont('Arial','',10);
        // lw titre

        $mois = array("Janvier", "Fevrier", "Mars", "Avril","Mai", "Juin", "Juillet", "Aout", "Septembre","Octombre", "Novembre", "Decembre");
        $n_mois = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);
        $tot_array = array();

        function nm($v){
            if(!empty($v)){
                return $v;
            }else{
                return '$';
            }
        }
        $pdf->SetFont('Arial','',8);

        $an = get_annee($annee_acad_deb);
        // annee academique moins un
        $year_below = $an[5].''.$an[6].''.$an[7].''.$an[8];
        // die($year_below);
        $sel_m = ConnexionBdd::Connecter()->prepare("SELECT * FROM `payement` WHERE  id_annee = ?");
        $sel_m->execute(array($annee_acad_deb));
        if($sel_m->rowCount() > 0){
            $pdf->cell(60, 5, "Type de frais", 1, 0, 'L');
            foreach($mois as $m){
                $pdf->cell(17, 5, $m, 1, 0, 'L');
            }
            // $pdf->cell(20, 5, "Tot", 1, 0, 'L');
            $pdf->Ln(5);

            // liste de frais enregistrer dans la base de donnees
            $list_f = array();

            $f = ConnexionBdd::Connecter()->prepare("SELECT DISTINCT prevision_frais.id_frais, prevision_frais.type_frais FROM prevision_frais WHERE prevision_frais.id_annee = ? GROUP BY prevision_frais.type_frais");
            $f->execute(array($annee_acad_deb));
            while($d = $f->fetch()){
                $list_f[] = utf8_decode($d['type_frais']);
            }

            foreach($list_f as $frais){
                $pdf->cell(60, 5, decode_fr($frais), 1, 0, 'L');
                foreach($n_mois as $n){
                    // $pdf->cell(20, 5, decode_fr($n), 1, 0, 'L');
                    $sql = "SELECT
                                payement.id_payement,
                                SUM(payement.montant) AS montant,
                                prevision_frais.id_frais,
                                annee_acad.id_annee
                            FROM
                                payement
                            LEFT JOIN prevision_frais ON payement.id_frais = prevision_frais.id_frais
                            LEFT JOIN annee_acad ON payement.id_annee = annee_acad.id_annee
                            WHERE
                                MONTH(payement.date_payement) = ? AND YEAR(payement.date_payement) = ? AND payement.id_annee = ? AND prevision_frais.type_frais = ?
                            GROUP BY
                                prevision_frais.type_frais";
                    $sel_m = ConnexionBdd::Connecter()->prepare($sql);

                    $an = get_annee($annee_acad_deb);
                    $year_below = $an[5].''.$an[6].''.$an[7].''.$an[8];
                    // die($year_below);
                    $sel_m->execute(array($n, $year_below, $annee_acad_deb, $frais));

                    if($sel_m->rowCount() >= 1){
                        while($data = $sel_m->fetch()){
                            $pdf->cell(17, 5, $data['montant'], 1, 0, 'L');
                            $tot_array[] = $data['montant'];
                            
                        }//$pdf->cell(20, 5, array_sum($tot_array), 1, 0, 'L');
                        $tot_array = array();
                    }else{
                        $pdf->cell(17, 5, '0', 1, 0, 'L');
                    }
                }
                $pdf->Ln(5);
            }
        }

        $pdf->Ln(15);
        $pdf->SetFont('Arial','',10);
	    $pdf->cell(300,20, decode_fr('par : '.$_SESSION['data']['noms'].'; le '.date('d M Y')),0,1,'C');
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
    <body  id="page-top">
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
                                                    <label for="">Section</label>
                                                </div>
                                                <div class="col-sm-12 col-md-9 col-lg-7">
                                                    <select class="form-control" name="fac_etudiant" id="fac_etudiant">
                                                        <option value="Tous" selected>Tous</option>
                                                        <?php
                                                            $sql = "SELECT DISTINCT section FROM sections";
                                                            $state = ConnexionBdd::Connecter()->query($sql);
                                                            while($d = $state->fetch()){
                                                                echo' 
                                                                    <option value="'.utf8_decode($d['section']).'">'.utf8_decode($d['section']).'</option>';
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
                                                    <label for="">Période de payement</label>
                                                </div>
                                                <div class="col-sm-12 col-md-9 col-lg-7">
                                                    Du <input type="date" name="date_debit" id="date_debit" class=""> 
                                                    Au <input type="date" name="date_fin" id="date_fin" class=""> 
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
                                        <hr/>
                                        <p>Rapport Mensuel</p>
                                        <button type="submit" id="btn_rap_mensuel" class="btn-link text-left" name="btn_rap_mensuel" value="btn_rap_mensuel">Rapport Mensuel</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer -->
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
    </body>
</html>