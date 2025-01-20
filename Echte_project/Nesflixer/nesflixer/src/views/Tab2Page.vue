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
    
    <ion-content :fullscreen="true" class="scrollable-content">
      <div v-if="movie" class="movie-container">
        <h2>{{ movie.title }}</h2>
        <div
          class="media-container"
          @click="toggleTrailer"
        >
          <!-- Foto -->
          <img
            ref="movieImage"
            :src="movie.image"
            :alt="movie.title"
            class="movie-image"
            @load="setImageDimensions"
            v-if="!showTrailer"
          />
          <!-- Trailer -->
          <iframe
            v-show="showTrailer"
            :src="getEmbedUrl(movie.trailer)"
            class="trailer-frame"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen
          ></iframe>
        </div>
        <p>{{ movie.description }}</p>

        <!-- Navigatieknop -->
        <ion-button
          expand="block"
          color="white"
          @click="goToTab3"
        >
          Kijk Film
        </ion-button>
      </div>
      <div v-else>
        <p>Film niet gevonden...</p>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const movie = ref<any>(null);
const route = useRoute();
const router = useRouter();
const showTrailer = ref(false);
const imageWidth = ref<number | null>(null);
const imageHeight = ref<number | null>(null);
const movieImage = ref<HTMLImageElement | null>(null);
const isDarkMode = ref(false); // Track dark mode status
const username = ref(localStorage.getItem('username') || '');
const profileImage = ref(localStorage.getItem('profileImage') || '');

const setImageDimensions = () => {
  if (movieImage.value) {
    imageWidth.value = movieImage.value.naturalWidth;
    imageHeight.value = movieImage.value.naturalHeight;
  }
};

const getEmbedUrl = (url: string): string => {
  const videoId = url.split('v=')[1]?.split('&')[0];
  return videoId
    ? `https://www.youtube.com/embed/${videoId}?autoplay=1&controls=1&modestbranding=1`
    : url;
};

const goToTab3 = () => {
  const movieId = route.params.id;
  if (movieId) {
    router.push(`/tabs/tab3/${movieId}`).then(() => {
      window.location.reload();
    });
  }
};

const toggleTrailer = () => {
  showTrailer.value = !showTrailer.value;
};

onMounted(async () => {
  const movieId = route.params.id;
  if (movieId) {
    try {
      const response = await fetch(`https://nessimelmazghari-odisee.be/nesflixer/Api/getMovie.php?id=${movieId}`);
      const data = await response.json();
      if (data.error) {
        console.error(data.error);
      } else {
        movie.value = data;
      }
    } catch (error) {
      console.error('Fout bij het ophalen van de film:', error);
    }
  }
});
</script>

<style scoped>
.scrollable-content {
  overflow-y: auto;
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

.movie-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 1rem;
  color: #e50914;
  text-align: center;
  min-height: 120vh; /* Ensure container takes at least full viewport height */
  max-width: 90%;
}

.media-container {
  position: relative;
  width: 100%;
  max-width: 600px;
  aspect-ratio: 16 / 9;
  overflow: hidden;
  cursor: pointer;
  border-radius: 10px;
  margin-bottom: 1rem;
  box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.3);
}

.movie-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 10px;
  max-width: 100%;
}

.trailer-frame {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  border-radius: 10px;
  z-index: 2;
}

h2 {
  font-size: 3rem;
  margin-bottom: 1rem;
  color: #e50914;
  font-weight: bolder;
}

p {
  font-size: 1rem;
  line-height: 1.5;
  padding: 0 1rem;
}

ion-button {
  margin-top: 1rem;
  width: 90%;
  max-width: 300px;
  align-self: center;
  background-color: #e50914;
  border-radius: 5px;
}

ion-button p{
color: white;
}

/* Profile photo in toolbar */
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
  color: #e50914;
}
</style>
