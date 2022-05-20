$(document).ready(function() {
    $("#show_all").click(function(e) {
        e.preventDefault();
        $("#Affich_info").modal('toggle');
        // afficher_etudiants();
    });
    // rechercher un etudiants pour l'affectation des frais
    $("#form_inscription_etud").submit(function(e) {
        e.preventDefault();
        var mat_etud = $("#mat_etud");
        var fac = $("#fac_search option:selected").text();
        var promotion = $("#promotion_search option:selected").text();
        var annee_acad = $("#annee_acad_search option:selected").text();

        if ($(mat_etud).val() != "" && fac != "" && promotion != "" && annee_acad != "") {
            const data = {
                "id_search": "search",
                "mat": $(mat_etud).val(),
                "fac": fac,
                "annee_acad": annee_acad,
                "promotion": promotion
            };

            $.ajax({
                type: "POST",
                url: "../../includes/AffectationFraisEtud.php",
                data: data,
            }).done(function(data) {
                if (data != "") {
                    $("#t_inscription_etudiants").empty();
                    $("#t_inscription_etudiants").append(data);
                    $("#myModal").modal('toggle');
                }
            }).fail(function(data) {
                $("#t3").append('<p class="text-danger">un souci est survenu lors du traitement de la requete</p>');
            });
        } else {
            $("#t3").append('<p class="text-danger">Veuillez completer tous les champs svp !</p>');
            alert("Veuillez completer tous les champs svp !");
        }
    });

    // recherche des des etudiants par fac , promotion pour l'affectation des fais
    $("#form_inscription_fac_prom").submit(function(e) {
        e.preventDefault();
        var fac = $("#fac_search_f_p option:selected").text();
        var promotion = $("#promotion_search_f_p option:selected").text();
        var annee_acad = $("#annee_acad_search_f_p option:selected").text();

        if (fac != "" && promotion != "" && annee_acad != "") {
            const data = {
                "id_fpa": "search_fpa",
                "fac_fpa": fac,
                "annee_acad_fpa": annee_acad,
                "promotion_fpa": promotion
            };

            $.ajax({
                type: "POST",
                url: "../../includes/AffectationFraisEtud.php",
                data: data,
            }).done(function(data) {
                $("#t_inscription_etudiants").empty();
                $("#t_inscription_etudiants").append(data);
                $("#mod_affectation_fac_promotion").modal('toggle');
            }).fail(function(data) {
                alert("Erreur : " + data);
            });

        } else {
            alert("Certain champs sont vide.");
        }
    });

    // affichage des resultats des tous les etudiants inscrit pour l'annee encours
    afficher_etudiants();

    function afficher_etudiants() {
        $.ajax({
            type: "GET",
            url: "../../includes/AffectationFraisEtud.php",
            data: { affich: "affich_form" },
        }).done(function(data) {
            if (data != "") {
                $($("#t_inscription_etudiants")).empty();
                $("#t_inscription_etudiants").append(data);
            } else {
                $($("#t_inscription_etudiants")).empty();
                $("#error_modal").modal('toggle');
                $("#error_modal_text").html('<p class="text-danger"><i class="fa fa-database" aria-hidden="true"></i> Aucun resultat trouver pour l\'instant.<br/> aucun etudiants dans la base de donnees.</p>');
                $("#no-data").html("<marquee>Aucun resultat trouver pour l'instant.</marquee>");
            }
        }).fail(function(data) {});
    }

    // affecter les fais a l etudiants
    $("#fff").submit(function(e) {
        e.preventDefault();
        var mat_etud_aff = $("#mat_etud_aff_aff").val();
        var annee_acad_aff = $("#annee_acad_aff_aff").val();
        var section = $("#section_aff").val();
        var departement = $("#departement_aff").val();
        var option = $("#option_aff").val();
        var promotion_aff = $("#promotion_aff").val();

        frais_a_payer = Array();

        $('input[name="ch_sh"]:checked').each(function() {
            frais_a_payer.push($(this).val());
            frais_a_payer.join(', ');
        });
        if (mat_etud_aff != "" && annee_acad_aff != "" && promotion_aff != "" && option !="" && departement !="" && section !="") {
            const data = {
                affect: "affect",
                mat: mat_etud_aff,
                promotion: promotion_aff,
                frais: frais_a_payer,
                section: section,
                departement:departement,
                option:option,
                annee_acad: annee_acad_aff
            };
            //{ affect: "affecter", matricule: mat_etud_aff, promotion_aff: promotion_aff, fac_aff: fac_aff, annee_acad_aff: annee_acad_aff, frais: frais_a_payer, montant_f: montant }
            $.ajax({
                type: "POST",
                url: "../../includes/AffectationFraisEtud.php",
                data: data,
            }).done(function(data) {
                if (data != "" && data == "ok") {
                    // window.location.reload();
                    $("#mod_affectation").modal('toggle');
                    $("#Affich_info").modal('toggle');
                } else {
                    $("#ErrorAff").text("");
                    $("#ErrorAff").text(data).css({ color: 'red' });
                }
            }).fail(function(data) {
                $("#ErrorAff").text("");
                $("#ErrorAff").text("Erreur de connexion,...").css({ color: 'red' });
            });
        } else {
            $("#ErrorAff").text("");
             $("#ErrorAff").text("Veuillez completer tous les champs, ...").css({ color: 'red' });
        }
    });

    $("#delaffect").submit(function(e) {
        e.preventDefault();
        frais_a_payer = Array();

        $('input[name="ch_sh_d"]:checked').each(function() {
            frais_a_payer.push($(this).val());
            frais_a_payer.join(', ');
        });

        const data = {
            d_affect: "d_affect",
            frais: frais_a_payer
        };
        $.ajax({
            type: "POST",
            url: "../../includes/del_affect.php",
            data: data,
        }).done(function(data) {
            if (data != "" && data == "ok") {
                $("#del_affectation").modal('toggle');
                $("#Affich_info").modal('toggle');
            } else {
                // alert(data);
                $("#ErrorAffD").text("");
                $("#ErrorAffD").text(data).css({ color: 'red' });
                // $("#ErrorAff").text("Veuillez selectionner au moins un type de frais").css({ color: 'red' });
            }
        }).fail(function(data) {
            $("#ErrorAffD").text("");
            $("#ErrorAffD").text("Erreur de connexion,...").css({ color: 'red' });
        });
    });
});