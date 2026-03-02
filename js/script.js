(function (Drupal) {
  Drupal.behaviors.urbandataForm = {
    attach: function (context, settings) {
      
      const formControleAcesso = context.querySelector('#controle-acesso');
      
      if (formControleAcesso && !formControleAcesso.dataset.jsAttached) {
        formControleAcesso.dataset.jsAttached = 'true'; 
        
        formControleAcesso.addEventListener('submit', function (event) {
          event.preventDefault(); 
          
          const emailInput = document.getElementById('email');
          const emailValue = emailInput ? emailInput.value : '';
          const formData = new URLSearchParams(new FormData(this));

          // Usamos Drupal.url() para respeitar o /pt-br/
          fetch(Drupal.url('valida_email'), {
            method: 'POST',
            body: formData,
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded',
            }
          })
          .then(response => response.json())
          .then(data => {
            if (data.retorno === false) {
              window.location.href = Drupal.url('form/user') + '?email=' + encodeURIComponent(emailValue);
            } else {
              window.location.href = Drupal.url('node/add');
            }
          })
          .catch(error => console.error('Erro ao validar email:', error));
        });
      }

      const formWebform = context.querySelector('#webform-submission-user-add-form');
      if (formWebform && !formWebform.dataset.jsAttached) {
        formWebform.dataset.jsAttached = 'true';
        
        formWebform.addEventListener('submit', function (event) {
          event.preventDefault();
          
          const formData = new URLSearchParams(new FormData(this));

          fetch(Drupal.url('configura_sessao'), {
            method: 'POST',
            body: formData,
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded',
            }
          })
          .then(response => response.json())
          .then(data => {
            if (data.retorno !== null && data.retorno !== false) {
              window.location.href = Drupal.url('node/add');
            }
          })
          .catch(error => console.error('Erro ao configurar sessão:', error));
        });
      }

    }
  };
})(Drupal);