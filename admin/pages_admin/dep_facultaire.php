<?php
    session_start();
    require_once '../../includes/ConnexionBdd.class.php';
    require_once '../../includes/verification.class.php';
    //verification des sessions
    require_once './sessions.php';
    $p = "Dépense facultaire";
    $post_dep = "SELECT * FROM depense_facultaire WHERE faculte = ?";
    $params = array($rr['access']);

    $_SESSION['req_rapport'] = $post_dep;
    $_SESSION['params'] = $params ;

    // selection des facultes
    $lf = array();
    $fac = connexionBdd::Connecter()->query("SELECT DISTINCT * FROM sections GROUP BY section");
    while($l = $fac->fetch()){
        $lf[] =  $l['section'];  
    }

    // if(!in_array($rr['access'], $lf)){
    //     header("location: ../index.php");
    //     exit();
    // }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="../../images/ispt_kin.png" type="image/x-icon">

    <title>Poste de depense facultaires</title>

    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
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
                <div class="container-fluid">
                    <div class="row mt-1">
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xs-12 m-0">
                            <div class="card shadow m-0" style="font-size: medium;">
                                <div class="card-header d-flex" style="justify-content: space-between;">
                                    <h6 class="m-0 font-weight-bold text-primary" style="text-transform: uppercase;">Poste des depenses facultaires</h6>
                                    <div class="dropdown open">
                                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="triggerId" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                                        <div class="dropdown-menu" aria-labelledby="triggerId">
                                            <a class="dropdown-item" data-toggle="modal" href="#myModal_1"><i class="fa fa-plus" aria-hidden="true"></i> Ajouter un poste de dépense</a>

                                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#transactioN-date"><i class="fa fa-calendar" aria-hidden="true"></i> Transactions par date</a>

                                            <a class="dropdown-item" href="./rapport_pdf/rap_pd_fac.php"></i><i class="fa fa-print" aria-hidden="true"></i> Imprimer les postes de dépense</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body" id="f_poste_depense">
                                    <table class="table table-bordered table-hover mt-1" id="t_poste_depense|">
                                        <thead>
                                            <tr>
                                                <th>#ID</th>
                                                <th>Poste</th>
                                                <th>Faculte</th>
                                                <th>Montant Prevu</th>
                                                <th>Depenses</th>
                                                <th> % </th>
                                                <th>Progression</th>
                                                <th>#Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbody_poste">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- les transactions -->
                    <div class="row mt-3">
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xs-12 m-0">
                            <div class="card shadow m-0">
                                <div class="card-header">
                                    <p class="m-0 font-weight-bold text-primary">Transactions</p>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered table-hover" id="t_poste_depense_t">
                                        <thead class="bg-gray-200">
                                            <tr>
                                                <th>#ID</th>
                                                <th>Poste</th>
                                                <th>Section</th>
                                                <th>Montant</th>
                                                <th>Date</th>
                                                <th>Motif</th>
                                                <td> ### </td>
                                            </tr>
                                        </thead>
                                        <tbody class="text-secondary" id="tbody_transaction">
                                            <?php
                                                // die($rr['access']);
                                                $sql = "SELECT
                                                            transaction_pdf.id_transaction as id,
                                                            transaction_pdf.montant as montant_trans ,
                                                            transaction_pdf.motif,
                                                            transaction_pdf.date_transaction as date_trans,
                                                            depense_facultaire.id_pdf,
                                                            depense_facultaire.poste as poste_df,
                                                            sections.id_section,
                                                            sections.section as faculte,
                                                            annee_acad.id_annee,
                                                            annee_acad.annee_acad
                                                        FROM
                                                            transaction_pdf
                                                        LEFT JOIN depense_facultaire ON transaction_pdf.id_pdf = depense_facultaire.id_pdf
                                                        LEFT JOIN sections ON transaction_pdf.id_section = sections.id_section
                                                        LEFT JOIN annee_acad ON transaction_pdf.id_annee = annee_acad.id_annee
                                                        WHERE
                                                            transaction_pdf.id_annee = 1 AND transaction_pdf.id_section = 1";
                                                $p = array($_SESSION['data']['access']); 
                                                $pd = ConnexionBdd::Connecter()->prepare($sql);
                                                $pd->execute($p); 
                                            //  	 	 	 	 	 	
                                                while($data = $pd->fetch()){
                                                    ?>
                                                        <tr>
                                                            <td id="trans_id"><?=$data['id']?></td>
                                                            <td id="trans_poste"><?=$data['poste_df']?></td>
                                                            <td id="trans_date_t"><?=$data['faculte']?></td>
                                                            <td id="trans_montant"><?=$data['montant_trans']?></td>
                                                            <td id="trans_motif"><?=date("d/m/Y", strtotime($data['date_trans']))?></td>
                                                            <td id=""><?=$data['motif']?></td>
                                                            <td>
                                                                <button type="button" class="btn btn-danger btn-sm" alt="suppresion de la transaction" id="btn_del_trans" data-toggle="modal" data-target="#del_trans_pd">Supprimer</button>
                                                            </td>
                                                        </tr>
                                                    <?php
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="card-footer text-muted"></div>
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

    <!-- fenetre modal pour ajouter un poste de depense facultaires-->
    <div class="modal fade" id="myModal_1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="exampleModalLabel">Ajout des postes de depenses facultaires</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="" id="ajout_poste_depense">
                    <div class="modal-body">
                        <div class="form-group">
                          <input type="text" class="form-control" name="p_depense" id="p_depense" aria-describedby="helpId" placeholder="Poste de dépense" required>
                        </div>
                        <div class="form-group">
                            <select name="faculte" id="faculte" class="form-control" required>
                                <?php
                                    $nn = ConnexionBdd::Connecter()->prepare("SELECT id_section, section FROM sections WHERE id_section = ?");
                                    $nn->execute(array(VerificationUser::verif($rr['access'])));
                                    while($data = $nn->fetch()){
                                        ?>
                                            <option value="<?=$data['id_section']?>"><?=$data['section']?></option>
                                        <?php
                                    }
                                ?>
                            </select>
                        </div>
                         <!-- promotion -->
                         <div class="form-group" style="display:none">
                            <select name="promotion" id="promotion" class="form-control" required>
                                <option value="-">-</option>
                            </select>
                        </div>
                        <!-- annee academique -->
                        <div class="form-group">
                            <select name="annee_acad" id="annee_acad" class="form-control">
                                <?php
                                    $list = ConnexionBdd::Connecter()->query("SELECT * FROM `annee_acad` ORDER BY id_annee DESC LIMIT 1");
                                    while($data = $list->fetch()){
                                        ?>
                                            <option value="<?=$data['id_annee']?>"><?=$data['annee_acad']?></option>
                                        <?php
                                    }
                                ?>
                            </select>
                        </div>
                        <!-- montant -->
                        <div class="form-group">
                          <input type="text" class="form-control" name="a_montant" id="a_montant" aria-describedby="helpId" placeholder="Montant" min="0" required>
                        </div>
                        <div for="" id="r"><small id="error_pf"></small></div>
                    </div>
                    
                    <div class="modal-footer">
                        <button class="btn btn-danger" type="button" data-dismiss="modal">Annuler et fermer</button>
                        <button class="btn btn-primary" type="submit" id="btn_add">
                            <i class="fa fa-plus" aria-hidden="true"> Ajouter</i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Fenetere modal pour la transaction -->
    <div class="modal fade" id="transactions" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Transaction sur le poste de depense facultare</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="btn btn-danger">x</span>
                    </button>
                </div>
                <form action="" id="form_transaction_pf" method="post">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="">date</label>
                            <input type="date" class="form-control" name="date_r" id="date_r" aria-describedby="helpId" placeholder="Date" required>
                        </div>
                        <input type="hidden" name="id_pdf_t" id="id_pdf_t">
                        <input type="hidden" name="fac_pf" id="fac_pf">
                        <input type="hidden" name="promotion_pf" id="promotion_pf">
                        <input type="hidden" name="depense_pf" id="depense_pf">

                        <div class="form-group">
                            <label for="">Motif</label>
                            <textarea rows="" cols="2" placeholder="Motif" class="form-control" name="motif" id="motif" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="">Cout</label>
                            <input type="text" class="form-control" name="update_montant_" id="update_montant_" aria-describedby="helpId" placeholder="coût" min="0" required>
                        </div>
                        <input type="hidden" name="tot_montant" id="tot_montant">
                        <div class="form-group">
                            <label for="">Poste de depenses</label>
                            <input type="text" class="form-control" name="dep_post_" id="dep_post_" aria-describedby="helpId" placeholder="poste de depense" readonly required>
                        </div>
                        <!--  -->
                        <div class="" id="rr"></div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-danger" type="button btn-danger" data-dismiss="modal">Annuler et fermer</button>
                        <button class="btn btn-success" type="submit" id="btn-tran">Effectuer la transaction</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- modal erreur -->
    <div class="modal fade" id="ErreurModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Erreur</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <div class="modal-body">
                    <span class="text-danger" id="span_error">Veuillez completer tous les champs svp !!!</span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- selection de transaction par date -->
    <div class="modal fade" id="transactioN-date" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Historique de transaction par date</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <form action="./rapport_pdf/hist_transaction_fac_date.php" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="">poste de depense</label>
                            <select name="poste_depense" id="poste_depense" class="form-control">
                                <option value="Tous">Tous</option>
                                <?php
                                    $pdh = ConnexionBdd::Connecter()->prepare("SELECT DISTINCT poste FROM depense_facultaire WHERE id_sections = ?");
                                    $pdh->execute(array($_SESSION['data']['access']));
                                    while($data = $pdh->fetch()){
                                        echo '
                                        <option value="'.$data['poste'].'">'.$data['poste'].'</option>';
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">date debut</label>
                            <input type="date" name="date_1" id="date_1" class="form-control" required>
                            <label for="">date fin</label>
                            <input type="date" name="date_2" id="date_2" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"> x Close</button>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- search -->
    <div class="modal fade" id="mod_search" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chercher un poste de depense</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" class="" id="form_search">
                        <div class="form-group">
                            <select class="custom-select" name="poste" id="poste">
                                <option value="All">All</option>
                                <optgroup></optgroup>
                                <hr>
                                <?php
                                    $lpd = ConnexionBdd::Connecter()->query("SELECT * FROM depense_facultaire");
                                    while($data = $lpd->fetch()){
                                        ?>
                                            <option value="<?=$data['poste']?>"><?=$data['poste']?></option>
                                        <?php
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <select class="form-control" name="annee_acad" id="annee_acad">
                                <!-- <option value="">Annee academique</option> -->
                                <label for="">Annee academique</label>
                                <optgroup></optgroup>
                                <hr>
                                <?php
                                    $lpd = ConnexionBdd::Connecter()->query("SELECT * FROM depense_facultaire GROUP BY id_annee DESC");
                                    while($data = $lpd->fetch()){
                                        ?>
                                            <option value="<?=$data['annee_acad']?>"><?=$data['annee_acad']?></option>
                                        <?php
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <div id="ch_elmts" class="d-flex flex">
                                <input type="checkbox" name="ch_sh" id="ch_sh" value="p"> 
                                <label for="ch_sh" class="ml-1 mt-1" id="ch_sh_l"> selectioner pourcentage</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="text"class="form-control" name="pourc" id="pourc" placeholder="pourcentage">
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

    <!-- search par date de coupure-->
    <div class="modal fade" id="mod_search_par_date" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chercher un poste de depense par date</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" class="" id="form_search_date">
                        <div class="form-group">
                            <select class="custom-select" name="poste_search" id="poste_search" require>
                                <option value="All">All</option>
                                <optgroup></optgroup>
                                <hr>
                                <?php
                                    $lpd = ConnexionBdd::Connecter()->query("SELECT DISTINCT * FROM depense_facultaire");
                                    while($data = $lpd->fetch()){

                                        ?>
                                            <option value="<?=$data['id_pdf']?>"><?=$data['poste']?></option>
                                        <?php
                                    }
                                ?>
                            </select>
                        </div>

                        <div class="form-group d-flex flex" style="justify-content: space-between">
                            <input type="date" name="date_search_1" id="date_search_1" class="form-control" style="width: 48%;" require>
                            <input type="date" name="date_search_2" id="date_search_2" class="form-control" style="width: 48%;" require>
                        </div>
                        <div class="form-group">
                            <select class="form-control" name="annee_acad_search" id="annee_acad_search" require>
                                <!-- <option value="">Annee academique</option> -->
                                <label for="">Annee academique</label>
                                <optgroup></optgroup>
                                <hr>
                                <?php
                                    $lpd = ConnexionBdd::Connecter()->query("SELECT * FROM depense_facultaire GROUP BY id_annee DESC");
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

    <!-- del poste de depense -->
    <div class="modal fade" id="del_poste" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Suppresion de poste de dépense</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <form action="" method="POST" id="form_del_pdd">
                    <input type="hidden" name="id_form_hidden" id="id_form_hidden">
                    <div class="modal-body">
                        <div id="dpd" class="text-danger">Voulez-vous supprimer <b><span id="pdd"></span></b> ?</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Non</button>
                        <button type="submit" class="btn btn-danger">Oui</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- modifier le montant sur le poste de depense -->
    <div class="modal fade" id="update_poste_montant" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier le montant d'un poste de depense</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <form action="" method="post" id="update_mont_poste_depense">
                    <div class="modal-body">
                        <p>Modifier le montant de : <span id="name_poste_d" class="text-primary font-weight-bold font-italic"></span></p>
                        <input type="hidden" name="id_poset_depense_mod_m" id="id_poset_depense_mod_m">
                        <div class="form-group">
                            <input type="text" class="form-control" name="montant_update_pd" id="montant_update_pd" aria-describedby="helpId" placeholder="le nouveau montant">
                        </div>
                    </div>
                    <small id="helpId_Error" class="form-text text-muted"></small>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="sibmit" class="btn btn-primary" id="btn_update_pd">Modifier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Supprimer une transaction -->
    <div class="modal fade" id="del_trans_pd" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Suppression des Transactions effectuer sur le poste de depense</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <form action="" method="post" id="delete_transaction_pd">
                    <div class="modal-body">
                        <input type="hidden" name="id_trans_mod_delete" id="id_trans_mod_delete">
                        <input type="hidden" name="id_trans_mod_delete" id="fac_delete">

                        <p class="text-warning">Voulez-vous vraiment supprimer cette transaction ?</span></p>
                        <div class="form-group">
                            <input type="text" class="form-control" name="trans_post_d_delete" id="trans_post_d_delete" aria-describedby="helpId" placeholder="poste de depense" disabled>
                        </div>

                        <div class="form-group">
                            <input type="text" class="form-control" name="trans_post_montant_delete" id="trans_post_montant_delete" aria-describedby="helpId" placeholder="Montant" disabled>
                        </div>
                    </div>
                    <small id="err_del"></small>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger" id="btn_delete_trans_pd">Supprimer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- modifier le transacrion -->
    <div class="modal fade" id="modifier_trans_pd" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier la transaction sur le poste de depense</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <form action="" method="post" id="action_update_trans">
                    <div class="modal-body">
                        <input type="hidden" name="id_trans_mod" id="id_trans_mod">
                        <div class="form-group">
                            <input type="text" class="form-control" name="trans_post_d" id="trans_post_d" aria-describedby="helpId" placeholder="poste de depense">
                        </div>

                        <div class="form-group">
                            <input type="text" class="form-control" name="trans_post_montant" id="trans_post_montant" aria-describedby="helpId" placeholder="Montant">
                        </div>

                        <div class="form-group">
                            <input type="date" class="form-control" name="trans_post_date_m" id="trans_post_date_m" aria-describedby="helpId" placeholder="date de transaction">
                        </div>

                        <div class="form-group">
                            <div class="form-group">
                                <textarea class="form-control" name="trans_motif_mod" id="trans_motif_mod" placeholder="poste de depense" rows="3"></textarea>
                            </div>
                        </div>

                        <small id="error_trans_mod"></small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="btn_trans_update">Modifier</button>
                    </div>
                </form>                                
            </div>
        </div>
    </div>

    
    <!-- fenetre modal pour la deconnexion-->
    <?php include_once './modal_decon.php';?>

    <!-- <script src="js/mes_scripts/transactions.js"></script> -->

    <script src="js/DataTables/js/jquery.dataTables.min.js"></script>
	<script src="js/DataTables/js/dataTables.bootstrap.min.js"></script>

    <script type="text/javascript">
        $("#a_montant").keyup(function (e) { 
            var x = $("#a_montant").val();
            if(!isNaN(x) && x >= 1 && x !="0."){
                if(x !=""){
                    $("#btn_add").removeAttr('disabled');
                    $("#rr").html('montant valide (: :)').css({color:'green'}).addClass('text-success');
                    console.log(x + "is a number");
                    $("#a_montant").val(x);
                }else{
                    $("#rr").html("une valeur est requis").addClass('text-danger');
                    $("#btn_add").attr('disabled', true);
                }
            }else{
                $("#rr").html("Veuillez saisir un montant valide.").addClass('text-danger');
                $("#btn_add").attr('disabled', true);
                console.log(x + "is not a number");
            }
        });
        // 
        $("#btn-tran").attr('disabled', true);
        $("#update_montant_").keyup(function (e) { 
            var x = $("#update_montant_").val();
            if(!isNaN(x) && x >= 1 && x !="0."){
                if(x !=""){
                    $("#btn-tran").removeAttr('disabled');
                    $("#rr").html('montant valide (: :)').css({color:'green'}).addClass('text-success');
                    console.log(x + "is a number");
                    $("#update_montant_").val(x);
                }else{
                    $("#rr").html("une valeur est requis").addClass('text-danger');
                    $("#btn-tran").attr('disabled', true);
                }
            }else{
                $("#rr").html("Veuillez saisir un montant valide.").addClass('text-danger');
                $("#btn-tran").attr('disabled', true);
                console.log(x + "is not a number");
            }
        });
    </script>

    <script>
        t();
        $(document).ready(function() {
		    $('#t_poste_depense').DataTable();
		} );

        function t(){
            if($("#poste>option:selected").text() !="All"){
                $('#ch_sh_l').hide();
                $('#ch_sh').hide();
                $('#ch_sh_l').css({display:'none'});
            }else{
                $('#ch_sh_l').show();
                $('#ch_sh').show();
                $('#ch_sh_l').css({display:'block'});
            }
        }
    </script>

    <script src="js/mes_scripts/depense_facultaire.js"></script>
    <script type="text/javascript" language="javascript">
        liste();
        $("#t3_form, #hr").css({
            display:'none'
        });

        $("#btn_h").click(function(){
            $("#filtrer_data, #hr").slideDown();
        });

        $("#pourc").hide();

        var affich = $("#ch_sh").val();
        $("#ch_sh").click(function(){
            if($('#ch_sh:checked').val()){
                $("#pourc").slideDown();
            }else{
                $("#pourc").hide(500);
            }
        });

        function liste () {
            $("#poste").change(function(){
                if($("#poste>option:selected").text() !="All"){
                    $('#ch_sh_l').hide();
                    $('#ch_sh').hide();
                    $('#ch_sh_l').css({display:'none'});
                }else{
                    $('#ch_sh_l').show();
                    $('#ch_sh').show();
                    $('#ch_sh_l').css({display:'block'});
                }
            });
        }

        $("#filtrer_data, #hr").hide().css({display:'none'});

        $('table').on('click', '#btn_transaction', function(e){
            b = $(this);
            m = $(this).parent();
            mm = $(m).parent();
            
            poste = mm.find("#post");
            montant = mm.find("#montant");
            montant_restant = mm.find("#m_restant");

            m_fac = mm.find("#m_fac");
            m_promotion = mm.find("#m_promotion");

            m_depense = mm.find("#m_depense");
            id_poste_dep = mm.find("#id_poste_dep");

            $("#id_pdf_t").val(id_poste_dep.text());
            $("#fac_pf").val(m_fac.html());
            $("#promotion_pf").val(m_promotion.html());
            $("#depense_pf").val(m_depense.text());

            // alert(m_depense.text());

            $("#dep_post_").val(poste.text());
            $("#update_montant_").attr('min', '0');
            $("#update_montant_").attr('max', parseInt(montant_restant.text(), 10));

            $("#tot_montant").val($("#update_montant_").val());
        });
        $("#update_montant_").change(function(){
            $("#tot_montant").val(eval(parseFloat($(this).val())) + eval(parseFloat(m_depense.text())));
            // alert()
        });     
        
        $("#update_montant_").keypress(function(){
            $("#tot_montant").val(eval(parseFloat($(this).val())) + eval(parseFloat(m_depense.text())));
        });
    </script>

    <!-- supprimer une transaction -->
    <script>
        $('table').on('click', '#btn_del_trans', function(e){
            b = $(this);
            m = $(this).parent();
            mm = $(m).parent();

            // alert(mm.text());
            
            trans_id = mm.find("#trans_id");
            trans_poste = mm.find("#trans_poste");
            trans_date_t = mm.find("#trans_date_t");
            trans_montant = mm.find("#trans_montant");
            trans_motif = mm.find("#trans_motif");

            $("#id_trans_mod_delete").val(trans_id.text());
            $("#trans_post_d_delete").val(trans_poste.text());
            $("#trans_post_montant_delete").val(trans_montant.text());
            $("#fac_delete").val(trans_date_t.text());

            var montant = $("#trans_post_montant_delete").val();
            var m = montant.replace("$","");

            $("#trans_post_montant_delete").val(m);
        });

        // on soummet le form
        $("#delete_transaction_pd").submit(function (e) { 
            e.preventDefault();
            if($("#id_trans_mod_delete").val() !="" && $("#trans_post_d_delete").val() !="" && $("#trans_post_montant_delete").val() !=""){
                const data = {
                    del_trans : "del_trans",
                    id_trans_mod_delete : $("#id_trans_mod_delete").val() ,
                    trans_post_d_delete : $("#trans_post_d_delete").val() ,
                    trans_post_d_fac : $("#fac_delete").val() ,
                    trans_post_montant_delete : $("#trans_post_montant_delete").val()
                };
                // ajax
                $.ajax({
                    type: "POST",
                    url: "../../includes/update_poste_depense_pf.php",
                    data: data,
                    beforeSend: function(){
                        $("#err_del").html("Un instant svp ...");
                    },
                    success: function (response) {
                        if(response !="" && response == "ok"){
                            $("#err_del").html("Ok suppression reussi");
                            window.location.reload();
                        }else{
                            $("#err_del").html("Erreur : "+response);
                        }
                    },
                    error: function(){
                        $("#err_del").html("Erreur de connexion ...");
                    }
                });
            }else{
                $("#err_del").html("Remplissez tous les champs svp");
            }
        });
    </script>

    <!-- modifier le montant de poste de depense facultaire-->
    <script type="text/javascript">
        // modifier le montant de poste de depense
        $('table').on('click', 'button', function(e){
            b = $(this);
            m = $(this).parent();
            mm = $(m).parent();
            poste = mm.find("#post");
            montant = mm.find("#montant");
            montant_restant = mm.find("#m_restant");

            m_depense = mm.find("#m_depense");
            // alert(m_depense.text());

            name_poste_d = mm.find("#post");
            $("#name_poste_d").html(name_poste_d.text());
            id_poset_depense_mod_m = mm.find("#id_poste_dep");
            $("#id_poset_depense_mod_m").val(id_poset_depense_mod_m.text());
        });

        $("#btn_update_pd").attr('disabled', true);
        $("#montant_update_pd").keyup(function (e) { 
            var x = $("#montant_update_pd").val();
            if(!isNaN(x) && x >= 1 && x !="" && x !="0."){
                if(x !=""){
                    $("#btn_update_pd").removeAttr('disabled');
                    $("#helpId_Error").html('');
                    $("#helpId_Error").html('montant valide (: :)').css({color:'green'}).addClass('text-success');
                    console.log(x + "is a number");
                    $("#montant_update_pd").val(x);
                }else{
                    $("#helpId_Error").html('');
                    $("#helpId_Error").html("une valeur est requis").addClass('text-danger');
                    $("#btn_update_pd").attr('disabled', true);
                }
            }else{
                $("#helpId_Error").html('');
                $("#helpId_Error").html("Veuillez saisir un montant valide.").addClass('text-danger');
                $("#btn_update_pd").attr('disabled', true);
                console.log(x + "is not a number");
            }
        });

        // on soumet le formulaire
        $("#update_mont_poste_depense").submit(function (e) { 
            e.preventDefault();
            if($("#id_poset_depense_mod_m").val() !="" && $("#montant_update_pd").val() !=""){
                const data = {
                    update_pd:"montant_update_pd",
                    id: $("#id_poset_depense_mod_m").val(),
                    montant : $("#montant_update_pd").val()
                };
                $.ajax({
                    type: "POST",
                    url: "../../includes/update_poste_depense_pf.php",
                    data: data,
                    beforeSend:function(){
                        $("#helpId_Error").html("Un instant svp...").addClass('text-primary').css({'color':'blue'});
                    },
                    success: function (response) {
                        if(response !="" && response == "ok"){
                            $("#helpId_Error").html("Traitement reussi").addClass('text-success').css({'color':'green'});
                            window.location.reload();
                        }else{
                            $("#helpId_Error").html("Une Erreur s'est produite : "+response).addClass('text-danger').css({'color':'red'});
                        }
                    },
                    error: function(){
                        $("#helpId_Error").html("Erreur de connexion").addClass('text-danger').css({'color':'red'});
                    }
                });
            }else{
                $("#helpId_Error").html("Veuillez completer tous les champs svp...");
            }
        });
    </script>

    <script>
        $('table').on('click', 'button', function(e){
            b = $(this);
            m = $(this).parent();
            mm = $(m).parent();
            poste = mm.find("#post");
            montant = mm.find("#montant");
            montant_restant = mm.find("#m_restant");

            id_poste_dep = mm.find("#id_poste_dep");

            $("#id_form_hidden").val(id_poste_dep.text());
            $("#pdd").html(poste.text());

            m_depense = mm.find("#m_depense");
        });

        $("#form_del_pdd").submit(function (e) { 
            e.preventDefault();
            if($("#id_form_hidden").val() !=""){
                $.ajax({
                    type: "POST",
                    url: "../../includes/del_poste_r_univ_fac.php",
                    data: {"id_poste_dep":$("#id_form_hidden").val()},
                    beforeSend:function(){
                        $("#dpd").html("Un instant svp ...");
                    },
                    success: function (response) {
                        if(response != "" && response == "ok"){
                            window.location.reload();
                        }else{
                            $("#dpd").html("Erreur : "+response);
                        }
                    }, error: function (e){
                        $("#dpd").html("erreur de connexion, reessayer svp ...");
                    }
                });
            }else{
                alert("erreur de connexion, reessayer svp ...");
            }
        });
    </script>
</body>

</html>