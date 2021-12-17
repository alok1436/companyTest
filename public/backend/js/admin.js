jQuery( function( $ ){	

	jQuery.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
		}
	});

	//data table list and filter search start
  	var table = $('#datatablebac').DataTable({
        processing: true,
        serverSide: true,
        //searching: true,
        ajax: ajaxurl+'/admin/productsFilterByKeyword/',
        columns: [
            {data: 'radioBtn', render: getRadio },
            {data: 'p_image', render: getImg },
            {data: 'name', name: 'name'},
            {data: 'vendor', name: 'vendor'},
            {data: 'product_type', name: 'Product Type'},
            {data: 'quantity', name: 'Quantity'},
            {data: 'regular_price', name: 'Regular Price'},
            {data: 'sale_price', name: 'Sale Price'},
            {data: 'date', name: 'Date'},
            /*{data: 'email', name: 'email'},
            {data: 'username', name: 'username'},
            {data: 'phone', name: 'phone'},
            {data: 'dob', name: 'dob'},
            {
                data: 'action', 
                name: 'action', 
                orderable: true, 
                searchable: true
            },*/
        ]
    });
    function getRadio(data, type, full, meta){
       return '<input type="radio" value="' + data + '" name="product_id" class="form-control product_id">';
    }
  	function getImg(data, type, full, meta) {
        return '<img src="'+data+'" width="35" />';
    }
    //data table list and filter search end


    $("body").on("submit","#add_to_cart_form",function(){
		$.ajax({
			beforeSend:function(){
			    $("#add_to_cart_form").find('button').attr('disabled',true);
			    $("#add_to_cart_form").find('button>i').show(); 
		  	},
		    url: ajaxurl+'/admin/addToCart',
		    type:'POST',
		    data:$("#add_to_cart_form").serialize(),
		    success:function(response){
		      	if (response.success) {
		 		 	toastr.success(response.message,'Success');
		 		 	$(".modal").modal('hide');
		 		 	location.reload();
			  	}
		  	},   
		  	complete: function(){
			    $("#add_to_cart_form").find('button').attr('disabled',false);
			    $("#add_to_cart_form").find('button>i').hide(); 
		  	}, 
		  	error:function(status,error){
				var errors = JSON.parse(status.responseText);
				var msg_error = '';
				if(status.status == 400){
                    $("#add_to_cart_form").find('button>i').hide();  
					$.each(errors, function(i,v){	
						msg_error += v[0]+'!</br>';
					});
					toastr.error( msg_error,'Opps!'); 
				}else{
					toastr.error(errors.message,'Opps!');
				} 				
          	}	  
		}); 
		return false;
	});


	$("body").on("change",".getSkuValues",function(){
		$.ajax({
			beforeSend:function(){
			    $("#add_to_cart_form").find('button').attr('disabled',true);
			    $("#add_to_cart_form").find('button>i').show(); 
		  	},
			url: ajaxurl+'/admin/getSkuValues',
		    type:'POST',
		    data: $("#add_to_cart_form").serialize(),
		    success:function(response){
		      	if (response.success) {
			 		$('input[name=variation_id]').val(response.data);
		      	}
		  	},   
		  	complete: function(){
		  		$("#add_to_cart_form").find('button').attr('disabled',false);
			    $("#add_to_cart_form").find('button>i').hide(); 
		  	},
		}); 
		return false;
	});


	$("body").on("click",".product_id",function(){
		var id = $(this).val();
		$.ajax({
			beforeSend:function(){
			    $('.ajax-loader').show();
		  	},
		    url: ajaxurl+'/admin/getOneProductDetail',
		    type:'POST',
		    data:{
		    	product_id:id,
		    	customer_id:$("#customer_id").val(),
		    },
	    	type: 'get',
		    success:function(response){
		      	if (response.success) {
			      	$('#product_detail').html(response.html);
			      	$('#addToCartModalRightSide').modal('show');
		      	}
		  	},   
		  	complete: function(){
			    $('.ajax-loader').hide();
		  	},   
		}); 
		return false;
	});


	$("#select_all").click(function () {
	    $(".sub_checkboxes").prop('checked', $(this).prop('checked'));
	});

	$('.sub_checkboxes').click(function(){
		if($(".sub_checkboxes").length == $(".sub_checkboxes:checked").length) { 
		    $("#select_all").prop("checked", true);
		}else {
		    $("#select_all").prop("checked", false);            
		}
	});

	$('.dropify').dropify({
            messages: {
                'default': 'Drag and drop a file here',
                'replace': 'Drag and drop or click to replace',
                'remove': 'Remove',
                'error': 'Ooops, something wrong happended.'
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
			  		$("#form").find('button').attr('disabled',true);
					$("#form").find('button>i').show(); 
			  	},
			  	url: $("#form").attr('action'),
			  	data: formData,
			  	type: 'POST',
			  	processData: false,
    			contentType: false,
			  	success:function(response){
				  	if(response.success){
				        toastr.success(response.message,'Success');
				        console.log(response.reload);
				        if (response.reload !='') {
				        	location.reload();
				        }else if (response.redirect_url !='') {
							setTimeout(function(){
								 location.href = response.redirect_url;
							},1000);
						}
				  	}else{
					  
				  	}
			  	},
			  	complete:function(){
			  		$("#form").find('button').attr('disabled',false);
					$("#form").find('button>i').hide(); 
			  	},
              	error:function(status,error){
					var errors = JSON.parse(status.responseText);
					var msg_error = '';
					if(status.status == 401){
	                    $("#form").find('button').attr('disabled',false);
	                    $("#form").find('button>i').hide();  
						$.each(errors.error, function(i,v){	
							msg_error += v[0]+'!</br>';
						});
						toastr.error( msg_error,'Opps!'); 
					}else{
						toastr.error(errors.message,'Opps!');
					} 				
              	}		  
			});	
			return false;
		}
	});
	$("#form2").validate({
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

			var formData = new FormData($("#form2")[0]);
			$.ajax({
			  	beforeSend:function(){
				  	$("#form2").find('button>i').show();  
				  	// $("#form2").find('button').attr('disabled',true);   
				  	//$('.ajax-loader').show();
			  	},
			  	url: $("#form2").attr('action'),
			  	data: formData,
			  	type: 'POST',
			  	processData: false,
    			contentType: false,
			  	success:function(response){
				  	$('.modal').modal('hide');
			  	},
			  	
              	error:function(status,error){
					var errors = JSON.parse(status.responseText);
					$.each(errors.error, function(i,v){	
						toastr.error( v[0],'Opps!');
					});  				
              	}		  
			});	
			return false;
		}
	});
	$("#add_variant_model").validate({
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

			var formData = new FormData($("#add_variant_model")[0]);
			$.ajax({
				beforeSend:function(){
				  	$("#add_variant_model").find('button>i').show();  
				  	// $("#form2").find('button').attr('disabled',true);   
				  	//$('.ajax-loader').show();
			  	},
			  	
			  	url: $("#add_variant_model").attr('action'),
			  	data: formData,
			  	type: 'POST',
			  	processData: false,
    			contentType: false,
			  	success:function(response){
				  	$('.modal').modal('hide');
				  var newStateVal = response.data.name;
			      var id = response.data.variant_id;
			      if ($(".variantClass"+id).find("option[value=" + newStateVal + "]").length) {
			        $(".variantClass"+id).val(newStateVal).trigger("change");
			      } else { 
			        var newState = new Option(newStateVal, newStateVal, true, true);
			        $(".variantClass"+id).append(newState).trigger('change');
			      } 

			  	},
			  	
              	error:function(status,error){
					var errors = JSON.parse(status.responseText);
					console.log(errors);
                    
					$.each(errors.error, function(i,v){	
						toastr.error( v[0],'Opps!');
					});  				
              	}		  
			});	
			return false;
		}
	});
	$("#add_tag_model").validate({
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

			var formData = new FormData($("#add_tag_model")[0]);
			$.ajax({
			  	
			  	url: $("#add_tag_model").attr('action'),
			  	data: formData,
			  	type: 'POST',
			  	processData: false,
    			contentType: false,
			  	success:function(response){
				  $('.modal').modal('hide');
			      if ($(".select2_tags").find("option[value=" + response.data.id + "]").length) {
			        $(".select2_tags").val(response.data.id).trigger("change");
			      } else { 
			         var newState = new Option(response.data.name,response.data.id, true, true);
			        $(".select2_tags").append(newState).trigger('change');
			      } 

			  	},
			  	
              	error:function(status,error){
					var errors = JSON.parse(status.responseText);
					console.log(errors);
					$.each(errors.error, function(i,v){	
						toastr.error( v[0],'Opps!');
					});  				
              	}		  
			});	
			return false;
		}
	});
	
	//Add Brand model
	$(".addBrand").on("click",function(){
		$(".exp_heading").html('Add Brand');
		$("#form")[0].reset();
		$("#img").hide();
		$("#hid_id").val(0);
		$("#brandModal").modal('show');
	});
	//Edit Brand Image Model
	$("body").on("click",".editBrand",function(){
		$("#form")[0].reset();
		var r = $(this).data('data');
		//var cate = r.categories[0].name;
		var img =$(this).data('img');
		$("#hid_id").val(r.id);
		$("#name").val(r.name);

		var arr = [];
		$(r.categories).each(function(i,v){
			arr.push(v.id);				 
		    // Create a DOM Option and pre-select by default
		    var newOption = new Option(v.name, v.id, true, true);
		    // Append it to the select
		    $('#getCategoriesBySearch').append(newOption).trigger('change');
		});

		$('#getCategoriesBySearch').val(arr).change();
		$(".exp_heading").html('Edit Brand');
		$("#img").show();
		$("#img").html('<img src="'+img+'" style="width:50px; margin-top: 10px;">')
		$("#brandModal").modal('show');
	});

	//Add Category model
	$(".addCategory").on("click",function(){
		$(".exp_heading").html('Add Category');
		$("#form")[0].reset();
		$("#hid_id").val(0);
		$("#categoryModal").modal('show');
	});
	//Edit Category Model
	$("body").on("click",".editCategory",function(){
		$("#form")[0].reset();
		var r = $(this).data('data');
		$("#hid_id").val(r.id);
		$("#name").val(r.name);
		$(".exp_heading").html('Edit Category');
		$("#categoryModal").modal('show');
	});

	//Add Tag model
	$(".addTag").on("click",function(){
		$(".exp_heading").html('Add Tag');
		$("#form")[0].reset();
		$("#hid_id").val(0);
		$("#tagModal").modal('show');
	});

	$("body").on("click",".fb_send",function(){
		$("#form")[0].reset();
		var r = $(this).data('data');
		$("#fbid").val(r.id);
	});
	//Edit Tag Model
	$("body").on("click",".editTag",function(){
		$("#form")[0].reset();
		var r = $(this).data('data');
		$("#hid_id").val(r.id);
		$("#name").val(r.name);

		var arr = [];
		$(r.categories).each(function(i,v){
			arr.push(v.id);	
		});
		$('#tagCategory').val(arr).change();
		$(".exp_heading").html('Edit Tag');
		$("#tagModal").modal('show');
	});

	//Add Variant model
	$(".addVariant").on("click",function(){
		$(".exp_heading").html('Add New Variant');
		$("#form")[0].reset();
		$("#hid_id").val(0);
		$("#variantModal").modal('show');
	});
	//Edit Variant Model
	$("body").on("click",".editVariant",function(){
		$("#form")[0].reset();
		var r = $(this).data('data');
		$("#hid_id").val(r.id);
		$("#name").val(r.name);
		$(".exp_heading").html('Edit Variant');
		$("#variantModal").modal('show');
	});

	//Add Variant Option model
	$(".addVariantOption").on("click",function(){
		$(".exp_heading").html('Add New Variant Option');
		$("#form")[0].reset();
		$("#hid_id").val(0);
		$("#variantOptionModal").modal('show');
	});
	//Edit Variant Option Model
	$("body").on("click",".editVariantOption",function(){
		$("#form")[0].reset();
		var r = $(this).data('data');
		$("#hid_id").val(r.id);
		$("#name").val(r.name);
		$(".exp_heading").html('Edit Variant Option');
		$("#variantOptionModal").modal('show');
	});

	$("body").on("click",".delivery_chage_status",function(){
	 
		var r = $(this).data('data');
		$("#order_id").val(r.id);
		$("#status1").val(r.status);
		$("#reason").val(r.meta_data.reason);
	});

	//ckeditor js
	if($("#summary-ckeditor").length > 0){
    	CKEDITOR.replace( 'summary-ckeditor');
	}
	if($("#summary-ckeditor2").length > 0){
    	CKEDITOR.replace( 'summary-ckeditor2');
	}
    //CKEDITOR.replace( 'summary-ckeditor3');

    //Datepicker
    // $( ".datepicker" ).datepicker({
    //     dateFormat: 'dd-mm-yy'
    // });

    //Multi select from dropdown
    $(".select2").select2();

	$( document ).ready(function() {

		//For Admin
		$("body").on("change",".selectCategory, .level_subcategory_2",function(){
			var id = $(this).val();
			var level = $(this).data('level');
			var type = $(this).data('type');
			$.ajax({
			    url: ajaxurl+'/ajax/getSubcategories/'+id,
			    type:'POST',
			    data:{
			    	product_id:$("#product_id").val(),
			    	level:level,
			    	type:type
			    },
			    beforeSend:function(){
			    $('.ajax-loader').show();
			  },
			  complete: function(){
			    $('.ajax-loader').hide();
			  },
			    type: 'get',
			    success:function(response){
			      if (response.success) {

			      	if(level == 1){
			      		jQuery('#level_'+(level+1)).html(response.html);
			      	}else if(level == 2){
			      		jQuery('#level_'+(level+1)).html(response.html);
			      	}

			      	if ($("#product_id").val() >0) {
			      		jQuery('#level_'+(level+1)).trigger('change');
			      	}

			      }
			  },      
			}); 
			return false;
		});

		if($(".selectCategory").length > 0){
			$(".selectCategory").trigger('change');
		}

		$('body').on('click','.delete_attribute_row',function(){
			$(this).parents('.html_penel').remove();
		});

		$('body').on('click','.delete_variation_row',function(){
			$(this).parents('.variation_panel').remove();
		});

		$('#add_button').click(function(){
		  // get the last DIV which ID starts with ^= "attributeId"
		  var $div = $('div[id^="attributeId"]:last');

		  // Read the Number from that DIV's ID (i.e: 3 from "$attributeId")
		  // And increment that number by 1
		  var num = parseInt( $div.prop("id").match(/\d+/g), 10 ) +1;
 		  var $fieldId = $div.data('fieldid');
		  // Clone it and assign the new ID (i.e: from num 4 to ID "$attributeId")
		  var $attributeId = $div.clone().prop('id', 'attributeId'+num ).data('fieldid', $fieldId+1);
		 
		  $attributeId.find('.a_key').html('Key');
		  $attributeId.find('.a_value').html('Value');
		  $attributeId.find('.addMorebtn').html('<a href="javascript:void(0)" class="delete_attribute_row"><i class="fa fa-trash" style="color:red"></i></a>');
		  $attributeId.find('[name ="attribute['+$fieldId+'][key]"]').attr('name','attribute['+($fieldId+1)+'][key]');
		  $attributeId.find('[name ="attribute['+$fieldId+'][value]"]').attr('name','attribute['+($fieldId+1)+'][value]');
		  $attributeId.find('input').val('');
		  // Finally insert $attributeId wherever you want
		  $div.after( $attributeId );

		});

		$(".product_type").on("change",function(){
			if($(this).val() == 'variable'){
				$(".price_container").hide();
				$(".nav_variation").show();
			}else{
				$(".price_container").show();
				$(".nav_variation").hide();
			}
		});

		$(".status_change1").on("change",function(){
		    var order_id = $(this).children("option:selected").attr('data_id');
		    var order_status = $(this).children("option:selected").val();

			if(confirm("Are you sure to Change Status?")){
				$.ajax({
					url: ajaxurl+'/vendor/changeStatus',
					type: "POST",
					data: { id:order_id, status:order_status },
					success: function(response){
					   if(response.success){
						   toastr.success(response.message,'Success');
						}
					},
	              	error:function(status,error){
						//var errors = JSON.parse(status.responseText);
						console.log(status.responseText);
						//$.each(errors.error, function(i,v){	
							//toastr.error( v[0],'Opps!');
						//});  				
	              	}
				});
			}
		});

		$('body').on('click','.add_more_variation',function(){

			  var $div = $('div[id^="variationId"]:last');
			  // Read the Number from that DIV's ID (i.e: 3 from "$attributeId")
			  // And increment that number by 1
			  var num = parseInt( $div.prop("id").match(/\d+/g), 10 ) +1;
	 		  var $fieldId = $div.data('fieldid');
			  var $variationId = $div.clone().prop('id', 'variationId'+num ).data('fieldid', $fieldId+1);
			  console.log($variationId);
			  var dataVariant = $variationId.find('.variant_value').data('variant');
			  var data = $variationId.find('.raw_').val();
			   
			  var __raw = JSON.parse($variationId.find('.raw_').val());
			 
			  $(__raw).each(function(i,v){
			 	$variationId.find('[name ="variation['+$fieldId+'][variants]['+v+']"]').attr('name','variation['+($fieldId+1)+'][variants]['+v+']');
			  });

			  $variationId.find('[name ="variation['+$fieldId+'][regular_price]"]').attr('name','variation['+($fieldId+1)+'][regular_price]');
			  $variationId.find('[name ="variation['+$fieldId+'][sale_price]"]').attr('name','variation['+($fieldId+1)+'][sale_price]');
			  $variationId.find('[name ="variation['+$fieldId+'][quantity]"]').attr('name','variation['+($fieldId+1)+'][quantity]');
			  $variationId.find('[name ="variation['+$fieldId+'][sku]"]').attr('name','variation['+($fieldId+1)+'][sku]');
			  $variationId.find('.acctin_btn_container').html('<a href="javascript:void(0)" class="delete_variation_row"><i class="fa fa-trash" style="color:red; margin-top: 41px; margin-left: 26px;" ></i></a>');
			  $variationId.find('input').val('');
			  $variationId.find('.raw_').val(data);
			  // Finally insert $attributeId wherever you want
			  $div.after( $variationId );

		});

	$('.addvariants').click(function(){
	     $('#id').val($(this).attr('variant-id'));
	});
});

	//Gallery Image
	$(document).on('click','.del_img', function(){
		
		var thisobj = $(this);
		var isDel = confirm("Are you sure to delete?");
		if(isDel == true){
			$.ajax({
				url: ajaxurl+"/ajax/product/deleteGalleryImage",
				type: "post",
				data: { id:$(this).attr('data-id') },
				success: function(response){
					$(thisobj).closest('.dz-preview').remove(); //Remove field html	
					console.log(response);
				},
              	error:function(status,error){
					//var errors = JSON.parse(status.responseText);
					console.log(status.responseText);
					//$.each(errors.error, function(i,v){	
						//toastr.error( v[0],'Opps!');
					//});  				
              	}
			});
		}
	});

	$(document).on('click','.getVariations', function(){
		if($(".variation_panel").length == 0){
			$.ajax({
				url: ajaxurl+"/ajax/product/generateHtmlPartVariations",
				type: "post",
				data: $("#form").serialize(),
				success: function(response){
					 $("#variation").html(response.html);
				},
	          	error:function(status,error){	
	          	}
			});
		}
	});

	$(document).on('change','#product_title', function(){
		$.ajax({
			url: ajaxurl+"/ajax/product/createSlug",
			type: "post",
			data: { title:$(this).val() },
			success: function(response){ console.log(response);
				$("#product_slug").val(response.slug);
			},
          	error:function(status,error){
				 				
          	}
		});
	});


