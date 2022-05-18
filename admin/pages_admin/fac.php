<?php
    session_start();
    require_once '../../includes/ConnexionBdd.class.php';
    require_once '../../includes/verification.class.php';
    //verification des sessions
    require_once './sessions.php';
    $p = "Ajout des sections";

    function restruct_user_admin(){
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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="author" content="Justin Micah">

    <title>SEctions</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"rel="stylesheet">
    <link rel="shortcut icon" href="../../images/ispt_kin.png" type="image/x-icon">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        <?php require_once 'menu.php'; ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
				<!-- menu user -->
                <?php require_once 'menu_user.php'; ?>
                <!-- main Content -->
                <div class="container-fluid">
                    <div class="card shadow">
                        <div class="card-body">
                            <div class="row">
                                
                                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                    <button class="btn btn-primary btn-sm mb-1" data-toggle="modal" data-target="#add_section">Ajouter une section</button>
                                    <section class="panel">
                                        <div class="panel-body">
                                            <table class="table table-bordered table-hover table-sm table-md table-lg" id="table_fac">
                                                <thead class="bg-gray-200">
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Sections</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="f_table"></tbody>
                                            </table>
                                        </div>
                                    </section>
                                </div>

                                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                    <button class="btn btn-primary btn-sm mb-1" data-toggle="modal" data-target="#add_depart">Ajouter un département</button>
                                    <section class="panel">
                                        <div class="panel-body">
                                            <table class="table table-bordered table-hover table-sm table-md table-lg" id="table_fac">
                                                <thead class="bg-gray-200">
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Département</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="f_table_depart">
                                                <?php
                                                    // require_once './ConnexionBdd.class.php';
                                                    $an =  ConnexionBdd::Connecter()->query("SELECT * FROM annee_acad GROUP BY annee_acad ORDER BY id_annee DESC LIMIT 1");
                                                    if($an->rowCount() > 0){
                                                        $an_r = $an->fetch();
                                                    }else{
                                                        $an_r['annee_acad'] = '';
                                                        die("Veuillez AJouter l annee académique");
                                                    }

                                                    $verif = ConnexionBdd::Connecter()->prepare("SELECT * FROM departement WHERE id_annee = ? ORDER BY departement ASC");
                                                    $verif->execute(array($an_r['id_annee']));
                                                    while($data = $verif->fetch()){
                                                        ?>
                                                        <tr>
                                                            <td id="id_departement"><?=$data['id_departement']?></td>
                                                            <td id="id_section" style="display:none"><?=$data['id_section']?></td>
                                                            <td id="departement"><?=$data['departement']?></td>
                                                            <td>
                                                                <button href="#" data-toggle="modal" data-target="#update_depart_" class="btn btn-primary btn-sm" id="modif_depart">
                                                                    <i class="fa fa-edit" aria-hidden="true"></i>
                                                                </button>

                                                                <button href="#" data-toggle="modal" data-target="#add_option" class="btn btn-primary btn-sm" id="add_option_option" title="Ajouter une option">
                                                                    <i class="fa fa-plus-circle" aria-hidden="true"></i>
                                                                </button>

                                                                <button href="#" data-toggle="modal" data-target="#list_options" class="btn btn-info btn-sm" id="modif_fac_l" title="Ajouter une option">
                                                                    <i class="fa fa-list" aria-hidden="true"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                    }
                                                ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </section>
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
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    <!-- update departement -->
    <div class="modal fade" id="update_depart_" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">mettre ajour un departement</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <form action="" method="post" id="update_departement">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="">departement</label>
                            <input type="text" name="_update_depart" id="_update_depart" class="form-control" placeholder="" aria-describedby="helpId">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                    <small id="response"></small>
                </form>
            </div>
        </div>
    </div>                                                

    <!-- Button trigger modal -->
    <div class="modal fade" id="list_options" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Liste des options</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <div class="modal-body">
                    <table class="table table-striped table-inverse">
                        <thead class="thead-inverse">
                            <tr>
                                <th>ID</th>
                                <th>Option</th>
                                <th>Promotion</th>
                                <th>Code</th>
                            </tr>
                            </thead>
                            <tbody id="option_table">
                            </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>                                               

    <!-- ajout option -->
    <div class="modal fade" id="add_option" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter une option</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <form action="" method="post" id="add_option_s">
                    <div class="modal-body">
                        <span>Ajouter une option dans le departement de <span id="d"></span></span>
                        <div class="form-group">
                            <label for="">Option</label>
                            <input type="text" name="Option_Option" id="Option_Option" class="form-control" placeholder="Option" aria-describedby="helpId">
                        </div>

                        <div class="form-group">
                            <label for="">Promotion</label>
                            <input type="text" name="Option_promotion" id="Option_promotion" class="form-control" placeholder="Promotion" aria-describedby="helpId">
                        </div>

                        <div class="form-group">
                            <label for="">code </label>
                            <input type="text" name="Option_code" id="Option_code" class="form-control" placeholder="code" aria-describedby="helpId">
                        </div>
                        <small id="error_option"></small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="add_section" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajout des sections</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <div class="modal-body">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <section class="panel bg-light p-2 text-secondary" style="border-radius: 5px;">
                            <div class="panel-heading text-center"> Ajout des sections</div>
                            <div class="panel-body">
                                <form class="form-signin  text-white text-center m-2" action="" method="post" id="f0rm-fac" style="<?php restruct_user_admin();?>">
                                    <input type="text" name="fac" placeholder="Sections" class="form-control" id="fac">
                                    <input type="submit" id="l" class="btn btn-primary btn-block mt-2" value="Ajouter" style="margin-top:3px;">
                                    <label id="error_s"></label>
                                </form>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- add_depart -->
    <div class="modal fade" id="add_depart" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajout de departement</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <form action="" method="post" id="add_depart">
                    <div class="modal-body bg-light">
                        <div class="form-group">
                            <label for="">departement</label>
                            <input type="text" class="form-control" id="n_depart" value="" placeholder="departement">
                        </div>
                        <div class="form-group">
                            <label for="">Section</label>
                            <select class="form-control" id="section_id">
                                <?php
                                    $an =  ConnexionBdd::Connecter()->query("SELECT * FROM annee_acad GROUP BY annee_acad ORDER BY id_annee DESC LIMIT 1");
                                    if($an->rowCount() > 0){
                                        $an_r = $an->fetch();
                                    }else{
                                        $an_r['id_annee'] = '';
                                        // die("Veuillez AJouter l annee académique");
                                    }
                                    $s = ConnexionBdd::Connecter()->prepare("SELECT * FROM sections WHERE id_annee = ?");
                                    $s->execute(array($an_r['id_annee']));
                                    
                                    while($data = $s->fetch()){
                                        ?>
                                            <option value="<?=$data['id_section']?>"><?=$data['section']?></option>
                                        <?php
                                    }
                                ?>
                            </select>
                        </div>
                        <span id="erreur_dep"></span>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Ajouter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>`

    <!-- Modal Erreur-->
    <div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Erreur</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <div class="modal-body">
                    <label id="error_s"></label>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- modifier une faculte -->
    <div class="modal fade" id="Modify_fac" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier la faculté</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" id="update_faculte">
                        <input type="hidden" name="id_fac_a_modifier" id="id_fac_a_modifier">
                        <div class="form-group">
                            <label for="">la faculté à modifier</label>
                            <input type="text" class="form-control" name="fac_a_modifier" id="fac_a_modifier" aria-describedby="helpId" placeholder="">
                            <small id="helpId_f" class="form-text text-muted"></small>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                            <button type="Submit" class="btn btn-primary">Mettre à jour</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- fenetre modal pour la deconnexion-->
    <?php include_once './modal_decon.php';?>
    <!-- other js -->
    <script src="js/mes_scripts/l_fac.js"></script>

    <script type="text/javascript">
        $("table").on('click', '#modif_fac_l', function() {
            b = $(this);
            m = $(this).parent();
            mm = $(m).parent();

            fac = mm.find("#fac_list");
            id_fac = mm.find("#id_fac_list");
            if(fac.html() != "" && id_fac.html()){
                $("#fac_a_modifier").val(fac.html());
                $("#id_fac_a_modifier").val(id_fac.html())
            }
        })
    </script>

    <!-- afficher les options d'un departement -->
    <script type="text/javascript">
        $("table").on('click', '#list_option_option', function() {
            $("#list_options").modal("toggle");
            b = $(this);
            m = $(this).parent();
            mm = $(m).parent();

            id_departement_ = mm.find("#id_departement");
            id_section_ = mm.find("#id_section");
            departement = mm.find("#departement");

            // rehete ajax
            const data  = {
                id_departement_:id_departement_.text(),
                id_section_:id_section_.text()
            };
            $.ajax({
                type: "GET",
                url: "../../includes/view_options.php",
                data: data,
                success: function (response) {
                    // alert(response);
                    $("#option_table").empty();
                    $("#option_table").html(response);
                    // if(response !=""){
                    //     $("#option_table").empty();
                    //     $("#option_table").append(data);
                    // }else{
                    //     $("#option_table").empty();
                    //     $("#option_table").parent().append("<caption>Pas de donnees pour l'instant.</caption>");
                    // }
                },
                error:function(e){

                }
            });
        });
    </script>

    <!-- //add_option_option -->
    <script type="text/javascript">
        $("table").on('click', '#add_option_option', function() {
            b = $(this);
            m = $(this).parent();
            mm = $(m).parent();

            id_departement_ = mm.find("#id_departement");
            id_section_ = mm.find("#id_section");
            departement = mm.find("#departement");
            $("#d").html(departement.text()).addClass('text-primary');
            // alert(id_departement_.text() +" - "+id_section_.text());

            $("#add_option_s").submit(function (e) { 
                e.preventDefault();
                const data  = {
                    section_id:id_section_.text(),
                    id_departement_:id_departement_.text(),
                    Option_Option:$("#Option_Option").val(),
                    Option_promotion:$("#Option_promotion").val(),
                    Option_code:$("#Option_code").val()
                };

                if($("#Option_Option").val() !="" && $("#Option_promotion").val() !="" && $("#Option_code").val() !="" && id_departement_.text() !="" && id_section_.text() !=""){
                    $.ajax({
                        type: "POST",
                        url: "../../includes/add_option.php",
                        data: data,
                        success: function (response) {
                            if(response !="" && response == "ok"){
                                $("#error_option").html("ok").removeClass('text-danger').addClass('text-success');
                                window.location.reload();
                            }else{
                                $("#error_option").html("Erreur : "+response).addClass('text-danger');
                            }
                        },error: function(e){
                            $("#error_option").html("Erreur de connexion, try later,...").addClass('text-danger');
                        }
                    });
                }else{
                    $("#error_option").html("Veuillez completer tous les champs.").addClass('text-danger');
                }
            });
        });
    </script>

    <!-- mettre a jour un departement -->
    <script type="text/javascript">
        $("table").on('click', '#modif_depart', function() {
            b = $(this);
            m = $(this).parent();
            mm = $(m).parent();

            id_departement = mm.find("#id_departement");
            id_section = mm.find("#id_section");
            departement = mm.find("#departement");

            $("#_update_depart").val(departement.text());

            $("#update_departement").submit(function (e) { 
                e.preventDefault();
                const data = {
                    departement:$("#_update_depart").val(),
                    id_departement:id_departement.text()
                };

                $.ajax({
                    type: "POST",
                    url: "../../includes/update_departement.php",
                    data: data,
                    success: function (response) {
                        if(response !="" && response == "ok"){
                            window.location.reload();
                        }else{
                            $("#response").html(response);
                        }
                    },error:function(e){
                        $("#response").html("Erreur de connexion.");
                    }
                });
            });
        });
    </script>

    <!-- ajout d un departement -->
    <script>
        $("#add_depart").submit(function (e) { 
            e.preventDefault();
            if($("#n_depart").val() !="" && $("#section_id").val() !=""){
                const data = {
                    n_depart:$("#n_depart").val(),
                    section_id:$("#section_id").val()
                };

                $.ajax({
                    type: "POST",
                    url: "../../includes/depart.php",
                    data: data,
                    success: function (response) {
                        if(response == "ok"){
                            $("#erreur_dep").html(response);
                            window.location.reload();
                        }else{
                            $("#erreur_dep").html(response);
                        }
                    },
                    error:function(e){
                        $("#erreur_dep").html("Erreur du reseau.");
                    }
                });
            }else{
                $("#erreur_dep").html("Veuillez completer tous les champs svp.");
            }
        });
    </script>
</body>

</html>