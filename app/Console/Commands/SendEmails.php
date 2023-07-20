<?php

namespace App\Console\Commands;

use App\Models\Entries;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SendEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendEmail:entries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'this  sends entries gotten from bot to email';

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
     * @return int
     */
    public function handle()
    {
        $entries_model = new Entries();
        $result = $entries_model->get();

        $columns  = ['First Name', 'Last Name', "Email", "Phone", "Region", " Closer Store", "Project", "Industry", "Connect to Store", "DIY'ER", "Contractor",];

        $file = fopen(public_path('attachments/entries.csv'), 'w'); //<-here. name of file is written in headers
        fputcsv($file, $columns);

        foreach ($result as $res) {
            fputcsv($file, [$res->first_name, $res->last_name, $res->email, $res->phone, $res->region, $res->store_closes, $res->project, $res->industry, $res->connect_to_store, $res->is_diy_customer, $res->is_contractor,]);
        // Storage::disk("public_uploads")->put("entries.csv", $file);

        }
        fclose($file);

      
        // Mail::send()
        $cc_user = ["sheldon@pfiredigital.co.za"];
        $data["email"] = "info@digi-express.co.za";
        $data["title"] = "Timbuild Entries";

        $files = [
            public_path('attachments/entries.csv'),
        ];


        Mail::send('mail.entries', $data, function ($message) use ($data, $files, $cc_user) {
            $message->to($data["email"])
                ->subject($data["title"]);
            $message->cc($cc_user);

            foreach ($files as $file) {
                $message->attach($file);
            }
        });

        
        return 0;
    }
}
