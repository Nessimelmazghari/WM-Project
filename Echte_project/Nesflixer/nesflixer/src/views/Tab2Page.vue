<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="dark">
        <ion-title class="logo">{{ movie?.title || 'Loading...' }}</ion-title>
      </ion-toolbar>
    </ion-header>
    <ion-content :fullscreen="true">
      <div v-if="movie" class="movie-container">
        <h2>{{ movie.title }}</h2>
        <div
          class="media-container"
          :style="{ width: 500 + 'px', height: 300 + 'px' }"
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
            :style="{ width: 500 + 'px', height: 300 + 'px' }"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen
          ></iframe>
        </div>
        <p>{{ movie.description }}</p>

        <!-- Navigatieknop -->
        <ion-button
          expand="block"
          color="primary"
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

// Stel de breedte en hoogte in op basis van de afbeelding
const setImageDimensions = () => {
  if (movieImage.value) {
    imageWidth.value = movieImage.value.naturalWidth;
    imageHeight.value = movieImage.value.naturalHeight;
  }
};

// Functie om een YouTube URL om te zetten naar een embedbare versie
const getEmbedUrl = (url: string): string => {
  const videoId = url.split('v=')[1]?.split('&')[0];
  return videoId
    ? `https://www.youtube.com/embed/${videoId}?autoplay=1&controls=1&modestbranding=1`
    : url;
};

// Navigatie naar tab3 met herladen
const goToTab3 = () => {
  const movieId = route.params.id;
  if (movieId) {
    router.push(`/tabs/tab3/${movieId}`).then(() => {
      window.location.reload(); // Herlaad de pagina na navigatie
    });
  }
};

// Toggle functie voor de trailer zichtbaar maken
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
.logo {
  font-family: 'Arial', sans-serif;
  font-size: 1.5rem;
  font-weight: bold;
  color: #e50914;
  text-align: center;
}

.movie-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 1rem;
  color: white;
  text-align: center;
}

.media-container {
  position: relative;
  width: 90%; /* Vul 90% van het schermbreedte */
  max-width: 600px; /* Limiteer tot 600px voor grotere schermen */
  aspect-ratio: 16 / 9; /* Zorg voor een 16:9 verhouding */
  overflow: hidden;
  cursor: pointer;
  border-radius: 10px;
  margin-bottom: 1rem;
  box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.3); /* Schaduw voor een moderne look */
}

.movie-image {
  width: 100%;
  height: 100%;
  object-fit: cover; /* Zorg voor een goede bijsnijding van de afbeelding */
  border-radius: 10px;
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
  font-size: 1.2rem; /* Geschikte grootte voor mobiele apparaten */
  margin-bottom: 1rem;
}

p {
  font-size: 1rem; /* Goede leesbaarheid op kleinere schermen */
  line-height: 1.5;
  padding: 0 1rem;
}

ion-button {
  margin-top: 1rem;
  width: 90%; /* Zorg dat de knop zich aanpast aan de breedte */
  max-width: 300px; /* Limiteer tot 300px op grotere schermen */
  align-self: center;
}
</style>
