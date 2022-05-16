<?php
    session_start();
	require_once '../includes/ConnexionBdd.class.php';
	require_once './header_etudiants.php';
?>

<!DOCTYPE html>
<html>
<head>
	<title>Acceuil :: <?=$_SESSION['noms']?></title>
	<!-- logo de la fac. -->
	<link rel="icon" type="image/jpg" href="../images/UNIGOM_W260px.jpg">
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<!-- le responsive design -->
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<!-- bootstrap -->
	<link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="../css/icones.css">
	<link rel="stylesheet" type="text/css" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
	<style>
        .h3{
            font-weight: 500m !important;
        }
        .nav-link:hover {
            color: #cbd3f3 !important;
        }
	</style>
</head>
<body>
	<div class="main_page_etudiants">
		<!-- menu -->
		<?php
			require_once 'menu.php';
		?>
		<!-- contenus -->
		<div class="jumbotron">
			<div class="container m-auto p-3">
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 m-auto">
						<p class="h3 text-center">Bienvenue à L’université de Goma(UNIGOM)</p>
						<span class="text-secondary">
                            L’université de Goma, est un établissement universitaire public. Elle comme mission :  <b>*La formation des cadres de conception dans les domaines les plus divers de la vie nationale ; *La recherche scientifique fondamentale et appliquée orientée vers la solution des problèmes</b>
						</span>
                        <span class="text-secondary">
                            Les valeurs de l'Université de Goma sont basées sur :
                            Equité et Objectivité;
                            Unité et Diversité;
                            Dignité humaine.
                        </span>
						<div class="text-center mt-2" id="identite_etudiants">
							<img src="../images/etudiants.jpg" style="border-radius: 50%;" class="m-1" width="100px" height="100px">
							<table class="table bg-dark text-white text-left" style="border-radius: 10px;">
								<tr>
									<td>Noms</td>
									<td><?=format_session($_SESSION['noms'])?></td>
								</tr>
								<!--  -->
								<tr>
									<td>Promotion</td>
									<td><?=format_session($_SESSION['promotion'])?></td>
								</tr>
								<tr>
									<td>Faculte</td>
									<td><?=format_session($_SESSION['fac_etud'])?></td>
								</tr>
								<tr>
									<td>Annee Academique</td>
									<td><?=format_session($_SESSION['annee_academique'])?></td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- pied de page -->
		<div class="jumbotron-fluid text-center p-3 bg-dark">
			<p>
                <span class="text-white">© Copyright <a href="https://unigom.ac.cd">Université de Goma</a> 2021. Tous droits réservés. </span>
				Designed by <a href="mailto:jmika734@gmail.com">Justin Micah</a>
			</p>
		</div>
	</div>
	<!-- jascript -->
	<script src="../js/jquery-3.6.0.min.js"></script>
    <script src="../js/bootstrap.js"></script>

    <script type="text/javascript" language="javascript">
        // $("#identite_etudiants").hide();
        $(document).ready(function () {
            $("#identite_etudiants").slideDown(100).show(100);
         });
    </script>
	</body>
</html>