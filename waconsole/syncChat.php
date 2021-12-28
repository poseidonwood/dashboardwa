<?php
include_once("../configuration.inc");
include_once("../includes/koneksi.php");
include_once("../includes/function.php");
// $url_example = MESSAGING_WEB_IMAGES;
$url_example = "https://messaging.fekusa.xyz/images/";
$q = getContacts();
while ($row = mysqli_fetch_assoc($q)) {
  $row['remoteJid'] = $row['rJid'];
  if (preg_match("/g\.us/", $row['remoteJid']))
    $row['remoteJid'] = explode("|", $row['remoteJid'])[0];

  $last = getLastMsg($row['remoteJid']);
  $row['nomor'] = explode("@", $row['remoteJid'])[0];

  $profile_data = getContactProfile($row['remoteJid']);

  if ($profile_data['profile_picture_path'] != "" or $profile_data['profile_picture_path'] != "undefined") {
    if ($profile_data['profile_picture_path'] == "") {
      $profile_pict_path1 = "../img/empty1.png";
    } else {
      $profile_pict_path2 = $url_example . "whatsapp/profile/" . $profile_data['profile_picture_path'];
      $profile_pict_path1 = $profile_pict_path2;
    }
    $profile_pict_path = $profile_pict_path1;
  } else {
    $profile_pict_path = "../img/empty1.png";
  }

  $n = "";
  if ($profile_data['name'] != "") {
    $n = $profile_data['name'] . "<br />";
  }

?>

  <li class="chats-item">
    <div class="chats-item-button js-chat-button chatdiv" role="button" tabindex="0" data-shortjid="<?php
                                                                                                    if (preg_match("/c\.us/", $row['remoteJid']))
                                                                                                      echo $row['nomor'];
                                                                                                    else
                                                                                                      echo str_replace("@g.us", "", $row['remoteJid']);
                                                                                                    ?>" data-nomor="<?= $row['remoteJid'] ?>" data-name="<?= $n ?><?php
                                                                                                                                                                  if (!preg_match("/g\.us/", $row['remoteJid'])) echo $row['nomor']; ?>">
      <img class="profile-image" onerror="this.onerror=null; this.remove();" id="<?= $row['nomor'] ?>_pp" src="<?php echo $profile_pict_path; ?>" alt="<?= $profile_data['profile_picture_path']; ?>">
      <header class="chats-item-header">
        <h3 class="chats-item-title"><?= $n ?><?php
                                              if (!preg_match("/g\.us/", $row['remoteJid'])) echo $row['nomor'];
                                              ?></h3>
        <time class="chats-item-time tanggal-terakhir-<?= $row['nomor'] ?>"><?= $last['messageTimestamp'] ?></time>
      </header>
      <div class="chats-item-content">
        <p class="chats-item-last pesan-terakhir-<?= $row['nomor'] ?>"><?= $last['message'] ?></p>
        <!-- <p class="chats-item-last pesan-terakhir><?= $row['nomor'] ?>"></p> -->
        <ul class="chats-item-info">
          <li class="chats-item-info-item"><span class="unread-messsages"></span></li>
        </ul>
      </div>
    </div>
  </li>
<?php
}

// Disabled IF Production
// $nomor = no_wa();
// getChatMessages($nomor . "@c.us");
?>
<script src="./script.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js" integrity="sha512-qTXRIMyZIFb8iQcfjXWCO8+M5Tbc38Qi5WzdPOYZHIlZpzBHG3L3by84BBBOiRGiEb7KKtAOAs5qYdUiZiQNNQ==" crossorigin="anonymous"></script>

<center><button class="common-button">
    <span class="icon btn-refresh" id="refresh-btn" style="font-size:10pt"><i class="fas fa-sync"></i></span>
  </button></center>
<script>
  let limitplus = 20;

  $('.chatdiv').click(function(data) {
    $("#spinner").show();
    $("#loadmoreclick").hide();
    let jid = $(this).data("nomor");
    let nomor = $(this).data("shortjid");
    let name = $(this).data("name");
    $(".pesan-container").empty();
    $(".nomor").empty();
    $(".nomor").val(nomor);
    $(".nama-container").html(name);
    $("#common-header-profile-image").attr('src', $("#" + nomor + "_pp").attr('src'));
    $(".main-info-image").attr('src', $("#" + nomor + "_pp").attr('src'));
    $.get("get_chat.php?nomor=" + jid, function(data) {
      r = JSON.parse(data);
      const inputlimit = "<input type='hidden' id='limitloadmore" + nomor + "' value='" + limitplus + "'>";
      jQuery.each(r, function(i, val) {
        let from_me = "";
        //console.log(val.fromMe);
        if (val.fromMe == false) {
          from_me = "is-other";
        } else {
          from_me = "is-you";
        }
        let chat = `<li onclick="pop('` + val.msgID + `')"  class='common-message ` + from_me + `' id='message` + val.msgID + `' >` +
          `<p class='common-message-content'>` + val.message + `</p>` +
          `<time datetime>` + val.tanggal + `</time>` +
          `<span class='status'>` + val.ack + `</span>` +
          `</li>`;
        $(".pesan-container").prepend(chat);
        $("#message-box").show();
        $("#header-webconsole").show();
        updateScroll();
      });
      $("#spinner").hide();
      $("#loadmoreclick").show();
      $("#loadmore").html(`<a href="#" id="loadmoreclick" onclick="loadmore('` + nomor + `')">Load More</a><br>` + inputlimit);
      localStorage.setItem('nomor', nomor);
    });
  });
  // Get the modal
  var modal = document.getElementById("myModal");

  // Get the button that opens the modal
  var btn = document.getElementById("myBtn");

  // Get the <span> element that closes the modal
  var span = document.getElementsByClassName("close")[0];

  // When the user clicks the button, open the modal 


  // When the user clicks on <span> (x), close the modal
  span.onclick = function() {
    modal.style.display = "none";
  }

  // When the user clicks anywhere outside of the modal, close it
  window.onclick = function(event) {
    if (event.target == modal) {
      modal.style.display = "none";
    }
  }

  function pop(message) {
    $.ajax({
      url: "./get_chat.php?msgID=" + message,
      success: function(data) {
        $("#modal-content-message").html(data);
        console.log(data);
        modal.style.display = "block";
      }
    });
  }
  $('.btn-refresh').click(function(data) {
    $("#chats-list").hide();
    $("#loading").show();
    $("#spinner-chat").show();
    $("#refresh-btn").hide();
    $.ajax({
      url: "./syncChat.php",
      success: function(data) {
        //$(".common-message").toggle();
        $("#chats-list").html(data);
        $("#loading").hide();
        $("#refresh-btn").show();
        $("#spinner-chat").hide();
        $("#chats-list").show();
      }
    });
  });
</script>