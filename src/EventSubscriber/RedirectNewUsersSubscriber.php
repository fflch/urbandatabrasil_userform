<?php

namespace Drupal\urbandatabrasil_userform\EventSubscriber;

use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Path\CurrentPathStack;
use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Redirects anonymous, non-validated users from /node/add to /cadastre-se.
 */
class RedirectNewUsersSubscriber implements EventSubscriberInterface {

  /**
   * The current path stack.
   *
   * @var \Drupal\Core\Path\CurrentPathStack
   */
  protected $pathCurrent;

  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Constructs a new RedirectNewUsersSubscriber object.
   */
  public function __construct(CurrentPathStack $path_current, RequestStack $request_stack, AccountProxyInterface $current_user, MessengerInterface $messenger) {
    $this->pathCurrent = $path_current;
    $this->requestStack = $request_stack;
    $this->currentUser = $current_user;
    $this->messenger = $messenger;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      KernelEvents::REQUEST => ['newUserRedirection'],
    ];
  }

  /**
   * Called when the kernel.request event is dispatched.
   *
   * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
   *   The dispatched event.
   */
  public function newUserRedirection(RequestEvent $event) {
    if ($this->currentUser->isAuthenticated()) {
      return;
    }

    if ($this->pathCurrent->getPath() != '/node/add') {
      return;
    }

    $request = $event->getRequest();
    $usuario_validado = $request->hasSession() ? $request->getSession()->get('usuario_validado') : NULL;
    if (!empty($usuario_validado)) {
      return;
    }

    $this->messenger->addStatus('Faça parte da base de dados bibliográficos do UrbanData-Brasil!', TRUE);
    $event->setResponse(new RedirectResponse('/cadastre-se', 301));
  }

}
