<?php

use Phalcon\Http\Response;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;



$app->get('/', function () use ($app) {
  echo 'API g v2';
});

$app->get('/projects/all', function () use ($app) {
   $app->response->setContentType("application/json");
   $app->response->setHeader('Access-Control-Allow-Origin', '*');
   $app->response->setHeader('Access-Control-Allow-Headers', 'X-Requested-With');      
   $app->response->sendHeaders();
   
   $more_data = [];
   $data = \Chemicals\Backend\Models\Project::find(array('delete_flag'=>''));
    foreach($data as $data)
        {
			$tmp = $data->toArray();
			 $tmp['country'] = [];
             $countrymap = \Chemicals\Backend\Models\ProjectCountryMap::find(array(
			 "project_id = {$data->id}",
			 "columns" => "country_iso_2"
			 ));
			   $c = [];
			  foreach($countrymap as $countrymap){
				   $tmpc = $countrymap->toArray();
				  
				   $tmpc['country_name'] = 'N/A';
                   $countrymap->country_iso_2 = strtoupper($countrymap->country_iso_2);
				   $cn = \Chemicals\Backend\Models\Country::find(array(
				   "iso_2  = '{$countrymap->country_iso_2}'",
                   "columns" => "iso_2, name"
                  ));
				   $tmpc['country_name'] = $cn[0]['name'];
				   
				  // $countrymap = $tmpc;
				 
				    //$c['name'] = "test";
				    $c[] = $tmpc;
				 
			  }
			   $tmp['country'] = $c;

			 $tmp['party'] = [];
             $partymap = \Chemicals\Backend\Models\ProjectPartyMap::find(array(
			 "project_id = {$data->id}",
			 "columns" => "party_id"
			 ));


			   $c = [];
			  foreach($partymap as $partymap){
				   $tmpc = $partymap->toArray();
				  
				   $tmpc['party_name'] = 'N/A';
				   $cn = \Chemicals\Backend\Models\Party::find(array(
				   "id  = '{$partymap->party_id}'",
                   "columns" => "party"
                  ));
				   $tmpc['party_name'] = $cn[0]['party'];
				   
				  // $countrymap = $tmpc;
				 
				    //$c['name'] = "test";
				    $c[] = $tmpc;
				 
			  }
			   $tmp['party'] = $c;
			
			  
			  

			
			
			$tmp['keywords'] = \Chemicals\Backend\Models\ProjectKeyWordMap::find(array(
                    "project_id = {$data->id}"
            ));
			
			 $tmp['regions'] = [];
             $regionsmap = \Chemicals\Backend\Models\ProjectRegionMap::find(array(
			 "project_id = {$data->id}",
			 "columns" => "region_id"
			 ));

			
			   $c = [];
			  foreach($regionsmap as $regionsmap){
				   $tmpc = $regionsmap->toArray();
				  
				   /*$tmpc['region_name'] = 'N/A';
				   $tmpc['region_iso'] = 'N/A';*/
				   $cn = \Chemicals\Backend\Models\Region::find(array(
				   "id  = '{$regionsmap->region_id}'",
                   "columns" => "region_iso,region_name"
                  ));
				   $tmpc['region_name'] = $cn[0]['region_name'];
				   
				  // $countrymap = $tmpc;
				 
				    //$c['name'] = "test";
				    $c[] = $tmpc;
				 
			  }
			   $tmp['regions'] = $c;
			
			
			
			
			
			
			$more_data[] = $tmp;
			
			//$data->project_country = $project_country;
			
			//array_push($data, '3');
		
        }

            //return $data;
  
   $app->response->setContent( 
		\json_encode(
			[ 
			   'status' => 'OK',
			   'data'   => $more_data,
			]
		)
   );
   

   return $app->response ;

});


