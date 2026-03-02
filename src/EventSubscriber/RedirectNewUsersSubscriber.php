<?php

namespace Drupal\urbandatabrasil_userform\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Drupal\Core\Path\CurrentPathStack;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class RedirectNewUsersSubscriber.
 */
class RedirectNewUsersSubscriber implements EventSubscriberInterface {

  /**
   * Drupal\Core\Path\CurrentPathStack definition.
   *
   * @var \Drupal\Core\Path\CurrentPathStack
   */
  protected $pathCurrent;

  /**
   * Constructs a new RedirectNewUsersSubscriber object.
   */
  public function __construct(CurrentPathStack $path_current) {
    $this->pathCurrent = $path_current;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    // Escuta os eventos do Kernel usando o novo padrão Symfony.
    $events['kernel.request'] = ['newUserRedirection'];
    return $events;
  }

  /**
   * This method is called when the kernel.request is dispatched.
   *
   * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
   * The dispatched event.
   */
  public function newUserRedirection(RequestEvent $event) {

    $current_path = $this->pathCurrent->getPath();
    
    // É mais seguro pegar a sessão diretamente do request injetado no evento
    $session = $event->getRequest()->getSession()->get('usuario_validado');

    if ($current_path != '/node/add') {
      return;
    }

    if (!empty($session)) {
      return;
    }

    \Drupal::messenger()->addMessage('Faça parte da base de dados bibliográficos do UrbanData-Brasil!', 'status', TRUE);
    $response = new RedirectResponse("/cadastre-se", 301);
    $event->setResponse($response);

  }

}