<?php
    require_once './ConnexionBdd.class.php';
    // print_r($_GET);
    if(isset($_GET['id_departement_']) && isset($_GET['id_section_'])){
        $s = ConnexionBdd::Connecter()->prepare("SELECT * FROM options WHERE id_departement = ? AND id_section = ?");
        $s->execute(array($_GET['id_departement_'], $_GET['id_section_']));
        if($s->rowCount() > 0){
            while($data = $s->fetch()){
                echo '
                    <tr>
                        <td id="id_option">'.$data['id_option'].'</td>
                        <td id="option_">'.$data['option_'].'</td>
                        <td id="promotion">'.$data['promotion'].'</td>
                        <td id="code_">'.$data['code_'].'</td>
                    </tr>';
            }
        }else{
            echo '
                <tr>
                    <td> pas de donnees</td>
                </tr>';
        }
    }else{
        die("Une erreur s'est produite.");
    }
?>