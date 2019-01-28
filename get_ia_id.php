<?php

/* For existing uploads get IA identifier so we can store it locally */


require_once(dirname(__FILE__) . '/lib.php');
require_once(dirname(__FILE__) . '/ris.php');

//----------------------------------------------------------------------------------------
function get_pdf_filename($pdf)
{
	$filename = '';
	

	// if no name use basename
	if ($filename == '')
	{
		$filename = basename($pdf);
		$filename = str_replace('lognavi?name=nels&lang=en&type=pdf&id=', '', $filename);
	}
		
	echo "filename=$filename\n";
	
	return $filename;
}


//----------------------------------------------------------------------------------------
// Import reference and create identifier based on DOI
function archive_import($reference)
{
	global $force;
	global $config;


	print_r($reference);
	
	$id = '';
	
	if (isset($reference->doi))
	{
		$id = $reference->doi;
		$id = preg_replace('/[\.\/]/', '-', $id);
		$id = 'doi-' . $id;
		
		echo $id . "\n";
		
		if (isset($reference->pdf))
		{
			// fetch PDF
			$cache_dir =  dirname(__FILE__) . "/cache/";
			$article_pdf_filename = $cache_dir . '/' . get_pdf_filename($reference->pdf);
			
			if (file_exists($article_pdf_filename) && !$force)
			{
				echo "Have PDF $article_pdf_filename\n";
			}
			else
			{				
				$command = "curl --location " . $reference->pdf . " > " . $article_pdf_filename;
				echo $command . "\n";
				system ($command);
			}	
			
			// Upload	
			$identifier = $id;					

			// upload to IA
			$headers = array();
	
			$headers[] = '"x-archive-auto-make-bucket:1"';
			$headers[] = '"x-archive-ignore-preexisting-bucket:1"';
			$headers[] = '"x-archive-interactive-priority:1"';
	
			// collection
			$headers[] = '"x-archive-meta01-collection:taxonomyarchive"';

			// metadata
			$headers[] = '"x-archive-meta-mediatype:texts"'; 
				
			if (isset($reference->title))
			{
				$headers[] = '"x-archive-meta-title:' . addcslashes($reference->title, '"') . '"';
			}
			if (isset($reference->secondary_title))
			{
				if (isset($reference->secondary_title))
				{
					$headers[] = '"x-archive-meta-journaltitle:' . addcslashes($reference->secondary_title, '"') . '"';
				}
				if (isset($reference->volume))
				{
					$headers[] = '"x-archive-meta-volume:' . addcslashes($reference->volume, '"') . '"';
				}
				
				$pages = '';
				if (isset($reference->spage))
				{
					$pages = $reference->spage;
				}
				if (isset($reference->epage))
				{
					$pages .= '-' . $reference->epage;
				}
				
				if ($pages != '')
				{
					$headers[] = '"x-archive-meta-pages:' . addcslashes($pages, '"') . '"';
				}
			}
			if (isset($reference->year))
			{
				$headers[] = '"x-archive-meta-year:' . addcslashes($reference->year, '"') . '"';
				$headers[] = '"x-archive-meta-date:' . addcslashes($reference->year, '"') . '"';
			}

			if (isset($reference->authors))
			{
				for ($i = 0; $i < count($reference->authors); $i++)
				{
					$headers[] = '"x-archive-meta' . str_pad(($i+1), 2, 0, STR_PAD_LEFT) . '-creator:' . addcslashes($reference->authors[$i], '"') . '"';
				}
			}
			
			if (isset($reference->doi))
			{
				$headers[] = '"x-archive-meta-external-identifier:' . 'doi:' . $reference->doi . '"';
			}
										
			// licensing
			//$headers[] = '"x-archive-meta-licenseurl:http://creativecommons.org/licenses/by-nc/3.0/"';

			// authorisation
			$headers[] = '"authorization: LOW ' . $config['s3_access_key']. ':' . $config['s3_secret_key'] . '"';

			$headers[] = '"x-archive-meta-identifier:' . $identifier . '"';
	
			$url = 'http://s3.us.archive.org/' . $identifier . '/' . $identifier . '.pdf';
	
			print_r($headers);
			echo "$url\n";
			
			if (head($url) && !$force)
			{
				echo " PDF exists (HEAD returns 200)\n";
			}
			else
			{
				$command = 'curl --location';
				$command .= ' --header ' . join(' --header ', $headers);
				$command .= ' --upload-file ' . $article_pdf_filename;
				$command .= ' http://s3.us.archive.org/' . $identifier . '/' . $identifier . '.pdf';

				echo $command . "\n";
				
				system ($command);
				
			}
		}
	}
}


