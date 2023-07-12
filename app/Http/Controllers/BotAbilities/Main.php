<?php

namespace App\Http\Controllers\BotAbilities;

use App\Http\Controllers\BotFunctions\GeneralFunctions as BotFunctionsGeneralFunctions;
use App\Http\Controllers\BotFunctions\TextMenuSelection;
use App\Models\ScheduleMenu;
use Illuminate\Http\Request;


 
/**
 * this main file will be used to map main menu selection to journies to be started 
 */

class Main extends BotFunctionsGeneralFunctions implements AbilityInterface
{

    public $steps = ["begin_func", "", ""];
   



    public function begin_func()
    {
        // echo"loozp";
        // this should be removed

     
    }


    public function start_from_main_menu()
    {
        // this should the hello message with main menu
    }

   

   


    function call_method($key)
    {
        $method_name = $this->steps[$key];
        $this->$method_name();
    }

    public static function test_main()
    {
        return "ok";
    }



}
