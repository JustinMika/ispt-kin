<?php
	session_start();
	require_once '../../../includes/ConnexionBdd.class.php';
	// FPDF
	require '../fpdf/fpdf.php';

    if(isset($_SESSION['data']['noms']) && !empty($_SESSION['data']['noms'])){

    }else{
        header('location:../dec');
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
    $pdf->cell(197,7,ucwords('PAYEMENT PAR TYPE DE FRAIS, FACULTE ET PAR PROMOTION'),0,1,'C');

    //Tableau
    $pdf->SetFont('Arial','B',9);
    $pdf->cell(30, 5,'Faculte',1,0,'C');
    $pdf->cell(20, 5,'Promotion',1,0,'C');
    $pdf->cell(60, 5,'Type de frais',1,0,'C');
    $pdf->cell(30, 5,'Montant prevu',1,0,'C');
    $pdf->cell(30, 5,'Montant payer',1,0,'C');
    $pdf->cell(20, 5,'Solde',1,0,'C');
    $pdf->Ln(5);

    $sql = "SELECT faculte, promotion, type_frais, SUM(montant) as mt FROM affectation_frais GROUP BY faculte, type_frais";
                                                    
    $all = ConnexionBdd::Connecter()->query($sql);
    $pdf->SetFont('Arial','',9);
    while ($data = $all->fetch()){
        $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE faculte = ? AND promotion = ? AND type_frais = ?";
        $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
        $sql_2->execute(array($data['faculte'], $data['promotion'], $data['type_frais'])); 

        while($d = $sql_2->fetch()){
            $pdf->cell(30, 5, decode_fr($data['faculte']) , 1, 0, 'L');
            $pdf->cell(20, 5, decode_fr($data['promotion']) , 1, 0, 'L');
            $pdf->cell(60, 5, decode_fr($data['type_frais']) , 1, 0, 'L');
            $pdf->cell(30, 5, decode_fr(m_format($data['mt'])) , 1, 0, 'L');
            $pdf->cell(30, 5, decode_fr(m_format($d['mp'])) , 1, 0, 'L');
            $pdf->cell(20, 5, '$ '.decode_fr($data['mt']-$d['mp']) , 1, 0, 'L');
            $pdf->Ln(5);
        }
    }

    $pdf->Ln(5);
    $pdf->SetFont('Arial','',10);
	$pdf->cell(300,20, decode_fr('par : '.$_SESSION['data']['noms'].'; le '.date('d/M/Y')),0,1,'C');
    $pdf->output();
?>