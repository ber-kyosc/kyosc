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
            $('#message_content').val('');
            let pseudoAuthor = data.author.firstName;
            if (data.author.pseudo !== null) {
                pseudoAuthor = data.author.pseudo;
            }
            let avatar = "<img src='https://kyosc.com/uploads/profils/default-profil-picture.png' alt='default profil picture' class='avatar-testimony'>";
            if (data.author.profilPhoto !== null) {
                avatar = `<img src='https://kyosc.com/media/cache/avatar_thumb/uploads/profils/${
                    data.author.profilPhoto
                }' class='avatar-testimony'>`;
            }
            const html = `${"<div class='align-self-end text-right position-relative'>"
                    + "<div class='m-3 bubble bubble-bottom-right'>"
                        + "<p class='pre-line'>"}${data.content}</p>`
                    + '</div>'
                    + '<h4 class=\'mt-4\'>'
                        + `<a href='/profil/${data.author.id}' class='w-25 text-center'>${
                            avatar
                        }</a>${
                            pseudoAuthor
                        }</h4>`
                    + '<span class=\'message-date\'>Posté à l\'instant'
                    + '</span>'
                + '</div>';
            commentSpace.prepend(html);
        },
    });
});
