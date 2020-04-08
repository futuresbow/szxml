<?php
function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float) $usec + (float) $sec);
}
function cleanURL($str)
{
    $plattern = array(
        "ä",
        "Ä",
        "á",
        "Á",
        "é",
        "É",
        "í",
        "Í",
        "ó",
        "Ó",
        "ö",
        "Ö",
        "ő",
        "Ő",
        "ú",
        "Ú",
        "ü",
        "Ü",
        "ű",
        "Ű", //hu
        "Č",
        "č",
        "Ď",
        "ď",
        "Ĺ",
        "ĺ",
        "Ľ",
        "ľ",
        "Ň",
        "ň",
        "Ô",
        "ô",
        "Ŕ",
        "ŕ",
        "Š",
        "š",
        "Ť",
        "ť",
        "Ý",
        "ý",
        "Ž",
        "ž", //sk
        "Ě",
        "ě",
        "Ř",
        "ř",
        "Ů",
        "ů",
        "Æ",
        "æ",
        "Ø",
        "ø",
        "Å",
        "å",
        "Ć",
        "ć",
        "Đ",
        "đ",
        "Ñ",
        "ñ", //cz, dk,no, hr, es
        "Ă",
        "ă",
        "Â",
        "â",
        "Î",
        "î",
        "Α",
        "α",
        "Β",
        "β",
        "Γ",
        "γ",
        "Δ",
        "δ",
        "Ε",
        "ε",
        "Ζ",
        "ζ",
        "Η",
        "η",
        "Θ",
        "θ",
        "Κ",
        "κ",
        "Λ",
        "λ",
        "Μ",
        "μ",
        "Ν",
        "ν",
        "Ξ",
        "ξ",
        "Ο",
        "ο",
        "Π",
        "π",
        "Ρ",
        "ρ",
        "Σ",
        "σ",
        "Τ",
        "τ",
        "Υ",
        "υ",
        "Φ",
        "φ",
        "Χ",
        "χ",
        "Ψ",
        "ψ",
        "Ω",
        "ω"
    ); //gr
    
    $replacement = array(
        "a",
        "a",
        "a",
        "a",
        "e",
        "e",
        "i",
        "i",
        "o",
        "o",
        "o",
        "o",
        "o",
        "o",
        "u",
        "u",
        "u",
        "u",
        "u",
        "u", //hu
        "c",
        "c",
        "d",
        "d",
        "l",
        "l",
        "l",
        "l",
        "n",
        "n",
        "o",
        "o",
        "r",
        "r",
        "s",
        "s",
        "t",
        "t",
        "y",
        "y",
        "z",
        "z", //sk
        "e",
        "e",
        "r",
        "r",
        "u",
        "u",
        "a",
        "a",
        "o",
        "o",
        "a",
        "a",
        "c",
        "c",
        "d",
        "d",
        "n",
        "n", //cz, dk,no, hr, es
        "a",
        "a",
        "a",
        "a",
        "i",
        "i",
        "a",
        "a",
        "b",
        "b",
        "g",
        "g",
        "d",
        "d",
        "e",
        "e",
        "z",
        "z",
        "e",
        "e",
        "t",
        "t",
        "k",
        "k",
        "l",
        "l",
        "m",
        "m",
        "n",
        "n",
        "k",
        "k",
        "o",
        "o",
        "p",
        "p",
        "r",
        "r",
        "s",
        "s",
        "t",
        "t",
        "u",
        "u",
        "f",
        "f",
        "x",
        "x",
        "p",
        "p",
        "o",
        "o"
    ); //gr
    
    while (strstr($str, ".."))
        $str = str_replace("..", ".", $str);
    
    $str = SubStr(str_replace($plattern, $replacement, $str), 0, 1000);
    
    $str = StrToLower(preg_replace("/\W/", "-", $str));
    
    while (strstr($str, "--"))
        $str = str_replace("--", "-", $str);
    
    return $str;
}



