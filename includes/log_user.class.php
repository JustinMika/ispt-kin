<?php
    require_once 'ConnexionBdd.class.php';
    /**
     * classe name : LogUser \n
     * Enregistre les logs des utilisateurs dans la base de donnees.
     * @todo : Enregistre les logs des utilisateurs dans la base de donnees.
     */
    class LogUser{

        /** 
         * @return : null
        */
        public static function addlog($username, $action){
            if(!empty($username) && !empty($action)){
                $insert = ConnexionBdd::Connecter()->prepare("INSERT INTO log_admin_user(noms, date_action, actions) VALUES(?, NOW(), ?)");
                $ok = $insert->execute(array($username, $action));
                if(!$ok){
                    die("Log no saved !!!");
                }
            }
        }
    }
?>