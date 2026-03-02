<?php

namespace Drupal\urbandatabrasil_userform\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class ValidaEmailController.
 */
class ValidaEmailController extends ControllerBase {

  /**
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * {@inheritdoc}
   */
  public function __construct(Connection $database, RequestStack $request_stack) {
    $this->database = $database;
    $this->requestStack = $request_stack;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database'),
      $container->get('request_stack')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function validate() {
    // Usa a requisição atual do Symfony em vez de acessar $_POST diretamente
    $current_request = $this->requestStack->getCurrentRequest();
    $email_post = $current_request->request->get('email');

    $email = false;

    if (!empty($email_post)) {
      $email = filter_var($email_post, FILTER_SANITIZE_EMAIL);
      $query = $this->database->select('webform_submission_data', 'webform');
      $query->condition('webform.name', 'email', '=');
      $query->condition('webform.value', $email, '=');
      $query->fields('webform', ['sid']);
      $sid = $query->execute()->fetchField();
      
      if (!empty($sid)) {
        // Correção do "\drupal" para "\Drupal"
        $webformSubmission = \Drupal::entityTypeManager()->getStorage('webform_submission')->load($sid);
        $email = $webformSubmission->getData()['email'];
        $session = $current_request->getSession();
        $session->set('usuario_validado', $email);
      } else {
        $email = false;
      }
    }
    
    $response['retorno'] = $email;
    return new JsonResponse($response);
  }

  /**
   * {@inheritdoc}
   */
  public function sessao(Request $request) {
    $email = false;
    $email_post = $request->request->get('email');

    if (!empty($email_post)) {
      $email = filter_var($email_post, FILTER_SANITIZE_EMAIL);
      $session = $request->getSession();
      $session->set('usuario_validado', $email);
    }
    
    $response['retorno'] = $email;
    return new JsonResponse($response);
  }

}