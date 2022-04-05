<?php

namespace Drupal\tinyurl\Plugin\GraphQL\DataProducer;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;
use Drupal\tinyurl\Wrappers\Response\UrlResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Deletes an existing url node.
 *
 * @DataProducer(
 *   id = "delete_url",
 *   name = @Translation("Delete Url"),
 *   description = @Translation("Deletes existing TinyUrl."),
 *   produces = @ContextDefinition("any",
 *     label = @Translation("Url")
 *   ),
 *   consumes = {
 *     "id" = @ContextDefinition("any",
 *       label = @Translation("Url id")
 *     )
 *   }
 * )
 */
class DeleteUrl extends DataProducerPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The Entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * DeleteUrl constructor.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param array $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The Entity type manager service.
   */
  public function __construct(array $configuration, string $plugin_id, array $plugin_definition, AccountInterface $current_user, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentUser = $current_user;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_user'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * Deletes an url node by id.
   *
   * @param int $id
   *   The node id required to delete a url node.
   *
   * @return \Drupal\tinyurl\Wrappers\Response\UrlResponse
   *   The url response.
   *
   * @throws \Exception
   */
  public function resolve(int $id): UrlResponse {
    // Create new url response.
    $response = new UrlResponse();

    // Make sure current user has access to delete url nodes.
    if ($this->currentUser->hasPermission("delete any url content")) {

      // Create url node.
      $node = $this->entityTypeManager->getStorage('node')->load($id);
      if ($node && $node->id()) {
        // Set url node to show that we found it.
        $response->setUrlNode($node);

        // Delete url node.
        $node->delete();
      }
      else {
        $response->addViolation($this->t('Url was not found. Unable to delete url.'));
      }
    }
    else {
      $response->addViolation(
        $this->t('You do not have permissions to delete url.')
      );
    }

    // Return response.
    return $response;
  }

}
