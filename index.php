<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        include 'api.php';
        
        $api = new api();
        
        $numberOfDevices =0;
        $numberOfAvDefenders =0;
        
        foreach ($api->getDevices()->return as $device) {
            $numberOfDevices++;
            $deviceid = $device->info[0]->value;
            foreach ($api->getDeviceStatus($deviceid)->return as $value) {
                foreach ($value->info as $info) {
                    //var_dump($info);
                    //echo '<br/>';
                    
                    
                    
                    if($info->key == "devicestatus.modulename"){
                        if($info->value == "AV Defender Status"){
                            $numberOfAvDefenders++;
                            
                            
                        }
                    }
                }
            }
        }
        echo "Number of devices : " . $numberOfDevices . "<br />";
        echo "Number of AV defenders: " . $numberOfAvDefenders;
        
  
        
        //echo $api->addCustomer("customername", "0622331122", "1122MM", "Kerkstraat 11", "", "Veldhoven", "NB", "NL", "Mr", "John", "Lennon", "0633221111", "31", "johnlennon@email.com", "Finance", "111");
        
       
        
        //var_dump($api->getDevices());
        
        
//        foreach ($api->getDevices()->return as $value) {
//            var_dump($value);
//            echo '<br /><br />';
//        }
        
        //var_dump($api->deleteCustomer(154));
        
        //$api->getDeviceStatus("2061228974")->return
        
        //var_dump();
        //var_dump($api->getDevice("2061228974"));
        
        //$api->addUserTest();
        //$api->addUser('testmail@mail.com', 'pAs_w0rdd', '111', 'John', 'Lennon');
        
        //var_dump($api->getAccessGroups()->return);
//        foreach ($api->getAccessGroups()->return as $value) {
//            var_dump($value);
//            echo '<br /><br />';
//        }
        
        //$api->addUser("fakemail@mail.com", "p@sSw0rd", "111", "first", "last", "1002", "708215820")
        // 1003 = default technician role
        // 1002 = default admin role
        // 1001 = default dashboard role
        // 1000 = default remote control
         
        
        //$array = $api->getCustomerListChildren("111");

        //$api->addUser('testmail@mail.com', 'pAs_w0rdd', '111', 'John', 'Lennon', '1002');
        
        ?>
    </body>
</html>
