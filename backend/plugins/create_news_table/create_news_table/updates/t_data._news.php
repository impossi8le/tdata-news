<?php namespace CreateNewsTable\CreateNewsTable\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * TData.News Migration
 *
 * @link https://docs.octobercms.com/4.x/extend/database/structure.html
 */
// return new class extends Migration
// {
//     /**
//      * up builds the migration
//      */
//     public function up()
//     {
//         Schema::table('create_news_table_create_news_table_t_data._news', function(Blueprint $table) {
//             // ...
//         });
//     }

//     /**
//      * down reverses the migration
//      */
//     public function down()
//     {
//         Schema::table('create_news_table_create_news_table_t_data._news', function(Blueprint $table) {
//             // ...
//         });
//     }
// };
return new class extends Migration
{
    public function up()
    {
        Schema::create('tdata_news_news', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content');
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tdata_news_news');
    }
};
