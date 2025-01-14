<?php

use Bitrix\Main\Loader;

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin.php");

if (!$USER->IsAdmin())
{
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}



$APPLICATION->IncludeComponent(
	'pogran:reviews',
	'',
	array(

	),
);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");