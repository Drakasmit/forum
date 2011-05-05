var ForumPostClass = new Class({

	typeComment: {
		topic: {
			url_add: aRouter.blog+'ajaxaddcomment/',			
			url_response: DIR_WEB_ROOT+'/include/ajax/commentResponse.php'		
		},
		talk: {
			url_add: aRouter.talk+'ajaxaddcomment/',
			url_response: aRouter.talk+'ajaxresponsecomment/'
		}
	},
	
	initialize: function(options){		
		this.aCommentNew=[];
		this.iCurrentShowFormComment=0;	
		this.iCommentIdLastView=null;	
		this.countNewComment=0;
		this.docScroller = new Fx.Scroll(document.getDocument());	
		this.hideCommentForm(this.iCurrentShowFormComment);
	},
	
	injectComment: function(idCommentParent,idComment,sHtml) {		
		var newComment = new Element('div',{'class':'comment', 'id': 'comment_id_'+idComment});
		newComment.set('html',sHtml);		
		if (idCommentParent) {
			this.expandNodeAll();	
			var divChildren = $('comment-children-'+idCommentParent);		
			var imgParent = divChildren.getParent('div').getChildren('img.folding')[0];		
			this.makeImg(imgParent);
			divChildren.appendChild(newComment);
		} else {
			var divChildren = $('comment-children-0');
			newComment.inject(divChildren,'before');
		}	
	},	
	
	responseNewComment: function(idTarget,typeTarget,objImg,selfIdComment,bNotFlushNew) {
		var thisObj=this;		
		
		if (!bNotFlushNew) {
			var aDivComments=$$('.comment');
			aDivComments.each(function(item,index){
				var divContent=item.getChildren('div.content')[0];
				if (divContent) {
					divContent.removeClass('new');
					divContent.removeClass('view');
				}
			});
		}
		
		var idCommentLast=this.idCommentLast;
		objImg=$(objImg);
		objImg.setProperty('src',DIR_STATIC_SKIN+'/images/update_act.gif');	
		(function(){		
		JsHttpRequest.query(        	
        	'POST '+'http://my.artemeff.ru/forum/ajaxaddcomment/',
        	{ idCommentLast: idCommentLast, idTarget: idTarget, typeTarget: typeTarget, security_ls_key: LIVESTREET_SECURITY_KEY },
        	function(result, errors) {        		
        		objImg.setProperty('src',DIR_STATIC_SKIN+'/images/update.gif'); 
            	if (!result) {
                	msgErrorBox.alert('Error','Please try again later');           
        		}      
        		if (result.bStateError) {
                	msgErrorBox.alert(result.sMsgTitle,result.sMsg);
        		} else {   
        			var aCmt=result.aComments;         			
        			if (aCmt.length>0 && result.iMaxIdComment) {
        				thisObj.setIdCommentLast(result.iMaxIdComment);
        				var countComments=$('count-comments');
        				countComments.set('text',parseInt(countComments.get('text'))+aCmt.length);
        				if ($('block_stream_comment') && lsBlockStream) {
        					lsBlockStream.toggle($('block_stream_comment'),'comment_stream');
        				}
        			}        	
        			var iCountOld=0;
        			if (bNotFlushNew) {		      	       			       			
        				iCountOld=thisObj.countNewComment;        				
        			} else {
        				thisObj.aCommentNew=[];
        			}
        			if (selfIdComment) {
        				thisObj.setCountNewComment(aCmt.length-1+iCountOld);
        				thisObj.hideCommentForm(thisObj.iCurrentShowFormComment); 
        			} else {
        				thisObj.setCountNewComment(aCmt.length+iCountOld);
        			}        			
        			aCmt.each(function(item,index) {   
        				if (!(selfIdComment && selfIdComment==item.id)) {
        					thisObj.aCommentNew.extend([item.id]);
        				}        				 				
        				thisObj.injectComment(item.idParent,item.id,item.html);
        			}); 
        			
        			if (selfIdComment && $('comment_id_'+selfIdComment)) {
						thisObj.scrollToComment(selfIdComment);
					}
        		}                           
	        },
        	true
       );
       }).delay(1000);
	},
	
	setIdCommentLast: function(id) {
		this.idCommentLast=id;
	},
	
	addComment: function(formObj,targetId,targetType) {
		var thisObj=this;
		formObj=$(formObj);			
		JsHttpRequest.query(
        	'POST '+'http://my.artemeff.ru/forum/ajaxaddcomment/',
        	{ params: formObj, security_ls_key: LIVESTREET_SECURITY_KEY },
        	function(result, errors) {
            	if (!result) {
            		thisObj.enableFormComment();
                	msgErrorBox.alert('Error','Please try again later');  
                	return;         
        		}      
        		if (result.bStateError) {        			
					thisObj.enableFormComment();        			
                	msgErrorBox.alert(result.sMsgTitle,result.sMsg);
        		} else {
        			thisObj.responseNewComment(targetId,targetType,$('update-comments'),result.sCommentId,true);        			   								
        		}                           
	        },
        	true
      	);
      	//$('form_comment_text').addClass('loader');		
      	//$('form_comment_text').setProperty('readonly',true);		
	},
	
	toggleComment: function(obj,commentId) {
		var divContent=$('comment_content_id_'+commentId);
		if (!divContent) {
			return false;
		}
		
		var thisObj=this;
		JsHttpRequest.query(
        	'POST '+DIR_WEB_ROOT+'/include/ajax/commentToggle.php',
        	{ idComment: commentId, security_ls_key: LIVESTREET_SECURITY_KEY },
        	function(result, errors) {         		 
            	if (!result) {
                	msgErrorBox.alert('Error','Please try again later');           
        		}      
        		if (result.bStateError) {        			
                	msgErrorBox.alert(result.sMsgTitle,result.sMsg);
        		} else {   
        			msgNoticeBox.alert(result.sMsgTitle,result.sMsg);     			
        			divContent.removeClass('old').removeClass('self').removeClass('new').removeClass('del');
        			obj.removeClass('delete').removeClass('repair');
        			if (result.bState) {
        				divContent.addClass('del');
        				obj.addClass('repair');
        			} else {
        				obj.addClass('delete');
        			}
					obj.set('text',result.sTextToggle);        			        								
        		}                           
	        },
        	true
       );
	},
	
	toggleCommentForm: function(idComment) {
		if (!$('reply_'+this.iCurrentShowFormComment) || !$('reply_'+idComment)) {
			return;
		} 
		divCurrentForm=$('reply_'+this.iCurrentShowFormComment);
		divNextForm=$('reply_'+idComment);
				
		var slideCurrentForm = new Fx.Slide(divCurrentForm);
		var slideNextForm = new Fx.Slide(divNextForm);
		
		$('comment_preview_'+this.iCurrentShowFormComment).set('html','').setStyle('display','none');
		if (this.iCurrentShowFormComment==idComment) {
			slideCurrentForm.toggle();			
			slideCurrentForm.addEvent('complete', function() {
        		$('form_comment_text').focus();
			});
			return;
		}
		
		slideCurrentForm.slideOut();
		divNextForm.set('html',divCurrentForm.get('html'));
		divCurrentForm.set('html','');		
		divNextForm.setStyle('display','block');
		slideNextForm.hide();
		
		slideNextForm.slideIn();
		
		$('form_comment_text').setProperty('value','');
		$('form_comment_reply').setProperty('value',idComment);
		this.iCurrentShowFormComment=idComment;
		slideNextForm.addEvent('complete', function() {
        	$('form_comment_text').focus();
		});
	},
	
	hideCommentForm: function(idComment) {
		if ($('reply_'+idComment)) {
			this.enableFormComment();
			$('comment_preview_'+this.iCurrentShowFormComment).set('html','').setStyle('display','none');
			var slideForm = new Fx.Slide('reply_'+idComment);							
			slideForm.hide();
		}
	},
	
	preview: function() {
		ajaxTextPreview('form_comment_text',false,'comment_preview_'+this.iCurrentShowFormComment);		
	},
	
	goToParentComment: function(obj) {
		var idCmt = obj.href.substr(obj.href.indexOf('#')+8);
		var objCmtParent=$('comment_id_'+idCmt);
		var objCmt=obj.getParent('div.comment');
		objCmtParent.getElement('.goto-comment-child').removeClass('hidden');
		objCmtParent.getElement('.goto-comment-child a').href = '#comment' + objCmt.id.substr(11);
		this.docScroller.setOptions({ 			
			offset: {'y': 0}
 		});
		this.docScroller.toElement(objCmtParent);
		return false;
	},
	
	goToChildComment: function(obj) {
		var idCmt = obj.href.substr(obj.href.indexOf('#')+8);
		var objCmtChild=$('comment_id_'+idCmt);
		var objCmt=obj.getParent('div.comment');
		objCmt.getElement('.goto-comment-child').addClass('hidden');
		this.docScroller.setOptions({ 			
			offset: {'y': 0}
 		});
		this.docScroller.toElement(objCmtChild);
		return false;
	}
});


var ForumPost;
var formCommentSlide;

window.addEvent('domready', function() {  	
    ForumPost = new ForumPostClass({});
});

window.addEvent('keyup', function(e) {
	if(e.control && e.key == 'enter') {
		$('form_comment').getElement('input[name=submit_comment]').click();
		return false;
	}
});
