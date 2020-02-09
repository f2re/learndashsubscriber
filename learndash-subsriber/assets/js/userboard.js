jQuery(document).ready(function($){

  init();



  /**
   * initialisation function
   * @return {[type]} [description]
   */
  function init(){

    // 
    // we must get user data after user finish course!
    // 

    // get user data
    get_table_data();
  }

  /**
   * request table data from API
   * @return {[type]} [description]
   */
  function get_table_data(){

    $.ajax({
      url:'/wp-json/ldsubscriber/v1/get_course_data',
      method: "POST",
      data:{}
    })
    .done(function(resp){
      console.log(resp);
      // 
      // calc percentage of user summary course
      // 
      let percentage = 0,
          courses    = 0;

      // 
      // iterate over courses
      // 
      $.each(resp.data.data,function(i,ind){
        console.log(i,ind);

        percentage+=ind.progress;
        courses+=1;
      });

      $('#leadersection-subscriber form').text( percentage/courses + " percentage courses complete " );

    });

  }


});