<?php

$source_pdf = 'JABG29P041_Catcheside.pdf';
$source_pdf = 'JABG29P147_Short.pdf';
$source_pdf = 'JABG29P071_Toelken.pdf';
$source_pdf = 'JABG29P023_Bean.pdf';
$source_pdf = 'JABG29P053_Kantvilas.pdf';

$source_pdf = 'NHBSS_058_1i_Supparatvikorn_DiscoveryO.pdf';
$source_pdf = 'NHBSS_021_3-4h_Robbins_ABotanicalAscent.pdf';

$source_pdf = '84-87.pdf';


$pdfs = scandir(dirname(__FILE__));

//$pdfs=array('36054-37425.pdf');


foreach ($pdfs as $source_pdf)
{
	if (preg_match('/\.pdf$/', $source_pdf))
	{
		// mutool to fix PDF
		if (1)
		{
			$command = "mutool clean $source_pdf output.pdf";

			system($command);
		}

		// last resort, extract images
		if (0)
		{
			$command = "convert -density 300 -quality 95 $source_pdf image.png";

			system($command);

			$files = scandir(dirname(__FILE__));
		
			$image_files = array();

			foreach ($files as $filename)
			{
				if (preg_match('/image-(?<page>\d+)\.png$/', $filename, $m))
				{	
					$old_name = $filename;
					$new_name = 'image-' . str_pad($m['page'], 3, '0', STR_PAD_LEFT) . '.png';
		
					rename($old_name, $new_name);
	
					$image_files[] = $new_name;
				}
			}

			//asort($image_files);
			// print_r($image_files);

			$command = 'convert image*.png output.pdf';
			system($command);

			// cleanup

			// delete images
			foreach ($image_files as $filename)
			{
				unlink($filename);
			}
		}

		// move original
		rename($source_pdf , 'originals/' . $source_pdf);

		// rename new version
		rename('output.pdf' , $source_pdf);	
	
	}
}



?>


