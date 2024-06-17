<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTraxXapiActivityProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trax_xapi_activity_profiles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('profile_id');
            $table->string('activity_id', 348)->index();
            $table->json('data');
            $table->string('timestamp');
            $table->timestamps();

            // Owner relation
            $table->unsignedBigInteger('owner_id')->nullable()->index();
            $table->foreign('owner_id')
                ->references('id')
                ->on('trax_owners')
                ->onDelete('cascade');

            // Unicity.
            $table->unique(['activity_id', 'profile_id', 'owner_id'], 'trax_xapi_activity_profiles_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trax_xapi_activity_profiles', function (Blueprint $table) {
            $table->dropForeign('trax_xapi_activity_profiles_owner_id_foreign');
        });
        Schema::dropIfExists('trax_xapi_activity_profiles');
    }
}
