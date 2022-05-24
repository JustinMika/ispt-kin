<?php
    session_start();
    require_once '../../includes/ConnexionBdd.class.php';
    require_once '../../includes/verification.class.php';

    //verification des sessions
    require_once './sessions.php';
    require_once '../../includes/log_user.class.php';
    $p = "Comptabilié des chèques";

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
    <link rel="shortcut icon" href="../../images/ispt_kin.png" type="image/x-icon">
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
                    <div class="card shadow">
                        <div class="">
                            <div class="container p-2">
                                <div class="row">
                                    <!-- utilisateur -->
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                        <div class="card">
                                            <div class="card-header bg-gray-200 p-2 text-center d-flex flex" style="justify-content: space-between;"> 
                                                <h3><?=$p?></h3>
                                                <div class="dropdown open">
                                                    <button class="btn btn-secondary dropdown-toggle" type="button" id="triggerId" data-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false">
                                                                Action
                                                            </button>
                                                    <div class="dropdown-menu" aria-labelledby="triggerId">
                                                        <button class="dropdown-item" id="add_trans"><i class="fa fa-plus" aria-hidden="true"></i>  Ajouter</button>
                                                        <a class="dropdown-item" href="./rapport_pdf/ch.php"><i class="fa fa-print" aria-hidden="true"></i> Imprimer</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body p-0">
                                            <table class="table table-bordered table-hover table-md">
                                                <thead class="thead-inverse">
                                                    <tr>
                                                        <th>#ID</th>
                                                        <th>Date</th>
                                                        <th>libellé</th>
                                                        <th>N<sup>o</sup> Chèque</th>
                                                        <th> Montant </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                        $list_user = ConnexionBdd::Connecter()->query("SELECT * FROM gestion_cheque ORDER BY id DESC");
                                                        while($data = $list_user->fetch()){
                                                            ?>
                                                                <tr>
                                                                    <td class="m-3"><?=$data['id']?></td>
                                                                    <td class="m-3"><?=date("d/m/Y", strtotime($data['date_']))?></td>
                                                                    <td><?=$data['liebelle']?></td>
                                                                    <td><?=$data['num_cheque']?></td>
                                                                    <td><?='$'.$data['montant']?></td>
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
            <!-- footer -->
		  <?php include './footer.php';?>
        </div>
    </div>
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <?php include_once("modal_decon.php");?>

    <!-- delete user admin in database -->
    <div class="modal fade" id="Add_trans_m" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa fa-plus" aria-hidden="true"></i> Ajout </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <form action="" method="post" id="add_cheque_form">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="form-group">
                                <label for="">date</label>
                                <input type="date" name="date_cheque" id="date_cheque" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="">libellé</label>
                                <input type="text" name="libelle_cheque" id="libelle_cheque" class="form-control" placeholder="libellé" required>
                            </div>
                            <div class="form-group">
                                <label for="">N<sup>o</sup> Cheque</label>
                                <input type="text" name="n_cheque" id="n_cheque" class="form-control" placeholder="numero cheque" required>
                            </div>
                            <div class="form-group">
                                <label for="">Montant</label>
                                <input type="text" name="montant_cheque" id="montant_cheque" class="form-control" placeholder="montant" required>
                            </div>
                            <small id="error_t3"></small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-danger" id="btn_c"><i class="fa fa-plus" aria-hidden="true"></i> Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $("#add_trans").click(function (e) { 
            e.preventDefault();
            $("#Add_trans_m").modal('toggle');
        });

        $("#btn_c").attr('disabled', true);
        $("#montant_cheque").keyup(function (e) { 
            var x = $("#montant_cheque").val();
            if(!isNaN(x) && x >= 0 && x !="00" && x !="0,"){
                if(x !=""){
                    $("#btn_c").removeAttr('disabled');
                    $("#error_t3").html('');
                    $("#error_t3").html('montant valide (: :)').css({color:'green'}).addClass('text-success');
                    $("#montant_cheque").val(x);
                }else{
                    $("#error_t3").html('');
                    $("#error_t3").html("une valeur est requis").addClass('text-danger');
                    $("#btn_c").attr('disabled', true);
                }
            }else{
                $("#error_t3").html('');
                $("#error_t3").html("Veuillez saisir un montant valide.").addClass('text-danger');
                $("#btn_c").attr('disabled', true);
            }
        });
    </script>

    <!-- submit form -->
    <script type="text/javascript">
        $("#add_cheque_form").submit(function (e) { 
            e.preventDefault();
            const data = {
                date_cheque : $("#date_cheque").val(),
                libelle_cheque : $("#libelle_cheque").val(),
                n_cheque : $("#n_cheque").val(),
                montant_cheque : $("#montant_cheque").val()
            };
            $.ajax({
                type: "POST",
                url: "../../includes/t3_cheque.php",
                data: data,
                beforeSend: function(){
                    $("#error_t3").html('un instant svp ...').css({color:'green'}).addClass('text-success');
                },
                success: function (response) {
                    if(response == "success"){
                        $("#error_t3").html('ok').css({color:'green'}).addClass('text-success');
                        window.location.reload();
                    }else{
                        $("#error_t3").html(response).css({color:'red'}).addClass('text-danger');
                    }
                }, 
                error: function (response){
                    $("#error_t3").html(response).css({color:'red'}).addClass('text-danger');
                }
            });
        });
    </script>
</body>
</html>