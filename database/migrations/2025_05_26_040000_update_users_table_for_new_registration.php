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
        // Vérifier si les colonnes existent déjà avant de les ajouter
        if (!Schema::hasColumn('users', 'first_name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('first_name')->nullable()->after('name');
            });
        }

        if (!Schema::hasColumn('users', 'last_name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('last_name')->nullable()->after('first_name');
            });
        }

        if (!Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('username')->nullable()->after('last_name');
            });
        }

        if (!Schema::hasColumn('users', 'rgpd_accepted')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('rgpd_accepted')->default(false)->after('is_professional');
            });
        }

        if (!Schema::hasColumn('users', 'quiz_completed_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('quiz_completed_at')->nullable()->after('level_n8n');
            });
        }

        // Mettre à jour les utilisateurs existants avec des valeurs par défaut
        $users = DB::table('users')->whereNull('username')->orWhere('username', '')->get();
        foreach ($users as $user) {
            $nameParts = explode(' ', $user->name, 2);
            $firstName = $nameParts[0];
            $lastName = isset($nameParts[1]) ? $nameParts[1] : $nameParts[0];
            $username = 'user_' . $user->id;
            
            DB::table('users')->where('id', $user->id)->update([
                'username' => $username,
                'first_name' => $firstName,
                'last_name' => $lastName
            ]);
        }

        // Ajouter la contrainte unique sur username
        if (Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('username')->unique()->change();
                $table->string('first_name')->nullable(false)->change();
                $table->string('last_name')->nullable(false)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'first_name')) {
                $table->dropColumn('first_name');
            }
            if (Schema::hasColumn('users', 'last_name')) {
                $table->dropColumn('last_name');
            }
            if (Schema::hasColumn('users', 'username')) {
                $table->dropColumn('username');
            }
            if (Schema::hasColumn('users', 'rgpd_accepted')) {
                $table->dropColumn('rgpd_accepted');
            }
            if (Schema::hasColumn('users', 'quiz_completed_at')) {
                $table->dropColumn('quiz_completed_at');
            }
        });
    }
};
