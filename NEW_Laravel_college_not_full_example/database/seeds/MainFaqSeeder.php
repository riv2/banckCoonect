<?php

use Illuminate\Database\Seeder;

class MainFaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $category = new \DevDojo\Chatter\Models\Category();

        $category->parent_id = 0;
        $category->order = 1;
        $category->name = 'Общее описание';
        $category->slug = 'global_description_hidden';
        $category->color = '';
        $category->hidden = true;
        $category->save();

        $discussion = new \DevDojo\Chatter\Models\Discussion();
        $discussion->chatter_category_id = $category->id;
        $discussion->title = 'F.A.Q.';
        $discussion->user_id = 1;
        $discussion->topic = true;
        $discussion->color = null;
        $discussion->slug = 'global_faq';
        $discussion->save();

        $post = new \DevDojo\Chatter\Models\Post();
        $post->chatter_discussion_id = $discussion->id;
        $post->user_id = 1;
        $post->body = 'Часто задаваемые вопросы';
        $post->save();
    }
}
