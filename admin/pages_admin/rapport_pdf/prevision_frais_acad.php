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
    $pdf->SetFont('Arial','BU',12);

    // on recupere le dernier annee academique
    $an =  ConnexionBdd::Connecter()->query("SELECT * FROM annee_acad GROUP BY id_annee ORDER BY id_annee DESC LIMIT 1");
    if($an->rowCount() > 0){
        $an_r = $an->fetch();
    }else{
        $an_r['id_annee'] = '';
        die("Veuillez AJouter l annee academique");
    }

    $pdf->cell(197,7,'PREVISON DES FRAIS '.$an_r['annee_acad'],0,1,'C');
    $pdf->Ln(1);

    //Tableau
    $pdf->SetFont('Arial','B', 9);
    $pdf->cell(70, 5,'Type de frais',1,0,'C');
    $pdf->cell(20, 5,'Montant',1,0,'C');
    $pdf->cell(70, 5,'Option',1,0,'C');
    $pdf->cell(20, 5,'Promotion',1,0,'C');
    // $pdf->cell(30, 5,'Annee Academique',1,0,'C');
    $pdf->Ln(5);

    $an =  ConnexionBdd::Connecter()->query("SELECT * FROM annee_acad GROUP BY id_annee ORDER BY id_annee DESC LIMIT 1");
    if($an->rowCount() > 0){
        $an_r = $an->fetch();
    }else{
        $an_r['annee_acad'] = '';
        die("Veuillez AJouter l annee academique");
    }

    $sql = "SELECT
                prevision_frais.type_frais,
                prevision_frais.montant,
                sections.section,
                departement.departement,
                options.option_ as faculte,
                options.promotion
            FROM
                prevision_frais
            LEFT JOIN sections ON prevision_frais.id_section = sections.id_section
            LEFT JOIN departement ON departement.id_departement
            LEFT JOIN options ON prevision_frais.id_option = options.id_option
            WHERE
                prevision_frais.id_annee = ?";
    $req = ConnexionBdd::Connecter()->prepare($sql);
    $req->execute(array($an_r['id_annee']));

    $pdf->SetFont('Arial','',7);
    while ($res1=$req->fetch()) {
        $pdf->cell(70, 5, decode_fr(utf8_decode(ucfirst($res1['type_frais']))), 1, 0, 'L');
        $pdf->cell(20, 5, decode_fr($res1['montant'].'$'), 1, 0, 'L');
        $pdf->cell(70, 5, decode_fr(utf8_decode($res1['faculte'])), 1, 0, 'L');
        $pdf->cell(20, 5, decode_fr($res1['promotion']), 1, 0, 'L');
        // $pdf->cell(30, 5, decode_fr($res1['annee_acad']), 1, 0, 'L');
        $pdf->Ln(5);
    }

    $pdf->Ln(5);
    $pdf->SetFont('Arial','',10);
	$pdf->cell(300,20, decode_fr('par : '.$_SESSION['data']['noms'].'; le '.date('d/M/Y')),0,1,'C');
    $pdf->output();
?>