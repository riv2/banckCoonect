<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SmartIDController extends Controller
{
    public function index()
    {
	    
	   $file = $_GET['file'];
	   $type = $_GET['type'];
	   
    
	$SmartID = shell_exec('php '.__DIR__.'/SmartID/SmartID.php '.__DIR__.'/SmartID/testdata/'.$file.' '.__DIR__.'/SmartID/bundle_kaz_mrz_server.zip '.$type.'');
	
	//cpp
	//$SmartID = shell_exec(__DIR__.'/SmartID/smartid_sample '.__DIR__.'/SmartID/testdata/'.$file.' '.__DIR__.'/SmartID/bundle_kaz_mrz_server.zip '.$type.'');
	
    	
    	return $SmartID;
    }
}
	
	
  