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
        $menu_main = $this->MenuArrayToObj($this->main_menu);
        $text_menu_selection = new TextMenuSelection($menu_main);
        $check = $text_menu_selection->check_expected_response($this->user_message_original);

        if($check)
        {
            // do the menu selection
           $user_selected = $this->user_message_original;
           $user_selected_lowered = $this->user_message_lowered;

           if($user_selected == "1" || $user_selected_lowered == "Enter our latest Competition" )
           {
                $this->send_post_curl($this->make_text_message("coming soon!",$this->userphone));
                $this->ResponsedWith200();
           }

           if($user_selected == "2" || $user_selected_lowered == "Locate your nearest TimBuild Store" )
           {
                $this->send_post_curl($this->make_text_message("coming soon!",$this->userphone));
                $this->ResponsedWith200();
           }

           if($user_selected == "3" || $user_selected_lowered == "Visit the TimBuild SA’s website" )
           {
                $this->send_post_curl($this->make_text_message("coming soon!",$this->userphone));
                $this->ResponsedWith200();
           }

           if($user_selected == "4" || $user_selected_lowered == "Read our Competition Rules for Entry" )
           {
            $button = [
                [
                    "type" => "url",
                    "reply" => [
                        "id" => "bnl_sample:4",
                        "title" => "Sample Audio"
                    ]
                ]

            ];
               $message = <<<MSG
               Our latest rules of entry into our competitions can be found on our main TimBuild SA website by clicking the link below:

               www.timbuild.co.za/competitions 

               NOTE: Reply MENU at any time to return to our main menu.

               MSG;
               $button_message = $this->make_text_message($message,$this->userphone,true);
               $this->ResponsedWith200();
           }
        }
        



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
