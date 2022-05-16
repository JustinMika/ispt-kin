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

    $pdf->cell(290,6, decode_fr(strtoupper("institut superieur pedagogique et technique de kinshasa")),0,1,'C');
    $pdf->SetFont('Arial','',11); //Mail : info@isptkin.ac.cd
    $pdf->cell(290,6, decode_fr("ISPT-KIN"),0,1,'C');
    $pdf->cell(290,6, decode_fr("E-mail : info@isptkin.ac.cd"),0,1,'C');
    $pdf->cell(290,6, decode_fr("site web : www.isptkin.ac.cd"),0,1,'C', false, 'www.isptkin.ac.cd');

    $pdf->Ln(5);
    // logo de la faculte
    $pdf->Image("../../../images/ispt_kin.png", 10,15,25, 25);
    $pdf->Ln(2);
    $pdf->cell(260,1 ,"",1,1,'C', true);

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
                    $a = array();

                    if($post == "Tous"){
                        $pdf->Ln(1);
                        $pdf->SetFont('Arial','BU',10);
                        $pdf->cell(197,10,'HISTORIQUE DES TRANSACTIONS DES POSTES DE DEPENSES : '.date("d/m/Y",strtotime($data_1)).' au '.date("d/m/Y",strtotime($data_2)),0,1,'C');
                        $pdf->cell(197,10,'poste : Tous',0,1,'C');
                        $pdf->Ln(1);

                        //Tableau
                        $pdf->SetFont('Arial','B',10);
                        $pdf->cell(20, 5,'Date',1,0,'C');
                        $pdf->cell(30, 5,'Num. OP',1,0,'C');
                        $pdf->cell(70, 5,'Poste',1,0,'C');
                        $pdf->cell(150, 5,'Motif',1,0,'C');
                        $pdf->cell(15, 5,'Montant',1,0,'C');
                        $pdf->Ln(5);
                        $req = ConnexionBdd::Connecter()->prepare("SELECT * FROM transaction_depense  WHERE date_t BETWEEN ? AND ? ORDER BY date_t ASC");
                        $req->execute(array($data_1, $data_2));

                        $pdf->SetFont('Arial','',10);
                        while ($res1=$req->fetch()) {
                            $pdf->cell(20, 5, date("d/m/Y", strtotime($res1['date_t'])), 1, 0, 'L');
                            $pdf->cell(30, 5, decode_fr($res1['num_op']), 1, 0, 'L');
                            $pdf->cell(70, 5, decode_fr($res1['poste']), 1, 0, 'L');
                            $pdf->cell(150, 5, decode_fr($res1['motif']), 1, 0, 'L');
                            $pdf->SetFont('Arial','', 8);
                            $pdf->cell(15, 5, '$'.$res1['montant'], 1, 0, 'L');
                            $pdf->SetFont('Arial','', 10);
                            $pdf->Ln(5);
                            $a[] = $res1['montant'];
                        }
                        $pdf->cell(20, 5, "Total", 1, 0, 'L');
                        $pdf->cell(30, 5, decode_fr(""), 1, 0, 'L', true);
                        $pdf->cell(70, 5, decode_fr(""), 1, 0, 'L', true);
                        $pdf->cell(150, 5, utf8_decode(""), 1, 0, 'L', true);
                        $pdf->SetFont('Arial','', 8);
                        $pdf->cell(15, 5, '$'.array_sum($a), 1, 0, 'L');
                        $pdf->SetFont('Arial','',10);
                    }else{
                        $pdf->Ln(1);
                        $pdf->SetFont('Arial','BU',10);
                        $pdf->cell(197,10,'HISTORIQUE DES TRANSACTIONS DES POSTES DE DEPENSES : '.date("d/m/Y",strtotime($data_1)).' au '.date("d/m/Y",strtotime($data_2)),0,1,'C');
                        $pdf->cell(197,10, decode_fr('Poste : '.$post),0,1,'C');
                        $pdf->Ln(1);

                        //Tableau
                        $pdf->SetFont('Arial','B',10);
                        $pdf->cell(20, 5,'Date',1,0,'C');
                        $pdf->cell(30, 5,'Num. OP',1,0,'C');
                        $pdf->cell(150, 5,'Motif',1,0,'C');
                        $pdf->cell(15, 5,'Montant',1,0,'C');
                        $pdf->Ln(5);
                        $req = ConnexionBdd::Connecter()->prepare("SELECT * FROM transaction_depense  WHERE poste = ? AND date_t BETWEEN ? AND ? ORDER BY date_t ASC");
                        $req->execute(array($post, $data_1, $data_2));

                        $pdf->SetFont('Arial','',10);
                        while ($res1=$req->fetch()) {
                            $pdf->cell(20, 5, date("d/m/Y", strtotime($res1['date_t'])), 1, 0, 'L');
                            $pdf->cell(30, 5, decode_fr($res1['num_op']), 1, 0, 'L');
                            $pdf->cell(150, 5, decode_fr($res1['motif']), 1, 0, 'L');
                            $pdf->SetFont('Arial','', 8);
                            $pdf->cell(15, 5, '$'.$res1['montant'], 1, 0, 'L');
                            $pdf->SetFont('Arial','', 10);
                            $pdf->Ln(5);
                            $a[] = $res1['montant'];
                        }
                        $pdf->cell(20, 5, "Total", 1, 0, 'L');
                        $pdf->cell(30, 5, decode_fr(""), 1, 0, 'L', true);
                        $pdf->cell(150, 5, utf8_decode(""), 1, 0, 'L', true);
                        $pdf->SetFont('Arial','', 8);
                        $pdf->cell(15, 5, '$'.array_sum($a), 1, 0, 'L');
                        $pdf->SetFont('Arial','',10);
                    }
                }else{
                    echo ('indiference des date: la premiere date doit etre ineferiere ou egal a la deucieme date');
                }
            }
        }
    }

    $pdf->Ln(3);
    $pdf->SetFont('Arial','',10);
	$pdf->cell(300,20, decode_fr('par : '.$_SESSION['data']['noms'].'; le '.date('d/M/Y')),0,1,'C');
    $pdf->output();
?>