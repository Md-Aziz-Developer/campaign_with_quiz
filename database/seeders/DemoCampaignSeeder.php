<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\NumberRule;
use App\Models\Option;
use App\Models\Question;
use App\Models\Response;
use App\Models\ResponseAnswer;
use App\Models\TextKeyword;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoCampaignSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        if (! $admin) {
            return;
        }

        $campaign = Campaign::create([
            'title' => 'Demo Customer Satisfaction Survey',
            'description' => '<p>This is a <strong>demo campaign</strong> to test the questionnaire flow: MCQ single, MCQ multi, text, number, and optional vs mandatory questions.</p>',
            'status' => 'published',
            'unique_slug' => 'demo-customer-satisfaction',
            'created_by' => $admin->id,
        ]);

        // Q1: MCQ Single (mandatory)
        $q1 = Question::create([
            'campaign_id' => $campaign->id,
            'question_text' => 'How would you rate our service?',
            'type' => Question::TYPE_MCQ_SINGLE,
            'is_mandatory' => true,
            'order' => 1,
        ]);
        $opt1 = Option::create(['question_id' => $q1->id, 'option_text' => 'Excellent', 'score' => 1.0, 'is_correct' => true, 'order' => 1]);
        $opt2 = Option::create(['question_id' => $q1->id, 'option_text' => 'Good', 'score' => 0.7, 'is_correct' => false, 'order' => 2]);
        Option::create(['question_id' => $q1->id, 'option_text' => 'Fair', 'score' => 0.4, 'is_correct' => false, 'order' => 3]);
        Option::create(['question_id' => $q1->id, 'option_text' => 'Poor', 'score' => 0.1, 'is_correct' => false, 'order' => 4]);

        // Q2: Text (mandatory)
        $q2 = Question::create([
            'campaign_id' => $campaign->id,
            'question_text' => 'How are you feeling today? (text)',
            'type' => Question::TYPE_TEXT,
            'is_mandatory' => true,
            'order' => 2,
        ]);
        TextKeyword::create([
            'question_id' => $q2->id,
            'rules' => [
                ['keyword' => 'fine', 'score' => 0.5],
                ['keyword' => 'good', 'score' => 0.8],
                ['keyword' => 'bad', 'score' => 0.1],
            ],
        ]);

        // Q3: Number (optional)
        $q3 = Question::create([
            'campaign_id' => $campaign->id,
            'question_text' => 'How many times did you contact support? (number, optional)',
            'type' => Question::TYPE_NUMBER,
            'is_mandatory' => false,
            'order' => 3,
        ]);
        NumberRule::create(['question_id' => $q3->id, 'exact_value' => 0, 'min_value' => null, 'max_value' => null, 'score' => 1.0]);
        NumberRule::create(['question_id' => $q3->id, 'exact_value' => null, 'min_value' => 1, 'max_value' => 3, 'score' => 0.6]);
        NumberRule::create(['question_id' => $q3->id, 'exact_value' => null, 'min_value' => 4, 'max_value' => 10, 'score' => 0.3]);

        // Q4: MCQ Multi (optional) – multiple selection
        $q4 = Question::create([
            'campaign_id' => $campaign->id,
            'question_text' => 'Which channels do you use to contact us? (select all that apply, optional)',
            'type' => Question::TYPE_MCQ_MULTI,
            'is_mandatory' => false,
            'order' => 4,
        ]);
        $ch1 = Option::create(['question_id' => $q4->id, 'option_text' => 'Email', 'score' => 0.25, 'is_correct' => true, 'order' => 1]);
        $ch2 = Option::create(['question_id' => $q4->id, 'option_text' => 'Phone', 'score' => 0.25, 'is_correct' => true, 'order' => 2]);
        $ch3 = Option::create(['question_id' => $q4->id, 'option_text' => 'Chat', 'score' => 0.25, 'is_correct' => true, 'order' => 3]);
        $ch4 = Option::create(['question_id' => $q4->id, 'option_text' => 'In person', 'score' => 0.25, 'is_correct' => true, 'order' => 4]);

        // Q5: Text optional
        $q5 = Question::create([
            'campaign_id' => $campaign->id,
            'question_text' => 'Any other comments? (optional)',
            'type' => Question::TYPE_TEXT,
            'is_mandatory' => false,
            'order' => 5,
        ]);
        TextKeyword::create([
            'question_id' => $q5->id,
            'rules' => [
                ['keyword' => 'great', 'score' => 0.9],
                ['keyword' => 'improve', 'score' => 0.5],
            ],
        ]);

        // Sample response 1 – all questions answered, multi-select: Email + Chat
        $response1 = Response::create([
            'campaign_id' => $campaign->id,
            'participant_name' => 'Jane Doe',
            'participant_email' => 'jane@example.com',
            'total_score' => 4.2,
            'completed_at' => now(),
        ]);
        ResponseAnswer::create(['response_id' => $response1->id, 'question_id' => $q1->id, 'score' => 1.0, 'selected_option_ids' => [$opt1->id]]);
        ResponseAnswer::create(['response_id' => $response1->id, 'question_id' => $q2->id, 'answer_text' => 'I am feeling good', 'score' => 0.8]);
        ResponseAnswer::create(['response_id' => $response1->id, 'question_id' => $q3->id, 'answer_number' => 0, 'score' => 1.0]);
        ResponseAnswer::create(['response_id' => $response1->id, 'question_id' => $q4->id, 'score' => 0.5, 'selected_option_ids' => [$ch1->id, $ch3->id]]);
        ResponseAnswer::create(['response_id' => $response1->id, 'question_id' => $q5->id, 'answer_text' => 'Great experience overall', 'score' => 0.9]);

        // Sample response 2 – multi-select: Phone + In person
        $response2 = Response::create([
            'campaign_id' => $campaign->id,
            'participant_name' => 'John Smith',
            'participant_email' => 'john@example.com',
            'total_score' => 2.3,
            'completed_at' => now(),
        ]);
        ResponseAnswer::create(['response_id' => $response2->id, 'question_id' => $q1->id, 'score' => 0.7, 'selected_option_ids' => [$opt2->id]]);
        ResponseAnswer::create(['response_id' => $response2->id, 'question_id' => $q2->id, 'answer_text' => 'Fine thanks', 'score' => 0.5]);
        ResponseAnswer::create(['response_id' => $response2->id, 'question_id' => $q3->id, 'answer_number' => 2, 'score' => 0.6]);
        ResponseAnswer::create(['response_id' => $response2->id, 'question_id' => $q4->id, 'score' => 0.5, 'selected_option_ids' => [$ch2->id, $ch4->id]]);
        // q5 left empty (optional)

        // Sample response 3 – only mandatory + one optional
        $fairOpt = Option::where('question_id', $q1->id)->where('order', 3)->first();
        $response3 = Response::create([
            'campaign_id' => $campaign->id,
            'participant_name' => 'Alex Lee',
            'participant_email' => 'alex@example.com',
            'total_score' => 0.5,
            'completed_at' => now(),
        ]);
        ResponseAnswer::create(['response_id' => $response3->id, 'question_id' => $q1->id, 'score' => 0.4, 'selected_option_ids' => $fairOpt ? [$fairOpt->id] : []]);
        ResponseAnswer::create(['response_id' => $response3->id, 'question_id' => $q2->id, 'answer_text' => 'Not great', 'score' => 0.1]);
        // q3, q4, q5 skipped (all optional)
    }
}
