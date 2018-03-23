<?php
class Contact
{	
	var $msgSucces;
	var $Destinatar;
	var $SendMail=TRUE;
	var $SaveDb=TRUE;
	
	function contactForm($frmValues=NULL)
		{
		$htmlForm = '
<form name="frmContact" id="frmContact" method="post" action="" >
                  <table width="100%" border="0" cellpadding="4" cellspacing="2">
                    <tr>
                      <td width="17%" align="right" valign="top">&nbsp;</td>
                      <td valign="top">&nbsp;</td>
                    </tr>
                    <tr>
                      <td width="17%" align="right" valign="top"><span class="text_black">Nume:</span></td>
                      <td width="83%" colspan="2" valign="top">
                        <input name="txtNume" type="text" id="txtNume" style="width:300px; " value="'. $frmValues['txtNume'] .'" size="50" />
                      </td>
                    </tr>
                    <tr>
                      <td width="17%" align="right" valign="top"><span class="text_black">Email:</span></td>
                      <td colspan="2" valign="top">
                        <input name="txtEmail" type="text" id="txtEmail" style="width:300px; " value="'.$frmValues['txtEmail'].'" size="50" />
                      </td>
                    </tr>
                    <tr>
                      <td width="17%" align="right" valign="top"><span class="text_black">Subiect:</span></td>
                      <td colspan="2" valign="top">
                        <input name="txtSubiect" type="text" id="txtSubiect" style="width:300px; " value="'.$frmValues['txtSubiect'].'" size="50" />
                      </td>
                    </tr>
                    <tr>
                      <td valign="top" align="right" width="17%"><span class="text_black">Mesaj:</span></td>
                      <td colspan="2" valign="top">
                        <textarea name="txtMesaj" cols="20"  rows="5" id="txtMesaj" style="width:300px; ">'.$frmValues['txtMesaj'].'</textarea>
                      </td>
                    </tr>
                    <tr>
                      <td width="17%" align="right"></td>
                      <td colspan="2">
                        <input type="submit" name="Submit2" value="Trimite!" />
					</td>
                    </tr>
             </table>
		</form>
		';
		return $htmlForm;
		}
		
	function validareForm($frmValues)
		{
		$eroareForm = "";
					
	   				if(empty($frmValues['txtNume'])){
						$eroareForm .= "<li>Nu ai completat campul '<strong>Nume</strong>'</li>";
						}
			
					if(empty($frmValues['txtEmail'])){
						$eroareForm .= "<li>Nu ai completat campul '<strong>Email</strong>'</li>";
					}else if(!preg_match('/^[a-zA-Z0-9\._-]+@[a-zA-Z0-9\._-]+\.[A-Za-z]{2,5}$/', $frmValues['txtEmail'])){
						$eroareForm .= "<li>'<strong>Adresa email</strong>' indicata nu este valida</li>";
						}
					
					if(empty($frmValues['txtSubiect'])){
						$eroareForm .= "<li>Nu ai completat campul '<strong>Subiect</strong>'</li>";
						}	
			
					if(empty($frmValues['txtMesaj'])){
						$eroareForm .= "<li>Nu ai completat campul '<strong>Mesaj</strong>'</li>";
						}
		if($eroareForm == "") return NULL;
		else return $eroareForm;				
		}
	
	function proccesForm($frmValues)
		{
		if($_SERVER['REQUEST_METHOD'] == "POST")
			{
			$msgError = $this -> validareForm($frmValues);
			if(empty($msgError)) 
				{
				if($this -> SaveDb)
				{
				$mail = new ContactMail;
				$frmValues['txtCitit'] = 'NU';
				$frmValues['txtData'] = date("Y-m-d H:i:s");
				$mail -> incarcaMailArray($frmValues, "txt");
				$mail -> insertMail();
				echo $mail -> Nume;
				}
				
				if($this -> SendMail)
				{
				$mail = new PHPMailer();

				$mail->From = $frmValues['txtEmail'];
				$mail->FromName = $frmValues['txtNume'];
				$mail->AddAddress($this -> Destinatar, "");
           
				$mail->AddReplyTo($frmValues['txtEmail'], $frmValues['txtNume']);

				$mail->WordWrap = 50;                                 // set word wrap to 50 characters

				$mail->IsHTML(true);                                  // set email format to HTML

				$mail->Subject = $frmValues['txtSubiect'];
				$mail->Body    = $frmValues['txtMesaj'];
				$mail->AltBody = $frmValues['txtMesaj'];

				if(!$mail->Send()) {}


				}
				echo $this -> msgSucces;
				}
			else
				{
				echo $msgError;
				echo $this -> contactForm($frmValues);
				}
			}
		else
			{
			echo $this -> contactForm();
			}	
		}	
	
}

class ContactMail
{
	var $IdEmail;
	var $Nume;
	var $Email;
	var $Subiect;
	var $Mesaj;
	var $Data;
	var $Citit;
	var $RaspunsPentru;
	
	function incarcaMail()
		{
		$mysql = new MySQL();
		$sql = "SELECT * FROM Contact WHERE IdEmail = '". $this -> IdEmail ."'";
		$this -> incarcaMailArray($mysql -> getRow($sql));
		}
	
	function incarcaMailArray($array, $key=NULL)
		{
		$this -> IdEmail = $array[$key.'IdEmail'];
		$this -> Nume = $array[$key.'Nume'];
		$this -> Email = $array[$key.'Email'];
		$this -> Subiect = $array[$key.'Subiect'];
		$this -> Mesaj = $array[$key.'Mesaj'];
		$this -> Data = $array[$key.'Data'];
		$this -> Citit = $array[$key.'Citit'];
		$this -> RapunsPentru = $array[$key.'RaspunsPentru'];
		}
		
	function insertMail()
		{
		$sql =
		"
		INSERT INTO Contact
		(
		Nume
		,Email
		,Subiect
		,Mesaj
		,Data
		,Citit
		)
		VALUES
		(
		'". $this -> Nume ."'
		,'". $this -> Email ."'
		,'". $this -> Subiect ."'
		,'". $this -> Mesaj ."'
		,'". $this -> Data ."'
		,'". $this -> Citit ."'
		)
		";
		$mysql = new MySQL();
		$this -> IdEmail = $mysql -> insertRow($sql);
		$this -> incarcaMail();
		}	
	
	
	function stergeMail()		
		{
		$sql = "
		DELETE FROM Contact WHERE IdEmail = '". $this -> IdEmail ."'
		";
		$mysql = new MySQL();
		$mysql -> query($sql);
		}
}

?>