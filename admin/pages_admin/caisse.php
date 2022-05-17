<?php
    session_start();
    require_once '../../includes/ConnexionBdd.class.php';
    require_once '../../includes/verification.class.php';
    //verification des sessions
    require_once './sessions.php';
    $p = "SYTHESE";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Caisse et Rapports</title>

    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link rel="shortcut icon" href="../../images/ispt_kin.png" type="image/x-icon">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="js/DataTables/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="js/DataTables/css/dataTables.bootstrap4.min.css">
</head>
<body id="page-top">
    <div id="wrapper">
        <?php require_once 'menu.php'; ?>
        <!-- End Sidebar   #le menu droit de l  utiisateur# -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
				<!-- menu user -->
                <?php require_once 'menu_user.php'; ?>
                <!-- main Content -->
                <div class="container-fluid">
                    <div class="card shadow">
                        <div class="card-header bg-gray-200 d-flex flex" style="justify-content: space-between;">
                            <h6 class="m-0 font-weight-bolder text-primary text-center" style="text-transform: uppercase;">Statistique sythetique  de paiement PAR FACIULTE ET PAR PROMOTION</h6>
                            <div class="dropup">
                                <button class="btn btn-primary dropdown-toggle" type="button" id="triggerId" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                                <div class="dropdown-menu" aria-labelledby="triggerId">
                                    <h6 class="dropdown-header">RAPPORTS</h6>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item " href="rapport_pdf/stat.php" target="_blank"><i class="fa fa-print" aria-hidden="true"></i> Imprimer</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-2">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="m-0 font-weight-bold text-secondary" style="text-transform: none;">Payement par faculté</h6>
                                        </div>
                                        <div class="card-body p-0">
                                            <table class="table table-bordered table-hover">
                                                <thead class="font-weight-bold bg-gray-200">
                                                    <tr>
                                                        <td>Faculté</td>
                                                        <td>Montant prevu</td>
                                                        <td>Montant payé</td>
                                                        <td>   Solde  </td>
                                                        <td> % </td>
                                                    </tr>
                                                </thead>
                                                <tbody style="font-size:80%">
                                                    <!-- affichage -->
                                                    <?php
                                                        function f($v){
                                                            if(!empty($v)){
                                                                return 0;
                                                            }else{
                                                                return $v;
                                                            }
                                                        }
                                                        // on recupere le dernier annee academique
                                                        $a = ConnexionBdd::Connecter()->query("SELECT DISTINCT annee_acad FROM annee_academique ORDER BY id DESC LIMIT 1");
                                                        if($a->rowCount() > 0){
                                                            $data = $a->fetch();
                                                        }else{
                                                            $data['annee_acad'] = '';
                                                        }

                                                        // print_r($data);

                                                        $frais_par_fac = ConnexionBdd::Connecter()->query("SELECT faculte.fac, payement.faculte AS f, SUM(payement.montant) AS m FROM faculte LEFT JOIN payement ON faculte.fac = payement.faculte WHERE payement.annee_acad = '{$data['annee_acad']}' GROUP BY faculte.fac");
                                                        // $frais_par_fac->execute(array(f($data['annee_acad'])));

                                                        while($data_fac = $frais_par_fac->fetch()){
                                                            $mt = ConnexionBdd::Connecter()->prepare("SELECT SUM(montant) AS mf FROM affectation_frais WHERE faculte = ?");
                                                            $mt->execute(array($data_fac['fac']));

                                                            while($d = $mt->fetch()){
                                                                ?>
                                                                    <tr>
                                                                        <td><?=$data_fac['fac']?></td>
                                                                        <td><?=m_format($d['mf'])?></td>
                                                                        <td><?=m_format($data_fac['m'])?></td>
                                                                        <td><?=m_format($d['mf']-$data_fac['m'])?></td>
                                                                        <td><?=montant_restant_pourcent($data_fac['m'], $d['mf'])?>%</td>
                                                                    </tr>
                                                                <?php
                                                            }
                                                        }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="m-0 font-weight-bold text-secondary text-left" style="text-transform: none;">Paiement par promotion</h6>
                                        </div>
                                        <div class="card-body p-0">
                                            <table class="table table-bordered table-sm table-md table-hover">
                                                <thead class="font-weight-bold bg-secondary text-white">
                                                    <tr>
                                                        <td>Promotion</td>
                                                        <td>Montant prevu</td>
                                                        <td>Montant payé</td>
                                                        <td>  Solde  </td>
                                                        <td>%</td>
                                                    </tr>
                                                </thead>
                                                <tbody style="font-size:85%">
                                                    <!-- affichage -->
                                                    <?php
                                                        function ff($v){
                                                            if(!empty($v)){
                                                                return 0;
                                                            }else{
                                                                return $v;
                                                            }
                                                        }
                                                        // on recupere le dernier annee academique
                                                        $a = ConnexionBdd::Connecter()->query("SELECT DISTINCT annee_acad FROM annee_academique ORDER BY id DESC LIMIT 1");
                                                        if($a->rowCount() > 0){
                                                            $data = $a->fetch();
                                                        }else{
                                                            $data['annee_acad'] = '';
                                                        }

                                                        $mt = ConnexionBdd::Connecter()->prepare("SELECT promotion, SUM(montant) as m FROM affectation_frais WHERE annee_acad = ? GROUP BY promotion");
                                                        $mt->execute(array($data['annee_acad']));

                                                        while($data = $mt->fetch()){
                                                            // print_r($data);
                                                            // on recupere le dernier annee academique
                                                            $a = ConnexionBdd::Connecter()->query("SELECT DISTINCT annee_acad FROM annee_academique ORDER BY id DESC LIMIT 1");
                                                            $data_ = $a->fetch();

                                                            $sql = "SELECT sum(montant) AS montant FROM payement WHERE promotion = ? AND annee_acad = ?";
                                                            $frais_fac = ConnexionBdd::Connecter()->prepare($sql);
                                                            $frais_fac->execute(array($data['promotion'], $data_['annee_acad']));
                                                            while($d = $frais_fac->fetch())
                                                                {
                                                                ?>
                                                                    <tr>
                                                                        <td><?=$data['promotion']?></td>
                                                                        <td><?=m_format($data['m'])?></td>
                                                                        <td><?=m_format($d['montant'])?></td>
                                                                        <td><?=m_format($data['m'] - $d['montant'])?></td>
                                                                        <td><?=montant_restant_p($d['montant'], $data['m'])?>%</td>
                                                                    </tr>
                                                                <?php
                                                            }
                                                        }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- filtre par an, date, type de frais -->
                    <div  class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12 m-0 p-0">
                            <div class="card shadow ml-3 mr-3 mt-3">
                                <div class="card-header d-flex flex" style="text-transform: uppercase;justify-content: space-between;">
                                    <h6  class="m-0 font-weight-bolder text-primary text-center"><button id="btn_h" title="afficher les contenues du tableau"><i class="fa fa-eye" aria-hidden="true"></i></button> Payement par Type de frais, faculte et par promotion</h6>
                                    <a href="./rapport_pdf/rapport_type_f_f_p.php" target="_blank" class="btn btn-primary" style="text-transform: none;"><i class="fa fa-download" aria-hidden="true"></i> Génerer un rapport</a>
                                </div>
                                <div class="card-body" style="display: non" id="table_sh">
                                    <table class="table table-bordered table-hover display" style="width:100%" id="t34">
                                        <thead class="bg-dark text-white">
                                            <tr>
                                                <!-- <th>#ID</th> -->
                                                <th>Faculte</th>
                                                <th>Promotion</th>
                                                <th>Type de frais</th>
                                                <th>Montant prevu</th>
                                                <th>Montant payé</th>
                                                <th>solde</th>
                                                <th>Progression</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                    $sql = "SELECT faculte, promotion, type_frais, SUM(montant) as mt FROM affectation_frais WHERE annee_acad  = ? GROUP BY faculte, type_frais";

                                                    // on recupere le dernier annee academique
                                                    $a = ConnexionBdd::Connecter()->query("SELECT DISTINCT annee_acad FROM annee_academique ORDER BY id DESC LIMIT 1");
                                                    if($a->rowCount() > 0){
                                                        $data = $a->fetch();
                                                    }else{
                                                        $data['annee_acad'] = '';
                                                    }
                                                    
                                                    $all = ConnexionBdd::Connecter()->prepare($sql);
                                                    $all->execute(array($data['annee_acad']));

                                                    while ($data = $all->fetch()){
                                                        // on recupere le dernier annee academique
                                                        $a = ConnexionBdd::Connecter()->query("SELECT DISTINCT annee_acad FROM annee_academique ORDER BY id DESC LIMIT 1");
                                                        $data_ = $a->fetch();

                                                        $sql_2 = "SELECT SUM(montant) as mp FROM payement WHERE faculte = ? AND promotion = ? AND type_frais = ? AND annee_acad = ?";
                                                        $sql_2 = ConnexionBdd::Connecter()->prepare($sql_2);
                                                        $sql_2->execute(array($data['faculte'], $data['promotion'], $data['type_frais'], $data_['annee_acad'])); 

                                                        while($d = $sql_2->fetch()){
                                                            ?>
                                                                <tr>
                                                                    <td><?=$data['faculte']?></td>
                                                                    <td><?=$data['promotion']?></td>
                                                                    <td><?=decode_fr($data['type_frais'])?></td>
                                                                    <td><?=m_format($data['mt'])?></td>
                                                                    <td><?=m_format($d['mp'])?></td>
                                                                    <td><?=m_format($data['mt']-$d['mp'])?></td>
                                                                    <td>
                                                                        <div class="progress">
                                                                            <div class="progress-bar bg-primary" role="progressbar" style="width: <?=montant_restant_p($d['mp'], $data['mt'])?>%;"
                                                                                aria-valuenow="<?=montant_restant_p($d['mp'], $data['mt'])?>" aria-valuemin="0" aria-valuemax="100"><?=montant_restant_p($d['mp'], $data['mt'])?>%</div>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            <?php
                                                        }
                                                    }
                                                ?>
                                            </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="card shadow mt-3">
                                <div class="card-header m-0 font-weight-bolder text-primary text-center" style="text-transform: uppercase;"> statistique de payement et Suivi des dettes envers l'université</div>
                                <div class="card-body p-3">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-lg-12">
                                            <div class="card shadow">
                                                <div class="card-header bg-gray-200 d-flex flex" style="justify-content: space-between;">  
                                                    <h5 class="m-0 font-weight-bolder text-primary text-center"><button id="btn_ht" title="afficher les contenues du tableau"><i class="fa fa-eye" aria-hidden="true"></i></button> Suivi des dettes envers l'université</h5>
                                                    <!-- dropdown -->
                                                    <div class="dropup">
                                                        <button class="btn btn-primary dropdown-toggle" type="button" id="triggerId" data-toggle="dropdown" aria-haspopup="true"
                                                                aria-expanded="false">Actions</button>
                                                        <div class="dropdown-menu" aria-labelledby="triggerId">
                                                            <h6 class="dropdown-header">Rechercher</h6>
                                                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#mod_search">Par etudiants</a>
                                                            <div class="dropdown-divider"></div>
                                                            <a class="dropdown-item " href="./Rapport_periodique.php?Ff=<?=VerificationUser::verif($_SESSION['data']['fonction'])?>&i=<?=VerificationUser::verif($_SESSION['data']['id'])?>" style=";<?=restruct_r_bc()?>">Rapport périodique</a>
                                                            <div class="dropdown-divider"></div>
                                                            <a class="dropdown-item " href="FraisRapport.php?Ff=<?=VerificationUser::verif($_SESSION['data']['fonction'])?>&i=<?=VerificationUser::verif($_SESSION['data']['id'])?>">Frais Rapports</a>
                                                            <div class="dropdown-divider"></div>
                                                            <!-- payement de chaque etudiants -->
                                                            <button class="dropdown-item" data-toggle="modal" data-target="#modal_all_students">payement de chaque etudiant</button>
                                                            <div class="dropdown-divider"></div>
                                                            <a class="dropdown-item " href="rapport_pdf/rapport_type_f_f_p.php" target="_blank"><i class="fa fa-print" aria-hidden="true"></i> Imprimer tous les frais</a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body p-1" style="display: non;">
                                                    <table class="table table-bordered table-hover" id="t3">
                                                        <thead class="bg- text-secondary">
                                                            <tr class="">
                                                                <!-- <th>#ID</th> -->
                                                                <th>Matricule</th>
                                                                <th>Noms</th>
                                                                <th>Faculte</th>
                                                                <th>Promotion</th>
                                                                <th>Montant prevu</th>
                                                                <th>Montant payer</th>
                                                                <th>Solde</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="list_all">
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- on cache cette ligne temporelement -->
                                    <div class="row" style="display:none">
                                        <div class="col-sm-12 col-md-12 col-lg-12 m-0 p-0">
                                            <div class="card shadow m-0 mt-3">
                                                <div class="card-header" style="text-transform: uppercase;">
                                                    <h6  class="m-0 font-weight-bolder text-primary text-center">Payement de frais par faculte et par promotion</h6>
                                                </div>
                                                <div class="card-body">
                                                    <table class="table table-bordered table-hover" id="t31">
                                                        <thead class="bg-dark text-white">
                                                            <tr>
                                                                <th>Faculte</th>
                                                                <th>Promotion</th>
                                                                <th>Montant</th>
                                                                <th>Montant Restant</th>
                                                                <th>   %   </th>
                                                                <th>Progression</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                    $frais_fac = ConnexionBdd::Connecter()->query("SELECT faculte, promotion, SUM(montant) AS montant FROM payement GROUP BY faculte, promotion");

                                                                    while($data = $frais_fac->fetch()){
                                                                        $frais_prev = ConnexionBdd::Connecter()->prepare("SELECT SUM(montant) AS montants FROM affectation_frais WHERE faculte = ? AND promotion = ? GROUP BY faculte");
                                                                        $frais_prev->execute(array($data['faculte'], $data['promotion']));

                                                                        while($data_ = $frais_prev->fetch()){
                                                                            ?>
                                                                                <tr>
                                                                                    <td><?=$data['faculte']?></td>
                                                                                    <td><?=$data['promotion']?></td>
                                                                                    <td><?=$data['montant']?></td>
                                                                                    <td><?=$data_['montants']?></td>
                                                                                    <td><?=montant_restant_p($data['montant'], $data_['montants'])?></td>
                                                                                    <td>
                                                                                        <div class="progress">
                                                                                            <div class="progress-bar bg-primary" role="progressbar" style="width: <?=montant_restant_p($data['montant'], $data_['montants'])?>%;"
                                                                                                aria-valuenow="<?=montant_restant_p($data['montant'], $data_['montants'])?>" aria-valuemin="0" aria-valuemax="100"><?=montant_restant_p($data['montant'], $data_['montants'])?>%</div>
                                                                                        </div>
                                                                                    </td>
                                                                                </tr>
                                                                            <?php
                                                                        }
                                                                    }
                                                                ?>
                                                            </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
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

    <!-- modal pour chercher un etudiant -->
    <div class="modal fade" id="mod_search" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Historique de payement d'un etudiant</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" class="" id="form_search_etud">
                        <div class="form-group">
                            <input type="text" name="mat" id="mat" value="" placeholder="matricule" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <select class="form-control" name="promotion" id="promotion" required>
                                <option value="">-promotion-</option>
                                <?php
                                    $verif = ConnexionBdd::Connecter()->query("SELECT * FROM etudiants_inscrits GROUP BY promotion");
                                    while($data = $verif->fetch()){
                                        ?>
                                            <option value="<?=$data['promotion']?>"><?=$data['promotion']?></option>
                                        <?php
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Faculte</label>
                            <select class="form-control" name="fac" id="fac" required>
                                <option value="">-Faculte-</option>
                                <?php
                                    $verif = ConnexionBdd::Connecter()->query("SELECT * FROM etudiants_inscrits GROUP BY fac");
                                    while($data = $verif->fetch()){
                                        ?>
                                            <option value="<?=utf8_decode($data['fac'])?>"><?=utf8_decode($data['fac'])?></option>
                                        <?php
                                    }
                                ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <div class="form-check">
                              <label class="form-check-label">
                                <input type="checkbox" class="form-check-input" name="ck_ck_all" id="ck_ck_all" value="All" checked>Tous les type de frais
                              </label>
                            </div>
                            <select class="form-control" name="poste_frais" id="poste_frais" multiple>
                                <?php
                                    $lpd = ConnexionBdd::Connecter()->query("SELECT * FROM prevision_frais GROUP BY type_frais DESC");
                                    while($data = $lpd->fetch()){
                                        ?>
                                            <option value="<?=utf8_decode($data['type_frais'])?>"><?=utf8_decode($data['type_frais']).','?>
                                            </option>
                                        <?php
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <select class="form-control" name="annee_acad" id="annee_acad">
                                <!-- <option value="">Annee academique</option> -->
                                <label for="">Annee Academique</label>
                                <optgroup></optgroup>
                                <hr>
                                <?php
                                    $lpd = ConnexionBdd::Connecter()->query("SELECT * FROM annee_academique GROUP BY annee_acad DESC");
                                    while($data = $lpd->fetch()){
                                        ?>
                                            <option value="<?=$data['annee_acad']?>"><?=$data['annee_acad']?></option>
                                        <?php
                                    }
                                ?>
                            </select>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="submit" value="Checher" class="form-control  btn btn-primary btn-sm" name="btn_s"><i class="fa fa-search" aria-hidden="true"></i> Checrher</button>
                        </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- recherche par date de coupure, fac  et promotion -->
    <div class="modal fade" id="mod_search_all" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Payement par faculte, Promotion, date et par type frais</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" class="" id="form_search_date_pour">
                        <div class="form-group">
                            <select class="form-control" name="fac" id="fac_ss" required required>
                                <option value="All">-All-</option>
                                <?php
                                    $verif = ConnexionBdd::Connecter()->query("SELECT * FROM etudiants_inscrits GROUP BY fac");
                                    while($data = $verif->fetch()){
                                        ?>
                                            <option value="<?=$data['fac']?>"><?=$data['fac']?></option>
                                        <?php
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <select class="custom-select" name="promotion" id="promotion_ss" required>
                                <option value="All">-All-</option>
                                <?php
                                    $verif = ConnexionBdd::Connecter()->query("SELECT * FROM etudiants_inscrits GROUP BY promotion");
                                    while($data = $verif->fetch()){
                                        ?>
                                            <option value="<?=$data['promotion']?>"><?=$data['promotion']?></option>
                                        <?php
                                    }
                                ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <select class="form-control" name="annee_acad" id="annee_acad_s" required>
                                <!-- <option value="">Annee academique</option> -->
                                <label for="">Annee academique</label>
                                <optgroup></optgroup>
                                <hr>
                                <?php
                                    $lpd = ConnexionBdd::Connecter()->query("SELECT * FROM annee_academique GROUP BY annee_acad DESC");
                                    while($data = $lpd->fetch()){
                                        ?>
                                            <option value="<?=$data['annee_acad']?>"><?=$data['annee_acad']?></option>
                                        <?php
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" name="montant_search" id="montant_search" placeholder="montant" required>
                        </div>
                        <div class="form-group d-flex flex" style="justify-content: space-between;">
                            <input type="date" name="date_1_ss" class="form-control" style="width: 47%;" id="date_1_ss" required>
                            <input type="date" name="date_2_ss" class="form-control" style="width: 47%;" id="date_2" required>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="submit" value="Checher" class="form-control  btn btn-primary btn-sm" name="btn_s"><i class="fa fa-search" aria-hidden="true"></i> Checrher</button>
                        </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- payement par type de frais, dfate de coupure, montant minimoum, montant maximoun -->
    <div class="modal fade" id="Payement_t_d_m_m" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true" style="overflow-y: scroll;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Frais Rapports</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" id="payement_tf_d_m_m">
                        <!-- liste des annees academique -->
                        <div class="form-group">
                            <label for="">Annee academique</label>
                            <select class="form-control" name="annee_acad_search_s" id="annee_acad_search_s">
                                <?php
                                    $lpd = ConnexionBdd::Connecter()->query("SELECT * FROM annee_academique GROUP BY annee_acad DESC");
                                    while($data = $lpd->fetch()){
                                        ?>
                                            <option value="<?=$data['annee_acad']?>"><?=$data['annee_acad']?></option>
                                        <?php
                                    }
                                ?>
                            </select>
                        </div>
                        
                        <!-- list des promotions -->
                        <div class="form-group">
                            <!-- <label for="">Toute les promotions</label> -->
                            <select class="form-control" name="promotion_search_s" id="promotion_search_s">
                                <option value="All">Toute les promotions</option>
                                <?php
                                    $lpd = ConnexionBdd::Connecter()->query("SELECT * FROM annee_academique GROUP BY annee_acad DESC");
                                    $data = $lpd->fetch();
                                    $lpd = ConnexionBdd::Connecter()->query("SELECT promotion FROM etudiants_inscrits  WHERE annee_academique = '{$data['annee_acad']}' GROUP BY promotion ASC");
                                    while($data = $lpd->fetch()){
                                        ?>
                                            <option value="<?=$data['promotion']?>"><?=$data['promotion']?></option>
                                        <?php
                                    }
                                ?>
                            </select>
                        </div>

                        <!-- listes des facultes -->
                        <div class="form-group">
                            <!-- <label for=""></label> -->
                            <select class="form-control" name="facul_search_s" id="facul_search_s">
                                <option value="All">Toute les facultés</option>
                                <?php
                                    $lpd = ConnexionBdd::Connecter()->query("SELECT * FROM faculte GROUP BY fac ASC");
                                    while($data = $lpd->fetch()){
                                        ?>
                                            <option value="<?=$data['fac']?>"><?=$data['fac']?></option>
                                        <?php
                                    }
                                ?>
                            </select>
                        </div>
                        
                        <!-- type de frais -->
                        <div class="form-group">
                            <label for="">Type de frais</label>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input" name="all_type_frais" id="all_type_frais" checked> Tous les types des frais
                                </label>
                            </div>
                            <select class="custom-select" name="poste_search_lst" id="poste_search_lst" multiple style="height:200px;">
                                <?php
                                    $lpd = ConnexionBdd::Connecter()->query("SELECT * FROM prevision_frais GROUP BY type_frais DESC");
                                    while($data = $lpd->fetch()){
                                        ?>
                                            <option value="<?=$data['type_frais'].','?>"><?=$data['type_frais'].','?></option>
                                        <?php
                                    }
                                ?>
                            </select>
                        </div>
                        <!-- la date de coupure -->
                        <div class="form-group">
                            <div class="" id="f_date">
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" name="ch_ch_date" id="ch_ch_date">Par date de coupure
                                    </label>
                                </div>
                            </div>
                            <div id="form_date" class="d-flex flex mt-1" style="justify-content: space-between;">
                                <input type="date" name="date_debut" id="date_debut" style="width:48%" class="form-control">
                                <input type="date" name="date_fin" id="date_fin" style="width:48%" class="form-control">
                            </div>
                        </div>

                        <!-- le montant min -->
                        <div class="form-group">
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input" name="ch_m_min" id="ch_m_min" > % minimum 
                                </label>
                            </div>
                        </div>

                        <div id="" class="d-flex flex mt-1" style="justify-content: space-between;">
                            <input type="number" name="montant_minimum " id="montant_minimum" style="width:100%" class="form-control" placeholder="montant minimoum">
                        </div>

                        <div class="form-group">
                            <div class="" id="f_date">
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" name="ch_ch_m_max" id="ch_ch_m_max"> % maximum 
                                    </label>
                                </div>
                            </div>
                            <div id="form_m_max" class="d-flex flex mt-1" style="justify-content: space-between;">
                                <input type="number" name="montant_maximum" id="montant_maximum" style="width:100%" class="form-control" placeholder="montant maximum">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
                        </div>
                        <small id="helpId_error_" class="form-text text-muted"></small>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- recherche par faculte -->
    <div class="modal fade" id="mod_search_fac_prom" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Payement par faculte ET Promotion</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" class="" id="form_search_fac_prom_s">
                        <div class="form-group">
                            <label for="">Faculte</label>
                            <select class="form-control" name="fac" id="fac_search_xy" required>
                                <option value="">-Faculte-</option>
                                <?php
                                    $verif = ConnexionBdd::Connecter()->query("SELECT * FROM etudiants_inscrits GROUP BY fac");
                                    while($data = $verif->fetch()){
                                        ?>
                                            <option value="<?=$data['fac']?>"><?=$data['fac']?></option>
                                        <?php
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <select class="custom-select" name="promotion" id="promotion_search_xy" required>
                                <option value="">-promotion-</option>
                                <?php
                                    $verif = ConnexionBdd::Connecter()->query("SELECT * FROM etudiants_inscrits GROUP BY promotion");
                                    while($data = $verif->fetch()){
                                        ?>
                                            <option value="<?=$data['promotion']?>"><?=$data['promotion']?></option>
                                        <?php
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <select class="custom-select" name="poste" id="poste_search_xy">
                                <option value="All">All</option>
                                <optgroup></optgroup>
                                <hr>
                                <?php
                                    $lpd = ConnexionBdd::Connecter()->query("SELECT * FROM prevision_frais GROUP BY type_frais");
                                    while($data = $lpd->fetch()){
                                        ?>
                                            <option value="<?=$data['type_frais']?>"><?=$data['type_frais']?></option>
                                        <?php
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <select class="form-control" name="annee_acad" id="annee_acad_search_xy">
                                <!-- <option value="">Annee academique</option> -->
                                <label for="">Annee academique</label>
                                <optgroup></optgroup>
                                <hr>
                                <?php
                                    $lpd = ConnexionBdd::Connecter()->query("SELECT * FROM annee_academique GROUP BY annee_acad DESC");
                                    while($data = $lpd->fetch()){
                                        ?>
                                            <option value="<?=$data['annee_acad']?>"><?=$data['annee_acad']?></option>
                                        <?php
                                    }
                                ?>
                            </select>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="submit" value="Checher" class="form-control  btn btn-primary btn-sm" name="btn_s"><i class="fa fa-search" aria-hidden="true"></i> Search </button>
                        </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- rapport de payement de chaque etudiants -->
    <div class="modal fade" id="modal_all_students" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                    <div class="modal-header">
                            <h5 class="modal-title">Rapport de payement pour chaque etudiant</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                        </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <form action="rapport_pdf/payement_par_etudiants.php" method="post" id="s">
                            <p>Selectionner une annee academique</p>
                            <div class="form-group">
                            <select class="form-control" name="annee_acad" id="annee_acad_search_all">
                                <hr>
                                <?php
                                    $lpd = ConnexionBdd::Connecter()->query("SELECT * FROM annee_academique GROUP BY annee_acad DESC");
                                    while($data = $lpd->fetch()){
                                        ?>
                                            <option value="<?=$data['annee_acad']?>"><?=$data['annee_acad']?></option>
                                        <?php
                                    }
                                ?>
                            </select>
                            <button  class="btn btn-primary btn-block mt-2"type="submit">Envoyer</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- fenetre modal pour la deconnexion-->
    <?php include_once './modal_decon.php';?>

    <script src="js/mes_scripts/caisse.js"></script>
    <script type="text/javascript">
        // les checkboxies 
        $("#montant_minimum").hide();
        $("#form_date").hide();
        $("form #form_m_max").hide();

        $("#poste_frais").change(function (e) { 
            e.preventDefault();
            var a = Array();
            a.push($("#poste_frais option:selected").text());
            // a.join(', ');

            // alert(a);
        });

        // la case a cocher pour tous les types des frais 
        if($("#ck_ck_all:checked").val()){
            $("#poste_frais").slideUp();
        }else{
            $("#poste_frais").slideDown();
        }

        // tous les type de frais
        if($('#all_type_frais:checked').val()){
            $("#poste_search_lst").slideUp(500);
            // $("#poste_search").removeAttr('required');
        }else{
            $("#poste_search_lst").slideDown();
            // $("#poste_search").attr('required', true);
        }

        // $("#all_type_frais").click(function(){
        //     if($('#all_type_frais:checked').val()){
        //         $("#poste_search_lst").hide();
        //         // $("#poste_search_lst").removeAttr('required');
        //     }else{
        //         $("#poste_search_lst").slideDown(500);
        //     }
        // });

        // click
        $("#ck_ck_all").click(function(){
            // alert($('#ck_ck_all:checked').val());
            if($('#ck_ck_all:checked').val()){
                $("#poste_frais").hide();
            }else{
                $("#poste_frais").slideDown(500);
            }
        });

        // 
        // 

        if($('#ch_ch_m_max:checked').val()){
            $("#montant_maximum").slideDown();
        }else{
            $("#montant_maximum").hide(500);
        }

        if($('#ch_ch_date:checked').val()){
            $("#date_debut").slideDown();
            $("#date_fin").slideDown();
        }else{
            $("#date_debut").hide(500);
            $("#date_fin").hide(500);
        }

        if($('#ch_m_min:checked').val()){
            $("#montant_minimum ").slideDown();
        }else{
            $("#montant_minimum ").hide(500);
        }

        // $("#ch_ch_m_max").click(function(){
        //     if($('#ch_ch_m_max:checked').val()){
        //         $("#montant_maximum").slideDown();
        //     }else{
        //         $("#montant_maximum").hide(500);
        //     }
        // });
            
        $("#ch_m_min").click(function(){
            if($('#ch_m_min:checked').val()){
                $("#montant_minimum ").slideDown();
            }else{
                $("#montant_minimum ").hide(500);
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            $("#s").submit(function (e) { 
                e.preventDefault();
                v = $("#annee_acad_search_all option:selected").text();
                window.location.href = 'rapport_pdf/payement_par_etudiants.php?a='+v;
            });

            $("#ch_ch_date").change(function (e) { 
                e.preventDefault();
                if($("#ch_ch_date").is(":checked")){
                    // alert("cocher la date ok");
                    $(this).val("date_ok");
                    $("#date_debut").attr('required', true);
                    $("#date_fin").attr('required', true);
                    $("#date_debut").slideDown();
                    $("#date_fin").slideDown();
                }else{
                    // alert("decocher");
                    $(this).val("");
                    $("#date_debut").removeAttr('required');
                    $("#date_fin").removeAttr('required');
                    $("#date_debut").slideUp();
                    $("#date_fin").slideUp();
                    $("#date_debut").val('');
                    $("#date_fin").val('');
                }
            });

            $("#all_type_frais").change(function(e){
                e.preventDefault();
                if($(this).is(":checked")){
                    $("#all_type_frais").val("type_fr_p");
                    $("#poste_search_lst").removeAttr('required');
                    $("#poste_search_lst").hide(5000);
                }else{
                    $("#all_type_frais").val("___");
                    $("#poste_search_lst").attr('required', true);
                    $("#poste_search_lst").slideDown(5000);
                    $("#poste_search_lst").val('');
                }
            });

            $('#ch_m_min').change(function (e) { 
                e.preventDefault();
                if($(this).is(":checked")){
                    $("#montant_minimum").slideDown();
                    $("#montant_minimum").attr('required', true);
                    $("#ch_m_min").val("_min_");
                }else{
                    $("#montant_minimum").hide();
                    $("#montant_minimum").removeAttr('required');
                    $("#ch_m_min").val("");
                    $("#montant_minimum").val('');
                }
            });

            $("#ch_ch_m_max").change(function (e) { 
                e.preventDefault();
                if($(this).is(":checked")){
                    $("#montant_maximum").slideDown();
                    $("#montant_maximum").attr('required', true);
                    $("#ch_ch_m_max").val("_max_");
                }else{
                    $("#montant_maximum").hide(500);
                    $("#montant_maximum").removeAttr('required');
                    $("#ch_ch_m_max").val("");
                    $("#montant_maximum").val('');
                }
            });
        });
    </script>
    <script>   
        $('#btn_h').click(function (e) { 
            e.preventDefault();
            $('#table_sh').slideToggle(1, function(){});
        });

        $('#btn_ht').click(function (e) { 
            e.preventDefault();
            $('#t3').slideToggle(1, function(){});
        });
    </script>
    <script>
        $('#table_sh').slideUp('slow', function(){});
        $('#t3').slideUp('slow', function(){});
    </script>
</body>

</html>