//----------------------------------------------------------------------------------------
function get_pdf_details($pdf)
{
	$obj = null;

	// Look up
	$url = 'http://bionames.org/bionames-archive/pdfstore?url=' . urlencode($pdf) . '&noredirect&format=json';
	//$url = 'http://direct.bionames.org/bionames-archive/pdfstore?url=' . urlencode($pdf) . '&noredirect&format=json';

	$opts = array(
	  CURLOPT_URL =>$url,
	  CURLOPT_FOLLOWLOCATION => TRUE,
	  CURLOPT_RETURNTRANSFER => TRUE
	);

	$ch = curl_init();
	curl_setopt_array($ch, $opts);
	$data = curl_exec($ch);
	$info = curl_getinfo($ch); 
	curl_close($ch);

	if ($data != '')
	{
		$obj = json_decode($data);
	}
		
	return $obj;
}

//----------------------------------------------------------------------------------------
// http://stackoverflow.com/questions/247678/how-does-mediawiki-compose-the-image-paths
function sha1_to_path_array($sha1)
{
	preg_match('/^(..)(..)(..)/', $sha1, $matches);
	
	$sha1_path = array();
	$sha1_path[] = $matches[1];
	$sha1_path[] = $matches[2];
	$sha1_path[] = $matches[3];

	return $sha1_path;
}

//----------------------------------------------------------------------------------------
// Return path for a sha1
function sha1_to_path_string($sha1)
{
	$sha1_path_parts = sha1_to_path_array($sha1);
	
	$sha1_path = '/' . join("/", $sha1_path_parts) . '/' . $sha1;

	return $sha1_path;
}

