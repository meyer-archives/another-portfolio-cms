<?php

/*

php-typography
Modified into awesomeness by:

Michael Meyer
mikemeyer@gmail.com
twitter.com/mikemeyer

Code copyright is annoying. Do with this what you will. Tell me about it if you want. Just don't blame me for your illegitimate child.

Old, really verbose copyright notice:

	Copyright (c) 2007, Hamish Macpherson. All rights reserved.

	Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
		* Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
		* Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
		* Neither the name of the php-typogrify nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.
	THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

*/

require("smartypants.php");
define( "RANDOM_STRING", md5(time()) );
define( "LT", "LESSTHANSYMBOL" . RANDOM_STRING );
define( "GT", "GREATERTHANSYMBOL" . RANDOM_STRING );
define( "DQ", "DOUBLEQUOTESYMBOL" . RANDOM_STRING );

function amp( $text ){
	$amp_finder = "/(\s|&nbsp;)(&|&amp;|&\#38;|&#038;)(\s|&nbsp;)/";
	return preg_replace($amp_finder, '\\1'.LT.'span class='.DQ.'amp'.DQ.GT.'&amp;'.LT.'/span'.GT.'\\3', $text);
}

function dash( $text ){
	$dash_finder = "/(\s|&nbsp;|&thinsp;)*(&mdash;|&ndash;|&#x2013;|&#8211;|&#x2014;|&#8212;)(\s|&nbsp;|&thinsp;)*/";
	return preg_replace($dash_finder, '&thinsp;\\2&thinsp;', $text);
}

function _cap_wrapper( $matchobj ){
	if ( !empty($matchobj[2]) ){
		return sprintf(''.LT.'span class='.DQ.'caps'.DQ.GT.'%s'.LT.'/span'.GT.'', $matchobj[2]);
	} else {
		$mthree = $matchobj[3];
		if ( ($mthree{strlen($mthree)-1}) == " " ){
			$caps = substr($mthree, 0, -1);
			$tail = ' ';
		} else {
			$caps = $mthree;
			$tail = '';
		}			 
		return sprintf(''.LT.'span class='.DQ.'caps'.DQ.GT.'%s'.LT.'/span'.GT.'%s', $caps, $tail);
	}
}

function caps( $text ){
	// Tokenize from Smartypants
	$tokens = _TokenizeHTML($text);	   
	$result = array();
	$in_skipped_tag = false;

	$cap_finder = "/(
			(\b[A-Z\d]*		   # Group 2: Any amount of caps and digits
			[A-Z]\d*[A-Z]	   # A cap string much at least include two caps (but they can have digits between them)
			[A-Z\d]*\b)		   # Any amount of caps and digits
			| (\b[A-Z]+\.\s?   # OR: Group 3: Some caps, followed by a '.' and an optional space
			(?:[A-Z]+\.\s?)+)  # Followed by the same thing at least once more
			(?:\s|\b|$))/x";

	$tags_to_skip_regex = "/<(\/)?(?:pre|code|kbd|script|math)[^>]*>/i";

	foreach ($tokens as $token){
		if ( $token[0] == "tag" ){
			// Don't mess with tags.
			$result[] = $token[1];
			$close_match = preg_match($tags_to_skip_regex, $token[1]);			  
			if ( $close_match ){
				$in_skipped_tag = true;
			} else {
				$in_skipped_tag = false;
			}
		} else {
			if ( $in_skipped_tag ){
				$result[] = $token[1];
			} else {
				$result[] = preg_replace_callback($cap_finder, "_cap_wrapper", $token[1]);
			}
		}
	}		 
	return join("", $result);	 
}

function _quote_wrapper( $matchobj ){
	if ( !empty($matchobj[7]) ){
		$classname = "dquo";
		$quote = $matchobj[7];
	} else {
		$classname = "quo";
		$quote = $matchobj[8];
	}
	return sprintf('%s'.LT.'span class='.DQ.'%s'.DQ.GT.'%s'.LT.'/span'.GT, $matchobj[1], $classname, $quote);
}

function initial_quotes( $text ){
	$quote_finder = "/((<(p|h[1-6]|li)[^>]*>|^)
		\s*
		(<(a|em|span|strong|i|b)[^>]*>\s*)*)
		((\"|&ldquo;|&\#8220;)|('|&lsquo;|&\#8216;))
		/ix";

	return preg_replace_callback($quote_finder, "_quote_wrapper", $text);
}

function widont( $text ){
	// This regex is a beast, tread lightly
	$widont_finder = "/([^\s])\s+(((<(a|span|i|b|em|strong|acronym|caps|sub|sup|abbr|big|small|code|cite|tt)[^>]*>)*\s*[^\s<>]+)(<\/(a|span|i|b|em|strong|acronym|caps|sub|sup|abbr|big|small|code|cite|tt)>)*[^\s<>]*\s*(<\/(p|h[1-6]|li)>|$))/i";
	return preg_replace($widont_finder, '$1&nbsp;$2', $text);
}

function typogrify( $text, $do_guillemets = false ){
	// Escape all unsafe HTML entities
	$text = amp( $text );
	$text = widont( $text );
	$text = SmartyPants( $text );
	$text = caps( $text );
	$text = initial_quotes( $text );
	$text = dash( $text );

	$text = htmlspecialchars( $text, ENT_QUOTES, "UTF-8", false );
	$text = str_replace( array( LT, GT, DQ ), array( "<", ">", '"' ), $text );

	return $text;
}

?>