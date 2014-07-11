<?php 
/*************************
* author: Rodney Olivier;
* date:06/29/2014
* version: 0.1.0
* last modified date 07/09/2014
* copyright©2014
************************/
session_start(); 

/* $a = session_id();
if(empty($a)) session_start();
echo "SID: ".SID."<br>session_id(): ".session_id()."<br>COOKIE: ".$_COOKIE["PHPSESSID"];
 */
	function debug($v,$d=false){
		echo "<pre>";
		var_dump($v);
		echo "</pre>";
		if ($d === true){
			die();
		}
	}
	
				
	class cards
	{
		
		function __construct()
		{
			
		}
		
		public function repack()
		{
			$cardDeckFactory = array(
				// 1 = Spades, 2=Diamonds, 3=Clubs , 4=Hearts
				// spades [DeckIndex, Suite, CardValue]
				// 1-13 = Spades
				// 14-26 = Diamonds
				// 27-39 = Clubs
				// 40-52 = Hearts
				/*
				"card1": { "card":"A","suite":"&spades;","color":"black"},
				"card2": { "card":"K","suite":"&hearts;","color":"red"},
				"card3": { "card":"Q","suite":"&clubs;","color":"black"},
				"card4": { "card":"J","suite":"&diams;","color":"red"},
				"card5": { "card":"10","suite":"&diams;","color":"red"}
				*/
				[1,"spades;",'A'],[2,"spades;",'2'],[3,"spades;",'3'],[4,"spades;",'4'],[5,"spades;",'5'],[6,"spades;",6],[7,"spades;",7],[8,"spades;",'8'],[9,"spades;",'9'],[10,"spades;",'10'],[11,"spades;",'J'],[12,"spades;",'Q'],[13,"spades;",'K'],
				[14,"diams;",'A'],[15,"diams;",'2'],[16,"diams;",'3'],[17,"diams;",'4'],[18,"diams;",'5'],[19,"diams;",'6'],[20,"diams;",'7'],[21,"diams;",'8'],[22,"diams;",'9'],[23,"diams;",'10'],[24,"diams;",'J'],[25,"diams;",'Q'],[26,"diams;",'K'],
				[27,"clubs;",'A'],[28,"clubs;",'2'],[29,"clubs;",'3'],[30,"clubs;",'4'],[31,"clubs;",'5'],[32,"clubs;",'6'],[33,"clubs;",'7'],[34,"clubs;",'8'],[35,"clubs;",'9'],[36,"clubs;",'10'],[37,"clubs;",'J'],[38,"clubs;",'Q'],[39,"clubs;",'K'],
				[40,"hearts;",'A'],[41,"hearts;",'2'],[42,"hearts;",'3'],[43,"hearts;",'4'],[44,"hearts;",'5'],[45,"hearts;",'6'],[46,"hearts;",'7'],[47,"hearts;",'8'],[48,"hearts;",'9'],[49,"hearts;",'10'],[50,"hearts;",'J'],[51,"hearts;",'Q'],[52,"hearts;",'K']
				);
			//debug($cardDeckFactory, true);	
			return $cardDeckFactory;
		}
		
		 public function deal($ante)
		{
			$newCards = $_SESSION['Cards'];
			$_SESSION['Player0']['Bank'];
			$_SESSION['Ante'] = $ante;
			
			$_SESSION['WinPool'] = $ante * $_SESSION['PlayerCount'];
			$handCount = 0;
			$handArray = array();
			$sortedHands = array();
			for( $p=0; $p < $_SESSION['PlayerCount']; $p++)
			{
				$handArray['player'.$p] = array();
				$_SESSION['Player'.$p]['HandRank'] = '';
				$_SESSION['Player'.$p]['Bank'] -= $ante;
			}
			while ( $handCount < 5)
			{
				if(count($newCards) > 0 )
				{
					for($x=0; $x< $_SESSION['PlayerCount']; $x++)
					{
						$handArray['player'.$x][] = array_pop($newCards);
					}
					$handCount ++;
				} 
				else 
				{
					//deliver_response(400,"Deal Not Successful", NULL);
					break 2;
				}
			}
			$_SESSION['Cards'] = $newCards;
			for($x=0; $x< $_SESSION['PlayerCount']; $x++)
			{
				$sortedHands['player'.$x] = sortHand($handArray['player'.$x],'Player'.$x);
			}
			return $sortedHands;
		}
		
		
		public function draw($currentHandCount, $newCards, $handArray )
		{
			$newCards = $_SESSION['Cards'];
			$cardsDrawn = 0;
			while ( $currentHandCount < 5){
				if(count($newCards) > 0 ){ 
					$handArray[] = array_pop($newCards);
					$currentHandCount ++;
					$cardsDrawn ++;
				} else { 
					// no more cards in the deck
					if ($currentHandCount < 5){
						// not enough cards in the deck to fill the players hand
						return false;
					}
					// the hand is now full and the deck is empty so exit the loop
					break;
				}
			}
			$_SESSION['HandCount'] = 5;	
			$_SESSION['DeckCount'] = $_SESSION['DeckCount'] - $cardsDrawn;
			$_SESSION['PlayerHand'] = $handArray;
			$_SESSION['Cards'] = $newCards;
			return true;
		}
		
		public function shuffle(){
			if(!isset($_SESSION['Shuffled']) || !$_SESSION['Shuffled'] === true){
				$newCards =$_SESSION['Cards'];
				$_SESSION['DeckCount'] = count($newCards);
				// shuffle once for each suite
				for ($x = 0; $x < 4; $x++){
					shuffle($newCards);
				}
				$_SESSION['Cards']  = $newCards;
				if(!empty($_SESSION['Cards'])){
					$_SESSION['Shuffled'] = true;
					$_SESSION['GameStatus'] = 'InSession';
					return true;
				}else{
					return false;
				}
			}
			return false;
		}
		
		public function discard($cardList = null) 
		{
			if(!empty($cardList))
			{
				$PlayerHand = $_SESSION['PlayerHand'];
				$discarded = false;
				foreach($cardList as $index)
				{
					// unset the card that matches the index
					foreach( $PlayerHand as $key=>$value)
					{
						if($index == $value[0])
						{
							unset($PlayerHand[$key]);
							break;
						} 
					}
				}
				$newPlayerHand = array();
				foreach ($PlayerHand as $value)
				{
					$newPlayerHand[] = $value;
				}
				return $newPlayerHand;
			}
			return false;
		}
		
		public function sortHand($handArray,$player)
		{
		
			return $handArray;
		}
	}
	
