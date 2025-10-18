<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PDO;
use PDOException;

class SetupSupabaseDbTables extends Command
{
    protected $signature = 'supabase:bootstrap-tables {--seed : Seed sample categories and questions}';
    protected $description = 'Create required Supabase tables (categories, questions, tests, results) directly via Postgres connection and optionally seed sample data';

    public function handle(): int
    {
        $host = env('SUPABASE_DB_HOST');
        $port = env('SUPABASE_DB_PORT', 5432);
        $db   = env('SUPABASE_DB_NAME', 'postgres');
        $user = env('SUPABASE_DB_USER', 'postgres');
        $pass = env('SUPABASE_DB_PASSWORD');
        $sslmode = env('SUPABASE_DB_SSLMODE', 'require');

        if (!$host || !$user || !$pass) {
            $this->error('Missing DB env. Please set SUPABASE_DB_HOST, SUPABASE_DB_USER, SUPABASE_DB_PASSWORD.');
            return Command::FAILURE;
        }

        $dsn = "pgsql:host={$host};port={$port};dbname={$db};sslmode={$sslmode}";

        try {
            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
        } catch (PDOException $e) {
            $this->error('Connection failed: ' . $e->getMessage());
            return Command::FAILURE;
        }

        $this->info('Connected. Creating tables...');

        $sql = <<<'SQL'
create table if not exists public.categories (
  id bigserial primary key,
  name text not null unique
);

create table if not exists public.questions (
  id bigserial primary key,
  category_id bigint not null references public.categories(id) on delete cascade,
  question text not null,
  options jsonb not null,
  correct_option text not null
);

create table if not exists public.tests (
  id bigserial primary key,
  user_id bigint not null,
  category_id bigint not null references public.categories(id),
  started_at timestamptz default now(),
  completed_at timestamptz
);

create table if not exists public.results (
  id bigserial primary key,
  test_id bigint not null references public.tests(id) on delete cascade,
  user_id bigint not null,
  score integer not null,
  total integer not null,
  created_at timestamptz default now()
);
SQL;

        try {
            $pdo->exec($sql);
            $this->info('Tables created or already exist.');
        } catch (PDOException $e) {
            $this->error('Failed to create tables: ' . $e->getMessage());
            return Command::FAILURE;
        }

        if ($this->option('seed')) {
            $this->info('Seeding data...');
            try {
                $pdo->exec("insert into public.categories (name) values ('Aptitude') on conflict (name) do nothing;");
                $pdo->exec("insert into public.categories (name) values ('Technical') on conflict (name) do nothing;");

                // Get ids
                $aptId = (int) $pdo->query("select id from public.categories where name='Aptitude' limit 1")->fetchColumn();
                $techId = (int) $pdo->query("select id from public.categories where name='Technical' limit 1")->fetchColumn();

                if ($aptId) {
                    $pdo->exec("insert into public.questions (category_id,question,options,correct_option) values
                        ({$aptId}, 'What is 12 * 8?', jsonb_build_array('80','88','96','108'), '2')
                        on conflict do nothing;");
                    $pdo->exec("insert into public.questions (category_id,question,options,correct_option) values
                        ({$aptId}, 'If a train travels 60 km in 1.5 hours, what is its average speed?', jsonb_build_array('30 km/h','40 km/h','45 km/h','50 km/h'), '2')
                        on conflict do nothing;");
                }
                if ($techId) {
                    $pdo->exec("insert into public.questions (category_id,question,options,correct_option) values
                        ({$techId}, 'Which data structure uses FIFO?', jsonb_build_array('Stack','Queue','Tree','Graph'), '1')
                        on conflict do nothing;");
                    $pdo->exec("insert into public.questions (category_id,question,options,correct_option) values
                        ({$techId}, 'Which SQL clause filters rows?', jsonb_build_array('ORDER BY','GROUP BY','HAVING','WHERE'), '3')
                        on conflict do nothing;");
                }
                $this->info('Seeded categories and sample questions.');
            } catch (PDOException $e) {
                $this->warn('Seeding failed: ' . $e->getMessage());
            }
        }

        $this->info('Done.');
        return Command::SUCCESS;
    }
}
