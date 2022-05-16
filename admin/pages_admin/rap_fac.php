<?php
    // header()
    session_start();
    require_once '../../includes/ConnexionBdd.class.php';
    require_once '../../includes/verification.class.php';
    //verification des sessions
    require_once './sessions.php';
    require './fpdf/fpdf.php';
    $p = "Rapport Facultaire";

    function all($pdf){
        $pdf->AddPage();
        $pdf->SetFont('Arial','B',12);

        $pdf->cell(150,6,'',0,1,'C');
        $pdf->cell(197,6, decode_fr("UNIVERSITE DE GOMA"),0,1,'C');
        $pdf->cell(197,6, decode_fr("«UNIGOM»"),0,1,'C');
        $pdf->SetFont('Arial','',11);
        $pdf->cell(197,6, decode_fr("BP 204 Goma (RDC)"),0,1,'C');
        $pdf->cell(197,6, decode_fr("BP 2277 Gisenyi (RWANDA)"),0,1,'C');
        $pdf->cell(197,6, decode_fr("E-mail : rectorat@unigom.ac.cd"),0,1,'C');
        $pdf->SetFont('Arial','BI',11);
        $pdf->cell(197,4, decode_fr("site web : www.unigom.ac.cd"),0,1,'C', false, 'www.unigom.ac.cd');
        $pdf->SetFont('Arial','',11);

        $pdf->Ln(5);
        $pdf->cell(150,5,'Pax ex scientia splendeat',0,1,'L');
        // logo de la faculte
        $pdf->Image("../../images/UNIGOM_W260px.jpg", 15,16,30, 30);
        $pdf->SetFillColor(12, 12, 200);
        $pdf->SetTextColor(12, 12, 200);
        $pdf->cell(190,1 ,"",1,0,'C', true);
        $pdf->Ln(3);

        $pdf->SetFont('Arial','',10);
        $pdf->SetTextColor(0,0,0);
        $pdf->cell(60, 5, decode_fr('Année Academique : '.verify($_POST['annee_acad_deb'])), 0, 1, 'L');
        $pdf->cell(60, 5, decode_fr('Faculté                     : '.verify($_POST['fac_etudiant'])), 0, 1, 'L');
    }

    function verify($var){
        if(isset($var) && !empty($var)){
            return $var;
        }else{
            return ' - ';
        }

    }

    function mm($v){
        if(empty($v)){
            return '0';
        }else{
            return $v;
        }
    }

    // filtre par faculte
    if(isset($_POST['btn_fac']) && isset($_POST['type_frais']) && !empty($_POST['type_frais'])){
        $pdf = new FPDF('P', 'mm', 'A4');
        $annee_acad_deb = $_POST['annee_acad_deb'];

        all($pdf);
        $pdf->SetFont('Arial','BU',10);
        $pdf->cell(190, 10, decode_fr('RAPPORT FACULTAIRE'), 0, 1, 'C');
        $pdf->SetFont('Arial','',10);

        $annee_acad_deb = $_POST['annee_acad_deb'];
        $ff = $_POST['fac_etudiant'];
        if(isset($annee_acad_deb)){
            $t_fac = array();
            $ff = $_POST['fac_etudiant'];
            // selection des toutes les facultes
            $f = ConnexionBdd::Connecter()->query("SELECT DISTINCT fac FROM etudiants_inscrits GROUP BY fac");
            while($df = $f->fetch()){
                $t_fac[] = $df['fac'];
            }
            if($_POST['fac_etudiant'] == "Tous"){
                $a = array();
                $b = array();
                foreach($t_fac as $ff){
                    $pdf->Ln(3);
                    $pdf->cell(190, 5, decode_fr('Annee Acad. : '.$annee_acad_deb), 0, 1, 'L');
                    $pdf->cell(190, 5, decode_fr('Faculte : '.$ff), 0, 1, 'L');
                    $pdf->Ln(1);

                    $pdf->cell(70, 5, decode_fr('Tye de frais'), 1, 0, 'L');
                    $pdf->cell(20, 5, decode_fr('Promotion'), 1, 0, 'L');
                    $pdf->cell(30, 5, decode_fr('Montant prévue'), 1, 0, 'L');
                    $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                    $pdf->cell(28, 5, decode_fr('Solde'), 1, 0, 'L');
                    $pdf->cell(15, 5, decode_fr('%'), 1, 0, 'L');
                    $pdf->Ln(4);

                    $rf = ConnexionBdd::Connecter()->prepare("SELECT SUM(montant) as montant, type_frais, promotion FROM affect_fac_frais WHERE faculte = ? AND annee_acad = ? GROUP BY promotion, type_frais");
                    $rf->execute(array($ff, $annee_acad_deb));

                    while($d = $rf->fetch()){
                        $pay = ConnexionBdd::Connecter()->prepare("SELECT SUM(montant) as montant FROM payement_fac_frais WHERE faculte = ? AND annee_acad = ? AND promotion = ?");
                        $pay->execute(array($ff, $annee_acad_deb, $d['promotion']));
                        while($data = $pay->fetch()){
                            $pdf->Ln(1);
                            $pdf->cell(70, 5, decode_fr($d['type_frais']), 1, 0, 'L');
                            $pdf->cell(20, 5, decode_fr($d['promotion']), 1, 0, 'L');
                            $pdf->cell(30, 5, '$'.mm($d['montant']), 1, 0, 'L');
                            $pdf->cell(30, 5, '$'.mm($data['montant']), 1, 0, 'L');
                            $pdf->cell(28, 5, '$'.decode_fr(mm($d['montant'] - $data['montant'])), 1, 0, 'L');
                            $pdf->cell(15, 5, decode_fr(montant_restant_pourcent($data['montant'], $d['montant'])).'%', 1, 0, 'L'); 
                            $pdf->Ln(4);
                            $a[] = $d['montant'];
                            $b[] = $data['montant'];
                        }
                    }
                    $pdf->Ln(1);
                    $pdf->cell(70, 5, decode_fr('Total'), 1, 0, 'L');
                    $pdf->SetFillColor(0, 0, 0);
                    $pdf->cell(20, 5, '-', 1, 0, 'L', true);
                    $pdf->cell(30, 5, '$'.decode_fr(array_sum($a)), 1, 0, 'L');
                    $pdf->cell(30, 5, '$'.decode_fr(array_sum($b)), 1, 0, 'L');
                    $pdf->cell(28, 5, '$'.decode_fr(array_sum($a) - array_sum($b)), 1, 0, 'L');
                    $pdf->cell(15, 5, decode_fr(montant_restant_pourcent(array_sum($b) , array_sum($a))).'%', 1, 0, 'L');
                    $pdf->Ln(5);
                    $a = array();
                    $b = array();
                }

                $pdf->Ln(5);
                $pdf->SetFont('Arial','BU',13);
                $pdf->cell(190, 5, decode_fr('Tableau synthèse'), 0, 1, 'C');
                $pdf->SetFont('Arial','B',10);
                $a = array();
                $b = array();
                $pdf->Ln(3);
                $pdf->cell(190, 5, decode_fr('Annee Acad. : '.$annee_acad_deb), 0, 1, 'L');
                $pdf->Ln(1);


                $pdf->cell(70, 5, decode_fr('Faculté'), 1, 0, 'L');
                $pdf->cell(30, 5, decode_fr('Montant prévue'), 1, 0, 'L');
                $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                $pdf->cell(28, 5, decode_fr('Solde '), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('%'), 1, 0, 'L');
                $pdf->Ln(4);
                $pdf->SetFont('Arial','',10);

                $rf = ConnexionBdd::Connecter()->prepare("SELECT faculte, SUM(montant) as montant FROM affect_fac_frais WHERE annee_acad = ? GROUP BY faculte");
                $rf->execute(array($annee_acad_deb));

                while($d = $rf->fetch()){
                    $pay = ConnexionBdd::Connecter()->prepare("SELECT SUM(montant) as montant FROM payement_fac_frais WHERE annee_acad = ? AND faculte = ?");
                    $pay->execute(array($annee_acad_deb, $d['faculte']));
                    while($data = $pay->fetch()){
                        $pdf->Ln(1);
                        $pdf->cell(70, 5, decode_fr($d['faculte']), 1, 0, 'L');
                        $pdf->cell(30, 5, '$'.mm($d['montant']), 1, 0, 'L');
                        $pdf->cell(30, 5, '$'.mm($data['montant']), 1, 0, 'L');
                        $pdf->cell(28, 5, '$'.decode_fr(mm($d['montant'] - $data['montant'])), 1, 0, 'L');
                        $pdf->cell(15, 5, decode_fr(montant_restant_pourcent($data['montant'], $d['montant'])).'%', 1, 0, 'L'); 
                        $pdf->Ln(4);
                        $a[] = $d['montant'];
                        $b[] = $data['montant'];
                    }
                }
                $pdf->Ln(1);
                $pdf->cell(70, 5, decode_fr('Total'), 1, 0, 'L');
                $pdf->cell(30, 5, '$'.decode_fr(array_sum($a)), 1, 0, 'L');
                $pdf->cell(30, 5, '$'.decode_fr(array_sum($b)), 1, 0, 'L');
                $pdf->cell(28, 5, '$'.decode_fr(array_sum($a) - array_sum($b)), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr(montant_restant_pourcent(array_sum($b) , array_sum($a))).'%', 1, 0, 'L');
                $pdf->Ln(5);
            }else{
                $a = array();
                $b = array();
                $pdf->Ln(3);
                $pdf->cell(190, 5, decode_fr('Annee Acad. : '.$annee_acad_deb), 0, 1, 'L');
                $pdf->cell(190, 5, decode_fr('Faculte : '.$ff), 0, 1, 'L');
                $pdf->Ln(1);

                $pdf->cell(70, 5, decode_fr('Type de frais'), 1, 0, 'L');
                $pdf->cell(20, 5, decode_fr('Promotion'), 1, 0, 'L');
                $pdf->cell(30, 5, decode_fr('Montant prévue'), 1, 0, 'L');
                $pdf->cell(30, 5, decode_fr('Montant payé'), 1, 0, 'L');
                $pdf->cell(28, 5, decode_fr('Solde '), 1, 0, 'L');
                $pdf->cell(15, 5, decode_fr('%'), 1, 0, 'L');
                $pdf->Ln(4);

                if($_POST['type_frais'] == "Tous"){
                    $rf = ConnexionBdd::Connecter()->prepare("SELECT SUM(montant) as montant, type_frais, promotion FROM affect_fac_frais WHERE faculte = ? AND annee_acad = ? GROUP BY promotion, type_frais");
                    $rf->execute(array($ff, $annee_acad_deb));

                    while($d = $rf->fetch()){
                        $pay = ConnexionBdd::Connecter()->prepare("SELECT SUM(montant) as montant FROM payement_fac_frais WHERE faculte = ? AND annee_acad = ? AND promotion = ?");
                        $pay->execute(array($ff, $annee_acad_deb, $d['promotion']));
                        while($data = $pay->fetch()){
                            $pdf->Ln(1);
                            $pdf->cell(70, 5, decode_fr($d['type_frais']), 1, 0, 'L');
                            $pdf->cell(20, 5, decode_fr($d['promotion']), 1, 0, 'L');
                            $pdf->cell(30, 5, '$'.mm($d['montant']), 1, 0, 'L');
                            $pdf->cell(30, 5, '$'.mm($data['montant']), 1, 0, 'L');
                            $pdf->cell(28, 5, '$'.decode_fr(mm($d['montant'] - $data['montant'])), 1, 0, 'L');
                            $pdf->cell(15, 5, decode_fr(montant_restant_pourcent($data['montant'], $d['montant'])).'%', 1, 0, 'L'); 
                            $pdf->Ln(4);
                            $a[] = $d['montant'];
                            $b[] = $data['montant'];
                        }
                    }
                    $pdf->Ln(1);
                    $pdf->cell(70, 5, decode_fr('Total'), 1, 0, 'L');
                    $pdf->SetFillColor(0, 0, 0);
                    $pdf->cell(20, 5, '-', 1, 0, 'L', true);
                    $pdf->cell(30, 5, '$'.decode_fr(array_sum($a)), 1, 0, 'L');
                    $pdf->cell(30, 5, '$'.decode_fr(array_sum($b)), 1, 0, 'L');
                    $pdf->cell(28, 5, '$'.decode_fr(array_sum($a) - array_sum($b)), 1, 0, 'L');
                    $pdf->cell(15, 5, decode_fr(montant_restant_pourcent(array_sum($b) , array_sum($a))).'%', 1, 0, 'L');
                    $pdf->Ln(5);
                    $a = array();
                    $b = array();
                }else{
                    $rf = ConnexionBdd::Connecter()->prepare("SELECT SUM(montant) as montant, type_frais, promotion FROM affect_fac_frais WHERE type_frais = ? AND faculte = ? AND annee_acad = ? GROUP BY promotion, type_frais");
                    $rf->execute(array($_POST['type_frais'], $ff, $annee_acad_deb));

                    while($d = $rf->fetch()){
                        $pay = ConnexionBdd::Connecter()->prepare("SELECT SUM(montant) as montant FROM payement_fac_frais WHERE faculte = ? AND annee_acad = ? AND promotion = ?");
                        $pay->execute(array($ff, $annee_acad_deb, $d['promotion']));
                        while($data = $pay->fetch()){
                            $pdf->Ln(1);
                            $pdf->cell(70, 5, decode_fr($d['type_frais']), 1, 0, 'L');
                            $pdf->cell(20, 5, decode_fr($d['promotion']), 1, 0, 'L');
                            $pdf->cell(30, 5, '$'.mm($d['montant']), 1, 0, 'L');
                            $pdf->cell(30, 5, '$'.mm($data['montant']), 1, 0, 'L');
                            $pdf->cell(28, 5, '$'.decode_fr(mm($d['montant'] - $data['montant'])), 1, 0, 'L');
                            $pdf->cell(15, 5, decode_fr(montant_restant_pourcent($data['montant'], $d['montant'])).'%', 1, 0, 'L'); 
                            $pdf->Ln(4);
                            $a[] = $d['montant'];
                            $b[] = $data['montant'];
                        }
                    }
                    $pdf->Ln(1);
                    $pdf->cell(70, 5, decode_fr('Total'), 1, 0, 'L');
                    $pdf->SetFillColor(0, 0, 0);
                    $pdf->cell(20, 5, '-', 1, 0, 'L', true);
                    $pdf->cell(30, 5, '$'.decode_fr(array_sum($a)), 1, 0, 'L');
                    $pdf->cell(30, 5, '$'.decode_fr(array_sum($b)), 1, 0, 'L');
                    $pdf->cell(28, 5, '$'.decode_fr(array_sum($a) - array_sum($b)), 1, 0, 'L');
                    $pdf->cell(15, 5, decode_fr(montant_restant_pourcent(array_sum($b) , array_sum($a))).'%', 1, 0, 'L');
                    $pdf->Ln(5);
                    $a = array();
                    $b = array();
                }
            }
        }

        $pdf->Ln(15);
        $pdf->SetFont('Arial','',10);
	    $pdf->cell(300,20, decode_fr('par : '.$_SESSION['data']['noms'].'; le '.date('d/M/Y')),0,1,'C');
        $pdf->Output();
    }
