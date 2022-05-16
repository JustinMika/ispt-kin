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
    // print_r($_SESSION['p_etud_t']);
    if(isset($_SESSION['p_etud']) && strlen($_SESSION['p_etud']) > 0 && count($_SESSION['p_etud_t']) > 1){
        // recherche par etudiants
        $pdf->cell(197,7,'PAYEMENT DES ETUDIANTS',0,1,'C');
        //Tableau
        $pdf->SetFont('Arial','B',9);
        $pdf->cell(30, 5,'Matricule',1,0,'C');
        $pdf->cell(55, 5,'Faculte',1,0,'C');
        $pdf->cell(20, 5,'Promotion',1,0,'C');
        $pdf->cell(50, 5,'Type frais',1,0,'C');
        $pdf->cell(15, 5,'Montant',1,0,'C');
        $pdf->cell(20, 5,'Date',1,0,'C');
        $pdf->Ln(5);

        $pdf->SetFont('Arial','',7);
        $req = ConnexionBdd::Connecter()->prepare($_SESSION['p_etud']);
        $req->execute($_SESSION['p_etud_t']);
        while($res1=$req->fetch()) {
            $pdf->cell(30, 5, decode_fr($res1['matricule']), 1, 0, 'C');
            $pdf->cell(55, 5, decode_fr($res1['faculte']), 1, 0, 'L');
            $pdf->cell(20, 5, decode_fr($res1['promotion']), 1, 0, 'L');
            $pdf->cell(50, 5, decode_fr($res1['type_frais']), 1, 0, 'L');
            $pdf->cell(15, 5, decode_fr($res1['montant'].'$'), 1, 0, 'L');
            $pdf->cell(20, 5, decode_fr($res1['date_payement']), 1, 0, 'L');
            $pdf->Ln(5);
        }
    }
    else if(isset($_SESSION['p_etud']) && strlen($_SESSION['p_etud']) > 0 && isset($_SESSION['params'])){

    }else{
        // $pdf->cell(197,7,'ETUDIANTS AYANT LES DETTES ENVERS L\'UNIVERSITE',0,1,'C');

        // $pdf->SetFont('Arial','B',9);
        // $pdf->cell(30, 5,'Faculte',1,0,'C');
        // $pdf->cell(55, 5,'Montant',1,0,'C');
        // $pdf->cell(20, 5,'Montant total',1,0,'C');
        // $pdf->cell(50, 5,'Type frais',1,0,'C');
        // $pdf->cell(15, 5,'Montant',1,0,'C');
        // $pdf->cell(20, 5,'%',1,0,'C');
        // $pdf->Ln(5);

        // $frais_fac = ConnexionBdd::Connecter()->query("SELECT ifaculte,type_frais, SUM(montant) AS montant FROM payement ORDER BY montant ASC");

        // while($data = $frais_fac->fetch()){
        //     $pdf->cell(30, 5, decode_fr($data['faculte']), 1, 0, 'C');
        //     $pdf->cell(55, 5, decode_fr($data['montant']), 1, 0, 'L');
        //     $pdf->cell(20, 5, decode_fr($data['montant']), 1, 0, 'L');
        //     $pdf->cell(50, 5, decode_fr($data['type_frais']), 1, 0, 'L');
        //     $pdf->cell(15, 5, decode_fr($data['montant'].'$'), 1, 0, 'L');
        //     $pdf->cell(20, 5, decode_fr(), 1, 0, 'L');
        //     $pdf->Ln(5);
        // }
    }
    

    //Tableau

    $pdf->Ln(5);
    $pdf->SetFont('Arial','',10);
	$pdf->cell(300,20, decode_fr('par : '.$_SESSION['data']['noms'].'; le '.date('d/M/Y')),0,1,'C');
    $pdf->output();
?>