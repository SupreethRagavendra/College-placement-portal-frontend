<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SeedSampleQuestions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:sample-questions {--force : Force re-insertion of existing questions}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed sample questions for testing';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $aptId = DB::table('categories')->where('name', 'Aptitude')->value('id');
        $techId = DB::table('categories')->where('name', 'Technical')->value('id');

        if (!$aptId && !$techId) {
            $this->error('Categories not found. Run category seeder first.');
            return Command::FAILURE;
        }

        $rows = [];
        if ($aptId) {
            $rows = array_merge($rows, [
                [ 'category_id' => $aptId, 'question_text' => 'What is 25% of 240?', 'options' => json_encode(['48','50','52','60']), 'correct_option' => '0' ],
                [ 'category_id' => $aptId, 'question_text' => 'Ratio of 3:4 equals?', 'options' => json_encode(['0.65','0.66','0.75','0.8']), 'correct_option' => '2' ],
                [ 'category_id' => $aptId, 'question_text' => 'Simple interest on 1000 at 10% for 2 years?', 'options' => json_encode(['100','150','200','250']), 'correct_option' => '2' ],
            ]);
        }
        if ($techId) {
            $rows = array_merge($rows, [
                [ 'category_id' => $techId, 'question_text' => 'Time complexity of binary search?', 'options' => json_encode(['O(n)','O(log n)','O(n log n)','O(1)']), 'correct_option' => '1' ],
                [ 'category_id' => $techId, 'question_text' => 'HTTP is built on which protocol?', 'options' => json_encode(['TCP','UDP','ICMP','IP']), 'correct_option' => '0' ],
                [ 'category_id' => $techId, 'question_text' => 'Which SQL command creates a table?', 'options' => json_encode(['MAKE TABLE','CREATE TABLE','NEW TABLE','ADD TABLE']), 'correct_option' => '1' ],
            ]);
        }

        $inserted = 0;
        foreach ($rows as $r) {
            $exists = DB::table('questions')
                ->where('category_id', $r['category_id'])
                ->where('question_text', $r['question_text'])
                ->exists();
            if ($exists && !$this->option('force')) { continue; }
            DB::table('questions')->insert($r);
            $inserted++;
        }

        $this->info("Inserted {$inserted} questions.");
        return Command::SUCCESS;
    }
}