<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_szamla extends CI_Controller {

	var $utazasirodak = array('508' => 'Utazom.com', '504' => 'Real', '461' => 'Budavár', '473' => 'Fairy');
	var $utazastipusok = array('U' => 'Utazom.com', 'R' => 'Real', 'B' => 'Budavár', 'F' => 'Fairy');
	var $kepSzel = 687;
	var $kepMag = 458;
    var $tn_kepSzel = 300;
	var $tn_kepMag = 200;

	public function __construct()
	{
		parent::__construct();


		// helperek
		$this->load->library('session');

		$this->load->model('Admin_model');
		$this->load->helper('Hotelkupon_helper');

		
		$this->data=array();
		$this->data['cropmod'] = array(
			6 => 'Közép vágása',0 => 'Keretezett',1 => 'Átméretezés', 2 => 'Bal felső vágás',
			3 => 'Felső közép vágás',4 => 'Felső bal vágás', 5 => 'Közép bal vágás',
			7 => 'Közép jobb vágás', 8 => 'Alsó bal vágás',
			9 => 'Alsó közép vágás',10 => 'Alsó jobb vágás'
		);

		
		/*array('fp' => 'Fekete péntek',
						'ka' => 'Karácsony',
						'sz' => 'Szilveszter',
						'lm' => 'Last Minute',
						'ex' => 'Exkluzív',
						'nya' => 'Nyári ajánlat',
						'pu' => 'Pünkösdi ajánlat',
		);
		*/

       $this->data['headerScript'] = "";
		$this->template = 'admin/adm_view_template' ;
		
	}
	
	public function index() {
		
		
		if(isset($_GET['letolt'])) {
			$file = $this->Admin_model->get((int)$_GET['letolt'], "hk_konyveles_szamlak", 'id');
			header('Content-disposition: attachment; filename="'.basename($file->file).'"');
			header('Content-Type: application/xml; charset=utf-8');
			print file_get_contents($file->file);
			return;
		}
		if(isset($_GET['kuldes'])) {
			
			print 'OKOK';
			$file = $this->Admin_model->get((int)$_GET['kuldes'], "hk_konyveles_szamlak", 'id');
			
			
			
			$ci = get_instance();
			$ci->load->library('email');
			$config['charset'] = "utf-8";
			$config['mailtype'] = "html";
			$config['newline'] = "\r\n";
			$config['useragent'] = "CodeIgniter";
			
			$config['protocol'] = 'smtp';
			$config['smtp_host'] = 'mail.zente.org';
			$config['smtp_user'] = 'sendmail@zente.org';
			$config['smtp_pass'] = 'send100MAIL';
			$config['smtp_port'] = 25;
			
			$ci->email->initialize($config);

			$ci->email->from('ivan@zente.org', 'Hotelkupon Számlák');
			//$list = array('cpinfo.laci@gmail.com','ivan@napiakciok.hu', 'cegledi.ivan74@gmail.com', 'konyveles@szakma96.hu');
			//$list = array('cegledi.ivan74@gmail.com', 'konyveles@szakma96.hu');
			$list = array('cegledi.ivan74@gmail.com');
			$ci->email->to($list);
			$this->email->reply_to('ivan@cpinfo.hu', 'Hotelkupon Számlahiba');
			$ci->email->subject('hotelkupon.hu aktuális számlák - '.date('Y-d.m H:i'));
			$ci->email->message("Szia Kati,<br><br>csatolva küldjük az aktuális számlát.<br>Ez egy autómatikusan generált levél, probléma esetén keresd Ivánt vagy Lacit: <a href=\"mailto:ivan@napiakciok.hu\">ivan@napiakciok.hu</a>, <a href=\"mailto:laci@napiakciok.hu\">laci@napiakciok.hu</a><br><br>Szép napot kívánunk:<br>Hotelkupon.hu ");

			$ci->email->attach( $file->file);
			$ci->email->send();
			
			//redirect(base_url().'admin/konyveles?kuldes_sikeres');
			return;
			
			
			
			
			
			$this->load->library('Levelkuldo');


			$l = new Levelkuldo();
			$l->level = "Szia Kati,<br><br>csatolva küldjük az aktuális számlát.<br>Ez egy autómatikusan generált levél, probléma esetén keresd Ivánt vagy Lacit: <a href=\"mailto:ivan@napiakciok.hu\">ivan@napiakciok.hu</a>, <a href=\"mailto:laci@napiakciok.hu\">laci@napiakciok.hu</a><br><br>Szép napot kívánunk:<br>Hotelkupon.hu ";

			
			$att[] = $file->file;
		
			$l->csatolmanyok = $att;

			$l->kinek = 'cegledi.ivan74@gmail.com';
			$l->targy = 'hotelkupon.hu aktuális számlák - '.date('Y-d.m H:i');
			$l->kuldes();
			redirect('admin/konyveles?kuldes_sikeres');
			return;
		}
		
		if(isset($_POST["submit"])) {
				//CSV kiterjesztés ellenőrzése
				if(pathinfo($_FILES['szamlaexport']['name'], PATHINFO_EXTENSION) == 'csv'){
					$target = FCPATH.'tmp/'.$_FILES['szamlaexport']['name'];
					$hova = FCPATH.'tmp/szamla_'.date('Y-m-d-H-i').'.xml';

					move_uploaded_file($_FILES['szamlaexport']['tmp_name'], $target);
					$szamlaexport 	= new Szamlaexport();
					$szamlak 		= $szamlaexport->loadCsv($target);
					
					$sz			= $szamlaexport->saveHessynXml($szamlak, $hova);
					
					//Töröljük a feltöltött fájlt a visszaélések elkerülése miatt
					//unlink($target);
					//exit;
				}
				//Nem CSV a feltöltött fájl
				else{
					header('Content-Type: text/html; charset=utf-8');
					print "Nem CSV fájl került feltöltésre! <br> <a href='index.html'>vissza a feltöltő felületre</a>";
				}
			}
			//Nem form-on keresztül hívták be az oldalt
			else{
				header('Content-Type: text/html; charset=utf-8');
				//print "Ez az oldal közvetlenül nem hívható meg!";
			}
			$this->data['lap'] = 'konyveles';
			$this->data['lista'] = $this->Admin_model->gets("hk_konyveles_szamlak", " ORDER BY ido DESC");
			$this->load->view($this->template, $this->data);
		
	}
	public function login()
	{
		$user = $this->input->get_post('felhasznalonev');
		$pwd = $this->input->get_post('jelszo');
		naplo('Login próbálkozás'.$this->input->get_post('felhasznalonev') );
		$tag = $this->Admin_model->tagellenorzes($user, $pwd);

		if($tag != false) {
			$this->session->set_userdata('loggendin_user', $tag);
			$this->legujabb();
			redirect('/admin/index', 'refresh');
			return;
		}
		$this->load->view('admin/login_view',$this->data);
	}

	public function belepesEllenorzo($url = '') {

		if(!$this->session->has_userdata('loggendin_user')) {
			redirect('/admin/login', 'refresh');
		}

		// szerepkör?
		$szerep = $this->Admin_model->getmenubyurl($url);
		$tag = $this->session->userdata('loggendin_user');
		
		if($tag['szerep']->rang < $szerep->rang) {
			$sql = "SELECT * FROM hk_menu WHERE rang < ".$tag['szerep']->rang;

			$rs = $this->db->query($sql)->result();
			if(isset($rs[0]->url)) {
				redirect('/admin/'.$rs[0]->url, 'refresh');
			}
			else {
				redirect('/admin/udvozlo', 'refresh');
			}
		}
	}
	public function kilepes() {

		// kiléptet, session törlés
		naplo('KILÉPÉS');

		$this->session->unset_userdata('loggendin_user');
		redirect('/admin/login', 'refresh');

	}
}




