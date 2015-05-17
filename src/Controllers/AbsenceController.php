<?php namespace ThunderID\Log\Controllers;

use \App\Http\Controllers\Controller;
use \ThunderID\Log\Models\Log;
use \ThunderID\Log\Models\ErrorLog;
use \ThunderID\Person\Models\Person;
use \ThunderID\Chauth\Models\Authentication;
use \ThunderID\Organisation\Models\Organisation;
use \ThunderID\Commoquent\Getting;
use \ThunderID\Commoquent\Saving;
use \ThunderID\Commoquent\Deleting;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App, Response, Mail, DateTime, DateInterval, DatePeriod;

class AbsenceController extends Controller {

	protected $controller_name 		= 'absence';

	/**
	 * absence index
	 *
	 * @return void
	 * @author 
	 **/
	public function index($page = 1)
	{
		$per_page 					= 100;

		$begin 						= new DateTime( '- 1 week' );
		$ended 						= new DateTime( 'today'  );

		$interval 					= DateInterval::createFromDateString('1 day');
		$periods 					= new DatePeriod($begin, $interval, $ended);
		
		foreach ($periods as $key => $value) 
		{
			$search['fullschedule']	= $value->format('Y-m-d');
			$sort 					= ['persons.created_at' => 'desc'];
			$results 				= $this->dispatch(new Getting(new Person, $search, $sort ,(int)$page, $per_page));
			$contents 				= json_decode($results);

			if(!$contents->meta->success)
			{
				App::abort(404);
			}
			
			$data[$value->format('Y-m-d')] = json_decode(json_encode($contents->data), true);
		}

		$results 					= $this->dispatch(new Getting(new Authentication, ['menuid' => 8, 'email' => true], ['chart_id' => 'asc'],1, 100));
		$contents 					= json_decode($results);

		if(!$contents->meta->success)
		{
			App::abort(404);
		}

		$data2 						= json_decode(json_encode($contents->data), true);

		foreach ($data2 as $key => $value) 
		{
			foreach ($value['chart']['works'] as $key2 => $value2) 
			{
				foreach ($value2['contacts'] as $key3 => $value3) 
				{
					if(strtolower($value3['item'])=='email')
					{
						if(isset($email) && !in_array($value3['value'], $mails))
						{
							$mails[]= $value3['value'];
							$email[]= ['email' => $value3['value'],'name' => $value2['name']];
						}
						elseif(!isset($email))
						{
							$mails[]= $value3['value'];
							$email[]= ['email' => $value3['value'],'name' => $value2['name']];
						}
					}
				}
			}
		}

		if(isset($email))
		{
			foreach($email as $key => $value)
			{
				Mail::send('emails.absence', ['data' => $data], function($message) use($value)
				{
				    $message->to($value['email'], $value['name'])->subject('Report Absence '.date('d-m-Y', strtotime('- 1 week')).' to '.date('d-m-Y', strtotime('today')));
				});
			}
		}
	}

	public function test($page = 1)
	{
		$per_page 					= 100;

		$begin 						= new DateTime( '- 1 week' );
		$ended 						= new DateTime( 'today'  );

		$interval 					= DateInterval::createFromDateString('1 day');
		$periods 					= new DatePeriod($begin, $interval, $ended);
		
		foreach ($periods as $key => $value) 
		{
			$search['fullschedule']	= $value->format('Y-m-d');
			$sort 					= ['persons.created_at' => 'desc'];
			$results 				= $this->dispatch(new Getting(new Person, $search, $sort ,(int)$page, $per_page));
			$contents 				= json_decode($results);

			if(!$contents->meta->success)
			{
				App::abort(404);
			}
			
			$data[$value->format('Y-m-d')] = json_decode(json_encode($contents->data), true);
		}

		$results 					= $this->dispatch(new Getting(new Authentication, ['menuid' => 8, 'email' => true], ['chart_id' => 'asc'],1, 100));
		$contents 					= json_decode($results);

		if(!$contents->meta->success)
		{
			App::abort(404);
		}

		$data2 						= json_decode(json_encode($contents->data), true);

		foreach ($data2 as $key => $value) 
		{
			foreach ($value['chart']['works'] as $key2 => $value2) 
			{
				foreach ($value2['contacts'] as $key3 => $value3) 
				{
					if(strtolower($value3['item'])=='email')
					{
						if(isset($email) && !in_array($value3['value'], $mails))
						{
							$mails[]= $value3['value'];
							$email[]= ['email' => $value3['value'],'name' => $value2['name']];
						}
						elseif(!isset($email))
						{
							$mails[]= $value3['value'];
							$email[]= ['email' => $value3['value'],'name' => $value2['name']];
						}
					}
				}
			}
		}

		return view('emails.absence')->with('data', $data);
	}
}