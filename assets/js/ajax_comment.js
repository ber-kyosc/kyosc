const $ = require('jquery');

const commentField = document.querySelector('#message_content');
const commentForm = $('#commentForm');
const commentSpace = $('.comment-space');

commentForm.submit((e) => {
    e.preventDefault();

    $.ajax({
        type: commentForm.attr('method'),
        url: commentForm.attr('action'),
        data: commentForm.serialize(),
        success(data) {
            console.log('Submission was successful.');
            $('#message_content').val('');
            console.log(data);
            let pseudo = data.author.firstName;
            if (data.author.profilPhoto !== null) {
                pseudo = data.author.pseudo;
            }
            let avatar = "<img src='/uploads/profils/default-profil-picture.png' alt='default profil picture' class='avatar-testimony'>";
            if (data.author.profilPhoto !== null) {
                avatar = "<img src='http://127.0.0.1:8000/media/cache/avatar_thumb/uploads/profils/"
                    + data.author.profilPhoto
                    + "' class='avatar-testimony'>";
            }
            const html = "<div class='align-self-end text-right position-relative'>"
                    + "<div class='m-3 bubble bubble-bottom-right'>"
                        + "<p class='pre-line'>" + data.content + '</p>'
                    + '</div>'
                    + "<h4 class='mt-4'>"
                        + "<a href='/profil/" + data.author.id + "' class='w-25 text-center'>"
                            + avatar
                        + '</a>'
                        + pseudo
                    + '</h4>'
                    + "<span class='message-date'>Posté à l'instant"
                    + '</span>'
                + '</div>';
            commentSpace.prepend(html);
        },
    });
});
