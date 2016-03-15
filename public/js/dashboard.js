/**
 * Created by khaled on 2/23/16.
 */

$(document).ready(function() {
  var idlist = [];
  var index;
  var stateInfo = [];
  var followerRuns = 0;
  var allowRecall = false;
  $('#friends-message').attr('disabled', 'disabled');
  stateInfo.push({short:"AL", fullname:"Alabama", electType:"Primary", eWhen:"3-1-16"});
  stateInfo.push({short:"AR", fullname:"Arkansas", electType:"Primary", eWhen:"3-1-16"});
  stateInfo.push({short:"CO", fullname:"Colorado", electType:"Caucus", eWhen:"3-1-16"});
  stateInfo.push({short:"GA", fullname:"Georgia", electType:"Primary", eWhen:"3-1-16"});
  stateInfo.push({short:"MA", fullname:"Massachusetts", electType:"Primary", eWhen:"3-1-16"});
  stateInfo.push({short:"MN", fullname:"Minnesota", electType:"Caucus", eWhen:"3-1-16"});
  stateInfo.push({short:"OK", fullname:"Oklahoma", electType:"Primary", eWhen:"3-1-16"});
  stateInfo.push({short:"TN", fullname:"Tenessee", electType:"Primary", eWhen:"3-1-16"});
  stateInfo.push({short:"TX", fullname:"Texas", electType:"Primary", eWhen:"3-1-16"});
  stateInfo.push({short:"VT", fullname:"Vermont", electType:"Primary", eWhen:"3-1-16"});
  stateInfo.push({short:"VA", fullname:"Virgina", electType:"Primary", eWhen:"3-1-16"});
  stateInfo.push({short:"KS", fullname:"Kansas", electType:"Caucus", eWhen:"3-5-16 8:00:00"});
  stateInfo.push({short:"LA", fullname:"Louisiana", electType:"Primary", eWhen:"3-5-16 8:00:00"});
  stateInfo.push({short:"NE", fullname:"Nebraska", electType:"Caucus", eWhen:"3-5-16 7:00:00"});
  stateInfo.push({short:"MN", fullname:"Maine", electType:"Caucus", eWhen:"3-6-16 7:00:00"});
  stateInfo.push({short:"MI", fullname:"Michigan", electType:"Primary", eWhen:"3-8-16"});
  stateInfo.push({short:"MS", fullname:"Mississippi", electType:"Primary", eWhen:"3-8-16"});
  stateInfo.push({short:"IL", fullname:"Illinois", electType:"Primary", eWhen:"3-15-16"});
  stateInfo.push({short:"FL", fullname:"Florida", electType:"Primary", eWhen:"3-15-16"});
  stateInfo.push({short:"OH", fullname:"Maine", electType:"Primary", eWhen:"3-15-16"});
  stateInfo.push({short:"NC", fullname:"North Carolina", electType:"Primary", eWhen:"3-15-16"});
  stateInfo.push({short:"AZ", fullname:"Arizona", electType:"Primary", eWhen:"3-22-16"});
  stateInfo.push({short:"UT", fullname:"Utah", electType:"Caucus", eWhen:"3-22-16"});
  stateInfo.push({short:"ID", fullname:"Idaho", electType:"Caucus", eWhen:"3-22-16"});
  stateInfo.push({short:"WA", fullname:"Washington", electType:"Caucus", eWhen:"3-26-16"});
  stateInfo.push({short:"HI", fullname:"Hawaii", electType:"Caucus", eWhen:"3-26-16"});
  stateInfo.push({short:"AK", fullname:"Alaska", electType:"Caucus", eWhen:"3-26-16"});
  stateInfo.push({short:"WI", fullname:"Wisconsin", electType:"Primary", eWhen:"4-5-16"});
  stateInfo.push({short:"WY", fullname:"Wyoming", electType:"Caucus", eWhen:"4-9-16"});
  stateInfo.push({short:"NY", fullname:"New York", electType:"Primary", eWhen:"4-19-16"});
  stateInfo.push({short:"PA", fullname:"Pennsylvania", electType:"Primary", eWhen:"4-26-16"});
  stateInfo.push({short:"CT", fullname:"Connecticut", electType:"Primary", eWhen:"4-26-16"});
  stateInfo.push({short:"MD", fullname:"Maryland", electType:"Primary", eWhen:"4-26-16"});
  stateInfo.push({short:"RI", fullname:"Rhode Island", electType:"Primary", eWhen:"4-26-16"});
  stateInfo.push({short:"DE", fullname:"Delaware", electType:"Primary", eWhen:"4-26-16"});
  stateInfo.push({short:"IN", fullname:"Indiana", electType:"Primary", eWhen:"5-3-16"});
  stateInfo.push({short:"WV", fullname:"West Virginia", electType:"Primary", eWhen:"5-10-16"});
  stateInfo.push({short:"OR", fullname:"Oregon", electType:"Primary", eWhen:"5-17-16"});
  stateInfo.push({short:"KY", fullname:"Kentucky", electType:"Primary", eWhen:"5-17-16"});
  stateInfo.push({short:"PR", fullname:"Puerto Rico", electType:"Primary", eWhen:"6-5-16"});
  stateInfo.push({short:"CA", fullname:"California", electType:"Primary", eWhen:"6-7-16"});
  stateInfo.push({short:"NJ", fullname:"New Jersy", electType:"Primary", eWhen:"6-7-16"});
  stateInfo.push({short:"NM", fullname:"New Mexico", electType:"Primary", eWhen:"6-7-16"});
  stateInfo.push({short:"MT", fullname:"Montana", electType:"Primary", eWhen:"6-7-16"});
  stateInfo.push({short:"ND", fullname:"North Dakota", electType:"Primary", eWhen:"6-7-16"});
  stateInfo.push({short:"SD", fullname:"South Dakota", electType:"Primary", eWhen:"6-7-16"});
  stateInfo.push({short:"DC", fullname:"District of Columbia", electType:"Primary", eWhen:"6-14-16"});

  stateInfo.push({short:"UNK", fullname:"Lcoation Unknown", electType:"", eWhen:"12-31-99"});






  var today = new Date();
  stateInfo.forEach(function(stateData){
    var eventWhen = new Date(stateData.eWhen);
    var milsTill = eventWhen.getTime() - today.getTime();
    var daysTill = Math.ceil(milsTill / (1000 * 60 * 60 * 24));
    var hoursTill = Math.ceil(milsTill / (1000 * 60 * 60));
    var timeTill;

    if (hoursTill < 0) {
       var momentCheck = " passed";
     } else if((today.getDate() == eventWhen.getDate()) && (today.getMonth() == eventWhen.getMonth())) {
        $('#states-list')
         .append($("<option></option>")
         .attr("value", stateData.short)
         .text(stateData.fullname+" ("+stateData.electType+" Today)"));

    } else {
      if(eventWhen.getDate() - today.getDate() == 1 && (today.getMonth() == eventWhen.getMonth())) {
        $('#states-list')
         .append($("<option></option>")
          .attr("value", stateData.short)
          .text(stateData.fullname+" ("+stateData.electType+" Tomorrow)")
        );
      } else if (daysTill < 10) {
        $('#states-list')
         .append($("<option></option>")
          .attr("value", stateData.short)
          .text(stateData.fullname+" ("+stateData.electType+" In "+daysTill+" Days)"));

      }
    }
  });

  $('#states-list')
   .append($("<option></option>")
    .attr("value", "UNK")
    .text("Followers with Unknown Locations"));

  localStorage.setItem('twitter_ids', JSON.stringify(idlist));
  $('#states-list').change(function(){
    $('#friends-message').attr('disabled', 'disabled');
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
            if( this.className.indexOf('state-unknown') == -1) {
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
            } else {

            }
          });
        }
      });
      var stateID = stateValue.toUpperCase();
      var electT, whenD, fullname;
      stateInfo.forEach(function (stInfo) {
        if (stInfo.short == stateID) {
          electT = stInfo.electType;
          whenD = stInfo.eWhen;
          fullname = stInfo.fullname;
        }
      });
      var today = new Date();
      var eventWhen = new Date(whenD);
      var milsTill = eventWhen.getTime() - today.getTime();
      var daysTill = Math.ceil(milsTill / (1000 * 60 * 60 * 24));
      var hoursTill = Math.ceil(milsTill / (1000 * 60 * 60));
      var momentCheck;
      var timeTill;
      if (hoursTill < 0) {
        momentCheck = " passed";
      } else if((today.getDate() == eventWhen.getDate()) && (today.getMonth() == eventWhen.getMonth())) {
        momentCheck = " today";
      } else {
        if(eventWhen.getDate() - today.getDate() == 1 && (today.getMonth() == eventWhen.getMonth())) {
          momentCheck = " tomorrow";
        } else if (daysTill < 10) {
            momentCheck = " in "+daysTill+" days"
        }
      }
      var stateinformationMsg = "Democratic " + electT + momentCheck;

      $('#task-deadline').text(stateinformationMsg);


      var stateMsg = "Bernie wins if there's a large voter turnout. This political revolution depends on "+fullname+". Will you vote for him in the " + stateID + " " + electT.toLowerCase() + "" + momentCheck + "? https://vote.berniesanders.com/" + stateID.toLowerCase() + " #TweetForBernie"
      $('#followers-state').text('Your Bernie Friends in ' + stateName + ": ");
      if(stateID == 'UNK') {
        stateMsg = 'We need help finding where your Followers with Unknown Locations reside. Please click on them and provide their State if you know it.';
        $('#send-messages').hide();
      }
      $('#friends-message').text(stateMsg);
      if(stateID != 'UNK') {
        $('#friends-message').attr('disabled', false);
        $('#send-messages').show();
      }

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