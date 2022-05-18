<div class="">
    <noscript>
        <h4 class="h6 text-danger text-center m-3">
            <div class="alert alert-danger" role="alert">
                <strong>Bonjour chers utilisateur, veuillez activer JavaScript sur votre navigateur pour que le site fonctionne correctement. Ou bien si votre navigateur ne supporte pas JavaScript Merci d'utiliser un autre.</strong>
            </div>
        </h4>
    </noscript>
</div>
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>
    
    <h3 class="page-header"><i class="fa fa-laptop"></i>
        <?php echo $p;?></h3>
    <ul class="navbar-nav ml-auto">
        <div class="topbar-divider d-none d-sm-block"></div>
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?=$m['noms']?></span>
                <img class="img-profile rounded-circle" src="<?=$m['profil']?>">
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                aria-labelledby="userDropdown">
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modelId_profil">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>Profile
                </a>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#m_journal">
                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>Journal d'activité
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>Déconnection
                </a>
            </div>
        </li>
    </ul>
</nav>
<!-- Modal -->
<div class="modal fade" id="modelId_profil" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Profil de <b><?=VerificationUser::verif($m['noms'])?></b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-inverse">
                    <thead class="thead-inverse bg-light">
                        <tr>
                            <th>Profil</th>
                            <th>Noms</th>
                            <th>Fonction</th>
                            <th>Email</th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <img src="<?=VerificationUser::verif($m['profil'])?>" class="img-fluid rounded-top,rounded-right,rounded-bottom,rounded-left,rounded-circle" alt="profil">
                                </td>
                                <td><?=VerificationUser::verif($m['noms'])?></td>
                                <td><?=VerificationUser::verif($m['fonction'])?></td>
                                <td><?=VerificationUser::verif($m['email'])?></td>
                            </tr>
                        </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal journal d'activite-->
<div class="modal fade" id="m_journal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
                <div class="modal-header">
                        <h5 class="modal-title">Journal d'activite de <b><?=VerificationUser::verif($m['noms'])?></b></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                    </div>
            <div class="modal-body">
                <div class="">
                    <?php
                        $journal = ConnexionBdd::Connecter()->prepare("SELECT * FROM log_admin_user WHERE noms  = ?");
                        $journal->execute(array(VerificationUser::verif($m['noms'])));

                        if($journal->rowCount() > 0){
                            ?>
                                <table class="table table-bordered table-inverse table-hover">
                                    <thead class="thead-inverse bg-light">
                                        <tr>
                                            <th>###</th>
                                            <th>Activite</th>
                                            <th>Date</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                while($data_ = $journal->fetch()){
                                                    ?>
                                                        <tr>
                                                            <td>
                                                                <img src="<?=VerificationUser::verif($m['profil'])?>" class="img-fluid rounded-top,rounded-right,rounded-bottom,rounded-left,rounded-circle" alt="profil">
                                                            </td>
                                                            <td><?=$data_['actions']?></td>
                                                            <td><?=$data_['date_action']?></td>
                                                        </tr>
                                                    <?php
                                                }
                                            ?>
                                        </tbody>
                                </table>
                            <?php
                        }else{
                            echo "pas d'activite recente sur votre compte";
                        }
                    ?>
                </div>
            </div>
            <div class="modal-footer p-2 m-0">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>