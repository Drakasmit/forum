var ForumPostClass = new Class({

	make: function(){
		var thisObj = this;
	},

	addComment: function(formObj,targetId) {
		var thisObj=this;
		formObj=$(formObj);
		JsHttpRequest.query(
        	'POST '+aRouter.forum+'ajaxaddpost/',
        	{ params: formObj, security_ls_key: LIVESTREET_SECURITY_KEY },
        	function(result, errors) {
            	if (!result) {
                	msgErrorBox.alert('Error','Please try again later');
					msgErrorBox.alert('/'+result+'/',result);
                	return;
        		}
        		if (result.bStateError) {
                	msgErrorBox.alert(result.sMsgTitle,result.sMsg);
        		} else {
        			//msgErrorBox.alert('Yahoo!','...');
					thisObj.responseNewComment(targetId);
        		}
	        },
        	true
      	);
      	$('form_post_text').addClass('loader');
      	$('form_post_text').setProperty('readonly',true);
	},

	responseNewComment: function(idTarget) {
		var thisObj=this;	

		var idPostLast=this.idPostLast;
		var idForum=this.idForum;
		(function(){		
		JsHttpRequest.query(        	
        	'POST '+aRouter.forum+'ajaxresponsepost/',
        	{ idPostLast: idPostLast, idTargetTopic: idTarget, idTargetForum: idForum, security_ls_key: LIVESTREET_SECURITY_KEY },
        	function(result, errors) {
            	if (!result) {
                	msgErrorBox.alert('Error','Please try again later');           
        		}      
        		if (result.bStateError) {
                	msgErrorBox.alert(result.sMsgTitle,result.sMsg);
        		} else {   
        			var aPst=result.aPosts;         			
        			if (aPst.length>0 && result.iMaxIdPost) {
        				//thisObj.setidPostLast(result.iMaxIdPost);
        				//var countComments=$('count-comments');
        				//countComments.set('text',parseInt(countComments.get('text'))+aPst.length);
        			}
        			/*if (bNotFlushNew) {		      	       			       			
        				iCountOld=thisObj.countNewComment;        				
        			} else {
        				thisObj.aCommentNew=[];
        			}*/
        			/*if (selfIdComment) {
        				thisObj.setCountNewComment(aPst.length-1+iCountOld);
        				thisObj.hideCommentForm(thisObj.iCurrentShowFormComment); 
        			} else {
        				thisObj.setCountNewComment(aPst.length+iCountOld);
        			}*/
        			aPst.each(function(item,index) {
        				thisObj.injectPost(item.id,item.html);
        			});
        		}                           
	        },
        	true
       );
       }).delay(1000);
	},
	
	injectPost: function(idPost,sHtml) {		
		var newPost = new Element('div',{'class':'sv-post-new'});
		newPost.set('html',sHtml);		
		var divChildren = $('post_id_new');
		newPost.inject(divChildren,'before');
	},	

	setIdPostLast: function(id) {
		this.idPostLast=id;
	},
	
	setIdForum: function(id) {
		this.idForum=id;
	},

	preview: function() {
		ajaxTextPreview('form_post_text',false,'post_preview');
	}
});


var ForumPost;

window.addEvent('domready', function() {
    ForumPost = new ForumPostClass();
});