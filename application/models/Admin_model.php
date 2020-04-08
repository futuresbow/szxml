<?php 



class Admin_model extends CI_Model  {
   
    
    
  function getAktivSzallasAjanlatok() {
		$sql = "SELECT * FROM ajanlat a, szallas sz WHERE a.szallas_szallasid = sz.szallasid AND 
			statuszid = 1 AND ervenyesseg >= '".date('Y-m-d')."' ORDER BY szallasnev ASC";
		
		return $this->db->query($sql)->result();
		
	}
	function keres($tabla, $where) {
		$rs = $this->db->query("SELECT * FROM $tabla WHERE $where ")->result();
		if(!empty($rs[0])) return $rs;
		return false;
	}
   function tagellenorzes($user, $pwd) { 
		$this->db->where(array('nick' => $user, 'jelszo' => md5($pwd)));
		$rs = $this->db->get('hk_tagok')->result();
		
		if(!isset($rs[0]->szerep_id)) return false ;
		if($rs[0]->copy!=0) $rs = $this->db->query("SELECT * FROM hk_tagok WHERE id = ".$rs[0]->copy)->result();
		
		
		$sql = "SELECT * FROM hk_szerepek WHERE id = ".$rs[0]->szerep_id;
		$szereprs = $this->db->query($sql)->result();
		if(!isset($szereprs[0]->id)) return false ;
	
		return array('tag' => $rs[0], 'szerep' => $szereprs[0]);
    }
   
   function getmenubyurl($url) {
		$sql = "SELECT * FROM hk_menu WHERE url = '$url' ";
		$rs = $this->db->query($sql)->result();
		if(isset($rs[0]->id)) {
			return $rs[0];
		} else {
			return false;
		}
    } 
    function getmenuk() {
		$sql = "SELECT * FROM hk_menu ORDER BY sorrend ASC ";
		return $this->db->query($sql)->result();
    }
    function getleirasok($tipus, $kereses) {
		if($tipus) $w = " v.tartalomtipusid = $tipus AND "; else $w = ' 1 = 1 AND ';
		if($kereses) $w .= " v.cim LIKE '$kereses%' AND "; 
		$sql = "SELECT v.*, t.tipusnev FROM hk_tartalomtipus t, hk_tartalom v  WHERE $w v.tartalomtipusid = t.id ORDER BY tipusnev ASC ";
	return $this->db->query($sql)->result();
    }
    function getleirastipusok() {
		$sql = "SELECT * FROM hk_tartalomtipus t  ORDER BY tipusnev ASC ";
		return $this->db->query($sql)->result();
    }
    function getleirasbyid($id) {
		$id = (int)$id;
		$sql = "SELECT v.*, t.tipusnev FROM hk_tartalomtipus t, hk_tartalom v  WHERE v.tartalomtipusid = t.id AND v.id = $id LIMIT 1 ";
		$rs = $this->db->query($sql)->result();
		if(isset($rs[0]->id)) return $rs[0];
		return false;
    }
    function getszallasok($w = '') {
		$sql = "SELECT * FROM szallas WHERE szallasjellegid = 0 AND kikapcsolva = 0  $w ORDER BY szallasnev ASC";
		$rs = $this->db->query($sql)->result();
		if(isset($rs[0]->szallasid)) return $rs;
		return false;
	}
	function getSzallasokAjanlatok( $statusz = '', $w = '') {
		$sz = $this->getszallasok($w);
		$ret = array();
		if(!empty($sz)) foreach($sz as $k => $sor) {
			$sor = (array)$sor;
			$sor['ajanlatok'] = $this->getSzallasAjanlatokBySzallasid($sor['szallasid'], $statusz);
			$ret[] = $sor;
		
		}
		
		return $ret;
	}
	
	function getSzallasokAjanlatokArchiv( $statusz = '', $w = '') {
		$sz = $this->getszallasok($w);
		$ret = array();
		if(!empty($sz)) foreach($sz as $k => $sor) {
			$sor = (array)$sor;
			$sor['ajanlatok'] = $this->getSzallasAjanlatokBySzallasidArchiv($sor['szallasid'], $statusz);
			if(!empty($sor['ajanlatok'])) $ret[] = $sor;
		
		}
		
		return $ret;
	}
	
	function getSzallasAjanlatokBySzallasid($id, $statusz = '') {
		//$sql = "SELECT * FROM ajanlat a, szallas sz WHERE a.szallas_szallasid = sz.szallasid AND szallas_szallasid = $id ORDER BY aktivalasdatuma DESC";
		$w = '';
		if($statusz != '') $w = " a.statusz = '$statusz' AND ";
		if($statusz == 'aktív' ) $w .= " lejart = 0 AND ";
		$sql = "SELECT * FROM ajanlat a, szallas sz WHERE $w a.szallas_szallasid = sz.szallasid AND szallas_szallasid = $id ORDER BY ervenyesseg DESC";
		$rs = $this->db->query($sql)->result();
		
		if(isset($rs[0]->ajanlatid)) return $rs;
		return false;
	}
	
