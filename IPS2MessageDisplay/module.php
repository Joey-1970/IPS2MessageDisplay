<?
    // Klassendefinition
    class IPS2MessageDisplay extends IPSModule 
    {
	public function Destroy() 
	{
		//Never delete this line!
		parent::Destroy();
		$this->SetTimerInterval("AutoRemove", 0);
	}  
	    
	// Überschreibt die interne IPS_Create($id) Funktion
        public function Create() 
        {
            	// Diese Zeile nicht löschen.
            	parent::Create();
		$this->RegisterMessage(0, IPS_KERNELMESSAGE);
		
		$this->RegisterPropertyBoolean("Open", false);
		$this->RegisterPropertyInteger("Sorting", 3);
		$this->RegisterPropertyBoolean("ShowTime", false);
		$this->RegisterAttributeString("MessageData", ""); 
		$this->RegisterPropertyInteger("WebfrontID", 0);
		$this->RegisterPropertyInteger("AutoRemove", 1000);
		$this->RegisterTimer("AutoRemove", 0, 'IPS2MessageDisplay_AutoRemove($_IPS["TARGET"]);');
		$this->RegisterPropertyInteger("ActuatorID", 0);
		
		//Status-Variablen anlegen
		$this->RegisterVariableString("Messages", "Meldungen", "~HTMLBox", 10);
		$this->RegisterVariableInteger("MessageCount", "Anzahl", "", 20);
		
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
		$arrayElements[] = array("type" => "Label", "label" => "_____________________________________________________________________________________________________");
 
		$arrayOptions = array();
		$arrayOptions[] = array("label" => "Neuste Nachricht zuerst", "value" => SORT_DESC);
		$arrayOptions[] = array("label" => "Älteste Nachricht zuerst", "value" => SORT_ASC);
		$arrayElements[] = array("type" => "Select", "name" => "Sorting", "caption" => "Sortierung in der Darstellung", "options" => $arrayOptions );
		
		$arrayElements[] = array("name" => "ShowTime", "type" => "CheckBox",  "caption" => "Uhrzeit anzeigen");
		
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
            	$arrayElements[] = array("type" => "Label", "label" => "Aktor-Variable (Boolean)");
            	$arrayElements[] = array("type" => "SelectVariable", "name" => "ActuatorID", "caption" => "Aktor"); 
		
 		return JSON_encode(array("status" => $arrayStatus, "elements" => $arrayElements)); 		 
 	}       
	   
        // Überschreibt die intere IPS_ApplyChanges($id) Funktion
        public function ApplyChanges() 
        {
            	// Diese Zeile nicht löschen
            	parent::ApplyChanges();
		
		if (IPS_GetKernelRunlevel() == KR_READY) {
			// Webhook einrichten
			$this->RegisterHook("/hook/IPS2MessageDisplay_".$this->InstanceID);
		}
		
		If ($this->ReadPropertyBoolean("Open") == true) {
			$MessageData = array();
			$MessageData = unserialize($this->ReadAttributeString("MessageData"));
			$this->RenderData($MessageData);
			$this->SetTimerInterval("AutoRemove", 1000);
			$this->SetStatus(102);
			
		}
		else {
			$this->SetStatus(104);
			$this->SetTimerInterval("AutoRemove", 0);
		}	
	}
	
	public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    	{
		switch ($Message) {
			case 10001:
				// IPS_KERNELSTARTED
				$this->ApplyChanges;
				break;
			
		}
    	}        
	    
	// Beginn der Funktionen
	private function WorkProcess(string $Activity, int $MessageID, string $Text, int $Expires, bool $Removable, int $Type, string $Image, string $Page) 
	{
		If ($this->ReadPropertyBoolean("Open") == true) {
			if (IPS_SemaphoreEnter("WorkProcess", 2000))
			{
				$MessageData = array();
				$MessageData = unserialize($this->ReadAttributeString("MessageData"));
				switch ($Activity) {
					case 'Add':
						$MessageData[$MessageID]["MessageID"] = $MessageID;
						$MessageData[$MessageID]["Text"] = $Text;
						$MessageData[$MessageID]["Expires"] = $Expires;
						$MessageData[$MessageID]["Removable"] = $Removable;
						$MessageData[$MessageID]["Type"] = $Type;
						$MessageData[$MessageID]["Image"] = $Image;
						$MessageData[$MessageID]["Page"] = $Page;
						$MessageData[$MessageID]["Timestamp"] = microtime(true);
						$this->SendDebug("WorkProcess", "Message ".$MessageID." wurde hinzugefuegt", 0);
						break;
					case 'Remove':
						If (is_array($MessageData)) {
							if (array_key_exists($MessageID, $MessageData)) {
								unset($MessageData[$MessageID]);
								$this->SendDebug("WorkProcess", "Message ".$MessageID." wurde entfernt", 0);
							}
							else {
								$this->SendDebug("WorkProcess", "Message ".$MessageID." wurde nicht gefunden", 0);
							}
						}
						break;
					case 'RemoveAll':
						$MessageData = array();
						$this->SendDebug("WorkProcess", "Alle Messages wurde entfernt", 0);
						break;
					case 'RemoveType':
						foreach ($MessageData as $MessageID => $Message) {
							If ($Message["Type"] == $Type) {
								unset($MessageData[$MessageID]);
								$this->SendDebug("WorkProcess", "Message ".$MessageID." wurde entfernt", 0);
							}
						}
						break;
					case 'AutoRemove':
						if (count($MessageData) > 0) {
							foreach ($MessageData as $MessageID => $Message) {
								If ($Message["Expires"] > 0) {
									If ($Message["Expires"] + $Message["Timestamp"] <= microtime(true) ) {
										unset($MessageData[$MessageID]);
										$this->SendDebug("WorkProcess", "Message ".$MessageID." wurde entfernt", 0);
										$this->RenderData($MessageData);
									}
								}
							}
						}
						break;
				}
				$this->WriteAttributeString("MessageData", serialize($MessageData));
			}
			IPS_SemaphoreLeave("WorkProcess");
			If ($Activity <> "AutoRemove") {
				$this->RenderData($MessageData);
			}
		}
		else {
			$this->SendDebug("WorkProcess", "Semaphore Abbruch!", 0);
		}
	}
	    
	public function Add(int $MessageID, string $Text, int $Expires, bool $Removable, int $Type, string $Image, string $Page) 
	{
		$this->WorkProcess("Add", $MessageID, $Text, $Expires, $Removable, $Type, $Image, $Page);
	}
	    
	public function Remove(int $MessageID) 
	{
		$this->WorkProcess("Remove", $MessageID, "", 0, false, 0, "", "");
	}
	    
	public function RemoveAll() 
	{
		$this->WorkProcess("RemoveAll", 0, "", 0, false, 0, "", "");
	}    
	    
	public function RemoveType(int $Type) 
	{
		$Type = min(3, max(0, $Type));
		$this->WorkProcess("RemoveType", 0, "", 0, false, $Type, "", "");
	}
	    
	public function AutoRemove() 
	{
		$this->WorkProcess("AutoRemove", 0, "", 0, false, 0, "", "");
	}
	    
	protected function ProcessHookData() 
	{		
		If ($this->ReadPropertyBoolean("Open") == true) {
			$this->SendDebug("ProcessHookData", "Ausfuehrung", 0);
			switch ($_GET['action']) {
			    	case 'remove':
			      		$MessageID = isset($_GET['MessageID']) ? $_GET['MessageID'] : -1;
			      		if ($MessageID > 0) {
						//$this->WorkProcess("Remove", $MessageID, "", 0, false, 0, "", "");
				  		$this->Remove($MessageID);
			      		}
					else {
						$this->SendDebug("ProcessHookData", "Keine MessageID!", 0);
					}
			      		break;
			    case 'switch':
			      		$PageID = isset($_GET['PageID']) ? $_GET['PageID'] : '';
			      		if (is_string($PageID) && $PageID !='') {
				  		$WebfrontID = $this->ReadPropertyInteger("WebfrontID");
						if ($WebfrontID > 0) {
							WFC_SwitchPage($WebfrontID, $PageID);
						}
						else {
							$this->SendDebug("ProcessHookData", "Kein Webfront definiert!", 0);
						}
			      		}
			      break;
			}
		}
	}       
	    
	private function RenderData($MessageData)		
	{
		$ShowTime = $this->ReadPropertyBoolean("ShowTime");
		$Sorting = $this->ReadPropertyInteger("Sorting");
		$WebfrontID = $this->ReadPropertyInteger("WebfrontID");
		If ($WebfrontID > 0) {
			$Skin = IPS_GetProperty($WebfrontID, "Skin");
			If ($Skin == "") {
				$IconPath = "img/icons";
			}
			else {
				$IconPath = $Skin."/img/icons";
			}
		}
		else {
			$IconPath = "img/icons";
		}
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
			if ($ShowTime == true) {
				$content .= '<td class="lst">'.date("d.m.Y H:i", time() ).'</td>';
			}
			$content .= '<td class="mid">Keine Meldungen vorhanden!</td>';
			$content .= '<td class="mid"></td>';
			$content .= '<td class=\'lst\'><div class=\'green\' onclick=\'alert("Nachricht kann nicht bestätigt werden.");\'>...</div></td>';
			$content .= '</tr>';
	  	}
	  	else {
	    		$MessageData =  $this->MessageSort($MessageData, 'Timestamp',  $Sorting);
			foreach ($MessageData as $Number => $Message) {
	      			$TypeColor = array("green", "red", "yellow", "blue");
				$TypeImage = array("Ok", "Alert", "Warning", "Clock");
				$Message['Type'] = min(3, max(0, $Message['Type']));
						
				if ($Message['Image'] <> "") {
					$Image = '<img src=\'img/icons/'.$Message['Image'].'.svg\'></img>';
				}
				else {
					$Image = '<img src=\'img/icons/'.$TypeImage[$Message['Type']].'.svg\'></img>';
				}

				$content .= '<tr>';
				$content .= '<td class="fst">'.$Image.'</td>';
				if ($ShowTime == true) {
					$SecondsToday= date('H') * 3600 + date('i') * 60 + date('s');
					If ($Message['Timestamp'] <= (time() - $SecondsToday)) {
						$content .= '<td class="lst">'.date("d.m.Y H:i", $Message['Timestamp']).'</td>';
					}
					else {
						$content .= '<td class="lst">'.date("H:i:s", $Message['Timestamp']).'</td>';
					}
				}
				$content .= '<td class="mid">'.utf8_decode($Message['Text']).'</td>';
				
				if ($Message['Page'] <> "") {
					$TypeWF = 'orange';
					$content .= '<td class=\'lst\'><div class=\''.$TypeWF.'\' onclick="window.xhrGet=function xhrGet(o) {var HTTP = new XMLHttpRequest();HTTP.open(\'GET\',o.url,true);HTTP.send();};window.xhrGet({ url: \'hook/IPS2MessageDisplay_'.$this->InstanceID.'?ts=\' + (new Date()).getTime() + \'&action=switch&PageID='.$Message['Page'].'\' });">WF</div></td>';
					
				}
				else {
					$content .= '<td class="mid"></td>';
				}
				
				if ($Message['Removable'] == true) {
					$content .= '<td class=\'lst\'><div class=\''.$TypeColor[$Message['Type']].'\' onclick="window.xhrGet=function xhrGet(o) {var HTTP = new XMLHttpRequest();HTTP.open(\'GET\',o.url,true);HTTP.send();};window.xhrGet({ url: \'hook/IPS2MessageDisplay_'.$this->InstanceID.'?ts=\' + (new Date()).getTime() + \'&action=remove&MessageID='.$Message['MessageID'].'\' });">OK</div></td>';
					
				}
				else {
					$content .= '<td class=\'lst\'><div class=\''.$TypeColor[$Message['Type']].'\' onclick=\'alert("Nachricht kann nicht bestätigt werden.");\'>...</div></td>';
				}
				$content .= '</tr>';
			}
	  	}
	  	$content .= '</table>';
	  	If (GetValueString($this->GetIDForIdent("Messages")) <> $content) {
			SetValueString($this->GetIDForIdent("Messages"), $content);
		}
		If (GetValueInteger($this->GetIDForIdent("MessageCount")) <> count($MessageData)) {
			SetValueInteger($this->GetIDForIdent("MessageCount"), count($MessageData));
		}
		If ((count($MessageData) == 0) AND ($this->ReadPropertyInteger("ActuatorID") > 0)) {
			If (GetValueBoolean($this->ReadPropertyInteger("ActuatorID")) == true) {
				SetValueBoolean($this->ReadPropertyInteger("ActuatorID"), false);
			}
		}
		elseif ((count($MessageData) > 0) AND ($this->ReadPropertyInteger("ActuatorID") > 0)) {
			If (GetValueBoolean($this->ReadPropertyInteger("ActuatorID")) == false) {
				SetValueBoolean($this->ReadPropertyInteger("ActuatorID"), true);
			}
		}
	}    
	
	private function MessageSort($MessageData, $DataField, $SortOrder) 
	{
    		if(is_array($MessageData)==true) {
            		foreach ($MessageData as $key => $value) {
            			if(is_array($value) == true){
                			foreach ($value as $kk => $vv) {
                    				${$kk}[$key]  = strtolower( $value[$kk]);
                			}
            			}
        		}
    		}
    		array_multisort(${$DataField}, $SortOrder, $MessageData);
    	return $MessageData;
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
	    
	private function RegisterHook($WebHook)
    	{
        	$ids = IPS_GetInstanceListByModuleID('{015A6EB8-D6E5-4B93-B496-0D3F77AE9FE1}');
        	if (count($ids) > 0) {
            		$hooks = json_decode(IPS_GetProperty($ids[0], 'Hooks'), true);
            		$found = false;
            		foreach ($hooks as $index => $hook) {
                		if ($hook['Hook'] == $WebHook) {
                    			if ($hook['TargetID'] == $this->InstanceID) {
                        			return;
                    			}
                    			$hooks[$index]['TargetID'] = $this->InstanceID;
                    			$found = true;
                		}
            		}
            		if (!$found) {
                		$hooks[] = ['Hook' => $WebHook, 'TargetID' => $this->InstanceID];
            		}
            	IPS_SetProperty($ids[0], 'Hooks', json_encode($hooks));
            	IPS_ApplyChanges($ids[0]);
        }
}