//--------------------------------		
	// Simple Rest Service.
	if ( $_SERVER['REQUEST_URI'])
	{
		$actionUri = $_SERVER['REQUEST_URI'];
		$arAction = explode('/', $actionUri);
		$action = array_pop($arAction);
		if ( strpos($action, '?') !== false)
		{ // this is for action discard
			$actionCount = explode('?', $action);
			$action = $actionCount[0];
			$subaction = explode('=', $actionCount[1]);
			if ($action == 'discard')
			{
				if(!empty($subaction[1]))
				{
					$cardList = $subaction[1];
					$cardList = explode(',',$cardList);
					$count = count($cardList);
				}
				else
				{
					$cardList = '';
					$count = 0;
				}
			}
			elseif ($action == 'draw')
			{
				$count = $subaction[1];
			} 
			elseif ($action == 'newGame')
			{
				$playerCount = $subaction[1];
			} 
			elseif ($action == 'deal')
			{
				$ante = $subaction[1];
			} 
			elseif ( $action == "placeBet")
			{
				$bet = $subaction[1];
			}
		}
		
		if (!empty($action))
		{
			switch ($action)
			{
				case 'newGame':
					$cards = new cards();
					$newCards = $cards->repack();
					$_SESSION['Cards'] = $newCards;
					$_SESSION['Shuffled'] = false;
					$_SESSION['GameStatus'] = 'Started';
					$_SESSION['DeckCount'] = count($newCards);
					$_SESSION['PlayerHand'] = '0';
					$_SESSION['NonePlayerHands'] = '';
					$_SESSION['HandCount'] = '0';
					$_SESSION['Winner'] = '';
					$_SESSION['Ante'] = 0;
					$_SESSION['WinPool'] = 0;
					$_SESSION['PlayerCount'] = (!empty($playerCount))?$playerCount: 2;
					for ($p = 0; $p <= $_SESSION['PlayerCount']; $p++)
					{
						$_SESSION['Player'.$p]['Bank'] = '100';
						$_SESSION['Player'.$p]['HandRank'] = '';
						$_SESSION['Player'.$p]['Bet'] = '';
					}
					if (!empty($_SESSION['Cards']))
					{
						deliver_response(200, "New Game Started", NULL);
					} 
					else 
					{
						deliver_response(200, "Start New Game Failed - Try to Start a new Game", NULL);
					}
				break;
				case 'repack':
					if(isset($_SESSION['GameStatus']) && $_SESSION['GameStatus'] == 'ended')
					{
						$cards = new cards();
						$newCards = $cards->repack();
						$_SESSION['Cards'] = $newCards;
						$_SESSION['Shuffled'] = false;
						$_SESSION['DeckCount'] = count($newCards);
						$_SESSION['PlayerHand'] = '0';
						$_SESSION['HandCount'] = '0';
						$_SESSION['GameStatus'] = 'insession';
						for ($p = 0; $p <= $_SESSION['PlayerCount']; $p++)
						{
							$_SESSION['Player'.$p]['HandRank'] = '';
						}
						if (!empty($_SESSION['Cards']))
						{
							deliver_response(200, "New Round Started", NULL);
						} 
						else 
						{
							deliver_response(200, "Start New Round Failed - Try to Start a new Game", NULL);
						}
					}
					else
					{
						deliver_response(200, "Start New Round Failed - Try 'Call' or Start a new Game ", NULL);
					}
				break;
				case 'shuffle':
					if($_SESSION['GameStatus'] != 'ended')
					{
						if( (!isset($_SESSION['Shuffled']) || $_SESSION['Shuffled'] === false) && 
							(isset($_SESSION['Cards']) && !empty($_SESSION['Cards'])) )
						{						
							$cards = new cards();
							if($cards->shuffle()){
								deliver_response(200, "Cards shuffled", NULL);
							} else {
								deliver_response(200, "Shuffle failed - Try to Start a new Game", NULL);
							}
						} else {
							deliver_response(200, "Shuffle failed - with Shuffled Status: ".$_SESSION['shuffled'], NULL);
						}
					} else {
						deliver_response(200, "Shuffle failed - with Game Status: ".$_SESSION['GameStatus'], NULL);
					}
				break;
				case 'deal':
					if($_SESSION['GameStatus'] != 'ended')
					{
						if(!isset($_SESSION['HandCount']) || $_SESSION['HandCount'] == 0)
						{
							if(isset($_SESSION['Shuffled']) && $_SESSION['Shuffled'] === true)
							{
								$cards = new cards();
								$sortedHands = $cards->deal($ante);
								$_SESSION['HandCount'] = count($sortedHands['player0']);
								$_SESSION['DeckCount'] = count($_SESSION['Cards']);//count($newCards); // $_SESSION['DeckCount'] - $_SESSION['HandCount'];
								$_SESSION['PlayerHand'] = $sortedHands['player0'];//$handArray['player0'];
								for($x=1; $x< $_SESSION['PlayerCount']; $x++)
								{
									$_SESSION['NonePlayerHands'][] = $sortedHands['player'.$x];
								}
								$jsonObj = ['hand' => $sortedHands['player0'],
											'deckCount' => $_SESSION['DeckCount'],
											'handRank' => $_SESSION['Player0']['HandRank'],
											'playerCash' => $_SESSION['Player0']['Bank'],
											'winPool' => $_SESSION['WinPool']
											];
								deliver_response(200,"Deal Successful", $jsonObj ); //[$handArray,$_SESSION['DeckCount']]
							} 
							else 
							{
								deliver_response(200,"Deal Not Successful - Deck not Shuffled",NULL );
							}
						}
						else
						{
							deliver_response(200,"Deal already in Play - try Discard and Draw",NULL );
						}
					} 
					else 
					{
						deliver_response(200, "Deal failed - with Game Status: ".$_SESSION['GameStatus'], NULL);
					}
				break;
				case 'discard':
					if($_SESSION['GameStatus'] != 'ended')
					{
						if(empty($count) || $count == 0)
						{ 
							deliver_response(400,"Discard Requires one or more cards to return to the dealer", NULL);
							break;
						}
						else 
						{
							if(!empty($cardList) && count($cardList) >=1)
							{
								$cards = new cards();
								$newPlayerHand = $cards->discard($cardList);
								if($newPlayerHand !== false)
								{
									$_SESSION['PlayerHand'] = $newPlayerHand;
									$_SESSION['HandCount'] = count($_SESSION['PlayerHand']);
									$jsonObj = ['hand' => $_SESSION['PlayerHand'], 'handCount' =>$_SESSION['HandCount']];
									deliver_response(200,"Discard Successful", $jsonObj);
								}
								else
								{
									deliver_response(200,"Discard Failed -  be sure to select a card first.", NULL);
								}
							}
							else
							{
								deliver_response(400,"Discard Failed -  be sure to select a card first.", NULL);
							}
						}
					}
					else 
					{
						deliver_response(200, "Discard failed - with Game Status: ".$_SESSION['GameStatus'], NULL);
					}
				break;
				case 'draw':
					if($_SESSION['GameStatus'] != 'ended')
					{
						$cards = new cards();
						$newCards = $_SESSION['Cards'];
						$handArray = $_SESSION['PlayerHand'];
						$currentHandCount = count($handArray);
						$sortedHands = array();
						//$_SESSION['HandCount'] = $currentHandCount;
						if ($currentHandCount + $count != 5)
						{
							deliver_response(400,"Your Current Hand plus the requested cards Must equal 5.", NULL);
							return false;
						}
						$_SESSION['Player0']['HandRank'] = ''; 
						if($cards->draw($currentHandCount, $newCards, $handArray ))
						{
							$sortedHands['Player0'] = sortHand($_SESSION['PlayerHand'],'Player0');
							$_SESSION['PlayerHand'] = $sortedHands['Player0'];
							$jsonObj = [
								'hand' => $_SESSION['PlayerHand'], 
								'handCount' =>$_SESSION['HandCount'],
								'deckCount'=>$_SESSION['DeckCount'] ,
								'handRank' =>$_SESSION['Player0']['HandRank'] 
								];
							deliver_response(200,"Draw Successful", $jsonObj);
						} 
						else 
						{
							deliver_response(400,"Draw was incomplete: Game is Void.", NULL);
						}
					} 
					else 
					{
						deliver_response(200, "Draw failed - with Game Status: ".$_SESSION['GameStatus'], NULL);
					}
				break;
				case 'placeBet':
					if(isset($bet) && $bet > 0 && $bet <= $_SESSION['Player0']['Bank'])
					{
						$_SESSION['Player0']['Bet'] = $bet;
						$_SESSION['Player0']['Bank'] -= $bet;
						$jsonObj = ['playerCash' => $_SESSION['Player0']['Bank']];
						deliver_response(200,"Bet was Successful", $jsonObj);
					}
					else
					{
						deliver_response(400,"Bet is not a valid number.", NULL);
					}
				break;
				case 'call':
					$_SESSION['GameStatus'] = 'ended';
					$sortedHands = array();
// temp create a set of hands that can be used to test various hand patterns.

// createHands(playerCount, rank[mixed][')
/* 
$_SESSION['PlayerCount'] = 4;
$_SESSION['PlayerHand'] =         array(array(24,'diams;','10'),array(25,'hearts;','10'),array(51,'spades;','10'), array(12,'diams;','9'), array(38,'spades;','9'));
$_SESSION['NonePlayerHands'][0] = array(array(14,'diams;','8'), array(26,'spades;','8'),array(52,'clubs;','8'), array(13,'clubs;','J'),array(39,'hearts;','J'));
$_SESSION['NonePlayerHands'][1] = array(array(14,'spades;','4'), array(26,'hearts;','4'), array(52,'diams;','Q'),array(13,'hearts;','Q'), array(39,'spades;','Q'));
$_SESSION['NonePlayerHands'][2] = array(array(14,'spades;','A'), array(26,'hearts;','A'), array(39,'diams;','A'), array(52,'diams;','9'),array(13,'spades;','9'));
*/// end-temp					
					$sortedHands['player0'] = $_SESSION['PlayerHand'];
					for($x = 1; $x < $_SESSION['PlayerCount']; $x++ )
					{
						$sortedHands['player'.$x] = sortHand($_SESSION['NonePlayerHands'][$x-1],'Player'.$x);

					}
 //debug($sortedHands, true);
					$jsonObj = array();
					$jsonObj['playerCount'] = (int) $_SESSION['PlayerCount'];
					$jsonObj['winner'] = $_SESSION['Winner'];
					$jsonObj['hands'][0] = $_SESSION['PlayerHand'];
					$jsonObj['ranks'][0] = $_SESSION['Player0']['HandRank'];
/* debug($_SESSION['Player0']); */					

/* possible ranks
	Royal Flush 
	Straight Flush
	Four of a kind
	Full House
	Flush
	Straight
	Three of a kind
	Two pair
	One pair
	High Card
*/					
/* 
$_SESSION['Player0']['HandRank'] = 'Full House';
$_SESSION['Player1']['HandRank'] = 'Full House';
$_SESSION['Player2']['HandRank'] = 'Full House';
$_SESSION['Player3']['HandRank'] = 'Full House'; 
$_SESSION['Player4']['HandRank'] = 'Full House'; 
*/

//debug($_SESSION['Player0']['HandRank']);
//debug($_SESSION['Player1']['HandRank']);

					for($x = 0; $x < $_SESSION['PlayerCount']; $x++ )
					{
						$jsonObj['hands'][$x] = $sortedHands['player'.$x];
						$jsonObj['ranks'][$x] = $_SESSION['Player'.$x]['HandRank'];
					}
/* debug($jsonObj['ranks']); */
					// find winner in the sortedHands Array
					
					//'0'; // 0-4 player is player 0 ..  Create function getWinner($sortedHands) returns the index of the hand that won;
					
					$_SESSION['Winner'] = findWinner($jsonObj['hands'],$jsonObj['ranks']);
//debug($_SESSION['Winner']);
//die();					
					$jsonObj['winner'] = $_SESSION['Winner']; // player index
					if(count($jsonObj) > 0)
					{
						deliver_response(200,"Call was Successful", $jsonObj);
					}
					else 
					{
						deliver_response(400,"Call was Not Successful", NULL);
					}
				break;
				default:
					deliver_response(400,"Invalid Request", NULL);
				break;
			}
		}
	}
	else
	{
		// throw invalid request.
		deliver_response(400,"Invalid Request", NULL);
	}
