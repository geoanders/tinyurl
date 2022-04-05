<?php

namespace Drupal\tinyurl\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\tinyurl\TinyUrl;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * The TinyUrlController class.
 */
class TinyUrlController extends ControllerBase {

  /**
   * The TinyUrl service.
   *
   * @var \Drupal\tinyurl\TinyUrl
   */
  protected $tinyUrl;

  /**
   * TinyUrlController Constructor.
   *
   * @param \Drupal\tinyurl\TinyUrl $tiny_url
   *   The TinyUrl service.
   */
  public function __construct(TinyUrl $tiny_url) {
    $this->tinyUrl = $tiny_url;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): TinyUrlController {
    return new static(
      $container->get('tinyurl')
    );
  }

  /**
   * Slug redirection.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request.
   * @param string $slug
   *   The slug.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   Returns the response.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function slugRedirect(Request $request, string $slug = ''): Response {
    return $this->tinyUrl->redirectSlug($slug);
  }

}
