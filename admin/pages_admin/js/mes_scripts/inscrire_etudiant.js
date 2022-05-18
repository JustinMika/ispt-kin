$(document).ready(function() {
    $("#t3_form").css({ display: 'none' });

    afficher_etudiants();
    $("#form_inscription_etud").submit(function(e) {
        e.preventDefault();
        var property = document.getElementById('fichier_excel').files[0];
        var image_name = property.name;
        var image_extension = image_name.split('.').pop().toLowerCase();

        var form_data = new FormData();
        form_data.append("file", property);

        $.ajax({
            url: '../../includes/inscrire_etudiant.php',
            method: 'POST',
            data: form_data,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function() {
                $("#t3_form").css({ display: 'block' });
            },
            success: function(data) {
                if (data != "insertion encours") {
                    $("#t3_form").css({ display: 'block' });
                    $("#fichier_excel").val();
                    // window.location.reload();
                    // alert(data);
                    $("#span").html("<small>" + data + "</small><hr/>");
                    $("#spinner").css({ display: 'block' });
                } else {
                    $("#fichier_excel").val();
                }

            }
        });

        $(document).ajaxComplete(function() {
            $("#term").html("Traitement reussi avec succes");
            $("#spinner").css({ display: 'none' });
            $("#fichier_excel").val();
            $("#spinner_sp").hide();
            // window.location.reload();
        });
    });

    function afficher_etudiants() {
        $.ajax({
            type: "GET",
            url: "../../includes/list_etudiants_inscr.php",
        }).done(function(data) {
            if (data != "") {
                $($("#t_inscription_etudiants")).empty();
                $("#t_inscription_etudiants").append(data);
            } else {
                $($("#t_inscription_etudiants")).empty();
                $("#no-data").html("<marquee>Pas de donnees pour l'instant.</marquee>");
            }
        }).fail(function(data) {});
    }

    setInterval(() => {
        afficher_etudiants();
    }, 3000);
});