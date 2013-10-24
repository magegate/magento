<?php

use Illuminate\Database\Migrations\Migration;

class Event extends Migration {

    public $mage_model_length = 48;
    public $mage_event_length = 8;

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('event', function(Illuminate\Database\Schema\Blueprint $table)
        {
            $table->string('event',$this->mage_event_length);
            $table->string('model',$this->mage_model_length);
            $table->integer('id');
            $table->timestamps();

            $table->primary(array('event','model','id'));
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('event');
	}

}