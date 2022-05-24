<?php
    $lf = array();
    $fac = connexionBdd::Connecter()->query("SELECT DISTINCT * FROM sections GROUP BY section");
    while($l = $fac->fetch()){
        $lf[] =  $l['section'];  
    }

    // pour restreidre droit chez de faculte
    function restruct_r_(){
        if($_SESSION['data']['fonction'] != "" && $_SESSION['data']['access'] !=""){
            if($_SESSION['data']['fonction'] != "Sec. de fac."){
                return '';
            }else{
                return 'display:none';
            }
        }else{
            header("Location:../index.php", true, 301);
        }
    }

    function rr(){
        if($_SESSION['data']['fonction'] == "Admin" || $_SESSION['data']['fonction'] =="AB"){
            return ''; 
        }else{
            return 'display:none';
        }
    }

    // admin et chef de faculte
    function rrf(){
        if($_SESSION['data']['fonction'] == "Admin" || $_SESSION['data']['fonction'] =="Sec. de fac."){
            return ''; 
        }else{
            return 'display:none';
        }
    }

    function rrr(){
        if($_SESSION['data']['fonction'] == "Admin" || $_SESSION['data']['fonction'] =="AB" || $_SESSION['data']['fonction'] == "Comptable"){
            return ''; 
        }else{
            return 'display:none';
        }
    }

    function rrrr(){
        if($_SESSION['data']['fonction'] == "Admin" || $_SESSION['data']['fonction'] =="AB" || $_SESSION['data']['fonction'] == "Comptable"){
            return ''; 
        }else{
            return 'display:none';
        }
    }

    function r5(){
        if($_SESSION['data']['fonction'] == "Admin" || $_SESSION['data']['fonction'] =="AB" || $_SESSION['data']['fonction'] == "Comptable" || $_SESSION['data']['fonction']== "Agent budget control"){
            return ''; 
        }else{
            return 'display:none';
        }
    }

    function r6(){
        if($_SESSION['data']['fonction'] == "Admin" || $_SESSION['data']['fonction'] =="AB" || $_SESSION['data']['fonction'] == "Comptable" || $_SESSION['data']['fonction']== "Agent budget control" || $_SESSION['data']['fonction'] == "Sec. de fac."){
            return ''; 
        }else{
            return 'display:none';
        }
    }

    /** 
     * le droit pour le comptable admin et le comptable
     */
    function r4(){
        if($_SESSION['data']['fonction'] == "Admin" || $_SESSION['data']['fonction'] =="AB" || $_SESSION['data']['fonction'] == "Comptable"){
            return ''; 
        }else{
            return 'display:none';
        }
    }

    function rest_corb(){
        if($_SESSION['data']['fonction'] == "Admin" || $_SESSION['data']['fonction'] =="AB" || $_SESSION['data']['fonction']== "Agent budget control"){
            return ''; 
        }else{
            return 'display:none';
        }
    }

    function sec_fac(){
        if($_SESSION['data']['fonction'] == "Admin" || $_SESSION['data']['fonction'] =="AB" || $_SESSION['data']['fonction']== "Sec. de fac."){
            return ''; 
        }else{
            return 'display:none';
        }
    }


    function r_sec_fac(){
        if($_SESSION['data']['fonction']== "Sec. de fac."){
            return ''; 
        }else{
            return 'display:none';
        }
    }

    //admin
    function restruct_r_admin(){
        if($_SESSION['data']['fonction'] != "" && $_SESSION['data']['access'] !=""){
            if($_SESSION['data']['fonction'] == "Admin"){
                return '';
            }else{
                return 'display:none';
            }
        }else{
            header("Location:../index.php", true, 301);
        }
    }

    // AB
    function restruct_r_ab(){
        if($_SESSION['data']['fonction'] != "" && $_SESSION['data']['access'] !=""){
            if($_SESSION['data']['fonction'] != "AB"){
                return '';
            }else{
                return 'display:none';
            }
        }else{
            header("Location:../index.php", true, 301);
        }
    }

    //le service de budget controle
    function restruct_r_bc(){
        if($_SESSION['data']['fonction'] != "" && $_SESSION['data']['access'] !=""){
            if($_SESSION['data']['fonction'] != "Agent budget control"){
                return '';
            }else{
                return 'display:none';
            }
        }else{
            header("Location:../index.php", true, 301);
        }
    }

    // le comptable
    function restruct_r_compt(){
        if($_SESSION['data']['fonction'] != "" && $_SESSION['data']['access'] !=""){
            if($_SESSION['data']['fonction'] != "Comptable"){
                return '';
            }else{
                return 'display:none';
            }
        }else{
            header("Location:../index.php", true, 301);
        }
    }

