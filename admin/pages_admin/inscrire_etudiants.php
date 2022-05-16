<?php
    session_start();
    require_once '../../includes/ConnexionBdd.class.php';
    require_once '../../includes/verification.class.php';
    //verification des sessions
    require_once './sessions.php';
    $p = 'Inscription des Etudiant(e)';
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Inscription des Etudiants</title>

    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="shortcut icon" type="image/x-icon" href="../../images/UNIGOM_W260px.jpg">
    <!-- <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet"> -->

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
                                <div class="card-header d-flex flex" style="justify-content: space-between;">
                                    <h5 class="" style="text-transform: uppercase;">Inscription des etudiants</h5>
                                    <div class="dropdown open">
                                        <button class="btn btn-primary dropdown-toggle" type="button" id="triggerId" data-toggle="dropdown" aria-haspopup="true"aria-expanded="false">Actions</button>
                                        <div class="dropdown-menu" aria-labelledby="triggerId">
                                            <button class="dropdown-item btn btn-success" data-toggle="modal" href="#mod_inscrire_etud" style="float: right;"><i class="fa fa-plus" aria-hidden="true"></i> Inscrire un etudiants</button>
                                            <button class="dropdown-item btn btn-success" data-toggle="modal" href="#myModal" style="float: right;"><i class="fa fa-upload" aria-hidden="true"></i> Uploader le fichier excel</button>
                                            <button class="dropdown-item btn btn-success" id="btn_seacr_etud"><i class="fa fa-search" aria-hidden="true"></i> Chercher un(e) étudiant(e)</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table class="table table-hover table-bordered" id="t_etudiants">
                                        <thead class="bg-secondary text-white">
                                            <tr  style="font-size: medium;">
                                                <th>Mat</th>
                                                <th>Noms</th>
                                                <th>Fac.</th>
                                                <th>Promotion</th>
                                                <th>Annee Acad.</th>
                                                <th>#</th>
                                            </tr>
                                        </thead>
                                        <tbody id="t_inscription_etudiants" class="" style="font-size: medium;">
                                        </tbody>
                                        <caption id="no-data"></caption>
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

    <!-- formulaire pour uploader le fichier excel pour les inscriptions -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog bg-dark">
            <div class="modal-content">
                <div class="modal-header text-secondary">
                <h4 class="modal-title text-center">Upload le Fichier Excel [inscription des etudiant(e)s]</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-window-close text-danger" aria-hidden="true"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form enctype="multipart/form-data" method="post" action="" id="form_inscription_etud">
                        <input type="file" class="form-control" id="fichier_excel" placeholder="fichier excel" name="file_excel_upload" required="" autofocus="">
                        <input type="hidden" name="n_fichier">
                        <input type="submit" name="upload_file_excel" class="form-control bg-success text-white btn btn-primary mt-2" value="Upload">
                    </form>
                </div>
                <div class="modal-footer mt-2" id="t3" style="font-size:80%">
                    <div class="mr-5" style="float: left;" id="t3_form" style="font-size:95%">
                        <div class="spinner-border text-primary" role="status" style="font-size:95%" id="spinner_sp"></div>
                        <span class="h4" id="span" style="font-size:95%">Traitement encours ...</span>
                    </div>
                    <p id="term"></p>
                    <button data-dismiss="modal" class="btn btn-danger" type="button" id="Fermer">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- modification -->
    <div class="modal fade" id="update_student" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Mise à jour des informations des étudiants</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <form action="" method="POST" id="form_submit_insc">
                    <div class="modal-body">
                        <!-- mat -->
                        <div class="form-group">
                            <input type="text" class="form-control" name="update_mat" id="update_mat" placeholder="matricule de l étudiant">
                        </div>
                        <!-- noms -->
                        <div class="form-group">
                            <input type="text" class="form-control" name="update_noms" id="update_noms" placeholder="noms de l étudiant">
                        </div>
                        <!-- fac -->
                        <div class="form-group">
                            <input type="text" class="form-control" name="update_fac" id="update_fac" placeholder="faculte de l étudiant">
                        </div>
                        <!-- promotion -->
                        <div class="form-group">
                            <input type="text" class="form-control" name="update_promotion" id="update_promotion" placeholder="promotion de l étudiant">
                        </div>
                        <!-- annee acad -->
                        <div class="form-group">
                            <input type="text" class="form-control" name="update_annee_acad" id="update_annee_acad" placeholder="annee acad">
                        </div>
                        <small id="error_ins"></small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"> <i class="fa fa-backward" aria-hidden="true"></i> Annuler</button>
                        <button type="submit" class="btn btn-primary">Mettre à jour</button>
                    </div>

                    <input type="hidden" name="old_fac" id="old_fac">
                    <input type="hidden" name="old_promotion" id="old_promotion">
                </form>
            </div>
        </div>
    </div>

    <!-- modification -->
    <div class="modal fade" id="update_student_pwd" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Mise à jour des le mot de passe des étudiants</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <form action="" method="POST" id="form_inscrire_etudiants_form__">
                    <div class="modal-body">
                        <span id="md"></span>
                        <!-- mat -->
                        <div class="form-group">
                            <input type="text" class="form-control" name="update_mat_mat" id="update_mat_mat" placeholder="matricule de l étudiant">
                        </div>
                        <!-- noms -->
                        <div class="form-group">
                            <input type="text" class="form-control" name="pwd_generate" id="pwd_generate" placeholder="mot de passe">
                        </div>
                        <small id="_error_ins_"></small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"> <i class="fa fa-backward" aria-hidden="true"></i> Annuler</button>
                        <button id="btn_gererate" class="btn btn-primary" title="Gererate pwd">
                            <i class="fa fa-random" aria-hidden="true"></i>
                        </button>
                        <button type="submit" class="btn btn-primary">Mettre à jour</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- delete -->
    <div class="modal fade" id="del_student" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Suppression des étudiants</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <form action="" method="POST" id="form_del_etud">
                    <input type="hidden" name="id_etud_del" id="id_etud_del">
                    <div class="modal-body">
                        <div id="info_del" class="text-danger"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Non</button>
                        <button type="submit" class="btn btn-danger">Oui</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- chercher un etudiant -->
    <div class="modal fade" id="m_btn_seacr_etud" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa fa-search" aria-hidden="true"></i> Chercher un(e) étudiant(e)</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <form action="" method="POST" id="form_search_etud">
                    <div class="modal-body">
                        <div class="" id="se">
                            <div class="form-group">
                                <label for="">Matricule de l'étudiant</label>
                                <input type="text"
                                class="form-control" name="mat_etu" id="mat_etu" aria-describedby="helpId" required placeholder="Matricule de l'étudiant">
                                <small id="helpId" class="form-text text-muted"></small>
                            </div>

                            <div class="form-group">
                                <label for="">Année academique</label>
                                <select class="form-control" id="annee_acad" name="annee_acad" required>
                                    <?php
                                        $a = ConnexionBdd::Connecter()->query("SELECT annee_acad FROM annee_academique");
                                        while($d = $a->fetch()){
                                            echo '<option value="'.$d['annee_acad'].'">'.$d['annee_acad'].'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" id="id_close">Close</button>
                        <button type="submit" class="btn btn-primary" id="btn_s"><i class="fa fa-search" aria-hidden="true"></i>  Chercher</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- modal pour inscrire les etudiants -->
    <div class="modal fade" id="mod_inscrire_etud" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Formulaire pour inscrire les Etudiants</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <form action="" method="post" id="form_inscrire_etudiants_form">
                    <div class="modal-body">
                        <div class="form-group">
                            <input type="text" class="form-control" name="Matricule_etud_ins" id="Matricule_etud_ins" aria-describedby="helpId" placeholder="Matricule" required>
                        </div>

                        <div class="form-group">
                            <input type="text" class="form-control" name="Noms_etud_ins" id="Noms_etud_ins" aria-describedby="helpId" placeholder="Noms de l etudiant" required>
                        </div>

                        <div class="form-group">
                            <input type="text" class="form-control" name="Password_etud_ins" id="Password_etud_ins" aria-describedby="helpId" placeholder="Password de l etudiant" required>
                        </div>

                        <div class="form-group">
                            <select type="text" class="form-control" name="Faculte_etud_ins" id="Faculte_etud_ins" required>
                                <option> -- Faculté -- </option>
                                <?php
                                    $an =  ConnexionBdd::Connecter()->query("SELECT * FROM annee_academique GROUP BY annee_acad ORDER BY id DESC LIMIT 1 ");
                                    $an_r = $an->fetch();

                                    if(isset($an_r) && !empty($an_r)){
                                        $ann = $an_r['annee_acad'];
                                    }else{
                                        $ann = '';
                                    }

                                    // selection de la faculte de la derniere annee academique
                                    $sel_etudiants = ConnexionBdd::Connecter()->prepare("SELECT fac FROM faculte WHERE annee_acad = ? GROUP BY fac");
                                    $sel_etudiants->execute(array($ann));
                                    $nbre = $sel_etudiants->rowCount();

                                    while($data = $sel_etudiants->fetch()){
                                        echo '<option value="'.$data['fac'].'">'.$data['fac'].'</option>';
                                    }
                                ?>
                            </select>

                        </div>

                        <div class="form-group">
                            <input type="text" class="form-control" name="Promotion_etud_ins" id="Promotion_etud_ins" aria-describedby="helpId" placeholder="Promotion de l etudiant" required>
                        </div>

                        <div class="form-group">
                            <?php
                                $an =  ConnexionBdd::Connecter()->query("SELECT annee_acad FROM annee_academique GROUP BY annee_acad ORDER BY id DESC LIMIT 1 ");
                                $an_r = $an->fetch();

                                if(!empty($an_r)){
                                    $ann = $an_r['annee_acad'];
                                }else{
                                    $ann = '';
                                }
                            ?>
                            <input type="text" class="form-control" name="annee_acad_etud_ins" id="annee_acad_etud_ins" aria-describedby="helpId" placeholder="annee_acad" required value="<?=$ann?>" disabled>
                        </div>
                    </div>
                    <small id="error_insc"></small>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Inscrire</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- fenetre modal pour la deconnexion-->
    <?php include_once './modal_decon.php';?>
    <script type="text/javascript" language="javascript">
        $("#t3_form").css({
            display:'none'
        }); 

        $("#btn_seacr_etud").click(function (e) { 
            e.preventDefault();
            $("#m_btn_seacr_etud").modal('toggle');
        });
	</script>

    <script src="js/mes_scripts/inscrire_etudiant.js"></script>

    <!-- update l'etudiant -->
    <script>
        $('table').on('click', '#btn_update_stud', function(e){
            e.preventDefault();

            $("#error_ins").html("").text("");

            b = $(this);
            m = $(this).parent();
            mm = $(m).parent();
            mmm = $(mm).parent();
            // alert(mmm.html());
            insc_id = mmm.find("#insc_id");
            

            insc_mat = mmm.find("#insc_mat");
            insc_noms = mmm.find("#insc_noms");
            insc_fac = mmm.find("#insc_fac");
            insc_promotion = mmm.find("#insc_promotion");
            insc_annee_acad = mmm.find("#insc_annee_acad");

            old_fac = mmm.find("#insc_fac");
            old_promotion = mmm.find("#insc_promotion");

            $("#update_mat").val(insc_mat.text()).attr('disabled', true).attr('required', true);
            $("#update_noms").val(insc_noms.text()).attr('required', true);
            $("#update_fac").val(insc_fac.text()).attr('required', true);
            $("#update_promotion").val(insc_promotion.text()).attr('required', true);
            $("#update_annee_acad").val(insc_annee_acad.text()).attr('disabled', true).attr('required', true);

            $("#old_fac").val(old_fac.text());
            $("#old_promotion").val(old_promotion.text());
        });

        // bloquer l envoi du formulaire pour inscrire manuellement les etudiants
        $("#form_inscrire_etudiants_form").submit(function (e) { 
            e.preventDefault();
            const data = {
                mat:$("#Matricule_etud_ins").val(),
                noms:$("#Noms_etud_ins").val(),
                pwd:$("#Password_etud_ins").val(),
                fac:$("#Faculte_etud_ins option:selected").text(),
                promotion:$("#Promotion_etud_ins").val(),
                annee_acad:$("#annee_acad_etud_ins").val()
            };
            $.ajax({
                type: "POST",
                url: "../../includes/inscrire_etud_form.php",
                data: data,
                beforeSend: function(){
                    $("#error_insc").text("Un instant svp ...").addClass('text-success');
                },
                success: function (response) {
                    if(response !="" && response == "ok"){
                        $("#error_insc").text("l etudiant(e) est bien enregistre(e)").addClass('text-success');
                        $("#Matricule_etud_ins").val('');
                        $("#Noms_etud_ins").val('');
                        $("#Password_etud_ins").val('');
                        $("#Promotion_etud_ins").val('');
                    }else{
                        $("#error_insc").text("l'Erreur suivante : "+response +" est survenue").addClass('text-danger');
                    }
                },
                error:function(e){
                    $("#error_insc").text("Erreur : la connexion n pas bonne.").addClass('text-danger');
                    $("#error_insc").append("Erreur de connexion.");
                    alert("Erreur de connexion.");
                }
            });
        });

        function generer_pwd_std(){
            var annee_acad = $("#annee_acad_etud_ins").val();
            var a1 = annee_acad.charAt(annee_acad.length - 1);
            var a2 = annee_acad.charAt(annee_acad.length - 2);
            var ann = a1+a2;

            var mat =$("#Matricule_etud_ins").val();
            var a1 = mat.charAt(mat.length - 1);
            var a2 = mat.charAt(mat.length - 2);
            var m = a1+a2;

            var noms =$("#Noms_etud_ins").val();
            var a1 = noms.charAt(noms.length - 1);
            var a2 = noms.charAt(noms.length - 2);
            var a3 = noms.charAt(noms.length - 3);
            var n = a1+a2+a3;

            var fac = $("#Faculte_etud_ins option:selected").text();
            var a1 = fac.charAt(fac.length - 1);
            var a2 = fac.charAt(fac.length - 2);
            var a3 = fac.charAt(fac.length - 3);
            var f = a1+a2+a3;

            var pr = $("#Promotion_etud_ins").val();
            pr  = pr.charAt(pr.length - 1);

            var pwd_std = m+"-"+n+"_"+pr+f+"#@"+ann;

            $("#Password_etud_ins").val(pwd_std);
            $("#Password_etud_ins").attr('disabled', true);
            // alert(rand());
        }

        $("#Matricule_etud_ins").keyup(function (e) { 
            generer_pwd_std();
        });

        $("#Promotion_etud_ins").keyup(function (e) { 
            generer_pwd_std();
        });

        $("#Noms_etud_ins").keyup(function (e) { 
            generer_pwd_std();
        });

        $("#Faculte_etud_ins").change(function (e) { 
            generer_pwd_std();
        });
    </script>

    <!-- update les mot  de l'etudiants -->
    <script>
        $('table').on('click', '#btn_update_pwd_stud', function(e){
            e.preventDefault();
            $("#update_student_pwd").modal('toggle');

            $("#_error_ins_").html("").text("");

            b = $(this);
            m = $(this).parent();
            mm = $(m).parent();
            mmm = $(mm).parent();
            // alert(mmm.html());
            insc_id = mmm.find("#insc_id");
            

            insc_mat = mmm.find("#insc_mat");
            insc_noms = mmm.find("#insc_noms");

            $("#update_mat_mat").val(insc_mat.text()).attr('disabled', true).attr('required', true);
            $("#md").html(insc_noms.text()).attr('required', true);
        });

        // bloquer l envoi du formulaire pour inscrire manuellement les etudiants
        $("#form_inscrire_etudiants_form__").submit(function (e) { 
            e.preventDefault();
            const data = {
                update_etudiants_pwd:"update_etudiants_pwd",
                mat:$("#update_mat_mat").val(),
                pwd:$("#pwd_generate").val()
            };
            // alert(data['mat']);
            $.ajax({
                type: "POST",
                url: "../../includes/t3_update_honoraire.php",
                data: data,
                beforeSend: function(){
                    $("#_error_ins_").text("Un instant svp ...").addClass('text-success');
                },
                success: function (response) {
                    if(response !="" && response == "ok"){
                        $("#_error_ins_").text("mise ajour reussi.").addClass('text-success');
                        $("#update_student_pwd").modal('toggle');
                    }else{
                        $("#_error_ins_").text("l'Erreur suivante : "+response +" est survenue").addClass('text-danger');
                    }
                },
                error:function(e){
                    $("#_error_ins_").text("Erreur : la connexion n pas bonne.").addClass('text-danger');
                    $("#_error_ins_").append("Erreur de connexion.");
                    alert("Erreur de connexion.");
                }
            });
        });

        $("#btn_gererate").click(function (e) { 
            e.preventDefault();
            $("#pwd_generate").val(generateP());
        });

        /* Function to generate combination of password */
        function generateP() {
            var pass = '';
            var str = 'AB!CDEFGHIJKLMNOP_QRSTUVWXYZ-' + 
                    'abcdefghijklmnopqrstuvwxyz0123456789@#$';
              
            for (i = 1; i <= 10; i++) {
                var char = Math.floor(Math.random()
                            * str.length + 1);
                  
                pass += str.charAt(char)
            }
            return pass;
        }
    </script>

    <script type="text/javascript">
        $("#form_submit_insc").submit(function (e) { 
            e.preventDefault();
            if($("#update_mat").val() !="" && $("#update_annee_acad").val() !=""){
                const data = {
                    update_mat : $("#update_mat").val(),
                    update_noms : $("#update_noms").val(),
                    update_fac : $("#update_fac").val(),
                    update_promotion : $("#update_promotion").val(),
                    update_annee_acad : $("#update_annee_acad").val(),
                    old_fac : $("#old_fac").val(),
                    old_promotion : $("#old_promotion").val()
                };
                $.ajax({
                    type: "POST",
                    url: "../../includes/update_etudiants.php",
                    data: data,
                    beforeSend:function(){
                        $("#error_ins").html("Un instant svp ...").css('color','green');
                    },
                    success: function (response) {
                        if(response !="" && response == "ok"){
                            $("#error_ins").html("");
                            $("#error_ins").html("ok les donnees sont mise a jour avec success").css('color','green');
                            $("#update_student").modal('toggle');
                            $("#error_ins").html("").text("");
                        }else{
                            $("#error_ins").html("Une erreur est survenue, revoyez les informations de l etudiant ..."+response).css('color','red');
                        }
                    },
                    error:function(e){
                        $("#error_ins").html("Erreur de connexion ...").css('color','red');
                    }
                });
            }else{
                $("#error_ins").html("Certains champs sont vide...").css('color', 'red');
            }
        });
    </script>
    <script>
        $('table').on('click', '#btn_del_student', function(e){
            e.preventDefault();

            b = $(this);
            m = $(this).parent();
            mm = $(m).parent();
            mmm = $(mm).parent();
            // alert(mmm.html());
            insc_id = mmm.find("#insc_id");
            // alert(insc_id.text());

            insc_mat = mmm.find("#insc_mat");
            insc_noms = mmm.find("#insc_noms");
            insc_fac = mmm.find("#insc_fac");
            insc_promotion = mmm.find("#insc_promotion");

            if(insc_noms.text() !="" && insc_fac.text() !="" && insc_mat.text() !=""){
                $("#id_etud_del").val(insc_id.text());
                $("#info_del").html("Voulez-vous vraiment supprimer : "+insc_noms.text()+" qui est en "+" "+insc_promotion.text()+" "+insc_fac.text()+" ?");
            }
        });

        $("#form_del_etud").submit(function (e) { 
            e.preventDefault();
            if($("#id_etud_del").val() !=""){
                $.ajax({
                    type: "POST",
                    url: "../../includes/del_poste_r_univ.php",
                    data: {"id_etud_delete":$("#id_etud_del").val()},
                    beforeSend: function(){
                        $("#info_del").addClass('text-primary').html("Un instant svp ...");
                    },
                    success: function (data) {
                        if(data !="" && data == "ok"){
                            $("#info_del").addClass('text-primary').html("Suppression reussi avec success ...");
                            // window.location.reload();
                            $("#del_student").modal('toggle');
                        } else {
                            $("#info_del").addClass('text-danger').html("une erreur s'est produite lors de la suppressionnde l etudiant ...");
                        }  
                    }
                });
            }
        });

        $("#Fermer").click(function (e) { 
            e.preventDefault();
            window.location.reload();
        });
    </script>

    <script type="text/javascript">
        $("#form_search_etud").submit(function (e) { 
            e.preventDefault();
            if($("#mat_etu").val() !=""){
                const data = {
                    mat_etu:$("#mat_etu").val(),
                    annee_acad:$("#annee_acad").val()
                };
                $.ajax({
                    type: "post",
                    url: "../../includes/search_etudiants_insc.php",
                    data: data,
                    success: function (response) {
                        if (response != "") {
                            $("#se").empty();
                            $("#se").html("");
                            $("#se").html(response);
                            $("#btn_s").hide();
                        } else {
                            alert("Erreur");
                        }
                    },
                    error:function(e){
                        alert("Erreur de connexion, reesayer ...");
                    }
                });
            } 
        });

        $("#id_close").click(function (e) { 
            e.preventDefault();
            window.location.reload();
        });
    </script>
</body>
</html>