$("#my-dropzone").dropzone({ 
	url: ajaxurl+"/ajax/product/saveImages",
	success: function(file, response){
        $("#my-dropzone").append($('<input class="hiddenfile" type="hidden" ' + 'name="gallery_images[]" ' + 'value="' + response.image.fileName + '">')); 
    },
    error:function(status,error){
					 
					$.each(error, function(i,v){
					 	$.each(v, function(i1,v1){
						toastr.error( v1,'Opps!');
					});  
				});  	
				var wrapperThis = this;			
    },
    init: function () {
		var wrapperThis = this;
		this.on("addedfile", function (file) { console.log(file); 
			// Create the remove button
			var removeButton = Dropzone.createElement("<div class='btn-ct dz-remove-btn'><button class='del_img'><i class='fa fa-trash text-danger'></i></button></div>");
			// Listen to the click event
			removeButton.addEventListener("click", function (e) {
				// Make sure the button click doesn't submit the form:
				e.preventDefault();
				e.stopPropagation();
				// Remove the file preview.
				wrapperThis.removeFile(file);
				// If you want to the delete the file on the server as well,
				// you can do the AJAX request here.
			});
			// Add the button to the file preview element.
			file.previewElement.appendChild(removeButton);
		});

		this.on('success', function(file, response) { console.log(file);
			console.log(response.image.fileName);
			//$("#files_container").append($('<input class="hiddenfile" type="hidden" ' + 'name="gallery_images[]" ' + 'value="' + response.image.fileName + '">')); 
			// Create a hidden input to submit to the server:
			/*$.each(response, function(k, v) {	
				$("#files_container").append($('<input class="hiddenfile" type="hidden" ' + 'name="gallery_images[]" ' + 'value="' + v.fileName + '">')); 
			});*/
		});

		this.on('error', function(file, errorMessage) {
			wrapperThis.removeFile(file);	   
		});
    }
});
//dropzone

