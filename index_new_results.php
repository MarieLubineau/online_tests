
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Decision Lexicale</title>

<!-- Scripts to be used for the stimuli to parse the csv files -->
    <script src="papaparse.js"></script>
    <script src="fetch_and_parse.js"></script>

<!-- Scripts to be used to draw graphs -->
	<script src="Chart.bundle.js"></script>

<!--JsPsych plugins -->
	<script src="jspsych-6.1.0/jspsych.js"></script>
	<script src="jspsych-6.1.0/plugins/jspsych-html-keyboard-response.js"></script>
	<script src="jspsych-6.1.0/plugins/jspsych-html-keyboard-response-animate.js"></script>
	<script src="jspsych-6.1.0/plugins/jspsych-external-html.js"></script>
	<script src="jspsych-6.1.0/plugins/jspsych-html-button-response.js"></script>
	<script src="jspsych-6.1.0/plugins/jspsych-instructions.js"></script>
	<script src="jspsych-6.1.0/plugins/jspsych-fullscreen.js"></script>
	<script src="jspsych-6.1.0/plugins/jspsych-survey-multi-choice.js"></script>
	<link href="jspsych-6.1.0/css/jspsych.css" rel="stylesheet" type="text/css"></link>

<!--Stylesheet -->
	<link href="styleglobal_new_results.css" rel="stylesheet"></link>
  </head>


<!-- Organisation of the screen -->
<body>


	<div id='exp'>
		<div id='cheminee' >
			<img id="image_cheminee" src="cheminee.jpg">
		</div>
		<div id="jspsych-experiment" >
		</div>
		<div id='dictionnaire'>
			<img id="image_dictionnaire" src="dictionnaire.jpg">
	   </div>
	</div>

</body>

<!-- Experiment -->
 	<script>

/*Explanations about the organisation of the following code:
		- the first part is dedicated to the definition of different variables that are used in the code
		- the second part is the definition of functions that are going to be used after in the code
		- the third part is dedicated to the experiment
*/

// ======================================= Global variables for the code ======================================================================================
nsamples_for_each_combination_of_freqency_and_length =3;
nsample_for_each_length = 2;
stimuli =[];
alasuite=0;

// ======================================= Global variables for the code ======================================================================================
number_of_words_for_each_frequency=15;
number_of_words_for_each_number_of_letter=12;
number_of_pseudowords_for_each_type=10;


// ======================================= Functions ==========================================================================================================
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

function mean(tab){
	// function that calculates the mean of the values of a table
	var moy=0;
	for (var i=0 ; i<tab.length ; i++){
		moy=moy+tab[i];
	}
	moy=moy/tab.length;
	return moy;
};

function display_errors (nb_errors, errors_to_display) {
	// function that will display the errors on pseudo words made by the child
	tableau='<table><tr>';
	if (nb_errors<5){
		for (var k=0 ; k<nb_errors; k++){

			tableau=tableau+'<td class="table_pseudo">'+errors_to_display[k]+'</td>';
		}
		tableau=tableau+'</tr></table>';
	}
	else{
		if (nb_errors%2==0){
			for (var k=0 ; k<nb_errors/2; k++){
				tableau=tableau+'<td class="table_pseudo">'+errors_to_display[k]+'</td>';
			}
		tableau=tableau+'</tr><tr>';
			for (var k=nb_errors/2 ; k<nb_errors; k++){
				tableau=tableau+'<td class="table_pseudo">'+errors_to_display[k]+'</td>';
			}
		tableau=tableau+'</tr></table>';

		}else{
			for (var k=0 ; k<Math.floor(nb_errors/2)+1; k++){
				tableau=tableau+'<td class="table_pseudo">'+errors_to_display[k]+'</td>';
			}
		tableau=tableau+'</tr><tr>';
			for (var k=Math.floor(nb_errors/2)+1 ; k<nb_errors; k++){
				tableau=tableau+'<td class="table_pseudo">'+errors_to_display[k]+'</td>';
			}
		tableau=tableau+'</tr></table>';

		}
	}
	return tableau
};

function saveData(name, data){
	console.log("We entred the function")
  var xhr = new XMLHttpRequest();
  xhr.open('POST', 'write_data.php'); // 'write_data.php' is the path to the php file described above.
  xhr.setRequestHeader('Content-Type', 'text/csv');
  xhr.send(data);
}