	function getSzallasAjanlatokBySzallasidArchiv($id, $statusz = '') {
		//$sql = "SELECT * FROM ajanlat a, szallas sz WHERE a.szallas_szallasid = sz.szallasid AND szallas_szallasid = $id ORDER BY aktivalasdatuma DESC";
		$w = '';
		if($statusz != '') $w = " a.statusz = '$statusz' AND ";
		if($statusz == 'aktív' ) $w .= " lejart = 0 AND ";
		$sql = "SELECT * FROM ajanlat_archiv a, szallas sz WHERE $w a.szallas_szallasid = sz.szallasid AND szallas_szallasid = $id ORDER BY ervenyesseg DESC";
		$rs = $this->db->query($sql)->result();
		
		if(isset($rs[0]->ajanlatid)) return $rs;
		return false;
	}
	
	function getSzallasAjanlatByAjanlatid($id) {
		$sql = "SELECT * FROM ajanlat a, szallas sz WHERE a.szallas_szallasid = sz.szallasid AND ajanlatid = $id ";
		$rs = $this->db->query($sql)->result('SzallasAjanlat');
		
		if(isset($rs[0]->ajanlatid)) return $rs[0];
		return false;
	}
	
    function getszallas($id) {
		$sql = "SELECT * FROM szallas WHERE szallasid = $id";
		$rs = $this->db->query($sql)->result();
		if(isset($rs[0]->szallasid)) return $rs[0];
		return false;
	}
    
    // általános függvények
    function getByStr($id, $tabla, $idStr, $resultclass = '') {
		
		if(strpos($id, '"')===false or strpos($id, "'")===false ) $id = "'".$id."'";
		
		$sql = "SELECT * FROM $tabla WHERE $idStr LIKE $id";
		
		if($resultclass!='') 
			$rs = $this->db->query($sql)->result($resultclass);
		else 
			$rs = $this->db->query($sql)->result();
		
		if(isset($rs[0]->$idStr)) return $rs[0];
		return false;
		
	}
    function get($id, $tabla, $idStr, $resultclass = '') {
		if(!is_numeric($id)) {
			if(strpos($id, '"')===false or strpos($id, "'")===false ) $id = "'".$id."'";
		}
		$sql = "SELECT * FROM $tabla WHERE $idStr = $id";
		
		if($resultclass!='') 
			$rs = $this->db->query($sql)->result($resultclass);
		else 
			$rs = $this->db->query($sql)->result();
		
		if(isset($rs[0]->$idStr)) return $rs[0];
		return false;
	}
    function gets($tabla, $sqlplussz, $resultclass = '') {
		$sql = "SELECT * FROM $tabla ".$sqlplussz;
		
		
		if($resultclass!='') 
			$rs = $this->db->query($sql)->result($resultclass);
		else 
			$rs = $this->db->query($sql)->result();
		
		
		if(isset($rs[0])) return $rs;
		return false;
	}
    function getsCount($tabla, $sqlplussz) {
		$sql = "SELECT count(*) as ossz FROM $tabla ".$sqlplussz;
		$rs = $this->db->query($sql)->result();
		
		if(isset($rs[0])) return $rs[0]->ossz;
		return false;
	}
	public function mentes($adat, $tabla, $id = 'id') {
		
		if($id!=='false') if(isset($adat[$id])) unset($adat[$id]);
		$this->db->insert( $tabla,$adat);
		return $this->db->insert_id();
    }
    public function modositas($adat, $tabla, $idStr = 'id') {
		$adat = (array)$adat;
		$id = $adat[$idStr];
		unset($adat[$idStr]);
		
		$this->db->where($idStr, $id);
		
		$this->db->update($tabla,$adat );
		return $this->db->affected_rows();
    }
    public function torles($id,$tabla,$idmezo = 'id') {
		$sql = "DELETE FROM $tabla WHERE $idmezo = '$id' LIMIT 1";
		$this->db->query($sql);
	}
    public function torlesOsszes($tabla) {
		$sql = "TRUNCATE TABLE $tabla ";
		$this->db->query($sql);
	}
	function sqlSorok($sql, $class = false) { 
		if($class==false) {
			return $this->db->query($sql)->result();
		} else {
			return $this->db->query($sql)->result($class);
		}
		
	}
	function sqlSor($sql) { 
		$rs = $this->db->query($sql)->result();
		if(isset($rs[0])) return $rs[0];
		return false;
	}
	function sqlMezo($sql, $mezo) {
		$rs = $this->sqlSor($sql);
		if(isset($rs->{$mezo})) return $rs->{$mezo};
		return false;
	}
	
