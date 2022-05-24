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

    $an =  ConnexionBdd::Connecter()->query("SELECT * FROM annee_acad GROUP BY annee_acad ORDER BY id_annee DESC LIMIT 1");
    if($an->rowCount() > 0){
        $an_r = $an->fetch();
    }else{
        $an_r['id_annee'] = '';
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

    $sql = "SELECT
                transaction_depense.id_transaction,
                transaction_depense.montant,
                transaction_depense.motif,
                transaction_depense.date_motif as date_t,
                poste_depense.poste,
                transaction_depense.num_op
            FROM
                transaction_depense
            LEFT JOIN poste_depense ON transaction_depense.id_poste = poste_depense.id_poste
            WHERE
                transaction_depense.id_annee = 1
            ORDER BY
                transaction_depense.date_motif ASC";
    $req = ConnexionBdd::Connecter()->prepare($sql);
    $req->execute(array($an_r['id_annee']));
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
	$pdf->cell(300,20, decode_fr('par : '.$_SESSION['data']['noms'].'; le '.date('d M Y')),0,1,'C');
    $pdf->output();
?>