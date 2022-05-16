<?php
	session_start();
	require_once '../../../includes/ConnexionBdd.class.php';
	// FPDF
	require '../fpdf/fpdf.php';

    if(array_sum($_SESSION) == 0){
        header('location:../dec.php');
        exit();
    }

    if(isset($_SESSION['data']['noms']) && !empty($_SESSION['data']['noms'])){

    }else{
        header('location:../dec');
        exit();
    }


    // print_r($_SESSION);
    $pdf = new FPDF('P', 'mm', 'A4');
	$pdf->AddPage();
	$pdf->SetFont('Arial','B',12);

	$pdf->cell(150,10,'',0,1,'C');
    $pdf->cell(197,6, decode_fr("INSTITUT SUPERIEUR PEDAGOGIQUE ET TECHNIQUE DE KINSHASA"),0,1,'C');
    $pdf->SetFont('Arial','',11);
    $pdf->cell(197,6, decode_fr("«UNIGOM»"),0,1,'C');
    $pdf->cell(197,6, decode_fr("BP 204 Goma (RDC)"),0,1,'C');
    $pdf->cell(197,6, decode_fr("BP 2277 Gisenyi (RWANDA)"),0,1,'C');
    $pdf->cell(197,6, decode_fr("E-mail : rectorat@unigom.ac.cd"),0,1,'C');
    $pdf->cell(197,6, decode_fr("site web : www.unigom.ac.cd"),0,1,'C', false, 'www.unigom.ac.cd');

    $pdf->Ln(5);
    $pdf->cell(150,10,'',0,1,'L');
    // logo de la faculte
    $pdf->Image("../../../images/ispt_kin.png", 15,25,30, 30);
    $pdf->cell(190,1 ,"",1,1,'C', true);

    $pdf->Ln(1);
    $pdf->SetFont('Arial','BU',8);
    $pdf->cell(197,7,ucwords('PAYEMENT PAR TYPE DE FRAIS, FACULTE ET PAR PROMOTION'),0,1,'C');
    $pdf->Ln(1);

    $pdf->SetFont('Arial','',10);
    // on recupere les facultes et les promotions
    $qsl_insc = "SELECT fac, promotion FROM `etudiants_inscrits` GROUP BY fac, promotion";
    $qsl_insc_array = array();
    $sel_ins = ConnexionBdd::Connecter()->prepare($qsl_insc);
    $sel_ins->execute($qsl_insc_array);

    // three arrays
    $a = array();
    $b = array();
    $c = array();

    while ($donnees = $sel_ins->fetch()){
        $pdf->Ln(5);
        $pdf->SetFont('Arial','B',8);
        $pdf->cell(15, 5, decode_fr("Faculte      : ".$donnees['fac']),0,1,'L');
        $pdf->cell(15, 5, decode_fr("Promotion : ".$donnees['promotion']),0,1,'L');

        $sql = "SELECT faculte, promotion, type_frais, SUM(montant) as mt FROM affectation_frais WHERE faculte = ? AND promotion = ? GROUP BY faculte, type_frais";
        $sql_array = array($donnees['fac'], $donnees['promotion']);

        $all = ConnexionBdd::Connecter()->prepare($sql);
        $all->execute($sql_array);
        $pdf->SetFont('Arial','B',9);
        $pdf->cell(60, 5, decode_fr("Type Frais") , 1, 0, 'L');
        $pdf->cell(30, 5, decode_fr("Montant prevu") , 1, 0, 'L');
        $pdf->cell(30, 5, decode_fr("montant payer") , 1, 0, 'L');
        $pdf->cell(30, 5, decode_fr("Solde"), 1, 0, 'L');
        $pdf->cell(15, 5, decode_fr("%"), 1, 0, 'L');
        $pdf->Ln(5);

        while ($data = $all->fetch()){
            $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE faculte = ? AND promotion = ? AND type_frais = ?";
            $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
            $sql_2->execute(array($data['faculte'], $data['promotion'], $data['type_frais'])); 

            while($d = $sql_2->fetch()){
                $pdf->SetFont('Arial','I',8);
                $pdf->cell(60, 5, decode_fr($data['type_frais']) , 1, 0, 'L');
                $pdf->cell(30, 5, decode_fr(m_format($data['mt'])) , 1, 0, 'L');
                $pdf->cell(30, 5, decode_fr(m_format($d['mp'])) , 1, 0, 'L');
                $pdf->cell(30, 5, '$ '.decode_fr($data['mt']-$d['mp']) , 1, 0, 'L');
                $pdf->cell(15, 5, montant_restant_pourcent($d['mp'], $data['mt']).'%' , 1, 0, 'L');
                $a[] = $data['mt'];
                $b[] = $d['mp'];
                $c[] = $data['mt']-$d['mp'];
                // $pdf->cell(20, 5, decode_fr("Total") , 1, 0, 'C');
                $pdf->Ln(5);
                // $pdf->Ln(1);
            }
            
        }
        if(count($a) > 0 && count($b) > 0 && count($c) > 0){
            $pdf->SetFont('Arial','IB',8);
            $pdf->cell(60, 5, decode_fr("Total") , 1, 0, 'L');
            $pdf->cell(30, 5, '$ '.decode_fr(array_sum($a)) , 1, 0, 'L');
            $pdf->cell(30, 5, '$ '.decode_fr(array_sum($b)) , 1, 0, 'L');
            $pdf->cell(30, 5, '$ '.decode_fr(array_sum($c)) , 1, 0, 'L');
            $pdf->cell(15, 5, decode_fr(montant_restant_pourcent(array_sum($b), array_sum($a))).'%' , 1, 0, 'L');
            $a = array();
            $b = array();
            $c = array();
            $pdf->Ln(5);
        }else{
            $pdf->cell(60, 5, decode_fr("############ pas de payement ###########") , 0, 0, 'L');
        }
    }

    $pdf->Ln(5);
    $pdf->SetFont('Arial','',10);
	$pdf->cell(300,20, decode_fr('par : '.$_SESSION['data']['noms'].'; le '.date('d/M/Y')),0,1,'C');
    $pdf->output();
?>