?>
<ul class="navbar-nav bg-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="#">
        <div class="sidebar-brand-icon rotate-n-15">
            <img src="../../images/ispt_kin.png" alt="UNIGOM" width="50" height="50" style="border-radius: 50%;">
        </div>
        <div class="sidebar-brand-text mx-3">ISPT-KIN</div>
    </a>
    <hr class="sidebar-divider my-0">

    <li class="nav-item">
        <a class="nav-link" href="index.php?Ff=<?=VerificationUser::verif($_SESSION['data']['fonction'])?>&i=<?=VerificationUser::verif($_SESSION['data']['id'])?>">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Home</span>
        </a>
    </li>
    <!-- <hr class="sidebar-divider"> -->

    <li class="nav-item" style="<?=rr()?>;margin-top:-5%">
        <a class="nav-link" href="annee_acad.php?Ff=<?=VerificationUser::verif($_SESSION['data']['fonction'])?>&i=<?=VerificationUser::verif($_SESSION['data']['id'])?>">
            <i class="fas fa-fw fa-table"></i>
            <span>Année Academique</span>
        </a>
    </li>

    <li class="nav-item" style="<?=rr()?>;margin-top:-5%">
        <a class="nav-link" href="fac.php?Ff=<?=VerificationUser::verif($_SESSION['data']['fonction'])?>&i=<?=VerificationUser::verif($_SESSION['data']['id'])?>">
            <i class="fas fa-fw fa-chart-area"></i>
            <span>Section</span>
        </a>
    </li>

    <!-- <li class="nav-item" style="<?=rrf()?>">
        <a class="nav-link" href="compt_facultaire.php?Ff=<?=VerificationUser::verif($_SESSION['data']['fonction'])?>&i=<?=VerificationUser::verif($_SESSION['data']['id'])?>">
            <i class="fas fa-fw fa-chart-area"></i>
            <span>Comptabilité de section</span>
        </a>
    </li> -->

    <!-- <div class="sidebar-heading" style="<?=rr()?>;margin-top:-5%">
        Etudiant(e)s
    </div> -->
    <li class="nav-item"  style="<?=rest_corb()?>;; margin-top:-5%">
        <a class="nav-link collapsed" href="" data-toggle="collapse" data-target="#collapseTwo"
            aria-expanded="true" aria-controls="collapseTwo">
            <i class="fas fa-fw fa-cog"></i>
            <span>Inscription</span>
        </a>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="inscrire_etudiants.php?Ff=<?=VerificationUser::verif($_SESSION['data']['fonction'])?>&i=<?=VerificationUser::verif($_SESSION['data']['id'])?>">Inscription</a>
                <!-- affectation des frais academique aux etudiants -->
                <a class="collapse-item" href="Affectation.php?Ff=<?=VerificationUser::verif($_SESSION['data']['fonction'])?>&i=<?=VerificationUser::verif($_SESSION['data']['id'])?>">Affectation des frais</a>
                <!--  -->
                <a class="collapse-item" href="comptabilite.php?Ff=<?=VerificationUser::verif($_SESSION['data']['fonction'])?>&i=<?=VerificationUser::verif($_SESSION['data']['id'])?>">Payement</a>
                <!-- <a class="collapse-item" href="compt_facultaire.php?Ff=<?=VerificationUser::verif($_SESSION['data']['fonction'])?>&i=<?=VerificationUser::verif($_SESSION['data']['id'])?>"  style="<?=rrf()?>">Comptabilité de Section</a> -->
            </div>
        </div>
    </li>

    <!-- <div class="sidebar-heading" style="<?=r5()?>; margin-top:-5%">
        Comptabilité
    </div> -->
    <li class="nav-item" style="<?=r6()?>;">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
            aria-expanded="true" aria-controls="collapseUtilities">
            <i class="fas fa-fw fa-wrench"></i>
            <span>Comptabilité</span>
        </a>
        <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <!-- <h6 class="collapse-header">Comptabilité</h6> -->

                <!-- Gestion de la caisse -->
                <!-- <a class="dropdown-item" href="caisse.php?Ff=<?=VerificationUser::verif($_SESSION['data']['fonction'])?>&i=<?=VerificationUser::verif($_SESSION['data']['id'])?>" style="<?=rr()?>"> Caisse</a> -->

                <!-- les Checque -->
                <a class="collapse-item" href="cheques.php?Ff=<?=VerificationUser::verif($_SESSION['data']['fonction'])?>&i=<?=VerificationUser::verif($_SESSION['data']['id'])?>"  style="<?=rrr()?>"> Compt. des chèques</a>

                <!-- Rapport facultaire -->
                <a class="dropdown-item " href="./rapport_fac.php?Ff=<?=VerificationUser::verif($_SESSION['data']['fonction'])?>&i=<?=VerificationUser::verif($_SESSION['data']['id'])?>"  style="<?=rrr()?>">Depense Section</a>

                <!-- Rapport périodique -->
                <a class="dropdown-item " href="./Rapport_periodique.php?Ff=<?=VerificationUser::verif($_SESSION['data']['fonction'])?>&i=<?=VerificationUser::verif($_SESSION['data']['id'])?>"  style="<?=rr()?>"> Rapport périodique</a>

                <!-- Frais Rapports -->
                <a class="dropdown-item " href="FraisRapport.php?Ff=<?=VerificationUser::verif($_SESSION['data']['fonction'])?>&i=<?=VerificationUser::verif($_SESSION['data']['id'])?>"  style="<?=r5()?>"> Frais Rapports</a>

                <!-- Poste de dépense -->
                <a class="dropdown-item" href="poste_depense.php?Ff=<?=VerificationUser::verif($_SESSION['data']['fonction'])?>&i=<?=VerificationUser::verif($_SESSION['data']['id'])?>"  style="<?=rrr()?>"> Poste de dépense</a>

                <!-- Depense facultaire -->
                <a class="dropdown-item" href="dep_facultaire.php?Ff=<?=VerificationUser::verif($_SESSION['data']['fonction'])?>&i=<?=VerificationUser::verif($_SESSION['data']['id'])?>" style="<?=r_sec_fac()?>"> Dépense de section</a>

                <!-- Poste des recettes -->
                <a class="dropdown-item" href="poste_de_recettes.php?Ff=<?=VerificationUser::verif($_SESSION['data']['fonction'])?>&i=<?=VerificationUser::verif($_SESSION['data']['id'])?>"  style="<?=rrrr()?>"> Poste des recettes</a>
            </div>
        </div>
    </li>

    <li class="nav-item"  style="<?=rr()?>">
        <a class="nav-link" href="rap_fac.php?Ff=<?=VerificationUser::verif($_SESSION['data']['fonction'])?>&i=<?=VerificationUser::verif($_SESSION['data']['id'])?>">
        <i class="fa fa-calculator" aria-hidden="true"></i>
        <span>Rapport de section </span>
        </a>
    </li>

    <li class="nav-item"  style="<?=rrrr()?>">
        <a class="nav-link" href="GestionHonoraire.php?Ff=<?=VerificationUser::verif($_SESSION['data']['fonction'])?>&i=<?=VerificationUser::verif($_SESSION['data']['id'])?>">
        <i class="fa fa-calculator" aria-hidden="true"></i>
        <span>Gestion Honoraire </span>
        </a>
    </li>

    <!-- <hr class="sidebar-divider m-0"> -->

    <li class="nav-item">
        <a class="nav-link" href="Utilisateurs.php?Ff=<?=VerificationUser::verif($_SESSION['data']['fonction'])?>&i=<?=VerificationUser::verif($_SESSION['data']['id'])?>">
            <i class="fas fa-fw fa-table"></i>
            <span>Utilisateurs</span>
        </a>
    </li>
    
    <!-- <li class="nav-item"  style="<?=rest_corb()?>">
        <a class="nav-link" href="corbeille.php?Ff=<?=VerificationUser::verif($_SESSION['data']['fonction'])?>&i=<?=VerificationUser::verif($_SESSION['data']['id'])?>">
        <i class="fa fa-trash" aria-hidden="true"></i>
        <span>Corbeille </span>
        </a>
    </li> -->
   
    <hr class="sidebar-divider d-none d-md-block mt-2 p-1">

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>
<!-- End Sidebar -->