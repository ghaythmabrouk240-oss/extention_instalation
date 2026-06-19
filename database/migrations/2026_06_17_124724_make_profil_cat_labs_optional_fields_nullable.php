<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('profil_cat_labs', function (Blueprint $table) {
            $table->string('table_patient')->nullable()->change();
            $table->string('injecteur')->nullable()->change();
            $table->string('moniteurs')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profil_cat_labs', function (Blueprint $table) {
            $table->string('table_patient')->nullable(false)->change();
            $table->string('injecteur')->nullable(false)->change();
            $table->string('moniteurs')->nullable(false)->change();
        });
    }
};
