<?php
    session_start();
    require_once '../../includes/ConnexionBdd.class.php';
    require_once '../../includes/verification.class.php';
    //verification des sessions
    require_once './sessions.php';
    $p = "Ajout des faculte";

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

    <title>Faculte</title>
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
                                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                                    <section class="panel bg-dark p-2 text-white" style="border-radius: 5px;">
                                        <div class="panel-heading text-center"> Ajout des Facultés</div>
                                        <div class="panel-body">
                                            <form class="form-signin  text-white text-center m-2" action="" method="post" id="f0rm-fac" style="<?php restruct_user_admin();?>">
                                                <input type="text" name="fac" placeholder="Faculté" class="form-control" id="fac">
                                                <input type="submit" id="l" class="btn btn-primary btn-block mt-2" value="Ajouter" style="margin-top:3px;">
                                                <label id="error_s"></label>
                                            </form>
                                        </div>
                                    </section>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-7 col-lg-7">
                                    <section class="panel">
                                        <div class="panel-body">
                                            <table class="table table-bordered table-hover table-sm table-md table-lg" id="table_fac">
                                                <thead class="bg-gray-200">
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Faculté</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="f_table"></tbody>
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
    <!-- <script src="../../js/jquery-3.6.0.min.js"></script> -->
    <script type="text/javascript">
        $(document).ready(function () { 
            $("#f_table").slideDown(30000);
         });
    </script>

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
</body>

</html>