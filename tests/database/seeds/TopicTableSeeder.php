<?php
use Illuminate\Database\Seeder;

class TopicTableSeeder extends Seeder
{
    public function run()
    {
        $table = $this->getTable();

        $title = 'Lorem ipsum dolor sit amet';

        $topic = new Topic([
            'title'     => $title,
        ]);

        $topic->save();
    }

    /**
     * Get the table for the migrations.
     *
     * @return string   Table name
     */
    protected function getTable()
    {
        return 'topics';
    }
}

