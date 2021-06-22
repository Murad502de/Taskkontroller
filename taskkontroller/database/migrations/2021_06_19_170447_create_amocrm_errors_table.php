<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAmocrmErrorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('amocrm_errors', function (Blueprint $table) {
            $table->bigIncrements( 'id' );
            $table->string( 'subdomain' );
            $table->integer( 'code' );
            $table->text( 'out' );
            $table->text( 'exportData' );
            $table->string( 'link' );
            $table->text( 'headers' );
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
        Schema::dropIfExists('amocrm_errors');
    }
}
