gadgets['countdown'] = (function(){
   var time_left = 10; //number of seconds for countdown
   var output_element_id = 'countdown';
   var keep_counting = 1;
   var no_time_left_message = "Time's Up!!!";

   function countdown() {
      if(time_left < 2) {
         keep_counting = 0;
      }
 
      time_left = time_left - 1;
   }
 
   function add_leading_zero(n) {
      if(n.toString().length < 2) {
         return '0' + n;
      } else {
         return n;
      }
   }
 
   function format_output() {
      var hours, minutes, seconds;
      seconds = Math.floor(time_left % 60);
      minutes = Math.floor(time_left / 60) % 60;
      hours = Math.floor(time_left / 3600);
 
      seconds = add_leading_zero( seconds );
      minutes = add_leading_zero( minutes );
      hours = add_leading_zero( hours );
 
      return hours + ':' + minutes + ':' + seconds;
   }
 
   function show_time_left() {
      document.getElementById(output_element_id).innerHTML = format_output();//time_left;
   }
 
   function no_time_left() {
      document.getElementById(output_element_id).innerHTML = no_time_left_message;
   }
 
   return {
      count: function () {
         countdown();
         show_time_left();
      },

      timer: function () {
         this.count();
 
         if(keep_counting) {
            setTimeout("gadgets.countdown.timer();", 1000);
         } else {
            no_time_left();
         }
      },

      init: function (t, element_id) {
         time_left = t;
         output_element_id = element_id;
         this.timer();
      }
   };
})();
