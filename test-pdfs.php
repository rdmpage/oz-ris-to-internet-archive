<?php

// check PDF using mutools, rename any bad pdfs

$basedir = dirname(__FILE__) . '/cache';

$basedir =  "/Volumes/Samsung_T5/pdfs/Sichuan Journal of Zoology";
$basedir = '/Volumes/Samsung_T5/pdfs/Gardens Bulletin Singapore';


// Acta Phyto Sin
//$basedir = '/Volumes/Samsung_T5/cache-to-do/cache-aps-zh';

// J Trop 
//$basedir = '/Volumes/Samsung_T5/cache-to-do/cache-zh';

// 
$basedir =  "/Volumes/Ultra Touch/pdfs/Acta Entomologica Sinica";


$pdfs = scandir($basedir);

// $pdfs=array('1899.pdf', '1882.pdf');

//$pdfs=array('3201.pdf');

$problem_files = array();


foreach ($pdfs as $filename)
{
	if (preg_match('/\.pdf$/', $filename))
	{
		$full_filename = $basedir . '/' . $filename;
		
		$command = "mutool info '" . $full_filename . "'";
		$output = array();
		$return_var = 0;

		exec($command, $output, $return_var);

		//print_r($output);

		echo "return_var $return_var\n";
		
		if ($return_var != 0)
		{
			$problem_files[] = $filename;
			
			$oldname = $full_filename;
			$newname = $basedir . '/' . 'bad-' . $filename;
			
			system("mv '" . $oldname . "' '" . $newname . "'");
			
			//rename($oldname, $newname);
		}
	}
}

print_r($problem_files);

/*
foreach ($problem_files as $filename)
{
	$id = str_replace('.pdf', '', $filename);
	
	
	//echo 'UPDATE publications SET pii="badpdf" WHERE pdf = "http://www.plantsystematics.com/CN/article/downloadArticleFile.do?attachType=PDF&id=' . $id . '";' . "\n";
	
	echo 'UPDATE publications SET pii="badpdf" WHERE pdf LIKE "http://jtsb.ijournals.cn/jtsb_en/ch/reader/create_pdf.aspx?file_no=' . $id . '&%";' . "\n";



}

*/

?>

		
