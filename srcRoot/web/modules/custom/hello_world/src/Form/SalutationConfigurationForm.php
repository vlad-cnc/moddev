<?php
namespace Drupal\hello_world\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SalutationConfigurationForm extends ConfigFormBase {
  protected function getEditableConfigNames() {
    return ['hello_world.custom_salutation'];
  }

  /**
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected $logger;


  /**
   * SalutationConfigurationForm constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   * @param \Drupal\Core\Logger\LoggerChannelInterface $logger
   */
  public function __construct(ConfigFactoryInterface $config_factory, LoggerChannelInterface $logger) {
    parent::__construct($config_factory);
    $this->logger=$logger;
  }

  /**
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *
   * @return static
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('hello_world.logger.channel.hello_world')
    );
  }

  public function getFormId() {
    return 'salutation_configuration_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state){
    $config = $this->config('hello_world.custom_salutation');

    $form['salutation'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Salutation'),
      '#description' => $this->t('Please provide the salutation you want to use'),
      '#default_value' => $config->get('salutation'),
    );

    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('hello_world.custom_salutation')
      ->set('salutation', $form_state->getValue('salutation'))
      ->save();
    $this->logger->info('The Hello World salutation has been changed to @message', ['@message' => $form_state->getValue('salutation')]);
    parent::submitForm($form, $form_state);

  }



  public function validateForm(array &$form, FormStateInterface $form_state) {
    $salutation = $form_state->getValue('salutation');
    if (strlen($salutation) > 20) {
      $form_state->setErrorByName('salutation', $this->t('This salutation is too long'));
    }
  }
}

