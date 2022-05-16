<?php
    session_start();
    require_once '../../../includes/ConnexionBdd.class.php';
	// FPDF
	require '../fpdf/fpdf.php';
    // verfication des sessions
    if(array_sum($_SESSION) == 0 && empty($_GET['a'])){
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

    function s_para(){
        $a = explode(", ", $_GET['type_f']);
        $var = "";

        for ($i=0; $i < count($a); $i++) { 
            $var .= str_replace(",", ", ", $a[$i]);
        }
        return $var;
    }

    // print_r($_GET);

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

    $pdf->Ln(1);
    $pdf->cell(197,7,'PAYEMENT DE L ETUDIANT(E) : ',0,1,'C');
    $pdf->Ln(2);

    // three arrays
    $a = array();
    $b = array();
    $c = array();

    if(isset($_GET['type_f']) && !empty($_GET['type_f']) && $_GET['type_f'] == "All"){
        // on commence par selectionner tous les etudiants
        $sel_all = ConnexionBdd::Connecter()->prepare("SELECT * FROM etudiants_inscrits WHERE matricule = ? AND fac = ? AND promotion = ? AND annee_academique = ?");
        $sel_all->execute(array($_GET['mat'], $_GET['fac'], $_GET['promotion'], $_GET['annee_acad']));
        while($data_student = $sel_all->fetch()){
            $pdf->SetFont('Arial','',8);
            $pdf->cell(60, 5, 'Matricule      : '.decode_fr($data_student['matricule']), 0, 1, 'L');
            $pdf->cell(60, 5, 'Noms           : '.decode_fr($data_student['noms']), 0, 1, 'L');
            $pdf->cell(60, 5, 'Faculte         : '.decode_fr($data_student['fac']), 0, 1, 'L');
            $pdf->cell(60, 5, 'Promotion     : '.decode_fr($data_student['promotion']), 0, 1, 'L');
            $pdf->cell(60, 5, 'Annee Acad. : '.decode_fr($data_student['annee_academique']), 0, 1, 'L');
            $pdf->cell(60, 5, 'Frais. : '.decode_fr("Tous les frais."), 0, 1, 'L');
    
            // selection du montant
            $pdf->SetFont('Arial','B',8);
            $pdf->cell(60, 5, "Type frais" , 1, 0, 'L');
            $pdf->cell(25, 5, "Montant prevu", 1, 0, 'L');
            $pdf->cell(25, 5, decode_fr("montant payé"), 1, 0, 'L');
            $pdf->cell(22, 5, "Solde", 1, 0, 'L');
            $pdf->cell(22, 5, " % ", 1, 0, 'L');
            $pdf->Ln(5);
            $sql = "SELECT faculte, promotion, type_frais, SUM(montant) as mt FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND annee_acad = ? GROUP BY faculte, type_frais";
            $sql_array = array($data_student['matricule'], $data_student['promotion'], $data_student['fac'], $data_student['annee_academique']);
                                                        
            $all = ConnexionBdd::Connecter()->prepare($sql);
            $all->execute($sql_array);
    
            while ($data = $all->fetch()){
                $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ? AND type_frais = ? AND annee_acad = ?";
                $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                $sql_2->execute(array($data_student['matricule'], $data['faculte'], $data['promotion'], $data['type_frais'], $data_student['annee_academique'])); 
                $pdf->SetFont('Arial','I',8);
                while($d = $sql_2->fetch()){
                    $pdf->cell(60, 5, utf8_decode(decode_fr($data['type_frais'])), 1, 0, 'L');
                    $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L');
                    $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                    $pdf->cell(22, 5, '$ '.mm($data['mt']-$d['mp']), 1, 0, 'L');
                    $pdf->cell(22, 5, montant_restant_pourcent($d['mp'], $data['mt']).'%', 1, 0, 'L');
                    // on affete les frais dans le tableau
                    $a[] = $data['mt'];
                    $b[] = $d['mp'];
                    $c[] = $data['mt']-$d['mp'];
                    $pdf->Ln(5);
                }
            }
            
            // total de frais
            if(count($a) > 0 && count($b) > 0 && count($c) > 0){
                $pdf->cell(60, 5, decode_fr("Total") , 1, 0, 'L');
                $pdf->cell(25, 5, '$ '.decode_fr(array_sum($a)) , 1, 0, 'L');
                $pdf->cell(25, 5, '$ '.decode_fr(array_sum($b)) , 1, 0, 'L');
                $pdf->cell(22, 5, '$ '.decode_fr(array_sum($c)) , 1, 0, 'L');
                $pdf->cell(22, 5, decode_fr(montant_restant_pourcent(array_sum($b), array_sum($a)).'%') , 1, 0, 'L');
                $a = array();
                $b = array();
                $c = array();
                $pdf->Ln(5);
            }else{
                $pdf->cell(60, 5, decode_fr("############ Aucun frais affecter à l'étudiant(e) ###########") , 0, 0, 'L');
            }
    
            $pdf->Ln(5);
            $pdf->Ln(5);
        }
    }else if(isset($_GET['type_frais']) && !empty($_GET['type_frais'])){
        // on commence par selectionner tous les etudiants
        $sel_all = ConnexionBdd::Connecter()->prepare("SELECT * FROM etudiants_inscrits WHERE matricule = ? AND fac = ? AND promotion = ? AND annee_academique = ?");
        $sel_all->execute(array($_GET['mat'], $_GET['fac'], $_GET['promotion'], $_GET['annee_acad']));
        while($data_student = $sel_all->fetch()){
            $pdf->SetFont('Arial','',8);
            $pdf->cell(60, 5, 'Matricule      : '.decode_fr($data_student['matricule']), 0, 1, 'L');
            $pdf->cell(60, 5, 'Noms           : '.decode_fr($data_student['noms']), 0, 1, 'L');
            $pdf->cell(60, 5, 'Faculte         : '.decode_fr($data_student['fac']), 0, 1, 'L');
            $pdf->cell(60, 5, 'Promotion     : '.decode_fr($data_student['promotion']), 0, 1, 'L');
            $pdf->cell(60, 5, 'Annee Acad. : '.decode_fr($data_student['annee_academique']), 0, 1, 'L');
            $pdf->cell(60, 5, 'Frais. : ', 0, 1, 'L');
            //explode(",", trim($_GET['type_f']))
            $a = explode(", ", $_GET['type_frais']);
            $t = array();
            $var = "";
            for ($i=0; $i < count($a); $i++) { 
                $var .= str_replace(",", " ", $a[$i]);
                $t[] = $a[$i];
            }
            $pdf->SetFont('Arial','BI',8);
            foreach ($t as $lst){
                $pdf->cell(60, 2, trim(utf8_decode($lst.PHP_EOL)), 0, 1, 'L');
                $pdf->Ln(3);
            }
    
            // selection du montant
            $pdf->SetFont('Arial','B',8);
            $pdf->cell(60, 5, "Type frais" , 1, 0, 'L');
            $pdf->cell(25, 5, "Montant prevu", 1, 0, 'L');
            $pdf->cell(25, 5, decode_fr("montant payé"), 1, 0, 'L');
            $pdf->cell(22, 5, "Solde", 1, 0, 'L');
            $pdf->cell(22, 5, " % ", 1, 0, 'L');
            $pdf->Ln(5);

            $a = explode(", ", $_GET['type_frais']);
            for ($i=0; $i < count($a); $i++){

                $sql = "SELECT faculte, promotion, type_frais, SUM(montant) as mt FROM affectation_frais WHERE matricule = ? AND promotion = ? AND faculte = ? AND type_frais = ? AND annee_acad = ? GROUP BY faculte, type_frais";
                $sql_array = array($data_student['matricule'], $data_student['promotion'], $data_student['fac'], trim(str_replace(",", " ", $a[$i])), $data_student['annee_academique']);
                                                            
                $all = ConnexionBdd::Connecter()->prepare($sql);
                $all->execute($sql_array);
        
                while ($data = $all->fetch()){
                    $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE matricule = ? AND faculte = ? AND promotion = ? AND type_frais = ? AND annee_acad = ?";
                    $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                    $sql_2->execute(array($data_student['matricule'], $data['faculte'], $data['promotion'], $data['type_frais'], $data_student['annee_academique'])); 
                    $pdf->SetFont('Arial','',8);
                    while($d = $sql_2->fetch()){
                        $pdf->cell(60, 5, decode_fr($data['type_frais']) , 1, 0, 'L');
                        $pdf->cell(25, 5, '$ '.mm($data['mt']), 1, 0, 'L');
                        $pdf->cell(25, 5, '$ '.mm($d['mp']), 1, 0, 'L');
                        $pdf->cell(22, 5, '$ '.mm($data['mt']-$d['mp']), 1, 0, 'L');
                        $pdf->cell(22, 5, montant_restant_pourcent($d['mp'], $data['mt']).'%', 1, 0, 'L');
                        // on affete les frais dans le tableau
                        $a[] = $data['mt'];
                        $b[] = $d['mp'];
                        $c[] = $data['mt']-$d['mp'];
                        $pdf->Ln(5);
                    }
                }
            }
            
            // total de frais
            $pdf->SetFont('Arial','BI',8);
            if(count($a) > 0 && count($b) > 0 && count($c) > 0){
                $pdf->cell(60, 5, decode_fr("Total") , 1, 0, 'L');
                $pdf->cell(25, 5, '$ '.decode_fr(array_sum($a)) , 1, 0, 'L');
                $pdf->cell(25, 5, '$ '.decode_fr(array_sum($b)) , 1, 0, 'L');
                $pdf->cell(22, 5, '$ '.decode_fr(array_sum($c)) , 1, 0, 'L');
                $pdf->cell(22, 5, decode_fr(montant_restant_pourcent(array_sum($b), array_sum($a)).'%') , 1, 0, 'L');
                $a = array();
                $b = array();
                $c = array();
                $pdf->Ln(5);
            }else{
                $pdf->SetFont('Arial','I',8);
                $pdf->SetTextColor(255, 0, 0);
                $pdf->cell(60, 10, decode_fr("############ Les type des frais selectionner ne sont pas affecté à l'étudiant(e) ###########") , 0, 0, 'L');
            }
            
            $pdf->SetTextColor(0, 0, 0);
            // $pdf->cell(190,0.3 ,"",1,1,'C');
            $pdf->Ln(5);
            // print_r($d);
            // $pdf->cell(60, 5, $d[0], 0, 1, 'L');
    
            $pdf->Ln(5);
        }
    }else{
        // header("Location: ../index.php" , true);
        // exit();
    }

    $pdf->Ln(5);
    $pdf->SetFont('Arial','',10);
	$pdf->cell(300,20, decode_fr('par : '.$_SESSION['data']['noms'].'; le '.date('d/M/Y')),0,1,'C');
    $pdf->output();
?>