function dbdate($str)
{
    return trim(str_replace('. ', '-', $str), '.');
}
function isEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}
function obStart()
{
    ob_start();
}
function obStop()
{
    $ret = ob_get_contents();
    ob_end_clean();
    return $ret;
}
function obend($kiiratas)
{
    $ret = obStop();
    if ($kiiratas)
        print $ret;
}

function osszesfile($dir, $szuro = '')
{
    print $dir;
    $fileok = array();
    if (is_dir($dir)) {
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                
                if ($file == '.' or $file == '..')
                    continue;
                
                $ex = explode('.', $file);
                $ex = end($ex);
                
                if ($szuro != '') {
                    if ($ex != $szuro)
                        continue;
                }
                
                $fileok[] = $file;
            }
        }
    }
    return $fileok;
}

function ext($str)
{
    $str = strtolower($str);
    $arr = explode('.', $str);
    return end($arr);
}
function woext($str)
{
    $str = strtolower($str);
    return str_replace('.' . ext($str), '', $str);
}

function strToUrl($str)
{
    
    return cleanURL($str);
}




function getj2date($source)
{
    $date = new DateTime($source);
    return $date->format('Y.m.d.');
}
function honapokArr()
{
    return array(
        '',
        'január',
        'február',
        'március',
        'április',
        'május',
        'június',
        'július',
        'augusztus',
        'szeptember',
        'október',
        'november',
        'december'
    );
}

function hkhonap($ho)
{
    $honapok = honapokArr();
    return $honapok((int) $ho);
}
function hkdatum($str)
{
    $time    = strtotime($str);
    $honapok = honapokArr();
    return date('Y. ', $time) . $honapok[(int) date('m', $time)] . ' ' . (int) date('d', $time) . '.';
}
function hkar($str)
{
    return number_format($str, 0, '', '.');
}

