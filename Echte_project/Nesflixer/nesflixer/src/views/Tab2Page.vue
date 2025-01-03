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
  padding: 1rem;
}

.media-container {
  position: relative;
  overflow: hidden;
  cursor: pointer;
}

.movie-image {
  display: block;
  border-radius: 5px;
  height: 300px;
}

.trailer-frame {
  position: absolute;
  top: 0;
  left: 0;
  border-radius: 5px;
  z-index: 2;
}

h2 {
  font-size: 1.5rem;
  color: white;
  padding: 1rem 0;
}

p {
  color: white;
  padding: 1rem 0 1rem;
}

ion-button {
  margin-top: 1rem;
  margin-bottom: 10rem;
}
</style>
