<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTopicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = $this->getTable();

        Schema::create($table, function ($table) {
            $table->increments('id');
            $table->string('title', 32);
            $table->datetime('activated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table = $this->getTable();

        Schema::drop($table);
    }

    /**
     * Get the table for the migrations.
     *
     * @return string   Table name
     */
    protected function getTable()
    {
        return 'topics';
    }
}

