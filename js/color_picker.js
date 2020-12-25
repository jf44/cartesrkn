// JavaScript Document
//	convertion decimal ver hexa
function Hexa(Dec){
	if ((Dec>=0) & (Dec<=255))
	{
		var nb = Dec.toString(16);
		if (nb.length < 2)
		{
			nb = "0" + nb;
		}
		return(nb);
	}
	return "00";
}

function verifHexa(acolor){
	if (acolor != "")
	{
		var re = /^[0-9a-f]{6}$/;
		if (re.test(acolor) !== null)
		{
			return true;
		}
	}
	return false;
}

//
function GradientPart(dr, dg, db, fr, fg, fb, Step) {
	cr=dr;cg=dg;cb=db
	//	Calcul du pas par couleur
	sr=((fr-dr)/Step)	// rouge
	sg=((fg-dg)/Step)	// vert
	sb=((fb-db)/Step)	// bleu
	var Result = ''
    for (var x = 0; x <= Step; x++)
	{
		var cmd = " onclick=\"ColorCode.value=this.bgColor; placerUneCouleur(this.bgColor);\" onmouseover=\"ColorShow.style.backgroundColor=this.bgColor;\""
        var a_color = Hexa(Math.floor(cr)) + Hexa(Math.floor(cg)) + Hexa(Math.floor(cb));
        if (verifHexa(a_color))
		{
			Result += "<td class=ColorCell bgcolor=" + a_color + cmd + "></td>";
		}
		cr += sr; cg += sg; cb += sb
	}
	return(Result)
}

function WriteRow(a,i){
	document.write("<tr>")
	document.write(GradientPart(a,i,i, a,a,i, StepH))
	document.write(GradientPart(a,a,i ,i,a,i, StepH))
	document.write(GradientPart(i,a,i, i,a,a, StepH))
	document.write(GradientPart(i,a,a, i,i,a, StepH))
	document.write(GradientPart(i,i,a, a,i,a, StepH))
	document.write(GradientPart(a,i,a, a,i,i, StepH))
	document.write("</tr>")
}

function WriteRow2(a,i){
var s ="";
	s += "<tr>";
	s += GradientPart(a,i,i, a,a,i, StepH);
	s += GradientPart(a,a,i ,i,a,i, StepH);
	s += GradientPart(i,a,i, i,a,a, StepH);
	s += GradientPart(i,a,a, i,i,a, StepH);
	s += GradientPart(i,i,a, a,i,a, StepH);
	s += GradientPart(a,i,a, a,i,i, StepH);
	s += "</tr>\n";
	return s;
}
