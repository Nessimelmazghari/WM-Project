<template>
  <ion-page>
    <ion-header>
      <ion-toolbar :color="isDarkMode ? 'dark' : 'light'">
        <ion-title class="logo">
          <img
            src="https://nessimelmazghari-odisee.be/nesflixer/nesflixer_logo.png"
            alt="Logo"
            id="logo"
          />
        </ion-title>
      </ion-toolbar>
    </ion-header>
    <ion-content :fullscreen="true">
      <!-- Movie Details and Video Player -->
      <div v-if="movie" :class="{ dark: isDarkMode }">
        <div class="movie-details">
          <h2>{{ movie.title }}</h2>
        </div>
        <div v-if="sourceUrl" class="video-container">
          <video ref="videoElement" controls>
            <source :src="sourceUrl" type="video/mp4" />
            Je browser ondersteunt deze video niet.
          </video>
        </div>
        <div v-else>
          <p>Geen video beschikbaar.</p>
        </div>
      </div>
      <div v-else>
        <p>Film niet gevonden...</p>
      </div>

      <!-- Chat Section -->
      <div v-if="chatMessages.length > 0" class="chat-container">
        <div class="chat-messages">
          <div v-for="message in chatMessages" :key="message.timestamp" class="chat-message">
            <strong>{{ message.username }}:</strong> {{ message.message }}
            <span class="timestamp">{{ new Date(message.timestamp).toLocaleString() }}</span>
          </div>
        </div>
        <div class="chat-input">
          <input v-model="newMessage" type="text" placeholder="Schrijf een bericht..." />
          <button @click="sendMessage">Verstuur</button>
        </div>
      </div>

      <div v-if="errorMessage" class="error-message">{{ errorMessage }}</div>

      <!-- Console log for errors related to sensors -->
      <div class="console-log">
        <p>{{ sensorStatus }}</p>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { useRoute } from 'vue-router';

// Data refs
const movie = ref<any>(null);
const sourceUrl = ref<string | null>(null);
const videoElement = ref<HTMLVideoElement | null>(null);
const isDarkMode = ref(false); // Track dark mode status
const errorMessage = ref<string | null>(null); // Error message
const sensorStatus = ref<string>(''); // Holds status or errors from sensors

// Chat data
const chatMessages = ref<any[]>([]);
const newMessage = ref<string>('');

// Route handling
const route = useRoute();
const chatId = ref<string>('123'); // Example chat ID, you can set dynamically if needed

// Lifecycle: Fetch data and set up functionality
onMounted(async () => {
  const movieId = route.params.id;
  if (typeof movieId === 'string') {
    await fetchMovieData(movieId);
    setupVideoSync();
    startPolling();
    fetchChatMessages();
  } else {
    console.error('Movie ID is not a string:', movieId);
  }
});

onBeforeUnmount(() => {
  removeVideoEventListeners();
  stopPolling();
});

// Fetch movie and source data
async function fetchMovieData(movieId: string) {
  try {
    const movieResponse = await fetch(
      `https://nessimelmazghari-odisee.be/nesflixer/Api/getMovie.php?id=${movieId}`
    );
    const movieData = await movieResponse.json();
    if (!movieData.error) {
      movie.value = movieData; // Fetch movie data (title and other info)
      errorMessage.value = null; // Reset error message if successful
    } else {
      errorMessage.value = movieData.error;
    }

    const sourceResponse = await fetch(
      `https://nessimelmazghari-odisee.be/nesflixer/Api/getMovieSource.php?id=${movieId}`
    );
    const sourceData = await sourceResponse.json();
    if (!sourceData.error) {
      sourceUrl.value = sourceData.source_url;
    } else {
      errorMessage.value = sourceData.error;
    }
  } catch (error) {
    errorMessage.value = 'Error fetching data';
    console.error('Fout bij het ophalen van gegevens:', error);
  }
}

// Chat functions
async function fetchChatMessages() {
  try {
    const response = await fetch(`https://nessimelmazghari-odisee.be/nesflixer/Api/load_chat.php?chat_id=${chatId.value}`);
    const messages = await response.json();
    if (Array.isArray(messages)) {
      chatMessages.value = messages;
    } else {
      console.error('Error loading chat messages:', messages);
    }
  } catch (error) {
    console.error('Fout bij het ophalen van chatberichten:', error);
  }
}

