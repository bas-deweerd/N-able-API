<?php

/**
 * Class which allows communication with the N-able API.
 * See @link: https://rmm.cloudnet.services/dms/javadocei2/index.html for API JavaDoc
 * @author bas de weerd
 *
 */
class api {
    private $username;
    private $password;
    private $wsdl;
    private $soapClient;

    public function __construct() {
        $this->username = 'productadmin@n-able.com';
        $this->password = '';

        $this->wsdl = 'https://rmm.cloudnet.services/dms2/services2/ServerEI2?wsdl';
        $this->soapClient = new Soapclient($this->wsdl, array(
            'soap_version' => SOAP_1_2,
            'trace' => TRUE
                )
        );
    }

    /**
     * Gets a list of all the customers and sites corresponding to the user.
     * @return type
     */
    public function getCustomerAndSitesList() {
        $params = array(
            'username' => $this->username,
            'password' => $this->password,
            'settings' => array(
                array('key' => 'listSOs', 'value' => "false")
            )
        );
        return $this->soapClient->customerList($params);
    }
    
    /**
     * This function creates a list of customers/sites that are associated with the selected Customer/SO.
     * @param unknown $customerID
     * @return site[] An array of sites
     */
    public function getCustomerListChildren($customerID){
        $params = array(
            'username' => $this->username,
            'password' => $this->password,
            'settings' => array(
                array('key' => 'customerID', 'value' => $customerID)
            )
        );
        $result = $this->soapClient->customerListChildren($params);
        $sites = array();
        
        foreach ($result->return as $jsonsite) {
            $site = new site();
            foreach ($jsonsite->info as $keyvaluepair) {
                $key = $keyvaluepair->key;
                if(!isset($keyvaluepair->value)){
                    break 1;
                }
                $value = $keyvaluepair->value;
                switch($key){
                    case "customer.customerid":
                        $site->siteid = $value;
                        break;
                    case "customer.customername":
                        $site->sitename = $value;
                        break;
                    case "customer.street1":
                        $site->siteadress->street1 = $value;
                        break;
                    case "customer.street2":
                        $site->siteadress->street2 = $value;
                        break;
                    case "customer.city":
                        $site->siteadress->city = $value;
                        break;
                    case "customer.stateprov":
                        $site->siteadress->province = $value;
                        break;
                    case "customer.postalcode":
                        $site->siteadress->zip = $value;
                        break;
                    case "customer.county":
                        $site->siteadress->country = $value;
                        break;
                    case "customer.phone":
                        $site->telephone = $value;
                        break;
                    case "customer.contactfirstname":
                        $site->sitecontact->contact->firstname = $value;
                        break;
                    case "customer.contactlastname":
                        $site->sitecontact->contact->lastname = $value;
                        break;
                    case "customer.contacttitle":
                        $site->sitecontact->title = $value;
                        break;
                    case "customer.contactdepartment":
                        $site->sitecontact->contact->department = $value;
                        break;
                    case "customer.contactphonenumber":
                        $site->sitecontact->contact->telephone = $value;
                        break;
                    case "customer.contactext":
                        $site->sitecontact->contact->telephoneextension = $value;
                        break;
                    case "customer.contactemail":
                        $site->sitecontact->contact->email = $value;
                        break;
                    case "customer.parentid":
                        $site->customer->customerid = $value;
                        break;
                    case "customer.parentcustomername":
                        $site->customer->customername = $value;
                        break;
                }
            }
            array_push($sites, $site);
        }
        return $sites;
    }

    /**
     * Gets a list of all the service organisations corresponding to the user.
     * @return type
     */
    public function getServiceOrganisationList() {
        $params = array(
            'username' => $this->username,
            'password' => $this->password,
            'settings' => array(
                array('key' => 'listSOs', 'value' => "true")
            )
        );
        return $this->soapClient->customerList($params);
    }

