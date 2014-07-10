/*************************
* author: Rodney Olivier;
* date:06/29/2014
* version: 0.1.0
* last modified date 07/10/2014
* copyright©2014
**************************/
$(document).ready(function(){
/* console.log(window.screen.width);
console.log(window.screen.height);*/
	
//alert(window.innerWidth+'='+ window.innerHeight);
	/*$("#centerElement").center(); */ 
	$('#numberOfPlayers').spinner();
	$('#bet').spinner();//.spinner("disable");;
	$('.button').button();
	$('#submitBet').prop('disabled', true);
	/* $('#nextRound').prop('disabled', true); */
	$('#submitBet').click(function(){
		$.get('cards.php/placeBet?bet='+$('#bet').val(), function(data) {
			var betData = jQuery.parseJSON(data);
			_data = betData.data;
			if( betData){
				$('#playerCash').text("Coins In Bank:"+_data.playerCash);
			} else {
				_message = betData.status_message;
				$('#statusMessage').html(_message).removeClass('hidden');
			}
		});
	});
	$('#help').click( function(){
//alert(help()); 	
		_html = '';
		_width  = window.innerWidth;
		_height = window.innerHeight;
		$('<div/>').dialog({
			modal:true,
			//id:"helpText", 
			title:"Help - Users Guide",
			open: function(){
				var _html = help();
				$(this).html(_html);
			},
			width:_width*.60,//700, // figure out by screen width and height what these values should be.
			height: _height*.92,//700,
			buttons: {
				"Ok": function() { 
					$(this).dialog("close"); 
				}, 
				"Cancel": function() { 
					$(this).dialog("close"); 
				} 
			}
		});
		/* $("#helpText").toggleClass('hidden'); */
		/* $("#statusBlock").toggleClass('hidden'); */
	});
/* 	$('#helpText').click( function(){
		$("#helpText").toggleClass('hidden');
		$("#statusBlock").toggleClass('hidden');
	}); */
	$('#deckCount').text("Deck Count: ");
	$('#handCount').text("Hand Count: ");
	$('#handRank').text("Hand Rank: ");
	$.ajaxSetup ({ cache: false });
	
		$('#newGame').click(function(){
			newGame();
			// work through the state factors so that the next item does not fire until the the state is 200.
			/*shuffle();
			deal(); */
		});
		$('#nextRound').click(function(){
			nextRound();
		});
		
		$('#shuffle').click(function(){
			shuffle();
		});
		$('#deal').click(function(){
			deal();
		});
		$('#discard').click(function()
		{
			deckCount = $('#deckCount').text();
			deckCount = deckCount.split(':');
			deckCount = deckCount[1];
			cardList = $('.cardId:checkbox:checked');
			if(cardList.length < 5 && cardList.length > 0 && deckCount > 0 && (deckCount - cardList.length >= 0) )
			{
				var listOfCards = '';
				$.each(cardList, function( key,value) {
					listOfCards += cardList[key].value +','
				});
				listOfCards = listOfCards.substring(0,listOfCards.length-1);
				$.get( "cards.php/discard?cards="+listOfCards, function( data ) {
					var cardData = jQuery.parseJSON(data);
					_data = cardData.data;
					_message = cardData.status_message;
					if(_data){
						for ( x = 0; x < 5; x++){
							$('#card'+x).html('');
							$('#card'+x).removeClass('cardLiHi');
						}
						$('#handCount').text("Hand Count: "+_data.hand.length);
						buildCard (_data);
					}
					$('#status').html(_message);
				});
			}
		});
		$('#draw').click(function()
		{
			handCount = $('#handCount').text();
			handCount = handCount.split(':');
			handCount = 5 - handCount[1];
			$.get( "cards.php/draw?count="+handCount, function( data ) {
				var cardData = jQuery.parseJSON(data);
				_data = cardData.data;
				_message = cardData.status_message;
				if(_data){
					for ( x = 0; x < 5; x++){
						$('#card'+x).html('');
					}
					$('#deckCount').text("Deck Count: "+_data.deckCount);
					$('#handCount').text("Hand Count: "+_data.hand.length);
					$('#handRank').text("Hand Rank: "+_data.handRank);
					buildCard (_data);
				}
				$('#status').html(_message);
			});
		});
		$('#call').click(function()
		{
			vhtml='';
			$.get( "cards.php/call", function( data ) {
						
				var callData = jQuery.parseJSON(data);
				_data = callData.data;
				_message = callData.status_message;
				//$('#handBlock').addClass('hidden');
				if(_data)
				{
					playerCount = _data.playerCount;
//console.log(playerCount);
					hands = _data.hands;
					ranks = _data.ranks;
/*console.log(hands[1]);
console.log(ranks[1]);	*/			
					
					for(x=0; x < playerCount; x ++){
						if(_data.winner == x){
							winner = true;//'You are the winner!';
						} else {
							winner = false;//'';
						}
						 vhtml += buildCall(hands[x],ranks[x], x, winner);
					}
					_width  = window.innerWidth;
					_height = window.innerHeight;
					$('<div/>').dialog({
						modal:true,
						title:"Call - Declare Winner",
						open: function(){
console.log(vhtml);						
							$(this).html(vhtml);
						},
						width:_width*.60,//700, // figure out by screen width and height what these values should be.
						height: _height*.92,//700,
						buttons: {
							"Ok": function() { 
								$(this).dialog("close"); 
							}, 
							"Cancel": function() { 
								$(this).dialog("close"); 
							} 
						}
					});
					$('#betLable').text('Ante: ');
//console.log(vhtml);
					//$('#callBlock').removeClass('hidden');
					//$('#callBlock').dialog('open');
				} else {
					$('#status').html(_message);
				}
			});
			$('#nextRound').prop('disabled', false);
			
			
		});
});
	
	function newGame()
	{
	// calls Rest Server with repack() function.
		var numberOfPlayers = $("#numberOfPlayers").val();
//console.log(numberOfPlayers);			
		var jqxhr = $.get( "cards.php/newGame?numberofplayers="+numberOfPlayers, function( data ) {
			var cardData = jQuery.parseJSON(data);
			_message = cardData.status_message;
			$('#status').html(_message);
		}).done( function(){
			shuffle();
		}).fail(function(){
		
		}).always(function(){
			
		});
		
		jqxhr.always(function(){
		// to reset in the middle of a game. So no refresh is required.
			$('#submitBet').prop('disabled', true);
			$('#callBlock').addClass('hidden');
			$('#handBlock').removeClass('hidden');
			deckCount = $('#deckCount').text();
			deckCount = deckCount.split(':');
			deckCount = deckCount[1];
			if(deckCount){
				// flip the cards over
				$('.card-back').toggleClass('card-front');
				//  clear the card contents
				for ( x = 0; x < 5; x++){
					$('#card'+x).html('');
				}
			}
			for(x = 0; x < 5; x++){
				if($('#card'+x).hasClass('card-front')){
					$('#card'+x).removeClass('card-front').addClass('card-back');
				}
			}
			$('#nextRound').prop('disabled', true);
		});
	}								
	function nextRound()
	{
		$.get( "cards.php/repack", function( data ) {
			var cardData = jQuery.parseJSON(data);
			_message = cardData.status_message;
			$('#status').html(_message);
		}).done( function(){
			shuffle();
		}).fail(function(){
		
		}).always(function(){
			
		});
	}
	function shuffle()
	{
		// this maybe should be call automatically after newGame.
		$.get( "cards.php/shuffle", function( data ) {
			var cardData = jQuery.parseJSON(data);
			_message = cardData.status_message;
			$('#status').html(_message);
		}).done( function(){
			deal();
		}).fail(function(){
		
		}).always(function(){
			
		});
	}
	
	function deal()
	{
		// This should be called after Shuffle.
		$.get( "cards.php/deal?ante="+$('#bet').val(), function( data ) {
		var cardData = jQuery.parseJSON(data);
		_data = cardData.data;
		_message = cardData.status_message;
		
		}).done( function(){
			if(_data){
			$('#deckCount').text("Deck Count: "+_data.deckCount);
			$('#handCount').text("Hand Count: "+_data.hand.length);
			$('#handRank').text("Hand Rank: "+_data.handRank);
			$('#winpool').text("Win Pool: "+_data.winPool);
			
			$('#betLable').text('Bet: ');
			$('#playerCash').text("Coins In Bank:"+_data.playerCash);
			$('.card').removeClass('card-back').addClass('card-front');
			buildCard (_data);
			
		}
		}).fail(function(){
		
		}).always(function(){
			$('#submitBet').prop('disabled', false);
			$('#status').html(_message);
		});
		
		
		
		
	}	
	function buildCall (hand, rank,x,winner)
	{
		
		player = 'player'+x;
		xPlayer = 'Player'+(parseInt(x)+1);
		winnerText = ''
		if(winner)
		{
			if(x === 0)
			{
				winnerText = 'You are the winner!';
			}
			else
			{
				winnerText = 'The winner is Player'+(x+1)+' !';
			}
		}
//console.log(hand);
		cardsHtml = '<div class="player"><h3>'+xPlayer+'</h3></div>';
		for( y in  hand)
		{
/*
console.log(y);
console.log(hand)
console.log(hand[y]);
*/			
		
			cardHtml ='<div class="card-front-call card-call" >';
			
			
			//var cardKey   = hand[y][0];
			var cardSuite = hand[y][1];
			var cardValue = hand[y][2];
			cardColor = cardColorFunc(cardSuite);
			cardSuite = '&'+cardSuite;
			cardHtml += 
			'<div class="suiteLeft-call '+cardColor+'">'+cardSuite+'</div>'+
			'<div class="suiteCenter-call '+cardColor+'">'+cardValue+'</div>'+
			'<div class="suiteRight-call '+cardColor+'">'+cardSuite+'</div>';
			
			cardHtml +='</div>';
			cardsHtml += cardHtml;
//console.log(hand[y][x]);				
		}
		cardsHtml +='<div class="rank">'+rank+'</div>';
			if (winner)
			{
				cardsHtml +='<div class="winner borderRadius5" id="win'+player+'">'+winnerText+'</div>';
			}
			else
			{
				cardsHtml +='<div class="winner" id="win'+player+'">'+winnerText+'</div>';
			}
		
		cardsHtml += '<hr/>';
		return cardsHtml;
		//$('#'+player).html(cardsHtml)
//console.log(rank);
	}
	
	function cardColorFunc(cardSuite)
	{
		switch (cardSuite)
		{
			case 'spades;':
				cardColor = 'black';
			break;
			case 'clubs;':
				cardColor = 'black';
			break;
			case 'diams;':
				cardColor = 'red';
			break;
			case 'hearts;':
				cardColor = 'red';
			break;
		}
		return cardColor;
	}
	function buildCard (_data)
	{
//console.log(_data);
		for ( x = 0; x < _data.hand.length; x++){
			var cardKey   = _data.hand[x][0];
			var cardSuite = _data.hand[x][1];
			var cardValue = _data.hand[x][2];
//console.log(cardSuite);
			cardColor = cardColorFunc(cardSuite);
			
			cardSuite = '&'+cardSuite;
//console.log(cardSuite);
			$('#card'+x).html(
				'<label for="cardId'+x+'" id="labelId'+x+'">'+
				'<div class="suiteLeft '+cardColor+'">'+cardSuite+'</div>'+
				'<div class="suiteCenter '+cardColor+'">'+cardValue+'</div>'+
				'<div class="suiteRight '+cardColor+'">'+cardSuite+'</div>'+
				'</label>'+
				'<input type="checkbox" name="cardId'+x+'" id="cardId'+x+'" class="cardId" value="'+cardKey+'" />'
			);
			setClick();
		}
		function setClick() {
			$("article div").on('click', function(e){
				var isChecked = $('#'+ this.id +' input:checkbox' ).is(':checked');
				if(isChecked){
					$('#'+this.id).addClass('cardLiHi');
				} else {
					$('#'+this.id).removeClass('cardLiHi');
				}
			});
		}
	}
	function help()
	{
		var _html ="";
			_html +="<article id='helpText' >"; // class='help'
			_html +="	<h2 class='center'>Basic Rules</h2>";
			_html +="	<ul>";
			_html +="		<li><h2>Ante</h2>Enter 1  or more to Ante</li>";
			_html +="		<li><h2>NewGame</h2>Press New Game to To Start/Restart a game.</li>";
			_html +="		<li><h2>Bet</h2>Enter 1 or more to Bet</li>";
			_html +="		<li>";
			_html +="			<h2>Discard</h2>";
			_html +="			Click a card to mark it for discard.";
			_html +="			<ul>";
			_html +="				<li>You must select at least one card.</li>";
			_html +="				<li>You must keep at least one Card.</li>";
			_html +="				<li>Click the card again to un-mark it.</li>";
			_html +="			</ul>";
			_html +="			Click Discard to remove selected cards. (Cards will be highlighted.)";
			_html +="		</li>";
			_html +="		<li><h2>Draw</h2>Click Draw to select new cards.</li>";
			_html +="		<li><h2>Call</h2>Click Call to end the round and see the winner and other hands.</li>";
			_html +="		<li><h2>Next Round</h2>Press Next Round to continue the game after the current round is over.</li>";
			_html +="		<li>";
			_html +="			<h3>Possible hands in order of highest to lowest rank are:</h3>";
			_html +="			<ol>";
			_html +="				<li>Royal Flush </li>";
			_html +="				<li>Straight Flush</li>";
			_html +="				<li>Four of a kind</li>";
			_html +="				<li>Full House</li>";
			_html +="				<li>Flush</li>";
			_html +="				<li>Straight</li>";
			_html +="				<li>Three of a kind</li>";
			_html +="				<li>Two pair</li>";
			_html +="				<li>One pair</li>";
			_html +="				<li>High Card</li>";
			_html +="			</ol>";
			_html +="		</li>";
			_html +="		<li>Click Ok/Cancel or [X] to Hide this text.</li>";
			_html +="	</ul>";
			_html +="	<p>author: Rodney Olivier<br/>";
			_html +="	date: 06/29/2014<br/>";
			_html +="	version: 0.1.0<br/>";
			_html +="	copyright©2014 Rodney Olivier</p>";
			_html +="</article>";
			
		return _html;
	}