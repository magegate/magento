<?php

use Illuminate\Database\Migrations\Migration;

class Entity extends Migration {

    public $host_length = 32;
    public $host_id_length = 32;
    public $mage_model_length = 32;
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('entity', function(Illuminate\Database\Schema\Blueprint $table)
        {
            $table->increments('id');
            $table->string('host',$this->host_length);
            $table->string('host_id',$this->host_id_length);
            $table->integer('mage_id')->nullable();
            $table->string('mage_model',$this->mage_model_length);
            $table->boolean('is_owner')->default(false);
            $table->timestamps();

            $table->index(array('host','host_id','mage_model'));
            $table->index(array('mage_id','mage_model'));
            $table->unique(array('host','host_id','mage_id','mage_model'));
            $table->unique(array('mage_id','mage_model','is_owner'));
        });
   }

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('entity');
	}

}