$app->get('/projects/country/{cn}', function ($cn) use ($app) {
  
	$app->response->setContentType("application/json");
	$app->response->setHeader('Access-Control-Allow-Origin', '*');
	$app->response->setHeader('Access-Control-Allow-Headers', 'X-Requested-With');      
	$app->response->sendHeaders();
 
	$country_data = [];
  
	
	 $cfn = \Chemicals\Backend\Models\Country::find(array(
					"iso_2  = '{$cn}'",
					"columns" => "name,latitude,longitude"
					));
					
	 foreach($cfn as $cfn){
		 $cnn = $cfn->name;
		 $lat = $cfn->latitude;
		 $lon = $cfn->longitude;
		 $country_data['projects']['name'] = $cnn;
		 $country_data['projects']['latitude'] = $lat;
		 $country_data['projects']['longitude'] = $lon;
	 }
					
	 $country_data['name'] = $cfn['name'];
	 $country_data['lat']  = $lat; 
	 $country_data['lon']  = $lon; 
 
	 
	 $cp = \Chemicals\Backend\Models\ProjectCountryMap::find(array(
					"country_iso_2 = '{$cn}'",
					"columns" => "project_id"));
	 $country_data['projects'] =[];			   
	 $country_data['projects']['data'] =[];
	 
	 foreach($cp as $cp){
		 $tp = $cp->project_id;
		 $country_data['projects']['data'][] = $tp;
	 }
	 $country_data['projects']['count'] = count($country_data['projects']['data']);
	 
	 /**var_dump($country_data);
	 exit;
	 foreach($data as $data)
		 {
			 $tmp = $data->toArray();
			 $tmp['country'] = \Chemicals\Backend\Models\ProjectCountryMap::find(array("project_id = {$data->id}"));
			 
			 $tmp['keywords'] = \Chemicals\Backend\Models\ProjectKeyWordMap::find(array(
					 "project_id = {$data->id}"
			 ));
			 
			 $tmp['regions'] = \Chemicals\Backend\Models\ProjectRegionMap::find(array(
					 "project_id = {$data->id}"
			 ));
			 
			 $more_data[] = $tmp;
			 
			 //$data->project_country = $project_country;
			 
			 //array_push($data, '3');
		 
		 }**/
 
			 //return $data;
   
	$app->response->setContent( 
		 \json_encode(
			 [ 
				'status' => 'OK',
				'data'   => $country_data,
			 ]
		 )
	);
 
	return $app->response ;
 
 });
 $app->get('/projects/status/{status}', function ($id) use ($app) {
  
	$app->response->setContentType("application/json");
	$app->response->setHeader('Access-Control-Allow-Origin', '*');
	$app->response->setHeader('Access-Control-Allow-Headers', 'X-Requested-With');      
	$app->response->sendHeaders();
 
	$more_data = [];
	$data = \Chemicals\Backend\Models\Project::find($id);
	 foreach($data as $data)
		 {
			 $tmp = $data->toArray();
			 $tmp['country'] = \Chemicals\Backend\Models\ProjectCountryMap::find(array("status = {$data->status}"));
			 
			 $tmp['keywords'] = \Chemicals\Backend\Models\ProjectKeyWordMap::find(array(
					 "project_id = {$data->id}"
			 ));
			 
			 $tmp['regions'] = \Chemicals\Backend\Models\ProjectRegionMap::find(array(
					 "project_id = {$data->id}"
			 ));
			 
			 $more_data[] = $tmp;
			 
			 //$data->project_country = $project_country;
			 
			 //array_push($data, '3');
		 
		 }
 
			 //return $data;
   
	$app->response->setContent( 
		 \json_encode(
			 [ 
				'status' => 'OK',
				'data'   => $more_data,
			 ]
		 )
	);
 
	return $app->response ;
 
 });

$app->get('/project/{id}', function ($id) use ($app) {
  
   $app->response->setContentType("application/json");
   $app->response->setHeader('Access-Control-Allow-Origin', '*');
   $app->response->setHeader('Access-Control-Allow-Headers', 'X-Requested-With');      
   $app->response->sendHeaders();

   $more_data = [];
   $data = \Chemicals\Backend\Models\Project::find($id);
    foreach($data as $data)
        {
			$tmp = $data->toArray();
            $tmp['country'] = \Chemicals\Backend\Models\ProjectCountryMap::find(array("project_id = {$data->id}"));
			
			$tmp['keywords'] = \Chemicals\Backend\Models\ProjectKeyWordMap::find(array(
                    "project_id = {$data->id}"
            ));
			
			$tmp['regions'] = \Chemicals\Backend\Models\ProjectRegionMap::find(array(
                    "project_id = {$data->id}"
            ));
			
			$more_data[] = $tmp;
			
			//$data->project_country = $project_country;
			
			//array_push($data, '3');
		
        }

            //return $data;
  
   $app->response->setContent( 
		\json_encode(
			[ 
			   'status' => 'OK',
			   'data'   => $more_data,
			]
		)
   );

   return $app->response ;

});



