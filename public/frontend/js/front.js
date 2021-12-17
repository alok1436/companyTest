jQuery( function( $ ){	

	jQuery.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
		}
	});
 
 	/*Action : ajax
 	* used to: submit forms
 	* Instance of: Jquery vailidate libaray
	* @JSON 
 	**/
	$("#form").validate({
		errorPlacement: function (error, element) {
			 return;
		},
		highlight: function(element) {
        	$(element).addClass('is-invalid');
        	$(element).parent().addClass("error");
	    },
	    unhighlight: function(element) {
	    	$(element).parent().removeClass("error");
	        $(element).removeClass('is-invalid').addClass('is-valid');
	    },
		submitHandler: function(form){
			for ( instance in CKEDITOR.instances ){
				CKEDITOR.instances[instance].updateElement();
			}

			var formData = new FormData($("#form")[0]);
			$.ajax({
			  	beforeSend:function(){
				  	$("#form").find('button>i').show();  
				  	$("#form").find('button').attr('disabled',true);   
				  	//$('.ajax-loader').show();
			  	},
			  	url: $("#form").attr('action'),
			  	data: formData,
			  	type: 'POST',
			  	processData: false,
    			contentType: false,
			  	success:function(response){
				  	if(response.success){
				        toastr.success(response.message,'Success');
				         if (response.redirect_url !='') {
							setTimeout(function(){
								 location.href = response.redirect_url;
							},1000);
						}
				  	}else{
					  
				  	}
			  	},
			  	complete:function(){
					$("#form").find('button>i').hide();
					$("#form").find('button').attr('disabled',false);  
					$('.ajax-loader').hide();
			  	},
              	error:function(status,error){
					var errors = JSON.parse(status.responseText);
                    $("#form").find('button').attr('disabled',false);
                    $("#form").find('button>i').hide(); 
                    if (status.status !=400) {
						$.each(errors.error, function(i,v){	
							toastr.error( v[0],'Opps!');
						});
                    }else{
                    	toastr.error(errors.message,'Opps!');
                    }   				
              	}		  
			});	
			return false;
		}
	});
});

