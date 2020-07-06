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
		$this->RegisterAttributeString("MessageData", ""); 
		
		//Status-Variablen anlegen
		$this->RegisterVariableString("Messages", "Meldungen", "~HTMLBox", 10);
			
		// Webhook einrichten
		$this->RegisterHook("/hook/MessageDisplay_".$this->InstanceID);
		
		$MessageData = array();
		$this->WriteAttributeString("MessageData", serialize($MessageData)); 

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
			$this->RenderData($MessageData);
			$this->SetStatus(102);
			
		}
		else {
			$this->SetStatus(104);
			
		}	
	}
	

	    
	// Beginn der Funktionen
	public function Add(int $MessageID, string $Text, int $Expires, bool $Removable, int $Type, string $Image, string $Page) 
	{
		If ($this->ReadPropertyBoolean("Open") == true) {
			$MessageData = array();
			$MessageData = unserialize($this->ReadAttributeString("MessageData"));
			$MessageData[$MessageID]["Text"] = $Text;
			$MessageData[$MessageID]["Expires"] = $Expires;
			$MessageData[$MessageID]["Removable"] = $Removable;
			$MessageData[$MessageID]["Type"] = $Type;
			$MessageData[$MessageID]["Image"] = $Image;
			$MessageData[$MessageID]["Page"] = $Page;
			$MessageData[$MessageID]["Timestamp"] = time();
			$this->WriteAttributeString("MessageData", serialize($MessageData));
			$this->SendDebug("Add", "Message ".$MessageID." wurde hinzugefuegt", 0);
			$this->RenderData($MessageData);
		}
	}
	    
	public function Remove(int $MessageID) 
	{
		If ($this->ReadPropertyBoolean("Open") == true) {
			$MessageData = array();
			$MessageData = unserialize($this->ReadAttributeString("MessageData"));
			If (is_array($MessageData)) {
				if (array_key_exists($MessageID, $MessageData)) {
					unset($MessageData[$MessageID]);
					$this->SendDebug("Remove", "Message ".$MessageID." wurde entfernt", 0);
				}
				else {
					$this->SendDebug("Remove", "Message ".$MessageID." wurde nicht gefunden", 0);
				}
			}
			
			$this->WriteAttributeString("MessageData", serialize($MessageData));
			$this->RenderData($MessageData);
		}
	}
	    
	public function RemoveAll(int $MessageID) 
	{
		If ($this->ReadPropertyBoolean("Open") == true) {
			$MessageData = array();
			$this->WriteAttributeString("MessageData", serialize($MessageData));
			$this->SendDebug("RemoveAll", "Alle Messages wurde entfernt", 0);
		}
	}
	
	public function RemoveType(int $Type) 
	{
		If ($this->ReadPropertyBoolean("Open") == true) {
			
		}
	}
	
	/*
		case 'WebHook':
		$result = 0;
		switch ($_GET['action']) {
		    case 'remove':
		      $number = isset($_GET['number']) ? $_GET['number'] : -1;
		      if ($number > 0) {
			  $result = removeMessage($number);
		      }
		      break;
		    case 'switch':
		      $page = isset($_GET['page']) ? $_GET['page'] : '';
		      if (is_string($page) && $page !='') {
			  $result = switchPage($wfc, $page);
		      }
		      break;
	    }
    	*/
	    
	protected function ProcessHookData() 
	{		
		If ($this->ReadPropertyBoolean("Open") == true) {
			switch ($_GET['action']) {
			    	case 'remove':
			      		$MessageID = isset($_GET['number']) ? $_GET['number'] : -1;
			      		if ($number > 0) {
				  		$this->Remove($MessageID);
			      		}
			      		break;
			    case 'switch':
			      		$Page = isset($_GET['page']) ? $_GET['page'] : '';
			      		if (is_string($page) && $page !='') {
				  		switchPage($WFC, $Page);
			      		}
			      break;
			}
		}
	}       
	    
	private function RenderData($MessageData)		
	{
		// Etwas CSS und HTML
		$style = "";
		$style .= '<style type="text/css">';
		$style .= 'table { width:100%; border-collapse: collapse; }';
		$style .= 'td.fst { width: 36px; padding: 2px; border-left: 1px solid rgba(255, 255, 255, 0.2); border-top: 1px solid rgba(255, 255, 255, 0.1); }';
		$style .= 'td.mid { padding: 2px;  border-top: 1px solid rgba(255, 255, 255, 0.1); }';
		$style .= 'td.lst { width: 42px; text-align:center; padding: 2px;  border-right: 1px solid rgba(255, 255, 255, 0.2); border-top: 1px solid rgba(255, 255, 255, 0.1); }';
		$style .= 'tr:last-child { border-bottom: 1px solid rgba(255, 255, 255, 0.2); }';
		$style .= '.blue { padding: 5px; color: rgb(255, 255, 255); background-color: rgb(0, 0, 255); background-image: linear-gradient(top,rgba(0,0,0,0) 0,rgba(0,0,0,0.3) 50%,rgba(0,0,0,0.3) 100%); background-image: -o-linear-gradient(top,rgba(0,0,0,0) 0,rgba(0,0,0,0.3) 50%,rgba(0,0,0,0.3) 100%); background-image: -moz-linear-gradient(top,rgba(0,0,0,0) 0,rgba(0,0,0,0.3) 50%,rgba(0,0,0,0.3) 100%); background-image: -webkit-linear-gradient(top,rgba(0,0,0,0) 0,rgba(0,0,0,0.3) 50%,rgba(0,0,0,0.3) 100%); background-image: -ms-linear-gradient(top,rgba(0,0,0,0) 0,rgba(0,0,0,0.3) 50%,rgba(0,0,0,0.3) 100%); }';
		$style .= '.red { padding: 5px; color: rgb(255, 255, 255); background-color: rgb(255, 0, 0); background-image: linear-gradient(top,rgba(0,0,0,0) 0,rgba(0,0,0,0.3) 50%,rgba(0,0,0,0.3) 100%); background-image: -o-linear-gradient(top,rgba(0,0,0,0) 0,rgba(0,0,0,0.3) 50%,rgba(0,0,0,0.3) 100%); background-image: -moz-linear-gradient(top,rgba(0,0,0,0) 0,rgba(0,0,0,0.3) 50%,rgba(0,0,0,0.3) 100%); background-image: -webkit-linear-gradient(top,rgba(0,0,0,0) 0,rgba(0,0,0,0.3) 50%,rgba(0,0,0,0.3) 100%); background-image: -ms-linear-gradient(top,rgba(0,0,0,0) 0,rgba(0,0,0,0.3) 50%,rgba(0,0,0,0.3) 100%); }';
		$style .= '.green { padding: 5px; color: rgb(255, 255, 255); background-color: rgb(0, 255, 0); background-image: linear-gradient(top,rgba(0,0,0,0) 0,rgba(0,0,0,0.3) 50%,rgba(0,0,0,0.3) 100%); background-image: -o-linear-gradient(top,rgba(0,0,0,0) 0,rgba(0,0,0,0.3) 50%,rgba(0,0,0,0.3) 100%); background-image: -moz-linear-gradient(top,rgba(0,0,0,0) 0,rgba(0,0,0,0.3) 50%,rgba(0,0,0,0.3) 100%); background-image: -webkit-linear-gradient(top,rgba(0,0,0,0) 0,rgba(0,0,0,0.3) 50%,rgba(0,0,0,0.3) 100%); background-image: -ms-linear-gradient(top,rgba(0,0,0,0) 0,rgba(0,0,0,0.3) 50%,rgba(0,0,0,0.3) 100%); }';
		$style .= '.yellow { padding: 5px; color: rgb(255, 255, 255); background-color: rgb(255, 255, 0); background-image: linear-gradient(top,rgba(0,0,0,0) 0,rgba(0,0,0,0.3) 50%,rgba(0,0,0,0.3) 100%); background-image: -o-linear-gradient(top,rgba(0,0,0,0) 0,rgba(0,0,0,0.3) 50%,rgba(0,0,0,0.3) 100%); background-image: -moz-linear-gradient(top,rgba(0,0,0,0) 0,rgba(0,0,0,0.3) 50%,rgba(0,0,0,0.3) 100%); background-image: -webkit-linear-gradient(top,rgba(0,0,0,0) 0,rgba(0,0,0,0.3) 50%,rgba(0,0,0,0.3) 100%); background-image: -ms-linear-gradient(top,rgba(0,0,0,0) 0,rgba(0,0,0,0.3) 50%,rgba(0,0,0,0.3) 100%); }';
		$style .= '.orange { padding: 5px; color: rgb(255, 255, 255); background-color: rgb(255, 160, 0); background-image: linear-gradient(top,rgba(0,0,0,0) 0,rgba(0,0,0,0.3) 50%,rgba(0,0,0,0.3) 100%); background-image: -o-linear-gradient(top,rgba(0,0,0,0) 0,rgba(0,0,0,0.3) 50%,rgba(0,0,0,0.3) 100%); background-image: -moz-linear-gradient(top,rgba(0,0,0,0) 0,rgba(0,0,0,0.3) 50%,rgba(0,0,0,0.3) 100%); background-image: -webkit-linear-gradient(top,rgba(0,0,0,0) 0,rgba(0,0,0,0.3) 50%,rgba(0,0,0,0.3) 100%); background-image: -ms-linear-gradient(top,rgba(0,0,0,0) 0,rgba(0,0,0,0.3) 50%,rgba(0,0,0,0.3) 100%); }';
		$style .= '</style>';

		$content = $style;
		$content .= '<table>';
		if (count($MessageData) == 0) {
	  		$content .= '<tr>';
	  		$content .= '<td class="fst"><img src=\'img/icons/Ok.svg\'></img></td>';
	  		$content .= '<td class="mid">Keine Meldungen vorhanden!</td>';
	  		$content .= '<td class=\'lst\'><div class=\'green\' onclick=\'alert("Nachricht kann nicht bestätigt werden.");\'>OK</div></td>';
	  		$content .= '</tr>';
	  	}
	  	else {
	    		foreach ($MessageData as $Number => $Message) {
	      			if ($Message['Type']) {
					switch ($Message['Type']) {
		  				case 4:
		    					$Type = 'orange';
		    					break;
		  				case 3:
		    					$Type = 'blue';
		    					break;
		  				case 2:
		    					$Type = 'yellow';
		    					break;
		  				case 1:
		    					$Type = 'red';
		    					break;
		  				default:
		    					$Type = 'green';
		    					break;
					}
	      			}
				else {
					$Type = 'green';
				}
				if ($Message['Image']) {
					$Image = '<img src=\'img/icons/'.$Message['Image'].'.svg\'></img>';
				}
				else {
					$Image = '<img src=\'img/icons/Ok.svg\'></img>';
				}

				$content .= '<tr>';
				$content .= '<td class="fst">'.$Image.'</td>';

				$content .= '<td class="mid">'.utf8_decode($Message['Text']).'</td>';
				if ($Message['Removable']) {
					$content .= '<td class=\'lst\'><div class=\''.$Type.'\' onclick="window.xhrGet=function xhrGet(o) {var HTTP = new XMLHttpRequest();HTTP.open(\'GET\',o.url,true);HTTP.send();};window.xhrGet({ url: \'hook/msg?ts=\' + (new Date()).getTime() + \'&action=remove&number='.$Number.'\' });">OK</div></td>';
				}
				elseif ($Message['Page']) {
					$content .= '<td class=\'lst\'><div class=\''.$Type.'\' onclick="window.xhrGet=function xhrGet(o) {var HTTP = new XMLHttpRequest();HTTP.open(\'GET\',o.url,true);HTTP.send();};window.xhrGet({ url: \'hook/msg?ts=\' + (new Date()).getTime() + \'&action=switch&page='.$Message['Page'].'\' });">OK</div></td>';
				}
				else {
					$content .= '<td class=\'lst\'><div class=\''.$Type.'\' onclick=\'alert("Nachricht kann nicht bestätigt werden.");\'>OK</div></td>';
				}
				$content .= '</tr>';
			}
	  	}
	  	$content .= '</table>';
	  	SetValueString($this->GetIDForIdent("Messages"), $content);
	}    
	
	private function RegisterHook($WebHook) 
	{ 
		$ids = IPS_GetInstanceListByModuleID("{015A6EB8-D6E5-4B93-B496-0D3F77AE9FE1}"); 
		if(sizeof($ids) > 0) { 
			$hooks = json_decode(IPS_GetProperty($ids[0], "Hooks"), true); 
			$found = false; 
			foreach($hooks as $index => $hook) { 
				if($hook['Hook'] == $WebHook) { 
					if($hook['TargetID'] == $this->InstanceID) 
						return; 
					$hooks[$index]['TargetID'] = $this->InstanceID; 
					$found = true; 
				} 
			} 
			if(!$found) { 
				$hooks[] = Array("Hook" => $WebHook, "TargetID" => $this->InstanceID); 
			} 
			IPS_SetProperty($ids[0], "Hooks", json_encode($hooks)); 
			IPS_ApplyChanges($ids[0]); 
		} 
	}     
}
?>
