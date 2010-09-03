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
    $query = $this->getQuery($request);
  }
  
  public function executeResource(sfWebRequest $request)
  {
    $query = $this->getQuery($request);
  }

  public function execute500(sfWebRequest $request)
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

  protected function checkModelAvailability(sfWebRequest $request)
  {
    $this->model = $request->getParameter('model');
    $models = sfConfig::get('app_ws_models');

    if (is_array($models) && !array_key_exists($this->model, $models))
    {
      $this->forward404();
    }
  }

  protected function getQuery(sfWebRequest $request)
  {
    $this->checkModelAvailability($request);

    if (!class_exists($this->model))
    {
      $this->response->setStatusCode(500);
      $this->forward('sfRestWebService', '500');
    }

    return Doctrine::getTable($this->model)->createQuery('wsmodel');
  }

  protected function isProtected()
  {
    return sfConfig::get('app_ws_protected');
  }
}
