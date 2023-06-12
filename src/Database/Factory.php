<?php

namespace LaraFiler\Database;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use Illuminate\Database\Migrations\MigrationRepositoryInterface;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Filesystem\Filesystem;

class Factory
{
    public function runMigrations()
    {
        $capsule = new Capsule;
        $capsule->addConnection([
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'database' => 'test_package',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
        ]);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        if (!Capsule::schema()->hasTable('larafm_documents')) {
            Capsule::schema()->create('larafm_documents', function (Blueprint $table) {
                $table->id();
                $table->string('filename');
                $table->string('slug');
                $table->string('path');
                $table->integer('size');
                $table->string('mimetype');
                $table->string('type');
                $table->string('extension');
                $table->string('thumbs')->nullable()->default('');
                $table->string('group_name')->nullable();
                $table->bigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }

    }
}