# Log Overview

Package contain migrations, seeds and models for Log (HR). Documentation will be written in wiki.

# Installation

composer.json:
```
	"thunderid/log": "dev-master"
```

run
```
	composer update
```

```
	composer dump-autoload
```

# Usage

service provider
```
'ThunderID\Log\LogServiceProvider'
```

migration
```
  php artisan migrate --path=vendor/thunderid/log/src/migrations
```

seed (run in mac or linux)
```
  php artisan db:seed --class=ThunderID\\Log\\seeds\\DatabaseSeeder
```

seed (run in windows)
```
  php artisan db:seed --class='\ThunderID\Log\seeds\DatabaseSeeder'
```

# Developer Notes for UI
## Table Log

/* ----------------------------------------------------------------------
 * Document Model:
 * 	ID 								: Auto Increment, Integer, PK
 * 	person_id 						: Foreign Key From Person, Integer, Required
 * 	name 		 					: Required max 255
 * 	on 		 						: Required, Datetime
 * 	pc 			 					: Required max 255
 *	created_at						: Timestamp
 * 	updated_at						: Timestamp
 * 	deleted_at						: Timestamp
 * 
/* ----------------------------------------------------------------------
 * Document Relationship :
 * 	//other package
 	1 Relationship belongsTo 
	{
		Person
	}

/* ----------------------------------------------------------------------
 * Document Fillable :
 * 	name
	on
	pc

/* ----------------------------------------------------------------------
 * Document Observe :
 	delete 							: cannot delete row
 	save 							: depend on business model (create p.logs)

/* ----------------------------------------------------------------------
 * Document Searchable :
 * 	id 								: Search by id, parameter => string, id
	personid 						: Search by person_id, parameter => string, person_id
	name 	 						: Search by name, parameter => string, name
	withattributes					: Search with relationship, parameter => array of relationship (ex : ['chart', 'person'], if relationship is belongsTo then return must be single object, if hasMany or belongsToMany then return must be plural object)

/* ----------------------------------------------------------------------


## Table ProcessLog

/* ----------------------------------------------------------------------
 * Document Model:
 * 	ID 								: Auto Increment, Integer, PK
 * 	person_id 						: Foreign Key From Person, Integer, Required
 * 	work_id 						: Foreign Key From Work, Integer, Required
 * 	name 		 					: Required max 255
 * 	on 		 						: Required, Date
 * 	start 		 					: Required, Time
 * 	end 		 					: Time
 * 	schedule_start 		 			: Required, Time
 * 	schedule_end 		 			: Required, Time
 * 	margin_start 		 			: Double
 * 	margin_end 		 				: Double
 * 	total_idle 		 				: Double
 *	created_at						: Timestamp
 * 	updated_at						: Timestamp
 * 	deleted_at						: Timestamp
 * 
/* ----------------------------------------------------------------------
 * Document Relationship :
 * 	//other package
 	2 Relationships belongsTo 
	{
		Person
		Work
	}

/* ----------------------------------------------------------------------
 * Document Fillable :
 * 	name
	on
	start
	end
	schedule_start
	schedule_end
	margin_start
	margin_end
	total_idle

/* ----------------------------------------------------------------------
 * Document Observe :
 	delete 							: cannot delete row

/* ----------------------------------------------------------------------
 * Document Searchable :
 * 	id 								: Search by id, parameter => string, id
	personid 						: Search by person_id, parameter => string, person_id
	ondate 							: Search by process log date, parameter => if array looking for range, if single looking for exact
	late 							: Search by process margin_start < 0, parameter => not counting
	ontime 							: Search by process margin_start >= 0, parameter => not counting
	earlier 						: Search by process margin_end < 0, parameter => not counting
	overtime 						: Search by process margin_start > 0, parameter => not counting
	global 							: Search by process log date, parameter => date, on, count sum and average of works, a user a row
	local 							: Search by process log date, parameter => date, on, count only workhour per row, a user many rows
	charttag 						: Search by chart tag, parameter => string, tag
	branchname 						: Search by branch name, parameter => string, name
	orderworkhour 					: Order by process log sum workhour, parameter => desc or asc
	orderavgworkhour 				: Order by process log avd workhour, parameter => desc or asc
	withattributes					: Search with relationship, parameter => array of relationship (ex : ['chart', 'person'], if relationship is belongsTo then return must be single object, if hasMany or belongsToMany then return must be plural object)

/* ----------------------------------------------------------------------