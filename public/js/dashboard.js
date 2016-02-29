/**
 * Created by khaled on 2/23/16.
 */

$(document).ready(function() {
  var idlist = [];
  var index;
  var stateInfo = [];
  stateInfo.push({short:"AL", fullname:"Alabama", electType:"primary"});
  stateInfo.push({short:"AR", fullname:"Arkansas", electType:"primary"});
  stateInfo.push({short:"CO", fullname:"Colorado", electType:"caucus"});
  stateInfo.push({short:"FL", fullname:"Florida", electType:"primary"});
  stateInfo.push({short:"GA", fullname:"Georgia", electType:"primary"});
  stateInfo.push({short:"MA", fullname:"Massachusetts", electType:"primary"});
  stateInfo.push({short:"MN", fullname:"Minnesota", electType:"caucus"});
  stateInfo.push({short:"OK", fullname:"Oklahoma", electType:"primary"});
  stateInfo.push({short:"TN", fullname:"Tenessee", electType:"primary"});
  stateInfo.push({short:"TX", fullname:"Texas", electType:"primary"});

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
                friendItem.style.backgroundPosition = 'right 5px center';
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
    var stateID = stateValue.toUpperCase();
    var electT;
    stateInfo.forEach(function(stInfo) {
      if(stInfo.short == stateID) {
        electT = stInfo.electType;
      }
    });
    var today = new Date();
    var dd = today.getUTCDate();
    var hours = today.getUTCHours();
    if(dd == 29) {
      var momentCheck = "tomorrow";
    } else if(dd == 1 && hours == 10) {
      var momentCheck = "today";
    }

    var stateMsg = "Bernie wins if there's large voter turnout. Will you vote for him in the "+stateID+" "+electT+" "+momentCheck+"? https://vote.berniesanders.com/"+stateID.toLowerCase()+"#TweetsForBernie"
    $('#followers-state').text('Your Bernie Friends in '+stateName+": ");
    $('#friends-message').text(stateMsg);
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

  $('#send-messages').click(function(){
    var friendMsg = $('#friends-message').val();
    alert(friendMsg);
    idlist = JSON.parse(localStorage.getItem('twitter_ids'));
    idlist.forEach(function (item) {
      var friendItem = document.getElementById(item);
      if(friendItem != null) {
        $.ajax({
          url: "/api/sendmessages.php",
          data: {citizenId: item, tw_message: friendMsg},
          success: function (msg) {
           if(msg == "SUCCESS") {
             friendItem.style.backgroundImage = 'url("/assets/img/mailsendiconmini.png")';
             friendItem.style.backgroundRepeat = 'no-repeat';
             friendItem.style.backgroundPosition = 'right 5px center';

           }
          }
        });
      }
    });
  });
});