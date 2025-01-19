<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="dark">
        <ion-title class="logo"><img src="https://nessimelmazghari-odisee.be/nesflixer/nesflixer_logo.png" alt="Logo" id="logo"></ion-title>
      </ion-toolbar>
    </ion-header>
    <ion-content :fullscreen="true">
      <!-- Swiper Banner from the first code -->
      <swiper :slides-per-view="1" class="banner-swiper">
        <swiper-slide>
          <img src="https://nessimelmazghari-odisee.be/nesflixer/thumbnails/Moana.jpg" alt="Featured" class="banner" />
        </swiper-slide>
      </swiper>
      
      <ion-list>
        <h2>Populaire titels</h2>
        <swiper :slides-per-view="3" space-between="10">
          <swiper-slide v-for="(movie, index) in movies" :key="index">
            <!-- Klikken op de afbeelding geeft de juiste movieId door -->
            <img :src="movie.image" :alt="movie.title" class="thumbnail" @click="navigateToTab2(movie.id)" />
          </swiper-slide>
        </swiper>
      </ion-list>
      <ExploreContainer name="Tab 1 page" />
    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { IonPage, IonHeader, IonToolbar, IonTitle, IonContent, IonList } from '@ionic/vue';
import { Swiper, SwiperSlide } from 'swiper/vue';
import { useRouter } from 'vue-router'; // Router importeren
import 'swiper/css';

const router = useRouter(); // Router instance verkrijgen

const navigateToTab2 = (movieId: number) => {
  // Navigeren naar Tab 2 met de juiste movieId
  router.push(`/tabs/tab2/${movieId}`);
};

// Voeg de 'id' toe aan je 'movies' array om de juiste movieId door te geven
const movies = [
  { id: 1, title: 'Film 1', image: 'https://nessimelmazghari-odisee.be/nesflixer/thumbnails/Deadpool&Wolverine.jpg' },
  { id: 2, title: 'Film 2', image: 'https://nessimelmazghari-odisee.be/nesflixer/thumbnails/Mufasa.jpg' },
  { id: 3, title: 'Film 3', image: 'https://nessimelmazghari-odisee.be/nesflixer/thumbnails/Kraven_The_Hunter.jpg' },
  { id: 4, title: 'Film 4', image: 'https://nessimelmazghari-odisee.be/nesflixer/thumbnails/Wicked.jpg' },
  { id: 5, title: 'Film 5', image: 'https://via.placeholder.com/200x300' },
  { id: 6, title: 'Film 6', image: 'https://via.placeholder.com/200x300' },
];
</script>

<style scoped>
.logo {
  font-family: 'Arial', sans-serif;
  font-size: 1.5rem;
  font-weight: bold;
  color: #e50914;
  text-align: center;
  background-color: black;
}

.banner {
  width: 100%;
  height: auto;
  display: block;
}

.thumbnail {
  width: 100%;
  aspect-ratio: 2 / 3;
  border-radius: 10px;
  object-fit: cover;
  box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.3);
  margin: 0.5rem;
}

h2 {
  font-size: 1.2rem;
  color: white;
  padding: 0 1rem;
}

.ion-content {
  padding: 0;
}

#logo {
  width: 80px;
}
</style>
