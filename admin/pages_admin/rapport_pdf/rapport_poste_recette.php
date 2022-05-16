<?php
	session_start();
	require_once '../../../includes/ConnexionBdd.class.php';
	// FPDF
	require '../fpdf/fpdf.php';
    if(array_sum($_SESSION) == 0){
        header('location:../dec.php');
        exit();
    }

	$pdf = new FPDF('P', 'mm', 'A4');
	$pdf->AddPage();
	$pdf->SetFont('Arial','B',12);

	$$pdf->cell(150,10,'',0,1,'C');
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

    $an =  ConnexionBdd::Connecter()->query("SELECT * FROM annee_academique GROUP BY annee_acad ORDER BY id DESC LIMIT 1");
    if($an->rowCount() > 0){
        $an_r = $an->fetch();
    }else{
        $an_r['annee_acad'] = '';
        die("Veuillez AJouter l annee academique");
    }
    $pdf->Ln(1);
    $pdf->SetFont('Arial','BU',11);
    $pdf->cell(197,7,'POSTES DES RECETTES UNIVERSITAIRES : '.$an_r['annee_acad'],0,1,'C');
    $pdf->Ln(1);

    //Tableau
    $pdf->SetFont('Arial','B',10);
    $pdf->cell(10, 5,'#ID',1,0,'C');
    $pdf->cell(120, 5,'Poste de poste recette',1,0,'C');
    $pdf->cell(30, 5,'Montant',1,0,'L');
    // $pdf->cell(40, 5,'Annee Academique',1,0,'C');
    $pdf->Ln(5);

    $req = ConnexionBdd::Connecter()->prepare("SELECT * FROM previson_frais_univ WHERE annee_acad =? ORDER BY poste");
    $req->execute(array($an_r['annee_acad']));

    $pdf->SetFont('Arial','',9);
    while ($res1=$req->fetch()) {
        $pdf->cell(10, 5, $res1['id'], 1, 0, 'C');
        $pdf->cell(120, 5, decode_fr($res1['poste']), 1, 0, 'L');
        $pdf->cell(30, 5, decode_fr($res1['montant'].'$'), 1, 0, 'L');
        // $pdf->cell(40, 5, $res1['annee_acad'], 1, 0, 'L');
        $pdf->Ln(5);
    }

    $pdf->Ln(5);
    $pdf->SetFont('Arial','',10);
	$pdf->cell(300,20, decode_fr('par : '.$_SESSION['data']['noms'].'; le '.date('d/M/Y')),0,1,'C');
    $pdf->output();
?>