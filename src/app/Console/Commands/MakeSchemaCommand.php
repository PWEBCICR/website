<?php

namespace App\Console\Commands;

use App\Models\Crew;
use App\Models\Schema;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use PDO;

class MakeSchemaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:schema {team_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates a schema for a team in the database if the schema doesn\'t already exist';

    protected Builder $builder;

    public function __construct(
        Builder $builder,
    )
    {
        parent::__construct();
        $this->builder = DB::getSchemaBuilder();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $crew_id = $this->argument('team_id');
            $crew = Crew::find($crew_id);

            if(!$crew) {
                $this->warn("Team " . $this->argument('team_id') . " doesn't exist yet.");
                $this->info("Create the team before creating its schema");
                return Command::FAILURE;
            }

            $dbName = Schema::getSchemaName($crew_id);
            $schema = Schema::where('name', $dbName)->first();

            if(!$schema) {
                $this->info("Creating database " . $dbName);
                $schema['name'] = $dbName;
                $schema['crew_id'] = $crew_id;
                Schema::Create($schema);
                $this->builder->createDatabase($dbName);
            } else {
                $this->info("Database already exists for this team");
            }

            return Command::SUCCESS;

        } catch(\Exception $e) {
            $this->error($e->getMessage());
            return Command::FAILURE;
        }
    }
}