@extends('admin::layouts.master')
@section('content')
<style>
     .chat-item.active {
        background-color: #FFFFFF;
    }
</style>
    <main>
        <section>
          <article>
            <div class="msg-board">
              <div class="container">
                <div class="chat-panel">
                  <div class="chat-user-list-panel">
                    <div class="add-group-btn justify-space-between">
                      <h4>Chat</h4>
                      <a href="#" class="open-grp"><i class="fa fa-plus" aria-hidden="true"></i></a>
                    </div>
                    <ul class="chat-list" id="chatContainer">
                      {{--<li>
                        <a href="#">
                          <ul class="chat-detail">
                            <li>
                              <div class="chat-profile-user" style="background: url('https://www.yudiz.com/codepen/chat-dashboard/user-profile.png') no-repeat center center / cover;">
                              </div>
                            </li>
                            <li>
                              <div class="chat-user-nm">Roxy Riana</div>
                              <div class="chat-desc">Combination of neutral colors</div>
                            </li>
                            <li>
                              <div class="chat-seen">15 Min Ago</div>
                              <div class="not-read-lable">10</div>
                            </li>
                          </ul>
                        </a>
                      </li> --}}
                    </ul>
                  </div>
                  <!------ user chat main panel ------>
                  <div class="chat-user-message-panel">
                    <div class="user-select-panel">
                      <a href="#">
                        <ul class="chat-detail">
                          <li>
                            <div class="chat-profile-user" style="background: url('https://www.yudiz.com/codepen/chat-dashboard/user-profile.png') no-repeat center center / cover;">
                            </div>
                          </li>
                          <li>
                            <div class="chat-user-nm" id="chatPanelHeaderName">Art of Design</div>
                            <div class="chat-desc grp-mem"><span>4</span>Members</div>
                          </li>
                        </ul>
                      </a>
                    </div>
                    <div class="msg-conversation">
                      <!--load dynamic chats here-->
                    </div>
                    <div class="write-comment align-item-center">
                      <a class="box">
                        <input type="file" name="profile_image" id="file-1" class="inputfile inputfile-1" data-multiple-caption="{count} files selected" multiple="">
                        <label for="file-1"><i class="fa fa-picture-o" aria-hidden="true"></i></label>
                      </a>
                      <div class="form-group">
                        <input type="text" class="form-control" placeholder="You cannot send message from here....." />
                        <button class="submit-comment"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i></button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </article>
        </section>
    </main>

