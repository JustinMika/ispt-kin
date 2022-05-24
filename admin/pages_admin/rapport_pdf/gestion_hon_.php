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

	$pdf = new FPDF('L', 'mm', 'A4');
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
    $pdf->Image("../../../images/ispt_kin.png", 10,15,25, 25);
    $pdf->Ln(2);
    $pdf->cell(280,1 ,"",1,1,'C', true);

    $pdf->Ln(1);
    $pdf->SetFont('Arial','BU',12);
    $pdf->cell(290,7,'GESTION DES HONORAIRES',0,1,'C');
    $pdf->Ln(2);

    //Tableau
    $pdf->SetFont('Arial','B',10);
    $pdf->cell(60, 5,'Enseignants',1,0,'C');
    $pdf->cell(15, 5,'Grade',1,0,'C');
    $pdf->cell(70, 5,'Cours',1,0,'C');
    $pdf->cell(50, 5,'Section',1,0,'C');
    $pdf->cell(18, 5,'V. Horaire',1,0,'C');
    $pdf->cell(15, 5,'Taux',1,0,'C');
    $pdf->cell(15, 5,'Total',1,0,'C');
    $pdf->cell(20, 5, decode_fr('Total payé'),1,0,'C');
    $pdf->cell(15, 5,'Solde',1,0,'C');
    $pdf->Ln(5);

    $sql = "SELECT
                gest_honoraire.id,
                gest_honoraire.noms_ens,
                gest_honoraire.grade_ens,
                gest_honoraire.cours,
                gest_honoraire.heure_th,
                gest_honoraire.montant_ht,
                gest_honoraire.heure_pr,
                gest_honoraire.montant_hp,
                gest_honoraire.taux,
                gest_honoraire.total,
                gest_honoraire.total_payer,
                gest_honoraire.type_enseig,
                gest_honoraire.prestation,
                annee_acad.annee_acad,
                sections.section as faculte
            FROM
                gest_honoraire
            LEFT JOIN annee_acad ON gest_honoraire.id_annee = annee_acad.id_annee
            LEFT JOIN sections ON gest_honoraire.id_section = sections.id_section";
    $req = ConnexionBdd::Connecter()->query($sql);

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
    }

    $pdf->Ln(3);
    $pdf->SetFont('Arial','',10);
	$pdf->cell(300,20, decode_fr('par : '.$_SESSION['data']['noms'].'; le '.date('d/M/Y')),0,1,'C');
    $pdf->output();
?>