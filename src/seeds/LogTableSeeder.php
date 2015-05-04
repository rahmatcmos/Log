<?php namespace ThunderID\Log\seeds;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use ThunderID\Log\Models\Log;
use ThunderID\Person\Models\Person;
use \Faker\Factory, Illuminate\Support\Facades\DB;

class LogTableSeeder extends Seeder
{
	function run()
	{

		DB::table('logs')->truncate();
		$faker 										= Factory::create();
		$total_persons  							= Person::count();
		$logs 										= ['login', 'logout','sleep', 'idle', 'lock', 'working'];
		$pcs 										= ['redhat', 'ubuntu', 'debian', 'mint', 'centos', '7', 'xp'];
		try
		{
			foreach(range(1, count($total_persons)) as $index)
			{
				$person 							= Person::find($index);

				foreach(range(1, 8) as $index2)
				{
					if($index2==1)
					{
						$state 						= 1;
						$time 						= 'hour';
					}
					elseif($index2==8)
					{
						$state						= 2;
						$time 						= 'hours';
					}
					else
					{
						$state 						= rand(2,5);
						$time 						= 'hours';
					}

					$rand 							= rand(0,6);
					$data 							= new Log;
					$data->fill([
						'name'						=> $logs[$state],
						'on'						=> date("Y-m-d H:i:s", strtotime('+ '.$index2.' '.$time)),
						'pc'						=> $pcs[$rand],
					]);

					$data->Person()->associate($person);

					if (!$data->save())
					{
						print_r($data->getError());
						exit;
					}
				}
			} 
		}
		catch (Exception $e) 
		{
    		echo 'Caught exception: ',  $e->getMessage(), "\n";
		}	
	}
}