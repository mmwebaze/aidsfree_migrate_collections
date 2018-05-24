<?php

namespace Drupal\aidsfree_migrate_collections\Plugin\migrate\process;

use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Checks if fid already exists and returns 0 so it gets skipped or ignored
 *
 * @MigrateProcessPlugin(
 *   id = "fid_exists"
 * )
 *
 */
class FileExists extends ProcessPluginBase implements ContainerFactoryPluginInterface{
  /**
   * The entity storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $storage;
  /**
   * FileExists constructor.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param $storage
   *   The entity storage.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityStorageInterface $storage) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->storage = $storage;
  }
  public function transform($value, MigrateExecutableInterface $migrate_executable,
                            Row $row, $destination_property){

    $entity = $this->storage->load($value);
    if ($entity instanceof EntityInterface) {
      print('Should skip id: '.$value);
      return FALSE;
    }
    print('Should import id: '.$value);
    return $value;
  }
  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration = NULL) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')->getStorage($configuration['entity_type'])
    );
  }
}