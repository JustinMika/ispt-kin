<?php
    session_start();
    require_once '../../includes/ConnexionBdd.class.php';
    require_once '../../includes/verification.class.php';
    //verification des sessions
    require_once './sessions.php';
    $p = 'PAYEMENT DES FRAIS';

    function restruct_user(){
        if($_SESSION['data']['fonction'] != "" && $_SESSION['data']['access'] !=""){
            if($_SESSION['data']['access'] == "utilisateur" && $_SESSION['data']['fonction'] == "Agent budget controlsier"){
                return '';
            }else{
                return 'display:none';
            }
        }else{
            header("location:../index.php", true, 301);
        }
    }

    function m(){
        if($_SESSION['data']['fonction'] != "" && $_SESSION['data']['access'] !=""){
            if($_SESSION['data']['access'] == "utilisateur" && $_SESSION['data']['fonction'] != "Agent budget control"){
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
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <title>payements | Admin</title>

        <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
        <link rel="shortcut icon" href="../../images/ispt_kin.png" type="image/x-icon">
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
                        <div class="row" style="width: 100%;">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <div class="card">
                                    <div class="card-header d-flex flex" style="justify-content: space-between;">
                                        <h4 style="text-transform: uppercase;" class="h5 text-primary">payement des frais</h4>

                                        <div class="btn-group dropdown">
                                            <button type="button" class="btn btn-primary">Actions</button>
                                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <span class="sr-only">Toggle Dropdown</span>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" data-toggle="modal" href="#myModal_ppe" style="<?=restruct_r_ab()?>;"><i class="fa fa-upload" aria-hidden="true"></i> Payement par étudiant(e)</a>

                                                <a class="dropdown-item" data-toggle="modal" href="#Effect_payement" style="<?=restruct_r_ab()?>;"><i class="fa fa-plus" aria-hidden="true"></i> Effectuer un Payement</a>
                                                <!-- <a class="dropdown-item" data-toggle="modal" href="#myModal" style="<?=restruct_r_ab()?>;"><i class="fa fa-upload" aria-hidden="true"></i> Charger le Fichier excel ...</a> -->
                                                <a class="dropdown-item" data-toggle="modal" href="#histpay"><i class="fa fa-upload" aria-hidden="true"></i> historique de paiement</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body m-0 p-1">
                                        <table class="table table-hover" id="t_payement">
                                            <thead class="bg-secondary text-white">
                                                <tr>
                                                    <td>#</td>
                                                    <td style="font-weight: 500;"><i class="icon_profile"></i> Matricule</td>
                                                    <td>Option</td>
                                                    <td>Promotion</td>
                                                    <td>Type de frais</td>
                                                    <td>Numero du Bordereau</td>
                                                    <td>Montant</td>
                                                    <td>Date de Payement</td>
                                                    <td>Année Acad.</td>
                                                    <td> # </td>
                                                </tr>
                                            </thead>
                                            <tbody class="">
                                                <?php
                                                    $verif = ConnexionBdd::Connecter()->query("SELECT * FROM payement ORDER BY date_payement, id_annee DESC");
                                                    while($data = $verif->fetch()){
                                                        ?>
                                                            <tr>
                                                                <td id="id_payement"><?=$data['id']?></td>
                                                                <td id="mat_etud"><?=$data['matricule']?></td>
                                                                <td id="faculte_"><?=utf8_decode($data['faculte'])?></td>
                                                                <td id="promotion_"><?=$data['promotion']?></td>
                                                                <td id="type_frais_"><?=utf8_decode($data['type_frais'])?></td>
                                                                <td id="num_bordereau"><?=utf8_decode($data['num_borderon'])?></td>
                                                                <td id="montant_a">$<?=$data['montant']?></label></td>
                                                                <td id="datepy"><?=$data['date_payement']?></td>
                                                                <td id="annee_acad_a"><?=$data['annee_acad']?></td>
                                                                <td style="<?=rr()?>;">
                                                                    <button type="button" class="btn btn-primary btn-sm m-1 p-1" data-toggle="modal" data-target="#mod_payement" aria-pressed="false" autocomplete="off" id="btn_mod_payement" title="Modifier">
                                                                        <i class="fa fa-edit" aria-hidden="true"></i>
                                                                    </button>

                                                                    <button type="button" class="btn btn-danger btn-sm m-1 p-1" data-toggle="modal" data-target="#mod_payement_delete" aria-pressed="false" autocomplete="off" id="btn_mod_delete" title="Supprimer">
                                                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        <?php
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
                <!-- footer -->
            <?php include './footer.php';?>
            </div>
        </div>
        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>
        <!-- fenetre modal pour uploader le fichier excel contenant les payement. -->
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog bg-dark">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title text-left">Upload le Fichier Excel</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-window-close text-danger" aria-hidden="true"></i></button>
                    </div>
                    <div class="modal-body">
                        <form enctype="multipart/form-data" method="post" action="" id="f_payement">
                            <input type="file" class="form-control mb-1" id="fichier_excel" placeholder="fichier excel" name="file_excel_upload" required="" autofocus="">
                            <input type="submit" name="upload_file_excel" class="form-control bg-success text-white btn btn-primary" value="Uploader">
                        </form>
                    </div>
                    <div class="modal-footer">
                        <div class="mr-5" style="float: left;" id="t3_form">
                            <div class="spinner-border text-primary" role="status" id="spinner"></div>
                            <span class="h4" id="span">Traitement encours ...</span>
                        </div>
                        <button id="btn_closeF" data-dismiss="modal" class="btn btn-danger" type="button" >Fermer</button>
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
                                                echo '
                                                    <option value="'.$data['promotion'].'">'.utf8_decode($data['promotion']).'</option>';
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select class="form-control" name="fac_etud_payemt" id="fac_etud_payemt" required>
                                        <option>--section--</option>
                                        <?php
                                            $list = ConnexionBdd::Connecter()->query("SELECT  DISTINCT * FROM `sections` ORDER BY section ASC");
                                            while($data = $list->fetch()){
                                                echo '<option value="'.$data['id_section'].'">'.$data['section'].'</option>';
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select class="form-control" name="type_frais_p_etud" id="type_frais_p_etud" required>
                                        <option>--Type de frais--</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <select class="form-control" name="annee_acad_pay_etud" id="annee_acad_pay_etud" required>
                                        <option>--Annee Academique--</option>
                                        <?php
                                            $list = ConnexionBdd::Connecter()->query("SELECT * FROM `annee_acad` ORDER BY id_annee DESC LIMIT 1");
                                            while($data = $list->fetch()){
                                                echo '<option value="'.$data['id_annee'].'">'.$data['annee_acad'].'</option>';
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

        <!-- erreur durant les payements : fichier Excel -->
        <div class="modal fade" id="modal_error" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true"  style="overflow-y: scroll;">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-danger">ERREUR</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="close_btn_2">
                                <span aria-hidden="true">&times;</span>
                            </button>
                    </div>
                    <div class="modal-body" style="overflow-y: scroll;">
                        <div>
                            <ul class="list-group"  id="erreur_t3_erreur"> </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="close_error" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- modifier un payement -->
        <div class="modal fade" id="mod_payement" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Modifier le payement d'un étudiant(e)</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post" style="font-size: small;" id="update_payement_etud">
                            <div class="form-group">
                                <input type="hidden" name="id_payement_etud" id="id_payement_etud" class="form-control" placeholder="Matricule" aria-describedby="helpId">
                                <input type="text" name="mat_update" id="mat_update" class="form-control" placeholder="Matricule" aria-describedby="helpId">
                            </div>
                            <!--  -->
                            <div class="form-group">
                                <input type="text" name="promotion_update" id="promotion_update" class="form-control" placeholder="Promotion" aria-describedby="helpId">
                            </div>
                            <!-- fac -->
                            <div class="form-group">
                                <input type="text" name="fac_update" id="fac_update" class="form-control" placeholder="Faculte" aria-describedby="helpId">
                            </div>
                            <!-- type de frais -->
                            <div class="form-group">
                                <input type="text" name="type_frais_update" id="type_frais_update" class="form-control" placeholder="Type de frais" aria-describedby="helpId">
                            </div>
                            <!-- Annee Acad. -->
                            <div class="form-group">
                                <input type="text" name="annee_acad_update" id="annee_acad_update" class="form-control" placeholder="Année Acad." aria-describedby="helpId">
                            </div>
                            <!-- montant -->
                            <div class="form-group">
                                <input type="text" name="num_b_update" id="num_b_update" class="form-control" placeholder="N. Bodereau" aria-describedby="helpId">
                            </div>
                            <!-- date payement -->
                            <div class="form-group">
                                <input type="date" name="date_p_update" id="date_p_update" class="form-control" placeholder="Montant" aria-describedby="helpId">
                            </div>
                            <!-- montant -->
                            <div class="form-group">
                                <input type="text" name="montant_update" id="montant_update" class="form-control" placeholder="Montant" aria-describedby="helpId">
                            </div>

                            <div id="r"></div>

                            <div class="modal-footer p-1">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                                <button type="submit" class="btn btn-primary" id="btn_update">Modifier</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- supprimer un payement -->
        <!-- modifier un payement -->
        <div class="modal fade" id="mod_payement_delete" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Supprimer un payement</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post" style="font-size: small;" id="delete_payement_etud">
                            <div class="form-group">
                                <p class="text-danger">Voulez-vous vraiment supprimer cette enregistrement ?</p>
                                <input type="hidden" name="id_payement_etud" id="id_payement_etud" class="form-control" placeholder="Matricule" aria-describedby="helpId">
                            </div>
                            <small id="error_delete"></small>
                            <div class="modal-footer p-1">
                                <button type="button" class="btn btn-primary" data-dismiss="modal">Non</button>
                                <button type="submit" class="btn btn-danger" id="btn_update">Oui</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- fenetre modal pour la deconnexion-->
        <?php include_once './modal_decon.php';?>

        <div class="modal fade" id="myModal_ppe" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Payement</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="" method="post" id="payement_form_pay">
                        <div class="modal-body m-0 pl-2 pr-2">
                            <div class="" id="">
                                <div class="form-group">
                                <label for="">Matricule de l'étudiant(e)</label>
                                <input type="text"
                                    class="form-control" name="mat_etudiants_" id="mat_etudiants_" aria-describedby="helpId" placeholder="Matricule de l'étudiant(e)" required>
                                </div>
                                
                                <!-- annee academique -->
                                <div class="form-group m-0 p-0 mt-1">
                                    <label for="">Année academique</label>
                                    <select class="form-control" id="annee_acad_pay" name="annee_acad_pay" required>
                                        <?php
                                            $a = ConnexionBdd::Connecter()->query("SELECT id_annee, annee_acad FROM annee_acad ORDER BY id_annee DESC LIMIT 1");
                                            while($d = $a->fetch()){
                                                echo '<option value="'.$d['id_annee'].'">'.$d['annee_acad'].'</option>';
                                            }
                                        ?>
                                    </select>
                                </div>

                                <!-- code de l'option -->
                                <div class="form-group m-0 p-0">
                                    <input type="hidden" class="form-control" id="section_etud" name="section_etud" required />
                                    <input type="hidden" class="form-control" id="departement_etud" name="departement_etud" required />
                                    <input type="hidden" class="form-control" id="option_etu" name="option_etu" required />
                                </div>
                                <!-- type de frais -->
                                <div class="form-group m-0 p-0 mt-1">
                                    <label for="">Type de frais</label>
                                    <select class="form-control" id="type_d_frais" name="type_d_frais" required>
                                    </select>
                                </div>
                                <!-- num bordear -->
                                <div class="form-group m-0 p-0 mt-1">
                                    <input type="text" class="form-control" id="numer_border_" name="numer_border_" required placeholder="Numero du bordereau">
                                </div>
                                <!-- date payement -->
                                <div class="form-group m-0 p-0 mt-1">
                                    <input type="date" class="form-control" id="date_pay_etud" name="date_pay_etud" required>
                                </div>
                                <!-- montant -->
                                <div class="form-group m-0 p-0 mt-1">
                                    <input type="text" class="form-control" id="montant_pay_et" name="montant_pay_et" required placeholder="Montant : ">
                                </div>
                                <small id="error_payement"></small>
                            </div>
                        </div>
                        <div class="modal-footer m-0 p-0">
                            <button type="button" class="btn btn-secondary" id="btn_close" data-dismiss="modal">Fermer</button>
                            <button type="submit" class="btn btn-primary" id="btn_payem_">Effectuer le payement</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="histpay" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Historique de payement d'un(e) étudiant(e)</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="" method="post" id="historique_pay">
                        <div class="modal-body">
                            <div class="" id="se">
                                <div class="form-group">
                                <label for="">Matricule de l'étudiant</label>
                                <input type="text"
                                    class="form-control" name="mat_etu" id="mat_etu" aria-describedby="helpId" placeholder="" required>
                                <small id="helpId" class="form-text text-muted"></small>
                                </div>

                                <div class="form-group">
                                    <label for="">Année academique</label>
                                    <select class="form-control" id="annee_acad" name="annee_acad" required>
                                        <?php
                                            $a = ConnexionBdd::Connecter()->query("SELECT id_annee, annee_acad FROM annee_acad");
                                            while($d = $a->fetch()){
                                                echo '<option value="'.$d['id_annee'].'">'.$d['annee_acad'].'</option>';
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" id="btn_close" data-dismiss="modal">Fermer</button>
                            <button type="submit" class="btn btn-primary" id="btn_s">Chercher</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- payement des etudiants(e) -->
        <script>
            // mat_etudiants_   annee_acad_pay
            /**code_section
            type_d_frais */
            $("#btn_payem_").attr('disabled', true);
            $("#mat_etudiants_").keyup(function (e) { 
                if($("#annee_acad_pay").val() !=""){
                    getInfoEtud();
                }else{
                    $("#error_payement").html("Veuillez selectionner une année académique");
                    $("#btn_payem_").attr('disabled', true);
                }
            });

            $("#annee_acad_pay").change(function (e) { 
                e.preventDefault();
                if($("#mat_etudiants_").val() !=""){
                    getInfoEtud();
                }else{
                    $("#error_payement").html("Veuillez Enter le mat. de l' étudiant");
                    $("#btn_payem_").attr('disabled', true);
                }
            });

            function getInfoEtud(){
                const data = {
                    get_code:"get_code",
                    a:$("#mat_etudiants_").val(),
                    f:$("#annee_acad_pay").val()
                };
                $.ajax({
                    type: "GET",
                    url: "../../includes/select_std.php",
                    data: data,
                    success: function (data) {
                        if(data !=""){
                            try {
                                const obj_data = JSON.parse(data);
                                $("#section_etud").val(obj_data.id_section);
                                $("#departement_etud").val(obj_data.id_departement);
                                $("#option_etu").val(obj_data.id_option);

                                $("#error_payement").html("l'etudiant(e) "+obj_data.noms).addClass('text-success');
                                if($("#section_etud").val() !="" && $("#option_etu").val() !="" && $("#departement_etud").val() !=""){
                                    getFraisEtud();
                                }else{
                                    $("#type_d_frais").empty();
                                }
                            } catch (err) {
                                $("#error_payement").html("Erreur").removeClass('text-success').addClass('text-danger');
                                $("#btn_payem_").attr('disabled', true);
                                $("#type_d_frais").empty();
                            }
                        }else{
                            $("#error_payement").html("l'etudiant n'est pas inscrit(e) dans "+$("#annee_acad_pay option:selected").text() +" - "+ data);
                            $("#type_d_frais").empty();
                            $("#btn_payem_").attr('disabled', true);
                        }
                    },
                    error: function(e){
                        $("#error_payement").html("Erreur de connexion ...");
                        $("#type_d_frais").empty();
                        $("#btn_payem_").attr('disabled', true);
                    }
                });
            }

            function getFraisEtud(){
                if($("#section_etud").val() !="" && $("#option_etu").val() !="" && $("#departement_etud").val() !="" && $("#mat_etudiants_").val() !="" && $("#annee_acad_pay").val() !=""){
                    const data_  = {
                        list_frais:"list_frais",
                        a:$("#section_etud").val(),
                        b:$("#option_etu").val(),
                        c:$("#departement_etud").val(),
                        d:$("#mat_etudiants_").val(),
                        e:$("#annee_acad_pay").val()
                    };
                    $.ajax({
                        type: "GET",
                        url: "../../includes/select_std.php",
                        data: data_,
                        success: function (response) {
                            if(response !=""){
                                $("#type_d_frais").empty();
                                $("#type_d_frais").append(response);
                            }else{
                                $("#error_payement").html("Aucun frais n'est affecté à l'étudiant(e)" +response).removeClass('text-success').addClass('text-danger');
                                $("#type_d_frais").empty();
                                $("#btn_payem_").attr('disabled', true);
                            }
                        },
                        error: function(e){
                            $("#type_d_frais").empty();
                            alert("Erreur de connexion.");
                        }
                    });
                }else{
                    $("#type_d_frais").empty();
                    alert("Rien");
                }
            }

            $("#montant_pay_et").keyup(function (e) { 
                var x = $("#montant_pay_et").val();
                if(!isNaN(x) && x >= 1 && x !="0" && x !="0,"){
                    if(x !=""){
                        $("#btn_payem_").removeAttr('disabled');
                        $("#error_payement").html('');
                        $("#error_payement").html('montant valide (: :)').css({color:'green'}).addClass('text-success');
                        console.log(x + "is a number");
                        $("#montant_pay_et").val(x);
                    }else{
                        $("#error_payement").html('');
                        $("#error_payement").html("une valeur est requis").addClass('text-danger');
                        $("#btn_payem_").attr('disabled', true);
                    }
                }else{
                    $("#error_payement").html('');
                    $("#error_payement").html("Veuillez saisir un montant valide.").addClass('text-danger');
                    $("#btn_payem_").attr('disabled', true);
                    console.log(x + "is not a number");
                }
            });

            $("#payement_form_pay").submit(function (e) { 
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    url: "../../includes/payement_etudiants_.php",
                    data: $(this).serializeArray(),
                    success: function (response) {
                        if(response !="" && response =="ok"){
                            $("#error_payement").html(response +" payement reussi avec success").addClass('text-danger');
                            window.location.reload();
                        }else{
                            $("#error_payement").html(response).addClass('text-danger');
                        }
                    },
                    error: function (response){
                        $("#error_payement").html("Erreur de connexion,..").addClass('text-danger');
                    }
                });
            });
        </script>
        
        <script>
            $("#mat_etu").show();
            $("#historique_pay").submit(function(e) {
                e.preventDefault();
                const data = {
                    mat_etu:$("#mat_etu").val(),
                    annee_acad:$("#annee_acad").val()
                };
                if($("#mat_etu").val() !=""){
                    $.ajax({
                        type: "POST",
                        url: "../../includes/hist_pay_etud.php",
                        data: data,
                        success: function(response) {
                            if (response != "") {
                                $("#se").empty();
                                $("#se").html(response);
                                $("#btn_s").hide();
                            } else {
                                alert("Erreur");
                            }
                        }, 
                        error: function(e){
                            alert("Erreur");   
                        }
                    });
                }
            });

            $("#btn_close").click(function(){
                $("#se").empty();
                $("#mat_etu").show();
                window.location.reload();
            });
        </script>
        
        <script src="js/sb-admin-2.min.js"></script>

        <script src="js/mes_scripts/payement.js"></script>
        <script type="text/javascript" language="javascript">
            $("#t3_form").css({
                display:'none'
            });
        </script>

        <script src="js/DataTables/js/jquery.dataTables.min.js"></script>
        <script src="js/DataTables/js/dataTables.bootstrap.min.js"></script>

        <script>
            $(document).ready(function() {
                $('#t_payement').DataTable();

                $("#close_error").click(function (e) { 
                    e.preventDefault();
                    window.location.reload();
                });

                $("#close_btn_2").click(function (e) { 
                    e.preventDefault();
                    window.location.reload();
                });
            } );

            $('table').on('click', '#btn_mod_payement', function(e){
                e.preventDefault();

                b = $(this);
                m = $(this).parent();
                mm = $(m).parent();

                $("#mat_update").val(mm.find("#mat_etud").text()).attr('disabled', true);
                $("#promotion_update").val(mm.find("#promotion_").text()).attr('disabled', true);
                $("#fac_update").val(mm.find("#faculte_").text()).attr('disabled', true);
                $("#type_frais_update").val(mm.find("#type_frais_").text()).attr('disabled', true);
                $("#annee_acad_update").val(mm.find("#annee_acad_a").text()).attr('disabled', true);
                $("#num_b_update").val(mm.find("#num_bordereau").text()).attr('disabled', true);
                $("#date_p_update").val(mm.find("#datepy").text());
                $("#montant_update").val(mm.find("#montant_a").text());
                $("#id_payement_etud").val(mm.find("#id_payement").text());

                var x = montant_a.text();
                if(!isNaN(x) && x >= 1 && x !="0" && x != "0,"){
                    if(x !=""){
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

            $("#btn_update").attr('disabled', true);
            $("#montant_update").keyup(function (e) { 
                var x = $("#montant_update").val();
                if(!isNaN(x) && x >= 0 && x !="0" && x !="0,"){
                    if(x !=""){
                        $("#btn_update").removeAttr('disabled');
                        $("#r").html('');
                        $("#r").html('montant valide (: :)').css({color:'green'}).addClass('text-success');
                        console.log(x + "is a number");
                        $("#montant_update").val(x);
                    }else{
                        $("#r").html('');
                        $("#r").html("une valeur est requis").addClass('text-danger');
                        $("#btn_update").attr('disabled', true);
                    }
                }else{
                    $("#r").html('');
                    $("#r").html("Veuillez saisir un montant valide.").addClass('text-danger');
                    $("#btn_update").attr('disabled', true);
                    console.log(x + "is not a number");
                }
            });     
        </script>

        <!-- supprimer un payement -->
        <script>
            $('table').on('click', '#btn_mod_delete', function(e){
                e.preventDefault();

                b = $(this);
                m = $(this).parent();
                mm = $(m).parent();

                id_payement = mm.find("#id_payement");

                // alert(id_payement.text());
                $("#id_payement_etud").val(id_payement.text());

                $("#delete_payement_etud").submit(function (e) { 
                    e.preventDefault();
                    const data = {
                        id_payement:$("#id_payement_etud").val()
                    };
                    if($("#id_payement_etud").val() !=""){
                        $.ajax({
                            type: "POST",
                            url: "../../includes/delete_payement.php",
                            data: data,
                            beforeSend:function(){
                                $("#error_delete").removeClass('text-danger');
                                $("#error_delete").html("Un instant svp ...").addClass('text-success');
                            },
                            success: function (response) {
                                if(response !="" && response == "ok"){
                                    $("#error_delete").removeClass('text-danger');
                                    $("#error_delete").html("ok").addClass('text-success');
                                    window.location.reload();
                                }else{
                                    $("#error_delete").removeClass('text-success');
                                    $("#error_delete").html("Erreur : "+response).addClass('text-danger');
                                }
                            },
                            error: function(e){
                                $("#error_delete").removeClass('text-success');
                                $("#error_delete").html("Erreur de connexion.").addClass('text-danger');
                            }
                        });
                    }
                });
            });
        </script>
    </body>
</html>