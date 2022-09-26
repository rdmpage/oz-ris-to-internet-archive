<?php

// check PDF

$basedir = dirname(__FILE__) . '/cache';

$pdfs = scandir($basedir);

//$pdfs=array('31720-31756.pdf');

$bytes_to_read = 2048;

$problem_files = array();


foreach ($pdfs as $filename)
{
	if (preg_match('/\.pdf$/', $filename))
	{
		$full_filename = $basedir . '/' . $filename;
	
		$handle = fopen($full_filename, "r");
		$contents = fread($handle, $bytes_to_read);
		fclose($handle);	
		
		//echo "------\n";
		//echo $filename . "\n";
		//echo $contents . "\n";
		
		if (preg_match('/%PDF-\d+/', $contents))
		{
			// PDF
		}
		else
		{
			$problem_files[] = $filename;
		}
		
	}
}

//print_r($problem_files);

foreach ($problem_files as $filename)
{
	if (0)
	{
		$id = preg_replace('/-\d+\.pdf/', '', $filename);
		$url = 'https://revistas.unal.edu.co/index.php/cal/article/view/' . $id;
		echo $url . "\n";
	}
	
	if (0)
	{
		$id = preg_replace('/-/', '/', $filename);
		$id = preg_replace('/\.pdf/', '', $id);
		$url = 'https://revistas.unal.edu.co/index.php/cal/article/download/' . $id;
		echo $url . "\n";
	}
	
	if (0)
	{
		$id = preg_replace('/-/', '/', $filename);
		$id = preg_replace('/\.pdf/', '', $id);
		$url = 'https://revistas.unal.edu.co/index.php/cal/article/download/' . $id;
		echo 'UPDATE publications SET pdf=NULL WHERE pdf="' . $url . '";' . "\n";
	}	
	
	if (1)
	{
		// delete bad PDFs
		$full_filename = $basedir . '/' . $filename;
		echo $full_filename . "\n";
		unlink($full_filename);
	}
	
}



?>