Class HessynSzamla {
	private $szamlaSzam;
	private $szamlaTipus;
	private $szamlaKelte;
	private $szamlaTeljesitesIdopontja;
	private $szamlaFizetesiHatarido;
	private $szamlaFizetesiMod;
	private $szamlaDevizanem;
	private $szamlaVevoNeve;
	private $szamlaVevoIranyitoszama;
	private $szamlaVevoVaros;
	private $szamlaVevoutca;
	private $szamlaNettoOsszesen;
	private $szamlaAfaOsszesen;
	private $szamlaBruttoOsszesen;
	private $szamlaTermekVSzolgaltatas;
	private $szamlaMennyiseg;
	private $szamlaMennyisegiegyseg;
	private $szamlaNettoegysegar;
	private $szamlaAfaKulcs;
	private $szamlaTetelNettEertek;
	private $szamlaTetelafaertek;
	private $szamlaTetelBruttoertek;
	private $szamlaPenzforgalmis;
	private $szamlatetelTartozik;
	private $szamlatetelKovetel;
	private $szamlatetelAfaTartozik;
	private $szamlatetelAfaKovetel;
	private $szamlaTetelFizAfaNev;
	private $szamlaAfaTipus;
	private $szamlaElolegTipus;
	private $szamlaKNaplo;
	private $szamlaAfaGen;

	//Hessyn számla tulajdonságainak tömbösítése és visszaadása
	public function getSzamlaElemek(){
		return array_keys(get_class_vars(__CLASS__));
	}

	//számlaadtok betöltése
	public function populate($szamlaTetelek){
		// törzsadatok
		$szamla = $szamlaTetelek[0];
		
		
		$this->szamlaSzam				= $szamla->{'Számlaszám'};
		$this->szamlaTipus				= $szamla->{'Számla típus'};
		$this->szamlaKelte				= $szamla->{'Számla kelte'};
		$this->szamlaTeljesitesIdopontja 	= $szamla->{'Telj. időpontja'};
		$this->szamlaFizetesiHatarido		= $szamla->{'Fiz. határidő'};
		$this->szamlaFizetesiMod			= $szamla->{'Fizetés módja'};
		$this->szamlaDevizanem			= $szamla->{'Devizanem'};
		if($this->szamlaDevizanem == 'Ft'){
			$this->szamlaDevizanem		= 'HUF';
		}
		$this->szamlaVevoNeve			= $szamla->{'Vevő neve'};
		$this->szamlaVevoIranyitoszama	= $szamla->{'Vevő irsz.'};
		$this->szamlaVevoVaros			= $szamla->{'Vevő város'};
		$this->szamlaVevoutca			= $szamla->{'Vevő utca'};
		$NettoÖsszesen = $AfaOsszesen = $BruttoOsszesen = 0;
		
		$NettoÖsszesen = $szamla->{'Nettó összesen'};
		$AfaOsszesen = $szamla->{'Áfa összesen'};
		$BruttoOsszesen = $szamla->{'Bruttó összesen'};
		
		
		$this->szamlaNettoOsszesen		= $NettoÖsszesen;
		$this->szamlaAfaOsszesen		= $AfaOsszesen;
		$this->szamlaBruttoOsszesen		= $BruttoOsszesen;
		
		$this->tetel = array();
		foreach($szamlaTetelek as $tetel) {
			$obj = new stdClass;
			$obj->szamlaTermekVSzolgaltatas	= $szamla->{'Termék,szolgáltatás'};
			$obj->szamlaMennyiseg			= $szamla->{'Mennyiség'};
			$obj->szamlaMennyisegiegyseg		= $szamla->{'Mennyiségi egység'};
			$obj->szamlaNettoegysegar		= $szamla->{'Nettó egységár'};
			$obj->szamlaAfaKulcs			= $szamla->{'Áfakulcs'};
			$obj->szamlaTetelNettoErtek		= $szamla->{'Tétel nettó érték'};
			$obj->szamlaTetelAfaErtek		= $szamla->{'Tétel áfa érték'};
			$obj->szamlaTetelBruttoertek		= $szamla->{'Tétel bruttó érték'};
			$this->tetel[] = $obj;
	    }
		//Hessyn 
		$this->szamlaPenzforgalmis		= 'NEM';
		$this->szamlaTetelTartozik		= '311';
		$this->szamlaTetelKovetel		= '922';
		$this->szamlaTetelAfaTartozik		= '311';
		$this->szamlaTetelAfaKovetel		= '467';
		$this->szamlaAfaTipus			= '501';
		$this->szamlaElolegTipus			= '0';
		$this->szamlaKNaplo				= 'K1';
		$this->szamlaAfaGen				= 'NEM';
		$this->szamlaTetelFizAfaNev		= 'Fizetendő ÁFA';
		
		
	}

	public function generateXmlDoc($cegAdoszam){
		$implementation 			= new \DOMImplementation();
		$dtd 					= $implementation->createDocumentType('FELADAS', '', 'WINKETTOS.DTD');
		$doc 					= $implementation->createDocument('', '', $dtd);
		//$doc->encoding 			= 'UTF-8';
		$doc->encoding 			= 'ISO-8859-2';
		$doc->formatOutput 			= true;
		$doc->preserveWhiteSpace		= false;
		$root 					= $doc->createElement('ROOT', null);
		$doc->appendChild($root);
		$ceg 					= $doc->createElement('CEG', null);
		$adoszam					= $doc->createAttribute('adoszam');
		$adoszam->value			= $cegAdoszam;
		$root->appendChild($ceg);
		$ceg->appendChild($adoszam);
		$szamlak					= $doc->createElement('SZAMLAK', null);
		$root->appendChild($szamlak);

		return $doc;
	}

	public function getXmlSzamla($doc){
		//Csak normál számlákat dologzunk fel
		if($this->szamlaTipus == 'SZ'){
			//Számla elem létrehozása
			$szamla				= $doc->createElement('SZAMLA');
			//Számlaszám attribútum létrehozása
			$szamlaSzam			= $doc->createAttribute('SZAMLASZAM');
			//Számlaszám attribútum értékének megadása
			$szamlaSzam->value		= $this->szamlaSzam;
			//Számlaszám attribútum hozzáadása a számla elemhez
			$szamla->appendChild($szamlaSzam);

			$szamlaKelte			= $doc->createAttribute('KELTE');
			$szamlaKelte->value		= $this->szamlaKelte;
			//addChild helyett appendChild-ot kell hasznánlni, mert nem létrhozunk, hanem hozzáfűzünk elemet
			$szamla->appendChild($szamlaKelte);

			$szamlaTeljesitesIdopontja		= $doc->createAttribute('TELJESITES');
			$szamlaTeljesitesIdopontja->value	= $this->szamlaTeljesitesIdopontja;
			$szamla->appendChild($szamlaTeljesitesIdopontja);

			$szamlaFizetesiHatarido			= $doc->createAttribute('FIZETESHAT');
			$szamlaFizetesiHatarido->value	= $this->szamlaFizetesiHatarido;
			$szamla->appendChild($szamlaFizetesiHatarido);

			$szamlaAfaDatum		= $doc->createAttribute('AFADATUM');
			$szamlaAfaDatum->value	= $this->szamlaTeljesitesIdopontja;
			$szamla->appendChild($szamlaAfaDatum);

			$szamlaFizetesiMod		= $doc->createAttribute('FIZMOD');

			if($this->szamlaFizetesiMod == 'Átutalás'){
				$this->szamlaFizetesiMod = 'AT';
			}
			elseif($this->szamlaFizetesiMod == 'Befizetés bankfiókban'){
				$this->szamlaFizetesiMod = 'AT';
			}
			elseif($this->szamlaFizetesiMod == 'Bankkártyás fizetés'){
				$this->szamlaFizetesiMod = 'BK';
			}
			else{
				$this->szamlaFizetesiMod = 'E';
			}

			$szamlaFizetesiMod->value	= $this->szamlaFizetesiMod;
			$szamla->appendChild($szamlaFizetesiMod);

			$szamlaDevizanem			= $doc->createAttribute('DEVIZA');
			$szamlaDevizanem->value		= $this->szamlaDevizanem;
			$szamla->appendChild($szamlaDevizanem);

			$szamlaNettoOsszesen		= $doc->createAttribute('NETTO');
			$szamlaNettoOsszesen->value	= $this->szamlaNettoOsszesen;
			$szamla->appendChild($szamlaNettoOsszesen);

			$szamlaAfaOsszesen			= $doc->createAttribute('AFA');
			$szamlaAfaOsszesen->value	= $this->szamlaAfaOsszesen;
			$szamla->appendChild($szamlaAfaOsszesen);

			$szamlaBruttoOsszesen		= $doc->createAttribute('BRUTTO');
			$szamlaBruttoOsszesen->value	= $this->szamlaBruttoOsszesen;
			$szamla->appendChild($szamlaBruttoOsszesen);

			$szamlaKNaplo				= $doc->createAttribute('KNAPLO');
			$szamlaKNaplo->value		= $this->szamlaKNaplo;
			$szamla->appendchild($szamlaKNaplo);

			$szamlaPenzforgalmis		= $doc->createAttribute('PENZFORGALMIS');
			$szamlaPenzforgalmis->value	= $this->szamlaPenzforgalmis;
			$szamla->appendChild($szamlaPenzforgalmis);

			$szamlaAfaGen				= $doc->createAttribute('AFAGEN');
			$szamlaAfaGen->value		= $this->szamlaAfaGen;
			$szamla->appendChild($szamlaAfaGen);

			//vevő elem
			$vevo					= $doc->createElement('PARTNER');
			$szamlaVevoNeve			= $doc->createAttribute('NEV');
			
			// & jel, aposztróf
			$this->szamlaVevoNeve = str_replace('&', '', $this->szamlaVevoNeve);
			$this->szamlaVevoNeve = str_replace("'", '', $this->szamlaVevoNeve);
			$this->szamlaVevoNeve = str_replace('"', '', $this->szamlaVevoNeve);
			
			$szamlaVevoNeve->value		= $this->szamlaVevoNeve;
			$vevo->appendChild($szamlaVevoNeve);
			$szamla->appendChild($vevo);

			//Számlatétel
			$huf = 0;
			$afa = 0;
			
			foreach($this->tetel as $tetel) {
				
				$szamlaTetel				= $doc->createElement('SZTETEL');
				$szamlaArfolyam			= $doc->createAttribute('ARFOLYAM');
				$szamlaArfolyam->value		= number_format('1',3,'.','');
				$szamlaTetel->appendChild($szamlaArfolyam);

				$szamlaTetelDev			= $doc->createAttribute('DEV');
				$szamlaTetelDev->value		= number_format($tetel->szamlaTetelNettoErtek,2,'.','');
				$szamlaTetel->appendChild($szamlaTetelDev);
				$huf += $tetel->szamlaTetelNettoErtek;
				$szamlaTetelHuf			= $doc->createAttribute('HUF');
				$szamlaTetelHuf->value		= number_format($tetel->szamlaTetelNettoErtek,2,'.','');
				$szamlaTetel->appendChild($szamlaTetelHuf);

				$szamlaTermekVSzolgaltatas		= $doc->createAttribute('SZOVEG');
				$szamlaTermekVSzolgaltatas->value	= $tetel->szamlaTermekVSzolgaltatas;
				$szamlaTetel->appendChild($szamlaTermekVSzolgaltatas);

				$szamlaTetelTartozik		= $doc->createAttribute('TARTOZIK');
				$szamlaTetelTartozik->value	= $this->szamlaTetelTartozik;
				$szamlaTetel->appendChild($szamlaTetelTartozik);

				$szamlaTetelKovetel			= $doc->createAttribute('KOVETEL');
				$szamlaTetelKovetel->value	= $this->szamlaTetelKovetel;
				$szamlaTetel->appendChild($szamlaTetelKovetel);

				$szamla->appendChild($szamlaTetel);

				//Számlatétel ÁFA
				$szamlaTetelA			= $doc->createElement('SZTETEL', '');
				$szamlaArfolyamA		= $doc->createAttribute('ARFOLYAM');
				$szamlaArfolyamA->value	= number_format('1',3,'.','');
				$szamlaTetelA->appendChild($szamlaArfolyamA);

				$szamlaTetelDevA		= $doc->createAttribute('DEV');
				$szamlaTetelDevA->value	= number_format($tetel->szamlaTetelAfaErtek,2,'.','');
				$szamlaTetelA->appendChild($szamlaTetelDevA);
				$afa += $tetel->szamlaTetelAfaErtek;
				$szamlaTetelHufA		= $doc->createAttribute('HUF');
				$szamlaTetelHufA->value	= number_format($tetel->szamlaTetelAfaErtek,2,'.','');
				$szamlaTetelA->appendChild($szamlaTetelHufA);

				$szamlaTetelTermekA			= $doc->createAttribute('SZOVEG');
				$szamlaTetelTermekA->value	= $this->szamlaTetelFizAfaNev;
				$szamlaTetelA->appendChild($szamlaTetelTermekA);

				$szamlaTetelTartozikA		= $doc->createAttribute('TARTOZIK');
				$szamlaTetelTartozikA->value	= $this->szamlaTetelAfaTartozik;
				$szamlaTetelA->appendChild($szamlaTetelTartozikA);

				$szamlaTetelKovetelA		= $doc->createAttribute('KOVETEL');
				$szamlaTetelKovetelA->value	= $this->szamlaTetelAfaKovetel;
				$szamlaTetelA->appendChild($szamlaTetelKovetelA);

				$szamla->appendChild($szamlaTetelA);
			}
			
			//ÁFA elem
			$afa					= $doc->createElement('AFA');
			$afaDevAlap			= $doc->createAttribute('DEVALAP');
			$afaDevAlap->value		= number_format($this->szamlaNettoOsszesen,2,'.','');
			$afa->appendChild($afaDevAlap);

			$afaDevAfa			= $doc->createAttribute('DEVAFAERTEK');
			$afaDevAfa->value		= number_format($this->szamlaAfaOsszesen,2,'.','');
			$afa->appendChild($afaDevAfa);

			$afaHufAlap			= $doc->createAttribute('HUFALAP');
			$afaHufAlap->value		= $this->szamlaNettoOsszesen;
			$afa->appendChild($afaHufAlap);

			$afaHufAfa			= $doc->createAttribute('HUFAFAERTEK');
			$afaHufAfa->value		= $this->szamlaAfaOsszesen;
			$afa->appendChild($afaHufAfa);

			$afaKulcs				= $doc->createAttribute('AFAKULCS');
			$afaKulcs->value		= $this->szamlaAfaKulcs;
			$afa->appendChild($afaKulcs);

			$afaTipus				= $doc->createAttribute('AFATIPUS');
			$afaTipus->value		= $this->szamlaAfaTipus;
			$afa->appendChild($afaTipus);

			$afaElolegTipus		= $doc->createAttribute('ELOLEGTIPUS');
			$afaElolegTipus->value	= $this->szamlaElolegTipus;
			$afa->appendChild($afaElolegTipus);

			$szamla->appendChild($afa);

			//Számlák elem kiolvasása a függvénybe beküldött xml dom dokumentumból
			$szamlak				= $doc->getElementsByTagName('SZAMLAK')->item(0);

			//Betesszük a számlák elembe az elkészült számlát
			$szamlak->appendChild($szamla);
		}
	}

	public function postXmlProcess($doc){
		//konvertálás iso-8859-2-be
		$charConv 	= iconv('UTF-8', 'ISO-8859-2', $doc->saveXML($doc, LIBXML_NOEMPTYTAG));
		//lf-ből cr-lf konvertálás
		$lfcrlfConv 	= str_replace("\n", "\r\n", $charConv);
		//Tagek utolsó elemének új sorba tétele
		$tagConv 		= str_replace('">', "\"\r\n>", $lfcrlfConv);
		//dtd
		$dtdConv 		= str_replace("DTD\"\r\n>", "DTD\">", $tagConv);
		//nyitótagek konvertálása
		$openConv		= str_replace("><", ">\r\n<", $dtdConv);
		//Zárótegek konvertálása
		$out 		= str_replace("></", ">\r\n</", $openConv);
		return $out;
	}

}
class Szamlaexport {
	//Számla kiállítójának adatai
 	private $cegNev;
	private $cegCim;
	private $cegAdoszam;
	//private $szamlazoPartnerek = array('szamlazz.hu' => array('Számlaszám','Számla típus','Hivatkozási számlaszám','Számla kelte','Telj. időpontja','Fiz. határidő','Fizetés módja','Rendelésszám','Nyelv','Devizanem','Árfolyam bank','Árfolyam','Vevő neve','Vevő azonosító','Vevő irsz.','Vevő város','Vevő utca','Vevő adószám','Nettó összesen','Áfa összesen','Bruttó összesen','Főkönyv vevő','Főkönyv vevő azonosító','Főkönyvi dátum','Termék,szolgáltatás','Mennyiség','Mennyiségi egység','Nettó egységár','Áfakulcs','Tétel nettó érték','Tétel áfa érték','Tétel bruttó érték','Tétel árbevétel főkönyv','Tétel árbevétel áfa','Tétel gazdasági esemény','Tétel áfa gazdasági esemény','Tétel megjegyzés','Tétel árrés áfaalap'));
	//private $szamlazoPartnerek = array('szamlazz.hu' => array('Számlaszám','Számla típus','Hivatkozási számlaszám','Számla kelte','Telj. időpontja','Fiz. határidő','Fizetés módja','Rendelésszám','Nyelv','Devizanem','Árfolyam bank','Árfolyam','Vevő neve','Vevő irsz.','Vevő város','Vevő utca','Vevő adószám','Nettó összesen','Áfa összesen','Bruttó összesen','Főkönyv vevő','Főkönyv vevő azonosító','Főkönyvi dátum','Termék,szolgáltatás','Mennyiség','Mennyiségi egység','Nettó egységár','Áfakulcs','Tétel nettó érték','Tétel áfa érték','Tétel bruttó érték','Tétel árbevétel főkönyv','Tétel árbevétel áfa','Tétel gazdasági esemény','Tétel áfa gazdasági esemény','Tétel megjegyzés','Tétel árrés áfaalap'));
	//private $szamlazoPartnerek = array('szamlazz.hu' => array('Számlaszám','Számla típus','Hivatkozási számlaszám','Számla kelte','Telj. időpontja','Fiz. határidő','Fizetés módja','Rendelésszám','Nyelv','Devizanem','Árfolyam bank','Árfolyam','Vevő neve','Vevő azonosító','Vevő irsz.','Vevő város','Vevő utca','Vevő adószám','Nettó összesen','Áfa összesen','Bruttó összesen','Főkönyv vevő','Főkönyv vevő azonosító','Főkönyvi dátum','Termék,szolgáltatás','Mennyiség','Mennyiségi egység','Nettó egységár','Áfakulcs','Tétel nettó érték','Tétel áfa érték','Tétel bruttó érték','Tétel árbevétel főkönyv','Tétel árbevétel áfa','Tétel gazdasági esemény','Tétel áfa gazdasági esemény','Tétel megjegyzés','Tétel árrés áfaalap'));
	//private $szamlazoPartnerek = array('szamlazz.hu' => array('Számlaszám','Számla típus','Hivatkozási számlaszám','Számla kelte','Telj. időpontja','Fiz. határidő','Fizetés módja','Rendelésszám','Nyelv','Devizanem','Árfolyam bank','Árfolyam','Vevő neve','Vevő azonosító','Vevő irsz.','Vevő város','Vevő utca','Vevő adószám','Nettó összesen','Áfa összesen','Bruttó összesen','Főkönyv vevő','Főkönyv vevő azonosító','Főkönyvi dátum','Termék,szolgáltatás','Mennyiség','Mennyiségi egység','Nettó egységár','Áfakulcs','Tétel nettó érték','Tétel áfa érték','Tétel bruttó érték','Tétel árbevétel főkönyv','Tétel árbevétel áfa','Tétel gazdasági esemény','Tétel áfa gazdasági esemény','Tétel megjegyzés'));
	//private $szamlazoPartnerek = array('szamlazz.hu' => array('Számlaszám','Számla típus','Hivatkozási számlaszám','Számla kelte','Telj. időpontja','Fiz. határidő','Fizetés módja','Rendelésszám','Nyelv','Devizanem','Árfolyam bank','Árfolyam','Vevő neve','Vevő azonosító','Vevő országa.','Vevő irsz.','Vevő város','Vevő utca','Vevő adószám','Vevő adószám EU','Nettó összesen','Áfa összesen','Bruttó összesen','Főkönyv vevő','Főkönyv vevő azonosító','Főkönyvi dátum','Termék,szolgáltatás','Mennyiség','Mennyiségi egység','Nettó egységár','Áfakulcs','Tétel nettó érték','Tétel áfa érték','Tétel bruttó érték','Tétel árbevétel főkönyv','Tétel árbevétel áfa','Tétel gazdasági esemény','Tétel áfa gazdasági esemény','Tétel megjegyzés'));
	//private $szamlazoPartnerek = array('szamlazz.hu' => array('Számlaszám','Számla típus','Hivatkozási számlaszám','Számla kelte','Telj. időpontja','Fiz. határidő','Fizetés módja','Rendelésszám','Nyelv','Devizanem','Árfolyam bank','Árfolyam','Vevő neve','Vevő irsz.','Vevő város','Vevő utca','Vevő adószám','Nettó összesen','Áfa összesen','Bruttó összesen','Főkönyv vevő','Főkönyv vevő azonosító','Főkönyvi dátum','Termék,szolgáltatás','Mennyiség','Mennyiségi egység','Nettó egységár','Áfakulcs','Tétel nettó érték','Tétel áfa érték','Tétel bruttó érték','Tétel árbevétel főkönyv','Tétel árbevétel áfa','Tétel gazdasági esemény','Tétel áfa gazdasági esemény','Tétel megjegyzés'));
	//private $szamlazoPartnerek = array('szamlazz.hu' => array('Számlaszám','Számla típus','Hivatkozási számlaszám','Számla kelte','Telj. időpontja','Fiz. határidő','Fizetés módja','Rendelésszám','Nyelv','Devizanem','Árfolyam bank','Árfolyam','Vevő neve','Vevő irsz.','Vevő város','Vevő utca','Vevő adószám','Nettó összesen','Áfa összesen','Bruttó összesen','Főkönyv vevő','Főkönyv vevő azonosító','Főkönyvi dátum','Termék,szolgáltatás','Mennyiség','Mennyiségi egység','Nettó egységár','Áfakulcs','Tétel nettó érték','Tétel áfa érték','Tétel bruttó érték','Tétel árbevétel főkönyv','Tétel árbevétel áfa','Tétel gazdasági esemény','Tétel áfa gazdasági esemény','Tétel megjegyzés'));
	
