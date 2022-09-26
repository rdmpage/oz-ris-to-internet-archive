<?php

require_once(dirname(__FILE__) . '/config.inc.php');
require_once(dirname(__FILE__) . '/lib.php');
require_once(dirname(__FILE__) . '/ris.php');

$ai_sql = '';

$count = 1;

//----------------------------------------------------------------------------------------
function get_pdf_filename($pdf)
{
	$filename = '';
	
	/// local file file://
	if ($filename == '')
	{
		if (preg_match('/file:\/\/(?<filename>.*)$/', $pdf, $m))
		{
			$filename = $m['filename'];
		}	
	}	
	
	// downfile.aspx?id=3905
	if ($filename == '')
	{
		if (preg_match('/downfile.aspx\?id=(?<id>\d+)/', $pdf, $m))
		{
			$filename = $m['id'] . '.pdf';
		}	
	}		
	
	// http://qdhys.ijournal.cn/hyyhze/ch/reader/create_pdf.aspx?file_no=19810110&flag=1&year_id=1981&quarter_id=1
	if ($filename == '')
	{
		if (preg_match('/ijournal.cn/', $pdf))
		{
			if (preg_match('/file_no=(?<id>\d+)/', $pdf, $m))
			{
				$filename = $m['id'] . '.pdf';
			}	
		}
	}		
	
	// http://jtsb.ijournals.cn/jtsb_en/ch/reader/create_pdf.aspx?file_no=200405009&flag=1&journal_id=jtsb_en&year_id=2004
	if ($filename == '')
	{
		if (preg_match('/jtsb.ijournals.cn/', $pdf))
		{
			if (preg_match('/file_no=(?<id>\d+)/', $pdf, $m))
			{
				$filename = $m['id'] . '.pdf';
			}	
		}
	}	
		
	// http://journal.kib.ac.cn/CN/article/downloadArticleFile.do?attachType=PDF&id=30057
	if ($filename == '')
	{
		if (preg_match('/attachType=PDF&id=(?<id>\d+)/', $pdf, $m))
		{
			$filename = $m['id'] . '.pdf';
		}	
	}	
	
	//
	// https://journals.co.za/deliver/fulltext/nfi_annalstm/2004/1/1007.pdf?itemId=/content/nfi_annalstm/2004/1/AJA00411752_1006
	// bitstream (DSpace)
	if ($filename == '')
	{
		if (preg_match('/journals.co.za\//', $pdf, $m))
		{
			$parts = explode("/", $pdf);
		
			$filename = $parts[count($parts) - 1] . '.pdf';
		}	
	}	
		
	
	// AMNH
	if ($filename == '')
	{
		if (preg_match('/amnh.org/', $pdf, $m))
		{
			$parts = explode("/", $pdf);
			$filename = $parts[count($parts) - 1];
		}	
	}		
	
	// bitstream (DSpace)
	if ($filename == '')
	{
		if (preg_match('/bitstream\/(?<id>.*)/', $pdf, $m))
		{
			$filename = $m['id'];
			$filename = str_replace('/', '-', $filename);
		}	
	}	
	
	// J-STAGE
	// https://www.jstage.jst.go.jp/article/	
	if ($filename == '')
	{
		if (preg_match('/jstage.jst.go.jp\/article\/(?<id>.*)/', $pdf, $m))
		{
			$filename = $m['id'];
			$filename = str_replace('/', '-', $filename);
			$filename = str_replace('-_pdf', '.pdf', $filename);
		}	
	}	
	
	
	// http://www.jjbotany.com/getpdf.php?tid=2328
	if ($filename == '')
	{
		if (preg_match('/https?:\/\/www.jjbotany.com\/getpdf.php\?tid=(?<id>\d+)/', $pdf, $m))
		{
			$filename = $m['id'] . '.pdf';
		}	
	}		
	
	
	// http://sea-entomologia.org/PDF/
	if ($filename == '')
	{
		if (preg_match('/sea-entomologia.org/', $pdf, $m))
		{
			$filename = $pdf;
			$filename = preg_replace('/http?:\/\/sea-entomologia.org\/PDF\//', '', $filename);
			$filename = preg_replace('/http:\/\/sea-entomologia.org\/Publicaciones\/RevistaIbericaAracnologia\//', '', $filename);
			
			$filename = preg_replace('/í/u', 'i', $filename);
			
			$filename = str_replace('/', '-', $filename);
		}	
	}
	
	// https://australianmuseum.net.au/Uploads/Journals/
	if ($filename == '')
	{
		if (preg_match('/australianmuseum.net.au\/Uploads\/Journals/', $pdf, $m))
		{
			$filename = $pdf;
			$filename = preg_replace('/https?:\/\/australianmuseum.net.au\/Uploads\/Journals\//', '', $filename);
			$filename = str_replace('/', '-', $filename);
		}	
	}
	
	
	// http://rsnz.natlib.govt.nz/volume/rsnz_63/
	if ($filename == '')
	{
		if (preg_match('/rsnz.natlib.govt.nz/', $pdf, $m))
		{
			$filename = $pdf;
			$filename = preg_replace('/http:\/\/rsnz.natlib.govt.nz\/volume\/rsnz_\d+\//', '', $filename);
			$filename = str_replace('/', '-', $filename);
		}	
	}
	
	
	// http://faunaofindia.nic.in/PDFVolumes/records/
	if ($filename == '')
	{
		if (preg_match('/faunaofindia.nic.in/', $pdf, $m))
		{
			$filename = $pdf;
			$filename = str_replace('http://faunaofindia.nic.in/PDFVolumes/records/', '', $filename);
			$filename = str_replace('/', '-', $filename);
			$filename .= '.pdf';
		}	
	}

	
	// download?type=document;docid=
	if ($filename == '')
	{
		if (preg_match('/document;docid=(?<id>.*)$/', $pdf, $m))
		{
			$filename = $m['id'] . '.pdf';
			$filename = str_replace('/', '-', $filename);
		}	
	}
	
	// http://www.iaat.org.in/images/journalprev/1992/1/1-10.pdf
	if ($filename == '')
	{
		if (preg_match('/images\/(?<id>journalprev\/.*)\.pdf$/', $pdf, $m))
		{
			$filename = $m['id'] . '.pdf';
			$filename = str_replace('/', '-', $filename);
		}	
	}

	if ($filename == '')
	{
		if (preg_match('/images\/(?<id>.*)\.pdf$/', $pdf, $m))
		{
			$filename = $m['id'] . '.pdf';
			$filename = str_replace('/', '-', $filename);
		}	
	}
	
	
		
	if ($filename == '')
	{
		if (preg_match('/article\/download\/(?<id>.*)$/', $pdf, $m))
		{
			$filename = $m['id'] . '.pdf';
			$filename = str_replace('/', '-', $filename);
		}	
	}
	
	// if no name use basename
	if ($filename == '')
	{
		$filename = basename($pdf);
		$filename = str_replace('lognavi?name=nels&lang=en&type=pdf&id=', '', $filename);
	}
	
	if ($filename == '')
	{
		if (preg_match('/pdfstore\?sha1=/', $filename))
		{
			$filename = str_replace('pdfstore?sha1=', '', $filename);
		}
	}

	if ($filename == '')
	{
		if (preg_match('/download\?type=document;docid=/', $filename))
		{
			$filename = str_replace('download?type=document;docid=', '', $filename);
		}	
	}	
	
	if (!preg_match('/\.pdf$/', $filename))
	{
		$filename .= '.pdf';
	}
		
	echo "filename=$filename\n";
	
	return $filename;
}


