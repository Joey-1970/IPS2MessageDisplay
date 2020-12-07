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
		$this->RegisterMessage(0, IPS_KERNELMESSAGE);
		
		$this->ConnectParent("{8D668956-7DB5-49FD-B1A2-149149D99CEB}");
		
		$this->RegisterPropertyBoolean("Open", false);
		$this->RegisterPropertyInteger("Function", 0);
		$this->RegisterPropertyInteger("MessageType", 0);
		$this->RegisterPropertyInteger("VariableID", 0);
		$this->RegisterPropertyInteger("InstanceID", 0);
		$this->RegisterPropertyString("Operator", "<");
		$this->RegisterPropertyString("OperatorString", "==");
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
		$arrayOptions[] = array("label" => "Überwachung einer Instanz", "value" => 1);
		//$arrayOptions[] = array("label" => "Erinnerung nach Uhrzeit", "value" => 2);
		$arrayElements[] = array("type" => "Select", "name" => "Function", "caption" => "Funktion", "options" => $arrayOptions, "onChange" => 'IPS_RequestAction($id,"ChangeFunction",$Function);' );
		
 		$arrayElements[] = array("type" => "Label", "caption" => "_____________________________________________________________________________________________________");
		
		If ($this->ReadPropertyInteger("Function") == 0) {
			// Funktion Überwachung einer Variablen
			$arrayElements[] = array("type" => "Label", "caption" => "Zu überwachende Variable", "visible" => true);
            		$arrayElements[] = array("type" => "SelectVariable", "name" => "VariableID", "caption" => "Variable", "visible" => true, "onChange" => 'IPS_RequestAction($id,"ChangeVariable",$VariableID);'); 
			
			// Select Boolean Variable
			$arrayOptionsBool = array();
			$arrayOptionsBool[] = array("caption" => "Falsch", "value" => false);
			$arrayOptionsBool[] = array("caption" => "Wahr", "value" => true);
			
			// Select Integer und Float
			$arrayOptionsCompare = array();
			$arrayOptionsCompare[] = array("caption" => "<", "value" => "<");
			$arrayOptionsCompare[] = array("caption" => "<=", "value" => "<=");
			$arrayOptionsCompare[] = array("caption" => ">", "value" => ">");
			$arrayOptionsCompare[] = array("caption" => ">=", "value" => ">=");
			$arrayOptionsCompare[] = array("caption" => "==", "value" => "==");
			$arrayOptionsCompare[] = array("caption" => "===", "value" => "===");
			$arrayOptionsCompare[] = array("caption" => "<>", "value" => "<>");
			
			// Select String
			$arrayOptionsCompareString = array();
			$arrayOptionsCompareString[] = array("caption" => "==", "value" => "==");
			$arrayOptionsCompareString[] = array("caption" => "<>", "value" => "<>");

			switch($this->GetVariableType($this->ReadPropertyInteger("VariableID"))) {
				case 0: // Boolean			
					$arrayElements[] = array("type" => "Select", "name" => "ComparativeValueBool", "caption" => "Vergleichswert", "options" => $arrayOptionsBool, "visible" => true);
					$arrayElements[] = array("type" => "Select", "name" => "Operator", "caption" => "Vergleichsart", "options" => $arrayOptionsCompare, "visible" => false);
					$arrayElements[] = array("type" => "NumberSpinner", "name" => "ComparativeValueInt", "caption" => "Vergleichswert", "visible" => false);
					$arrayElements[] = array("type" => "NumberSpinner", "name" => "ComparativeValueFloat", "caption" => "Vergleichswert", "digits" => 1, "visible" => false);
					$arrayElements[] = array("type" => "Select", "name" => "OperatorString", "caption" => "Vergleichsart", "options" => $arrayOptionsCompareString, "visible" => false);
					$arrayElements[] = array("type" => "ValidationTextBox", "name" => "ComparativeValueString", "caption" => "Vergleichswert", "visible" => false);
					break;
				case 1: // Integer			
					$arrayElements[] = array("type" => "Select", "name" => "ComparativeValueBool", "caption" => "Vergleichswert", "options" => $arrayOptionsBool, "visible" => false);
					$arrayElements[] = array("type" => "Select", "name" => "Operator", "caption" => "Vergleichsart", "options" => $arrayOptionsCompare, "visible" => true);
					$arrayElements[] = array("type" => "NumberSpinner", "name" => "ComparativeValueInt", "caption" => "Vergleichswert", "visible" => true);
					$arrayElements[] = array("type" => "NumberSpinner", "name" => "ComparativeValueFloat", "caption" => "Vergleichswert", "digits" => 1, "visible" => false);
					$arrayElements[] = array("type" => "Select", "name" => "OperatorString", "caption" => "Vergleichsart", "options" => $arrayOptionsCompareString, "visible" => false);
					$arrayElements[] = array("type" => "ValidationTextBox", "name" => "ComparativeValueString", "caption" => "Vergleichswert", "visible" => false);
					break;	
				case 2: // Float			
					$arrayElements[] = array("type" => "Select", "name" => "ComparativeValueBool", "caption" => "Vergleichswert", "options" => $arrayOptionsBool, "visible" => false);
					$arrayElements[] = array("type" => "Select", "name" => "Operator", "caption" => "Vergleichsart", "options" => $arrayOptionsCompare, "visible" => true);
					$arrayElements[] = array("type" => "NumberSpinner", "name" => "ComparativeValueInt", "caption" => "Vergleichswert", "visible" => false);
					$arrayElements[] = array("type" => "NumberSpinner", "name" => "ComparativeValueFloat", "caption" => "Vergleichswert", "digits" => 1, "visible" => true);
					$arrayElements[] = array("type" => "Select", "name" => "OperatorString", "caption" => "Vergleichsart", "options" => $arrayOptionsCompareString, "visible" => false);
					$arrayElements[] = array("type" => "ValidationTextBox", "name" => "ComparativeValueString", "caption" => "Vergleichswert", "visible" => false);
					break;
				case 3: // String			
					$arrayElements[] = array("type" => "Select", "name" => "ComparativeValueBool", "caption" => "Vergleichswert", "options" => $arrayOptionsBool, "visible" => false);
					$arrayElements[] = array("type" => "Select", "name" => "Operator", "caption" => "Vergleichsart", "options" => $arrayOptionsCompare, "visible" => false);
					$arrayElements[] = array("type" => "NumberSpinner", "name" => "ComparativeValueInt", "caption" => "Vergleichswert", "visible" => false);
					$arrayElements[] = array("type" => "NumberSpinner", "name" => "ComparativeValueFloat", "caption" => "Vergleichswert", "digits" => 1, "visible" => false);
					$arrayElements[] = array("type" => "Select", "name" => "OperatorString", "caption" => "Vergleichsart", "options" => $arrayOptionsCompareString, "visible" => true);
					$arrayElements[] = array("type" => "ValidationTextBox", "name" => "ComparativeValueString", "caption" => "Vergleichswert", "visible" => true);
					break;
			}
		}
		elseif ($this->ReadPropertyInteger("Function") == 1) {
			$arrayElements[] = array("type" => "Label", "caption" => "Zu überwachende Instanz", "visible" => true);
			$arrayElements[] = array("type" => "SelectInstance", "name" => "InstanceID", "caption" => "Variable", "visible" => true, "onChange" => 'IPS_RequestAction($id,"ChangeInstance",$InstanceID);'); 

		}
		elseif ($this->ReadPropertyInteger("Function") == 2) {
			$arrayElements[] = array("type" => "Label", "caption" => "Funktion Erinnerung", "visible" => true);
		}
		
		// Funktion nach Uhrzeit
		
	
		$arrayElements[] = array("type" => "Label", "caption" => "_____________________________________________________________________________________________________");
		$arrayElements[] = array("type" => "ValidationTextBox", "name" => "MessageText", "caption" => "Nachrichtentext");
		
		$arrayOptions = array();
		$arrayOptions[] = array("caption" => "Information (grün)", "value" => 0);
		$arrayOptions[] = array("caption" => "Alarm (rot)", "value" => 1);
		$arrayOptions[] = array("caption" => "Warnung (gelb)", "value" => 2);
		$arrayOptions[] = array("caption" => "Aufgaben (blau)", "value" => 3);
		$arrayElements[] = array("type" => "Select", "name" => "MessageType", "caption" => "Nachrichten-Typ", "options" => $arrayOptions);
		
		$arrayElements[] = array("type" => "Label", "caption" => "Nachricht manuell löschbar");
		$arrayOptions = array();
		$arrayOptions[] = array("label" => "Ja", "value" => true);
		$arrayOptions[] = array("label" => "Nein", "value" => false);
		$arrayElements[] = array("type" => "Select", "name" => "Removable", "caption" => "Löschbarkeit", "options" => $arrayOptions);

		$arrayElements[] = array("type" => "Label", "caption" => "Automatische Löschung");
		$arrayOptions = array();
		$arrayOptions[] = array("caption" => "Keine automatische Löschung", "value" => 0);
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
		
		If ($this->ReadPropertyInteger("Function") == 0) {
			// Registrierung für die Änderung der Variablen
			If ($this->ReadPropertyInteger("VariableID") > 0) {
				$this->RegisterMessage($this->ReadPropertyInteger("VariableID"), 10603);
			}
		}
		elseif ($this->ReadPropertyInteger("Function") == 1) {
			// Registrierung für die Änderung der Instance
			If ($this->ReadPropertyInteger("InstanceID") > 0) {
				$this->RegisterMessage($this->ReadPropertyInteger("InstanceID"), 10505);
			}
		}
		
		If ($this->ReadPropertyBoolean("Open") == true) {
			$this->Initialize();
			$this->SetStatus(102);
			
		}
		else {
			$this->SetStatus(104);
			
		}	
	}
	
	public function RequestAction($Ident, $Value) 
	{
  		switch($Ident) {
		case "ChangeFunction":
				$this->SendDebug("RequestAction", "Wert: ".$Value, 0);
				switch($Value) {
					case 0: // Variablenüberwachung
						If ($this->ReadPropertyInteger("InstanceID") > 0) {
							switch($this->GetVariableType($this->ReadPropertyInteger("InstanceID"))) {
								case 0: // Boolean
									$this->UpdateFormField('ComparativeValueBool', 'visible', true);
									$this->UpdateFormField('ComparativeValueInt', 'visible', false);
									$this->UpdateFormField('ComparativeValueFloat', 'visible', false);
									$this->UpdateFormField('ComparativeValueString', 'visible', false);
									$this->UpdateFormField('Operator', 'visible', false);
									$this->UpdateFormField('OperatorString', 'visible', false);
									break;
								case 1: // Integer
									$this->UpdateFormField('ComparativeValueBool', 'visible', false);
									$this->UpdateFormField('ComparativeValueInt', 'visible', true);
									$this->UpdateFormField('ComparativeValueFloat', 'visible', false);
									$this->UpdateFormField('ComparativeValueString', 'visible', false);
									$this->UpdateFormField('Operator', 'visible', true);
									$this->UpdateFormField('OperatorString', 'visible', false);
									break;
								case 2: // Float
									$this->UpdateFormField('ComparativeValueBool', 'visible', false);
									$this->UpdateFormField('ComparativeValueInt', 'visible', false);
									$this->UpdateFormField('ComparativeValueFloat', 'visible', true);
									$this->UpdateFormField('ComparativeValueString', 'visible', false);
									$this->UpdateFormField('Operator', 'visible', true);
									$this->UpdateFormField('OperatorString', 'visible', false);
									break;
								case 3: // String
									$this->UpdateFormField('ComparativeValueBool', 'visible', false);
									$this->UpdateFormField('ComparativeValueInt', 'visible', false);
									$this->UpdateFormField('ComparativeValueFloat', 'visible', false);
									$this->UpdateFormField('ComparativeValueString', 'visible', true);
									$this->UpdateFormField('Operator', 'visible', false);
									$this->UpdateFormField('OperatorString', 'visible', true);
									break;
							}
						}
						else {
							$this->UpdateFormField('ComparativeValueBool', 'visible', false);
							$this->UpdateFormField('ComparativeValueInt', 'visible', false);
							$this->UpdateFormField('ComparativeValueFloat', 'visible', false);
							$this->UpdateFormField('ComparativeValueString', 'visible', false);
							$this->UpdateFormField('Operator', 'visible', false);
							$this->UpdateFormField('OperatorString', 'visible', false);
						}
						
						$this->UpdateFormField('InstanceID', 'visible', false);						
						break;
					case 1: // Instanzüberwachung
						$this->UpdateFormField('ComparativeValueBool', 'visible', false);
						$this->UpdateFormField('ComparativeValueInt', 'visible', false);
						$this->UpdateFormField('ComparativeValueFloat', 'visible', false);
						$this->UpdateFormField('ComparativeValueString', 'visible', false);
						$this->UpdateFormField('Operator', 'visible', false);
						$this->UpdateFormField('OperatorString', 'visible', false);
						
						$this->UpdateFormField('InstanceID', 'visible', true);	
						break;
				}
			break;
		case "ChangeVariable":
				$this->SendDebug("RequestAction", "Wert: ".$Value, 0);
				// Registrierung für die Änderung der Variablen
				If ($Value > 0) {
					$this->RegisterMessage($Value, 10603);
					switch($this->GetVariableType($Value)) {
						case 0: // Boolean
							$this->UpdateFormField('ComparativeValueBool', 'visible', true);
							$this->UpdateFormField('ComparativeValueInt', 'visible', false);
							$this->UpdateFormField('ComparativeValueFloat', 'visible', false);
							$this->UpdateFormField('ComparativeValueString', 'visible', false);
							$this->UpdateFormField('Operator', 'visible', false);
							$this->UpdateFormField('OperatorString', 'visible', false);
							break;
						case 1: // Integer
							$this->UpdateFormField('ComparativeValueBool', 'visible', false);
							$this->UpdateFormField('ComparativeValueInt', 'visible', true);
							$this->UpdateFormField('ComparativeValueFloat', 'visible', false);
							$this->UpdateFormField('ComparativeValueString', 'visible', false);
							$this->UpdateFormField('Operator', 'visible', true);
							$this->UpdateFormField('OperatorString', 'visible', false);
							break;
						case 2: // Float
							$this->UpdateFormField('ComparativeValueBool', 'visible', false);
							$this->UpdateFormField('ComparativeValueInt', 'visible', false);
							$this->UpdateFormField('ComparativeValueFloat', 'visible', true);
							$this->UpdateFormField('ComparativeValueString', 'visible', false);
							$this->UpdateFormField('Operator', 'visible', true);
							$this->UpdateFormField('OperatorString', 'visible', false);
							break;
						case 3: // String
							$this->UpdateFormField('ComparativeValueBool', 'visible', false);
							$this->UpdateFormField('ComparativeValueInt', 'visible', false);
							$this->UpdateFormField('ComparativeValueFloat', 'visible', false);
							$this->UpdateFormField('ComparativeValueString', 'visible', true);
							$this->UpdateFormField('Operator', 'visible', false);
							$this->UpdateFormField('OperatorString', 'visible', true);
							break;
					}
				}
				else {
					$this->UpdateFormField('ComparativeValueBool', 'visible', false);
					$this->UpdateFormField('ComparativeValueInt', 'visible', false);
					$this->UpdateFormField('ComparativeValueFloat', 'visible', false);
					$this->UpdateFormField('ComparativeValueString', 'visible', false);
					$this->UpdateFormField('Operator', 'visible', false);
					$this->UpdateFormField('OperatorString', 'visible', false);
				}
			break;
		case "ChangeInstance":
				$this->SendDebug("RequestAction", "Wert: ".$Value, 0);
				// Registrierung für die Änderung der Variablen
				If ($Value > 0) {
					$this->RegisterMessage($Value, 10505);
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
			case 10100:
				// IPS_KERNELSTARTED
				$this->ApplyChanges();
				break;
			case 10603:
				// Änderung an der zu überwachende Variable
				If ($SenderID == $this->ReadPropertyInteger("VariableID")) {
					switch ($this->GetVariableType($SenderID)) {
						case 0: // Boolean
							If (GetValueBoolean($SenderID) == $this->ReadPropertyBoolean("ComparativeValueBool")) {
								$this->Add();
							}
							else {
								$this->Remove();	
							}
							break;
						case 1: // Integer
							If ($this->checkOperator(GetValueInteger($SenderID), $this->ReadPropertyString("Operator"), $this->ReadPropertyInteger("ComparativeValueInt"))) {
								$this->Add();
							}
							else {
								$this->Remove();	
							}
							break;
						case 2: // Float
							If ($this->checkOperator(GetValueFloat($SenderID), $this->ReadPropertyString("Operator"), $this->ReadPropertyFloat("ComparativeValueFloat"))) {
								$this->Add();
							}
							else {
								$this->Remove();	
							}
							break;
						case 3: // String
							If ($this->checkOperator(GetValueString($SenderID), $this->ReadPropertyString("OperatorString"), $this->ReadPropertyString("ComparativeValueString"))) {
								$this->Add();
							}
							else {
								$this->Remove();	
							}
							break;
					}
				}
				break;
			case 10505:
				$this->SendDebug("MessageSink", "SenderID: ".$SenderID, 0);
				If ($SenderID == $this->ReadPropertyInteger("InstanceID")) {
					$Status = (IPS_GetInstance($SenderID)['InstanceStatus']);  
					$this->SendDebug("MessageSink", "Status: ".$Status, 0);
					If ($Status <> 102) {
						$this->Add();
					}
					else {
						$this->Remove();	
					}
				}
				break;
		}
    	}    
	    
	// Beginn der Funktionen
	private function Initialize()
	{
		If ($this->ReadPropertyBoolean("Open") == true) {
			If ($this->ReadPropertyInteger("Function") == 0) {
				switch ($this->GetVariableType($this->ReadPropertyInteger("VariableID"))) {
					case 0: // Boolean
						If (GetValueBoolean($this->ReadPropertyInteger("VariableID")) == $this->ReadPropertyBoolean("ComparativeValueBool")) {
							$this->Add();
						}
						else {
							$this->Remove();	
						}
						break;
					case 1: // Integer
						If ($this->checkOperator(GetValueInteger($this->ReadPropertyInteger("VariableID")), $this->ReadPropertyString("Operator"), $this->ReadPropertyInteger("ComparativeValueInt"))) {
							$this->Add();
						}
						else {
							$this->Remove();	
						}
						break;
					case 2: // Float
						If ($this->checkOperator(GetValueFloat($this->ReadPropertyInteger("VariableID")), $this->ReadPropertyString("Operator"), $this->ReadPropertyFloat("ComparativeValueFloat"))) {
							$this->Add();
						}
						else {
							$this->Remove();	
						}
						break;
					case 3: // String
						If ($this->checkOperator(GetValueString($this->ReadPropertyInteger("VariableID")), $this->ReadPropertyString("OperatorString"), $this->ReadPropertyString("ComparativeValueString"))) {
							$this->Add();
						}
						else {
							$this->Remove();	
						}
						break;
				}
			}
			elseif ($this->ReadPropertyInteger("Function") == 1) {
				$InstanceID = $this->ReadPropertyInteger("InstanceID");
				$Status = (IPS_GetInstance($InstanceID)['InstanceStatus']);  
				$this->SendDebug("Initialize", "Status: ".$Status, 0);
				If ($Status <> 102) {
					$this->Add();
				}
				else {
					$this->Remove();	
				}
			}
		}
	}
	    
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
			If ($this->ReadPropertyString("Page") <> "unbestimmt") {
				$Page = $this->ReadPropertyString("Page");
			}
			else {
				$Page = "";
			}
			
			if (IPS_GetKernelRunlevel() == KR_READY) {
				$this->SendDataToParent(json_encode(Array("DataID"=> "{4F07F8AF-DDF9-A175-6A16-C960F8040723}", 
						"Function" => "Add", "MessageID" => $MessageID, "Text" => $Text, "Expires" => $Expires, "Removable" => $Removable, "Type" => $Type, "Image" => $Image, "WebfrontID" => $WebfrontID, "Page" => $Page )));
			}
		}
	}
	
	public function Remove()
	{
		If ($this->ReadPropertyBoolean("Open") == true) {
			$MessageID = $this->InstanceID;
			if (IPS_GetKernelRunlevel() == KR_READY) {
				$this->SendDataToParent(json_encode(Array("DataID"=> "{4F07F8AF-DDF9-A175-6A16-C960F8040723}", 
						"Function" => "Remove", "MessageID" => $MessageID )));
			}
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
	    
	private function GetIconsList()
	{
	    	$id = IPS_GetInstanceListByModuleID('{B69010EA-96D5-46DF-B885-24821B8C8DBD}')[0];
	    	$Icons = array();
	    	foreach (UC_GetIconList($id) as $Icon) {
			$Icons[] = ['caption' => $Icon, 'value' => $Icon];
	    	}
		sort($Icons);
            	array_unshift($Icons, ['caption' => 'Standard', 'value' => 'Transparent']);
	return $Icons;
	}
}
?>