    /**
     * Gets a list of all the devices corresponsding to the ID of the user.
     * @param User's id to get devices from $id
     * @return type
     */
    public function getDevices() {
        $params = array(
            'username' => $this->username,
            'password' => $this->password,
            'settings' => array(
                array('key' => 'customerID', 'value' => '1')
            )
        );
        return $this->soapClient->deviceList($params);
    }

    /**
     * Gets version information.
     * @return type
     */
    public function getVersion() {
        return $this->soapClient->versionInfoGet();
        //    foreach($item in $response->return)
        //    {
        //        $item->PKey
        //        $item->PValue
        //    }
        //return $soapClient->VersionInfoGet->return[40]->PValue;
    }

    /**
     * Adds a new service organisation (SO).
     * @param Desired name for the new SO $name
     * @param SO contact's first name $firstName
     * @param SO contact's last name $lastName
     */
    public function addServiceOrganisation($name, $firstName, $lastName) {
        $params = array(
            'username' => $this->username,
            'password' => $this->password,
            'settings' => array(
                array('key' => 'soname', 'value' => $name),
                array('key' => 'firstname', 'value' => $parentId), // 50 = COPACO CLOUD B.V. ID (rmm.cloudnet.services),  152 = 2tCloud (ncentral.weritech.nl)
                array('key' => 'lastname', 'value' => $licenseType)
            )
        );
        $this->soapClient->customerAdd($params);
    }

    /**
     * Adds a new customer or site under the specified SO/customer.
     * Does not check input validity.
     * @param customerName - Desired name for the new customer or site. Maximum of 120 characters.
     * @param telephone - Phone number of the customer/site
     * @param zip/postalcode - (Value) Customer's zip/ postal code
     * @param street1 - (Value) Address line 1 for the customer. Maximum of 100 characters
     * @param street2 - (Value) Address line 2 for the customer. Maximum of 100 characters
     * @param city - (Value) Customer/site's city
     * @param province - (Value) Customer/site's state/ province
     * @param country - (Value) Customer's country. Two character country code, see http://en.wikipedia.org/wiki/ISO_3166-1_alpha-2 for a list of country codes
     * @param contactTitle - Customer/site contact's title
     * @param contactFirstName - Customer/site contact's first name
     * @param contactLastName - Customer/site contact's first name
     * @param contactTelephone - Customer/site contact's telephone number
     * @param contactTelephoneExtension - Customer/site contact's telephone extension
     * @param contactEmail - Customer/site contact's email. Maximum of 100 characters
     * @param contactDepartment - Customer/site contact's department
     * @param parentID - ID of the parent (SO/customer)
     */
    public function addCustomer(
    		$customerName,
    		$telephone,
    		$zip,
    		$street1,
    		$street2,
    		$city,
    		$province,
    		$country,
    		$contactTitle,
    		$contactFirstName,
    		$contactLastName,
    		$contactTelephone,
    		$contactTelephoneExtension,
    		$contactEmail,
    		$contactDepartment,
    		$parentID
    		){
        $params = array(
            'username' => $this->username,
            'password' => $this->password,
            'settings' => array(
                array('key' => 'customername',		'value' => $customerName),
            	array('key' => 'telephone', 		'value' => $telephone),
            	array('key' => 'zip/postalcode', 	'value' => $zip),
            	array('key' => 'street1', 			'value' => $street1),
            	array('key' => 'street2', 			'value' => $street2),
            	array('key' => 'city', 				'value' => $city),
            	array('key' => 'state/province', 	'value' => $province),
            	array('key' => 'country', 			'value' => $country),
            	array('key' => 'title', 			'value' => $contactTitle),
            	array('key' => 'firstname', 		'value' => $contactFirstName),
            	array('key' => 'lastname', 			'value' => $contactLastName),
            	array('key' => 'contact_telephone', 'value' => $contactTelephone),
            	array('key' => 'ext', 				'value' => $contactTelephoneExtension),
            	array('key' => 'email', 			'value' => $contactEmail),
            	array('key' => 'department', 		'value' => $contactDepartment),      	
            	array('key' => 'parentid', 			'value' => $parentID) // 50 = COPACO CLOUD B.V. ID (rmm.cloudnet.services),  152 = 2tCloud (ncentral.weritech.nl)
            )
        );
        $this->soapClient->customerAdd($params);
    }