//--------------------------------------------------------------------------//
	function findhighestCard($sortedHands, $aceHigh = true)
	{
		$tempHighCard = $highCard =0;
		foreach($sortedHands as $key => $valueArray)
		{
			$tempHighCard = adjustFaceCard($valueArray[2],$aceHigh);
			if( $tempHighCard > $highCard)
			{
				$highCard = $tempHighCard;
			}
		}
		return $highCard; 
	}
	function getNextHighestCard($sortedHands, $oldHighestCard, $aceHigh = true)
	{
		$tempArray = array();
		$tempNextHighestCard = $nextHighestCard = 0;
		foreach( $sortedHands as $key => $valueArray)
		{
			$tempNextHighestCard = adjustFaceCard($valueArray[2], $aceHigh);
			if( ($tempNextHighestCard !== $oldHighestCard) && $tempNextHighestCard > $nextHighestCard )
			{
				$nextHighestCard = $tempNextHighestCard;
			}
		}
		return $nextHighestCard;
	}
	
	function adjustFaceCard($card, $aceHigh = true)
	{
	
		switch($card)
		{
			case 'A':
				if($aceHigh)
					$card = 14;
				else
					$card = 1;
			break;
			case 'K':
				$card = 13;
			break;
			case 'Q':
				$card = 12;
			break;
			case 'J':
				$card = 11;
			break;
			default:
				$card = (int) $card;
		}
		return $card;
	}
	
	function findOnePair($sortedHands, $aceHigh=true)
	{
		$onePair = $highCard = $index = $currentCard = '';
		foreach($sortedHands as $key => $valueArray)
		{
			$tempCard = $tempHighCard = '';
			$tempCard = adjustFaceCard($valueArray[2],$aceHigh);
			if( empty($currentCard) )
			{
				$currentCard = $tempCard;
				$onePair[] = $tempCard;
				continue; 
			}
			if( $tempCard != $currentCard)
			{
				$currentCard = $tempCard; 
				if(count($onePair) < 2) 
				{
					$tempHighCard = array_pop($onePair);
					$onePair[] = $tempCard; 
				}
				else
				{
					$tempHighCard = $tempCard; 
				}
				if( $tempHighCard > $highCard) 
				{
					$highCard = $tempHighCard; 
				}
			} 
			elseif(count($onePair) < 2) 
			{
				$currentCard = $tempCard; 
				$onePair[] = $tempCard; 
			}
		}
		return array('pair'=> $onePair, 'highCard'=>$highCard);
	}
	
	function findhighestTwoPair($sortedHands,$aceHigh = true)
	{
		$firstPair = $secondPair = $fifthCard = $currentCard = '';
		$tempFirstPair = $tempSecondPair = array();
		$tempFifthCard = '';
		foreach($sortedHands as $key => $valueArray)
		{
			$tempCard = '';
			$tempCard = adjustFaceCard($valueArray[2],$aceHigh);
			if( empty($tempFirstPair) )
			{
				$currentCard = $tempCard; 
				$tempFirstPair[] = $tempCard; 
				continue;
			}
			if(count($tempFirstPair) < 2)
			{
				if($tempCard == $currentCard) 
				{
					$currentCard = $tempCard; 
					$tempFirstPair[] = $tempCard;
					continue;
				} 
				else 
				{
					// This assumes pattern of 12233 vs 11223
					$tempFifthCard = array_pop($tempFirstPair); 
					$currentCard = $tempCard; 
					$tempFirstPair[] = $tempCard;
					continue;
				}
			}
			if (count($tempFirstPair) == 2 && empty($tempSecondPair) )
			{
				$currentCard = $tempCard;
				$tempSecondPair[] = $tempCard; 
				continue;
			}
			if( count($tempFirstPair) == 2 && count($tempSecondPair) < 2)
			{
				// this assume pattern of 11223 vs 12233
				if($tempCard == $currentCard) 
				{
					$currentCard = $tempCard;
					$tempSecondPair[] = $tempCard;
					continue;
				}
				else
				{
					$tempFifthCard = array_pop($tempSecondPair);
					$tempSecondPair [] = $tempCard;
					$currentCard = $tempCard;
					continue;
				}
			}
			else
			{
				$tempFifthCard = $tempCard;
			}
		}
	return ['firstPair'=>$tempFirstPair, 'secondPair' => $tempSecondPair, 'fifthCard' => $tempFifthCard];
	}
	
	function findhighestThreeOfAKind($sortedHands,$aceHigh = true)
	{
		$threeOfAKindArray = array();
		$tempCard = $currenCard = '';
		foreach($sortedHands as $key => $valueArray)
		{
			$tempCard = adjustFaceCard($valueArray[2],$aceHigh);
			if(empty($currentCard))
			{
				$currentCard = $tempCard;
				$threeOfAKindArray[] = $currentCard;
				continue;
			}
			if($tempCard == $currentCard)
			{
				$threeOfAKindArray[] = $tempCard;
				if(count($threeOfAKindArray) == 3)
				{
					break;
				}
			}
			else
			{
				array_pop($threeOfAKindArray);
				$currentCard = $tempCard;
			}
		}
		return $currentCard;
	}
	
	function findhighestFourOfAKind($sortedHand, $index)
	{
		// four of a kind.
		// 1 aces
		// 2 kings
		// 3 queens
		// etc.
		// if four of a kind is aces then fifth card cannot be an ace.
		// assumes the the cards are in order.
		//[0]=> array(3) {[0]=>1  ,[1]=>"spades;" ,[2]=>"A"}
		//[1]=> array(3) {[0]=>14 ,[1]=>"diams;"  ,[2]=>"A"}
		//[2]=> array(3) {[0]=>27 ,[1]=>"clubs;"  ,[2]=>"A"}
		//[3]=> array(3) {[0]=>40 ,[1]=>"hearts;" ,[2]=>"A"}
		//[4]=> array(3) {[0]=>52 ,[1]=>"hearts;" ,[2]=>"K"}
		$fourOfAKindHand = $sortedHand[$index];
		$fourOfAKindValue = $fourOfAKindHand[4][2];
		return $fourOfAKindValue;
	}

	function findFullHouse($sortedHands, $aceHigh=true)
	{
		$fullHouseTrips = $fullHousePair = array();
		foreach($sortedHands as $key => $valueArray)
		{
			$tempFullHouseTrips[] = adjustFaceCard($valueArray[2],$aceHigh); 
		}
		sort($tempFullHouseTrips);
		
		$tempTrips = '';
		foreach($tempFullHouseTrips as $key => $value)
		{
			if(empty($fullHouseTrips))
			{
				$fullHouseTrips[] = $tempFullHouseTrips[$key];
				continue;
			}
			if($tempFullHouseTrips[$key] == $fullHouseTrips[0])
			{
				$fullHouseTrips[] = $tempFullHouseTrips[$key];
			}
			else
			{
				$fullHousePair[] = $tempFullHouseTrips[$key];
			}
		}
		$tempArray = array();
		if(count($fullHouseTrips) == 2)
		{
			$tempArray = $fullHouseTrips;
			$fullHouseTrips = $fullHousePair;
			$fullHousePair = $tempArray;
		}
		return $fullHouseTrips[0];
	}
	
	//***********************************************************************//
	function findWinner($sortedHands, $sortedRanks)
	{
	 	$_straightflush = $_fourofakind = $_fullhouse = $_flush = $_straight =$_threeofakind = $_twopair = $_onepair = $_highcard = array();
		foreach($sortedRanks as $key => $rank)
		{
			// test for:
			// 1. Royal Flush 
			if($sortedRanks[$key] == 'Royal Flush')
			{
				return $key;
			}
		}
		foreach($sortedRanks as $key => $rank)
		{
			// count rank types
			// test for:
			// 2. Straight Flush
			// 3. Four of a kind
			// 4. Full House
			// 5. Flush
			// 6. Straight
			// 7. Three of a kind
			// 8. Two pair
			// 9. One pair
			//10. High Card
			// although not likely there may be multiple straight flush, flush 4 of a kind etc hands.
			// determine how many of each hand types are in play and then determine who has the highest values.
			switch($rank)
			{
				case "Straight Flush":
					$_straightflush [] = $key;
				break;
				case "Four of a kind":
					$_fourofakind [] = $key;
				break;
				case "Full House":
					$_fullhouse [] = $key;
				break;
				case "Flush":
					$_flush [] = $key;
				break;
				case "Straight":
					$_straight [] = $key;
				break;
				case "Three of a kind":
					$_threeofakind [] = $key;
				break;
				case "Two Pair":
					$_twopair [] = $key;
				break;
				case "One Pair":
					$_onepair [] = $key;
				break;case "High Card":
					$_highcard [] = $key;
				break;
			}
		}	
		if( count($_straightflush)  === 1 )
		{
			return $_straightflush[0];
		} 
		elseif( count($_straightflush) > 1)
		{
			// find the highest card in the hand array for the indexes
			$highestCardIndex = '';
			$highestCard = 0;
				
			foreach ($_straightflush as $key => $index)
			{
				$result = array();
				$tempIndex = $tempCard = '';
				$result [] = findhighestCard($sortedHands[$index]);
				if(!empty($result))
				{
					$tempIndex = $result[0];
					$tempCard = $result[1];
					if($tempCard > $highestCard)
					{
						$highestCardIndex = $tempIndex;
						$highestCard = $tempCard;
					}
				} else {
					die('Array Failure');
				} 
			}
			return $highestCardIndex;
		}
		if( count($_fourofakind) === 1)
		{
			return $_fourofakind[0];
		}
		elseif( count($_fourofakind) > 1)
		{
			// find the highest four of a kind value A, K, Q,.....2
	
			$highestFourOfAKindIndex = '';
			$highestFifthCard = '';
			$winner = '0';
			// $highestFourEqual = array();
			//[0],..,[4]
			foreach( $_fourofakind as $key)
			{
				$result = $tempIndex = $tempHighestFourOfAKind = '';
				
				$result = findhighestFourOfAKind($sortedHands,$key);
				$result = adjustFaceCard($result);
				// result should be what is four of a kind card value
				if(!empty($result))
				{
					$tempIndex = $key;
					$tempHighestFourOfAKind = $result;
					if($highestFourOfAKindIndex == '')
					{
						$highestFourOfAKindIndex = $tempHighestFourOfAKind;
						if($tempHighestFourOfAKind == 'A'){
							return $tempIndex;
						}
						$winner = $tempIndex;
						continue;
					}
					elseif($tempHighestFourOfAKind == 'A')
					{
						return $tempIndex;
					}
					if($tempHighestFourOfAKind > $highestFourOfAKindIndex)
					{
						$highestFourOfAKindIndex = $tempHighestFourOfAKind;
						$winner = $tempIndex;
					}
				} else {
					die('Array Failure');
				} 
			}
			return $winner;
		}
		elseif( count($_fullhouse) === 1)
		{
			return $_fullhouse[0];
		}
		elseif( count($_fullhouse) > 1)
		{
			// find what the cards of the full house are,
			// [A A A ? ?] beats all others full houses,
			//else
			// [K K K ? ?], [Q Q Q ? ?], [J J J ? ?], [10 10 10 ? ?] ...[2 2 2 ? ?] 
			// I expect to see two patterns 11222 or 11122
			$fullHouseIndex = '';
			$fullHouse = '';
			foreach($_fullhouse as $key => $index)
			{
				$tempFullHouse = '';
				$tempFullHouse = findFullHouse($sortedHands[$index]);
				if($tempFullHouse > $fullHouse)
				{
					$fullHouse = $tempFullHouse;
					$fullHouseIndex = $key;
				}
			}
			return $fullHouseIndex;
		}
		elseif( count($_flush) === 1)
		{
			return $_flush[0];
		}
		elseif( count($_flush) > 1)
		{
			// Flush with highest card wins.  In case where highest card is identical then
			// hand with highest secondary card wins.  Etc.
			$highCard = $nextHighestCard = '';
			foreach ($_flush as $key => $index)
			{
				$result = array();
				$tempIndex = $tempHighCard = $tempNextHighestCard = '';
				$result = findhighestCard($sortedHands[$index]);
				if(!empty($result))
				{
					$tempIndex = $index;
					$tempHighCard = $result;
					if($tempHighCard > $highCard)
					{
						$highCardIndex = $tempIndex;
						$highCard = $tempHighCard;
						$nextHighestCard = getNextHighestCard($sortedHands[$index],$highCard);
					}
					elseif($tempHighCard == $highCard)
					{
						$tempNextHighestCard = getNextHighestCard($sortedHands[$index],$highCard);
						if( $tempNextHighestCard > $nextHighestCard)
						{
							$highCardIndex = $tempIndex;
							$highCard = $tempHighCard;
						}
					}
				} else {
					die('Flush Failure');
				} 
			}
			return $highCardIndex;
		}
		elseif( count($_straight) === 1)
		{
			return $_straight[0];
		}
		elseif( count($_straight) > 1)
		{
			// Straight with highest card wins. In case where highest card is identical then
			// hand with highest secondary card wins.  Etc.
			$highestCardIndex = $highestCard = '';
			foreach($_straight as $key => $index)
			{
				$tempHighestCard = '';
				$tempHighestCard = findhighestCard($sortedHands[$index],false); // false is to indicate that ace is = 1 not 14
				if($tempHighestCard > $highestCard)
				{
					$highestCardIndex = $index;
					$highestCard = $tempHighestCard;
				}
			}
			return $highestCardIndex;
		}
		elseif( count($_threeofakind) === 1)
		{
			return $_threeofakind[0];
		}
		elseif( count($_threeofakind) > 1)
		{
			// Highest Three of a kind wins.
			$highestThreeOfAKindIndex = $highestThreeOfAKind = '';
			foreach($_threeofakind as $key => $index)
			{
				$tempHighestThreeOfAKind = '';
				$tempHighestThreeOfAKind = findhighestThreeOfAKind($sortedHands[$index],true); // false is to indicate that ace is = 1 not 14
				if($tempHighestThreeOfAKind > $highestThreeOfAKind)
				{
					$highestThreeOfAKindIndex = $index;
					$highestThreeOfAKind = $tempHighestThreeOfAKind;
				}
			}
			return $highestThreeOfAKindIndex;
		}
		elseif( count($_twopair) === 1)
		{
			return $_twopair[0];
		}
		elseif( count($_twopair) > 1)
		{
			// highest pair of two pair wins. In case where highest pairs are identical then highest second pair wins.
			// In case where both pairs are identical then highest fifth card wins.
			$highestPairIndexOne = $highestPairIndexTwo = '';
			$highestTwoPair = array();
			$highestPairOne = $highestPairTwo = $pairInit = array('firstPair'=>'','secondPair'=>'','fifthCard'=>'','index'=>'');
			$highestFifthCardOne = $highestFifthCardTwo = ''; // in case we have two players that have 2233k 2233a
			foreach($_twopair as $key => $index)
			{
				$tempHighestTwoPair = array();
				$tempHighestTwoPair = findhighestTwoPair($sortedHands[$index],true); // false is to indicate that ace is = 1 not 14
				if(empty($highestPairOne['firstPair']))
				{
					$highestTwoPair = $tempHighestTwoPair;
					if($tempHighestTwoPair['firstPair'][0] > $tempHighestTwoPair['secondPair'][0] )
					{
						$highestPairOne['firstPair'] = $tempHighestTwoPair['firstPair'][0];  // A A
						$highestPairOne['secondPair'] = $tempHighestTwoPair['secondPair'][0]; // 8 8
					}
					else
					{
						$highestPairOne['firstPair'] = $tempHighestTwoPair['secondPair'][0];
						$highestPairOne['secondPair'] = $tempHighestTwoPair['firstPair'][0];
					}
					$highestPairOne['fifthCard'] = $tempHighestTwoPair['fifthCard']; // K
					$highestPairOne['index'] = $index;
					continue; // go get then next hand.
				}
				if(empty($highestPairTwo['firstPair']))
				{
					if($tempHighestTwoPair['firstPair'][0] > $tempHighestTwoPair['secondPair'][0] )
					{
						$highestPairTwo['firstPair'] = $tempHighestTwoPair['firstPair'][0];
						$highestPairTwo['secondPair'] = $tempHighestTwoPair['secondPair'][0];
					}
					else
					{
						$highestPairTwo['firstPair'] = $tempHighestTwoPair['secondPair'][0];
						$highestPairTwo['secondPair'] = $tempHighestTwoPair['firstPair'][0];
					}
					$highestPairTwo['fifthCard'] = $tempHighestTwoPair['fifthCard'];
					$highestPairTwo['index'] = $index;
				}
				if($highestPairTwo['firstPair'] > $highestPairOne['firstPair'])
				{
					$highestPairOne = $highestPairTwo;
					$highestPairTwo = $pairInit;
					continue;
				}
				elseif($highestPairTwo['firstPair'] == $highestPairOne['firstPair'])
				{
					if($highestPairTwo['secondPair'] > $highestPairOne['secondPair'])
					{
						$highestPairOne = $highestPairTwo;
						$highestPairTwo = $pairInit;;
						continue;
					}
					if($highestPairTwo['secondPair'] == $highestPairOne['secondPair'])
					{
						if($highestPairTwo['fifthCard'] > $highestPairOne['fifthCard'])
						{
							$highestPairOne = $highestPairTwo;
							$highestPairTwo = $pairInit;
							continue;
						}
					}
				}
				else
				{
					$highestPairTwo = $pairInit;
				}
			}
			return $highestPairOne['index'];
		}
		elseif( count($_onepair) === 1)
		{
			return $_onepair[0];
		}
		elseif( count($_onepair) > 1)
		{
			// highest pair wins. In case where highest pair is identical then highest other card wins
			$onePairArray = $resetArray = array('pair'=>'', 'highCard'=>'', 'index'=>'');
			foreach($_onepair as $key => $index)
			{
				$result = array();
				$result = findOnePair($sortedHands[$index],true);
				if($result['pair'][0] > $onePairArray['pair']) // 14 > '' true
				{
					$onePairArray['pair'] = $result['pair'][0];
					$onePairArray['highCard'] = $result['highCard'];
					$onePairArray['index'] = $index;
				}
				elseif ($result['pair'][0] == $onePairArray['pair'])
				{
					if($result['highCard'] > $onePairArray['highCard'])
					{
						$onePairArray['pair'] = $result['pair'][0];
						$onePairArray['highCard'] = $result['highCard'];
						$onePairArray['index'] = $index;
					}
				}
			}
			return $onePairArray['index'];
		}
		else
		{
			// all must have $_highcard
			// highest card wins. equal high card then Next High Card wins
			$highCardIndex = $highCard = $nextHighestCard ='';
			foreach($_highcard as $key => $index)
			{
				$tempCard = array();
				$tempCard = findhighestCard($sortedHands[$index], $aceHigh = true);
				$tempNextHighestCard = getNextHighestCard($sortedHands[$index], $tempCard);
				if($tempCard > $highCard)
				{
					$highCard = $tempCard;
					$nextHighestCard = $tempCard['nextHighestCard'];
					$highCardIndex = $index;
				}
				elseif($tempCard == $highCard)
				{
/// this does not allways work
					if ($tempNextHighestCard > $nextHighestCard )
					{
						$highCard = $tempCard;
						$nextHighestCard = $tempNextHighestCard;
						$highCardIndex = $index;
					}
				}
			}
			return $highCardIndex;
		}
		return false;
	}

	function swapSuiteSymbol($symbol, $in=true)
	{
		// &spades = 1 = &spades
		// &diams  = 2 = &diams
		// &clubs  = 3 = &clubs
		// &hearts = 4 = &hearts
		if($in)
		{
			switch($symbol){
				case "spades;":
					return '1';
				break;
				case 'diams;':
					return '2';
				break;
				case 'clubs;':
					return '3';
				break;
				case 'hearts;':
					return '4';
				break;
			}
		} else {
			switch($symbol){
				case 1:
					return 'spades;';
				break;
				case 2:
					return 'diams;';
				break;
				case 3:
					return 'clubs;';
				break;
				case 4:
					return 'hearts;';
				break;
			}
		}
	}
	
	function swapRank($cardValue, $in=true)
	{
		if($in){
			switch ($cardValue){
				case 'A':
					return '1';
				break;
				case 'K':
					return '13';
				break;
				case 'Q':
					return '12';
				break;
				case 'J':
					return '11';
				break;
				default:
					return $cardValue;
			}
		} else {
			switch ($cardValue){
				case '1':
					return 'A';
				break;
				case '13':
					return 'K';
				break;
				case '12':
					return 'Q';
				break;
				case '11':
					return 'J';
				break;
				default:
					return $cardValue;
			}
		}
	}
	
	function sortCallBack($key)
	{
		return function ($a, $b) use ($key) {
			return strnatcmp($a[$key], $b[$key]);
		};
		
	}
	
	function rankedHands($newArray,$player)
	{
		// test for:
		// 1. Royal Flush
		// 2. Straight Flush
		// 3. Four of a kind
		// 4. Full House
		// 5. Flush
		// 6. Straight
		// 7. Three of a kind
		// 8. Two pair
		// 9. One pair
		//10. High Card
		$suite = array();
		$rank = array();
		$_SESSION[$player]['HandRank'] ='';
		foreach($newArray as $key => $card){
			$suite[] = $card['suite'];
			$rank[] = $card['rank'];
		}
		
		sort($rank);
		$royalFlush = $straightFlush = $fourOkAKind = $fullHouse = $flush = $straight = $threeOfAKind = $twoPair = $onePair = $highCard = false;
		$handCountArray = ['aces'=> 0,'twos' => 0, 'threes'=> 0, 'fours' => 0, 'fives' => 0, 'sixes' => 0, 'sevens' => 0 ,'eights'=>0, 'nines' => 0, 'tens' => 0, 'jacks' => 0, 'queens' => 0, 'kings' => 0];
		$firstSuite = $suite[0];
		// test for flush
		for( $x = 1; $x < count($suite); $x++){
			// if any of the cards are a different suite then we are done testing for Flush.
			//  This is relevant for RoyalFlush, StraightFlush as well as Flush
			if($suite[$x] != $firstSuite){
				$flush = false;
				break;
			} else {
				$flush = true;
			}
		}
		
		for ($x = 0; $x < count($rank); $x ++){
			switch ($rank[$x]){
				case '1':
					$handCountArray['aces'] ++;
				break;
				case '2':
					$handCountArray['twos'] ++;
				break;
				case '3':
					$handCountArray['threes'] ++;
				break;
				case '4':
					$handCountArray['fours'] ++;
				break;
				case '5':
					$handCountArray['fives'] ++;
				break;
				case '6':
					$handCountArray['sixes'] ++;
				break;
				case '7':
					$handCountArray['sevens'] ++;
				break;
				case '8':
					$handCountArray['eights'] ++;
				break;
				case '9':
					$handCountArray['nines'] ++;
				break;
				case '10':
					$handCountArray['tens'] ++;
				break;
				case '11':
					$handCountArray['jacks'] ++;
				break;
				case '12':
					$handCountArray['queens'] ++;
				break;
				case '13':
					$handCountArray['kings'] ++;
				break;
			}
		}
		if($flush){
			// test for RoyalFlush, then straight Flush then flush
			if( $handCountArray['aces']  === 1 &&
				$handCountArray['tens']  === 1 &&
				$handCountArray['jacks'] === 1 &&
				$handCountArray['queens']=== 1 &&
				$handCountArray['kings'] === 1
			){
				$royalFlush = true;
				$_SESSION[$player]['HandRank'] = 'Royal Flush';
				return; 
			}
			if( $rank[1] == $rank[0]+1 &&
				$rank[2] == $rank[1]+1 &&
				$rank[3] == $rank[2]+1 &&
				$rank[4] == $rank[3]+1)
			{
				$straightFlush = true;
				$_SESSION[$player]['HandRank'] = 'Straight Flush';
				return;
			} else {
				$flush = true;
				$_SESSION[$player]['HandRank'] = 'Flush';
				return;
			}
		}
		if( 	
			$handCountArray['aces'] == 4 ||
			$handCountArray['twos'] == 4 ||
			$handCountArray['threes'] == 4 ||
			$handCountArray['fours'] == 4 ||
			$handCountArray['fives'] == 4 ||
			$handCountArray['sixes'] == 4 ||
			$handCountArray['sevens'] == 4 ||
			$handCountArray['eights'] == 4 ||
			$handCountArray['nines'] == 4 ||
			$handCountArray['tens'] == 4 ||
			$handCountArray['jacks'] == 4 ||
			$handCountArray['queens'] == 4 ||
			$handCountArray['kings'] == 4 
		){
			$fourOfAKind = true;
			$_SESSION[$player]['HandRank'] = 'Four of a kind';
			return;
		}
		if(	
			$handCountArray['aces'] == 3 ||
			$handCountArray['twos'] == 3 ||
			$handCountArray['threes'] == 3 ||
			$handCountArray['fours'] == 3 ||
			$handCountArray['fives'] == 3 ||
			$handCountArray['sixes'] == 3 ||
			$handCountArray['sevens'] == 3 ||
			$handCountArray['eights'] == 3 ||
			$handCountArray['nines'] == 3 ||
			$handCountArray['tens'] == 3 ||
			$handCountArray['jacks'] == 3 ||
			$handCountArray['queens'] == 3 ||
			$handCountArray['kings'] == 3 
		){
			if(
				$handCountArray['aces'] == 2 ||
				$handCountArray['twos'] == 2 ||
				$handCountArray['threes'] == 2 ||
				$handCountArray['fours'] == 2 ||
				$handCountArray['fives'] == 2 ||
				$handCountArray['sixes'] == 2 ||
				$handCountArray['sevens'] == 2 ||
				$handCountArray['eights'] == 2 ||
				$handCountArray['nines'] == 2 ||
				$handCountArray['tens'] == 2 ||
				$handCountArray['jacks'] == 2 ||
				$handCountArray['queens'] == 2 ||
				$handCountArray['kings'] == 2 
			)
			{
				$fullHouse = true;
				$_SESSION[$player]['HandRank'] = 'Full House';
				return;
			} else {
				$threeOfAKind = true;
				$_SESSION[$player]['HandRank'] = 'Three of a kind';
				return;
			}
		}

		if(	
			$rank[1] == $rank[0]+1 &&
			$rank[2] == $rank[1]+1 &&
			$rank[3] == $rank[2]+1 && 
			$rank[4] == $rank[3]+1 
		){
			$straight = true;
			$_SESSION[$player]['HandRank'] = 'Straight';
			return;
		} 
		$countPairs = 0;
		foreach($handCountArray as $key => $value)
		{
			if($handCountArray[$key] == 2)
			{
				$countPairs ++;
			}
		}
		if ( $countPairs == 2)
		{
			$twoPair = true;
			$_SESSION[$player]['HandRank'] = 'Two Pair';
			return;
		} 
		if ($countPairs === 1)
		{
			$onePair = true;
			$_SESSION[$player]['HandRank'] = 'One Pair';
			return;
		}
		
		$highCard = true;
		$_SESSION[$player]['HandRank'] = 'High Card';
		return;
	}
	function sortHand($handArray,$player)
	{
		$newArray = array();
		foreach($handArray as $key => $value){
			$newArray[]=['index'=>$value[0], 'suite'=>swapSuiteSymbol($value[1],true), 'rank'=>swapRank($value[2],true)];
		}
 		//usort($newArray, sortCallBack('suite'));
		usort($newArray, sortCallBack('rank'));
		// rank each hand.
 		rankedHands($newArray,$player);
		$handArray = array();
		foreach($newArray as $key => $value){
			$handArray[]=[$value["index"], swapSuiteSymbol($value["suite"],false), swapRank($value["rank"],false)];
		}
		return $handArray;
	}
	function deliver_response($status, $status_message, $data)
	{
		header("HTTP/1.1 $status, $status_message ");
		
		$response['status'] = $status;
		$response['status_message'] = $status_message;
		$response['data'] = $data;
		
		$json_responce = json_encode($response);
		echo $json_responce;
	}
?>

