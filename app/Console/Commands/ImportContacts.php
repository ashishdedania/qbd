<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportContacts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:contacts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'import contacts from stored csv files';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //set the path for the csv files
        $path = base_path("resources/pendingcontacts/*.csv"); 
        
        //run 2 loops at a time 
        foreach (array_slice(glob($path),0,2) as $file) {
            
            //read the data into an array
            $data = array_map('str_getcsv', file($file));

            //loop over the data
            foreach($data as $row) {

                //insert the record or update if the email already exists
                Contact::updateOrCreate([
                    'email' => $row[6],
                ], ['email' => $row[6]]); 
            }

            //delete the file
            unlink($file);
        }
    }
}
