<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->string('name');
            $table->string('native');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_rtl')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
        
        // Insert default languages from config
        $this->seedDefaultLanguages();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('languages');
    }
    
    /**
     * Seed default languages from the config file
     */
    private function seedDefaultLanguages()
    {
        $supportedLocales = config('laravellocalization.supportedLocales');
        $order = 0;
        
        foreach ($supportedLocales as $code => $properties) {
            DB::table('languages')->insert([
                'code' => $code,
                'name' => $properties['name'],
                'native' => $properties['native'],
                'is_active' => true,
                'is_rtl' => $properties['script'] === 'Arab',
                'sort_order' => $order++,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}; 