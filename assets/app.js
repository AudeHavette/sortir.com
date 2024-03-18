import './bootstrap.js';
import 'bootstrap/dist/css/bootstrap.min.css';

/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import 'bootstrap/dist/css/bootstrap.min.css';
import './styles/app.css';
import canvasConfetti from 'canvas-confetti'


console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

//Inscription de l'utilisateur à une sortie via une requête Ajax que le contrôleur va gérer :
$(document).ready(
    function() {

        $('button.btn-inscriptionSortie').click(function() {//Ici, on écoute les click sur le bouton qui a l'id "inscriptionSortie"
            var id = $(this).data('sortie-id');//Je récupère l'id de la sortie à partir de l'id du bouton


        $.ajax({//envoi de la req Ajax au serveur pour inscrire l'utilisateur à sa sortie
            url: '/inscriptionSortie/' + id,
            type: 'POST',
            success: function(response) {
                if (response.success) {

                    $('.confirmation-message').html('Félicitations, vous êtes inscrit à la sortie.');

                    canvasConfetti();

                    $('body').css('background-color', '#f9e5eb');

                    setTimeout(function() {
                        $('.confirmation-message').addClass('slide-out');
                    }, 3000);


                    setTimeout(function() {
                        $('body').css('background-color', '');
                    }, 3000);

                    $('.success-image').addClass('rotate-on-success');

                    $('.letter').addClass('flyUp');
                } else {
                    alert('Inscription impossible.');
                }
            },
            error: function(xhr, status, error) {
                alert('Inscription impossible.');
            }
        });
    });
});


//Fonction JS pour créer des larmes quand l'utilisateur se désiste : je l'appelle dans la requête Ajax de désistement
function createTear() {
    const tear = document.createElement('div');
    tear.classList.add('tear');
    tear.style.left = Math.random() * window.innerWidth + 'px';
    document.querySelector('.tears-container').appendChild(tear);

    setTimeout(() => {
        tear.remove();
    }, 3000);
}

//setInterval(createTear, 100); // Crée une larme toutes les 100 millisecondes


//Désistement de l'utilisateur à une sortie via une requête Ajax que le contrôleur va gérer :
$(document).ready(function() {
    $('button.btn-desistementSortie').click(function() {
        setInterval(createTear, 100);
        var id = $(this).data('sortie-id');

        $.ajax({
            url: '/desistementSortie/' + id,
            type: 'POST',
            success: function(response) {
                if (response.success) {
                    $('.confirmation-message').html('Nous regrettons votre départ, au revoir !.');
                    $('body').css('background-color', '#B0C4DE');
                    setTimeout(function() {
                        $('.confirmation-message').addClass('slide-out');
                    }, 3000);
                    setTimeout(function() {
                        $('body').css('background-color', '');
                    }, 3000);

                    $('.success-image').addClass('rotate-on-success');

                    $('.letter').addClass('flyUp');
                } else {
                    alert('Désistement impossible.');
                }
            },
            error: function(xhr, status, error) {
                alert('Désistement impossible : ' + error);
            }
        });
    });
});

//L'organisateur publie la sortie qu'il a précédemment créer (elle passe de créée à ouverte via cette action)
//Requête Ajax avec effet JS lors de la publication et traitement dans le contrôleur :
$(document).ready(function() {
    $('button.btn-publicationSortie').click(function() {
        var id = $(this).data('sortie-id');

        $.ajax({
            url: '/publicationSortie/' + id,
            type: 'POST',
            success: function(response) {
            if (response.success) {
            $('.confirmation-message').html('Sortie publiée');
            $('body').css('background-color', '#B0C4DE');
            setTimeout(function() {
                $('.confirmation-message').addClass('slide-out');
            }, 3000);
            setTimeout(function() {
                $('body').css('background-color', '');
            }, 3000);

            $('.success-image').addClass('rotate-on-success');

            $('.letter').addClass('flyUp');
        } else {
            alert('Publication impossible.');
        }
    },
            error: function(xhr, status, error) {
                alert('Publication impossible 2: ' + error);
            }
        });
    });
});



