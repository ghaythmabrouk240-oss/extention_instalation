<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_installations', function (Blueprint $table) {
            $table->string('type_rapport')->nullable()->after('categorie');
            $table->text('description')->nullable()->after('statut');
            $table->string('fichier_path')->nullable()->after('reference_fichier');
            $table->string('fichier_original_name')->nullable()->after('fichier_path');
            $table->string('fichier_mime_type')->nullable()->after('fichier_original_name');
        });

        Schema::table('installations', function (Blueprint $table) {
            $table->text('calendar_note')->nullable()->after('actual_end_date');
        });
    }

    public function down(): void
    {
        Schema::table('document_installations', function (Blueprint $table) {
            $table->dropColumn([
                'type_rapport',
                'description',
                'fichier_path',
                'fichier_original_name',
                'fichier_mime_type',
            ]);
        });

        Schema::table('installations', function (Blueprint $table) {
            $table->dropColumn('calendar_note');
        });
    }
};
