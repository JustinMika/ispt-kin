<?php
	session_start();
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

	$pdf->cell(290,10,'',0,1,'C');
    $pdf->cell(290,6, decode_fr("UNIVERSITE DE GOMA"),0,1,'C');
    $pdf->SetFont('Arial','',11);
    $pdf->cell(290,6, decode_fr("«UNIGOM»"),0,1,'C');
    $pdf->cell(290,6, decode_fr("BP 204 Goma (RDC)"),0,1,'C');
    $pdf->cell(290,6, decode_fr("BP 2277 Gisenyi (RWANDA)"),0,1,'C');
    $pdf->cell(290,6, decode_fr("E-mail : rectorat@unigom.ac.cd"),0,1,'C');
    $pdf->cell(290,6, decode_fr("site web : www.unigom.ac.cd"),0,1,'C', false, 'www.unigom.ac.cd');

    $pdf->Ln(5);
    $pdf->cell(290,10,'Pax ex scientia splendeat',0,1,'L');
    // logo de la faculte
    $pdf->Image("../../../images/UNIGOM_W260px.jpg", 15,25,30, 30);
    $pdf->cell(290,1 ,"",1,1,'C', true);

    $an =  ConnexionBdd::Connecter()->query("SELECT * FROM annee_academique GROUP BY annee_acad ORDER BY id DESC LIMIT 1");
    if($an->rowCount() > 0){
        $an_r = $an->fetch();
    }else{
        $an_r['annee_acad'] = '';
        die("Veuillez AJouter l annee academique");
    }

    $pdf->Ln(1);
    $pdf->SetFont('Arial','BU',12);
    $pdf->cell(290,7,'HISTORIQUE DES TRANSACTIONS DES POSTES DE DEPENSES : '.$an_r['annee_acad'],0,1,'C');
    $pdf->Ln(1);

    //Tableau
    $pdf->SetFont('Arial','B',10);
    $pdf->cell(20, 5,'Date',1,0,'C');
    $pdf->cell(30, 5,'Num. OP',1,0,'C');
    $pdf->cell(70, 5,'Poste',1,0,'C');
    $pdf->cell(119, 5,'motif',1,0,'C');
    $pdf->cell(35, 5,'Montant',1,0,'C');
    $pdf->Ln(5);

    $req = ConnexionBdd::Connecter()->prepare("SELECT * FROM transaction_depense  WHERE  annee_acad = ? ORDER BY date_t ASC");
    $req->execute(array($an_r['annee_acad']));
    $a = array();
    $pdf->SetFont('Arial','',10);
    while ($res1=$req->fetch()) {
        $pdf->SetFont('Arial','',8);
        $pdf->cell(20, 5, utf8_decode(date("d/m/Y", strtotime(decode_fr($res1['date_t'])))), 1, 0, 'R');
        $pdf->cell(30, 5, substr(decode_fr($res1['num_op']), 0, 50), 1, 0, 'L');
        $pdf->cell(70, 5, substr(decode_fr($res1['poste']), 0, 50), 1, 0, 'L');
        $pdf->cell(119, 5, substr(decode_fr($res1['motif']), 0, 110), 1, 0, 'L');
        $pdf->SetFont('Arial','',8);
        $pdf->cell(35, 5, '$'.utf8_decode(decode_fr($res1['montant'])), 1, 0, 'L');
        $pdf->SetFont('Arial','',10);
        $pdf->Ln(5);
        $a[] = $res1['montant'];
    }

    $pdf->cell(20, 5, "Total", 1, 0, 'L');
    $pdf->cell(30, 5, "Total", 1, 0, 'L');
    $pdf->cell(70, 5, decode_fr(""), 1, 0, 'L', true);
    $pdf->cell(119, 5, utf8_decode(decode_fr("")), 1, 0, 'L', true);
    $pdf->cell(35, 5, '$'.array_sum($a), 1, 0, 'L');

    $pdf->Ln(3);
    $pdf->SetFont('Arial','',10);
	$pdf->cell(300,20, decode_fr('par : '.$_SESSION['data']['noms'].'; le '.date('d/M/Y')),0,1,'C');
    $pdf->output();
?>