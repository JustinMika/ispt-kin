<?php
	session_start();
	require_once '../../../includes/ConnexionBdd.class.php';
    require_once '../../../includes/verification.class.php';
	// FPDF
	require '../fpdf/fpdf.php';

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

    if(array_sum($_SESSION) > 0){
        if(isset($_SESSION['req_rapport']) && strlen($_SESSION['req_rapport']) > 0 && isset($_SESSION['params'])){
            $req = $_SESSION['req_rapport'];
            $params = $_SESSION['params'];
            $pdf->Ln(1);
            $pdf->cell(197,7,'POSTE DES DEPENSES FACULTAIRE : FACULTE D\'(E) '.VerificationUser::verif($rr['access']),0,1,'C');
        
            //Tableau
            $pdf->SetFont('Arial','B',9);
            $pdf->cell(75, 5,'Poste',1,0,'C');
            $pdf->cell(20, 5,'Prevision',1,0,'C');
            $pdf->cell(20, 5,'Depense',1,0,'C');
            $pdf->cell(20, 5,'Solde',1,0,'C');
            $pdf->cell(35, 5, decode_fr('Niveau d\'exécution'),1,0,'C');
            $pdf->Ln(5);
        
            $req = ConnexionBdd::Connecter()->prepare($req);
            $req->execute($params);
        
            $pdf->SetFont('Arial','',8);
            while ($res1=$req->fetch()) {
                $pdf->cell(75, 5, decode_fr($res1[1]) , 1, 0, 'L');
                $pdf->cell(20, 5, $res1[2].'$', 1, 0, 'L');
                $pdf->cell(20, 5, $res1[3].'$', 1, 0, 'L');
                $pdf->cell(20, 5, montant_restant($res1[2], $res1[3]).'$', 1, 0, 'L');
                $pdf->cell(35, 5, montant_restant_pourcent($res1[3], $res1[2]).'%', 1, 0, 'L');
                $pdf->Ln(5);
            }
        }else{
            // header('location:../dec.php');
        }
        if(isset($_SESSION['req_rapport_trans']) && strlen($_SESSION['req_rapport_trans']) > 0 && isset($_SESSION['params_trans'])){
            $pdf->Ln(1);
            $pdf->cell(197,7,'TRANSACTION SUR LES POSTES DES DEPENSES',0,1,'C');
            //Tableau
            $pdf->SetFont('Arial','B',8);
            $pdf->cell(50, 5,'Poste',1,0,'C');
            $pdf->cell(20, 5,'Montant',1,0,'C');
            $pdf->cell(20, 5,'Date',1,0,'C');
            $pdf->cell(100, 5,'Motif',1,0,'C');
            $pdf->Ln(5);
        
            $req = ConnexionBdd::Connecter()->prepare($_SESSION['req_rapport_trans']);
            $req->execute($_SESSION['params_trans']);
        
            $pdf->SetFont('Arial','',9);
            while ($res1=$req->fetch()) {
                $pdf->cell(50, 5, $res1['poste'], 1, 0, '');
                $pdf->cell(20, 5, decode_fr($res1['montant']) , 1, 0, 'L');
                $pdf->cell(20, 5, decode_fr($res1['date_t']) , 1, 0, 'L');
                $pdf->cell(100, 5, decode_fr($res1['motif']) , 1, 0, 'L');
                $pdf->Ln(5);
            }
        }else{
            // header('location:../dec.php');
        }
    }else{
        // die("session n existe pas ");
        header('location:../dec.php');
    }


    $pdf->Ln(5);
    $pdf->SetFont('Arial','',10);
	$pdf->cell(300,20, decode_fr('par : '.$_SESSION['data']['noms'].'; le '.date('d/M/Y')),0,1,'C');
    $pdf->output();
?>