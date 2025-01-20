<template>
  <ion-page>
    <ion-tabs @ionTabsWillChange="updateActiveTab">
      <ion-router-outlet></ion-router-outlet>
      <ion-tab-bar slot="bottom">
        <!-- Tab 1 (zonder ID) -->
        <ion-tab-button
          tab="tab1"
          @click="navigateToTab('/tabs/tab1', false)"
        >
          <ion-icon aria-hidden="true"  /> 
          <img src="@/assets/icons/home.png" alt="HoofdPagina" />
     
        </ion-tab-button>

        <!-- Tab 2 (met ID) -->
        <ion-tab-button
          tab="tab2"
          @click="navigateToTab('/tabs/tab2', true)"
        ><img src="@/assets/icons/info.png" alt="HoofdPagina" />
          <ion-icon aria-hidden="true" />

       
        </ion-tab-button>

        <!-- Tab 3 (met ID) -->
        <ion-tab-button
          tab="tab3"
          @click="navigateToTab('/tabs/tab3', true)"
        >
          <ion-icon aria-hidden="true" />
          <img src="@/assets/icons/play.png" alt="HoofdPagina" />
          
        </ion-tab-button>
      </ion-tab-bar>
    </ion-tabs>
  </ion-page>
</template>

<script setup lang="ts">
import {
  IonTabBar,
  IonTabButton,
  IonTabs,
  IonIcon,
  IonPage,
  IonRouterOutlet,
} from '@ionic/vue';
import { ref, watch, onMounted } from 'vue';
import { useRoute } from 'vue-router';

const currentTabId = ref(0); // Standaard ID is 1
const route = useRoute();

// Controleer de huidige tab ID en update de waarde
const updateActiveTab = () => {
  const idFromRoute = route.params.id;
  currentTabId.value = idFromRoute ? parseInt(idFromRoute as string, 10) : 1;
};

// Zorg ervoor dat de huidige ID wordt bijgewerkt bij routeverandering
watch(
  () => route.params.id,
  (newId) => {
    currentTabId.value = newId ? parseInt(newId as string, 10) : 1;
  }
);

// Navigeer naar een tab en herlaad de pagina
const navigateToTab = (path: string, withId: boolean) => {
  const fullPath = withId ? `${path}/${currentTabId.value || 1}` : path;
  window.location.href = fullPath; // Zorg voor herladen
};

// Stel de juiste ID in bij het initialiseren van de pagina
onMounted(() => {
  updateActiveTab();
});
</script>
<style scoped>
img {
  width: 30px; /* Adjust the width of the icons */
  height: auto; /* Adjust the height of the icons */
  margin-right: 8px; /* Optional: adds space between the icon and the label */
  margin-bottom: 10%;
}
</style>