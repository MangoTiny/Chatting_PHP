$(document).ready(function(){

	fetch_user();

	setInterval(function(){
		update_last_activity();
		fetch_user();
		update_chat_history_data();
		fetch_group_chat_history();
	}, 1000);

	function fetch_user()
	{
		$.ajax({
			url:"fetch_user.php",
			method:"POST",
			success:function(data){
				$('#user_details').html(data);
			}
		})
	}

	function update_last_activity()
	{
		$.ajax({
			url:"update_last_activity.php",
			success:function()
			{

			}
		})
	}

	function make_chat_dialog_box(to_user_id, to_user_name)
	{
        var modal_content1 = '<div class="card" id="user_dialog_' + to_user_id + '"><div class="card-header" id="kt_chat_messenger_header"><div class="card-title">';
        modal_content1 +='<div class="d-flex justify-content-center flex-column me-3">';
        modal_content1 += '<a href="#" class="fs-4 fw-bold text-gray-900 text-hover-primary me-1 mb-2 lh-1">' + to_user_name +'</a>';
        modal_content1 += '<div class="mb-0 lh-1">';
        modal_content1 += '<span class="badge badge-success badge-circle w-10px h-10px me-1"></span>';
        modal_content1 += '<span class="fs-7 fw-semibold text-muted">Active</span>';
        modal_content1 += '</div>';
        modal_content1 += '</div>';
        modal_content1 += '</div>';
        modal_content1 +='</div>';
        modal_content1 +='<div class="card-body chat_history" id="chat_history_' + to_user_id +'" data-touserid="'+ to_user_id + '" >';
        modal_content1 += fetch_user_chat_history(to_user_id);
        modal_content1 += '</div>';
        modal_content1 += '</div>';
        modal_content1 +='</div>';
        modal_content1 +='<div class="card-footer pt-4" id="kt_chat_messenger_footer">';
        modal_content1 += '<textarea id="chat_message_' + to_user_id + '" class="form-control form-control-flush mb-3" rows="1" data-kt-element="input" placeholder="Type a message" name="chat_message_'+ to_user_id +'"></textarea>';
        modal_content1 += '<div class="d-flex flex-stack">';
        modal_content1 += '<div class="d-flex align-items-center me-2">';
        modal_content1 += '<button class="btn btn-sm btn-icon btn-active-light-primary me-1" type="button" data-bs-toggle="tooltip" title="Coming soon">';
        modal_content1 += '<i class="ki-duotone ki-paper-clip fs-3"></i>';
        modal_content1 += '</button>';
        modal_content1 += '<button class="btn btn-sm btn-icon btn-active-light-primary me-1" type="button" data-bs-toggle="tooltip" title="Coming soon">';
        modal_content1 += '<i class="ki-duotone ki-exit-up fs-3">';
        modal_content1 += '<span class="path1"></span>';
        modal_content1 += '<span class="path2"></span>';
        modal_content1 += '</i>';
        modal_content1 += '</button>';
        modal_content1 += '</div>';
        modal_content1 += '<button class="btn btn-primary send_chat" type="button" data-kt-element="send" name="send_chat" id="'+ to_user_id +'" >Send</button>';
        modal_content1 += '</div>';
        modal_content1 +='</div>';
        modal_content1 +='</div>';

		// var modal_content = '<div id="user_dialog_'+to_user_id+'" class="user_dialog" title="You have chat with '+to_user_name+'">';
		// modal_content += '<div style="height:400px; border:1px solid #ccc; overflow-y: scroll; margin-bottom:24px; padding:16px;" class="chat_history" data-touserid="'+to_user_id+'" id="chat_history_'+to_user_id+'">';
		// modal_content += fetch_user_chat_history(to_user_id);
		// modal_content += '</div>';
		// modal_content += '<div class="form-group">';
		// modal_content += '<textarea name="chat_message_'+to_user_id+'" id="chat_message_'+to_user_id+'" class="form-control chat_message"></textarea>';
		// modal_content += '</div><div class="form-group" align="right">';
		// modal_content+= '<button type="button" name="send_chat" id="'+to_user_id+'" class="btn btn-info send_chat">Send</button></div></div>';
		$('#user_model_details').html(modal_content1);
	}

	$(document).on('click', '.start_chat', function(){

		var to_user_id = $(this).data('touserid');
		var to_user_name = $(this).data('tousername');
		make_chat_dialog_box(to_user_id, to_user_name);
		// $("#user_dialog_"+to_user_id).dialog({
		// 	autoOpen:false,
		// 	width:400
		// });
		// $('#user_dialog_'+to_user_id).dialog('open');
		// $('#chat_message_'+to_user_id).emojioneArea({
		// 	pickerPosition:"top",
		// 	toneStyle: "bullet"
		// });
	});

	$(document).on('click', '.send_chat', function(){
		var to_user_id = $(this).attr('id');
		var chat_message = $.trim($('#chat_message_'+to_user_id).val());
		if(chat_message != '')
		{
			$.ajax({
				url:"insert_chat.php",
				method:"POST",
				data:{to_user_id:to_user_id, chat_message:chat_message},
				success:function(data)
				{
					$('#chat_message_'+to_user_id).val('');
					// var element = $('#chat_message_'+to_user_id).emojioneArea();
					// element[0].emojioneArea.setText('');
					$('#chat_history_'+to_user_id).html(data);
				}
			})
		}
		else
		{
			alert('Type something');
		}
	});

	function fetch_user_chat_history(to_user_id)
	{
		$.ajax({
			url:"fetch_user_chat_history.php",
			method:"POST",
			data:{to_user_id:to_user_id},
			success:function(data){
				$('#chat_history_'+to_user_id).html(data);
			}
		})
	}

	function update_chat_history_data()
	{
		$('.chat_history').each(function(){
			var to_user_id = $(this).data('touserid');
			fetch_user_chat_history(to_user_id);
		});
	}

	$(document).on('click', '.ui-button-icon', function(){
		$('.user_dialog').dialog('destroy').remove();
		$('#is_active_group_chat_window').val('no');
	});

	$(document).on('focus', '.chat_message', function(){
		var is_type = 'yes';
		$.ajax({
			url:"update_is_type_status.php",
			method:"POST",
			data:{is_type:is_type},
			success:function()
			{

			}
		})
	});

	$(document).on('blur', '.chat_message', function(){
		var is_type = 'no';
		$.ajax({
			url:"update_is_type_status.php",
			method:"POST",
			data:{is_type:is_type},
			success:function()
			{
				
			}
		})
	});

	// $('#group_chat_dialog').dialog({
	// 	autoOpen:false,
	// 	width:400
	// });

	$('#group_chat').click(function(){
		$('#group_chat_dialog').dialog('open');
		$('#is_active_group_chat_window').val('yes');
		fetch_group_chat_history();
	});

	$('#send_group_chat').click(function(){
		var chat_message = $.trim($('#group_chat_message').html());
		var action = 'insert_data';
		if(chat_message != '')
		{
			$.ajax({
				url:"group_chat.php",
				method:"POST",
				data:{chat_message:chat_message, action:action},
				success:function(data){
					$('#group_chat_message').html('');
					$('#group_chat_history').html(data);
				}
			})
		}
		else
		{
			alert('Type something');
		}
	});

	function fetch_group_chat_history()
	{
		var group_chat_dialog_active = $('#is_active_group_chat_window').val();
		var action = "fetch_data";
		if(group_chat_dialog_active == 'yes')
		{
			$.ajax({
				url:"group_chat.php",
				method:"POST",
				data:{action:action},
				success:function(data)
				{
					$('#group_chat_history').html(data);
				}
			})
		}
	}

	$('#uploadFile').on('change', function(){
		$('#uploadImage').ajaxSubmit({
			target: "#group_chat_message",
			resetForm: true
		});
	});

	$(document).on('click', '.remove_chat', function(){
		var chat_message_id = $(this).attr('id');
		if(confirm("Are you sure you want to remove this chat?"))
		{
			$.ajax({
				url:"remove_chat.php",
				method:"POST",
				data:{chat_message_id:chat_message_id},
				success:function(data)
				{
					update_chat_history_data();
				}
			})
		}
	});
	
}); 