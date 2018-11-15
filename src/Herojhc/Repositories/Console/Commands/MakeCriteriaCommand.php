<?php

namespace Herojhc\Repositories\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Composer;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Herojhc\Repositories\Console\Commands\Creators\CriteriaCreator;

/**
 * Class MakeCriteriaCommand
 *
 * @package Herojhc\Repositories\Console\Commands
 */
class MakeCriteriaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:criteria';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new criteria class';

    /**
     * @var
     */
    protected $creator;

    /**
     * @var
     */
    protected $composer;

    /**
     * @param CriteriaCreator $creator
     * @param Composer $composer
     */
    public function __construct(CriteriaCreator $creator, Composer $composer)
    {
        parent::__construct();

        // Set the creator.
        $this->creator = $creator;

        // Set the composer.
        $this->composer = $composer;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Get the arguments.
        $arguments = $this->argument();

        // Get the options.
        $options = $this->option();

        // Write criteria.
        $this->writeCriteria($arguments, $options);

        // Dump autoload.
        $this->composer->dumpAutoloads();
    }

    /**
     * @param $arguments
     * @param $options
     * @return bool
     */
    public function writeCriteria($arguments, $options)
    {
        // Set criteria.
        $criteria = $arguments['criteria'];

        // Set model.
        $model = $options['model'];

        try {
            // Create the criteria.
            if ($this->creator->create($criteria, $model)) {
                // Information message.
                $this->info("Succesfully created the criteria class.");
            }
        } catch (FileNotFoundException $exception) {
            $this->error($criteria . ' already exists!');
            return false;
        }

    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['criteria', InputArgument::REQUIRED, 'The criteria name.']
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['model', null, InputOption::VALUE_OPTIONAL, 'The model name.', null],
        ];
    }
}