	//private $szamlazoPartnerek = array('szamlazz.hu' => array('Számlaszám','Számla típus','Hivatkozási számlaszám','Számla kelte','Telj. időpontja','Fiz. határidő','Fizetés módja','Rendelésszám','Nyelv','Devizanem','Árfolyam bank','Árfolyam','Vevő neve','Vevő irsz.','Vevő város','Vevő utca','Vevő adószám','Nettó összesen','Áfa összesen','Bruttó összesen','Főkönyv vevő','Főkönyv vevő azonosító','Főkönyvi dátum','Termék,szolgáltatás','Mennyiség','Mennyiségi egység','Nettó egységár','Áfakulcs','Tétel nettó érték','Tétel áfa érték','Tétel bruttó érték','Tétel árbevétel főkönyv','Tétel árbevétel áfa','Tétel gazdasági esemény','Tétel áfa gazdasági esemény','Tétel megjegyzés', 'Tétel árrés áfaalap'));
	private $szamlazoPartnerek = array('szamlazz.hu' => array('Számlaszám','Számla típus','Hivatkozási számlaszám','Számla kelte','Telj. időpontja','Fiz. határidő','Fizetés módja','Rendelésszám','Nyelv','Devizanem','Árfolyam bank','Árfolyam','Vevő neve','Vevő irsz.','Vevő város','Vevő utca','Vevő adószám','Nettó összesen','Áfa összesen','Bruttó összesen','Főkönyv vevő','Főkönyv vevő azonosító','Főkönyvi dátum','Termék,szolgáltatás','Mennyiség','Mennyiségi egység','Nettó egységár','Áfakulcs','Tétel nettó érték','Tétel áfa érték','Tétel bruttó érték','Tétel árbevétel főkönyv','Tétel árbevétel áfa','Tétel gazdasági esemény','Tétel áfa gazdasági esemény','Tétel megjegyzés'));
	
