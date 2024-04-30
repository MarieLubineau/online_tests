/**
 * jspsych-html-button-response
 * Josh de Leeuw
 *
 * plugin for displaying a stimulus and getting a keyboard response
 *
 * documentation: docs.jspsych.org
 *
 **/

jsPsych.plugins["html-button-response-animate"] = (function() {

  var plugin = {};

  plugin.info = {
    name: 'html-button-response',
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
        type: jsPsych.plugins.parameterType.STRING,
        pretty_name: 'Choices',
        default: undefined,
        array: true,
        description: 'The labels for the buttons.'
      },
      button_html: {
        type: jsPsych.plugins.parameterType.STRING,
        pretty_name: 'Button HTML',
        default: '<button class="jspsych-btn">%choice%</button>',
        array: true,
        description: 'The html of the button. Can create own style.'
      },
      prompt: {
        type: jsPsych.plugins.parameterType.STRING,
        pretty_name: 'Prompt',
        default: null,
        description: 'Any content here will be displayed under the button.'
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
        description: 'How long to show the trial.'
      },
      margin_vertical: {
        type: jsPsych.plugins.parameterType.STRING,
        pretty_name: 'Margin vertical',
        default: '0px',
        description: 'The vertical margin of the button.'
      },
      margin_horizontal: {
        type: jsPsych.plugins.parameterType.STRING,
        pretty_name: 'Margin horizontal',
        default: '8px',
        description: 'The horizontal margin of the button.'
      },
      response_ends_trial: {
        type: jsPsych.plugins.parameterType.BOOL,
        pretty_name: 'Response ends trial',
        default: true,
        description: 'If true, then trial will end when user responds.'
      },

    }
  }

  plugin.trial = function(display_element, trial) {

    var buttons = [];
    if (Array.isArray(trial.button_html)) {
      if (trial.button_html.length == trial.choices.length) {
        buttons = trial.button_html;
      } else {
        console.error('Error in html-button-response plugin. The length of the button_html array does not equal the length of the choices array');
      }
    } else {
      for (var i = 0; i < trial.choices.length; i++) {
        buttons.push(trial.button_html);
      }
    }
    var html='<div id="block-exp">';
    //display buttons
    html += '<div id="jspsych-html-button-response-btn1">';
    var str = buttons[0].replace(/%choice%/g, trial.choices[0]);
    html += '<div class="jspsych-html-button-response-button" id="jspsych-html-button-response-button-' + 0 +'" data-choice="'+0+'">'+str+'</div>';
    html += '</div>';

    // display stimulus
    html += '<div id="jspsych-html-button-response-stimulus"></div>';
    //display buttons
    html += '<div id="jspsych-html-button-response-btn2">';
    var str = buttons[1].replace(/%choice%/g, trial.choices[1]);
    html += '<div class="jspsych-html-button-response-button" id="jspsych-html-button-response-button-' + 1 +'" data-choice="'+1+'">'+str+'</div>';
    html += '</div>';
    html+='</div>';


    //show prompt if there is one
    if (trial.prompt !== null) {
      html += trial.prompt;
    }
    display_element.innerHTML = html;

    var canvas_stimulus=document.createElement("CANVAS");
    canvas_stimulus.id='canvas_exp';
    canvas_stimulus.width=1000;
    canvas_stimulus.height=600;
    canvas_stimulus.style.width="100%";
    canvas_stimulus.style.height="25%";
    //canvas_stimulus.style.border="10px solid black";
    var ctx=canvas_stimulus.getContext('2d');
    document.getElementById("jspsych-html-button-response-stimulus").appendChild(canvas_stimulus);

    // start time
    var start_time = performance.now();


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
          ctx.fillRect(dx-4,dy-4,x+8,y+8);
          ctx.fillStyle="#FDECA6";
          ctx.fillRect(dx,dy,x,y);
          x=x+30;
          y=y+10;
          dx=dx-15;
          dy=dy-5;
        }


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

    // add event listeners to buttons
    for (var i = 0; i < trial.choices.length; i++) {
      display_element.querySelector('#jspsych-html-button-response-button-' + i).addEventListener('click', function(e){
        var choice = e.currentTarget.getAttribute('data-choice'); // don't use dataset for jsdom compatibility
        after_response(choice);
      });
    }

    // store response
    var response = {
      rt: null,
      button: null,
      end_time: null,
    };



    // function to handle responses by the subject
    function after_response(choice) {

      // measure rt
      var end_time = performance.now();
      var rt = end_time - start_time;
      response.button = choice;
      response.rt = rt;
      response.end_time=end_time;

      // after a valid response, the stimulus will have the CSS class 'responded'
      // which can be used to provide visual feedback that a response was recorded
      display_element.querySelector('#jspsych-html-button-response-stimulus').className += ' responded';

      // disable all the buttons after a response
      var btns = document.querySelectorAll('.jspsych-html-button-response-button button');
      for(var i=0; i<btns.length; i++){
        //btns[i].removeEventListener('click');
        btns[i].setAttribute('disabled', 'disabled');
      }


      if (trial.response_ends_trial) {
        end_trial();
      }
    };

    // function to end trial when it is time
    function end_trial() {

      // kill any remaining setTimeout handlers
      jsPsych.pluginAPI.clearAllTimeouts();

      // gather the data to store for the trial
      var trial_data = {
        "rt": response.rt,
        "end_time": response.end_time,
        "stimulus": trial.stimulus,
        "button_pressed": response.button,
		    "button_pressed_text":trial.choices[response.button],
        "stimulus_appear":time_stimulus_appear,
      };

      //animation of the word in either one direction or the other according to subject's answer
      let anim_text = null;
      let f = 0;
      const NF_text = 20;
      let xt=500; // initial position of the text
      let yt=300;
      timetowait=0;

      answer=100;
      //conversion of the correct answser
      if (trial.correct_answer=='M'){
        answer=1;
      } else {
        answer=0;
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
            // visual feedback
            cancelAnimationFrame(anim_text);
            // clear the display
            document.getElementById('jspsych-html-button-response-stimulus').innerHTML = '';

            f = 0;
            timetowait=1;
            anim_text = null;
            return;
          }
          anim_text = requestAnimationFrame(frame);
        };
        anim_text = requestAnimationFrame(frame);
      }

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
            document.getElementById('jspsych-html-button-response-stimulus').innerHTML = '';
            f = 0;
            timetowait=1;
            anim_text = null;
            return;
          }
          anim_text = requestAnimationFrame(frame);
        };
        anim_text = requestAnimationFrame(frame);
      }

      if (response.button==1){
        text_animation_right();
      } else if (response.button==0){
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

      wait();

      // hide image if timing is set
      if (trial.stimulus_duration !== null) {
        jsPsych.pluginAPI.setTimeout(function() {
          display_element.querySelector('#jspsych-html-button-response-stimulus').style.visibility = 'hidden';
        }, trial.stimulus_duration);
      }

      // end trial if time limit is set
      if (trial.trial_duration !== null) {
        jsPsych.pluginAPI.setTimeout(function() {
          end_trial();
        }, trial.trial_duration);
      }
    }
  };
  return plugin;
})();
