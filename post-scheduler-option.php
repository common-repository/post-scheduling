<?php
add_action('admin_menu', 'post_scheduler_menu');              	 	

function post_scheduler_menu() {
	
    add_menu_page('Post Scheduler Setting', 'Post Scheduler', 'manage_options', basename(__FILE__), 'post_scheduler', null, 6 );
    
}

function post_scheduler() { ?>
	<div class="wrap">
        <div id="success" style="display:none;">Your post successfully scheduled.</div>
        <div class="importer_holder">
        	<h2>Post Scheduler</h2>
        	<div class="importer_box" id="importer_setting">
        		<form name="ps_form" method="post" action="" enctype="multipart/form-data" id="ps-form">
        			
        			<div class="import_left"><strong>Upload Zip file :</strong></div>
        			<div class="import_right"><input name="upload_file" type="file" id="upload_file" value=""/><br><div id="err-upload-file"></div></div>
        			
        			<div class="import_left"><strong>Select Post Type :</strong></div>
        			<div class="import_right">
        				<select name="post_type" id="post_type"><option value="">Select Post</option>
								<?php $post_types = get_post_types(); 
									  foreach ( $post_types as $post_type ) { ?>
										<option value="<?php echo $post_type; ?>"><?php echo $post_type; ?></option>  
								<?php } ?>
                        </select><div id="err-posttype"></div>
                   	</div>
                   	
                   	<div class="import_left"><strong>Select Category : </strong></div>
                   	<div class="import_right"><?php wp_dropdown_categories(array('hide_empty' => 0,'show_option_none'   => 'Select Category','order'  => 'ASC',)); ?>
                   		OR You can Add your custom category <input type="text" name="catname" value="" placeholder="Add your custom category" /><br>
                   		<div id="err-category"></div>
                	</div>
                	
                	<div class="import_left"><strong>Allow Comments :</strong></div>
                	<div class="import_right"><input type="checkbox" name="comments" id='checkbox'></div>
                	
                	<div class="import_left"><strong>Schedule Your post for publishing :</strong></div>
                	<div class="import_right"><label for="uep-event-start-date"><?php _e( 'Start time and date:', 'uep' ); ?></label><br>
                		<input type="text" id="datepicker" name="date" value="" class="example-datepicker required" data-field="datetime" /><div id="err-datetime"></div>
                	</div>
                	
                	<div class="import_left"></div>
                	<div class="import_right"><label for="uep-event-start-date"><strong><?php _e( 'Select Frequency Range :', 'uep' ); ?></strong></label><br>
                		<div class="freq-range">
                			<div class="min-range">
                				<label for="minutes"><?php _e( 'Minutes :', 'uep' ); ?></label><br>
                				<select name="minutes">
                					<option value="">--Select Minutes--</option>
                					<?php for ($i = 0; $i<=60; $i++){?>
                						<option value="<?php echo $i;?>"><?php echo $i."Minutes";?></option>
                					<?php }?>
                				</select><br><div id="err-freq-min"></div>
                			</div>
                			<div class="min-range">
                				<label for="hours"><?php _e( 'Hours :', 'uep' ); ?></label><br>
                				<select name="hours">
                					<option value="">--Select Hours--</option>
                					<?php  for ($i = 0; $i<=24; $i++){?>
                						<option value="<?php echo $i;?>"><?php echo $i."Hours";?></option>
                					<?php }?>
                				</select><br><div id="err-freq-hours"></div>
                            </div>
                            <div class="min-range">
                            	<label for="day"><?php _e( 'Days :', 'uep' ); ?></label><br>
                            	<input type="text" name="day" value="0" placeholder="Day"/><br><div id="err-freq-day"></div>
                            </div>
                        </div>
                    </div>
                    
					<div class="import_left"><input type="hidden" name="action" value="update" /></div>
					<div class="import_right"> <?php wp_nonce_field( 'post-schedule', 'post-schedule_nonce' ); ?>
					<input type="submit" class="button button-primary" value="<?php _e('Save Changes') ?>" /></div>
        		</form>
        	</div>
        </div>
    </div>
<?php 
	if(! wp_verify_nonce( $_POST['post-schedule_nonce'], 'post-schedule' ) ) {
		return;
	}else{
		ps_save_data($_POST);
	}
}

