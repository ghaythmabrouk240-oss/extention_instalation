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
            // Equipment identification
            $table->string('angio_manufacturer')->nullable();
            $table->string('angio_model')->nullable();
            $table->string('angio_serial')->nullable();
            
            // Safety status ENUMs
            $table->enum('radiation_shielding_status', ['conforme', 'a_verifier', 'non_conforme'])->nullable();
            $table->enum('lead_glass_status', ['conforme', 'a_verifier', 'non_conforme'])->nullable();
            $table->enum('ceiling_support_status', ['conforme', 'a_verifier', 'non_conforme'])->nullable();
            $table->enum('emergency_equipment_status', ['conforme', 'a_verifier', 'non_conforme'])->nullable();
            $table->enum('access_control_status', ['conforme', 'a_verifier', 'non_conforme'])->nullable();
            
            // Boolean fields
            $table->boolean('dose_monitoring_available')->default(false);
            
            // Additional info
            $table->string('hvac_info')->nullable();
            
            // Test status
            $table->enum('acceptance_test_status', ['conforme', 'a_verifier', 'non_conforme'])->nullable();
            
            // Dates
            $table->date('installation_date')->nullable();
            $table->date('warranty_end_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profil_cat_labs', function (Blueprint $table) {
            $table->dropColumn([
                'angio_manufacturer',
                'angio_model',
                'angio_serial',
                'radiation_shielding_status',
                'lead_glass_status',
                'ceiling_support_status',
                'emergency_equipment_status',
                'access_control_status',
                'dose_monitoring_available',
                'hvac_info',
                'acceptance_test_status',
                'installation_date',
                'warranty_end_date',
            ]);
        });
    }
};
