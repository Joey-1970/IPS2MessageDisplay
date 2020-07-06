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
			
			$this->SetStatus(102);
			
		}
		else {
			$this->SetStatus(104);
			
		}	
	}
	

	    
	// Beginn der Funktionen
	public function Add(int $MessagesID, string $Text, int $Expires, bool $Removable, int $Type, string $Image, int $Page) 
	{
		If ($this->ReadPropertyBoolean("Open") == true) {
			$MessageData = array();
			$MessageData = unserialize($this->ReadAttributeString("MessageData"));
			$MessageData[$MessagesID]["Text"] = $Text;
			$MessageData[$MessagesID]["Expires"] = $Expires;
			$MessageData[$MessagesID]["Removable"] = $Removable;
			$MessageData[$MessagesID]["Type"] = $Type;
			$MessageData[$MessagesID]["Image"] = $Image;
			$MessageData[$MessagesID]["Page"] = $Page;
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
			
		}
	}
	
	public function RemoveType(int $Type) 
	{
		If ($this->ReadPropertyBoolean("Open") == true) {
			
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
	      $content = $content.'<td class="fst">'.$image.'</td>';

	      $content = $content.'<td class="mid">'.utf8_decode($message['text']).'</td>';
	      if ($message['removable']) {
		$content = $content.'<td class=\'lst\'><div class=\''.$type.'\' onclick="window.xhrGet=function xhrGet(o) {var HTTP = new XMLHttpRequest();HTTP.open(\'GET\',o.url,true);HTTP.send();};window.xhrGet({ url: \'hook/msg?ts=\' + (new Date()).getTime() + \'&action=remove&number='.$number.'\' });">OK</div></td>';
	      }
	      elseif ($message['page']) {
		$content = $content.'<td class=\'lst\'><div class=\''.$type.'\' onclick="window.xhrGet=function xhrGet(o) {var HTTP = new XMLHttpRequest();HTTP.open(\'GET\',o.url,true);HTTP.send();};window.xhrGet({ url: \'hook/msg?ts=\' + (new Date()).getTime() + \'&action=switch&page='.$message['page'].'\' });">OK</div></td>';
	      }
	      else {
		$content = $content.'<td class=\'lst\'><div class=\''.$type.'\' onclick=\'alert("Nachricht kann nicht bestätigt werden.");\'>OK</div></td>';
	      }
	      $content .= '</tr>';
	    }
	  }
	  $content = $content. '</table>';
	  SetValueString($MessagesID, $content);
	}    
	    
}
?>