$app->get('/country_project', function () use ($app) {
  
   $app->response->setContentType("application/json");
   $app->response->setHeader('Access-Control-Allow-Origin', '*');
   $app->response->setHeader('Access-Control-Allow-Headers', 'X-Requested-With');      
   $app->response->sendHeaders();

   
    $more_data = [];
    $cfn = \Chemicals\Backend\Models\Country::find();
				   
	foreach($cfn as $cfn){
		$country_data = [];
		$tmp = $cfn->toArray();
		//$country_data['projects']['name'] = $cnn;
    $cn = $tmp['iso_2'];
	$country_data['iso_2'] = $cn;
    $country_data['name'] = $tmp['name'];
	
	$country_data['lat']  = $tmp['latitude'];
    $country_data['lon']  = $tmp['longitude'];

	
	$cp = \Chemicals\Backend\Models\ProjectCountryMap::find(array(
	               "country_iso_2 = '{$cn}'",
				   "columns" => "project_id"));

	$country_data['projects'] =[];			   
	$country_data['projects']['data'] =[];
	
	foreach($cp as $cp){
		$tp = $cp->project_id;
		$country_data['projects']['data'][] = $tp;
	}
	$country_data['projects']['count'] = count($country_data['projects']['data']);
			
	$more_data[] =$country_data;		
	}
  
   $app->response->setContent( 
		\json_encode(
			[ 
			   'status' => 'OK',
			   'data'   => $more_data ,
			]
		)
   );

   return $app->response ;

});




$app->get('/project_country_map', function () use ($app) {
  
   $app->response->setContentType("application/json");
   $app->response->setHeader('Access-Control-Allow-Origin', '*');
   $app->response->setHeader('Access-Control-Allow-Headers', 'X-Requested-With');      
   $app->response->sendHeaders();

   $data = \Chemicals\Backend\Models\ProjectCountryMap::find();
  
   $app->response->setContent( 
		\json_encode(
			[ 
			   'status' => 'OK',
			   'data'   => $data,
			]
		)
   );

   return $app->response ;

});

$app->get('/project_keyword_map', function () use ($app) {
  
   $app->response->setContentType("application/json");
   $app->response->setHeader('Access-Control-Allow-Origin', '*');
   $app->response->setHeader('Access-Control-Allow-Headers', 'X-Requested-With');      
   $app->response->sendHeaders();

   $data = \Chemicals\Backend\Models\ProjectKeyWordMap::find();
  
   $app->response->setContent( 
		\json_encode(
			[ 
			   'status' => 'OK',
			   'data'   => $data,
			]
		)
   );

   return $app->response ;

});

$app->get('/project_region_map', function () use ($app) {
  
   $app->response->setContentType("application/json");
   $app->response->setHeader('Access-Control-Allow-Origin', '*');
   $app->response->setHeader('Access-Control-Allow-Headers', 'X-Requested-With');      
   $app->response->sendHeaders();

   $data = \Chemicals\Backend\Models\ProjectRegionMap::find();
  
   $app->response->setContent( 
		\json_encode(
			[ 
			   'status' => 'OK',
			   'data'   => $data,
			]
		)
   );

   return $app->response ;

});



$app->get('/country', function () use ($app) {
  
   $app->response->setContentType("application/json");
   $app->response->setHeader('Access-Control-Allow-Origin', '*');
   $app->response->setHeader('Access-Control-Allow-Headers', 'X-Requested-With');      
   $app->response->sendHeaders();

   $data = \Chemicals\Backend\Models\Country::find();
  
   $app->response->setContent( 
		\json_encode(
			[ 
			   'status' => 'OK',
			   'data'   => $data,
			]
		)
   );

   return $app->response ;

});

$app->get('/countryclass', function () use ($app) {
  
   $app->response->setContentType("application/json");
   $app->response->setHeader('Access-Control-Allow-Origin', '*');
   $app->response->setHeader('Access-Control-Allow-Headers', 'X-Requested-With');      
   $app->response->sendHeaders();

   $data = \Chemicals\Backend\Models\CountryClass::find();
  
   $app->response->setContent( 
		\json_encode(
			[ 
			   'status' => 'OK',
			   'data'   => $data,
			]
		)
   );

   return $app->response ;

});

