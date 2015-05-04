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
		$data->on($on)->first();
		if($data)
		{
			if($data->start <= $time)
			{
				$data->fill([
									'end'			=> $time,
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
								'start'			=> $time,
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
