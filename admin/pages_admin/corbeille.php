<?php
    session_start();
    require_once '../../includes/ConnexionBdd.class.php';
    require_once '../../includes/verification.class.php';
    //verification des sessions
    require_once './sessions.php';
    require_once '../../includes/log_user.class.php';
    $p = "Gestion des élèments dans la Corbeille";

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
    <title>Corbeille</title>
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
                    <div class="card shadow">
                        <div class="card-header bg-gray-200">
                            <h4 class="text-center text-secondary">Corbeille</h4>
                        </div>
                        <div class="card-body pt-2">
                            <div class="container p-2">
                                <div class="row">
                                    <!-- utilisateur -->
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                        <div class="card">
                                            <div class="card-header bg-gray-200 p-2"> 
                                                <h3>Élements supprimés dans le système</h3>
                                            </div>
                                            <div class="card-body p-0">
                                            <table class="table table-bordered table-hover table-md">
                                                <thead class="thead-inverse">
                                                    <tr>
                                                        <th>#ID</th>
                                                        <th>Titre de l'élèment supprimé</th>
                                                        <th>Noms / profil de l'agent ayant supprimé</th>
                                                        <th>Date de la suppression</th>
                                                        <th> # </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                        $list_user = ConnexionBdd::Connecter()->query("SELECT * FROM corbeille ORDER BY id DESC");
                                                        while($data = $list_user->fetch()){
                                                            ?>
                                                                <tr>
                                                                    <td class="m-3"><?=$data['id']?></td>
                                                                    <td class="m-3"><?=$data['titre']?></td>
                                                                    <td class="m-3"><?=$data['noms_del']?></td>
                                                                    <td><?=$data['date']?></td>
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

    <?php include_once("modal_decon.php");?>

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
</body>
</html>