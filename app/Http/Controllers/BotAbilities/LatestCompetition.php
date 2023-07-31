<?php

namespace App\Http\Controllers\BotAbilities;

use App\Http\Controllers\BotFunctions\GeneralFunctions;
use App\Http\Controllers\BotFunctions\TextMenuSelection;
use App\Models\Entries;
use App\Traits\GeneralAbilities;

class LatestCompetition extends GeneralFunctions implements AbilityInterface
{
    use GeneralAbilities;

    public const USER_REGION = "user_region";
    public const STORE_SELECTED = "store_selected";
    public $steps = [
        "begin_func", "getLastName", "getEmail","competitorType",
        "confirmCompetitorType",
        "saveWorkingOn",
        "getStoreLocation","getConsent","CheckConsentSelection",];
    public $competitor_type_menu = ["DIY Customer","Contractor"];
    public $industry = [
        "Carpenter / Cabinet Maker",
        "Building Contractor",
        "Electrical",
        "Plumbing",
        "Painting",
        "Other",

    ];
    public $project = [
        "Board / Cabinetry",
        "Building / Renovating",
        "Woodworking",
        "Paint Project",
        "Plumbing",
        "Electrical",
        "Other",

    ];


    public function begin_func()
    {
        $this->set_session_route("LatestCompetition");

        $msg = <<<MSG
        Awesome! And thank you for taking part.
        Entry is quick and easy!
        Please follow these simple steps to enter our competition.
        
        Reply MENU at any time to return to our main menu.

        May we have your First Name? 

        MSG;

        $txt_msg = $this->make_text_message($msg, $this->userphone);
        $this->send_post_curl($txt_msg);
        $this->go_to_next_step();
        $this->ResponsedWith200();
        
    }

    public function getLastName()
    {
        // store first name
        $this->storeAnswerToSession(["store_as"=>"first_name"]);

        $msg = <<<MSG
        Thanks {$this->user_message_original}. Please could you let us have your Surname?

        NOTE: Reply MENU at any time to return to our main menu.
        MSG;

        $txt_msg = $this->make_text_message($msg, $this->userphone);
        $this->send_post_curl($txt_msg);
        $this->go_to_next_step();
        $this->ResponsedWith200();
    }

    public function getEmail()
    {
        // store first name
        $this->storeAnswerToSession(["store_as"=>"last_name"]);
        $this->storeAnswerToSession(["store_as"=>"phone"],$this->userphone);


        $msg = <<<MSG
        Great! Now, {$this->getAnswerFromSession('first_name')} {$this->user_message_original}. We need to know your email address in order to communicate with you should you be one of our lucky winners. 

        NOTE: Reply MENU at any time to return to our main menu.

        MSG;

        $txt_msg = $this->make_text_message($msg, $this->userphone);
        $this->send_post_curl($txt_msg);
        $this->go_to_next_step();
        $this->ResponsedWith200();
    }

    public function competitorType()
    {
        $this->storeAnswerToSession(["store_as"=>"email"]);

        $msg = "Thank you for submitting your information. We would love to know whether you are a";
        $comp_type_menu = $this->MenuArrayToObj($this->competitor_type_menu);
        $txt_menu_select = new TextMenuSelection($comp_type_menu);
        $txt_menu_select->send_menu_to_user($msg);
        $this->go_to_next_step();
        $this->ResponsedWith200();



    }

    public function confirmCompetitorType()
    {
        $user_selected = $this->user_message_original;
        $user_selected_lowered = $this->user_message_lowered;
        // check selection first
        $comp_type_menu = $this->MenuArrayToObj($this->competitor_type_menu);
        $txt_menu_select = new TextMenuSelection($comp_type_menu);
        $txt_menu_select->check_expected_response($user_selected);

        // save comp type


        if($user_selected == "1" || $user_selected_lowered == "diy customer")
        {
            $comp_type = "DIY Customer";
            $msg = <<<MSG
            So you’re a DIY’er. That is good to know. Last Question.
            What is the current or next project you are going to be working on?
            MSG;

            $menu = $this->project;

        }

        if($user_selected == "2" || $user_selected_lowered == "contractor")
        {
            $comp_type = "Contractor";

            $msg = <<<MSG
            So you’re a Contractor. That is good to know. Last Question.
            Please confirm the type of industry you are currently involved in?            
            MSG;

            $menu = $this->industry;


        }

        $this->storeAnswerToSession(["store_as"=>"comp_type"],$comp_type);

        $menu_arr = $this->MenuArrayToObj($menu);
        $txt_menu_select = new TextMenuSelection($menu_arr);
        $txt_menu_select->send_menu_to_user($msg);
        $this->go_to_next_step();
        $this->ResponsedWith200();
    


    }