//----------------------------------------------------------------------------------------
// Import reference and create identifier based on metadata
function archive_import_ris($reference)
{
	global $force;
	global $config;
	
	global $count;


	// fetch PDF
	
	$cache_dir =  $config['cache_dir'];
	//$cache_dir =  dirname(__FILE__) . "/cache-zh";
	//$cache_dir =  dirname(__FILE__) . "/cache-aps-zh";
	
	
	$cache_dir =  "/Volumes/Samsung_T5/pdfs/Sichuan Journal of Zoology";
	
	$article_pdf_filename = $cache_dir . '/' . get_pdf_filename($reference->pdf);
	
	if (file_exists($article_pdf_filename) && !$force)
	//if (file_exists($article_pdf_filename))
	{
		echo "Have PDF $article_pdf_filename\n";
	}
	else
	{				
		$command = "curl -C - -A \"Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:59.0) Gecko/20100101 Firefox/59.0\" --location '" . $reference->pdf . "' --output '" . $article_pdf_filename . "'";
		$command = "curl -A \"Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:59.0) Gecko/20100101 Firefox/59.0\" --location '" . $reference->pdf . "' --output '" . $article_pdf_filename . "'";
		
		$command = "wget --timeout=20 --tries=4 '" . $reference->pdf . "' -O '$article_pdf_filename'";
		echo $command . "\n";
		system ($command);
		//exit();
		
		$count++;
		
		// Give server a break every 10 items
		if (($count++ % 1) == 0)
		{
			$rand = rand(1000000, 3000000);
			echo "\n-- ...sleeping for " . round(($rand / 1000000),2) . ' seconds' . "\n\n";
			usleep($rand);
		}
		
	}	

}



//----------------------------------------------------------------------------------------


$force = false;
//$force = true;

$filename = '';
if ($argc < 2)
{
	echo "Usage: fetch_pdfs.php <RIS file> <mode>\n";
	exit(1);
}
else
{
	$filename = $argv[1];
}


$file = @fopen($filename, "r") or die("couldn't open $filename");
fclose($file);

import_ris_file($filename, 'archive_import_ris');

//echo $ai_sql;


?>