$app->get('/party-to', function () use ($app) {
  
   $app->response->setContentType("application/json");
   $app->response->setHeader('Access-Control-Allow-Origin', '*');
   $app->response->setHeader('Access-Control-Allow-Headers', 'X-Requested-With');      
   $app->response->sendHeaders();

   $data = \Chemicals\Backend\Models\Party::find();
  
   $app->response->setContent( 
		\json_encode(
			[ 
			   'status' => 'OK',
			   'data'   => $data,
			]
		)
   );

   return $app->response ;

});

$app->get('/projectstatus', function () use ($app) {
  
   $app->response->setContentType("application/json");
   $app->response->setHeader('Access-Control-Allow-Origin', '*');
   $app->response->setHeader('Access-Control-Allow-Headers', 'X-Requested-With');      
   $app->response->sendHeaders();

   $data = \Chemicals\Backend\Models\ProjectStatus::find();
  
   $app->response->setContent( 
		\json_encode(
			[ 
			   'status' => 'OK',
			   'data'   => $data,
			]
		)
   );

   return $app->response ;

});

$app->get('/region', function () use ($app) {
  
   $app->response->setContentType("application/json");
   $app->response->setHeader('Access-Control-Allow-Origin', '*');
   $app->response->setHeader('Access-Control-Allow-Headers', 'X-Requested-With');      
   $app->response->sendHeaders();

   $data = \Chemicals\Backend\Models\Region::find();
  
   $app->response->setContent( 
		\json_encode(
			[ 
			   'status' => 'OK',
			   'data'   => $data,
			]
		)
   );

   return $app->response ;

});

$app->post('/project', function () use ($app) {

    //$textData = $_POST;
	$dt = file_get_contents('php://input');
	$dt = json_decode($dt, true);
	$dt = $dt['params']; 
	$textData = $dt;
	//var_dump($dt);
	//var_dump($_POST);
	//exit;

    $project = new \Chemicals\Backend\Models\Project(); 
    $mandatory_fields = $textData;
	unset($mandatory_fields['id']);
	unset($mandatory_fields['story_url']);
	unset($mandatory_fields['keyword']);
	
	$errors = [];
	foreach($mandatory_fields as $key => $value){
		if(!$value){
			$error = 1;
			$errors[] = 'fill in '.$key;
		}
	}
	 /*(if(!$textData['title']|| !$textData['summary'] ||!$textData['objectives'] ||!$textData['country_classification_id']
	 ||!$textData['sp_trust_fund_usd']||!$textData['co_financing_usd']||!$textData['duration_months'] || $textData['country_iso_2'] || textData['region_id']){ 
       $error = 'fill in all details';	 
	     $app->response->setContent( 
		\json_encode(
			[ 
			   'status' => 'error',
			   'data'   => $error
			]
		)
    );

	 }*/
	 if($error == 1){
		 
		  $app->response->setContent( 
		\json_encode(
			[ 
			   'status' => 'error',
			   'data'   => $errors
			]
		)
    );
		 
		 
	 }else{
	$project->title = $textData['title'];
	$project->summary = $textData['summary'];
	$project->objectives = $textData['objectives'];
	$project->country_classification_id = $textData['country_classification_id'];
	$project->sp_trust_fund_usd = $textData['sp_trust_fund_usd'];
	$project->co_financing_usd = $textData['co_financing_usd'];
	$project->duration_months = $textData['duration_months'];
	$project->story_url = $textData['story_url'];//not mandatory
	$project->save();
	if($project->save()){
		$insertedID = $project->id;
		
		foreach($textData['country_iso_2'] as $country_iso){
		$project_country_map = new \Chemicals\Backend\Models\ProjectCountryMap();
		$project_country_map->project_id = $insertedID;
		$project_country_map->country_iso_2 = $country_iso;
		$project_country_map->save();
		}
		
		foreach($textData['keyword'] as $keyword){//not mandatory
		$project_keyword_map = new \Chemicals\Backend\Models\ProjectKeyWordMap();
		$project_keyword_map->project_id = $insertedID;
		$project_keyword_map->keyword = $keyword;
		$project_keyword_map->save();
		}
		
		foreach($textData['region_id'] as $regionID){
		$project_region_map = new \Chemicals\Backend\Models\ProjectRegionMap();
		$project_region_map->project_id = $insertedID;
		$project_region_map->region_id = $regionID;
		$project_region_map->save();
		}
		
 	}else{
		$insertedID = 0;
	}
	  $app->response->setContent( 
		\json_encode(
			[ 
			   'status' => 'OK',
			   'insertedID' => $insertedID, 
			   'data'   => $textData
			]
		)
    );

	 }
   
  
    return $app->response ;

});



