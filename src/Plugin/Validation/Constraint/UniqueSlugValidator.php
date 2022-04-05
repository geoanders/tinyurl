<?php

namespace Drupal\tinyurl\Plugin\Validation\Constraint;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Drupal\tinyurl\TinyUrl;

/**
 * Validates the UniqueSlug constraint.
 */
class UniqueSlugValidator extends ConstraintValidator implements ContainerInjectionInterface {

  /**
   * The TinyUrl service.
   *
   * @var \Drupal\tinyurl\TinyUrl
   */
  protected $tinyUrl;

  /**
   * Constructs a new UniqueSlugValidator.
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
  public static function create(ContainerInterface $container): UniqueSlugValidator {
    return new static(
      $container->get('tinyurl')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function validate($items, Constraint $constraint) {
    // Loop over values in field. Should only be one in this case.
    foreach ($items as $item) {
      // Check to see if slug is not unique. Add error message if so.
      if (!empty($item->value) && !$this->isSlugUnique($item->value, (int) $items->getEntity()
          ->id())) {
        $this->context->addViolation($constraint->message, ['%value' => $item->value]);
      }
    }
  }

  /**
   * Is slug unique. Make sure slug is not already in use.
   *
   * @param string $slug
   *   The slug.
   * @param int $nid
   *   The excluded url node id.
   *
   * @return bool
   *   Returns TRUE if unique, FALSE if not unique.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  private function isSlugUnique(string $slug, int $nid = 0): bool {
    $isUnique = FALSE;

    // Build up exclusion array.
    $excluded = [];
    if ($nid) {
      $excluded[] = $nid;
    }

    // Check to see if slug exists. If not, then it's unique.
    if (!$this->tinyUrl->slugExists($slug, $excluded)) {
      // Slug already exists, set error.
      $isUnique = TRUE;
    }

    // Return unique flag.
    return $isUnique;
  }

}
