<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContactsController extends Controller
{
    public function import()
    {

       //setup an empty array
    	$records = [];

    	//path where the csv files are stored
        $path = base_path('resources/pendingcontacts');

        //loop over each file
        foreach (glob($path.'/*.csv') as $file) {

        	//open the file and add the total number of lines to the records array
            $file = new \SplFileObject($file, 'r');
            $file->seek(PHP_INT_MAX);
            $records[] = $file->key();
        }

        //now sum all the array keys together to get the total
        $toImport = array_sum($records);

        return view('import', compact('toImport'));
    }

    


    public function upload()
    { //dd(base_path('storage/qbd'));
        if(request()->hasFile('csvFile'))
        {
            $file = request()->file('csvFile');
            $originalname = uniqid() . '.csv';
            $file->move(base_path('storage/qbd'), $originalname);
            echo $originalname; 
            
        }
    }

    public function getEventStream(Request $request) {

        $filename = storage_path('/qbd/'.$request->get('filename'));


        if(file_exists($filename))
        {
            $records  = file($filename);
            $total    = count($records);
            $count    = 1;
            $errorArray = '';

            foreach($records as $record)
            {
                $row = explode(',' , $record);
                
                $error = false;

                $message = 'Validating '.$count.' / '.$total.' records';

                if($row[0] == '' || $row[2] == '' || $row[3] == '')
                {
                    $error = true;

                    $errorArray = $errorArray . '\n' . $record;
                }

                

                $data = [
                    'row' => $row,
                    'progress' => (100/$total)*$count,
                    'message'  => $message,
                    'error'    => $error,
                    'total'    => $total,
                    'count'    => $count,
                    'errorArray' => $errorArray
                ];
            
                $response = new StreamedResponse();
                $response->setCallback(function () use ($data){
                
                    echo 'data: ' . json_encode($data) . "\n\n";
                    //echo "retry: 100\n\n"; // no retry would default to 3 seconds.
                    //echo "data: Hello There\n\n";
                    //ob_flush();
                    flush();
                    sleep(1);
                    //usleep(1000);
                });
                
                $response->headers->set('Content-Type', 'text/event-stream');
                $response->headers->set('X-Accel-Buffering', 'no');
                $response->headers->set('Cach-Control', 'no-cache');
                $response->send();

                $count = $count + 1;
            }
        }
    }



    public function getProcessData(Request $request) {
        //$filename = storage_path('/qbd/606b8a0b481b3.csv');
        $filename = storage_path('/qbd/'.$request->get('filename'));
        if(file_exists($filename))
        {
            $records  = file($filename);
            $total    = count($records);
            $count    = 1;
            $errorArray = '';

            foreach($records as $record)
            {
                $row = explode(',' , $record);
                
                $message = 'Processing '.$count.' / '.$total.' records';

                if(($row[0] != '') && ($row[2] != '') && ($row[3] != ''))
                {
                    // insert in to db
                    // set active
                }
                
                $data = [
                    'row' => $row,
                    'progress' => (100/$total)*$count,
                    'message'  => $message,
                    'total'    => $total,
                    'count'    => $count,
                ];
            
                $response = new StreamedResponse();
                $response->setCallback(function () use ($data){
                
                    echo 'data: ' . json_encode($data) . "\n\n";
                    //echo "retry: 100\n\n"; // no retry would default to 3 seconds.
                    //echo "data: Hello There\n\n";
                    //ob_flush();
                    flush();
                    sleep(1);
                    //usleep(1000);
                });
                
                $response->headers->set('Content-Type', 'text/event-stream');
                $response->headers->set('X-Accel-Buffering', 'no');
                $response->headers->set('Cach-Control', 'no-cache');
                $response->send();

                $count = $count + 1;
            }
        }
    }
}