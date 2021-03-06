<?php

namespace Drupal\openy_gc_auth;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the base plugin for GCIdentityProvider classes.
 *
 * @see \Drupal\openy_gc_auth\GCIdentityProviderManager
 * @see \Drupal\openy_gc_auth\GCIdentityProviderInterface
 * @see \Drupal\openy_gc_auth\Annotation\GCIdentityProvider
 * @see plugin_api
 */
abstract class GCIdentityProviderPluginBase extends PluginBase implements GCIdentityProviderInterface, ContainerFactoryPluginInterface {

  use StringTranslationTrait;

  /**
   * The configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactoryInterface $config, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->configFactory = $config;
    // We use pre-saved configuration here.
    $configuration = $this->configFactory->get($this->getConfigName())->get();
    $this->setConfiguration($configuration);
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($configuration, $plugin_id, $plugin_definition, $container->get('config.factory'), $container->get('entity_type.manager'));
  }

  /**
   * {@inheritdoc}
   */
  public function getId() {
    return $this->pluginDefinition['id'];
  }

  /**
   * {@inheritdoc}
   */
  public function getLabel() {
    return $this->pluginDefinition['label'];
  }

  /**
   * {@inheritdoc}
   */
  public function getConfigName() {
    return $this->pluginDefinition['config'];
  }

  /**
   * {@inheritdoc}
   */
  private function baseConfigurationDefaults():array {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration():array {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getConfiguration() {
    return $this->configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function setConfiguration(array $configuration) {
    $this->configuration = NestedArray::mergeDeep(
      $this->baseConfigurationDefaults(),
      $this->defaultConfiguration(),
      $configuration
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['admin_label'] = [
      '#type' => 'page_title',
      '#title' => $this->getLabel(),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
    // This method not required.
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    // Process the settings save if no errors occurred only.
    if (!$form_state->getErrors()) {
      // Save config in active storage.
      $configuration = $this->configFactory->getEditable($this->getConfigName());
      $configuration->setData($this->configuration);
      $configuration->save();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getDataForApp():array {
    return [
      'type' => $this->getId(),
    ];
  }

}
