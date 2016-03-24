  var datum = new Date(); 
  var denVTydnu = new Array("Nedìle","Pondìlí", "Úterý", "Støeda", "Ètvrtek", "Pátek", "Sobota");
  var retezec = "Dnes je "; 
  retezec += denVTydnu[datum.getDay()] + ", "; 
  retezec += datum.getDate() + "."; 
  retezec += (1 + datum.getMonth()) + ".";
  retezec += datum.getFullYear() + " "; 
  document.write( retezec ); 
</script> 
  , 
       <span id="cas"></span>
	<script language="javascript" type="text/javascript">
  function naplnCas (){
	var datum = new Date(); 
	aktualniCas = datum.getHours() + ":" + datum.getMinutes() + ":" + datum.getSeconds();
	window.document.getElementById("cas").innerHTML = aktualniCas;}
  naplnCas();
  window.setInterval("naplnCas()", 1000);