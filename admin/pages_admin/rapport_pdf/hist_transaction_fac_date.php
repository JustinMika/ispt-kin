<?php
    header('Content-type:text/html; charset=UTF-8');
	session_start();
	require_once '../../../includes/ConnexionBdd.class.php';
	require '../fpdf/fpdf.php';
    if(array_sum($_SESSION['data']) == 0){
        header('location:../dec.php');
        exit();
    }

	$pdf = new FPDF('L', 'mm', 'A4');
	$pdf->AddPage();
	$pdf->SetFont('Arial','B',12);

	$pdf->cell(300,10,'',0,1,'C');
    $pdf->cell(300,6, decode_fr("UNIVERSITE DE GOMA"),0,1,'C');
    $pdf->SetFont('Arial','',11);
    $pdf->cell(300,6, decode_fr("«UNIGOM»"),0,1,'C');
    $pdf->cell(300,6, decode_fr("BP 204 Goma (RDC)"),0,1,'C');
    $pdf->cell(300,6, decode_fr("BP 2277 Gisenyi (RWANDA)"),0,1,'C');
    $pdf->cell(300,6, decode_fr("E-mail : rectorat@unigom.ac.cd"),0,1,'C');
    $pdf->cell(300,6, decode_fr("site web : www.unigom.ac.cd"),0,1,'C', false, 'www.unigom.ac.cd');

    $pdf->Ln(5);
    $pdf->cell(300,10,'Pax ex scientia splendeat',0,1,'L');
    // logo de la faculte
    $pdf->Image("../../../images/UNIGOM_W260px.jpg", 15,25,30, 30);
    $pdf->cell(280,1 ,"",1,1,'C', true);

    $an =  ConnexionBdd::Connecter()->query("SELECT * FROM annee_academique GROUP BY annee_acad ORDER BY id DESC LIMIT 1");
    if($an->rowCount() > 0){
        $an_r = $an->fetch();
    }else{
        $an_r['annee_acad'] = '';
        die("Veuillez AJouter l annee academique");
    }

    if(isset($_POST['poste_depense']) && !empty($_POST['poste_depense'])){
        if(isset($_POST['date_1']) && !empty($_POST['date_1'])){
            if(isset($_POST['date_2']) && !empty($_POST['date_2'])){
                $data_1 = htmlspecialchars(trim($_POST['date_1']));
                $data_2 = htmlspecialchars(trim($_POST['date_2']));
                $post = $_POST['poste_depense'];
                // die(htmlentities($post));
                // $post = htmlentities(utf8_decode($post));

                if($data_1 <= $data_2){
                    $pdf->Ln(1);
                    $pdf->SetFont('Arial','BU',10);
                    $pdf->cell(280,10,'HISTORIQUE DES TRANSACTIONS DES POSTES DE DEPENSES FACULTAIRES: '.date("d/m/Y",strtotime($data_1)).' au '.date("d/m/Y",strtotime($data_2)),0,1,'C');
                    $pdf->Ln(1);


                    $a = array();

                    if($post == "Tous"){
                        //Tableau
                        $pdf->SetFont('Arial','B',10);
                        $pdf->cell(20, 5,'Date',1,0,'C');
                        // $pdf->cell(30, 5,'Num. OP',1,0,'C');
                        $pdf->cell(70, 5,'Poste',1,0,'C');
                        $pdf->cell(150, 5,'Motif',1,0,'C');
                        $pdf->cell(15, 5,'Montant',1,0,'C');
                        $pdf->Ln(5);
                        $req = ConnexionBdd::Connecter()->prepare("SELECT * FROM depense_facultaire_transact  WHERE faculte = ? AND date_trans BETWEEN ? AND ? ORDER BY id, date_trans DESC");
                        $req->execute(array($_SESSION['data']['access'], $data_1, $data_2));

                        $pdf->SetFont('Arial','',10);
                        while ($res1=$req->fetch()) {
                            $pdf->cell(20, 5, date("d/m/Y", strtotime($res1['date_trans'])), 1, 0, 'L');
                            // $pdf->cell(30, 5, substr(decode_fr($res1['num_op']).'.', 0, 40), 1, 0, 'L');
                            $pdf->cell(70, 5, substr(decode_fr($res1['poste_df']), 0, 40), 1, 0, 'L');
                            $pdf->cell(150, 5, substr(decode_fr($res1['motif']), 0, 105), 1, 0, 'L');
                            $pdf->SetFont('Arial','', 8);
                            $pdf->cell(15, 5, '$'.$res1['montant_trans'], 1, 0, 'L');
                            $pdf->SetFont('Arial','', 10);
                            $pdf->Ln(5);
                            $a[] = $res1['montant_trans'];
                        }
                        $pdf->cell(20, 5, "Total", 1, 0, 'L');
                        // $pdf->cell(30, 5, decode_fr(""), 1, 0, 'L', true);
                        $pdf->cell(70, 5, decode_fr(""), 1, 0, 'L', true);
                        $pdf->cell(150, 5, utf8_decode(""), 1, 0, 'L', true);
                        $pdf->SetFont('Arial','', 8);
                        $pdf->cell(15, 5, '$'.array_sum($a), 1, 0, 'L');
                        $pdf->SetFont('Arial','',10);
                    }else{
                        $pdf->SetFont('Arial','B',10);
                        $pdf->cell(280,10,'POSTE : '.$post,0,1,'C');
                        //Tableau
                        $pdf->SetFont('Arial','B',10);
                        $pdf->cell(20, 5,'Date',1,0,'C');
                        // $pdf->cell(30, 5,'Num. OP',1,0,'C');
                        // $pdf->cell(70, 5,'Poste',1,0,'C');
                        $pdf->cell(150, 5,'Motif',1,0,'C');
                        $pdf->cell(15, 5,'Montant',1,0,'C');
                        $pdf->Ln(5);
                        $req = ConnexionBdd::Connecter()->prepare("SELECT * FROM depense_facultaire_transact  WHERE poste_df = ? AND faculte = ? AND date_trans BETWEEN ? AND ? ORDER BY id, date_trans DESC");
                        $req->execute(array($post, $_SESSION['data']['access'], $data_1, $data_2));

                        $pdf->SetFont('Arial','',10);
                        while ($res1=$req->fetch()) {
                            $pdf->cell(20, 5, date("d/m/Y", strtotime($res1['date_trans'])), 1, 0, 'L');
                            // $pdf->cell(30, 5, substr(decode_fr($res1['num_op']).'.', 0, 40), 1, 0, 'L');
                            // $pdf->cell(70, 5, substr(decode_fr($res1['poste_df']).'.', 0, 40), 1, 0, 'L');
                            $pdf->cell(150, 5, decode_fr($res1['motif']), 1, 0, 'L');
                            $pdf->SetFont('Arial','', 8);
                            $pdf->cell(15, 5, '$'.$res1['montant_trans'], 1, 0, 'L');
                            $pdf->SetFont('Arial','', 10);
                            $pdf->Ln(5);
                            $a[] = $res1['montant_trans'];
                        }
                        $pdf->cell(20, 5, "Total", 1, 0, 'L');
                        // $pdf->cell(30, 5, decode_fr(""), 1, 0, 'L', true);
                        // $pdf->cell(70, 5, decode_fr(""), 1, 0, 'L', true);
                        $pdf->cell(150, 5, utf8_decode(""), 1, 0, 'L', true);
                        $pdf->SetFont('Arial','', 8);
                        $pdf->cell(15, 5, '$'.array_sum($a), 1, 0, 'L');
                        $pdf->SetFont('Arial','',10);
                    }
                }else{
                    echo 'indiference des date: la premiere date doit etre ineferiere ou egal a la deucieme date';
                }
            }
        }
    }

    $pdf->Ln(3);
    $pdf->SetFont('Arial','',10);
	$pdf->cell(300,20, decode_fr('par : '.$_SESSION['data']['noms'].'; le '.date('d/m/Y')),0,1,'C');
	$pdf->cell(300,10, decode_fr('Chef de fac. d\'/de '.$_SESSION['data']['access']),0,1,'C');
    $pdf->output();
?>