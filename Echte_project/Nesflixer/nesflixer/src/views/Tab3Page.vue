<template>
  <ion-page>
    <ion-header>
  <ion-toolbar :color="isDarkMode ? 'dark' : 'light'">
    <div class="logo">
      <img
        src="https://nessimelmazghari-odisee.be/nesflixer/nesflixer_logo.png"
        alt="Logo"
        id="logo"
      />
    </div>
    <div v-if="profileImage" class="profile-photo">
      <img :src="profileImage" alt="Profiel Foto" />
      <p>{{ username }}</p>
    </div>
  </ion-toolbar>
</ion-header>



    <ion-content :fullscreen="true">
      <div v-if="!isProfileSet">
        <h2>Stel je profiel in</h2>
        <input type="text" v-model="username" placeholder="Gebruikersnaam" required />
        <button @click="capturePhoto">Maak Foto</button>
        <input type="file" @change="handleFileChange" />
        <button @click="saveProfile">Profiel Opslaan</button>
      </div>

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

      <div class="video-sync-toggle">
        <button @click="toggleSyncing">{{ isSyncing ? 'Stop Synchronisatie' : 'Start Synchronisatie' }}</button>
        <button @click="sendManualPost('playing')">Speel Video</button>
        <button @click="sendManualPost('paused')">Pauzeer Video</button>
      </div>

      <div class="chat-container">
        <div class="chat-messages" v-if="chatMessages.length > 0">
          <div v-for="(message, index) in chatMessages" :key="index" class="chat-message">
            <strong>{{ message.username }}:</strong> {{ message.message }}
            <span class="timestamp">{{ new Date(message.timestamp).toLocaleString() }}</span>
          </div>
        </div>
        <div v-else>
          <p>Geen berichten beschikbaar...</p>
        </div>

        <div class="chat-input">
          <input
            type="text"
            placeholder="Schrijf een bericht..."
            v-model="newMessage"
            @keyup.enter="sendMessage"
          />
          <button @click="sendMessage">Verstuur</button>
        </div>
      </div>

    </ion-content>
  </ion-page>
</template>


<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { useRoute } from 'vue-router';
import { Camera, CameraResultType, CameraSource } from '@capacitor/camera';

const username = ref('');
const profileImage = ref<string | null>(null);
const isProfileSet = ref<boolean>(false);

const movie = ref<any>(null);
const sourceUrl = ref<string | null>(null);
const videoElement = ref<HTMLVideoElement | null>(null);
const isDarkMode = ref(false);
const errorMessage = ref<string | null>(null);
const chatMessages = ref<any[]>([]);
const newMessage = ref<string>('');

const isSyncing = ref<boolean>(false);  // Track whether syncing is active
let pollingInterval: ReturnType<typeof setInterval>;

function saveProfile() {
  if (username.value) {
    localStorage.setItem('username', username.value);
    if (profileImage.value) {
      localStorage.setItem('profileImage', profileImage.value);
    }
    isProfileSet.value = true;
  }
}

function handleFileChange(event: Event) {
  const file = (event.target as HTMLInputElement)?.files?.[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = () => {
      profileImage.value = reader.result as string;
      // Save to local storage
      if (username.value) {
        localStorage.setItem('username', username.value);
        localStorage.setItem('profileImage', profileImage.value);
      }
    };
    reader.readAsDataURL(file);
  }
}


async function capturePhoto() {
  try {
    const image = await Camera.getPhoto({
      quality: 90,
      allowEditing: false,
      resultType: CameraResultType.Uri,
      source: CameraSource.Camera, // Capture photo from the camera
    });

    profileImage.value = image.webPath as string; // Store the image URI
  } catch (error) {
    console.error("Fout bij het maken van foto:", error);
  }
}

