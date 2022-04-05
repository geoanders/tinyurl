<?php

namespace Drupal\tinyurl\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Checks that the slug is unique/not duplicated.
 *
 * @Constraint(
 *   id = "UniqueSlug",
 *   label = @Translation("Unique TinyUrl Slug", context = "Validation"),
 *   type = "string"
 * )
 */
class UniqueSlug extends Constraint {

  /**
   * The message that will be shown if the value is not unique.
   *
   * @var string
   */
  public $message = 'The slug %value is not unique.';

}