	private $szamlazoPartner;
	//Betöltött számla elemei, az egyes kimeneti XML-ek forámtumánal összehasonlításához
	private $szamlaElemek;
	//Nyers számlaadatokat tartalmazó tömb
	private $szamlaTomb;

	//Konstruktor, beállítjuk a cégadatokat
	public function __construct($cegNev = 'Company Info Kft.', $cegCim = '1141 Budapest, Komócsy u. 5.', $cegAdoszam = '13951968-2-42'){
		$this->cegNev		= $cegNev;
		$this->cegCim		= $cegCim;
		$this->cegAdoszam	= $cegAdoszam;
	}

	/*
	*
	*	Csv betöltése a nyers számlatömbbe
	*	a számlatömb tulajdonságai dinamikusan generálódnak
	*
	*/
	public function loadCsv($csvFile, $delimiter = ';'){
		//CSV fájl betöltése tömbbe
		$fileArr			= file($csvFile);
		//Sorork számának megahtározása. (azaz mennyi számlánk van)
		$lineNum			= count($fileArr);
		//A CSV első sora a számlaelemek megnevezése, ezt külön kezeljük
		
		//$this->szamlaElemek = explode(';', iconv('ISO-8859-2', 'UTF-8', $fileArr[0]));
		$this->szamlaElemek = explode(";", iconv('ISO-8859-2', 'UTF-8', $fileArr[0]));
		//$this->szamlaElemek = explode(';', $fileArr[0]);
		
		//print_r($this->szamlaElemek);
		//Az explode függvény az utolsó delimiter miatt +1 tömbelemet hoz létre
		//Openoffice-ban nincs ;jel az utolsó mező után, a szamlazz.hu-nál van, ez gondot okozhat
		array_pop($this->szamlaElemek);
		//forrásfájl ellenőrzés
		if($this->checkSource($this->szamlaElemek) == false){
			die('Nem felismerhető a számlaformátum. Vagy rossz fájl került feltöltésre, vagy megváltoztak a számla mezőnevei.');
		}
		//die('Ez a gond...');
		//Számlaelemek számának meghatározása. Mivel az explode fv az utolsó delimiternél is bont, 1-el kevesebb elemmel kell számolni
//		$szamlaElemSzam	= count($this->szamlaElemek)-1;
		$szamlaElemSzam	= count($this->szamlaElemek);
		//Végigmegyünk az összes számlán. 1 sor = 1 számla. Az első sort kihagyjuk, ez a számlaelemek megnevezése, amit fent külön kezeltük
		for($i=1; $i<$lineNum; $i++){
			//Egyik konvertertáló eljárás sem működött!!!
			//$arr[] = str_getcsv(iconv('ISO-8859-2', 'UTF-8', $fileArr[$i]), $delimiter);
			//$arr[] = str_getcsv(iconv('ISO8859-2', 'UTF-8', $fileArr[$i]), $delimiter);
			//$arr[] = str_getcsv(utf8_encode($fileArr[$i]), $delimiter);
			//$arr[] = str_getcsv($fileArr[$i], $delimiter);
			//$arr[] = str_getcsv(quoted_printable_decode($fileArr[$i]), $delimiter);
			//$arr[] = mb_convert_encoding($fileArr[$i], 'UTF-8', 'Quoted-Printable');

			//Számla elemeit tömbbe tesszük
			$szamlaSor = explode(';', iconv('ISO-8859-2', 'UTF-8', $fileArr[$i]));
			//$szamlaSor = explode("\n", iconv('ISO-8859-2', 'UTF-8', $fileArr[$i]));
			//print_r($szamlaSor);

			//Az összes számlaelem és azok értékének betöltése egy tömbbe
			for($j=0; $j<$szamlaElemSzam; $j++){
				$sz[trim($this->szamlaElemek[$j], '"')] = trim($szamlaSor[$j], '"');
			}

			//objektummá konvertáljuk az aktuális (1db) számla tömbjét
			$szamla = (object) $sz;
			
			//A nyers számladatok tömbje
			$this->szamlaTomb[] = $szamla;
		}
		
		return $this->szamlaTomb;
	}

