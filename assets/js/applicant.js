//import Swal from "sweetalert2";
//import Swal from 'sweetalert2/dist/sweetalert2.js'
//animate css
import "animate.css";

let app = {
        init: function(){
            // Ecouteur bouton submit
            $('#applicant_email').on('change', app.cheeck)
        },
        cheeck: function(e){
            //stop comportement naturel
            e.preventDefault();
            //Récupération des données
            let mail = $('#applicant_email').val();
           
            // Envoi du POST
            $.post({
                //L'URL de la requête
                url: "/contacts/cheeck",
                data: {mail: mail}

            })
            .done(function(data){
                
                if (data === 404){
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Cet email existe déjà dans la base, vous allez créer un doublon !',
                        footer: '<a href="../contacts/liste">Liste des contacts</a>'
                      })
                }
            })
            
                    
        }
}
$(app.init);