<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityLogsTable extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->bigIncrements('id');

            /*
             * User yang melakukan aktivitas.
             * Nullable karena aktivitas bisa berasal dari system/console.
             */
            $table->unsignedBigInteger('user_id')->nullable();

            /*
             * created
             * updated
             * deleted
             * restored
             * custom
             * login
             * logout
             */
            $table->string('action', 50);

            /*
             * Nama model/table yang terkena aktivitas.
             *
             * Contoh:
             * berita
             * pengaduan
             * users
             * pages
             */
            $table->string('module', 100)->nullable();

            /*
             * Kalimat yang ditampilkan di dashboard.
             */
            $table->text('description')->nullable();

            /*
             * Polymorphic subject.
             */
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();

            /*
             * Data perubahan / informasi tambahan.
             */
            $table->json('properties')->nullable();

            /*
             * Informasi audit.
             */
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();

            $table->index('user_id');
            $table->index('action');
            $table->index('module');
            $table->index('subject_id');
            $table->index('created_at');

            $table->index([
                'subject_type',
                'subject_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
}