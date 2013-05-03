<?php
/*********************************************************************************\
| Code by: Janis Blaus (Glorificus @ http://www.suncore.lv/)                      |
\*********************************************************************************/

class curl
{
	public $session = false;
	public $cookies = array();
	public $url;
	public $result;
	public $uagent = 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.13 (KHTML, like Gecko) Chrome/24.0.1284.2 Safari/537.13';
	public $initalized = false;
	
	function __construct( $params = false )
	{
		$this->header = true;
		
		if( isset( $params ) AND !empty( $params ) )
		{
			foreach( $params as $key => $val )
			{
				$this->{$key} = $val;
			}
			
			unset( $params );
		}
	}
	
	function init( $url = '')
	{
		if ( $this->initalized )
		{
			curl_close( $this->session );
			
			$this->initalized = false;
		}
		else
		{
			$this->initalized = true;
		}

		if( empty( $url ) AND !empty( $this->url ) )
		{
			$url = $this->url;
		}
		else
		{
			$this->url = $url;
		}
	
		$this->session = curl_init( $url );
		
		if( isset( $this->referer ) AND !empty( $this->referer ) )
		{
			curl_setopt( $this->session, CURLOPT_REFERER, $this->referer );
		}
		
		curl_setopt( $this->session, CURLOPT_USERAGENT, $this->uagent );
		curl_setopt( $this->session, CURLOPT_HEADER,$this->header );
		curl_setopt( $this->session, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $this->session, CURLOPT_SSL_VERIFYPEER, false);

		if( !empty( $this->cookies ) )
		{
			$this->cookies( $this->cookies );
		}
	}
	
	function post( $array )
	{
		if( !$this->initalized )
		{
			$this->init();
		}
		
		curl_setopt( $this->session, CURLOPT_POST, true );
		curl_setopt( $this->session, CURLOPT_POSTFIELDS, http_build_query( $array ) );
		
		return $this;
	}
	
	function get_cookies()
	{
		preg_match_all( '/^Set-Cookie: (.*?);/m', $this->result, $find_cookies );
        
		if( !empty( $find_cookies ) )
		{
			$find_cookies = str_replace( array( 'Set-Cookie:',';',' ' ), '', $find_cookies[ 0 ] );
		   
			foreach( $find_cookies as $cookie )
			{
				$cookie = explode( '=', $cookie );
				$this->cookies[ $cookie[ 0 ] ] = $cookie[ 1 ];
			}
		}
		
		return $this;
	
	}
	
	function cookies( $array )
	{
		$this->cookies = array_merge( $array, $this->cookies );
		
		curl_setopt( $this->session, CURLOPT_COOKIE, $this->queryzer( $this->cookies ) );
		
		return $this;
	}
	
	function queryzer( $array )
	{
		$query = '';
		
		foreach(  $array as $key => $value )
		{
			$query .= $key . '=' . $value . '; ';
		}

		return $query;
	}
	function change( $url, $follow = 'yes' )
	{
		$this->url = $url;
		return $this;
	}
	function exec( $follow = 'yes' )
	{
		if( !$this->initalized )
		{
			$this->init();
		}
		
		curl_setopt( $this->session, CURLOPT_FOLLOWLOCATION, ( $follow == 'yes' ? true : false ) );
		
		$this->result = curl_exec( $this->session );
		
		$this->get_cookies();
		
		$this->initalized = false;
		
		curl_close( $this->session );
		
		return $this->result;
	}
	
	function preg($start, $end, $context, $error = '', $modifiers = '')
	{
		$v = '/' . preg_quote($start, '/') . '(.+?)' . preg_quote($end, '/') . '/' . $modifiers;
		preg_match_all($v, $context, $matches);

		if (empty($error) === false && (isset($matches[1][0]) === false || empty($matches[1][0]) === true))
		{
			echo $v . ' - ';
			echo $error;
		}

		return $matches;

	}
	function get_inputs( $data_s )
	{
		preg_match_all('/<(input|button)([^>]*)>/is', $data_s, $inputi);
		$data = array();

		foreach( $inputi[2] as $key => $val )
		{
			preg_match( '/value="([^"]*)"/is', $val, $value );
			preg_match( '/name="([^"]*)"/is', $val, $name );
			
			if( isset( $name[ 1 ] ) )
			{
				if( !isset( $value[ 1 ] ) )
				{
					$value[ 1 ] = '';
				}
				
				$data[ $name[ 1 ] ] = $value[ 1 ];
        
				if( ( preg_match('/checkbox/is', $val) AND !preg_match('/checked/', $val)) OR (preg_match('/radio/is', $val) AND !preg_match('/checked/', $val) ) )
				{
					unset( $data[ $name[ 1 ] ] );
				}
			}
		}
    
		preg_match_all('/<textarea([^>]+)name="([^"]*)"([^>]+)>((?:(?!<\/textarea>).)+)<\/textarea>/is', $data_s, $texts);
    
		foreach( $texts[ 4 ] as $key => $val )
		{
			$data[$texts[2][$key]] = $val;
		}
    
		return $data;
	}
}