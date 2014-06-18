<?php

class CommonProvider {
	public static $ID_AUTH_ERROR = "AUTH_ERROR";
	public static $ID_AUTH_DEMO_CONFIRM = "AUTH_DEMO_CONFIRM";
	public static $ID_ERROR = "ERROR";
	public static $ID_CHANGE_AUTH = "CHANGE_AUTH";
	
	public static function getObject($objectId, $params=null) {
		if (self::$ID_AUTH_ERROR == $objectId) {
			$obj = new MediaObject("0", self::$ID_AUTH_ERROR);
			$obj->setParam("message", 'Ошибка входа ('.self::getParam($params, "message").'). Пожалуйста проверьте правильность абонемента и пароля.');
		} else if (self::$ID_AUTH_DEMO_CONFIRM == $objectId) {
			$obj = new MediaObject("0", self::$ID_AUTH_DEMO_CONFIRM);
			$obj->setParam("message", "Вы пользуетесь пробным доступом. Введите свои данные или подтвердите пробный доступ кнопкой \"Вход\".");
		} else if(self::$ID_ERROR == $objectId ) {
			$obj = new MediaObject("0", self::$ID_ERROR, "Error");
			$obj->setParam("descr", self::getParam($params, "message"));
		} else if(self::$ID_CHANGE_AUTH == $objectId ) {
			$obj = new MediaObject("0", self::$ID_CHANGE_AUTH);
			$obj->setParam("message", "Введите свои данные и подтвердите кнопкой \"Вход\".");
		} else {
            $obj = new MediaObject("0", self::$OBJ_ERROR, "Error");
            $obj->setParam("message", "Unknown error");
		}
        return $obj;    
	}
	
    public static function getParam($params, $name, $default = null) {
        return isset($params[$name]) ? $params[$name] : $default;
    }
}
?>