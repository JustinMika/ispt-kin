<?php
    session_start();
    require_once '../../includes/ConnexionBdd.class.php';
    require_once '../../includes/verification.class.php';
    //verification des sessions
    require_once './sessions.php';
    require './fpdf/fpdf.php';
    require_once '../../includes/log_user.class.php';
    $p = "Gestion des honoraires";

    function restruct_user(){
        if($_SESSION['data']['fonction'] != "" && $_SESSION['data']['access'] !=""){
            if($_SESSION['data']['fonction'] && $_SESSION['data']['access'] && $_SESSION['data']['access'] == "Admin"){
                return '';
            }else{
                return 'display:none';
            }
        }else{
            header("location:../index.php", true, 301);
        }
        
    }

    function restruct_r_r(){
        if($_SESSION['data']['fonction'] != "" && $_SESSION['data']['access'] !=""){
            if($_SESSION['data']['fonction'] && $_SESSION['data']['access'] && $_SESSION['data']['access'] == "Admin" || $_SESSION['data']['access'] == "AB"){
                return '';
            }else{
                return 'display:none';
            }
        }else{
            header("Location:../index.php", true, 301);
        }
    }

    function verify($var){
        if(isset($var) && !empty($var)){
            return $var;
        }else{
            return ' - ';
        }

    }

    function all($pdf){
        $pdf->AddPage();
        $pdf->SetFont('Arial','B',12);

        $pdf->cell(300,10,'',0,1,'C');
        $pdf->cell(300,6, decode_fr(strtoupper("institut superieur pedagogique et technique de kinshasa")),0,1,'C');
        $pdf->SetFont('Arial','',11); //Mail : info@isptkin.ac.cd
        $pdf->cell(300,6, decode_fr("ISPT-KIN"),0,1,'C');
        $pdf->cell(300,6, decode_fr("E-mail : info@isptkin.ac.cd"),0,1,'C');
        $pdf->cell(300,6, decode_fr("site web : www.isptkin.ac.cd"),0,1,'C', false, 'www.isptkin.ac.cd');
        $pdf->Ln(5);
        // logo de la faculte
        $pdf->Image("../../images/ispt_kin.png", 10,15,25, 25);
        $pdf->Ln(2);
        $pdf->cell(280,1 ,"",1,1,'C', true);

        // print_r($_POST);

        $pdf->SetFont('Arial','',10);
        $pdf->SetTextColor(0,0,0);
        $pdf->SetFillColor(0,0,0);

        $pdf->cell(60, 5, decode_fr('Année Academique : '.verify($_POST['annee_acad'])), 0, 1, 'L');
        $pdf->cell(60, 5, decode_fr('Faculté                     : '.verify($_POST['faculte'])), 0, 1, 'L');
    }

    function footer($pdf){
        $pdf->Ln(15);
        $pdf->SetFont('Arial','',10);
        $pdf->cell(300,20, decode_fr('par : '.$_SESSION['data']['noms'].'; le '.date('d/M/Y')),0,1,'C');
        $pdf->Output();
    }

    $a = array();
    $b = array();
    $c = array();
    $d = array();

    if(isset($_POST['btn_fac'])){
        $pdf = new FPDF('L', 'mm', 'A4');
        $annee_acad = $_POST['annee_acad'];

        all($pdf);
        $pdf->SetFont('Arial','BU',10);
        $pdf->cell(270, 10, decode_fr('RAPPORT GESTION DES HONORAIRES'), 0, 1, 'C');
        $pdf->SetFont('Arial','',10);
        // corps
        if($_POST['faculte'] == "Tous" && $_POST['prestation'] == "Tous" && $_POST['type_enseign'] == "Tous"){
            $list_fac = array();

            $fac = ConnexionBdd::Connecter()->query("SELECT DISTINCT fac FROM faculte");
            while($data = $fac->fetch()){
                $list_fac[] = $data['fac'];
            }

            foreach ($list_fac as $fac) {
                $pdf->Ln(3);
                $pdf->SetFont('Arial','BU',10);
                $pdf->cell(60, 5, decode_fr(''.verify($fac)), 0, 1, 'L');
                $pdf->Ln(2);
                $pdf->SetFont('Arial','B',10);
                $pdf->cell(60, 5,'Enseignants',1,0,'C');
                $pdf->cell(15, 5,'Grade',1,0,'C');
                $pdf->cell(70, 5,'Cours',1,0,'C');
                // $pdf->cell(50, 5,'Faculte',1,0,'C');
                $pdf->cell(18, 5,'V. Horaire',1,0,'C');
                $pdf->cell(25, 5,'Type Enseign.',1,0,'C');
                $pdf->cell(25, 5,'Type Prest.',1,0,'C');
                $pdf->cell(15, 5,'Taux',1,0,'C');
                $pdf->cell(15, 5,'Total',1,0,'C');
                $pdf->cell(20, 5, decode_fr('Total payé'),1,0,'C');
                $pdf->cell(15, 5,'Solde',1,0,'C');
                $pdf->Ln(5);

                $req = ConnexionBdd::Connecter()->prepare("SELECT * FROM gest_honoraire WHERE annee_acad = ? AND faculte = ?");
                $req->execute(array($annee_acad, $fac));

                $pdf->SetFont('Arial','',7);
                while ($res1=$req->fetch()) {
                    $pdf->SetFont('Arial','',9);
                    $pdf->cell(60, 5, decode_fr($res1['noms_ens']), 1, 0, 'L');
                    $pdf->cell(15, 5, decode_fr($res1['grade_ens']), 1, 0, 'L');
                    $pdf->cell(70, 5, decode_fr($res1['cours']), 1, 0, 'L');
                    // $pdf->cell(50, 5, decode_fr($res1['faculte']), 1, 0, 'L');
                    $pdf->cell(18, 5, decode_fr($res1['heure_th'] + $res1['heure_pr']), 1, 0, 'L');
                    $pdf->cell(25, 5, decode_fr($res1['type_enseig']), 1, 0, 'L');
                    $pdf->cell(25, 5, decode_fr($res1['prestation']), 1, 0, 'L');
                    $pdf->cell(15, 5, decode_fr('$'.$res1['taux']), 1, 0, 'L');
                    $pdf->cell(15, 5, decode_fr('$'.$res1['total']), 1, 0, 'L');
                    $pdf->cell(20, 5, decode_fr('$'.$res1['total_payer']), 1, 0, 'L');
                    $pdf->cell(15, 5, decode_fr('$'.floatval($res1['total']-$res1['total_payer'])), 1, 0, 'L');
                    $pdf->Ln(5);
                    $a[] = $res1['heure_th'] + $res1['heure_pr'];
                    $b[] = $res1['total'];
                    $c[] = $res1['total_payer'];
                    $d[] = floatval($res1['total']-$res1['total_payer']);
                }
                $pdf->SetFont('Arial','',9);
                $pdf->cell(60, 5, decode_fr("Total"), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr("-"), 1, 0, 'L', true);
                $pdf->cell(70, 5, decode_fr("-"), 1, 0, 'L', true);
                $pdf->cell(18, 5, decode_fr(array_sum($a)), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr("-"), 1, 0, 'L', true);
                $pdf->cell(25, 5, decode_fr("-"), 1, 0, 'L', true);
                $pdf->cell(25, 5, decode_fr("-"), 1, 0, 'L', true);
                $pdf->cell(15, 5, decode_fr('$'.array_sum($b)), 1, 0, 'L');
                $pdf->cell(20, 5, decode_fr('$'.array_sum($c)), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('$'.array_sum($d)), 1, 0, 'L');
                $pdf->Ln(5);
                $a = array();
                $b = array();
                $c = array();
                $d = array();
            }

            // sous-total
            $pdf->Ln(5);
            $pdf->SetFont('Arial','B',12);
            $pdf->cell(100, 5, decode_fr('TOTAL GENERAL'), 0, 1, 'C');
            $pdf->Ln(2);
            $pdf->SetFont('Arial','B',10);
            $pdf->cell(60, 5,'Faculte',1,0,'C');
            $pdf->cell(15, 5,'Total',1,0,'C');
            $pdf->cell(20, 5, decode_fr('Total payé'),1,0,'C');
            $pdf->cell(15, 5,'Solde',1,0,'C');
            $pdf->Ln(5);

            $req = ConnexionBdd::Connecter()->prepare("SELECT SUM(total) AS total, SUM(total_payer) AS total_payer, faculte  FROM gest_honoraire WHERE annee_acad = ? GROUP BY faculte");
            $req->execute(array($annee_acad));

            $pdf->SetFont('Arial','',7);
            while ($res1=$req->fetch()) {
                $pdf->SetFont('Arial','',9);
                $pdf->cell(60, 5, decode_fr($res1['faculte']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('$'.$res1['total']), 1, 0, 'L');
                $pdf->cell(20, 5, decode_fr('$'.$res1['total_payer']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('$'.floatval($res1['total']-$res1['total_payer'])), 1, 0, 'L');
                $pdf->Ln(5);
                // $a[] = $res1['heure_th'] + $res1['heure_pr'];
                $b[] = $res1['total'];
                $c[] = $res1['total_payer'];
                $d[] = floatval($res1['total']-$res1['total_payer']);
            }
            $pdf->SetFont('Arial','',9);
            $pdf->cell(60, 5, decode_fr("Total"), 1, 0, 'L');
            $pdf->cell(15, 5, decode_fr('$'.array_sum($b)), 1, 0, 'L');
            $pdf->cell(20, 5, decode_fr('$'.array_sum($c)), 1, 0, 'L');
            $pdf->cell(15, 5, decode_fr('$'.array_sum($d)), 1, 0, 'L');
            $pdf->Ln(5);
            $a = array();
            $b = array();
            $c = array();
            $d = array();
        }else if($_POST['faculte'] == "Tous" && $_POST['prestation'] != "Tous" && $_POST['type_enseign'] == "Tous"){
            $list_fac = array();

            $fac = ConnexionBdd::Connecter()->query("SELECT DISTINCT fac FROM faculte");
            while($data = $fac->fetch()){
                $list_fac[] = $data['fac'];
            }

            foreach ($list_fac as $fac) {
                $pdf->Ln(3);
                $pdf->SetFont('Arial','BU',10);
                $pdf->cell(60, 5, decode_fr(''.verify($fac)), 0, 1, 'L');
                $pdf->Ln(2);
                $pdf->SetFont('Arial','B',10);
                $pdf->cell(60, 5,'Enseignants',1,0,'C');
                $pdf->cell(15, 5,'Grade',1,0,'C');
                $pdf->cell(70, 5,'Cours',1,0,'C');
                // $pdf->cell(50, 5,'Faculte',1,0,'C');
                $pdf->cell(18, 5,'V. Horaire',1,0,'C');
                $pdf->cell(25, 5,'Type Enseign.',1,0,'C');
                $pdf->cell(25, 5,'Type Prest.',1,0,'C');
                $pdf->cell(15, 5,'Taux',1,0,'C');
                $pdf->cell(15, 5,'Total',1,0,'C');
                $pdf->cell(20, 5, decode_fr('Total payé'),1,0,'C');
                $pdf->cell(15, 5,'Solde',1,0,'C');
                $pdf->Ln(5);

                $req = ConnexionBdd::Connecter()->prepare("SELECT * FROM gest_honoraire WHERE annee_acad = ? AND
                 faculte = ? AND prestation = ?");
                $req->execute(array($annee_acad, $fac, $_POST['prestation']));

                $pdf->SetFont('Arial','',7);
                while ($res1=$req->fetch()) {
                    $pdf->SetFont('Arial','',9);
                    $pdf->cell(60, 5, decode_fr($res1['noms_ens']), 1, 0, 'L');
                    $pdf->cell(15, 5, decode_fr($res1['grade_ens']), 1, 0, 'L');
                    $pdf->cell(70, 5, decode_fr($res1['cours']), 1, 0, 'L');
                    // $pdf->cell(50, 5, decode_fr($res1['faculte']), 1, 0, 'L');
                    $pdf->cell(18, 5, decode_fr($res1['heure_th'] + $res1['heure_pr']), 1, 0, 'L');
                    $pdf->cell(25, 5, decode_fr($res1['type_enseig']), 1, 0, 'L');
                    $pdf->cell(25, 5, decode_fr($res1['prestation']), 1, 0, 'L');
                    $pdf->cell(15, 5, decode_fr('$'.$res1['taux']), 1, 0, 'L');
                    $pdf->cell(15, 5, decode_fr('$'.$res1['total']), 1, 0, 'L');
                    $pdf->cell(20, 5, decode_fr('$'.$res1['total_payer']), 1, 0, 'L');
                    $pdf->cell(15, 5, decode_fr('$'.floatval($res1['total']-$res1['total_payer'])), 1, 0, 'L');
                    $pdf->Ln(5);
                    $a[] = $res1['heure_th'] + $res1['heure_pr'];
                    $b[] = $res1['total'];
                    $c[] = $res1['total_payer'];
                    $d[] = floatval($res1['total']-$res1['total_payer']);
                }
                $pdf->SetFont('Arial','',9);
                $pdf->cell(60, 5, decode_fr("Total"), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr("-"), 1, 0, 'L', true);
                $pdf->cell(70, 5, decode_fr("-"), 1, 0, 'L', true);
                $pdf->cell(18, 5, decode_fr(array_sum($a)), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr("-"), 1, 0, 'L', true);
                $pdf->cell(25, 5, decode_fr("-"), 1, 0, 'L', true);
                $pdf->cell(25, 5, decode_fr("-"), 1, 0, 'L', true);
                $pdf->cell(15, 5, decode_fr('$'.array_sum($b)), 1, 0, 'L');
                $pdf->cell(20, 5, decode_fr('$'.array_sum($c)), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('$'.array_sum($d)), 1, 0, 'L');
                $pdf->Ln(5);
                $a = array();
                $b = array();
                $c = array();
                $d = array();
            }

            // sous-total
            // sous-total
            $pdf->Ln(5);
            $pdf->SetFont('Arial','B',12);
            $pdf->cell(100, 5, decode_fr('TOTAL GENERAL'), 0, 1, 'C');
            $pdf->Ln(2);
            $pdf->SetFont('Arial','B',10);
            $pdf->cell(50, 5,'Faculte',1,0,'C');
            $pdf->cell(15, 5,'Total',1,0,'C');
            $pdf->cell(20, 5, decode_fr('Total payé'),1,0,'C');
            $pdf->cell(15, 5,'Solde',1,0,'C');
            $pdf->Ln(5);

            $req = ConnexionBdd::Connecter()->prepare("SELECT SUM(total) AS total, SUM(total_payer) AS total_payer, faculte  FROM gest_honoraire WHERE annee_acad = ? GROUP BY faculte");
            $req->execute(array($annee_acad));

            $pdf->SetFont('Arial','',7);
            while ($res1=$req->fetch()) {
                $pdf->SetFont('Arial','',9);
                $pdf->cell(50, 5, decode_fr($res1['faculte']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('$'.$res1['total']), 1, 0, 'L');
                $pdf->cell(20, 5, decode_fr('$'.$res1['total_payer']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('$'.floatval($res1['total']-$res1['total_payer'])), 1, 0, 'L');
                $pdf->Ln(5);
                $a[] = '';
                $b[] = $res1['total'];
                $c[] = $res1['total_payer'];
                $d[] = floatval($res1['total']-$res1['total_payer']);
            }
            $pdf->SetFont('Arial','',9);
            $pdf->cell(50, 5, decode_fr("Total"), 1, 0, 'L');
            $pdf->cell(15, 5, decode_fr('$'.array_sum($b)), 1, 0, 'L');
            $pdf->cell(20, 5, decode_fr('$'.array_sum($c)), 1, 0, 'L');
            $pdf->cell(15, 5, decode_fr('$'.array_sum($d)), 1, 0, 'L');
            $pdf->Ln(5);
            $a = array();
            $b = array();
            $c = array();
            $d = array();
        }else if($_POST['faculte'] == "Tous" && $_POST['prestation'] == "Tous" && $_POST['type_enseign'] != "Tous"){
            $list_fac = array();

            $fac = ConnexionBdd::Connecter()->query("SELECT DISTINCT fac FROM faculte");
            while($data = $fac->fetch()){
                $list_fac[] = $data['fac'];
            }

            foreach ($list_fac as $fac) {
                $pdf->Ln(3);
                $pdf->SetFont('Arial','BU',10);
                $pdf->cell(60, 5, decode_fr(''.verify($fac)), 0, 1, 'L');
                $pdf->Ln(2);
                $pdf->SetFont('Arial','B',10);
                $pdf->cell(60, 5,'Enseignants',1,0,'C');
                $pdf->cell(15, 5,'Grade',1,0,'C');
                $pdf->cell(70, 5,'Cours',1,0,'C');
                // $pdf->cell(50, 5,'Faculte',1,0,'C');
                $pdf->cell(18, 5,'V. Horaire',1,0,'C');
                $pdf->cell(25, 5,'Type Enseign.',1,0,'C');
                $pdf->cell(25, 5,'Type Prest.',1,0,'C');
                $pdf->cell(15, 5,'Taux',1,0,'C');
                $pdf->cell(15, 5,'Total',1,0,'C');
                $pdf->cell(20, 5, decode_fr('Total payé'),1,0,'C');
                $pdf->cell(15, 5,'Solde',1,0,'C');
                $pdf->Ln(5);

                $req = ConnexionBdd::Connecter()->prepare("SELECT * FROM gest_honoraire WHERE annee_acad = ? AND
                 faculte = ? AND type_enseig = ?");
                $req->execute(array($annee_acad, $fac, $_POST['type_enseign']));

                $pdf->SetFont('Arial','',7);
                while ($res1=$req->fetch()) {
                    $pdf->SetFont('Arial','',9);
                    $pdf->cell(60, 5, decode_fr($res1['noms_ens']), 1, 0, 'L');
                    $pdf->cell(15, 5, decode_fr($res1['grade_ens']), 1, 0, 'L');
                    $pdf->cell(70, 5, decode_fr($res1['cours']), 1, 0, 'L');
                    // $pdf->cell(50, 5, decode_fr($res1['faculte']), 1, 0, 'L');
                    $pdf->cell(18, 5, decode_fr($res1['heure_th'] + $res1['heure_pr']), 1, 0, 'L');
                    $pdf->cell(25, 5, decode_fr($res1['type_enseig']), 1, 0, 'L');
                    $pdf->cell(25, 5, decode_fr($res1['prestation']), 1, 0, 'L');
                    $pdf->cell(15, 5, decode_fr('$'.$res1['taux']), 1, 0, 'L');
                    $pdf->cell(15, 5, decode_fr('$'.$res1['total']), 1, 0, 'L');
                    $pdf->cell(20, 5, decode_fr('$'.$res1['total_payer']), 1, 0, 'L');
                    $pdf->cell(15, 5, decode_fr('$'.floatval($res1['total']-$res1['total_payer'])), 1, 0, 'L');
                    $pdf->Ln(5);
                    $a[] = $res1['heure_th'] + $res1['heure_pr'];
                    $b[] = $res1['total'];
                    $c[] = $res1['total_payer'];
                    $d[] = floatval($res1['total']-$res1['total_payer']);
                }
                $pdf->SetFont('Arial','',9);
                $pdf->cell(60, 5, decode_fr("Total"), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr("-"), 1, 0, 'L', true);
                $pdf->cell(70, 5, decode_fr("-"), 1, 0, 'L', true);
                $pdf->cell(18, 5, decode_fr(array_sum($a)), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr("-"), 1, 0, 'L', true);
                $pdf->cell(25, 5, decode_fr("-"), 1, 0, 'L', true);
                $pdf->cell(25, 5, decode_fr("-"), 1, 0, 'L', true);
                $pdf->cell(15, 5, decode_fr('$'.array_sum($b)), 1, 0, 'L');
                $pdf->cell(20, 5, decode_fr('$'.array_sum($c)), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('$'.array_sum($d)), 1, 0, 'L');
                $pdf->Ln(5);
                $a = array();
                $b = array();
                $c = array();
                $d = array();
            }

            // sous-total
            // sous-total
            $pdf->Ln(5);
            $pdf->SetFont('Arial','B',12);
            $pdf->cell(100, 5, decode_fr('TOTAL GENERAL'), 0, 1, 'C');
            $pdf->Ln(2);
            $pdf->SetFont('Arial','B',10);
            $pdf->cell(50, 5,'Faculte',1,0,'C');
            $pdf->cell(15, 5,'Total',1,0,'C');
            $pdf->cell(20, 5, decode_fr('Total payé'),1,0,'C');
            $pdf->cell(15, 5,'Solde',1,0,'C');
            $pdf->Ln(5);

            $req = ConnexionBdd::Connecter()->prepare("SELECT SUM(total) AS total, SUM(total_payer) AS total_payer, faculte  FROM gest_honoraire WHERE annee_acad = ? GROUP BY faculte");
            $req->execute(array($annee_acad));

            $pdf->SetFont('Arial','',7);
            while ($res1=$req->fetch()) {
                $pdf->SetFont('Arial','',9);
                $pdf->cell(50, 5, decode_fr($res1['faculte']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('$'.$res1['total']), 1, 0, 'L');
                $pdf->cell(20, 5, decode_fr('$'.$res1['total_payer']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('$'.floatval($res1['total']-$res1['total_payer'])), 1, 0, 'L');
                $pdf->Ln(5);
                $a[] = $res1['heure_th'] + $res1['heure_pr'];
                $b[] = $res1['total'];
                $c[] = $res1['total_payer'];
                $d[] = floatval($res1['total']-$res1['total_payer']);
            }
            $pdf->SetFont('Arial','',9);
            $pdf->cell(50, 5, decode_fr("Total"), 1, 0, 'L');
            $pdf->cell(15, 5, decode_fr('$'.array_sum($b)), 1, 0, 'L');
            $pdf->cell(20, 5, decode_fr('$'.array_sum($c)), 1, 0, 'L');
            $pdf->cell(15, 5, decode_fr('$'.array_sum($d)), 1, 0, 'L');
            $pdf->Ln(5);
            $a = array();
            $b = array();
            $c = array();
            $d = array();
        }else if($_POST['faculte'] != "Tous" && $_POST['prestation'] == "Tous" && $_POST['type_enseign'] == "Tous"){
            $pdf->Ln(1);
            $pdf->SetFont('Arial','BU',10);
            $pdf->cell(60, 5, decode_fr(''.verify($fac)), 0, 1, 'L');
            $pdf->Ln(2);
            $pdf->SetFont('Arial','B',10);
            $pdf->cell(60, 5,'Enseignants',1,0,'C');
            $pdf->cell(15, 5,'Grade',1,0,'C');
            $pdf->cell(70, 5,'Cours',1,0,'C');
            // $pdf->cell(50, 5,'Faculte',1,0,'C');
            $pdf->cell(18, 5,'V. Horaire',1,0,'C');
            $pdf->cell(25, 5,'Type Enseign.',1,0,'C');
            $pdf->cell(25, 5,'Type Prest.',1,0,'C');
            $pdf->cell(15, 5,'Taux',1,0,'C');
            $pdf->cell(15, 5,'Total',1,0,'C');
            $pdf->cell(20, 5, decode_fr('Total payé'),1,0,'C');
            $pdf->cell(15, 5,'Solde',1,0,'C');
            $pdf->Ln(5);

            $req = ConnexionBdd::Connecter()->prepare("SELECT * FROM gest_honoraire WHERE annee_acad = ? AND
                faculte = ?");
            $req->execute(array($annee_acad, $_POST['faculte']));

            $pdf->SetFont('Arial','',7);
            while ($res1=$req->fetch()) {
                $pdf->SetFont('Arial','',9);
                $pdf->cell(60, 5, decode_fr($res1['noms_ens']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr($res1['grade_ens']), 1, 0, 'L');
                $pdf->cell(70, 5, decode_fr($res1['cours']), 1, 0, 'L');
                // $pdf->cell(50, 5, decode_fr($res1['faculte']), 1, 0, 'L');
                $pdf->cell(18, 5, decode_fr($res1['heure_th'] + $res1['heure_pr']), 1, 0, 'L');
                $pdf->cell(25, 5, decode_fr($res1['type_enseig']), 1, 0, 'L');
                $pdf->cell(25, 5, decode_fr($res1['prestation']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('$'.$res1['taux']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('$'.$res1['total']), 1, 0, 'L');
                $pdf->cell(20, 5, decode_fr('$'.$res1['total_payer']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('$'.floatval($res1['total']-$res1['total_payer'])), 1, 0, 'L');
                $pdf->Ln(5);
                $a[] = $res1['heure_th'] + $res1['heure_pr'];
                $b[] = $res1['total'];
                $c[] = $res1['total_payer'];
                $d[] = floatval($res1['total']-$res1['total_payer']);
            }
            $pdf->SetFont('Arial','',9);
            $pdf->cell(60, 5, decode_fr("Total"), 1, 0, 'L');
            $pdf->cell(15, 5, decode_fr("-"), 1, 0, 'L', true);
            $pdf->cell(70, 5, decode_fr("-"), 1, 0, 'L', true);
            $pdf->cell(18, 5, decode_fr(array_sum($a)), 1, 0, 'L');
            $pdf->cell(15, 5, decode_fr("-"), 1, 0, 'L', true);
            $pdf->cell(25, 5, decode_fr("-"), 1, 0, 'L', true);
            $pdf->cell(25, 5, decode_fr("-"), 1, 0, 'L', true);
            $pdf->cell(15, 5, decode_fr('$'.array_sum($b)), 1, 0, 'L');
            $pdf->cell(20, 5, decode_fr('$'.array_sum($c)), 1, 0, 'L');
            $pdf->cell(15, 5, decode_fr('$'.array_sum($d)), 1, 0, 'L');
            $pdf->Ln(5);
            $a = array();
            $b = array();
            $c = array();
            $d = array();
        }else if($_POST['faculte'] != "Tous" && $_POST['prestation'] != "Tous" && $_POST['type_enseign'] == "Tous"){
            $pdf->Ln(1);
            $pdf->SetFont('Arial','BU',10);
            $pdf->cell(60, 5, decode_fr(''.verify($fac)), 0, 1, 'L');
            $pdf->Ln(2);
            $pdf->SetFont('Arial','B',10);
            $pdf->cell(60, 5,'Enseignants',1,0,'C');
            $pdf->cell(15, 5,'Grade',1,0,'C');
            $pdf->cell(70, 5,'Cours',1,0,'C');
            // $pdf->cell(50, 5,'Faculte',1,0,'C');
            $pdf->cell(18, 5,'V. Horaire',1,0,'C');
            $pdf->cell(25, 5,'Type Enseign.',1,0,'C');
            $pdf->cell(25, 5,'Type Prest.',1,0,'C');
            $pdf->cell(15, 5,'Taux',1,0,'C');
            $pdf->cell(15, 5,'Total',1,0,'C');
            $pdf->cell(20, 5, decode_fr('Total payé'),1,0,'C');
            $pdf->cell(15, 5,'Solde',1,0,'C');
            $pdf->Ln(5);

            $req = ConnexionBdd::Connecter()->prepare("SELECT * FROM gest_honoraire WHERE annee_acad = ? AND
                faculte = ? AND prestation = ?");
            $req->execute(array($annee_acad, $_POST['faculte'], $_POST['prestation']));

            $pdf->SetFont('Arial','',7);
            while ($res1=$req->fetch()) {
                $pdf->SetFont('Arial','',9);
                $pdf->cell(60, 5, decode_fr($res1['noms_ens']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr($res1['grade_ens']), 1, 0, 'L');
                $pdf->cell(70, 5, decode_fr($res1['cours']), 1, 0, 'L');
                // $pdf->cell(50, 5, decode_fr($res1['faculte']), 1, 0, 'L');
                $pdf->cell(18, 5, decode_fr($res1['heure_th'] + $res1['heure_pr']), 1, 0, 'L');
                $pdf->cell(25, 5, decode_fr($res1['type_enseig']), 1, 0, 'L');
                $pdf->cell(25, 5, decode_fr($res1['prestation']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('$'.$res1['taux']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('$'.$res1['total']), 1, 0, 'L');
                $pdf->cell(20, 5, decode_fr('$'.$res1['total_payer']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('$'.floatval($res1['total']-$res1['total_payer'])), 1, 0, 'L');
                $pdf->Ln(5);
                $a[] = $res1['heure_th'] + $res1['heure_pr'];
                $b[] = $res1['total'];
                $c[] = $res1['total_payer'];
                $d[] = floatval($res1['total']-$res1['total_payer']);
            }
            $pdf->SetFont('Arial','',9);
            $pdf->cell(60, 5, decode_fr("Total"), 1, 0, 'L');
            $pdf->cell(15, 5, decode_fr("-"), 1, 0, 'L', true);
            $pdf->cell(70, 5, decode_fr("-"), 1, 0, 'L', true);
            $pdf->cell(18, 5, decode_fr(array_sum($a)), 1, 0, 'L');
            $pdf->cell(15, 5, decode_fr("-"), 1, 0, 'L', true);
            $pdf->cell(25, 5, decode_fr("-"), 1, 0, 'L', true);
            $pdf->cell(25, 5, decode_fr("-"), 1, 0, 'L', true);
            $pdf->cell(15, 5, decode_fr('$'.array_sum($b)), 1, 0, 'L');
            $pdf->cell(20, 5, decode_fr('$'.array_sum($c)), 1, 0, 'L');
            $pdf->cell(15, 5, decode_fr('$'.array_sum($d)), 1, 0, 'L');
            $pdf->Ln(5);
            $a = array();
            $b = array();
            $c = array();
            $d = array();
        }else if($_POST['faculte'] != "Tous" && $_POST['prestation'] != "Tous" && $_POST['type_enseign'] != "Tous"){
            $pdf->Ln(1);
            $pdf->SetFont('Arial','BU',10);
            $pdf->cell(60, 5, decode_fr(''.verify($fac)), 0, 1, 'L');
            $pdf->Ln(2);
            $pdf->SetFont('Arial','B',10);
            $pdf->cell(60, 5,'Enseignants',1,0,'C');
            $pdf->cell(15, 5,'Grade',1,0,'C');
            $pdf->cell(70, 5,'Cours',1,0,'C');
            // $pdf->cell(50, 5,'Faculte',1,0,'C');
            $pdf->cell(18, 5,'V. Horaire',1,0,'C');
            $pdf->cell(25, 5,'Type Enseign.',1,0,'C');
            $pdf->cell(25, 5,'Type Prest.',1,0,'C');
            $pdf->cell(15, 5,'Taux',1,0,'C');
            $pdf->cell(15, 5,'Total',1,0,'C');
            $pdf->cell(20, 5, decode_fr('Total payé'),1,0,'C');
            $pdf->cell(15, 5,'Solde',1,0,'C');
            $pdf->Ln(5);

            $req = ConnexionBdd::Connecter()->prepare("SELECT * FROM gest_honoraire WHERE annee_acad = ? AND
                faculte = ? AND prestation = ? AND type_enseig = ?");
            $req->execute(array($annee_acad, $_POST['faculte'], $_POST['prestation'], $_POST['type_enseign']));

            $pdf->SetFont('Arial','',7);
            while ($res1=$req->fetch()) {
                $pdf->SetFont('Arial','',9);
                $pdf->cell(60, 5, decode_fr($res1['noms_ens']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr($res1['grade_ens']), 1, 0, 'L');
                $pdf->cell(70, 5, decode_fr($res1['cours']), 1, 0, 'L');
                // $pdf->cell(50, 5, decode_fr($res1['faculte']), 1, 0, 'L');
                $pdf->cell(18, 5, decode_fr($res1['heure_th'] + $res1['heure_pr']), 1, 0, 'L');
                $pdf->cell(25, 5, decode_fr($res1['type_enseig']), 1, 0, 'L');
                $pdf->cell(25, 5, decode_fr($res1['prestation']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('$'.$res1['taux']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('$'.$res1['total']), 1, 0, 'L');
                $pdf->cell(20, 5, decode_fr('$'.$res1['total_payer']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('$'.floatval($res1['total']-$res1['total_payer'])), 1, 0, 'L');
                $pdf->Ln(5);
                $a[] = $res1['heure_th'] + $res1['heure_pr'];
                $b[] = $res1['total'];
                $c[] = $res1['total_payer'];
                $d[] = floatval($res1['total']-$res1['total_payer']);
            }
            $pdf->SetFont('Arial','',9);
            $pdf->cell(60, 5, decode_fr("Total"), 1, 0, 'L');
            $pdf->cell(15, 5, decode_fr("-"), 1, 0, 'L', true);
            $pdf->cell(70, 5, decode_fr("-"), 1, 0, 'L', true);
            $pdf->cell(18, 5, decode_fr(array_sum($a)), 1, 0, 'L');
            $pdf->cell(15, 5, decode_fr("-"), 1, 0, 'L', true);
            $pdf->cell(25, 5, decode_fr("-"), 1, 0, 'L', true);
            $pdf->cell(25, 5, decode_fr("-"), 1, 0, 'L', true);
            $pdf->cell(15, 5, decode_fr('$'.array_sum($b)), 1, 0, 'L');
            $pdf->cell(20, 5, decode_fr('$'.array_sum($c)), 1, 0, 'L');
            $pdf->cell(15, 5, decode_fr('$'.array_sum($d)), 1, 0, 'L');
            $pdf->Ln(5);
            $a = array();
            $b = array();
            $c = array();
            $d = array();
        }
        // footer
        footer($pdf);
    }

    // prestation
    if(isset($_POST['btn_prestation'])){
        $pdf = new FPDF('L', 'mm', 'A4');
        $annee_acad = $_POST['annee_acad'];

        all($pdf);
        $pdf->SetFont('Arial','BU',10);
        $pdf->cell(270, 10, decode_fr('RAPPORT GESTION DES HONORAIRES'), 0, 1, 'C');
        $pdf->SetFont('Arial','',10);
        if($_POST['faculte'] == "Tous" && $_POST['prestation'] == "Tous"){
            $list_fac = array();

            $fac = ConnexionBdd::Connecter()->query("SELECT DISTINCT prestation FROM gest_honoraire");
            while($data = $fac->fetch()){
                $list_fac[] = $data['prestation'];
            }

            foreach ($list_fac as $fac) {
                $pdf->Ln(3);
                $pdf->SetFont('Arial','B',10);
                $pdf->cell(60, 5, decode_fr(''.verify(strtoupper($fac))), 0, 1, 'L');
                $pdf->Ln(2);

                $pdf->SetFont('Arial','B',10);
                $pdf->cell(60, 5,'Enseignants',1,0,'C');
                $pdf->cell(15, 5,'Grade',1,0,'C');
                $pdf->cell(70, 5,'Cours',1,0,'C');
                $pdf->cell(50, 5,'Faculte',1,0,'C');
                $pdf->cell(18, 5,'V. Horaire',1,0,'C');
                $pdf->cell(15, 5,'Taux',1,0,'C');
                $pdf->cell(15, 5,'Total',1,0,'C');
                $pdf->cell(20, 5, decode_fr('Total payé'),1,0,'C');
                $pdf->cell(15, 5,'Solde',1,0,'C');
                $pdf->Ln(5);

                $req = ConnexionBdd::Connecter()->prepare("SELECT * FROM gest_honoraire WHERE annee_acad = ? AND prestation = ?");
                $req->execute(array($annee_acad, $fac));

                $pdf->SetFont('Arial','',7);
                while ($res1=$req->fetch()) {
                    $pdf->SetFont('Arial','',9);
                    $pdf->cell(60, 5, decode_fr($res1['noms_ens']), 1, 0, 'L');
                    $pdf->cell(15, 5, decode_fr($res1['grade_ens']), 1, 0, 'L');
                    $pdf->cell(70, 5, decode_fr($res1['cours']), 1, 0, 'L');
                    $pdf->cell(50, 5, decode_fr($res1['faculte']), 1, 0, 'L');
                    $pdf->cell(18, 5, decode_fr($res1['heure_th'] + $res1['heure_pr']), 1, 0, 'L');
                    $pdf->cell(15, 5, decode_fr('$'.$res1['taux']), 1, 0, 'L');
                    $pdf->cell(15, 5, decode_fr('$'.$res1['total']), 1, 0, 'L');
                    $pdf->cell(20, 5, decode_fr('$'.$res1['total_payer']), 1, 0, 'L');
                    $pdf->cell(15, 5, decode_fr('$'.floatval($res1['total']-$res1['total_payer'])), 1, 0, 'L');
                    $pdf->Ln(5);
                    $a[] = $res1['heure_th'] + $res1['heure_pr'];
                    $b[] = $res1['total'];
                    $c[] = $res1['total_payer'];
                    $d[] = floatval($res1['total']-$res1['total_payer']);
                }
                $pdf->SetFont('Arial','',9);
                $pdf->cell(60, 5, decode_fr("Total"), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr("-"), 1, 0, 'L', true);
                $pdf->cell(70, 5, decode_fr("-"), 1, 0, 'L', true);
                $pdf->cell(50, 5, decode_fr("-"), 1, 0, 'L', true);
                $pdf->cell(18, 5, decode_fr(array_sum($a)), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr("-"), 1, 0, 'L', true);
                $pdf->cell(15, 5, decode_fr('$'.array_sum($b)), 1, 0, 'L');
                $pdf->cell(20, 5, decode_fr('$'.array_sum($c)), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('$'.array_sum($d)), 1, 0, 'L');
                $pdf->Ln(5);
                $a = array();
                $b = array();
                $c = array();
                $d = array();
            }

            // sous-total
            $pdf->Ln(5);
            $pdf->SetFont('Arial','B',12);
            $pdf->cell(160, 5, decode_fr('TOTAL GENERAL'), 0, 1, 'C');
            $pdf->Ln(2);
            $pdf->SetFont('Arial','B',10);
            $pdf->cell(60, 5,'Enseignants',1,0,'C');
            $pdf->cell(15, 5,'Heures',1,0,'C');
            $pdf->cell(15, 5,'Total',1,0,'C');
            $pdf->cell(20, 5, decode_fr('Total payé'),1,0,'C');
            $pdf->cell(15, 5,'Solde',1,0,'C');
            $pdf->Ln(5);

            $req = ConnexionBdd::Connecter()->prepare("SELECT prestation, SUM(heure_pr) AS heure_pr, SUM(heure_th) AS heure_th, SUM(total) AS total, SUM(total_payer) AS total_payer,
            SUM(montant_ht) AS montant_ht, SUM(montant_hp) AS montant_hp FROM gest_honoraire WHERE annee_acad = ? GROUP BY prestation");
            $req->execute(array($annee_acad));
            //, SUM(heure_pr) AS heure_pr, SUM(heure_pr) AS heure_pr SUM() AS , SUM() AS 
            $pdf->SetFont('Arial','',7);
            while ($res1=$req->fetch()) {
                $pdf->SetFont('Arial','',9);
                $pdf->cell(60, 5, decode_fr($res1['prestation']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr($res1['heure_th'] + $res1['heure_pr']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('$'.$res1['total']), 1, 0, 'L');
                $pdf->cell(20, 5, decode_fr('$'.$res1['total_payer']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr($res1['total']-$res1['total_payer']), 1, 0, 'L');
                $pdf->Ln(5);
                $a[] = $res1['heure_th'] + $res1['heure_pr'];
                $b[] = $res1['total'];
                $c[] = $res1['total_payer'];
                $d[] = floatval($res1['total']-$res1['total_payer']);
            }
            $pdf->SetFont('Arial','',9);
            $pdf->cell(60, 5, decode_fr("Total"), 1, 0, 'L');
            $pdf->cell(15, 5, decode_fr(array_sum($a)), 1, 0, 'L');
            $pdf->cell(15, 5, decode_fr('$'.array_sum($b)), 1, 0, 'L');
            $pdf->cell(20, 5, decode_fr('$'.array_sum($c)), 1, 0, 'L');
            $pdf->cell(15, 5, decode_fr(array_sum($d)), 1, 0, 'L');
            $pdf->Ln(5);
            $a = array();
            $b = array();
            $c = array();
            $d = array();
        }else if($_POST['faculte'] == "Tous" && $_POST['prestation'] != "Tous"){
            $pdf->Ln(3);
            $pdf->SetFont('Arial','B',10);
            $pdf->cell(60, 5, decode_fr(''.verify(strtoupper($_POST['prestation']))), 0, 1, 'L');
            $pdf->Ln(2);

            $pdf->SetFont('Arial','B',10);
            $pdf->cell(60, 5,'Enseignants',1,0,'C');
            $pdf->cell(15, 5,'Grade',1,0,'C');
            $pdf->cell(70, 5,'Cours',1,0,'C');
            $pdf->cell(50, 5,'Faculte',1,0,'C');
            $pdf->cell(18, 5,'V. Horaire',1,0,'C');
            $pdf->cell(15, 5,'Taux',1,0,'C');
            $pdf->cell(15, 5,'Total',1,0,'C');
            $pdf->cell(20, 5, decode_fr('Total payé'),1,0,'C');
            $pdf->cell(15, 5,'Solde',1,0,'C');
            $pdf->Ln(5);

            $req = ConnexionBdd::Connecter()->prepare("SELECT * FROM gest_honoraire WHERE annee_acad = ? AND prestation = ?");
            $req->execute(array($annee_acad, $_POST['prestation']));

            $pdf->SetFont('Arial','',7);
            while ($res1=$req->fetch()) {
                $pdf->SetFont('Arial','',9);
                $pdf->cell(60, 5, decode_fr($res1['noms_ens']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr($res1['grade_ens']), 1, 0, 'L');
                $pdf->cell(70, 5, decode_fr($res1['cours']), 1, 0, 'L');
                $pdf->cell(50, 5, decode_fr($res1['faculte']), 1, 0, 'L');
                $pdf->cell(18, 5, decode_fr($res1['heure_th'] + $res1['heure_pr']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('$'.$res1['taux']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('$'.$res1['total']), 1, 0, 'L');
                $pdf->cell(20, 5, decode_fr('$'.$res1['total_payer']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('$'.floatval($res1['total']-$res1['total_payer'])), 1, 0, 'L');
                $pdf->Ln(5);
                $a[] = $res1['heure_th'] + $res1['heure_pr'];
                $b[] = $res1['total'];
                $c[] = $res1['total_payer'];
                $d[] = floatval($res1['total']-$res1['total_payer']);
            }
            $pdf->SetFont('Arial','',9);
            $pdf->cell(60, 5, decode_fr("Total"), 1, 0, 'L');
            $pdf->cell(15, 5, decode_fr("-"), 1, 0, 'L', true);
            $pdf->cell(70, 5, decode_fr("-"), 1, 0, 'L', true);
            $pdf->cell(50, 5, decode_fr("-"), 1, 0, 'L', true);
            $pdf->cell(18, 5, decode_fr(array_sum($a)), 1, 0, 'L');
            $pdf->cell(15, 5, decode_fr("-"), 1, 0, 'L', true);
            $pdf->cell(15, 5, decode_fr('$'.array_sum($b)), 1, 0, 'L');
            $pdf->cell(20, 5, decode_fr('$'.array_sum($c)), 1, 0, 'L');
            $pdf->cell(15, 5, decode_fr('$'.array_sum($d)), 1, 0, 'L');
            $pdf->Ln(5);
            $a = array();
            $b = array();
            $c = array();
            $d = array();
        }else if($_POST['faculte'] != "Tous" && $_POST['prestation'] != "Tous"){
            $pdf->Ln(3);
            $pdf->SetFont('Arial','B',10);
            $pdf->cell(60, 5, decode_fr(''.verify(strtoupper($_POST['prestation']))), 0, 1, 'L');
            $pdf->Ln(2);

            $pdf->SetFont('Arial','B',10);
            $pdf->cell(60, 5,'Enseignants',1,0,'C');
            $pdf->cell(15, 5,'Grade',1,0,'C');
            $pdf->cell(70, 5,'Cours',1,0,'C');
            $pdf->cell(50, 5,'Faculte',1,0,'C');
            $pdf->cell(18, 5,'V. Horaire',1,0,'C');
            $pdf->cell(15, 5,'Taux',1,0,'C');
            $pdf->cell(15, 5,'Total',1,0,'C');
            $pdf->cell(20, 5, decode_fr('Total payé'),1,0,'C');
            $pdf->cell(15, 5,'Solde',1,0,'C');
            $pdf->Ln(5);

            $req = ConnexionBdd::Connecter()->prepare("SELECT * FROM gest_honoraire WHERE annee_acad = ? AND prestation = ? AND faculte = ?");
            $req->execute(array($annee_acad, $_POST['prestation'], $_POST['faculte']));

            $pdf->SetFont('Arial','',7);
            while ($res1=$req->fetch()) {
                $pdf->SetFont('Arial','',9);
                $pdf->cell(60, 5, decode_fr($res1['noms_ens']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr($res1['grade_ens']), 1, 0, 'L');
                $pdf->cell(70, 5, decode_fr($res1['cours']), 1, 0, 'L');
                $pdf->cell(50, 5, decode_fr($res1['faculte']), 1, 0, 'L');
                $pdf->cell(18, 5, decode_fr($res1['heure_th'] + $res1['heure_pr']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('$'.$res1['taux']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('$'.$res1['total']), 1, 0, 'L');
                $pdf->cell(20, 5, decode_fr('$'.$res1['total_payer']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('$'.floatval($res1['total']-$res1['total_payer'])), 1, 0, 'L');
                $pdf->Ln(5);
                $a[] = $res1['heure_th'] + $res1['heure_pr'];
                $b[] = $res1['total'];
                $c[] = $res1['total_payer'];
                $d[] = floatval($res1['total']-$res1['total_payer']);
            }
            $pdf->SetFont('Arial','',9);
            $pdf->cell(60, 5, decode_fr("Total"), 1, 0, 'L');
            $pdf->cell(15, 5, decode_fr("-"), 1, 0, 'L', true);
            $pdf->cell(70, 5, decode_fr("-"), 1, 0, 'L', true);
            $pdf->cell(50, 5, decode_fr("-"), 1, 0, 'L', true);
            $pdf->cell(18, 5, decode_fr(array_sum($a)), 1, 0, 'L');
            $pdf->cell(15, 5, decode_fr("-"), 1, 0, 'L', true);
            $pdf->cell(15, 5, decode_fr('$'.array_sum($b)), 1, 0, 'L');
            $pdf->cell(20, 5, decode_fr('$'.array_sum($c)), 1, 0, 'L');
            $pdf->cell(15, 5, decode_fr('$'.array_sum($d)), 1, 0, 'L');
            $pdf->Ln(5);
            $a = array();
            $b = array();
            $c = array();
            $d = array();
        }
        footer($pdf);
    }

    // Par type d'enseignants
    if(isset($_POST['btn_type_enseign'])){
        $pdf = new FPDF('L', 'mm', 'A4');
        $annee_acad = $_POST['annee_acad'];

        all($pdf);
        $pdf->SetFont('Arial','BU',10);
        $pdf->cell(270, 10, decode_fr('RAPPORT GESTION DES HONORAIRES'), 0, 1, 'C');
        $pdf->SetFont('Arial','',10);
        if($_POST['faculte'] == "Tous" && $_POST['type_enseign'] == "Tous"){
            $list_fac = array();

            $fac = ConnexionBdd::Connecter()->query("SELECT DISTINCT type_enseig FROM gest_honoraire");
            while($data = $fac->fetch()){
                $list_fac[] = $data['type_enseig'];
            }

            foreach ($list_fac as $fac) {
                $pdf->Ln(3);
                $pdf->SetFont('Arial','B',10);
                $pdf->cell(60, 5, decode_fr(''.verify(strtoupper($fac))), 0, 1, 'L');
                $pdf->Ln(2);

                $pdf->SetFont('Arial','B',10);
                $pdf->cell(60, 5,'Enseignants',1,0,'C');
                $pdf->cell(15, 5,'Grade',1,0,'C');
                $pdf->cell(70, 5,'Cours',1,0,'C');
                $pdf->cell(50, 5,'Faculte',1,0,'C');
                $pdf->cell(18, 5,'V. Horaire',1,0,'C');
                $pdf->cell(15, 5,'Taux',1,0,'C');
                $pdf->cell(15, 5,'Total',1,0,'C');
                $pdf->cell(20, 5, decode_fr('Total payé'),1,0,'C');
                $pdf->cell(15, 5,'Solde',1,0,'C');
                $pdf->Ln(5);

                $req = ConnexionBdd::Connecter()->prepare("SELECT * FROM gest_honoraire WHERE annee_acad = ? AND type_enseig = ?");
                $req->execute(array($annee_acad, $fac));

                $pdf->SetFont('Arial','',7);
                while ($res1=$req->fetch()) {
                    $pdf->SetFont('Arial','',9);
                    $pdf->cell(60, 5, decode_fr($res1['noms_ens']), 1, 0, 'L');
                    $pdf->cell(15, 5, decode_fr($res1['grade_ens']), 1, 0, 'L');
                    $pdf->cell(70, 5, decode_fr($res1['cours']), 1, 0, 'L');
                    $pdf->cell(50, 5, decode_fr($res1['faculte']), 1, 0, 'L');
                    $pdf->cell(18, 5, decode_fr($res1['heure_th'] + $res1['heure_pr']), 1, 0, 'L');
                    $pdf->cell(15, 5, decode_fr('$'.$res1['taux']), 1, 0, 'L');
                    $pdf->cell(15, 5, decode_fr('$'.$res1['total']), 1, 0, 'L');
                    $pdf->cell(20, 5, decode_fr('$'.$res1['total_payer']), 1, 0, 'L');
                    $pdf->cell(15, 5, decode_fr('$'.floatval($res1['total']-$res1['total_payer'])), 1, 0, 'L');
                    $pdf->Ln(5);
                    $a[] = $res1['heure_th'] + $res1['heure_pr'];
                    $b[] = $res1['total'];
                    $c[] = $res1['total_payer'];
                    $d[] = floatval($res1['total']-$res1['total_payer']);
                }
                $pdf->SetFont('Arial','',9);
                $pdf->cell(60, 5, decode_fr("Total"), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr("-"), 1, 0, 'L', true);
                $pdf->cell(70, 5, decode_fr("-"), 1, 0, 'L', true);
                $pdf->cell(50, 5, decode_fr("-"), 1, 0, 'L', true);
                $pdf->cell(18, 5, decode_fr(array_sum($a)), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr("-"), 1, 0, 'L', true);
                $pdf->cell(15, 5, decode_fr('$'.array_sum($b)), 1, 0, 'L');
                $pdf->cell(20, 5, decode_fr('$'.array_sum($c)), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('$'.array_sum($d)), 1, 0, 'L');
                $pdf->Ln(5);
                $a = array();
                $b = array();
                $c = array();
                $d = array();
            }

            // sous-total
            $pdf->Ln(5);
            $pdf->SetFont('Arial','B',12);
            $pdf->cell(160, 5, decode_fr('TOTAL GENERAL'), 0, 1, 'C');
            $pdf->Ln(2);
            $pdf->SetFont('Arial','B',10);
            $pdf->cell(60, 5,'Enseignants',1,0,'C');
            $pdf->cell(15, 5,'Heures',1,0,'C');
            $pdf->cell(15, 5,'Total',1,0,'C');
            $pdf->cell(20, 5, decode_fr('Total payé'),1,0,'C');
            $pdf->cell(15, 5,'Solde',1,0,'C');
            $pdf->Ln(5);

            $req = ConnexionBdd::Connecter()->prepare("SELECT type_enseig, SUM(heure_pr) AS heure_pr, SUM(heure_th) AS heure_th, SUM(total) AS total, SUM(total_payer) AS total_payer,
            SUM(montant_ht) AS montant_ht, SUM(montant_hp) AS montant_hp FROM gest_honoraire WHERE annee_acad = ? GROUP BY type_enseig");
            $req->execute(array($annee_acad));
            //, SUM(heure_pr) AS heure_pr, SUM(heure_pr) AS heure_pr SUM() AS , SUM() AS 
            $pdf->SetFont('Arial','',7);
            while ($res1=$req->fetch()) {
                $pdf->SetFont('Arial','',9);
                $pdf->cell(60, 5, decode_fr($res1['type_enseig'].' à la tache'), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr($res1['heure_th'] + $res1['heure_pr']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('$'.$res1['total']), 1, 0, 'L');
                $pdf->cell(20, 5, decode_fr('$'.$res1['total_payer']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr($res1['total']-$res1['total_payer']), 1, 0, 'L');
                $pdf->Ln(5);
                $a[] = $res1['heure_th'] + $res1['heure_pr'];
                $b[] = $res1['total'];
                $c[] = $res1['total_payer'];
                $d[] = floatval($res1['total']-$res1['total_payer']);
            }

            // permanant mais avec des heures supplentaires
            $req = ConnexionBdd::Connecter()->prepare("SELECT type_enseig, SUM(heure_pr) AS heure_pr, SUM(heure_th) AS heure_th, SUM(total) AS total, SUM(total_payer) AS total_payer,
            SUM(montant_ht) AS montant_ht, SUM(montant_hp) AS montant_hp FROM gest_honoraire WHERE type_enseig = 'Permanent' AND prestation = 'Suplementaire' AND annee_acad = ? GROUP BY type_enseig");
            $req->execute(array($annee_acad));
            while ($res1=$req->fetch()) {
                $pdf->SetFont('Arial','',9);
                $pdf->cell(60, 5, decode_fr($res1['type_enseig'].' supplementaire'), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr($res1['heure_th'] + $res1['heure_pr']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('$'.$res1['total']), 1, 0, 'L');
                $pdf->cell(20, 5, decode_fr('$'.$res1['total_payer']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr($res1['total']-$res1['total_payer']), 1, 0, 'L');
                $pdf->Ln(5);
                $a[] = $res1['heure_th'] + $res1['heure_pr'];
                $b[] = $res1['total'];
                $c[] = $res1['total_payer'];
                $d[] = floatval($res1['total']-$res1['total_payer']);
            }


            $pdf->SetFont('Arial','',9);
            $pdf->cell(60, 5, decode_fr("Total"), 1, 0, 'L');
            $pdf->cell(15, 5, decode_fr(array_sum($a)), 1, 0, 'L');
            $pdf->cell(15, 5, decode_fr('$'.array_sum($b)), 1, 0, 'L');
            $pdf->cell(20, 5, decode_fr('$'.array_sum($c)), 1, 0, 'L');
            $pdf->cell(15, 5, decode_fr(array_sum($d)), 1, 0, 'L');
            $pdf->Ln(5);
            $a = array();
            $b = array();
            $c = array();
            $d = array();
        }else if($_POST['faculte'] == "Tous" && $_POST['type_enseign'] != "Tous"){
            $pdf->Ln(3);
            $pdf->SetFont('Arial','B',10);
            $pdf->cell(60, 5, decode_fr(''.verify(strtoupper($_POST['type_enseign']))), 0, 1, 'L');
            $pdf->Ln(2);

            $pdf->SetFont('Arial','B',10);
            $pdf->cell(60, 5,'Enseignants',1,0,'C');
            $pdf->cell(15, 5,'Grade',1,0,'C');
            $pdf->cell(70, 5,'Cours',1,0,'C');
            $pdf->cell(50, 5,'Faculte',1,0,'C');
            $pdf->cell(18, 5,'V. Horaire',1,0,'C');
            $pdf->cell(15, 5,'Taux',1,0,'C');
            $pdf->cell(15, 5,'Total',1,0,'C');
            $pdf->cell(20, 5, decode_fr('Total payé'),1,0,'C');
            $pdf->cell(15, 5,'Solde',1,0,'C');
            $pdf->Ln(5);

            $req = ConnexionBdd::Connecter()->prepare("SELECT * FROM gest_honoraire WHERE annee_acad = ? AND type_enseig = ?");
            $req->execute(array($annee_acad, $_POST['type_enseign']));

            $pdf->SetFont('Arial','',7);
            while ($res1=$req->fetch()) {
                $pdf->SetFont('Arial','',9);
                $pdf->cell(60, 5, decode_fr($res1['noms_ens']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr($res1['grade_ens']), 1, 0, 'L');
                $pdf->cell(70, 5, decode_fr($res1['cours']), 1, 0, 'L');
                $pdf->cell(50, 5, decode_fr($res1['faculte']), 1, 0, 'L');
                $pdf->cell(18, 5, decode_fr($res1['heure_th'] + $res1['heure_pr']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('$'.$res1['taux']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('$'.$res1['total']), 1, 0, 'L');
                $pdf->cell(20, 5, decode_fr('$'.$res1['total_payer']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('$'.floatval($res1['total']-$res1['total_payer'])), 1, 0, 'L');
                $pdf->Ln(5);
                $a[] = $res1['heure_th'] + $res1['heure_pr'];
                $b[] = $res1['total'];
                $c[] = $res1['total_payer'];
                $d[] = floatval($res1['total']-$res1['total_payer']);
            }
            $pdf->SetFont('Arial','',9);
            $pdf->cell(60, 5, decode_fr("Total"), 1, 0, 'L');
            $pdf->cell(15, 5, decode_fr("-"), 1, 0, 'L', true);
            $pdf->cell(70, 5, decode_fr("-"), 1, 0, 'L', true);
            $pdf->cell(50, 5, decode_fr("-"), 1, 0, 'L', true);
            $pdf->cell(18, 5, decode_fr(array_sum($a)), 1, 0, 'L');
            $pdf->cell(15, 5, decode_fr("-"), 1, 0, 'L', true);
            $pdf->cell(15, 5, decode_fr('$'.array_sum($b)), 1, 0, 'L');
            $pdf->cell(20, 5, decode_fr('$'.array_sum($c)), 1, 0, 'L');
            $pdf->cell(15, 5, decode_fr('$'.array_sum($d)), 1, 0, 'L');
            $pdf->Ln(5);
            $a = array();
            $b = array();
            $c = array();
            $d = array();
        }else if($_POST['faculte'] != "Tous" && $_POST['type_enseign'] != "Tous"){
            $pdf->Ln(3);
            $pdf->SetFont('Arial','B',10);
            $pdf->cell(60, 5, decode_fr(''.verify(strtoupper($_POST['type_enseign']))), 0, 1, 'L');
            $pdf->Ln(2);

            $pdf->SetFont('Arial','B',10);
            $pdf->cell(60, 5,'Enseignants',1,0,'C');
            $pdf->cell(15, 5,'Grade',1,0,'C');
            $pdf->cell(70, 5,'Cours',1,0,'C');
            $pdf->cell(50, 5,'Faculte',1,0,'C');
            $pdf->cell(18, 5,'V. Horaire',1,0,'C');
            $pdf->cell(15, 5,'Taux',1,0,'C');
            $pdf->cell(15, 5,'Total',1,0,'C');
            $pdf->cell(20, 5, decode_fr('Total payé'),1,0,'C');
            $pdf->cell(15, 5,'Solde',1,0,'C');
            $pdf->Ln(5);

            $req = ConnexionBdd::Connecter()->prepare("SELECT * FROM gest_honoraire WHERE annee_acad = ? AND type_enseig = ? AND faculte = ?");
            $req->execute(array($annee_acad, $_POST['type_enseign'], $_POST['faculte']));

            $pdf->SetFont('Arial','',7);
            while ($res1=$req->fetch()) {
                $pdf->SetFont('Arial','',9);
                $pdf->cell(60, 5, decode_fr($res1['noms_ens']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr($res1['grade_ens']), 1, 0, 'L');
                $pdf->cell(70, 5, decode_fr($res1['cours']), 1, 0, 'L');
                $pdf->cell(50, 5, decode_fr($res1['faculte']), 1, 0, 'L');
                $pdf->cell(18, 5, decode_fr($res1['heure_th'] + $res1['heure_pr']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('$'.$res1['taux']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('$'.$res1['total']), 1, 0, 'L');
                $pdf->cell(20, 5, decode_fr('$'.$res1['total_payer']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('$'.floatval($res1['total']-$res1['total_payer'])), 1, 0, 'L');
                $pdf->Ln(5);
                $a[] = $res1['heure_th'] + $res1['heure_pr'];
                $b[] = $res1['total'];
                $c[] = $res1['total_payer'];
                $d[] = floatval($res1['total']-$res1['total_payer']);
            }
            $pdf->SetFont('Arial','',9);
            $pdf->cell(60, 5, decode_fr("Total"), 1, 0, 'L');
            $pdf->cell(15, 5, decode_fr("-"), 1, 0, 'L', true);
            $pdf->cell(70, 5, decode_fr("-"), 1, 0, 'L', true);
            $pdf->cell(50, 5, decode_fr("-"), 1, 0, 'L', true);
            $pdf->cell(18, 5, decode_fr(array_sum($a)), 1, 0, 'L');
            $pdf->cell(15, 5, decode_fr("-"), 1, 0, 'L', true);
            $pdf->cell(15, 5, decode_fr('$'.array_sum($b)), 1, 0, 'L');
            $pdf->cell(20, 5, decode_fr('$'.array_sum($c)), 1, 0, 'L');
            $pdf->cell(15, 5, decode_fr('$'.array_sum($d)), 1, 0, 'L');
            $pdf->Ln(5);
            $a = array();
            $b = array();
            $c = array();
            $d = array();
        }
        footer($pdf);
    }

    //btn_cours_payer
    if(isset($_POST['btn_cours_payer'])){
        $pdf = new FPDF('L', 'mm', 'A4');
        $annee_acad = $_POST['annee_acad'];

        all($pdf);
        $pdf->SetFont('Arial','BU',10);
        $pdf->cell(270, 10, decode_fr('RAPPORT GESTION DES HONORAIRES :: COURS DEJA PAYER'), 0, 1, 'C');
        $pdf->SetFont('Arial','',10);
        if($_POST['faculte'] == "Tous"){
            $pdf->SetFont('Arial','B',10);
            $pdf->cell(60, 5,'Enseignants',1,0,'C');
            $pdf->cell(15, 5,'Grade',1,0,'C');
            $pdf->cell(70, 5,'Cours',1,0,'C');
            $pdf->cell(50, 5,'Faculte',1,0,'C');
            $pdf->cell(18, 5,'V. Horaire',1,0,'C');
            $pdf->cell(15, 5,'Taux',1,0,'C');
            $pdf->cell(15, 5,'Total',1,0,'C');
            $pdf->cell(20, 5, decode_fr('Total payé'),1,0,'C');
            $pdf->cell(15, 5,'Solde',1,0,'C');
            $pdf->Ln(5);

            $req = ConnexionBdd::Connecter()->prepare("SELECT * FROM gest_honoraire WHERE annee_acad = ? HAVING total-total_payer=0");
            $req->execute(array($annee_acad));

            $pdf->SetFont('Arial','',7);
            while ($res1=$req->fetch()) {
                $pdf->SetFont('Arial','',9);
                $pdf->cell(60, 5, decode_fr($res1['noms_ens']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr($res1['grade_ens']), 1, 0, 'L');
                $pdf->cell(70, 5, decode_fr($res1['cours']), 1, 0, 'L');
                $pdf->cell(50, 5, decode_fr($res1['faculte']), 1, 0, 'L');
                $pdf->cell(18, 5, decode_fr($res1['heure_th'] + $res1['heure_pr']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('$'.$res1['taux']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('$'.$res1['total']), 1, 0, 'L');
                $pdf->cell(20, 5, decode_fr('$'.$res1['total_payer']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('$'.floatval($res1['total']-$res1['total_payer'])), 1, 0, 'L');
                $pdf->Ln(5);
                $a[] = $res1['heure_th'] + $res1['heure_pr'];
                $b[] = $res1['total'];
                $c[] = $res1['total_payer'];
                $d[] = floatval($res1['total']-$res1['total_payer']);
            }
            $pdf->SetFont('Arial','',9);
            $pdf->cell(60, 5, decode_fr("Total"), 1, 0, 'L');
            $pdf->cell(15, 5, decode_fr("-"), 1, 0, 'L', true);
            $pdf->cell(70, 5, decode_fr("-"), 1, 0, 'L', true);
            $pdf->cell(50, 5, decode_fr("-"), 1, 0, 'L', true);
            $pdf->cell(18, 5, decode_fr(array_sum($a)), 1, 0, 'L');
            $pdf->cell(15, 5, decode_fr("-"), 1, 0, 'L', true);
            $pdf->cell(15, 5, decode_fr('$'.array_sum($b)), 1, 0, 'L');
            $pdf->cell(20, 5, decode_fr('$'.array_sum($c)), 1, 0, 'L');
            $pdf->cell(15, 5, decode_fr('$'.array_sum($d)), 1, 0, 'L');
            $pdf->Ln(5);
            $a = array();
            $b = array();
            $c = array();
            $d = array();
        }else if($_POST['faculte'] != "Tous"){
            $pdf->SetFont('Arial','B',10);
            $pdf->cell(60, 5,'Enseignants',1,0,'C');
            $pdf->cell(15, 5,'Grade',1,0,'C');
            $pdf->cell(70, 5,'Cours',1,0,'C');
            $pdf->cell(50, 5,'Faculte',1,0,'C');
            $pdf->cell(18, 5,'V. Horaire',1,0,'C');
            $pdf->cell(15, 5,'Taux',1,0,'C');
            $pdf->cell(15, 5,'Total',1,0,'C');
            $pdf->cell(20, 5, decode_fr('Total payé'),1,0,'C');
            $pdf->cell(15, 5,'Solde',1,0,'C');
            $pdf->Ln(5);

            $req = ConnexionBdd::Connecter()->prepare("SELECT * FROM gest_honoraire WHERE faculte = ? AND annee_acad = ? HAVING total-total_payer=0");
            $req->execute(array($_POST['faculte'], $annee_acad));

            $pdf->SetFont('Arial','',7);
            while ($res1=$req->fetch()) {
                $pdf->SetFont('Arial','',9);
                $pdf->cell(60, 5, decode_fr($res1['noms_ens']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr($res1['grade_ens']), 1, 0, 'L');
                $pdf->cell(70, 5, decode_fr($res1['cours']), 1, 0, 'L');
                $pdf->cell(50, 5, decode_fr($res1['faculte']), 1, 0, 'L');
                $pdf->cell(18, 5, decode_fr($res1['heure_th'] + $res1['heure_pr']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('$'.$res1['taux']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('$'.$res1['total']), 1, 0, 'L');
                $pdf->cell(20, 5, decode_fr('$'.$res1['total_payer']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('$'.floatval($res1['total']-$res1['total_payer'])), 1, 0, 'L');
                $pdf->Ln(5);
                $a[] = $res1['heure_th'] + $res1['heure_pr'];
                $b[] = $res1['total'];
                $c[] = $res1['total_payer'];
                $d[] = floatval($res1['total']-$res1['total_payer']);
            }
            $pdf->SetFont('Arial','',9);
            $pdf->cell(60, 5, decode_fr("Total"), 1, 0, 'L');
            $pdf->cell(15, 5, decode_fr("-"), 1, 0, 'L', true);
            $pdf->cell(70, 5, decode_fr("-"), 1, 0, 'L', true);
            $pdf->cell(50, 5, decode_fr("-"), 1, 0, 'L', true);
            $pdf->cell(18, 5, decode_fr(array_sum($a)), 1, 0, 'L');
            $pdf->cell(15, 5, decode_fr("-"), 1, 0, 'L', true);
            $pdf->cell(15, 5, decode_fr('$'.array_sum($b)), 1, 0, 'L');
            $pdf->cell(20, 5, decode_fr('$'.array_sum($c)), 1, 0, 'L');
            $pdf->cell(15, 5, decode_fr('$'.array_sum($d)), 1, 0, 'L');
            $pdf->Ln(5);
            $a = array();
            $b = array();
            $c = array();
            $d = array();
        }else{
            die();
        }
        footer($pdf);
    }

    //btn_encours btn_cours_payer
    if(isset($_POST['btn_encours'])){
        $pdf = new FPDF('L', 'mm', 'A4');
        $annee_acad = $_POST['annee_acad'];

        all($pdf);
        $pdf->SetFont('Arial','BU',10);
        $pdf->cell(270, 10, decode_fr('RAPPORT GESTION DES HONORAIRES :: COURS ENCOURS DE PAYEMENT'), 0, 1, 'C');
        $pdf->SetFont('Arial','',10);
        if($_POST['faculte'] == "Tous"){
            $pdf->SetFont('Arial','B',10);
            $pdf->cell(60, 5,'Enseignants',1,0,'C');
            $pdf->cell(15, 5,'Grade',1,0,'C');
            $pdf->cell(70, 5,'Cours',1,0,'C');
            $pdf->cell(50, 5,'Faculte',1,0,'C');
            $pdf->cell(18, 5,'V. Horaire',1,0,'C');
            $pdf->cell(15, 5,'Taux',1,0,'C');
            $pdf->cell(15, 5,'Total',1,0,'C');
            $pdf->cell(20, 5, decode_fr('Total payé'),1,0,'C');
            $pdf->cell(15, 5,'Solde',1,0,'C');
            $pdf->Ln(5);

            $req = ConnexionBdd::Connecter()->prepare("SELECT * FROM gest_honoraire WHERE annee_acad = ? HAVING total-total_payer > 0");
            $req->execute(array($annee_acad));

            $pdf->SetFont('Arial','',7);
            while ($res1=$req->fetch()) {
                $pdf->SetFont('Arial','',9);
                $pdf->cell(60, 5, decode_fr($res1['noms_ens']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr($res1['grade_ens']), 1, 0, 'L');
                $pdf->cell(70, 5, decode_fr($res1['cours']), 1, 0, 'L');
                $pdf->cell(50, 5, decode_fr($res1['faculte']), 1, 0, 'L');
                $pdf->cell(18, 5, decode_fr($res1['heure_th'] + $res1['heure_pr']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('$'.$res1['taux']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('$'.$res1['total']), 1, 0, 'L');
                $pdf->cell(20, 5, decode_fr('$'.$res1['total_payer']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('$'.floatval($res1['total']-$res1['total_payer'])), 1, 0, 'L');
                $pdf->Ln(5);
                $a[] = $res1['heure_th'] + $res1['heure_pr'];
                $b[] = $res1['total'];
                $c[] = $res1['total_payer'];
                $d[] = floatval($res1['total']-$res1['total_payer']);
            }
            $pdf->SetFont('Arial','',9);
            $pdf->cell(60, 5, decode_fr("Total"), 1, 0, 'L');
            $pdf->cell(15, 5, decode_fr("-"), 1, 0, 'L', true);
            $pdf->cell(70, 5, decode_fr("-"), 1, 0, 'L', true);
            $pdf->cell(50, 5, decode_fr("-"), 1, 0, 'L', true);
            $pdf->cell(18, 5, decode_fr(array_sum($a)), 1, 0, 'L');
            $pdf->cell(15, 5, decode_fr("-"), 1, 0, 'L', true);
            $pdf->cell(15, 5, decode_fr('$'.array_sum($b)), 1, 0, 'L');
            $pdf->cell(20, 5, decode_fr('$'.array_sum($c)), 1, 0, 'L');
            $pdf->cell(15, 5, decode_fr('$'.array_sum($d)), 1, 0, 'L');
            $pdf->Ln(5);
            $a = array();
            $b = array();
            $c = array();
            $d = array();
        }else if($_POST['faculte'] != "Tous"){
            $pdf->SetFont('Arial','B',10);
            $pdf->cell(60, 5,'Enseignants',1,0,'C');
            $pdf->cell(15, 5,'Grade',1,0,'C');
            $pdf->cell(70, 5,'Cours',1,0,'C');
            $pdf->cell(50, 5,'Faculte',1,0,'C');
            $pdf->cell(18, 5,'V. Horaire',1,0,'C');
            $pdf->cell(15, 5,'Taux',1,0,'C');
            $pdf->cell(15, 5,'Total',1,0,'C');
            $pdf->cell(20, 5, decode_fr('Total payé'),1,0,'C');
            $pdf->cell(15, 5,'Solde',1,0,'C');
            $pdf->Ln(5);

            $req = ConnexionBdd::Connecter()->prepare("SELECT * FROM gest_honoraire WHERE faculte = ? AND annee_acad = ? HAVING total-total_payer > 0");
            $req->execute(array($_POST['faculte'], $annee_acad));

            $pdf->SetFont('Arial','',7);
            while ($res1=$req->fetch()) {
                $pdf->SetFont('Arial','',9);
                $pdf->cell(60, 5, decode_fr($res1['noms_ens']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr($res1['grade_ens']), 1, 0, 'L');
                $pdf->cell(70, 5, decode_fr($res1['cours']), 1, 0, 'L');
                $pdf->cell(50, 5, decode_fr($res1['faculte']), 1, 0, 'L');
                $pdf->cell(18, 5, decode_fr($res1['heure_th'] + $res1['heure_pr']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('$'.$res1['taux']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('$'.$res1['total']), 1, 0, 'L');
                $pdf->cell(20, 5, decode_fr('$'.$res1['total_payer']), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('$'.floatval($res1['total']-$res1['total_payer'])), 1, 0, 'L');
                $pdf->Ln(5);
                $a[] = $res1['heure_th'] + $res1['heure_pr'];
                $b[] = $res1['total'];
                $c[] = $res1['total_payer'];
                $d[] = floatval($res1['total']-$res1['total_payer']);
            }
            $pdf->SetFont('Arial','',9);
            $pdf->cell(60, 5, decode_fr("Total"), 1, 0, 'L');
            $pdf->cell(15, 5, decode_fr("-"), 1, 0, 'L', true);
            $pdf->cell(70, 5, decode_fr("-"), 1, 0, 'L', true);
            $pdf->cell(50, 5, decode_fr("-"), 1, 0, 'L', true);
            $pdf->cell(18, 5, decode_fr(array_sum($a)), 1, 0, 'L');
            $pdf->cell(15, 5, decode_fr("-"), 1, 0, 'L', true);
            $pdf->cell(15, 5, decode_fr('$'.array_sum($b)), 1, 0, 'L');
            $pdf->cell(20, 5, decode_fr('$'.array_sum($c)), 1, 0, 'L');
            $pdf->cell(15, 5, decode_fr('$'.array_sum($d)), 1, 0, 'L');
            $pdf->Ln(5);
            $a = array();
            $b = array();
            $c = array();
            $d = array();
        }

        footer($pdf);
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="Justin Micah" content="">
    <title><?=$p?></title>
    <link rel="shortcut icon" href="../../images/UNIGOM_W260px.jpg" type="image/x-icon">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        label, .link>button{
            /* font-size: 100%; */
            border:none;
        }
    </style>
</head>
<body id="page-top">
    <div id="wrapper">
        <?php require_once 'menu.php'; ?>
        <!-- End Sidebar -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
				<!-- menu user -->
                <?php require_once 'menu_user.php'; ?>
                <!-- main Content -->
                <div class="container-fluid mt-3">
                    <div class="card shadow">
                        <div class="card-header d-flex flex-row justify-content-between">
                            <div class="text-primary text-uppercase font-weight-bold">
                                <?php
                                    $an =  ConnexionBdd::Connecter()->query("SELECT * FROM annee_academique GROUP BY annee_acad ORDER BY id DESC LIMIT 1 ");
                                    $an_r = $an->fetch();
                                    if(!empty($an_r)){
                                       echo $p.' pour l\'annee acadmeique '.$an_r['annee_acad']; 
                                    }else{
                                        echo $p;
                                    }
                                ?></div>
                            <div class="btn-group">
                              <button type="button" class="btn btn-primary">Actions</button>
                              <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                              </button>
                              <div class="dropdown-menu">
                                <a class="dropdown-item" data-toggle="modal" data-target="#add_ens_gest_honoraire">
                                    <i class="fa fa-plus" aria-hidden="true"></i> Ajouter un enseignenant</a>
                                <div class="dropdown-divider"></div>
                                <button type="button" class="dropdown-item btn btn-primary" data-toggle="modal" data-target="#rapport_gh">Rapport</button>
                                <a class="dropdown-item" href="./rapport_pdf/gestion_hon_.php"> <i class="fa fa-print" aria-hidden="true"></i> Imprimmer</a>
                              </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-inverse table-* table-hover">
                                <thead class="thead-inverse bg-gray-100">
                                    <tr>
                                        <th>Noms enseignants</th>
                                        <th>Grade</th>
                                        <th>Cours</th>
                                        <th>Faculte</th>
                                        <th>Volume Horaire</th>
                                        <th>Prestation</th>
                                        <th>Type Enseign.</th>
                                        <th>Taux</th>
                                        <th>Total</th>
                                        <th>Total payé</th>
                                        <th>Solde</th>
                                        <th style="text-align:center"> ### </th>
                                    </tr>
                                    </thead>
                                    <tbody style="font-size: 80%;">
                                        <?php
                                            $result = ConnexionBdd::Connecter()->query("SELECT * FROM gest_honoraire");
                                            while($data = $result->fetch()){
                                                ?>
                                                    <tr>
                                                        <td id="id_transact_cours" style="display: none;"><?=$data['id']?></td>

                                                        <td id="ht" style="display: none;"><?=$data['heure_th']?></td>
                                                        <td id="hp" style="display: none;"><?=$data['heure_pr']?></td>

                                                        <td id="mt" style="display: none;"><?=$data['montant_ht']?></td>
                                                        <td id="mp" style="display: none;"><?=$data['montant_hp']?></td>

                                                        <td id="noms_prof"><?=$data['noms_ens']?></td>
                                                        <td id="grade_ense"><?=$data['grade_ens']?></td>
                                                        <td id="cours"><?=$data['cours']?></td>
                                                        <td id="faculte"><?=$data['faculte']?></td>
                                                        <td id="vol_horaire_" style="display: none;"><?=$data['heure_pr']+$data['heure_th']?></td>
                                                        <td><?=$data['heure_pr']+$data['heure_th'].' Heures'?></td>
                                                        <td id="prestation"><?=$data['prestation']?></td>
                                                        <td id="type_enseig"><?=$data['type_enseig']?></td>

                                                        <td id="taux" style="display: none;"><?=$data['taux']?></td>
                                                        <td id=""><?=$data['taux'].'$'?></td>
                                                        <td id="m_total"><?=$data['total']?></td>
                                                        <td id="m_deja_p"><?=$data['total_payer']?></td>
                                                        <td>$<?=$data['total'] - $data['total_payer']?></td>
                                                        <td>
                                                            <button id="btn_tnt" type="button" class="btn btn-primary btn-sm m-1" title="Effectuer le payement" <?php if($data['total'] == $data['total_payer']){echo 'disabled';}?> title="effectuer le payement"> Payement</button>
                                                            <button id="btn_del_info" type="button" class="btn btn-danger btn-sm m-1" title="Supprimer" <?php if($data['total'] == $data['total_payer']){echo 'disabled';}?> title="effectuer le payement"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                                            <button id="btn_enseign" type="button" class="btn btn-primary btn-sm m-1" title="Modifier les information de l'enseignant" <?php //if($data['total'] == $data['total_payer']){echo 'disabled';}?>><i class="fa fa-edit" aria-hidden="true"></i> </button>
                                                        </td>
                                                    </tr>
                                                <?php
                                            }
                                        ?>
                                    </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- fin main content-->
            </div>
            <!-- footer -->
		    <?php include './footer.php';?>
        </div>
    </div>

    <!-- Button trigger modal rapport gestion honoraire-->
    <div class="modal fade" id="rapport_gh" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Rapport Getion Honoraire</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <form action="" method="post" id="rapport_gh">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="">Annee Académique</label>
                            <select name="annee_acad" id="annee_acad" class="form-control">
                                <?php
                                    $a = ConnexionBdd::Connecter()->query("SELECT annee_acad FROM annee_academique");
                                    while($data = $a->fetch()){
                                        ?>
                                        <option value="<?=$data['annee_acad']?>"><?=$data['annee_acad']?></option>
                                    <?php }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="">Faculté</label>
                            <select name="faculte" id="faculte" class="form-control">
                                <option value="Tous">Tous</option>
                                <?php
                                    $a = ConnexionBdd::Connecter()->query("SELECT DISTINCT fac FROM faculte");
                                    while($data = $a->fetch()){
                                        ?>
                                        <option value="<?=$data['fac']?>"><?=$data['fac']?></option>
                                    <?php }
                                ?>
                            </select>
                        </div>
                        <div class="form-row">
                            <div class="col"><label for="">Prestation</label></div>
                            <div class="col"><label for="">Type enseignant</label></div>
                        </div>
                        <div class="form-row">
                            <div class="col">
                                <!-- <label for="">Prestation</label> -->
                                <select name="prestation" id="prestation" class="form-control">
                                    <!-- <option for="">- Prestation -</option> -->
                                    <option value="Tous">Tous</option>
                                    <?php
                                        $a = ConnexionBdd::Connecter()->query("SELECT DISTINCT prestation FROM gest_honoraire");
                                        while($data = $a->fetch()){
                                            ?>
                                            <option value="<?=$data['prestation']?>"><?=$data['prestation']?></option>
                                        <?php }
                                    ?>
                                </select>
                            </div>

                            <div class="col">
                                <!-- <label for="">Type enseignant</label> -->
                                <select name="type_enseign" id="type_enseign" class="form-control">
                                    <!-- <option for="">- Type enseignant -</option> -->
                                    <option value="Tous">Tous</option>
                                    <?php
                                        $a = ConnexionBdd::Connecter()->query("SELECT DISTINCT type_enseig FROM gest_honoraire");
                                        while($data = $a->fetch()){
                                            ?>
                                            <option value="<?=$data['type_enseig']?>"><?=$data['type_enseig']?></option>
                                        <?php }
                                    ?>
                                </select>
                            </div>
                        </div>
                        
                    </div>
                    <div class="m-1">
                        <div class="d-flex flex flex-column link">
                            <button type="submit" id="btn_fac" name="btn_fac" class="btn-link text-left mt-1" value="btn_fac">par faculte</button>
                            <button type="submit" id="btn_prestation" name="btn_prestation" class="btn-link text-left mt-1" value="btn_prestation">par prestation</button>
                            <button type="submit" id="btn_type_enseign" name="btn_type_enseign" class="btn-link text-left mt-1" value="btn_type_enseign">par type d'enseignant</button>
                            <button type="submit" id="btn_cours_payer" name="btn_cours_payer" class="btn-link text-left mt-1" value="btn_cours_payer">Cours deja payer</button>
                            <button type="submit" id="btn_encours" name="btn_encours" class="btn-link text-left mt-1" value="btn_encours">Cours encours de payement</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal pour ajouter un enseignant-->
    <div class="modal fade" id="add_ens_gest_honoraire" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog modal-md modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?=$p?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <form action="" method="post" id="ajouter_enseign_honoraire">
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="col">
                                <input type="text" class="form-control" name="noms_enseign_" id="noms_enseign_" aria-describedby="helpId" placeholder="Noms de l'enseignant" required>
                            </div>
                            
                            <div class="col">
                                <input type="text" class="form-control" name="grade_enseign_" id="grade_enseign_" aria-describedby="helpId" placeholder="Grade de l'enseignant" required> 
                            </div>
                        </div>

                        <div class="form-row mt-2">
                            <div class="col">
                                <select type="text" class="form-control" name="faculte_gh" id="faculte_gh" aria-describedby="helpId" placeholder="Faculte" required>
                                    <option>-Faculte-</option>
                                    <?php
                                        $f = ConnexionBdd::Connecter()->query("SELECT DISTINCT * FROM faculte");
                                        while($data = $f->fetch()){?>
                                            <option value="<?=$data['fac']?>"><?=$data['fac']?></option>
                                            <?php
                                        }
                                    ?>
                                </select>
                            </div>

                            <div class="col">
                                <input type="text" class="form-control" name="cours_enseign_" id="cours_enseign_" aria-describedby="helpId" placeholder="Cours" required>
                            </div>
                        </div>

                        <div class="form-row mt-2">
                            <div class="col">
                                <select class="form-control" name="type_enseignant" id="type_enseignant">
                                    <option> -Type enseignant- </option>
                                    <option value="Visiteur">Visiteur</option>
                                    <option value="Permanent">Permanent</option>
                                </select>
                            </div>

                            <div class="col">
                                <select class="form-control" name="type_prestation" id="type_prestation">
                                    <option> -Prestation- </option>
                                    <option value="Ordinaire">Ordinaire</option>
                                    <option value="Suplementaire">Suplementaire</option>
                                </select>
                            </div>

                            <div class="col">
                                <div class="input-group">
                                    <input type="number" class="form-control" name="taux_taux" id="taux_taux" aria-describedby="helpId" placeholder="Taux" min="1">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">$</div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>

                        <div class="form-row mt-2">
                            <div class="col">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="heure_t" id="heure_t" aria-describedby="helpId" placeholder="Heure theorie" required>
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">H</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="montant_ht" id="montant_ht" aria-describedby="helpId" placeholder="Montant" disabled>
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">$</div>
                                    </div>
                                </div>
                                
                            </div>

                            <div class="col">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="heure_pr" id="heure_pr" aria-describedby="helpId" placeholder="Heure pratique" required>
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">H</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="montant_pr" id="montant_pr" aria-describedby="helpId" placeholder="Montant" disabled>
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">$</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-2">
                            <div class="input-group">
                                <input type="text" class="form-control" name="total_gen" id="total_gen" aria-describedby="helpId" placeholder="Total" disabled>
                                <div class="input-group-prepend">
                                    <div class="input-group-text">$</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <span id="erreur_trans" class="p-3"> </span>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="btn_ghe" name="">Ajouter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- modal pour update le payement -->
    <div class="modal fade" id="mod_update_m" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Payement des honoraires</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <form action="" method="post" id="form_update_payement">
                    <div class="modal-body">
                        <p id="paragraphe"></p>
                        <input type="hidden" name="id_trans_p" id="id_trans_p">
                        <input type="hidden" name="montant_t_trans_p" id="montant_t_trans_p">
                        <input type="hidden" name="t_trans_p" id="t_trans_p">
                        <div class="form-group">
                            <input type="text" class="form-control" name="id_montant_p" id="id_montant_p" aria-describedby="helpId" placeholder="montant : ">
                        </div>
                    </div>
                    <span id="erreur_trans_pay" class="p3"></span>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="btn_pay">Effectuer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- info supprimer les informations -->
    <div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Suppression</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="post" id="form_delete_gh">
                    <div class="modal-body">
                        <div class="container-fluid text-danger">
                            <input type="hidden" name="id_delete_gh" id="id_delete_gh">
                            Voulez-vous vraiment proceder a la suppression ?
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"> NON</button>
                        <button type="submit" class="btn btn-primary"> OUI</button>
                    </div>
                    <small id="epp"></small>
                </form>
            </div>
        </div>
    </div> 

    <!-- modifier les infos de l' enseignant -->
    <div class="modal fade" id="mod_update_enseign" tabindex="-1" role="dialog" aria-laubelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier les informations de l'enseignant</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="post" id="form_update_payement_update">
                    <div class="modal-body">
                        <input type="hidden" id="_id_transact_cours">
                        <div class="form-row">
                            <div class="col">
                                <input type="text" class="form-control" name="noms_enseign_" id="_noms_enseign_" aria-describedby="helpId" placeholder="Noms de l'enseignant" required>
                            </div>
                            
                            <div class="col">
                                <input type="text" class="form-control" name="grade_enseign_" id="_grade_enseign_" aria-describedby="helpId" placeholder="Grade de l'enseignant" required> 
                            </div>
                        </div>

                        <div class="form-row mt-2">
                            <div class="col">
                                <select type="text" class="form-control" name="faculte_gh" id="_faculte_gh" aria-describedby="helpId" placeholder="Faculte" required>
                                    <option>-Faculte-</option>
                                    <?php
                                        $f = ConnexionBdd::Connecter()->query("SELECT DISTINCT * FROM faculte");
                                        while($data = $f->fetch()){?>
                                            <option value="<?=$data['fac']?>"><?=$data['fac']?></option>
                                            <?php
                                        }
                                    ?>
                                </select>
                            </div>

                            <div class="col">
                                <input type="text" class="form-control" name="_cours_enseign_" id="_cours_enseign_" aria-describedby="helpId" placeholder="Cours" required>
                            </div>
                        </div>

                        <div class="form-row mt-2">
                            <div class="col">
                                <select class="form-control" name="type_enseignant" id="_type_enseignant">
                                    <option> -Type enseignant- </option>
                                    <option value="Visiteur">Visiteur</option>
                                    <option value="Permanent">Permanent</option>
                                </select>
                            </div>

                            <div class="col">
                                <select class="form-control" name="type_prestation" id="_type_prestation">
                                    <option> -Prestation- </option>
                                    <option value="Ordinaire">Ordinaire</option>
                                    <option value="Suplementaire">Suplementaire</option>
                                </select>
                            </div>

                            <div class="col">
                                <div class="input-group">
                                    <input type="number" class="form-control" name="taux_taux" id="_taux_taux" aria-describedby="helpId" placeholder="Taux" min="1">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">$</div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>

                        <div class="form-row mt-2">
                            <div class="col">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="heure_t" id="_heure_t" aria-describedby="helpId" placeholder="Heure theorie" required>
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">H</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="montant_ht" id="_montant_ht" aria-describedby="helpId" placeholder="Montant" disabled>
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">$</div>
                                    </div>
                                </div>
                                
                            </div>

                            <div class="col">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="heure_pr" id="_heure_pr" aria-describedby="helpId" placeholder="Heure pratique" required>
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">H</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="montant_pr" id="_montant_pr" aria-describedby="helpId" placeholder="Montant" disabled>
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">$</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-2">
                            <div class="input-group">
                                <input type="text" class="form-control" name="total_gen" id="_total_gen" aria-describedby="helpId" placeholder="Total" disabled>
                                <div class="input-group-prepend">
                                    <div class="input-group-text">$</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <span id="__erreur_trans_" class="p3"></span>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="btn_pay_">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <?php include_once("modal_decon.php");?>
    
    <!-- insertion de l'enseignant -->
    <script type="text/javascript">
        // empecher les utilisateurs d'entree n importe quoi !
        $("#btn_ghe").attr('disabled', true);
        $("#taux_taux").keyup(function (e) { 
            $("#erreur_trans").css('display', 'bloc');
            var x = $("#taux_taux").val();
            if(!isNaN(x) && x >= 1 && x !="" && x !="0."){
                if(x !=""){
                    $("#btn_ghe").removeAttr('disabled');
                    $("#erreur_trans").html('');
                    $("#erreur_trans").removeClass('text-danger');
                    $("#erreur_trans").html('montant valide (: :)').css('color','green').addClass('text-success');
                    console.log(x + "is a number");
                    $("#taux_taux").val(x);

                    // $("#total_gen").val(eval($("#taux").val() * $("#volume_horaire_enseign_").val()));
                    $("#montant_ht").val(eval($("#heure_t").val() * $("#taux_taux").val()));
                    $("#total_gen").val(eval($("#montant_ht").val()) + eval($("#montant_pr").val()));
                    $("#montant_pr").val(eval($("#heure_pr").val() * $("#taux_taux").val()));
                    $("#total_gen").val(eval($("#montant_ht").val()) + eval($("#montant_pr").val()));
                }else{
                    $("#erreur_trans").html('');
                    $("#erreur_trans").html("une valeur est requis").addClass('text-danger');
                    $("#btn_ghe").attr('disabled', true);
                }
            }else{
                $("#erreur_trans").html('');
                $("#erreur_trans").html("Veuillez saisir un montant valide.").addClass('text-danger');
                $("#btn_ghe").attr('disabled', true);
            }
        });

        $("#heure_t").keyup(function (e) { 
            var x = $("#heure_t").val();
            if(!isNaN(x) && x >= 0 && x !="" && x !="0."){
                if(x !=""){
                    $("#btn_ghe").removeAttr('disabled');
                    $("#erreur_trans").html('');
                    $("#erreur_trans").removeClass('text-danger');
                    $("#erreur_trans").html('montant valide (: :)').css('color','green').addClass('text-success');
                    console.log(x + "is a number");
                    $("#heure_t").val(x);

                    $("#montant_ht").val(eval($("#heure_t").val() * $("#taux_taux").val()));
                    $("#total_gen").val(eval($("#montant_ht").val()) + eval($("#montant_pr").val()));
                }else{
                    $("#erreur_trans").html('');
                    $("#erreur_trans").html("une valeur est requis").addClass('text-danger');
                    $("#btn_ghe").attr('disabled', true);
                }
            }else{
                $("#erreur_trans").html('');
                $("#erreur_trans").html("Veuillez saisir un montant valide.").addClass('text-danger');
                $("#btn_ghe").attr('disabled', true);
            }
        });

        $("#heure_pr").keyup(function (e) { 
            var x = $("#heure_pr").val();
            if(!isNaN(x) && x >= 0 && x !="" && x !="0."){
                if(x !=""){
                    $("#btn_ghe").removeAttr('disabled');
                    $("#erreur_trans").html('');
                    $("#erreur_trans").removeClass('text-danger');
                    $("#erreur_trans").html('montant valide (: :)').css('color','green').addClass('text-success');
                    console.log(x + "is a number");
                    $("#heure_pr").val(x);

                    $("#montant_pr").val(eval($("#heure_pr").val() * $("#taux_taux").val()));
                    $("#total_gen").val(eval($("#montant_ht").val()) + eval($("#montant_pr").val()));
                }else{
                    $("#erreur_trans").html('');
                    $("#erreur_trans").html("une valeur est requis").addClass('text-danger');
                    $("#btn_ghe").attr('disabled', true);
                }
            }else{
                $("#erreur_trans").html('');
                $("#erreur_trans").html("Veuillez saisir un montant valide.").addClass('text-danger');
                $("#btn_ghe").attr('disabled', true);
            }
        });

        $("#ajouter_enseign_honoraire").submit(function (e) { 
            e.preventDefault();
            const data = {
                noms_enseign_:$("#noms_enseign_").val(),
                grade_enseign_:$("#grade_enseign_").val(),
                faculte_gh:$("#faculte_gh").val(),
                cours_enseign_:$("#cours_enseign_").val(),
                type_enseignant:$("#type_enseignant").val(),
                type_prestation:$("#type_prestation").val(),
                taux:$("#taux_taux").val(),
                heure_t:$("#heure_t").val(),
                montant_ht:$("#montant_ht").val(),
                heure_pr:$("#heure_pr").val(),
                montant_pr:$("#montant_pr").val(),
                total_gen:$("#total_gen").val()
            };

            $.ajax({
                type: "POST",
                url: "../../includes/gestion_hon_ens.php",
                data: data,
                beforeSend: function(e){
                    $("#erreur_trans").removeClass('text-danger');
                    $("#erreur_trans").html('Un instant svp ...').css('color','green').addClass('text-success');
                },
                success: function (data) {
                    if(data !="" && data == "ok"){
                        $("#erreur_trans").removeClass('text-danger');
                        $("#erreur_trans").html('ok, traitement reussi avec succes, ...').css('color','green').addClass('text-success');
                        $("#noms_enseign_").val('');
                        $("#grade_enseign_").val('');
                        $("#cours_enseign_").val('');
                        $("#volume_horaire_enseign_").val('');
                        $("#taux_enseign_").val('');
                        $("#total_gen").val('');
                        window.location.reload();
                    }else{
                        $("#erreur_trans").removeClass('text-success');
                        $("#erreur_trans").html("Erreur : " + data).css('color','red').addClass('text-danger');
                    }
                },
                error: function (e){
                    $("#erreur_trans").html('');
                    $("#erreur_trans").removeClass('text-success');
                    $("#erreur_trans").html('Erreur de connexion ...').css('color','red').addClass('text-danger');
                }
            });
        });
    </script>

    <!-- les payements -->
    <script type="text/javascript">
        $('table').on('click', '#btn_tnt', function(e){ 
            e.preventDefault();
            $("#mod_update_m").modal('toggle');
            b = $(this);
            m = $(this).parent();
            mm = $(m).parent();

            id_transact_cours = mm.find("#id_transact_cours");
            noms_prof = mm.find("#noms_prof");
            cours = mm.find("#cours");
            m_total = mm.find("#m_total");
            m_deja_p = mm.find("#m_deja_p");

            $("#paragraphe").html("payement de <b>" + noms_prof.text() +"</b> sur le cours : <b>" + cours.text()+"</b>");
            $("#id_trans_p").val(id_transact_cours.text());
            $("#montant_t_trans_p").val(m_total.text());
            $("#t_trans_p").val(m_deja_p.text());
        });

        // empecher les utilisateurs d'entree n importe quoi !
        $("#btn_pay").attr('disabled', true);
        $("#id_montant_p").keyup(function (e) { 
            var x = $("#id_montant_p").val();
            if(!isNaN(x) && x >= 1 && x !="" && x !="0."){
                if(x !=""){
                    $("#btn_pay").removeAttr('disabled');
                    $("#erreur_trans_pay").html('');
                    $("#erreur_trans_pay").removeClass('text-danger');
                    $("#erreur_trans_pay").html('montant valide (: :)').css('color','green').addClass('text-success');
                    console.log(x + "is a number");
                    $("#id_montant_p").val(x);

                    if(parseFloat($("#id_montant_p").val()) <= parseFloat($("#montant_t_trans_p").val())){
                        $("#btn_pay").removeAttr('disabled');
                        $("#erreur_trans_pay").html('');
                        $("#erreur_trans_pay").removeClass('text-danger');
                        $("#erreur_trans_pay").html('montant valide (: :)').css('color','green').addClass('text-success');
                    }else{
                        $("#erreur_trans_pay").html('');
                        $("#erreur_trans_pay").html("Le montant saisi est invalide. il est superieur a celui qui etait defini.").addClass('text-danger');
                        $("#btn_pay").attr('disabled', true);
                    }
                }else{
                    $("#erreur_trans_pay").html('');
                    $("#erreur_trans_pay").html("une valeur est requis").addClass('text-danger');
                    $("#btn_pay").attr('disabled', true);
                }
            }else{
                $("#erreur_trans_pay").html('');
                $("#erreur_trans_pay").html("Veuillez saisir un montant valide.").addClass('text-danger');
                $("#btn_pay").attr('disabled', true);
                console.log(x + "is not a number");
            }
        });

        // soummetre le formulaire pour le payement
        $("#form_update_payement").submit(function (e) { 
            e.preventDefault();
           if($("#id_montant_p").val() !="" && $("#id_trans_p").val()){
               var m = parseFloat($("#id_montant_p").val()) + parseFloat($("#t_trans_p").val());
               const data = {
                   id:$("#id_trans_p").val(),
                   montant:m
               };
                $.ajax({
                    type: "POST",
                    url: "../../includes/update_pay_gest_hon.php",
                    data: data,
                    beforeSend: function(){
                        $("#erreur_trans_pay").html('');
                        $("#erreur_trans_pay").html("Un instant svp ...").addClass('text-success');
                        $("#id_montant_p").css({'border-color':'none'});
                    },
                    success: function (response) {
                        if(response !="" && response == "ok"){
                            $("#erreur_trans_pay").html('');
                            $("#erreur_trans_pay").html("Payement effectuer avec succes ...").addClass('text-success');
                            $("#id_montant_p").css({'border-color':'none'});
                            window.location.reload();
                        }else{
                            $("#id_montant_p").css({'border-color':'red'});
                            $("#erreur_trans_pay").html('');
                            $("#erreur_trans_pay").html("Erreur : "+response).addClass('text-danger');
                        }
                    },
                    error: function(){
                        $("#id_montant_p").css({'border-color':'red'});
                        $("#erreur_trans_pay").html('');
                        $("#erreur_trans_pay").html("Erreur de connexion ...").addClass('text-danger');
                    }
                });
           } else {
                $("#id_montant_p").css({'border-color':'red'});
                $("#erreur_trans_pay").html('');
                $("#erreur_trans_pay").html("Veuillez saisir un montant svp ...").addClass('text-danger');
                $("#btn_pay").attr('disabled', true);
           }
        });
    </script>

    <!-- update -->
    <script>
        // $("#btn_pay_").attr('disabled', true);
        $("#_taux_taux").keyup(function (e) { 
            $("#erreur_trans").css('display', 'bloc');
            var x = $("#_taux_taux").val();
            if(!isNaN(x) && x >= 1 && x !="" && x !="0."){
                if(x !=""){
                    $("#btn_pay_").removeAttr('disabled');
                    $("#__erreur_trans_").html('');
                    $("#__erreur_trans_").removeClass('text-danger');
                    $("#__erreur_trans_").html('montant valide (: :)').css('color','green').addClass('text-success');
                    console.log(x + "is a number");
                    $("#_taux_taux").val(x);

                    // $("#total_gen").val(eval($("#taux").val() * $("#volume_horaire_enseign_").val()));
                    $("#_montant_ht").val(eval($("#_heure_t").val() * $("#_taux_taux").val()));
                    // $("#_total_gen").val(eval($("#_montant_ht").val()) + eval($("#_montant_pr").val()));
                    $("#_montant_pr").val(eval($("#_heure_pr").val() * $("#_taux_taux").val()));
                    $("#_total_gen").val(eval($("#_montant_ht").val()) + eval($("#_montant_pr").val()));
                }else{
                    $("#__erreur_trans_").html('');
                    $("#__erreur_trans_").html("une valeur est requis").addClass('text-danger');
                    $("#btn_pay_").attr('disabled', true);
                }
            }else{
                $("#__erreur_trans_").html('');
                $("#__erreur_trans_").html("Veuillez saisir un montant valide.").addClass('text-danger');
                $("#btn_pay_").attr('disabled', true);
            }
        });

        $("#_heure_t").keyup(function (e) { 
            var x = $("#_heure_t").val();
            if(!isNaN(x) && x >= 0 && x !="" && x !="0."){
                if(x !=""){
                    $("#btn_pay_").removeAttr('disabled');
                    $("#__erreur_trans_").html('');
                    $("#__erreur_trans_").removeClass('text-danger');
                    $("#__erreur_trans_").html('montant valide (: :)').css('color','green').addClass('text-success');
                    console.log(x + "is a number");
                    $("#_heure_t").val(x);

                    $("#_montant_ht").val(eval($("#_heure_t").val() * $("#_taux_taux").val()));
                    $("#_total_gen").val(eval($("#_montant_ht").val()) + eval($("#_montant_pr").val()));
                }else{
                    $("#__erreur_trans_").html('');
                    $("#__erreur_trans_").html("une valeur est requis").addClass('text-danger');
                    $("#btn_pay_").attr('disabled', true);
                }
            }else{
                $("#__erreur_trans_").html('');
                $("#__erreur_trans_").html("Veuillez saisir un montant valide.").addClass('text-danger');
                $("#btn_pay_").attr('disabled', true);
            }
        });
        
        $("#_heure_pr").keyup(function (e) { 
            var x = $("#_heure_pr").val();
            if(!isNaN(x) && x >= 0 && x !="" && x !="0."){
                if(x !=""){
                    $("#btn_pay_").removeAttr('disabled');
                    $("#__erreur_trans_").html('');
                    $("#__erreur_trans_").removeClass('text-danger');
                    $("#__erreur_trans_").html('montant valide (: :)').css('color','green').addClass('text-success');
                    console.log(x + "is a number");
                    $("#_heure_pr").val(x);

                    $("#_montant_pr").val(eval($("#_heure_pr").val() * $("#_taux_taux").val()));
                    $("#_total_gen").val(eval($("#_montant_pr").val()) + eval($("#_montant_ht").val()));
                }else{
                    $("#__erreur_trans_").html('');
                    $("#__erreur_trans_").html("une valeur est requis").addClass('text-danger');
                    $("#btn_pay_").attr('disabled', true);
                }
            }else{
                $("#__erreur_trans_").html('');
                $("#__erreur_trans_").html("Veuillez saisir un montant valide.").addClass('text-danger');
                // $("#btn_pay_").attr('disabled', true);
            }
        });
        
        // mod_update_enseign
        $('table').on('click', '#btn_enseign', function(e){ 
            e.preventDefault();
            $("#mod_update_enseign").modal('toggle');
            b = $(this);
            m = $(this).parent();
            mm = $(m).parent();

            id_transact_cours = mm.find("#id_transact_cours");   
            noms_prof = mm.find("#noms_prof");
            grade_ense = mm.find("#grade_ense");
            cours = mm.find("#cours");

            faculte = mm.find("#faculte");
            prestation = mm.find("#prestation");
            type_enseig = mm.find("#type_enseig");
            taux = mm.find("#taux");
            m_total = mm.find("#m_total");
            ht = mm.find("#ht");
            hp = mm.find("#hp");

            mt = mm.find("#mt");
            mp = mm.find("#mp");

            $("#_id_transact_cours").val(id_transact_cours.text());
            $("#_noms_enseign_").val(noms_prof.text());
            $("#_grade_enseign_").val(grade_ense.text());
            $("#_cours_enseign_").val(cours.text());

            $("#_heure_t").val(ht.text());
            $("#_heure_pr").val(hp.text());

            $("#_montant_ht").val(mt.text());
            $("#_montant_pr").val(mp.text());

            $("#_total_gen").val(m_total.text());
            $("#_taux_taux").val(taux.text());

            $("#form_update_payement_update").submit(function (e) { 
                e.preventDefault();
                // erreur_trans_
                const data = {
                    update:"update",
                    id_update :$("#_id_transact_cours").val(),
                    noms_enseign_:$("#_noms_enseign_").val(),
                    grade_enseign_:$("#_grade_enseign_").val(),
                    faculte_gh:$("#_faculte_gh").val(),
                    cours_enseign_:$("#_cours_enseign_").val(),
                    type_enseignant:$("#_type_enseignant").val(),
                    type_prestation:$("#_type_prestation").val(),
                    taux:$("#_taux_taux").val(),
                    heure_t:$("#_heure_t").val(),
                    montant_ht:$("#_montant_ht").val(),
                    heure_pr:$("#_heure_pr").val(),
                    montant_pr:$("#_montant_pr").val(),
                    total_gen:$("#_total_gen").val()
                };

                $.ajax({
                    type: "POST",
                    url: "../../includes/t3_update_honoraire.php",
                    data: data,
                    beforeSend: function(){
                        $("#__erreur_trans_").html('');
                        $("#__erreur_trans_").html("Un instant svp ...").addClass('text-primary');
                    },
                    success: function (response) {
                        if(response !="" && response == "ok"){
                            $("#__erreur_trans_").html('ok mise a jour reussi.');
                            $("#mod_update_enseign").modal('toggle');
                            window.location.reload();
                        }else{
                            $("#__erreur_trans_").html('');
                            $("#__erreur_trans_").html(response);
                        }
                    },
                    error:function(){
                        $("#__erreur_trans_").html('');
                        $("#__erreur_trans_").html("Erreur de connexion").addClass('text-danger');
                    }
                });
            });
        });
    </script>

    <script>
        // btn_del_info
        $('table').on('click', '#btn_del_info', function(e){ 
            e.preventDefault();
            $("#modelId").modal('toggle');
            b = $(this);
            m = $(this).parent();
            mm = $(m).parent();

            id_transact_cours = mm.find("#id_transact_cours");
            noms_prof = mm.find("#noms_prof");
            cours = mm.find("#cours");
            m_total = mm.find("#m_total");
            m_deja_p = mm.find("#m_deja_p");

            $("#id_delete_gh").val(id_transact_cours.text());
                // alert($("#id_delete_gh").val());
                $("#form_delete_gh").submit(function (e) { 
                    e.preventDefault();
                    if($("#id_delete_gh").val() !=""){
                    /**
                     * form_delete_gh
                     id_delete_gh
                    */
                    const data = {
                        delete_gh:"delete_gh",
                        id_delete_gh:$("#id_delete_gh").val()
                    };
                    $.ajax({
                        type: "POST",
                        url: "../../includes/t3_update_honoraire.php",
                        data: data,
                        success: function (response) {
                            if(response !="" && response == "ok"){
                                $(mm).slideUp();
                                $("#modelId").modal('toggle');
                            }else{
                                $("#epp").html("");
                                $("#epp").html("certains champs sont vide.");
                            }
                        }
                    });
                }else{
                    $("#epp").html("");
                    $("#epp").html("Veuillez completer tous les champs svp !!!");
                }
            });
            
        });
    </script>
</body>
</html>