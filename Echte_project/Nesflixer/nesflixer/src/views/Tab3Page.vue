<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="dark">
        <ion-title>{{ movie?.title || 'Film Laden...' }}</ion-title>
      </ion-toolbar>
    </ion-header>
    <ion-content :fullscreen="true">
      <div v-if="movie">
        <div class="movie-details">
          <h2>{{ movie.title }}</h2>
          <p>{{ movie.description }}</p>
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

// Route handling
const route = useRoute();

// Lifecycle: Fetch data and set up functionality
onMounted(async () => {
  const movieId = route.params.id;
  if (typeof movieId === 'string') {
    await fetchMovieData(movieId);
    setupVideoSync();
    startPolling();  // Start polling for video state
    setupVoiceControl();  // Setup voice control functionality
  } else {
    console.error('Movie ID is not a string:', movieId);
  }
});

onBeforeUnmount(() => {
  removeVideoEventListeners();
  stopPolling();  // Stop polling when component unmounts
  stopVoiceControl();  // Stop voice control
});

// Fetch movie and source data
async function fetchMovieData(movieId: string) {
  try {
    const movieResponse = await fetch(
      `https://nessimelmazghari-odisee.be/nesflixer/Api/getMovie.php?id=${movieId}`
    );
    const movieData = await movieResponse.json();
    if (!movieData.error) {
      movie.value = movieData;
    } else {
      console.error(movieData.error);
    }

    const sourceResponse = await fetch(
      `https://nessimelmazghari-odisee.be/nesflixer/Api/getMovieSource.php?id=${movieId}`
    );
    const sourceData = await sourceResponse.json();
    if (!sourceData.error) {
      sourceUrl.value = sourceData.source_url;
    } else {
      console.error(sourceData.error);
    }
  } catch (error) {
    console.error('Fout bij het ophalen van gegevens:', error);
  }
}

// Set up video sync
function setupVideoSync() {
  const video = videoElement.value;
  if (video) {
    console.log('Video-element gevonden. Event listeners worden toegevoegd.');
    video.addEventListener('play', () => updateState('playing'));
    video.addEventListener('pause', () => updateState('paused'));
  } else {
    console.error('Video-element niet gevonden.');
  }
}

// Remove event listeners on unmount
function removeVideoEventListeners() {
  const video = videoElement.value;
  if (video) {
    console.log('Event listeners worden verwijderd.');
    video.removeEventListener('play', () => updateState('playing'));
    video.removeEventListener('pause', () => updateState('paused'));
  }
}

// Update state to server
function updateState(state: string) {
  console.log(`State wordt verzonden: ${state}`);
  fetch('https://nessimelmazghari-odisee.be/nesflixer/video_state.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `state=${state}`,
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP-fout! Status: ${response.status}`);
      }
      return response.json();
    })
    .then((data) => console.log('Server antwoord:', data))
    .catch((error) => console.error('Error bij POST-verzoek:', error));
}

// Polling voor video-status (GET)
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
  }, 1000);  // Poll every second
}

function stopPolling() {
  clearInterval(pollingInterval);
}

// Voice control functionality
let recognition: any;

function setupVoiceControl() {
  const SpeechRecognition = (window as any).SpeechRecognition || (window as any).webkitSpeechRecognition;
  if (!SpeechRecognition) {
    alert('Web Speech API wordt niet ondersteund in deze browser.');
    return;
  }

  recognition = new SpeechRecognition();
  recognition.lang = 'nl-NL';
  recognition.continuous = true;
  recognition.interimResults = false;

  recognition.start();

  recognition.onresult = (event: any) => {
    const command = event.results[event.results.length - 1][0].transcript.toLowerCase();
    const video = videoElement.value;
    if (!video) return;

    if (command.includes('afspelen')) {
      video.play();
    } else if (command.includes('pauze')) {
      video.pause();
    } else if (command.includes('terugspoelen')) {
      video.currentTime -= 10;
    } else if (command.includes('vooruitspoelen')) {
      video.currentTime += 10;
    }
  };

  recognition.onend = () => recognition.start();
  recognition.onerror = (event: any) =>
    console.error('Spraakherkenning fout:', event.error);
}

function stopVoiceControl() {
  if (recognition) {
    recognition.stop();
    console.log('Voice control gestopt.');
  }
}
</script>

<style scoped>
.movie-details {
  padding: 1rem;
  color: white;
  text-align: center;
}

.video-container {
  padding: 1rem;
  display: flex;
  justify-content: center;
  align-items: center;
}

video {
  width: 90%;
  max-width: 800px;
  aspect-ratio: 16 / 9;
  height: auto;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
}

h2 {
  font-size: 1.5rem;
  margin-bottom: 0.5rem;
  color: #e50914;
}

p {
  font-size: 1rem;
  color: #ccc;
  line-height: 1.6;
}

ion-title {
  text-align: center;
  font-weight: bold;
  font-size: 1.5rem;
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