// This sends a well-formated request to save the data on the server
/*sendData = function(uuid, user, project, data) {
  var xhr = new XMLHttpRequest();
  xhr.open("POST", "https://neurospin-data.cea.fr/experiments", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.send("uuid="+uuid+"&user="+user+"&project="+project+"&data="+data);
}*/

// ======================================= Audio stuff ===========================================================================================================

// Sounds for the instructions
consigne_1=new Audio('consignes.wav');
consigne_2=new Audio ('start.wav');
lila= new Audio('lila.wav');
ruj=new Audio ('ruj.wav');
fin=new Audio ('fin.wav');
rappel=new Audio('full_screen.wav');
moitie=new Audio ('moitie.wav');

// feedback for the player
pos1 = new Audio('pos1.wav');
pos2 = new Audio('pos2.wav');
pos3 = new Audio('pos3.wav');
pos4 = new Audio('pos4.wav');
neg1 = new Audio('neg1.wav');

consigne_1.load();
consigne_2.load();
rappel.load();
moitie.load();
pos1.load()
pos2.load()
pos3.load()
pos4.load()

neg1.load()
lila.load();
ruj.load();
fin.load();
pos_feed=[pos1,pos2,pos3,pos4];

// ======================================= Experiment ==============================================================================================================

// Test: parse two files, and when parsed log the data
fetch_and_parse(["selectwords_with_manual_edits_DO_NOT_DELETE.csv", "transposed_with_manual_edits_DO_NOT_DELETE.csv", "substi2_with_manual_edits_DO_NOT_DELETE.csv","pseudo_with_manual_edits_DO_NOT_DELETE.csv","homo_with_manual_edits_DO_NOT_DELETE.csv","mirrored_with_manual_edits_DO_NOT_DELETE.csv","substi1_with_manual_edits_DO_NOT_DELETE.csv"],
  function(parsed) {
	  entrainement=[]; // list where to put the stimuli for the train part of the task

    // choice of the stimuli inside our data

	// ==================== Choice of the words =====================================
	for (var freq=1 ; freq<5; freq++){
		for (var wordlength=4; wordlength<9; wordlength++){
		var list=[] ;// list of words that correspond to a certain word_length and a certain frequence
			for (var k=0; k<parsed[0].length;k++){
				if (parsed[0][k].nblettres==wordlength && parsed[0][k].ifreq==freq){
					list.push(parsed[0][k])
				};
			};
		shuffled_list=randomize(list);
		for (var l=0; l<nsamples_for_each_combination_of_freqency_and_length; l++){
			stimuli.push(shuffled_list[l]); // selection inside the list of the stimuli that are going to be used in the experiment
		};

		if (freq==4 && wordlength==4){
			entrainement.push(shuffled_list[nsamples_for_each_combination_of_freqency_and_length]); // selection inside the list of the train stimuli
			entrainement.push(shuffled_list[nsamples_for_each_combination_of_freqency_and_length+1]);
			entrainement.push(shuffled_list[nsamples_for_each_combination_of_freqency_and_length+2]);
		};

		};
	};

	// ================== Choice of the non-words ====================================
	for (var wordlength=4 ; wordlength<9 ; wordlength++){
		for (var i=1;i<7;i++){
			var list1=[]; // list of words that correspond to a certain wordlength in each csv file of non words
			for (var k=0;k<parsed[i].length;k++){
				if (parsed[i][k].nblettres==wordlength){
					list1.push(parsed[i][k]);
				};

			};
			shuffled_list1=randomize(list1);
			for (var l=0 ; l<nsample_for_each_length ; l++){
				stimuli.push(shuffled_list1[l]);
			};

			if (wordlength==4 && i==3){
				entrainement.push(shuffled_list1[nsample_for_each_length]);
				entrainement.push(shuffled_list1[nsample_for_each_length+1]);
				entrainement.push(shuffled_list1[nsample_for_each_length+2]);
			}

		};

	};

	// randomization of each list (train and stimuli)
	entrainement=randomize(entrainement);
	stimuli=randomize(stimuli);

	// we attribute to each subject a random ID to identify him that is linked to its php session id
	var subjectID = jsPsych.randomization.randomID(15);
	jsPsych.data.addProperties({'ID': subjectID});
	jsPsych.data.addProperties({'device': "clavier"});

	// initialization of the timeline
	var timeline=[];

	// ================== Experiment's blocks ===========================================
	/*var full_screen = {
		type : "fullscreen",
		fullscreen_mode: true,
		message: "<p class='pret'> Bienvenue à la bibliothèque ! </p>",
		button_label: 'Commencer',
		delay_after: 1000,
	};
	timeline.push(full_screen);*/

/*	var consentement_parents = {
		type: 'external-html',
		url: 'parent_consent.html',
		cont_btn:"start",
	};
	timeline.push(consentement_parents);

	var consentement_enfant = {
		type: 'html-button-response',
		stimulus: "<h3> Un jeu pour comprendre la lecture </h3><p>Ce jeu cherche à mieux comprendre comment tu lis. Pour étudier cela, nous allons te montrer des mots à l'écran et tu devras dire si ils sont français ou pas en cliquant sur des touches du clavier. Tu es libre de refuser. </p><p>Si tu es d'accord clique sur le bouton vert sinon clique sur le bouton rouge.  </p>",
		choices: ['',''],
		button_html: ['<button class="jspsych-btn" id="smileypos">%choice%</button>', '<button class="jspsych-btn" id="smileyneg">%choice%</button>'],
		on_start: function(){
			kids_consent.play();
		},
		on_finish: function() {
			kids_consent.pause();
			kids_consent.currentTime =0;
		}
	};
	timeline.push(consentement_enfant);

	var dommage = {
		type: "html-keyboard-response",
		stimulus: "N'hésite pas à revenir une prochaine fois !",
		choices:jsPsych.NO_KEYS,
		on_start: function(){
			reponseneg.play();
		},
		on_finish: function() {
			reponseneg.pause();
			reponseneg.currentTime =0;
		}
	};

	var consentement_neg = {
		timeline: [dommage],
		conditional_function: function(){
			var data=jsPsych.data.get().last(1).values()[0];
			if (data.button_pressed == 1){
				return true
			}
			else{
				return false
			}
		}
	}
	timeline.push(consentement_neg);*/


	var survey_1 = {
		type: 'survey-multi-choice',
		preamble:"Quelques questions pour commencer",
		button_label: 'Suivant',
		questions: [{prompt: "<strong>Es-tu un garçon ou une fille ?</strong>",
					options: ["garçon", "fille", "Je ne veux pas répondre"],
					required: true,
					horizontal: true,
					name: "sexe"},

        ],
    data: {trial_category: "survey"}
	};
	timeline.push(survey_1)

	var survey_2 = {
		type: 'survey-multi-choice',
		preamble: "Quelques questions pour commencer",
		button_label: 'Suivant',
		questions: [{prompt: "<strong>Quel âge as-tu ?</strong>",
					 options: [
						 "5 ans",
						 "6 ans",
						 "7 ans",
						 "8 ans",
						 "9 ans",
						 "10 ans",
						 "11 ans",
						 "12 ans",
						 "13 ans",
						 "14 ans",
						 "15 ans",
						 "16 ans",
						 "17 ans",
						 "18 ans ou plus",],
					 required:true,
					 horizontal: true,
					 name: 'Age'},],
      data: {trial_category: "survey"}
	};
	timeline.push(survey_2)

	var survey_3 = {
		type: 'survey-multi-choice',
		preamble:"Quelques questions pour commencer",
		button_label: 'Jouer',
		questions: [{prompt: "<strong>En quelle classe es-tu ?</strong>",
					 options: [
						 "GS",
						 "CP",
						 "CE1",
						 "CE2",
						 "CM1",
						 "CM2",
						 "6ème",
						 "5ème",
						 "4ème",
						 "3ème",
						 "2nde",
						 "1ère",
						 "Tle",
						 "Autre",
					 ],
					 required:true,
					 horizontal: true,
					 name: 'Classe'},],
    data: {trial_category: "survey"}
	};
	timeline.push(survey_3)


	var full_screen = {
		type : "fullscreen",
		fullscreen_mode: true,
		message: "N'oublie pas d'allumer le son</br></br>",
		button_label: 'Commencer',
		delay_after: 1000,
    data: {trial_category: "rules"},
		on_start: function(){
			rappel.play();
		},
		on_finish: function(){
			rappel.pause();
			rappel.currentTime=0;
		},
	};
	timeline.push(full_screen);


	var instruction={
		type:"external-html",
		url: 'consigne.html',
		cont_btn:"start",
    data: {trial_category: "rules"},
		on_start: function(){
			consigne_1.play();
		},
		on_finish: function() {
			consigne_1.pause();
			consigne_1.currentTime =0;
		}
	};
	timeline.push(instruction);

	var exemple1={
		type: "html-keyboard-response-animate",
		stimulus: "lilas",
		choices:['m'],
    data: {trial_category: "rules"},
		on_start: function(){
			lila.play();
		},
		on_finish: function() {
			lila.pause();
			lila.currentTime=0;
		}

	}
	timeline.push(exemple1);

	var exemple2={
		type: "html-keyboard-response-animate",
		stimulus: "ruj",
		choices:['q'],
    data: {trial_category: "rules"},
		on_start: function(){
			ruj.play();
		},
		on_finish: function(){
			ruj.pause();
			ruj.currentTime=0;
		}

	}
	timeline.push(exemple2);

	var start={
		type:"instructions",
		pages: ["Pose ton <strong>index droit sur 'M'</strong> et ton <strong>index gauche sur 'Q'</strong> </br></br> Prêt ? </br></br>Appuie sur 'M' et c'est parti !"],
		key_forward: 77,
    data: {trial_category: "rules"},
		on_start: function(){
			consigne_2.play()
		},
		on_finish: function (){
			consigne_2.pause();
			consigne_2.currentTime=0;
		}

	};
	timeline.push(start);


	for (var k=0;k<entrainement.length;k++){
		var train={
			type: "html-keyboard-response-animate",
			stimulus : entrainement[k].stimulus,
			correct_answer: entrainement[k].correct_answer,
			choices: ['q','m'],
			data: { freq: entrainement[k].ifreq, nblettre: entrainement[k].nblettres, correct: entrainement[k].correct_answer, type: entrainement[k].wtype, trial_category: "train" }
		};
		console.log('OK');
		timeline.push(train);
	};


	for (var k=0;k<stimuli.length;k++){
		var new_stimulus={
			type: "html-keyboard-response-animate",
			stimulus : stimuli[k].stimulus,
			correct_answer: stimuli[k].correct_answer,
			choices: ['q','m'],
			data: { freq: stimuli[k].ifreq, nblettre: stimuli[k].nblettres, correct: stimuli[k].correct_answer, type: stimuli[k].wtype, trial_category: "test" }
		};
		timeline.push(new_stimulus);
		if (k==stimuli.length/2){
			var middle={
				type: "html-keyboard-response",
				stimulus : "Bravo tu as passé la moitié ! Appuie sur 'M' pour continuer",
				choices: ['m'],
        data: {trial_category: "rules"},
				on_start: function(){
					moitie.play();
				},
				on_finish: function() {
					moitie.pause();
					moitie.currentTime =0;
				}

			};
			timeline.push(middle);
		}
	};


	var ensavoirplus= {
	type:"html-keyboard-response",
	stimulus: "Mission accomplie! </br> Merci d'avoir participé ! </br>Si tu veux en savoir plus sur tes résultats, appuie sur 'O'. </br> </br>  Ton identifiant était :"+subjectID+". </br> Si tu as des questions ou commentaires, envoie les à l'adresse suivante: <strong>marie.lubineau@cri-paris.org</strong> </br> ",
	choices: ['o'],
	on_start: function(){
		fin.play();
		var Nom='clav'+subjectID+'_statique.csv';
		//jsPsych.data.get().localSave('csv',Nom);
		saveData(Nom, jsPsych.data.get().csv());
	},
	on_finish: function() {
		fin.pause();
		fin.currentTime =0;
	}
	};
	timeline.push(ensavoirplus);


	var resultats={
		type: 'html-keyboard-response',
		stimulus: function(){

			// ======================================= Data that are going to be use ===========================================================================
			var dataToUse=jsPsych.data.get().filterCustom(function(trial){return trial.trial_index>13 && trial.trial_index<137;});

			// ======================================= Variables to store the data ===============================================================================================================
			var nb_erreur_mots= dataToUse.filterCustom(function(trial){return trial.type==0 && jsPsych.pluginAPI.convertKeyCodeToKeyCharacter(trial.key_press)!=trial.correct}).count();
			var nb_correct_mots=60-nb_erreur_mots;
			var nb_erreur_pseudo = dataToUse.filterCustom(function(trial){return trial.freq==0 && jsPsych.pluginAPI.convertKeyCodeToKeyCharacter(trial.key_press)!=trial.correct}).count();
			var nb_correct_pseudo=60-nb_erreur_pseudo;
			var data_erreurtype= new Array (0, 0, 0, 0, 0, 0);
			var erreur_nonword_type= new Array([],[],[],[],[],[]);
			var erreur_nonword_total=dataToUse.filterCustom(function(trial){return trial.freq==0 && jsPsych.pluginAPI.convertKeyCodeToKeyCharacter(trial.key_press)!=trial.correct}).select('stimulus').values;
			var erreur_word_freq = new Array([],[],[],[]);

			// ======================================= Variables to display the exemple of errors on the screen ===================================================================================

			canvas_names_type = ['exemple_transpo','exemple_substi2','exemple_pseudo','exemple_homo','exemple_miroir','exemple_substi1'];
			canvas_names_freq = ['exemple_tresfreq', 'exemple_freq', 'exemple_peufreq', 'exemple_rare'];

			// =================================================== Data collection ============================================================================================================
			for (var k=1; k<5; k++){
				erreur_word_freq[4-k]=dataToUse.filterCustom(function(trial){return trial.freq==k && jsPsych.pluginAPI.convertKeyCodeToKeyCharacter(trial.key_press)!=trial.correct}).select('stimulus').values;
				erreur_word_freq[4-k].push(' ');
				erreur_word_freq[4-k].push(' ');
				erreur_word_freq[4-k].push(' ');
			}

			for (var k=1; k<7; k++){
				data_erreurtype[k-1]=dataToUse.filterCustom(function(trial){return jsPsych.pluginAPI.convertKeyCodeToKeyCharacter(trial.key_press)!=trial.correct && trial.type==k}).count();
				erreur_nonword_type[k-1]=dataToUse.filterCustom(function(trial){return trial.type==k && jsPsych.pluginAPI.convertKeyCodeToKeyCharacter(trial.key_press)!=trial.correct}).select('stimulus').values;
			}



			document.getElementById('dictionnaire').remove();
			document.getElementById('cheminee').remove();
			document.getElementById('jspsych-experiment').remove();

			document.body.style.border='0px solid black';
			document.body.style.backgroundColor='#fdeca6';
			document.body.style.display='flex';
			document.body.style.flexDirection='column';
			document.body.style.justifyContent='flex-start';



		// ==================	HTML Design of the new page ================================================================================

			var chap1=document.createElement('div');
			chap1.id='chapitre1';
			chap1.height="50%";
			document.body.appendChild(chap1);

				document.getElementById('chapitre1').innerHTML='<p class="sentences">Tu as classé correctement ' + nb_correct_mots + ' mots !</p>';

				var graph1=document.createElement('div');
				graph1.id='graphe1';
				graph1.height="100%";
				document.getElementById('chapitre1').appendChild(graph1);

					var image_dico=document.createElement('div');
					image_dico.id='image_dico_pos';
					image_dico.style.height="100%";
					image_dico.style.width="40%";
					//image_dico.style.margin="5%";
					document.getElementById('graphe1').appendChild(image_dico)
					document.getElementById('image_dico_pos').innerHTML='<img id="image_dictionnaire" src="dictionnaire_pos.jpg">';

					var conteneur1=document.createElement('div');
					conteneur1.id='conteneur1';
					conteneur1.style.width="50%";
					conteneur1.style.height="100%";
					//conteneur1.style.border="2px solid black";
					document.getElementById('graphe1').appendChild(conteneur1);

						var canvas_resultats=document.createElement("CANVAS");
						canvas_resultats.id='erreurtype';
						canvas_resultats.width=1000;
						canvas_resultats.height=500;
						canvas_resultats.style.width="100%";
						var ctx5=canvas_resultats.getContext('2d');
						document.getElementById('conteneur1').appendChild(canvas_resultats);

			var chap2=document.createElement('div');
			chap2.id='chapitre2';
			chap2.height="50%";
			document.body.appendChild(chap2);

				if (nb_erreur_mots==0){
					document.getElementById('chapitre2').innerHTML="<p class='sentences'>Bravo, tu n'as fait aucune erreur sur les mots !</p>";
				}else{
				document.getElementById('chapitre2').innerHTML='<p class="sentences">Voici des mots où tu as fait une erreur:</p>';
				document.getElementById('chapitre2').innerHTML= document.getElementById('chapitre2').innerHTML+'<table><tr><td class="table_titre">Très fréquents</td><td class="table_word">'+erreur_word_freq[0][0]+'</td><td class="table_word">'+erreur_word_freq[0][1]+'</td><td class="table_word">'+erreur_word_freq[0][2]+'</td></tr><tr><td class="table_titre">Fréquents</td><td class="table_word">'+erreur_word_freq[1][0]+'</td><td class="table_word">'+erreur_word_freq[1][1]+'</td><td class="table_word">'+erreur_word_freq[1][2]+'</td></tr><tr ><td class="table_titre">Rares</td><td class="table_word">'+erreur_word_freq[2][0]+'</td><td class="table_word" >'+erreur_word_freq[2][1]+'</td><td class="table_word">'+erreur_word_freq[2][2]+'</td></tr><tr ><td class="table_titre_last">Très rares</td><td class="table_word_last">'+erreur_word_freq[3][0]+'</td><td class="table_word_last">'+erreur_word_freq[3][1]+'</td><td class="table_word_last">'+erreur_word_freq[3][2]+'</td></tr></table>'
				};

			var chap3=document.createElement('div');
			chap3.id='chapitre3';
			chap3.height="50%";
			document.body.appendChild(chap3);

				document.getElementById('chapitre3').innerHTML='<p class="sentences">Tu as découvert ' + nb_correct_pseudo + ' pièges !</p>';

				var graph2=document.createElement('div');
				graph2.id='graphe2';
				graph2.height="100%";
				document.getElementById('chapitre3').appendChild(graph2);

					var image_chem=document.createElement('div');
					image_chem.id='image_chem_pos';
					image_chem.style.height="100%";
					image_chem.style.width="40%";
					//image_chem.style.margin="5%";
					document.getElementById('graphe2').appendChild(image_chem)
					document.getElementById('image_chem_pos').innerHTML='<img id="image_cheminee" src="cheminee_pos.jpg" >';

					var conteneur2=document.createElement('div');
					conteneur2.id='conteneur2';
					conteneur2.style.width="50%";
					conteneur2.style.height="100%";
					//conteneur1.style.border="2px solid black";
					document.getElementById('graphe2').appendChild(conteneur2);

						var canvas_resultats_pseudo=document.createElement("CANVAS");
						canvas_resultats_pseudo.id='erreurpseudo';
						canvas_resultats_pseudo.width=1000;
						canvas_resultats_pseudo.height=500;
						canvas_resultats_pseudo.style.width="100%";
						var ctx4=canvas_resultats_pseudo.getContext('2d');
						document.getElementById('conteneur2').appendChild(canvas_resultats_pseudo);

			var chap4=document.createElement('div');
			chap4.id='chapitre4';
			chap4.height="50%";
			document.body.appendChild(chap4);

				if (nb_erreur_pseudo==0){
					document.getElementById('chapitre4').innerHTML="<p class='sentences'>Bravo, tu as découvert tous les pièges !</p>";
				}else if (nb_erreur_pseudo<11){
					document.getElementById('chapitre4').innerHTML="<p class='sentences'>Voici les quelques pièges où tu t'es trompé: vois-tu pourquoi ce ne sont pas des mots?</p>";
					document.getElementById('chapitre4').innerHTML=document.getElementById('chapitre4').innerHTML+display_errors(nb_erreur_pseudo, erreur_nonword_total);
				}else {
				document.getElementById('chapitre4').innerHTML="<p class='sentences'>Voici certains pièges où tu t'es trompé: vois-tu pourquoi ce ne sont pas des mots?</p>";
					maximum=Math.max(...data_erreurtype);
					if(data_erreurtype[1]==maximum){
						document.getElementById('chapitre4').innerHTML=document.getElementById('chapitre4').innerHTML+display_errors(maximum, erreur_nonword_type[1]);
						document.getElementById('chapitre4').innerHTML=document.getElementById('chapitre4').innerHTML+'<p class="conseil">Fais bien attention à chaque lettre !</p>';
					}
					else if(data_erreurtype[4]==maximum){
						document.getElementById('chapitre4').innerHTML=document.getElementById('chapitre4').innerHTML+display_errors(maximum, erreur_nonword_type[4]);
						document.getElementById('chapitre4').innerHTML=document.getElementById('chapitre4').innerHTML+'<p class="conseil">Fais bien attention aux lettres <strong>b</strong>, <strong>d</strong>, <strong>p</strong> et <strong>q</strong> !</p>';
					}
					else if(data_erreurtype[2]==maximum){
						document.getElementById('chapitre4').innerHTML=document.getElementById('chapitre4').innerHTML+display_errors(maximum, erreur_nonword_type[2]);
						document.getElementById('chapitre4').innerHTML=document.getElementById('chapitre4').innerHTML+"<p class='conseil'>Ces mots n'existent pas !</p>";
					}
					else if(data_erreurtype[0]==maximum){
						document.getElementById('chapitre4').innerHTML=document.getElementById('chapitre4').innerHTML+display_errors(maximum, erreur_nonword_type[0]);
						document.getElementById('chapitre4').innerHTML=document.getElementById('chapitre4').innerHTML+"<p class='conseil'>Fais bien attention à l'ordre des lettres !</p>";
					}
					else if(data_erreurtype[5]==maximum){
						document.getElementById('chapitre4').innerHTML=document.getElementById('chapitre4').innerHTML+display_errors(maximum, erreur_nonword_type[5]);
						document.getElementById('chapitre4').innerHTML=document.getElementById('chapitre4').innerHTML+"<p class='conseil'>Fais bien attention à chaque lettre !</p>";
					}
					else{
						document.getElementById('chapitre4').innerHTML=document.getElementById('chapitre4').innerHTML+display_errors(maximum, erreur_nonword_type[3]);
						document.getElementById('chapitre4').innerHTML=document.getElementById('chapitre4').innerHTML+"<p class='conseil'>Fais bien attention à la prononciation des lettres <strong>s</strong>, <strong>c</strong> et <strong>g</strong>!</p>";
					}
				};
	// ==================== Design of the different graphs =======================================================================

	// ============== Word correctly read ==============================================================================
			var chart_word = new Chart(ctx5, {
				type: 'doughnut',

				data: {
					 labels: [
						'Correct',
						'Erreurs',
					],
					datasets: [{
						data: [nb_correct_mots, nb_erreur_mots],
						backgroundColor:[
							'rgb(87, 205, 119)',
							'rgb(253, 236, 156)',
						],
						borderColor: 'rgb(0, 0, 0)',
						borderWidth: 3,



					}]
				},
				options :{
					radius: '50%',
					legend: {
						display: false,
					},

				},
			});


	// ============== Pseudo word correctly read ==============================================================================
			var chart_peusdo = new Chart(ctx4, {
				type: 'doughnut',

				data: {
					 labels: [
						'Correct',
						'Erreurs',
					],
					datasets: [{
						data: [nb_correct_pseudo, nb_erreur_pseudo],
						backgroundColor:[
							'rgb(87, 205, 119)',
							'rgb(253, 236, 156)',
						],
						borderColor: 'rgb(0, 0, 0)',
						borderWidth: 3,



				}]
				},
				options :{
					radius: '80%',
					legend: {
						display: false,
					},

				},
			});

			var feedback="<p> Résultats </p>";
			return feedback
		},
		choices: jsPsych.NO_KEYS,
	};

	var cond={
		timeline:[resultats],
		conditional_function: function(){
			var data=jsPsych.data.get().last(1).values()[0];
			if(data.key_press == jsPsych.pluginAPI.convertKeyCharacterToKeyCode('n')){
				return false;
			} else {
				return true;
			}
		}
	}
	timeline.push(cond);

	//sendData('clav'+subjectID+'_statique.csv',"Marie","LexicalDecisionTask","ping");

	jsPsych.init({
		timeline: timeline,
		display_element: 'jspsych-experiment',
		on_trial_finish: function() {

			// Collecting trial index

			var k=jsPsych.data.getLastTrialData().select('trial_index').values;
			if (k>=8 && k<entrainement.length+stimuli.length+8 && k!=9+entrainement.length+stimuli.length/2){ // k>=6 is here to make sure that the last trial was a trial in which feedback is needed
				var last_trial_data=jsPsych.data.getLastTrialData();
				var correct=last_trial_data.select('correct').values[0]; // collecting the correct answer of the last trial
				var answer=last_trial_data.select('key_press').values[0]; // collecting the answer given by the subject in the last trial

				// conversion to have letters for both the corect answer and the answer given by the subject
				if (answer==77){
					answer='m';
				}
				else {
					answer='q';
				}

				// calculation of the number of success one after another
				if (answer==correct){
					alasuite+=1;
				}
				else {
					alasuite=0;
				}

				// audio feedback
				if (0<alasuite && alasuite<4){
					pos_feed[alasuite-1].play();
				} else if(alasuite>=4){
					pos4.play();
				}else {
					neg1.play();
				}

				// visual feedback

				if (answer==correct){
					if (answer=='m'){

						// // =========== Animation block of the dictionnary =====================
						let animation_dp=null;
						let f=0;
						const NF = 20; // define the duration of the animation --> one third of a second
						function animer_dictionnaire_pos(){
							const frame = now => {
							f ++;
							// define progress
							const progress = f/NF;

							if (f<NF){
							document.getElementById('dictionnaire').innerHTML='<img id="image_dictionnaire" src="dictionnaire_pos.jpg">';
							}

							if (f >= NF) {
								cancelAnimationFrame(animation_dp);
								document.getElementById('dictionnaire').innerHTML='<img id="image_dictionnaire" src="dictionnaire.jpg">';
								f = 0;
								animation_dp = null;
								return;
							}
							animation_dp = requestAnimationFrame(frame);
						};
						animation_dp = requestAnimationFrame(frame);
							}
						animer_dictionnaire_pos();

					}
					else{

						// =========== Animation block of the fireplace =====================
						let animation_cp=null;
						let f=0;
						const NF = 20; // define the duration of the animation --> one third of a second
						function animer_cheminee_pos(){
						const frame = now => {
						f ++;
						// define progress
						const progress = f/NF;

						if (f<NF){
							document.getElementById('cheminee').innerHTML='<img id="image_cheminee" src="cheminee_pos.jpg">';
						}

						if (f >= NF) {
							cancelAnimationFrame(animation_cp);
							document.getElementById('cheminee').innerHTML='<img id="image_cheminee" src="cheminee.jpg">';
							f = 0;
							animation_cp = null;
							return;
						}
						animation_cp = requestAnimationFrame(frame);
					};
					animation_cp = requestAnimationFrame(frame);
						}
						animer_cheminee_pos();
					}
				}
				else{
					if (answer=='m'){

						// =========== Animation block of the dictionnary =====================
						let animation_dn=null;
						let f=0;
						const NF = 20; // define the duration of the animation --> one third of a second
						function animer_dictionnaire_neg(){
							const frame = now => {
							f ++;
							// define progress
							const progress = f/NF;

							//mettre ici le début de l'animation

							if (f<NF){
							document.getElementById('dictionnaire').innerHTML='<img id="image_dictionnaire"  src="dictionnaire_neg.jpg">';
							//document.getElementById('cheminee').innerHTML='<img id="image_cheminee" src="cheminee_pos.jpg">';
							}

							if (f >= NF) {
								cancelAnimationFrame(animation_dn);
								document.getElementById('dictionnaire').innerHTML='<img id="image_dictionnaire" src="dictionnaire.jpg" >';
								//document.getElementById('cheminee').innerHTML='<img id="image_cheminee" src="cheminee.jpg">';
								f = 0;
								animation_dn = null;
								return;
							}
							animation_dn = requestAnimationFrame(frame);
						};
						animation_dn = requestAnimationFrame(frame);
							}
						animer_dictionnaire_neg();
					}
					else{

						// =========== Animation block of the fireplace =====================
						let animation_cn=null;
						let f=0;
						const NF = 20; // define the duration of the animation --> one third of a second
						function animer_cheminee_neg(){
							const frame = now => {
							f ++;
							// define progress
							const progress = f/NF;

							if (f<NF){
								document.getElementById('cheminee').innerHTML='<img id="image_cheminee" src="cheminee_neg.jpg" >';
								//document.getElementById('dictionnaire').innerHTML='<img id="image_dictionnaire" src="dictionnaire_pos.jpg">';
							}

							if (f >= NF) {
								cancelAnimationFrame(animation_cn);
								document.getElementById('cheminee').innerHTML='<img id="image_cheminee" src="cheminee.jpg" >';
								//document.getElementById('dictionnaire').innerHTML='<img id="image_dictionnaire" src="dictionnaire.jpg">';
								f = 0;
								animation_cn = null;
								return;
							}
							animation_cn = requestAnimationFrame(frame);
						};
						animation_cn = requestAnimationFrame(frame);
							}
						animer_cheminee_neg();
					}
				}

			}

		},
		//on_finish: function(){
			//var Nom=subjectID+'.csv';
			//jsPsych.data.get().localSave('csv',Nom);
			// //sendData(Nom, "Marie", "LexicalDecisionTask", jsPsych.data.get().csv());

		//},
	});


  })
	</script>
</html>
