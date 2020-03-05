<?php

use Phalcon\Cli\Task;

//php cli.php main synchronizefiles

class MainTask extends Task
{

    public function mainAction()
    {
        echo 'This is the default task and the default action' . PHP_EOL;
    }

    public function synchronizefilesAction()
    {
	  $di       = $this->getDI();
	  $db       = $di->get('db');
	  $config   = $di->get('config');

	  $docs = self::documentsNotSynchronizedToWecollaborate($db) ;

	  $log = new \APIv2\Travel\Models\Log;
	  $log->username     = 'cronjob';
	  $log->action_type  = "INF: FILES REQUIRING SYNCHRONIZATION";
	  $log->uploaded_mission_report_id = null;
	  $log->action_details = \json_encode( $docs );
	  $log->date = @date("Y-m-d H:i:s");
	  $log->save();

	  
	  
	  foreach( $docs as $v )
	  {
		  if ( is_file( $config['app']['upload_mission_report_path'] . $v['uploaded_mission_report_id'] ."-". $v['doc_name']) ) {

			   $log = new \APIv2\Travel\Models\Log;
			   $log->username     = 'cronjob';
			   $log->action_type  = "REQ: UPLOADING TO WECOLLABORATE";
			   $log->uploaded_mission_report_id = null;
			   $log->action_details = $v['uploaded_mission_report_id'] ."-". $v['doc_name'] ;
			   $log->date = @date("Y-m-d H:i:s");
			   $log->save();

		       $wecollaborate_data = self::addDocumentToWecollaborate($v['uploaded_mission_report_id'], $v['doc_name'], $config);

			   if ($wecollaborate_data['status'] === 'ok' && !empty( $wecollaborate_data['msg']['results'] ) )
			   {

				   $log = new \APIv2\Travel\Models\Log;
				   $log->username     = 'cronjob';
				   $log->action_type  = "RES: UPLOAD TO WECOLLABORATE SUCCESS";
				   $log->uploaded_mission_report_id = null;
				   $log->action_details = \json_encode( $wecollaborate_data );
				   $log->date = @date("Y-m-d H:i:s");
				   $log->save();

				   $db->begin();
				   
				   $success = $db->execute(
						"UPDATE uploaded_mission_document_map SET wecollaborate_attachment_id =? , wecollaborate_download_path = ?, is_uploaded_to_confluence =?, uploaded_to_confluence_date =?  WHERE uploaded_mission_report_id =? AND doc_name = ? ",
						[
							$wecollaborate_data['msg']['results'][0]['id'],
							$wecollaborate_data['msg']['results'][0]['_links']['download'],
							1,
					        @date("Y-m-d H:i:s"), 
					        $v['uploaded_mission_report_id'],
					        $v['doc_name']
						]
					);

				   if ( $db->affectedRows() > 1 )
				   {
						  $log = new \APIv2\Travel\Models\Log;
						  $log->username     = 'cronjob';
						  $log->action_type  = "ERR: UPDATING LOCAL WECOLLABORATE DATA";
						  $log->uploaded_mission_report_id = null;
						  $log->action_details = \json_encode( [ 'doc_name' => $v['uploaded_mission_report_id'] ."-". $v['doc_name'], 'sql_query' => $db->getSQLStatement(), 'sql_variables' => $db->getSqlVariables() , 'affected_rows' => $db->affectedRows() ] );
						  $log->date = @date("Y-m-d H:i:s");
						  $log->save();

					      $db->rollback();
					      print "More than one file culprit ";
				   
				   } else {

					   $db->commit();
				   
				   }  

		       }
			   else
			   {			   				      
					  print_r ( $wecollaborate_data['msg'] ) ;

					  $log = new \APIv2\Travel\Models\Log;
					  $log->username     = 'cronjob';
					  $log->action_type  = "RES: UPLOAD TO WECOLLABORATE ERROR";
					  $log->uploaded_mission_report_id = null;
					  $log->action_details = \json_encode( $wecollaborate_data );
					  $log->date = @date("Y-m-d H:i:s");
					  $log->save();
			   
			   }
		  
		  } else {

			  $log = new \APIv2\Travel\Models\Log;
			  $log->username     = 'cronjob';
		      $log->action_type  = "ERR: PHYSICAL FILE IS NOT FOUND";
		      $log->uploaded_mission_report_id = null;
		      $log->action_details = $v['uploaded_mission_report_id'] ."-". $v['doc_name'] ;
		      $log->date = @date("Y-m-d H:i:s");
		      $log->save();
		  
		  }

	  
	  }

    }

	public function notifyuploadreportsAction()
	{

		\APIv2\Travel\Services\EmailNotification::bulkNotifyStaffWithMissionsWithoutReports();
	}

   	public static function addDocumentToWecollaborate( $missionId, $docName, $config ) {
		
		$username       = $config['wecollaborate']['username'];
		$password       = $config['wecollaborate']['password'];
		$documents_page = $config['wecollaborate']['documents_page'];

		$file_name      = $docName;
		$file_path      = $config['app']['upload_mission_report_path'] . $missionId ."-" . $docName ;
		$file_type      = mime_content_type($file_path);

		$data = [
		'file' => \curl_file_create( $file_path, $file_type, $missionId .'-'. $file_name )
		 ];
		
		$ch = \curl_init();
		
		\curl_setopt_array($ch, 
			[
			CURLOPT_POST => 1,
			CURLOPT_URL => $documents_page,
			CURLOPT_USERPWD => $username . ':' . $password,
			CURLOPT_POSTFIELDS => $data,
			CURLOPT_VERBOSE => 0,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => ['X-Atlassian-Token: nocheck']
		    ]
		);

		$result = curl_exec($ch);
		$ch_error = curl_error($ch);

		curl_close($ch);
		
		if ($ch_error) {
			return ['status' => 'error', 'msg' => $ch_error ];
		} else {
			return ['status' => 'ok' ,  'msg' => \json_decode($result, true), 'msg_raw' => $result, 'ch_error' => $ch_error ];
		}		
	}

	public static function updateDocTable($missionId, $docName, $weCollaborateData, $db)
	{	
	}

	public static function documentsNotSynchronizedToWecollaborate($db)
	{

		$sql = "		
			SELECT
				uploaded_mission_report_id,
				doc_name 
			FROM
				uploaded_mission_document_map 
			WHERE
				wecollaborate_attachment_id IS NULL 
				OR is_uploaded_to_confluence = 0 " ;


		$result_set = $db->query($sql);
        $result_set->setFetchMode( \Phalcon\Db::FETCH_ASSOC );
        $result_set = $result_set->fetchAll($result_set);

		return $result_set;
	      
	}

	public function asyncUpdateMissionListAction() {
	
	    $di       = $this->getDI();
	    $db       = $di->get('db');
	    $config   = $di->get('config');
		
		$sql = "CALL updateMissionList(); " ;

		$result_set = $db->query($sql);

		return $result_set;
	
	}


}
