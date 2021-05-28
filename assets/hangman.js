import '../assets/styles/hangman.css'
$(document).on('change','#game_letters input',function (){
    $(this).closest('form').submit();
})

$('form[name="game"]').on('submit',function (e){
    e.preventDefault();
    $.ajax({
        url: '',
        method: 'POST',
        data: $(this).serialize(),
        success: (response) => {
            $('.word').replaceWith($(response).filter('.word'));
            $('.guessed').replaceWith($(response).filter('.guessed'));
            $('.status').replaceWith($(response).filter('.status'));
            $('#game_letters').replaceWith($(response).find('#game_letters'));
        }
    })
})