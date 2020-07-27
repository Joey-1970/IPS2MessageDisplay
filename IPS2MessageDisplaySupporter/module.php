<?
    // Klassendefinition
    class IPS2MessageDisplaySupporter extends IPSModule 
    {
	public function Destroy() 
	{
		//Never delete this line!
		parent::Destroy();
	}  
	    
	// Überschreibt die interne IPS_Create($id) Funktion
        public function Create() 
        {
            	// Diese Zeile nicht löschen.
            	parent::Create();
		$this->ConnectParent("{8D668956-7DB5-49FD-B1A2-149149D99CEB}");
		
		$this->RegisterPropertyBoolean("Open", false);
		$this->RegisterPropertyInteger("Function", 0);
		$this->RegisterPropertyInteger("MessageType", 0);
		$this->RegisterPropertyInteger("VariableID", 0);
		$this->RegisterPropertyInteger("WebfrontID", 0);
		$this->RegisterPropertyString("MessageText", "");
		$this->RegisterPropertyInteger("Expires", 0);
		$this->RegisterPropertyBoolean("Removable", true);
		$this->RegisterPropertyString("Image", "");
		$this->RegisterPropertyString("Page", "unbestimmt");
		//Status-Variablen anlegen
		
			
		// Webhook einrichten
	

        }
 	
	public function GetConfigurationForm() 
	{ 
		$arrayStatus = array(); 
		$arrayStatus[] = array("code" => 101, "icon" => "inactive", "caption" => "Instanz wird erstellt"); 
		$arrayStatus[] = array("code" => 102, "icon" => "active", "caption" => "Instanz ist aktiv");
		$arrayStatus[] = array("code" => 104, "icon" => "inactive", "caption" => "Instanz ist inaktiv");
				
		$arrayElements = array(); 
		
		$arrayElements[] = array("name" => "Open", "type" => "CheckBox",  "caption" => "Aktiv");
		$arrayElements[] = array("type" => "Label", "label" => "_____________________________________________________________________________________________________");
 		
		$arrayOptions = array();
		$arrayOptions[] = array("label" => "Überwachung einer Variablen", "value" => 0);
		$arrayOptions[] = array("label" => "Erinnerung nach Uhrzeit", "value" => 1);
		$arrayElements[] = array("type" => "Select", "name" => "Function", "caption" => "Funktion", "options" => $arrayOptions, "onChange" => 'IPS_RequestAction($id,"RefreshProfileForm",$Function);' );

 		$arrayElements[] = array("type" => "Label", "label" => "_____________________________________________________________________________________________________");

		// Funktion Überwachung einer Variablen
		$arrayElements[] = array("type" => "Label", "name" => "LabelFunction1", "caption" => "Zu überwachende Variable", "visible" => true);
            	$arrayElements[] = array("type" => "SelectVariable", "name" => "VariableID", "caption" => "Variable", "visible" => true, "onChange" => 'IPS_RequestAction($id,"ChangeVariable",$VariableID);'); 
		
		// Funktion nach Uhrzeit
		
		
		$arrayElements[] = array("type" => "Label", "label" => "_____________________________________________________________________________________________________");
	
		$arrayOptions = array();
		$arrayOptions[] = array("label" => "Information (grün)", "value" => 0);
		$arrayOptions[] = array("label" => "Alarm (rot)", "value" => 1);
		$arrayOptions[] = array("label" => "Warnung (gelb)", "value" => 2);
		$arrayOptions[] = array("label" => "Aufgaben (blau)", "value" => 3);
		$arrayElements[] = array("type" => "Select", "name" => "MessageType", "caption" => "Nachrichten-Typ", "options" => $arrayOptions);

		$arrayOptions = array();
		$arrayOptions[] = array("label" => "Keine Löschung", "value" => 0);
		$arrayOptions[] = array("label" => "10", "value" => 10);
		$arrayOptions[] = array("label" => "30", "value" => 30);
		$arrayOptions[] = array("label" => "60", "value" => 60);
		$arrayElements[] = array("type" => "Select", "name" => "Expires", "caption" => "Automatische Löschung (sek)", "options" => $arrayOptions);
		
		
		$arrayElements[] = array("type" => "Label", "label" => "Auswahl des Webfronts für die SwitchPage-Funktion"); 
		$WebfrontID = Array();
		$WebfrontID = $this->GetWebfrontID();
		$arrayOptions = array();
		$arrayOptions[] = array("label" => "unbestimmt", "value" => 0);
		foreach ($WebfrontID as $ID => $Webfront) {
        		$arrayOptions[] = array("label" => $Webfront, "value" => $ID);
    		}
		$arrayElements[] = array("type" => "Select", "name" => "WebfrontID", "caption" => "Webfront", "options" => $arrayOptions, "onChange" => 'IPS_RequestAction($id,"ChangeWebfront",$WebfrontID);'  );
		
		$PagesArray = array();
		$PagesArray = $this->GetWebfrontPages($this->ReadPropertyInteger("WebfrontID"));
		$arrayOptions = array();
		$arrayOptions[] = array("label" => "unbestimmt", "value" => "unbestimmt");
		foreach ($PagesArray as $ID => $Page) {
        		$arrayOptions[] = array("label" => $Page, "value" => $Page);
    		}
		$arrayElements[] = array("type" => "Select", "name" => "Page", "caption" => "Seiten", "options" => $arrayOptions, "onChange" => 'IPS_RequestAction($id,"ChangeWebfront",$WebfrontID);'  );

		
		$arrayElements[] = array("type" => "Label", "label" => "_____________________________________________________________________________________________________");
            	
		
 		return JSON_encode(array("status" => $arrayStatus, "elements" => $arrayElements)); 		 
 	}       
	   
        // Überschreibt die intere IPS_ApplyChanges($id) Funktion
        public function ApplyChanges() 
        {
            	// Diese Zeile nicht löschen
            	parent::ApplyChanges();
		
		if (IPS_GetKernelRunlevel() == KR_READY) {
				//$this->SendDataToParent(json_encode(Array("DataID"=> "{4F07F8AF-DDF9-A175-6A16-C960F8040723}", 
				//						  "Function" => "set_MaxWatering", "InstanceID" => $this->InstanceID, "MaxWatering" => $MaxWatering )));
			}
		
		If ($this->ReadPropertyBoolean("Open") == true) {
			
			$this->SetStatus(102);
			
		}
		else {
			$this->SetStatus(104);
			
		}	
	}
	
	public function RequestAction($Ident, $Value) 
	{
  		switch($Ident) {
		case "RefreshProfileForm":
				$this->SendDebug("RequestAction", "Wert: ".$Value, 0);
				$this->RefreshProfileForm($Value);
			break;
		case "ChangeVariable":
				$this->SendDebug("RequestAction", "Wert: ".$Value, 0);
				$this->GetVariableType($Value);
				
			break;
		case "ChangeWebfront":
				$this->SendDebug("RequestAction", "ChangeWebfront - Wert: ".$Value, 0);
				If ($Value > 0) {
					$PagesArray = array();
					$PagesArray = $this->GetWebfrontPages($Value);
					$arrayOptions[] = array("label" => "unbestimmt", "value" => "unbestimmt");
					foreach ($PagesArray as $Value) {
						$arrayOptions[] = array("label" => $Value, "value" => $Value);
					}
				}
				else {
					$arrayOptions[] = array("label" => "unbestimmt", "value" => "unbestimmt");
				}
				$this->UpdateFormField('Page', 'value', "unbestimmt");
				$this->UpdateFormField('Page', 'options', json_encode($arrayOptions));
			break;
	        default:
	            throw new Exception("Invalid Ident");
	    	}
	}
	    
	// Beginn der Funktionen

	    
	private function GetVariableType(int $VariableID) 
	{
		$Result = false;
		if (IPS_VariableExists($VariableID) == true) {
			$InformationArray = array();
			$InformationsArray = IPS_GetVariable($VariableID);
			$VariableType = $InformationsArray["VariableType"]; // Variablentyp (0: Boolean, 1: Integer, 2: Float, 3: String)
			$VariableTypeArray = array("Boolean", "integer", "Float", "String");
			$this->SendDebug("Get Variable Type", "Variablen Typ: ".$VariableTypeArray[$Value], 0);
			$Result = $VariableType;
		}
	return $Result;
	}
	    
	public function Add()
	{
		If ($this->ReadPropertyBoolean("Open") == true) {
			$MessageID = $this->InstanceID;
			$Text = $this->ReadPropertyString("MessageText");
			$Expires = $this->ReadPropertyInteger("Expires");
			$Removable = $this->ReadPropertyBoolean("Removable");
			$Type = $this->ReadPropertyInteger("MessageType");
			$WebfrontID = $this->ReadPropertyInteger("WebfrontID");
			$Page = $this->ReadPropertyString("Page");
			
			$this->SendDataToParent(json_encode(Array("DataID"=> "{4F07F8AF-DDF9-A175-6A16-C960F8040723}", 
						"Function" => "Add", "MessageID" => $MessageID, "Text" => $Text, "Expires" => $Expires, "Removable" => $Removable, "Type" => $Type, "Image" => $Image, "WebfrontID" => $WebfrontID, "Page" => $Page )));

		}
	}
	
	private function RefreshProfileForm($Model)
    	{
        	
        	//$this->UpdateFormField('Output', 'options', json_encode($arrayOptions));
		//$this->UpdateFormField('Output_AP', 'options', json_encode($arrayOptions));
    	}    
	    
	private function GetWebfrontID()
	{
    		$guid = "{3565B1F2-8F7B-4311-A4B6-1BF1D868F39E}"; // Webfront Konfigurator
    		//Auflisten
    		$WebfrontArray = (IPS_GetInstanceListByModuleID($guid));
    		$Result = array();
    		foreach ($WebfrontArray as $Webfront) {
        		$Result[$Webfront] = IPS_GetName($Webfront);
    		}
	return $Result;   
	}    
	    
  	private function GetWebfrontPages($WebfrontID)
	{
    		$PagesArray = array();
    		If ($WebfrontID > 0) {
			$config = IPS_GetConfiguration($WebfrontID);
			$WebfrontData = json_decode($config);
			$ItemsData = json_decode($WebfrontData->Items);
			foreach ($ItemsData as $Value) {
				$PagesArray[] = $Value->ID;
			}
		}
	return $PagesArray;
	}
}
?>
