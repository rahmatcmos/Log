<?php namespace ThunderID\Log\Models\Observers;

use DB, Validator;
use ThunderID\Log\Models\ProcessLog;
use ThunderID\Person\Models\Person;

/* ----------------------------------------------------------------------
 * Event:
 * 	Creating						
 * 	Saving						
 * 	Updating						
 * 	Deleting						
 * ---------------------------------------------------------------------- */

class LogObserver 
{
	public function creating($model)
	{
		//
	}

	public function saving($model)
	{
		$validator 				= Validator::make($model['attributes'], $model['rules']);

		if ($validator->passes())
		{
			return true;
		}
		else
		{
			$model['errors'] 	= $validator->errors();

			return false;
		}
	}

	public function saved($model)
	{
		$on 					= date("Y-m-d", strtotime($model['attributes']['on']));
		$time 					= date("H:i:s", strtotime($model['attributes']['on']));
		$data 					= new ProcessLog;
		$data->ondate($on)->first();
		if($data->id)
		{
			if($data->start <= $time)
			{
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
								'schedule_start'=> '08.00',
								'schedule_end'	=> '16.00',
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

	public function updating($model)
	{
		$model['errors'] 	= ['Tidak dapat mengubah data log.'];

		return false;
	}

	public function deleting($model)
	{
		$model['errors'] 	= ['Tidak dapat menghapus data log.'];

		return false;
	}
}
