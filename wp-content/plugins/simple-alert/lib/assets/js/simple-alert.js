jQuery(document).ready(function($){
    $('.toast__close').click(function(e){
      e.preventDefault();
      var parent = $(this).parent('.toast');
      parent.fadeOut("slow", function() { $(this).remove(); } );
      $('.toast__main').fadeOut("slow", function() { $(this).remove(); } );
    });
  });