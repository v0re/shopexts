<script type="text/javascript">



  /*
  Autor: Jorge Ortiz Giraldo
  e-mail: jortizg@hotmail.com
  website: jortizg.tripod.com
  Empresa: WebSys (Medellín - Colombia)
  Version 1.0 : Fecha: 31/marzo/2000
  Version 1.1 : Fecha: 16/junio/2000
  Mejoras introducidas en esta versión:
  - Ahora funciona también en Internet Explorer (v5).
  - Puede usarse este control varias veces en una misma página.
  - Escribe automáticamente las fechas elegidas, en los campos designados.
  - Permite asignar un título a la ventana de calendario.
  - Resalta en rojo el día de la fecha actual.
  - Presenta la fecha actual en un botón que permite elegirla inmediatamente.
  Licenciamiento: La propiedad de este script corresponde unica y exclusivamente a su autor, quien concede
  a otros usuarios la posibilidad de usarlo libremente si y solo si se conservan estas lineas
  de autoría.  Ni el autor ni su empresa ofrecen ninguna garantía sobre el funcionamiento de este
  script ni se hacen responsables por fallas ocasionadas por su uso.
  */

  nombresMes = Array("","january","february","march","april","may","june","july","august","september","october","november","december");

  var anoInicial = 1900;
  var anoFinal = 2100;
  var ano;
  var mes;
  var dia;
  var campoDeRetorno;
  var titulo;

  function diasDelMes(ano,mes) {
	         if ((mes==1)||(mes==3)||(mes==5)||(mes==7)||(mes==8)||(mes==10)||(mes==12)) dias=31
			   else if ((mes==4)||(mes==6)||(mes==9)||(mes==11)) dias=31
				     else if ((((ano % 100)==0) && ((ano % 400)==0)) || (((ano % 100)!=0) && ((ano % 4)==0))) dias = 29
					       else dias = 28;
					         return dias;
  };

  function crearSelectorMes(mesActual) {
	    var selectorMes = "";
	      selectorMes = "<select name='mes' size='1' onChange='javascript:opener.dibujarMes(self.document.Forma1.ano[self.document.Forma1.ano.selectedIndex].value,self.document.Forma1.mes[self.document.Forma1.mes.selectedIndex].value);'>\r\n";
	        for (var i=1; i<=12; i++) {
			    selectorMes = selectorMes + "  <option value='" + i + "'";
			        if (i == mesActual) selectorMes = selectorMes + " selected";
				    selectorMes = selectorMes + ">" + nombresMes[i] + "</option>\r\n";
				      }
				        selectorMes = selectorMes + "</select>\r\n";
					  return selectorMes;
  }

  function crearSelectorAno(anoActual) {
	    var selectorAno = "";
	      selectorAno = "<select name='ano' size='1' onChange='javascript:opener.dibujarMes(self.document.Forma1.ano[self.document.Forma1.ano.selectedIndex].value,self.document.Forma1.mes[self.document.Forma1.mes.selectedIndex].value);'>\r\n";
	        for (var i=anoInicial; i<=anoFinal; i++) {
			    selectorAno = selectorAno + "  <option value='" + i + "'";
			        if (i == anoActual) selectorAno = selectorAno + " selected";
				    selectorAno = selectorAno + ">" + i + "</option>\r\n";
				      }
				        selectorAno = selectorAno + "</select>";
					  return selectorAno;
  }

  function crearTablaDias(numeroAno,numeroMes) {
	    var tabla = "<table border='0' cellpadding='2' cellspacing='0' bgcolor='#ffffff'>\r\n  <tr>";
	      var fechaInicio = new Date();
	        fechaInicio.setYear(numeroAno);
		  fechaInicio.setMonth(numeroMes-1);
		    fechaInicio.setDate(1);
		      ajuste = fechaInicio.getDay();
		        tabla = tabla + "\r\n    <td align='center'>Su</td><td align='center'>Mo</td><td align='center'>Tu</td><td align='center'>We</td><td align='center'>Th</td><td align='center'>Fr</td><td align='center'>Sa</td></div>\r\n  <tr>";
			  for (var j=1; j<=ajuste; j++) {
				      tabla = tabla + "\r\n    <td></td>";
				        }
					  for (var i=1; i<10; i++) {
						      tabla = tabla + "\r\n    <td"
						          if ((i == diaHoy()) && (numeroMes == mesHoy()) && (numeroAno == anoHoy())) tabla = tabla + " bgcolor='#ff0000'";
							      tabla = tabla + "><input type='button' value='0" + i + "' onClick='javascript:opener.ano=self.document.Forma1.ano[self.document.Forma1.ano.selectedIndex].value; opener.mes=self.document.Forma1.mes[self.document.Forma1.mes.selectedIndex].value; opener.dia=" + i + "; self.close();'></td>";
							          if (((i+ajuste) % 7)==0) tabla = tabla + "\r\n  </tr>\r\n\  <tr>";
								    }
								      for (var i=10; i<=diasDelMes(numeroAno,numeroMes); i++) {
									          tabla = tabla + "\r\n    <td"
										      if ((i == diaHoy()) && (numeroMes == mesHoy()) && (numeroAno == anoHoy())) tabla = tabla + " bgcolor='#ff0000'";
										          tabla = tabla + "><input type='button' value='" + i + "' onClick='javascript:opener.ano=self.document.Forma1.ano[self.document.Forma1.ano.selectedIndex].value; opener.mes=self.document.Forma1.mes[self.document.Forma1.mes.selectedIndex].value; opener.dia=" + i + "; self.close();'></td>";
											      if (((i+ajuste) % 7)==0) tabla = tabla + "\r\n  </tr>\r\n\  <tr>";
											        }
												  tabla = tabla + "\r\n  </tr>\r\n</table>";
												    return tabla;
  }

  function dibujarMes(numeroAno,numeroMes) {
	    var html = "";
	      html = html + "<html>\r\n<head>\r\n  <title>" + titulo + "</title>\r\n</head>\r\n<body bgcolor='#ffffff' onUnload='opener.escribirFecha();'>\r\n  <div align='center'>\r\n  <form name='Forma1'>\r\n";
	        html = html + crearSelectorMes(numeroMes);
		  html = html + crearSelectorAno(numeroAno);
		    html = html + crearTablaDias(numeroAno,numeroMes);
		      html = html + "<center><p><input type='button' name='hoy' value='today: " + ano + "-" + mes + "-" + dia + "' onClick='javascript:self.close();'></center>";
		        html = html + "\r\n  </form>\r\n  </div>\r\n</body>\r\n</html>\r\n";
			  ventana = open("","calendario","width=220,height=270");
			    ventana.document.open();
			      ventana.document.writeln(html);
			        ventana.document.close();
				  ventana.focus();
  }

  function anoHoy() {
	    var fecha = new Date();
	      if (navigator.appName == "Netscape") return fecha.getYear() + 1900
		        else return fecha.getYear();
  }

  function mesHoy() {
	    var fecha = new Date();
	      return fecha.getMonth()+1;
  }

  function diaHoy() {
	    var fecha = new Date();
	      return fecha.getDate();
  }

  function pedirFecha(campoTexto,nombreCampo) {
	    ano = anoHoy();
	      mes = mesHoy();
	        dia = diaHoy();
		  campoDeRetorno = campoTexto;
		    titulo = nombreCampo;
		      dibujarMes(ano,mes);
  }

  function escribirFecha() {
	    campoDeRetorno.value = ano + "-" + mes + "-" + dia;
  }
function open_wizz() {

window.open("wiz_start.php,"Fenster12","width=710,height=550,left=100,top=100");
}
    </script>
