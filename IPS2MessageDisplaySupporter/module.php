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
		$this->RegisterPropertyBoolean("Open", false);
		$this->RegisterPropertyInteger("Function", 0);
		$this->RegisterPropertyInteger("MessageType", 0);
		$this->RegisterPropertyInteger("VariableID", 0);
		$this->RegisterPropertyInteger("WebfrontID", 0);
		
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
            	$arrayElements[] = array("type" => "SelectVariable", "name" => "VariableID", "caption" => "Variable", "visible" => true, "onChange" => 'IPS_RequestAction($id,"ChangeVariable",$Function);'); 
		
		// Funktion nach Uhrzeit
		
		
		$arrayElements[] = array("type" => "Label", "label" => "_____________________________________________________________________________________________________");
	
		$arrayOptions = array();
		$arrayOptions[] = array("label" => "Information (grün)", "value" => 0);
		$arrayOptions[] = array("label" => "Alarm (rot)", "value" => 1);
		$arrayOptions[] = array("label" => "Warnung (gelb)", "value" => 2);
		$arrayOptions[] = array("label" => "Aufgaben (blau)", "value" => 3);
		$arrayElements[] = array("type" => "Select", "name" => "MessageType", "caption" => "Nachrichten-Typ", "options" => $arrayOptions);

		
		
		
		$arrayElements[] = array("type" => "Label", "label" => "Auswahl des Webfronts für die SwitchPage-Funktion"); 
		$WebfrontID = Array();
		$WebfrontID = $this->GetWebfrontID();
		$arrayOptions = array();
		$arrayOptions[] = array("label" => "unbestimmt", "value" => 0);
		foreach ($WebfrontID as $ID => $Webfront) {
        		$arrayOptions[] = array("label" => $Webfront, "value" => $ID);
    		}
		$arrayElements[] = array("type" => "Select", "name" => "WebfrontID", "caption" => "Webfront", "options" => $arrayOptions );
		$arrayElements[] = array("type" => "Label", "label" => "_____________________________________________________________________________________________________");
            	
		
 		return JSON_encode(array("status" => $arrayStatus, "elements" => $arrayElements)); 		 
 	}       
	   
        // Überschreibt die intere IPS_ApplyChanges($id) Funktion
        public function ApplyChanges() 
        {
            	// Diese Zeile nicht löschen
            	parent::ApplyChanges();
		
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
				$this->RefreshProfileForm($Value);
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
			$Result = $VariableType;
		}
	return $Result;
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
	    
  
}
?>
