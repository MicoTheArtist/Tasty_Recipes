<?

// wordCloud
class wordCloud {

	public $wordsArray = array();	// $wordsArray
	private $showCloudAs;
	
	/*
	 * __construct ($words = false) -- automaticly takes an array or word tags and adds a value of 1 to them if they already exist or inserts them as new and sets them to 1 if they do not.
	 *
	 *@param array $words
	 *@return void
	 */
	 public function __construct($words = false, $showCloudAsNew = false) {
	 
	 	if (($words != false) && (is_array($words))) {		// validate that $words is not false and that it is an array
		
			foreach ($words as $key => $new_tags) {			// loop through the array of words and their popularity
			
				$this->addWord($new_tags);					// run the function addWord() to add or set the new popularity value for the $wordsArray array
	 		}
		}
		
		if ($showCloudAsNew != false) {	// if $showCloudAs is not equal to false
		
			$this->showCloudAs = $showCloudAsNew; 			// set $this->showCloudAs = $this->showCloudAsNew -- to prepare the function showCloud to print in list form.
		}
	}
	
	
	/*
	 * addWord() -- assigns a word to the array $wordsArray and adds or sets a value of $value to the new or already existing word key.
	 *
	 *@ param string $word
	 *@ return string $wordsArray
	 */
	 public function addWord($word, $value = 1) {
	 
	 	$words = strtolower($word);							// make all of the words lowercase for consistency
		
		if (array_key_exists($word, $this->wordsArray)) {	// check if $word is an existing key of the array $this->wordsArray
		
			$this->wordsArray[$word] += $value;				// if it is than add a popularity of 1 to the matching key value
		}			
		else {
		
			$this->wordsArray[$word] = $value;				// assign a new word to $this->wordsArray and set it to 1
		}
		return $wordsArray;									// return the newly set $wordsArray
	 }
	 
	
	/*
	 * shuffleCloud() -- shuffles the associated names in the array 
	 */
	 public function shuffleCloud() {
	 
	 	$keys = array_keys($this->wordsArray);				// create an array made up only of the $this->wordsArray array keys
		
		shuffle($keys);										// shuffle all of the array keys randomly
		
		if ((count($keys)) && (is_array($keys))) {			// validate the $keys array
			
			$tmpArray = $this->wordsArray;					// set $tmpArray to the original array to use later
			
			$this->wordsArray = array();					// reset $wordArray as a blank array so that I can reset it later
			
			foreach ($keys as $key => $tag) {				// loop through the $keys array
			
				$this->wordsArray[$tag] = $tmpArray[$tag];	// set the newly created $wordsArray to the values of the original array contained within $tmpArray
			}
		}
	 }

	 
	/*
	 * getCloudSize() -- calculates the sum of all of the arrays values
	 */
	 public function getCloudSize() {
	 
	 	return array_sum($this->wordsArray);
	 
	 }

	 
	/*
	 * getCloud() -- returns the wordsArray
	 */
	 public function getCloud() {
	 
	 	return $this->wordsArray;
	 }
	 
	 
	/*
	 * getClassFromPercent($percent) -- returns the class range using the percent parameter
	 *
	 *@return int $class
	 */
	 public function getClassFromPercent($percent) {
	 
	 	if ($percent >= 99) {		// 99
			$class = 1;
		}
		else if ($percent >= 70) {	// 70
			$class = 2;
		}
		else if ($percent >= 60) {	// 60
			$class = 3;
		}
		else if ($percent >= 50) {	// 50
			$class = 4;
		}
		else if ($percent >= 40) {	// 40
			$class = 5;
		}
		else if ($percent >= 30) {	// 30
			$class = 6;
		}
		else if ($percent >= 20) {	// 20
			$class = 7;
		}
		else if ($percent >= 10) {	// 10
			$class = 8;
		}
		else {	
			$class = 0;
		}
		return $class;	// return the $class
	 
	 }
	 

	/*
	 * showCloud() -- returns the word along with the correct html for size and color
	 *
	 *@returns string $html_word
	 */
	 public function showCloud() {
	 
	 	$this->shuffleCloud();																// shuffle the $wordsArray
		
		$max = max($this->wordsArray);														// find the max() value of the $wordsArray popularity value to use later
		
		foreach ($this->wordsArray as $word => $popularity) {								// loop through the entire $wordsArray
		
			$sizeRange = $this->getClassFromPercent(($popularity/$max)*100);				// set the $sizeRange according to the percentage of popularity it contains within the array
			
			if ($this->showCloudAs != false && $this->showCloudAs == 'list') {												// if - print the cloud in list form
				
				$html_word .= "<span class=''> <a href='tag_bookmarks.php?tag=".$word."'>".$word.", ".$popularity."</a> </span><br>"; // print word tag as a list
			}
			else {
			
				$html_word .= "<span class='word size".$sizeRange."'> <a href='tag_bookmarks.php?tag=".$word."'>".$word."</a> </span>"; 	// print word tag as a cloud		
			}							
		}
		return $html_word;																	
	}
	
	
	/*
	 * showCloudAs() -- returns a value for $showCloudAs
	 */
	public function showTheCloudAs() {
	
		return $this->showCloudAs;
	
	}
	
}


?>



