$app->put('/project/{id}', function ($id) use ($app) {
    

    global $_PUT;
    function parse_raw_http_request($a_data = [])
    {
    // read incoming data
    $input = file_get_contents('php://input');
    // grab multipart boundary from content type header
    preg_match('/boundary=(.*)$/', $_SERVER['CONTENT_TYPE'], $matches);
    // content type is probably regular form-encoded
    if (!count($matches))
    {
        // we expect regular puts to containt a query string containing data
        parse_str(urldecode($input), $a_data);
        return $a_data;
    }
    $boundary = $matches[1];
    // split content by boundary and get rid of last -- element
    $a_blocks = preg_split("/-+$boundary/", $input);
    array_pop($a_blocks);
    $keyValueStr = '';
    // loop data blocks
    foreach ($a_blocks as $id => $block)
    {
        if (empty($block))
            continue;
        // you'll have to var_dump $block to understand this and maybe replace \n or \r with a visibile char
        // parse uploaded files
        if (strpos($block, 'application/octet-stream') !== FALSE)
        {
            // match "name", then everything after "stream" (optional) except for prepending newlines
            preg_match("/name=\"([^\"]*)\".*stream[\n|\r]+([^\n\r].*)?$/s", $block, $matches);
            $a_data['files'][$matches[1]] = $matches[2];
        }
        // parse all other fields
        else
        {
            // match "name" and optional value in between newline sequences
            preg_match('/name=\"([^\"]*)\"[\n|\r]+([^\n\r].*)?\r$/s', $block, $matches);
            $keyValueStr .= $matches[1]."=".$matches[2]."&";
        }
    }
    $keyValueArr = [];
    parse_str($keyValueStr, $keyValueArr);
    return array_merge($a_data, $keyValueArr);
}
    $a_data = array();
    $GLOBALS[ '_PUT' ] = parse_raw_http_request($a_data);
	
	
	
	
    //return;
	  
   // $title = $this->request->getPut(); 
     //$t = json_decode($data, TRUE);
    //$textData = $_PUT;
    
	//var_dump($_PUT);
	
	//var_dump($data);
	//exit;
	//return $app->response ;
	//exit;
	
	
	/**$phql = "UPDATE project SET title = :title:, summary = :summary:, objectives = :objectives:, country_classification_id = :country_classification_id:,  sp_trust_fund_usd = :sp_trust_fund_usd:, co_financing_usd = :co_financing_usd:, duration_months = :duration_months:, story_url = :story_url: WHERE id = ".$id;
    $status = $app->modelsManager->executeQuery($phql, array(
        'id' => $id,
        'title' => $textData['title']
    ));**/

    //Create a response
    //$response = new Phalcon\Http\Response();

	//var_dump( $input = file_get_contents('php://input') );

	$sData = json_decode( file_get_contents('php://input'), true );
	//var_dump( $sData['params'] );
	//exit;
   
    $textData = $sData['params'];

    $project = \Chemicals\Backend\Models\Project::findFirst($id);
	$project->title = $textData['title'];
	$project->summary = $textData['summary'];
	$project->objectives = $textData['objectives'];
	$project->country_classification_id = $textData['country_classification_id'];
	$project->sp_trust_fund_usd = $textData['sp_trust_fund_usd'];
	$project->co_financing_usd = $textData['co_financing_usd'];
	$project->duration_months = $textData['duration_months'];
	$project->story_url = $textData['story_url'];
	$project->update();
	if($project->update()){
		$insertedID = $project->id;
		
		
		$project_country_map = \Chemicals\Backend\Models\ProjectCountryMap::find("project_id = ".$id);
	    $project_country_map->delete();
		$project_keyword_map = \Chemicals\Backend\Models\ProjectKeyWordMap::find("project_id = ".$id);
	    $project_keyword_map->delete();
		$project_region_map = \Chemicals\Backend\Models\ProjectRegionMap::find("project_id = ".$id);
	    $project_region_map->delete();
		
		foreach($textData['country_iso_2'] as $country_iso){
		$project_country_map = new \Chemicals\Backend\Models\ProjectCountryMap();
		$project_country_map->project_id = $insertedID;
		$project_country_map->country_iso_2 = $country_iso;
		$project_country_map->save();
		}
		
		foreach($textData['keyword'] as $keyword){
		$project_keyword_map = new \Chemicals\Backend\Models\ProjectKeyWordMap();
		$project_keyword_map->project_id = $insertedID;
		$project_keyword_map->keyword = $keyword;
		$project_keyword_map->save();
		}
		
		foreach($textData['region_id'] as $regionID){
		$project_region_map = new \Chemicals\Backend\Models\ProjectRegionMap();
		$project_region_map->project_id = $insertedID;
		$project_region_map->region_id = $regionID;
		$project_region_map->save();
		}
		
 	}else{
		$insertedID = 0;
	}
   
    $app->response->setContent( 
		\json_encode(
			[ 
			   'status' => 'OK',
			   'insertedID' => $insertedID, 
			   'data'   => $textData
			]
		)
    );
	

    return $app->response;
	

});


