<?php namespace ThunderID\Log\Models\Observers;

use DB, Validator;
use ThunderID\Log\Models\ProcessLog;
use ThunderID\Log\Models\Log;
use ThunderID\Person\Models\Person;

/* ----------------------------------------------------------------------
 * Event:
 * 	Creating						
 * 	Saving						
 * 	Updating						
 * 	Deleting						
 * ---------------------------------------------------------------------- */

class ProcessingLogObserver 
{
	public function updating($model)
	{
		$on 					= date("Y-m-d", strtotime($model['attributes']['on']));
		$time 					= date("H:i:s", strtotime($model['attributes']['on']));
		$data 					= new ProcessLog;
		$data 					= $data->ondate($on)->first();
		if($data->id)
		{
			if($data->start <= $time)
			{
				//consider to count idle
				$data->fill([
									'end'			=> $time,
									'margin_start'	=> strtotime('s',$data->start - $data->schedule_start),
									'margin_end'	=> strtotime('s',$data->end - $data->schedule_end),
							]
				);
			}
			if (!$data->save())
			{
				$model['errors'] = $data->getError();
				return false;
			}

			return true;

		}
		else
		{
			$data 				= new ProcessLog;
			$person 			= Person::find($model['attributes']['person_id']);
			$data->fill([
								'name'			=> 'Login (Temporary)',
								'on'			=> $on,
								'start'			=> $time,
								'schedule_start'=> '08:00:00',
								'schedule_end'	=> '16:00:00',
						]
			);

			$data->Person()->associate($person);
			if (!$data->save())
			{
				$model['errors'] = $data->getError();
				return false;
			}

			return true;
		}
	}
}
