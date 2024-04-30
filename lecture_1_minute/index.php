<!DOCTYPE html>
<html lang="en">
<head>
<meta charset='utf-8'>
    <title>PDF lecture une minute</title>
    <script src='build/pdfmake.min.js'></script>
    <script src='build/vfs_fonts.js'></script>
    <script src="papaparse.js"></script>
    <script src="fetch_and_parse.js"></script>
    <link href="index_style.css" rel="stylesheet"></link>
</head>
<body>
<h3>Bienvenue sur le test de lecture en une minute</h3>

<p>
    Ce test permet d'évaluer la fluence de lecture d'un élève. Pour cela, vous allez présenter à votre élève une liste de mots qu'il devra lire du mieux possible. Vous l'arrêterez au bout d'une minute et comptabiliserez le nombre de mots correctement lus. <strong>Il est accessible à partir du mois de Janvier de CP.</strong>
</p>
<div class="liste">
<h4>Renseignez les paramètres pour générer une liste:</h4>

    
        <p>Veuillez sélectionner la classe de vos élèves:</p>
        <div>
          <input type="radio" id="classChoice1" name="class" value="CP" />
          <label for="classChoice1">CP</label>
      
          <input type="radio" id="classChoice2" name="class" value="CE1" />
          <label for="classChoice2">CE1</label>
      
          <input type="radio" id="classChoice3" name="class" value="CE2" />
          <label for="classChoice3">CE2</label>

          <input type="radio" id="classChoice4" name="class" value="CM1" />
          <label for="classChoice4">CM1</label>

          <input type="radio" id="classChoice5" name="class" value="CM2" />
          <label for="classChoice5">CM2</label>

        </div>
        <p>Quelle fréquence de mots souhaitez-vous? </p>
        <div>

          <input type="radio" id="freqChoice1" name="freq" value="frequents" />
          <label for="freqChoice1">Fréquents</label>
      
          <input type="radio" id="freqChoice2" name="freq" value="rares" />
          <label for="freqChoice2">Rares</label>

          <input type="radio" id="freqChoice3" name="freq" value="all" checked/>
          <label for="freqChoice3">Peu importe</label>
        </div>

        <p>Quelle longueur de mots souhaitez-vous?</p>
        <div>
          <input type="radio" id="lengthChoice1" name="length" value="court" />
          <label for="lengthChoice1">Courts (1-5 lettres)</label>
      
          <input type="radio" id="lengthChoice2" name="length" value="long" />
          <label for="lengthChoice2">Longs (6-8 lettres)</label>

          <input type="radio" id="lengthChoice3" name="length" value="all" checked/>
          <label for="lengthChoice3">Peu importe</label>
        </div>
        <p></br></p>
        <div>
          <button onclick="list(grade, frequency, nblettre)">Générer une liste</button>
        </div>

</div>
<h4>Interprétation des résultats</h4>
<p>
    Dans le tableau suivant, vous trouverez les attendus moyens en fin de chaque niveau.


</p>
<div style="overflow-x:auto;">
<table>
    <thead>
        <tr>
            <th scope="col"></th>
            <th scope="col">CP</th>
            <th scope="col">CE1</th>
            <th scope="col">CE2</th>
            <th scope="col">CM1</th>
            <th scope="col">CM2</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th scope="raw">Nombre de mots correctements lus en une minute</th>
            <td>50</td>
            <td>70</td>
            <td>90</td>
            <td>110</td>
            <td>120</td>
        </tr>
    </tbody>
</table>
</div>





<h4>Comment sont constituées les listes?</h4>
<p>Chacune de ces listes contient 100 mots (50 entre janvier et mars de CP) tirés aléatoirement de notre base de données qui en contient plusieurs milliers.
    Vous êtes libres de choisir la fréquence et la longueur des mots que vous souhaitez inclure, afin d'adapter la liste au niveau de vos élèves. 
    En plus de ces paramètres, les listes de CP et de CE1 commencent toujours par 20 mots dont la structure orthographique est une alternance de consonnes et de voyelles (ce sont donc des mots plus facilement déchiffrables).
