<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLinksTable extends Migration
{
      /**
     * Run the migrations.
     *
     * @return void
     */
    protected $table_name;

    public function __construct()
    {

        // read the json file to get the values
        $this->table_name = "links";
    }

    public function up()
    {

        Schema::create($this->table_name, function (Blueprint $table) {
            $table->uuid( "id")
                ->primary()
                ->unique();
            $table->datetime( "date")
                ->useCurrent();
            $table->boolean( "deleted")
            ->default(0);
            $table->foreignUuid( "relation");
            $table->foreignUuid( "from");
            $table->foreignUuid( "to");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->table_name);
    }
}
