function fAddComment(formObj,targetId) {
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
			fResponseNewComment(targetId, result.idPostLast, result.idForum);
			ls.msg.notice(null,result.sMsg);
			$('#form_post_text').removeClass('loader').attr('readonly',false).val('');
		}
	}.bind(this));
}

function fResponseNewComment(idTarget, idPostLast, idForum ) {

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
				fInjectPost(item.id,item.html);
			});
		}
	}.bind(this));
}

function fInjectPost(idPost,sHtml) {
	var newPost=$('<div>', {'class': 'sv-post-new', id: 'post_id_'+idPost}).html(sHtml);
	$('#post_id_new').append(newPost);
}

function fSetIdPostLast(id) {
	this.idPostLast=id;
}

function fSetIdForum(id) {
	this.idForum=id;
}

function fPreview() {
	if (tinyMCE) {
		$("#form_post_text").val(tinyMCE.activeEditor.getContent());
	}
	if ($("#form_post_text").val() == '') return;
	$("#post_preview").css('display', 'block');
	ls.tools.textPreview('form_post_text', false, 'post_preview');
}