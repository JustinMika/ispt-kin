<?php
    require_once 'ConnexionBdd.class.php';

    /** 
     * classe pour verifier les sessions des utulisateurs
    */
    class VerificationUser{
        private static $nom, $fonction, $fonction_1, $access, $id, $id_1, $data_a = array();

        function __construct($noms, $fonction, $fonction_1, $access, $id, $id_1){
            self::$nom = $noms;
            self::$fonction = $fonction;
            self::$fonction_1 = $fonction_1;
            self::$access = $access;
            self::$id = $id; 
            self::$id_1 = $id_1; 
        }

        public static function verif_sess(){
            if(md5(sha1(self::$id)) == self::$id_1 && self::$fonction == self::$fonction_1){
                self::$fonction = self::$fonction_1;
            }else{
                header('location:../index.php');
                // die("E2");
            }
        }

        public static function session_id_verf(){
            $data = ConnexionBdd::Connecter()->prepare("SELECT * FROM utilisateurs WHERE noms = ? AND fonction = ? AND access = ? AND id_user = ?");

            $data->execute(array(
                self::$nom,
                self::$fonction,
                self::$access,
                self::$id
            ));

            $n = $data->rowCount();
            $d = $data->fetch(PDO::FETCH_ASSOC);

            if($n == 1){
                self::$data_a = $d;
                return self::$data_a;
            }else{
                return false;
            }
        }

        public static function verif($var){
            if(isset($var) && !empty($var)){
                return $var;
            }else{
                header('location:../index.php', true, 301);
                // die("e1");
            }
        }
    }
?>