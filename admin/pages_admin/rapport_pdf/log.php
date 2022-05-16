<?php
	session_start();
    header('Content-type:text/html; charset=UTF-8');
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
    $pdf->SetFont('Arial','BU',12);
    $pdf->cell(197,7,'JOURNAL D\'ACTIVITE DES UTILISATEURS',0,1,'C');
    $pdf->Ln(1);

    //Tableau
    $pdf->SetFont('Arial','B',10);
    $pdf->cell(35, 5,'Noms',1,0,'C');
    $pdf->cell(125, 5,'Actions',1,0,'C');
    $pdf->cell(25, 5,'Date et Heure',1,0,'C');
    $pdf->Ln(5);

    $req = ConnexionBdd::Connecter()->query("SELECT * FROM log_admin_user ORDER BY date_action ASC");

    $pdf->SetFont('Arial','',7);
    while ($res1=$req->fetch()) {
        $pdf->SetFont('Arial','',6);
        $pdf->cell(35, 5, decode_fr($res1[1]), 1, 0, 'L');
        $pdf->SetFont('Arial','',6);
        $pdf->cell(125, 5, utf8_decode(decode_fr($res1[3])), 1, 0, 'L');
        $pdf->cell(25, 5, utf8_decode(date("d/m/Y à H:m:s", strtotime(decode_fr($res1[2])))), 1, 0, 'R');
        $pdf->Ln(5);
    }
    $pdf->Ln(3);
    $pdf->SetFont('Arial','',10);
	$pdf->cell(300,20, decode_fr('par : '.$_SESSION['data']['noms'].'; le '.date('d/M/Y')),0,1,'C');
    $pdf->output();
?>