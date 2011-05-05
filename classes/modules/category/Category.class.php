<?php
/*---------------------------------------------------------------------------------------
 *	author: Artemev Yurii
 *	livestreet version: 0.4.2
 *	plugin: Forum
 *	version: 0.1a
 *	author site: http://artemeff.ru/
 *	license: GNU GPL v2, http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *--------------------------------------------------------------------------------------*/

class PluginForum_ModuleCategory extends Module {
	/**
	 * Инициализация модуля
	 */
	public function Init() {
		$this->oMapperCategory=Engine::GetMapper(__CLASS__);
	}
	
	public function GetCategories() {
		$data=$this->oMapperCategory->GetCategories();
		
		$data=$this->GetCategoriesByArrayId($data);

		return $data;
	}

	/**
	 * Список форумов по ID
	 *
	 * @param array $aUserId
	 */
	public function GetCategoriesByArrayId($aCategoryId) {
		if (!$aCategoryId) {
			return array();
		}
		if (!is_array($aCategoryId)) {
			$aCategoryId=array($aCategoryId);
		}
		$aCategoryId=array_unique($aCategoryId);
		$aCategories=array();
		$aCategoryIdNotNeedQuery=array();
		/**
		 * Делаем мульти-запрос к кешу
		 */
		$aCacheKeys=func_build_cache_keys($aCategoryId,'Category_');
		if (false !== ($data = $this->Cache_Get($aCacheKeys))) {			
			/**
			 * проверяем что досталось из кеша
			 */
			foreach ($aCacheKeys as $sValue => $sKey ) {
				if (array_key_exists($sKey,$data)) {	
					if ($data[$sKey]) {
						$aCategories[$data[$sKey]->getId()]=$data[$sKey];
					} else {
						$aCategoryIdNotNeedQuery[]=$sValue;
					}
				} 
			}
		}
		/**
		 * Смотрим каких блогов не было в кеше и делаем запрос в БД
		 */		
		$aBCategoryIdNeedQuery=array_diff($aCategoryId,array_keys($aCategories));		
		$aBCategoryIdNeedQuery=array_diff($aBCategoryIdNeedQuery,$aCategoryIdNotNeedQuery);		
		$aCategoryIdNeedStore=$aBCategoryIdNeedQuery;
		if ($data = $this->oMapperCategory->GetCategoriesByArrayId($aBCategoryIdNeedQuery)) {
			foreach ($data as $oCategory) {
				/**
				 * Добавляем к результату и сохраняем в кеш
				 */
				$aCategories[$oCategory->getId()]=$oCategory;
				$this->Cache_Set($oCategory, "Category_{$oCategory->getId()}", array(), 60*60*24*4);
				$aCategoryIdNeedStore=array_diff($aCategoryIdNeedStore,array($oCategory->getId()));
			}
		}
		/**
		 * Сохраняем в кеш запросы не вернувшие результата
		 */
		foreach ($aCategoryIdNeedStore as $sId) {
			$this->Cache_Set(null, "Category_{$sId}", array(), 60*60*24*4);
		}		
		/**
		 * Сортируем результат согласно входящему массиву
		 */
		$aCategories=func_array_sort_by_keys($aCategories,$aCategoryId);
		return $aCategories;		
	}


}
?>