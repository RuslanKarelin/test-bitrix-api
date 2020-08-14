<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Highloadblock\HighloadBlockTable;

class Controller
{
    protected $iBlockId = null;
    protected $HLBlockId = null;
    protected $request;
    protected $ApiV1;

    const SELECTED_PROPERTIES = [
        "ID", "IBLOCK_ID", "PROPERTY_NAME", "PROPERTY_ADDRESS",
        "PROPERTY_PHONE", "PROPERTY_TIME", "PROPERTY_TYPE"
    ];

    /**
     * @return array
     */
    private function setParams(): array
    {
        return [
            'IBLOCK' => [
                'IBLOCK_ID' => $this->iBlockId,
                'NAME' => $this->request['params']['name'],
                'PROPERTY_VALUES' => [
                    'NAME' => $this->request['params']['name'],
                    'ADDRESS' => $this->request['params']['address'],
                    'PHONE' => $this->request['params']['phone'],
                    'TIME' => $this->request['params']['time'],
                    'TYPE' => ["VALUE" => $this->getEnumIdByValue()],
                ]
            ],
            'HLBLOCK' => [
                'UF_NAME' => $this->request['params']['name'],
                'UF_ADDRESS' => $this->request['params']['address'],
                'UF_PHONE' => $this->request['params']['phone'],
                'UF_TIME' => $this->request['params']['time'],
                'UF_TYPE' => $this->getEnumIdByValue('hlBlock'),
            ]
        ];
    }

    /**
     * @return \Bitrix\Main\ORM\Data\DataManager
     * @throws \Bitrix\Main\SystemException
     */
    private function getHLEntity()
    {
        $hlblock = HighloadBlockTable::getById($this->HLBlockId)->fetch();
        $entity = HighloadBlockTable::compileEntity($hlblock);
        return $entity->getDataClass();
    }

    /**
     * @param $arFields
     * @param $arProps
     * @return array
     */
    private function prepareElement(array $arFields, array $arProps): array
    {
        $arFieldsNew = [];
        $arFieldsNew['params'] = [];
        $arFieldsNew['id'] = $arFields['ID'];
        foreach ($arProps as $key => $value) {
            $newKey = strtolower($key);
            if ($key == 'TYPE') {
                $arFieldsNew['params'][$newKey] = $value['VALUE_ENUM'];
            } else {
                $arFieldsNew['params'][$newKey] = $value['VALUE'];
            }
        }
        return $arFieldsNew;
    }

    /**
     * @return |null
     */
    private function getEnumIdByValue($type = null)
    {
        if ($type) {
            $propertyEnums = CUserFieldEnum::GetList(
                [],
                ["USER_FIELD_NAME" => 'UF_TYPE', "VALUE" => $this->request['params']['type']]
            );
        } else {
            $propertyEnums = CIBlockPropertyEnum::GetList(
                [],
                ["IBLOCK_ID" => $this->iBlockId, "CODE" => "TYPE", "VALUE" => $this->request['params']['type']]
            );
        }

        if ($enumFields = $propertyEnums->GetNext()) {
            return $enumFields["ID"];
        }
        return null;
    }

    /**
     * Controller constructor.
     * @param $request
     * @param $arParams
     * @param $ApiV1
     * @throws \Bitrix\Main\LoaderException
     */
    public function __construct(array $request, array $arParams, &$ApiV1)
    {
        $this->iBlockId = $arParams['IBLOCK_ID'];
        $this->HLBlockId = $arParams['HLBLOCK_ID'];
        $this->request = $request;
        $this->ApiV1 = $ApiV1;
        if (!$this->iBlockId) {
            throw new \InvalidArgumentException();
        }
        Loader::includeModule('iblock');
        Loader::includeModule("highloadblock");
    }

    public function index()
    {
        $filter = ['IBLOCK_ID' => $this->iBlockId];
        if (!empty($this->request['filter'])) {
            $filter['PROPERTY_TYPE_VALUE'] = $this->request['filter']['type'];
        }
        $res = CIBlockElement::GetList(
            [],
            $filter,
            false,
            false,
            static::SELECTED_PROPERTIES
        );
        $offices = [];
        while ($element = $res->GetNextElement()) {
            $arProps = $element->GetProperties();
            $arFields = $element->GetFields();
            $offices['offices'][] = $this->prepareElement($arFields, $arProps);
        }
        $this->ApiV1->result = $offices;
    }

    public function show()
    {
        $res = CIBlockElement::GetList(
            [],
            ['IBLOCK_ID' => $this->iBlockId, 'ID' => $this->request['id']],
            false,
            false,
            static::SELECTED_PROPERTIES
        );
        if ($element = $res->GetNextElement()) {
            $arProps = $element->GetProperties();
            $arFields = $element->GetFields();
            $this->ApiV1->result = $this->prepareElement($arFields, $arProps);
        }
    }

    public function create()
    {
        $element = new CIBlockElement;
        $params = $this->setParams();
        if ($id = $element->Add($params['IBLOCK'])) {
            $this->ApiV1->result = Loc::getMessage('CREATE') . $id;
            $hlEntity = $this->getHLEntity();
            $hlEntity::add($params['HLBLOCK']);
        }
    }

    public function update()
    {
        $element = new CIBlockElement;
        $element->Update($this->request['id'], $this->setParams()['IBLOCK']);
        $this->ApiV1->result = Loc::getMessage('UPDATE') . $this->request['id'];
    }

    public function destroy()
    {
        if (CIBlockElement::Delete($this->request['id'])) {
            $this->ApiV1->result = Loc::getMessage('DELETE') . $this->request['id'];
        }
    }
}