</p>
<h5>Spécificité de la liste en CP</h5>
<p>Les listes de CP sont générées en suivant la progression de la méthode Kalulu développée par Excello. Au moment du téléchargement, elles comprennent donc uniquement des mots déchiffrables à cette période de l'année pour les élèves apprenant à lire avec cette méthode.</p>

<p></br></p>

    

      

<script>

let radioBtnClass = document.querySelectorAll('input[name="class"]')
let grade = ""
for (let j = 0; j < radioBtnClass.length; j++) {
    radioBtnClass[j].addEventListener("change", (event)=>{
        console.log(event.target.value)
        grade=event.target.value

    })
}

let radioBtnFreq = document.querySelectorAll('input[name="freq"]')
let frequency = "all"
for (let k = 0; k < radioBtnFreq.length; k++) {
    radioBtnFreq[k].addEventListener("change", (event)=>{
        console.log(event.target.value)
        frequency=event.target.value
    })
}

let radioBtnLength = document.querySelectorAll('input[name="length"]')
let nblettre = "all"
for (let i = 0; i < radioBtnLength.length; i++) {
    radioBtnLength[i].addEventListener("change", (event)=>{
        console.log(event.target.value)
        nblettre=event.target.value
    })
}
//console.log(nbsyll) // affiche la valeur du radio coché

function randomize(tab) {
	// function to randomize the different elements in a table
    var i, j, tmp;
    for (i = tab.length - 1; i > 0; i--) {
        j = Math.floor(Math.random() * (i + 1));
        tmp = tab[i];
        tab[i] = tab[j];
        tab[j] = tmp;
    }
    return tab;
};

