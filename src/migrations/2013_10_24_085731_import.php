<?php

use Illuminate\Database\Migrations\Migration;

class Import extends Migration {

    public $host_length = 32;
    public $status_length = 8;

    /**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('import', function(Illuminate\Database\Schema\Blueprint $table)
        {
            $table->increments('id');
            $table->string('host',$this->host_length);
            $table->string('name',$this->host_length);
            $table->string('status',$this->status_length);
            $table->integer('number');
            $table->integer('queued')->default(0);
            $table->integer('finish')->default(0);
            $table->integer('faulty')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(array('host'));
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('import');
	}

}