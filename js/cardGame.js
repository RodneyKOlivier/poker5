/*****************************************************
* author: Rodney Olivier;
* date:06/29/2014
* version: 0.1.0
* last modified date 07/17/2014
* contact: rodneyolivier@live.com
* copyright©2014 Rodney Olivier License Apache 2
*****************************************************/
$(document).ready(function(){
/* console.log(window.screen.width);
console.log(window.screen.height);*/
	
//alert(window.innerWidth+'='+ window.innerHeight);
	/*$("#centerElement").center(); */ 
	$('#numberOfPlayers').spinner();
	$('#bet').spinner();//.spinner("disable");;
	//$('.button').button();
	
	// audio element
	audioElement = document.createElement('audio');
	audioElement.setAttribute('autoplay', 'autoplay');
	
	$( "input[type=submit], a, button" )
	.button()
	.click(function( event ) {
		event.preventDefault();
	});
	$("#newGame").button();
	$("#standPat").button("disable");
	$("#discard").button("disable");
	$("#draw").button("disable");
	$("#call").button("disable");
	$("#nextRound").button("disable");
	$("#help").button();
	$('#submitBet').button("disable");
	
	$(function() {
		$( "input[type=submit], a, button" )
		.button()
		.click(function( event ) {
			event.preventDefault();
		});
	});

	
	/* $('#submitBet').attr('disabled');
	$('#submitBet').addClass('ui-state-disabled'); */
	
	$('#help').click( function()
	{
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
	});

	$('#deckCount').text("Deck Count: ");
	$('#handCount').text("Hand Count: ");
	$('#handRank').text("Hand Rank: ");
	$.ajaxSetup ({ cache: false });
	
	$('#newGame').click(function()
	{	betVal = $('#bet').val();
		if(parseInt(betVal) === 0)
		{
			playAudio();
			$('#bet').fadeOut(1000).fadeIn(1000).effect("highlight",{color:"#ff0000"},3000);
			$('#status').html('You must Ante at least 1 coin');
			$('#status').addClass('error');
			return false;
		}
		else
		{
			$('#status').removeClass('error');
			$('#status').html('');
			newGame();
		}
	});
	
	$('#nextRound').click(function()
	{
		nextRound();
	});
	
	$('#shuffle').click(function()
	{
		shuffle();
	});
	
	$('#deal').click(function(){
		deal();
	});
	
	$('#standPat').click(function()
	{
		standPat();
	});
	
 	$('#discard').click( function()
	{
		discard();
	});
	
	$('#draw').click(function()
	{
		draw();
	});
	$('#call').click(function()
	{
		call();
	});
});
/*  
$("#standPat").button("disable");
	$("#discard").button("disable");
	$("#draw").button("disable");
	$("#call").button("disable");
	$("#nextRound").button("disable");

*/
	function newGame()
	{
		$("#standPat").button("enable");
		$('#submitBet').button("enable");
		$("#nextRound").button("disable");
		$('#submitBet').click(function(){
			submitBet();
		});
		
		var numberOfPlayers = $("#numberOfPlayers").val();
		
	
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
			$('#callBind').text("");
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
			for(x = 1; x < numberOfPlayers; x++)
			{
				$('#sPlayer'+(x+1)).html('Player'+(x+1)+' Stat:_____');
				$('#sPlayer'+(x+1)).removeClass('hidden');
			}
			//playAudio('audio/cardFan2.mp3');//cardOpenPackage1.mp3
			/* $('#submitBet').removeAttr('disabled');
			$('#submitBet').removeClass('ui-state-disabled'); */
		});
	}	
	
	function standPat()
	{
		betVal = $('#bet').val();
		if(parseInt(betVal) === 0)
		{
			playAudio();
			$('#bet').fadeOut(1000).fadeIn(1000).effect("highlight",{color:"#ff0000"},3000);
			$('#status').html('You must Bet at least 1 coin');
			$('#status').addClass('error');
		}
		else
		{
			$('#status').html('');
			$('#status').removeClass('error');
			submitBet();
		}
		/* .effect( function() 
		{
			$(this).fadeOut(3000, "liner", complete);
			$(this).fadeIn(3000, "liner", complete);
			//'highlight', {}, 3000
			for(i=0;i<3;i++) {
				$(this).fadeTo('slow', 0.5).fadeTo('slow', 1.0);
			} 
		
		}); */
		//standPat should trigger a round of betting.
		/* var jqxhr = $.get( "cards.php/standPat", function( data ) {
			var cardData = jQuery.parseJSON(data);
			_message = cardData.status_message;
			$('#status').html(_message);
		}).done( function(){
			
		}).fail(function(){
		
		}).always(function(){
			
		}); */
	}
	
	function discard()
	{
	
		deckCount = $('#deckCount').text();
		deckCount = deckCount.split(':');
		deckCount = deckCount[1];
		cardList = $('.cardId:checkbox:checked');
		if(cardList.length < 5 && cardList.length > 0 && deckCount > 0 && (deckCount - cardList.length >= 0) )
		{
			$("#discard").button("disable");
			$("#call").button("disable");
			$("#standPat").button("disable");
			$('#status').html('');
			$('#status').removeClass('error');
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
					cardHtml = 
					'<label>'+
					'<div class="suiteLeft">&nbsp;</div>'+
					'<div class="suiteCenter">&nbsp;</div>'+
					'<div class="suiteRight">&nbsp;</div>'+
					'</label>'+
					'<input  class="cardId" type="checkbox">';
					
					for ( x = 0; x < 5; x++){
						$('#card'+x).html(cardHtml);
						$('#card'+x).removeClass('cardLiHi');
					}
					$('#handCount').text("Hand Count: "+_data.hand.length);
					$('#deckCount').text("Deck Count: "+_data.deckCount);
					buildCard (_data);
				}
				$('#status').html(_message);
			}).done(function(){
				$("#draw").button("enable");
			}).fail(function(){
			}).always(function(){
			
			});
		}
		else
		{
			playAudio();
			
			$('#status').html('You must select at least 1 card');
			$('#status').addClass('error');
			$('#status').fadeOut(1000).fadeIn(1000).effect("highlight",{color:"#ff0000"},3000);
		}
	}
	
	function draw()
	{
		$("#discard").button("disable");
		$("#draw").button("disable");
		$("#standPat").button("enable");
		$("#submitBet").button("enable");
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
		}).done(function(){
			$("#call").button("enable");
		}).fail(function(){
		
		}).always(function(){
		
		});
	}
	
	function playAudio(source)
	{
		if(!source){
			source = 'audio/ding.mp3';
		}
		//var audioElement = document.createElement('audio');
		audioElement.setAttribute('src', source);
		//audioElement.setAttribute('autoplay', 'autoplay');
		//audioElement.load()
		$.get();
		audioElement.addEventListener("load", function() {
			audioElement.volume = 0.25;
			//audioElement.play();
		}, true);
		return audioElement;
		
	}
	
	function submitBet()
	{
		betVal = $('#bet').val();
		if(parseInt(betVal) === 0)
		{
			playAudio();
			$('#bet').fadeOut(1000).fadeIn(1000).effect("highlight",{color:"#ff0000"},3000);
			$('#status').html('You must Bet at least 1 coin');
			$('#status').addClass('error');
			return false;
		}
		$('#status').html('');
		$('#status').removeClass('error');
		$("#standPat").button("disable");
		
		$.get('cards.php/placeBet?bet='+betVal, function(data) {
			var betData = jQuery.parseJSON(data);
			_message = betData.status_message;
			_data = betData.data;
			if( _data){
				if(_data.fail)
				{
					$('#status').html(_message);
					playAudio();
				}
				else
				{
					$('#playerCash').text(_data.PlayerCash['Player'][0]['Cash']);
					$('#winpool').text("Win Pool: "+_data.winPool);
					for(x = 1; x < _data.PlayerCash['Player'].length; x++)
					{
						$('#sPlayer'+(x+1)).html('Player'+(x+1)+' Status: Bank: '+_data.PlayerCash['Player'][x]['Cash']);
					}
				}
			} else {

				_message = betData.status_message;
				$('#statusMessage').html(_message).removeClass('hidden');
			}
		}).fail(function(data){
			var betData = jQuery.parseJSON(data);
			_message = betData.status_message;
			_data = betData.data;

		}).always(function(){
			$('#status').html(_message);
			$("#discard").button("enable");
			$("#call").button("enable");
			$('#bet').val(0);
			$("#submitBet").button("disable");
		});
	}
	
	function call()
	{
		// Rodney write for final win or loss is when other player have no more coin(win) or player has no coin(loss)
		
		$("#standPat").button("disable");
		$("#discard").button("disable");
		$("#draw").button("disable");
		$("#call").button("disable");
		$("#nextRound").button("enable");
		if( $('#callBind').text() == "disabled")
		{
			return false;
		}
		
		$('#submitBet').unbind("click");
		$('#submitBet').attr('disabled');
		$('#submitBet').addClass('ui-state-disabled');
		
		vhtml='';
		$.get( "cards.php/call", function( data ) {
					
			var callData = jQuery.parseJSON(data);
			_data = callData.data;
			_message = callData.status_message;
			if(_data)
			{
				playerCount = _data.playerCount;
				hands = _data.hands;
				ranks = _data.ranks;
				winnerPool = _data.winnerPool;
				$('#winpool').text("Win Pool: 0");
				$('#callBind').text('disabled'); 
				for(x=0; x < playerCount; x ++){
					if(_data.winner == x){
						winner = true;//'You are the winner!';
					} else {
						winner = false;//'';
					}
					 vhtml += buildCall(hands[x],ranks[x], x, winner, winnerPool);
				}
				_width  = window.innerWidth;
				_height = window.innerHeight;
				$('<div/>').dialog({
					modal:true,
					title:"Call - Declare Winner",
					open: function(){
						$(this).html(vhtml);
					},
					width:_width*.60,
					height: _height*.92,
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
			} else {
				$('#status').html(_message);
			}
		}).done(function(){
		}).fail(function(){
		}).always(function(){
			//  if player cash is zero then game over set all buttons to disable except newGame and help.
			//		add game over loose animation
			//  if all npc are out of cash then game over apply all funds to player set all buttons to disable except newGame and help.
			//  	add game over win animation.
			//$jsonObj['playerBankStatus'] = $playerBankStatus;
			//$jsonObj['nonPlayerBankStatus'] = $npcBankStatus;
			if( _data.playerBankStatus == false)
			{
				playLooseAnimation();
			}
			else if( _data.npcBankStatus == false)
			{
				playWinAnimation();
			}
			
		});
		$('#nextRoundBind').text('');
	}
	
	function playLooseAnimation()
	{
		playAudio();
		alert('Loose');	
	}
	
	function playWinAnimation()
	{
		playAudio();
		x=0;	
		playAudios = setInterval(function(){
			playAudio();
			x ++;
			if( x >= 2)
			{
				clearInterval(playAudios);
			}
		},2000);
	//	alert('Win');
	}
	function flipCardsBack(v)
	{
		for(x = 0; x < 5; x++)
		{
			$('#card'+x).html('');
			$('#card'+x).removeClass('card-front').addClass('card-back');
			v++;
			if(v >= 5){
				return v;
			}
		}
	}
	
	function nextRound()
	{	
		betVal = $('#bet').val();
		if(parseInt(betVal) === 0)
		{
			playAudio();
			$('#bet').fadeOut(1000).fadeIn(1000).effect("highlight",{color:"#ff0000"},3000);
			$('#status').html('You must Ante at least 1 coin');
			$('#status').addClass('error');
			return false
		}
		$('#status').html('');
		$('#status').removeClass('error');
		
		$("#standPat").button("enable");
		$("#discard").button("disable");
		$("#draw").button("disable");
		$("#call").button("disable");
		$("#nextRound").button("disable");
		$("#submitBet").button("enable");
		$("#submitBet").click(function(){
			submitBet();
		});
		$.get( "cards.php/repack", function( data ) {
			var cardData = jQuery.parseJSON(data);
			_message = cardData.status_message;
			$('#status').html(_message);
		}).done( function(){
			shuffle();
		}).fail(function(){
		
		}).always(function(){
			$('#callBind').text("");
			$('#submitBet').removeAttr('disabled');
			$('#submitBet').removeClass('ui-state-disabled');
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
			$('#handCount').text("Hand Count: "+_data.handCount);
			$('#handRank').text("Hand Rank: "+_data.handRank);
			$('#winpool').text("Win Pool: "+_data.winPool);
			playerCount = _data.playerCount;
			
			$('#betLable').text('Bet: ');
//console.log(_data.playerCash);			
			$('#playerCash').text(_data.playerCash[0]);
			for(x = 1; x < playerCount; x++)
			{
				$('#sPlayer'+(x+1)).html('Player'+(x+1)+' Status: Bank: '+_data.playerCash[x]);
			}
			flipCardsBack(0);
			x = 0;
			buildCardAnimation = setInterval(function(){
				//$('.card').removeClass('card-back').addClass('card-front');
				
				//$('#card'+x).removeClass('card-back').slideUp(200).addClass('card-front').slideDown(200);
				$('#card'+x).removeClass('card-back').addClass('card-front');
				$.when(oneCard(_data, x)).done(function(x){
					playAudio('audio/cardPlace1.mp3');
				});
				x++;
				if(x >= 5){
				clearInterval(buildCardAnimation);
				}
			}, 1000);
			//buildCard (_data);
			
		}
		}).fail(function(){
		
		}).always(function(){
/* console.log('deal always'); */
			/* $('#submitBet').prop('disabled', false); */// callBind method when you fix this
			$('#status').html(_message);
			$('#bet').val(0);
			
		});
		
		
		
		
	}

	function buildAnimation(_data)
	{
		// add loading bar.
						
/* 			var v = 0;
			$.when(flipCardsBack(v)).done(function(v){
			if(v == 5)
			{
			

			else
				console.log(v); // this should not happen but I will leave it now in case I need to debug later.
			}); */
			//flipCardsBack(0);
/* 			source = 'audio/cardShuffle.mp3';
			$.when(playAudio(source)).done(function (audioElement){
				audioElement.addEventListener('ended',function()
				{
console.log(audioElement.ended);
				}, false);
			}).done(function(_data){
			
			}); */
	}

	
	function buildCall (hand, rank,x,winner,winnerPool)
	{
/* console.log('winner='+winner); */		
		player = 'player'+x;
		xPlayer = 'Player'+(parseInt(x)+1);
		if(winner)
		{
			if(x === 0)
			{
				$('#playerCash').html(winnerPool);
			}
			else
			{
				$('#sPlayer'+(x+1)).html('Player'+(x+1)+' Status: Bank: '+winnerPool);
			}
		}
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
	
	function oneCard(_data, x)
	{
		
		/* $.when(playAudio('audio/cardPlace1.mp3')).done(function(audioElement){
					audioElement.addEventListener('ended',function()
					{
						oneCard(_data, x);
					}, false);
				}); */
		
		var cardKey   = _data.hand[x][0];
		var cardSuite = _data.hand[x][1];
		var cardValue = _data.hand[x][2];
		cardColor = cardColorFunc(cardSuite);
		cardSuite = '&'+cardSuite;
		$('#card'+x).html(
			'<label for="cardId'+x+'" id="labelId'+x+'">'+
			'<div class="suiteLeft '+cardColor+'">'+cardSuite+'</div>'+
			'<div class="suiteCenter '+cardColor+'">'+cardValue+'</div>'+
			'<div class="suiteRight '+cardColor+'">'+cardSuite+'</div>'+
			'</label>'+
			'<input type="checkbox" name="cardId'+x+'" id="cardId'+x+'" class="cardId" value="'+cardKey+'" />'
		);
		setClick();
		return true;
	}
	
	function buildCard (_data)
	{
//console.log(_data);
		for ( x = 0; x < _data.hand.length; x++){
			$.when(	oneCard(_data,x)).promise().done(function(){playAudio('audio/cardPlace1.mp3');});
		}
		
	}
	
	function setClick()
	{
		$("article div").on('click', function(e){
			var isChecked = $('#'+ this.id +' input:checkbox' ).is(':checked');
			if(isChecked){
				$('#'+this.id).addClass('cardLiHi');
			} else {
				$('#'+this.id).removeClass('cardLiHi');
			}
		});
	}
	
	function help()
	{
		var _html ="";
			_html +="<article id='helpText' >"; // class='help'
			_html +="	<h2 class='center'>Basic Rules</h2>";
			_html +="	<ul>";
			_html +="		<li><h2>Ante</h2>Enter 1  or more to Ante</li>";
			_html +="		<li><h2>NewGame</h2>Press New Game to To Start/Restart a game.</li>";
			_html +="		<li><h2>Bet</h2>Enter 1 or more to Bet<ul><li>Click Place Bet</li><li>OR</li><li>Click Stand Pat</li></ul></li>";
			_html +="		<li><h2>Stand Pat(optional)</h2>Enter 1 or more to Bet then click Stand Pat</li>";
			_html +="		<li>";
			_html +="			<h2>Discard</h2>";
			_html +="			Click a card to mark it for discard.";
			_html +="			<ul>";
			_html +="				<li>You must select at least one card.</li>";
			_html +="				<li>You must keep at least one Card.</li>";
			_html +="				<li>Click the card again to un-mark it.</li>";
			_html +="			</ul>";
			_html +="			Click Discard to remove selected cards. (Highlighted Cards will be removed).";
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