/*Dropzone.options.myDropzone = {
	headers: {
			'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
		},
	url: ajaxurl+"/ajax/product/saveImages",
	autoProcessQueue: true,
	uploadMultiple: true,
	parallelUploads: 10,
	maxFiles: 10,
	acceptedFiles: "image/*",

	init: function () {
		
		var wrapperThis = this;
		
		this.on("addedfile", function (file) { 

			// Create the remove button
			var removeButton = Dropzone.createElement("<button class='del_img'><i class='fa fa-trash text-danger'></i></button>");

			// Listen to the click event
			removeButton.addEventListener("click", function (e) {
				// Make sure the button click doesn't submit the form:
				e.preventDefault();
				e.stopPropagation();
				// Remove the file preview.
				wrapperThis.removeFile(file);
				// If you want to the delete the file on the server as well,
				// you can do the AJAX request here.
			});

			// Add the button to the file preview element.
			file.previewElement.appendChild(removeButton);
		});
		this.on('successmultiple', function(file, response) {
			//console.log(response);
			// Create a hidden input to submit to the server:
			$.each(response, function(k, v) {	
				$("#files_container").append($('<input class="hiddenfile" type="hidden" ' + 'name="gallery_images[]" ' + 'value="' + v.fileName + '">')); 
			});
		});
		this.on('sendingmultiple', function (data, xhr, formData) {
			//formData.append("Username", $("#Username").val());
		});
	}
};*/
});

