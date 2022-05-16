<?php
	session_start();
	require_once '../../../includes/ConnexionBdd.class.php';
    require_once '../../../includes/verification.class.php';

    if(array_sum($_SESSION) == 0){
        header('location:../dec.php');
        exit();
    }
	// FPDF
	require '../fpdf/fpdf.php';

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
    $pdf->SetFont('Arial','BU',12);

    // on recupere le dernier annee academique
    $an =  ConnexionBdd::Connecter()->query("SELECT * FROM annee_academique GROUP BY annee_acad ORDER BY id DESC LIMIT 1");
    if($an->rowCount() > 0){
        $an_r = $an->fetch();
    }else{
        $an_r['annee_acad'] = '';
        die("Veuillez AJouter l annee academique");
    }

    $pdf->cell(197,7,'PREVISON DES FRAIS '.$an_r['annee_acad'],0,1,'C');
    $pdf->Ln(1);

    //Tableau
    $pdf->SetFont('Arial','B', 9);
    $pdf->cell(70, 5,'Type de frais',1,0,'C');
    $pdf->cell(20, 5,'Montant',1,0,'C');
    $pdf->cell(70, 5,'Faculte',1,0,'C');
    $pdf->cell(20, 5,'Promotion',1,0,'C');
    // $pdf->cell(30, 5,'Annee Academique',1,0,'C');
    $pdf->Ln(5);

    $an =  ConnexionBdd::Connecter()->query("SELECT * FROM annee_academique GROUP BY annee_acad ORDER BY id DESC LIMIT 1");
    if($an->rowCount() > 0){
        $an_r = $an->fetch();
    }else{
        $an_r['annee_acad'] = '';
        die("Veuillez AJouter l annee academique");
    }

    $req = ConnexionBdd::Connecter()->prepare("SELECT * FROM prevision_frais WHERE annee_acad = ? ORDER BY type_frais, faculte, promotion ASC");
    $req->execute(array($an_r['annee_acad']));

    $pdf->SetFont('Arial','',7);
    while ($res1=$req->fetch()) {
        $pdf->cell(70, 5, utf8_decode(decode_fr($res1['type_frais'])), 1, 0, 'L');
        $pdf->cell(20, 5, decode_fr($res1['montant'].'$'), 1, 0, 'L');
        $pdf->cell(70, 5, decode_fr($res1['faculte']), 1, 0, 'L');
        $pdf->cell(20, 5, decode_fr($res1['promotion']), 1, 0, 'L');
        // $pdf->cell(30, 5, decode_fr($res1['annee_acad']), 1, 0, 'L');
        $pdf->Ln(5);
    }

    $pdf->Ln(5);
    $pdf->SetFont('Arial','',10);
	$pdf->cell(300,20, decode_fr('par : '.$_SESSION['data']['noms'].'; le '.date('d/M/Y')),0,1,'C');
    $pdf->output();
?>