// JavaScript Document
function CallPrintContent(strid)
{
var prtContent = document.getElementById(strid);
var WinPrint =
window.open('','','left=0,top=0,width=1024,height=600,toolbar=0,scrollbars=1,status=1,fullscreen=yes');
WinPrint.document.write('<style>@media print { #buttons { display:none } }</style>');
WinPrint.document.write('<div id="buttons">');
WinPrint.document.write('<button onClick="WebBrowser1.ExecWB(7, 6);">Preview</button>');  
WinPrint.document.write('<button onClick="window.print();">Print</button>');
WinPrint.document.write('<button onClick="window.close();">Close</button>');
WinPrint.document.write('</div>');
WinPrint.document.write(prtContent.innerHTML);
WinPrint.document.write('<object ID="WebBrowser1" WIDTH="0" HEIGHT="0" CLASSID="CLSID:8856F961-340A-11D0-A96B-00C04FD705A2"></object>');
WinPrint.document.close();
}

function popup(mylink, windowname)
{
if (! window.focus)return true;
var href;
if (typeof(mylink) == 'string')
   href=mylink;
else
   href='';
window.open(href, windowname, 'type=fullWindow,fullscreen,scrollbars=yes');
return true;
}
