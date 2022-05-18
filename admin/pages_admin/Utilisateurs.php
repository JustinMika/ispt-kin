<?php
    session_start();
    require_once '../../includes/ConnexionBdd.class.php';
    require_once '../../includes/verification.class.php';
    //verification des sessions
    require_once './sessions.php';
    require_once '../../includes/log_user.class.php';
    $p = "Gestion des utilisateurs";

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
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- <meta charset="utf-8"> -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Gestion utilisateurs</title>
    <link rel="shortcut icon" href="../../images/ispt_kin.png" type="image/x-icon">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i 800,800i,900,900i" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <script
  src="https://code.jquery.com/jquery-3.6.0.min.js"
  integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
  crossorigin="anonymous"></script>
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
                        <div class="card-header d-flex">
                            <button type="button" class="btn btn-primary btn-sm mb-3" data-toggle="modal" data-target="#modelId" style="<?=restruct_user()?>">Ajouter un utilisateur</button>
                            <h4 class="ml-5">Utilisateurs</h4>
                        </div>
                        <div class="card-body m-0 p-1">
                            <div class="container m-0 p-0">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-12 ">
                                        <div class="card">
                                            <div class="card-body p-2">
                                                <table class="table table-bordered table-hover table-sm table-md table-lg">
                                                    <thead class="bg-gray-200">
                                                        <tr>
                                                            <th>#ID</th>
                                                            <th>Profil</th>
                                                            <th>Noms</th>
                                                            <th>E-mail</th>
                                                            <th>Fonction</th>
                                                            <th>Droit Access</th>
                                                            <th>actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody >
                                                        <?php
                                                            $list_user = ConnexionBdd::Connecter()->query("SELECT * FROM utilisateurs ORDER BY access ASC");
                                                            while($data = $list_user->fetch()){
                                                                ?>
                                                                    <tr>
                                                                        <td id="id_user"><?=$data['id']?></td>
                                                                        <td>
                                                                            <img src="<?=$data['profil']?>" class="img-fluid rounded-top,rounded-right,rounded-bottom,rounded-left,rounded-circle" alt="" width="50">
                                                                        </td>
                                                                        <td id="noms_user"><?=$data['noms']?></td>
                                                                        <td id="email_user"><?=$data['email']?></td>
                                                                        <td id="fonction_user"><?=$data['fonction']?></td>
                                                                        <td id="user"><?=$data['access']?></td>
                                                                        <td class="d-flex">
                                                                            <div class="dropdown open"  style="<?php if($_SESSION['data']['id_user'] == $data['id'] || $_SESSION['data']['fonction'] == "Admin"){echo'';}else{echo'display:none';}?>">
                                                                                <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="triggerId" data-toggle="dropdown" aria-haspopup="true"
                                                                                        aria-expanded="false">Update</button>
                                                                                <div class="dropdown-menu" aria-labelledby="triggerId" style="<?php //restruct_user()?>">
                                                                                    <button class="dropdown-item btn-sm" href="#" data-toggle="modal" data-target="#update_identifiant" id="update_identifiant_btn" style="<?php if($_SESSION['data']['id_user'] == $data['id'] || $_SESSION['data']['fonction'] == "Admin"){echo'';}else{echo'display:none';}?>">Profil</button>
                                                                                    <button class="dropdown-item btn-sm" data-toggle="modal" data-target="#update_modal"id="update_id_" style="<?php if($_SESSION['data']['id_user'] == $data['id'] || $_SESSION['data']['fonction'] == "Admin"){echo'';}else{echo'display:none';}?>">Identifiants</button>
                                                                                </div>
                                                                            </div>
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
                                    <!--  -->
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-muted">...</div>
                    </div>
                </div>
                <div class="container-fluid mt-3" style="<?=restruct_r()?>">
                    <div class="card shadow">
                        <div class="card-header bg-gray-200">
                            <h4 class="text-center text-secondary">Journal d'activites des utilisateurs</h4>
                        </div>
                        <div class="card-body pt-2">
                            <div class="container p-2">
                                <div class="row">
                                    <!-- utilisateur -->
                                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                                        <div class="card">
                                            <div class="card-header bg-gray-200 p-2"> 
                                                <h3>Utilisateurs</h3>
                                            </div>
                                            <div class="card-body p-0">
                                            <table class="table table-bordered table-hover table-stripted" style="width: 100%">
                                                <thead class="thead-inverse">
                                                    <tr>
                                                        <th>#ID</th>
                                                        <th>noms</th>
                                                        <th>profil</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                        $list_user = ConnexionBdd::Connecter()->query("SELECT * FROM utilisateurs ORDER BY access ASC");
                                                        while($data = $list_user->fetch()){
                                                            ?>
                                                                <tr>
                                                                    <td class="m-3" id="id"><?=$data['id']?></td>
                                                                    <td class="m-3" id="noms"><?=$data['noms']?></td>
                                                                    <td>
                                                                        <img src="<?=$data['profil']?>" class="img-fluid rounded-top,rounded-right,rounded-bottom,rounded-left,rounded-circle" alt="" width="50">
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

                                    <!-- log utilisateur -->
                                    <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
                                        <div class="card">
                                            <div class="card-header bg-gray-200 text-center p-1 d-flex flex" style="justify-content: space-between;">
                                                <h3>Journal d'activites</h3>
                                                <a href="rapport_pdf/log.php" class="btn btn-sm btn-primary mb-1 mt-1 mr-1" target="_blank" style="<?=restruct_r()?>">Telecharger le log</a>
                                            </div>
                                            <div class="card-body p-0">
                                            <table class="table table-bordered table-hover" id="log_user" style="width:100%">
                                                <thead class="thead-inverse">
                                                    <tr>
                                                        <th>#ID</th>
                                                        <th>noms</th>
                                                        <th>date& Heure</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                        $list_user = ConnexionBdd::Connecter()->query("SELECT * FROM log_admin_user ORDER BY date_action, id, noms ASC");
                                                        while($data = $list_user->fetch()){
                                                            ?>
                                                                <tr>
                                                                    <td class=""><?=$data['id']?></td>
                                                                    <td class=""><?=utf8_decode($data['noms'])?></td>
                                                                    <td class=""><?=date("d/m/Y à H:m:s",strtotime($data[2]))?></td>
                                                                    <td class=""><?=utf8_decode($data['actions'])?></td>
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
                    </div>
                </div>
                <!-- fin main content-->
            </div>
            <!-- Footer -->
            <!-- footer -->
		  <?php include './footer.php';?>
        </div>
    </div>
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    
    
    <!-- update mot de passe utilisateur -->
    <div class="modal fade" id="update_modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier le mot de passe</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <form action="" method="POST" id="form_update_f">
                    <p class="text-info ml-4 text-center">changement du mot de passe pour : <span id="u"></span></p>
                    <div class="modal-body">
                        <input type="hidden" name="id_user_id" id="id_user_id">
                        <div class="form-group">
                          <label for="">mot de passe</label>
                          <input type="password"
                            class="form-control" name="n_pwd_1" id="n_pwd_1" aria-describedby="helpId" placeholder="mot de passe">
                        </div>
                        <div class="form-group">
                          <label for="">confirmer le mot de passe</label>
                          <input type="password"
                            class="form-control" name="n_pwd_2" id="n_pwd_2" aria-describedby="helpId" placeholder="mot de passe">
                        </div>
                    </div>
                    <div class="ml-3">
                        <small id="err_t3" class="text-danger"></small><br/>
                        <small id="err_t31" class="text-danger"></small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="btn_update_pwd">Modifier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- update identifiant -->
    <div class="modal fade" id="update_identifiant" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Mettre a jour mon profil</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <form action="" method="post" id="update_ident">
                    <div class="modal-body">
                        <input type="hidden" name="id_update_xy_user" id="id_update_xy_user">
                        <div class="form-group">
                          <label for="">Noms</label>
                          <input type="text"
                            class="form-control" name="name_ident" id="name_ident" placeholder="Noms" required>
                        </div>
                        <div class="form-group">
                          <label for="">E-mail</label>
                          <input type="email"
                            class="form-control" name="mail_ident" id="mail_ident" placeholder="E-mail" reqclass="form-control" name="name_ident" id="name_ident" placeholder="Noms" required>
                        </div>
                    </div>
                    <small id="erreur_update_user"></small>
                    <div class="modal-footer" id="sthdgfdf">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Mettre a jour</button>
                    </div>
                </form>
                <body>
                    <div class='container text-success' style="width: 100% !important;">
                        <span id="st">mise a jours reussi. Nous allons recharger la page dans :</span> <h5 class='timer' data-seconds-left=3 style="width: 100% !important;"></h5>
                        <section class='actions'></section>
                    </div>
                </body>
            </div>
        </div>
    </div>
    
    <script>
        $('#exampleModal').on('show.bs.modal', event => {
            var button = $(event.relatedTarget);
            var modal = $(this);
            // Use above variables to manipulate the DOM
            
        });
    </script>

    <script type="text/javascript">
        $("#update_pwd_user").click(function (e) { 
            e.preventDefault();
            $("#modeUpdate_pwd").modal('toggle');
        });

        $('table').on('click', '#update_id_', function(e){
            b = $(this);
            m = $(this).parent();
            mm = $(m).parent();
            mmm = $(mm).parent();
            b = $(mmm).parent();
            noms_user = b.find("#noms_user");

            var id = $(b).find('#id_user');
            // alert(mmm.text());
            $("#id_user_id").val(id.text());
            $("#u").html(noms_user.text());
        });

        $("#form_update_f").submit(function (e) { 
            e.preventDefault();
            // alert($("#id_user").val());
           if($("#n_pwd_1").val() !="" && $("#n_pwd_2").val() !="" && $("#id_user_id").val() !="") {
               const data = {
                   id_user : $("#id_user_id").val(),
                   n_pwd_1 : $("#n_pwd_1").val(),
                   n_pwd_2 : $("#n_pwd_2").val()
               };
                $.ajax({
                    type: "POST",
                    url: "../../includes/update_pwd_user.php",
                    data: data,
                    success: function (response) {
                        if(response !="" && response == "ok"){
                            $("#err_t3").text("ok, ...");
                            window.location.reload();
                        }else{
                            $("#err_t3").text(response);
                        }
                    },
                    error: function (e){
                        $("#err_t3").text("Verifier votre connexion svp !!!.");
                    }
                });
           }else{
                $("#err_t3").text("Veuillez remplir tous les champs svp !!!.");
                $("#btn_update_pwd").attr('disabled', 'true');
           }
        });
        $("#n_pwd_1").keyup(function (e) { 
            console.log($("#n_pwd_1").val());
            if($("#n_pwd_1").val().length >= 8){
                $("#err_t3").css({display:'block', color:'green'}).html("mot de passe valide");
                $("#n_pwd_1").css('border-color', 'green');
                $("#btn_update_pwd").removeAttr('disabled');
            }else{
                $("#err_t3").css({display:'block', color:'red'}).html("un mot de passe doit avoir au moins 8 caracteres");
                $("#n_pwd_1").css('border-color', 'red');
                $("#btn_update_pwd").attr('disabled', 'true');
            }
        });

        $("#n_pwd_2").keyup(function (e) { 
            if($("#n_pwd_2").val().length >= 8){
                $("#err_t3").css({display:'block', color:'green'}).html("mot de passe valide");
                $("#n_pwd_2").css('border-color', 'green');
                $("#btn_update_pwd").removeAttr('disabled');
            }else{
                $("#err_t3").css({display:'block', color:'red'}).html("un mot de passe doit avoir au moins 8 caracteres");
                $("#n_pwd_2").css('border-color', 'red');
                $("#btn_update_pwd").attr('disabled', 'true');
            }

            if($("#n_pwd_2").val() != $("#n_pwd_1").val()){
                $("#err_t31").text("les deux mot de passe ne correspondent pas.");
                $("#btn_update_pwd").attr('disabled', 'true');
            }else{
                $("#btn_update_pwd").removeAttr('disabled');
                $("#err_t31").removeClass('text-danger');
                $("#err_t31").text("ok::les deux mot de passe  correspondent.").addClass('text-info');
            }
        });
    </script>

    

    <!-- fenetre pour ajouter un utilisateur admin. -->
    <div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" style="text-transform: capitalize;">utilisateur administrateur</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="" id="form_admin_user">
                    <div class="modal-body">
                        <div class="">
                            <div class="form-group">
                                <label for="">Nom</label>
                                <input type="text" name="user_name" id="user_name" class="form-control" placeholder="Nom utilisateur" required>
                            </div>

                            <div class="form-group">
                                <label for="">Fonction</label>
                                <select name="fonction" class="form-control" id="fonction" required>
                                    <option value="AB">AB</option>
                                    <option value="Comptable">Comptable</option>
                                    <option value="Caissier">Agent budget control</option>
                                    <option value="Sec. de fac.">Sec. de fac.</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="">Access</label>
                                <select name="Access" class="form-control" id="Access" required>
                                    <!-- <option value="Admin">Admin</option> -->
                                    <option value="utilisateur">utilisateur</option>
                                    <option> ---------- </option>
                                    <!-- on charge les differenrs faculte se trouvabnt dans la base de donnees -->
                                    <?php
                                        $fac = ConnexionBdd::Connecter()->query("SELECT DISTINCT fac FROM faculte");
                                        while($d = $fac->fetch()){
                                            echo '
                                                <option value="'.$d['fac'].'">'.$d['fac'].'</option>';
                                        } 
                                    ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="">E-mail</label>
                                <input type="email" name="mail_user" id="mail_user" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="">Mot de passe de base</label>
                                <input type="password" name="Accpass_useress" id="Accpass_useress" class="form-control" required min="8">
                            </div>
                            <label id="error_s"></label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary" id="btn_add">Ajouter l'utilisateur</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- delete user admin in database -->
    <div class="modal fade" id="Delete_user" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Suppression de l'utilisateur</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        voulez-vous vraiment supprimer [] parmi les utilisateurs ?
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">NON</button>
                    <button type="button" class="btn btn-danger">OUI</button>
                </div>
            </div>
        </div>
    </div>
    <script src="js/mes_scripts/admin_script.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $("#st").hide();
            $("#error_s").css({
                display:'none'
            });
        });

        $("#mail_user").keyup(function (e) { 
            // console.log($("#mail_user").val());
        });
        $("#Accpass_useress").keyup(function (e) { 
            console.log($("#Accpass_useress").val());
            if($("#Accpass_useress").val().length >= 8){
                $("#error_s").css({display:'block', color:'green'}).html("mot de passe valide");
                $("#Accpass_useress").css('border-color', 'green');
                $("#btn_add").removeAttr('disabled');
            }else{
                $("#error_s").css({display:'block', color:'red'}).html("un mot de passe doit avoir au moins 8 caracteres");
                $("#Accpass_useress").css('border-color', 'red');
                $("#btn_add").attr('disabled', 'true');
            }
        });

        $('table').on('click', '#update_identifiant_btn', function(e){
            b = $(this);
            m = $(this).parent();
            mm = $(m).parent();
            mmm = $(mm).parent();
            b = $(mmm).parent();

            var id = $(b).find('#id_user');
            // alert(b.text());
            // console.log(b.html());
            noms_user = b.find("#noms_user");
            email_user = b.find("#email_user");
            // alert(noms_user.text());
            $("#id_update_xy_user").val(id.text());
            $("#name_ident").val(noms_user.text());
            $("#mail_ident").val(email_user.text());
        });

        $("#update_ident").submit(function (e) { 
            e.preventDefault();
            // verification
            if($("#name_ident").val() !="" && $("#mail_ident").val() !=""){
                const data = {
                    id : $("#id_update_xy_user").val(),
                    username : $("#name_ident").val(),
                    email : $("#mail_ident").val()
                };
                // ajax
                $.ajax({
                    type: "POST",
                    url: "../../includes/update_user_id.php",
                    data: data,
                    beforeSend: function(){
                        $("#erreur_update_user").html("Un instant svp ...").addClass('text-info');
                    },
                    success: function (response) {
                        if(response !="" && response == "ok"){
                            $("#st").show();
                            $("#sthdgfdf").hide();
                            $("#erreur_update_user").empty();
                            // $("#erreur_update_user").html("ok, succes").addClass('text-info');
                            // window.location.reload();
                            $(function(){
                                $('.timer').startTimer({
                                    onComplete: function(element){
                                        // $('html, body').addClass('bodyTimeoutBackground');
                                        window.location.reload();
                                    }
                                }).click(function(){ window.location.reload() });
                            });
                        }else{
                            $("#erreur_update_user").html(response).addClass('text-danger');
                        }
                    },
                    error: function(){
                        $("#erreur_update_user").html("Erreur de connexion,...").addClass('text-info');
                    }
                });
            }else{
                $("#erreur_update_user").html("Une erreur et survenue").addClass('text-danger');
            }
        });
    </script>
    <script src="js/DataTables/js/jquery.dataTables.min.js"></script>
	<script src="js/DataTables/js/dataTables.bootstrap.min.js"></script> -->
    <script type="text/javascript">
        $('#log_user').DataTable();
    </script>
    <?php include_once("modal_decon.php");?>
    <script src="./js/mes_scripts/jquery.simple.timer.js"></script>
</body>

</html>