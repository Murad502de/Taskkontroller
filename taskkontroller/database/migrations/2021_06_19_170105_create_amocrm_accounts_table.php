<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAmocrmAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('amocrm_accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string( 'subdomain' );
            $table->integer( 'code' );
            $table->text( 'access_token' );
            $table->string( 'client_id' );
            $table->string( 'client_secret' );
            $table->string( 'redirect_uri' );
            $table->text( 'refresh_token' );
            $table->bigInteger( 'when_expires' );
            $table->text( 'user_statuses' );
            $table->text( 'settings' );
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
        Schema::dropIfExists('amocrm_accounts');
    }
}
