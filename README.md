# Definirea problemei

Se dorește construirea unei aplicații care să permită gestionarea stocurilor, al partenerilor și situațiilor financiare pentru o societate comercială sau un grup de societăți care își desfășoară activitatea in mai multe puncte de lucru. Aplicația trebuie sa permită întocmirea actelor necesare activității de gestiune (Note de Intrare Recepție și Constatare Diferențe, Note de Transfer, Avize de Expediție si Însoțire Marfă, Facturi Fiscale, Facturi Proforma, Fișa de Magazie, Bonuri Consum etc.),  rapoarte pe fiecare punct de lucru sau rapoarte centralizate, evidenta stocurilor si a mișcărilor de stocuri, rapoarte financiare (plăti si încasări), export de date pentru contabilitate. 

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



