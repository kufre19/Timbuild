<?php
namespace App\Traits;

use App\Http\Controllers\BotFunctions\TextMenuSelection;
use App\Models\Region;
use App\Models\StoreInfo;

trait GeneralAbilities
{


    // this will house methods that will be general in any project

    public function listRegion($message)
    {
        $regions_Arr = [];
        $region_model = new Region();
        $regions = $region_model->select("region")->get();
        foreach ($regions as $region => $value) {
            array_push($regions_Arr,$value['region']);
        }
        $region_obj = $this->MenuArrayToObj($regions_Arr);
        $txt_menu = new TextMenuSelection($region_obj);
       
        $txt_menu->send_menu_to_user($message);

    }


    public function CheckRegionSelected($response)
    {
        $regions_Arr = [];
        $region_model = new Region();
        $regions = $region_model->select("region")->get();
        foreach ($regions as $region => $value) {
            array_push($regions_Arr,$value['region']);
        }
        $region_obj = $this->MenuArrayToObj($regions_Arr);
        $txt_menu = new TextMenuSelection($region_obj);
       
        $check = $txt_menu->check_expected_response($response);
        if($check)
        {
            return true;
        }

    }

    public function listStoreInRegion($region)
    {
        if(!is_numeric($region))
        {
            $region_model = new Region();
            $region_select = $region_model->select("id")->where('region',$region)->first();
            $region = $region_select->id;

        }
        $store_model = new StoreInfo();
        $stores = $store_model->select("location")->where("region_id",$region)->get();
        $stores_Arr = [];

        foreach ($stores as $store => $value) {
            array_push($stores_Arr,$value['location']);
        }
        // extra data that's not saved in db 
        array_push($stores_Arr,"Go Back to Province selection");

        $store_obj = $this->MenuArrayToObj($stores_Arr);
        return $store_obj;


    }

    public function sendstoreMenu($store_obj,$message)
    {
        $txt_menu = new TextMenuSelection($store_obj);
        $txt_menu->send_menu_to_user($message);
       
    }


    public function checkStoreSelected($store_obj,$response)
    {
        $txt_menu = new TextMenuSelection($store_obj);
        $check = $txt_menu->check_expected_response($response);
        if($check)
        {
            return true;
        }
    }

    public function goBackToRegionSelection($region,$response)
    {
        if(!is_numeric($region))
        {
            $region_model = new Region();
            $region_select = $region_model->select("id")->where('region',$region)->first();
            $region = $region_select->id;

        }
        $store_model = new StoreInfo();
        $stores = $store_model->select("location")->where("region_id",$region)->get();
        $stores_Arr = [];

        foreach ($stores as $store => $value) {
            array_push($stores_Arr,$value['location']);
        }
        // extra data that's not saved in db 
        array_push($stores_Arr,"Go Back to Province selection");

        if(!is_numeric($response))
        {
            // check by text
            if($response == "Go Back to Province selection")
            {
                return true;
            }
        }else{
            if($response == count($stores_Arr))
            {
                return true;
            }
        }

        return false;


    }
    
    // this will fetch sstore from db using the selection stored in db
    public function fetchStoreSelected($region, $selection)
    {
        if(!is_numeric($region))
        {
            $region_model = new Region();
            $region_select = $region_model->select("id")->where('region',$region)->first();
            $region = $region_select->id;

        }
        $store_model = new StoreInfo();
        $stores = $store_model->select("location")->where("region_id",$region)->get();
        $stores_Arr = [];

        foreach ($stores as $store => $value) {
            array_push($stores_Arr,$value['location']);
        }
        if(!is_numeric($selection))
        {
            // fetch by location
            $location = $selection;
        }else{
            // get location first then fetch by location
            $item = $selection -1;
            $location = $stores_Arr[$item];
        }
        $store_model = new StoreInfo();
        $store = $store_model->where('location',$location)->first();

        return $store;



       
    }

    public function connection_consent()
    {
        $opt = ["Yes Please!","No. I am all sorted. Thank You"];
        $consent_menu = $this->MenuArrayToObj($opt);
        $message = "Would you like us to put you in contact with your nearest store?";
        $txt_menu = new TextMenuSelection($consent_menu);
        $txt_menu->send_menu_to_user($message);

    }

    public function checkConnectionConsent($response,$store)
    {
        $opt = ["Yes Please!","No. I am all sorted. Thank You"];
        $consent_menu = $this->MenuArrayToObj($opt);
        $txt_menu = new TextMenuSelection($consent_menu);
        $txt_menu->check_expected_response($response);

        if($response == "2" || $response == "No. I am all sorted. Thank You")
        {
            $this->send_post_curl($this->make_text_message("TimBuild SA appreciates you. Watch out for our future competitions and promotions.",$this->userphone));
            $this->returnHomeMessage();
            
        }else{
            $this->sendConnection($store);
        }




    }

    public function sendConnection($store)
    {
        $store_model = new StoreInfo();
        $store = $store_model->where('id',$store)->first();
        $message = <<<MSG
        Thank You {$this->username}. TimBuild {$store->location} is located at {$store->address}. 
        Their contact number is: {$store->landline}
        You can also email them on {$store->email_1}.

        To be directed to the above store and by clicking the link below, you consent for us to 
        connect you to the store. Your communications will be with your chosen store 
        directly and no longer with TimBuild SA.
        {link}
        MSG;

        

        $this->send_post_curl($this->make_text_message($message,$this->userphone));


    }

    public function returnHomeMessage ()
    {
        $this->send_post_curl($this->make_text_message("NOTE: Reply MENU at any time to return to our main menu.",$this->userphone));

    }
}
