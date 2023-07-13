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

    public $steps = ["begin_func", "DoSelection", ""];
    public $main_menu = [
        "Enter our latest Competition",
        "Locate your nearest TimBuild Store",
        "Visit the TimBuild SA’s website",
        "Read our Competition Rules for Entry"
    ];
   



    public function begin_func()
    {
       $menu_main = $this->MenuArrayToObj($this->main_menu);
       $text_menu = new TextMenuSelection($menu_main);
       $menu_message = <<<MSG
       Hello There!
       Welcome to Timbuild South Africa.
       Please choose from one of the following options:
       MSG;
       $this->set_session_route("Main");
       $text_menu->send_menu_to_user($menu_message);
       $this->go_to_next_step();
       $this->ResponsedWith200();
    }


    public function start_from_main_menu()
    {
        // this should the hello message with main menu
    }

   

   
    public function DoSelection()
    {
        // check first if the selection is in the list

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
