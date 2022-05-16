<?php
    require_once '../../includes/verification.class.php';
    $r = $_SESSION;
    
    if(array_sum($r) == 0 && count($r) == 0){
        // die("e2");
        // header('location:dec.php');
        // exit();
    }else{
        
        $rr = $r['data'];
        // print_r($r['data']['id']);

        // la classe pour la verification des sessions
        $session = new VerificationUser(VerificationUser::verif($rr['noms']), 
                        VerificationUser::verif($rr['fonction']), 
                        VerificationUser::verif($_GET['Ff']), 
                        VerificationUser::verif($rr['access']), 
                        VerificationUser::verif($rr['id_user']), 
                        VerificationUser::verif($_GET['i']));
        VerificationUser::verif_sess();

        $m = VerificationUser::session_id_verf();  
    }

    if(empty($m['noms'])){
        header('location:dec.php', true, 301);
    }
?>