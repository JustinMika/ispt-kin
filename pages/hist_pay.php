<?php
    session_start();
    require_once '../includes/ConnexionBdd.class.php';
	require_once '../includes/payement.class.php';

    // on recupere ;le dernier annee academique -> annee academique encours
	$req_annee = ConnexionBdd::Connecter()->query("SELECT * FROM `annee_academique` ORDER BY id DESC");

	$data = $req_annee->fetch();

    if(count($data) > 1) {
        $Payement = new Payement($_SESSION['matricule'], $_SESSION['fac_etud'], $_SESSION['promotion'], $data['annee_acad']);
    }else{
        header('location:../index.php');
    }
?>

<!doctype html>
<html lang="en">
    <head>
        <title>Historique de payement de l'etudiant</title>
        <!-- logo de la fac. -->
        <link rel="icon" type="image/jpg" href="../images/etudiants.jpg">
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <!-- bootstrap -->
        <link rel="stylesheet" type="text/css" href="../../css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="../../css/icones.css">
        <link rel="stylesheet" type="text/css" href="../../DataTables/css/jquery.dataTables.css">

        <!-- font awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <style>
            .h3{
                font-weight: 500 !important;
            }
            .nav-link:hover {
                color: #cbd3f3 !important;
            }

            .dec:hover{
                color: red;
            }
        </style>
    </head>
  <body>
        <?php require_once './menu.php'; ?>
        <div class="container-fluid p-3 mt-5" style="margin-top: 3.7%;">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-8 col-8-lg m-auto mt-5">
                    <div class="d-flex flex" style="justify-content:space-between">
                        <span class="text-center h3 text-secondary mt-3">Historique de payement de frais</span>
                        <a href="../TTFichierHist/" target="_blank" class="btn btn-primary btn-sm mb-5 mt-1">Imprimer</a>
                    </div>
                    <?php
                        $Payement->voir_historique();
                    ?>
                </div>
            </div>
        </div>
  </body>
</html>