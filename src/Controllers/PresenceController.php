<?php namespace ThunderID\Log\Controllers;

use \App\Http\Controllers\Controller;
use \ThunderID\Log\Models\Log;
use \ThunderID\Log\Models\ErrorLog;
use \ThunderID\Person\Models\Person;
use \ThunderID\Organisation\Models\Organisation;
use \ThunderID\Commoquent\Getting;
use \ThunderID\Commoquent\Saving;
use \ThunderID\Commoquent\Deleting;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App, Response;

class PresenceController extends Controller {

	protected $controller_name 		= 'presence';

	/**
	 * login form
	 *
	 * @return void
	 * @author 
	 **/
	public function store()
	{
		$attributes 							= Input::only('application', 'log');
		if(!$attributes['application'])
		{
			return Response::json(['message' => 'Server Error'], 500);
		}

		$api 									= $attributes['application']['api'];
		if($api['client']!='123456789' || $api['secret']!='123456789')
		{
			return Response::json(['message' => 'Not Found'], 404);
		}

		if(!$attributes['log'])
		{
			return Response::json(['message' => 'Server Error'], 500);
		}

		DB::beginTransaction();

		if(isset($attributes['log']))
		{
			$attributes['log']					= (array)$attributes['log'];
			foreach ($attributes['log'] as $key => $value) 
			{
				$log['name']					= strtolower('presence');
				$log['on']						= date("Y-m-d H:i:s", strtotime($value[1]));
				$log['pc']						= $value[2];

				$data 							= $this->dispatch(new Getting(new Person, ['email' => $value[0]], [] ,1, 1));
				$person 						= json_decode($data);
				if(!$person->meta->success)
				{
					$log['email']				= $value[0];
					$log['message']				= json_encode($person->meta->errors);
					$saved_error_log 			= $this->dispatch(new Saving(new ErrorLog, $log, null, new Organisation, 1));
				}
				else
				{
					$saved_log 					= $this->dispatch(new Saving(new Log, $log, null, new Person, $person->data->id));
					$is_success_2 				= json_decode($saved_log);
					if(!$is_success_2->meta->success)
					{
						$log['email']			= $value[0];
						$log['message']			= json_encode($is_success_2->meta->errors);
						$saved_error_log 		= $this->dispatch(new Saving(new ErrorLog, $log, null, new Organisation, 1));
					}
				}
			}
		}

		DB::commit();

		return Response::json(['message' => 'Sukses'], 200);
	}
}