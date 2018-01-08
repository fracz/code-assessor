import Vue from 'vue'
import Router from 'vue-router'
import Home from '@/components/Home'
import Comparator from '@/components/Comparator'
import Questionnaire from '@/components/Questionnaire'

Vue.use(Router);

export default new Router({
  routes: [
    {
      path: '/',
      name: 'Home',
      component: Home
    },
    {
      path: '/say-hello',
      name: 'Questionnaire',
      component: Questionnaire
    },
    {
      path: '/play',
      name: 'Comparator',
      component: Comparator
    }
  ]
})
