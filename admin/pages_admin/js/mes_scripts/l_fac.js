$(document).ready(function() {
    afficher_facul();
    afficher_depart();
    $("#f0rm-fac").submit((e) => {
        e.preventDefault();
        var fac = $("#fac");

        $("#l").click(function(e) {
            e.preventDefault();
            $(fac).val("");
        });

        if ($(fac).val() != "" && $(fac).val().length >= 5) {
            $.ajax({
                type: "POST",
                url: "../../includes/add_fac.php",
                data: $('#f0rm-fac').serializeArray(),
            }).done(function(data) {
                if (data == "ok") {
                    // if()
                    $("#fac").val("");
                    window.location.reload();
                    afficher_facul();
                } else {
                    $("#fac").val("");
                    $("#error_s")
                        .addClass('text-danger text-center mt-1')
                        .css({
                            'font-weight': '500'
                        })
                        .text(data)
                        .slideUp(10000);
                    // $("#modelId").modal('toggle');
                }
            }).fail(function(data) {
                $("#error_s")
                    .addClass('text-danger text-center mt-1')
                    .css({
                        'font-weight': '500'
                    })
                    .text("Erreur : une inconue est survenue : la faculte n est pas ajouter dans la base de donnees.");
            });
        } else {
            $("#error_s")
                .addClass('text-warning text-center mt-1')
                .css({
                    'font-weight': '500'
                })
                .text("Erreur : la faculte doit avoir au moins 5 caraceteres");
        }
    });

    function afficher_facul() {
        $.ajax({
            type: "GET",
            url: "../../includes/list_fac.php",
        }).done(function(data) {
            if (data != "") {
                $($("#f_table")).empty();
                $("#f_table").append(data);
            } else {
                $($("#f_table")).empty();
                $("#f_table").parent().append("<caption>Pas de donnees pour l'instant.</caption>");
            }
        }).fail(function(data) {});
    }

    function afficher_depart() {
        $.ajax({
            type: "GET",
            url: "../../includes/list_depart.php",
        }).done(function(data) {
            alert("Hello!");
            if (data != "") {
                $($("#f_table_depart")).empty();
                $("#f_table_depart").append(data);
            } else {
                $($("#f_table_depart")).empty();
                $("#f_table_depart").parent().append("<caption>Pas de donnees pour l'instant.</caption>");
            }
        }).fail(function(data) {});
    }

    $("#update_faculte").submit(function(e) {
        e.preventDefault();
        if ($("#fac_a_modifier").val() != "" && $("#id_fac_a_modifier").val() != "") {
            $.ajax({
                type: "POST",
                url: "../../includes/update_fac.php",
                data: $('#update_faculte').serializeArray(),
                success: function(response) {
                    if (response == "ok") {
                        $("#Modify_fac").modal('toggle');
                        afficher_facul();
                    } else {
                        $("#helpId_f").text("ERROR:faculte non modifier");
                    }
                },
                error: function(e) {
                    $("#helpId_f").text("ERROR: " + e);
                }
            });
        } else {
            $("#helpId_f").text("ERROR:veuillez selectionner une faculte");
        }
    });

    // setInterval(function() {
    //     afficher_facul();
    // }, 500);
});