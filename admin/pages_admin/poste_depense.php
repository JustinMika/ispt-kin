<?php
    session_start();
    require_once '../../includes/ConnexionBdd.class.php';
    require_once '../../includes/verification.class.php';
    //verification des sessions
    require_once './sessions.php';
    $p = "Poste de depense";
    $post_dep = "SELECT * FROM poste_depense";
    $params = array();

    $_SESSION['req_rapport'] = $post_dep;
    $_SESSION['params'] = $params;
?>
<!DOCTYPE html>
<html lang="fr">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="../../images/ispt_kin.png" type="image/x-icon">
    <title>Poste de depense</title>

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
                            <div class="card shadow m-0">
                                <div class="card-header d-flex" style="justify-content: space-between;">
                                    <h6 class="m-0 font-weight-bold text-primary" style="text-transform: uppercase;">Poste des depenses</h6>
                                    <div class="dropdown open">
                                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="triggerId" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                                        <div class="dropdown-menu" aria-labelledby="triggerId">
                                            <a class="dropdown-item" data-toggle="modal" href="#myModal_1"> <i class="fa fa-plus" aria-hidden="true"></i> Ajouter un poste de dépense</a>
                                            <!-- <a class="dropdown-item" data-toggle="modal" href="#myModal_2"> <i class="fa fa-upload" aria-hidden="true"> Uploader le fichier Excel</i></a> -->
                                            <hr class="m-0 p-0">
                                            <?php
                                                if(isset($_SESSION['req_rapport']) && strlen($_SESSION['req_rapport']) > 0 && isset($_SESSION['params']) && array_sum($_SESSION['params'])){
                                                    $post_dep = $_SESSION['req_rapport'];
                                                    $params = $_SESSION['params'];
                                                }else{
                                                    $params = array();
                                                }
                                            ?>
                                            <a class="dropdown-item" href="rapport_pdf/rapport_poste_depense.php?r=<?=$post_dep?>&params=<?php $params?>" target="_blank">Imprimer</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body" id="f_poste_depense">
                                    <table class="table table-bordered table-hover mt-1 table-hover table-responsive table-sm table-md table-lg" id="t_poste_depense_">
                                        <thead>
                                            <tr>
                                                <th>#ID</th>
                                                <th>Poste</th>
                                                <th>Montant Prevu</th>
                                                <th>Depense</th>
                                                <th>Montant restant</th>
                                                <th>Niveau d'exécution</th>
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
                                <div class="card-header d-flex flex" style="justify-content: space-between;">
                                    <p class="m-0 font-weight-bold text-primary">Transactions sur les postes de dépense</p>

                                    <div class="btn-group">
                                        <button class="btn btn-primary dropdown-toggle" type="button" id="triggerId" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="triggerId">
                                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#transactioN-date"><i class="fa fa-calendar" aria-hidden="true"></i> Transactions par date</a>
                                            <a class="dropdown-item" href="./rapport_pdf/hist_transaction_moi.php"><i class="fa fa-print" aria-hidden="true"></i> Transactions par mois</a>
                                            <a class="dropdown-item" href="./rapport_pdf/hist_transaction.php"><i class="fa fa-print" aria-hidden="true"></i> Imprimer les transactions</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered table-hover" id="t_poste_depense_t">
                                        <thead class="bg-gray-200">
                                            <tr>
                                                <th>#ID</th>
                                                <th>Num. op</th>
                                                <th>Poste</th>
                                                <th>Date</th>
                                                <th>montant</th>
                                                <th>Motif</th>
                                                <td> # </td>
                                            </tr>
                                        </thead>
                                        <tbody class="text-secondary" id="tbody_transaction_">
                                            <?php
                                                $an =  ConnexionBdd::Connecter()->query("SELECT id_annee FROM annee_acad GROUP BY annee_acad ORDER BY id_annee DESC LIMIT 1");
                                                if($an->rowCount() > 0){
                                                    $an_r = $an->fetch();
                                                    
                                                }else{
                                                    $an_r['id_annee'] = '';
                                                    die("Veuillez AJouter l annee academique");
                                                }

                                                $pd = ConnexionBdd::Connecter()->prepare("SELECT transaction_depense.id_transaction, transaction_depense.montant, transaction_depense.num_op, transaction_depense.date_motif, transaction_depense.motif, poste_depense.id_poste, poste_depense.poste FROM transaction_depense LEFT JOIN poste_depense on transaction_depense.id_poste=poste_depense.id_poste WHERE transaction_depense.id_annee =  ? ORDER BY transaction_depense.date_motif DESC");
                                                $pd->execute(array($an_r['id_annee']));
                                                        
                                                while($data = $pd->fetch()){
                                                    ?>
                                                        <tr>
                                                            <td id="trans_id"><?=$data['id_transaction']?></td>
                                                            <td id="num_op"><?php if(!empty($data['num_op'])){echo $data['num_op'];}else{echo '-';}?></td>
                                                            <td id="trans_poste_id" style="display:none"><?=$data['id_poste']?></td>
                                                            <td id="trans_poste"><?=$data['poste']?></td>
                                                            <td id="trans_date_t"><?=$data['date_motif']?></td>
                                                            <td id="trans_montant"><?=$data['montant'].'$'?></td>
                                                            <td id="trans_motif"><?=$data['motif']?></td>
                                                            <td>
                                                                <button type="button" class="btn btn-primary btn-circle" alt="modifier la transaction" id="btn_update_trans" data-toggle="modal" data-target="#update_trans_pd" title="Modifier"> <i class="fa fa-edit" aria-hidden="true"></i></button>

                                                                <button type="button" class="btn btn-danger btn-circle" alt="suppresion de la transaction" id="btn_del_trans" data-toggle="modal" data-target="#del_trans_pd" title="Supprimer"> <i class="fa fa-trash" aria-hidden="true"></i></button>
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

            <!-- Footer -->
            <!-- footer -->
		  <?php include './footer.php';?>
        </div>
    </div>
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- fenetre modal pour ajouter un poste de depense -->
    <div class="modal fade" id="myModal_1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="exampleModalLabel">Ajout des postes de depenses</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="" id="ajout_poste_depense">
                    <div class="modal-body">
                        <div class="form-group">
                          <label for="">Poste de dépense</label>
                          <input type="text" class="form-control" name="p_depense" id="p_depense" aria-describedby="helpId" placeholder="Poste de dépense">
                        </div>
                        <!-- annee academique -->
                        <div class="form-group">
                            <label for="">Annee Academique</label>
                            <select name="annee_acad" id="annee_acad" class="form-control">
                                <?php
                                    $list = ConnexionBdd::Connecter()->query("SELECT * FROM annee_acad ORDER BY id_annee DESC");
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
                          <label for="">Montant </label>
                          <input type="text" class="form-control" name="a_montant" id="a_montant" aria-describedby="helpId" placeholder="Montant" min="0">
                        </div>
                        <div class="form-group">
                            <label for="" id="r"></label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-danger" type="button" data-dismiss="modal">Annuler</button>
                        <button class="btn btn-primary" type="submit" id="btn_pd">
                            <i class="fa fa-plus" aria-hidden="true"> Ajouter</i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- fenetre modal -> ajout poste de depense-->
    <div class="modal fade" id="myModal_2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog bg-dark">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-left">Upload le Fichier Excel</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-window-close text-danger" aria-hidden="true"></i></button>
                </div>
                <div class="modal-body">
                    <form enctype="multipart/form-data" method="post" action="" id="f_poste">
                        <input type="file" class="form-control mb-1" id="fichier_excel" placeholder="fichier excel" name="file_excel_upload" required="" autofocus="">
                        <input type="submit" name="upload_file_excel" class="form-control bg-success text-white btn btn-primary" value="Uploader">
                    </form>
                </div>
                <div class="modal-footer">
                    <div class="mr-5" style="float: left;" id="t3_form">
                        <div class="spinner-border text-primary" role="status" id="spinner"></div>
                        <span class="h4" id="span">Traitement encours ...</span>
                    </div>
                    <button data-dismiss="modal" class="btn btn-danger" type="button" id="btn_closeF">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Fenetere modal pour la transaction -->
    <div class="modal fade" id="transactions" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Transaction sur le poste de depense</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="btn btn-danger">x</span>
                    </button>
                </div>
                <form action="" id="form_transaction" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <!-- <label for="">date</label> -->
                            <input type="date" class="form-control" name="date_r" id="date_r" aria-describedby="helpId" placeholder="Date" require>

                            <input type="hidden" class="form-control" name="id_poste" id="id_poste" aria-describedby="helpId" placeholder="Date" require>
                        </div>

                        <div class="form-group">
                            <!-- <label for="">Motif</label> -->
                            <textarea rows="" cols="2" placeholder="Motif" class="form-control" name="motif" id="motif" require></textarea>
                        </div>

                        <div class="form-group">
                            <!-- <label for="">Cout</label> -->
                            <input type="text" class="form-control" name="update_montant_" id="update_montant_" aria-describedby="helpId" placeholder="Cout : montant poste de depense" min="0" require>
                        </div>
                        <input type="hidden" name="tot_m" id="tot_m">
                        <div class="form-group">
                            <!-- <label for="">Poste de depenses</label> -->
                            <input type="text" class="form-control" name="dep_post_" id="dep_post_" aria-describedby="helpId" placeholder="poste de depense" readonly require>
                        </div>
                        <div class="form-group">
                            <!-- <label for="">Num. OP</label> -->
                            <input type="text" class="form-control" name="num_op_num" id="num_op_num" aria-describedby="helpId" placeholder="Num. OP" require>
                        </div>
                        <!--  -->
                        <div class=""><span for="" id="rr"></span> </div>
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
                                    $lpd = ConnexionBdd::Connecter()->query("SELECT * FROM transaction_depense");
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
                                    $lpd = ConnexionBdd::Connecter()->query("SELECT * FROM annee_acad GROUP BY id_annee DESC");
                                    while($data = $lpd->fetch()){
                                        ?>
                                            <option value="<?=$data['id_annee']?>"><?=$data['annee_acad']?></option>
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
                                    $lpd = ConnexionBdd::Connecter()->query("SELECT * FROM poste_depense");
                                    while($data = $lpd->fetch()){
                                        ?>
                                            <option value="<?=$data['id_poste']?>"><?=$data['poste']?></option>
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
                                    $lpd = ConnexionBdd::Connecter()->query("SELECT * FROM annee_acad GROUP BY id_annee DESC");
                                    while($data = $lpd->fetch()){
                                        ?>
                                            <option value="<?=$data['id_annee']?>"><?=$data['annee_acad']?></option>
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
                            <input type="text" class="form-control mb-2" name="poste_d_update_pd" id="poste_d_update_pd" aria-describedby="helpId" placeholder="le poste de depense">
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

                        <p class="text-warning">Voulez-vous vraiment supprimer cette transaction ?</span></p>
                        <div class="form-group">
                            <input type="text" class="form-control" name="trans_post_d_delete" id="trans_post_d_delete" aria-describedby="helpId" placeholder="poste de depense" disabled>
                            <input type="hidden" class="form-control" name="trans_post_d_delete_id" id="trans_post_d_delete_id" aria-describedby="helpId" placeholder="poste de depense" disabled>
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
                <form action="./rapport_pdf/hist_transaction_date.php" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="">poste de depense</label>
                            <select name="poste_depense" id="poste_depense" class="form-control">
                                <option value="Tous">Tous</option>
                                <?php
                                    $pdh = ConnexionBdd::Connecter()->prepare("SELECT DISTINCT poste FROM transaction_depense WHERE annee_acad = ?");
                                    $pdh->execute(array($an_r['annee_acad']));
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

    <!-- modifier la transaction sur le psote de depense. -->
    <div class="modal fade" id="update_trans_pd" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier la transaction</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="post" id="form_edit_">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <input type="hidden" name="form_edit_id" id="form_edit_id">
                            <label for="">Numero Op</label>
                            <input type="text" name="num_op_" id="num_op_" placeholder="numero op." class="form-control mb-1">

                            <label for="">poste de dépense</label>
                            <input type="text" name="post_trans_up" id="post_trans_up" placeholder="poste de depense" class="form-control mb-1" disabled>

                            <label for="">Motif</label>
                            <textarea rows="3" cols="" id="form_edit_text" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary"> Modifier</button>
                    </div>
                    <span id="error_update"></span>
                </form>
            </div>
        </div>
    </div>
    
    <!-- fenetre modal pour la deconnexion-->
    <?php include_once './modal_decon.php';?>

    <script src="js/mes_scripts/transactions.js"></script>

    <script src="js/DataTables/js/jquery.dataTables.min.js"></script>
	<script src="js/DataTables/js/dataTables.bootstrap.min.js"></script>
    <script src="js/datatables.filters.js"></script>

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
        
        // prevision
        $("#btn_pd").attr('disabled', true);
        $("#a_montant").keyup(function (e) { 
            var x = $("#a_montant").val();
            if(!isNaN(x) && x >= 1 && x !="" && x !="0."){
                if(x !=""){
                    $("#btn_pd").removeAttr('disabled');
                    $("#r").html('');
                    $("#r").html('montant valide (: :)').css({color:'green'}).addClass('text-success');
                    console.log(x + "is a number");
                    $("#a_montant").val(x);
                }else{
                    $("#r").html('');
                    $("#r").html("une valeur est requis").addClass('text-danger');
                    $("#btn_pd").attr('disabled', true);
                }
            }else{
                $("#r").html('');
                $("#r").html("Veuillez saisir un montant valide.").addClass('text-danger');
                $("#btn_pd").attr('disabled', true);
                console.log(x + "is not a number");
            }
        });

        // transaction  
        $("#btn-tran").attr('disabled', true);
        $("#update_montant_").keyup(function (e) { 
            var x = $("#update_montant_").val();
            if(!isNaN(x) && x >= 1 && x !="" && x !="0."){
                if(x !=""){
                    $("#btn-tran").removeAttr('disabled');
                    $("#rr").html('');
                    $("#rr").html('montant valide (: :)').css({color:'green'}).addClass('text-success');
                    console.log(x + "is a number");
                    $("#update_montant_").val(x);
                }else{
                    $("#rr").html('');
                    $("#rr").html("une valeur est requis").addClass('text-danger');
                    $("#btn-tran").attr('disabled', true);
                }
            }else{
                $("#rr").html('');
                $("#rr").html("Veuillez saisir un montant valide.").addClass('text-danger');
                $("#btn-tran").attr('disabled', true);
                console.log(x + "is not a number");
            }
        });
    </script>

    <script src="js/mes_scripts/poste_depense.js"></script>
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

        $('table').on('click', 'button', function(e){
            b = $(this);
            m = $(this).parent();
            mm = $(m).parent();
            poste = mm.find("#post");
            montant = mm.find("#montant");
            montant_restant = mm.find("#m_restant");
            id_poste_dep = mm.find("#id_poste_dep");

            m_depense = mm.find("#m_depense");
            $("#id_poste").val(id_poste_dep.text());
            $("#tot_m").val(parseInt(m_depense.text()), 10);

            // alert($("#id_poste").val());


            $("#dep_post_").val(poste.text());
            $("#update_montant_").attr('min', '0');
            $("#update_montant_").attr('max', parseInt(montant_restant.text(), 10));
        });

        $("#btn_closeF").click(function (e) { 
            window.location.reload();
        });  
    </script>

    <script>
        // clic sur le tableau
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
                    url: "../../includes/del_poste_r_univ.php",
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
                    }
                });
            }else{
                alert("erreur");
            }
        });
    </script>

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
            
            montant = mm.find("#montant");
            // alert(montant.text());

            name_poste_d = mm.find("#post");
            $("#name_poste_d").html(name_poste_d.text());
            id_poset_depense_mod_m = mm.find("#id_poste_dep");
            $("#id_poset_depense_mod_m").val(id_poset_depense_mod_m.text());
            $("#poste_d_update_pd").val(name_poste_d.text());
            $("#montant_update_pd").val(montant.text());
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
            if($("#id_poset_depense_mod_m").val() !="" && $("#montant_update_pd").val() !="" 
            && $("#poste_d_update_pd").val() !=""){
                const data = {
                    update_pd:"montant_update_pd",
                    id: $("#id_poset_depense_mod_m").val(),
                    montant : $("#montant_update_pd").val(),
                    name_poste_d:$("#poste_d_update_pd").val()
                };
                $.ajax({
                    type: "POST",
                    url: "../../includes/update_poste_depense.php",
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

    <!-- modifier la transaction -->
    <script>
        $('table').on('click', '#btn_update_trans', function(e){
            b = $(this);
            m = $(this).parent();
            mm = $(m).parent();

            
            trans_id = mm.find("#trans_id");
            trans_motif = mm.find("#trans_motif");
            num_op = mm.find("#num_op");
            trans_poste = mm.find("#trans_poste");

            

            $("#form_edit_id").val(trans_id.text());
            $("#form_edit_text").val(trans_motif.text());
            $("#num_op_").val(num_op.text());
            $("#post_trans_up").val(trans_poste.text());

            // on lance la requette ajax
            $("#form_edit_").submit(function (e) { 
                e.preventDefault();
                
                if($("#form_edit_id").val() !="" && $("#form_edit_text").val() !="" && $("#num_op_").val() !="" && $("#post_trans_up").val() !=""){
                    // ajax en action
                    const data = {
                        update_trans_mod : "update_trans_mod",
                        id_trans_mod : $("#form_edit_id").val(),
                        trans_motif : $("#form_edit_text").val(),
                        num_op : $("#num_op_").val(),
                        post_trans_upp : $("#post_trans_up").val()
                    };
                    $.ajax({
                        type: "POST",
                        url: "../../includes/update_poste_depense.php",
                        data: data,
                        beforeSend: function(){
                            $("#error_update").removeClass('text-danger');
                            $("#error_update").html('');
                            $("#error_update").html("Un instant svp ...").addClass('text-success');
                        },
                        success: function (response) {
                            if(response !="" && response == "ok"){
                                $("#error_update").removeClass('text-danger');
                                $("#error_update").html('');
                                $("#error_update").html("ok").addClass('text-success');
                                window.location.reload();
                            }else{
                                $("#error_update").removeClass('text-success');
                                $("#error_update").html('');
                                $("#error_update").html("Une erreur est survenue : "+response).addClass('text-danger');
                            }
                        },
                        error: function (error){
                            $("#error_update").removeClass('text-success');
                            $("#error_update").html('');
                            $("#error_update").html("Erreur de connexion ...").addClass('text-danger');
                        }
                    });
                }else{
                    $("#error_update").removeClass('text-success');
                    $("#error_update").html('');
                    $("#error_update").html("Veuillez completer tous les champs requis svp ...").addClass('text-danger');
                }
            });
        });
    </script>

    <!--update tranxation  -->
    <script>
        $('table').on('click', '#btn_mod_trans', function(e){
            b = $(this);
            m = $(this).parent();
            mm = $(m).parent();

            data_verif();
            
            trans_id = mm.find("#trans_id");
            trans_poste = mm.find("#trans_poste");
            trans_date_t = mm.find("#trans_date_t");
            trans_montant = mm.find("#trans_montant");
            trans_motif = mm.find("#trans_motif");

            $("#id_trans_mod").val(trans_id.text());
            $("#trans_post_d").val(trans_poste.text());
            $("#trans_post_montant").val(trans_montant.text());
            $("#trans_post_date_m").val(trans_date_t.text());
            $("#trans_motif_mod").val(trans_motif.text());

            function data_verif(){
                $("#btn_trans_update").attr('disabled', true);
                $("#trans_post_montant").keyup(function (e) { 
                    var x = $("#trans_post_montant").val();
                    if(!isNaN(x) && x >= 1 && x !="" && x !="0."){
                        if(x !=""){
                            $("#btn_trans_update").removeAttr('disabled');
                            $("#error_trans_mod").removeClass('text-danger');
                            $("#error_trans_mod").html('');
                            $("#error_trans_mod").html('montant valide (: :)').css({color:'green'}).addClass('text-success');

                            $("#trans_post_montant").val(x);
                        }else{
                            $("#error_trans_mod").removeClass('text-success');
                            $("#error_trans_mod").html('');
                            $("#error_trans_mod").html("une valeur est requis").addClass('text-danger');
                            $("#btn_trans_update").attr('disabled', true);
                        }
                    }else{
                        $("#error_trans_mod").removeClass('text-success');
                        $("#error_trans_mod").html('');
                        $("#error_trans_mod").html("Veuillez saisir un montant valide.").addClass('text-danger');
                        $("#btn_trans_update").attr('disabled', true);
                        console.log(x + "is not a number");
                    }
                });
            }

            // on lance la requette ajax
            $("#action_update_trans").submit(function (e) { 
                e.preventDefault();
                
                if($("#id_trans_mod").val() !="" && $("#trans_post_d").val() !="" && $("#trans_post_montant").val() !="" && $("#trans_post_date_m").val() !="" && $("#trans_motif_mod").val() !=""){
                    // ajax en action
                    const data = {
                        update_trans : "update_trans",
                        id_trans_mod : $("#id_trans_mod").val(),
                        trans_post_d : $("#trans_post_d").val(),
                        trans_post_montant : $("#trans_post_montant").val(),
                        trans_post_date_m : $("#trans_post_date_m").val(),
                        trans_motif_mod : $("#trans_motif_mod").val()
                    };
                    $.ajax({
                        type: "POST",
                        url: "../../includes/update_poste_depense.php",
                        data: data,
                        beforeSend: function(){
                            $("#error_trans_mod").removeClass('text-danger');
                            $("#error_trans_mod").html('');
                            $("#error_trans_mod").html("Un instant svp ...").addClass('text-success');
                        },
                        success: function (response) {
                            if(response !="" && response == "ok"){
                                $("#error_trans_mod").removeClass('text-danger');
                                $("#error_trans_mod").html('');
                                $("#error_trans_mod").html("ok").addClass('text-success');
                                window.location.reload();
                            }else{
                                $("#error_trans_mod").removeClass('text-success');
                                $("#error_trans_mod").html('');
                                $("#error_trans_mod").html("Une erreur est survenue : "+response).addClass('text-danger');
                            }
                        },
                        error: function (error){
                            $("#error_trans_mod").removeClass('text-success');
                            $("#error_trans_mod").html('');
                            $("#error_trans_mod").html("Erreur de connexion ...").addClass('text-danger');
                        }
                    });
                }else{
                    $("#error_trans_mod").removeClass('text-success');
                    $("#error_trans_mod").html('');
                    $("#error_trans_mod").html("Veuillez repmlir tous les champs requis svp ...").addClass('text-danger');
                }
            });
        });
    </script>

    <!-- supprimer une transaction -->
    <script>
        $('table').on('click', '#btn_del_trans', function(e){
            b = $(this);
            m = $(this).parent();
            mm = $(m).parent();
            
            trans_id = mm.find("#trans_id");
            trans_id_post = mm.find("#trans_poste_id");
            trans_poste = mm.find("#trans_poste");
            trans_date_t = mm.find("#trans_date_t");
            trans_montant = mm.find("#trans_montant");
            trans_motif = mm.find("#trans_motif");

            // alert(trans_id_post.text());

            $("#id_trans_mod_delete").val(trans_id.text());
            $("#trans_post_d_delete").val(trans_poste.text());
            $("#trans_post_montant_delete").val(trans_montant.text());

            var montant = $("#trans_post_montant_delete").val();
            var m = montant.replace("$","");

            $("#trans_post_montant_delete").val(m);
        });

        // on soummet le form
        $("#delete_transaction_pd").submit(function (e) { 
            e.preventDefault();
            if($("#id_trans_mod_delete").val() !="" && $("#trans_post_d_delete").val() !="" && $("#trans_post_montant_delete").val() !="" && trans_id_post.text() !=""){
                const data = {
                    del_trans : "del_trans",
                    trans_id_post:trans_id_post.text(),
                    id_trans_mod_delete : $("#id_trans_mod_delete").val() ,
                    trans_post_d_delete : $("#trans_post_d_delete").val() ,
                    trans_post_montant_delete : $("#trans_post_montant_delete").val()
                };
                // ajax
                $.ajax({
                    type: "POST",
                    url: "../../includes/update_poste_depense.php",
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
</body>
</html>