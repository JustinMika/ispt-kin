<?php
    session_start();
    require_once '../../includes/ConnexionBdd.class.php';
    require_once '../../includes/verification.class.php';
    require_once '../../includes/log_user.class.php';
    //verification des sessions
    require_once './sessions.php';
    $p = "Annee Academique";
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Annee Academique</title>

    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="shortcut icon" href="../../images/ispt_kin.png" type="image/x-icon">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

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
                    <div class="card shadow">
                        <div class="card-header"></div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-3">
                                    <section class="panel border-left-dark p-2 bg-dark">
                                        <div class="panel-heading">Annee Academique </div>
                                        <div class="panel-body">
                                            <form class="form-signin  text-white text-center m-2" action="" method="post" id="f0rm">
                                                <input type="text" name="annee_acad" placeholder="2020-2021" class="form-control" id="annee_0_acad">
                                                <input type="submit" name="l" class="btn btn-primary btn-block mt-2" value="Ajouter" style="margin-top:3px;">
                                                <label for="" id="error_s"></label>
                                            </form>
                                        </div>
                                    </section>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                    <section class="panel">
                                        <div class="panel-body">
                                            <table class="table table-hover table-dark" id="table_annee_acad">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Annee Academique</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="b_table">
                                                </tbody>
                                                <caption><label id="error"></label></caption>
                                            </table>
                                            
                                        </div>
                                    </section>
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
    
    <!-- Modal -->
    <div class="modal fade" id="MyModalModif" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Mise a jour de l' annee academique</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" id="update_annee_acad">
                        <div class="form-group">
                            <input type="hidden" name="id_m_annee_acad" id="id_m_annee_acad">
                        <label for="">annee academique</label>
                        <input type="text"
                            class="form-control form-control-sm" name="update_ann_acad" id="update_ann_acad" placeholder="annee academique">
                        <small id="helpId_error" class="form-text text-muted"></small>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                            <button type="submit" class="btn btn-primary">Mettre a jour</button>
                        </div>
                    </form>
                </div>
                
            </div>
        </div>
    </div>
    <script src="../../js/jquery-3.6.0.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    <script type="text/javascript">
        $('table').on('click', 'a', function() {
            b = $(this);
            m = $(this).parent();
            mm = $(m).parent();

            fac = mm.find("#annee_acad");
            id_fac = mm.find("#id_annee_acad");

            if(fac.html() != "" && id_fac.html()){
                $("#id_m_annee_acad").val(id_fac.html());
                $("#update_ann_acad").val(fac.html())
            }
        })
    </script>
    <!-- fenetre modal pour la deconnexion-->
    <?php include_once './modal_decon.php';?>
    <!-- other script -->
    <script src="js/mes_scripts/l_promotion.js"></script>
</body>

</html>