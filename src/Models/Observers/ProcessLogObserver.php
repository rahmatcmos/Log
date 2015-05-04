<?php namespace ThunderID\Log\Models\Observers;

use DB, Validator;

/* ----------------------------------------------------------------------
 * Event:
 * 	Creating						
 * 	Saving						
 * 	Updating						
 * 	Deleting						
 * ---------------------------------------------------------------------- */

class ProcessLogObserver 
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

	public function updating($model)
	{
		//temporary
		$model['errors'] 	= ['Tidak dapat mengubah process log.'];

		return false;
	}

	public function deleting($model)
	{
		$model['errors'] 	= ['Tidak dapat menghapus process log.'];

		return false;
	}
}
