<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="dark">
        <ion-title class="logo">Nesflixer</ion-title>
      </ion-toolbar>
    </ion-header>
    <ion-content :fullscreen="true">
      <swiper :slides-per-view="1" class="banner-swiper">
        <swiper-slide>
          <img src="https://nessimelmazghari-odisee.be/nesflixer/thumbnails/Moana.jpg" alt="Featured" class="banner" />
        </swiper-slide>
      </swiper>
      <ion-list>
        <h2>Populaire titels</h2>
        <swiper :slides-per-view="3" space-between="10">
          <swiper-slide v-for="(movie, index) in movies" :key="index">
            <img :src="movie.image" :alt="movie.title" class="thumbnail" />
          </swiper-slide>
        </swiper>
      </ion-list>
    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { IonPage, IonHeader, IonToolbar, IonTitle, IonContent, IonList } from '@ionic/vue';
import { Swiper, SwiperSlide } from 'swiper/vue';
import 'swiper/css';

const movies = [
  { title: 'Film 1', image: 'https://nessimelmazghari-odisee.be/nesflixer/thumbnails/Deadpool&Wolverine.jpg' },
  { title: 'Film 2', image: 'https://nessimelmazghari-odisee.be/nesflixer/thumbnails/Mufasa.jpg' },
  { title: 'Film 3', image: 'https://nessimelmazghari-odisee.be/nesflixer/thumbnails/Sonic3.jpg' },
  { title: 'Film 1', image: 'https://nessimelmazghari-odisee.be/nesflixer/thumbnails/Wicked.jpg' },
    { title: 'Film 2', image: 'https://via.placeholder.com/200x300' },
    { title: 'Film 3', image: 'https://via.placeholder.com/200x300' },
];
</script>

<style scoped>
.logo {
  font-family: 'Arial', sans-serif;
  font-size: 1.5rem;
  font-weight: bold;
  color: #e50914;
  text-align: center;
}

.banner {
  width: 100%;
  height: auto;
  display: block;
}

.thumbnail {
  width: 100%;
  height: auto;
  border-radius: 5px;
}

h2 {
  font-size: 1.5rem;
  color: white;
  padding: 0 1rem;
}
</style>
