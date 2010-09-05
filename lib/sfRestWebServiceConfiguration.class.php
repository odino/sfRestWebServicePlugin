<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @package    qredd
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */
class sfRestWebServiceConfiguration
{
  public function __construct(sfYaml $yaml_handler, $environment)
  {
    $this->handler     = $yaml_handler;
    $this->environment = $environment;
    $this->config_path = $this->getConfigurationPath();

    return $this;
  }

  protected function getConfigurationPath()
  {
    // TODO: avoid sfContext
    $application = sfContext::getInstance()->getConfiguration()->getApplication();

    if (file_exists($override))
    {
      return $override;
    }

    return sfConfig::get('sf_plugins_dir').'/sfRestWebServicePlugin/modules/sfRestWebService/config/config.yml';
  }
}

