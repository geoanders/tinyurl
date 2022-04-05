<?php

namespace Drupal\tinyurl;

use Drupal\Component\Utility\Random;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * The TinyUrl Service.
 */
class TinyUrl {

  /**
   * The Entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * TinyUrl Constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The Entity type manager service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }


  /**
   * Generate random slug.
   *
   * @param int $length
   *   The length of the generated slug.
   *
   * @return string
   *   Returns generated slug.
   */
  public function generateSlug(int $length = 10): string {
    $random = new Random();
    return $random->name($length, TRUE);
  }

  /**
   * Get url node by slug.
   *
   * @param string $slug
   *   The slug.
   *
   * @return false|mixed
   *   Returns url node if found.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  private function getUrlBySlug(string $slug) {
    $nodes = $this->entityTypeManager->getStorage('node')->loadByProperties([
      'field_slug' => $slug,
      'type' => 'url',
    ]);

    return reset($nodes);
  }

  /**
   * Check to see if slug exists.
   *
   * @param string $slug
   *   The slug.
   * @param array $excluded
   *   The array of excluded url node ids.
   *
   * @return bool
   *   Returns TRUE if slug exists, FALSE if slug does not exist.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function slugExists(string $slug, array $excluded = []): bool {
    // Get query.
    $query = $this->entityTypeManager->getStorage('node')->getQuery();

    // Filter results by slug and node type.
    $query = $query
      ->condition('field_slug', $slug, '=')
      ->condition('type', 'url', '=');

    // Url node ids to exclude from result set.
    if (!empty($excluded)) {
      $query = $query->condition('nid', $excluded, 'NOT IN');
    }

    // Return count.
    return (bool) $query->count()->execute();
  }

  /**
   * Redirect slug to end url.
   *
   * @param string $slug
   *   The slug.
   *
   * @return \Drupal\Core\Routing\TrustedRedirectResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
   *   Returns a response.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function redirectSlug(string $slug) {
    $response = new Response();

    // Make sure we have a slug.
    if (!empty($slug)) {
      // Get url node based on slug and node type.
      /** @var \Drupal\node\Entity\Node $urlNode */
      $urlNode = $this->getUrlBySlug($slug);

      // Make sure we have node object and url field exists/not empty.
      if ($urlNode && $urlNode->hasField('field_url') && !$urlNode->get('field_url')->isEmpty()) {

        // Check to see if we have uri.
        if (!empty($urlNode->get('field_url')->getValue()[0]['uri'])) {

          // Get url object.
          $url = Url::fromUri($urlNode->get('field_url')->getValue()[0]['uri']);

          // If external, need to adjust response type.
          if ($url->isExternal()) {
            $response = new TrustedRedirectResponse($url->toString());
            $response->addCacheableDependency($urlNode);
          }
          else {
            $response = new RedirectResponse($url->toString());
          }
        }
      }
      else {
        if ($url = Url::fromRoute('system.404')) {
          $response = new RedirectResponse($url->toString());
        }
        else {
          throw new NotFoundHttpException();
        }
      }
    }

    // Return response.
    return $response;
  }

}
