<?php

declare(strict_types=1);

namespace Drupal\tinyurl\Wrappers\Response;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\graphql\GraphQL\Response\Response;

/**
 * Type of response used when an url is returned.
 */
class UrlResponse extends Response {

  /**
   * The url to be served.
   *
   * @var \Drupal\Core\Entity\ContentEntityInterface|null
   */
  protected $url;

  /**
   * Sets the content.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface|null $url
   *   The url to be served.
   */
  public function setUrlNode(ContentEntityInterface $url) {
    $this->url = $url;
  }

  /**
   * Gets the url node to be served.
   *
   * @return \Drupal\Core\Entity\ContentEntityInterface|null
   *   The url node to be served.
   */
  public function getUrlNode() {
    return !empty($this->url) ? $this->url : NULL;
  }

  /**
   * Get the slug.
   *
   * @return string
   */
  public function getSlug(): string {
    $slug = '';
    if ($this->getUrlNode() && $this->getUrlNode()
        ->hasField('field_slug') && !$this->getUrlNode()
        ->get('field_slug')
        ->isEmpty()) {
      $slug = $this->getUrlNode()->get('field_slug')->value;
    }
    return $slug;
  }

  /**
   * Get the url.
   *
   * @return string
   */
  public function getUrl(): string {
    $url = '';
    if ($this->getUrlNode() && $this->getUrlNode()
        ->hasField('field_url') && !$this->getUrlNode()
        ->get('field_url')
        ->isEmpty()) {
      $url = $this->getUrlNode()->get('field_url')->getValue()[0]['uri'];
    }
    return $url;
  }

}
