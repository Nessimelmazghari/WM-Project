import { createRouter, createWebHistory } from '@ionic/vue-router';
import { RouteRecordRaw } from 'vue-router';
import TabsPage from '@/views/TabsPage.vue';
import Tab1Page from '@/views/Tab1Page.vue';
import Tab2Page from '@/views/Tab2Page.vue';
import Tab3Page from '@/views/Tab3Page.vue';

const routes: Array<RouteRecordRaw> = [
  { path: '/', redirect: '/tabs/tab1' },
  { path: '/tab1', component: Tab1Page, name: 'Tab1Page' },
  {
    path: '/tabs/',
    component: TabsPage,
    children: [
      {
        path: '',
        redirect: '/tabs/tab1'
      },
      {
        path: 'tab1',
        name: 'Tab1Page',
        component: Tab1Page,
      },
      {
        path: 'tab2',
        name: 'Tab2Page',
        component: Tab2Page,
      },
      {
        path: 'tab3',
        name: 'Tab3Page',
        component: Tab3Page,
      }
    ]
  }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
});

export default router;
