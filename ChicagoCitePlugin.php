<?php
/// This simple plugin is based on some very helpful code and instructions provided on
/// the Omeka forum by sheepeeh (http://omeka.org/forums/topic/change-citation-style)
///
/// The plugin will check the "Item Type" metadata element within Omeka's Zotero array,
/// and (according to the Chicago Manual of Style rules pertaining to that item type)
/// generate Chicago citations by pulling from the Zotero metadata associated with the
/// Omeka item.
///
/// Currently, this document will detect the following item types:
/// Book, Book Section, Journal Article, Newspaper Article, Webpage, Thesis, Document
///
/// This plugin was originally created to serve the needs of New York University
/// Libraries' "Jewish Peddler Project" (http://jewishpeddler.org)
///
/// Last updated: Feb. 23, 2015
///
/// Stephen Balogh, 2015 | stephen.balogh@nyu.edu

class ChicagoCitePlugin extends Omeka_Plugin_AbstractPlugin
{
	protected $_filters = array('item_citation');

	public function filterItemCitation($citation, $args)
	{

/// BEGINNING OF DOCUMENT TYPE CHECKER
		$document = strip_formatting(metadata('item', array('Zotero', 'Item Type')));
		if ($document) {
			switch ($document) {
			
// BEGIN BOOK			
				case 'Book':


		$citation = '';

		$authors = metadata('item', array('Zotero', 'Author'), array('all' => true));
	/// Strip formatting and remove empty creator elements.
		$authors = array_filter(array_map('strip_formatting', $authors));
		if ($authors) {
			switch (count($authors)) {
				case 1:
				$author = $authors[0];
				break;
				case 2:
	/// Chicago-style item citation: two authors
				$author = __('%1$s and %2$s', $authors[0], $authors[1]);
				break;
				case 3:
	/// Chicago-style item citation: three authors
				$author = __('%1$s, %2$s, and %3$s', $authors[0], $authors[1], $authors[2]);
				break;
				default:
	/// Chicago-style item citation: more than three authors
				$author = __('%s et al.', $authors[0]);
			}
			$citation .= "$author. ";
		} else {
			$citation .= "Author Unknown. ";
		}
	
	/// Note that the following (title) portion is drawing from the DC array, not Zotero!

		$title = strip_formatting(metadata('item', array('Dublin Core', 'Title')));
		if ($title) {
			$citation .= "<i>$title</i> (";
		}

		$place = metadata('item', array('Zotero', 'Place'), array('all' => true));
		$place = array_filter(array_map('strip_formatting', $place));
		if ($place) {
			switch (count($place)) {
				case 1:
				$place = $place[0];
				break;
				case 2:
	/// two fields in publishing metadata –– publisher then location
				$place = __('%2$s and %1$s', $place[0], $place[1]);
				break;
			
			}
			$citation .= "$place: ";
		} else {
			$citation .= "";
	}
	
		$publisher = metadata('item', array('Zotero', 'Publisher'), array('all' => true));
		$publisher = array_filter(array_map('strip_formatting', $publisher));
		if ($publisher) {
			switch (count($publisher)) {
				case 1:
				$publisher = $publisher[0];
				break;
				case 2:
	/// two fields in publishing metadata –– publisher then location
				$publisher = __('%1$s and %2$s', $publisher[0], $publisher[1]);
				break;
			
			}
			$citation .= "$publisher, ";
		} else {
			$citation .= "Unknown, ";
	}


		$date = metadata('item', array('Zotero', 'Date'), array('all' => true));
		$date = array_filter(array_map('strip_formatting', $date));
		if ($date) {
			switch (count($date)) {
				case 1:
				$date = $date[0];
				break;
			}
			$citation .= "$date).";
		} else {
			$citation .= "Unknown Date).";
	}

	///	$accessed = format_date(time(), Zend_Date::DATE_LONG);
	///	$url = html_escape(record_url('item', null, true));
	/// Chicago-style item citation: access date and URL
	///	$citation .= __('accessed %1$s, %2$s.', $accessed, $url);
		

break;

// END BOOK
//
// BEGIN JOURNAL ARTICLE

case 'Journal Article':

$citation = '';

		$authors = metadata('item', array('Zotero', 'Author'), array('all' => true));
	/// Strip formatting and remove empty author elements.
		$authors = array_filter(array_map('strip_formatting', $authors));
		if ($authors) {
			switch (count($authors)) {
				case 1:
				$author = $authors[0];
				break;
				case 2:
	/// Chicago-style item citation: two authors
				$author = __('%1$s and %2$s', $authors[0], $authors[1]);
				break;
				case 3:
	/// Chicago-style item citation: three authors
				$author = __('%1$s, %2$s, and %3$s', $authors[0], $authors[1], $authors[2]);
				break;
				default:
	/// Chicago-style item citation: more than three authors
				$creator = __('%s et al.', $authors[0]);
			}
			$citation .= "$author. ";
		} else {
			$citation .= "Author Unknown. ";
		}

				$title = strip_formatting(metadata('item', array('Dublin Core', 'Title')));
		if ($title) {
			$citation .= "“".$title.".” ";
		}

		$publication = metadata('item', array('Zotero', 'Publication Title'), array('all' => true));
		$publication = array_filter(array_map('strip_formatting', $publication));
		if ($publication) {
			switch (count($publication)) {
				case 1:
				$publication = __('<i>%1$s</i>', $publication[0]);
				break;
			}
			$citation .= "$publication ";
		} else {
			$citation .= "<i>Publication Title Missing</i> ";
	}
	
		$volume = metadata('item', array('Zotero', 'Volume'), array('all' => true));
		$volume = array_filter(array_map('strip_formatting', $volume));
		if ($volume) {
		switch (count($volume)) {
				case 1:
				$volume = __('%1$s', $volume[0]);
				break;
			}
			$citation .= " $volume ";
		} else {
			$citation .= "";
	}
	
		$issue = metadata('item', array('Zotero', 'Issue'), array('all' => true));
		$issue = array_filter(array_map('strip_formatting', $issue));
		if ($issue) {
			switch (count($issue)) {
				case 1:
				$issue = __('%1$s', $issue[0]);
				break;
			}
			$citation .= " $issue ";
		} else {
			$citation .= "";
	}


		$date = metadata('item', array('Zotero', 'Date'), array('all' => true));
		$date = array_filter(array_map('strip_formatting', $date));
		if ($date) {
			switch (count($date)) {
				case 1:
				$date = $date[0];
				break;
			}
			$citation .= "($date)";
		} else {
			$citation .= "(Unknown Date)";
	}

			$pagerange = strip_formatting(metadata('item', array('Zotero', 'Pages')));
		if ($pagerange) {
			$citation .= ": $pagerange.";
		}
		else {
			$citation .= ".";
}

		$DOI = strip_formatting(metadata('item', array('Zotero', 'DOI')));
		if ($DOI) {
			$citation .= " doi: $DOI.";
		}



break;

// END JOURNAL ARTICLE
//
// BEGIN BOOK SECTION

case 'Book Section':

$citation = '';

		$authors = metadata('item', array('Zotero', 'Author'), array('all' => true));
	/// Strip formatting and remove empty author elements.
		$authors = array_filter(array_map('strip_formatting', $authors));
		if ($authors) {
			switch (count($authors)) {
				case 1:
				$author = $authors[0];
				break;
				case 2:
	/// Chicago-style item citation: two authors
				$author = __('%1$s and %2$s', $authors[0], $authors[1]);
				break;
				case 3:
	/// Chicago-style item citation: three authors
				$author = __('%1$s, %2$s, and %3$s', $authors[0], $authors[1], $authors[2]);
				break;
				default:
	/// Chicago-style item citation: more than three authors
				$creator = __('%s et al.', $authors[0]);
			}
			$citation .= "$author. ";
		} else {
			$citation .= "Author Unknown. ";
		}

				$title = strip_formatting(metadata('item', array('Dublin Core', 'Title')));
		if ($title) {
			$citation .= "“".$title.".” ";
		}

		$publication = metadata('item', array('Zotero', 'Publication Title'), array('all' => true));
		$publication = array_filter(array_map('strip_formatting', $publication));
		if ($publication) {
			switch (count($publication)) {
				case 1:
				$publication = __('<i>%1$s</i>', $publication[0]);
				break;
			}
			$citation .= "In $publication";
		} else {
			$citation .= "<i>Publication Title Missing</i> ";
	}
	
		$editors = metadata('item', array('Zotero', 'Editor'), array('all' => true));
		$editors = array_filter(array_map('strip_formatting', $editors));
		if ($editors) {
			switch (count($editors)) {
				case 1:
				$editor = __('%1$s', $editors[0]);
				break;
				case 2:
				$editor = __('%1$s and %2$s', $editors[0], $editors[1]);
				break;
				case 3:
				$editor = __('%1$s, %2$s, and %3$s', $editors[0], $editors[1], $editors[2]);
				break;
				default:
				case 4:
				$editor = __('%1$s, %2$s, %3$s, and %4$s', $editors[0], $editors[1], $editors[2], $editors[3]);
				break;
				default:
				$editor = __('%s et al.', $editors[0]);
			}
			$citation .= ", edited by $editor";
		} else {
			$citation .= "";
	}
	
	$pagerange = strip_formatting(metadata('item', array('Zotero', 'Pages')));
		if ($pagerange) {
			$citation .= ", $pagerange.";
		}
		else {
			$citation .= ".";
}

		$place = metadata('item', array('Zotero', 'Place'), array('all' => true));
		$place = array_filter(array_map('strip_formatting', $place));
		if ($place) {
			switch (count($place)) {
				case 1:
				$place = $place[0];
				break;
				case 2:
	/// two fields in publishing metadata –– publisher then location
				$place = __('%2$s and %1$s', $place[0], $place[1]);
				break;
			
			}
			$citation .= " $place: ";
		} else {
			$citation .= "";
	}
	
		$publisher = metadata('item', array('Zotero', 'Publisher'), array('all' => true));
		$publisher = array_filter(array_map('strip_formatting', $publisher));
		if ($publisher) {
			switch (count($publisher)) {
				case 1:
				$publisher = $publisher[0];
				break;
				case 2:
	/// two fields in publishing metadata –– publisher then location
				$publisher = __('%2$s, %1$s', $publisher[0], $publisher[1]);
				break;
			
			}
			$citation .= "$publisher, ";
		} else {
			$citation .= "Unknown, ";
	}


		$date = metadata('item', array('Zotero', 'Date'), array('all' => true));
		$date = array_filter(array_map('strip_formatting', $date));
		if ($date) {
			switch (count($date)) {
				case 1:
				$date = $date[0];
				break;
			}
			$citation .= "$date.";
		} else {
			$citation .= "Unknown Date.";
	}


break;

// END BOOK SECTION / CHAPTER
//
// BEGIN DOCUMENT

case 'Document':

$citation = '';

		$authors = metadata('item', array('Zotero', 'Author'), array('all' => true));
	/// Strip formatting and remove empty author elements.
		$authors = array_filter(array_map('strip_formatting', $authors));
		if ($authors) {
			switch (count($authors)) {
				case 1:
				$author = $authors[0];
				break;
				case 2:
	/// Chicago-style item citation: two authors
				$author = __('%1$s and %2$s', $authors[0], $authors[1]);
				break;
				case 3:
	/// Chicago-style item citation: three authors
				$author = __('%1$s, %2$s, and %3$s', $authors[0], $authors[1], $authors[2]);
				break;
				default:
	/// Chicago-style item citation: more than three authors
				$creator = __('%s et al.', $authors[0]);
			}
			$citation .= "$author: ";
		} else {
			$citation .= "";
		}

				$title = strip_formatting(metadata('item', array('Dublin Core', 'Title')));
		if ($title) {
			$citation .= "$title";
		}

		$date = metadata('item', array('Zotero', 'Date'), array('all' => true));
		$date = array_filter(array_map('strip_formatting', $date));
		if ($date) {
			switch (count($date)) {
				case 1:
				$date = $date[0];
				break;
			}
			$citation .= ", $date.";
		} else {
			$citation .= ".";
	}

		$arcloc = metadata('item', array('Zotero', 'Archive Location'), array('all' => true));
		$arcloc = array_filter(array_map('strip_formatting', $arcloc));
		if ($arcloc) {
			switch (count($arcloc)) {
				case 1:
				$arcloc = $arcloc[0];
				break;
				case 2:
	/// two fields in publishing metadata –– publisher then location
				$arcloc = __('%2$s and %1$s', $arcloc[0], $arcloc[1]);
				break;
			
			}
			$citation .= " $arcloc.";
		} else {
			$citation .= "";
	}
	

break;

// END DOCUMENT
//
// BEGIN NEWSPAPER ARTICLE

case 'Newspaper Article':

$citation = '';

		$authors = metadata('item', array('Zotero', 'Author'), array('all' => true));
	/// Strip formatting and remove empty author elements.
		$authors = array_filter(array_map('strip_formatting', $authors));
		if ($authors) {
			switch (count($authors)) {
				case 1:
				$author = $authors[0];
				break;
				case 2:
	/// Chicago-style item citation: two authors
				$author = __('%1$s and %2$s', $authors[0], $authors[1]);
				break;
				case 3:
	/// Chicago-style item citation: three authors
				$author = __('%1$s, %2$s, and %3$s', $authors[0], $authors[1], $authors[2]);
				break;
				default:
	/// Chicago-style item citation: more than three authors
				$creator = __('%s et al.', $authors[0]);
			}
			$citation .= "$author. ";
		} else {
			$citation .= "Author Unknown. ";
		}

				$title = strip_formatting(metadata('item', array('Dublin Core', 'Title')));
		if ($title) {
			$citation .= "“".$title.".” ";
		}

		$publication = metadata('item', array('Zotero', 'Publication Title'), array('all' => true));
		$publication = array_filter(array_map('strip_formatting', $publication));
		if ($publication) {
			switch (count($publication)) {
				case 1:
				$publication = __('<i>%1$s</i>', $publication[0]);
				break;
			}
			$citation .= "$publication";
		} else {
			$citation .= "<i>Publication Title Missing</i>";
	}
	
		
		$date = metadata('item', array('Zotero', 'Date'), array('all' => true));
		$date = array_filter(array_map('strip_formatting', $date));
		if ($date) {
			switch (count($date)) {
				case 1:
				$date = $date[0];
				break;
			}
			$citation .= ", ($date)";
		} else {
			$citation .= ", (Unknown Date)";
	}

			$pagerange = strip_formatting(metadata('item', array('Zotero', 'Pages')));
		if ($pagerange) {
			$citation .= ": $pagerange";
		}
		else {
			$citation .= "";
}

			$url = strip_formatting(metadata('item', array('Zotero', 'URL')));
		if ($url) {
			$citation .= ", <a href=\"$url\">$url</a>";
		}
		else {
			$citation .= ".";
}

break;

// END NEWSPAPER ARTICLE
//
// BEGIN THESIS

case 'Thesis':

$citation = '';

		$authors = metadata('item', array('Zotero', 'Author'), array('all' => true));
	/// Strip formatting and remove empty author elements.
		$authors = array_filter(array_map('strip_formatting', $authors));
		if ($authors) {
			switch (count($authors)) {
				case 1:
				$author = $authors[0];
				break;
				case 2:
	/// Chicago-style item citation: two authors
				$author = __('%1$s and %2$s', $authors[0], $authors[1]);
				break;
				case 3:
	/// Chicago-style item citation: three authors
				$author = __('%1$s, %2$s, and %3$s', $authors[0], $authors[1], $authors[2]);
				break;
				default:
	/// Chicago-style item citation: more than three authors
				$creator = __('%s et al.', $authors[0]);
			}
			$citation .= "$author. ";
		} else {
			$citation .= "Author Unknown. ";
		}

				$title = strip_formatting(metadata('item', array('Dublin Core', 'Title')));
		if ($title) {
			$citation .= "“".$title.".” ";
		}

	
		$publisher = metadata('item', array('Zotero', 'Publisher'), array('all' => true));
		$publisher = array_filter(array_map('strip_formatting', $publisher));
		$place = strip_formatting(metadata('item', array('Zotero', 'Place')));
		if ($publisher) {
			switch (count($publisher)) {
				case 1:
				$publisher = $publisher[0];
				break;
				case 2:
	/// two fields in publishing metadata –– publisher then location
				$publisher = __('%2$s, %1$s', $publisher[0], $publisher[1]);
				break;
			
			}
			$citation .= "Thesis, $publisher, ";
		} elseif ($place) {
			$citation .= "Thesis, $place, ";
		} else {
			$citation .= "Thesis, Unknown Institution, ";
	}


		$date = metadata('item', array('Zotero', 'Date'), array('all' => true));
		$date = array_filter(array_map('strip_formatting', $date));
		if ($date) {
			switch (count($date)) {
				case 1:
				$date = $date[0];
				break;
			}
			$citation .= "$date.";
		} else {
			$citation .= "Unknown Date.";
	}


break; 

// END THESIS
//
// BEGIN WEBPAGE

case 'Webpage':

$citation = '';

		$publication = metadata('item', array('Zotero', 'Publication Title'), array('all' => true));
		$publication = array_filter(array_map('strip_formatting', $publication));
		if ($publication) {
			switch (count($publication)) {
				case 1:
				$publication = __('<i>%1$s</i>', $publication[0]);
				break;
			}
			$citation .= "$publication.";
		} else {
			$citation .= "<i>Unnamed Web Collection.</i> ";
	}
	
			$title = strip_formatting(metadata('item', array('Dublin Core', 'Title')));
		if ($title) {
			$citation .= " “".$title.".” ";
		}

		
		$dateacc = metadata('item', array('Zotero', 'Access Date'), array('all' => true));
		$date = array_filter(array_map('strip_formatting', $dateacc));
		if ($date) {
			switch (count($dateacc)) {
				case 1:
				$dateacc = $dateacc[0];
				break;
			}
			$citation .= "Accessed $dateacc. ";
		} else {
			$citation .= "";
	}

			$url = strip_formatting(metadata('item', array('Zotero', 'URL')));
		if ($url) {
			$citation .= "<a href=\"$url\">$url</a>";
		}
		else {
			$citation .= "";
}

break;

// END WEBPAGE

}
}
else {
$citation = "Document type unrecognized.";
}
return $citation;

	}
}