<?php 
    session_start();
    require_once '../includes/ConnexionBdd.class.php';
	require_once '../includes/payement.class.php';

	// FPDF
    require_once '../admin/pages_admin/fpdf/fpdf.php';

    // on recupere ;le dernier annee academique -> annee academique encours
	$req_annee = ConnexionBdd::Connecter()->query("SELECT * FROM `annee_academique` ORDER BY id DESC");

	$data = $req_annee->fetch();

    if(count($data) > 1) {
        $Payement = new Payement($_SESSION['matricule'], $_SESSION['fac_etud'], $_SESSION['promotion'], $data['annee_acad']);
    }else{
        header('location:../index.php');
    }

    if(array_sum($_SESSION) == 0){
        header('location:../dec.php');
        exit();
    }

	$Payement->rapport();
?>