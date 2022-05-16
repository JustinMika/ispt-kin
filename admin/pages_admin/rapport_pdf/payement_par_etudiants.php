<?php
    session_start();
    require_once '../../../includes/ConnexionBdd.class.php';
	// FPDF
	require '../fpdf/fpdf.php';

    if(array_sum($_SESSION) == 0 && empty($_GET['a'])){
        header('location:../dec.php');
        exit();
    }
    if(empty($_SESSION['data']) || empty($_SESSION)){
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

    $pdf = new FPDF('P', 'mm', 'A4');
	$pdf->AddPage();
	$pdf->SetFont('Arial','B',12);

	$pdf->cell(150,10,'',0,1,'C');
    $pdf->cell(290,6, decode_fr(strtoupper("institut superieur pedagogique et technique de kinshasa")),0,1,'C');
    $pdf->SetFont('Arial','',11); //Mail : info@isptkin.ac.cd
    $pdf->cell(290,6, decode_fr("ISPT-KIN"),0,1,'C');
    $pdf->cell(290,6, decode_fr("E-mail : info@isptkin.ac.cd"),0,1,'C');
    $pdf->cell(290,6, decode_fr("site web : www.isptkin.ac.cd"),0,1,'C', false, 'www.isptkin.ac.cd');

    $pdf->Ln(5);
    // logo de la faculte
    $pdf->Image("../../../images/ispt_kin.png", 10,15,25, 25);
    $pdf->Ln(2);
    $pdf->cell(260,1 ,"",1,1,'C', true);

    $pdf->Ln(1);
    $pdf->cell(197,7,'PAYEMENT POUR CHAQUE ETUDIANT : ['.$_GET['a'].']',0,1,'C');
    $pdf->Ln(2);

    // three arrays
    $a = array();
    $b = array();
    $c = array();

    // on commence par selectionner tous les etudiants
    $sel_all = ConnexionBdd::Connecter()->prepare("SELECT * FROM etudiants_inscrits WHERE annee_academique = ? ORDER BY fac ASC");
    $sel_all->execute(array($_GET['a']));

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
        $sql = "SELECT faculte, promotion, type_frais, SUM(montant) as mt FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ? GROUP BY faculte, type_frais";
        $sql_array = array($data_student['matricule'], $data_student['promotion'], $data_student['fac'], $data_student['annee_academique']);
                                                    
        $all = ConnexionBdd::Connecter()->prepare($sql);
        $all->execute($sql_array);

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
                $pdf->cell(22, 5, '$ '.montant_restant_pourcent($d['mp'], $data['mt']), 1, 0, 'L');
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
        $pdf->Ln(5);

        $pdf->Ln(5);
    }

    $pdf->Ln(5);
    $pdf->SetFont('Arial','',10);
	$pdf->cell(300,20, decode_fr('par : '.$_SESSION['data']['noms'].'; le '.date('d/M/Y')),0,1,'C');
    $pdf->output();
?>