$(document).ready(function() {
    $(".btn-toggle-visibility").on('click', function(event) {
        event.preventDefault();
		var passInput = $(this).parent().prev();
        if(passInput.attr("type") == "text"){
            passInput.attr('type', 'password');
            $(this).children('.fa').removeClass("fa-eye-slash");
            $(this).children('.fa').addClass("fa-eye");
        }else if(passInput.attr("type") == "password"){
            passInput.attr('type', 'text');
            $(this).children('.fa').removeClass("fa-eye");
            $(this).children('.fa').addClass("fa-eye-slash");
        }
    });
});