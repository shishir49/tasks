<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Jobs\CustomerCsv;
use App\Models\User;
use App\Notifications\ConfirmationEmail;
use Illuminate\Support\Carbon;
use File;
use Storage;

class Main extends Controller
{
    public function index() {
        return view('welcome');
    }

    public function storeCsvData(Request $request) {
        $data   = file($request->uploadCsv);
        $chunks = array_chunk($data,5);
        $path   = resource_path('temp');

        foreach($chunks as $key => $chunk) {
            $name = "/tmp{$key}.csv";
            
            file_put_contents($path . $name, $chunk);
        }

        $files = glob("$path/*.csv");

        $header = []; 
        foreach($files as $key => $file) {
           $data = array_map('str_getcsv',file($file));

           if($key == 0) {
              $header = $data[0];
              
              unset($data[0]);
           }

           CustomerCsv::dispatch($data, $header);

           unlink($file);
        }

        \Notification::route('mail', 'admin@akaarit.com')->notify(new ConfirmationEmail());

        return response()->json(200);
    }

    public function getCustomer(Request $request) {
        if($request->ajax()) {
            $output = '';

            if($request->branch_id != '' || $request->gender != '') {
               $output = Customer::where('branch_id', 'like', '%'.$request->branch_id.'%')->where('gender','like','%'.$request->gender.'%')->get();
            }else{
               $output = Customer::all();
            }

            return response()->json($output);

        }
    }

    public function storePdf(Request $request) {

        $filename = file("task.csv");
        unset($filename[0]);

        foreach($filename as $key => $file) {
 
           $get_name = explode(',', $file);
           $path_from = resource_path('pdfFiles');
           $path_to = public_path('upload');
           
            if(file_exists($path_from.'/'.trim(preg_replace('/\s\s+/', ' ', $get_name[1])).'.pdf')) {

                if (!file_exists($path_to.'/'.trim(preg_replace('/\s\s+/', ' ', $get_name[0])))) {
                    mkdir($path_to.'/'.trim(preg_replace('/\s\s+/', ' ', $get_name[0])), 0777, true);
                }
    
                rename($path_from.'/'.trim(preg_replace('/\s\s+/', ' ', $get_name[1])).'.pdf', $path_to.'/'.trim(preg_replace('/\s\s+/', ' ', $get_name[0])).'/'.trim(preg_replace('/\s\s+/', ' ', $get_name[1])).'.pdf');
            }
        }

        return redirect()->back()->with("message", "Files Moved Successfully");
    }
}
