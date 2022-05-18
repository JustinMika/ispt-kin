<?php
    session_start();
    require_once '../../../includes/ConnexionBdd.class.php';
    // print_r($_POST);
    // FPDF
	require '../fpdf/fpdf.php';

    if(array_sum($_SESSION) == 0 && empty($_SESSION['data'])){
        header('location:../dec.php');
        exit();
    }

    function mm($v){
        if(empty($v)){
            return '0';
        }else{
            return $v;
        }
    }

    function entete($pdf){
        $pdf->SetFont('Arial','B',8);
        $pdf->cell(20, 5, "Matricule" , 1, 0, 'L');
        $pdf->cell(90, 5, "Noms", 1, 0, 'L');
        $pdf->cell(25, 5, decode_fr("montant à payer"), 1, 0, 'L');
        $pdf->cell(25, 5, "montant payer", 1, 0, 'L');
        $pdf->cell(22, 5, "Solde", 1, 0, 'L');
        $pdf->cell(10, 5, "%", 1, 0, 'L');
        $pdf->Ln(5);
    }

    function all($pdf){
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
        $pdf->Ln(2);
    }

    function verify($var){
        if(isset($var) && !empty($var)){
            return $var;
        }else{
            return ' - ';
        }

    }

	$pdf = new FPDF('P', 'mm', 'A4');
    all($pdf);
    $pdf->SetFont('Arial','',8);
    $pdf->SetTextColor(0,0,0);
    $pdf->cell(60, 5, 'Annee Academique Debut : '.decode_fr(verify($_POST['annee_acad_deb'])), 0, 1, 'L');
    $pdf->cell(60, 5, 'Annee Academique  Fin: '.decode_fr(verify($_POST['annee_acad_fin'])), 0, 1, 'L');
    $pdf->cell(60, 5, 'Promotion                 : '.decode_fr(verify($_POST['promotion_etud'])), 0, 1, 'L');
    $pdf->cell(60, 5, 'Faculte                      : '.decode_fr(verify($_POST['fac_etudiant'])), 0, 1, 'L');
    $pdf->cell(60, 5, 'Type de frais             : '.decode_fr(verify($_POST['type_frais'])), 0, 1, 'L');
    $pdf->cell(60, 5, decode_fr('Date de coupure   : Du '.date("d F Y", strtotime(strtolower($_POST['date_debit']))).' au '.date("d F Y", strtotime(strtolower($_POST['date_fin'])))), 0, 1, 'L');
    $pdf->cell(60, 5, decode_fr('Poucentage min.       : '.verify($_POST['pourcent_debut']).'%'), 0, 1, 'L');
    $pdf->cell(60, 5, decode_fr('Poucentage max.      : '.verify($_POST['pourcent_fin']).'%'), 0, 1, 'L');
    $pdf->Ln(3);

    $pdf->Output();
?>