/**$app->post('/country', function () use ($app) {

    $textData = $_POST;
	
	//var_dump($_POST);
	//exit;

    $country = new \Chemicals\Backend\Models\Country(); 

	$country->iso_2 = $textData['iso_2'];
	$country->iso_3 = $textData['iso_3'];
	$country->name = $textData['name'];
	$country->latitude = $textData['latitude'];
	$country->longitude = $textData['longitude'];
	//$country->zoom = $textData['zoom'];
	$country->save();
   
    $app->response->setContent( 
		\json_encode(
			[ 
			   'status' => 'OK',
			   'data'   => $textData,
			]
		)
    );


});**/

/**$app->put('/project', function () use ($app) {

    $textData = json_decode($_POST['text-data'], true);

    $project = \Chemicals\Backend\Models\Project:findFirst("id=" . $textData['id']);
   
    $app->response->setContent( 
		\json_encode(
			[ 
			   'status' => 'OK',
			   'data'   => $textData,
			]
		)
    );


});**/

$app->delete('/project/{id}', function ($id) use ($app) {

    //$textData = json_decode($_POST['text-data'], true);
	
	$project = \Chemicals\Backend\Models\Project::findFirst($id);
	$project->delete();
	
	if($project->delete()){
		$res = 'success';
	}else{
		$res = 'failed';
	}

     
   
    $app->response->setContent( 
		\json_encode(
			[ 
			   'status' => 'OK',
			   'data'   => $res,
			]
		)
    );
	
	 return $app->response ;


});


$app->delete('/project_country_map/{id}', function ($id) use ($app) {

    //$textData = json_decode($_POST['text-data'], true);
	
	$project_country_map = \Chemicals\Backend\Models\ProjectCountryMap::find("project_id = ".$id);
	$project_country_map->delete();
	
	if($project_country_map->delete()){
		$res = 'success';
	}else{
		$res = 'failed';
	}

     
   
    $app->response->setContent( 
		\json_encode(
			[ 
			   'status' => 'OK',
			   'data'   => $res,
			]
		)
    );
	
	 return $app->response ;


});

$app->delete('/project_keyword_map/{id}', function ($id) use ($app) {

    //$textData = json_decode($_POST['text-data'], true);
	
	$project_keyword_map = \Chemicals\Backend\Models\ProjectKeyWordMap::find("project_id = ".$id);
	$project_keyword_map->delete();
	
	if($project_keyword_map->delete()){
		$res = 'success';
	}else{
		$res = 'failed';
	}

     
   
    $app->response->setContent( 
		\json_encode(
			[ 
			   'status' => 'OK',
			   'data'   => $res,
			]
		)
    );
	
	 return $app->response ;


});

$app->delete('/project_region_map/{id}', function ($id) use ($app) {

    //$textData = json_decode($_POST['text-data'], true);
	
	$project_region_map = \Chemicals\Backend\Models\ProjectRegionMap::find("project_id = ".$id);
	$project_region_map->delete();
	
	if($project_region_map->delete()){
		$res = 'success';
	}else{
		$res = 'failed';
	}

     
   
    $app->response->setContent( 
		\json_encode(
			[ 
			   'status' => 'OK',
			   'data'   => $res,
			]
		)
    );
	
	 return $app->response ;


});






$app->notFound(function () use ($app) {
  
  $app->response->setStatusCode(404, "Not Found")->sendHeaders();
  echo 'Not Found';
  
});
