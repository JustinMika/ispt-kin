<?php
	session_start();
    require_once '../../includes/ConnexionBdd.class.php';
    require_once '../../includes/verification.class.php';
    //verification des sessions
    require_once './sessions.php';
    $p = "postes des recettes";
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>postes des recettes</title>

    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="shortcut icon" href="../../images/ispt_kin.png" type="image/x-icon">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="js/DataTables/css/dataTables.jqueryui.min.css">
    <link rel="stylesheet" href="js/DataTables/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="js/DataTables/css/dataTables.bootstrap5.min.css">

</head>

<body id="page-top">
    <div id="wrapper">
        <?php require_once 'menu.php'; ?>
        <!-- End Sidebar -->

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
				<!-- menu user -->
                <?php require_once 'menu_user.php'; ?>
                <!-- main Content -->
                <div class="container-fluid bg-light">
                    <!-- page start-->
					<div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 p-0">
                            <div class="card m-0">
                                <div class="card-header bg-gradient-primary text-white">
                                    <h6 style="text-transform: uppercase;">les postes des recettes</h6>
                                </div>
                                <div class="card-body p-2" style="width: 100%;">
                                    <table class="table  table-hover table-dark table-md table-lg table-sm" id="table_annee_acad">
                                        <thead class="bg-dark">
                                            <tr>
                                                <!-- <th>#</th> -->
                                                <th>Postes des recettes</th>
                                                <th>Annee Academique</th>
                                                <th>Montant</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="t-frais" class=" text-secondary">
                                            <!-- affichage -->
                                            <?php
                                                $pfrais = ConnexionBdd::Connecter()->query("SELECT * FROM previson_frais_univ");
                                                while($data = $pfrais->fetch()){
                                                    ?>
                                                        <tr>
                                                            <!-- <td><?=$data['id']?></td> -->
                                                            <td><?=$data['poste']?></td>
                                                            <td><?=$data['annee_acad']?></td>
                                                            <td><?=$data['montant']?></td>
                                                            <td>
                                                                <button class="btn btn-primary btn-sm">voir</button>
                                                            </td>
                                                        </tr>
                                                    <?php
                                                }
                                            ?>
                                            <?php
                                                $pfrais = ConnexionBdd::Connecter()->query("SELECT type_frais, annee_acad, SUM(montant) AS montant FROM prevision_frais GROUP BY type_frais DESC");
                                                while($data = $pfrais->fetch()){
                                                    ?>
                                                        <tr>
                                                            <!-- <td><?=$data['id']?></td> -->
                                                            <td><?=$data['type_frais']?></td>
                                                            <td><?=$data['annee_acad']?></td>
                                                            <td><?=$data['montant']?></td>
                                                            <td>
                                                                <button class="btn btn-primary btn-sm">voir</button>
                                                            </td>
                                                        </tr>
                                                    <?php
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="card-footer text-muted p-0">...</div>
                            </div>
                        </div>
                    </div>
					<!-- page end-->
                </div>
            </div>
            <!-- footer -->
		  <?php include './footer.php';?>
        </div>
    </div>
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- fenetre modal pour la deconnexion-->
    <?php include_once './modal_decon.php';?>

    <script src="js/mes_scripts/poste_depense.js"></script>
    
    <script src="js/DataTables/js/jquery.dataTables.min.js"></script>
	<script src="js/DataTables/js/dataTables.bootstrap.min.js"></script>

    <script>
        $(document).ready(function() {
		    $('#table_annee_acad').DataTable();
		} );
    </script>
</body>

</html>