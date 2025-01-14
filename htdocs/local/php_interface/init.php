<?php

use \Bitrix\Main\Loader;

AddEventHandler("main", "OnBuildGlobalMenu", "ReviewsMenu");
function ReviewsMenu(&$adminMenu, &$moduleMenu){
      $moduleMenu[] = array(
         "parent_menu" => "global_menu_content", // поместим в раздел "Сервис"
         "section" => "reviews",
         "sort"        => 1000,                    // сортировка пункта меню
         "url"         => "reviews.php",  // ссылка на пункте меню
         "text"        => 'Отзывы',       // текст пункта меню
         "title"       => 'Отзывы', // текст всплывающей подсказки
         "icon"        => "form_menu_icon", // малая иконка
         "page_icon"   => "form_page_icon", // большая иконка
         "items_id"    => "menu_ваше название аля модуля",  // идентификатор ветви
         "items"       => array()          // остальные уровни меню сформируем ниже.

            );
}

Loader::registerNamespace(
   "Pogran",
   Loader::getDocumentRoot()."/local/php_interface/lib/"
);