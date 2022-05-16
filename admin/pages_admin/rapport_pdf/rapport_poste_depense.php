<?php
	session_start();
	require_once '../../../includes/ConnexionBdd.class.php';
	// FPDF
	require '../fpdf/fpdf.php';

    // print_r($_SESSION);
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

    if(array_sum($_SESSION) > 0){
        if(isset($_SESSION['req_rapport']) && strlen($_SESSION['req_rapport']) > 0 && isset($_SESSION['params'])){
            $req = $_SESSION['req_rapport'];
            $params = $_SESSION['params'];
            $pdf->Ln(1);
            $pdf->cell(197,7,'POSTE DES DEPENSES',0,1,'C');
            $a = array();
            $b = array();
        
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
                $pdf->cell(20, 5, '$ '.$res1[2], 1, 0, 'L');
                $pdf->cell(20, 5, '$ '.$res1[3], 1, 0, 'L');
                $pdf->cell(20, 5, '$ '.montant_restant($res1[2], $res1[3]), 1, 0, 'L');
                $pdf->cell(35, 5, montant_restant_pourcent($res1[3], $res1[2]).'%', 1, 0, 'L');
                $a[] = $res1[2];
                $b[] = $res1[3];
                $pdf->Ln(5);
            }
            $pdf->cell(75, 5,'Total',1,0,'L');
            $pdf->cell(20, 5, '$ '.array_sum($a),1,0,'L');
            $pdf->cell(20, 5, '$ '.array_sum($b),1,0,'L');
            $pdf->cell(20, 5, '$ '.montant_restant(array_sum($a), array_sum($b)),1,0,'L');
            $pdf->cell(35, 5, montant_restant_pourcent(array_sum($b), array_sum($a)).'%',1,0,'L');
        }else{
            // header('location:../dec.php');
        }
        if(isset($_SESSION['req_rapport_trans']) && strlen($_SESSION['req_rapport_trans']) > 0 && isset($_SESSION['params_trans'])){
            $pdf->Ln(1);
            $pdf->SetFont('Arial','BU',8);
            $d1 = date("d/m/Y", strtotime($_SESSION['params_trans'][0]));
            $d2 = date("d/m/Y", strtotime($_SESSION['params_trans'][1]));
            $pdf->cell(197,15,decode_fr('TRANSACTION SUR LES POSTES DES DEPENSES du '.$d1.' au '.$d2),0,1,'C');
            //Tableau
            $pdf->SetFont('Arial','B',8);
            $pdf->cell(60, 5,'Poste de depense',1,0,'C');
            $pdf->cell(20, 5,'Montant',1,0,'C');
            $pdf->Ln(5);
            $a = array();
        
            $req = ConnexionBdd::Connecter()->prepare($_SESSION['req_rapport_trans']);
            $req->execute($_SESSION['params_trans']);
        
            $pdf->SetFont('Arial','',9);
            while ($res1=$req->fetch()) {
                $pdf->cell(60, 5, $res1['poste'], 1, 0, '');
                $pdf->cell(20, 5, '$ '.decode_fr($res1['montant']) , 1, 0, 'L');
                $a[] = $res1['montant'];
                $pdf->Ln(5);
            }
        
            $pdf->SetFont('Arial','BI',8);
            $pdf->cell(60, 5,'Total',1,0,'L');
            $pdf->cell(20, 5,'$ '.array_sum($a),1,0,'L');
            $a = array();
        }else if(isset($_SESSION['req_rapport_trans']) && strlen($_SESSION['req_rapport_trans']) > 0 && isset($_SESSION['params_trans_1'])){
            $pdf->Ln(1);
            $pdf->SetFont('Arial','BU',8);
            $d1 = date("d/m/Y", strtotime($_SESSION['params_trans_1'][0]));
            $d2 = date("d/m/Y", strtotime($_SESSION['params_trans_1'][1]));
            $pdf->cell(197,15,decode_fr('TRANSACTION SUR LES POSTES DES DEPENSES du '.$d1.' au '.$d2),0,1,'C');
            //Tableau
            $pdf->SetFont('Arial','B',8);
            $pdf->cell(60, 5,'Poste de depense',1,0,'C');
            $pdf->cell(20, 5,'Montant',1,0,'C');
            $pdf->Ln(5);
            $a = array();
        
            $req = ConnexionBdd::Connecter()->prepare($_SESSION['req_rapport_trans_1']);
            $req->execute($_SESSION['params_trans_1']);
        
            $pdf->SetFont('Arial','',9);
            while ($res1=$req->fetch()) {
                $pdf->cell(60, 5, $res1['poste'], 1, 0, '');
                $pdf->cell(20, 5, '$ '.decode_fr($res1['montant']) , 1, 0, 'L');
                $a[] = $res1['montant'];
                $pdf->Ln(5);
            }
        
            $pdf->SetFont('Arial','BI',8);
            $pdf->cell(60, 5,'Total',1,0,'L');
            $pdf->cell(20, 5,'$ '.array_sum($a),1,0,'L');
            $a = array();
        }else{

        }
    }else{
        // die("session n existe pas ");
        header('location:../dec.php');
    }


    $pdf->Ln(10);
    $pdf->SetFont('Arial','',10);
	$pdf->cell(300,20, decode_fr('par : '.$_SESSION['data']['noms'].'; le '.date('d/M/Y')),0,1,'C');
    $pdf->output();
?>