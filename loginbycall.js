jQuery(document).ready(function(){
	
	/* -- Form USER --*/
	window.setTimeout(loginbycallHiddenMessge, 1000);
	function loginbycallHiddenMessge(){
		jQery('.loginbycall-message').hiden();
	}
	if(jQuery('#loginbycall-user-form [name=create]:checked').val()==0){
		jQuery('#loginbycall-user-form div.form-item-login, #loginbycall-user-form div.form-item-pass').hide();
		jQuery('#loginbycall-user-form div.form-item-create-login').show();
		jQuery('#loginbycall-user-form div.form-item-create-email').show();
	}
	if(jQuery('#loginbycall-user-form [name=create]:checked').val()==1){
		jQuery('#loginbycall-user-form div.form-item-login, #loginbycall-user-form div.form-item-pass').show();
		jQuery('#loginbycall-user-form div.form-item-create-login').hide();
		jQuery('#loginbycall-user-form div.form-item-create-email').hide();
	} 
	
	jQuery('#loginbycall-user-form [name=create]').change(function(){
		if(jQuery(this).val()==0){
			jQuery('#loginbycall-user-form div.form-item-login, #loginbycall-user-form div.form-item-pass').hide();
			jQuery('#loginbycall-user-form div.form-item-create-login').show();
			jQuery('#loginbycall-user-form div.form-item-create-email').show();
		} 
		if(jQuery(this).val()==1){
			jQuery('#loginbycall-user-form div.form-item-login, #loginbycall-user-form div.form-item-pass').show(); 
			jQuery('#loginbycall-user-form div.form-item-create-login').hide();
			jQuery('#loginbycall-user-form div.form-item-create-email').hide();
		} 
	});
	jQuery('#loginbycall-edit-submit').click(function(){
		if(jQuery('#edit-create-0').attr('checked')=='checked'){
			if(!jQuery('#edit-create-login').val()){
				jQuery('#edit-create-login').css('border-color','red');
				return false;
			}
			if(!jQuery('#edit-create-email').val() || !makeCheck(jQuery('#edit-create-email').val())){
				jQuery('#edit-create-email').css('border-color','red');
				return false;
			}
		}
		if(jQuery('#edit-create-1').attr('checked')=='checked'){
			if(!jQuery('#edit-login').val()){
				jQuery('#edit-login').css('border-color','red');
				return false;
			}
			if(!jQuery('#edit-pass').val()){
				jQuery('#edit-pass').css('border-color','red');
				return false;
			}	
		}
	});
	
	/* -- Form OFFER -- */
	//jQuery('#loginbycall-form-place').fadeIn(2000);
	
	jQuery('#loginbycall-form-close').click(function(){
		jQuery('#wrapper-loginbycall-form-offer-place').fadeOut(1000);
		return false;
	});
	jQuery('#loginbycall-close').click(function(){
		jQuery('#wrapper-loginbycall-form-offer-place').fadeOut(1000);
		return false;
	});
	jQuery('#loginbycall-form-close').click(function(){
		jQuery('#wrapper-loginbycall-delete-info').fadeOut(1000);
		return false;
	});
	jQuery('#loginbycall-close').click(function(){
		jQuery('#wrapper-loginbycall-delete-info').fadeOut(1000);
		return false;
	});
	
	jQuery('#loginbycall-oauth-unbind').click(function(){
		jQuery.post('/loginbycall-redirect-uri',{
			unbind:jQuery('div.loginbycall-oauth-unbind-value').html()
		});
		jQuery('#wrapper-loginbycall-form-offer-place').fadeOut(1000);
		return false;
	});
	function makeCheck(email)
	{

		var re = /^([a-z0-9_\-]+\.)*[a-z0-9_\-]+@([a-z0-9][a-z0-9\-]*[a-z0-9]\.)+[a-z]{2,6}$/i;
		return re.test(email);
	}
});