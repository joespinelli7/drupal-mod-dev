<?php

namespace Drupal\redirect_unauthenticated_user\EventSubscriber;

use Drupal\Core\Session\AccountProxy;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Path\PathMatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Event subscriber subscribing to KernelEvents::REQUEST.
 */
class RedirectAnonymousSubscriber implements EventSubscriberInterface {
  /**
   * Provide HTTP status code.
   *
   * @var redirection
   */
  private $redirectCode = 301;

  /**
   * Provide current user.
   *
   * @var \Drupal\Core\Session\AccountProxy
   */
  protected $currentUser;

  /**
   * Provide current route.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $routeMatch;

  /**
   * Provide path Matcher.
   *
   * @var \Drupal\Core\Path\PathMatcher
   */
  protected $pathMatcher;

  /**
   * Constructor.
   *
   * @param \Drupal\Core\Session\AccountProxy $currentUser
   *   The current user.
   * @param \Drupal\Core\Routing\CurrentRouteMatch $routeMatch
   *   The current route.
   * @param \Drupal\Core\Path\PathMatcher $pathMatcher
   *   The path matcher.
   */
  public function __construct(AccountProxy $currentUser, CurrentRouteMatch $routeMatch, PathMatcher $pathMatcher) {
    $this->currentUser = $currentUser;
    $this->routeMatch = $routeMatch;
    $this->pathMatcher = $pathMatcher;
  }

  /**
   * {@inheritdoc}
   */
  public function checkForRedirection(GetResponseEvent $event) {
    $is_authenticated = $this->currentUser->isAuthenticated();
    $route_name = $this->routeMatch->getRouteName();
    $is_front = $this->pathMatcher->isFrontPage();

    if ($is_authenticated || $route_name === 'user') {
      return;
    }

    if ($route_name === 'system.404' || $is_front) {
      header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
      header('Cache-Control: post-check=0, pre-check=0', FALSE);
      header('Pragma: no-cache');
      define('HIDSITE', 'https://www.google.com');
      $response = new RedirectResponse(HIDSITE, $this->redirectCode);
      $response->send();
      $event->setResponse($response);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = ['checkForRedirection'];
    return $events;
  }

}
