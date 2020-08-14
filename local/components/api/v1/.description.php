<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arComponentDescription = array(
    "NAME" => GetMessage("API_CMPT_NAME"),
    "DESCRIPTION" => GetMessage("API_CMPT_DESC"),
    "ICON" => "/images/icon.gif",
    "SORT" => 10,
    "CACHE_PATH" => "Y",
    "PATH" => array(
        "ID" => "api",
    ),
    "COMPLEX" => "N",
);

?>