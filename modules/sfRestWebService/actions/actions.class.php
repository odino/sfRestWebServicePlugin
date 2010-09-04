<?php

class sfRestWebServiceActions extends sfActions
{
  public function preExecute()
  {
    parent::preExecute();

    $manager = Doctrine_Manager::getInstance();
    $manager->setAttribute(Doctrine::ATTR_VALIDATE, Doctrine::VALIDATE_ALL);

    if ($this->isProtected())
    {
      $this->authenticate($this->request);
    }

    $this->feedback = '';
  }

  public function executeEntry(sfWebRequest $request)
  {
    $query = $this->getQuery($request);
    $this->executeRequest($query, $request);
  }
  
  public function executeResource(sfWebRequest $request)
  {
    $query = $this->getQuery($request);
    $this->object = $query->where('id = ?', $request->getParameter('id'))->fetchOne();

    if (!$this->object)
    {
      $this->feedback = 'Unable to load the specified resource';
      $this->setTemplate('500');
    }
    else
    {
      $this->setTemplate('object');
      $this->executeRequest($query, $request);
    }
  }

  public function execute500(sfWebRequest $request)
  {
    $this->feedback = 'Internal server error: unsupported service';
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

  protected function executeRequest(Doctrine_Query $query, sfWebRequest $request)
  {
    // TODO: use an inflector
    $request_type = 'execute'.ucfirst((strtolower($request->getMethod()))).'Request';
    $this->$request_type($query, $request);
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

  protected function executeDeleteRequest(Doctrine_Query $query, sfWebRequest $request)
  {
    $this->object->delete();
    $this->feedback = 'Object has been deleted';
    $this->setTemplate('delete');
  }

  protected function executeGetRequest(Doctrine_Query $query, sfWebRequest $request)
  {
    $this->objects = $query->execute();
  }


  protected function executePostRequest(Doctrine_Query $query, sfWebRequest $request)
  {
    $this->setTemplate('object');
    $this->object = new $this->model;
    $this->updateObject($request);
  }

  protected function executePutRequest(Doctrine_Query $query, sfWebRequest $request)
  {
    $this->updateObject($request);
  }

  protected function updateObject(sfWebRequest $request)
  {
    // TODO: nedd a way to retrieve only PUT parameters, not POST
    $this->object->fromArray($request->getPostParameters());

    try
    {
      $this->object->save();
    }
    catch (Exception $e)
    {
      $this->feedback = $e->getMessage();
      $this->setTemplate('500');
    }
  }
}
