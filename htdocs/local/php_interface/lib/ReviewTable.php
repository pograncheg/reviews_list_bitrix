<?php
namespace  Pogran;
use Bitrix\Main\Entity;
class ReviewTable extends Entity\DataManager
{
	public static function getTableName()
	{
		return 'mnd_reviews';
	}
	
	public static function getMap()
	{
		return array(
			new Entity\IntegerField('ID', [
                'primary' => true,
                'autocomplete' => true,
            ]),
			new Entity\DateField('CREATED_AT', [
                'default_value' => new \Bitrix\Main\Type\DateTime(),
            ]),
			new Entity\DateField('UPDATED_AT'),
			new Entity\IntegerField('RATING'),
			new Entity\StringField('TEXT', [
                'required' => true,
            ]
		),
			new Entity\BooleanField('VALID', [
                'default_value' => 'N',
            ]),
			new Entity\StringField('ENTITY_TYPE'),
			new Entity\IntegerField('ENTITY'),
		);
	}
}