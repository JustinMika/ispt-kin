<?php
    header('Content-type:text/html; charset=UTF-8');
	session_start();
	require_once '../../../includes/ConnexionBdd.class.php';
	require '../fpdf/fpdf.php';
    if(array_sum($_SESSION['data']) == 0){
        header('location:../dec.php');
        exit();
    }

    function get_poste($f){
        if($f == "Tous"){
            return $f;
        }else{
            $d = ConnexionBdd::Connecter()->query("SELECT transaction_pdf.id_transaction, depense_facultaire.poste FROM transaction_pdf LEFT JOIN depense_facultaire ON transaction_pdf.id_pdf = depense_facultaire.id_pdf WHERE transaction_pdf.id_pdf = {$f}");
            $data = $d->fetch();
            return $data['poste'];
        }
    }

    function get_fac($f){
        if($f == "Tous"){
            return $f;
        }else{
            $d = ConnexionBdd::Connecter()->query("SELECT section from sections where id_section  = {$f}");
            $data = $d->fetch();
            return $data['section'];
        }
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
    $pdf->cell(300,1 ,"",1,1,'C', true);

    // $an =  ConnexionBdd::Connecter()->query("SELECT * FROM annee_academique GROUP BY annee_acad ORDER BY id DESC LIMIT 1");
    // if($an->rowCount() > 0){
    //     $an_r = $an->fetch();
    // }else{
    //     $an_r['annee_acad'] = '';
    //     die("Veuillez AJouter l annee academique");
    // }

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
                    $pdf->cell(280,10,'HISTORIQUE DES TRANSACTIONS DES POSTES DE DEPENSES DE SECTIONS: '.date("d/m/Y",strtotime($data_1)).' au '.date("d/m/Y",strtotime($data_2)),0,1,'C');
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
                        $req = ConnexionBdd::Connecter()->prepare("SELECT
                                transaction_pdf.id_transaction,
                                transaction_pdf.montant as montant_trans,
                                transaction_pdf.motif as motif,
                                transaction_pdf.date_transaction,
                                depense_facultaire.poste
                            FROM
                                transaction_pdf
                            LEFT JOIN depense_facultaire ON transaction_pdf.id_pdf = depense_facultaire.id_pdf
                            WHERE transaction_pdf.id_section = ? and transaction_pdf.date_transaction BETWEEN ? AND ? ORDER BY id_transaction, date_transaction ASC");
                        $req->execute(array($_SESSION['data']['access'], $data_1, $data_2));

                        $pdf->SetFont('Arial','',10);
                        while ($res1=$req->fetch()) {
                            $pdf->cell(20, 5, date("d/m/Y", strtotime($res1['date_transaction'])), 1, 0, 'L');
                            // $pdf->cell(30, 5, substr(decode_fr($res1['num_op']).'.', 0, 40), 1, 0, 'L');
                            $pdf->cell(70, 5, substr(decode_fr($res1['poste']), 0, 40), 1, 0, 'L');
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
                        // die($_SESSION['data']['access']);
                        $pdf->SetFont('Arial','B',10);
                        $pdf->cell(280,10,'POSTE : '.get_poste($post),0,1,'C');
                        //Tableau
                        $pdf->SetFont('Arial','B',10);
                        $pdf->cell(20, 5,'Date',1,0,'C');
                        // $pdf->cell(30, 5,'Num. OP',1,0,'C');
                        // $pdf->cell(70, 5,'Poste',1,0,'C');
                        $pdf->cell(150, 5,'Motif',1,0,'C');
                        $pdf->cell(15, 5,'Montant',1,0,'C');
                        $pdf->Ln(5);
                        $sql = "SELECT
                                    transaction_pdf.id_transaction,
                                    transaction_pdf.montant as montant_trans,
                                    transaction_pdf.motif,
                                    transaction_pdf.date_transaction as date_trans,
                                    depense_facultaire.poste
                                FROM
                                    transaction_pdf
                                LEFT JOIN depense_facultaire ON transaction_pdf.id_pdf = depense_facultaire.id_pdf
                                WHERE transaction_pdf.id_section = ? AND transaction_pdf.date_transaction BETWEEN ? AND ? AND transaction_pdf.id_pdf = ? ORDER BY transaction_pdf.date_transaction ASC";
                        $req = ConnexionBdd::Connecter()->prepare($sql);
                        $req->execute(array($_SESSION['data']['access'], $data_1, $data_2, $post));

                        $pdf->SetFont('Arial','',10);
                        while ($res1=$req->fetch()) {
                            $pdf->cell(20, 5, date("d/m/Y", strtotime($res1['date_trans'])), 1, 0, 'L');
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
	$pdf->cell(300,10, decode_fr('Chef de section : '.get_fac($_SESSION['data']['access'])),0,1,'C');
    $pdf->output();
?>