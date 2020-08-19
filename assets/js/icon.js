$('.form_button').click(function(){
    $(this).children('i.fa').removeClass('hide');
});
$('.form_button').mouseout(function(){
    $(this).children('i.fa').addClass('hide');
});