<?php

declare(strict_types=1);

namespace Drupal\tinyurl\Wrappers\Response;

use Drupal\Core\Entity\EntityInterface;
use Drupal\graphql\GraphQL\Response\Response;

/**
 * Type of response used when an url is returned.
 */
class UrlResponse extends Response {

  /**
   * The url to be served.
   *
   * @var \Drupal\Core\Entity\EntityInterface|null
   */
  protected $url;

  /**
   * Sets the content.
   *
   * @param \Drupal\Core\Entity\EntityInterface|null $url
   *   The url to be served.
   */
  public function setUrlNode(EntityInterface $url) {
    $this->url = $url;
  }

  /**
   * Gets the url node to be served.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   The url node to be served.
   */
  public function getUrlNode(): ?EntityInterface {
    return !empty($this->url) ? $this->url : NULL;
  }

}
