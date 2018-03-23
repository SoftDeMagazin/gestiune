<?php
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
 
 
 
 
