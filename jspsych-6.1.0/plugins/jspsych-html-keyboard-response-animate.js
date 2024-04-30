/**
 * jspsych-html-keyboard-response
 * Josh de Leeuw
 *
 * plugin for displaying a stimulus and getting a keyboard response
 *
 * documentation: docs.jspsych.org
 *
 **/



jsPsych.plugins["html-keyboard-response-animate"] = (function() {

  var plugin = {};

  plugin.info = {
    name: 'html-keyboard-response',
    description: '',
    parameters: {
      stimulus: {
        type: jsPsych.plugins.parameterType.HTML_STRING,
        pretty_name: 'Stimulus',
        default: undefined,
        description: 'The HTML string to be displayed'
      },
	  correct_answer: {
		type: jsPsych.plugins.parameterType.HTML_STRING,
		pretty_name: 'Correct',
		default: undefined,
        description: 'The HTML string that tells us the correct answer that should have been told by the subject'
	  },
      choices: {
        type: jsPsych.plugins.parameterType.KEYCODE,
        array: true,
        pretty_name: 'Choices',
        default: jsPsych.ALL_KEYS,
        description: 'The keys the subject is allowed to press to respond to the stimulus.'
      },
      prompt: {
        type: jsPsych.plugins.parameterType.STRING,
        pretty_name: 'Prompt',
        default: null,
        description: 'Any content here will be displayed below the stimulus.'
      },
      stimulus_duration: {
        type: jsPsych.plugins.parameterType.INT,
        pretty_name: 'Stimulus duration',
        default: null,
        description: 'How long to hide the stimulus.'
      },
      trial_duration: {
        type: jsPsych.plugins.parameterType.INT,
        pretty_name: 'Trial duration',
        default: null,
        description: 'How long to show trial before it ends.'
      },
      response_ends_trial: {
        type: jsPsych.plugins.parameterType.BOOL,
        pretty_name: 'Response ends trial',
        default: true,
        description: 'If true, trial will end when subject makes a response.'
      },

    }
  }

  plugin.trial = function(display_element, trial) {

    // var new_html = '<div id="jspsych-html-keyboard-response-stimulus">'+trial.stimulus+'</div>';


     // //add prompt
    // if(trial.prompt !== null){
      // new_html += trial.prompt;
    // }

    // //draw
    // display_element.innerHTML = new_html;

	var canvas_stimulus=document.createElement("CANVAS");
	canvas_stimulus.id='canvas_exp';
	canvas_stimulus.width=1000;
	canvas_stimulus.height=600;
	canvas_stimulus.style.width="100%";
	canvas_stimulus.style.height="25%";
	//canvas_stimulus.style.border="10px solid black";
	var ctx=canvas_stimulus.getContext('2d');
	display_element.appendChild(canvas_stimulus);

	//drawing of the chemney and the dictionnary

	//animation of the box in which the text is written
	let anim_rect = null;
	let f = 0;
	const NF = 30;
	let x=10; // initial dimensions of the rectangle
	let y=6;
	let dx=500;
	let dy=300;
	let origin=0; //time at which the trial begins
	let delta=0;
  let time_stimulus_appear=0;

	function rect_animation(){
	const frame = now => {
	f ++;
	if (origin==0){

	origin=performance.now();
	}

	// define progress
	const progress = f/NF;

	delta=now-origin;

	//mettre ici le début de l'animation

	if (f>14){
	ctx.fillStyle="#000000";
	ctx.fillRect(dx-2,dy-2,x+4,y+4);
	ctx.fillStyle="#FDECA6";
	ctx.fillRect(dx,dy,x,y);
	x=x+30;
	y=y+10;
	dx=dx-15;
	dy=dy-5;}


	if (delta>=500) {
		ctx.clearRect(0,0,1000,600);
		ctx.fillStyle="#000000";
		ctx.fillRect(243,213,514,180);
		ctx.fillStyle="#FDECA6";
		ctx.fillRect(245,215,510,176);

		cancelAnimationFrame(anim_rect);
    if (time_stimulus_appear==0){
      time_stimulus_appear=performance.now();
    };
    ctx.fillStyle='#000000';
		ctx.font='90px Muli';
		ctx.textAlign='center';
		ctx.textBaseline='middle';
		ctx.fillText(trial.stimulus, 500, 300);

        f = 0;
        anim_rect = null;
		return;
	}
	anim_rect = requestAnimationFrame(frame);
};
anim_rect = requestAnimationFrame(frame);
	}
	rect_animation();





    // store response
    var response = {
      rt: null,
      key: null,
      end_time: null,
    };

    // function to end trial when it is time
    var end_trial = function() {

      // kill any remaining setTimeout handlers
      jsPsych.pluginAPI.clearAllTimeouts();

      // kill keyboard listeners
      if (typeof keyboardListener !== 'undefined') {
        jsPsych.pluginAPI.cancelKeyboardResponse(keyboardListener);
      }

      // gather the data to store for the trial
      var trial_data = {
        "rt": response.rt,
        "end_time":response.end_time,
        "stimulus": trial.stimulus,
        "key_press": response.key,
        "stimulus_appear":time_stimulus_appear,
      };
	  console.log(response.key);

	  	//animation of the word in either one direction or the other according to subject's answer
	let anim_text = null;
	let f = 0;
	const NF = 20;
	let xt=500; // initial position of the text
	let yt=300;
	timetowait=0;

	answer=0;
	//conversion of the correct answser
	if (trial.correct_answer=='M'){
		answer=77;
	} else {
		answer=81;
	}


function text_animation_right(){
	const frame = now => {
	f ++;
	// define progress
	const progress = f/NF;

	//mettre ici le début de l'animation
	ctx.clearRect(0,0,1000,600);
	ctx.fillStyle="#000000";
	ctx.font='100px Muli';
	ctx.textAlign='center';
	ctx.textBaseline='middle';
	ctx.fillText(trial.stimulus,xt,yt);
	xt=xt+14;

	if (f >= NF) {
		cancelAnimationFrame(anim_text);
		// clear the display
		display_element.innerHTML = '';
        f = 0;
		timetowait=1;
        anim_text = null;
		return;
	}
	anim_text = requestAnimationFrame(frame);
};
anim_text = requestAnimationFrame(frame);}

function text_animation_left(){
	const frame = now => {
	f ++;
	// define progress
	const progress = f/NF;

	//mettre ici le début de l'animation
	ctx.clearRect(0,0,1000,600);
	ctx.fillStyle="#000000";
	ctx.font='100px Muli';
	ctx.textAlign='center';
	ctx.textBaseline='middle';
	ctx.fillText(trial.stimulus,xt,yt);
	xt=xt-14;

	if (f >= NF) {
		cancelAnimationFrame(anim_text);
		// clear the display
		display_element.innerHTML = '';
        f = 0;
		timetowait=1;
        anim_text = null;
		return;
	}
	anim_text = requestAnimationFrame(frame);
};
anim_text = requestAnimationFrame(frame);}

	if (response.key==77){
		text_animation_right();
	} else if (response.key==81){
		text_animation_left();
	}


function wait() {
	if (timetowait == 0) {
		setTimeout(wait, 700);
	} else {
	// move on to the next trial
	jsPsych.finishTrial(trial_data);
	}
};

wait() ;






    };

    // function to handle responses by the subject
    var after_response = function(info) {




	// display_element.replaceChild(canvas_response, canvas_stimulus);

      // after a valid response, the stimulus will have the CSS class 'responded'
      // which can be used to provide visual feedback that a response was recorded
      //display_element.querySelector('#jspsych-html-keyboard-response-stimulus').className += ' responded';

      // only record the first response
      if (response.key == null) {
        response = info;
      }

      if (trial.response_ends_trial) {
        end_trial();
      }
    };

    // start the response listener
    if (trial.choices != jsPsych.NO_KEYS) {
      var keyboardListener = jsPsych.pluginAPI.getKeyboardResponse({
        callback_function: after_response,
        valid_responses: trial.choices,
        rt_method: 'performance',
        persist: false,
        allow_held_key: false
      });
    }

    // hide stimulus if stimulus_duration is set
    if (trial.stimulus_duration !== null) {
      jsPsych.pluginAPI.setTimeout(function() {
        display_element.querySelector('#jspsych-html-keyboard-response-stimulus').style.visibility = 'hidden';
      }, trial.stimulus_duration);
    }

    // end trial if trial_duration is set
    if (trial.trial_duration !== null) {
      jsPsych.pluginAPI.setTimeout(function() {
        end_trial();
      }, trial.trial_duration);
    }

  };

  return plugin;
})();