	public function saveHessynXml($nyersSzamlak, $target){
		$szamlaElemek = array_keys(get_object_vars($nyersSzamlak[0]));
		//Ellenőrizzük, hogy minden adat megvan-e a hessyn számlához

		if($this->checkData($szamlaElemek, 'hessyn') != false){
			//header('Content-disposition: attachment; filename="xmlexport.xml"');
			//header('Content-Type: application/xml; charset=utf-8');

			
			$hessynSzamla	= new HessynSzamla();
			$doc 		= $hessynSzamla->generateXmlDoc($this->cegAdoszam);
			
			$kivenni = array();
			foreach($nyersSzamlak as $szamlaAdatok){
				
				if($szamlaAdatok->{'Számla típus'}=='SS') {
					$kivenni[] = $szamlaAdatok->{'Hivatkozási számlaszám'};
					continue;
				}
			}
			$szamlasorrend = array();
			foreach($nyersSzamlak as $szamlaAdatok){
				if($szamlaAdatok->{'Számla típus'}=='SS' or in_array($szamlaAdatok->{'Számlaszám'}, $kivenni)) {
					
					continue;
				}
				$szamlasorrend[$szamlaAdatok->{'Számlaszám'}][] = $szamlaAdatok;
			}
			ksort($szamlasorrend);
			$nyersSzamlak = array();
			foreach($szamlasorrend as $szamlak) foreach($szamlak as $szamla) $nyersSzamlak[] = $szamla;
			
			$kivontSzamlak = array();
			
			foreach($nyersSzamlak as $szamlaAdatok){
				if($szamlaAdatok->{'Számla típus'}=='SS' or in_array($szamlaAdatok->{'Számlaszám'}, $kivenni)) {
					
					continue;
				}
				// több tétel miatt összekapcsoljuk az azonos számlaszámon lévő számlákat
				$kivontSzamlak[$szamlaAdatok->{'Számlaszám'}][] = $szamlaAdatok;

			}
			
			foreach( $kivontSzamlak as $szamlaAdatok) {
				$hessynSzamla->populate($szamlaAdatok);
				$hessynSzamla->getXmlSzamla($doc);
			}

			
			
			
			$out = $hessynSzamla->postXmlProcess($doc);
			
			
			file_put_contents($target, $out);
			$ci =& get_instance();
			$ci->Admin_model->mentes(array('file' => $target), 'hk_konyveles_szamlak');
			
		}
		//Ha nincs meg
		else{
			die('Nincs meg az összes számlaadat az export elkészítéséhez');
		}
	}

