<?php

use Illuminate\Database\Seeder;
use App\SecurityQuestion;

class SecurityQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //inject security questions
        $securityQuestions = [
            'What is your favorite sport ?',
            'Who is your childhood superhero ?',
            'What is your mother\'s maiden name ?',
            'What is your favorite color ?',
            'What is your favorite food ?',
        ];

        foreach ($securityQuestions as $question) {
            SecurityQuestion::create([
                'security_question' => $question
            ]);
        }
    }
}
