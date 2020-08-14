<?
define("NO_KEEP_STATISTIC", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
$APPLICATION->IncludeComponent(
	"api:v1",
	"",
	array(
        "IBLOCK_ID" => "5",
        "IBLOCK_TYPE" => "office",
        "HLBLOCK_ID" => "1"
    ),
	false
);
?>