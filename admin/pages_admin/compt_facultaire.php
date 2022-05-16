<?php
    session_start();
    require_once '../../includes/ConnexionBdd.class.php';
    require_once '../../includes/verification.class.php';
    //verification des sessions
    require_once './sessions.php';
    require_once '../../includes/log_user.class.php';
    $p = "Comptabilité facultaire";

    function restruct_user(){
        if($_SESSION['data']['fonction'] != "" && $_SESSION['data']['access'] !=""){
            if($_SESSION['data']['fonction'] && $_SESSION['data']['access'] && $_SESSION['data']['access'] == "Admin"){
                return '';
            }else{
                return 'display:none';
            }
        }else{
            header("location:../index.php", true, 301);
        }
        
    }

    function restruct_r_r(){
        if($_SESSION['data']['fonction'] != "" && $_SESSION['data']['access'] !=""){
            if($_SESSION['data']['fonction'] && $_SESSION['data']['access'] && $_SESSION['data']['access'] == "Admin" || $_SESSION['data']['access'] == "AB"){
                return '';
            }else{
                return 'display:none';
            }
        }else{
            header("Location:../index.php", true, 301);
        }
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="Justin Micah" content="">
    <title><?=$p?></title>
    <link rel="shortcut icon" href="../../images/UNIGOM_W260px.jpg" type="image/x-icon">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
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
                
                <div class="container-fluid mt-3">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 m-0">
                            <div class="card shadow">
                                <div class="card-header d-flex flex bg-gray-200" style="justify-content: space-between;">
                                    <h5 class="m-0 font-weight-bolder text-primary text-center">LISTE DES ETUDIANTS</h5>
                                    <div class="dropdown">
                                        <button class="btn btn-primary dropdown-toggle" type="button" id="triggerId" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                                        <div class="dropdown-menu" aria-labelledby="triggerId">
                                            <h6 class="text-left ml-2">Previsons des frais</h6>
                                            <a class="dropdown-item" href="#" data-target="#prevision_f" data-toggle="modal" style="<?php //restruct_user();?>"><i class="fa fa-plus" aria-hidden="true"></i> Ajouter une previson</a>
                                            <div class="dropdown-divider"></div>

                                            <h6 class="text-left ml-2">Payement</h6>
                                            <a class="dropdown-item" href="#" data-target="#Effect_payement" data-toggle="modal" style="<?php //restruct_user();?>"><i class="fa fa-search" aria-hidden="true"></i> Effectuer un payement</a>
                                            <a class="dropdown-item" href="#" data-target="#modal_payement_etud" data-toggle="modal" style="<?php //restruct_user();?>"><i class="fa fa-search" aria-hidden="true"></i> Listes des Payements</a>
                                            <div class="dropdown-divider"></div>

                                            <!-- <h6 class="text-left ml-2">chercher par : </h6> -->
                                            <div class="dropdown-divider"></div>
                                            <a href="./rapport_pdf/prev_facultaire.php" class="dropdown-item"><i class="fa fa-print" aria-hidden="true"></i> Imprimer les prévisions</a>
                                            <!-- payement_fac_frais -->
                                        </div>
                                    </div> 
                                </div>
                                <div class="card-body">
                                    <table class="table table-hover" id="t_etudiants">
                                        <thead class="bg-dark text-white">
                                            <tr>
                                                <td><i class="icon_profile"></i> Matricule</td>
                                                <td><i class="icon_calendar"></i> Noms</td>
                                                <td><i class="icon_mail_alt"></i> Fac.</td>
                                                <td><i class="icon_pin_alt"></i> Promotion</td>
                                                <td><i class="icon_mobile"></i> Année Acad.</td>
                                                <td><i class="icon_cogs"></i> #</td>
                                            </tr>
                                        </thead>
                                        <tbody id="t_inscription_etudiants" class="" style="<?php //restruct_user()?>">
                                        </tbody>
                                        <caption id="no-data"></caption>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- fin main content-->
            </div>
            <!-- footer -->
		  <?php include './footer.php';?>
        </div>
    </div>
    <!-- le modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog bg-dark">
            <div class="modal-content">
                <div class="modal-header bg-gray-100 text-secondary">
                <h4 class="modal-title text-center">Affectation des frais aux etudiants</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-window-close text-danger" aria-hidden="true"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" id="form_inscription_etud">
                        <div class="form-group">
                            <input type="text" class="form-control" name="mat_etud" id="mat_etud" required placeholder="matricule de l'etudiant">
                        </div>
                        <div class="form-group">
                            <select class="custom-select" name="fac_search" id="fac_search" required>
                                <option value="<?=$_SESSION['data']['access']?>"><?=$_SESSION['data']['access']?></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <select class="custom-select" name="promotion_search" id="promotion_search" required>
                                <option value="">- Promotion -</option>
                                <optgroup>-------------------</optgroup>
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
                            <select class="custom-select" name="annee_acad_search" id="annee_acad_search" required>
                                <option value="">- Annee Academique -</option>
                                <?php
                                    $verif = ConnexionBdd::Connecter()->query("SELECT * FROM annee_academique ORDER BY id DESC");
                                    while($data = $verif->fetch()){
                                        ?>
                                            <option value="<?=$data['annee_acad']?>"><?=$data['annee_acad']?></option>
                                        <?php
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="modal-footer mt-2" id="t3">
                            <button data-dismiss="modal" class="btn btn-danger" type="button">Fermer</button>
                            <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Chercher l'etudiant</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- affecter les frais aux etudiants -->
    <div class="modal fade" id="mod_affectation" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true" style="overflow: scroll;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Afectation des frais pour : [<span id="etud_" class="text-dark"></span>]</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="btn_12">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" id="fff">
                        <input type="hidden" name="mat_etud" id="mat_etud_aff_aff">
                        <input type="hidden" name="annee_acad" id="annee_acad_aff_aff">
                        <input type="hidden" name="fac" id="fac_aff_aff">
                        <input type="hidden" name="promotion" id="promotion_aff_aff">
                        <!-- <div class="form-group">
                            <input type="checkbox" name="ch_sh_cochet_t" id="ch_sh_cochet_t"> Tous cocher
                        </div> -->
                        <div class="card" style="overflow-y: scroll;height:20rem">
                            <div class="card-body p-3" id="ffff"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn_11">Annuler et fermer</button>
                            <button type="submit" class="btn btn-primary" id="b_b">Affecter</button>
                        </div>
                        <p id="ErrorAff"></p>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- supprimer les affectations aux etudiants -->
    <div class="modal fade" id="del_affectation" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true" style="overflow: scroll;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier l'Afectation des frais pour : [<span id="etud_xy_mod" class="text-dark"></span>]</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="btn_12">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" id="delaffect">
                        <input type="hidden" name="mat_etud_da" id="mat_etud_aff_aff_da">
                        <input type="hidden" name="annee_acad_da" id="annee_acad_aff_aff_da">
                        <input type="hidden" name="fac_da" id="fac_aff_aff_da">
                        <input type="hidden" name="promotion_da" id="promotion_aff_aff_da">
                        
                        <div class="card" style="overflow-y: scroll;height:20rem">
                            <div class="card-body p-3" id="frais_del"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-dismiss="modal" id="btn_11">Annuler et fermer</button>
                            <button type="submit" class="btn btn-danger" id="b_b_s">Supprimer les frais</button>
                        </div>
                        <p id="ErrorAffD"></p>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- affectation par faculte et promotion -->
    <div class="modal fade" id="mod_affectation_fac_promotion" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Affectation des frais aux etudiants</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="" id="form_inscription_fac_prom">
                        <div class="form-group">
                            <select class="custom-select" name="fac_search_f_p" id="fac_search_f_p" required>
                                <option value="">- Faculté -</option>
                                <optgroup>____________</optgroup>
                                <?php
                                    echo '
                                    <option value="'.$_SESSION['data']['access'].'">'.$_SESSION['data']['access'].'</option>';
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <select class="custom-select" name="promotion_search_f_p" id="promotion_search_f_p" required>
                                <option value="">- Promotion -</option>
                                <optgroup>-------------------</optgroup>
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
                            <select class="custom-select" name="annee_acad_search_f_p" id="annee_acad_search_f_p" required>
                                <option value="">- Annee Academique -</option>
                                <optgroup>-------------------</optgroup>
                                <?php
                                    $verif = ConnexionBdd::Connecter()->query("SELECT * FROM annee_academique ORDER BY id DESC");
                                    while($data = $verif->fetch()){
                                        ?>
                                            <option value="<?=$data['annee_acad']?>"><?=$data['annee_acad']?></option>
                                        <?php
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="modal-footer mt-2" id="t3">
                            <button data-dismiss="modal" class="btn btn-danger" type="button">Fermer et Quitter</button>
                            <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Chercher</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="error_modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">[INFO]</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <div class="modal-body">
                    <p id="error_modal_text"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Effectuer un seul payement -->
    <div class="modal fade" id="Effect_payement" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header m-0 p-2">
                    <h5 class="modal-title">Effectuer un Payement</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <form action="" method="POST" id="Payement_etudiant_form">
                            <div class="form-group">
                                <input type="text" name="mat_etud_payement" id="mat_etud_payement" class="form-control" placeholder="matricule de l etudiant" required>
                            </div>
                            <div class="form-group">
                                <select class="form-control" name="promotion_etud_p" id="promotion_etud_p" required>
                                    <option>--promotion--</option>
                                    <?php
                                        $list = ConnexionBdd::Connecter()->query("SELECT  * FROM `etudiants_inscrits`  GROUP By promotion ORDER BY promotion ASC");
                                        while($data = $list->fetch()){
                                            ?>
                                                <option value="<?=$data['promotion']?>"><?=$data['promotion']?></option>
                                            <?php
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <select class="form-control" name="fac_etud_payemt" id="fac_etud_payemt" required>
                                    <?php
                                    echo '
                                        <option value="'.$_SESSION['data']['access'].'">'.$_SESSION['data']['access'].'</option>';
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <select class="form-control" name="type_frais_p_etud" id="type_frais_p_etud" required>
                                    <option>--Type de frais--</option>
                                    <?php
                                        $list = ConnexionBdd::Connecter()->prepare("SELECT type_frais FROM `prev_fac_frais` WHERE faculte = ? GROUP BY type_frais");
                                        $list->execute(array($_SESSION['data']['access']));
                                        while($data = $list->fetch()){
                                            ?>
                                                <option value="<?=$data['type_frais']?>"><?=$data['type_frais']?></option>
                                            <?php
                                        }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <select class="form-control" name="annee_acad_pay_etud" id="annee_acad_pay_etud" required>
                                    <option>--Annee Academique--</option>
                                    <?php
                                        $list = ConnexionBdd::Connecter()->query("SELECT * FROM `annee_academique` ORDER BY id DESC LIMIT 1");
                                        while($data = $list->fetch()){
                                            ?>
                                                <option value="<?=$data['annee_acad']?>"><?=$data['annee_acad']?></option>
                                            <?php
                                        }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <input type="text" name="pay_num_bordereau" id="pay_num_bordereau" class="form-control" placeholder="numero bordereau" required>
                            </div>

                            <div class="form-group">
                                <input type="date" name="date_p" id="date_p" class="form-control" placeholder="date payement" required>
                            </div>

                            <div class="form-group">
                                <input type="text" name="pay_etud_p" id="pay_etud_p" class="form-control" placeholder="montant" required>
                            </div>
                            <div id="error_t3"></div>
                            <div class="modal-footer p-0">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-close" aria-hidden="true"></i> Fermer</button>
                                <button type="submit" class="btn btn-primary" id="pay_btn"><i class="fa fa-paypal" aria-hidden="true"></i> Effectuer le payement</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- info modal -->
    <div class="modal fade" id="Affich_info" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">[INFO]</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <div class="modal-body">
                    <p class="h4 text-success">Affectation des frais reussi avec succes.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                    <button type="button" class="btn btn-primary" id="show_all">Afficher tous les etudiants</button>
                </div>
            </div>
        </div>
    </div>

    <!-- AJouter une prevision des frais. -->
    <div class="modal fade" id="prevision_f" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Prévision des frais facultaire pour les étudiant(e)s</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <div class="modal-body">
                    <form action="" method ="POST" id="Ajout_frais_etud">
                        <div class="form-group">
                            <label for="">Type de frais</label>
                            <input type="text" class="form-control" name="type_Frais_pay_" id="type_Frais_pay_" aria-describedby="helpId" placeholder="Type de frais">
                        </div>
                        <div class="form-group">
                            <label for="">Faculte</label>
                            <select class="form-control" name="fac_prev_p" id="fac_prev_p">
                                <?php
                                    echo '
                                        <option value="'.$_SESSION['data']['access'].'">'.$_SESSION['data']['access'].'</option>';
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Promotion</label>
                            <select class="form-control" name="promotion_prev_p" id="promotion_prev_p">
                                <?php
                                    $sel_fac = ConnexionBdd::Connecter()->query("SELECT DISTINCT promotion FROM etudiants_inscrits ORDER BY id");
                                    while($d = $sel_fac->fetch()){
                                        echo '
                                        <option value="'.$d['promotion'].'">'.$d['promotion'].'</option>';
                                    } 
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Montant</label>
                            <input type="text" class="form-control" name="montant_prev_p" id="montant_prev_p" aria-describedby="helpId" placeholder="Montant" required>
                            <small id="Erreor_s"></small>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                            <button type="submit" class="btn btn-primary" id="btn_u" disabled="true">Ajouter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Listes des payements etudiants -->
    <div class="modal fade" id="modal_payement_etud" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Payement des étudiants</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <div class="modal-body">
                    <table class="table table-hover table-bordered table-full-width table-* table-condensed" id="t_etudiants">
                        <thead class="bg- text-secondary">
                            <tr>
                                <td><i class=""></i> Matricule</td>
                                <td><i class=""></i> Faculté</td>
                                <td><i class=""></i> Promotion</td>
                                <td><i class=""></i> Date</td>
                                <td><i class=""></i> Type Frais</td>
                                <td><i class=""></i> Num. Borderau</td>
                                <td><i class=""></i> Montant</td>
                                <td><i class=""></i> # </td>
                            </tr>
                        </thead>
                        <tbody id="t_inscription_etudiants" class="" style="<?php //restruct_user()?>">
                            <?php
                                $an =  ConnexionBdd::Connecter()->query("SELECT * FROM annee_academique GROUP BY annee_acad ORDER BY id DESC LIMIT 1");
                                if($an->rowCount() > 0){
                                    $an_r = $an->fetch();
                                }else{
                                    $an_r['annee_acad'] = '';
                                    die("Veuillez AJouter l annee academique");
                                }
                                $list = ConnexionBdd::Connecter()->prepare("SELECT * FROM payement_fac_frais WHERE faculte = ? AND annee_acad = ?");
                                $list->execute(array($_SESSION['data']['access'], $an_r['annee_acad']));
                                if($list->rowCount() > 0){
                                    while($data = $list->fetch()){
                                        ?>
                                        <tr>
                                            <td id="id_pay_etud" style="display:none"><?=$data['id']?></td>
                                            <td id="mat_pay_etud"><?=$data['matricule']?></td>
                                            <td id="fac_pay_etud"><?=$data['faculte']?></td>
                                            <td id="pr_pay_etud"><?=$data['promotion']?></td>
                                            <td id="date_pay_etud"><?=$data['date_payement']?></td>
                                            <td id="type_f_pay_etud"><?=$data['type_frais']?></td>
                                            <td id="num_b_pay_etud"><?=$data['num_borderon']?></td>
                                            <td id="montant_pay_etud"><?=$data['montant']?></td>
                                            <td><i class=""></i>
                                                <button class="btn-sm btn-circle btn-primary mb-1 mr-1" title="Modifier le payement" id="btn_edit_p">
                                                    <i class="fa fa-edit" aria-hidden="true"></i>
                                                </button>

                                                <button class="btn-sm btn-circle btn-danger" title="Supprimer le payement" id="btn_del_p">
                                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                }else{
                                    echo '<p class="text-warning">Pas des données</p>';
                                }
                            ?>
                        </tbody>
                        <caption id="no-data"></caption>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <!-- <button type="button" class="btn btn-primary">Save</button> -->
                </div>
            </div>
        </div>
    </div>

    <!--  modal pour supprimer un payement -->
    <div class="modal fade" id="modal_del_payement" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Suppression</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <form action="" method="post" id="form_del_payement">
                    <div class="modal-body">
                        <p class="text-danger">Voulez-vous vraiment supprimer cette enregistrement ?</p>
                        <input type="hidden" name="id_form_del_p" id="id_form_del_p">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Non</button>
                        <button type="submit" class="btn btn-danger">Oui</button>
                    </div>
                    <span id="r3"></span>
                </form>
                    
            </div>
        </div>
    </div>

    <!-- modal pour modifier le payement -->
    <div class="modal fade" id="modal_update_pay" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modal title</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <form action="#" method="post" id="form_update_payement_etud">
                    <div class="modal-body">
                        <input type="hidden" name="x_id_pay_etud" id="x_id_pay_etud">
                        <div class="form-group">
                            <input type="text" class="form-control" name="x_mat_pay_etud" id="x_mat_pay_etud" aria-describedby="helpId" placeholder="" disabled>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" name="x_fac_pay_etud" id="x_fac_pay_etud" aria-describedby="helpId" placeholder="" disabled>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" name="x_pr_pay_etud" id="x_pr_pay_etud" aria-describedby="helpId" placeholder="" disabled>
                        </div>
                        <div class="form-group">
                            <input type="date" class="form-control" name="x_date_pay_etud" id="x_date_pay_etud" aria-describedby="helpId" placeholder="">
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" name="x_type_f_pay_etud" id="x_type_f_pay_etud" aria-describedby="helpId" placeholder="" disabled>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" name="x_num_b_pay_etud" id="x_num_b_pay_etud" aria-describedby="helpId" placeholder="">
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" name="x_montant_pay_etud" id="x_montant_pay_etud" aria-describedby="helpId" placeholder="">
                        </div>
                    </div>
                    <div class="modal-footer p-2">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Modifier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <?php include_once("modal_decon.php");?>
    
    <!-- previsions des frais facultaires -->
    <script>
        $("#montant_prev_p").keyup(function () { 
            //  || Number.isFloat(x)
            var x = $("#montant_prev_p").val();
            // var x = montant_a.text();
            if(!isNaN(x) && x >= 1 && x !="0" && x!="0."){
                if(x !=""){
                    $("#btn_u").removeAttr('disabled');
                    $("#Erreor_s").removeClass('text-danger');
                    $("#Erreor_s").html('montant valide (: :)').css({color:'green'}).addClass('text-success');
                    console.log(x + "is a number");
                    $("#montant_prev_p").val(x);
                }else{
                    $("#Erreor_s").removeClass('text-success');
                    $("#Erreor_s").html("une valeur est requis").addClass('text-danger');
                    $("#btn_u").attr('disabled', true);
                }
            }else{
                $("#Erreor_s").html("Veuillez saisir un montant valide.").addClass('text-danger');
                $("#btn_u").attr('disabled', true);
                console.log(x + "is not a number");
            }
        });

        // ajouter une prevision pour les etudiants
        $("#Ajout_frais_etud").submit(function(e) {
            e.preventDefault();
            if ($("#type_Frais_pay_").val() != "" && $("#type_Frais_pay_").val().length > 4) {
                $.ajax({
                    type: "POST",
                    url: "../../includes/add_prev_facultaire.php",
                    data: $('#Ajout_frais_etud').serializeArray(),
                    beforeSend: function() {
                        $("#Erreor_s").text("Patienter un moment").css({ color: 'blue' });
                    },
                    success: function(response) {
                        if (response == "ok") {
                            $("#Erreor_s").text("le type de frais '" + $("#type_Frais_pay_").val() + "' est ajouté avec succès").css({ color: 'green' });
                            window.location.reload();
                        } else {
                            $("#Erreor_s").text("Erreur : " + response).addClass('text-danger');
                        }
                    },
                    error: function(response) {
                        $("#Erreor_s").text("Une erreur est survenue ... : " + response).addClass('text-danger');
                    }
                });
            } else {
                $("#Erreor_s").html("Veuillez renseigner le type de frais a payé par les étudiant(e)s").addClass('text-danger');
            }
        });
    </script>

    <!-- affichage de la listes des etudiants -->
    <script>
        $.ajax({
            type: "GET",
            url: "../../includes/AffectationFacFraisEtud.php",
            data: { 
                affich : "affich_form",
                fac : "<?=$_SESSION['data']['access']?>"
            },
        }).done(function(data) {
            if (data != "") {
                $($("#t_inscription_etudiants")).empty();
                $("#t_inscription_etudiants").append(data);
            } else {
                $($("#t_inscription_etudiants")).empty();
                $("#error_modal").modal('toggle');
                $("#error_modal_text").html('<p class="text-danger"><i class="fa fa-database" aria-hidden="true"></i> Aucun resultat trouver pour l\'instant.<br/> aucun etudiants dans la base de donnees.</p>');
                $("#no-data").html("<marquee>Aucun resultat trouver pour l'instant.</marquee>");
            }
        }).fail(function(data) {});
    </script>

    <!-- modifier le payements de frais facultaires -->
    <script type="text/javascript" language="javascript">
        $('table').on('click', '#btn_edit_p', function(e){
            $("#modal_payement_etud").modal("toggle");
            $("#modal_update_pay").modal("toggle");
            b = $(this);
            m = $(this).parent();
            mm = $(m).parent();
            
            id_pay_etud = mm.find("#id_pay_etud");
            mat_pay_etud = mm.find("#mat_pay_etud");
            fac_pay_etud = mm.find("#fac_pay_etud");
            pr_pay_etud = mm.find("#pr_pay_etud");
            date_pay_etud = mm.find("#date_pay_etud");
            type_f_pay_etud = mm.find("#type_f_pay_etud");
            num_b_pay_etud = mm.find("#num_b_pay_etud");
            montant_pay_etud = mm.find("#montant_pay_etud");

            $("#x_id_pay_etud").val(id_pay_etud.text());
            $("#x_mat_pay_etud").val(mat_pay_etud.text());
            $("#x_fac_pay_etud").val(fac_pay_etud.text());
            $("#x_pr_pay_etud").val(pr_pay_etud.text());
            $("#x_date_pay_etud").val(date_pay_etud.text());
            $("#x_type_f_pay_etud").val(type_f_pay_etud.text());
            $("#x_num_b_pay_etud").val(num_b_pay_etud.text());
            $("#x_montant_pay_etud").val(montant_pay_etud.text());

            // alert(montant_pay_etud.text());
            $("#form_update_payement_etud").submit(function (e) { 
                e.preventDefault();
                if($("#x_id_pay_etud").val() !="" && $("#x_mat_pay_etud").val() !="" && $("#x_fac_pay_etud").val() !="" && $("#x_pr_pay_etud").val() !="" && $("#x_date_pay_etud").val() !="" && $("#x_type_f_pay_etud").val() !="" && $("#x_num_b_pay_etud").val() !="" && $("#x_montant_pay_etud").val() !=""){
                    const data = {
                        update_payemt_etud:"update_payemt_etud",
                        x_id_pay_etud:id_pay_etud.text(),
                        x_mat_pay_etud:mat_pay_etud.text(),
                        x_fac_pay_etud:fac_pay_etud.text(),
                        x_pr_pay_etud:pr_pay_etud.text(),
                        x_date_pay_etud:date_pay_etud.text(),
                        x_type_f_pay_etud:type_f_pay_etud.text(),
                        x_num_b_pay_etud:num_b_pay_etud.text(),
                        x_montant_pay_etud:montant_pay_etud.text()
                    };

                    $.ajax({
                        type: "POST",
                        url: "../../includes/t3_update_honoraire.php",
                        data: data,
                        success: function (data) {
                            if(data != "" && data == "ok"){
                                $("#modal_update_pay").modal("toggle");
                                $("#modal_payement_etud").modal("toggle");
                                $("#id_pay_etud").text($("#x_id_pay_etud").val());
                                $("#mat_pay_etud").text($("#x_mat_pay_etud").val());
                                $("#fac_pay_etud").text($("#x_fac_pay_etud").val());
                                $("#pr_pay_etud").text($("#x_pr_pay_etud").val());
                                $("#date_pay_etud").text($("#x_date_pay_etud").val());
                                $("#type_f_pay_etud").text($("#x_type_f_pay_etud").val());
                                $("#num_b_pay_etud").text($("#x_num_b_pay_etud").val());
                                $("#montant_pay_etud").text($("#x_montant_pay_etud").val());
                            }else{
                                $("#rr1").html("");
                                $("#rr1").html(data);
                            }
                        },
                        error: function (data){
                            $("#rr1").html("");
                            $("#rr1").html("Erreur de connexion.");
                        }
                    });
                }else{
                    $("#rr1").html("");
                    $("#rr1").html("Veuillez selectionner au moins un element, ...");
                }
            });
        });
    </script>

    <!-- supprimer un payement de frais facultaire -->
    <script type="text/javascript" language="javascript">
        $('table').on('click', '#btn_del_p', function(e){
            $("#modal_del_payement").modal("toggle");
            b = $(this);
            m = $(this).parent();
            mm = $(m).parent();
            
            id_pay_etud = mm.find("#id_pay_etud");

            $("#id_form_del_p").val(id_pay_etud.text());

            $("#form_del_payement").submit(function (e) { 
                e.preventDefault();
                if($("#id_form_del_p").val() !=""){
                    const data = {
                        del_p:"del_p",
                        id_form_del_p:$("#id_form_del_p").val()
                    };
                    $.ajax({
                        type: "POST",
                        url: "../../includes/t3_update_honoraire.php",
                        data: data,
                        success: function (response) {
                            if(response !="" && response == "ok"){
                                $("#modal_del_payement").modal("toggle");
                                $(mm).slideUp();
                            }else{
                                $("#r3").html("");
                                $("#r3").html(response);
                            }
                        },
                        error: function(e){
                            $("#r3").html("");
                            $("#r3").html("Erreur de connexion.");
                        }
                    });
                }else{
                    $("#r3").html("");
                    $("#r3").html("Veuillez selectionner un element svp ...");
                }
            });
        });
    </script>

    <script type="text/javascript" language="javascript">
        $(document).ready(function(){
            $('table').on('click', 'a', function(e){
                b = $(this);
                m = $(this).parent();
                mm = $(m).parent();
                etudiants = mm.find("#mat").text()+ " : "+mm.find("#noms").text();
                fac = mm.find("#fac").text();
                promotion = mm.find("#promotion").text();
                annee_acad = mm.find("#annee_academique").text();
                // alert(promotion);
                if(fac !="" && promotion !="" && annee_acad != ""){
                    $("#etud_").text(etudiants);
                    
                    $("#mat_etud_aff_aff").val(mm.find("#mat").text());
                    $("#annee_acad_aff_aff").val(annee_acad);
                    $("#fac_aff_aff").val(fac);
                    $("#promotion_aff_aff").val(promotion);

                    const data = {
                        "payement":"payement",
                        "mat_student":mm.find("#mat").text(),
                        "fac":fac,
                        "annee_acad":annee_acad,
                        "promotion":promotion
                    };
                    $.ajax({
                        type: "GET",
                        url: "../../includes/AffectationFacFraisEtud.php",
                        data: data,
                        async: false,
                        success: function (data) {
                            $("#ffff").empty();
                            $("#ffff").append(data);
                        },
                        error: function (response){
                            // une erreur est survenue
                            alert("Erreur de la connexion" + response);
                        }
                    });
                }
            });

            $('table').on('click', '#btn_del_affecter', function(e){
                b = $(this);
                m = $(this).parent();
                mm = $(m).parent();                

                etudiants = mm.find("#mat").text()+ " : "+mm.find("#noms").text();
                $("#etud_xy_mod").text(etudiants);

                fac = mm.find("#fac").text();
                promotion = mm.find("#promotion").text();
                annee_acad = mm.find("#annee_academique").text();
                // alert(mm.find("#mat").text()+" "+fac+" "+promotion+" "+annee_acad);
                if(fac !="" && promotion !="" && annee_acad != ""){
                    $("#etud_").text(etudiants);

                    $("#mat_etud_aff_aff_da").val(mm.find("#mat").text());
                    $("#annee_acad_aff_aff_da").val(annee_acad);
                    $("#fac_aff_aff_da").val(fac);
                    $("#promotion_aff_aff_da").val(promotion);

                    const data = {
                        "payement_del_aff":"payement",
                        "mat_student":mm.find("#mat").text(),
                        "fac":fac,
                        "annee_acad":annee_acad,
                        "promotion":promotion
                    };
                    $.ajax({
                        type: "GET",
                        url: "../../includes/del_affect_fac.php",
                        data: data,
                        async: false,
                        success: function (data) {
                            $("#frais_del").empty();
                            $("#frais_del").append(data);
                        },
                        error: function (response){
                            // une erreur est survenue
                            alert("Erreur de la connexion" + response);
                        }
                    });
                }
            });
        });
	</script>

    <!-- affectation -->
    <script>
        // affecter les fais a l etudiants
        $("#fff").submit(function(e) {
            e.preventDefault();
            var mat_etud_aff = $("#mat_etud_aff_aff").val();
            var annee_acad_aff = $("#annee_acad_aff_aff").val();
            var fac_aff = $("#fac_aff_aff").val();
            var promotion_aff = $("#promotion_aff_aff").val();

            frais_a_payer = Array();
            montant = Array();

            $('input[name="ch_sh"]:checked').each(function() {
                montant.push($(this).val());
                montant.join(', ');
                frais_a_payer.push($(this).attr('placeholder'));
                frais_a_payer.join(', ');
            });
            if (mat_etud_aff != "" && annee_acad_aff != "" && fac_aff != "" && promotion_aff != "") {
                const data = {
                    affect: "affect",
                    mat: mat_etud_aff,
                    promotion: promotion_aff,
                    frais: frais_a_payer,
                    fac: fac_aff,
                    annee_acad: annee_acad_aff,
                    montant_f: montant
                };
                $.ajax({
                    type: "POST",
                    url: "../../includes/AffectationFacFraisEtud.php",
                    data: { affect: "affecter", matricule: mat_etud_aff, promotion_aff: promotion_aff, fac_aff: fac_aff, annee_acad_aff: annee_acad_aff, frais: frais_a_payer, montant_f: montant },
                }).done(function(data) {
                    if (data != "" && data == "ok") {
                        // window.location.reload();
                        $("#mod_affectation").modal('toggle');
                        $("#Affich_info").modal('toggle');
                    } else {
                        // alert(data);
                        $("#ErrorAff").text("");
                        $("#ErrorAff").text(data).css({ color: 'red' });
                        // $("#ErrorAff").text("Veuillez selectionner au moins un type de frais").css({ color: 'red' });
                    }
                }).fail(function(data) {
                    alert("Erreur de connexion.");
                });
            } else {
                alert("Veuillez completer tous les champs");
            }
        });

        $("#delaffect").submit(function(e) {
            e.preventDefault();
            var mat_etud_aff = $("#mat_etud_aff_aff_da").val();
            var annee_acad_aff = $("#annee_acad_aff_aff_da").val();
            var fac_aff = $("#fac_aff_aff_da").val();
            var promotion_aff = $("#promotion_aff_aff_da").val();

            frais_a_payer = Array();
            montant = Array();

            $('input[name="ch_sh_d"]:checked').each(function() {
                montant.push($(this).val());
                montant.join(', ');
                frais_a_payer.push($(this).attr('placeholder'));
                frais_a_payer.join(', ');
            });

            if (mat_etud_aff != "" && annee_acad_aff != "" && fac_aff != "" && promotion_aff != "") {
                const data = {
                    d_affect: "d_affect",
                    mat: mat_etud_aff,
                    promotion: promotion_aff,
                    frais: frais_a_payer,
                    fac: fac_aff,
                    annee_acad: annee_acad_aff,
                    montant_f: montant
                };
                $.ajax({
                    type: "POST",
                    url: "../../includes/del_affect_fac.php",
                    data: data,
                }).done(function(data) {
                    if (data != "" && data == "ok") {
                        $("#del_affectation").modal('toggle');
                        $("#Affich_info").modal('toggle');
                    } else {
                        // alert(data);
                        $("#ErrorAffD").text("");
                        $("#ErrorAffD").text(data).css({ color: 'red' });
                        // $("#ErrorAff").text("Veuillez selectionner au moins un type de frais").css({ color: 'red' });
                    }
                }).fail(function(data) {
                    alert("Erreur de connexion.");
                });
            } else {
                alert("Veuillez completer tous les champs");
            }
        });
    </script>
    <!-- del affectation -->
    <script>
        $("#pay_btn").attr('disabled', true);
        $("#pay_etud_p").keyup(function (e) { 
            var x = $("#pay_etud_p").val();
            if(!isNaN(x) && x >= 1 && x !="0" && x !="0,"){
                if(x !=""){
                    $("#pay_btn").removeAttr('disabled');
                    $("#error_t3").html('');
                    $("#error_t3").html('montant valide (: :)').css({color:'green'}).addClass('text-success');
                    console.log(x + "is a number");
                    $("#pay_etud_p").val(x);
                }else{
                    $("#error_t3").html('');
                    $("#error_t3").html("une valeur est requis").addClass('text-danger');
                    $("#pay_btn").attr('disabled', true);
                }
            }else{
                $("#error_t3").html('');
                $("#error_t3").html("Veuillez saisir un montant valide.").addClass('text-danger');
                $("#pay_btn").attr('disabled', true);
                console.log(x + "is not a number");
            }
        });
    </script>

    <!-- payement frais fac -->
    <script>
        // paye,ent form
    $("#Payement_etudiant_form").submit(function(e) {
        e.preventDefault();
        // payement_form_etudiants.php
        $.ajax({
            type: "POST",
            url: "../../includes/payement_form_etudiants_fac.php",
            data: $('#Payement_etudiant_form').serializeArray(),
            success: function(response) {
                if (response == "Donnees insere") {
                    $("#error_t3").html("ERROR: " + response).css({ color: "green" });
                    $("#mat_etud_payement").val('');
                    $("#promotion_etud_p").val('');
                    $("#fac_etud_payemt").val('');
                    $("#type_frais_p_etud").val('');
                    $("#pay_etud_p").val('');
                    $("#pay_num_bordereau").val('');
                    $("#date_p").val('');
                    $("#annee_acad_pay_etud").val('');
                    window.location.reload();
                } else {
                    $("#error_t3").html("ERROR: " + response).css({ color: "red" });
                }
            },
            beforeSend: function() {
                $("#error_t3").html("status : veuillez patienter").css({ color: "green" });
            },
            error: function(response) {
                $("#error_t3").html("ERROR: " + response);
            }
        });
    });
    </script>
</body>
</html>