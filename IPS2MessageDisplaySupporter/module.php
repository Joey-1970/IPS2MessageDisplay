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
		$this->RegisterPropertyString("Operator", "<");
		$this->RegisterPropertyBoolean("ComparativeValueBool", false);
		$this->RegisterPropertyInteger("ComparativeValueInt", 0);
		$this->RegisterPropertyFloat("ComparativeValueFloat", 0.0);
		$this->RegisterPropertyString("ComparativeValueString", "");
		$this->RegisterPropertyInteger("WebfrontID", 0);
		$this->RegisterPropertyString("MessageText", "");
		$this->RegisterPropertyInteger("Expires", 0);
		$this->RegisterPropertyBoolean("Removable", true);
		$this->RegisterPropertyString("Image", "Transparent");
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
		$arrayElements[] = array("type" => "Label", "caption" => "_____________________________________________________________________________________________________");
 		
		$arrayOptions = array();
		$arrayOptions[] = array("label" => "Überwachung einer Variablen", "value" => 0);
		//$arrayOptions[] = array("label" => "Erinnerung nach Uhrzeit", "value" => 1);
		$arrayElements[] = array("type" => "Select", "name" => "Function", "caption" => "Funktion", "options" => $arrayOptions, "onChange" => 'IPS_RequestAction($id,"RefreshProfileForm",$Function);' );

 		$arrayElements[] = array("type" => "Label", "caption" => "_____________________________________________________________________________________________________");

		// Funktion Überwachung einer Variablen
		$arrayElements[] = array("type" => "Label", "name" => "LabelFunction1", "caption" => "Zu überwachende Variable", "visible" => true);
            	$arrayElements[] = array("type" => "SelectVariable", "name" => "VariableID", "caption" => "Variable", "visible" => true, "onChange" => 'IPS_RequestAction($id,"ChangeVariable",$VariableID);'); 
			// Boolean Variable
			$arrayOptions = array();
			$arrayOptions[] = array("caption" => "Falsch", "value" => false);
			$arrayOptions[] = array("caption" => "Wahr", "value" => true);
			$arrayElements[] = array("type" => "Select", "name" => "ComparativeValueBool", "caption" => "Nachrichten-Erstellung", "options" => $arrayOptions, "visible" => true);
			
			// Integer und Float
			$arrayOptions = array();
			$arrayOptions[] = array("caption" => "<", "value" => "<");
			$arrayOptions[] = array("caption" => "<=", "value" => "<=");
			$arrayOptions[] = array("caption" => ">", "value" => ">");
			$arrayOptions[] = array("caption" => ">=", "value" => ">=");
			$arrayOptions[] = array("caption" => "==", "value" => "==");
			$arrayOptions[] = array("caption" => "===", "value" => "===");
			$arrayOptions[] = array("caption" => "<>", "value" => "<>");
			$arrayElements[] = array("type" => "Select", "name" => "Operator", "caption" => "Vergleichsart", "options" => $arrayOptions, "visible" => true);

			// Integer
			$arrayElements[] = array("type" => "NumberSpinner", "name" => "ComparativeValueInt", "caption" => "Vergleichswert", "visible" => true);
		
			// Float
			$arrayElements[] = array("type" => "NumberSpinner", "name" => "ComparativeValueFloat", "caption" => "Vergleichswert", "digits" => 1, "visible" => true);
		
			// String
			$arrayElements[] = array("type" => "ValidationTextBox", "name" => "ComparativeValueString", "caption" => "Vergleichswert", "visible" => true);
		
		// Funktion nach Uhrzeit
		
	
		$arrayElements[] = array("type" => "Label", "caption" => "_____________________________________________________________________________________________________");
		$arrayElements[] = array("type" => "ValidationTextBox", "name" => "MessageText", "caption" => "Nachricht");
		
		$arrayOptions = array();
		$arrayOptions[] = array("caption" => "Information (grün)", "value" => 0);
		$arrayOptions[] = array("caption" => "Alarm (rot)", "value" => 1);
		$arrayOptions[] = array("caption" => "Warnung (gelb)", "value" => 2);
		$arrayOptions[] = array("caption" => "Aufgaben (blau)", "value" => 3);
		$arrayElements[] = array("type" => "Select", "name" => "MessageType", "caption" => "Nachrichten-Typ", "options" => $arrayOptions);

		$arrayOptions = array();
		$arrayOptions[] = array("caption" => "Keine Löschung", "value" => 0);
		$arrayOptions[] = array("caption" => "10", "value" => 10);
		$arrayOptions[] = array("caption" => "30", "value" => 30);
		$arrayOptions[] = array("caption" => "60", "value" => 60);
		$arrayElements[] = array("type" => "Select", "name" => "Expires", "caption" => "Automatische Löschung (sek)", "options" => $arrayOptions);
		
		
		$arrayElements[] = array("type" => "Label", "caption" => "Auswahl des Webfronts und der Seite für die Sprung-Funktion"); 
		$WebfrontID = array();
		$WebfrontID = $this->GetWebfrontID();
		$arrayWebfronts = array();
		$arrayWebfronts[] = array("caption" => "unbestimmt", "value" => 0);
		foreach ($WebfrontID as $ID => $Webfront) {
        		$arrayWebfronts[] = array("caption" => $Webfront, "value" => $ID);
    		}
		
		$PagesArray = array();
		$PagesArray = $this->GetWebfrontPages($this->ReadPropertyInteger("WebfrontID"));
		$arrayPages = array();
		$arrayPages[] = array("caption" => "unbestimmt", "value" => "unbestimmt");
		foreach ($PagesArray as $ID => $Page) {
        		$arrayPages[] = array("caption" => $Page, "value" => $Page);
    		}
		
		$ArrayRowLayout = array();
		$ArrayRowLayout[] = array("type" => "Select", "name" => "WebfrontID", "caption" => "Webfront", "options" => $arrayWebfronts, "onChange" => 'IPS_RequestAction($id,"ChangeWebfront",$WebfrontID);');
		$ArrayRowLayout[] = array("type" => "Select", "name" => "Page", "caption" => "Seite", "options" => $arrayPages);
		$arrayElements[] = array("type" => "RowLayout", "items" => $ArrayRowLayout);
		
		$arrayElements[] = array("type" => "Label", "caption" => "Auswahl des Icons"); 
		$IconsArray = array();
		$IconsArray = $this->GetIconsList();
		$arrayOptions = array();
		foreach ($IconsArray as $Value) {
			$arrayOptions[] = array("caption" => $Value['caption'], "value" => $Value['value']);
		}
		$arrayElements[] = array("type" => "Select", "name" => "Image", "caption" => "Icon", "options" => $arrayOptions);

		
		$arrayElements[] = array("type" => "Label", "caption" => "_____________________________________________________________________________________________________");
            	
		
 		return JSON_encode(array("status" => $arrayStatus, "elements" => $arrayElements)); 		 
 	}       
	   
        // Überschreibt die intere IPS_ApplyChanges($id) Funktion
        public function ApplyChanges() 
        {
            	// Diese Zeile nicht löschen
            	parent::ApplyChanges();
		
		if (IPS_GetKernelRunlevel() == KR_READY) {
		
		}
		
		// Registrierung für die Änderung der Variablen
		If ($this->ReadPropertyInteger("VariableID") > 0) {
			$this->RegisterMessage($this->ReadPropertyInteger("VariableID"), 10603);
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
				// Registrierung für die Änderung der Variablen
				If ($this->ReadPropertyInteger("VariableID") > 0) {
					$this->RegisterMessage($this->ReadPropertyInteger("VariableID"), 10603);
				}
				switch($this->GetVariableType($Value)) {
					case 0: // Boolean
						$this->UpdateFormField('ComparativeValueBool', 'visible', true);
						$this->UpdateFormField('ComparativeValueInt', 'visible', false);
						$this->UpdateFormField('ComparativeValueFloat', 'visible', false);
						$this->UpdateFormField('ComparativeValueString', 'visible', false);
						$this->UpdateFormField('Operator', 'visible', false);
						break;
					case 1: // Integer
						$this->UpdateFormField('ComparativeValueBool', 'visible', false);
						$this->UpdateFormField('ComparativeValueInt', 'visible', true);
						$this->UpdateFormField('ComparativeValueFloat', 'visible', false);
						$this->UpdateFormField('ComparativeValueString', 'visible', false);
						$this->UpdateFormField('Operator', 'visible', true);
						break;
					case 2: // Float
						$this->UpdateFormField('ComparativeValueBool', 'visible', false);
						$this->UpdateFormField('ComparativeValueInt', 'visible', false);
						$this->UpdateFormField('ComparativeValueFloat', 'visible', true);
						$this->UpdateFormField('ComparativeValueString', 'visible', false);
						$this->UpdateFormField('Operator', 'visible', true);
						break;
					case 3: // String
						$this->UpdateFormField('ComparativeValueBool', 'visible', false);
						$this->UpdateFormField('ComparativeValueInt', 'visible', false);
						$this->UpdateFormField('ComparativeValueFloat', 'visible', false);
						$this->UpdateFormField('ComparativeValueString', 'visible', true);
						$this->UpdateFormField('Operator', 'visible', true);
						break;
				}
			break;
		case "ChangeWebfront":
				$this->SendDebug("RequestAction", "ChangeWebfront - Wert: ".$Value, 0);
				$this->UpdateFormField('Page', 'value', "unbestimmt");
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
				$this->UpdateFormField('Page', 'options', json_encode($arrayOptions));
			break;
	        default:
	            throw new Exception("Invalid Ident");
	    	}
	}
	
	public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    	{
		switch ($Message) {
			case 10603:
				// Änderung der Ist-Temperatur, die Temperatur aus dem angegebenen Sensor in das Modul kopieren
				If ($SenderID == $this->ReadPropertyInteger("VariableID")) {
					switch ($this->GetVariableType($SenderID)) {
						case 0: // Boolean
							If (GetValueBoolean($SenderID) == $this->ReadPropertyBoolean("ActionBoolean")) {
								$this->Add();
							}
							else {
								$this->Remove();	
							}
							break;
					}
				}
				break;
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
			$VariableTypeArray = array("Boolean", "Integer", "Float", "String");
			$this->SendDebug("Get Variable Type", "Variablen Typ: ".$VariableTypeArray[$VariableType], 0);
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
			If ($this->ReadPropertyString("Image") == "Transparent") {
				$Image = "";
			}
			   else {
				$Image = $this->ReadPropertyString("Image");
			}
			$WebfrontID = $this->ReadPropertyInteger("WebfrontID");
			$Page = $this->ReadPropertyString("Page");
			
			$this->SendDataToParent(json_encode(Array("DataID"=> "{4F07F8AF-DDF9-A175-6A16-C960F8040723}", 
						"Function" => "Add", "MessageID" => $MessageID, "Text" => $Text, "Expires" => $Expires, "Removable" => $Removable, "Type" => $Type, "Image" => $Image, "WebfrontID" => $WebfrontID, "Page" => $Page )));

		}
	}
	
	public function Remove()
	{
		If ($this->ReadPropertyBoolean("Open") == true) {
			$MessageID = $this->InstanceID;
			$this->SendDataToParent(json_encode(Array("DataID"=> "{4F07F8AF-DDF9-A175-6A16-C960F8040723}", 
						"Function" => "Remove", "MessageID" => $MessageID )));

		}
	}    
	
	private function checkOperator($value1, $operator, $value2) 
	{
    		switch ($operator) {
        		case '<': // Less than
            			return $value1 < $value2;
        		case '<=': // Less than or equal to
            			return $value1 <= $value2;
        		case '>': // Greater than
            			return $value1 > $value2;
        		case '>=': // Greater than or equal to
            			return $value1 >= $value2;
        		case '==': // Equal
            			return $value1 == $value2;
        		case '===': // Identical
            			return $value1 === $value2;
        		case '<>': // Not equal
            			return $value1 != $value2;
        		default:
            			return FALSE;
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
	    
	function GetIconsList()
	{
	    	$id = IPS_GetInstanceListByModuleID('{B69010EA-96D5-46DF-B885-24821B8C8DBD}')[0];
	    	$Icons = array();
	    	$Icons[] = ['caption' => 'Standard', 'value' => 'Transparent'];
	    	foreach (UC_GetIconList($id) as $Icon) {
			$Icons[] = ['caption' => $Icon, 'value' => $Icon];
	    	}
	return $Icons;
	}
}
?>
