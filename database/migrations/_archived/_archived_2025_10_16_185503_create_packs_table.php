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
        Schema::create('packs', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('marketing_title');
            $table->text('tagline');
            $table->decimal('price_eur', 8, 2);
            $table->decimal('price_usd', 8, 2);
            $table->decimal('original_price_eur', 8, 2)->nullable();
            $table->decimal('original_price_usd', 8, 2)->nullable();
            $table->longText('description')->nullable();
            $table->integer('workflows_count')->default(0);
            $table->string('complexity')->nullable(); // Débutant, Intermédiaire, Avancé
            $table->string('category')->nullable(); // Crypto, IA, Marketing, etc.
            $table->json('features')->nullable(); // Liste des features du pack
            $table->json('benefits')->nullable(); // Liste des bénéfices
            $table->json('requirements')->nullable(); // APIs/Services requis
            $table->string('folder_path'); // Chemin vers le dossier des workflows
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('sales_count')->default(0);
            $table->integer('views_count')->default(0);
            $table->timestamps();

            $table->index('slug');
            $table->index('is_active');
            $table->index('is_featured');
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packs');
    }
};
