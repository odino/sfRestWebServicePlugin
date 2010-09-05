<?php

/**
 * @package    sfRestWebServicePlugin
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */
class sfRestWebServiceConfiguration
{
  public function __construct(sfApplicationConfiguration $applicationConfiguration, $yaml_handler = 'sfYaml')
  {
    $this->checkYamlHandler($yaml_handler);
    $this->environment                = $applicationConfiguration->getEnvironment();
    $this->application_configuration  = $applicationConfiguration;
    $this->config_path                = $this->getConfigurationPath();
    $this->entry                      = $this->getEntry();
  }

  public function get($param)
  {
    $params = explode('_', $param);

    $configuration = $this->config;

    foreach ($params as $key => $param)
    {
      if (!array_key_exists($param, $configuration))
      {
        $message = 'Please provide a '.$param.' in sfRestWebService config.yml for the '.$this->environment.' environment';
        throw new sfException($message);
      }

      $configuration = $configuration[$param];
    }

    return $configuration;
  }

  protected function checkYamlhandler($yaml_handler)
  {
    if ($yaml_handler != 'sfYaml' && !is_subclass_of($yaml_handler, 'sfYaml'))
    {
      throw new sfException('The yaml handler class must be a subclass of sfYaml');
    }

    $this->handler = $yaml_handler;
  }

  protected function getConfigurationPath()
  {
    $config_path = '/modules/sfRestWebService/config/config.yml';
    $override = sfConfig::get('sf_root_dir').'/apps/'.$this->application_configuration->getApplication().$config_path;
    
    if (file_exists($override))
    {
      return $override;
    }

    return __DIR__.'/../config/config.yml';
  }

  protected function getEntry()
  {
    $handler = $this->handler;
    $params = $handler::load($this->config_path);

    if (array_key_exists($this->environment, $params))
    {
      $this->config = $params[$this->environment];
      return $this->environment;
    }
    elseif(array_key_exists('all', $params))
    {
      $this->config = $params['all'];
      return 'all';
    }

    throw new sfException('You must specify a configuration for the sfRestWebServicePlugin');
  }
}

