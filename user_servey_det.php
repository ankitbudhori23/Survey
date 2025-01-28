<?php include'db_connect.php' ?>

<?php 
$answers = $conn->query("SELECT a.*,q.type from answers a inner join questions q on q.id = a.question_id where a.date_created='{$_GET['d']}'");
$ans = array();

while($row=$answers->fetch_assoc()){
	$survery_id =  $row['survey_id'];
	if($row['type'] == 'radio_opt'){
		$ans[$row['question_id']][$row['answer']][] = 1;
	}
	if($row['type'] == 'check_opt'){
		foreach(explode(",", str_replace(array("[","]"), '', $row['answer'])) as $v){
		$ans[$row['question_id']][$v][] = 1;
		}
	}
	if($row['type'] == 'textfield_s'){
		$ans[$row['question_id']][] = $row['answer'];
	}
    if($row['type'] == 'image'){
        $ans[$row['question_id']][] = json_decode($row['answer'], true);
    }
}

$taken = $conn->query("SELECT distinct(user_id) from answers where survey_id ={$survery_id} ")->num_rows;

?>
<style>
	.tfield-area{
		max-height: 30vh;
		overflow: auto;
	}
</style>
<div class="col-lg-12">
	<div class="row">
		<div class="col-md-12">
			<div class="card card-outline card-success">
				<div class="card-header">
					<h3 class="card-title"><b>Survey Report</b></h3>
				</div>
				<div class="card-body ui-sortable">
					<?php 
					$question = $conn->query("SELECT * FROM questions where survey_id = {$survery_id} order by abs(order_by) asc,abs(id) asc");
					while($row=$question->fetch_assoc()):	
					?>
					<div class="callout callout-info">
						<h5><?php echo $row['question'] ?></h5>	
						<div class="col-md-12">
						<input type="hidden" name="qid[<?php echo $row['id'] ?>]" value="<?php echo $row['id'] ?>">	
						<input type="hidden" name="type[<?php echo $row['id'] ?>]" value="<?php echo $row['type'] ?>">	
							
						<?php if($row['type'] == 'image'):?>
							<?php if(isset($ans[$row['id']])): ?>
								<?php foreach($ans[$row['id']] as $images): ?>
									<?php foreach($images as $val): ?>
										<img onclick="showImageModal('<?php echo $val ?>')" src="<?php echo $val ?>" alt="Image Answer" style="width: 150px; height: 150px; cursor: pointer">
									<?php endforeach; ?>
								<?php endforeach; ?>
							<?php endif; ?>

						<?php elseif($row['type'] != 'textfield_s'):?>
							<ul>
							<?php foreach(json_decode($row['frm_option']) as $k => $v):
								$prog = $taken > 0 ? ((isset($ans[$row['id']][$k]) ? count($ans[$row['id']][$k]) : 0) / $taken) * 100 : 0;
								$prog = round($prog,2);
								?>
								<li>
									<div class="d-block w-100">
										<b><?php echo $v ?></b>
									</div>
									<div class="d-flex w-100">
									<div class="mx-1 col-sm-8"">
									<div class="progress w-100" >
					                  <div class="progress-bar bg-primary progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $prog ?>%">
					                    <span class="sr-only"><?php echo $prog ?>%</span>
					                  </div>
					                </div>
					                </div>
					                <span class="badge badge-info"><?php echo $prog ?>%</span>
									</div>
								</li>
								<?php endforeach; ?>
							</ul>
						<?php else: ?>
							<div class="d-block tfield-area w-100">
								<?php if(isset($ans[$row['id']])): ?>
								<?php foreach($ans[$row['id']] as $val): ?>
								<?php echo $val ?>
								<?php endforeach; ?>
								<?php endif; ?>
							</div>
						<?php endif; ?>
						</div>	
					</div>
					<?php endwhile; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modal for full screen image -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="imageModalLabel">Image View</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <img id="modalImage" src="" alt="Full Screen Image" style="width: 100%; height: auto;">
      </div>
    </div>
  </div>
</div>

<script>
	function showImageModal(src) {
		$('#modalImage').attr('src', src);
		$('#imageModal').modal('show');
	}
</script>