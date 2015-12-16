<?php

// get status and reply url
$status = $_POST['status'];
$reply = $_POST['reply'];
$display = $_POST['display'];

if( $display === 'true' ) {
	$displayVal = '1';
} else {
	$displayVal = '0';
}

$status = stripslashes($status);

// if note is a reply, get handle and ID
if( $reply !== '' ) {
	if( preg_match("|https?://(www\.)?twitter\.com/(#!/)?@?([^/]*)|", $reply, $matches) ) {
		$replyHandle = $matches[3];
		$replyLength = strlen( $replyHandle ) + 2;
		$status .= ' @' . $replyHandle;
	}
	if( preg_match('/\/(\d+)$/', $reply, $matches) ) {
	  $replyID = $matches[1];
	}
}

session_start();
require_once("twitteroauth.php");

//twitter handle and auth keys
$twitteruser = "babydo0m";
$consumerkey = "uz7610zmJf3FS2GSfj0CdeloA";
$consumersecret = "4BbbaR6YBBXYmlPt5iTgXrnfg2eYOdnDV1qKybkPTJrBOKFYjG";
$accesstoken = "2149567442-dhEw5XDoYgS1V6jwwCehfqPY0w7BBhOcmbcBYvh";
$accesstokensecret = "UzP8iDrGHHvzm9gxpzUGnfllJsQwjm7CxNy6KwUIgjjpv";


// test keys
/*$twitteruser = "babydooom";
$consumerkey = "QFRmbqYVqF4QYyuTSSbu8PnCZ";
$consumersecret = "wqUwzVD8yXEzfPJBdJhnXuGpfsK2Mn7GPuMdKJPKUpBjfd9eA3";
$accesstoken = "2575677535-yNj6ONAiUlFHHtmqvPxJak9gAVQXjHuXaAs8zFv";
$accesstokensecret = "mc8nB0ZfD5l18Vr2tymzpEwQs2f5xEcHJJDNPBp4PxMwz";*/

// get connection 
function getConnectionWithAccessToken($cons_key, $cons_secret, $oauth_token, $oauth_token_secret) {
  $connection = new TwitterOAuth($cons_key, $cons_secret, $oauth_token, $oauth_token_secret);
  return $connection;
}
$connection = getConnectionWithAccessToken($consumerkey, $consumersecret, $accesstoken, $accesstokensecret);

if( $reply === '' ) {
	$tweets = $connection->post("https://api.twitter.com/1.1/statuses/update.json?status=" . rawurlencode($status));
} else {
	$tweets = $connection->post("https://api.twitter.com/1.1/statuses/update.json?status=" . rawurlencode($status) . "&in_reply_to_status_id=" . $replyID);
}

$tweetfeed = json_encode( $tweets );
$tweet = json_decode( $tweetfeed, true );

// check if status is a duplicate
if( !$tweet['errors'] ) {
	// set variables
	$createdAt = $tweet['created_at'];
	$tweetID = $tweet['id_str'];
	$text = $tweet['text'];
	$text = addslashes($text);
	$inReplyToStatusID = $tweet['in_reply_to_status_id'];
	$inReplyToUserID = $tweet['in_reply_to_user_id'];
	$inReplyToScreenName = $tweet['in_reply_to_screen_name'];

	$userID = $tweet['user']['id'];
	$userName = $tweet['user']['name'];
	$userScreenName = $tweet['user']['screen_name'];

	$permalink = '<a href="http://twitter.com/babydo0m/status/' . $tweetID . '">view</a>';
	$twitterlink = '<a href="http://twitter.com/babydo0m/status/' . $tweetID . '">view on twitter</a>';
	$datetime = new DateTime( $createdAt );
	$datetime->setTimezone(new DateTimeZone('America/Chicago'));
	//$datelink = '<a href="notes/' . $tweetID . '">' . $datetime->format('Y-m-d H:i') . '</a>';
	$datelink = '<a href="http://twitter.com/babydo0m/status/' . $tweetID . '">' . $datetime->format('H:i | Y-m-d') . '</a>';
	$replylink = '<a href="https://twitter.com/intent/tweet?in_reply_to=' . $tweetID . '">reply</a>';
	$retweetlink = '<a href="https://twitter.com/intent/retweet?tweet_id="' . $tweetID . '">retweet</a>';
	$favlink = '<a href="https://twitter.com/intent/favorite?tweet_id="' . $tweetID . '">favorite</a>';

	$permalink = '<a href="http://twitter.com/babydo0m/status/' . $tweetID . '">permalink</a>';

	// set up mysql connection
	$host = "68.178.143.68";
	$username = "clrnlltweets";
	$password = "Marcie330!";
	$db = new mysqli($host, $username, $password);
	$db->select_db( 'clrnlltweets' );

	// check connection
	if( $db->connect_error ) {
	  die("db connection failed: " . $db->connect_error);
	}
	//echo '<small class="block">db connection successful</small>';

	$insert = "INSERT INTO tweets (created_at, tweet_id, tweet_text, display, in_reply_to_status_id, in_reply_to_user_id, in_reply_to_screen_name, user_id, user_name, user_screen_name) VALUES ('$createdAt', '$tweetID', '$text', '$displayVal', '$inReplyToStatusID', '$inReplyToUserID', '$inReplyToScreenName', '$userID', '$userName', '$userScreenName')";
	if( $db->query($insert) === TRUE ) { 
		if( $displayVal === '0' ) { ?>
			<p data-id="<?php echo $tweetID; ?>">
				<b class="color-01">note was posted on twitter (hidden from site):</b>
				<br><br>
				<?php echo $text; ?>
				<small class="block">
					<?php echo $twitterlink; ?>
					&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;
					<a href="#">delete</a>
				</small>
			</p>
			<?php if ( $reply !== '' ) {
				echo '<small class="block">reply to &darr;</small>';
			}
		} else { ?>
	 		<p data-id="<?php echo $tweetID; ?>">
  			<?php echo $text; ?>
  			<br>
  			<small class="block">
  				<?php echo $datelink; ?>
  			</small>
  		</p>
			<?php if ( $reply !== '' ) {
				echo '<small class="block">reply to &darr;</small>';
			}
		}
	} else {
	  echo "<p>error: " . $insert . "<br>" . $db->error . "</p>";
	}
}

//$db->close();*/

?>