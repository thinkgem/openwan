<?php
// $Id: attachment.php 895 2010-03-23 05:36:29Z thinkgem $
/*
	[Discuz!] (C)2001-2009 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	attachment.func.php 17535 2009-01-20 05:12:20Z monkey
*/
class Helper_Attachment{

	static function attachtype($type, $returnval = 'html') {

		static $attachicons = array(
			1 => 'unknown.gif',
			2 => 'binary.gif',
			3 => 'zip.gif',
			4 => 'rar.gif',
			5 => 'msoffice.gif',
			6 => 'text.gif',
			7 => 'html.gif',
			8 => 'real.gif',
			9 => 'av.gif',
			10 => 'flash.gif',
			11 => 'image.gif',
			12 => 'pdf.gif',
			13 => 'torrent.gif'
		);

		if(is_numeric($type)) {
			$typeid = $type;
		} else {
			if(preg_match("/bittorrent|^torrent\t/", $type)) {
				$typeid = 13;
			} elseif(preg_match("/pdf|^pdf\t/", $type)) {
				$typeid = 12;
			} elseif(preg_match("/image|^(jpg|gif|png|bmp)\t/", $type)) {
				$typeid = 11;
			} elseif(preg_match("/flash|^(swf|fla|swi)\t/", $type)) {
				$typeid = 10;
			} elseif(preg_match("/audio|video|^(wav|mid|mp3|m3u|wma|asf|asx|vqf|mpg|mpeg|avi|wmv)\t/", $type)) {
				$typeid = 9;
			} elseif(preg_match("/real|^(ra|rm|rv)\t/", $type)) {
				$typeid = 8;
			} elseif(preg_match("/htm|^(php|js|pl|cgi|asp)\t/", $type)) {
				$typeid = 7;
			} elseif(preg_match("/text|^(txt|rtf|wri|chm)\t/", $type)) {
				$typeid = 6;
			} elseif(preg_match("/word|powerpoint|^(doc|ppt)\t/", $type)) {
				$typeid = 5;
			} elseif(preg_match("/^rar\t/", $type)) {
				$typeid = 4;
			} elseif(preg_match("/compressed|^(zip|arj|arc|cab|lzh|lha|tar|gz)\t/", $type)) {
				$typeid = 3;
			} elseif(preg_match("/octet-stream|^(exe|com|bat|dll)\t/", $type)) {
				$typeid = 2;
			} elseif($type) {
				$typeid = 1;
			} else {
				$typeid = 0;
			}
		}
		if($returnval == 'html') {
			return '<img src="images/attachicons/'.$attachicons[$typeid].'" border="0" class="absmiddle" alt="" />';
		} elseif($returnval == 'id') {
			return $typeid;
		}
	}

	static function sizecount($filesize) {
		if($filesize >= 1073741824) {
			$filesize = round($filesize / 1073741824 * 100) / 100 . ' GB';
		} elseif($filesize >= 1048576) {
			$filesize = round($filesize / 1048576 * 100) / 100 . ' MB';
		} elseif($filesize >= 1024) {
			$filesize = round($filesize / 1024 * 100) / 100 . ' KB';
		} else {
			$filesize = $filesize . ' Bytes';
		}
		return $filesize;
	}

	static function parseattach($attachpids, $attachtags, &$postlist, $showimages = 1, $skipaids = array()) {
		global $db, $tablepre, $discuz_uid, $readaccess, $attachlist, $attachimgpost, $maxchargespan, $timestamp, $forum, $ftp, $attachurl, $dateformat, $timeformat, $timeoffset, $hideattach, $thread, $tradesaids, $trades, $exthtml, $tagstatus, $sid, $authkey;

		$query = $db->query("SELECT a.*, ap.aid AS payed FROM {$tablepre}attachments a LEFT JOIN {$tablepre}attachpaymentlog ap ON ap.aid=a.aid AND ap.uid='$discuz_uid' WHERE a.pid IN ($attachpids)");

		$sidauth = rawurlencode(authcode($sid, 'ENCODE', $authkey));
		$attachexists = FALSE;
		while($attach = $db->fetch_array($query)) {
			$attachexists = TRUE;
			$exthtml = '';
			if($skipaids && in_array($attach['aid'], $skipaids)) {
				continue;
			}
			$attached = 0;
			$extension = strtolower(fileext($attach['filename']));
			$attach['ext'] = $extension;
			$attach['attachicon'] = attachtype($extension."\t".$attach['filetype']);
			$attach['attachsize'] = sizecount($attach['filesize']);
			$attach['attachimg'] = $showimages && $attachimgpost && $attach['isimage'] && (!$attach['readperm'] || $readaccess >= $attach['readperm']) ? 1 : 0;
			if($attach['price']) {
				if($maxchargespan && $timestamp - $attach['dateline'] >= $maxchargespan * 3600) {
					$db->query("UPDATE {$tablepre}attachments SET price='0' WHERE aid='$attach[aid]'");
					$attach['price'] = 0;
				} else {
					if(!$discuz_uid || (!$forum['ismoderator'] && $attach['uid'] != $discuz_uid && !$attach['payed'])) {
						$attach['unpayed'] = 1;
					}
				}
			}
			$attach['payed'] = $attach['payed'] || $forum['ismoderator'] || $attach['uid'] == $discuz_uid ? 1 : 0;
			$attach['url'] = $attach['remote'] ? $ftp['attachurl'] : $attachurl;
			$attach['dateline'] = dgmdate("$dateformat $timeformat", $attach['dateline'] + $timeoffset * 3600);
			$postlist[$attach['pid']]['attachments'][$attach['aid']] = $attach;
			if(is_array($attachtags[$attach['pid']]) && in_array($attach['aid'], $attachtags[$attach['pid']])) {
				$findattach[$attach['pid']][] = "/\[attach\]$attach[aid]\[\/attach\]/i";
				$replaceattach[$attach['pid']][] = $hideattach[$attach['pid']] ? '[attach]***[/attach]' : attachtag($attach['pid'], $attach['aid'], $postlist, $sidauth);
				$attached = 1;
			}

			if(!$attached || $attach['unpayed']) {
				if($attach['isimage']) {
					$postlist[$attach['pid']]['imagelist'] .= attachlist($attach, $sidauth);
				} else {
					$postlist[$attach['pid']]['attachlist'] .= attachlist($attach, $sidauth);
				}
			}
		}

		if($attachexists) {
			foreach($attachtags as $pid => $aids) {
				if($findattach[$pid]) {
					$postlist[$pid]['message'] = preg_replace($findattach[$pid], $replaceattach[$pid], $postlist[$pid]['message'], 1);
					$postlist[$pid]['message'] = preg_replace($findattach[$pid], '', $postlist[$pid]['message']);
				}
			}
		} else {
			$db->query("UPDATE {$tablepre}posts SET attachment='0' WHERE pid IN ($attachpids)", 'UNBUFFERED');
		}
	}

	static function attachwidth($width) {
		$imagemaxwidth = intval(IMAGEMAXWIDTH);
		if($imagemaxwidth && $width) {
			if(substr(IMAGEMAXWIDTH, -1, 1) != '%') {
				$s = 'width="'.($width > $imagemaxwidth ? $imagemaxwidth : $width).'" onclick="zoom(this, this.src)"';
			} else {
				$s = 'thumbImg="1"';
			}
		} else {
			$s = 'thumbImg="1"';
		}
		return $s;
	}
}