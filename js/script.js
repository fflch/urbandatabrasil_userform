(function($) {
  $(document).ready(function(){
    $('#controle-acesso').submit(function(event){
      event.preventDefault(); //prevent default action 
      var dados = $( this ).serialize();
      var email = $("#email").val();
      $.ajax({
        type: "POST",
        url: "/valida_email",
        data: dados,
        success: function( data ) {
          if (data.retorno == false) {
            window.location.href = '/form/user?email='+email;
          }else {
            window.location.href = '/node/add';
          }
        }
      });
      return false;
    });

    $('#webform-submission-user-add-form').submit(function(event){
      var dados = $( this ).serialize();
        $.ajax({
        type: "POST",
        url: "/configura_sessao",
        data: dados,
        success: function( data ) {
          if (data.retorno != null) {
            // $('#webform-submission-user-add-form').submit();
            // window.location.href = '/node/add';
          }
        }
      });
      return true;
    });

  });
}(jQuery));
