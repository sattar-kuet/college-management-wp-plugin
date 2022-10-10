(function( $ ) {
	'use strict';
   $(document).ready(function(){

   $('select').on('change', function (e) {

        var optionSelected = $("option:selected", this);
        var valueSelected = this.value;
        var subjectId = optionSelected.data('subjectid');

       
        
      $('.subject_option').each(function(){
         alert(subjectId);
         alert($(this).data('parentid'));
         if($(this).data('parentid') == subjectId){
            if(valueSelected == -1){
               $('.inactive_subject:not(.mendatory_subject'+subjectId+', .optional_subject'+subjectId+' )').attr('selected','selected');
            }
            else if(valueSelected == 0){
               $('.optional_subject:not(.mendatory_subject'+subjectId+', .inactive_subject'+subjectId+' )').attr('selected','selected');
            }
            else if(valueSelected == 1){
               $('.mendatory_subject:not(.optional_subject'+subjectId+', .inactive_subject'+subjectId+' )').attr('selected','selected');
            }
         }   
      });
   });

   $('.session_start').on('change', function(e){
      var session_start = $(this).val();
      var session_end = parseInt(session_start) + 1;
      $('.session_end').val(session_end);

   });

   $('.session_end').on('change', function(e){
      var session_end = $(this).val();
      var session_start = parseInt(session_end) - 1;
      $('.session_start').val(session_start);

   });
   
   subject_two_part_manage($);

});




})( jQuery );


function subject_two_part_manage($){
  $(document).ready(function(){
    generate_two_part_subject_name($);
    $('#first_part').hide();
    $('#second_part').hide();
   
    $('#has_two_part').change(function() {
        if(this.checked) {
           $('#first_part').show();
           $('#second_part').show();
        }else{
              $('#first_part').hide();
              $('#second_part').hide();
        }      
    });
  });
}

function generate_two_part_subject_name($){
   $('.subject_name').on('change', function(){
      var subject_name = $('.subject_name').val();
      $('.first_part_name').val(subject_name+' First Paper');
      $('.second_part_name').val(subject_name+' Second Paper');
    });
}