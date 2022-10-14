//import Swal from 'sweetalert2';
//animate css

import "animate.css";

let app = {
        init: function(){
            // Ecouteur bouton vote
            $('#ratingP').on('click', app.vote)
        },
        vote: function(e){
            // Stop chargement page suite à clic sur lien
            e.preventDefault();
             //Vérifications des données
             let token = $("#token").val();
             let ratingProject = $('input[name=ratingProject]:checked', '#ratingForm').val();

             if (token.length === 0 ) {
                $("#token").after("<span>Erreur de formulaire</span>");
             }

             if (!ratingProject) {
                $("#error").after("<p class='amour'>Merci de choisir une note</p>");
             }
             else{

                if (parseInt(ratingProject) != 1 && parseInt(ratingProject) != 2 && parseInt(ratingProject) != 3 && parseInt(ratingProject) != 4 ) {
                    $("#error").after("<p class='amour'>Erreur de formulaire</p>");
                }
                else{
                     // Récupération des données
                    let ratingForm = $('#ratingForm').serializeArray();
                    // Envoi du POST
                    $.post({
                        //L'URL de la requête
                        url: "/projets/rating",
                        data: ratingForm

                    })
                    .done(function(data){
                        // En cas d'erreur, on l'affiche
                        if (data.error){
                            alert(data.message);
                        }
                        else {
                            location.reload();
                        }
                    })
                    .fail(function(){
                        alert('Erreur de connexion au serveur :/');
                    });
                }
            }
        }
}

$(app.init);

$('#notation-ticket').on('click',function(e){
    e.preventDefault();
    Swal.fire({
        title: 'Envoyer la demande de notation ?',
        showDenyButton: true,
        allowOutsideClick: false,
        confirmButtonText: 'Oui, envoyer',
        denyButtonText: `Annuler`,
    }).then((result) => {

        if (result.isConfirmed) {
            //redirection
            let projectId = document.getElementById('notation-ticket');
            let projectData = projectId.dataset.ticket;
            let url = "/projets/" + encodeURIComponent(projectData) + "/notation-ticket";
            window.location.href = url;
        } else if (result.isDenied) {
            Swal.fire('Annulé !', '', 'info')
        }
    })
})


