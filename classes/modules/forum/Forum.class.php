<?php
/*---------------------------------------------------------------------------------------
 *	author: Artemev Yurii
 *	plugin: Forum
 *	author site: http://artemeff.ru/
 *	license: CC BY-SA 3.0, http://creativecommons.org/licenses/by-sa/3.0/
 *--------------------------------------------------------------------------------------*/
 
class PluginForum_ModuleForum extends ModuleORM {
	/**
	 * Инициализация модуля
	 */
	public function Init() {
		parent::Init();
	}
	
	/**
	 *	Генерация URL
	 */
	public function GenerateUrl($sUrl) {
		$sUrl=mb_strtolower($sUrl);

		$aSymbols=array(
			'а' => 'a', 'б' => 'b', 'в' => 'v',
			'г' => 'g', 'д' => 'd', 'е' => 'e',
			'ё' => 'e', 'ж' => 'zh', 'з' => 'z',
			'и' => 'i', 'й' => 'y', 'к' => 'k',
			'л' => 'l', 'м' => 'm', 'н' => 'n',
			'о' => 'o', 'п' => 'p', 'р' => 'r',
			'с' => 's', 'т' => 't', 'у' => 'u',
			'ф' => 'f', 'х' => 'h', 'ц' => 'c',
			'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch',
			'ь' => "'", 'ы' => 'y', 'ъ' => "'",
			'э' => 'e', 'ю' => 'yu', 'я' => 'ya',

			" " => "-", "." => "", "/" => "-",
			"=" => "-"
		);
		
		return false;

		if ($sResIconv=@iconv("UTF-8", "ISO-8859-1//IGNORE//TRANSLIT", $sRes)) {
			$sRes=$sResIconv;
		}

		if (preg_match('/[^A-Za-z0-9_\-]/', $sRes)) {
			$sRes = preg_replace('/[^A-Za-z0-9_\-]/', '', $sRes);
			$sRes = preg_replace('/\-+/', '-', $sRes);
		}
		
		var_dump($sRes); return false;
		
		return $sRes;
	}

}
?>