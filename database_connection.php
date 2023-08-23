<?php

//database_connection.php

$connect = new PDO("mysql:host=localhost;dbname=chat;charset=utf8mb4", "root", "");

date_default_timezone_set('Asia/Kolkata');

function fetch_user_last_activity($user_id, $connect)
{
	$query = "
	SELECT * FROM login_details 
	WHERE user_id = '$user_id' 
	ORDER BY last_activity DESC 
	LIMIT 1
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		return $row['last_activity'];
	}
}

function fetch_user_chat_history($from_user_id, $to_user_id, $connect)
{
	$query = "
	SELECT l.avatar, q1.*
	FROM
	(SELECT * FROM chat_message 
	WHERE (from_user_id = '".$from_user_id."' 
	AND to_user_id = '".$to_user_id."') 
	OR (from_user_id = '".$to_user_id."' 
	AND to_user_id = '".$from_user_id."') 
	ORDER BY timestamp DESC) q1
	LEFT JOIN login l
	on q1.from_user_id = l.user_id
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();

	$output1 = '<div class="scroll-y" data-kt-element="messages" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_header, #kt_app_header, #kt_app_toolbar, #kt_toolbar, #kt_footer, #kt_app_footer, #kt_chat_messenger_header, #kt_chat_messenger_footer" data-kt-scroll-wrappers="#kt_content, #kt_app_content, #kt_chat_messenger_body" data-kt-scroll-offset="5px">';
	foreach($result as $row)
	{
		$user_name = '';
		$dynamic_background = '';
		$chat_message = '';
		if($row["from_user_id"] == $from_user_id)
		{
			// if($row["status"] == '2')
			// {
			// 	$chat_message = '<em>This message has been removed</em>';
			// 	$user_name = '<b class="text-success">You</b>';
			// }
			// else
			// {
			// 	$chat_message = $row['chat_message'];
			// 	$user_name = '<button type="button" class="btn btn-danger btn-xs remove_chat" id="'.$row['chat_message_id'].'">x</button>&nbsp;<b class="text-success">You</b>';
			// }
			

			// $dynamic_background = 'background-color:#ffe6e6;';
			$output1 .=	'<!--begin::Message(out)-->
							<div class="d-flex justify-content-end mb-10">
								<!--begin::Wrapper-->
								<div class="d-flex flex-column align-items-end">
									<!--begin::User-->
									<div class="d-flex align-items-center mb-2">
										<!--begin::Details-->
										<div class="me-3">
											<span class="text-muted fs-7 mb-1">'.$row['timestamp'].'</span>
											<a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary ms-1">You</a>
										</div>
										<!--end::Details-->
										<!--begin::Avatar-->
										<div class="symbol symbol-35px symbol-circle">
											<img alt="Pic" src="'.$row['avatar'].'" />
										</div>
										<!--end::Avatar-->
									</div>
									<!--end::User-->
									<!--begin::Text-->
									<div class="p-5 rounded bg-light-primary text-dark fw-semibold mw-lg-400px text-end" data-kt-element="message-text">'.$row['chat_message'].'</div>
									<!--end::Text-->
								</div>
								<!--end::Wrapper-->
							</div>
							<!--end::Message(out)-->';
			
		}
		else
		{
			// if($row["status"] == '2')
			// {
			// 	$chat_message = '<em>This message has been removed</em>';
			// }
			// else
			// {
			// 	$chat_message = $row["chat_message"];
			// }
			// $user_name = '<b class="text-danger">'.get_user_name($row['from_user_id'], $connect).'</b>';
			// $dynamic_background = 'background-color:#ffffe6;';
			$output1 .=	'<!--begin::Message(in)-->
						<div class="d-flex justify-content-start mb-10">
							<!--begin::Wrapper-->
							<div class="d-flex flex-column align-items-start">
								<!--begin::User-->
								<div class="d-flex align-items-center mb-2">
									<!--begin::Avatar-->
									<div class="symbol symbol-35px symbol-circle">
										<img alt="Pic" src="'.$row['avatar'].'" />
									</div>
									<!--end::Avatar-->
									<!--begin::Details-->
									<div class="ms-3">
										<a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary me-1"></a>
										<span class="text-muted fs-7 mb-1">'.$row['timestamp'].'</span>
									</div>
									<!--end::Details-->
								</div>
								<!--end::User-->
								<!--begin::Text-->
								<div class="p-5 rounded bg-light-info text-dark fw-semibold mw-lg-400px text-start" data-kt-element="message-text">'.$row['chat_message'].'</div>
								<!--end::Text-->
							</div>
							<!--end::Wrapper-->
						</div>
						<!--end::Message(in)-->';
		}
		// $output .= '
		// <li style="border-bottom:1px dotted #ccc;padding-top:8px; padding-left:8px; padding-right:8px;'.$dynamic_background.'">
		// 	<p>'.$user_name.' - '.$chat_message.'
		// 		<div align="right">
		// 			- <small><em>'.$row['timestamp'].'</em></small>
		// 		</div>
		// 	</p>
		// </li>
		// ';
	}
	
	
	$output1 .= '</div>';
	
	$query = "
	UPDATE chat_message 
	SET status = '0' 
	WHERE from_user_id = '".$to_user_id."' 
	AND to_user_id = '".$from_user_id."' 
	AND status = '1'
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	return $output1;
}