    /**
     * Adds a new user to specified SO/customer/site.
     * Does NOT check input validity.
     * @param Login email for the new user. Maximum of 100 characters $email
     * @param Password for the new user. Must meet configured password complexity level $password
     * @param The customerID of the site/customer/SO that the user is associated with $customerId
     * @param User's first name. $firstName
     * @param User's last name $lastName
     * @return The ID number of the new user
     */
    public function addUser(
    		$email,
    		$password,
    		$customerId,
    		$firstName,
    		$lastName,
                $userroleid
    		){
        $params = array(
            'username' => $this->username,
            'password' => $this->password,
            'settings' => array(
                array('key' => 'email', 		'value' => $email),
                array('key' => 'password', 		'value' => $password),
                array('key' => 'customerID', 		'value' => '111'),
                array('key' => 'firstname', 		'value' => $firstName),
                array('key' => 'lastname', 		'value' => $lastName),
                array('key' => 'userroleID',            'value' => $userroleid),
                array('key' => 'accessgroupID',         'value' => $accessgroupid)
            )
        );
        $this->soapClient->userAdd($params);
    }
    
    public function addUserTest(){
        
        $username2 = 'bas.de.weerd@copaco.com';
        $password2 = 'NC3ntr@l';

        $wsdl2 = 'https://ncentral.weritech.nl/dms2/services2/ServerEI2?wsdl';
        $soapClient2 = new Soapclient($wsdl2, array(
            'soap_version' => SOAP_1_2,
            'trace' => TRUE
            )
        );
        
        $params = array(
            'username' => $username2,
            'password' => $password2,
            'settings' => array(
                array('key' => 'email',         'value'=> 'testmail@mail.com'),
                array('key' => 'password',      'value'=> 'p@sSw0rd'),
                array('key' => 'customerID',    'value'=> '158'), //TEST CUSTOMER
                array('key' => 'firstname',     'value'=> 'firstname'),
                array('key' => 'lastname',      'value'=> 'lastname'),
                array('key' => 'type',          'value'=> 'Admin')
            )
        );
        $soapClient2->userAdd($params);
    }

    public function getActiveIssues() {

        $params = array(
            'username' => $this->username,
            'password' => $this->password,
            'settings' => array(
                array('key' => 'customerid', 'value' => '1') // Using 1 does NOT retrieve all SOs / customers / sites
            )
        );
        return $this->soapClient->activeIssuesList($params);
    }
    
    public function getAllAPIFunctions(){
        return $this->soapClient->__getFunctions();
    }
    
    public function getUserRoles(){
        $params = array(
            'username' => $this->username,
            'password' => $this->password,
            'settings' => array('key' => 'customerID', 'value' => '111')
        );
        return $this->soapClient->userRoleList($params);
    }
    
    public function getAccessGroups(){
        $params = array(
            'username' => $this->username,
            'password' => $this->password,
            'settings' => array('key' => 'customerID', 'value' => '111')
        );
        return $this->soapClient->accessGroupList($params);
    }
    
    public function getDevice($id){
        $params = array(
            'username' => $this->username,
            'password' => $this->password,
            'settings' => array('key' => 'deviceID', 'value' => $id)
        );
        return $this->soapClient->deviceGet($params);
    }
    
    public function getDeviceStatus($id){
        $params = array(
            'username' => $this->username,
            'password' => $this->password,
            'settings' => array('key' => 'deviceID', 'value' => $id)
        );
        return $this->soapClient->deviceGetStatus($params);
    }
    
    public function deleteCustomer($id){
        $params = array(
            'username' => $this->username,
            'password' => $this->password,
            'settings' => array('key' => 'customerID', 'value' => $id)
        );
        return $this->soapClient->customerDelete($params);
    }
}
?>
