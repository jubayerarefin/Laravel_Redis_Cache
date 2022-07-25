<?php

namespace App\Console\Commands;

use App\Models\Person;
use Illuminate\Console\Command;
use League\Csv\Exception;
use League\Csv\Reader;
use SplFileObject;

class ImportCSV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:csv {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import CSV data into a table';

    /**
     * Execute the console command.
     *
     * @return void
     * @throws Exception
     */
    public function handle(): void
    {
        $file_name = $this->argument('file');
        $this->info("Importing data from {$file_name}");
        //load the CSV document from a file path
        $file = new SplFileObject($file_name);
        $csv = Reader::createFromFileObject($file, 'r');
        $csv->setHeaderOffset(0);
        $records = $csv->getRecords(); //returns all the CSV records as an Iterator object
        foreach ($records as $record) {
            dump($record);
            $this->info('Inserting record: ' . json_encode($record));
            Person::insert($record);
        }
        $this->info("All Data from {$file_name} is imported");
    }
}
