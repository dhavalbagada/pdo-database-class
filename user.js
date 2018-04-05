jQuery(document).ready(function(){
	
	jQuery(document).on("click","#login",function(e){

		var send_data = {'username':jQuery("#username").val(),
							'password':jQuery("#password").val()};

		$.ajax({
			type:"POST",
			url:"includes/user-manage.php",
			dataType:"json",
			data:{type:'login_user',data:send_data},
			encode:true,
			success:function(response){
				if(response.status == 0){

					jQuery("#error_msg").html(response.message);
					
				}else if(response.status == 1){

					jQuery("#error_msg").html(response.message);
					window.setTimeout(function(){ window.location.href = response.redirectURL ; }, 2000);
				}	
			}
		});
		e.preventDefault();					
	});
	// user register
	jQuery("#registration").submit(function(e){

		var hobbies = [];

		jQuery(".hobbie:checked").each(function(){
			hobbies.push($(this).val());
		});
		console.log(hobbies);
		var formdata = new FormData();
		formdata.append('type',"user_register");
		formdata.append('uname',jQuery("#uname").val());
		formdata.append('pass',jQuery("#pass").val());
		formdata.append('fname',jQuery("#fname").val());
		formdata.append('lname',jQuery("#lname").val());
		formdata.append('hobbie',hobbies);
		formdata.append('image',jQuery("input[name='image']")[0].files[0]);
		
		/*var send_data = {
			'uname':jQuery("#uname").val(),
			'pass':jQuery("#pass").val(),
			'fname':jQuery("#fname").val(),
			'lname':jQuery("#lname").val(),
			'hobbie':hobbies,
			'image':jQuery("#image")[0].files[0]
		};*/
		console.log(jQuery("#image")[0].files[0]);
		$.ajax({
			type:"POST",
			url:"includes/user-manage.php",
			dataType:"json",			
			data:formdata,	
			encode:true,		
			contentType: false,
            cache: false,
			processData:false,
			success:function(response){

				if(response.status == 0){

					jQuery("#error_msg").html(response.message);
					
				}else if(response.status == 1){

					jQuery("#error_msg").html(response.message);
				}
			}
		
		});
		e.preventDefault();
	});
});