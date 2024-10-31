		jQuery(document).ready(function() {
		    // bind form using ajaxForm and the form ID
		     jQuery('#ps-form').ajaxForm( { 
		     	beforeSubmit: validate, 
		     	success: function($response) {
		     		 if($response['status'] = 'success') {
		     		 	jQuery('#ps-form').clearForm();
		     		 	jQuery('#success').show();
		     		 } 
		     	} 
		     });
		});
		
		function validate() {
			isValide = true;
			var exts = ['zip'];
			var uploadfile = jQuery('input[name=upload_file]').fieldValue();
		    var posttype = jQuery('select[name=post_type]').fieldValue();
		    var cat = jQuery('select[name=cat]').fieldValue();
		    var catname = jQuery('input[name=catname]').fieldValue();
		    var date = jQuery('input[name=date]').fieldValue();
		    var minutes = jQuery('select[name=minutes]').fieldValue();
		    var hours = jQuery('select[name=hours]').fieldValue();
		    var day = jQuery('input[name=day]').fieldValue();
		    // usernameValue and passwordValue are arrays but we can do simple
		    // "not" tests to see if the arrays are empty
		    if (!uploadfile[0]) {
		    	jQuery('#err-upload-file').html('Please upload the file');
		        isValide = false;
		    }else { 
		    	var get_ext = uploadfile[0].split('.');
		        // reverse name to check extension
		        get_ext = get_ext.reverse();
		        // check file type is valid as given in 'exts' array
		        if ( jQuery.inArray ( get_ext[0].toLowerCase(), exts ) > -1 ){
		          jQuery('#err-upload-file').html('');
		        } else {
		          jQuery('#err-upload-file').html('Please upload extention .zip file');
		        }
		    }
		    
		    if (!posttype[0]) {
		    	jQuery('#err-posttype').html('Please select your posttype');
		        isValide = false;
		    }else { 
		    	jQuery('#err-posttype').html(''); 
		    }
		    
		    if (cat[0] == "-1") {
		    	if (!catname[0]){
		    		jQuery('#err-category').html('Please select category or add your custom category');
		        	isValid = false;
		    	}else { jQuery('#err-category').html(''); isValid = true; }    	
		    }else { jQuery('#err-category').html(''); }
		    
		    if (!date[0]) {
		    	jQuery('#err-datetime').html('Please select schedule start date and time.');
		        isValide = false;
		    }else { jQuery('#err-datetime').html(''); }
		    
		    if (!minutes[0]) {
		    	jQuery('#err-freq-min').html('Select your frequency range in minuts');
		        isValide = false;
		    }else { jQuery('#err-freq-min').html(''); }
		    
		    if (!hours[0]) {
		    	jQuery('#err-freq-hours').html('Select your frequency range in hours');
		        isValide = false;
		    }else { jQuery('#err-freq-hours').html(''); }
		    
		    if (!day[0]) {
		    	jQuery('#err-freq-day').html('Select your frequency range in day');
		        isValide = false;
		    }else { jQuery('#err-freq-day').html(''); }
		    
		    
		    return isValide;
		    
		}