//----------------------------------------------------------------------------------------
// Import reference and create identifier based on SHA1 of PDF
function archive_sha1_import($reference)
{
	global $force;
	global $config;

	print_r($reference);
		
	if (isset($reference->pdf))
	{
		// Do we have this in BioNames?
		$obj = get_pdf_details($reference->pdf);
		
		if (isset($obj->sha1))
		{
			$sha1 = $obj->sha1;
			
			$id = $sha1; // Use SHA1 as identifier
			
			$cache_dir =  dirname(__FILE__) . "/cache";
			$article_pdf_filename = $cache_dir . '/' . $sha1 . '.pdf';
		
			$prefix = 'http://bionames.org/bionames-archive/pdf';
				
			$sha1 = $obj->sha1;

			$url = $prefix . sha1_to_path_string($sha1) . '/' . $sha1 . '.pdf';
						
			$triples[] = $pdf_id . ' <http://schema.org/fileFormat> "application/pdf" .';

			if (file_exists($article_pdf_filename) && !$force)
			{
				echo "Have PDF $article_pdf_filename\n";
			}
			else
			{				
				$command = "curl --location " . $reference->pdf . " > " . $article_pdf_filename;
				echo $command . "\n";
				system ($command);
			}	
			
			// Can we construct a nicer identifier?
			
			$terms = array();
			
			if (isset($reference->secondary_title))
			{
				if (isset($reference->secondary_title))
				{
					$journal = $reference->secondary_title;
					$journal = strtolower($journal);
					
					$commonWords = array('a', 'of', 'the');
					
					$journal = preg_replace('/\b('.implode('|',$commonWords).')\b/','',$journal);
					$journal = preg_replace('/^\s+/','',$journal);		
					$journal = preg_replace('/\s+$/','',$journal);		
					$journal = preg_replace('/\s+/','-',$journal);					

					$terms[] = $journal;
				}
				if (isset($reference->volume))
				{
					$terms[] = $reference->volume;
				}
				
				if (isset($reference->spage))
				{
					$terms[] = str_pad($reference->spage, 3, '0', STR_PAD_LEFT);
				}
				
				if (isset($reference->epage))
				{
					$terms[] = str_pad($reference->epage, 3, '0', STR_PAD_LEFT);
				}
				
				if (count($terms) >= 3)
				{
					$id = join('-', $terms);
				}
			}			

			
			// Upload	
			$identifier = $id;					

			// upload to IA
			$headers = array();
	
			$headers[] = '"x-archive-auto-make-bucket:1"';
			$headers[] = '"x-archive-ignore-preexisting-bucket:1"';
			$headers[] = '"x-archive-interactive-priority:1"';
	
			// collection
			$headers[] = '"x-archive-meta01-collection:taxonomyarchive"';

			// metadata
			$headers[] = '"x-archive-meta-mediatype:texts"'; 
				
			if (isset($reference->title))
			{
				$headers[] = '"x-archive-meta-title:' . addcslashes($reference->title, '"') . '"';
			}
			if (isset($reference->secondary_title))
			{
				if (isset($reference->secondary_title))
				{
					$headers[] = '"x-archive-meta-journaltitle:' . addcslashes($reference->secondary_title, '"') . '"';
				}
				if (isset($reference->volume))
				{
					$headers[] = '"x-archive-meta-volume:' . addcslashes($reference->volume, '"') . '"';
				}
				
				$pages = '';
				if (isset($reference->spage))
				{
					$pages = $reference->spage;
				}
				if (isset($reference->epage))
				{
					$pages .= '-' . $reference->epage;
				}
				
				if ($pages != '')
				{
					$headers[] = '"x-archive-meta-pages:' . addcslashes($pages, '"') . '"';
				}
			}
			if (isset($reference->year))
			{
				$headers[] = '"x-archive-meta-year:' . addcslashes($reference->year, '"') . '"';
				$headers[] = '"x-archive-meta-date:' . addcslashes($reference->year, '"') . '"';
			}

			if (isset($reference->authors))
			{
				for ($i = 0; $i < count($reference->authors); $i++)
				{
					$headers[] = '"x-archive-meta' . str_pad(($i+1), 2, 0, STR_PAD_LEFT) . '-creator:' . addcslashes($reference->authors[$i], '"') . '"';
				}
			}
			
			if (isset($reference->doi))
			{
				$headers[] = '"x-archive-meta-external-identifier:' . 'doi:' . $reference->doi . '"';
			}
										
			// licensing
			//$headers[] = '"x-archive-meta-licenseurl:http://creativecommons.org/licenses/by-nc/3.0/"';

			// authorisation
			$headers[] = '"authorization: LOW ' . $config['s3_access_key']. ':' . $config['s3_secret_key'] . '"';

			$headers[] = '"x-archive-meta-identifier:' . $identifier . '"';
	
			$url = 'http://s3.us.archive.org/' . $identifier . '/' . $identifier . '.pdf';
	
			print_r($headers);
			echo "$url\n";
			
			echo "Checking whether we have this already...";
			if (head($url) && !$force)
			{
				echo " PDF exists (HEAD returns 200)\n";
				
				
			}
			else
			{
				echo " uploading\n";
				
				$command = 'curl --location';
				$command .= ' --header ' . join(' --header ', $headers);
				$command .= ' --upload-file ' . $article_pdf_filename;
				$command .= ' http://s3.us.archive.org/' . $identifier . '/' . $identifier . '.pdf';

				echo $command . "\n";
				
				system ($command);
				
			}
		}
	}
}



