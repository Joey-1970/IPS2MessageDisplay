<?
    // Klassendefinition
    class IPS2MessageDisplay extends IPSModule 
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
		
		
		//Status-Variablen anlegen
		$this->RegisterVariableString("Messages", "Meldungen", "~HTMLBox", 10);
		
		
        }
 	
	public function GetConfigurationForm() 
	{ 
		$arrayStatus = array(); 
		$arrayStatus[] = array("code" => 101, "icon" => "inactive", "caption" => "Instanz wird erstellt"); 
		$arrayStatus[] = array("code" => 102, "icon" => "active", "caption" => "Instanz ist aktiv");
		$arrayStatus[] = array("code" => 104, "icon" => "inactive", "caption" => "Instanz ist inaktiv");
				
		$arrayElements = array(); 
		
		$arrayElements[] = array("name" => "Open", "type" => "CheckBox",  "caption" => "Aktiv");
		
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
	

	    
	// Beginn der Funktionen
	
	
	    
	
	

	
}
?>
