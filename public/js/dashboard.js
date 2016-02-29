/**
 * Created by khaled on 2/23/16.
 */

$(document).ready(function() {
  var idlist = [];
  $('#states-list').change(function(){
    var stateValue = $('#states-list').val();
    var stateName = $('#states-list option:selected').text();
    $.ajax({
      url: "/api/gettwitters.php",
      data: {"stateChoice" : stateValue},
      success: function(msg) {
        $('#friends-list-container').html(msg);
        $('.friend-block').click(function()
        {
          idlist = idlist + this.id+";";
          localStorage.setItem('twitter_ids', idlist);
          this.style.backgroundImage = 'url("/assets/img/ticklittle.png")';
          this.style.backgroundRepeat = 'no-repeat';
          this.style.backgroundPosition = 'right center';
        });
      }
    });
    $('#followers-state').text('Your followers in '+stateName+": ");
    $('#friends-message').text('Voting in '+stateValue+' on Tuesday? ');
  });

  $('#sync-citizens').click(function(){
    alert("Clicked the Sync");
    $.ajax({
      url: "/api/buildtwitters.php",
      beforeSend: function() {
        $('#sync-citizens').attr('disabled', 'disabled');

      },
      success: function(msg) {
        $('#sync-citizens').removeAttr('disabled');
      }
    });
  });
});