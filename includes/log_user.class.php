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
        public static function addlog($id_user, $action){
            if(!empty($id_user) && !empty($action)){
                $insert = ConnexionBdd::Connecter()->prepare("INSERT INTO log_user(log_action, date_action, id_user) VALUES(?, NOW(), ?)");
                $ok = $insert->execute(array($action, $id_user));
                if(!$ok){
                    die("Log no saved !!!");
                }
            }
        }
    }
?>