//----------------------------------------------------------------------------------------
// Import reference and create identifier based on SHA1 of PDF
function check_archive_sha1_import($reference)
{
	global $force;
	global $config;

	//print_r($reference);
		
	if (isset($reference->pdf))
	{
		// Do we have this in BioNames?
		$obj = get_pdf_details($reference->pdf);
		
		if (isset($obj->sha1))
		{
			$sha1 = $obj->sha1;
			
			$id = $sha1; // Use SHA1 as identifier

			// Can we construct a nicer identifier?
			
			$terms = array();
			
			if (isset($reference->secondary_title))
			{
				if (isset($reference->secondary_title))
				{
					$journal = $reference->secondary_title;
					$journal = strtolower($journal);
					
					$commonWords = array('a', 'of', 'the');
					
					$journal = preg_replace('/\b('.implode('|',$commonWords).')\b/','',$journal);
					$journal = preg_replace('/^\s+/','',$journal);		
					$journal = preg_replace('/\s+$/','',$journal);		
					$journal = preg_replace('/\s+/','-',$journal);					

					$terms[] = $journal;
				}
				if (isset($reference->volume))
				{
					$terms[] = $reference->volume;
				}
				
				if (isset($reference->spage))
				{
					$terms[] = str_pad($reference->spage, 3, '0', STR_PAD_LEFT);
				}
				
				if (isset($reference->epage))
				{
					$terms[] = str_pad($reference->epage, 3, '0', STR_PAD_LEFT);
				}
				
				if (count($terms) >= 3)
				{
					$id = join('-', $terms);
				}
			}			

			
			// Upload	
			$identifier = $id;					

			// upload to IA
			$headers = array();
	
			$headers[] = '"x-archive-auto-make-bucket:1"';
			$headers[] = '"x-archive-ignore-preexisting-bucket:1"';
			$headers[] = '"x-archive-interactive-priority:1"';
	
			// collection
			$headers[] = '"x-archive-meta01-collection:taxonomyarchive"';

			// metadata
			$headers[] = '"x-archive-meta-mediatype:texts"'; 
				
			if (isset($reference->title))
			{
				$headers[] = '"x-archive-meta-title:' . addcslashes($reference->title, '"') . '"';
			}
			if (isset($reference->secondary_title))
			{
				if (isset($reference->secondary_title))
				{
					$headers[] = '"x-archive-meta-journaltitle:' . addcslashes($reference->secondary_title, '"') . '"';
				}
				if (isset($reference->volume))
				{
					$headers[] = '"x-archive-meta-volume:' . addcslashes($reference->volume, '"') . '"';
				}
				
				$pages = '';
				if (isset($reference->spage))
				{
					$pages = $reference->spage;
				}
				if (isset($reference->epage))
				{
					$pages .= '-' . $reference->epage;
				}
				
				if ($pages != '')
				{
					$headers[] = '"x-archive-meta-pages:' . addcslashes($pages, '"') . '"';
				}
			}
			if (isset($reference->year))
			{
				$headers[] = '"x-archive-meta-year:' . addcslashes($reference->year, '"') . '"';
				$headers[] = '"x-archive-meta-date:' . addcslashes($reference->year, '"') . '"';
			}

			if (isset($reference->authors))
			{
				for ($i = 0; $i < count($reference->authors); $i++)
				{
					$headers[] = '"x-archive-meta' . str_pad(($i+1), 2, 0, STR_PAD_LEFT) . '-creator:' . addcslashes($reference->authors[$i], '"') . '"';
				}
			}
			
			if (isset($reference->doi))
			{
				$headers[] = '"x-archive-meta-external-identifier:' . 'doi:' . $reference->doi . '"';
			}
										
			// licensing
			//$headers[] = '"x-archive-meta-licenseurl:http://creativecommons.org/licenses/by-nc/3.0/"';

			// authorisation
			$headers[] = '"authorization: LOW ' . $config['s3_access_key']. ':' . $config['s3_secret_key'] . '"';

			$headers[] = '"x-archive-meta-identifier:' . $identifier . '"';
	
			$url = 'http://s3.us.archive.org/' . $identifier . '/' . $identifier . '.pdf';
	
			//print_r($headers);
			echo "-- $url\n";
			
			echo "-- Checking whether we have this already...";
			if (head($url) && !$force)
			{
				echo " PDF exists (HEAD returns 200)\n";
				echo 'UPDATE publications SET internetarchive="' . $identifier . '" WHERE pdf="' . $reference->pdf . '";' . "\n";
			}
			else
			{
				echo "-- *** Not found! ***\n";
			}
		}
	}
}

//----------------------------------------------------------------------------------------


$force = false;


$filename = '';
if ($argc < 2)
{
	echo "Usage: import.php <RIS file> <mode>\n";
	exit(1);
}
else
{
	$filename = $argv[1];
}


$file = @fopen($filename, "r") or die("couldn't open $filename");
fclose($file);

//import_ris_file($filename, 'archive_import');
//import_ris_file($filename, 'archive_sha1_import');
import_ris_file($filename, 'check_archive_sha1_import');


?>