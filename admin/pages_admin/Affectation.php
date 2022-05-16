<?php
    session_start();
    require_once '../../includes/ConnexionBdd.class.php';
    require_once '../../includes/verification.class.php';
    //verification des sessions
    require_once './sessions.php';
    $p = 'Affectation des frais aux etudiants';

    /**autoriser ou bloquer certains utilisateurs a faire quekques chose de particulier */
    function restruct_user(){
        if($_SESSION['data']['fonction'] != "" && $_SESSION['data']['access'] !=""){
            if($_SESSION['data']['access'] == "utilisateur" && $_SESSION['data']['fonction'] == "Caissier"){
                return '';
            }else{
                return 'display:none';
            }
        }else{
            header("location:../index.php", true, 301);
        }
        
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Affectation des frais</title>

    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="shortcut icon" type="image/x-icon" href="../../images/UNIGOM_W260px.jpg">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

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
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 m-0">
                            <div class="card shadow">
                                <div class="card-header d-flex flex bg-gray-200" style="justify-content: space-between;">
                                    <h5 class="m-0 font-weight-bolder text-primary text-center">LISTE DES ETUDIANTS</h5>
                                    <div class="dropdown">
                                        <button class="btn btn-primary dropdown-toggle" type="button" id="triggerId" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                                        <div class="dropdown-menu" aria-labelledby="triggerId">
                                            <h6 class="text-center">chercher par : </h6>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#myModal" style="<?php //restruct_user();?>">
                                                <i class="fa fa-search" aria-hidden="true"></i>  Etudiant</a>
                                            <a class="dropdown-item" href="#" data-target="#mod_affectation_fac_promotion" data-toggle="modal" style="<?php //restruct_user();?>"><i class="fa fa-search" aria-hidden="true"></i> Faculte & promotion</a>
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
            <!-- Footer -->
            <!-- footer -->
		  <?php include './footer.php';?>
        </div>
    </div>
    <a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>

    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog bg-dark">
            <div class="modal-content">
                <div class="modal-header bg-gray-100 text-secondary">
                <h4 class="modal-title text-center">Affectation des frais aux etudiants |</h4>
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
                                <option value="">- Faculté -</option>
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

    <!-- fenetre modal pour la deconnexion-->
    <?php include_once './modal_decon.php';?>

    <script src="js/mes_scripts/AffectationFrais.js"></script>

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
                        url: "../../includes/AffectationFraisEtud.php",
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
                        url: "../../includes/del_affect.php",
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
</body>
</html>