	//A detektált számlaadatok alapján meghatározzuk a számlázó partnert. (ha van)
	public function checkSource($source){
		

		foreach($source as $k => $v)$source[$k] = trim($v, '"');
		
		$szuksegesMezok = array('Számlaszám','Számla típus','Számla kelte','Számla kelte',
			'Telj. időpontja','Fiz. határidő','Fizetés módja','Devizanem','Vevő neve',
			'Vevő irsz.','Vevő város','Vevő utca','Nettó összesen','Áfa összesen',
			'Bruttó összesen','Termék,szolgáltatás','Mennyiség','Mennyiségi egység',
			'Nettó egységár','Áfakulcs','Tétel nettó érték','Tétel áfa érték',
			'Tétel bruttó érték'
		);
		$this->source = $source;
		$prep = array();
		foreach($source as $k => $v)$prep[$v] = $v;
		
		//print_r($prep);
		
		foreach($szuksegesMezok as $v) if(!isset($prep[$v])) {
			print $v.' mező nincs az inputban!';
			exit;
		}
		$this->szamlazoPartner = 'szamlazz.hu';
		return true;
		exit;
		
		//print_r($source);
		exit;
		//Előzőleg rögzített számlázópartnerek tömbjének feldolgozása 
		foreach($this->szamlazoPartnerek as $partnerNev => $szamlaElem){
			$szElem = array();
			//	foreach($szamlaElem as  $elem) $szElem[] = '"'.$elem.'"';
			foreach($szamlaElem as  $elem) {
				//print $elem.'<br>';
				$szElem[] = ''.trim($elem, '"').'';
			}
			//var_dump($this->szamlazoPartnerek[$partnerNev]);
			//ha a számlaelemek alapján megvan a partner
			if($source == $szElem){

				$this->szamlazoPartner = $partnerNev;
				return true;
			}
			else{
						// hasonlítás
						
		header('Content-Type: text/html; charset=utf-8');
		print"<meta  ><table><tr><td valign=top><pre>source:";
		var_dump($source);print '</pre></td><td valign=top><pre>Szelem:';
		var_dump($szElem);
		print"</pre></td></tr></table>Kül:";
		var_dump(array_diff($szElem,$source )) ;
		print "
		
		";
		//var_dump(array_diff( $source,$this->szamlazoPartnerek));
		
		
		
				return false;
			}
		}

	}

