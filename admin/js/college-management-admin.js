(function( $ ) {
	'use strict';
   $(document).ready(function(){
      change_child_subject_as_per_parent($);
      set_alternative_optional_or_mendatory_subject($);
      session_adjust($);
      subject_two_part_manage($);
});




})( jQuery );

function session_adjust($){
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
}
function change_child_subject_as_per_parent($){
   $('select').on('change', function (e) {

      var optionSelected = $("option:selected", this);
      var valueSelected = this.value;
      var subjectId = optionSelected.data('subjectid');

   //  alert(valueSelected);
      
    $('.subject_option').each(function(){
       console.log('selected subject id',subjectId);
       console.log('parent id',$(this).data('parentid'));
       if($(this).data('parentid') == subjectId){
          if(valueSelected == -1){
             $('.mendatory_subject_parent_id'+subjectId).prop('selected', false);
             $('.optional_subject_parent_id'+subjectId).prop('selected', false);
             $('.inactive_subject_parent_id'+subjectId).prop('selected', true);
          }
          else if(valueSelected == 0){
             $('.mendatory_subject_parent_id'+subjectId).prop('selected', false);
             $('.inactive_subject_parent_id'+subjectId).prop('selected', false);
             $('.optional_subject_parent_id'+subjectId).prop('selected', true);
          }
          else if(valueSelected == 1){
             $('.inactive_subject_parent_id'+subjectId).prop('selected', false);
             $('.optional_subject_parent_id'+subjectId).prop('selected', false);
             $('.mendatory_subject_parent_id'+subjectId).prop('selected', true);
          }
       }   
    });
 });
}


function set_alternative_optional_or_mendatory_subject($){

   $('select').on('change', function (e) {

      var optionSelected = $("option:selected", this);
      var valueSelected = this.value;
      var subjectId = optionSelected.data('subjectid');
      var optional_subject_ids = [];
      $('.optional_subject').each(function(){
           var optional_subject_id = $(this).data('subjectid');
           optional_subject_ids.push(optional_subject_id);
      });
      var child_ids = [subjectId];
      $('.optional_subject_parent_id'+subjectId).each(function(){
         var optional_child_subject_id = $(this).data('subjectid');
         child_ids.push(optional_child_subject_id);
    });
    let aternative_optional_subject_ids = optional_subject_ids.filter(x => !child_ids.includes(x));
     console.log(optional_subject_ids);
     console.log(child_ids);
     console.log(aternative_optional_subject_ids);
    $('.subject_option').each(function(){
          if(valueSelected == 1){
            aternative_optional_subject_ids.forEach(function(aternative_optional_subject_id) {
               console.log(aternative_optional_subject_id);
           });
          }
          else if(valueSelected == 0){
            aternative_optional_subject_ids.forEach(function(aternative_optional_subject_id) {
               console.log(aternative_optional_subject_id);
           });
        
          }
         
    });
 });

}


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