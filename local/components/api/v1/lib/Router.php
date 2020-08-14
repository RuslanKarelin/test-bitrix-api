<?php

class Router
{
    protected $params;
    protected $controller;

    protected function runControllerMethod()
    {
        switch ($this->params['method']) {
            case ($this->params['method'] == 'get' && isset($this->params['id'])):
                $this->controller->show();
                break;
            case 'get':
                $this->controller->index();
                break;
            case 'post':
                $this->controller->create();
                break;
            case 'patch':
                $this->controller->update();
                break;
            case 'delete':
                $this->controller->destroy();
                break;
        }
    }

    /**
     * Router constructor.
     * @param $params
     * @param $controller
     */
    public function __construct(array $params, $controller)
    {
        $this->params = $params;
        $this->controller = $controller;
        $this->runControllerMethod();
    }
}