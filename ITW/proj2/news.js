/*
 * - funkce zjistuje viditelnost elementu a tuto hodnotu invertuje
 * - zaroven uklada hodnotu viditelnosti do susenky, stejne jako datum jeji expirace
 * - po nacteni stranky se pouzije posledni ulozena hodnota viditelnosti, cimz se docili
 *   obnoveni stavu po prechozim zavreni stranky
 */
function visibility(element)
{
  if(document.getElementById(element).style.visibility == "hidden")
  {
    document.getElementById(element).style.visibility = "visible";
    document.cookie = "news=visible; expires=Sat, 30 Jun 2018 23:59:59 GMT";
  }
  else
  {
    document.getElementById(element).style.visibility = 'hidden';
    document.cookie = "news=hidden; expires=Sat, 30 Jun 2018 23:59:59 GMT";
  }
}

function SetSetting()
{
  // rozdelime susenku podle ';'
  splited = document.cookie.split("; ");

  for(i in splited)
  {
    // rozdelime susenku podle '=' => na nultem indexu je jmeno susenky, na prvnim indexu je hodnota
    cookie = splited[i].split("=");
    if(cookie[0] == "news") document.getElementById("news").style.visibility = cookie[1];
  }
}
