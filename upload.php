<?php


require_once(dirname(__FILE__) . '/lib.php');
require_once(dirname(__FILE__) . '/ris.php');

$ai_sql = '';

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
	
	// https://db.koreascholar.com/ASP/Download.aspx?key=*OSu1j6BpquUL%2fMy0JTpQKQ%5e%5e&code=
	if ($filename == '')
	{
		if (preg_match('/\*OSu1j6BpquUL%2fMy0JTpQKQ%5e%5e&code=(?<id>.*)$/', $pdf, $m))
		{
			$filename = $m['id'] . '.pdf';
			$filename = str_replace('/', '-', $filename);
		}
	}		
	
	// opuscula.elte.hu
	if ($filename == '')
	{
		if (preg_match('/opuscula.elte.hu/', $pdf, $m))
		{
			$filename = $pdf;
			$filename = str_replace('http://opuscula.elte.hu/PDF/', '', $filename);
			$filename = str_replace('/', '-', $filename);
		}
	}	
	
	
	if ($filename == '')
	{
		if (preg_match('/web.archive.org/', $pdf, $m))
		{
			$filename = $pdf;
			
			// web archive
			$filename = preg_replace('/https:\/\/web.archive.org\/web\/\d+\//', '', $filename);
			
			// domain-specific
			$filename = basename($filename);
			
			$filename = str_replace('/', '-', $filename);
			
		}
	}		
	
	// https://dgfo-articulata.de/downloads/articulata/
	
	if ($filename == '')
	{
		if (preg_match('/dgfo-articulata.de/', $pdf, $m))
		{
			$filename = $pdf;
			$filename = str_replace('https://dgfo-articulata.de/downloads/articulata//', '', $filename);
			$filename = str_replace('https://dgfo-articulata.de/downloads/articulata/', '', $filename);
			$filename = str_replace('articulata_I_1975_1982/', '', $filename);
			$filename = str_replace('/', '-', $filename);
			$filename = str_replace(' ', '%20', $filename);
		}
	}	
	
	
	if ($filename == '')
	{
		if (preg_match('/sar.fld.czu.cz/', $pdf, $m))
		{
			$filename = $pdf;
			$filename = str_replace('https://sar.fld.czu.cz/cache/article-data/SaR/Published_volumes/', '', $filename);
			$filename = str_replace('/', '-', $filename);
			
		}
	}	
	
	
	
	// https://mds.marshall.edu/cgi/viewcontent.cgi?article=1271&context=euscorpius
	if ($filename == '')
	{
		if (preg_match('/mds.marshall.edu\/cgi\/viewcontent.cgi\?article=(?<id>\d+)/', $pdf, $m))
		{
			$filename = $m['id'] . '.pdf';
		}
	}	
	
	
	// http://www.v-zool.kiev.ua/pdfs/
	if ($filename == '')
	{
		if (preg_match('/kiev.ua(.*)pdfs?\/(?<pdf>.*)/', $pdf, $m))
		{
			$filename = $m['pdf'];
			$filename = str_replace('/', '-', $filename);
		}
	}	
	
	
	
	if ($filename == '')
	{
		if (preg_match('/bitstream/', $pdf, $m))
		{
			$filename = basename($pdf);
			$filename = str_replace('/', '-', $filename);
		}
	}		

	
	
	if ($filename == '')
	{
		if (preg_match('/https?:\/\/lasef.org\/wp-content\/uploads\/BSEF\/(?<id>.*)/', $pdf, $m))
		{
			$filename = $m['id'];
			$filename = str_replace('/', '-', $filename);
		}
	}		
	
	
	
	// http://maxbot.botany.pl/cgi-bin/pubs/data/article_pdf?id=712
	if ($filename == '')
	{
		if (preg_match('/http:\/\/maxbot.botany.pl\/cgi-bin\/pubs\/data\/article_pdf\?id=(?<id>.*)/', $pdf, $m))
		{
			$filename = $m['id'] . '.pdf';
			$filename = str_replace('/', '-', $filename);
		}
	}		
	
	// vital.seals.ac.za
	if ($filename == '')
	{
		if (preg_match('/vital.seals.ac.za/', $pdf, $m))
		{
			$filename = str_replace('http://vital.seals.ac.za:8080/vital/access/services/Download/vital:', '', $pdf);
			$filename = str_replace('/SOURCEPDF', '.pdf', $filename);
		}
	}	
	
	if ($filename == '')
	{
		if (preg_match('/(download|view|downloadSuppFile)\/(?<id1>(\d+|v[^\/]+))\/(?<id2>\d+)$/', $pdf, $m))
		{
			$filename = $m['id1'] . '-' . $m['id2'];
		}
	}
	
	
	
	
	if ($filename == '')
	{
		if (preg_match('/downloadArticleFile.do\?attachType=PDF&id=(?<id>\d+)/', $pdf, $m))
		{
			//$pos = strrpos($pdf, '/');
			//$filename = substr($pdf, $pos + 1);
			$filename = $m['id'] . '.pdf';
		}
	}	
	
	
	if ($filename == '')
	{
		if (preg_match('/scholarspace.manoa.hawaii.edu/', $pdf))
		{
			$filename = basename($pdf);
		}
		
		if (preg_match('/deepblue.lib.umich.edu/', $pdf))
		{
			$filename = basename($pdf);
		}
		
	}
	
	
	// https://www.jstage.jst.go.jp/article/jji1950/28/1/28_1_91/_pdf
	if ($filename == '')
	{
		if (preg_match('/\/(?<id>[0-9A-Z_-]+)\/_pdf/Uu', $pdf, $m))
		{
			$filename = $m['id'] . '.pdf';
		}
	}	
	
	
	// /biblio.naturalsciences.be
	if ($filename == '')
	{
		if (preg_match('/biblio.naturalsciences.be/', $pdf, $m))
		{
			$filename = basename($pdf);
		}	
	}		
	
	// repositories.lib.utexas
	if ($filename == '')
	{
		if (preg_match('/repositories.lib.utexas/', $pdf, $m))
		{
			$parts = explode('/', $pdf);			
		
			$filename = $parts[count($parts) - 1];
		}	
	}	
	
	// https://www.biotaxa.org/RSEA/article/download/37023/31751
	if ($filename == '')
	{
		if (preg_match('/www.biotaxa.org\/RSEA\/article\/download\/(?<id>\d+\/\d+)/', $pdf, $m))
		{
			$filename = $m['id'];
			$filename = str_replace('/', '-', $filename);
			$filename .= '.pdf';
		}	
	}	
	
	// http://biblio.naturalsciences.be/rbins-publications/bulletins-de-linstitut-royal-des-sciences-naturelles-de-belgique-entomologie/
	if ($filename == '')
	{
		if (preg_match('/biblio.naturalsciences.be/', $pdf, $m))
		{
			$filename = str_replace('http://biblio.naturalsciences.be/rbins-publications/bulletins-de-linstitut-royal-des-sciences-naturelles-de-belgique-entomologie/', '', $pdf);
			$filename = str_replace('/', '-', $filename);
		}	
	}	

	
	// BioNames
	// http://bionames.org/bionames-archive/issn/1049-233X/65/169.pdf
	if ($filename == '')
	{
		if (preg_match('/bionames-archive\/issn\/[0-9]{4}-[0-9]{3}(\d|X)\/(?<id>.*)/', $pdf, $m))
		{
			$filename = $m['id'];
			$filename = str_replace('/', '-', $filename);
		}	
	}		
	
	// Esakia
	// https://catalog.lib.kyushu-u.ac.jp/opac_download_md/
	if ($filename == '')
	{
		if (preg_match('/opac_download_md\/(?<id>.*)/', $pdf, $m))
		{
			$filename = $m['id'];
			$filename = str_replace('/', '-', $filename);
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
	
	// http://www.salamandra-journal.com/index.php/home/contents/
	if ($filename == '')
	{
		if (preg_match('/www.salamandra-journal.com/', $pdf, $m))
		{
			$filename = $pdf;
			$filename = str_replace('http://www.salamandra-journal.com/index.php/home/contents/', '', $filename);
			$filename = str_replace('/file', '', $filename);
			$filename = str_replace('/', '-', $filename);
			$filename .= '.pdf';
		}	
	}	
	
	// AJA03040798_802&mimeType=pdf
	
	// https://publications.rzsnsw.org.au/doi/pdf/
	if ($filename == '')
	{
		if (preg_match('/publications.rzsnsw.org.au/', $pdf, $m))
		{
			$filename = $pdf;
			$filename = str_replace('https://publications.rzsnsw.org.au/doi/pdf/', '', $filename);
			$filename = str_replace('/', '-', $filename);
			$filename .= '.pdf';
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
	
	if (preg_match('/isAllowed/', $filename))
	{
		$filename = str_replace('?sequence=1&isAllowed=y', '', $filename);
	}	
	
	if (!preg_match('/\.pdf$/', $filename))
	{
		$filename = str_replace('&mimeType=pdf', '', $filename);
	
		$filename .= '.pdf';
	}
	
	
	$filename = str_replace('article_id_', '', $filename);
	
	//$filename = urldecode($filename);
		
	echo "filename=$filename\n";
	
	return $filename;
}


//----------------------------------------------------------------------------------------
// Import reference and create identifier based on metadata
function archive_import_ris($reference)
{
	global $force;
	global $config;
	global $ai_sql;

	print_r($reference);
	
	if (isset($reference->authors))
	{
		if (isset($reference->authors[0]) && ($reference->authors[0] == ""))
		{
			unset($reference->authors);
		}
	
	}
	
	
	//exit();
	
	//exit();
	
	//echo get_pdf_filename($reference->pdf) . "\n";
	
	
	
	if (1)
	{
		// Volume based, e.g. monograph
		if (0)
		{
			$terms = array();
		
			$keys = array('secondary_title', 'volume', 'year');
		
		
			foreach ($keys as $k)
			{			
				if (isset($reference->{$k}))
				{
					$v = $reference->{$k};
					$v = mb_strtolower($v);
					$v = str_replace(' ', '', $v);
					$v = str_replace('_', '', $v);				
					switch ($k)
					{
						case 'secondary_title':
							if ($reference->issn == '0083-7903')
							{
								$v = 'nzoimemoir';
							}
							break;
				
						case 'volume':						
							$v = str_pad($v, 4, '0', STR_PAD_LEFT);
							break; 
						
						case 'issue':
							$v = str_pad($v, 3, '0', STR_PAD_LEFT);
							break; 

						case 'year':
							$v = str_pad($v, 4, '0', STR_PAD_LEFT);
							break; 

						default:
							break;				
					}
					$terms[] = $v;		
				}
			
		
				print_r($terms);
		
				$id = join('', $terms);
			
			}
		}
		
		// Article based
		if (1)
		{
			$terms = array();
			
			if (isset($reference->secondary_title))
			{
				if (isset($reference->secondary_title))
				{
					$journal = $reference->secondary_title;
					
					if ($journal == 'Genus - International Journal of Invertebrate Taxonomy')
					{
						$journal = 'Genus' . ' ' . $reference->issn;
					}					
					if ($journal == 'Blumea - Biodiversity, Evolution And Biogeography of Plants')
					{
						$journal = 'Blumea' . ' ' . $reference->issn;
					}					
					if ($journal == 'Nelumbo - The Bulletin of The Botanical Survey of India')
					{
						$journal = 'Nelumbo' . ' ' . $reference->issn;
					}					
					
					if (isset($reference->issn) &&  $reference->issn== '0065-6755')
					{
						echo $journal . "\n";
						$journal = 'Amazoniana';
					}

					if (isset($reference->issn) &&  $reference->issn== '0132-8069')
					{
						echo $journal . "\n";
						$journal = 'rusentj';
					}
					
					if (in_array($reference->issn, array('1805-5648', '1803-1544')))
					{
						$journal = 'sar' . str_replace('-', '', $reference->issn);
					}

							

					
					$journal = mb_strtolower($journal);
					
					$commonWords = array('a', 'die', 'fur', 'für', 'of', 'the', 'und');
					
					$journal = preg_replace('/\b('.implode('|',$commonWords).')\b/','',$journal);
					$journal = preg_replace('/^\s+/','',$journal);		
					$journal = preg_replace('/\s+$/','',$journal);		
					$journal = preg_replace('/\s+/','-',$journal);						
					$journal = preg_replace('/,/','',$journal);
					$journal = preg_replace('/é/','e',$journal);	
					$journal = preg_replace('/ç/','c',$journal);
					$journal = preg_replace('/í/','i',$journal);
					$journal = preg_replace('/á/','a',$journal);
					$journal = preg_replace('/ó/','o',$journal);
					$journal = preg_replace('/ü/','u',$journal);
					
					$journal = preg_replace('/\(/','',$journal);			
					$journal = preg_replace('/\)/','',$journal);
					$journal = preg_replace('/\'/','',$journal);			
					
					$journal = preg_replace('/--+/','-',$journal);

					$terms[] = $journal;
				}
				if (isset($reference->volume))
				{
					$volume = $reference->volume;
					$volume = preg_replace('/á/','a', $volume);

					$volume = strtolower($volume);
					$volume = str_replace(', ', '-', $volume);
					$volume = str_replace(',', '', $volume);
					$volume = str_replace('  ', ' ', $volume);
					$volume = str_replace(' ', '-', $volume);
					$terms[] = $volume;
				}
								
				if (isset($reference->issue))
				{
					$issue = $reference->issue;
					$issue = strtolower($issue);
					$issue = str_replace(' ', '-', $issue);
					$issue = str_replace('/', '-', $issue);
					
					if ($issue == '增刊一')
					{
						$issue = 'sup';
					}
					
					//echo "$issue\n"; exit();
					$issue = preg_replace('/supplementum\-+/', 's', $issue);
					
					// $terms[] = str_pad($issue, 3, '0', STR_PAD_LEFT);
					
					if (isset($reference->issn) && in_array($reference->issn, array('0024-1652')))
					{
						// don't use issue
					}
					else
					{		
						// use issue			
						$terms[] = $issue;
					}
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
			
			//print_r($terms);
		}
		
		// special handling of some journals
		
		// id based on PDF file name
		if ($reference->issn =='0080-9462')
		{
			$id = get_pdf_filename($reference->pdf);
			$id = strtolower($id);
			$id = str_replace('.pdf', '', $id);	
			
		}
		
		if ($reference->issn =='0723-9319')
		{
			$id = get_pdf_filename($reference->pdf);
			$id = strtolower($id);
			$id = str_replace('.pdf', '', $id);	
			
		}		
		
		// Sabinet use handle-style identifier
		if ($reference->issn =='0304-0798' or $reference->issn == '1681-5556')
		{
			$id = get_pdf_filename($reference->pdf);
			$id = strtolower($id);
			$id = str_replace('.pdf', '', $id);				
		}		
		
		// lacking pages
		if ($reference->issn =='2357-3759')
		{
			$id = get_pdf_filename($reference->pdf);
			$id = strtolower($id);
			$id = str_replace('.pdf', '', $id);				
			$id = 'caldasia-' . $id;	
		}
		
		// essier to use PDF
		if ($reference->journal =='Abhandlungen Aus Dem Gebiete Der Naturwissenschaften Hamburg')
		{
			$id = get_pdf_filename($reference->pdf);
			$id = strtolower($id);
			$id = str_replace('.pdf', '', $id);				
		}
		
		// lacking pages
		if (in_array($reference->issn, array('0097-0425','1050-4842')))
		{
			$id = 'occ-' . $reference->issn . '-' . $reference->volume . '-' . $reference->year;
			$id = strtolower($id);
		}		
		
		// lacking pages
		if ($reference->issn == '2373-0951')
		{
			$id = get_pdf_filename($reference->pdf);
			$id = strtolower($id);
			$id = str_replace('_', '-', $id);	
			$id = str_replace('.pdf', '', $id);	
			$id = 'mesoamericanherpetology-' . $id;
		}
	
		// lacking pages
		if ($reference->issn == '2333-2468')
		{
			$id = 'caribbean-herpetology-' . $reference->issn . '-' . $reference->volume;
			$id = strtolower($id);
		}
		
		// special handling for Plant Diversity as we may lack metadata
		if ($reference->issn == '2096-2703' || $reference->issn == '0253-2700')
		{
			$pdf_id = $reference->pdf;
			$pdf_id = str_replace('file://', '', $pdf_id);
			$pdf_id = str_replace('.pdf', '', $pdf_id);
			
			$id = 'plantdiversity-' . $reference->issn . '-' . $pdf_id;
			$id = strtolower($id);
		}
		
		// special handling for Zoological Research as we may lack metadata
		if ($reference->issn == '2095-8137' )
		{
			$pdf_id = $reference->pdf;
			$pdf_id = str_replace('file://', '', $pdf_id);
			$pdf_id = str_replace('.pdf', '', $pdf_id);
			
			$id = 'zoological-research-' . $reference->issn . '-' . $pdf_id;
			$id = strtolower($id);
		}	
		
		// 2337-876X Treubia, missing some pages so use DOI
		if ($reference->issn == '2337-876X' )
		{
			$id = $reference->doi;
			$id = str_replace('10.14203/', '', $id);
			$id = str_replace('trb.', 'treubia.', $id);			
			$id = str_replace('.', '-', $id);
			$id = strtolower($id);
		}	
			
			
		// 1851-7471 Revista de la Sociedad Entomológica Argentina
		if ($reference->issn == '1851-7471')
		{
			if (preg_match('/www.biotaxa.org\/RSEA\/article\/download\/(?<id>\d+\/\d+)/', $reference->pdf, $m))
			{		
				$pdf_id = $m['id'];
				$pdf_id = str_replace('/', '-', $pdf_id);
			
				$id = 'rsea-' . $reference->issn . '-' . $pdf_id;
			}
			else
			{
				echo "Bad id " . $reference->pdf . "\n";
				exit();
			
			}
		}	
		
		// special handling for Pearce-Sellards Series Texas Memorial Museum
		if ($reference->issn == '0079-0354' )
		{
			$pdf_id = $reference->pdf;
			$pdf_id = str_replace('file://', '', $pdf_id);
			$pdf_id = str_replace('.pdf', '', $pdf_id);
			
			$id = 'pearce-sellards-series-' . str_replace('.pdf', '', get_pdf_filename($reference->pdf));
			$id = strtolower($id);
		}			
			
			
		// Mémoires du Musée Royal D'histoire Naturelle de Belgique Hors Série
		if ($reference->issn == '0770-7622' )
		{
			//$id = str_replace('memoires-du-musee-royal-dhistoire-naturelle-de-belgique-hors-serie-', '0770-7622-', $id);
			$id = str_replace('memmusroyhistnatbelghorsserie-', '0770-7622-', $id);
			
			$id = preg_replace('/\((\d)\)/', '-$1', $id);
		}	
		
		// transactions of the lepidopterological society of japan
		if ($reference->issn =='0024-0974')
		{
			$id = strtolower($reference->doi);
			$id = str_replace('10.18984/', 'tlsj-', $id);
			$id = str_replace('.', '-', $id);
			$id = str_replace('/', '-', $id);
			$id = str_replace('_', '-', $id);
			$id = strtolower($id);
		}
		
		// Konowia (Vienna)
		if ($reference->journal == 'Konowia (Vienna)')
		{
			$id = 'konowia-' . str_replace('.pdf', '', get_pdf_filename($reference->pdf));
			$id = strtolower($id);	
			$id = str_replace('kon_', '', $id);	
			$id = str_replace('_', '-', $id);	
		}
		
		// 0253-116X
		if ($reference->issn =='0253-116X')
		{
			$id = 'linzer-' . str_replace('.pdf', '', get_pdf_filename($reference->pdf));
			$id = strtolower($id);	
			$id = str_replace('_', '-', $id);	
		}
		
		// 2539-200X
		if ($reference->issn =='2539-200X')
		{
			$id = 'biota-colombiana-' . str_replace('.pdf', '', get_pdf_filename($reference->pdf));
			$id = strtolower($id);	
		}

		// 0454-6296
		if ($reference->issn =='0454-6296')
		{
			$id = 'acta-entomologica-sinica-' . str_replace('.pdf', '', get_pdf_filename($reference->pdf));
			$id = strtolower($id);	
		}

		// 0385-2423 Bulletin of The National Science Museum Series A (Zoology)
		if ($reference->issn =='0385-2423')
		{
			$id = 'bullnatscimusazool-' . str_replace('.pdf', '', get_pdf_filename($reference->pdf));
			$id = strtolower($id);	
		}

		if ($reference->issn =='0075-207X')
		{
			$id = 'occasionalpaper-0075-207X-' . str_replace('.pdf', '', get_pdf_filename($reference->pdf));
			$id = strtolower($id);	
		}
		
		// Zobodat PDFs with journal name in PDF file name
		//if ($reference->issn =='0006-8152')
		//if ($reference->issn =='0342-412X')
		if (in_array($reference->issn, array('0368-1254')))
		{
			$id = str_replace('.pdf', '', get_pdf_filename($reference->pdf));
			$id = strtolower($id);
			$id = str_replace('_', '-', $id);	
		}
		
		// Occasional Papers
		if ($reference->issn == '0076-8413' )
		{
			$reference->secondary_title = 'Occasional Papers of the Museum of Zoology University of Michigan';
			$id = 'occpap-0076-8413-' . str_replace('.pdf', '', get_pdf_filename($reference->pdf));
			$id = strtolower($id);	
		}	
		
		// 0037-928X
		if ($reference->issn == '0037-928X' )
		{
			
			$id = str_replace('.pdf', '', get_pdf_filename($reference->pdf));
			$id = str_replace('&_', '', $id);
			$id = str_replace('.', '-', $id);
			$id = str_replace('_', '-', $id);
			$id = strtolower($id);
			$id = 'bsef-' . $id;
		}
		
		// 0013-9440	
		if ($reference->issn == '0013-9440' )
		{			
			$id = 'eos-' . $reference->issn . '-' . str_replace('10261/', '', $reference->handle);
		}
		
		if ($reference->issn == '1908-6865' && $id == '')
		{
			if (isset($reference->doi))
			{
				$id = str_replace('10.26757/', '', $reference->doi);
			}
		}	
		
		// 2444-8192
		// Bolletí de la Societat D'història Natural de Les Balears	
		if ($reference->issn == '2444-8192' )
		{			
			$id = 'bshnb-2444-8192-' . str_replace('.pdf', '', get_pdf_filename($reference->pdf));
			$id = strtolower($id);	
		}

		if ($reference->issn == '1179-7193' )
		{			
			$id = 'fnz-1179-7193-' . $reference->volume;
			$id = strtolower($id);	
		}

		if ($reference->issn == '0082-3074' )
		{			
			$id = str_replace('.pdf', '', get_pdf_filename($reference->pdf));
			$id = str_replace('tmm-', 'tmm-0082-3074-', $id);
			$id = strtolower($id);	
		}
		
		if ($reference->issn == '0149-175X' )
		{			
			$id = str_replace('.pdf', '', get_pdf_filename($reference->pdf));
			$id = str_replace('OP', 'op-' . $reference->issn . '-', $id);
			$id = strtolower($id);	
		}
		
		if ($reference->issn == '1474-0036' )
		{			
			$id = $reference->doi;
			$id = str_replace('10.1017/', '', $id);
			$id = str_replace('10.24823/', '', $id);
			$id = str_replace('.', '-', $id);
			$id = strtolower($id);	
		}
		
		if ($reference->issn == '2269-6016' )
		{			
			$id = str_replace('.pdf', '', get_pdf_filename($reference->pdf));
			$id = str_replace('_f', '',  $id);
			$id = strtolower($id);	
		}
		
		if ($reference->issn == '1026-051X' )
		{			
			$id = str_replace('.pdf', '', get_pdf_filename($reference->pdf));
			$id = "fareasternentomol" . $id;
			$id = strtolower($id);	
		}

		if ($reference->issn == '0076-3519' || $reference->issn == '1545-1410')
		{			
			
			
			if ($reference->year >= 2010 && $reference->year <= 2012)
			{
				$volume = $reference->doi;
				$volume = str_replace('10.1644/', '', $volume);
				$volume = str_replace('.1', '', $volume);
			}
			else
			{
				if (!isset($reference->volume))
				{
					$volume = $reference->issue;
				}
				else
				{
					$volume = $reference->volume;
				}
			}
			
			$id = "mammalianspecies-$volume";
		}
		
		// 0011-3891
		if (in_array($reference->issn, array('0011-3891')))
		{			
			$id = str_replace('.pdf', '', $reference->pdf);
			$id = str_replace('https://wwwops.currentscience.ac.in/Downloads/article_id_', '', $id);
			$id = str_replace(' ', '', $reference->secondary_title) . '-' . str_replace('_', '-', $id);
			$id = strtolower($id);
			
		}
		
		if (in_array($reference->issn, array('1560-2745')))
		{			
			$id = str_replace('.pdf', '', $reference->pdf);
			$id = str_replace('https://www.fungaldiversity.org/fdp/sfdp/', '', $id);
			$id = str_replace(' ', '', $reference->secondary_title) . '-' . str_replace('_', '-', $id);
			$id = strtolower($id);
			
		}
		
		
		// OJS
		if (in_array($reference->issn, array('2007-9133','2689-0682','2586-9892', '2489-4966', '2317-1111', '1048-8138', '2575-9256', '1070-4140')))
		{			
			if (preg_match('/(\d+)\/(\d+)$/', $reference->pdf, $m))
			{
				$id = str_replace(' ', '-', $reference->secondary_title) . '-' . $m[1] . '-' . $m[2];
				$id = strtolower($id);
			}
		}
		
		// Vestnik Zoologii
		if (in_array($reference->issn, array('2073-2333','0084-5604')))
		{			
			if (isset($reference->doi))
			{
				$id = $reference->doi;
				$id = str_replace('10.1515/', '', $id);
				$id = str_replace('10.2478/', '', $id);
				$id = str_replace('-', '', $id);
				$id = strtolower($id);
			}
		}
		
		// Euscorpius
		if (in_array($reference->issn, array('1536-9307')))
		{			
			$id = str_replace('.pdf', '', $reference->pdf);
			$id = str_replace('https://mds.marshall.edu/cgi/viewcontent.cgi?article=', '', $id);
			$id = str_replace('&context=euscorpius', '', $id);
		
			$id = 'euscorpius' . $id;
		}
		

		// Bonner Zoologische Monographie, etc.
		if (in_array($reference->issn, array('0302-671X', '0006-7172')))
		{			
			$id = str_replace('.pdf', '', $reference->pdf);
			$id = str_replace('https://www.zobodat.at/pdf/', '', $id);			
			$id = str_replace(' ', '-', $id);
			$id = str_replace('_', '-', $id);
			$id = strtolower($id);
		}
	
		// 0300-5267
		if (in_array($reference->issn, array('0300-5267')))
		{			
			$id = get_pdf_filename($reference->pdf);
			$id = str_replace('.pdf', '', $id);
			$id = 'shilaprevlep' . $id;
		}
		
		// 1855-5810
		if (in_array($reference->issn, array('1855-5810')))
		{			
			$id = get_pdf_filename($reference->pdf);
			$id = str_replace('.pdf', '', $id);
			$id = strtolower($id);
		}
		
		// Bonner Zoologische Monographie, etc.
		if (in_array($reference->issn, array('2159-6719','1388-7890')))
		{			
			$id = $reference->doi;
			
			$id = preg_replace('/^10\.\d+\//', 'ijo', $id);
			$id = preg_replace('/[-_\.]/', '', $id);
		}

		if ($reference->secondary_title == 'Insecta Koreana')
		{			
			$id = $reference->url;
			
			$id = str_replace('http://db.koreascholar.com/article?code=', '', $id);
			$id = 'insectakoreana' . $id;
		}
		
		if ($reference->secondary_title == 'Korean Journal of Systematic Zoology')
		{			
			$id = $reference->pdf;
			
			$id = str_replace('http://koreascience.or.kr:80/article/JAKO', '', $id);
			$id = str_replace('.pdf', '', $id);
			$id = 'koreanjsystzool' . $id;
		}

		if (in_array($reference->issn, array('1123-6787')))
		{			
			$id = get_pdf_filename($reference->pdf);
			$id = str_replace('.pdf', '', $id);
			$id = 'quadernoromagna-' . $id;
		}

		if ($reference->issn =='1008-0384')
		{			
			$id = get_pdf_filename($reference->pdf);
			$id = str_replace('.pdf', '', $id);
			$id = 'fjnyxb' . $id;
		}


		if ($reference->issn =='1028-6764')
		{			
			$id = get_pdf_filename($reference->pdf);
			$id = str_replace('.pdf', '', $id);
			$id = str_replace('Quad_', 'quadrifina-', $id);
			$id = str_replace('_', '-', $id);
		}


		/*
		if (preg_match('/2-/', $reference->issn))
		{
			$id = 'isbn-' . $reference->issn;
		}
		*/
		

		echo "-- $id\n";
		
		if ($id == '')
		{
			echo "Bad id: $id\n";
			exit();
		
		}
		
		if (isset($reference->pdf))
		{
			$ai_id[$reference->pdf] = $id;
			
			
			$just_ids = false;
			//$just_ids = true;
			
			if ($just_ids)
			{
				$url = 'https://archive.org/details/' . $id;
				
				if (isset($reference->pdf))
				{
					
					
					$have = head($url);
					
					if ($have)
					{
						echo  'UPDATE publications SET internetarchive="' . $id . '" WHERE pdf="' . $reference->pdf . '";' . "\n";
					}
				}
				/*
				if (isset($reference->url))
				{
					$ai_sql .= 'UPDATE publications SET internetarchive="' . $id . '" WHERE url="' . $reference->url . '";' . "\n";
				}
				*/
			
			}
			else 
			{		
		
			// fetch PDF
			$cache_dir =  dirname(__FILE__) . "/cache";
			$cache_dir =  $config['cache_dir'];
			
			//$cache_dir =  "/Volumes/Samsung_T5/pdfs/Zootaxa";
			$cache_dir =  "/Volumes/Samsung_T5/pdfs/Neue Entomologische Nachrichten";
			$cache_dir =  "/Volumes/Samsung_T5/pdfs/Atalanta";
			
			$cache_dir =  "/Volumes/Samsung_T5/pdfs/Plant Diversity";
			//$cache_dir =  "/Volumes/Samsung_T5/pdfs/Boletín Del Museo Nacional de Historia Natural, Chile";
			//$cache_dir =  "/Volumes/Samsung_T5/pdfs/Zoological Research";
			
			// $cache_dir =  $config['cache_dir'];
			
			// $cache_dir =  "/Volumes/Ultra Touch/pdfs/Revista de la Sociedad Entomológica Argentina";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Pearce-Sellards Series";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Résultats scientifiques du voyage aux Indes orientales Néerlandaises";
			
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Transactions of the Lepidopterological Society of Japan";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Konowia";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Proceedings of The Hawaiian Entomological Society";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Linzer Biologische Beiträge";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Zoosystematica Rossica";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Biota Colombiana";
			
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Acta Entomologica Sinica";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Orchid Monographs";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Acta Zoológica Lilloana";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Bulletin of the National Science Museum. Series A, Zoology";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Occasional Paper";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Occasional Paper";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Bulletin of the National Science Museum. Series A, Zoology";
			
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Acta Entomologica Sinica";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Botanische Jahrbücher Für Systematik, Pflanzengeschichte Und Pflanzengeographie";
			
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Contributions From The Museum of Paleontology University of Michigan";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Amazonia";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Polish Botanical Journal";
			
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Proceedings of the Zoological Institute RAS";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/rcin";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Entomologische Blätter";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Jahrbücher Des Nassauischen Vereins Für Naturkunde";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Sugapa";
			
			$cache_dir =  "/Volumes/Ultra Touch/pdfs";

			$cache_dir =  "/Volumes/Ultra Touch/pdfs/deepblue";
			
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Entomologisk Tidskrift";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Bulletin de la Société Entomologique de France";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Philippine Journal of Systematic Biology";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Eos";
						
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Bolleti de la Societat dHistoria Natural de Les Balears";

			$cache_dir =  "fix";

			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Fauna of New Zealand Landcare";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Bulletin of The Texas Memorial Museum";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Occasional Papers TTU";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Edinburgh Journal of Botany";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Faunitaxys";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Far Eastern entomologist";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Mammalian Species";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Dugesiana";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Current Science";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Fungal Diversity";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Selbyana";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Vestnik Zoologii";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Euscorpius";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Russian Entomological Journal";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Tropical Natural History";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Bonner zoologische Monographien";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Bonner Zoologische Beitraege";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Studies and Reports";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Onychium";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Acta Phytotaxonomica Sinica";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Journal of Systematics and Evolution";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Pacific Insects Monographs";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Shilap Revista de Lepidopterología";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Entomologica Fennica";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Illiesia";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Articulata";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Zoologische Verhandelingen";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/International Journal of Odonatology";
			$cache_dir =  "/Users/rpage/Dropbox/Development/bibscraper/pdfs/Unisanta Bioscience";
			
			$cache_dir =  "/Users/rpage/Dropbox/Development/bibscraper/working/Opuscula Zoologica Budapest/pdfs";

			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Tropical Lepidoptera Research";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Insecta Koreana";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Korea Science";
			
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Quaderno di studi e notizie di storia naturale della Romagna";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Holarctic Lepidoptera";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Fujian Journal of Agricultural Sciences";
			$cache_dir =  "/Volumes/Ultra Touch/pdfs/Quadrifina";
			
			$article_pdf_filename = $cache_dir . '/' . get_pdf_filename($reference->pdf);
			
			//if (file_exists($article_pdf_filename) && !$force)
			if (file_exists($article_pdf_filename))
			{
				echo "Have PDF $article_pdf_filename\n";
			}
			else
			{				
				$go_fetch = true;
				$go_fetch = false;
				
				if ($reference->issn == '2095-8137' )
				{
					$go_fetch = false;
				}	

				if ($reference->issn == '1852-6098' )
				{
					$go_fetch = false;
				}	
				
				if ($go_fetch)
				{
					$command = "curl -H \"User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.89 Safari/537.36\" -L --cookie -A \"Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:59.0) Gecko/20100101 Firefox/59.0\" --location '" . $reference->pdf . "' > '$article_pdf_filename'";
				
					//$command = "curl  -L -H \"Accept: */*\" --location '" . $reference->pdf . "' > '$article_pdf_filename'";
				
				
					$command = "wget '" . $reference->pdf . "' -O '$article_pdf_filename'";
					echo $command . "\n";
					system ($command);
					//exit();
				}
			}	
			
			// PDF sanity check
			$pdf_ok = true;
			
			$handle = fopen($article_pdf_filename, "rb");
   			$file_start = fread($handle, 1024);  //<<--- as per your need 
		   	fclose($handle);
		   	
		   	$pdf_ok = true;
		   	
		   	
		   	
		   	if (preg_match('/^\s*%PDF/', $file_start ))
		   	{
		   		$pdf_ok = true;
		   	}
		   	else
		   	{
		   		echo "*** Not a PDF ***\n";
		   		$pdf_ok = false;
		   	}
		   	
			
			if ($pdf_ok)
			{
				// Upload	
				$identifier = $id;		
			
				// if we've made something dark by mistake add a version flag so we can upload it
				//$identifier .= '-v2';			

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
							
				if (isset($reference->handle))
				{
					$headers[] = '"x-archive-meta-external-identifier:' . 'hdl:' . $reference->handle . '"';
				}

				if (isset($reference->issn))
				{
					$headers[] = '"x-archive-meta-issn:' . $reference->issn . '"';
				}
										
				// licensing
				//$headers[] = '"x-archive-meta-licenseurl:http://creativecommons.org/licenses/by-nc/3.0/"';

				// NIWA
				if ($reference->issn == '0083-7903')
				{
					$headers[] = '"x-archive-meta-licenseurl:http://creativecommons.org/licenses/by-nc-nd/3.0/"';
				}

				// Peckhamia
				if ($reference->issn == '2161-8526')
				{
					$headers[] = '"x-archive-meta-licenseurl:https://creativecommons.org/licenses/by-nd/4.0/"';
				}		

				// Basteria http://natuurtijdschriften.nl
				if ($reference->issn == '0005-6219')
				{
					$headers[] = '"x-archive-meta-licenseurl:http://creativecommons.org/licenses/by-nc/3.0/nl/"';
				}	
				// Recueil Des Travaux Botaniques Néerlandais
				if ($reference->issn == '0370-7504')
				{
					$headers[] = '"x-archive-meta-licenseurl:http://creativecommons.org/licenses/by-nc/3.0/nl/"';
				}					
				// Odonatologica cc by sa
				if ($reference->issn == '0375-0183')
				{
					$headers[] = '"x-archive-meta-licenseurl:http://creativecommons.org/licenses/by-sa/3.0/nl/"';
				}	
			
				// Swainsona is CC-BY
				if ($reference->issn == '2206-1649')
				{
					$headers[] = '"x-archive-meta-licenseurl:https://creativecommons.org/licenses/by/4.0/"';
				}	

				// Transactions of the Royal Society of New Zealand : Zoology is CC-BY
				if ($reference->issn == '0035-9181')
				{
					$headers[] = '"x-archive-meta-licenseurl:https://creativecommons.org/licenses/by/4.0/"';
				}	
				if ($reference->issn == '1176-6166')
				{
					$headers[] = '"x-archive-meta-licenseurl:https://creativecommons.org/licenses/by/4.0/"';
				}	
			
				// Caldasia is CC-BY
				if ($reference->issn == '2357-3759')
				{
					$headers[] = '"x-archive-meta-licenseurl:https://creativecommons.org/licenses/by/4.0"';
				}	
			
				// 1390-0129
				if ($reference->issn == '1390-0129')
				{
					$headers[] = '"x-archive-meta-licenseurl:https://creativecommons.org/licenses/by-nc-nd/4.0/"';
				}	
			
				// 2373-0951
				if ($reference->issn == '2373-0951')
				{
					$headers[] = '"x-archive-meta-licenseurl:https://creativecommons.org/licenses/by-nc-nd/1.0/"';
				}	

				// 2095-8137 Zoological Research
				if ($reference->issn == '2095-8137')
				{
					$headers[] = '"x-archive-meta-licenseurl:https://creativecommons.org/licenses/by/3.0/"';
				}	

				// 2337-876X Treubia
				if ($reference->issn == '2337-876X')
				{
					$headers[] = '"x-archive-meta-licenseurl:http://creativecommons.org/licenses/by-nc-sa/4.0/"';
				}	

				// 1851-7471 Revista de la Sociedad Entomológica Argentina
				if ($reference->issn == '1851-7471')
				{
					$headers[] = '"x-archive-meta-licenseurl:https://creativecommons.org/licenses/by/4.0/"';
				}	

				// 0920-1998
				if ($reference->issn == '0920-1998')
				{
					$headers[] = '"x-archive-meta-licenseurl:https://creativecommons.org/licenses/by/4.0/"';
				}	

				if ($reference->issn == '1852-6098')
				{
					$headers[] = '"x-archive-meta-licenseurl:http://creativecommons.org/licenses/by-nc-nd/4.0/"';
				}	
				
				if ($reference->issn =='0075-207X')
				{
					$headers[] = '"x-archive-meta-licenseurl:https://creativecommons.org/licenses/by-nc-sa/1.0/"';
				}	

				if ($reference->issn =='1474-0036')
				{
					$headers[] = '"x-archive-meta-licenseurl:https://creativecommons.org/licenses/by/4.0/"';
				}	

				if ($reference->issn =='2689-0682')
				{
					$headers[] = '"x-archive-meta-licenseurl:http://creativecommons.org/licenses/by-nc/4.0/"';
				}	

				if ($reference->issn =='1070-4140')
				{
					$headers[] = '"x-archive-meta-licenseurl:https://creativecommons.org/licenses/by-nc/4.0/"';
				}	

				if ($reference->issn =='1008-0384')
				{
					$headers[] = '"x-archive-meta-licenseurl:http://creativecommons.org/licenses/by/3.0/"';
				}	


				// authorisation
				$headers[] = '"authorization: LOW ' . $config['s3_access_key']. ':' . $config['s3_secret_key'] . '"';

				$headers[] = '"x-archive-meta-identifier:' . $identifier . '"';
	
				$url = 'http://s3.us.archive.org/' . $identifier . '/' . $identifier . '.pdf';
	
				print_r($headers);
				echo "$url\n";
			
				if (head($url) && !$force)
				{
					echo " PDF exists (HEAD returns 200)\n";
				
					$ai_sql .= 'UPDATE publications SET internetarchive="' . $identifier . '" WHERE pdf="' . $reference->pdf . '";' . "\n";

				}
				else
				{
					$command = 'curl --location';
					$command .= ' --header ' . join(' --header ', $headers);
					$command .= ' --upload-file \'' . $article_pdf_filename . '\'';
					$command .= ' http://s3.us.archive.org/' . $identifier . '/' . $identifier . '.pdf';

					echo $command . "\n";
				
					system ($command);
				
					//exit();
				
				
					if (1)
					{
						if (isset($reference->pdf))
						{
							$ai_sql .= 'UPDATE publications SET internetarchive="' . $identifier . '" WHERE pdf="' . $reference->pdf . '";' . "\n";
						}
					}
				
				
					if (0)
					{
						if (isset($reference->url))
						{
							$ai_sql .= 'UPDATE publications SET internetarchive="' . $identifier . '" WHERE url="' . $reference->url . '";' . "\n";
						}
					}
				
				
				}
			
				}
			}
			
			
		}
	}
}



//----------------------------------------------------------------------------------------


$force = false;
$force = true;

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

import_ris_file($filename, 'archive_import_ris');

echo $ai_sql;


?>