//Order status change for vendor
$( document ).ready(function() {
	$("body").on("change",".vendor_order_change_status",function(){
		var id = $(this).data('id');
		var status = $(this).val();
		$.ajax({
		    url: ajaxurl+'/vendor/vendorOrderChangeStatus/'+id,
		    type:'POST',
		    data:{ status:status},
		    beforeSend:function(){
		    $('.ajax-loader').show();
		  },
		  complete: function(){
		    $('.ajax-loader').hide();
		  },
		    type: 'get',
		    success:function(response){
		      if (response.success) {
		        toastr.success(response.message,'Success');
		        location.reload();
		      }
		  },      
		}); 
		return false;
	});
});

//Order status change for admin
$( document ).ready(function() {
	$("body").on("change",".admin_order_change_status",function(){
		var id = $(this).data('id');
		var status = $(this).val();
		$.ajax({
		    url: ajaxurl+'/admin/adminOrderChangeStatus/'+id,
		    type:'POST',
		    data:{ status:status},
		    beforeSend:function(){
		    $('.ajax-loader').show();
		  },
		  complete: function(){
		    $('.ajax-loader').hide();
		  },
		    type: 'get',
		    success:function(response){
		      if (response.success) {
		        toastr.success(response.message,'Success');
		        location.reload();
		      }
		  },      
		}); 
		return false;
	});

	$(".getNotification").on("click",function(){
		getNotification();
	});
 	getNotification()
 	function getNotification(){
 		$.ajax({
		    url: ajaxurl+'/getNotification',
		    type:'GET',
		    beforeSend:function(){
		    $('.ajax-loader').show();
		  },
		  complete: function(){
		    $('.ajax-loader').hide();
		  },
		    type: 'get',
		    success:function(response){
		      if (response.success) {
		        $("#notifications").html(response.html)
		      }
		  },      
		}); 		
 	}

	$("body").on("change",".delivery_order_change_status",function(){
		var id = $(this).data('id');
		var status = $(this).val();
		$.ajax({
		    url: ajaxurl+'/delivery/deliveryOrderChangeStatus/'+id,
		    type:'POST',
		    data:{ status:status},
		    beforeSend:function(){
		    $('.ajax-loader').show();
		  },
		  complete: function(){
		    $('.ajax-loader').hide();
		  },
		    type: 'get',
		    success:function(response){
		      if (response.success) {
		        toastr.success(response.message,'Success');
		        location.reload();
		      }
		  },      
		}); 
		return false;
	});

	$("body").on("click",".show_attributes",function(){
		var id = $(this).data('id');
		$.ajax({
		    url: ajaxurl+'/order/get_attributes',
		    type:'POST',
		    data:{ id:id},
		    beforeSend:function(){
		    $('.ajax-loader').show();
		    $('#btn_'+id).html('Loading....');
		  },
		  complete: function(){
		    $('#btn_'+id).html('View attribute');
		  },
		    success:function(response){
		      if (response.success) {
		      	$("#attributes_container").html(response.data);
		      	$("#attributes_container_motal").modal('show');
		        //toastr.success(response.message,'Success');
		      }
		  },      
		}); 
		return false;
	});

	$("body").on("click",".get_options",function(){
		var id = $(this).data('id');
		$.ajax({
		    url: ajaxurl+'/order/get_order_options',
		    type:'POST',
		    data:{ id:id},
		    beforeSend:function(){
		    $('.ajax-loader').show();
		    $('#btn_update_order_'+id).html('Loading....');
		  },
		  complete: function(){
		    $('#btn_update_order_'+id).html('Update order');
		  },
		    success:function(response){
		      if (response.success) {
		      	$("#options_container").html(response.data);
		      	$("#options_container_modal").modal('show');
		        //toastr.success(response.message,'Success');
		      }
		  },      
		}); 
		return false;
	}); 
	$("body").on("change",".discount_type",function(){
		if($(this).val() == 'Fixed'){
			$("#fixed").show();
			$("#percentage").hide();
		}else{
			$("#fixed").hide();
			$("#percentage").show();
		}
	});

	$("body").on("change","#position",function(){
		if($(this).val() == 'Top'){
			$("#offer_ct").show();
			$("#sub_title_ct").hide();
		}else{
			$("#offer_ct").hide();
			$("#sub_title_ct").show();
		}
	});
 
	$("body").on("change","#offer_type",function(){
		if($(this).val() == 'brand'){
			$("#brand_row").show();
			$("#category_row").hide();
			$("#product_row").hide();
		}else if($(this).val() == 'product'){
			$("#brand_row").hide();
			$("#category_row").hide();
			$("#product_row").show();
		}else if($(this).val() == 'category'){
			$("#brand_row").hide();
			$("#category_row").show();
			$("#product_row").hide();
		}
	});

	$("body").on("change",".asignToDeliveryGuys",function(){
		var order_id = $(this).data('id');
		var user_id = $(this).val();
		$.ajax({
		    url: ajaxurl+'/admin/asignToDeliveryGuys/',
		    type:'POST',
		    data:{ 
		    	order_id:order_id,
		    	user_id:user_id
		    },
		    beforeSend:function(){
		    $('.ajax-loader').show();
		  },
		  complete: function(){
		    $('.ajax-loader').hide();
		  },
		    type: 'get',
		    success:function(response){
		      if (response.success) {
		        toastr.success(response.message,'Success');
		        location.reload();
		      }
		  },      
		}); 
		return false;
	});

	$("#getProductsBySearch").select2({
		minimumInputLength: 2,
    	/*multiple:true,*/
	  	ajax: {
	    url: ajaxurl+'/admin/coupon/get_products_by_keywords',
	    dataType: 'json',
        processResults: function (data) {
         	 
            return {
                results: $.map(data, function (item) {
                    return {
                        text: item.name,
                        id: item.id  
                    }
                })
            };
        }	    
	  	}
	});

		$("#getCategoriesBySearch").select2({
		minimumInputLength: 2,
    	multiple:true,
	  	ajax: {
	    url: ajaxurl+'/admin/coupon/get_categories_by_keywords',
	    dataType: 'json',
        processResults: function (data) {
         	 
            return {
                results: $.map(data, function (item) {
                    return {
                        text: item.name,
                        id: item.id  
                    }
                })
            };
        }	    
	  	}
	});


	$("#getBrandsBySearch").select2({
		minimumInputLength: 2,
    	multiple:true,
	  	ajax: {
	    url: ajaxurl+'/admin/coupon/get_brands_by_keywords',
	    dataType: 'json',
        processResults: function (data) {
         	 
            return {
                results: $.map(data, function (item) {
                    return {
                        text: item.name,
                        id: item.id  
                    }
                })
            };
        }	    
	  	}
	});

	$("body").on("change",".admin_affilate_change_status",function(){
			var id = $(this).data('id');
			var status = $(this).val();
			$.ajax({
			    url: ajaxurl+'/admin/adminAffiliateChangeStatus/'+id,
			    type:'POST',
			    data:{ status:status},
			    beforeSend:function(){
			    $('.ajax-loader').show();
			  },
			  complete: function(){
			    $('.ajax-loader').hide();
			  },
			    success:function(response){
			      if (response.success) {
			        toastr.success(response.message,'Success');
			        location.reload();
			      }
			  },      
			}); 
			return false;
		});
	
		$("body").on("change",".admin_affilate_change_status",function(){
			var id = $(this).data('id');
			var status = $(this).val();
			$.ajax({
			    url: ajaxurl+'/admin/adminAffiliateChangeStatus/'+id,
			    type:'POST',
			    data:{ status:status},
			    beforeSend:function(){
			    $('.ajax-loader').show();
			  },
			  complete: function(){
			    $('.ajax-loader').hide();
			  },
			    success:function(response){
			      if (response.success) {
			        toastr.success(response.message,'Success');
			        location.reload();
			      }
			  },      
			}); 
			return false;
		});
		
	$("body").on("change",".admin_role_request_change_status",function(){
			var id = $(this).data('id');
			var status = $(this).val();
			$.ajax({
			    url: ajaxurl+'/admin/admin_role_request_change_status/'+id,
			    type:'POST',
			    data:{ status:status},
			    beforeSend:function(){
			    $('.ajax-loader').show();
			  },
			  complete: function(){
			    $('.ajax-loader').hide();
			  },
			    success:function(response){
			      if (response.success) {
			        toastr.success(response.message,'Success');
			        location.reload();
			      }
			  },      
			}); 
			return false;
		});
	
	$("body").on("change","#bc_level_1",function(){ 
			var category_id = $(this).val();
			var brand_id = $("#brand_id").val() > 0 ? $("#brand_id").val() : 0;
			var module_type = $("#module_type").val();
			$.ajax({
			    url: ajaxurl+'/admin/brand/getSubcategories',
			    type:'POST',
			    data:{ category_id:category_id,brand_id:brand_id,module_type:module_type},
			    beforeSend:function(){
			    $('.ajax-loader').show();
			  },
			  complete: function(){
			    $('.ajax-loader').hide();
			  },
			    success:function(response){
			      if (response.success) {
			      	$("#bc_level_2").html(response.html);
			      	$("#bc_level_2").select2();
			      	$("#bc_level_2").trigger('change');
			        //toastr.success(response.message,'Success');
			        //location.reload();
			      }
			  },      
			}); 
			return false;
	});


	$("body").on("change","#bc_level_2",function(){ 
			var category_id = $(this).val();
			var brand_id = $("#brand_id").val() > 0 ? $("#brand_id").val() : 0;
			var module_type = $("#module_type").val();
			$.ajax({
			    url: ajaxurl+'/admin/brand/getSubcategories',
			    type:'POST',
			    data:{ category_id:category_id,brand_id:brand_id,module_type:module_type},
			    beforeSend:function(){
			    $('.ajax-loader').show();
			  },
			  complete: function(){
			    $('.ajax-loader').hide();
			  },
			    success:function(response){
			      if (response.success) {
			      	$("#bc_level_3").html(response.html);
			      	$("#bc_level_3").select2();
			        //toastr.success(response.message,'Success');
			        //location.reload();
			      }
			  },      
			}); 
			return false;
	});


	if($("#brand_id").val() > 0){
		console.log($("#brand_id").val());
		$("#bc_level_1").trigger('change');
	}

	/******** Delivery guy order start**********/
	$("body").on("change",".select_role",function(){
		if($(this).val() == 'vendor'){
			$(".vendor_wr").show();
		}else{
			$(".vendor_wr").hide();
		}
	});

	$("body").on("click","#submit_filter_delivery_guy",function(){ 
		
			var delivery_guy_id = $('#delivery_guys').val();
			
			$.ajax({
			    url: ajaxurl+'/admin/delivery-guys-analytics/orders',
			    type:'POST',
			    data:{
			    	id:delivery_guy_id,
			    },
			    beforeSend:function(){
			    $('.ajax-loader').show();
			  },
			  complete: function(){
			    $('.ajax-loader').hide();
			  },
			    success:function(response){
			      if (response.success) {
			      	$("#bc_level_2").html(response.html);
			      	$("#bc_level_2").select2();
			      	$("#bc_level_2").trigger('change');
			        //toastr.success(response.message,'Success');
			        //location.reload();
			      }
			  },      
			}); 
			return false;
	});
	/******** Delivery guy order end**********/

	$("body").on("click",".mark_featured",function(){ 
		//console.log($(this).is(':checked'));
		var that = $(this);
		$.ajax({
		    url: ajaxurl+'/ajax/product/mark_featured',
		    type:'POST',
		    data:{
		    	id:$(this).val(),
		    	bool: $(this).is(':checked')
		    },
		    beforeSend:function(){
		    	that.hide();
		  },
		  complete: function(){
		    that.show();
		  },
		  success:function(response){
		      //toastr.success(response.message,'Success');
		  },      
		});
	});
});