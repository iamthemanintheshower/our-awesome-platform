$(document).ready( function() {

    $('.filesystem-nav').find('ul').hide();

    $('.folder-nav a').click( function() {
        $(this).parent().find('ul:first').slideToggle('fast');
        if($(this).parent().attr('className') === 'folder-nav') return false;
    });

});