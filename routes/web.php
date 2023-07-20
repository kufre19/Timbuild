<?php

use App\Models\Entries;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get("/", function () {
    return view("login");
});

Route::post("login", function (Request $request) {
    $pass = $request->input("pass");
    if ($pass == "9MXtpUBANu") {
        session()->put("loggin", true);
        return redirect()->to("/entries");
    } else {
        return redirect()->to("/");
    }
});

Route::get('/entries', function () {
    if (!session()->get("loggin") || !session()->has("loggin")) {
        return redirect()->to("/");
    }
    $entries_model = new Entries();
    $entries = $entries_model->paginate(15);
    return view('index', compact("entries"));
});


Route::get("download", function () {
    $entries_model = new Entries();
    $result = $entries_model->get();
    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=entries.csv", // <- name of file
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0",
    ];
    $columns  = ['First Name', 'Last Name', "Email", "Phone", "Region", " Closer Store", "Project", "Industry", "Connect to Store", "DIY'ER", "Contractor",];
    $callback = function () use ($result, $columns) {
        $file = fopen('php://output', 'w'); //<-here. name of file is written in headers
        fputcsv($file, $columns);
        foreach ($result as $res) {
            fputcsv($file, [$res->first_name, $res->last_name, $res->email, $res->phone, $res->region, $res->store_closes, $res->project, $res->industry, $res->connect_to_store, $res->is_diy_customer, $res->is_contractor,]);
        }
        fclose($file);
    };
    return response()->stream($callback, 200, $headers);
});


Route::get("test", function () {
    $cc_user = ["kufresolomon21@gmai.com"];
    $data["email"] = "whitemaxwell5@gmail.com";
    $data["title"] = "techsolutionstuff.com";

    $files = [
        public_path('attachments/entries.csv'),
    ];


    Mail::send('mail.entries', $data, function ($message) use ($data, $files,$cc_user) {
        $message->to($data["email"])
            ->subject($data["title"]);
            $message->cc($cc_user);

        foreach ($files as $file) {
            $message->attach($file);
        }
    });

    echo "Mail send successfully !!";
});
