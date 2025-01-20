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
      <!-- Swiper Banner -->
      <swiper :slides-per-view="1" class="banner-swiper">
        <swiper-slide @click="navigateToTab2(0)">
          <img src="https://nessimelmazghari-odisee.be/nesflixer/thumbnails/Moana.jpg" alt="Featured" class="banner" />
        </swiper-slide>
      </swiper>
      
      <ion-list>
        <h2>Populaire titels</h2>
        <swiper :slides-per-view="3" space-between="10">
          <swiper-slide v-for="(movie, index) in movies" :key="index">
            <img :src="movie.image" :alt="movie.title" class="thumbnail" @click="navigateToTab2(movie.id)" />
          </swiper-slide>
        </swiper>
      </ion-list>
      <ExploreContainer name="Tab 1 page" />
    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { ref } from 'vue';  // <-- Import 'ref' from Vue
import { IonPage, IonHeader, IonToolbar, IonContent, IonList } from '@ionic/vue';
import { Swiper, SwiperSlide } from 'swiper/vue';
import { useRouter } from 'vue-router'; // Router importeren
import 'swiper/css';

const router = useRouter(); // Router instance verkrijgen

const navigateToTab2 = (movieId: number) => {
  router.push(`/tabs/tab2/${movieId}`);
};

// Movies array with movie ID
const movies = [
  { id: 1, title: 'Film 1', image: 'https://nessimelmazghari-odisee.be/nesflixer/thumbnails/Deadpool&Wolverine.jpg' },
  { id: 2, title: 'Film 2', image: 'https://nessimelmazghari-odisee.be/nesflixer/thumbnails/Mufasa.jpg' },
  { id: 3, title: 'Film 3', image: 'https://nessimelmazghari-odisee.be/nesflixer/thumbnails/Kraven_The_Hunter.jpg' },
  { id: 4, title: 'Film 4', image: 'https://nessimelmazghari-odisee.be/nesflixer/thumbnails/Wicked.jpg' },
];

const username = ref(localStorage.getItem('username') || '');
const profileImage = ref(localStorage.getItem('profileImage') || '');
const isDarkMode = ref(false);
</script>


<style scoped>
.logo {
  font-family: 'Arial', sans-serif;
  font-size: 1.5rem;
  font-weight: bold;
  color: #e50914;
  text-align: center;
}

#logo {
  width: 80px;
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
  color: #e50914;
  padding: 0 1rem;
}

.ion-content {
  padding: 0;
}

/* Profile photo in toolbar */
.profile-photo {
  position: absolute;
  top: 10px;
  right: 10px;
  display: flex;
  flex-direction: column;
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
  margin: 2px 0 0;
  font-size: 0.9rem;
  color: #e50914;
}
</style>