function list(grade, frequency, nblettre) {
  console.log(grade)
  console.log(frequency)
  console.log(nblettre)

  stimuli=[]
  //========================== Consitution de la liste en classe de CP =====================================
  if (grade=="CP"){
    fetch_and_parse(["db_CP.csv"],
    function(parsed) {
        // ============== Identification de la periode de l'annee ==========================================
    var date = new Date();
    var mois=date.getMonth();
    //var mois=6
    console.log(mois)
    if (mois<3){
        // ============== Extraction de l'ensemble des stimuli respectant les critères =====================
        var list_facile=[] ;
        var list_difficile=[];
        if (frequency=="all"){
            if (nblettre=="all"){
                var text = grade +" - "+"Fréquence: peu importe - Longeur: peu importe";
                for (var k=0; k<parsed[0].length;k++){
                    if (parsed[0][k].PERIODE==1){
                        if (parsed[0][k].is_easy_vc==1){
                            list_facile.push('\n\n'+parsed[0][k].ORTHO)
                        }else{
                            list_difficile.push('\n\n'+parsed[0][k].ORTHO)
                        }
                    }
                    
                };
            }else{
                var text = grade +" - "+"Fréquence: peu importe - Longeur: "+nblettre;
                for (var k=0; k<parsed[0].length;k++){
                    if (parsed[0][k].nblet_mot==nblettre&&parsed[0][k].PERIODE==1){
                        if (parsed[0][k].is_easy_vc==1){
                            list_facile.push('\n\n'+parsed[0][k].ORTHO)
                        }else{
                            list_difficile.push('\n\n'+parsed[0][k].ORTHO)
                        }
                    };
                }
            };
        }else{
            if (nblettre=="all"){
                var text = grade +" - "+"Fréquence: "+frequency+" - Longeur: peu importe";
                for (var k=0; k<parsed[0].length;k++){
                    if (parsed[0][k].freq_CP==frequency&&parsed[0][k].PERIODE==1){
                        if (parsed[0][k].is_easy_vc==1){
                            list_facile.push('\n\n'+parsed[0][k].ORTHO)
                        }else{
                            list_difficile.push('\n\n'+parsed[0][k].ORTHO)
                        }
                    };
                }
            }else{
                var text = grade +" - "+"Fréquence: "+frequency+" - Longeur: "+nblettre;
                for (var k=0; k<parsed[0].length;k++){
                    if (parsed[0][k].nblet_mot==nblettre&&parsed[0][k].freq_CP==frequency&&parsed[0][k].PERIODE==1){
                        if (parsed[0][k].is_easy_vc==1){
                            list_facile.push('\n\n'+parsed[0][k].ORTHO)
                        }else{
                            list_difficile.push('\n\n'+parsed[0][k].ORTHO)
                        }
                    };
                }
            }
        }

    }else if(mois<5){
        // ============== Extraction de l'ensemble des stimuli respectant les critères =====================
        var list_facile=[] ;
        var list_difficile=[];
        if (frequency=="all"){
            if (nblettre=="all"){
                var text = grade +" - "+"Fréquence: peu importe"+" - Longeur: peu importe";
                for (var k=0; k<parsed[0].length;k++){
                    if (parsed[0][k].PERIODE<=2){
                        if (parsed[0][k].is_easy_vc==1){
                            list_facile.push('\n\n'+parsed[0][k].ORTHO)
                        }else{
                            list_difficile.push('\n\n'+parsed[0][k].ORTHO)
                        }
                    }
                    
                };
            }else{
                var text = grade +" - "+"Fréquence: peu importe"+" - Longeur: "+nblettre;
                for (var k=0; k<parsed[0].length;k++){
                    if (parsed[0][k].nblet_mot==nblettre&&parsed[0][k].PERIODE<=2){
                        if (parsed[0][k].is_easy_vc==1){
                            list_facile.push('\n\n'+parsed[0][k].ORTHO)
                        }else{
                            list_difficile.push('\n\n'+parsed[0][k].ORTHO)
                        }
                    };
                }
            };
        }else{
            if (nblettre=="all"){
                var text = grade +" - "+"Fréquence: "+frequency+" - Longeur: peu importe";
                for (var k=0; k<parsed[0].length;k++){
                    if (parsed[0][k].freq_CP==frequency&&parsed[0][k].PERIODE<=2){
                        if (parsed[0][k].is_easy_vc==1){
                            list_facile.push('\n\n'+parsed[0][k].ORTHO)
                        }else{
                            list_difficile.push('\n\n'+parsed[0][k].ORTHO)
                        }
                    };
                }
            }else{
                var text = grade +" - "+"Fréquence: "+frequency+" - Longeur: "+nblettre;
                for (var k=0; k<parsed[0].length;k++){
                    if (parsed[0][k].nblet_mot==nblettre&&parsed[0][k].freq_CP==frequency&&parsed[0][k].PERIODE<=2){
                        if (parsed[0][k].is_easy_vc==1){
                            list_facile.push('\n\n'+parsed[0][k].ORTHO)
                        }else{
                            list_difficile.push('\n\n'+parsed[0][k].ORTHO)
                        }
                    };
                }
            }
        }

    }else if(mois<6){
        // ============== Extraction de l'ensemble des stimuli respectant les critères =====================
        var list_facile=[] ;
        var list_difficile=[];
        if (frequency=="all"){
            if (nblettre=="all"){
                var text = grade +" - "+"Fréquence: peu importe"+" - Longeur: peu importe";
                for (var k=0; k<parsed[0].length;k++){
                    if (parsed[0][k].PERIODE<=3){
                        if (parsed[0][k].is_easy_vc==1){
                            list_facile.push('\n\n'+parsed[0][k].ORTHO)
                        }else{
                            list_difficile.push('\n\n'+parsed[0][k].ORTHO)
                        }
                    }
                    
                };
            }else{
                var text = grade +" - "+"Fréquence: peu importe"+" - Longeur: "+nblettre;
                for (var k=0; k<parsed[0].length;k++){
                    if (parsed[0][k].nblet_mot==nblettre&&parsed[0][k].PERIODE<=3){
                        if (parsed[0][k].is_easy_vc==1){
                            list_facile.push('\n\n'+parsed[0][k].ORTHO)
                        }else{
                            list_difficile.push('\n\n'+parsed[0][k].ORTHO)
                        }
                    };
                }
            };
        }else{
            if (nblettre=="all"){
                var text = grade +" - "+"Fréquence: "+frequency+" - Longeur: peu importe";
                for (var k=0; k<parsed[0].length;k++){
                    if (parsed[0][k].freq_CP==frequency&&parsed[0][k].PERIODE<=3){
                        if (parsed[0][k].is_easy_vc==1){
                            list_facile.push('\n\n'+parsed[0][k].ORTHO)
                        }else{
                            list_difficile.push('\n\n'+parsed[0][k].ORTHO)
                        }
                    };
                }
            }else{
                var text = grade +" - "+"Fréquence: "+frequency+" - Longeur: "+nblettre;
                for (var k=0; k<parsed[0].length;k++){
                    if (parsed[0][k].nblet_mot==nblettre&&parsed[0][k].freq_CP==frequency&&parsed[0][k].PERIODE<=3){
                        if (parsed[0][k].is_easy_vc==1){
                            list_facile.push('\n\n'+parsed[0][k].ORTHO)
                        }else{
                            list_difficile.push('\n\n'+parsed[0][k].ORTHO)
                        }
                    };
                }
            }
        }


    }else{
                // ============== Extraction de l'ensemble des stimuli respectant les critères =====================
                var list_facile=[] ;
        var list_difficile=[];
        if (frequency=="all"){
            if (nblettre=="all"){
                var text = grade +" - "+"Fréquence: peu importe"+" - Longeur: peu importe";
                for (var k=0; k<parsed[0].length;k++){
                    if (parsed[0][k].PERIODE<=4){
                        if (parsed[0][k].is_easy_vc==1){
                            list_facile.push('\n\n'+parsed[0][k].ORTHO)
                        }else{
                            list_difficile.push('\n\n'+parsed[0][k].ORTHO)
                        }
                    }
                    
                };
            }else{
                var text = grade +" - "+"Fréquence: peu importe"+" - Longeur: "+nblettre;
                for (var k=0; k<parsed[0].length;k++){
                    if (parsed[0][k].nblet_mot==nblettre&&parsed[0][k].PERIODE<=4){
                        if (parsed[0][k].is_easy_vc==1){
                            list_facile.push('\n\n'+parsed[0][k].ORTHO)
                        }else{
                            list_difficile.push('\n\n'+parsed[0][k].ORTHO)
                        }
                    };
                }
            };
        }else{
            if (nblettre=="all"){
                var text = grade +" - "+"Fréquence: "+frequency+" - Longeur: peu importe";
                for (var k=0; k<parsed[0].length;k++){
                    if (parsed[0][k].freq_CP==frequency&&parsed[0][k].PERIODE<=4){
                        if (parsed[0][k].is_easy_vc==1){
                            list_facile.push('\n\n'+parsed[0][k].ORTHO)
                        }else{
                            list_difficile.push('\n\n'+parsed[0][k].ORTHO)
                        }
                    };
                }
            }else{
                var text = grade +" - "+"Fréquence: "+frequency+" - Longeur: "+nblettre;
                for (var k=0; k<parsed[0].length;k++){
                    if (parsed[0][k].nblet_mot==nblettre&&parsed[0][k].freq_CP==frequency&&parsed[0][k].PERIODE<=4){
                        if (parsed[0][k].is_easy_vc==1){
                            list_facile.push('\n\n'+parsed[0][k].ORTHO)
                        }else{
                            list_difficile.push('\n\n'+parsed[0][k].ORTHO)
                        }
                    };
                }
            }
        }


    }

    if(mois<3){
        // ============== Extraction d'une sous-liste de 100 items ====================================
        shuffled_list_facile=randomize(list_facile);
        //shuffled_list_difficile=randomize(list_difficile);
        stimuli_facile = shuffled_list_facile.slice(0,20);
        list_autre=list_difficile.concat(shuffled_list_facile.slice(20))
        shuffled_list_autre=randomize(list_autre)
        stimuli_autre=shuffled_list_autre.slice(0,80)
        console.log(stimuli)

        stimuli_line1 = stimuli_facile.slice(0,5);
        stimuli_line2 = stimuli_facile.slice(5,10);
        stimuli_line3 = stimuli_facile.slice(10,15);
        stimuli_line4 = stimuli_facile.slice(15,20);
        stimuli_line5 = stimuli_autre.slice(0,5);
        stimuli_line6 = stimuli_autre.slice(5,10);
        stimuli_line7 = stimuli_autre.slice(10,15);
        stimuli_line8 = stimuli_autre.slice(15,20);
        stimuli_line9 = stimuli_autre.slice(20,25);
        stimuli_line10 = stimuli_autre.slice(25,30);

        var docDefinition = {
	content: [
        {
			text: text,
			style: 'header'
		},
		
		{
			style: 'bigger',
			table: {
                widths: ['*', '*', '*', '*', '*'],
                heights: 50,
                dontBreakRows: true,
                
				body: [
					stimuli_line1,
					stimuli_line2,
                    stimuli_line3,
                    stimuli_line4,
                    stimuli_line5,
                    stimuli_line6,
                    stimuli_line7,
                    stimuli_line8,
                    stimuli_line9,
                    stimuli_line10

				]
                
			},
            layout: {
				hLineWidth: function (i, node) {
					return (i === 0 || i===12 || i === node.table.body.length) ? 0 : 1;
				},
                vLineWidth: function (i, node) {
					return (i === 0 || i === node.table.widths.length) ? 0 : 0;
				},
				hLineColor: function (i, node) {
					return (i === 0) ? 'gray' : 'gray';
				},
			}
		},
    ],
    styles: {
		header: {
			fontSize: 8,
			bold: false,
            italics: true
		},
		bigger: {
			fontSize: 16,
			italics: false,
            alignment: 'center'
		}
	},
    defaultStyle: {
		alignment: 'centered'
	}
};

    pdfMake.createPdf(docDefinition).download('liste_lecture_1_minute.pdf');
    //pdfMake.createPdf(docDefinition).open();



    }else{
        // ============== Extraction d'une sous-liste de 100 items ====================================
        shuffled_list_facile=randomize(list_facile);
        //shuffled_list_difficile=randomize(list_difficile);
        stimuli_facile = shuffled_list_facile.slice(0,20);
        list_autre=list_difficile.concat(shuffled_list_facile.slice(20))
        shuffled_list_autre=randomize(list_autre)
        stimuli_autre=shuffled_list_autre.slice(0,80)
        console.log(stimuli)

        // ============== Constitution de chaque ligne ================================================
        stimuli_line1 = stimuli_facile.slice(0,5);
        stimuli_line2 = stimuli_facile.slice(5,10);
        stimuli_line3 = stimuli_facile.slice(10,15);
        stimuli_line4 = stimuli_facile.slice(15,20);
        stimuli_line5 = stimuli_autre.slice(0,5);
        stimuli_line6 = stimuli_autre.slice(5,10);
        stimuli_line7 = stimuli_autre.slice(10,15);
        stimuli_line8 = stimuli_autre.slice(15,20);
        stimuli_line9 = stimuli_autre.slice(20,25);
        stimuli_line10 = stimuli_autre.slice(25,30);
        stimuli_line11 = stimuli_autre.slice(30,35);
        stimuli_line12 = stimuli_autre.slice(35,40);
        stimuli_line13 = stimuli_autre.slice(40,45);
        stimuli_line14 = stimuli_autre.slice(45,50);
        stimuli_line15 = stimuli_autre.slice(50,55);
        stimuli_line16 = stimuli_autre.slice(55,60);
        stimuli_line17 = stimuli_autre.slice(60,65);
        stimuli_line18 = stimuli_autre.slice(65,70);
        stimuli_line19 = stimuli_autre.slice(70,75);
        stimuli_line20 = stimuli_autre.slice(75,80);


   var docDefinition = {
	content: [
        {
			text: text,
			style: 'header'
		},
		
		{
			style: 'bigger',
			table: {
                widths: ['*', '*', '*', '*', '*'],
                heights: 50,
                dontBreakRows: true,
                
				body: [
					stimuli_line1,
					stimuli_line2,
                    stimuli_line3,
                    stimuli_line4,
                    stimuli_line5,
                    stimuli_line6,
                    stimuli_line7,
                    stimuli_line8,
                    stimuli_line9,
                    stimuli_line10,
                    stimuli_line11,
                    stimuli_line12,
                    stimuli_line13,
                    stimuli_line14,
                    stimuli_line15,
                    stimuli_line16,
                    stimuli_line17,
                    stimuli_line18,
                    stimuli_line19,
                    stimuli_line20

				]
                
			},
            layout: {
				hLineWidth: function (i, node) {
					return (i === 0 || i===12 || i === node.table.body.length) ? 0 : 1;
				},
                vLineWidth: function (i, node) {
					return (i === 0 || i === node.table.widths.length) ? 0 : 0;
				},
				hLineColor: function (i, node) {
					return (i === 0) ? 'gray' : 'gray';
				},
			}
		},
    ],
    styles: {
		header: {
			fontSize: 8,
            bold: false,
            italics: true
		},
		bigger: {
			fontSize: 16,
			italics: false,
            alignment: 'center'
		}
	},
    defaultStyle: {
		alignment: 'centered'
	}
};

    pdfMake.createPdf(docDefinition).download('liste_lecture_1_minute.pdf');
    //pdfMake.createPdf(docDefinition).open();
    }
        
    })
  }
  //========================== Consitution de la liste en classe de CE1 =====================================

  else if(grade == "CE1"){    
    fetch_and_parse(["db_CE1_CM2.csv"],
    function(parsed) {
        // ============== Extraction de l'ensemble des stimuli respectant les critères =====================
        var list_facile=[] ;
        var list_difficile=[];
        if (frequency=="all"){
            if (nblettre=="all"){
                var text = grade +" - "+"Fréquence: peu importe"+" - Longeur: peu importe";
                for (var k=0; k<parsed[0].length;k++){
                    if (parsed[0][k].is_easy_vc==1){
                        list_facile.push('\n\n'+parsed[0][k].ORTHO)
                    }else{
                        list_difficile.push('\n\n'+parsed[0][k].ORTHO)
                    }
                    
                };
            }else{
                var text = grade +" - "+"Fréquence: peu importe"+" - Longeur: "+nblettre;
                for (var k=0; k<parsed[0].length;k++){
                    if (parsed[0][k].nblet_mot==nblettre){
                        if (parsed[0][k].is_easy_vc==1){
                            list_facile.push('\n\n'+parsed[0][k].ORTHO)
                        }else{
                            list_difficile.push('\n\n'+parsed[0][k].ORTHO)
                        }
                    };
                }
            };
        }else{
            if (nblettre=="all"){
                var text = grade +" - "+"Fréquence: "+frequency+" - Longeur: peu importe";
                for (var k=0; k<parsed[0].length;k++){
                    if (parsed[0][k].freq_CE1==frequency){
                        if (parsed[0][k].is_easy_vc==1){
                            list_facile.push('\n\n'+parsed[0][k].ORTHO)
                        }else{
                            list_difficile.push('\n\n'+parsed[0][k].ORTHO)
                        }
                    };
                }
            }else{
                var text = grade +" - "+"Fréquence: "+frequency+" - Longeur: "+nblettre;
                for (var k=0; k<parsed[0].length;k++){
                    if (parsed[0][k].nblet_mot==nblettre&&parsed[0][k].freq_CE1==frequency){
                        if (parsed[0][k].is_easy_vc==1){
                            list_facile.push('\n\n'+parsed[0][k].ORTHO)
                        }else{
                            list_difficile.push('\n\n'+parsed[0][k].ORTHO)
                        }
                    };
                }
            }
        }
            
        // ============== Extraction d'une sous-liste de 100 items ====================================
        shuffled_list_facile=randomize(list_facile);
        //shuffled_list_difficile=randomize(list_difficile);
        stimuli_facile = shuffled_list_facile.slice(0,20);
        list_autre=list_difficile.concat(shuffled_list_facile.slice(20))
        shuffled_list_autre=randomize(list_autre)
        stimuli_autre=shuffled_list_autre.slice(0,80)
        console.log(stimuli)

        // ============== Constitution de chaque ligne ================================================
        stimuli_line1 = stimuli_facile.slice(0,5);
        stimuli_line2 = stimuli_facile.slice(5,10);
        stimuli_line3 = stimuli_facile.slice(10,15);
        stimuli_line4 = stimuli_facile.slice(15,20);
        stimuli_line5 = stimuli_autre.slice(0,5);
        stimuli_line6 = stimuli_autre.slice(5,10);
        stimuli_line7 = stimuli_autre.slice(10,15);
        stimuli_line8 = stimuli_autre.slice(15,20);
        stimuli_line9 = stimuli_autre.slice(20,25);
        stimuli_line10 = stimuli_autre.slice(25,30);
        stimuli_line11 = stimuli_autre.slice(30,35);
        stimuli_line12 = stimuli_autre.slice(35,40);
        stimuli_line13 = stimuli_autre.slice(40,45);
        stimuli_line14 = stimuli_autre.slice(45,50);
        stimuli_line15 = stimuli_autre.slice(50,55);
        stimuli_line16 = stimuli_autre.slice(55,60);
        stimuli_line17 = stimuli_autre.slice(60,65);
        stimuli_line18 = stimuli_autre.slice(65,70);
        stimuli_line19 = stimuli_autre.slice(70,75);
        stimuli_line20 = stimuli_autre.slice(75,80);


   var docDefinition = {
	content: [
        {
			text: text,
			style: 'header'
		},
		
		{
			style: 'bigger',
			table: {
                widths: ['*', '*', '*', '*', '*'],
                heights: 50,
                dontBreakRows: true,
                
				body: [
					stimuli_line1,
					stimuli_line2,
                    stimuli_line3,
                    stimuli_line4,
                    stimuli_line5,
                    stimuli_line6,
                    stimuli_line7,
                    stimuli_line8,
                    stimuli_line9,
                    stimuli_line10,
                    stimuli_line11,
                    stimuli_line12,
                    stimuli_line13,
                    stimuli_line14,
                    stimuli_line15,
                    stimuli_line16,
                    stimuli_line17,
                    stimuli_line18,
                    stimuli_line19,
                    stimuli_line20

				]
                
			},
            layout: {
				hLineWidth: function (i, node) {
					return (i === 0 || i===12 || i === node.table.body.length) ? 0 : 1;
				},
                vLineWidth: function (i, node) {
					return (i === 0 || i === node.table.widths.length) ? 0 : 0;
				},
				hLineColor: function (i, node) {
					return (i === 0) ? 'gray' : 'gray';
				},
			}
		},
    ],
    styles: {
		header: {
			fontSize: 8,
			bold: false,
            italics: true
		},
		bigger: {
			fontSize: 16,
			italics: false,
            alignment: 'center'
		}
	},
    defaultStyle: {
		alignment: 'centered'
	}
};

    pdfMake.createPdf(docDefinition).download('liste_lecture_1_minute.pdf');
    //pdfMake.createPdf(docDefinition).open();
})

  }


  //========================== Constitution de la liste pour les autres classes ============================
  else {
    fetch_and_parse(["db_CE1_CM2.csv"],
    function(parsed) {
        // ============== Extraction de l'ensemble des stimuli respectant les critères =====================
        var list=[] ;
        if (frequency=="all"){
            if (nblettre=="all"){
                var text = grade +" - "+"Fréquence: peu importe"+" - Longeur: peu importe";
                for (var k=0; k<parsed[0].length;k++){
                    list.push('\n\n'+parsed[0][k].ORTHO)
                };
            }else{
                var text = grade +" - "+"Fréquence: peu importe"+" - Longeur: "+nblettre;
                for (var k=0; k<parsed[0].length;k++){
                    if (parsed[0][k].nblet_mot==nblettre){
                    list.push('\n\n'+parsed[0][k].ORTHO)
                    };
                }
            };
        }else{
            if (nblettre=="all"){
                var text = grade +" - "+"Fréquence: "+frequency+" - Longeur: peu importe";
                for (var k=0; k<parsed[0].length;k++){
                    if (parsed[0][k].freq_CE2_CM2==frequency){
                    list.push('\n\n'+parsed[0][k].ORTHO)
                    };
                }
            }else{
                var text = grade +" - "+"Fréquence: "+frequency+" - Longeur: "+nblettre;
                for (var k=0; k<parsed[0].length;k++){
                    if (parsed[0][k].nblet_mot==nblettre&&parsed[0][k].freq_CE2_CM2==frequency){
                    list.push('\n\n'+parsed[0][k].ORTHO)
                    };
                }
            }
        }
            
        // ============== Extraction d'une sous-liste de 100 items ====================================
        shuffled_list=randomize(list);
        stimuli = shuffled_list.slice(0,100);
        console.log(stimuli)

        // ============== Constitution de chaque ligne ================================================
        stimuli_line1 = stimuli.slice(0,5);
        stimuli_line2 = stimuli.slice(5,10);
        stimuli_line3 = stimuli.slice(10,15);
        stimuli_line4 = stimuli.slice(15,20);
        stimuli_line5 = stimuli.slice(20,25);
        stimuli_line6 = stimuli.slice(25,30);
        stimuli_line7 = stimuli.slice(30,35);
        stimuli_line8 = stimuli.slice(35,40);
        stimuli_line9 = stimuli.slice(40,45);
        stimuli_line10 = stimuli.slice(45,50);
        stimuli_line11 = stimuli.slice(50,55);
        stimuli_line12 = stimuli.slice(55,60);
        stimuli_line13 = stimuli.slice(60,65);
        stimuli_line14 = stimuli.slice(65,70);
        stimuli_line15 = stimuli.slice(70,75);
        stimuli_line16 = stimuli.slice(75,80);
        stimuli_line17 = stimuli.slice(80,85);
        stimuli_line18 = stimuli.slice(85,90);
        stimuli_line19 = stimuli.slice(90,95);
        stimuli_line20 = stimuli.slice(95,100);


   var docDefinition = {
	content: [
        {
			text: text,
			style: 'header'
		},
		
		{
			style: 'bigger',
			table: {
                widths: ['*', '*', '*', '*', '*'],
                heights: 50,
                dontBreakRows: true,
				body: [
					stimuli_line1,
					stimuli_line2,
                    stimuli_line3,
                    stimuli_line4,
                    stimuli_line5,
                    stimuli_line6,
                    stimuli_line7,
                    stimuli_line8,
                    stimuli_line9,
                    stimuli_line10,
                    stimuli_line11,
                    stimuli_line12,
                    stimuli_line13,
                    stimuli_line14,
                    stimuli_line15,
                    stimuli_line16,
                    stimuli_line17,
                    stimuli_line18,
                    stimuli_line19,
                    stimuli_line20

				]
                
			},
            layout: {
				hLineWidth: function (i, node) {
					return (i === 0 || i===12 || i === node.table.body.length) ? 0 : 1;
				},
                vLineWidth: function (i, node) {
					return (i === 0 || i === node.table.widths.length) ? 0 : 0;
				},
				hLineColor: function (i, node) {
					return (i === 0) ? 'gray' : 'gray';
				},
			}
            
		},
    ],
    styles: {
		header: {
			fontSize: 8,
			bold: false,
            italics: true
		},
		bigger: {
			fontSize: 16,
			italics: false,
            alignment: 'center'
		}
	},
    defaultStyle: {
		alignment: 'centered'
	}
};

    pdfMake.createPdf(docDefinition).download('liste_lecture_1_minute.pdf');
    //pdfMake.createPdf(docDefinition).open();
})

  }

}
    
</script>



</body>
</html>
