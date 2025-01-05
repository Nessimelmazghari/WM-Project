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
          <video controls>
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
      <ExploreContainer name="Tab 3 page" />
    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';

// Reactieve variabelen
const movie = ref<any>(null); // Filmgegevens
const sourceUrl = ref<string | null>(null); // URL van de filmbron

// Haal de ID op uit de URL
const route = useRoute();

onMounted(async () => {
  const movieId = route.params.id; // Film ID
  if (movieId) {
    try {
      // Haal filmgegevens op uit de eerste API
      const movieResponse = await fetch(
        `https://nessimelmazghari-odisee.be/nesflixer/Api/getMovie.php?id=${movieId}`
      );
      const movieData = await movieResponse.json();
      if (!movieData.error) {
        movie.value = movieData;
      } else {
        console.error(movieData.error);
      }

      // Haal de bronlocatie van de film op uit de tweede API
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
});
</script>

<style scoped>
/* Algemene stijl voor de pagina */
.movie-details {
  padding: 1rem;
  color: white;
  text-align: center; /* Uitlijnen voor consistentie */
}

.video-container {
  padding: 1rem;
  display: flex;
  justify-content: center;
  align-items: center;
}

video {
  width: 90%; /* Vul 90% van de schermbreedte */
  max-width: 800px; /* Limiteer tot 800px op grotere schermen */
  aspect-ratio: 16 / 9; /* Standaard 16:9 verhouding */
  height: auto; /* Zorg dat de hoogte automatisch wordt berekend */
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3); /* Schaduw voor een moderne look */
}

h2 {
  font-size: 1.5rem; /* Titelgrootte aangepast */
  margin-bottom: 0.5rem;
  color: #e50914; /* Kleur toegevoegd voor accent */
}

p {
  font-size: 1rem; /* Geschikt formaat voor mobiele apparaten */
  color: #ccc;
  line-height: 1.6; /* Verbeterde leesbaarheid */
}

ion-title {
  text-align: center;
  font-weight: bold;
  font-size: 1.5rem;
}

/* Responsiviteit voor kleinere schermen */
@media (max-width: 600px) {
  h2 {
    font-size: 1.2rem; /* Kleiner formaat voor titels op kleine schermen */
  }
  
  p {
    font-size: 0.9rem; /* Kleinere tekstgrootte voor beschrijvingen */
  }

  video {
    width: 100%; /* Vul de volledige breedte van de container */
  }
}
</style>
