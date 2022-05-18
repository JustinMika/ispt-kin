$(document).ready(function() {
    afficher_annee_acad();
    $("#f0rm").submit(function(e) {
        e.preventDefault();
        var annee_0_acad = $("#annee_0_acad");

        // alert($("#annee_0_acad").val());

        if ($("#annee_0_acad").val() != "") {
            $.ajax({
                type: "POST",
                url: "../../includes/add_annee_acad.php",
                data: $('#f0rm').serializeArray(),
            }).done(function(data) {
                if (data == "ok") {
                    afficher_annee_acad();
                    $("#annee_0_acad").val("");
                } else {
                    $("#error_s")
                        .addClass('text-danger text-center mt-1')
                        .css({
                            'font-weight': '500'
                        })
                        .text(data);
                }
            }).fail(function(data) {
                $("#error_s")
                    .addClass('text-danger text-center mt-1')
                    .css({
                        'font-weight': '500'
                    })
                    .text("Erreur : l'annee acadenmique n'est pas entegistre");
            });
        } else {
            $(annee_0_acad).val("");
            $("#error_s")
                .addClass('text-warning text-center mt-1')
                .css({
                    'font-weight': '500'
                })
                .text("Erreur : Veuillez remplir ce champs svp");
            $("#error_s").text("")
        }
    });

    $("#del_").click(function(e) {
        e.preventDefault();
    });

    $("#modifier").click(function(e) {
        e.preventDefault();
    });

    function afficher_annee_acad() {
        $.ajax({
            type: "GET",
            url: "../../includes/list_annee_.php",
        }).done(function(data) {
            if (data != "") {
                $($("#b_table")).empty();
                $("#b_table").append(data);
            } else {
                $($("#b_table")).empty();
                $("#b_table").append("<td><marquee>Pas de donnees pour l'instant.</marquee></td>");
            }
        }).fail(function(data) {});
    }

    // update annee academique
    $("#update_annee_acad").submit(function(e) {
        e.preventDefault();

        if ($("#id_m_annee_acad").val() != "" && $("#update_ann_acad").val() != "") {
            $.ajax({
                type: "POST",
                url: "../../includes/update_annee_acad.php",
                data: $("#update_annee_acad").serializeArray(),
                success: function(response) {
                    if (response == "ok") {
                        afficher_annee_acad();
                        $("#MyModalModif").modal('toggle');
                    } else {
                        $("#helpId_error").html(response);
                    }
                },
                error: function(error) {
                    $("#helpId_error").html(error);
                }
            });
        } else {
            $("#helpId_error").html("veuilez completer tous les champs...");
        }

    });

    setInterval(() => {
        afficher_annee_acad();
    }, 3000);
});