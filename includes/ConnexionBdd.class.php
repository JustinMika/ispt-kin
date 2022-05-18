<?php
    /**
     * classe pour se connecter a la base de donnees via PDO
     */
    class ConnexionBdd{
        private static $host = "localhost";
		private static $db_name = "projet_fin_ispt";
		private static $user = "root";
		private static $pwd_user = "";
		private static $pdo = null;
        private static $pdo_o = null;

		/**
         * fonction pour se connecter a la bdd via PDO
		 * @return : $pdo
		 */
		public static function Connecter()
		{
			try {
				$dns = 'mysql:host='.self::$host.';dbname='.self::$db_name.'; CHARSET=utf8';
				self::$pdo = new pdo($dns, self::$user, self::$pwd_user);
				self::$pdo_o = self::$pdo;
			} 
			catch (Exception $e) {
				die('Impossible de se connecter a la base de donnees : <b style="color:red;">'.$e.'</b>');
			}
			return self::$pdo_o;
		}
    }

	function format_session($v){
		if(isset($v) && !empty($v)){
			return $v;
		}else{
			header('location:../index.php');
			exit();
		}
	}

	function montant_restant($var1, $var_2){
		if($var1 > 0){
			return intval($var1-$var_2);
		}
	}

	function montant_restant_p($var1, $var_2){
		if($var_2 > 1){
			$v = floatval(($var1 * 100) /  $var_2);
			$str = strtolower($v);

			if(strlen($str) > 4){
				$v2 = $str[0].$str[1].$str[2].$str[3].$str[4];
				return $v2;
			}else{
				return $v;
			}
		}else{
			return '0%';
		}
	}
	
	function montant_restant_pourcent($var1, $var_2){
		// return intval((($var1 * 100) /  $var_2));
		if($var_2 >= 1){
			$v = floatval(($var1 * 100) /  $var_2);
			$str = strtolower($v);

			if(strlen($str) > 4){
				$v2 = $str[0].$str[1].$str[2].$str[3];
				return $v2;
			}else{
				return $v;
			}
		}else{
			return '0';
		}
	}

	function desactiver_btn($var1, $var_2){
		if($var1 == $var_2 || $var_2 > $var1){
			return 'display:none';
		}else{
			return '';
		}
	}

	function decode_fr($v){
		return iconv('UTF-8', 'windows-1252', html_entity_decode($v));
	}

	function m_format($v){
        if(empty($v)){
            return '$ 0';
        }else{
            return '$ '.$v;
        }
    }

	// restriction des droits a certains utiisateurs
	function restruct_r(){
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

	// regler les soucis d'encodage de caracters
	function encode($v){
		$t1 = array("é", "è", "ç", "â", "î", "à","ù");
		$t2 = array("&Atilde;&copy;",
		"&Atilde;&uml;",
		"&Atilde;&sect;",
		"&Atilde;&cent;",
		"&Atilde;&reg;",
		"&Atilde;&nbsp;",
		"&Atilde;&sup1;);");
	}
	function decode($v){}
?>