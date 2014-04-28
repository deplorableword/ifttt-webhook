<?php
error_reporting(-1);
ini_set('display_errors',1);
$request_body = file_get_contents('php://input');

$xml = simplexml_load_string($request_body);

switch($xml->methodName)
{

	//wordpress blog verification
	case 'mt.supportedMethods':
		success('metaWeblog.getRecentPosts');
		break;
	//first authentication request from ifttt
	case 'metaWeblog.getRecentPosts':
		//send a blank blog response
		//this also makes sure that the channel is never triggered
		success('<array><data></data></array>');
		break;

	case 'metaWeblog.newPost':
		//@see http://codex.wordpress.org/XML-RPC_WordPress_API/Posts#wp.newPost
		$obj = new stdClass;
		//get the parameters from xml
		$obj->direct_print_code = (string)$xml->params->param[1]->value->string;
		$obj->password = (string)$xml->params->param[2]->value->string;

		//@see content in the wordpress docs
		$content = $xml->params->param[3]->value->struct->member;
		foreach($content as $data)
		{
			switch((string)$data->name)
			{	
				case 'title':
					$obj->instagram_url = (string)$data->value->string;
					break;
					
				case 'description':
					$obj->caption = (string)$data->value->string;
					break;
					
				//neglect these sections of the request
				case 'post_status' ://publish status
				case 'mt_keywords': //tags
					break;

				//the passed categories are parsed into an array
				case 'categories':
					$categories=array();
					foreach($data->xpath('value/array/data/value/string') as $cat)
						array_push($categories,(string)$cat);
					$obj->categories = $categories;
					break;
					
				// misc
				default:
					$obj->{$data->name} = (string)$data->value->string;
			}
		}

		//Make the webrequest
		include('requests/Requests.php');
		Requests::register_autoloader();

		$html = 'html=<html><head><meta charset="utf-8"></head><body style="text-align:center">
			<h1 style="font-family:Cabin">if<em>#lp</em>then<em>print</em></h1>
			<img class="dither" width="380px" src="'.$obj->instagram_url.'"/>';
			if (!empty($obj->caption)) {
				$html .='<p>'.$obj->caption.'</p>';
			}
		$html .='</body></html>';
		
		$url = 'http://remote.bergcloud.com/playground/direct_print/'.$obj->direct_print_code;
					
		$response = Requests::post($url, null, $html);

		if($response->success)
			success('<string>'.$response->status_code.'</string>');
		else
			failure($response->status_code);
		}

/** Copied from wordpress */

function success($innerXML)
{
	$xml =  <<<EOD
<?xml version="1.0"?>
<methodResponse>
  <params>
    <param>
      <value>
      $innerXML
      </value>
    </param>
  </params>
</methodResponse>

EOD;
	output($xml);
}

function output($xml){
	$length = strlen($xml);
	header('Connection: close');
	header('Content-Length: '.$length);
	header('Content-Type: text/xml');
	header('Date: '.date('r'));
	echo $xml;
	exit;
}

function failure($status){
$xml= <<<EOD
<?xml version="1.0"?>
<methodResponse>
  <fault>
    <value>
      <struct>
        <member>
          <name>faultCode</name>
          <value><int>$status</int></value>
        </member>
        <member>
          <name>faultString</name>
          <value><string>Request was not successful.</string></value>
        </member>
      </struct>
    </value>
  </fault>
</methodResponse>

EOD;
output($xml);
}

/** Used from drupal */
function valid_url($url, $absolute = FALSE) {
  if ($absolute) {
    return (bool) preg_match("
      /^                                                      # Start at the beginning of the text
      (?:https?):\/\/                                # Look for ftp, http, https or feed schemes
      (?:                                                     # Userinfo (optional) which is typically
        (?:(?:[\w\.\-\+!$&'\(\)*\+,;=]|%[0-9a-f]{2})+:)*      # a username or a username and password
        (?:[\w\.\-\+%!$&'\(\)*\+,;=]|%[0-9a-f]{2})+@          # combination
      )?
      (?:
        (?:[a-z0-9\-\.]|%[0-9a-f]{2})+                        # A domain name or a IPv4 address
        |(?:\[(?:[0-9a-f]{0,4}:)*(?:[0-9a-f]{0,4})\])         # or a well formed IPv6 address
      )
      (?::[0-9]+)?                                            # Server port number (optional)
      (?:[\/|\?]
        (?:[\w#!:\.\?\+=&@$'~*,;\/\(\)\[\]\-]|%[0-9a-f]{2})   # The path and query (optional)
      *)?
    $/xi", $url);
  }
  else {
    return (bool) preg_match("/^(?:[\w#!:\.\?\+=&@$'~*,;\/\(\)\[\]\-]|%[0-9a-f]{2})+$/i", $url);
  }
}
