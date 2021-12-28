<?php
include_once("../configuration.inc");
$activity[0] = "messaging";
$activity[1] = "waconsole-main";
include_once("../header.php");
include_once("../includes/koneksi.php");
include_once("../includes/function.php");
// include_once COMMON_INC_DIR . "class.configs.php";
// $url_example = "http://10.8.9.1/aluciodev/messaging/images/";
// $url_example = MESSAGING_WEB_IMAGES;
$url_example = "https://messaging.fekusa.xyz/images/";
// var_dump(MESSAGING_WEB_IMAGES);
if (!isset($user_session)) {
  $login = cekSession();
  if ($login == 0) {
    redirect("../login.php");
  }
}

// $mConfig = new cConfigs(true, "whatsapp");
// $self_jid = $mConfig->GetConfig("wa_jid", "whatsapp");
// unset($mConfig);
$self_jid = no_wa() . "@c.us";
$self_profile_data = getContactProfile($self_jid);

if ($self_profile_data['profile_picture_path'] != "") {
  $self_profile_pict_path = $url_example . "whatsapp/profile/" . $self_profile_data['profile_picture_path'];
} else {
  $self_profile_pict_path = "../img/empty1.png";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>WA Console</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
  <link rel="stylesheet" href="./style.css">
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
  <style>
    * {
      font-family: "Segoe UI", "Helvetica Neue", Helvetica, "Lucida Grande", Arial, Ubuntu, Cantarell, "Fira Sans", sans-serif;
    }
  </style>
</head>

<body>
  <!-- partial:index.partial.html -->
  <section class="main-grid">
    <aside class="main-side">

      <header class="common-header">
        <div class="common-header-start">
          <button class="u-flex js-user-nav">
            <img class="profile-image" onerror="this.onerror=null; this.remove();" src="<?php echo $self_profile_pict_path; ?>" alt="">
            <div class="common-header-content">
              <h6 class="common-header-title" id="device_info"><?php echo $self_profile_data['name']; ?>
              </h6>
            </div>
          </button>
        </div>
        <!-- <i class="btn btn-block btn-refresh btn-primary" id="refresh-btn" style="font-size:20px"> Option </i> -->
        <ul class="common-nav-list pull-right">
          <li class="common-nav-item">
            <button class="common-button">
              <span class="icon btn-refresh" id="refresh-btn" style="font-size:10pt"><i class="fas fa-sync"></i> Restart</span>
            </button>

            <!-- Modal -->
            <div id="myModal" class="modal">
              <!-- Modal content -->
              <div class="modal-content">
                <span class="close">&times;</span>
                <p id='modal-content-message' class="common-message-content"></p>
              </div>
            </div>
          </li>
        </ul>
        <div id="loading">
          <center><img src="../images/loading-buffering.gif" width="50px"></center>
        </div>

        <nav class="common-nav d-none">
          <ul class="common-nav-list">
            <li class="common-nav-item">
              <button class="common-button">
                <span class="icon">🕘</span>
              </button>
            </li>
            <li class="common-nav-item">
              <button class="common-button">
                <span class="icon icon-status">💬</span>
              </button>
            </li>
            <li class="common-nav-item">
              <button class="common-button">
                <span class="icon icon-menu" aria-label="menu"></span>
              </button>
            </li>
          </ul>
        </nav>
      </header>

      <!-- <section class="common-alerts">
      </section> -->
      <section class="common-search">
        <input type="search" class="text-input" placeholder="Cari pembicaraan" id="chatsearch">
        <a href="#" class="search-icon">
          <i class="fa fa-search"></i>
        </a>
      </section>
      <section class="chats">
        <div class="spinner" id="spinner-chat">
          <div class="rect1"></div>
          <div class="rect2"></div>
          <div class="rect3"></div>
          <div class="rect4"></div>
          <div class="rect5"></div>
        </div>
        <ul class="chats-list" id="chats-list">
        </ul>
      </section>
    </aside>
    <main class="main-content">
      <div id="header-webconsole">
        <header class="common-header">
          <div class="common-header-start" id="common-header-start">
            <button class="common-button is-only-mobile u-margin-end js-back"><span class="icon icon-back">⬅</span></button>
            <button class="u-flex js-side-info-button">
              <img class="profile-image" onerror="this.onerror=null; this.remove();" id='common-header-profile-image' src="<?= "../img/empty1.png"; ?>">
              <div class="common-header-content">
                <h2 class="common-header-title nama-container">Alucio Net</h2>
                <p class="common-header-status"></p>
              </div>
            </button>
          </div>
          <nav class="common-nav">
            <ul class="common-nav-list">
              <li class="common-nav-item">
                <button class="common-button">
                  <span class="icon synclatest"><i class="fas fa-sync"></i></span>
                </button>
              </li>
              <li class="common-nav-item">
                <button class="common-button">
                  <span class="icon icon-attach clearchat"><i class="fas fa-trash"></i></span>
                </button>
              </li>
              <li class="common-nav-item">
                <button class="common-button u-animation-click js-side-info-button">
                  <span class="icon icon-attach clearchat"><i class="fas fa-bars"></i></span>
                </button>
              </li>
            </ul>
          </nav>
        </header>
      </div>

      <div class="messanger" id="msg-container">
        <br>
        <center>
          <div id="loadmore"></div>
          <input type="hidden" id="lastload">
        </center>
        <div class="spinner" id="spinner">
          <div class="rect1"></div>
          <div class="rect2"></div>
          <div class="rect3"></div>
          <div class="rect4"></div>
          <div class="rect5"></div>
        </div>
        <ol class="messanger-list pesan-container" id="pesan-container">
          <li class="common-message is-time">
            <p class="common-message-content">
              DEVELOPED BY Alucio Net
            </p>
          </li>
          <br>
          <br>
          <br>
          <br>
          <center>
            <div class="ldL67 _3sh5K">
              <div class="zaKsw">
                <div class="_1RAKT">
                  <div class="WM0_u" style="transform: scale(1); opacity: 1;"><span data-testid="intro-md-beta-logo-light" data-icon="intro-md-beta-logo-light" class="IVxyB"><svg width="360" viewBox="0 0 303 172" fill="none" preserveAspectRatio="xMidYMid meet" class="">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M229.565 160.229c32.647-10.984 57.366-41.988 53.825-86.81-5.381-68.1-71.025-84.993-111.918-64.932C115.998 35.7 108.972 40.16 69.239 40.16c-29.594 0-59.726 14.254-63.492 52.791-2.73 27.933 8.252 52.315 48.89 64.764 73.962 22.657 143.38 13.128 174.928 2.513z" fill="#DAF7F3"></path>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M131.589 68.942h.01c6.261 0 11.336-5.263 11.336-11.756S137.86 45.43 131.599 45.43c-5.081 0-9.381 3.466-10.822 8.242a7.302 7.302 0 0 0-2.404-.405c-4.174 0-7.558 3.51-7.558 7.838s3.384 7.837 7.558 7.837h13.216zM105.682 128.716c3.504 0 6.344-2.808 6.344-6.27 0-3.463-2.84-6.27-6.344-6.27-1.156 0-2.24.305-3.173.839v-.056c0-6.492-5.326-11.756-11.896-11.756-5.29 0-9.775 3.413-11.32 8.132a8.025 8.025 0 0 0-2.163-.294c-4.38 0-7.93 3.509-7.93 7.837 0 4.329 3.55 7.838 7.93 7.838h28.552z" fill="#fff"></path>
                        <rect x=".445" y=".55" width="50.58" height="100.068" rx="7.5" transform="rotate(6 -391.775 121.507) skewX(.036)" fill="#42CBA5" stroke="#316474"></rect>
                        <rect x=".445" y=".55" width="50.403" height="99.722" rx="7.5" transform="rotate(6 -356.664 123.217) skewX(.036)" fill="#fff" stroke="#316474"></rect>
                        <path d="M57.16 51.735l-8.568 82.024a5.495 5.495 0 0 1-6.042 4.895l-32.97-3.465a5.504 5.504 0 0 1-4.897-6.045l8.569-82.024a5.496 5.496 0 0 1 6.041-4.895l5.259.553 22.452 2.36 5.259.552a5.504 5.504 0 0 1 4.898 6.045z" fill="#EEFEFA" stroke="#316474"></path>
                        <path d="M26.2 102.937c.863.082 1.732.182 2.602.273.238-2.178.469-4.366.69-6.546l-2.61-.274c-.238 2.178-.477 4.365-.681 6.547zm-2.73-9.608l2.27-1.833 1.837 2.264 1.135-.917-1.838-2.266 2.27-1.833-.92-1.133-2.269 1.834-1.837-2.264-1.136.916 1.839 2.265-2.27 1.835.92 1.132zm-.816 5.286c-.128 1.3-.265 2.6-.41 3.899.877.109 1.748.183 2.626.284.146-1.31.275-2.614.413-3.925-.878-.092-1.753-.218-2.629-.258zm16.848-8.837c-.506 4.801-1.019 9.593-1.516 14.396.88.083 1.748.192 2.628.267.496-4.794 1-9.578 1.513-14.37-.864-.143-1.747-.192-2.625-.293zm-4.264 2.668c-.389 3.772-.803 7.541-1.183 11.314.87.091 1.74.174 2.601.273.447-3.912.826-7.84 1.255-11.755-.855-.15-1.731-.181-2.589-.306-.04.156-.069.314-.084.474zm-4.132 1.736c-.043.159-.06.329-.077.49-.297 2.896-.617 5.78-.905 8.676l2.61.274c.124-1.02.214-2.035.33-3.055.197-2.036.455-4.075.627-6.115-.863-.08-1.724-.17-2.585-.27z" fill="#316474"></path>
                        <path d="M17.892 48.489a1.652 1.652 0 0 0 1.468 1.803 1.65 1.65 0 0 0 1.82-1.459 1.652 1.652 0 0 0-1.468-1.803 1.65 1.65 0 0 0-1.82 1.459zM231.807 136.678l-33.863 2.362c-.294.02-.54-.02-.695-.08a.472.472 0 0 1-.089-.042l-.704-10.042a.61.61 0 0 1 .082-.054c.145-.081.383-.154.677-.175l33.863-2.362c.294-.02.54.02.695.08.041.016.069.03.088.042l.705 10.042a.61.61 0 0 1-.082.054 1.678 1.678 0 0 1-.677.175z" fill="#fff" stroke="#316474"></path>
                        <path d="M283.734 125.679l-138.87 9.684c-2.87.2-5.371-1.963-5.571-4.823l-6.234-88.905c-.201-2.86 1.972-5.35 4.844-5.55l138.87-9.684c2.874-.2 5.371 1.963 5.572 4.823l6.233 88.905c.201 2.86-1.971 5.349-4.844 5.55z" fill="#fff"></path>
                        <path d="M144.864 135.363c-2.87.2-5.371-1.963-5.571-4.823l-6.234-88.905c-.201-2.86 1.972-5.35 4.844-5.55l138.87-9.684c2.874-.2 5.371 1.963 5.572 4.823l6.233 88.905c.201 2.86-1.971 5.349-4.844 5.55" stroke="#316474"></path>
                        <path d="M278.565 121.405l-129.885 9.058c-2.424.169-4.506-1.602-4.668-3.913l-5.669-80.855c-.162-2.31 1.651-4.354 4.076-4.523l129.885-9.058c2.427-.169 4.506 1.603 4.668 3.913l5.669 80.855c.162 2.311-1.649 4.354-4.076 4.523z" fill="#EEFEFA" stroke="#316474"></path>
                        <path d="M230.198 129.97l68.493-4.777.42 5.996c.055.781-.098 1.478-.363 1.972-.27.5-.611.726-.923.748l-165.031 11.509c-.312.022-.681-.155-1.017-.613-.332-.452-.581-1.121-.636-1.902l-.42-5.996 68.494-4.776c.261.79.652 1.483 1.142 1.998.572.6 1.308.986 2.125.929l24.889-1.736c.817-.057 1.491-.54 1.974-1.214.413-.577.705-1.318.853-2.138z" fill="#42CBA5" stroke="#316474"></path>
                        <path d="M230.367 129.051l69.908-4.876.258 3.676a1.51 1.51 0 0 1-1.403 1.61l-168.272 11.735a1.51 1.51 0 0 1-1.613-1.399l-.258-3.676 69.909-4.876a3.323 3.323 0 0 0 3.188 1.806l25.378-1.77a3.32 3.32 0 0 0 2.905-2.23z" fill="#fff" stroke="#316474"></path>
                        <ellipse rx="15.997" ry="15.997" transform="rotate(-3.989 1304.861 -2982.552) skewX(.021)" fill="#42CBA5" stroke="#316474"></ellipse>
                        <path d="M208.184 87.11l-3.407-2.75-.001-.002a1.952 1.952 0 0 0-2.715.25 1.89 1.89 0 0 0 .249 2.692l.002.001 5.077 4.11v.001a1.95 1.95 0 0 0 2.853-.433l8.041-12.209a1.892 1.892 0 0 0-.573-2.643 1.95 1.95 0 0 0-2.667.567l-6.859 10.415z" fill="#fff" stroke="#316474"></path>
                      </svg></span></div>
                  <div class="_3q5qB" style="opacity: 1; transform: translateY(0px);">
                    <div class="_1vjYt">
                      <br>
                      <h1>WhatsApp Web Alucio </h1>
                      <div class="mDUle"><b class="_3Lm9O">Dev</b></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </center>
        </ol>
      </div>

      <div class="message-box" id="message-box">
        <input class="text-input pesan" id="message-box" placeholder="Type a message" contenteditable required>
        <input type="hidden" class="nomor">
        <button id="voice-button" class="common-button send-button" type="submit"><span class="icon"><i class="fas fa-paper-plane"></i></span></button>
      </div>
    </main>
    <aside class="main-info u-hide">
      <header class="common-header">
        <button class="common-button js-close-main-info"><span class="icon">&#10060;</span></button>
        <div class="common-header-content">
          <h3 class="common-header-title">Info</h3>
        </div>
      </header>
      <div class="main-info-content">
        <section class="common-box">
          <img class="main-info-image" src="<?= "../img/empty1.png"; ?>" alt="">
          <h4 class="big-title nama-container"></h4>
          <p class="info-text"></p>
        </section>
        <section class="common-box">
          <h5 class="section-title">Description</h5>
          <p>==COMING SOON==</p>
        </section>
      </div>
    </aside>
  </section>

  <!-- partial -->
  <script src="./script.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js" integrity="sha512-qTXRIMyZIFb8iQcfjXWCO8+M5Tbc38Qi5WzdPOYZHIlZpzBHG3L3by84BBBOiRGiEb7KKtAOAs5qYdUiZiQNNQ==" crossorigin="anonymous"></script>
  <script>
    function updateScroll() {
      //TODO: Hanya waktu load pertama kali dipanggil
      var element = document.getElementById("msg-container");
      element.scrollTop = element.scrollHeight;
    }
    // setInterval(getdevice, 3000);

    function getdevice() {
      let url = "./getdetaildevice.php";
      $.ajax({
        url: url,
      }).done(function(data) {
        var json = data,
          obj = JSON.parse(JSON.stringify(json));
        var datanya = "<b>" + obj.name + "<br>" + obj.number + "</b>";
        $("#device_info").html(datanya);
      });
    }

    function sync() {
      let sync = localStorage.getItem('sync');
      let nomor = localStorage.getItem('nomor');

      if (nomor != null) {
        if (sync == null) {
          sync = moment().format("YYYY-MM-DD HH:mm:ss");
          localStorage.setItem('sync', sync);
        }

        $.get("longpooling.php?nomor=" + nomor + "&lastsync=" + sync, function(data) {
          r = JSON.parse(data);
          jQuery.each(r, function(i, val) {
            let from_me = "";
            console.log(val.from_me);
            if (val.from_me == "0") {
              from_me = "is-other";
            } else {
              from_me = "is-you";
            }
            let chat = `<li onclick="pop('` + val.msgID + `')"  class='common-message ` + from_me + `' id='message` + val.msgID + `' >` +
              `<p class='common-message-content'>` + val.message + `</p>` +
              `<time datetime>` + val.tanggal + `</time>` +
              `<span class='status'>` + val.ack + `</span>` +
              `</li>`;
            $(".pesan-container").append(chat);
            $(".pesan-terakhir-" + nomor).html(val.pesan);
            $(".tanggal-terakhir-" + nomor).html(val.tanggal);
            updateScroll();
          });
        });
        localStorage.setItem('sync', moment().format("YYYY-MM-DD HH:mm:ss"));
      } else {
        console.log("nothing");
      }
    }
    let limitplus = 20;

    function loadmore(nomor) {
      $("#loadmoreclick").hide();
      $("#spinner").show();
      $(".pesan-container").empty();
      $(".nomor").empty();
      let limit = $("#limitloadmore" + nomor).val();
      let lengthnomor = nomor.toString().length
      // console.log(lengthnomor);
      let nomorformat = ""

      if (lengthnomor > 15) {
        nomorformat = nomor + "@g.us";
      } else {
        nomorformat = nomor + "@c.us";
      }
      $.get("get_chat.php?nomor=" + nomorformat + "&limit=" + limit, function(data) {
        r = JSON.parse(data);
        count = Object.keys(r).length;
        let last_number = 0;
        if (limit == limitplus) {
          last_number = 5;
        } else {
          last_number = parseInt(count) - parseInt(limitplus);
        }


        let no = 1;
        jQuery.each(r, function(i, val) {

          let from_me = "";
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
          // updateScroll();
          let limitnew = parseInt(limit) + parseInt(limitplus);
          $("#limitloadmore" + nomor).val(limitnew);
          if (no == last_number) {
            console.log(no + " and " + last_number);
            $("#lastload").val("message" + val.msgID);
          }
          // if (no == parseInt(count)) {
          //   $("#loadmoreclick").hide();
          // }
          // console.log(no + " dan " + count);
          no++;
        });
        $("#spinner").hide();
        $("#loadmoreclick").show();
        var getlastload = $("#lastload").val();
        loadbyid(getlastload);
        localStorage.setItem('nomor', nomor);
      });
    }

    function loadbyid(id) {
      $("#msg-container").animate({
        scrollTop: $("#" + id).offset().top - document.body.clientHeight + $("#" + id).height()
      }, 0);

    }
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

    function syncChat() {
      $("#chats-list").hide();
      $("#spinner-chat").show();
      $.ajax({
        url: "./syncChat.php",
        success: function(data) {
          //$(".common-message").toggle();
          $("#chats-list").html(data);
          $("#spinner-chat").hide();
          $("#chats-list").show();
        }
      });
    }

    async function syncChatEndless() {
      try {
        $.ajax({
          url: "./syncChat.php",
          success: function(data) {
            //$(".common-message").toggle();
            $("#chats-list").html(data);
          }
        });
      } catch (err) {
        console.log(err);
      }
    }

    $(document).ready(function() {
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
      setInterval(syncChatEndless, 30000);
      syncChat();
      $("#loading").hide();
      $("#message-box").hide();
      $("#header-webconsole").hide();
      $("#spinner").hide();
      $("#spinner-chat").show();
      $("#chats-list").hide();


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
      let input = document.getElementById("message-box");
      input.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
          event.preventDefault();
          document.getElementById("voice-button").click();
        }
      });

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
      $('.btn-reset').click(function(data) {
        // $.get("refresh.php", function(data) {
        $("#loading").show();
        $("#refresh-btn").hide();
        $.ajax({
          url: "./resetChat.php"
        });
        // window.location.href = "<?= callback_wa() . 'waconsole'; ?>";
      });

      $("#chatsearch").on("keyup keydown change", function() {
        if ($(this).val().length >= 3) {
          var regex = new RegExp('\\b\\w*' + $(this).val() + '\\w*\\b', "i");
          $('[data-name]').hide().filter(function() {
            return regex.test($(this).data('name'))
          }).show();
        } else
          $('[data-name]').show();
      });

      $('.clearchat').click(function(data) {
        let nomor = $(".nomor").val();

        let post = {
          nomor: nomor.replace("_", "@")
        };
        $.ajax({
          type: "POST",
          url: "../api/clearchat.php",
          data: JSON.stringify(post), // serializes the form's elements.
          dataType: "json",
          contentType: "application/json; charset=utf-8",
          success: function(data) {
            //$(".common-message").toggle();
            $(".common-message").remove();
            updateScroll();
          }
        });
      });

      $('.synclatest').click(function(data) {
        let nomor = $(".nomor").val();

        let post = {
          nomor: nomor.replace("_", "@"),
          latestOnly: 1
        };
        $.ajax({
          type: "POST",
          url: "../api/getchatmsgs.php",
          data: JSON.stringify(post), // serializes the form's elements.
          dataType: "json",
          contentType: "application/json; charset=utf-8",
          success: function(data) {
            $(".common-message").remove();

            $.get("get_chat.php?nomor=" + nomor, function(data) {
              r = JSON.parse(data);
              jQuery.each(r, function(i, val) {
                let from_me = "";
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

                $(".pesan-container").append(chat);
                updateScroll();
              });
              localStorage.setItem('nomor', nomor);
            });
          }
        });
      });

      $(".send-button").click(function(e) {
        let nomor = $(".nomor").val();
        let pesan = $(".pesan").val();
        let url = "../api/sendnew.php";
        let post = {
          nomor: nomor.replace("_", "@"),
          msg: pesan,
          sender: "<?= $_SESSION['username']; ?>",
        };
        let tanggal = moment().format("HH:mm");

        $(".pesan-terakhir-" + nomor).html(pesan);
        $(".tanggal-terakhir-" + nomor).html(tanggal);
        $.ajax({
          type: "POST",
          url: url,
          // data: JSON.stringify(post), // serializes the form's elements.
          data: post, // serializes the form's elements.
          // dataType: "json",
          // contentType: "application/json; charset=utf-8",
          success: function(data) {
            let chat = "<li class='common-message is-you'><p class='common-message-content'>" + pesan + "<hr><b><i>(by <?= $_SESSION['username']; ?>)</b></i></p><time datetime>" + tanggal + "</time></li>";
            $(".pesan-container").append(chat);
            updateScroll();
            $(".pesan").val("");
          }
        });
      });
    });
  </script>

</body>

</html>