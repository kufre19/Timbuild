<?php

use App\Models\Entries;
use App\Models\Region;
use Illuminate\Http\Request;
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

Route::get("/",function(){
    return view("login");
});

Route::post("login",function(Request $request){
    $pass = $request->input("pass");
    if($pass == "9MXtpUBANu")
    {
        session()->put("loggin",true);
        return redirect()->to("/entries");
    }else{
        return redirect()->to("/");

    }
});

Route::get('/entries', function () {
    if(!session()->get("loggin") || !session()->has("loggin"))
    {
        return redirect()->to("/");

    }
    $entries_model = new Entries();
    $entries = $entries_model->paginate(20);
    return view('index',compact("entries"));
});


// Route::get("test", function(){
//     $headers = [
//         "Content-type"        => "text/csv",
//         "Content-Disposition" => "attachment; filename=output_chat.csv", // <- name of file
//         "Pragma"              => "no-cache",
//         "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
//         "Expires"             => "0",
//     ];
//     $columns  = ['from_user', 'to_user', 'message', 'date_added'];
//     $callback = function () use ($result, $columns) {
//         $file = fopen('php://output', 'w'); //<-here. name of file is written in headers
//         fputcsv($file, $columns);
//         foreach ($result as $res) {
//             fputcsv($file, [$res->from_user, $res->to_user, $res->message, $res->date_added]);
//         }
//         fclose($file);
//     };
// });
