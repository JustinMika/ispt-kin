<?php 
    session_start();
    require_once '../../includes/ConnexionBdd.class.php';
    require_once '../../includes/verification.class.php';
    //verification des sessions
    require_once './sessions.php';
    $p = "Poste des recettes";
?>
<!DOCTYPE html>
<html lang="fr">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Poste de recette</title>
    <link rel="shortcut icon" href="../../images/ispt_kin.png" type="image/x-icon">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="js/DataTables/css/dataTables.jqueryui.min.css">
    <link rel="stylesheet" href="js/DataTables/css/dataTables.bootstrap4.min.css">

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
                    <div class="row"></div>
                    <div class="row">
                        <!-- reccette academique -->
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="card shadow">
                                <div class="card-header d-flex" style="justify-content: space-between;">
                                    <h1 class="h3 mb-3 text-gray-800 ml-4">Poste de poste de recettes</h1>
                                    <div class="dropdown open">
                                        <button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false">Actions</button>
                                        <div class="dropdown-menu" aria-labelledby="triggerId">
                                            <button data-target="#add_poste" data-toggle="modal" class="dropdown-item btn btn-primary btn-sm text-secondary"><i class="fa fa-plus" aria-hidden="true"> Ajouter un poste de recette</i></button>
                                            <!-- <button data-target="#post_recette" data-toggle="modal" class="dropdown-item btn btn-default btn-sm text-secondary"><i class="fa fa-upload" aria-hidden="true"> Uploader le fichier</i></button> -->
                                            <a href="rapport_pdf/rapport_poste_recette.php" class="dropdown-item btn btn-default btn-sm text-secondary" target="_blank"><i class="fa fa-print" aria-hidden="true"></i> Imprimer</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="col-md-12 col-sm-12 col-lg-12">
                                    <table class="table table-bordered table-hover" id="12">
                                        <thead class="bg-ligth">
                                            <tr>
                                                <th>#ID</th>
                                                <th>Poste de poste recette</th>
                                                <th>Montant</th>
                                                <th>Année Academique</th>
                                                <th>#</th>
                                            </tr>
                                        </thead>
                                        <tbody class="">
                                            <!-- affichage -->
                                            <?php
                                                // on recupere le dernier annee academique
                                                $a = ConnexionBdd::Connecter()->query("SELECT id_annee, annee_acad FROM annee_acad ORDER BY id_annee DESC LIMIT 0,1");
                                                if($a->rowCount() > 0){
                                                    $d = $a->fetch();
                                                }else{
                                                    $d['id_annee'] = '';
                                                }

                                                $pfrais = ConnexionBdd::Connecter()->prepare("SELECT poste_recette.id_post_rec as id, poste_recette.poste_rec as poste, poste_recette.montant, annee_acad.annee_acad, annee_acad.id_annee FROM poste_recette LEFT JOIN annee_acad on poste_recette.id_annee=annee_acad.id_annee WHERE poste_recette.id_annee = ?");
                                                $pfrais->execute(array($d['id_annee']));
                                                while($data = $pfrais->fetch()){
                                                    ?>
                                                        <tr>
                                                            <td id="id_post"><?=$data['id']?></td>
                                                            <td id="post_pru"><?=utf8_decode(ucfirst($data['poste']))?></td>
                                                            <td id="montant_pru"><?=$data['montant']?></td>
                                                            <td id="annee_acad"><?=$data['annee_acad']?></td>
                                                            <td>
                                                                <button class="btn btn-sm btn-primary" id="btn_update_pru" title="Modifier">Modifier</button>
                                                                <button class="btn btn-sm btn-danger" id="btn_del_pfrais" title="Supprimer">Supprimer</button>
                                                            </td>
                                                        </tr>
                                                    <?php
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                                </div>
                                <div class="card-footer text-muted">...</div>
                            </div>
                        </div>

                        <!-- recette cote etudiants -->
                        <div class="col-sm-12 col-md-12 col-lg-12 mt-4">
                            <div class="card">
                                <div class="card-header d-flex p-1 bg-gray-200" style="justify-content:space-between">
                                    <h1 class="h3 mb-3 text-gray-800 ml-5 text-center">Prévision des frais</h1>
                                    <div class="dropdown open">
                                        <button class="btn btn-primary dropdown-toggle m-2" type="button" id="triggerId" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                                        <div class="dropdown-menu" aria-labelledby="triggerId">
                                            <button data-target="#prevision_f" data-toggle="modal" class="dropdown-item btn btn-default btn-sm text-secondary"><i class="fa fa-upload" aria-hidden="true"> Ajouter une prévision</i></button>

                                            <!-- <button data-target="#modal_etud" data-toggle="modal" class="dropdown-item btn btn-default btn-sm text-secondary"><i class="fa fa-upload" aria-hidden="true"> Uploader le fichier</i></button> -->
                                            
                                            <a href="rapport_pdf/prevision_frais_acad.php" class="dropdown-item btn-sm btn-default text-secondary" id="btn_rapport" target="_blank"><i class="fa fa-print" aria-hidden="true"> Imprimer un Rapport</i> </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div id="form_filter">
                                        <form action="" method="post" class="form-inline mb-3">
                                            <div class="form-group">
                                                <input type="text" class="form-control" name="" id="" aria-describedby="helpId" placeholder="type de frais">
                                                <select name="fac" id="" class="form-control ml-3">
                                                    <option value="fac">Faculte</option>
                                                </select>
                                                <select name="fac" id="" class="form-control ml-3">
                                                    <option value="fac">Promotion</option>
                                                </select>

                                                <button type="submit" class="btn btn-primary ml-3">Chercher</button>
                                            </div>
                                        </form>
                                    </div>
                                    <table class="table table-bordered table-hover" id="log_user">
                                        <thead class="bg-gray-200">
                                            <tr>
                                                <th>#ID</th>
                                                <th>Type </th>
                                                <th>Montant</th>
                                                <th>Section</th>
                                                <th>Promotion</th>
                                                <th>Annee Academique</th>
                                                <th> # </th>
                                            </tr>
                                        </thead>
                                        <tbody class="">
                                            <!-- affichage -->
                                            <?php
                                                $an =  ConnexionBdd::Connecter()->query("SELECT * FROM annee_acad GROUP BY annee_acad ORDER BY id_annee DESC LIMIT 1");
                                                if($an->rowCount() > 0){
                                                    $an_r = $an->fetch();
                                                }else{
                                                    $an_r['id_annee'] = '';
                                                }

                                                $pfrais = ConnexionBdd::Connecter()->prepare("SELECT prevision_frais.id_frais as id,prevision_frais.montant, prevision_frais.promotion, prevision_frais.type_frais, sections.section as faculte, departement.departement, annee_acad.annee_acad FROM prevision_frais 
                                                LEFT JOIN sections on prevision_frais.id_section = sections.id_section 
                                                LEFT JOIN departement on prevision_frais.id_departement = departement.id_departement 
                                                LEFT JOIN options on prevision_frais.id_option = options.id_option
                                                LEFT JOIN annee_acad on prevision_frais.id_annee = annee_acad.id_annee WHERE prevision_frais.id_annee = ?");
                                                $pfrais->execute(array($an_r['id_annee']));
                                                while($data = $pfrais->fetch()){
                                                    ?>
                                                        <tr>
                                                            <td id="id_type_frais_"><?=$data['id']?></td>
                                                            <td id="type_frais_"><?=utf8_decode(ucfirst($data['type_frais']))?></td>
                                                            <td id="montant_a"><?=$data['montant'].'$'?></td>
                                                            <td id="faculte_"><?=$data['faculte']?></td>
                                                            <td id="promotion_"><?=$data['promotion']?></td>
                                                            <td id="annee_acad_a"><?=$data['annee_acad']?></td>
                                                            <td>
                                                                <button type="button" class="btn btn-primary p-1" data-toggle="modal" aria-pressed="false" autocomplete="off" data-target="#update_from" id="update_pf">
                                                                    <i class="fas fa-edit">  Modifier</i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    <?php
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="card-footer text-muted">...</div>
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

    <!-- Suppresion des poste de recettes universitaire -->
    <div class="modal fade" id="del_pfrais" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Suppresion des poste de recettes universitaire</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <form action="../../includes/del_poste_r_univ.php" method="POST" id="form_del_post_rec_univ">
                    <input type="hidden" name="id_poste_rec_univ" id="id_poste_rec_univ">
                    <div class="modal-body">
                        <div class="text-danger" id="ex">Voulez-vous vraiment supprimer <b><span id="pfrais_d"></span></b> ?</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Non</button>
                        <button class="btn btn-danger" type="submit">Oui</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Button trigger modal -->
    <div class="modal fade" id="update_from" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier une prévision des frais</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" id="update_prevision_ffrais">
                        <div class="form-group">
                            <input type="text" name="type_frais_" id="type_frais" class="form-control" placeholder="Type de frais" aria-describedby="helpId">

                            <input type="hidden" name="_id_type_frais_" id="_id_type_frais_" class="form-control" placeholder="Type de frais" aria-describedby="helpId">
                        </div>
                        <div class="form-group">
                            <input type="text" name="montant_" id="montant_" class="form-control" placeholder="Montant" aria-describedby="helpId">
                        </div>
                        <div id="e_update"></div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="upda">Modifier</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- AJouter une prevision des frais. -->
    <div class="modal fade" id="prevision_f" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Prévision des frais pour les étudiant(e)s</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <div class="modal-body">
                    <form action="" method ="POST" id="Ajout_frais_etud">
                        <div class="form-group">
                            <input type="text" class="form-control" name="_type_Frais_pay_" id="_type_Frais_pay_" aria-describedby="helpId" placeholder="Type de frais">
                        </div>
                        <div class="form-group">
                            <label for="">Sections</label>
                            <select class="form-control" name="section_" id="section_">
                                <?php
                                    $sel_fac = ConnexionBdd::Connecter()->query("SELECT id_section, section FROM sections ORDER BY id_section");
                                    while($d = $sel_fac->fetch()){
                                        echo '
                                        <option value="'.$d['id_section'].'">'.$d['section'].'</option>';
                                    } 
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Departement</label>
                            <select class="form-control" name="departement_" id="departement_">
                                <?php
                                    $sel_fac = ConnexionBdd::Connecter()->query("SELECT id_departement, departement FROM departement ORDER BY id_departement");
                                    while($d = $sel_fac->fetch()){
                                        echo '
                                        <option value="'.$d['id_departement'].'">'.$d['departement'].'</option>';
                                    } 
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Option</label>
                            <select class="form-control" name="option_" id="option_">
                                <?php
                                    $sel_fac = ConnexionBdd::Connecter()->query("SELECT id_option, option_ FROM options ORDER BY id_option");
                                    while($d = $sel_fac->fetch()){
                                        echo '
                                        <option value="'.$d['id_option'].'">'.$d['option_'].'</option>';
                                    } 
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Promotion</label>
                            <select class="form-control" name="promotion_prev_p" id="promotion_prev_p">
                                <?php
                                    $sel_fac = ConnexionBdd::Connecter()->query("SELECT DISTINCT promotion FROM options ORDER BY id_option");
                                    while($d = $sel_fac->fetch()){
                                        echo '
                                        <option value="'.$d['promotion'].'">'.$d['promotion'].'</option>';
                                    } 
                                ?>
                            </select>
                        </div>
                        <div class="form-group p-0">
                            <label for="">Montant</label>
                            <input type="text" class="form-control" name="montant_prev_p" id="montant_prev_p" aria-describedby="helpId" placeholder="Montant" required>
                            <small id="Erreor_s"></small>
                        </div>
                        <div class="modal-footer p-o m-0">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                            <button type="submit" class="btn btn-primary" id="btn_u" disabled="true">Ajouter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Uploader un fichier de poste de recette -->
    <div class="modal fade" id="post_recette" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h5 class="modal-title text-white" id="exampleModalLabel">Poste de Recette</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="" method="POST" id="form_poste_recette">
                    <div class="modal-body">
                        <div class="form-group">
                        <label for="">uploader le fichier</label>
                        <input type="file" class="form-control-file" name="fichier_excel_" id="fichier_excel_" placeholder="uploader les fichiers">
                        </div>
                    </div>
                    <div class="modal-footer d-flex flex-row">
                        <div class="" style="width: 45%;" id="t3_form">
                            <div class="spinner-border text-primary" role="status" id="spinner"></div>
                            <span class="" id="span">Traitement encours ...</span>
                        </div>
                        
                        <div style="width: 45%;">
                            <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal"><i class="fa fa-eraser" aria-hidden="true"> Annuler</i></button>
                            <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-upload" aria-hidden="true"> Uploader</i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- modal pour la prevision des frais a payer par les etudiants -->
    <div class="modal fade" id="modal_etud" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog bg-dark">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title text-left text-white">Prevision des frais</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-window-close text-danger" aria-hidden="true"></i></button>
                </div>
                <div class="modal-body">
                    <form enctype="multipart/form-data" method="post" action="" id="previson_frais">
                        <input type="file" class="form-control mb-1" id="fichier_excel" placeholder="fichier excel" name="file_excel_upload" required="" autofocus="">
                        <input type="submit" name="upload_file_excel" class="form-control bg-success text-white btn btn-primary" value="Uploader">
                    </form>
                </div>
                <div class="modal-footer">
                    <div class="mr-5" style="float: left;" id="t3_form_">
                        <div class="spinner-border text-primary" role="status" id="spinner"></div>
                        <span class="h4" id="span">Traitement encours ...</span>
                    </div>
                    <button data-dismiss="modal" class="btn btn-danger" type="button">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Ajouter un poste de recette  -->
    <div class="modal fade" id="add_poste" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajout d'un poste de recette universitaire</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" id="add_univ_poste_">
                        <div class="form-group">
                            <label for="poste_recette_univ_">poste de recette universitaire</label>
                            <input type="text" name="poste_recette_univ_" id="poste_recette_univ_" class="form-control" placeholder="poste de recette universitaire" aria-describedby="helpId" required>
                        </div>
                        <!-- le montant -->
                        <div class="form-group">
                            <label for="montant_poste_univ">Montant : </label>
                            <input type="text" name="montant_poste_univ" id="montant_poste_univ" class="form-control" placeholder="Montant" aria-describedby="helpId" required>
                        </div>
                        <small id="Erreor_ss"></small>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                            <button type="submit" class="btn btn-primary" id="save">Enregistrer</button>
                        </div>
                        <small id="errorId" class="text-muted"></small>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- modifer le montant pour le poste de recettes universitaire. -->
    <div class="modal fade" id="mod_update" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Poste de recette universitaire</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" id="id_update_poste">
                        mise à jour du montant de : <b><span id="mpru"></span></b>
                        <div class="form-group">
                            <input type="hidden" name="id_id_id" id="id_id_id"  class="form-control" disabled>
                        </div>
                        <div class="form-group">
                            <input type="text" name="post_ru" id="post_ru" class="form-control" disabled>
                        </div>
                        <div class="form-group">
                            <input type="text" name="post_ru_ann" id="post_ru_ann" class="form-control" disabled>
                        </div>
                        <div class="form-group">
                            <input type="text" name="montant_ru" id="montant_ru" class="form-control" placeholder="montant" required>
                        </div>
                        <small id="Erreor_sss"></small>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Fermer</button>
                            <button type="submit" class="btn btn-primary" id="m_update">Mettre à jour</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- fenetre modal pour la deconnexion-->
    <?php include_once './modal_decon.php';?>

    <script src="js/DataTables/js/jquery.dataTables.min.js"></script>
	<script src="js/DataTables/js/dataTables.bootstrap.min.js"></script>
    <script src="js/mes_scripts/previsions.js"></script>

    <script>
        $(document).ready(function() {
            $('#t-prev').DataTable();
        });
    </script>

    <script type="text/javascript" language="javascript">
        $("#t3_form").css({
            display:'none'
        });

        $("#t3_form_").css({
            display:'none'
        });
        $("#form_filter").hide();
    </script>

    <script type="text/javascript">
        $("#montant_prev_p").keyup(function () { 
            //  || Number.isFloat(x)
            var x = $("#montant_prev_p").val();
            // var x = montant_a.text();
            if(!isNaN(x) && x >= 1 && x !="0" && x!="0."){
                if(x !=""){
                    $("#btn_u").removeAttr('disabled');
                    $("#Erreor_s").html('montant valide (: :)').css({color:'green'}).addClass('text-success');
                    console.log(x + "is a number");
                    $("#montant_prev_p").val(x);
                }else{
                    $("#Erreor_s").html("une valeur est requis").addClass('text-danger');
                    $("#btn_u").attr('disabled', true);
                }
            }else{
                $("#Erreor_s").html("Veuillez saisir un montant valide.").addClass('text-danger');
                $("#btn_u").attr('disabled', true);
                console.log(x + "is not a number");
            }
        });
// 
        $("#montant_poste_univ").keyup(function (e) { 
            var x = $("#montant_poste_univ").val();
            if(!isNaN(x) && x >= 1 && x !="0" && x!="0."){
                if(x !=""){
                    $("#save").removeAttr('disabled');
                    $("#Erreor_ss").html('montant valide (: :)').css({color:'green'}).addClass('text-success');
                    console.log(x + "is a number");
                    $("#montant_poste_univ").val(x);
                }else{
                    $("#Erreor_ss").html("une valeur est requis").addClass('text-danger');
                    $("#save").attr('disabled', true);
                }
            }else{
                $("#Erreor_ss").html("Veuillez saisir un montant valide.").addClass('text-danger');
                $("#save").attr('disabled', true);
                console.log(x + "is not a number");
            }
        });

        $("#montant_ru").keyup(function (e) { 
            var x = $("#montant_ru").val();
            if(!isNaN(x) && x >= 1 && x !="0" && x!="0."){
                if(x !=""){
                    $("#m_update").removeAttr('disabled');
                    $("#Erreor_sss").html('montant valide (: :)').css({color:'green'}).addClass('text-success');
                    console.log(x + "is a number");
                    $("#montant_ru").val(x);
                }else{
                    $("#Erreor_sss").html("une valeur est requis").addClass('text-danger');
                    $("#m_update").attr('disabled', true);
                }
            }else{
                $("#Erreor_sss").html("Veuillez saisir un montant valide.").addClass('text-danger');
                $("#m_update").attr('disabled', true);
                console.log(x + "is not a number");
            }
        });

        // click sur le bouton modifier
        $('table').on('click', '#btn_update_pru', function(e){ 
            e.preventDefault();
            $("#mod_update").modal('toggle');

            b = $(this);
            m = $(this).parent();
            mm = $(m).parent();
            poste = mm.find("#post_pru");
            montant = mm.find("#montant_pru");
            annee_acad = mm.find("#annee_acad");
            id = mm.find("#id_post");

            // alert(id.text());

            $("#post_ru").val(poste.text());
            $("#mpru").text(poste.text());
            $("#post_ru_ann").val(annee_acad.text());
            $("#montant_ru").val(montant.text());
            $("#id_id_id").val(id.text());
        });

        $('table').on('click', '#update_pf', function(e){
            e.preventDefault();

            b = $(this);
            m = $(this).parent();
            mm = $(m).parent();
            
            var id_type_frais_ = mm.find("#id_type_frais_");
            var type_frais_ = mm.find("#type_frais_");
            var montant_a = mm.find("#montant_a");
            
            // e_update
            $("#type_frais").val(type_frais_.text()).attr('disabled', true);
            $("#_id_type_frais_").val(id_type_frais_.text());
            $("#montant_").val(montant_a.text());

            // alert(id_type_frais_.text());

            var x = $("#montant_").val();
            if(!isNaN(x)){
                if(x !="" && x >= 1 && x !="0" && x !="0."){
                    $("#upda").removeAttr('disabled');
                    $("#e_update").html('montant valide (: :)').css({color:'green'}).addClass('text-success');
                    console.log(x + "is a number");
                    $("#montant_").val(x);
                }else{
                    $("#e_update").html("une valeur est requis").addClass('text-danger');
                    $("#upda").attr('disabled', true);
                }
            }else{
                $("#e_update").html("Veuillez saisir un montant valide.").addClass('text-danger');
                $("#upda").attr('disabled', true);
                console.log(x + "is not a number");
            }
        });

        $("#montant_").keyup(function (e) { 
            var x = $("#montant_").val();
            if(!isNaN(x)){
                if(x !="" && x >= 1 && x !="0" && x !="0."){
                    $("#upda").removeAttr('disabled');
                    $("#e_update").html('montant valide (: :)').css({color:'green'}).addClass('text-success');
                    console.log(x + "is a number");
                    $("#montant_").val(x);
                }else{
                    $("#e_update").html("une valeur est requis").addClass('text-danger');
                    $("#upda").attr('disabled', true);
                }
            }else{
                $("#e_update").html("Veuillez saisir un montant valide.").addClass('text-danger');
                $("#upda").attr('disabled', true);
                console.log(x + "is not a number");
            }
        });

        // click sur le bouton supprimer un poste de resc. univ.
        $('table').on('click', '#btn_del_pfrais', function(e){ 
            e.preventDefault();
            
            b = $(this);
            m = $(this).parent();
            mm = $(m).parent();
            poste = mm.find("#post_pru");
            montant = mm.find("#montant_pru");
            annee_acad = mm.find("#annee_acad");
            id = mm.find("#id_post");

            // alert();

            $("#pfrais_d").text(poste.text());
            if(id.text() !=""){
                $("#del_pfrais").modal('toggle');
                $("#id_poste_rec_univ").val(id.text());
            }
        });

        $("#form_del_post_rec_univ").submit(function (e) { 
            e.preventDefault();
            $.ajax({
                type: "POST",
                url: "../../includes/del_poste_r_univ.php",
                data: {"id_poste_rec_univ":$("#id_poste_rec_univ").val()},
                beforeSend: function(){
                    $("#ex").css({'color':'green'}).html("un instant ...");
                },
                success: function (response) {
                    if(response !="" && response == "ok"){
                        window.location.reload();
                    }else{
                        // alert(response);
                        $("#ex").html(response);
                    }
                }
            });
        });
    </script>
</body>

</html>