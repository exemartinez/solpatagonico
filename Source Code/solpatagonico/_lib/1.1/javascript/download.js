document.writeln('<iframe width="100%" height="100" frameborder="yes" onLoad="O_download.downloadFinished()" name="IF_download" id="IF_download" style="display: none" src=""></iframe>');
document.writeln('<iframe width="100%" height="100" frameborder="yes" onLoad="O_downloadTo.downloadFinished()" name="IF_downloadTo" id="IF_downloadTo" style="display: none" src=""></iframe>');

function C_download(){
	this.callback='';
	IF_download=top.frames['main']?top.frames['main'].frames['IF_download']:frames['IF_download'];
	
	this.downloadFinished=function (){
		if(IF_download.location!='about:blank'){
			ret=IF_download.document.body.innerHTML;
			this.callback(ret);
		}
	}
}
function C_downloadTo(){
	this.element='';
	IF_downloadTo=top.frames['main']?top.frames['main'].frames['IF_downloadTo']:frames['IF_downloadTo'];
	
	this.downloadFinished=function (){
		if(IF_downloadTo.location!='about:blank'){
			this.element.innerHTML = IF_downloadTo.document.body.innerHTML;
		}
	}
}
var O_download=new C_download();
var O_downloadTo=new C_downloadTo();

function download(sTarget, fCallback){
	IF_download=top.frames['main']?top.frames['main'].frames['IF_download']:frames['IF_download'];
	
	O_download.callback=fCallback;
	url='<?=CFG_virtualPath?>'+sTarget;
	IF_download.location.replace(url);
}
function downloadTo(sTarget, oElement){
	IF_downloadTo=top.frames['main']?top.frames['main'].frames['IF_downloadTo']:frames['IF_downloadTo'];
	
	O_downloadTo.element=oElement;
	url='<?=CFG_virtualPath?>'+sTarget;
	IF_downloadTo.location.replace(url);
}