	function leirasKepek($leirasid) {
		$sql = "SELECT x.id as xid, k.id as kid, k.*, x.* FROM hk_leiraskep k, hk_leirasxkep x WHERE 
			x.leirasid = $leirasid AND x.kepid = k.id ORDER BY x.sorrend ASC
		";
		$rs = $this->db->query($sql)->result();
		if(isset($rs[0]->id)) return $rs;
		return false;
	}
	function getOsszeskepOpciok() {
		$sql = "
		SELECT sz.szallasnev,  k.id as kid, k.* FROM hk_kep k, hk_szallasxkep x, szallas sz  
			WHERE
			 sz.szallasid = x.szallasid
			 AND x.kepid = k.id 
			ORDER BY sz.szallasnev ASC
		";
		return $this->db->query($sql)->result();
	}
	
	function szallasKepek($szallasid) {
		$sql = "
		SELECT k.id as kid,x.id as xid, k.*, x.* FROM hk_kep k, hk_szallasxkep x  
			WHERE
				x.szallasid = $szallasid AND 
				x.kepid = k.id 
			
			ORDER BY x.sorrend ASC
		";
		return $this->db->query($sql)->result();
	}
	function telepulesKepek($id) {
		$sql = "
		SELECT k.id as kid,x.id as xid, k.*, x.* FROM hk_kep k, hk_telepulesxkep x  
			WHERE
				x.telepulesid = $id AND 
				x.kepid = k.id 
			
			ORDER BY x.sorrend ASC
		";
		return $this->db->query($sql)->result();
	}
	function ajanlatKepek($ajanlatid) {
		$sql = "
		SELECT k.id as kid,x.id as xid, k.*, x.* FROM hk_ajanlatkep k, hk_ajanlatxkep x  
			WHERE
				x.ajanlatid = $ajanlatid AND 
				x.kepid = k.id 
			
			ORDER BY x.sorrend ASC
		";
		//print $sql;
		return $this->db->query($sql)->result();
	}
	function utazasKepek($ajanlatid) {
		$sql = "
		SELECT k.id as kid,x.id as xid, k.*, x.* FROM hk_ajanlatkep k, hk_ajanlatxkep x  
			WHERE
				x.ajanlatid = $ajanlatid AND 
				x.kepid = k.id 
			
			ORDER BY x.sorrend ASC
		";
		return $this->db->query($sql)->result();
	}
	
