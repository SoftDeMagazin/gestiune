# Despre

Aplicație pentru gestiune și managementul stocurilor realizată ca proiect în intervalul 2008-2009 de către [Eduard Budacu](http://eduard.budacu.ro/) și publicată ca lucrare de licență în 2010. Proiectul a fost realizat în cadrul companiei Pit Software și am primit acceptul companiei de a publica codul sursă.

[Pit Software](http://pitsoft.ro/) este o companie dezvoltatoare de soft de gestiune pentru restaurante, soft de marcaj, soft de gestiune hoteliera, soft de ticketing pentru stadioane si cinematografe. Pe langa acestea dezvoltă si aplicatii customizate la cererea clientilor.

# Definirea problemei

Se dorește construirea unei aplicații care să permită gestionarea stocurilor, a partenerilor și situațiilor financiare pentru o societate comercială sau un grup de societăți care își desfășoară activitatea in mai multe puncte de lucru. Aplicația trebuie sa permită întocmirea actelor necesare activității de gestiune (Note de Intrare Recepție și Constatare Diferențe, Note de Transfer, Avize de Expediție si Însoțire Marfă, Facturi Fiscale, Facturi Proforma, Fișa de Magazie, Bonuri Consum etc.),  rapoarte pe fiecare punct de lucru sau rapoarte centralizate, evidenta stocurilor si a mișcărilor de stocuri, rapoarte financiare (plăti si încasări), export de date pentru contabilitate. 

Aplicația se va implementa pentru un lanț de cafenele din Romania, printre cele mai mari din Estul Europei. Cele trei societăți ale grupului își desfășoară activitatea in 50 de cafenele, un restaurant, un club și o terasa cu piscina. Nevoia de a cunoaște situația vânzărilor și situației stocului in orice moment va fi rezolvată prin implementarea unei soluții software web.

Scopul programului nu este acela de a înlocui aplicația de contabilitate a firmei ci de a oferi acesteia informații prin exporturi, rapoarte, întocmirea unor acte contabile pentru a evita înregistrarea datelor în ambele aplicații fiind furnizor de informații pentru departamentul financiar-contabil.

Operatorii principali sunt managerii de locație ce au rolul de a recepționa mărfurile, facturile de la furnizori si a le introduce în aplicație pentru întocmirea Notelor de Intrare Recepție. De asemenea prin intermediul soft-ului va ține evidenta plăților către furnizori, ale încasărilor zilnice, vor supraveghea  descărcarea vânzărilor și scăderea stocurilor. 

Se vor folosi tehnologii web, putând rula pe majoritatea browserelor si sistemelor de operare cu un minim de necesitați atât hardware cât si software ale calculatorului pe care se operează întrucât datele sunt  prelucrate pe serverul web acesta din urmă dispunând de resurse și putere de calcul superioară. 

Întrucât este folosită de un număr mare de utilizatori programul va fi dezvoltat modular putând fi definite grupuri de useri (roluri) cu diferite niveluri de acces la modulele  aplicației cât si la gestiunile definite. 

# Tehnologii folosite

Pentru realizarea aplicatiei s-au folosit limbajele PHP, HTML, CSS, AJAX, JavaScript alaturi de baza de date MySQL ce vor fi descrise pe larg in cele ce urmează.

## PHP

PHP este un limbaj de programare ce rulează pe server proiectat special pentru web. Numele PHP provine din limba engleză si este un acronim recursiv: Php: Hypertext Preprocesor. Folosit inițial pentru a produce pagini web dinamice, este folosit pe scară largă în dezvoltarea paginilor și aplicațiilor web. Se folosește în principal înglobat în codul HTML, dar poate fi folosit si in mod linie de comandă (CLI). Este unul din cele mai importante limbaje de programare web open-source si server-side existând versiuni disponibile pentru majoritatea web serverelor si pentru toate sistemele de operare.

PHP este un produs Open Source, cu acces la codul sursa. Îl puteți folosi, modifica și redistribui, toate acesta in mod gratuit.

PHP este simplu de utilizat, fiind un limbaj de programare structurat, ca și C-ul, Perl-ul sau începând de la versiunea 5 chiar Java, sintaxa limbajului fiind o combinație a celor trei. Datorită modularității sale poate fi folosit si pentru a dezvolta aplicații de sine stătătoare. Probabil una din cele mai importante facilitați ale limbajului este conlucrarea cu majoritatea bazelor de date relaționale de la MySQL și pană la Oracle.

## MySQL 

MySQL este un sistem de gestiune a bazelor de date relaționale, foarte rapid și robust. O bază de date vă permite să stocați, să căutați, să sortați și să vă regăsiți datele în mod eficient. Serverul MySQL controlează accesul la datele dumneavoastră pentru a garanta că mai mulți utilizatori pot lucra simultan cu acestea.


Deci, MySQL este un server multi-user (mai mulți utilizatori) şi multi-thread (mai multe fire de execuție). Utilizează SQL (Structured Query Language), limbajul standard de interogare a bazelor de date din întreaga lume. MySQL este disponibil în mod public din 1996, dar istoria dezvoltării sale începe în 1979. A câștigat de mai multe ori Linux Journal Readers Choice Award (Premiul cititorilor). MySQL este disponibil sub o licență Open Source, dar dacă este nevoie sunt disponibile și licențe comerciale.

## JavaScript

JavaScript este un limbaj de programare orientat obiect bazat pe conceptul prototipurilor. Este folosit mai ales pentru introducerea unor funcționalități în paginile web, codul Javascript din aceste pagini fiind rulat de către browser. Limbajul este bine cunoscut pentru folosirea sa în construirea site-urilor web, dar este folosit și pentru accesul la obiecte încastrate (embedded objects) în alte aplicații. A fost dezvoltat iniţial de către Brendan Eich de la Netscape Communications Corporation sub numele de Mocha, apoi LiveScript, şi denumit în final JavaScript.

În ciuda numelui și a unor similarități în sintaxă, între JavaScript și limbajul Java nu există nicio legătură. Ca şi  Java, JavaScript are o sintaxă apropiată de cea a limbajului C, dar are mai multe în comun cu limbajul Self decât cu Java. Cea mai des întâlnită utilizare a JavaScript este în scriptarea paginilor web. Programatorii web pot îngloba în paginile HTML script-uri pentru diverse activități cum ar fi verificarea datelor introduse de utilizatori sau crearea de meniuri şi alte efecte animate.

## HTML

Unul din primele elemente, fundamentale de altfel, ale WWW (World Wide Web) este HTML (HyperText Markup Language), standard ce descrie formatul primar în care documentele sunt distribuite şi văzute pe Web. Multe din trăsăturile lui, cum ar fi independentă fată de platformă, structurarea formatării şi legăturile hipertext, fac din el un foarte bun format pentru documentele Internet și Web. Primele specificații de bază ale Web-ului au fost HTML, HTTP și URL.

Erau necesare câteva condiții esențiale: independența de platformă, posibilități hipertext și structurarea documentelor. Independentă de platformă semnifică faptul că un document poate fi afișat în mod asemănător (sau aproape identic) de computere diferite (deci cu font, grafică și culori aidoma), lucru vital pentru o audiență numeroasă și extrem de variată. Hipertext se traduce prin faptul că orice cuvânt, frază, imagine sau element al documentului văzut de un utilizator (client) poate face referință la un alt document sau chiar la paragrafe din interiorul aceluiași document, ceea ce ușurează mult navigarea între pârțile componente ale unui document sau între multiple documente. Structurarea riguroasă a documentelor permite convertirea acestora dintr-un format în altul precum şi interogarea unor baze de date înglobând aceste documente.

Partea de afișare a site-ului este asigurată de HTML. Proprietățile tag-urilor au fost definite cu ajutorul CSS. Design-ul formularelor, modul de afișare în pagină, link-urile, imaginile, toate elementele care interacționează în mod vizual cu utilizatorul, sunt elemente specifice HTML şi au fost editate fie manual, prin cod HTML.

## CSS

CSS este acronimul pentru Cascading Style Sheets. CSS este un limbaj (style language) care definește "layout-ul" pentru documentele HTML. CSS acoperă culori, font-uri, margini (borders), linii, înălțime, lățime, imagini de fundal, poziții avansate și multe alte opțiuni.

HTML este de multe ori folosit necorespunzător pentru a crea layoutul site-urilor de internet. CSS oferă mai multe opțiuni, este mai exact şi sofisticat. În plus, este suportat de toate browserele actuale.

HTML este folosit pentru a structura conținutul în timp ce CSS este folosit pentru a formata conținutul.

În perioada de început a web-ului, HTML era folosit numai pentru structura textului. Textul se putea marca cu taguri precum <hl> şi <p> pentru a marca titlul sau un paragraf. Odată cu creșterea popularității web-ului, designerii au început să caute diferite posibilități de a adăuga layout documentelor online. Pentru a răspunde acestor cerințe, producătorii de browsere (în acea vreme Microsoft și Netscape) au inventat noi taguri HTML precum <font> care diferă fată de tagurile originale HTML prin faptul că definesc layoutul și nu structura.
  
Acest lucru a dus și la o situație unde tagurile originale de structură ca <table> să fie folosite necorespunzător pe pagini de layout (to layout pages). Multe taguri noi de layout precum <blink> erau recunoscute numai de unele browsere. O formulă comună ce apărea pe siteuri era "Aveţi nevoie de browserul X pentru a vedea această pagină". CSS a fost inventat pentru a remedia această situație, furnizându-le designerilor facilități sofisticate pentru editarea layoutului, suportate de toate browserele.

CSS a reprezentat un element revoluționar în lumea web-designului.
Beneficiile concrete includ:
*	controlarea layoutului documentelor dintr-o singură pagină de stiluri;
*	control mai exact al layoutului;
*	aplicare de layouturi diferite pentru tipuri media diferite (ecran, printare etc);
*	tehnici numeroase și sofisticate.

# Framework-ul propriu

Aplicația este dezvoltată in jurul acestui framework ce conține un set de clase definite pentru a realiza cu ușurință accesul la baza de date, manipularea datelor din tabele, apelarea procedurilor, generarea de cod html, formulare, rapoarte.

## DataSource 

Clasa DataSource reprezintă o colecție de obiecte ce stochează datele returnate de executarea unui query, apelarea unei proceduri stocate sau apelarea unei funcții din baza de date. Conține doua proprietăți $_data (array asociativ) înregistrarea de pe poziția curenta si $_dataSource array(array($_data)) colecția de date înregistrate.

Clasa DataSource implementează interfețele:

*	Countable – la apelarea functiei count() pe un obiect DataSource returnează numarul de inregistrari din $_dataSource prin implementarea functiei count()
*	IteratorAggregate – permite parcurgerea obiectelor folosind foreach prin implementarea functiei getIterator()
*	ArrayAcces – permite accesul folosind index asemanator vectorilor  prin implementarea functiilor:  offsetGet($offset),  offsetExists($offset),  offsetUnset($offset),  offsetSet($offset)

Folosind metodele magice _get() si _set() clasa permite accesul la coloanele returnate

## Model

Clasa Model extinde clasa DataSource. Implementează funcțiile pentru accesul la tabelele din baza de date creare, inserare, update, ștergere cunoscute si ca operații CRUD (create, read, update, delete).

Proprietățile clasei:

*	$tbl – numele tabelei
*	$key – primary key-ul tabelei
*	$_tblColumns – coloanele tabelului
*	$_relations – relatiile de legatura cu celelalte tabele (one-to-one, one-to-many)
*	$_validator – validarile de date pe coloane
*	$db – connectorul la baza de date

Operații pe tabele:

*	insert()
*	update()
*	save()
*	delete()

Metode pentru interogare:

*	fromId($id) – returneaza randul cu primary key –ul $id
*	fromString($str) – returneaza randurile ce respecta conditiile sql din $str
*	fromArrayOfId($array) – returneaza randurile ale caror id-uri se regasesc in vectorul $array
*	find($key, $value) – returneaza randul e ce are valoarea $value pe coloana $key

Metode pentru generarea automată a formularelor:

*	frm($options) – genereaza formularul html
*	frmButton($value, $options) – genereaza butonul submit
*	frmButtonScript($value, $options) – genereaza butonul submit cu apelare din JavaScript
*	frmContent($form) - returneaza continutul formularului
*	frmDefault($form, $frmOptions) 
*	frmInnerHtml($innerHtml, $frmOptions) – seteaza continutul html al formularului
*	frmEnd()

Metode pentru validare

* validate(&$objResponse) – parcurge instructiunile de validare din $_validator si returneaza true daca nu a fost nici o eroare sau o lista cu mesajele de eroare asociate fiecarui camp invalid

```php

  var $_validator = array(
		"pret_val"=>array(array("numeric", "Pretul trebuie sa fie numeric")), 
		"denumire"=>array(array("required", "Introduceti denumire"), 
		array("unique", "Denumire existenta")), 
	);


```

## Proc

Clasa Proc extinde clasa DataSource. Permite apelarea procedurilor stocate din baza de date.

```php
class GetTotalIncasariTerti extends Proc {
	var $proc_name="getTotalIncasariTerti";
}
$total = new GetTotalIncasariTerti($gestiune_id, $tert_id);
```

## MySQL

Clasa MySQL realizează conexiunea cu baza de date. Este folosita de clasele Model și Proc.

Proprietățile clasei:

* $Link – resursa de identificare a conexiunii returnata de funcția mysqli_connect
* $Server – ip-ul serverului
* $User – utilizator
* $Pass – parola
* $Db – baza de date

Metodele clasei:

* __construct() – execută conectarea
* __destruct() – eliberarea resurselor folosite
* connect()
* query($sql) – executa un query, returnează un result set al funcției mysqli_query()
* insertRow($sql) – inserează un rând si returneaza id-ul randului
* numRows($sql) – returnează numărul de rânduri ale query-ului
* tableColumns()
* getRow($sql) – returnează array asociativ cu date pentru un query ce returnează un singur rand
* getRows($sql) – returnează array asociativ cu date pentru un query ce returnează mai multe randuri
* callProc($sql) – apelează o procedura
* getRowsNum – returnează array numeric cu datele pentru un query ce returnează mai multe randuri
* cleanConnection()
* insertArray($array, $table, $id) – parcurge coloanele vectorului si returnează query-ul pentru inserarea datelor
* unsertArray($array, $table, $id) – parcurge coloanele vectorului si returnează query-ul pentru update-ul datelor

Exemplu

```php
/* 
 modelele reprezinta clasele care fac legatura cu baza de date 
 
 se vor pune in app/include/models/
 
 trebuie sa se respecte urmatoarea conventie de nume:
 
 daca tabela se numeste produse
 
 modelul se  va numi Produse
 
si va fi scris in fiserul produse.php

- daca tabela se numeste categorii_produse
modelul se va numi CategoriiProduse
si va fi scris in fiserul categorii_produse.php
 
 */
 //clasa Produse extinde clasa Model
 /*
  * tabela produse contine coloanele produs_id (autoincrement), denumire, categorie_id
  */
 class Produse extends Model {
 	
	//numele tabelei
	var $tbl = "produse";
	
	//in aceasta proprietate se pot definii relatii intre tabele
	/*
	 * se defineste un array asociativ
	 * 
	 * 
	 */
	var $_relations = array(
		"categorie" => array(
			"type" => "one", //tipul de ralatie one sau many
			"model" => "CategoriiProduse", //clasa de relatie
			"key" => "categorie_id", //coloana de legatura
			"value" =>  "denumire", //coloana din categorii_produse pe care sa o ia ca valoare
		)
	);
	
	// aici se poate defini formularul care se va genera automa
	var $_defaultForm = array(
		//va genera un input:text cu id denumire si name denumire
		"denumire" => array(
			/*
			 * text -> textfield
			 * textarea -> textarea
			 * select -> un select box (trebuie definita options care va fi array asociativ de valori pentru select value => text
			 * 							sau un query sql care returneaza 2 coloane ex: select categorie_id, denumire from categorii
			 *  hidden -> un input hidden
			 */
			"type" => "text", 
			"label" => "Denumire Produs",
			/*
			 * se pot defini proprietati html
			 */
			"attributes" => array("style" => "color:red;font-size:20px", "onClick" => "this.value='';")
		),
		/*
		 * pentru ca am definit in _relations relatia categorie
		 * aceasta afiseaza un selectbox cu categoriile din baza de date
		 */
		"categorie" => array(
			"label" => "Selectati Categorie",
		),
	);
 }
 
 
 
 /*
  * tabela categorii_produse contine coloanele categorie_id (autoincrement), denumire 
  */
 class CategoriiProduse extends Model {
 	var $tbl = "categorii_produse";
 	var $_relations = array(
		"produse" => array(
			"type" => "many", //tipul de ralatie one sau many
			"model" => "Produse", //clasa de relatie
			"key" => "categorie_id", //coloana de legatura
		)
	);
 }
 
 /*
  * exemple apelare constructor clasa
  */
 
 /*
  * 1.daca pun un int va intergoa baza de date cu: SELECT * FROM produse WHERE produs_id = 1
  */
 $produs = new Produse(1);
 
 /*
  * permite accesarea coloanelor din tabela astfel:
  * 
  */

 echo $produs -> denumire;  //afiseaza denumirea

 echo $produs -> id;  //afiseaza id-ul similar cu $produs -> produs_id

echo $produs -> categorie -> denumire; // pentru ca am definit relatia in $_relation va interoga automat categoria din care face parte


/*
 * 2.daca pun un string va executa un query
 */
 
 $produse = new Produse("where categorie_id = '1'"); // va executa SELECT * FROM produse where categorie_id = 1
 
 
 echo $produse -> denumire; //ptimul produs returnat
 echo $produse[1] -> denumire; // al doilea produs returnat, etc
 
 /*
  * se poate itera cu foreach clasa:
  */
 foreach($produse as $produs) {
 	echo $produs -> denumire;
 }
 
 /*
  * se afla numarul de randuri returnate cu count()
  */
$nr_r = count($produse);
/*
 * se poate parcurge cu for
 */
for($i = 1; $i < $nr_r; $i++) {
	echo $produse[$i] -> denumire; // aceata accesare va fi mai lenta cu date multe
	//recomand:
	$produse -> fromDataSource($i);
	echo $produse -> denumire;
}

/*
 * pentru a afisa formlarul definit in _defalutForm
 */
 echo $produs -> frmDefault();
 //afiseaza butonul de submit
 echo $produs -> frmButtonScript("Trimite");
 
 /*
  * 3. apelare constructor cu array
  */
 $array = array("denumire" => "Cola", "categorie_id" => "1");
 $produs = new Produse($array);
 
 $produs -> denumire; // Cola
 $produs -> cagegorie_id; // 1
 $produs -> save(); // daca produs_id = 0 face insert, daca produs_id != 0 face update
 
 /*
  * 4. apelare constuctor gol
  */
 
 $produs = new Produse();
 $produs -> denumire = "Fanta";
 $produs -> save(); // va insera Fanta

```

## Html

Clasa Html permite generarea de cod html. Metodele clasei se pot apela static sau sunt folosite de clasele Form, Dialog, DataGrid, Table, TableCell, TableRow. Metodele clasei Html reproduc tag-urile uzuale din limbajul HTML. 

## Form

Clasa Form permite generarea automata a formularelor de introducere a datelor. Extinde clasa Html. Clasa Form este folosita de clasa Model pentru a genera formularele de introducere a datelor conform structurii tabelei. 

## DataGrid (Table, TableCell, TableRow)

Folosind cele trei clasa Table, TableCell si TableRow clasa DataGrid permite generarea de tabele HTM.

Exemplu:
```php
require_once("cfg.php");
/*
 * clasa DataGrid permite cu usurinta crearea de tabele
 */
 $proprietati_tag_table = array("width" => "100%", "border" => "1");
$dg = new DataGrid($proprietati_tag_table);

/*
 * antet tabel
 */
$dg -> addHeadColumn("Denumire");
$dg -> addHeadColumn("Categorie", array("style" => "color:red"));

$produse = new Produse("where 1 limit 0, 30");

foreach($produse as $produs) {
	$dg -> addColumn($produs -> denumire);
	$dg -> addColumn($produs -> categorie -> denumire, array("align" => "right"));
	//trec la urmatorul rand
	$dg -> index();
} 

//afisez dg-ul
echo $dg -> getDataGrid();
```
Rezultat:

```html
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <th scope="col">Denumire</th>
    <th scope="col">Categorie</th>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
```

# Framework-uri externe

Pentru dezvoltarea aplicației s-au folosit și o serie de framework-uri open source pentru operații cum ar fi generare de fișiere pdf, fișiere excel, coduri de bare, comunicare SOAP. În cele ce urmează vor fi prezentate câteva din cele mai importante.

## xAJAX

xAJAX este un framework ce permite definirea de funcții PHP ce pot fi apelate din JavaScript asincron. Folosirea implică crearea unui fișier server.php in care sunt definite funcțiile ce trebuie să returneze un obiect de tip xajaxReponse, un fișier common.php în care sunt înregistrate funcțiile. Rolul framework-ului este de a converti funcțiile PHP în funcții JavaScript urmând să fie apelate prin xajax_numeFunctie(parametrii).

server.php

```php
<?php
require_once("common.php");
$xajax -> processRequest();
/*
 * in server definesc functiile in php care trebuie sa returneze in 
 */
function test() {
	//trebuie sa generezi si sa returnezi un $objResponse
	
	$objResponse = new xajaxResponse();
	$objResponse -> assign("div_id", "innerHTML", "hello");
	return $objResponse;
}

function helloWorld($nume) {
	//
	
	$objResponse = new xajaxResponse();
	
	$objResponse -> alert($nume);
	
	//va copia ce returneaza test() in actualul objResponse
	copyResponse($objResponse, test());
	return $objResponse;
}
?>
```

common.php
```php
<?php
require_once("cfg.php");

$xajax = new xajax("server.php");
//definesc functiile
$xajax -> registerFunction("test");

$xajax -> registerFunction("helloWorld");
?>
```

## NuSOAP

SOAP este un protocol lightweight, XML-based pentru schimb de informații intr-un sistem distribuit, decentralizat. Este o varianta a RPC (Remote Procedure Call). 

Protocolul constă din trei parți:

* Un envelope (infăsurator) care definește ce conține mesajul si cum trebuie prelucrat
* Un set de reguli de codificare care exprima instanțe de tipuri de date definite de aplicație.
* Convenții pentru reprezentarea apelurilor de metode remote si răspunsurile la acestea

SOAP teoretic poate fi folosit in combinație cu o gama larga de protocoale, dar singurele reguli descrise in specificații sunt cum se folosește protocolul in combinatie cu HTTP si HTTP Extension Framework. SOAP folosește protocolul HTTP, conexiunile HTTP, majoritatea companiilor au serverele Web configurate pe portul standard 80 pentru conexiunile HTTP, deci protocolul poate sa fie folosit fără schimbări complexe in firewall-urile rețelelor, schimbări care pentru multe alte protocoale ar fi necesare.
Folosind tehnologia SOAP aplicația de gestiune poate comunica cu alte aplicații chiar scrise in alt limbaj de programare. De exemplu sunt definite un set de funcții ce permit comunicarea cu aplicația PitosPOS dezvoltată in C# ce permite transferul nomenclatorului de produse, categorii și preluarea automată a vânzărilor.

Exemplu înregistrare obiect de tip Produs – vector de produse

```php
// ---------- Produs ---------------------------------

$server->wsdl->addComplexType(
    'Produs',
    'complexType',
    'struct',
    'all',
    '',
    array(
        'produs_id' => array('name'=>'produs_id','type'=>'xsd:int'),
		'categorie_id' => array('name'=>'categorie_id','type'=>'xsd:int'),
		'denumire_categorie' => array('name'=>'denumire_categorie','type'=>'xsd:string'),
		'denumire' => array('name'=>'denumire','type'=>'xsd:string'),
		'pret_ron' => array('name'=>'pret_ron','type'=>'xsd:float'),
		'pret_val' => array('name'=>'pret_val','type'=>'xsd:float'),
    )
);
// ---------- Produs[] --------------------------------

$server->wsdl->addComplexType(
    'ProdusArray',
    'complexType',
    'array',
    '',
    'SOAP-ENC:Array',
    array(),
    array(
        array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:Produs[]')
    ),
    'tns:Produs'
);
```

## Barcode

BarCode conține un set de clase pentru generarea de coduri de bare. Se pot genera coduri de bare Codabar, Code11, Code39, Code93, Code128, EAN-8, EAN-13, ISBN, Interleaved 2 of 5, Standard 2 of 5, MSI Plessey, UPC-A, UPC-E, UPC Extension 2, UPC Extension 5 and PostNet . Clasele generează fișiere png, gif sau jpg.

## PHPExcel

PHPExcel contine un set de clase pentru citirea si scrierea documentelor Excel. 

```php
<?php
$objReader = PHPExcel_IOFactory::createReader('Excel5');

$objPHPExcel = $objReader->load("xls/nir.xls");
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setCellValue('C6', $furnizor -> obj -> nume);
$objPHPExcel->getActiveSheet()->setCellValue('C7', $this -> numar_factura);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter -> save("temp/".$time.".xls");
?>
```

## tcPDF

Libraria tcPDF conține un set de clase pentru generarea de documente in format PDF (Portable Document Format).

## jQuery

jQuery este o platformă de dezvoltare JavaScript, concepută pentru a ușura și îmbunătăți procese precum traversarea arborelui DOM în HTML, managementul inter-browser al evenimentelor, animaţii şi cereri tip AJAX. jQuery a fost gândit să fie cât mai mic posibil, disponibil în toate versiunile de browsere importante existente, şi să respecte filosofia "Unobtrusive JavaScript". Librăria a fost lansată in 2006 de către John Resig.

jQuery se poate folosi pentru a rezolva următoarele probleme specifice programării web:

* selecții de elemente în arborele DOM folosind propriul motor de selecții open source Sizzle, un proiect născut din jQuery
* parcurgere și modificarea arborelui DOM (incluzând suport pentru selectori CSS 3 şi XPath simpli)
* înregistrarea și modificarea evenimentelor din browser
* manipularea elementelor CSS
* efecte și animații
* cereri tip AJAX
* extensii
* utilități - versiunea browser-ului, funcția each.

Exemplu

```js
//segventa urmatoare de cod se activeaza la incarcarea pagini si are rolul de
//a genera meniul
//a initializa arajarea in pagina 
//si initializarea componentelor (calendar, multiSelect, tab)
$(document).ready(
	function() {
		$('#meniu').accordion({header: 'h3', animated: false});
		$('#meniu').accordion('activate', <?=RAPOARTE_FINANCIARE?>);
		$('#tabs').tabs();
		$('.calendar').datepicker({ buttonImageOnly: true, hideIfNoPrevNext: true, duration: '', showOn: 'button', buttonImage:'/app/files/img/office-calendar.png' });
		$('#categorie_id').multiSelect({selectAllText: 'Selecteaza tot!', oneOrMoreSelected: '*'}, function(el) {
					
				});
		$('#tip_produs').multiSelect({selectAllText: 'Selecteaza tot!', oneOrMoreSelected: '*'}, function(el) {
					
				});		
		$('#societate_id').multiSelect({selectAllText: 'Selecteaza tot!', oneOrMoreSelected: '*'}, function(el) {
					xajax_loadSocietate(xajax.getFormValues('frmFiltre'));
				});				
		$('#gestiune_id').multiSelect({selectAllText: 'Selecteaza tot!', oneOrMoreSelected: '*'}, function(el) {
					xajax_loadGestiune(xajax.getFormValues('frmFiltre'));
				});			
		$('#tert_id').multiSelect({selectAllText: 'Selecteaza tot!', oneOrMoreSelected: '*'}, function(el) {
					
				});			
	}
);
```