function hkwrap($str, $max = 700)
{
    
    $str = str_replace('</h1>', "</h1>\n", $str);
    $str = str_replace('</h2>', "</h2>\n", $str);
    $str = str_replace('</h3>', "</h3>\n", $str);
    $str = str_replace('<p></p>', "\n", $str);
    $a   = explode('|', wordwrap(strip_tags($str), $max, '|'));
    return nl2br($a[0]) . (trim(@$a[1] != '') ? ' ...' : '');
}
function naplo($str, $id = 0)
{
    $CI =& get_instance();
    $sessvar = $CI->session->userdata('loggendin_user');
    $tag     = $sessvar['tag'];
    if (!isset($tag->id)) {
        $tag       = new stdClass();
        $tag->id   = 0;
        $tag->nick = 'AZONOSÍTATTLAN FELHASZNÁLÓ';
    }
    $a = array(
        'tagid' => $tag->id,
        'nick' => $tag->nick,
        'txt' => $str,
        'tablaid' => $id
    );
    $CI->db->insert('hk_log', $a);
}
function hklog($str, $felhasznaloid = false)
{
    $CI =& get_instance();
    if (!$CI->session->has_userdata('hklog_userid')) {
        
        $CI->load->library('user_agent');
        if ($CI->agent->is_browser()) {
            $agent = $CI->agent->browser() . ' ' . $CI->agent->version();
        } elseif ($CI->agent->is_robot()) {
            $agent = $CI->agent->robot();
        } elseif ($CI->agent->is_mobile()) {
            $agent = $CI->agent->mobile();
        } else {
            $agent = 'Ismeretlen eszköz';
        }
        $a = array(
            'browser' => $agent . ' ' . $CI->agent->platform(),
            'ip' => $CI->input->ip_address()
        );
        
        $userid = $CI->Hotelkupon_model->mentes($a, 'hk_userlog_user', 'id');
        $CI->session->set_userdata('hklog_userid', $userid);
    }
    $a = array(
        'userid' => $CI->session->userdata('hklog_userid'),
        'feladat' => $str
    );
    
    $CI->Hotelkupon_model->mentes($a, 'hk_userlog', 'id');
    if ($felhasznaloid !== false) {
        $a = array(
            'felhasznaloid' => $felhasznaloid,
            'id' => $CI->session->userdata('hklog_userid')
        );
        $CI->Hotelkupon_model->modositas($a, 'hk_userlog_user', 'id');
    }
}
function hk_menulink($csoport, $ertek)
{
    $CI =& get_instance();
    $szuro = $CI->Szuro_model;
    $p     = (isset($_GET['aklista'])) ? '&aklista=1' : '';
    return $szuro->getSzuroLink($csoport, $ertek) . $p;
}
function hk_ischecked($csoport, $ertek)
{
    $CI =& get_instance();
    $szuro = $CI->Szuro_model;
    
    return $szuro->pipa($csoport, $ertek);
}
function hk_menuCount($csoport, $ertek)
{
    $CI =& get_instance();
    $szuro = $CI->Szuro_model;
    
    return $szuro->getDarabszam($csoport, $ertek);
}
function hkmero($kulcs)
{
    $CI =& get_instance();
    $CI->benchmark->mark($kulcs);
    print $kulcs . ': ' . $CI->benchmark->elapsed_time('code_start', $kulcs) . '<br>';
}
function deleteDir($dirPath)
{
    
    if (!is_dir($dirPath)) {
        return false;
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            deleteDir($file);
        } else {
            unlink($file);
        }
    }
    rmdir($dirPath);
}
function beallitasOlvasas($kulcs)
{
    $CI =& get_instance();
    $rs = $CI->db->where(array(
        'kulcs = ' => $kulcs
    ));
    $rs = $CI->db->get('hk_beallitas')->result();
    if (!isset($rs[0]->id)) {
        return false;
    }
    if ($rs[0]->tipus != 'tömb') {
        return trim($rs[0]->ertek);
    } else {
        
        $strArr = explode("\n", $rs[0]->ertek);
        if (strpos($rs[0]->ertek, '=>') !== false) {
            $arr = array();
            foreach ($strArr as $sor) {
                $sor = explode('=>', $sor);
                if (isset($sor[1])) {
                    $arr[trim($sor[0])] = trim($sor[1]);
                } else {
                    $arr[] = trim($sor[0]);
                }
            }
            return $arr;
        }
        
        return $strArr;
    }
}
// tábla adatok csv-be
function tabletocsv($tabla, $mezok = null, $sqlPlussz = '')
{
    $CI =& get_instance();
    $out = '';
    
    $sql = "SELECT  * FROM $tabla  $sqlPlussz";
    $rs  = $CI->db->query($sql)->result();
    if (!empty($rs)) {
        
        if(empty($mezok)) {
			$mezok = Array();
			foreach((array)$rs[0] as $k => $v) $mezok[] = $k;
			
		}
		$mezok = (array_flip($mezok));
		
		$sorArr = array();
		foreach($mezok as $k => $v) $sorArr[] =  '"'.$k.'"';
		$out .= implode(';', $sorArr)."\n";
		foreach($rs as  $sor) {
			$sorArr = array();
			foreach($mezok as $k => $v) $sorArr[] =  '"'.$sor->{$k}.'"';
			$out .= implode(';', $sorArr)."\n";
		}
		
    }
    
    return ($out);
}
// reccopy


function recurse_copy($src, $dst)
{
    global $copylevel;
    // teszteléshezs
    
    $dir = opendir($src);
    @mkdir($dst);
    while (false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..')) {
            if (is_dir($src . '/' . $file)) {
                recurse_copy($src . '/' . $file, $dst . '/' . $file);
            } else {
                print($src . '/' . $file . ' => ' . $dst . '/' . $file . '<br>');
                copy($src . '/' . $file,$dst . '/' . $file); 
                
            }
        }
    }
    closedir($dir);
}

