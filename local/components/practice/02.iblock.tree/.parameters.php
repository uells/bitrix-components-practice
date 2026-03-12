<?php
// Запрещает выполнение скрипта при прямом вызове файла
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

// Используем пространство имен Loader
use Bitrix\Main\Loader;

// Подключаем модуль инфоблоков
if (!Loader::includeModule('iblock')) {
    return;
}

// Класс свойств инфоблока
class IBlockProps
{
    protected array $props = [];

    public function __construct(array $arCurrentValues, string $paramCodeIBlockId)
    {
        // Флаг для проверки задано ли значение ID корневого инфоблока
        $iblockId = (int)($arCurrentValues[$paramCodeIBlockId] ?? 0);
        if ($iblockId <= 0) {
            return;
        }

        // Запрос к свойствам инфоблока
        $rsProp = CIBlockProperty::GetList(
            [
                "SORT" => "ASC",
                "NAME" => "ASC",
            ],
            [
                "ACTIVE" => "Y",
                "IBLOCK_ID" => $iblockId,
            ]
        );

        // Чтение результата и составление массива свойств 
        while ($arr = $rsProp->Fetch()) {
            $this->props[$arr["CODE"]] = "[" . $arr["CODE"] . "] " . $arr["NAME"];
        }
        
    }

    public function getProps(): array
    {
        return $this->props;
    }
}

// Получаем массив типов инфоблоков
$arTypesEx = CIBlockParameters::GetIBlockTypes();

// инициализация фильтра и массива инфоблоков
$arIBlocks = [];
$iblockFilter = [
    'ACTIVE' => 'Y',
];

// Проверяем задан ли тип инфоблока, добавляем его в фильтр
if (!empty($arCurrentValues['IBLOCK_TYPE'])) {
    $iblockFilter['TYPE'] = $arCurrentValues['IBLOCK_TYPE'];
}
// если в запросе указан site, то добавляем его в фильтр
if (isset($_REQUEST['site'])) {
    $iblockFilter['SITE_ID'] = $_REQUEST['site'];
}

// Запрос на получение инфоблоков по фильтру (тип инфоблока + site)
$db_iblock = CIBlock::GetList(["SORT" => "ASC"], $iblockFilter);
while ($arRes = $db_iblock->Fetch()) {
    $arIBlocks[$arRes["ID"]] = "[" . $arRes["ID"] . "] " . $arRes["NAME"];
}

// Определяем свойства выбранных инфоблоков
$propsRoot = new IBlockProps($arCurrentValues, "ROOT_IBLOCK_ID");
$propsMiddle = new IBlockProps($arCurrentValues, "MIDDLE_IBLOCK_ID");
$propsTop = new IBlockProps($arCurrentValues, "TOP_IBLOCK_ID");


// Формируем массив $arComponentParameters, который описывает входные параметры компонента
$arComponentParameters = [
    "GROUPS" => [],
    "PARAMETERS" => [
        // Тип инфоблоков
        "IBLOCK_TYPE" => [
            "PARENT" => "BASE",
            "NAME" => GetMessage("T_IBLOCK_DESC_LIST_TYPE"),
            "TYPE" => "LIST",
            "VALUES" => $arTypesEx,
            "DEFAULT" => "",
            "REFRESH" => "Y"
        ],

        // id инфоблоков
        "ROOT_IBLOCK_ID" => [
            "PARENT" => "BASE",
            "NAME" => GetMessage("ROOT_IBLOCK_ID_DESC"),
            "TYPE" => "LIST",
            "VALUES" => $arIBlocks,
            "DEFAULT" => '={$_REQUEST["ROOT_ID"]}',
            "REFRESH" => "Y"
        ],
        "MIDDLE_IBLOCK_ID" => [
            "PARENT" => "BASE",
            "NAME" => GetMessage("MIDDLE_IBLOCK_ID_DESC"),
            "TYPE" => "LIST",
            "VALUES" => $arIBlocks,
            "DEFAULT" => '={$_REQUEST["MIDDLE_ID"]}',
            "REFRESH" => "Y"
        ],
        "TOP_IBLOCK_ID" => [
            "PARENT" => "BASE",
            "NAME" => GetMessage("TOP_IBLOCK_ID_DESC"),
            "TYPE" => "LIST",
            "VALUES" => $arIBlocks,
            "DEFAULT" => '={$_REQUEST["TOP_ID"]}',
            "REFRESH" => "Y"
        ],

        // Свойства инфоблоков
        "ROOT_PROPERTY_CODE" => [
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("ROOT_PROPERTY_CODE_DESC"),
            "TYPE" => "LIST",
            "VALUES" => $propsRoot->getProps(),
            "MULTIPLE" => "Y",
            "ADDITIONAL_VALUES" => "Y"
        ],
        "MIDDLE_PROPERTY_CODE" => [
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("MIDDLE_PROPERTY_CODE_DESC"),
            "TYPE" => "LIST",
            "VALUES" => $propsMiddle->getProps(),
            "MULTIPLE" => "Y",
            "ADDITIONAL_VALUES" => "Y"
        ],
        "TOP_PROPERTY_CODE" => [
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("TOP_PROPERTY_CODE_DESC"),
            "TYPE" => "LIST",
            "VALUES" => $propsTop->getProps(),
            "MULTIPLE" => "Y",
            "ADDITIONAL_VALUES" => "Y"
        ],

        // Коды свойств привязок
        "MIDDLE_TO_ROOT_PROPERTY_CODE" => [
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("MIDDLE_TO_ROOT_PROPERRTY_CODE_DESC"),
            "TYPE" => "LIST"
        ],

        "TOP_TO_MIDDLE_PROPERTY_CODE" => [
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("TOP_TO_MIDDLE_PROPERTY_CODE_DESC"),
            "TYPE" => "LIST"
        ]

        // кеш
        "CACHE_TIME" => [
            "DEFAULT" => 360000
        ]
    ]
];
