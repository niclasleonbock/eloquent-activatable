<?php
use Illuminate\Database\Seeder;

class TopicTableSeeder extends Seeder
{
    public function run()
    {
        $title = 'Lorem ipsum dolor sit amet';

        $topics = [
            [ 'title' => $title ],
            [ 'title' => $title ],
            [ 'title' => $title ],
        ];

        Topic::insert($topics);

        $firstTopic = Topic::withDeactivated()->first();
        $firstTopic->activate();
        $firstTopic->save();
    }
}

