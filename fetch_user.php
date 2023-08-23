<?php

//fetch_user.php

include('database_connection.php');

session_start();

$query = "
SELECT * FROM login 
WHERE user_id != '".$_SESSION['user_id']."' 
";

$statement = $connect->prepare($query);

$statement->execute();

$result = $statement->fetchAll();

$output1 = '';
foreach ($result as $row){
	$output1 .= '<div class="d-flex flex-stack py-4">
			<!--begin::Details-->
			<div class="d-flex align-items-center">
				<!--begin::Avatar-->
				<div class="symbol symbol-35px symbol-circle">
					<img alt="Pic" src="'.$row['avatar'].'" />
				</div>
				<!--end::Avatar-->
				<!--begin::Details-->
				<div class="ms-5">
					<a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2 start_chat" data-touserid='.$row['user_id'].' data-tousername='.$row['username'].' >'.$row['username'].'</a>
					<div class="fw-semibold text-muted">melody@altbox.com</div>
				</div>
				<!--end::Details-->
			</div>
			<!--end::Details-->
			<!--begin::Lat seen-->
			<div class="d-flex flex-column align-items-end ms-2">
				<span class="text-muted fs-7 mb-1">1 week</span>
			</div>
			<!--end::Lat seen-->
			</div>
			<!--end::User-->
			<!--begin::Separator-->
			<div class="separator separator-dashed d-none"></div>';
}


$output = '
<table class="table table-bordered table-striped">
	<tr>
		<th width="70%">Username</td>
		<th width="20%">Status</td>
		<th width="10%">Action</td>
	</tr>
';

foreach($result as $row)
{
	$status = '';
	$current_timestamp = strtotime(date("Y-m-d H:i:s") . '- 10 second');
	$current_timestamp = date('Y-m-d H:i:s', $current_timestamp);
	$user_last_activity = fetch_user_last_activity($row['user_id'], $connect);
	if($user_last_activity > $current_timestamp)
	{
		$status = '<span class="label label-success">Online</span>';
	}
	else
	{
		$status = '<span class="label label-danger">Offline</span>';
	}
	$output .= '
	<tr>
		<td>'.$row['username'].' '.count_unseen_message($row['user_id'], $_SESSION['user_id'], $connect).' '.fetch_is_type_status($row['user_id'], $connect).'</td>
		<td>'.$status.'</td>
		<td><button type="button" class="btn btn-info btn-xs start_chat" data-touserid="'.$row['user_id'].'" data-tousername="'.$row['username'].'">Start Chat</button></td>
	</tr>
	';
}

$output .= '</table>';

echo $output1;

?>