?>
<!doctype html>
<html lang="fr">
    <head>
        <title><?=$p?></title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="Justin Micah" content="">
        <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
        <link rel="shortcut icon" href="../../images/UNIGOM_W260px.jpg" type="image/x-icon">
        <link href="css/sb-admin-2.min.css" rel="stylesheet">

        <style>
            .form-control{
                /* padding:0px !important; */
                height: 100% !important;
                
                width: 100%;
            }
            label, .link>button{
                /* font-size: 100%; */
                border:none;
            }
        </style>
    </head>
    <body  id="page-top">
    <div id="wrapper">
        <?php require_once './menu.php'; ?>
        <!-- End Sidebar -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content mt-4">
                <!-- menu user -->
                <?php require_once 'menu_user.php'; ?>
                <!-- main Content -->
                <div class="container-fluid" style="margin-top: -15px;">
                    <div class="card shadow ml-3 mt-2 m-0 p-0" style="width:42rem;">
                        <div class="card-body  ml-3 mr-3 mt-1 mb-2 p-0">
                            <h4 style="text-transform: capitalize;" class="text-center h6"><?=$p?></h4>
                            <form action="" method="POST" class="form-login" style="width:40rem;">
                                <div class="card m-0 p-0">
                                    <div class="card-body p-2 m-1">
                                        <div class="">
                                            <div class="row mt-1" >
                                                <div class="col-sm-12 col-md-4 col-lg-4">
                                                    <label for="">Faculté</label>
                                                </div>
                                                <div class="col-sm-12 col-md-9 col-lg-7">
                                                    <select class="form-control" name="fac_etudiant" id="fac_etudiant">
                                                        <?php
                                                            $sql = "SELECT DISTINCT fac FROM faculte ORDER BY fac";
                                                            $state = ConnexionBdd::Connecter()->query($sql);
                                                            while($d = $state->fetch()){
                                                                echo' 
                                                                    <option value="'.$d['fac'].'">'.$d['fac'].'</option>';
                                                            }
                                                        ?>
                                                    </select>
                                                </div>                                  
                                            </div>

                                            <div class="row mt-1" >
                                                <div class="col-sm-12 col-md-5 col-lg-4">
                                                    <label for="">Année Académique</label>
                                                </div>
                                                <div class="col-sm-12 col-md-9 col-lg-7">
                                                    <select class="form-control" name="annee_acad_deb" id="annee_acad_deb">
                                                        <?php
                                                            $sql = "SELECT * FROM annee_academique ORDER BY annee_acad DESC";
                                                            $state = ConnexionBdd::Connecter()->query($sql);
                                                            while($d = $state->fetch()){
                                                                echo' 
                                                                    <option value="'.$d['annee_acad'].'">'.$d['annee_acad'].'</option>';
                                                            }
                                                        ?>
                                                    </select>
                                                </div>                                  
                                            </div>

                                            <div class="row mt-1">
                                                <div class="col-sm-12 col-md-4 col-lg-4">
                                                    <label for="">Type de frais</label>
                                                </div>
                                                <div class="col-sm-12 col-md-7 col-lg-7">
                                                <select class="form-control" name="type_frais" id="type_frais">
                                                    <option value="Tous">Tous</option>
                                                    <option>_____________________________________</option>
                                                </select>
                                                </div>
                                            </div>
                                            <small id="error" class="text-danger"><?php if(isset($e) && !empty($e)){echo $e;}?></small>
                                        </div>
                                    </div>
                                    <div class="d-flex flex flex-column link ml-2 mt-4">
                                        <label class=""><b>Rapports facultaire</b></label>
                                        <!-- systhese par faculte -->
                                        <button type="submit" id="btn_fac" class="btn-link text-left mt-1" name="btn_fac" value="btn_fac">Voir le rapport par Faculté</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer -->
           <!-- footer -->
		  <?php include './footer.php';?>
        </div>
    </div>
    <script src="../../js/jquery-3.6.0.min.js"></script>
    <!-- fenetre modal pour la deconnexion-->
    <?php include_once './modal_decon.php';?>
    
    <script type="text/javascript">
        $("#pourcent_debut").change(function (e) { 
            e.preventDefault();
            if($("#pourcent_debut").val().length > 0){
                $("#pourcent_fin").attr('required', true);
            }else{
                $("#pourcent_fin").removeAttr('required');
            }
        });

        $("#pourcent_debut").keyup(function (e) { 
            if($("#pourcent_debut").val().length > 0){
                $("#pourcent_fin").attr('required', true);
            }else{
                $("#pourcent_fin").removeAttr('required');
            }
        });
        // pour le % max
        $("#pourcent_fin").change(function (e) { 
            e.preventDefault();
            if($("#pourcent_fin").val().length > 0){
                $("#pourcent_debut").attr('required', true);
            }else{
                $("#pourcent_debut").removeAttr('required');
            }
        });

        $("#pourcent_fin").keyup(function (e) { 
            if($("#pourcent_fin").val().length > 0){
                $("#pourcent_debut").attr('required', true);
            }else{
                $("#pourcent_debut").removeAttr('required');
            }
        });

        // pour les dates debit
        $("#date_debit").change(function (e) { 
            e.preventDefault();
            if($("#date_debit").val().length > 0){
                $("#date_fin").attr('required', true);
            }else{
                $("#date_fin").removeAttr('required');
            }
        });

        $("#date_debit").keyup(function (e) { 
            if($("#date_debit").val().length > 0){
                $("#date_fin").attr('required', true);
            }else{
                $("#date_fin").removeAttr('required');
            }
        });

        // pour les dates
        $("#date_fin").change(function (e) { 
            e.preventDefault();
            if($("#date_fin").val().length > 0){
                $("#date_debit").attr('required', true);
            }else{
                $("#date_debit").removeAttr('required');
            }
        });

        $("#date_fin").keyup(function (e) { 
            if($("#date_fin").val().length > 1){
                $("#date_debit").attr('required', true);
            }else{
                $("#date_debit").removeAttr('required');
            }
        });
    </script>
    <script type="text/javascript">
		// une fonction
		getMatricule();
		function getMatricule(){
			const data = {
				a:$("#annee_academique option:selected").text(),
				f:$("#faculte option:selected").text(),
				p:$("#promotion option:selected").text()
			};
			$.ajax({
				type: "GET",
				url: "Mes_classes/includes/select_mat.php",
				data: data,
				success: function (data) {
					if(data !=""){
						$("#matricule").text("<option>-- matricule --</option>");
						$("#matricule").append(data);
					}else{
						$("#matricule").text("<option>-- matricule --</option>");
					}
				},
				error: function(e){
					alert("Erreur de connexion...");
				}
			});
		}

		$("#annee_acad_deb").change(function (e) { 
			e.preventDefault();
			getMatricule();
		});

		$("#fac_etudiant").change(function (e) { 
			e.preventDefault();
			getMatricule();
		});
        function getMatricule(){
			const data = {
				a:$("#annee_acad_deb option:selected").text(),
				f:$("#fac_etudiant option:selected").text()
			};
			$.ajax({
				type: "GET",
				url: "../../includes/select_frais.php",
				data: data,
				success: function (data) {
					if(data !=""){
                        // $("#type_frais").empty();
						// $("#type_frais").html('option value="Tous">Tous</option>');
						$("#type_frais").append(data);
                        $("#error").html("");
					}else{
                        $("#error").html("");
                        $("#error").html("Désolé, Aucun résultat trouvé...");
					}
				},
				error: function(e){
					alert("Erreur de connexion...");
                    $("#error").html("");
                    $("#error").html("Erreur de connexion...");
				}
			});
		}
	</script>
    </body>
</html>