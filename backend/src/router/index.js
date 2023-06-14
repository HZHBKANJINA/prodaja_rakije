import { createRouter, createWebHistory } from 'vue-router'
import AppLayout from  '../components/AppLayout.vue'
import Dashboard from "@/views/Dashboard.vue";
import Login from "@/views/Login.vue";
import RequestPassword from "../views/RequestPassword.vue"
import ResetPassword from "../views/ResetPassword.vue"
import Products from "@/views/Products/Products.vue";
import store from "@/store";
import NotFound from "@/views/NotFound.vue";


const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
      {
          path:'/app',
          name:'app',
          component:AppLayout,
          meta:{
              requiresAuth: true
          },
          children:[
              {
                  path: 'dashboard',
                  name: 'app.dashboard',
                  component: Dashboard
              },
              {
                  path: 'products',
                  name: 'app.products',
                  component: Products
              }

          ]
      },
      {
          path: '/login',
          name: 'login',
          meta:{
              requiresGuest: true
          },
          component: Login
      },
      {
          path: '/request-password/:token',
          name: 'requestPassword',
          meta:{
              requiresGuest: true
          },
          component: RequestPassword
      },
      {
          path: '/reset-password',
          name: 'resetPassword',
          meta:{
              requiresGuest: true
          },
          component: ResetPassword
      },
      {
          path: '/:pathMatch(.*)',
          name: 'notfound',
          component: NotFound
      },

  ]
})

router.beforeEach((to,from,next)=>{
    if(to.meta.requiresAuth && !store.state.user.token){
        next({name:'login'})
    }else if(to.meta.requiresGuest && store.state.user.token) {
        next({name:'app.dashboard'})
    } else{
        next();
    }
})

export default router
