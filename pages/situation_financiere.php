<?php
	session_start();
	require_once '../includes/ConnexionBdd.class.php';
	require_once '../includes/payement.class.php';

	// on recupere ;le dernier annee academique -> annee academique encours
	$req_annee = ConnexionBdd::Connecter()->query("SELECT * FROM `annee_academique` ORDER BY id DESC");

	$data = $req_annee->fetch();
	
	$Payement = new Payement($_SESSION['matricule'], $_SESSION['fac_etud'], $_SESSION['promotion'], $data['annee_acad']);
?>

<!DOCTYPE html>
<html>
<head>
	<title>situation financière :: <?=$_SESSION['noms']?></title>
	<meta charset="utf-8">
	<link rel="icon" type="image/jpg" href="./../images/UNIGOM_W260px.jpg">
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<!-- le responsive design -->
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<!-- bootstrap -->
	<link rel="stylesheet" type="text/css" href="../../css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="../../css/icones.css">
	<link rel="stylesheet" type="text/css" href="../../css/style.css">

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
	<!-- menu -->
	<?php
		require_once 'menu.php';
	?>
	<div class="container-fluid p-3 mt-5" style="margin-top: 3.7%;">
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center mt-3">
				<p class="h5 text-primary">Bienvenu(e) [<?=format_session($_SESSION['noms'])?>]</b> dans votre espace privé</p>
			</div>
		</div>
		<!-- infos -->
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 p-3">
				<?php
					// informations sur la situation financiere de l etudiant(e)
					$Payement->info_finance();
				?>
			</div>
			<!--  -->
			<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 p-3">
				<?php
                    // affichage des montant deja payer par l etudiants
					$Payement->montanttotal();
				?>
			</div>
		</div>
	</div>
	<!-- pied de page -->
    <div class="jumbotron-fluid text-center p-2 fixed-bottom">
        <p>
            <span class="text-white">© Copyright <a href="https://unigom.ac.cd">Université de Goma</a> 2021. Tous droits réservés. </span>
            Designed by <a href="mailto:jmika734@gmail.com">Justin Micah</a>
        </p>
    </div>
	<!-- jascript -->
	<script>
    	// jquery-3.5.1.min
    	window.jQuery || document.write('<script src="../js/jquery-3.5.1.min.js"><\/script>')</script><script src="../js/bootstrap.bundle.min.js">
    </script>
	<script src="../js/DataTables/js/jquery.dataTables.min.js"></script>
	<script src="../js/DataTables/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
		    $('#payement').DataTable();
		} );
	</script>

	<!-- ordoner les elements -->
	<script type="text/javascript"></script>
	<!-- Scripts Js End -->
</body>
</html>