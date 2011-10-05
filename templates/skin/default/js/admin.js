/*---------------------------------------------------------------------------
* @Description: JS code for Forum
* @Version: 0.1
* @Author: Chiffa
* @LiveStreet Version: 0.5.1
* @File Name: admin.js
* @License: GNU GPL v2, http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*----------------------------------------------------------------------------
*/

var ls = ls || {};

ls.forum = ls.forum || {};

ls.forum.admin = {

	deleteForum:function(idForum,sTitle){
		if (!confirm(ls.lang.get('forum_delete_confirm',{'title':sTitle}) + '?')) return false;
 
		var tree=$('forums-tree');
		if (!tree) return;

		ls.ajax(aRouter['forum']+'ajax/deleteforum/',{'idForum':idForum},function(data){
			if (data.bStateError) {
				ls.msg.error(data.sMsgTitle,data.sMsg);
			} else {
				$('#forum-'+idForum).remove();
				ls.msg.notice(data.sMsgTitle,data.sMsg);
			}
		});

		return false;
	}

}