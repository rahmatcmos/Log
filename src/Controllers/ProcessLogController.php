<?php namespace ThunderID\Log\Controllers;

use \App\Http\Controllers\Controller;
use \ThunderID\Log\Models\ProcessLog;
use \ThunderID\Person\Models\Person;
use \ThunderID\Commoquent\Getting;
use \ThunderID\Commoquent\Saving;
use \ThunderID\Commoquent\Deleting;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App, Response;

class ProcessLogController extends Controller {

	/**
	 * login form
	 *
	 * @return void
	 * @author 
	 **/
	public function index($page = 1, $search = null, $sort = null, $per_page = 12)
	{
		$contents 								= $this->dispatch(new Getting(new ProcessLog, $search, $sort ,(int)$page, $per_page));
		
		return $contents;
	}
}