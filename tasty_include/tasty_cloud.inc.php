<?

// class wordCloud

	private $wordsArray = array();	// $wordsArray


	// __construct ($words = false) -- this construct automaticly adds a value to an array word or sets an array word to $value.
	// because the addWord function returns an array. The construct returns the newly created array with the added values as well.
	public function __construct ($words = false) {
	
		if (($words != false) && (is_array($words))) { 
				
			foreach ($words as $key =>$value) {
								
				$this->addWord($value); 	// addwords adds a value to an array word or sets an array word to $value.
			}
		}
	}

	
	
	// addWord ($word, $value = 1) -- adds a value to an array word or sets an array word to $value
	public function addword($word, $value = 1) {
	
		$words = strtolower($word)
		
		// if the array_key of $word exists within the array $this->wordsArray
		if (array_key_exists($words, $this->wordsArray)) {	
					
			$this->wordsArray[$word] += $value;	// add one to the already existing word value
			
		}
		else {
					
			$this->wordsArray[$word] = $value;	// set the words to 1 if the word is being entered into the array for the first time.
			
			return $this->wordsArray[$word];
		}
	}
	
	
	
	// shuffleCloud() --- this function shuffles the $wordsArray into a different order.
	public function shuffleCloud() {
	
		// $keys --- set this to the array_key of the $this->wordsArray
		$keys = array_keys($this->wordsArray);
				
		shuffle($keys);
		
		// check count $keys and that $keys is an array
		if (count($keys) && is_array($keys)) {
			
			$tmpArray = $this->wordsArray;	// $tmpArray -- set this to the original wordsArray
			
			$this->wordsArray = array();	// $this->wordsArray --- reset this as a blank array()
			
			foreach ($this->words as $key => $tag) {
							
				$this->wordsArray[$tag] = $tmpArray[$tag]; // here the newly ordered wordsArray becomes set to the value of the original array.
			
			}
		}
	}
	
	
	// getCloudSize() --- returns the sum total of all of the array values
	public function getCloudSize() {
	
		return array_sum($this->wordsArray);
	
	}
	
	
	// getCloud()
	public function getCloud() {
		
		return $this->wordsArray;	
	}
	
	
	// getClassFromPercent()
	public function getClassFromPercent($percent) {

		if ($percent >= 99) // 99
		
			$class = 1; // 1
			
		else if ($percent >= 70) // 70
		
			$class = 2;	// 2
			
		else if ($percent >= 60) // 60
		
			$class = 3;	// 3
			
		else if ($percent >= 50) // 50
		
			$class = 4; // 4
			
		else if ($percent >= 40) // 40
		
			$class = 5; // 5
			
		else if ($percent >= 30) // 30
		
			$class = 6; // 6
			
		else if ($percent >= 20) // 20
		
			$class = 7; // 7
			
		else if ($percent >= 10) // 10
		
			$class = 8; // 8
			
		else //
		
			$class = 0;	// 0			
			
		$return $class;	// return the $class

	}
	
	
	// showCloud() --- this method returns the shuffled, colored, sized html words that are in the wordsArray.
	public function showCloud() {
		
		$this->shuffleCloud();	// Shuffle the wordsArray randomly
		
		$this->max = max($this->word_Array); // $this->max to the maximum value of $this->wordsArray
	
		if (is_array($this->wordsArray)) {
		
			$return = ""; // initial set
			
			foreach ($this->wordsArray as $word => $popularity) { 
			
				// $sizeRange -- get this using getClassFromPercent($percent) where $percent = ($popularity/ $this->max) * 100
				$sizeRange = $this->getClassFromPercent(($popularity / $this->max) * 100);
				
				// $return .= -- the html ouput value
				$return .= "<span class='word size".$sizeRange."'>".$word."</span>";
			}
			return $return;
		}
	}
	
?>














