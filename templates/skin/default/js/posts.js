Function.prototype.bind = function(context) {
	var fn = this;
	return function() {
		return fn.apply(context, arguments);
	};
};


var forum = forum || {};

forum.post = (function($) {

	this.addComment = function(formObj,targetId) {
		if (tinyMCE) {
			$('#'+formObj+' textarea').val(tinyMCE.activeEditor.getContent());
		}
		formObj=$('#'+formObj);

		ls.ajax(aRouter['forum']+'ajaxaddpost/', formObj.serializeJSON(), function(result){
			$('#form_post_text').addClass('loader').attr('readonly',true);
			if (!result) {
				ls.msg.error('Error','Please try again later');
			}
			if (result.bStateError) {
				ls.msg.error(null,result.sMsg);
			} else {
				forum.post.responseNewComment(targetId, result.idPostLast, result.idForum);
				ls.msg.notice(null,result.sMsg);
				$('#form_post_text').removeClass('loader').attr('readonly',false).val('');
			}
		}.bind(this));
	};

	this.responseNewComment = function(idTarget, idPostLast, idForum ) {

		var params = {idPostLast: idPostLast, idTargetForum: idForum, idTargetTopic: idTarget};
		
		ls.ajax(aRouter['forum']+'ajaxresponsepost/', params, function(result){
			if (!result) {
				ls.msg.error('Error','Please try again later');
			}
			if (result.bStateError) {
				ls.msg.error(null,result.sMsg);
			} else {
				var aPst=result.aPosts;
				$.each(aPst, function(index, item) {
					forum.post.injectPost(item.id,item.html);
				});
			}
		}.bind(this));
	};

	this.injectPost = function(idPost,sHtml) {
		var newPost=$('<div>', {'class': 'sv-post-new', id: 'post_id_'+idPost}).html(sHtml);
		$('#post_id_new').append(newPost);
	};

	this.setIdPostLast = function(id) {
		this.idPostLast=id;
	};

	this.setIdForum = function(id) {
		this.idForum=id;
	};

	this.preview = function() {
		if (tinyMCE) {
			$("#form_post_text").val(tinyMCE.activeEditor.getContent());
		}
		if ($("#form_post_text").val() == '') return;
		$("#post_preview").css('display', 'block');
		ls.tools.textPreview('form_post_text', false, 'post_preview');
	};
	
	return this;

}).call(forum.post || {},jQuery);