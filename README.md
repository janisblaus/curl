curl
====

Best PHP cURL wrapper

include 'curl.php';

/*
Initialize class with some default configuration values
*/
$curl = new curl(array(
  'referer' => 'http://www.domain.com/',
	'url'     => 'http://www.domain.com/form.php',
	'uagent'  => 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.13 (KHTML, like Gecko) Chrome/24.0.1284.2 Safari/537.13',
	
	/* for auth if needed, it makes some things easier */
	'cookies' => array( 
		'PHPSESSID' => 'd9fac2kskv9v7q1t0mi1ibbcl1',
	)
));

/*
Return current remote page
*/
echo $curl->exec();


/*
Change uri to different location and return result
*/
echo $curl->change('http://www.domain.com/page/1/')->exec();

/*
Post some data to form and return result
*/
$fields = array(
	'field_name_1' => 'Foo',
	'field_name_2' => 'Bar',
	'field_name_3' => 1337
);

echo $curl->change('http://www.domain.com/form.php')->post( $fields )->exec();
