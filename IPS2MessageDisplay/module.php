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
	# Parameter:
# - 'text': Meldungstext
# - 'expires' (optional): Zeitpunkt des automatischen Löschens der Meldung
#          als Unix-Timestamp. Ist der Wert kleiner als die aktuelle Timestamp,
#          wird nicht automatisch gelöscht.
# - 'removable' (optional): Anzeige eines Buttons zum Löschen der Meldung im WebFront.
# - 'type' (optional): Art der Meldung ... 0 => Normal(grün),
#          1 => Fehler(rot), 2 => Warnung(gelb), 3 => Todo(blau), 4 => Goto(orange)
# - 'image' (optional): Name des WebFront-Icons (ipsIcon<name>), welches
#          für Meldung verwendet werden soll, Standard ist "Talk"
# - 'page' (optional): Nur in Verbindung mit Type 4 - Seitenname
	public function Add(string $Text, int $Expires, bool $Removable, int $Type, string $Image, int $Page) {
		If ($this->ReadPropertyBoolean("Open") == true) {
			
		}
	}
	    
	public function Remove(int $MessageID) {
		If ($this->ReadPropertyBoolean("Open") == true) {
			
		}
	}
	    
	public function RemoveAll(int $MessageID) {
		If ($this->ReadPropertyBoolean("Open") == true) {
			
		}
	}
	
	public function RemoveType(int $MessageID) {
		If ($this->ReadPropertyBoolean("Open") == true) {
			
		}
	}
	
}
?>