	//A beküldött adatok ellenőrzése, hogy az összes megvan-e, ami az xml exporthoz kell
	public function checkData($szamlaElemek, $exportFormatum){
		//Export formátum
		if($exportFormatum == 'hessyn'){
			//Import partner
			if($this->szamlazoPartner == 'szamlazz.hu'){
				//Kötelező számlamezők
				$kotelezoElemek = array('Számlaszám','Számla kelte','Telj. időpontja','Fizetés módja','Nettó összesen','Bruttó összesen','Áfa összesen','Vevő neve','Tétel nettó érték','Termék,szolgáltatás','Tétel áfa érték','Tétel nettó érték', 'Számla típus');
				//$kotelezoElemek = array('"Számlaszám"','"Számla kelte"','"Telj. időpontja"','"Fizetés módja"','"Nettó összesen"','"Bruttó összesen"','"Áfa összesen"','"Vevő neve"','"Tétel nettó érték"','"Termék,szolgáltatás"','"Tétel áfa érték"','"Tétel nettó érték"', '"Számla típus"');
			}
		}
		//A számlázópartner számlaelemeinek összehasonlítása a szükséges elemekkel
		if(!array_diff($kotelezoElemek, $szamlaElemek)){
			//ha megvan az összes szükséges, 
			return true;
		}
		else{
			print_r (array_diff($kotelezoElemek, $szamlaElemek));
			//ha hiányzik valamelyik, vagy más a neve
			return false;
		}
	}
}