@endsection
@push('script')
<!-- FIREBASE -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-firestore.js"></script>
<script>
  const firebaseConfig = {
    apiKey: "AIzaSyB2MMOSFP0KQCiwnlbFRgi_EQjc-FyepYA",
    authDomain: "stayezyapp-91fad.firebaseapp.com",
    projectId: "stayezyapp-91fad",
    storageBucket: "stayezyapp-91fad.appspot.com",
    messagingSenderId: "834151349853",
    appId: "1:834151349853:web:290e8a9e7ff9d13b7457b4",
  };
  
   // Initialize Firebase
    firebase.initializeApp(firebaseConfig);
    //   console.log("Firebase initialized:", firebase.app());
    const db = firebase.firestore();

    function timeAgo(timeMillis) {
          const now = new Date().getTime();
          const diffInSeconds = Math.floor((now - timeMillis) / 1000);
          
          if (diffInSeconds < 60) {
              return diffInSeconds + " sec ago";
          } else if (diffInSeconds < 3600) {
              const minutes = Math.floor(diffInSeconds / 60);
              return minutes + " min ago";
          } else if (diffInSeconds < 86400) {
              const hours = Math.floor(diffInSeconds / 3600);
              return hours + " hour" + (hours > 1 ? "s" : "") + " ago";
          } else if (diffInSeconds < 2592000) { // less than 30 days
              const days = Math.floor(diffInSeconds / 86400);
              return days + " day" + (days > 1 ? "s" : "") + " ago";
          } else if (diffInSeconds < 31536000) { // less than one year
              const months = Math.floor(diffInSeconds / 2592000);
              return months + " month" + (months > 1 ? "s" : "") + " ago";
          } else {
              const years = Math.floor(diffInSeconds / 31536000);
              return years + " year" + (years > 1 ? "s" : "") + " ago";
          }
      }

    // get user name
    function getChatDisplayName(chatData) {
      if (chatData.members && Array.isArray(chatData.members) && 
      chatData.member_details_list && Array.isArray(chatData.member_details_list)) 
      {
        const names = chatData.members.map(memberId => {
          // Locate the corresponding member details by matching member id.
          const detail = chatData.member_details_list.find(item => item.id == memberId);
          if (detail && detail.name) {
            // Return the full name in lowercase.
            return detail.name.toLowerCase();
          }
          return '';
        });
        return names.join("-");
      }
      return "unknown-chat";
    }


    // Function to load chat conversations from Firestore
    function loadChats() {
      // Query the 'chats' collection ordering by 'last_message_time'
      const chatsRef = db.collection("chats").orderBy("last_message_time");
      console.log("Querying chats from:", chatsRef);
    
      chatsRef.onSnapshot(function(snapshot) {
        let chatsHtml = "";
        if (snapshot.empty) {
          console.log("No chats found in Firestore.");
          chatsHtml = "<p>No chats found.</p>";
        } else {
          snapshot.forEach(function(doc) {
            const chatData = doc.data();
            const chatId = doc.id; // Get the dynamic chat ID
            console.log("Chat received:", chatData);
    
            // Get the formatted display name from chatData
            const chatDisplayName = getChatDisplayName(chatData);
    
            // Format the last_message_time using timeAgo() if present
            let time = "";
            if (chatData.last_message_time) {
              const timeMillis = parseInt(chatData.last_message_time, 10);
              if (!isNaN(timeMillis)) {
                time = timeAgo(timeMillis);
                console.log("My time format:", time);
              }
            }
    
            // Build the list item with the dynamic chat ID and full chat data as JSON.
            chatsHtml += `
              <li class="chat-item" data-chat-id="${chatId}" data-chat='${JSON.stringify(chatData)}'>
                <a href="#">
                  <ul class="chat-detail">
                    <li>
                      <div class="chat-profile-user" style="background: url('https://www.yudiz.com/codepen/chat-dashboard/user-profile.png') no-repeat center center / cover;">
                      </div>
                    </li>
                    <li>
                      <div class="chat-user-nm">${ chatDisplayName }</div>
                      <div class="chat-desc">${ chatData.last_message || "" }</div>
                    </li>
                    <li>
                      <div class="chat-seen">${ time }</div>
                      <div class="not-read-lable">10</div>
                    </li>
                  </ul>
                </a>
              </li>
            `;
          });
        }
        // Update the sidebar container with the compiled HTML
        $("#chatContainer").html(chatsHtml);
      }, function(error) {
        console.error("Error loading chats: ", error);
      });
    }



    // Load messages when the document is ready.
    $(document).ready(function(){
      // Load sidebar chats on page load.
      loadChats();
    
      // Delegate click event to dynamically added chat items.
      $(document).on("click", ".chat-item", function() {
        const chatId = $(this).attr("data-chat-id");
        // Parse the full chat data from the data-chat attribute.
        const chatData = JSON.parse($(this).attr("data-chat"));
    
        // Update the chat panel header with the dynamic user names using the helper.
        const chatDisplayName = getChatDisplayName(chatData);
        // Update only the main chat panel header element.
        $("#chatPanelHeaderName").text(chatDisplayName);
    
        console.log("Loading chat:", chatId, chatData);
    
        // Load messages for the clicked chat conversation.
        loadMessagesForChat(chatId, chatData);
    
        // Optional: Highlight the active chat.
        $(".chat-item").removeClass("active");
        $(this).addClass("active");
      });
    });

    /* ----- working code of full chatting --------*/
    // function loadMessagesForChat(chatId, chatData) {
    //   // Reference the 'messages' subcollection for the selected chat
    //   const messagesRef = db.collection("chats").doc(chatId)
    //                         .collection("messages").orderBy("sent");
    
    //   messagesRef.onSnapshot(function(snapshot) {
    //     let messagesHtml = "";
    //     // We assume the first member in chatData.members is the primary sender
    //     const primaryId = chatData.members && chatData.members[0] ? chatData.members[0] : null;
        
    //     snapshot.forEach(function(doc) {
    //       const message = doc.data();
    //       // Format the timestamp using our helper
    //       const formattedTime = message.sent ? timeAgo(parseInt(message.sent, 10)) : "";
    
    //       // Use different HTML templates based on who sent the message
    //       if (message.sender_id === primaryId) {
    //         messagesHtml += `
    //           <div class="sender">
    //             <div class="sender-chat">
    //               <div class="sender-msg">${ message.msg || "" }</div>
    //               <div class="send-time">${ formattedTime }</div>
    //             </div>
    //           </div>
    //         `;
    //       } else {
    //         messagesHtml += `
    //           <div class="receiver">
    //             <div class="receiver-chat">
    //               <div class="receiver-msg">${ message.msg || "" }</div>
    //               <div class="send-time">${ formattedTime }</div>
    //             </div>
    //             <div class="messenger-img">
    //               <div class="msg-img" style="background: url('${ message.image ? message.image : "https://www.yudiz.com/codepen/chat-dashboard/chat-msg-img.jpg" }') no-repeat center center / cover;">
    //               </div>
    //             </div>
    //           </div>
    //         `;
    //       }
    //     });
    
    //     // Update the message conversation container
    //     $(".msg-conversation").html(messagesHtml);
    //     // Optional: Scroll to the bottom after loading new messages
    //     $(".msg-conversation").scrollTop($(".msg-conversation")[0].scrollHeight);
    //   }, function(error) {
    //     console.error("Error loading conversation messages: ", error);
    //   });
    // };
    
    
    function loadMessagesForChat(chatId, chatData) {
      const messagesRef = db.collection("chats").doc(chatId)
                            .collection("messages").orderBy("sent");
    
      messagesRef.onSnapshot(function(snapshot) {
        let messagesHtml = "";
        const primaryId = chatData.members && chatData.members[0] ? chatData.members[0] : null;
        
        snapshot.forEach(function(doc) {
          const message = doc.data();
          const formattedTime = message.sent ? timeAgo(parseInt(message.sent, 10)) : "";
    
          if (message.sender_id === primaryId) {
            messagesHtml += `
              <div class="sender">
                <div class="sender-chat">
                  <div class="sender-msg">${ message.msg || "" }</div>
                  <div class="send-time">${ formattedTime }</div>
                </div>
              </div>
            `;
          } else {
            messagesHtml += `
              <div class="receiver">
                <div class="receiver-chat">
                  <div class="receiver-msg">${ message.msg || "" }</div>
                  <div class="send-time">${ formattedTime }</div>
                </div>
                <div class="messenger-img">
                  <div class="msg-img" style="background: url('${ message.image ? message.image : "https://www.yudiz.com/codepen/chat-dashboard/chat-msg-img.jpg" }') no-repeat center center / cover;">
                  </div>
                </div>
              </div>
            `;
          }
        });
    
        $(".msg-conversation").html(messagesHtml);
        $(".msg-conversation").scrollTop($(".msg-conversation")[0].scrollHeight);
      }, function(error) {
        console.error("Error loading conversation messages: ", error);
      });
    }
</script>
<script>
    $(".open-grp").click(function () {
      $(".msg-board").addClass("show-grp");
    });
    $(".user-select-panel a").click(function () {
      $(".msg-board").addClass("show-grp-2");
      $(".msg-board").removeClass("show-grp");
    });
    $(".close-grp-window .close-grp").click(function () {
      $(".msg-board").removeClass("show-grp");
      $(".msg-board").removeClass("show-grp-2");
    });
    $(".follow-btn").click(function () {
      $(".new-group-request").hide();
    });
</script>
@endpush




