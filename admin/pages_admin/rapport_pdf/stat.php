<?php
	session_start();
	require_once '../../../includes/ConnexionBdd.class.php';
	// FPDF
	require '../fpdf/fpdf.php';

    if(array_sum($_SESSION) == 0){
        header('location:../dec.php');
        exit();
    }

    // three arrays
    $a = array();
    $b = array();
    $c = array();
    $dd = array();

	$pdf = new FPDF('P', 'mm', 'A4');
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

    $pdf->Ln(1);
    $pdf->SetFont('Arial','UB',12);
    $pdf->cell(197, 10, 'PAYEMENT PAR FACULTE ET PAR PROMOTION', 0, 1, 'C');
    $pdf->Ln(3);

    $pdf->cell(50, 10, 'PAYEMENT PAR FACULTE', 0, 1, 'C');

    //Tableau
    $pdf->SetFont('Arial','B',10);
    $pdf->cell(60, 5,decode_fr('Faculté'),1,0,'L');
    $pdf->cell(30, 5,decode_fr('Montant à payer'),1,0,'C');
    $pdf->cell(30, 5,decode_fr('Montant payé'),1,0,'C');
    $pdf->cell(20, 5,'Solde',1,0,'C');
    $pdf->cell(15, 5,' % ',1,0,'L');
    $pdf->Ln(5);

    $frais_par_fac = ConnexionBdd::Connecter()->query("SELECT faculte.fac, payement.faculte AS f, SUM(payement.montant) AS m FROM faculte LEFT JOIN payement ON faculte.fac = payement.faculte GROUP BY faculte.fac");

    $pdf->SetFont('Arial','',9);
    while($data_fac = $frais_par_fac->fetch()){
        $mt = ConnexionBdd::Connecter()->prepare("SELECT SUM(montant) AS mf FROM affectation_frais WHERE faculte = ?");
        $mt->execute(array($data_fac['fac']));

        while($d = $mt->fetch()){
            $pdf->cell(60, 5, decode_fr($data_fac['fac']), 1, 0, 'L');
            $pdf->cell(30, 5, decode_fr(m_format($d['mf'])), 1, 0, 'L');
            $pdf->cell(30, 5, decode_fr(m_format($data_fac['m'])), 1, 0, 'L');
            $pdf->cell(20, 5, decode_fr(m_format($d['mf']-$data_fac['m'])), 1, 0, 'L');
            $pdf->cell(15, 5, decode_fr(montant_restant_pourcent($data_fac['m'], $d['mf'])).'%', 1, 0, 'L');
            $a[] = $d['mf'];
            $b[] = $data_fac['m'];
            $c[] = $d['mf']-$data_fac['m'];
            $pdf->Ln(5);
        }
    }
    if(count($a) > 0 && count($b) > 0 && count($c) > 0){
        $pdf->cell(60, 5, decode_fr("Total") , 1, 0, 'L');
        $pdf->cell(30, 5, '$ '.decode_fr(array_sum($a)) , 1, 0, 'L');
        $pdf->cell(30, 5, '$ '.decode_fr(array_sum($b)) , 1, 0, 'L');
        $pdf->cell(20, 5, '$ '.decode_fr(array_sum($c)) , 1, 0, 'L');
        $pdf->cell(15, 5, ''.''.decode_fr(montant_restant_pourcent(array_sum($b), array_sum($a))).'%' , 1, 0, 'L');
        $a = array();
        $b = array();
        $c = array();
        $dd = array();
        $pdf->Ln(5);
    }else{
        $pdf->cell(60, 5, decode_fr("############ pas de payement ###########") , 0, 0, 'L');
    }
    $pdf->Ln(5);
    $pdf->cell(50, 10, 'PAYEMENT PAR PROMOTION', 0, 1, 'C');

    $mt = ConnexionBdd::Connecter()->query("SELECT affectation_frais.promotion, SUM(affectation_frais.montant) as m FROM affectation_frais GROUP BY affectation_frais.promotion ");

    // ENTETE DE TABLEAU
    $pdf->SetFont('Arial','B',10);
    $pdf->cell(20, 5,'Promotion',1,0,'L');
    $pdf->cell(35, 5,decode_fr('Montant à payer'),1,0,'C');
    $pdf->cell(35, 5,decode_fr('Montant payé'),1,0,'C');
    $pdf->cell(35, 5,'Solde',1,0,'C');
    $pdf->cell(15, 5,' % ',1,0,'L');
    $pdf->Ln(5);
    $pdf->SetFont('Arial','I',10);
    while($data = $mt->fetch()){
        $sql = "SELECT sum(montant) AS montant FROM `payement` WHERE promotion = ?";
    
        $frais_fac = ConnexionBdd::Connecter()->prepare($sql);
        $frais_fac->execute(array($data['promotion']));
        while($d = $frais_fac->fetch()){
                $pdf->cell(20, 5, decode_fr($data['promotion']), 1, 0, 'L');
                $pdf->cell(35, 5, decode_fr(m_format($data['m'])), 1, 0, 'L');
                $pdf->cell(35, 5, decode_fr(m_format($d['montant'])), 1, 0, 'L');
                $pdf->cell(35, 5, decode_fr(m_format($data['m'] - $d['montant'])), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr(montant_restant_p($d['montant'], $data['m'])).'%', 1, 0, 'L');
                $a[] = $data['m'];
                $b[] = $d['montant'];
                $c[] = $data['m']-$d['montant'];
                $dd[] = montant_restant_p($d['montant'], $data['m']);
                $pdf->Ln(5);
        }
    }
    if(count($a) > 0 && count($b) > 0 && count($c) > 0){
        $pdf->cell(20, 5, decode_fr("Total") , 1, 0, 'L');
        $pdf->cell(35, 5, '$ '.decode_fr(array_sum($a)) , 1, 0, 'L');
        $pdf->cell(35, 5, '$ '.decode_fr(array_sum($b)) , 1, 0, 'L');
        $pdf->cell(35, 5, '$ '.decode_fr(array_sum($c)) , 1, 0, 'L');
        $pdf->cell(15, 5, ''.decode_fr(montant_restant_pourcent(array_sum($b), array_sum($a))).'%' , 1, 0, 'L');
        $a = array();
        $b = array();
        $c = array();
        $dd = array();
        $pdf->Ln(5);
    }else{
        $pdf->cell(60, 5, decode_fr("############ pas de payement ###########") , 0, 0, 'L');
    }

    $pdf->Ln(5);
    $pdf->SetFont('Arial','',10);
	$pdf->cell(300,20, decode_fr('par : '.$_SESSION['data']['noms'].'; le '.date('d/M/Y')),0,1,'C');
    $pdf->output();
?>