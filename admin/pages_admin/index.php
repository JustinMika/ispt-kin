<?php
	session_start();
	require_once '../../includes/ConnexionBdd.class.php';
	require_once '../../includes/verification.class.php';
	//verification des sessions
	require_once './sessions.php';
	
	$p = "Dashboard";

	// restriction des user
	function f_annee($v){
		if(isset($v) && !empty($v)){
			return $v;
		}else{
			return '';
		}
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="">
	<meta name="author" content="">

	<title>Home Admin :: Dashboard</title>
	<link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
	<link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
		rel="stylesheet">
		<script src="js/Chart.min.js"></script>
	<link href="css/sb-admin-2.min.css" rel="stylesheet">
	<link rel="shortcut icon" href="../../images/ispt_kin.png" type="image/x-icon">
</head>

<body id="page-top">
	<div id="wrapper">
		<?php require_once 'menu.php'; ?>
		<!-- End Sidebar -->                     
		<div id="content-wrapper" class="d-flex flex-column">
			<div id="content">
				<?php require_once 'menu_user.php'; ?>
				<!-- main Content -->
				<div class="container-fluid">
					<!-- dashboard -->
					<div class="d-sm-flex align-items-center justify-content-between mb-4">
					  <h1 class="h3 mb-0 text-gray-800" style="text-transform: uppercase;">Dashboard</h1>
					</div>
					<div class="row" style="<?php if($_SESSION['data']['fonction'] == "Sec. de fac."){echo 'd';}else{
						echo 'display:none';}?>">
						<!-- nombre total d etudiants -->
						<?php 
							$an =  ConnexionBdd::Connecter()->query("SELECT * FROM annee_acad GROUP BY annee_acad ORDER BY id_annee DESC LIMIT 1 ");
							if(!empty($an->fetch())){
								$an_r = $an->fetch();
							}else{
								$an_r['annee_acad'] = '';
							}
							
							$nb_fac = ConnexionBdd::Connecter()->prepare("SELECT * FROM etudiants_inscrits WHERE annee_academique = ? AND fac  = ?");
							$nb_fac->execute(array($an_r['annee_acad'], $_SESSION['data']['access']));
							$n = $nb_fac->rowCount();
							?>
								<div class="col-xl-3 col-md-6 mb-4">
									<div class="card border-left-success shadow">
										<div class="card-body">
											<div class="row no-gutters align-items-center">
												<div class="">
													<div class="text-xs font-weight-bold text-primary text-uppercase mb-3">
													<!-- <i class="fab fa-facebook-f"></i> -->
													<?=$_SESSION['data']['access']?>::<?=$an_r['annee_acad']?></div>
													<div class="h5 mt-1 font-weight-bold text-gray-800">
														<?=$n?> Etudiant(e)s
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							<?php
						?>
					</div>

					<!-- faculte -> nombres etudiants -->
					<div class="row" style="<?=r5()?>">
						<?php
							$sel_etudiants = ConnexionBdd::Connecter()->query("SELECT id_section FROM etudiants_inscrits GROUP BY id_section");
							$nbre = $sel_etudiants->rowCount();
							$tableau_fac = array();
							// die($nbre);

							while($data = $sel_etudiants->fetch()){
								$tableau_fac[] = $data['id_section'];
							}

							foreach ($tableau_fac as $faculte) {
								// on recuoere le dernier annee acad
								$an =  ConnexionBdd::Connecter()->query("SELECT id_annee FROM annee_acad GROUP BY annee_acad ORDER BY id_annee DESC LIMIT 1 ");
								$an_r = $an->fetch();

								$nb_fac = ConnexionBdd::Connecter()->prepare("SELECT * FROM etudiants_inscrits WHERE id_section = ? AND id_annee = ?");
								$nb_fac->execute(array($faculte, $an_r['id_annee']));
								$n = $nb_fac->rowCount();
								
								$s = ConnexionBdd::Connecter()->prepare("SELECT section FROM sections WHERE id_section = ?");
								$s->execute(array($faculte));
								$f = $s->fetch();
								?>
									<div class="col-xl-3 col-md-6 mb-4">
										<div class="card border-left-primary shadow h-100 py-2">
											<div class="card-body">
												<div class="row no-gutters align-items-center">
													<div class="col mr-2">
														<div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
															<?=$f['section']?></div>
														<div class="h5 mb-0 font-weight-bold text-gray-800"><?=$n?> Étudiant(s)</div>
													</div>
													<div class="col-auto">
														<i class="fas fa-university fa-2x text-gray-300"></i>
													</div>
												</div>
											</div>
										</div>
									</div>
								<?php
							}
						?>	
						<!-- nombre total d etudiants -->
						<?php 
							$an =  ConnexionBdd::Connecter()->query("SELECT * FROM annee_acad GROUP BY annee_acad ORDER BY id_annee DESC LIMIT 1 ");
							try {
								$an_r = $an->fetch();
							} catch (Exception $e) {
								die($e->getMessage());
							}
							if(!empty($an_r = $an->fetch())){
								$an_r = $an->fetch();
							}else{
								$an_r['annee_acad'] = '';
							}
							$nb_fac = ConnexionBdd::Connecter()->prepare("SELECT * FROM etudiants_inscrits WHERE annee_academique = ?");
							$nb_fac->execute(array($an_r['annee_acad']));
							$n = $nb_fac->rowCount();
							?>
								<div class="col-xl-3 col-md-6 mb-4">
									<div class="card border-left-success shadow">
										<div class="card-body">
											<div class="row no-gutters align-items-center">
												<div class="">
													<div class="text-xs font-weight-bold text-primary text-uppercase mb-3">
														Total Etudiants : <?=$an_r['annee_acad']?></div>
													<div class="h5 mt-1 font-weight-bold text-gray-800">
														<?=$n?> Etudiant(e)s
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							<?php
						?>
					</div>

					<div class="row" style="<?=r4()?>">
						<div class="col-xl-8 col-lg-7">
							<div class="card shadow mb-4">
								<!-- Card Header - Dropdown -->
								<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
									<h6 class="m-0 font-weight-bold text-primary">Payement selon les facultes</h6>
									<div class="dropdown no-arrow">
										<a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
											data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										<i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
										</a>
										<div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
											aria-labelledby="dropdownMenuLink">
											<a class="dropdown-item" href="#">Imprimer</a>
										</div>
									</div>
								</div>
								<div class="card-body">
									<div class="chart-area">
										<canvas id="myBarChart"></canvas>
										<?php
											$data1 = "";
											$data2 = "";

											// on recuoere le dernier annee acad
											$an =  ConnexionBdd::Connecter()->query("SELECT * FROM annee_acad GROUP BY annee_acad ORDER BY id_annee DESC LIMIT 0,1");
											if($an->rowCount() > 0){
												$an_r = $an->fetch();
											}else{
												$an_r['annee_acad'] = '';
											}

											$frais_par_fac = ConnexionBdd::Connecter()->prepare("SELECT faculte.fac AS f, SUM(payement.montant) AS m FROM faculte LEFT JOIN payement ON faculte.fac = payement.faculte WHERE payement.annee_acad = ? GROUP BY faculte.fac");
											$frais_par_fac->execute(array(f_annee($an_r['annee_acad'])));

											while($data_ = $frais_par_fac->fetch()){
												$data1 = $data1.'"'. $data_['f'].'",';
												$data2 = $data2.''. $data_['m'].',';
											}

											$data1 = trim(decode_fr($data1), ",");
											$data2 = trim(decode_fr($data2), ",");
										?>
										<script>
										Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
										Chart.defaults.global.defaultFontColor = '#858796';

										function number_format(number, decimals, dec_point, thousands_sep) {
											// *     example: number_format(1234.56, 2, ',', ' ');
											// *     return: '1 234,56'
											number = (number + '').replace(',', '').replace(' ', '');
											var n = !isFinite(+number) ? 0 : +number,
											prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
											sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
											dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
											s = '',
											toFixedFix = function(n, prec) {
												var k = Math.pow(10, prec);
												return '' + Math.round(n * k) / k;
											};
											// Fix for IE parseFloat(0.55).toFixed(0) = 0;
											s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
											if (s[0].length > 3) {
											s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
											}
											if ((s[1] || '').length < prec) {
											s[1] = s[1] || '';
											s[1] += new Array(prec - s[1].length + 1).join('0');
											}
											return s.join(dec);
										}

										// Bar Chart Example
										var ctx = document.getElementById("myBarChart");
										var myBarChart = new Chart(ctx, {
											type: 'bar',
											data: {
											labels: [<?=$data1?>],
											datasets: [{
												label: "Montant ",
												backgroundColor: "#4e73df",
												hoverBackgroundColor: "#2e59d9",
												borderColor: "#4e73df",
												data: [<?=$data2?>],
											}],
											},
											options: {
											maintainAspectRatio: false,
											layout: {
												padding: {
												left: 10,
												right: 25,
												top: 25,
												bottom: 0
												}
											},
											scales: {
												xAxes: [{
												time: {
													unit: 'month'
												},
												gridLines: {
													display: true,
													drawBorder: true
												},
												ticks: {
													maxTicksLimit: 20
												},
												maxBarThickness: 25,
												}],
												yAxes: [{
												ticks: {
													min: 0
												//   max: 150,
												//   maxTicksLimit: 5
													/*padding: 10*/,
													// Include a dollar sign in the ticks
													callback: function(value, index, values) {
													return '$' + number_format(value);
													}
												},
												gridLines: {
													color: "rgb(234, 236, 244)",
													zeroLineColor: "rgb(234, 236, 244)",
													drawBorder: false,
													borderDash: [2],
													zeroLineBorderDash: [2]
												}
												}],
											},
											legend: {
												display: false
											},
											tooltips: {
												titleMarginBottom: 10,
												titleFontColor: '#6e707e',
												titleFontSize: 12,
												backgroundColor: "rgb(255,255,255)",
												bodyFontColor: "#858796",
												borderColor: '#dddfeb',
												borderWidth: 1,
												xPadding: 15,
												yPadding: 15,
												displayColors: false,
												caretPadding: 10,
												callbacks: {
												label: function(tooltipItem, chart) {
													var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
													return datasetLabel + ': $' + number_format(tooltipItem.yLabel);
												}
												}
											},
											}
										});
										</script>
									</div>
								</div>
							</div>
						</div>

						<div class="col-xl-4 col-lg-5">
							<div class="card shadow mb-4">
								<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
									<h6 class="m-0 font-weight-bold text-primary">Payement selon les promotions</h6>
									<div class="dropdown no-arrow">
										<a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
											data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										<i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
										</a>
										<div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
											aria-labelledby="dropdownMenuLink">
											<a class="dropdown-item" href="#">Imprimer</a>
										</div>
									</div>
								</div>
								<!-- Card Body -->
								<div class="card-body">
									<div class="chart-pie pt-4 pb-2">
										<canvas id="myPieChart"></canvas>
										<?php
										
										// $frais_promotion = ConnexionBdd::Connecter()->query("SELECT id, promotion, SUM(montant) AS montant FROM payement GROUP BY promotion");

										$data_promotion = "";
										$data__promotion = "";

										// on recuoere le dernier annee acad
										$an =  ConnexionBdd::Connecter()->query("SELECT * FROM annee_acad GROUP BY annee_acad ORDER BY id_annee DESC LIMIT 1 ");
										if($an->rowCount() > 0){
											$an_r = $an->fetch();
										}else{
											$an_r['annee_acad'] = '';
										}

										$frais_promotion = ConnexionBdd::Connecter()->prepare("SELECT promotion, SUM(payement.montant) AS m FROM payement WHERE annee_acad = ? GROUP BY promotion");
										$frais_promotion->execute(array($an_r['annee_acad']));

										while($data = $frais_promotion->fetch()){
											$data_promotion = $data_promotion.'"'. $data['m'].'",';
											$data__promotion = $data__promotion.'"'. $data['promotion'].'",';
										}

										$data_promotion = trim($data_promotion, ",");
										$data__promotion = trim($data__promotion, ",");
										?>

										<script>
										Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
										Chart.defaults.global.defaultFontColor = '#858796';

										// Pie Chart Example
										var ctx = document.getElementById("myPieChart");
										var myPieChart = new Chart(ctx, {
											type: 'doughnut',
											data: {
											labels: [<?=$data__promotion?>],
											datasets: [{
												data: [<?=$data_promotion?>],
												backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
												hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf'],
												hoverBorderColor: "rgba(234, 236, 244, 1)",
											}],
											},
											options: {
											maintainAspectRatio: false,
											tooltips: {
												backgroundColor: "rgb(255,255,255)",
												bodyFontColor: "#858796",
												borderColor: '#dddfeb',
												borderWidth: 1,
												xPadding: 15,
												yPadding: 15,
												displayColors: true,
												caretPadding: 10,
											},
											legend: {
												display: true
											},
											cutoutPercentage: 80,
											},
										});
										</script>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="row" style="<?=r4()?>">
						<!-- poste de recette -->
						<div class="col-xl-12 col-sm-12 col-md-12 col-lg-12">
							<div class="card shadow mb-4">
								<div class="card-header py-3">
									<h6 class="m-0 font-weight-bold text-primary">Poste des recette</h6>
								</div>
								<div class="card-body">
									<?php
									$npost_recette = "";
									$mposte_recette = '';
									// on recuoere le dernier annee acad
									$an =  ConnexionBdd::Connecter()->query("SELECT * FROM annee_acad GROUP BY annee_acad ORDER BY id_annee DESC LIMIT 1");
									if($an->rowCount() > 0){
										$an_r = $an->fetch();
									}else{
										$an_r['id_annee'] = '';
									}

									$pfrais = ConnexionBdd::Connecter()->query("SELECT * FROM poste_recette WHERE id_annee = '".$an_r['id_annee']."'");
									while($data = $pfrais->fetch()){
										$npost_recette = $npost_recette.'"'. $data['poste_rec'].'",';
										$mposte_recette = $mposte_recette.''. $data['montant'].', ';
									}

									$pfrais = ConnexionBdd::Connecter()->query("SELECT type_frais, id_annee, SUM(montant) AS montant FROM prevision_frais WHERE id_annee = '".$an_r['id_annee']."' GROUP BY type_frais DESC");
									while($data = $pfrais->fetch()){
										$npost_recette = $npost_recette.'"'. $data['type_frais'].'",';
										$mposte_recette = $mposte_recette.''. $data['montant'].', ';
									}

									$npost_recette = trim($npost_recette, ",");
									$mposte_recette = trim($mposte_recette, ",");

									// var_dump($npost_recette);
									?>
									
									<div class="chart-bar">
										<canvas id="myBarChartPR"></canvas>
									</div>
									<hr>
									<code>Poste des recette</code>
									<script>
									function number_format(number, decimals, dec_point, thousands_sep) {
										// *     example: number_format(1234.56, 2, ',', ' ');
										// *     return: '1 234,56'
										number = (number + '').replace(',', '').replace(' ', '');
										var n = !isFinite(+number) ? 0 : +number,
										prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
										sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
										dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
										s = '',
										toFixedFix = function(n, prec) {
											var k = Math.pow(10, prec);
											return '' + Math.round(n * k) / k;
										};
										// Fix for IE parseFloat(0.55).toFixed(0) = 0;
										s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
										if (s[0].length > 3) {
										s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
										}
										if ((s[1] || '').length < prec) {
										s[1] = s[1] || '';
										s[1] += new Array(prec - s[1].length + 1).join('0');
										}
										return s.join(dec);
									}
									// Area Chart Example
									var ctx = document.getElementById("myBarChartPR");
									var myLineChart = new Chart(ctx, {
										type: 'bar',
										data: {
										labels: [<?=$npost_recette?>],
										datasets: [{
											label: "Poste des recettes",
											lineTension: 0.01,
											/**backgroundColor: "#4e73df",
										hoverBackgroundColor: "#2e59d9",
										borderColor: "#4e73df", */
											backgroundColor: "#2e59d9",
											borderColor: "rgba(78, 115, 223, 1)",
											pointRadius: 5,
											pointBackgroundColor: "rgba(78, 115, 223, 1)",
											pointBorderColor: "rgba(78, 115, 223, 1)",
											pointHoverRadius: 6,
											pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
											pointHoverBorderColor: "rgba(78, 115, 223, 1)",
											pointHitRadius: 50,
											pointBorderWidth: 2,
											data: [<?=$mposte_recette?>],
										}],
										},
										options: {
										maintainAspectRatio: false,
										layout: {
											padding: {
											left: 10,
											right: 15,
											top: 15,
											bottom: 0
											}
										},
										scales: {
											xAxes: [{
											time: {
												unit: 'date'
											},
											gridLines: {
												display: true,
												drawBorder: true
											},
											ticks: {
												maxTicksLimit: 20
											}
											}],
											yAxes: [{
											ticks: {
												min: 0,
												// max: 100000,
												// maxTicksLimit: 10,
												padding: 10,
												// Include a dollar sign in the ticks
												callback: function(value, index, values) {
												return '$' + number_format(value);
												}
											},
											gridLines: {
												color: "rgb(234, 236, 244)",
												zeroLineColor: "rgb(234, 236, 244)",
												drawBorder: false,
												borderDash: [2],
												zeroLineBorderDash: [0]
											}
											}],
										},
										legend: {
											display: true
										},
										tooltips: {
											backgroundColor: "rgb(255,255,255)",
											bodyFontColor: "#858796",
											titleMarginBottom: 10,
											titleFontColor: '#6e707e',
											titleFontSize: 12,
											borderColor: '#dddfeb',
											borderWidth: 1,
											xPadding: 15,
											yPadding: 15,
											displayColors: true,
											intersect: true,
											mode: 'index',
											caretPadding: 10,
											callbacks: {
											label: function(tooltipItem, chart) {
												var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
												return datasetLabel + ': $' + number_format(tooltipItem.yLabel);
											}
											}
										}
										}
									});
									</script>
								</div>
							</div>
						</div>
					</div>

					<div class="row" style="<?=r4()?>">
						<!-- poste de recette -->
						<div class="col-xl-12 col-sm-12 col-md-12 col-lg-12">
							<div class="card shadow mb-4">
								<div class="card-header py-3">
									<h6 class="m-0 font-weight-bold text-primary">Poste des depenses</h6>
								</div>
								<div class="card-body">
									<div class="chart-bar">
										<?php
											$npost = "";
											$mposte = "";
											// on recuoere le dernier annee acad
											$an =  ConnexionBdd::Connecter()->query("SELECT * FROM annee_acad GROUP BY annee_acad ORDER BY id_annee DESC LIMIT 1 ");
											if($an->rowCount() > 0){
												$an_r = $an->fetch();
											}else{
												$an_r['id_annee'] = '';
											}
											$pd = ConnexionBdd::Connecter()->query("SELECT * FROM poste_depense WHERE id_annee = '".$an_r['id_annee']."'");
											while($data = $pd->fetch()){
												$npost = $npost.'"'. $data['poste'].'",';
												$mposte = $mposte.''. montant_restant_pourcent(montant_restant($data['montant'], $data['depense']), $data['montant']).',';
											}
											$npost = trim(decode_fr($npost), ",");
											$mposte = trim(decode_fr($mposte), ",");
										?>
										<canvas id="myBarChartPosteDepense"></canvas>
									</div>
									<hr>
									Poste des depenses pour l'annee academique encours          
								</div>
								<script>
								// Set new default font family and font color to mimic Bootstrap's default styling
								Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
								Chart.defaults.global.defaultFontColor = '#858796';

								function number_format(number, decimals, dec_point, thousands_sep) {
									// *     example: number_format(1234.56, 2, ',', ' ');
									// *     return: '1 234,56'
									number = (number + '').replace(',', '').replace(' ', '');
									var n = !isFinite(+number) ? 0 : +number,
									prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
									sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
									dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
									s = '',
									toFixedFix = function(n, prec) {
										var k = Math.pow(10, prec);
										return '' + Math.round(n * k) / k;
									};
									// Fix for IE parseFloat(0.55).toFixed(0) = 0;
									s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
									if (s[0].length > 3) {
									s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
									}
									if ((s[1] || '').length < prec) {
									s[1] = s[1] || '';
									s[1] += new Array(prec - s[1].length + 1).join('0');
									}
									return s.join(dec);
								}

								// Bar Chart Example
								var ctx = document.getElementById("myBarChartPosteDepense");
								var myBarChart = new Chart(ctx, {
									type: 'bar',
									data: {
									labels: [<?=$npost?>],
									datasets: [{
										label: "Poste des depenses",
										backgroundColor: "#4e73df",
										hoverBackgroundColor: "#2e59d9",
										borderColor: "#4e73df",
										data: [<?=$mposte?>],
									}],
									},
									options: {
									maintainAspectRatio: false,
									layout: {
										padding: {
										left: 0,
										right: 0,
										top: 0,
										bottom: 0
										}
									},
									scales: {
										xAxes: [{
										time: {
											unit: 'month'
										},
										gridLines: {
											display: true,
											drawBorder: false
										},
										ticks: {
											maxTicksLimit: 100
										},
										maxBarThickness: 20,
										}],
										yAxes: [{
										ticks: {
											min: 0,
											max: 100,
											maxTicksLimit: 10,
											padding: 10,
											// Include a dollar sign in the ticks
											callback: function(value, index, values) {
											return number_format(value) + '%';
											}
										},
										gridLines: {
											color: "rgb(234, 236, 244)",
											zeroLineColor: "rgb(234, 236, 244)",
											drawBorder: false,
											borderDash: [2],
											zeroLineBorderDash: [2]
										}
										}],
									},
									legend: {
										display: true
									},
									tooltips: {
										titleMarginBottom: 10,
										titleFontColor: '#6e707e',
										titleFontSize: 14,
										backgroundColor: "rgb(255,255,255)",
										bodyFontColor: "#858796",
										borderColor: '#dddfeb',
										borderWidth: 1,
										xPadding: 15,
										yPadding: 15,
										displayColors: false,
										caretPadding: 10,
										callbacks: {
										label: function(tooltipItem, chart) {
											var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
											return datasetLabel + ' : ' + number_format(tooltipItem.yLabel)+ '%' ;
										}
										}
									},
									}
								});
								</script>
							</div>
						</div>
					</div>
					<!-- utilisateur admin et logs -->
					<div class="row">
					  <div class="col-md-5">
						<div class="card shadow">
							<div class="card-header">
								<h4>utilisateur admin</h4>
							</div>
							<div class="card-body">
								<table class="table table-bordered table-hover table-md">
									<thead class="thead-inverse">
										<tr>
											<th>#ID</th>
											<th>profil</th>
											<th>noms</th>
										</tr>
										</thead>
										<tbody>
											<?php
												$list_user = ConnexionBdd::Connecter()->query("SELECT * FROM utilisateurs");
												while($data = $list_user->fetch()){
													?>
														<tr>
																<td class="m-3"><?=$data['id_user']?></td>
																<td>
																	<img src="<?=$data['profil']?>" class="img-fluid rounded-top,rounded-right,rounded-bottom,rounded-left,rounded-circle" alt="" width="50">
																</td>
																<td class="m-3"><?=$data['noms']?></td>
														</tr>
													<?php
												}
											?>
										</tbody>
								</table>
							</div>
							<div class="card-footer"></div>
						</div>
					  </div>
					<div class="col-md-7">
						<div class="card shadow">
							<div class="card-header">
								<h4>Journal d'activite des utilisateurs</h4>
							</div>
							<div class="card-body">
								<table class="table table-bordered table-hover table-md">
									<thead class="thead-inverse">
										<tr>
											<!-- <th>#ID</th> -->
											<th>noms</th>
											<th>Date & Heure</th>
											<th>Actions</th>
										</tr>
									</thead>
									<tbody>
										<?php
											$list_user = ConnexionBdd::Connecter()->query("SELECT utilisateurs.noms, log_user.log_action as actions, log_user.date_action FROM log_user LEFT JOIN utilisateurs ON utilisateurs.id_user = log_user.id_user ORDER BY log_user.date_action DESC LIMIT 11");
											while($data = $list_user->fetch()){
												?>
													<tr>
														<!-- <td class="m-3"><?=$data['id']?></td> -->
														<td class="m-3"><?=$data['noms']?></td>
														<td class="m-3"><?=date($data['date_action'], strtotime("d/m/Y H:M:S"))?></td>
														<td class="m-3"><?=$data['actions']?></td>
													</tr>
												<?php
											}
										?>
									</tbody>
							  	</table>
							</div>
						  <div class="card-footer"></div>
					  </div>
				  </div>
				</div>
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
</body>
</html>