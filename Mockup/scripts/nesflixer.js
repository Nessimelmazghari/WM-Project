document.addEventListener('DOMContentLoaded', function () {
  const inviteLinkBtn = document.getElementById('inviteLinkBtn');
  const liveCallCheckbox = document.getElementById('liveCallCheckbox');
  const liveCallSection = document.getElementById('liveCallSection');
  const cameraStream = document.getElementById('cameraStream');
  const chatInput = document.getElementById('chatInput');
  const sendMessageBtn = document.getElementById('sendMessageBtn');
  const messagesContainer = document.getElementById('messages');
  const body = document.body;

  const videoPlayer = document.getElementById('videoPlayer');
  const playPauseBtn = document.getElementById('playPauseBtn');
  const rewindBtn = document.getElementById('rewindBtn');
  const fastForwardBtn = document.getElementById('fastForwardBtn');
  const volumeControl = document.getElementById('volumeControl');
  const progressControl = document.getElementById('progressControl');
  const currentTimeDisplay = document.getElementById('currentTime');
  const durationDisplay = document.getElementById('duration');

  // Handle Invite Link button visibility based on live call checkbox
  inviteLinkBtn.style.display = liveCallCheckbox.checked ? 'inline-block' : 'none';
  liveCallCheckbox.addEventListener('change', function () {
      liveCallSection.style.display = this.checked ? 'flex' : 'none';
      if (this.checked) {
          startCamera(); // Start the camera when live call is enabled
      }
  });

  // Function to start the camera
  function startCamera() {
      navigator.mediaDevices.getUserMedia({ video: true })
          .then(function (stream) {
              cameraStream.srcObject = stream;
          })
          .catch(function (error) {
              console.error("Error accessing camera: ", error);
          });
  }

  // Send message functionality
  sendMessageBtn.addEventListener('click', function () {
      const message = chatInput.value.trim();
      if (message) {
          const messageElem = document.createElement('div');
          messageElem.textContent = message;
          messagesContainer.appendChild(messageElem);
          chatInput.value = '';
          messagesContainer.scrollTop = messagesContainer.scrollHeight; // Scroll to the latest message
      }
  });

  // Video controls
  playPauseBtn.addEventListener('click', function () {
      if (videoPlayer.paused) {
          videoPlayer.play();
          playPauseBtn.textContent = "Pause";
      } else {
          videoPlayer.pause();
          playPauseBtn.textContent = "Play";
      }
  });

  rewindBtn.addEventListener('click', function () {
      videoPlayer.currentTime -= 10;
  });

  fastForwardBtn.addEventListener('click', function () {
      videoPlayer.currentTime += 10;
  });

  volumeControl.addEventListener('input', function () {
      videoPlayer.volume = volumeControl.value;
  });

  videoPlayer.addEventListener('timeupdate', function () {
      // Update the progress bar and time display
      progressControl.value = (videoPlayer.currentTime / videoPlayer.duration) * 100 || 0;
      currentTimeDisplay.textContent = formatTime(videoPlayer.currentTime);
      durationDisplay.textContent = formatTime(videoPlayer.duration);
  });

  progressControl.addEventListener('input', function () {
      const seekTime = (progressControl.value / 100) * videoPlayer.duration;
      videoPlayer.currentTime = seekTime;
  });

  function formatTime(seconds) {
      const minutes = Math.floor(seconds / 60);
      const secs = Math.floor(seconds % 60);
      return `${minutes}:${secs.toString().padStart(2, '0')}`;
  }

  // Handle fullscreen toggle
  document.addEventListener('fullscreenchange', handleFullscreenChange);
  document.addEventListener('webkitfullscreenchange', handleFullscreenChange);
  document.addEventListener('mozfullscreenchange', handleFullscreenChange);
  document.addEventListener('MSFullscreenChange', handleFullscreenChange);

  function handleFullscreenChange() {
      if (document.fullscreenElement ||
          document.webkitFullscreenElement ||
          document.mozFullScreenElement ||
          document.msFullscreenElement) {
          // Video is in fullscreen mode
          videoEmbed.classList.add('fullscreen');
          body.classList.add('fullscreen');
      } else {
          // Exit fullscreen
          videoEmbed.classList.remove('fullscreen');
          body.classList.remove('fullscreen');
      }
  }
});
