/**
 * Created by khaled on 2/23/16.
 */

$(document).ready(function() {
  var idlist = [];
  var index;
  var stateInfo = [];
  var followerRuns = 0;
  var allowRecall = false;
  stateInfo.push({short:"AL", fullname:"Alabama", electType:"primary", eWhen:"3-1-16"});
  stateInfo.push({short:"AR", fullname:"Arkansas", electType:"primary", eWhen:"3-1-16"});
  stateInfo.push({short:"CO", fullname:"Colorado", electType:"caucus", eWhen:"3-1-16"});
  stateInfo.push({short:"FL", fullname:"Florida", electType:"primary", eWhen:"3-15-16"});
  stateInfo.push({short:"GA", fullname:"Georgia", electType:"primary", eWhen:"3-1-16"});
  stateInfo.push({short:"MA", fullname:"Massachusetts", electType:"primary", eWhen:"3-1-16"});
  stateInfo.push({short:"MN", fullname:"Minnesota", electType:"caucus", eWhen:"3-1-16"});
  stateInfo.push({short:"OK", fullname:"Oklahoma", electType:"primary", eWhen:"3-1-16"});
  stateInfo.push({short:"TN", fullname:"Tenessee", electType:"primary", eWhen:"3-1-16"});
  stateInfo.push({short:"TX", fullname:"Texas", electType:"primary", eWhen:"3-1-16"});
  stateInfo.push({short:"VT", fullname:"Vermont", electType:"primary", eWhen:"3-1-16"});
  stateInfo.push({short:"VA", fullname:"Virgina", electType:"primary", eWhen:"3-1-16"});

  stateInfo.push({short:"KS", fullname:"Kansas", electType:"primary", eWhen:"3-5-16"});
  stateInfo.push({short:"LA", fullname:"Louisiana", electType:"primary", eWhen:"3-5-16"});
  stateInfo.push({short:"NE", fullname:"Nebraska", electType:"primary", eWhen:"3-5-16"});
  stateInfo.push({short:"MN", fullname:"Maine", electType:"primary", eWhen:"3-6-16"});


  stateInfo.forEach(function(stateData){
    var today = new Date();
    var eventWhen = new Date(stateData.eWhen);
    var milsTill = eventWhen.getTime() - today.getTime();
    var daysTill = Math.ceil(milsTill / (1000 * 60 * 60 * 24));
    var hoursTill = Math.ceil(milsTill / (1000 * 60 * 60));
    var timeTill;
    if (hoursTill < 24) {
      if (hoursTill < 0) {
        var momentCheck = " passed";
      } else {
        $('#states-list')
         .append($("<option></option>")
         .attr("value", stateData.short)
         .text(stateData.fullname));
      }
    } else {
      if(daysTill == 1) {
        $('#states-list')
         .append($("<option></option>")
          .attr("value", stateData.short)
          .text(stateData.fullname));
      } else if (daysTill < 10) {
        $('#states-list')
         .append($("<option></option>")
          .attr("value", stateData.short)
          .text(stateData.fullname));

      }
    }
  });


  localStorage.setItem('twitter_ids', JSON.stringify(idlist));
  $('#states-list').change(function(){

    var stateValue = $('#states-list').val();
    if(stateValue == 'Unset') {
      $('#followers-state').text('Pick a State to find your Followers');
      $('#friends-list-container').html('');
      $('#friends-message').text('');
      $('#task-deadline').text('');
    } else {
      $("#states-list option[value='Unset']").remove();
      var stateName = $('#states-list option:selected').text();
      $.ajax({
        url: "/api/gettwitters.php",
        data: {"stateChoice": stateValue},
        success: function (msg) {
          $('#friends-list-container').html(msg);
          idlist = JSON.parse(localStorage.getItem('twitter_ids'));
          if (idlist != null) {
            idlist.forEach(function (item) {
              var friendItem = document.getElementById(item);
              if (friendItem != null) {
                friendItem.style.backgroundImage = 'url("/assets/img/ticklittle.png")';
                friendItem.style.backgroundRepeat = 'no-repeat';
                friendItem.style.backgroundPosition = 'right 5px center';
              }
            });
          }
          $('.friend-block').click(function () {
            if (this.className.indexOf('friend-messaged') == -1) {
              idlist = JSON.parse(localStorage.getItem('twitter_ids'));
              if (idlist === null) {
                index = -1;
              } else {
                index = idlist.indexOf(this.id);
              }
              if (index == -1) {
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
            }
          });
        }
      });
      var stateID = stateValue.toUpperCase();
      var electT, whenD;
      stateInfo.forEach(function (stInfo) {
        if (stInfo.short == stateID) {
          electT = stInfo.electType;
          whenD = stInfo.eWhen;
        }
      });
      var today = new Date();
      var eventWhen = new Date(whenD);
      var milsTill = eventWhen.getTime() - today.getTime();
      var daysTill = Math.ceil(milsTill / (1000 * 60 * 60 * 24));
      var hoursTill = Math.ceil(milsTill / (1000 * 60 * 60));
      var timeTill;
      if (hoursTill < 24) {
        if (hoursTill < 0) {
          timeTill = " has PASSED"
          var momentCheck = " today";
        } else {
          timeTill = " is in " + hoursTill + " hour"+(hoursTill > 1 ? 's' : '');
          var momentCheck = " today";
        }
      } else {
        timeTill = " is in " + daysTill + " day"+(daysTill > 1 ? 's' : '');
        if(daysTill == 1) {

        } else {
          var momentCheck = "";
        }
      }
      var stateinformationMsg = "Democratic " + electT.charAt(0).toUpperCase() + electT.slice(1) + timeTill;

      $('#task-deadline').text(stateinformationMsg);


      var stateMsg = "Bernie wins if there's large voter turnout. Will you vote for him in the " + stateID + " " + electT + "" + momentCheck + "? https://vote.berniesanders.com/" + stateID.toLowerCase() + " #TweetForBernie"
      $('#followers-state').text('Your Bernie Friends in ' + stateName + ": ");
      $('#friends-message').text(stateMsg);
    }
  });
  var syncUp = function() {

    $.ajax({
      url: "/api/buildtwitters.php",
      data: {rebuild_citizen: 'true'},
      beforeSend: function() {
        $('#sync-citizens').attr('disabled', 'disabled');
        $('#sync-citizens').html('<img src="/assets/img/ajax-loader.gif" width="50px"><span>Progress : '+followerRuns+'</span>');
      },
      success: function(msg) {
        $('#sync-citizens').removeAttr('disabled');
        if(msg == "-1") {
          $('#sync-citizens').html('Sync my Friends and Followers Again');
          allowRecall = false;
        } else if(msg == "RATE-LIMIT") {
          $('#sync-citizens').html('RATE LIMIT : Wait 15 minutes, and try Sync Again');
          allowRecall = false;
          setTimeout(function(){$('#sync-citizens').html('Sync my Friends and Followers Again');}, 900000);
        }else {
          followerRuns = followerRuns + 1;
          allowRecall = true;
        }
      },
      error: function() {
        $('#sync-citizens').removeAttr('disabled');
        $('#sync-citizens').html('ERROR IN SYNC: Try and sync my friends and followers again');
      },
      complete: function() {
        if(allowRecall) {
          syncUp();
        }
      }
    });
  };

  $('#sync-citizens').click(function(){
    /*
    $.ajax({
      url: "/api/buildtwitters.php",
      data: {rebuild_citizen: 'true'},
      beforeSend: function() {
        $('#sync-citizens').attr('disabled', 'disabled');
        $('#sync-citizens').html('<img src="/assets/img/ajax-loader.gif">');
      },
      success: function(msg) {
        $('#sync-citizens').removeAttr('disabled');
        if(msg == "-1") {
          $('#sync-citizens').html('Sync my friends and followers again');
        } else {

        }
        //$('#sync-citizens').html('Sync my friends and followers again');
      },
      error: function() {
        $('#sync-citizens').removeAttr('disabled');
        $('#sync-citizens').html('ERROR IN SYNC: Try and sync my friends and followers again');
      }
    });
    */
    syncUp();
  });

  var resync = document.getElementById('resync-rule');
  if(resync.dataset['resync'] == 'yes') {
    syncUp();
  }
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