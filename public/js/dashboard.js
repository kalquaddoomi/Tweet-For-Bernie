/**
 * Created by khaled on 2/23/16.
 */

$(document).ready(function() {
  var idlist = [];
  var index;
  localStorage.setItem('twitter_ids', JSON.stringify(idlist));
  $('#states-list').change(function(){
    var stateValue = $('#states-list').val();
    var stateName = $('#states-list option:selected').text();
    $.ajax({
      url: "/api/gettwitters.php",
      data: {"stateChoice" : stateValue},
      success: function(msg) {
        $('#friends-list-container').html(msg);
          idlist = JSON.parse(localStorage.getItem('twitter_ids'));
          if(idlist != null) {
            idlist.forEach(function (item) {
              var friendItem = document.getElementById(item);
              if(friendItem != null) {
                friendItem.style.backgroundImage = 'url("/assets/img/ticklittle.png")';
                friendItem.style.backgroundRepeat = 'no-repeat';
                friendItem.style.backgroundPosition = 'right center';
              }
            });
          }
        $('.friend-block').click(function()
        {
          idlist = JSON.parse(localStorage.getItem('twitter_ids'));
          if(idlist === null) {
            index = -1;
          } else {
            index = idlist.indexOf(this.id);
          }
          if(index == -1) {
            idlist.push(this.id);
            localStorage.setItem('twitter_ids', JSON.stringify(idlist));
            this.style.backgroundImage = 'url("/assets/img/ticklittle.png")';
            this.style.backgroundRepeat = 'no-repeat';
            this.style.backgroundPosition = 'right center';
          } else {
            idlist.splice(index, 1);
            localStorage.setItem('twitter_ids', JSON.stringify(idlist));
            this.style.backgroundImage = 'none';
            this.style.backgroundRepeat = 'none';
            this.style.backgroundPosition = 'none';
          }
        });
      }
    });
    $('#followers-state').text('Your followers in '+stateName+": ");
    $('#friends-message').text('Voting in '+stateValue+' on Tuesday? ');
  });

  $('#sync-citizens').click(function(){
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