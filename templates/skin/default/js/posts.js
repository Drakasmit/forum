var ForumPostClass = new Class({

	typeComment: { forum: { url_add: aRouter.forum+'ajaxaddcomment/' } },

	addComment: function(formObj,targetId) {
		var thisObj=this;
		formObj=$(formObj);
		JsHttpRequest.query(
        	'POST '+thisObj.url_add,
        	{ params: formObj, security_ls_key: LIVESTREET_SECURITY_KEY },
        	function(result, errors) {
            	if (!result) {
                	msgErrorBox.alert('Error','Please try again later');
                	return;
        		}
        		if (result.bStateError) {
                	msgErrorBox.alert(result.sMsgTitle,result.sMsg);
        		} else {
        			msgErrorBox.success('Yahoo!','...');
        		}
	        },
        	true
      	);
      	$('post_text').addClass('loader');
      	$('post_text').setProperty('readonly',true);
	},

	preview: function() {
		ajaxTextPreview('post_text',false,'comment_preview_'+this.iCurrentShowFormComment);
	}
});


var ForumPost;

window.addEvent('domready', function() {
    ForumPost = new ForumPostClass();
});