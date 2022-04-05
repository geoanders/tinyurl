<?php

namespace Drupal\tinyurl\Plugin\GraphQL\DataProducer;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;
use Drupal\node\Entity\Node;
use Drupal\tinyurl\TinyUrl;
use Drupal\tinyurl\Wrappers\Response\UrlResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Creates a new url entity.
 *
 * @DataProducer(
 *   id = "create_url",
 *   name = @Translation("Create Url"),
 *   description = @Translation("Creates a new TinyUrl."),
 *   produces = @ContextDefinition("any",
 *     label = @Translation("Url")
 *   ),
 *   consumes = {
 *     "data" = @ContextDefinition("any",
 *       label = @Translation("Url data")
 *     )
 *   }
 * )
 */
class CreateUrl extends DataProducerPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The TinyUrl service.
   *
   * @var \Drupal\tinyurl\TinyUrl
   */
  protected $tinyUrl;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_user'),
      $container->get('tinyurl')
    );
  }

  /**
   * CreateUrl constructor.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param array $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   * @param \Drupal\tinyurl\TinyUrl $tiny_url
   *   The TinyUrl service.
   */
  public function __construct(array $configuration, string $plugin_id, array $plugin_definition, AccountInterface $current_user, TinyUrl $tiny_url) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentUser = $current_user;
    $this->tinyUrl = $tiny_url;
  }

  /**
   * Creates an url.
   *
   * @param array $data
   *   The data required to create a new url node.
   *
   * @return \Drupal\tinyurl\Wrappers\Response\UrlResponse
   *   The newly created url node.
   *
   * @throws \Exception
   */
  public function resolve(array $data): UrlResponse {
    // Create new url response.
    $response = new UrlResponse();

    // Make sure current user has access to create url nodes.
    if ($this->currentUser->hasPermission("create url content")) {

      // Setup values to save for url node.
      $values = [
        'type' => 'url',
      ];

      // Attach url if present, though it's required.
      if (!empty($data['url'])) {
        $values['field_url'] = [
          'uri' => $data['url'],
        ];
      }

      // Attach slug if present.
      if (!empty($data['slug'])) {
        $values['field_slug'] = $data['slug'];
      }

      // Create url node.
      $node = Node::create($values);

      // Validate node
      $violations = $node->validate();
      if ($violations->count() > 0) {
        // Add violations to response.
        foreach ($violations as $violation) {
          $response->addViolation((string) $violation->getMessage());
        }
      }
      else {
        // Create url node.
        $node->save();

        // Set url node for response.
        $response->setUrlNode($node);
      }
    }
    else {
      $response->addViolation(
        $this->t('You do not have permissions to create urls.')
      );
    }

    // Return response.
    return $response;
  }

}
