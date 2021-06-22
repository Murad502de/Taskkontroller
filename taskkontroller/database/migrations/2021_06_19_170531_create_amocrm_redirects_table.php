<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAmocrmRedirectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('amocrm_redirects', function (Blueprint $table) {
            $table->bigIncrements( 'id' );
            $table->string( 'subdomain' );
            $table->string( 'client_id' );
            $table->text( 'auth_code' );
            $table->bigInteger( 'when_expires' );
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
        Schema::dropIfExists('amocrm_redirects');
    }
}
