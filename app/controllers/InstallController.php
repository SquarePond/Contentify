<?php

class InstallController extends BaseController {

	public function index() 
	{
		$this->createDatabase();
		//$this->createUserGroups();
	}

	public function createDatabase()
	{
		/*
		 * Notice: The default length of strings is 255 chars.
		 */

		//$this->create('newscats', function($table)
		//{
		//	$table->string('image')->nullable();
		//});

		$this->create('news', function($table)
		{
			$table->text('intro')->nullable();
			$table->text('text')->nullable();
			$table->boolean('published')->default(false);
			$table->boolean('internal')->default(false);
			$table->boolean('allow_comments')->default(false);
		}, ['newscat']);

		return; // DEBUG

		$this->create('games', function($table)
		{
			$table->string('short', 6)->nullable();
			$table->string('image')->nullable();
		});

		$this->create('contact_messages', function($table)
		{
			$table->string('username', 30);
			$table->string('email');
			$table->text('text');
			$table->string('ip');
			$table->boolean('new')->default(true);
		});
	}

	/**
	 * Create user permision groups
	 * @return void
	 */
	public function createUserGroups()
	{
		Sentry::createGroup(array(
        	'name'        => 'Visitors',
        	'permissions' => array()
        ));

	    Sentry::createGroup(array(
	        'name'        => 'Users',
        	'permissions' => array(
        		'frontend'	=> true
        	)
	    ));

	    Sentry::createGroup(array(
	        'name'        => 'Members',
        	'permissions' => array(
        		'frontend'	=> true,
        		'internal'	=> true,
        	)
	    ));

	    Sentry::createGroup(array(
	        'name'        => 'Admins',
        	'permissions' => array(
        		'frontend'	=> true,
        		'internal'	=> true,
        		'backend'	=> true
        	)
		));

	    Sentry::createGroup(array(
	        'name'        => 'Super-Admins',
        	'permissions' => array(
        		'frontend'	=> true,
        		'internal'	=> true,
        		'backend'	=> true
        	)
	    ));
	}

	/**
	 * Helper functions. Creates a database table.
	 * 
	 * @param  string  	$tableName
	 * @param  Closure 	$tableRows
	 * @param  array 	$foreignKeys
	 * @param  boolean 	$isContentObject
	 * @return void
	 */
	private function create($tableName, Closure $tableRows, $foreignKeys = array(), $isContentObject = true)
	{
		/*
		 * Delete existing table:
		 */
		Schema::dropIfExists($tableName);

		/*
		 * Add ID:
		 */
		Schema::create($tableName, function($table)
		{
			$table->engine = 'InnoDB'; // Since we create the table here we ensure InnoDB is used as storage engine

			$table->increments('id'); // Primary key (unique, auto-increment)
		});

		/*
		 * Add the table rows:
		 */
		Schema::table($tableName, $tableRows);

		/*
		 * Add the content object attributes:
		 */
		if ($isContentObject) {
			Schema::table($tableName, function($table)
			{
				/*
				 * We can use after() to insert he title attribute right after id.
				 * But after() only workws with MySQL databases so we have to check that:
				 */
				if (strtolower(DB::connection()->getDriverName()) == 'mysql') {
					$table->string('title', 70)->after('id');
				} else {
					$table->string('title', 70);
				}
				$table->integer('creator_id')->unsigned()->default(0);
				$table->foreign('creator_id')->references('id')->on('users');
				$table->integer('updater_id')->unsigned()->default(0);
				$table->foreign('updater_id')->references('id')->on('users');
				$table->integer('access_counter')->default(0);
				$table->timestamps();
			});

			/*
		 	 * Add soft deletes:
		 	 */ 
			Schema::table($tableName, function($table)
			{
				$table->softDeletes();
			});
		}

		/*
		 * Add the foreign keys:
		 */
		foreach ($foreignKeys as $foreignKey) {
			Schema::table($tableName, function($table) use ($foreignKey)
			{
				$table->integer($foreignKey.'_id')->unsigned();
            	$table->foreign($foreignKey.'_id')->references('id')->on(str_plural($foreignKey));
            });
		}
	}
}