	function kepatiras($szallasid) {
		$sql = "UPDATE hk_szallasxkep SET szallasid = $szallasid WHERE szallasid = 0";
		$this->db->query($sql);
		$sql = "UPDATE hk_kep SET dir = $szallasid WHERE dir = 0";
		$this->db->query($sql);
		rename(HKPATH.'img/szallas/orig/0',HKPATH.'img/szallas/orig/'.$szallasid);
		rename(HKPATH.'img/szallas/0',HKPATH.'img/szallas/'.$szallasid);
		deleteDir (HKPATH.'img/szallas/orig/0');
		deleteDir (HKPATH.'img/szallas/0');
		
	}
	function telepuleskepatiras($id) {
		
		$sql = "UPDATE hk_telepulesxkep SET telepulesid = $id WHERE telepulesid = 0";
		$this->db->query($sql);
		$sql = "UPDATE hk_kep SET dir = $id WHERE dir = 0";
		$this->db->query($sql);
		
		rename(HKPATH.'img/telepules/orig/0',HKPATH.'img/telepules/orig/'.$id);
		rename(HKPATH.'img/telepules/0',HKPATH.'img/telepules/'.$id);
		deleteDir (HKPATH.'img/telepules/orig/0');
		deleteDir (HKPATH.'img/telepules/0');
		
	}
	function ajanlatkepatiras($ajanlatid) {
		
		$sql = "DELETE FROM hk_ajanlatxkep WHERE ajanlatid = $ajanlatid ";
		$this->db->query($sql);
		$sql = "DELETE FROM  hk_ajanlatkep WHERE dir = $ajanlatid ";
		$this->db->query($sql);
		
		$sql = "UPDATE hk_ajanlatxkep SET ajanlatid = $ajanlatid WHERE ajanlatid = 0";
		$this->db->query($sql);
		$sql = "UPDATE hk_ajanlatkep SET dir = $ajanlatid WHERE dir = 0";
		$this->db->query($sql);
		rename(HKPATH.'img/ajanlat/orig/0',HKPATH.'img/ajanlat/orig/'.$ajanlatid);
		rename(HKPATH.'img/ajanlat/0',HKPATH.'img/ajanlat/'.$ajanlatid);
		deleteDir (HKPATH.'img/ajanlat/orig/0');
		deleteDir (HKPATH.'img/ajanlat/0');
	}
	function telepukeskepatiras($telepulesid) {
		
		$sql = "UPDATE  hk_telepulesxkep SET telepulesid = $telepulesid WHERE telepulesid = 0";
		$this->db->query($sql);
		$sql = "UPDATE hk_kep SET dir = $telepulesid WHERE dir = 0";
		$this->db->query($sql);
		rename(HKPATH.'img/telepules/orig/0',HKPATH.'img/telepules/orig/'.$telepulesid);
		rename(HKPATH.'img/telepules/0',HKPATH.'img/telepules/'.$telepulesid);
		deleteDir (HKPATH.'img/telepules/orig/0');
		deleteDir (HKPATH.'img/telepules/0');
	}
	function leiraskepatiras($leirasid) {
		$sql = "UPDATE hk_leirasxkep SET leirasid = $leirasid WHERE leirasid = 0";
		$this->db->query($sql);
		$sql = "UPDATE hk_leiraskep SET dir = $leirasid WHERE dir = 0";
		$this->db->query($sql);
		rename(HKPATH.'img/leiras/orig/0',HKPATH.'img/leiras/orig/'.$leirasid);
		rename(HKPATH.'img/leiras/0',HKPATH.'img/leiras/'.$leirasid);
		deleteDir (HKPATH.'img/leiras/orig/0');
		deleteDir (HKPATH.'img/leiras/0');
	}
	function getsIdArr($tabla, $kulcs, $sqlplussz) {
		$lista = $this->gets($tabla, $sqlplussz);
		if(empty($lista)) return false;
		$ret = array();
		foreach($lista as $l) {
			$ret[$l->$kulcs] = $l;
		}
		return $ret;
	}
	function getHirlevelValasztasok($szerep) {
		$sql = "SELECT * FROM hk_levelajanlat WHERE szerep = '$szerep'";
		$rs = $this->db->query($sql)->result_array();
		
		if(empty($rs)) return '';
		$ret = array();
		foreach($rs as $sor) $ret[] = $sor['ajanlatid'];
		
		return implode(',', $ret); 
	}
	public function hirlevelgeneralasmentes($targy, $file, $html, $teszt = 0, $kikuldes = 0 ) {
		$adat = array(
	    'targy' => $targy,
	    'file' => $file,
	    'html' => $html,
	    'teszt' => $teszt,
	    'allapot' => $kikuldes==0?'generálás':'kiküldés',
	    'ido' => date('Y-m-d H:i')
		);
		$this->db->insert('hk_hirlevelek', $adat);
		return $this->db->insert_id();
    }
    public function getutolsogeneraltlevelfile($teszt = 0) {
		$sql = "UPDATE hk_hirlevelek SET allapot = 'kikuldes', teszt = '".$teszt."' WHERE adatbazis = 'Hotelkupon' ORDER BY id DESC LIMIT 1";
		$this->db->query($sql);
		$sql = "SELECT  * FROM hk_hirlevelek WHERE adatbazis = 'Hotelkupon' ORDER BY ido DESC LIMIT 1";
		$talalat = $this->db->query($sql)->result();
		return $talalat[0];
    }
    function mentesHaNincs($a, $tabla, $where) {
		$rs = $this->db->query("SELECT * FROM $tabla WHERE $where")->result();
		if(empty($rs)) {
			$this->db->insert($tabla, $a);
		}
	} 
	
	function tablaadat($a, $tabla, $idStr) {
		$rs = $this->db->query("SELECT * FROM $tabla WHERE $idStr = '".$a[$idStr]."'")->result();
		if(empty($rs)) {
			$this->db->insert($tabla, $a);
		} else {
			$this->modositas($a, $tabla, $idStr);
		}
	} 
	
    function torlesHaVan($tabla, $where, $id = 'id') {
		$rs = $this->db->query("SELECT * FROM $tabla WHERE $where")->result();
		if(!empty($rs)) {
			foreach($rs as $sor)
			$this->torles($sor->$id, $tabla, $id);
		}
	}
	function egyedi($tabla, $mezo, $sqlWhere = '') {
		$rs = $this->db->query("SELECT DISTINCT($mezo) FROM $tabla $sqlWhere")->result();
		if(!empty($rs)) return $rs;
		return false;
	}
	function letezik( $tabla, $sqlplussz) {
		$res = $this->gets($tabla, $sqlplussz);
		if(isset($res[0])) return $res[0];
		return false;
	}
}
