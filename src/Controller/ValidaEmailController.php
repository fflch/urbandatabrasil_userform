<?php

namespace Drupal\urbandatabrasil_userform\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ValidaEmailController.
 */
class ValidaEmailController extends ControllerBase {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Constructs a new ValidaEmailController object.
   */
  public function __construct(Connection $database) {
    $this->database = $database;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }

  /**
   * Checks if the e-mail has a user webform submission.
   */
  public function validate(Request $request) {
    $email = FALSE;
    $posted = (string) $request->request->get('email', '');

    if ($posted !== '') {
      $posted = filter_var($posted, FILTER_SANITIZE_EMAIL);
      $query = $this->database->select('webform_submission_data', 'webform');
      $query->condition('webform.name', 'email', '=');
      $query->condition('webform.value', $posted, '=');
      $query->fields('webform', ['sid']);
      $sid = $query->execute()->fetchField();
      if (!empty($sid)) {
        $webformSubmission = $this->entityTypeManager()->getStorage('webform_submission')->load($sid);
        if ($webformSubmission) {
          $email = $webformSubmission->getData()['email'] ?? $posted;
          $request->getSession()->set('usuario_validado', $email);
        }
      }
    }

    return new JsonResponse(['retorno' => $email]);
  }

  /**
   * Stores the posted e-mail in the session.
   */
  public function sessao(Request $request) {
    $email = FALSE;
    $posted = (string) $request->request->get('email', '');

    if ($posted !== '') {
      $email = filter_var($posted, FILTER_SANITIZE_EMAIL);
      $request->getSession()->set('usuario_validado', $email);
    }

    return new JsonResponse(['retorno' => $email]);
  }

}
