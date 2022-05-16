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

	$pdf->cell(150,10,'',0,1,'C');
    $pdf->cell(270,6, decode_fr("UNIVERSITE DE GOMA"),0,1,'C');
    $pdf->SetFont('Arial','',11);
    $pdf->cell(270,6, decode_fr("«UNIGOM»"),0,1,'C');
    $pdf->cell(270,6, decode_fr("BP 204 Goma (RDC)"),0,1,'C');
    $pdf->cell(270,6, decode_fr("BP 2277 Gisenyi (RWANDA)"),0,1,'C');
    $pdf->cell(270,6, decode_fr("E-mail : rectorat@unigom.ac.cd"),0,1,'C');
    $pdf->cell(270,6, decode_fr("site web : www.unigom.ac.cd"),0,1,'C', false, 'www.unigom.ac.cd');

    $pdf->Ln(5);
    $pdf->cell(150,10,'Pax ex scientia splendeat',0,1,'L');
    // logo de la faculte
    $pdf->Image("../../../images/UNIGOM_W260px.jpg", 15,25,30, 30);
    $pdf->cell(270,1 ,"",1,1,'C', true);

    $an =  ConnexionBdd::Connecter()->query("SELECT * FROM annee_academique GROUP BY annee_acad ORDER BY id DESC LIMIT 1");
    if($an->rowCount() > 0){
        $an_r = $an->fetch();
    }else{
        $an_r['annee_acad'] = ''; 
        die("Veuillez AJouter l annee academique");
    }

    $pdf->Ln(1);
    $pdf->SetFont('Arial','BU',12);
    $pdf->cell(270,10,'HISTORIQUE DES TRANSACTIONS DES POSTES DE DEPENSES : '.$an_r['annee_acad'].' par mois',0,1,'C');
    $pdf->Ln(1);

    //Tableau
    $pdf->SetFont('Arial','B',10);

    $mois = array("Janv.", "Fev.", "Mars", "Avr.","Mai", "Juin", "Juil.", "Aout", "Sept.","Oct.", "Nov.", "Dec.");

    function nm($v){
        if(!empty($v)){
            return $v;
        }else{
            return '$';
        }
    }
    $pdf->SetFont('Arial','B',8);

    $n_mois = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);
    $pdf->cell(60, 5, "Poste", 1, 0, 'L');
    
    foreach($mois as $m){
        $pdf->cell(15, 5, $m, 1, 0, 'L');
    }
    $pdf->cell(15, 5, "Tot.", 1, 0, 'L');
    $pdf->Ln(5);
    $pdf->SetFont('Arial','',8);

    // liste de frais enregistrer dans la base de donnees
    $list_f = array();
    $annee_acad_deb = $an_r['annee_acad'];
    $f = ConnexionBdd::Connecter()->prepare("SELECT DISTINCT poste FROM `poste_depense` WHERE annee_acad = ?");
    $f->execute(array($annee_acad_deb));
    while($d = $f->fetch()){
        $list_f[] = $d['poste'];
    }

    // tableau
    $tot = array();
    $tot_2 = array();

    foreach($list_f as $frais){
        // $pdf->cell(60, 5, decode_fr($frais), 1, 0, 'L');
        $pdf->Cell(60, 5, substr(decode_fr($frais), 0, 50), 1, 'L');
        foreach($n_mois as $n){
            $year = $annee_acad_deb[5].''.$annee_acad_deb[6].''.$annee_acad_deb[7].''.$annee_acad_deb[8];
            
            $sel_m = ConnexionBdd::Connecter()->prepare("SELECT SUM(montant) as montant FROM `transaction_depense` WHERE MONTH(date_t) = ? AND YEAR(date_t) = ? AND annee_acad = ? AND poste = ? GROUP BY poste");
            $sel_m->execute(array($n, $year, $annee_acad_deb, $frais));

            if($sel_m->rowCount() >= 1){
                while($data = $sel_m->fetch()){
                    $pdf->SetFont('Arial','',8);
                    $pdf->Cell(15, 5, number_format($data['montant'], 1, '.', ''), 1,0,'L');
                    
                    $pdf->SetFont('Arial','',8);
                    $tot[] = $data['montant'];
                }
            }else{
                $pdf->Cell(15, 5, '0', 1, 0, 'L');
                $tot[] = 0;
            }
        }
        $pdf->Cell(15, 5, number_format(array_sum($tot), 1, '.', ''), 1,0,'L');
        $tot_2[] = array_sum($tot);
        $tot = array();
        $pdf->Ln(5);
    }
    $pdf->cell(60, 5, "Total", 1, 0, 'L');
    foreach($n_mois as $m){
        $year = $annee_acad_deb[5].''.$annee_acad_deb[6].''.$annee_acad_deb[7].''.$annee_acad_deb[8];
        $sel_m = ConnexionBdd::Connecter()->prepare("SELECT SUM(montant) as montant FROM `transaction_depense`
         WHERE MONTH(date_t) = ? AND YEAR(date_t) = ?");
        $sel_m->execute(array($m, $year));

        if($sel_m->rowCount() > 0){
            $d = $sel_m->fetch();
            $pdf->cell(15, 5, number_format($d['montant'], 1, '.', ''), 1, 0, 'L');
        }else{
            $pdf->cell(15, 5, '0', 1, 0, 'L');
        }   
    }
    $pdf->cell(15, 5, number_format(array_sum($tot_2), 1, '.', ''), 1, 0, 'L');

    $pdf->Ln(3);
    $pdf->SetFont('Arial','',10);
	$pdf->cell(300,20, decode_fr('par : '.$_SESSION['data']['noms'].'; le '.date('d/M/Y')),0,1,'C');
    $pdf->output();
?>