async function fetchChatMessages() {
  try {
    const response = await fetch(
      'https://nessimelmazghari-odisee.be/nesflixer/Api/load_chat.php?chat_id=123'
    );
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
  if (newMessage.value.trim() !== '') {
    const message = newMessage.value;
    const usernameStored = localStorage.getItem('username');
    const profileImageStored = localStorage.getItem('profileImage');

    if (usernameStored) {
      try {
        const response = await fetch(
          'https://nessimelmazghari-odisee.be/nesflixer/Api/save_message.php',
          {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
              chat_id: '123',
              message,
              username: usernameStored,
              profile_image: profileImageStored || '',
            }),
          }
        );

        if (!response.ok) {
          console.error(
            'POST-verzoek mislukt met status:',
            response.status,
            response.statusText
          );
          return;
        }

        const result = await response.json();

        if (result.success) {
          chatMessages.value.push({
            username: usernameStored,
            message,
            timestamp: new Date().toISOString(),
            profileImage: profileImageStored,
          });
          newMessage.value = '';
        } else {
          console.error('Fout in serverantwoord:', result);
        }
      } catch (error) {
        console.error('Netwerkfout bij het verzenden van bericht:', error);
      }
    }
  }
}

async function fetchMovieData(movieId: string) {
  try {
    const movieResponse = await fetch(
      `https://nessimelmazghari-odisee.be/nesflixer/Api/getMovie.php?id=${movieId}`
    );
    const movieData = await movieResponse.json();
    if (!movieData.error) {
      movie.value = movieData;
      errorMessage.value = null;
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

function setupVideoSync() {
  const video = videoElement.value;
  if (video) {
    video.addEventListener('play', () => updateState('playing'));
    video.addEventListener('pause', () => updateState('paused'));
  }
}

function updateState(state: string) {
  fetch('https://nessimelmazghari-odisee.be/nesflixer/video_state.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `state=${state}`,
  }).catch((error) => console.error('Error bij POST-verzoek:', error));
}

function sendManualPost(state: string) {
  updateState(state);
}

function toggleSyncing() {
  if (isSyncing.value) {
    stopSyncing();
  } else {
    startSyncing();
  }
  isSyncing.value = !isSyncing.value;  // Toggle sync state
}

function startSyncing() {
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

function stopSyncing() {
  clearInterval(pollingInterval);
  console.log("Synchronisatie gestopt.");
}

onMounted(() => {
  const storedUsername = localStorage.getItem('username');
  const storedProfileImage = localStorage.getItem('profileImage');
  if (storedUsername) {
    username.value = storedUsername;
    isProfileSet.value = true;
  }
  if (storedProfileImage) {
    profileImage.value = storedProfileImage;
  }

  const route = useRoute();
  const movieId = route.params.id as string;
  if (movieId) fetchMovieData(movieId);
  setupVideoSync();
  fetchChatMessages();
  
  // Fetch chat messages every 2 seconds
  setInterval(fetchChatMessages, 2000);
});

onBeforeUnmount(() => {
  clearInterval(pollingInterval);
});
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
  color: #000;
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
  color: #888;
  margin-left: 10px;
}

.chat-input {
  display: flex;
  gap: 10px;
  margin-bottom: 100px;
}

.chat-input input {
  width: 80%;
  padding: 0.5rem;
  border-radius: 4px;
  border: 1px solid #ccc;
  color: #fff;
}

.chat-input button {
  padding: 0.5rem;
  border: none;
  background-color: #e50914;
  color: #fff;
  cursor: pointer;
  border-radius: 4px;
}

.chat-input button:hover {
  background-color: #b20710;
}

ion-toolbar {
  display: flex;
  justify-content: center;
  align-items: center;
  position: relative;  /* To make sure we can position the profile photo absolutely */
}

.logo {
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 1.5rem;
  font-weight: bold;
  color: #e50914;
  text-align: center;
}

#logo {
  width: 80px;
}

.profile-photo {
  position: absolute;
  top: 10px;
  right: 10px;
  display: flex;
  flex-direction: column;  /* Stack the profile photo and name vertically */
  justify-content: center;
  align-items: center;
}

.profile-photo img {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  object-fit: cover;
}

.profile-photo p {
  margin: 2px 0 0;  /* Add some space between the photo and the username */
  font-size: 0.9rem;
  color: white;
}



.video-sync-toggle button {
  background-color: #e50914;
  padding: 10px;
  margin-top: 10px;
  color: white;
  border-radius: 5px;
  border: none;
  cursor: pointer;
}

.video-sync-toggle button:hover {
  background-color: #b20710;
}

.message-avatar {
  width: 30px;
  height: 30px;
  border-radius: 50%;
}
</style>

