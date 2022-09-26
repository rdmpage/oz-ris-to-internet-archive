<?php

// Generate identifier mapping

require_once(dirname(__FILE__) . '/lib.php');
require_once(dirname(__FILE__) . '/ris.php');

//----------------------------------------------------------------------------------------
// Import reference and create identifier based on article metadata
function archive_id_import($reference)
{
	global $force;
	global $config;
	global $ai_sql;

	//print_r($reference);
		
	if (isset($reference->pdf))
	{

		// Can we construct a nicer identifier?
		
		$terms = array();
		
		if (isset($reference->secondary_title))
		{
			if (isset($reference->secondary_title))
			{
				$journal = $reference->secondary_title;
				
				if ($reference->secondary_title == '植物研究')
				{
					$journal = 'Bulletin of Botanical Research Harbin';
				}
				
				$journal = strtolower($journal);
				
				$commonWords = array('a', 'of', 'the');
				
				$journal = preg_replace('/\b('.implode('|',$commonWords).')\b/','',$journal);
				$journal = preg_replace('/^\s+/','',$journal);		
				$journal = preg_replace('/\s+$/','',$journal);		
				$journal = preg_replace('/\s+/','-',$journal);					
				$journal = preg_replace('/\(/','',$journal);					
				$journal = preg_replace('/\)/','',$journal);					

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

		
		if (isset($reference->url))
		{
			//$ai_sql .= 'UPDATE publications SET internetarchive="' . $identifier . '" WHERE url="' . $reference->url . '";' . "\n";
		}
		if (isset($reference->pdf))
		{
			echo 'UPDATE publications SET internetarchive="' . $identifier . '" WHERE pdf="' . $reference->pdf . '";' . "\n";
		}

	}
}



//----------------------------------------------------------------------------------------


$force = false;
//$force = true;


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
import_ris_file($filename, 'archive_id_import');



?>