function ps_save_data(){
	$resp['status'] = 'error';
	$cat= $_POST['cat'];
	$catname= $_POST['catname'];
	if ($catname != ""){
		$cat_name = array('cat_name' => $catname, 'category_nicename' => $catname );
		wp_insert_category($cat_name);
		$cat_name = get_cat_ID($catname);
	}else{ $cat_name = $cat; }
			
			if ( ! function_exists( 'wp_handle_upload' ) ) require_once( ABSPATH . 'wp-admin/includes/file.php' );
			$uploadfile = $_FILES['upload_file'];
			$upload_overrides = array( 'test_form' => FALSE );
			$movefile = wp_handle_upload( $uploadfile, $upload_overrides );
						
			if ( $movefile ) {
				$file = $movefile['file'];
				WP_Filesystem();
				$destination = wp_upload_dir();
				$destination_path = $destination['path'];
				$unzipfile = unzip_file( $destination_path.'/'.basename($file), $destination_path);
					if ( $unzipfile ) {
						$filename = pathinfo($_FILES['upload_file']['name'], PATHINFO_FILENAME);
						$files=glob($destination_path.'/'.$filename.'/*.txt');
						$count = count($files);
						$intval = get_the_interval($count);						
						$date = date("Y-m-d H:i:s", strtotime($_POST['date']));
						foreach ($files as $file) {
							$success = insert_the_post($file, $date, $cat_name);
							$date = date("Y-m-d H:i:s", strtotime("+".$intval."minutes", strtotime($date)));							
						}
						if(!$success==1){
							$resp['status'] = 'error';
						} else {
                            $resp['status'] = 'success';
						}
				   } else {
				      echo 'There was an error unzipping the file.';
				   }
				
			} else {
			    echo "Possible file upload attack!\n";
			}
	json_encode($resp);
}

function get_the_interval($count){
	if(isset($_POST['date'])){
		
		$date= date("Y-m-d H:i:s", strtotime('+'.$_POST['minutes'].' minutes', strtotime($_POST['date'])));
		$date= date("Y-m-d H:i:s", strtotime('+'.$_POST['hours'].' hours', strtotime($date)));
		$date= date("Y-m-d H:i:s", strtotime('+'.$_POST['day'].' days', strtotime($date)));
			
			$start_date = strtotime($_POST['date']);
			$end_date = strtotime($date);
			$interval = abs($start_date - $end_date);
			$minutes = round($interval / 60)."</br>";
			$intval = intval($minutes/$count);
	}
	return $intval;
}
function insert_the_post($file, $date, $cat_name){
	
	if(isset ($_POST['comments'])){ $comments = 'open'; }
	else { $comments = 'closed'; }
	
	$post_type= $_POST['post_type'];	
	$body = "";
	
	$txt_file = array();
	$txtfile = fopen($file, 'r');
	$j = 0;
	while (!feof($txtfile)) {
		$txt_data[$j] = fgets($txtfile);
		if($j>=3){
			$txt_file[] = $txt_data[$j];
		}
		$j++;
	}
	$title = $txt_data['0'];
	$meta_description = $txt_data['1'];
	$meta_tags = $txt_data['2'];
	foreach($txt_file as $test ){
		$body .= $test;
	}
	fclose($txtfile);
	$new_post = array(
						'post_title'    => $title,
						'post_status'   =>  'future',
						'comment_status' => $comments,
						'post_type' =>  $post_type,
						'post_content' => $body,
						'post_excerpt' => $meta_description,
						'post_category' => $cat_name,
						'tags_input'	=> $meta_tags,
						'post_date'  => $date
								
	);
					
	$pid = wp_insert_post($new_post);
	wp_set_post_terms( $pid, $cat_name, 'category', false );
	return true;
}					
?>