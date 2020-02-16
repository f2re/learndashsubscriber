jQuery(document).ready(function($){

  init();

  var observer = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
      if (mutation.attributeName === "class") {
        var attributeValue = $(mutation.target).prop(mutation.attributeName);

        if ( $(mutation.target).hasClass('menu-item-selected') ){
          // 
          // call function to show score
          // 
          // get_table_data();

          // 
          // get data from body attr
          // 
          let _body       = $(mutation.target).parents('body');
          let correct     = 0,
              incorrect   = 0,
              points      = 0;

          if ( _body.attr('correct')!==undefined ){
            correct = parseInt(_body.attr('correct'));
          }
          if ( _body.attr('incorrect')!==undefined ){
            incorrect = parseInt(_body.attr('incorrect'));
          }
          if ( _body.attr('points')!==undefined ){
            points = parseInt(_body.attr('points'));
          }

          dirty_data(correct,incorrect,points);
          // console.log("Class attribute changed to:", attributeValue);
        }

      }
    });
  });
  

  /**
   * initialisation function
   * @return {[type]} [description]
   */
  function init(){
    // wait for iframes loaded
    iframes_loaded();

    // 
    // hide popup event
    // 
    let popup = $('#LDS-popup');
    popup.find('a.close')
         .off('click')
         .on('click',function(ev){
            popup.removeClass('active');
         });

    // 
    // we must get user data after user finish course!
    // 

    // get user data
    // get_table_data();
    get_course_list();
  }

  /**
   * when iframe loaded
   * @return {[type]} [description]
   */
  function iframes_loaded(){
    let iframes = $('iframe');

    $('iframe').hover( function() {
      let item = $(this).contents().find('#panel-outline li a.cs-listitem').last();
      // console.log( item );
      if ( item.length>0 ){
        observer.observe( item[0], {
          attributes: true
        });
        $(this).off('hover');
      }
    });
    return;

  } 

  /**
   * get dirty data from body attrs in iframe
   * @return {[type]} [description]
   */
  function dirty_data(correct, incorrect, points){

    let popup = $('#LDS-popup');
    popup.addClass('active');

    popup.find('.content .correct').text(correct);
    popup.find('.content .all').text(correct+incorrect);
    popup.find('.content .points').text(points);

    return;
  }

  /**
   * request table data from API
   * @return {[type]} [description]
   */
  function get_course_list(){
    // require._defined.
    // window._gsDefine
    // require( ["models/presentation/interactions/Interaction"], function(p){
    //   console.log(p);
    // });


    $.ajax({
      url:'/wp-json/ldsubscriber/v1/get_course_list',
      method: "POST",
      data:{postid: $('#leadersection-subscriber').attr('postid') }
    })
    .done(function(resp){
      // console.log(resp);
      let _div = $(resp.all_course_content);

      // let url = _div.find('a').attr('href');
      // console.log(url);
      // let iframe = $('<iframe src="'+url+'" style="width:100%; height:100%;"></iframe>');
      let popup = $('#LDS-popup');
      // popup.addClass('active');

      // if next-link empty - show all courses
      if ( resp.next_post=='' ){
        popup.find('.content-after ').html(_div);
      }else{
        let lnk = $('<a href="'+resp.next_post+'">Next module </a>')
        popup.find('.content-after ').html(lnk);
      }

    });

  }

  /**
   * request table data from API
   * @return {[type]} [description]
   */
  function get_table_data(){
    // require._defined.
    // window._gsDefine
    // require( ["models/presentation/interactions/Interaction"], function(p){
    //   console.log(p);
    // });


    $.ajax({
      url:'/wp-json/ldsubscriber/v1/get_course_data',
      method: "POST",
      data:{}
    })
    .done(function(resp){
      // console.log(resp);
      // 
      // calc percentage of user summary course
      // 
      let percentage = 0,
          courses    = 0;

      // 
      // iterate over courses
      // 
      $.each(resp.data.data,function(i,ind){
        // console.log(i,ind);

        percentage+=ind.progress;
        courses+=1;
      });

      $('#leadersection-subscriber form').text( percentage/courses + " percentage courses complete " );

    });

  }


});