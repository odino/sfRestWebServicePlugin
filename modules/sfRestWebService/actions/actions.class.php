<?php

class sfRestWebServiceActions extends sfActions
{
  public function preExecute()
  {
    parent::preExecute();

    if ($this->isProtected())
    {
      $this->authenticate($this->request);
    }
  }

  public function executeEntry(sfWebRequest $request)
  {
  }
  
  public function executeResource(sfWebRequest $request)
  {
  }

  protected function authenticate(sfWebRequest $request)
  { 
    $ip_addresses = sfConfig::get('app_ws_allowed');

    if (is_array($ip_addresses) && in_array($request->getRemoteAddress(), $ip_addresses))
    {
      return true;
    }

    $this->response->setStatusCode('403');
    $this->redirect(sfConfig::get('app_ws_protected_route'), '403');
  }

  protected function isProtected()
  {
    return sfConfig::get('app_ws_protected');
  }
}
