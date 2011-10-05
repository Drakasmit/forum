<?php
/*---------------------------------------------------------------------------
* @Description: Functions for LiveStreet Forum
* @Version: 0.1.0
* @Author: Chiffa
* @LiveStreet Version: 0.5.1
* @File Name: forum.php
* @License: CC BY-SA 3.0, http://creativecommons.org/licenses/by-sa/3.0/
*----------------------------------------------------------------------------
*/

/**
 * Строит дерево форумов
 */
if (!function_exists('create_forum_list')) {
	function create_forum_list($aForums=array(),$aList=array(),$sDepthGuide="") {
		if (is_array($aForums) && !empty($aForums)) {
			foreach ($aForums as $oForum) {
				$aList[] = array(
					'id' => $oForum->getId(),
					'title' => $sDepthGuide . $oForum->getTitle()
				);

				if ($aSubForums = $oForum->getChildren()) {
					$aList = create_forum_list($aSubForums, $aList, $sDepthGuide . PluginForum_ModuleForum::DEPTH_GUIDE);
				}
			}
		}
		return $aList;
	}
}

?>