import { createRouter, createWebHistory } from 'vue-router';
import HomeView from '../views/HomeView.vue';
import AuthView from '../views/AuthView.vue';
import Register from '../views/RegisterView.vue';
import LoginView from '../views/LoginView.vue';
import StationsView from '../views/stations/StationsView.vue';
import StationView from '../views/stations/StationView.vue';
import userEdit from '../views/user/editProfil.vue';
import Users from '../views/user/users.vue';
import UserView from '../views/user/user.vue';
import ReportView from '../views/reports/ReportView.vue';
import ReportsView from '../views/reports/ReportsView.vue';
import VacationsView from '../views/Vacations/VacationsView.vue';
import restallowance from '../views/Vacations/Restallowance.vue';
import typePreparationView from '../views/preparation/preparationView.vue';
import NotFound from '../views/NotFound.vue';
import appView from '../views/appView.vue';

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomeView,
    meta: { title: 'Home' },
  },
  {
    path: '/stations',
    name: 'stations',
    component: StationsView,
    meta: { title: 'Stations', titleAr: 'المحطات', show: false },
  },
  {
    path: '/station/:id',
    name: 'station',
    component: StationView,
    meta: { title: 'Station' },
  },
  {
    path: '/user/edit',
    name: 'userEdit',
    component: userEdit,
    meta: { title: 'User Edit' },
  },
  {
    //reports
    path: '/reports',
    name: 'reports',
    component: ReportsView,
    meta: { title: 'Reports', titleAr: 'التقارير', show: true },
  },
  {
    //rest
    path: '/rest',
    name: 'rest',
    component: restallowance,
    meta: { title: 'Rest allowance', titleAr: 'بدل راحة', show: true },
  },

  {
    ///report/:id
    path: '/report/:id',
    name: 'report',
    component: ReportView,
    meta: { title: 'Report' },
  },
  {
    //users
    path: '/users',
    name: 'users',
    component: Users,
    meta: { title: 'Users' },
  },
  {
    ///user/:id
    path: '/user/:id',
    name: 'user',
    component: UserView,
    meta: { title: 'User' },
  },
  {
    //vacations
    path: '/vacations',
    name: 'vacations',
    component: VacationsView,
    meta: { title: 'Vacations', titleAr: 'الاجازات', show: true },
  },
  {
    //preparation
    path: '/preparation',
    name: 'preparation',
    component: typePreparationView,
    meta: { title: 'Preparation', titleAr: 'التحضيرات', show: true },
  },
  {
    //auth
    path: '/auth',
    name: 'auth',
    component: AuthView,
    meta: { title: 'Auth' },
    children: [
      {
        path: 'register',
        name: 'register',
        component: Register,
        meta: { title: 'Register' },
      },
      {
        path: 'login',
        name: 'login',
        component: LoginView,
        meta: { title: 'Log In' },
      },
    ],
  },
  {
    path: '/main',
    name: 'main',
    component: () => import('../views/main/main.vue'),
    meta: { title: 'Main' },
  },
  {
    path: '/main/order',
    name: 'order',
    component:  () => import('../views/main/order.vue'),
    meta: { title: 'Order' },
  },
  {
    path: '/:path(.*)*',
    name: 'notfound',
    component: NotFound,
    meta: { title: 'Not Found' },
  },
];
const router = createRouter({
  history: createWebHistory(),
  routes,
});


router.beforeEach((to, from, next) => {
  document.title = to.meta.title || 'atsh';
  const token = localStorage.getItem('token');
  if (to.path.startsWith('/main')) {
    return next();
  } else {
    if (token) {
      if (to.name === 'login' || to.name === 'register' || to.name === 'auth') {
        next({ name: 'home' });
      } else {
        next();
      }
    } else {
      if (to.name !== 'auth' && to.name !== 'login' && to.name !== 'register') {
        next({ name: 'login' });
      } else {
        next();
      }
    }
  }
});

export default router;
