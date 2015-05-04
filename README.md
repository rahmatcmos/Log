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
 * Document Searchable :
 * 	id 								: Search by id, parameter => string, id
	personid 						: Search by person_id, parameter => string, person_id
	withattributes					: Search with relationship, parameter => array of relationship (ex : ['chart', 'person'], if relationship is belongsTo then return must be single object, if hasMany or belongsToMany then return must be plural object)

/* ----------------------------------------------------------------------


## Table ProcessLog

/* ----------------------------------------------------------------------
 * Document Model:
 * 	ID 								: Auto Increment, Integer, PK
 * 	person_id 						: Foreign Key From Person, Integer, Required
 * 	name 		 					: Required max 255
 * 	on 		 						: Required, Date
 * 	start 		 					: Required, Time
 * 	end 		 					: Required, Time
 * 	is_overtime 		 			: Boolean
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
	start
	end
	is_overtime

/* ----------------------------------------------------------------------
 * Document Searchable :
 * 	id 								: Search by id, parameter => string, id
	personid 						: Search by person_id, parameter => string, person_id
	on 								: Search by process log date, parameter => date, on
	withattributes					: Search with relationship, parameter => array of relationship (ex : ['chart', 'person'], if relationship is belongsTo then return must be single object, if hasMany or belongsToMany then return must be plural object)

/* ----------------------------------------------------------------------