async function sendMessage() {
  if (newMessage.value.trim()) {
    const message = newMessage.value;
    try {
      const username = 'User'; // Replace with dynamic username if needed
      const response = await fetch('https://nessimelmazghari-odisee.be/nesflixer/Api/save_message.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ chat_id: chatId.value, message, username })
      });
      const result = await response.json();
      if (result.success) {
        chatMessages.value.push({ username, message, timestamp: new Date().toISOString() });
        newMessage.value = ''; // Clear input
      } else {
        console.error('Error sending message:', result);
      }
    } catch (error) {
      console.error('Fout bij het versturen van bericht:', error);
    }
  }
}

// Video sync and polling logic
function setupVideoSync() {
  const video = videoElement.value;
  if (video) {
    video.addEventListener('play', () => updateState('playing'));
    video.addEventListener('pause', () => updateState('paused'));
  }
}

function removeVideoEventListeners() {
  const video = videoElement.value;
  if (video) {
    video.removeEventListener('play', () => updateState('playing'));
    video.removeEventListener('pause', () => updateState('paused'));
  }
}

function updateState(state: string) {
  fetch('https://nessimelmazghari-odisee.be/nesflixer/video_state.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `state=${state}`,
  }).catch((error) => console.error('Error bij POST-verzoek:', error));
}

let pollingInterval: ReturnType<typeof setInterval>;

function startPolling() {
  pollingInterval = setInterval(async () => {
    try {
      const response = await fetch(
        'https://nessimelmazghari-odisee.be/nesflixer/video_state.php'
      );
      const data = await response.json();
      const video = videoElement.value;
      if (video && data.state) {
        if (data.state === 'playing' && video.paused) {
          video.play();
        } else if (data.state === 'paused' && !video.paused) {
          video.pause();
        }
      }
    } catch (error) {
      console.error('Error bij ophalen video-status:', error);
    }
  }, 1000);
}

function stopPolling() {
  clearInterval(pollingInterval);
}
</script>

<style scoped>
.movie-details {
  padding: 1rem;
  text-align: center;
}

.video-container {
  padding: 1rem;
  display: flex;
  justify-content: center;
  align-items: center;
  margin-bottom: 200px;
}

video {
  width: 90%;
  max-width: 800px;
  aspect-ratio: 16 / 9;
  height: auto;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
}

.dark {
  background-color: #000;
  color: #fff;
}

.light {
  background-color: #fff;
  color: #000;
}

h2 {
  font-size: 1.5rem;
  margin-bottom: 0.5rem;
  color: #e50914;
}

p {
  font-size: 1rem;
  line-height: 1.6;
}

.logo {
  font-size: 1.5rem;
  font-weight: bold;
  color: #e50914;
  text-align: center;
}

#logo {
  width: 80px;
}

.error-message {
  color: red;
  text-align: center;
  font-size: 1.2rem;
  margin-top: 1rem;
}

.console-log {
  background-color: #282828;
  color: #fff;
  padding: 10px;
  font-family: monospace;
  margin-top: 10px;
  border-radius: 8px;
}

.chat-container {
  padding: 1rem;
  background-color: #f4f4f4;
  border-top: 1px solid #ccc;
}

.chat-messages {
  max-height: 300px;
  overflow-y: auto;
  margin-bottom: 1rem;
}

.chat-message {
  margin-bottom: 0.5rem;
}

.timestamp {
  font-size: 0.8rem;
  color: gray;
}

.chat-input {
  display: flex;
  gap: 10px;
}

.chat-input input {
  flex: 1;
  padding: 0.5rem;
  font-size: 1rem;
  border: 1px solid #ccc;
  border-radius: 4px;
}

.chat-input button {
  padding: 0.5rem;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.chat-input button:hover {
  background-color: #0056b3;
}

@media (max-width: 600px) {
  h2 {
    font-size: 1.2rem;
  }

  p {
    font-size: 0.9rem;
  }

  video {
    width: 100%;
  }
}
</style>
