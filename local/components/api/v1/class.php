<?php

use Bitrix\Main\Context;

class ApiV1 extends CBitrixComponent
{
    protected $params;
    protected $settings;
    protected $status;
    public $result = '';

    private function requireLibrary()
    {
        spl_autoload_register(function ($class) {
            if (file_exists(dirname(__FILE__) . '/lib/' . $class . '.php')) {
                require_once 'lib/' . $class . '.php';
            }
        });
    }

    protected function getParams()
    {
        $this->params = json_decode(file_get_contents("php://input"), true);
    }

    protected function getSettings()
    {
        $this->settings = include('settings.php');
    }

    /**
     * @throws \Bitrix\Main\LoaderException
     */
    protected function general()
    {
        $validator = new Validator($this->params, $this->settings['token']);
        $this->status = $validator->validate();
        if (empty($this->status)) {
            new Router($this->params, new Controller($this->params, $this->arParams, $this));
        }
    }

    protected function response()
    {
        echo json_encode(['status' => $this->status, 'result' => $this->result]);
    }

    /**
     * @return mixed|void
     */
    public function executeComponent()
    {
        $request = Context::getCurrent()->getRequest();
        if ($request->isPost()) {
            $this->requireLibrary();
            $this->getParams();
            $this->getSettings();
            $this->general();
            $this->response();
        }
    }
}

