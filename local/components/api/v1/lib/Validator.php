<?php

use Bitrix\Main\Localization\Loc;

class Validator
{
    const REQUIRED_METHODS = ['get', 'post', 'patch', 'delete'];
    const REQUIRED_PROPERTIES = ['name', 'phone', 'address', 'time', 'type'];
    const METHODS_FOR_REQUIRED_PROPERTIES = ['post', 'patch'];
    const METHOD_LIST_WITH_ID = ['patch', 'delete'];

    protected $token;
    protected $params;
    protected $status = '';

    /**
     * @return $this
     */
    protected function checkToken()
    {
        if (
        	empty($this->status) &&
            (empty($_SERVER['HTTP_TOKEN']) || $_SERVER['HTTP_TOKEN'] !== $this->token)
        ) {
            $this->status = Loc::getMessage('CHECK_TOKEN');
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function checkMethod()
    {
        if (
        	empty($this->status) &&
            (empty($this->params['method']) || !in_array($this->params['method'], static::REQUIRED_METHODS))
        ) {
            $this->status = Loc::getMessage('CHECK_METHOD');
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function checkId()
    {
        if (
        	empty($this->status) &&
            (in_array($this->params['method'], static::METHOD_LIST_WITH_ID) && empty($this->params['id']))
        ) {
            $this->status = Loc::getMessage('CHECK_ID');
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function checkRequiredProperties()
    {
        if (
            empty($this->status) &&
            in_array($this->params['method'], static::METHODS_FOR_REQUIRED_PROPERTIES)
        ) {
            foreach (static::REQUIRED_PROPERTIES as $propertyName) {
                if (
                    !key_exists($propertyName, $this->params['params']) ||
                    empty($this->params['params'][$propertyName])
                ) {
                    $this->status = Loc::getMessage('CHECK_REUIRED_PROPERTIES');
                    break;
                }
            }
        }

        return $this;
    }

    /**
     * Validator constructor.
     * @param $params
     * @param $token
     */
    public function __construct($params, string $token)
    {
        if (empty($params)) die();
        $this->params = $params;
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function validate(): string
    {
        $this->checkToken()->checkMethod()->checkId()->checkRequiredProperties();

        return $this->status;
    }
}