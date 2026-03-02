document.addEventListener('DOMContentLoaded', function() {
  
  // Primeiro formulário: controle-acesso
  const formControleAcesso = document.getElementById('controle-acesso');
  if (formControleAcesso) {
    formControleAcesso.addEventListener('submit', function(event) {
      event.preventDefault(); // Previne a ação padrão
      
      const emailInput = document.getElementById('email');
      const emailValue = emailInput ? emailInput.value : '';
      const formData = new URLSearchParams(new FormData(this)); // Simula o .serialize() do jQuery

      fetch('/valida_email', {
        method: 'POST',
        body: formData,
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.retorno === false) {
          window.location.href = '/form/user?email=' + encodeURIComponent(emailValue);
        } else {
          window.location.href = '/node/add';
        }
      })
      .catch(error => console.error('Erro ao validar email:', error));
    });
  }

  // Segundo formulário: webform-submission-user-add-form
  const formWebform = document.getElementById('webform-submission-user-add-form');
  if (formWebform) {
    formWebform.addEventListener('submit', function(event) {
      event.preventDefault();
      
      const formData = new URLSearchParams(new FormData(this));

      fetch('/configura_sessao', {
        method: 'POST',
        body: formData,
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.retorno !== null && data.retorno !== false) {
          window.location.href = '/node/add';
        }
      })
      .catch(error => console.error('Erro ao configurar sessão:', error));
    });
  }

});