function get_user_name($user_id, $connect)
{
	$query = "SELECT username FROM login WHERE user_id = '$user_id'";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		return $row['username'];
	}
}

function count_unseen_message($from_user_id, $to_user_id, $connect)
{
	$query = "
	SELECT * FROM chat_message 
	WHERE from_user_id = '$from_user_id' 
	AND to_user_id = '$to_user_id' 
	AND status = '1'
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	$count = $statement->rowCount();
	$output = '';
	if($count > 0)
	{
		$output = '<span class="label label-success">'.$count.'</span>';
	}
	return $output;
}

function fetch_is_type_status($user_id, $connect)
{
	$query = "
	SELECT is_type FROM login_details 
	WHERE user_id = '".$user_id."' 
	ORDER BY last_activity DESC 
	LIMIT 1
	";	
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$output = '';
	foreach($result as $row)
	{
		if($row["is_type"] == 'yes')
		{
			$output = ' - <small><em><span class="text-muted">Typing...</span></em></small>';
		}
	}
	return $output;
}

function fetch_group_chat_history($connect)
{
	$query = "
	SELECT * FROM chat_message 
	WHERE to_user_id = '0'  
	ORDER BY timestamp DESC
	";

	$statement = $connect->prepare($query);

	$statement->execute();

	$result = $statement->fetchAll();

	$output = '<ul class="list-unstyled">';
	foreach($result as $row)
	{
		$user_name = '';
		$dynamic_background = '';
		$chat_message = '';
		if($row["from_user_id"] == $_SESSION["user_id"])
		{
			if($row["status"] == '2')
			{
				$chat_message = '<em>This message has been removed</em>';
				$user_name = '<b class="text-success">You</b>';
			}
			else
			{
				$chat_message = $row["chat_message"];
				$user_name = '<button type="button" class="btn btn-danger btn-xs remove_chat" id="'.$row['chat_message_id'].'">x</button>&nbsp;<b class="text-success">You</b>';
			}
			
			$dynamic_background = 'background-color:#ffe6e6;';
		}
		else
		{
			if($row["status"] == '2')
			{
				$chat_message = '<em>This message has been removed</em>';
			}
			else
			{
				$chat_message = $row["chat_message"];
			}
			$user_name = '<b class="text-danger">'.get_user_name($row['from_user_id'], $connect).'</b>';
			$dynamic_background = 'background-color:#ffffe6;';
		}

		$output .= '

		<li style="border-bottom:1px dotted #ccc;padding-top:8px; padding-left:8px; padding-right:8px;'.$dynamic_background.'">
			<p>'.$user_name.' - '.$chat_message.' 
				<div align="right">
					- <small><em>'.$row['timestamp'].'</em></small>
				</div>
			</p>
		</li>
		';
	}
	$output .= '</ul>';
	return $output;
}


?>