    public function saveWorkingOn()
    {
        $comp_type = $this->getAnswerFromSession("comp_type");

        if($comp_type == "DIY Customer")
        {
            $menu = $this->project;
        }else{
            $menu = $this->industry;

        }

        // confirm selection
        $menu_arr = $this->MenuArrayToObj($menu);
        $txt_menu_select = new TextMenuSelection($menu_arr);
        $txt_menu_select->check_expected_response($this->user_message_original);

        // store selection
        if(!is_numeric($this->user_message_original))
        {
            $working_on = $this->user_message_original;
        }else {
            $selection = $this->user_message_original - 1;
            $working_on = $menu[$selection];
        }
        $this->storeAnswerToSession(["store_as"=>"working_on"],$working_on);

        // start listing region
       
        $message = <<<MSG
        Great. In order to complete your entry into the right competition, we need to know which store is closest to you.
        Please select your region to make your store selection easier. You need to reply with the corresponding number:
        
        MSG;
        $this->listRegion($message);

        $this->returnHomeMessage();

        $this->go_to_next_step();
        $this->ResponsedWith200();

    }

    public function getStoreLocation()
    {
    
        // check first for selection to be correct
        $user_selected = $this->user_message_original;
        $this->CheckRegionSelected($user_selected);

      
        // second check for selection of others
        if ($user_selected == "5" || $this->user_message_lowered == "others") {
            $message = <<<MSG
            We’re sorry you’re located in a province we currently do not have any stores, but we’re positive one will be opening soon.
            MSG;
            $this->send_post_curl($this->make_text_message($message, $this->userphone));
            $this->returnHomeMessage();
            $this->ResponsedWith200();
        }

          // store collected region
          $this->storeAnswerToSession(['store_as'=>self::USER_REGION]);

          $store_menu = $this->listStoreInRegion($user_selected,"11");
          $message = <<<MSG
          Fantastic. Here is a list of our stores located in your choosen Province.
          MSG;
          $this->sendstoreMenu($store_menu,$message);
          $this->returnHomeMessage();
          $this->go_to_next_step();
          $this->ResponsedWith200();


    }


    public function getConsent()
    {
        $answers = $this->user_session_data['answered_questions'];
        $region = $answers[self::USER_REGION];
        // check store location selected
        $store_menu = $this->listStoreInRegion($region,"11");
        $this->checkStoreSelected($store_menu,$this->user_message_original);

        // check if need to be returned to province menu
        if($this->goBackToRegionSelection($region,$this->user_message_original,"11"))
        {
            $message = <<<MSG
            Great. In order to complete your entry into the right competition, we need to know which store is closest to you.
            Please select your region to make your store selection easier. You need to reply with the corresponding number:
            
            MSG;
           $this->listRegion($message);
           $this->go_to_previous_step();
           $this->returnHomeMessage();

           $this->ResponsedWith200();
        }

        // store the store-location selected 
        $store = $this->fetchStoreSelected($region,$this->user_message_original,"11");
        $this->storeAnswerToSession(["store_as"=>self::STORE_SELECTED],$store->id);

        // send congratulation message
        $msg = <<<MSG
        Congratulations! You have successfully entered our competition.
        Good Luck and thank you for your continued support.

        MSG;
        $this->send_post_curl($this->make_text_message($msg, $this->userphone));
        
        // ask for constent
        $this->connection_consent();
        $this->go_to_next_step();
      

    }

    public function CheckConsentSelection()
    {
        $answers = $this->user_session_data['answered_questions'];
        $store = $answers[self::STORE_SELECTED];

        $username = $this->getAnswerFromSession("first_name") . " ". $this->getAnswerFromSession("last_name");
        $permission = $this->getAnswerFromSession("connect_to_store");

        $this->checkConnectionConsent($this->user_message_original,$store,$permission,$username);

        // store data collected
        $this->storeCollectedData();
        
        $this->returnHomeMessage();
        $this->ResponsedWith200();
    }


    public function storeCollectedData()
    {
       
        // set session answers
        $answers = $this->user_session_data['answered_questions'];


        // set all data into var
        $store_id = $answers[self::STORE_SELECTED];
        $region_id = $answers[self::USER_REGION];
        
        $first_name = $answers['first_name'];
        $last_name = $answers['last_name'];
        $email = $answers['email'];
        $phone = $answers['phone'];
        $is_diy_customer ="no";
        $is_contractor	= "no";
        $project = "NA";
        $industry = "NA";


        // set all data from abstract to readable data
        // var is_diy_customer
        // var is_contractor
        // var project
        // var industry
        // region
        // store
      


        if($answers['comp_type'] == "DIY Customer")
        {
            $is_diy_customer = "yes";
            $project = $answers['working_on'];
        }else {
            $is_contractor = "yes";
            $industry = $answers['working_on'];
        }

        $region = $this->fetchRegion($region_id);
        $store = $this->fetchStore($store_id);




        // then save data to entries table
        $entries_model = new Entries();
        $entries_model->first_name = $first_name;
        $entries_model->last_name = $last_name;
        $entries_model->email = $email;
        $entries_model->phone = $phone;
        $entries_model->region = $region->region;
        $entries_model->store_closes = $store->location;
        $entries_model->project = $project;
        $entries_model->industry = $industry;
        $entries_model->is_diy_customer = $is_diy_customer;
        $entries_model->is_contractor = $is_contractor;
        $entries_model->connect_to_store = $answers['connect_to_store'] ?? "";
        $entries_model->save();



    }





    function call_method($key)
    {
        $method_name = $this->steps[$key];
        $this->$method_name();
    }



}