<?php include'db_connect.php' ?>
<div class="card-header">
			<div class="card-tools">
				<a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="index.php?page=view_survey_report_all&id=<?php echo $_GET['id'] ?>"><i class="fa fa-eye"></i> View All</a>
			</div>
		</div>
<div class="col-lg-12">
	<div class="card card-outline card-primary">
		<div class="card-body">
			<table class="table tabe-hover table-bordered" id="list">
				<colgroup>
					<col width="5%">
					<col width="20%">
					<col width="20%">
					<col width="20%">
					<col width="20%">
					<col width="15%">
				</colgroup>
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th>Taken By</th>
						<th>Date</th>
						<th>Time</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					$qry = $conn->query("SELECT 
                        DATE_FORMAT(answers.date_created, '%Y-%m-%d %H:%i:%s') AS timestamp, 
                        GROUP_CONCAT(DISTINCT CONCAT(users.firstname, ' ', users.lastname) SEPARATOR ' _ ') AS user_names
                    FROM answers
                    INNER JOIN survey_set ON survey_set.id = answers.survey_id
                    INNER JOIN users ON users.id = answers.user_id
                    WHERE answers.survey_id = {$_GET['id']}
                    GROUP BY timestamp, survey_set.title
                    ORDER BY timestamp DESC;");

					while($row= $qry->fetch_assoc()):
				// 		echo '<pre>';
				// 		print_r($row);
				// 		echo '</pre>';
					?>
					<tr>
						<th class="text-center"><?php echo $i++ ?></th>
						<td><b><?php echo $row['user_names']?></b></td>
						<td><b><?php echo date("M d, Y",strtotime($row['timestamp'])) ?></b></td>
						<td><b><?php echo date("h:i:s A", strtotime($row['timestamp'])) ?></b></td>
						<td class="text-center">
		                    <div class="btn-group">
		                        <a href="index.php?page=user_servey_det&d=<?php echo $row['timestamp'] ?>" class="btn btn-info btn-flat">
		                          <i class="fas fa-eye"></i>
		                        </a>
	                      </div>
						</td>
					</tr>	
				<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		$('#list').dataTable({
        "pageLength": 20,
    })
	$('.delete_survey').click(function(){
	_conf("Are you sure to delete this survey?","delete_survey",[$(this).attr('data-id')])
	})
	})
	function delete_survey($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_survey',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Data successfully deleted",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
			}
		})
	}
</script>